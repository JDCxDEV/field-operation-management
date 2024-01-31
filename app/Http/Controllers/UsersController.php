<?php

namespace App\Http\Controllers;

use App\Http\Requests\Users\UserRequest;
use App\Http\Requests\Users\UserResetPasswordRequest;
use App\Models\User;
use App\Models\UserInfo;
use App\Models\LoginSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use App\Helpers\GoogleStorageHelper;
use App\Helpers\TwilioHelper;

use Auth;
use Storage;
use Hash;
use DB;
use Carbon\Carbon;

class UsersController extends Controller
{

    public function __invoke(Request $request)
    {   
        $config = theme()->getOption('page');

        $pageTitle = "Manage User Accounts";
        $user = Auth::user();
        
        if($user->companyCoordinator()) {
            $pageTitle = $pageTitle. ' — ' .$user->info->company_name;
        }

        if($user->marketDirector()) {
            $pageTitle = $pageTitle. ' — ' .$user->info->market_name;
        }

        return view("pages.users.index", [
            "pageTitle" => $pageTitle
        ]); 
    }

    /**
     * Fetch the datalist of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function fetch(Request $request)
    {
        $users = new User();
        $currUser = Auth::user();

        if(!$request->noexemptions) {
            $users = $users->whereNotIn("role_type", User::EXEMPTIONS);
        } 
        
        // add active status filter
        if($request->status && $request->status != "all") {
            $query;
            switch($request->status) {
                case "active":
                    $query = ["status" => USER::USER_ACTIVE]; 
                    break;
                case "inactive":
                    $query = ["status" => USER::USER_INACTIVE];                    
                    break;
                case "blacklisted":
                    $query = ["blacklisted" => true];                    
                    break;
            }
            $users = $users->where($query);
        }

        if($request->market_directors_only && $request->market_directors_only === "true") {
            $users = $users->where(["role_type" => User::MARKET_DIRECTOR]);
        }

        if($request->nocompany == "true" && !$request->company) {
            $users = $users->whereHas("info", function($query) {
                $query->where("company_id", null);
            });
        }

        if($request->nocompany == "true" && $request->company) {
            $company = $request->company;
            $users = $users->whereHas("info", function($query) use ($company) {
                $query->where("company_id", $company)->orWhere("company_id", null);
            });
        }

        if($request->nocompany == "true" && $request->company) {
            $company = $request->company;
            $users = $users->whereHas("info", function($query) use ($company) {
                $query->where("company_id", $company)->orWhere("company_id", null);
            });
        }

        // add search keyword filter
        if($request->search && $request->search != null) {
            $users = $users->whereRaw("concat(first_name, ' ', last_name) like '%" . $request->search . "%' ");
        }

        if($request->nopaginate) {
            return $users->orderBy("first_name", "asc")->get();
        }

        // add company filter
        if($request->company && $request->company != "all") {
            $company = $request->company;

            $users = $users->whereHas("info", function($query) use ($company) {
                $query->where("company_id", $company);
            });
        }
        
        // add market filter
        if($request->market && $request->market != "all") {
            $market = $request->market;
            $users = $users->whereHas("info", function($query) use ($market) {
                $query->where("market_id", $market);
            });
        }

        if($request->status != 'blacklisted' && $request->status != 'all') {
            $query = ["blacklisted" => false];      
            $users->where($query);
        }

        if($request->user_type || $request->user_type == 0) {
            if($request->user_type != "all") {
                if($request->user_type == User::COMPANY_COORDINATOR || $request->user_type == User::MARKET_DIRECTOR) {
                    $users->whereIn("role_type", [$request->user_type, User::COMPANY_AND_MARKET_COORDINATOR]);
                } else {
                    $userType = $request->user_type == 0 ? User::AGENT: $request->user_type;                    
                    $users->where(["role_type" => $userType]);
                }
            }
        }

        if($currUser->role_type === User::POD_LEADER) {
            $ids = $currUser->pod_users->pluck("id")->toArray();
            $users = $users->whereIn("id", $ids);            
        }

        return $users->with('info')->orderBy("created_at", "desc")->paginate(10)
                ->through(function($user) {
                    $user->role_description = $user->renderRole()["description"];
                    $user->role_class = $user->renderRole()["class"];
                    return $user;
                });

    }

    public function getUser()
    {
        $user = Auth::user();
        $user->info = $user->info;

        return response()->json([
            "user" => $user
        ]);
    }
    
    public function podUsers($id)
    {
        $leader = User::find($id);
        $users = $leader->pod_users();

        $users = $users->orderBy("first_name", "asc")->get()->map(function($user) {
                $user->avatar_url = $user->info->renderAvatarUrl();
                return $user;
            });

        return response()->json([
            "status" => 200,
            "users" => $users
        ]);
    }

    public function appointPodUsers(Request $request, $id)
    {
        $leader = User::findOrFail($id);
        DB::beginTransaction();
            $leader->pod_users()->syncWithoutDetaching($request->userIds);
            User::whereIn("id", $request->userIds)->update(["pod_status" => true]);
        DB::commit();
        return response()->json([
            "status" => 200,
            "message" => "Users has been assign in pod"
        ]);
    }
    
    public function removePodUsers(Request $request, $id)
    {
        $leader = User::findOrFail($id);
        DB::beginTransaction();
            $leader->pod_users()->detach($request->userId);
            User::where(["id" => $request->userId])->update(["pod_status" => false]);
        DB::commit();
        return response()->json([
            "status" => 200,
            "message" => "Users has been remove from pod"
        ]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        $user = new User;
        
        DB::beginTransaction();
            // update user
            $userParams = $request->only(["first_name", "last_name", "email"]);
            $password = Str::random(8);
            $userParams['password'] = Hash::make($password);
            $user = $user->create($userParams);
            
            /**
             * Starts saving user info
             */

            // info params
            $infoParams = $request->only(["phone", "website", "company_id", "market_id"]);

            // include to save avatar
            if ($avatar = $this->upload()) {
                $infoParams['avatar'] = $avatar;
            }

            $info = new UserInfo();        
            foreach($infoParams as $key => $value) {
                $info->$key = $value;
            }

            // create user info
            $info->user()->associate($user)->save();

            if($request->notify == "email") {
                Password::sendResetLink($request->only('email'));
            } else {
                $this->sendResetSms($user);
            }
        DB::commit();

        return response()->json([
			"status" => 200,
            "message" => "User has been added.",
		]);
    }

    public function sendResetSms($user)
    {
        /**
         * Force insert in password reset
         */
        $token = Str::random(60);
        DB::table('password_resets')->insert([
            'email' => $user->email,
            'token' => Hash::make($token),
            'created_at' => now()
        ]);

        /**
         * Create Twilio message
         */
        $url = url(route('password.reset', [
            'token' => $token,
            'email' => $user->email,
        ], false));
        $expiration = config('auth.passwords.'.config('auth.defaults.passwords').'.expire');
        $message = "Hello {$user->name}, 
        this is FieldOps. You are receiving this sms because we received a password reset request for your account.
        This password reset link will expire in {$expiration} minutes. {$url}
        ";
        
        /**
         * Send sms using Twilio
         */
        $twilio = new TwilioHelper;
        $recipient = $user->info->phone;
        $twilio->sendMessage($recipient, $message);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $config = theme()->getOption('page');

        return User::find($id);
    }

     /**
     * Find the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\Response
     */
    public function find($id)
    {
        $user = User::with("info")->find($id);
        $user->role_description = $user->renderRole()["description"];
        $user->role_class = $user->renderRole()["class"];        
        return response()->json([
            "status" => 200,
            "user" => $user
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $config = theme()->getOption('page', 'edit');

        return User::find($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(UserRequest $request, $id)
    {
        $user = User::findOrfail($id);

        // update user
        $user->update($request->only(["first_name", "last_name"]));
        
        $infoParams = $request->except(["first_name", "last_name", "email", "avatar"]);

        $gcs = new GoogleStorageHelper;

        // include to save avatar
        if ($avatar = $this->upload()) {
            $gcs->deleteFile($user->avatar);       
            $infoParams['avatar'] = $avatar;
        }

        // update user info
        $user->info->update($infoParams);

        return response()->json([
			"status" => 200,
            "message" => "User has been updated.",
		]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


    /**
     * Set the status to false of the specified resource from storage
     * @param int $id
     */
    public function deactivate($id) 
    {
		$user = User::where(["id" => $id])->first();
        activity()->disableLogging();
        $user->update(["status" => User::USER_INACTIVE]);
        activity()->enableLogging();
        activity()
            ->causedBy(Auth::user())
            ->performedOn($user)
            ->event('deactivated')
            ->log($user->name. ' has been deactivated');
		return response()->json([
			"status" => 200,
		]);
    }

    /**
     * Batch update the status to false of the specified resource from storage
     * 
     * @param  \Illuminate\Http\Request  $request
     */
    public function batchDeactivate(Request $request)
    {
        User::whereIn("id", $request->ids)->update(["status" => User::USER_INACTIVE]);
        return response()->json([
            "status" => 200,
        ]);   
    }

	/**
     * Set the status to true of the specified resource from storage
     * 
     * @param int $id
     */
    public function activate($id) 
    {
		$user = User::where(["id" => $id])->first();
        activity()->disableLogging();
        $user->update(["status" => User::USER_ACTIVE]);
        activity()->enableLogging();
        activity()
            ->causedBy(Auth::user())
            ->performedOn($user)
            ->event('activated')
            ->log($user->name. ' has been activated'); 
		return response()->json([
			"status" => 200,
		]);
    }

    /**
     * Batch update the status to true of the specified resource from storage
     * 
     * @param  \Illuminate\Http\Request  $request
     */
    public function batchActivate(Request $request)
    {
        User::whereIn("id", $request->ids)->update(["status" => User::USER_ACTIVE]);
        return response()->json([
            "status" => 200,
        ]);   
    }

    /**
     * Set the blacklisted to true of the specified resource from storage
     * @param int $id
     */
    public function block($id) 
    {
		$user = User::where(["id" => $id])->first();
        activity()->disableLogging();     
        $user->update(["blacklisted" => 1]);
        activity()->enableLogging();
        activity()
            ->causedBy(Auth::user())
            ->performedOn($user)
            ->event('blacklisted')
            ->log($user->name. ' has been blacklisted');
        return response()->json([
			"status" => 200,
		]);
    }

	/**
     * Set the blacklisted to false of the specified resource from storage
     * @param int $id
     */
    public function unblock($id) 
    {
		User::where(["id" => $id])->update(["blacklisted" => 0]);
		return response()->json([
			"status" => 200,
		]);
    }

    /**
     * Resetting user password
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     */
    public function resetPassword(UserResetPasswordRequest $request, $id) 
    {
        $user = User::findOrFail($id);
        $user->update(['password' => Hash::make($request->password)]);

        return response()->json([
            "status" => 200,
            "message" => "User password has been reset"
        ]);
    }

    /**
     * Update user theme
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     */
    public function updateTheme(Request $request, $id) 
    {
        activity()->disableLogging();
        $user = User::findOrFail($id);
        $user->update(['theme_mode' => $request->mode]);
        activity()->enableLogging();
        
        return response()->json([
            "status" => 200,
            "message" => "Theme has been update to ". $request->mode
        ]);
    }

    /**
     * Returns list of user login sessions
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     */
    public function loginSessions(Request $request, $id = null)
    {   
        $userId = $id ? $id : auth()->user->id;
        
        $sessions = LoginSession::where(["user_id" => $id]);
        
        // add search keyword filter
        if($request->search && $request->search != null) {
            $sessions = $sessions->whereRaw("concat(location, ' ', device) like '%" . $request->search . "%' ");
        }

        if($request->time && $request->time != null) {
            $start = "";
            $end = "";
            
            if($request->time == "today") {
                $start = Carbon::now()->startOfDay();
                $end = Carbon::now()->endOfDay();
            } else if ($request->time == "this-week") {
                $start = Carbon::now()->startOfWeek();
                $end = Carbon::now()->endOfWeek();
            } else if ($request->time == "this-month") {
                $start = Carbon::now()->startOfMonth();
                $end = Carbon::now()->endOfMonth();
            }

            if($start && $end) {
                $sessions = $sessions->whereBetween("created_at", [$start, $end]);
            }
        }

        $sessions = $sessions->latest();

        if(!$request->nopaginate) {
            $sessions = $sessions->paginate(10)
                ->through(function($session) {
                    $session->time_ago = $session->created_at->diffForHumans();
                    return $session;
                });
        } else {
            $sessions = $sessions->get()
                ->map(function($session) {
                    $session->time_ago = $session->created_at->diffForHumans();
                    $session->time = $session->created_at->format("H:i");
                    return $session;
                });
        }

        return $sessions;
    }

    /**
     * Function for upload avatar image
     *
     * @param  string  $folder
     * @param  string  $key
     * @param  string  $validation
     *
     * @return false|string|null
     */
    public function upload($folder = 'images', $key = 'avatar', $validation = 'image|mimes:jpeg,png,jpg,gif,svg|max:2048|sometimes')
    {
        request()->validate([$key => $validation]);

        $file = null;
        if (request()->hasFile($key)) {
            $gcs = new GoogleStorageHelper;
            $file = $gcs->storeFile(request()->file($key), $folder);
        }

        return $file;
    }    
}

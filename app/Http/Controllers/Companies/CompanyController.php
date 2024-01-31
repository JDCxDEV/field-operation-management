<?php

namespace App\Http\Controllers\Companies;

use App\Http\Requests\Companies\CompanyRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Market;
use App\Models\User;
use App\Models\UserInfo;
use DB;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {   
        return view("pages.companies.index");        
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function fetch(Request $request, $id = null)
    {
        if($request->paginate && $request->paginate === "true") {
            $companies = new Company;
            if($request->status) {
                if($request->status == "archived") {
                    $companies = $companies->onlyTrashed();
                } 

                if($request->status == "all") {
                    $companies = $companies->withTrashed();
                }
            }
            
            if($request->search && $request->search != null) {
                $companies = $companies->where('company','like', '%' .$request->search . '%');
            }

            if($request->state && $request->state != "all") {
                $companies = $companies->where(["state" => $request->state]);
            }

            if($id) {
                $companies =  $companies->withTrashed()->where("id", $id);
            }

            return $companies->orderBy("created_at", "desc")->paginate(10);
        }

        $companies = Company::orderBy("company", "asc")->get();

        return response()->json([
            "status" => 200,
            "companies" => $companies
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("pages.companies.create");
    }

    /**
     * Show the form for appointing a resource as executives.
     *
     * @return \Illuminate\Http\Response
     */
    public function appoint()
    {
        return view("pages.companies.appoint");
    }

    /**
     * Fetch coordinators and users of company
     * 
     * @param int $id
     * 
     * @return \Illuminate\Http\Response
     */
    public function coordinatorsAndUsers($id)
    {
        $company = Company::withTrashed()->find($id);
        $coordinators = $company->coordinators()->where(["status" => true, "blacklisted" => false])
                        ->orderBy("first_name", "asc")
                        ->get()
                        ->map(function($user) {
                            $user->avatar_url = $user->info->renderAvatarUrl();
                            $user->date_added = $user->pivot->created_at->format('j M Y, g:i A');                            
                            return $user;
                        });
        
        return response()->json([
            "status" => 200,
            "coordinators" => $coordinators,
        ]);
    }

    /**
     * Fetch common users of company
     * 
     * @param int $id
     * 
     * @return \Illuminate\Http\Response
     */
    public function commonUsers(Request $request, $id)
    {
        $company = Company::withTrashed()->find($id);
        $coordinators = $company->coordinators;
        
        $exceptionIds = $company->coordinators->pluck("id")->toArray();
        $userIds = $company->users()->whereNotIn("user_id", $exceptionIds)->pluck("user_id")->toArray();
        $users = User::whereIn("id", $userIds);

        if($request->search && $request->search != null) {
            $users = $users->whereRaw("concat(first_name, ' ', last_name) like '%" . $request->search . "%' ");            
        }

        $users = $users->orderBy("first_name", "asc")->paginate(20)
            ->through(function($user) {
                $user->avatar_url = $user->info->renderAvatarUrl();
                return $user;
            });

        return response()->json([
            "status" => 200,
            "users" => $users
        ]);
    }

    /**
     * Appoint users as coordinators
     * 
     * @param object $request
     * @param int $id
     * 
     * @return \Illuminate\Http\Response
     */
    public function appointUsers(Request $request, $id)
    {
        $company = Company::withTrashed()->findOrFail($id);
        /** Set role type to company coordinator */
        DB::beginTransaction();
            User::setAsCoordinator($request->userIds, User::COMPANY_COORDINATOR);
            $company->coordinators()->syncWithoutDetaching($request->userIds);
        DB::commit();
        return response()->json([
            "status" => 200,
            "message" => "Users has been appointed"
        ]);
    }

    /**
     * Remove users from coordinators
     * 
     * @param object $request
     * @param int $id
     * 
     * @return \Illuminate\Http\Response
     */
    public function coordinatorsRemove(Request $request, $id) 
    {
        $company = Company::withTrashed()->findOrFail($id);
        DB::beginTransaction();
            $company->coordinators()->detach($request->userId);
            /** Set role to agent */
            User::removeFromCoordinator($request->userId, "company");
        DB::commit();
        return response()->json([
            "status" => 200,
            "message" => "Users has been removed from coordinators"
        ]);
    }

    /**
     * Get company markets
     * 
     * @param object $request
     * @param int $id
     * 
     * @return \Illuminate\Http\Response
     */
    public function markets(Request $request, $id) 
    {
        if($request->paginate && $request->paginate === "true") {
            $markets = new Market;

            if($id) {
                $markets = $markets->where("company_id", $id);
            }

            if($request->status) {
                if($request->status == "archived") {
                    $markets = $markets->onlyTrashed();
                } 

                if($request->status == "all") {
                    $markets = $markets->withTrashed();
                }
            }
            
            if($request->search && $request->search != null) {
                $markets = $markets->where('market_name','like', '%' .$request->search . '%');
            }

            return $markets->orderBy("created_at", "desc")->paginate(6);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CompanyRequest $request)
    {
        DB::beginTransaction();
            $company = Company::create($request->only(app(Company::class)->getFillable()));

            if($request->coordinators && $request->coordinators != null) {
                $users = explode(",", $request->coordinators);
                $company->coordinators()->sync($users);
                /** Set role type to company coordinator */
                User::setAsCoordinator($users, User::COMPANY_COORDINATOR);      
                UserInfo::whereIn("user_id", $users)->update(["company_id" => $company->id]);
            }
        DB::commit();

        return response()->json([
            "status" => 200,
            "company" => $company
        ]);
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
        $company = Company::with("coordinators")->withTrashed()->find($id);
        $company->coordinator_latest_login = $company->latestLogin();
        $company->market_directors_count = $company->countMarketDirectors();
        return response()->json([
            "status" => 200,
            "company" => $company
        ]);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CompanyRequest $request, $id)
    {
        $company = Company::withTrashed()->findOrFail($id);
        DB::beginTransaction();
            if($company) {
                $company->update($request->only($company->getFillable()));
            }

            if($request->coordinators && $request->coordinators != "") {
                $users = explode(",", $request->coordinators);
                $company->coordinators()->sync($users);
                /** Set role type to company coordinator */
                User::setAsCoordinator($users, User::COMPANY_COORDINATOR);
                UserInfo::whereIn("user_id", $users)->update(["company_id" => $company->id]);
            } else {
                $company->coordinators()->sync([]);
            }
        DB::commit();

        return response()->json([
            "status" => 200,
            "company" => $company
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $company = Company::findOrFail($id);
        if($company) {
            $company->delete();
        }

        return response()->json([
            "status" => 200,
            "message" => "Company has been archived"
        ]);

    }

    /**
     * Restore the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        $company = Company::onlyTrashed()->findOrFail($id);
        if($company) {
            $company->restore();
        }

        return response()->json([
            "status" => 200,
            "message" => "Company has been restored"
        ]);
    }

    /**
     * Display overview of the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function overview($id)
    {
        $company = Company::withTrashed()->findOrFail($id);
        
        if(!$company) {
            return redirect()->back();
        }
        
        return view("pages.companies.overview", [
            "company" => $company
        ]);
    }
}

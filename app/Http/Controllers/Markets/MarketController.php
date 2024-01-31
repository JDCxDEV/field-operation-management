<?php

namespace App\Http\Controllers\Markets;

use App\Http\Requests\Markets\MarketRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Market;
use App\Models\User;
use Auth;
use DB;

class MarketController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $pageTitle = "Manage Markets";
        $user = Auth::user();
        
        if($user->companyCoordinator()) {
            $pageTitle = $pageTitle. ' — ' .$user->info->company_name;
        }

        return view("pages.markets.index", [
            'pageTitle' => $pageTitle
        ]);        
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function fetch(Request $request, $id = null)
    {
        $currUser = Auth::user();
        if($request->paginate && $request->paginate === "true") {
            $markets = new Market;

            if($request->status) {
                if($request->status == "archived") {
                    $markets = $markets->onlyTrashed();
                } 

                if($request->status == "all") {
                    $markets = $markets->withTrashed();
                }
            }

            if($request->state && $request->state != "all") {
                $markets = $markets->where(["state" => $request->state]);
            }

            if($request->company && $request->company != "all") {
                $company = $request->company;
    
                $markets = $markets->where("company_id", $company);
            }
            
            if($request->search && $request->search != null) {
                $markets = $markets->where('market_name','like', '%' .$request->search . '%');
            }

            if($id) {
                $markets =  $markets->withTrashed()->where("id", $id);
            } else {
                /** Filter company via coordinator company_id */
                if($currUser->companyCoordinator()) {
                    $company = $currUser->info->company_id;
                    $markets = $markets->where("company_id", $company);
                }                
            }
            

            return $markets->orderBy("created_at", "desc")->paginate(10);
        }
        
        $markets = Market::orderBy("market_name", "asc");

        if($request->company_id) {
            $markets = $markets->where("company_id", $request->company_id);
        }

        $markets = $markets->get();

        return response()->json([
            "status" => 200,
            "markets" => $markets
        ]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("pages.markets.create");
    }
    
    /**
     * Show the form for appointing a resource as directors.
     *
     * @return \Illuminate\Http\Response
     */
    public function appoint()
    {
        return view("pages.markets.appoint");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MarketRequest $request)
    {
        $market = Market::create($request->only(app(Market::class)->getFillable()));
        return response()->json([
            "status" => 200,
            "market" => $market
        ]);
    }

    public function overview($id)
    {
        $market = Market::withTrashed()->find($id);
        return view("pages.markets.overview", [
            "market" => $market
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
        $market = Market::withTrashed()->find($id);
        $market->director_latest_login = $market->latestLogin();
        return response()->json([
            "status" => 200,
            "market" => $market
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(MarketRequest $request, $id)
    {
        $market = Market::withTrashed()->findOrFail($id);
        if($market) {
            $market->update($request->only($market->getFillable()));
        }
        return response()->json([
            "status" => 200,
            "market" => $market
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
        $market = Market::findOrFail($id);
        if($market) {
            $market->delete();
        }

        return response()->json([
            "status" => 200,
            "message" => "Market has been archived"
        ]);

    }

    /**
     * Batch remove the specified resources from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function batchArchive(Request $request)
    {
        $markets = Market::whereIn("id", $request->ids)->delete();
        return response()->json([
            "status" => 200,
            "message" => "Markets has been archived"
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
        $market = Market::onlyTrashed()->findOrFail($id);
        if($market) {
            $market->restore();
        }

        return response()->json([
            "status" => 200,
            "message" => "Market has been restored"
        ]);
    }

    /**
     * Batch restore the specified resources from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function batchRestore(Request $request)
    {
        $markets = Market::onlyTrashed()->whereIn("id", $request->ids)->restore();
        return response()->json([
            "status" => 200,
            "message" => "Markets has been restore"
        ]);
    }

    /**
     * Fetch coordinators and users of market
     * 
     * @param int $id
     * 
     * @return \Illuminate\Http\Response
     */
    public function directorsAndUsers($id)
    {
        $market = Market::find($id);
        $directors = $market->directors()->where(["status" => true, "blacklisted" => false])->get()
            ->map(function($user) {
                $user->avatar_url = $user->info->renderAvatarUrl();
                $user->date_added = $user->pivot->created_at->format('j M Y, g:i A');
                return $user;
            });
        
        return response()->json([
            "status" => 200,
            "directors" => $directors,
        ]);
    }

    /**
     * Fetch common users of market
     * 
     * @param int $id
     * 
     * @return \Illuminate\Http\Response
     */
    public function commonUsers(Request $request, $id)
    {
        $market = Market::find($id);
        $directors = $market->directors;
        $podLeaders = $market->pod_leaders;        
        
        $exceptionIds = array_merge($directors->pluck("id")->toArray(), $podLeaders->pluck("id")->toArray());
        $userIds = $market->users()->whereNotIn("user_id", $exceptionIds)->pluck("user_id")->toArray();
        $users = User::whereIn("id", $userIds)->where(["status" => User::USER_ACTIVE, "blacklisted" => false]);

        if($request->agent_only && $request->agent_only === "true") {
            $users = $users->where(["role_type" => USER::AGENT, "pod_status" => false]);
        } 

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
     * Fetch common users of market
     * 
     * @param int $id
     * 
     * @return \Illuminate\Http\Response
     */
    public function marketPodLeaders(Request $request, $id)
    {
        $market = Market::find($id);
        $users = $market->pod_leaders();

        $users = $users->orderBy("first_name", "asc")->get()->map(function($user) {
                $user->avatar_url = $user->info->renderAvatarUrl();
                $user->pod_users_count = $user->pod_users->count();
                return $user;
            });

        return response()->json([
            "status" => 200,
            "pod_leaders" => $users
        ]);
    }

    /**
     * Appoint users as directors
     * 
     * @param object $request
     * @param int $id
     * 
     * @return \Illuminate\Http\Response
     */
    public function appointUsers(Request $request, $id)
    {
        $market = Market::findOrFail($id);
        DB::beginTransaction();
            $market->directors()->syncWithoutDetaching($request->userIds);
            /** Set role type to market directors */
            User::setAsCoordinator($request->userIds, User::MARKET_DIRECTOR);
        DB::commit();
        return response()->json([
            "status" => 200,
            "message" => "Users has been appointed"
        ]);
    }

    /**
     * Appoint users as pod leaders
     * 
     * @param object $request
     * @param int $id
     * 
     * @return \Illuminate\Http\Response
     */
    public function appointUsersAsPodLeaders(Request $request, $id)
    {
        $market = Market::findOrFail($id);
        DB::beginTransaction();
            $market->pod_leaders()->syncWithoutDetaching($request->userIds);
            /** Set role type to market pod leaders */
            User::setAsPodLeader($request->userIds);
        DB::commit();
        return response()->json([
            "status" => 200,
            "message" => "Users has been appointed as pod leaders"
        ]);
    }

    /**
     * Remove users from pod leaders
     * 
     * @param object $request
     * @param int $id
     * 
     * @return \Illuminate\Http\Response
     */
    public function podLeadersRemove(Request $request, $id) 
    {
        $market = Market::findOrFail($id);
        DB::beginTransaction();        
            $market->pod_leaders()->detach($request->userId);

            $user = User::find($request->userId);
            $podUserIds = $user->pod_users()->pluck("id")->toArray();
            $user->pod_users()->detach();
            
            User::whereIn("id", $podUserIds)->update(["pod_status" => false]);
            $user->update(["role_type" => User::AGENT]);
        DB::commit();
        return response()->json([
            "status" => 200,
            "message" => "Users has been removed from pod leaders"
        ]);
    }

    /**
     * Remove users from director
     * 
     * @param object $request
     * @param int $id
     * 
     * @return \Illuminate\Http\Response
     */
    public function directorsRemove(Request $request, $id) 
    {
        $market = Market::findOrFail($id);
        DB::beginTransaction();        
            $market->directors()->detach($request->userId);
            /** Set role to agent */
            User::removeFromCoordinator($request->userId, "market");
        DB::commit();
        return response()->json([
            "status" => 200,
            "message" => "Users has been removed from directors"
        ]);
    }

    /**
     * Link markets
     * 
     * @param object $request
     * @param int $id
     * 
     * @return \Illuminate\Http\Response
     */
    public function linkCompany(Request $request)
    {
        $marketIds = explode(",", $request->markets);
        $markets = Market::whereIn("id", $marketIds)->update(["company_id" => $request->company_id]);
        
        return response()->json([
            "status" => 200,
            "message" => "Company linked"
        ]);
    }

    public function unlinkCompany($id) 
    {
        $market = Market::findOrFail($id);
        $market->update(["company_id" => null]);
        return response()->json([
            "status" => 200,
            "message" => "Company unlinked"
        ]);
    }

}

<?php

namespace App\Http\Controllers\Recruits;

use App\Models\Recruit;
use App\Http\Requests\Recruits\RecruitRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RecruitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return view("pages.recruits.index");        
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function fetch(Request $request)
    {   
        $recruits = new Recruit;
        if($request->status) {
            if($request->status == "archived") {
                $recruits = $recruits->onlyTrashed();
            } 

            if($request->status == "all") {
                $recruits = $recruits->withTrashed();
            }
        }
        
        if($request->search && $request->search != null) {
            $recruits = $recruits->whereRaw("name like '%" . $request->search . "%' ");
        }

        return $recruits->orderBy("created_at", "desc")->paginate(6);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("pages.recruits.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RecruitRequest $request)
    {
        $recruit = Recruit::create($request->only(["name", "phone", "email", "company_id", "market_id"]));
        return response()->json([
            "status" => 200,
            "recruit " => $recruit 
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
        $recruit = Recruit::withTrashed()->find($id);
        return response()->json([
            "status" => 200,
            "recruit" => $recruit
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(RecruitRequest $request, $id)
    {
        $recruit = Recruit::withTrashed()->findOrFail($id);
        if($recruit) {
            $recruit->update($request->only(["name", "phone", "email", "company_id", "market_id"]));
        }
        return response()->json([
            "status" => 200,
            "recruit" => $recruit
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
        $recruit = Recruit::findOrFail($id);
        if($recruit) {
            $recruit->delete();
        }

        return response()->json([
            "status" => 200,
            "message" => "Recruit has been archived"
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
        $recruit = Recruit::onlyTrashed()->findOrFail($id);
        if($recruit) {
            $recruit->restore();
        }

        return response()->json([
            "status" => 200,
            "message" => "Recruit has been restored"
        ]);
    }
}

<?php

namespace App\Http\Controllers\Statuses;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Statuses\StatusRequest;
use App\Models\Status;

class StatusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function fetch(Request $request)
    {   
        $statuses = new Status;
        if($request->status) {
            if($request->status == "archived") {
                $statuses = $statuses->onlyTrashed();
            } 

            if($request->status == "all") {
                $statuses = $statuses->withTrashed();
            }
        }
        
        if($request->search && $request->search != null) {
            $statuses = $statuses->whereRaw("status like '%" . $request->search . "%' ");
        }

        $statuses = $statuses->orderBy("created_at", "desc");
        
        if($request->nopaginate) {
            return $statuses->get();
        }

        return $statuses->paginate(10);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StatusRequest $request)
    {
        $statusDetails = Status::create($request->all());
        return response()->json([
            "status" => 200,
            "status_details" => $statusDetails 
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
        $statusDetails = Status::withTrashed()->find($id);
        return response()->json([
            "status" => 200,
            "status_details" => $statusDetails
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StatusRequest $request, $id)
    {
        $statusDetails = Status::withTrashed()->findOrFail($id);
        if($statusDetails) {
            $statusDetails->update($request->all());
        }
        return response()->json([
            "status" => 200,
            "status_details" => $statusDetails
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
        $statusDetails = Status::findOrFail($id);
        if($statusDetails) {
            $statusDetails->delete();
        }

        return response()->json([
            "status" => 200,
            "message" => "Status has been archived"
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
        $statusDetails = Status::onlyTrashed()->findOrFail($id);
        if($statusDetails) {
            $statusDetails->restore();
        }

        return response()->json([
            "status" => 200,
            "message" => "Status has been restored"
        ]);
    }

    /**
     * Force delete the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function forceDelete($id)
    {
        $statusDetails = Status::withTrashed()->findOrFail($id);
        if($statusDetails) {
            $statusDetails->forceDelete();
        }

        return response()->json([
            "status" => 200,
            "message" => "Status has been deleted"
        ]);

    }    
}

<?php

namespace App\Http\Controllers\BlacklistedAddresses;

use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use App\Http\Requests\BlacklistedAddresses\BlacklistedAddressRequest;
use App\Models\BlacklistedAddress;
use App\Exports\BlacklistedAddressExport;

class BlacklistedAddressController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("pages.blacklisted-addresses.index");
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function fetch(Request $request)
    {   
        $addresses = new BlacklistedAddress;
        if($request->status) {
            if($request->status == "archived") {
                $addresses = $addresses->onlyTrashed();
            } 

            if($request->status == "all") {
                $addresses = $addresses->withTrashed();
            }
        }
        
        if($request->search && $request->search != null) {
            $addresses = $addresses->whereRaw("address_1 like '%" . $request->search . "%' ");
        }

        return $addresses->orderBy("created_at", "desc")->paginate(10);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BlacklistedAddressRequest $request)
    {
        $address = BlacklistedAddress::create($request->all());
        return response()->json([
            "status" => 200,
            "address" => $address 
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
        $address = BlacklistedAddress::withTrashed()->find($id);
        return response()->json([
            "status" => 200,
            "address" => $address
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(BlacklistedAddressRequest $request, $id)
    {
        $address = BlacklistedAddress::withTrashed()->findOrFail($id);
        if($address) {
            $address->update($request->all());
        }
        return response()->json([
            "status" => 200,
            "address" => $address
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
        $address = BlacklistedAddress::findOrFail($id);
        if($address) {
            $address->delete();
        }

        return response()->json([
            "status" => 200,
            "message" => "Blacklisted Address has been archived"
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
        $address = BlacklistedAddress::onlyTrashed()->findOrFail($id);
        if($address) {
            $address->restore();
        }

        return response()->json([
            "status" => 200,
            "message" => "Blacklisted Address has been restored"
        ]);
    }

    public function export() 
    {
        return Excel::download(new BlacklistedAddressExport, "blacklisted-addresses.xlsx");
    }
    
}

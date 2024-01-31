<?php

namespace App\Models;

use App\Traits\ActivityLogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlacklistedAddress extends Model
{
    use HasFactory;
    use SoftDeletes;
    use ActivityLogTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'address_1',
        'address_2',
        'city',
        'state',
        'zip'
    ];

    /**
     * The attributes that will be on the default result.
     *
     * @var array
     */
    protected $appends = [
        'create_at_readable',
        'find_url',
        'update_url',
        'delete_url',
        'restore_url',
    ];

    /**
     * Get readable created_at
     *
     * @return string
     */
    public function getCreateAtReadableAttribute()
    {
        return $this->created_at->format('j M Y, g:i A') ;
    }
    
    /**
     * Get find url of specified resource
     * 
     * @return string
     */
    public function getFindUrlAttribute() 
    {
        return route("blacklisted-addresses.find", $this->id);
    }

    /**
     * Get update url of specified resource
     * 
     * @return string
     */
    public function getUpdateUrlAttribute() 
    {
        return route("blacklisted-addresses.update", $this->id);
    }

    /**
     * Get delete url of specified resource
     * 
     * @return string
     */
    public function getDeleteUrlAttribute() 
    {
        if(!$this->deleted_at) {
            return route("blacklisted-addresses.delete", $this->id);
        }
        return "";
    }

    /**
     * Get restore url of specified resource
     * 
     * @return string
     */
    public function getRestoreUrlAttribute() 
    {
        if($this->deleted_at) {
            return route("blacklisted-addresses.restore", $this->id);
        }
        return "";
    }

    public static function fixAddress() 
    {
        $addresses = BlacklistedAddress::get();
        foreach($addresses as $address) {
            $completeAddress = $address->address_1 . ' ' . $address->city . ' ' . $address->state . ' ' . $address->zip;
            $response = \GoogleMaps::load('geocoding')->setParam (['address' => $completeAddress])->get();
            $result = json_decode($response, true);
            if($result && count($result["results"])) {
                $address_components = $result["results"][0]["address_components"];
                $newAddress = "";
                if($address_components && count($address_components)) {
                    foreach($address_components as $comp) {
                        if(in_array("street_number", $comp["types"])) {
                            $newAddress = $comp["long_name"];
                        }

                        if(in_array("route", $comp["types"])) {
                            $newAddress = $newAddress ? $newAddress . ' '. $comp["long_name"] : $comp["long_name"];
                        }
                    }
                }
                $address->update(["address_1" => $newAddress]);

            } else {
                \Log::info($address->address_1);
            }
        }
    }
}

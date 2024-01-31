<?php

namespace App\Models;

use App\Traits\ActivityLogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Recruit extends Model
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
        'name',
        'phone',
        'email',
        'company_id',
        'market_id'
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
        'company_name',
        'market_name',
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
        return route("recruits.find", $this->id);
    }

    /**
     * Get update url of specified resource
     * 
     * @return string
     */
    public function getUpdateUrlAttribute() 
    {
        return route("recruits.update", $this->id);
    }

    /**
     * Get delete url of specified resource
     * 
     * @return string
     */
    public function getDeleteUrlAttribute() 
    {
        if(!$this->deleted_at) {
            return route("recruits.delete", $this->id);
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
            return route("recruits.restore", $this->id);
        }
        return "";
    }

    /**
     * User info relation to user model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function getCompanyNameAttribute()
    {
        return $this->company->company;
    }


    /**
     * User info relation to user model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function getMarketNameAttribute()
    {
        return $this->market->market_name;
    }

    /**
     * UserInfo relation to company model
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class)->withTrashed();
    }

    /**
     * UserInfo relation to company model
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function market()
    {
        return $this->belongsTo(Market::class)->withTrashed();
    }
}

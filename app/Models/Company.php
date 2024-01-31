<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\CoordinatorTrait;
use App\Traits\ActivityLogTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory;
    use SoftDeletes;
    use CoordinatorTrait;
    use ActivityLogTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'company',
        'phone',
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
        'users_count',
        'coordinators_count',
        'markets_count',
        'coordinators_and_users_url',
        'custom_id'
    ];

    /**
     * Get users count
     *
     * @return string
     */
    public function getUsersCountAttribute()
    {
        return $this->users()->whereHas("user", function($user) {
            $user->where(["status" => true, "blacklisted" => false]);
        })->count();
    }

    /**
     * Get coordinators count
     *
     * @return string
     */
    public function getCoordinatorsCountAttribute()
    {
        return $this->coordinators()->where(["status" => true, "blacklisted" => false])->count();
    }

    /**
     * Get markets count
     *
     * @return string
     */
    public function getMarketsCountAttribute()
    {
        return $this->markets()->count();
    }

    /**
     * Get readable created_at
     *
     * @return string
     */
    public function getCreateAtReadableAttribute()
    {
        return $this->created_at->format('j M Y, g:i A');
    }

    /**
     * Get find url of specified resource
     * 
     * @return string
     */
    public function getFindUrlAttribute() 
    {
        return route("companies.find", $this->id);
    }

    /**
     * Get update url of specified resource
     * 
     * @return string
     */
    public function getUpdateUrlAttribute() 
    {
        return route("companies.update", $this->id);
    }

    /**
     * Get delete url of specified resource
     * 
     * @return string
     */
    public function getDeleteUrlAttribute() 
    {
        if(!$this->deleted_at) {
            return route("companies.delete", $this->id);
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
            return route("companies.restore", $this->id);
        }
        return "";
    }
    
    /**
     * Get coordinators and users url of specified resource
     * 
     * @return string
     */
    public function getCoordinatorsAndUsersUrlAttribute() 
    {
        return route("companies.coordinators-and-users", $this->id);
    }

    public function getCustomIdAttribute()
    {
        $id = $this->id;
        $idLength = strlen($id);
        $pattern = "0000";
        $patternLength = strlen($pattern);

        if($patternLength >= $idLength) {
            $pattern = substr($pattern, $patternLength - ($patternLength - $idLength));
        } else {
            $pattern = "";
        }

        $id = $pattern . $id;
        return "CMY-{$id}";
    }

    /**
     * Get market directors count
     *
     * @return string
     */
    public function countMarketDirectors()
    {
        return $this->markets->map(function($market) { return $market->directors()->where(["status" => true, "blacklisted" => false])->count(); })->sum();
    }
    
    /**
     * Company relation to user model
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function users()
    {
        return $this->hasMany(UserInfo::class);
    }
    
    /**
     * Company relation to market model
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function markets()
    {
        return $this->hasMany(Market::class);
    }

    /**
     * Pivotal table for company coordinators
     * 
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function coordinators() 
    {
        return $this->belongsToMany(User::class, 'company_coordinators', 'company_id', 'user_id')->withTimestamps();
    }
}

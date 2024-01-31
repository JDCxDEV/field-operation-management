<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\CoordinatorTrait;
use App\Traits\ActivityLogTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Market extends Model
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
        'company_id',
        'market_name',
        'email',
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
        'company_name',
        'create_at_readable',
        'find_url',
        'update_url',
        'delete_url',
        'restore_url',
        'users_count',
        'directors_count',
        'pod_leaders_count',        
        'unlink_company_url',
        'directors_and_users_url',
        'pod_leaders_url',
        'custom_id'
    ];    

    /**
     * Get company name
     *
     * @return string
     */
    public function getCompanyNameAttribute()
    {
        if($this->company) {
            return $this->company->company;
        }
        return "";
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
        return route("markets.find", $this->id);
    }

    /**
     * Get update url of specified resource
     * 
     * @return string
     */
    public function getUpdateUrlAttribute() 
    {
        return route("markets.update", $this->id);
    }

    /**
     * Get delete url of specified resource
     * 
     * @return string
     */
    public function getDeleteUrlAttribute() 
    {
        if(!$this->deleted_at) {
            return route("markets.delete", $this->id);
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
            return route("markets.restore", $this->id);
        }
        return "";
    }

    /**
     * Get a fullname combination of first_name and last_name
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
     * Get directors count
     *
     * @return string
     */
    public function getDirectorsCountAttribute()
    {
        return $this->directors()->where(["status" => true, "blacklisted" => false])->count();
    }

    /**
     * Get pod leaders count
     *
     * @return string
     */
    public function getPodLeadersCountAttribute()
    {
        return $this->pod_leaders()->where(["status" => true, "blacklisted" => false])->count();
    }

    /**
     * Get unlink company url
     * 
     * @return string
     */
    public function getUnlinkCompanyUrlAttribute() 
    {
        return route("markets.unlink-company", $this->id);
    }

    /**
     * Get directors and users url of specified resource
     * 
     * @return string
     */
    public function getDirectorsAndUsersUrlAttribute() 
    {
        return route("markets.directors-and-users", $this->id);
    }

    /**
     * Get pod leaders url of specified resource
     * 
     * @return string
     */
    public function getPodLeadersUrlAttribute() 
    {
        return route("markets.pod-leaders", $this->id);
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
        return "MKT-{$id}";
    }

    /**
     * Market relation to company model
     * 
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function company()
    {
        return $this->belongsTo(Company::class)->withTrashed();
    }

    /**
     * Market relation to user model
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function users()
    {
        return $this->hasMany(UserInfo::class);
    }

    /**
     * Pivotal table for market directors
     * 
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function directors() 
    {
        return $this->belongsToMany(User::class, 'market_directors', 'market_id', 'user_id')->withTimestamps();
    }

    /**
     * Pivotal table for market pod leaders
     * 
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function pod_leaders() 
    {
        return $this->belongsToMany(User::class, 'pod_leaders', 'market_id', 'user_id')->withTimestamps();
    }
}

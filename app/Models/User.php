<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use App\Traits\RoleTrait;
use App\Traits\ActivityLogTrait;
use Password;

use Stevebauman\Location\Facades\Location;
use Jenssegers\Agent\Facades\Agent;
use App\Models\LoginSession;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;
    use HasRoles;
    use RoleTrait;
    use SoftDeletes;
    use ActivityLogTrait;

    const USER_ACTIVE = 1;
    const USER_INACTIVE = 0;

    const SUPER_ADMIN = 1;
    const ADMIN = 2;
    const COMPANY_COORDINATOR = 3;
    const MARKET_DIRECTOR = 4;
    const COMPANY_AND_MARKET_COORDINATOR = 5;
    const POD_LEADER = 6;
    const AGENT = 0;

    const EXEMPTIONS = [
        User::SUPER_ADMIN,
        User::ADMIN
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'api_token',
        'password',
        'status',
        'last_login',
        'blacklisted',
        'role_type',
        'theme_mode',
        'pod_status',
        'two_auth',
        'two_auth_type',
        'verified'
    ];

    /**
     * The attributes that will be on the default result.
     *
     * @var array
     */
    protected $appends = [
        'name',
        'create_at_readable',
        'last_login_readable',        
        'update_url',
        'deactivate_url',
        'activate_url',
        'block_url',
        'unblock_url',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login' => 'datetime'
    ];

    public function getRememberToken()
    {
        return $this->remember_token;
    }

    public function setRememberToken($value)
    {
        $this->remember_token = $value;
    }

    /* Overrides default forgot password */
    public function broker() {
        return Password::broker('users');
    }

    /**
     * Get a fullname combination of first_name and last_name
     *
     * @return string
     */
    public function getNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Get a fullname combination of first_name and last_name
     *
     * @return string
     */
    public function getCreateAtReadableAttribute()
    {
        return $this->created_at->format('j M Y, g:i A') ;
    }

    /**
     * Create last login readable text 
     *
     * @return string
     */
    public function getLastLoginReadableAttribute()
    {
        if($this->last_login) {
            return $this->last_login->format('j M Y, g:i A') ;
        }
        return '-';
    }

    /**
     * Get update url of specified resource
     * 
     * @return string
     */
    public function getUpdateUrlAttribute() 
    {
        return route("users.update", $this->id);
    }

    /**
     * Get deactivate url of specified resource
     * 
     * @return string
     */
    public function getDeactivateUrlAttribute() 
    {
        if($this->status) {
            return route("users.deactivate", $this->id);
        }
        return "";
    }

    /**
     * Get activate url of specified resource
     * 
     * @return string
     */
    public function getActivateUrlAttribute() 
    {
        if(!$this->status) {
            return route("users.activate", $this->id);
        }
        return "";
    }    

    /**
     * Get block url of specified resource
     * 
     * @return string
     */
    public function getBlockUrlAttribute() 
    {
        if(!$this->blacklisted) {
            return route("users.block", $this->id);
        }
        return "";
    }

    /**
     * Get unblock url of specified resource
     * 
     * @return string
     */
    public function getUnblockUrlAttribute() 
    {
        if($this->blacklisted) {
            return route("users.unblock", $this->id);
        }
        return "";
    }

    /**
     * Prepare proper error handling for url attribute
     *
     * @return string
     */
    public function getAvatarUrlAttribute()
    {
        if ($this->info) {
            return asset($this->info->avatar_url);
        }

        return asset(theme()->getMediaUrlPath().'avatars/blank.png');
    }

    /**
     * User relation to info model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function info()
    {
        return $this->hasOne(UserInfo::class);
    }

    public function device_tokens() 
    {
        return $this->hasMany(DeviceToken::class);
    }

    /**
     * Pivotal table for market directors
     * 
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function pod_users() 
    {
        return $this->belongsToMany(User::class, 'pod_users', 'leader_id', 'user_id')->withTimestamps();;
    }    

    /**
     * Returns company id form user info
     * 
     * @return integer
     */
    public function getCompanyId() 
    {
        if($this->info && $this->info->company) {
            return $this->info->company->id;
        }
    }

    /**
     * Returns market id form user info
     * 
     * @return integer
     */
    public function getMarketId() 
    {
        if($this->info && $this->info->market) {
            return $this->info->market->id;
        }
    }

    /**
     * Save user login
     * 
     * @params $email = string
     * @params $status = string
     */
    public static function saveLogin($email, $status, $action = "login")
    {
        $user = User::where(["email" => $email])->first();
        if($user) {
            $ip = request()->ip();
            $position = Location::get($ip);
            $location = isset($position->countryName) ? $position->countryName : "";

            $browser = Agent::browser();
            $platform = Agent::platform(); 

            $payload = [
                "action" => $action,
                "user_id" => $user->id,
                "ip" => $ip,
                "location" => $location,
                "device" => $browser,
                "os" => $platform,
                "status" => $status
            ];
            LoginSession::create($payload);
        }
    } 
}

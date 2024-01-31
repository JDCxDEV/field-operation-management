<?php

namespace App\Models;

use App\Traits\ActivityLogTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Helpers\GoogleStorageHelper;

class UserInfo extends Model
{

    use SoftDeletes;
    use ActivityLogTrait;    

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'avatar',
        'company_id',
        'market_id',
        'phone',
        'website',
        'language',
        'country',
        'timezone',
        'currency',
        'communication',
        'marketing'
    ];   

    /**
     * The attributes that will be on the default result.
     *
     * @var array
     */
    protected $appends = [
        'company_name',
        'market_name',
        'avatar_url'
    ];

    /**
     * Prepare proper error handling for url attribute
     *
     * @return string
     */
    public function getAvatarUrlAttribute()
    {
        if($avatar = $this->renderAvatarUrl()) {
           return $avatar; 
        }

        // check if the avatar is an external url, eg. image from google
        if (filter_var($this->avatar, FILTER_VALIDATE_URL)) {
            return $this->avatar;
        }

        // no avatar, return blank avatar
        return asset(theme()->getMediaUrlPath().'avatars/blank.png');
    }

    public function renderAvatarUrl()
    {
        // if file avatar exist in storage folder
        if ($this->avatar) {
            $gcs = new GoogleStorageHelper;
            // get avatar url from storage
            return $gcs->renderFileUrl($this->avatar);
        }
    }

    /**
     * User info relation to user model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    
    /**
     * User info relation to user model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function getCompanyNameAttribute()
    {
        if($this->company) {
            return $this->company->company;
        }
        return "Not Assigned";        
    }


    /**
     * User info relation to user model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function getMarketNameAttribute()
    {
        if($this->market) {
            return $this->market->market_name;
        }
        return "Not Assigned";
    }    


    /**
     * Unserialize values by default
     *
     * @param $value
     *
     * @return mixed|null
     */
    public function getCommunicationAttribute($value)
    {
        // test to un-serialize value and return as array
        $data = @unserialize($value);
        if ($data !== false) {
            return $data;
        } else {
            return null;
        }
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

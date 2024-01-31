<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Status extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'status',
        'definition',
        'order',
    ];

    protected $appends = [
        'create_at_readable',
        'find_url',
        'update_url',
        'delete_url',
        'force_delete_url',
        'restore_url',
        'total_count'
    ];
    
    /**
     * Get readable created_at
     *
     * @return string
     */
    public function getCreateAtReadableAttribute()
    {
        return $this->created_at->format('j M Y, g:i A');
    }

    public function getTotalCountAttribute()
    {
        return $this->forms->count();
    }

    /**
     * Get find url of specified resource
     * 
     * @return string
     */
    public function getFindUrlAttribute() 
    {
        return route("statuses.find", $this->id);
    }

    /**
     * Get update url of specified resource
     * 
     * @return string
     */
    public function getUpdateUrlAttribute() 
    {
        return route("statuses.update", $this->id);
    }

    /**
     * Get delete url of specified resource
     * 
     * @return string
     */
    public function getDeleteUrlAttribute() 
    {
        if(!$this->deleted_at) {
            return route("statuses.delete", $this->id);
        }
        return "";
    }

    /**
     * Get delete url of specified resource
     * 
     * @return string
     */
    public function getForceDeleteUrlAttribute() 
    {
        if($this->deleted_at) {
            return route("statuses.force-delete", $this->id);
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
            return route("statuses.restore", $this->id);
        }
        return "";
    }

    public function forms() 
    {
        return $this->hasMany(Form::class);
    }
}

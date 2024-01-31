<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormTemplate extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'content',  
        'parent_id',
        'is_active'
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
        'activate_url'
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
        return route("form-templates.find", $this->id);
    }

    /**
     * Get update url of specified resource
     * 
     * @return string
     */
    public function getUpdateUrlAttribute() 
    {
        return route("form-templates.update", $this->id);
    }

    /**
     * Get delete url of specified resource
     * 
     * @return string
     */
    public function getDeleteUrlAttribute() 
    {
        if(!$this->deleted_at) {
            return route("form-templates.delete", $this->id);
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
            return route("form-templates.restore", $this->id);
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
        if(!$this->is_active) {
            return route("form-templates.activate", $this->id);
        }
        return "";
    }  
}

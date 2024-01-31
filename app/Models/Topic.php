<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Topic extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'order',        
        'title'
    ];

    protected $appends = [
        'create_at_readable',
        'items_count',
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
        return $this->created_at->format('j M Y, g:i A');
    }

    public function getItemsCountAttribute()
    {
        return $this->items->count();
    }
    
    /**
     * Get find url of specified resource
     * 
     * @return string
     */
    public function getFindUrlAttribute() 
    {
        return route("topics.find", $this->id);
    }

    /**
     * Get update url of specified resource
     * 
     * @return string
     */
    public function getUpdateUrlAttribute() 
    {
        return route("topics.update", $this->id);
    }

    /**
     * Get delete url of specified resource
     * 
     * @return string
     */
    public function getDeleteUrlAttribute() 
    {
        if(!$this->deleted_at) {
            return route("topics.delete", $this->id);
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
            return route("topics.restore", $this->id);
        }
        return "";
    }

    public function getItems()
    {
        return $this->items()->orderBy("order", "asc")->get();
    }

    public function items()
    {
        return $this->hasMany(TopicItem::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Core\Traits\SpatieLogsActivity;

class TopicItem extends Model
{
    use HasFactory;
    use SpatieLogsActivity;
    use SoftDeletes;

    protected $fillable = [
        'order',
        'topic_id',        
        'title',
        'content'
    ];

    protected $appends = [
        'create_at_readable',
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

    /**
     * Get update url of specified resource
     * 
     * @return string
     */
    public function getUpdateUrlAttribute() 
    {
        return route("topic-items.update", $this->id);
    }

    /**
     * Get delete url of specified resource
     * 
     * @return string
     */
    public function getDeleteUrlAttribute() 
    {
        if(!$this->deleted_at) {
            return route("topic-items.delete", $this->id);
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
            return route("topic-items.restore", $this->id);
        }
        return "";
    }
}

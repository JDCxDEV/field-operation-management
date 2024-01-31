<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Form;

class FormFile extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'form_id',
        'id_type',
        'file_type',
        'file_path'
    ];

    public function form() 
    {
        return $this->belongsTo(Form::class);
    }
}

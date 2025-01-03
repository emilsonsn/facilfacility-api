<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Action extends Model
{
    use HasFactory, SoftDeletes;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';

    public $table = 'actions';
    
    public $fillable = [
        'component_id',
        'name',
        'type',
        'date',
        'category',
        'condition',
        'priority',
        'frequency',
        'coast',
        'curracy',
        'description',
        'image',
    ];

    public function getImageAttribute($value){
        return $value ? asset($value) : null;
    }

    public function component(){
        return $this->belongsTo(Component::class);
    } 
    
}

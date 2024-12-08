<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Component extends Model
{
    use HasFactory, SoftDeletes;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';

    public $table = 'components';
    
    public $fillable = [
        'facility_id',
        'group',
        'uniformat',
        'name',
        'time_left_by_condition',
        'condition',
        'year_installed',
        'quantity',
        'unity',
        'time_left_by_lifespan',
        'coast',
        'currency',
        'description',
        'image',
    ];

    public function facility(){
        return $this->belongsTo(Facility::class);
    }
}

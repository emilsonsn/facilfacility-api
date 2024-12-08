<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Facility extends Model
{
    use HasFactory, SoftDeletes;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';

    public $table = 'facilities';

    public $fillable = [
        'name',
        'user_id',
        'number',
        'used',
        'size',
        'unity',
        'report_last_update',
        'consultant_name',
        'address',
        'city',
        'region',
        'country',
        'zip_code',
        'year_installed',
        'replacement_cost',
        'description',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function images(){
        return $this->hasMany(FacilityImage::class);
    }
    
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FacilityImage extends Model
{
    use HasFactory, SoftDeletes;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';

    public $table = 'facility_images';

    public $fillable = [
        'filename',
        'path',
        'facility_id'
    ];

    public function getPathAttribute($attribute){
        return $attribute ? asset('storage/' . $attribute) : null;
    }

    public function facility(){
        return $this->belongsTo(Facility::class);
    }
    
}

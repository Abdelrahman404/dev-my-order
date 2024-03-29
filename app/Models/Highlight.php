<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Highlight extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function getImagesAttribute($value){

        return json_decode($value);
    }
}

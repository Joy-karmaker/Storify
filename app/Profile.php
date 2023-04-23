<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\Factories\HasFactory;

class Profile extends Model
{
    //use HasFactory;

    protected $fillable = ['biography', 'address'];

    public function user() {
      return $this->belongsTo(\App\User::class);
    }
}

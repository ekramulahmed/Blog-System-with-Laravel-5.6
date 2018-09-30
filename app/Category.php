<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    // Many to many relationship
    public function posts(){
      return $this->belongsToMany('App\Post')->withTimestamps();
    }
}

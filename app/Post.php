<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    // Relationship with User Model
    public function user(){
      return $this->belongsTo('App\User');
    }

    // mulriple post er sathe belong kore, prevet table (category_post_table.php)
    public function categories(){
      return $this->belongsToMany('App\Category')->withTimestamps();
    }

    //
    public function tags(){
      return $this->belongsToMany('App\Tag')->withTimestamps();
    }

}

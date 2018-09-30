<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    // ---------- Create users function (many user for 1 role)
    public function users(){
      return $this->hasMany('App\User');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    // return dashboard
    public function index(){
      return view('admin.dashboard');
    }
}

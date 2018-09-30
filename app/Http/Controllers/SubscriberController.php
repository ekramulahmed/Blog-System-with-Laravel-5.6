<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Subscriber;
use Brian2694\Toastr\Facades\Toastr;

class SubscriberController extends Controller
{
    // -- for subscriber
    public function store(Request $request){

      $this->validate($request,[
        'email' => 'required|email|unique:subscribers'
      ]);

      $subscriber = new Subscriber();
      $subscriber->email = $request->email;
      $subscriber->save();
      Toastr::success('You have Successfully added to our subscriber list :)', 'Success');
      return redirect()->back();

      // Debug
      // return $request->all();
    }
}

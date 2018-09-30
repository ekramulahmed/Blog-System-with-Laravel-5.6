<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Subscriber;
use Brian2694\Toastr\Facades\Toastr;

class SubscriberController extends Controller
{
    // ---- view subscriber list
    public function index(){
      $subscribers = Subscriber::latest()->get();
      return view('admin.subscriber', compact('subscribers'));
    }

    // ---------- delete subscriber from list
    public function destroy($subscriber){
      $subscriber = Subscriber::findOrfail($subscriber);
      $subscriber->delete();
      Toastr::success('Subscriber Successfully deleted :)', 'Success');
      return redirect()->back();

    }
}

<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\User;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class SettingsController extends Controller
{
    //
    public function index(){
      return view('admin.settings');
    }

    // update_profile
    public function updateProfile(Request $request){
      
      $this->validate($request,[
        'name' => 'required',
        'email' => 'required|email',
        'image' => 'required|image',
      ]);
      $image = $request->file('image');
      $slug = str_slug($request->name);
      $user = User::findOrFail(Auth::id());

      if(isset($image)){
        $currentDate = Carbon::now()->toDateString();
        $imageName = $slug.'-'.$currentDate.'-'.uniqid().'.'.$image->getClientOriginalExtension();

        if(!Storage::disk('public')->exists('profile')){
          Storage::disk('public')->makeDirectory('profile');

        }

        // delete old image from profile folder
        if(Storage::disk('public')->exists('profile/'.$user->image))
        {
            Storage::disk('public')->delete('profile/'.$user->image);
        }
        // resize profile image and save
        $profile = Image::make($image)->resize(500,500)->save($image->getClientOriginalExtension());
        Storage::disk('public')->put('profile/'.$imageName,$profile);
      }
      else {
        $imageName = $user->image;
      }
      $user->name = $request->name;
      $user->email = $request->email;
      $user->image = $imageName;
      $user->about = $request->about;
      $user->save();
      Toastr::success('Profile Successfully Updated :)', 'Success');
      return redirect()->back();
      // return $request;
    }

    // update_passwprd
    public function updatePassword(Request $request){

      $this->validate($request,[
        'old_password' => 'required',
        'password' => 'required|confirmed',
      ]);

      $hashedPassword = Auth::user()->password;
      // if old_password and database hash_password is same
      if(Hash::check($request->old_password,$hashedPassword))
      {
        // if new_password and database hash_password is not same
        if(!Hash::check($request->password,$hashedPassword))
        {
          $user = User::find(Auth::id());
          $user->password = Hash::make($request->password);
          $user->save();
          Toastr::success('Password Successfully Changed!!', 'Success');
          Auth::logout();
          return redirect()->back();
        }
        // if new_password and database hash_password is same
        else {
          Toastr::error('New password cannot be the same as old password!', 'Error');

        }
      }
      // if old_password and database hash_password is not same
      else {
        Toastr::error('Current password not match!', 'Error');
        return redirect()->back();
      }

      // return $request;
    }
}

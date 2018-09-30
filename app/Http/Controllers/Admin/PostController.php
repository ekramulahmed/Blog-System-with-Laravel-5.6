<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use App\Tag;
use App\Post;

use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

use App\Notifications\AuthorPostApproved;
use App\User;
use Illuminate\Support\Facades\Notification;

use App\Subscriber;
use App\Notifications\NewPostNotify;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // ----------
        $posts = Post::latest()->get();
        return view('admin.post.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // for Admin
        $categories = Category::all();
        $tags = Tag::all();
        return view('admin.post.create', compact('categories', 'tags'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // code for store
        $this->validate($request,[
          'title' => 'required',
          'image' => 'required',
          'categories' => 'required',
          'tags' => 'required',
          'body' => 'required',
        ]);
        $image = $request->file('image');
        $slug = str_slug($request->title);
        if(isset($image)){
          // make unique name for image
          $currentDate = Carbon::now()->toDateString();
          $imageName = $slug.'-'.$currentDate.'-'.uniqid().'.'.$image->getClientOriginalExtension();

          if(!Storage::disk('public')->exists('post')){
            Storage::disk('public')->makeDirectory('post');
          }

          $postImage = Image::make($image)->resize(1600,1066)->save($image->getClientOriginalExtension());
          Storage::disk('public')->put('post/'.$imageName, $postImage);
        }
        else {
          $imageName = 'default.png';
        }
        $post = new Post();
        $post->user_id = Auth::id();
        $post->title = $request->title;
        $post->slug = $slug;
        $post->image = $imageName;
        $post->body = $request->body;
        if(isset($request->status))
        {
          $post->status = true;
        }
        else {
          $post->status = false;
        }
        $post->is_approved = true;
        $post->save();

        $post->categories()->attach($request->categories);
        $post->tags()->attach($request->tags);

        // send notofocation to subscriber
        $subscribers = Subscriber::all();
        foreach($subscribers as $subscriber){
          Notification::route('mail', $subscriber->email)
              ->notify(new NewPostNotify($post));
        }

        Toastr::success('Post Successfully Saved !!', 'Success');
        return redirect()->route('admin.post.index');
        // return $request->all();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        // Show post details
        // return $post;
        return view('admin.post.show', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        // Edit post for admin
        $categories = Category::all();
        $tags = Tag::all();
        return view('admin.post.edit', compact('post', 'categories', 'tags'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
      // code for update
      $this->validate($request,[
            'title' => 'required',
            'image' => 'image',
            'categories' => 'required',
            'tags' => 'required',
            'body' => 'required',
        ]);
        $image = $request->file('image');
        $slug = str_slug($request->title);
        if(isset($image))
        {
            // make unipue name for image
            $currentDate = Carbon::now()->toDateString();
            $imageName  = $slug.'-'.$currentDate.'-'.uniqid().'.'.$image->getClientOriginalExtension();

            if(!Storage::disk('public')->exists('post'))
            {
                Storage::disk('public')->makeDirectory('post');
            }
            // delete old post image
            if(Storage::disk('public')->exists('post/'.$post->image))
            {
                Storage::disk('public')->delete('post/'.$post->image);
            }
            $postImage = Image::make($image)->resize(1600,1066)->save($image->getClientOriginalExtension());
            Storage::disk('public')->put('post/'.$imageName,$postImage);

        } else {
            $imageName = $post->image;
        }

        $post->user_id = Auth::id();
        $post->title = $request->title;
        $post->slug = $slug;
        $post->image = $imageName;
        $post->body = $request->body;
        if(isset($request->status))
        {
            $post->status = true;
        }else {
            $post->status = false;
        }
        $post->is_approved = true;
        $post->save();

        $post->categories()->sync($request->categories);
        $post->tags()->sync($request->tags);

        Toastr::success('Post Successfully Updated :)','Success');
        return redirect()->route('admin.post.index');
    }

    // Work on pending
    public function pending(){
      $posts = Post::where('is_approved', false)->get();
      return view('admin.post.pending', compact('posts'));
    }

    // work on approval
    public function approval($id){

      $post = Post::find($id);
      if ($post->is_approved == false){

        $post->is_approved = true;
        $post->save();

        // send notification to author
        $post->user->notify(new AuthorPostApproved($post));

        // send notofocation to subscriber
        $subscribers = Subscriber::all();
        foreach($subscribers as $subscriber){
          Notification::route('mail', $subscriber->email)
              ->notify(new NewPostNotify($post));
        }

        Toastr::success('Post Successfully Approved !!', 'Success');
      }
      else {
        Toastr::info('This post is already Approved !!', 'Info');
      }
      return redirect()->back();
      // return $id;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        // image delete
        if(Storage::disk('public')->exists('post/'.$post->image)){
          Storage::disk('public')->delete('post/'.$post->image);
        }
        // category & tag delete
        $post->categories()->detach();
        $post->tags()->detach();
        $post->delete();
        Toastr::success('Post Successfully Deleted !!', 'Success');
        return redirect()->back();
        // debug
        // return $post;
    }
}

<?php

namespace App\Http\Controllers\Author;

use App\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Category;
use App\Tag;

use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

use App\Notifications\NewAuthorPost;
use App\User;
use Illuminate\Support\Facades\Notification;


use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $posts = Auth::User()->posts()->latest()->get();
        return view('author.post.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // --- Author
        $categories = Category::all();
        $tags = Tag::all();
        return view('author.post.create', compact('categories', 'tags'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // code for store (Author)
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
        // make it false
        $post->is_approved = false;
        $post->save();

        $post->categories()->attach($request->categories);
        $post->tags()->attach($request->tags);

        // send notification to admin (21)
        $users = User::where('role_id', '1')->get();
        Notification::send($users, new NewAuthorPost($post)); // eita sei $post, jeta user mattroi create korlo

        Toastr::success('Post Successfully Saved !!', 'Success');
        return redirect()->route('author.post.index');
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
        // ---- Author Post Permission fix
        if ($post->user_id != Auth::id()){
          Toastr::error('You are not authorized to access this post !', 'Error');
          return redirect()->back();
        }
        // ------------
        return view('author.post.show', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
      // ---- Author Post Permission fix
      if ($post->user_id != Auth::id()){
        Toastr::error('You are not authorized to access this post !', 'Error');
        return redirect()->back();
      }

      // Edit post for author
      $categories = Category::all();
      $tags = Tag::all();
      return view('author.post.edit', compact('post', 'categories', 'tags'));
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
        // ---- Author Post Permission fix
        if ($post->user_id != Auth::id()){
          Toastr::error('You are not authorized to access this post !', 'Error');
          return redirect()->back();
        }

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
  //            make unipue name for image
              $currentDate = Carbon::now()->toDateString();
              $imageName  = $slug.'-'.$currentDate.'-'.uniqid().'.'.$image->getClientOriginalExtension();

              if(!Storage::disk('public')->exists('post'))
              {
                  Storage::disk('public')->makeDirectory('post');
              }
  //            delete old post image
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
          $post->is_approved = false;
          $post->save();

          $post->categories()->sync($request->categories);
          $post->tags()->sync($request->tags);

          Toastr::success('Post Successfully Updated :)','Success');
          return redirect()->route('author.post.index');
    }

    /* work on Pending
    public function pending(){
      $posts = Post::where('is_approved', false)->get();
      return view('admin.post.pending', compact('posts'))
    }
    */


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
      // ---- Author Post Permission fix
      if ($post->user_id != Auth::id()){
        Toastr::error('You are not authorized to access this post !', 'Error');
        return redirect()->back();
      }

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

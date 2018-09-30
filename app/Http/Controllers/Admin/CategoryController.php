<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;


class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $categories = Category::latest()->get();
        return view('admin.category.index', compact('categories'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('admin.category.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $this->validate($request,[
          'name' => 'required|unique:categories',
          'image' => 'required|mimes:jpeg,bmp,png,jpg'
      ]);
      // get form image
      $image = $request->file('image');
      $slug = str_slug($request->name);
      if (isset($image))
      {
          // make unique name for image
          $currentDate = Carbon::now()->toDateString();
          $imagename = $slug.'-'.$currentDate.'-'.uniqid().'.'.$image->getClientOriginalExtension();

          // Check if Category Dir exists
          if (!Storage::disk('public')->exists('category'))
          {
              Storage::disk('public')->makeDirectory('category');
          }
          // Resize image for category and upload
          $category = Image::make($image)->resize(1600,479)->save($image->getClientOriginalExtension());
          Storage::disk('public')->put('category/'.$imagename,$category);

          // Check if Category Slider Dir exists
          if (!Storage::disk('public')->exists('category/slider'))
          {
              Storage::disk('public')->makeDirectory('category/slider');
          }
          // Resize image for category slider and upload
          $slider = Image::make($image)->resize(500,333)->save($image->getClientOriginalExtension());
          Storage::disk('public')->put('category/slider/'.$imagename,$slider);

      } else {
          $imagename = "default.png";
      }

      $category = new Category();
      $category->name = $request->name;
      $category->slug = $slug;
      $category->image = $imagename;
      $category->save();
      Toastr::success('Category Successfully Added !!' ,'Success');
      return redirect()->route('admin.category.index');
      // return $request;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // for edit
        $category = Category::find($id);
        return view('admin.category.edit', compact('category'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // for images
        $this->validate($request,[
            'name' => 'required',
            'image' => 'mimes:jpeg,bmp,png,jpg'
        ]);
        // get form image
        $image = $request->file('image');
        $slug = str_slug($request->name);
        // for replace new image
        $category = Category::find($id);
        if (isset($image))
        {
            // make unique name for image
            $currentDate = Carbon::now()->toDateString();
            $imagename = $slug.'-'.$currentDate.'-'.uniqid().'.'.$image->getClientOriginalExtension();

            // Check if Category Dir exists
            if (!Storage::disk('public')->exists('category'))
            {
                Storage::disk('public')->makeDirectory('category');
            }

            // Delete old image in category folder
            if(Storage::disk('public')->exists('category/'.$category->image)){
              Storage::disk('public')->delete('category/'.$category->image);
            }

            // Resize image for category and upload (for advoid conflict use $categoryImage)
            $categoryImage = Image::make($image)->resize(1600,479)->save($image->getClientOriginalExtension());
            Storage::disk('public')->put('category/'.$imagename,$categoryImage);

            // Check if Category Slider Dir exists
            if (!Storage::disk('public')->exists('category/slider'))
            {
                Storage::disk('public')->makeDirectory('category/slider');
            }

            // Delete old image in category->slider folder
            if(Storage::disk('public')->exists('category/slider/'.$category->image)){
              Storage::disk('public')->delete('category/slider/'.$category->image);
            }


            // Resize image for category slider and upload
            $slider = Image::make($image)->resize(500,333)->save($image->getClientOriginalExtension());
            Storage::disk('public')->put('category/slider/'.$imagename,$slider);

        } else {
            $imagename = $category->image;
        }

        // $category = new Category();
        $category->name = $request->name;
        $category->slug = $slug;
        $category->image = $imagename;
        $category->save();
        Toastr::success('Category Successfully Updated !!' ,'Success');
        return redirect()->route('admin.category.index');
        // return $request;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = Category::find($id);

        // for delete image from category
        if(Storage::disk('public')->exists('category/'.$category->image)){
          Storage::disk('public')->delete('category/'.$category->image);
        }

        // for delete image from slider
        if(Storage::disk('public')->exists('category/slider/'.$category->image)){
          Storage::disk('public')->delete('category/slider/'.$category->image);
        }
        $category->delete();
        Toastr::success('Category Successfully Deleted !!' ,'Success');
        return redirect()->back();

        // specific id er everything return korbe
        // return $category = Category::find($id);
    }
}

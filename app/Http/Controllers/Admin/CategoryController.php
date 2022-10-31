<?php

namespace App\Http\Controllers\Admin;

use App\CPU\Helpers;
use App\Models\Category;
use Illuminate\Http\Request;
use function App\CPU\translate;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Validator;


class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = new Category();
        $query_param = [];
        $search = $request['search'];

        if($request->has('search')) {
            $key = explode(' ', $request['search']);
            $categories=$categories->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        }

        $categories = $categories->where(['position'=>0])->latest()->paginate(Helpers::pagination_limit())->appends($query_param);
        return view('admin-views.category.index',compact('categories', 'search'));
    }
    public function sub_index(Request $request)
    {
        $categories = new Category();
        $query_param = [];
        $search = $request['search'];

        if($request->has('search')) {
            $key = explode(' ', $request['search']);
            $categories=Category::where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        }

        $categories = $categories->with(['parent'])->where(['position'=>1])->latest()->paginate(Helpers::pagination_limit())->appends($query_param);
        return view('admin-views.category.sub-index',compact('categories', 'search'));
    }
    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required|unique:categories,name'
        ]);

        if (!empty($request->file('image'))) {
            $image_name =  Helpers::upload('category/', 'png', $request->file('image'));
        } else {
            $image_name = 'def.png';
        }
        $category = new Category();
        $category->name = $request->name;
        $category->image = $image_name;
        $category->parent_id = $request->parent_id == null ? 0 : $request->parent_id;
        $category->position = $request->position;
        $category->save();

        Toastr::success(translate('Category stored successfully'));
        return back();
    }

    public function status(Request $request)
    {
        $category = category::find($request->id);
        $category->status = $request->status;
        $category->save();
        Toastr::success(translate('Category status updated'));
        return back();
    }
    public function edit($id)
    {
        $category = category::find($id);
        return view('admin-views.category.edit', compact('category'));
    }
    public function edit_sub($id)
    {
        $category = category::find($id);
        return view('admin-views.category.edit-sub-category', compact('category'));
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
        ], [
            'name.required' => translate('Name is required'),
        ]);
        $category = category::find($id);
        $category->name = $request->name;
        $category->image = $request->has('image') ? Helpers::update('category/', $category->image, 'png', $request->file('image')) : $category->image;
        $category->save();

        Toastr::success(translate('Category updated successfully'));
        return back();
    }
    public function update_sub(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
        ], [
            'name.required' => translate('Name is required'),
        ]);
        $category = category::find($id);
        $category->name = $request->name;
        $category->save();

        Toastr::success(translate('Sub Category updated successfully'));
        return back();
    }
    public function delete(Request $request)
    {
        $category = category::find($request->id);

        if ($category->childes->count()==0){
            Helpers::delete('category/' . $category['image']);
            $category->delete();
            Toastr::success(translate('Category removed'));
        }else{
            Toastr::warning(translate('Remove subcategories first'));
        }
        return back();
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\CPU\Helpers;
use App\Models\Brand;
use Brian2694\Toastr\Facades\Toastr;
use function App\CPU\translate;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::latest()->paginate(Helpers::pagination_limit());
        return view('admin-views.brand.index', compact('brands'));
    }
    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required',
            'image'=>'required'
        ], [
            'name.required' => translate('Name is required'),
        ]);

        if (!empty($request->file('image'))) {
            $image_name =  Helpers::upload('brand/', 'png', $request->file('image'));
        } else {
            $image_name = 'def.png';
        }
        $brand = new Brand();
        $brand->name = $request->name;
        $brand->image = $image_name;
        $brand->save();

        Toastr::success(translate('Brand stored successfully'));
        return back();
    }
    public function edit($id)
    {
        $brand = Brand::find($id);
        return view('admin-views.brand.edit',compact('brand'));
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
        ], [
            'name.required' => translate('Name is required'),
        ]);

        $brand = Brand::find($id);
        $brand->name = $request->name;
        $brand->image = $request->has('image') ? Helpers::update('brand/', $brand->image, 'png', $request->file('image')) : $brand->image;
        $brand->save();

        Toastr::success(translate('Brand updated successfully'));
        return back();
    }
    public function delete(Request $request)
    {
        $brand = Brand::find($request->id);
        Helpers::delete('brand/' . $brand['image']);
        $brand->delete();

        Toastr::success(translate('Brand deleted successfully'));
        return back();
    }
}

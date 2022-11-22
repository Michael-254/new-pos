<?php

namespace App\Http\Controllers\Api\V1;

use App\CPU\Helpers;
use App\Models\Brand;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;



class BrandController extends Controller
{
    //Get Brand
    public function getIndex(Request $request)
    {

        $limit = $request['limit'] ?? 10;
        $offset = $request['offset'] ?? 1;

        $brands = Brand::latest()->paginate($limit, ['*'], 'page', $offset);
        $data =  [
            'total' => $brands->total(),
            'limit' => $limit,
            'offset' => $offset,
            'brands' => $brands->items()
        ];

        return response()->json($data, 200);
    }
    //Save Brand
    public function postStore(Request $request, Brand $brand)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name'          => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => Helpers::error_processor($validator)], 403);
            }
            // Image upload
            if (!empty($request->file('image'))) {
                $image_name =  Helpers::upload('brand/', 'png', $request->file('image'));
            } else {
                $image_name = 'def.png';
            }
            $brand = new Brand();
            $brand->name = $request->name;
            $brand->image = $image_name;
            $brand->save();
            return response()->json([
                'success' => true,
                'message' => 'Brand saved successfully',
            ], 200);
        } catch (\Throwable $th) {
            //dd($th);
            return response()->json([
                'success' => false,
                'message' => 'Brand not saved'
            ], 403);
        }
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Brand  $Brand
     * @return \Illuminate\Http\Response
     */
    public function postUpdate(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name'          => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => Helpers::error_processor($validator)], 403);
            }
            $brand = Brand::findOrFail($request->id);
            $brand->name = $request->name;
            $brand->image = $request->has('image') ? Helpers::update('brand/', $brand->image, 'png', $request->file('image')) : $brand->image;
            $brand->save();
            return response()->json([
                'success' => true,
                'message' => 'Brand updated successfully',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Brand not updated'
            ], 403);
        }
    }
    //Delete Brand
    public function delete(Request $request)
    {
        try {
            $brand = Brand::findOrFail($request->id);
            Helpers::delete('brand/' . $brand['image']);
            $brand->delete();
            return response()->json(
                ['success' => true, 'message' => 'Brand deleted successfully',],
                200
            );
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Brand not deleted'
            ], 403);
        }
    }
    //public function getSearch($name)
    public function getSearch(Request $request)
    {
        $limit = $request['limit'] ?? 10;
        $offset = $request['offset'] ?? 1;

        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $brands = Brand::where('name', 'LIKE', '%' . $request->name . '%')->latest()->paginate($limit, ['*'], 'page', $offset);
        $data =  [
            'total' => $brands->total(),
            'limit' => $limit,
            'offset' => $offset,
            'brands' => $brands->items()
        ];
        return response()->json($data, 200);
    }


    public function updateStatus(Request $request)
    {
        $brand = Brand::find($request->id);
        $brand->status = !$brand['status'];
        $brand->update();
        return response()->json([
            'message' => 'Status updated successfully',
        ], 200);
    }

}

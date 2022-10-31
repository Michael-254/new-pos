<?php

namespace App\Http\Controllers\Api\V1;

use App\CPU\Helpers;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    //Get Category
    public function getIndex(Request $request)
    {
        $limit = $request['limit'] ?? 10;
        $offset = $request['offset'] ?? 1;
        $categories = Category::position()->latest()->paginate($limit, ['*'], 'page', $offset);
        $data =  [
            'total' => $categories->total(),
            'limit' => $limit,
            'offset' => $offset,
            'categories' => $categories->items()
        ];
        return response()->json($data, 200);
    }
    //Save Category
    public function postStore(Request $request, Category $category)
    {
        $validator = Validator::make($request->all(), [
            'name'          => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        //Image Upload
        if (!empty($request->file('image'))) {
            $image_name =  Helpers::upload('category/', 'png', $request->file('image'));
        } else {
            $image_name = 'def.png';
        }
        try {
            $category->name = $request->name;
            $category->parent_id = $request->parent_id == null ? 0 : $request->parent_id;
            $category->position = 0;
            $category->image = $image_name;
            $category->save();
            return response()->json([
                'success' => true,
                'message' => 'Category saved successfully',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Category not saved'
            ], 403);
        }
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function postUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'          => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $category = Category::findOrFail($request->id);
        $category->name = $request->name;
        $category->image = $request->has('image') ? Helpers::update('category/', $category->image, 'png', $request->file('image')) : $category->image;
        $category->update();
        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully',
        ], 200);
    }
    //Delete Category
    public function delete(Request $request)
    {
        $category = Category::findOrFail($request->id);
        if ($category->childes->count() == 0) {
            //dd($category);
            Helpers::delete('category/' . $category['image']);
            $category->delete();
            return response()->json(['success' => true, 'message' => 'Category deleted successfully',], 200);
        } else {
            return response()->json(['success' => false, 'message' => 'Remove subcategories first'], 200);
        }
    }

    public function getSearch(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $limit = $request['limit'] ?? 10;
        $offset = $request['offset'] ?? 1;
        $categories = Category::active()->position()->where('name', 'LIKE', '%' . $request->name . '%')->paginate($limit, ['*'], 'page', $offset);
        $data =  [
            'limit' => $limit,
            'offset' => $offset,
            'categories' => $categories->items()
        ];
        return response()->json($data, 200);
    }
    public function updateStatus(Request $request)
    {
        $category = Category::find($request->id);
        $category->status = !$category['status'];
        $category->update();
        return response()->json([
            'message' => 'Status updated successfully',
        ], 200);
    }
}

<?php

namespace App\Http\Controllers\Api\V1;

use App\CPU\Helpers;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Admin\Categoy\CategoryUpdateRequest;
use App\Http\Requests\Admin\Categoy\SubCategoryStoreRequest;

class SubCategoryController extends Controller
{
    //Get Category
    public function getIndex(Request $request)
    {
        $limit = $request['limit'] ?? 10;
        $offset = $request['offset'] ?? 1;
        $category_id = $request['category_id'] ?? 1;

        try {
            $subCategories = Category::where(['position' => 1,'parent_id' => $category_id])->latest()->paginate($limit, ['*'], 'page', $offset);
            $data =  [
                'total' => $subCategories->total(),
                'limit' => $limit,
                'offset' => $offset,
                'subCategories' => $subCategories->items()
            ];
            return response()->json($data, 200);
        } catch (\Exception $e) {
            return response()->json(['Result' => 'No Data not found'], 404);
        }
    }
    //Save Category
    public function postStore(Request $request, Category $subCategory)
    {
        //Image Upload
        if (!empty($request->file('image'))) {
            $image_name =  Helpers::upload('category/', 'png', $request->file('image'));
        } else {
            $image_name = 'def.png';
        }
        try {
            $subCategory->name = $request->name;
            //$subCategory->parent_id = $request->parent;
            $subCategory->parent_id = $request->parent_id;
            $subCategory->position = 1;
            $subCategory->image = $image_name;
            $subCategory->save();
            return response()->json([
                'success' => true,
                'message' => 'Sub Category saved successfully',
            ], 200);
        } catch (\Exception $th) {
            info($th);
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
    public function postUpdate(CategoryUpdateRequest $request)
    {
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
        try {
            $category = Category::findOrFail($request->id);
            $image_path  = public_path('storage/category/') . $category->image;
            if (!is_null($category)) {
                $category->delete();
                unlink($image_path);
                return response()->json(
                    [
                        'success' => true,
                        'message' => 'Category deleted successfully',
                    ],
                    200
                );
            }
        } catch (\Exception $th) {
            info($th);
            return response()->json([
                'success' => false,
                'message' => 'Category not deleted',
                'err' > $th
            ], 403);
        }
    }

    public function getSearch(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'limit' => 'required',
            'offset' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $result = Category::active()->where('name', 'LIKE', '%' . $request->name . '%')->get();
        if (count($result)) {
            return Response()->json($result, 200);
        } else {
            return response()->json(['message' => 'No Data not found'], 404);
        }
    }
}

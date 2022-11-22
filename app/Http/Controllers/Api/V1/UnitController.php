<?php

namespace App\Http\Controllers\Api\V1;

use App\CPU\Helpers;
use App\Models\Unit;
use App\Models\Brand;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Admin\Unit\UnitStoreRequest;
use App\Http\Requests\Admin\Unit\UnitUpdateRequest;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    //Get Category
    public function getIndex(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'required',
            'offset' => 'required',
        ]);
        $limit = $request['limit'] ?? 10;
        $offset = $request['offset'] ?? 1;

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $units = Unit::latest()->paginate($limit, ['*'], 'page', $offset);
        $data =  [
            'total' => $units->total(),
            'limit' => $limit,
            'offset' => $offset,
            'units' => $units->items()
        ];
        return response()->json($data, 200);
    }
    //Save Brand
    public function postStore(UnitStoreRequest $request, Unit $unit)
    {
        try {
            $unit->unit_type = $request->unit_type;
            $unit->save();
            return response()->json([
                'success' => true,
                'message' => 'Unit saved successfully',
            ], 200);
        } catch (\Throwable $th) {
            //dd($th);
            return response()->json([
                'success' => false,
                'message' => 'Unit not saved'
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
    public function postUpdate(UnitUpdateRequest $request)
    {
        $validator = Validator::make($request->all(), [
            'unit_type' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $brand = Unit::findOrFail($request->id);
        $brand->unit_type = $request->unit_type;
        $brand->update();
        return response()->json([
            'success' => true,
            'message' => 'Unit updated successfully',
        ], 200);
    }
    //Delete Brand
    public function delete(Request $request)
    {
        try {
            $brand = Unit::findOrFail($request->id);
            if (!is_null($brand)) {
                $brand->delete();
                return response()->json(
                    [
                        'success' => true,
                        'message' => 'Unit deleted successfully',
                    ],
                    200
                );
            }
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Unit not deleted'
            ], 403);
        }
    }
    public function getSearch(Request $request)
    {
        $limit = $request['limit'] ?? 10;
        $offset = $request['offset'] ?? 1;
        $units = Unit::where('unit_type', 'Like', '%' . $request->name . '%')->latest()->paginate($limit, ['*'], 'page', $offset);
        $data =  [
            'total' => $units->total(),
            'limit' => $limit,
            'offset' => $offset,
            'units' => $units->items()
        ];
        return response($data, 200);
    }
}

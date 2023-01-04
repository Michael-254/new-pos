<?php

namespace App\Http\Controllers\Api\V1;

use App\CPU\Helpers;
use App\Models\Account;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    //Get Category
    public function getIndex(Request $request)
    {
        $limit = $request['limit'] ?? 10;
        $offset = $request['offset'] ?? 1;
        $users = User::withCount('products')
            ->where('company_id', auth()->user()->company_id)
            ->latest()->paginate($limit, ['*'], 'page', $offset);
        $data =  [
            'total' => $users->total(),
            'limit' => $limit,
            'offset' => $offset,
            'users' => $users->items()
        ];
        return response()->json($data, 200);
    }
    //Save Category
    public function postStore(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'mobile' => 'required',
            'email' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        //Image Upload
        if (!empty($request->file('image'))) {
            $image_name =  Helpers::upload('user/', 'png', $request->file('image'));
        } else {
            $image_name = 'def.png';
        }
        try {
            $user->name = $request->name;
            $user->mobile = $request->mobile;
            $user->email = $request->email;
            $user->image = $image_name;
            $user->due_amount = $request->due_amount;
            $user->company_id = auth()->user()->company_id;
            $user->save();
            return response()->json([
                'success' => true,
                'message' => 'User saved successfully',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'User not saved'
            ], 403);
        }
    }

    public function getDetails(Request $request)
    {
        $user = User::findOrFail($request->id);
        return response()->json([
            'success' => true,
            'message' => 'User details',
            'user' => $user
        ], 200);
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
        try {
            $user = User::findOrFail($request->id);
            $request->validate([
                'name' => 'required',
                'mobile' => 'required',
                'email' => 'required',
            ]);
            $user->name = $request->name;
            $user->mobile = $request->mobile;
            $user->email = $request->email;
            $user->image = $request->has('image') ? Helpers::update('user/', $user->image, 'png', $request->file('image')) : $user->image;
            $user->save();
            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
            ], 200);
        } catch (\Throwable $th) {
            //dd($th);
            return response()->json([
                'success' => true,
                'message' => 'User not updated',
            ], 403);
        }
    }
    //Delete Category
    public function delete(Request $request)
    {
        try {
            $user = User::findOrFail($request->id);
            Helpers::delete('user/' . $user['image']);
            $user->delete();
            return response()->json([
                'success' => true,
                'message' => 'User deleted'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'User not deleted'
            ], 403);
        }
    }

    public function getSearch(Request $request)
    {
        $limit = $request['limit'] ?? 10;
        $offset = $request['offset'] ?? 1;
        $search = $request->name;
        // if (!empty($search)) {
        $result = User::where('name', 'like', '%' . $search . '%')->orWhere('mobile', 'like', '%' . $search . '%')->latest()->paginate($limit, ['*'], 'page', $offset);
        $data = [
            'total' => $result->total(),
            'limit' => $limit,
            'offset' => $offset,
            'users' => $result->items(),
        ];
        return response()->json($data, 200);
        // }
    }

    public function filterByCity(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'city' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $limit = $request['limit'] ?? 10;
        $offset = $request['offset'] ?? 1;
        if (!empty($request->city)) {
            $result = User::where('city', $request->city)->latest()->paginate($limit, ['*'], 'page', $offset);
            $data = [
                'total' => $result->total(),
                'limit' => $limit,
                'offset' => $offset,
                'user' => $result->items(),
            ];
            return response()->json($data, 200);
        } else {
            $data = [
                'total' => 0,
                'limit' => $limit,
                'offset' => $offset,
                'user' => [],
            ];
            return response()->json($data, 200);
        }
    }
}

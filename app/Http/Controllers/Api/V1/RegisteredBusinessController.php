<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductsResource;
use App\Models\BusinessSetting;
use App\Models\Product;
use Illuminate\Http\Request;

class RegisteredBusinessController extends Controller
{
    //Get Category
    public function getIndex(Request $request)
    {
        $limit = $request['limit'] ?? 10;
        $offset = $request['offset'] ?? 1;
        $merchants = BusinessSetting::select('id', 'shop_name', 'shop_logo', 'shop_address', 'shop_phone', 'shop_email', 'company_id', 'created_at', 'updated_at')
            ->latest()->paginate($limit, ['*'], 'page', $offset);
            
        foreach($merchants as $merchant) {
            $merchant['product_count'] = Product::where('company_id', $merchant->company_id)->count();
        }

        $data =  [
            'total' => $merchants->total(),
            'limit' => $limit,
            'offset' => $offset,
            'merchants' => $merchants->items()
        ];
        return response()->json($data, 200);
    }

    public function getSearch(Request $request)
    {
        $limit = $request['limit'] ?? 10;
        $offset = $request['offset'] ?? 1;
        $search = $request->name;
        // if (!empty($search)) {
        $result = BusinessSetting::select('id', 'shop_name', 'shop_logo', 'shop_address', 'shop_phone', 'shop_email', 'company_id', 'created_at', 'updated_at')
            ->where('shop_name', 'like', '%' . $search . '%')->orWhere('shop_phone', 'like', '%' . $search . '%')->latest()->paginate($limit, ['*'], 'page', $offset);

        foreach($merchants as $merchant) {
            $merchant['product_count'] = Product::where('company_id', $merchant->company_id)->count();
        }

        $data = [
            'total' => $result->total(),
            'limit' => $limit,
            'offset' => $offset,
            'merchants' => $result->items(),
        ];
        return response()->json($data, 200);
        // }
    }

    public function merchantProduct(Request $request)
    {
        $limit = $request['limit'] ?? 10;
        $offset = $request['offset'] ?? 1;
        $product = Product::where('company_id', $request->company_id)->latest()->paginate($limit, ['*'], 'page', $offset);
        $products = ProductsResource::collection($product);
        $data = [
            'total' => $products->total(),
            'limit' => $limit,
            'offset' => $offset,
            'products' => $products->items(),
        ];
        return response()->json($data, 200);
    }
}

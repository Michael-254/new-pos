<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\BusinessSetting;
use Illuminate\Http\Request;

class RegisteredBusinessController extends Controller
{
    //Get Category
    public function getIndex(Request $request)
    {
        $limit = $request['limit'] ?? 10;
        $offset = $request['offset'] ?? 1;
        $merchants = BusinessSetting::select('shop_name', 'shop_address', 'shop_phone', 'shop_email')
            ->latest()->paginate($limit, ['*'], 'page', $offset);
        $data =  [
            'total' => $merchants->total(),
            'limit' => $limit,
            'offset' => $offset,
            'merchants' => $merchants->items()
        ];
        return response()->json($data, 200);
    }
}

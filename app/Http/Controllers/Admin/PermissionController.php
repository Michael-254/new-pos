<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Order;
use Brian2694\Toastr\Facades\Toastr;
use App\CPU\Helpers;
use App\Models\Account;
use App\Models\Transaction;
use Carbon\Carbon;

class PermissionController extends Controller
{
    public function index()
    {
        return view('admin-views.permissions.index');
    }

    public function store(Request $request)
    {
        $company_id = auth('admin')->user()->company_id;
        $request->validate([
            'name' => 'required',
            'mobile' => 'required',
        ]);

        if (!empty($request->file('image'))) {
            $image_name =  Helpers::upload('permission/', 'png', $request->file('image'));
        } else {
            $image_name = 'def.png';
        }

        $split_name = explode(" ", $request->name);
        $dukapaq_member = Permission::where(['phone' => $request->mobile])->first();

        if (!$dukapaq_member) {
            $dukapaq_member = Permission::Create([
                'phone' => $request->mobile,
                'email' => $request->email ?? '',
                'f_name' => $split_name[0],
                'l_name' => $split_name[1] ? $split_name[1] : '',
                'password' => bcrypt(123456),
                'is_loyalty_enrolled' => $request->is_loyalty_enrolled,
            ]);
        }


        if ($request->is_loyalty_enrolled == 'Yes') {
            $dukapaq_member->update([
                'loyalty_points' => 100,
            ]);
        }

        $permission = new Permission;
        $permission->member_id = $dukapaq_member->id;
        $permission->name = $request->name;
        $permission->mobile = $request->mobile;
        $permission->email = $request->email;
        $permission->image = $image_name;
        $permission->balance = $request->balance;
        $permission->company_id = auth('admin')->user()->company_id;
        $permission->save();


        Toastr::success(translate('Permission Added successfully'));
        return back();
    }

    public function list(Request $request)
    {
        $accounts = Account::orderBy('id')->get();
        $query_param = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $permissions = Permission::where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%")
                        ->orWhere('mobile', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        } else {
            $permissions = new Permission;
        }
        //$walk_permission = $permissions->where('type',0)->get();
        $permissions = $permissions->where('company_id', auth('admin')->user()->company_id)->latest()->paginate(Helpers::pagination_limit())->appends($query_param);
        return view('admin-views.permissions.list', compact('permissions', 'accounts', 'search'));
    }

    public function view(Request $request, $id)
    {
        $permission = Permission::where('id', $id)->first();

        if (isset($permission)) {
            $query_param = [];
            $search = $request['search'];
            if ($request->has('search')) {
                $key = explode(' ', $request['search']);
                $orders = Order::where(['user_id' => $id])
                    ->where(function ($q) use ($key) {
                        foreach ($key as $value) {
                            $q->where('id', 'like', "%{$value}%");
                        }
                    });
                $query_param = ['search' => $request['search']];
            } else {
                $orders = Order::where(['user_id' => $id]);
            }

            $orders = $orders->latest()->paginate(Helpers::pagination_limit())->appends($query_param);
            return view('admin-views.permissions.view', compact('permission', 'orders', 'search'));
        }
        Toastr::error('Permission not found!');
        return back();
    }
 
    public function edit(Request $request)
    {
        $permission = Permission::where('id', $request->id)->first();
        return view('admin-views.permissions.edit', compact('permission'));
    }

    public function update(Request $request)
    {
        $permission = Permission::where('id', $request->id)->first();
        $request->validate([
            'name' => 'required',
            'mobile' => 'required',
        ]);

        $dukapaq_member = Permission::where(['phone' => $request->mobile])->first();

        if ($request->is_loyalty_enrolled == 'Yes' && $dukapaq_member->is_loyalty_enrolled == 'No') {
            $dukapaq_member->update([
                'is_loyalty_enrolled' => 'Yes',
                'loyalty_points' => 100,
            ]);
        }

        $permission->name = $request->name;
        $permission->mobile = $request->mobile;
        $permission->email = $request->email;
        $permission->image = $request->has('image') ? Helpers::update('permission/', $permission->image, 'png', $request->file('image')) : $permission->image;
        $permission->state = $request->state;
        $permission->city = $request->city;
        $permission->zip_code = $request->zip_code;
        $permission->address = $request->address;
        $permission->balance = $request->balance;
        $permission->save();

        Toastr::success(translate('Permission updated successfully'));
        return back();
    }

    public function delete(Request $request)
    {
        $permission = Permission::find($request->id);
        Helpers::delete('permission/' . $permission['image']);
        $permission->delete();

        Toastr::success(translate('Permission removed successfully'));
        return back();
    }
}

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

class RoleController extends Controller
{
    public function index()
    {
        $permissions = Permission::all();
        
        return view('admin-views.roles.index', compact('permissions'));
    }

    public function store(Request $request)
    {
        $company_id = auth('admin')->user()->company_id;
        $request->validate([
            'name' => 'required',
            'mobile' => 'required',
        ]);

        if (!empty($request->file('image'))) {
            $image_name =  Helpers::upload('role/', 'png', $request->file('image'));
        } else {
            $image_name = 'def.png';
        }

        $split_name = explode(" ", $request->name);
        $dukapaq_member = Role::where(['phone' => $request->mobile])->first();

        if (!$dukapaq_member) {
            $dukapaq_member = Role::Create([
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

        $role = new Role;
        $role->member_id = $dukapaq_member->id;
        $role->name = $request->name;
        $role->mobile = $request->mobile;
        $role->email = $request->email;
        $role->image = $image_name;
        $role->balance = $request->balance;
        $role->company_id = auth('admin')->user()->company_id;
        $role->save();


        Toastr::success(translate('Role Added successfully'));
        return back();
    }

    public function list(Request $request)
    {
        $accounts = Account::orderBy('id')->get();
        $query_param = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $roles = Role::where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%")
                        ->orWhere('mobile', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        } else {
            $roles = new Role;
        }
      
        $roles = $roles->where('company_id', auth('admin')->user()->company_id ?? 1)->latest()->paginate(Helpers::pagination_limit())->appends($query_param);
        return view('admin-views.roles.list', compact('roles', 'accounts', 'search'));
    }

    public function view(Request $request, $id)
    {
        $role = Role::where('id', $id)->first();

        if (isset($role)) {
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
            return view('admin-views.roles.view', compact('role', 'orders', 'search'));
        }
        Toastr::error('Role not found!');
        return back();
    }
  
    public function edit(Request $request)
    {
        $role = Role::where('id', $request->id)->first();
        return view('admin-views.roles.edit', compact('role'));
    }

    public function update(Request $request)
    {
        $role = Role::where('id', $request->id)->first();
        $request->validate([
            'name' => 'required',
            'mobile' => 'required',
        ]);

        $dukapaq_member = Role::where(['phone' => $request->mobile])->first();

        if ($request->is_loyalty_enrolled == 'Yes' && $dukapaq_member->is_loyalty_enrolled == 'No') {
            $dukapaq_member->update([
                'is_loyalty_enrolled' => 'Yes',
                'loyalty_points' => 100,
            ]);
        }

        $role->name = $request->name;
        $role->mobile = $request->mobile;
        $role->email = $request->email;
        $role->image = $request->has('image') ? Helpers::update('role/', $role->image, 'png', $request->file('image')) : $role->image;
        $role->state = $request->state;
        $role->city = $request->city;
        $role->zip_code = $request->zip_code;
        $role->address = $request->address;
        $role->balance = $request->balance;
        $role->save();

        Toastr::success(translate('Role updated successfully'));
        return back();
    }

    public function delete(Request $request)
    {
        $role = Role::find($request->id);
        Helpers::delete('role/' . $role['image']);
        $role->delete();

        Toastr::success(translate('Role removed successfully'));
        return back();
    }
}

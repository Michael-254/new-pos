<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use Spatie\Permission\Models\Role;
use App\Models\Order;
use Brian2694\Toastr\Facades\Toastr;
use App\CPU\Helpers;
use App\Models\Account;
use App\Models\Transaction;
use Carbon\Carbon;

use function App\CPU\translate;

class UserController extends Controller
{
    public function index()
    {
        $company_id = auth('admin')->user()->company_id;
        $roles = Role::where('company_id', auth('admin')->user()->company_id ?? 1)->get();

        return view('admin-views.users.index', compact('roles'));
    }
    public function store(Request $request)
    {
        $company_id = auth('admin')->user()->company_id;
        $request->validate([
            'f_name' => 'required',
            'l_name' => 'required',
            'mobile' => 'required',
        ]);

        if (!empty($request->file('image'))) {
            $image_name =  Helpers::upload('user/', 'png', $request->file('image'));
        } else {
            $image_name = 'def.png';
        }

        $split_name = explode(" ", $request->name);
        $dukapaq_member = Admin::where(['phone' => $request->mobile])->first();

        $dukapaq_member = Admin::Create([
            'phone' => $request->mobile,
            'email' => $request->email ?? '',
            'f_name' => $request->f_name,
            'l_name' => $request->l_name,
            'password' => bcrypt(123456),
            'is_loyalty_enrolled' => $request->is_loyalty_enrolled,
        ]);

        $user = new Admin;
        $user->name = $request->name;
        $user->mobile = $request->mobile;
        $user->email = $request->email;
        $user->image = $image_name;
        $user->balance = $request->balance;
        $user->company_id = auth('admin')->user()->company_id;
        $user->save();


        Toastr::success(translate('Admin Added successfully'));
        return back();
    }
    public function list(Request $request)
    {
        $query_param = [];
        $users = Admin::where('company_id', auth('admin')->user()->company_id)->latest()->paginate(Helpers::pagination_limit())->appends($query_param);
        return view('admin-views.users.list', compact('users', 'accounts', 'search'));
    }

    public function view(Request $request, $id)
    {
        $user = Admin::where('id', $id)->first();

        if (isset($user)) {
            return view('admin-views.users.view', compact('user', 'orders', 'search'));
        }
        Toastr::error('Admin not found!');
        return back();
    }

    public function edit(Request $request)
    {
        $user = Admin::where('id', $request->id)->first();
        return view('admin-views.users.edit', compact('user'));
    }
    public function update(Request $request)
    {
        $user = Admin::where('id', $request->id)->first();
        $request->validate([
            'email' => $request->email ?? '',
            'f_name' => $request->f_name,
            'l_name' => $request->l_name,
        ]);

        $user = Admin::where(['phone' => $request->mobile])->first();

        $user->f_name = $request->f_name;
        $user->l_name = $request->l_name;
        $user->mobile = $request->mobile;
        $user->email = $request->email;
        $user->image = $request->has('image') ? Helpers::update('user/', $user->image, 'png', $request->file('image')) : $user->image;
        $user->state = $request->state;
        $user->city = $request->city;
        $user->zip_code = $request->zip_code;
        $user->address = $request->address;
        $user->balance = $request->balance;
        $user->save();

        Toastr::success(translate('Admin updated successfully'));
        return back();
    }

    public function delete(Request $request)
    {
        $user = Admin::find($request->id);
        Helpers::delete('user/' . $user['image']);
        $user->delete();

        Toastr::success(translate('Admin removed successfully'));
        return back();
    }
}

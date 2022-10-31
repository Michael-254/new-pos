<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Unit;
use App\CPU\Helpers;
use Brian2694\Toastr\Facades\Toastr;
use function App\CPU\translate;

class UnitController extends Controller
{
    public function index()
    {
        $units = Unit::latest()->paginate(Helpers::pagination_limit());
        return view('admin-views.unit.index',compact('units'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'unit_type' => 'required|unique:units',
        ]);

        $unit = new Unit;
        $unit->unit_type = $request->unit_type;

        $unit->save();
        Toastr::success(translate('New Unit Type added'));
        return back();
    }
    public function edit($id)
    {
        $unit = Unit::find($id);

        return view('admin-views.unit.edit',compact('unit'));
    }
    public function update(Request $request, $id)
    {
        $unit = Unit::find($id);
        $request->validate([
            'unit_type' => 'required|unique:units,unit_type,'.$unit->id,
        ]);

        $unit->unit_type = $request->unit_type;
        $unit->save();

        Toastr::success(translate('Unit Type updated successfully'));
        return back();
    }
    public function delete($id)
    {
        $unit = Unit::find($id);
        $unit->delete();

        Toastr::success(translate('Unit Type removed'));
        return back();
    }
}

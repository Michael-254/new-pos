@extends('layouts.admin.app')

@section('title',\App\CPU\translate('add_new_role'))

@push('css_or_js')
<link rel="stylesheet" href="{{asset('assets/admin')}}/css/custom.css" />
@endpush

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title text-capitalize"><i class="tio-add-circle-outlined"></i> {{\App\CPU\translate('add_new_role')}}
                </h1>
            </div>
        </div>
    </div>
    <!-- End Page Header -->
    <div class="row gx-2 gx-lg-3">
        <div class="col-sm-12 col-lg-12  mb-lg-2">
            <div class="card">
                <div class="card-body">
                    <form action="{{route('admin.role.store')}}" method="post" id="product_form" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" class="form-control" name="balance" value=0>
                        <div class="row pl-2">
                            <div class="col-12 col-sm-4">
                                <div class="form-group">
                                    <label class="input-label">{{\App\CPU\translate('role_name')}} <span class="input-label-secondary text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="{{\App\CPU\translate('role_name')}}" required>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row pl-2">
                            @foreach($permissions as $permission)
                                <div class="col-12 col-sm-4">
                                    <div class="form-group">
                                        <input type="checkbox" id="{{ $permission->id }}" name="permission" value="{{ $permission->id }}">
                                        <label for="{{ $permission->id }}">{{ $permission->name }}</label><br>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <button type="submit" class="btn btn-primary">{{\App\CPU\translate('submit')}}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('script_2')
<script src={{asset("public/assets/admin/js/global.js")}}></script>
@endpush
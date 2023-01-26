@extends('layouts.admin.app')

@section('title',\App\CPU\translate('view_user'))

@push('css_or_js')
<link rel="stylesheet" href="{{asset('assets/admin')}}/css/custom.css" />
@endpush

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title text-capitalize"><i class="tio-visible"></i> {{\App\CPU\translate('view_user')}}
                </h1>
            </div>
        </div>
    </div>
    <!-- End Page Header -->
    <div class="row gx-2 gx-lg-3">
        <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
            <div class="card">
                <div class="card-body">
                    <form action="{{route('admin.user.update',[$user->id])}}" method="post" id="product_form" enctype="multipart/form-data">
                        @csrf
                        <div class="row pl-2">
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label class="input-label">First name</label>
                                    <input type="text" name="f_name" class="form-control" disabled value="{{ $user->f_name }}" placeholder="First name" required>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label class="input-label">Last name</label>
                                    <input type="text" name="l_name" class="form-control" disabled value="{{ $user->l_name }}" placeholder="Last name" required>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label class="input-label">{{\App\CPU\translate('mobile_no')}}</label>
                                    <input type="number" id="mobile" name="mobile" disabled class="form-control" value="{{ $user->mobile }}" placeholder="{{\App\CPU\translate('mobile_no')}}" required>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label class="input-label">{{\App\CPU\translate('email')}}</label>
                                    <input type="email" name="email" class="form-control" disabled value="{{ $user->email }}" placeholder="{{\App\CPU\translate('Ex_:_ex@example.com')}}">
                                </div>
                            </div>
                        </div>
                        <hr>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
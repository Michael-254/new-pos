@extends('layouts.admin.app')

@section('title',\App\CPU\translate('add_new_user'))

@push('css_or_js')
<link rel="stylesheet" href="{{asset('assets/admin')}}/css/custom.css" />
@endpush

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title text-capitalize"><i class="tio-add-circle-outlined"></i> {{\App\CPU\translate('add_new_user')}}
                </h1>
            </div>
        </div>
    </div>
    <!-- End Page Header -->
    <div class="row gx-2 gx-lg-3">
        <div class="col-sm-12 col-lg-12  mb-lg-2">
            <div class="card">
                <div class="card-body">
                    <form action="{{route('admin.user.store')}}" method="post" id="product_form" enctype="multipart/form-data">
                        @csrf
                        <div class="row pl-2">
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label class="input-label">First name <span class="input-label-secondary text-danger">*</span></label>
                                    <input type="text" name="f_name" class="form-control" value="{{ old('f_name') }}" placeholder="First name" required>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label class="input-label">Last name<span class="input-label-secondary text-danger">*</span></label>
                                    <input type="text" name="l_name" class="form-control" value="{{ old('l_name') }}" placeholder="Last name" required>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label class="input-label">{{\App\CPU\translate('mobile_no')}} <span class="input-label-secondary text-danger">*</span></label>
                                    <input type="number" id="mobile" name="mobile" class="form-control" value="{{ old('mobile') }}" placeholder="{{\App\CPU\translate('mobile_no')}}" required>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label class="input-label">{{\App\CPU\translate('email')}}</label>
                                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="{{\App\CPU\translate('Ex_:_ex@example.com')}}">
                                </div>
                            </div>
                        </div>
                        <div class="row pl-2">
                            <div class="col-12 col-sm-6">
                                <label>{{\App\CPU\translate('image')}}</label><small> ( {{\App\CPU\translate('ratio_1:1')}} )( {{\App\CPU\translate('optional')}} )</small>
                                <div class="custom-file">
                                    <input type="file" name="image" id="customFileEg1" class="custom-file-input" accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                    <label class="custom-file-label" for="customFileEg1">{{\App\CPU\translate('choose')}} {{\App\CPU\translate('file')}}</label>
                                </div>
                                <div class="form-group">
                                    <hr>
                                    <center>
                                        <img class="img-one-ci" id="viewer" src="{{asset('assets/admin/img/400x400/img2.jpg')}}" alt="image" />
                                    </center>
                                </div>
                            </div>
                        </div>

                        <hr>
                        <button type="submit" class="btn btn-primary">{{\App\CPU\translate('submit')}}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('script_2')
<script src="{{asset('public/assets/admin/js/global.js')}}"></script>
@endpush
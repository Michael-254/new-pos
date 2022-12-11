@extends('layouts.admin.app')

@section('title',\App\CPU\translate('update_supplier'))

@push('css_or_js')

@endpush

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title text-capitalize"><i class="tio-edit"></i> {{\App\CPU\translate('update_supplier')}}
                </h1>
            </div>
        </div>
    </div>
    <!-- End Page Header -->
    <div class="row gx-2 gx-lg-3">
        <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
            <div class="card">
                <div class="card-body">
                    <form action="{{route('admin.supplier.update',[$supplier->id])}}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row pl-2">
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label class="input-label">{{\App\CPU\translate('supplier_name')}} <span class="input-label-secondary text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" value="{{ $supplier->name }}" placeholder="{{\App\CPU\translate('supplier_name')}}" required>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label class="input-label">{{\App\CPU\translate('mobile_no')}} <span class="input-label-secondary text-danger">*</span></label>
                                    <input type="number" id="mobile" name="mobile" class="form-control" value="{{ $supplier->mobile }}" placeholder="{{\App\CPU\translate('mobile_no')}}" required>
                                </div>
                            </div>
                        </div>
                        <div class="row pl-2">
                            <div class="col-12 col-md-12">
                                <div class="form-group">
                                    <label class="input-label">{{\App\CPU\translate('email')}} <span class="input-label-secondary text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control" value="{{ $supplier->email }}" placeholder="{{\App\CPU\translate('Ex_:_ex@example.com')}}">
                                </div>
                            </div>
                        </div>
                        <div class="row pl-2">
                            <div class="col-12 col-md-12">
                                <label>{{\App\CPU\translate('image')}}</label><small class="text-danger"> ( {{\App\CPU\translate('ratio_1:1')}} )( {{\App\CPU\translate('optional')}} )</small>
                                <div class="custom-file">
                                    <input type="file" name="image" id="customFileEg1" class="custom-file-input" accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                    <label class="custom-file-label" for="customFileEg1">{{\App\CPU\translate('choose_file')}}</label>
                                </div>
                                <div class="form-group">
                                    <hr>
                                    <center>
                                        <img class="img-one-su" id="viewer" onerror="this.src='{{asset('assets/admin/img/400x400/img2.jpg')}}'" src="{{asset('storage/app/public/supplier')}}/{{$supplier['image']}}" alt="{{\App\CPU\translate('image')}}" />
                                    </center>
                                </div>
                            </div>
                        </div>

                        <hr>
                        <button type="submit" class="btn btn-primary">{{\App\CPU\translate('update')}}</button>
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
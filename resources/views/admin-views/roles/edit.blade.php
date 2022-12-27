@extends('layouts.admin.app')

@section('title',\App\CPU\translate('update_customer'))

@push('css_or_js')
<link rel="stylesheet" href="{{asset('assets/admin')}}/css/custom.css" />
@endpush

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title text-capitalize"><i class="tio-edit"></i> {{\App\CPU\translate('update_customer')}}
                </h1>
            </div>
        </div>
    </div>
    <!-- End Page Header -->
    <div class="row gx-2 gx-lg-3">
        <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
            <div class="card">
                <div class="card-body">
                    <form action="{{route('admin.customer.update',[$customer->id])}}" method="post" id="product_form" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" class="form-control" name="balance" min="0" step="0.01" value="{{ $customer->balance }}">
                        <div class="row pl-2">
                            <div class="col-12 col-sm-4">
                                <div class="form-group">
                                    <label class="input-label">{{\App\CPU\translate('customer_name')}} <span class="input-label-secondary text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" value="{{ $customer->name }}" placeholder="{{\App\CPU\translate('customer_name')}}" required>
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
<script>
    "use strict";
    $(document).on('ready', function() {
        @php($country = $customer->country)
        $("#country option[value='{{$country}}']").attr('selected', 'selected').change();
    })
</script>
<script src={{asset("public/assets/admin/js/global.js")}}></script>
@endpush

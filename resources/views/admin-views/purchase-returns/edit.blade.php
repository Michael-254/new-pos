@extends('layouts.admin.app')

@section('title',\App\CPU\translate('update_product'))

@push('css_or_js')
    {{-- <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{asset('assets/admin/css/tags-input.min.css')}}" rel="stylesheet"> --}}
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title text-capitalize"><i
                            class="tio-edit"></i> {{\App\CPU\translate('update_product')}} </h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <div class="card">
                    <div class="card-body">
                        <form action="{{route('admin.product.update',[$product['id']])}}" method="post"
                              id="product_form"
                              enctype="multipart/form-data">
                            @csrf

                            <div class="row pl-2">
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label class="input-label"
                                               for="exampleFormControlInput1">{{\App\CPU\translate('name')}}
                                            <span class="input-label-secondary">*</span>
                                        </label>
                                        <input type="text" name="name" class="form-control"
                                               value="{{ $product['name'] }}"
                                               placeholder="{{\App\CPU\translate('product_name')}}" required>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label class="input-label"
                                               for="exampleFormControlInput1">{{\App\CPU\translate('product_code_SKU')}}
                                            <span class="input-label-secondary">*</span>
                                            <a class="style-one-pu"
                                               onclick="document.getElementById('generate_number').value = getRndInteger()">{{\App\CPU\translate('generate_code')}}</a></label>
                                        <input type="text" id="generate_number" minlength="5" name="product_code"
                                               class="form-control" value="{{ $product['product_code'] }}"
                                               placeholder="{{\App\CPU\translate('product_code')}}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row pl-2">
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label class="input-label"
                                               for="exampleFormControlInput1">{{\App\CPU\translate('brand')}}</label>
                                        <select name="brand_id" class="form-control js-select2-custom">
                                            <option value="">---{{\App\CPU\translate('select')}}---</option>
                                            @foreach ($brands as $brand)
                                                <option
                                                    value="{{ $brand['id'] }}" {{ $product->brand == $brand['id'] ? 'selected' : ' ' }}>{{$brand['name']}}</option>
                                            @endforeach


                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label class="input-label"
                                               for="exampleFormControlInput1">{{\App\CPU\translate('quantity')}}
                                            <span class="input-label-secondary">*</span>
                                        </label>
                                        <input type="number" min="1" name="quantity" class="form-control"
                                               value="{{ $product['quantity'] }}"
                                               placeholder="{{\App\CPU\translate('quantity')}}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row pl-2">
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label class="input-label"
                                               for="exampleFormControlInput1">{{\App\CPU\translate('unit_type')}}
                                            <span class="input-label-secondary">*</span>
                                        </label>
                                        <select name="unit_type" class="form-control js-select2-custom" required>
                                            <option value="">---{{\App\CPU\translate('select')}}---</option>
                                            @foreach ($units as $unit)
                                                <option
                                                    value="{{$unit['id']}}" {{ $product->unit_type==$unit['id']?'selected':'' }}>{{$unit['unit_type']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label class="input-label"
                                               for="exampleFormControlInput1">{{\App\CPU\translate('unit_value')}}
                                            <span class="input-label-secondary">*</span>
                                        </label>
                                        <input type="number" min="0" name="unit_value" class="form-control"
                                               value="{{ $product['unit_value'] }}"
                                               placeholder="{{\App\CPU\translate('unit_value')}}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row pl-2">
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label class="input-label"
                                               for="exampleFormControlSelect1">{{\App\CPU\translate('category')}}<span
                                                class="input-label-secondary">*</span></label>
                                        <select name="category_id" id="category-id"
                                                class="form-control js-select2-custom"
                                                onchange="getRequest('{{url('/')}}/admin/product/get-categories?parent_id='+this.value,'sub-categories')">
                                            <option value="">---{{\App\CPU\translate('select')}}---</option>
                                            @foreach($categories as $category)
                                                <option
                                                    value="{{$category['id']}}" {{ $category->id==$product_category[0]->id ? 'selected' : ''}}>{{$category['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label class="input-label"
                                               for="exampleFormControlSelect1">{{\App\CPU\translate('sub_category')}}
                                            <span
                                                class="input-label-secondary"></span></label>
                                        <select name="sub_category_id" id="sub-categories"
                                                data-id="{{count($product_category)>=2?$product_category[1]->id:''}}"
                                                class="form-control js-select2-custom"
                                                onchange="getRequest('{{url('/')}}/admin/product/get-categories?parent_id='+this.value,'sub-sub-categories')">

                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row pl-2">
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label class="input-label"
                                               for="exampleFormControlInput1">{{\App\CPU\translate('selling_price')}}
                                            <span class="input-label-secondary">*</span>
                                        </label>
                                        <input type="number" step="0.01" name="selling_price" class="form-control"
                                               value="{{ $product['selling_price'] }}"
                                               placeholder="{{\App\CPU\translate('selling_price')}}" required>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label class="input-label"
                                               for="exampleFormControlInput1">{{\App\CPU\translate('purchase_price')}}
                                            <span class="input-label-secondary">*</span>
                                        </label>
                                        <input type="number" step="0.01" name="purchase_price" class="form-control"
                                               value="{{ $product['purchase_price'] }}"
                                               placeholder="{{\App\CPU\translate('purchase_price')}}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row pl-2">
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label class="input-label"
                                               for="exampleFormControlInput1">{{\App\CPU\translate('discount_type')}}</label>
                                        <select onchange="discount_option(this);" name="discount_type"
                                                class="form-control js-select2-custom">
                                            <option
                                                value="percent" {{ $product->discount_type == 'percent'?'selected':'' }} >{{\App\CPU\translate('percent')}}</option>
                                            <option
                                                value="amount" {{ $product->discount_type == 'amount'?'selected':'' }}>{{\App\CPU\translate('amount')}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label id="percent" class="input-label">{{\App\CPU\translate('discount_percent')}}
                                            (%)</label>
                                        <label id="amount"
                                               class="input-label d-none">{{\App\CPU\translate('discount_amount')}}</label>
                                        <input type="number" min="0" name="discount" class="form-control"
                                               value="{{ $product['discount'] }}"
                                               placeholder="{{\App\CPU\translate('discount')}}">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label class="input-label"
                                               for="exampleFormControlInput1">{{\App\CPU\translate('tax_in_percent')}}
                                            (%)</label>
                                        <input type="number" min="0" name="tax" class="form-control"
                                               value="{{ $product['tax'] }}"
                                               placeholder="{{\App\CPU\translate('tax')}}">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label class="input-label"
                                               for="exampleFormControlInput1">{{\App\CPU\translate('select_supplier')}}</label>
                                        <select name="supplier_id" class="form-control js-select2-custom">
                                            <option value="">---{{\App\CPU\translate('select')}}---</option>
                                            @foreach ($suppliers as $supplier)
                                                <option
                                                    value="{{$supplier['id']}}" {{ $product->supplier_id==$supplier['id']?'selected':'' }}>{{$supplier['name']}}
                                                    ({{ $supplier['mobile'] }})
                                                </option>
                                            @endforeach


                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row pl-2">

                                <div class="col-12">
                                    <label>{{\App\CPU\translate('image')}}</label>
                                    <div class="custom-file">
                                        <input type="file" name="image" id="customFileEg1" class="custom-file-input"
                                               accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                        <label class="custom-file-label"
                                               for="customFileEg1">{{\App\CPU\translate('choose_file')}}</label>
                                    </div>
                                    <div class="form-group">
                                        <hr>
                                        <center>
                                            <img class="img-one-pu" id="viewer"
                                                 onerror="this.src='{{asset('assets/admin/img/400x400/img2.jpg')}}'"
                                                 src="{{asset('storage/app/public/product')}}/{{$product['image']}}"
                                                 alt="{{ \App\CPU\translate('image')}}"/>
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
    <script>
        "use strict";
        $(document).ready(function () {
            setTimeout(function () {
                let category = $("#category-id").val();
                let sub_category = '{{count($product_category)>=2?$product_category[1]->id:''}}';
                //let sub_sub_category = '{{count($product_category)>=3?$product_category[2]->id:''}}';
                getRequest('{{url('/')}}/admin/product/get-categories?parent_id=' + category + '&&sub_category=' + sub_category, 'sub-categories');
                //('{{url('/')}}/admin/product/get-categories?parent_id=' + sub_category + '&&sub_category=' + sub_sub_category, 'sub-sub-categories');
            }, 1000)
        });
    </script>

    <script src={{asset("public/assets/admin/js/global.js")}}></script>
@endpush

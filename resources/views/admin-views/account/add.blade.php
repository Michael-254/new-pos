@extends('layouts.admin.app')

@section('title',\App\CPU\translate('add_new_account'))

@push('css_or_js')

@endpush

@section('content')
<div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title text-capitalize"><i
                            class="tio-add-circle-outlined"></i> {{\App\CPU\translate('add_new_account')}}
                    </h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <div class="card">
                    <div class="card-body">
                        <form action="{{route('admin.account.store')}}" method="post" >
                            @csrf
                            <div class="row pl-2" >
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label class="input-label" >{{\App\CPU\translate('account_title')}}</label>
                                        <input type="text" name="account" class="form-control" value="{{ old('account') }}"  placeholder="{{\App\CPU\translate('account_title')}}" required>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label class="input-label">{{\App\CPU\translate('description')}} </label>
                                        <input type="text" name="description" class="form-control" value="{{ old('description') }}"  placeholder="{{\App\CPU\translate('description')}}" >
                                    </div>
                                </div>
                            </div>
                            <div class="row pl-2" >
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label class="input-label" >{{\App\CPU\translate('balance')}}</label>
                                        <input type="number" step="0.01" min="0" name="balance" class="form-control" value="{{ old('balance') }}"  placeholder="{{\App\CPU\translate('initial_balance')}}" required>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label class="input-label" >{{\App\CPU\translate('account_number')}}</label>
                                        <input type="text" name="account_number" class="form-control" value="{{ old('account_number') }}"  placeholder="{{\App\CPU\translate('account_number')}}" required>
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


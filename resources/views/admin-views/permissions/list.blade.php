@extends('layouts.admin.app')

@section('title',\App\CPU\translate('permission_list'))

@push('css_or_js')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="{{asset('assets/admin')}}/css/custom.css" />
@endpush

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title text-capitalize"><i class="tio-filter-list"></i> {{\App\CPU\translate('permission_list')}}
                    <span class="badge badge-soft-dark ml-2">{{$permissions->total()}}</span>
                </h1>
            </div>
        </div>
    </div>
    <!-- End Page Header -->
    <div class="row gx-2 gx-lg-3">
        <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
            <!-- Card -->
            <div class="card">
                <!-- Header -->
                <div class="card-header">
                    <div class="row justify-content-between align-items-center flex-grow-1">
                        <div class="col-12 col-sm-7 col-md-6 col-lg-4 col-xl-6 mb-3 mb-sm-0">
                            <form action="{{url()->current()}}" method="GET">
                                <!-- Search -->
                                <div class="input-group input-group-merge input-group-flush">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="tio-search"></i>
                                        </div>
                                    </div>
                                    <input id="datatableSearch_" type="search" name="search" class="form-control" placeholder="{{\App\CPU\translate('search_by_name_or_phone')}}" aria-label="Search" value="{{ $search }}" required>
                                    <button type="submit" class="btn btn-primary">{{\App\CPU\translate('search')}} </button>

                                </div>
                                <!-- End Search -->
                            </form>
                        </div>
                        <div class="col-12 col-sm-5">
                            <a href="{{route('admin.permission.add')}}" class="btn btn-primary float-right"><i class="tio-add-circle"></i> {{\App\CPU\translate('add_new_permission')}}
                            </a>
                        </div>
                    </div>
                </div>
                <!-- End Header -->

                <!-- Table -->
                <div class="table-responsive datatable-custom">
                    <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                        <thead class="thead-light">
                            <tr>
                                <th>{{\App\CPU\translate('#')}}</th>
                                <th>{{ \App\CPU\translate('image') }}</th>
                                <th>{{\App\CPU\translate('name')}}</th>
                                <th>{{\App\CPU\translate('phone')}}</th>
                                <th>{{ \App\CPU\translate('orders') }}</th>
                                <th class="text-center">{{ \App\CPU\translate('balance') }}</th>
                                <th>{{\App\CPU\translate('action')}}</th>
                            </tr>
                        </thead>

                        <tbody id="set-rows">
                            @foreach($permissions as $key=>$permission)
                            <tr>
                                <td>{{ $permissions->firstItem()+$key+1 }}</td>
                                <td>
                                    <a href="{{route('admin.permission.view',[$permission['id']])}}">
                                        <img class="img-one-cl" onerror="this.src='{{asset('assets/admin/img/160x160/img1.jpg')}}'" src="{{asset('storage/app/public/permission')}}/{{ $permission->image }}" alt="">
                                    </a>
                                </td>
                                <td>
                                    <a class="text-primary" href="{{route('admin.permission.view',[$permission['id']])}}">
                                        {{ $permission->name }}
                                    </a>
                                </td>
                                <td>
                                    @if ($permission->id != 1)
                                    {{ $permission->mobile }}
                                    @else
                                    {{\App\CPU\translate('no_phone')}}
                                    @endif
                                </td>
                                <td>{{ $permission->orders->count() }}</td>
                                <td class="text-center p-5">
                                    @if ($permission->id != 1)
                                    <div class="row">
                                        <div class="col-5">
                                            {{ $permission->balance. ' ' . \App\CPU\Helpers::currency_symbol() }}
                                        </div>
                                        <div class="col-5">

                                            <a class=" btn btn-info p-1 badge" id="{{ $permission->id }}" onclick="update_permission_balance_cl({{ $permission->id }})" type="button" data-toggle="modal" data-target="#update-permission-balance">
                                                <i class="tio-add-circle"></i>
                                                {{\App\CPU\translate('add_balance')}}</a>

                                        </div>
                                    </div>
                                    @else
                                    <div class="row">
                                        <div class="col-6">
                                            {{\App\CPU\translate('no_balance')}}
                                        </div>
                                    </div>

                                    @endif

                                </td>
                                <td>
                                    @if ($permission->name != "walking permission")
                                    <a class="btn btn-white mr-1" href="{{route('admin.permission.view',[$permission['id']])}}"><span class="tio-visible"></span></a>
                                    <a class="btn btn-white mr-1" href="{{route('admin.permission.edit',[$permission['id']])}}">
                                        <span class="tio-edit"></span>
                                    </a>
                                    <a class="btn btn-white mr-1" href="javascript:" onclick="form_alert('permission-{{$permission['id']}}','Want to delete this permission?')"><span class="tio-delete"></span></a>
                                    <form action="{{route('admin.permission.delete',[$permission['id']])}}" method="post" id="permission-{{$permission['id']}}">
                                        @csrf @method('delete')
                                    </form>
                                    @else
                                    <a class="btn btn-white mr-1" href="{{route('admin.permission.view',[$permission['id']])}}"><span class="tio-visible"></span></a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="page-area">
                        <table>
                            <tfoot class="border-top">
                                {!! $permissions->links() !!}
                            </tfoot>
                        </table>
                    </div>
                    @if(count($permissions)==0)
                    <div class="text-center p-4">
                        <img class="mb-3 w-one-cl" src="{{asset('assets/admin')}}/svg/illustrations/sorry.svg" alt="{{\App\CPU\translate('Image Description')}}">
                        <p class="mb-0">{{ \App\CPU\translate('No_data_to_show')}}</p>
                    </div>
                    @endif
                </div>
                <!-- End Table -->
            </div>
            <!-- End Card -->
        </div>
    </div>
</div>
@endsection

@push('script_2')
<script src={{asset("public/assets/admin/js/global.js")}}></script>
@endpush

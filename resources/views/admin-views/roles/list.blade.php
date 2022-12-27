@extends('layouts.admin.app')

@section('title',\App\CPU\translate('role_list'))

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
                <h1 class="page-header-title text-capitalize"><i class="tio-filter-list"></i> {{\App\CPU\translate('role_list')}}
                    <span class="badge badge-soft-dark ml-2">{{$roles->total()}}</span>
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
                            <a href="{{route('admin.role.add')}}" class="btn btn-primary float-right"><i class="tio-add-circle"></i> {{\App\CPU\translate('add_new_role')}}
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
                            @foreach($roles as $key=>$role)
                            <tr>
                                <td>{{ $roles->firstItem()+$key+1 }}</td>
                                <td>
                                    <a href="{{route('admin.role.view',[$role['id']])}}">
                                        <img class="img-one-cl" onerror="this.src='{{asset('assets/admin/img/160x160/img1.jpg')}}'" src="{{asset('storage/app/public/role')}}/{{ $role->image }}" alt="">
                                    </a>
                                </td>
                                <td>
                                    <a class="text-primary" href="{{route('admin.role.view',[$role['id']])}}">
                                        {{ $role->name }}
                                    </a>
                                </td>
                                <td>
                                    @if ($role->id != 1)
                                    {{ $role->mobile }}
                                    @else
                                    {{\App\CPU\translate('no_phone')}}
                                    @endif
                                </td>
                                <td>{{ $role->orders->count() }}</td>
                                <td class="text-center p-5">
                                    @if ($role->id != 1)
                                    <div class="row">
                                        <div class="col-5">
                                            {{ $role->balance. ' ' . \App\CPU\Helpers::currency_symbol() }}
                                        </div>
                                        <div class="col-5">

                                            <a class=" btn btn-info p-1 badge" id="{{ $role->id }}" onclick="update_role_balance_cl({{ $role->id }})" type="button" data-toggle="modal" data-target="#update-role-balance">
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
                                    @if ($role->name != "walking role")
                                    <a class="btn btn-white mr-1" href="{{route('admin.role.view',[$role['id']])}}"><span class="tio-visible"></span></a>
                                    <a class="btn btn-white mr-1" href="{{route('admin.role.edit',[$role['id']])}}">
                                        <span class="tio-edit"></span>
                                    </a>
                                    <a class="btn btn-white mr-1" href="javascript:" onclick="form_alert('role-{{$role['id']}}','Want to delete this role?')"><span class="tio-delete"></span></a>
                                    <form action="{{route('admin.role.delete',[$role['id']])}}" method="post" id="role-{{$role['id']}}">
                                        @csrf @method('delete')
                                    </form>
                                    @else
                                    <a class="btn btn-white mr-1" href="{{route('admin.role.view',[$role['id']])}}"><span class="tio-visible"></span></a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="page-area">
                        <table>
                            <tfoot class="border-top">
                                {!! $roles->links() !!}
                            </tfoot>
                        </table>
                    </div>
                    @if(count($roles)==0)
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

@extends('layouts.admin.app')

@section('title',\App\CPU\translate('user_list'))

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
                <h1 class="page-header-title text-capitalize"><i class="tio-filter-list"></i> {{\App\CPU\translate('user_list')}}
                    <span class="badge badge-soft-dark ml-2">{{$users->total()}}</span>
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
                            <a href="{{route('admin.user.add')}}" class="btn btn-primary float-right"><i class="tio-add-circle"></i> {{\App\CPU\translate('add_new_user')}}
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
                                <th>{{\App\CPU\translate('action')}}</th>
                            </tr>
                        </thead>

                        <tbody id="set-rows">
                            @foreach($users as $key=>$user)
                            <tr>
                                <td>{{ $users->firstItem()+$key+1 }}</td>
                                <td>
                                    <a href="{{route('admin.user.view',[$user['id']])}}">
                                        <img class="img-one-cl" onerror="this.src='{{asset('assets/admin/img/160x160/img1.jpg')}}'" src="{{asset('storage/app/public/user')}}/{{ $user->image }}" alt="">
                                    </a>
                                </td>
                                <td>
                                    <a class="text-primary" href="{{route('admin.user.view',[$user['id']])}}">
                                        {{ $user->name }}
                                    </a>
                                </td>
                                <td>
                                    @if ($user->id != 1)
                                    {{ $user->mobile }}
                                    @else
                                    {{\App\CPU\translate('no_phone')}}
                                    @endif
                                </td>
                                <td>
                                    @if ($user->name != "walking user")
                                    <a class="btn btn-white mr-1" href="{{route('admin.user.view',[$user['id']])}}"><span class="tio-visible"></span></a>
                                    <a class="btn btn-white mr-1" href="{{route('admin.user.edit',[$user['id']])}}">
                                        <span class="tio-edit"></span>
                                    </a>
                                    <a class="btn btn-white mr-1" href="javascript:" onclick="form_alert('user-{{$user['id']}}','Want to delete this user?')"><span class="tio-delete"></span></a>
                                    <form action="{{route('admin.user.delete',[$user['id']])}}" method="post" id="user-{{$user['id']}}">
                                        @csrf @method('delete')
                                    </form>
                                    @else
                                    <a class="btn btn-white mr-1" href="{{route('admin.user.view',[$user['id']])}}"><span class="tio-visible"></span></a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="page-area">
                        <table>
                            <tfoot class="border-top">
                                {!! $users->links() !!}
                            </tfoot>
                        </table>
                    </div>
                    @if(count($users)==0)
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

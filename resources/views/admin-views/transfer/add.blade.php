@extends('layouts.admin.app')

@section('title', \App\CPU\translate('add_new_transfer'))

@push('css_or_js')
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title text-capitalize"><i class="tio-add-circle-outlined"></i>
                        {{ \App\CPU\translate('add_new_transfer') }}
                    </h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.account.store-transfer') }}" method="post">
                            @csrf
                            <div class="row pl-2">
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label class="input-label"
                                            for="exampleFormControlInput1">{{ \App\CPU\translate('account_from') }}</label>
                                        <select onchange="accountChangeTr(this.value);" name="account_from_id"
                                            class="form-control">
                                            <option value="">---{{ \App\CPU\translate('select') }}---</option>
                                            @foreach ($accounts as $account)
                                                @if ($account['id'] != 2 && $account['id'] != 3)
                                                    <option value="{{ $account['id'] }}">{{ $account['account'] }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label class="input-label"
                                            for="exampleFormControlInput1">{{ \App\CPU\translate('account_to') }} </label>
                                        <select id="account_to_id" name="account_to_id" class="form-control">
                                            <option value="">---{{ \App\CPU\translate('select') }}---</option>
                                            @foreach ($accounts as $account)
                                                @if ($account['id'] != 2 && $account['id'] != 3)
                                                    <option value="{{ $account['id'] }}" class="account"
                                                        style="display: none;">{{ $account['account'] }} </option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                            </div>
                            <div class="row pl-2">
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label class="input-label">{{ \App\CPU\translate('description') }} </label>
                                        <input type="text" name="description" class="form-control"
                                            placeholder="{{ \App\CPU\translate('description') }}">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label class="input-label">{{ \App\CPU\translate('amount') }}</label>
                                        <input type="number" step="0.01" min="1" name="amount"
                                            class="form-control" placeholder="{{ \App\CPU\translate('amount') }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row pl-2">
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label class="input-label"
                                            for="exampleFormControlInput1">{{ \App\CPU\translate('date') }} </label>
                                        <input type="date" name="date" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <button type="submit" class="btn btn-primary">{{ \App\CPU\translate('submit') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title text-capitalize"><i class="tio-files"></i>
                        {{ \App\CPU\translate('transfer_list') }}
                        <span class="badge badge-soft-dark ml-2">{{ $transfers->total() }}</span>
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
                            <div class="col-12 col-md-6 col-lg-5 mb-3 mb-lg-0">
                                <form action="{{ url()->current() }}" method="GET">
                                    <!-- Search -->
                                    <div class="input-group input-group-merge input-group-flush">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="tio-search"></i>
                                            </div>
                                        </div>
                                        <input id="datatableSearch_" type="search" name="search" class="form-control"
                                            placeholder="{{ \App\CPU\translate('search_by_description') }}"
                                            value="{{ $search }}" required>
                                        <button type="submit" class="btn btn-primary">{{ \App\CPU\translate('search') }}
                                        </button>

                                    </div>
                                    <!-- End Search -->
                                </form>
                            </div>
                            <div class="col-12 col-lg-7">
                                <form action="{{ url()->current() }}" method="GET">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label class="input-label"
                                                    for="exampleFormControlInput1">{{ \App\CPU\translate('from') }}
                                                </label>
                                                <input type="date" name="from" class="form-control"
                                                    value="{{ $from }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label class="input-label"
                                                    for="exampleFormControlInput1">{{ \App\CPU\translate('to') }} </label>
                                                <input type="date" name="to" class="form-control"
                                                    value="{{ $to }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <button href="" class="btn btn-success mt-4">
                                                {{ \App\CPU\translate('filter') }}</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- End Header -->

                    <!-- Table -->
                    <div class="table-responsive datatable-custom">
                        <table
                            class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                            <thead class="thead-light">
                                <tr>
                                    <th>{{ \App\CPU\translate('date') }}</th>
                                    <th>{{ \App\CPU\translate('account') }}</th>
                                    <th>{{ \App\CPU\translate('type') }}</th>
                                    <th>{{ \App\CPU\translate('amount') }}</th>
                                    <th>{{ \App\CPU\translate('description') }}</th>
                                    <th>{{ \App\CPU\translate('debit') }}</th>
                                    <th>{{ \App\CPU\translate('credit') }}</th>
                                    <th>{{ \App\CPU\translate('balance') }}</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($transfers as $key => $transfer)
                                    <tr>

                                        <td>{{ $transfer->date }}</td>
                                        <td>
                                            {{ $transfer->account ? $transfer->account->account : ' ' }} <br>
                                        </td>
                                        <td>
                                            <span class="badge badge-warning ml-sm-3">
                                                {{ $transfer->tran_type }} <br>
                                            </span>
                                        </td>
                                        <td>
                                            {{ $transfer->amount . ' ' . \App\CPU\Helpers::currency_symbol() }}
                                        </td>
                                        <td>
                                            {{ Str::limit($transfer->description, 30) }}
                                        </td>
                                        <td>
                                            @if ($transfer->debit)
                                                {{ $transfer->amount . ' ' . \App\CPU\Helpers::currency_symbol() }}
                                            @else
                                                {{ 0 . ' ' . \App\CPU\Helpers::currency_symbol() }}
                                            @endif

                                        </td>
                                        <td>
                                            @if ($transfer->credit)
                                                {{ $transfer->amount . ' ' . \App\CPU\Helpers::currency_symbol() }}
                                            @else
                                                {{ 0 . ' ' . \App\CPU\Helpers::currency_symbol() }}
                                            @endif

                                        </td>

                                        <td>
                                            {{ $transfer->balance . ' ' . \App\CPU\Helpers::currency_symbol() }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="page-area">
                            <table>
                                <tfoot class="border-top">
                                    {!! $transfers->links() !!}
                                </tfoot>
                            </table>
                        </div>
                        @if (count($transfers) == 0)
                            <div class="text-center p-4">
                                <img class="mb-3 img-one-tadd"
                                    src="{{ asset('public/assets/admin') }}/svg/illustrations/sorry.svg"
                                    alt="{{ \App\CPU\translate('image_description') }}">
                                <p class="mb-0">{{ \App\CPU\translate('No_data_to_show') }}</p>
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
    <script src={{ asset('public/assets/admin/js/global.js') }}></script>
@endpush

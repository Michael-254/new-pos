@extends('layouts.admin.app')

@section('title',\App\CPU\translate('dashboard'))

@section('content')
<div class="content container-fluid">
    <div class="card mb-3 bg-white">
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-12">
                    <label class="badge badge-soft-success float-right mb-2" style="z-index: 9;right: 0.5rem;margin-top: -14px">
                        {{\App\CPU\translate('Software version')}}: {{ env('SOFTWARE_VERSION') }}
                    </label>
                </div>
                <div class="col-md-9">
                    <h4><i class="font-one-dash tio-chart-bar-4"></i>{{$customer->name.' '.\App\CPU\translate('dashboard')}}</h4>
                </div>
            </div>
            <div class="row gx-2 gx-lg-3" id="account_stats">
                @include('admin-views.partials._customer-balance-stats',['customer'=>$customer])
            </div>
        </div>
    </div>

    <div class="row gx-2 gx-lg-3 mb-3 mb-lg-5">
        <div class="col-md-12">
            <div class="card ">
                <div class="card-header">
                    <h3>{{\App\CPU\translate('your_purchases')}}
                        <span class="badge badge-soft-dark ml-2">{{$orders->count()}}</span>
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Table -->
                    <div class="table-responsive datatable-custom">
                        <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                            <thead class="thead-light">
                            <tr>
                                <th>{{ \App\CPU\translate('#') }}</th>
                                <th>{{ \App\CPU\translate('date') }}</th>
                                <th>{{\App\CPU\translate('shop_name')}}</th>
                                <th>{{\App\CPU\translate('product')}}</th>
                                <th>{{\App\CPU\translate('amount_spent')}}</th>
                                <th>{{\App\CPU\translate('loyalty_points_earned')}}</th>
                            </tr>
                            </thead>

                            <tbody>
                                @foreach ($orders as $order)
                                  
                                    <tr>
                                        @php
                                            $money_spent = ($order->tax_amount + $order->price - $order->discount_on_product) * $order->quantity
                                        @endphp
                                        <td>{{ $loop->index + 1 }}</td>
                                        <td>{{ $order->created_at }}</td>
                                        <td>DUKAPAQ</td>
                                        <td>{{ $order->product->name  }}</td>
                                        <td>{{ $money_spent }}</td>
                                        <td>{{ round($money_spent / 10) }}</td>
                                        
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="page-area float-right mt-3">
                            <table>
                                <tfoot class="border-top">
                                     {{ $orders->links() }}
                                </tfoot>
                            </table>
                        </div>
                        @if(count($orders)==0)
                            <div class="text-center p-4">
                                <img class="mb-3 img-one-dash" src="{{asset('assets/admin')}}/svg/illustrations/sorry.svg" alt="Image Description">
                                <p class="mb-0">{{ \App\CPU\translate('No_data_to_show')}}</p>
                            </div>
                        @endif
                    </div>
                    <!-- End Table -->
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('script')
    <script src="{{asset('assets/admin')}}/vendor/chart.js/dist/Chart.min.js"></script>
    <script src="{{asset('assets/admin')}}/vendor/chart.js.extensions/chartjs-extensions.js"></script>
    <script src="{{asset('assets/admin')}}/vendor/chartjs-plugin-datalabels/dist/chartjs-plugin-datalabels.min.js"></script>
@endpush

@push('script_2')
    <script>
        "use strict";
        function account_stats_update(type) {
            //console.log(type)
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('admin.account-status')}}',
                data: {
                    statistics_type: type
                },
                beforeSend: function () {
                    $('#loading').show()
                },
                success: function (data) {
                    $('#account_stats').html(data.view)
                },
                complete: function () {
                    $('#loading').hide()
                }
            });
        }
    </script>

    <script src={{asset("public/assets/admin/js/global.js")}}></script>
@endpush

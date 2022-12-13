@extends('layouts.admin.app')
@section('title','Order List')
@push('css_or_js')
{{-- <meta name="csrf-token" content="{{ csrf_token() }}"> --}}
<link rel="stylesheet" href="{{asset('assets/admin')}}/css/custom.css" />
@endpush

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center mb-2">
            <div class="col-sm">
                <h1 class="page-header-title text-capitalize">{{\App\CPU\translate('pos')}} {{\App\CPU\translate('order')}}
                </h1>
            </div>
        </div>
    </div>
    <!-- End Page Header -->
    <!-- Card -->
    <div class="card">
        <!-- Header -->
        <div class="card-header">
            <div class="row justify-content-between align-items-center flex-grow-1">

                <div class="width-inone">
                    <hr class="line-dot">

                    <div class="row mt-3">
                        <div class="col-6">
                            <h5>{{ \App\CPU\translate('order_ID') }} : {{ $order['id'] }}</h5>
                        </div>
                        <div class="col-6">
                            <h5 class="font-inone">
                                {{ date('d/M/Y h:i a', strtotime($order['created_at'])) }}
                            </h5>
                        </div>
                    </div>
                    <h5 class="text-uppercase"></h5>
                    <hr class="line-dot">
                    <form action="{{route('admin.pos.order-return')}}" method="POST">
                        @csrf
                        <table class="table table-bordered mt-3">
                            <thead>
                                <tr>
                                    <th>{{ \App\CPU\translate('SL') }}</th>
                                    <th>{{ \App\CPU\translate('DESC') }}</th>
                                    <th>{{ \App\CPU\translate('QTY') }}</th>
                                    <th>{{ \App\CPU\translate('Price') }}</th>
                                    <th>QTY to return to stock</th>
                                </tr>
                            </thead>

                            <tbody>
                                @php($sub_total = 0)
                                @php($total_tax = 0)
                                @php($total_dis_on_pro = 0)
                                @foreach ($order->details as $key => $detail)
                                @if ($detail->product)
                                <tr>
                                    <td>
                                        {{ $key + 1 }}
                                    </td>
                                    <td>
                                        <span class="style-inthree">{{ $detail->product['name'] }}</span><br />
                                        {{ \App\CPU\translate('price') }} :
                                        {{ $detail['price'] . ' ' . \App\CPU\Helpers::currency_symbol() }} <br>
                                        {{ \App\CPU\translate('discount') }} :
                                        {{ $detail['discount_on_product'] * $detail['quantity'] . ' ' . \App\CPU\Helpers::currency_symbol() }}
                                    </td>
                                    <td class="">
                                        {{ $detail['quantity'] }}
                                    </td>
                                    <td>
                                        @php($amount = ($detail['price'] - $detail['discount_on_product']) * $detail['quantity'])
                                        {{ $amount . ' ' . \App\CPU\Helpers::currency_symbol() }}
                                    </td>
                                    <td>
                                        <input name="return_quantity[]" type="number" class="style-two-cart qty-width" value="0" max="{{ $detail->quantity }}">
                                        <input type="hidden" name="detail_id[]" value="{{ $detail->id }}">
                                    </td>
                                </tr>
                                @php($sub_total += $amount)
                                @php($total_tax += $detail['tax_amount'] * $detail['quantity'])
                                @endif
                                @endforeach
                            </tbody>
                        </table>
                        <div class="row justify-content-md-end px-3">
                            <button type="submit" class="btn  btn-primary btn-sm btn-block">Return</button>
                        </div>
                    </form>
                    <hr class="line-dot">
                    <div class="row justify-content-md-end">
                        <div class="col-md-7 col-lg-7">
                            <dl class="row text-right text-black-50">
                                <dt class="col-7">{{ \App\CPU\translate('items_price') }}:</dt>
                                <dd class="col-5">{{ $sub_total . ' ' . \App\CPU\Helpers::currency_symbol() }}</dd>
                                <dt class="col-7">{{ \App\CPU\translate('Tax_/_VAT') }}:</dt>
                                <dd class="col-5">{{ $total_tax . ' ' . \App\CPU\Helpers::currency_symbol() }}
                                    <hr>
                                </dd>
                                <dt class="col-7">{{ \App\CPU\translate('subtotal') }}:</dt>
                                <dd class="col-5">{{ $sub_total + $total_tax . ' ' . \App\CPU\Helpers::currency_symbol() }}</dd>
                                <dt class="col-7">{{ \App\CPU\translate('extra_discount') }}:</dt>
                                <dd class="col-5">
                                    {{ $order['extra_discount'] ? number_format($order['extra_discount'], 2) . ' ' . \App\CPU\Helpers::currency_symbol() : 0 . ' ' . \App\CPU\Helpers::currency_symbol() }}
                                    <hr>
                                </dd>
                                <dt class="col-7">{{ \App\CPU\translate('coupon_discount') }}:</dt>
                                <dd class="col-5">
                                    {{ $order['coupon_discount_amount'] . ' ' . \App\CPU\Helpers::currency_symbol() }}
                                </dd>
                                <dt class="col-7 font-inthree">{{ \App\CPU\translate('total') }}:</dt>
                                <dd class="col-5 font-inthree">

                                    {{ $sub_total + $total_tax  - ($order['coupon_discount_amount'] + $order['extra_discount']) }} {{ \App\CPU\Helpers::currency_symbol()  }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                    <div class="d-flex flex-row justify-content-between border-top">
                        <span>{{ \App\CPU\translate('Paid_by') }}:
                            {{ $order->account ? $order->account->account : \App\CPU\translate('customer_balance') }}</span>
                        @if ($order->payment_id == 1)
                        <span>{{ \App\CPU\translate('amount') }}:
                            {{ $order->collected_cash ? $order->collected_cash . ' ' . \App\CPU\Helpers::currency_symbol() : 0 . ' ' . \App\CPU\Helpers::currency_symbol() }}</span>

                        <span>{{ \App\CPU\translate('change') }}:{{ number_format($order->collected_cash - $order->order_amount - $order->total_tax + $order->extra_discount + $order->coupon_discount_amount, 2) }}
                            {{ \App\CPU\Helpers::currency_symbol() }}</span>
                        @endif
                    </div>
                    <hr class="line-dot">
                </div>

                <div class="col-lg-6"></div>
            </div>
            <!-- End Row -->
        </div>
        <!-- End Header -->

    </div>
    <!-- End Card -->
</div>


@endsection

@push('script_2')
<script src={{asset("public/assets/admin/js/global.js")}}></script>
@endpush
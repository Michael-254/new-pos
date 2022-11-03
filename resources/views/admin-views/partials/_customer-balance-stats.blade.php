<div class="col-sm-12 col-lg-4 mb-3 mt-3 mb-lg-5"><!-- Card -->
    <a class="card card-hover-shadow h-100 color-five" href="#">
        <div class="card-body">
            <div class="flex-between align-items-center mb-1">
                <div>
                    <h6 class="card-subtitle text-white">{{\App\CPU\translate('loyalty_points')}}</h6>
                    <span class="card-title h2 text-white">
                        {{ $customer->loyalty_points }} 
                    </span>
                </div>
                <div class="mt-2">
                    <i class="tio-money-vs text-white font-one"></i>
                </div>
            </div>
            <!-- End Row -->
        </div>
    </a>
    <!-- End Card -->
</div>
<div class="col-sm-6 col-lg-4 mb-3 mt-3 mb-lg-5">
<!-- Card -->
<a class="card card-hover-shadow h-100 color-eight" href="#">
    <div class="card-body">
        <div class="flex-between align-items-center mb-1">
            <div>
                <h6 class="card-subtitle text-white">{{\App\CPU\translate('expiry_date')}}</h6>
                <span class="card-title h2 text-white">
                    {{ $customer->loyalty_expire_date}}
                </span>
            </div>
            <div class="mt-2">
                <i class="tio-money-vs text-white font-one"></i>
            </div>
        </div>
        <!-- End Row -->
    </div>
</a>
<!-- End Card -->
</div>
<div class="col-sm-6 col-lg-4 mb-3 mt-3 mb-lg-5">
<!-- Card -->
<a class="card card-hover-shadow h-100 color-three" href="#">
    <div class="card-body">
        <div class="flex-between align-items-center mb-1">
            <div>
                <h6 class="card-subtitle text-white">{{\App\CPU\translate('recently_earned')}}</h6>
                <span class="card-title h2 text-white">
                    @foreach ($customer->orders as $order)
                      @if ($loop->last)
                          {{ $order->collected_cash / 10 }}
                      @endif
                    @endforeach
                </span>
            </div>
            <div class="mt-2">
                <i class="tio-money-vs font-one text-white"></i>
            </div>
        </div>
        <!-- End Row -->
    </div>
</a>
<!-- End Card -->
</div>

<div class="col-sm-6 col-lg-6 mb-3 mt-3 mb-lg-5">
<!-- Card -->
<a class="card card-hover-shadow h-100 color-two" href="#">
    <div class="card-body">
        <div class="flex-between align-items-center mb-1">
            <div>
                <h6 class="card-subtitle text-white">{{\App\CPU\translate('recently_spent')}}</h6>
                <span class="card-title h2 text-white">
                    0
                </span>
            </div>
            <div class="mt-2">
                <i class="tio-money-vs text-white font-one"></i>
            </div>
        </div>
        <!-- End Row -->
    </div>
</a>
<!-- End Card -->
</div>
<div class="col-sm-6 col-lg-6 mb-3 mt-3 mb-lg-5">
<!-- Card -->
<a class="card card-hover-shadow h-100 color-seven" href="#">
    <div class="card-body">
        <div class="flex-between align-items-center mb-1">
            <div>
                <h6 class="card-subtitle text-white">{{\App\CPU\translate('equivalent_cash')}}</h6>
                <span class="card-title h2 text-white">
                    {{ $customer->loyalty_points * 0.01 }}
                </span>
            </div>
            <div class="mt-2">
                <i class="tio-money-vs text-white font-one"></i>
            </div>
        </div>
        <!-- End Row -->
    </div>
</a>
<!-- End Card -->
</div>

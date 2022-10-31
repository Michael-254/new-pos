<form id="{{ $product->id }}" class="mb-2">
    @csrf
    <input type="hidden" id="product_id" name="id" value="{{ $product->id }}">
    <input type="hidden" id="product_qty" name="quantity" value=1>
    <a onclick="addToCart({{ $product->id }})" class="c-one-sp">
        <div class="row style-one-sp">
            <div class="col-2 p-3">
                <img src="{{asset('storage/product')}}/{{$product['image']}}"
                 onerror="this.src='{{asset('assets/admin/img/160x160/img2.jpg')}}'"
                class="style-two-sp">
            </div>
            <div class="col-8 m-2">
                <div class="w-one-sp">
                    <span>{{ $product['name'] }}</span>
                </div>
                <div class="w-one-sp">
                    <span>{{\App\CPU\translate('code')}}: {{ $product['product_code'] }}</span>
                </div>
                <div class="w-one-sp">
                    {{ ($product['selling_price']- \App\CPU\Helpers::discount_calculate($product, $product['selling_price'])) . ' ' . \App\CPU\Helpers::currency_symbol() }}

                    @if($product->discount > 0)
                        <strike class="style-three-sp">
                            {{ $product['selling_price'] . ' ' . \App\CPU\Helpers::currency_symbol() }}
                        </strike><br>
                    @endif
                </div>
            </div>
        </div>
    </a>
</form>




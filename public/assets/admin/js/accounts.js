"use strict";
function balance_transfer(val){
    let payableBalance = $('#available_balance-'+val).val();
    $('#transaction_id').val(val);
    $('#payment_balance').val(payableBalance).attr('max',payableBalance);
}

"use strict";
function balance_transfer_rec(val){
    let payableBalance = $('#available_balance-'+val).val();
    $('#transaction_id').val(val);
    $('#payment_balance').val(payableBalance).attr('max',payableBalance);
}

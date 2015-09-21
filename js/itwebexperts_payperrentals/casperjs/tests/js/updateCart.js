
casper.updateCart = function(test, qty, pos, expectedValue){

    casper.thenOpen(checkoutUrl,function() {
        test.info('Update cart:  ' + ' Qty:' + qty+  ' Position in cart:' + pos);
        this.evaluate(function(pos, qty){
            $jppr = jQuery.noConflict();
            $jppr('.page-title h1').html('');
            $jppr('#shopping-cart-table tbody tr:eq('+pos+') .qty').val(qty);
            $jppr('.btn-update').click();
            return true;
        }, pos, qty);

        this.waitFor(function check() {
            return this.evaluate(function () {
                $jppr = jQuery.noConflict();
                var pageTitleNow = $jppr('.page-title h1');
                if(pageTitleNow.length > 0) {
                    return pageTitleNow.html().toLowerCase() == 'shopping cart';
                }else{
                    return false;
                }
            });
        }, function then() {

            var errorMsg = this.evaluate(function () {
                $jppr = jQuery.noConflict();
                return $jppr('.error-msg').text();

            });
            if(errorMsg == null){
                errorMsg = "null";
            }
            test.assertEqual(expectedValue, errorMsg, 'Update cart:  ' + ' Qty:' + qty+  ' Position in cart:' + pos);
        });
    });
};


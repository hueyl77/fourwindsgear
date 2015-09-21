
casper.removeFromCart = function(test, pos, expectedValue){

    casper.thenOpen(checkoutUrl,function() {
        test.info('Remove from cart:  ' + ' Position in cart:' + pos);

        this.evaluate(function(pos){
            $jppr = jQuery.noConflict();
            $jppr('.page-title h1').html('');
            window.location.href = $jppr('#shopping-cart-table tbody tr:eq('+pos+') .btn-remove').attr('href');
            return true;
        }, pos);

        this.waitFor(function check() {
            return this.evaluate(function () {
                $jppr = jQuery.noConflict();
                var pageTitleNow = $jppr('.page-title h1');
                if(pageTitleNow.length > 0) {
                    return pageTitleNow.html().toLowerCase() == 'shopping cart' || pageTitleNow.html().toLowerCase() == 'shopping cart is empty';
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
            test.assertEqual(expectedValue, errorMsg, 'Remove from cart:  ' + ' Position in cart:' + pos);
        });
    });
};


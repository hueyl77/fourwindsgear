

casper.checkoutCart = function(test){

    casper.thenOpen(mage.getBaseUrl()+'checkout/onepage/',function() {
        test.info('Checkout ');

        test.info('Continue Billing');
        this.evaluate(function(){
            $jppr = jQuery.noConflict();
            $('shipping:same_as_billing').checked = true;
            $jppr('#checkout-step-billing .button').click();
            return true;
        });
        this.waitFor(function check() {
            return this.evaluate(function () {
                return $jppr('#opc-shipping_method').hasClass('active');
            });
        }, function then() {
            test.info('Continue Shipping Method');
            this.evaluate(function(){
                $jppr('#opc-shipping_method .button').click();
                return true;
            });
            this.waitFor(function check() {
                return this.evaluate(function () {
                    return $jppr('#opc-payment').hasClass('active');
                });
            }, function then() {
                test.info('Continue Payment');
                this.evaluate(function(){
                    $jppr('#p_method_checkmo').click();
                    $jppr('#opc-payment .button').click();
                    return true;
                });
                this.waitFor(function check() {
                    return this.evaluate(function () {
                        return $jppr('#opc-review').hasClass('active');
                    });
                }, function then() {
                    test.info('Continue Review');
                    this.evaluate(function(){
                        $jppr('#opc-review .btn-checkout').click();
                        return true;
                    });
                    //document.URL
                    //this.getCurrentURL
                    this.waitForUrl(/checkout\/onepage\/success/, function(){
                        test.info('Order has been Placed');
                        test.assertEqual(1,1,'Place order:');
                    });

                });
            });
        });


    });
};


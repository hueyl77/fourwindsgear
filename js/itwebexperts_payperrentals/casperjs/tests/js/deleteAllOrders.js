casper.deleteAllOrders = function(test){
    casper.then(function() {
        test.info('Delete all orders completely');
        this.evaluate(function () {
            window.location.href = $jppr('#nav li:eq(1) ul li:eq(0) a').attr('href');//sales order link
            return true;
        });

        this.waitForUrl(/sales_order\/index/, function () {

            var reg = new RegExp(toRegularExp(mage.getBaseUrl() + 'admin/sales_order/index/') + '.*$');
            test.assertUrlMatch(reg);
            this.page.injectJs('js/itwebexperts_payperrentals/jquery/jquery-1.8.3-tests.min.js');
            this.evaluate(function () {
                if($jppr('.massaction-checkbox').length > 0) {
                    sales_order_grid_massactionJsObject.selectVisible();
                    $jppr('.head-sales-order').remove();
                    $jppr('#sales_order_grid_massaction-select').val('payperrentals1');
                    sales_order_grid_massactionJsObject.apply();
                }
                return true;
            });

            this.waitForSelector('.head-sales-order', function () {
                var nrOrders = this.evaluate(function () {
                    return $jppr('.massaction-checkbox').length;
                });

                test.assertEqual(0, nrOrders,'Delete orders: ');
            });
        });
    });
};


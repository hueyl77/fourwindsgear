casper.checkInventoryReport = function(test, product, startDate, expected){
    casper.then(function() {
        var date = toFormattedDate(startDate);
        var dbDate = toDbDate(startDate);
        test.info('Check Inventory Report For: Product'+product+' Date:'+date);
        this.evaluate(function () {
            console.log('Change location:'+$jppr('#nav li.level0:eq(7) ul li.level1:eq(2) ul li.level2:eq(3) a').attr('href'));
            window.location.href = $jppr('#nav li.level0:eq(7) ul li.level1:eq(2) ul li.level2:eq(3) a').attr('href');//inventory report-check the formula with firefox firepath
            return true;
        });

        this.waitForUrl(/adminhtml_quantityreport\/index/, function () {

            var reg = new RegExp(toRegularExp(mage.getBaseUrl() + 'payperrentals_admin/adminhtml_quantityreport/index/') + '.*$');
            test.assertUrlMatch(reg);

            test.info('Search Product');
            this.evaluate(function (product) {
                $jppr('.productName').val(product);
                $jppr('.searchBut').trigger('click');
                $jppr('.content-header').html('');
                return true;
            }, product);
            this.waitFor(function check() {
                return this.evaluate(function () {
                    return $jppr('.content-header').html() != '';
                });
            }, function then() {
               // var monthStart = startDate.getMonth() + 1;
                //var dayStart = startDate.getDate();
                //test.info('Check Inventory for Month:'+monthStart+' Day:'+dayStart);
                var invValue = this.evaluate(function (dbDate) {
                    //here I can add an option to check resource day too
                    return $jppr('#calendarProducts .fc-view-resourceMonth a[href^="'+dbDate+'"]').text();
                }, dbDate);

                test.info('Check inventory value:');
                test.assertEqual(invValue, expected, 'Check Inventory Report For: Product'+product+' Date:'+date);

            });
        });
    });
};


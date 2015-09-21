
casper.addToCart = function(test, url, currentStartTime, currentEndTime, startTimeValue, endTimeValue, optionsArr, qty, buyout, expectedValue){

    casper.thenOpen(mage.getBaseUrl()+url,function() {

        startDate = toFormattedDate(currentStartTime);
        endDate = toFormattedDate(currentEndTime);

        test.info('Start Date: ' + startDate + ' Start Time:' + startTimeValue + ' End Date: ' + endDate + ' End Time:' + endTimeValue + ' Qty:' + qty +' Buyout:'+ buyout);
        //clean the price
        this.evaluate(function () {
            $jppr('.reservationCalendarDiv .price-box').css('display', 'none');
            return true;

        });

        this.evaluate(function (qty, startTimeValue, endTimeValue) {
            $jppr('#qty').val(qty);
            if ($jppr('#start_time').length > 0 && startTimeValue != '00:00:00') {
                $jppr('#start_time').val(startTimeValue);
            }
            if ($jppr('#end_time').length > 0 && endTimeValue != '00:00:00') {
                $jppr('#end_time').val(endTimeValue);
            }
            return true;
        }, qty, startTimeValue, endTimeValue);

        for (var key1 in optionsArr) {
            casper.evalFunc(optionsArr, key1);
        }
        updateNrOptions(optionsArr);
        this.on('resource.received', checkCallsUpdateBooked);

        this.waitFor(function check() {
            return nrOptionsUpdateBooked <= 0;

        }, function then() {
            this.removeListener('resource.received', checkCallsUpdateBooked);
            var retValueStart = this.evaluate(function (currentStartTime) {
                var startTime = new Date(currentStartTime);
                $jppr('.datePicker').datepick('selectDateTd', startTime);
                return $jppr('.readStartDate').val();

            }, currentStartTime);

            test.info('Filling start date input');
            test.assertEqual(startDate, retValueStart, 'Filling start date input');

            var retValueEnd = this.evaluate(function (currentEndTime) {
                var endTime = new Date(currentEndTime);
                $jppr('.datePicker').datepick('selectDateTd', endTime);
                return $jppr('.readEndDate').val();
            }, currentEndTime);

            test.info('Filling end date input');
            test.assertEqual(endDate, retValueEnd, 'Filling end date input');

            this.wait(5000, function () {
                this.waitForResource(function testResource(resource) {
                    if (resource.url.indexOf("getprice") > 0) {
                        console.log("Returned status code " + resource.status);
                        return true;
                    }
                    return false;
                }, function onReceived() {
                    //this.capture('D:/Projects/rfq14/.modman/rental/js/itwebexperts_payperrentals/casperjs/captures/bf.png');
                    this.evaluate(function (buyout) {
                        $jppr('.product-name h1').remove();
                        if(typeof buyout != 'undefined' && buyout == 1){
                            $jppr('.btn-cart-buyout').click();
                        }else{
                            $jppr('.btn-cart').click();
                        }

                    }, buyout);

                    this.waitFor(function check() {
                        return this.evaluate(function () {
                            $jppr = jQuery.noConflict();
                            var pageTitleNow = $jppr('.page-title h1');
                            if(pageTitleNow.length > 0) {
                                return pageTitleNow.html().toLowerCase() == 'shopping cart';
                            }else{
                                if($jppr('.product-name h1').length > 0){
                                    return true;
                                }else {
                                    return false;
                                }
                            }
                        });
                    }, function then() {
                        var errorMsg = this.evaluate(function () {
                            $jppr = jQuery.noConflict();
                            return $jppr('.error-msg').text();
                        });
                        if (errorMsg === null) {
                            errorMsg = "null";
                        }
                        test.assertEqual(errorMsg, expectedValue,'Add to cart with data: '+'Start Date: ' + startDate + ' Start Time:' + startTimeValue + ' End Date: ' + endDate + ' End Time:' + endTimeValue + ' Qty:' + qty +' Buyout:'+ buyout);
                    });
                });
            });
        });
    });
};


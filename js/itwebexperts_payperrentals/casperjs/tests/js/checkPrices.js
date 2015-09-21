casper.checkPrices = function(test, url, currentStartTime, currentEndTime, startTimeValue, endTimeValue, optionsArr, qty, expectedValue){

    casper.thenOpen(mage.getBaseUrl()+url,function(){

        startDate = toFormattedDate(currentStartTime);
        endDate = toFormattedDate(currentEndTime);

        test.info('Start Date: ' + startDate + ' Start Time:' + startTimeValue+ ' End Date: ' + endDate + ' End Time:' +endTimeValue+ ' Qty:' + qty);
        this.evaluate(function() {
            $jppr('.btn-resfreshCalendar').trigger('click');
        });

        this.evaluate(function (qty, startTimeValue, endTimeValue) {
            $jppr('#qty').val(qty);
            if($jppr('#start_time').length > 0 && startTimeValue != '00:00:00') {
                $jppr('#start_time').val(startTimeValue);
            }
            if($jppr('#end_time').length > 0 && endTimeValue != '00:00:00') {
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

                    var retValueStart = this.evaluate(function(currentStartTime){
                        var startTime = new Date(currentStartTime);
                        $jppr('.datePicker').datepick('selectDateTd', startTime);
                        return $jppr('.readStartDate').val();

                    }, currentStartTime);

                    test.info('Filling start date input');
                    test.assertEqual(startDate, retValueStart, 'Filling start date input');
                    var retValueEnd = this.evaluate(function(currentEndTime){
                        var endTime = new Date(currentEndTime);
                        $jppr('.datePicker').datepick('selectDateTd', endTime);
                        return $jppr('.readEndDate').val();
                    }, currentEndTime);

                    test.info('Filling end date input');
                    test.assertEqual(endDate, retValueEnd, 'Filling end date input');



                    this.wait(5000, function () {
                        this.waitForResource(function testResource(resource) {
                            if (resource.url.indexOf("getprice") > 0) {
                                console.log("Returned status code " + resource.status+' url: '+resource.url);
                                if(resource.status == "200") return true;
                            }
                            return false;
                        }, function onReceived() {
                            var number = this.evaluate(function () {
                                if($jppr('.reservationCalendarDiv .price-box .price').length > 0) {
                                    return Number($jppr('.reservationCalendarDiv .price-box .price').html().replace(/[^0-9\.]+/g, ""));
                                }else{
                                    return Number($jppr('.product-options-bottom .price-box .price').html().replace(/[^0-9\.]+/g, ""));
                                }
                            });
                            test.info('Test pricing for selected dates:');
                            test.assertEqual(number, parseFloat(expectedValue),'Pricing test with data: '+'Start Date: ' + startDate + ' Start Time:' + startTimeValue+ ' End Date: ' + endDate + ' End Time:' +endTimeValue+ ' Qty:' + qty);
                            casper.emit('test.finished');
                        });
                    });
                });
            });
};

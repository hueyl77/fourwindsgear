casper.checkBlackoutDates = function(test, url, currentStartTime, currentEndTime, startTimeValue, endTimeValue, optionsArr, qty, expectedValue){

    casper.thenOpen(mage.getBaseUrl()+url,function(){

        startDate = toFormattedDate(currentStartTime);
        endDate = toFormattedDate(currentEndTime);

        test.info('Start Date: ' + startDate + ' Start Time:' + startTimeValue+ ' End Date: ' + endDate + ' End Time:' +endTimeValue+ ' Qty:' + qty);

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
            var checkVal = this.evaluate(function (expectedValue) {
                var bookedDatesReturned = [];
                $jppr('.datePicker .ui-datepicker-reserved').each(function () {
                    var dateObj = new Date(parseFloat($jppr(this).attr('itime')));
                    var dateFormatted = $jppr.datepick.formatDate('yy-m-d', dateObj);
                    bookedDatesReturned.push(dateFormatted);
                });

                var bookDateArrayInit = [];
                if(expectedValue != 'disabled') {
                    var expectedArr = expectedValue.split(',');

                    for (var i = 0; i < expectedArr.length; i++) {
                        var newDate = new Date();
                        newDate.setDate(newDate.getDate() + parseInt(expectedArr[i]));
                        var dateFormatted = $jppr.datepick.formatDate('yy-m-d', newDate);
                        bookDateArrayInit.push(dateFormatted);
                    }

                    console.log('Check if bookedDates are same -Expected:' + bookDateArrayInit.toString() + ' Returned:' + bookedDatesReturned.toString());
                    return $jppr(bookedDatesReturned).not(bookDateArrayInit).length == 0 && $jppr(bookDateArrayInit).not(bookedDatesReturned).length == 0;
                }else{
                    var bookedDatesReturned = [];
                    $jppr('.datePicker .ui-datepicker-reserved, .datePicker .blockedtoday').each(function () {
                        var dateObj = new Date(parseFloat($jppr(this).attr('itime')));
                        var dateFormatted = $jppr.datepick.formatDate('yy-m-d', dateObj);
                        bookedDatesReturned.push( dateFormatted);
                    });

                    var currentDate = new Date();
                    var monthStart = new Date(currentDate.getYear(), currentDate.getMonth(), 1);
                    var monthEnd = new Date(currentDate.getYear(), currentDate.getMonth() + 1, 1);
                    var monthLength = parseInt((monthEnd - monthStart) / (1000 * 60 * 60 * 24));
                    console.log('Calendar should be disabled so bookedDatesReturned length:'+bookedDatesReturned.length+' should be the same as month length:'+monthLength);
                    return (monthLength - 1) <= bookedDatesReturned.length;
                }
            }, expectedValue);
            test.assertEqual(true, checkVal,'Check blackout dates, verify console to see what should be the expected result for:'+ 'Start Date: ' + startDate + ' Start Time:' + startTimeValue+ ' End Date: ' + endDate + ' End Time:' +endTimeValue+ ' Qty:' + qty);


        });
    });
};

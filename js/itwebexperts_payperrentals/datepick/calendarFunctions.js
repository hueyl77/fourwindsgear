//frontend functions

/**
 * Check if date is fixed date and returns end_date for it
 */

isFixedDateFunction = function(date){
    var returnDate = 'none';
    if(isAdminSide){
        return returnDate;
    }
    for(i = 0; i < fixedRentalDates.length; i++){
        if(date == fixedRentalDates[i]['start_date']){
            returnDate = fixedRentalDates[i]['end_date'];
            break;
        }
    }
    return returnDate;
}

/**
 * Shows loading icon
 * @param $_elem
 */
showAjaxLoader = function ($_elem) {
    $_elem.hide();
    $jppr("<span class='ajax_loader' style=''><img src='"+ajaxLoaderImg+"'/></span>").insertAfter($_elem);
};

/**
 * Hides loading icon
 * @param $_elem
 */

removeAjaxLoader = function ($_elem) {
    $_elem.parent().find('.ajax_loader').remove();
    $_elem.show();
};

/*
Functions used to update the price in admin search grid after user selects the product
or configure if bundle or configurable
 */
function updateRowWithPrice(price, stockAvail, stockRest, qty){
    var listType = 'product_to_add';
    $quoteItemObject.find('.qty').removeAttr('disabled');

    productConfigure.setConfirmCallback(listType, function() {
        $quoteItemObject.find('.price').html(price);
        $quoteItemObject.find('.currentstock').html(stockAvail);
        $quoteItemObject.find('.remainingstock').html(stockRest);
        $quoteItemObject.find('.qty').val(qty);

        $quoteItemObject.find('.qty').change(function () {
            $quoteItemObject.find('.remainingstock').html($quoteItemObject.find('.currentstock').html() - $jppr(this).val());
        });
        var checkbox = $quoteItemObject.find('.checkbox').get(0);

        sales_order_create_search_gridJsObject.setCheckboxChecked(checkbox, true);
        if ($quoteItemObject.find('.checkbox').first().attr('isadd') == '1' && typeof $quoteItemObject.find('.checkbox').first().attr('clicked') === 'undefined') {
            $quoteItemObject.find('.checkbox').first().attr('clicked', 1);
            order.productGridAddSelected();
        }
    }.bind(this));
}

calculatePriceShoppingCart = function (selfObj) {
    $jppr('.errorShow').text('');
    $jppr('.datesShow').text('');
    $jppr('#btn-update-global-dates').removeAttr('disabled');
    $jppr('#btn-update-global-dates').attr('style', 'opacity:1')
    $jppr.ajax({
        cache: false,
        dataType: 'json',
        type: 'post',
        url: urlCheckPriceForCart,
        data: $jppr(currentCalendar).find('*').serialize(),
        success: function (data) {
            if (data.noAllDate == false) {
                if (data.noHaveAmount != undefined && data.noHaveAmount != '') {
                    var message = 'Products ' + data.noHaveAmount.replace('|||', ', ') + ' not have amount for selected date';
                    $jppr(currentCalendar).find(' .errorShow').text(message);
                    $jppr('#btn-update-global-dates').attr('disabled', 'disabled');
                    $jppr('#btn-update-global-dates').attr('style', 'opacity:0.5')
                }
                if (data.useGlobalDisabled != undefined && data.useGlobalDisabled != '') {
                    var message = 'Global event dates disabled for products ' + data.useGlobalDisabled.replace('|||', ', ');
                    $jppr(currentCalendar).find(' .datesShow').text(message);
                }
            }
            $jppr(currentCalendar).removeData('wait_price');
        }
    });
}

/**
 * Function used to calculate the price in admin
 * Is different compared to the frontend one because different rules applies to what happens after date is selected
 */
calculatePriceAdmin = function () {
    if(!checkboxWasPressed && !isInQuote) {
        if ($quoteItemObject.find('.checkbox').first().attr('isadd') != '1') {
            $quoteItemObject.attr('is_pressed', '0');
        }

        if (isAvailable) {
            updateRowWithPrice(price, stockAvail, stockRest, 1);
            productConfigure.onConfirmBtn();
        } else {
            productConfigure._showWindow();
        }
    }else {
        Element.show('loading-mask');
        var qtyDisabledArr = [];
        $jppr('.qty-disabled').each(function () {
            qtyDisabledArr.push($jppr(this).attr('id'));
            $jppr(this).removeAttr('disabled');
        });
        if (typeof bundle !== 'undefined') {
            for (var option in bundle.config.selected) {
                if (bundle.config.options[option]) {
                    for (var i = 0; i < bundle.config.selected[option].length; i++) {
                        if (bundle.config.options[option].selections[bundle.config.selected[option][i]].typeid == 'reservation') {
                            //alert( $jppr('input[name="bundle_option_qty['+option+']"]').attr('id'));
                            $jppr('input[name="bundle_option_qty[' + option + ']"]').attr('onblur', '');
                            $jppr('input[name="bundle_option_qty[' + option + ']"]').attr('onkeyup', '');
                        }
                    }
                }
            }
        }
        if (typeof $quoteItemObject.data('wait_price') === 'undefined') {
            $quoteItemObject.data('wait_price', 1);
            $jppr.ajax({
                cache: false,
                dataType: 'json',
                type: 'post',
                url: getPriceAndAvailabilityUrl,
                data: $calendarTableObject.closest('form').find('*').serialize(),
                success: function (data) {
                    $quoteItemObject.removeData('price_current_product');
                    $jppr('.qty-disabled').each(function () {
                        $jppr(this).attr('disabled', 'disabled');
                    });
                    if (data.amount >= 0) {
                        $jppr('.datesPrice').html('Price for selected dates: ' + data.formatAmount);
                    } else {
                        $jppr('.datesPrice').html('No Price for the selected dates');
                    }

                    //show current stock and remaining stock?
                    $jppr('.datesPrice').html(data.stockText + $jppr('.datesPrice').html());

                    if (data.amount == -1 || data.available == false) {
                        $jppr('.buttons-set button[type="submit"]').hide();

                        if (data.available == false && data.maxqty != $calendatTableObject.find('input[name="qty"]').first().val() && data.maxqty > 0) {
                            $jppr('.datesPrice').html(noQtyMessage);
                        } else {
                            $jppr('.datesPrice').html(noPriceMessage);
                        }
                    } else {
                        $quoteItemObject.data('price_current_product', data.amount);
                        $jppr('.buttons-set button[type="submit"]').show();
                        updateRowWithPrice(data.formatAmount, data.stockAvail, data.stockRest, data.qty);
                    }
                    $quoteItemObject.removeData('wait_price');
                    Element.hide('loading-mask');
                }
            });
        }
    }
};
/**
 * This function is used to calculate the price when dates are changed
 * @param selfObj
 */
calculatePrice = function () {
    if($jppr('.datesblock').length > 0){
        dataForm = $jppr('.datesblock').find('*').serialize()+ '&' + $jppr('#product_addtocart_form').find('*').serialize() + '&qty=' + $jppr('#qty').val();
    }else{
        dataForm = $jppr(currentCalendar).closest('form').find('*').serialize() + '&qty=' + $jppr('#qty').val();
    }
    var qtyDisabledArr = [];
    $jppr(currentCalendar).find('.qty-disabled').each(function () {
        qtyDisabledArr.push($jppr(this).attr('id'));
        $jppr(this).removeAttr('disabled');
    });
    if (typeof bundle !== 'undefined') {
        for (var option in bundle.config.selected) {
            if (bundle.config.options[option]) {
                for (var i = 0; i < bundle.config.selected[option].length; i++) {
                    if (bundle.config.options[option].selections[bundle.config.selected[option][i]].typeid == 'reservation') {
                        $jppr(currentCalendar).find('input[name="bundle_option_qty[' + option + ']"]').attr('onblur', '');
                        $jppr(currentCalendar).find('input[name="bundle_option_qty[' + option + ']"]').attr('onkeyup', '');
                    }
                }
            }
        }
    }
    if (typeof $jppr(currentCalendar).data('wait_price') === 'undefined') {
        $jppr(currentCalendar).data('wait_price', 1);
        $jppr.ajax({
            cache: false,
            dataType: 'json',
            type: 'post',
            url: getPriceUrl,
            data: dataForm,
            success: function (data) {
                $jppr(currentCalendar).find('.btn-cartgrouped').attr('onclick', data.onclick);

                $jppr(currentCalendar).find('.qty-disabled').each(function () {
                    $jppr(this).attr('disabled', 'disabled');
                });

                //rtrwStyleAutomaticallySelectFirstAvailableDate($selfID, data.availdate);

                if (!useGlobals && (data.amount == -1 /*|| data.isavail == false*/)) {
                    $jppr(currentCalendar).find('.btn-cart').each(function () {
                        $(this).disabled = true;
                        if ($jppr(currentCalendar).find('.readStartDate').val() == '' || $jppr(currentCalendar).find('.readEndDate').val() == '') {
                            $jppr(currentCalendar).find('.errorShow').html('');
                        } else {
                            if (data.needsConfigure == true) {
                                $jppr(currentCalendar).find('.errorShow').html(chooseOptionsForPrice);
                            } else if (data.amount == -1) {
                                $jppr(currentCalendar).find('.errorShow').html(noPriceMessage);
                            } else {
                                $jppr(currentCalendar).find('.instock').html('Item is Out of Stock(Estimated Availability Date:' + data.availdate + ')');
                            }
                        }
                        $jppr(currentCalendar).find('.errorShow').show();
                    });
                } else {
                    if (!isDisabledByTimes && !isDisabled) {
                        $jppr(currentCalendar).find('.btn-cart').each(function () {
                            $(this).disabled = false;
                            $jppr(currentCalendar).find('.errorShow').hide();
                            $jppr(currentCalendar).find('.instock').html('Item is In Stock');
                        });
                    }
                    //updateBundleOrCustomOptionPrice(data);

                }

                if(data.fixedDates && data.fixedDates != ''){
                    $jppr('.fixedoptionbelow').show();
                    $jppr('.fixedDateDropdown').html(data.fixedDates);
                    $jppr('.fixed_array li').css('cursor', 'pointer');
                    $jppr('.fixed_array li').click(function(){
                        $jppr('.fixed_array li').each(function(){
                           $jppr(this).removeClass('selectedli');
                        });
                        $jppr(this).addClass('selectedli');
                       $jppr('.fixeddateid').val($jppr(this).attr('idname'));
                    });
                    $jppr('.fixed_array li').first().trigger('click');
                }else{
                    $jppr('.fixedoptionbelow').hide();
                }
                removeAjaxLoader($jppr(currentCalendar).find('.product-options-bottom .price-box'));
                updatePriceHtml(data.amount, data.formatAmount);
                $jppr(currentCalendar).removeData('wait_price');
            }
        });
    }


};

/**
 * Updates the actual inputs value with the selected dates from the calendar
 * The actual inputs which are posted are hidden and they change when a date in the calendar is changed
 * @param selfObj
 */

updateInputVals = function () {
    if ($jppr(currentCalendar).find('.start_date').val() != '' && $jppr(currentCalendar).find('.end_date').val() != '') {
        if(isAdminSide){
            if(typeof $quoteItemObject !== 'undefined') {
                calculatePriceAdmin();
            }else{
                updateInitials();
            }
        }else {
            if(isCheckoutPage && typeof calculatePriceShoppingCart == 'function'){
                calculatePriceShoppingCart()
            }else {
                calculatePrice();
            }
        }
    } else {
        $jppr(currentCalendar).find('.btn-cart').each(function () {
            $(this).disabled = true;
        });
    }

};


/**
 * Price container is different for product types. This function updates the price when dates are changed
 * @param amount
 */

updatePriceHtml = function (amount, formatAmount) {
    if($jppr('#product_addtocart_form').length> 0 ){
        currentCalendarTemp = currentCalendar;
        currentCalendar = '#product_addtocart_form';
    }
    $jppr(currentCalendar).find('.price-box').html(formatAmount);
    $jppr(currentCalendar).find('.price-box').hide();
    if ($jppr(currentCalendar).find(' .product-options-bottom .price-box').length > 0) {
        if (amount > 0) {
            $jppr(currentCalendar).find('.product-options-bottom .price-box').show();
        }
    } else if (amount > 0) {
        $jppr(currentCalendar).find('.reservationCalendarDiv .price-box').show();
    }

    var startValue = $jppr(currentCalendar).find('.start_time').val();
    var endValue = $jppr(currentCalendar).find('.end_time').val();

    var startDateValue = Date.parseExact($jppr(currentCalendar).find('[name=start_date]').val(), localDateFormat);
    var startDateFormated = $jppr.formatDateTime('yy-mm-dd', startDateValue);
    var startDate1 = new Date(startDateFormated + ' ' + startValue);

    var endDateValue = Date.parseExact($jppr(currentCalendar).find('[name=end_date]').val(), localDateFormat);
    var endDateFormated = $jppr.formatDateTime('yy-mm-dd', endDateValue);
    var endDate1 = new Date(endDateFormated + ' ' + endValue);


    if(startDate1 != 'Invalid Date' && endDate1 != 'Invalid Date' && ($jppr(currentCalendar).find('[name=start_date]').val() == $jppr(currentCalendar).find('[name=end_date]').val())) {
        var allowSelectionMin = !(endDate1.getTime() - startDate1.getTime() + 1000 <= minRentalPeriod - addStartingDatePeriod);
        var allowSelectionMax = !((endDate1.getTime() - startDate1.getTime() > maxRentalPeriod) && maxRentalPeriod != 0);

        if (allowSelectionMin == false && minRentalPeriod > 0) {
            alert(minRentalPeriodMessage);
            $jppr(currentCalendar).find('.btn-cart').each(function () {
                $(this).disabled = true;
            });
            currentCalendar = currentCalendarTemp;
            return false;
        }
        if (allowSelectionMax == false) {
            alert(maxRentalPeriodMessage);
            $jppr(currentCalendar).find('.btn-cart').each(function () {
                $(this).disabled = true;
            });
            currentCalendar = currentCalendarTemp;
            return false;
        }
    }
    if(typeof currentCalendarTemp != 'undefined') {
        currentCalendar = currentCalendarTemp;
    }
};

/**
 * Automatically select first available date when RTRW style is enabled
 * deprecated
 */

rtrwStyleAutomaticallySelectFirstAvailableDate = function(availdate){
    var availAsDate = Date.parseExact(availdate, 'yyyy-MM-dd');
    selectedToStartPeriod = availdate;
    if ($jppr(currentCalendar).find('.start_date').val() != $jppr.formatDateTime(serverDateFormat, availAsDate) && availdate != '') {
        $jppr(currentCalendar).find('.start_date').val($jppr.formatDateTime(serverDateFormat, availAsDate));
        $jppr(currentCalendar).find('.datePicker').datepick('selectDateTd', availAsDate);
        return false;
    }
    return true;
};
/**
 * On before show day action in calendar
 * Checks if dates are available
 * @param dateObj
 * @returns {*}
 */
beforeShowDayAction = function (dateObj) {
    dateObj.setHours(0, 0, 0, 0);
    var dateFormatted = $jppr.datepick.formatDate('yy-m-d', dateObj);

    today = currentDateForCalendar;

    if ($jppr.inArray(dayShortNames[dateObj.getDay()], disabledDays) > -1) {
        return [false, 'ui-datepicker-disabled manualadded disabledday', notAvailableText];
    }else if ($jppr.inArray(dayShortNames[dateObj.getDay()], disabledDaysEnd) > -1 && $jppr(currentCalendar).data('selected') == 'start') {
        return [false, 'ui-datepicker-disabled manualadded disableddayend', notAvailableText];
    }else if ($jppr.inArray(dayShortNames[dateObj.getDay()], disabledDaysStart) > -1 && $jppr(currentCalendar).data('selected') != 'start') {
        return [false, 'ui-datepicker-disabled manualadded disableddaystart', notAvailableText];
    }
    else if ($jppr.inArray(dateFormatted, disabledDatesPadding) > -1 && disabledWithMessage == false) {
        return [false, 'ui-datepicker-disabled manualadded disabledpadding', notAvailableText];
    } else if ($jppr.inArray(dateFormatted, blockedDates) > -1) {
        return [false, 'ui-datepicker-disabled manualadded blockeddate', notAvailableText];
    }
    else if (today > dateObj) {
        return [false, 'ui-datepicker-disabled manualadded blockedtoday', notAvailableText];
    }
    else if (typeof futureDate != 'undefined' && futureDate < dateObj) {
        return [false, 'ui-datepicker-disabled manualadded blockedtoday', notAvailableText];
    }
    else if (isBookedDate(dateFormatted) || isDisabled == true) {
        return [false, 'ui-datepicker-reserved manualadded bookeddate', reservedText];
    }
    fixedEndDate = isFixedDateFunction(dateFormatted);
    if(fixedEndDate != 'none'){
        return [true, 'fixeddate enddate'+encodeURIComponent(fixedEndDate), 'Select Start Fixed Date'];
    } else {
        if(isFixedDate){
            return [false, 'notavailabledate', notAvailableText];
        }else {
            return [true, 'availabledate', availableText];
        }
    }
}

/**
 * On select action in the calendar
 * @param value
 * @param date
 * @param inst
 */
selectDayAction = function (value, date, inst) {
    $jppr(currentCalendar).attr('isenter', 0);
    var dates = value.split(',');

    if(!useNonSequential) {
        var noEndDate = false;
        if (dates[1] == dates[0] && $jppr(currentCalendar).data('selected') == 'start') {
            noEndDate = true;
        }
        if (autoSelectEndDate && noEndDate) {
            var stDate1 = new Date(dates[0]);
            stDate1.setDate(stDate1.getDate() + parseInt(selectedToEndPeriod));
            $jppr(currentCalendar).find('.datePicker').datepick('selectDateTd', stDate1);
            return;
        }

        $jppr(currentCalendar).find('.start_date').val($jppr.formatDateTime(serverDateFormat, new Date(dates[0])));
        $jppr(currentCalendar).find('.readStartDate').val($jppr.formatDateTime(serverDateFormat, new Date(dates[0])));

        if (!noEndDate) {
            $jppr(currentCalendar).find('.readEndDate').val($jppr.formatDateTime(serverDateFormat, new Date(dates[1])));
            $jppr(currentCalendar).find('.end_date').val($jppr.formatDateTime(serverDateFormat, new Date(dates[1])));
            updateInputVals();
            if ($jppr(currentCalendar).find('.start_date').val() != $jppr(currentCalendar).find('.end_date').val()) {
                if (!isDisabledByTimes) {
                    $jppr(currentCalendar).find('.btn-cart').each(function () {
                        $(this).disabled = false;
                        $jppr(currentCalendar).find('.errorShow').hide();
                    });
                }
            }
            if(!showByDefault) {
                $jppr(currentCalendar).find('.datePickerDiv').hide();
            }
        } else {
            $jppr(currentCalendar).find('.readEndDate').val('');
            $jppr(currentCalendar).find('.end_date').val('');
        }

        if ($jppr(currentCalendar).find('.day-detail-container') != undefined && $jppr(currentCalendar).find('.day-detail-container').length > 0) {
            createBusyTime($jppr(currentCalendar).data('selected'));
        }

    }
    if(useNonSequential) {
        if (dates[0] == dates[1]) {
            $jppr(currentCalendar).find('.start_date').val(dates[0]);
            $jppr(currentCalendar).find('.readStartDate').val(dates[0]);
        } else {
            valueArr = value.split(',');
            idates = '';
            key = valueArr.length - 1;
            for (i = 0; i < valueArr.length; i++) {
                if (i != key) {
                    idates = idates + $jppr.formatDateTime(serverDateFormat, new Date(valueArr[i])) + ',';
                } else {
                    idates = idates + $jppr.formatDateTime(serverDateFormat, new Date(valueArr[i]));
                }
            }
            $jppr(currentCalendar).find('.start_date').val(idates);
            $jppr(currentCalendar).find('.readStartDate').val(idates);

            $jppr(currentCalendar).find('.end_date').val(idates);
            $jppr(currentCalendar).find('.readEndDate').val(idates);
        }
        updateInputVals();
    }
    if ($jppr(currentCalendar).data('mousedown') == 1) {

    } else {
        if(!useNonSequential) {
            if(!showByDefault) {
                $jppr(currentCalendar).find('.datePickerDiv').fadeOut("fast");
            }
        }
    }

}
/**
 * On click action in the calendar
 * @param date
 * @param inst
 * @param td
 * @returns {*}
 */
clickDayAction =  function (date, inst, td){
    var dateFormatted = $jppr.datepick.formatDate('yy-m-d', date);

    if($jppr(td).hasClass('fixeddate')){
        autoSelectEndDate = true;
        endDateStringPos = $jppr(td).attr('class').indexOf('enddate')+7;
        endDateStringPosLength = $jppr(td).attr('class').indexOf(' ', endDateStringPos);
        endDateFormatted = $jppr(td).attr('class').substr(endDateStringPos, endDateStringPosLength - endDateStringPos);
        timeDiff = Math.abs(Date.parseExact(endDateFormatted,'yyyy-M-d').getTime() - date.getTime());
        selectedToEndPeriod = 0;/*Math.ceil(timeDiff / (1000 * 3600 * 24));*/
    }
    if ($jppr.inArray(dateFormatted, disabledDatesPadding) > -1 && disabledWithMessage) {
        alert(disabledMessage);
        return false;
    }

    if(isBookedDateWithTurnovers(dateFormatted)){
        alert(disabledMessageTurnovers);
        $jppr(currentCalendar).find('.btn-resfreshCalendar').trigger('click');
        return false;
    }

    var allowSelectionMin = true;
    var allowSelection = true;
    var allowSelectionMax = true;

    if(!useNonSequential) {
        if ($jppr(currentCalendar).data('selected') == 'start') {
            allowSelection = true;
            $jppr.each(bookedDates, function (index, value) {
                if (parseInt($jppr('#qty').val()) > value) {
                    bDate = toDate(index);
                    if (typeof $jppr(currentCalendar).data('selectedDate') != 'undefined' && $jppr(currentCalendar).data('selectedDate') != '' && $jppr(currentCalendar).data('selectedDate').getTime() - toMilisecondsFromDays(turnoverTimeBefore) <= bDate.getTime() && date.getTime() + toMilisecondsFromDays(turnoverTimeAfter) >= bDate.getTime()) {
                        allowSelection = false;
                    }
                }
            });
            if (typeof $jppr(currentCalendar).data('selectedDate') != 'undefined' && $jppr(currentCalendar).data('selectedDate') != '') {
                allowSelectionMin = !(date.getTime() - $jppr(currentCalendar).data('selectedDate').getTime() + 24 * 60 * 60 * 1000 <= minRentalPeriod - addStartingDatePeriod);
                allowSelectionMax = !((date.getTime() - $jppr(currentCalendar).data('selectedDate').getTime() > maxRentalPeriod) && maxRentalPeriod != 0);
            }
        }
        selected = (typeof $jppr(currentCalendar).data('selected') == 'undefined' || $jppr(currentCalendar).data('selected') == '' || $jppr(currentCalendar).data('selected') == 'end' ? 'start' : 'end');
        if (selected == 'end') {
            if (allowSelectionMin == false) {
                alert(minRentalPeriodMessage);
                return false;
            }
            if (allowSelectionMax == false) {
                alert(maxRentalPeriodMessage);
                return false;
            }
            if (allowSelection == false) {
                alert(betweenMessage);
                $jppr(currentCalendar).find('.btn-resfreshCalendar').trigger('click');
                return false;
            }
        }


        selectedDate = '';

        if (selected == 'start') {
            selectedDate = date;
            if(showByDefault && fixedRentalDates.length == 0) {
                $jppr('.chooseDatesLabel').text(chooseDatesLabelEnd);
            }
        }else{
            if(showByDefault && fixedRentalDates.length == 0) {
                $jppr('.chooseDatesLabel').text(chooseDatesLabelStart);
            }
        }

        $jppr(currentCalendar).data('selected', selected);
        $jppr(currentCalendar).data('selectedDate', selectedDate);
    }
    if(useNonSequential) {
        var myVal = $jppr(currentCalendar).find('.start_date').val();
        var dates = myVal.split(',');
        var nrDates = dates.length;
    }
    return this;
}

function toDate(dateValue){
    bDateArr = dateValue.split('-');
    return new Date(parseInt(bDateArr[0]), parseInt(bDateArr[1]) - 1, parseInt(bDateArr[2]));
}

function toDaysFromMiliseconds(milis){
    return milis / (60 * 60 * 24 * 1000);
}

function toMilisecondsFromDays(days){
    return days * (60 * 60 * 24 * 1000);
}

function isBookedDate(dateFormatted){
    var qty = parseInt($jppr('#qty').val());
    if(!qty) qty = 1;
    return (bookedDates[dateFormatted] < qty);

}


function isBookedDateWithTurnovers(dateFormatted){
    var qty = parseInt($jppr('#qty').val());
    var isBook = false;

    for (i = 0; i <= turnoverTimeBefore; i++) {

        var stDate1 = toDate(dateFormatted);
        stDate1.setDate(stDate1.getDate() - i);
        var dateFormattedNew = $jppr.datepick.formatDate('yy-m-d', stDate1);

        if (qty > bookedDates[dateFormattedNew]) {
            isBook = true;
            break;
        }
    }

    for (i = 1; i <= turnoverTimeAfter;i++) {
        var stDate1 = toDate(dateFormatted);
        stDate1.setDate(stDate1.getDate() + i);
        var dateFormattedNew = $jppr.datepick.formatDate('yy-m-d', stDate1);

        if (qty > bookedDates[dateFormattedNew]) {
            isBook = true;
            break;
        }
    }

    return isBook;
}


/**
 * Function which creates the calendar
 * @param selfObj
 */
createCalendar = function () {
    if ($jppr(currentCalendar).find('.datePicker').length > 0) {
        $jppr(currentCalendar).find('.datePicker').datepick({
            minDate: (typeof $jppr(currentCalendar).data('pprMinDate') === "undefined" ? currentDateForCalendar : $jppr(currentCalendar).data('pprMinDate') ),
            maxDate: (typeof $jppr(currentCalendar).data('pprMaxDate') === "undefined" ? null : $jppr(currentCalendar).data('pprMaxDate') ),
            monthsToShow: monthsToShow,
            rangeSelect: rangeSelect,
            rangeSeparator: ',',
            changeMonth: false,
            //firstDay: 0,
            turnoverBefore: turnoverTimeBefore,
            turnoverAfter: turnoverTimeAfter,
            useThemeRoller:useThemeRoller,
            useFlicker: useFlicker,
            changeYear: false,
            numberOfMonths: monthsToShow,
            showStatus: true,
            beforeShowDay: beforeShowDayAction,
            onSelect: selectDayAction,
            onDayClick: clickDayAction
        });

        if(useNonSequential) {
            $jppr(currentCalendar).find('.datePicker').datepick('multiSelect', 15);
            $jppr(currentCalendar).find('.datePicker').datepick('multiSeparator', ',');
        }


        $jppr(currentCalendar).find('.btn-cart').each(function () {
            if ($jppr(currentCalendar).find('.readStartDate').val() != '' && $jppr(currentCalendar).find('.readEndDate').val() != '') {
                $(this).disabled = false;
                $jppr(currentCalendar).find('.errorShow').hide();
            } else {
                $(this).disabled = true;
            }
        });
    }
};
/**
 * Function used when reset button is used
 * @param $selfID
 */
refreshCalendar = function () {
    if ($jppr(currentCalendar).data('selected') == 'start') {
        $jppr(currentCalendar).find('.datePicker').datepick('setDate', 0);
    } else if ($jppr(currentCalendar).data('selected') == 'end') {
        $jppr(currentCalendar).find('.datePicker').datepick('setDate', -1);
    }
    $jppr(currentCalendar).data('selected', '');
    $jppr(currentCalendar).data('selectedDate', '');

    $jppr(currentCalendar).find('.start_date').val('').trigger('change');
    $jppr(currentCalendar).find('.end_date').val('').trigger('change');
    $jppr(currentCalendar).find('.end_date').val('').trigger('change');
    $jppr(currentCalendar).find('.readStartDate').val('');
    $jppr(currentCalendar).find('.readEndDate').val('');
    //varienGlobalEvents.fireEvent('refresh_calendar');
    $jppr(currentCalendar).find('.btn-cart').each(function () {
        $(this).disabled = true;
        if ($jppr(currentCalendar).find('.readStartDate').val() == '' || $jppr(currentCalendar).find('.readEndDate').val() == '') {
            $jppr(currentCalendar).find('.errorShow').html('');
        } else {
            $jppr(currentCalendar).find('.errorShow').html(noPriceMessage);
        }
        $jppr(currentCalendar).find('.errorShow').show();
    });
}


/**
 * This function updates the calendar with the booked dates, disabled dates etc.
 * @param selfObj
 */
updateBookedDates = function () {
    if($jppr('.datesblock').length > 0){
        dataForm = $jppr('.datesblock').find('*').serialize()+ '&' + $jppr('#product_addtocart_form').find('*').serialize() + '&qty=' + $jppr('#qty').val();
    }else{
        dataForm = $jppr(currentCalendar).closest('form').find('*').serialize() + '&qty=' + $jppr('#qty').val();
    }

    showAjaxLoader($jppr(currentCalendar).find('.product-options-bottom .price-box'));
    var qtyDisabledArr = [];
    $jppr(currentCalendar).find('.qty-disabled').each(function () {
        qtyDisabledArr.push($jppr(this).attr('id'));
        $jppr(this).removeAttr('disabled');
    });

    if (typeof bundle !== 'undefined') {
        for (var option in bundle.config.selected) {
            if (bundle.config.options[option]) {
                for (var i = 0; i < bundle.config.selected[option].length; i++) {
                    if (bundle.config.options[option].selections[bundle.config.selected[option][i]].typeid == 'reservation') {
                        $jppr(currentCalendar).find('input[name="bundle_option_qty[' + option + ']"]').attr('onblur', '');
                        $jppr(currentCalendar).find('input[name="bundle_option_qty[' + option + ']"]').attr('onkeyup', '');
                    }
                }
            }
        }
    }

    $jppr.ajax({
        cache: false,
        dataType: 'json',
        type: 'post',
        url: updateBookedUrl,
        data: dataForm,
        success: function (data) {
            $jppr(currentCalendar).find('.qty-disabled').each(function () {
                $jppr(this).attr('disabled', 'disabled');
            });

            bookedDates = data.bookedDates;

            turnoverTimeBefore = data.turnoverTimeBefore;
            turnoverTimeAfter = data.turnoverTimeAfter;
            maxQty = data.maxQty;
            blockedDates = data.blockedDates.split(',');
            fixedRentalDates =  data.fixedRentalDates;
            partiallyBooked = data.partiallyBooked;

            if (typeof data.disabledForStartRange != 'undefined') {
                disabledDaysStart = data.disabledForStartRange;
            }

            if (typeof data.disabledForEndRange != 'undefined') {
                disabledDaysEnd = data.disabledForEndRange;
            }

            if (typeof data.disabledDatesPadding != 'undefined') {
                disabledDatesPadding = data.disabledDatesPadding;
            }

            if (typeof data.ignoreTurnoverDay != 'undefined') {
                ignoreTurnoverDay = data.ignoreTurnoverDay;
            }

            if(typeof data.futureDate != 'undefined'){
                futureDate = new Date(data.futureDate);
            }

            if (typeof data.shippingMinRentalPeriod != 'undefined') {
                if (parseInt(minRentalPeriod, 10) < parseInt(data.shippingMinRentalPeriod, 10) * 1000) {
                    minRentalPeriod = parseInt(data.shippingMinRentalPeriod, 10) * 1000;

                    if (typeof data.shippingMinRentalMessage != 'undefined') {
                        minRentalPeriodMessage = data.shippingMinRentalMessage;
                    }
                }
            }

            isDisabled = data.isDisabled;
            if (isDisabled) {
                $jppr(currentCalendar).find('.btn-cart').each(function () {
                    $(this).disabled = true;
                    if ($jppr(currentCalendar).find('.readStartDate').val() == '' || $jppr(currentCalendar).find('.readEndDate').val() == '') {
                        $jppr(currentCalendar).find('.errorShow').html('');
                    } else {
                        $jppr(currentCalendar).find('.errorShow').html(noQtyMessage);
                    }
                    $jppr(currentCalendar).find('.errorShow').show();
                });

                if($jppr('.super-attribute-select').length > 0 || $jppr('.color-swatch-wrapper li').length > 0){
                    $jppr('.dateSelectedCalendar').hide();
                    if($jppr('.super-attribute-select').val() != '' || $jppr('.color-swatch-wrapper li.active').length > 0) {
                        if(data.isConfigurable == true){
                            alert('Maximum quantity for this option and product is: '+maxQty+' Press ok to adjust!');
                            $jppr('#qty').val(maxQty);
                            $jppr('#qty').trigger('change');
                            return false;
                        }else {
                            $jppr('.date-select-panel-disabled').show();
                        }
                    }else{
                        $jppr('.date-select-panel').show();
                    }
                }
            } else {
                $jppr(currentCalendar).find('.btn-cart').each(function () {
                    $(this).disabled = false;
                    $jppr(currentCalendar).find('.errorShow').hide();
                });
                if ($$('.shipMethods').length != 0 && $('shipping_method_select_box') != null && $('shipping_method_select_box').value == 'null' && $('zip_code').value == '') {
                    //$jppr('.dateSelectedCalendar').show();
                    //$jppr('.date-select-panel').hide();
                }else{
                    $jppr('.dateSelectedCalendar').show();
                    $jppr('.date-select-panel').hide();
                    $jppr('.date-select-panel-disabled').hide();
                }
            }
            if (!data.isShoppingCartDates && !data.needsConfigure && data.isConfigurable == true) {
                $jppr('.normalPrice').html(data.normalPrice);
            }
            $jppr(currentCalendar).find('.datePicker').remove();
            $jppr(currentCalendar).find('.datePickerDiv').prepend('<div class="datePicker"></div>');
            createCalendar();
            if(isAutoSelection) {
                calculatePrice();
            }else {
                removeAjaxLoader($jppr(currentCalendar).find('.product-options-bottom .price-box'));
                if ($jppr(currentCalendar).find('.readStartDate').val() == '' || $jppr(currentCalendar).find('.readEndDate').val() == '' || data.needsConfigure == true) {
                    updatePriceHtml(0, 0);
                    if (data.needsConfigure) {
                        $jppr(currentCalendar).find('.errorShow').html(chooseOptionsForPrice);
                        $jppr(currentCalendar).find('.errorShow').show();
                    }
                }
                if(!$jppr('.datesblock .calendarSelector .datesSelector').length) {
                    if (isUsingGlobalDatesShoppingCart) {
                        updatePriceHtml(0, 0);
                    }
                }
            }

            if(typeof createBusyTime == 'function') {
                createBusyTime('start');
                createBusyTime('end');
            }
            $jppr(currentCalendar).find('.start_time').trigger('change');
            return true;
        }
    });
};

/**
 * Function used to update the selected dates in admin
 */
function updateInitials() {
    if($jppr('#topDates .readStartDate').val() != '' && $jppr('#topDates .readEndDate').val() != ''){
        //Element.show('loading-mask');
        if(typeof $jppr(currentCalendar).data('wait_initials') === 'undefined'){
            $jppr(currentCalendar).data('wait_initials', 1);
            $jppr.ajax({
                cache: false,
                dataType: 'json',
                type: 'post',
                url: urlUpdateInitialsAdmin,
                data: $jppr('#topDates').find('*').serialize(),
                success: function (data) {
                Element.hide('loading-mask');
                if($jppr(currentCalendar).data('loaded')) {
                    order.loadArea(['search'], true);
                }
                $jppr(currentCalendar).data('loaded', true);
                $jppr(currentCalendar).removeData('wait_initials');
            }
        });
    }
}
}


/**
 * When calendar is used in global mode, it set the dates for the session.
 * @param url
 */
function updateInitialsGlobal(url) {

    if (typeof $jppr(currentCalendar).data('wait_initials') === 'undefined' || typeof url !== 'undefined') {
        $jppr(currentCalendar).data('wait_initials', 1);
        $jppr.ajax({
            cache: false,
            dataType: 'json',
            type: 'post',
            url: urlUpdateInitials,
            data: $jppr(currentCalendar).find('*').serialize(),
            beforeSend: function () {
                $jppr('#ajax-panel').html('<div class="loading"><img src="/skin/frontend/default/default/images/opc-ajax-loader.gif" alt="Loading..." /></div>');
            },
            success: function (data) {
                $jppr('#ajax-panel').empty();
                varienGlobalEvents.fireEvent('update_initial_global_success', {data: data, url: url});
                $jppr(currentCalendar).removeData('wait_initials');
            }
        });
    }
}

function onDocReady(){
    if(isAdminSide){
        $jppr('#changeButton').hide();
        $jppr('#topDates .btn-resfreshCalendar').hide();

        if ($jppr('#topDates .timeSelector').length > 0) {
            $jppr('#changeButton').nextTo($jppr('#topDates .end_time'), {
                position: 'right',
                shareBorder: 'bottom',
                offsetX: 20
            });
            $jppr('#changeButton').show();
            $jppr('#topDates .btn-resfreshCalendar').nextTo($jppr('#changeButton'), {
                position: 'right',
                shareBorder: 'bottom',
                offsetX: 20
            });

            $jppr('#topDates .btn-resfreshCalendar').show();
        } else {
            $jppr('#changeButton').nextTo($jppr('#topDates .readEndDate'), {
                position: 'right',
                shareBorder: 'bottom',
                offsetX: 20
            });
            $jppr('#changeButton').show();
            $jppr('#topDates .btn-resfreshCalendar').nextTo($jppr('#changeButton'), {
                position: 'right',
                shareBorder: 'bottom',
                offsetX: 20
            });
            $jppr('#topDates .btn-resfreshCalendar').show();
        }
    }
    $jppr(currentCalendar).find('.readStartDate,  .readEndDate').keyup(function (event) {
        $jppr(this).val('');
    });

    $jppr(currentCalendar).attr('isenter', 0);
    $jppr(currentCalendar).find('.datePicker').mouseenter(function (event) {
        $jppr(currentCalendar).attr('isenter', 1);
    }).mouseleave(function (event) {
        $jppr(currentCalendar).attr('isenter', 0);
    });

    $jppr(currentCalendar).data('mousedown', 0);
    if(showByDefault) {
        $jppr(currentCalendar).find('.datePicker').remove();
        $jppr(currentCalendar).find('.datePickerDiv').prepend('<div class="datePicker"></div>');
        createCalendar();
        if(!showByDefault) {
            $jppr(currentCalendar).find('.datePickerDiv').fadeIn("fast");
        }
    }else {
        $jppr('.readStartDate,  .readEndDate').mousedown(function (event) {
            if ($jppr(this).closest('#shoppingCartCalendar').length > 0) {
                currentCalendar = '#shoppingCartCalendar';
            } else if ($jppr(this).closest('.datesblock').length > 0) {
                currentCalendar = '.datesblock';
            } else if ($jppr(this).closest('#topDates').length > 0) {
                currentCalendar = '#topDates';
            } else if ($jppr(this).closest('.calendarTables').length > 0) {
                currentCalendar = '.calendarTables';
            } else {
                currentCalendar = '#product_addtocart_form';
            }
            if ($$('.shipMethods').length == 0 || (($('shipping_method_select_box') == null && $('shipping_method').value != '') || ($('shipping_method_select_box') != null && ($('shipping_method_select_box').value != 'null' || ($('shipping_method_select_box').value == 'null' && $('zip_code').value != ''))))) {
                $jppr(currentCalendar).find('.readStartDate,  .readEndDate').attr('disabled', false);
                if ($jppr('.datesblock .calendarSelector .datesSelector').length == 0) {
                    $jppr('.dateSelectedCalendar').show();
                    $jppr('.zip-select-panel').hide();
                }

                if ($jppr(this).hasClass('readStartDate')) {
                    $jppr(currentCalendar).data('global_field_selected', 'start');
                } else if ($jppr(this).hasClass('readEndDate')) {
                    $jppr(currentCalendar).data('global_field_selected', 'end');
                }
                $jppr(currentCalendar).find('.datePicker').remove();
                $jppr(currentCalendar).find('.datePickerDiv').prepend('<div class="datePicker"></div>');
                createCalendar();
                if ($jppr(this).hasClass('readStartDate') || autoSelectEndDate == false) {
                    if(!showByDefault) {
                        var pos = $jppr(this).position();
                        $jppr(currentCalendar).find('.datePickerDiv').css('top', (pos.top + 30) + 'px');
                        $jppr(currentCalendar).find('.datePickerDiv').css('left', (pos.left + 0) + 'px');
                        $jppr(currentCalendar).find('.datePickerDiv').fadeIn("fast");
                    }
                    if ($jppr(currentCalendar).find('.start_date').val() != '') {
                        var stDate = Date.parseExact($jppr(currentCalendar).find('.start_date').val(), localDateFormat);
                        var currentDate = new Date();
                        if (currentDate.getTime() < stDate.getTime()) {
                            var monthCount = stDate.getMonth() - new Date().getMonth();
                            var yearCount = stDate.getYear() - new Date().getYear();
                            $jppr.datepick._adjustDate('#' + $jppr(currentCalendar).find('.datePicker').attr('id'), +(monthCount + yearCount * 12), 'M');
                        }
                    }
                    if ($jppr(this).hasClass('readStartDate')) {
                        if ($jppr(currentCalendar).find('.readStartDate').val() != '') {
                            if ($jppr(currentCalendar).find('.readEndDate').val() != '') {
                                var endDate = Date.parseExact($jppr(currentCalendar).find('.readEndDate').val(), localDateFormat);
                            }
                            $jppr(currentCalendar).data('mousedown', 1);
                            $jppr(currentCalendar).data('selected', '');
                            if (typeof endDate != 'undefined') {
                                $jppr(currentCalendar).find('.datePicker').datepick('selectDateTd', stDate);
                                if (autoSelectEndDate == false) {
                                    $jppr(currentCalendar).find('.datePicker').datepick('selectDateTd', endDate);
                                }
                            }
                            $jppr(currentCalendar).data('mousedown', 0);
                            if(!showByDefault) {
                                $jppr(currentCalendar).find('.datePickerDiv').fadeIn("fast");
                            }
                        }
                    } else {
                        if ($jppr(currentCalendar).find('.readStartDate').val() != '') {
                            $jppr(currentCalendar).data('mousedown', 1);
                            $jppr(currentCalendar).data('selected', '');
                            $jppr(currentCalendar).find('.datePicker').datepick('selectDateTd', stDate);
                            $jppr(currentCalendar).data('mousedown', 0);
                            if(!showByDefault) {
                                $jppr(currentCalendar).find('.datePickerDiv').fadeIn("fast");
                            }
                        }
                    }
                }
            }
        });
        createCalendar();
    }
    if ($$('.shipMethods').length != 0 && $('shipping_method_select_box') != null && $('shipping_method_select_box').value == 'null' && $('zip_code').value == '') {
        $jppr(currentCalendar).find('.readStartDate,  .readEndDate').attr('disabled', true);
        if($jppr('.datesblock .calendarSelector .datesSelector').length == 0) {
            $jppr('.dateSelectedCalendar').hide();
            $jppr('.zip-select-panel').show();
        }
    }

    $jppr(currentCalendar).data('wait', 0);
    if(!useNonSequential) {
        if (typeof startDateInitial !== 'undefined' && startDateInitial != '' ) {
            if ($jppr(currentCalendar).find('.datePicker').length > 0) {
                if ($$('.shipMethods').length == 0 || (($('shipping_method_select_box') == null && $('shipping_method').value != '') || ($('shipping_method_select_box') != null && ($('shipping_method_select_box').value != 'null' || ($('shipping_method_select_box').value == 'null' && $('zip_code').value != ''))))) {
                    $jppr(currentCalendar).data('wait', 1);
                    var currentDate = new Date();
                    if (currentDate.getTime() < startDateInitial.getTime()) {
                        var monthCount = startDateInitial.getMonth() - new Date().getMonth();
                        var yearCount = startDateInitial.getYear() - new Date().getYear();
                        $jppr.datepick._adjustDate('#' + $jppr(currentCalendar).find('.datePicker').attr('id'), +(monthCount + yearCount * 12), 'M');
                    }

                    $jppr(currentCalendar).find('.datePicker').datepick('selectDateTd', startDateInitial);

                    if (autoSelectEndDate == false) {

                        if (endDateInitial && endDateInitial.getTime() - startDateInitial.getTime() >= minRentalPeriod * 1000) {
                            $jppr(currentCalendar).find('.datePicker').datepick('selectDateTd', endDateInitial);
                        }

                    }
                    $jppr(currentCalendar).data('wait', 0);
                }
            }
    }
    }

    if (startTimeInitial){
        $jppr(currentCalendar).data('wait', 1);

        $jppr(currentCalendar).find('.start_time').find('option').removeAttr("selected");
        $jppr(currentCalendar).find('.end_time').find('option').removeAttr("selected");
        $jppr(currentCalendar).find('.start_time option[value="' + startTimeInitial + '"]').attr("selected", "selected");
        $jppr(currentCalendar).find('.end_time option[value="' + endTimeInitial + '"]').attr("selected", "selected");

        $jppr(currentCalendar).data('wait', 0);
    }


    $jppr(currentCalendar).find('.btn-resfreshCalendar').click(function (event) {
        refreshCalendar();
    });
    if (!$jppr(currentCalendar).find('.datePicker').length) {
        $jppr(currentCalendar).find('.price-box').hide();
    }
    if(!showByDefault) {
        document.observe('click', function (e) {
            var el = $(e.target);
            var isDatePicker = false;
            while (el) {
                if (el !== window.document && el.hasAttribute('class') && el.hasClassName('datePickerDiv')) {
                    isDatePicker = true;
                    break;
                }
                if (el !== window.document && el.hasAttribute('class') && el.hasClassName('innerdpjppr')) {
                    isDatePicker = true;
                    break;
                }
                if (el !== window.document && el.hasAttribute('class') && el.hasClassName('readStartDate')) {
                    isDatePicker = true;
                    break;
                }
                if (el !== window.document && el.hasAttribute('class') && el.hasClassName('readEndDate')) {
                    isDatePicker = true;
                    break;
                }
                if (el !== window.document && el.hasAttribute('class') && el.hasClassName('ui-datepicker-next')) {
                    isDatePicker = true;
                    break;
                }
                if (el !== window.document && el.hasAttribute('class') && el.hasClassName('ui-datepicker-prev')) {
                    isDatePicker = true;
                    break;
                }
                el = el.parentNode;
            }
            if (!isDatePicker && $jppr(currentCalendar).attr('isenter') != 1) {
                $jppr(currentCalendar).find('.datePickerDiv').fadeOut('fast');
            }
        });
    }
    if(useTimes){
        if(showTimeGrid) {
            if (timeHeaderArCount> 0) {
                createBusyTime(false);

                $jppr(currentCalendar).find('.start_time,  .end_time').change(function (event) {
                    selectByRange($jppr(currentCalendar).find('.start_time').val(), $jppr(currentCalendar).find('.end_time').val());
                });

                $jppr(currentCalendar).find('[name=refreshCalendar]').click(function () {
                    createBusyTime(false);
                });
            }
        }

        $jppr(currentCalendar).find('.start_time,  .end_time').change(function (event) {
            updateInputVals($jppr(currentCalendar));
        });
    }

    $jppr(currentCalendar).find('.product-custom-option').change(function () {
        updateInputVals($jppr(currentCalendar));
    });

    $jppr('.super-attribute-select').change(function () {
        if(typeof updateBookedDates == 'function') {
            updateBookedDates($jppr(currentCalendar));
        }
        updateInputVals($jppr(currentCalendar));
    });

    $jppr('input[name^="bundle_option"], select[name^="bundle_option"], .bundle-option-select').change(function () {
        if(typeof updateBookedDates == 'function') {
            updateBookedDates($jppr(currentCalendar));
        }
        updateInputVals($jppr(currentCalendar));
    });

    $jppr('input[name^="options"]').change(function () {
        updateInputVals($jppr(currentCalendar));
    });

}
if(!isAdminSide) {
    $jppr(document).ready(function () {
        onDocReady();
    });
}else{
    //onDocReady();
}


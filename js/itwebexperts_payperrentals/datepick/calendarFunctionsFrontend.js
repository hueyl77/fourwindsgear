$jppr(document).ready(function () {
    //if ($jppr(currentCalendar).find('#qty').val() == 0) {
        $jppr(currentCalendar).find('#qty').val(minSaleQty);
    //}
    $jppr(currentCalendar).find('.qty-container').appendTo('.qty-move-container');
    $jppr(currentCalendar).find('.reservationCalendarDiv').insertBefore('#product-options-wrapper');
    $jppr(currentCalendar).find('#qty').keyup(function () {
        $jppr(currentCalendar).find('.btn-cart').each(function () {
            $(this).disabled = true;
        });
    });
    $jppr(currentCalendar).find('#qty').change(function () {
        updateBookedDates();
        updateInputVals();
    });

    $jppr(currentCalendar).find('.datePicker .datepick-inline').css('width', '100%');

    $jppr('#qty').trigger('change');
    updatePriceHtml(0, 0);
    $jppr(currentCalendar).find('.selectedDayRadio').click(function () {
        var val = $jppr(this).val();
        selectedToEndPeriod = (parseInt(val) - addStartingDateNumber);
        $jppr('.calendarSelector').attr('nrSel', (parseInt(val) - addStartingDateNumber + 1));
        if (selectedToStartPeriod != '0') {
            if ($jppr(currentCalendar).data('selected') == 'start') {
                if ($jppr(currentCalendar).find('.readStartDate').val() != '') {
                    $jppr(currentCalendar).data('wait', 1);
                    var startDate = $jppr(currentCalendar).find('.readStartDate').val();
                    startDate = Date.parseExact(startDate[0], localDateFormat);
                    $jppr(currentCalendar).find('.datePicker').datepick('selectDateTd', startDate);
                    $jppr(currentCalendar).data('wait', 0);
                }
            }
            $jppr(currentCalendar).find('.datePicker').datepick('selectDateTd', Date.parseExact(selectedToStartPeriod, 'yyyy-MM-dd'));
        }
    });
    if ($('shipping_method_select_box') == null || $('shipping_method_select_box').value != 'null' || ($('shipping_method_select_box').value == 'null' && $('zip_code').value != '')) {
        $jppr(currentCalendar).find('.selectedDayRadio:checked').trigger('click');
    }

    $jppr(currentCalendar).find('.btn-cart').wrap('<div class="over-btn" style="" />');
    $jppr(currentCalendar).find('.over-btn').css('float', $jppr(currentCalendar).find('.btn-cart:eq(0)').css('float'));
    $jppr(currentCalendar).find('.over-btn').css('display', $jppr(currentCalendar).find('.btn-cart:eq(0)').css('display'));
    $jppr(currentCalendar).find('.over-btn').css('margin', $jppr(currentCalendar).find('.btn-cart:eq(0)').css('margin'));

    $jppr(currentCalendar).find('.btn-cart-bqueue, .btn-cart-buyout').each(function(){
        $jppr(this).css('float', $jppr(currentCalendar).find('.over-btn').css('float'));
        $jppr(this).css('display', $jppr(currentCalendar).find('.over-btn').css('display'));
        $jppr(this).css('width', $jppr(currentCalendar).find('.over-btn').css('width'));
        $jppr(this).css('margin', $jppr(currentCalendar).find('.over-btn').css('margin'));
    });
    $jppr(currentCalendar).find('.btn-cart-bqueue, .btn-cart-buyout').css('margin-left', '5px');
    $jppr(currentCalendar).find('.btn-cart-bqueue').css('width', ($jppr(currentCalendar).find('.btn-cart-bqueue').outerWidth()+20)+'px');

    $jppr(currentCalendar).find('.over-btn').mousedown(function () {
        if(typeof Modernizr != 'undefined' && (Modernizr.csstransforms || Modernizr.csstransforms3d)){
            Modernizr.csstransforms = null;
            Modernizr.csstransforms3d = null;
        }
        if(useGlobals) {
            $jppr(currentCalendar).find('.btn-cart').removeAttr('disabled');
        }

        if ($jppr(currentCalendar).find('.btn-cart[disabled]').length > 0) {
            if ($jppr(currentCalendar).find('.readStartDate').val() == '' || $jppr(currentCalendar).find('.readEndDate').val() == '') {
                alert(pleaseSelectMessage);
            } else {
                alert(noPriceMessage);
            }
        }
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

    $jppr(currentCalendar).find('input[name^="bundle_option_qty"]').each(function () {
        qtyDefaultArr[$jppr(this).attr('id')] = $jppr(this).val();
    });

    if (isCheckoutPage) {
        //$jppr('.reservationCalendarDiv').hide();
        var htmlBut = $jppr('<div style="display: inline-block;vertical-align: middle;padding-top: 5px;" class="nearCalendar"><div class="calendar-button-container" style="display: inline-block;margin-right: 10px;">' +
            '<button id="btn-update-global-dates" type="submit" class="button">' +
            '<span>' +
            '<span>' + confirmDatesText + '</span>' +
            '</span>' +
            '</button>' +
            '</div> <div class="datesShow"></div>' +
            '<div class="errorShow"></div></div>');
        $jppr(currentCalendar).wrapInner('<form action="' + urlUpdateDates + '" method="post" id="formWrap"></form>');
        $jppr('#formWrap').append(htmlBut);
        $jppr(currentCalendar).find(' .btn-resfreshCalendar').parent().appendTo($jppr(currentCalendar).find(' .nearCalendar'));
        $jppr(currentCalendar).find(' .dateSelectedCalendar').css('display', 'inline-block');
        $jppr(currentCalendar).find(' .dateSelectedCalendar').css('width', '450px');
        $jppr(currentCalendar).find(' .dateSelectedCalendar').css('vertical-align', 'middle');
        $jppr(currentCalendar).find(' .dateView').css('width', '100%');
    }
});

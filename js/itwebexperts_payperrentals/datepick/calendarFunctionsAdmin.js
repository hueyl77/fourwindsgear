//functions used only when configure popup is shown
$jppr('#product_composite_configure_fields_qty').css('margin-top', '15px');

$calendarTableObject.find('input[name="qty"]').first().val(1);

$calendarTableObject.find('input[name="qty"]').first().keydown(function (event) {
    $jppr('.buttons-set button[type="submit"]').hide();
});
$calendarTableObject.find('input[name="qty"]').first().change(function (event) {
    $jppr('.buttons-set button[type="submit"]').hide();
    if ($calendarTableObject.find('.qtycheck').length == 0) {
        $jppr('<dt style="margin-top: 10px"><div class="form-button qtycheck" style="width:160px;"><span><span>Check Price and availability</span></span></div></dt>').insertAfter($calendarTableObject.find('input[name="qty"]').first().parent());
    }
    $calendarTableObject.find('.qtycheck').click(function (event) {
        updateInputVals();
        event.stopImmediatePropagation();
    });
    $calendarTableObject.find('.qtycheck').trigger('click');
    event.stopImmediatePropagation();
});

$jppr('.super-attribute-select').change(function () {
    updateInputVals();
});

$jppr('input[name^="bundle_option"], select[name^="bundle_option"], .bundle-option-select').change(function () {
    updateInputVals();
});

$jppr('input[name^="options"]').change(function () {
    updateInputVals();
});

if(startTimeInitial != '') {
    $calendarTableObjectChild.find('.start_time option[value="'+startTimeInitial+'"]').attr("selected", "selected");
    $calendarTableObjectChild.find('.end_time option[value="'+endTimeInitial+'"]').attr("selected", "selected");
}


/** Initialize keydown event for qty field */
$calendarTableObject.find('input[name="qty"]').first().trigger('keydown');

/**
 * Function used when change all button is pushed to update all dates in an order
 */
function updateInputValsAll() {
    Element.show('loading-mask');
    $jppr.ajax({
        cache: false,
        dataType: 'json',
        type: 'post',
        url: updateAllDatesUrl,
        data: $jppr('#topDates').find('*').serialize(),
        success: function (data) {
        Element.hide('loading-mask');
        if (data.itemId) {
            var fields = [];
            $jppr.each(data.itemId, function (key1, item) {
                fields.push(new Element('input', {type: 'hidden', name: 'item[' + item + '][configured]', value: 1}));
                $jppr.each(data.itemConfigs[item], function (keyConfig, itemConfig) {

                    if (Object.prototype.toString.call(itemConfig) === '[object Object]') {
                        for (var key in itemConfig) {
                            if(Object.prototype.toString.call(itemConfig[key]) === '[object Array]' || Object.prototype.toString.call(itemConfig[key]) === '[object Object]') {
                                $jppr.each(itemConfig[key], function(bpKey, bpItem){
                                    fields.push(new Element('input', {type: 'hidden', name: 'item[' + item + '][' + keyConfig + '][' + key + '][' + bpKey + ']', value: bpItem}));
                                });
                            } else {
                                fields.push(new Element('input', {type: 'hidden', name: 'item[' + item + '][' + keyConfig + '][' + key + ']', value: itemConfig[key]}));
                            }
                        }
                    } else {
                        fields.push(new Element('input', {type: 'hidden', name: 'item[' + item + '][' + keyConfig + ']', value: itemConfig}));
                    }
                });
            });
            productConfigure.addFields(fields);
            order.itemsUpdate();
        }
        order.loadArea(['items'], true);
    }}
);
}

$jppr('#select-new-customer').change(function () {
    Element.show('loading-mask');
    $jppr.ajax({
        cache: false,
        dataType: 'json',
        type: 'post',
        url: selectNewCustomerUrl,
        data: 'customer_id=' + $jppr('#select-new-customer').val(),
        success: function (data) {
        Element.hide('loading-mask');
        order.customerId = $jppr('#select-new-customer').val();
        order.loadArea(['header', 'shipping_method', 'billing_method', 'totals','search', 'items', 'giftmessage', 'shipping_address', 'billing_address', 'data'], true);
    }
});
});

if(startTimeInitial) {
    $jppr('#topDates').find('.start_time option[value="' + startTimeInitial + '"]').attr("selected", "selected");
    $jppr('#topDates').find('.end_time option[value="' + endTimeInitial + '"]').attr("selected", "selected");
}

/**
 * Function used to show the qty avail/price/etc when popup configuration window appears in admin
 * @param itemId
 * @param wind
 */

function calculatePriceGeneral(itemId, wind) {
    var needConfigure = ($jppr('a[product_id="' + itemId + '"]').parent().parent().attr('is_pressed') == '0');

    if (wind && needConfigure) {
        wind._showWindow();
    }
}

ProductConfigure.prototype._requestItemConfiguration = ProductConfigure.prototype._requestItemConfiguration.wrap(function (parentMethod, listType, itemId) {
        if (!this.listTypes[listType].urlFetch) {
            return false;
        }
        var url = this.listTypes[listType].urlFetch;
        if (url) {
            new Ajax.Request(url, {
                parameters: {id: itemId},
                onSuccess: function (transport) {
                    var response = transport.responseText;
                    if (response.isJSON()) {
                        response = response.evalJSON();
                        if (response.error) {
                            this.blockMsg.show();
                            this.blockMsgError.innerHTML = response.message;
                            this.blockCancelBtn.hide();
                            this.setConfirmCallback(listType, null);
                            if (listType == 'product_to_add') {
                                calculatePriceGeneral(itemId, this);//todo check if we can replace this call with some data attribute from getprice and availability
                            } else {
                                this._showWindow();
                            }

                        }
                    } else if (response) {
                        response = response + '';
                        this.blockFormFields.update(response);

                        // Add special div to hold mage data, e.g. scripts to execute on every popup show
                        var mageData = {};
                        var scripts = response.extractScripts();
                        mageData.scripts = scripts;

                        var scriptHolder = new Element('div', {'style': 'display:none'});
                        scriptHolder.mageData = mageData;
                        this.blockFormFields.insert(scriptHolder);
                        if (listType == 'product_to_add') {
                            calculatePriceGeneral(itemId, this);
                        } else {
                            this._showWindow();
                        }

                    }
                }.bind(this)
            });
        }

    }
);

AdminOrder.prototype.accountGroupChange = AdminOrder.prototype.accountGroupChange.wrap(function (parentMethod) {
    this.loadArea(['data, search'], true, this.serializeData('order-form_account').toObject());
});
AdminOrder.prototype.showQuoteItemConfiguration = AdminOrder.prototype.showQuoteItemConfiguration.wrap(function(parentMethod, itemId){
    var listType = 'quote_items';
    var qtyElement = $('order-items_grid').select('input[name="item\['+itemId+'\]\[qty\]"]')[0];
    productConfigure.setConfirmCallback(listType, function() {
        // sync qty of popup and qty of grid
        var confirmedCurrentQty = productConfigure.getCurrentConfirmedQtyElement();
        if (qtyElement && confirmedCurrentQty && !isNaN(confirmedCurrentQty.value)) {
            qtyElement.value = confirmedCurrentQty.value;
        }
        this.productConfigureAddFields['item['+itemId+'][configured]'] = 1;
        this.itemsUpdate();//
    }.bind(this));
    productConfigure.setShowWindowCallback(listType, function() {
        // sync qty of grid and qty of popup
        var formCurrentQty = productConfigure.getCurrentFormQtyElement();
        if (formCurrentQty && qtyElement && !isNaN(qtyElement.value)) {
            formCurrentQty.value = qtyElement.value;
        }
    }.bind(this));
    productConfigure.showItemConfiguration(listType, itemId);
});
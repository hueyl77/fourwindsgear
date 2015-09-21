
casper.changeGlobalDates = function (test, currentStartTime, currentEndTime, startTimeValue, endTimeValue){
    casper.then(function () {
        console.log('ddddd');

        this.wait(5000, function () {

            this.waitForResource(function testResource(resource) {
                if (resource.url.indexOf("data?isAjax=true") > 0) {
                    console.log("Returned status code " + resource.status);
                    return true;
                }
                return false;
            }, function onReceived() {
                //this.capture('/home/magt/public_html/rent14/captures/g1.png');
                startDate = toFormattedDate(currentStartTime);
                endDate = toFormattedDate(currentEndTime);

                test.info('Change Global Date: Start Date: ' + startDate + ' End Date: ' + endDate);


                var retValueStart = this.evaluate(function (currentStartTime) {
                    var startTime = new Date(currentStartTime);
                    $jppr('.datePicker').datepick('selectDateTd', startTime);
                    return $jppr('.readStartDate').val();

                }, currentStartTime);

                test.info('Filling start date input');
                test.assertEqual(startDate, retValueStart);

                var retValueEnd = this.evaluate(function (currentEndTime) {
                    var endTime = new Date(currentEndTime);
                    $jppr('.datePicker').datepick('selectDateTd', endTime);
                    return $jppr('.readEndDate').val();
                }, currentEndTime);

                test.info('Filling end date input');
                test.assertEqual(endDate, retValueEnd);

                this.wait(5000, function () {
                    this.evaluate(function () {
                        $jppr('.head-sales-order').html('Waiting Action');
                        return true;
                    });
                    this.waitForResource(function testResource(resource) {
                        if (resource.url.indexOf("updateinitials") > 0) {
                            console.log("Returned status code " + resource.status);
                            return true;
                        }
                        return false;
                    }, function onReceived() {
                        this.evaluate(function () {
                            $jppr('.head-sales-order').html('Finish');
                            return true;
                        });

                        test.info('Global dates Updated');

                    });

                });
            });
        });
    });
};


casper.addProductFromGrid = function (test, qty){
    casper.then(function () {

        this.waitFor(function check(){
            return this.evaluate(function () {
                return  $jppr('.head-sales-order').html() == 'Ready To Add In Grid';
            });
        }, function then(){
            test.info('Start add product');
            var numberOfItemsInGrid = this.evaluate(function () {
                if($jppr('#order-items_grid table tbody tr:eq(0) td').length == 1){
                    return 0;
                }else {
                    return $jppr('#order-items_grid table tbody tr').length;
                }
            });

            this.evaluate(function (qty) {
                $jppr('#sales_order_create_search_grid_table tbody tr:eq(0) td:eq(6) .qty').val(qty);
                order.productGridAddSelected();
                return true;
            }, qty);
            this.wait(8000, function() {
                this.evaluate(function () {
                    $jppr('.head-sales-order').html('Waiting Action');
                    return true;
                });
                this.waitForResource(function testResource(resource) {
                    if (resource.url.indexOf("loadBlock") > 0) {
                        console.log("Returned status code " + resource.status);
                        return true;
                    }
                    return false;
                }, function onReceived() {
                    this.evaluate(function () {
                        $jppr('.head-sales-order').html('Ready to Configure In Cart');
                        return true;
                    });

                    test.info('Check Product added to cart');
                    var checkGridTableProducts = this.evaluate(function(){
                        if($jppr('#order-items_grid table tbody tr:eq(0) td').length == 1){
                            return 0;
                        }
                        return $jppr('#order-items_grid table tbody tr').length;
                    });
                    test.assertEqual((parseInt(numberOfItemsInGrid)+1), parseInt(checkGridTableProducts));
                });

            });
        });
    });
};



casper.changeConfigure = function(test, currentStartTime, currentEndTime, startTimeValue, endTimeValue, qty, expected,isCart){
   casper.then(function(){
       this.wait(5000, function () {

           this.waitForResource(function testResource(resource) {
               if (resource.url.indexOf("getpriceandavailability") > 0) {
                   console.log("Returned status code " + resource.status);
                   return true;
               }
               return false;
           }, function onReceived() {
               test.info('Start check prices in configure grid for the previous selected product with checkPricesInGrid test.');
               startDate = toFormattedDate(currentStartTime);
               endDate = toFormattedDate(currentEndTime);

               test.info('Change Date in configure window: Start Date: ' + startDate + ' End Date: ' + endDate);

               test.info('Change Qty');

               this.evaluate(function (qty) {
                   $jppr('#product_composite_configure_form .datesPrice .price').html('');
                   $jppr('#product_composite_configure_form #product_composite_configure_input_qty').val(qty);
                   return true;
               }, qty);

               var retValueStart = this.evaluate(function (currentStartTime) {
                   $jppr('#product_composite_configure_form .datesPrice .price').html('');
                   var startTime = new Date(currentStartTime);
                   $jppr('#product_composite_configure_form .datePicker').datepick('selectDateTd', startTime);
                   return $jppr('#product_composite_configure_form .readStartDate').val();

               }, currentStartTime);

               test.info('Filling start date input');
               test.assertEqual(startDate, retValueStart);

               var retValueEnd = this.evaluate(function (currentEndTime) {
                   $jppr('#product_composite_configure_form .datesPrice .price').html('');
                   var endTime = new Date(currentEndTime);
                   $jppr('#product_composite_configure_form .datePicker').datepick('selectDateTd', endTime);
                   return $jppr('#product_composite_configure_form .readEndDate').val();
               }, currentEndTime);

               test.info('Filling end date input');
               test.assertEqual(endDate, retValueEnd);

               this.waitFor(function check() {
                   return this.evaluate(function () {
                       return $jppr('#product_composite_configure_form .datesPrice .price').html() != '';
                   });
               }, function then() {

                   test.info('Configure dates Updated');
                   var number = this.evaluate(function () {
                       return Number($jppr('#product_composite_configure_form .datesPrice .price').text().replace(/[^0-9\.]+/g, ""));
                   });

                   test.info('Check Pricing');
                   test.assertEqual(number, parseFloat(expected));
                   this.evaluate(function (isCart) {
                       $jppr('.head-sales-order').html('Ready to Ok'+isCart);
                       return true;
                   }, isCart);

               });

           });
       });
});
};

casper.configureInCart = function(test, pos, currentStartTime, currentEndTime, startTimeValue, endTimeValue, qty, expectedPrice, expectedTotal, expected){
    casper.then(function () {
        this.waitFor(function check() {
            return this.evaluate(function () {
                return $jppr('.head-sales-order').html() == 'Ready to Configure In Cart';
            });
        }, function then() {
            //this.capture('/home/magt/public_html/rent14/captures/g4.png');
            this.evaluate(function (pos) {
                eval($jppr('#order-items_grid table tbody tr:eq('+pos+') td:eq(0) .configureImg').attr('onclick'));
                return true;
            }, pos);
            this.wait(5000, function () {

                this.waitForResource(function testResource(resource) {
                    if (resource.url.indexOf("configureQuoteItems") > 0) {
                        console.log("Returned status code " + resource.status);
                        return true;
                    }
                    return false;
                }, function onReceived() {
                    casper.changeConfigure(test, currentStartTime, currentEndTime, qty, expectedTotal,' In Cart');
                    this.capture('D:/Projects/rfq14/.modman/rental/js/itwebexperts_payperrentals/casperjs/captures/g2.png');
                    this.waitFor(function check() {
                        return this.evaluate(function () {
                            return $jppr('.head-sales-order').html() == 'Ready to Ok In Cart';
                        });
                    }, function then() {
                        test.info('Update product from configure');
                        this.wait(3000, function () {
                            this.evaluate(function () {
                                $jppr('#product_composite_configure_form .buttons-set').find('button[type="submit"]').trigger('click');
                                return true;
                            });
                        });
                        this.wait(5000, function () {

                            this.waitForResource(function testResource(resource) {
                                if (resource.url.indexOf("showUpdateResult") > 0) {
                                    console.log("Returned status code " + resource.status);
                                    return true;
                                }
                                return false;
                            }, function onReceived() {

                                //this.capture('/home/magt/public_html/rent14/captures/g2.png');

                                var number = this.evaluate(function (pos) {
                                    return Number($jppr('#order-items_grid table tbody tr:eq('+pos+') td:eq(4) span.price').text().replace(/[^0-9\.]+/g, ""));
                                }, pos);

                                test.info('Check Price for one item in cart');
                                test.assertEqual(number, parseFloat(expectedPrice));


                                var numberQty = this.evaluate(function (pos) {
                                    return Number($jppr('#order-items_grid table tbody tr:eq('+pos+') td:eq(5) .item-qty').val().replace(/[^0-9\.]+/g, ""));
                                }, pos);

                                test.info('Check Qty for one item in cart');
                                test.assertEqual(numberQty, parseInt(qty));

                                var numberSubtotal = this.evaluate(function (pos) {
                                    return Number($jppr('#order-items_grid table tbody tr:eq('+pos+') td:eq(6) span.price').text().replace(/[^0-9\.]+/g, ""));
                                }, pos);

                                test.info('Check Subtotal for one item in cart');
                                test.assertEqual(numberSubtotal, parseFloat(expectedTotal));

                                this.evaluate(function () {
                                    $jppr('.head-sales-order').html('Finish Added To Cart');
                                    return true;
                                });
                            });
                        });

                    });
                });
            });
        });
    });
};
casper.checkPricesInConfigureGrid = function (test, currentStartTime, currentEndTime, startTimeValue, endTimeValue, qty, expected){

    casper.then(function () {
        this.waitFor(function check() {
            return this.evaluate(function () {
                return $jppr('.head-sales-order').html() == 'Ready To Add In Grid';
            });
        }, function then() {
            this.evaluate(function () {
                $jppr('#product_composite_configure_form .datesPrice .price').html('');
                /**
                 * Dispatching a click event on the search grid doesn't work with jQuery so I had to actually dispatch the event with javascript
                 * */

                var myInnerLink = 'myInnerLink' + $jppr('#sales_order_create_search_grid_table tbody tr:eq(0) td:eq(1) a').attr('list_type') + '' + $jppr('#sales_order_create_search_grid_table tbody tr:eq(0) td:eq(1) a').attr('product_id');
                $jppr('#sales_order_create_search_grid_table tbody tr:eq(0) td:eq(1) a').attr('id', myInnerLink);
                var myEvt = document.createEvent('MouseEvents');
                myEvt.initEvent(
                    'click'      // event type
                    , true      // can bubble?
                    , true      // cancelable?
                );
                document.getElementById(myInnerLink).dispatchEvent(myEvt);
            });
            this.waitFor(function check() {
                return this.evaluate(function () {
                    return (($jppr('#product_composite_configure_form .datesPrice .price').length > 0) && $jppr('#product_composite_configure_form .datesPrice .price').html() != '') && $jppr('#product_composite_configure_form .calendarSelector').length > 0;
                });
            }, function then() {
                casper.changeConfigure(test, currentStartTime, currentEndTime, startTimeValue, endTimeValue, qty, expected,'');
            });
        });
    });
};
casper.checkPricesInGridAfterConfigure = function (test, expected) {
    casper.then(function () {
        this.waitFor(function check(){
            return this.evaluate(function () {
                return  $jppr('.head-sales-order').html() == 'Ready to Ok';
            });
        }, function then() {
            test.info('Check Price in Grid After Configure');
            this.evaluate(function () {
                $jppr('#sales_order_create_search_grid_table tbody tr:eq(0) td:eq(4)').html('');
                $jppr('#product_composite_configure_form .buttons-set').find('button[type="submit"]').trigger('click');

                return true;
            });

            this.waitFor(function check(){
                return this.evaluate(function () {
                    return $jppr('#sales_order_create_search_grid_table tbody tr:eq(0) td:eq(4)').html() != '';
                });
            }, function then(){
                var number = this.evaluate(function(){
                    return Number( $jppr('#sales_order_create_search_grid_table tbody tr:eq(0) td:eq(4)').text().replace(/[^0-9\.]+/g,""));
                });
                this.evaluate(function () {
                    $jppr('.head-sales-order').html('Ready To Add In Grid');
                    return true;
                });
                test.info('Test pricing for selected dates:');
                test.assertEqual(number, parseFloat(expected));

            });
        });
    });
};


casper.checkPricesInGrid = function (test, product, expected){
    casper.then(function () {

        this.waitFor(function check(){
            return this.evaluate(function () {
                return  $jppr('.head-sales-order').html() == 'Finish';
            });
        }, function then(){
            test.info('Start check prices in grid.'+product);

            //click AddProducts button
            this.evaluate(function (product) {
                $jppr('#quick-add-container').next().find('.button').trigger('click');
                $jppr('#sales_order_create_search_grid_filter_name').val(product);
                sales_order_create_search_gridJsObject.doFilter();
                return true;
            }, product);
            //this.waitUntilVisible('order-search',function(){

            //});
            this.wait(5000, function() {
                this.evaluate(function () {
                    $jppr('.head-sales-order').html('Waiting Action Search');
                    return true;
                });
                this.waitForResource(function testResource(resource) {
                    if (resource.url.indexOf("loadBlock") > 0) {
                        console.log("Returned status code " + resource.status);
                        return true;
                    }
                    return false;
                }, function onReceived() {
                    this.evaluate(function () {
                        $jppr('.head-sales-order').html('Finish Search');
                        return true;
                    });

                    test.info('Search Completed.Click on row');
                    this.evaluate(function () {
                        $jppr('#sales_order_create_search_grid_table tbody tr:eq(0) td:eq(4)').html('');
                        $jppr('#sales_order_create_search_grid_table tbody tr:eq(0) td:eq(5)').trigger('click');
                        return true;
                    });
                    this.capture('D:/Projects/rfq14/.modman/rental/js/itwebexperts_payperrentals/casperjs/captures/g1.png');
                    this.waitFor(function check(){
                        return this.evaluate(function () {
                            return $jppr('#sales_order_create_search_grid_table tbody tr:eq(0) td:eq(4)').html() != '';
                        });
                    }, function then(){
                        this.evaluate(function () {
                            $jppr('.head-sales-order').html('Ready To Add In Grid');
                            return true;
                        });
                        var number = this.evaluate(function(){
                            return Number( $jppr('#sales_order_create_search_grid_table tbody tr:eq(0) td:eq(4)').text().replace(/[^0-9\.]+/g,""));
                        });
                        test.info('Test pricing for selected dates:');
                        test.assertEqual(number, parseFloat(expected));

                    });


                });

            });

        });
    });
};

casper.createOrderAdmin = function (test){
    casper.then(function () {

        this.waitFor(function check(){
            return this.evaluate(function () {
                return  $jppr('.head-sales-order').html() == 'Finish Added To Cart';
            });
        }, function then() {
            test.info('Start create order');
            this.evaluate(function () {
                $jppr('#p_method_checkmo').trigger('click');
                return  true;
            });
            test.info('Payment Method Selected');
            this.wait(5000, function() {
                this.evaluate(function () {
                    $jppr('.head-sales-order').html('Waiting Action Payment');
                    return true;
                });
                this.waitForResource(function testResource(resource) {
                    if (resource.url.indexOf("loadBlock") > 0) {
                        console.log("Returned status code " + resource.status);
                        return true;
                    }
                    return false;
                }, function onReceived() {
                    this.evaluate(function () {
                        $jppr('.head-sales-order').html('Finish Action Payment');
                        return true;
                    });
                    this.evaluate(function () {
                        order.loadShippingRates();
                        return  true;
                    });
                    test.info('Get Shipping Methods');
                    this.wait(5000, function() {
                        this.evaluate(function () {
                            $jppr('.head-sales-order').html('Waiting Action Shipping');
                            return true;
                        });
                        this.waitForResource(function testResource(resource) {
                            if (resource.url.indexOf("loadBlock") > 0) {
                                console.log("Returned status code " + resource.status);
                                return true;
                            }
                            return false;
                        }, function onReceived() {
                            this.evaluate(function () {
                                $jppr('.head-sales-order').html('Finish Action Shipping');
                                return true;
                            });
                            this.evaluate(function () {
                                //$jppr('#s_method_flatrate_flatrate').trigger('click');
                                order.setShippingMethod('flatrate_flatrate');

                                return  true;
                            });
                            test.info('Shipping Method Selected');
                            this.wait(5000, function() {
                                this.evaluate(function () {
                                    $jppr('.head-sales-order').html('Waiting Action Shipping Setup');
                                    return true;
                                });
                                this.waitForResource(function testResource(resource) {
                                    if (resource.url.indexOf("loadBlock") > 0) {
                                        console.log("Returned status code " + resource.status);
                                        return true;
                                    }
                                    return false;
                                }, function onReceived() {
                                    this.evaluate(function () {
                                        $jppr('.head-sales-order').html('Finish Action Shipping Setup');
                                        return true;
                                    });

                                    this.evaluate(function () {
                                        order.submit();
                                        return  true;
                                    });
                                    //captures path
                                    this.capture('/home/magt/public_html/captures/createOrderAfterSubmit.png');
                                    this.waitForUrl(/sales_order\/view/, function () {
                                        test.info('Order Created');
                                        var reg = new RegExp(toRegularExp(mage.getBaseUrl() + 'admin/sales_order/view/') + '.*$');
                                        test.assertUrlMatch(reg);
                                    });
                                });
                            });
                        });
                    });
                });
            });
        });
    });
};



casper.startCreateNewOrderAdmin = function(test) {
    casper.then(function () {
        this.page.injectJs('js/itwebexperts_payperrentals/jquery/jquery-1.8.3-tests.min.js');
        this.evaluate(function () {
            window.location.href = $jppr('#nav li:eq(1) ul li:eq(0) a').attr('href');//sales order link
            return true;
        });

        this.waitForUrl(/sales_order\/index/, function () {

            var reg = new RegExp(toRegularExp(mage.getBaseUrl() + 'admin/sales_order/index/') + '.*$');
            test.assertUrlMatch(reg);
            this.page.injectJs('js/itwebexperts_payperrentals/jquery/jquery-1.8.3-tests.min.js');
            this.evaluate(function () {
                //click create new order
                $jppr('.head-sales-order').remove();
                $jppr('button[title="Create New Order"]').click();
                return true;
            });

            this.waitForSelector('.head-sales-order', function () {
                this.page.injectJs('js/itwebexperts_payperrentals/jquery/jquery-1.8.3-tests.min.js');
                this.evaluate(function () {
                    var myTRLink = 'myInnerLinkTr';
                    $jppr('#sales_order_create_customer_grid_table tbody tr:eq(0)').attr('id',myTRLink);
                    var myEvtTr = document.createEvent('MouseEvents');
                    myEvtTr.initEvent(
                        'click'      // event type
                        ,true      // can bubble?
                        ,true      // cancelable?
                    );
                    document.getElementById(myTRLink).dispatchEvent(myEvtTr);
                    //$jppr('#sales_order_create_customer_grid_table tbody tr:eq(0)').trigger('click');
                    return true;
                });

            });
        });
    });
};


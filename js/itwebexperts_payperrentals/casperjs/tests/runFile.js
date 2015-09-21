casper.test.begin('Run tests', function(test) {
    casper.start(mage.getBaseUrl());
    for(testC=0; testC<testArray.length; testC++)
    {
        switch (testArray[testC].testName){
            case 'checkPrices':
                casper.checkPrices(test, testArray[testC].url, testArray[testC].startDate, testArray[testC].endDate, testArray[testC].startTime, testArray[testC].endTime, testArray[testC].optionsArr, testArray[testC].qty, testArray[testC].expected);
                break;
            case 'checkBlackoutDates':
                casper.checkBlackoutDates(test, testArray[testC].url, testArray[testC].startDate, testArray[testC].endDate, testArray[testC].startTime, testArray[testC].endTime, testArray[testC].optionsArr, testArray[testC].qty, testArray[testC].expected);
                break;
            case 'addToCart':
                casper.addToCart(test, testArray[testC].url, testArray[testC].startDate, testArray[testC].endDate, testArray[testC].startTime, testArray[testC].endTime, testArray[testC].optionsArr, testArray[testC].qty, testArray[testC].buyout, testArray[testC].expected);
                break;
            case 'updateCart':
                casper.updateCart(test, testArray[testC].qty, testArray[testC].pos, testArray[testC].expected);
                break;
            case 'removeFromCart':
                casper.removeFromCart(test, testArray[testC].pos, testArray[testC].expected);
                break;
            case 'changeSettingsGeneral':
                casper.changeSettingsGeneral(test, testArray[testC].section, testArray[testC].group, testArray[testC].field, testArray[testC].value);
                break;
            case 'changeSettingsProduct':
                casper.changeSettingsProduct(test, testArray[testC].sku, testArray[testC].attribute, testArray[testC].value);
                break;
            case 'loginAdmin':
                casper.loginAdmin(test);
                break;
            case 'openAdmin':
                casper.openAdmin(test);
                break;
            case 'deleteAllOrders':
                casper.deleteAllOrders(test);
                break;
            case 'checkoutCart':
                casper.checkoutCart(test);
                break;
            case 'registerCustomer':
                casper.registerCustomer(test);
                break;
            case 'loginCustomer':
                casper.loginCustomer(test);
                break;
            case 'checkInventoryReport':
                casper.checkInventoryReport(test, testArray[testC].product, testArray[testC].startDate, testArray[testC].expected);
                break;
            case 'startCreateNewOrderAdmin':
                casper.startCreateNewOrderAdmin(test);
                break;
            case 'changeGlobalDates':
                casper.changeGlobalDates(test, testArray[testC].startDate, testArray[testC].endDate, testArray[testC].startTime, testArray[testC].endTime, testArray[testC].optionsArr);
                break;
            case 'addProductFromGrid':
                casper.addProductFromGrid(test, testArray[testC].qty);
                break;
            case 'checkPricesInGrid':
                casper.checkPricesInGrid(test, testArray[testC].product, testArray[testC].expected);
                break;
            case 'checkPricesInGridAfterConfigure':
                casper.checkPricesInGridAfterConfigure(test, testArray[testC].expected);
                break;
            case 'checkPricesInConfigureGrid':
                casper.checkPricesInConfigureGrid(test, testArray[testC].startDate, testArray[testC].endDate, testArray[testC].startTime, testArray[testC].endTime, testArray[testC].optionsArr, testArray[testC].qty, testArray[testC].expected);
                break;
            case 'configureInCart':
                casper.configureInCart(test, testArray[testC].pos, testArray[testC].startDate, testArray[testC].endDate, testArray[testC].startTime, testArray[testC].endTime, testArray[testC].optionsArr, testArray[testC].qty, testArray[testC].expectedPrice, testArray[testC].expectedTotal, testArray[testC].expected);
                break;
            case 'createOrderAdmin':
                casper.createOrderAdmin(test);
                break;
            default:
                break;
        }

    }

    casper.run(function() {test.done();});
});
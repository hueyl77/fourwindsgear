// Configuration and some usefull methods

/**
 * Debug/Verbose
 * ----------------------------------------------------------------------------
 */
var debug_mode = !!casper.cli.get('verbose');
if (debug_mode) {
    debug_mode = true;
    casper.options.verbose = true;
    casper.options.logLevel = 'debug';
}

var colorizer = require('colorizer').create('Colorizer');

/**
 * The view
 * ----------------------------------------------------------------------------
 */

// The viewport size
casper.options.viewportSize = {
    width: 1200,
    height: 900
};


casper.options.viewportSize = {width: 1920, height: 640};
//casper.options.remoteScripts.push('https://code.jquery.com/jquery-1.11.2.min.js');
casper.options.waitTimeout = 50000;

/**
 * This function should return the formatted date based on store locale
 * @param currentStartTime
 * @returns {string}
 */

function toFormattedDate(currentStartTime){
    var monthStart = currentStartTime.getMonth() + 1;
    var dayStart = currentStartTime.getDate();
    var yearStart = currentStartTime.getFullYear();
    return monthStart + '/' + dayStart + '/' + yearStart;
}

function toDbDate(currentStartTime){
    var monthStart = currentStartTime.getMonth() + 1;
    if(monthStart < 10){
        monthStart = '0'+monthStart;
    }
    var dayStart = currentStartTime.getDate();
    if(dayStart < 10){
        dayStart = '0'+dayStart
    }
    var yearStart = currentStartTime.getFullYear();
    return yearStart + '-' + monthStart + '-' + dayStart;
}

function toRegularExp(str){
   // var regExp = new RegExp(str);
    //regExp = regExp.toString();
    //return regExp.substring(1, regExp.length - 1);
    return str;
}

/**
 * The HTTP responses
 * ----------------------------------------------------------------------------
 */
casper.options.httpStatusHandlers = {
    200:  function(self, resource) {
        this.echo(resource.url + " is OK (200)", "INFO");
    },
    400:  function(self, resource) {
        this.echo(resource.url + " is nok (400)", "INFO");
    },
    404: function(self, resource) {
        this.echo("Resource at " + resource.url + " not found (404)", "COMMENT");
    },
    302: function(self, resource) {
        this.echo(resource.url + " has been redirected (302)", "INFO");
    }
};

/**
 * Utils, XPath, FileSystem
 * ----------------------------------------------------------------------------
 */
var utils   = require('utils');
var x       = casper.selectXPath;
var fs = require('fs'),
    system = require('system');

/**
 * URLs
 * ----------------------------------------------------------------------------
 */
var url = casper.cli.get("url");
if(undefined === url){
    url = 'http://magt.sidev.info/rent19/index.php/';
}
if (!/\/$/.test(url)) {
    // We haven't trailing slash: add it
    url = url + '/';
}

var secure_url = casper.cli.get('secure_url');
if (undefined === secure_url) {
    // Secure URL isn't defined, we get the unsecure one instead
    secure_url = url;
} else if (!/\/$/.test(secure_url)) {
    // We haven't trailing slash: add it
    secure_url = secure_url + '/';
}

var admin_url = casper.cli.get('admin_url');
if (undefined === admin_url) {
    // Admin URL is secured by default, if not, specify the command line option
    admin_url = secure_url + 'admin/';
} else if (!/\/$/.test(admin_url)) {
    // We haven't trailing slash: add it
    admin_url = admin_url + '/';
}

checkoutUrl = secure_url + 'checkout/cart/';

/*End urls*/

/*Read test file and create testArray object*/
var inputFile = casper.cli.get('file');
var content = '',
    f = null,
    lines = null,
    eol = system.os.name == 'windows' ? "\r\n" : "\n";

try {
    f = fs.open(inputFile, "r");
    content = f.read();
} catch (e) {
    console.log(e);
}

if (f) {
    f.close();
}
var testArray = [];
if (content) {
    lines = content.split(eol);
    for (var i = 0, len = lines.length; i < len; i++) {
        //console.log(lines[i]);
        if(lines[i].charAt(0) !== '#') {
            arguments = lines[i].split(' --- ');
            if (arguments.length > 1) {
                var newTest = {};
                for (var j = 0, lenj = arguments.length; j < lenj; j++) {
                    var argumentSplit = arguments[j].split('=');
                    argumentSplit[1] = argumentSplit[1].replace(/(\r\n|\n|\r)/gm, "");
                    if (argumentSplit[0] == 'startDate' || argumentSplit[0] == 'endDate') {
                        var currentStartTime = new Date();
                        if(argumentSplit[1].indexOf(';;') != -1){/*with time set*/
                            var timeArr = argumentSplit[1].split(';;');
                            if(!isNaN(parseFloat(argumentSplit[1])) && isFinite(argumentSplit[1])) {
                                currentStartTime.setDate(currentStartTime.getDate() + parseInt(timeArr[0]));
                            }else{
                                dateArr = timeArr[0].split(',');
                                currentStartTime = new Date(dateArr[0], dateArr[1], dateArr[2]);
                            }
                            if(argumentSplit[0] == 'startDate') {
                                newTest['startTime'] = timeArr[1];
                            }
                            if(argumentSplit[0] == 'endDate') {
                                newTest['endTime'] = timeArr[1];
                            }
                        }else { /*time set*/
                            if(!isNaN(parseFloat(argumentSplit[1])) && isFinite(argumentSplit[1])){
                                currentStartTime.setDate(currentStartTime.getDate() + parseInt(argumentSplit[1]));
                            }else{
                                dateArr = argumentSplit[1].split(',');
                                currentStartTime = new Date(dateArr[0], dateArr[1], dateArr[2]);
                            }

                            if(argumentSplit[0] == 'startDate') {
                                newTest['startTime'] = '00:00:00';
                            }
                            if(argumentSplit[0] == 'endDate') {
                                newTest['endTime'] = '00:00:00';
                            }
                        }
                        newTest[argumentSplit[0]] = currentStartTime;
                    } else if(argumentSplit[0] == 'options'){
                        optionsArrWithValues = {};
                        optionsArrVal = argumentSplit[1].split(';;');
                        for(p = 0; p < optionsArrVal.length; p++) {
                            optionValue = optionsArrVal[p].split('::');
                            optionsArrWithValues[optionValue[0]] = optionValue[1];
                            if(typeof optionValue[2] != 'undefined') {
                                optionsArrWithValues['qty_'+optionValue[0]] = optionValue[2];
                            }
                        }
                        newTest['optionsArr'] = optionsArrWithValues;
                    }
                    else {
                        newTest[argumentSplit[0]] = argumentSplit[1];
                    }
                }
                testArray.push(newTest);
            }
        }

    }
}

/*End read test*/

// Magento module
// ----------------------------------------------------------------------------
var mage = require(fs.workingDirectory + '/js/itwebexperts_payperrentals/casperjs/modules/magento');
mage.init(url, secure_url, admin_url);

// Done for the test file
// ----------------------------------------------------------------------------
casper.test.done();

casper.on('remote.message', function(msg) {
    this.echo('remote message caught: ' + msg);
});

/*casper.on('resource.received', function(resource) {
    this.echo('received:'+JSON.stringify(resource, null, 4));
});

casper.on('resource.requested', function(resource) {
 this.echo('requested:'+JSON.stringify(resource, null, 4));
 });
*/

casper.on("page.error", function(msg, trace) {
    this.echo("Error:    " + msg, "ERROR");
    this.echo("file:     " + trace[0].file, "WARNING");
    this.echo("line:     " + trace[0].line, "WARNING");
    this.echo("function: " + trace[0]["function"], "WARNING");
});

casper.on("started", function() {
    casper.clearCookies();
});

casper.on("test.finished'", function() {
    casper.clear();
    casper.page.close();
});

casper.clearCookies = function () {
    casper.test.info("Clear cookies");
    casper.page.clearCookies();
    //phantom.clearCookies();
};

var nrOptionsUpdateBooked ;

function updateNrOptions(optionsArr){
    nrOptionsUpdateBooked = 0;
    for (var key2 in optionsArr) {
        if(key2.indexOf('qty_') == -1) {
            nrOptionsUpdateBooked++;
        }
    }
}

function checkCallsUpdateBooked(resource){

    //this.echo(JSON.stringify(resource, null, 4));
    if (resource.url.indexOf("updatebookedforproduct") > 0 && resource.stage == 'end') {
        console.log("Update booked Returned status code " + resource.url);
        if (typeof nrOptionsUpdateBooked !== 'undefined' && nrOptionsUpdateBooked > 0) {
            nrOptionsUpdateBooked--;
        }
    }
}

casper.evalFunc = function(optionsArr, key){
    casper.evaluate(function (optionsArr, key) {

        key1 = key;

        if ($jppr('#' + key1).is('input') && $jppr('#' + key1).attr('type') == 'text') {
            console.log('change qty ' + key);
            $jppr('#' + key1).val(optionsArr[key]);
            $jppr('#' + key1).trigger('blur');
        }

        if($jppr('#product-options-wrapper').find("li:contains("+optionsArr[key]+")").children(":first").attr('type') == 'radio' || $jppr('#product-options-wrapper').find("li:contains("+optionsArr[key]+")").children(":first").attr('type') == 'checkbox'){
            key1 = $jppr('#product-options-wrapper').find("li:contains("+optionsArr[key]+")").children(":first").attr('id');
            console.log('radio: '+key1);
        }else if($jppr('#product-options-wrapper').find("option:contains("+optionsArr[key]+")").parent().is('select')){
            key1 = $jppr('#product-options-wrapper').find("option:contains("+optionsArr[key]+")").parent().attr('id');
            console.log('select: '+key1);
        }

        if ($jppr('#' + key1).is('input') && $jppr('#' + key1).attr('type') == 'radio') {
            console.log('click radio ' + key);
            $jppr('#' + key1).trigger('click');

        }

        if ($jppr('#' + key1).is('input') && $jppr('#' + key1).attr('type') == 'checkbox') {
            console.log('click checkbox ' + key);
            $jppr('#' + key1).trigger('click');
        }

        if ($jppr('#' + key1).is('select') && $jppr('#' + key1).attr('multiple') != 'multiple') {
            console.log('change select to ' + optionsArr[key]);
            //$jppr('#' + key).find("option[value=" + optionsArr[key] + "]").prop("selected", "selected");
            $jppr('#' + key1).find("option:selected").prop("selected", false);
            $jppr('#' + key1).find("option:contains("+optionsArr[key]+")").prop("selected", "selected");
            $jppr('#' + key1).trigger('change');
        }

        if ($jppr('#' + key1).is('select') && $jppr('#' + key1).attr('multiple') == 'multiple') {

            var selectedOptions = optionsArr[key].split(",");
            $jppr('#' + key1).find("option:selected").prop("selected", false);
            for (var i in selectedOptions) {
                var optionVal = selectedOptions[i];
                console.log('change multiselect to ' + optionVal);
                //$jppr('#' + key).find("option[value=" + optionVal + "]").prop("selected", "selected");
                $jppr('#' + key1).find("option:contains("+optionVal+")").prop("selected", "selected");
            }
            $jppr('#' + key1).multiselect('refresh');
            $jppr('#' + key1).trigger('change');
        }

        if (typeof optionsArr['qty_'+key] != 'undefined'){
            key2 = $jppr('#'+ key1).attr('name').replace('bundle_option','bundle_option_qty');
            $jppr('input[name="'+ key2+'"]').val(optionsArr['qty_'+key]);
        }


        return true;
    }, optionsArr, key);
}

//call should be like --pre=config.js --includes=checkPrices,addtocartFromProductPage,updateShoppingCart,checkoutAsUser,loginAsAdmin,cleanOrders,addToCartFromListings,checkInventoryReport,addtocartAdmin, updateShoppingCartAdmin,changePerProductOptionsAdmin,changeDatesGlobalAdmin test runFile --file="priceTests.data"

//casperjs --pre=js/itwebexperts_payperrentals/casperjs/tests/config.js --includes=js/itwebexperts_payperrentals/casperjs/tests/rent14/checkPrices.js test js/itwebexperts_payperrentals/casperjs/tests/runFile.js --file="js/itwebexperts_payperrentals/casperjs/tests/rent14/priceTests.data"

//the db can be exported or because is using the product name only cat should be variable anyway the idea anyone can run on his own computer the tests/or create his own tests with his own products
//main problem will be on product page with selecting options to be flexible
//also changing setup data

//move into run file






casper.loginCustomer = function(test){


    casper.thenOpen(mage.getBaseUrl()+'customer/account/login',function() {
        test.info('Login with valid identifiers');
        this.fill('form#login-form', {
            'login[username]': 'cristian@itwebexperts.com',
            'login[password]': '111111'
        }, true);
    });

    this.waitForUrl(/customer\/account/, function() {
        var reg = new RegExp(toRegularExp(/*mage.getBaseUrl()+*/'customer/account/')+'.*$');
        test.assertUrlMatch(reg, 'Login customer with user cristian and pass 1111');
    });
};


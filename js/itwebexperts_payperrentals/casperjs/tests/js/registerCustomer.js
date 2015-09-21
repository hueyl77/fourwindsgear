
casper.registerCustomer = function(test){

    casper.thenOpen(mage.getBaseUrl()+'customer/account/login',function() {
        this.thenClick('.account-login .new-users .button', function () {

            test.assertHttpStatus(200);
            this.test.pass('Register page was loaded');

            // check that the form exists
            test.assertExists('div.input-box');
            test.assertExists('input#firstname');
            test.assertExists('input#lastname');
            test.assertExists('input#email_address');
            test.assertExists('input#is_subscribed');
            test.assertExists('input#password');
            test.assertExists('input#confirmation');
            this.test.pass('Register form fields was founded');

            // fill the form
            //TODO: Generate unique data for each test
            test.info('Create new dummy account');
            this.fill('form#form-validate', {
                'firstname': 'Cristian',
                'lastname': 'AA',
                'email': 'cristian@itwebexperts.com',
                'password': '111111',
                'confirmation': '111111'
            }, true);
            this.test.pass('Register form fields was filled');
        });

        this.waitForUrl(/customer\/account/, function () {
            var reg = new RegExp(toRegularExp(mage.getBaseUrl() + 'customer/account/') + '.*$');
            test.assertUrlMatch(reg, 'Register customer');
        });
    });
};


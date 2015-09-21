
casper.loginAdmin = function(test){

    casper.thenOpen(mage.getBaseUrl()+'admin',function() {

        test.info('Login Admin with valid identifiers');
        this.fill('form#loginForm', {
            'login[username]': 'admin',
            'login[password]': 'pass123'
        }, true);
    });
    this.waitForUrl(/dashboard\/index/, function() {

        var reg = new RegExp(toRegularExp(mage.getBaseUrl()+'admin/dashboard/index/')+'.*$');
        test.assertUrlMatch(reg);

    });
};
casper.openAdmin = function(test){

    casper.thenOpen(mage.getBaseUrl()+'admin',function() {

        test.info('Go to Admin');

    });
    this.waitForUrl(/dashboard\/index/, function() {

        var reg = new RegExp(toRegularExp(mage.getBaseUrl()+'admin/dashboard/index/')+'.*$');
        test.assertUrlMatch(reg, 'Login into admin with user admin and pass pass123:');

    });
};


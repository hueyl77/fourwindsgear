
casper.changeSettingsGeneral = function(test, section, group, field, value){
    var path = mage.getBaseUrl().replace('index.php/', '');
    casper.thenOpen(path+'shell/change_settings_general.php?'+'section='+section+'&'+'group='+group+'&'+'field='+field+'&'+'value='+value,function() {
        test.info('General Settings changed');
    });
};

casper.changeSettingsProduct = function(test, sku, attribute, value){
    var path = mage.getBaseUrl().replace('index.php/', '');
    casper.thenOpen(path+'shell/change_settings_product.php?'+'sku='+sku+'&'+'attribute='+attribute+'&'+'value='+value,function() {
        test.info('Product Settings changed');
    });
};

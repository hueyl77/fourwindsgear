function getSerialNumbers(classValue, serviceUrl, selectCompleted){
    $jppr(classValue).each(function () {
        var params = {
            productId: $jppr(this).attr('prid'),
            startDate: $jppr(this).attr('start_date'),
            endDate: $jppr(this).attr('end_date')
        }

        $jppr(this).autocomplete({
            serviceUrl: serviceUrl,
            type:'post',
            minChars: 0,
            paramName: 'value',
            params: params,
            onSelect: function (suggestion) {
                if(typeof selectCompleted === 'function') {
                    selectCompleted($jppr(this).val());
                }
            }
        });
    });
}


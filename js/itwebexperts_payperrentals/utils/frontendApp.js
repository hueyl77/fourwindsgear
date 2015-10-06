require.config({
    baseUrl: jsMagentoUrl+"itwebexperts_payperrentals",
    urlArgs: "bust=" + (new Date()).getTime(),
    paths: {
        'jquery': 'jquery/jquery-1.11.2',
        'frontendbuttons': 'utils/frontendButtons',
        'jqueryui': 'jquery/jquery-ui.min',
        'timepicker': 'jquery/timepicker/jquery-ui-timepicker-addon.min'
    },
    shim: {
        'jqueryui': {
            deps: [ 'jquery' ]
        },
        'timepicker': {
            deps: [ 'jqueryui' ]
        }
    }
});

define('jppr', ['jquery'], function (jppr) {
    $jppr = jQuery.noConflict(  );
});

require(['jppr'], function(myNonGlobaljQuery) {

});
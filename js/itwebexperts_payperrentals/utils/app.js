require.config({
    baseUrl: jsMagentoUrl+"itwebexperts_payperrentals",
    paths: {
        "datepick": "datepick/jquery.datepick",
        "datepicklocale": "datepick/locale/jquery.datepick-"+langCode,
        "formatdatetime": "datepick/jquery.formatDateTime",
        "date": "datepick/date",
        "autocomplete": "autocomplete/jquery-autocomplete",
        "calendarfunctions": "datepick/calendarFunctions",
        "calendarfunctionsadmin": "datepick/calendarFunctionsAdmin",
        "calendarfunctionsfrontend": "datepick/calendarFunctionsFrontend"
    },
    shim: {
        'date': {
            deps: ['jquery']
        },
        'autocomplete': {
            deps: ['jquery']
        },
        'formatdatetime': {
            deps: ['jquery']
        },
        'datepick': {
            deps: ['jquery']
        },
        'datepicklocale': {
            deps: ['jquery']
        }
    }
});

require(['jppr', 'date', 'formatdatetime', 'datepick','datepicklocale'], function(myNonGlobaljQuery) {
    currentDateForCalendar = Date.parseExact(currentDateForCalendar, 'yyyy-MM-dd');

    if(startDateInitial && startDateInitial != '' && typeof startDateInitial !== 'object') {
        startDateInitial = Date.parseExact(startDateInitial, 'yyyy-MM-dd');
    }
    if(endDateInitial && endDateInitial != '' && typeof endDateInitial !== 'object') {
        endDateInitial = Date.parseExact(endDateInitial, 'yyyy-MM-dd');
    }

    require(["calendarfunctions"], function() {
            require(["calendarfunctionsfrontend"], function() {

            });
    });
});


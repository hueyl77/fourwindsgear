enableAllTime = function (type) {
    if (type == 'start') {
        $jppr(currentCalendar).find('[name=start_time]').find('option').removeAttr('disabled');
        $jppr(currentCalendar).find('[name=start_time]').find('option').removeAttr('selected');
    } else if (type == 'end') {
        $jppr(currentCalendar).find('[name=end_time]').find('option').removeAttr('disabled');
        $jppr(currentCalendar).find('[name=end_time]').find('option').removeAttr('selected');
    }
};

deselectAll = function () {
    $jppr(currentCalendar).find('.day-detail tbody td').each(function () {
        $jppr(this).removeClass('busy');
        $jppr(this).removeClass('selected-time');
        $jppr(this).removeClass('available');
        $jppr(this).addClass('available');
    })
};

selectTimeByValue = function (value) {
    $jppr(currentCalendar).find('.day-detail tbody td').each(function () {
        if ($jppr(this).attr('timeendvalue') == value || $jppr(this).attr('timestartvalue') == value) {
            $jppr(this).removeClass('available');
            $jppr(this).addClass('busy');
        }
    });
};
//TODO this needs to be rewriten to use Date.parseExact.
selectByRange = function (startValue, endValue) {
    deselectAll();
    var busyTime = checkBusyTime($jppr(currentCalendar).find('[name=start_date]').val());
    if (busyTime.length > 0 && $jppr(currentCalendar).find('[name=start_date]').val() == $jppr(currentCalendar).find('[name=end_date]').val()) {
        $jppr.each(busyTime, function (elIndex, excludeTime) {
            selectTimeByValue(excludeTime);
        });
    }
    if ($jppr(currentCalendar).find('[name=start_date]').val() == $jppr(currentCalendar).find('[name=end_date]').val() && startValue != endValue) {
        $jppr(currentCalendar).find('.day-detail tbody td').each(function () {
            var startDateValue = Date.parseExact($jppr(currentCalendar).find('[name=start_date]').val(), localDateFormat);
            var startDateFormated = $jppr.formatDateTime('yy-mm-dd', startDateValue);
            if (new Date(startDateFormated + ' ' + $jppr(this).attr('timestartvalue')) <  new Date(startDateFormated + ' ' + endValue)
                && new Date(startDateFormated + ' ' + $jppr(this).attr('timeendvalue')) > new Date(startDateFormated + ' ' + startValue)) {
                $jppr(this).removeClass('available');
                $jppr(this).addClass('selected-time');
            }
        });
    }
};

checkBusyTime = function (selectedDate) {
    var busyTime = [];

    $jppr.each(partiallyBooked, function (index, element) {
        if (element == 0 && $jppr.formatDateTime(dateFormat, new Date(index.replace(/-/g,'/'))) == selectedDate) {
            busyTime.push($jppr.formatDateTime('hh:ii:ss', new Date(index.replace(/-/g,'/'))));
        }
    });
    return busyTime;
};

createBusyTime = function (selected) {

    var d_names = ["sunday","monday", "tuesday", "wednesday", "thursday", "friday", "saturday"];
    $jppr('.timeinputcls').hide();
    if(selected == 'start' && ($jppr(currentCalendar).find('[name=start_date]').val() != '')){
        startDateVal = Date.parseExact($jppr(currentCalendar).find('[name=start_date]').val(), localDateFormat);
        $startTimeDropdown = $jppr('#start_time_'+d_names[startDateVal.getDay()]);
        selectedText = $jppr('#start_time :selected').text();
        $jppr('#start_time').empty(); // remove old options
        $startTimeDropdown.find('option').each(function() {
            if($jppr(this).text() == selectedText) {

                $jppr('#start_time').append($jppr("<option></option>")
                    .attr("value", $jppr(this).val()).text($jppr(this).text()).attr('selected','selected'));
            }else{
                $jppr('#start_time').append($jppr("<option></option>")
                    .attr("value", $jppr(this).val()).text($jppr(this).text()));
            }
        });


    }else if(selected == 'end' && ($jppr(currentCalendar).find('[name=end_date]').val() != '')){
        endDateVal = Date.parseExact($jppr(currentCalendar).find('[name=end_date]').val(), localDateFormat);
        $endTimeDropdown = $jppr('#end_time_'+d_names[endDateVal.getDay()]);
        selectedEndText = $jppr('#end_time :selected').text();
        $jppr('#end_time').empty(); // remove old options
        $endTimeDropdown.find('option').each(function() {
            if($jppr(this).text() == selectedEndText) {
                $jppr('#end_time').append($jppr("<option></option>")
                    .attr("value", $jppr(this).val()).text($jppr(this).text()).attr('selected','selected'));
            }else {
                $jppr('#end_time').append($jppr("<option></option>")
                    .attr("value", $jppr(this).val()).text($jppr(this).text()));
            }
        });
    }
    if ( selected != 'end') return;
    deselectAll();
    enableAllTime(selected);
    if ($jppr(currentCalendar).find('[name=read_' + selected + '_date]').val() != '') {
        var selectedDate = $jppr(currentCalendar).find('[name=read_' + selected + '_date]').val();
        var busyTime = checkBusyTime(selectedDate);
        if (busyTime.length > 0) {
            $jppr.each(busyTime, function (elIndex, excludeTime) {
                $jppr(currentCalendar).find('[name=' + selected + '_time] option[value="' + excludeTime + '"]').attr('disabled', 'disabled');
                $jppr(currentCalendar).find('[name=' + selected + '_time_'+d_names[endDateVal.getDay()]+'] option[value="' + excludeTime + '"]').attr('disabled', 'disabled');
                selectTimeByValue(excludeTime);
            });
        }
        $jppr(currentCalendar).find('[name=' + selected + '_time_'+d_names[endDateVal.getDay()]+']').children('option:not(:disabled):first').attr('selected', 'selected');
    }
};
enableAllTime = function (type) {
    if (type == 'start') {
        $jppr(currentCalendar).find('[name=start_time]').find('option').removeAttr('disabled');
        $jppr(currentCalendar).find('[name=start_time]').find('option').removeAttr('selected');
    } else if (type == 'end') {
        $jppr(currentCalendar).find('[name=end_time]').find('option').removeAttr('disabled');
        $jppr(currentCalendar).find('[name=end_time]').find('option').removeAttr('selected');
    }
};

deselectAll= function () {
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
            if (new Date($jppr(currentCalendar).find('[name=start_date]').val().replace(/(\d{2})\.(\d{2})\.(\d{4})/, '$3-$2-$1') + ' ' + $jppr(this).attr('timestartvalue')) < new Date($jppr(currentCalendar).find('[name=start_date]').val().replace(/(\d{2})\.(\d{2})\.(\d{4})/, '$3-$2-$1') + ' ' + endValue)
                && new Date($jppr(currentCalendar).find('[name=start_date]').val().replace(/(\d{2})\.(\d{2})\.(\d{4})/, '$3-$2-$1') + ' ' + $jppr(this).attr('timeendvalue')) > new Date($jppr(currentCalendar).find('[name=start_date]').val().replace(/(\d{2})\.(\d{2})\.(\d{4})/, '$3-$2-$1') + ' ' + startValue)) {
                $jppr(this).removeClass('available');
                $jppr(this).addClass('selected-time');
            }
        });
    }
};

checkBusyTime = function (selectedDate) {
    var busyTime = [];

    $jppr.each(partiallyBooked, function (index, element) {
        if (element == 0 && $jppr.formatDateTime(dateFormat, new Date(index)) == selectedDate) {
            busyTime.push($jppr.formatDateTime('hh:ii:ss', new Date(index)));
        }
    });
    return busyTime;
};

createBusyTime = function (selected) {
    if ( selected != 'end') return;
    deselectAll();
    enableAllTime(selected);
    if ($jppr(currentCalendar).find('[name=read_' + selected + '_date]').val() != '') {
        var selectedDate = $jppr(currentCalendar).find(' [name=read_' + selected + '_date]').val();
        var busyTime = checkBusyTime(selectedDate);
        if (busyTime.length > 0) {
            $jppr.each(busyTime, function (elIndex, excludeTime) {
                $jppr(currentCalendar).find('[name=' + selected + '_time]').find('[value="' + excludeTime + '"]').attr('disabled', 'disabled');
                selectTimeByValue(excludeTime);
            });
        }
        $jppr(currentCalendar).find('[name=' + selected + '_time]').children('option:not(:disabled):first').attr('selected', 'selected');
    }
};
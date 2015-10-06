<?php



class ITwebexperts_Payperrentals_Helper_Date extends Mage_Core_Helper_Abstract
{
    /**
     * @see http://docs.jquery.com/UI/Datepicker/$.datepicker.formatDate
     * @var array[string]string
     */
    static $formatIsoToDatepicker = array(
        'dd' => 'dd',
        'd' => 'd',
        'DD' => 'EEEE',
        'D' => 'EEE',
        'oo' => 'DD',
        'o' => 'D',
        'mm' => 'MM',
        'm' => 'M',
        'MM' => 'MMMM',
        'M' => 'MMM',
        'yy' => 'yyyy',
        'y' => 'yy',
        '@' => 'U'
    );

    /**
     * Return start end dates as filtered dates in format Y-m-d H:i:s
     * @param $startDate
     * @param $endDate
     *
     * @return array
     */

    public static function convertDatepickerToDbFormat($startDate, $endDate = null){
        if(is_null($endDate)){
            $endDate = $startDate;
        }

        $allDates = explode(',', $startDate);
        $nrVal = count($allDates) - 1;
        if($nrVal > 1) {
            $startingDate = '';
            foreach ($allDates as $key => $iDate) {
                $paramsArr = array('idate' => $iDate);
                $paramsArr = ITwebexperts_Payperrentals_Helper_Date::filterDates($paramsArr, true);
                if ($key != $nrVal) {
                    $startingDate .= $paramsArr['idate'] . ',';
                } else {
                    $startingDate .= $paramsArr['idate'];
                }
            }
            $params = array('start_date' => $startingDate, 'end_date' => $startingDate);
        }else {
            $params = array('start_date' => $startDate, 'end_date' => $endDate);
            $params = ITwebexperts_Payperrentals_Helper_Date::filterDates($params, true);
        }
        return array($params['start_date'], $params['end_date']);
    }


    /**
     * Filters only start end date and save them into session if param is true and returns array with start date filtered
     * Function can take as @params any array but will automatically filter start and end dates and return only those
     * @param array $params
     * @param bool $canSave
     * return array
     */

    public static function saveDatesForGlobalUse($params, $canSave = true)
    {
        $newParams = array();
        if(isset($params['start_date'])){
            if (!isset($params['start_time']) || $params['start_time'] == '00:00:00' || strpos($params['start_date'],' ') !== false) {
                $newParams['start_date'] = $params['start_date'];
            } else {
                $newParams['start_date'] = $params['start_date'] . ' ' . $params['start_time']; //todo needs a check if the separator is always a space
            }
        }

        if(isset($params['end_date'])){
            if (!isset($params['end_time']) || $params['end_time'] == '00:00:00' || strpos($params['end_date'],' ') !== false) {
                $newParams['end_date'] = $params['end_date'];
            } else {
                $newParams['end_date'] = $params['end_date'] . ' ' . $params['end_time']; //todo needs a check if the separator is always a space
            }
        }

        list($startDate, $endDate) = ITwebexperts_Payperrentals_Helper_Date::convertDatepickerToDbFormat($newParams['start_date'], $newParams['end_date']);
        //if(!$isMultiple){
          //  self::_completeEndDate($params, $endDate);
        //}
        if ($canSave) {
            Mage::getSingleton('core/session')->setData('startDateInitial', $startDate);
            Mage::getSingleton('core/session')->setData('endDateInitial', $endDate);
        }
        return array($startDate, $endDate);
    }

    /**
     * Localisation of all the dates when nonsequential mode is enabled
     * @param string $startDate
     * @param bool $showTime
     * @return string
     */
    public static function localiseNonsequentialBuyRequest($startDate, $showTime)
    {
        $startDateArr = explode(',', $startDate);
        $nrVal = count($startDateArr) - 1;
        $startingDateFiltered = '';
        foreach ($startDateArr as $key => $iDate) {
            $iDate = self::formatDbDate($iDate, !$showTime, false);
            if ($key != $nrVal) {
                $startingDateFiltered .= $iDate . ',';
            } else {
                $startingDateFiltered .= $iDate;
            }
        }
        return $startingDateFiltered;
    }

    /**
     * Takes an array of dates from a javascript post (date picker) and converts them format:
     * If using $isDb = true, format will be Timestamp
     * @param array $array
     * @param bool $isDb
     * @return array
     */

    public static function filterDates($array, $isDb = false)
    {
        $arrTime = array();
        $arrTimeFiltered = array();
        $arrDates = array();
        $arrDatesFiltered = array();
        foreach ($array as $dateField => $val) {
            $hasTimeArr = explode(' ', $val);
            if (isset($hasTimeArr[1]) && $hasTimeArr[1] != '00:00:00') {
                if(!self::isFilteredDate($val)) {
                    $arrTime[$dateField] = $val;
                }else{
                    $arrTimeFiltered[$dateField] = $val;
                }
            } else {
                $valWithTime = $val;
                if(!isset($hasTimeArr[1])) {
                    $valWithTime = $val . ' 00:00:00';
                }
                if(!self::isFilteredDate($valWithTime)) {
                    $arrDates[$dateField] = $val;
                }else{
                    $arrDatesFiltered[$dateField] = $val;
                }
            }
        }
        $arrTime = self::filterDateTimeOnly($arrTime, $isDb);
        $arrDates = self::filterDatesOnly($arrDates, $isDb);
        return array_merge($arrTime, $arrDates, $arrTimeFiltered, $arrDatesFiltered);
    }

    /**
     * Convert dates in array from localized to internal format
     *
     * @param   array $array
     * @param $isDb
     * @internal param array $dateFields
     * @return  array
     */
    public static function filterDatesOnly($array, $isDb)
    {
        $filterInput = new Zend_Filter_LocalizedToNormalized(array(
            'date_format' => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT)
        ));
        if (!$isDb) {
            $filterInternal = new Zend_Filter_NormalizedToLocalized(array(
                'date_format' => Varien_Date::DATE_INTERNAL_FORMAT
            ));
        } else {
            $filterInternal = new Zend_Filter_NormalizedToLocalized(array(
                'date_format' => 'yyyy-MM-dd HH:mm:ss'
            ));
        }
        foreach ($array as $dateField => $val) {
            $array[$dateField] = $filterInput->filter($array[$dateField]);
            $array[$dateField] = $filterInternal->filter($array[$dateField]);
        }
        return $array;
    }

    /**
     * Convert dates with time in array from localized to internal format
     *
     * @param   array $array
     * @param $isDb
     * @internal param array $dateFields
     * @return  array
     */
    public static function filterDateTimeOnly($array, $isDb)
    {
        $filterInput = new Zend_Filter_LocalizedToNormalized(array(
            'date_format' => Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT)
        ));
        if (!$isDb) {
            $filterInternal = new Zend_Filter_NormalizedToLocalized(array(
                'date_format' => Varien_Date::DATETIME_INTERNAL_FORMAT
            ));
        } else {
            $filterInternal = new Zend_Filter_NormalizedToLocalized(array(
                'date_format' => 'yyyy-MM-dd HH:mm:ss'
            ));
        }
        foreach ($array as $dateField => $val) {
            $array[$dateField] = $filterInput->filter($array[$dateField]);
            $array[$dateField] = $filterInternal->filter($array[$dateField]);
        }
        return $array;
    }

    public static function convertIsoToPhpFormat($format)
    {
        if ($format === null) {
            return null;
        }
        $convert = array('d' => 'dd', 'D' => 'EE', 'j' => 'd', 'l' => 'EEEE', 'N' => 'eee', 'S' => 'SS',
            'w' => 'e', 'z' => 'D', 'W' => 'ww', 'F' => 'MMMM', 'm' => 'MM', 'M' => 'MMM',
            'n' => 'M', 't' => 'ddd', 'L' => 'l', 'o' => 'YYYY', 'Y' => 'yyyy', 'y' => 'yy',
            'a' => 'a', 'A' => 'a', 'B' => 'B', 'g' => 'h', 'G' => 'H', 'h' => 'hh',
            'H' => 'HH', 'i' => 'mm', 's' => 'ss', 'e' => 'zzzz', 'I' => 'I', 'O' => 'Z',
            'P' => 'ZZZZ', 'T' => 'z', 'Z' => 'X', 'c' => 'yyyy-MM-ddTHH:mm:ssZZZZ',
            'r' => 'r', 'U' => 'U');

        $convert = array_flip($convert);
        asort($convert);

        return strtr($format, $convert);
    }

    /**
     * Used for converting ISO date format to Datepicker format
     * @param $format
     * @return null|string
     */

    public static function convertIsoToDatepickerFormat($format)
    {
        if ($format === null) {
            return null;
        }

        $convert = array_flip(self::$formatIsoToDatepicker);
        arsort($convert);

        $value = strtr($format, $convert);

        return $value;
    }

    /**
     * Used for converting datepicker date format to ISO format
     * @param $format
     * @return null|string
     */

    public static function convertDatepickerToIsoFormat($format)
    {
        if ($format === null) {
            return null;
        }

        $convert = self::$formatIsoToDatepicker;
        arsort($convert);

        $value = strtr($format, $convert);

        return $value;
    }

    /**
     * Validates a date against a format
     * @param string $date
     * @param string $format
     * @return bool
     */

     public static function isFilteredDate($date, $format = 'Y-m-d H:i:s')
    {
        $dt = DateTime::createFromFormat($format, $date);
        return $dt !== false && !array_sum($dt->getLastErrors()) && $dt->format($format) == $date;
    }

    /**
     * Function used in queries with date type for convert timestamp to db date format
     *
     * @param $date
     * @param bool $isTimestamp
     * @param int $pVal
     *
     *@return string
     */
    public static function toDbDate($date, $isTimestamp = false, $pVal = 0)
    {
        if (!$isTimestamp) {
            return date(
                'Y-m-d H:i:s', strtotime((($pVal >= 0) ? '+' : '-') . abs($pVal) . ' SECONDS', strtotime($date))
            );
        } else {
            return date('Y-m-d H:i:s', strtotime((($pVal >= 0) ? '+' : '-') . abs($pVal) . ' SECONDS', $date));
        }
    }

    /**
     * Function to format a date from database and display it on a page
     *
     * @param string $date
     * @param bool $noTime
     * @param bool $long
     *
     * @return string
     */
    public static function formatDbDate($date, $noTime = true, $long = false)
    {
        if ($date == '1970-01-01 00:00:00' || $date == null || $date == '0000-00-00 00:00:00'){
            return '';
        }

        $_myDate = new Zend_Date(date('Y-m-d H:i:s', strtotime($date)), 'yyyy-MM-dd HH:mm:ss');

        if (date('H:i', strtotime($date)) == '00:00') {
            $noTime = true;
            if (Mage::getStoreConfig(ITwebexperts_Payperrentals_Helper_Config::XML_PATH_GLOBAL_DATES_SHOW_MIDNIGHT)) {
                $noTime = false;
            }
        }
        if ($noTime) {
            if ($long) {
                return $_myDate->get(Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_LONG));
            } else {
                return $_myDate->get(
                    Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT)
                );
            }
        } else {
            if ($long) {
                return $_myDate->get(
                    Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_LONG)
                );
            } else {
                return $_myDate->get(
                    Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT)
                );
            }
        }
    }

    /**
     * Defaults to converting a datetime form entry to a timestamp
     * if $isDate is true it converts a form entry to a mysql datetime format like YYYY-MM-DD HH:II:SS
     *
     * @param $datetime should be in format Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT)
     * @return int|string
     */

    public static function toMysqlDate($datetime, $isDate = false)
    {
        $myDate = new Zend_Date(
            $datetime, Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT)
        );
        if($isDate){
            return date('Y-m-d H:i:s', $myDate->getTimestamp());
        }else {
            return $myDate->getTimestamp();
        }
    }

    /**
     * Return the difference of 2 dates in seconds
     * @param $startDate
     * @param $endDate
     *
     * @return int|string
     */

    public static function getDifferenceInSeconds($startDate, $endDate){
        $locale = Mage::getModel('core/locale')->getLocale();

        $start_date = new Zend_Date($startDate, 'yyyy-MM-dd HH:mm:ss', $locale);
        $end_date = new Zend_Date($endDate, 'yyyy-MM-dd HH:mm:ss', $locale);
        $difference = $end_date->sub($start_date);
        $measure = new Zend_Measure_Time($difference->toValue(), Zend_Measure_Time::SECOND);

        return $measure->getValue();
    }

    /**
     * used to determine if two sets of dates overlap, returns true
     * if either the start or end date is within the second start and end date
     *
     * @param $startdate YYYY-MM-DD HH:MM:SS
     * @param $enddate YYYY-MM-DD HH:MM:SS
     * @param $startdate2 YYYY-MM-DD HH:MM:SS
     * @param $enddate2 YYYY-MM-DD HH:MM:SS
     */

    public function doDatesOverlap($startdate, $enddate, $startdate2, $enddate2){
        $startdate = strtotime($startdate);
        $enddate = strtotime($enddate);
        $enddate2 = strtotime($enddate2);
        $startdate2 = strtotime($startdate2);
        if($startdate <= $enddate2 & $enddate >= $startdate2){
            return true;
        } else {return false;}

    }

}
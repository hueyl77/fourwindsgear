<?php

class ITwebexperts_Payperrentals_Model_System_Config_Source_Turnaroundtype
{
    public function toKeyValueAr()
    {
        return array(
            '1' => 'minute',
            '2' => 'hour',
            '3' => 'day',
            '4' => 'week',
            '5' => 'month',
            '6' => 'year'
        );
    }

    public function getWeekDaysAr()
    {
        return array(
            array('value' => 0, 'label' => 'Sunday'),
            array('value' => 1, 'label' => 'Monday'),
            array('value' => 2, 'label' => 'Tuesday'),
            array('value' => 3, 'label' => 'Wednesday'),
            array('value' => 4, 'label' => 'Thursday'),
            array('value' => 5, 'label' => 'Friday'),
            array('value' => 6, 'label' => 'Saturday'),
        );
    }
}
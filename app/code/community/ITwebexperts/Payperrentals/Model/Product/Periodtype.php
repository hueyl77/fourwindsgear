<?php

class ITwebexperts_Payperrentals_Model_Product_Periodtype extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    const MINUTES = 1;
    const HOURS = 2;
    const DAYS = 3;
    const WEEKS = 4;
    const MONTHS = 5;
    const YEARS = 6;

    static public function getOptionArray($_number = null)
    {
        return array(
            self::MINUTES => Mage::helper('payperrentals')->__('Minute') . ((is_null($_number) || (int)$_number > 1) ? 's' : ''),
            self::HOURS => Mage::helper('payperrentals')->__('Hour')  . ((is_null($_number) || (int)$_number > 1) ? 's' : ''),
            self::DAYS => Mage::helper('payperrentals')->__('Day')  . ((is_null($_number) || (int)$_number > 1) ? 's' : ''),
            self::WEEKS => Mage::helper('payperrentals')->__('Week')  . ((is_null($_number) || (int)$_number > 1) ? 's' : ''),
            self::MONTHS => Mage::helper('payperrentals')->__('Month') . ((is_null($_number) || (int)$_number > 1) ? 's' : ''),
            self::YEARS => Mage::helper('payperrentals')->__('Year') . ((is_null($_number) || (int)$_number > 1) ? 's' : '')

        );
    }

    static public function getAllOption()
    {

        $options = self::getOptionArray();
        array_unshift($options, array('value' => '', 'label' => ''));
        return $options;
    }

    public function getAllOptions()
    {
        $res = array(
            array(
                'value' => '',
                'label' => Mage::helper('payperrentals')->__('-- Please Select --')
            )
        );
        foreach (self::getOptionArray() as $index => $value) {
            $res[] = array(
                'value' => $index,
                'label' => $value
            );
        }
        return $res;
    }
}


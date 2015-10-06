<?php

class ITwebexperts_Payperrentals_Model_Product_Excludedaysweek extends Mage_Eav_Model_Entity_Attribute_Source_Abstract{
    const DISABLED    = 0;
    const MONDAY   = 1;
	const TUESDAY   = 2;
	const WEDNESDAY   = 3;
	const THURSDAY   = 4;
	const FRIDAY   = 5;
	const SATURDAY   = 6;
	const SUNDAY   = 7;
	
	static public function getOptionArray(){
        return array(
            self::DISABLED    => Mage::helper('payperrentals')->__('Disabled'),
            self::MONDAY   => Mage::helper('payperrentals')->__('Monday'),
			self::TUESDAY   => Mage::helper('payperrentals')->__('Tuesday'),
			self::WEDNESDAY   => Mage::helper('payperrentals')->__('Wednesday'),
			self::THURSDAY   => Mage::helper('payperrentals')->__('Thursday'),
			self::FRIDAY   => Mage::helper('payperrentals')->__('Friday'),
			self::SATURDAY   => Mage::helper('payperrentals')->__('Saturday'),
			self::SUNDAY   => Mage::helper('payperrentals')->__('Sunday')
        );
    }
    
    static public function getAllOption()
    {
    	
        $options = self::getOptionArray();
        array_unshift($options, array('value'=>'', 'label'=>''));
        return $options;
    }
    
    public function getAllOptions()
    {
		$res = array();
        foreach (self::getOptionArray() as $index => $value) {
            $res[] = array(
               'value' => $index,
               'label' => $value
            );
        }
        return $res;
    }
}


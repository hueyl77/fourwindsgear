<?php

class ITwebexperts_Payperrentals_Model_Product_Isreservation extends Mage_Eav_Model_Entity_Attribute_Source_Abstract{
	const STATUS_RENTALANDRESERVATION   = 4;//reservation and rental queue
    const STATUS_ENABLED    = 1;//reservation
	const STATUS_RENTAL     = 2;//rental queue
    const STATUS_DISABLED   = 5;//disabled
    const STATUS_NOTSET   = NULL;//not set
	
	static public function getOptionArray(){
        return array(
            self::STATUS_ENABLED    => Mage::helper('payperrentals')->__('Reservation'),
			self::STATUS_RENTAL    => Mage::helper('payperrentals')->__('Rental Queue'),
			self::STATUS_RENTALANDRESERVATION    => Mage::helper('payperrentals')->__('Rental AND Reservation'),
            self::STATUS_DISABLED   => Mage::helper('payperrentals')->__('Disabled')
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


<?php

class ITwebexperts_Payperrentals_Model_Customer_Membershippackage_Ccmonth extends Mage_Eav_Model_Entity_Attribute_Source_Abstract{
    const STATUS_ENABLED    = 1;
    const STATUS_DISABLED   = 0;
	
	static public function getOptionArray(){
        return array(
            1 => Mage::helper('payperrentals')->__('January'),
            2 => Mage::helper('payperrentals')->__('February'),
            3 => Mage::helper('payperrentals')->__('March'),
            4 => Mage::helper('payperrentals')->__('April'),
            5 => Mage::helper('payperrentals')->__('May'),
            6 => Mage::helper('payperrentals')->__('June'),
            7 => Mage::helper('payperrentals')->__('July'),
            8 => Mage::helper('payperrentals')->__('August'),
            9 => Mage::helper('payperrentals')->__('September'),
            10 => Mage::helper('payperrentals')->__('October'),
            11 => Mage::helper('payperrentals')->__('November'),
            12 => Mage::helper('payperrentals')->__('December'),
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


<?php

class ITwebexperts_Payperrentals_Model_Product_Allowoverbooking extends Mage_Eav_Model_Entity_Attribute_Source_Abstract{
    const STATUS_ENABLED    = 1;
    const STATUS_DISABLED   = 0;
	
	static public function getOptionArray(){
        return array(
            self::STATUS_ENABLED    => Mage::helper('payperrentals')->__('Enabled'),
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


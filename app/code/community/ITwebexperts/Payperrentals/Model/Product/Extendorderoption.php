<?php

class ITwebexperts_Payperrentals_Model_Product_Extendorderoption extends Mage_Eav_Model_Entity_Attribute_Source_Abstract{
    const USE_GLOBAL   = 0;
    const YES   = 1;
    const NO   = 2;
	
	static public function getOptionArray(){
        return array(
            self::USE_GLOBAL    => Mage::helper('payperrentals')->__('Use Global Config'),
            self::YES   => Mage::helper('payperrentals')->__('Yes'),
            self::NO   => Mage::helper('payperrentals')->__('No')
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
        foreach (self::getOptionArray() as $index => $value) {
            $res[] = array(
               'value' => $index,
               'label' => $value
            );
        }
        return $res;
    }
}


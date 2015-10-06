<?php

class ITwebexperts_Payperrentals_Model_Product_Pricingtype extends Mage_Eav_Model_Entity_Attribute_Source_Abstract{
    const PRICING_PRORATED    = 1;
    const PRICING_NONPRORATED   = 2;
	
	static public function getOptionArray(){
        return array(
            self::PRICING_PRORATED    => Mage::helper('payperrentals')->__('Prorated'),
            self::PRICING_NONPRORATED   => Mage::helper('payperrentals')->__('Nonprorated')
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


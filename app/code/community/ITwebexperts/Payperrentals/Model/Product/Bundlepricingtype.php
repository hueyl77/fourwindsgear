<?php

class ITwebexperts_Payperrentals_Model_Product_Bundlepricingtype extends Mage_Eav_Model_Entity_Attribute_Source_Abstract{
    const PRICING_BUNDLE_PERPRODUCT   = 1;
    const PRICING_BUNDLE_FORALL   = 2;
	
	static public function getOptionArray(){
        return array(
            self::PRICING_BUNDLE_PERPRODUCT    => Mage::helper('payperrentals')->__('Per Product'),
            self::PRICING_BUNDLE_FORALL   => Mage::helper('payperrentals')->__('For all')
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


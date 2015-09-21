<?php

class ITwebexperts_Payperrentals_Model_Customer_Membershippackage_Name extends Mage_Eav_Model_Entity_Attribute_Source_Abstract{
    const STATUS_ENABLED    = 1;
    const STATUS_DISABLED   = 0;
	
	static public function getOptionArray(){
        $membershippackages = Mage::getModel('catalog/product')->getCollection()->addAttributeToFilter('type_id','membershippackage');
        $optionsarray = array();
        foreach($membershippackages as $memberpackage){
            $product = Mage::getModel('catalog/product')->load($memberpackage->getId());
            $optionsarray[$memberpackage->getId()] = $product->getName();
        }
        return $optionsarray;
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


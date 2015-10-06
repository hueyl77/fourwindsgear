<?php

class ITwebexperts_Payperrentals_Model_Product_Fixedrentalnames extends Mage_Eav_Model_Entity_Attribute_Source_Abstract{

	
	static public function getOptionArray(){
        $options = array();
        $collectionFixed = Mage::getModel('payperrentals/fixedrentalnames')->getCollection();
        foreach($collectionFixed as $fixedRental){
            $options[$fixedRental->getId()] = $fixedRental->getName();
        }

        return $options;
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
                'label' => Mage::helper('payperrentals')->__('No')
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


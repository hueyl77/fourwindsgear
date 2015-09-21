<?php

class ITwebexperts_Payperrentals_Model_Product_Excludemembership extends Mage_Eav_Model_Entity_Attribute_Source_Abstract{
    const DISABLED    = 0;

	
	static public function getOptionArray(){
		$arr =  array(
			self::DISABLED    => Mage::helper('payperrentals')->__('Disabled'),
		);

		$coll = Mage::getModel('catalog/product')->getCollection()
		->addAttributeToSelect('name')
		->addAttributeToFilter('type_id', array('eq' => 'membershippackage'));

		foreach($coll as $item){
			$arr[$item->getSku()] = $item->getName();
		}

		return $arr;
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


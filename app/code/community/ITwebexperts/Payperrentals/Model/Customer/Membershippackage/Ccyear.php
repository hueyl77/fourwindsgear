<?php

class ITwebexperts_Payperrentals_Model_Customer_Membershippackage_Ccyear extends Mage_Eav_Model_Entity_Attribute_Source_Abstract{
    const STATUS_ENABLED    = 1;
    const STATUS_DISABLED   = 0;
	
	static public function getOptionArray(){
        $year = date("Y");
        $yeararray = array();
        for($i=0;$i<=10;$i++){
            $yeararray[$year] = $year;
                $year++;
        }
        return $yeararray;
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


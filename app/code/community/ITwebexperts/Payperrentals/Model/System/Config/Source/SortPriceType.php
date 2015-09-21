<?php

class ITwebexperts_Payperrentals_Model_System_Config_Source_SortPriceType
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 1, 'label' => Mage::helper('payperrentals')->__('ASC')),
            array('value' => 0, 'label' => Mage::helper('payperrentals')->__('DESC')),
        );
    }
}
<?php

class ITwebexperts_Payperrentals_Model_Source_Turnover
{

    public function toOptionArray()
    {
        return array(
            array('value' => 'turnover', 'label'=>Mage::helper('payperrentals')->__('End Date With Turnover')),
            array('value' => 'withoutturnover', 'label'=>Mage::helper('payperrentals')->__('End Date Without Turnover'))
        );
    }

}
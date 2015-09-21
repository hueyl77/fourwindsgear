<?php
class ITwebexperts_Payperrentals_Model_Product_Convert extends Mage_Core_Model_Abstract
{
    /**
     * Retrieve option array
     *
     * @return array
     */
    public function getOptionArray()
    {
        return array(
            'simpletoreservation'    => Mage::helper('payperrentals')->__('Simple to reservation'),
            'reservationtosimple'   => Mage::helper('payperrentals')->__('Reservation to simple')
        );
    }
}
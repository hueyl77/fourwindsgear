<?php
class ITwebexperts_Payperrentals_Model_Config_Version extends Mage_Core_Model_Config_Data
{
    protected function _afterLoad() {
        $this->setValue( (string)Mage::getConfig()->getNode()->modules->ITwebexperts_Payperrentals->version );
    }
}

<?php

class ITwebexperts_Payperrentals_Model_Mysql4_Fixedrentalnames_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('payperrentals/fixedrentalnames');
    }
}

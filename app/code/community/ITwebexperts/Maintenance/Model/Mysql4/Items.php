<?php
class ITwebexperts_Maintenance_Model_Mysql4_Items extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('simaintenance/items','item_id');
    }
}
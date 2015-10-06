<?php
class ITwebexperts_Maintenance_Model_Mysql4_Periodic extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('simaintenance/periodic','periodic_id');
    }
}
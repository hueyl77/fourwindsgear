<?php
class ITwebexperts_Maintenance_Model_Mysql4_Snippets_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        $this->_init('simaintenance/snippets');
        parent::_construct();
    }
}
<?php
class ITwebexperts_Maintenance_Model_Maintainers extends Mage_Core_Model_Abstract
{
    public function __construct()
    {
        $this->_init('simaintenance/maintainers');
        parent::_construct();
    }
}
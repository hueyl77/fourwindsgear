<?php

class ITwebexperts_Rshipping_Model_Mysql4_Rshipping extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        // Note that the rshipping_id refers to the key field in your database table.
        $this->_init('rshipping/rshipping', 'rshipping_id');
    }
}
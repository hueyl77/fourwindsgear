<?php

/**
 * @category   Itwebexperts
 * @package    Itwebexperts_Rshipping
 * @copyright  Copyright (c) 2013
 *
 */
class ITwebexperts_Rshipping_Model_Mysql4_Products_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

    public function _construct()
    {
        parent::_construct();
        $this->_init('rshipping/products');
    }

    public function addShippingFilter($shippingId)
    {
        $this->getSelect()
            ->where('rshipping_id = ?', $shippingId);
        return $this;
    }

}
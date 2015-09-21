<?php

/**
 * @category   Itwebexperts
 * @package    Itwebexperts_Rshipping
 * @copyright  Copyright (c) 2013
 *
 */
class ITwebexperts_Rshipping_Model_Mysql4_Products extends Mage_Core_Model_Mysql4_Abstract
{

    public function _construct()
    {
        $this->_init('rshipping/products', 'link_id');
    }


}
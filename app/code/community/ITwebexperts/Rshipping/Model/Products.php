<?php

/**
 * @category   ITwebexperts
 * @package    Itwebexperts_Rshipping
 * @copyright  Copyright (c) 2013
 *
 */

class ITwebexperts_Rshipping_Model_Products extends Mage_Core_Model_Abstract
{

    public function _construct()
    {
        parent::_construct();
        $this->_init('rshipping/products');
    }

}
<?php

/**
 * @category   Itwebexperts
 * @package    Itwebexperts_Rshipping
 * @copyright  Copyright (c) 2013
 *
 */

class ITwebexperts_Rshipping_Model_Status extends Varien_Object
{
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 2;

    static public function getOptionArray()
    {
        return array(
            self::STATUS_ENABLED => Mage::helper('rshipping')->__('Enabled'),
            self::STATUS_DISABLED => Mage::helper('rshipping')->__('Disabled')
        );
    }
}
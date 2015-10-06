<?php

/**
 * @category   ITwebexperts
 * @package    Itwebexperts_Rshipping
 * @copyright  Copyright (c) 2013
 *
 */
class ITwebexperts_Rshipping_Block_Rshipping extends Mage_Core_Block_Template
{

    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    public function getRshipping()
    {
        if (!$this->hasData('rshipping')) {
            $this->setData('rshipping', Mage::registry('rshipping'));
        }
        return $this->getData('rshipping');
    }

}
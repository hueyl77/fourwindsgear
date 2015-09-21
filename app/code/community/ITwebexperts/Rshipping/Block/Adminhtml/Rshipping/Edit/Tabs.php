<?php

/**
 * @category   Itwebexperts
 * @package    Itwebexperts_Rshipping
 * @copyright  Copyright (c) 2013
 *
 */

class ITwebexperts_Rshipping_Block_Adminhtml_Rshipping_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('rshipping_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('rshipping')->__('Method Information'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('form_section', array(
            'label' => Mage::helper('rshipping')->__('Method Information'),
            'title' => Mage::helper('rshipping')->__('Method Information'),
            'content' => $this->getLayout()->createBlock('rshipping/adminhtml_rshipping_edit_tab_form')->toHtml(),
        ));

        return parent::_beforeToHtml();
    }

}
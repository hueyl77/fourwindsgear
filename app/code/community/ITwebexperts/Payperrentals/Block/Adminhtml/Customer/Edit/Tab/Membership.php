<?php

/**
 * Class ITwebexperts_Payperrentals_Block_Adminhtml_Customer_Edit_Tab_Membership
 */
class ITwebexperts_Payperrentals_Block_Adminhtml_Customer_Edit_Tab_Membership extends Mage_Adminhtml_Block_Widget implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     *
     */
    public function __construct()
	{
		parent::__construct();
		$this->setTemplate('payperrentals/customer/edit/tab/membership.phtml');
	}


    /**
     * @return string
     */
    public function getTabLabel()
	{
		return Mage::helper('payperrentals')->__('Membership');
	}

    /**
     * @return string
     */
    public function getTabTitle()
	{
		return Mage::helper('payperrentals')->__('Membership');
	}

    /**
     * @return bool
     */
    public function canShowTab()
	{
		if (Mage::registry('current_customer')->getId()) {
			return true;
		}
		return false;
	}

    /**
     * @return bool
     */
    public function isHidden()
	{
		if (Mage::registry('current_customer')->getId()) {
			return false;
		}
		return true;
	}


}

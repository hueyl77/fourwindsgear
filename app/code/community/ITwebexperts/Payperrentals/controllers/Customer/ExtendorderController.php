<?php
/**
 * Class ITwebexperts_Payperrentals_Customer_RentalqueueController
 */
class ITwebexperts_Payperrentals_Customer_ExtendorderController extends Mage_Core_Controller_Front_Action
{

    /**
     *
     */
    public function indexAction()
	{
		if( !Mage::getSingleton('customer/session')->isLoggedIn() ) {
			Mage::getSingleton('customer/session')->authenticate($this);
			return;
		}

		$this->loadLayout();

		$this->getLayout()->getBlock('head')->setTitle(Mage::helper('payperrentals')->__('Extend Order'));
		$this->renderLayout();
	}
}

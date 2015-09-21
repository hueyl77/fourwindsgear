<?php
/**
 * Class ITwebexperts_Payperrentals_Customer_RentalqueueController
 */
class ITwebexperts_Payperrentals_Customer_RentalqueueController extends Mage_Core_Controller_Front_Action
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

		//$this->_initLayoutMessages('catalog/session');

		$navigationBlock = $this->getLayout()->getBlock('customer_account_navigation');
		if ($navigationBlock) {
			$navigationBlock->setActive('payperrentals/customer_rentalqueue');
		}
		if ($block = $this->getLayout()->getBlock('payperrentals_customer_rentalqueuelist')) {
			$block->setRefererUrl($this->_getRefererUrl());
		}

		$this->getLayout()->getBlock('head')->setTitle(Mage::helper('payperrentals')->__('Rental Queue'));
		$this->renderLayout();
	}
}

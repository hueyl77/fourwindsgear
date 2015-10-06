<?php
/**
 *
 * @author Enrique Piatti
 */
require_once 'Mage/Checkout/controllers/OnepageController.php';


class ITwebexperts_Payperrentals_OnepageController extends Mage_Checkout_OnepageController
{

	/**
	 * override for adding desired dropoff and pickup dates
	 */
	public function saveShippingMethodAction()
	{
		$dropoffDate = $this->getRequest()->getParam('dropoff_date');
		$pickupDate = $this->getRequest()->getParam('pickup_date');
		$dropoffTime = $this->getRequest()->getParam('dropoff_time');
		$pickupTime = $this->getRequest()->getParam('pickup_time');
		$dropoff = $dropoffDate . ' ' . $dropoffTime;
		$pickup = $pickupDate . ' ' . $pickupTime;
		$this->getOnepage()->getQuote()->setSendDatetime($dropoff);
		$this->getOnepage()->getQuote()->setReturnDatetime($pickup);
		parent::saveShippingMethodAction();
	}


}

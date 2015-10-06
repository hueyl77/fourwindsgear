<?php


/**
 * Class ITwebexperts_Payperrentals_Block_Adminhtml_Html_Renderer_Customername
 */
class ITwebexperts_Payperrentals_Block_Adminhtml_Html_Renderer_Customername extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row){
		$data = $row->getData($this->getColumn()->getIndex());
		//$customer = Mage::getModel('customer/customer')->load( $data);
		//$customerName = $customer->getFirstname(). ' '.$customer->getLastname();
		$order = Mage::getModel('sales/order')->load($data);

		$shippingId = $order->getShippingAddressId();
		if(empty($shippingId)){
			$shippingId = $order->getBillingAddressId();
		}
		$address = Mage::getModel('sales/order_address')->load($shippingId);
		$customerName = $address->getFirstname(). ' '.$address->getLastname();
		return $customerName;
	}

}

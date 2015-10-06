<?php


/**
 * Class ITwebexperts_Payperrentals_Block_Adminhtml_Html_Renderer_Customeraddress
 */
class ITwebexperts_Payperrentals_Block_Adminhtml_Html_Renderer_Customeraddress extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * @param Varien_Object $row
     * @return mixed
     */
    public function render(Varien_Object $row){
		$data = $row->getData($this->getColumn()->getIndex());
		/*
		$customer = Mage::getModel('customer/customer')->load( $data);
		$address = Mage::getModel('customer/address')->load( $customer->getDefaultShipping());
		*/
		$order = Mage::getModel('sales/order')->load($data);

		$shippingId = $order->getShippingAddressId();
		if(empty($shippingId)){
			$shippingId = $order->getBillingAddressId();
		}
		$address = Mage::getModel('sales/order_address')->load($shippingId);
		return $address->format('html');
	}

}

<?php


/**
 * Class ITwebexperts_Payperrentals_Block_Adminhtml_Html_Rendererqueue_Customeraddress
 */
class ITwebexperts_Payperrentals_Block_Adminhtml_Html_Rendererqueue_Customeraddress extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * @param Varien_Object $row
     * @return mixed
     */
    public function render(Varien_Object $row){
		$data = $row->getData($this->getColumn()->getIndex());
		$customer = Mage::getModel('customer/customer')->load( $data);
		$address = Mage::getModel('customer/address')->load( $customer->getDefaultShipping());
		return $address->format('html');
	}

}

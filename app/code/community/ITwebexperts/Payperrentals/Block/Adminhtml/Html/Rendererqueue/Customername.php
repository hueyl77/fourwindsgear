<?php


/**
 * Class ITwebexperts_Payperrentals_Block_Adminhtml_Html_Rendererqueue_Customername
 */
class ITwebexperts_Payperrentals_Block_Adminhtml_Html_Rendererqueue_Customername extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row){
		$data = $row->getData($this->getColumn()->getIndex());
		$customer = Mage::getModel('customer/customer')->load( $data);
		$customerName = $customer->getFirstname(). ' '.$customer->getLastname();

		return $customerName;
	}

}

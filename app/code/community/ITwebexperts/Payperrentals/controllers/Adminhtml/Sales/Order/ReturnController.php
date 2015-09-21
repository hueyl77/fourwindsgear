<?php
/**
 *
 * @author Enrique Piatti
 */
class ITwebexperts_Payperrentals_Adminhtml_Sales_Order_ReturnController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed(){
        return true;
    }

    /**
     * Return create page
     */
    public function newAction()
    {
        $_orderId = $this->getRequest()->getParam('order_id');
        $_order = Mage::getModel('sales/order')->load($_orderId);
        Mage::register('current_order', $_order);

        $this->_title($this->__('New Return'));

        $this->loadLayout()->_setActiveMenu('sales/order')->renderLayout();
    }

    /**
     *
     */
    public function saveAction()
    {
        $_orderId = $this->getRequest()->getParam('order_id');
        $_sendItems = $this->getRequest()->getParam('send_items');
        if (!$_sendItems) {
            $this->_getSession()->addError('Select at least one item to return');
            $this->_redirect('*/*/new', array('_current' => true));
            return;
        }
        Mage::helper('payperrentals/inventory')->processReturn($_sendItems);
        $this->_getSession()->addSuccess('Returns saved successfully');
        $this->_redirect('*/sales_order/view', array('order_id' => $_orderId));
    }
}

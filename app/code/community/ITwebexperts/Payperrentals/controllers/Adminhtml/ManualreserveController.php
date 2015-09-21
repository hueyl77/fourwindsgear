<?php
if(Mage::helper('itwebcommon')->isVendorInstalled() && preg_match('/vendors/',Mage::helper('core/url')->getCurrentUrl())){
    class ITwebexperts_Payperrentals_Adminhtml_ManualreserveControllerExtender extends VES_Vendors_Controller_Action
    {}
} else {
    class ITwebexperts_Payperrentals_Adminhtml_ManualreserveControllerExtender extends Mage_Adminhtml_Controller_Action
    {}
}
class ITwebexperts_Payperrentals_Adminhtml_ManualreserveController extends ITwebexperts_Payperrentals_Adminhtml_ManualreserveControllerExtender
{
    protected function _isAllowed(){
        return Mage::getSingleton('admin/session')->isAllowed('admin/payperrentals/manualreserve');;
    }

    public function indexAction()
    {
        $this->loadLayout()->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        $orderid = $this->getRequest()->getParam('order_id');
        $data = $this->_getSession()->getFormData(true);
        $reservationLoad = Mage::getModel('payperrentals/reservationorders');

        if ($id || $orderid) {
            $reservationLoad->load($id);
            if ($reservationLoad->getId() || $orderid) {
                /**
                 * If reservation has order id, use it to get all reservations
                 * for that order
                 */
                if(!isset($orderid)){$orderid = $reservationLoad->getOrderId();}
                if ($orderid != 0) {
                    $reservationLoad = Mage::getModel('payperrentals/reservationorders')->getCollection();
                    $reservationLoad->addFieldToFilter('order_id', array(
                        '=' => $orderid
                    ));
                    $productsInOrder = $reservationLoad->count();
                    Mage::register('productsInOrder', $productsInOrder);
                }
                if ($data) {
                    $reservationLoad->setData($data)->setId($id);
                }

                /** Permissions check if vendor */
                if(Mage::helper('itwebcommon')->isVendorAdmin()) {
                    foreach($reservationLoad as $reservationOrder) {
                        if (Mage::getSingleton('vendors/session')->getId() != $reservationOrder->getVendorId()) {
                            $this->_getSession()->addError(Mage::helper('payperrentals')->__('You do not have permission to edit this reservation'));
                            $this->_redirect('*/*/');
                            return;
                        }
                    }
                }

            } else {
                $resnotexist =  true; // reservation does not exist
            }
        }

        if($resnotexist){
            $this->_getSession()->addError(Mage::helper('payperrentals')->__('The reservation does not exist'));
            $this->_redirect('*/*/');
        }
        Mage::register('registry_data', $reservationLoad);
        $this->loadLayout()->renderLayout();
    }

    public function saveAction()
    {
        if ($this->getRequest()->getPost()) {
            try {
                $data = $this->getRequest()->getPost();

                if ($data) {
                    $numberOfProducts = $data['number_of_products'];
                    for ($i = 0; $i < $numberOfProducts; $i++) {
                        $entityid = $data['entity_id' . $i];

                        $reservationOrderItem = Mage::getModel('payperrentals/reservationorders')->load($entityid);

                        $orderObj = Mage::getModel('sales/order')->load($reservationOrderItem->getOrderId());

                        $turnoverAr = ITwebexperts_Payperrentals_Helper_Data::getTurnoverFromQuoteItemOrBuyRequest($data['product_id' . $i], $orderObj, $data['start_date' . $i], $data['end_date' . $i]);

                        /**
                         * check inventory, add to quantity available the already booked quantity
                         */
                        $availableQuantity = ITwebexperts_Payperrentals_Helper_Inventory::getQuantity($data['product_id' . $i], $turnoverAr['before'], $turnoverAr['after']);
                        $originalReservationOrdersQuantity = $reservationOrderItem->getQty();
                        $quantityAvailableForDates = $availableQuantity + $originalReservationOrdersQuantity;
                        /**
                         * Commented out part below disabled overbooking, but jquery
                         * will show error so for now we'll disable it
                         */
//                        if ($quantityAvailableForDates < $data['qty' . $i] ) {
//                            $this->_getSession()->addError(Mage::helper('payperrentals')->__('There is not enough inventory available'));
//                            $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
//                            return $this;
//                        }

                        $reservationOrderItem->setStartTurnoverBefore($turnoverAr['before'])
                            ->setEndTurnoverAfter($turnoverAr['after'])
                            ->setQty($data['qty' . $i])
                            ->setProductId($data['product_id' . $i])
                            ->setStartDate($data['start_date' . $i])
                            ->setEndDate($data['end_date' . $i])
                            ->setProductType('reservation')
                            ->setComments($data['comments' . $i]);

                        if ($entityid) {
                            $reservationOrderItem->setId($entityid);
                        }
                        $reservationOrderItem->save();
                        ITwebexperts_Payperrentals_Helper_Inventory::updateInventory($data['product_id' . $i]);

                        // Also update buy request in order item table
                        if($data['order_item_id' . $i] != ('' || 0)){

                        ITwebexperts_Payperrentals_Helper_Inventory::updateOrderBuyRequestStartEndDates
                            ($data['order_item_id' . $i], date('Y-m-d H:i:s', strtotime($data['start_date' . $i])), date('Y-m-d H:i:s', strtotime($data['end_date' . $i])));
                        }

                        /** if is for order, update sales_flat_order_item table */
                        if($reservationOrderItem->getOrderItemId() != 0){
                            Mage::getModel('payperrentals/reservationorders')->updateSalesFlatOrderItem($data['order_item_id' . $i],$data['product_id' . $i]);
                        }

                    }

                    $this->_getSession()->addSuccess('Reservation Saved');
                    $this->_getSession()->setFormData(false);
                    $this->_redirect('*/*/index');
                }
            } catch (Exception $e) {
                $this->_getSession()->addError(Mage::helper('payperrentals')->__('An error occurred while saving the reservation data. Please review the log and try again.'));
                Mage::logException($e);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return $this;
            }
        }
    }

    public function deleteAction(){
        $id = $this->getRequest()->getParam('id');
        if($id != 0 || $id != null){
        $res = Mage::getModel('payperrentals/reservationorders')->load($id);
            $productid = $res->getProductId();
            $res->delete();
            ITwebexperts_Payperrentals_Helper_Inventory::updateInventory($productid);
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('payperrentals')->__('The reservation has been deleted.'));
            $this->_redirect('*/*/');
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('payperrentals')->__('The reservation does not exist.'));
            $this->_redirect('*/*/');
        }
    }
}
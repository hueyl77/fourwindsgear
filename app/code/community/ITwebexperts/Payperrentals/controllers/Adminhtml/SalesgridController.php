<?php



/**
 * Class ITwebexperts_Payperrentals_Adminhtml_SalesgridController
 */
class ITwebexperts_Payperrentals_Adminhtml_SalesgridController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed(){
        return true;
    }

    /**
     *
     */
    public function massDeleteAction()
    {
        $orderIds = $this->getRequest()->getPost('order_ids', array());
        $this->_deleteOrderCompletely($orderIds);
        $this->_redirect('adminhtml/sales_order/');
    }

    /**
     *
     */
    public function massReserveAction()
    {
        $orderIds = $this->getRequest()->getPost('order_ids', array());
        $this->_reserveOrders($orderIds);
        $this->_redirect('adminhtml/sales_order/');
    }

    /**
     * @param $orderIds
     * @return int
     */
    protected function _reserveOrders($orderIds)
    {
        foreach ($orderIds as $orderId) {
            $this->_reserveOrdersById($orderId);
        }
        return count($orderIds);
    }

    /**
     * @param $orderId
     */
    protected function _reserveOrdersById($orderId)
    {
        $order = Mage::getModel('sales/order')->load($orderId, 'entity_id');
        $iStatus =  Mage::getStoreConfig(ITwebexperts_Payperrentals_Helper_Config::XML_PATH_RESERVATION_STATUS);
        if(ITwebexperts_Payperrentals_Helper_Config::reserveByStatus() && $order->getStatus() != $iStatus){
            ITwebexperts_Payperrentals_Helper_Data::reserveOrder( $order->getItemsCollection(), $order);
            $order->setStatus($iStatus);
            $order->save();
        }
    }

    /**
     * @param $orderIds
     * @return int
     */
    protected function _deleteOrderCompletely($orderIds)
    {
        foreach ($orderIds as $orderId) {
            $this->_deleteOrderCompletelyById($orderId);
        }
        return count($orderIds);
    }

    /**
     * @param $order
     */
    protected function _deleteOrderCompletelyById($order)
    {
        $coreResource = Mage::getSingleton('core/resource');
        $write = $coreResource->getConnection('core_write');
        if (is_object($order)) {
            $orderId = $order->getId();
            $orderIncrement = $order->getIncrementId();
            $orderId = $order->getId();
        } else {
            $orderId = intval($order);
            $order = Mage::getModel('sales/order')->load($orderId, 'entity_id');
            $orderIncrement = $order->getIncrementId();
        }
        if ($orderId) {
            $orderItemCollection = Mage::getModel('sales/order_item')
                ->getCollection()
            ->addAttributeToFilter('order_id', $orderId)
            ->addAttributeToSelect('product_id');
            $productsIds = array();
            foreach($orderItemCollection as $item){
                $productsIds[] = $item->getProductId();
            }
            if ($order->getQuoteId()) {
                $write->query("DELETE FROM `" . $coreResource->getTableName('sales_flat_quote') . "` WHERE `entity_id`=" . $order->getQuoteId());
                $write->query("DELETE FROM `" . $coreResource->getTableName('sales_flat_quote_address') . "` WHERE `quote_id`=" . $order->getQuoteId());
                $write->query("DELETE FROM `" . $coreResource->getTableName('sales_flat_quote_item') . "` WHERE `quote_id`=" . $order->getQuoteId());
                $write->query("DELETE FROM `" . $coreResource->getTableName('sales_flat_quote_payment') . "` WHERE `quote_id`=" . $order->getQuoteId());
            }
            $order->delete();
            $write->query("DELETE FROM `" . $coreResource->getTableName('sales_flat_order_grid') . "` WHERE `entity_id`=" . $orderId);
            $write->query("DELETE FROM `" . $coreResource->getTableName('sales_flat_order_address') . "` WHERE `parent_id`=" . $orderId);
            $write->query("DELETE FROM `" . $coreResource->getTableName('sales_flat_order_item') . "` WHERE `order_id`=" . $orderId);
            $write->query("DELETE FROM `" . $coreResource->getTableName('sales_flat_order_payment') . "` WHERE `parent_id`=" . $orderId);
            $write->query("DELETE FROM `" . $coreResource->getTableName('sales_payment_transaction') . "` WHERE `order_id`=" . $orderId);
            $write->query("DELETE FROM `" . $coreResource->getTableName('sales_flat_order_status_history') . "` WHERE `parent_id`=" . $orderId);

            $write->query("DELETE FROM `" . $coreResource->getTableName('sales_flat_invoice') . "` WHERE `order_id`=" . $orderId);
            $write->query("DELETE FROM `" . $coreResource->getTableName('sales_flat_creditmemo') . "` WHERE `order_id`=" . $orderId);
            $write->query("DELETE FROM `" . $coreResource->getTableName('sales_flat_shipment') . "` WHERE `order_id`=" . $orderId);
            $write->query("DELETE FROM `" . $coreResource->getTableName('sales_order_tax') . "` WHERE `order_id`=" . $orderId);

            $write->query("DELETE FROM `" . $coreResource->getTableName('payperrentals_reservationorders') . "` WHERE `order_id`=" . $orderId);
            $write->query("DELETE FROM `" . $coreResource->getTableName('payperrentals_sendreturn') . "` WHERE `order_id`=" . $orderId);
            try {
                ITwebexperts_Payperrentals_Helper_Inventory::updateInventory($productsIds);
            }
            catch(Exception $e){

            }


        }
    }
    /**
     *
     */
    public function exportIcalAction(){
		require_once(Mage::getBaseDir().'/lib/iCal/Eluceo/iCal/Component.php');
		require_once(Mage::getBaseDir().'/lib/iCal/Eluceo/iCal/PropertyBag.php');
		require_once(Mage::getBaseDir().'/lib/iCal/Eluceo/iCal/Property.php');
		require_once(Mage::getBaseDir().'/lib/iCal/Eluceo/iCal/Component/Calendar.php');
		require_once(Mage::getBaseDir().'/lib/iCal/Eluceo/iCal/Component/Event.php');
		//date_default_timezone_set('Europe/Berlin');

		$vCalendar = new \Eluceo\iCal\Component\Calendar(Mage::app()->getStore()->getName());
		$coll = Mage::getModel('payperrentals/reservationorders')
				->getCollection()
				->groupByOrderId();

        if(urldecode($this->getRequest()->getParam('store'))) {
            $coll->getSelect()->joinLeft(array('so'=> Mage::getSingleton('core/resource')->getTableName('sales_flat_order')), 'main_table.order_id = '.'so.entity_id', array('so.store_id as store_id'));
            $coll->getSelect()->where('so.store_id=?', $this->getRequest()->getParam('store'));
        }

		foreach($coll as $item){
            $_order = Mage::getModel('sales/order')->load($item->getOrderId());
            $_address = $_order->getShippingAddress();

			$vEvent = new \Eluceo\iCal\Component\Event();
			$vEvent->setDtStart(new \DateTime($item->getStartDate()));
			$vEvent->setDtEnd(new \DateTime($item->getEndDate()));
            $vEvent->setDescription($_order->getCustomerFirstname() . ' ' . $_order->getCustomerLastname());
            $_addressString = '';
            if($_address instanceof Mage_Sales_Model_Order_Address){
                $_addressString .= implode(' ', $_address->getStreet()) . ', ' . $_address->getPostcode() . ', ' . $_address->getCity();
            }
            $vEvent->setLocation($_order->getCustomerFirstname() . ' ' . $_order->getCustomerLastname() . (($_addressString != '') ? ', ' . $_addressString : ''));
			//$vEvent->setNoTime(true);
			$vEvent->setSummary($item->getOrderId());
			//$vEvent->setUseTimezone(true);
			$vCalendar->addEvent($vEvent);
		}

		header('Content-Type: text/calendar; charset=utf-8');
		header('Content-Disposition: attachment; filename="cal.ics"');
		echo $vCalendar->render();
	}
}

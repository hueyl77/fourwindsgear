<?php
class ITwebexperts_Maintenance_Model_Observer extends Mage_Core_Model_Observer
{

    /**
     * Observer on payperrentals/inventory getquantity that removes quantity in maintenance from available quantity
     * We check both the quantity in the product EAV field maintenance_quantity and also
     * quantity that is under maintenance in a maintenance report when not using specific dates
     *
     * @param Varien_Event_Observer $observer
     */

    function removeMaintenanceFromGetQuantity(Varien_Event_Observer $observer)
    {
        $maintenanceQuantity = Mage::getResourceModel('catalog/product')->getAttributeRawValue($observer->getProduct(),'maintenance_quantity',Mage::app()->getStore()->getStoreId());
        $maintenanceQuantityReports = Mage::helper('simaintenance')->getReportMaintenanceQuantity($observer->getProduct());
        $retQty = $observer->getResult()->getRetQty();
        $retQty = $retQty - ($maintenanceQuantity + $maintenanceQuantityReports);
        $observer->getResult()->setRetQty($retQty);
    }

    /**
     * Called from payperrentals/inventory helper after_booked event of getBooked()
     *
     * Observer that adds maintenance quantity when using specific maintenance dates to
     * the serialized inventory field of the product
     *
     * @param Varien_Event_Observer $observer
     */

    function addMaintenanceToSerialized(Varien_Event_Observer $observer)
    {
        $booked = $observer->getResult()->getBooked();
        $reservedCollection = $observer->getReservedCollection();
        /** @var  $maintenanceColl ITwebexperts_Maintenance_Model_Mysql4_Items_Collection */
        foreach($booked as $productid=>$booking)
        {
            $maintenanceColl = Mage::getModel('simaintenance/items')->getCollection();
            $maintenanceColl->addFieldToFilter('product_id',$productid);
            foreach($maintenanceColl as $maintenanceItem)
            {
                if(is_null($maintenanceItem->getStartDate()) || $maintenanceItem->getSpecificDates()){
                    continue;
                }
            $start = strtotime($maintenanceItem->getStartDate());
            $end = strtotime($maintenanceItem->getEndDate());
                $usetimes = Mage::getResourceModel('catalog/product')->getAttributeRawValue($productid,'payperrentals_use_times',Mage::app()->getStore()->getStoreId());
                if ((date('H:i:s', $start) != '00:00:00'
                    || (date('H:i:s', $end) != '23:59:00'
                        && date('H:i:s', $end) != '23:58:59') ||
                $usetimes == 1
                )
                ) {
                    $configHelper = Mage::helper('payperrentals/config');
                    $timeIncrement = $configHelper->getTimeIncrement() * 60;
                } else {
                    $timeIncrement = 3600 * 24;
                }

                while ($start < $end) {
                    $dateFormatted = date('Y-m-d H:i', $start);
                    if (!isset($booking[$dateFormatted])) {
                        $vObject = new Varien_Object();
                        $vObject->setQty($maintenanceItem->getQuantity());
                        $vObject->setOrders(array('m'));
                        $booking[$dateFormatted] = $vObject;
                    } else {
                        $vObject = $booking[$dateFormatted];
                        $vObject->setQty($vObject->getQty() + $maintenanceItem->getQuantity());
                        $orderArr = $vObject->getOrders();
                        $orderArr = array_merge($orderArr,array('m'));
                        $vObject->setOrders($orderArr);
                    }
                    $booked[$productid][$dateFormatted] = $vObject;
                    $start += $timeIncrement;
                }
        }

        }
        $observer->getResult()->setBooked($booked);
    }
}
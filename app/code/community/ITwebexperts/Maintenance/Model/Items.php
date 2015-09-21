<?php
class ITwebexperts_Maintenance_Model_Items extends Mage_Core_Model_Abstract
{
    public function __construct()
    {
        $this->_init('simaintenance/items');
        parent::_construct();
    }

    /**
     * Finds products that need maintenance from automatic maintenance (periodic) and adds maintenance record
     */

    public function createMaintenanceRecords(){
        $periodicColl = Mage::getModel('simaintenance/periodic')->getCollection();
        foreach($periodicColl as $periodic){
            if(Mage::helper('simaintenance')->isProductInMaintenanceWindow($periodic)){
                if(Mage::helper('simaintenance')->alreadyUpdated($periodic)){
                    $maintenancelogmessage = "Record already existed for automated periodic maintenance for product id " . $periodic->getProductId();
                    Mage::log($maintenancelogmessage,NULL,"maintenance.log");
                    continue;
                }
                $maintenanceDateArray = Mage::helper('simaintenance')->getMaintenanceDateArray($periodic);
                Mage::helper('simaintenance')->addMaintenanceRecords($periodic,$maintenanceDateArray);
                $maintenancelogmessage = "Product ID: " . $periodic->getProductId() . " has had a maintenance record added";
                Mage::log($maintenancelogmessage,NULL,"maintenance.log");
            }
        }
    }
}
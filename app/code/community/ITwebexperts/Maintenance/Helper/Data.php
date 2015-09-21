<?php
class ITwebexperts_Maintenance_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Get maintenance templates and return them as a JSON array
     *
     * @return array|string
     */
    public function templatesToJson() {
        $templates = Mage::getModel('simaintenance/snippets')->getCollection();
        $jsonarray = array();
        foreach($templates as $template){
            $jsonarray[$template->getId()] = array(
                'title'=>$template->getTitle(),
                'description'=>$template->getSnippet()
            );
        }
        $jsonarray = json_encode($jsonarray);
        return $jsonarray;
    }


    /**
     * Returns a list of maintainers useful for form selects
     *
     * @return array
     */

    public function getMaintainers(){
        $maintainers = Mage::getModel('simaintenance/maintainers')->getCollection();
        $maintainers->getSelect()->joinLeft(array('admins'=>Mage::getConfig()->getTablePrefix() . 'admin_user'),'main_table.admin_id=admins.user_id');
        $maintenanceArray = array('-1'=>Mage::helper('simaintenance')->__('Please Select...'));
        foreach($maintainers as $maintain){
            $maintenanceArray[$maintain->getMaintainerId()] = $maintain->getFirstname() . ' ' . $maintain->getLastname();
        }
        return $maintenanceArray;
    }

    /**
     * Returns array of admins for a form
     *
     * @return array
     */

    public function getAdmins(){
        $maintainers = Mage::getModel('admin/user')->getCollection();
        $maintenanceArray = array('-1'=>Mage::helper('simaintenance')->__('Please Select...'));
        foreach($maintainers as $maintain){
            $maintenanceArray[$maintain->getUserId()] = $maintain->getFirstname() . ' ' . $maintain->getLastname();
        }
        return $maintenanceArray;
    }

    public function arrayToString($array){
        $string = implode(',',$array);
        return $string;
    }

    /**
     * Get a collection of rental products
     *
     * @return mixed
     */

    public function loadProductCollection(){
        return Mage::getModel('catalog/product')->getCollection()->addAttributeToFilter('type_id','reservation')->addAttributeToSelect('name');

    }

    /**
     * Returns a form select of products
     *
     * @param $prodCollection
     * @return array
     */

    public function getProductNamesArray($prodCollection){
        $prodArray = array('-1'    =>  'Please Select');
        foreach ($prodCollection as $productItem){
            $prodArray[$productItem->getId()] = $productItem->getName();
        }
        asort($prodArray);
        return $prodArray;
    }

    /**
     * Returns a list of serials numbers and their status for a form. Only the serial number is in the value.
     *
     * @param $productid
     * @return array
     */

    public function getSerialValues($productid){
        $serials = Mage::getModel('payperrentals/serialnumbers')->getCollection()->addEntityIdFilter($productid);
        $values = "";
        foreach($serials as $serial){
            $values[] = array(
                'label' =>  $serial->getSn() . ' - ' . Mage::getModel('payperrentals/serialnumbers')->statusToText($serial->getStatus()),
                'value' =>  $serial->getSn()
            );
        }
        return $values;
    }

    /**
     * Returns the serial numbers in a maintenance report
     *
     * @param $id
     * @return mixed
     */

    public function getSerialValue($id){
        $record = Mage::getModel('simaintenance/items')->load($id);
        return $record->getSerials();
    }



    /**
     * Checks if an automated maintenance record was already added in the last 24 hours
     */

    public function alreadyUpdated($periodic){
        $maintenanceColl = Mage::getModel('simaintenance/items')->getCollection()
            ->addFieldToFilter('product_id',$periodic->getProductId())
            ->addFieldToFilter('added_from','auto')
            ->addFieldToFilter('date_added',array(
                'from'  =>  date('Y-m-d'),
                'to'    =>  date('Y-m-d'),
            ))->load();
        $log = $maintenanceColl->getSelect()->__toString();
        Mage::log($log,NULL,"maintenance.log");
            if($maintenanceColl->count() >= 1){
                return true;
            } else {return false;}
    }

    /**
     * Adds maintenance record to table, very similar to maintenance controller saveAction()
     *
     * @param $periodic
     * @param $maintenanceDateArray
     */

    public function addMaintenanceRecords($periodic,$maintenanceDateArray){
            $data['date_added'] = date('Y-m-d');
            $data['product_id'] = $periodic->getProductId();
            $quantity = $this->getQuantityToMaintain($periodic->getProductId());
            $data['quantity'] = $quantity;
            $snippet = Mage::getModel('simaintenance/snippets')->load($periodic->getSnippetId());
            $data['summary'] = $snippet->getTitle();
            $data['description'] = $snippet->getSnippet();
            $data['status'] = 1;
            $data['maintainer_id'] = $periodic->getMaintainerId();
            $data['added_from'] = "auto";
            if($this->productUsesSerials($periodic->getProductId())) {
                $serials = $this->assignSerials($quantity, $maintenanceDateArray);
                $data['serials'] = $serials;
                Mage::helper('simaintenance')->setSerialsStatus($serials,$data);
            }
            $maintenanceItem = Mage::getModel('simaintenance/items')->load();
            $maintenanceItem->setData($data);
            $maintenanceItem->save();

            Mage::helper('simaintenance/emails')->sendMaintenanceReportEmail($maintenanceItem->getId());
    }

    public function productUsesSerials($productid){
        $product = Mage::getModel('catalog/product')->load($productid);
        if($product->getPayperrentalsUseSerials()){
            return true;
        } else {
            return false;
        }
    }

    /**
     * Assigns serials that will be maintained and returns them in a comma separated string
     *
     * @param $quantity
     * @param $maintenanceDateArray
     * @return string
     */

    public function assignSerials($quantity,$maintenanceDateArray){
       $pos = 1;
        $serials = '';
        foreach($maintenanceDateArray as $maintenance){
           $serials[] = $maintenance['serial'];
            $pos++;
            if($pos > $quantity){
                break;
            }
       }
        return $this->arrayToString($serials);
    }

    /**
     * Find out how many products should be maintained based on percentage
     * and how many are available
     *
     * @param $productid
     * @return float
     */

    public function getQuantityToMaintain($productid){
        $percentToMaintain = Mage::getStoreConfig('payperrentals/maintenance/percent_maintain',Mage::app()->getStore());
        $now = new DateTime();
        $monthfromnow = new DateTime();
        $monthfromnow->modify('+1 month');
        $availableQuantity = Mage::helper('payperrentals/inventory')
            ->getQuantity($productid,$now->format('Y-m-d'),$monthfromnow->format('Y-m-d'));
        return round($percentToMaintain * $availableQuantity);
    }

    /**
     * If the maintenance date is within 24 hours of current date time, return true
     *
     * @param $periodic
     * @return bool
     */

    public function isProductInMaintenanceWindow($periodic){
        $maintenanceDateArray = $this->getMaintenanceDateArray($periodic);
        foreach($maintenanceDateArray as $datesn){
            if((time() - $datesn['timestamp']) < 86400){
                return true;
            }
        }
        return false;
    }

    /**
     * gets array of when each serial should be maintained closest to current date time by taking
     * the product created at date (or serial date acquired) and adding to it
     * the maintenance frequency, then checking if that is within 24 hours of current date time.
     *
     * @param $periodic
     */

    public function getMaintenanceDateArray($periodic){
        /** add 2 to periodic type because we start with days instead of seconds & minutes
         * See app/code/community/ITwebexperts/Payperrentals/Helper/Data.php where types are defined
         */
        $periodicType = $periodic->getFrequencyType() + 2;
        $frequencyInSeconds = Mage::helper('payperrentals')
            ->getPeriodInSeconds($periodic->getFrequencyQuantity(),$periodicType);
        $createdAtArray = $this->getCreatedAtArray($periodic->getProductId());
        $maintenanceDateArray = array();
            foreach($createdAtArray as $serial => $timestamp){
                for($i=1;$i<1000000;$i++){
                    $createdPlusFrequency = $timestamp + ($frequencyInSeconds * $i);
                    /** find closest maintenance date to current date time minus 24 hours */
                if($createdPlusFrequency >= (time() - 86400)){
                    $maintenanceDateArray[] = array(
                        'timestamp' => $createdPlusFrequency,
                        'serial'    => $serial,
                        'date'      => date('Y-m-d',$createdPlusFrequency));
                break;
                };
            }
        }
        return $maintenanceDateArray;
    }

    /**
     * Returns array of when product was created at timestamp, or when serial numbers were acquired
     *
     * @param $productid
     * @return array of timestamps by serial number or 'createdat' for non serial product
     */

    public function getCreatedAtArray($productid){
        $product = Mage::getModel('catalog/product')->load($productid);
        $createdAtArray = array();
        if($product->getPayperrentalsUseSerials() == 1){
            $serialColl = Mage::getModel('payperrentals/serialnumbers')->getCollection()->addEntityIdFilter($productid);
            foreach($serialColl as $serial){
                $createdAtArray[$serial->getSn()] = strtotime($serial->getDateAcquired());
            }
        } else {
            $createdAtArray['createdat'] = strtotime($product->getCreatedAt());
        }
        return $createdAtArray;
    }

    /**
     * If status is not 3 (complete) serials are set to maintenance status
     *
     * @param $serials
     * @param $data
     */

    public function setSerialsStatus($serials,$data){
        if(!is_array($serials)){
            $serials = explode(',',$serials);
        }
        foreach($serials as $serial) {
            /** cehck if status should be reserved */
            $statusReserved = Mage::getModel('simaintenance/status')->load($data['status'])->getReserveInventory();
            if($statusReserved) {
                Mage::getResourceSingleton('payperrentals/serialnumbers')
                    ->updateStatusBySerial($serial, 'M');
            } else {
                Mage::getResourceSingleton('payperrentals/serialnumbers')
                    ->updateStatusBySerial($serial, 'A');
            }
        }
    }

    /**
     * Returns maintenance quantity for maintenance reports for a certain product id.
     * It will only return products that do not use serials, and do not use specific
     * maintenance dates because those are already counted using an observer
     *
     * @param $productid
     */

    public function getReportMaintenanceQuantity($productid,$addserials=false,$addpprmaint=false){
        /** @var $reportsColl ITwebexperts_Maintenance_Model_Mysql4_Items_Collection */
        $maintenancequantity = 0;
        $useSerials = Mage::getModel('catalog/product')->load($productid)->getPayperrentalsUseSerials();
        if($useSerials == 1) {
            if ($addserials) {
                $serialcoll = Mage::getModel('payperrentals/serialnumbers')->getCollection()->addFieldToFilter('entity_id',$productid)->addFieldToFilter('status','M');
                return count($serialcoll);
            } else {
                return 0;
            }
        }
        $reportsColl = Mage::getModel('simaintenance/items')->getCollection()->addFieldToFilter('product_id',$productid);
        foreach($reportsColl as $report){
            /** add eav maintenance field if set to true  */
            /** if status does not have reserve inventory set to 1, don't add to maintenance quantity */
            if(Mage::getModel('simaintenance/status')->load($report->getStatus())->getReserveInventory() == 0){
                continue;
            }
            if($this->isWithinDates($report)) {
                $maintenancequantity += $report->getQuantity();
            }
        }
        if($addpprmaint){
            $maintenancequantity += Mage::helper('payperrentals')->getAttributeCodeForId($productid,'maintenance_quantity');
        }
        return $maintenancequantity;
    }

    /**
     * Returns true if the product doesn't use specific dates for maintenance, or
     * the current date time is within the report start and end dates
     *
     * @param $report
     */

    public function isWithinDates($report){
        if($report->getSpecificDates() == 0){
            return true;
        }

        $startTimeStamp = strtotime($report->getStartDate());
        $endTimeStamp = strtotime($report->getEndDate());
        $now = time();
        if($now >= $startTimeStamp && $now <= $endTimeStamp){
            return true;
        } else {return false;}
    }
}
<?php
class ITwebexperts_Maintenance_Model_Status extends Mage_Core_Model_Abstract
{
    public function __construct()
    {
        $this->_init('simaintenance/status');
        parent::_construct();
    }

    /**
     * Status array for edit status form
     *
     * @return array
     */

    public function getStatusArray($grid=false){
        $statusArray = array();
        if($grid==false) {
            $statusArray['-1'] = 'Please Select';
        }
        /** @var $statusColl ITwebexperts_Maintenance_Model_Status */
        $statusColl = Mage::getModel('simaintenance/status')->getCollection();
        foreach($statusColl as $statusItem){
            $statusArray[$statusItem->getStatusId()] = $statusItem->getStatus();
        }
        return $statusArray;
    }

}
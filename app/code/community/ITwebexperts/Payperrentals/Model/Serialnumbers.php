<?php

class ITwebexperts_Payperrentals_Model_Serialnumbers extends Mage_Core_Model_Abstract{
	
	protected function _construct(){
		$this->_init('payperrentals/serialnumbers');
	}

    public function statusToText($status){
        switch($status) {
            case 'A':
                return  Mage::helper('payperrentals')->__('Available');
            break;
            case 'B':
                return  Mage::helper('payperrentals')->__('Broken');
            break;
            case 'O':
                return  Mage::helper('payperrentals')->__('Out');
            break;
            case 'M':
                return  Mage::helper('payperrentals')->__('Maintenance');
            break;
        }
    }

    /**
     * Load serial number record by serial number
     *
     * @param $sn
     * @return mixed
     */

    public function loadBySerial($sn){
        $collection = $this->getCollection()
            ->addFieldToFilter('sn',$sn);
        return $collection->getFirstItem();
    }

    /**
     * If serial date acquired is null, set the date to the date
     * the product was entered in in the database
     *
     * @param $date
     */

    public function ifSerialIsNullSetToProductCreatedDate($date,$sn){
        if(is_null($date)){
        $productid = $this->loadBySerial($sn)->getEntityId();
            $createddate = Mage::getModel('catalog/product')->load($productid)->getCreatedAt();
            $createddate = date("Y-m-d",strtotime($createddate));
            return $createddate;
        } else {
            return $date;
        }
    }
	
}

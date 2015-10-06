<?php
class ITwebexperts_Maintenance_Model_Periodic extends Mage_Core_Model_Abstract
{
    public function __construct()
    {
        $this->_init('simaintenance/periodic');
        parent::_construct();
    }

    public function getFrequencyArray(){
        return array(
            '-1'    =>  Mage::helper('simaintenance')->__('Please Select'),
            '1' =>  $this->getTypeById(1),
            '2' =>  $this->getTypeById(2),
            '3' =>  $this->getTypeById(3),
            '4' =>  $this->getTypeById(4)
        );
    }

    public function getTypeById($id){
        switch ($id){
            case "1":
                return Mage::helper('simaintenance')->__('Day');
            break;
            case "2":
                return Mage::helper('simaintenance')->__('Week');
            break;
            case "3":
                return Mage::helper('simaintenance')->__('Month');
            break;
            case "4":
                return Mage::helper('simaintenance')->__('Year');
            break;
        }
    }
}
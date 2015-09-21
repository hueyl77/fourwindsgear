<?php

class ITwebexperts_Payperrentals_Model_Reservationquotes extends Mage_Core_Model_Abstract{


    protected $_product;

    protected function _construct(){
        $this->_init('payperrentals/reservationquotes');
    }

    public function getProduct(){
        if(!$this->_product){
            $this->_product = Mage::getModel('catalog/product')->load($this->getProductId());
        }
        return $this->_product;
    }

}

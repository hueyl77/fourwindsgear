<?php

class ITwebexperts_Payperrentals_Model_Product_Backend_Serialnumbers extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{

    protected function _getResource()
    {
        return Mage::getResourceSingleton('payperrentals/serialnumbers');
    }

    protected function _getProduct()
    {
        return Mage::registry('product');
    }

    public function validate($object)
    {

        $sns = $object->getData($this->getAttribute()->getName());
        if (empty($sns)) {
            return $this;
        }


        return $this;
    }

    public function afterSave($object)
    {
        if (is_object($this->_getProduct())) {
            $stockData = $this->_getProduct()->getStockData();
            $stockData['qty'] = $this->_getProduct()->getPayperrentalsQuantity();
            $stockData['is_in_stock'] = 1;
            $stockData['manage_stock'] = 0;
            $stockData['use_config_manage_stock'] = 0;
            $this->_getProduct()->setStockData($stockData);

            if (Mage::app()->getRequest()->getActionName() == 'duplicate') {
                $this->_getProduct()->setPayperrentalsUseSerials(ITwebexperts_Payperrentals_Model_Product_Useserials::STATUS_DISABLED);
            }

            if ($this->_getProduct()->getPayperrentalsUseSerials() == ITwebexperts_Payperrentals_Model_Product_Useserials::STATUS_ENABLED) {
                $sns = $object->getData($this->getAttribute()->getName());
                if (is_null($sns)) {
                    $sns = Mage::getModel('payperrentals/serialnumbers')->getCollection()
                        ->addEntityIdFilter($this->_getProduct()->getId())
                        ->getItems();
                }

//                if ((!is_array($sns) || $this->_getProduct()->getPayperrentalsQuantity() != count($sns))) { //I need to check for broken status
//                    Mage::getSingleton('adminhtml/session')->setData('ppr', Mage::app()->getRequest()->getParam('product'));
//                    Mage::throwException('Number of items is different than number of serial numbers!');
//                    return $this;
//                }

                Mage::getResourceSingleton('payperrentals/serialnumbers')->deleteByEntityId($object->getId());

                foreach ($sns as $k => $sn) {
                    if (!is_numeric($k)) continue;

                    $ex = Mage::getModel('payperrentals/serialnumbers')
                        ->setEntityId($this->_getProduct()->getId())
                        ->setSn($sn['sn'])
                        ->setStatus($sn['status'])
                        ->setDateAcquired(ITwebexperts_Payperrentals_Helper_Date::toMysqlDate($sn['dateacquired']),true)
                        // even if advanced inventory is not installed, we can still set warehouse id will not throw error
                        ->setWarehouseId($sn['warehouseid'])
                        ->save();
                }
            }
        }
        return $this;
    }
}

<?php
class ITwebexperts_Payperrentals_Adminhtml_ProductgridController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed(){
        return true;
    }

    /**
     * Converts product types from simple to reservation or reservation to simple
     * from the catalog product massaction grid
     */
    public function massConvertAction() {
        $convertType = $this->getRequest()->getParam('convertoption');
        $productArray = $this->getRequest()->getParam('product');

        /** Convert simple to reservation */
        if($convertType == 'simpletoreservation'){
            foreach($productArray as $product){
                $product = Mage::getModel('catalog/product')->load($product);
                $producttype = $product->getTypeId();
                $type = 'simple';
                $check = $this->checkType($producttype, $type);
                if($check == 'error'){return $this;} else {
                    $product->setTypeId('reservation');

                    /** Set rental inventory to what the simple product inventory was */
                    $inventory = $product->getStockItem()->getQty();
                    $product->setPayperrentalsQuantity($inventory);
                    $product->save();
                }
            }
            $Msg = Mage::helper('payperrentals')->__('Products have been converted from simple to reservation');
            $this->_getSession()->addSuccess($Msg);
            $this->_redirect('adminhtml/catalog_product');
            return $this;
        }

        /** Convert reservation to simple */
        if($convertType == 'reservationtosimple'){
            foreach($productArray as $product){
                $product = Mage::getModel('catalog/product')->load($product);
                $producttype = $product->getTypeId();
                $type = 'reservation';
                $check = $this->checkType($producttype, $type);
                if($check == 'error'){return $this;} else {
                    $product->setTypeId('simple');

                    /** Set simple product inventory to same as the reservation product inventory */
                    $rentalInventory = $product->getPayperrentalsQuantity();
                    $stockItem =Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);
                    $stockItem->setData('qty', $rentalInventory);
                    $product->save();
                }
            }
            $Msg = Mage::helper('payperrentals')->__('Products have been converted from reservation to simple');
            $this->_getSession()->addSuccess($Msg);
            $this->_redirect('adminhtml/catalog_product');
            return $this;
        }
    }

    /**
     * Check product type vs the desired product type, redirect if not correct
     *
     * @param $producttype
     * @param $type
     */

    private function checkType($producttype, $type){
        if($producttype != $type){
            $errorMsg = Mage::helper('payperrentals')->__('All products must be simple product type');
            $this->_getSession()->addError($errorMsg);
            $this->_redirect('adminhtml/catalog_product');
            return 'error';
        }
    }
}
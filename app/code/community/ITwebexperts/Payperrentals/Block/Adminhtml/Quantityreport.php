<?php

/**
 * Class ITwebexperts_Payperrentals_Block_Adminhtml_Quantityreport
 */
class ITwebexperts_Payperrentals_Block_Adminhtml_Quantityreport extends Mage_Core_Block_Template
{

    /**
     *
     */
    public function __construct()
    {
        /*$this->_controller = 'adminhtml_quantityreport';
        $this->_blockGroup = 'payperrentals';
        $this->_headerText = Mage::helper('payperrentals')->__('Quantity Report');
        parent::__construct();
       //die();*/
        //$this->setTemplate('payperrentals/qreport.phtml');
    }

    /**
     * @var
     */
    protected $_myCollection;
    /**
     * @var
     */
    protected $_pager;

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $pager = $this->getLayout()
            ->createBlock('payperrentals/adminhtml_html_pager', 'my.pager')
            ->setCollection($this->getMyCollection());
        if (urldecode($this->getRequest()->getParam('limit'))) {
            $pager->setLimit($this->getRequest()->getParam('limit'));
        }
        $this->setChild('pager', $pager);
        $this->_pager = $pager;

        $this->getMyCollection()->load();

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPager()
    {
        return $this->_pager;
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * @return mixed
     */
    protected function getMyCollection()
    {
        if (is_null($this->_myCollection)) {
            $this->_myCollection = Mage::getModel('catalog/product')->getCollection()
                ->addAttributeToFilter(
                    array(
                        array('attribute' => 'type_id', 'eq' => 'reservation')
                        //array('attribute'=>'type_id', 'eq'=>'configurable')
                    )
                );
            if(Mage::helper('itwebcommon')->isVendorAdmin()){
                $this->_myCollection->addAttributeToFilter(
                        'vendor_id', array('eq'   =>  Mage::getSingleton('vendors/session')->getId())
                );
            }
            if (urldecode($this->getRequest()->getParam('productName'))) {
                $prodArr = explode(';', urldecode($this->getRequest()->getParam('productName')));
                $likeArr = array();
                foreach ($prodArr as $iProd) {
                    $likeArr[] = array('attribute' => 'name', 'like' => '%' . $iProd . '%');
                }
                $this->_myCollection->addFieldToFilter($likeArr);
            }

            if (urldecode($this->getRequest()->getParam('productSku'))) {
                $prodArr = explode(';', urldecode($this->getRequest()->getParam('productSku')));
                $likeArr = array();
                foreach ($prodArr as $iProd) {
                    $likeArr[] = array('attribute' => 'sku', 'like' => '%' . $iProd . '%');
                }
                $this->_myCollection->addFieldToFilter($likeArr);
            }

            if ($_categoryId = urldecode($this->getRequest()->getParam('productCategory'))) {
                $this->_myCollection->addCategoryFilter(Mage::getModel('catalog/category')->load($_categoryId));
            }

            if (urldecode($this->getRequest()->getParam('store'))) {
                $this->_myCollection->addStoreFilter($this->getRequest()->getParam('store'));
            }
        }
        if (urldecode($this->getRequest()->getParam('limit'))) {
            $this->_myCollection->setPageSize($this->getRequest()->getParam('limit'));
        }
        if (urldecode($this->getRequest()->getParam('p'))) {
            $this->_myCollection->setCurPage($this->getRequest()->getParam('p'));
        }

        $this->_myCollection->addAttributeToSort('name', 'ASC')->addAttributeToFilter('status', array('eq' => 1));

        return $this->_myCollection;
    }

    public function getEventsUrl(){
        if(Mage::app()->getStore()->isAdmin()) {
            return Mage::getUrl("payperrentals_admin/adminhtml_ajax/getevents/", array('form_key' => Mage::getSingleton('core/session')->getFormKey()));
        }
        if(Mage::helper('itwebcommon')->isVendorInstalled()) {
            return Mage::getUrl('vendors/siinventory/getevents', array('form_key' => Mage::getSingleton('core/session')->getFormKey()));
        }
    }

    public function getDateDetailsUrl(){
        if(Mage::app()->getStore()->isAdmin()) {
            return Mage::getUrl("payperrentals_admin/adminhtml_ajax/getDateDetails/",array('form_key'=> Mage::getSingleton('core/session')->getFormKey()));
        }
        if(Mage::helper('itwebcommon')->isVendorInstalled()) {
            return Mage::getUrl('vendors/siinventory/getdatedetails', array('form_key' => Mage::getSingleton('core/session')->getFormKey()));
        }
    }


}

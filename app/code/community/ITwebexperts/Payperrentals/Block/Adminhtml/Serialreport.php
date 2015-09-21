<?php

/**
 * Class ITwebexperts_Payperrentals_Block_Adminhtml_Serialreport
 */
class ITwebexperts_Payperrentals_Block_Adminhtml_Serialreport extends Mage_Core_Block_Template{

    /**
     *
     */
    public function __construct(){
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
				->setCollection($this->getMyCollection())
				;

		$this->setChild('pager', $pager);
		$this->_pager = $pager;
		$this->getMyCollection()->load();

		return $this;
	}

    /**
     * @return mixed
     */
    public function getPager(){
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
					array('attribute'=>'type_id', 'eq'=>'reservation')
				)
			);
			$this->_myCollection->addFieldToFilter(array(
				array('attribute'=>'payperrentals_use_serials', 'eq'=>ITwebexperts_Payperrentals_Model_Product_Useserials::STATUS_ENABLED)
			));

			if(urldecode($this->getRequest()->getParam('productName'))){
				$this->_myCollection->addFieldToFilter(array(
					array('attribute'=>'name','like'=>'%'.urldecode($this->getRequest()->getParam('productName')).'%'),
				));
			}

			if(urldecode($this->getRequest()->getParam('serialName'))){
				$coll = Mage::getModel('payperrentals/serialnumbers')
						->getCollection()
						->addSelectFilter("sn like '%".urldecode($this->getRequest()->getParam('serialName'))."%'")
				;
				$idList = array();
				foreach ($coll as $item) {
					$idList[] = $item->getEntityId();
				}
				$this->_myCollection = Mage::getModel('catalog/product')->getCollection()
						->addAttributeToFilter(
					array(
						array('attribute'=>'entity_id', 'in'=>$idList)
					)
				);
			}

            if(urldecode($this->getRequest()->getParam('store'))) {
                $this->_myCollection->addStoreFilter($this->getRequest()->getParam('store'));
            }
		}

		return $this->_myCollection;
	}


}

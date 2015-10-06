<?php

/**
 * Class ITwebexperts_Payperrentals_Block_Adminhtml_Serialreport
 */
class ITwebexperts_Payperrentals_Block_Adminhtml_Serialreport extends Mage_Core_Block_Template{

    /**
     *
     */
    public function __construct(){
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
				->setCollection($this->getProductCollection());
		$this->setChild('pager', $pager);
		$this->_pager = $pager;
		$this->getProductCollection()->load();

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
	 * Get collection of reservation products that use serials
	 * filter for product name if that filter is used
	 *
     * @return mixed
     */
    public function getProductCollection()
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

	public function getSerialCollection($productid)
	{
		$serialNumber = $this->getRequest()->getParam('serialName');
		$serialColl =  Mage::getModel('payperrentals/serialnumbers')
			->getCollection()
			->addEntityIdFilter($productid);
		if($serialNumber){
			$serialColl->addFieldToFilter('sn',$serialNumber);
		}
		return $serialColl;
	}

	public function getResource($Product,$coll,$resources){
		$nameqty = '<div style="width:150px;float:left;line-height:48px;"><b>'.$Product->getName().'</b> </div>';
		foreach ($coll as $item) {
			$resources[] = array(
				'name' => $nameqty . '<div style="width:100px;float:left;line-height:48px;">'.$item->getSn().'</div>',
				'id' => $item->getSn()
			);

			$serialNumbers[] = $item->getSn();
			$nameqty = '<div style="width:150px;float:left;line-height:48px;">&nbsp;</div>';
		}
		return $resources;
	}

	public function getDatesFormat(){
		$localeDateFormat = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_LONG);
		$localeDateFormat = preg_replace("/y+/", "yyyy", $localeDateFormat);
		$monthFormat = str_replace('d', '', $localeDateFormat);
		$weekFormat = $localeDateFormat . "{ &#8212;" . $localeDateFormat . "}";
		$dayFormat = str_replace('yy', '', $localeDateFormat);
		return array($monthFormat, $weekFormat, $dayFormat);
	}

	public function getDatesFormatCalendar(){
		$localeDateFormat = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
		$localeDateFormat = preg_replace("/y+/", "yy", $localeDateFormat);
		$monthFormatC = str_replace('yy', '', $localeDateFormat);
		$monthFormatC = str_replace('MM', '', $monthFormatC);
		$localeDateFormat = preg_replace("/\/+/", "/", $localeDateFormat);
		$monthFormatC = rtrim($monthFormatC, '/');
		$weekFormatC = 'ddd ' . $monthFormatC;
		$weekFormatC = str_replace('//', '/', $weekFormatC);
		$dayFormatC = str_replace('yy', '', $localeDateFormat);
		return array($monthFormatC, $weekFormatC, $dayFormatC);

	}

}

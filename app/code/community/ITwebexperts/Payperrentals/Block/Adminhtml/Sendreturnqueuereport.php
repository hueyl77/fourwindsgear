<?php

/**
 * Class ITwebexperts_Payperrentals_Block_Adminhtml_Sendreturnqueuereport
 */
class ITwebexperts_Payperrentals_Block_Adminhtml_Sendreturnqueuereport extends Mage_Core_Block_Template{

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

		return $this->_myCollection;
	}


}

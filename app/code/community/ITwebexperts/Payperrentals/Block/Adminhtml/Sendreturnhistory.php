<?php

/**
 * Class ITwebexperts_Payperrentals_Block_Adminhtml_Sendreturnhistory
 */
class ITwebexperts_Payperrentals_Block_Adminhtml_Sendreturnhistory extends Mage_Core_Block_Template{

    /**
     *
     */
    public function __construct(){

	}

    /**
     * @var
     */
    protected $_grid;

    /**
     * @return $this
     */
    protected function _prepareLayout()
	{
		parent::_prepareLayout();
		$grid = $this->getLayout()
				->createBlock('payperrentals/adminhtml_html_sendreturngrid', 'my.payperrentals.grid')
				;
		$this->setChild('grid', $grid);
		$this->_grid = $grid;

		return $this;
	}

    /**
     * @return mixed
     */
    public function getGrid(){
		return $this->_grid;
	}

    /**
     * @return string
     */
    public function getGridHtml()
	{
		return $this->getChildHtml('grid');
	}

    public function checkboxCheck($fieldname){
        if($this->getRequest()->getParam($fieldname)){
            return 'checked';
        }
    }
}

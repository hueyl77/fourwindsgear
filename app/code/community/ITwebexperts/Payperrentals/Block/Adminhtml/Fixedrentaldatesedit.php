<?php

/**
 * Class ITwebexperts_Payperrentals_Block_Adminhtml_Fixedrentaldatesedit
 */
class ITwebexperts_Payperrentals_Block_Adminhtml_Fixedrentaldatesedit extends Mage_Core_Block_Template{

    /**
     *
     */
    public function __construct(){

	}

    /**
     * @var
     */
    protected $_form;

    /**
     * @return $this
     */
    protected function _prepareLayout()
	{
		parent::_prepareLayout();
		$form = $this->getLayout()
				->createBlock('payperrentals/adminhtml_fixedrentaldates_edit', 'my.payperrentalsfixedrentaldatesedit.form')
				;
		$this->setChild('form', $form);
		$this->_form = $form;

		return $this;
	}

    /**
     * @return mixed
     */
    public function getForm(){
		return $this->_form;
	}

    /**
     * @return string
     */
    public function getFormHtml()
	{
		return $this->getChildHtml('form');
	}

}

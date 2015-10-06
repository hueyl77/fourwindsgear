<?php

/**
 * Class ITwebexperts_Payperrentals_Block_Adminhtml_Reservationpricesdatesedit
 */
class ITwebexperts_Payperrentals_Block_Adminhtml_Reservationpricesdatesedit extends Mage_Core_Block_Template{

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
				->createBlock('payperrentals/adminhtml_reservationpricesdates_edit', 'my.payperrentalsreservationpricesedit.form')
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

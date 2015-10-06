<?php

/**
 * Class ITwebexperts_Payperrentals_Block_Adminhtml_Customer_Edit_Tab_Membership_Form_Membershipform
 */
class ITwebexperts_Payperrentals_Block_Adminhtml_Customer_Edit_Tab_Membership_Form_Membershipform extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     *
     */
    public function __construct()
	{
		parent::__construct();
		//$this->setTemplate('payperrentals/customer/edit/tab/membership.phtml');
	}

    /**
     * @return $this
     */
    public function initForm()
	{
		$form = new Varien_Data_Form();
		$form->setHtmlIdPrefix('_membership');
		$form->setFieldNameSuffix('membership');

		$customer = Mage::registry('current_customer');

		$customerForm = Mage::getModel('customer/form');
		  $customerForm->setEntity($customer)
		  ->setFormCode('adminhtml_customer_membership')
		  ->initDefaultValues();

		  $fieldset = $form->addFieldset('base_fieldset', array(
		  'legend' => Mage::helper('payperrentals')->__('Membership')
		  ));

        $fieldset->addField('note','note',array(
        'text'  =>  Mage::helper('payperrentals')->__('Only use the following for manually setting rental memberships. This is useful if your payment module does not support recurring billing.')
    ));

		  $attributes = $customerForm->getAttributes();
		  foreach ($attributes as $attribute) {
		  $attribute->setFrontendLabel(Mage::helper('customer')->__($attribute->getFrontend()->getLabel()));
		  $attribute->unsIsVisible();
		  }

		  $this->_setFieldset($attributes, $fieldset, array());
		  if ($customer->isReadonly()) {
			  foreach ($customer->getAttributes() as $attribute) {
			  	$element = $form->getElement($attribute->getAttributeCode());
				if ($element) {
					$element->setReadonly(true, true);
				}
			  }
		  }
        $customer->setData('membershippackage_cc',Mage::getModel('core/encryption')->decrypt($customer->getData('membershippackage_cc')));
		  $form->setValues($customer->getData());
		$this->setForm($form);
		return $this;
	}


}



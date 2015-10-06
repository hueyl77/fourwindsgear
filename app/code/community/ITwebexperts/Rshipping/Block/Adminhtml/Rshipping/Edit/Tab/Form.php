<?php

/**
 * @category   ITwebexperts
 * @package    Itwebexperts_Rshipping
 * @copyright  Copyright (c) 2013
 *
 */
class ITwebexperts_Rshipping_Block_Adminhtml_Rshipping_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('rshipping_form', array('legend' => Mage::helper('rshipping')->__('Item information')));
        $this->_addElementTypes($fieldset);

        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('rshipping')->__('Status'),
            'name' => 'status',
            'values' => array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('rshipping')->__('Enabled'),
                ),
                array(
                    'value' => 2,
                    'label' => Mage::helper('rshipping')->__('Disabled'),
                ),
            ),
        ));

        $fieldset->addField('is_local_pickup', 'select', array(
            'label' => Mage::helper('rshipping')->__('Is Local Pickup'),
            'name' => 'is_local_pickup',
            'values' => array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('rshipping')->__('Yes'),
                ),
                array(
                    'value' => 0,
                    'label' => Mage::helper('rshipping')->__('No'),
                ),
            ),
        ));

        $fieldset->addField('is_default_method', 'select', array(
            'label' => Mage::helper('rshipping')->__('Use as Default'),
            'name' => 'is_default_method',
            'values' => array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('rshipping')->__('Yes'),
                ),
                array(
                    'value' => 0,
                    'label' => Mage::helper('rshipping')->__('No'),
                ),
            ),
        ));

        $fieldset->addField('shipping_title', 'text', array(
            'label' => Mage::helper('rshipping')->__('Shipping Title'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'shipping_title',
        ));

        $fieldset->addField('shipping_method', 'select', array(
            'label' => Mage::helper('rshipping')->__('Shipping Methods'),
            'name' => 'shipping_method',
            'values' => Mage::getModel('rshipping/system_config_source_shipping_allmethods')->toOptionArray(true)
        ));

        if (Mage::helper('rshipping/config')->isCustomShippingPrice()) {
            $fieldset->addField('use_custom_shipping_amount', 'select', array(
                'label' => Mage::helper('rshipping')->__('Use Custom Shipping Price'),
                'name' => 'use_custom_shipping_amount',
                'values' => array(
                    array(
                        'value' => 1,
                        'label' => Mage::helper('rshipping')->__('Yes'),
                    ),
                    array(
                        'value' => 0,
                        'label' => Mage::helper('rshipping')->__('No'),
                    ),
                ),
            ));

            $fieldset->addField('custom_shipping_amount_type', 'select', array(
                'label' => Mage::helper('rshipping')->__('Shipping Amount Type'),
                'name' => 'custom_shipping_amount_type',
                'values' => Mage::getModel('rshipping/source_shippingAmountType')->toOptionArray()
            ));
            $fieldset->addField('shipping_amount', 'price', array(
                'label' => Mage::helper('rshipping')->__('Shipping Price'),
                'name' => 'shipping_amount'
            ));
        }


        $fieldset->addField('turnover_before_period', 'text', array(
            'label' => Mage::helper('rshipping')->__('Turnover Before Period'),
            'name' => 'turnover_before_period'
        ));

        $fieldset->addField('turnover_before_type', 'select', array(
            'label' => Mage::helper('rshipping')->__('Turnover Before Type'),
            'name' => 'turnover_before_type',
            'values' => Mage::getModel('rshipping/system_config_source_shipping_turnaroundtype')->toOptionArray()
        ));

        $fieldset->addField('turnover_after_period', 'text', array(
            'label' => Mage::helper('rshipping')->__('Turnover After Period'),
            'name' => 'turnover_after_period'
        ));

        $fieldset->addField('turnover_after_type', 'select', array(
            'label' => Mage::helper('rshipping')->__('Turnover After Type'),
            'name' => 'turnover_after_type',
            'values' => Mage::getModel('rshipping/system_config_source_shipping_turnaroundtype')->toOptionArray()
        ));

        $fieldset->addField('min_rental_period', 'text', array(
            'label' => Mage::helper('rshipping')->__('Min Rental Period'),
            'name' => 'min_rental_period'
        ));

        $fieldset->addField('min_rental_type', 'select', array(
            'label' => Mage::helper('rshipping')->__('Min Rental Type'),
            'name' => 'min_rental_type',
            'values' => Mage::getModel('rshipping/system_config_source_shipping_turnaroundtype')->toOptionArray()
        ));

        $fieldset->addField('start_disabled_days', 'multiselect', array(
            'name' => 'start_disabled_days',
            'label' => $this->__('Start Range Disabled Days of Week'),
            'title' => $this->__('Start Range Disabled Days of Week'),
            'required' => false,
            'values' => Mage::getModel('rshipping/system_config_source_shipping_turnaroundtype')->getWeekDaysAr()
        ));

        $fieldset->addField('end_disabled_days', 'multiselect', array(
            'name' => 'end_disabled_days',
            'label' => $this->__('End Range Disabled Days of Week'),
            'title' => $this->__('End Range Disabled Days of Week'),
            'required' => false,
            'values' => Mage::getModel('rshipping/system_config_source_shipping_turnaroundtype')->getWeekDaysAr()
        ));

        $fieldset->addField('ignore_turnover_day', 'multiselect', array(
            'name' => 'ignore_turnover_day',
            'label' => $this->__('Ignore Turnover Day'),
            'title' => $this->__('Ignore Turnover Day'),
            'required' => false,
            'values' => Mage::getModel('rshipping/system_config_source_shipping_turnaroundtype')->getWeekDaysAr()
        ));

        $fieldset->addField('shipping_cutoff_time', 'text', array(
            'label' => Mage::helper('rshipping')->__('Day Cutoff Time'),
            'name' => 'shipping_cutoff_time',
            'note' => 'Please insert cutoff time value or stay empty for not using this feature<br/>Example: 00:10:00'
        ));

        $_type = (Mage::getStoreConfig(Itwebexperts_Rshipping_Helper_Data::RSHIPPING_UPS_API_ENABLE)) ? 'select' : 'hidden';
        $fieldset->addField('use_live_ups_api', $_type, array(
            'label' => Mage::helper('rshipping')->__('Use Live API Transit Time'),
            'name' => 'use_live_ups_api',
            'values' => array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('rshipping')->__('Enabled'),
                ),
                array(
                    'value' => 0,
                    'label' => Mage::helper('rshipping')->__('Disabled'),
                ),
            ),
            'required' => false,
        ));


        if (Mage::getSingleton('adminhtml/session')->getRshippingData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getRshippingData());
            Mage::getSingleton('adminhtml/session')->setRshippingData(null);
        } elseif (Mage::registry('rshipping_data')) {
            $form->setValues(Mage::registry('rshipping_data')->getData());
        }
        return parent::_prepareForm();
    }

    /**
     * Retrieve additional element types
     *
     * @return array
     */
    protected function _getAdditionalElementTypes()
    {
        $result = array(
            'price' => Mage::getConfig()->getBlockClassName('rshipping/adminhtml_rshipping_helper_form_price'),
        );

        return $result;
    }
}
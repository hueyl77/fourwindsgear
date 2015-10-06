<?php
class ITwebexperts_Payperrentals_Block_Adminhtml_Reservationpricesdates_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm(){
        $data = null;
        $form = new Varien_Data_Form(array(
            'id'        =>  'edit_form',
            'action'    =>  $this->getUrl('*/*/save', array
                ('id'      =>  $this->getRequest()->getParam('id'))),
            'method'    =>  'post',
            'enctype'   =>  'multipart/form-data',
        ));

        $form->setUseContainer(true);
        $this->setForm($form);

        if (Mage::getSingleton('adminhtml/session')->getFormData())
        {
            $data = Mage::getSingleton('adminhtml/session')->getFormData();
            Mage::getSingleton('adminhtml/session')->setFormData(null);
        }  elseif(Mage::registry('registry_data'))
            $data = Mage::registry('registry_data')->getData();


        $fieldset = $form->addFieldset('registry_form',array('legend'=>Mage::helper('payperrentals')->__('Price By Date & Time')));
        $dateFormatIso = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);

        $fieldset->addField('id', 'hidden', array(
            'label'     =>  Mage::helper('payperrentals')->__('Description'),
            'name'      =>  'id'
        ));


        $fieldset->addField('description', 'text', array(
            'label'     =>  Mage::helper('payperrentals')->__('Description'),
            'required'  =>  false,
            'name'      =>  'description'
        ));

            $fieldset->addField('date_from', 'text', array(
                'label'     =>  Mage::helper('payperrentals')->__('Start Date'),
                'class'     =>  'required-entry startdate',
                'required'  =>  'true',
                'style'  =>  'width:140px',
                'name'      =>  'date_from',
            ));

            $fieldset->addField('date_to', 'text', array(
                'label'     =>  Mage::helper('payperrentals')->__('End Date'),
                'class'     =>  'required-entry enddate',
                'required'  =>  'true',
                'style'  =>  'width:140px',
                'name'      =>  'date_to'
            ));

            $fieldset->addField('disabled_type', 'select', array(
                'label'     =>  Mage::helper('payperrentals')->__('Repeat'),
                'name'      =>  'disabled_type',
                'values'    =>  array(
                    'Daily'     => Mage::helper('payperrentals')->__('Daily'),
                    'Weekly'    => Mage::helper('payperrentals')->__('Weekly'),
                    'Monthly'   => Mage::helper('payperrentals')->__('Monthly'),
                    'Yearly'    => Mage::helper('payperrentals')->__('Yearly'),
                    'Never'     => Mage::helper('payperrentals')->__('Never')),
                'value'     =>  'disabled_type'
            ));

        $fieldset->addField('repeat_days', 'multiselect', array(
            'label'     =>  Mage::helper('payperrentals')->__('Days to Repeat'),
            'name'      =>  'repeat_days[]',
            'values'    =>  array(
                array(
                    'value' => '1',
                    'label' => Mage::helper('payperrentals')->__('Monday')
                ),
                array(
                    'value' => '2',
                    'label' => Mage::helper('payperrentals')->__('Tuesday')
                ),
                array(
                    'value' => '3',
                    'label' => Mage::helper('payperrentals')->__('Wednesday')
                ),
                array(
                    'value' => '4',
                    'label' => Mage::helper('payperrentals')->__('Thursday')
                ),
                array(
                    'value' => '5',
                    'label' => Mage::helper('payperrentals')->__('Friday')
                ),
                array(
                    'value' => '6',
                    'label' => Mage::helper('payperrentals')->__('Saturday')
                ),
                array(
                    'value' => '0',
                    'label' => Mage::helper('payperrentals')->__('Sunday')
                )
            ),
            'value'     =>  'repeat_days'
        ));

        $form->setValues($data);
        return parent::_prepareForm();
    }
}
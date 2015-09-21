<?php
class ITwebexperts_Maintenance_Block_Adminhtml_Periodic_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm(){
        $data = null;
        $form = new Varien_Data_Form(array(
            'id'    =>  'edit_form',
            'action'    =>  $this->getUrl('*/*/save', array
            ('id'      =>  $this->getRequest()->getParam('id'))),
            'method'    =>  'post',
            'enctype'   =>  'multipart/form-data',
        ));
        $form->setUseContainer(true);
        $this->setForm($form);
        $dateFormatIso = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);

        if (Mage::getSingleton('adminhtml/session')->getFormData())
        {
            $data = Mage::getSingleton('adminhtml/session')->getFormData();
            Mage::getSingleton('adminhtml/session')->setFormData(null);
        }  elseif(Mage::registry('form_data'))
            $data = Mage::registry('form_data')->getData();

        $prodCollection = Mage::helper('simaintenance')->loadProductCollection();
        $prodArray = Mage::helper('simaintenance')->getProductNamesArray($prodCollection);
        $maintenanceTemplates = Mage::getModel('simaintenance/snippets')->getTemplates();
        $maintenanceArray = Mage::helper('simaintenance')->getMaintainers();
        $frequencyArray = Mage::getModel('simaintenance/periodic')->getFrequencyArray();

        $fieldset = $form->addFieldset('registry_form',array('legend'=>Mage::helper('simaintenance')->__('Automated Maintenance')));

        $fieldset->addField('product_id', 'select', array(
            'label'     =>  Mage::helper('simaintenance')->__('Product'),
            'class'     =>  'required-entry productdrop',
            'required'  =>  'true',
            'values'    =>  $prodArray,
            'name'      =>  'product_id',
            'value'     =>  'product_id',
        ));

        $fieldset->addField('frequency_quantity', 'text', array(
            'label'     =>  Mage::helper('simaintenance')->__('Frequency Quantity'),
            'class'     =>  'required-entry',
            'required'  =>  'true',
            'name'      =>  'frequency_quantity'
        ));



        $fieldset->addField('frequency_type', 'select', array(
            'label'     =>  Mage::helper('simaintenance')->__('Frequency Time Period'),
            'class'     =>  'required-entry',
            'required'  =>  'true',
            'values'    =>  $frequencyArray,
            'name'      =>  'frequency_type'
        ));

        $fieldset->addField('snippet_id', 'select', array(
            'label'     =>  Mage::helper('simaintenance')->__('Maintenance Template'),
            'class'     =>  'required-entry',
            'required'  =>  'true',
            'name'      =>  'snippet_id',
            'values'    =>  $maintenanceTemplates
        ));

        $fieldset->addField('note', 'note', array(
            'text'  =>  Mage::helper('simaintenance')->__('The start date for maintenance is counted from the date the inventory was acquired (for serial number based) or the date the product was entered (for quantity based). So if you put quantity: 1 and type: Year it will auto-add a maintenance request a year from the start date (when inventory was acquired)')
        ));

        $fieldset->addField('start_date', 'date', array(
            'label'     =>  Mage::helper('simaintenance')->__('Override Start Date Of Maintenance'),
            'name'      =>  'start_date',
            'image'  => $this->getSkinUrl('images/grid-cal.gif'),
            'input_format' => 'yyyy-MM-dd H:mm:ss',
            'format'       => $dateFormatIso,
            'time' => true
        ));

        $fieldset->addField('maintainer_id', 'select', array(
            'label'     =>  Mage::helper('simaintenance')->__('Maintainer'),
            'name'      =>  'maintainer_id',
            'value'     =>  '-1',
            'values'    =>  $maintenanceArray
        ));

        $form->setValues($data);
        return parent::_prepareForm();
    }

}
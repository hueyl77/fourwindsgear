<?php
class ITwebexperts_Maintenance_Block_Adminhtml_Snippets_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
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

        if (Mage::getSingleton('adminhtml/session')->getFormData())
        {
            $data = Mage::getSingleton('adminhtml/session')->getFormData();
            Mage::getSingleton('adminhtml/session')->setFormData(null);
        }  elseif(Mage::registry('form_data'))
            $data = Mage::registry('form_data')->getData();

        $fieldset = $form->addFieldset('registry_form',array('legend'=>Mage::helper('simaintenance')->__('Template')));

        $fieldset->addField('title', 'text', array(
            'label'     =>  Mage::helper('simaintenance')->__('Title'),
            'class'     =>  'required-entry',
            'required'  =>  'true',
            'name'      =>  'title'
        ));

        $fieldset->addField('snippet', 'textarea', array(
            'label'     =>  Mage::helper('simaintenance')->__('Description'),
            'class'     =>  'required-entry',
            'required'  =>  'true',
            'name'      =>  'snippet'
        ));

        $form->setValues($data);
        return parent::_prepareForm();
    }

}
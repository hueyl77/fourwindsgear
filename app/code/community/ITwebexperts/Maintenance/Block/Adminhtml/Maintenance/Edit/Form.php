<?php
class ITwebexperts_Maintenance_Block_Adminhtml_Maintenance_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
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
        }  elseif(Mage::registry('maintenance_data'))
            $data = Mage::registry('maintenance_data')->getData();

        $fieldset = $form->addFieldset('registry_form',array('legend'=>Mage::helper('simaintenance')->__('Maintenance Record')));
        $dateFormatIso = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
        $prodCollection = Mage::helper('simaintenance')->loadProductCollection();
        $prodArray = Mage::helper('simaintenance')->getProductNamesArray($prodCollection);
        $statusArray = Mage::getModel('simaintenance/status')->getStatusArray();
        $maintenanceTemplates = Mage::getModel('simaintenance/snippets')->getTemplates();
        if(array_key_exists('product_id',$data)) {
            $serialValues = Mage::helper('simaintenance')->getSerialValues($data['product_id']);
            $serialValue = Mage::helper('simaintenance')->getSerialValue($this->getRequest()->getParam('id'));
        } else {
            $serialValues = array();
            $serialValue = "";
        }
        $maintenanceArray = Mage::helper('simaintenance')->getMaintainers();

        $fieldset->addField('status', 'select', array(
            'label'     =>  Mage::helper('simaintenance')->__('Status'),
            'class'     =>  'required-entry',
            'required'  =>  true,
            'name'      =>  'status',
            'values'    =>  $statusArray,
            //'value'     =>  '1',
        ));

        $fieldset->addField('product_id', 'select', array(
            'label'     =>  Mage::helper('simaintenance')->__('Product'),
            'class'     =>  'required-entry productdrop',
            'required'  =>  'true',
            'name'      =>  'product_id',
            'values'    =>  $prodArray,
            'value'     =>  'product_id',
        ));

            $fieldset->addField('quantity', 'text', array(
                'label'     =>  Mage::helper('simaintenance')->__('Quantity'),
                'name'      =>  'quantity',
            ));

            $fieldset->addField('serials', 'multiselect', array(
                'label'     =>  Mage::helper('simaintenance')->__('Serials'),
                'name'      =>  'serials[]',
                'value'     =>  $serialValue,
                'values'    =>  $serialValues,
            ));

            $fieldset->addField('cost', 'text', array(
                'label'     =>  Mage::helper('simaintenance')->__('Cost'),
                'required'  =>  false,
                'name'      =>  'cost'
            ));


        $fieldset->addField('snippet','select',array(
            'label'     =>  Mage::helper('simaintenance')->__('Maintenance Template'),
            'name'      =>  'snippet',
            'after_element_html'    =>  ' &nbsp;<button id="apply">Apply Template</button>',
            'values'    =>  $maintenanceTemplates,
            'value'     =>  '-1'
        ));


            $fieldset->addField('summary', 'text', array(
                'label'     =>  Mage::helper('simaintenance')->__('Summary'),
                'required'  =>  false,
                'name'      =>  'summary'
            ));

            $fieldset->addField('description', 'textarea', array(
                'label'     =>  Mage::helper('simaintenance')->__('Description'),
                'name'      =>  'description'
            ));

        $fieldset->addField('comments', 'textarea', array(
            'label'     =>  Mage::helper('simaintenance')->__('Comments'),
            'name'      =>  'comments',
        ));



        $fieldset->addField('maintainer_id', 'select', array(
            'label'     =>  Mage::helper('simaintenance')->__('Maintainer'),
            'name'      =>  'maintainer_id',
            'value'     =>  '-1',
            'values'    =>  $maintenanceArray
        ));

        $fieldset->addField('specific_dates', 'select', array(
            'label'     =>  Mage::helper('simaintenance')->__('Specific Start & End Date?'),
            'name'      =>  'specific_dates',
            'values'    =>  array('1'=>Mage::helper('simaintenance')->__('Yes'),'0'=>Mage::helper('simaintenance')->__('No')),
            'value'     =>  1
        ));

        $fieldset->addField('start_date', 'date', array(
            'label'     =>  Mage::helper('simaintenance')->__('Start Date'),
            'name'      =>  'start_date',
            'image'  => $this->getSkinUrl('images/grid-cal.gif'),
            'input_format' => 'yyyy-MM-dd H:mm:ss',
            'format'       => $dateFormatIso,
            'time' => true
        ));

        $fieldset->addField('end_date', 'date', array(
            'label'     =>  Mage::helper('simaintenance')->__('End Date'),
            'name'      =>  'end_date',
            'image'  => $this->getSkinUrl('images/grid-cal.gif'),
            'input_format' => 'yyyy-MM-dd H:mm:ss',
            'format'       => $dateFormatIso,
            'time' => true
        ));

        $fieldset->addField('email_maintainer','checkbox',array(
            'label' =>  Mage::helper('simaintenance')->__('Email assigned maintenance technician'),
            'checked'   =>  true,
            'name'  =>  'email_maintainer',
        ));

        $form->setValues($data);
        return parent::_prepareForm();


    }

}
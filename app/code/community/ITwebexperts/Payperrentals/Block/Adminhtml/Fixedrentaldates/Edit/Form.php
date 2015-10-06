<?php
class ITwebexperts_Payperrentals_Block_Adminhtml_Fixedrentaldates_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    private function _addFields($fieldset, $countFixed){

        $fieldset->addField('start_date'.$countFixed, 'text', array(
            'label' => Mage::helper('payperrentals')->__('Start Date'),
            'class' => 'required-entry startdate',
            'required' => 'true',
            'style' => 'width:140px',
            'name' => 'start_date'.$countFixed,
        ));

        $fieldset->addField('end_date'.$countFixed, 'text', array(
            'label' => Mage::helper('payperrentals')->__('End Date'),
            'class' => 'required-entry enddate',
            'required' => 'true',
            'style' => 'width:140px',
            'name' => 'end_date'.$countFixed
        ));

        $fieldset->addField('repeat_type'.$countFixed, 'select', array(
            'label' => Mage::helper('payperrentals')->__('Repeat'),
            'name' => 'repeat_type'.$countFixed,
            'class' => 'repeattype',
            'values' => array(
                'none' => Mage::helper('payperrentals')->__('None'),
                'dayweek' => Mage::helper('payperrentals')->__('Days of week'),
                'monthly' => Mage::helper('payperrentals')->__('Monthly'),
                'yearly' => Mage::helper('payperrentals')->__('Yearly')
            ),
            'value' => 'repeat_type'
        ));
        $fieldset->addField('repeat_days'.$countFixed, 'multiselect', array(
            'label'     =>  Mage::helper('payperrentals')->__('Days to Repeat'),
            'name'      =>  'repeat_days'.$countFixed.'[]',
            'class' => 'repeatdays',
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
        $fieldset->addField('button_delete'.$countFixed, 'hidden', array(
            'class' => 'buttondelete',
            'name' => 'button_delete'.$countFixed
        ));
        $fieldset->addField(
            'notesdates'.$countFixed, 'note', array(
                'text'  => Mage::helper('payperrentals')->__(
                    ''
                ),
                'class' => 'notedates'
            )
        );
    }
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


        $fieldset = $form->addFieldset('registry_form',array('legend' => Mage::helper('payperrentals')->__('Fixed Rental Dates')));

        if(isset($data['id'])){
            $fieldset->addField('id', 'hidden', array(
                'class' => 'id',
                'name' => 'id'
            ));
        }
        $fieldset->addField('name', 'text', array(
            'label' => Mage::helper('payperrentals')->__('Name'),
            'class' => 'required-entry name',
            'required' => 'true',
            'style' => 'width:140px',
            'name' => 'name'
        ));

        $fieldset->addField('nrdates', 'hidden', array(
            'class' => 'nrdates',
            'name' => 'nrdates'
        ));

        $collectionFixed = Mage::getModel('payperrentals/fixedrentaldates')->getCollection()
        ->addFieldToFilter('nameid',  $this->getRequest()->getParam('id'));
        $valueData['name'] = $data['name'];
        $valueData['id'] = $data['id'];
        if($collectionFixed->getSize() > 0) {
            $countFixed = 1;

            foreach ($collectionFixed as $fixedRental) {
                $fieldset2 = $form->addFieldset('registry_form'.$countFixed,array('legend' => Mage::helper('payperrentals')->__('')));
                $valueData['start_date' . $countFixed] = $fixedRental->getStartDate();
                $valueData['end_date' . $countFixed] = $fixedRental->getEndDate();
                $valueData['repeat_type' . $countFixed] = $fixedRental->getRepeatType();
                $valueData['repeat_days' . $countFixed] = $fixedRental->getRepeatDays();
                $valueData['button_delete'.$countFixed] = $countFixed;
                $this->_addFields($fieldset2, $countFixed);
                $countFixed++;
            }
        }else{
            $valueData = $data;
            $this->_addFields($fieldset, '1');
        }

        $form->setValues($valueData);
        return parent::_prepareForm();
    }
}
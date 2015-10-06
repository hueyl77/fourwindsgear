<?php

class ITwebexperts_Payperrentals_Block_Adminhtml_Report_Filter_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected $_filters = array();

    /**
     * Add fieldset with general report fields
     *
     * @return ITwebexperts_Payperrentals_Block_Adminhtml_Report_Filter_Form
     */
    protected function _prepareForm()
    {
        $_actionUrl = $this->getUrl('*/*/index');
        $_form = new Varien_Data_Form(
            array('id' => 'filter_form', 'action' => $_actionUrl, 'method' => 'get')
        );
        $_htmlIdPrefix = 'report_rented_';
        $_form->setHtmlIdPrefix($_htmlIdPrefix);
        $_fieldset = $_form->addFieldset('report_filter', array('legend' => Mage::helper('payperrentals')->__('Filter')));

        $_fieldset->addField('store_ids', 'hidden', array(
            'name' => 'store_ids'
        ));

        foreach ($this->_filters as $_filter) {
            $_fieldset->addField($_filter['id'], $_filter['type'], $_filter['params']);
        }

        $_form->setUseContainer(true);
        $this->setForm($_form);

        return parent::_prepareForm();
    }

    /**
     * Set field option(s)
     *
     * @param string $fieldId Field id
     * @param mixed $option Field option name
     * @param mixed $value Field option value
     */
    public function setFieldOption($fieldId, $option, $value = null)
    {
        if (is_array($option)) {
            $options = $option;
        } else {
            $options = array($option => $value);
        }
        if (!array_key_exists($fieldId, $this->_fieldOptions)) {
            $this->_fieldOptions[$fieldId] = array();
        }
        foreach ($options as $k => $v) {
            $this->_fieldOptions[$fieldId][$k] = $v;
        }
    }

    /**
     * Add report type option
     *
     * @param string $key
     * @param string $value
     * @return Mage_Adminhtml_Block_Report_Filter_Form
     */
    public function addReportTypeOption($key, $value)
    {
        $this->_reportTypeOptions[$key] = $this->__($value);
        return $this;
    }

    /**
     * Initialize form fileds values
     * Method will be called after prepareForm and can be used for field values initialization
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _initFormValues()
    {
        $_data = $this->getFilterData()->getData();
        foreach ($_data as $_key => $_value) {
            if (is_array($_value) && isset($_value[0])) {
                $_data[$_key] = explode(',', $_value[0]);
            }
        }
        $this->getForm()->addValues($_data);
        return parent::_initFormValues();
    }

    public function addRentedFilters($_isHidden = false)
    {
        $this->addPeriod($_isHidden);
        $_dateFormat = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
        $this->_filters[] = array(
            'id' => 'report_from',
            'type' => 'date',
            'params' => array(
                'name' => 'report_from',
                'format' => $_dateFormat,
                'image' => $this->getSkinUrl('images/grid-cal.gif'),
                'label' => Mage::helper('payperrentals')->__('From'),
                'title' => Mage::helper('payperrentals')->__('From'),
                'required' => true
            )
        );
        $this->_filters[] = array(
            'id' => 'report_to',
            'type' => 'date',
            'params' => array(
                'name' => 'report_to',
                'format' => $_dateFormat,
                'image' => $this->getSkinUrl('images/grid-cal.gif'),
                'label' => Mage::helper('payperrentals')->__('To'),
                'title' => Mage::helper('payperrentals')->__('To'),
                'required' => true
            )
        );
        $this->addCategoryFilterField();
    }

    public function addPeriod($_isHidden)
    {
        if (!$_isHidden) {
            $this->_filters[] = array(
                'id' => 'report_period',
                'type' => 'select',
                'params' => array(
                    'name' => 'report_period',
                    'options' => array(
                        'day' => Mage::helper('payperrentals')->__('Day'),
                        'month' => Mage::helper('payperrentals')->__('Month'),
                        'year' => Mage::helper('payperrentals')->__('Year')
                    ),
                    'label' => Mage::helper('payperrentals')->__('Period'),
                    'title' => Mage::helper('payperrentals')->__('Period')
                )
            );
        } else {
            $this->_filters[] = array(
                'id' => 'report_period',
                'type' => 'hidden',
                'params' => array(
                    'name' => 'report_period',
                    'value' => 'month'
                )
            );
        }
    }

    public function addCategoryFilterField()
    {
        $_categoryAr = array('none' => '');
        $_categoryCollection = Mage::getResourceSingleton('catalog/category_collection')
            ->addAttributeToSelect('name')
            ->addAttributeToFilter('level', array('gteq' => 1));
        foreach ($_categoryCollection as $_category) {
            $_categoryAr[$_category->getId()] = $_category->getName();
        }
        $this->_filters[] = array(
            'id' => 'report_category',
            'type' => 'select',
            'params' => array(
                'name' => 'report_category',
                'options' => $_categoryAr,
                'label' => Mage::helper('payperrentals')->__('Product Category'),
                'title' => Mage::helper('payperrentals')->__('Product Category')
            ));
    }
}
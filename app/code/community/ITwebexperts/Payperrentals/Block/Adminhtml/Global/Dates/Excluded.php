<?php

/**
 * Class ITwebexperts_Payperrentals_Block_Adminhtml_Global_Dates_Excluded
 */
class ITwebexperts_Payperrentals_Block_Adminhtml_Global_Dates_Excluded extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    /**
     * @var
     */
    protected $_selectFieldTypeRenderer;

    /**
     * @var
     */
    protected $_selectFieldExcludeRenderer;

    /**
     * @var
     */
    protected $_selectFieldExcludeTurnoverRenderer;
    /**
     * @var
     */
    protected $_dateFieldRenderer;


    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get select block
     *
     * @return Mage_Core_Block_Html_Select
     */
    protected function _getSelectFieldTypeRenderer()
    {
        if (!$this->_selectFieldTypeRenderer) {
            $this->_selectFieldTypeRenderer = $this->getLayout()
                ->createBlock('core/html_select')
                ->setIsRenderToJsTemplate(true)
                ->setClass('input-select require')
                ->setExtraParams('style="width:100px"')
                ->setOptions($this->getElement()->getValues());
        }
        return $this->_selectFieldTypeRenderer;
    }

    /**
     * Get select block
     *
     * @return Mage_Core_Block_Html_Select
     */
    protected function _getSelectFieldExcludeRenderer()
    {
        if (!$this->_selectFieldExcludeRenderer) {
            $this->_selectFieldExcludeRenderer = $this->getLayout()
                ->createBlock('core/html_select')
                ->setIsRenderToJsTemplate(true)
                ->setClass('input-select require')
                ->setExtraParams('style="width:100px"')
                ->setOptions(array(ITwebexperts_Payperrentals_Model_Source_Excludedaysfrom::CALENDAR => 'Calendar', ITwebexperts_Payperrentals_Model_Source_Excludedaysfrom::PRICE => 'Price' , ITwebexperts_Payperrentals_Model_Source_Excludedaysfrom::BOTH => 'Both'));
        }
        return $this->_selectFieldExcludeRenderer;
    }

    /**
     * Get select block
     *
     * @return Mage_Core_Block_Html_Select
     */
    protected function _getSelectFieldExcludeTurnoverRenderer()
    {
        if (!$this->_selectFieldExcludeTurnoverRenderer) {
            $this->_selectFieldExcludeTurnoverRenderer = $this->getLayout()
                ->createBlock('core/html_select')
                ->setIsRenderToJsTemplate(true)
                ->setClass('input-select require')
                ->setExtraParams('style="width:100px"')
                ->setOptions(array(1 => 'Yes', 0 => 'No'));
        }
        return $this->_selectFieldExcludeTurnoverRenderer;
    }
    /**
     * Get date block
     *
     * @return Mage_Core_Block_Html_Date
     */
    protected function _getDateFieldRenderer()
    {
        if (!$this->_dateFieldRenderer) {
            $this->_dateFieldRenderer = $this->getLayout()
                ->createBlock('core/html_date')
                ->setIsRenderToJsTemplate(true)
                ->setClass('datetime-picker input-text require')
                ->setTime(true)
                ->setExtraParams('style="width:150px"')
                ->setImage(Mage::getDesign()->getSkinUrl('images/grid-cal.gif'))
                ->setFormat(Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT));
        }
        return $this->_dateFieldRenderer;
    }

    /**
     * @param $_blockName
     * @return bool|Mage_Core_Block_Html_Date|Mage_Core_Block_Html_Select
     */
    private function _getFieldBlock($_blockName)
    {
        switch ($_blockName) {
            case 'disabled_from':
            case 'disabled_to':
                return $this->_getDateFieldRenderer();
            case 'disabled_type':
                return $this->_getSelectFieldTypeRenderer();
            case 'exclude_dates_from':
                return $this->_getSelectFieldExcludeRenderer();
            case 'exclude_from_turnover':
                return $this->_getSelectFieldExcludeTurnoverRenderer();
            default:
                return false;
        }
    }

    /**
     *
     */
    protected function _prepareToRender()
    {

        $this->_typeRenderer = null;
        $this->_searchFieldRenderer = null;
        $this->addColumn('disabled_from', array(
            'label' => Mage::helper('payperrentals')->__('Disabled From')
        ));
        $this->addColumn('disabled_to', array(
            'label' => Mage::helper('payperrentals')->__('Disabled To')
        ));
        $this->addColumn('disabled_type', array(
            'label' => Mage::helper('payperrentals')->__('Repeat')
        ));
        $this->addColumn('exclude_dates_from', array(
            'label' => Mage::helper('payperrentals')->__('Exclude From')
        ));
        $this->addColumn('exclude_from_turnover', array(
            'label' => Mage::helper('payperrentals')->__('Exclude From Turnover')
        ));
        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('payperrentals')->__('Add Rule');
    }

    /**
     * @param $_data
     * @return string
     */
    private function _jsStringEscapeForHtml($_data)
    {
        $_safe = "";
        $nr = strlen($_data);
        for ($_i = 0; $_i < $nr; $_i++) {
            if (ctype_alnum($_data[$_i]))
                $_safe .= $_data[$_i];
            else
                $_safe .= sprintf("\\x%02X", ord($_data[$_i]));
        }
        return $_safe;
    }

    /**
     *
     * My renderer does need parameters that are not supported by original implementation
     *
     * @param string $columnName
     * @return ITwebexperts_Payperrentals_Block_Adminhtml_Global_Dates_Excluded
     * @throws Exception
     */
    protected function _renderCellTemplate($_columnName)
    {
        $_inputName = $this->getElement()->getName() . '[#{_id}][' . $_columnName . ']';

        $_block = $this->_getFieldBlock($_columnName);
        if ($_block !== false) {
            $_block->setName($_inputName)
                ->setTitle($_columnName)
                ->setId('exclude_dates_row_#{_id}_' . $_columnName);
            if ($_columnName == 'disabled_from' || $_columnName == 'disabled_to') {
                $_block->setValue('#{' . $_columnName . '}');
            }
            return $this->_jsStringEscapeForHtml($_block->toHtml());
        } else {
            return parent::_renderCellTemplate($_columnName);
        }
    }

    /**
     * Assign extra parameters to row
     *
     * @param Varien_Object $row
     */
    protected function _prepareArrayRow(Varien_Object $_row)
    {
        $_row->setData('option_extra_attr_' . $this->_getSelectFieldTypeRenderer()->calcOptionHash($_row->getData('disabled_type')), 'selected="selected"');
        $_row->setData('option_extra_attr_' . $this->_getSelectFieldExcludeRenderer()->calcOptionHash($_row->getData('exclude_dates_from')), 'selected="selected"');
        $_row->setData('option_extra_attr_' . $this->_getSelectFieldExcludeTurnoverRenderer()->calcOptionHash($_row->getData('exclude_from_turnover')), 'selected="selected"');
    }

}
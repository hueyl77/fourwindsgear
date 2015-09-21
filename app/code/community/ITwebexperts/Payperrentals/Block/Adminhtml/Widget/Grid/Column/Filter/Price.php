<?php

class ITwebexperts_Payperrentals_Block_Adminhtml_Widget_Grid_Column_Filter_Price extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Price
{
    public function getHtml()
    {
        $html = '<div class="range">';
        $html .= '<div class="range-line"><span class="label">' . Mage::helper('adminhtml')->__('From') . ':</span> <input type="text" name="' . $this->_getHtmlName() . '[from]" id="' . $this->_getHtmlId() . '_from" value="' . $this->getEscapedValue('from') . '" class="input-text no-changes"/></div>';
        $html .= '<div class="range-line"><span class="label">' . Mage::helper('adminhtml')->__('To') . ' : </span><input type="text" name="' . $this->_getHtmlName() . '[to]" id="' . $this->_getHtmlId() . '_to" value="' . $this->getEscapedValue('to') . '" class="input-text no-changes"/></div>';
        if ($this->_isProductGrid()) {
            $html .= '<div class="range-line"><span class="label">' . Mage::helper('adminhtml')->__('Type') . ' : </span>' . $this->_getTypeSelectHtml() . '</div>';
        }
        if ($this->getDisplayCurrencySelect())
            $html .= '<div class="range-line"><span class="label">' . Mage::helper('adminhtml')->__('In') . ' : </span>' . $this->_getCurrencySelectHtml() . '</div>';
        $html .= '</div>';

        return $html;
    }

    protected function _getTypeSelectHtml()
    {
        $_periodTypes = Mage::getModel('payperrentals/product_periodtype')->getOptionArray();

        $_value = $this->getEscapedValue('type');
        if (!$_value)
            $_value = $this->getColumn()->getType();

        $html = '';
        $html .= '<select name="' . $this->_getHtmlName() . '[type]" id="' . $this->_getHtmlId() . '_type">';
        $html .= '<option value="" ' . ('' == $_value ? 'selected="selected"' : '') . '></option>';
        foreach ($_periodTypes as $_typeValue => $_label) {
            $html .= '<option value="' . $_typeValue . '" ' . ($_typeValue == $_value ? 'selected="selected"' : '') . '>' . $_label . '</option>';
        }
        $html .= '</select>';
        return $html;
    }

    protected function _isProductGrid()
    {
        return ($this->getLayout()->getBlock('product.grid') || $this->getLayout()->getBlock('admin.product.grid')) ? true : false;
    }

    /**
     * Callback method for applying price filter.
     * @param Mage_Catalog_Model_Resource_Product_Collection $_collection
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $_column
     */
    public function _filterPrice($_collection, $_column)
    {
        $_field = ($_column->getFilterIndex()) ? $_column->getFilterIndex() : $_column->getIndex();

        $_condition = $_column->getFilter()->getCondition();
        if (!$_field || !is_array($_condition)) return;
        if (!array_key_exists('type', $_condition) || !is_numeric($_condition['type'])) {
            $_collection->addFieldToFilter($_field, $_condition);
        } else {
            $_storeId = (int)$this->getRequest()->getParam('store', 0);
            $_store = Mage::app()->getStore($_storeId);

            $_joinCondition = array(
                '`e`.`entity_id` = `at_reservation_price`.`entity_id`',
                '`at_reservation_price`.`store_id` = ' . $_store->getId(),
                '`at_reservation_price`.`ptype` = ' . $_condition['type']
            );
            if (array_key_exists('from', $_condition)) {
                $_joinCondition[] = '`at_reservation_price`.`price` >= ' . $_condition['from'];
            }
            if (array_key_exists('to', $_condition)) {
                $_joinCondition[] = '`at_reservation_price`.`price` <= ' . $_condition['to'];
            }

            $_joinCondition[] = '`at_reservation_price`.`date_from` = \'0000-00-00 00:00:00\' OR DATE(`at_reservation_price`.`date_from`) <= DATE(\'' . date('Y-m-d H:i:s', Mage::getModel('core/date')->gmtTimestamp(time())) . '\')';
            $_joinCondition[] = '`at_reservation_price`.`date_to` = \'0000-00-00 00:00:00\' OR DATE(`at_reservation_price`.`date_to`) >= DATE(\'' . date('Y-m-d H:i:s', Mage::getModel('core/date')->gmtTimestamp(time())) . '\')';

            $_collection->getSelect()->joinInner(
                array('at_reservation_price' => $_collection->getTable('payperrentals/reservationprices')),
                '(' . implode(') AND (', $_joinCondition) . ')',
                array(
                    /*'reservation_entity_id' => new Zend_Db_Expr('DISTINCT at_reservation_price.entity_id'),*/
                    'price_type' => 'at_reservation_price.ptype',
                    'reservation_price' => 'at_reservation_price.price',
                    'reservation_number' => 'at_reservation_price.numberof',
                )
            );
            /** TODO Check collection count calculation with group. I think need change join for use distinct */
            $_collection->getSelect()->group('e.entity_id');
        }
    }
}
<?php

class ITwebexperts_Payperrentals_Model_Mysql4_Reports_Product_Roi_Collection extends Mage_Reports_Model_Mysql4_Product_Sold_Collection
{
    /**
     * Add rented qty's
     *
     * @param string $_from
     * @param string $_to
     * @return ITwebexperts_Payperrentals_Model_Mysql4_Reports_Product_Rent_Collection
     */
    public function addRentedQty($_from = '', $_to = '')
    {
        $_adapter = $this->getConnection();
        $_compositeTypeIds = Mage::getSingleton('catalog/product_type')->getCompositeTypes();
        $_orderTableAliasName = $_adapter->quoteIdentifier('order');

        $_sendReturnJoinCondition = array(
            'ppro.product_id = ppsr.product_id',
            'ppro.order_id = ppsr.order_id',
            'ppsr.return_date <> \'0000-00-00 00:00:00\'',
            'ppsr.return_date <> \'1970-01-01 00:00:00\'',
        );

        $_orderJoinCondition = array(
            $_orderTableAliasName . '.entity_id = order_items.order_id',
            $_adapter->quoteInto("{$_orderTableAliasName}.state <> ?", Mage_Sales_Model_Order::STATE_CANCELED),

        );

        $_productJoinCondition = array(
            $_adapter->quoteInto('(e.type_id NOT IN (?))', $_compositeTypeIds),
            'e.entity_id = ppro.product_id',
            $_adapter->quoteInto('e.entity_type_id = ?', $this->getProductEntityTypeId())
        );

        if ($_from != '' && $_to != '') {
            $_fieldName = $_orderTableAliasName . '.created_at';
            $_orderJoinCondition[] = $this->_prepareBetweenSql($_fieldName, $_from, $_to);
        }

        $_reservationCostAttribute = Mage::getSingleton('eav/entity_attribute')->loadByCode(Mage_Catalog_Model_Product::ENTITY, 'reservation_cost');
        $_reservationCostAttributeJoin = array(
            'ppro.product_id = cped.entity_id',
            'cped.attribute_id = ' . $_reservationCostAttribute->getId()
        );
        $attributeCode = 'payperrentals_quantity';
        $alias     = $attributeCode . '_table';
        $attribute = Mage::getSingleton('eav/config')
            ->getAttribute(Mage_Catalog_Model_Product::ENTITY, $attributeCode);
        $this->getSelect()->reset()
            ->from(
                array('ppro' => $this->getTable('payperrentals/reservationorders')),
                array('time_diff' => 'SUM(TIMESTAMPDIFF(MINUTE, ppro.start_date,ppro.end_date))'))
            ->joinLeft(
                array('order_items' => $this->getTable('sales/order_item')),
                'ppro.order_item_id = order_items.item_id',
                array(
                    'ordered_qty' => 'SUM(order_items.qty_ordered - order_items.qty_canceled - order_items.qty_refunded)',
                    'order_items_name' => 'order_items.name',
                    'entity_id' => 'order_items.product_id',
                    'monthly_revenue_1' => 'SUM(order_items.row_total)'
                ))
            ->joinLeft(
                array('order_items_parent' => $this->getTable('sales/order_item')),
                'order_items.parent_item_id = order_items_parent.item_id',
                array(

                    'monthly_revenue_2' => 'SUM(order_items_parent.row_total)'
                ))
            ->joinLeft(
                array('e' => $this->getProductEntityTableName()),
                implode(' AND ', $_productJoinCondition),
                array(
                    'entity_id' => 'ppro.product_id',
                    'entity_type_id' => 'e.entity_type_id',
                    'attribute_set_id' => 'e.attribute_set_id',
                    'type_id' => 'e.type_id',
                    'sku' => 'e.sku',
                    'has_options' => 'e.has_options',
                    'required_options' => 'e.required_options',
                    'created_at' => 'e.created_at',
                    'updated_at' => 'e.updated_at',
                ))
            /*->joinLeft(
                array('cis' => $this->getTable('cataloginventory/stock_item')),
                'ppro.product_id = cis.item_id',
                array('stock_inventory' => 'cis.qty'))*/
            ->joinLeft(
                array($alias => $attribute->getBackendTable()),
                "ppro.product_id = $alias.entity_id AND $alias.attribute_id={$attribute->getId()}",
                array('stock_inventory' => $alias.'.value')
            )//todo add another join with serialnumbers table, when items are broken or maintenance
            ->joinLeft(
                array('ppsr' => $this->getTable('payperrentals/sendreturn')),
                implode(' AND ', $_sendReturnJoinCondition),
                array(
                    'total_returned_qty' => 'SUM(ppsr.qty)'
                ))
            ->joinInner(
                array('order' => $this->getTable('sales/order')),
                implode(' AND ', $_orderJoinCondition),
                array('base_grand_total' => 'SUM(order.base_grand_total)'))
            ->joinLeft(array('cped' => $_reservationCostAttribute->getBackendTable()),
                implode(' AND ', $_reservationCostAttributeJoin),
                array('total_inventory_cost' => 'IFNULL((cped.value * '.$alias.'.value), 0)'))
            ->columns(new Zend_Db_Expr('('.$alias.'.value - SUM(order_items.qty_ordered - order_items.qty_canceled - order_items.qty_refunded) + IFNULL(SUM(ppsr.qty), 0)) AS current_stock'))
            ->columns(new Zend_Db_Expr('(SUM(order_items.qty_ordered - order_items.qty_canceled - order_items.qty_refunded)) AS booked_inventory'))
            ->columns(new Zend_Db_Expr('IFNULL((SUM(order_items.row_total)+SUM(order_items_parent.row_total) - (cped.value * '.$alias.'.value)), 0) AS `gross_profit`'))
            ->columns(new Zend_Db_Expr('IFNULL((SUM(order_items.row_total)+SUM(order_items_parent.row_total)), 0) AS `monthly_revenue`'))
            //->where('parent_item_id IS NULL')
            ->group('ppro.product_id')
            ->having('SUM(order_items.qty_ordered) > ?', 0);

        if (Mage::registry('category_filter')) {
            $_categoryJoinCondition = array(
                'ccp.product_id = ppro.product_id',
                'ccp.category_id = ' . Mage::registry('category_filter'),
            );
            $this->getSelect()
                ->joinInner(
                    array('ccp' => $this->getTable('catalog/category_product')),
                    implode(' AND ', $_categoryJoinCondition),
                    array());
        }

        if(Mage::registry('filter_data') && Mage::registry('grid_columns')){
            $_filterData = Mage::registry('filter_data');
            $_gridColumns = Mage::registry('grid_columns');
            foreach ($_gridColumns as $_columnId => $_column) {
                if (isset($_filterData[$_columnId]) && (!empty($_filterData[$_columnId]) || strlen($_filterData[$_columnId]) > 0) && $_column->getFilter()) {
                    ITwebexperts_Payperrentals_Model_Mysql4_Reports_Product_Report_Abstract::addReportFilter($this, $_columnId, $_filterData[$_columnId]);
                }
            }
        }

        return $this;
    }

    /**
     * Set Date range to collection
     *
     * @param string $_from
     * @param string $_to
     * @return ITwebexperts_Payperrentals_Model_Mysql4_Reports_Product_Rent_Collection
     */
    public function setDateRange($_from, $_to)
    {
        $this->_reset()
            ->addAttributeToSelect('*')
            ->addRentedQty($_from, $_to)
            ->setOrder('ordered_qty', self::SORT_ORDER_DESC);
        return $this;
    }
}
<?php

/**
 * @var $installer Mage_Core_Model_Resource_Setup
 */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->addColumn($installer->getTable('payperrentals/reservationorders'),'qty_shipped',
    array(
        'nullable' => false,
        'primary' => false,
        'default'   =>  0,
        'type'     =>   Varien_Db_Ddl_Table::TYPE_INTEGER,
        'comment'  =>  'Total Quantity Shipped'
    ));

$installer->getConnection()->addColumn($installer->getTable('payperrentals/reservationorders'),'qty_returned',
    array(
        'nullable' => false,
        'primary' => false,
        'default'   =>  0,
        'type'     =>   Varien_Db_Ddl_Table::TYPE_INTEGER,
        'comment'  =>  'Total Quantity Returned'
    ));

$installer->getConnection()->addColumn($installer->getTable('payperrentals/sendreturn'),'resorder_id',
    array(
        'nullable' => false,
        'primary' => false,
        'default'   =>  0,
        'type'     =>   Varien_Db_Ddl_Table::TYPE_INTEGER,
        'comment'  =>  'Reservation Order Id'
    ));

$installer->getConnection()->addColumn($installer->getTable('payperrentals/sendreturn'),'qty_parent',
    array(
        'nullable' => false,
        'primary' => false,
        'default'   =>  0,
        'type'     =>   Varien_Db_Ddl_Table::TYPE_INTEGER,
        'comment'  =>  'Parent Quantity'
    ));

$installer->getConnection()->addIndex($installer->getTable('payperrentals/sendreturn'), $installer->getIdxName(
    'payperrentals/sendreturn',
    array('resorder_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
),
    array('resorder_id'),
    array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX)
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('payperrentals/sendreturn', 'resorder_id', 'payperrentals/reservationorders', 'id'),
    $installer->getTable('payperrentals/sendreturn'),
    'resorder_id',
    $installer->getTable('payperrentals/reservationorders'),
    'id'
);

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'future_reservation_limit', array(
    'backend'       => '',
    'source'        => '',
    'group'		=> 'Rental Bookings',
    'label'         => 'Future Reservation Limit In Days(0 for no limit)',
    'input'         => 'text',
    'class'         => 'validate-digit',
    'type'			=> 'int',
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'default_value' => 0,
    'visible'       => true,
    'required'      => false,
    'user_defined'  => false,
    'default'       => '0',
    'apply_to'      => 'reservation,configurable,grouped,bundle',
    'visible_on_front' => false,
    'position'      =>  100,
));

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'use_global_future_limit', array(
    'group'			=> 'Rental Bookings',
    'label'         => 'Use Global Future Limit',
    'type'			=> 'int',
    'input'         => 'select',
    'source' 		=> 'eav/entity_attribute_source_boolean',
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible'       => true,
    'required'      => false,
    'user_defined'  => false,
    'default'       => '1',
    'default_value' => '1',
    'apply_to'      => 'reservation,configurable,grouped,bundle',
    'visible_on_front' => false,
    'position'      =>  101,
));

$installer->endSetup();
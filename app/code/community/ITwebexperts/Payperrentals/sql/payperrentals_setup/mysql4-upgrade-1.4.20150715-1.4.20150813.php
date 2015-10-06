<?php

/**
 * @var $installer Mage_Core_Model_Resource_Setup
 */
$installer = $this;
$installer->startSetup();

    $table = $installer->getConnection()
        ->newTable($installer->getTable('payperrentals/fixedrentalnames'))
        ->addColumn(
            'id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                'unsigned' => true,
                'identity' => true,
                'nullable' => false,
                'primary'  => true,
            ), 'id'
        )
        ->addColumn(
            'name', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
                'unsigned' => true,
                'nullable' => true,
            ), 'name'
        )
      ;


    $installer->getConnection()->createTable($table);


    $table = $installer->getConnection()
        ->newTable($installer->getTable('payperrentals/fixedrentaldates'))
        ->addColumn(
            'id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                'unsigned' => true,
                'identity' => true,
                'nullable' => false,
                'primary'  => true,
            ), 'id'
        )
        ->addColumn(
            'nameid', Varien_Db_Ddl_Table::TYPE_INTEGER, 100, array(
                'nullable' => true,
                'default'  => null,
            ), 'Fixed name Id'
        )
        ->addColumn(
            'repeat_type', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
                'nullable' => true,
                'default'  => null,
            ), 'Repeat Type'
        )
        ->addColumn(
            'start_date', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
                'nullable' => true,
                'default'  => null,
            ), 'From'
        )
        ->addColumn(
            'end_date', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
                'nullable' => true,
                'default'  => null,
            ), 'To'
        )
        ->addColumn(
            'repeat_days',Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable' => true,
            'default' => null,
            'comment'   =>  'Days to repeat')
        );


$installer->getConnection()->createTable($table);

$installer->getConnection()->addIndex($installer->getTable('payperrentals/fixedrentaldates'), $installer->getIdxName(
    'payperrentals/fixedrentaldates',
    array('nameid'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
),
    array('nameid'),
    array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX)
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('payperrentals/fixedrentaldates', 'nameid', 'payperrentals/fixedrentalnames', 'id'),
    $installer->getTable('payperrentals/fixedrentaldates'),
    'nameid',
    $installer->getTable('payperrentals/fixedrentalnames'),
    'id'
);

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$setup->addAttribute('catalog_product', 'fixed_rental_name', array(
    'backend'       => '',
    'source'        => 'payperrentals/product_fixedrentalnames',
    'group'			=> 'Rental Bookings',
    'label'         => 'Use fixed rental dates',
    'input'         => 'select',
    'type'          => 'int',
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible'       => true,
    'default' 		=> 0,
    'required'      => false,
    'user_defined'  => false,
    'apply_to'      => 'reservation,grouped,bundle,configurable',
    'visible_on_front' => false,
    'position'      =>  25,
));


// Product attributes
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
try {
    $setup->removeAttribute(Mage_Catalog_Model_Product::ENTITY, 'payperrentals_damage_waiver');
    $setup->removeAttribute(Mage_Catalog_Model_Product::ENTITY, 'use_global_damage_waiver');
    $setup->removeAttribute(Mage_Catalog_Model_Product::ENTITY, 'use_global_deposit_per_product');
}catch(Exception $E) {

}
$setup->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'use_global_deposit_per_product', array(
    'group'			=> 'Prices',
    'label'         => 'Use Global Deposit Per Product',
    'type'			=> 'int',
    'input'         => 'select',
    'source' 		=> 'eav/entity_attribute_source_boolean',
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible'       => true,
    'required'      => true,
    'user_defined'  => false,
    'default'       => 1,
    'apply_to'      => 'reservation',
    'visible_on_front' => false,
    'position'      =>  100,
));

$setup->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'use_global_damage_waiver', array(
    'group'			=> 'Prices',
    'label'         => 'Use Global Damage Waiver',
    'type'			=> 'int',
    'input'         => 'select',
    'source' 		=> 'eav/entity_attribute_source_boolean',
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible'       => true,
    'required'      => true,
    'user_defined'  => false,
    'default'       => 1,
    'apply_to'      => 'reservation',
    'visible_on_front' => false,
    'position'      =>  100,
));
$setup->addAttribute('catalog_product', 'payperrentals_damage_waiver', array(
    'backend'       => '',
    'source'        => '',
    'group'		=> 'Prices',
    'label'         => 'Damage Waiver Amount',
    'input'         => 'text',
    'class'         => 'validate-digit',
    'type'			=> 'decimal',
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'default_value' => 1,
    'visible'       => true,
    'required'      => false,
    'user_defined'  => false,
    'default'       => '0',
    'apply_to'      => 'reservation',
    'visible_on_front' => false,
    'position'      =>  14,
));



$fieldList = array('payperrentals_damage_waiver','use_global_deposit_per_product','use_global_damage_waiver');
$applyTo = array('reservation', 'configurable', 'bundle', 'grouped');
foreach ($fieldList as $field) {
    $installer->updateAttribute('catalog_product', $field, 'apply_to', implode(',', $applyTo));
    $installer->updateAttribute('catalog_product', $field, 'is_global', Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE);
}



$installer->endSetup();
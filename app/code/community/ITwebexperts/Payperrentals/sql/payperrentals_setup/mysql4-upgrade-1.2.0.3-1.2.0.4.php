<?php

/**
 * @var $installer Mage_Core_Model_Resource_Setup
 */
$installer = $this;
$installer->startSetup();

/*$installer->addAttribute('catalog_product', 'price_model',array(
    'group'     =>  'Prices',
    'type'      =>  'select',
    'label'     =>  'Choose Pricing Model',
    'input'     =>  'select',
    'global'    =>  Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'source'    =>  'eav/entity_attribute_source_table',
    'visible'   =>  true,
    'required'  =>  true,
    'searchable'    =>  false,
    'filterable' => false,
    'comparable' => false,
    'visible_on_front' => true,
    'unique' => false,
    'apply_to' => 'reservation,bundle',
    'is_configurable' => false,
    'option'    =>  array(
        'Default'   =>  'Default Multiple Price Points',
        'Single'    =>  '1 Price Point Plus $x/day'
    )
));*/

$installer->getConnection()->addColumn($installer->getTable('payperrentals/reservationprices'),'ptypeadditional',
    array(
        'nullable' => true,
        'primary' => false,
        'default'   =>  null,
        'type'     =>   Varien_Db_Ddl_Table::TYPE_INTEGER,
        'comment'  =>  'ptype additional'
    ));

$installer->getConnection()->addColumn($installer->getTable('payperrentals/reservationprices'),'priceadditional',
        array(
            'nullable' => true,
            'primary' => false,
            'default'   =>  null,
            'type'      =>  Varien_Db_Ddl_Table::TYPE_FLOAT,
            'comment'  =>  'Additional Price'
        ));

$installer->endSetup();
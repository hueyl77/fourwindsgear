<?php
/** @var Mage_Customer_Model_Entity_Setup $this */
/** @var Mage_Catalog_Model_Resource_Setup $installer */
$installer = new Mage_Catalog_Model_Resource_Setup('core_setup');

$installer->startSetup();

$installer->removeAttribute(Mage_Catalog_Model_Product::ENTITY, 'use_global_exclude_dates');
$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'use_global_exclude_dates', array(
    'group'			=> 'Payperrentals',
    'label'         => 'Use Global Exclude Dates',
    'type'			=> 'int',
    'input'         => 'select',
    'source' 		=> 'eav/entity_attribute_source_boolean',
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible'       => true,
    'required'      => false,
    'user_defined'  => false,
    'default'       => 1,
    'apply_to'      => 'reservation',
    'visible_on_front' => false,
    'position'      =>  26,
));

$installer->endSetup();
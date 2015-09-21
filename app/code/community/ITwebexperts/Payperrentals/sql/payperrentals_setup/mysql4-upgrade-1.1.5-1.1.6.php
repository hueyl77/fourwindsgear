<?php

$installer = $this;
$installer->startSetup();

// Product attributes
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$setup->removeAttribute(Mage_Catalog_Model_Product::ENTITY, 'use_global_dates');
$setup->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'use_global_dates', array(
    'group'			=> 'Payperrentals',
    'label'         => 'Use Global Event Dates',
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


$fieldList = array('use_global_dates');
$applyTo = array('reservation', 'configurable', 'bundle', 'grouped');
foreach ($fieldList as $field) {
    $installer->updateAttribute('catalog_product', $field, 'apply_to', implode(',', $applyTo));
    $installer->updateAttribute('catalog_product', $field, 'is_global', Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE);
}

$installer->endSetup();
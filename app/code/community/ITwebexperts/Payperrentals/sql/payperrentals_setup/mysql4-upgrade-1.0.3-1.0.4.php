<?php

$installer = new Mage_Catalog_Model_Resource_Setup('core_setup');

$installer->startSetup();

$installer->removeAttribute(Mage_Catalog_Model_Product::ENTITY, 'hide_end_date');
$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'hide_end_date', array(
    'group'			=> 'Payperrentals',
    'label'         => 'Hide End Date on Product Page ',
    'type'			=> 'int',
    'input'         => 'select',
    'source' 		=> 'eav/entity_attribute_source_boolean',
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible'       => true,
    'required'      => false,
    'user_defined'  => false,
    'default'       => 0,
    'apply_to'      => 'reservation',
    'visible_on_front' => false,
    'position'      =>  100,
));

$installer->endSetup();
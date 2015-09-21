<?php

/**
 * @var $installer Mage_Core_Model_Resource_Setup
 */
$installer = $this;
$installer->startSetup();

$attribute = $installer->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'use_global_exclude_dates');
$installer->addAttributeToGroup(
    Mage_Catalog_Model_Product::ENTITY, //catalog_product
    $installer->getDefaultAttributeSetId(Mage_Catalog_Model_Product::ENTITY), //Attribute Set Id
    'Rental Bookings', //Group Name
    $attribute['attribute_id'], //attribute id
    22
);

$installer->endSetup();
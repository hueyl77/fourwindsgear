<?php

/**
 * @var $installer Mage_Core_Model_Resource_Setup
 */
$installer = $this;
$installer->startSetup();
$_fieldList = array('use_global_exclude_dates');
$_applyTo = array('reservation', 'configurable', 'bundle', 'grouped');
foreach ($_fieldList as $_field) {
    $installer->updateAttribute(Mage_Catalog_Model_Product::ENTITY, $_field, 'apply_to', implode(',', $_applyTo));
    $installer->updateAttribute(Mage_Catalog_Model_Product::ENTITY, $_field, 'is_global', Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE);
}

$installer->endSetup();
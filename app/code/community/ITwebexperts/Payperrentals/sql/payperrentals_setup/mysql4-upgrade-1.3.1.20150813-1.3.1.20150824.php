<?php
/** @var Mage_Customer_Model_Entity_Setup $this */
/** @var Mage_Catalog_Model_Resource_Setup $installer */
$installer = new Mage_Catalog_Model_Resource_Setup('core_setup');

$installer->startSetup();

$attribute_model        = Mage::getModel('eav/entity_attribute');
$attribute_code         = $attribute_model->getIdByCode('catalog_product', 'use_global_padding_days');
$resource = Mage::getSingleton('core/resource');
$writeConnection = $resource->getConnection('core_write');
$table = $resource->getTableName('eav/entity_attribute');
$newSortOrder = '20';
$query = "UPDATE {$table} SET sort_order = '{$newSortOrder}' WHERE attribute_id = "
    . (int)$attribute_code;

$writeConnection->query($query);

$installer->endSetup();
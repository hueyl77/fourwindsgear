<?php

$installer = $this;
$installer->startSetup();

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$entityTypeId = $setup->getEntityTypeId('catalog_product');
$select = $this->_conn->select()
    ->from($this->getTable('eav/attribute_set'))
    ->where('entity_type_id = :entity_type_id');
$sets = $this->_conn->fetchAll($select, array('entity_type_id' => $entityTypeId));


foreach ($sets as $set) {
    $setup->addAttributeToGroup('catalog_product', $set['attribute_set_id'], 'Prices', 'res_prices');
    $setup->addAttributeGroup('catalog_product', $set['attribute_set_id'], 'Inventory');
    $setup->addAttributeToGroup('catalog_product', $set['attribute_set_id'], 'Inventory', 'payperrentals_quantity');
    $setup->addAttributeToGroup('catalog_product', $set['attribute_set_id'], 'Inventory', 'payperrentals_use_serials');
    $setup->addAttributeToGroup('catalog_product', $set['attribute_set_id'], 'Inventory', 'res_serialnumbers');
}




$installer->endSetup();

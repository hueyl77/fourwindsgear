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
    $setup->addAttributeToGroup('catalog_product', $set['attribute_set_id'], 'Prices', 'payperrentals_buyoutprice');
    $setup->addAttributeToGroup('catalog_product', $set['attribute_set_id'], 'Prices', 'payperrentals_enable_extend');
    $setup->addAttributeToGroup('catalog_product', $set['attribute_set_id'], 'Prices', 'payperrentals_enable_buyout');
    $setup->addAttributeToGroup('catalog_product', $set['attribute_set_id'], 'Prices', 'payperrentals_buyout_onproduct');
    $setup->addAttributeToGroup('catalog_product', $set['attribute_set_id'], 'Prices', 'bundle_pricingtype');
}




$installer->endSetup();
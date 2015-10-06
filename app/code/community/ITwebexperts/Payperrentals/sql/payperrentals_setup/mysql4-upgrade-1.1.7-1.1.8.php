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
    $setup->updateAttributeGroup($entityTypeId, $set['attribute_set_id'], 'Payperrentals', 'attribute_group_name', 'Rental Bookings');
    $setup->addAttributeToGroup($entityTypeId, $set['attribute_set_id'], 'Prices', 'payperrentals_pricingtype');
    $setup->addAttributeToGroup($entityTypeId, $set['attribute_set_id'], 'Prices', 'payperrentals_deposit');
    $setup->addAttributeToGroup($entityTypeId, $set['attribute_set_id'], 'Inventory', 'allow_overbooking');
}




$installer->endSetup();

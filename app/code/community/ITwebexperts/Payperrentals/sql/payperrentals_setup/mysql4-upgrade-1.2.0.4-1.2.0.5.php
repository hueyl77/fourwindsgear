<?php

/**
 * @var $installer Mage_Core_Model_Resource_Setup
 */
$installer = $this;
$installer->startSetup();
// Product attributes
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
// Buyout price
$setup->addAttribute('catalog_product', 'payperrentals_buyoutprice', array(
        'backend'       => '',
        'source'        => '',
        'group'		=> 'Payperrentals',
        'label'         => 'Buyout Price',
        'input'         => 'text',
        'class'         => 'validate-zero-or-greater',
        'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
        'visible'       => true,
        'required'      => false,
        'user_defined'  => false,
        'type'			=> 'decimal',
        'apply_to'      => 'reservation',
        'visible_on_front' => false,
        'position'      =>  300,
    ));

$setup->addAttribute('catalog_product', 'payperrentals_enable_buyout', array(
        'backend'       => '',
        'type' => 'int',
        'input' => 'select',
        'source'  => 'eav/entity_attribute_source_boolean',
        'group'		=> 'Payperrentals',
        'label'         => 'Enable Rental Buyouts',
        'class'         => '',
        'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
        'default_value' => 0,
        'visible'       => true,
        'required'      => false,
        'user_defined'  => false,
        'default'       => '0',
        'apply_to'      => array('reservation', 'configurable'),
        'visible_on_front' => false,
        'position'      =>  320
    ));

$setup->addAttribute('catalog_product', 'payperrentals_buyout_onproduct', array(
        'backend'       => '',
        'type' => 'int',
        'input' => 'select',
        'source'  => 'eav/entity_attribute_source_boolean',
        'group'		=> 'Payperrentals',
        'label'         => 'Show Buyout Button on Product Page',
        'class'         => '',
        'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
        'default_value' => 0,
        'visible'       => true,
        'required'      => false,
        'user_defined'  => false,
        'default'       => '0',
        'apply_to'      => array('reservation', 'configurable'),
        'visible_on_front' => false,
        'position'      =>  330
    ));

$fieldList = array('payperrentals_enable_buyout','payperrentals_buyout_onproduct','payperrentals_buyoutprice');
$applyTo = array('reservation', 'configurable', 'bundle', 'grouped');
foreach ($fieldList as $field) {
    $installer->updateAttribute('catalog_product', $field, 'apply_to', implode(',', $applyTo));
    $installer->updateAttribute('catalog_product', $field, 'is_global', Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE);
}

$entityTypeId = $setup->getEntityTypeId('catalog_product');
$select = $this->_conn->select()
    ->from($this->getTable('eav/attribute_set'))
    ->where('entity_type_id = :entity_type_id');
$sets = $this->_conn->fetchAll($select, array('entity_type_id' => $entityTypeId));


foreach ($sets as $set) {
    $setup->addAttributeToGroup('catalog_product', $set['attribute_set_id'], 'Prices', 'payperrentals_buyoutprice');
    $setup->addAttributeToGroup('catalog_product', $set['attribute_set_id'], 'Prices', 'payperrentals_enable_buyout');
    $setup->addAttributeToGroup('catalog_product', $set['attribute_set_id'], 'Prices', 'payperrentals_buyout_onproduct');
}

$installer->endSetup();
<?php

$installer = $this;
$installer->startSetup();

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

try {
    $setup->removeAttribute('catalog_product', 'shipping_method');
    $setup->removeAttribute('catalog_product', 'allow_shipping');
} catch (Exception $e) {

}

$setup->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'allow_shipping', array(
    'group' => 'Rental Shipping Method',
    'type' => 'int',
    'backend' => '',
    'frontend' => '',
    'label' => 'Customer Choses Shipping Method on Product Page',
    'input' => 'boolean',
    'source' => 'eav/entity_attribute_source_table',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'is_visible' => true,
    'required' => false,
    'user_defined' => true,
    'default' => '',
    'is_searchable' => false,
    'is_filterable' => false,
    'is_comparable' => false,
    'is_visible_on_front' => true,
    'is_visible_in_advanced_search' => false,
    'is_used_in_product_listing' => false,
    'unique' => false,
));

$setup->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'shipping_method', array(
    'group' => 'Rental Shipping Method',
    'type' => 'varchar',
    'backend' => '',
    'frontend' => '',
    'source' => 'rshipping/entity_attribute_source_method',
    'label' => 'Allowed Shipping Methods',
    'input' => 'multiselect',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'is_visible' => true,
    'required' => false,
    'user_defined' => true,
    'default' => '',
    'is_searchable' => true,
    'is_filterable' => false,
    'is_comparable' => false,
    'is_visible_on_front' => false,
    'is_visible_in_advanced_search' => false,
    'is_used_in_product_listing' => false,
    'unique' => false,
    'is_html_allowed_on_front' => true
));

$installer->endSetup();
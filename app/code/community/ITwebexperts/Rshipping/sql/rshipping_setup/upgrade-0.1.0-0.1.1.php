<?php

/** @var $this Mage_Core_Model_Resource_Setup */
$_installer = $this;
$_installer->startSetup();

$_setup = new Mage_Eav_Model_Entity_Setup('core_setup');

try {
    $_setup->removeAttribute('catalog_product', 'shipping_method');
    $_setup->removeAttribute('catalog_product', 'allow_shipping');
} catch (Exception $_e) {
    Mage::logException($_e);
}

$_setup->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'allow_shipping', array(
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

$_setup->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'shipping_method', array(
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

$_installer->endSetup();
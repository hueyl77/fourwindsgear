<?php

$installer = $this;
$installer->startSetup();

// Product attributes
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

$setup->addAttribute('catalog_product', 'payperrentals_enable_extend', array(
    'backend'       => '',
    'type' => 'int',
    'input' => 'select',
    'source'  => 'eav/entity_attribute_source_boolean',
    'group'		=> 'Payperrentals',
    'label'         => 'Enable Rental Extensions',
    'class'         => '',
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'default_value' => 0,
    'visible'       => true,
    'required'      => false,
    'user_defined'  => false,
    'default'       => '0',
    'apply_to'      => array('reservation', 'configurable'),
    'visible_on_front' => false,
    'position'      =>  310
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


$fieldList = array('payperrentals_enable_extend','payperrentals_enable_buyout','payperrentals_buyout_onproduct','payperrentals_buyoutprice');
$applyTo = array('reservation', 'configurable', 'bundle', 'grouped');
foreach ($fieldList as $field) {
    $installer->updateAttribute('catalog_product', $field, 'apply_to', implode(',', $applyTo));
    $installer->updateAttribute('catalog_product', $field, 'is_global', Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE);
}

$installer->endSetup();
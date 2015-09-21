<?php

$installer = $this;
$installer->startSetup();

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
//
//try {
//    $setup->removeAttribute('catalog_product', 'payperrentals_min_quantity');
//    $setup->removeAttribute('catalog_product', 'payperrentals_max_quantity');
//
//}catch(Exception $E) {
//
//}

$setup = $this;
$setup->addAttribute('catalog_product', 'payperrentals_min_quantity', array(
    'backend'       => '',
    'source'        => '',
    'group'		=> 'Inventory',
    'label'         => 'Minimum Qty Allowed in Shopping Cart',
    'input'         => 'text',
    'class'         => 'input-text validate-digits',
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'default_value' => '1',
    'visible'       => true,
    'required'      => false,
    'user_defined'  => false,
    'type'			=> 'int',
    'default'       => '1',
    'apply_to'      => 'reservation',
    'visible_on_front' => false,
    'position'      =>  21,
));

$setup->addAttribute('catalog_product', 'payperrentals_max_quantity', array(
    'backend'       => '',
    'source'        => '',
    'group'		=> 'Inventory',
    'label'         => 'Maximum Qty Allowed in Shopping Cart',
    'input'         => 'text',
    'class'         => 'input-text validate-digits',
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'default_value' => '10000',
    'visible'       => true,
    'required'      => false,
    'user_defined'  => false,
    'type'			=> 'int',
    'default'       => '10000',
    'apply_to'      => 'reservation',
    'visible_on_front' => false,
    'position'      =>  31,
));


$installer->endSetup();

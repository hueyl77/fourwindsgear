<?php

$_installer = $this;
$_installer->startSetup();

// Product attributes
$_setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$_setup->removeAttribute(Mage_Catalog_Model_Product::ENTITY, 'reservation_cost');
$_setup->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'reservation_cost', array(
    'group'			=> 'Prices',
    'label'         => 'Acquisition Cost',
    'type'			=> 'decimal',
    'input'         => 'price',
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible'       => true,
    'required'      => false,
    'user_defined'  => false,
    'default'       => '0.0000',
    'apply_to'      => 'reservation',
    'visible_on_front' => false,
    'position'      =>  100,
));


$_fieldList = array('reservation_cost');
$_applyTo = array('reservation', 'configurable', 'bundle', 'grouped');
foreach ($_fieldList as $_field) {
    $_installer->updateAttribute(Mage_Catalog_Model_Product::ENTITY, $_field, 'apply_to', implode(',', $_applyTo));
    $_installer->updateAttribute(Mage_Catalog_Model_Product::ENTITY, $_field, 'is_global', Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE);
}

$_installer->endSetup();
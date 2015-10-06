<?php

/**
 * @var $installer Mage_Core_Model_Resource_Setup
 */

$installer = $this;
$installer->startSetup();

// Product attributes
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$setup->removeAttribute('catalog_product', 'inventory_serialized');
$setup->addAttribute('catalog_product', 'inventory_serialized', array(
        'group'			=> 'Rental Bookings',
        'label'         => 'Inventory Serialized',
        'input'         => 'text',
        'type'          => 'text',
        'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
        'visible'       => false,
        'default' 		=> 'not_updated',
        'required'      => false,
        'user_defined'  => false,
        'apply_to'      => 'reservation',
        'visible_on_front' => false,
        'position'      =>  27,
));
$fieldList = array('inventory_serialized');
$applyTo = array('reservation', 'configurable', 'bundle', 'grouped');
foreach ($fieldList as $field) {
    $installer->updateAttribute('catalog_product', $field, 'apply_to', implode(',', $applyTo));
    $installer->updateAttribute('catalog_product', $field, 'is_global', Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE);
    $installer->updateAttribute('catalog_product', $field, 'is_visible', false);
}

$installer->endSetup();
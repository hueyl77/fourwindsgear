<?php

/**
 * @var $installer Mage_Core_Model_Resource_Setup
 */
$installer = $this;
$installer->startSetup();
// Product attributes
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');


$setup->addAttribute('catalog_product', 'enable_extend_order', array(
        'backend'       => '',
        'type' => 'int',
        'input' => 'select',
        'source'  => 'payperrentals/product_extendorderoption',
        'group'		=> 'Rental Bookings',
        'label'         => 'Enable Extend Order',
        'class'         => '',
        'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
        'default_value' => 0,
        'visible'       => true,
        'required'      => false,
        'user_defined'  => false,
        'default'       => '0',
        'apply_to'      => array('reservation', 'configurable', 'bundle', 'grouped'),
        'visible_on_front' => false,
        'position'      =>  320
    ));


$fieldList = array('enable_extend_order');
$applyTo = array('reservation', 'configurable', 'bundle', 'grouped');
foreach ($fieldList as $field) {
    $installer->updateAttribute('catalog_product', $field, 'apply_to', implode(',', $applyTo));
    $installer->updateAttribute('catalog_product', $field, 'is_global', Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE);
}

$installer->endSetup();
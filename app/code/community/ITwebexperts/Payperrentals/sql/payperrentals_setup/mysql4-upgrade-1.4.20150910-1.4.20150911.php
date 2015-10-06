<?php

/**
 * @var $installer Mage_Eav_Model_Entity_Setup
 */
$installer = $this;
$installer->startSetup();

$installer->updateAttribute('catalog_product', 'is_reservation', array(
'backend'       => '',
'source'        => 'payperrentals/product_isreservation',
'group'			=> 'General',
'frontend_label'=> 'Reservation Type',
'input'         => 'select',
'type'          => 'int',
'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
'visible'       => true,
'default' 		=> null,
'required'      => 1,
'user_defined'  => false,
'apply_to'      => 'configurable,bundle,reservation',
'visible_on_front' => false,
'position'      =>  10,
    'class'     =>  'required-entry select'
));


$installer->endSetup();
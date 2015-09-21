<?php
/** @var $this Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$setup = new Mage_Catalog_Model_Resource_Setup('core_setup');


$setup->addAttribute('catalog_product','maintenance_quantity',array(
    'group'    =>  'Inventory',
    'input'     =>  'text',
    'type'      =>  'int',
    'global'    =>  Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible'   =>  true,
    'required'  =>  false,
    'user_defined'  =>  false,
    'default'   =>  0,
    'apply_to'  =>  'reservation',
    'visible_on_frontend'   =>  false,
    'position'  =>  2,
    'label' =>  'Maintenance Quantity',
    'searchable'    =>  0,
    'filterable'    =>  0,
    'visible_in_advanced_search'    =>  0,
    'note'  =>  'Used only for quantity based non-specific dates maintenance, otherwise inventory in maintenance is checked by dates. It is usually NOT necessary to modify this field manually as this is done automatically via the maintenance module.'
));

$installer->endSetup();
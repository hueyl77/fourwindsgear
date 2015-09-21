<?php

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


// Notification flag
$installer->run("
	ALTER TABLE {$this->getTable('payperrentals/reservationorders')}
		ADD `extend_notification_sent` INT( 11 ) NOT NULL DEFAULT '0';
");


$installer->endSetup();
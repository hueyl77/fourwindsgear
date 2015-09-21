<?php

$installer = $this;
$installer->startSetup();

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

try {
    $setup->removeAttribute('catalog_product', 'payperrentals_quantity');
	$setup->removeAttribute('catalog_product', 'payperrentals_min_number');
	$setup->removeAttribute('catalog_product', 'payperrentals_min_type');
	$setup->removeAttribute('catalog_product', 'global_min_period');
	$setup->removeAttribute('catalog_product', 'payperrentals_max_number');
	$setup->removeAttribute('catalog_product', 'payperrentals_max_type');

	$setup->removeAttribute('catalog_product', 'global_max_period');
	$setup->removeAttribute('catalog_product', 'payperrentals_avail_number');
	$setup->removeAttribute('catalog_product', 'payperrentals_avail_type');
	$setup->removeAttribute('catalog_product', 'global_turnover_after');
	$setup->removeAttribute('catalog_product', 'payperrentals_avail_numberb');
	$setup->removeAttribute('catalog_product', 'payperrentals_avail_typeb');
	$setup->removeAttribute('catalog_product', 'global_turnover_before');

	$setup->removeAttribute('catalog_product', 'payperrentals_deposit');
	$setup->removeAttribute('catalog_product', 'payperrentals_has_shipping');
	$setup->removeAttribute('catalog_product', 'payperrentals_has_multiply');
	$setup->removeAttribute('catalog_product', 'payperrentals_pricingtype');
	$setup->removeAttribute('catalog_product', 'payperrentals_use_send_return');
	$setup->removeAttribute('catalog_product', 'payperrentals_use_times');
	$setup->removeAttribute('catalog_product', 'payperrentals_padding_days');

	$setup->removeAttribute('catalog_product', 'disabled_with_message');
	$setup->removeAttribute('catalog_product', 'res_excluded_daysweek');
	$setup->removeAttribute('catalog_product', 'global_excludedays');
	$setup->removeAttribute('catalog_product', 'allow_overbooking');
	$setup->removeAttribute('catalog_product', 'res_excluded_dates');
	$setup->removeAttribute('catalog_product', 'res_prices');
	$setup->removeAttribute('catalog_product', 'payperrentals_use_serials');
	$setup->removeAttribute('catalog_product', 'res_serialnumbers');
	$setup->removeAttribute('catalog_product', 'is_reservation');


}catch(Exception $E) {

}

$setup = $this;
$setup->addAttribute('catalog_product', 'payperrentals_quantity', array(
	'backend'       => '',
	'source'        => '',
	'group'		=> 'Payperrentals',
	'label'         => 'Quantity',
	'input'         => 'text',
	'class'         => 'validate-digit',
	'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
	'default_value' => 1,
	'visible'       => true,
	'required'      => true,
	'user_defined'  => false,
	'type'			=> 'int',
	'default'       => '1',
	'apply_to'      => 'reservation',
	'visible_on_front' => false,
    'position'      =>  1,
));

$setup->addAttribute('catalog_product', 'payperrentals_min_number', array(
	'backend'       => '',
	'source'        => '',
	'group'		=> 'Payperrentals',
	'label'         => 'Minimum Period',
	'input'         => 'text',
	'class'         => 'validate-digit',
	'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
	'default_value' => 1,
	'type'			=> 'int',
	'visible'       => true,
	'required'      => false,
	'user_defined'  => false,
	'default'       => '0',
	'apply_to'      => 'reservation',
	'visible_on_front' => false,
	'position'      =>  2,
));

$setup->addAttribute('catalog_product', 'payperrentals_min_type', array(
	'backend'       => '',
	'source'        => 'payperrentals/product_periodtype',
	'group'		=> 'Payperrentals',
	'label'         => 'Minimum Period Type',
	'input'         => 'select',
	'type'			=> 'int',
	'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
	'default_value' => 1,
	'visible'       => true,
	'required'      => false,
	'user_defined'  => false,
	'default'       => '0',
	'apply_to'      => 'reservation',
	'visible_on_front' => false,
	'position'      =>  3,
));

$setup->addAttribute('catalog_product', 'global_min_period', array(
	'backend'       => '',
	'type' => 'int',
	'input' => 'select',
	'source'  => 'eav/entity_attribute_source_boolean',
	'group'		=> 'Payperrentals',
	'label'         => 'Use Global Config for minimum period',
	'class'         => '',
	'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
	'default_value' => 1,
	'visible'       => true,
	'required'      => true,
	'user_defined'  => false,
	'default'       => '1',
	'apply_to'      => 'reservation',
	'visible_on_front' => false,
	'position'      =>  4,
));

$setup->addAttribute('catalog_product', 'payperrentals_max_number', array(
	'backend'       => '',
	'source'        => '',
	'group'		=> 'Payperrentals',
	'label'         => 'Maximum Period',
	'input'         => 'text',
	'class'         => 'validate-digit',
	'type'			=> 'int',
	'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
	'default_value' => 1,
	'visible'       => true,
	'required'      => false,
	'user_defined'  => false,
	'default'       => '0',
	'apply_to'      => 'reservation',
	'visible_on_front' => false,
	'position'      =>  5,
));

$setup->addAttribute('catalog_product', 'payperrentals_max_type', array(
	'backend'       => '',
	'source'        => 'payperrentals/product_periodtype',
	'group'		=> 'Payperrentals',
	'label'         => 'Maximum Period Type',
	'input'         => 'select',
	'type'			=> 'int',
	'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
	'default_value' => 1,
	'visible'       => true,
	'required'      => false,
	'user_defined'  => false,
	'default'       => '0',
	'apply_to'      => 'reservation',
	'visible_on_front' => false,
	'position'      =>  6,
));

$setup->addAttribute('catalog_product', 'global_max_period', array(
	'backend'       => '',
	'type' => 'int',
	'input' => 'select',
	'source'  => 'eav/entity_attribute_source_boolean',
	'group'		=> 'Payperrentals',
	'label'         => 'Use Global Config for maximum period',
	'class'         => '',
	'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
	'default_value' => 1,
	'visible'       => true,
	'required'      => true,
	'user_defined'  => false,
	'default'       => '1',
	'apply_to'      => 'reservation',
	'visible_on_front' => false,
	'position'      =>  7,
));

$setup->addAttribute('catalog_product', 'payperrentals_avail_number', array(
	'backend'       => '',
	'source'        => '',
	'group'		=> 'Payperrentals',
	'label'         => 'Turnover After Period',
	'input'         => 'text',
	'class'         => 'validate-digit',
	'type'			=> 'int',
	'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
	'default_value' => 1,
	'visible'       => true,
	'required'      => false,
	'user_defined'  => false,
	'default'       => '0',
	'apply_to'      => 'reservation',
	'visible_on_front' => false,
	'position'      =>  8,
));

$setup->addAttribute('catalog_product', 'payperrentals_avail_type', array(
	'backend'       => '',
	'source'        => 'payperrentals/product_periodtype',
	'group'		=> 'Payperrentals',
	'label'         => 'Turnover After Type',
	'input'         => 'select',
	'type'			=> 'int',
	'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
	'default_value' => 1,
	'visible'       => true,
	'required'      => false,
	'user_defined'  => false,
	'default'       => '0',
	'apply_to'      => 'reservation',
	'visible_on_front' => false,
	'position'      =>  9,
));

$setup->addAttribute('catalog_product', 'global_turnover_after', array(
	'backend'       => '',
	'type' => 'int',
	'input' => 'select',
	'source'  => 'eav/entity_attribute_source_boolean',
	'group'		=> 'Payperrentals',
	'label'         => 'Use Global Config for Turnover After',
	'class'         => '',
	'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
	'default_value' => 1,
	'visible'       => true,
	'required'      => true,
	'user_defined'  => false,
	'default'       => '1',
	'apply_to'      => 'reservation',
	'visible_on_front' => false,
	'position'      =>  10,
));

$setup->addAttribute('catalog_product', 'payperrentals_avail_numberb', array(
	'backend'       => '',
	'source'        => '',
	'group'		=> 'Payperrentals',
	'label'         => 'Turnover Before Period',
	'input'         => 'text',
	'class'         => 'validate-digit',
	'type'			=> 'int',
	'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
	'default_value' => 1,
	'visible'       => true,
	'required'      => false,
	'user_defined'  => false,
	'default'       => '0',
	'apply_to'      => 'reservation',
	'visible_on_front' => false,
	'position'      =>  11,
));

$setup->addAttribute('catalog_product', 'payperrentals_avail_typeb', array(
	'backend'       => '',
	'source'        => 'payperrentals/product_periodtype',
	'group'		=> 'Payperrentals',
	'label'         => 'Turnover Before Type',
	'input'         => 'select',
	'type'			=> 'int',
	'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
	'default_value' => 1,
	'visible'       => true,
	'required'      => false,
	'user_defined'  => false,
	'default'       => '0',
	'apply_to'      => 'reservation',
	'visible_on_front' => false,
	'position'      =>  12,
));


$setup->addAttribute('catalog_product', 'global_turnover_before', array(
	'backend'       => '',
	'type' => 'int',
	'input' => 'select',
	'source'  => 'eav/entity_attribute_source_boolean',
	'group'		=> 'Payperrentals',
	'label'         => 'Use Global Config for Turnover before',
	'class'         => '',
	'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
	'default_value' => 1,
	'visible'       => true,
	'required'      => true,
	'user_defined'  => false,
	'default'       => '1',
	'apply_to'      => 'reservation',
	'visible_on_front' => false,
	'position'      =>  13,
));

$setup->addAttribute('catalog_product', 'payperrentals_deposit', array(
	'backend'       => '',
	'source'        => '',
	'group'		=> 'Payperrentals',
	'label'         => 'Deposit Amount',
	'input'         => 'text',
	'class'         => 'validate-digit',
	'type'			=> 'decimal',
	'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
	'default_value' => 1,
	'visible'       => true,
	'required'      => false,
	'user_defined'  => false,
	'default'       => '0',
	'apply_to'      => 'reservation',
	'visible_on_front' => false,
	'position'      =>  14,
));


$setup->addAttribute('catalog_product', 'payperrentals_has_shipping', array(
	'backend'       => '',
	'source'        => 'payperrentals/product_hasshipping',
	'group'			=> 'Payperrentals',
	'label'         => 'Has Shipping',
	'input'         => 'select',
	'type'          => 'int',
	'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
	'visible'       => true,
	'default' 		=> 1,
	'required'      => false,
	'user_defined'  => false,
	'apply_to'      => 'reservation',
	'visible_on_front' => false,
	'position'      =>  15,
));

$setup->addAttribute('catalog_product', 'payperrentals_has_multiply', array(
	'backend'       => '',
	'source'        => 'payperrentals/product_hasmultiply',
	'group'			=> 'Payperrentals',
	'label'         => 'Has Multiply Custom Options',
	'input'         => 'select',
	'type'          => 'int',
	'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
	'visible'       => true,
	'default' 		=> 1,
	'required'      => false,
	'user_defined'  => false,
	'apply_to'      => 'reservation',
	'visible_on_front' => false,
	'position'      =>  16,
));

$setup->addAttribute('catalog_product', 'payperrentals_pricingtype', array(
	'backend'       => '',
	'source'        => 'payperrentals/product_pricingtype',
	'group'			=> 'Payperrentals',
	'label'         => 'Pricing Type',
	'input'         => 'select',
	'type'          => 'int',
	'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
	'visible'       => true,
	'default' 		=> 2,
	'required'      => false,
	'user_defined'  => false,
	'apply_to'      => 'reservation',
	'visible_on_front' => false,
	'position'      =>  17,
));

$setup->addAttribute('catalog_product', 'payperrentals_use_send_return', array(
	'backend'       => '',
	'source'        => 'payperrentals/product_usesendreturn',
	'group'			=> 'Payperrentals',
	'label'         => 'Use Send/Return',
	'input'         => 'select',
	'type'          => 'int',
	'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
	'visible'       => true,
	'default' 		=> 1,
	'required'      => false,
	'user_defined'  => false,
	'apply_to'      => 'reservation',
	'visible_on_front' => false,
	'position'      =>  19,
));

$setup->addAttribute('catalog_product', 'payperrentals_use_times', array(
	'backend'       => '',
	'source'        => 'payperrentals/product_usetimes',
	'group'			=> 'Payperrentals',
	'label'         => 'Use Times',
	'input'         => 'select',
	'type'          => 'int',
	'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
	'visible'       => true,
	'default' 		=> 0,
	'required'      => false,
	'user_defined'  => false,
	'apply_to'      => 'reservation',
	'visible_on_front' => false,
	'position'      =>  20,
));

$setup->addAttribute('catalog_product', 'payperrentals_padding_days', array(
	'backend'       => '',
	'source'        => '',
	'group'		=> 'Payperrentals',
	'label'         => 'Padding days',
	'input'         => 'text',
	'class'         => 'validate-digit',
	'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
	'default_value' => 1,
	'type'			=> 'int',
	'visible'       => true,
	'required'      => false,
	'user_defined'  => false,
	'default'       => '0',
	'apply_to'      => 'reservation',
	'visible_on_front' => false,
	'position'      =>  21,
));

$setup->addAttribute('catalog_product', 'disabled_with_message', array(
	'backend'       => '',
	'source'        => 'payperrentals/product_disabledwithmessage',
	'group'			=> 'Payperrentals',
	'label'         => 'Disable With Message',
	'input'         => 'select',
	'type'          => 'int',
	'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
	'visible'       => true,
	'default' 		=> 0,
	'required'      => true,
	'user_defined'  => false,
	'apply_to'      => 'reservation',
	'visible_on_front' => false,
	'position'      =>  22,
));

$setup->addAttribute('catalog_product', 'res_excluded_daysweek', array(
	'backend'       => 'eav/entity_attribute_backend_array',
	'source'        => 'payperrentals/product_excludedaysweek',
	'group'			=> 'Payperrentals',
	'label'         => 'Exclude Days Of Week',
	'input'         => 'multiselect',
	'type'          => 'varchar',
	'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
	'visible'       => true,
	'default' 		=> 0,
	'required'      => false,
	'user_defined'  => false,
	'apply_to'      => 'reservation',
	'visible_on_front' => false,
	'position'      =>  23,
));

$setup->addAttribute('catalog_product', 'global_excludedays', array(
	'backend'       => '',
	'type' => 'int',
	'input' => 'select',
	'source'  => 'eav/entity_attribute_source_boolean',
	'group'		=> 'Payperrentals',
	'label'         => 'Use Global Config for ExcludeDays',
	'class'         => '',
	'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
	'default_value' => 1,
	'visible'       => true,
	'required'      => true,
	'user_defined'  => false,
	'default'       => '1',
	'apply_to'      => 'reservation',
	'visible_on_front' => false,
	'position'      =>  24,
));

$setup->addAttribute('catalog_product', 'allow_overbooking', array(
	'backend'       => '',
	'source'        => 'payperrentals/product_allowoverbooking',
	'group'			=> 'Payperrentals',
	'label'         => 'Allow Overbooking',
	'input'         => 'select',
	'type'          => 'int',
	'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
	'visible'       => true,
	'default' 		=> 0,
	'required'      => true,
	'user_defined'  => false,
	'apply_to'      => 'reservation',
	'visible_on_front' => false,
	'position'      =>  25,
));


$setup->addAttribute('catalog_product', 'res_excluded_dates', array(
	'backend'       => 'payperrentals/product_backend_excludeddates',
	'source'        => '',
	'group'			=> 'Payperrentals',
	'label'         => 'Dates Excluded',
	'input'         => 'text',
	'type'          => 'text',
	'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
	'visible'       => true,
	'default' 		=> 0,
	'required'      => false,
	'user_defined'  => false,
	'apply_to'      => 'reservation',
	'visible_on_front' => false,
	'position'      =>  26,
));

$setup->addAttribute('catalog_product', 'res_prices', array(
	'backend'       => 'payperrentals/product_backend_reservationprices',
	'source'        => '',
	'group'			=> 'Payperrentals',
	'label'         => 'Pricing',
	'input'         => 'text',
	'type'          => 'text',
	'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
	'visible'       => true,
	'default' 		=> 0,
	'required'      => false,
	'user_defined'  => false,
	'apply_to'      => 'reservation',
	'visible_on_front' => false,
	'position'      =>  27,
));

$setup->addAttribute('catalog_product', 'payperrentals_use_serials', array(
	'backend'       => '',
	'source'        => 'payperrentals/product_useserials',
	'group'			=> 'Payperrentals',
	'label'         => 'Use Serials on Send/Return',
	'input'         => 'select',
	'type'          => 'int',
	'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
	'visible'       => true,
	'default' 		=> 0,
	'required'      => false,
	'user_defined'  => false,
	'apply_to'      => 'reservation',
	'visible_on_front' => false,
	'position'      =>  28,
));

$setup->addAttribute('catalog_product', 'res_serialnumbers', array(
	'backend'       => 'payperrentals/product_backend_serialnumbers',
	'source'        => '',
	'group'			=> 'Payperrentals',
	'label'         => 'Serial Numbers',
	'input'         => 'text',
	'type'          => 'text',
	'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
	'visible'       => true,
	'default' 		=> 0,
	'required'      => false,
	'user_defined'  => false,
	'apply_to'      => 'reservation',
	'visible_on_front' => false,
	'position'      =>  29,
));

$setup->addAttribute('catalog_product', 'is_reservation', array(
	'backend'       => '',
	'source'        => 'payperrentals/product_isreservation',
	'group'			=> 'General',
	'label'         => 'Is reservation',
	'input'         => 'select',
	'type'          => 'int',
	'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
	'visible'       => true,
	'default' 		=> 0,
	'required'      => true,
	'user_defined'  => false,
	'apply_to'      => 'configurable',
	'visible_on_front' => false,
	'position'      =>  10,
));



$fieldList = array('tax_class_id', 'weight');
foreach ($fieldList as $field) {
	$applyTo = explode(',', $installer->getAttribute('catalog_product', $field, 'apply_to'));
	if (!in_array('reservation', $applyTo)) {
		$applyTo[] = 'reservation';
		$installer->updateAttribute('catalog_product', $field, 'apply_to', join(',', $applyTo));
	}
}

$fieldList = array('tax_class_id','payperrentals_quantity','payperrentals_min_number','payperrentals_min_type','payperrentals_max_number','payperrentals_max_type','payperrentals_avail_number','payperrentals_avail_type','payperrentals_avail_numberb','payperrentals_avail_typeb','payperrentals_deposit','payperrentals_has_shipping','payperrentals_has_multiply','payperrentals_pricingtype','payperrentals_use_serials','payperrentals_use_send_return','payperrentals_use_times','res_excluded_dates','res_prices','payperrentals_padding_days','res_serialnumbers','disabled_with_message','res_excluded_daysweek','allow_overbooking');
foreach ($fieldList as $field) {
	$applyTo = explode(',', $installer->getAttribute('catalog_product', $field, 'apply_to'));
	if (!in_array('configurable', $applyTo)) {
		$applyTo[] = 'configurable';
		$installer->updateAttribute('catalog_product', $field, 'apply_to', implode(',', $applyTo));
	}
}

$installer->run("
	DROP TABLE IF EXISTS {$this->getTable('payperrentals/excludeddates')};

	CREATE TABLE {$this->getTable('payperrentals/excludeddates')} (
		`id` INT( 11 ) NOT NULL auto_increment,
		`entity_id` INT( 11 ) NOT NULL ,
		`store_id` INT( 11 ) NOT NULL ,
		`disabled_type` ENUM('none','dayweek','daily', 'monthly', 'yearly' ) NOT NULL,
		`disabled_from` DATETIME NOT NULL ,
		`disabled_to` DATETIME NOT NULL ,
		PRIMARY KEY ( `id` ) ,
		KEY `store_id` (`store_id`),
		INDEX ( `entity_id` )

	) DEFAULT CHARSET utf8 ENGINE = InnoDB;

	");

$installer->run("
 DROP TABLE IF EXISTS {$this->getTable('payperrentals/reservationprices')};

 CREATE TABLE {$this->getTable('payperrentals/reservationprices')} (
	`id` INT( 11 ) NOT NULL auto_increment,
	`entity_id` INT( 11 ) NOT NULL ,
	`store_id` INT( 11 ) NOT NULL DEFAULT '0',
	`numberof` INT( 11 ) NOT NULL DEFAULT '1',
	`ptype` TINYINT NOT NULL DEFAULT '1',
	`date_from` DATETIME,
	`date_to` DATETIME,
	`price` FLOAT NOT NULL ,
    `qty_start` INT( 11 ) NOT NULL DEFAULT '0',
    `qty_end` INT( 11 ) NOT NULL DEFAULT '0',
    `customers_group` INT( 11 ) NOT NULL DEFAULT '-1',
	PRIMARY KEY ( `id` ) ,
	INDEX ( `entity_id` , `store_id` , `date_from` , `date_to` , `price` , `customers_group` )
) DEFAULT CHARSET utf8 ENGINE = InnoDB;
");

$installer->run("
	DROP TABLE IF EXISTS {$this->getTable('payperrentals/reservationorders')};

	CREATE TABLE {$this->getTable('payperrentals/reservationorders')} (
		`id` int(11) NOT NULL auto_increment,
		`order_id` int(11) NOT NULL,
		`quote_id` int(11) NOT NULL,
		`product_id` int(11) NOT NULL,
		`qty` int(11) NOT NULL,
		`qty_cancel` int(11) NOT NULL DEFAULT '0',
		`order_item_id` int(11) NOT NULL DEFAULT '0',
		`start_date` datetime NOT NULL,
		`end_date` datetime NOT NULL,
		`otype` varchar(64) NOT NULL,
		`sendreturn_id` INT( 11 ) NOT NULL DEFAULT '0',
		`product_type` VARCHAR( 255 ) NOT NULL DEFAULT 'reservation',
		PRIMARY KEY ( `id` ) ,
        INDEX ( `order_id` , `quote_id` , `product_id` , `order_item_id` , `otype` , `sendreturn_id` )
	) DEFAULT CHARSET utf8 ENGINE = InnoDB;

	");

$installer->run("
	DROP TABLE IF EXISTS {$this->getTable('payperrentals/serialnumbers')};

	CREATE TABLE {$this->getTable('payperrentals/serialnumbers')} (
		`id` int(11) NOT NULL auto_increment,
		`entity_id` int(11) NOT NULL,
		`sn` varchar(250) NOT NULL,
		`status` ENUM('A','O','B', 'M') NOT NULL,
		PRIMARY KEY ( `id` ) ,
		INDEX ( `entity_id` , `status`)

	) DEFAULT CHARSET utf8 ENGINE = InnoDB;

	DROP TABLE IF EXISTS {$this->getTable('payperrentals/serialnumbersdetails')};

	CREATE TABLE {$this->getTable('payperrentals/serialnumbersdetails')} (
		`id` int(11) NOT NULL auto_increment,
		`entity_id` int(11) NOT NULL,
		`name` varchar(250) NOT NULL,
		`description` TEXT,
		`documents` TEXT,
		`cost` FLOAT,
		`date_added` DATETIME NOT NULL,
		`date_edited` DATETIME,
		PRIMARY KEY ( `id` ) ,
        INDEX ( `entity_id`)
	) DEFAULT CHARSET utf8 ENGINE = InnoDB;

	DROP TABLE IF EXISTS {$this->getTable('payperrentals/sendreturn')};

	CREATE TABLE {$this->getTable('payperrentals/sendreturn')} (
		`id` int(11) NOT NULL auto_increment,
		`order_id` int(11) NOT NULL,
		`product_id` int(11) NOT NULL,
		`res_startdate` DATETIME NOT NULL,
		`res_enddate` DATETIME NOT NULL,
		`send_date` DATETIME NOT NULL,
		`return_date` DATETIME NOT NULL,
		`qty` int(11) NOT NULL,
		`sn` TEXT,
		PRIMARY KEY ( `id` ) ,
		INDEX ( `order_id` , `product_id` , `res_startdate` , `res_enddate` , `send_date` , `return_date`)
	) DEFAULT CHARSET utf8 ENGINE = InnoDB;
	");
$installer->endSetup();

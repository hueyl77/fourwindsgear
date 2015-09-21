<?php
$installer = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();

$setup = $this;

$fieldList = array('is_reservation');
foreach ($fieldList as $field) {
   $installer->updateAttribute('catalog_product', $field, 'required', '1');
   $installer->updateAttribute('catalog_product', $field, 'default_value', null);
    $installer->updateAttribute('catalog_product', $field, 'class', 'required-entry select');
}

$configValuesMap = array(
    'payperrentals/notificationemails/send_queue_email' =>
    'payperrentals_notification_send_queue',
);

foreach ($configValuesMap as $configPath=>$configValue) {
    $installer->setConfigData($configPath, $configValue);
}
$installer->run("
	DROP TABLE IF EXISTS {$this->getTable('payperrentals/mailinglog')};

	CREATE TABLE {$this->getTable('payperrentals/mailinglog')} (
		`id` INT( 11 ) NOT NULL auto_increment,
		`is_cron` int(11) NOT NULL DEFAULT '0',
		`from_name` varchar(250) NOT NULL,
		`from_email` varchar(250) NOT NULL,
		`to_name` varchar(250) NOT NULL,
		`to_email` varchar(250) NOT NULL,
		`subject` TEXT,
		`message` TEXT,
		`message_error` TEXT,
		`created_at` DATETIME NOT NULL ,
		PRIMARY KEY ( `id` )

	) DEFAULT CHARSET utf8 ENGINE = InnoDB;

	");


$installer->endSetup();

$installer2 = new Mage_Sales_Model_Mysql4_Setup('sales_setup');
$installer2->startSetup();


$installer2->addAttribute('order', 'start_datetime', array(
	'type' => 'datetime',
	'grid' => true,
	'unsigned'  => true
));
$installer2->addAttribute('order', 'end_datetime', array(
	'type' => 'datetime',
	'grid' => true,
	'unsigned'  => true
));



$installer2->addAttribute('order', 'send_datetime', array(
    'type' => 'datetime',
    'grid' => true,
    'unsigned'  => true
));
$installer2->addAttribute('order', 'return_datetime', array(
    'type' => 'datetime',
    'grid' => true,
    'unsigned'  => true
));

$installer2->addAttribute('order', 'estimate_send', array(
    'type' => 'datetime',
    'grid' => true,
    'unsigned'  => true
));
$installer2->addAttribute('order', 'estimate_return', array(
    'type' => 'datetime',
    'grid' => true,
    'unsigned'  => true
));



$installer2->addAttribute('quote', 'start_datetime', array(
	'type' => 'datetime',
	'grid' => true,
	'unsigned'  => true
));
$installer2->addAttribute('quote', 'end_datetime', array(
	'type' => 'datetime',
	'grid' => true,
	'unsigned'  => true
));


$installer2->getConnection()->addColumn($installer2->getTable('sales/quote'), 'start_datetime', 'DATETIME');
$installer2->getConnection()->addColumn($installer2->getTable('sales/order'), 'end_datetime', 'DATETIME');

$installer2->getConnection()->addColumn($installer2->getTable('sales/quote'), 'end_datetime', 'DATETIME');
$installer2->getConnection()->addColumn($installer2->getTable('sales/order'), 'start_datetime', 'DATETIME');

$installer2->getConnection()->addColumn($installer2->getTable('sales/order'), 'send_datetime', 'DATETIME');
$installer2->getConnection()->addColumn($installer2->getTable('sales/order'), 'return_datetime', 'DATETIME');
$installer2->getConnection()->addColumn($installer2->getTable('sales/order'), 'estimate_send', 'DATETIME');
$installer2->getConnection()->addColumn($installer2->getTable('sales/order'), 'estimate_return', 'DATETIME');


$installer2->addAttribute('quote', 'send_datetime', array(
    'type' => 'datetime',
    'grid' => true,
    'unsigned'  => true
));
$installer2->addAttribute('quote', 'return_datetime', array(
    'type' => 'datetime',
    'grid' => true,
    'unsigned'  => true
));

$installer2->addAttribute('quote', 'estimate_send', array(
    'type' => 'datetime',
    'grid' => true,
    'unsigned'  => true
));
$installer2->addAttribute('quote', 'estimate_return', array(
    'type' => 'datetime',
    'grid' => true,
    'unsigned'  => true
));

$installer2->getConnection()->addColumn($installer2->getTable('sales/quote'), 'send_datetime', 'DATETIME');
$installer2->getConnection()->addColumn($installer2->getTable('sales/quote'), 'return_datetime', 'DATETIME');
$installer2->getConnection()->addColumn($installer2->getTable('sales/quote'), 'estimate_send', 'DATETIME');
$installer2->getConnection()->addColumn($installer2->getTable('sales/quote'), 'estimate_return', 'DATETIME');
$installer2->endSetup();
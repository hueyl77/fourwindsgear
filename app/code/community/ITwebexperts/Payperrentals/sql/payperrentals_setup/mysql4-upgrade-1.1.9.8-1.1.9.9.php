<?php

$installer = $this;
$installer->startSetup();

$installer->run("
	CREATE TABLE {$this->getTable('payperrentals/payment_transaction')} LIKE {$this->getTable('sales/payment_transaction')};
");

$installer->endSetup();
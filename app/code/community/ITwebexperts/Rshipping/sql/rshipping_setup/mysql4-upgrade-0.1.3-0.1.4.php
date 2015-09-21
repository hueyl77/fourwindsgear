<?php

$_installer = $this;
$_installer->startSetup();

$_installer->run("ALTER TABLE {$this->getTable('rshipping')} ADD `shipping_cutoff_time` varchar(255) NOT NULL default ''");

$_installer->endSetup();
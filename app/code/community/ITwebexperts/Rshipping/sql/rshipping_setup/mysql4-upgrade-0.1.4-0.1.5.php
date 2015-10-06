<?php

$_installer = $this;
$_installer->startSetup();

$_installer->run("ALTER TABLE {$this->getTable('rshipping')} ADD `use_live_ups_api` tinyint(1) NOT NULL default '0'");

$_installer->endSetup();
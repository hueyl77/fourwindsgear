<?php
$installer = $this;

$installer->startSetup();
	$installer->run("
	DROP TABLE IF EXISTS {$this->getTable('jmmegamenu_store_menugroup')};
	ALTER TABLE {$this->getTable('jmmegamenu_types')} DROP INDEX menutype;
	CREATE INDEX menutype ON {$this->getTable('jmmegamenu_types')} (menutype);
	");
$installer->endSetup();

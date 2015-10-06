<?php
/**
 * Snowcode
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Snowcode EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://snowcode.info/LICENSE-1.0.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://snowcode.info/ for more information
 *
 * Especially for Tagesjump
 *
 * @category   Snowcode
 * @package    Snowcode
 * @date       30.07.13
 * @copyright  Copyright (c) 2013 Snowcode (http://snowcode.info/)
 * @license    http://snowcode.info/LICENSE-1.0.html
 */

$_installer = $this;
$_installer->startSetup();

$_installer->run("ALTER TABLE {$this->getTable('rshipping')} ADD `start_disabled_days` varchar(255) NOT NULL default ''");
$_installer->run("ALTER TABLE {$this->getTable('rshipping')} ADD `end_disabled_days` varchar(255) NOT NULL default ''");

$_installer->endSetup();
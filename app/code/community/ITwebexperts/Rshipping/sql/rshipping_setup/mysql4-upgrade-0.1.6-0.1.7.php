<?php
/**
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
 *
 * @category   Snowcode
 * @package    Snowcode
 * @date       12.08.13
 * @copyright  Copyright (c) 2013 Snowcode (http://snowcode.info/)
 * @license    http://snowcode.info/LICENSE-1.0.html
 */

$_installer = $this;
$_installer->startSetup();

$_installer->run("ALTER TABLE {$this->getTable('rshipping')} ADD `is_local_pickup` tinyint(1) NOT NULL default '0'");
$_installer->run("ALTER TABLE {$this->getTable('rshipping')} ADD `is_default_method` tinyint(1) NOT NULL default '0'");

$_installer->endSetup();
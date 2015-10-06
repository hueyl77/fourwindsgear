<?php

$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('rshipping')};
CREATE TABLE {$this->getTable('rshipping')} (
  `rshipping_id` int(10) unsigned NOT NULL auto_increment,
  `shipping_title` varchar(255) NOT NULL default '',
  `shipping_method` varchar(255) NOT NULL default '',
  `turnover_before_period` varchar(255) NOT NULL default '',
  `turnover_before_type` int(11) NOT NULL default '3',
  `turnover_after_period` varchar(255) NOT NULL default '',
  `turnover_after_type` int(11) NOT NULL default '3',
  `status` smallint(6) NOT NULL default '0',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`rshipping_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS {$this->getTable('rshipping_products')};
CREATE TABLE IF NOT EXISTS {$this->getTable('rshipping_products')}  (
  `link_id` int(10) NOT NULL AUTO_INCREMENT,
  `rshipping_id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
   PRIMARY KEY (`link_id`),
   UNIQUE KEY `VIDEO_PRODUCT_UNIQUE_KEY` (`rshipping_id`,`product_id`),
  KEY `FK_PRODUCT_ID` (`product_id`),
  KEY `FK_SHIPPING_ID` (`rshipping_id`),
  CONSTRAINT `FK_VIDEO_ID` FOREIGN KEY (`rshipping_id`) REFERENCES `rshipping` (`rshipping_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_PRODUCT_ID` FOREIGN KEY (`product_id`) REFERENCES `catalog_product_entity` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



");

$installer->endSetup();

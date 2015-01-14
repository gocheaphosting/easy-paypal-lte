CREATE TABLE IF NOT EXISTS `{prefix}administrator` (
  `id` int(2) unsigned NOT NULL AUTO_INCREMENT,
  `created` TIMESTAMP DEFAULT NOW(),
  `username` varchar(255) NOT NULL,
  `password` varchar(32) NOT NULL,
  `email` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `{prefix}options_meta` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `created` TIMESTAMP DEFAULT NOW(),
  `name` varchar(128) NOT NULL UNIQUE,
  `value` text NOT NULL,
  `desc` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `{prefix}templates` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `created` timestamp NOT NULL default NOW(),
  `active` tinyint(1) NOT NULL default '1',
  `category_id` int(4) NOT NULL,
  `product_grouping` varchar(128) NOT NULL,
  `name` varchar(128) default NULL,
  `value` longtext,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `category_id` (`category_id`,`product_grouping`,`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `{prefix}products` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `created` timestamp NOT NULL default NOW(),
  `active` tinyint(1) NOT NULL default '1',
  `category_id` int(4) NOT NULL,
  `expire_hours` int(3) NOT NULL,
  `product_name` varchar(128) NOT NULL,
  `product_code` varchar(128) NOT NULL,
  `product_grouping` varchar(128) NOT NULL,
  `product_price` decimal(6,2) NOT NULL,
  `no_shipping` smallint(6) NOT NULL default '0',
  `mc_currency` varchar(3) default NULL,
  `version` decimal(6,2) NOT NULL,
  `filename` varchar(128) NOT NULL,
  `file` varchar(256) NOT NULL,
  `recurring` tinyint(1) default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `product_code` (`product_code`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `{prefix}product_meta` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `created` timestamp NOT NULL default NOW(),
  `product_id` int(10) NOT NULL,
  `name` varchar(128) default NULL,
  `value` longtext,
  `comment` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `product_id` (`product_id`,`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `{prefix}categories` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `created` TIMESTAMP DEFAULT NOW(),
  `name` varchar(32) NOT NULL UNIQUE,
  `comment` varchar(255) NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

INSERT IGNORE INTO `{prefix}categories` (`id`, `name`, `comment`, `active`)
VALUES
	(1,'category-1','Default category 1',1),
	(2,'category-2','Default category 2',1),
	(3,'category-3','Default category 3',1);

CREATE TABLE IF NOT EXISTS `{prefix}sales` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `created` timestamp NOT NULL default NOW(),
  `product_id` int(10) NOT NULL,
  `txn_id` varchar(20) NOT NULL,
  `customer_name` varchar(128) NOT NULL,
  `customer_email` varchar(128) NOT NULL,
  `business_name` varchar(128) NOT NULL,
  `purchase_amount` decimal(6,2) NOT NULL,
  `purchase_status` varchar(16) NOT NULL,
  `purchase_mode` varchar(16) default NULL,
  `purchase_date` datetime NOT NULL,
  `expire_hours` int(3) NOT NULL,
  `expire_date` datetime NOT NULL,
  `product_name` varchar(128) NOT NULL,
  `product_code` varchar(128) NOT NULL,
  `quantity` int(10) unsigned default '1',
  `sold_version` varchar(32) default NULL,
  `updated_version` varchar(32) default NULL,
  `affiliate_id` varchar(32) default NULL,
  `subscr_id` varchar(32) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `txn_id` (`txn_id`,`purchase_status`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `{prefix}sale_details` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `created` timestamp NOT NULL default NOW(),
  `sale_id` int(11) NOT NULL,
  `business` varchar(128) default NULL,
  `charset` varchar(32) default NULL,
  `custom` varchar(255) default NULL,
  `first_name` varchar(64) default NULL,
  `handling_amount` decimal(6,2) default NULL,
  `ipn_track_id` varchar(32) default NULL,
  `item_name` varchar(128) NOT NULL,
  `item_number` varchar(128) NOT NULL,
  `last_name` varchar(64) NOT NULL,
  `mc_currency` varchar(3) default NULL,
  `mc_fee` decimal(10,2) default NULL,
  `mc_gross` decimal(10,2) default NULL,
  `notify_version` varchar(32) default NULL,
  `parent_txn_id` varchar(20) NOT NULL,
  `payer_email` varchar(128) NOT NULL,
  `payer_business_name` varchar(128) NOT NULL,
  `payer_id` varchar(20) default NULL,
  `payer_status` varchar(20) default NULL,
  `payment_date` datetime NOT NULL,
  `payment_fee` decimal(6,2) default NULL,
  `payment_gross` decimal(6,2) NOT NULL,
  `payment_status` varchar(32) NOT NULL,
  `payment_type` varchar(128) default NULL,
  `protection_eligibility` varchar(128) default NULL,
  `quantity` int(10) unsigned default '1',
  `receiver_email` varchar(128) NOT NULL,
  `receiver_id` varchar(20) default NULL,
  `residence_country` varchar(2) default NULL,
  `shipping` decimal(6,2) default NULL,
  `tax` varchar(20) default NULL,
  `transaction_subject` varchar(128) default NULL,
  `txn_id` varchar(20) NOT NULL,
  `txn_type` varchar(48) default NULL,
  `verify_sign` varchar(128) default NULL,
  `dbStatus` varchar(128) default NULL,
  `subscr_date` datetime default NULL,
  `subscr_effective` datetime default NULL,
  `period1` varchar(32) default NULL,
  `period2` varchar(32) default NULL,
  `period3` varchar(32) default NULL,
  `amount1` decimal(10,2) default NULL,
  `amount2` decimal(10,2) default NULL,
  `amount3` decimal(10,2) default NULL,
  `mc_amount1` decimal(10,2) default NULL,
  `mc_amount2` decimal(10,2) default NULL,
  `mc_amount3` decimal(10,2) default NULL,
  `recurring` tinyint(1) default '0',
  `reattempt` tinyint(1) default '0',
  `retry_at` datetime default NULL,
  `recur_times` decimal(6,2) default NULL,
  `username` varchar(128) default NULL,
  `password` varchar(128) default NULL,
  `subscr_id` varchar(128) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `txn_id` (`txn_id`,`txn_type`,`payment_status`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

FLUSH TABLES

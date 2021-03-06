CREATE TABLE `store_products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `score` int(11) NOT NULL DEFAULT '0',
  `product_id` int(11) NOT NULL DEFAULT '0',
  `product_status` tinyint(4) NOT NULL DEFAULT '0',
  `product_name` varchar(255) NOT NULL DEFAULT '',
  `product_description` text NOT NULL,
  `product_size` varchar(50) NOT NULL DEFAULT '',
  `product_quantity` int(11) NOT NULL DEFAULT '0',
  `product_price` double NOT NULL DEFAULT '0',
  `product_shipping` double NOT NULL DEFAULT '0',
  `product_pinterest_pin_id` int(11) NOT NULL DEFAULT '0',
  `product_ext_ref_id` varchar(50) NOT NULL DEFAULT '',
  `product_ext_ref_url` varchar(255) NOT NULL DEFAULT '',
  `product_brand` varchar(255) NOT NULL DEFAULT '',
  `product_misc` text NOT NULL,
  `product_start_date` datetime DEFAULT '0000-00-00 00:00:00',
  `product_end_date` datetime DEFAULT '0000-00-00 00:00:00',
  `category` varchar(50) NOT NULL DEFAULT '',
  `pic_ids` varchar(255) NOT NULL DEFAULT '',
  `pic_types` varchar(255) NOT NULL DEFAULT '',
  `pic_sources` varchar(255) NOT NULL DEFAULT '',
  `pic_urls` text NOT NULL,
  `store_id` int(11) NOT NULL DEFAULT '0',
  `store_status` tinyint(4) NOT NULL DEFAULT '0',
  `store_subdomain` varchar(50) NOT NULL DEFAULT '',
  `store_name` varchar(50) NOT NULL DEFAULT '',
  `store_host` varchar(50) NOT NULL DEFAULT '',
  `store_featured` tinyint(4) NOT NULL DEFAULT '0',
  `store_logo` varchar(255) NOT NULL DEFAULT '',
  `store_tax` double NOT NULL DEFAULT '0',
  `store_shipping` double NOT NULL DEFAULT '0',
  `store_additional_shipping` double NOT NULL DEFAULT '0',
  `created` datetime DEFAULT '0000-00-00 00:00:00',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `store_product_id` (`store_id`,`product_id`),
  KEY `product_pinterest_pin_id` (`product_pinterest_pin_id`),
  KEY `product_ext_ref_id` (`product_ext_ref_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

update version set version=3;
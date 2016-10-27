<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for ...
 **/
class Migration20161027171135ComLabs extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		foreach([
			"DROP TABLE IF EXISTS `jos_lab_attribute_release`",
			"CREATE TABLE `jos_lab_attribute_release` (
			  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			  `lab_id` int(11) NOT NULL,
			  `attribute` varchar(50) NOT NULL,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `id` (`id`)
			) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1",
			"INSERT INTO `jos_lab_attribute_release` VALUES (3,4,'firstName'),(4,4,'lastName'),(5,4,'email'),(6,4,'username')",

			"DROP TABLE IF EXISTS `jos_labs`",
			"CREATE TABLE `jos_labs` (
			  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			  `name` varchar(100) NOT NULL,
			  `title` varchar(100) NOT NULL,
			  `description` text,
			  `published` tinyint(4) NOT NULL DEFAULT '0',
			  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			  `owner_uid` int(11) DEFAULT NULL,
			  `owner_group` int(11) DEFAULT NULL,
			  `login_scope` varchar(50) DEFAULT NULL,
			  `login_id` int(11) DEFAULT NULL,
			  `entrance` varchar(50) DEFAULT NULL,
			  `access_group` int(11) DEFAULT NULL,
			  `add_group` int(11) DEFAULT NULL,
			  `transport_key` varchar(200) DEFAULT NULL,
			  `view` varchar(50) NOT NULL DEFAULT 'app',
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `id` (`id`)
			) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1",
			"INSERT INTO `jos_labs` VALUES (4,'ojs','Open Conference System',NULL,1,'2016-10-19 14:48:08',NULL,NULL,NULL,NULL,'/',NULL,(SELECT gidNumber FROM jos_xgroups WHERE cn = 'ocstest'),'ntvkw9Bw5cuN5tF7Tzf47HrOdLPNThNhS7VqKeyeN2J/Jnrwj2ow/mIwqXs8PrB2uZsCurGCIVcO9rSah7zRhQ','fullscreen')",

			"DROP TABLE IF EXISTS `jos_labs_proxy`",
			"CREATE TABLE `jos_labs_proxy` (
			  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			  `published` tinyint(4) NOT NULL DEFAULT '0',
			  `host` varchar(50) NOT NULL,
			  `shared_key` varchar(100) DEFAULT NULL,
			  `ordering` int(11) DEFAULT NULL,
			  `forward` varchar(100) NOT NULL,
			  `lab_id` int(11) NOT NULL,
			  `component` varchar(20) NOT NULL DEFAULT 'com_labs',
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `id` (`id`)
			) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1",
			"INSERT INTO `jos_labs_proxy` VALUES (3,1,'ojs.labs.cdmhub.aws.hubzero.org','ep3/KSaRrQhupUauJnO18n5yeNTXz7HwJnJ7Uw1IM5VnYKx9tB',NULL,'https://lab1.cdmhub.aws.hubzero.org',4,'com_labs')"
		] as $stmt) {
			$this->db->setQuery($stmt);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{

	}
}

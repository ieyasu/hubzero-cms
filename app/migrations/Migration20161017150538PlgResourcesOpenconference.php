<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing the Resources openconference plugin
 **/
class Migration20161017150538PlgResourcesOpenconference extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('resources', 'openconference');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('resources', 'openconference');
	}
}

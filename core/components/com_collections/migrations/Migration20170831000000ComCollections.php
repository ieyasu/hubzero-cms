<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding component entry for com_collections
 **/
class Migration20170831000000ComCollections extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addComponentEntry('collections');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteComponentEntry('collections');
	}
}

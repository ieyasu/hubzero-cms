<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

require_once Component::path('com_publications') . '/models/publication.php';
require_once Component::path('com_publications') . '/models/orm/version.php';
use Components\Publications\Models\Orm\Version;
use Components\Publications\Models\Publication;

/**
 * Migration script for creating sym links in the SFTP directory
 **/
class Migration20180820101435ComPublications extends Base
{
	/**
	 * Number of database rows to process at a time
	 *
	 * @var  integer
	 */
	public $limit = 1000;

	/**
	 * Up
	 **/
	public function up()
	{
		$versionCount = Version::all()->whereEquals('state', 1)->total();
		$offset = 0;
		while ($offset < $versionCount)
		{
			$publications = Version::all()
				->whereEquals('state', 1)
				->limit($this->limit)
				->start($offset)
				->rows();

			foreach ($publications as $publication)
			{
				$pubId = $publication->get('publication_id');
				$versionId = $publication->get('id');

				$pubModel = new Publication($pubId, null, $versionId);
				$pubModel->setCuration();
				$pubModel->_curationModel->removeSymLink();
			}

			$offset += $this->limit;
		}

		if (is_dir(PATH_APP . '/site/publications/ftp'))
		{
			$versionCount = Version::all()->whereEquals('state', 1)->total();
			$offset = 0;
			while ($offset < $versionCount)
			{
				$publications = Version::all()
					->whereEquals('state', 1)
					->limit($this->limit)
					->start($offset)
					->rows();

				foreach ($publications as $publication)
				{
					$pubId = $publication->get('publication_id');
					$versionId = $publication->get('id');

					$pubModel = new Publication($pubId, null, $versionId);
					$pubModel->setCuration();
					$pubModel->_curationModel->createSymLink();
				}

				$offset += $this->limit;
			}
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if (is_dir(PATH_APP . '/site/publications/ftp'))
		{
			$versionCount = Version::all()->whereEquals('state', 1)->total();
			$offset = 0;
			while ($offset < $versionCount)
			{
				$publications = Version::all()
					->whereEquals('state', 1)
					->limit($this->limit)
					->start($offset)
					->rows();

				foreach ($publications as $publication)
				{
					$pubId = $publication->get('publication_id');
					$versionId = $publication->get('id');

					$pubModel = new Publication($pubId, null, $versionId);
					$pubModel->setCuration();
					$pubModel->_curationModel->removeSymLink();
				}

				$offset += $this->limit;
			}
		}

	}
}

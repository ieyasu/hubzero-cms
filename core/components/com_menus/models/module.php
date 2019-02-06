<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Menus\Models;

use Hubzero\Database\Relational;
use Hubzero\Config\Registry;
use App;

/**
 * Module extension model
 */
class Module extends Relational
{
	/**
	 * Menu module name
	 **/
	const MODULE_NAME = 'mod_menu';

	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = '';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'ordering';

	/**
	 * Default order direction for select queries
	 *
	 * @var  string
	 */
	public $orderDir = 'asc';

	/**
	 * Configuration registry
	 *
	 * @var  object
	 */
	protected $paramsRegistry = null;

	/**
	 * XML manifest
	 *
	 * @var  object
	 */
	protected $manifest = null;

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'title'    => 'notempty',
		'position' => 'notempty'
	);

	/**
	 * Get params as a Registry object
	 *
	 * @return  object
	 */
	public function transformParams()
	{
		if (!($this->paramsRegistry instanceof Registry))
		{
			$this->paramsRegistry = new Registry($this->get('params'));
		}
		return $this->paramsRegistry;
	}

	/**
	 * Delete the record and all associated data
	 *
	 * @return  boolean  False if error, True on success
	 */
	public function destroy()
	{
		$pk = $this->get('id');

		$result = parent::destroy();

		// Attempt to delete the record
		if ($result)
		{
			// Delete the menu assignments
			$db = App::get('db');
			$query = $db->getQuery()
				->delete('#__modules_menu')
				->whereEquals('moduleid', (int)$pk);
			$db->setQuery((string)$query);
			$result = $db->query();
		}

		return $result;
	}

	/**
	 * Gets the extension id of the core mod_menu module.
	 *
	 * @return  integer
	 */
	public static function getModMenuId()
	{
		$db = App::get('db');

		$query = $db->getQuery();

		$query->select('e.extension_id')
			->from('#__extensions', 'e')
			->whereEquals('e.type', 'module')
			->whereEquals('e.element', self::MODULE_NAME)
			->whereEquals('e.client_id', '0');
		$db->setQuery($query->toString());

		return $db->loadResult();
	}

	/**
	 * Get the list of modules not in trash.
	 *
	 * @param   integer  $pk
	 * @return  array    An array of module records (id, title, position)
	 */
	public static function getModules($pk = 0)
	{
		$db = App::get('db');

		$query = $db->getQuery()
			->select('a.id')
			->select('a.title')
			->select('a.position')
			->select('a.published')
			->select('map.menuid')
			->from(self::blank()->getTableName(), 'a');

		$query->joinRaw('#__modules_menu AS map', sprintf('map.moduleid = a.id AND map.menuid IN (0, %1$d, -%1$d)', $pk), 'left');
		$query->select('(SELECT COUNT(*) FROM #__modules_menu WHERE moduleid = a.id AND menuid < 0)', 'except');

		// Join on the asset groups table.
		$query->select('ag.title', 'access_title')
			->join('#__viewlevels AS ag', 'ag.id', 'a.access', 'left')
			->where('a.published', '>=', 0)
			->whereEquals('a.client_id', 0)
			->order('a.position', 'asc')
			->order('a.ordering', 'asc');

		$db->setQuery($query->toString());
		$result = $db->loadObjectList();

		return $result;
	}
}
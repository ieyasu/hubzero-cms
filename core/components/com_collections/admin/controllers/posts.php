<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Collections\Admin\Controllers;

use Components\Collections\Models\Orm\Item;
use Components\Collections\Models\Orm\Post;
use Hubzero\Component\AdminController;
use Request;
use Notify;
use Route;
use User;
use Lang;
use App;

/**
 * Controller class for collection posts
 */
class Posts extends AdminController
{
	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->registerTask('add', 'edit');
		$this->registerTask('apply', 'save');

		parent::execute();
	}

	/**
	 * Display a list of all categories
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Get filters
		$filters = array(
			'collection_id' => Request::getState(
				$this->_option . '.' . $this->_controller . '.collection_id',
				'collection_id',
				0,
				'int'
			),
			'item_id' => Request::getState(
				$this->_option . '.' . $this->_controller . '.item_id',
				'item_id',
				0,
				'int'
			),
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'created'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'DESC'
			),
			'search' => urldecode(Request::getState(
				$this->_option . '.' . $this->_controller . '.search',
				'search',
				''
			)),
			'state' => Request::getState(
				$this->_option . '.' . $this->_controller . '.state',
				'state',
				'-1'
			),
			'access' => Request::getState(
				$this->_option . '.' . $this->_controller . '.access',
				'access',
				'-1'
			)
		);

		$model = Post::all()
			->including(['creator', function ($creator){
				$creator->select('*');
			}]);

		$p = $model->getTableName();

		$model->select($p . '.*');

		if ($filters['search'])
		{
			$i = Item::blank()->getTableName();

			$model->join($i, $i . '.id', $p . '.item_id', 'left');

			$model->whereLike($p . '.description', strtolower((string)$filters['search']), 1)
				->orWhereLike($i . '.title', strtolower((string)$filters['search']), 1)
				->resetDepth();
		}

		if ($filters['state'] >= 0)
		{
			$model->whereEquals($p . '.state', $filters['state']);
		}

		if ($filters['access'] >= 0)
		{
			$model->whereEquals($p . '.access', (int)$filters['access']);
		}

		if ($filters['collection_id'])
		{
			$model->whereEquals($p . '.collection_id', $filters['collection_id']);
		}

		if ($filters['item_id'])
		{
			$model->whereEquals($p . '.item_id', $filters['item_id']);
		}

		// Get records
		$rows = $model
			->ordered('filter_order', 'filter_order_Dir')
			->paginated('limitstart', 'limit')
			->rows();

		if (Request::getCmd('tmpl') == 'component')
		{
			$this->view->setLayout('display_alt');
		}

		// Output the HTML
		$this->view
			->set('filters', $filters)
			->set('rows', $rows)
			->display();
	}

	/**
	 * Edit a post
	 *
	 * @return  void
	 */
	public function editTask($row=null)
	{
		Request::setVar('hidemainmenu', 1);

		if (!User::authorise('core.edit', $this->_option)
		 && !User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		if (!is_object($row))
		{
			// Incoming
			$id = Request::getArray('id', array(0));

			if (is_array($id))
			{
				$id = (!empty($id) ? $id[0] : 0);
			}

			// Load record
			$row = Post::oneOrNew($id);
		}

		$this->view
			->set('row', $row)
			->setLayout('edit')
			->display();
	}

	/**
	 * Save an entry
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		if (!User::authorise('core.edit', $this->_option)
		 && !User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming
		$fields = Request::getArray('fields', array(), 'post', 'none', 2);

		// Initiate model
		$row = Post::oneOrNew($fields['id'])->set($fields);

		// Store new content
		if (!$row->save())
		{
			Notify::error($row->getError());
			return $this->editTask($row);
		}

		// Process tags
		//$row->tag(trim(Request::getString('tags', '')));

		Notify::success(Lang::txt('COM_COLLECTIONS_POST_SAVED'));

		if ($this->getTask() == 'apply')
		{
			return $this->editTask($row);
		}

		// Set the redirect
		$this->cancelTask();
	}

	/**
	 * Delete one or more entries
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		if (!User::authorise('core.delete', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming
		$ids = Request::getArray('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);
		$i = 0;

		if (count($ids) > 0)
		{
			// Loop through all the IDs
			foreach ($ids as $id)
			{
				$entry = Post::oneOrFail(intval($id));

				// Delete the entry
				if (!$entry->destroy())
				{
					Notify::error($entry->getError());
					continue;
				}

				$i++;
			}
		}

		if ($i)
		{
			Notify::success(Lang::txt('COM_COLLECTIONS_ITEMS_DELETED'));
		}

		// Set the redirect
		$this->cancelTask();
	}

	/**
	 * Cancel a task
	 *
	 * @return  void
	 */
	public function cancelTask()
	{
		$item_id = Request::getInt('item_id');
		$item_id = ($item_id ? '&item_id=' . $item_id : '');

		$collection_id = Request::getInt('collection_id');
		$collection_id = ($collection_id ? '&collection_id=' . $collection_id : '');

		$tmpl = Request::getCmd('tmpl');
		$tmpl = ($tmpl ? '&tmpl=' . $tmpl : '');

		// Set the redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . $item_id . $collection_id . $tmpl, false)
		);
	}
}

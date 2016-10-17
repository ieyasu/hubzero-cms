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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Resources plugin for Open Conference
 */
class plgResourcesOpenconference extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Return plugin name if this plugin has an admin interface
	 *
	 * @return  string
	 */
	public function onCanManage()
	{
		return $this->_name;
	}

	/**
	 * Determine task and execute it
	 *
	 * @param   string  $option      Component name
	 * @param   string  $controller  Controller name
	 * @param   string  $task        Task to perform
	 * @return  void
	 */
	public function onManage($option, $controller='plugins', $task='default')
	{
		if (Request::getCmd('plugin') != $this->_name)
		{
			return;
		}

		$task = ($task) ?  $task : 'default';

		$this->_option     = $option;
		$this->_controller = $controller;
		$this->_task       = $task;
		$this->database    = App::get('db');

		$method = strtolower($task) . 'Task';

		return $this->$method();
	}

	/**
	 * Default
	 *
	 * @return  void
	 */
	public function defaultTask()
	{
		// Instantiate a view
		$view = $this->view('default', 'admin')
			->set('option', $this->_option)
			->set('controller', $this->_controller)
			->set('task', $this->_task);

		return $view
			->setErrors($this->getErrors())
			->loadTemplate();
	}

	/**
	 * Save
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=manage&plugin=' . $this->_name, false),
			Lang::txt('PLG_RESOURCES_OPENCONFERENCE_ITEM_SAVED')
		);
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return  void
	 */
	public function cancelTask()
	{
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=manage&plugin=' . $this->_name, false)
		);
	}
}

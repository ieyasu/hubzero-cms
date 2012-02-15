<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');
JPlugin::loadLanguage('plg_members_dashboard');

class plgMembersDashboard extends JPlugin
{
	public function plgMembersDashboard(&$subject, $config)
	{
		parent::__construct($subject, $config);
		
		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin('members', 'dashboard');
		$this->_params = new JParameter($this->_plugin->params);
	}
	
	//-----------
	
	public function onMembersAreas( $user, $member )
	{	
		//default areas returned to nothing
		$areas = array();
		
		//if this is the logged in user show them
		if($user->get("id") == $member->get("uidNumber"))
		{
			$areas['dashboard'] = JText::_('PLG_MEMBERS_DASHBOARD');
		}

		return $areas;
	}

	//-----------

	public function onMembers($user, $member, $option, $areas)
	{
		$returnhtml = true;
		$returnmeta = true;
		
		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas)) 
		{
			if (!array_intersect($areas, $this->onMembersAreas($user, $member)) 
			 && !array_intersect($areas, array_keys($this->onMembersAreas($user, $member)))) 
			{
				$returnhtml = false;
			}
		}
		
		/*
		if (!$authorized) 
		{
			$returnhtml = false;
			$returnmeta = false;
		}
		*/
		
		$arr = array(
			'html' => '',
			'metadata' => ''
		);

		// Build the final HTML
		if ($returnhtml) 
		{
			include_once(JPATH_ROOT . DS . 'plugins' . DS . 'members' . DS . 'dashboard' . DS . 'tables' . DS . 'params.php');
			include_once(JPATH_ROOT . DS . 'plugins' . DS . 'members' . DS . 'dashboard' . DS . 'tables' . DS . 'prefs.php');
			
			$this->_act = JRequest::getVar('act', 'customize');
			
			ximport('Hubzero_Document');
			Hubzero_Document::addPluginStylesheet('members', 'dashboard');
			
			ximport('Hubzero_Plugin_View');
			$this->view = new Hubzero_Plugin_View(
				array(
					'folder'  => 'members',
					'element' => 'dashboard',
					'name'    => 'display'
				)
			);
			//$this->view->authorized = $authorized;
			$this->view->member = $this->member = $member;
			//$this->view->option = $option;
			$this->view->act = $this->_act;
			$this->view->config = $this->_params;
			
			$this->juser = $this->view->juser = JFactory::getUser();
			$this->database = $this->view->database = JFactory::getDBO();
			$this->option = $this->view->option = $option;
			
			$action = JRequest::getVar('action', '');
			switch ($action)
			{
				case 'refresh':    $this->refresh();    break;
				case 'rebuild':    $this->rebuild();    break;
				case 'restore':    $this->restore();    break;
				case 'save':       $this->save();       break;
				case 'saveparams': $this->saveparams(); break;
				case 'addmodule':  $this->addmodule();  break;

				default: $this->dashboard(); break;
			}
			
			if ($this->getError()) 
			{
				$this->view->setError($this->getError());
			}

			$arr['html'] = $this->view->loadTemplate();
		}

		// Build the HTML meant for the "profile" tab's metadata overview
		if ($returnmeta) 
		{
			//build the metadata
		}

		return $arr;
	}
	
	public function dashboard()
	{
		if ($this->_params->get('allow_customization', 0) != 1) 
		{
			$document =& JFactory::getDocument();
			$document->addScript(DS . 'plugins' . DS . 'members' . DS . 'dashboard' . DS . 'xsortables.js');
			$document->addScript(DS . 'plugins' . DS . 'members' . DS . 'dashboard' . DS . 'dashboard.js');
			//$doc->addScript('https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js');
			//$doc->addScriptDeclaration("\tvar j = jQuery.noConflict();");
		}
		
		$this->num_default = 6;
		
		$myhub = new MyhubPrefs($this->database);
		$myhub->load($this->juser->get('id'));

		// No preferences found
		if (trim($myhub->prefs) == '') 
		{
			// Create a default set of preferences
			$myhub->uid = $this->juser->get('id');
			$myhub->prefs = $this->_getDefaultModules();
			if ($this->_params->get('allow_customization', 0) != 1) 
			{
				if (!$myhub->check()) 
				{
					$this->setError($myhub->getError());
				}
				if (!$myhub->create()) 
				{
					$this->setError($myhub->getError());
				}
			}
		}

		// Splits string into columns
		$mymods = explode(';', $myhub->prefs);
		// Save array of columns for later work
		$usermods = $mymods;

		// Splits each column into modules, listed by the order they should appear
		for ($i = 0; $i < count($mymods); $i++)
		{
			if (!trim($mymods[$i])) {
				continue;
			}
			$mymods[$i] = explode(',', $mymods[$i]);
		}

		// Build a list of all modules being used by this user 
		// so we know what to exclude from the list of modules they can still add
		$allmods = array();
		for ($i = 0; $i < count($mymods); $i++)
		{
			$allmods = array_merge($allmods, $mymods[$i]);
		}

		// The number of columns
		$cols = 3;

		// Instantiate a view
		$this->view->usermods = $usermods;
		//$this->view->juser = $this->juser;

		if ($this->_params->get('allow_customization', 0) != 1) 
		{
			$this->view->availmods = $this->_getUnusedModules($allmods);
		} else {
			$this->view->availmods = null;
		}

		$this->view->columns = array();
		for ($c = 0; $c < $cols; $c++)
		{
			$this->view->columns[$c] = $this->output_modules($mymods[$c], $this->juser->get('id'), $this->_act);
		}
	}
	
	/**
	 * Outputs a group of modules, each contained in a list item
	 * 
	 * @param      array $mods Parameter description (if any) ...
	 * @param      unknown $uid Parameter description (if any) ...
	 * @param      string $act Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	protected function output_modules($mods, $uid, $act='')
	{
		// Get the modules
		$modules = array();
		for ($i=0, $n=count($mods); $i < $n; $i++)
		{
			$myparams = new MyhubParams($this->database);
			$modules[] = $myparams->loadModule($uid, $mods[$i]);
		}

		$html = '';

		// Loop through the modules and output
		foreach ($modules as $module)
		{
			if (isset($module->published)) 
			{
				if ($module->published != 1) 
				{
					continue;
				}

				$rendered = false;
				// if the user has special prefs, load them. Otherwise, load default prefs
				if ($module->myparams != '') 
				{
					$params = new JParameter($module->myparams);
					$module->params .= $module->myparams;
				} 
				else 
				{
					$params = new JParameter($module->params);
				}

				if ($params) 
				{
					$rendered = false; //$this->render($params, $mainframe->getPath('mod0_xml', $module->module), 'module');
				}

				// Instantiate a view
				$view = new Hubzero_Plugin_View(
					array(
						'folder'  => 'members',
						'element' => 'dashboard',
						'name'    => 'module'
					)
				);
				$view->module = $module;
				$view->params = $params;
				$view->container = true;
				$view->extras = true;
				$view->database = $this->database;
				$view->option = $this->option;
				$view->act = $act;
				$view->config = $this->_params;
				$view->rendered = $rendered;
				$view->juser = $this->juser;
				
				$html .= $view->loadTemplate();
			}
		}

		return $html;
	}
	
	/**
	 * Rebuild the "available modules" list
	 * 
	 * @return     void
	 */
	protected function rebuild()
	{
		$id  = $this->save(1);

		$ids = explode(';',$id);
		for ($i = 0; $i < count($ids); $i++)
		{
			if (!trim($ids[$i])) {
				continue;
			}
			$ids[$i] = explode(',', $ids[$i]);
		}

		$allmods = array();
		for ($i = 0; $i < count($ids); $i++)
		{
			$allmods = array_merge($allmods, $ids[$i]);
		}

		// Instantiate a view
		//$this->view->_name = 'list';
		$this->view = new Hubzero_Plugin_View(
			array(
				'folder'  => 'members',
				'element' => 'dashboard',
				'name'    => 'list',
			)
		);
		$this->view->modules = $this->_getUnusedModules($allmods);
	}
	
	/**
	 * Save preferences (i.e., the list of modules to be displayed and their locations)
	 * 
	 * @param      integer $rtrn Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	protected function save($rtrn=0)
	{
		$this->view = new Hubzero_Plugin_View(
			array(
				'folder'  => 'members',
				'element' => 'dashboard',
				'name'    => 'data'
			)
		);
		//$this->view->_name = 'data';

		// Incoming
		$uid = $this->member->get('uidNumber'); //JRequest::getInt('id', 0);
		$ids = JRequest::getVar('mids', '');

		// Ensure we have a user ID ($uid)
		if (!$uid) 
		{
			if ($rtrn) 
			{
				return $ids;
			}
		}

		// Instantiate object, bind data, and save
		$myhub = new MyhubPrefs($this->database);
		$myhub->load($uid);
		$myhub->prefs = $ids;
		$myhub->modified = date("Y-m-d H:i:s");
		if (!$myhub->check()) 
		{
			$this->setError($myhub->getError());
		}
		if (!$myhub->store()) 
		{
			$this->setError($myhub->getError());
		}
		$this->view->data = $ids;
		if ($rtrn) 
		{
			return $ids;
		}
	}
	
	/**
	 * Save the parameters for a module
	 * 
	 * @return     void
	 */
	protected function saveparams()
	{
		// Incoming
		$uid = $this->member->get('uidNumber'); //JRequest::getInt('id', 0);
		$mid = JRequest::getVar('mid', '');
		$update = JRequest::getInt('update', 0);
		$params = JRequest::getVar('params', array());

		// Process parameters
		$newparams = array();
		foreach ($params as $aKey => $aValue)
		{
			$newparams[] = $aKey.'='.$aValue;
		}

		// Instantiate object, bind data, and save
		$myparams = new MyhubParams($this->database);
		$myparams->loadParams($uid, $mid);
		if (!$myparams->params) 
		{
			$myparams->uid = $uid;
			$myparams->mid = $mid;
			$new = true;
		} 
		else 
		{
			$new = false;
		}
		$myparams->params = implode($newparams, "\n");
		if (!$myparams->check()) 
		{
			$this->setError($myparams->getError());
		}
		if (!$myparams->storeParams($new)) 
		{
			$this->setError($myparams->getError());
		}

		if ($update) 
		{
			$this->getmodule();
		}
	}
	
	/**
	 * Short description for 'getmodule'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      boolean $extras Parameter description (if any) ...
	 * @param      string $act Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	protected function getmodule($extras=false, $act='')
	{
		// Instantiate a view
		//$this->view->_name = 'module';
		$this->view = new Hubzero_Plugin_View(
			array(
				'folder'  => 'members',
				'element' => 'dashboard',
				'name'    => 'module'
			)
		);
		
		// Incoming
		$mid = JRequest::getInt('mid', 0);
		$uid = $this->member->get('uidNumber'); //JRequest::getInt('id', 0);

		// Make sure we have a module ID to load
		if (!$mid) 
		{
			$this->setError(JText::_('COM_MYHUB_ERROR_NO_MOD_ID'));
			return;
		}

		// Get the module from the database
		$myparams = new MyhubParams($this->database);
		$module = $myparams->loadModule($uid, $mid);

		// If the user has special prefs, load them.
		// Otherwise, load default prefs
		if ($module->myparams != '') 
		{
			$params = new JParameter($module->myparams);
		} 
		else 
		{
			$params = new JParameter($module->params);
		}

		if ($params) 
		{
			$rendered = false; //$this->render($params, $mainframe->getPath('mod0_xml', $module->module), 'module');
		}
		$module->params = $module->myparams;

		// Output the module
		$this->view->module = $module;
		$this->view->params = $params;
		$this->view->container = false;
		$this->view->extras = $extras;
		$this->view->rendered = $rendered;
		$this->view->config = $this->_params;
		$this->view->act = $this->_act;
		$this->view->option = $this->option;
		$this->view->juser = $this->juser;
		$this->view->database = $this->database;
		$this->view->member = $this->member;
	}
	
	/**
	 * Reload the contents of a module
	 * 
	 * @return     void
	 */
	protected function refresh()
	{
		$this->getmodule(false, '');
	}

	/**
	 * Builds the HTML for a module
	 * 
	 * NOTE: this is different from the method above in that
	 * it also builds the title, parameters form, and container
	 * for the module
	 * 
	 * @return     void
	 */
	protected function addmodule()
	{
		$this->getmodule(true, 'customize');
	}
	
	/**
	 * Short description for '_getUnusedModules'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $mods Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	private function _getUnusedModules($mods)
	{
		include_once(JPATH_ROOT . DS . 'libraries' . DS . 'joomla' . DS . 'database' . DS . 'table' . DS . 'module.php');
		$jmodule = new JTableModule($this->database);

		$position = ($this->_params->get('position')) ? $this->_params->get('position') : 'myhub';

		//$querym = '';
		$query = "SELECT id, title, module"
				. " FROM ".$jmodule->getTableName()." AS m"
				. " WHERE m.position='".$position."' AND m.published='1' AND m.client_id='0' ";
		$ids = implode(',', $mods);
		$ids = trim($ids, ',');
		if ($ids)
		{
			$query .= " AND id NOT IN(" . $ids .")";
		}
		//$querym = substr($querym, 0, strlen($querym)-4);
		//$query .= $querym;
		$query .= " ORDER BY ordering";

		$this->database->setQuery($query);
		$modules = $this->database->loadObjectList();

		return $modules;
	}
	
	/**
	 * Short description for '_getDefaultModules'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     string Return description (if any) ...
	 */
	private function _getDefaultModules()
	{
		$string = '';

		if ($this->_params->get('defaults')) 
		{
			$string = $this->_params->get('defaults');
		} 
		else 
		{
			$position = ($this->_params->get('position')) ? $this->_params->get('position') : 'myhub';

			include_once(JPATH_ROOT . DS . 'libraries' . DS . 'joomla' . DS . 'database' . DS . 'table' . DS . 'module.php');
			$jmodule = new JTableModule($this->database);

			$query = "SELECT m.id 
						FROM ".$jmodule->getTableName()." AS m 
						WHERE m.position='".$position."' AND m.published='1' AND m.client_id='0' 
						ORDER BY m.ordering LIMIT ".$this->num_default;
			$this->database->setQuery($query);
			$modules = $this->database->loadObjectList();

			if ($modules) {
				$i = 0;
				foreach ($modules as $module)
				{
					$i++;
					$string .= $module->id;
					if ($i == 2) 
					{
						$i = 0;
						$string .= ';';
					} 
					else 
					{
						$string .= ',';
					}
				}
				$string = substr($string, 0, strlen($string)-1);
			}
		}

		return $string;
	}
}

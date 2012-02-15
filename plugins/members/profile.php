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

//-----------

jimport('joomla.plugin.plugin');
JPlugin::loadLanguage('plg_members_profile');

//-----------

class plgMembersProfile extends JPlugin
{
	public function plgMembersProfile(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin('members', 'profile');
		$this->_params = new JParameter($this->_plugin->params);
	}
	
	//-----------
	
	public function &onMembersAreas( $user, $member )
	{
		$areas = array();
		
		$areas['profile'] = JText::_('PLG_MEMBERS_PROFILE');
				
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

		$arr = array(
			'html' => '',
			'metadata' => ''
		);

		// Build the final HTML
		if ($returnhtml) 
		{
			// Load some needed libraries
			ximport('Hubzero_Registration');
			
			// Find out which fields are hidden, optional, or required
			$registration = new JObject();
			$registration->Fullname = $this->_registrationField('registrationFullname','RRRR','edit');
			$registration->Email = $this->_registrationField('registrationEmail','RRRR','edit');
			$registration->URL = $this->_registrationField('registrationURL','HHHH','edit');
			$registration->Phone = $this->_registrationField('registrationPhone','HHHH','edit');
			$registration->Employment = $this->_registrationField('registrationEmployment','HHHH','edit');
			$registration->Organization = $this->_registrationField('registrationOrganization','HHHH','edit');
			$registration->Citizenship = $this->_registrationField('registrationCitizenship','HHHH','edit');
			$registration->Residency = $this->_registrationField('registrationResidency','HHHH','edit');
			$registration->Sex = $this->_registrationField('registrationSex','HHHH','edit');
			$registration->Disability = $this->_registrationField('registrationDisability','HHHH','edit');
			$registration->Hispanic = $this->_registrationField('registrationHispanic','HHHH','edit');
			$registration->Race = $this->_registrationField('registrationRace','HHHH','edit');
			$registration->Interests = $this->_registrationField('registrationInterests','HHHH','edit');
			$registration->Reason = $this->_registrationField('registrationReason','HHHH','edit');
			$registration->OptIn = $this->_registrationField('registrationOptIn','HHHH','edit');
			
			$rparams = new JParameter( $member->get('params') );
			$params = JComponentHelper::getParams('com_members');
			$params->merge( $rparams );
			
			ximport('Hubzero_Document');
			Hubzero_Document::addPluginStylesheet('members', 'profile');
			
			$document =& JFactory::getDocument();
			if (is_file(JPATH_ROOT . DS . 'plugins' . DS . 'members' . DS . 'profile' . DS . 'profile.js')) {
				$document->addScript('plugins' . DS . 'members' . DS . 'profile' . DS . 'profile.js');
			}
			
			ximport('Hubzero_Plugin_View');
			$this->view = new Hubzero_Plugin_View(
				array(
					'folder'  => 'members',
					'element' => 'profile',
					'name'    => 'index'
				)
			);
			
			$this->view->option = "com_members";
			$this->view->profile = $member;
			$this->view->registration = $registration;
			$this->view->params = $params;
			
			if ($this->getError()) 
			{
				$this->view->setError($this->getError());
			}
			
			$arr['html'] = $this->view->loadTemplate();
		}

		return $arr;
	}
	
	
	private function _registrationField($name, $default, $task = 'create')
	{
		switch ($task) 
		{
			case 'register':
			case 'create': $index = 0; break;
			case 'proxy':  $index = 1; break;
			case 'update': $index = 2; break;
			case 'edit':   $index = 3; break;
			default:       $index = 0; break;
		}

		$hconfig =& JComponentHelper::getParams('com_hub');
		$default = str_pad($default, '-', 4);
		$configured = $hconfig->get($name);
		if (empty($configured)) {
			$configured = $default;
		}
		$length = strlen($configured);
		if ($length > $index) {
			$value = substr($configured, $index, 1);
		} else {
			$value = substr($default, $index, 1);
		}
		
		switch ($value)
		{
			case 'R': return(REG_REQUIRED);
			case 'O': return(REG_OPTIONAL);
			case 'H': return(REG_HIDE);
			case '-': return(REG_HIDE);
			case 'U': return(REG_READONLY);
			default : return(REG_HIDE);
		}
	}
	
}

<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_installer
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

/**
 * Installer helper.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_installer
 * @since		1.6
 */
class InstallerHelper
{
	/**
	 * Configure the Linkbar.
	 *
	 * @param	string	The name of the active view.
	 */
	public static function addSubmenu($vName = 'install')
	{
		Submenu::addEntry(
			Lang::txt('COM_INSTALLER_SUBMENU_INSTALL'),
			Route::url('index.php?option=com_installer'),
			$vName == 'install'
		);
		Submenu::addEntry(
			Lang::txt('COM_INSTALLER_SUBMENU_UPDATE'),
			Route::url('index.php?option=com_installer&view=update'),
			$vName == 'update'
		);
		Submenu::addEntry(
			Lang::txt('COM_INSTALLER_SUBMENU_MANAGE'),
			Route::url('index.php?option=com_installer&view=manage'),
			$vName == 'manage'
		);
		Submenu::addEntry(
			Lang::txt('COM_INSTALLER_SUBMENU_DISCOVER'),
			Route::url('index.php?option=com_installer&view=discover'),
			$vName == 'discover'
		);
		/*Submenu::addEntry(
			Lang::txt('COM_INSTALLER_SUBMENU_DATABASE'),
			Route::url('index.php?option=com_installer&view=database'),
			$vName == 'database'
		);*/
		Submenu::addEntry(
			Lang::txt('COM_INSTALLER_SUBMENU_WARNINGS'),
			Route::url('index.php?option=com_installer&view=warnings'),
			$vName == 'warnings'
		);
		Submenu::addEntry(
			Lang::txt('COM_INSTALLER_SUBMENU_LANGUAGES'),
			Route::url('index.php?option=com_installer&view=languages'),
			$vName == 'languages'
		);
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return	Object
	 * @since	1.6
	 */
	public static function getActions()
	{
		$user = User::getRoot();
		$result = new \Hubzero\Base\Object;

		$assetName = 'com_installer';

		$actions = JAccess::getActions($assetName);

		foreach ($actions as $action)
		{
			$result->set($action->name,	$user->authorise($action->name, $assetName));
		}

		return $result;
	}
}

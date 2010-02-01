<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );


//----------------------------------------------------------

require_once( JApplicationHelper::getPath( 'toolbar_html' ) );
//-----------


switch ($task) 
{
	case 'subscriptions':   	ServicesToolbar::_DEFAULT();   break;
	case 'subscription': 		ServicesToolbar::_SUBSCRIPTION(); break;
	case 'services':   			ServicesToolbar::_SERVICES();   break;
	case 'service': 			ServicesToolbar::_SERVICE(); break;
	case 'newservice': 			ServicesToolbar::_SERVICE(); break;
	default: ServicesToolbar::_DEFAULT(); break;
}



?>
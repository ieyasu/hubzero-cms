<?php
/**
 * HUBzero CMS
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
 *
 * @package   hubzero-cms
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Short description for 'AnswersBuildRoute'
 * 
 * Long description (if any) ...
 * 
 * @param  array &$query Parameter description (if any) ...
 * @return array Return description (if any) ...
 */
function AnswersBuildRoute(&$query)
{
    $segments = array();

    if (!empty($query['task'])) {
		if ($query['task'] == 'new') {
			$segments[] = 'question';
			$segments[] = 'new';
		} else {
        	$segments[] = $query['task'];
        }
		unset($query['task']);
    }
    if (!empty($query['tag'])) {
        $segments[] = $query['tag'];
        unset($query['tag']);
    }
    if (!empty($query['id'])) {
        $segments[] = $query['id'];
        unset($query['id']);
    }
    return $segments;
}

/**
 * Short description for 'AnswersParseRoute'
 * 
 * Long description (if any) ...
 * 
 * @param  array $segments Parameter description (if any) ...
 * @return array Return description (if any) ...
 */
function AnswersParseRoute($segments)
{
    $vars = array();

    // Count route segments
    $count = count($segments);

	if (empty($segments[0])) {
		return $vars;
	}

    switch ($segments[0])
	{
		case 'latest.rss':
			$vars['task'] = $segments[0];
			break;
		case 'question':
			if (empty($segments[1])) {
				return $vars;
			}

			$vars['task'] = 'question';

			if ($segments[1] == 'new') {
				$vars['task'] = 'new';
				return $vars;
			}

	        $vars['id'] = $segments[1];
		break;

		case 'tags':
			$vars['task'] = 'tags';
        	$vars['tag'] = $segments[1];
		break;

		case 'myquestions':
			$vars['task'] = 'myquestions';
		break;

		case 'search':
			$vars['task'] = 'search';
		break;

		case 'answer':
			$vars['task'] = 'answer';
			$vars['id'] = $segments[1];
		break;

		case 'delete':
			$vars['task'] = 'delete';
			$vars['id'] = $segments[1];
		break;

		case 'delete_q':
			$vars['task'] = 'delete_q';
			$vars['id'] = $segments[1];
		break;

		case 'rateitem':
			$vars['task'] = 'rateitem';
		break;

		case 'reply':
			$vars['task'] = 'reply';
			$vars['id'] = $segments[1];
		break;

		case 'math':
			$vars['task'] = 'math';
			$vars['id'] = $segments[1];
		break;

		case 'savereply':
			$vars['task'] = 'reply';
		break;

		case 'accept':
			$vars['task'] = 'accept';
			$vars['id'] = $segments[1];
			$vars['rid'] = $segments[2];
		break;
	}

    return $vars;
}
?>
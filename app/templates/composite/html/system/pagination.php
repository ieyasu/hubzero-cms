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

defined('_HZEXEC_') or die();

/**
 * This is a file to add template specific chrome to pagination rendering.
 *
 * pagination_list_footer
 * 	Input variable $list is an array with offsets:
 * 		$list[limit]		: int
 * 		$list[limitstart]	: int
 * 		$list[total]		: int
 * 		$list[limitfield]	: string
 * 		$list[pagescounter]	: string
 * 		$list[pageslinks]	: string
 *
 * pagination_list_render
 * 	Input variable $list is an array with offsets:
 * 		$list[all]
 * 			[data]		: string
 * 			[active]	: boolean
 * 		$list[start]
 * 			[data]		: string
 * 			[active]	: boolean
 * 		$list[previous]
 * 			[data]		: string
 * 			[active]	: boolean
 * 		$list[next]
 * 			[data]		: string
 * 			[active]	: boolean
 * 		$list[end]
 * 			[data]		: string
 * 			[active]	: boolean
 * 		$list[pages]
 * 			[{PAGE}][data]		: string
 * 			[{PAGE}][active]	: boolean
 *
 * pagination_item_active
 * 	Input variable $item is an object with fields:
 * 		$item->base	: integer
 * 		$item->link	: string
 * 		$item->text	: string
 *
 * pagination_item_inactive
 * 	Input variable $item is an object with fields:
 * 		$item->base	: integer
 * 		$item->link	: string
 * 		$item->text	: string
 *
 * This gives template designers ultimate control over how pagination is rendered.
 *
 * NOTE: If you override pagination_item_active OR pagination_item_inactive you MUST override them both
 */

function pagination_list_footer($list)
{
	$html  = '<ul class="list-footer">'."\n";
	$html .= "\t".'<li class="counter">'.Lang::txt('Results').' '.($list['limitstart'] + 1).' - ';
	$html .= ($list['total'] > ($list['limitstart'] + $list['limit'])) ? ($list['limitstart'] + $list['limit']) : $list['total'];
	$html .= ' '.Lang::txt('of').' '.$list['total'].'</li>'."\n";
	$html .= "\t".'<li class="limit"><label for="limit">'.Lang::txt('Display Num').'</label> '.$list['limitfield'].'</li>'."\n";
	//$html .= $list['pageslinks'];
	$html .= pagination_list_render2($list);
	$html .= '</ul>'."\n";
	$html .= '<input type="hidden" name="limitstart" value="'.$list['limitstart'].'" />'."\n";

	return $html;
}

function pagination_list_render2($list) 
{
	$pages = $list['pageslinks'];

	$html = '';

	$html .= "\t" . '<li class="start">';
	if (isset($pages['start']))
	{
		if ($pages['start']->link) 
		{
			$html .= '<a href="' . $pages['start']->link .'">' . $pages['start']->text .'</a>';
		}
		else 
		{
			$html .= '<span>' . $pages['start']->text .'</span>';
		}
	}
	else 
	{
		$html .= '<span>' . Lang::txt('Start') .'</span>';
	}
	$html .= '</li>' . "\n";

	$html .= "\t" . '<li class="prev">';
	if (isset($pages['previous']))
	{
		if ($pages['previous']->link) 
		{
			$html .= '<a href="' . $pages['previous']->link .'">' . $pages['previous']->text .'</a>';
		}
		else 
		{
			$html .= '<span>' . $pages['previous']->text .'</span>';
		}
	}
	else 
	{
		$html .= '<span>' . Lang::txt('Prev') .'</span>';
	}
	$html .= '</li>' . "\n";

	$link = '';

	if (isset($pages['pages']) && count($pages['pages']) > 1) 
	{
		if ($pages['pages'][0]->link) 
		{ 
			$link = $pages['pages'][0]->link;
		} 
		else 
		{
			$link = $pages['pages'][1]->link;
		}

		$link = preg_replace('/limitstart=[0-9]+/i',"",$link);
		$link = preg_replace('/start=[0-9]+/i',"",$link);
		$link = preg_replace('/limit=[0-9]+/i',"",$link);
		$link = str_replace('?&amp;','?',$link);
		if (!strstr($link, '?'))
		{
			$link .= '?';
		}
	}

	$displayed_pages = 10;
	$total_pages = ($list['limit'] > 0) ? ceil( $list['total'] / $list['limit'] ) : 1;
	$this_page = ($list['limit'] > 0) ? ceil( ($list['limitstart']+1) / $list['limit'] ) : $list['limitstart']+1;

	$pager_middle = ceil($displayed_pages / 2);
	$start_loop = $this_page - $pager_middle + 1;
	$stop_loop = $this_page + $displayed_pages - $pager_middle;
	$i = $start_loop;
	if ($stop_loop > $total_pages) 
	{
		$i = $i + ($total_pages - $stop_loop);
		$stop_loop = $total_pages;
	}
	if ($i <= 0) 
	{
		$stop_loop = $stop_loop + (1 - $i);
		$i = 1;
	}

	if ($i > 1) 
	{
		$html .= "\t".'<li class="page">...</li>'."\n";
	}

	for (; $i <= $stop_loop && $i <= $total_pages; $i++) 
	{
		$page = ($i - 1) * $list['limit'];
		if ($i == $this_page) 
		{
			$html .= "\t".'<li class="page"><strong>'. $i .'</strong></li>'."\n";
		} 
		else 
		{
			$html .= "\t".'<li class="page"><a href="'.$link.'limit='.$list['limit'].'&amp;limitstart='. $page  .'">'. $i .'</a></li>'."\n";
		}
	}
	
	if (($i - 1) < $total_pages) 
	{
		$html .= "\t".'<li class="page">...</li>'."\n";
	}

	
	$html .= "\t" . '<li class="next">';
	if (isset($pages['next']))
	{
		if ($pages['next']->link) 
		{
			$html .= '<a href="' . $pages['next']->link .'">' . $pages['next']->text .'</a>';
		}
		else 
		{
			$html .= '<span>' . $pages['next']->text .'</span>';
		}
	}
	else 
	{
		$html .= '<span>' . Lang::txt('Next') .'</span>';
	}
	$html .= '</li>' . "\n";

	$html .= "\t" . '<li class="end">';
	if (isset($pages['end']))
	{
		if ($pages['end']->link) 
		{
			$html .= '<a href="' . $pages['end']->link .'">' . $pages['end']->text .'</a>';
		}
		else 
		{
			$html .= '<span>' . $pages['end']->text .'</span>';
		}
	}
	else 
	{
		$html .= '<span>' . Lang::txt('End') .'</span>';
	}
	$html .= '</li>' . "\n";

	return $html;
}

function pagination_list_render($list)
{
	// All we're really doing here is gathering Joomla's pagination data
	// so we can use it in pagination_list_render2
	
	// Who do all the work elsewhere? Because we don't have the limit number
	// in this data. Joomla only passes that when we get to pagination_list_footer()
	$pages = array();
	$pages['start'] = $list['start']['data'];
	$pages['previous'] = $list['previous']['data'];
	$pages['pages'] = array();
	foreach ($list['pages'] as $page)
	{
		$pages['pages'][] = $page['data'];
	}
	$pages['next'] = $list['next']['data'];
	$pages['end'] = $list['end']['data'];
	return $pages;
}

function pagination_item_active(&$item) 
{
	return $item;
}

function pagination_item_inactive(&$item) 
{
	return $item;
}

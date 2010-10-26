<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>
<div id="content-header">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->

<form action="<?php echo JRoute::_('index.php?option='.$this->option); ?>" method="get" id="tagBrowserForm">

	<div id="content-header-extra">
		<fieldset>
			<label>
				<span>hackhackhackhackhack<?php echo JText::_('COM_RESOURCES_TYPE'); ?>:</span> 
				<select name="type">
<?php 
foreach ($this->types as $type) 
{
?>
					<option value="<?php echo $type->title; ?>"<?php if ($type->id == $this->filters['type']) { echo ' selected="selected"'; } ?>><?php echo $type->type; ?></option>
<?php 
} 
?>
				</select>
			</label>
			<input type="submit" value="<?php echo JText::_('COM_RESOURCES_GO'); ?>"/>
			<input type="hidden" name="task" value="browsetags" />
			
		</fieldset>
	</div><!-- / #content-header-extra -->
	<form method="get" action="/search" id="searchform">
	<fieldset>
	<label for="searchword" style="display: non; ">Resource Title:</label>
	<input type="search" name="terms" id="searchword" size="20" value="" style="width: 185px; " placeholder="Search" autosave="bsn_search" results="5">
	<?php if (isset($this->terms)) { ?>
			<label>Currently searching for: '<?php echo $this->terms?>'</label>
			<?php if (isset($this->designator)){  ?>
			<label> and </label>
			<?php } ?>
	<?php } if (isset($this->designator)){  ?>
			<label>Only showing Resources tagged: '<?php echo $this->designator?>'</label>
	<?php } ?>
	<?php if (isset($this->designator)) { ?>
			<input type="hidden" name="designator" id="designator" value="<?php echo $this->designator; ?>" />
	<?php } ?>
	</fieldset>
	</form>
	<div class="main section" id="browse-resources">
		<div id="tagbrowser">
			<p class="info"><?php echo JText::_('COM_RESOURCES_TAGBROWSER_EXPLANATION'); ?></p>
			<div id="level-1">
				<h3><?php echo JText::_('COM_RESOURCES_TAG'); ?></h3>
				<ul>
					<li id="level-1-loading"></li>
				</ul>
			</div><!-- / #level-1 -->
			<div id="level-2">
				<h3><?php echo JText::_('COM_RESOURCES'); ?> <select name="sortby" id="sortby"></select></h3>
				<ul>
					<li id="level-2-loading"></li>
				</ul>
			</div><!-- / #level-2 -->
			<div id="level-3">
				<h3><?php echo JText::_('COM_RESOURCES_INFO'); ?></h3>
				<ul>
					<li><?php echo JText::_('COM_RESOURCES_TAGBROWSER_COL_EXPLANATION'); ?></li>
				</ul>
			</div><!-- / #level-3 -->
			<input type="hidden" name="pretype" id="pretype" value="<?php echo $this->filters['type']; ?>" />
			<input type="hidden" name="id" id="id" value="" />
			<input type="hidden" name="preinput" id="preinput" value="<?php echo $this->tag; ?>" />
			<input type="hidden" name="preinput2" id="preinput2" value="<?php echo $this->tag2; ?>" />
			<?php if (isset($this->terms)) { ?>
			<input type="hidden" name="terms" id="terms" value="<?php echo $this->terms; ?>" />
			<?php } ?>
			<?php if (isset($this->designator)) { ?>
			<input type="hidden" name="designator" id="designator" value="<?php echo $this->designator; ?>" />
			<?php } ?>
			<div class="clear"></div>
		</div><!-- / #tagbrowser -->
		
		<p id="viewalltools"><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&type='.$this->filters['type']); ?>"><?php echo JText::_('COM_RESOURCES_VIEW_MORE'); ?></a></p>
		<div class="clear"></div>

<?php
$database =& JFactory::getDBO();

if ($this->supportedtag) {
	include_once(JPATH_ROOT.DS.'components'.DS.'com_tags'.DS.'tags.tag.php');
	
	$tag = new TagsTag( $database );
	$tag->loadTag($this->supportedtag);

	$sl = $this->config->get('supportedlink');
	if ($sl) {
		$link = $sl;
	} else {
		$link = JRoute::_('index.php?option=com_tags&tag='.$tag->tag);
	}
?>
		<p class="supported"><?php echo JText::_('COM_RESOURCES_WHATS_THIS'); ?> <a href="<?php echo $link; ?>"><?php echo JText::sprintf('COM_RESOURCES_ABOUT_TAG', $tag->raw_tag); ?></a></p>
<?php
}
?>

<?php
if ($this->results) {
?>
		<h3><?php echo JText::_('COM_RESOURCES_TOP_RATED'); ?></h3>
		<div class="aside">
			<p><?php echo JText::_('COM_RESOURCES_TOP_RATED_EXPLANATION'); ?></p>
		</div><!-- / .aside -->
		<div class="subject">
			<?php echo ResourcesHtml::writeResults( $database, $this->results, $this->authorized ); ?>
		</div><!-- / .subject -->
<?php
}
?>
	</div><!-- / .main section -->

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
defined('_JEXEC') or die('Restricted access');

$canDo = TagsHelper::getActions();

$text = ($this->task == 'edit' ? JText::_('EDIT') : JText::_('NEW'));

JToolBarHelper::title(JText::_('TAGS') . ': ' . $text, 'tags.png');
if ($canDo->get('core.edit')) 
{
	JToolBarHelper::save();
	JToolBarHelper::apply();
	JToolBarHelper::spacer();
}
JToolBarHelper::cancel();
JToolBarHelper::spacer();
JToolBarHelper::help('edit.html', true);

?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	// do field validation
	if (form.raw_tag.value == '') {
		alert('<?php echo JText::_('ERROR_EMPTY_TAG'); ?>');
	} else {
		submitform(pressbutton);
	}
}
</script>

<?php
if ($this->getError()) 
{
	echo '<p class="error">' . implode('<br />', $this->getError()) . '</p>';
}
?>

<form action="index.php" method="post" name="adminForm" id="item-form">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('DETAILS'); ?></span></legend>

			<div class="input-wrap" data-hint="<?php echo JText::_('Only administrators can see admin tags. They\'re a useful way to apply metadata that may not be appropriate or useful for the site\'s visitors.'); ?>">
				<input type="checkbox" name="fields[admin]" id="field-admin" value="1" <?php if ($this->tag->get('admin') == 1) { echo 'checked="checked"'; } ?> /> 
				<label for="field-admin"><?php echo JText::_('ADMIN'); ?></label>
			</div>

			<div class="input-wrap" data-hint="<?php echo JText::_('To create the normalized tag (used for URLs), all spaces, punctuation, and non-alpanumeric characters are stripped. &quot;N.Y.&quot;, &quot;NY&quot;, and &quot;ny&quot; will all have a normalized tag of &quot;ny&quot;.'); ?>">
				<label for="field-raw_tag"><?php echo JText::_('RAW_TAG'); ?>: <span class="required"><?php echo JText::_('requiredG'); ?></span></label><br />
				<input type="text" name="fields[raw_tag]" id="field-raw_tag" size="30" maxlength="250" value="<?php echo $this->escape(stripslashes($this->tag->get('raw_tag'))); ?>" />
			</div>

			<div class="input-wrap" data-hint="<?php echo JText::_('Enter a comma-separated list of alternate spellings, abbreviations, or synonyms for this tag.'); ?>">
				<label for="field-substitutions"><?php echo JText::_('ALIAS'); ?>:</label><br />
				<textarea name="fields[substitutions]" id="field-substitutions" cols="50" rows="5"><?php echo $this->escape(stripslashes($this->tag->substitutes('string'))); ?></textarea>
			</div>

			<div class="input-wrap">
				<label for="field-description"><?php echo JText::_('DESCRIPTION'); ?>:</label><br />
				<?php echo JFactory::getEditor()->display('fields[description]', stripslashes($this->tag->get('description')), '', '', '50', '4', false, 'field-description', null, null, array('class' => 'minimal')); ?>
			</div>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
		<div class="data-wrap">
<?php
	if ($this->tag->exists())
	{
		if ($logs = $this->tag->logs('list'))
		{
?>
			<h4><?php echo JText::_('Activity log'); ?></h4>
			<ul class="entry-log">
				<?php
				foreach ($logs as $log)
				{
					$actor = $this->escape(stripslashes($log->actor('name')));

					$data = json_decode($log->get('comments'));
					if (!is_object($data))
					{
						$data = new stdClass;
					}
					if (!isset($data->entries))
					{
						$data->entries = 0;
					}
					switch ($log->get('action'))
					{
						case 'substitute_created':
							$c = 'created';
							$s = JText::sprintf('%s alias created on %s by %s', $data->raw_tag, $log->get('timestamp'), $actor);
						break;

						case 'substitute_edited':
							$c = 'edited';
							$s = JText::sprintf('%s alias edited on %s by %s', $data->raw_tag, $log->get('timestamp'), $actor);
						break;

						case 'substitute_deleted':
							$c = 'deleted';
							$s = JText::sprintf('%s aliases removed on %s by %s', implode(', ', $data->tags), $log->get('timestamp'), $actor);
						break;

						case 'substitute_moved':
							$c = 'moved';
							$s = JText::sprintf('%s aliases moved from %s on %s by %s', count($data->entries), $data->old_id, $log->get('timestamp'), $actor);
						break;

						case 'tags_removed':
							$c = 'deleted';
							$s = JText::sprintf('%s associations removed from %s %s on %s by %s', count($data->entries), $data->tbl, $data->objectid, $log->get('timestamp'), $actor);
						break;

						case 'objects_copied':
							$c = 'copied';
							$s = JText::sprintf('%s associations copied from %s on %s by %s', count($data->entries), $data->old_id, $log->get('timestamp'), $actor);
						break;

						case 'objects_moved':
							$c = 'moved';
							$s = JText::sprintf('%s associations moved from %s on %s by %s', count($data->entries), $data->old_id, $log->get('timestamp'), $actor);
						break;

						case 'objects_removed':
							$c = 'deleted';
							if ($data->objectid || $data->tbl)
							{
								$s = JText::sprintf('%s associations removed for %s %s on %s by %s', count($data->entries), $data->tbl, $data->objectid, $log->get('timestamp'), $actor);
							}
							else 
							{
								$s = JText::sprintf('%s associations removed on %s by %s', count($data->entries), $data->tagid, $log->get('timestamp'), $actor);
							}
						break;

						default:
							$c = 'edited';
							$s = JText::sprintf('%s on %s by %s', str_replace('_', ' ', $log->get('action')), $log->get('timestamp'), $actor);
						break;
					}
					if ($s)
					{
						?>
					<li class="<?php echo $c; ?>">
						<span class="entry-log-data"><?php echo $s; ?></span>
					</li>
							<?php 
					}
				}
				?>
			</ul>
<?php 
		}
	}
?>
		</div>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="fields[id]" value="<?php echo $this->tag->get('id'); ?>" />
	<input type="hidden" name="fields[tag]" value="<?php echo $this->tag->get('tag'); ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo JHTML::_('form.token'); ?>
</form>
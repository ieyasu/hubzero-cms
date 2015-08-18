<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access.
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_RESOURCES_IMPORTHOOK_TITLE_HOOKS'), 'import.png');

Toolbar::spacer();
Toolbar::addNew();
Toolbar::editList();
Toolbar::deleteList();
?>

<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
	// do field validation
	submitform( pressbutton );
}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset>
		<table class="adminlist">
			<thead>
				<tr>
					<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo $this->hooks->count(); ?>);" /></th>
					<th scope="col"><?php echo Lang::txt('COM_RESOURCES_IMPORTHOOK_DISPLAY_FIELD_NAME'); ?></th>
					<th scope="col"><?php echo Lang::txt('COM_RESOURCES_IMPORTHOOK_DISPLAY_FIELD_TYPE'); ?></th>
					<th scope="col" class="priority-2"><?php echo Lang::txt('COM_RESOURCES_IMPORTHOOK_DISPLAY_FIELD_FILE'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if ($this->hooks->count() > 0) : ?>
					<?php foreach ($this->hooks as $i => $hook) : ?>
						<tr>
							<td>
								<input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $hook->get('id'); ?>" onclick="isChecked(this.checked);" />
							</td>
							<td>
								<a href="<?php echo Route::url('index.php?option=com_resources&controller=importhooks&task=edit&id=' . $hook->get('id')); ?>">
									<?php echo $this->escape($hook->get('name')); ?>
								</a><br />
								<span class="hint">
									<?php echo nl2br($this->escape($hook->get('notes'))); ?>
								</span>
							</td>
							<td>
								<?php
									switch ($hook->get('type'))
									{
										case 'postconvert':    echo Lang::txt('COM_RESOURCES_IMPORTHOOK_DISPLAY_TYPE_POSTCONVERT');    break;
										case 'postmap':        echo Lang::txt('COM_RESOURCES_IMPORTHOOK_DISPLAY_TYPE_POSTMAP');        break;
										case 'postparse':
										default:               echo Lang::txt('COM_RESOURCES_IMPORTHOOK_DISPLAY_TYPE_POSTPARSE');      break;
									}
								?>
							</td>
							<td class="priority-2">
								<?php echo $hook->get('file'); ?> &mdash;
								<a target="_blank" href="<?php echo Route::url('index.php?option=com_resources&controller=importhooks&task=raw&id=' . $hook->get('id')); ?>">
									<?php echo Lang::txt('COM_RESOURCES_IMPORTHOOK_DISPLAY_FILE_VIEWRAW'); ?>
								</a>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php else : ?>
					<tr>
						<td colspan="4"><?php echo Lang::txt('COM_RESOURCES_IMPORTHOOK_NONE_FOUND'); ?></td>
					</tr>
				<?php endif; ?>
			</tbody>
		</table>
	</fieldset>
	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
	<input type="hidden" name="task" value="" autocomplete="off" />
	<input type="hidden" name="boxchecked" value="0" />

	<?php echo Html::input('token'); ?>
</form>
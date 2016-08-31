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

\Hubzero\Document\Assets::addPluginStylesheet('resources', 'share');

$view = Request::getVar( 'view', '' );
if ($this->filters['type'] == 6)
{
	$view = ($view) ? $view : 'taxonomy';
}

$this->css()
     ->js()
     ->js('tagbrowser');
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<?php foreach ($this->types as $type) { ?>
		<?php if ($type->id == $this->filters['type'] && $type->contributable) { ?>
		<div id="content-header-extra">
			<p>
				<?php if ($type->id == 7) { ?>
				<a class="icon-add btn" href="<?php echo Route::url('index.php?option=com_tools&task=create'); ?>">
					<?php
					$name = $type->type;
					if (substr($type->type, -1) == 's')
					{
						$name = substr($type->type, 0, -1);
					}
					echo Lang::txt('Start a new %s', $this->escape(stripslashes($name))); ?>
				</a>
				<?php } else { ?>
				<a class="icon-add btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=draft&step=1&type=' . $type->id); ?>">
					<?php
					$name = $type->type;
					if (substr($type->type, -1) == 's')
					{
						$name = substr($type->type, 0, -1);
					}
					echo Lang::txt('Start a new %s', $this->escape(stripslashes($name))); ?>
				</a>
				<?php } ?>
			</p>
		</div>
		<?php } ?>
	<?php } ?>
</header><!-- / #content-header -->

<form action="<?php echo Route::url('index.php?option='.$this->option); ?>" method="get" id="tagBrowserForm">

		<?php if ($this->filters['type'] == 7 || $this->filters['type'] == 6) { ?>
			<div id="sub-menu">
				<ul class="sub-menu">
					<li id="sm-1"<?php if ($view != 'taxonomy') { echo ' class="active"'; } ?>>
						<a class="tab" rel="tags" href="<?php echo Route::url('index.php?option='.$this->option.'&type='.strtolower($this->type).'&view=tags'); ?>">
							<span>Browse by Tags</span>
						</a>
					</li>
					<li id="sm-2"<?php if ($view == 'taxonomy') { echo ' class="active"'; } ?>>
						<a class="tab" rel="taxonomy" href="<?php echo Route::url('index.php?option='.$this->option.'&type='.strtolower($this->type).'&view=taxonomy'); ?>">
							<span>Browse Visually</span>
						</a>
					</li>
				</ul>
				<div class="clear"></div>
			</div><!-- /sub-menu -->
		<?php } ?>

<?php if ($view != 'taxonomy') { ?>
	<section class="main section" id="browse-resources">
		<fieldset>
			<label for="browse-type">
				<span><?php echo Lang::txt('COM_RESOURCES_TYPE'); ?>:</span> 
				<select name="type" id="browse-type">
				<?php foreach ($this->types as $type) { ?>
					<option value="<?php echo $this->escape($type->alias); ?>"<?php if ($type->id == $this->filters['type']) { echo ' selected="selected"'; } ?>><?php echo $this->escape(stripslashes($type->type)); ?></option>
				<?php } ?>
				</select>
			</label>
			<input type="submit" value="<?php echo Lang::txt('COM_RESOURCES_GO'); ?>"/>
			<input type="hidden" name="task" value="browsetags" />
		</fieldset>

		<div id="tagbrowser">
			<p class="info"><?php echo Lang::txt('COM_RESOURCES_TAGBROWSER_EXPLANATION'); ?></p>
			<div id="level-1">
				<h3><?php echo Lang::txt('COM_RESOURCES_TAG'); ?></h3>
				<ul>
					<li id="level-1-loading"></li>
				</ul>
			</div><!-- / #level-1 -->
			<div id="level-2">
				<h3><?php echo Lang::txt('COM_RESOURCES'); ?> <select name="sortby" id="sortby"></select></h3>
				<ul>
					<li id="level-2-loading"></li>
				</ul>
			</div><!-- / #level-2 -->
			<div id="level-3">
				<h3><?php echo Lang::txt('COM_RESOURCES_INFO'); ?></h3>
				<ul>
					<li><?php echo Lang::txt('COM_RESOURCES_TAGBROWSER_COL_EXPLANATION'); ?></li>
				</ul>
			</div><!-- / #level-3 -->
			<input type="hidden" name="pretype" id="pretype" value="<?php echo $this->filters['type']; ?>" />
			<input type="hidden" name="id" id="id" value="" />
			<input type="hidden" name="preinput" id="preinput" value="<?php echo $this->tag; ?>" />
			<input type="hidden" name="preinput2" id="preinput2" value="<?php echo $this->tag2; ?>" />
			<div class="clear"></div>
		</div><!-- / #tagbrowser -->

		<p id="viewalltools"><a href="<?php echo Route::url('index.php?option='.$this->option.'&type='.$this->filters['type']); ?>"><?php echo Lang::txt('COM_RESOURCES_VIEW_MORE'); ?></a></p>
		<div class="clear"></div>

		<?php if ($this->supportedtag) {
			$database = App::get('db');

			$tag = \Components\Tags\Models\Tag::oneByTag($this->supportedtag);

			$sl = $this->config->get('supportedlink');
			if ($sl) {
				$link = $sl;
			} else {
				$link = Route::url('index.php?option=com_tags&tag='.$tag->get('tag'));
			}
			?>
			<p class="supported"><?php echo Lang::txt('COM_RESOURCES_WHATS_THIS'); ?> <a href="<?php echo $link; ?>"><?php echo Lang::txt('COM_RESOURCES_ABOUT_TAG', $tag->raw_tag); ?></a></p>
		<?php } ?>
	</section>

	<?php if ($this->results) { ?>
		<section class="below section">
			<?php
			/*	<h2 class="explore">Explore Resources</h2>
				<div id="search-container">

				define('HG_INLINE', 1);
				require PATH_CORE.'/components/com_hubgraph/hubgraph_client.php';
				require PATH_CORE.'/components/com_hubgraph/search_request.php';

				Document::addStyleSheet('/components/com_hubgraph/hubgraph.css');
				Document::addScript('/components/com_hubgraph/jquery.js');
				Document::addScript('/components/com_hubgraph/hubgraph.js');

				require PATH_CORE.'/components/com_hubgraph/index-view.php';
			?>
				</div>
			<?php
			*/
			?>
			<div class="subject">
				<h3><?php echo Lang::txt('Popular'); ?></h3>
				<?php
				$config = Component::params('com_resources');
				$supported = array();
				if ($tag = $config->get('supportedtag'))
				{
					include_once(Component::path('com_resources') . DS . 'helpers' . DS . 'tags.php');
					$rt = new \Components\Resources\Helpers\Tags(0);
					$supported = $rt->getTagUsage($tag, 'id');
				}
				$this->view('_list', 'browse')
				     ->set('lines', $this->results)
				     ->set('show_edit', $this->authorized)
				     ->set('supported', $supported)
				     ->display();
				?>
			</div><!-- / .subject -->
			<aside class="aside">
				<p><?php echo Lang::txt('The following are popular resources of this type.'); ?></p>
			</aside><!-- / .aside -->
		</section><!-- / .main section -->
	<?php } ?>
<?php } else { ?>
	<?php
	$this->view('taxonomy', 'browse')
	     ->set('option', $this->option)
	     ->set('config', $this->config)
	     ->set('type', $this->filters['type'])
	     ->display();
	?>
<?php } ?>
</form>

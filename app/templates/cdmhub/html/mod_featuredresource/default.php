<?php
/**
 * @purpose  Alternate layout
 */

// No direct access
defined('_HZEXEC_') or die();

$database = App::get('db');
$query = "SELECT a.*,
		(SELECT GROUP_CONCAT(c.name SEPARATOR ', ') FROM `#__tool_authors` AS c WHERE a.alias = c.toolname AND c.revision = (SELECT MAX(c.revision) FROM `#__tool_authors` AS c WHERE a.alias = c.toolname)) AS contributors,
		(SELECT v.revision FROM `#__tool_version` AS v WHERE v.toolname = a.alias AND v.`state` = 1 LIMIT 1) AS revision
	FROM `#__resources` AS a
	WHERE a.type = 7
	AND a.published = 1
	AND a.title <> 'workspace'
	ORDER BY a.hits DESC
	LIMIT 4";
$database->setQuery($query);
$this->rows = $database->loadObjectList();

if ($this->getError()) { ?>
	<p class="error"><?php echo Lang::txt('MOD_FEATUREDRESOURCE_MISSING_CLASS'); ?></p>
<?php } else { ?>
<div class="resourcewrapper scrollpane-wrapper">
	<div class="scrollpane-inner-wrapper">
	<?php
	if ($this->rows) {
		foreach ($this->rows as $row)
		{
		?>
		<div class="resourcepane scrollpane clearfix">
			<div class="half-width">
				<p class="text _text text-9">
					<?php echo stripslashes($row->title); ?>
				</p>
				<p class="text _text text-10"><span class="resource-category">Tool</span> By <?php echo stripslashes($row->contributors); ?></p>
				<p class="text _text text-11">
					<?php if ($row->introtext) {
						echo htmlentities(strip_tags($row->introtext));
					} else {
						echo htmlentities(strip_tags($row->fulltxt));
					} ?>
				</p>
			</div>

			<div class="half-width">
				<!-- <div class="quote clearfix">
					<p class="text text-12">“</p>
					<p class="text _text">This tool helped my students visualize these concepts like never before! &nbsp;Every professor should consider using this as a part of their course.</p>
					<p class="text text-14">”</p>
				</div> -->
				<button class="_button _button-6" onclick="location.href='<?php echo Route::url('index.php?option=com_resources&alias=' . $row->alias); ?>'">Learn More</button>
				<button class="_button _button-7 launchtool" onclick="location.href='<?php echo Route::url('index.php?option=com_tools&app=' . $row->alias . '&task=invoke/' . $row->revision); ?>'">Launch Tool<span class="small"><br>(Requres Login)</span></button>
			</div>
		</div>
		<?php
		}
		?>
	</div>
	<div class="links-container element _container clearfix">
		<div class="resources-links wrapper clearfix">
			<?php
			$index = 0;
			foreach ($this->rows as $row)
			{
				?><button class="_button <?php echo $index == 0 ? 'active' : ''; ?>" data-index="<?php echo $index++; ?>"><?php echo stripslashes($row->title); ?></button><?php
			}
			?>
		</div>
	</div>
</div>
	<?php } else { ?>
		<div class="<?php echo $this->cls; ?>">
			<p>
				<?php echo Lang::txt('No results'); ?>
			</p>
		</div>
	</div>
</div>
<?php
	}
}

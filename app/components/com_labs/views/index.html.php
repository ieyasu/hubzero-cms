<?php defined('JPATH_BASE') or die();
if (!$labs) {
	throw new NotFoundError('No labs are active here');
}
?>
<ol class="labs">
<?php foreach ($labs as $lab): ?>
	<li>
		<a href="/labs/run/<?php echo $a($lab['name'].($lab['entrance'] ? $lab['entrance'] : '')); ?>"><?php echo $h($lab['title']); ?></a>
		<?php if ($lab['description']): ?>
			<blockquote><?php echo $h($lab['description']) ?></blockquote>
		<?php endif; ?>
	</li>
<?php endforeach; ?>
</ol>

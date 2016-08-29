<?php defined('_HZEXEC_') or die(); ?>
<tr>
	<td class="news-date">
		<span class="month"><?php echo Date::of($item->publish_up)->toLocal('%b'); ?></span><span class="day"><?php echo Date::of($item->publish_up)->toLocal('%d'); ?></span>
	</td>
	<td class="news-title">
<?php if (!$params->get('intro_only')) :
	echo $item->afterDisplayTitle;
endif; ?>
		<?php echo $item->beforeDisplayContent; ?>
		<?php if ($params->get('item_title')) : ?>
			<?php if ($params->get('link_titles') && $item->linkOn != '') : ?>
				<a href="<?php echo '/news/latest/'.$item->alias; //$item->linkOn;?>" class="contentpagetitle<?php echo $params->get( 'moduleclass_sfx' ); ?>">
					<?php echo $item->title;?>
				</a><br />
			<?php else : ?>
				<?php echo $item->title; ?><br />
			<?php endif; ?>
		<?php endif; ?>
		<?php 
		$chars=75;
			$text = strip_tags($item->text);
			$text = trim($text);

			if (strlen($text) > $chars) {
				$text = $text.' ';
				$text = substr($text,0,$chars);
				$text = substr($text,0,strrpos($text,' '));
				$text = $text.' &#8230;';
			}

			if ($text == '') {
				$text = '&#8230;';
			}

		echo $text; 
		?>
	</td>
</tr>
<?php
/**
 * @purpose  Adding nanoHUB Pro indicator
 */

// No direct access.
defined('_HZEXEC_') or die();

$no_html = Request::getInt('no_html', 0);
$user_messaging = $this->config->get('user_messaging', 0);

$prefix = $this->profile->get("name") . "'s";
$edit = false;
$password = false;
$messaging = false;

$tab = $this->active;
$tab_name = 'Dashboard';

//are we allowed to messagin user
switch ($user_messaging)
{
	case 0:
		$mssaging = false;
		break;
	case 1:
		$common = \Hubzero\User\Helper::getCommonGroups(User::get('id'), $this->profile->get('id'));
		if (count($common) > 0)
		{
			$messaging = true;
		}
		break;
	case 2:
		$messaging = true;
		break;
}

//if user is this member turn on editing and password change, turn off messaging
if ($this->profile->get('id') == User::get("id"))
{
	if ($this->active == "profile")
	{
		$edit = true;
		$password = true;
	}
	$messaging = false;
	$prefix = "My";
}

//no messaging if guest
if (User::isGuest())
{
	$messaging = false;
}

if (!$no_html)
{
$this->css()
	->js();
?>
	<header id="content-header" class="content-header">
		<h2>
			<?php echo $this->escape(stripslashes($this->profile->get('name'))); ?>
		</h2>

		<?php
		$uId = $this->profile->get('id');
		$proEnabled = false;
		$isPro = false;

		$user = User::getInstance();

		require_once(PATH_CORE . DS . 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Memberships.php');
		$proSubscription = Components\Storefront\Models\Memberships::getMembershipInfoByUser($uId, $user->getState('proId'));

		// check if PRO plugin is published
		$plugin = Plugin::byType('system', 'pro');
		$proEnabled = $user->getState('proEnabled');

		if ($plugin && $proEnabled)
		{
			if (!User::get('guest') && $proEnabled = $user->getState('pro'))
			{
				$isPro = true;
			}
		}

		if ($proEnabled && $proSubscription['crtmActive'] && $proSubscription['crtmExpires'])
		{
			?>
			<p class="proBar"><strong>Pro</strong> Pro membership expires <?php echo date('M j, Y', strtotime($proSubscription['crtmExpires'])); ?></p>
			<?php
		}
		?>

		<?php if ($this->profile->get('id') == User::get('id')) :
			$cls = '';
			$span_title = Lang::txt('COM_MEMBERS_PUBLIC_PROFILE_TITLE');
			$title = Lang::txt('COM_MEMBERS_PUBLIC_PROFILE_SET_PRIVATE_TITLE');

			if ($this->profile->get('access') == 2)
			{
				$cls = 'protected';
				$span_title = Lang::txt('COM_MEMBERS_PROTECTED_PROFILE_TITLE');
				$title = Lang::txt('COM_MEMBERS_PRIVATE_PROFILE_SET_PUBLIC_TITLE');
			}

			if ($this->profile->get('access') > 2)
			{
				$cls = 'private';
				$span_title = Lang::txt('COM_MEMBERS_PRIVATE_PROFILE_TITLE');
				$title = Lang::txt('COM_MEMBERS_PRIVATE_PROFILE_SET_PUBLIC_TITLE');
			}

			if ($this->active == 'profile') : ?>
				<a id="profile-privacy" href="<?php echo Route::url($this->profile->link() . '&' . Session::getFormToken() . '=1'); ?>"
					data-id="<?php echo $this->profile->get('id'); ?>"
					data-private="<?php echo Lang::txt('Click here to set your profile private.'); ?>"
					data-public="<?php echo Lang::txt('Click here to set your profile public.'); ?>"
					class="<?php echo $cls; ?> tooltips"
					title="<?php echo $title; ?>">
					<?php echo $title; ?>
				</a>
			<?php else: ?>
				<span id="profile-privacy"<?php echo ($cls ? ' class="' . $cls . '"' : ''); ?>
					data-id="<?php echo $this->profile->get('id'); ?>"
					data-private="<?php echo Lang::txt('Click here to set your profile private.'); ?>"
					data-public="<?php echo Lang::txt('Click here to set your profile public.'); ?>">
					<?php echo $span_title; ?>
				</span>
			<?php endif; ?>
		<?php endif; ?>
	</header>

<div class="innerwrap sidebar-layout">
	<div id="page_container">
		<div id="page_sidebar">
			<div id="page_identity">
				<?php $title = ($this->profile->get('id') == User::get('id')) ? Lang::txt('COM_MEMBERS_GO_TO_MY_DASHBOARD') : Lang::txt('COM_MEMBERS_GO_TO_MEMBER_PROFILE', $this->profile->get('name')); ?>
				<a href="<?php echo Route::url($this->profile->link()); ?>" id="page_identity_link" title="<?php echo $title; ?>">
					<img src="<?php echo $this->profile->picture(0, false); ?>" alt="<?php echo Lang::txt('COM_MEMBERS_PROFILE_PICTURE_FOR', $this->escape(stripslashes($this->profile->get('name')))); ?>" class="profile-pic full" />
				</a>
			</div><!-- /#page_identity -->
			<?php if ($messaging): ?>
				<ul id="member_options">
					<li class="message-member">
						<a class="tooltips" title="<?php echo Lang::txt('COM_MEMBERS_MESSAGE'); ?> :: <?php echo Lang::txt('COM_MEMBERS_SEND_A_MESSAGE_TO', $this->escape(stripslashes($this->profile->get('name')))); ?>" href="<?php echo Route::url('index.php?option=com_members&id=' . User::get("id") . '&active=messages&task=new&to[]=' . $this->profile->get('uidNumber')); ?>">
							<?php echo Lang::txt('COM_MEMBERS_MESSAGE'); ?>
						</a>
					</li>
				</ul>
			<?php endif; ?>
			<ul id="page_menu">
				<?php foreach ($this->cats as $k => $c) : ?>
					<?php
					$key = key($c);
					if (!$key)
					{
						continue;
					}
					$name = $c[$key];
					$url = Route::url($this->profile->link() . '&active=' . $key);
					$cls = ($this->active == $key) ? 'active' : '';
					$tab_name = ($this->active == $key) ? $name : $tab_name;

					$metadata = $this->sections[$k]['metadata'];
					$meta_count = (isset($metadata['count']) && $metadata['count'] != "") ? $metadata['count'] : "";
					if (isset($metadata['alert']) && $metadata['alert'] != "")
					{
						$meta_alert = $metadata['alert'];
						$cls .= ' with-alert';
					}
					else
					{
						$meta_alert = '';
					}

					if (!isset($c['icon']))
					{
						$c['icon'] = 'f009';
					}
					?>
					<li class="<?php echo $cls; ?>">
						<a class="<?php echo $key; ?>" data-icon="<?php echo '&#x' . $c['icon']; ?>" title="<?php echo $prefix.' '.$name; ?>" href="<?php echo $url; ?>">
							<?php echo $name; ?>
						</a>
						<span class="meta">
							<?php if ($meta_count) : ?>
								<span class="count"><?php echo $meta_count; ?></span>
							<?php endif; ?>
						</span>
						<?php echo $meta_alert; ?>
						<?php if (isset($metadata['options']) && is_array($metadata['options'])) : ?>
							<ul class="tab-options">
								<?php
								foreach ($metadata['options'] as $option)
								{
									if (!isset($option['text']))
									{
										if (!isset($option['title']))
										{
											continue;
										}
										$option['text'] = $option['title'];
									}

									$attribs = array();
									foreach ($option as $key => $val)
									{
										if ($key == 'text') continue;

										$attribs[] = $key . '="' . $this->escape($val) . '"';
									}

									echo '<li><a ' . implode(' ', $attribs) . '>' . $this->escape($option['text']) . '</a></li>';
								}
								?>
							</ul>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul><!-- /#page_menu -->

			<?php
			$thumb = substr(PATH_APP, strlen(PATH_ROOT)) . '/site/stats/contributor_impact/impact_' . $this->profile->get('uidNumber') . '_th.gif';
			$full = substr(PATH_APP, strlen(PATH_ROOT)) . '/site/stats/contributor_impact/impact_' . $this->profile->get('uidNumber') . '.gif';
			?>
			<?php if (file_exists(PATH_ROOT . $thumb)) : ?>
				<a id="member-stats-graph" rel="lightbox" title="<?php echo Lang::txt('COM_MEMBERS_MEMBER_IMPACT', $this->profile->get('name')); ?>" data-name="<?php echo $this->profile->get('name'); ?>" data-type="Impact Graph" href="<?php echo $full; ?>">
					<img src="<?php echo $thumb; ?>" alt="<?php echo Lang::txt('COM_MEMBERS_MEMBER_IMPACT', $this->profile->get('name')); ?>" />
				</a>
			<?php endif; ?>

		</div><!-- /#page_sidebar -->
		<div id="page_main">
			<div class="head">
				<div id="page_notifications">
					<?php
					if ($this->getError())
					{
						echo '<p class="error">' . implode('<br />', $this->getErrors()) . '</p>';
					}
					?>
				</div>
			</div>

			<div id="page_content" class="member_<?php echo $this->active; ?>">
				<?php if ($edit || $password) : ?>
					<ul id="page_options">
						<?php if ($edit) : ?>
							<li>
								<a class="edit tooltips" id="edit-profile" title="<?php echo Lang::txt('COM_MEMBERS_EDIT_PROFILE'); ?> :: Edit <?php if ($this->profile->get('uidNumber') == User::get("id")) { echo "my"; } else { echo $this->profile->get("name") . "'s"; } ?> profile." href="<?php echo Route::url($this->profile->link() . '&task=edit'); ?>">
									<?php echo Lang::txt('COM_MEMBERS_EDIT_PROFILE'); ?>
								</a>
							</li>
						<?php endif; ?>
						<?php if ($password) : ?>
							<li>
								<a class="password tooltips" id="change-password" title="<?php echo Lang::txt('COM_MEMBERS_CHANGE_PASSWORD'); ?> :: <?php echo Lang::txt('Change your password'); ?>" href="<?php echo Route::url($this->profile->link('changepassword')); ?>">
									<?php echo Lang::txt('COM_MEMBERS_CHANGE_PASSWORD'); ?>
								</a>
							</li>
						<?php endif; ?>
					</ul>
				<?php endif; ?>

				<?php
				}
				if (isset($this->overwrite_content) && $this->overwrite_content)
				{
					echo $this->overwrite_content;
				}
				else
				{
					foreach ($this->sections as $s)
					{
						if ($s['html'] != '')
						{
							echo $s['html'];
						}
					}
				}
				if (!$no_html) {
				?>
			</div><!-- /#page_content -->
		</div><!-- /#page_main -->
	</div> <!-- //#page_container -->
</div><!-- /.innerwrap -->
<?php } ?>

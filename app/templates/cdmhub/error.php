<?php
defined('_HZEXEC_') or die();

$this->template = 'cdmhub';

Lang::load('tpl_' . $this->template) ||
Lang::load('tpl_' . $this->template, __DIR__);

$browser = new \Hubzero\Browser\Detector();
$b = $browser->name();
$v = $browser->major();
?>
<!DOCTYPE html>
<!--[if lt IE 7 ]> <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="ie6"> <![endif]-->
<!--[if IE 7 ]>    <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="ie7"> <![endif]-->
<!--[if IE 8 ]>    <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="ie8"> <![endif]-->
<!--[if IE 9 ]>    <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html dir="<?php echo $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="<?php echo $b . ' ' . $b . $v; ?>"> <!--<![endif]-->
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />

		<title><?php echo Config::get('sitename') . ' - ' . (in_array($this->error->getCode(), array(404, 403, 500)) ? $this->error->getCode() : 500); ?></title>

		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo \Hubzero\Document\Assets::getSystemStylesheet(array('fontcons', 'reset', 'columns', 'notifications')); /* reset MUST come before all others except fontcons */ ?>" />
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/main.css" />
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/error.css" />
<?php if (Config::get('application_env', 'production') != 'production') { ?>
		<link rel="stylesheet" type="text/css" media="screen" href="/modules/mod_application_env/mod_application_env.css" />
<?php } ?>
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/html/mod_reportproblems/mod_reportproblems.css" />
		<link rel="stylesheet" type="text/css" media="print" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/print.css" />
<?php if (Plugin::isEnabled('system', 'debug')) { ?>
		<link rel="stylesheet" type="text/css" media="screen" href="/media/cms/css/debug.css" />
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/debug.css" />
<?php } ?>

		<script type="text/javascript" src="<?php echo $this->baseurl; ?>/core/assets/js/jquery.js"></script>
		<script type="text/javascript" src="<?php echo $this->baseurl; ?>/core/assets/js/jquery.ui.js"></script>
		<script type="text/javascript" src="<?php echo $this->baseurl; ?>/core/assets/js/jquery.fancybox.js"></script>
		<script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/hub.jquery.js"></script>
		<script type="text/javascript" src="/core/modules/mod_reportproblems/mod_reportproblems.js"></script>
		<!--[if IE 9]>
			<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/browser/ie9.css" />
		<![endif]-->
		<!--[if IE 8]>
			<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/browser/ie8.css" />
		<![endif]-->
		<!--[if IE 7]>
			<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/browser/ie7.css" />
		<![endif]-->
	</head>
	<body>
		<?php \Hubzero\Module\Helper::displayModules('notices'); ?>
		<?php \Hubzero\Module\Helper::displayModules('helppane'); ?>

		<div id="top">
			<div id="masthead">
				<div class="inner">
					<a class="menu-logo small-logo" href="<?php echo Request::base(); ?>" title="<?php echo Config::get('sitename'); ?>">
						<img src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/images/logos/cdmHUBlogo.png" alt="<?php echo Config::get('sitename'); ?>" />
					</a>

					<div id="account" role="navigation">
					<?php if (!User::get('guest')) {
							$profile = \Hubzero\User\Profile::getInstance(User::get('id'));
					?>
						<ul class="menu <?php echo (!User::get('guest')) ? 'loggedin' : 'loggedout'; ?>">
							<li>
								<div id="account-info">
									<img src="<?php echo $profile->getPicture(); ?>" alt="<?php echo User::get('name'); ?>" width="30" height="30" />
									<a class="account-details" href="<?php echo Route::url('index.php?option=com_members&id=' . User::get('id')); ?>">
										<?php echo stripslashes(User::get('name')); ?>
										<span class="account-email"><?php echo User::get('email'); ?></span>
									</a>
								</div>
								<ul>
									<li id="account-dashboard">
										<a href="<?php echo Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=dashboard'); ?>"><span><?php echo Lang::txt('TPL_HUBBASIC_ACCOUNT_DASHBOARD'); ?></span></a>
									</li>
									<li id="account-profile">
										<a href="<?php echo Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=profile'); ?>"><span><?php echo Lang::txt('TPL_HUBBASIC_ACCOUNT_PROFILE'); ?></span></a>
									</li>
									<li id="account-messages">
										<a href="<?php echo Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=messages'); ?>"><span><?php echo Lang::txt('TPL_HUBBASIC_ACCOUNT_MESSAGES'); ?></span></a>
									</li>
									<li id="account-logout">
										<a href="<?php echo Route::url('index.php?option=com_logout'); ?>"><span><?php echo Lang::txt('TPL_HUBBASIC_LOGOUT'); ?></span></a>
									</li>
								</ul>
							</li>
						</ul>
					<?php } else { ?>
						<ul class="menu <?php echo (!User::get('guest')) ? 'loggedin' : 'loggedout'; ?>">
							<li id="account-login">
								<a href="<?php echo Route::url('index.php?option=com_users&view=login'); ?>" title="<?php echo Lang::txt('TPL_HUBBASIC_LOGIN'); ?>"><?php echo Lang::txt('TPL_HUBBASIC_LOGIN'); ?></a>
							</li>
							<li id="account-register">
								<a href="<?php echo Route::url('index.php?option=com_register'); ?>" title="<?php echo Lang::txt('TPL_HUBBASIC_SIGN_UP'); ?>"><?php echo Lang::txt('TPL_HUBBASIC_REGISTER'); ?></a>
							</li>
						</ul>
						<?php /* <jdoc:include type="modules" name="account" /> */ ?>
					<?php } ?>
					</div><!-- / #account -->

					<div id="nav" role="main navigation">
						<a name="nav"></a>
						<?php \Hubzero\Module\Helper::displayModules('user3'); ?>
					</div><!-- / #nav -->
				</div><!-- / .inner -->
			</div><!-- / #masthead -->
		</div><!-- / #top -->

	<div id="top-spacer"></div>

			<div id="sub-masthead">
				<div class="inner">
				<?php if (\Hubzero\Module\Helper::countModules('helppane')) : ?>
					<p id="tab">
						<a href="<?php echo Route::url('index.php?option=com_support'); ?>" title="<?php echo Lang::txt('TPL_HUBBASIC_NEED_HELP'); ?>">
							<span><?php echo Lang::txt('TPL_HUBBASIC_HELP'); ?></span>
						</a>
					</p>
				<?php endif; ?>
					<?php \Hubzero\Module\Helper::displayModules('search'); ?>
					<div id="trail">
						<!-- <span class="pathway"><?php echo Lang::txt('TPL_HUBBASIC_TAGLINE'); ?></span> -->
					</div><!-- / #trail -->
				</div><!-- / .inner -->
			</div><!-- / #sub-masthead -->

		<div id="wrap">
			<div id="content" class="<?php echo Request::getCmd('option', ''); ?> <?php echo 'code' . $this->error->getCode(); ?>" role="main">
				<div class="inner">
					<a name="content" id="content-anchor"></a>

					<div class="main section">
						<div class="two columns first">
							<div id="errormessage">
								<h2 class="error-code">
									<?php echo (in_array($this->error->getCode(), array(404, 403, 500))) ? $this->error->getCode() : 500; ?>
								</h2>
							</div><!-- / #errormessage -->
						</div><!-- / .two columns first -->
						<div class="two columns second">
							<div id="errorbox">
								<div class="wrap">
								<?php
								switch ($this->error->getCode())
								{
									case 404: ?>
									<h3><?php echo Lang::txt('Page Not Found'); ?></h3>
									<blockquote>
										<p><?php echo Lang::txt("The page you were looking for is not here."); ?></p>
										<p><?php echo Lang::txt('You can try starting over from the <a href="/home">Home Page</a> and avoid the wrong turn you took, or you can <a href="/search">search</a> for it and go straight there.  If you are sure that the page is actually missing, please <a href="/support/tickets/ticket/new"?>file a ticket</a> to mobilize the team for a deeper inquiry. '); ?></p>
									</blockquote>
									<p class="signature">&mdash;The cdmHUB Team</p>
									<?php
									break;
									case 403: ?>
									<h3><?php echo Lang::txt('Access Denied!'); ?></h3>

									<p class="error"><?php echo $this->error->getMessage(); ?></p>
									<p class="signature">&mdash;The cdmHUB Team</p>
									<?php
									break;
									case 500:
									default: ?>
									<h3><?php echo Lang::txt('Internal Error'); ?></h3>
									<blockquote>
										<p><?php echo Lang::txt('It looks like something has broken behind the scenes, making this area unavailable right now.  Please try back later, as it may be up and running by then.  If the area is not available within a couple days, please <a href="/support/tickets/ticket/new"?>file a ticket</a> to mobilize the team for a deeper inquiry.'); ?></p>
									</blockquote>
									<p class="signature">&mdash;The cdmHUB Team</p>
									<?php
									break;
								} ?>
								</div><!-- / .wrap -->
							</div><!-- / #errorbox -->
						</div><!-- / .two columns second -->
						<div class="clear"></div>
					<?php if ($this->debug) { ?>
						<p class="error">
							<?php echo $this->error->getMessage(); ?>
						</p>
					<?php } ?>
					</div><!-- / .main section -->

				<?php if ($this->debug) { ?>
					<div id="techinfo">
						<?php echo $this->renderBacktrace(); ?>
					</div><!-- / #techinfo -->
				<?php } ?>
				</div><!-- / .inner -->
			</div><!-- / #content -->

			<div id="footer">
				<a name="footer" id="footer-anchor"></a>
				<?php \Hubzero\Module\Helper::displayModules('footer'); ?>
			</div><!-- / #footer -->
		</div><!-- / #wrap -->

		<?php \Hubzero\Module\Helper::displayModules('endpage'); ?>
	</body>
</html>

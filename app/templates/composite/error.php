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

// No Direct Access
defined('_HZEXEC_') or die();

// Get browser info to set some classes
$browser = new \Hubzero\Browser\Detector();
$cls = array(
	'no-js',
	$browser->name(),
	$browser->name() . $browser->major(),
	$this->direction
);

$code = (is_numeric($this->error->getCode()) && $this->error->getCode() > 100 ? $this->error->getCode() : 500);

Lang::load('tpl_' . $this->template) ||
Lang::load('tpl_' . $this->template, __DIR__);
?>
<!DOCTYPE html>
<html dir="<?php echo $this->direction; ?>" lang="<?php echo $this->language; ?>" class="<?php echo implode(' ', $cls); ?>">
	<head>
		<meta name="viewport" content="width=device-width" />
		<title><?php echo Config::get('sitename') . ' - ' . $code; ?></title>

		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo \Hubzero\Document\Assets::getSystemStylesheet(); ?>" />
		<script type="text/javascript" src="<?php echo \Html::asset('script', 'jquery.js', false, true, true); ?>"></script>
		<script type="text/javascript" src="<?php echo \Html::asset('script', 'jquery.ui.js', false, true, true); ?>"></script>
		<script type="text/javascript" src="<?php echo \Html::asset('script', 'jquery.fancybox.js', false, true, true); ?>"></script>
		<script type="text/javascript" src="<?php echo str_replace('/core', '', $this->baseurl); ?>/templates/<?php echo $this->template; ?>/js/hub.js"></script>

		<!--[if lt IE 9]><script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/html5.js"></script><![endif]-->
	</head>
	<body>
		<div class="hub-wrap">
			<section class="hub-top">
				<?php echo Module::position('notices'); ?>
			</section>

			<main>

				<div class="pageTop"></div>
				<!-- Header -->
				<header class="main on-top">
					<div class="wrap cf">
						<div class="inner">
							<div class="hub-brand">
								<nav class="hub-logo nanohub-logo">
									<a href="<?php echo Request::root(); ?>" title="<?php echo Config::get('sitename'); ?>">
										<?php echo file_get_contents(__DIR__ . '/images/layout/cdmhub-logo.svg'); ?>
									</a>
								</nav>
							</div>

							<div class="all-nav">
								<div class="all-nav-aux subnav">
									<div class="search-container">
										<span class="icon"><?php echo file_get_contents(__DIR__ . '/images/layout/search.svg'); ?></span>
										<?php echo Module::position('search'); ?>
									</div>

									<ul>
										<li class="user-account loggedin" id="account">
											<?php if (!User::isGuest()) { ?>
												<?php
												$profile = \Hubzero\User\Profile::getInstance(User::get('id'));
												$pic = $profile->getPicture();
												if ($pic == '/core/components/com_members/site/assets/img/profile_thumb.gif')
												{
													// no picture
													$pic = false;
												}
												?>
												<a class="user-account-link loggedin" href="<?php echo Route::url('index.php?option=com_members&id=' . User::get('id')); ?>">
													Logged in
												</a>
												<div class="account-details">
													<div class="user-info">
														<a href="<?php echo Route::url('index.php?option=com_members&id=' . User::get('id')); ?>" class="cf">
																		<span class="user-image">
																			<img src="<?php echo $profile->getPicture(); ?>" alt="<?php echo User::get('name'); ?>" />
																		</span>

															<p>
																<span class="account-name"><?php echo stripslashes(User::get('name')) . ' (' . stripslashes(User::get('username')) . ')'; ?></span>
																<span class="account-email"><?php echo User::get('email'); ?></span>
															</p>
														</a>
													</div>
													<ul>
														<li id="account-dashboard">
															<a href="<?php echo Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=dashboard'); ?>"><span><?php echo Lang::txt('TPL_ACCOUNT_DASHBOARD'); ?></span></a>
														</li>
														<li id="account-profile">
															<a href="<?php echo Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=profile'); ?>"><span><?php echo Lang::txt('TPL_ACCOUNT_PROFILE'); ?></span></a>
														</li>
														<li id="account-logout">
															<a href="<?php echo Route::url('index.php?option=com_users&view=logout'); ?>"><span><?php echo Lang::txt('TPL_LOGOUT'); ?></span></a>
														</li>
													</ul>
												</div>
											<?php } else { ?>
												<a href="<?php echo Route::url('index.php?option=com_users&view=login'); ?>" title="<?php echo Lang::txt('TPL_LOGIN'); ?>" class="user-account-link loggedout"><?php echo Lang::txt('Login'); ?></a>
											<?php } ?>
										</li>

										<li class="subnav-search"><a href="/search">Search</a></li>

										<?php if (false && $this->countModules('helppane')) : ?>
											<!-- set module REPORTPROBLEMS parameter to have it work with .helpme -->
											<div class="subnav-helpme helpme">
												<a href="<?php echo Route::url('index.php?option=com_support'); ?>" title="<?php echo Lang::txt('Help'); ?>">
													<span><?php echo Lang::txt('Help'); ?></span>
												</a>
											</div>
										<?php endif; ?>
								</div>

								<div class="all-nav-main site-navigation">
									<div class="cf">
										<nav class="main cf" role="menu">
											<?php echo Module::position('user3'); ?>
										</nav>
									</div>
								</div>
							</div>

							<a class="menu-button" href="#"><button class="icon"><span></span></button><span>Menu</span></a>
						</div>
					</div>
				</header>

				<div class="trail">
					<span class="breadcrumbs pathway"><a href="/" class="pathway">Home</a> <span class="sep">/</span> <span>Error</span></span>
				</div>

				<div id="content" class="<?php echo Request::getVar('option', ''); ?>" role="main">
					<div class="inner">
						<div class="content">
							<div id="content-header">
								<h2>Error (<?php echo $code; ?>)</h2>
							</div>

							<section class="main section">
								<div class="section-inner">

									<p class="error"><?php
									if ($this->debug)
									{
										$message = $this->error->getMessage();
									}
									else
									{
										switch ($this->error->getCode())
										{
											case 404:
												$message = Lang::txt('TPL_404_HEADER');
												break;
											case 403:
												$message = Lang::txt('TPL_403_HEADER');
												break;
											case 500:
											default:
												$message = Lang::txt('TPL_500_HEADER');
												break;
										}
									}
									echo $message;
									?></p>
								</div>
							</section>
						</div><!-- / .content -->
					</div>
				</div>

				<footer>
					<section class="main cf">
						<ul class="legal">
							<li><a href="/about/accessibility">Accessibility Issues</a></li>
							<li><a href="/about">About Us</a></li>
							<li><a href="/legal/terms">Terms of use</a></li>
							<li><a href="/about/contact">Contact us</a></li>
							<li><a href="/legal/licensing">Licensing Content</a></li>
						</ul>

						<div class="copy">
							<p>Copyright &copy; <?php echo date('Y'); ?> cdmHUB</p>
						</div>
					</section>
				</footer>

			</main>
			<?php if ($this->debug) { ?>
				<div class="backtrace-wrap">
					<?php echo $this->renderBacktrace(); ?>
				</div>
			<?php } ?>
		</div>

		<div class="hub-overlay"></div>
		<div id="big-search">
			<div class="inner">
				<?php echo Module::position('search'); ?>
			</div>
			<button class="close">
				<span>close search</span>
			</button>
		</div>

		<?php echo Module::position('endpage'); ?>
	</body>
</html>
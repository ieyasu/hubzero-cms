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

$menu = App::get('menu');
Html::behavior('framework', true);
Html::behavior('modal');

$this->addScript($this->baseurl . '/templates/' . $this->template . '/js/hub.js?v=' . filemtime(__DIR__ . '/js/hub.js'));

$menu = App::get('menu');
$isFrontPage = ($menu->getActive() == $menu->getDefault());

// Index page files only
if ($isFrontPage)
{
	$this->addScript($this->baseurl . '/templates/' . $this->template . '/js/vendor/home.js');
	//$this->addScript($this->baseurl . '/templates/' . $this->template . '/js/vendor/slick.js');
	//$this->addScript($this->baseurl . '/templates/' . $this->template . '/js/vendor/greensock/TweenMax.min.js');
	//$this->addScript($this->baseurl . '/templates/' . $this->template . '/js/vendor/jquery.flexslider-min.js');
	//$this->addScript($this->baseurl . '/templates/' . $this->template . '/js/vendor/ScrollMagic.min.js');
	//$this->addScript($this->baseurl . '/templates/' . $this->template . '/js/vendor/animation.gsap.min.js');
	$this->addScript($this->baseurl . '/templates/' . $this->template . '/js/home.js?v=' . filemtime(__DIR__ . '/js/home.js'));
}
else {
	$this->addScript($this->baseurl . '/templates/' . $this->template . '/js/vendor/greensock/TweenMax.min.js');
}

$browser = new \Hubzero\Browser\Detector();
$cls = array(
	$this->direction,
	$browser->name(),
	$browser->name() . $browser->major()
);

$user = User::getInstance();

$this->setTitle(Config::get('sitename') . ' - ' . $this->getTitle());
?>
<!DOCTYPE html>
<html dir="<?php echo $this->direction; ?>" lang="<?php echo $this->language; ?>" class="<?php echo implode(' ', $cls); ?>">
	<head>
		<meta name="viewport" content="width=device-width" />
		<!-- <meta http-equiv="X-UA-Compatible" content="IE=edge" /> Doesn't validate... -->

		<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
		<link rel="icon" type="image/png" href="/favicon-32x32.png" sizes="32x32">
		<link rel="icon" type="image/png" href="/favicon-16x16.png" sizes="16x16">
		<link rel="manifest" href="/manifest.json">
		<link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
		<meta name="theme-color" content="#ffffff">

		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo \Hubzero\Document\Assets::getSystemStylesheet(); ?>" />

		<jdoc:include type="head" />

		<!--[if lt IE 9]><script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/html5.js"></script><![endif]-->

		<!--[if IE 10]><link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/browser/ie10.css" /><![endif]-->
		<!--[if IE 9]><link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/browser/ie9.css" /><![endif]-->
		<!--[if IE 8]><link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/browser/ie8.css" /><![endif]-->
		<!--[if IE 7]><link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/browser/ie7.css" /><![endif]-->
	</head>
	<body>
		<div class="hub-wrap">
			<section class="hub-top">
				<jdoc:include type="modules" name="notices" />
				<jdoc:include type="modules" name="helppane" />

				<?php if ($this->getBuffer('message')) : ?>
					<jdoc:include type="message" />
				<?php endif; ?>
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
										<jdoc:include type="modules" name="search" />
									</div>

									<ul>
									<?php if (!User::isGuest()) { ?>
										<li class="user-account loggedin" id="account">
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
												Logged in (<?php echo stripslashes(User::get('username')); ?>)
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
										</li>
									<?php } else { ?>
										<li>
											<a href="<?php echo Route::url('index.php?option=com_users&view=login'); ?>" title="<?php echo Lang::txt('TPL_LOGIN'); ?>" class="user-account-link loggedout"><?php echo Lang::txt('TPL_LOGIN'); ?></a>
										</li>
										<li>
											<a href="<?php echo Route::url('index.php?option=com_members&view=register'); ?>" title="<?php echo Lang::txt('TPL_SIGNUP'); ?>" class="user-account-link"><?php echo Lang::txt('TPL_SIGNUP'); ?></a>
										</li>
									<?php } ?>
									<?php if ($this->countModules('helppane')) : ?>
										<li class="subnav-helpme helpme">
											<a href="<?php echo Route::url('index.php?option=com_support'); ?>" title="<?php echo Lang::txt('Help'); ?>"><!-- set module REPORTPROBLEMS parameter to have it work with .helpme -->
												<span><?php echo Lang::txt('Help'); ?></span>
											</a>
										</li>
									<?php endif; ?>

										<li class="subnav-search"><a href="/search">Search</a></li>
									</ul>

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
											<jdoc:include type="modules" name="user3" />
										</nav>
									</div>
								</div>
							</div>

							<a class="menu-button" href="#"><button class="icon"><span></span></button><span>Menu</span></a>
						</div>
					</div>
				</header>
				<!-- --------------------------- header ends here ------------------------ -->

				<?php if ($menu->getActive() != $menu->getDefault()) : ?>

				<div class="trail">
						<jdoc:include type="modules" name="breadcrumbs" />
				</div><!-- / #trail -->

				<?php endif; ?>

				<div id="content" class="<?php echo Request::getCmd('option', ''); ?>" role="main">
					<div class="inner">
						<div class="content<?php if ($this->countModules('left or right')) { echo ' withmenu'; } ?>">
							<?php if ($this->countModules('left or right')) : ?>
							<section class="main section">
							<?php endif; ?>

								<?php if ($this->countModules('left')) : ?>
								<aside class="aside">
									<jdoc:include type="modules" name="left" />
								</aside><!-- / .aside -->
								<?php endif; ?>

								<?php if ($this->countModules('left or right')) : ?>
								<div class="subject">
								<?php endif; ?>

									<!-- start component output -->

									<?php
									if ($isFrontPage)
									{
									?>

										<div class="hero flexslider">
											<ul class="slides">
												<!--li class="hero-dna" data-slide="huge">
													<div class="content">
														<div class="head">
															<div class="slogan">
																<div
																	class="icon"><?php echo file_get_contents(dirname(__FILE__) . '/images/home/nano-is-huge.svg'); ?></div>
															</div>
															<p>largest nanotechnology online resource</p>
														</div>
														<div class="stats">
															<div class="stat tools">
																<span class="value">400</span>
																<span>simulation tools</span>
															</div>
															<div class="stat users">
																<span class="value">1.4M</span>
																<span>users</span>
															</div>
															<div class="stat resources">
																<span class="value">4500</span>
																<span>resources</span>
															</div>
														</div>
													</div>
												</li-->
												<li class="billboard hero-intro">
													<a href="/about">
														<div class="content">
															<div class="title">
																<h2>COMPOSITES ARE THE FUTURE</h2>
															</div>
															<div class="details">
																<p>Convening The Composites Community</p>
															</div>
														</div>
													</a>
												</li>
												<li class="billboard hero-simulate">
													<a href="/resources/tools">
														<div class="content">
															<div class="title">
																<h2>Simulate</h2>
															</div>
															<div class="details">
																<p>Composites APPs and Commercial Tools</p>
															</div>
														</div>
													</a>
												</li>
												<li class="billboard hero-learn">
													<a href="/resources">
														<div class="content">
															<div class="title">
																<h2>Learn</h2>
															</div>
															<div class="details">
																<p>A wide array of resources at your fingertips</p>
															</div>
														</div>
													</a>
												</li>
												<li class="billboard hero-explore">
													<a href="/community">
														<div class="content">
															<div class="title">
																<h2>COLLABORATE</h2>
															</div>
															<div class="details">
																<p>Interact with the community</p>
															</div>
														</div>
													</a>
												</li>
											</ul>
											<div class="loader-overlay">
												<div class="loader-wrap">
													<div class="loader">
														<svg class="circular" viewBox="25 25 50 50">
															<circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/>
														</svg>
													</div>
												</div>
											</div>
										</div>

										<?php

										if (!User::get('guest'))
										{
											//include('index-logged-in.php');
											include('index-logged-out.php');
										}
										else {
											include('index-logged-out.php');
										}
										?>

										<!--section class="home community">
											<div class="heading">
												<h2 class="smaller">Community</h2>
												<p>our partners help us make a difference in science</p>
											</div>

											<div class="featured">
												<div class="s-contain cf">
													<div class="partner needs">
														<div class="content">
															<a href="/groups/needs"><img src="/app/templates/composite/images/home/partners/needs-logo.svg"></a>
														</div>
														<div class="image"></div>
													</div>

													<div class="partner nanofbio">
														<div class="content">
															<a href="/groups/nanobio"><img src="/app/templates/composite/images/home/partners/nanobio-logo.svg"></a>
														</div>
														<div class="image"></div>
													</div>
												</div>
											</div>

											<div class="partners s-contain-wide">
												<ul>
													<li><a href="/groups/ncn"><img src="/app/templates/composite/images/home/partners/ncn-logo.svg"></a></li>
													<li><a href="/groups/bnc"><img src="/app/templates/composite/images/home/partners/discovery-park-logo.svg"></a></li>
													<li><a href="/groups/quest"><img src="/app/templates/composite/images/home/partners/quest-logo.svg"></a></li>
													<li><a href="/groups/pv"><img src="/app/templates/composite/images/home/partners/pvhub-logo.svg"></a></li>
													<li><a href="/groups/npt"><img src="/app/templates/composite/images/home/partners/npt-logo.svg"></a></li>
													<li><a href="/groups/gng"><img src="/app/templates/composite/images/home/partners/gng-logo.svg"></a></li>
												</ul>
											</div>
										</section-->

										<?php

									}
									?>

									<?php
									if (!$isFrontPage) {
									?>

									<jdoc:include type="component" />

									<?php
									}
									?>

									<!-- end component output -->

								<?php if ($this->countModules('left or right')) : ?>
								</div><!-- / .subject -->
								<?php endif; ?>

								<?php if ($this->countModules('right')) : ?>
									<aside class="aside">
										<jdoc:include type="modules" name="right" />
									</aside><!-- / .aside -->
								<?php endif; ?>

								<?php if ($this->countModules('left or right')) : ?>
								</section><!-- / .main section -->
								<?php endif; ?>
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
							<p>Copyright © <?php echo date('Y'); ?> cdmHUB</p>
						</div>
					</section>
				</footer>

			</main>

		</div>
		<div class="hub-overlay"></div>
		<div id="big-search">
			<div class="inner">
				<jdoc:include type="modules" name="search" />
			</div>
			<button class="close">
				<span>close search</span>
			</button>
		</div>

		<jdoc:include type="modules" name="endpage" />
	</body>
</html>
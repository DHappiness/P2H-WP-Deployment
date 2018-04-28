<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('UTC');
ignore_user_abort( true );
set_time_limit( 0 );


// General Configuration
$deployment_settings = array(
  'svn_username' => '', /* required */
  'svn_password' => '', /* required */
  'install_plugins' => array( 'acf' ),
  'deployment_version' => '1.2', /* Do not change */
  'delete_after_execution' => false,
);

// References to plugins' repositories ( except ACF )
$plugins_url = 'https://downloads.wordpress.org/plugin/';
$plugins = array(
	'contact-form-7/wp-contact-form-7.php' => array(
		'label' => 'Contact Form 7',
		'url' => $plugins_url . 'contact-form-7.latest-stable.zip'
	),
	'yet-another-related-posts-plugin/yarpp.php' => array(
		'label' => 'Yet Another Related Posts Plugin (YARPP)',
		'url' => $plugins_url . 'yet-another-related-posts-plugin.latest-stable.zip'
	),
	'woocommerce/woocommerce.php' => array(
		'label' => 'WooCommerce',
		'url' => $plugins_url . 'woocommerce.latest-stable.zip'
	),
	'wordpress-popular-posts/wordpress-popular-posts.php' => array(
		'label' => 'Wordpress Popular Posts',
		'url' => $plugins_url . 'wordpress-popular-posts.latest-stable.zip'
	),
	'breadcrumb-navxt/breadcrumb-navxt.php' => array(
		'label' => 'Breadcrumb NavXT',
		'url' => $plugins_url . 'breadcrumb-navxt.latest-stable.zip'
	),
	'the-events-calendar/the-events-calendar.php' => array(
		'label' => 'The Events Calendar',
		'url' => $plugins_url . 'the-events-calendar.latest-stable.zip'
	),
	'guideme/guideme.php' => array(
		'label' => 'GuideMe',
		'url' => $plugins_url . 'guideme.latest-stable.zip'
	),
	'xhanch-my-twitter/xhanch-my-twitter.php' => array(
		'label' => 'Xhanch – My Twitter',
		'url' => $plugins_url . 'xhanch-my-twitter.latest-stable.zip'
	),
	'instagram-feed/instagram-feed.php' => array(
		'label' => 'Instagram Feed',
		'url' => $plugins_url . 'instagram-feed.latest-stable.zip'
	),
	'wp-postviews/wp-postviews.php' => array(
		'label' => 'WP-PostViews',
		'url' => $plugins_url . 'wp-postviews.latest-stable.zip'
	),
	'bbpress/bbpress.php' => array(
		'label' => 'bbPress',
		'url' => $plugins_url . 'bbpress.latest-stable.zip'
	),
	'disqus-comment-system/disqus.php' => array(
		'label' => 'Disqus Comment System',
		'url' => $plugins_url . 'disqus-comment-system.latest-stable.zip'
	),
	'mailchimp-for-wp/mailchimp-for-wp.php' => array(
		'label' => 'MailChimp for WordPress',
		'url' => $plugins_url . 'mailchimp-for-wp.latest-stable.zip'
	),
	'wysija-newsletters/index.php' => array(
		'label' => 'MailPoet 2',
		'url' => $plugins_url . 'wysija-newsletters.latest-stable.zip'
	),
	'mailpoet/mailpoet.php' => array(
		'label' => 'MailPoet 3',
		'url' => $plugins_url . 'mailpoet.latest-stable.zip'
	),
	'dynamic-widgets/dynamic-widgets.php' => array(
		'label' => 'Dynamic Widgets',
		'url' => $plugins_url . 'dynamic-widgets.latest-stable.zip'
	),
	'widget-logic/widget-logic.php' => array(
		'label' => 'Widget Logic',
		'url' => $plugins_url . 'widget-logic.latest-stable.zip'
	),
	'all-in-one-seo-pack/all_in_one_seo_pack.php' => array(
		'label' => 'All in One SEO Pack',
		'url' => $plugins_url . 'all-in-one-seo-pack.latest-stable.zip'
	),
	'wordpress-seo/wp-seo.php' => array(
		'label' => 'Yoast SEO',
		'url' => $plugins_url . 'wordpress-seo.latest-stable.zip'
	),
	'duplicate-post/duplicate-post.php' => array(
		'label' => 'Duplicate Post',
		'url' => $plugins_url . 'duplicate-post.latest-stable.zip'
	),
	'force-regenerate-thumbnails/force-regenerate-thumbnails.php' => array(
		'label' => 'Force Regenerate Thumbnails',
		'url' => $plugins_url . 'force-regenerate-thumbnails.latest-stable.zip'
	),
	'svg-support/svg-support.php' => array(
		'label' => 'SVG Support',
		'url' => $plugins_url . 'svg-support.latest-stable.zip'
	),
	'theme-check/theme-check.php' => array(
		'label' => 'Theme Check',
		'url' => $plugins_url . 'theme-check.latest-stable.zip'
	),
	'sharethis-share-buttons/sharethis-share-buttons.php' => array(
		'label' => 'ShareThis',
		'url' => $plugins_url . 'sharethis-share-buttons.latest-stable.zip'
	),
);

<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
set_time_limit( 3600 );


// General Configuration
$deployment_settings = array(
  'svn_username' => 'dyakimchuk', /* required */
  'svn_password' => 'MpCfhqGg2r', /* required */
  'install_plugins' => array( 'acf' ),
  'deployment_version' => '1.0', /* Don not change */
  'delete_after_execution' => true,
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
	
);

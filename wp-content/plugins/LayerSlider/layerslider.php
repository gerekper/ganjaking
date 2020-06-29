<?php

/*
        CHECK THE "QUICK START GUIDE.HTML" FILE LOCATED
        IN THIS DIRECTORY FOR INSTALLATION INSTRUCTIONS
        AND OTHER HELPFUL RESOURCES.
*/



/*
    Plugin Name:  LayerSlider WP
     Plugin URI:  https://layerslider.kreaturamedia.com
        Version:  6.11.1

    Description:  LayerSlider is a premium multi-purpose content creation and animation platform. Easily create sliders, image galleries, slideshows with mind-blowing effects, popups, landing pages, animated page blocks, or even a full website. LayerSlider empowers millions of active websites on a daily basis with stunning visuals and eye-catching effects.

         Author:  Kreatura Media
     Author URI:  https://kreaturamedia.com

        License:  Kreatura License
    License URI:  https://layerslider.kreaturamedia.com/licensing/

    Text Domain:  LayerSlider
    Domain Path:  /assets/locales
*/


// Prevent direct file access
defined( 'ABSPATH' ) || exit;

define( 'LS_MINIMUM_PHP_VERSION', '5.3' );
define( 'LS_MINIMUM_WP_VERSION',  '3.5' );

$php_version = phpversion();
$wp_version  = get_bloginfo('version');


// Detect duplicate versions of LayerSlider
if( defined('LS_PLUGIN_VERSION') || isset( $GLOBALS['lsPluginPath'] ) ) {
	add_action( 'admin_notices', 'ls_duplicate_version_notice' );

// Check required PHP version
} elseif( version_compare( $php_version, LS_MINIMUM_PHP_VERSION, '<' ) ) {
	add_action( 'admin_notices', 'ls_server_requirements_notice' );

// Check required WordPress version
} elseif( version_compare( $wp_version, LS_MINIMUM_WP_VERSION, '<' ) ) {
	add_action( 'admin_notices', 'ls_wordpress_requirements_notice' );

// Initialize the plugin
} else {

	define( 'LS_ROOT_FILE', __FILE__ );

	define( 'LS_PLUGIN_VERSION', '6.11.1' );
	define( 'LS_DB_VERSION', '6.9.0' );

	require __DIR__.'/assets/init.php';
}


// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

if( ! function_exists( 'ls_duplicate_version_notice' ) ) {
	function ls_duplicate_version_notice() { ?>
		<div class="notice notice-error" style="text-align: justify;">
			<h3>Action Required: Multiple LayerSlider instances detected</h3>
			<p>It looks like you already had one copy of LayerSlider installed on your site. Having multiple copies installed simultaneously can cause serious issues, thus other copies are suppressed until this issue gets resolved. Here’s what you can do:</p>
			<ul class="ul-square">
				<li>Please check your <a href="<?php echo admin_url( 'plugins.php' ) ?>">Plugins screen</a> and disable the older copies of LayerSlider. <b>Remember, you should see at least two copies of LayerSlider and you should disable those beside the one you’ve just installed.</b> Look at their version number to easily identify them. You’ll likely want to disable the ones with a lower version number.</li>
				<li>If the other copies aren’t listed there, it’s almost certain that your active WordPress theme loads LayerSlider as a bundled plugin. In such a case, please check your theme’s settings and find a way to uninstall or disable loading the bundled version of LayerSlider. The process is different for each theme, thus we recommend contacting the appropriate theme author if you experience difficulties.</li>
			</ul>
			<p><small style="font-size: 13px; color: #666;">This message will automatically be dismissed once the issue has been resolved. You can also disable all copies of LayerSlider under the Plugins screen to hide this message. However, we strongly discourage choosing that since you might be stuck with an old and potentially outdated version of LayerSlider or no access to any version at all.</small></p>
		</div>

<?php } }


if( ! function_exists( 'ls_server_requirements_notice' ) ) {
	function ls_server_requirements_notice() { ?>
		<div class="notice notice-error" style="text-align: justify;">
			<h3>Action Required: LayerSlider cannot run on your server with its current settings</h3>
			<p><b>LayerSlider requires PHP <?php echo LS_MINIMUM_PHP_VERSION ?> or greater. Please contact your hosting provider and ask them to upgrade the PHP on your server. WordPress itself has much higher <a target="_blank" href="https://wordpress.org/about/requirements/">requirements</a> with its current releases. Upgrading is necessary to be compatible with the latest releases of WordPress and the overwhelming majority of its themes and plugins. It’s also crucial for security and performance, so be pushy if your host is hesitant. <a href="https://wordpress.org/support/update-php/" target="_blank">Learn more about updating PHP</a></b></p>

			<p><b>Alternatively, if you’ve previously purchased LayerSlider, you can log in to <a href="https://account.kreaturamedia.com/" target="_blank">Your Account</a> and download & install an older release that supported this version of PHP. However, we strongly recommend to use this only as a temporary measure.</b></p>

			<p><small style="font-size: 13px; color: #666;">This message will automatically be dismissed once the issue has been resolved. After that, look for the <b>LayerSlider WP</b> sidebar menu item to get started using the plugin. You can also disable LayerSlider under the Plugins screen to hide this message. However, we strongly discourage choosing to look away as your site will remain in a vulnerable state and you will experience more and more issues with themes and plugins if you don’t take the necessary steps.</small></p>
		</div>

<?php } }


if( ! function_exists( 'ls_wordpress_requirements_notice' ) ) {
	function ls_wordpress_requirements_notice() { ?>
		<div class="notice notice-error" style="text-align: justify;">
			<h3>Action Required: LayerSlider cannot run on this version of WordPress</h3>
			<p><b>LayerSlider requires WordPress <?php echo LS_MINIMUM_WP_VERSION ?> or greater. Please visit <a href="<?php echo admin_url( 'update-core.php' ) ?>">Dashboard → Updates</a> and try to run the updater. If you run into troubles, contact your server hosting provider and ask them to make any changes that may be necessary. Your current WordPress version is reached its End-of-Life, meaning it doesn’t even receive security updates. Updating is strongly recommended.</b></p>

			<p><b>Alternatively, if you’ve previously purchased LayerSlider, you can log in to <a href="https://account.kreaturamedia.com/" target="_blank">Your Account</a> and download & install an older release that supported this version of WordPress. However, we strongly recommend to use this only as a temporary measure.</b></p>

			<p><small style="font-size: 13px; color: #666;">This message will automatically be dismissed once the issue has been resolved. After that, look for the <b>LayerSlider WP</b> sidebar menu item to get started using the plugin. You can also disable LayerSlider under the Plugins screen to hide this message. However, we strongly discourage choosing to look away as your site will remain in a vulnerable state and you will experience more and more issues with themes and plugins if you don’t take the necessary steps.</small></p>
		</div>

<?php } }

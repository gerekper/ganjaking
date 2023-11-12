<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              www.redefiningtheweb.com
 * @since             1.0.0
 * @package           Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai
 *
 * @wordpress-plugin
 * Plugin Name:       WooCommerce Dynamic Pricing & Discounts with AI
 * Plugin URI:        www.redefiningtheweb.com
 * Description:       This plugin is a high functional and comprehensive pricing and discount plugin for Woocommerce stores.
 * Version:           2.6.1
 * Author:            RedefiningTheWeb
 * Author URI:        www.redefiningtheweb.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       rtwwdpd-woo-dynamic-pricing-discounts-with-ai
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'RTWWDPD_WOO_DYNAMIC_PRICING_DISCOUNTS_WITH_AI_VERSION', '2.6.1' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/rtwwdpd-class-woo-dynamic-pricing-discounts-with-ai.php';

$rtwwdpd_update_array = array( 'purchase_code' => '************','status' => true );
update_option( 'rtwbma_verification_done', $rtwwdpd_update_array );

/**
 * Check woocommerce and other required setting to run plugin.
 *
 * @since     1.0.0
 * @return    boolean.
 */
function rtwwdpd_check_run_allows() 
{

	$rtwwdpd_woo_status = true;
	$rtwwdpd_lite_status = true;
	if( function_exists('is_multisite') && is_multisite() )
	{
		include_once(ABSPATH. 'wp-admin/includes/plugin.php');
		if( !is_plugin_active('woocommerce/woocommerce.php') )
		{
			$rtwwdpd_woo_status = false;
		}
	}
	else
	{
		if( !in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins') ) ) )
		{
			$rtwwdpd_woo_status = false;
		}
	}

	$rtwwdpd_check_lite = get_option('rtwwdpdl_setting_priority', array());
	
	if(is_array($rtwwdpd_check_lite) && !empty($rtwwdpd_check_lite))
	{
		if( function_exists('is_multisite') && is_multisite() )
		{
			include_once(ABSPATH. 'wp-admin/includes/plugin.php');
			if( !is_plugin_active('woo-dynamic-pricing-discounts-lite/dynamic-pricing-discounts-lite-for-woocommerce.php') )
			{
				$rtwwdpd_lite_status = false;
			}
		}
		else
		{
			if( !in_array('woo-dynamic-pricing-discounts-lite/dynamic-pricing-discounts-lite-for-woocommerce.php', apply_filters('active_plugins', get_option('active_plugins') ) ) )
			{
				$rtwwdpd_lite_status = false;
			}
		}
	}
	else
	{
		if( !in_array('woo-dynamic-pricing-discounts-lite/dynamic-pricing-discounts-lite-for-woocommerce.php', apply_filters('active_plugins', get_option('active_plugins') ) ) )
		{
			$rtwwdpd_lite_status = false;
		}
	}

	$rtwwdpd_installed_array = array();
	$rtwwdpd_installed_array['woocommerce'] = $rtwwdpd_woo_status;
	$rtwwdpd_installed_array['lite'] = $rtwwdpd_lite_status;

	return $rtwwdpd_installed_array;
}

$rtwwdpd_check = rtwwdpd_check_run_allows();

if( $rtwwdpd_check['woocommerce'] && $rtwwdpd_check['lite'] )
{
	$rtwwdpd_verification_done = get_site_option( 'rtwbma_verification_done', array() );
	/**
	 * Begins execution of the plugin.
	 *
	 * Since everything within the plugin is registered via hooks,
	 * then kicking off the plugin from this point in the file does
	 * not affect the page life cycle.
	 *
	 * @since    1.0.0
	 */
	
	function rtwwdpd_run_woo_dynamic_pricing_discounts_with_ai() {

		$plugin = new Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai();
		$plugin->rtwwdpd_run();

	}
	rtwwdpd_run_woo_dynamic_pricing_discounts_with_ai();
	//Plugin Constant
	if ( !defined( 'RTWWDPD_DIR' ) ) {
		define('RTWWDPD_DIR', plugin_dir_path( __FILE__ ) );
	}
	if ( !defined( 'RTWWDPD_URL' ) ) {
		define('RTWWDPD_URL', plugin_dir_url( __FILE__ ) );
	}
	if ( !defined( 'RTWWDPD_HOME' ) ) {
		define('RTWWDPD_HOME', home_url() );
	}
	$rtwwdpd_verification_done = get_site_option( 'rtwbma_verification_done', array() );
	$rtwwdpd_verified = false;
	if( !empty( $rtwwdpd_verification_done ) && $rtwwdpd_verification_done['status'] == true && !empty($rtwwdpd_verification_done['purchase_code']) )
	{
		$rtwwdpd_verified = true;
	
	}else
	{
		add_action('admin_notices', 'rtwwdpd_error_notice');
		function rtwwdpd_error_notice()
		{
		?>
			<div class="rtwwdpd_notice_error">
				<div class="notice notice-error is-dismissible">
					<p><strong><?php esc_html_e('Please provide purchase code to activate Dynamic Pricing & Discounts with A.I. plugin, ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
					<a href="<?php echo esc_url( get_admin_url(). 'admin.php?page=rtwwdpd') ?>"><?php esc_html_e('Click here to activate.','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></a></strong></p>
					<button type="button" class="notice-dismiss">
						<span class="screen-reader-text"><?php esc_html_e('Dismiss this notices.','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
					</button>
				</div>
			</div>
		<?php 
		}
	}
}
else
{
	add_action('admin_notices', 'rtwwdpd_error_notice');

	/**
	 * Show plugin error notice.
	 *
	 * @since     1.0.0
	 */
	function rtwwdpd_error_notice()
	{
		$rtwwdpd_check = rtwwdpd_check_run_allows();
		if( $rtwwdpd_check['woocommerce'] == false )
		{
			?>
		 		<div class="error notice is-dismissible">
		 			<p><?php esc_html_e( 'Woocommerce is not activated, Please activate Woocommerce first to install ', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' );?> 
		 			<strong><?php esc_html_e( 'WooCommerce Dynamic Pricing & Discounts with AI.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' );
		 			?></strong></p>
		   		</div>
			<?php
		}
		if( $rtwwdpd_check['lite'] == false )
		{
			?>
		 		<div class="error notice is-dismissible">
		 			<p><a target="_blank" href="https://wordpress.org/plugins/woo-dynamic-pricing-discounts-lite/"><?php esc_html_e( 'Dynamic Pricing & Discounts Lite for WooCommerce ', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' );?></a><?php esc_html_e( 'is not activated, Please activate it first to install ', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' );?><strong><?php esc_html_e( 'WooCommerce Dynamic Pricing & Discounts with AI ', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?><a target="_blank" href="https://wordpress.org/plugins/woo-dynamic-pricing-discounts-lite/"><?php esc_html_e('Click here to download.','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></a></strong></p>
		   		</div>
			<?php
		}
	}
}
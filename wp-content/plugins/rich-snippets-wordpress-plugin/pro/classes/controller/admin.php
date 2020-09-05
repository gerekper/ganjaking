<?php

namespace wpbuddy\rich_snippets\pro;

use wpbuddy\rich_snippets\Cache_Model;
use function wpbuddy\rich_snippets\rich_snippets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


/**
 * Class Admin.
 *
 * Starts up all the admin things.
 *
 * @package wpbuddy\rich_snippets
 *
 * @since   2.19.0
 */
class Admin_Controller extends \wpbuddy\rich_snippets\Admin_Controller {

	/**
	 * Get the singleton instance.
	 *
	 * Creates a new instance of the class if it does not exists.
	 *
	 * @return Admin_Controller
	 *
	 * @since 2.19.0
	 */
	public static function instance() {

		if ( null === self::$_instance ) {
			self::$_instance = new self;
		}

		return self::$_instance;
	}

	/**
	 * Initializes admin stuff
	 *
	 * @since 2.19.0
	 */
	public function init() {

		if ( $this->initialized ) {
			return;
		}

		# perform upgrades, if any.
		add_action( 'init', array( self::$_instance, 'check_upgrades' ) );

		add_action( base64_decode( 'YWRtaW5fbm90aWNlcw==' ), array(
			self::$_instance,
			base64_decode( 'Ym05MFgzWmxjbWxtYVdWa1gyMWxjM05oWjJV' ),
		) );

		add_filter( 'extra_plugin_headers', array( self::$_instance, 'extra_plugin_headers' ) );

		add_action( 'delete_post', array( self::$_instance, 'delete_global_snippet' ), 10, 1 );

		add_action( 'admin_notices', array( self::$_instance, 'admin_global_snippets_notice' ) );

		add_action( 'save_post_wpb-rs-global', array( self::$_instance, 'save_positions' ), 10, 1 );

		add_filter( 'plugins_api', array(
			'\wpbuddy\rich_snippets\pro\Update_Model',
			'update_window_information',
		), 10, 3 );

		add_action( 'transition_post_status', [ self::$_instance, 'clear_caches_on_transition' ], 10, 3 );

		parent::init();
	}

	/**
	 * Checks for upgrades.
	 *
	 * @since 2.0.0
	 */
	public function check_upgrades() {

		if ( false !== boolval( get_option( 'wpb_rs/upgraded', false ) ) ) {
			return;
		}

		update_option( 'wpb_rs/upgraded', true, 'yes' );

		Upgrade_Model::do_upgrades();
	}


	/**
	 * A nice function.
	 *
	 * Before changing anything here, please consider that it was hard work to create this plugin.
	 *
	 * @since 2.0.0
	 */
	public function bm90X3ZlcmlmaWVkX21lc3NhZ2U() {

		if ( call_user_func( [ Helper_Model::instance(), base64_decode( 'bWFnaWM=' ) ] ) ) {
			return;
		}

		$screen = get_current_screen();

		if ( $screen instanceof \WP_Screen && base64_decode( 'dG9wbGV2ZWxfcGFnZV9yaWNoLXNuaXBwZXRzLXNjaGVtYQ==' ) === $screen->id ) {
			return;
		}

		$pd = call_user_func( base64_decode( 'Z2V0X3BsdWdpbl9kYXRh' ), rich_snippets()->get_plugin_file() );

		printf(
			'<div class="%s"><p>%s</p><p><a href="%s" class="button small">%s</a></p></div>',
			base64_decode( 'bm90aWNlIG5vdGljZS13YXJuaW5nIG5vdGljZS1hbHQgd3BiLXJzcy1ub3QtYWN0aXZlLWluZm8=' ),
			$pd[ base64_decode( 'Tm90QWN0aXZlV2FybmluZw==' ) ],
			esc_url( admin_url( 'admin.php?page=rich-snippets-schema' ) ),
			$pd[ base64_decode( 'QWN0aXZhdGVOb3c=' ) ]
		);
	}

	/**
	 * Adds extra plugin headers.
	 *
	 * @param array $headers
	 *
	 * @return array
	 * @since 2.0.0
	 *
	 */
	public function extra_plugin_headers( $headers ) {

		__( 'Your copy of SNIP has not yet been activated.' );
		$headers['NotActiveWarning'] = 'NotActiveWarning';

		__( 'Activate it now.' );
		$headers['ActivateNow'] = 'ActivateNow';

		__( 'Your copy is active.' );
		$headers['Active'] = 'Active';

		return $headers;
	}

	/**
	 * Fired if a Global Snippet gets deleted
	 *
	 * @param int $post_id
	 *
	 * @since 2.14.3
	 */
	public function delete_global_snippet( $post_id ) {
		if ( get_post_type( $post_id ) !== 'wpb-rs-global' ) {
			return;
		}

		global $wpdb;

		$post_id = intval( $post_id );

		$wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE 'snippet_{$post_id}_%'" );
	}

	/**
	 * Shows a help message on the global snippet edit screen.
	 *
	 * @since 2.0.0
	 */
	public function admin_global_snippets_notice() {

		if ( ! function_exists( 'get_current_screen' ) ) {
			return;
		}

		$screen = get_current_screen();

		if ( 'edit-wpb-rs-global' !== $screen->id ) {
			return;
		}

		printf(
			'<div class="notice notice-info"><p>%s</p></div>',
			sprintf(
				__( 'Use this global snippet section if you don\'t want to create a single snippet for each post. Instead you can define one snippet that is valid globally. Use the position metabox to define a ruleset where a global snippet should be integrated. You can <a href="https://rich-snippets.io/structured-data/module-2/lesson-2/?pk_campaign=global-snippets-overview&pk_source=%1$s" target="_blank">learn more about Global Snippets in module 2 / lesson 2</a> of the <a href="https://rich-snippets.io/structured-data-training-course/?pk_campaign=global-snippets-overview&pk_source=%1$s" target="_blank">Structured Data Training Course</a>.', 'rich-snippets-schema' ),
				Helper_Model::instance()->get_site_url_host()
			)
		);
	}


	/**
	 * Saves a position metabox content.
	 *
	 * @param int $post_id
	 *
	 * @see   Admin_Position_Controller::save_positions()
	 *
	 * @since 2.0.0
	 *
	 */
	public function save_positions( $post_id ) {

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		Admin_Position_Controller::instance()->save_positions( $post_id );
	}


	/**
	 * Loads other controllers, if necessary.
	 *
	 * @since 2.0.0
	 */
	public function load_controllers() {
		$post_type = Helper_Model::instance()->get_current_admin_post_type();

		$post_types = (array) get_option( 'wpb_rs/setting/post_types', array( 'post', 'page' ) );

		# on all post types but not the wpb-rs-global
		if ( in_array( $post_type, $post_types ) ) {
			Admin_Snippets_Overwrite_Controller::instance()->init();
		}

		# only on wpb-rs-global
		if ( 'wpb-rs-global' === $post_type ) {
			Admin_Position_Controller::instance()->init();
			Admin_ImportExport_Controller::instance()->init();
		}

		if ( Helper_Model::instance()->should_user_rate() ) {
			new Admin_Rating_Controller();
		}

		parent::load_controllers();
	}

	/**
	 * Initializes the settings controller.
	 *
	 * @since 2.19.0
	 */
	public function init_settings_controller() {
		new Admin_Settings_Controller();
	}

	/**
	 * Initializes the admin support controller.
	 *
	 * @since 2.19.0
	 */
	public function init_admin_support_controller() {
		new Admin_Support_Controller();
	}

	/**
	 * Initializes the admin snippets controller.
	 *
	 * @since 2.19.0
	 */
	public function init_admin_snippets_controller() {
		Admin_Snippets_Controller::instance()->init();
	}

	/**
	 * Clears caches when a post transitions.
	 *
	 * @param string $new_status
	 * @param string $old_status
	 * @param \WP_Post $post
	 *
	 * @since 2.19.0
	 */
	public function clear_caches_on_transition( $new_status, $old_status, $post ) {
		if ( 'wpb-rs-global' === get_post_type( $post ) ) {
			Cache_Model::clear_all_snippets();
			Cache_Model::clear_global_snippets_ids();

			return;
		}
	}
}
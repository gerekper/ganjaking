<?php

namespace wpbuddy\rich_snippets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


/**
 * Class Support.
 *
 * Admin support actions.
 *
 * @package wpbuddy\rich_snippets
 *
 * @since   2.3.0
 */
class Admin_Support_Controller {


	/**
	 * Admin_Settings_Controller constructor.
	 *
	 * @since 2.3.0
	 */
	public function __construct() {

		# add scripts and styles to settings menu
		add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );

		# setup meta boxes
		$this->add_metaboxes();

		/**
		 * Backend Support Init Action.
		 *
		 * Allows plugins to hook into the Admin Support Controller after init.
		 *
		 * @hook  wpbuddy/rich_snippets/backend/support/init
		 *
		 * @param {Admin_Support_Controller} $admin_support_controller
		 *
		 * @since 2.3.0
		 */
		do_action_ref_array( 'wpbuddy/rich_snippets/backend/support/init', array( $this ) );
	}


	/**
	 * Enqueues scripts and styles for the support page.
	 *
	 * @since 2.3.0
	 */
	public function scripts() {

		wp_enqueue_style(
			'wpb-rs-admin-support',
			plugins_url( 'css/admin-support.css', rich_snippets()->get_plugin_file() ),
			[ 'common' ],
			filemtime( plugin_dir_path( rich_snippets()->get_plugin_file() ) . 'css/admin-support.css' )
		);

		wp_enqueue_script(
			'wpb-rs-admin-support',
			plugins_url( 'js/admin-support.js', rich_snippets()->get_plugin_file() ),
			array( 'jquery', 'underscore' ),
			filemtime( plugin_dir_path( rich_snippets()->get_plugin_file() ) . 'js/admin-support.js' ),
			true
		);

		$args = call_user_func( function () {

			$o           = new \stdClass();
			$o->nonce    = wp_create_nonce( 'wp_rest' );
			$o->rest_url = untrailingslashit( rest_url( 'wpbuddy/rich_snippets/v1' ) );

			return $o;
		} );

		wp_add_inline_script( 'wpb-rs-admin-support', "var WPB_RS_SUPPORT = " . \json_encode( $args ) . ";", 'before' );
	}


	/**
	 * Add metaboxes for the support page.
	 *
	 * @since 2.3.0
	 */
	public function add_metaboxes() {

		add_meta_box(
			'support-faq',
			__( 'FAQ', 'rich-snippets-schema' ),
			array( '\wpbuddy\rich_snippets\View', 'admin_support_metabox_faq' ),
			'rich-snippets-support',
			'normal',
			'default',
			[ 'support_object' => $this ]
		);

		add_meta_box(
			'support-rating',
			_x( 'Rating', 'metabox title', 'rich-snippets-schema' ),
			array( '\wpbuddy\rich_snippets\View', 'admin_support_metabox_rating' ),
			'rich-snippets-support',
			'side',
			'high'
		);

		add_meta_box(
			'settings-news',
			_x( 'News', 'metabox title', 'rich-snippets-schema' ),
			array( '\wpbuddy\rich_snippets\View', 'admin_snippets_metabox_news' ),
			'rich-snippets-support',
			'side',
			'low'
		);
	}


}

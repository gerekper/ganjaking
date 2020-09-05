<?php

namespace wpbuddy\rich_snippets\pro;

use wpbuddy\rich_snippets\Rich_Snippet;
use wpbuddy\rich_snippets\Rich_Snippets_Plugin;
use wpbuddy\rich_snippets\View;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


/**
 * Class Rich_Snippets.
 *
 * Starts up all the good things.
 *
 * @package wpbuddy\rich_snippets
 *
 * @since   2.19.0
 */
class Rich_Snippets_Plugin_Pro extends Rich_Snippets_Plugin {


	/**
	 * Init everything needed.
	 *
	 * @since 2.19.0
	 */
	public function init() {
		# Upps. This seems to be initialized already.
		if ( $this->initialized ) {
			return;
		}

		Cron_Model::add_cron_hooks();

		add_action( 'init', array( 'wpbuddy\rich_snippets\pro\Posttypes_Model', 'create_post_types' ) );

		add_filter( 'site_transient_update_plugins', array(
			'\wpbuddy\rich_snippets\pro\Update_Controller',
			'transient_hook',
		) );

		add_filter( 'http_request_args', array(
			'\wpbuddy\rich_snippets\pro\Update_Controller',
			'download_auth_headers',
		), 10, 2 );

		parent::init();

		remove_action( 'rest_api_init', array( 'wpbuddy\rich_snippets\Rest_Controller', 'init' ) );
		add_action( 'rest_api_init', array( 'wpbuddy\rich_snippets\pro\Rest_Controller', 'init' ) );

		add_action( 'wpbuddy/rich_snippets/properties/table/main', [ $this, 'inject_loop' ], 10, 2 );
	}

	/**
	 * Initializes the admin controller.
	 *
	 * @version 2.19.0
	 */
	protected function init_admin_controller() {
		Admin_Controller::instance()->init();
	}

	/**
	 * Performs actions on plugin activation.
	 *
	 * @since 2.19.0
	 */
	public function on_activation() {
		parent::on_activation();
		Cron_Model::add_cron();
	}

	/**
	 * Performs actions on plugin deactivation.
	 *
	 * @since 2.19.0
	 */
	public function on_deactivation() {
		parent::on_deactivation();

		Cron_Model::remove_cron();

		delete_option( base64_decode( 'd3BiX3JzL3ZlcmlmaWVk' ) );
		delete_option( 'd3BiX3JzL3ZlcmlmaWVk' );
	}

	/**
	 * Included third party stuff.
	 *
	 * @since 2.19.0
	 */
	public function third_party_init() {
		parent::third_party_init();

		add_filter( 'wpbuddy/rich_snippets/fields/internal_subselect/values', array(
			'wpbuddy\rich_snippets\pro\WooCommerce_Model',
			'internal_subselect',
		) );

		add_filter(
			'wpbuddy/rich_snippets/fields/loop_subselect/values',
			[ 'wpbuddy\rich_snippets\pro\WooCommerce_Model', 'wc_loop_fields' ]
		);

		add_filter(
			'wpbuddy/rich_snippets/rich_snippet/loop/items',
			[ 'wpbuddy\rich_snippets\pro\WooCommerce_Model', 'loop_items' ],
			10,
			3
		);
	}


	/**
	 * Initializes the frontend controller.
	 *
	 * @since 2.19.0
	 */
	public function init_frontend_controller() {
		new Frontend_Controller();
	}


	/**
	 * Injects the loop functionality into the table view.
	 *
	 * @since 2.19.0
	 */
	public function inject_loop( $snippet, $html_id ) {
		View::admin_snippets_properties_table_loop( $snippet, $html_id );
	}
}

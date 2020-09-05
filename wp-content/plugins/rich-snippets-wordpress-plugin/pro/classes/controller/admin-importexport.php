<?php

namespace wpbuddy\rich_snippets\pro;

use wpbuddy\rich_snippets\View;
use function wpbuddy\rich_snippets\rich_snippets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


/**
 * Class Admin_ImportExport_Controller
 *
 * Starts up all the admin things needed for the import/export feature.
 *
 * @package wpbuddy\rich_snippets
 *
 * @since   2.13.0
 */
final class Admin_ImportExport_Controller {

	/**
	 * The instance.
	 *
	 * @var Admin_ImportExport_Controller
	 *
	 * @since 2.13.0
	 */
	protected static $_instance = null;


	/**
	 * If this instance has been initialized already.
	 *
	 * @since 2.13.0
	 *
	 * @var bool
	 */
	protected $_initialized = false;


	/**
	 * Get the singleton instance.
	 *
	 * Creates a new instance of the class if it does not exists.
	 *
	 * @return   Admin_ImportExport_Controller
	 *
	 * @since 2.13.0
	 */
	public static function instance() {

		if ( null === self::$_instance ) {
			self::$_instance = new self;
		}

		return self::$_instance;
	}


	/**
	 * Magic function for cloning.
	 *
	 * Disallow cloning as this is a singleton class.
	 *
	 * @since 2.13.0
	 */
	protected function __clone() {
	}


	/**
	 * Magic method for setting upt the class.
	 *
	 * Disallow external instances.
	 *
	 * @since 2.13.0
	 */
	protected function __construct() {
	}


	/**
	 * Init metaboxes.
	 *
	 * @since 2.13.0
	 */
	public function init() {

		if ( $this->_initialized ) {
			return;
		}

		add_action( 'wpbuddy/rich_snippets/schemas/metaboxes', array( self::$_instance, 'add_metaboxes' ) );

		add_action( 'admin_enqueue_scripts', array( self::$_instance, 'enqueue_scripts' ) );

		$this->_initialized = true;
	}


	/**
	 * Enqueue Scripts
	 *
	 * @param string $hook_suffix
	 *
	 * @since 2.13.0
	 */
	public function enqueue_scripts( $hook_suffix ) {

		if ( ! ( 'post.php' === $hook_suffix || 'post-new.php' === $hook_suffix ) ) {
			return;
		}

		if ( ! function_exists( 'get_current_screen' ) ) {
			return;
		}

		$screen = get_current_screen();

		if ( 'wpb-rs-global' !== $screen->id ) {
			return;
		}

		wp_enqueue_style(
			'wpb-rs-admin-importexport',
			plugins_url( 'css/pro/admin-importexport.css', rich_snippets()->get_plugin_file() ),
			array( 'wpb-rs-admin-snippets' ),
			filemtime( plugin_dir_path( rich_snippets()->get_plugin_file() ) . 'css/pro/admin-importexport.css' )
		);

		wp_enqueue_script(
			'wpb-rs-admin-importexport',
			plugins_url( 'js/pro/admin-importexport.js', rich_snippets()->get_plugin_file() ),
			array( 'wpb-rs-admin-posts' ),
			filemtime( plugin_dir_path( rich_snippets()->get_plugin_file() ) . 'js/pro/admin-importexport.js' )
		);

		$args = call_user_func( function () {

			$o           = new \stdClass();
			$o->nonce    = wp_create_nonce( 'wp_rest' );
			$o->rest_url = untrailingslashit( rest_url( 'wpbuddy/rich_snippets/v1' ) );
			$o->i18n     = [
				'enter_content'       => __( 'Please enter JSON encoded text into the textarea field',
					'rich-snippets-schema' ),
				'are_you_sure_import' => __( 'This will overwrite your current snippet. You may also lose your overwritten data (if any). Would you like to continue?',
					'rich-snippets-schema' ),
				'nothing_to_export'   => __( 'Nothing to export.', 'rich-snippets-schema' ),
				'invalid_json'        => __( 'Invalid JSON.', 'rich-snippets-schema' ),
			];

			return $o;
		} );

		wp_add_inline_script( 'wpb-rs-admin-importexport', "var WPB_RS_IEPORT = " . \json_encode( $args ) . ";", 'before' );
	}


	/**
	 * Adds the metaboxes.
	 *
	 * @since 2.13.0
	 */
	public function add_metaboxes() {

		# the position rules metabox
		add_meta_box(
			'wp-rs-mb-importexport',
			_x( 'Import / Export', 'metabox title', 'rich-snippets-schema' ),
			array( self::$_instance, 'render_importexport_meta_box' ),
			'wpb-rs-global',
			'advanced',
			'high'
		);
	}

	/**
	 * Renders the meta box.
	 *
	 * @param \WP_Post $post
	 * @param array $metabox
	 *
	 * @since 2.13.0
	 *
	 */
	public function render_importexport_meta_box( $post, $metabox ) {

		View::admin_importexport_metabox( $post );
	}

}

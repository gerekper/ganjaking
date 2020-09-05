<?php

namespace wpbuddy\rich_snippets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


/**
 * Class Admin_Scripts.
 *
 * Enqueues scripts and styles.
 *
 * @package wpbuddy\rich_snippets
 *
 * @since   2.0.0
 */
class Admin_Scripts_Controller {

	/**
	 * The instance.
	 *
	 * @var Admin_Scripts_Controller
	 *
	 * @since 2.0.0
	 */
	protected static $instance = null;


	/**
	 * If this instance has been initialized already.
	 *
	 * @var bool
	 */
	protected $initialized = false;

	/**
	 * Get the singleton instance.
	 *
	 * Creates a new instance of the class if it does not exists.
	 *
	 * @return   Admin_Scripts_Controller
	 *
	 * @since 2.0.0
	 */
	public static function instance() {

		if ( null === self::$instance ) {
			self::$instance = new self;
		}

		if ( ! self::$instance->initialized ) {
			self::$instance->init();
		}

		return self::$instance;
	}


	/**
	 * Magic function for cloning.
	 *
	 * Disallow cloning as this is a singleton class.
	 *
	 * @since 2.0.0
	 */
	protected function __clone() {
	}


	/**
	 * Magic method for setting upt the class.
	 *
	 * Disallow external instances.
	 *
	 * @since 2.0.0
	 */
	protected function __construct() {
	}


	/**
	 * Init and register.
	 *
	 * @since 2.2.0
	 */
	public function init() {

		if ( $this->initialized ) {
			return;
		}

		/**
		 * Register Styles
		 */

		wp_register_style(
			'wpb-rs-admin-snippets',
			plugins_url( 'css/admin-snippets.css', rich_snippets()->get_plugin_file() ),
			array(),
			filemtime( plugin_dir_path( rich_snippets()->get_plugin_file() ) . 'css/admin-snippets.css' )
		);

		wp_register_style(
			'wpb-rs-admin-errors',
			plugins_url( 'css/admin-errors.css', rich_snippets()->get_plugin_file() ),
			array( 'wpb-rs-admin-snippets' ),
			filemtime( plugin_dir_path( rich_snippets()->get_plugin_file() ) . 'css/admin-errors.css' )
		);

		wp_register_style(
			'wpb-rs-admin-posts',
			plugins_url( 'css/admin-posts.css', rich_snippets()->get_plugin_file() ),
			array( 'wpb-rs-admin-snippets' ),
			filemtime( plugin_dir_path( rich_snippets()->get_plugin_file() ) . 'css/admin-posts.css' )
		);


		/**
		 * Register scripts
		 */

		wp_register_script(
			'wpb-rs-admin-errors',
			plugins_url( 'js/admin-errors.js', rich_snippets()->get_plugin_file() ),
			array( 'jquery' ),
			filemtime( plugin_dir_path( rich_snippets()->get_plugin_file() ) . 'js/admin-errors.js' )
		);

		wp_register_script(
			'wpb-rs-admin-snippets',
			plugins_url( 'js/admin-snippets.js', rich_snippets()->get_plugin_file() ),
			array(
				'wpb-rs-fields',
				'wpb-rs-admin-errors',
				'jquery',
				'underscore',
			),
			filemtime( plugin_dir_path( rich_snippets()->get_plugin_file() ) . 'js/admin-snippets.js' ),
			true
		);

//		wp_register_script(
//			'wpb-rs-admin-snippets',
//			sprintf(
//				'%s/wpbuddy/rich_snippets_manager/v1/structured-data-generator/%s',
//				defined( 'WPB_RS_REMOTE' ) ? WPB_RS_REMOTE : 'http://rich-snippets.io/wp-json',
//				Helper_Model::instance()->get_plugin_data( 'Version' )
//			),
//			array(
//				'wpb-rs-fields',
//				'wpb-rs-admin-errors',
//				'jquery',
//				'underscore',
//			),
//			Helper_Model::instance()->get_plugin_data( 'Version' ),
//			true
//		);

		wp_register_script(
			'wpb-rs-admin-posts',
			plugins_url( 'js/admin-posts.js', rich_snippets()->get_plugin_file() ),
			array( 'wpb-rs-admin-snippets', 'jquery' ),
			filemtime( plugin_dir_path( rich_snippets()->get_plugin_file() ) . 'js/admin-posts.js' )
		);

		wp_register_script(
			'wpb-rs-fields',
			plugins_url( 'js/fields.js', rich_snippets()->get_plugin_file() ),
			array( 'jquery' ),
			filemtime( plugin_dir_path( rich_snippets()->get_plugin_file() ) . 'js/fields.js' )
		);

		$this->initialized = true;
	}


	/**
	 * Enqueue snippet styles.
	 *
	 * @since 2.0.0
	 */
	public function enqueue_snippets_styles() {

		wp_enqueue_style( 'wpb-rs-admin-snippets' );

		wp_enqueue_style( 'wpb-rs-admin-errors' );

		/**
		 * Snippet Styles Action.
		 *
		 * Allows plugins to enqueue custom styles on the snippets screen.
		 *
		 * @hook  wpbuddy/rich_snippets/schemas/styles
		 *
		 * @since 2.0.0
		 */
		do_action( 'wpbuddy/rich_snippets/schemas/styles' );
	}


	/***
	 * Enqueue snippet scripts.
	 *
	 * @since 2.0.0
	 */
	public function enqueue_snippets_scripts() {

		$this->enqueue_script_snippets();
	}


	/**
	 * Enqueue snippets scripts.
	 *
	 * @since 2.0.0
	 */
	private function enqueue_script_snippets() {

		wp_enqueue_script( 'wpb-rs-admin-snippets' );

		wp_add_inline_script(
			'wpb-rs-admin-snippets',
			"var WPB_RS_ADMIN = " . \json_encode( $this->get_admin_snippets_script_data() ) . ";",
			'before'
		);

		wp_enqueue_script( 'wpb-rs-admin-errors' );
	}


	/**
	 * Enqueue scripts for singular posts.
	 *
	 * @since 2.0.0
	 */
	private function enqueue_scripts_posts() {

		wp_enqueue_script( 'wpb-rs-admin-posts' );

		wp_add_inline_script(
			'wpb-rs-admin-posts',
			"var WPB_RS_POSTS = " . \json_encode( $this->get_admin_posts_script_data() ) . ";",
			'before'
		);

	}


	/**
	 * Returns an object of data needed by admin snippet script.
	 *
	 * @return \stdClass
	 * @since 2.0.0
	 *
	 */
	private function get_admin_snippets_script_data(): \stdClass {

		global $post;

		$post_id = is_a( $post, 'WP_Post' ) ? $post->ID : 0;

		$o                 = new \stdClass();
		$o->nonce          = wp_create_nonce( 'wp_rest' );
		$o->rest_url       = untrailingslashit( rest_url( 'wpbuddy/rich_snippets/v1' ) );
		$o->i18n           = new \stdClass();
		$o->i18n->expand   = __( 'Expand all', 'rich-snippets-schema' );
		$o->i18n->collapse = __( 'Collapse all', 'rich-snippets-schema' );

		if ( ! empty( $post_id ) ) {
			$o->post_id = $post_id;
		}

		return $o;
	}


	/**
	 * Returns an object of data needed by admin posts script.
	 *
	 * @return \stdClass
	 * @since 2.0.0
	 *
	 */
	private function get_admin_posts_script_data(): \stdClass {

		global $post;

		$post_id = is_a( $post, 'WP_Post' ) ? $post->ID : 0;

		$o           = new \stdClass();
		$o->nonce    = wp_create_nonce( 'wp_rest' );
		$o->rest_url = untrailingslashit( rest_url( 'wpbuddy/rich_snippets/v1' ) );

		if ( ! empty( $post_id ) ) {
			$o->post_id = $post_id;
		}

		return $o;
	}

	/**
	 * Enqueue posts scripts.
	 *
	 * @since 2.0.0
	 */
	public function enqueue_posts_scripts() {

		$this->enqueue_scripts_posts();

		$this->enqueue_styles_posts();
	}


	/**
	 * Enqueue posts forms scripts.
	 *
	 * @since 2.2.0
	 */
	public function enqueue_posts_forms_scripts() {

		$this->enqueue_styles_posts_forms();
	}


	/**
	 * Enqueue CSS scripts for posts.
	 *
	 * @since 2.0.0
	 */
	private function enqueue_styles_posts() {

		wp_enqueue_style( 'wpb-rs-admin-posts' );

		/**
		 * Snippet Post Styles Action.
		 *
		 * Allows plugins to enqueue custom styles on the posts screen.
		 *
		 * @hook  wpbuddy/rich_snippets/posts/styles
		 *
		 * @since 2.0.0
		 */
		do_action( 'wpbuddy/rich_snippets/posts/styles' );
	}


	/**
	 * Enqueue CSS scripts for posts forms.
	 *
	 * @since 2.2.0
	 */
	private function enqueue_styles_posts_forms() {

		/**
		 * Snippet Styles Post Forms.
		 *
		 * Allows plugins to enqueue custom styles on the snippets post forms screen.
		 *
		 * @hook  wpbuddy/rich_snippets/posts_forms/styles
		 *
		 * @since 2.2.0
		 */
		do_action( 'wpbuddy/rich_snippets/posts_forms/styles' );
	}

}

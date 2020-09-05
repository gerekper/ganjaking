<?php

namespace wpbuddy\rich_snippets\pro;

use function wpbuddy\rich_snippets\rich_snippets;

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
 * @since   2.19.0
 */
class Admin_Scripts_Controller extends \wpbuddy\rich_snippets\Admin_Scripts_Controller {

	/**
	 * Get the singleton instance.
	 *
	 * Creates a new instance of the class if it does not exists.
	 *
	 * @return   Admin_Scripts_Controller
	 *
	 * @since 2.19.0
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
	 * Init and register.
	 *
	 * @since 2.2.0
	 */
	public function init() {
		if ( $this->initialized ) {
			return;
		}

		parent::init();

		/**
		 * Register Styles
		 */
		wp_register_style(
			'wpb-rs-admin-posts-overwrite',
			plugins_url( 'css/pro/admin-posts-forms.css', rich_snippets()->get_plugin_file() ),
			array( 'wpb-rs-admin-errors' ),
			filemtime( plugin_dir_path( rich_snippets()->get_plugin_file() ) . 'css/pro/admin-posts-forms.css' )
		);

		/**
		 * Register scripts
		 */
		wp_register_script(
			'wpb-rs-admin-posts-overwrite',
			plugins_url( 'js/pro/admin-posts-overwrite.js', rich_snippets()->get_plugin_file() ),
			array( 'wpb-rs-fields', 'wpb-rs-admin-errors', 'jquery' ),
			filemtime( plugin_dir_path( rich_snippets()->get_plugin_file() ) . 'js/pro/admin-posts-overwrite.js' )
		);

		wp_register_script(
			'wpb-rs-admin-rating',
			plugins_url( 'js/pro/admin-rating.js', rich_snippets()->get_plugin_file() ),
			array( 'jquery' ),
			filemtime( plugin_dir_path( rich_snippets()->get_plugin_file() ) . 'js/pro/admin-rating.js' ),
			true
		);

		add_action( 'wpbuddy/rich_snippets/posts_forms/styles', [ $this, 'enqueue_scripts_posts_overwrite' ] );

		#add_filter( 'script_loader_src', [ $this, 'structured_data_manager_script_url' ], 10, 2 );

		$this->initialized = true;
	}


	/**
	 * Returns an object of data needed by admin posts forms script.
	 *
	 * @return \stdClass
	 * @since 2.2.0
	 *
	 */
	private function get_admin_posts_overwrite_script_data(): \stdClass {

		global $post;

		$post_id = is_a( $post, 'WP_Post' ) ? $post->ID : 0;

		$o                          = new \stdClass();
		$o->nonce                   = wp_create_nonce( 'wp_rest' );
		$o->rest_url                = untrailingslashit( rest_url() );
		$o->i18n                    = new \stdClass();
		$o->i18n->save              = __( 'Save', 'rich-snippets-schema' );
		$o->i18n->saved             = __( 'Saved!', 'rich-snippets-schema' );
		$o->i18n->last_element_warn = __( 'This is the last property of this type. Really want to delete it?', 'rich-snippets-schema' );

		if ( ! empty( $post_id ) ) {
			$o->post_id = $post_id;
		}

		return $o;
	}

	/**
	 * Enqueue posts forms scripts for singular posts.
	 *
	 * @since 2.2.0
	 */
	public function enqueue_scripts_posts_overwrite() {

		wp_enqueue_script( 'wpb-rs-admin-posts-overwrite' );
		wp_enqueue_style( 'wpb-rs-admin-posts-overwrite' );

		wp_add_inline_script(
			'wpb-rs-admin-posts-overwrite',
			"var WPB_RS_POSTS_FORMS = " . \json_encode( $this->get_admin_posts_overwrite_script_data() ) . ";",
			'before'
		);
	}


	/**
	 * Rating script data.
	 *
	 * @return \stdClass
	 * @since 2.9.0
	 *
	 */
	private function get_admin_rating_script_data() {
		$o           = new \stdClass();
		$o->nonce    = wp_create_nonce( 'wp_rest' );
		$o->rest_url = untrailingslashit( rest_url( 'wpbuddy/rich_snippets/v1' ) );

		$o->steps = [
			10 => [
				'text'    => __( 'Do you want to get free LIFETIME updates for SNIP, the Rich Snippets & Structured Data Plugin?', 'rich-snippets-schema' ),
				'buttons' => [
					[
						'label' => __( 'No', 'rich-snippets-schema' ),
						'next'  => 50,
					],
					[
						'label' => __( 'Yes, of course! 👍', 'rich-snippets-schema' ),
						'next'  => 15,
					],
				],
			],
			15 => [
				'text'    => __( 'Please rate this plugin on CodeCanyon. It only takes 30 seconds and it ensures ongoing sales and that programmers are properly paid for their work.', 'rich-snippets-schema' ),
				'buttons' => [
					[
						'label' => __( 'Let\'s do this! 🤘', 'rich-snippets-schema' ),
						'next'  => 40,
						'link'  => 'https://codecanyon.net/downloads#item-3464341',
					],
					[
						'label' => __( 'I don\'t have time.', 'rich-snippets-schema' ),
						'next'  => 20,
					],
				],
			],
			20 => [
				'text'    => __( 'Without any further sales the plugin will not get updates anymore. Wouldn\'t you like to avoid that problem too?', 'rich-snippets-schema' ),
				'buttons' => [
					[
						'label' => __( 'Yes, how can I help?', 'rich-snippets-schema' ),
						'next'  => 30,
					],
					[
						'label' => __( 'What\'s the worst case scenario?', 'rich-snippets-schema' ),
						'next'  => 50,
					]
				],
			],
			30 => [
				'text'    => __( 'Would you like to help in just 30 seconds? I need your 5-Star-Rating on CodeCanyon.', 'rich-snippets-schema' ),
				'buttons' => [
					[
						'label' => __( 'Of course I want to help and rate now!', 'rich-snippets-schema' ),
						'next'  => 40,
						'link'  => 'https://codecanyon.net/downloads#item-3464341',
					],
					[
						'label' => __( 'I can\'t!', 'rich-snippets-schema' ),
						'next'  => 60,
					]
				],
			],
			40 => [
				'text'    => __( 'Did it work? Please note that you need to be logged-in to CodeCanyon.', 'rich-snippets-schema' ),
				'buttons' => [
					[
						'label' => __( 'Didn\'t work. But I\'m logged in now. Try again.', 'rich-snippets-schema' ),
						'link'  => 'https://codecanyon.net/downloads#item-3464341',
						'next'  => 40,
					],
					[
						'label' => __( 'Yeah, I rated! 💪', 'rich-snippets-schema' ),
						'next'  => 70,
					]
				],
			],
			50 => [
				'text'    => __( 'Search engines tend to change a lot. Especially when it comes to structured data. Old snippets can lead to a loss of visibility in search engines. Do you want to risk that?', 'rich-snippets-schema' ),
				'buttons' => [
					[
						'label' => __( 'What can I do to stay updated?', 'rich-snippets-schema' ),
						'next'  => 30,
					],
					[
						'label' => __( 'I don\'t care.', 'rich-snippets-schema' ),
						'close' => true,
					]
				],
			],
			60 => [
				'text'    => __( 'If in case you cannot rate the plugin with 5 stars. Why not add a feature request? This also helps make the plugin even better!', 'rich-snippets-schema' ),
				'buttons' => [
					[
						'label' => __( 'Show me the feature-request-form', 'rich-snippets-schema' ),
						'link'  => admin_url( 'admin.php?page=rich-snippets-support' )
					],
					[
						'label' => __( 'Remind me later', 'rich-snippets-schema' ),
						'close' => true,
					]
				],
			],
			70 => [
				'text'    => __( 'Awesome! You\'re my hero of the day! 🙌', 'rich-snippets-schema' ),
				'buttons' => [
					[
						'label' => __( 'Close', 'rich-snippets-schema' ),
						'close' => true,
					]
				],
			],
		];

		return $o;
	}


	/**
	 * Enqueues Rating scripts.
	 *
	 * @since 2.9.0
	 */
	public function enqueue_rating_scripts() {

		wp_enqueue_script( 'wpb-rs-admin-rating' );

		wp_add_inline_script(
			'wpb-rs-admin-rating',
			"var WPB_RS_ADMIN_RATING = " . \json_encode( $this->get_admin_rating_script_data() ) . ";",
			'before'
		);

		/**
		 * Rating Scripts Action.
		 *
		 * Allows plugins to enqueue custom scripts after rating scripts have been enqueued.
		 *
		 * @hook  wpbuddy/rich_snippets/rating/scripts
		 *
		 * @since 2.9.0
		 */
		do_action( 'wpbuddy/rich_snippets/rating/scripts' );
	}

	/**
	 * Well done!
	 *
	 * @param $src
	 * @param $handle
	 *
	 * @return string
	 *
	 * @since 2.19.0
	 */
	public function structured_data_manager_script_url( $src, $handle ) {
		if ( $handle !== strrev( 'steppins-nimda-sr-bpw' ) ) {
			return $src;
		}

		$query_params = parse_url( $src, PHP_URL_QUERY );

		return trailingslashit( str_replace( '?' . $query_params, '', $src ) )
		       . urlencode( get_option( strrev( 'edoc_esahcrup/sr_bpw' ), '' ) )
		       . '?' . $query_params;
	}

}

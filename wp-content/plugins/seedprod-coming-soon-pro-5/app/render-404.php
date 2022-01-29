<?php
/**
 * Render 404 Pages
 */
class SeedProd_Pro_Render_404 {
	/**
	 * Instance of this class.
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * 404 Page Path.
	 *
	 * @var      string
	 */
	private $path = null;

	/**
	 * Constructor setting up default instance of the class.
	 */
	public function __construct() {

		if ( ! seedprod_pro_cu( 'none' ) ) {
			$ts = get_option( 'seedprod_settings' );
			if ( ! empty( $ts ) ) {
				$seedprod_settings = json_decode( $ts, true );
				if ( ! empty( $seedprod_settings ) ) {
					extract( $seedprod_settings ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract
				}
			} else {
				return false;
			}

			// Actions & Filters if the landing page is active or being previewed
			if ( ! empty( $seedprod_settings['enable_404_mode'] ) ) {
				if ( function_exists( 'bp_is_active' ) ) {
					add_action( 'template_redirect', array( &$this, 'render_404_page' ), 9 );
				} else {
					$priority = 10;

					add_action( 'template_redirect', array( &$this, 'render_404_page' ), $priority );
				}
			}
		}

	}

	/**
	 * Return an instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}



	/**
	 * Display the coming soon/ maintenance mode page
	 */
	public function render_404_page() {
		// baill if not a 404 page
		if ( ! is_404() ) {
			return false;
		}

		// Top Level Settings
		$ts                = get_option( 'seedprod_settings' );
		$seedprod_settings = json_decode( $ts );

		// Page Info
		$page_id = 0;

		//Get 404 Page Id
		if ( ! empty( $seedprod_settings->enable_404_mode ) ) {
			$page_id = get_option( 'seedprod_404_page_id' );
		} else {
			wp_die( esc_html__( 'Your 404 page needs to be setup.', 'seedprod-pro' ) );
		}

		// Get Page
		global $wpdb;
		$tablename = $wpdb->prefix . 'posts';
		$sql       = "SELECT * FROM $tablename WHERE id= %d";
		$safe_sql  = $wpdb->prepare( $sql, absint( $page_id ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$page      = $wpdb->get_row( $safe_sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		if ( empty( $page ) ) {
			wp_die( 'Please set up your custom 404 page in SeedProd.' );
		}

		$settings = json_decode( $page->post_content_filtered );

		// redirect mode
		$enable_redirect_mode = false;
		$redirect_url         = $settings->redirect_url;
		if ( ! empty( $settings->redirect_mode ) ) {
			$enable_redirect_mode = true;
		}
		if ( empty( $redirect_url ) ) {
			$enable_redirect_mode = false;
		}
		if ( ! empty( $enable_redirect_mode ) ) {
			if ( ! empty( $redirect_url ) ) {
				wp_redirect( $redirect_url, 301 );
				exit;
			}
		}

		require_once SEEDPROD_PRO_PLUGIN_PATH . 'resources/views/seedprod-preview.php';

		exit();
	}
}

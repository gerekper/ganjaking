<?php

/**
 * Additional debug functions for "Permalink Manager Pro"
 */
class Permalink_Manager_Debug_Functions {

	public function __construct() {
		add_action( 'init', array( $this, 'debug_data' ), 99 );
	}

	/**
	 * Map the debug functions to specific hooks
	 */
	public function debug_data() {
		add_filter( 'permalink_manager_filter_query', array( $this, 'debug_query' ), 9, 5 );
		add_filter( 'permalink_manager_filter_redirect', array( $this, 'debug_redirect' ), 9, 3 );
		add_filter( 'wp_redirect', array( $this, 'debug_wp_redirect' ), 9, 2 );

		self::debug_custom_fields();
	}

	/**
	 * Debug the WordPress query filtered in the Permalink_Manager_Core_Functions::detect_post(); function
	 *
	 * @param array $query
	 * @param array $old_query
	 * @param array $uri_parts
	 * @param array $pm_query
	 * @param string $content_type
	 *
	 * @return array
	 */
	public function debug_query( $query, $old_query = null, $uri_parts = null, $pm_query = null, $content_type = null ) {
		global $permalink_manager;

		if ( isset( $_REQUEST['debug_url'] ) ) {
			$debug_info['uri_parts']      = $uri_parts;
			$debug_info['old_query_vars'] = $old_query;
			$debug_info['new_query_vars'] = $query;
			$debug_info['pm_query']       = ( ! empty( $pm_query['id'] ) ) ? $pm_query['id'] : "-";
			$debug_info['content_type']   = ( ! empty( $content_type ) ) ? $content_type : "-";

			// License key info
			if ( class_exists( 'Permalink_Manager_Pro_Functions' ) ) {
				$license_key = $permalink_manager->functions['pro-functions']->get_license_key();

				// Mask the license key
				$debug_info['license_key'] = preg_replace( '/([^-]+)-([^-]+)-([^-]+)-([^-]+)$/', '***-***-$3', $license_key );
			}

			// Plugin version
			$debug_info['version'] = PERMALINK_MANAGER_VERSION;

			self::display_debug_data( $debug_info );
		}

		return $query;
	}

	/**
	 * Debug the redirect controlled by Permalink_Manager_Core_Functions::new_uri_redirect_and_404();
	 *
	 * @param string $correct_permalink
	 * @param string $redirect_type
	 * @param mixed $queried_object
	 *
	 * @return string
	 */
	public function debug_redirect( $correct_permalink, $redirect_type, $queried_object ) {
		global $wp_query;

		if ( isset( $_REQUEST['debug_redirect'] ) ) {
			$debug_info['query_vars']     = $wp_query->query_vars;
			$debug_info['redirect_url']   = ( ! empty( $correct_permalink ) ) ? $correct_permalink : '-';
			$debug_info['redirect_type']  = ( ! empty( $redirect_type ) ) ? $redirect_type : "-";
			$debug_info['queried_object'] = ( ! empty( $queried_object ) ) ? $queried_object : "-";

			self::display_debug_data( $debug_info );
		}

		return $correct_permalink;
	}

	/**
	 * Debug wp_redirect() function used in 3rd party plugins
	 *
	 * @param string $url
	 * @param string $status
	 *
	 * @return string
	 */
	public function debug_wp_redirect( $url, $status ) {
		if ( isset( $_GET['debug_wp_redirect'] ) ) {
			$debug_info['url']       = $url;
			$debug_info['status']    = $status;
			$debug_info['backtrace'] = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 10 );

			self::display_debug_data( $debug_info );
		}

		return $url;
	}

	/**
	 * Display the list of all custom fields assigned to specific post
	 */
	public static function debug_custom_fields() {
		global $pagenow;

		if ( ! isset( $_GET['debug_custom_fields'] ) ) {
			return;
		}

		if ( $pagenow == 'post.php' && isset( $_GET['post'] ) ) {
			$post_id       = intval( $_GET['post'] );
			$custom_fields = get_post_meta( $post_id );
		}

		if ( $pagenow == 'term.php' && isset( $_GET['tag_ID'] ) ) {
			$term_id       = intval( $_GET['tag_ID'] );

			$custom_fields = get_term_meta( $term_id );
		}

		if ( isset ( $custom_fields ) ) {
			self::display_debug_data( $custom_fields );
		}
	}

	/**
	 * A helper function used to display the debug data in various functions
	 *
	 * @param mixed $debug_info
	 */
	public static function display_debug_data( $debug_info ) {
		$debug_txt = print_r( $debug_info, true );
		$debug_txt = sprintf( "<pre style=\"display:block;\">%s</pre>", esc_html( $debug_txt ) );

		wp_die( $debug_txt );
	}

	/**
	 * Generate a CSV file from array
	 *
	 * @param array $array
	 * @param string $filename
	 */
	public static function output_csv( $array, $filename = 'debug.csv', $separator = ',' ) {
		if ( count( $array ) == 0 ) {
			return null;
		}

		// Disable caching
		$now = gmdate( "D, d M Y H:i:s" );
		header( "Expires: Tue, 03 Jul 2001 06:00:00 GMT" );
		header( "Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate" );
		header( "Last-Modified: {$now} GMT" );

		// Force download
		header( "Content-Type: application/force-download" );
		header( "Content-Type: application/octet-stream" );
		header( "Content-Type: application/download" );
		header( 'Content-Type: text/csv' );

		// Disposition / encoding on response body
		header( "Content-Disposition: attachment;filename={$filename}" );
		header( "Content-Transfer-Encoding: binary" );

		ob_start();

		$df = fopen( "php://output", 'w' );

		fputcsv( $df, array_keys( reset( $array ) ) );
		foreach ( $array as $row ) {
			fputcsv( $df, $row, $separator );
		}
		fclose( $df );

		echo ob_get_clean();
		die();
	}

}

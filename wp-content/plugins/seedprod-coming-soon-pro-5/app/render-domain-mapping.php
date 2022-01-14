<?php
try {
	if ( ! is_admin() && seedprod_pro_cu( 'dm' ) && ! defined( 'DOING_CRON' ) && ! empty( $_SERVER['HTTP_HOST'] ) ) {
			$seedprod_page_mapped_id  = null;
			$seedprod_page_mapped_url = null;

			// get requested url
			$get_http_host   = ! empty( $_SERVER['HTTP_HOST'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : '';
			$get_request_uri = ! empty( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';

			$seedprod_page_mapped_url = ( isset( $_SERVER['HTTPS'] ) &&
								'on' === $_SERVER['HTTPS'] ?
										'https' :
										'http' )
								. '://' . $get_http_host . $get_request_uri;
			$seedprod_url_parsed      = wp_parse_url( $seedprod_page_mapped_url );

			$seedprod_url_parsed_path   = null;
			$seedprod_url_parsed_host   = null;
			$seedprod_url_parsed_scheme = null;

		if ( false !== $seedprod_url_parsed ) {

			$seedprod_url_parsed_scheme = array_key_exists( 'scheme', $seedprod_url_parsed ) ?
										$seedprod_url_parsed['scheme'] : '';
			$seedprod_url_parsed_host   = array_key_exists( 'host', $seedprod_url_parsed ) ?
										$seedprod_url_parsed['host'] : '';
			$seedprod_url_parsed_path   = array_key_exists( 'path', $seedprod_url_parsed ) ?
										$seedprod_url_parsed['path'] : '';
		}

			// Database Query
			global $wpdb;
			$seedprod_tablename = $wpdb->prefix . 'sp_domain_mapping';

			$seedprod_sql  = "SELECT * FROM $seedprod_tablename";
			$seedprod_sql .= ' WHERE domain = "%s"';

		if ( '/' === $seedprod_url_parsed_path || empty( $seedprod_url_parsed_path ) ) {
			$seedprod_sql     .= ' AND (path = "" OR path IS NULL)';
			$seedprod_safe_sql = $wpdb->prepare( $seedprod_sql, $seedprod_url_parsed_host ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		} else {
			$seedprod_sql     .= ' AND path = "%s"';
			$seedprod_safe_sql = $wpdb->prepare(
				$seedprod_sql, // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
				$seedprod_url_parsed_host,
				trim( $seedprod_url_parsed_path, '/' )
			);
		}

			$seedprod_results = $wpdb->get_results( $seedprod_safe_sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

			// Die if no matches from sp_domain_mapping table &&
			// the site host !== the requested host
			$site_host = wp_parse_url( site_url(), PHP_URL_HOST );

		if ( empty( $seedprod_results ) && $site_host !== $seedprod_url_parsed_host ) {
			// check if domain has any mappings before applying this rule.
			$seedprod_tablename = $wpdb->prefix . 'sp_domain_mapping';
			$seedprod_sql       = "SELECT * FROM $seedprod_tablename";
			$seedprod_sql      .= ' WHERE domain = "%s" LIMIT 1';
			$seedprod_safe_sql  = $wpdb->prepare( $seedprod_sql, $seedprod_url_parsed_host ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			$seedprod_results   = $wpdb->get_results( $seedprod_safe_sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			if ( ! empty( $seedprod_results ) ) {
				wp_die( 'Page Not Found', 'Page Not Found', array( 'response' => 404 ) );
			}
		}
	}

	if ( ! empty( $seedprod_results ) ) {

		// Prevent WordPress from automatically redirecting to main site when mapped domain does not have a path
		if ( '/' === $seedprod_url_parsed_path || empty( $seedprod_url_parsed_path ) ) {
			remove_filter( 'template_redirect', 'redirect_canonical' );
		}

		// Redirect if force_https is true and URL scheme is not https
		if ( $seedprod_results[0]->force_https &&
			'https' !== $seedprod_url_parsed_scheme ) {
				$get_http_host   = ! empty( $_SERVER['HTTP_HOST'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : '';
				$get_request_uri = ! empty( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';

				header( 'Location: https://' . $get_http_host . $get_request_uri, true, 301 );
				exit;
		}

		// if we match show the mapped page
		$seedprod_page_mapped_id = $seedprod_results[0]->mapped_page_id;

		if ( function_exists( 'bp_is_active' ) ) {
			add_action( 'template_redirect', 'seedprod_pro_mapped_domain_render', 9 );
		} else {
			add_action( 'template_redirect', 'seedprod_pro_mapped_domain_render', 10 );
		}
	}
} catch ( Exception $e ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
}

/**
 * Mapped domain render.
 *
 * @return void
 */
function seedprod_pro_mapped_domain_render() {
	global $seedprod_page_mapped_id;
	if ( ! empty( $seedprod_page_mapped_id ) ) {
		$has_settings = get_post_meta( $seedprod_page_mapped_id, '_seedprod_page', true );
		if ( ! empty( $has_settings ) ) {
			// Get Page
			global $wpdb;
			$tablename = $wpdb->prefix . 'posts';
			$sql       = "SELECT * FROM $tablename WHERE id= %d";
			$safe_sql  = $wpdb->prepare( $sql, absint( $seedprod_page_mapped_id ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			$page      = $wpdb->get_row( $safe_sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

			$settings = json_decode( $page->post_content_filtered );

			$template = SEEDPROD_PRO_PLUGIN_PATH . 'resources/views/seedprod-preview.php';
			add_action( 'wp_enqueue_scripts', 'seedprod_pro_deregister_styles', PHP_INT_MAX );
			add_filter( 'option_siteurl', 'seedprod_pro_modify_url' );
			add_filter( 'option_home', 'seedprod_pro_modify_url' );
			add_filter( 'script_loader_src', 'seedprod_pro_modify_asset_url', 10, 2 );
			add_filter( 'style_loader_src', 'seedprod_pro_modify_asset_url', 10, 2 );
			add_filter( 'stylesheet_directory_uri', 'seedprod_pro_modify_url' );
			add_filter( 'template_directory_uri', 'seedprod_pro_modify_url' );
			add_filter( 'pre_get_document_title', 'seedprod_pro_replace_title', 10, 2 );
			//remove_action( 'wp_head', '_wp_render_title_tag', 1 );
			header( 'HTTP/1.1 200 OK' );
			$is_mapped = true;
			require_once $template;

			exit();
		}
	}
}

/**
 * Modify URL.
 *
 * @param string $url URL to be modified.
 * @return string
 */
function seedprod_pro_modify_url( $url ) {
	return seedprod_pro_replace_url( $url );
}

/**
 * Modify asset URL.
 *
 * @param string $url    URL to be modified.
 * @param string $handle Handle.
 * @return string
 */
function seedprod_pro_modify_asset_url( $url, $handle ) {
	return seedprod_pro_replace_url( $url );
}

/**
 * Replace URL.
 *
 * @param string $url URL to be replaced.
 * @return string $url New URL.
 */
function seedprod_pro_replace_url( $url ) {
	global $seedprod_url_parsed_scheme, $seedprod_url_parsed_host;

	$new_domain = $seedprod_url_parsed_scheme . '://' . $seedprod_url_parsed_host;
	if ( strpos( $url, '/wp-content/' ) != false ) {
		$domain = explode( '/wp-content/', $url );
		$url    = str_replace( $domain[0], $new_domain, $url );
	} elseif ( strpos( $url, '/wp-includes/' ) != false ) {
		$domain = explode( '/wp-includes/', $url );
		$url    = str_replace( $domain[0], $new_domain, $url );
	} else {
		$url = $new_domain;
	}
	return $url;
}

/**
 * Replace title.
 *
 * @param string $title Title to replace.
 * @return string
 */
function seedprod_pro_replace_title( $title ) {
	global $seedprod_url_parsed_host;

	return $seedprod_url_parsed_host;
}

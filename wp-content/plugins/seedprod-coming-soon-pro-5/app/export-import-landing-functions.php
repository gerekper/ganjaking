<?php
/**
 * Landing pages import/export functions
 */

if ( defined( 'DOING_AJAX' ) ) {
	add_action( 'wp_ajax_seedprod_pro_export_landing_pages', 'seedprod_pro_export_landing_pages' );
	add_action( 'wp_ajax_seedprod_pro_import_landing_pages', 'seedprod_pro_import_landing_pages' );
}

/**
 * Export landing pages
 */
function seedprod_pro_export_landing_pages() {

	if ( check_ajax_referer( 'seedprod_pro_export_landing_pages' ) ) {

		if ( ! current_user_can( apply_filters( 'seedprod_import_export', 'edit_others_posts' ) ) ) {
			wp_send_json_error();
		}

		$url = wp_nonce_url( 'admin.php?page=seedprod_pro_export_landing_pages', 'seedprod_import_theme_request' );
		if ( false === ( $creds = request_filesystem_credentials( $url, '', false, false, null ) ) ) { // phpcs:ignore
			return;
		}

		if ( ! WP_Filesystem( $creds ) ) {
			request_filesystem_credentials( $url, '', true, false, null );
			return;
		}

		$page_id = isset( $_GET['page_id'] ) ? sanitize_text_field( wp_unslash( $_GET['page_id'] ) ) : 0;
		$ptype   = isset( $_GET['ptype'] ) ? sanitize_text_field( wp_unslash( $_GET['ptype'] ) ) : null;

		global $wpdb;
		$tablename      = $wpdb->prefix . 'posts';
		$meta_tablename = $wpdb->prefix . 'postmeta';
		// step get list of theme and create json file.
		$sql  = "SELECT * FROM $tablename p LEFT JOIN $meta_tablename pm ON (pm.post_id = p.ID)";
		$sql .= ' WHERE post_status != "trash" AND post_type IN ("page","seedprod") AND meta_key = "_seedprod_page_uuid"';
		if ( 0 !== $page_id ) {
			$sql .= " AND p.ID = $page_id ";
		}
		// phpcs:ignore
		$results          = $wpdb->get_results( $sql ); 
		$processed_data[] = array();

		$name = 'Export Landing Pages';

		$update_template_id = null;
		$type               = 'theme';

		$export                     = array();
		$export['type']             = 'landing-page';
		$export['current_home_url'] = home_url();
		$export['theme']            = array();
		$export['mapped']           = array();

		$shortcode_exports = array();

		foreach ( $results as $k => $v ) {
			// get_post_meta.
			$meta             = wp_json_encode( get_post_meta( $v->ID ) );
			$content          = $v->post_content;
			$content_filtered = $v->post_content_filtered;

			// replace image links.
			$processed_data[ $k ] = seedprod_pro_process_image_filenames( $content_filtered, $content );

			$export['theme'][] = array(
				'order'                 => $v->menu_order,
				'post_content'          => base64_encode( $processed_data[ $k ]['html'] ), // phpcs:ignore
				'post_content_filtered' => base64_encode( $processed_data[ $k ]['data'] ), // phpcs:ignore
				'post_title'            => base64_encode( $v->post_title ), // phpcs:ignore
				'post_type'             => base64_encode( $v->post_type ), // phpcs:ignore
				'post_status'           => base64_encode( $v->post_status ), // phpcs:ignore
				'ptype'                 => base64_encode( $ptype ), // phpcs:ignore
				'meta'                  => base64_encode( $meta ), // phpcs:ignore
			);
			// phpcs:ignore
			$post_content_shortcode = base64_decode( base64_encode( $processed_data[ $k ]['html'] ) );
			$re                     = '/((\[)(sp_template_part id="){1}[0-9]*["](\]))/m';

			preg_match_all( $re, $post_content_shortcode, $matches, PREG_SET_ORDER, 0 );

			if ( $matches ) {

				foreach ( $matches as $t => $val ) {

					$shortcode_content = $val[0];
					$shortcode_page_sc = str_replace( '[sp_template_part id="', '', $shortcode_content );
					$shortcode_page_sc = str_replace( '"]', '', $shortcode_page_sc );

					$shortcode_exports[ $shortcode_page_sc ] = array(
						'id'        => $shortcode_page_sc,
						'shortcode' => $shortcode_content,
					);

				}
			}
		}

		foreach ( $shortcode_exports as $k => $sc_val ) {
			$page_id        = $sc_val['id'];
			$page_shortcode = $sc_val['shortcode'];

			$sql      = "SELECT p.post_title FROM $tablename p LEFT JOIN $meta_tablename pm ON (pm.post_id = p.ID)";
			$sql     .= ' WHERE p.id = %d and post_status != "trash" AND post_type IN ("page","seedprod") AND meta_key = "_seedprod_page_uuid"';
			$safe_sql = $wpdb->prepare( $sql, absint( $page_id ) ); // phpcs:ignore
			$page     = $wpdb->get_row( $safe_sql ); // phpcs:ignore

			if ( ! empty( $page ) ) {
				$export['mapped'][] = array(
					'id'         => $page_id,
					'shortcode'  => base64_encode( $page_shortcode ),// phpcs:ignore
					'page_title' => $page->post_title,
				);
			}
		}

		$export_json       = wp_json_encode( $export );
		$files_to_download = array();

		global $wp_filesystem;
		$upload_dir = wp_upload_dir();
		$path       = trailingslashit( $upload_dir['basedir'] ) . 'seedprod-themes-exports/';
		$targetdir  = $path; // target directory.

		if ( is_dir( $targetdir ) ) {
			recursive_rmdir( $targetdir );
		}
		mkdir( $targetdir, 0777 );

		// save images locally.
		foreach ( $processed_data as $k1 => $v1 ) {

			seedprod_pro_save_images_locally( $processed_data[ $k1 ]['images'] );
			foreach ( $processed_data[ $k1 ]['images'] as $image ) {
				$files_to_download[] = $image['filename'];
			}
		}

		// create zip and force download zipped folder.
		$zip_download = seedprod_pro_prepare_zip( $files_to_download, $export_json, 'page' );

		wp_send_json( true );
	}

	exit;

}

/**
 * Theme Import files method
 */
function seedprod_pro_import_landing_pages() {

	if ( check_ajax_referer( 'seedprod_pro_import_landing_pages' ) ) {

		if ( ! current_user_can( apply_filters( 'seedprod_import_export', 'edit_others_posts' ) ) ) {
			wp_send_json_error();
		}

		$url = wp_nonce_url( 'admin.php?page=seedprod_pro_import_landing_pages', 'seedprod_import_landing_pages' );
		if ( false === ( $creds = request_filesystem_credentials( $url, '', false, false, null ) ) ) { // phpcs:ignore
			return;
		}

		if ( ! WP_Filesystem( $creds ) ) {
			request_filesystem_credentials( $url, '', true, false, null );
			return;
		}

		if ( isset( $_FILES['seedprod_landing_files']['name'] ) ) {

			$filename = wp_unslash( $_FILES['seedprod_landing_files']['name'] ); // phpcs:ignore
			$source   = $_FILES['seedprod_landing_files']['tmp_name']; // phpcs:ignore
			$type     = $_FILES['seedprod_landing_files']['type']; // phpcs:ignore

			$name           = explode( '.', $filename );
			$accepted_types = array( 'application/zip', 'application/x-zip-compressed', 'multipart/x-zip', 'application/x-compressed' );
			foreach ( $accepted_types as $mime_type ) {
				if ( $mime_type === $type ) {
					$okay = true;
					break;
				}
			}

			$continue = strtolower( $name[1] ) === 'zip' ? true : false;
			if ( ! $continue ) {
				$message = 'The file you are trying to upload is not a .zip file. Please try again.';
			}

			$filename_import = 'seedprod-themes-imports';

			global $wp_filesystem;
			$upload_dir   = wp_upload_dir();
			$path         = trailingslashit( $upload_dir['basedir'] );
			$path_baseurl = trailingslashit( $upload_dir['baseurl'] );

			$filenoext = basename( $filename_import, '.zip' );  // absolute path to the directory where zipper.php is in (lowercase).
			$filenoext = basename( $filenoext, '.ZIP' );  // absolute path to the directory where zipper.php is in (when uppercase).

			$targetdir  = $path . $filenoext; // target directory.
			$targetzip  = $path . $filename; // target zip file.
			$target_url = $path_baseurl . $filenoext;

			if ( is_dir( $targetdir ) ) {
				recursive_rmdir( $targetdir );
			}
			mkdir( $targetdir, 0777 );

			if ( move_uploaded_file( $source, $targetzip ) ) {
				$zip = new ZipArchive();
				$x   = $zip->open( $targetzip );  // open the zip file to extract.
				if ( true === $x ) {
					$zip->extractTo( $targetdir ); // place in the directory with same name.
					$zip->close();

					unlink( $targetzip );
				}

				$theme_json_data     = $targetdir . '/export_page.json';
				$theme_json_data_url = $target_url . '/export_page.json';

				if ( file_exists( $theme_json_data ) ) {
					$file_theme_json = wp_remote_get( $theme_json_data_url, array( 'sslverify' => false ) );
					if ( is_wp_error( $file_theme_json ) ) {
						$error_code    = wp_remote_retrieve_response_code( $file_theme_json );
						$error_message = wp_remote_retrieve_response_message( $file_theme_json );
						wp_send_json_error( $error_message );
					}
					$data = json_decode( $file_theme_json['body'] ); // phpcs:ignore

					if ( ! empty( $data->type ) && 'landing-page' !== $data->type ) {
						$message = 'This does not appear to be a SeedProd landing page.';
						wp_send_json_error();
					}
					seedprod_pro_landing_import_json( $data );
					// remove the json file for security.
					wp_delete_file( $theme_json_data );

					wp_send_json( true );
				}
			} else {
				$message = 'There was a problem with the upload. Please try again.';
				wp_send_json_error();
			}
		} else {
			wp_send_json_error();
		}
	}
}


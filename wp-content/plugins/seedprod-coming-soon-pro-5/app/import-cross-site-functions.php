<?php
/**
 * Cross-site paste ajax functions
 */

if ( defined( 'DOING_AJAX' ) ) {
	add_action( 'wp_ajax_seedprod_pro_import_cross_site_paste', 'seedprod_pro_import_cross_site_paste' );
}

/**
 * At cross site paste uploading images and changing links.
 *
 *  @param string $data Page data.
 */

function seedprod_pro_process_image_filenames_import_cross_site( $data ) {

	$output = array(
		'data'   => '',
		'images' => array(),
	);

	$regex = '/(http)[^\s\'"]+?(png|jpg|jpeg|gif|ico|svg|bmp|tiff|webp)[^\s\'"]*?(?=[\'"])/i';

	$img_srcs = array();

	preg_match_all( $regex, $data, $img_srcs );

	// Eliminate duplicates & pair with extension match from above.
	$unique_img_srcs_extensions = array();
	foreach ( $img_srcs[0] as $index => $img_src ) {
		$unique_img_srcs_extensions[ $img_src ] = $img_srcs[2][ $index ];
	}

	// Need to decode data as WordPress is encoding special characters such as & to &amp; which is.
	// interfering with Unsplash URLs & making it hard to find / replace URLs in strings.
	$processed_data = wp_specialchars_decode( $data );

	foreach ( $unique_img_srcs_extensions as $old_url => $extension ) {
		$prefix = 'themebuilder';

		// Likewise, decode search string.
		$old_url_decoded = wp_specialchars_decode( $old_url );

		$alphanumeric_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$random_chars       = substr( str_shuffle( $alphanumeric_chars ), 0, 16 );

		$image_url = trim( urldecode( $old_url ) );

		$upload_dir       = wp_upload_dir();
		$contentdirimport = trailingslashit( $upload_dir['baseurl'] ) . 'seedprod-themes-imports/';

		$plugin_img_url = $old_url_decoded;

		$new_url_id = seedprod_pro_insert_attachment_from_url( $plugin_img_url );
		$new_url    = wp_get_attachment_url( $new_url_id );

		if ( is_wp_error( $new_url ) ) {
			echo esc_html( $new_url->get_error_message() );
		} else {

			$processed_data = str_replace( $old_url_decoded, $new_url, $processed_data );
		}

		$output['images'][] = array(
			'prefix'    => $prefix,
			'extension' => $extension,
			'old_url'   => $old_url_decoded,
			'new_url'   => $new_url,
		);
	}

	$output['data'] = $processed_data;

	return $output;
}


/**
 * Process Paste Cross-site JSON
 *
 * @param mixed $json_content Json content of theme template.
 * @return string.
 */
function seedprod_pro_landing_import_cross_site_json( $json_content = null ) {

	$full_code = $json_content;

	$processed_cross_site_data_import = seedprod_pro_process_image_filenames_import_cross_site( $full_code );

	$udpate_json_content = $processed_cross_site_data_import['data'];

	return $udpate_json_content;
}


/**
 * Cross site paste method
 */
function seedprod_pro_import_cross_site_paste() {

	if ( check_ajax_referer( 'seedprod_pro_import_cross_site_paste' ) ) {

		if ( ! current_user_can( apply_filters( 'seedprod_import_export', 'edit_others_posts' ) ) ) {
			wp_send_json_error();
		}

		$url = wp_nonce_url( 'admin.php?page=seedprod_pro_import_cross_site_paste', 'seedprod_import_cross_site_paste' );

		$data = isset( $_REQUEST['settings'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['settings'] ) ) : '';

		$cross_site_data = seedprod_pro_landing_import_cross_site_json( $data );
		echo wp_kses( $cross_site_data, 'post' );

		exit;
	}
}


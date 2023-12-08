<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

add_filter( 'attachment_fields_to_edit', 'vc_attachment_filter_field', 10, 2 );
add_filter( 'media_meta', 'vc_attachment_filter_media_meta', 10, 2 );
add_action( 'wp_ajax_vc_media_editor_add_image', 'vc_media_editor_add_image' );
add_action( 'wp_ajax_vc_media_editor_preview_image', 'vc_media_editor_preview_image' );

/**
 * @return array
 */
function vc_get_filters() {
	return array(
		'antique' => esc_html__( 'Antique', 'js_composer' ),
		'blackwhite' => esc_html__( 'Black & White', 'js_composer' ),
		'boost' => esc_html__( 'Boost', 'js_composer' ),
		'concentrate' => esc_html__( 'Concentrate', 'js_composer' ),
		'country' => esc_html__( 'Country', 'js_composer' ),
		'darken' => esc_html__( 'Darken', 'js_composer' ),
		'dream' => esc_html__( 'Dream', 'js_composer' ),
		'everglow' => esc_html__( 'Everglow', 'js_composer' ),
		'forest' => esc_html__( 'Forest', 'js_composer' ),
		'freshblue' => esc_html__( 'Fresh Blue', 'js_composer' ),
		'frozen' => esc_html__( 'Frozen', 'js_composer' ),
		'hermajesty' => esc_html__( 'Her Majesty', 'js_composer' ),
		'light' => esc_html__( 'Light', 'js_composer' ),
		'orangepeel' => esc_html__( 'Orange Peel', 'js_composer' ),
		'rain' => esc_html__( 'Rain', 'js_composer' ),
		'retro' => esc_html__( 'Retro', 'js_composer' ),
		'sepia' => esc_html__( 'Sepia', 'js_composer' ),
		'summer' => esc_html__( 'Summer', 'js_composer' ),
		'tender' => esc_html__( 'Tender', 'js_composer' ),
		'vintage' => esc_html__( 'Vintage', 'js_composer' ),
		'washed' => esc_html__( 'Washed', 'js_composer' ),
	);
}

/**
 * Add Image Filter field to media uploader
 *
 * @param array $form_fields , fields to include in attachment form
 * @param object $post , attachment record in database
 *
 * @return array $form_fields, modified form fields
 */
function vc_attachment_filter_field( $form_fields, $post ) {
	// don't add filter field, if image already has filter applied
	if ( get_post_meta( $post->ID, 'vc-applied-image-filter', true ) ) {
		return $form_fields;
	}

	$options = vc_get_filters();

	$html_options = '<option value="">' . esc_html__( 'None', 'js_composer' ) . '</option>';
	foreach ( $options as $value => $title ) {
		$html_options .= '<option value="' . esc_attr( $value ) . '">' . esc_html( $title ) . '</option>';
	}

	$form_fields['vc-image-filter'] = array(
		'label' => '',
		'input' => 'html',
		'html' => '
			<div style="display:none">
				<span class="vc-filter-label">' . esc_html__( 'Image filter', 'js_composer' ) . '</span>
				<select name="attachments[' . esc_attr( $post->ID ) . '][vc-image-filter]" id="attachments-' . esc_attr( $post->ID ) . '-vc-image-filter" data-vc-preview-image-filter="' . esc_attr( $post->ID ) . '">
					' . $html_options . '
				</select>
			</div>',
		'value' => get_post_meta( $post->ID, 'vc_image_filter', true ),
		'helps' => '',
	);

	return $form_fields;
}

/**
 * Apply filters to specified images
 *
 * If image(s) has filter specified via filters _POST param:
 * 1) copy it
 * 2) apply specified filter
 * 3) return new image id
 *
 * Required _POST params:
 * - array ids: array of attachment ids
 *
 * Optional _POST params:
 * - array filters: mapped array of ids and filters to apply
 *
 */
function vc_media_editor_add_image() {
	vc_user_access()->checkAdminNonce()->validateDie()->wpAny( 'upload_files' )->validateDie();

	require_once vc_path_dir( 'HELPERS_DIR', 'class-vc-image-filter.php' );
	$response = array(
		'success' => true,
		'data' => array(
			'ids' => array(),
		),
	);

	$filters = (array) vc_post_param( 'filters', array() );

	$ids = (array) vc_post_param( 'ids', array() );
	if ( ! $ids ) {
		wp_send_json( $response );
	}

	// default action is wp_handle_upload, which forces wp to check upload with is_uploaded_file()
	// override action to anything else to skip security checks
	$action = 'vc_handle_upload_imitation';

	$file_key = 0;
	$post_id = 0;
	$post_data = array();
	$overrides = array( 'action' => $action );
	$_POST = array( 'action' => $action );

	foreach ( $ids as $key => $attachment_id ) {
		if ( ! empty( $filters[ $attachment_id ] ) ) {
			$filter_name = $filters[ $attachment_id ];
		} else {
			continue;
		}

		$source_path = get_attached_file( $attachment_id );

		if ( empty( $source_path ) ) {
			continue;
		}

		$temp_path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . basename( $source_path );

		if ( ! copy( $source_path, $temp_path ) ) {
			continue;
		}

		$extension = strtolower( pathinfo( $temp_path, PATHINFO_EXTENSION ) );
		$mime_type = '';
		switch ( $extension ) {
			case 'jpeg':
			case 'jpg':
				$image = imagecreatefromjpeg( $temp_path );
				$mime_type = 'image/jpeg';
				break;

			case 'png':
				$image = imagecreatefrompng( $temp_path );
				$mime_type = 'image/png';
				break;

			case 'gif':
				$image = imagecreatefromgif( $temp_path );
				$mime_type = 'image/gif';
				break;

			default:
				$image = false;
		}

		if ( ! $image ) {
			continue;
		}

		$Filter = new vcImageFilter( $image );
		$Filter->$filter_name();

		if ( ! vc_save_gd_resource( $Filter->getImage(), $temp_path ) ) {
			continue;
		}

		$new_filename = basename( $temp_path, '.' . $extension ) . '-' . $filter_name . '.' . $extension;

		$_FILES = array(
			array(
				'name' => $new_filename,
				'type' => $mime_type,
				'tmp_name' => $temp_path,
				'error' => UPLOAD_ERR_OK,
				'size' => filesize( $temp_path ),
			),
		);

		$new_attachment_id = media_handle_upload( $file_key, $post_id, $post_data, $overrides );

		if ( ! $new_attachment_id || is_wp_error( $new_attachment_id ) ) {
			continue;
		}

		update_post_meta( $new_attachment_id, 'vc-applied-image-filter', $filter_name );

		$ids[ $key ] = $new_attachment_id;
	}

	$response['data']['ids'] = $ids;

	wp_send_json( $response );
}

/**
 * Generate filter preview
 *
 * Preview url is generated as data uri (base64)
 *
 * Required _POST params:
 * - string filter: filter name
 * - int attachment_id: attachment id
 *
 * @return void Results are sent out as json
 * @throws \Exception
 */
function vc_media_editor_preview_image() {
	vc_user_access()->checkAdminNonce()->validateDie()->wpAny( 'upload_files' )->validateDie();

	require_once vc_path_dir( 'HELPERS_DIR', 'class-vc-image-filter.php' );

	$response = array(
		'success' => true,
		'data' => array(
			'src' => '',
		),
	);

	$filter_name = vc_post_param( 'filter', '' );
	$attachment_id = vc_post_param( 'attachment_id', false );
	$preferred_size = vc_post_param( 'preferred_size', 'medium' );

	if ( ! $filter_name || ! $attachment_id ) {
		wp_send_json( $response );
	}

	$attachment_path = get_attached_file( $attachment_id );

	$attachment_details = wp_prepare_attachment_for_js( $attachment_id );

	if ( ! isset( $attachment_details['sizes'][ $preferred_size ] ) ) {
		$preferred_size = 'thumbnail';
	}

	$attachment_url = wp_get_attachment_image_src( $attachment_id, $preferred_size );

	if ( empty( $attachment_path ) || empty( $attachment_url[0] ) ) {
		wp_send_json( $response );
	}

	$source_path = dirname( $attachment_path ) . '/' . basename( $attachment_url[0] );

	$image = vc_get_gd_resource( $source_path );

	if ( ! $image ) {
		wp_send_json( $response );
	}

	$Filter = new vcImageFilter( $image );
	$Filter->$filter_name();

	$extension = strtolower( pathinfo( $source_path, PATHINFO_EXTENSION ) );

	ob_start();
	switch ( $extension ) {
		case 'jpeg':
		case 'jpg':
			imagejpeg( $Filter->getImage() );
			break;

		case 'png':
			imagepng( $Filter->getImage() );
			break;

		case 'gif':
			imagegif( $Filter->getImage() );
			break;
	}

	$data = ob_get_clean();

	// @codingStandardsIgnoreLine
	$response['data']['src'] = 'data:image/' . $extension . ';base64,' . base64_encode( $data );

	wp_send_json( $response );
}

/**
 * Read file from disk as GD resource
 *
 * @param string $file
 *
 * @return bool|resource
 */
function vc_get_gd_resource( $file ) {
	$extension = strtolower( pathinfo( $file, PATHINFO_EXTENSION ) );

	switch ( $extension ) {
		case 'jpeg':
		case 'jpg':
			return imagecreatefromjpeg( $file );

		case 'png':
			return imagecreatefrompng( $file );

		case 'gif':
			return imagecreatefromgif( $file );
	}

	return false;
}

/**
 * Save GD resource to file
 *
 * @param resource $resource
 * @param string $file
 *
 * @return bool
 */
function vc_save_gd_resource( $resource, $file ) {
	$extension = strtolower( pathinfo( $file, PATHINFO_EXTENSION ) );

	switch ( $extension ) {
		case 'jpeg':
		case 'jpg':
			return imagejpeg( $resource, $file );

		case 'png':
			return imagepng( $resource, $file );

		case 'gif':
			return imagegif( $resource, $file );
	}

	return false;
}

/**
 * Add "Filter: ..." meta field to attachment details box
 *
 * @param array $media_meta , meta to include in attachment form
 * @param object $post , attachment record in database
 *
 * @return array|string
 */
function vc_attachment_filter_media_meta( $media_meta, $post ) {
	$filter_name = get_post_meta( $post->ID, 'vc-applied-image-filter', true );
	if ( ! $filter_name ) {
		return $media_meta;
	}

	$filters = vc_get_filters();
	if ( ! isset( $filters[ $filter_name ] ) ) {
		return $media_meta;
	}

	$media_meta .= esc_html__( 'Filter:', 'js_composer' ) . ' ' . $filters[ $filter_name ];

	return $media_meta;
}

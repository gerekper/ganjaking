<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'ywcwat_backup_file_name' ) ) {

	function ywcwat_backup_file_name( $original_file_name ) {

		$upload_dir    = wp_upload_dir();
		$upload_dir    = $upload_dir['basedir'];
		$backup_url    = $upload_dir . '/' . YWCWAT_PRIVATE_DIR;
		$sub_directory = str_replace( $upload_dir, '', dirname( $original_file_name ) );
		$file_name     = str_replace( YWCWAT_BACKUP_FILE, '', basename( $original_file_name ) );

		$backup_file_name = $backup_url . $sub_directory . '/' . $file_name;

		return $backup_file_name;
	}
}

if ( ! function_exists( 'ywcwat_is_previous_backup_exist' ) ) {

	function ywcwat_is_previous_backup_exist( $file_name ) {

		$backup_file_name = dirname( $file_name ) . '/' . YWCWAT_BACKUP_FILE . basename( $file_name );

		if ( file_exists( $backup_file_name ) ) {
			return $backup_file_name;
		} else {
			return '';
		}
	}
}

if ( ! function_exists( 'ywcwat_backup_file' ) ) {

	function ywcwat_backup_file( $original_file ) {

		$original_file = str_replace( 'jpeg', 'jpg', $original_file );
		$file_name     = ywcwat_is_previous_backup_exist( $original_file );


		if ( $file_name !== '' ) {
			$original_file = $file_name;
		}

		$backup_file = ywcwat_backup_file_name( $original_file );

		if ( is_file( $original_file ) && ! is_file( $backup_file ) ) {

			if ( ! is_dir( dirname( $backup_file ) ) ) {

				wp_mkdir_p( dirname( $backup_file ) );
			}
			$result = copy( $original_file, $backup_file );

			//if exist delete old backup file ( previous plugin version )
			/*  if(  $file_name !=='' )
				  unlink( $file_name );
  */

			return $result;
		}

		return false;
	}
}


if ( ! function_exists( 'ywcwat_get_all_product_attach' ) ) {

	function ywcwat_get_all_product_attach() {

		global $wpdb;
		$result = $wpdb->get_results(
			"SELECT DISTINCT pm.meta_value as ID
                            FROM {$wpdb->postmeta} AS pm
                            INNER JOIN {$wpdb->posts} AS pr
                            ON pm.post_id= pr.ID 
                            INNER JOIN {$wpdb->posts} AS at
                            ON pm.meta_value = at.ID
                            WHERE pm.meta_key= '_thumbnail_id'
                            AND pr.post_type IN ('product', 'product_variation')
                            AND at.post_type='attachment'
                            AND at.post_mime_type LIKE 'image/%'
                            ORDER BY `meta_value` ASC"
		);
		$ids    = array();

		foreach ( $result as $attach_id ) {
			$ids[] = $attach_id->ID;
		}

		return $ids;
	}
}

if ( ! function_exists( 'ywcwat_generate_backup' ) ) {

	function ywcwat_generate_backup() {
		if ( isset( $_GET['gen_backup'] ) && 'yes' == $_GET['gen_backup'] ) {

			create_private_directory();
			$attach_ids = ywcwat_get_all_product_attach();

			foreach ( $attach_ids as $attach_id ) {


				$file_path = get_attached_file( $attach_id );

				ywcwat_backup_file( $file_path );
			}

			if ( function_exists( 'ywcwat_generate_backup_product_img_gallery' ) ) {
				ywcwat_generate_backup_product_img_gallery();
			}

			$redirect_url = remove_query_arg( 'gen_backup' );
			$redirect_url = add_query_arg( array( 'bakup_success' => 'yes' ), $redirect_url );
			wp_redirect( esc_url_raw( $redirect_url ) );
			die;
		}
	}
}

add_action( 'admin_init', 'ywcwat_generate_backup' );

if ( ! function_exists( 'create_private_directory' ) ) {
	/**
	 * create a private directory (if not exist)
	 * @author YITHEMES
	 * @since 1.0.7
	 */
	function create_private_directory() {

		$upload_dir = wp_upload_dir();
		$backup_url = $upload_dir['basedir'] . '/' . YWCWAT_PRIVATE_DIR;

		if ( ! is_dir( $backup_url ) ) {

			wp_mkdir_p( $backup_url );
		}
		if ( ! file_exists( $backup_url . '/.htaccess' ) ) {
			if ( $file_handle = @fopen( $backup_url . '/.htaccess', 'w' ) ) {
				fwrite( $file_handle, 'deny from all' );
				fclose( $file_handle );
			}
		}

		if ( ! file_exists( $backup_url . '/index.html' ) ) {
			if ( $file_handle = @fopen( $backup_url . '/index.html', 'w' ) ) {
				fwrite( $file_handle, '' );
				fclose( $file_handle );
			}
		}
	}


}
add_action( 'admin_init', 'create_private_directory' );

if ( ! function_exists( 'ywcwat_get_product_id_by_attach' ) ) {

	function ywcwat_get_product_id_by_attach( $attach_id ) {

		global $wpdb;

		$result = $wpdb->get_results(

			"SELECT {$wpdb->posts}.ID FROM {$wpdb->posts}
             WHERE {$wpdb->posts}.post_type IN ('product', 'product_variation')
             AND {$wpdb->posts}.ID IN (
                                  SELECT DISTINCT {$wpdb->postmeta}.post_id FROM {$wpdb->postmeta} INNER JOIN {$wpdb->posts} ON {$wpdb->postmeta}.post_id= {$wpdb->posts}.ID
                                  WHERE ( ( {$wpdb->postmeta}.meta_key= '_thumbnail_id' AND {$wpdb->postmeta}.meta_value =$attach_id )
                                          OR  ( {$wpdb->postmeta}.meta_key='_product_image_gallery' AND {$wpdb->postmeta}.meta_value REGEXP '$attach_id') ) )
                ORDER BY {$wpdb->posts}.ID ASC" );


		return $result;
	}
}

if ( ! function_exists( 'ywcwat_get_all_product_img_gallery' ) ) {

	function ywcwat_get_all_product_img_gallery() {

		global $wpdb;

		$result_gallery = $wpdb->get_results( "SELECT DISTINCT {$wpdb->postmeta}.meta_value AS ID FROM {$wpdb->postmeta} INNER JOIN {$wpdb->posts} ON {$wpdb->postmeta}.post_id= {$wpdb->posts}.ID
                                  WHERE {$wpdb->postmeta}.meta_key= '_product_image_gallery' AND {$wpdb->posts}.post_type ='product' AND {$wpdb->postmeta}.meta_value!='' ORDER BY {$wpdb->postmeta}.`meta_value` DESC" );

		return $result_gallery;
	}
}

if ( ! function_exists( 'ywcwat_generate_backup_product_img_gallery' ) ) {

	function ywcwat_generate_backup_product_img_gallery() {

		$result_gallery = ywcwat_get_all_product_img_gallery();

		foreach ( $result_gallery as $gallery ) {

			$attach_ids = explode( ',', $gallery->ID );

			foreach ( $attach_ids as $attach_id ) {

				$file_path = get_attached_file( $attach_id );

				ywcwat_backup_file( $file_path );
			}
		}

	}
}

if ( ! function_exists( 'ywcwat_get_font_name' ) ) {

	function ywcwat_get_font_name() {

		$font_ext   = apply_filters( 'ywcwat_font_types', array( 'ttf' ) );
		$font_dir   = YWCWAT_DIR . '/assets/fonts/';
		$fonts_name = array();


		$fonts = (array) glob( "$font_dir/*" );

		foreach ( $fonts as $font ) {

			$ext = pathinfo( $font, PATHINFO_EXTENSION );

			if ( in_array( $ext, $font_ext ) ) {
				$fonts_name[] = $font;
			}
		}

		return $fonts_name;
	}
}

if ( ! function_exists( 'ywcwat_Hex2RGB' ) ) {

	function ywcwat_Hex2RGB( $color ) {
		$color = str_replace( '#', '', $color );
		if ( strlen( $color ) != 6 ) {
			return array( 0, 0, 0 );
		}
		$rgb = array();
		for ( $x = 0; $x < 3; $x ++ ) {
			$rgb[ $x ] = hexdec( substr( $color, ( 2 * $x ), 2 ) );
		}

		return $rgb;
	}
}

if ( ! function_exists( ( 'ywcwat_get_attach_id_by_product' ) ) ) {

	function ywcwat_get_attach_id_by_product( $products ) {

		global $wpdb;
		$query = $wpdb->prepare( "SELECT DISTINCT {$wpdb->postmeta}.meta_value FROM {$wpdb->postmeta} INNER JOIN {$wpdb->posts} ON {$wpdb->postmeta}.post_id = {$wpdb->posts}.ID
                                  WHERE {$wpdb->postmeta}.meta_key IN ('_thumbnail_id', '_product_image_gallery') AND {$wpdb->postmeta}.meta_value!='' AND {$wpdb->postmeta}.post_id IN ( %d )", implode( ',', $products ) );

		return $wpdb->get_results( $query, ARRAY_A );
	}
}

/**@author YITHEMES
 * @since 1.0.0
 * @return array with woocommerce size name
 */
function yith_watermark_get_image_size() {
	return apply_filters( 'ywcwat_get_images_size', array(

		'woocommerce_single'    => __( 'WooCommerce Single', 'yith-woocommerce-watermark' ),
		'woocommerce_thumbnail'   => __( 'WooCommerce Thumbnail Catalog', 'yith-woocommerce-watermark' ),
		'woocommerce_gallery_thumbnail' => __( 'WooCommerce Gallery Thumbnail', 'yith-woocommerce-watermark' ),
		'full'           => __( 'Full Size (visible in modal)', 'yith-woocommerce-watermark' )
	) );
}

function yith_watermark_map_old_woocommerce_size_with_new_size( $old_size ){

	$map = array(
		'shop_single' => 'woocommerce_single',
		'shop_catalog' => 'woocommerce_thumbnail',
		'shop_thumbnail' => 'woocommerce_gallery_thumbnail'
	);

	return isset( $map[$old_size ] ) ? $map[$old_size]  : $old_size;
}

add_filter( 'ywcwat_get_images_size', 'ywcwat_add_images_size', 10, 1 );

function ywcwat_add_images_size( $watermark_sizes ) {

	if ( defined( 'YITH_YWZM_INIT' ) ) {

		$watermark_sizes['shop_magnifier'] = __( 'Shop Magnifier', 'yith-woocommerce-watermark' );
	}

	return $watermark_sizes;
}

add_filter( 'yit_src_file_path', 'ywcwat_change_image_url', 10, 1 );
add_filter( 'yit_dest_file_path', 'ywcwat_change_dest_image_url', 10, 4 );


function ywcwat_change_image_url( $image_url ) {

	$image_url = ywcwat_backup_file_name( $image_url );

	return $image_url;
}

/**
 * @param string $dest_path
 * @param string $image_path
 * @param int $attach_id
 * @param WP_Image_Editor $image
 *
 * @return string
 */
function ywcwat_change_dest_image_url( $dest_path, $image_path, $attach_id, $image ) {

	$info = pathinfo( $image_path );

	$dir       = $info['dirname'];
	$ext       = $info['extension'];
	$suffix    = $image->get_suffix();
	$name      = wp_basename( $image_path, ".$ext" );
	$dest_path = trailingslashit( $dir ) . "{$name}-{$suffix}.{$ext}";

	return $dest_path;
}


function yith_watermark_update_db_1_2_0(){

	$db_version = get_option( 'yith_watermark_db_version', '1.0.0' );

	if( version_compare( $db_version, '1.2.0', '<' ) ){

		$watermark_opt = get_option( 'ywcwat_watermark_select', array() );

		foreach( $watermark_opt as $key => $single_watermark ){

			$old_size = $single_watermark['ywcwat_watermark_sizes'];
			$single_watermark['ywcwat_watermark_sizes']  = yith_watermark_map_old_woocommerce_size_with_new_size( $old_size );
			$watermark_opt[$key] = $single_watermark;

		}

		$products_args = array(
			'post_type' => 'product',
			'post_status' => 'publish',
			'fields' => 'ids',
			'numberposts' => -1,
			'meta_query' => array(
				'relation' => 'AND',
				array(
					'key'     => '_ywcwat_product_watermark',
					'compare' => 'NOT IN',
					'value'	  => array( 'a:0:{}', '' ),
				),
			)
		);

		update_option( 'ywcwat_watermark_select', $watermark_opt );
		$product_ids = get_posts( $products_args );

		foreach( $product_ids as $product_id ){
			$old_watermarks = get_post_meta( $product_id, '_ywcwat_product_watermark', true );

			foreach( $old_watermarks as $key => $single_watermark ){

				$old_size = $single_watermark['ywcwat_watermark_sizes'];
				$single_watermark['ywcwat_watermark_sizes']  = yith_watermark_map_old_woocommerce_size_with_new_size( $old_size );
				$old_watermarks[$key] = $single_watermark;

			}

			update_post_meta( $product_id, '_ywcwat_product_watermark',$old_watermarks );
		}

		update_option( 'yith_watermark_db_version', '1.2.0' );
	}
}

add_action( 'admin_init', 'yith_watermark_update_db_1_2_0', 20 );

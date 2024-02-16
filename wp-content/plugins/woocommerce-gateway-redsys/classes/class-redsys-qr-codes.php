<?php
/**
 * Redsys QR Codes
 *
 * @package WooCommerce Redsys Gateway
 * @since 13.0.0
 * @author José Conti.
 * @author José Conti.
 * @link https://joseconti.com
 * @link https://redsys.joseconti.com
 * @link https://woo.com/products/redsys-gateway/
 * @license GNU General Public License v3.0
 * @license URI: http://www.gnu.org/licenses/gpl-3.0.html
 * @copyright 2013-2024 José Conti.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Redsys QR Codes
 */
class Redsys_QR_Codes {

	/**
	 * Bootstraps the class and hooks required actions & filters.
	 */
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woo.com/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2024 José Conti
	 */
	public static function init() {
		add_filter( 'upload_mimes', __CLASS__ . '::allow_svg' );
	}
	/**
	 *  Debug log
	 *
	 * @param string $log log.
	 */
	public function debug( $log ) {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			$debug = new WC_Logger();
			$debug->add( 'redsys-qr', $log );
		}
	}
	/**
	 * Return qr active
	 */
	public function redsys_qr_is_active() {
		$redsys_qr_is_active = get_option( 'redsys_qr_active', 'no' );
		if ( 'yes' === $redsys_qr_is_active ) {
			return true;
		} else {
			return false;
		}
	}
	/**
	 * Return User redsys.joseconti.com
	 */
	public function user_redsys_jc() {
		$redsys_qr_jc = get_option( 'redsys_qr_user_redsys_jc' );
		return $redsys_qr_jc;
	}
	/**
	 * Return QR Type
	 */
	public function type() {
		$redsys_qr_type = get_option( 'redsys_qr_type' );
		return str_replace( '_type', '', $redsys_qr_type );
	}
	/**
	 * Return QR Borer
	 */
	public function border() {
		$redsys_qr_border = get_option( 'redsys_qr_border' );
		return str_replace( '_border', '', $redsys_qr_border );
	}
	/**
	 * Return mcenter
	 */
	public function mcenter() {
		$redsys_qr_mcenter = get_option( 'redsys_qr_mcenter' );
		return str_replace( '_mcenter', '', $redsys_qr_mcenter );
	}
	/**
	 * Return QR Frame
	 */
	public function frame() {
		$redsys_qr_frame = get_option( 'redsys_qr_frame' );
		return str_replace( '_frame', '', $redsys_qr_frame );
	}
	/**
	 * Return QR Frame Label
	 */
	public function framelabel() {
		$redsys_qr_framelabel = get_option( 'redsys_qr_framelabel' );
		return $redsys_qr_framelabel;
	}
	/**
	 * Return QR Frame Label Font
	 */
	public function label_font() {
		$redsys_qr_label_font = get_option( 'redsys_qr_label_font' );
		return $redsys_qr_label_font;
	}
	/**
	 * Return QR backcolor
	 */
	public function backcolor() {
		$redsys_qr_backcolor = get_option( 'redsys_qr_backcolor' );
		return $redsys_qr_backcolor;
	}
	/**
	 * Return QR frontcolor
	 */
	public function frontcolor() {
		$redsys_qr_frontcolor = get_option( 'redsys_qr_frontcolor' );
		return $redsys_qr_frontcolor;
	}
	/**
	 * Return QR logo
	 */
	public function optionlogo() {
		$redsys_qr_optionlogo = get_option( 'redsys_qr_optionlogo' );
		return $redsys_qr_optionlogo;
	}
	/**
	 * Check is gradient active.
	 */
	public function gradient_active() {
		$redsys_qr_gradient_active = get_option( 'redsys_qr_gradient_active' );
		if ( 'yes' === $redsys_qr_gradient_active ) {
			return true;
		} else {
			return false;
		}
	}
	/**
	 * Return QR gradient color
	 */
	public function gradient_color() {
		$redsys_qr_gradient_color = get_option( 'redsys_qr_gradient_color' );
		return $redsys_qr_gradient_color;
	}
	/**
	 * Check is marker color active.
	 */
	public function marker_color_active() {
		$redsys_qr_marker_color_active = get_option( 'redsys_qr_marker_color_active' );
		if ( 'yes' === $redsys_qr_marker_color_active ) {
			return true;
		} else {
			return false;
		}
	}
	/**
	 * Return QR marker out color
	 */
	public function marker_out_color() {
		$redsys_qr_marker_out_color = get_option( 'redsys_qr_marker_out_color' );
		return $redsys_qr_marker_out_color;
	}
	/**
	 * Return QR marker in color
	 */
	public function marker_in_color() {
		$redsys_qr_marker_in_color = get_option( 'redsys_qr_marker_in_color' );
		return $redsys_qr_marker_in_color;
	}
	/**
	 * Return QR label redsys
	 */
	public function label_redsys() {
		$redsys_qr_label_redsys = get_option( 'redsys_qr_label_redsys' );
		return $redsys_qr_label_redsys;
	}
	/**
	 * Check if image exist
	 *
	 * @param string $path Path to image.
	 */
	public function check_image_exist( $path ) {

		$this->debug( 'check_image_exist()' );
		$this->debug( '$path : ' . $path );
		if ( file_exists( $path ) ) {
			$this->debug( 'return TRUE' );
			return true;
		}
		$this->debug( 'return FALSE' );
		return false;
	}
	/**
	 * Create image name
	 *
	 * @param string $product_id Product ID.
	 */
	public function create_name_image( $product_id ) {

		$this->debug( 'create_name_image()' );
		$this->debug( '$product_id : ' . $product_id );
		$upload_dir = wp_upload_dir();
		if ( wp_mkdir_p( $upload_dir['path'] ) ) {
			$file = $upload_dir['path'];
			$url  = $upload_dir['url'];
		} else {
			$file = $upload_dir['basedir'];
			$url  = $upload_dir['url'];
		}
		$this->debug( '$file : ' . $file );
		$i = 0;
		for ( $i = 0; $i < 100; $i++ ) {
			$image_name = 'image_' . $product_id . '_' . $i . '.svg';
			$path       = $file . '/' . $image_name;
			$url_img    = $url . '/' . $image_name;
			$this->debug( '$image_name : ' . $image_name );
			$this->debug( '$path : ' . $path );
			if ( ! $this->check_image_exist( $path ) ) {
				return array(
					'image_name' => $image_name,
					'path'       => $path,
					'url_img'    => $url_img,
				);
			}
		}
	}
	/**
	 * Return QR label
	 *
	 * @param string $link Link to QR.
	 * @param string $type2 Type of QR.
	 * @param string $product_id Product ID.
	 */
	public function get_qr( $link, $type2, $product_id ) {
		global $wp_filesystem;

		$this->debug( 'get_qr()' );

		require_once ABSPATH . '/wp-admin/includes/file.php';

		WP_Filesystem();

		if ( ! WCRed()->check_product_key() ) {
			return 'Error-5';
		}
		$api_link            = 'https://api.joseconti.com/v1/qr/';
		$user_redsys_jc      = $this->user_redsys_jc();
		$type                = $this->type();
		$border              = $this->border();
		$mcenter             = $this->mcenter();
		$frame               = $this->frame();
		$framelabel          = $this->framelabel();
		$label_font          = $this->label_font();
		$backcolor           = $this->backcolor();
		$frontcolor          = $this->frontcolor();
		$optionlogo          = $this->optionlogo();
		$gradient_active     = $this->gradient_active();
		$gradient_color      = $this->gradient_color();
		$marker_color_active = $this->marker_color_active();
		$marker_out_color    = $this->marker_out_color();
		$marker_in_color     = $this->marker_in_color();

		$this->debug( '$api_link : ' . $api_link );
		$this->debug( '$user_redsys_jc : ' . $user_redsys_jc );
		$this->debug( '$type : ' . $type );
		$this->debug( '$border : ' . $border );
		$this->debug( '$mcenter : ' . $mcenter );
		$this->debug( '$frame : ' . $frame );
		$this->debug( '$framelabel : ' . $framelabel );
		$this->debug( '$label_font : ' . $label_font );
		$this->debug( '$backcolor : ' . $backcolor );
		$this->debug( '$frontcolor : ' . $frontcolor );
		$this->debug( '$optionlogo : ' . $optionlogo );
		$this->debug( '$gradient_active : ' . $gradient_active );
		$this->debug( '$gradient_color : ' . $gradient_color );
		$this->debug( '$marker_color_active : ' . $marker_color_active );
		$this->debug( '$marker_out_color : ' . $marker_out_color );
		$this->debug( '$marker_in_color : ' . $marker_in_color );
		$this->debug( '$type2 : ' . $type2 );

		if ( 1 === (int) $marker_color_active ) {
			$marker_color_active = 'on';
		} else {
			$marker_color_active = '';
		}

		if ( 1 === (int) $gradient_active ) {
			$gradient_active = 'on';
		} else {
			$gradient_active = '';
		}

		$data = array(
			'user'             => $user_redsys_jc,
			'link'             => $link,
			'section'          => $type2,
			'pattern'          => $type,
			'marker_out'       => $border,
			'marker_in'        => $mcenter,
			'outer_frame'      => $frame,
			'framelabel'       => $framelabel,
			'label_font'       => $label_font,
			'backcolor'        => $backcolor,
			'frontcolor'       => $frontcolor,
			'optionlogo'       => $optionlogo,
			'gradient'         => $gradient_active,
			'gradient_color'   => $gradient_color,
			'markers_color'    => $marker_color_active,
			'marker_out_color' => $marker_out_color,
			'marker_in_color'  => $marker_in_color,
		);
		$this->debug( print_r( $data, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
		$response = wp_remote_post(
			$api_link,
			array(
				'method'      => 'POST',
				'timeout'     => 45,
				'httpversion' => '1.0',
				'user-agent'  => 'WooCommerce',
				'body'        => $data,
			)
		);

		$response_code = wp_remote_retrieve_response_code( $response );
		$response_body = wp_remote_retrieve_body( $response );
		$result        = json_decode( $response_body, true );
		$decoded       = base64_decode( $result['content'] ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode

		if ( 'Error-1' === $decoded || 'Error-2' === $decoded || 'Error-3' === $decoded || ! isset( $result['content'] ) || empty( $decoded ) || 200 !== $response_code ) {
			if ( 200 !== $response_code ) {
				$decoded = 'Error-4';
			}
			return $decoded;
		}

		$upload_dir = wp_upload_dir();
		$image      = $this->create_name_image( $product_id );

		$this->debug( '$image_path : ' . print_r( $image, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
		$this->debug( 'get_qr()' );

		$file_name  = $image['image_name'];
		$file_path  = $image['path'];
		$file_image = $image['url_img'];

		$wp_filesystem->put_contents( $file_path, $decoded );
		$wp_filetype = wp_check_filetype( $file_name, null );
		$this->debug( '$file_name: ' . $file_name );
		$this->debug( '$filetype: ' . print_r( $wp_filetype, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
		$this->debug( '$upload_dir: ' . print_r( $upload_dir, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
		$this->debug( '$file_name: ' . $file_name );
		$attachment = array(
			'post_mime_type' => $wp_filetype['type'],
			'post_title'     => sanitize_file_name( $file_name ),
			'post_content'   => '',
			'post_status'    => 'inherit',
		);
		$this->debug( '$attachment: ' . print_r( $attachment, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
		$attach_id = wp_insert_attachment( $attachment, $file_path, $product_id );
		require_once ABSPATH . 'wp-admin/includes/image.php';
		$attach_data = wp_generate_attachment_metadata( $attach_id, $file_path );
		wp_update_attachment_metadata( $attach_id, $attach_data );
		return $file_image;
	}
	/**
	 * Allow SVG in uploads
	 *
	 * @param array $mimes Mimes.
	 */
	public static function allow_svg( $mimes ) {
		$mimes['svg'] = 'image/svg+xml';
		return $mimes;
	}
}
Redsys_QR_Codes::init();

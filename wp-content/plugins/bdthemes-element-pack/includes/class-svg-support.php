<?php

namespace ElementPack\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class SVG_Support {

	/**
	 * A reference to an instance of this class.
	 * @var   object
	 */
	private static $instance = null;

	public function init() {
		add_filter( 'upload_mimes', [ $this, 'set_svg_mimes' ] );
		add_filter( 'wp_prepare_attachment_for_js', [ $this, 'prepare_attachment_modal_for_svg' ], 10, 3 );
		add_filter( 'wp_generate_attachment_metadata', [ $this, 'svg_attachment_metadata' ], 10, 3 );
		add_filter( 'wp_get_attachment_metadata', [ $this, 'get_attachment_metadata' ], 10, 2 );
	}

	/**
	 * Add Mime Types
	 * @return array
	 */
	function set_svg_mimes( $mimes = array() ) {

		if ( current_user_can( 'administrator' ) ) {

			// allow SVG file upload
			$mimes['svg']  = 'image/svg+xml';
			$mimes['svgz'] = 'image/svg+xml';

			return $mimes;

		} else {

			return $mimes;

		}

	}

	function prepare_attachment_modal_for_svg( $response, $attachment, $meta ) {

		if ( $response['mime'] == 'image/svg+xml' && empty( $response['sizes'] ) ) {

			$svg_path = get_attached_file( $attachment->ID );

			if ( ! file_exists( $svg_path ) ) {
				// If SVG is external, use the URL instead of the path
				$svg_path = $response['url'];
			}

			$dimensions = $this->get_dimensions( $svg_path );

			$response['sizes'] = array(
				'full' => array(
					'url'         => $response['url'],
					'width'       => $dimensions->width,
					'height'      => $dimensions->height,
					'orientation' => $dimensions->width > $dimensions->height ? 'landscape' : 'portrait'
				)
			);

		}

		return $response;

	}

	/**
	 * Generate attachment metadata (Thanks @surml)
	 * Fixes Illegal String Offset Warning for Height & Width
	 */
	public function svg_attachment_metadata( $metadata, $attachment_id ) {

		$mime = get_post_mime_type( $attachment_id );

		if ( $mime == 'image/svg+xml' ) {

			$svg_path   = get_attached_file( $attachment_id );
			$upload_dir = wp_upload_dir();
			// get the path relative to /uploads/ - found no better way:
			$relative_path = str_replace( $upload_dir['basedir'], '', $svg_path );
			$filename      = basename( $svg_path );

			$dimensions = $this->get_dimensions( $svg_path );

			$metadata = array(
				'width'  => intval( $dimensions->width ),
				'height' => intval( $dimensions->height ),
				'file'   => $relative_path
			);

			// Might come in handy to create the sizes array too - But it's not needed for this workaround! Always links to original svg-file => Hey, it's a vector graphic! ;)
			$sizes = array();
			foreach ( get_intermediate_image_sizes() as $s ) {
				$sizes[ $s ] = array( 'width' => '', 'height' => '', 'crop' => false );
				if ( isset( $_wp_additional_image_sizes[ $s ]['width'] ) ) {
					$sizes[ $s ]['width'] = intval( $_wp_additional_image_sizes[ $s ]['width'] );
				} // For theme-added sizes
				else {
					$sizes[ $s ]['width'] = get_option( "{$s}_size_w" );
				} // For default sizes set in options
				if ( isset( $_wp_additional_image_sizes[ $s ]['height'] ) ) {
					$sizes[ $s ]['height'] = intval( $_wp_additional_image_sizes[ $s ]['height'] );
				} // For theme-added sizes
				else {
					$sizes[ $s ]['height'] = get_option( "{$s}_size_h" );
				} // For default sizes set in options
				if ( isset( $_wp_additional_image_sizes[ $s ]['crop'] ) ) {
					$sizes[ $s ]['crop'] = intval( $_wp_additional_image_sizes[ $s ]['crop'] );
				} // For theme-added sizes
				else {
					$sizes[ $s ]['crop'] = get_option( "{$s}_crop" );
				} // For default sizes set in options

				$sizes[ $s ]['file']      = $filename;
				$sizes[ $s ]['mime-type'] = 'image/svg+xml';
			}
			$metadata['sizes'] = $sizes;
		}

		return $metadata;
	}


	// Fix image widget PHP warnings
	public function get_attachment_metadata( $data, $attachment_id ) {

		$mime = get_post_mime_type( $attachment_id );
        $type = current( explode( '/', $mime ) );
        if ( $type !== 'image' ) {
            return $data;
        }

        if ( ! isset( $data['width'] ) || ! isset( $data['height'] ) ) {
            return false;
        }

        return $data;

	}

	private function get_dimensions( $svg ) {

		$svg = simplexml_load_file( $svg );

		if ( $svg === false ) {

			$width  = '0';
			$height = '0';

		} else {

			$attributes = $svg->attributes();
			$width      = (string) $attributes->width;
			$height     = (string) $attributes->height;

		}

		return (object) array( 'width' => $width, 'height' => $height );

	}

	/**
	 * Returns the instance.
	 * @return object
	 * @since  3.0.0
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

}
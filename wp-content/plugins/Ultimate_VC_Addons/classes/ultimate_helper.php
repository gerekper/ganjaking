<?php
/**
 * BSF CORE common functions.
 *
 * @package BSF CORE commom functions.
 */

if ( ! function_exists( 'bsf_get_option' ) ) {
	/**
	 * Bsf_get_option.
	 *
	 * @param bool $request Request.
	 */
	function bsf_get_option( $request = false ) {
		$bsf_options = get_option( 'bsf_options' );
		if ( ! $request ) {
			return $bsf_options;
		} else {
			return ( isset( $bsf_options[ $request ] ) ) ? $bsf_options[ $request ] : false;
		}
	}
}
if ( ! function_exists( 'bsf_update_option' ) ) {
	/**
	 * Bsf_update_option.
	 *
	 * @param bool $request Request.
	 * @param bool $value Value.
	 */
	function bsf_update_option( $request, $value ) {
		$bsf_options             = get_option( 'bsf_options' );
		$bsf_options[ $request ] = $value;
		return update_option( 'bsf_options', $bsf_options );
	}
}
if ( ! function_exists( 'uavc_hex2rgb' ) ) {
	/**
	 * Ultimate_hex2rgb.
	 *
	 * @param string $hex Hex.
	 * @param string $opacity Opacity.
	 */
	function uavc_hex2rgb( $hex, $opacity = 1 ) {
		$hex = str_replace( '#', '', $hex );
		if ( 33 == strlen( $hex ) ) {
			$r = hexdec( substr( $hex, 0, 1 ) . substr( $hex, 0, 1 ) );
			$g = hexdec( substr( $hex, 1, 1 ) . substr( $hex, 1, 1 ) );
			$b = hexdec( substr( $hex, 2, 1 ) . substr( $hex, 2, 1 ) );
		} else {
			$r = hexdec( substr( $hex, 0, 2 ) );
			$g = hexdec( substr( $hex, 2, 2 ) );
			$b = hexdec( substr( $hex, 4, 2 ) );
		}
		$rgba = 'rgba(' . $r . ',' . $g . ',' . $b . ',' . $opacity . ')';
		return $rgba; // returns an array with the rgb values.
	}
}
/**
 * Get_ultimate_vc_responsive_media_css.
 *
 * @param array $args Arguments.
 */
function get_ultimate_vc_responsive_media_css( $args ) {
	$content = '';
	if ( isset( $args ) && is_array( $args ) ) {
		// get targeted css class/id from array.
		if ( array_key_exists( 'target', $args ) ) {
			if ( ! empty( $args['target'] ) ) {
				$content .= " data-ultimate-target='" . esc_attr( $args['target'] ) . "' ";
			}
		}

		// get media sizes.
		if ( array_key_exists( 'media_sizes', $args ) ) {
			if ( ! empty( $args['media_sizes'] ) ) {
				$content .= " data-responsive-json-new='" . wp_json_encode( $args['media_sizes'] ) . "' ";
			}
		}
	}
	return $content;
}

if ( ! function_exists( 'uavc_img_single_init' ) ) {
	/**
	 * Ult_img_single_init.
	 *
	 * @param string $content Content.
	 * @param string $data Data.
	 * @param string $size Size.
	 */
	function uavc_img_single_init( $content = null, $data = '', $size = 'full' ) {

		$final = '';

		if ( '' != $content && 'null|null' != $content ) {

			// Create an array.
			$mainstr = explode( '|', (string) $content );
			$string  = '';
			$mainarr = array();

			$temp_id  = $mainstr[0];
			$temp_url = ( isset( $mainstr[1] ) ) ? $mainstr[1] : 'null';

			if ( ! empty( $mainstr ) && is_array( $mainstr ) ) {
				foreach ( $mainstr as $key => $value ) {
					if ( ! empty( $value ) ) {
						if ( stripos( $value, '^' ) !== false ) {
							$tmvav_array = explode( '^', $value );
							if ( is_array( $tmvav_array ) && ! empty( $tmvav_array ) ) {
								if ( ! empty( $tmvav_array ) ) {
									if ( isset( $tmvav_array[0] ) ) {
										$mainarr[ $tmvav_array[0] ] = ( isset( $tmvav_array[1] ) ) ? $tmvav_array[1] : '';
									}
								}
							}
						} else {
							$mainarr['id']  = $temp_id;
							$mainarr['url'] = $temp_url;
						}
					}
				}
			}

			if ( '' != $data ) {
				switch ( $data ) {
					case 'url':     // First  - Priority for ID.
						if ( ! empty( $mainarr['id'] ) && 'null' != $mainarr['id'] ) {

							$image_url = '';
							// Get image URL, If input is number - e.g. 100x48 / 140x40 / 350x53.
							if ( 1 === preg_match( '/^\d/', $size ) ) {
								$size = explode( 'x', $size );

								// resize image using vc helper function - wpb_resize.
								$img = wpb_resize( $mainarr['id'], null, $size[0], $size[1], true );
								if ( $img ) {
									$image_url = $img['url'];
								}
							} else {

								// Get image URL, If input is string - [thumbnail, medium, large, full].
								$hasimage  = wp_get_attachment_image_src( $mainarr['id'], $size ); // returns an array.
								$image_url = isset( $hasimage[0] ) ? $hasimage[0] : '';
							}

							if ( isset( $image_url ) && ! empty( $image_url ) ) {
								$final = $image_url;
							} else {

								// Second - Priority for URL - get {image from url}.
								if ( isset( $mainarr['url'] ) ) {
									$final = uavc_get_url( $mainarr['url'] );
								}
							}
						} else {
							// Second - Priority for URL - get {image from url}.
							if ( isset( $mainarr['url'] ) ) {
								$final = uavc_get_url( $mainarr['url'] );
							}
						}
						break;
					case 'title':
						$final = isset( $mainarr['title'] ) ? $mainarr['title'] : get_post_meta( $mainarr['id'], '_wp_attachment_image_title', true );
						break;
					case 'caption':
						$final = isset( $mainarr['caption'] ) ? $mainarr['caption'] : get_post_meta( $mainarr['id'], '_wp_attachment_image_caption', true );
						break;
					case 'alt':
						$final = isset( $mainarr['alt'] ) ? $mainarr['alt'] : get_post_meta( $mainarr['id'], '_wp_attachment_image_alt', true );
						break;
					case 'description':
						$final = isset( $mainarr['description'] ) ? $mainarr['description'] : get_post_meta( $mainarr['id'], '_wp_attachment_image_description', true );
						break;
					case 'json':
						$final = wp_json_encode( $mainarr );
						break;

					case 'sizes':
						$img_size = uavc_get_image_squere_size( $img_id, $img_size );

						$img   = wpb_getImageBySize(
							array(
								'attach_id'  => $img_id,
								'thumb_size' => $img_size,
								'class'      => 'vc_single_image-img',
							)
						);
						$final = $img;
						break;

					case 'array':
					default:
						$final = $mainarr;
						break;

				}
			}
		}

		return $final;
	}
	add_filter( 'ult_get_img_single', 'uavc_img_single_init', 10, 3 );
}

if ( ! function_exists( 'uavc_get_url' ) ) {
	/**
	 * Ult_get_url.
	 *
	 * @param string $img Img.
	 */
	function uavc_get_url( $img ) {
		if ( isset( $img ) && ! empty( $img ) ) {
			return $img;
		}
	}
}

// USE THIS CODE TO SUPPORT CUSTOM SIZE OPTION.
if ( ! function_exists( 'uavc_get_image_squere_size' ) ) {
	/**
	 * GetImageSquereSize.
	 *
	 * @param string $img_id Image ID.
	 * @param string $img_size Image Size.
	 */
	function uavc_get_image_squere_size( $img_id, $img_size ) {
		if ( preg_match_all( '/(\d+)x(\d+)/', $img_size, $sizes ) ) {
			$exact_size = array(
				'width'  => isset( $sizes[1][0] ) ? $sizes[1][0] : '0',
				'height' => isset( $sizes[2][0] ) ? $sizes[2][0] : '0',
			);
		} else {
			$image_downsize = image_downsize( $img_id, $img_size );
			$exact_size     = array(
				'width'  => $image_downsize[1],
				'height' => $image_downsize[2],
			);
		}

		if ( isset( $exact_size['width'] ) && (int) $exact_size['width'] !== (int) $exact_size['height'] ) {
			$img_size = (int) $exact_size['width'] > (int) $exact_size['height']
				? $exact_size['height'] . 'x' . $exact_size['height']
				: $exact_size['width'] . 'x' . $exact_size['width'];
		}

		return $img_size;
	}
}

/* Ultimate Box Shadow */
if ( ! function_exists( 'uavc_get_box_shadow' ) ) {
	/**
	 * GetImageSquereSize.
	 *
	 * @param string $content Content.
	 * @param string $data Image Data.
	 */
	function uavc_get_box_shadow( $content = null, $data = '' ) {
		// e.g.    horizontal:14px|vertical:20px|blur:30px|spread:40px|color:#81d742|style:inset|.
		$final = '';

		if ( '' != $content ) {

			// Create an array.
			$mainstr = explode( '|', $content );
			$string  = '';
			$mainarr = array();
			if ( ! empty( $mainstr ) && is_array( $mainstr ) ) {
				foreach ( $mainstr as $key => $value ) {
					if ( ! empty( $value ) ) {
						$string = explode( ':', $value );
						if ( is_array( $string ) ) {
							if ( ! empty( $string[1] ) && 'outset' != $string[1] ) {
								$mainarr[ $string[0] ] = $string[1];
							}
						}
					}
				}
			}

			$rm_bar   = str_replace( '|', '', $content );
			$rm_colon = str_replace( ':', ' ', $rm_bar );
			$rmkeys   = str_replace( 'horizontal', '', $rm_colon );
			$rmkeys   = str_replace( 'vertical', '', $rmkeys );
			$rmkeys   = str_replace( 'blur', '', $rmkeys );
			$rmkeys   = str_replace( 'spread', '', $rmkeys );
			$rmkeys   = str_replace( 'color', '', $rmkeys );
			$rmkeys   = str_replace( 'style', '', $rmkeys );
			$rmkeys   = str_replace( 'outset', '', $rmkeys );     // Remove outset from style - To apply {outset} box. shadow.

			if ( '' != $data ) {
				switch ( $data ) {
					case 'data':
						$final = $rmkeys;
						break;
					case 'array':
						$final = $mainarr;
						break;
					case 'css':
					default:
						$final = 'box-shadow:' . $rmkeys . ';';
						break;
				}
			} else {
				$final = 'box-shadow:' . $rmkeys . ';';
			}
		}

		return $final;
	}

	add_filter( 'ultimate_getboxshadow', 'uavc_get_box_shadow', 10, 3 );
}

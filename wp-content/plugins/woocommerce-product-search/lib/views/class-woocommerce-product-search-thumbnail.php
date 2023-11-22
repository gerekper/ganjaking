<?php
/**
 * class-woocommerce-product-search-thumbnail.php
 *
 * Copyright (c) "kento" Karim Rahimpur www.itthinx.com
 *
 * This code is provided subject to the license granted.
 * Unauthorized use and distribution is prohibited.
 * See COPYRIGHT.txt and LICENSE.txt
 *
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * This header and all notices must be kept intact.
 *
 * @author itthinx
 * @package woocommerce-product-search
 * @since 1.0.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

use com\itthinx\woocommerce\search\engine\Settings;

/**
 * Thumbnails
 */
class WooCommerce_Product_Search_Thumbnail {

	const THUMBNAIL        = 'product-search-thumbnail';
	const THUMBNAIL_WIDTH  = 'product-search-thumbnail-width';
	const THUMBNAIL_HEIGHT = 'product-search-thumbnail-height';
	const THUMBNAIL_DEFAULT_WIDTH  = 32;
	const THUMBNAIL_DEFAULT_HEIGHT = 32;
	const THUMBNAIL_MAX_DIM        = 1024;
	const THUMBNAIL_CROP         = 'product-search-thumbnail-crop';
	const THUMBNAIL_DEFAULT_CROP = true;
	const THUMBNAIL_USE_PLACEHOLDER  = 'product-search-thumbnail-placeholder';
	const THUMBNAIL_USE_PLACEHOLDER_DEFAULT = true;

	/**
	 * Adds our filters and actions.
	 */
	public static function init() {
		add_action( 'after_setup_theme', array( __CLASS__, 'after_setup_theme' ) );
		add_filter( 'image_downsize', array( __CLASS__, 'image_downsize' ), 10, 3 );

	}

	/**
	 * Handler for after_setup_theme
	 */
	public static function after_setup_theme() {

		$settings = Settings::get_instance();
		$thumbnail_width  = $settings->get( self::THUMBNAIL_WIDTH, self::THUMBNAIL_DEFAULT_WIDTH );
		$thumbnail_height = $settings->get( self::THUMBNAIL_HEIGHT, self::THUMBNAIL_DEFAULT_HEIGHT );
		$thumbnail_crop   = $settings->get( self::THUMBNAIL_CROP, self::THUMBNAIL_DEFAULT_CROP );
		if ( !has_image_size( self::thumbnail_size_name() ) ) {
			add_image_size( self::thumbnail_size_name(), intval( $thumbnail_width ), intval( $thumbnail_height ), $thumbnail_crop );
		}

		$product_taxonomies = self::get_product_taxonomies();
		foreach( $product_taxonomies as $product_taxonomy ) {
			if ( $taxonomy = get_taxonomy( $product_taxonomy ) ) {
				$thumbnail_width  = $settings->get( $taxonomy->name . '-' . self::THUMBNAIL_WIDTH, self::THUMBNAIL_DEFAULT_WIDTH );
				$thumbnail_height = $settings->get( $taxonomy->name . '-' . self::THUMBNAIL_HEIGHT, self::THUMBNAIL_DEFAULT_HEIGHT );
				$thumbnail_crop   = $settings->get( $taxonomy->name . '-' . self::THUMBNAIL_CROP, self::THUMBNAIL_DEFAULT_CROP );
				if ( !has_image_size( self::thumbnail_size_name( $taxonomy->name . '-' ) ) ) {
					add_image_size( self::thumbnail_size_name( $taxonomy->name . '-' ), intval( $thumbnail_width ), intval( $thumbnail_height ), $thumbnail_crop );
				}
			}
		}
	}

	/**
	 * Size name
	 *
	 * @return string
	 */
	public static function thumbnail_size_name( $prefix = '' ) {

		$settings = Settings::get_instance();
		$thumbnail_width  = $settings->get( $prefix . self::THUMBNAIL_WIDTH, self::THUMBNAIL_DEFAULT_WIDTH );
		$thumbnail_height = $settings->get( $prefix . self::THUMBNAIL_HEIGHT, self::THUMBNAIL_DEFAULT_HEIGHT );
		return sprintf( self::THUMBNAIL . '-%dx%d', intval( $thumbnail_width ), intval( $thumbnail_height ) );
	}

	/**
	 * Size names
	 *
	 * @return array of size names
	 */
	public static function get_thumbnail_size_names() {

		$result = array( self::thumbnail_size_name() );
		$product_taxonomies = self::get_product_taxonomies();
		foreach( $product_taxonomies as $product_taxonomy ) {
			if ( $taxonomy = get_taxonomy( $product_taxonomy ) ) {
				$result[] = self::thumbnail_size_name( $taxonomy->name . '-' );
			}
		}
		return $result;
	}

	/**
	 * Image size
	 *
	 * @return string[]
	 */
	public static function get_image_size( $prefix = '' ) {

		$settings = Settings::get_instance();
		$thumbnail_width  = $settings->get( $prefix . self::THUMBNAIL_WIDTH, self::THUMBNAIL_DEFAULT_WIDTH );
		$thumbnail_height = $settings->get( $prefix . self::THUMBNAIL_HEIGHT, self::THUMBNAIL_DEFAULT_HEIGHT );
		$thumbnail_crop   = $settings->get( $prefix . self::THUMBNAIL_CROP, self::THUMBNAIL_DEFAULT_CROP );
		return array(
			'width'  => $thumbnail_width,
			'height' => $thumbnail_height,
			'crop'   => $thumbnail_crop
		);
	}

	/**
	 * Image downsize
	 *
	 * @param boolean $foo false
	 * @param int $id image ID
	 * @param string $size desired image size descriptor
	 *
	 * @return array|boolean image result
	 */
	public static function image_downsize( $foo, $id, $size ) {

		$result = false;

		if ( in_array( $size, self::get_thumbnail_size_names() ) ) {

			self::after_setup_theme();

			require_once ABSPATH . '/wp-admin/includes/image.php';

			if ( !empty( $size ) && wp_attachment_is_image( $id ) ) {
				$regenerate = false;

				if ( $intermediate = image_get_intermediate_size( $id, $size ) ) {
					$img_url = $intermediate['url'];
					if ( empty( $img_url ) && !empty( $intermediate['file'] ) ) {
						$original_file_url = wp_get_attachment_url( $id );
						if ( !empty( $original_file_url ) ) {
							$img_url = path_join( dirname( $original_file_url ), $intermediate['file'] );
						}
					}
					$width = $intermediate['width'];
					$height = $intermediate['height'];
					$is_intermediate = true;
				}

				if ( isset( $img_url ) ) {

					list( $width, $height ) = image_constrain_size_for_editor( $width, $height, $size );
					$result = array( $img_url, $width, $height, $is_intermediate );

					$prefix = '';
					if ( $size !== self::thumbnail_size_name() ) {
						$product_taxonomies = self::get_product_taxonomies();
						foreach( $product_taxonomies as $product_taxonomy ) {
							if ( $taxonomy = get_taxonomy( $product_taxonomy ) ) {
								if ( $size == self::thumbnail_size_name( $taxonomy->name . '-' ) ) {
									$prefix = $taxonomy->name . '-';
								}
							}
						}
					}
					$settings = Settings::get_instance();
					$thumbnail_width  = $settings->get( $prefix . self::THUMBNAIL_WIDTH, self::THUMBNAIL_DEFAULT_WIDTH );
					$thumbnail_height = $settings->get( $prefix . self::THUMBNAIL_HEIGHT, self::THUMBNAIL_DEFAULT_HEIGHT );
					$thumbnail_crop   = $settings->get( $prefix . self::THUMBNAIL_CROP, self::THUMBNAIL_DEFAULT_CROP );

					switch ( $thumbnail_crop ) {

						case true :
							if ( ( $width != $thumbnail_width ) || ( $height != $thumbnail_height ) ) {
								$regenerate = true;
							}
							break;

						case false :
							$meta = wp_get_attachment_metadata( $id );
							$r1 = round( floatval( $width ) / floatval( $height > 0 ? $height : 1 ), 3 );
							$r2 = round( floatval( $meta['width'] ) / floatval( $meta['height'] > 0 ? $meta['height'] : 1 ), 3 );
							if ( $r2 == 0 ) {
								$r2 = 0.001;
							}

							if ( abs( $r1 / $r2 - 1 ) > 0.25 ) {
								$regenerate = true;
							}
							break;
					}
				}

				if ( !$result || $regenerate ) {
					$meta = wp_get_attachment_metadata( $id );
					$upload_dir = wp_upload_dir();
					$img_file = get_attached_file( $id );
					$new_meta = wp_generate_attachment_metadata( $id, $img_file );
					wp_update_attachment_metadata( $id, $new_meta );

					if ( $intermediate = image_get_intermediate_size( $id, $size ) ) {
						$img_url = $intermediate['url'];
						if ( empty( $img_url ) && !empty( $intermediate['file'] ) ) {
							$original_file_url = wp_get_attachment_url( $id );
							if ( !empty( $original_file_url ) ) {
								$img_url = path_join( dirname( $original_file_url ), $intermediate['file'] );
							}
						}
						$width = $intermediate['width'];
						$height = $intermediate['height'];
						$is_intermediate = true;
					}
					if ( isset( $img_url ) ) {

						list( $width, $height ) = image_constrain_size_for_editor( $width, $height, $size );
						$result = array( $img_url, $width, $height, $is_intermediate );
					}
				}
			}
		}
		return $result;
	}

	/**
	 * Placeholder
	 *
	 * @return array|null
	 */
	public static function get_placeholder_thumbnail() {

		$result = null;
		$settings = Settings::get_instance();
		$thumbnail_use_placeholder = $settings->get( self::THUMBNAIL_USE_PLACEHOLDER, self::THUMBNAIL_USE_PLACEHOLDER_DEFAULT );
		if ( $thumbnail_use_placeholder ) {
			$thumbnail_url    = wc_placeholder_img_src();
			$thumbnail_width  = $settings->get( self::THUMBNAIL_WIDTH, self::THUMBNAIL_DEFAULT_WIDTH );
			$thumbnail_height = $settings->get( self::THUMBNAIL_HEIGHT, self::THUMBNAIL_DEFAULT_HEIGHT );
			$result = array( $thumbnail_url, $thumbnail_width, $thumbnail_height );
		}
		return $result;
	}

	/**
	 * Taxonomies
	 *
	 * @return array of product taxonomies
	 */
	public static function get_product_taxonomies() {

		$product_taxonomies = array( 'product_cat', 'product_tag' );
		$product_taxonomies = array_merge( $product_taxonomies, wc_get_attribute_taxonomy_names() );
		$product_taxonomies = array_unique( $product_taxonomies );
		return $product_taxonomies;
	}

	/**
	 * Term thumbnail
	 *
	 * @param object $term
	 *
	 * @return string product search filter thumbnail HTML
	 */
	public static function term_thumbnail( $term, $params = array() ) {

		if ( !( $term instanceof WP_Term ) ) {
			return '';
		}

		$return = 'string';
		if ( isset( $params['return'] ) ) {
			switch( $params['return'] ) {
				case 'string' :
				case 'array' :
					$return = $params['return'];
					break;
			}
		}

		$output = '';
		$data   = array();

		$taxonomy       = !empty( $term->taxonomy ) ? $term->taxonomy : '';
		$thumbnail_size = self::thumbnail_size_name( $taxonomy . '-' );
		$thumbnail_id   = get_term_meta( $term->term_id, 'product_search_image_id', true );
		$image_srcset   = false;
		$image_sizes    = false;
		if ( $thumbnail_id ) {

			$image            = wp_get_attachment_image_src( $thumbnail_id, $thumbnail_size, false );
			$size             = self::get_image_size( $taxonomy . '-' );
			$thumbnail_url    = $image[0];
			$thumbnail_width  = $size['width'];
			$thumbnail_height = $size['height'];
			$image_srcset     = function_exists( 'wp_get_attachment_image_srcset' ) ? wp_get_attachment_image_srcset( $thumbnail_id, $thumbnail_size ) : false;
			$image_sizes      = function_exists( 'wp_get_attachment_image_sizes' ) ? wp_get_attachment_image_sizes( $thumbnail_id, $thumbnail_size ) : false;
		} else {

			if ( apply_filters( 'woocommerce_product_search_term_thumbnail_use_placeholder', true, $term ) ) {
				$placeholder = self::get_placeholder_thumbnail();
				if ( $placeholder !== null ) {
					list( $thumbnail_url, $thumbnail_width, $thumbnail_height ) = $placeholder;
					$thumbnail_alt = __( 'Placeholder Image', 'woocommerce-product-search' );
				}
			}
		}

		if ( $thumbnail_url ) {
			$data = array(
				'taxonomy'         => $term->taxonomy,
				'slug'             => $term->slug,
				'term_id'          => $term->term_id,
				'thumbnail_url'    => $thumbnail_url,
				'name'             => $term->name,
				'thumbnail_width'  => $thumbnail_width,
				'thumbnail_height' => $thumbnail_height,
				'style'            => isset( $params['style'] ) ? $params['style'] : ''
			);
			if ( $image_srcset && $image_sizes ) {
				$output .= sprintf(
					'<img class="term-thumbnail term-taxonomy-%s term-slug-%s term-id-%s" src="%s" alt="%s" title="%s" width="%s" height="%s" srcset="%s" sizes="%s" style="%s"/>',
					esc_attr( $term->taxonomy ),
					esc_attr( $term->slug ),
					esc_attr( $term->term_id ),
					esc_url( $thumbnail_url ),
					esc_attr( $term->name ),
					esc_attr( $term->name ),
					esc_attr( $thumbnail_width ),
					esc_attr( $thumbnail_height ),
					esc_attr( $image_srcset ),
					esc_attr( $image_sizes ),
					isset( $params['style'] ) ? esc_attr( $params['style'] ) : ''
				);
				$data['image_srcset'] = $image_srcset;
				$data['image_sizes']  = $image_sizes;
				$data['html']         = $output;
			} else {
				$output .= sprintf(
					'<img class="term-thumbnail term-taxonomy-%s term-slug-%s term-id-%s" src="%s" alt="%s" title="%s" width="%s" height="%s" style="%s"/>',
					esc_attr( $term->taxonomy ),
					esc_attr( $term->slug ),
					esc_attr( $term->term_id ),
					esc_url( $thumbnail_url ),
					esc_attr( $term->name ),
					esc_attr( $term->name ),
					esc_attr( $thumbnail_width ),
					esc_attr( $thumbnail_height ),
					isset( $params['style'] ) ? esc_attr( $params['style'] ) : ''
				);
				$data['html'] = $output;
			}
		} else {

			$size = self::get_image_size();
			$symbol = strtoupper( substr( $term->name, 0, 1 ) );
			if ( $symbol !== false ) {
				$output .= sprintf(
					'<div style="width:%dpx;height:%dpx;display:block;border:1px solid #333;border-radius:4px;text-align:center;%s" title="%s">%s</div>',
					intval( $size['width'] ),
					intval( $size['height'] ),
					isset( $params['style'] ) ? esc_attr( $params['style'] ) : '',
					esc_attr( $term->name ),
					esc_attr( $symbol )
				);
			}
		}

		switch( $return ) {
			case 'array' :
				$return = $data;
				break;
			case 'string' :
				$return = $output;
				break;
			default :
				$return = '';
		}

		return $return;
	}
}
WooCommerce_Product_Search_Thumbnail::init();

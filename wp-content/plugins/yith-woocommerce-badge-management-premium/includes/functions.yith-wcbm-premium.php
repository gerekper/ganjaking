<?php
/**
 * Premium Functions
 *
 * @package YITH\BadgeManagementPremium\Functions
 * @author  YITH <plugins@yithemes.com>
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

if ( ! function_exists( 'yith_wcbm_wpml_translate_badge_id' ) ) {
	/**
	 * Get translated badge ID using WPML
	 *
	 * @param int $id The badge ID.
	 *
	 * @return int
	 */
	function yith_wcbm_wpml_translate_badge_id( $id ) {
		global $sitepress;

		if ( isset( $sitepress ) ) {

			if ( function_exists( 'icl_object_id' ) ) {
				$id = icl_object_id( $id, 'any', true );
			} else {
				if ( function_exists( 'wpml_object_id_filter' ) ) {
					$id = wpml_object_id_filter( $id, 'any', true );
				}
			}
		}

		return $id;
	}
}

if ( ! function_exists( 'yith_wcbm_get_terms_in_default_language' ) ) {
	/**
	 * Get Terms in default language
	 *
	 * @param int    $post_id  The post ID.
	 * @param string $taxonomy The taxonomy.
	 *
	 * @return false|WP_Error|WP_Term[]
	 */
	function yith_wcbm_get_terms_in_default_language( $post_id, $taxonomy ) {
		global $sitepress;
		if ( $sitepress ) {
			$current_language = is_admin() ? $sitepress->get_admin_language() : $sitepress->get_current_language();
			$default_language = $sitepress->get_default_language();
			$change_language  = $current_language !== $default_language;

			if ( $change_language ) {
				$sitepress->switch_lang( $default_language );
			}

			$terms = get_the_terms( $post_id, $taxonomy );

			if ( $change_language ) {
				$sitepress->switch_lang( $current_language );
			}
		} else {
			$terms = get_the_terms( $post_id, $taxonomy );
		}

		return $terms;
	}
}

if ( ! function_exists( 'yith_wcbm_get_product_badges' ) ) {
	/**
	 * Get the badge ids for a specific product
	 *
	 * @param WC_Product $product The product.
	 *
	 * @return int[]
	 * @since 1.3.26
	 */
	function yith_wcbm_get_product_badges( $product ) {
		$product = wc_get_product( $product );
		$badges  = array();

		if ( $product ) {
			$badges = array_merge(
				yith_wcbm_badge_rules()->get_product_badge_ids_from_rules( $product ),
				yith_wcbm_get_product_badge_ids_from_meta( $product )
			);

			$badges = array_filter( array_unique( array_map( 'absint', $badges ) ), 'yith_wcbm_is_badge_enabled' );
		}

		return apply_filters( 'yith_wcbm_get_product_badges', $badges, $product );
	}
}

if ( ! function_exists( 'yith_wcbm_get_product_badge_ids_from_meta' ) ) {
	/**
	 *  Get Product Badges form its meta
	 *
	 * @param WC_Product $product          The product.
	 * @param bool       $variation_badges True if you want to get even the variation badges if the product is variable.
	 *
	 * @return int[]
	 */
	function yith_wcbm_get_product_badge_ids_from_meta( $product, $variation_badges = false ) {
		$product        = wc_get_product( $product );
		$transient_name = $product ? 'yith_wcbm_badges_from_product_' . $product->get_id() . '_meta' . ( $variation_badges && $product->is_type( 'variable' ) ? '_with_variations' : '' ) : '';
		$badges         = $product ? get_transient( $transient_name ) : array();

		if ( false === $badges && $product ) {
			$badges     = array();
			$badge_info = yith_wcbm_get_product_badge_info( $product );
			$badge_ids  = $badge_info['badge_ids'];
			$scheduled  = wc_string_to_bool( $badge_info['scheduled'] );
			$start_date = strtotime( '00:00:01', ( is_numeric( $badge_info['start_date'] ) ? $badge_info['start_date'] : strtotime( $badge_info['start_date'] ) ) );
			$end_date   = strtotime( '23:59:59', ( is_numeric( $badge_info['end_date'] ) ? $badge_info['end_date'] : strtotime( $badge_info['end_date'] ) ) );

			if ( $badge_ids && ( ! $scheduled || ( $start_date < time() && $end_date > time() ) ) ) {
				$badges = $badge_ids;
			}
			if ( $variation_badges && $product->is_type( 'variable' ) ) {
				foreach ( $product->get_children() as $variation ) {
					$badges = array_merge( $badges, yith_wcbm_get_product_badge_ids_from_meta( $variation ) );
				}
			}

			$transient_expiration = apply_filters( 'yith_wcbm_product_transient_expiration', 0 );
			if ( $scheduled ) {
				if ( $start_date > time() ) {
					$transient_expiration = $start_date - time();
				} elseif ( $end_date > time() ) {
					$transient_expiration = $end_date - time();
				}
			}
			set_transient( $transient_name, $badges, $transient_expiration );
		}

		return is_array( $badges ) ? array_filter( array_map( 'absint', $badges ) ) : array();
	}
}

if ( ! function_exists( 'yith_wcbm_get_product_badge_ids_from_rules' ) ) {
	/**
	 * Get Product Badges form Badge Rules
	 *
	 * @param WC_Product $product The product.
	 *
	 * @return int[]
	 */
	function yith_wcbm_get_product_badge_ids_from_rules( $product ) {
		return yith_wcbm_badge_rules()->get_product_badge_ids_from_rules( $product );
	}
}

if ( ! function_exists( 'yith_wcbm_get_badges_premium' ) ) {
	/**
	 * Retrieve the product badges HTML
	 *
	 * @param WC_Product|int|WP_Post $product    The product.
	 * @param null                   $deprecated Deprecated Param.
	 *
	 * @return string
	 */
	function yith_wcbm_get_badges_premium( $product, $deprecated = null ) {
		if ( func_num_args() > 1 ) {
			$product = $deprecated;
		}
		$product     = wc_get_product( $product );
		$badges_html = '';

		if ( $product && $product->is_type( 'variable' ) && YITH_WCBM_Frontend_Premium::is_allowed_variation_badge_showing() ) {
			foreach ( $product->get_children() as $variation ) {
				$badges_html .= yith_wcbm_get_badges_premium( $variation ) . ' ';
			}
		}

		if ( ! $product ) {
			return '';
		}

		$product_id = $product->get_id();

		$badges_to_show = yith_wcbm_get_product_badges( $product );
		$badges_to_show = apply_filters( 'yith_wcbm_badges_to_show_on_product', $badges_to_show, $product );

		foreach ( $badges_to_show as $badge_id ) {
			$badges_html .= yith_wcbm_get_badge_premium( $badge_id, $product_id );
		}

		return apply_filters( 'yith_wcbm_get_badges_premium', $badges_html, $product );
	}
}

if ( ! function_exists( 'yith_wcbm_get_badge_premium' ) ) {
	/**
	 * Get Badge Premium HTML
	 *
	 * @param int $badge_id   Badge ID.
	 * @param int $product_id Product ID.
	 *
	 * @return string
	 */
	function yith_wcbm_get_badge_premium( $badge_id, $product_id ) {
		$badge_id = yith_wcbm_wpml_translate_badge_id( $badge_id );
		$html     = '';
		$badge    = yith_wcbm_get_badge_object( $badge_id );

		if ( $badge && $product_id ) {
			$args['product_id'] = $product_id;
			$args['badge']      = $badge;
			if ( 'preview' === $product_id ) {
				$bm_meta['is_preview'] = true;
			}
			$args = apply_filters( 'yith_wcbm_badge_content_args', array_merge( $args, $badge->get_data() ) );
			$badge->set_props( $args );

			ob_start();
			$badge->display( $product_id );
			$html = ob_get_clean();
		}

		return apply_filters( 'yith_wcbm_get_badge_premium', $html, $badge_id, $product_id, $badge );
	}
}

if ( ! function_exists( 'yith_wcbm_get_badge_style' ) ) {
	/**
	 * Retrieve the badge style
	 *
	 * @param array $args Badge Args.
	 */
	function yith_wcbm_get_badge_style( $args ) {
		if ( isset( $args['badge_id'] ) && get_post_type( $args['badge_id'] ) === YITH_WCBM_Post_Types::$badge ) {
			$badge_id = absint( $args['badge_id'] );
		} else {
			global $post, $post_type;
			$badge_id = YITH_WCBM_Post_Types::$badge === $post_type ? $post->ID : false;
		}
		$badge = yith_wcbm_get_badge_object( $badge_id );

		if ( $badge ) {
			$badge->get_style( $args );
		}
	}
}

if ( ! function_exists( 'yith_wcbm_product_is_on_sale' ) ) {
	/**
	 * Is the product On Sale?
	 *
	 * @param WC_Product $product The product.
	 *
	 * @return bool
	 */
	function yith_wcbm_product_is_on_sale( $product ) {
		$product_is_on_sale = false;
		$product            = wc_get_product( $product );

		if ( $product ) {
			$product_is_on_sale = $product->is_on_sale();

			if ( apply_filters( 'yith_wcbm_product_is_on_sale_based_on_woocommerce', false ) ) {
				return $product_is_on_sale;
			}

			if ( ! $product_is_on_sale && defined( 'YITH_YWDPD_PREMIUM' ) || defined( 'YWCRBP_PREMIUM' ) ) {

				if ( $product->is_type( 'variable' ) ) {
					$children = $product->get_children();

					foreach ( $children as $child_id ) {
						$child = wc_get_product( $child_id );
						if ( ! $child ) {
							continue;
						}
						$price = $child->get_price();
						if ( function_exists( 'YITH_WC_Dynamic_Pricing' ) ) {
							$price = yith_wcbm_get_dynamic_pricing_product_price( $price, $child );
						}

						if ( $child->get_regular_price() > $price ) {
							$product_is_on_sale = true;
							break;
						}
					}
				} else {
					$price = $product->get_price();
					if ( function_exists( 'YITH_WC_Dynamic_Pricing' ) ) {
						$price = yith_wcbm_get_dynamic_pricing_product_price( $product->get_price( 'edit' ), $product );
					}
					$product_is_on_sale = $product->get_regular_price() > $price;
				}
			}

			$product_is_on_sale = $product_is_on_sale && ! $product->is_type( 'auction' );

			// Check if the price is not empty (catalog mode support).
			$product_is_on_sale = $product_is_on_sale && ( $product->is_type( 'variable' ) || '' !== $product->get_price() );
		}

		return ! ! apply_filters( 'yith_wcbm_product_is_on_sale', $product_is_on_sale, $product );
	}
}

if ( ! function_exists( 'yith_wcbm_is_bestsellers' ) ) {
	/**
	 * Check if the product is a bestseller
	 *
	 * @param WC_Product $product  The Product.
	 * @param int        $quantity The bestsellers' quantity.
	 *
	 * @return bool
	 */
	function yith_wcbm_is_bestsellers( $product, $quantity ) {
		static $bestsellers_products = null;

		if ( is_null( $bestsellers_products ) ) {
			$bestsellers_products = get_transient( 'yith_wcbm_bestsellers_products' );
			if ( ! $bestsellers_products ) {
				$bestsellers_products = array();
			}
		}

		$product       = wc_get_product( $product );
		$is_bestseller = false;

		if ( $product ) {
			$quantity = intval( $quantity );
			if ( ! array_key_exists( $quantity, $bestsellers_products ) ) {
				$args = array(
					'post_type'      => 'product',
					'posts_per_page' => $quantity,
					'order'          => 'desc',
					'orderby'        => 'total_sales',
					'fields'         => 'ids',
					'meta_query'     => array(
						'relation' => 'or',
						array(
							'key'     => '_manage_stock',
							'compare' => 'NOT EXISTS',
						),
						array(
							'relation' => 'or',
							array(
								'key'     => '_manage_stock',
								'compare' => '=',
								'value'   => 'no',
							),
							array(
								'relation' => 'or',
								array(
									'key'     => '_stock',
									'compare' => '>',
									'value'   => 0,
								),
								array(
									'key'     => '_backorders',
									'compare' => '=',
									'value'   => 'yes',
								),
							),
						),
					),
				);

				$bestsellers_products[ $quantity ] = get_posts( $args );
				set_transient( 'yith_wcbm_bestsellers_products', $bestsellers_products );
			}
			$is_bestseller = in_array( $product->get_id(), $bestsellers_products[ $quantity ], true );
		}

		return $is_bestseller;
	}
}

if ( ! function_exists( 'yith_wcbm_get_dynamic_pricing_product_price' ) ) {
	/**
	 * Get Dynamic Pricing product price
	 *
	 * @param string     $price   The price.
	 * @param WC_Product $product The product.
	 *
	 * @return float
	 */
	function yith_wcbm_get_dynamic_pricing_product_price( $price, $product ) {
		if ( defined( 'YITH_YWDPD_PREMIUM' ) && YITH_YWDPD_PREMIUM && defined( 'YITH_YWDPD_VERSION' ) && version_compare( YITH_YWDPD_VERSION, '1.1.0', '>=' ) ) {
			if ( version_compare( YITH_YWDPD_VERSION, '3.0.0', '>=' ) ) {
				$price = YWDPD_Frontend::get_instance()->get_dynamic_price( $product->get_price( 'edit' ), $product );
			} else {
				$price = YITH_WC_Dynamic_Pricing()->get_discount_price( $price, $product );
			}
		}

		return $price;
	}
}

if ( ! function_exists( 'yith_wcbm_get_product_badge_info' ) ) {
	/**
	 * Get Product Badge info
	 *
	 * @param WC_Product $product Product.
	 *
	 * @return array
	 */
	function yith_wcbm_get_product_badge_info( $product ) {
		$info    = array(
			'badge_ids'  => array(),
			'scheduled'  => 'no',
			'start_date' => '',
			'end_date'   => '',
		);
		$product = wc_get_product( $product );
		if ( $product ) {
			if ( $product->is_type( 'variation' ) ) {
				$info = array(
					'badge_ids'  => $product->get_meta( 'yith_wcbm_badge_options_badges' ),
					'scheduled'  => wc_bool_to_string( wc_string_to_bool( $product->get_meta( 'yith_wcbm_badge_options_schedule' ) ) ),
					'start_date' => $product->get_meta( 'yith_wcbm_badge_options_schedule_from' ),
					'end_date'   => $product->get_meta( 'yith_wcbm_badge_options_schedule_to' ),
				);
			} else {
				$old_meta = $product->get_meta( '_yith_wcbm_product_meta' );
				if ( $old_meta ) {
					yith_wcbm_update_product_badge_meta_premium( $product->get_id() );
					$product->read_meta_data( true );
				}
				$info = array(
					'badge_ids'  => $product->get_meta( '_yith_wcbm_badge_ids' ),
					'scheduled'  => wc_bool_to_string( wc_string_to_bool( $product->get_meta( '_yith_wcbm_badge_schedule' ) ) ),
					'start_date' => $product->get_meta( '_yith_wcbm_badge_from_date' ),
					'end_date'   => $product->get_meta( '_yith_wcbm_badge_to_date' ),
				);
			}
			$info['badge_ids'] = is_array( $info['badge_ids'] ) ? $info['badge_ids'] : array( $info['badge_ids'] );
		}

		return $info;
	}
}

if ( ! function_exists( 'yith_wcbm_is_settings_panel' ) ) {
	/**
	 * Check if is Badge settings panel
	 *
	 * @return bool
	 */
	function yith_wcbm_is_settings_panel() {
		return isset( $_GET['page'] ) && 'yith_wcbm_panel' === $_GET['page']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}
}

if ( ! function_exists( 'yith_wcbm_get_badge_meta_premium' ) ) {
	/**
	 * Get badge meta Premium
	 *
	 * @param int $badge_id Badge ID.
	 *
	 * @return array
	 */
	function yith_wcbm_get_badge_meta_premium( $badge_id ) {
		$meta = yith_wcbm_get_badge_meta( $badge_id );
		if ( get_post_type( $badge_id ) === YITH_WCBM_Post_Types::$badge ) {

			$rotation = get_post_meta( $badge_id, '_rotation', true );
			if ( is_array( $rotation ) ) {
				$rotation = array_diff_key( $rotation, array_flip( array( 'x-input', 'y-input', 'z-input' ) ) );
			} else {
				$rotation = array(
					'x' => 0,
					'y' => 0,
					'z' => 0,
				);
			}

			$opacity = get_post_meta( $badge_id, '_opacity', true );

			$flip_text = 'no';
			if ( wc_string_to_bool( get_post_meta( $badge_id, '_use_flip_text', true ) ) ) {
				$flip      = get_post_meta( $badge_id, '_flip_text', true );
				$flip_text = in_array( $flip, array( 'horizontal', 'vertical', 'both' ), true ) ? $flip : 'horizontal';
			}

			$premium_meta = array(
				'rotation'  => $rotation,
				'opacity'   => '' === $opacity ? 100 : absint( $opacity ),
				'flip_text' => $flip_text,
			);

			$position_type = get_post_meta( $badge_id, '_position_type', true );
			if ( 'values' === $position_type ) {
				$anchor_point                    = get_post_meta( $badge_id, '_anchor_point', true );
				$position_values_defaults        = array(
					'top'    => 0,
					'right'  => 0,
					'bottom' => 0,
					'left'   => 0,
				);
				$position_values                 = get_post_meta( $badge_id, '_position_values', true );
				$premium_meta['anchor_point']    = in_array( $anchor_point, array( 'top-left', 'top-right', 'bottom-right', 'bottom-left' ), true ) ? $anchor_point : 'top-left';
				$premium_meta['position_values'] = is_array( $position_values ) ? wp_parse_args( $position_values, $position_values_defaults ) : $position_values_defaults;
			}
			$premium_meta['position_type'] = in_array( $position_type, array( 'fixed', 'values' ), true ) ? $position_type : 'fixed';

			$margin_dim = yith_plugin_fw_parse_dimensions( get_post_meta( $badge_id, '_margin', true ) );
			$margin     = array(
				'margin_top'    => 0,
				'margin_right'  => 0,
				'margin_bottom' => 0,
				'margin_left'   => 0,
			);
			if ( isset( $margin_dim['top'], $margin_dim['left'], $margin_dim['right'], $margin_dim['bottom'] ) ) {
				foreach ( $margin_dim as $side => $value ) {
					$margin[ 'margin_' . $side ] = $value;
				}
			}
			$premium_meta = array_merge( $premium_meta, $margin );

			if ( isset( $meta['type'] ) ) {
				switch ( $meta['type'] ) {
					case 'css':
						$premium_meta['css'] = get_post_meta( $badge_id, '_css', true );
						break;
					case 'advanced':
						$premium_meta['advanced']         = get_post_meta( $badge_id, '_advanced', true );
						$premium_meta['text_color']       = get_post_meta( $badge_id, '_text_color', true );
						$premium_meta['advanced_display'] = 'amount' === get_post_meta( $badge_id, '_advanced_display', true ) ? 'amount' : 'percentage';
						break;
				}
			}

			$meta = array_merge( $meta, $premium_meta );
		}

		return $meta;
	}
}

if ( ! function_exists( 'yith_wcbm_update_badge_meta_premium' ) ) {
	/**
	 * Update Badge meta Premium
	 *
	 * @param int $badge_id Badge ID.
	 */
	function yith_wcbm_update_badge_meta_premium( $badge_id ) {
		if ( get_post_type( $badge_id ) !== YITH_WCBM_Post_Types::$badge ) {
			return;
		}
		$defaults   = array(
			'type'                        => 'text',
			'text'                        => '',
			'txt_color'                   => '#000000',
			'bg_color'                    => '0',
			'advanced_display'            => 0,
			'border_top_left_radius'      => 0,
			'border_top_right_radius'     => 0,
			'border_bottom_right_radius'  => 0,
			'border_bottom_left_radius'   => 0,
			'flip_text_horizontally'      => false,
			'flip_text_vertically'        => false,
			'advanced_bg_color'           => '',
			'advanced_bg_color_default'   => '',
			'advanced_text_color'         => '',
			'advanced_text_color_default' => '',
			'advanced_badge'              => 1,
			'css_badge'                   => 1,
			'css_bg_color'                => '',
			'css_bg_color_default'        => '',
			'css_text_color'              => '',
			'css_text_color_default'      => '',
			'css_text'                    => '',
			'width'                       => '100',
			'height'                      => '50',
			'position'                    => 'top',
			'alignment'                   => 'left',
			'image_url'                   => '',
			'pos_top'                     => 0,
			'pos_bottom'                  => 0,
			'pos_left'                    => 0,
			'pos_right'                   => 0,
			'border_radius_top_left'      => 0,
			'border_radius_top_right'     => 0,
			'border_radius_bottom_right'  => 0,
			'border_radius_bottom_left'   => 0,
			'padding_top'                 => 0,
			'padding_bottom'              => 0,
			'padding_left'                => 0,
			'padding_right'               => 0,
			'font_size'                   => 13,
			'line_height'                 => -1,
			'opacity'                     => 100,
			'rotation'                    => array(
				'x' => 0,
				'y' => 0,
				'z' => 0,
			),
			'flip_text'                   => 'no',
			'scale_on_mobile'             => 1,
		);
		$badge_meta = get_post_meta( absint( $badge_id ), '_badge_meta', true );
		if ( $badge_meta && ! array_key_exists( 'opacity', $badge_meta ) ) {
			yith_wcbm_update_badge_meta( $badge_id );

			return;
		}
		$badge_meta      = wp_parse_args( $badge_meta, $defaults );
		$badge_meta      = is_array( $badge_meta ) ? $badge_meta : unserialize( get_post_meta( $badge_id, '_badge_meta', true ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_unserialize
		$text_key        = 'css' === $badge_meta['type'] ? 'css_text' : 'text';
		$bg_color_key    = in_array( $badge_meta['type'], array( 'css', 'advanced' ), true ) ? $badge_meta['type'] . '_bg_color' : 'bg_color';
		$text_color_key  = in_array( $badge_meta['type'], array( 'css', 'advanced' ), true ) ? $badge_meta['type'] . '_text_color' : 'txt_color';
		$old_to_new_meta = array(
			'type'             => '_type',
			$text_key          => '_text',
			$text_color_key    => '_text_color',
			$bg_color_key      => '_background_color',
			'image_url'        => '_image',
			'advanced_display' => '_advanced_display',
			'advanced_badge'   => '_advanced',
			'css_badge'        => '_css',
			'opacity'          => '_opacity',
			'rotation'         => '_rotation',
			'position'         => '_anchor_point',
			'scale_on_mobile'  => '_scale_on_mobile',
		);
		$new_meta        = array();
		foreach ( $badge_meta as $key => $value ) {
			$meta_value = null;

			switch ( $key ) {
				case $bg_color_key:
					if ( 'advanced' === $badge_meta['type'] && in_array( $badge_meta['advanced_badge'], array( '10', '5' ), true ) ) {
						$meta_value = '5' === $badge_meta['advanced_badge'] ? '#E832FD' : '#F56507';
					} else {
						$meta_value = $value;
					}
					break;
				case 'padding_top':
				case 'padding_right':
				case 'padding_bottom':
				case 'padding_left':
					if ( ! isset( $padding['dimensions'] ) ) {
						$new_meta['_padding']['dimensions'] = array();
						$new_meta['_padding']['unit']       = 'px';
						$new_meta['_padding']['linked']     = 'no';
					}
					$new_meta['padding']['dimensions'][ str_replace( 'padding_', '', $key ) ] = absint( $value );
					break;

				case 'border_bottom_right_radius':
				case 'border_bottom_left_radius':
				case 'border_top_right_radius':
				case 'border_top_left_radius':
					if ( ! isset( $new_meta['_border_radius']['dimensions'] ) ) {
						$new_meta['_border_radius']['dimensions'] = array();
						$new_meta['_border_radius']['unit']       = 'px';
						$new_meta['_border_radius']['linked']     = 'no';

					}
					$new_meta['_border_radius']['dimensions'][ str_replace( array( 'border_', '_radius', '_' ), array( '', '', '-' ), $key ) ] = absint( $value );
					break;

				case 'text':
				case 'css_text':
					$style = "font-family: 'Open Sans', sans-serif;";
					if ( isset( $badge_meta[ $text_color_key ] ) ) {
						$style .= ' color:' . $badge_meta[ $text_color_key ] . ';';
					}
					if ( isset( $badge_meta['font_size'] ) && floatval( $badge_meta['font_size'] ) ) {
						$style .= ' font-size:' . floatval( $badge_meta['font_size'] ) . 'px;';
					}
					$meta_value = '<div style="' . $style . '">' . $value . '</div>';
					break;

				case 'width':
				case 'height':
					if ( ! isset( $new_meta['_size']['dimensions'] ) ) {
						$new_meta['_size']['dimensions'] = array();
						$new_meta['_size']['unit']       = 'px';
						$new_meta['_size']['linked']     = 'no';
					}
					$new_meta['_size']['dimensions'][ $key ] = absint( $value );
					break;

				case 'image_url':
					$attachment_id = yit_plugin_get_attachment_id( $value );
					if ( false !== $attachment_id ) {
						$meta_value = 'upload';
						update_post_meta( $badge_id, '_uploaded__image_id', yit_plugin_get_attachment_id( $value ) );
					} else {
						$meta_value = str_replace( 'png', 'svg', basename( $value ) );
					}
					break;

				case 'css_badge':
				case 'advanced_badge':
					$meta_value = absint( $value ) . '.svg';
					break;

				case 'pos_top':
				case 'pos_bottom':
				case 'pos_left':
				case 'pos_right':
					if ( ! isset( $new_meta['_position_values']['dimensions'] ) ) {
						$new_meta['_position_values']['dimensions'] = array();
						$new_meta['_position_values']['unit']       = 'px';
						$new_meta['_position_values']['linked']     = 'no';
					}
					$new_meta['_position_values']['dimensions'][ str_replace( 'pos_', '', $key ) ] = intval( $value );
					break;

				case 'rotation':
					$meta_value = $value;
					foreach ( $meta_value as $axis => $rotation ) {
						$meta_value[ $axis . '-input' ] = $rotation;
					}
					break;

				default:
					$meta_value = $value;
					break;
			}
			if ( ! is_null( $meta_value ) && array_key_exists( $key, $old_to_new_meta ) ) {
				update_post_meta( $badge_id, $old_to_new_meta[ $key ], $meta_value );
			}
		}

		$new_meta['_use_flip_text'] = wc_bool_to_string( $badge_meta['flip_text_vertically'] || $badge_meta['flip_text_horizontally'] );
		$new_meta['_flip_text']     = $badge_meta['flip_text_vertically'] ? 'vertical' : '';
		$new_meta['_flip_text']     = $badge_meta['flip_text_horizontally'] ? ( 'vertical' === $new_meta['_flip_text'] ? 'both' : 'horizontal' ) : 'vertical';
		$new_meta['_margin']        = array(
			'dimensions' => array(
				'top'    => 0,
				'right'  => 0,
				'bottom' => 0,
				'left'   => 0,
			),
			'unit'       => 'px',
			'linked'     => 'no',
		);

		if ( strpos( $badge_meta['pos_top'], 'calc' ) !== false || strpos( $badge_meta['pos_left'], 'calc' ) !== false ) {
			$new_meta['_position_type'] = 'fixed';
			if ( strpos( $badge_meta['pos_top'], 'calc' ) !== false && strpos( $badge_meta['pos_left'], 'calc' ) !== false ) {
				$new_meta['_position']  = 'middle';
				$new_meta['_alignment'] = 'center';
			} elseif ( strpos( $badge_meta['pos_top'], 'calc' ) !== false ) {
				$new_meta['_position'] = 'middle';
				if ( 'auto' === $badge_meta['pos_left'] ) {
					$new_meta['_alignment']                     = 'right';
					$new_meta['_margin']['dimensions']['right'] = $badge_meta['pos_right'];
				} else {
					$new_meta['_alignment']                    = 'left';
					$new_meta['_margin']['dimensions']['left'] = $badge_meta['pos_left'];
				}
			} elseif ( strpos( $badge_meta['pos_left'], 'calc' ) !== false ) {
				$new_meta['_alignment'] = 'center';
				if ( 'auto' === $badge_meta['pos_top'] ) {
					$new_meta['_position']                       = 'bottom';
					$new_meta['_margin']['dimensions']['bottom'] = $badge_meta['pos_bottom'];
				} else {
					$new_meta['_position']                    = 'top';
					$new_meta['_margin']['dimensions']['top'] = $badge_meta['pos_top'];
				}
			}
		} else {
			$positions = array(
				'top'    => $badge_meta['pos_top'],
				'bottom' => $badge_meta['pos_bottom'],
				'left'   => $badge_meta['pos_left'],
				'right'  => $badge_meta['pos_right'],
			);
			if ( implode( '-', array_keys( array_diff( $positions, array( 'auto' ) ) ) ) === $badge_meta['position'] ) {
				$new_meta['_position_type'] = 'values';
				$new_meta['_position']      = explode( '-', $badge_meta['position'] )[0];
				$new_meta['_alignment']     = explode( '-', $badge_meta['position'] )[1];
			}
		}

		foreach ( $new_meta as $meta_key => $meta_value ) {
			update_post_meta( $badge_id, $meta_key, $meta_value );
		}

		delete_post_meta( $badge_id, '_badge_meta' );
	}
}

if ( ! function_exists( 'yith_wcbm_compare_field_position_value_in_array' ) ) {
	/**
	 * Compare the field_position options of two associative arrays.
	 *
	 * @param array $a First array.
	 * @param array $b Second array.
	 *
	 * @return bool
	 */
	function yith_wcbm_compare_field_position_value_in_array( $a, $b ) {
		if ( ! isset( $a['field_position'] ) ) {
			return 1;
		}
		if ( ! isset( $b['field_position'] ) ) {
			return 0;
		}

		return $a['field_position'] * 10 - $b['field_position'] * 10;
	}
}

if ( ! function_exists( 'yith_wcbm_get_user_roles' ) ) {
	/**
	 * Get user roles
	 *
	 * @param int $user_id User ID.
	 *
	 * @return string[]
	 */
	function yith_wcbm_get_user_roles( $user_id = false ) {
		if ( is_user_logged_in() ) {
			$user = $user_id ? get_userdata( $user_id ) : wp_get_current_user();
		} else {
			$user = (object) array( 'roles' => array( 'yith-wcbm-guest' ) );
		}

		return $user->roles ?? array();
	}
}

if ( ! function_exists( 'yith_wcbm_is_editing_badge_page' ) ) {
	/**
	 * Check if the current page is the editing badge one
	 *
	 * @return bool
	 */
	function yith_wcbm_is_editing_badge_page() {
		global $post_type, $pagenow;

		return in_array( $pagenow, array( 'post-new.php', 'post.php' ), true ) && YITH_WCBM_Post_Types::$badge === $post_type;
	}
}

if ( ! function_exists( 'yith_wcbm_get_badges_list' ) ) {
	/**
	 * Get Badge List
	 *
	 * @param string $type The badges type.
	 *
	 * @return array
	 */
	function yith_wcbm_get_badges_list( $type = null ) {
		$badges = array();
		$types  = array( 'image', 'css', 'advanced' );
		if ( in_array( $type, $types, true ) ) {
			$local_badges    = yith_wcbm_get_local_badges_list( $type );
			$imported_badges = yith_wcbm_get_imported_badge_list( $type );

			$badges = array_merge( $local_badges, $imported_badges );
		} else {
			foreach ( $types as $type ) {
				$badges[ $type ] = yith_wcbm_get_badges_list( $type );
			}
		}

		return $badges;
	}
}

if ( ! function_exists( 'yith_wcbm_get_imported_badge_list' ) ) {
	/**
	 * Get Library badge list
	 *
	 * @param string $type The badge type.
	 *
	 * @return array
	 */
	function yith_wcbm_get_imported_badge_list( $type = '' ) {
		/**
		 * WP Filesystem
		 *
		 * @var WP_Filesystem_Direct $wp_filesystem
		 */
		global $wp_filesystem;
		if ( empty( $wp_filesystem ) ) {
			require_once ABSPATH . '/wp-admin/includes/file.php';
			WP_Filesystem();
		}

		$library_dir = yith_wcbm_get_badge_library_dir_path();
		$list        = array(
			'image'    => array(),
			'css'      => array(),
			'advanced' => array(),
		);
		if ( yith_wcbm_has_active_license() ) {
			foreach ( $list as $badge_type => &$badge_list ) {
				if ( is_dir( $library_dir . $badge_type ) ) {
					$badge_list = array_keys( array_filter( $wp_filesystem->dirlist( $library_dir . $badge_type ), 'yith_wcbm_filter_only_files_in_dir_content' ) );
				}
			}
		}

		return array_key_exists( $type, $list ) ? $list[ $type ] : $list;
	}
}

if ( ! function_exists( 'yith_wcbm_filter_only_files_in_dir_content' ) ) {
	/**
	 * Used to filter jus files from an array of directory content retrieved by using WP Filesystem
	 *
	 * @param array $dir_content Directory content.
	 *
	 * @return bool
	 */
	function yith_wcbm_filter_only_files_in_dir_content( $dir_content ) {
		return ( $dir_content['type'] ?? '' ) === 'f';
	}
}

if ( ! function_exists( 'yith_wcbm_get_badge_library_dir_path' ) ) {
	/**
	 * Get path to the badge library folder
	 *
	 * @param string $type The Badge Type.
	 *
	 * @return string
	 */
	function yith_wcbm_get_badge_library_dir_path( $type = '' ) {
		$upload_dir_path   = wp_upload_dir()['basedir'] . '/';
		$badge_library_dir = $upload_dir_path . 'yith-badge-library-' . substr( md5( site_url() ), 0, 10 ) . '/';

		return in_array( $type, array( 'css', 'image', 'advanced' ), true ) ? $badge_library_dir . $type . '/' : $badge_library_dir;
	}
}

if ( ! function_exists( 'yith_wcbm_get_badge_library_dir_url' ) ) {
	/**
	 * Get URL to the badge library folder
	 *
	 * @param string $type The Badge Type.
	 *
	 * @return string
	 */
	function yith_wcbm_get_badge_library_dir_url( $type = '' ) {
		$upload_dir_url    = wp_upload_dir()['baseurl'] . '/';
		$badge_library_dir = $upload_dir_url . 'yith-badge-library-' . substr( md5( site_url() ), 0, 10 ) . '/';

		return in_array( $type, array( 'css', 'image', 'advanced' ), true ) ? $badge_library_dir . $type . '/' : $badge_library_dir;
	}
}

if ( ! function_exists( 'yith_wcbm_get_badge_list_with_data' ) ) {
	/**
	 * Get all local and imported badge list with their data.
	 *
	 * @return array
	 */
	function yith_wcbm_get_badge_list_with_data() {
		$local    = yith_wcbm_get_local_badge_list_with_data();
		$imported = yith_wcbm_get_remote_badges_list();

		return array_merge_recursive( $local, $imported );
	}
}

if ( ! function_exists( 'yith_wcbm_get_remote_badges_list' ) ) {
	/**
	 * Get remote Badges List
	 *
	 * @return array
	 */
	function yith_wcbm_get_remote_badges_list() {
		static $badges = null;

		if ( null === $badges ) {
			$badges = get_option( 'yith_wcbm_remote_badges_list', array() );
			if ( ! $badges || ! get_transient( 'yith_wcbm_is_badges_list_consistent' ) ) {
				$api_url       = 'https://plugins.yithemes.com/resources/yith-woocommerce-badge-management/badges/list.json';
				$api_call_args = array(
					'timeout'    => apply_filters( 'yith_wcbm_get_badges_list_timeout', 15 ),
					'user-agent' => 'YITH Badge Management Premium/' . YITH_WCBM_VERSION . '; ' . get_site_url(),
				);

				$badges   = array(
					'image'    => array(),
					'css'      => array(),
					'advanced' => array(),
				);
				$response = wp_remote_get( $api_url, $api_call_args );
				$status   = wp_remote_retrieve_response_code( $response );
				if ( 200 === $status ) {
					$response = wp_remote_retrieve_body( $response );
					$response = json_decode( $response, true );
					if ( is_array( $response ) && array_key_exists( 'badges', $response ) && $response['badges'] ) {
						$badges = wp_parse_args( $response['badges'], $badges );
						update_option( 'yith_wcbm_remote_badges_list', $badges );
						set_transient( 'yith_wcbm_is_badges_list_consistent', true, DAY_IN_SECONDS );
					}
				} elseif ( function_exists( 'yith_wcbm_debug_errors_trigger' ) ) {
					yith_wcbm_debug_errors_trigger( 'There was an error while calling the api at "' . $api_url . ( $status ? '", status code : ' : '' ) . $status );
				}
			}
		}

		return is_array( $badges ) ? $badges : array();
	}
}

if ( ! function_exists( 'yith_wcbm_has_active_license' ) ) {
	/**
	 * Check if there is an active license
	 *
	 * @return bool
	 */
	function yith_wcbm_has_active_license() {
		if ( function_exists( 'YITH_Plugin_Licence' ) ) {
			$license = YITH_Plugin_Licence();
			if ( is_callable( array( $license, 'get_licence' ) ) ) {
				$licenses = $license->get_licence();

				return ! empty( $licenses[ YITH_WCBM_SLUG ] );
			}
		}

		return false;
	}
}

if ( ! function_exists( 'yith_wcbm_get_license_activation_url' ) ) {
	/**
	 * Get license activation url
	 *
	 * @return string
	 */
	function yith_wcbm_get_license_activation_url() {
		if ( function_exists( 'YIT_Plugin_Licence' ) ) {
			$license = YIT_Plugin_Licence();
			if ( is_callable( array( $license, 'get_license_activation_url' ) ) ) {
				return $license->get_license_activation_url( YITH_WCBM_SLUG );
			}
		}

		return '';
	}
}

if ( ! function_exists( 'yith_wcbm_update_product_badge_meta_premium' ) ) {
	/**
	 * Update product meta badge options
	 *
	 * @param int $product_id The product ID.
	 */
	function yith_wcbm_update_product_badge_meta_premium( $product_id ) {
		$old_to_new_meta = array(
			'id_badge'   => 'ids',
			'start_date' => 'from_date',
			'end_date'   => 'to_date',
		);
		$product         = wc_get_product( $product_id );
		if ( $product ) {
			$old_meta = $product->get_meta( '_yith_wcbm_product_meta' );
			if ( $old_meta ) {
				if ( ! empty( $old_meta['start_date'] ) || ! empty( $old_meta['end_date'] ) ) {
					$product->update_meta_data( '_yith_wcbm_badge_schedule', 'yes' );
				}
				foreach ( $old_meta as $key => $value ) {
					$new_key = array_key_exists( $key, $old_to_new_meta ) ? $old_to_new_meta[ $key ] : $key;
					$value   = 'id_badge' === $key && ! is_array( $value ) ? array( $value ) : $value;
					$product->update_meta_data( '_yith_wcbm_badge_' . $new_key, $value );
				}
			}
			$product->delete_meta_data( '_yith_wcbm_product_meta' );
			$product->save_meta_data();
		}
	}
}

/**
 * Get the badge allowed html to use in the wp_kses sanitize
 *
 * @return array
 */
function yith_wcbm_get_badge_allowed_html(): array {
	$badge_allowed_html = array(
		'svg'            => array(
			'id'                => 1,
			'xmlns'             => 1,
			'x'                 => 1,
			'y'                 => 1,
			'width'             => 1,
			'height'            => 1,
			'viewbox'           => 1,
			'enable-background' => 1,
			'class'             => 1,
		),
		'g'              => array(
			'opacity' => 1,
			'class'   => 1,
		),
		'path'           => array(
			'fill'  => 1,
			'class' => 1,
			'd'     => 1,
		),
		'lineargradient' => array(
			'id'            => 1,
			'gradientunits' => 1,
			'x1'            => 1,
			'y1'            => 1,
			'x2'            => 1,
			'y2'            => 1,
			'class'         => 1,
		),
		'stop'           => array(
			'style'  => 1,
			'offset' => 1,
			'class'  => 1,
		),
		'style'          => array(),
		'polygon'        => array(
			'fill'   => 1,
			'points' => 1,
			'class'  => 1,
		),
		'rect'           => array(
			'fill'   => 1,
			'x'      => 1,
			'y'      => 1,
			'width'  => 1,
			'height' => 1,
			'class'  => 1,
		),
		'circle'         => array(
			'fill'              => 1,
			'stroke'            => 1,
			'stroke-linecap'    => 1,
			'stroke-linejoin'   => 1,
			'stroke-miterlimit' => 1,
			'stroke-dasharray'  => 1,
			'cx'                => 1,
			'cy'                => 1,
			'r'                 => 1,
			'class'             => 1,
		),
	);

	return apply_filters( 'yith_wcbm_get_badge_allowed_html', $badge_allowed_html );
}

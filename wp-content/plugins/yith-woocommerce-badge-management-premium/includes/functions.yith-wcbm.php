<?php
/**
 * Functions
 *
 * @package YITH\BadgeManagement\Functions
 * @author  YITH <plugins@yithemes.com>
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

if ( ! function_exists( 'yith_wcbm_get_view' ) ) {
	/**
	 * Print a view
	 *
	 * @param string $view The view.
	 * @param array  $args Arguments.
	 */
	function yith_wcbm_get_view( $view, $args = array() ) {
		$view_path = trailingslashit( YITH_WCBM_VIEWS_PATH ) . $view;
		extract( $args ); // @codingStandardsIgnoreLine
		if ( file_exists( $view_path ) ) {
			include $view_path;
		}
	}
}

if ( ! function_exists( 'yith_wcbm_get_view_html' ) ) {
	/**
	 * Return a view HTML
	 *
	 * @param string $view The view.
	 * @param array  $args Arguments.
	 */
	function yith_wcbm_get_view_html( $view, $args = array() ) {
		ob_start();
		yith_wcbm_get_view( $view, $args );

		return ob_get_clean();
	}
}

if ( ! function_exists( 'yith_wcbm_get_template' ) ) {
	/**
	 * Get the template
	 *
	 * @param string $filename name of the file to get in templates.
	 * @param array  $args     Arguments.
	 */
	function yith_wcbm_get_template( $filename, $args = array() ) {
		wc_get_template( $filename, $args, '', YITH_WCBM_TEMPLATES_PATH . '/' );
	}
}

if ( ! function_exists( 'yith_wcbm_get_icon' ) ) {
	/**
	 * Print a view
	 *
	 * @param string $icon The icon.
	 * @param bool   $echo Print the Icon.
	 */
	function yith_wcbm_get_icon( $icon, $echo = false ) {
		$icon_path = trailingslashit( YITH_WCBM_ASSETS_IMAGES_PATH ) . 'icons/' . $icon . '.svg';
		ob_start();
		if ( file_exists( $icon_path ) ) {
			include $icon_path;
		}
		$icon = ob_get_clean();
		if ( $echo ) {
			echo $icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		return $icon;
	}
}

if ( ! function_exists( 'yith_wcbm_get_panel_url' ) ) {
	/**
	 * Return the YITH WCBM Panel url
	 *
	 * @param string $tab Tab.
	 *
	 * @return string
	 */
	function yith_wcbm_get_panel_url( $tab = '' ) {
		$query_args = array( 'page' => yith_wcbm()->admin->get_panel_page() );
		if ( ! ! $tab ) {
			$query_args['tab'] = $tab;
		}

		return add_query_arg( $query_args, admin_url( 'admin.php' ) );
	}
}

if ( ! function_exists( 'yith_wcbm_is_my_account_page' ) ) {
	/**
	 * Check if is My Account page [possibility to specify an endpoint]
	 *
	 * @param string $endpoint The endpoint.
	 *
	 * @return bool
	 */
	function yith_wcbm_is_my_account_page( $endpoint = '' ) {
		global $wp;
		$page_id = wc_get_page_id( 'myaccount' );

		return ( $page_id && is_page( $page_id ) ) && ( '' === $endpoint || isset( $wp->query_vars[ $endpoint ] ) );
	}
}

if ( ! function_exists( 'yith_wcbm_product_has_badges' ) ) {
	/**
	 * Does the product have any badges?
	 *
	 * @param WC_Product $product The Product.
	 *
	 * @return bool
	 * @since 1.3.26
	 */
	function yith_wcbm_product_has_badges( $product ) {
		return $product && ! ! ( defined( 'YITH_WCBM_PREMIUM' ) && YITH_WCBM_PREMIUM ? yith_wcbm_get_product_badges( $product ) : yith_wcbm_get_product_badge( $product->get_id() ) );
	}
}

if ( ! function_exists( 'yith_wcbm_create_capabilities' ) ) {
	/**
	 * Create a capability array.
	 *
	 * @param array|string $capability_type The capability type.
	 *
	 * @return array
	 */
	function yith_wcbm_create_capabilities( $capability_type ) {
		if ( ! is_array( $capability_type ) ) {
			$capability_type = array( $capability_type, $capability_type . 's' );
		}

		list( $singular, $plural ) = $capability_type;

		return array(
			'edit_' . $singular           => true,
			'read_' . $singular           => true,
			'delete_' . $singular         => true,
			'edit_' . $plural             => true,
			'edit_others_' . $plural      => true,
			'publish_' . $plural          => true,
			'read_private_' . $plural     => true,
			'delete_' . $plural           => true,
			'delete_private_' . $plural   => true,
			'delete_published_' . $plural => true,
			'delete_others_' . $plural    => true,
			'edit_private_' . $plural     => true,
			'edit_published_' . $plural   => true,
		);
	}
}

if ( ! function_exists( 'yith_wcbm_get_badges' ) ) {
	/**
	 * Get badges
	 *
	 * @param array $args Arguments.
	 *
	 * @return int[]|WP_Post[]|YITH_WCBM_Badge[]
	 */
	function yith_wcbm_get_badges( $args = array() ) {
		$default_args      = array(
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
			'post_status'    => 'publish',
			'fields'         => 'ids',
		);
		$args              = wp_parse_args( $args, $default_args );
		$args['post_type'] = YITH_WCBM_Post_Types::$badge;

		$badges = get_posts( $args );

		if ( isset( $args['return'] ) && 'objects' === $args['return'] ) {
			$badges = array_map( 'yith_wcbm_get_badge_object', $badges );
		}

		return $badges;
	}
}

if ( ! function_exists( 'yith_wcbm_is_frontend_manager' ) ) {
	/**
	 * Is this a page of YITH Frontend Manager?
	 *
	 * @return bool
	 */
	function yith_wcbm_is_frontend_manager() {
		$fm = function_exists( 'YITH_Frontend_Manager' ) ? YITH_Frontend_Manager() : false;
		if ( $fm && isset( $fm->gui ) && is_object( $fm->gui ) && is_callable( array( $fm->gui, 'is_main_page' ) ) ) {
			return $fm->gui->is_main_page();
		}

		return false;
	}
}

if ( ! function_exists( 'yith_wcbm_get_badge_meta' ) ) {
	/**
	 * Get Badge Meta
	 *
	 * @param int $badge_id Badge ID.
	 *
	 * @return array
	 *
	 * TODO: remove this function, use object instead.
	 */
	function yith_wcbm_get_badge_meta( $badge_id ) {
		$badge_meta = array();
		if ( get_post_type( $badge_id ) === YITH_WCBM_Post_Types::$badge ) {
			$old_badge_meta = array();
			$new_badge_meta = array(
				'type'              => get_post_meta( $badge_id, '_type', true ),
				'text'              => get_post_meta( $badge_id, '_text', true ),
				'txt_color_default' => '#000000',
				'bg_color_default'  => '#2470FF',
				'bg_color'          => get_post_meta( $badge_id, '_background_color', true ),
				'position'          => get_post_meta( $badge_id, '_position', true ),
				'alignment'         => get_post_meta( $badge_id, '_alignment', true ),
				'image_url'         => get_post_meta( $badge_id, '_image', true ),
				'scale_on_mobile'   => get_post_meta( $badge_id, '_scale_on_mobile', true ),
				'id_badge'          => $badge_id,
			);

			$badge_size          = get_post_meta( $badge_id, '_size', true );
			$badge_padding       = yith_plugin_fw_parse_dimensions( get_post_meta( $badge_id, '_padding', true ) );
			$badge_border_radius = yith_plugin_fw_parse_dimensions( get_post_meta( $badge_id, '_border_radius', true ) );

			if ( isset( $badge_size['unit'] ) ) {
				if ( isset( $badge_size['dimensions']['width'] ) ) {
					$new_badge_meta['width'] = $badge_size['dimensions']['width'] ? $badge_size['dimensions']['width'] . $badge_size['unit'] : 'auto';
				}
				if ( isset( $badge_size['dimensions']['height'] ) ) {
					$new_badge_meta['height'] = $badge_size['dimensions']['height'] ? $badge_size['dimensions']['height'] . $badge_size['unit'] : 'auto';
				}
			}
			if ( isset( $badge_padding['top'], $badge_padding['right'], $badge_padding['bottom'], $badge_padding['left'] ) ) {
				foreach ( $badge_padding as $side => $padding ) {
					$new_badge_meta[ 'padding_' . $side ] = $padding;
				}
			}
			if ( isset( $badge_border_radius['top-left'], $badge_border_radius['top-right'], $badge_border_radius['bottom-right'], $badge_border_radius['bottom-left'] ) ) {
				foreach ( $badge_border_radius as $corner => $border_radius ) {
					$new_badge_meta[ 'border_radius_' . str_replace( '-', '_', $corner ) ] = $border_radius;
				}
			}
			$new_badge_meta = array_filter( $new_badge_meta );

			$badge_meta = wp_parse_args( $old_badge_meta, $badge_meta );
			$badge_meta = wp_parse_args( $new_badge_meta, $badge_meta );
		}

		return $badge_meta;
	}
}

if ( ! function_exists( 'yith_wcbm_is_badge_enabled' ) ) {
	/**
	 * Check if the badge is enabled
	 *
	 * @param int $badge_id Badge ID.
	 *
	 * @return bool
	 */
	function yith_wcbm_is_badge_enabled( $badge_id ) {
		$badge = yith_wcbm_get_badge_object( $badge_id );

		return $badge && $badge->is_enabled();
	}
}

if ( ! function_exists( 'yith_wcbm_get_badge_editor_fonts' ) ) {
	/**
	 * Retrieve the Badge Editor fonts
	 *
	 * @return array
	 */
	function yith_wcbm_get_badge_editor_fonts() {
		$fonts = array(
			'Andale Mono = andale mono,times;',
			'Arial = arial,helvetica,sans-serif;',
			'Arial Black = arial black,avant garde;',
			'Book Antiqua = book antiqua,palatino;',
			'Comic Sans MS = comic sans ms,sans-serif;',
			'Courier New = courier new,courier;',
			'Georgia = georgia,palatino;',
			'Helvetica = helvetica;',
			'Impact = impact,chicago;',
			'Symbol = symbol;',
			'Tahoma = tahoma,arial,helvetica,sans-serif;',
			'Terminal = terminal,monaco;',
			'Times New Roman = times new roman,times;',
			'Trebuchet MS = trebuchet ms,geneva;',
			'Verdana = verdana,geneva;',
			'Webdings = webdings;',
			'Wingdings = wingdings,zapf dingbats;',
			'Open Sans =  Open Sans,sans-serif;',
		);

		return apply_filters( 'yith_wcbm_get_badge_editor_fonts', $fonts );
	}
}

if ( ! function_exists( 'yith_wcbm_get_product_badge' ) ) {
	/**
	 * Get Product Badge
	 *
	 * @param int $product_id Product ID.
	 *
	 * @return string|false
	 */
	function yith_wcbm_get_product_badge( $product_id ) {
		$has_badge = false;
		$product   = wc_get_product( $product_id );

		if ( $product ) {
			$old_meta = $product->get_meta( '_yith_wcbm_product_meta' );
			if ( $old_meta ) {
				yith_wcbm_update_product_badge_meta( $product->get_id() );
				$product->read_meta_data( true );
			}
			$is_scheduled  = wc_string_to_bool( $product->get_meta( '_yith_wcbm_badge_schedule' ) );
			$schedule_from = $product->get_meta( '_yith_wcbm_badge_from_date' );
			$schedule_to   = $product->get_meta( '_yith_wcbm_badge_to_date' );
			if ( ! $is_scheduled || ( $schedule_from && $schedule_to && strtotime( '0:0:01', strtotime( $schedule_from ) ) < time() && strtotime( '23:59:59', strtotime( $schedule_to ) ) > time() ) ) {
				$has_badge = true;
			}
		}

		return $has_badge && $product ? $product->get_meta( '_yith_wcbm_badge_ids' ) : false;
	}
}

if ( ! function_exists( 'yith_wcbm_get_badge_defaults' ) ) {
	/**
	 * Return Badge Default values
	 *
	 * @return array
	 */
	function yith_wcbm_get_badge_defaults() {
		return array(
			'type'                        => 'text',
			'text'                        => '',
			'txt_color_default'           => '#000000',
			'bg_color_default'            => '#2470FF',
			'bg_color'                    => '#2470FF',
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
			'id_badge'                    => false,
		);
	}
}

if ( ! class_exists( 'yith_wcbm_get_badge_object' ) ) {
	/**
	 * Get Badge Object
	 *
	 * @param int|WP_Post|YITH_WCBM_Badge $badge The Badge.
	 *
	 * @return false|YITH_WCBM_Badge
	 */
	function yith_wcbm_get_badge_object( $badge = false ) {
		global $post;
		if ( false === $badge && isset( $post, $post->ID ) && get_post_type( $post->ID ) === YITH_WCBM_Post_Types::$badge ) {
			$badge = $post;
		}
		$class = defined( 'YITH_WCBM_PREMIUM' ) && YITH_WCBM_PREMIUM && class_exists( 'YITH_WCBM_Badge_Premium' ) ? 'YITH_WCBM_Badge_Premium' : 'YITH_WCBM_Badge';

		$badge = new $class( $badge );

		return $badge->get_id() ? $badge : false;
	}
}

if ( ! function_exists( 'yith_wcbm_get_badge_svg' ) ) {
	/**
	 * Get Badge SVG file
	 *
	 * @param array $args     Arguments.
	 * @param bool  $use_vars Use CSS vars for style props.
	 *
	 * @return string
	 */
	function yith_wcbm_get_badge_svg( $args, $use_vars = false ) {
		$content = '';
		if ( isset( $args['type'], $args['style'] ) ) {
			$badge = yith_wcbm_get_badge_object( $args['badge'] ?? false );
			if ( ! $use_vars && ! $badge ) {
				return '';
			}

			ob_start();
			$dir = ! ( function_exists( 'yith_wcbm_get_imported_badge_list' ) && in_array( $args['style'], yith_wcbm_get_imported_badge_list( $args['type'] ), true ) ) ? YITH_WCBM_ASSETS_IMAGES_PATH . '/' . $args['type'] . '-badges/' : yith_wcbm_get_badge_library_dir_path( $args['type'] );
			if ( file_exists( $dir . $args['style'] ) ) {
				include $dir . $args['style'];
			}

			$content = ob_get_clean();

			$replacements_to_vars = array(
				'discount_text'                       => '--badge-discount-text',
				'up_to_text'                          => '--badge-up-to-text',
				'background_color'                    => '--badge-primary-color',
				'text_color'                          => '--badge-text-color',
				'secondary_background_color'          => '--badge-secondary-color',
				'secondary_light_background_color'    => '--badge-secondary-light-color',
				'secondary_dark_background_color'     => '--badge-secondary-dark-color',
				'tertiary_background_color'           => '--badge-tertiary-color',
				'triadic_positive_background_color'   => '--badge-triadic-positive-color',
				'triadic_negative_background_color'   => '--badge-triadic-negative-color',
				'analogous_positive_background_color' => '--badge-analogous-positive-color',
				'analogous_negative_background_color' => '--badge-analogous-negative-color',
				'complementary_background_color'      => '--badge-complementary-color',
			);

			$replacements = array(
				'nonce'    => uniqid(),
				'badge_id' => $use_vars ? str_replace( '.svg', '', $args['style'] ) : $badge->get_id(),
			);

			$translate_badge_strings = apply_filters( 'yith_wcbm_translate_badge_strings', true, $badge );
			foreach ( $replacements_to_vars as $replacement => $var ) {
				$value = '';
				if ( $use_vars ) {
					switch ( $replacement ) {
						case 'up_to_text':
							$value = "'" . apply_filters( 'yith_wcbm_up_to_text_advanced_badge', $translate_badge_strings ? _x( 'Up to', 'Text used inside the badges', 'yith-woocommerce-badges-management' ) : 'Up to', $args ) . "'";
							break;
						case 'discount_text':
							$value = "'" . apply_filters( 'yith_wcbm_discount_text_advanced_badge', $translate_badge_strings ? _x( 'Discount', 'Text used inside the badges', 'yith-woocommerce-badges-management' ) : 'Discount', $args ) . "'";
							break;
						default:
							$value = 'var(' . $var . ')';
					}
				} else {
					switch ( $replacement ) {
						case 'up_to_text':
							$value = "'" . apply_filters( 'yith_wcbm_up_to_text_advanced_badge', $translate_badge_strings ? _x( 'Up to', 'Text used inside the badges', 'yith-woocommerce-badges-management' ) : 'Up to', $args ) . "'";
							break;
						case 'discount_text':
							$value = "'" . apply_filters( 'yith_wcbm_discount_text_advanced_badge', $translate_badge_strings ? _x( 'Discount', 'Text used inside the badges', 'yith-woocommerce-badges-management' ) : 'Discount', $args ) . "'";
							break;
						case 'text_color':
							$value = $badge->is_type( 'advanced' ) ? $badge->get_text_color() : $badge->get_text_color_from_text();
							break;
						default:
							$getter = 'get_' . $replacement;
							if ( method_exists( $badge, $getter ) ) {
								$value = $badge->{$getter}();
							}
					}
				}
				$replacements[ $replacement ] = $value;
			}

			foreach ( $replacements as $to_replace => $replace_with ) {
				$content = str_replace( '{{' . $to_replace . '}}', $replace_with, $content );
			}

			$localized_vars = "<style> .yith-wcbm-badge-{$replacements['badge_id']}{";
			foreach ( $replacements_to_vars as $replacement => $var ) {
				if ( array_key_exists( $replacement, $replacements ) ) {
					if ( ! $use_vars || in_array( $replacement, array( 'discount_text', 'up_to_text' ), true ) ) {
						$localized_vars .= $var . ' : ' . $replacements[ $replacement ] . ';';
					}
				}
			}
			$localized_vars .= '} ';

			$content = str_replace( '<style>', $localized_vars, $content );
		}

		return $content;
	}
}

/** ------------------------------------------------------------------------------
 * WPML Integration Functions
 */

if ( ! function_exists( 'yith_wcbm_wpml_register_string' ) ) {
	/**
	 * Register a string in WPML translations.
	 *
	 * @param string $context The Context.
	 * @param string $name    The name.
	 * @param string $value   The value.
	 *
	 * @since  2.0.0
	 */
	function yith_wcbm_wpml_register_string( $context, $name, $value ) {
		do_action( 'wpml_register_single_string', $context, $name, $value );
	}
}

if ( ! function_exists( 'yith_wcbm_wpml_string_translate' ) ) {
	/**
	 * Get a string translation
	 *
	 * @param string $context       The context.
	 * @param string $name          The name.
	 * @param string $default_value The default value.
	 *
	 * @return string The translated string
	 * @since  2.0.0
	 */
	function yith_wcbm_wpml_string_translate( $context, $name, $default_value ) {
		return apply_filters( 'wpml_translate_single_string', $default_value, $context, $name );
	}
}

if ( ! function_exists( 'yith_wcbm_is_wpml_parent_based_on_default_language' ) ) {
	/**
	 * Is WPML parent based on default language?
	 *
	 * @return bool
	 */
	function yith_wcbm_is_wpml_parent_based_on_default_language() {
		return ! ! apply_filters( 'yith_wcbm_is_wpml_parent_based_on_default_language', false );
	}
}

if ( ! function_exists( 'yith_wcbm_wpml_autosync_product_badge_translations' ) ) {
	/**
	 * Does WPML autosync product badge translations?
	 *
	 * @return bool
	 */
	function yith_wcbm_wpml_autosync_product_badge_translations() {
		return ! ! apply_filters( 'yith_wcbm_wpml_autosync_product_badge_translations', false );
	}
}

if ( ! function_exists( 'yith_wcbm_get_local_badges_list' ) ) {
	/**
	 * Get local badge List
	 *
	 * @param string $type The badge type.
	 *
	 * @return array
	 */
	function yith_wcbm_get_local_badges_list( $type = '' ) {
		$badges = array(
			'image'    => array(),
			'css'      => array(),
			'advanced' => array(),
		);
		foreach ( $badges as $badge_type => &$list ) {
			if ( file_exists( YITH_WCBM_PLUGIN_OPTIONS_PATH . 'badge-list/' . $badge_type . '-badges.php' ) ) {
				$list = array_column( include YITH_WCBM_PLUGIN_OPTIONS_PATH . 'badge-list/' . $badge_type . '-badges.php', 'id' );
			}
		}

		return array_key_exists( $type, $badges ) ? $badges[ $type ] : $badges;
	}
}

if ( ! function_exists( 'yith_wcbm_get_local_badge_list_with_data' ) ) {
	/**
	 * Get all local and imported badge list with their data.
	 *
	 * @return array
	 */
	function yith_wcbm_get_local_badge_list_with_data() {
		$badges_data = array(
			'image'    => array(),
			'css'      => array(),
			'advanced' => array(),
		);

		foreach ( $badges_data as $type => &$badges ) {
			if ( file_exists( YITH_WCBM_PLUGIN_OPTIONS_PATH . 'badge-list/' . $type . '-badges.php' ) ) {
				$badges = include YITH_WCBM_PLUGIN_OPTIONS_PATH . 'badge-list/' . $type . '-badges.php';
			}
		}

		return $badges_data;
	}
}

if ( ! function_exists( 'yith_wcbm_get_unique_post_title' ) ) {
	/**
	 * Get unique post title
	 *
	 * @param string $title     The post title.
	 * @param int    $post_id   The post ID.
	 * @param string $post_type The post type.
	 *
	 * @return string
	 */
	function yith_wcbm_get_unique_post_title( $title, $post_id, $post_type = null ) {
		$post_type = is_null( $post_type ) ? get_post_type( $post_id ) : $post_type;
		$count     = 1;

		$args = array( 'title' => $title, 'post_type' => $post_type, 'posts_per_page' => 1, 'fields' => 'ids' );
		while ( ! in_array( current( get_posts( $args ) ), array( absint( $post_id ), false ), true ) ) {
			$args['title'] = $title . ' (' . $count++ . ')';
		}

		return $args['title'];
	}
}

if ( ! function_exists( 'yith_wcbm_get_badges_placeholders' ) ) {
	/**
	 * Get badges placeholders
	 *
	 * @param int|string|WC_Product $product The product.
	 *
	 * @return array
	 */
	function yith_wcbm_get_badges_placeholders( $product = false ) {
		$placeholders        = array(
			'regular_price'    => array(
				'desc'  => __( 'Show the product regular price', 'yith-woocommerce-badges-management' ),
				'value' => '',
			),
			'sale_price'       => array(
				'desc'  => __( 'Show the product sale price', 'yith-woocommerce-badges-management' ),
				'value' => '',
			),
			'stock_quantity'   => array(
				'desc'  => __( 'Show the available stock for the product', 'yith-woocommerce-badges-management' ),
				'note'  => __( 'Note: in product without stock management the badge will be not shown', 'yith-woocommerce-badges-management' ),
				'value' => '',
			),
			'saved_amount'     => array(
				'desc'  => __( "Show the customer's saving amount", 'yith-woocommerce-badges-management' ),
				'value' => '',
			),
			'saved_percentage' => array(
				'desc'  => __( "Show the customer's saving percentage", 'yith-woocommerce-badges-management' ),
				'value' => '',
			),
		);
		$currency_symbol     = get_woocommerce_currency_symbol();
		$placeholders_values = array();
		if ( 'template' === $product ) {
			$placeholders_values = array(
				'sale_price'       => '30' . $currency_symbol,
				'regular_price'    => '50' . $currency_symbol,
				'stock_quantity'   => '5',
				'saved_amount'     => '20' . $currency_symbol,
				'saved_percentage' => '40%',
			);
		}

		$product = $product ? wc_get_product( $product ) : false;

		if ( $product ) {
			if ( ! $product->is_type( 'variable' ) ) {
				$regular_price                        = floatval( $product->get_regular_price() );
				$placeholders_values['regular_price'] = absint( $regular_price ) . $currency_symbol;

				if ( $product->is_on_sale() && $regular_price ) {
					$sale_price                              = floatval( $product->get_sale_price() );
					$placeholders_values['sale_price']       = absint( $sale_price ) . $currency_symbol;
					$placeholders_values['saved_amount']     = absint( $regular_price - $sale_price ) . $currency_symbol;
					$placeholders_values['saved_percentage'] = absint( 100 / $regular_price * ( $regular_price - $sale_price ) ) . '%';
				}

				if ( $product->managing_stock() ) {
					$placeholders_values['stock_quantity'] = $product->get_stock_quantity();
				}
			}
		}

		foreach ( $placeholders_values as $placeholder => $value ) {
			if ( array_key_exists( $placeholder, $placeholders ) ) {
				$placeholders[ $placeholder ]['value'] = $value;
			}
		}

		return apply_filters( 'yith_wcbm_badges_placeholders', $placeholders, $product );
	}
}

if ( ! function_exists( 'yith_wcbm_get_badges_placeholders_values' ) ) {
	/**
	 * Get badges placeholders values
	 *
	 * @param int|string|WC_Product $product The product.
	 *
	 * @return array
	 */
	function yith_wcbm_get_badges_placeholders_values( $product = false ) {
		$placeholders = yith_wcbm_get_badges_placeholders( $product );

		return array_filter( array_combine( array_keys( $placeholders ), array_column( $placeholders, 'value' ) ) );
	}
}

/** ------------------------------------------------------------------------------
 * Update functions
 */

if ( ! function_exists( 'yith_wcbm_update_is_running' ) ) {
	/**
	 * Check if an update is running
	 *
	 * @return bool
	 */
	function yith_wcbm_update_is_running() {
		$table_updates_pending = WC()->queue()->search(
			array(
				'status'   => 'pending',
				'hook'     => 'yith_wcbm_run_update_callback',
				'per_page' => 1,
			)
		);

		return (bool) count( $table_updates_pending );
	}
}

if ( ! function_exists( 'yith_wcbm_update_badge_meta' ) ) {
	/**
	 * Update Badge metas
	 *
	 * @param int $badge_id Badge ID.
	 *
	 * @since 2.0.0
	 */
	function yith_wcbm_update_badge_meta( $badge_id ) {
		$badge_meta = get_post_meta( absint( $badge_id ), '_badge_meta', true );
		if ( $badge_meta ) {
			$badge_meta      = is_array( $badge_meta ) ? $badge_meta : unserialize( get_post_meta( $badge_id, '_badge_meta', true ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_unserialize
			$old_to_new_meta = array(
				'type'      => '_type',
				'text'      => '_text',
				'bg_color'  => '_background_color',
				'image_url' => '_image',
			);
			$size            = array();
			$padding         = array();
			$border_radius   = array();
			foreach ( $badge_meta as $key => $value ) {
				$meta_value = null;

				switch ( $key ) {
					case 'padding_top':
					case 'padding_right':
					case 'padding_bottom':
					case 'padding_left':
						if ( ! isset( $padding['dimensions'] ) ) {
							$padding['dimensions'] = array();
							$padding['unit']       = 'px';
							$padding['linked']     = 'no';
						}
						$padding['dimensions'][ str_replace( 'padding_', '', $key ) ] = absint( $value );
						break;

					case 'border_bottom_right_radius':
					case 'border_bottom_left_radius':
					case 'border_top_right_radius':
					case 'border_top_left_radius':
						if ( ! isset( $border_radius['dimensions'] ) ) {
							$border_radius['dimensions'] = array();
							$border_radius['unit']       = 'px';
							$border_radius['linked']     = 'no';

						}
						$border_radius['dimensions'][ str_replace( array( 'border_', '_radius', '_' ), array( '', '', '-' ), $key ) ] = absint( $value );
						break;

					case 'text':
						$style = "font-family: 'Open Sans', sans-serif;";
						if ( isset( $badge_meta['txt_color'] ) && '#000000' !== $badge_meta['txt_color'] ) {
							$style .= ' color:' . $badge_meta['txt_color'] . ';';
						}
						if ( isset( $badge_meta['font_size'] ) && floatval( $badge_meta['font_size'] ) ) {
							$style .= ' font-size:' . floatval( $badge_meta['font_size'] ) . 'px;';
						}
						$meta_value = '<div style="' . $style . '">' . $value . '</div>';
						break;

					case 'width':
					case 'height':
						if ( ! isset( $size['dimensions'] ) ) {
							$size['dimensions'] = array();
							$size['unit']       = 'px';
							$size['linked']     = 'no';
						}
						$size['dimensions'][ $key ] = absint( $value );
						break;

					case 'image_url':
						$meta_value = str_replace( 'png', 'svg', basename( $value ) );
						break;

					case 'position':
						$values = explode( '-', $value );
						if ( isset( $values[0], $values[1] ) ) {
							update_post_meta( $badge_id, '_position', 'bottom' === $values[0] ? 'bottom' : 'top' );
							update_post_meta( $badge_id, '_alignment', 'right' === $values[1] ? 'right' : 'left' );
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
			update_post_meta( $badge_id, '_size', $size );
			update_post_meta( $badge_id, '_padding', $padding );
			update_post_meta( $badge_id, '_border_radius', $border_radius );
		}
		delete_post_meta( $badge_id, '_badge_meta' );
	}
}

if ( ! function_exists( 'yith_wcbm_update_product_badge_meta' ) ) {
	/**
	 * Update badge product meta
	 *
	 * @param int $product_id Product ID to update.
	 *
	 * @since 2.0.0
	 */
	function yith_wcbm_update_product_badge_meta( $product_id ) {
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
					$value   = 'id_badge' === $key && is_array( $value ) ? current( $value ) : $value;
					$product->update_meta_data( '_yith_wcbm_badge_' . $new_key, $value );
				}
			}
			$product->delete_meta_data( '_yith_wcbm_product_meta' );
			$product->save_meta_data();
		}
	}
}

/** ------------------------------------------------------------------------------
 * Color functions
 */

if ( ! function_exists( 'yith_wcbm_color_with_factor' ) ) {
	/**
	 * Return color with factor
	 *
	 * @param string $color HEX Start Color.
	 * @param float  $fact  Factor.
	 *
	 * @return string
	 */
	function yith_wcbm_color_with_factor( $color, $fact ) {
		$red        = $color[0] . $color[1];
		$green      = $color[2] . $color[3];
		$blue       = $color[4] . $color[5];
		$red_d      = $fact * hexdec( $red );
		$green_d    = $fact * hexdec( $green );
		$blue_d     = $fact * hexdec( $blue );
		$r1         = ( $red_d < 16 ? '0' : '' ) . dechex( intval( $red_d ) );
		$g1         = ( $green_d < 16 ? '0' : '' ) . dechex( intval( $green_d ) );
		$b1         = ( $blue_d < 16 ? '0' : '' ) . dechex( intval( $blue_d ) );
		$dark_color = '#' . $r1 . $g1 . $b1;

		return $dark_color;
	}
}

if ( ! function_exists( 'yith_wcbm_get_hue_rotated_color' ) ) {
	/**
	 * Get Hue rotated color
	 *
	 * @param string $color        Hex Color.
	 * @param int    $hue_rotation HUE rotation.
	 *
	 * @return string
	 */
	function yith_wcbm_get_hue_rotated_color( $color, $hue_rotation ) {
		$hsl = yith_wcbm_hex_to_hsl_color( $color );

		$hsl['h'] += intval( $hue_rotation );

		return 'hsl(' . $hsl['h'] . ' , ' . $hsl['s'] . '% , ' . $hsl['l'] . '% )';
	}
}

if ( ! function_exists( 'yith_wcbm_hex_to_hsl_color' ) ) {
	/**
	 * Convert Hex color into RGB
	 *
	 * @param string $hex_color Hexadecimal Color.
	 *
	 * @return array
	 */
	function yith_wcbm_hex_to_hsl_color( $hex_color ) {
		$hex_color = str_replace( '#', '', $hex_color );
		$red       = hexdec( $hex_color[0] . $hex_color[1] ) / 255;
		$green     = hexdec( $hex_color[2] . $hex_color[3] ) / 255;
		$blue      = hexdec( $hex_color[4] . $hex_color[5] ) / 255;
		$max       = max( $red, $green, $blue );
		$min       = min( $red, $green, $blue );
		$l         = ( $max + $min ) / 2 * 100;
		if ( $max === $min ) {
			$h = 0;
			$s = 0;
		} else {
			$d = $max - $min;
			$s = 100 * ( $l > 50 ? $d / ( 2 - $max - $min ) : $d / ( $max + $min ) );
			if ( $red === $max ) {
				$h = ( $green - $blue ) / $d + ( $green < $blue ? 6 : 0 );
			} else {
				if ( $green === $max ) {
					$h = ( $blue - $red ) / $d + 2;
				} else {
					$h = ( $red - $green ) / $d + 4;
				}
			}
			$h *= 60;
		}

		return array_map( 'intval', compact( 'h', 's', 'l' ) );
	}
}

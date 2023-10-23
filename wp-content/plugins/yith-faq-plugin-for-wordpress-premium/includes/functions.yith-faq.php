<?php
/**
 * FAQ functions file
 *
 * @package YITH\FAQPluginForWordPress
 * @author  YITH <plugins@yithemes.com>
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! function_exists( 'yfwp_get_option' ) ) {

	/**
	 * Get plugin option
	 *
	 * @param string $option  The option name.
	 * @param mixed  $default The default value.
	 *
	 * @return  mixed
	 * @since   1.0.0
	 */
	function yfwp_get_option( $option, $default = false ) {
		return YITH_FAQ_Settings::get_instance()->get_option( 'faq_wp', $option, $default );
	}
}

if ( ! function_exists( 'yfwp_get_default' ) ) {

	/**
	 * Get default values
	 *
	 * @param string $option    The option name.
	 * @param string $suboption The sub-option name.
	 *
	 * @return array|mixed
	 * @since  2.0.0
	 */
	function yfwp_get_default( $option = false, $suboption = false ) {

		$defaults = array(
			'search-field'           => array(
				'background'        => '#ffffff',
				'background-active' => '#fafafa',
				'border'            => '#d3d3d3',
				'border-active'     => '#9ac3c5',
				'placeholder-text'  => '#b6b6b6',
				'active-text'       => '#000000',
			),
			'search-button'          => array(
				'background'       => '#089aa1',
				'background-hover' => '#09b5bc',
				'icon'             => '#ffffff',
				'icon-hover'       => '#ffffff',
			),
			'filters-layout'         => 'minimal',
			'filters-colors'         => array(
				'background'        => '#ffffff',
				'background-hover'  => '#ffffff',
				'background-active' => '#ffffff',
				'border'            => '#a9a9a9',
				'border-hover'      => '#68bdb3',
				'border-active'     => '#68bdb3',
				'text'              => '#000000',
				'text-hover'        => '#000000',
				'text-active'       => '#000000',
			),
			'filters-border'         => array(
				'dimensions' => array(
					'top'    => 5,
					'right'  => 5,
					'bottom' => 5,
					'left'   => 5,
				),
				'linked'     => 'yes',
				'unit'       => 'px',
			),
			'icon-colors'            => array(
				'background'        => 'rgba(255,255,255,0)',
				'background-hover'  => 'rgba(255,255,255,0)',
				'background-active' => 'rgba(255,255,255,0)',
				'icon'              => '#000000',
				'icon-hover'        => '#009f8b',
				'icon-active'       => '#009f8b',
			),
			'icon-border'            => array(
				'dimensions' => array(
					'top'    => 5,
					'right'  => 5,
					'bottom' => 5,
					'left'   => 5,
				),
				'linked'     => 'yes',
				'unit'       => 'px',
			),
			'faq-layout'             => 'minimal',
			'faq-colors'             => array(
				'background'        => '#f8f8f8',
				'background-hover'  => '#ececec',
				'background-active' => '#eef5f4',
				'border'            => 'rgba(255,255,255,0)',
				'border-hover'      => 'rgba(255,255,255,0)',
				'border-active'     => 'rgba(255,255,255,0)',
				'text'              => '#000000',
				'text-hover'        => '#000000',
				'text-active'       => '#0d9c8b',
				'content'           => '#000000',
				'content-hover'     => '#000000',
				'content-active'    => '#000000',
			),
			'faq-border'             => array(
				'dimensions' => array(
					'top'    => 5,
					'right'  => 5,
					'bottom' => 5,
					'left'   => 5,
				),
				'linked'     => 'yes',
				'unit'       => 'px',
			),
			'faq-copy-button'        => 'yes',
			'faq-loader-type'        => 'default',
			'faq-loader-color'       => '#009f8b',
			'faq-loader-custom'      => YITH_FWP_ASSETS_URL . '/images/loader.svg',
			'faq-copy-button-color'  => array(
				'background'       => '#ffffff',
				'background-hover' => '#f3f9f9',
				'icon'             => '#009f8b',
				'icon-hover'       => '#009f8b',
				'border'           => '#009f8b',
				'border-hover'     => '#009f8b',
			),
			'faq-copy-button-border' => array(
				'dimensions' => array(
					'top'    => 20,
					'right'  => 20,
					'bottom' => 20,
					'left'   => 20,
				),
				'linked'     => 'yes',
				'unit'       => 'px',
			),
			'pagination-layout'      => 'minimal',
			'pagination-colors'      => array(
				'background'        => 'rgba(255,255,255,0)',
				'background-hover'  => 'rgba(255,255,255,0)',
				'background-active' => 'rgba(255,255,255,0)',
				'border'            => 'rgba(255,255,255,0)',
				'border-hover'      => 'rgba(255,255,255,0)',
				'border-active'     => 'rgba(255,255,255,0)',
				'text'              => '#8f8f8f',
				'text-hover'        => '#000000',
				'text-active'       => '#009f8b',
			),
			'pagination-border'      => array(
				'dimensions' => array(
					'top'    => 5,
					'right'  => 5,
					'bottom' => 5,
					'left'   => 5,
				),
				'linked'     => 'yes',
				'unit'       => 'px',
			),
		);

		if ( $option ) {
			if ( $suboption ) {
				return $defaults[ $option ][ $suboption ];
			} else {
				return $defaults[ $option ];
			}
		} else {
			return $defaults;
		}

	}
}

if ( ! function_exists( 'yfwp_get_categories' ) ) {

	/**
	 * Get FAQ Categories
	 *
	 * @return  array
	 * @since   1.1.5
	 */
	function yfwp_get_categories() {

		$categories = get_terms(
			array(
				'taxonomy'   => YITH_FWP_FAQ_TAXONOMY,
				'hide_empty' => false,
			)
		);

		$terms = array();
		if ( $categories ) {
			foreach ( $categories as $category ) {
				$terms[ $category->term_id ] = $category->name;
			}
		}

		return $terms;
	}
}

if ( ! function_exists( 'yfwp_get_pages' ) ) {

	/**
	 * Get pages
	 *
	 * @return  array
	 * @since   1.8.0
	 */
	function yfwp_get_pages() {
		$pages = get_posts(
			array(
				'post_type'   => 'page',
				'numberposts' => -1,
				'orderby'     => 'title',
				'order'       => 'ASC',
			)
		);

		$elements = array(
			'' => esc_html__( 'None', 'yith-faq-plugin-for-wordpress' ),
		);
		if ( $pages ) {
			foreach ( $pages as $page ) {
				$elements[ $page->ID ] = $page->post_title;
			}
		}

		return $elements;
	}
}

if ( ! function_exists( 'yfwp_get_elementor_item_for_page' ) ) {

	/**
	 * Check if a page has a determined Elementor widget
	 *
	 * @param integer $post_id      The Post ID where we can check if the widget is used.
	 * @param boolean $get_settings Choose to get the widget settings.
	 *
	 * @return bool|array
	 * @since   1.1.5
	 */
	function yfwp_get_elementor_item_for_page( $post_id, $get_settings = false ) {

		$item_ids = array(
			'yith-faq-shortcode',
			'yith-faq-preset',
		);

		// Check if Elementor is enabled.
		if ( defined( 'ELEMENTOR_VERSION' ) && 0 !== $post_id ) {

			// Check if page is built with Elementor.
			if ( \Elementor\Plugin::$instance->documents->get( $post_id )->is_built_with_elementor() ) {
				if ( ! $get_settings ) {

					// If i only want to check if the Elementor widget is used on the page.
					$meta = get_post_meta( $post_id, '_elementor_controls_usage', true );
					foreach ( $item_ids as $item_id ) {
						if ( is_array( $meta ) && array_key_exists( $item_id, $meta ) ) {
							return true;
						}
					}
				} else {

					// If i want to get the Elementor widget settings.
					$meta = get_post_meta( $post_id, '_elementor_data', true );
					if ( is_string( $meta ) && ! empty( $meta ) ) {
						$meta = json_decode( $meta, true );
					}

					if ( ! empty( $meta ) ) {

						$item_settings = false;
						foreach ( $item_ids as $item_id ) {

							\Elementor\Plugin::$instance->db->iterate_data(
								$meta,
								function ( $element ) use ( $item_id, &$item_settings ) {
									if ( ! empty( $element['widgetType'] ) && $item_id === $element['widgetType'] ) {
										$item_settings = $element['settings'];
									}

									return $element;
								}
							);
						}

						return $item_settings;
					}
				}
			}
		}

		return false;
	}
}

if ( ! function_exists( 'yfwp_get_presets' ) ) {

	/**
	 * Get pages
	 *
	 * @return  array
	 * @since   1.8.0
	 */
	function yfwp_get_presets() {
		$pages = get_posts(
			array(
				'post_type'   => YITH_FWP_SHORTCODE_POST_TYPE,
				'numberposts' => -1,
				'orderby'     => 'title',
				'order'       => 'ASC',
			)
		);

		$elements = array(
			'' => esc_html__( 'Select preset', 'yith-faq-plugin-for-wordpress' ),
		);
		if ( $pages ) {
			foreach ( $pages as $page ) {
				$elements[ $page->ID ] = $page->post_title;
			}
		}

		return $elements;
	}
}

if ( ! function_exists( 'yfwp_create_shortcode' ) ) {

	/**
	 * Create a shortcode from a specific preset.
	 *
	 * @param integer $shortcode_id The shortcode ID.
	 * @param boolean $args_only    Choose to return only the arguments.
	 *
	 * @return string|array
	 * @since  2.0.0
	 */
	function yfwp_create_shortcode( $shortcode_id, $args_only = false ) {

		$shortcode_type = get_post_meta( $shortcode_id, 'yfwp_shortcode_type', true );
		if ( empty( $shortcode_type ) ) {
			return '';
		}
		$allowed_params = yfwp_get_shortcode_allowed_params( $shortcode_type );
		$shortcode_args = array();
		foreach ( $allowed_params as $param ) {
			if ( 'faq_to_show' === $param ) {
				continue;
			}
			$value = get_post_meta( $shortcode_id, 'yfwp_' . $param, true );
			$value = is_array( $value ) ? implode( ',', $value ) : $value;
			if ( ! empty( $value ) && yfwp_get_shortcode_defaults( $param ) !== $value ) {
				if ( $args_only ) {
					$shortcode_args[ $param ] = $value;
				} else {

					$shortcode_args[] = $param . '="' . $value . '"';
				}
			}
		}
		if ( $args_only ) {
			return $shortcode_args;
		} else {
			$shortcode_args = implode( ' ', $shortcode_args );
			$spacer         = ! empty( $shortcode_args ) ? ' ' : '';
			$shortcode_type = ( 'faqs' !== $shortcode_type ? '_summary' : '' );

			return "[yith_faq$shortcode_type$spacer$shortcode_args]";
		}
	}
}

if ( ! function_exists( 'yfwp_get_shortcode_defaults' ) ) {

	/**
	 * Get shortcode options defaults
	 *
	 * @param array|string $options The key, or the keys, of the options to retrieve.
	 *
	 * @return array|string
	 * @since  2.0.0
	 */
	function yfwp_get_shortcode_defaults( $options ) {

		$defaults = array(
			'search_box'       => 'off',
			'category_filters' => 'off',
			'style'            => 'list',
			'title'            => '',
			'title_type'       => 'h3',
			'show_pagination'  => 'on',
			'page_size'        => 10,
			'faq_to_show'      => 'all',
			'categories'       => array(),
			'expand_faq'       => 'all-closed',
			'show_icon'        => 'right',
			'icon_size'        => 14,
			'icon'             => 'yfwp:plus',
			'page_id'          => '',
		);

		if ( is_array( $options ) ) {
			$result = array();
			foreach ( $options as $option ) {
				$result[ $option ] = $defaults[ $option ];
			}

			return $result;
		} else {
			return $defaults[ $options ];
		}

	}
}

if ( ! function_exists( 'yfwp_get_shortcode_allowed_params' ) ) {

	/**
	 * Get the allowed parameters for each allowed shortcode
	 *
	 * @param string $type The shortcode type.
	 *
	 * @return array
	 * @since  2.0.0
	 */
	function yfwp_get_shortcode_allowed_params( $type ) {
		$params = array(
			'faqs'    => array(
				'search_box',
				'category_filters',
				'style',
				'faq_to_show',
				'categories',
				'expand_faq',
				'show_pagination',
				'page_size',
				'show_icon',
				'icon_size',
				'icon',
			),
			'summary' => array(
				'title',
				'title_type',
				'faq_to_show',
				'categories',
				'page_id',
			),
		);

		return $params[ $type ];
	}
}

if ( ! function_exists( 'yfwp_action_link' ) ) {

	/**
	 * Shortcodes action links
	 *
	 * @param string          $action       The action to perform.
	 * @param boolean|integer $shortcode_id The shortcode ID.
	 * @param boolean|string  $page         The page slug.
	 * @param boolean|integer $paged        The page number.
	 *
	 * @return string
	 * @since  2.0.0
	 */
	function yfwp_action_link( $action, $shortcode_id = false, $page = false, $paged = false ) {

		$args = array(
			'action' => "yfwp_$action",
		);

		if ( $shortcode_id ) {
			$args['shortcode'] = $shortcode_id;
		}

		if ( $page ) {
			$args['page'] = $page;
		}

		if ( $paged ) {
			$args['paged'] = $paged;
		}

		return add_query_arg( $args, wp_nonce_url( admin_url( 'admin.php' ), $action ) );

	}
}

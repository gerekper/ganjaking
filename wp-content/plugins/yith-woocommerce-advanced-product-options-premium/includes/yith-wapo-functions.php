<?php
/**
 * WAPO Functions
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\ProductAddOns
 * @version 2.0.0
 */

defined( 'YITH_WAPO' ) || exit; // Exit if accessed directly.

if ( ! function_exists( 'yith_wapo_get_view' ) ) {
	/**
	 * Get the view
	 *
	 * @param string $view View name.
	 * @param array  $args Parameters to include in the view.
     * @param string $prefix Prefix for the view path.
     */
	function yith_wapo_get_view( $view, $args = array(), $prefix = '' ) {
		$view_path = trailingslashit( YITH_WAPO_VIEWS_PATH ) . $prefix . $view;

		extract( $args ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract

		if ( file_exists( $view_path ) ) {
			include $view_path;
		}
	}
}

//TODO: Create integration with WOOCS - Currency Switcher for WooCommerce
if ( ! function_exists( 'yith_wapo_get_currency_rate' ) ) {
	/**
	 * Get Currency Rate Function
	 *
	 * @param string $type The type.
	 *
	 * @return float|int
	 */
	function yith_wapo_get_currency_rate( $type = '' ) {
		$currency_rate = 1;
		global $WOOCS; // phpcs:ignore
		if ( $WOOCS && 'product' === $type ) { // phpcs:ignore

			$default_currency = $WOOCS->default_currency; // phpcs:ignore
			$current_currency = $WOOCS->current_currency; // phpcs:ignore
			if ( $default_currency !== $current_currency ) {
				$currencies = $WOOCS->get_currencies(); // phpcs:ignore
				if ( is_array( $currencies ) && isset( $currencies[ $current_currency ] ) ) {
					$currency_rate = $currencies[ $current_currency ]['rate'];
				}
			}
		}
		return apply_filters( 'yith_wapo_get_currency_rate', $currency_rate, $type );
	}
}

if ( ! function_exists( 'yith_wapo_get_option_info' ) ) {
	/**
	 * Get Option Info
	 *
	 * @param int     $addon_id Addon ID.
	 * @param int     $option_id Option ID.
	 * @param boolean $calculate_taxes Boolean to calculate taxes on prices.
	 * @return array
	 */
	function yith_wapo_get_option_info( $addon_id, $option_id, $calculate_taxes = true ) {

		$info = array();

		if ( $addon_id > 0 ) {

			$addon = yith_wapo_instance_class( 'YITH_WAPO_Addon',
                array(
                    'id'   => $addon_id,
                )
            );

			// Option.
			$info['color']             = $addon->get_option( 'color', $option_id );
            $info['color_b']           = $addon->get_option( 'color_b', $option_id, '', false );
            $info['label']             = $addon->get_option( 'label', $option_id );
			$info['label_in_cart']     = $addon->get_option( 'label_in_cart', $option_id );
			$info['label_in_cart_opt'] = $addon->get_option( 'label_in_cart_opt', $option_id );
			$info['tooltip']           = $addon->get_option( 'tooltip', $option_id );
			$info['price_method']      = $addon->get_option( 'price_method', $option_id, 'free', false );
			$info['price_type']        = $addon->get_option( 'price_type', $option_id, 'fixed', false );
			$info['price']             = $addon->get_price( $option_id, $calculate_taxes );
			$info['price_sale']        = $addon->get_sale_price( $option_id, $calculate_taxes );

			// Addon settings.
			$info['addon_label']       = $addon->get_setting( 'title', '' );
			$info['title_in_cart']     = $addon->get_setting( 'title_in_cart', 'yes', false );
			$info['title_in_cart_opt'] = $addon->get_setting( 'title_in_cart_opt', '' );
			$info['addon_type']        = $addon->get_setting( 'type', '' );
			$info['sell_individually'] = $addon->get_setting( 'sell_individually', 'no', false );

			if ( 'product' === $info['addon_type'] ) {
				$info['product_id'] = $addon->get_option( 'product', $option_id );
			}

			// Addon advanced.
			$info['addon_first_options_selected'] = $addon->get_setting( 'first_options_selected' );
			$info['addon_first_free_options']     = $addon->get_setting( 'first_free_options' );

		}
		return $info;
	}
}

if ( ! function_exists( 'yith_wapo_get_option_label' ) ) {
	/**
	 * Get Option Label
	 *
	 * @param int $addon_id Addon ID.
	 * @param int $option_id Option ID.
	 * @return string
	 */
	function yith_wapo_get_option_label( $addon_id, $option_id ) {

		$label = '';
		$info  = yith_wapo_get_option_info( $addon_id, $option_id );

		if ( ! empty( $info ) && is_array( $info ) ) {
			if ( in_array(
				$info['addon_type'],
				array(
					'checkbox',
					'radio',
					'color',
					'select',
					'label',
					'file',
					'product',
				),
				true
			) ) {
				$label = isset( $info['addon_label'] ) && ! empty( $info['addon_label'] ) ? $info['addon_label'] : _x( 'Option', '[FRONT] Show it in the cart page if the add-on has not a label set', 'yith-woocommerce-product-add-ons' );
			} else {
				$label = isset( $info['label'] ) && ! empty( $info['label'] ) ? $info['label'] : _x( 'Option', '[FRONT] Show it in the cart page if the add-on has not a label set', 'yith-woocommerce-product-add-ons' );
			}
		}

		return $label;
	}
}

if ( ! function_exists( 'yith_wapo_get_option_price' ) ) {
	/**
	 * Get Option Price
	 *
	 * @param int $product_id Product ID.
	 * @param int $addon_id Addon ID.
	 * @param int $option_id Option ID.
	 * @param int $quantity Option Quantity.
	 */
	function yith_wapo_get_option_price( $product_id, $addon_id, $option_id, $quantity = 0 ) {
		$info              = yith_wapo_get_option_info( $addon_id, $option_id );
		$option_price      = '';
		$option_price_sale = '';
		if ( 'percentage' === $info['price_type'] ) {
			$_product = wc_get_product( $product_id );

			// WooCommerce Measurement Price Calculator (compatibility).
			if ( isset( $cart_item['pricing_item_meta_data']['_price'] ) ) {
				$product_price = $cart_item['pricing_item_meta_data']['_price'];
			} else {
				$product_price = ( $_product instanceof WC_Product ) ? floatval( $_product->get_price() ) : 0;
			}
			// end WooCommerce Measurement Price Calculator (compatibility).
			$option_percentage      = floatval( $info['price'] );
			$option_percentage_sale = floatval( $info['price_sale'] );
			$option_price           = ( $product_price / 100 ) * $option_percentage;
			$option_price_sale      = ( $product_price / 100 ) * $option_percentage_sale;
		} elseif ( 'multiplied' === $info['price_type'] ) {
			$option_price      = $info['price'] * $quantity;
			$option_price_sale = $info['price'] * $quantity;
		} else {
			$option_price      = $info['price'];
			$option_price_sale = $info['price_sale'];
		}

		return array(
			'price'      => $option_price,
			'price_sale' => $option_price_sale,
		);

	}
}

if ( ! function_exists( 'yith_wapo_get_tax_rate' ) ) {
	/**
	 * Get WooCommerce Tax Rate
	 *
	 * @param float $price The price.
	 */
	function yith_wapo_get_tax_rate( $price = false ) {
		$wc_tax_rate = false;

		if ( get_option( 'woocommerce_calc_taxes', 'no' ) === 'yes' ) {

			$wc_tax_rates = WC_Tax::get_rates();

			if ( is_cart() || is_checkout() ) {
				$wc_tax_rate = false;

				if ( get_option( 'woocommerce_prices_include_tax' ) === 'no' && get_option( 'woocommerce_tax_display_cart' ) === 'incl' ) {
					$wc_tax_rate = reset( $wc_tax_rates )['rate'] ?? 0;
				}
				if ( get_option( 'woocommerce_prices_include_tax' ) === 'yes' && get_option( 'woocommerce_tax_display_cart' ) === 'excl' ) {

					$wc_tax_rate = - reset( $wc_tax_rates )['rate'] ?? 0;

				}
			} else {
				if ( get_option( 'woocommerce_prices_include_tax' ) === 'no' && get_option( 'woocommerce_tax_display_shop' ) === 'incl' ) {
					$wc_tax_rate = reset( $wc_tax_rates )['rate'] ?? 0;
				}
				if ( get_option( 'woocommerce_prices_include_tax' ) === 'yes' && get_option( 'woocommerce_tax_display_shop' ) === 'excl' ) {
					$wc_tax_rate = - reset( $wc_tax_rates )['rate'] ?? 0;
				}
			}
		}

		return $wc_tax_rate;
	}
}

if ( ! function_exists( 'yith_wapo_is_addon_type_available' ) ) {
	/**
	 * Is addon type available
	 *
	 * @param string $addon_type Addon type.
	 */
	function yith_wapo_is_addon_type_available( $addon_type ) {
		if ( '' === $addon_type || substr( $addon_type, 0, 5 ) === 'html_' || in_array( $addon_type, YITH_WAPO()->get_available_addon_types(), true ) ) {
			return true;
		}
		return false;
	}
}

if ( ! function_exists( 'yith_wapo_previous_version_exists' ) ) {
	/**
	 * Previous version 1 check
	 */
	function yith_wapo_previous_version_exists() {

		global $wpdb;

		$table_name = $wpdb->prefix . 'yith_wapo_groups';

		$query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table_name ) );

		if ( $wpdb->get_var( $query ) === $table_name ) { // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared
			return true;
		}
		return ( $wpdb->get_var( $query ) === $table_name ) ? true : false; // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared
	}
}

if ( ! function_exists( 'yith_wapo_product_has_blocks' ) ) {
	/**
	 * Product has blocks
	 *
	 * @param int $product_id Product ID.
	 */
	function yith_wapo_product_has_blocks( $product_id ) {

		if ( ! $product_id ) {
			return false;
		}

		$product = wc_get_product( $product_id );

		if ( $product instanceof WC_Product ) {
            //TODO: remove this global exclusion from v1.
            $exclude_global = apply_filters( 'yith_wapo_exclude_global', get_post_meta( $product_id, '_wapo_disable_global', true ) === 'yes' ? 1 : 0 );
            $blocks         = YITH_WAPO_DB()->yith_wapo_get_blocks_by_product( $product, null, true );

            if ( ! $exclude_global && count( $blocks ) > 0 ) {
                return true;
            }
        }
		return false;

	}
}

if ( ! function_exists( 'yith_wapo_wpml_register_string' ) ) {
	/**
	 * Register a string in wpml translation.
	 *
	 * @param string $context The context name.
	 * @param string $name    The name.
	 * @param string $value   The value to translate.
	 */
	function yith_wapo_wpml_register_string( $context, $name, $value ) {
		do_action( 'wpml_register_single_string', $context, $name, $value );
	}
}

if ( ! function_exists( 'yith_wapo_get_term_meta' ) ) {
	/**
	 * Get term meta.
	 *
	 * @param integer|string $term_id The term ID.
	 * @param string         $key The term meta key.
	 * @param boolean        $single Optional. Whether to return a single value.
	 * @param string         $taxonomy Optional. The taxonomy slug.
	 * @return mixed
	 */
	function yith_wapo_get_term_meta( $term_id, $key, $single = true, $taxonomy = '' ) {
		$value = get_term_meta( $term_id, $key, $single );

		// Compatibility with old format. To be removed on next version.
		if ( apply_filters( 'yith_wapo_get_term_meta', true, $term_id ) && ( false === $value || '' === $value ) && ! empty( $taxonomy ) ) {
			$value = get_term_meta( $term_id, $taxonomy . $key, $single );
			// If meta is not empty, save it with the new key.
			if ( false !== $value && '' !== $value ) {
				yith_wapo_update_term_meta( $term_id, $key, $value );
				// Delete old meta.
				// delete_term_meta( $term_id, $taxonomy . $key );.
			}
		}

		return $value;
	}
}

if ( ! function_exists( 'yith_wapo_update_term_meta' ) ) {
	/**
	 * Update term meta.
	 *
	 * @param integer|string $term_id The term ID.
	 * @param string         $key The term meta key.
	 * @param mixed          $meta_value Metadata value.
	 * @param mixed          $prev_value Optional. Previous value to check before updating.
	 * @return mixed
	 */
	function yith_wapo_update_term_meta( $term_id, $key, $meta_value, $prev_value = '' ) {
		if ( '' === $meta_value || false === $meta_value ) {
			return delete_term_meta( $term_id, $key );
		}

		return update_term_meta( $term_id, $key, $meta_value, $prev_value );
	}
}

if ( ! function_exists( 'yith_wapo_calculate_price_depending_on_tax' ) ) {
	/**
	 * Calculate the price with the tax included if necessary.
	 *
	 * @param float $price The price added.
	 * @return float
	 */
	function yith_wapo_calculate_price_depending_on_tax( $price = 0 ) {

		if ( ! wc_tax_enabled() ) {
			return $price;
		}

		if ( 0 !== $price && '' !== $price ) {

			if ( get_option( 'woocommerce_calc_taxes', 'no' ) === 'yes' ) {
				$price             = floatval( $price );
				$wc_tax_rates      = WC_Tax::get_rates();
				$wc_tax_rate       = reset( $wc_tax_rates )['rate'] ?? 0;
				$price_include_tax = get_option( 'woocommerce_prices_include_tax' );
				if ( is_cart() || is_checkout() ) {
					$tax_display_cart = get_option( 'woocommerce_tax_display_cart' );

					if ( 'no' === $price_include_tax && 'incl' === $tax_display_cart ) {
						$price += floatval( $price ) * floatval( $wc_tax_rate / 100 );
					}
					if ( 'yes' === $price_include_tax && 'excl' === $tax_display_cart ) {
						$price = $wc_tax_rate > 0 ? ( 100 * $price ) / ( 100 + $wc_tax_rate ) : $price;
					}
				} else {
					$tax_display_shop = get_option( 'woocommerce_tax_display_shop' );
					if ( 'no' === $price_include_tax && 'incl' === $tax_display_shop ) {
						$price += floatval( $price ) * floatval( $wc_tax_rate / 100 );
					}

					if ( 'yes' === $price_include_tax && 'excl' === $tax_display_shop ) {
						$price = $wc_tax_rate > 0 ? ( 100 * $price ) / ( 100 + $wc_tax_rate ) : $price;
					}
				}
			}
		}

		return $price;
	}
}

if ( ! function_exists( 'yith_wapo_get_addon_tabs' ) ) {
	/**
	 * Get add-ons tabs.
	 *
	 * @param int    $addon_id The add-on id.
	 * @param string $addon_type The add-on type.
	 *
	 * @return array
	 */
	function yith_wapo_get_addon_tabs( $addon_id, $addon_type ) {

		$tabs = array(
			'populate'          => array(
				'id'    => 'options-list',
				'class' => 'selected',
                // translators: Populate options tab of the add-on configuration
				'label' => esc_html__( 'Populate options', 'yith-woocommerce-product-add-ons' ),
			),
			'advanced'          => array(
				'id'    => 'advanced-settings',
				'class' => '',
                // translators: Options configuration tab of the add-on configuration
                'label' => esc_html__( 'Options configuration', 'yith-woocommerce-product-add-ons' ),
			),
			'conditional-logic' => array(
				'id'    => 'conditional-logic',
				'class' => '',
                // translators: Conditional logic tab of the add-on configuration
				'label' => esc_html__( 'Conditional logic', 'yith-woocommerce-product-add-ons' ),
            )
		);

        if ( ! defined( 'YITH_WAPO_PREMIUM' ) && 'radio' === $addon_type ) {
            unset( $tabs['advanced'] );
        }

		return apply_filters( 'yith_wapo_get_addon_tabs', $tabs, $addon_id, $addon_type );
	}
}

if ( ! function_exists( 'yith_wapo_get_attributes' ) ) {

	function yith_wapo_get_attributes() {

		$tooltip_options = get_option( 'yith_wapo_tooltip_color', array(
            'text' => '#ffffff',
            'background' => '#03bfac'
        ) );
        $dimensions_array_default = array(
            'dimensions' => array(
                'top'    => '',
                'right'  => '',
                'bottom' => '',
                'left'   => '',
            ),
        );

        $block_padding = get_option( 'yith_wapo_style_addon_padding', $dimensions_array_default );

		// Options declared as multi-colorpicker.
		$multi_atts = array(
			'block-background'    => get_option( 'yith_wapo_style_addon_background', array(
                'color' => '#ffffff'
            ) ),
			'accent-color'        => get_option( 'yith_wapo_style_accent_color', array(
                'color' => '#03bfac'
            ) ),
			'form-border-color'   => get_option( 'yith_wapo_style_borders_color', array(
                'color' => '#7a7a7a'
            ) ),
			'price-box-colors'    => get_option( 'yith_wapo_price_box_colors', array(
                'text'       => '#474747',
                'background' => '#ffffff'
            ) ),
			'uploads-file-colors' => get_option( 'yith_wapo_upload_file_colors', array(
                'background' => '#f3f3f3',
                'border'     => '#c4c4c4'
            ) ),
			'tooltip-colors'      => $tooltip_options
		);

		$attributes[ 'required-option-color' ] = get_option( 'yith_wapo_required_option_color', '#AF2323' );
		$attributes[ 'checkbox-style' ]        = get_option( 'yith_wapo_style_checkbox_style' ) === 'rounded' ? '50%' : '5px';
		$attributes[ 'color-swatch-style' ]    = get_option( 'yith_wapo_style_color_swatch_style' ) === 'rounded' ? '50%' : '2px';
		$attributes[ 'label-font-size' ]       = get_option( 'yith_wapo_style_label_font_size', 16 ) . 'px';
		$attributes[ 'description-font-size' ] = get_option( 'yith_wapo_style_description_font_size', 12 ) . 'px';
		$attributes[ 'color-swatch-size' ]     = get_option( 'yith_wapo_style_color_swatch_size', 40 ) . 'px';
		$attributes[ 'block-padding' ]         = $block_padding['dimensions']['top'] . 'px ' .
            $block_padding['dimensions']['right'] . 'px ' .
            $block_padding['dimensions']['bottom'] . 'px ' .
            $block_padding['dimensions']['left'] . 'px ';

		foreach( $multi_atts as $atts => $options ) {
			foreach ( $options as $key => $value ) {
				$attributes[ $atts . '-' . $key ] = $value;
			}
		}

		return apply_filters( 'yith_wapo_get_attributes', $attributes );
	}
}

if ( ! function_exists( 'get_configuration_options_by_type' ) ) {
	/**
	 * Get the options of each add-on type.
	 *
	 * @param string $addon_type The add-on type
	 * @param string $option_tab The add-on tab
	 *
	 * @return array
	 */
	function get_configuration_options_by_type( $addon_type = '', $option_tab = '' ) {
		$options = array();

		if ( $addon_type ) {
			if ( 'configuration' === $option_tab ) {
				switch ( $addon_type ) {
					case 'checkbox':
					case 'text':
					case 'textarea':
					case 'color':
					case 'label':
					case 'colorpicker':
						array_push( $options,
							'addon-selection-type',
							'addon-first-options-selected',
							'addon-first-free-options',
							'addon-enable-min-max',
							'addon-min-exa-rules',
							'addon-max-rule',
							'addon-sell-individually'
						);
						break;
					case 'radio':
					case 'date':
						$options[] = 'addon-sell-individually';
						break;
					case 'select':
						array_push( $options,
							'addon-required',
							'addon-sell-individually'
						);
						break;
					case 'product':
					case 'file':
						array_push( $options,
							'addon-selection-type',
							'addon-enable-min-max',
							'addon-min-exa-rules',
							'addon-max-rule',
							'addon-sell-individually'
						);
						break;
					case 'number':
						array_push( $options,
							'addon-selection-type',
							'addon-first-options-selected',
							'addon-first-free-options',
							'addon-enable-min-max',
							'addon-min-exa-rules',
							'addon-max-rule',
							'addon-enable-min-max-all',
							'min-max-number',
							'addon-sell-individually'
						);
				};

			} elseif ( 'style' === $option_tab ) {

				switch ( $addon_type ) {
					case 'checkbox':
					case 'text':
					case 'textarea':
					case 'color':
					case 'colorpicker':
					case 'radio':
					case 'date':
					case 'file':
					case 'number':
						array_push( $options,
							'addon-show-image',
							'addon-image',
							'addon-image-replacement',
							'addon-hide-options-images',
							'addon-options-images-position',
							'addon-show-as-toggle',
							'addon-hide-options-label',
							'addon-hide-options-prices',
							'addon-options-per-row',
							'addon-show-in-a-grid',
							'addon-options-width',
						);
						break;
					case 'product':
						array_push( $options,
							'addon-show-image',
							'addon-image',
							'addon-show-as-toggle',
							'addon-hide-products-prices',
							'addon-show-sku',
							'addon-show-stock',
							'addon-show-add-to-cart',
							'addon-show-quantity',
							'addon-product-out-of-stock',
							'addon-options-per-row',
							'addon-show-in-a-grid',
							'addon-options-width',
						);
						break;
					case 'select':
						array_push( $options,
							'addon-show-image',
							'addon-image',
							'addon-image-replacement',
							'addon-hide-options-images',
							'addon-options-images-position',
							'addon-show-as-toggle',
							'addon-hide-options-label',
							'addon-hide-options-prices',
							'addon-select-width'
						);
						break;
					case 'label':
						array_push( $options,
							'addon-show-image',
							'addon-image',
							'addon-image-replacement',
							'addon-hide-options-images',
                            'addon-image-equal-height',
                            'addon-images-height',
                            'addon-options-images-position',
							'addon-show-as-toggle',
							'addon-hide-options-label',
							'addon-hide-options-prices',
							'addon-options-per-row',
							'addon-show-in-a-grid',
							'addon-options-width',
							'addon-label-content-align',
							'addon-label-position',
							'addon-description-position',
							'addon-label-padding'
						);
						break;
				};

			}
		}

		return $options;
	}
}

if ( ! function_exists( 'get_default_configuration_options' ) ) {
	/**
	 * Get the default options for Options configuration tab.
	 *
	 * @return array[]
	 */
	function get_default_configuration_options() {

		$options = array(
			'parent' => array(
				'enabled-by' => '',
				'title' => '',
                'field-wrap-class' => '',
                'div-class' => '',
				'field' => array(
					array()
				),
				'description' => '',
			),
			'field' => array(
				'title' => '',
				'div-class' => '',
				'name' => '',
				'class' => '',
				'type' => '',
				'min'   => '',
				'max'   => '',
				'step'  => '',
				'value' => '',
				'default' => '',
				'options' => array(),
				'units' => '',
			)
		);

		return $options;
	}
}

if ( ! function_exists( 'yith_wapo_get_string_by_addon_type' ) ) {
    /**
     * Return a string depending on add-on type.
     *
     * @param string $key The key of the array of values.
     * @param string $addon_type The add-on type.
     * @return string
     */
    function yith_wapo_get_string_by_addon_type( $key, $addon_type )
    {
        $str_values = array(
            'checkbox' => array(
                // translators: [ADMIN] Add-on editor > Options configuration option
                'add_new' => _x( 'Add a new', 'Add-on editor panel > Add a new + add-on name (fem)', 'yith-woocommerce-product-add-ons' ),
            ),
            'number' => array(
                // translators: [ADMIN] Add-on editor > Options configuration option
                'single_option' => __( 'Single - Users can fill ONE of the available number fields', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] Add-on editor > Options configuration option
                'multiple_options' => __( 'Multiple - Users can fill MULTIPLE number fields', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] Add-on editor > Options configuration option
                'selection_description' => __( 'Choose if users can fill one or multiple number fields.', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] Add-on editor > Options configuration option
                'first_options' =>  __( 'Set the first number fields selected as free', 'yith-woocommerce-product-add-ons' ),
                'first_options_description' => sprintf(
                // translators: %1$s and %2$s are line breaks.
                    esc_html__( 'Enable to set a specific number of number fields as free. %1$s For example, the first three "pizza toppings" are free, included in the product price. %2$s Users will pay from the fourth topping on.', 'yith-woocommerce-product-add-ons' ),
                    '<br>',
                    '<br>'
                ),
                // translators: [ADMIN] Add-on editor > Options configuration option
                'select_free' => __( 'Users can fill for free', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] Add-on editor > Options configuration option (description)
                'can_select_for_free' => __( 'Set how many number fields users can fill for free.', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] Add-on editor > Options configuration option
                'force_select' => __( 'Force user to fill number fields of this block', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] Add-on editor > Options configuration option (description)
                'force_select_description' => __( 'Enable to force users to fill number fields to proceed with the purchase.', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] Add-on editor > Options configuration option
                'proceed_purchase' => __( 'To proceed with the purchase, users have to fill', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] Add-on editor > Options configuration option (description)
                'proceed_purchase_description' => __( 'Set how many number fields need to be filled in order to add a product to cart.', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] Add-on editor > Options configuration option
                'can_select_max' => __( 'Users can fill a max of', 'yith-woocommerce-product-add-ons' ),
                'can_select_max_description' => sprintf(
                // translators: %s is a line break. [ADMIN] Add-on editor > Options configuration option (description)
                    esc_html__( 'Optional: set the max number of number fields fillable by users in this block. %s Leave empty if users can select all number fields without any limits.', 'yith-woocommerce-product-add-ons' ),
                    '<br>'
                ),
                // translators: [ADMIN] Add-on editor > Options configuration option
                'min_max_all' => __( 'Set a min/max value among all number fields', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] Add-on editor > Options configuration option (description)
                'min_max_all_description' => __( 'Enable to force users to enter values that are within a specific range when all number fields are added together.', 'yith-woocommerce-product-add-ons' ),

                // translators: [ADMIN] Add-on editor > Options configuration option
                'min_max_number' => __( 'Sum of number fields between', 'yith-woocommerce-product-add-ons' ),

                // translators: [ADMIN] text description on several "Options configuration" options, depending on add-on type
                'options' => __( 'number fields' , 'yith-woocommerce-product-add-ons' ),
            ),
            'color' => array(
                // translators: [ADMIN] Add-on editor > Options configuration option
                'single_option' => __( 'Single - Users can select ONE of the available colors', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] Add-on editor > Options configuration option
                'multiple_options' => __( 'Multiple - Users can select MULTIPLE colors', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] Add-on editor > Options configuration option
                'selection_description' => __( 'Choose if users can select one or multiple colors.', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] Add-on editor > Options configuration option
                'first_options' =>  __( 'Set the first colors selected as free', 'yith-woocommerce-product-add-ons' ),
                'first_options_description' => sprintf(
                // translators: %1$s and %2$s are line breaks.
                    esc_html__( 'Enable to set a specific number of colors as free. %1$s For example, the first three "pizza toppings" are free, included in the product price. %2$s Users will pay from the fourth topping on.', 'yith-woocommerce-product-add-ons' ),
                    '<br>',
                    '<br>'
                ),
                // translators: [ADMIN] Add-on editor > Options configuration option
                'select_free' => __( 'Users can select for free', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] Add-on editor > Options configuration option (description)
                'can_select_for_free' => __( 'Set how many colors users can select for free.', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] Add-on editor > Options configuration option
                'force_select' => __( 'Force user to select colors of this block', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] Add-on editor > Options configuration option (description)
                'force_select_description' => __( 'Enable to force users to select colors to proceed with the purchase.', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] Add-on editor > Options configuration option
                'proceed_purchase' => __( 'To proceed with the purchase, users have to select', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] Add-on editor > Options configuration option (description)
                'proceed_purchase_description' => __( 'Set how many colors need to be selected in order to add a product to cart.', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] Add-on editor > Options configuration option
                'can_select_max' => __( 'Users can select a max of', 'yith-woocommerce-product-add-ons' ),
                'can_select_max_description' => sprintf(
                // translators: %s is a line break. [ADMIN] Add-on editor > Options configuration option (description)
                    esc_html__( 'Optional: set the max number of colors selectable by users in this block. %s Leave empty if users can select all colors without any limits.', 'yith-woocommerce-product-add-ons' ),
                    '<br>'
                ),

                // translators: [ADMIN] text description on several "Options configuration" options, depending on add-on type
                'options' => __( 'colors' , 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] Add-on editor > Options configuration option
                'add_new' => _x( 'Add a new', 'Add-on editor panel > Add a new + add-on name (fem)', 'yith-woocommerce-product-add-ons' ),

            ),
            'colorpicker' => array(
                // translators: [ADMIN] Add-on editor > Options configuration option
                'single_option' => __( 'Single - Users can select ONE of the available colors', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] Add-on editor > Options configuration option
                'multiple_options' => __( 'Multiple - Users can select MULTIPLE colors', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] Add-on editor > Options configuration option
                'selection_description' => __( 'Choose if users can select one or multiple colors.', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] Add-on editor > Options configuration option
                'first_options' =>  __( 'Set the first colors selected as free', 'yith-woocommerce-product-add-ons' ),
                'first_options_description' => sprintf(
                // translators: %1$s and %2$s are line breaks.
                    esc_html__( 'Enable to set a specific number of colors as free. %1$s For example, the first three "pizza toppings" are free, included in the product price. %2$s Users will pay from the fourth topping on.', 'yith-woocommerce-product-add-ons' ),
                    '<br>',
                    '<br>'
                ),
                // translators: [ADMIN] Add-on editor > Options configuration option
                'select_free' => __( 'Users can select for free', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] Add-on editor > Options configuration option (description)
                'can_select_for_free' => __( 'Set how many colors users can select for free.', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] Add-on editor > Options configuration option
                'force_select' => __( 'Force user to select colors of this block', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] Add-on editor > Options configuration option (description)
                'force_select_description' => __( 'Enable to force users to select colors to proceed with the purchase.', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] Add-on editor > Options configuration option
                'proceed_purchase' => __( 'To proceed with the purchase, users have to select', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] Add-on editor > Options configuration option (description)
                'proceed_purchase_description' => __( 'Set how many colors need to be selected in order to add a product to cart.', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] Add-on editor > Options configuration option
                'can_select_max' => __( 'Users can select a max of', 'yith-woocommerce-product-add-ons' ),
                'can_select_max_description' => sprintf(
                // translators: %s is a line break. [ADMIN] Add-on editor > Options configuration option (description).
                    esc_html__( 'Optional: set the max number of colors selectable by users in this block. %s Leave empty if users can select all colors without any limits.', 'yith-woocommerce-product-add-ons' ),
                    '<br>'
                ),

                // translators: [ADMIN] text description on several "Options configuration" options, depending on add-on type
                'options' => __( 'colors' , 'yith-woocommerce-product-add-ons' ),
            ),
            'product' => array(
                // translators: [ADMIN] Add-on editor > Options configuration option
                'single_option' => __( 'Single - Users can select ONE of the available products', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] Add-on editor > Options configuration option
                'multiple_options' => __( 'Multiple - Users can select MULTIPLE products', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] Add-on editor > Options configuration option
                'selection_description' => __( 'Choose if users can select one or multiple products.', 'yith-woocommerce-product-add-ons' ),
                'force_select' => __( 'Force user to select products of this block', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] Add-on editor > Options configuration option (description)
                'force_select_description' => __( 'Enable to force users to select products to proceed with the purchase.', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] Add-on editor > Options configuration option
                'proceed_purchase' => __( 'To proceed with the purchase, users have to select', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] Add-on editor > Options configuration option (description)
                'proceed_purchase_description' => __( 'Set how many products need to be selected in order to add a product to cart.', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] Add-on editor > Options configuration option
                'can_select_max' => __( 'Users can select a max of', 'yith-woocommerce-product-add-ons' ),
                'can_select_max_description' => sprintf(
                // translators: %s is a line break. [ADMIN] Add-on editor > Options configuration option (description)
                    esc_html__( 'Optional: set the max number of products selectable by users in this block. %s Leave empty if users can select all products without any limits.', 'yith-woocommerce-product-add-ons' ),
                    '<br>'
                ),

                // translators: [ADMIN] text description on several "Options configuration" options, depending on add-on type
                'options' => __( 'products' , 'yith-woocommerce-product-add-ons' ),
            ),
            'text' => array(
                // translators: [ADMIN] Add-on editor > Options configuration option
                'single_option' => __( 'Single - Users can fill ONE of the available fields', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] Add-on editor > Options configuration option
                'multiple_options' => __( 'Multiple - Users can fill MULTIPLE fields', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] Add-on editor > Options configuration option
                'selection_description' => __( 'Choose if users can fill one or multiple fields.', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] Add-on editor > Options configuration option
                'first_options' => __( 'Set the first fields selected as free', 'yith-woocommerce-product-add-ons' ),
                'first_options_description' => sprintf(
                // translators: %1$s and %2$s are line breaks.
                    esc_html__( 'Enable to set a specific number of fields as free. %1$s For example, the first three "pizza toppings" are free, included in the product price. %2$s Users will pay from the fourth topping on.', 'yith-woocommerce-product-add-ons' ),
                    '<br>',
                    '<br>'
                ),
                // translators: [ADMIN] Add-on editor > Options configuration option
                'select_free' => __( 'Users can fill for free', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] Add-on editor > Options configuration option (description)
                'can_select_for_free' => __( 'Set how many fields users can fill for free.', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] Add-on editor > Options configuration option
                'force_select' => __( 'Force user to fill fields of this block', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] Add-on editor > Options configuration option (description)
                'force_select_description' => __( 'Enable to force users to fill fields to proceed with the purchase.', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] Add-on editor > Options configuration option
                'proceed_purchase' => __( 'To proceed with the purchase, users have to fill', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] Add-on editor > Options configuration option (description)
                'proceed_purchase_description' => __( 'Set how many fields need to be filled in order to add a product to cart.', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] Add-on editor > Options configuration option
                'can_select_max' => __( 'Users can fill a max of', 'yith-woocommerce-product-add-ons' ),
                'can_select_max_description' => sprintf(
                // translators: %s is a line break. [ADMIN] Add-on editor > Options configuration option (description)
                    esc_html__( 'Optional: set the max number of fields fillable by users in this block. %s Leave empty if users can fill all fields without any limits.', 'yith-woocommerce-product-add-ons' ),
                    '<br>'
                ),

                // translators: [ADMIN] text description on several "Options configuration" options, depending on add-on type
                'options' => __( 'fields' , 'yith-woocommerce-product-add-ons' ),
            ),
            'textarea' => array(
                // translators: [ADMIN] Add-on editor > Options configuration option
                'single_option' => __( 'Single - Users can fill ONE of the available text areas', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] Add-on editor > Options configuration option
                'multiple_options' => __( 'Multiple - Users can fill MULTIPLE text areas', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] Add-on editor > Options configuration option
                'selection_description' => __( 'Choose if users can fill one or multiple text areas.', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] Add-on editor > Options configuration option
                'first_options' =>  __( 'Set the first text areas selected as free', 'yith-woocommerce-product-add-ons' ),
                'first_options_description' => sprintf(
                // translators: %1$s and %2$s are line breaks.
                    esc_html__( 'Enable to set a specific number of text areas as free. %1$s For example, the first three "pizza toppings" are free, included in the product price. %2$s Users will pay from the fourth topping on.', 'yith-woocommerce-product-add-ons' ),
                    '<br>',
                    '<br>'
                ),
                // translators: [ADMIN] Add-on editor > Options configuration option
                'select_free' => __( 'Users can fill for free', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] Add-on editor > Options configuration option (description)
                'can_select_for_free' => __( 'Set how many text areas users can fill for free.', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] Add-on editor > Options configuration option
                'force_select' => __( 'Force user to fill text areas of this block', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] Add-on editor > Options configuration option (description)
                'force_select_description' => __( 'Enable to force users to fill text areas to proceed with the purchase.', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] Add-on editor > Options configuration option
                'proceed_purchase' => __( 'To proceed with the purchase, users have to fill', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] Add-on editor > Options configuration option (description)
                'proceed_purchase_description' => __( 'Set how many text areas need to be filled in order to add a product to cart.', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] Add-on editor > Options configuration option
                'can_select_max' => __( 'Users can fill a max of', 'yith-woocommerce-product-add-ons' ),
                'can_select_max_description' => sprintf(
                // translators: %s is a line break. [ADMIN] Add-on editor > Options configuration option (description)
                    esc_html__( 'Optional: set the max number of text areas fillable by users in this block. %s Leave empty if users can fill all text areas without any limits.', 'yith-woocommerce-product-add-ons' ),
                    '<br>'
                ),

                // translators: [ADMIN] text description on several "Options configuration" options, depending on add-on type
                'options' => __( 'text areas' , 'yith-woocommerce-product-add-ons' ),
            ),
            'file' => array(
                // translators: [ADMIN] Add-on editor > Options configuration option
                'single_option' => __( 'Single - Users can make use of ONE of the available uploaders', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] Add-on editor > Options configuration option
                'multiple_options' => __( 'Multiple - Users can make use of MULTIPLE uploaders', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] Add-on editor > Options configuration option
                'selection_description' => __( 'Choose if users can make use of one or multiple uploaders.', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] Add-on editor > Options configuration option
                'force_select' => __( 'Force user to make use of uploaders of this block', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] Add-on editor > Options configuration option (description)
                'force_select_description' => __( 'Enable to force users to make use of uploaders to proceed with the purchase.', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] Add-on editor > Options configuration option
                'proceed_purchase' => __( 'To proceed with the purchase, users have to make use of', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] Add-on editor > Options configuration option (description)
                'proceed_purchase_description' => __( 'Set how many uploaders need to be make use of in order to add a product to cart.', 'yith-woocommerce-product-add-ons' ),

                // translators: [ADMIN] text description on several "Options configuration" options, depending on add-on type
                'options' => __( 'uploaders' , 'yith-woocommerce-product-add-ons' ),
            ),
            'date' => array(
                // translators: [ADMIN] Add-on editor > Options configuration option
                'add_new' => _x( 'Add a new', 'Add-on editor panel > Add a new + add-on name (fem)', 'yith-woocommerce-product-add-ons' ),
            ),
            'label' => array(
                // translators: [ADMIN] Add-on editor > Options configuration option
                'add_new' => _x( 'Add a new', 'Add-on editor panel > Add a new + add-on name (fem)', 'yith-woocommerce-product-add-ons' ),
            ),
            'default' => array(
                // translators: [ADMIN] Add-on editor > Options configuration option
                'single_option' => __( 'Single - Users can select ONE of the available options', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] Add-on editor > Options configuration option
                'multiple_options' => __( 'Multiple - Users can select MULTIPLE options', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] Add-on editor > Options configuration option
                'selection_description' => __( 'Choose if users can select one or multiple options.', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] Add-on editor > Options configuration option
                'first_options' =>  __( 'Set the first options selected as free', 'yith-woocommerce-product-add-ons' ),
                'first_options_description' => sprintf(
                // translators: %1$s and %2$s are line breaks.
                    esc_html__( 'Enable to set a specific number of options as free. %1$s For example, the first three "pizza toppings" are free, included in the product price. %2$s Users will pay from the fourth topping on.', 'yith-woocommerce-product-add-ons' ),
                    '<br>',
                    '<br>'
                ),
                // translators: [ADMIN] Add-on editor > Options configuration option
                'select_free' => __( 'Users can select for free', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] Add-on editor > Options configuration option (description)
                'can_select_for_free' => __( 'Set how many options users can select for free.', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] Add-on editor > Options configuration option
                'force_select' => __( 'Force user to select options of this block', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] Add-on editor > Options configuration option (description)
                'force_select_description' => __( 'Enable to force users to select options to proceed with the purchase.', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] Add-on editor > Options configuration option
                'proceed_purchase' => __( 'To proceed with the purchase, users have to select', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] Add-on editor > Options configuration option (description)
                'proceed_purchase_description' => __( 'Set how many options need to be selected in order to add a product to cart.', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] Add-on editor > Options configuration option
                'can_select_max' => __( 'Users can select a max of', 'yith-woocommerce-product-add-ons' ),
                'can_select_max_description' => sprintf(
                // translators: %s is a line break. [ADMIN] Add-on editor > Options configuration option (description)
                    esc_html__( 'Optional: set the max number of options selectable by users in this block. %s Leave empty if users can select all options without any limits.', 'yith-woocommerce-product-add-ons' ),
                    '<br>'
                ),
                // translators: [ADMIN] Add-on editor > Options configuration option
                'min_max_all' => __( 'Set a min/max value among all options', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] Add-on editor > Options configuration option (description)
                'min_max_all_description' => __( 'Enable to force users to enter values that are within a specific range when all options are added together.', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] Add-on editor > Options configuration option
                'min_max_number' => __( 'Sum of options between', 'yith-woocommerce-product-add-ons' ),

                // translators: [ADMIN] Part of text on block editor, depending on add-on type
                'options' => __( 'options', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] Add-on editor > Options configuration option
                'add_new' => _x( 'Add a new', 'Add-on editor panel > Add a new + add-on name (masc)', 'yith-woocommerce-product-add-ons' ),
            )
        );

        return $str_values[$addon_type][$key] ?? $str_values['default'][$key];
    }
}

if ( ! function_exists( 'yith_wapo_create_time_range' ) ) {

    /**
     * Create a time range
     *
     * @param mixed $start start time, e.g., 7:30am or 7:30
     * @param mixed $end   end time, e.g., 8:30pm or 20:30
     * @param string $interval_type time interval type => hour, minutes, seconds.
     * @param string $interval time intervals, 1 hour, 1 mins, 1 secs, etc.
     * @param string $format time format, e.g., 12 or 24
     */
    function yith_wapo_create_time_range( $start, $end, $interval_type = 'hours', $interval = '30 mins', $format = '12' ) {

        $startTime        = strtotime( $start );
        $endTime          = strtotime( $end );
        $returnTimeFormat = ( $format == '12' ) ? 'g:i a' : 'G:i';
        if ( 'seconds' === $interval_type ) {
            $returnTimeFormat = ( $format == '12' ) ? 'g:i:s a' : 'G:i:s';
        }

        $current   = time();
        $addTime   = strtotime('+' . $interval, $current );
        $diff      = $addTime - $current;

        $times = array();

        while ( $startTime + $diff <= $endTime ) {
            $times[] = date( $returnTimeFormat, $startTime );
            $startTime += $diff;
        }
        $times[] = date( $returnTimeFormat, $startTime );

        return $times;
    }
}

if ( ! function_exists( 'yith_wapo_array_insert_after' ) ) {
    /**
     * Insert a value or key/value pair after a specific key in an array.  If key doesn't exist, value is appended
     * to the end of the array.
     *
     * @param array $array
     * @param string $key
     * @param array $new
     *
     * @return array
     */
    function yith_wapo_array_insert_after( array $array, $key, array $new ) {
        $keys = array_keys( $array );
        $index = array_search( $key, $keys );
        $pos = false === $index ? count( $array ) : $index + 1;

        return array_merge( array_slice( $array, 0, $pos ), $new, array_slice( $array, $pos ) );
    }
}

if ( ! function_exists( 'yith_wapo_instance_class' ) ) {
    function yith_wapo_instance_class( $class, $args = array() ) {
        $class_name = $class . ( class_exists( $class . '_Premium' ) ? '_Premium' : '' );

        return new $class_name( $args );
    }
}
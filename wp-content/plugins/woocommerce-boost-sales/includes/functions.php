<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Function include all files in folder
 *
 * @param $path   Directory address
 * @param $ext    array file extension what will include
 * @param $prefix string Class prefix
 */

if ( ! function_exists( 'vi_include_folder' ) ) {
	function vi_include_folder( $path, $prefix = '', $ext = array( 'php' ) ) {

		/*Include all files in payment folder*/
		if ( ! is_array( $ext ) ) {
			$ext = explode( ',', $ext );
			$ext = array_map( 'trim', $ext );
		}
		$sfiles = scandir( $path );
		foreach ( $sfiles as $sfile ) {
			if ( $sfile != '.' && $sfile != '..' ) {
				if ( is_file( $path . "/" . $sfile ) ) {
					$ext_file  = pathinfo( $path . "/" . $sfile );
					$file_name = $ext_file['filename'];
					if ( $ext_file['extension'] ) {
						if ( in_array( $ext_file['extension'], $ext ) ) {
							$class = preg_replace( '/\W/i', '_', $prefix . ucfirst( $file_name ) );

							if ( ! class_exists( $class ) ) {
								require_once $path . $sfile;
								if ( class_exists( $class ) ) {
									new $class;
								}
							}
						}
					}
				}
			}
		}
	}
}

if ( ! function_exists( 'woocommerce_boost_sales_prefix' ) ) {
	function woocommerce_boost_sales_prefix() {
		$prefix = get_option( '_woocommerce_boost_sales_prefix', date( "Ymd" ) );

		return $prefix . '_products_' . date( "Ymd" );
	}
}

if ( ! function_exists( 'wbs_set_prop' ) ) {
	/**
	 *
	 */
	function wbs_set_prop( $object, $arg1, $arg2 = false ) {

		if ( ! is_array( $arg1 ) ) {
			$arg1 = array(
				$arg1 => $arg2
			);
		}

		$prop_map   = wbs_return_new_attribute_map();
		$is_wc_data = $object instanceof WC_Data;

		foreach ( $arg1 as $key => $value ) {
			if ( $is_wc_data ) {
				$key = ( array_key_exists( $key, $prop_map ) ) ? $prop_map[$key] : $key;

				if ( ( $setter = "set{$key}" ) && method_exists( $object, $setter ) ) {
					$object->$setter( $value );
				} elseif ( ( $setter = "set_{$key}" ) && method_exists( $object, $setter ) ) {
					$object->$setter( $value );
				} else {
					$object->update_meta_data( $key, $value );
				}
			} else {
				$key = ( in_array( $key, $prop_map ) ) ? array_search( $key, $prop_map ) : $key;
				( strpos( $key, '_' ) === 0 ) && $key = substr( $key, 1 );

				if ( wbs_wc_check_post_columns( $key ) ) {
					$object->post->$key = $value;
				} else {
					$object->$key = $value;
				}
			}
		}
	}
}

if ( ! function_exists( 'wbs_return_new_attribute_map' ) ) {
	function wbs_return_new_attribute_map() {
		return array(
			'post_parent'                => 'parent_id',
			'post_title'                 => 'name',
			'post_status'                => 'status',
			'post_content'               => 'description',
			'post_excerpt'               => 'short_description',
			/* Orders */
			'paid_date'                  => 'date_paid',
			'_paid_date'                 => '_date_paid',
			'completed_date'             => 'date_completed',
			'_completed_date'            => '_date_completed',
			'_order_date'                => '_date_created',
			'order_date'                 => 'date_created',
			'order_total'                => 'total',
			'customer_user'              => 'customer_id',
			'_customer_user'             => 'customer_id',
			/* Products */
			'visibility'                 => 'catalog_visibility',
			'_visibility'                => '_catalog_visibility',
			'sale_price_dates_from'      => 'date_on_sale_from',
			'_sale_price_dates_from'     => '_date_on_sale_from',
			'sale_price_dates_to'        => 'date_on_sale_to',
			'_sale_price_dates_to'       => '_date_on_sale_to',
			'product_attributes'         => 'attributes',
			'_product_attributes'        => '_attributes',
			/*Coupons*/
			'coupon_amount'              => 'amount',
			'exclude_product_ids'        => 'excluded_product_ids',
			'exclude_product_categories' => 'excluded_product_categories',
			'customer_email'             => 'email_restrictions',
			'expiry_date'                => 'date_expires',
		);
	}
}

if ( ! function_exists( 'wbs_wc_check_post_columns' ) ) {
	/**
	 *
	 */
	function wbs_wc_check_post_columns( $key ) {
		$columns = array(
			'post_author',
			'post_date',
			'post_date_gmt',
			'post_content',
			'post_title',
			'post_excerpt',
			'post_status',
			'comment_status',
			'ping_status',
			'post_password',
			'post_name',
			'to_ping',
			'pinged',
			'post_modified',
			'post_modified_gmt',
			'post_content_filtered',
			'post_parent',
			'guid',
			'menu_order',
			'post_type',
			'post_mime_type',
			'comment_count',
		);

		return in_array( $key, $columns );
	}
}
if ( ! function_exists( 'wbs_get_template' ) ) {

	/**
	 * Get other templates (e.g. product attributes) passing attributes and including the file.
	 *
	 * @access public
	 *
	 * @param string $template_name
	 * @param array  $args          (default: array())
	 * @param string $template_path (default: '')
	 * @param string $default_path  (default: '')
	 */
	function wbs_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
		if ( ! empty( $args ) && is_array( $args ) ) {
			extract( $args );
		}

		$located = $default_path . $template_name;

		if ( ! file_exists( $located ) ) {
			wc_doing_it_wrong( __FUNCTION__, sprintf( __( '%s does not exist.', 'woocommerce-boost-sales' ), '<code>' . $located . '</code>' ), '2.1' );

			return;
		}

		// Allow 3rd party plugin filter template file from their plugin.
		$located = $located;


		include( $located );

	}
}
if(!function_exists('wbs_wc_dropdown_variation_attribute_options')){
	function wbs_wc_dropdown_variation_attribute_options($args = array()){
		$args = wp_parse_args( apply_filters( 'woocommerce_dropdown_variation_attribute_options_args', $args ), array(
			'options'          => false,
			'attribute'        => false,
			'product'          => false,
			'selected'         => false,
			'name'             => '',
			'id'               => '',
			'class'            => '',
			'show_option_none' => __( 'Choose an option', 'woocommerce' ),
		) );

		// Get selected value.
		if ( false === $args['selected'] && $args['attribute'] && $args['product'] instanceof WC_Product ) {
			$selected_key     = 'attribute_' . sanitize_title( $args['attribute'] );
			$args['selected'] = isset( $_REQUEST[ $selected_key ] ) ? wc_clean( wp_unslash( $_REQUEST[ $selected_key ] ) ) : $args['product']->get_variation_default_attribute( $args['attribute'] ); // WPCS: input var ok, CSRF ok, sanitization ok.
		}

		$options               = $args['options'];
		$product               = $args['product'];
		$attribute             = $args['attribute'];
		$name                  = $args['name'] ? $args['name'] : 'attribute_' . sanitize_title( $attribute );
		$id                    = $args['id'] ? $args['id'] : sanitize_title( $attribute );
		$class                 = $args['class'];
		$show_option_none      = (bool) $args['show_option_none'];
		$show_option_none_text = $args['show_option_none'] ? $args['show_option_none'] : __( 'Choose an option', 'woocommerce' ); // We'll do our best to hide the placeholder, but we'll need to show something when resetting options.

		if ( empty( $options ) && ! empty( $product ) && ! empty( $attribute ) ) {
			$attributes = $product->get_variation_attributes();
			$options    = $attributes[ $attribute ];
		}

		$html  = '<select id="' . esc_attr( $id ) . '" class="' . esc_attr( $class ) . '" name="' . esc_attr( $name ) . '" data-attribute_name="attribute_' . esc_attr( sanitize_title( $attribute ) ) . '" data-show_option_none="' . ( $show_option_none ? 'yes' : 'no' ) . '">';
		$html .= '<option value="">' . esc_html( $show_option_none_text ) . '</option>';

		if ( ! empty( $options ) ) {
			if ( $product && taxonomy_exists( $attribute ) ) {
				// Get terms if this is a taxonomy - ordered. We need the names too.
				$terms = wc_get_product_terms( $product->get_id(), $attribute, array(
					'fields' => 'all',
				) );

				foreach ( $terms as $term ) {
					if ( in_array( $term->slug, $options, true ) ) {
						$html .= '<option value="' . esc_attr( $term->slug ) . '" ' . selected( sanitize_title( $args['selected'] ), $term->slug, false ) . '>' . esc_html( apply_filters( 'woocommerce_variation_option_name', $term->name ) ) . '</option>';
					}
				}
			} else {
				foreach ( $options as $option ) {
					// This handles < 2.4.0 bw compatibility where text attributes were not sanitized.
					$selected = sanitize_title( $args['selected'] ) === $args['selected'] ? selected( $args['selected'], sanitize_title( $option ), false ) : selected( $args['selected'], $option, false );
					$html    .= '<option value="' . esc_attr( $option ) . '" ' . $selected . '>' . esc_html( apply_filters( 'woocommerce_variation_option_name', $option ) ) . '</option>';
				}
			}
		}

		$html .= '</select>';

		echo apply_filters( 'wbs_woocommerce_dropdown_variation_attribute_options_html', $html, $args ); // WPCS: XSS ok.
	}
}
if ( ! function_exists( 'wbs_woocommerce_quantity_input' ) ) {

	/**
	 * Output the quantity input for add to cart forms.
	 *
	 * @param  array           $args Args for the input.
	 * @param  WC_Product|null $product Product.
	 * @param  boolean         $echo Whether to return or echo|string.
	 *
	 * @return string
	 */
	function wbs_woocommerce_quantity_input( $args = array(), $product = null, $echo = true ) {
		if ( is_null( $product ) ) {
			$product = $GLOBALS['product'];
		}

		$defaults = array(
			'input_id'     => uniqid( 'quantity_' ),
			'input_name'   => 'quantity',
			'input_value'  => '1',
			'classes'      => apply_filters( 'woocommerce_quantity_input_classes', array( 'input-text', 'qty', 'text' ), $product ),
			'max_value'    => apply_filters( 'woocommerce_quantity_input_max', -1, $product ),
			'min_value'    => apply_filters( 'woocommerce_quantity_input_min', 0, $product ),
			'step'         => apply_filters( 'woocommerce_quantity_input_step', 1, $product ),
			'pattern'      => apply_filters( 'woocommerce_quantity_input_pattern', has_filter( 'woocommerce_stock_amount', 'intval' ) ? '[0-9]*' : '' ),
			'inputmode'    => apply_filters( 'woocommerce_quantity_input_inputmode', has_filter( 'woocommerce_stock_amount', 'intval' ) ? 'numeric' : '' ),
			'product_name' => $product ? $product->get_title() : '',
		);

		$args = apply_filters( 'woocommerce_quantity_input_args', wp_parse_args( $args, $defaults ), $product );

		// Apply sanity to min/max args - min cannot be lower than 0.
		$args['min_value'] = max( $args['min_value'], 0 );
		$args['max_value'] = 0 < $args['max_value'] ? $args['max_value'] : '';

		// Max cannot be lower than min if defined.
		if ( '' !== $args['max_value'] && $args['max_value'] < $args['min_value'] ) {
			$args['max_value'] = $args['min_value'];
		}

		ob_start();
		wbs_get_template( 'single-product/add-to-cart/quantity-input.php', $args, '', VI_WBOOSTSALES_TEMPLATES );

		if ( $echo ) {
			echo ob_get_clean(); // WPCS: XSS ok.
		} else {
			return ob_get_clean();
		}
	}
}
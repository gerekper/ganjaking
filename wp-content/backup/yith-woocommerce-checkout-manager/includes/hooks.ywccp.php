<?php
/**
 * Plugins Hooks
 *
 * @author  YITH
 * @package YITH WooCommerce Checkout Manager
 * @version 1.0.0
 */

if ( ! defined( 'YWCCP' ) ) {
	exit;
} // Exit if accessed directly.

// Billing fields.
add_filter( 'woocommerce_billing_fields', 'ywccp_load_custom_billing_fields', 50, 1 );
add_filter( 'woocommerce_admin_billing_fields', 'ywccp_load_custom_billing_fields_admin', 50, 1 );
// Shipping fields.
add_filter( 'woocommerce_shipping_fields', 'ywccp_load_custom_shipping_fields', 50, 1 );
add_filter( 'woocommerce_admin_shipping_fields', 'ywccp_load_custom_shipping_fields_admin', 50, 1 );

// Order fields.
add_filter( 'woocommerce_get_order_address', 'ywccp_filter_get_order_address', 10, 3 );

// Additional fields.
add_filter( 'woocommerce_checkout_fields', 'ywccp_add_additional_fields', 100, 1 );
// Add additional meta to order.
add_action( 'woocommerce_checkout_create_order', 'ywccp_add_additional_fields_meta', 10, 2 );

add_filter( 'woocommerce_localisation_address_formats', 'ywccp_add_address_formats', 100, 1 );
add_filter( 'woocommerce_order_formatted_billing_address', 'ywccp_update_formatted_billing_address_order', 10, 2 );
add_filter( 'woocommerce_order_formatted_shipping_address', 'ywccp_update_formatted_shipping_address_order', 10, 2 );
add_filter( 'woocommerce_formatted_address_replacements', 'ywccp_update_address_replacement', 10, 2 );
add_action( 'woocommerce_email_after_order_table', 'ywccp_email_additional_fields_list', 10, 4 );
add_filter( 'woocommerce_address_to_edit', 'ywccp_hide_fields_in_order_detail_page', 10, 2 );

// Filter customer billing and shipping address.
add_filter( 'woocommerce_customer_get_billing', 'ywccp_customer_get_billing', 10, 2 );
add_filter( 'woocommerce_customer_get_shipping', 'ywccp_customer_get_shipping', 10, 2 );

// Compatibility with WooCommerce Customer Order CSV Export.
add_filter( 'wc_customer_order_csv_export_order_headers', 'ywccp_customer_order_csv_export_order_headers', 1, 2 );
add_filter( 'wc_customer_order_csv_export_order_row', 'ywccp_customer_order_csv_export_order_row', 1, 3 );

// Filter strings for MultiLingual Plugin.
add_filter( 'woocommerce_checkout_fields', 'ywccp_filter_multilingual_strings', 999, 1 );
add_filter( 'woocommerce_shipping_fields', 'ywccp_filter_multilingual_strings_for_multiple_shipping_addresses_popup', 999, 1 );

// Manage conditions.
add_filter( 'woocommerce_checkout_fields', 'ywccp_check_conditions_for_products_in_cart', 500 );
add_action( 'woocommerce_after_checkout_validation', 'ywccp_validate_conditions_field_before_to_place_the_order', 100, 2 );

if ( ! function_exists( 'ywccp_load_custom_billing_fields' ) ) {
	/**
	 * Load customized billing fields function.
	 *
	 * @since  1.0.0
	 * @author Francesco Licandro
	 * @param array $old Array of default fields.
	 * @return array
	 */
	function ywccp_load_custom_billing_fields( $old ) {
		$new = apply_filters( 'ywccp_custom_billing_fields', ywccp_get_checkout_fields( 'billing' ), $old );

		if ( empty( $new ) ) {
			return $old;
		}

		foreach ( $new as $key => &$value ) {
            if ( ! empty( $value['required'] ) && isset( $old[ $key ] ) && empty( $old[ $key ]['required'] ) ) {
				$value['required'] = false;
			}
			if ( isset( $value['enabled'] ) && ! $value['enabled'] ) {
				unset( $new[ $key ] );
			}
		}

		return $new;
	}
}

if ( ! function_exists( 'ywccp_load_custom_billing_fields_admin' ) ) {
	/**
	 * Load customized billing fields for admin section.
	 *
	 * @since  1.0.0
	 * @author Francesco Licandro
	 * @param array $old Array of default fields.
	 * @return array
	 */
	function ywccp_load_custom_billing_fields_admin( $old ) {

		$fields = apply_filters( 'ywccp_custom_billing_fields', ywccp_get_checkout_fields( 'billing' ), $old );

		if ( ! is_array( $fields ) || empty( $fields ) ) {
			return $old;
		}

		return ywccp_build_fields_array_admin( $fields, $old, 'billing_' );
	}
}

if ( ! function_exists( 'ywccp_load_custom_shipping_fields' ) ) {
	/**
	 * Load customized shipping fields function.
	 *
	 * @since  1.0.0
	 * @author Francesco Licandro
	 * @param array $old Array of default fields.
	 * @return array
	 */
	function ywccp_load_custom_shipping_fields( $old ) {
		$new = apply_filters( 'ywccp_custom_shipping_fields', ywccp_get_checkout_fields( 'shipping' ), $old );

		if ( empty( $new ) ) {
			return $old;
		}

		foreach ( $new as $key => &$value ) {
			if ( isset( $value['enabled'] ) && ! $value['enabled'] ) {
				unset( $new[ $key ] );
			}
		}

		return $new;
	}
}

if ( ! function_exists( 'ywccp_load_custom_shipping_fields_admin' ) ) {
	/**
	 * Load customized shipping fields for admin section.
	 *
	 * @since  1.0.0
	 * @author Francesco Licandro
	 * @param array $old Array of default fields.
	 * @return array
	 */
	function ywccp_load_custom_shipping_fields_admin( $old ) {

		$fields = apply_filters( 'ywccp_custom_shipping_fields', ywccp_get_checkout_fields( 'shipping' ), $old );

		if ( ! is_array( $fields ) || empty( $fields ) ) {
			return $old;
		}

		return ywccp_build_fields_array_admin( $fields, $old, 'shipping_' );
	}
}

if ( ! function_exists( 'ywccp_add_address_formats' ) ) {
	/**
	 * Update address formats for all formatted address and all nations
	 *
	 * @since  1.0.0
	 * @author Francesco Licandro
	 * @param $formats array Array of available formats, indexed for nation code
	 * @return array Filtered array of available formats
	 */
	function ywccp_add_address_formats( $formats ) {

		$overwrite       = get_option( 'ywccp-override-formatted-addresses', 'no' ) === 'yes';
		$new_replacement = ywccp_get_fields_localisation_address_formats( 'all' );

		foreach ( $formats as $country => &$value ) {
			$overwrite ? $value = $new_replacement : $value .= $new_replacement;
		}

		return $formats;
	}
}

if ( ! function_exists( 'ywccp_update_formatted_billing_address_order' ) ) {
	/**
	 * Adds field to formatted address for order's admin view
	 *
	 * @access public
	 *
	 * @since  1.0.0
	 * @param array    $billing_fields Array of fields to be used in formatted address.
	 * @param WC_Order $order          Order object.
	 * @return array
	 */
	function ywccp_update_formatted_billing_address_order( $billing_fields, $order ) {
		// Get address replacement!
		$replacement = ywccp_get_address_replacement( 'billing', $order );
		return array_merge( $billing_fields, $replacement );
	}
}

if ( ! function_exists( 'ywccp_update_formatted_shipping_address_order' ) ) {
	/**
	 * Adds field to formatted address for order's admin view
	 *
	 * @access public
	 *
	 * @since  1.0.0
	 * @param array    $shipping_fields Array of fields to be used in formatted address.
	 * @param WC_Order $order           Order object.
	 * @return array
	 */
	function ywccp_update_formatted_shipping_address_order( $shipping_fields, $order ) {
		// Get address replacement!
		$replacement = ywccp_get_address_replacement( 'shipping', $order );
		return is_array( $shipping_fields ) ? array_merge( $shipping_fields, $replacement ) : null;
	}
}

if ( ! function_exists( 'ywccp_hide_fields_in_order_detail_page' ) ) {

	function ywccp_hide_fields_in_order_detail_page( $fields, $load_address ) {

		if ( empty( $fields ) ) {
			return array();
		} elseif ( is_order_received_page() || is_account_page() ) {
			$where_im = 'show_in_order';
		} else {
			$where_im = 'show_in_email';
		}

		if ( $where_im ) {
			foreach ( $fields as $key => $value ) {
				if ( isset( $value[ $where_im ] ) && ! $value[ $where_im ] ) {
					unset( $fields[ $key ] );
				};
			}
		}
		return $fields;
	}
}

if ( ! function_exists( 'ywccp_customer_get_billing' ) ) {
	/**
	 * Filter customer billing address
	 *
	 * @since  1.1.0
	 * @author Francesco Licandro
	 * @param array       $value
	 * @param WC_Customer $customer
	 * @return array
	 */
	function ywccp_customer_get_billing( $value, $customer ) {
		return ywccp_customer_get_address( $value, $customer );
	}
}

if ( ! function_exists( 'ywccp_customer_get_shipping' ) ) {
	/**
	 * Filter customer shipping address
	 *
	 * @since  1.1.0
	 * @author Francesco Licandro
	 * @param array       $value
	 * @param WC_Customer $customer
	 * @return array
	 */
	function ywccp_customer_get_shipping( $value, $customer ) {
		return ywccp_customer_get_address( $value, $customer, 'shipping' );
	}
}

if ( ! function_exists( 'ywccp_update_address_replacement' ) ) {
	/**
	 * Update address replacement for all site address formats
	 *
	 * @access public
	 *
	 * @since  1.0.0
	 * @param array $replacements Array of available replacements.
	 * @param array $args         Array of arguments to use in replacements.
	 * @return array Filtered array of replacements.
	 */
	function ywccp_update_address_replacement( $replacements, $args ) {

		$fields = ywccp_get_fields_localisation_address_formats( 'all', true );

		if ( empty( $fields ) ) {
			return $replacements;
		}

		foreach ( (array) $fields as $value ) {
			if ( isset( $replacements[ '{' . $value . '}' ] ) ) {
				continue;
			}
			$replacements[ '{' . $value . '}' ] = isset( $args[ $value ] ) ? $args[ $value ] : '';
		}

		return $replacements;
	}
}

if ( ! function_exists( 'ywccp_add_additional_fields' ) ) {
	/**
	 * Add additional fields to checkout form
	 *
	 * @since  1.0.0
	 * @author Francesco Licandro
	 * @param $fields
	 * @return array
	 */
	function ywccp_add_additional_fields( $fields ) {

		$fields_new = ywccp_get_checkout_fields( 'additional' );

		if ( empty( $fields_new ) || ! isset( $fields['order'] ) ) {
			return $fields;
		}

		foreach ( $fields_new as $key => &$value ) {
			if ( isset( $value['enabled'] ) && ! $value['enabled'] ) {
				unset( $fields_new[ $key ] );
			}
		}

		$fields['order'] = $fields_new;

		return $fields;
	}
}

if ( ! function_exists( 'ywccp_add_additional_fields_meta' ) ) {
	/**
	 * Add order meta for additional fields
	 *
	 * @since  1.0.0
	 * @author Francesco Licandro
	 * @param WC_Order|integer $order
	 * @param array            $posted
	 */
	function ywccp_add_additional_fields_meta( $order, $posted ) {
		// Get additional fields key.
		$fields       = ywccp_get_checkout_fields( 'additional' );
		$default_keys = ywccp_get_default_fields_key( 'additional' );
		if ( ! $order instanceof WC_Order ) {
			$order = wc_get_order( $order );
		}

		foreach ( $fields as $key => $field ) {
			if ( in_array( $key, $default_keys ) || empty( $posted[ $key ] ) ) {
				continue;
			}

			yit_set_prop( $order, $key, $posted[ $key ] );
		}
	}
}

if ( ! function_exists( 'ywccp_email_additional_fields_list' ) ) {
	/**
	 * Add the additional fields list on order email
	 *
	 * @since  1.0.0
	 * @author Francesco Licandro
	 * @param WC_Order $order
	 * @param boolean  $sent_to_admin
	 * @param string   $plain_text
	 * @param          $email
	 */
	function ywccp_email_additional_fields_list( $order, $sent_to_admin, $plain_text = '', $email = false ) {

		$fields = ywccp_get_custom_fields( 'additional' );

		$content = array();
		foreach ( $fields as $key => $field ) {
			// Check if value exists for order.
			$value = yit_get_prop( $order, $key, true );
			// Get translated field if needed.
			$field = ywccp_multilingual_single_field( $key, $field );

			if ( $value && $field['show_in_email'] ) {
				$content[ $key ] = array(
					'label' => $field['label'],
					'value' => isset( $field['options'][ $value ] ) ? $field['options'][ $value ] : $value,
				);
			}
		}

		if ( empty( $content ) ) {
			return;
		}

		if ( $plain_text ) {
			wc_get_template( 'ywccp-additional-fields-list.php', array( 'fields' => $content ), '', YWCCP_TEMPLATE_PATH . '/email/plain/' );
		} else {
			wc_get_template( 'ywccp-additional-fields-list.php', array( 'fields' => $content ), '', YWCCP_TEMPLATE_PATH . '/email/' );
		}
	}
}

if ( ! function_exists( 'ywccp_customer_order_csv_export_order_headers' ) ) {
	/**
	 * Add headers for customer order csv export plugins
	 *
	 * @since  1.0.3
	 * @author Francesco Licandro
	 * @param array                                  $headers
	 * @param WC_Customer_Order_CSV_Export_Generator $class
	 * @return array
	 */
	function ywccp_customer_order_csv_export_order_headers( $headers, $class ) {

		$custom_fields = ywccp_get_all_custom_fields();
		$csv_format    = get_option( 'wc_customer_order_csv_export_order_format' );
		$use_label     = ! in_array( $csv_format, array( 'legacy_import', 'import', 'default', 'default_one_row_per_item' ) );
		$new_headers   = array();

		foreach ( $headers as $key => $value ) {

			if ( 'billing_country' === $key ) {
				foreach ( $custom_fields['billing'] as $key_custom => $value_custom ) {
					$new_headers[ $key_custom ] = ( $use_label && ! empty( $value_custom['label'] ) ) ? 'Billing ' . $value_custom['label'] : $key_custom;
				}
			} elseif ( 'shipping_country' === $key  ) {
				foreach ( $custom_fields['shipping'] as $key_custom => $value_custom ) {
					$new_headers[ $key_custom ] = ( $use_label && ! empty( $value_custom['label'] ) ) ? 'Shipping ' . $value_custom['label'] : $key_custom;
				}
			} elseif ( 'customer_note' === $key  ) {
				foreach ( $custom_fields['additional'] as $key_custom => $value_custom ) {
					$new_headers[ $key_custom ] = ( $use_label && ! empty( $value_custom['label'] ) ) ? $value_custom['label'] : $key_custom;
				}
			}

			$new_headers[ $key ] = $value;
		}

		return $new_headers;
	}
}

if ( ! function_exists( 'ywccp_customer_order_csv_export_order_row' ) ) {
	/**
	 * Modify order row for CSV export
	 *
	 * @since  1.0.3
	 * @author Francesco Licandro
	 * @param array                                  $order_data
	 * @param WC_Order                               $order
	 * @param WC_Customer_Order_CSV_Export_Generator $class
	 * @return array
	 */
	function ywccp_customer_order_csv_export_order_row( $order_data, $order, $class ) {
		$custom_fields = ywccp_get_all_custom_fields();

		foreach ( $custom_fields as $section => $fields ) {
			foreach ( $fields as $key => $options ) {
				$meta_key           = ( 'additional' === $section ) ? $key : '_' . $key;
				$order_data[ $key ] = yit_get_prop( $order, $meta_key, true );
			}
		}

		return $order_data;
	}
}

if ( ! function_exists( 'ywccp_filter_wpml_strings' ) ) {
	/**
	 * Filter strings for WPML
	 *
	 * @since  1.0.0
	 * @author Francesco Licandro
	 * @param $fields
	 * @return array
	 */
	function ywccp_filter_wpml_strings( $fields ) {
		return ywccp_filter_multilingual_strings( $fields );
	}
}

if ( ! function_exists( 'ywccp_filter_multilingual_strings' ) ) {
	/**
	 * Filter strings for WPML
	 *
	 * @since  1.0.0
	 * @author Francesco Licandro
	 * @param $fields
	 * @return array
	 */
	function ywccp_filter_multilingual_strings( $fields ) {

		foreach ( $fields as $section => &$field ) {
			if ( 'account' === $section ) {
				continue;
			}
			foreach ( $field as $key => &$single ) {
				$single = ywccp_multilingual_single_field( $key, $single );
			}
		}

		return $fields;
	}
}

if ( ! function_exists( 'ywccp_filter_multilingual_strings_for_multiple_shipping_addresses_popup' ) ) {
	/**
	 * Filter shipping strings for fields inside popup of YITH Multiple Shipping Addresses for WooCommerce
	 *
	 * @param $fields
	 * @return mixed
	 */
	function ywccp_filter_multilingual_strings_for_multiple_shipping_addresses_popup( $fields ) {

		if ( ! empty( $_SERVER['QUERY_STRING'] ) && strstr( $_SERVER['QUERY_STRING'], 'ywcmas_shipping_address_form' ) ) {
			foreach ( $fields as $key => &$single ) {
				$single = ywccp_multilingual_single_field( $key, $single );
			}
		}

		return $fields;

	}
}

if ( ! function_exists( 'ywccp_filter_get_order_address' ) ) {
	/**
	 * Filter get address method for WC_Order
	 *
	 * @since  1.2.8
	 * @author Francesco Licandro
	 * @param array    $address
	 * @param string   $type
	 * @param WC_Order $order
	 * @return array
	 */
	function ywccp_filter_get_order_address( $address, $type, $order ) {
		$custom_fields = ywccp_get_custom_fields( $type );
		if ( empty( $custom_fields ) ) {
			return $address;
		}

		foreach ( $custom_fields as $key => $options ) {
			$value = $order->get_meta( '_' . $key );
			$key   = str_replace( $type . '_', '', $key );
			if ( $value ) {
				$address[ $key ] = $value;
			}
		}

		return $address;
	}
}

if ( ! function_exists( 'ywccp_check_conditions_for_products_in_cart' ) ) {

	/**
	 * Hide fields basing of products in cart
	 *
	 * @since  1.3.0
	 * @author Alessio Torrisi
	 * @param $fields
	 * @return mixed
	 */
	function ywccp_check_conditions_for_products_in_cart( $fields ) {
		$is_valid   = false;
		$cart       = WC()->cart->get_cart_contents();
		$products   = array();
		$categories = array();

		foreach ( $cart as $item ) {
			$products[] = ! empty( $item['variation_id'] ) ? $item['variation_id'] : $item['product_id'];
			$item_id    = $item['data']->get_parent_id() ? $item['data']->get_parent_id() : $item['data']->get_id();
			$product    = wc_get_product( $item_id );
			if( $product instanceof WC_Product ){
                foreach ( $product->get_category_ids() as $key => $value ) {
                    $categories[] = $value;
                }
            }
		}

		foreach ( $fields as $section_key => $section ) {
			foreach ( $section as $field_key => $field_value ) {
				$conditions = ywccp_get_conditions_for_field( $field_key );

				foreach ( $conditions as $condition ) {
					$values_to_check = explode( ',', $condition['value'] );
					if ( 'products' === $condition['input_name'] ) {
						switch ( $condition['type'] ) {
							case 'at-least-one-product-in-cart':
								if ( ! empty( array_intersect( $products, $values_to_check ) ) ) {
									$is_valid = true;
								}
								break;

							case 'all-in-cart':
								foreach ( $values_to_check as $product ) {
									if ( ! in_array( $product, $products ) ) {
										$is_valid = false;
										break;
									} else {
										$is_valid = true;
									}
								}

								break;

							case 'at-least-one-category-in-cart':
								if ( ! empty( array_intersect( $categories, $values_to_check ) ) ) {
									$is_valid = true;
								}
								break;

							case 'all-categories-in-cart':
								foreach ( $values_to_check as $category ) {
									if ( ! in_array( $category, $categories ) ) {
										$is_valid = false;
										break;
									} else {
										$is_valid = true;
									}
								}
								break;

							default:
								break;

						}
					}

					if ( $is_valid ) {
						if ( 'hide' === $condition['action'] ) {
							unset( $fields[ $section_key ][ $field_key ] );
						} elseif ( 'required' === $condition['action'] ) {
							$fields[ $section_key ][ $field_key ]['required'] = true;
						}
						break;
					} elseif ( 'products' === $condition['input_name'] && 'show' === $condition['action'] ) {
						unset( $fields[ $section_key ][ $field_key ] );
					}
				}
			}
		}
		return $fields;
	}
}

if ( ! function_exists( 'ywccp_validate_conditions_field_before_to_place_the_order' ) ) {
	/**
	 * Deny place order if at least one condition is not valid
	 *
	 * @since  1.3.0
	 * @author Alessio Torrisi
	 * @param array $data
	 * @param       $errors
	 * @return mixed
	 */
	function ywccp_validate_conditions_field_before_to_place_the_order( $data, $errors ) {

		$checkout_fields_details = ywccp_get_all_checkout_fields();

		foreach ( $data as $key => $field ) {
			ywccp_can_field_be_placed( $key, $data, $checkout_fields_details );
		}
		return $data;
	}
}

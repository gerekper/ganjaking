<?php
/**
 * Multiple Upload Field class
 *
 * @package Extra Product Options/Fields
 * @version 6.4.2
 * phpcs:disable PEAR.NamingConventions.ValidClassName
 */

defined( 'ABSPATH' ) || exit;

/**
 * Multiple Upload Field class
 *
 * @package Extra Product Options/Fields
 * @version 6.4.2
 */
class THEMECOMPLETE_EPO_FIELDS_multiple_file_upload extends THEMECOMPLETE_EPO_FIELDS {

	/**
	 * Display field array
	 *
	 * @param array<mixed> $element The element array.
	 * @param array<mixed> $args Array of arguments.
	 * @return array<mixed>
	 * @since 6.4.2
	 */
	public function display_field( $element = [], $args = [] ) {

		$saved_value = '';
		if ( ! empty( THEMECOMPLETE_EPO_CART()->last_added_cart_key ) || ( THEMECOMPLETE_EPO()->is_edit_mode() && THEMECOMPLETE_EPO()->cart_edit_key ) ) {
			$cart_item_key = THEMECOMPLETE_EPO()->is_edit_mode() ? THEMECOMPLETE_EPO()->cart_edit_key : THEMECOMPLETE_EPO_CART()->last_added_cart_key;
			$cart_item     = WC()->cart->get_cart_item( $cart_item_key );

			if ( $cart_item ) {
				if ( isset( $cart_item['tmcartepo'] ) ) {
					$saved_epos = $cart_item['tmcartepo'];
					foreach ( $saved_epos as $key => $val ) {
						if ( $element['uniqid'] === $val['section'] ) {
							$saved_value = $val['value'];
							break;
						}
					}
				}
				if ( empty( $saved_value ) && isset( $cart_item['tmcartfees'] ) ) {
					$saved_epos = $cart_item['tmcartfees'];
					foreach ( $saved_epos as $key => $val ) {
						if ( $element['uniqid'] === $val['section'] ) {
							$saved_value = $val['value'];
							break;
						}
					}
				}
			}
		}
		$style = $this->get_value( $element, 'button_type', '' );

		$upload_text = '';
		switch ( $style ) {
			case '':
				$style = ' cpf-upload-container-basic';
				break;
			case 'button':
				$style       = ' cpf-upload-container';
				$upload_text = ( ( ! empty( THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_select_file_text' ) ) ) ? esc_html( THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_select_file_text' ) ) : esc_html__( 'Select file', 'woocommerce-tm-extra-product-options' ) );
				break;
		}

		/* translators: %s file size */
		$max_file_size_text = sprintf( esc_html__( '(max file size %s)', 'woocommerce-tm-extra-product-options' ), size_format( wp_max_upload_size() ) );

		$class_label = '';
		if ( 'yes' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_select_fullwidth' ) ) {
			$class_label = ' fullwidth';
		}
		$display = [
			'max_size'           => size_format( wp_max_upload_size() ),
			'style'              => $style,
			'textbeforeprice'    => $this->get_value( $element, 'text_before_price', '' ),
			'textafterprice'     => $this->get_value( $element, 'text_after_price', '' ),
			'hide_amount'        => $this->get_value( $element, 'hide_amount', '' ),
			'quantity'           => $this->get_value( $element, 'quantity', '' ),
			'saved_value'        => $saved_value,
			'upload_text'        => $upload_text,
			'max_file_size_text' => $max_file_size_text,
			'allowed_mimes'      => implode( ', ', THEMECOMPLETE_EPO()->get_allowed_mimes() ),
			'class_label'        => $class_label,
		];

		return apply_filters( 'wc_epo_display_field_upload', $display, $this, $element, $args );
	}

	/**
	 * Field validation
	 *
	 * @return array<mixed>
	 * @since 6.4.2
	 */
	public function validate() {

		$passed  = true;
		$message = [];
		$files   = $_FILES; // phpcs:ignore WordPress.Security.NonceVerification

		foreach ( $this->field_names as $attribute ) {
			if ( isset( $files[ $attribute ] ) ) {
				$file          = $files[ $attribute ];
				$file_name     = '';
				$file_tmp_name = '';
				if ( ! empty( $file ) ) {
					$file_name     = $file['name'];
					$file_tmp_name = $file['tmp_name'];
				}
				if ( ! ( isset( $this->epo_post_fields[ $attribute ] ) && '' !== $this->epo_post_fields[ $attribute ] ) ) {
					if ( $this->element['required'] && ( empty( $file ) || empty( $file_name ) ) ) {
						$passed    = false;
						$message[] = 'required';
						break;
					} elseif ( ! empty( $file_name ) && is_array( $file_name ) ) {
						$name     = (array) $file_name;
						$tmp_name = (array) $file_tmp_name;
						foreach ( $name as $name_id => $name_value ) {
							if ( $this->element['required'] && empty( $name_value ) ) {
								$passed    = false;
								$message[] = 'required';
								break 2;
							} elseif ( ! empty( $name_value ) ) {
								$ext   = strtolower( pathinfo( $name_value, PATHINFO_EXTENSION ) );
								$check = true;
								if ( apply_filters( 'wc_epo_no_upload_to_png', true ) && in_array( $ext, [ 'jpg', 'png', 'gif' ], true ) ) {
									$check = THEMECOMPLETE_EPO_HELPER()->upload_to_png( $tmp_name[ $name_id ], $tmp_name[ $name_id ] );
								}
								if ( false === $check ) {
									$passed = false;
									/* translators: %s file name. */
									$message[] = sprintf( esc_html__( '%s is not a valid image file!', 'woocommerce-tm-extra-product-options' ), $name_value );
									break 2;
								}
							}
						}
					}
				}
			}
		}

		return [
			'passed'  => $passed,
			'message' => $message,
		];
	}

	/**
	 * Add field data to cart (single type fields)
	 *
	 * @return false|array<mixed>
	 * @since 6.4.2
	 */
	public function add_cart_item_data_single() {
		$files = $_FILES; // phpcs:ignore WordPress.Security.NonceVerification

		$can_be_added = false;
		$posted_check = false;
		if ( isset( $this->post_data[ $this->attribute ] ) && '' !== $this->post_data[ $this->attribute ] ) {
			$posted_check = true;
		}
		if ( $posted_check ) {
			$value        = $this->post_data[ $this->attribute ];
			$can_be_added = true;
		} elseif ( ! empty( $files[ $this->attribute ] ) && ! empty( $files[ $this->attribute ]['name'] ) ) {
			if ( is_array( $files[ $this->attribute ]['name'] ) ) {
				$file_object = [];
				$upload      = [];
				$value       = [];
				foreach ( $files[ $this->attribute ]['name'] as $index => $file ) {
					if ( $file ) {
						foreach ( $files[ $this->attribute ] as $att => $value_object ) {
							$file_object[ $att ] = $value_object[ $index ];
						}
						$upload[ $index ] = THEMECOMPLETE_EPO()->upload_file( $file_object, $this->key_id, $this->keyvalue_id );
						if ( false !== $upload[ $index ] ) {
							if ( empty( $upload[ $index ]['error'] ) && ! empty( $upload[ $index ]['file'] ) ) {
								$value[ $index ] = wc_clean( $upload[ $index ]['url'] );
								if ( empty( $upload[ $index ]['tc'] ) && 'yes' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_upload_success_message' ) ) {
									wc_add_notice( esc_html__( 'Upload successful', 'woocommerce-tm-extra-product-options' ), 'success' );
								}
								$can_be_added = true;
							} else {
								wc_add_notice( $upload[ $index ]['error'], 'error' );
							}
						}
					}
				}
			} else {
				$upload = THEMECOMPLETE_EPO()->upload_file( $files[ $this->attribute ], $this->key_id, $this->keyvalue_id );
				if ( false !== $upload ) {
					if ( empty( $upload['error'] ) && ! empty( $upload['file'] ) ) {
						$value = wc_clean( $upload['url'] );
						if ( empty( $upload['tc'] ) && 'yes' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_upload_success_message' ) ) {
							wc_add_notice( esc_html__( 'Upload successful', 'woocommerce-tm-extra-product-options' ), 'success' );
						}
						$can_be_added = true;

					} else {
						wc_add_notice( $upload['error'], 'error' );
					}
				}
			}
		}

		if ( isset( $upload ) && isset( $value ) && ! empty( $value ) && $can_be_added ) {
			if ( ! is_array( $upload ) ) {
				$upload = [ $upload ];
			}
			if ( ! is_array( $value ) ) {
				$value = [ $value ];
			}
			if ( count( $value ) === 1 ) {
				if ( ! empty( $value[0] ) ) {
					$test = explode( '|', $value[0] );
					if ( is_array( $test ) && count( $test ) > 1 ) {
						$value = $test;
					}
				}
			}
			$value = THEMECOMPLETE_EPO_HELPER()->to_ssl( $value );

			$display_value = [];
			foreach ( $value as $v ) {
				$display_value[] = THEMECOMPLETE_EPO_ORDER()->display_meta_value( $v, 1, 'always' );
			}

			$value         = implode( '|', $value );
			$display_value = implode( '|', $display_value );

			$_price = THEMECOMPLETE_EPO()->calculate_price( $this->post_data, $this->element, $this->key, $this->attribute, 1, $this->key_id, $this->keyvalue_id, $this->per_product_pricing, $this->cpf_product_price, $this->variation_id );
			if ( empty( $this->key ) ) {
				$_price = 0;
			}

			return apply_filters(
				'wc_epo_add_cart_item_data_single',
				[
					'mode'                             => 'builder',
					'cssclass'                         => $this->element['class'],
					'hidelabelincart'                  => $this->element['hide_element_label_in_cart'],
					'hidevalueincart'                  => $this->element['hide_element_value_in_cart'],
					'hidelabelinorder'                 => $this->element['hide_element_label_in_order'],
					'hidevalueinorder'                 => $this->element['hide_element_value_in_order'],
					'shippingmethodsenable'            => $this->element['shipping_methods_enable'],
					'shippingmethodsenablelogicrules'  => $this->element['shipping_methods_enable_logicrules'],
					'shippingmethodsdisable'           => $this->element['shipping_methods_disable'],
					'shippingmethodsdisablelogicrules' => $this->element['shipping_methods_disable_logicrules'],
					'element'                          => $this->order_saved_element,
					'name'                             => $this->element['label'],
					'value'                            => $value,
					'post_name'                        => $this->attribute,
					'display'                          => $display_value,
					'price'                            => $_price,
					'section'                          => $this->element['uniqid'],
					'section_label'                    => $this->element['label'],
					'percentcurrenttotal'              => isset( $this->post_data[ $this->attribute . '_hidden' ] ) ? 1 : 0,
					'fixedcurrenttotal'                => isset( $this->post_data[ $this->attribute . '_hiddenfixed' ] ) ? 1 : 0,
					'currencies'                       => isset( $this->element['currencies'] ) ? $this->element['currencies'] : [],
					'price_per_currency'               => $this->fill_currencies( 1 ),
					'quantity'                         => 1,
					'quantity_selector'                => '',
					'multiple_values'                  => '|',
					'multiple'                         => '1',
					'key'                              => 0,
					'use_images'                       => false,
					'changes_product_image'            => false,
					'imagesp'                          => '',
					'images'                           => '',
					'file'                             => isset( $upload ) && isset( $upload['file'] ) ? $upload : '',
				],
				$this
			);
		}

		return false;
	}

	/**
	 * Add field data to cart (fees single)
	 *
	 * @return false|array<mixed>
	 * @since 6.4.2
	 */
	public function add_cart_item_data_cart_fees_single() {
		$files = $_FILES; // phpcs:ignore WordPress.Security.NonceVerification

		$can_be_added = false;
		$posted_check = false;
		if ( isset( $this->post_data[ $this->attribute ] ) && '' !== $this->post_data[ $this->attribute ] ) {
			$posted_check = true;
		}
		if ( $posted_check ) {
			$value        = $this->post_data[ $this->attribute ];
			$can_be_added = true;
		} elseif ( ! empty( $files[ $this->attribute ] ) && ! empty( $files[ $this->attribute ]['name'] ) ) {
			if ( is_array( $files[ $this->attribute ]['name'] ) ) {
				$file_object = [];
				$upload      = [];
				$value       = [];
				foreach ( $files[ $this->attribute ]['name'] as $index => $file ) {
					if ( $file ) {
						foreach ( $files[ $this->attribute ] as $att => $value_object ) {
							$file_object[ $att ] = $value_object[ $index ];
						}
						$upload[ $index ] = THEMECOMPLETE_EPO()->upload_file( $file_object, $this->key_id, $this->keyvalue_id );
						if ( false !== $upload[ $index ] ) {
							if ( empty( $upload[ $index ]['error'] ) && ! empty( $upload[ $index ]['file'] ) ) {
								$value[ $index ] = wc_clean( $upload[ $index ]['url'] );
								if ( empty( $upload[ $index ]['tc'] ) && 'yes' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_upload_success_message' ) ) {
									wc_add_notice( esc_html__( 'Upload successful', 'woocommerce-tm-extra-product-options' ), 'success' );
								}
								$can_be_added = true;
							} else {
								wc_add_notice( $upload[ $index ]['error'], 'error' );
							}
						}
					}
				}
			} else {
				$upload = THEMECOMPLETE_EPO()->upload_file( $files[ $this->attribute ], $this->key_id, $this->keyvalue_id );
				if ( false !== $upload ) {
					if ( empty( $upload['error'] ) && ! empty( $upload['file'] ) ) {
						$value = wc_clean( $upload['url'] );
						if ( empty( $upload['tc'] ) && 'yes' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_upload_success_message' ) ) {
							wc_add_notice( esc_html__( 'Upload successful', 'woocommerce-tm-extra-product-options' ), 'success' );
						}
						$can_be_added = true;

					} else {
						wc_add_notice( $upload['error'], 'error' );
					}
				}
			}
		}

		if ( isset( $upload ) && isset( $value ) && ! empty( $value ) && $can_be_added ) {
			if ( ! is_array( $upload ) ) {
				$upload = [ $upload ];
			}
			if ( ! is_array( $value ) ) {
				$value = [ $value ];
			}
			if ( count( $value ) === 1 ) {
				if ( ! empty( $value[0] ) ) {
					$test = explode( '|', $value[0] );
					if ( is_array( $test ) && count( $test ) > 1 ) {
						$value = $test;
					}
				}
			}
			$value = THEMECOMPLETE_EPO_HELPER()->to_ssl( $value );

			$display_value = [];
			foreach ( $value as $v ) {
				$display_value[] = THEMECOMPLETE_EPO_ORDER()->display_meta_value( $v, 0, 'always' );
			}

			$value         = implode( '|', $value );
			$display_value = implode( '|', $display_value );

			$_price = THEMECOMPLETE_EPO()->calculate_price( $this->post_data, $this->element, $this->key, $this->attribute, 1, $this->key_id, $this->keyvalue_id, $this->per_product_pricing, $this->cpf_product_price, $this->variation_id );
			if ( empty( $this->key ) ) {
				$_price = 0;
			}

			return apply_filters(
				'wc_epo_add_cart_item_data_single',
				[
					'mode'                             => 'builder',
					'include_tax_for_fee_price_type'   => $this->element['include_tax_for_fee_price_type'],
					'tax_class_for_fee_price_type'     => $this->element['tax_class_for_fee_price_type'],
					'cssclass'                         => $this->element['class'],
					'hidelabelincart'                  => $this->element['hide_element_label_in_cart'],
					'hidevalueincart'                  => $this->element['hide_element_value_in_cart'],
					'hidelabelinorder'                 => $this->element['hide_element_label_in_order'],
					'hidevalueinorder'                 => $this->element['hide_element_value_in_order'],
					'shippingmethodsenable'            => $this->element['shipping_methods_enable'],
					'shippingmethodsenablelogicrules'  => $this->element['shipping_methods_enable_logicrules'],
					'shippingmethodsdisable'           => $this->element['shipping_methods_disable'],
					'shippingmethodsdisablelogicrules' => $this->element['shipping_methods_disable_logicrules'],
					'element'                          => $this->order_saved_element,
					'name'                             => $this->element['label'],
					'value'                            => $value,
					'post_name'                        => $this->attribute,
					'display'                          => $display_value,
					'price'                            => THEMECOMPLETE_EPO_CART()->calculate_fee_price( $_price, $this->product_id, $this->element ),
					'section'                          => $this->element['uniqid'],
					'section_label'                    => $this->element['label'],
					'percentcurrenttotal'              => isset( $this->post_data[ $this->attribute . '_hidden' ] ) ? 1 : 0,
					'fixedcurrenttotal'                => isset( $this->post_data[ $this->attribute . '_hiddenfixed' ] ) ? 1 : 0,
					'currencies'                       => isset( $this->element['currencies'] ) ? $this->element['currencies'] : [],
					'price_per_currency'               => $this->fill_currencies( 1 ),
					'quantity'                         => 1,
					'quantity_selector'                => '',
					'multiple_values'                  => '|',
					'multiple'                         => '1',
					'key'                              => 0,
					'use_images'                       => false,
					'changes_product_image'            => false,
					'imagesp'                          => '',
					'images'                           => '',
					'cart_fees'                        => 'multiple',
					'file'                             => isset( $upload ) && isset( $upload['file'] ) ? $upload : '',
				],
				$this
			);
		}

		return false;
	}
}

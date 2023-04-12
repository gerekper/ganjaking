<?php
/**
 * Upload Field class
 *
 * @package Extra Product Options/Fields
 * @version 6.0
 * phpcs:disable PEAR.NamingConventions.ValidClassName
 */

defined( 'ABSPATH' ) || exit;

/**
 * Upload Field class
 *
 * @package Extra Product Options/Fields
 * @version 6.0
 */
class THEMECOMPLETE_EPO_FIELDS_upload extends THEMECOMPLETE_EPO_FIELDS {

	/**
	 * Display field array
	 *
	 * @param array $element The element array.
	 * @param array $args Array of arguments.
	 * @since 1.0
	 */
	public function display_field( $element = [], $args = [] ) {

		$saved_value = '';
		if ( isset( $element ) && ( ! empty( THEMECOMPLETE_EPO_CART()->last_added_cart_key ) || ( THEMECOMPLETE_EPO()->is_edit_mode() && THEMECOMPLETE_EPO()->cart_edit_key ) ) ) {
			$cart_item_key = THEMECOMPLETE_EPO()->is_edit_mode() ? THEMECOMPLETE_EPO()->cart_edit_key : THEMECOMPLETE_EPO_CART()->last_added_cart_key;
			$cart_item     = WC()->cart->get_cart_item( $cart_item_key );

			if ( $cart_item ) {
				if ( isset( $cart_item['tmcartepo'] ) ) {
					$saved_epos = $cart_item['tmcartepo'];
					foreach ( $saved_epos as $key => $val ) {
						if ( $element['uniqid'] === $val['section'] ) {
							if ( isset( $val['repeater'] ) && isset( $args['get_posted_key'] ) ) {
								if ( (string) $val['key_id'] === (string) $args['get_posted_key'] ) {
									$saved_value = $val['value'];
									break;
								}
							} else {
								$saved_value = $val['value'];
								break;
							}
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
				$upload_text = ( ( ! empty( THEMECOMPLETE_EPO()->tm_epo_select_file_text ) ) ? esc_html( THEMECOMPLETE_EPO()->tm_epo_select_file_text ) : esc_html__( 'Select file', 'woocommerce-tm-extra-product-options' ) );
				break;
		}

		/* translators: %s file size */
		$max_file_size_text = sprintf( esc_html__( '(max file size %s)', 'woocommerce-tm-extra-product-options' ), size_format( wp_max_upload_size() ) );

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
		];

		return apply_filters( 'wc_epo_display_field_upload', $display, $this, $element, $args );
	}

	/**
	 * Field validation
	 *
	 * @since 1.0
	 */
	public function validate() {

		$passed  = true;
		$message = [];
		$files   = $_FILES;

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
					} elseif ( ! empty( $file_name ) ) {
						$name     = (array) $file_name;
						$tmp_name = (array) $file_tmp_name;
						foreach ( $name as $name_id => $name_value ) {
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

		return [
			'passed'  => $passed,
			'message' => $message,
		];
	}

	/**
	 * Add field data to cart (single type fields)
	 *
	 * @since 1.0
	 */
	public function add_cart_item_data_single() {
		$files  = $_FILES;
		$_price = THEMECOMPLETE_EPO()->calculate_price( $this->post_data, $this->element, $this->key, $this->attribute, 1, $this->key_id, $this->keyvalue_id, $this->per_product_pricing, $this->cpf_product_price, $this->variation_id );
		if ( empty( $this->key ) ) {
			$_price = 0;
		}
		$can_be_added = false;

		$posted_check = false;
		if ( isset( $this->post_data[ $this->attribute ] ) && '' !== $this->post_data[ $this->attribute ] ) {
			if ( is_array( $this->post_data[ $this->attribute ] ) ) {
				if ( isset( $this->post_data[ $this->attribute ][ $this->key_id ] ) && '' !== $this->post_data[ $this->attribute ][ $this->key_id ] ) {
					$posted_check = true;
				}
			} else {
				$posted_check = true;
			}
		}
		if ( $posted_check ) {
			$value = $this->post_data[ $this->attribute ];
			if ( is_array( $value ) && isset( $value[ $this->key_id ] ) ) {
				$value = $value[ $this->key_id ];
				if ( is_array( $value ) && isset( $value[ $this->keyvalue_id ] ) ) {
					$value = $value[ $this->keyvalue_id ];
				}
			}
			$can_be_added = true;
		} elseif ( ! empty( $files[ $this->attribute ] ) && ! empty( $files[ $this->attribute ]['name'] ) ) {
			$upload = THEMECOMPLETE_EPO()->upload_file( $files[ $this->attribute ], $this->key_id, $this->keyvalue_id );
			if ( false !== $upload ) {
				if ( empty( $upload['error'] ) && ! empty( $upload['file'] ) ) {
					$value = wc_clean( $upload['url'] );
					if ( empty( $upload['tc'] ) && THEMECOMPLETE_EPO()->tm_epo_upload_success_message === 'yes' ) {
						wc_add_notice( esc_html__( 'Upload successful', 'woocommerce-tm-extra-product-options' ), 'success' );
					}
					$can_be_added = true;

				} else {
					wc_add_notice( $upload['error'], 'error' );
				}
			}
		}

		if ( isset( $value ) ) {
			$value = THEMECOMPLETE_EPO_HELPER()->to_ssl( $value );
		}

		if ( $can_be_added ) {
			return apply_filters(
				'wc_epo_add_cart_item_data_single',
				[
					'mode'                => 'builder',
					'cssclass'            => $this->element['class'],
					'hidelabelincart'     => $this->element['hide_element_label_in_cart'],
					'hidevalueincart'     => $this->element['hide_element_value_in_cart'],
					'hidelabelinorder'    => $this->element['hide_element_label_in_order'],
					'hidevalueinorder'    => $this->element['hide_element_value_in_order'],
					'element'             => $this->order_saved_element,
					'name'                => $this->element['label'],
					'value'               => $value,
					'display'             => THEMECOMPLETE_EPO_ORDER()->display_meta_value( $value, 1 ),
					'price'               => $_price,
					'section'             => $this->element['uniqid'],
					'section_label'       => $this->element['label'],
					'percentcurrenttotal' => isset( $this->post_data[ $this->attribute . '_hidden' ] ) ? 1 : 0,
					'fixedcurrenttotal'   => isset( $this->post_data[ $this->attribute . '_hiddenfixed' ] ) ? 1 : 0,
					'currencies'          => isset( $this->element['currencies'] ) ? $this->element['currencies'] : [],
					'price_per_currency'  => $this->fill_currencies( 1 ),
					'quantity'            => 1,
				],
				$this
			);
		}

		return false;
	}

	/**
	 * Add field data to cart (fees single)
	 *
	 * @since 1.0
	 */
	public function add_cart_item_data_cart_fees_single() {
		$files = $_FILES;
		if ( ! empty( $files[ $this->attribute ] ) && ! empty( $files[ $this->attribute ]['name'] ) ) {
			$upload = THEMECOMPLETE_EPO()->upload_file( $files[ $this->attribute ], $this->key_id, $this->keyvalue_id );
			if ( empty( $upload['error'] ) && ! empty( $upload['file'] ) ) {
				$value = wc_clean( $upload['url'] );
				if ( empty( $upload['tc'] ) && THEMECOMPLETE_EPO()->tm_epo_upload_success_message === 'yes' ) {
					wc_add_notice( esc_html__( 'Upload successful', 'woocommerce-tm-extra-product-options' ), 'success' );
				}
				$_price = THEMECOMPLETE_EPO()->calculate_price( $this->post_data, $this->element, $this->key, $this->attribute, 1, $this->key_id, $this->keyvalue_id, $this->per_product_pricing, $this->cpf_product_price, $this->variation_id );
				if ( empty( $this->key ) ) {
					$_price = 0;
				}

				return [
					'mode'                           => 'builder',
					'cssclass'                       => $this->element['class'],
					'include_tax_for_fee_price_type' => $this->element['include_tax_for_fee_price_type'],
					'tax_class_for_fee_price_type'   => $this->element['tax_class_for_fee_price_type'],
					'hidelabelincart'                => $this->element['hide_element_label_in_cart'],
					'hidevalueincart'                => $this->element['hide_element_value_in_cart'],
					'hidelabelinorder'               => $this->element['hide_element_label_in_order'],
					'hidevalueinorder'               => $this->element['hide_element_value_in_order'],
					'element'                        => $this->order_saved_element,
					'name'                           => $this->element['label'],
					'value'                          => $value,
					'display'                        => THEMECOMPLETE_EPO_ORDER()->display_meta_value( $value, 0 ),
					'price'                          => THEMECOMPLETE_EPO_CART()->calculate_fee_price( $_price, $this->product_id, $this->element, $this->attribute ),
					'section'                        => $this->element['uniqid'],
					'section_label'                  => $this->element['label'],
					'percentcurrenttotal'            => 0,
					'fixedcurrenttotal'              => 0,
					'currencies'                     => isset( $this->element['currencies'] ) ? $this->element['currencies'] : [],
					'price_per_currency'             => $this->fill_currencies( 1 ),
					'quantity'                       => 1,

					'cart_fees'                      => 'single',
				];
			}
		}

		return false;
	}

}

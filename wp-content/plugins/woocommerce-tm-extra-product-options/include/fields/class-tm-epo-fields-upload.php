<?php
/**
 * Upload Field class
 *
 * @package Extra Product Options/Fields
 * @version 4.9
 */

defined( 'ABSPATH' ) || exit;

class THEMECOMPLETE_EPO_FIELDS_upload extends THEMECOMPLETE_EPO_FIELDS {

	/**
	 * Display field array
	 *
	 * @since 1.0
	 */
	public function display_field( $element = array(), $args = array() ) {

		$saved_value = "";
		if ( isset( $element ) && THEMECOMPLETE_EPO()->is_edit_mode() && THEMECOMPLETE_EPO()->cart_edit_key ) {
			$cart_item_key = THEMECOMPLETE_EPO()->cart_edit_key;
			$cart_item     = WC()->cart->get_cart_item( $cart_item_key );

			if ( $cart_item ) {
				if ( isset( $cart_item['tmcartepo'] ) ) {
					$saved_epos = $cart_item['tmcartepo'];
					foreach ( $saved_epos as $key => $val ) {
						if ( $element['uniqid'] == $val["section"] ) {
							$saved_value = $val["value"];
							break;
						}
					}
				}
				if ( empty( $saved_value ) && isset( $cart_item['tmcartfees'] ) ) {
					$saved_epos = $cart_item['tmcartfees'];
					foreach ( $saved_epos as $key => $val ) {
						if ( $element['uniqid'] == $val["section"] ) {
							$saved_value = $val["value"];
							break;
						}
					}
				}
			}
		}
		$style = isset( $element['button_type'] ) ? $element['button_type'] : "";

		$upload_text = "";
		switch ( $style ) {
			case "":
				$style = " cpf-upload-container-basic";
				break;
			case "button":
				$style       = " cpf-upload-container";
				$upload_text = ( ( ! empty( THEMECOMPLETE_EPO()->tm_epo_select_file_text ) ) ? esc_html( THEMECOMPLETE_EPO()->tm_epo_select_file_text ) : esc_html__( 'Select file', 'woocommerce-tm-extra-product-options' ) );
				break;
		}

		return array(
			'max_size'        => size_format( wp_max_upload_size() ),
			'style'           => $style,
			'textbeforeprice' => isset( $element['text_before_price'] ) ? $element['text_before_price'] : "",
			'textafterprice'  => isset( $element['text_after_price'] ) ? $element['text_after_price'] : "",
			'hide_amount'     => isset( $element['hide_amount'] ) ? " " . $element['hide_amount'] : "",
			'quantity'        => isset( $element['quantity'] ) ? $element['quantity'] : "",
			'saved_value'     => $saved_value,
			'upload_text'     => $upload_text,
		);
	}

	/**
	 * Field validation
	 *
	 * @since 1.0
	 */
	public function validate() {

		$passed  = TRUE;
		$message = array();

		foreach ( $this->field_names as $attribute ) {
			if ( isset( $_FILES[ $attribute ] ) ) {

				if ( ! ( isset( $this->epo_post_fields[ $attribute ] ) && $this->epo_post_fields[ $attribute ] !== "" ) ) {

					if ( $this->element['required'] && ( empty( $_FILES[ $attribute ] ) || empty( $_FILES[ $attribute ]['name'] ) ) ) {
						$passed    = FALSE;
						$message[] = 'required';
						break;
					} elseif ( ! empty( $_FILES[ $attribute ]['name'] ) ) {
						$ext   = strtolower( pathinfo( $_FILES[ $attribute ]['name'], PATHINFO_EXTENSION ) );
						$check = TRUE;
						if ( apply_filters( 'wc_epo_no_upload_to_png', TRUE ) && in_array( $ext, array( 'jpg', 'png', 'gif' ) ) ) {
							$check = THEMECOMPLETE_EPO_HELPER()->upload_to_png( $_FILES[ $attribute ]['tmp_name'], $_FILES[ $attribute ]['tmp_name'] );
						}
						if ( $check === FALSE ) {
							$passed    = FALSE;
							$message[] = sprintf( esc_html__( "%s is not a valid image file!", 'woocommerce-tm-extra-product-options' ), $_FILES[ $attribute ]['name'] );
							break;
						}

					}

				}

			}
		}

		return array( 'passed' => $passed, 'message' => $message );
	}

	/**
	 * Add field data to cart (single type fields)
	 *
	 * @since 1.0
	 */
	public function add_cart_item_data_single() {
		$_price = THEMECOMPLETE_EPO()->calculate_price( $this->post_data, $this->element, $this->key, $this->attribute, $this->per_product_pricing, $this->cpf_product_price, $this->variation_id );
		if ( empty( $this->key ) ) {
			$_price = 0;
		}
		$can_be_added = FALSE;

		if ( isset( $this->post_data[ $this->attribute ] ) && $this->post_data[ $this->attribute ] !== "" ) {
			$value        = $this->post_data[ $this->attribute ];
			$can_be_added = TRUE;
		} elseif ( ! empty( $_FILES[ $this->attribute ] ) && ! empty( $_FILES[ $this->attribute ]['name'] ) ) {
			$upload = THEMECOMPLETE_EPO()->upload_file( $_FILES[ $this->attribute ] );

			if ( empty( $upload['error'] ) && ! empty( $upload['file'] ) ) {
				$value = wc_clean( $upload['url'] );
				if ( empty( $upload['tc'] ) ) {
					wc_add_notice( esc_html__( "Upload successful", 'woocommerce-tm-extra-product-options' ), 'success' );
				}
				$can_be_added = TRUE;

			} else {
				wc_add_notice( $upload['error'], 'error' );
			}
		}

		if ( $can_be_added ) {
			return apply_filters( 'wc_epo_add_cart_item_data_single', array(
				'mode' => 'builder',

				'cssclass'         => $this->element['class'],
				'hidelabelincart'  => $this->element['hide_element_label_in_cart'],
				'hidevalueincart'  => $this->element['hide_element_value_in_cart'],
				'hidelabelinorder' => $this->element['hide_element_label_in_order'],
				'hidevalueinorder' => $this->element['hide_element_value_in_order'],
				'element'          => $this->order_saved_element,

				'name'                => $this->element['label'],
				'value'               => $value,
				'display'             => THEMECOMPLETE_EPO_ORDER()->display_meta_value( $value, 1 ),
				'price'               => $_price,
				'section'             => $this->element['uniqid'],
				'section_label'       => $this->element['label'],
				'percentcurrenttotal' => isset( $this->post_data[ $this->attribute . '_hidden' ] ) ? 1 : 0,
				'fixedcurrenttotal'   => isset( $this->post_data[ $this->attribute . '_hiddenfixed' ] ) ? 1 : 0,
				'currencies'          => isset( $this->element['currencies'] ) ? $this->element['currencies'] : array(),
				'price_per_currency'  => $this->fill_currencies(),
				'quantity'            => 1,
			), $this );
		}

		return FALSE;
	}

	/**
	 * Add field data to cart (fees single)
	 *
	 * @since 1.0
	 */
	public function add_cart_item_data_cart_fees_single() {

		if ( ! empty( $_FILES[ $this->attribute ] ) && ! empty( $_FILES[ $this->attribute ]['name'] ) ) {
			$upload = THEMECOMPLETE_EPO()->upload_file( $_FILES[ $this->attribute ] );
			if ( empty( $upload['error'] ) && ! empty( $upload['file'] ) ) {
				$value = wc_clean( $upload['url'] );
				if ( empty( $upload['tc'] ) ) {
					wc_add_notice( esc_html__( "Upload successful", 'woocommerce-tm-extra-product-options' ), 'success' );
				}
				$_price = THEMECOMPLETE_EPO()->calculate_price( $this->post_data, $this->element, $this->key, $this->attribute, $this->per_product_pricing, $this->cpf_product_price, $this->variation_id );
				if ( empty( $this->key ) ) {
					$_price = 0;
				}

				return array(
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
					'price'                          => THEMECOMPLETE_EPO_CART()->cacl_fee_price( $_price, $this->product_id, $this->element, $this->attribute ),
					'section'                        => $this->element['uniqid'],
					'section_label'                  => $this->element['label'],
					'percentcurrenttotal'            => 0,
					'fixedcurrenttotal'              => 0,
					'currencies'                     => isset( $this->element['currencies'] ) ? $this->element['currencies'] : array(),
					'price_per_currency'             => $this->fill_currencies(),
					'quantity'                       => 1,

					'cart_fees' => 'single',
				);
			}
		}

		return FALSE;
	}

}

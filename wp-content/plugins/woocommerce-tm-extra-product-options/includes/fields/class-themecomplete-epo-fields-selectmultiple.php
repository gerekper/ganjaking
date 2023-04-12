<?php
/**
 * Select Box Multiple Field class
 *
 * @package Extra Product Options/Fields
 * @version 6.0
 * phpcs:disable PEAR.NamingConventions.ValidClassName
 */

defined( 'ABSPATH' ) || exit;

/**
 * Select Box Multiple Field class
 *
 * @package Extra Product Options/Fields
 * @version 6.0
 */
class THEMECOMPLETE_EPO_FIELDS_selectmultiple extends THEMECOMPLETE_EPO_FIELDS {

	/**
	 * Display field array
	 *
	 * @param array $element The element array.
	 * @param array $args Array of arguments.
	 * @since 1.0
	 */
	public function display_field( $element = [], $args = [] ) {

		$changes_product_image = empty( $element['changes_product_image'] ) ? '' : $element['changes_product_image'];

		$class_label = '';
		if ( THEMECOMPLETE_EPO()->tm_epo_select_fullwidth === 'yes' ) {
			$class_label = ' fullwidth';
		}

		$display = [
			'options'               => [],
			'options_array'         => [],
			'use_url'               => isset( $element['use_url'] ) ? $element['use_url'] : '',
			'textbeforeprice'       => isset( $element['text_before_price'] ) ? $element['text_before_price'] : '',
			'textafterprice'        => isset( $element['text_after_price'] ) ? $element['text_after_price'] : '',
			'hide_amount'           => $this->get_value( $element, 'hide_amount', '' ),
			'changes_product_image' => $changes_product_image,
			'quantity'              => isset( $element['quantity'] ) ? $element['quantity'] : '',
			'class_label'           => $class_label,
		];

		$_default_value_counter           = 0;
		$display['default_value_counter'] = false;

		$selected_value = '';
		if ( isset( $args['posted_name'] ) ) {
			$name = $args['posted_name'];
			if ( 'no' === THEMECOMPLETE_EPO()->tm_epo_global_reset_options_after_add && isset( $this->post_data[ $name ] ) ) {
				$selected_value = $this->post_data[ $name ];
			} elseif ( empty( $this->post_data ) && isset( $_REQUEST[ $name ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$selected_value = wp_unslash( $_REQUEST[ $name ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.Recommended
			} elseif ( THEMECOMPLETE_EPO()->is_quick_view() || ( empty( $this->post_data ) || ( isset( $this->post_data['action'] ) && 'wc_epo_get_associated_product_html' === $this->post_data['action'] ) ) || 'yes' === THEMECOMPLETE_EPO()->tm_epo_global_reset_options_after_add ) {
				$selected_value = -1;
			}
		}

		$selected_value = apply_filters( 'wc_epo_default_value', $selected_value, $element );

		if ( is_array( $selected_value ) ) {

			if ( isset( $args['get_posted_key'] ) && isset( $selected_value[ $args['get_posted_key'] ] ) ) {
				$selected_value = $selected_value[ $args['get_posted_key'] ];
			} else {
				$selected_value = '';
			}
		}

		foreach ( $element['options'] as $value => $label ) {
			$default_value = isset( $element['default_value'] ) && isset( $element['default_value'][ $_default_value_counter ] )
				?
				( ( '' !== $element['default_value'][ $_default_value_counter ] )
					? ( (int) $element['default_value'][ $_default_value_counter ] )
					: false )
				: false;

			$selected = false;

			if ( -1 === $selected_value ) {
				if ( ( THEMECOMPLETE_EPO()->is_quick_view() || ( ( empty( $this->post_data ) || ( ! empty( $this->post_data ) && ! isset( $this->post_data['quantity'] ) ) ) || ( isset( $this->post_data['action'] ) && 'wc_epo_get_associated_product_html' === $this->post_data['action'] ) ) || 'yes' === THEMECOMPLETE_EPO()->tm_epo_global_reset_options_after_add ) ) {
					if ( false !== $default_value ) {
						$selected = true;
					}
				}
			} else {
				if ( $default_value && ! empty( $element['default_value_override'] ) && isset( $element['default_value'] ) ) {
					$selected = true;
				} elseif ( is_array( $selected_value ) && in_array( esc_attr( $value ), array_map( 'esc_attr', array_map( 'stripcslashes', $selected_value ) ), true ) ) {
					$selected = true;
				}
			}
			if ( $selected ) {
				$display['default_value_counter'] = $value;
			}

			$data_url = isset( $element['url'][ $_default_value_counter ] ) ? $element['url'][ $_default_value_counter ] : '';

			$css_class = apply_filters( 'wc_epo_multiple_options_css_class', '', $element, $_default_value_counter );
			if ( '' !== $css_class ) {
				$css_class = ' ' . $css_class;
			}

			$image_variations = [];
			if ( $changes_product_image ) {
				$image_link       = '';
				$image_variations = THEMECOMPLETE_EPO_HELPER()->generate_image_array( $image_variations, $image_link, 'image' );

				$image_link       = isset( $element['imagesp'][ $_default_value_counter ] ) ? $element['imagesp'][ $_default_value_counter ] : '';
				$image_variations = THEMECOMPLETE_EPO_HELPER()->generate_image_array( $image_variations, $image_link, 'imagep' );
			}

			$value_to_show = isset( $element['original_options'] ) && isset( $element['original_options'][ $value ] ) ? $element['original_options'][ $value ] : $value;

			$text = apply_filters( 'woocommerce_tm_epo_option_name', $label, $element, $_default_value_counter, $value, $label );
			$text = wptexturize( apply_filters( 'wc_epo_kses', $text, $text ) );

			$option = [
				'selected'            => $selected,
				'current'             => true,
				'value_to_show'       => $value_to_show,
				'css_class'           => $css_class,
				'data_url'            => $data_url,
				'data_imagep'         => isset( $element['imagesp'][ $_default_value_counter ] ) ? $element['imagesp'][ $_default_value_counter ] : '',
				'data_price'          => isset( $element['rules_filtered'][ $value ][0] ) ? $element['rules_filtered'][ $value ][0] : 0,
				'tm_tooltip_html'     => ( isset( $element['cdescription'] ) && isset( $element['cdescription'][ $_default_value_counter ] ) ) ? apply_filters( 'wc_epo_kses', $element['cdescription'][ $_default_value_counter ], $element['cdescription'][ $_default_value_counter ] ) : '',
				'image_variations'    => wp_json_encode( $image_variations ),
				'data_rules'          => isset( $element['rules_filtered'][ $value ] ) ? wp_json_encode( ( $element['rules_filtered'][ $value ] ) ) : '',
				'data_original_rules' => isset( $element['original_rules_filtered'][ $value ] ) ? wp_json_encode( ( $element['original_rules_filtered'][ $value ] ) ) : '',
				'data_rulestype'      => isset( $element['rules_type'][ $value ] ) ? wp_json_encode( ( $element['rules_type'][ $value ] ) ) : '',
				'data_text'           => $label,
				'data_hide_amount'    => empty( $element['hide_amount'] ) ? '0' : '1',
				'text'                => $text,
			];

			$option               = apply_filters( 'wc_epo_select_options', $option, $element, $_default_value_counter );
			$display['options'][] = apply_filters( 'wc_epo_multiple_options', $option, $element, $_default_value_counter );

			$display['options_array'][] = [
				'value'            => $value,
				'data_url'         => $data_url,
				'image_variations' => wp_json_encode( $image_variations ),
				'imagep'           => ( isset( $element['imagesp'][ $_default_value_counter ] ) ? $element['imagesp'][ $_default_value_counter ] : '' ),
				'price'            => ( isset( $element['rules_filtered'][ $value ][0] ) ? $element['rules_filtered'][ $value ][0] : 0 ),
				'cdescription'     => ( isset( $element['cdescription'] ) ? ( isset( $element['cdescription'][ $_default_value_counter ] ) ? $element['cdescription'][ $_default_value_counter ] : '' ) : '' ),
				'rules'            => ( isset( $element['rules_filtered'][ $value ] ) ? wp_json_encode( ( $element['rules_filtered'][ $value ] ) ) : '' ),
				'original_rules'   => ( isset( $element['original_rules_filtered'][ $value ] ) ? wp_json_encode( ( $element['original_rules_filtered'][ $value ] ) ) : '' ),
				'rulestype'        => ( isset( $element['rules_type'][ $value ] ) ? wp_json_encode( ( $element['rules_type'][ $value ] ) ) : '' ),
				'label'            => apply_filters( 'woocommerce_tm_epo_option_name', $label, $element, $_default_value_counter, $value, $label ),
			];

			$_default_value_counter ++;
		}

		$display['element'] = $element;

		if ( ! empty( $changes_product_image ) ) {
			$fieldtype            = $args['fieldtype'] . ' tm-product-image';
			$display['fieldtype'] = $fieldtype;
		}

		if ( ! empty( $args['element_data_attr'] ) && is_array( $args['element_data_attr'] ) ) {
			$display['element_data_attr'] = $args['element_data_attr'];
		} else {
			$display['element_data_attr'] = [];
		}

		return apply_filters( 'wc_epo_display_field_select', $display, $this, $element, $args );
	}

	/**
	 * Field validation
	 *
	 * @since 1.0
	 */
	public function validate() {

		$passed  = true;
		$message = [];

		$quantity_once = false;
		$min_quantity  = isset( $this->element['quantity_min'] ) ? (int) $this->element['quantity_min'] : 0;
		if ( apply_filters( 'wc_epo_field_min_quantity_greater_than_zero', true ) && $min_quantity < 0 ) {
			$min_quantity = 0;
		}
		foreach ( $this->field_names as $attribute ) {
			$attribute_quantity = $attribute . '_quantity';
			if ( ! $quantity_once && isset( $this->epo_post_fields[ $attribute ] ) && '' !== $this->epo_post_fields[ $attribute ] && isset( $this->epo_post_fields[ $attribute_quantity ] ) && ! ( (int) array_sum( (array) $this->epo_post_fields[ $attribute_quantity ] ) >= $min_quantity ) ) {
				$passed        = false;
				$quantity_once = true;
				/* translators: %1 element label %2 quantity value. */
				$message[] = sprintf( esc_html__( 'The quantity for "%1$s" must be greater than %2$s', 'woocommerce-tm-extra-product-options' ), $this->element['options'][ $this->epo_post_fields[ $attribute ] ], $min_quantity );
			}
			if ( $this->element['required'] ) {
				if ( ! isset( $this->epo_post_fields[ $attribute ] ) || '' === $this->epo_post_fields[ $attribute ] ) {
					$passed    = false;
					$message[] = 'required';
					break;
				}
			}
		}

		return [
			'passed'  => $passed,
			'message' => $message,
		];
	}

}

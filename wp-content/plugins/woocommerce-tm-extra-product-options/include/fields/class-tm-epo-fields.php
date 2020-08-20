<?php
/**
 * Field class
 *
 * This class acts as the base class
 * for the various field options.
 *
 * @package Extra Product Options/Fields
 * @version 4.9
 */

defined( 'ABSPATH' ) || exit;

class THEMECOMPLETE_EPO_FIELDS {

	public $product_id;
	public $element;
	public $order_saved_element;
	public $attribute;
	public $key;
	public $per_product_pricing;
	public $cpf_product_price;
	public $variation_id;
	public $post_data;
	public $holder;
	public $holder_cart_fees;
	public $epo_post_fields;
	public $loop;
	public $form_prefix;
	public $tmcp_attributes;
	public $tmcp_attributes_fee;
	public $field_names;

	private $setup = FALSE;

	/**
	 * Class Constructor
	 *
	 * @since 1.0
	 */
	public function __construct( $product_id = FALSE, $element = FALSE, $per_product_pricing = FALSE, $cpf_product_price = FALSE, $variation_id = FALSE, $post_data = NULL ) {
		if ( is_null( $post_data ) && isset( $_POST ) ) {
			$post_data = $_POST;
		}
		if ( empty( $post_data ) && isset( $_REQUEST['tcajax'] ) ) {
			$post_data = $_REQUEST;
		}
		$this->post_data = $post_data;
		if ( $product_id !== FALSE ) {
			$this->product_id          = $product_id;
			$this->element             = $element;
			$this->order_saved_element = array(
				'type'       => $element['type'],
				'rules_type' => $element['rules_type'],
				'_'          => array( 'price_type' => isset( $element['_']['price_type'] ) ? $element['_']['price_type'] : FALSE ),
			);
			$this->per_product_pricing = $per_product_pricing;
			$this->cpf_product_price   = $cpf_product_price;
			$this->variation_id        = $variation_id;
			$this->holder           = THEMECOMPLETE_EPO()->tm_builder_elements[ $this->element['type'] ]['type'];
			$this->holder_cart_fees = THEMECOMPLETE_EPO()->tm_builder_elements[ $this->element['type'] ]['fee_type'];
			$this->setup = TRUE;
		}
	}

	/**
	 * Check is all variables are setup
	 *
	 * @since 1.0
	 */
	public function is_setup() {
		return $this->setup;
	}

	/**
	 * Return price per currency array
	 *
	 * @since 1.0
	 */
	public function fill_currencies() {
		$price_per_currencies = isset( $this->element['price_per_currencies'] ) ? $this->element['price_per_currencies'] : array();
		$price_per_currency   = array();
		$current_currency     = themecomplete_get_woocommerce_currency();
 
		foreach ( $price_per_currencies as $currency => $price_rule ) {
			$copy_element                         = $this->element;
			$copy_element['price_rules_original'] = $copy_element['price_rules'];
			$copy_element['price_rules']          = $price_rule;
			$currency_price                       = THEMECOMPLETE_EPO()->calculate_price( $this->post_data, $copy_element, $this->key, $this->attribute, $this->per_product_pricing, $this->cpf_product_price, $this->variation_id, '', $currency, $current_currency, $price_per_currencies );
			$price_per_currency[ $currency ]      = $currency_price;
		}

		return $price_per_currency;
	}

	/**
	 * Display field array
	 *
	 * @since 1.0
	 */
	public function display_field( $element = array(), $args = array() ) {
		return array();
	}

	/**
	 * Pre display field actions
	 *
	 * @since 1.0
	 */
	public function display_field_pre( $element = array(), $args = array() ) {

	}

	/**
	 * Field pre validation
	 *
	 * @since 1.0
	 */
	public final function validate_field( $epo_post_fields = FALSE, $element = FALSE, $loop = FALSE, $form_prefix = FALSE ) {
		$this->epo_post_fields     = $epo_post_fields;
		$this->element             = $element;
		$this->loop                = $loop;
		$this->form_prefix         = $form_prefix;
		$this->tmcp_attributes     = THEMECOMPLETE_EPO()->get_post_names( $element['options'], $element['type'], $loop, $form_prefix );
		$this->tmcp_attributes_fee = THEMECOMPLETE_EPO()->get_post_names( $element['options'], $element['type'], $loop, $form_prefix, THEMECOMPLETE_EPO()->cart_fee_name );

		$is_fee = FALSE;
		if ( isset( $this->element['is_cart_fee'] ) ) {
			if ( is_array( $this->element['is_cart_fee'] ) && isset( $this->element['is_cart_fee'][ $loop ] ) ) {
				$is_fee = $this->element['is_cart_fee'][ $loop ];
			} elseif ( ! is_array( $this->element['is_cart_fee'] ) ) {
				$is_fee = $this->element['is_cart_fee'];
			}
		}

		if ( $is_fee ) {
			$this->field_names = $this->tmcp_attributes_fee;
		} else {
			$this->field_names = $this->tmcp_attributes;
			$this->field_names = apply_filters( 'wc_epo_validate_field_field_names', $this->field_names, $this, $element, $loop, $form_prefix );
		}

		return $this->validate();
	}

	/**
	 * Field validation
	 *
	 * @since 1.0
	 */
	public function validate() {
		return array( 'passed' => TRUE, 'message' => FALSE );
	}

	/**
	 * Add field data to cart
	 *
	 * @since 1.0
	 */
	public final function add_cart_item_data( $attribute = FALSE, $key = FALSE ) {
		if ( ! $this->setup ) {
			return FALSE;
		}
		$this->attribute = $attribute;
		$this->key       = $key;

		$ret = FALSE;
		if ( $this->holder == "single" || $this->holder == "multipleallsingle" ) {
			$ret = $this->add_cart_item_data_single();
		} elseif ( $this->holder == "multiple" || $this->holder == "multipleall" || $this->holder == "multiplesingle" ) {
			$ret = $this->add_cart_item_data_multiple();
		}

		if ($ret !== FALSE){

			$_price_type = THEMECOMPLETE_EPO()->get_element_price_type("", $this->element, $this->key, $this->per_product_pricing, $this->variation_id);
			if ($_price_type === "math"){
				$_price = THEMECOMPLETE_EPO()->get_element_price(0, $_price_type, $this->element, $this->key, $this->per_product_pricing, $this->variation_id);
				if (strpos($_price, '{quantity}') !== false){
					$ret['price_formula'] = $_price;
				}
			}
			return $ret;

		}

		return FALSE;
	}

	/**
	 * Add field data to cart (fees)
	 *
	 * @since 1.0
	 */
	public final function add_cart_item_data_cart_fees( $attribute = FALSE, $key = FALSE ) {
		if ( ! $this->setup ) {
			return FALSE;
		}
		$this->attribute = $attribute;
		$this->key       = $key;
		if ( $this->holder_cart_fees == "single" ) {
			$ret = $this->add_cart_item_data_cart_fees_single();
		} elseif ( $this->holder_cart_fees == "multiple" ) {
			$ret = $this->add_cart_item_data_cart_fees_multiple();
		}

		if ($ret !== FALSE){

			$_price_type = THEMECOMPLETE_EPO()->get_element_price_type("", $this->element, $this->key, $this->per_product_pricing, $this->variation_id);
			if ($_price_type === "math"){
				$_price = THEMECOMPLETE_EPO()->get_element_price(0, $_price_type, $_price_type, $this->element, $this->key, $this->per_product_pricing, $this->variation_id);
				if (strpos($_price, '{quantity}') !== false){
					$ret['price_formula'] = $_price;
				}
			}
			return $ret;

		}

		return FALSE;

	}

	/**
	 * Add field data to cart (single type fields)
	 *
	 * @since 1.0
	 */
	public function add_cart_item_data_single() {
		if ( ! $this->setup ) {
			return FALSE;
		}
		if ( isset( $this->key ) && $this->key != '' ) {

			$_price = THEMECOMPLETE_EPO()->calculate_price( $this->post_data, $this->element, $this->key, $this->attribute, $this->per_product_pricing, $this->cpf_product_price, $this->variation_id );

			return apply_filters( 'wc_epo_add_cart_item_data_single', array(
				'mode'                => 'builder',
				'cssclass'            => $this->element['class'],
				'hidelabelincart'     => $this->element['hide_element_label_in_cart'],
				'hidevalueincart'     => $this->element['hide_element_value_in_cart'],
				'hidelabelinorder'    => $this->element['hide_element_label_in_order'],
				'hidevalueinorder'    => $this->element['hide_element_value_in_order'],
				'element'             => $this->order_saved_element,
				'name'                => $this->element['label'],
				'value'               => $this->key,
				'price'               => $_price,
				'section'             => $this->element['uniqid'],
				'section_label'       => $this->element['label'],
				'percentcurrenttotal' => isset( $this->post_data[ $this->attribute . '_hidden' ] ) ? 1 : 0,
				'fixedcurrenttotal'   => isset( $this->post_data[ $this->attribute . '_hiddenfixed' ] ) ? 1 : 0,
				'currencies'          => isset( $this->element['currencies'] ) ? $this->element['currencies'] : array(),
				'price_per_currency'  => $this->fill_currencies(),
				'quantity'            => isset( $this->post_data[ $this->attribute . '_quantity' ] ) ? $this->post_data[ $this->attribute . '_quantity' ] : 1,
			), $this );
		}

		return FALSE;
	}

	/**
	 * Add field data to cart (multiple type fields)
	 *
	 * @since 1.0
	 */
	public function add_cart_item_data_multiple() {

		if ( ! $this->setup ) {
			return FALSE;
		}

		// select placeholder check 
		if ( isset( $this->element['options'][ esc_attr( $this->key ) ] ) ) {
			$_price = THEMECOMPLETE_EPO()->calculate_price( $this->post_data, $this->element, $this->key, $this->attribute, $this->per_product_pricing, $this->cpf_product_price, $this->variation_id );

			$use_images = ! empty( $this->element['use_images'] ) ? $this->element['use_images'] : "";
			if ( $use_images ) {
				$_image_key = array_search( $this->key, $this->element['option_values'] );
				if ( $_image_key === NULL || $_image_key === FALSE ) {
					$_image_key = FALSE;
				}
			} else {
				$_image_key = FALSE;
			}

			$use_colors = ! empty( $this->element['use_colors'] ) ? $this->element['use_colors'] : "";
			if ( $use_colors ) {
				$_color_key = array_search( $this->key, $this->element['option_values'] );
				if ( $_color_key === NULL || $_color_key === FALSE ) {
					$_color_key = FALSE;
				}
			} else {
				$_color_key = FALSE;
			}

			$changes_product_image = ! empty( $this->element['changes_product_image'] ) ? $this->element['changes_product_image'] : "";
			if ( $changes_product_image ) {
				$c_image_key = array_search( $this->key, $this->element['option_values'] );
				if ( $c_image_key === NULL || $c_image_key === FALSE ) {
					$c_image_key = FALSE;
				}
			} else {
				$c_image_key = FALSE;
			}

			return apply_filters( 'wc_epo_add_cart_item_data_multiple', array(
				'mode'                => 'builder',
				'cssclass'            => $this->element['class'],
				'hidelabelincart'     => $this->element['hide_element_label_in_cart'],
				'hidevalueincart'     => $this->element['hide_element_value_in_cart'],
				'hidelabelinorder'    => $this->element['hide_element_label_in_order'],
				'hidevalueinorder'    => $this->element['hide_element_value_in_order'],
				'element'             => $this->order_saved_element,
				'name'                => $this->element['label'],
				'value'               => $this->element['options'][ esc_attr( $this->key ) ],
				'price'               => $_price,
				'section'             => $this->element['uniqid'],
				'section_label'       => $this->element['label'],
				'percentcurrenttotal' => isset( $this->post_data[ $this->attribute . '_hidden' ] ) ? 1 : 0,
				'fixedcurrenttotal'   => isset( $this->post_data[ $this->attribute . '_hiddenfixed' ] ) ? 1 : 0,
				'currencies'          => isset( $this->element['currencies'] ) ? $this->element['currencies'] : array(),
				'price_per_currency'  => $this->fill_currencies(),
				'quantity'            => isset( $this->post_data[ $this->attribute . '_quantity' ] ) ? $this->post_data[ $this->attribute . '_quantity' ] : 1,
				'multiple'              => '1',
				'key'                   => esc_attr( $this->key ),
				'use_images'            => $use_images,
				'use_colors'            => $use_colors,
				'changes_product_image' => $changes_product_image,
				'imagesp'               => ( $c_image_key !== FALSE && isset( $this->element['imagesp'][ $c_image_key ] ) ) ? $this->element['imagesp'][ $c_image_key ] : "",
				'images'                => ( $_image_key !== FALSE && isset( $this->element['images'][ $_image_key ] ) ) ? $this->element['images'][ $_image_key ] : "",
				'color'                 => ( $_color_key !== FALSE && isset( $this->element['color'][ $_color_key ] ) ) ? empty( $this->element['color'][ $_color_key ] ) ? "transparent" : $this->element['color'][ $_color_key ] : "",
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
		if ( ! $this->setup ) {
			return FALSE;
		}
		if ( isset( $this->key ) && $this->key != '' ) {
			$_price = THEMECOMPLETE_EPO()->calculate_price( $this->post_data, $this->element, $this->key, $this->attribute, $this->per_product_pricing, $this->cpf_product_price, $this->variation_id );

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
				'value'                          => $this->key,
				'price'                          => THEMECOMPLETE_EPO_CART()->cacl_fee_price( $_price, $this->product_id, $this->element, $this->attribute ),
				'section'                        => $this->element['uniqid'],
				'section_label'                  => $this->element['label'],
				'percentcurrenttotal'            => 0,
				'fixedcurrenttotal'              => 0,
				'currencies'                     => isset( $this->element['currencies'] ) ? $this->element['currencies'] : array(),
				'price_per_currency'             => $this->fill_currencies(),
				'quantity'                       => isset( $this->post_data[ $this->attribute . '_quantity' ] ) ? $this->post_data[ $this->attribute . '_quantity' ] : 1,
				'cart_fees' => 'single',
			);
		}

		return FALSE;
	}

	/**
	 * Add field data to cart (fees multiple)
	 *
	 * @since 1.0
	 */
	public function add_cart_item_data_cart_fees_multiple() {
		if ( ! $this->setup ) {
			return FALSE;
		}
		if ( empty( $this->key ) ) {
			return FALSE;
		}
		// select placeholder check 
		if ( isset( $this->element['options'][ esc_attr( $this->key ) ] ) ) {
			$_price = THEMECOMPLETE_EPO()->calculate_price( $this->post_data, $this->element, $this->key, $this->attribute, $this->per_product_pricing, $this->cpf_product_price, $this->variation_id );

			$use_images = ! empty( $this->element['use_images'] ) ? $this->element['use_images'] : "";
			if ( $use_images ) {
				$_image_key = array_search( $this->key, $this->element['option_values'] );
				if ( $_image_key === NULL || $_image_key === FALSE ) {
					$_image_key = FALSE;
				}
			} else {
				$_image_key = FALSE;
			}

			$use_colors = ! empty( $this->element['use_colors'] ) ? $this->element['use_colors'] : "";
			if ( $use_colors ) {
				$_color_key = array_search( $this->key, $this->element['option_values'] );
				if ( $_color_key === NULL || $_color_key === FALSE ) {
					$_color_key = FALSE;
				}
			} else {
				$_color_key = FALSE;
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
				'value'                          => $this->element['options'][ esc_attr( $this->key ) ],
				'price'                          => THEMECOMPLETE_EPO_CART()->cacl_fee_price( $_price, $this->product_id, $this->element, $this->attribute ),
				'section'                        => $this->element['uniqid'],
				'section_label'                  => $this->element['label'],
				'percentcurrenttotal'            => 0,
				'fixedcurrenttotal'              => 0,
				'currencies'                     => isset( $this->element['currencies'] ) ? $this->element['currencies'] : array(),
				'price_per_currency'             => $this->fill_currencies(),
				'quantity'                       => isset( $this->post_data[ $this->attribute . '_quantity' ] ) ? $this->post_data[ $this->attribute . '_quantity' ] : 1,
				'cart_fees'             => 'multiple',
				'key'                   => esc_attr( $this->key ),
				'use_images'            => $use_images,
				'use_colors'            => $use_colors,
				'color'                 => ( $_color_key !== FALSE && isset( $this->element['color'][ $_color_key ] ) ) ? empty( $this->element['color'][ $_color_key ] ) ? "transparent" : $this->element['color'][ $_color_key ] : "",
				'changes_product_image' => ! empty( $this->element['changes_product_image'] ) ? $this->element['changes_product_image'] : "",
				'images'                => ( $_image_key !== FALSE && isset( $this->element['images'][ $_image_key ] ) ) ? $this->element['images'][ $_image_key ] : "",
				'imagesp'               => ( $_image_key !== FALSE && isset( $this->element['imagesp'][ $_image_key ] ) ) ? $this->element['imagesp'][ $_image_key ] : "",
			);
		}

		return FALSE;
	}

}

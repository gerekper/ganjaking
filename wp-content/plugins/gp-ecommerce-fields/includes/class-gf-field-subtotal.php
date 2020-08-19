<?php

if ( ! class_exists( 'GFForms' ) || ! class_exists( 'GF_Field_Calculation' ) ) {
	die();
}

class GF_Field_Subtotal extends GF_Field_SingleProduct {

	public $type = 'subtotal';

	public function __construct( $data = array() ) {

		parent::__construct( $data );

	}



	// # FORM EDITOR

	public function get_form_editor_button() {
		return array(
			'group' => 'pricing_fields',
			'text'  => $this->get_form_editor_field_title()
		);
	}

	public function get_form_editor_field_title() {
		return ucfirst( $this->type );
	}

	public function get_form_editor_field_settings() {
		return array(
			'label_setting',
			'description_setting',
			'css_class_setting',
			'admin_label_setting',
			'label_placement_setting',
			'conditional_logic_field_setting',
			'ecommerce-products-setting',
		);
	}

	public function get_form_editor_inline_script_on_page_render() {

		$set_default_values = sprintf( 'function SetDefaultValues_%1$s( field ) {

			field.label            = "%2$s";
			field.%1$sProductsType = "all";
			field.%1$sProducts     = [];

			return field;
		};', $this->type, $this->get_form_editor_field_title() );

		return $set_default_values;
	}



	// # BACKEND

	public static function get_subtotal( $order, $exclude_products = array() ) {

		$subtotal = 0;

		if( ! $order ) {
			return $subtotal;
		}

		foreach ( $order['products'] as $product_id => $product ) {

			if( rgar( $product, 'isTax' ) || rgar( $product, 'isDiscount' ) || rgar( $product, 'isCoupon' ) ) {
				continue;
			}

			if( in_array( $product_id, $exclude_products ) ) {
				continue;
			}

			$price = GFCommon::to_number( $product['price'] );
			if ( is_array( rgar( $product, 'options' ) ) ) {
				foreach ( $product['options'] as $option ) {
					$price += GFCommon::to_number( $option['price'] );
				}
			}

			$subtotal += floatval( rgar( $product, 'quantity' ) ) * $price;

		}

		return max( 0, $subtotal );
	}

	public static function add_order_summary_subtotal_items( $order_summary, $form, $entry, $order ) {
		$labels = gp_ecommerce_fields()->get_order_labels( $form['id'] );
		$order_summary['subtotal'][] = array(
			'name'      => $labels['subtotal'],
			'price'     => GF_Field_Subtotal::get_subtotal( $order ),
			'cellStyle' => 'border-bottom: 1px solid rgba( 0, 0, 0, 0.20 );',
			'class'     => 'subtotal'
		);
		return $order_summary;
	}



    // # FRONTEND

	public function get_field_input( $form, $value = '', $entry = null ) {

		$form_id         = $form['id'];
		$is_entry_detail = $this->is_entry_detail();
		$is_form_editor  = $this->is_form_editor();

		$id      = (int) $this->id;
		$html_id = $is_entry_detail || $is_form_editor || $form_id == 0 ? "input_$id" : 'input_' . $form_id . "_$id";

		if ( $is_entry_detail ) {
			return ''; // field should not be displayed on entry detail
		} else {
			return $this->get_input_markup( $form_id, $id, $html_id );
		}

	}

	public function get_input_markup( $form_id, $field_id, $html_id ) {
	    return "
            <div class='ginput_container'>
                <span class='ginput_{$this->type} ginput_product_price ginput_{$this->type}_{$form_id}_{$field_id}'
                	style='" . $this->get_inline_price_styles() . "'>" . GFCommon::to_money( '0' ) . "</span>
                <input type='hidden' name='input_{$field_id}' id='{$html_id}' class='gform_hidden ginput_{$this->type}_input' 
                    onchange='jQuery( this ).prev( \"span\" ).text( gformFormatMoney( this.value, true ) );' 
                    data-amount='{$this->{$this->type . 'Amount'}}' data-amounttype='{$this->{$this->type . 'AmountType'}}'
                    data-productstype='{$this->{$this->type . 'ProductsType'}}' data-products='" . json_encode( $this->{$this->type . 'Products'} ) . "' />
            </div>";
    }

	public function get_field_label( $force_frontend_label, $value ) {
		// Override GF_Field_SingleProduct::get_field_label() which includes markup that will not get escaped for our field.
		return GF_Field::get_field_label( $force_frontend_label, $value );
	}

    public function get_inline_price_styles() {
		return '';
    }

	public function validate( $value, $form ) {
		return true;
	}

	public function get_value_save_entry( $value, $form, $input_name, $entry_id, $entry ) {
		return $value;
	}

	public function get_value_entry_detail( $value, $currency = '', $use_text = false, $format = 'html', $media = 'screen' ) {
		return GFCommon::format_number( $value, 'currency', $currency );
	}

}

GF_Fields::register( new GF_Field_Subtotal() );

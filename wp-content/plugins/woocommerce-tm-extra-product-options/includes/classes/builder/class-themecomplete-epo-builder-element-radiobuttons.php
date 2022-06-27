<?php
/**
 * Builder Element
 *
 * @package Extra Product Options/Classes/Builder
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Radio buttons Element
 *
 * @package Extra Product Options/Classes/Builder
 * @version 6.0
 */
class THEMECOMPLETE_EPO_BUILDER_ELEMENT_RADIOBUTTONS extends THEMECOMPLETE_EPO_BUILDER_ELEMENT {

	/**
	 * Class Constructor
	 *
	 * @param string $name The element name.
	 * @since 6.0
	 */
	public function __construct( $name = '' ) {
		$this->element_name     = $name;
		$this->is_addon         = false;
		$this->namespace        = $this->elements_namespace;
		$this->name             = esc_html__( 'Radio buttons', 'woocommerce-tm-extra-product-options' );
		$this->description      = '';
		$this->width            = 'w100';
		$this->width_display    = '100%';
		$this->icon             = 'tcfa-dot-circle tcfar';
		$this->is_post          = 'post';
		$this->type             = 'multiple';
		$this->post_name_prefix = 'radio';
		$this->fee_type         = 'multiple';
		$this->tags             = 'price content';
		$this->show_on_backend  = true;
	}

	/**
	 * Initialize element properties
	 *
	 * @since 6.0
	 */
	public function set_properties() {
		$this->properties = $this->add_element(
			$this->element_name,
			[
				'enabled',
				'required',
				'fee',
				'hide_amount',
				'text_before_price',
				'text_after_price',
				'quantity',
				'use_url',
				'replacement_mode',
				'swatch_position',
				'use_lightbox',
				'show_tooltip',
				'changes_product_image',
				'items_per_row',
				'clear_options',
				'options',
			]
		);
	}
}

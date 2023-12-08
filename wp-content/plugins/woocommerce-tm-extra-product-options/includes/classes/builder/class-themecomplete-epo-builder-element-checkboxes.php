<?php
/**
 * Builder Element
 *
 * @package Extra Product Options/Classes/Builder
 * @version 6.4
 */

defined( 'ABSPATH' ) || exit;

/**
 * Checkboxes Element
 *
 * @package Extra Product Options/Classes/Builder
 * @version 6.4
 */
class THEMECOMPLETE_EPO_BUILDER_ELEMENT_CHECKBOXES extends THEMECOMPLETE_EPO_BUILDER_ELEMENT {

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
		$this->name             = esc_html__( 'Checkboxes', 'woocommerce-tm-extra-product-options' );
		$this->description      = '';
		$this->width            = 'w100';
		$this->width_display    = '100%';
		$this->icon             = 'tcfa-check-square';
		$this->is_post          = 'post';
		$this->type             = 'multipleall';
		$this->post_name_prefix = 'checkbox';
		$this->fee_type         = 'multiple';
		$this->tags             = 'price content';
		$this->show_on_backend  = true;
	}

	/**
	 * Initialize element properties
	 *
	 * @since 6.0
	 * @return void
	 */
	public function set_properties() {
		$this->properties = $this->add_element(
			$this->element_name,
			[
				'enabled',
				'required',
				'hide_amount',
				'text_before_price',
				'text_after_price',
				'quantity',
				'limit_choices',
				'exactlimit_choices',
				'minimumlimit_choices',
				'replacement_mode',
				'swatch_position',
				'items_per_row',
				'use_lightbox',
				'show_tooltip',
				'changes_product_image',
				'options',
			]
		);
	}
}

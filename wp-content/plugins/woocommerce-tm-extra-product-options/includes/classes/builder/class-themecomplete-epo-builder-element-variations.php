<?php
/**
 * Builder Element
 *
 * @package Extra Product Options/Classes/Builder
 * @version 6.4
 */

defined( 'ABSPATH' ) || exit;

/**
 * Variations Element
 *
 * @package Extra Product Options/Classes/Builder
 * @version 6.4
 */
class THEMECOMPLETE_EPO_BUILDER_ELEMENT_VARIATIONS extends THEMECOMPLETE_EPO_BUILDER_ELEMENT {

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
		$this->name             = esc_html__( 'Variations', 'woocommerce-tm-extra-product-options' );
		$this->description      = '';
		$this->width            = 'w100';
		$this->width_display    = '100%';
		$this->icon             = 'tcfa-layer-group';
		$this->is_post          = 'display';
		$this->type             = 'multiplesingle';
		$this->post_name_prefix = 'variations';
		$this->fee_type         = '';
		$this->one_time_field   = true;
		$this->no_selection     = true;
		$this->tags             = '';
		$this->show_on_backend  = false;
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
				'variations_disabled',
				'variations_options',
			]
		);
	}
}

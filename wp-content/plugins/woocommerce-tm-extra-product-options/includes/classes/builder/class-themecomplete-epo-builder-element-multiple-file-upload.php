<?php
/**
 * Multiple Upload Element
 *
 * @package Extra Product Options/Classes/Builder
 * @version 6.4.2
 */

defined( 'ABSPATH' ) || exit;

/**
 * Multiple Upload Element
 *
 * @package Extra Product Options/Classes/Builder
 * @version 6.4.2
 */
class THEMECOMPLETE_EPO_BUILDER_ELEMENT_MULTIPLE_FILE_UPLOAD extends THEMECOMPLETE_EPO_BUILDER_ELEMENT {

	/**
	 * Class Constructor
	 *
	 * @param string $name The element name.
	 * @since 6.4.2
	 */
	public function __construct( $name = '' ) {
		$this->element_name     = $name;
		$this->is_addon         = false;
		$this->namespace        = $this->elements_namespace;
		$this->name             = esc_html__( 'Multiple Upload', 'woocommerce-tm-extra-product-options' );
		$this->description      = '';
		$this->width            = 'w100';
		$this->width_display    = '100%';
		$this->icon             = 'tcfa-upload';
		$this->is_post          = 'post';
		$this->type             = 'multipleallsingle';
		$this->post_name_prefix = 'multiple_file_upload';
		$this->fee_type         = 'multiple';
		$this->tags             = 'price content';
		$this->show_on_backend  = true;
	}

	/**
	 * Initialize element properties
	 *
	 * @since 6.4.2
	 * @return void
	 */
	public function set_properties() {
		$this->properties = $this->add_element(
			$this->element_name,
			[
				'enabled',
				'required',
				'price_type5',
				'lookuptable',
				'price',
				'sale_price',
				'fee',
				'hide_amount',
				'text_before_price',
				'text_after_price',
				'button_type',
			]
		);
	}
}

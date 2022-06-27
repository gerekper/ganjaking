<?php
/**
 * Builder Element
 *
 * @package Extra Product Options/Classes/Builder
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Divider Element
 *
 * @package Extra Product Options/Classes/Builder
 * @version 6.0
 */
class THEMECOMPLETE_EPO_BUILDER_ELEMENT_DIVIDER extends THEMECOMPLETE_EPO_BUILDER_ELEMENT {

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
		$this->name             = esc_html__( 'Divider', 'woocommerce-tm-extra-product-options' );
		$this->description      = '';
		$this->width            = 'w100';
		$this->width_display    = '100%';
		$this->icon             = 'tcfa-divide';
		$this->is_post          = 'display';
		$this->type             = '';
		$this->post_name_prefix = 'divider';
		$this->fee_type         = '';
		$this->tags             = 'content';
		$this->show_on_backend  = true;
	}

	/**
	 * Initialize element properties
	 *
	 * @since 6.0
	 */
	public function set_properties() {
		$this->properties = array_merge(
			$this->prepend_div( '', 'tm-tabs' ),
			$this->prepend_div( 'divider', 'tm-tab-headers' ),
			$this->prepend_tab( 'divider2', esc_html__( 'General options', 'woocommerce-tm-extra-product-options' ), 'open' ),
			$this->prepend_tab( 'divider3', esc_html__( 'Conditional Logic', 'woocommerce-tm-extra-product-options' ) ),
			$this->prepend_tab( 'divider4', esc_html__( 'CSS settings', 'woocommerce-tm-extra-product-options' ) ),
			$this->append_div( 'divider' ),
			$this->prepend_div( 'divider2' ),
			$this->get_divider_array(),
			$this->append_div( 'divider2' ),
			$this->prepend_div( 'divider3' ),
			$this->prepend_logic( 'divider' ),
			$this->append_div( 'divider3' ),
			$this->prepend_div( 'divider4' ),
			[
				[
					'id'      => 'divider_class',
					'default' => '',
					'type'    => 'text',
					'tags'    => [
						'class' => 't',
						'id'    => 'builder_divider_class',
						'name'  => 'tm_meta[tmfbuilder][divider_class][]',
						'value' => '',
					],
					'label'   => esc_html__( 'Element class name', 'woocommerce-tm-extra-product-options' ),
					'desc'    => esc_html__( 'Enter an extra class name to add to this element', 'woocommerce-tm-extra-product-options' ),
				],
			],
			$this->append_div( 'divider4' ),
			$this->append_div( '' )
		);
	}
}

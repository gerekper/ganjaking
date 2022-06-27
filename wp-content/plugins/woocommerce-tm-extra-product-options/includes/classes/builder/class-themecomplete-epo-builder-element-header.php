<?php
/**
 * Builder Element
 *
 * @package Extra Product Options/Classes/Builder
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Heading Element
 *
 * @package Extra Product Options/Classes/Builder
 * @version 6.0
 */
class THEMECOMPLETE_EPO_BUILDER_ELEMENT_HEADER extends THEMECOMPLETE_EPO_BUILDER_ELEMENT {

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
		$this->name             = esc_html__( 'Heading', 'woocommerce-tm-extra-product-options' );
		$this->description      = '';
		$this->width            = 'w100';
		$this->width_display    = '100%';
		$this->icon             = 'tcfa-heading';
		$this->is_post          = 'display';
		$this->type             = '';
		$this->post_name_prefix = 'header';
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
			$this->prepend_div( 'header', 'tm-tab-headers' ),
			$this->prepend_tab( 'header2', esc_html__( 'General options', 'woocommerce-tm-extra-product-options' ), 'open' ),
			$this->prepend_tab( 'header3', esc_html__( 'Conditional Logic', 'woocommerce-tm-extra-product-options' ) ),
			$this->prepend_tab( 'header4', esc_html__( 'CSS settings', 'woocommerce-tm-extra-product-options' ) ),
			$this->append_div( 'header' ),
			$this->prepend_div( 'header2' ),
			[
				[
					'id'          => 'header_size',
					'wpmldisable' => 1,
					'default'     => '3',
					'type'        => 'select',
					'tags'        => [
						'id'   => 'builder_header_size',
						'name' => 'tm_meta[tmfbuilder][header_size][]',
					],
					'options'     => [
						[
							'text'  => esc_html__( 'H1', 'woocommerce-tm-extra-product-options' ),
							'value' => '1',
						],
						[
							'text'  => esc_html__( 'H2', 'woocommerce-tm-extra-product-options' ),
							'value' => '2',
						],
						[
							'text'  => esc_html__( 'H3', 'woocommerce-tm-extra-product-options' ),
							'value' => '3',
						],
						[
							'text'  => esc_html__( 'H4', 'woocommerce-tm-extra-product-options' ),
							'value' => '4',
						],
						[
							'text'  => esc_html__( 'H5', 'woocommerce-tm-extra-product-options' ),
							'value' => '5',
						],
						[
							'text'  => esc_html__( 'H6', 'woocommerce-tm-extra-product-options' ),
							'value' => '6',
						],
						[
							'text'  => esc_html__( 'p', 'woocommerce-tm-extra-product-options' ),
							'value' => '7',
						],
						[
							'text'  => esc_html__( 'div', 'woocommerce-tm-extra-product-options' ),
							'value' => '8',
						],
						[
							'text'  => esc_html__( 'span', 'woocommerce-tm-extra-product-options' ),
							'value' => '9',
						],
					],
					'label'       => esc_html__( 'Header type', 'woocommerce-tm-extra-product-options' ),
					'desc'        => '',
				],
				[
					'id'      => 'header_title',
					'default' => '',
					'type'    => 'text',
					'tags'    => [
						'class' => 't tm-header-title',
						'id'    => 'builder_header_title',
						'name'  => 'tm_meta[tmfbuilder][header_title][]',
						'value' => '',
					],
					'label'   => esc_html__( 'Header title', 'woocommerce-tm-extra-product-options' ),
					'desc'    => '',
				],
				[
					'id'          => 'header_title_position',
					'wpmldisable' => 1,
					'default'     => '',
					'type'        => 'select',
					'tags'        => [
						'id'   => 'builder_header_title_position',
						'name' => 'tm_meta[tmfbuilder][header_title_position][]',
					],
					'options'     => [
						[
							'text'  => esc_html__( 'Above field', 'woocommerce-tm-extra-product-options' ),
							'value' => '',
						],
						[
							'text'  => esc_html__( 'Left of the field', 'woocommerce-tm-extra-product-options' ),
							'value' => 'left',
						],
						[
							'text'  => esc_html__( 'Right of the field', 'woocommerce-tm-extra-product-options' ),
							'value' => 'right',
						],
						[
							'text'  => esc_html__( 'Disable', 'woocommerce-tm-extra-product-options' ),
							'value' => 'disable',
						],
					],
					'label'       => esc_html__( 'Header position', 'woocommerce-tm-extra-product-options' ),
					'desc'        => '',
				],
				[
					'id'          => 'header_title_color',
					'wpmldisable' => 1,
					'default'     => '',
					'type'        => 'text',
					'tags'        => [
						'class' => 'tm-color-picker',
						'id'    => 'builder_header_title_color',
						'name'  => 'tm_meta[tmfbuilder][header_title_color][]',
						'value' => '',
					],
					'label'       => esc_html__( 'Header color', 'woocommerce-tm-extra-product-options' ),
					'desc'        => esc_html__( 'Leave empty for default value', 'woocommerce-tm-extra-product-options' ),
				],
				[
					'id'      => 'header_subtitle',
					'default' => '',
					'type'    => 'textarea',
					'tags'    => [
						'id'   => 'builder_header_subtitle',
						'name' => 'tm_meta[tmfbuilder][header_subtitle][]',
					],
					'label'   => esc_html__( 'Content', 'woocommerce-tm-extra-product-options' ),
					'desc'    => '',
				],
				[
					'id'          => 'header_subtitle_color',
					'wpmldisable' => 1,
					'default'     => '',
					'type'        => 'text',
					'tags'        => [
						'class' => 'tm-color-picker',
						'id'    => 'builder_header_subtitle_color',
						'name'  => 'tm_meta[tmfbuilder][header_subtitle_color][]',
						'value' => '',
					],
					'label'       => esc_html__( 'Content color', 'woocommerce-tm-extra-product-options' ),
					'desc'        => esc_html__( 'Leave empty for default value', 'woocommerce-tm-extra-product-options' ),
				],
				[
					'id'          => 'header_subtitle_position',
					'wpmldisable' => 1,
					'default'     => '',
					'type'        => 'select',
					'tags'        => [
						'id'   => 'builder_header_subtitle_position',
						'name' => 'tm_meta[tmfbuilder][header_subtitle_position][]',
					],
					'options'     => [
						[
							'text'  => esc_html__( 'Above field', 'woocommerce-tm-extra-product-options' ),
							'value' => '',
						],
						[
							'text'  => esc_html__( 'Below field', 'woocommerce-tm-extra-product-options' ),
							'value' => 'below',
						],
						[
							'text'  => esc_html__( 'Tooltip', 'woocommerce-tm-extra-product-options' ),
							'value' => 'tooltip',
						],
						[
							'text'  => esc_html__( 'Icon tooltip left', 'woocommerce-tm-extra-product-options' ),
							'value' => 'icontooltipleft',
						],
						[
							'text'  => esc_html__( 'Icon tooltip right', 'woocommerce-tm-extra-product-options' ),
							'value' => 'icontooltipright',
						],
					],
					'label'       => esc_html__( 'Content position', 'woocommerce-tm-extra-product-options' ),
					'desc'        => '',
				],
			],
			$this->append_div( 'header2' ),
			$this->prepend_div( 'header3' ),
			$this->prepend_logic( 'header' ),
			$this->append_div( 'header3' ),
			$this->prepend_div( 'header4' ),
			[
				[
					'id'      => 'header_class',
					'default' => '',
					'type'    => 'text',
					'tags'    => [
						'class' => 't',
						'id'    => 'builder_header_class',
						'name'  => 'tm_meta[tmfbuilder][header_class][]',
						'value' => '',
					],
					'label'   => esc_html__( 'Element class name', 'woocommerce-tm-extra-product-options' ),
					'desc'    => esc_html__( 'Enter an extra class name to add to this element', 'woocommerce-tm-extra-product-options' ),
				],
			],
			$this->append_div( 'header4' ),
			$this->append_div( '' )
		);
	}
}

<?php
/**
 * Builder Element
 *
 * @package Extra Product Options/Classes/Builder
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Section
 *
 * @package Extra Product Options/Classes/Builder
 * @version 6.0
 */
class THEMECOMPLETE_EPO_BUILDER_ELEMENT_SECTION extends THEMECOMPLETE_EPO_BUILDER_ELEMENT {

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
		$this->name             = esc_html__( 'Section', 'woocommerce-tm-extra-product-options' );
		$this->description      = '';
		$this->width            = 'w100';
		$this->width_display    = '100%';
		$this->icon             = 'tcfa-layer-group';
		$this->is_post          = 'display';
		$this->type             = false;
		$this->post_name_prefix = false;
		$this->fee_type         = false;
		$this->tags             = false;
		$this->show_on_backend  = false;
	}

	/**
	 * Initialize element properties
	 *
	 * @since 6.0
	 */
	public function set_properties() {
		$this->properties = array_merge(
			$this->prepend_div( '', 'tm-tabs' ),
			$this->prepend_div( 'section', 'tm-tab-headers' ),
			$this->prepend_tab(
				'section0',
				[
					'tcfa tcfa-heading',
					esc_html__( 'Title options' ),
					'woocommerce-tm-extra-product-options',
				],
				'',
				'tma-tab-title'
			),
			$this->prepend_tab(
				'section1',
				[
					'tcfa tcfa-cog',
					esc_html__( 'General options', 'woocommerce-tm-extra-product-options' ),
				],
				'open',
				'tma-tab-general'
			),
			$this->prepend_tab(
				'section2',
				[
					'tcfa tcfa-lightbulb',
					esc_html__( 'Conditional Logic', 'woocommerce-tm-extra-product-options' ),
				],
				'',
				'tma-tab-logic'
			),
			$this->append_div( 'section' ),
			$this->prepend_div( 'section0' ),
			$this->get_header_array( 'section_header', 'section' ),
			$this->get_divider_array( 'section_divider', 0 ),
			$this->append_div( 'section0' ),
			$this->prepend_div( 'section1' ),
			apply_filters(
				'tc_builder_section_settings',
				[
					'sectionnum'                     => [
						'id'          => 'sections',
						'wpmldisable' => 1,
						'default'     => 0,
						'nodiv'       => 1,
						'type'        => 'hidden',
						'tags'        => [
							'class' => 'tm_builder_sections',
							'name'  => 'tm_meta[tmfbuilder][sections][]',
							'value' => 0,
						],
						'label'       => '',
						'desc'        => '',
					],
					'sections_slides'                => [
						'id'          => 'sections_slides',
						'wpmldisable' => 1,
						'default'     => '',
						'nodiv'       => 1,
						'type'        => 'hidden',
						'tags'        => [
							'class' => 'tm_builder_section_slides',
							'name'  => 'tm_meta[tmfbuilder][sections_slides][]',
							'value' => 0,
						],
						'label'       => '',
						'desc'        => '',
					],
					'sections_tabs_labels'           => [
						'id'      => 'sections_tabs_labels',
						'default' => '',
						'nodiv'   => 1,
						'type'    => 'hidden',
						'tags'    => [
							'class' => 'tm_builder_sections_tabs_labels',
							'name'  => 'tm_meta[tmfbuilder][sections_tabs_labels][]',
							'value' => 0,
						],
						'label'   => '',
						'desc'    => '',
					],
					'sectionsize'                    => [
						'id'          => 'sections_size',
						'wpmldisable' => 1,
						'default'     => 'w100',
						'nodiv'       => 1,
						'type'        => 'hidden',
						'tags'        => [
							'class' => 'tm_builder_sections_size',
							'name'  => 'tm_meta[tmfbuilder][sections_size][]',
							'value' => 'w100',
						],
						'label'       => '',
						'desc'        => '',
					],
					'sectionuniqid'                  => [
						'id'      => 'sections_uniqid',
						'default' => '',
						'nodiv'   => 1,
						'type'    => 'hidden',
						'tags'    => [
							'class' => 'tm-builder-sections-uniqid',
							'name'  => 'tm_meta[tmfbuilder][sections_uniqid][]',
							'value' => '',
						],
						'label'   => '',
						'desc'    => '',
					],
					'sectionstyle'                   => [
						'id'          => 'sections_style',
						'wpmldisable' => 1,
						'default'     => '',
						'type'        => 'select',
						'tags'        => [
							'class' => 'sections_style',
							'id'    => 'tm_sections_style',
							'name'  => 'tm_meta[tmfbuilder][sections_style][]',
						],
						'options'     => [
							[
								'text'  => esc_html__( 'Normal (clear)', 'woocommerce-tm-extra-product-options' ),
								'value' => '',
							],
							[
								'text'  => esc_html__( 'Box', 'woocommerce-tm-extra-product-options' ),
								'value' => 'box',
							],
							[
								'text'  => esc_html__( 'Expand and Collapse (start opened)', 'woocommerce-tm-extra-product-options' ),
								'value' => 'collapse',
								'class' => 'builder_hide_for_variation-reset',
							],
							[
								'text'  => esc_html__( 'Expand and Collapse (start closed)', 'woocommerce-tm-extra-product-options' ),
								'value' => 'collapseclosed',
								'class' => 'builder_hide_for_variation-reset',
							],
							[
								'text'  => esc_html__( 'Accordion', 'woocommerce-tm-extra-product-options' ),
								'value' => 'accordion',
								'class' => 'builder_hide_for_variation-reset',
							],
						],
						'label'       => esc_html__( 'Section style', 'woocommerce-tm-extra-product-options' ),
						'desc'        => esc_html__( "Select this section's display style.", 'woocommerce-tm-extra-product-options' ),
					],
					'sectionbackgroundcolor'         => [
						'id'       => 'sections_background_color',
						'default'  => '',
						'type'     => 'text',
						'tags'     => [
							'data-show-input'            => 'true',
							'data-show-initial'          => 'true',
							'data-allow-empty'           => 'true',
							'data-show-alpha'            => 'false',
							'data-show-palette'          => 'false',
							'data-clickout-fires-change' => 'true',
							'data-show-buttons'          => 'false',
							'data-preferred-format'      => 'hex',
							'class'                      => 'tm-color-picker',
							'id'                         => 'sections_background_color',
							'name'                       => 'tm_meta[tmfbuilder][sections_background_color][]',
							'value'                      => '',
						],
						'label'    => esc_html__( 'Section background color', 'woocommerce-tm-extra-product-options' ),
						'desc'     => esc_html__( 'Leave empty for default value', 'woocommerce-tm-extra-product-options' ),
						'required' => [
							'.sections_style' => [
								'operator' => 'isnot',
								'value'    => '',
							],
						],
					],
					'sectionlabelbackgroundcolor'    => [
						'id'       => 'sections_label_background_color',
						'default'  => '',
						'type'     => 'text',
						'tags'     => [
							'data-show-input'            => 'true',
							'data-show-initial'          => 'true',
							'data-allow-empty'           => 'true',
							'data-show-alpha'            => 'false',
							'data-show-palette'          => 'false',
							'data-clickout-fires-change' => 'true',
							'data-show-buttons'          => 'false',
							'data-preferred-format'      => 'hex',
							'class'                      => 'tm-color-picker',
							'id'                         => 'sections_label_background_color',
							'name'                       => 'tm_meta[tmfbuilder][sections_label_background_color][]',
							'value'                      => '',
						],
						'label'    => esc_html__( 'Section label background color', 'woocommerce-tm-extra-product-options' ),
						'desc'     => esc_html__( 'Leave empty for default value', 'woocommerce-tm-extra-product-options' ),
						'required' => [
							'.sections_style' => [
								'operator' => 'isnot',
								'value'    => '',
							],
						],
					],
					'sectionsubtitlebackgroundcolor' => [
						'id'       => 'sections_subtitle_background_color',
						'default'  => '',
						'type'     => 'text',
						'tags'     => [
							'data-show-input'            => 'true',
							'data-show-initial'          => 'true',
							'data-allow-empty'           => 'true',
							'data-show-alpha'            => 'false',
							'data-show-palette'          => 'false',
							'data-clickout-fires-change' => 'true',
							'data-show-buttons'          => 'false',
							'data-preferred-format'      => 'hex',
							'class'                      => 'tm-color-picker',
							'id'                         => 'sections_subtitle_background_color',
							'name'                       => 'tm_meta[tmfbuilder][sections_subtitle_background_color][]',
							'value'                      => '',
						],
						'label'    => esc_html__( 'Section subtitle background color', 'woocommerce-tm-extra-product-options' ),
						'desc'     => esc_html__( 'Leave empty for default value', 'woocommerce-tm-extra-product-options' ),
						'required' => [
							'.sections_style' => [
								'operator' => 'isnot',
								'value'    => '',
							],
						],
					],
					'sectionplacement'               => [
						'id'               => 'sections_placement',
						'message0x0_class' => 'builder_hide_for_variation',
						'wpmldisable'      => 1,
						'default'          => 'before',
						'type'             => 'select',
						'tags'             => [
							'id'   => 'sections_placement',
							'name' => 'tm_meta[tmfbuilder][sections_placement][]',
						],
						'options'          => [
							[
								'text'  => esc_html__( 'Before Local Options', 'woocommerce-tm-extra-product-options' ),
								'value' => 'before',
							],
							[
								'text'  => esc_html__( 'After Local Options', 'woocommerce-tm-extra-product-options' ),
								'value' => 'after',
							],
						],
						'label'            => esc_html__( 'Section placement', 'woocommerce-tm-extra-product-options' ),
						'desc'             => esc_html__( 'Select where this section will appear compared to local Options.', 'woocommerce-tm-extra-product-options' ),
					],
					'sectiontype'                    => [
						'id'               => 'sections_type',
						'wpmldisable'      => 1,
						'default'          => '',
						'type'             => 'radio',
						'message0x0_class' => 'tm-epo-switch-wrapper',
						'tags'             => [
							'class' => 'sections_type',
							'id'    => 'sections_type',
							'name'  => 'tm_meta[tmfbuilder][sections_type][]',
						],
						'options'          => [
							[
								'text'  => esc_html__( 'Normal', 'woocommerce-tm-extra-product-options' ),
								'value' => '',
							],
							[
								'text'  => esc_html__( 'Pop up', 'woocommerce-tm-extra-product-options' ),
								'value' => 'popup',
							],
							[
								'text'  => esc_html__( 'Slider (wizard)', 'woocommerce-tm-extra-product-options' ),
								'value' => 'slider',
								'class' => 'builder-remove-for-variations',
							],
							[
								'text'  => esc_html__( 'Tabs', 'woocommerce-tm-extra-product-options' ),
								'value' => 'tabs',
								'class' => 'builder-remove-for-variations',
							],
						],
						'label'            => esc_html__( 'Section type', 'woocommerce-tm-extra-product-options' ),
						'desc'             => esc_html__( "Select this section's display type.", 'woocommerce-tm-extra-product-options' ),
					],
					'sectionpopupbutton'             => [
						'id'          => 'sections_popupbutton',
						'wpmldisable' => 1,
						'default'     => '',
						'type'        => 'select',
						'tags'        => [
							'class' => 'sections_popupbutton',
							'id'    => 'sections_popupbutton',
							'name'  => 'tm_meta[tmfbuilder][sections_popupbutton][]',
						],
						'options'     => [
							[
								'text'  => esc_html__( 'Text link', 'woocommerce-tm-extra-product-options' ),
								'value' => '',
							],
							[
								'text'  => esc_html__( 'Button', 'woocommerce-tm-extra-product-options' ),
								'value' => 'button',
							],
							[
								'text'  => esc_html__( 'Button Alt', 'woocommerce-tm-extra-product-options' ),
								'value' => 'buttonalt',
							],
						],
						'label'       => esc_html__( 'Section pop up button style', 'woocommerce-tm-extra-product-options' ),
						'desc'        => esc_html__( "Select this section's pop up button style.", 'woocommerce-tm-extra-product-options' ),
						'required'    => [
							'.sections_type' => [
								'operator' => 'is',
								'value'    => 'popup',
							],
						],
					],
					'sectionpopupbuttontext'         => [
						'id'       => 'sections_popupbuttontext',
						'default'  => '',
						'type'     => 'text',
						'tags'     => [
							'class' => 't',
							'id'    => 'sections_popupbuttontext',
							'name'  => 'tm_meta[tmfbuilder][sections_popupbuttontext][]',
							'value' => '',
						],
						'label'    => esc_html__( 'Section pop up button text', 'woocommerce-tm-extra-product-options' ),
						'desc'     => esc_html__( 'Enter a text to replace the default pop up button text. This will override the global value.', 'woocommerce-tm-extra-product-options' ),
						'required' => [
							'.sections_type' => [
								'operator' => 'is',
								'value'    => 'popup',
							],
						],
					],

					'sectionsclass'                  => [
						'id'      => 'sections_class',
						'default' => '',
						'type'    => 'text',
						'tags'    => [
							'class' => 't',
							'id'    => 'sections_class',
							'name'  => 'tm_meta[tmfbuilder][sections_class][]',
							'value' => '',
						],
						'label'   => esc_html__( 'Section class name', 'woocommerce-tm-extra-product-options' ),
						'desc'    => esc_html__( 'Enter an extra class name to add to this section', 'woocommerce-tm-extra-product-options' ),
					],
				]
			),
			$this->append_div( 'section1' ),
			$this->prepend_div( 'section2' ),
			[
				'sectionclogic' => [
					'id'      => 'sections_clogic',
					'default' => '',
					'nodiv'   => 1,
					'type'    => 'hidden',
					'tags'    => [
						'class' => 'tm-builder-clogic',
						'name'  => 'tm_meta[tmfbuilder][sections_clogic][]',
						'value' => '',
					],
					'label'   => '',
					'desc'    => '',
				],
				'sectionlogic'  => [
					'id'        => 'sections_logic',
					'default'   => '',
					'leftclass' => 'align-self-start',
					'type'      => 'checkbox',
					'tags'      => [
						'class' => 'activate-sections-logic',
						'id'    => 'sections_logic',
						'name'  => 'tm_meta[tmfbuilder][sections_logic][]',
						'value' => '1',
					],
					'extra'     => [ [ $this, 'builder_showlogic' ], [] ],
					'label'     => esc_html__( 'Section Conditional Logic', 'woocommerce-tm-extra-product-options' ),
					'desc'      => esc_html__( 'Enable conditional logic for showing or hiding this section.', 'woocommerce-tm-extra-product-options' ),
				],
			],
			$this->append_div( 'section2' ),
			$this->append_div( '' )
		);
	}
}

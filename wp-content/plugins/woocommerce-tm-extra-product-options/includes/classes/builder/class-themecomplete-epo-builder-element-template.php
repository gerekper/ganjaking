<?php
/**
 * Template Element
 *
 * @package Extra Product Options/Classes/Builder
 * @version 6.4
 */

defined( 'ABSPATH' ) || exit;

/**
 * Template Element
 *
 * @package Extra Product Options/Classes/Builder
 * @version 6.4
 */
class THEMECOMPLETE_EPO_BUILDER_ELEMENT_TEMPLATE extends THEMECOMPLETE_EPO_BUILDER_ELEMENT {

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
		$this->name             = esc_html__( 'Template', 'woocommerce-tm-extra-product-options' );
		$this->description      = '';
		$this->width            = 'w100';
		$this->width_display    = '100%';
		$this->icon             = 'tcfa-shapes';
		$this->is_post          = 'display';
		$this->type             = 'template';
		$this->post_name_prefix = 'template';
		$this->fee_type         = '';
		$this->tags             = 'content';
		$this->show_on_backend  = true;
	}

	/**
	 * Fetch templates
	 * for use in a select box
	 *
	 * @since 5.0
	 * @return array<mixed>
	 */
	public function fetch_template_array() {
		$list = [];
		// for default value to be empty.
		$list[] = [
			'text'  => '',
			'value' => '',
		];

		$args = [
			'post_type'   => THEMECOMPLETE_EPO_TEMPLATE_POST_TYPE,
			'post_status' => [ 'publish' ], // get only enabled global extra options.
			'numberposts' => -1,
			'orderby'     => 'date',
			'order'       => 'asc',
		];

		THEMECOMPLETE_EPO_WPML()->remove_sql_filter();
		THEMECOMPLETE_EPO_WPML()->remove_term_filters();
		$templates = THEMECOMPLETE_EPO_HELPER()->get_cached_posts( $args );
		THEMECOMPLETE_EPO_WPML()->restore_term_filters();
		THEMECOMPLETE_EPO_WPML()->restore_sql_filter();

		foreach ( $templates as $template ) {
			$list[] = [
				'text'  => $template->post_title,
				'value' => $template->ID,
			];
		}

		return $list;
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

				[
					'id'          => 'template_templateids',
					'wpmldisable' => 1,
					'default'     => '',
					'type'        => 'select',
					'tags'        => [
						'data-placeholder' => esc_attr__( 'Select a template ...', 'woocommerce-tm-extra-product-options' ),
						'class'            => 'wc-template-search template-templates-selector',
						'id'               => 'builder_template_templateids',
						'name'             => 'tm_meta[tmfbuilder][template_templateids][]',
					],
					'options'     => $this->fetch_template_array(),
					'label'       => esc_html__( 'Select template', 'woocommerce-tm-extra-product-options' ),
					'desc'        => esc_html__( 'Add the template you want to display.', 'woocommerce-tm-extra-product-options' ),
				],
			],
			false,
			[
				'label_options'        => 0,
				'general_options'      => 1,
				'advanced_options'     => 0,
				'conditional_logic'    => 1,
				'css_settings'         => 0,
				'woocommerce_settings' => 0,
			]
		);
	}
}

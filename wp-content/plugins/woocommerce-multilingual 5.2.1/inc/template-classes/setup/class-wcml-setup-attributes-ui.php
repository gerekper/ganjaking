<?php

class WCML_Setup_Attributes_UI extends WCML_Setup_Step {

	const SLUG = 'attributes';

	/** @var woocommerce_wpml */
	private $woocommerce_wpml;

	/**
	 * @param woocommerce_wpml $woocommerce_wpml
	 * @param string           $next_step_url
	 * @param string           $previous_step_url
	 */
	public function __construct( $woocommerce_wpml, $next_step_url, $previous_step_url ) {
		// @todo Cover by tests, required for wcml-3037.
		parent::__construct( $next_step_url, $previous_step_url );

		$this->woocommerce_wpml = $woocommerce_wpml;
	}

	public function get_model() {

		$wc_attributes            = wc_get_attribute_taxonomies();
		$wc_attributes_translated = $this->woocommerce_wpml->attributes->get_translatable_attributes();
		$attribute_names          = [];
		foreach ( $wc_attributes_translated as $attribute ) {
			$attribute_names[] = $attribute->attribute_name;
		}

		$attributes = [];
		foreach ( $wc_attributes as $attribute ) {
			$attributes[] = [
				'name'       => $attribute->attribute_name,
				'label'      => $attribute->attribute_label,
				'translated' => in_array( $attribute->attribute_name, $attribute_names, true ),
			];
		}

		return [
			'strings'      => [
				'step_id'       => 'attributes_step',
				'heading'       => __( 'Which product attributes should be translatable?', 'woocommerce-multilingual' ),
				// @todo: Check UTM tags for wizard.
				'description_1' => sprintf(
					esc_html__( 'WPML allows you to %1$stranslate your product attributes%2$s. Some attributes, like the ones based on numbers and codes, can be universal and might not need to be translated.', 'woocommerce-multilingual' ),
					'<a target="blank" class="wpml-external-link" rel="noopener" href="' . WCML_Tracking_Link::getWcmlMainDoc( '#taxonomies', [ 'utm_term' => WCML_Tracking_Link::UTM_TERM_WIZARD ] ) . '">',
					'</a>'
				),
				'description_2' => __( 'Select which attributes in your store should be translatable:', 'woocommerce-multilingual' ),
				'no_attributes' => __( 'There are no attributes defined', 'woocommerce-multilingual' ),
				'continue'      => __( 'Continue', 'woocommerce-multilingual' ),
				'go_back'       => __( 'Go back', 'woocommerce-multilingual' ),
			],
			'attributes'   => $attributes,
			'continue_url' => $this->next_step_url,
			'go_back_url'  => $this->previous_step_url,
		];

	}

	public function get_template() {
		return '/setup/attributes.twig';
	}
}

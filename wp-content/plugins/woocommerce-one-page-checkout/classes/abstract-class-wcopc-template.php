<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WooCommerce One Page Checkout Template class
 *
 * Extended by individual payment gateways to handle payments.
 *
 * @class 		WCOPC_Template
 * @version		1.0
 * @package		WooCommerce One Page Checkout
 * @category	Abstract Class
 */
abstract class WCOPC_Template {

	protected $template_key;

	protected $label;

	protected $description;

	protected $supports_containers = false;

	protected $default_product_fields = false;

	public function __construct() {

		if ( empty( $this->template_key ) ) {
			throw new BadMethodCallException( get_class( $this ) . ' must set the $template_key property before calling parent::__construct()' );
		}

		if ( empty( $this->label ) ) {
			throw new BadMethodCallException( get_class( $this ) . ' must set the $template_label property before calling parent::__construct()' );
		}

		add_filter( 'wcopc_templates', array( $this, 'add_template' ) );

		add_filter( 'wcopc_template', array( $this, 'maybe_set_template' ), 10, 2 );

		add_filter( 'wcopc_show_product_selection_fields', array( $this, 'maybe_shortcircuit_default_product_fields' ), 10, 2 );
	}

	public function add_template( $templates ) {
		$templates[ $this->template_key ] = array(
			'label'               => $this->label,
			'description'         => $this->description,
			'supports_containers' => $this->supports_containers,
		);
		return $templates;
	}

	public function maybe_set_template( $template, $shortcode_attributes ) {

		if ( $this->template_key == $shortcode_attributes['template'] ) {
			$template = $this->template_key;
		}

		return $template;
	}

	public function maybe_shortcircuit_default_product_fields( $display_fields, $template ) {

		if ( true === $display_fields && $this->template_key === $template && false === $this->default_product_fields ) {
			$display_fields = false;
		}

		return $display_fields;
	}
}
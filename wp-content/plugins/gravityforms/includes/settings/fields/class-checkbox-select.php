<?php

namespace Rocketgenius\Gravity_Forms\Settings\Fields;

use Rocketgenius\Gravity_Forms\Settings\Fields;

defined( 'ABSPATH' ) || die();

// Load base classes.
require_once 'class-checkbox.php';
require_once 'class-select.php';

class Checkbox_And_Select extends Base {

	/**
	 * Field type.
	 *
	 * @since 2.5
	 *
	 * @var string
	 */
	public $type = 'checkbox_and_select';

	/**
	 * Child inputs.
	 *
	 * @since 2.5
	 *
	 * @var Base[]
	 */
	public $inputs = array();

	/**
	 * Initialize Checbox and Select field.
	 *
	 * @since 2.5
	 *
	 * @param array                                $props    Field properties.
	 * @param \Rocketgenius\Gravity_Forms\Settings $settings Settings instance.
	 */
	public function __construct( $props, $settings ) {

		parent::__construct( $props, $settings );

		// Prepare Checkbox field.
		$checkbox_input = rgars( $props, 'checkbox' );
		$checkbox_field = array(
			'type'       => 'checkbox',
			'name'       => rgar( $props, 'name' ) . 'Enable',
			'label'      => esc_html__( 'Enable', 'gravityforms' ),
			'horizontal' => true,
			'value'      => '1',
			'choices'    => false,
			'tooltip'    => false,
		);
		$this->inputs['checkbox'] = wp_parse_args( $checkbox_input, $checkbox_field );

		// Prepare Select field.
		$select_input           = rgars( $props, 'select' );
		$select_field           = array(
			'name'    => rgar( $props, 'name' ) . 'Value',
			'type'    => 'select',
			'class'   => '',
			'tooltip' => false,
		);
		$select_field['class']  .= ' ' . $select_field['name'];
		$this->inputs['select'] = wp_parse_args( $select_input, $select_field );

		// Add on change event to Checkbox.
		if ( empty( $this->inputs['checkbox']['choices'] ) ) {
			$this->inputs['checkbox']['choices'] = array(
				array(
					'name'     => $this->inputs['checkbox']['name'],
					'label'    => $this->inputs['checkbox']['label'],
					'onchange' => sprintf(
						"( function( $, elem ) {
						$( elem ).parents( 'td' ).css( 'position', 'relative' );
						if( $( elem ).prop( 'checked' ) ) {
							$( '%1\$s' ).css( 'visibility', 'visible' );
							$( '%1\$s' ).fadeTo( 400, 1 );
						} else {
							$( '%1\$s' ).fadeTo( 400, 0, function(){
								$( '%1\$s' ).css( 'visibility', 'hidden' );   
							} );
						}
					} )( jQuery, this );",
						"#{$this->inputs['select']['name']}Span" ),
				),
			);
		}

		/**
		 * Prepare input fields.
		 *
		 * @var array $input
		 */
		foreach ( $this->inputs as &$input ) {
			$input = Fields::create( $input, $this->settings );
		}

	}





	// # RENDER METHODS ------------------------------------------------------------------------------------------------

	/**
	 * Render field.
	 *
	 * @since 2.5
	 *
	 * @return string
	 */
	public function markup() {

		// Prepare markup.
		// Display description.
		$html = $this->get_description();

		$html .= sprintf(
			'<span class="%s">%s <span id="%s" style="%s">%s %s</span></span>',
			esc_attr( $this->get_container_classes() ),
			$this->inputs['checkbox']->markup(),
			$this->inputs['select']->name . 'Span',
			$this->inputs['checkbox']->get_value() ? '' : 'visibility: hidden; opacity: 0;',
			$this->inputs['select']->markup(),
			$this->settings->maybe_get_tooltip( $this->inputs['select'] )
		);

		$html .= $this->get_error_icon();

		return $html;

	}





	// # VALIDATION METHODS --------------------------------------------------------------------------------------------

	/**
	 * Validate posted field value.
	 *
	 * @since 2.5
	 *
	 * @param array $values Posted field values.
	 */
	public function is_valid( $values ) {

		$this->inputs['checkbox']->is_valid( $values );
		$this->inputs['select']->is_valid( $values );

	}

}

Fields::register( 'checkbox_and_select', '\Rocketgenius\Gravity_Forms\Settings\Fields\Checkbox_and_Select' );

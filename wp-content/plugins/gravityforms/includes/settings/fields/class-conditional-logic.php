<?php

namespace Rocketgenius\Gravity_Forms\Settings\Fields;

use GFFormsModel;
use Rocketgenius\Gravity_Forms\Settings\Fields;

defined( 'ABSPATH' ) || die();

class Conditional_Logic extends Base {

	/**
	 * Field type.
	 *
	 * @since 2.5
	 *
	 * @var string
	 */
	public $type = 'conditional_logic';

	public $checkbox = array(
		'label'  => 'Enable Condition',
		'hidden' => false,
	);

	public $object_type;

	/**
	 * Initialize Conditional Logic field.
	 *
	 * @since 2.5
	 *
	 * @param array                                $props    Field properties.
	 * @param \Rocketgenius\Gravity_Forms\Settings $settings Settings instance.
	 */
	public function __construct( $props, $settings ) {

		parent::__construct( $props, $settings );

		// Populate default instructions.
		if ( $this->object_type === 'feed_condition' && ! rgobj( $this, 'instructions' ) ) {
			$this->instructions = esc_html__( 'Process this feed if', 'gravityforms' );
		}

		// Translate Checkbox label.
		if ( $this->checkbox['label'] === 'Enable Condition' ) {
			$this->checkbox['label'] = esc_html__( 'Enable Condition', 'gravityforms' );
		}

		// Initialize Checkbox field.
		$this->inputs['checkbox'] = Fields::create(
			array(
				'name'    => sprintf( '%s_conditional_logic', esc_attr( $this->object_type ) ),
				'type'    => 'checkbox',
				'hidden'  => $this->checkbox['hidden'],
				'choices' => array(
					array(
						'label' => $this->checkbox['label'],
						'name'  => sprintf( '%s_conditional_logic', esc_attr( $this->object_type ) ),
					),
				),
				'onclick' => sprintf( 'ToggleConditionalLogic( false, "%s" );', esc_attr( $this->object_type ) ),
			),
			$settings
		);

		// Initialize Hidden field.
		$this->inputs['hidden'] = Fields::create(
			array(
				'name'          => sprintf( '%s_conditional_logic_object', esc_attr( $this->object_type ) ),
				'type'          => 'hidden',
				'default_value' => $this->get_logic_object(),
			),
			$settings
		);

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

		// Display description.
		$html = $this->get_description();

		$html .= '<span class="' . esc_attr( $this->get_container_classes() ) . '">';

		$html .= $this->inputs['hidden']->markup();
		$html .= $this->inputs['checkbox']->markup();

		$html .= '</span>';

		$html .= sprintf(
			'<div id="%s_conditional_logic_container" class="gform-settings-field__conditional-logic">
					<!-- content dynamically created from form_admin.js -->
				</div>',
			$this->object_type
		);

		// Handle feed condition.
		if ( $this->object_type === 'feed_condition' ) {

			// Prepare JS parameters.
			$js_params = array(
				'strings'     => array( 'objectDescription' => esc_attr( $this->instructions ) ),
				'logicObject' => json_decode( $this->get_logic_object(), true ),
			);

			// Initialize.
			$html .= sprintf(
				'<script type="text/javascript">var feedCondition = new FeedConditionObj(%s);</script>',
				wp_json_encode( $js_params )
			);

		}

		$html .= $this->get_error_icon();

		return $html;

	}





	// # HELPER METHODS ------------------------------------------------------------------------------------------------

	/**
	 * Returns the current conditional logic object as a JSON string.
	 *
	 * @since 2.5
	 *
	 * @return string
	 */
	private function get_logic_object() {

		// Get logic object.
		$object = $this->settings->get_value( sprintf( '%s_conditional_logic_object', $this->object_type ) );

		// Trim values from existing object.
		if ( $object ) {

			// Handle feed condition.
			if ( $this->object_type === 'feed_condition' && rgar( $object, 'actionType' ) ) {
				$object = array( 'conditionalLogic' => $object );
			}

			$object = GFFormsModel::trim_conditional_logic_values_from_element( $object, $this->settings->get_current_form() );
			return json_encode( $object );

		} else {

			return '{}';

		}

	}

}

Fields::register( 'conditional_logic', '\Rocketgenius\Gravity_Forms\Settings\Fields\Conditional_Logic' );

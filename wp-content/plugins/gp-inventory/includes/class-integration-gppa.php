<?php

class GP_Inventory_Integration_GPPA {

	private static $instance = null;

	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	private function __construct() {
		// GF Addons init at 15
		add_action( 'init', array( $this, 'init' ), 16 );
	}

	public function init() {
		if ( ! is_callable( 'gp_populate_anything' ) ) {
			return;
		}

		/* Non-choice-based fields (value templates) */
		add_filter( 'gpi_inventory_limit_simple', array( $this, 'use_value_template_inv_limit' ), 10, 2 );
		add_filter( 'gpi_inventory_limit_advanced', array( $this, 'use_value_template_inv_limit' ), 10, 2 );

		/* Choice-based fields (choices templates) */
		add_filter( 'gppa_input_choice', array( $this, 'set_choice_inventory_limit' ), 10, 4 );
		add_filter( 'gpi_property_map_field_value', array( $this, 'property_map_use_populate_anything_values' ), 12, 2 );

		/* Admin */
		add_action( 'admin_print_footer_scripts', array( $this, 'output_editor_script' ) );
	}

	/**
	 * Use values from Populate Anything when getting the values for the property map. gp_populate_anything()->get_posted_field_values() is quite robust
	 * and gets everything from Save & Continue to hydrated values.
	 *
	 * This will improve compatibility with Populate Anything on initial load and also properly populate the property map values when a field is refreshed
	 * using gppa_get_batch_field_html.
	 *
	 * @param mixed    $value
	 * @param GF_Field $field
	 *
	 * @return array|string|null
	 */
	public function property_map_use_populate_anything_values( $value, $field ) {
		$values = gp_populate_anything()->get_posted_field_values( GFAPI::get_form( $field->formId ) );

		return rgar( $values, $field->id );
	}

	/**
	 * Set the inventory limit key on choices during hydration so it can be enforced later.
	 *
	 * @param array $choice
	 * @param GF_Field $field
	 * @param array $object
	 * @param array $objects
	 *
	 * @return array
	 */
	public function set_choice_inventory_limit( $choice, $field, $object, $objects ) {
		if ( $this->is_inventory_template_set( $field, 'choices' ) ) {
			$choice['inventory_limit'] = gp_populate_anything()->process_template( $field, 'inventory_limit', $object, 'choices', $objects );
		}

		return $choice;
	}

	/**
	 * Apply choice limits using the inventory_limit set by Populate Anything.
	 *
	 * @param array $choices
	 * @param GF_Field $field
	 *
	 * @return array
	 */
	public function apply_choice_limits( $choices, $field ) {
		if ( $this->is_inventory_template_set( $field, 'choices' ) && gp_inventory_type_choices()->is_applicable_field( $field ) ) {
			$choices = gp_inventory_type_choices()->apply_choice_limits( $choices, $field, GFAPI::get_form( $field->formId ) );
		}

		return $choices;
	}

	/**
	 * Apply value template inventory limits to non-choice-based fields.
	 *
	 * @param int $limit
	 * @param GF_Field $field
	 *
	 * @return int
	 */
	public function use_value_template_inv_limit( $limit, $field ) {
		if (
			$this->is_inventory_template_set( $field, 'values' )
			&& ( gp_inventory_type_simple()->is_applicable_field( $field ) || gp_inventory_type_advanced()->is_applicable_field( $field ) )
		) {
			$objects = gp_populate_anything()->get_field_objects( $field, rgar( $GLOBALS, 'gppa-field-values/' . $field->formId, array() ), 'values' );

			if ( ! empty( $objects ) ) {
				return gp_populate_anything()->process_template( $field, 'inventory_limit', $objects[0], 'values', $objects );
			}
		}

		return $limit;
	}

	public function output_editor_script() {
		if ( ! is_callable( 'GFCommon::is_form_editor' ) || ! GFCommon::is_form_editor() ) {
			return;
		}
		?>
		<script>
			window.gform.addFilter( 'gppa_template_rows', function ( templateRows, field, populate ) {
				if ( !window.field.gpiInventory ) {
					return templateRows;
				}

				/* Do not show Inv. Limit template for choice-based field value templates as the choices are what dictates the inventory limit. */
				if ( populate !== 'choices' && field.choices ) {
					return templateRows;
				}

				templateRows.push( {
					id: 'inventory_limit',
					label: 'Inv. Limit',
				} );

				return templateRows;
			} );
		</script>
		<?php
	}

	/**
	 * @param GF_Field $field
	 * @param 'choices'|'values' $populate
	 *
	 * @return number|null
	 */
	public function is_inventory_template_set( $field, $populate ) {
		return rgars( $field, 'gppa-' . $populate . '-templates/inventory_limit' );
	}
}

function gp_inventory_integration_gppa() {
	return GP_Inventory_Integration_GPPA::get_instance();
}

<?php

/**
 * Class CT_Ultimate_GDPR_Service_Formidable_Forms
 */
class CT_Ultimate_GDPR_Service_Formidable_Forms extends CT_Ultimate_GDPR_Service_Abstract {

	/**
	 * @return void
	 */
	public function init() {
		add_filter( 'ct_ultimate_gdpr_controller_plugins_compatible_formidable/formidable.php', '__return_true' );
		add_filter( 'ct_ultimate_gdpr_controller_plugins_collects_data_formidable/formidable.php', '__return_true' );
		add_filter( 'frm_entries_before_create', array( $this, 'add_form_errors' ), 100, 2 );
	}

	/**
	 * @return $this
	 */
	public function collect() {

		global $wpdb;

		/* meta table */

		$query = $wpdb->prepare( "
				SELECT im2.* FROM {$wpdb->prefix}frm_item_metas as im
				INNER JOIN {$wpdb->prefix}frm_item_metas as im2 ON im.item_id = im2.item_id
				WHERE im.meta_value = %s
			",
			$this->user->get_email()
		);

		$meta = $wpdb->get_results( $query, ARRAY_A );

		/* items table */

		$items = array();

		if ( $meta ) {

			$item_id = isset( $meta[0]['item_id'] ) ? $meta[0]['item_id'] : 0;

			$query = $wpdb->prepare( "
					SELECT * FROM {$wpdb->prefix}frm_items
					WHERE id = %d
				",
				$item_id
			);

			$items = $wpdb->get_results( $query, ARRAY_A );

		}

		/* results combined */
		$collected = $meta || $items ? compact( 'items', 'meta' ) : array();
		$this->set_collected( $collected );

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function get_name() {
		return apply_filters( "ct_ultimate_gdpr_service_{$this->get_id()}_name", 'Formidable Forms' );
	}

	/**
	 * @return bool
	 */
	public function is_active() {
		return function_exists( 'load_formidable_forms' );
	}

	/**
	 * @return bool
	 */
	public function is_forgettable() {
		return true;
	}

	/**
	 * @throws Exception
	 * @return void
	 */
	public function forget() {

		global $wpdb;
		$this->collect();

		if ( ! empty( $this->collected['items'] ) ) {

			foreach ( $this->collected['items'] as $item ) {

				$wpdb->delete(
					"{$wpdb->prefix}frm_item_metas",
					array( 'item_id' => $item['id'] )
				);

				$wpdb->delete(
					"{$wpdb->prefix}frm_items",
					array( 'id' => $item['id'] )
				);

			}

		}

	}

	/**
	 * @return mixed
	 */
	public function add_option_fields() {


		add_settings_section(

            "ct-ultimate-gdpr-services-{$this->get_id()}_accordion-{$this->get_id()}", // ID
			esc_html( $this->get_name() ), // Title
			null, // callback
			$this->front_controller->find_controller('services')->get_id() // Page
		);

		/*add_settings_field(
			"services_{$this->get_id()}_header", // ID
			$this->get_name(), // Title
			'__return_empty_string', // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			'ct-ultimate-gdpr-services-formidableforms_accordion-7'// Section
		);*/

        add_settings_field(
            "services_{$this->get_id()}_service_name", // ID
            sprintf( esc_html__( "[%s] Name", 'ct-ultimate-gdpr' ), $this->get_name() ), // Title
            array( $this, "render_name_field" ), // Callback
            $this->front_controller->find_controller('services')->get_id(), // Page
            "ct-ultimate-gdpr-services-{$this->get_id()}_accordion-{$this->get_id()}"
        );

		add_settings_field(
			"services_{$this->get_id()}_description", // ID
			sprintf( esc_html__( "[%s] Description", 'ct-ultimate-gdpr' ), $this->get_name() ), // Title
			array( $this, "render_description_field" ), // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
            "ct-ultimate-gdpr-services-{$this->get_id()}_accordion-{$this->get_id()}"
		);

		add_settings_field(
			"services_{$this->get_id()}_consent_field", // ID
			sprintf(
				esc_html__( "[%s] Inject consent checkbox to all forms", 'ct-ultimate-gdpr' ),
				$this->get_name()
			),
			array( $this, "render_field_services_{$this->get_id()}_consent_field" ), // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
            "ct-ultimate-gdpr-services-{$this->get_id()}_accordion-{$this->get_id()}"
		);

		add_settings_field(
			"services_{$this->get_id()}_consent_field_position_first", // ID
			esc_html__( '[Formidable Forms] Inject consent checkbox as the first field instead of the last', 'ct-ultimate-gdpr' ), // Title
			array( $this, "render_field_services_{$this->get_id()}_consent_field_position_first" ), // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
            "ct-ultimate-gdpr-services-{$this->get_id()}_accordion-{$this->get_id()}"
		);

	}

	/**
	 *
	 */
	public function render_field_services_formidable_forms_consent_field() {

		$admin = $this->get_admin_controller();

		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input class='ct-ultimate-gdpr-field' type='checkbox' id='%s' name='%s' %s />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value_escaped( $field_name ) ? 'checked' : ''
		);

	}

	/**
	 *
	 */
	public function render_field_services_formidable_forms_consent_field_position_first() {

		$admin = $this->get_admin_controller();

		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input class='ct-ultimate-gdpr-field' type='checkbox' id='%s' name='%s' %s />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value_escaped( $field_name ) ? 'checked' : ''
		);

	}

	/**
	 * @return mixed
	 */
	public function front_action() {
		add_filter( 'frm_get_paged_fields', array( $this, 'add_form_fields' ), 100, 3 );
	}

	/**
	 * @param $errors
	 * @param $form
	 *
	 * @return mixed
	 */
	public function add_form_errors( $errors, $form ) {

		$inject   = $this->get_admin_controller()->get_option_value( "services_{$this->get_id()}_consent_field", false, $this->front_controller->find_controller('services')->get_id() );
		$field_id = FRMField::get_id_by_key( __CLASS__.get_locale() );
		if ( ! $field_id || ! $inject ) {
			return $errors;
		}

		$consent_given = ! ! ct_ultimate_gdpr_get_value( $field_id, ct_ultimate_gdpr_get_value( 'item_meta', $_REQUEST, array() ) );

		if ( ! $consent_given ) {

			$errors[ "field" . $field_id ] = esc_html__( 'This field is required', 'ct-ultimate-gdpr' );
		}

		return $errors;

	}

	/**
	 * @param $original_fields
	 *
	 * @return mixed
	 */
	public function add_form_fields( $original_fields, $form_id, $error ) {

		$field_key      = __CLASS__ . get_locale();
		$fields         = $original_fields;
		$field_id       = FRMField::get_id_by_key( $field_key );
		$position_first = $this->get_admin_controller()->get_option_value( "services_{$this->get_id()}_consent_field_position_first", false, $this->front_controller->find_controller('services')->get_id() );
		$inject         = $this->get_admin_controller()->get_option_value( "services_{$this->get_id()}_consent_field", false, $this->front_controller->find_controller('services')->get_id() );

		if ( ! $field_id ) {

			$options = apply_filters( "ct_ultimate_gdpr_service_{$this->get_id()}_form_field_options", array(
				'field_key'     => $field_key,
				'name'          => esc_html__( 'Consent', 'ct-ultimate-gdpr' ),
				'description'   => '',
				'type'          => 'checkbox',
				'default_value' => '',
				'options'       =>
					array(
						0 =>
							array(
								'label' => ct_ultimate_gdpr_render_template( ct_ultimate_gdpr_locate_template( 'service/service-formidable-forms-consent-field', false ), false ),
								'value' => 'Option 1',
							),
					),
				'field_order'   => '1',
				'required'      => '1',
				'field_options' =>
					array(
						'size'               => '',
						'max'                => '',
						'label'              => '',
						'blank'              => esc_html__( 'This field is required', 'ct-ultimate-gdpr' ),
						'required_indicator' => '*',
						'invalid'            => '',
						'separate_value'     => 0,
						'clear_on_focus'     => 0,
						'default_blank'      => 0,
						'classes'            => '',
						'custom_html'        => '<div id="frm_field_[id]_container" class="frm_form_field form-field [required_class][error_class]">
    <label for="field_[key]" class="frm_primary_label">[field_name]
        <span class="frm_required">[required_label]</span>
    </label>
    <div class="frm_opt_container">[input]</div>
    [if description]<div class="frm_description" id="frm_desc_field_[key]">[description]</div>[/if description]
    [if error]<div class="frm_error">[error]</div>[/if error]
</div>',
						'minnum'             => 1,
						'maxnum'             => 10,
						'step'               => 1,
						'format'             => '',
						'align'              => 'block',
					),
			) );

			$field_id = FrmField::create( $options, true );

		}

		$consent_field = FrmField::getOne( $field_id );


		if ( $inject ) {

			if ( $position_first ) {
				array_unshift( $fields, $consent_field );
			} else {
				array_push( $fields, $consent_field );
			}
		}

		return apply_filters( "ct_ultimate_gdpr_service_{$this->get_id()}_form_content", $fields, $original_fields, $inject, $position_first );
	}

	/**
	 * @return string
	 */
	protected function get_default_description() {
		return esc_html__( 'Formidable Forms gathers data entered by users in forms', 'ct-ultimate-gdpr' );
	}
}
<?php

if ( ! class_exists( 'GP_Plugin' ) ) {
	return;
}

class GP_Populate_Anything extends GP_Plugin {

	private static $instance = null;

	/**
	 * @var null|GP_Populate_Anything_Live_Merge_Tags
	 */
	public $live_merge_tags = null;

	public $gf_merge_tags_cache = array();

	protected $_field_objects_cache = array();

	protected $_field_choices_cache = array();


	/**
	 * Marks which scripts/styles have been localized to avoid localizing multiple times with Gravity Forms' scripts
	 * 'callback' property.
	 *
	 * @var array
	 */
	protected $_localized = array();

	protected $_version      = GPPA_VERSION;
	protected $_path         = 'gp-populate-anything/gp-populate-anything.php';
	protected $_full_path    = __FILE__;
	protected $_object_types = array();
	protected $_slug         = 'gp-populate-anything';
	protected $_title        = 'Gravity Forms Populate Anything';
	protected $_short_title  = 'Populate Anything';

	private $_getting_current_entry = false;

	/* Used for storing and passing around the $field_values passed to gform_pre_render */
	private $_prepopulate_fields_values = array();

	/**
	 * @var array Hydrated fields cached _only_ during submission to reduce the time to submit.
	 */
	private $_hydrated_fields_on_submission_cache = array();

	public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function minimum_requirements() {
		return array(
			'gravityforms' => array(
				'version' => '2.3-rc-1',
			),
			'wordpress'    => array(
				'version' => '4.8',
			),
			'plugins'      => array(
				'gravityperks/gravityperks.php' => array(
					'name'    => 'Gravity Perks',
					'version' => '2.2.3',
				),
			),
		);
	}

	public function init() {

		parent::init();

		load_plugin_textdomain( 'gp-populate-anything', false, basename( dirname( __file__ ) ) . '/languages/' );

		/* Form Display */
		add_filter( 'gform_pre_render', array( $this, 'field_value_js' ) );
		add_filter( 'gform_pre_render', array( $this, 'posted_value_js' ) );
		add_filter( 'gform_pre_render', array( $this, 'field_value_object_js' ) );
		add_filter( 'gform_pre_render', array( $this, 'hydrate_initial_load' ), 8, 3 );

		add_filter( 'gform_pre_validation', array( $this, 'override_validation_for_populated_product_fields' ) );

		add_filter( 'gform_field_input', array( $this, 'field_input_add_empty_field_value_filter' ), 10, 5 );

		add_filter( 'gform_field_content', array( $this, 'field_content_disable_if_empty_field_values' ), 10, 2 );

		add_filter( 'gppa_get_batch_field_html', array( $this, 'field_content_disable_if_empty_field_values' ), 10, 2 );

		add_filter( 'gform_entry_field_value', array( $this, 'entry_field_value' ), 20, 4 );

		add_filter( 'gform_entries_field_value', array( $this, 'entries_field_value' ), 20, 4 );

		add_action( 'gform_entry_detail_content_before', array( $this, 'field_value_js' ) );
		add_action( 'gform_entry_detail_content_before', array( $this, 'field_value_object_js' ) );

		add_action( 'gform_pre_process', array( $this, 'hydrate_fields' ) );
		add_action( 'gform_pre_validation', array( $this, 'hydrate_fields' ) ); // Required for Gravity View's Edit Entry view.
		add_action( 'gform_pre_submission_filter', array( $this, 'hydrate_fields' ) );

		add_filter( 'gform_admin_pre_render', array( $this, 'modify_admin_field_choices' ) );
		add_filter( 'gform_admin_pre_render', array( $this, 'modify_admin_field_values' ) );

		/* Permissions */
		add_filter( 'gform_form_update_meta', array( $this, 'check_gppa_settings_for_user' ), 10, 3 );

		/* Template Replacement */
		add_filter( 'gppa_process_template', array( $this, 'maybe_convert_array_value_to_text' ), 9, 8 );
		add_filter( 'gppa_process_template', array( $this, 'replace_template_generic_gf_merge_tags' ), 15, 1 );
		add_filter( 'gppa_process_template', array( $this, 'replace_template_object_merge_tags' ), 10, 6 );
		add_filter( 'gppa_process_template', array( $this, 'replace_template_count_merge_tags' ), 10, 7 );
		add_filter( 'gppa_process_template', array( $this, 'maybe_add_currency_to_price' ), 10, 7 );

		add_filter( 'gppa_array_value_to_text', array( $this, 'use_commas_for_arrays' ), 10, 6 );
		add_filter( 'gppa_array_value_to_text', array( $this, 'prepare_gf_field_array_value_to_text' ), 10, 7 );

		/* Form Submission */
		add_action( 'gform_save_field_value', array( $this, 'maybe_save_choice_label' ), 10, 4 );

		/* Field Value Parsing */
		add_filter( 'gppa_modify_field_value_date', array( $this, 'modify_field_values_date' ), 10, 2 );
		add_filter( 'gppa_modify_field_value_time', array( $this, 'modify_field_values_time' ), 10, 2 );

		/* Field HTML when there are input field values */
		add_filter( 'gppa_field_html_empty_field_value_radio', array( $this, 'radio_field_html_empty_field_value' ) );

		/* Conditional Logic */
		add_filter( 'gform_field_filters', array( $this, 'conditional_logic_field_filters' ), 10, 2 );
		add_action( 'admin_footer', array( $this, 'conditional_logic_use_text_field' ) );

		/* Exporting */
		add_filter( 'gform_export_field_value', array( $this, 'hydrate_export_value' ), 10, 4 );

		/**
		 * Hydrate form before updating an entry. This is particularly helpful when the form contains a Checkbox field
		 * so that dynamically populated inputs are hydrated and will be saved.
		 */
		add_filter( 'gform_form_pre_update_entry', array( $this, 'hydrate_form' ), 10, 2 );

		/* Globals */
		if ( ! isset( $GLOBALS['gppa-field-values'] ) ) {
			$GLOBALS['gppa-field-values'] = array();
		}

		/* Live Merge Tags */
		$this->live_merge_tags = new GP_Populate_Anything_Live_Merge_Tags();

		/* Add default object types */
		$object_types = array(
			'post'     => 'GPPA_Object_Type_Post',
			'term'     => 'GPPA_Object_Type_Term',
			'user'     => 'GPPA_Object_Type_User',
			'gf_entry' => 'GPPA_Object_Type_GF_Entry',
			'database' => 'GPPA_Object_Type_Database',
		);

		/**
		 * Filter object types GPPA will populate from.
		 *
		 * @since 1.0-beta-4.104
		 *
		 * @param $object_types Array of GPPA object types indexed by type name.
		 *  default value: array( 'post'     => 'GPPA_Object_Type_Post',
		 *                        'term'     => 'GPPA_Object_Type_Term',
		 *                        'user'     => 'GPPA_Object_Type_User',
		 *                        'gf_entry' => 'GPPA_Object_Type_GF_Entry',
		 *                        'database' => 'GPPA_Object_Type_Database');
		 */
		$object_types = apply_filters( 'gppa_autoloaded_object_types', $object_types );
		foreach ( $object_types as $type => $class_name ) {
			$this->register_object_type( $type, $class_name );
		}

		$this->perk_compatibility();

		gppa_compatibility_gravityview();
		gppa_compatibility_gravityflow();
		gppa_compatibility_gravitypdf();

	}

	public function pre_init() {
		parent::pre_init();

		// Must happen on pre_init to intercept the 'gform_export_form' filter.
		gppa_export();
	}

	/**
	 * Add necessary hooks to ensure compatibility with other Gravity Perks
	 */
	public function perk_compatibility() {
		/**
		 * GP Nested Forms
		 *
		 * Hydrate fields any time the nested form is fetched. One key example of this is ensuring that the label of
		 * a choice-based field is reflected in the merge value rather than the value of the option itself.
		 */
		add_action( 'gpnf_get_nested_form', array( $this, 'hydrate_fields' ) );
	}

	/**
	 * Some field types such as time handle the value as a single value rather than a value for each input.
	 * GPPA needs to know what field types behave this way so it treats the value templates correctly.
	 *
	 * @return array
	 */
	public static function get_interpreted_multi_input_field_types() {
		return apply_filters(
			'gppa_interpreted_multi_input_field_types',
			array(
				'time',
				'date',
			)
		);
	}

	/**
	 * Much like the interpreted multi input fields above, some fields such as checkboxes and multiselect need to have
	 * their value handled as a singular array value rather than a value for each input (AKA choice).
	 *
	 * @see GP_Populate_Anything::get_interpreted_multi_input_field_types()
	 *
	 * @return array
	 */
	public static function get_multi_selectable_choice_field_types() {
		return apply_filters(
			'gppa_multi_selectable_choice_field_types',
			array(
				'multiselect',
				'checkbox',
			)
		);
	}

	public function init_admin() {

		parent::init_admin();

		/* Form Editor */
		add_action( 'gform_field_standard_settings_75', array( $this, 'field_standard_settings' ) );

		/* We don't change field values in admin since it can cause the value to be saved as the defaultValue setting */

		add_filter( 'gform_field_css_class', array( $this, 'add_enabled_field_class' ), 10, 3 );

	}

	public function init_ajax() {

		parent::init_ajax();

		/* Privileged */
		add_action( 'wp_ajax_gppa_get_object_type_properties', array( $this, 'ajax_get_object_type_properties' ) );
		add_action( 'wp_ajax_gppa_get_property_values', array( $this, 'ajax_get_property_values' ) );
		add_action( 'wp_ajax_gppa_get_batch_field_html', array( $this, 'ajax_get_batch_field_html' ) );
		add_action( 'wp_ajax_gppa_get_query_results', array( $this, 'ajax_get_query_results' ) );

		/* Un-Privileged */
		add_action( 'wp_ajax_nopriv_gppa_get_batch_field_html', array( $this, 'ajax_get_batch_field_html' ) );

	}

	public function scripts() {

		$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG || isset( $_GET['gform_debug'] ) ? '' : '.min';

		$scripts = array(
			array(
				'handle'    => 'gp-populate-anything-admin',
				'src'       => $this->get_base_url() . '/js/built/gp-populate-anything-admin.js',
				'version'   => $this->_version,
				'deps'      => array( 'jquery' ),
				'in_footer' => true,
				'enqueue'   => array(
					array( 'admin_page' => array( 'form_editor' ) ),
				),
				'callback'  => array( $this, 'localize_admin_scripts' ),
			),
			array(
				'handle'    => 'gp-populate-anything',
				'src'       => $this->get_base_url() . '/js/built/gp-populate-anything.js',
				'version'   => $this->_version,
				'deps'      => array( 'gform_gravityforms', 'jquery' ),
				'in_footer' => true,
				'enqueue'   => array(
					array( $this, 'should_enqueue_frontend_scripts' ),
				),
				'callback'  => array( $this, 'localize_frontend_scripts' ),
			),
		);

		return array_merge( parent::scripts(), $scripts );

	}

	/**
	 * @param $form array
	 * @return bool
	 */
	public function should_enqueue_frontend_scripts( $form ) {
		/* form_has_lmts() is dependent on the LMT whitelist being populated. */
		$this->live_merge_tags->populate_lmt_whitelist( $form );

		return (
			$this->form_has_dynamic_population( $form )
			|| $this->live_merge_tags->form_has_lmts( rgar( $form, 'id' ) )
		);
	}

	public function styles() {

		$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG || isset( $_GET['gform_debug'] ) ? '' : '.min';

		$styles = array(
			array(
				'handle'  => 'gp-populate-anything-admin',
				'src'     => $this->get_base_url() . "/styles/gp-populate-anything-admin{$min}.css",
				'version' => $this->_version,
				'enqueue' => array(
					array( 'admin_page' => array( 'form_editor' ) ),
				),
			),
			array(
				'handle'  => 'gp-populate-anything',
				'src'     => $this->get_base_url() . "/styles/gp-populate-anything{$min}.css",
				'version' => $this->_version,
				'enqueue' => array(
					array( $this, 'should_enqueue_frontend_scripts' ),
				),
			),
		);

		return array_merge( parent::styles(), $styles );

	}

	public function is_localized( $item ) {
		return in_array( $item, $this->_localized );
	}

	public function localize_admin_scripts() {

		if ( $this->is_localized( 'admin-scripts' ) ) {
			return;
		}

		$gppa_object_types = array();

		foreach ( $this->get_object_types() as $object_type_id => $object_type_instance ) {
			$gppa_object_types[ $object_type_id ] = $object_type_instance->to_simple_array();
		}

		wp_localize_script(
			'gp-populate-anything-admin',
			'GPPA_ADMIN',
			array(
				'objectTypes'                     => $gppa_object_types,
				'strings'                         => $this->get_js_strings(),
				'defaultOperators'                => $this->get_default_operators(),
				'interpretedMultiInputFieldTypes' => self::get_interpreted_multi_input_field_types(),
				'multiSelectableChoiceFieldTypes' => self::get_multi_selectable_choice_field_types(),
				'gfBaseUrl'                       => GFCommon::get_base_url(),
				'nonce'                           => wp_create_nonce( 'gppa' ),
				'isSuperAdmin'                    => is_super_admin(),
			)
		);

		$this->_localized[] = 'admin-scripts';

	}

	public function get_default_operators() {
		/**
		 * Filter the default operators for ALL properties.
		 *
		 * Note: this will impact the UI only, additional logic will be required when adding new operators such as
		 * extending Object Types to know how to query using the added operator.
		 *
		 * @since 1.0-beta-4.91
		 *
		 * @param string[] $operators The default operators for ALL properties.
		 */
		return apply_filters(
			'gppa_default_operators',
			array(
				'is',
				'isnot',
				'>',
				'>=',
				'<',
				'<=',
				'contains',
				'starts_with',
				'ends_with',
				'like',
			)
		);
	}

	public function localize_frontend_scripts() {

		/**
		 * If a script is enqueued in the footer with in_footer, this script will
		 * be called multiple times and we need to guard against localizing multiple times.
		 */
		if ( $this->is_localized( 'frontend-scripts' ) ) {
			return;
		}

		wp_localize_script( 'gp-populate-anything', 'GPPA', array(
			'AJAXURL'    => admin_url( 'admin-ajax.php', null ),
			'GF_BASEURL' => GFCommon::get_base_url(),
			'NONCE'      => wp_create_nonce( 'gppa' ),
			'I18N'       => $this->get_js_strings(),
		) );

		$this->_localized[] = 'frontend-scripts';

	}

	public function get_js_strings() {

		return apply_filters(
			'gppa_strings',
			array(
				'populateChoices'                   => esc_html__( 'Populate choices dynamically', 'gp-populate-anything' ),
				'populateValues'                    => esc_html__( 'Populate value dynamically', 'gp-populate-anything' ),
				'addFilter'                         => esc_html__( 'Add Filter', 'gp-populate-anything' ),
				'label'                             => esc_html__( 'Label', 'gp-populate-anything' ),
				'value'                             => esc_html__( 'Value', 'gp-populate-anything' ),
				'price'                             => esc_html__( 'Price', 'gp-populate-anything' ),
				'loadingEllipsis'                   => esc_html__( 'Loading...', 'gp-populate-anything' ),
				/**
				 * Using HTML entity (&#9998;) does not work with esc_html__ so the pencil has been pasted in directly.
				 */
				'addCustomValue'                    => esc_html__( '✎ Custom Value', 'gp-populate-anything' ),
				'standardValues'                    => esc_html__( 'Standard Values', 'gp-populate-anything' ),
				'formFieldValues'                   => esc_html__( 'Form Field Values', 'gp-populate-anything' ),
				'specialValues'                     => esc_html__( 'Special Values', 'gp-populate-anything' ),
				'valueBoolTrue'                     => esc_html__( '(boolean) true', 'gp-populate-anything' ),
				'valueBoolFalse'                    => esc_html__( '(boolean) false', 'gp-populate-anything' ),
				'valueNull'                         => esc_html__( '(null) NULL', 'gp-populate-anything' ),
				'selectAnItem'                      => esc_html__( 'Select a %s', 'gp-populate-anything' ),
				'unique'                            => esc_html__( 'Only Show Unique Results', 'gp-populate-anything' ),
				'reset'                             => esc_html__( 'Reset', 'gp-populate-anything' ),
				'type'                              => esc_html__( 'Type', 'gp-populate-anything' ),
				'objectType'                        => esc_html__( 'Object Type', 'gp-populate-anything' ),
				'filters'                           => esc_html__( 'Filters', 'gp-populate-anything' ),
				'ordering'                          => esc_html__( 'Ordering', 'gp-populate-anything' ),
				'ascending'                         => esc_html__( 'Ascending', 'gp-populate-anything' ),
				'descending'                        => esc_html__( 'Descending', 'gp-populate-anything' ),
				'random'                            => esc_html__( 'Random', 'gp-populate-anything' ),
				'choiceTemplate'                    => esc_html__( 'Choice Template', 'gp-populate-anything' ),
				'valueTemplates'                    => esc_html__( 'Value Templates', 'gp-populate-anything' ),
				'operators'                         => array(
					'is'          => __( 'is', 'gp-populate-anything' ),
					'isnot'       => __( 'is not', 'gp-populate-anything' ),
					'>'           => __( '>', 'gp-populate-anything' ),
					'>='          => __( '>=', 'gp-populate-anything' ),
					'<'           => __( '<', 'gp-populate-anything' ),
					'<='          => __( '<=', 'gp-populate-anything' ),
					'contains'    => __( 'contains', 'gp-populate-anything' ),
					'starts_with' => __( 'starts with', 'gp-populate-anything' ),
					'ends_with'   => __( 'ends with', 'gp-populate-anything' ),
					'like'        => __( 'is LIKE', 'gp-populate-anything' ),
					'is_in'       => __( 'is in', 'gp-populate-anything' ),
					'is_not_in'   => __( 'is not in', 'gp-populate-anything' ),
				),
				'chosen_no_results'                 => esc_attr( gf_apply_filters( array( 'gform_dropdown_no_results_text', 0 ), __( 'No results matched', 'gp-populate-anything' ), 0 ) ),
				'restrictedObjectTypeNonPrivileged' => esc_html__( 'This field is configured to an object type for which you do not have permission to edit.', 'gp-populate-anything' ),
				'restrictedObjectTypePrivileged'    => esc_html__( 'The selected Object Type is restricted. Non-super admins will not be able to edit this field\'s GPPA settings.', 'gp-populate-anything' ),
				'tooManyPropertyValues'             => esc_html__( 'Too many values to display.', 'gp-populate-anything' ),
			)
		);

	}

	public function register_object_type( $id, $class ) {
		$this->_object_types[ $id ] = new $class( $id );
	}

	public function get_object_type( $id, $field = null ) {
		$id_parts = explode( ':', $id );

		if ( $id_parts[0] === 'field_value_object' && $field ) {
			$field = GFFormsModel::get_field( $field['formId'], $id_parts[1] );

			return $this->get_object_type( rgar( $field, 'gppa-choices-object-type' ), $field );
		}

		return rgar( $this->_object_types, $id );
	}

	public function get_object_types() {
		return apply_filters( 'gppa_object_types', $this->_object_types );
	}

	public function get_primary_property( $field, $populate ) {

		$object_type_id = rgar( $field, 'gppa-' . $populate . '-object-type' );
		$id_parts       = explode( ':', $object_type_id );

		if ( $id_parts[0] === 'field_value_object' && $field ) {
			$field = GFFormsModel::get_field( $field['formId'], $id_parts[1] );

			// This assumes that only choice-populated fields can be Field Value Objects.
			return $this->get_primary_property( $field, 'choices' );
		}

		$primary_property = rgar( $field, 'gppa-' . $populate . '-primary-property' );

		return $primary_property;
	}

	/**
	 * @param $object_type_instance GPPA_Object_Type
	 * @param $field GF_Field
	 *
	 * @return mixed
	 */
	public function get_query_limit( $object_type_instance, $field ) {
		return gf_apply_filters(
			array( 'gppa_query_limit', rgar( $field, 'formId' ), rgar( $field, 'id' ) ),
			501,
			$object_type_instance,
			$field
		);
	}

	/* Form Display */
	public function field_value_js( $form ) {

		if ( ! is_array( $form ) && GFCommon::is_form_editor() ) {
			return $form;
		}

		$form_fields          = rgar( $form, 'fields', array() );
		$has_gppa_field_value = false;
		$gppa_field_value_map = array( $form['id'] => array() );

		foreach ( $form_fields as $field ) {
			if ( ! $this->is_field_dynamically_populated( $field ) ) {
				continue;
			}

			$filter_groups = array_merge( rgar( $field, 'gppa-choices-filter-groups', array() ), rgar( $field, 'gppa-values-filter-groups', array() ) );

			if ( ! is_array( $filter_groups ) || ! count( $filter_groups ) ) {
				continue;
			}

			foreach ( $filter_groups as $filter_group ) {
				foreach ( $filter_group as $filter ) {
					$filter_value_exploded = explode( ':', $filter['value'] );
					$dependent_fields      = array();

					if ( $filter_value_exploded[0] === 'gf_field' ) {
						$dependent_fields[] = $filter_value_exploded[1];
					} elseif ( preg_match_all( '/{\w+:gf_field_(\d+)}/', $filter['value'], $field_matches ) ) {
						if ( count( $field_matches ) && ! empty( $field_matches[1] ) ) {
							$dependent_fields = $field_matches[1];
						}
					}

					if ( empty( $dependent_fields ) ) {
						continue;
					}

					$has_gppa_field_value = true;

					if ( ! isset( $gppa_field_value_map[ $form['id'] ][ $field->id ] ) ) {
						$gppa_field_value_map[ $form['id'] ][ $field->id ] = array();
					}

					foreach ( $dependent_fields as $dependent_field_id ) {
						$gppa_field_value_map[ $form['id'] ][ $field->id ][] = array(
							'gf_field' => $dependent_field_id,
							'property' => $filter['property'],
							'operator' => $filter['operator'],
						);
					}
				}
			}
		}

		if ( $has_gppa_field_value ) {

			$this->enqueue_scripts( $form );
			wp_localize_script( 'gp-populate-anything', "GPPA_FILTER_FIELD_MAP_{$form['id']}", $gppa_field_value_map );

		}

		return $form;

	}

	public function posted_value_js( $form ) {

		if ( ! rgar( $_POST, 'gform_submit' ) || ! is_array( $form ) ) {
			return $form;
		}

		$posted_values = array();

		foreach ( $_POST as $input_name => $input_value ) {
			$input_name = str_replace( '_', '.', str_replace( 'input_', '', $input_name ) );
			$field_id   = absint( $input_name );

			if ( ! $input_name ) {
				continue;
			}

			$field = GFFormsModel::get_field( $form, $field_id );

			if ( ! $this->is_field_dynamically_populated( $field ) ) {
				continue;
			}

			$posted_values[ $input_name ] = $input_value;
		}

		if ( ! count( $posted_values ) ) {
			return $form;
		}

		wp_localize_script( 'gp-populate-anything', "GPPA_POSTED_VALUES_{$form['id']}", $posted_values );

		return $form;

	}

	public function field_value_object_js( $form ) {

		if ( GFCommon::is_form_editor() || ! is_array( $form ) ) {
			return $form;
		}

		$form_fields            = rgar( $form, 'fields', array() );
		$has_field_value_object = false;
		$field_value_object_map = array( $form['id'] => array() );

		foreach ( $form_fields as $field ) {
			if ( ! rgar( $field, 'gppa-values-enabled' ) || strpos( rgar( $field, 'gppa-values-object-type' ), 'field_value_object' ) !== 0 ) {
				continue;
			}

			$object_type_exploded   = explode( ':', rgar( $field, 'gppa-values-object-type' ) );
			$has_field_value_object = true;

			if ( ! isset( $field_value_object_map[ $form['id'] ][ $field->id ] ) ) {
				$field_value_object_map[ $form['id'] ][ $field->id ] = array();
			}

			$field_value_object_map[ $form['id'] ][ $field->id ][] = array(
				'gf_field' => $object_type_exploded[1],
			);
		}

		if ( $has_field_value_object ) {

			$this->enqueue_scripts( $form );
			wp_localize_script( 'gp-populate-anything', "GPPA_FIELD_VALUE_OBJECT_MAP_{$form['id']}", $field_value_object_map );

		}

		return $form;

	}

	public function get_field_objects( $field, $field_values, $populate ) {

		$gppa_prefix          = 'gppa-' . $populate . '-';
		$templates            = rgar( $field, $gppa_prefix . 'templates' );
		$object_type          = rgar( $field, $gppa_prefix . 'object-type' );
		$unique               = rgar( $field, $gppa_prefix . 'unique-results' );
		$object_type_instance = rgar( $this->_object_types, $object_type );

		if ( $unique === null || $unique === '' ) {
			$unique = true;
		}

		if ( ! $object_type_instance ) {
			return array();
		}

		$args = array(
			'filter_groups'          => rgar( $field, $gppa_prefix . 'filter-groups' ),
			'ordering'               => array(
				'orderby' => rgar( $field, $gppa_prefix . 'ordering-property' ),
				'order'   => rgar( $field, $gppa_prefix . 'ordering-method' ),
			),
			'templates'              => $templates,
			'primary_property_value' => rgar( $field, $gppa_prefix . 'primary-property' ),
			'field_values'           => $field_values,
			'field'                  => $field,
			'unique'                 => $unique,
		);

		/**
		 * Store results in query cache before making them unique and once after.
		 * This ensures that identical unique queries that target different fields in their
		 * templates do not interfere with one another.
		 *
		 * This may end up using more memory but will ensure that we're always returning the most
		 * accurate results while utilizing caching for performance.
		 */
		$query_cache_hash      = $object_type_instance->query_cache_hash( $args );
		$unique_cache_hash     = ( $query_cache_hash ) ? sha1( $query_cache_hash . $args['field']->id ) : null;
		$return_unique_results = gf_apply_filters( array( "gppa_object_type_{$object_type}_unique", $field['formId'], $field['id'] ), $unique );

		if ( $return_unique_results ) {
			if ( isset( $this->_field_objects_cache[ $unique_cache_hash ] ) ) {
				// Return unique cached results if found
				return $this->_field_objects_cache[ $unique_cache_hash ];
			} elseif ( $query_cache_hash && isset( $this->_field_objects_cache[ $query_cache_hash ] ) ) {
				// Otherwise check full cached results before making them unique to avoid a second query()
				$results = $this->_field_objects_cache[ $query_cache_hash ];
			} else {
				// If all fails, perform the query and cache it
				$results = $object_type_instance->query( $args, $field );
				if ( $query_cache_hash ) {
					// Store all results in cache
					$this->_field_objects_cache[ $query_cache_hash ] = $results;
				}
			}

			// Make results unique
			$results = $this->make_results_unique( $results, $field, $templates, $populate );

			if ( $unique_cache_hash ) {
				// Store unique results in cache
				$this->_field_objects_cache[ $unique_cache_hash ] = $results;
			}
		} else {
			// None unique query
			if ( $query_cache_hash && isset( $this->_field_objects_cache[ $query_cache_hash ] ) ) {
				// Return cached results if found
				return $this->_field_objects_cache[ $query_cache_hash ];
			}
			$results = $object_type_instance->query( $args, $field );
			if ( $query_cache_hash ) {
				$this->_field_objects_cache[ $query_cache_hash ] = $results;
			}
		}

		return $results;

	}

	public function make_results_unique( $results, $field, $templates, $populate ) {

		$unique_results = array();
		$added_values   = array();
		$template       = ! empty( $templates['label'] ) ? 'label' : 'value';

		foreach ( $results as $result ) {

			$result_template_value = $this->process_template( $field, $template, $result, $populate, $results );

			if ( array_search( $result_template_value, $added_values ) !== false ) {
				continue;
			}

			$added_values[]   = $result_template_value;
			$unique_results[] = $result;

		}

		return $unique_results;

	}

	public function process_template( $field, $template_name, $object, $populate, $objects ) {

		static $_cache;

		$object_type_id   = rgar( $field, 'gppa-' . $populate . '-object-type' );
		$object_type      = $this->get_object_type( $object_type_id, $field );
		$templates        = rgar( $field, 'gppa-' . $populate . '-templates', array() );
		$primary_property = $this->get_primary_property( $field, $populate );
		$template         = rgar( $templates, $template_name );

		$cache_key = serialize(
			array(
				$template,
				$object_type->get_object_id( $object, $primary_property ),
				rgar( $field, 'id' ),
			)
		);

		if ( isset( $_cache[ $cache_key ] ) ) {
			return $_cache[ $cache_key ];
		}

		if ( strpos( $template, 'gf_custom' ) === 0 ) {

			$template_value = $this->extract_custom_value( $template );

			if ( empty( $template_value ) ) {
				return null;
			}

			$_cache[ $cache_key ] = gf_apply_filters(
				array(
					'gppa_process_template',
					$template_name,
				),
				$template_value,
				$field,
				$template_name,
				$populate,
				$object,
				$object_type,
				$objects,
				$template
			);

			return $_cache[ $cache_key ];
		}

		if ( ! $template ) {
			return null;
		}

		$value = $object_type->get_object_prop_value( $object, $template );

		try {
			$_cache[ $cache_key ] = gf_apply_filters(
				array(
					'gppa_process_template',
					$template_name,
				),
				$value,
				$field,
				$template_name,
				$populate,
				$object,
				$object_type,
				$objects,
				$template
			);

			return $_cache[ $cache_key ];
		} catch ( Exception $e ) {
			return null;
		}

	}

	public function replace_template_count_merge_tags( $template_value, $field, $template, $populate, $object, $object_type, $objects ) {

		return str_replace( '{count}', count( $objects ), $template_value );

	}

	public function maybe_convert_array_value_to_text( $template_value, $field, $template_name, $populate, $object, $object_type, $objects, $template ) {

		/**
		 * We only want to convert away from JSON/array if the current field can not display the data in a way that makes
		 * sense to the user.
		 *
		 * Without the conditional below, checkboxes and multi-selects may not repopulate correctly.
		 */
		if ( ( ( isset( $field->choices ) && is_array( $field->choices ) ) || rgar( $field, 'storageType' ) === 'json' ) && $populate === 'values' ) {
			return $template_value;
		}

		if ( self::is_json( $template_value ) ) {
			return apply_filters( 'gppa_array_value_to_text', $template_value, json_decode( $template_value, ARRAY_A ), $field, $object, $object_type, $objects, $template );
		}

		if ( is_array( $template_value ) ) {
			return apply_filters( 'gppa_array_value_to_text', $template_value, $template_value, $field, $object, $object_type, $objects, $template );
		}

		return $template_value;

	}

	/**
	 * Default callback to use for gppa_array_value_to_text filter.
	 *
	 * @param $text_value string
	 * @param $array_value array
	 * @param $field
	 * @param $object
	 * @param $object_type
	 * @param $objects
	 *
	 * @return string
	 */
	public function use_commas_for_arrays( $text_value, $array_value, $field, $object, $object_type, $objects ) {
		return implode( ', ', $array_value );
	}

	public function prepare_gf_field_array_value_to_text( $text_value, $array_value, $field, $object, $object_type, $objects, $template ) {

		if ( ! $object_type || $object_type->id !== 'gf_entry' ) {
			return $text_value;
		}

		$field = GFAPI::get_field( $object->form_id, str_replace( 'gf_field_', '', $template ) );

		$value_export = $field ? $field->get_value_export( $array_value ) : '';

		if ( $value_export ) {
			$text_value = $value_export;
		}

		return apply_filters( 'gppa_prepare_gf_field_array_value_to_text', $text_value, $array_value, $field, $object, $object_type, $template );

	}

	public function replace_template_object_merge_tags( $template_value, $field, $template, $populate, $object, $object_type ) {

		if ( ! is_string( $template_value ) ) {
			return $template_value;
		}

		$pattern = sprintf( '/{(%s):(.+?)(:(.+))?}/', implode( '|', array( 'object', 'post', 'user', 'gf_entry', 'database', 'term' ) ) );

		preg_match_all( $pattern, $template_value, $matches, PREG_SET_ORDER );
		foreach ( $matches as $match ) {
			list( $search, $tag, $prop, , $modifier ) = array_pad( $match, 5, null );

			$replace = $object_type->get_object_prop_value( $object, $prop );
			$replace = apply_filters( 'gppa_object_merge_tag_replacement_value', $replace, $object, $match );

			/**
			 * Allow fetching specific keys in an associative array using a merge tag in a Choice/Value template.
			 *
			 * @example {post:meta_example:key}
			 */
			if ( $modifier ) {
				/**
				 * PHP serialized data in meta will already be deserialized but JSON data will still need to be decoded
				 * at this point.
				 */
				$replace = self::maybe_decode_json( $replace );

				$replace = rgars( $replace, implode( '/', explode( ':', $modifier ) ) );
			}

			if ( is_array( $replace ) ) {
				$replace = $this->maybe_convert_array_value_to_text(
					$replace,
					$field,
					null,
					$populate,
					$object,
					$object_type,
					array( $object ),
					$template
				);

				$template_value = str_replace( $search, $replace, $template_value );
			} else {
				$template_value = str_replace( $search, $replace, $template_value );
			}
		}

		return $template_value;

	}

	/**
	 * Replace generic merge tags from Gravity Forms that don't require an entry
	 *
	 * @param $template_value
	 *
	 * @return mixed|void
	 */
	public function replace_template_generic_gf_merge_tags( $template_value ) {

		if ( ! is_string( $template_value ) ) {
			return $template_value;
		}

		if ( isset( $this->gf_merge_tags_cache[ $template_value ] ) ) {
			return $this->gf_merge_tags_cache[ $template_value ];
		}

		$result = GFCommon::replace_variables_prepopulate( $template_value, false, false, true );

		$this->gf_merge_tags_cache[ $template_value ] = $result;

		return $result;

	}

	/**
	 * GF 2.5 does not run GFCommon::to_money() on the frontend so we need to convert product field's prices to
	 * be formatted numbers with currency.
	 */
	public function maybe_add_currency_to_price( $template_value, $field, $template, $populate, $object, $object_type, $objects ) {
		if ( rgar( $field, 'type' ) !== 'product' ) {
			return $template_value;
		}

		$template_exploded = explode( '.', $template );

		/**
		 * Price input should be .2
		 */
		if ( rgar( $template_exploded, 1 ) == 2 ) {
			return GFCommon::to_money( $template_value );
		}

		return $template_value;
	}

	public function get_dependent_fields_by_filter_group( $field, $populate ) {

		$gppa_prefix = 'gppa-' . $populate . '-';

		$filter_groups    = rgar( $field, $gppa_prefix . 'filter-groups' );
		$dependent_fields = array();

		if ( ! rgar( $field, $gppa_prefix . 'enabled' ) || ! $filter_groups ) {
			return $dependent_fields;
		}

		foreach ( $filter_groups as $filter_group_index => $filters ) {
			$dependent_fields[ $filter_group_index ] = array();

			foreach ( $filters as $filter ) {
				$filter_value = rgar( $filter, 'value' );

				if ( preg_match_all( '/{\w+:gf_field_(\d+)}/', $filter_value, $field_matches ) ) {
					if ( count( $field_matches ) && ! empty( $field_matches[1] ) ) {
						$dependent_fields[ $filter_group_index ] = array_merge( $dependent_fields[ $filter_group_index ], $field_matches[1] );
					}
				} elseif ( strpos( $filter_value, 'gf_field:' ) === 0 ) {
					$dependent_fields[ $filter_group_index ][] = str_replace( 'gf_field:', '', $filter_value );
				}
			}

			if ( ! count( $dependent_fields[ $filter_group_index ] ) ) {
				unset( $dependent_fields[ $filter_group_index ] );
			}
		}

		return $dependent_fields;

	}

	public function has_empty_field_value( $field, $populate, $entry = false ) {

		$form = GFAPI::get_form( $field->formId );
		if ( ! $form ) {
			return false;
		}

		$field_values              = $entry ? $entry : $this->get_posted_field_values( $form );
		$dependent_fields_by_group = $this->get_dependent_fields_by_filter_group( $field, $populate );

		if ( count( $dependent_fields_by_group ) === 0 ) {
			return false;
		}

		foreach ( $dependent_fields_by_group as $dependent_field_group_index => $dependent_field_ids ) {
			$group_requirements_met = true;

			foreach ( $dependent_field_ids as $dependent_field_id ) {
				if ( ! $this->has_field_value( $dependent_field_id, $field_values ) ) {
					$group_requirements_met = false;

					break;
				}
			}

			if ( $group_requirements_met ) {
				return false;
			}
		}

		return true;

	}

	public function has_field_value( $field_id, $field_values ) {
		return ! $this->is_empty( $this->get_field_value_from_field_values( $field_id, $field_values ) );
	}

	/**
	 * Get the value of a given field from the passed array of field values.
	 *
	 * Multi-input fields store each inputs value in a decimal format (e.g. 1.1, 1.2). If the target field is 1, we
	 * should return all 1.x values.
	 *
	 * @param array $field_id
	 * @param array $field_values
	 *
	 * @return bool|string
	 */
	public function get_field_value_from_field_values( $field_id, $field_values ) {

		$is_input_specific = (int) $field_id != $field_id;
		$value             = '';

		// Return input-specific values without any fanfare (e.g. 1.2).
		if ( $is_input_specific ) {
			return rgar( $field_values, $field_id, null );
		}

		// If the target field ID is for a multi-input field (e.g. Checkbox), we want to get all input values for this field.
		foreach ( $field_values as $input_id => $field_value ) {

			$input_field_id = (int) $input_id;

			if ( $input_field_id == $field_id ) {
				// If input field ID does not match the input ID, we know that the current value is for a specific-input.
				// Let's collect all input values as an array.
				if ( $input_field_id != $input_id ) {
					if ( ! is_array( $value ) ) {
						$value = array();
					}
					$value[] = $field_value;
					// Otherwise, we are targeting a single-input field's value. There should only be one field value so we can break the loop.
				} else {
					$value = $field_value;
					break;
				}
			}
		}

		return $value;
	}

	public function extract_custom_value( $value ) {
		return preg_replace( '/^gf_custom:?/', '', $value );
	}

	/**
	 * @param $value
	 *
	 * empty can't be used on its own because it's a language construct
	 *
	 * @return bool
	 */
	public function is_empty( $value ) {
		return empty( $value ) && $value !== 0 && $value !== '0';
	}

	public function get_input_choices( $field, $field_values = null, $include_object = true ) {

		$templates = rgar( $field, 'gppa-choices-templates', array() );

		$cache_key = $field['formId'] . '-' . $field['id'] . serialize( $field_values );

		if ( isset( $this->_field_choices_cache[ $cache_key ] ) ) {
			return $this->_field_choices_cache[ $cache_key ];
		}

		if ( ! rgar( $field, 'gppa-choices-enabled' ) || ! rgar( $field, 'gppa-choices-object-type' ) || ! rgar( $templates, 'label' ) || ! rgar( $templates, 'value' ) ) {
			return $field->choices;
		}

		/* Force field to use both value and text */
		$field->enableChoiceValue = true;

		if ( $this->has_empty_field_value( $field, 'choices', $field_values ) ) {
			// This seems to break placeholders when the source is CPT-UI
			// Yet doesn't seem to affect GPPA. Leaving code in for posterity.
			//$field->placeholder = null;

			return array(
				array(
					// Unchecked checkboxes need to have a non-empty value otherwise they will automatically be checked by GF.
					'value'           => apply_filters( 'gppa_missing_filter_value', $field->get_input_type() === 'checkbox', $field ),
					'text'            => apply_filters( 'gppa_missing_filter_text', '&ndash; ' . esc_html__( 'Fill Out Other Fields', 'gp-populate-anything' ) . ' &ndash;', $field ),
					/*
					 * We only want our instructive text to be selected for Drop Downs. This bit below is necessary because
					 * Product Drop Downs do not have an empty value so the first option is not selected automatically.
					 * This also overrides placeholders for any Drop Down field.
					 */
					'isSelected'      => $field->inputType == 'select',
					'gppaErrorChoice' => 'missing_filter',
					'object'          => null,
				),
			);
		}

		$objects = $this->get_field_objects( $field, $field_values, 'choices' );

		if ( count( $objects ) === 0 ) {

			$choices = array(
				array(
					// Unchecked checkboxes need to have a non-empty value otherwise they will automatically be checked by GF.
					'value'           => apply_filters( 'gppa_no_choices_value', $field->get_input_type() === 'checkbox', $field ),
					'text'            => apply_filters( 'gppa_no_choices_text', '&ndash; ' . esc_html__( 'No Results', 'gp-populate-anything' ) . ' &ndash;', $field ),
					'isSelected'      => false,
					'gppaErrorChoice' => 'no_choices',
					'object'          => null,
				),
			);

		} else {

			$choices = array();

			foreach ( $objects as $object_index => $object ) {
				$value = $this->process_template( $field, 'value', $object, 'choices', $objects );
				$text  = $this->process_template( $field, 'label', $object, 'choices', $objects );

				if ( ! $value && ! $text ) {
					continue;
				}

				$choice = array(
					'value' => $value,
					'text'  => $text,
				);

				if ( rgar( $templates, 'price' ) ) {
					$choice['price'] = $this->process_template( $field, 'price', $object, 'choices', $objects );
				}

				if ( $include_object ) {
					$choice['object'] = $object;
				}

				/**
				 * Modify the choice to be populated into the current field.
				 *
				 * @since 1.0-beta-4.116
				 *
				 * @param array     $choice  The current choice being modified.
				 * @param \GF_Field $field   The current field being populated.
				 * @param array     $object  The current object being populated into the choice.
				 * @param array     $objects An array of objects being populated as choices into the field.
				 */
				$choices[] = gf_apply_filters( array( 'gppa_input_choice', $field->formId, $field->id ), $choice, $field, $object, $objects );
			}
		}

		/**
		 * Modify the choices to be populated into the current field.
		 *
		 * @since 1.0-beta-4.36
		 *
		 * @param array     $choices An array of Gravity Forms choices.
		 * @param \GF_Field $field   The current field being populated.
		 * @param array     $objects An array of objects being populated as choices into the field.
		 */
		$choices = gf_apply_filters( array( 'gppa_input_choices', $field->formId, $field->id ), $choices, $field, $objects );

		$this->_field_choices_cache[ $cache_key ] = $choices;

		return $choices;

	}

	/**
	 * Handles marking isSelected on fields with dynamic value population where multiple choices can be selected.
	 *
	 * Trello card #626
	 * https://secure.helpscout.net/conversation/870244683/12421
	 *
	 * @param $field
	 * @param null $field_values
	 *
	 * @see GP_Populate_Anything::get_selected_choices()
	 *
	 * @return mixed
	 */
	public function maybe_select_choices( $field, $field_values = null ) {

		$values_to_select = $this->get_selected_choices( $field, $field_values );

		if ( $values_to_select === null ) {
			return $field->choices;
		}

		foreach ( $field->choices as &$choice ) {
			if ( in_array( $choice['value'], $values_to_select ) ) {
				$choice['isSelected'] = true;
			}
		}

		return $field->choices;

	}

	/**
	 * @param $field
	 * @param null $field_values
	 *
	 * @see GP_Populate_Anything::maybe_select_choices()
	 *
	 * @return array|null
	 */
	public function get_selected_choices( $field, $field_values = null ) {

		$templates = rgar( $field, 'gppa-values-templates', array() );

		if ( ! in_array( $field->type, self::get_multi_selectable_choice_field_types() ) ) {
			return null;
		}

		if ( ! rgar( $field, 'gppa-values-enabled' ) || ! rgar( $field, 'gppa-values-object-type' ) || ! rgar( $templates, 'value' ) ) {
			return null;
		}

		/**
		 * @todo Extract this field value object block into a method.
		 */
		if ( strpos( rgar( $field, 'gppa-values-object-type' ), 'field_value_object' ) === 0 ) {
			$object_type_split           = explode( ':', rgar( $field, 'gppa-values-object-type' ) );
			$field_value_object_field_id = $object_type_split[1];
			$field_value_object_field    = GFFormsModel::get_field( $field->formId, $field_value_object_field_id );

			/* When using field value objects, we need to always set $populate to choices */
			$field_value_object_choices = $this->get_input_choices( $field_value_object_field, $field_values, true );
			$objects                    = wp_list_pluck( $field_value_object_choices, 'object' );

			foreach ( $field_value_object_choices as $field_value_object_choice ) {
				if ( $field_value_object_choice['value'] == rgar( $field_values, $field_value_object_field_id ) ) {
					$objects = array( $field_value_object_choice['object'] );
					break;
				}
			}
		} else {
			$objects = $this->get_field_objects( $field, $field_values, 'values' );
		}

		$values_to_select = array();

		foreach ( $objects as $object ) {
			$object_processed = $this->process_template( $field, 'value', $object, 'values', $objects );

			if ( ! is_array( $object_processed ) ) {
				// This will be an array when the top-level field is selected but it will be a string when a specific input is selected.
				$decoded = GFAddOn::maybe_decode_json( $object_processed );
				if ( $decoded !== null ) {
					$object_processed = $decoded;
				}

				/**
				 * Convert comma separated values to an array if source is meta/ACF and it's still not an array.
				 */
				if ( strpos( $templates['value'], 'meta_' ) === 0 && strpos( $object_processed, ',' ) ) {
					$object_processed = array_map( 'trim', explode( ',', $object_processed ) );
				}
			}

			if ( is_array( $object_processed ) ) {
				$values_to_select = array_unique( array_merge( $object_processed, $values_to_select ) );
			} else {
				$values_to_select[] = $object_processed;
			}
		}

		if ( $field->type === 'checkbox' ) {

			$values_to_select_by_input = array();
			$choice_number             = 0;

			foreach ( $field->choices as $choice ) {
				$choice_number++;

				// Hack to skip numbers ending in 0, so that 5.1 doesn't conflict with 5.10. From class-gf-field-checkbox.php
				if ( $choice_number % 10 == 0 ) {
					$choice_number ++;
				}

				$input = $field->id . '.' . $choice_number;

				if ( in_array( $choice['value'], $values_to_select ) ) {
					$values_to_select_by_input[ $input ] = $choice['value'];
				}
			}

			return $values_to_select_by_input;
		}

		return array_values( $values_to_select );

	}

	public function ajax_get_query_results() {

		if ( ! GFCommon::current_user_can_any( array( 'gravityforms_edit_forms' ) ) ) {
			wp_die( -1 );
		}

		check_ajax_referer( 'gppa', 'security' );

		global $wpdb;
		$wpdb->suppress_errors();

		$field_settings = json_decode( stripslashes( rgar( $_POST, 'fieldSettings' ) ), true );
		$template_rows  = rgar( $_POST, 'templateRows' );
		$populate       = rgar( $_POST, 'gppaPopulate' );

		$gppa_prefix          = 'gppa-' . $populate . '-';
		$object_type          = rgar( $field_settings, $gppa_prefix . 'object-type' );
		$object_type_instance = rgar( $this->_object_types, $object_type );

		if ( $object_type_instance->isRestricted() && ! is_super_admin() ) {
			wp_die( -1 );
		}

		$objects = $this->get_field_objects( $field_settings, null, $populate );

		$preview_results = array(
			'results' => array(),
			'limit'   => gp_populate_anything()->get_query_limit( $object_type_instance, $field_settings ),
		);

		foreach ( $objects as $object_index => $object ) {
			$row = array();

			foreach ( $template_rows as $template_row ) {
				$template_label = rgar( $template_row, 'label', '(Unknown Property)' );
				$template       = rgar( $template_row, 'id' );

				if ( ! $template ) {
					continue;
				}

				$row[ $template_label ] = esc_html( $this->process_template( $field_settings, $template, $object, $populate, $objects ) );
			}

			$preview_results['results'][] = $row;
		}

		if ( $wpdb->last_error ) {
			wp_send_json( array( 'error' => $wpdb->last_error ) );
		}

		wp_send_json( $preview_results );

	}

	public function get_input_values( $field, $template = 'value', $field_values = null, $lead = null, $form = null ) {

		$templates = rgar( $field, 'gppa-values-templates', array() );

		if ( ! $form ) {
			$form = GFAPI::get_form( rgar( $_REQUEST, 'form-id' ) );
		}

		if ( ! rgar( $field, 'gppa-values-enabled' ) || ! rgar( $field, 'gppa-values-object-type' ) || ! rgar( $templates, $template ) ) {
			if ( $lead ) {
				return RGFormsModel::get_lead_field_value( $lead, $field );
			}

			return null;
		}

		if ( strpos( rgar( $field, 'gppa-values-object-type' ), 'field_value_object' ) === 0 ) {
			if ( ! $form ) {
				if ( $lead ) {
					return RGFormsModel::get_lead_field_value( $lead, $field );
				}

				return null;
			}

			$object_type_split           = explode( ':', rgar( $field, 'gppa-values-object-type' ) );
			$field_value_object_field_id = $object_type_split[1];
			$field_value_object_field    = GFFormsModel::get_field( $form, $field_value_object_field_id );

			$field_value_object_choices = $this->get_input_choices( GFFormsModel::get_field( $form, $field_value_object_field_id ), $field_values, true );
			$objects                    = array_filter( wp_list_pluck( $field_value_object_choices, 'object' ) );

			$current_field_value = rgar( $field_values, $field_value_object_field_id );

			/**
			 * Update $current_field_value to work in the case that the field being populated from a Product dropdown
			 * which has a pipe delimiter to also include the price (which we don't want for value comparison).
			 */
			$current_field_value = $this->maybe_extract_value_from_product( $current_field_value, $field_value_object_field );

			foreach ( $field_value_object_choices as $field_value_object_choice ) {
				if ( $field_value_object_choice['value'] == $current_field_value ) {
					return $this->process_template( $field, $template, $field_value_object_choice['object'], 'values', $objects );
				}

				/**
				 * Maybe the field value object field has multiple inputs (checkbox, etc).
				 *
				 * We could check for the presence of floats in $field_values prior to the foreach, but that'd likely
				 * require a loop of some type which defeats the purpose.
				 **/
				foreach ( $field_values as $input_id => $input_value ) {
					if ( absint( $input_id ) != $field_value_object_field_id ) {
						continue;
					}

					$input_value = $this->maybe_extract_value_from_product( $input_value, $field_value_object_field );

					if ( ! isset( $values ) ) {
						$values = array();
					}

					if ( ! isset( $objects_in_value ) ) {
						$objects_in_value = array();
					}

					$add_input_value = function() use ( $values, $field_value_object_choice, $field, $template, $objects ) {
						$values[] = $this->process_template(
							$field,
							$template,
							$field_value_object_choice['object'],
							'values',
							$objects
						);

						return $values;
					};

					/**
					 * Field types where the inputs are scalar. This is specifically written for checkboxes but likely
					 * handles other inputs as well.
					 */
					if ( is_scalar( $input_value ) ) {
						if ( $field_value_object_choice['value'] == $input_value ) {
							$objects_in_value[] = $field_value_object_choice['object'];
							$values             = $add_input_value();
						}
						/**
						 * Loops values that are arrays like the values from Multi Select fields
						 */
					} elseif ( is_array( $input_value ) ) {
						foreach ( $input_value as $value ) {
							if ( $field_value_object_choice['value'] == $value ) {
								$objects_in_value[] = $field_value_object_choice['object'];
								$values             = $add_input_value();
							}
						}
					}
				}
			}

			if ( isset( $values ) && is_array( $values ) ) {
				return apply_filters( 'gppa_array_value_to_text', $values, $values, $field, $objects_in_value, $this->get_object_type( $object_type_split[0] ), $objects, rgar( $templates, $template ) );
			}

			if ( $lead ) {
				return RGFormsModel::get_lead_field_value( $lead, $field );
			}

			return null;
		}

		if ( $this->has_empty_field_value( $field, 'values', $field_values ) ) {
			/**
			 * Modify the value of an input when its value is being populated dynamically and there is a field
			 * dependency that is not filled in. This will take priority over the field's Default Value.
			 *
			 * @since 1.0-beta-4.129
			 *
			 * @param mixed $value Field value
			 * @param \GF_Field $field The field that is having its value modified
			 * @param array $form The form that is having its field's value modified
			 * @param array $templates Value templates for the current field
			 */
			return gf_apply_filters( array( 'gppa_has_empty_field_value', $field->formId, $field->id ), null, $field, $form, $templates );
		}

		$objects = $this->get_field_objects( $field, $field_values, 'values' );

		if ( count( $objects ) === 0 ) {
			if ( $lead ) {
				return RGFormsModel::get_lead_field_value( $lead, $field );
			}
		}

		if ( count( $objects ) === 0 ) {
			/**
			 * Modify the value of an input when no object results have been found. Note, the field's Default Value will
			 * be used if field dependencies have not been filled in.
			 *
			 * @since 1.0-beta-4.129
			 *
			 * @param mixed $value Field value
			 * @param \GF_Field $field The field that is having its value modified.
			 * @param array $form The form that is having its field's value modified
			 * @param array $templates Value templates for the current field
			 */
			return gf_apply_filters( array( 'gppa_no_results_value', $field->formId, $field->id ), null, $field, $form, $templates );
		}

		$values = $this->process_template( $field, $template, $objects[0], 'values', $objects );

		return gf_apply_filters(
			array( 'gppa_get_input_values', $field->formId, $field->id ),
			$values,
			$field,
			$template,
			$objects
		);

	}

	/**
	 * Gravity Forms product and option fields use values like "1|1" (1 being the value, 1 being the price). With GPPA,
	 * we need to extract out only the value for dynamic population.
	 *
	 * @param $value mixed
	 * @param $field_value_object_type GF_Field
	 *
	 * @return mixed
	 */
	public function maybe_extract_value_from_product( $value, $field_value_object_field ) {
		if (
			in_array( $field_value_object_field->type, array( 'product', 'option' ), true )
			&& strpos( $value, '|' ) !== false
		) {
			$value_bits = explode( '|', $value );

			return $value_bits[0];
		}

		return $value;
	}

	/**
	 * @param $field
	 *
	 * @return bool
	 */
	public function is_field_dynamically_populated( $field ) {
		return rgar( $field, 'gppa-choices-enabled' ) || rgar( $field, 'gppa-values-enabled' );
	}

	/**
	 * Loop through form fields to check if any field in the form uses dynamic population powered by Populate Anything.
	 *
	 * @param $form array Form to check for dynamic population
	 * @uses GP_Populate_Anything::is_field_dynamically_populated()
	 */
	public function form_has_dynamic_population( $form ) {
		$fields = rgar( $form, 'fields' );

		if ( empty( $fields ) ) {
			return false;
		}

		foreach ( $fields as $field ) {
			if ( $this->is_field_dynamically_populated( $field ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @param array|GF_Field $field
	 * @param array $form
	 * @param array $field_values
	 * @param array $entry
	 * @param boolean $force_use_field_value
	 * @param boolean $include_html
	 * @param boolean $run_pre_render
	 *
	 * @return array
	 */
	public function hydrate_field( $field, $form, $field_values, $entry = null, $force_use_field_value = false, $include_html = false, $run_pre_render = false ) {

		$field                    = GF_Fields::create( $field );
		$preselected_choice_value = null;

		if ( $field->choices !== '' && isset( $field->choices ) ) {

			$field->choices = $this->get_input_choices( $field, $field_values );
			$field->choices = $this->maybe_select_choices( $field, $field_values );

			$field->gppaDisable = ! empty( $field->choices[0]['gppaErrorChoice'] );

			if ( $field->get_input_type() == 'checkbox' ) {
				$inputs = array();
				$index  = 1;

				foreach ( $field->choices as $choice ) {

					if ( $index % 10 == 0 ) {
						$index++;
					}

					$inputs[] = array(
						'id'    => sprintf( '%d.%d', $field->id, $index ),
						'label' => $choice['text'],
					);

					$index++;

				}

				$field->inputs = $inputs;
			}

			/**
			 * If there's a value pre-selected, use it as the preselected choice value.
			 */
			foreach ( $field->choices as $choice ) {

				if ( ! rgar( $choice, 'isSelected' ) ) {
					continue;
				}

				if ( ! rgblank( $choice['value'] ) ) {
					// If there are multiple pre-selections, make sure we capture them all in an array
					if ( $preselected_choice_value ) {
						$preselected_choice_value   = ( is_array( $preselected_choice_value ) ) ? $preselected_choice_value : array( $preselected_choice_value );
						$preselected_choice_value[] = $choice['value'];
					} else {
						$preselected_choice_value = $choice['value'];
					}
				}
			}

			/**
			 * Set preselected choice value to first choice if there is not a placeholder and there isn't a pre-selected
			 * choice above.
			 */
			if ( ! $preselected_choice_value && $field->get_input_type() === 'select' && count( $field->choices ) && ! rgblank( $field->choices[0]['value'] ) && ! $field->placeholder ) {
				$preselected_choice_value = $field->choices[0]['value'];
			}
		}

		if ( $field->inputs && ! in_array( $field->type, self::get_interpreted_multi_input_field_types() ) ) {

			$field_value = array();

			if ( $force_use_field_value ) {
				foreach ( $field->inputs as $input ) {
					$field_value[ $input['id'] ] = rgar( $field_values, $input['id'] );
				}
			} else {
				foreach ( $field->inputs as $input ) {
					$value = $this->get_input_values( $field, $input['id'], $field_values, $entry, $form );

					if ( $value ) {
						$field_value[ $input['id'] ] = $value;
					}
				}
			}
		} else {
			/**
			 * This is here to force using the provided field values in instances like save and continue.
			 **/
			if ( $force_use_field_value ) {
				$field_value = rgar( $field_values, $field->id );
			} else {
				$field_value = $this->get_input_values( $field, 'value', $field_values, $entry, $form );
			}

			$filter_name = 'gppa_modify_field_value_' . $field->type;

			if ( has_filter( $filter_name ) ) {
				$field_value = apply_filters( $filter_name, $field_value, $field, $field_values );
			}
		}

		if ( in_array( $field->type, self::get_multi_selectable_choice_field_types() ) ) {
			$field_value = $this->get_selected_choices( $field, $field_values );

			if ( $field->storageType === 'json' ) {
				$field_value = json_encode( $field_value );
			}
		}

		/**
		 * We need to get *all* populated values (include GF-populated values) in order to establish the most accurate
		 * state of the form when it is loaded.
		 *
		 * For multi-input fields, the $field_value will most often default to an empty array. Our $field_values may
		 * contain an input-specific value, so let's check for it. Currently, this is limited to Single Product fields
		 * because GF-dyn-pop Quantity is not fetched correctly by the get_value_default_if_empty() below.
		 */
		if ( rgblank( $field_value ) ) {
			if ( rgar( $field, 'gppa-values-enabled' ) || rgar( $field, 'gppa-choices-enabled' ) ) {
				$field_value = GFFormsModel::get_field_value( $field, $field_values );
			} else {
				$field_value = rgar( $field_values, $field->id );
			}
		}

		if ( rgar( $_REQUEST, 'gravityview-meta' ) && isset( $field_values[ $field->id ] ) ) {
			$field_value = rgar( $field_values, $field->id );
		}

		$field_value = $field->get_value_default_if_empty( $field_value );

		// Can't always rely on Gravity Forms default value.
		switch ( $field->get_input_type() ) {
			case 'singleproduct':
			case 'hiddenproduct':
				if ( rgblank( rgar( $field_value, "{$field->id}.1" ) ) ) {
					$field_value[ "{$field->id}.1" ] = $field->label;
				}
				if ( rgblank( rgar( $field_value, "{$field->id}.3" ) ) ) {
					$quantity_field = GFCommon::get_product_fields_by_type( $form, array( 'quantity' ), $field->id );
					if ( ! count( $quantity_field ) ) {
						// GF-populated Single Product Quantity input values are not correctly fetched via get_value_default_if_empty()
						// above. Let's get them from our $field_values array.
						$field_value[ "{$field->id}.3" ] = rgar( $field_values, "{$field->id}.3", $field->disableQuantity );
					}
				}
				break;
			case 'calculation':
				if ( rgblank( $field_value[ "{$field->id}.1" ] ) ) {
					$field_value[ "{$field->id}.1" ] = $field->label;
				}
				if ( rgblank( $field_value[ "{$field->id}.3" ] ) && $field->disableQuantity ) {
					$quantity_field = GFCommon::get_product_fields_by_type( $form, array( 'quantity' ), $field->id );
					if ( ! count( $quantity_field ) ) {
						// GF-populated Single Product Quantity input values are not correctly fetched via get_value_default_if_empty()
						// above. Let's get them from our $field_values array.
						$field_value[ "{$field->id}.3" ] = rgar( $field_values, "{$field->id}.3", $field->disableQuantity );
					}
				}
				// Attempt to calculate the original calculation so it can be rendered in LMTs on load. Not 100% confident in this...
				$fake_entry                      = $field_values;
				$fake_entry['currency']          = GFCommon::get_currency();
				$fake_entry['id']                = null;
				$fake_entry['form_id']           = $form['id'];
				$field_value[ "{$field->id}.2" ] = GFCommon::calculate( $field, $form, $fake_entry );
				break;
		}

		/**
		 * current-merge-tag-values is used to see if the field is stilled coupled to the live merge tags.
		 * @todo Add suppport for fields that return an array for their default value.
		 */
		$default_value = $field->get_value_default();
		$request_val   = rgar( rgar( $_REQUEST, 'current-merge-tag-values', array() ), ! is_array( $default_value ) ? $default_value : '' );

		$field_value = str_replace( "\r\n", "\n", $field_value );
		$request_val = str_replace( "\r\n", "\n", $request_val );

		/**
		 * Added trim here to improve reliability of LMTs being in textareas. There were situations where the number of
		 * line breaks would not equal and cause LMTs to stop populating.
		 */
		if ( is_string( $field_value ) ) {
			if ( stripslashes( trim( $field_value ) ) == stripslashes( trim( $request_val ) ) ) {
				$field_value = $default_value;
			}
		} else {
			if ( $field_value == $request_val ) {
				$field_value = $default_value;
			}
		}

		$form_id = rgar( $form, 'id' );

		/**
		 * Filter the field object after it has been hydrated.
		 *
		 * @since 1.0-beta-4.166
		 *
		 * @param \GF_Field $field The field object that has been hydrated.
		 * @param array     $form  The current form object to which the hydrated field belongs.
		 */
		$field = gf_apply_filters( array( 'gppa_hydrated_field', $form['id'], $field['id'] ), $field, $form );

		/**
		 * Pass field through gform_pre_render to improve compatibility with Perks like GPLC during AJAX
		 */
		if ( $run_pre_render ) {
			remove_filter( 'gform_pre_render', array( $this, 'hydrate_initial_load' ), 8 );

			$pseudo_form = gf_apply_filters(
				array( 'gform_pre_render', $form['id'], $field['id'] ),
				array_merge(
					$form,
					array(
						'fields' => array( $field ),
					)
				),
				$form,
				false,
				$field_values
			);

			add_filter( 'gform_pre_render', array( $this, 'hydrate_initial_load' ), 8, 3 );

			$field = $pseudo_form['fields'][0];
		}

		$result = array(
			'field'       => $field,
			'field_value' => $field_value || $field_value === '0' ? $field_value : $preselected_choice_value,
			'lead_id'     => rgar( $entry, 'id' ),
			'form_id'     => $form_id,
			'form'        => $form,
		);

		/**
		 * gppa_hydrate_field_html is used as a filter to receive many of the Live Merge Tag filters like the form does
		 * on initial load.
		 */
		if ( $include_html ) {
			/**
			 * gppa_hydrate_input_html is here to provide a filter with the same signature as gform_field_content as
			 * there isn't a comparable filter for inputs.
			 */
			$input_html     = apply_filters( 'gppa_hydrate_input_html', GFCommon::get_field_input( $field, $field_value, rgar( $entry, 'id' ), $form_id, $form ), $field );
			$result['html'] = apply_filters( 'gppa_hydrate_field_html', $input_html, $form, $result, $field );
			$default_value  = $field->get_value_default(); // Cache default value
			/**
			 * Re-add the live merge tag value data attr if the field becomes uncoupled. This will allow re-coupling.
			 */
			if ( ! is_array( $default_value ) && preg_match( $this->live_merge_tags->live_merge_tag_regex, $default_value ) && $field_value !== $default_value ) {
				$result['html'] = preg_replace( $this->live_merge_tags->value_attr, 'data-gppa-live-merge-tag-value="' . esc_attr( $default_value ) . '" $0', $result['html'] );
			}
		}

		/**
		 * Convert field value from Live Merge tag to allow chaining with Form Field values.
		 */
		if ( is_scalar( $field->get_value_default() ) && preg_match( $this->live_merge_tags->live_merge_tag_regex, $field->get_value_default() ) ) {
			$result['field_value'] = $this->live_merge_tags->get_live_merge_tag_value( $result['field_value'], $form, $field_values );

			$GLOBALS['gppa-field-values'][ $form_id ][ $field->id ] = $result['field_value'];
		}

		return $result;

	}

	public function hydrate_fields( $form ) {

		if ( empty( $form['fields'] ) ) {
			return $form;
		}

		foreach ( $form['fields'] as &$field ) {

			if ( ! rgar( $field, 'gppa-choices-enabled' ) ) {
				continue;
			}

			$_field = $this->hydrate_field( $field, $form, $this->get_posted_field_values( $form ) );
			$field  = $_field['field'];

		}

		return $form;
	}

	/**
	 * Run exported values through helper method to ensure that the choice label is used instead of the value.
	 *
	 * @param $value
	 * @param $form_id
	 * @param $field_id
	 * @param $entry
	 *
	 * @return mixed|string|null
	 */
	public function hydrate_export_value( $value, $form_id, $field_id, $entry ) {
		$field = GFAPI::get_field( $form_id, $field_id );

		return $this->get_submitted_choice_label( $value, $field, $entry['id'] );
	}

	public function get_posted_field_values( $form ) {

		// Ensure that we're parsing the correct Form's posted values
		$form_id         = intval( rgar( $form, 'id', 0 ) );
		$gform_submit_id = intval( rgar( $_POST, 'gform_submit', - 1 ) );
		$ajax_id         = intval( rgar( $_POST, 'form-id', - 1 ) );
		$parse_request   = ( $form_id === $gform_submit_id || $form_id === $ajax_id );

		$field_values = $this->get_prepopulate_values( $form, rgar( $this->_prepopulate_fields_values, $form['id'], array() ) );
		$field_values = array_replace( $field_values, $this->get_save_and_continue_values( rgar( $_REQUEST, 'gf_token' ) ) );

		if ( isset( $GLOBALS['gppa-field-values'][ $form['id'] ] ) ) {
			$field_values = array_replace( $field_values, rgar( $GLOBALS['gppa-field-values'], $form['id'], array() ) );
		} elseif ( isset( $_REQUEST['field-values'] ) && $parse_request ) {
			$field_values = array_replace( $field_values, $this->get_field_values_from_request() );
		}

		if ( ! empty( $form['fields'] ) && is_array( $form['fields'] ) ) {
			foreach ( $form['fields'] as $field ) {
				$field_value = null;

				/**
				 * If value is directly posted, use it.
				 */
				if ( $parse_request && rgpost( "input_{$field->id}" ) ) {
					$field_value = rgpost( "input_{$field->id}" );

					/**
					 * Value is cached in runtime variable
					 */
				} elseif ( isset( $field_values[ $field->id ] ) ) {
					$field_value = $field_values[ $field->id ];

					/**
					 * Check for individually posted inputs for entire field
					 */
				} else {
					foreach ( $_POST as $posted_meta_key => $posted_meta ) {
						if ( strpos( $posted_meta_key, "input_{$field->id}_" ) !== 0 ) {
							continue;
						}

						$input_id = str_replace( "input_{$field->id}_", '', $posted_meta_key );

						if ( $field_value === null ) {
							$field_value = array();
						}

						$field_value[ $field->id . '.' . $input_id ]  = $posted_meta;
						$field_values[ $field->id . '.' . $input_id ] = $posted_meta;
					}

					if ( $field_value ) {
						continue;
					}
				}

				/**
				 * Ideally we'd like to use $field->get_value_submission() but it requires the submit $_POST value to be
				 * present. Setting that will likely cause unintended side-effects.
				 */
				if ( $field_value == 'gf_other_choice' ) {
					$other       = $field->id . '_other';
					$field_value = isset( $field_values[ $other ] ) ? $field_values[ $other ] : rgpost( 'input_' . $other );
				}

				if ( $field_value ) {
					$field_values[ $field->id ] = $field_value;
				}
			}
		}

		return count( $field_values ) ? $field_values : array();
	}

	public function get_prepopulate_values( $form, $field_values = array() ) {

		$prepopulate_values = array();

		if ( empty( $form['fields'] ) ) {
			return $prepopulate_values;
		}

		foreach ( $form['fields'] as $field ) {

			$input_type = $field->get_input_type();
			$inputs     = $field->get_entry_inputs();

			/**
			 * @note GP Nested Forms sets allowsPrepopulate to true on all fields in the child form.
			 */
			if ( $field->allowsPrepopulate ) {
				/* Skip over list fields as RGFormsModel::get_parameter_value() will recurse and try to merge values indefinitely. */
				if ( $input_type === 'list' ) {
					continue;
				}

				if ( $input_type == 'checkbox' || $input_type == 'multiselect' ) {
					$prepopulate_values[ $field->id ] = RGFormsModel::get_parameter_value( $field->inputName, $field_values, $field );

					if ( ! is_array( $prepopulate_values[ $field->id ] ) ) {
						$prepopulate_values[ $field->id ] = explode( ',', $prepopulate_values[ $field->id ] );
					}
				} elseif ( is_array( $inputs ) ) {
					foreach ( $inputs as $input ) {
						$prepopulate_values[ $input['id'] ] = RGFormsModel::get_parameter_value( rgar( $input, 'name' ), $field_values, $field );
					}
				} else {
					$prepopulate_values[ $field->id ] = RGFormsModel::get_parameter_value( $field->inputName, $field_values, $field );
				}
			}
		}

		$this->_prepopulate_fields_values[ $form['id'] ] = array_replace( $field_values, array_filter( $prepopulate_values ) );

		return $this->_prepopulate_fields_values[ $form['id'] ];

	}

	public function field_input_add_empty_field_value_filter( $html, $field, $value, $lead_id, $form_id ) {

		if ( GFCommon::is_form_editor() || ! $field->{'gppa-choices-enabled'} || ( ! $this->has_empty_field_value( $field, 'choices' ) && ! $this->has_empty_field_value( $field, 'values' ) ) ) {
			return $html;
		}

		$field_values = $this->get_field_values_from_request();

		$field_html_empty_field_value = gf_apply_filters(
			array(
				'gppa_field_html_empty_field_value',
				$field->type,
			),
			'',
			$field,
			$form_id,
			$field_values
		);

		if ( ( $this->has_empty_field_value( $field, 'choices' ) || $this->has_empty_field_value( $field, 'values' ) ) && $field_html_empty_field_value ) {
			return '<div class="ginput_container">' . $field_html_empty_field_value . '</div>';
		}

		return $html;

	}

	public function field_content_disable_if_empty_field_values( $field_content, $field ) {
		if ( ! $field || GFCommon::is_entry_detail() ) {
			return $field_content;
		}

		if ( ! isset( $field->gppaDisable ) || $field->gppaDisable === false ) {
			return $field_content;
		}

		/**
		 * Only disable option's if not a product option field. This is due to gformGetPrice() relying on jQuery's
		 * .val() method which will return undefined for disabled options.
		 *
		 * HS #23575
		 */
		if ( $field->type !== 'option' ) {
			// Keep "Other" radio options enabled even if there are no GPPA results
			$field_content = preg_replace( '/ value=([\'"](?!other|gf_other_choice[\'"]))/i', ' disabled="true" selected value=$1', $field_content );
		}

		$field_content = str_replace( '<select ', '<select disabled="true" ', $field_content );
		$field_content = str_replace( '<textarea ', '<textarea disabled="true" ', $field_content );

		return $field_content;

	}

	public function radio_field_html_empty_field_value() {
		return '<p>Please fill out other fields.</p>';
	}

	/**
	 * Since the choices for a field do not exist on the field object after submission, we use the gppa_choices meta
	 * that's included during entry submission.
	 *
	 * This is favorable over always filtering gform_form_meta since always filtering gform_form_meta can caused
	 * unintended consequences.
	 *
	 * @param $value
	 * @param $field
	 * @param $entry_id
	 *
	 * @return mixed|string|null
	 */
	public function get_submitted_choice_label( $value, $field, $entry_id ) {
		if ( ! rgar( $field, 'gppa-choices-enabled' ) ) {
			return $value;
		}

		$choices = rgar( gform_get_meta( $entry_id, 'gppa_choices' ), $field['id'], array() );

		return rgar( $choices, $value, $value );
	}

	public function entry_field_value( $display_value, $field, $lead, $form ) {
		return $this->get_submitted_choice_label( $display_value, $field, $lead['id'] );
	}

	public function entries_field_value( $value, $form_id, $field_id, $entry ) {
		$form  = GFAPI::get_form( $form_id );
		$field = GFFormsModel::get_field( $form, $field_id );

		return $this->get_submitted_choice_label( $value, $field, $entry['id'] );
	}

	public function maybe_save_choice_label( $value, $entry, $field, $form ) {

		if ( ! rgar( $field, 'gppa-choices-enabled' ) ) {
			return $value;
		}

		/**
		 * gppa_choices is a legacy meta_key. It's not as descriptive as the variable below but it's kept for backwards
		 * compatibility.
		 */
		$gppa_choice_labels = gform_get_meta( $entry['id'], 'gppa_choices' );

		if ( ! is_array( $gppa_choice_labels ) ) {
			$gppa_choice_labels = array();
		}

		if ( empty( $this->_hydrated_fields_on_submission_cache[ $form['id'] . '-' . $field['id'] ] ) ) {
			$this->_hydrated_fields_on_submission_cache[ $form['id'] . '-' . $field['id'] ] = $this->hydrate_field( $field, $form, $entry, $entry );
		}

		$hydrated_field = $this->_hydrated_fields_on_submission_cache[ $form['id'] . '-' . $field['id'] ];

		$choices = wp_list_pluck( $hydrated_field['field']->choices, 'text', 'value' );

		if ( ! empty( $gppa_choice_labels[ $field->id ] ) ) {
			$gppa_choice_labels[ $field->id ] = array_merge(
				$gppa_choice_labels[ $field->id ],
				array(
					$value => rgar( $choices, $value ),
				)
			);
		} else {
			$gppa_choice_labels[ $field->id ] = array(
				$value => rgar( $choices, $value ),
			);
		}

		gform_update_meta( $entry['id'], 'gppa_choices', $gppa_choice_labels, $form['id'] );

		return $value;

	}

	public function modify_admin_field_choices( $form, $ajax = false, $field_values = array() ) {

		if ( GFCommon::is_form_editor() || $this->_getting_current_entry || ! is_array( $form ) ) {
			return $form;
		}

		if ( GFCommon::is_entry_detail() ) {
			// @todo Ugh, this is super messy. Not sure that an $entry should be passed as $field_values. Let's revisit.
			$field_values = $this->get_current_entry();
		} else {
			$field_values = array_replace( (array) $field_values, $this->get_posted_field_values( $form ) );
		}

		foreach ( $form['fields'] as &$field ) {

			if ( empty( $field->choices ) ) {
				continue;
			}

			$field->choices     = $this->get_input_choices( $field, $field_values );
			$field->gppaDisable = ! empty( $field->choices[0]['gppaErrorChoice'] );

			/* Append selected choice during submission if missing from choices fetched above */
			if ( $entry = $this->get_current_entry() ) {
				$field_value = rgar( $field_values, $field->id );
				$choices     = wp_list_pluck( $field->choices, 'text', 'value' );

				$gppa_choices = gform_get_meta( $entry['id'], 'gppa_choices' );
				$label        = rgar( rgar( $gppa_choices, $field->id ), $field_value );

				if ( $field_value && ! isset( $choices[ $field_value ] ) && $label ) {
					$field->choices[] = array(
						'value'      => $field_value,
						'text'       => $label,
						'isSelected' => true,
					);
				}
			}

			if ( $field->get_input_type() == 'checkbox' ) {

				$inputs = array();
				$index  = 1;

				foreach ( $field->choices as $choice ) {

					if ( $index % 10 == 0 ) {
						$index++;
					}

					$inputs[] = array(
						'id'    => sprintf( '%d.%d', $field->id, $index ),
						'label' => $choice['text'],
					);

					$index++;

				}

				$field->inputs = $inputs;

			}
		}

		return $form;

	}

	public function get_current_entry() {

		if ( ! class_exists( 'GFEntryDetail' ) ) {
			return false;
		}

		// Avoid infinite loops...
		$this->_getting_current_entry = true;
		$entry                        = GFEntryDetail::get_current_entry();
		$this->_getting_current_entry = false;
		return $entry;
	}

	public function modify_field_values_date( $value, $field ) {

		$format = empty( $field->dateFormat ) ? 'mdy' : esc_attr( $field->dateFormat );

		if ( ! $field->inputs || ! count( $field->inputs ) ) {
			return $value;
		}

		$date_info = GFCommon::parse_date( $value, $format );

		$day_value   = esc_attr( rgget( 'day', $date_info ) );
		$month_value = esc_attr( rgget( 'month', $date_info ) );
		$year_value  = esc_attr( rgget( 'year', $date_info ) );

		$date_array        = $field->get_date_array_by_format( array( $month_value, $day_value, $year_value ) );
		$date_array_values = array_values( $date_array );

		$value = array();

		foreach ( $field->inputs as $input_index => &$input ) {
			$value[ $input['id'] ] = $date_array_values[ $input_index ];
		}

		return $value;

	}

	public function modify_field_values_time( $value, $field ) {

		if ( ! is_string( $value ) ) {
			return $value;
		}

		preg_match( '/^(\d*):(\d*) ?(.*)$/', $value, $matches );

		if ( ! $matches || ! count( $matches ) ) {
			return $value;
		}

		$hour     = esc_attr( $matches[1] );
		$minute   = esc_attr( $matches[2] );
		$the_rest = strtolower( rgar( $matches, 3 ) );

		$value = array();

		$value[ $field->id . '.' . 1 ] = $hour;
		$value[ $field->id . '.' . 2 ] = $minute;
		$value[ $field->id . '.' . 3 ] = strpos( $the_rest, 'am' ) > - 1 ? 'am' : 'pm';

		return $value;

	}

	public function should_force_use_field_value( $field, $save_and_continue_values ) {

		foreach ( $save_and_continue_values as $input_id => $value ) {
			if ( absint( $field->id ) === absint( $input_id ) ) {
				return true;
			}
		}

		if ( ! empty( $this->_prepopulate_fields_values[ $field->formId ] ) ) {
			foreach ( $this->_prepopulate_fields_values[ $field->formId ] as $input_id => $value ) {
				if ( absint( $field->id ) === absint( $input_id ) ) {
					return true;
				}
			}
		}

		return false;

	}

	public function hydrate_form( $form, $entry ) {
		return $this->hydrate_initial_load( $form, false, array(), $entry );
	}

	public function hydrate_initial_load( $form, $ajax = false, $field_values = array(), $entry = null, $hydrate_values = true ) {

		if ( ! isset( $form['fields'] ) ) {
			return $form;
		}

		if ( ! isset( $GLOBALS['gppa-field-values'][ $form['id'] ] ) ) {
			$GLOBALS['gppa-field-values'][ $form['id'] ] = array();
		}

		if ( ! empty( $field_values ) && is_array( $field_values ) ) {
			$this->_prepopulate_fields_values[ $form['id'] ] = $field_values;
			$GLOBALS['gppa-field-values'][ $form['id'] ]     = $field_values;
		}

		$field_values             = $this->get_posted_field_values( $form );
		$save_and_continue_values = $this->get_save_and_continue_values( rgar( $_REQUEST, 'gf_token' ) );

		$entry = gf_apply_filters(
			array(
				'gppa_hydrate_initial_load_entry',
				$form['id'],
			),
			$entry,
			$form,
			$ajax,
			$field_values
		);

		foreach ( $form['fields'] as &$field ) {
			// Ensure dateFormat is set if it's not specified (breaks LMT)
			if ( $field->type === 'date' && rgblank( $field->dateFormat ) ) {
				$field->dateFormat = 'mdy';
			}
			$force_use_field_value = $this->should_force_use_field_value( $field, $save_and_continue_values );
			$hydrated_field        = $this->hydrate_field( $field, $form, $field_values, $entry, $force_use_field_value );
			$hydrated_value        = $hydrated_field['field_value'];

			if ( $this->is_field_dynamically_populated( $field ) ) {
				$field = $hydrated_field['field'];

				if ( $hydrate_values ) {
					if ( is_array( $field->inputs ) ) {
						foreach ( $field->inputs as &$input ) {
							if ( $value = rgar( $hydrated_value, $input['id'] ) ) {
								if ( $field->get_input_type() == 'checkbox' ) {
									$field = $this->select_choice( $field, $value );
								} else {
									$input['defaultValue'] = $value;
									// Update basePrice if we're populating a product field
									if ( $field->type === 'product' && $input['label'] === 'Price' ) {
										$field->basePrice = $value;
									}
								}
							}
						}
					} else {
						$field->defaultValue = $hydrated_value;
					}
				}
			}

			/**
			 * If hydrated value is an array of input, add individual fields to gppa-field-values instead
			 */
			if ( $this->is_field_value_array_of_input_value( $hydrated_value, $field ) ) {
				foreach ( $hydrated_value as $input_id => $input_value ) {
					$GLOBALS['gppa-field-values'][ $field->formId ][ $input_id ] = $input_value;
				}
			} else {
				$GLOBALS['gppa-field-values'][ $field->formId ][ $field->id ] = $hydrated_value;
			}

			$field_values[ $field->id ] = $hydrated_value;
			// Store hydrated value for use in other perks (currently GPRO)
			$field->gppa_hydrated_value = $hydrated_value;
		}

		return $form;

	}

	/**
	 * Determine if the value is an array of input values for the given field.
	 *
	 * @param $value
	 * @param $field GF_Field
	 *
	 * @return boolean
	 */
	public function is_field_value_array_of_input_value( $value, $field ) {

		if ( ! is_array( $value ) ) {
			return false;
		}

		foreach ( $value as $input_id => $meta ) {
			if ( absint( $input_id ) !== absint( $field->id ) ) {
				return false;
			}
		}

		return true;

	}

	public function modify_admin_field_values( $form, $ajax = false, $field_values = array() ) {

		if ( GFCommon::is_form_editor() || $this->_getting_current_entry || ! is_array( $form ) ) {
			return $form;
		}

		if ( GFCommon::is_entry_detail() ) {
			// @todo Ugh, this is super messy. Not sure that an $entry should be passed as $field_values. Let's revisit.
			$field_values = $this->get_current_entry();
		} else {
			$field_values = array_replace( (array) $field_values, $this->get_posted_field_values( $form ) );
		}

		foreach ( $form['fields'] as &$field ) {
			if ( ! $field->inputs || in_array( $field->type, self::get_interpreted_multi_input_field_types() ) ) {
				if ( $value = $this->get_input_values( $field, 'value', $field_values ) ) {
					$filter_name = 'gppa_modify_field_values_' . $field->type;

					if ( has_filter( $filter_name ) ) {
						$field = apply_filters( $filter_name, $field, $value, $field_values );
					} else {
						$field->defaultValue = $value;
					}
				}

				continue;
			}

			foreach ( $field->inputs as &$input ) {
				if ( $value = $this->get_input_values( $field, $input['id'], $field_values ) ) {
					if ( $field->get_input_type() == 'checkbox' ) {
						$field = $this->select_choice( $field, $value );
					} else {
						$input['defaultValue'] = $value;
					}
				}
			}
		}

		return $form;

	}

	public function select_choice( $field, $value ) {
		foreach ( $field->choices as &$choice ) {
			if ( $choice['value'] == $value ) {
				$choice['isSelected'] = true;
			}
		}
		return $field;
	}

	/* Admin Methods */
	public function ajax_get_object_type_properties() {

		if ( ! GFCommon::current_user_can_any( array( 'gravityforms_edit_forms' ) ) ) {
			wp_die( -1 );
		}

		$object_type            = rgar( $this->_object_types, $_REQUEST['object-type'] );
		$primary_property_value = rgar( $_REQUEST, 'primary-property-value' );

		if ( ! $object_type ) {
			return array();
		}

		if ( $object_type->isRestricted() && ! is_super_admin() ) {
			wp_die( -1 );
		}

		$output = array();

		foreach ( $object_type->get_properties_filtered( $primary_property_value ) as $property_id => $property ) {
			if ( is_numeric( $property_id ) && is_string( $property ) ) {
				$output['ungrouped'] = array(
					'value' => $property,
					'label' => $property,
				);

				continue;
			}

			$output[ rgar( $property, 'group', 'ungrouped' ) ][] = array_merge(
				$property,
				array(
					'value' => $property_id,
				)
			);
		}

		foreach ( $output as $group_id => $group_items ) {
			usort(
				$output[ $group_id ],
				function ( $a, $b ) {
					if ( is_array( $a ) ) {
						$a = $a['label'];
					}

					if ( is_array( $b ) ) {
						$b = $b['label'];
					}

					return strnatcmp( $a, $b );
				}
			);
		}

		wp_send_json( $output );

	}

	public function ajax_get_property_values() {

		if ( ! GFCommon::current_user_can_any( array( 'gravityforms_edit_forms' ) ) ) {
			wp_die( -1 );
		}

		$object_type_id         = $_REQUEST['object-type'];
		$object_type            = rgar( $this->_object_types, $object_type_id );
		$primary_property_value = rgar( $_REQUEST, 'primary-property-value' );

		if ( ! $object_type ) {
			return array();
		}

		if ( $object_type->isRestricted() && ! is_super_admin() ) {
			wp_die( -1 );
		}

		$properties  = $object_type->get_properties_filtered( $primary_property_value );
		$property_id = $_REQUEST['property'];

		$property = rgar( $properties, $property_id );

		if ( $property_id === 'primary-property' ) {
			$property = $object_type->get_primary_property();
		}

		if ( ! $property ) {
			return array();
		}

		$property_args = rgar( $property, 'args', array() );

		$output = call_user_func_array( $property['callable'], $property_args );

		$label_filter = "gppa_property_label_{$object_type_id}_{$property_id}";

		if ( has_filter( $label_filter ) ) {
			$associative_output = array();

			foreach ( $output as $key => $value ) {
				$associative_output[ $value ] = apply_filters( $label_filter, $value );
			}

			$output = $associative_output;
		}

		/**
		 * Send back response to the editor that the property values should not be displayed in the dropdown for this
		 * particular property.
		 *
		 * Instead, a custom value or special value should by used by the user.
		 *
		 * This is done for usability purposes but also to help browsers from locking up if there are a huge number of
		 * results.
		 */
		if ( count( $output ) >= apply_filters( 'gppa_max_property_values_in_editor', 1000 ) ) {
			wp_send_json( 'gppa_over_max_values_in_editor' );

			die();
		}

		/**
		 * Transform array to flattened array for JavaScript ordering
		 */
		if ( gppa_is_assoc_array( $output ) ) {
			natcasesort( $output );

			$non_associative_output = array();

			foreach ( $output as $value => $label ) {
				$non_associative_output[] = array( $value, $label );
			}

			$output = $non_associative_output;
		} else {
			natcasesort( $output );
		}

		/* Remove duplicate property values */
		$output = array_unique( $output, SORT_REGULAR );

		wp_send_json( $output );

	}

	public function ajax_get_batch_field_html() {

		/**
		 * This option is used to simulate real-world server conditions while developing and with acceptance tests.
		 *
		 * NEVER set this option to true unless you are aware of the implications.
		 */
		if ( get_option( 'gppa_ajax_network_debug' ) ) {
			sleep( 1 );
		}

		check_ajax_referer( 'gppa', 'security' );

		$form         = GFAPI::get_form( $_REQUEST['form-id'] );
		$fields       = rgar( $_REQUEST, 'field-ids', array() );
		$field_values = $this->get_field_values_from_request();
		$entry_id     = rgar( $_REQUEST, 'lead-id', 0 );
		$using_entry  = ! ! $entry_id;
		$entry        = $using_entry ? GFAPI::get_entry( $entry_id ) : null;
		$fake_lead    = array();
		$response     = array(
			'fields'           => array(),
			'merge_tag_values' => array(),
			'event_id'         => rgpost( 'event-id' ),
		);

		/**
		 * Remove field values for fields that are being populated as the choices may change.
		 */
		$field_values = array_filter( $field_values, function( $field_value, $field_id ) use ( $fields, $form ) {
			$field = null;

			/**
			 * Using GFAPI::get_field() has unforseen consequences here most likely due to hydration.
			 */
			foreach ( rgar( $form, 'fields' ) as $current_field ) {
				if ( isset( $current_field->id ) && $current_field->id === $field_id ) {
					$field = $current_field;
					break;
				}
			}

			/**
			 * Only remove field values for fields that have populated choices. Without this condition, Live Merge Tags
			 * may not be properly populated.
			 */
			if ( rgar( $field, 'gppa-choices-enabled' ) && in_array( $field_id, $fields ) ) {
				return false;
			}

			return true;
		}, ARRAY_FILTER_USE_BOTH );

		/**
		 * Map the field values to $_POST to ensure that $field->get_value_save_entry() works as expected.
		 */
		foreach ( $field_values as $input => $value ) {
			$_POST[ 'input_' . $input ] = $value;
		}

		/**
		 * Hydrate form to get more accurate merge tag values.
		 */
		$form = $this->hydrate_initial_load( $form, false, $field_values, $entry, false );

		/**
		 * Map field values again to $_POST after hydration. Note this is a duplication of a block above.
		 */
		foreach ( $GLOBALS['gppa-field-values'][ $form['id'] ] as $input => $value ) {
			$_POST[ 'input_' . $input ] = $value;
		}

		foreach ( $GLOBALS['gppa-field-values'][ $form['id'] ] as $input => $value ) {
			$field = GFFormsModel::get_field( $form, $input );

			if ( ! $field ) {
				continue;
			}

			if ( $field->has_calculation() || $field->type == 'total' ) {
				$fake_lead[ $input ] = $value;
			} else {
				$fake_lead[ $input ] = $field->get_value_save_entry( $value, $form, $input, null, null );
			}
		}

		/**
		 * Flush GF cache to prevent issues from the fake lead creation from before.
		 *
		 * For posterity, issues encountered in the past are issues with conditional logic.
		 */
		GFCache::flush();

		// Default to no tabindex but allow 3rd-parties to override.
		GFCommon::$tab_index = gf_apply_filters( array( 'gform_tabindex', $form['id'] ), 0, $form );

		/* Merge HTTP referer GET params into field values for parameter [pre]population */
		$referer_parsed = parse_url( rgar( $_SERVER, 'HTTP_REFERER' ) );
		parse_str( rgar( $referer_parsed, 'query' ), $referer_get_params );

		/* The union operator for arrays is kinda funky and the order is the opposite of what you'd expect. */
		$GLOBALS['gppa-field-values'][ $form['id'] ] = apply_filters( 'gppa_field_filter_values', $field_values + $referer_get_params, $field_values, $referer_get_params, $form, $fields, $entry_id );

		foreach ( $fields as $field_id ) {

			$field = GFFormsModel::get_field( $form, $field_id );

			/**
			 * Use force_use_field_values if a lead is loaded in to ensure that Live Merge Tags are populated
			 * correctly in GravityView.
			 */
			$hydrated_field = $this->hydrate_field( $field, $form, $GLOBALS['gppa-field-values'][ $form['id'] ], $entry, $using_entry, true );

			$response['fields'][ $field_id ] = apply_filters( 'gppa_get_batch_field_html', rgar( $hydrated_field, 'html' ), rgar( $hydrated_field, 'field' ), $form, $fields, $entry_id, $hydrated_field );

			/* Add hydrated field value to field values object */
			$form_field_values = &$GLOBALS['gppa-field-values'][ $form['id'] ];
			$field_value       = rgar( $hydrated_field, 'field_value' );

			if ( ! is_array( $field_value ) ) {
				$form_field_values[ $field_id ] = $field_value;
				$fake_lead[ $field_id ]         = $field_value;
			} else {
				foreach ( $field_value as $input_id => $input_value ) {
					$form_field_values[ $input_id ] = $input_value;
					$fake_lead[ $input_id ]         = $input_value;
				}
			}

			// Remove decimal comma before replacing merge tag on number fields since GF will be interpret it
			// as a thousands separator in `GFCommon::format_number()` down the call stack.
			if ( $field->type === 'number' && $field->numberFormat === 'decimal_comma' ) {
				$fake_lead[ $field->id ] = GFCommon::clean_number( $fake_lead[ $field->id ], $field->numberFormat );
			}
		}

		$live_merge_tags = rgar( $_REQUEST, 'merge-tags', array() );

		foreach ( $live_merge_tags as $live_merge_tag ) {
			$live_merge_tag = stripslashes( $live_merge_tag );

			$live_merge_tag_value                            = $this->live_merge_tags->get_live_merge_tag_value( $live_merge_tag, $form, $fake_lead );
			$response['merge_tag_values'][ $live_merge_tag ] = gf_apply_filters(
				array(
					'gppa_ajax_merge_tag_value',
					$live_merge_tag,
				),
				$live_merge_tag_value,
				$live_merge_tag,
				$live_merge_tags
			);
		}

		wp_send_json( apply_filters( 'gppa_get_batch_field_html_response', $response ) );

	}

	/**
	 * From GFFormDisplay::get_form()
	 */
	public function get_save_and_continue_values( $token ) {

		if ( $incomplete_submission_info = GFFormsModel::get_draft_submission_values( $token ) ) {
			$submission_details_json = $incomplete_submission_info['submission'];
			$submission_details      = json_decode( $submission_details_json, true );

			return $submission_details['submitted_values'];
		}

		return array();

	}

	public function check_gppa_settings_for_user( $form_meta, $form_id, $meta_name ) {

		if ( empty( $form_meta['fields'] ) ) {
			return $form_meta;
		}

		if ( is_super_admin() ) {
			return $form_meta;
		}

		foreach ( $form_meta['fields'] as &$field ) {
			$reset_gppa_settings = array();

			if ( $this->is_population_restricted( 'values', $field ) ) {
				$reset_gppa_settings[] = 'values';
			}

			if ( $this->is_population_restricted( 'choices', $field ) ) {
				$reset_gppa_settings[] = 'choices';
			}

			if ( ! count( $reset_gppa_settings ) ) {
				continue;
			}

			/**
			 * Reset GPPA settings back to original prior to saving if a restricted object type is in use.
			 */
			$field_original = GFAPI::get_field( $form_id, $field->id );

			foreach ( $reset_gppa_settings as $populate ) {
				foreach ( $field as $key => $value ) {
					if ( strpos( $key, 'gppa-' . $populate ) === 0 ) {
						unset( $field[ $key ] );
					}
				}

				if ( is_array( $field_original ) ) {
					foreach ( $field_original as $orig_key => $orig_value ) {
						if ( strpos( $orig_key, 'gppa-' . $populate ) !== 0 ) {
							continue;
						}

						$field[ $orig_key ] = $orig_value;
					}
				}
			}
		}

		return $form_meta;

	}

	/**
	 * Check if object type for population is restricted.
	 */
	public function is_population_restricted( $populate, $field ) {

		if ( $object_type = $field[ 'gppa-' . $populate . '-object-type' ] ) {
			$id_parts = explode( ':', $object_type );

			if ( $id_parts[0] === 'field_value_object' && $field ) {
				$field = GFFormsModel::get_field( $field['formId'], $id_parts[1] );

				$values_object_type_instance = $this->get_object_type( rgar( $field, 'gppa-choices-object-type' ), $field );
			} else {
				$values_object_type_instance = $this->get_object_type( $object_type );
			}

			if ( ! $values_object_type_instance ) {
				return false;
			}

			if ( $values_object_type_instance->isRestricted() ) {
				return true;
			}
		}

		return false;

	}

	public function field_standard_settings() {
		?>
		<!-- Populated with Vue -->
		<div id="gppa"></div>
		<?php
	}

	public function add_enabled_field_class( $css_class, $field, $form ) {
		if ( rgar( $field, 'gppa-choices-enabled' ) ) {
			$css_class .= ' gppa-choices-enabled';
		}

		if ( rgar( $field, 'gppa-values-enabled' ) ) {
			$css_class .= ' gppa-values-enabled';
		}

		return $css_class;
	}

	public function get_field_values_from_request() {
		return stripslashes_deep( rgar( $_REQUEST, 'field-values', array() ) );
	}

	/**
	 * Gravity Forms attempts to prevent tampering of field values by checking a state. This is ignored for dynamically
	 * populated fields. Let's follow suit by indicating to GF that GPPA-enabled fields are dynamically populated.
	 *
	 * @param array $form
	 *
	 * @return array $form
	 */
	public function override_validation_for_populated_product_fields( $form ) {

		foreach ( $form['fields'] as &$field ) {
			if ( $this->is_field_dynamically_populated( $field ) && GFCommon::is_product_field( $field->type ) ) {
				$field->allowsPrepopulate = true;
			} elseif ( $field->type === 'consent' && $this->live_merge_tags->has_live_merge_tag( $field->checkboxLabel . $field->description ) ) {
				$field->allowsPrepopulate = true;
			}
		}

		return $form;
	}

	/**
	 * Convert the values input for dynamically-populated choice fields to use a text field instead. This is due to the
	 * fact that we don't have the dynamically populated values in the context of conditional logic. Additionally,
	 * if Form Field Values are used as filter values, you would not be able to get the results in the context of
	 * conditional logic.
	 *
	 * @see GP_Populate_Anything::conditional_logic_field_filters()
	 */
	public function conditional_logic_use_text_field() {
		if ( ! is_callable( array( 'GFForms', 'get_page' ) ) || ! GFForms::get_page() ) {
			return;
		}
		?>
		<script type="text/javascript">
			// GP Populate Anything - Replace dropdown for values with text input
			gform.addFilter( 'gform_conditional_logic_values_input', function( markup, objectType, ruleIndex, selectedFieldId, selectedValue ) {
				var field = GetFieldById( selectedFieldId );

				if ( field && field['gppa-choices-enabled'] ) {
					var inputId = objectType + '_rule_value_' + ruleIndex;
					selectedValue = selectedValue ? selectedValue.replace( /'/g, '&#039;' ) : '';
					markup = '<input ' +
						'type="text" ' +
						'placeholder="' + gf_vars.enterValue + '" ' +
						'class="gfield_rule_select gfield_rule_input" ' +
						'style="display: block" ' +
						'id="'+ inputId + '" ' +
						'name="'+ inputId + '" ' +
						'value="' + selectedValue.replace( /'/g, '&#039;' ) + '" ' +
						'onchange="SetRuleProperty( \'' + objectType + '\', ' + ruleIndex + ', \'value\', this.value );" ' +
						'onkeyup="SetRuleProperty( \'' + objectType + '\', ' + ruleIndex + ', \'value\', this.value );">';
				}

				return markup;
			} );
		</script>
		<?php
	}

	/**
	 * Converts dropdown for fields with dynamically populated choices to use a text input as the choices will not
	 * be correct.
	 *
	 * This filter differs from GP_Populate_Anything::conditional_logic_use_text_field() as it affects field filters
	 * used in locations such as GravityFlow's Conditional Routing option.
	 *
	 * @see GP_Populate_Anything::conditional_logic_use_text_field()
	 *
	 * @param $field_filters array The form field, entry properties, and entry meta filter settings.
	 * @param $form array The form object.
	 *
	 * @see [gform_field_filters](https://docs.gravityforms.com/gform_field_filters/)
	 *
	 * @return mixed
	 */
	public function conditional_logic_field_filters( $field_filters, $form ) {
		foreach ( $field_filters as &$field_filter ) {
			$field = GFAPI::get_field( $form, $field_filter['key'] );
			if ( $field && $field->{'gppa-choices-enabled'} ) {
				unset( $field_filter['values'] );
			}
		}
		return $field_filters;
	}

}

function gp_populate_anything() {
	return GP_Populate_Anything::get_instance();
}

GFAddOn::register( 'GP_Populate_Anything' );

<?php
if ( ! class_exists( 'GP_Plugin' ) ) {
	return;
}

class GP_Advanced_Select extends GP_Plugin {

	private static $instance = null;

	protected $_version     = GP_ADVANCED_SELECT_VERSION;
	protected $_path        = 'gp-advanced-select/gp-advanced-select.php';
	protected $_full_path   = __FILE__;
	protected $_slug        = 'gp-advanced-select';
	protected $_title       = 'Gravity Wiz Advanced Select';
	protected $_short_title = 'Advanced Select';

	public static function get_instance() {
		if ( self::$instance === null ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function minimum_requirements() {
		return array(
			'gravityforms' => array(
				'version' => '2.5',
			),
			'wordpress'    => array(
				'version' => '5.0',
			),
			'plugins'      => array(
				'gravityperks/gravityperks.php' => array(
					'name'    => 'Gravity Perks',
					'version' => '2.0',
				),
			),
		);
	}

	public function init() {

		parent::init();

		load_plugin_textdomain( 'gp-advanced-select', false, basename( dirname( __file__ ) ) . '/languages/' );

		add_action( 'gperk_field_settings', array( $this, 'field_settings_ui' ) );

		// Querying
		add_filter( 'gppa_specific_choice_query_args', array( $this, 'remove_search_value_filter_from_specific_choice_query' ), 10, 3 );

		// Override how Populate Anything populates Advanced Select fields.
		add_action( 'gppa_pre_populate_field', array( $this, 'populate_field_lazy' ), 10, 8 );

		// Override the paged query limit for fields that are lazy loaded but not using the search value
		add_filter( 'gppa_query_limit_paged', array( $this, 'maybe_change_paged_query_limit_to_default' ), 10, 3 );

		// Bypass state validation for fields using the Advanced Select Search Value
		add_filter( 'gform_pre_validation', array( $this, 'disable_state_validation_for_select_search_value' ) );

		/*
		 * We use gform_pre_render, so we can register before the Chosen script in
		 * GFFormDisplay::register_form_init_scripts() since it is not hooked to gform_register_init_scripts.
		 *
		 * Registering before Chosen is required to enable us to add a class prior to Chosen initializing to prevent
		 * Chosen from initializing in gformInitChosenFields().
		 */
		add_filter( 'gform_pre_render', array( $this, 'add_init_script' ), 10, 2 );

		// AJAX
		add_action( 'wp_ajax_gp_advanced_select_get_gppa_results', array( $this, 'ajax_get_gppa_results' ) );
		add_action( 'wp_ajax_nopriv_gp_advanced_select_get_gppa_results', array( $this, 'ajax_get_gppa_results' ) );

		// Filter Gravity Forms select choices to include JetSloth image choices
		add_filter( 'gform_field_choice_markup_pre_render', array( $this, 'add_image_choices_to_select' ), 10, 4 );

		// JetSloth Image Choices integration
		add_filter( 'gfic_is_supported_single_value_field', array( $this, 'image_choices_allow_select_input_type' ), 10, 2 );

		add_filter( 'gform_field_input', array( $this, 'add_gpadvs_field_preview_markup' ), 10, 5 );

		add_filter( 'gform_field_css_class', array( $this, 'add_gpadvs_css_class' ), 10, 3 );

	}
	public function add_gpadvs_field_preview_markup( $input, $field, $value, $entry_id, $form_id ) {
		if ( ! GFCommon::is_form_editor() || ! in_array( $field->get_input_type(), array( 'select', 'multiselect' ) ) ) {
			return $input;
		}

		$form         = GFAPI::get_form( $form_id );
		$input_prefix = "input_{$form['id']}_{$field['id']}";
		$select_id    = 'input_' . $form['id'] . '_' . 'gpadvs_preview_select';

		ob_start();
		if ( $field['type'] === 'select' ) {
			?>
			<div class="ginput_container ginput_container_select" inert>
				<select name="input_<?php echo $field['id']; ?>" id="<?php echo $select_id; ?>" class="large tomselected ts-hidden-accessible" aria-invalid="false" tabindex="-1">
				</select>
				<div class="ts-wrapper large single plugin-change_listener plugin-remove_button">
					<div class="ts-control">
						<input type="select-one" autocomplete="off" size="1" tabindex="0" role="combobox" aria-haspopup="listbox" aria-expanded="false" aria-controls="<?php echo $input_prefix; ?>-ts-dropdown" id="<?php echo $input_prefix; ?>-ts-control" aria-labelledby="<?php echo $input_prefix; ?>-ts-label" placeholder="" aria-activedescendant="<?php echo $input_prefix; ?>-opt-1">
					</div>
				</div>
			</div>

			<?php
		} else {
			?>
			<div class="ginput_container ginput_container_multiselect" inert>
				<select multiple="multiple" size="7" name="input_<?php echo $field['id']; ?>[]" id="<?php echo $select_id; ?>" class="large tomselected ts-hidden-accessible" aria-invalid="false" tabindex="-1">
				</select>
				<div class="ts-wrapper large multi plugin-change_listener plugin-remove_button has-items">
					<div class="ts-control">
						<input type="select-multiple" autocomplete="off" size="1" tabindex="0" role="combobox" aria-haspopup="listbox" aria-expanded="false" aria-controls="<?php echo $input_prefix; ?>-ts-dropdown" id="<?php echo $input_prefix; ?>-ts-control" aria-labelledby="<?php echo $input_prefix; ?>-ts-label" disabled="disabled">
					</div>
				</div>
			</div>
			<?php
		}
		$gapdvs_ms_preview = ob_get_clean();

		$default_preview = $field->get_field_input( $form, $value, GFAPI::get_entry( $entry_id ) );

		// Hide the default preview if the field is using GP Advanced Select or the
		// GP Advanced Select preview if the field is not using GP Advanced Select.
		$pattern     = '/^(\s*<div\s)/';
		$replacement = '$1 style="display:none"';
		if ( $field['gpadvsEnable'] ) {
			$default_preview = preg_replace( $pattern, $replacement, $default_preview );
		} else {
			$gapdvs_ms_preview = preg_replace( $pattern, $replacement, $gapdvs_ms_preview );
		}

		return $default_preview . $gapdvs_ms_preview;
	}

	public function add_gpadvs_css_class( $classes, $field, $form ) {
		if ( in_array( $field->get_input_type(), array( 'select', 'multiselect' ) ) && $field['gpadvsEnable'] === true ) {
			$classes .= ' gform-theme__disable';
		}

		return $classes;
	}

	public function add_init_script( $form ) {
		$fields = $this->get_fields( $form );

		if ( empty( $fields ) ) {
			return $form;
		}

		// Must manually require since plugins like Partial Entries and Nested Forms call gform_pre_render outside of the rendering context.
		require_once( GFCommon::get_base_path() . '/form_display.php' );

		foreach ( $fields as $field ) {
			$lazy_load          = ! ! $this->is_lazy_loaded_field( $field );
			$using_search_value = ! ! $this->is_field_using_search_value_in_gppa_filter( $field );

			$init_args = array(
				'formId'                 => $field['formId'],
				'fieldId'                => $field['id'],
				'lazyLoad'               => $lazy_load,
				'usingSearchValue'       => $using_search_value,
				'hasImageChoices'        => $this->is_field_using_image_choices( $field ),
				'fieldType'              => $field->get_input_type(),
				'minSearchLength'        => 3,
				'ignoreEmptySearchValue' => false,
				'placeholder'            => $field['placeholder'],
			);

			/**
			 * Allows you to filter the arguments passed to GP Advanced Select
			 * Field JS controller classes.
			 *
			 * This is useful for things such as modifying the minimum search length,
			 * or enabling lazy loading for fields.
			 *
			 * @param array $init_args Arguments to use when initializing the field's JS.
			 * @param array $form Current form.
			 * @param array $field Current field.
			 *
			 * @since 1.0
			 */
			$init_args = gf_apply_filters(
				array( 'gpadvs_js_init_args', $field['formId'], $field['id'] ),
				$init_args,
				$form,
				$field
			);

			$script = 'new GPAdvancedSelect(
				' . json_encode( $init_args ) . ',
			);';

			$slug = 'gp_advanced_select_' . $field['formId'] . '_' . $field['id'];

			GFFormDisplay::add_init_script( $field['formId'], $slug, GFFormDisplay::ON_PAGE_RENDER, $script );
		}

		return $form;
	}

	/**
	 * Gets the fields using Advanced Select Filters that need to be initialized.
	 *
	 * @param array $form A Gravity Form to get the Advanced Select Filter fields from.
	 *
	 * @return GF_Field[]
	 */
	public function get_fields( $form ) {
		if ( empty( rgar( $form, 'fields' ) ) || ! is_array( $form['fields'] ) ) {
			return array();
		}

		$fields = array();

		foreach ( $form['fields'] as $field ) {
			if ( $this->is_advanced_select_field( $field ) ) {
				$fields[] = $field;
			}
		}

		return $fields;
	}

	/**
	 * Determine whether the field is an Advanced Select field.
	 *
	 * @param GF_Field $field Field to check.
	 *
	 * @return boolean
	 */
	public function is_advanced_select_field( $field ) {
		return rgar( $field, 'gpadvsEnable' ) && in_array( $field->get_input_type(), array(
			'select',
			'multiselect',
			'address',
		) );
	}

	/**
	 * Determine whether a field uses Populate Anything and should be lazy loaded by checking if the field
	 * simply has it enabled or if they are using the "Advanced Select Search Value" filter special value.
	 */
	public function is_lazy_loaded_field( $field ) {
		$is_using_search_value = $this->is_field_using_search_value_in_gppa_filter( $field );
		$is_using_lazy_load    = $this->is_field_using_lazy_load_with_gppa( $field );

		/**
		 * Allows you to customize whether or not a field should lazy load search results.
		 *
		 * @param boolean $is_lazy_loaded Whether or not to lazy load the field.
		 * @param GF_Field|array $field The field to check.
		 *
		 * @since 1.0-beta-1
		 */
		return gf_apply_filters(
			array( 'gpadvs_is_lazy_loaded_field', $field->formId, $field->id ),
			$is_using_search_value || $is_using_lazy_load,
			$field
		);
	}

	/**
	 * Determine whether a field uses Populate Anything and the "Advanced Select Search Value"
	 *
	 * @param GF_Field|array $field
	 */
	public function is_field_using_search_value_in_gppa_filter( $field ) {
		$filter_groups = rgar( $field, 'gppa-choices-filter-groups' );

		if (
			! $this->is_advanced_select_field( $field )
			|| ! function_exists( 'gp_populate_anything' )
			|| ! rgar( $field, 'gppa-choices-enabled' )
			|| empty( $filter_groups )
		) {
			return false;
		}

		foreach ( $filter_groups as $filters ) {
			foreach ( $filters as $filter ) {
				if ( rgar( $filter, 'value' ) === 'special_value:advanced_select_search_value' ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Determine whether lazy loading is explicitly enabled for Populate Anything.
	 *
	 * @param GF_Field|array $field
	 */
	public function is_field_using_lazy_load_with_gppa( $field ) {
		if (
			! $this->is_advanced_select_field( $field )
			|| ! function_exists( 'gp_populate_anything' )
			|| ! rgar( $field, 'gppa-choices-enabled' )
		) {
			return false;
		}

		return ! ! rgar( $field, 'gpadvsGPPALazyLoad' );
	}

	/**
	 * Hydrates a field using Advanced Select Search Value as filter for initial load which includes re-adding selected
	 * values prior to AJAX request, and more.
	 *
	 * A lot of the normal logic for hydration does not apply to fields that are loading results directly using JSON.
	 *
	 * Specifically:
	 *   * Options are fetched after load, initial hydration of choices isn't required
	 *   * We do, however, need to populate selected choices using labels/values provided in a POSTed input, so
	 *     they can be reselected if a validation error occurs, on multi-page forms, with Save & Continue, etc.
	 *   * Value population will work very differently (if at all)
	 *
	 * @param array|null $hydrated_field
	 * @param array|GF_Field $field
	 * @param array $form
	 * @param array $field_values
	 * @param array $entry
	 * @param boolean $force_use_field_value
	 * @param boolean $include_html
	 * @param boolean $run_pre_render
	 */
	public function populate_field_lazy( $hydrated_field, $field, $form, $field_values, $entry, $force_use_field_value, $include_html, $run_pre_render ) {
		if ( ! $this->is_lazy_loaded_field( $field ) ) {
			return $hydrated_field;
		}

		/**
		 * Re-add selected items as choices for initial load to support validation failures, Save & Continue, etc.
		 */
		$field->choices = array();
		$selected_items = $this->get_selected_items( $field, $form, $field_values );

		foreach ( $selected_items as $selected ) {
			$field->choices[] = $selected;
		}

		/**
		 * Handle setting $field_value
		 *
		 * @todo extract a lot of the value-handling code in GP_Populate_Anything::hydrate_field() so it can be used here
		 *   if needed. Especially things like gp_populate_anything()->get_selected_choices() to support Value population if we ever do.
		 */
		$field_value = rgar( gp_populate_anything()->get_posted_field_values( $form ), $field->id );

		if ( is_string( $field_value ) && ( rgar( $field, 'storageType' ) === 'json' && GFCommon::is_json( $field_value ) ) ) {
			$field_value = GFCommon::maybe_decode_json( $field_value );
		}

		$hydrated_field = array(
			'field'       => $field,
			'field_value' => $field_value,
			'lead_id'     => rgar( $entry, 'id' ),
			'form_id'     => $form['id'],
			'form'        => $form,
		);

		if ( $include_html ) {
			// Documentation for this filter can be found inside GP Populate Anything.
			$input_html = apply_filters( 'gppa_hydrate_input_html', GFCommon::get_field_input( $field, $hydrated_field['field_value'], rgar( $entry, 'id' ), $form['id'], $form ), $field, $form );

			// Documentation for this filter can be found inside GP Populate Anything.
			$hydrated_field['html'] = apply_filters( 'gppa_hydrate_field_html', $input_html, $form, $hydrated_field, $field );
		}

		return $hydrated_field;
	}

	/**
	 * Gravity Forms attempts to prevent tampering of field values by checking a state.
	 *
	 * We need to disable this as the choices will not be loaded in during form load or validation if using
	 * the Advanced Select Search Value.
	 *
	 * @param array $form
	 *
	 * @return array $form
	 */
	public function disable_state_validation_for_select_search_value( $form ) {
		foreach ( $form['fields'] as &$field ) {
			if ( ! empty( $this->is_lazy_loaded_field( $field ) ) ) {
				$field->validateState = false;
			}
		}

		return $form;
	}

	/**
	 * Removes the search value filter while searching for a specific choice as the search value will not be present.
	 *
	 * @param array $query_args
	 * @param array $form
	 * @param GF_Field $field
	 *
	 * @return array
	 */
	public function remove_search_value_filter_from_specific_choice_query( $query_args, $form, $choice_field ) {
		if ( ! $this->is_advanced_select_field( $choice_field ) ) {
			return $query_args;
		}

		foreach ( $query_args['filter_groups'] as &$filters ) {
			foreach ( $filters as $filter_index => &$filter ) {
				/* Remove the filter that searches using the Advanced Select Search Value as we don't have that value handy */
				if ( $filter['value'] === 'special_value:advanced_select_search_value' ) {
					unset( $filters[ $filter_index ] );
				}
			}
		}

		return $query_args;
	}

	/**
	 * Gets the selected items (value and text) from either POST, Draft Submission, or the Entry Meta so the select can
	 * be appropriately re-populated without a query on the initial load.
	 *
	 * @param GF_Field $field
	 * @param array $form
	 * @param array $field_values
	 *
	 * @return array
	 */
	public function get_selected_items( $field, $form, $field_values ) {
		$field_value = rgar( $field_values, $field->id );

		// Fallback to dynamically populated value if present
		if ( rgblank( $field_value ) && gp_populate_anything()->is_field_dynamically_populated( $field, 'values' ) ) {
			$dynamic_value = gp_populate_anything()->get_input_values( $field, 'value', $field_values, null, $form );

			if ( ! rgblank( $dynamic_value ) ) {
				$field_value = $dynamic_value;
			}

			if ( in_array( $field->type, GP_Populate_Anything::get_multi_selectable_choice_field_types(), true ) ) {
				$field_value = gp_populate_anything()->get_selected_choices( $field, $field_values );
			}

			if ( ! rgblank( $field_value ) ) {
				$GLOBALS['gppa-field-values'][ $form['id'] ][ $field->id ] = $field_value;
			}
		}

		if ( ! rgblank( $field_value ) ) {
			$choice_value_template = rgars( $field, 'gppa-choices-templates/value' );

			// If the field has JSON storage type, decode it.
			if ( rgar( $field, 'storageType' ) === 'json' ) {
				$field_value = $this->maybe_decode_json( $field_value );
			}

			$selected_choices = gp_populate_anything()->get_specific_choices( $form, $field, $choice_value_template, $field_value, $field_values );

			if ( $selected_choices ) {
				return $selected_choices;
			}
		}

		return array();
	}

	/**
	 * Registers scripts using Gravity Forms Add-on framework.
	 *
	 * @return array
	 */
	public function scripts() {
		return array_merge( parent::scripts(), array(
			array(
				'handle'    => 'gp-advanced-select',
				'src'       => $this->get_base_url() . '/js/built/gp-advanced-select.js',
				'version'   => $this->_version,
				'deps'      => array( 'jquery' ),
				'in_footer' => true,
				'enqueue'   => array(
					array( $this, 'should_enqueue' ),
				),
				'callback'  => array( $this, 'localize_scripts' ),
			),
			array(
				'handle'    => 'gp-advanced-select-form-editor',
				'src'       => $this->get_base_url() . '/js/built/gp-advanced-select-form-editor.js',
				'version'   => $this->_version,
				'deps'      => array( 'gform_gravityforms', 'jquery' ),
				'in_footer' => true,
				'enqueue'   => array(
					array( 'admin_page' => array( 'form_editor' ) ),
				),
				'callback'  => array( $this, 'localize_admin_scripts' ),
			),
		) );
	}

	/**
	 * Registers styles using Gravity Forms Add-on framework.
	 *
	 * @return array
	 */
	public function styles() {
		return array_merge( parent::scripts(), array(
			array(
				'handle'  => 'gp-advanced-select-tom-select',
				'src'     => $this->get_base_url() . '/styles/tom-select.bootstrap5.css',
				'version' => $this->_version,
				'enqueue' => array(
					array( $this, 'should_enqueue' ),
					// load in the form editor as well so that we can use if for the Multi Select field preview styles
					array( 'admin_page' => array( 'form_editor' ) ),
				),
			),
			array(
				'handle'  => 'gp-advanced-select-frontend',
				'src'     => $this->get_base_url() . '/styles/frontend.css',
				'version' => $this->_version,
				'enqueue' => array(
					array( $this, 'should_enqueue' ),
				),
			),
			array(
				'handle'  => 'gp-advanced-select-form-editor',
				'src'     => $this->get_base_url() . '/styles/form-editor.css',
				'version' => $this->_version,
				'enqueue' => array(
					array( 'admin_page' => array( 'form_editor' ) ),
				),
			),
		) );
	}

	/**
	 * @param array $form Current form.
	 *
	 * @return boolean
	 */
	public function should_enqueue( $form ) {
		return ! empty( $this->get_fields( $form ) );
	}

	/**
	 * @return void
	 */
	public function localize_scripts() {
		wp_localize_script( 'gp-advanced-select', 'GPADVS', array(
			'strings' => array(
				'remove_this_item' => __( 'Remove this item', 'gp-advanced-select' ),
			),
		) );
	}

	/**
	 * @return void
	 */
	public function localize_admin_scripts() {
		wp_localize_script( 'gp-advanced-select-form-editor', 'GPADVS_FORM_EDITOR', array(
			'strings' => array(
				'not_compat_with_enhanced_ui' => __( 'GP Advanced Select requires that Enhanced UI is disabled.', 'gp-advanced-select' ),
			),
		) );
	}

	/**
	 * Returns results for Tom Select
	 *
	 * @return void
	 */
	public function ajax_get_gppa_results() {
		$data = gp_populate_anything()::maybe_decode_json( WP_REST_Server::get_raw_data() );

		// Copy $data onto $_REQUEST and $_POST
		$_REQUEST = array_merge( $_REQUEST, $data );
		$_POST    = array_merge( $_POST, $data );

		check_ajax_referer( 'gppa', 'security' );

		// Only start replacing special value here rather than all the time to help improve security.
		add_filter( 'gppa_special_value', array( $this, 'replace_special_value' ), 10, 3 );

		$form = GFAPI::get_form( rgpost( 'form-id' ) );

		$field_values = gp_populate_anything()->get_posted_field_values( $form );
		$field        = GFAPI::get_field( $form, rgpost( 'fieldId' ) );

		$page    = rgar( $_REQUEST, 'page' ) ? rgar( $_REQUEST, 'page' ) : 1;
		$choices = gp_populate_anything()->get_input_choices( $field, $field_values, 'choices', $page );

		$object_type = gp_populate_anything()->get_object_type( rgar( $field, 'gppa-choices-object-type' ), $field );
		$limit       = gp_populate_anything()->get_query_limit( $object_type, $field, true );
		$has_more    = false;

		/*
		 * We check if there are more results by showing 1 less than the actual query limit and keeping the offset
		 * also 1 less than limit. If the number of results returned matches the limit, then we know there are more
		 * results to load.
		 *
		 * Example:
		 * 	- Page 1: Offset 0, Limit 50, Results 51, Displayed Results 50, Additional page detected
		 *  - Page 2: Offset 50, Limit 51, Results 25, Displayed Results 25, No more pages
		 */
		if ( count( $choices ) === $limit ) {
			$has_more = true;

			array_pop( $choices );
		}

		if ( count( $choices ) === 1 && rgars( $choices, '0/gppaErrorChoice' ) ) {
			$choices = array();
		}

		$results = array_map( function( $choice ) use ( $field ) {
			$option = array(
				'id'   => $choice['value'],
				'text' => $choice['text'],
			);

			if ( ! empty( $choice['imageChoices_image'] ) && $this->is_field_using_image_choices( $field ) ) {
				$option['imageSrc'] = $choice['imageChoices_image'];
			}

			return $option;
		}, $choices );

		wp_send_json( array(
			'results'    => $results,
			'pagination' => array(
				'nextPage' => $has_more ? $page + 1 : null,
			),
		) );
	}

	/**
	 * Changes the paged query limit back to the normal query limit if a field is using lazy loading but not
	 * using the Advanced Select Search Value in a filter.
	 *
	 * @param int $limit
	 * @param array $field
	 * @param string $object_type
	 */
	public function maybe_change_paged_query_limit_to_default( $limit, $object_type, $field ) {
		if ( $this->is_field_using_search_value_in_gppa_filter( $field ) ) {
			return $limit;
		}

		return gp_populate_anything()->get_query_limit( $object_type, $field );
	}

	/**
	 * Replaces the special value with the provided query.
	 *
	 * @param mixed $value
	 * @param string $special_value
	 * @param string[] $special_value_parts
	 *
	 * @return mixed
	 */
	public function replace_special_value( $value, $special_value, $special_value_parts ) {
		if ( $special_value !== 'advanced_select_search_value' ) {
			return $value;
		}

		return rgpost( 'term' );
	}

	/**
	 * From GFFormDisplay::get_form()
	 */
	public function get_save_and_continue_values( $token ) {
		$incomplete_submission_info = GFFormsModel::get_draft_submission_values( $token );

		if ( $incomplete_submission_info ) {
			$submission_details_json = $incomplete_submission_info['submission'];
			$submission_details      = json_decode( $submission_details_json, true );

			return $submission_details['submitted_values'];
		}

		return array();
	}

	## Admin field settings

	public function field_settings_ui( $position ) {
		?>

		<li class="gpadvs-field-setting field_setting" style="display:none;">
			<div>
				<input type="checkbox" value="1" id="gpadvs-enable" />
				<label for="gpadvs-enable" class="inline">
					<?php _e( 'Enable Advanced Select', 'gp-advanced-select' ); ?>
					<?php gform_tooltip( $this->_slug . '_enable' ); ?>
				</label>
			</div>

			<div id="gpadvs-enable-child-settings" class="gp-child-settings gpadvs-child-settings">
				<div class="gp-row" id="gpadvs-gppa-lazy-load-row">
					<input type="checkbox" value="1" id="gpadvs-gppa-lazy-load" />
					<label for="gpadvs-gppa-lazy-load">
						<?php _e( 'Lazy Load Populated Choices', 'gp-advanced-select' ); ?>
						<?php gform_tooltip( $this->_slug . '_gppa_lazy_load' ); ?>
					</label>
				</div>
			</div>
		</li>

		<?php
	}

	public function tooltips( $tooltips ) {
		$tooltips[ $this->_slug . '_enable' ] = sprintf(
			'<h6>%s</h6> %s',
			__( 'GP Advanced Select', 'gp-advanced-phone-field' ),
			__( 'Enable GP Advanced Select for the current field. Adds improved UI and accessibility.', 'gp-advanced-phone-field' )
		);

		$tooltips[ $this->_slug . '_gppa_lazy_load' ] = sprintf(
			'<h6>%s</h6> %s',
			__( 'Lazy Load Dynamically Populated Choices', 'gp-advanced-phone-field' ),
			__( 'Unless the dynamically populated choices have a filter value using the "Advanced Select Search Value",
			choices will be loaded when the form is rendered.<br /><br />Enable lazy load to load the choices when the
			field is focused rather than during form render.', 'gp-advanced-phone-field' )
		);

		return $tooltips;
	}

	/**
	 * Check if a form is using Advanced Select with Image Choices.
	 *
	 * @param array $form
	 *
	 * @return bool
	 */
	public function is_form_using_image_choices( $form ) {
		if ( empty( rgar( $form, 'fields' ) ) || ! is_array( $form['fields'] ) ) {
			return false;
		}

		foreach ( $form['fields'] as $field ) {
			if ( $this->is_field_using_image_choices( $field ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Check if a field is using Advanced Select with Image Choices.
	 *
	 * @param GF_Field $field
	 *
	 * @return bool
	 */
	public function is_field_using_image_choices( $field ) {
		return $this->is_advanced_select_field( $field ) && rgar( $field, 'imageChoices_enableImages' );
	}

	/**
	 * Adds JetSloth Image Choices images to select options using data-image-src attribute.
	 *
	 * @param string $choice_markup
	 * @param array $choice
	 * @param GF_Field $field
	 * @param string $value
	 */
	public function add_image_choices_to_select( $choice_markup, $choice, $field, $value ) {
		if ( ! class_exists( 'GFImageChoices' ) ) {
			return $choice_markup;
		}

		if ( ! $this->is_field_using_image_choices( $field ) ) {
			return $choice_markup;
		}

		if ( rgar( $field, 'imageChoices_enableImages' ) ) {
			$img = rgar( $choice, 'imageChoices_image' );
			$img = ! empty( $img ) ? str_replace( '$', '\$', $img ) : '';

			// Replace <option with <option data-image-src="$img"
			$choice_markup = preg_replace( '/<option/', '<option data-image-src="' . $img . '"', $choice_markup );
		}

		return $choice_markup;
	}

	/**
	 * Adds select fields using Advanced Select as supported Image Choices single value fields.
	 *
	 * @param boolean $is_supported
	 * @param GF_Field $field
	 */
	public function image_choices_allow_select_input_type( $is_supported, $field ) {
		if ( ! $this->is_advanced_select_field( $field ) && $field->get_input_type() === 'select' ) {
			return $is_supported;
		}

		return true;
	}

	/**
	 * Adds multiselect fields using Advanced Select as supported Image Choices multi-value fields.
	 *
	 * @todo This currently does not work due to values being JSON encoded. Need to collaborate with Jetty Boys
	 *      add_filter( 'gfic_is_supported_multi_value_field', array( $this, 'image_choices_allow_multiselect_input_type' ), 10, 2 );
	 *
	 * @param boolean $is_supported
	 * @param GF_Field $field
	 */
	public function image_choices_allow_multiselect_input_type( $is_supported, $field ) {
		if ( ! $this->is_advanced_select_field( $field ) && $field->get_input_type() === 'multiselect' ) {
			return $is_supported;
		}

		return true;
	}


}

function gp_advanced_select() {
	return GP_Advanced_Select::get_instance();
}

GFAddOn::register( 'GP_Advanced_Select' );

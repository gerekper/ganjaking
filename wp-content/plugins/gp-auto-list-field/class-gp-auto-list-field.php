<?php

if ( ! class_exists( 'GP_Plugin' ) ) {
	return;
}

class GP_Auto_List_Field extends GP_Plugin {

	private static $instance = null;

	protected $_version     = GP_AUTO_LIST_FIELD_VERSION;
	protected $_path        = 'gp-auto-list-field/gp-auto-list-field.php';
	protected $_full_path   = __FILE__;
	protected $_slug        = 'gp-auto-list-field';
	protected $_title       = 'Gravity Forms Auto List Field';
	protected $_short_title = 'Auto List Field';

	/**
	 * Runtime cache of the List fields in a given form.
	 *
	 * @var array Associative array containing form IDs as keys with the values being arrays containing the List fields
	 *   for the form.
	 */
	public $form_list_fields_cache = array();

	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function minimum_requirements() {
		return array(
			'gravityforms' => array(
				'version' => '2.0',
			),
		);
	}

	public function init() {

		parent::init();

		// Admin
		add_action( 'gperk_field_settings', array( $this, 'field_settings_ui' ) );
		add_filter( 'gform_custom_merge_tags', array( $this, 'add_list_fields_to_calc_merge_tags_select' ), 10, 4 );

		// Frontend
		add_action( 'gform_register_init_scripts', array( $this, 'register_init_scripts' ) );

		// Submission
		add_filter( 'gform_save_field_value', array( $this, 'maybe_truncate_list_field_rows' ), 10, 5 );

		// Calculations
		add_filter( 'gform_calculation_formula', array( $this, 'modify_calculation_formula' ), 5, 4 ); /* Replace our merge tags first; give other plugins plenty of options to fire after us. */

	}

	/**
	 * Registers toolips that will be displayed using gform_tooltip().
	 *
	 * @see gform_tooltip
	 * @param array $tooltips
	 *
	 * @return array Tooltips for the perk.
	 */
	public function tooltips( $tooltips ) {
		$tooltips['gpalf_enable']       = sprintf( '<h6>%s</h6> %s', __( 'Enable Auto List Field', 'gp-auto-list-field' ), __( 'Automatically set the number of rows for this List field by the value entered into another field.', 'gp-auto-list-field' ) );
		$tooltips['gpalf_source_field'] = sprintf( '<h6>%s</h6> %s', __( 'Trigger Field', 'gp-auto-list-field' ), __( 'Specify which field\'s value should automatically set the number of rows for this List field.', 'gp-auto-list-field' ) );

		return $tooltips;
	}

	/**
	 * Registers (and enqueues) scripts needed for both the admin and frontend.
	 *
	 * @return array[] The registered scripts for the perk.
	 */
	public function scripts() {
		$scripts = array(
			array(
				'handle'  => 'gp-auto-list-field',
				'src'     => $this->get_base_url() . '/js/gp-auto-list-field.js',
				'version' => $this->_version,
				'deps'    => array( 'jquery' ),
				'enqueue' => array(
					array( $this, 'is_form_with_auto_list_field' ),
				),
			),
			array(
				'handle'  => 'gp-auto-list-field-calc',
				'src'     => $this->get_base_url() . '/js/gp-auto-list-field-calc.js',
				'version' => $this->_version,
				'deps'    => array( 'jquery' ),
				'enqueue' => array(
					array( $this, 'is_form_with_list_count_merge_tag' ),
				),
			),
			array(
				'handle'    => 'gp-auto-list-field-admin',
				'src'       => $this->get_base_url() . '/js/gp-auto-list-field-admin.js',
				'version'   => $this->_version,
				'deps'      => array( 'jquery' ),
				'in_footer' => true,
				'enqueue'   => array(
					array( 'admin_page' => array( 'form_editor' ) ),
				),
				'callback'  => array( $this, 'localize_admin_script' ),
			),
		);

		return array_merge( parent::scripts(), $scripts );
	}

	/**
	 * Localizes the admin script to output variable containing information needed by the admin script such as what
	 * field types are supported.
	 *
	 * @return void
	 */
	public function localize_admin_script() {
		wp_localize_script( 'gp-auto-list-field-admin', 'GPALFAdminData', array(
			'supportedFieldTypes' => $this->get_supported_source_field_types(),
		) );
	}

	/**
	 * Adds the init scripts to initialize the JavaScript class to control List fields.
	 *
	 * @param array $form The current form
	 *
	 * @return void
	 */
	public function register_init_scripts( $form ) {

		foreach ( $form['fields'] as $field ) {

			if ( ! $this->is_auto_list_field( $field ) ) {
				continue;
			}

			/**
			 * Filter the options that will be used to initialize the JS functionality for the frontend.
			 *
			 * @since 0.9.2
			 *
			 * @param array $args {
			 *
			 *     @type int  $formId                The current form ID.
			 *     @type int  $sourceFieldId         The ID of the field whose value will set the number of List field rows.
			 *     @type int  $sourceFieldSelector   The jQuery selector for the source field.
			 *     @type int  $targetFieldId         The ID of the List field for which the rows will be automatically set.
			 *     @type int  $targetFieldSelector   The jQuery selector of the target field.
			 *     @type bool $shouldHideListButtons Indicate whether the List field's add/remove buttons should be visible. Defaults to true.
			 *
			 * }
			 */
			$args = gf_apply_filters( array( 'gpalf_init_script_args', $form['id'], $field->id ), array(
				'formId'                => $field->formId,
				'sourceFieldId'         => $field->id,
				'sourceFieldSelector'   => $this->get_source_field_selector( GFFormsModel::get_field( $form, $field->gpalfSourceField ) ),
				'targetFieldId'         => $field->gpalfTargetField,
				'targetFieldSelector'   => $this->get_target_field_selector( $field ),
				'shouldHideListButtons' => $this->should_hide_list_buttons( $field, $form ),
			) );

			$script = 'new GPAutoListField( ' . json_encode( $args ) . ' );';
			$slug   = "gp_auto_list_field_{$field->id}";

			GFFormDisplay::add_init_script( $form['id'], $slug, GFFormDisplay::ON_PAGE_RENDER, $script );

		}
	}

	/**
	 * @param GF_Field_List $field
	 * @param array $form
	 *
	 * @return boolean Whether the Add/Remove buttons should be hidden for the controlled List field.
	 */
	public function should_hide_list_buttons( $field, $form ) {
		/**
		 * Specify whether List field add/remove buttons should be visible. Defaults to true.
		 *
		 * @param bool $should_hide Should List field add/remove buttons be hidden?
		 * @param GF_Field_List $field Current List field.
		 * @param array $form Current form.
		 *
		 * @since 0.9.2
		 *
		 */
		return gf_apply_filters( array(
			'gpalf_should_hide_list_buttons',
			$form['id'],
			$field->id,
		), true, $field, $form );
	}

	/**
	 * @param GF_Field $field The source field.
	 *
	 * @return string|number The source input ID.
	 */
	public function get_source_input_id( $field ) {
		switch ( $field->get_input_type() ) {
			case 'singleproduct':
				$id = "{$field->id}.3";

				break;
			default:
				$id = $field->id;
		}

		return $id;
	}

	/**
	 * @param GF_Field $field The source field.
	 *
	 * @return string CSS selector for the source field that's used to control the number of rows.
	 */
	public function get_source_field_selector( $field ) {

		switch ( $field->get_input_type() ) {
			case 'singleproduct':
				if ( version_compare( GFForms::$version, '2.5-beta-1', '>=' ) ) {
					$selector = "#input_{$field->formId}_{$field->id}_1";
				} else {
					$selector = "#ginput_quantity_{$field->formId}_{$field->id}";
				}

				break;
			default:
				$selector = "#input_{$field->formId}_{$field->id}";
		}

		return $selector;
	}

	/**
	 * @param GF_Field_List $field Current List field.
	 *
	 * @return string CSS selector of the List field.
	 */
	public function get_target_field_selector( $field ) {
		return "#field_{$field->formId}_{$field->id}";
	}

	/**
	 * Adds List fields to the Formula merge tag drop down in the Form Editor.
	 *
	 * @param array $merge_tags
	 * @param int $form_id
	 * @param GF_Field[] $fields
	 * @param string $element_id
	 *
	 * @return array
	 */
	public function add_list_fields_to_calc_merge_tags_select( $merge_tags, $form_id, $fields, $element_id ) {
		if ( $element_id !== 'field_calculation_formula' ) {
			return $merge_tags;
		}

		foreach ( $fields as $field ) {
			if ( in_array( GFFormsModel::get_input_type( $field ), array( 'list' ), true ) ) {
				$merge_tags[] = array(
					'tag'   => sprintf( '{%s:%d:count}', GFCommon::get_label( $field ), $field['id'] ),
					'label' => GFCommon::get_label( $field ) . ' ' . __( '(Count)', 'gp-auto-list-field' ),
				);
			}
		}

		return $merge_tags;
	}

	/**
	 * Outputs field setting markup for List field ALF settings for the Form Editor. These settings will be shown/hidden
	 * using JavaScript.
	 *
	 * @return void
	 */
	public function field_settings_ui() {
		?>

		<li class="gpalf-field-setting field_setting">

			<input type="checkbox" id="gpalf-enable" value="1" onclick="GPALFAdmin.toggleSettings( this.checked );" />
			<label class="inline" for="gpalf-enable">
				<?php _e( 'Enable Auto List Field', 'gp-auto-list-field' ); ?> <?php gform_tooltip( 'gpalf_enable' ); ?>
			</label>

			<div id="gpalf-child-settings" class="gp-child-settings">

				<label class="section_label"><?php esc_html_e( 'Trigger Field', 'gp-auto-list-field' ); ?> <?php gform_tooltip( 'gpalf_source_field' ); ?></label>
				<select id="gpalf-source-field" onchange="SetFieldProperty( 'gpalfSourceField', this.value );">
					<option value=""><?php esc_html_e( 'Select a Field', 'gp-auto-list-field' ); ?></option>
				</select>

			</div>

		</li>
		<?php
	}

	## HELPERS

	/**
	 * @param array $form The current form.
	 *
	 * @return bool Whether the current form has a List field automated by Auto List Fields.
	 */
	public function is_form_with_auto_list_field( $form ) {
		foreach ( rgar( $form, 'fields', array() ) as $field ) {
			if ( $this->is_auto_list_field( $field ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @param GF_Field_List $field
	 *
	 * @return bool Whether the field is a List field and is automated by Auto List Fields.
	 */
	public function is_auto_list_field( $field ) {
		if ( $field->type !== 'list' ) {
			return false;
		}

		$is_enabled = $field->gpalfEnable && $field->gpalfSourceField;

		if ( ! $is_enabled ) {
			return false;
		}

		// Verify that the source field exists.
		return ! ! GFFormsModel::get_field( $field->formId, $field->gpalfSourceField );
	}

	/**
	 * @param array $form The current form.
	 *
	 * @return bool Whether the current form contains a formula referencing the count of a List field.
	 */
	public function is_form_with_list_count_merge_tag( $form ) {
		if ( ! $form ) {
			return false;
		}

		$list_field_ids = wp_list_pluck( $this->get_form_list_fields( $form ), 'id' );

		if ( empty( $list_field_ids ) ) {
			return false;
		}

		foreach ( rgar( $form, 'fields', array() ) as $field ) {
			$formula = rgar( $field, 'calculationFormula' );

			if ( ! $formula ) {
				continue;
			}

			if ( preg_match( '/{(.*?):(' . join( '|', $list_field_ids ) . '):count}/', $formula ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @param array $form Form to search for List fields in.
	 *
	 * @return GF_Field_List[] List fields present in the form.
	 */
	public function get_form_list_fields( $form ) {
		/* Use cache as this method will could be called frequently via gform_modify_calculation. */
		if ( isset( $this->form_list_fields_cache[ $form['id'] ] ) ) {
			return $this->form_list_fields_cache[ $form['id'] ];
		}

		$list_fields = array();

		foreach ( rgar( $form, 'fields', array() ) as $field ) {
			if ( $field->type === 'list' ) {
				$list_fields[] = $field;
			}
		}

		$this->form_list_fields_cache[ $form['id'] ] = $list_fields;

		return $list_fields;
	}

	/**
	 * Parses any :count merge tags for List fields in the formula on the backend.
	 *
	 * @param string $formula The field's calculation formula
	 * @param GF_Field $field The current formula field.
	 * @param array $form The current form.
	 * @param array $entry The current entry.
	 *
	 * @return string
	 */
	public function modify_calculation_formula( $formula, $field, $form, $entry ) {
		preg_match_all( '/{[^{]*?:(\d+(\.\d+)?)(:(.*?))?}/mi', $formula, $matches, PREG_SET_ORDER );

		$list_field_ids = wp_list_pluck( $this->get_form_list_fields( $form ), 'id' );

		foreach ( $matches as $match ) {

			list( $full_match, $input_id, , , $modifier ) = array_pad( $match, 5, false );

			$field_id = intval( $input_id );

			if ( $modifier !== 'count' ) {
				continue;
			}

			if ( ! in_array( $field_id, $list_field_ids, true ) ) {
				continue;
			}

			$list_field_row_count = 0;
			$list_field_value     = rgar( $entry, $field_id );

			if ( ! empty( $list_field_value ) ) {
				if ( is_serialized( $list_field_value ) ) {
					$list_field_value = unserialize( $list_field_value );
				}

				if ( self::is_json( $list_field_value ) ) {
					$list_field_value = json_decode( $list_field_value );
				}

				if ( is_array( $list_field_value ) ) {
					$list_field_row_count = count( $list_field_value );
				}
			}

			$value = $list_field_row_count;

			$formula = str_replace( $full_match, $value, $formula );

		}

		return $formula;
	}

	/**
	 * Truncate the List field rows that are saved if a List field is linked to a trigger field using Auto List Field and the number of rows exceeds
	 * the trigger field value.
	 *
	 * @param string|array $value The fields input value.
	 * @param array $entry The current entry object.
	 * @param GF_Field $field The current field object.
	 * @param array $form The current form object.
	 * @param string $input_id The ID of the input being saved or the field ID for single input field types.
	 *
	 * @return mixed The field value.
	 */
	public function maybe_truncate_list_field_rows( $value, $entry, $field, $form, $input_id ) {
		if ( rgar( $field, 'type' ) !== 'list' ) {
			return $value;
		}

		$source_field  = GFAPI::get_field( $form, $field->gpalfSourceField );
		if ( ! $source_field ) {
			return $value;
		}

		$original_value     = $value;
		$value              = maybe_unserialize( $value );
		$source_input_value = rgar( $entry, $this->get_source_input_id( $source_field ) );

		if ( ! $source_field || $source_input_value === null ) {
			return $original_value;
		}

		$value = array_slice( $value, 0, (int) $source_input_value );

		return serialize( $value );
	}

	/**
	 * @return string[] Field types that can be used for the List field source field.
	 */
	public function get_supported_source_field_types() {
		/**
		 * Filter the field types that can trigger auto-list-field rows.
		 *
		 * @since 1.0.1
		 *
		 * @param array $field_types An array of supported field types.
		 */
		return apply_filters( 'gpalf_supported_field_types', array(
			'text',
			'select',
			'number',
			'hidden',
			'singleproduct',
			'quantity',
		) );
	}

}

function gp_auto_list_field() {
	return GP_Auto_List_Field::get_instance();
}

GFAddOn::register( 'GP_Auto_List_Field' );

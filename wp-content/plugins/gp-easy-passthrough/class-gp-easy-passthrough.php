<?php

if ( ! class_exists( 'GP_Feed_Plugin' ) ) {
	return;
}

class GP_Easy_Passthrough extends GP_Feed_Plugin {

	/**
	* Contains an instance of this class, if available.
	*
	* @since  1.0
	* @access private
	* @var    GP_Easy_Passthrough $_instance If available, contains an instance of this class.
	*/
	private static $_instance = null;

	/**
	* Defines the version of Easy Passthrough for Gravity Forms.
	*
	* @since  1.0
	* @access protected
	* @var    string $_version Contains the version, defined from easypassthrough.php
	*/
	protected $_version = GPEP_VERSION;

	/**
	* Defines the minimum Gravity Forms version required.
	*
	* @since  1.0
	* @access protected
	* @var    string $_min_gravityforms_version The minimum version required.
	*/
	protected $_min_gravityforms_version = '2.1';

	/**
	* Defines the plugin slug.
	*
	* @since  1.0
	* @access protected
	* @var    string $_slug The slug used for this plugin.
	*/
	protected $_slug = 'gp-easy-passthrough';

	/**
	* Defines the main plugin file.
	*
	* @since  1.0
	* @access protected
	* @var    string $_path The path to the main plugin file, relative to the plugins folder.
	*/
	protected $_path = 'gp-easy-passthrough/gp-easy-passthrough.php';

	/**
	* Defines the full path to this class file.
	*
	* @since  1.0
	* @access protected
	* @var    string $_full_path The full path.
	*/
	protected $_full_path = __FILE__;

	/**
	* Defines the URL where this Add-On can be found.
	*
	* @since  1.0
	* @access protected
	* @var    string The URL of the Add-On.
	*/
	protected $_url = 'https://gravitywiz.com/documentation/gravity-forms-easy-passthrough/';

	/**
	* Defines the title of this Add-On.
	*
	* @since  1.0
	* @access protected
	* @var    string $_title The title of the Add-On.
	*/
	protected $_title = 'Easy Passthrough';

	/**
	* Defines the short title of the Add-On.
	*
	* @since  1.0
	* @access protected
	* @var    string $_short_title The short title.
	*/
	protected $_short_title = 'Easy Passthrough';

	/**
	* Defines if feed ordering is supported.
	*
	* @since  1.0
	* @access protected
	* @var    bool $_supports_feed_ordering Is feed ordering supported?
	*/
	protected $_supports_feed_ordering = true;

	/**
	* Defines the capability needed to access the Add-On settings page.
	*
	* @since  1.0
	* @access protected
	* @var    string $_capabilities_settings_page The capability needed to access the Add-On settings page.
	*/
	protected $_capabilities_settings_page = 'gp_easy_passthrough';

	/**
	* Defines the capability needed to access the Add-On form settings page.
	*
	* @since  1.0
	* @access protected
	* @var    string $_capabilities_form_settings The capability needed to access the Add-On form settings page.
	*/
	protected $_capabilities_form_settings = 'gp_easy_passthrough';

	/**
	* Defines the capability needed to uninstall the Add-On.
	*
	* @since  1.0
	* @access protected
	* @var    string $_capabilities_uninstall The capability needed to uninstall the Add-On.
	*/
	protected $_capabilities_uninstall = 'gp_easy_passthrough_uninstall';

	/**
	* Defines the capabilities needed for Easy Passthrough.
	*
	* @since  1.0
	* @access protected
	* @var    array $_capabilities The capabilities needed for the Add-On.
	*/
	protected $_capabilities = array( 'gp_easy_passthrough', 'gp_easy_passthrough_uninstall' );

	/**
	* Stores the field values used during passthrough.
	*
	* @since  1.1.5
	* @access protected
	* @var    array $field_values Field values used during passthrough.
	*/
	protected $field_values = array();

	/**
	* Stores the entry IDs (and their form IDs) used during passthrough.
	*
	* @since  1.0.5
	* @access protected
	* @var    array $passed_through_entries Entry IDs (and their form IDs) used during passthrough.
	*/
	protected $passed_through_entries = array();

	/**
	* Get instance of this class.
	*
	* @since  1.0
	* @access public
	* @static
	*
	* @return GP_Easy_Passthrough $_instance
	*/
	public static function get_instance() {

		if ( null === self::$_instance ) {
			self::$_instance = new self;
		}

		return self::$_instance;

	}

	/**
	* Register needed hooks.
	*
	* @since  1.1
	* @access public
	*/
	public function pre_init() {

		parent::pre_init();

	}

	/**
	* Register needed hooks.
	*
	* @since  1.0
	* @access public
	*/
	public function init() {

		$this->load_entry_token();

		parent::init();

		remove_filter( 'gform_entry_post_save', array( $this, 'maybe_process_feed' ) );
		add_filter( 'gform_entry_post_save', array( $this, 'filter_gform_entry_post_save' ), 9, 1 );

		add_action( 'gform_after_submission', array( $this, 'store_entry_id' ), 10, 2 );

		add_filter( 'gform_pre_render', array( $this, 'populate_fields' ), 5 );

		add_filter( 'gform_admin_pre_render', array( $this, 'add_merge_tags' ) );
		add_action( 'gform_pre_replace_merge_tags', array( $this, 'replace_merge_tags' ), 6, 3 );

		add_filter( 'gform_' . $this->_slug . '_field_value', array( $this, 'override_field_value' ), 10, 4 );

		// Add support for populating child entries when EP token is provided.
		add_filter( 'gpnf_can_user_edit_entry', array( $this, 'can_user_edit_gpnf_entries' ), 10, 2 );

		add_filter( 'gform_field_map_choices', array( $this, 'append_checkbox_name' ), 10, 4 );

		/**
		 * Delete GPEP cookie when users log out.
		 *
		 * @since 1.9.4
		 *
		 * @param bool $delete_gpep_cookie  Delete GPEP cookie once users logout (default: false)
		 */
		if ( apply_filters( 'gpep_delete_cookie_on_logout', false ) ) {
			// Handle logging out and clearing session cookie if specified
			add_action( 'wp_logout', array( $this, 'clear_session' ) );
		}

	}

	/**
	* Register needed hooks.
	*
	* @since  1.2.1
	* @access public
	*/
	public function init_admin() {

		parent::init_admin();

		// Members 2.0+ integration.
		if ( function_exists( 'members_register_cap_group' ) ) {
			remove_filter( 'members_get_capabilities', array( $this, 'members_get_capabilities' ) );
			add_action( 'members_register_caps', array( $this, 'members_register_caps' ) );
		}

		// ForGravity Easy Passthrough Upgrade Handling
		if ( class_exists( 'EasyPassthrough_Bootstrap' ) ) {
			add_action( 'admin_notices', array( $this, 'maybe_upgrade_fg_easy_passthrough' ), 0 );
			add_action( 'admin_notices', array( $this, 'maybe_show_fg_easy_passthrough_upgrade_notice' ) );
		}

	}

	/**
	 * Add a menu icon to override the default cog/gear.
	 */
	public function get_menu_icon() {
		return '<svg version="1.1" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
 <path d="m70.844 16.73c-1.6953 0.003906-3.2188 1.0312-3.8516 2.6016-0.63672 1.5703-0.26172 3.3711 0.95312 4.5508l9.4336 9.4219h-31.535v0.003906c-1.1172-0.015625-2.1914 0.41797-2.9844 1.2031-0.79297 0.78125-1.2383 1.8516-1.2383 2.9688 0 1.1133 0.44922 2.1836 1.2461 2.9648 0.79297 0.78125 1.8711 1.2109 2.9844 1.1953h31.617l-9.5234 9.5117v0.003906c-0.80859 0.77344-1.2695 1.8438-1.2812 2.9609-0.011718 1.1211 0.42969 2.1992 1.2227 2.9883 0.78906 0.79297 1.8672 1.2344 2.9883 1.2227s2.1875-0.47266 2.9648-1.2812l16.594-16.578c0.78125-0.78125 1.2227-1.8438 1.2227-2.9492s-0.44141-2.168-1.2227-2.9492l-16.594-16.578c-0.78906-0.80859-1.8672-1.2656-2.9961-1.2617zm-41.789 24.871c-1.082 0.03125-2.1094 0.48828-2.8633 1.2695l-16.625 16.641h-0.003906c-1.625 1.6289-1.625 4.2656 0 5.8906l16.625 16.641 0.003906 0.003906c1.625 1.6289 4.2656 1.6289 5.8945 0.003906 1.6289-1.6289 1.6328-4.2656 0.003906-5.8945l-9.5039-9.5117h31.602l-0.003906-0.003906c1.1172 0.015625 2.1914-0.41406 2.9844-1.1992 0.79688-0.78125 1.2422-1.8516 1.2422-2.9688 0-1.1133-0.44531-2.1836-1.2422-2.9648-0.79297-0.78516-1.8672-1.2148-2.9844-1.1992h-31.641l9.5469-9.5547c1.2344-1.1992 1.6055-3.0312 0.93359-4.6172-0.67188-1.582-2.25-2.5898-3.9688-2.5352z" fill-rule="evenodd"/>
</svg>';
	}

	/**
	* Register needed frontend hooks.
	*
	* @since  1.0
	* @access public
	*/
	public function init_frontend() {

		parent::init_frontend();

		/**
		* Prevent session manager from initializing on page load.
		*
		* @since 1.1.8
		*
		* @param bool $bypass_session_init Bypass initializing session manager.
		*/
		$bypass_session_init = apply_filters( 'gpep_bypass_session_init', false );

		if ( ! $bypass_session_init ) {
			$this->session_manager();
		}

	}

	/**
	* Enqueue needed scripts.
	*
	* @since  1.0
	* @access public
	*
	* @return array
	*/
	public function scripts() {

		$scripts = array(
			array(
				'handle'    => 'gpep_vendor_cookie',
				'src'       => $this->get_base_url() . '/js/vendor/js.cookie.js',
				'version'   => $this->_version,
				'deps'      => array( 'jquery' ),
				'in_footer' => false,
				'enqueue'   => array( '__return_true' ),
			),
		);

		return array_merge( parent::scripts(), $scripts );

	}

	/**
	* Enqueue needed stylesheets.
	*
	* @since  1.0
	* @access public
	*
	* @uses   GFAddOn::get_base_url()
	* @uses   GFAddOn::get_slug()
	* @uses   GFAddOn::get_version()
	*
	* @return array
	*/
	public function styles() {

		$styles = array(
			array(
				'handle'  => $this->get_slug() . '_feed_settings',
				'src'     => $this->get_base_url() . '/css/feed_settings.css',
				'version' => $this->get_version(),
				'enqueue' => array(
					array(
						'admin_page' => array( 'form_settings' ),
						'tab'        => $this->_slug,
					),
				),
			),
		);

		return array_merge( parent::styles(), $styles );

	}





	// # FEED SETTINGS -------------------------------------------------------------------------------------------------

	public function can_duplicate_feed( $feed_id ) {
		return true;
	}

	/**
	* Setup fields for feed settings.
	*
	* @since  1.0
	* @access public
	*
	* @uses   GP_Easy_Passthrough::get_field_map()
	* @uses   GP_Easy_Passthrough::get_forms_as_choices()
	* @uses   GP_Easy_Passthrough::get_meta_map()
	*
	* @return array
	*/
	public function feed_settings_fields() {

		return array(
			array(
				'title'       => esc_html__( 'Select Source Form', 'gp-easy-passthrough' ),
				'description' => $this->is_gf_version_gte( '2.5-beta-1' ) ? esc_html__( 'Select which form you want to populate this form from.', 'gp-easy-passthrough' ) : '',
				'fields'      => array(
					array(
						'name'     => 'sourceForm',
						'label'    => esc_html__( 'Source Form', 'gp-easy-passthrough' ),
						'type'     => 'select',
						'required' => true,
						'onchange' => "jQuery( this ).parents( 'form' ).submit()",
						'choices'  => $this->get_forms_as_choices(),
						'tooltip'  => $this->is_gf_version_gte( '2.5-beta-1' ) ? '' : sprintf(
							'<h6>%s</h6>%s',
							esc_html__( 'Source Form', 'gp-easy-passthrough' ),
							esc_html__( 'Select which form you want to populate this form from.', 'gp-easy-passthrough' )
						),

					),
				),
			),
			array(
				'title'       => esc_html__( 'Map Entry Fields', 'gp-easy-passthrough' ),
				'description' => $this->is_gf_version_gte( '2.5-beta-1' ) ? esc_html__( 'Select which fields on this form should be populated from fields on the source form.', 'gp-easy-passthrough' ) : '',
				'fields'      => array(
					array(
						'name'        => 'fieldMap',
						'label'       => esc_html__( 'Map Fields', 'gp-easy-passthrough' ),
						'type'        => 'field_map',
						'key_field'   => array(
							'title' => 'Source Form Field',
						),
						'value_field' => array(
							'title' => 'Target Form Field',
						),
						'field_map'   => $this->get_field_map(),
						'dependency'  => 'sourceForm',
						'tooltip'     => $this->is_gf_version_gte( '2.5-beta-1' ) ? '' : sprintf(
							'<h6>%s</h6>%s',
							esc_html__( 'Map Fields', 'gp-easy-passthrough' ),
							esc_html__( 'Select which fields on this form should be populated from fields on the source form.', 'gp-easy-passthrough' )
						),

					),
				),
			),
			array(
				'title'       => esc_html__( 'Map Entry Meta', 'gp-easy-passthrough' ),
				'description' => $this->is_gf_version_gte( '2.5-beta-1' ) ? esc_html__( 'Select which fields on this form should be populated from entry meta data on the source form.', 'gp-easy-passthrough' ) : '',
				'fields'      => array(
					array(
						'name'           => 'metaMap',
						'label'          => esc_html__( 'Map Meta', 'gp-easy-passthrough' ),
						'type'           => 'dynamic_field_map',
						'field_map'      => $this->get_meta_map(),
						'disable_custom' => true,
						'dependency'     => 'sourceForm',
						'tooltip'        => $this->is_gf_version_gte( '2.5-beta-1' ) ? '' : sprintf(
							'<h6>%s</h6>%s',
							esc_html__( 'Map Fields', 'gp-easy-passthrough' ),
							esc_html__( 'Select which fields on this form should be populated from entry meta data on the source form.', 'gp-easy-passthrough' )
						),

					),
				),
			),
			array(
				'title'  => esc_html__( 'Additional Options', 'gp-easy-passthrough' ),
				'fields' => array(
					array(
						'name'       => 'options',
						'label'      => esc_html__( 'Options', 'gp-easy-passthrough' ),
						'type'       => 'checkbox',
						'dependency' => 'sourceForm',
						'choices'    => array(
							array(
								'name'    => 'userPassthrough',
								'label'   => esc_html__( "Use logged in user's last submitted entry", 'gp-easy-passthrough' ),
								'tooltip' => esc_html__( 'If user is logged in and they have submitted an entry to the source form, submitted entry will be used instead of entry in session.', 'gp-easy-passthrough' ),
							),
						),
					),
					array(
						'name'           => 'passthroughCondition',
						'type'           => 'feed_condition',
						'label'          => esc_html__( 'Conditional Logic', 'gp-easy-passthrough' ),
						'dependency'     => 'sourceForm',
						'checkbox_label' => esc_html__( 'Enable', 'gp-easy-passthrough' ),
						'instructions'   => esc_html__( 'Passthrough form entry if', 'gp-easy-passthrough' ),
					),
					array(
						'type'     => 'save',
						'messages' => array(
							'error'   => esc_html__( 'There was an error while saving the Easy Passthrough settings. Please review the errors below and try again.', 'gp-easy-passthrough' ),
							'success' => esc_html__( 'Entry Passthrough settings updated.', 'gp-easy-passthrough' ),
						),
					),
				),
			),
		);

	}

	/**
	* Define the title for the feed settings page.
	*
	* @since  1.0
	* @access public
	*
	* @return string
	*/
	public function feed_settings_title() {

		return esc_html__( 'Easy Passthrough Settings', 'gp-easy-passthrough' );

	}

	/**
	* Prepare forms for settings field.
	*
	* @since  1.0
	* @access public
	*
	* @uses   GFAddOn::get_current_form()
	* @uses   GFAPI::get_forms()
	*
	* @return array
	*/
	public function get_forms_as_choices() {

		// Initialize choices array.
		$choices = array(
			array(
				'label' => esc_html__( 'Select a Form', 'gp-easy-passthrough' ),
				'value' => '',
			),
		);

		// Get current form.
		$current_form = $this->get_current_form();

		// Get all forms.
		$forms = GFAPI::get_forms( true, false, 'title' );

		// Loop through forms.
		foreach ( $forms as $form ) {

			/**
			* Allow form to be populated from itself.
			*
			* @since 1.0.2
			*
			* @param bool $allow_same_form Allow form to be populated from itself.
			*/
			$allow_same_form = apply_filters( 'gpep_populate_same_form', true );

			// If form is the current form, skip it.
			if ( $form['id'] == $current_form['id'] && ! $allow_same_form ) {
				continue;
			}

			// Add form as choice.
			$choices[] = array(
				'label' => esc_html( $form['title'] ),
				'value' => esc_attr( $form['id'] ),
			);

		}

		return $choices;

	}

	/**
	* Prepare field map for settings field.
	*
	* @since  1.0
	* @access public
	*
	* @uses   GFAddOn::get_setting()
	* @uses   GFAPI::get_form()
	* @uses   GFCommon::get_label()
	* @uses   GF_Field::get_entry_inputs()
	*
	* @return array
	*/
	public function get_field_map() {

		// Initialize field map.
		$field_map = array();

		// Get source form ID.
		$source_form = $this->get_setting( 'sourceForm' );

		// Get source form.
		$source_form = GFAPI::get_form( $source_form );

		// If source form is not set, return field map.
		if ( ! $source_form ) {
			return $field_map;
		}

		/**
		* Loop through source form fields.
		*
		* @var GF_Field $field
		*/
		foreach ( $source_form['fields'] as $field ) {

			if ( $field->displayOnly ) {
				continue;
			}

			// Set admin label property.
			$field->set_context_property( 'use_admin_label', true );

			// Get input type.
			$input_type = $field->get_input_type();

			// Get field inputs.
			$inputs = $field->get_entry_inputs();

			// If field has inputs, add each input to field map.
			if ( $inputs ) {

				// Add checkboxes to the field map.
				if ( ! empty( $field->choices ) && $field->get_input_type() === 'checkbox' ) {
					$field_map[] = array(
						'name'          => esc_attr( $field['id'] ),
						'label'         => esc_html( $field['label'] ),
						'default_value' => array(
							'aliases' => array( esc_html( $field->adminLabel ) ),
						),
					);
				}

				$input_suffix = '';

				if ( $field->get_input_type() === 'checkbox' ) {
					$input_suffix = ' (' . esc_html( $field['label'] ) . ')';
				}

				// Loop through inputs.
				foreach ( $inputs as $input ) {

					$gppa_missing_filter_text = apply_filters( 'gppa_missing_filter_text', '&ndash; ' . esc_html__( 'Fill Out Other Fields', 'gp-populate-anything' ) . ' &ndash;', $field['id'] );
					$input_label              = GFCommon::get_label( $field, $input['id'] );

					if ( $gppa_missing_filter_text === $input_label ) {
						continue;
					}

					// Replace period in input ID.
					$name = str_replace( '.', '_', $input['id'] );

					// Add input to field map.
					$field_map[] = array(
						'name'  => esc_attr( $name ),
						'label' => $input_label . $input_suffix,
					);

				}
			} elseif ( 'list' === $input_type && $field->enableColumns ) {

				// Define initial column index.
				$column_index = 0;

				// Loop through columns.
				foreach ( $field->choices as $column ) {

					// Add column to field map.
					$field_map[] = array(
						'name'  => esc_attr( $field->id . '_' . $column_index ),
						'label' => GFCommon::get_label( $field ) . ' (' . esc_html( rgar( $column, 'text' ) ) . ')',
					);

					// Increase column index.
					$column_index++;

				}
			} elseif ( ! in_array( $input_type, array( 'fileupload' ) ) ) {

				// Add field to field map.
				$field_map[] = array(
					'name'          => esc_attr( $field['id'] ),
					'label'         => esc_html( $field['label'] ),
					'default_value' => array(
						'aliases' => array( esc_html( $field->adminLabel ) ),
					),
				);

			}
		}

		return $field_map;

	}

	/**
	* Prepare meta map for settings field.
	*
	* @since  1.0
	* @access public
	*
	* @uses   GFAddOn::get_setting()
	* @uses   GFFormsModel::get_entry_meta()
	*
	* @return array
	*/
	public function get_meta_map() {

		// Get source form ID.
		$source_form = $this->get_setting( 'sourceForm' );

		// If source form is not set, return meta map.
		if ( ! $source_form ) {
			return array();
		}

		// Initialize meta map.
		$meta_map = array(
			array(
				'value' => '',
				'label' => esc_html__( 'Select a Meta Key', 'gp-easy-passthrough' ),
			),
			array(
				'value' => 'id',
				'label' => esc_html__( 'Entry ID', 'gp-easy-passthrough' ),
			),
			array(
				'value' => 'date_created',
				'label' => esc_html__( 'Entry Date', 'gp-easy-passthrough' ),
			),
			array(
				'value' => 'ip',
				'label' => esc_html__( 'User IP', 'gp-easy-passthrough' ),
			),
			array(
				'value' => 'source_url',
				'label' => esc_html__( 'Source Url', 'gp-easy-passthrough' ),
			),
			array(
				'value' => 'form_title',
				'label' => esc_html__( 'Form Title', 'gp-easy-passthrough' ),
			),
			array(
				'label'   => esc_html__( 'Payment Meta', 'gp-easy-passthrough' ),
				'choices' => array(
					array(
						'value' => 'payment_status',
						'label' => esc_html__( 'Payment Status', 'gp-easy-passthrough' ),
					),
					array(
						'value' => 'transaction_id',
						'label' => esc_html__( 'Transaction Id', 'gp-easy-passthrough' ),
					),
					array(
						'value' => 'payment_date',
						'label' => esc_html__( 'Payment Date', 'gp-easy-passthrough' ),
					),
					array(
						'value' => 'payment_amount',
						'label' => esc_html__( 'Payment Amount', 'gp-easy-passthrough' ),
					),
					array(
						'value' => 'payment_gateway',
						'label' => esc_html__( 'Payment Amount', 'gp-easy-passthrough' ),
					),
				),
			),
		);

		// Get entry meta fields for form.
		$form_meta = GFFormsModel::get_entry_meta( $source_form );

		// Add entry meta to meta map.
		foreach ( $form_meta as $meta_key => $meta ) {
			$meta_map[] = array(
				'value' => $meta_key,
				'label' => rgars( $form_meta, "{$meta_key}/label" ),
			);
		}

		return $meta_map;

	}

	/**
	* Heading row for field map table.
	*
	* @since  2.2
	* @access public
	*
	* @uses   GFAddOn::field_map_title()
	*
	* @return string
	*/
	public function field_map_table_header() {

		return '<thead>
					<tr>
						<th>' . esc_html__( 'Source Form Field', 'gp-easy-passthrough' ) . '</th>
						<th>' . esc_html__( 'Target Form Field', 'gp-easy-passthrough' ) . '</th>
					</tr>
				</thead>';

	}

	/**
	* Renders the form settings page.
	* Forked to set Javascript form variable to source form.
	*
	* @since  1.0
	* @access public
	*/
	public function form_settings_page() {

		GFFormSettings::page_header( $this->_title );
		?>
		<div class="gform_panel gform_panel_form_settings" id="form_settings">

			<?php
			$form = $this->get_current_form();

			$form_id = $form['id'];
			$form    = gf_apply_filters( array( 'gform_admin_pre_render', $form_id ), $form );

			if ( $this->method_is_overridden( 'form_settings' ) ) {

				//enables plugins to override settings page by implementing a form_settings() function
				$this->form_settings( $form );
			} else {

				//saves form settings if save button was pressed
				$this->maybe_save_form_settings( $form );

				//reads current form settings
				$settings = $this->get_form_settings( $form );
				$this->set_settings( $settings );

				//reading addon fields
				$sections = $this->form_settings_fields( $form );

				GFCommon::display_admin_message();

				$page_title = $this->form_settings_page_title();
				if ( empty( $page_title ) ) {
					$page_title = rgar( $sections[0], 'title' );

					//using first section title as page title, so disable section title
					$sections[0]['title'] = false;
				}
				$icon = $this->form_settings_icon();
				if ( empty( $icon ) ) {
					$icon = '<i class="fa fa-cogs"></i>';
				}

				?>
				<h3><span><?php echo $icon; ?><?php echo $page_title; ?></span></h3>
				<?php

				//rendering settings based on fields and current settings
				$this->render_settings( $sections );
			}
			?>

			<script type="text/javascript">
				var form = <?php echo json_encode( GFAPI::get_form( $this->get_setting( 'sourceForm' ) ) ); ?>;
			</script>
		</div>
		<?php
		GFFormSettings::page_footer();
	}





	// # FEED LIST -----------------------------------------------------------------------------------------------------

	/**
	* Define the title for the feed list page.
	*
	* @since  1.0
	* @access public
	*
	* @uses   GFAddOn::get_short_title()
	* @uses   GFFeedAddOn::can_create_feed()
	*
	* @return string
	*/
	public function feed_list_title() {

		// If feed creation is disabled, display title without Add New button.
		if ( ! $this->can_create_feed() ) {
			return sprintf(
				esc_html__( '%s Configurations', 'gp-easy-passthrough' ),
				$this->get_short_title()
			);
		}

		$header = sprintf(
			esc_html__( '%s Configurations', 'gp-easy-passthrough' ),
			$this->get_short_title()
		);

		if ( ! $this->is_gf_version_gte( '2.5-beta-1' ) ) {

			// Prepare add new feed URL.
			$url = add_query_arg( array( 'fid' => '0' ) );
			$url = esc_url( $url );

			// Display feed list title with Add New button.
			$return = sprintf(
				'%s <a class="add-new-h2" href="%s">%s</a>',
				$header,
				$url,
				esc_html__( 'Add New', 'gravityforms' )
			);

		} else {
			$return = $header;
		}

		return $return;
	}

	/**
	* Setup columns for feed list table.
	*
	* @since  1.0
	* @access public
	*
	* @return array
	*/
	public function feed_list_columns() {

		return array(
			'sourceForm' => esc_html__( 'Source Form', 'gp-easy-passthrough' ),
		);

	}

	/**
	* Prepare Source Form column value for feed list table.
	*
	* @since  1.0
	* @access public
	*
	* @param array $feed Current feed.
	*
	* @uses   GFAPI::get_form()
	*
	* @return string
	*/
	public function get_column_value_sourceForm( $feed ) {

		// Get form.
		$form = GFAPI::get_form( $feed['meta']['sourceForm'] );

		// If form was not found, return.
		if ( ! $form ) {
			return $feed['meta']['sourceForm'];
		}

		return esc_html( $form['title'] );

	}





	// # PAGE LOAD -----------------------------------------------------------------------------------------------------

	/**
	* Load entry ID into session if token is set.
	*
	* @since  1.1
	* @access public
	*
	* @uses   GP_Easy_Passthrough::get_entry_for_token()
	* @uses   GP_Easy_Passthrough::store_entry_id()
	* @uses   GFAPI::get_form()
	*/
	public function load_entry_token() {

		// If token is not set, return.
		if ( ! rgget( 'ep_token' ) ) {
			return;
		}

		// Get token.
		$token = sanitize_text_field( rgget( 'ep_token' ) );

		// Get entry for token.
		$entry = $this->get_entry_for_token( $token );

		// If no entry was found, return.
		if ( ! $entry ) {
			return;
		}

		// Get form.
		$form = GFAPI::get_form( $entry['form_id'] );

		// Store entry ID.
		$this->store_entry_id( $entry, $form );

	}

	public function can_user_edit_gpnf_entries( $can_edit, $entry ) {

		if ( $can_edit ) {
			return $can_edit;
		}

		// Get entry for token.
		$parent_entry = $this->get_entry_for_token( sanitize_text_field( rgget( 'ep_token' ) ) );
		if ( ! $parent_entry ) {
			return $can_edit;
		}

		// Check if child entry's parent is the entry passed by the token.
		return gform_get_meta( $entry['id'], GPNF_Entry::ENTRY_PARENT_KEY ) == $parent_entry['id'];
	}





	// # FORM SUBMISSION -----------------------------------------------------------------------------------------------

	/**
	* Store entry ID to session.
	*
	* @since  1.0
	* @access public
	*
	* @param array $entry The entry that was just created.
	* @param array $form  The current form.
	*/
	public function store_entry_id( $entry, $form ) {

		// Get session manager.
		$session = $this->session_manager();

		// Store entry ID to session.
		$session[ $this->_slug . '_' . $form['id'] ] = $entry['id'];

	}





	// # FORM RENDER ---------------------------------------------------------------------------------------------------

	/**
	* Populate fields via Easy Passthrough.
	*
	* @since  1.0
	* @access public
	*
	* @param array $form The current Form object.
	*
	* @uses   GP_Easy_Passthrough::get_field_values()
	* @uses   GFAPI::get_form()
	* @uses   GFCommon::date_display()
	* @uses   GFCommon::implode_non_blank()
	* @uses   GF_Field::get_entry_inputs()
	* @uses   GF_Field::get_input_type()
	*
	* @return array
	*/
	public function populate_fields( $form ) {

		// If no form ID is set, return.
		if ( ! rgar( $form, 'id' ) ) {
			return $form;
		}

		// If GravityView is in edit context, return.
		if ( function_exists( 'gravityview_get_context' ) && 'edit' === gravityview_get_context() ) {
			return $form;
		}

		// Get field values.
		$field_values = $this->get_field_values( $form['id'] );

		// If no field values were found, return.
		if ( ! $field_values ) {

			/**
			* Modify form object after Easy Passthrough has been applied.
			*
			* @since 1.0.5
			*
			* @param array $form                   The current form object.
			* @param array $field_values           The prepared field values.
			* @param array $passed_through_entries The entry IDs used for passthrough and their form IDs.
			*/
			$form = gf_apply_filters( array(
				'gpep_form',
				$form['id'],
			), $form, $this->field_values, $this->passed_through_entries );

			return $form;

		}

		/**
		* Loop through form fields, prepare value for passthrough.
		*
		* @var GF_Field $field
		*/
		foreach ( $form['fields'] as &$field ) {

			// Skip administrative fields unless they're configured to support dynamic population.
			if ( $field->visibility == 'administrative' && ! $field->allowsPrepopulate ) {
				continue;
			}

			// Set passthrough value.
			$value = false;

			/**
			 * Modify whether to prefer existing dynamic population over values that are populated using
			 * Easy Passthrough.
			 *
			 * @since 1.9.6
			 *
			 * @param boolean   $prefer_dynamic_population  Whether to prefer existing dynamic population if value is present.
			 * @param GF_Field  $field                      The current field.
			 * @param array     $form                       The current form.
			 */
			$prefer_dynamic_population = gf_apply_filters( array(
				'gpep_prefer_dynamic_population',
				$form['id'],
				$field->id,
			), true, $field, $form );

			// Prepare passthrough value based on input type.
			switch ( $field->get_input_type() ) {

				case 'checkbox':
					// Get passed-through value.
					$value = rgar( $field_values, $field->id );

					// If the value is empty, search the field values array for individual checkboxes.
					if ( empty( $value ) ) {

						// Reset passthrough value as an empty array.
						$value = array();

						foreach ( $field_values as $input_id => $input_value ) {
							if ( absint( $input_id ) != $field->id ) {
								continue;
							}

							if ( is_array( $input_value ) ) {
								$value[] = GFCommon::implode_non_blank( ',', $input_value );
								continue;
							}

							$value[] = $input_value;
						}
					}

					break;

				case 'list':
					// Get field value.
					$value = rgar( $field_values, $field->id );

					// Add field value based on column state.
					if ( $field->enableColumns ) {

						// Initialize field value array.
						$value = array();

						// Loop through field columns.
						foreach ( $field->choices as $index => $column ) {

							// Get column value.
							$column_value = rgar( $field_values, $field->id . '.' . $index );

							// If column value was not found, set to array.
							if ( empty( $column_value ) ) {
								$column_value = array( array( 'text' => '' ) );
							}

							// Loop through column value.
							foreach ( $column_value as $row_index => $row ) {

								// Get row value.
								$row_value = end( $row );

								// If row was not found, set to array.
								if ( ! isset( $value[ $row_index ] ) ) {
									$value[ $row_index ] = array();
								}

								$value[ $row_index ] = array_merge( $value[ $row_index ], array( $column['text'] => $row_value ) );

							}
						}
					} else {

						// If field value is not an array, convert to array.
						if ( ! is_array( $value ) ) {
							$value = array( $value );
						}

						// Get array values.
						$value = array_values( $value );

					}

					break;

				case 'date':
					$date_array = rgar( $field_values, $field->id );

					$day   = rgar( $date_array, 'day' );
					$month = rgar( $date_array, 'month' );
					$year  = rgar( $date_array, 'year' );

					if ( $day && $month && $year ) {
						switch ( $field->dateFormat ) {
							case 'dmy':
							case 'dmy_dash':
							case 'dmy_dot':
								$value = array(
									$day,
									$month,
									$year,
								);
								break;

							case 'ymd':
							case 'ymd_slash':
							case 'ymd_dash':
							case 'ymd_dot':
								$value = array(
									$year,
									$month,
									$day,
								);
								break;

							default:
								$value = array(
									$month,
									$day,
									$year,
								);
								break;

						}

						if ( ! in_array( rgar( $field, 'dateType' ), array( 'datedropdown', 'datefield' ), true ) ) {
							$value = GFCommon::date_display( $value, $field->dateFormat, false );
						}
					} else {
						$value = null;
					}

					break;

				default:
					// Get available inputs.
					$inputs = $field->get_entry_inputs();

					/**
					 * get_entry_inputs() in CC field excludes some inputs so we fallback to $field->input to prevent
					 * undefined offset notices.
					 *
					 * The CC field will automatically change all but the last four numbers to "X" when repopulated.
					 */
					if ( $field->type === 'creditcard' || $field->type === 'stripe_creditcard' ) {
						$inputs = $field->inputs;
					}

					if ( is_array( $inputs ) ) {

						// Populate each input individually.
						foreach ( $inputs as &$input ) {

							$parameter_value = null;

							// If the field already allows dynamic population and the input has a value, use it.
							if ( $field->allowsPrepopulate ) {
								$parameter_value = GFFormsModel::get_parameter_value( $input['name'], array(), $field );
							}

							if ( ! $parameter_value || ! $prefer_dynamic_population ) {
								$field->allowsPrepopulate = true;
								$input['name'] = $this->passthrough_value( $form['id'], $input['id'], rgar( $field_values, (string) $input['id'] ) );
							}

							// Unset reference to prevent unexpected changes where $input is referenced elsewhere in this function.
							unset( $input );

						}

						$field->inputs = $inputs;

					} else {

						// Get field value.
						$value = rgar( $field_values, $field->id );
						if ( is_array( $value ) ) {
							// Check for nested arrays and leave the value as is for filters to handle in that case
							$is_nested = array_reduce( $value, function( $carry, $item ) {
								return is_array( $item ) || $carry;
							}, false );

							$value = ( $is_nested ) ? $value : implode( ',', $value );
						}
					}

					break;

			}

			if ( rgblank( $value ) ) {
				continue;
			}

			// If Signature field, set as field value.
			if ( is_a( $field, 'GF_Field_Signature' ) ) {

				$field->defaultValue = $value;

			} else {
				$parameter_value = null;

				// If the field already allows dynamic population and has a value, use it.
				if ( $field->allowsPrepopulate ) {
					$parameter_value = GFFormsModel::get_parameter_value( $field->inputName, array(), $field );
				}

				if ( ! $parameter_value || ! $prefer_dynamic_population ) {
					$field->allowsPrepopulate = true;
					$field->inputName         = $this->passthrough_value( $form['id'], $field->id, $value );
				}
			}
		}

		/**
		* Modify form object after Easy Passthrough has been applied.
		*
		* @since 1.0.5
		*
		* @param array $form                   The current form object.
		* @param array $field_values           The prepared field values.
		* @param array $passed_through_entries The entry IDs used for passthrough and their form IDs.
		*/
		$form = gf_apply_filters( array(
			'gpep_form',
			$form['id'],
		), $form, $this->field_values, $this->passed_through_entries );

		return $form;

	}

	/**
	* Passthrough value to form using gform_field_value filter.
	*
	* @since  1.4
	* @access public
	*
	* @param int    $form_id  ID of form being populated.
	* @param string $input_id ID of input being populated.
	* @param mixed  $value    Value to populate.
	*
	* @return string
	*/
	public function passthrough_value( $form_id, $input_id, $value ) {

		// Prepare filter name.
		$filter_name = sprintf(
			'gpep_%s_%s',
			$form_id,
			str_replace( '.', '_', $input_id )
		);

		// Add filter.
		add_filter( 'gform_field_value_' . $filter_name, function( $val ) use ( $value ) {
			return $value;
		} );

		return $filter_name;

	}

	/**
	* Get field values for entry based on field dynamic parameter name.
	*
	* @since  1.0
	* @access public
	*
	* @param int $form_id Current form ID.
	*
	* @uses   GFAddOn::get_field_map_fields()
	*
	* @return array|bool
	*/
	public function get_field_values( $form_id = null ) {

		// If no form ID is set, return an empty array.
		if ( rgblank( $form_id ) ) {
			return array();
		}

		// Get session manager.
		$session = $this->session_manager();

		// If field value have already been prepared, return.
		if ( isset( $this->field_values[ $form_id ] ) ) {
			return $this->field_values[ $form_id ];
		}

		// Get target form.
		$target_form = GFAPI::get_form( $form_id );

		// Get Easy Passthrough feeds for form.
		$feeds = $this->get_active_feeds( $form_id );

		// If no results were found, return false.
		if ( empty( $feeds ) ) {

			// Set field values to false.
			$this->field_values[ $form_id ] = false;

			return $this->field_values[ $form_id ];

		}

		// Log that feeds were found.
		$this->log_debug( __METHOD__ . '(): Easy Passthrough configurations found for form #' . $form_id . '. Beginning preparation of field values.' );

		// Initialize field values array.
		$this->field_values[ $form_id ] = array();

		// Initialize passed through entries array.
		$this->passed_through_entries = array();

		// Loop through feeds.
		foreach ( $feeds as $feed ) {

			// Get source form ID.
			$source_form = $feed['meta']['sourceForm'];

			// Get source form.
			$source_form = GFAPI::get_form( $source_form );

			// Get source entry ID.
			if ( rgars( $feed, 'meta/userPassthrough' ) && is_user_logged_in() ) {

				// Log that we are searching for user entry.
				$this->log_debug( __METHOD__ . '(): Looking for last entry submitted to form #' . $source_form['id'] . ' by user; fallback to session entry.' );

				// Get last submitted entry for form.
				$last_submitted_entry = GFAPI::get_entries(
					$source_form['id'],
					array(
						'field_filters' => array(
							array(
								'key'   => 'created_by',
								'value' => get_current_user_id(),
							),
						),
						'status'        => 'active',
					),
					array(
						'key'       => 'date_created',
						'direction' => 'DESC',
					),
					array( 'page_size' => 1 )
				);

				// If an entry was found, use it.
				$source_entry_id = ( $last_submitted_entry && count( $last_submitted_entry ) == 1 ) ? $last_submitted_entry[0]['id'] : $session[ $this->get_slug() . '_' . $source_form['id'] ];

				// Store the entry ID if it has not yet been set in this session.
				if ( ! empty( $last_submitted_entry ) && empty( $session[ $this->get_slug() . '_' . $source_form['id'] ] ) ) {
					$this->store_entry_id( $last_submitted_entry[0], $source_form );
				}
			} else {

				/**
				 * Filter whether GPEP should disable passing through an entry if the source and target forms are the same.
				 *
				 * @since 1.9.6
				 *
				 * @param bool  $disable_same_form_passthrough  Do not pass through entries on the same form (Default: false)
				 * @param array $form                           The current GF form array
				 */
				if ( $source_form['id'] === $target_form['id'] && gf_apply_filters( array( 'gpep_disable_same_form_passthrough', $target_form['id'] ), false, $target_form ) ) {
					continue;
				}

				// Use entry ID from session.
				$source_entry_id = $session[ $this->get_slug() . '_' . $source_form['id'] ];

			}

			// If no entry exists for source form, skip.
			if ( rgblank( $source_entry_id ) ) {
				$this->log_debug( __METHOD__ . '(): No entry was found for source form #' . $form_id . ' in this session. Skipping.' );
				continue;
			}

			// Get source entry.
			$source_entry = GFAPI::get_entry( $source_entry_id );
			if ( is_wp_error( $source_entry ) || $source_entry['status'] !== 'active' ) {
				continue;
			}

			// If feed condition is not met, skip feed.
			if ( ! $this->is_feed_condition_met( $feed, $source_form, $source_entry ) ) {
				$this->log_debug( __METHOD__ . '(): Feed condition was not met for feed #' . $feed['id'] . '. Skipping.' );
				continue;
			}

			// Add entry to array.
			$this->passed_through_entries[] = array(
				'form_id'  => $source_form['id'],
				'entry_id' => $session[ $this->_slug . '_' . $source_form['id'] ],
			);

			// Get field map.
			$mapping = $this->get_field_map_fields( $feed, 'fieldMap' );

			// Loop through field mapping.
			foreach ( $mapping as $key => $value ) {

				// If key does not need to be converted, skip.
				if ( false === strpos( $key, '_' ) ) {
					continue;
				}

				// Convert key.
				$new_key = str_replace( '_', '.', $key );

				// Add to mapping.
				$mapping[ $new_key ] = $value;

				// Remove original mapping.
				unset( $mapping[ $key ] );

			}

			// Add meta map to mapping.
			$mapping += $this->get_dynamic_field_map_fields( $feed, 'metaMap' );
			$mapping  = array_filter( $mapping );

			// Loop through mapping.
			foreach ( $mapping as $source_field_id => $target_field_id ) {

				// Ensure $target_field_id is valid if target form has changed
				if ( ! is_numeric( $target_field_id ) ) {
					continue;
				}
				// Get source field.
				$source_field      = GFFormsModel::get_field( $source_form, $source_field_id );
				$source_field_type = $source_field ? $source_field->get_input_type() : null;

				// Get target field.
				$target_field = GFFormsModel::get_field( $target_form, $target_field_id );

				// Ensure that the target field is valid
				if ( ! $target_field ) {
					continue;
				}

				// Get list field value.
				if ( 'list' === $source_field_type && $source_field->enableColumns ) {

					// Get field value.
					$field_value = $this->get_field_value( $source_form, $source_entry, $source_field->id );

					// If field value is empty, skip it.
					if ( empty( $field_value ) ) {
						continue;
					}

					// Unserialize field value.
					$field_value = maybe_unserialize( $field_value );

					// Get column index.
					$column_index = explode( '.', $source_field_id );
					$column_index = end( $column_index );

					// Get column label.
					$column_label = $source_field->choices[ $column_index ]['text'];

					// Initialize column values array.
					$column_values = array();

					// Loop through field value and get column values.
					foreach ( $field_value as $row ) {

						// Add to column values.
						$column_values[] = array( $column_label => rgar( $row, $column_label ) );

					}

					// Initialize field value array.
					if ( ! isset( $this->field_values[ $target_field_id ] ) ) {
						$this->field_values[ $form_id ][ $target_field_id ] = array();
					}

					// Add field value to array.
					$this->field_values[ $form_id ][ $target_field_id ] = gf_apply_filters( array(
						'gpep_target_field_value',
						$form_id,
						$target_field_id,
					), array_merge( $this->field_values[ $form_id ][ $target_field_id ], $column_values ), $form_id, $target_field_id );

				} else {

					if ( $source_field ) {
						$is_signature            = $source_field->type === 'signature';
						$is_product_to_product   = GFCommon::is_product_field( $source_field->type ) && GFCommon::is_product_field( $target_field->type );
						$is_quiz_to_quiz         = $source_field->type === 'quiz' && $target_field->type === 'quiz';
						$is_survey_to_survey     = $source_field->type === 'survey' && $target_field->type === 'survey';
						$is_date_to_date         = $source_field->type === 'date' && $target_field->type === 'date';
						$is_checkbox_to_checkbox = $source_field->get_input_type() === 'checkbox'
												   && $target_field->get_input_type() === 'checkbox'
												   && absint( $source_field_id ) === $source_field_id
												   && absint( $target_field_id ) === $target_field_id;
					}

					// Get field value.
					if ( $source_field && ( $is_signature || $is_product_to_product || $is_quiz_to_quiz || $is_survey_to_survey ) ) {
						$field_value = rgar( $source_entry, $source_field_id );
					} elseif ( $source_field && $is_checkbox_to_checkbox ) {
						$field_value = array();

						foreach ( $source_entry as $input_key => $value ) {
							if ( absint( $input_key ) !== $source_field_id ) {
								continue;
							}

							$field_value[] = $value;
						}
					} elseif ( $source_field && $is_date_to_date ) {
						$field_value = GFCommon::parse_date( rgar( $source_entry, $source_field_id ), $source_field->dateFormat );
					} else {
						$field_value = $this->get_field_value( $source_form, $source_entry, $source_field_id );
					}

					// Unserialize field value.
					$field_value = maybe_unserialize( $field_value );

					// Add field value to array.
					$this->field_values[ $form_id ][ $target_field_id ] = gf_apply_filters( array(
						'gpep_target_field_value',
						$form_id,
						$target_field_id,
					), $field_value, $form_id, $target_field_id );

				}
			}
		}

		// Remove empty keys.
		unset( $this->field_values[ $form_id ][ null ] );

		// Log prepared field values.
		$this->log_debug( __METHOD__ . '(): Prepared field values for form #' . $form_id . ': ' . print_r( $this->field_values[ $form_id ], true ) );

		/**
		* Modify generated field values.
		*
		* @since 1.0.5
		*
		* @param array $field_values The prepared field values.
		* @param int   $form_id      The current form ID being prepared for Easy Passthrough.
		*/
		$this->field_values[ $form_id ] = gf_apply_filters( array(
			'gpep_field_values',
			$form_id,
		), $this->field_values[ $form_id ], $form_id );

		return $this->field_values[ $form_id ];

	}





	// # MERGE TAGS ----------------------------------------------------------------------------------------------------

	/**
	* Add Easy Passthrough merge tags.
	*
	* @since  1.1
	* @access public
	*
	* @param array $form The form object.
	*
	* @uses   GP_Easy_Passthrough::has_easy_passthrough_feeds()
	*
	* @return array
	*/
	public function add_merge_tags( $form ) {

		// If this form does not have any Easy Passthrough feeds, return.
		if ( ! $this->has_easy_passthrough_feeds( $form['id'] ) ) {
			return $form;
		}

		// If the header has already been output, add merge tags script in the footer.
		if ( ! did_action( 'admin_head' ) ) {
			add_action( 'admin_footer', array( $this, 'add_merge_tags_footer' ) );

			return $form;
		}

		?>

		<script type="text/javascript">

			( function ( $ ) {

				if ( window.gform ) {

					gform.addFilter( 'gform_merge_tags', function ( mergeTags ) {

						mergeTags[ 'gp_easy_passthrough' ] = {
							label: '<?php _e( 'Easy Passthrough', 'gp-easy-passthrough' ); ?>',
							tags:  [
								{
									tag:   '{Easy Passthrough Token}',
									label: '<?php _e( 'Easy Passthrough Token', 'gp-easy-passthrough' ); ?>'
								}
							]
						};

						return mergeTags;

					} );

				}

			} )( jQuery );

		</script>

		<?php
		return $form;

	}

	/**
	* Add Easy Passthrough merge tags in admin footer.
	*
	* @since  1.1
	* @access public
	*
	* @uses   GP_Easy_Passthrough::add_merge_tags()
	* @uses   GFAddOn::get_current_form()
	*/
	public function add_merge_tags_footer() {

		// Get current form.
		$form = $this->get_current_form();

		// If form was found, include merge tags script.
		if ( $form ) {
			$this->add_merge_tags( $form );
		}

	}

	/**
	* Replace Easy Passthrough merge tags.
	*
	* @since  1.1
	* @access public
	*
	* @param string $text  The current text in which merge tags are being replaced.
	* @param array  $form  The current form.
	* @param array  $entry The current entry.
	*
	* @return string
	*/
	public function replace_merge_tags( $text, $form, $entry ) {

		// If text does not contain any merge tags, return.
		if ( false === strpos( $text, '{' ) ) {
			return $text;
		}

		// Search for merge tags in text.
		preg_match_all( '/({Easy Passthrough Token})/mi', $text, $matches, PREG_SET_ORDER );

		// Loop through matches.
		foreach ( $matches as $match ) {

			// Get parts.
			$merge_tag = $match[0];

			// If this is not the Easy Passthrough merge tag, skip it.
			if ( strpos( strtolower( $merge_tag ), '{easy passthrough token' ) !== 0 ) {
				continue;
			}

			// Get token for entry.
			$token = $this->get_entry_token( $entry );

			// Replace merge tag.
			$text = str_replace( $merge_tag, $token, $text );

		}

		return $text;

	}





	// # HELPER METHODS ------------------------------------------------------------------------------------------------

	/**
	* Generate Easy Passthrough token when Entry is created.
	*
	* @since 1.4.2
	*
	* @param array $entry Entry object.
	*
	* @return array
	*/
	public function filter_gform_entry_post_save( $entry ) {

		// Generate token.
		$token = $this->get_entry_token( $entry );

		// Log that token was generated.
		if ( $token ) {
			$this->log_debug( __METHOD__ . '(): Token generated for entry #' . $entry['id'] );
		} else {
			$this->log_error( __METHOD__ . '(): Token could not be generated for entry #' . $entry['id'] );
		}

		// Add token to entry.
		$entry['fg_easypassthrough_token'] = $token;

		return $entry;

	}

	/**
	* Activate and configure entry meta.
	*
	* @since  1.4
	* @access public
	*
	* @param array $entry_meta An array of entry meta already registered with the gform_entry_meta filter.
	* @param int   $form_id    The form ID.
	*
	* @return array
	*/
	public function get_entry_meta( $entry_meta, $form_id ) {

		$entry_meta['fg_easypassthrough_token'] = array(
			'label'             => 'Easy Passthrough Token',
			'is_numeric'        => false,
			'is_default_column' => false,
		);

		return $entry_meta;

	}

	/**
	* Get Easy Passthrough token for entry.
	*
	* @since  1.1.7
	* @access public
	*
	* @param array|int $entry Entry object or ID.
	*
	* @return string|bool
	*/
	public function get_entry_token( $entry ) {

		// Get entry ID.
		$entry_id = is_numeric( $entry ) ? $entry : rgar( $entry, 'id' );

		// If entry ID is not provided, return.
		if ( ! $entry_id ) {
			return false;
		}

		// Get existing token for entry.
		$token = gform_get_meta( $entry_id, 'fg_easypassthrough_token' );

		// If token exists, return it.
		if ( $token ) {
			return $token;
		}

		// Generate token.
		$token = md5( uniqid() . time() . $entry['id'] );

		// Save token.
		gform_update_meta( $entry_id, 'fg_easypassthrough_token', $token );

		return $token;

	}

	/**
	* Get entry using Easy Passthrough token.
	*
	* @since  1.1
	* @access public
	*
	* @param string $token Easy Passthrough token.
	*
	* @uses   GFAPI::get_entry()
	* @uses   GFFormsModel::get_lead_meta_table_name()
	* @uses   wpdb::get_var()
	* @uses   wpdb::prepare()
	*
	* @return array|null
	*/
	public function get_entry_for_token( $token ) {

		global $wpdb;

		// Get entry ID based on Gravity Forms database version.
		if ( version_compare( self::get_gravityforms_db_version(), '2.3-dev-1', '<' ) ) {

			// Get entry meta table name.
			$table_name = GFFormsModel::get_lead_meta_table_name();

			// Get entry ID.
			$entry_id = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT lead_id FROM {$table_name} WHERE `meta_key` = '%s' AND `meta_value` = '%s'",
					'fg_easypassthrough_token',
					$token
				)
			);

		} else {

			// Get entry meta table name.
			$table_name = GFFormsModel::get_entry_meta_table_name();

			// Get entry ID.
			$entry_id = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT entry_id FROM {$table_name} WHERE `meta_key` = '%s' AND `meta_value` = '%s'",
					'fg_easypassthrough_token',
					$token
				)
			);

		}

		// If entry ID was not found, return.
		if ( ! $entry_id ) {
			return null;
		}

		$entry = GFAPI::get_entry( $entry_id );
		// Do not load trashed entries
		if ( $entry['status'] !== 'active' ) {
			return null;
		}

		return $entry;

	}

	/**
	 * Suffix individual checkbox inputs with the checkbox name to make target form field dropdown consistent with the
	 * source form field column
	 *
	 * @param array $choices
	 * @param int $form_id
	 * @param string $field_type
	 * @param array $exclude_field_types
	 *
	 * @return array
	 */
	public function append_checkbox_name( $choices, $form_id, $field_type, $exclude_field_types ) {
		/*
		 * This filter is hooked to gform_field_map_choices as gform_{$slug}_field_map_choices is no longer in use.
		 */
		if ( rgget( 'subview' ) !== 'gp-easy-passthrough' ) {
			return $choices;
		}

		if ( empty( $choices['fields'] ) || empty( $choices['fields']['choices'] ) ) {
			return $choices;
		}

		foreach ( $choices['fields']['choices'] as &$choice ) {
			$field_id = absint( $choice['value'] );

			if ( rgar( $choice, 'type' ) !== 'checkbox' || $field_id == $choice['value'] ) {
				continue;
			}

			$field           = GFAPI::get_field( $form_id, $field_id );
			$choice['label'] = $choice['label'] . ' (' . esc_html( $field['label'] ) . ')';
		}

		return $choices;
	}

	/**
	* Get Gravity Forms database version number.
	*
	* @since 1.3
	* @access public
	*
	* @uses GFFormsModel::get_database_version()
	*
	* @return string
	*/
	public static function get_gravityforms_db_version() {

		if ( method_exists( 'GFFormsModel', 'get_database_version' ) ) {
			$db_version = GFFormsModel::get_database_version();
		} else {
			$db_version = GFForms::$version;
		}

		return $db_version;

	}

	/**
	* Override value form GFAddOn::get_field_value().
	*
	* @since  1.1.2
	* @access public
	*
	* @param string $field_value The current field value.
	* @param array  $form        The current Form object.
	* @param array  $entry       The current Entry object.
	* @param string $field_id    The current field ID.
	*
	* @return string
	*/
	public function override_field_value( $field_value, $form, $entry, $field_id ) {

		if ( is_numeric( $field_id ) ) {

			// Get field.
			$field = GFFormsModel::get_field( $form, $field_id );

			// If this is not a List or Multi Select field, return.
			if ( in_array( $field->type, array( 'list', 'multiselect' ) ) ) {
				$field_value = rgar( $entry, $field_id );
			}
		}

		return $field_value;
	}

	/**
	* Check if form has any Easy Passthrough feeds where it is the source form.
	*
	* @since  1.1
	* @access public
	*
	* @param int $form_id Form ID.
	*
	* @return bool
	*/
	public function has_easy_passthrough_feeds( $form_id ) {

		// Get Easy Passthrough feeds.
		$feeds = $this->get_feeds();

		// If no Easy Passthrough feeds are configured, return.
		if ( empty( $feeds ) ) {
			return false;
		}

		// Loop through feeds.
		foreach ( $feeds as $feed ) {

			// If form ID is the source form, return.
			if ( intval( $feed['meta']['sourceForm'] ) == $form_id ) {
				return true;
			}
		}

		return false;

	}

	/**
	* Get an instance of WP Session Manager.
	*
	* @since  1.0
	* @access public
	*
	* @uses   \GP_Easy_Passthrough\WP_Session::get_instance()
	*
	* @return \GP_Easy_Passthrough\WP_Session
	*/
	public function session_manager() {

		// let users change the session cookie name
		if ( ! defined( 'GPEP_SESSION_COOKIE' ) ) {
			define( 'GPEP_SESSION_COOKIE', 'gp_easy_passthrough_session' );
		}

		if ( ! class_exists( '\Recursive_ArrayAccess' ) ) {
			include 'includes/wp-session-manager/class-recursive-arrayaccess.php';
		}

		// Include utilities class
		if ( ! class_exists( '\GP_Easy_Passthrough\WP_Session_Utils' ) ) {
			include 'includes/wp-session-manager/class-wp-session-utils.php';
		}

		// Only include the functionality if it's not pre-defined.
		if ( ! class_exists( '\GP_Easy_Passthrough\WP_Session' ) ) {
			include 'includes/wp-session-manager/class-wp-session.php';
			include 'includes/wp-session-manager/wp-session.php';
		}

		return \GP_Easy_Passthrough\WP_Session::get_instance();

	}

	// # MEMBERS INTEGRATION -------------------------------------------------------------------------------------------

	/**
	* Register the capabilities and their human readable labels wit the Members plugin.
	*
	* @since  1.2.1
	* @access public
	*/
	public function members_register_caps() {

		// Define capabilities for Easy Passthrough.
		$caps = array(
			'gp_easy_passthrough'           => esc_html__( 'Manage Settings', 'gp-easy-passthrough' ),
			'gp_easy_passthrough_uninstall' => esc_html__( 'Uninstall', 'gp-easy-passthrough' ),
		);

		// Register capabilities.
		foreach ( $caps as $cap => $label ) {
			members_register_cap(
				$cap,
				array(
					'label' => sprintf( '%s: %s', $this->get_short_title(), $label ),
					'group' => 'gravityforms_addons',
				)
			);
		}

	}

	// # FORGRAVITY UPGRADE ROUTINE ------------------------------------------------------------------------------------

	/**
	* Show admin notice if user can activate plugins and ForGravity Easy Passthrough is detected.
	*
	* @since  1.4.4
	* @access public
	*/
	public function maybe_show_fg_easy_passthrough_upgrade_notice() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		$upgrade_url  = wp_nonce_url( add_query_arg( 'gpep-upgrade', 'forgravity-ep' ), 'gpep-upgrade-fg' );
		$kses_allowed = array( 'em' => array() );
		?>
		<div class="notice notice-warning gp-upgrade-notice" style="border-left-color: #6e4c88">
			<p>
				<strong><?php echo wp_kses( __( 'Heads up! It looks like you’re using the <em>Easy Passthrough for Gravity Forms</em> plugin or have used it in the past.', 'gp-easy-passthrough' ), $kses_allowed ); ?></strong>
			</p>

			<p>
				<?php echo wp_kses( __( 'This plugin is not compatible with the new <em>GP Easy Passthrough</em> plugin that is part of the Gravity Perks suite. For the best experience, we will need to:', 'gp-easy-passthrough' ), $kses_allowed ); ?>
			</p>

			<ul style="list-style: disc;padding: 0 0 0 20px;">
				<li><?php echo wp_kses( __( 'Migrate <em>Easy Passthrough for Gravity Forms</em> data to <em>GP Easy Passthrough</em>.', 'gp-easy-passthrough' ), $kses_allowed ); ?></li>
				<li><?php echo wp_kses( __( 'Deactivate the <em>Easy Passthrough for Gravity Forms</em> plugin.', 'gp-easy-passthrough' ), $kses_allowed ); ?></li>
			</ul>

			<p>
				<?php _e( 'All passthrough functionality will continue to function after the upgrade has been completed.', 'gp-easy-passthrough' ); ?>
			</p>

			<p>
				<a href="<?php echo esc_url( $upgrade_url ); ?>"
				   class="button button-secondary">Start Upgrade</a>
			</p>
		</div>
		<?php
	}

	/**
	* Process upgrade from ForGravity Easy Passthrough to GP Easy Passthrough.
	*
	* Only run if $_GET['gpep-upgrade'] == 'forgravity-ep', use has activate_plugins cap, and nonce matches.
	*
	* @since  1.4.4
	* @access public
	*/
	public function maybe_upgrade_fg_easy_passthrough() {
		global $wpdb;

		if ( rgget( 'gpep-upgrade' ) !== 'forgravity-ep' ) {
			return;
		}

		if ( ! current_user_can( 'activate_plugins' ) || ! wp_verify_nonce( rgget( '_wpnonce' ), 'gpep-upgrade-fg' ) ) {
			return;
		}

		/* Upgrade feeds */
		$fgep_slug = 'forgravity-easypassthrough';
		$query     = $wpdb->prepare( "UPDATE {$wpdb->prefix}gf_addon_feed SET addon_slug = %s WHERE addon_slug = %s", $this->_slug, $fgep_slug );

		$wpdb->query( $query );

		/* Deactivate ForGravity Easy Passthrough */
		deactivate_plugins( 'forgravity-easypassthrough/easypassthrough.php' );

		/* Set flag in database */
		$option_name = 'gp_easy_passthrough_upgrades';
		$upgrades    = get_option( $option_name, array() );

		$upgrades[] = 'forgravity-easy-passthrough';

		update_option( 'gp_easy_passthrough_upgrades', $upgrades );

		/* Queue up notices */
		remove_action( 'admin_notices', array( $this, 'maybe_show_fg_easy_passthrough_upgrade_notice' ) );
		add_action( 'admin_notices', array( $this, 'show_fg_easy_passthrough_upgrade_successful_notice' ) );
	}

	/**
	* Show notice if FG EP to GP EP upgrade was successful.
	*/
	public function show_fg_easy_passthrough_upgrade_successful_notice() {
		$kses_allowed = array( 'em' => array() );

		?>
		<div class="notice notice-success">
			<p>
				<strong><?php _e( 'Upgrade Successful!', 'gp-easy-passthrough' ); ?></strong>
			</p>

			<p>
				<?php echo wp_kses( __( '<em>Easy Passthrough for Gravity Forms</em> has been deactivated and its data has been successfully migrated to <em>GP Easy Passthrough</em>.', 'gp-easy-passthrough' ), $kses_allowed ); ?>
			</p>
		</div>
		<?php
	}

	/**
	 * Check if installed version of Gravity Forms is greater than or equal to the specified version.
	 *
	 * @param string $version Version to compare with Gravity Forms' version.
	 *
	 * @return bool
	 */
	public function is_gf_version_gte( $version ) {
		return class_exists( 'GFForms' ) && version_compare( GFForms::$version, $version, '>=' );
	}

	/**
	 * Clear session cookie when users logout.
	 *
	 * @param int $user_id Current user being logged out
	 *
	 * @return void
	 */
	public function clear_session() {
		setcookie( 'gp_easy_passthrough_session', '', time() - 3600 );
	}

}

function gp_easy_passthrough() {
	return GP_Easy_Passthrough::get_instance();
}

GFAddOn::register( 'GP_Easy_Passthrough' );

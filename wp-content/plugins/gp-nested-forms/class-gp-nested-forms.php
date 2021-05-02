<?php

if ( ! class_exists( 'GP_Plugin' ) ) {
	return;
}

class GP_Nested_Forms extends GP_Plugin {

	protected $_version     = GP_NESTED_FORMS_VERSION;
	protected $_path        = 'gp-nested-forms/gp-nested-forms.php';
	protected $_full_path   = __FILE__;
	protected $_slug        = 'gp-nested-forms';
	protected $_title       = 'Gravity Forms Nested Forms';
	protected $_short_title = 'Nested Forms';

	public $parent_form_id = null;
	public $field_type     = 'form';

	public static $nested_forms_markup = array();

	private static $instance = null;

	public static function get_instance() {

		if ( self::$instance === null ) {
			self::includes();
			self::$instance = isset( self::$perk_class ) ? new self( new self::$perk_class ) : new self();
		}

		return self::$instance;
	}

	public static function includes() { }

	public function minimum_requirements() {
		return array(
			'gravityforms' => array(
				'version' => '2.4',
			),
			'wordpress'    => array(
				'version' => '4.9',
			),
			'plugins'      => array(
				'gravityperks/gravityperks.php' => array(
					'name'    => 'Gravity Perks',
					'version' => '2.2.3',
				),
			),
		);
	}

	public function pre_init() {

		parent::pre_init();

		$this->setup_cron();

		require_once( 'includes/class-gp-template.php' );
		require_once( 'includes/class-gp-field-nested-form.php' );
		require_once( 'includes/class-gpnf-feed-processing.php' );
		require_once( 'includes/class-gpnf-gravityview.php' );
		require_once( 'includes/class-gpnf-gfml.php' );
		require_once( 'includes/class-gpnf-wc-product-addons.php' );
		require_once( 'includes/class-gpnf-merge-tags.php' );
		require_once( 'includes/class-gpnf-parent-merge-tag.php' );
		require_once( 'includes/class-gpnf-notification-processing.php' );
		require_once( 'includes/class-gpnf-zapier.php' );
		require_once( 'includes/class-gpnf-entry.php' );
		require_once( 'includes/class-gpnf-session.php' );
		require_once( 'includes/class-gpnf-export.php' );

		// Nested Form fields have a dynamically retrieved value set via this filter and needs to be in place as early as possible.
		add_filter( 'gform_get_input_value', array( $this, 'handle_nested_form_field_value' ), 10, 3 );

		// Must happen on pre_init to intercept the 'gform_export_form' filter.
		gpnf_export();

	}

	public function init() {

		parent::init();

		load_plugin_textdomain( 'gp-nested-forms', false, basename( dirname( __file__ ) ) . '/languages/' );

		// Initialize sub classes.
		gpnf_gravityview();
		gpnf_gfml();
		gpnf_feed_processing();
		gpnf_parent_merge_tag();
		gpnf_notification_processing();
		gpnf_zapier();
		gpnf_merge_tags();
		gpnf_wc_product_addons();

		// General Hooks
		add_action( 'gform_form_args', array( $this, 'stash_shortcode_field_values' ) );
		add_action( 'gform_pre_validation', array( $this, 'maybe_load_nested_form_hooks' ) );
		add_action( 'gform_pre_render', array( $this, 'maybe_load_nested_form_hooks' ) );
		add_filter( 'gform_entry_meta', array( $this, 'register_entry_meta' ) );
		add_filter( 'gform_merge_tag_filter', array( $this, 'all_fields_value' ), 11, 6 );
		add_action( 'gform_calculation_formula', array( $this, 'process_merge_tags' ), 10, 4 );
		add_filter( 'gform_custom_merge_tags', array( $this, 'add_nested_form_field_total_merge_tag' ), 10, 4 );

		// Handle parent form.
		add_action( 'gform_register_init_scripts', array( $this, 'register_all_form_init_scripts' ) );
		add_action( 'gform_entry_created', array( $this, 'handle_parent_submission' ), 10, 2 );
		add_action( 'gform_after_update_entry', array( $this, 'handle_parent_update_entry' ), 10, 2 );
		add_action( 'gform_entry_post_save', array( $this, 'handle_parent_submission_post_save' ), 20 /* should happen well after feeds are processed on 10 */, 2 );

		// Handle nested form.
		add_action( 'gform_get_form_filter', array( $this, 'handle_nested_forms_markup' ), 10, 2 );
		add_filter( 'gform_confirmation', array( $this, 'handle_nested_confirmation' ), 10, 3 );
		add_filter( 'gform_confirmation_anchor', array( $this, 'handle_nested_confirmation_anchor' ) );
		add_action( 'gform_entry_id_pre_save_lead', array( $this, 'maybe_edit_entry' ), 10, 2 );
		add_action( 'gform_entry_post_save', array( $this, 'add_child_entry_meta' ), 10, 2 );

		// Administrative hooks.
		// Trash child entries when a parent entry is trashed or deleted.
		add_action( 'gform_update_status', array( $this, 'child_entry_trash_manage' ), 10, 3 );
		// Delete child entries when parent entry is permanently deleted.
		add_action( 'gform_delete_entry', array( $this, 'child_entry_delete' ), 10 );
		// Filter child entries by parent entry ID in the List View.
		add_filter( 'gform_get_entries_args_entry_list', array( $this, 'filter_entry_list' ) );
		// Add support for processing nested forms in Gravity Forms preview.
		add_action( 'wp', array( $this, 'handle_core_preview_ajax' ), 9 );
		// Add support for filtering by Parent Entry ID in Entries List or and plugins like Gravity Flow Form Connector
		add_filter( 'gform_field_filters', array( $this, 'add_parent_form_filter' ), 10, 2 );

		// Integrations.
		add_filter( 'gform_webhooks_request_data', array( $this, 'add_full_child_entry_data_for_webhooks' ), 10, 4 );
		add_filter( 'gform_partialentries_post_entry_saved', array( $this, 'adopt_partial_entry_children' ), 10, 2 );
		add_filter( 'gform_partialentries_post_entry_updated', array( $this, 'adopt_partial_entry_children' ), 10, 2 );

		// Integration Fixes.
		if ( $this->use_jquery_ui_dialog() ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'fix_jquery_ui_issue' ), 1000 );
		}

		add_filter( 'gform_enqueue_scripts', array( $this, 'enqueue_child_form_scripts' ) );

		add_filter( 'gform_field_input', array( $this, 'rerender_signature_field_on_edit' ), 10, 5 );

		// Make single entry label and plural entry label translatable via WPML.
		add_filter( 'gform_multilingual_field_keys', array( $this, 'wpml_translate_entry_labels' ) );

		// Clear form's nested entries if save and continue is used (migrated from snippet library)
		add_action( 'gform_post_process', function( $form ) {
			if ( rgpost( 'gform_save' ) && class_exists( 'GPNF_Session' ) ) {
				$session = new GPNF_Session( $form['id'] );
				$session->delete_cookie();
			}
		} );
	}

	public function init_admin() {

		parent::init_admin();

		add_filter( 'gform_admin_pre_render', array( $this, 'cleanup_form_meta' ) );

		// Field Settings
		add_action( 'gform_field_standard_settings_1430', array( $this, 'editor_field_standard_settings' ) );
		add_action( 'gform_field_appearance_settings_500', array( $this, 'editor_field_appearance_settings' ) );
		add_action( 'gform_field_advanced_settings_400', array( $this, 'editor_field_advanced_settings' ) );

	}

	public function init_ajax() {

		parent::init_ajax();

		// AJAX
		add_action( 'wp_ajax_gpnf_get_form_fields', array( $this, 'ajax_get_form_fields' ) );
		add_action( 'wp_ajax_nopriv_gpnf_get_form_fields', array( $this, 'ajax_get_form_fields' ) );
		add_action( 'wp_ajax_gpnf_delete_entry', array( $this, 'ajax_delete_entry' ) );
		add_action( 'wp_ajax_nopriv_gpnf_delete_entry', array( $this, 'ajax_delete_entry' ) );
		add_action( 'wp_ajax_gpnf_edit_entry', array( $this, 'ajax_edit_entry' ) );
		add_action( 'wp_ajax_nopriv_gpnf_edit_entry', array( $this, 'ajax_edit_entry' ) );
		add_action( 'wp_ajax_gpnf_refresh_markup', array( $this, 'ajax_refresh_markup' ) );
		add_action( 'wp_ajax_nopriv_gpnf_refresh_markup', array( $this, 'ajax_refresh_markup' ) );
		add_action( 'wp_ajax_gpnf_duplicate_entry', array( $this, 'ajax_duplicate_entry' ) );
		add_action( 'wp_ajax_nopriv_gpnf_duplicate_entry', array( $this, 'ajax_duplicate_entry' ) );
		add_action( 'wp_ajax_gpnf_session', array( $this, 'ajax_session' ) );
		add_action( 'wp_ajax_nopriv_gpnf_session', array( $this, 'ajax_session' ) );

	}

	public function upgrade( $previous_version ) {
		global $wpdb;

		if ( ! $previous_version ) {
			return;
		}

		if ( version_compare( $previous_version, '1.0-beta-8', '<' ) ) {
			add_option( 'gpnf_use_jquery_ui', true );
		}

		if ( version_compare( $previous_version, '1.0-beta-5', '<' ) ) {

			// Delete expiration meta key from entries that have a valid parent entry ID.
			$sql = $wpdb->prepare(
				"
				DELETE em1 FROM {$wpdb->prefix}gf_entry_meta em1
				INNER JOIN {$wpdb->prefix}gf_entry_meta em2 ON em2.entry_id = em1.entry_id
				WHERE em1.meta_key = '%s'
				AND em2.meta_key = '%s'
				AND concat( '', em2.meta_value * 1 ) = em2.meta_value",
				GPNF_Entry::ENTRY_EXP_KEY,
				GPNF_Entry::ENTRY_PARENT_KEY
			);

			$wpdb->query( $sql );

		}

	}

	public function tooltips( $tooltips ) {

		$template = '<h6>%s</h6> %s';

		$tooltips['gpnf_form']               = sprintf( $template, __( 'Nested Form', 'gp-nested-forms' ), __( 'Select the form that should be used to create nested entries for this form.', 'gp-nested-forms' ) );
		$tooltips['gpnf_fields']             = sprintf( $template, __( 'Summary Fields', 'gp-nested-forms' ), __( 'Select which fields from the nested entry will display in table on the current form. This does not affect which fields will appear in the modal.', 'gp-nested-forms' ) );
		$tooltips['gpnf_entry_labels']       = sprintf( $template, __( 'Entry Labels', 'gp-nested-forms' ), __( 'Specify a singular and plural label with which entries submitted via this field will be labeled (i.e. "employee", "employees").', 'gp-nested-forms' ) );
		$tooltips['gpnf_entry_limits']       = sprintf( $template, __( 'Entry Limits', 'gp-nested-forms' ), __( 'Specify the minimum and maximum number of entries that can be submitted for this field.', 'gp-nested-forms' ) );
		$tooltips['gpnf_feed_processing']    = sprintf( $template, __( 'Feed Processing', 'gp-nested-forms' ), __( 'By default, any Gravity Forms add-on feeds will be processed immediately when the nested form is submitted. Use this option to delay feed processing for entries submitted via the nested form until after the parent form is submitted. <br><br>For example, if you have a User Registration feed configured for the nested form, you may not want the users to actually be registered until the parent form is submitted.', 'gp-nested-forms' ) );
		$tooltips['gpnf_modal_header_color'] = sprintf( $template, __( 'Modal Color', 'gp-nested-forms' ), __( 'Select a color which will be used to set the background color of the nested form modal header and navigational buttons.', 'gp-nested-forms' ) );

		return $tooltips;
	}

	public function scripts() {

		$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG || isset( $_GET['gform_debug'] ) ? '' : '.min';

		$scripts = array();

		// Don't include select2 on non-GF pages. Solves issues with ACF where our version of select2 is registered but it is expecting it's own.
		if ( GFForms::is_gravity_page() ) {

			$deps = array( 'jquery' );

			if ( ! $this->is_gf_version_gte( '2.5-dev-1' ) ) {
				$deps[]    = 'select2';
				$scripts[] = array(
					'handle'  => 'select2',
					'src'     => $this->get_base_url() . '/js/select2.min.js',
					'version' => '4.0.3',
					'enqueue' => null,
				);
			} else {
				$deps[] = 'gform_selectwoo';
			}

			$scripts[] = array(
				'handle'   => 'gp-nested-forms-admin',
				'src'      => $this->get_base_url() . "/js/gp-nested-forms-admin{$min}.js",
				'version'  => $this->_version,
				'deps'     => $deps,
				'enqueue'  => array(
					array( 'admin_page' => array( 'form_editor' ) ),
				),
				'callback' => array( $this, 'localize_scripts' ),
			);
		}

		$scripts[] = array(
			'handle'  => 'knockout',
			'src'     => $this->get_base_url() . '/js/knockout-3.5.1.js',
			'version' => $this->_version,
			'enqueue' => null,
		);

		$deps = array( 'jquery', 'knockout', 'gform_gravityforms' );
		if ( $this->use_jquery_ui_dialog() ) {
			$deps[] = 'jquery-ui-dialog';
		} else {
			$scripts[] = array(
				'handle'  => 'tingle',
				'src'     => $this->get_base_url() . "/js/tingle{$min}.js",
				'version' => $this->_version,
				'enqueue' => null,
			);
			$deps[]    = 'tingle';
		}

		$src = $this->use_jquery_ui_dialog() ? "/js/gp-nested-forms-jquery-ui{$min}.js" : "/js/gp-nested-forms{$min}.js";

		$scripts[] = array(
			'handle'   => 'gp-nested-forms',
			'src'      => $this->get_base_url() . $src,
			'version'  => $this->_version,
			'deps'     => $deps,
			'enqueue'  => array(
				array( $this, 'should_enqueue_frontend_script' ),
			),
			'callback' => array( $this, 'localize_scripts' ),
		);

		return array_merge( parent::scripts(), $scripts );
	}

	public function styles() {

		$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG || isset( $_GET['gform_debug'] ) ? '' : '.min';

		$styles = array(
			array(
				'handle'  => 'select2',
				'src'     => $this->get_base_url() . '/css/select2.min.css',
				'version' => '4.0.3',
				'enqueue' => array(
					array( 'admin_page' => array( 'form_editor' ) ),
				),
			),
			array(
				'handle'  => 'gp-nested-forms-admin',
				'src'     => $this->get_base_url() . "/css/gp-nested-forms-admin{$min}.css",
				'version' => $this->_version,
				'deps'    => array(),
				'enqueue' => array(
					array( 'admin_page' => array( 'form_editor', 'entry_view', 'entry_edit' ) ),
				),
			),
		);

		$deps = array();
		if ( ! $this->use_jquery_ui_dialog() ) {
			$styles[] = array(
				'handle'  => 'tingle',
				'src'     => $this->get_base_url() . "/css/tingle{$min}.css",
				'version' => $this->_version,
				'enqueue' => null,
			);
			$deps[]   = 'tingle';
		}

		$styles[] = array(
			'handle'  => 'gp-nested-forms',
			'src'     => $this->get_base_url() . "/css/gp-nested-forms{$min}.css",
			'version' => $this->_version,
			'deps'    => $deps,
			'enqueue' => array(
				array( $this, 'should_enqueue_frontend_script' ),
			),
		);

		return array_merge( parent::styles(), $styles );
	}

	public function should_enqueue_frontend_script( $form ) {
		return ! GFForms::get_page() && ! rgempty( GFFormsModel::get_fields_by_type( $form, array( 'form' ) ) );
	}

	public function use_jquery_ui_dialog() {

		$raw = (bool) get_option( 'gpnf_use_jquery_ui' );
		/**
		 * Filter whether jQuery UI Dialog should be used to power the Nested Forms modal experience.
		 *
		 * @since 1.0-beta-8
		 *
		 * @param bool $use_jquery_ui Should jQuery UI Dialog be used to power the modal experience?
		 */
		$filtered      = (bool) apply_filters( 'gpnf_use_jquery_ui', $raw );
		$use_jquery_ui = $filtered;

		if ( $filtered !== $raw ) {
			update_option( 'gpnf_use_jquery_ui', $filtered );
		}

		return $use_jquery_ui;
	}

	public function localize_scripts( $form ) {

		wp_localize_script(
			'gp-nested-forms-admin',
			'GPNFAdminData',
			array(
				'nonces'  => array(
					'getFormFields' => wp_create_nonce( 'gpnf_get_form_fields' ),
				),
				'strings' => array(
					'getFormFieldsError'       => esc_html__( 'There was an error retrieving the fields for this form. Please try again or contact support.', 'gp-nested-forms' ),
					'displayFieldsPlaceholder' => esc_html__( 'Select your fields', 'gp-nested-forms' ),
				),
			)
		);

		wp_localize_script(
			'gp-nested-forms',
			'GPNFData',
			array(
				'nonces'  => array(
					'editEntry'      => wp_create_nonce( 'gpnf_edit_entry' ),
					'refreshMarkup'  => wp_create_nonce( 'gpnf_refresh_markup' ),
					'deleteEntry'    => wp_create_nonce( 'gpnf_delete_entry' ),
					'duplicateEntry' => wp_create_nonce( 'gpnf_duplicate_entry' ),
				),
				'strings' => array(),
			)
		);

		foreach ( $form['fields'] as $field ) {
			if ( $field->get_input_type() === 'form' ) {
				$nested_form = $this->get_nested_form( $field->gpnfForm );
				if ( ! $nested_form ) {
					continue;
				}
				foreach ( $nested_form['fields'] as $_field ) {
					if ( $_field->get_input_type() === 'fileupload' && $_field->multipleFiles ) {
						GFCommon::localize_gform_gravityforms_multifile();
					}
				}
			}
		}

	}

	/**
	 * If certain scripts are loaded *after* jQuery UI it hides the close button in the modal. If one of these scripts
	 * is enqueued, let's add it as a dependency to jQuery UI so that script will be loaded first.
	 *
	 * @deprecated 1.0-beta-8
	 *
	 * @see https://stackoverflow.com/questions/17367736/jquery-ui-dialog-missing-close-icon
	 */
	public function fix_jquery_ui_issue() {

		$deps = array(
			/**
			 * Beaver Builder Theme
			 * https://www.wpbeaverbuilder.com/
			 */
			'bootstrap',
			/**
			 * Understrap Theme
			 * https://github.com/understrap/understrap
			 */
			'understrap-scripts',
		);

		/**
		 * Filter the dependencies for jQuery UI.
		 *
		 * Allows 3rd parties to avoid the issue where the modal close button is not present if certain scripts they
		 * load are loaded - after - jQuery UI.
		 *
		 * @since 1.0-beta-5.5
		 *
		 * @param array $deps An array of script handles.
		 */
		$deps = apply_filters( 'gpnf_jquery_ui_dependencies', $deps );

		foreach ( $deps as $dep ) {
			if ( wp_script_is( $dep ) ) {
				$this->add_script_dependency( 'jquery-ui-core', $dep );
			}
		}

	}

	/**
	 * Add a dependency to an existing script.
	 *
	 * @deprecated 1.0-beta-8
	 *
	 * @see https://wordpress.stackexchange.com/questions/100709/add-a-script-as-a-dependency-to-a-registered-script
	 *
	 * @param $handle
	 * @param $dep
	 *
	 * @return bool
	 */
	public function add_script_dependency( $handle, $dep ) {
		global $wp_scripts;

		$script = $wp_scripts->query( $handle, 'registered' );
		if ( ! $script ) {
			return false;
		}

		if ( ! in_array( $dep, $script->deps ) ) {
			$script->deps[] = $dep;
		}

		return true;
	}

	public function setup_cron() {

		if ( ! wp_next_scheduled( 'gpnf_daily_cron' ) ) {
			wp_schedule_event( time(), 'daily', 'gpnf_daily_cron' );
		}

		add_action( 'gpnf_daily_cron', array( $this, 'daily_cron' ) );

	}

	public function daily_cron() {

		$expired = $this->get_expired_entries();

		$this->log( 'Running daily cron.' );
		$this->log( sprintf( 'Expired entry IDs: %s', implode( ', ', $expired ) ) );

		foreach ( $expired as $entry_id ) {

			// Move expired entries to the trash. Gravity Forms will handle deleting them from there.
			GFFormsModel::update_lead_property( $entry_id, 'status', 'trash' );

			// Remove expiration meta so this entry will never "expire" again.
			$entry = new GPNF_Entry( $entry_id );
			$entry->delete_expiration();

		}

	}

	public function get_expired_entries() {
		global $wpdb;

		// Orphaned entries have an expiration timestamp. If it is before the current time, it is expired.
		$expiration = time();
		$this->log( sprintf( 'Expiration Timestamp: %d', $expiration ) );

		$sql       = $wpdb->prepare( "SELECT entry_id FROM {$wpdb->prefix}gf_entry_meta WHERE meta_key = %s and meta_value < %d", GPNF_Entry::ENTRY_EXP_KEY, $expiration );
		$entry_ids = wp_list_pluck( $wpdb->get_results( $sql ), 'entry_id' );

		return $entry_ids;
	}

	public function handle_core_preview_ajax() {
		if ( rgget( 'gf_page' ) == 'preview' && $this->is_nested_form_submission() && class_exists( 'GFFormDisplay' ) && ! empty( GFFormDisplay::$submission ) ) {
			echo GFForms::get_form( rgpost( 'gform_submit' ), true, true, true, null, true );
			exit;
		}
	}

	public function add_parent_form_filter( $field_filters, $form ) {

		$field_filters[] = array(
			'key'             => GPNF_Entry::ENTRY_PARENT_KEY,
			'text'            => __( 'Parent Entry ID', 'gp-nested-forms' ),
			'preventMultiple' => false,
			'operators'       => array(
				'is',
				'isnot',
			),
		);

		return $field_filters;

	}

	public function filter_entry_list( $args ) {

		$parent_entry_id      = rgget( GPNF_Entry::ENTRY_PARENT_KEY );
		$nested_form_field_id = rgget( GPNF_Entry::ENTRY_NESTED_FORM_FIELD_KEY );

		if ( ! $parent_entry_id || ! $nested_form_field_id ) {
			return $args;
		}

		// set field filters if not already set
		if ( ! isset( $args['search_criteria']['field_filters'] ) ) {
			$args['search_criteria']['field_filters'] = array();
		}

		$args['search_criteria']['field_filters'][] = array(
			'key'   => GPNF_Entry::ENTRY_PARENT_KEY,
			'value' => $parent_entry_id,
		);

		$args['search_criteria']['field_filters'][] = array(
			'key'   => GPNF_Entry::ENTRY_NESTED_FORM_FIELD_KEY,
			'value' => $nested_form_field_id,
		);

		return $args;
	}

	public function output_session_scripts() {
		foreach ( $this->_session_queue as $form_id ) {
			echo GPNF_Session::get_session_script( $form_id );
		}
	}

	public function child_entry_trash_manage( $entry_id, $new_status, $old_status ) {

		$entry = new GPNF_Entry( $entry_id );

		if ( ! $entry->has_children() ) {
			return;
		}

		// Entry is trashed, send children to trash.
		if ( $new_status == 'trash' ) {
			$entry->trash_children();
		}

		// Entry is untrashed, set children to active.
		if ( $old_status == 'trash' && $new_status == 'active' ) {
			$entry->untrash_children();
		}

	}

	public function child_entry_delete( $entry_id ) {

		$entry = new GPNF_Entry( $entry_id );

		if ( $entry->has_children() ) {
			$entry->delete_children();
		}

	}



	// # ADMIN

	public function editor_field_standard_settings( $form_id ) {

		$forms = GFFormsModel::get_forms();

		?>

		<li class="gpnf-setting field_setting gp-field-setting">

			<div class="gp-row">

				<label for="gpnf-form" class="section_label">
					<?php _e( 'Nested Form', 'gp-nested-forms' ); ?>
					<?php gform_tooltip( 'gpnf_form' ); ?>
				</label>

				<select id="gpnf-form" onchange="SetFieldProperty( 'gpnfForm', this.value ); window.gpGlobals.GPNFAdmin.toggleNestedFormFields();" class="fieldwidth-3">
					<option value=""><?php _e( 'Select a Form', 'gp-nested-forms' ); ?></option>
					<?php
					foreach ( $forms as $form ) :
						if ( $form->id == $form_id ) {
							continue;
						}
						?>
						<option value="<?php echo $form->id; ?>"><?php echo $form->title; ?></option>
					<?php endforeach; ?>
				</select>

			</div>

			<div id="gpnf-form-settings" class="gp-row">

				<label for="gpnf-fields" class="section_label">
					<?php _e( 'Summary Fields', 'gp-nested-forms' ); ?>
					<?php gform_tooltip( 'gpnf_fields' ); ?>
				</label>
				<div id="gpnf-fields-container" class="gp-group">
					<select id="gpnf-fields" class="fieldwidth-3" multiple disabled onchange="SetFieldProperty( 'gpnfFields', jQuery( this ).val() );">
						<!-- dynamically populated based on selection in 'form' select -->
					</select>
					<img class="gpnf-static-spinner" src="<?php echo GFCommon::get_base_url(); ?>/images/<?php echo $this->is_gf_version_gte( '2.5-beta-1' ) ? 'spinner.svg' : 'spinner.gif';?>">
				</div>

			</div>

			<!-- Entry Labels -->
			<div id="gpnf-entry-labels" class="gp-row">
				<label for="gpnf-entry-label-singular" class="section_label">
					<?php esc_html_e( 'Entry Labels', 'gp-nested-forms' ); ?>
					<?php gform_tooltip( 'gpnf_entry_labels' ); ?>
				</label>
				<div class="gp-group">
					<label for="gpnf-entry-label-singular">
						<?php _e( 'Singular', 'gp-nested-forms' ); ?>
					</label>
					<input type="text" id="gpnf-entry-label-singular" placeholder="<?php esc_html_e( 'e.g. Entry', 'gp-nested-forms' ); ?>" onchange="SetFieldProperty( 'gpnfEntryLabelSingular', jQuery( this ).val() );" />
				</div>
				<div class="gp-group">
					<label for="gpnf-entry-label-plural">
						<?php _e( 'Plural', 'gp-nested-forms' ); ?>
					</label>
					<input type="text" id="gpnf-entry-label-plural" placeholder="<?php esc_html_e( 'e.g. Entries', 'gp-nested-forms' ); ?>" onchange="SetFieldProperty( 'gpnfEntryLabelPlural', jQuery( this ).val() );" />
				</div>
			</div>

		</li>

		<?php
	}

	public function editor_field_appearance_settings() {
		?>

		<li class="gpnf-modal-header-color-setting field_setting" style="display:none;">

			<label for="gpnf-modal-header-color" class="section_label">
				<?php _e( 'Modal Color', 'gp-nested-forms' ); ?>
				<?php gform_tooltip( 'gpnf_modal_header_color' ); ?>
			</label>

			<div class="gp-group">
				<input type="text" class="iColorPicker" onchange="SetFieldProperty( 'gpnfModalHeaderColor', this.value );" id="gpnf-modal-header-color" />
				<img id="chip_gpnf-modal-header-color" height="24" width="24" src="<?php echo GFCommon::get_base_url(); ?>/images/blankspace.png" />
				<img id="chooser_gpnf-modal-header-color" height="16" width="16" src="<?php echo GFCommon::get_base_url(); ?>/images/color.png" />
			</div>

		</li>

		<?php
	}

	public function editor_field_advanced_settings() {
		?>

		<li class="gpnf-entry-limits-setting field_setting gp-field-setting" id="gpnf-entry-limits" style="display:none;">

			<label for="gpnf-entry-limit-min" class="section_label">
				<?php esc_html_e( 'Entry Limits', 'gp-nested-forms' ); ?>
				<?php gform_tooltip( 'gpnf_entry_limits' ); ?>
			</label>

			<div class="gp-group">
				<label for="gpnf-entry-limit-min">
					<?php _e( 'Minimum', 'gp-nested-forms' ); ?>
				</label>
				<input type="number" id="gpnf-entry-limit-min" placeholder="<?php esc_html_e( 'e.g. 2', 'gp-nested-forms' ); ?>" onchange="SetFieldProperty( 'gpnfEntryLimitMin', jQuery( this ).val() );" />
			</div>

			<div class="gp-group">
				<label for="gpnf-entry-limit-max">
					<?php _e( 'Maximum', 'gp-nested-forms' ); ?>
				</label>
				<input type="number" id="gpnf-entry-limit-max" placeholder="<?php esc_html_e( 'e.g. 5', 'gp-nested-forms' ); ?>" onchange="SetFieldProperty( 'gpnfEntryLimitMax', jQuery( this ).val() );" />
			</div>

		</li>

		<?php if ( apply_filters( 'gpnf_enable_feed_processing_setting', false ) ) : ?>
			<li class="gpnf-feed-processing-setting field_setting" id="gpnf-feed-processing-setting" style="display:none;">

				<label for="gpnf-feed-processing" class="section_label">
					<?php esc_html_e( 'Feed Processing', 'gp-nested-forms' ); ?>
					<?php gform_tooltip( 'gpnf_feed_processing' ); ?>
				</label>

				<span><?php esc_html_e( 'Process nested feeds when the', 'gp-nested-forms' ); ?></span>
				<select id="gpnf-feed-processing" onchange="SetFieldProperty( 'gpnfFeedProcessing', jQuery( this ).val() );">
					<option value="parent"><?php esc_html_e( 'parent form', 'gp-nested-forms' ); ?></option>
					<option value="child"><?php esc_html_e( 'nested form', 'gp-nested-forms' ); ?></option>
				</select>
				<span><?php esc_html_e( 'is submitted.', 'gp-nested-forms' ); ?></span>

			</li>
		<?php endif; ?>

		<?php
	}



	// # GENERAL FUNCTIONALITY

	/**
	 * Enqueue child scripts/styles at the same time as the parent.
	 *
	 * This resolves an issue with GF Tooltip where there inline styles were being output *before* the child form's
	 * inline styles had been enqueued. The child form styles were thus enqueued by not never output.
	 *
	 * @param $form
	 */
	public function enqueue_child_form_scripts( $form ) {

		if ( empty( $form['fields'] ) || ! is_array( $form['fields'] ) ) {
			return;
		}

		foreach ( $form['fields'] as $field ) {

			if ( $field->type !== 'form' ) {
				continue;
			}

			$nested_form_id = rgar( $field, 'gpnfForm' );
			$nested_form    = $this->get_nested_form( $nested_form_id );

			if ( rgar( $nested_form, 'fields' ) ) {
				GFFormDisplay::enqueue_form_scripts( $nested_form, true );
			}
		}

	}

	public function get_nested_forms_markup( $form ) {
		global $wp_filter;

		do_action( 'gpnf_pre_nested_forms_markup', $form );

		// I'm not a huge fan of this... but Gravity Forms is promoting a snippet that wraps all GF inline scripts in a
		// DOMContentLoaded event listener. This prevents child form markup from being properly initialized. As a stop-gap
		// solution, let's unbind (and later rebind) all functions on the 'gform_cdata_open' filter.
		$cdata_open_filters  = rgar( $wp_filter, 'gform_cdata_open' );
		$cdata_close_filters = rgar( $wp_filter, 'gform_cdata_close' );

		if ( $cdata_open_filters ) {
			unset( $wp_filter['gform_cdata_open'] );
			unset( $wp_filter['gform_cdata_close'] );
		}

		ob_start();

		foreach ( $form['fields'] as $field ) :

			if ( $field->type != 'form' ) {
				continue;
			}

			$nested_form_id = rgar( $field, 'gpnfForm' );
			$nested_form    = $this->get_nested_form( $nested_form_id );

			if ( ! $nested_form ) {
				$data = array(
					'nested_field_id' => $field->id,
					'nested_form_id'  => $nested_form_id,
				);
				$this->log( sprintf( $nested_form_id ? 'No nested form ID is configured for this field: %s' : 'Nested form does not exist: %s', print_r( $data, true ) ) );
				continue;
			}

			?>

			<div class="gpnf-nested-form gpnf-nested-form-<?php echo $form['id']; ?>-<?php echo $field['id']; ?>" style="display:none;">
				<?php
				if ( $this->use_jquery_ui_dialog() ) {
					$this->load_nested_form_hooks( $nested_form_id, $form['id'] );

					gravity_form( $nested_form_id, false, true, $this->is_preview(), $this->get_stashed_shortcode_field_values( $form['id'] ), true, 99999 );

					$this->unload_nested_form_hooks( $nested_form_id, $form );
				} else {
					/**
					 * Preload the form but do not echo it out. This is important for making sure all CSS/JS gets
					 * enqueued if enqueueing logic during the form.
					 *
					 * This addition was necessary for compatibility with GP Populate Anything's Live Merge Tags.
					 *
					 * @param boolean  $value Whether or not to pre-load (but not echo to document) the form.
					 * @param array    $form  The current form.
					 * @since 1.0-beta-9.4
					 */
					if ( gf_apply_filters( array( 'gpnf_preload_form', $form['id'] ), true, $form ) ) {
						add_filter( 'gform_init_scripts_footer', '__return_false', 123 );
						/* Ensure that the last param ($echo) is false so it does not get rendered out. */
						gravity_form( $nested_form_id, false, true, $this->is_preview(), $this->get_stashed_shortcode_field_values( $form['id'] ), true, 99999, false );
						remove_filter( 'gform_init_scripts_footer', '__return_false', 123 );
					}

					echo '<!-- Loaded dynamically via AJAX -->';
				}
				?>
			</div>

			<div class="gpnf-edit-form gpnf-edit-form-<?php echo $form['id']; ?>-<?php echo $field['id']; ?>" style="display:none;">
				<!-- Loaded dynamically via AJAX -->
			</div>

			<?php
		endforeach;

		if ( $cdata_open_filters ) {
			$wp_filter['gform_cdata_open']  = $cdata_open_filters;
			$wp_filter['gform_cdata_close'] = $cdata_close_filters;
		}

		do_action( 'gpnf_nested_forms_markup', $form );

		return ob_get_clean();
	}

	/**
	 * Output all queued nested forms markup.
	 */
	public static function output_nested_forms_markup() {
		foreach ( self::$nested_forms_markup as $markup ) {
			echo $markup;
		}
	}

	public function register_entry_meta( $meta ) {

		$meta[ GPNF_Entry::ENTRY_PARENT_KEY ] = array(
			'label'      => esc_html__( 'Parent Entry ID', 'gp-nested-forms' ),
			'is_numeric' => true,
		);

		$meta[ GPNF_Entry::ENTRY_PARENT_FORM_KEY ] = array(
			'label'      => esc_html__( 'Parent Entry Form ID', 'gp-nested-forms' ),
			'is_numeric' => true,
		);

		$meta[ GPNF_Entry::ENTRY_NESTED_FORM_FIELD_KEY ] = array(
			'label'      => esc_html__( 'Child Form Field ID', 'gp-nested-forms' ),
			'is_numeric' => true,
		);

		return $meta;
	}

	public function process_merge_tags( $formula, $field, $form, $entry ) {

		preg_match_all( '/{[^{]*?:([0-9]+):(sum|total|count)=?([0-9]*)}/', $formula, $matches, PREG_SET_ORDER );
		foreach ( $matches as $match ) {

			list( $search, $nested_form_field_id, $func, $target_field_id ) = $match;

			$nested_form_field = GFFormsModel::get_field( $form, $nested_form_field_id );
			if ( ! $nested_form_field ) {
				continue;
			}

			$nested_form = $this->get_nested_form( $nested_form_field->gpnfForm );
			$replace     = '';

			$_entry        = new GPNF_Entry( $entry );
			$child_entries = $_entry->get_child_entries( $nested_form_field_id );

			switch ( $func ) {
				case 'sum':
					$total = 0;
					foreach ( $child_entries as $child_entry ) {
						$total += (float) GFCommon::to_number( rgar( $child_entry, $target_field_id ), $entry['currency'] );
					}
					$replace = $total;
					break;
				case 'total':
					$total = 0;
					foreach ( $child_entries as $child_entry ) {
						$total += (float) GFCommon::get_order_total( $nested_form, $child_entry );
					}
					$replace = $total;
					break;
				case 'count':
					$replace = count( $child_entries );
					break;
			}

			$formula = str_replace( $search, $replace, $formula );

		}

		return $formula;
	}

	public function add_nested_form_field_total_merge_tag( $merge_tags ) {
		return $merge_tags;
	}

	public function stash_shortcode_field_values( $form_args ) {
		$this->shortcode_field_values[ $form_args['form_id'] ] = $form_args['field_values'];
		return $form_args;
	}

	public function get_stashed_shortcode_field_values( $form_id ) {
		return rgar( $this->shortcode_field_values, $form_id );
	}

	// # AJAX

	public function ajax_get_form_fields() {

		if ( ! wp_verify_nonce( rgpost( 'nonce' ), 'gpnf_get_form_fields' ) ) {
			die( __( 'Oops! You don\'t have permission to get fields for this form.', 'gp-nested-forms' ) );
		}

		$form_id = rgpost( 'form_id' );
		$form    = GFAPI::get_form( $form_id );

		wp_send_json( $form['fields'] );

	}

	public function ajax_delete_entry() {

		//usleep( 500000 ); // @todo Remove!

		if ( ! wp_verify_nonce( rgpost( 'nonce' ), 'gpnf_delete_entry' ) ) {
			wp_send_json_error( __( 'Oops! You don\'t have permission to delete this entry.', 'gp-nested-forms' ) );
		}

		$entry_id = $this->get_posted_entry_id();
		$entry    = GFAPI::get_entry( $entry_id );

		if ( ! GPNF_Entry::can_current_user_edit_entry( $entry ) ) {
			wp_send_json_error( __( 'Oops! You don\'t have permission to delete this entry.', 'gp-nested-forms' ) );
		}

		$result = GFAPI::delete_entry( $entry_id );

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( $result->get_error_message() );
		} else {
			wp_send_json_success();
		}

	}

	/**
	 * Fetch the form with the entry pre-populated, ready for editing.
	 */
	public function ajax_edit_entry() {

		if ( ! wp_verify_nonce( rgpost( 'nonce' ), 'gpnf_edit_entry' ) ) {
			die( __( 'Oops! You don\'t have permission to edit this entry.', 'gp-nested-forms' ) );
		}

		$entry_id = $this->get_posted_entry_id();
		$entry    = GFAPI::get_entry( $entry_id );
		$form_id  = $entry['form_id'];

		if ( ! $entry ) {
			die( __( 'Oops! We can\'t locate that entry.', 'gp-nested-forms' ) );
		}

		if ( ! GPNF_Entry::can_current_user_edit_entry( $entry ) ) {
			die( __( 'Oops! You don\'t have permission to edit this entry.', 'gp-nested-forms' ) );
		}

		/**
		 * Needed for rehydrating Signature Field
		 */
		$GLOBALS['gpnf_current_edit_entry'] = $entry;

		ob_start();

		add_filter( 'gform_pre_render_' . $form_id, array( $this, 'prepare_form_for_population' ) );
		add_filter( 'gform_form_tag', array( $this, 'set_edit_form_action' ) );
		add_filter( 'gwlc_is_edit_view', '__return_true' );
		add_filter( 'gwlc_selected_values', array( $this, 'set_gwlc_selected_values' ), 20, 2 );

		add_filter( 'gform_get_form_filter_' . $form_id, array( $this, 'replace_post_render_trigger' ), 10, 2 );
		add_filter( 'gform_footer_init_scripts_filter_' . $form_id, array( $this, 'replace_post_render_trigger' ), 10, 2 );

		$this->get_parent_form_id();
		add_filter( 'gform_form_tag', array( $this, 'add_nested_inputs' ), 10, 2 );
		add_filter( 'gform_field_value', array( $this, 'populate_field_from_session_cookie' ), 10, 3 );

		gravity_form( $form_id, false, false, false, $this->prepare_entry_for_population( $entry ), true, 9999 );

		/**
		 * footer_init_scripts does not run by default if explicitly loading the form with AJAX enabled in GF >2.5.
		 */
		if ( $this->is_gf_version_gte( '2.5-beta-1' ) ) {
			GFFormDisplay::footer_init_scripts( $form_id );
		}

		$markup = trim( ob_get_clean() );
		wp_send_json( $markup );

	}

	public function ajax_refresh_markup() {

		if ( ! wp_verify_nonce( rgpost( 'nonce' ), 'gpnf_refresh_markup' ) ) {
			die( __( 'Oops! You don\'t have permission to do this.', 'gp-nested-forms' ) );
		}

		$form_id = $this->get_parent_form_id();
		$form    = GFAPI::get_form( $form_id );

		$nested_form_field_id = $this->get_posted_nested_form_field_id();
		$nested_form_field    = GFFormsModel::get_field( $form, $nested_form_field_id );
		$nested_form_id       = rgar( $nested_form_field, 'gpnfForm' );

		ob_start();

		$this->load_nested_form_hooks( $nested_form_id, $form_id );
		add_filter( 'gform_form_tag', array( $this, 'set_edit_form_action' ) );
		add_filter( 'gform_field_value', array( $this, 'populate_field_from_session_cookie' ), 10, 3 );

		// Get the stashed field values from the session.
		$session      = new GPNF_Session( rgar( $_REQUEST, 'gpnf_parent_form_id' ) );
		$field_values = rgars( $session->get_cookie(), 'field_values' );

		// Clear the post so Gravity Forms will use isSelected property on choice-based fields and not try to determine
		// isSelected based on posted values. I'm betting this will resolve many other unknown issues as well.
		$_POST = array();

		gravity_form( $nested_form_id, false, true, true, $field_values, true, 99999 );

		/**
		 * footer_init_scripts does not run by default if explicitly loading the form with AJAX enabled in GF >2.5.
		 */
		if ( $this->is_gf_version_gte( '2.5-beta-1' ) || apply_filters( 'gform_init_scripts_footer', false ) ) {
			GFFormDisplay::footer_init_scripts( $nested_form_id );
		}

		$this->unload_nested_form_hooks( '', $nested_form_id );

		$markup = trim( ob_get_clean() );
		wp_send_json( $markup );

	}

	public function ajax_duplicate_entry() {

		if ( ! wp_verify_nonce( rgpost( 'nonce' ), 'gpnf_duplicate_entry' ) ) {
			wp_send_json_error( __( 'Oops! You don\'t have permission to duplicate this entry.', 'gp-nested-forms' ) );
		}

		$entry_id = $this->get_posted_entry_id();
		$entry    = GFAPI::get_entry( $entry_id );

		if ( ! GPNF_Entry::can_current_user_edit_entry( $entry ) ) {
			wp_send_json_error( __( 'Oops! You don\'t have permission to duplicate this entry.', 'gp-nested-forms' ) );
		}

		// Prepare the entry for duplication.
		unset( $entry['id'] );

		$dup_entry_id = GFAPI::add_entry( $entry );

		if ( is_wp_error( $dup_entry_id ) ) {
			wp_send_json_error( $dup_entry_id->get_error_message() );
		}

		$parent_form       = GFAPI::get_form( $this->get_posted_parent_form_id() );
		$nested_form_field = $this->get_posted_nested_form_field( $parent_form );
		$child_form        = GFAPI::get_form( $nested_form_field->gpnfForm );

		// Note: Entry meta included in the passed entry will also be duplicated.
		$dup_entry    = GFAPI::get_entry( $dup_entry_id );
		$field_values = gp_nested_forms()->get_entry_display_values( $dup_entry, $child_form );

		// Attach session meta to child entry.
		$session = new GPNF_Session( $parent_form['id'] );
		$session->add_child_entry( $dup_entry_id );

		// set args passed back to entry list on front-end
		$args = array(
			'formId'      => $parent_form['id'],
			'fieldId'     => $nested_form_field->id,
			'entryId'     => $dup_entry_id,
			'entry'       => $dup_entry,
			'fieldValues' => $field_values,
			'mode'        => 'add',
		);

		wp_send_json_success( $args );

	}

	public function ajax_session() {

		$form_id = rgpost( 'form_id' );

		$session = new GPNF_Session( $form_id );
		$session
			->set_session_data()
			->set_cookie();

		die();

	}



	// # VALUES

	public function get_child_entry_ids_from_value( $value ) {
		$child_entry_ids = explode( ',', $value );
		foreach ( $child_entry_ids as &$child_entry_id ) {
			// We typecast the value as an integer for consistency and to as a security measure. See PR #77.
			$child_entry_id = (int) trim( $child_entry_id );
		}
		$child_entry_ids = array_filter( $child_entry_ids );
		return $child_entry_ids;
	}

	public function get_entries( $entry_ids ) {

		$entries = array();

		if ( empty( $entry_ids ) ) {
			return $entries;
		}

		if ( is_string( $entry_ids ) ) {
			$entry_ids = $this->get_child_entry_ids_from_value( $entry_ids );
		} elseif ( ! is_array( $entry_ids ) ) {
			$entry_ids = array( $entry_ids );
		}

		foreach ( $entry_ids as $entry_id ) {
			$entry = GFAPI::get_entry( (int) $entry_id );
			if ( ! is_wp_error( $entry ) ) {
				$entries[] = GFAPI::get_entry( $entry_id );
			}
		}

		return $entries;
	}

	public function get_entry_url( $entry_id, $form_id ) {
		/**
		 * Filter the URL for entry detail view per entry.
		 *
		 * @since 1.0-beta-4.16
		 *
		 * @param string $entry_url The URL to a specific entry's detail view.
		 * @param int    $entry_id  The current entry ID.
		 * @param int    $form_id   The current form ID.
		 */
		return gf_apply_filters( array( 'gpnf_entry_url', $form_id ), admin_url( "admin.php?page=gf_entries&view=entry&id={$form_id}&lid={$entry_id}" ), $entry_id, $form_id );
	}

	public function all_fields_value( $value, $merge_tag, $modifiers, $field, $raw_value, $format = 'html' ) {

		// Only process for Nested Form fields - and - if All Fields template has not filtered this field out (i.e. false).
		if ( $field->type != 'form' || $value === false ) {
			return $value;
		}

		$nested_form_id = rgar( $field, 'gpnfForm' );
		$nested_form    = $this->get_nested_form( $nested_form_id );

		if ( ! $nested_form ) {
			$data = array(
				'nested_field_id' => $field->id,
				'nested_form_id'  => $nested_form_id,
			);
			$this->log( sprintf( $nested_form_id ? 'No nested form ID is configured for this field: %s' : 'Nested form does not exist: %s', print_r( $data, true ) ) );
			return $value;
		}

		$is_all_fields = $merge_tag === 'all_fields';
		$modifiers     = $is_all_fields ? "context[nested],parent[{$field->id}]," . $modifiers : $modifiers;

		// When filtering down to a single field from the child form (via All Fields Template), show simplified template.
		if ( $this->is_filtered_single( $modifiers, $field, $is_all_fields ) ) {
			$index = $this->parse_modifier( 'index', $modifiers );
			if ( $index !== false ) {
				return $this->get_single_value( $index, $field, $value, $modifiers, $format );
			}
			return $this->get_filtered_single_template( $field, $raw_value, $modifiers, $format );
		}

		// Provide opportunity for users to override the all entries template; no core template provided.
		return $this->get_all_entries_template( $field, $raw_value, $modifiers, $merge_tag, $format );
	}

	public function parse_modifiers( $modifiers ) {

		if ( ! is_callable( 'gw_all_fields_template' ) ) {
			return array();
		}

		return gw_all_fields_template()->parse_modifiers( $modifiers );
	}

	public function parse_modifier( $modifier, $modifiers ) {
		$modifiers = $this->parse_modifiers( $modifiers );
		// rgar() returns false when modifier is 0
		return isset( $modifiers[ $modifier ] ) ? $modifiers[ $modifier ] : false;
	}

	/**
	 * This template is used to render all entries for Nested Form field merge tags - and - the {all_fields} merge tag.
	 *
	 * @param $field
	 * @param $value
	 *
	 * @return string
	 */
	public function get_all_entries_template( $field, $value, $modifiers, $merge_tag, $format = 'html' ) {

		$template         = new GP_Template( gp_nested_forms() );
		$template_name    = 'nested-entries-all';
		$nested_field_ids = rgar( $field, 'gpnfFields' );
		$nested_form      = $this->get_nested_form( rgar( $field, 'gpnfForm' ) );

		$args = array(
			'template'             => $template_name,
			'field'                => $field,
			'nested_form'          => $nested_form,
			'nested_fields'        => gp_nested_forms()->get_fields_by_ids( $nested_field_ids, $nested_form ),
			'nested_field_ids'     => $nested_field_ids,
			'value'                => $value,
			'entries'              => gp_nested_forms()->get_entries( $value ),
			'column_count'         => null,
			'related_entries_link' => null,
			'actions'              => array(),
			'labels'               => array(),
			'modifiers'            => $modifiers,
			'is_all_fields'        => $merge_tag == 'all_fields',
			'format'               => $format,
		);

		/**
		 * See GP_Field_Nested_Form::get_value_entry_detail().
		 */
		$args = gf_apply_filters( array( 'gpnf_template_args', $field->formId, $field->id ), $args, $this );

		if ( ! $args['entries'] ) {
			return null;
		}

		$markup = $template->parse_template(
			array(
				sprintf( '%s-%s-%s.php', $args['template'], $field->formId, $field->id ),
				sprintf( '%s-%s.php', $args['template'], $field->formId ),
				sprintf( '%s.php', $args['template'] ),
			),
			true,
			false,
			$args
		);

		return $markup;
	}

	public function is_filtered_single( $modifiers, $nested_form_field, $is_all_fields ) {

		$filter = $this->parse_modifier( 'filter', $modifiers );
		if ( ! $filter ) {
			return false;
		}

		if ( $is_all_fields ) {

			if ( ! is_array( $filter ) ) {
				$filter = array( $filter );
			}

			$field_ids = array();
			foreach ( $filter as $field_id ) {
				// Convert "1.1" to "1" and make sure we're only doing field-specific NF (e.g. "1.1" vs "1").
				if ( intval( $field_id ) == $nested_form_field->id && $field_id !== intval( $field_id ) ) {
					$field_id_bits = explode( '.', $field_id );
					$field_ids[]   = array_pop( $field_id_bits );
					if ( count( $field_ids ) > 1 ) {
						return false;
					}
				}
			}
		}
		// If it's not the {all_fields} merge tag and the filter is an array, we know it's more than one field.
		elseif ( is_array( $filter ) ) {
			return false;
		}

		return true;
	}

	public function get_filtered_single_template( $field, $value, $modifiers, $format = 'html' ) {

		$template    = new GP_Template( gp_nested_forms() );
		$nested_form = $this->get_nested_form( rgar( $field, 'gpnfForm' ) );
		$entry_ids   = $this->get_child_entry_ids_from_value( $value );

		$args = array(
			'template'    => 'nested-entries-simple-list',
			'field'       => $field,
			'nested_form' => $nested_form,
			'modifiers'   => $modifiers,
			'items'       => $this->get_simple_list_items( $entry_ids, $nested_form, $modifiers, $format ),
			'format'      => $format,
		);

		$markup = $template->parse_template(
			array(
				sprintf( '%s-%s-%s.php', $args['template'], $field->formId, $field->id ),
				sprintf( '%s-%s.php', $args['template'], $field->formId ),
				sprintf( '%s.php', $args['template'] ),
			),
			true,
			false,
			$args
		);

		return $markup;
	}

	public function get_single_value( $index, $field, $value, $modifiers, $format = 'html' ) {

		$nested_form = $this->get_nested_form( rgar( $field, 'gpnfForm' ) );
		$entry_ids   = $this->get_child_entry_ids_from_value( $value );
		$items       = $this->get_simple_list_items( $entry_ids, $nested_form, $modifiers, $format );

		if ( $index < 0 ) {
			$count  = count( $items );
			$index += $count;
			$index  = max( $index, 0 );
		}

		return rgars( $items, "{$index}/value" );
	}

	public function get_simple_list_items( $entry_ids, $nested_form, $modifiers, $format = 'html' ) {

		if ( ! is_callable( 'gw_all_fields_template' ) ) {
			return array();
		}

		$items    = array();
		$use_text = ! in_array( 'value', explode( ',', $modifiers ), true );

		foreach ( $entry_ids as $entry_id ) {

			$entry = GFAPI::get_entry( $entry_id );
			if ( is_wp_error( $entry ) ) {
				continue;
			}

			$items = array_merge( $items, gw_all_fields_template()->get_items( $nested_form, $entry, false, $use_text, $format, false, '', $modifiers ) );

		}

		return $items;
	}

	public function get_all_entries_markup( $field, $value, $modifiers, $is_all_fields, $format = 'html' ) {

		$template    = new GP_Template( gp_nested_forms() );
		$entry_ids   = $this->get_child_entry_ids_from_value( $value );
		$nested_form = $this->get_nested_form( rgar( $field, 'gpnfForm' ) );

		$values = array();
		$args   = gf_apply_filters(
			array( 'gpnf_template_args', $field->formId, $field->id ),
			array(
				'template'        => 'nested-entry',
				'field'           => $field,
				'nested_form'     => $nested_form,
				'modifiers'       => $modifiers,
				'is_all_fields'   => $is_all_fields,
				'use_text'        => true,
				'use_admin_label' => false,
				'display_empty'   => false,
				'format'          => $format,
			),
			$this
		);

		foreach ( $entry_ids as $entry_id ) {

			// Gravity Forms cache is too aggressive to in the GFFormsModel::is_field_hidden() method. In order to render
			// the {all_fields} template for each entry, we must clear the cache before each.
			// @see https://secure.helpscout.net/conversation/918488296/13120/
			GFCache::flush();

			$entry = GFAPI::get_entry( $entry_id );
			if ( is_wp_error( $entry ) ) {
				continue;
			}

			$args['entry'] = $entry;
			// Pass entry for integration with GP Preview Submission.
			$args['modifiers'] = $modifiers . ",entry[{$entry_id}]";

			$values[] = $template->parse_template(
				array(
					sprintf( '%s-%s-%s.php', $args['template'], $field->formId, $field->id ),
					sprintf( '%s-%s.php', $args['template'], $field->formId ),
					sprintf( '%s.php', $args['template'] ),
				),
				true,
				false,
				$args
			);

		}

		$hr = $format == 'html' ? '<hr class="gpnf-nested-entries-hr" style="height:12px;visibility:hidden;margin:0;border:0;">' : "---\n\n";

		if ( $is_all_fields ) {
			foreach ( $values as &$_value ) {
				$_value = preg_replace( '/bgcolor/', 'style="border-top:5px solid #faebd2;" bgcolor', $_value, 1 );
				$_value = str_replace( 'EAF2FA', 'FAF4EA', $_value );
			}
			$markup = sprintf( '%s%s%s', $format == 'html' ? $hr : "\n\n{$hr}", implode( $hr, $values ), $hr );
		} else {
			foreach ( $values as &$_value ) {
				$_value = preg_replace( '/bgcolor/', 'style="border-top:5px solid #d2e6fa;" bgcolor', $_value, 1 );
			}
			$markup = implode( $hr, $values );
		}

		return $markup;
	}

	public function handle_nested_confirmation( $confirmation, $submitted_form, $entry ) {

		if ( ! $this->is_nested_form_submission() ) {
			return $confirmation;
		}

		$parent_form       = GFAPI::get_form( $this->get_parent_form_id() );
		$nested_form_field = $this->get_posted_nested_form_field( $parent_form );
		//$display_fields    = $nested_form_field->gpnfFields;
		$field_values = $this->get_entry_display_values( $entry, $submitted_form );
		$mode         = rgpost( 'gpnf_mode' ) ? rgpost( 'gpnf_mode' ) : 'add';

		// Attach session meta to child entry.
		$entry   = new GPNF_Entry( $entry );
		$session = new GPNF_Session( $parent_form['id'] );
		$session->add_child_entry( $entry->id );

		// set args passed back to entry list on front-end
		$args = array(
			'formId'      => $parent_form['id'],
			'fieldId'     => $nested_form_field['id'],
			'entryId'     => $entry->id,
			'entry'       => $entry,
			'fieldValues' => $field_values,
			'mode'        => $mode,
		);

		return '<script type="text/javascript"> if( typeof GPNestedForms != "undefined" ) { GPNestedForms.loadEntry( ' . json_encode( $args ) . ' ); } </script>';

	}

	public function handle_nested_confirmation_anchor( $anchor ) {
		return $this->is_nested_form_submission() ? false : $anchor;
	}

	public function handle_parent_submission( $parent_entry, $form ) {

		if ( ! $this->has_nested_form_field( $form ) ) {
			return;
		}

		// Clear the session when the parent form is submitted.
		$session = new GPNF_Session( $form['id'] );
		$session->delete_cookie();

		$parent_entry = new GPNF_Entry( $parent_entry );
		if ( ! $parent_entry->has_children() ) {
			return;
		}

		$child_entries = $parent_entry->get_child_entries();
		if ( ! $child_entries ) {
			return;
		}

		foreach ( $child_entries as $child_entry ) {

			$child_form = gf_apply_filters( array( 'gform_pre_process', $child_entry['form_id'] ), GFAPI::get_form( $child_entry['form_id'] ) );

			// Create posts for child entries; the func handles determining if the entry has post fields.
			GFCommon::create_post( $child_form, $child_entry );

			$child_entry = new GPNF_Entry( $child_entry );
			$child_entry->set_parent_form( $form['id'], $parent_entry->id );
			$child_entry->delete_expiration();

		}

	}

	public function handle_parent_update_entry( $form, $entry_id ) {

		$this->handle_parent_submission( $entry_id, $form );

	}

	public function handle_parent_submission_post_save( $entry, $form ) {

		if ( ! $this->has_nested_form_field( $form ) ) {
			return $entry;
		}

		$parent_entry = new GPNF_Entry( $entry );
		if ( ! $parent_entry->has_children() ) {
			return $entry;
		}

		$child_entries = $parent_entry->get_child_entries();

		foreach ( $child_entries as $child_entry ) {
			$child_entry = new GPNF_Entry( $child_entry );
			// Always match the created_by property of the child entry with that of the parent. Resolves issue with User
			// Registration add-on where, in some cases, the newly registered user is set as the entry creator.
			$child_entry->set_created_by( $parent_entry->id );
		}

		return $entry;
	}

	public function get_posted_nested_form_field( $form ) {
		foreach ( $form['fields'] as $field ) {
			if ( $field->id == $this->get_posted_nested_form_field_id() ) {
				return $field;
			}
		}
		return false;
	}

	public function get_entry_display_values( $entry, $form, $display_fields = array() ) {

		if ( ! is_array( $entry ) ) {
			$entry = GFAPI::get_entry( $entry );
		}

		if ( is_wp_error( $entry ) ) {
			return false;
		}

		$field_values = array();
		if ( empty( $display_fields ) ) {
			$display_fields = wp_list_pluck( $form['fields'], 'id' );
		}

		foreach ( $display_fields as $display_field_id ) {

			$field = GFFormsModel::get_field( $form, $display_field_id );

			// This can happen if the field is deleted from the child form but is still set as a Display Field on the Nested Form field.
			if ( ! $field ) {
				continue;
			}

			$raw_value = GFFormsModel::get_lead_field_value( $entry, $field );
			$value     = GFCommon::get_lead_field_display( $field, $raw_value, $entry['currency'], true );
			// Run $value through same filter GF uses before displaying on the entry detail view.
			$value = apply_filters( 'gform_entry_field_value', $value, $field, $entry, $form );

			if ( is_array( $value ) ) {
				ksort( $value );
				$value = implode( ' ', $value );
			}

			$value = array(
				'label' => $value,
				'value' => $raw_value,
			);

			/**
			 * Filter the value to be displayed in the Nested Form entries view (per field).
			 *
			 * @since 1.0
			 *
			 * @param mixed    $value The field value to be displayed.
			 * @param GF_Field $field The current field object.
			 * @param array    $form  The current form.
			 * @param array    $entry The current entry.
			 */
			$value = gf_apply_filters( array( 'gpnf_display_value', $form['id'], $field->id ), $value, $field, $form, $entry );
			$value = gf_apply_filters( array( "gpnf_{$field->get_input_type()}_display_value", $form['id'] ), $value, $field, $form, $entry );

			$field_values[ $display_field_id ] = $value;

		}

		$field_values['id'] = $entry['id'];

		$entry = new GPNF_Entry( $entry );
		$entry->set_total();
		$field_values['total'] = $entry->_total;

		return $field_values;
	}



	// # FORM RENDERING

	public function handle_nested_forms_markup( $form_html, $form ) {

		if ( ! $this->has_nested_form_field( $form, true ) ) {
			return $form_html;
		}

		$is_ajax_submission = rgpost( 'gform_submit' ) && rgpost( 'gform_ajax' );

		if ( $is_ajax_submission ) {
			//	$nested_entries = $this->get_submitted_nested_entries( $form );
			//	$form_html      = sprintf( '<script type="text/javascript"> parent.gpnfNestedEntries[%d] = %s; </script>', $form['id'], json_encode( $nested_entries ) ) . $form_html;
			return $form_html;
		}

		$nested_forms_markup = $this->get_nested_forms_markup( $form );

		/**
		 * This hook is deprecated.
		 */
		if ( apply_filters( 'gpnf_append_nested_forms_to_footer', apply_filters( 'gform_init_scripts_footer', true ), $form ) ) {
			self::$nested_forms_markup[ $form['id'] ] = $nested_forms_markup;
			if ( ! has_action( 'wp_footer', array( $this, 'output_nested_forms_markup' ) ) ) {
				add_action( 'wp_footer', array( $this, 'output_nested_forms_markup' ), 21 );
				add_action( 'gform_preview_footer', array( $this, 'output_nested_forms_markup' ), 21 );
				add_action( 'admin_footer', array( $this, 'output_nested_forms_markup' ), 21 );
			}
		} else {
			$form_html .= $nested_forms_markup;
		}

		return $form_html;
	}

	public function maybe_load_nested_form_hooks( $form ) {

		if ( ! $this->is_nested_form_submission() || did_action( 'gform_pre_validation' ) ) {
			return $form;
		}

		$this->load_nested_form_hooks( $form['id'], $this->get_parent_form_id() );

		return $form;
	}

	public function load_nested_form_hooks( $form_id, $parent_form_id ) {

		$this->parent_form_id = $parent_form_id;

		add_filter( 'gform_form_tag', array( $this, 'add_nested_inputs' ), 10, 2 );
		add_filter( 'gform_pre_render', array( $this, 'remove_extra_other_choices' ) );

		if ( $this->use_jquery_ui_dialog() ) {
			// Force scripts to load in the footer so that they are not reincluded in the fetched form markup.
			add_filter( 'gform_init_scripts_footer', '__return_true', 11 );
		}

		add_filter( 'gform_get_form_filter_' . $form_id, array( $this, 'replace_post_render_trigger' ), 10, 2 );
		add_filter( 'gform_footer_init_scripts_filter_' . $form_id, array( $this, 'replace_post_render_trigger' ), 10, 2 );

		// Prevent posts from being generated.
		add_filter( 'gform_disable_post_creation_' . $form_id, '__return_true', 11 );

		add_filter( 'gform_validation', array( $this, 'override_no_duplicates_validation' ) );

		// Setup unload to remove hooks after form has been generated.
		add_filter( 'gform_get_form_filter_' . $form_id, array( $this, 'unload_nested_form_hooks' ), 99, 2 );

		do_action( 'gpnf_load_nested_form_hooks', $form_id, $parent_form_id );

	}

	/**
	 * When editing a child entry via a Nested Form, override the no duplicates validation if the value of the child entry
	 * has not changed.
	 *
	 * @param $result
	 *
	 * @return mixed
	 */
	public function override_no_duplicates_validation( $result ) {

		if ( $result['is_valid'] ) {
			return $result;
		}

		$edit_entry_id = $this->get_posted_entry_id();
		if ( ! $edit_entry_id ) {
			return $result;
		}

		/** @var GF_Field $field */
		foreach ( $result['form']['fields'] as &$field ) {

			if ( ! $field->noDuplicates || ! $field->failed_validation ) {
				continue;
			}

			$submitted_value = $field->get_value_submission( array() );
			if ( ! GFFormsModel::is_duplicate( $result['form']['id'], $field, $submitted_value ) ) {
				continue;
			}

			$entry          = GFAPI::get_entry( $edit_entry_id );
			$existing_value = rgar( $entry, $field->id );

			if ( $submitted_value == $existing_value ) {
				$field->failed_validation = false;
			}
		}

		$result['is_valid'] = true;
		foreach ( $result['form']['fields'] as &$field ) {
			if ( $field->failed_validation ) {
				$result['is_valid'] = false;
			}
		}

		return $result;
	}

	public function unload_nested_form_hooks( $form_string, $form_or_id ) {

		if ( $this->use_jquery_ui_dialog() ) {
			remove_filter( 'gform_init_scripts_footer', '__return_true', 11 );
		}

		remove_filter( 'gform_form_tag', array( $this, 'add_nested_inputs' ) );
		remove_filter( 'gform_pre_render', array( $this, 'remove_extra_other_choices' ) );

		do_action( 'gpnf_unload_nested_form_hooks', rgar( $form_or_id, 'id', $form_or_id ), $this->parent_form_id );

		$this->parent_form_id = null;

		return $form_string;
	}

	public function replace_post_render_trigger( $form_html, $form ) {
		$form_html = preg_replace( '/trigger\([ ]*[\'"]gform_post_render[\'"]/', "trigger('gpnf_post_render'", $form_html );
		// Used by event handler functionality to target nested form post render events and prioritize them.
		$form_html = preg_replace( '/bind\([ ]*[\'"]gform_post_render[\'"]/', "bind('gform_post_render.gpnf'", $form_html );
		if ( ! $this->use_jquery_ui_dialog() ) {
			$form_html = preg_replace( '/<script.*gformInitSpinner.*?<\/script>/', '<!-- GPNF removes GF\'s default <iframe> script; replacing it with its own in gp-nested-form.js. -->', $form_html );
		}
		return $form_html;
	}

	public function handle_nested_form_field_value( $value, $entry, $field ) {

		if ( $this->should_use_static_value( $field, $entry ) ) {
			return $value;
		}

		$cache_key = "gpnf_field_value_{$field->formId}_{$field->id}_{$entry['id']}";

		$found        = null;
		$cached_value = GFCache::get( $cache_key, $found, false );
		if ( $cached_value !== false ) {
			return $cached_value;
		}

		// Turns out GFAPI::get_entries() is WAYYY faster than querying the DB directly...
		$child_entries = GFAPI::get_entries(
			$field->gpnfForm,
			array(
				/*
				 * When a parent entry is trashed, its child entries are also trashed. When a parent entry is restored,
				 * we should also restore its child entries. To support this, fetch child entries based on the status
				 * of the parent entry. We will likely need to improve this logic when we add support for more types of
				 * relationships between parent and child entries.
				 */
				'status'        => $entry['status'] === 'trash' ? 'trash' : 'active',
				'field_filters' => array(
					'mode' => 'all',
					array(
						'key'   => GPNF_Entry::ENTRY_PARENT_KEY,
						'value' => $entry['id'],
					),
					array(
						'key'   => GPNF_Entry::ENTRY_NESTED_FORM_FIELD_KEY,
						'value' => $field->id,
					),
				),
			),
			array(
				'key'        => 'id',
				'direction'  => 'ASC',
				'is_numeric' => true,
			),
			array(
				'offset'    => 0,
				'page_size' => $this->get_child_entry_max(),
			)
		);

		$value = implode( ',', wp_list_pluck( $child_entries, 'id' ) );

		GFCache::set( $cache_key, $value );

		return $value;
	}

	public function should_use_static_value( $field, $entry ) {

		// Honor the submitted value.
		// Note: with this change, the conditional below (for the WCGF Product plugin) may no longer be necessary...
		if ( $this->is_form_submission() ) {
			return true;
		}

		// Only process for Nested Form fields and when we're working with a "real" entry.
		// The latter check resolves a conflict with Preview Submission where an empty entry ID returned erroneous child entries.
		// The Code Canyon WCGF Product plugin uses randomly generated alphanumeric entry IDs for some reason. Let's ignore these entries.
		// 	@see https://secure.helpscout.net/conversation/956639062/13730?folderId=14965
		if ( ! is_a( $field, 'GP_Field_Nested_Form' ) || ! $entry['id'] || ! is_numeric( $entry['id'] ) ) {
			return true;
		}

		/**
		 * Filter whether the current Nested Form field's value should be fetched dynamically from the database or left as is.
		 *
		 * @param bool                  $should_use_static_value Should the field's value be static?
		 * @param \GP_Field_Nested_Form $field                   The current Nested Form field.
		 * @param array                 $entry                   The current entry.
		 *
		 * @since 1.0-beta-8.80
		 */
		$should_use_static_value = gf_apply_filters( array( 'gpnf_should_use_static_value', $field->formId, $field->id ), false, $field, $entry );

		return $should_use_static_value;
	}

	public static function get_child_entry_max() {
		/**
		 * Filter the maximum number of child entries accepted in a Nested Form field.
		 *
		 * This supersedes any maximum set in the Entry Limits setting of a Nested Form field.
		 *
		 * @since 1.0-beta-8.44
		 *
		 * @param int $child_entry_max The maximum number of child entries accepted in a Nested Form field.
		 */
		return apply_filters( 'gpnf_child_entry_max', 99 );
	}

	public function register_all_form_init_scripts( $form ) {

		$script = '';

		foreach ( $form['fields'] as $field ) {

			if ( $field->type != 'form' || $field->visibility == 'administrative' ) {
				continue;
			}

			/**
			 * @var GP_Field_Nested_Form $field
			 */
			$nested_form    = $this->get_nested_form( rgar( $field, 'gpnfForm' ) );
			$display_fields = rgar( $field, 'gpnfFields' );
			$entries        = $this->get_submitted_nested_entries( $form, $field->id );
			$primary_color  = $field->gpnfModalHeaderColor ? $field->gpnfModalHeaderColor : '#3498db';

			$args = array(
				'formId'              => $form['id'],
				'fieldId'             => $field['id'],
				'nestedFormId'        => rgar( $nested_form, 'id' ),
				'displayFields'       => $display_fields,
				'entries'             => $entries,
				'ajaxUrl'             => admin_url( 'admin-ajax.php', ! is_ssl() ? 'http' : 'admin' ),
				'modalLabels'         => array(
					'title'         => sprintf( __( 'Add %s', 'gp-nested-forms' ), $field->get_item_label() ),
					'editTitle'     => sprintf( __( 'Edit %s', 'gp-nested-forms' ), $field->get_item_label() ),
					'cancel'        => esc_html__( 'Cancel', 'gp-nested-forms' ),
					'delete'        => esc_html__( 'Delete', 'gp-nested-forms' ),
					'confirmAction' => esc_html__( 'Are you sure?', 'gp-nested-forms' ),
				),
				'modalColors'         => array(
					'primary'   => $primary_color,
					'secondary' => $this->color_luminance( $primary_color, -0.5 ),
					'danger'    => '#e74c3c',
				),
				'modalHeaderColor'    => $primary_color,
				'modalClass'          => $this->use_jquery_ui_dialog() ? 'gpnf-dialog' : 'gpnf-modal',
				'modalStickyFooter'   => true,
				'entryLimitMin'       => $field->gpnfEntryLimitMin,
				'entryLimitMax'       => $field->gpnfEntryLimitMax,
				'sessionData'         => GPNF_Session::get_default_session_data( $field->formId, $this->get_stashed_shortcode_field_values( $form['id'] ) ),
				'spinnerUrl'          => gf_apply_filters( array( 'gform_ajax_spinner_url', $field->formId ), GFCommon::get_base_url() . '/images/spinner' . ( $this->is_gf_version_gte( '2.5-beta-1' ) ? '.svg' : '.gif' ), $form ),
				/* @deprecated options below */
				'modalTitle'          => sprintf( __( 'Add %s', 'gp-nested-forms' ), $field->get_item_label() ),
				'editModalTitle'      => sprintf( __( 'Edit %s', 'gp-nested-forms' ), $field->get_item_label() ),
				'modalWidth'          => 700,
				'modalHeight'         => 'auto',
				'hasConditionalLogic' => GFFormDisplay::has_conditional_logic( $nested_form ),
				'isGF25'              => $this->is_gf_version_gte( '2.5-beta-1' ),
			);

			// Backwards compatibility for deprecated "modalTitle" option.
			if ( $args['modalLabels']['title'] == sprintf( __( 'Add %s', 'gp-nested-forms' ), $field->get_item_label() ) && $args['modalTitle'] !== $args['modalLabels']['title'] ) {
				$args['modalLabels']['title'] = $args['modalTitle'];
			}

			// Backwards compatibility for deprecated "editModalTitle" option.
			if ( $args['modalLabels']['editTitle'] == sprintf( __( 'Edit %s', 'gp-nested-forms' ), $field->get_item_label() ) && $args['editModalTitle'] !== $args['modalLabels']['editTitle'] ) {
				$args['modalLabels']['editTitle'] = $args['editModalTitle'];
			}

			/**
			 * Filter the arguments that will be used to initialized the nested forms frontend script.
			 *
			 * @since 1.0
			 *
			 * @param array $args {
			 *
			 *     @var int    $formId              The current form ID.
			 *     @var int    $fieldId             The field ID of the Nested Form field.
			 *     @var int    $nestedFormId        The form ID of the nested form.
			 *     @var string $modalTitle          The title to be displayed in the modal header.
			 *     @var string $editModalTitle      The title to be displayed in the modal header when editing an existing entry.
			 *     @var array  $displayFields       The fields which will be displayed in the Nested Forms entries view.
			 *     @var array  $entries             An array of modified entries, including only their display values.
			 *     @var string $ajaxUrl             The URL to which AJAX requests will be posted.
			 *     @var int    $modalWidth          The default width of the modal; defaults to 700.
			 *     @var mixed  $modalHeight         The default height of the modal; defaults to 'auto' which will automatically size the modal based on it's contents.
			 *     @var string $modalClass          The class that will be attached to the modal for styling.
			 *     @var string $modalHeaderColor    A HEX color that will be set as the default background color of the modal header.
			 *     @var bool   $hasConditionalLogic Indicate whether the current form has conditional logic enabled.
			 *
			 * }
			 * @param GF_Field $field The current Nested Form field.
			 * @param array    $form  The current form.
			 */
			$args = gf_apply_filters( array( 'gpnf_init_script_args', $form['id'], $field->id ), $args, $field, $form );

			//$script .= 'if( typeof window.gpnfNestedEntries == "undefined" ) { window.gpnfNestedEntries = {}; }';
			$script .= 'new GPNestedForms( ' . json_encode( $args ) . ' );';

		}

		if ( $script ) {
			GFFormDisplay::add_init_script( $form['id'], 'gpnf_init_script', GFFormDisplay::ON_PAGE_RENDER, $script );
		}

	}

	public function color_luminance( $hex, $percent ) {

		// validate hex string

		$hex     = preg_replace( '/[^0-9a-f]/i', '', $hex );
		$new_hex = '#';

		if ( strlen( $hex ) < 6 ) {
			$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
		}

		// convert to decimal and change luminosity
		for ( $i = 0; $i < 3; $i++ ) {
			$dec      = hexdec( substr( $hex, $i * 2, 2 ) );
			$dec      = min( max( 0, $dec + $dec * $percent ), 255 );
			$new_hex .= str_pad( dechex( $dec ), 2, 0, STR_PAD_LEFT );
		}

		return $new_hex;
	}

	public function get_submitted_nested_entries( $form, $field_id = false, $display_values = true ) {

		$all_entries = array();

		foreach ( $form['fields'] as $field ) {

			if ( $field->type != 'form' || $field->id != $field_id ) {
				continue;
			}

			$nested_form        = $this->get_nested_form( $field->gpnfForm );
			$display_fields     = $field->gpnfFields;
			$bypass_permissions = false;

			$entries   = array();
			$entry_ids = rgpost( 'input_' . $field['id'] );

			/**
			 * This has been updated to be a little more deliberate. Previously, we used $field->get_value_submission()
			 * to fetch the prepopulation value. Let's call GFFormsModel::get_parameter_value() directly instead.
			 */
			if ( empty( $entry_ids ) && $field->allowsPrepopulate ) {
				// If prepop is enabled - AND - we're using prepopulated values, let's bypass permissions.
				//$bypass_permissions = true; @note Good idea; bad execution. Allows bad folks to populate arbitrary entry IDs and view entry data. Filter below to allow advanced users to bypass permissions.
				$entry_ids = GFFormsModel::get_parameter_value( $field->inputName, array() /* @todo this might get us in trouble; should pass real $field_values */, $field );
			}

			if ( empty( $entry_ids ) || ! is_string( $entry_ids ) ) {
				$entry_ids = array();
			} else {
				$entry_ids = $this->get_child_entry_ids_from_value( $entry_ids );
			}

			// if no posted $entry_ids check if we are resuming a saved entry
			if ( $this->get_save_and_continue_token() && empty( $entry_ids ) ) {
				$entry_ids = $this->get_save_and_continue_child_entry_ids( $form['id'], $field->id );
			}

			if ( empty( $entry_ids ) && is_callable( 'gravityview' ) && $gv_entry = gravityview()->request->is_edit_entry() ) {
				$parent_entry = $gv_entry->as_entry();
				$entry_ids    = $this->get_child_entry_ids_from_value( $this->get_field_value( $form, $parent_entry, $field->id ) );

				if ( $entry_ids ) {
					$bypass_permissions = true;
				}
			}

			// Support populating child entries back into Nested Form field when parent form is reloaded via the
			// WC GF Product Add-on's Enable Cart Edit option.
			if ( empty( $entry_ids ) && is_callable( 'WC' ) && $cart_item_key = rgget( 'wc_gforms_cart_item_key' ) ) {

				$cart_item = WC()->cart->get_cart_item( $cart_item_key );
				if ( ! empty( $cart_item ) && isset( $cart_item['_gravity_form_lead'] ) && isset( $cart_item['_gravity_form_data'] ) ) {
					$entry     = $cart_item['_gravity_form_lead'];
					$entry_ids = $this->get_child_entry_ids_from_value( $this->get_field_value( $form, $entry, $field->id ) );
				}
			}

			// Load entries from session.
			if ( empty( $entry_ids ) ) {

				$session  = new GPNF_Session( $form['id'] );
				$_entries = $session->get( 'nested_entries' );
				if ( ! empty( $_entries[ $field['id'] ] ) ) {
					$entry_ids = $_entries[ $field['id'] ];
				}
			}

			/**
			 * Bypass entry permissions when populating entries into a Nested Form field.
			 *
			 * @since 1.0
			 *
			 * @param bool   $bypass_permissions Should entry permissions be bypassed?
			 * @param array  $form               Current form object.
			 * @param object $field              Current field object.
			 */
			$bypass_permissions = gf_apply_filters( array( 'gpnf_bypass_entry_permissions', $form['id'], $field->id ), $bypass_permissions, $form, $field );

			if ( ! empty( $entry_ids ) ) {

				foreach ( $entry_ids as $entry_id ) {

					$entry = GFAPI::get_entry( $entry_id );
					if ( is_wp_error( $entry ) ) {
						continue;
					}

					if ( ! $bypass_permissions && ! GPNF_Entry::can_current_user_edit_entry( $entry ) ) {
						continue;
					}

					if ( $display_values ) {
						$entries[] = $this->get_entry_display_values( $entry, $nested_form );
					} else {
						$entries[] = $entry;
					}
				}
			}

			$all_entries[ $field->id ] = $entries;

		}

		$return_entries = $field_id ? rgar( $all_entries, $field_id ) : $all_entries;
		/**
		 * Filter nested form submitted child entries.
		 *
		 * @since 1.0-beta-8.57
		 * @param array                $return_entries  Current submitted entries
		 * @param GP_Field_Nested_Form $nested_form     Nested form entries belong to
		 * @param bool                 $display_values  Array contains a simplified version of entries
		 *                                              if false, array contains a list of GPNF_Entry objects.
		 */
		$return_entries = gf_apply_filters(
			array( 'gpnf_submitted_nested_entries' ),
			$return_entries,
			$nested_form,
			$display_values
		);
		return $return_entries;
	}

	public function remove_extra_other_choices( $form ) {

		foreach ( $form['fields'] as &$field ) {

			if ( $field->get_input_type() !== 'radio' ) {
				continue;
			}

			$choices     = $field->choices;
			$other_index = 0;

			foreach ( $choices as $index => $choice ) {
				if ( $choice['value'] == 'gf_other_choice' ) {
					$other_index = $index;
				}
			}

			if ( $other_index ) {
				$field->choices = array_splice( $choices, 0, $other_index );
			}
		}

		return $form;
	}

	public function populate_field_from_session_cookie( $value, $field, $name ) {

		$session = new GPNF_Session( rgar( $_REQUEST, 'gpnf_parent_form_id' ) );
		$_value  = rgars( $session->get_cookie(), "request/{$name}" );
		if ( $_value ) {
			$value = $_value;
		}

		return $value;
	}

	/**
	 * Get Save & Continue from URL if it exists.
	 *
	 * @return string|null
	 */
	public function get_save_and_continue_token() {
		return isset( $_POST['gform_resume_token'] ) ? $_POST['gform_resume_token'] : rgget( 'gf_token' );
	}

	public function get_save_and_continue_child_entry_ids( $form, $field_id = false ) {

		if ( ! $this->get_save_and_continue_token() ) {
			return array();
		}

		// Form ID was passed; get form.
		if ( is_numeric( $form ) ) {
			$form = GFAPI::get_form( $form );
		}

		$incomplete_submission_info = GFFormsModel::get_draft_submission_values( $this->get_save_and_continue_token() );
		if ( $incomplete_submission_info['form_id'] != $form['id'] ) {
			return array();
		}

		$submission_details_json = $incomplete_submission_info['submission'];
		$submission_details      = json_decode( $submission_details_json, true );
		$submitted_values        = $submission_details['submitted_values'];

		$child_entries = array();

		foreach ( $form['fields'] as $field ) {
			if ( $field->get_input_type() == 'form' ) {
				$child_entries[ $field->id ] = $this->get_child_entry_ids_from_value( rgar( $submitted_values, $field->id ) );
			}
		}

		if ( $field_id ) {
			return rgar( $child_entries, $field_id );
		}

		return $child_entries;
	}

	public function get_save_and_continue_parent_hash( $form_id ) {

		$entry_ids = $this->get_save_and_continue_child_entry_ids( $form_id );

		if ( ! empty( $entry_ids ) ) {
			$first_entry_id = rgars( array_values( $entry_ids ), '0/0' );
			return gform_get_meta( $first_entry_id, GPNF_Entry::ENTRY_PARENT_KEY );
		}

		return false;
	}

	public function add_child_entry_meta( $entry ) {

		if ( ! $this->is_nested_form_submission() ) {
			return $entry;
		}

		$parent_form       = GFAPI::get_form( $this->get_parent_form_id() );
		$nested_form_field = $this->get_posted_nested_form_field( $parent_form );

		$entry = new GPNF_Entry( $entry );
		$entry->set_parent_form( $parent_form['id'] );
		$entry->set_nested_form_field( $nested_form_field->id );
		$entry->set_expiration();

		return $entry->get_entry();
	}



	// # EDIT POPULATION AND SUBMISSION

	public function prepare_form_for_population( $form ) {

		foreach ( $form['fields'] as &$field ) {

			$field['allowsPrepopulate'] = true;

			if ( is_array( $field['inputs'] ) ) {
				$inputs = $field['inputs'];
				foreach ( $inputs as &$input ) {
					$input['name'] = (string) $input['id'];
				}
				$field['inputs'] = $inputs;
			}

			$field['inputName'] = $field['id'];

		}

		return $form;
	}

	public function set_edit_form_action( $form_tag ) {
		return preg_replace( "|action='(.*?)'|", "action=''", $form_tag );
	}

	public function set_gwlc_selected_values( $values, $field ) {

		$entry_id = $this->get_posted_entry_id();
		$entry    = GFAPI::get_entry( $entry_id );

		return GFFormsModel::get_lead_field_value( $entry, $field );

	}

	public function prepare_entry_for_population( $entry ) {

		$form = GFFormsModel::get_form_meta( $entry['form_id'] );

		foreach ( $form['fields'] as $field ) {

			switch ( GFFormsModel::get_input_type( $field ) ) {

				case 'checkbox':
					$values = $this->get_field_values_from_entry( $field, $entry );
					if ( is_array( $values ) ) {
						$value = implode( ',', array_filter( $values ) );
					} else {
						$value = $values;
					}
					$entry[ $field['id'] ] = $value;

					break;

				case 'list':
					$value       = maybe_unserialize( rgar( $entry, $field->id ) );
					$list_values = array();

					if ( is_array( $value ) ) {
						foreach ( $value as $vals ) {
							if ( is_array( $vals ) ) {
								// Escape commas so the value is not split into multiple inputs.
								$vals = implode(
									'|',
									array_map(
										function( $value ) {
											$value = str_replace( ',', '&#44;', $value );
											return $value;
										},
										$vals
									)
								);
							}
							array_push( $list_values, $vals );
						}
						$entry[ $field->id ] = implode( ',', $list_values );
					}

					break;

				case 'multiselect':
					$value                 = self::maybe_decode_json( rgar( $entry, $field->id ) );
					$entry[ $field['id'] ] = $value;
					break;

				case 'fileupload':
					$is_multiple = $field->multipleFiles;
					$value       = rgar( $entry, $field->id );
					$return      = array();

					if ( $is_multiple ) {
						$files = json_decode( $value );
					} else {
						$files = array( $value );
					}

					foreach ( $files as $file ) {

						$path_info = pathinfo( $file );

						// Check if file has been "deleted" via form UI.
						$upload_files = json_decode( rgpost( 'gform_uploaded_files' ), ARRAY_A );
						$input_name   = "input_{$field->id}";

						if ( is_array( $upload_files ) && array_key_exists( $input_name, $upload_files ) && ! $upload_files[ $input_name ] ) {
							continue;
						}

						if ( $is_multiple ) {
							$return[] = array(
								'temp_filename'     => 'GPNF_DOES_NOT_EXIT.png',
								'uploaded_filename' => $path_info['basename'],
							);
						} else {
							$return[] = $path_info['basename'];
						}
					}

					// if $uploaded_files array is not set for this form at all, init as array
					if ( ! isset( GFFormsModel::$uploaded_files[ $form['id'] ] ) ) {
						GFFormsModel::$uploaded_files[ $form['id'] ] = array();
					}

					// check if this field's key has been set in the $uploaded_files array, if not add this file (otherwise, a new image may have been uploaded so don't overwrite)
					if ( ! isset( GFFormsModel::$uploaded_files[ $form['id'] ][ "input_{$field->id}" ] ) ) {
						GFFormsModel::$uploaded_files[ $form['id'] ][ "input_{$field->id}" ] = $is_multiple ? $return : reset( $return );
					}
			}

			switch ( $field->type ) {
				case 'post_category':
					$value = rgar( $entry, $field->id );

					if ( ! empty( $value ) ) {
						$categories = array();

						foreach ( explode( ',', $value ) as $cat_string ) {
							$categories[] = GFCommon::format_post_category( $cat_string, true );
						}

						$entry[ $field['id'] ] = 'multiselect' === $field->get_input_type() ? $categories : implode( '', $categories );
					}
					break;
			}
		}

		return $entry;
	}

	public function get_field_values_from_entry( $field, $entry ) {

		$values = array();

		foreach ( $entry as $input_id => $value ) {
			$fid = intval( $input_id );
			if ( $fid == $field['id'] ) {
				$values[] = $value;
			}
		}

		return count( $values ) <= 1 ? $values[0] : $values;
	}

	public function maybe_edit_entry( $entry_id, $form ) {

		if ( $this->is_nested_form_edit_submission() ) {

			$entry_id = $this->get_posted_entry_id();
			$this->handle_existing_images_submission( $form, $entry_id );

			// Force Gravity Forms to fetch data from the post when evaluating conditional logic while re-saving the entry.
			add_filter( 'gform_use_post_value_for_conditional_logic_save_entry', '__return_true' );

			add_filter( 'gform_entry_post_save', array( $this, 'refresh_product_cache_and_update_total' ), 10, 2 );

		}

		return $entry_id;
	}

	public function add_nested_inputs( $form_tag, $form ) {

		// makes it easier to show/hide these fields for debugging
		$type = 'hidden';

		// append parent form ID input
		$form_tag .= '<input type="' . $type . '" name="gpnf_parent_form_id" value="' . esc_attr( $this->get_parent_form_id() ) . '" />';

		// append nested form field ID input
		$form_tag .= '<input type="' . $type . '" name="gpnf_nested_form_field_id" value="' . esc_attr( $this->get_posted_nested_form_field_id() ) . '" />';

		// append entry ID and mode inputs
		if ( $entry_id = $this->get_posted_entry_id() ) {
			$form_tag .= '<input type="' . $type . '" value="' . esc_attr( $entry_id ) . '" name="gpnf_entry_id" />';
			$form_tag .= '<input type="' . $type . '" value="edit" name="gpnf_mode" />';
		}

		// append has_validation_error bool input
		$is_valid  = ! isset( GFFormDisplay::$submission[ $form['id'] ] ) || rgar( GFFormDisplay::$submission[ $form['id'] ], 'is_valid' );
		$form_tag .= '<input type="' . $type . '" value="' . esc_attr( $is_valid ) . '" id="' . esc_attr( 'gpnf_is_valid_' . $form['id'] ) . '" />';

		return $form_tag;
	}

	/**
	 * When editing a child entry, refresh the product cache so changes made to pricing fields are correctly reflected in
	 * the entry.
	 *
	 * @param $entry
	 * @param $form
	 *
	 * @return mixed
	 */
	public function refresh_product_cache_and_update_total( $entry, $form ) {

		if ( ! $this->has_pricing_field( $form ) ) {
			return $entry;
		}

		// Gravity Forms will already refresh product cache when re-saving an entry if there is a calculation field.
		// Let's save a little load and only do this when GF won't.
		if ( ! GFFormDisplay::has_calculation_field( $form ) ) {
			GFFormsModel::refresh_product_cache( $form, $entry );
		}

		foreach ( $form['fields'] as $field ) {

			if ( $field['type'] == 'total' ) {
				$entry[ $field['id'] ] = GFCommon::get_order_total( $form, $entry );
				GFAPI::update_entry( $entry );
			}
		}

		return $entry;
	}

	public function has_pricing_field( $form ) {

		if ( $form && is_array( $form['fields'] ) ) {
			foreach ( $form['fields'] as $field ) {
				if ( GFCommon::is_product_field( $field->type ) ) {
					return true;
				}
			}
		}

		return false;
	}

	public function handle_existing_images_submission( $form, $entry_id ) {
		global $_gf_uploaded_files;

		$entry = GFAPI::get_entry( $entry_id );
		if ( ! $entry ) {
			return;
		}

		// get all fileupload fields
		// loop through and see if the image has been:
		//  - resubmitted:         populate the existing image data into the $_gf_uploaded_files
		//  - deleted:             do nothing
		//  - new image submitted: do nothing

		if ( empty( $_gf_uploaded_files ) ) {
			$_gf_uploaded_files = array();
		}

		foreach ( $entry as $input_id => $value ) {

			if ( ! is_numeric( $input_id ) ) {
				continue;
			}

			$field      = GFFormsModel::get_field( $form, $input_id );
			$input_name = "input_{$field['id']}";

			if ( $field->get_input_type() != 'fileupload' ) {
				continue;
			}

			// Handle multi-file uploads.
			if ( $field->multipleFiles ) {

				$value = json_decode( $value, true );
				if ( ! is_array( $value ) ) {
					$value = array();
				}

				$posted = wp_list_pluck( rgar( json_decode( rgpost( 'gform_uploaded_files' ), true ), $input_name ), 'uploaded_filename' );
				$count  = count( $value );

				// Remove any files that have been removed via the UI.
				for ( $i = $count - 1; $i >= 0; $i-- ) {
					$path = pathinfo( $value[ $i ] );
					if ( ! in_array( $path['basename'], $posted ) ) {
						unset( $value[ $i ] );
					}
				}

				// Populate existing images into post where GF will be looking for them.
				$_POST[ "input_{$field->id}" ] = json_encode( $value );

			}
			// Handle single file uploads.
			elseif ( self::is_prepopulated_file_upload( $form['id'], $input_name ) ) {
				$_gf_uploaded_files[ $input_name ] = $value;
			}
		}

	}

	/**
	 * Check for newly updated file. Only applies to single file uploads.
	 *
	 * @param $form_id
	 * @param $input_name
	 *
	 * @return bool
	 */
	public function is_new_file_upload( $form_id, $input_name ) {

		$file_info     = GFFormsModel::get_temp_filename( $form_id, $input_name );
		$temp_filepath = GFFormsModel::get_upload_path( $form_id ) . '/tmp/' . $file_info['temp_filename'];

		// check if file has already been uploaded by previous step
		if ( $file_info && file_exists( $temp_filepath ) ) {
			return true;
		}
		// check if file is uploaded on current step
		elseif ( ! empty( $_FILES[ $input_name ]['name'] ) ) {
			return true;
		}

		return false;
	}

	public function is_prepopulated_file_upload( $form_id, $input_name, $is_multiple = false ) {

		// prepopulated files will be stored in the 'gform_uploaded_files' field
		$uploaded_files = json_decode( rgpost( 'gform_uploaded_files' ), ARRAY_A );

		// file is prepopulated if it is present in the 'gform_uploaded_files' field AND is not a new file upload
		$in_uploaded_files = is_array( $uploaded_files ) && array_key_exists( $input_name, $uploaded_files ) && ! empty( $uploaded_files[ $input_name ] );
		$is_prepopulated   = $in_uploaded_files && ! $this->is_new_file_upload( $form_id, $input_name );

		return $is_prepopulated;
	}



	// # VALIDATION

	public function has_nested_form_field( $form, $check_visibility = false ) {
		$fields = GFCommon::get_fields_by_type( $form, $this->field_type );
		$count  = 0;
		foreach ( $fields as $field ) {
			if ( ! $check_visibility || $field->visibility != 'administrative' ) {
				$count++;
			}
		}
		return $count > 0;
	}



	// # INTEGRATIONS

	public function add_full_child_entry_data_for_webhooks( $data, $feed, $entry, $form ) {

		// This should be structed like an Entry Object; if not, we don't want to mess with it.
		if ( ! is_array( $data ) ) {
			return $data;
		}

		foreach ( $form['fields'] as $field ) {

			if ( $field->get_input_type() != $this->field_type || ! array_key_exists( $field->id, $data ) ) {
				continue;
			}

			$_entry             = new GPNF_Entry( $entry );
			$data[ $field->id ] = $_entry->get_child_entries( $field->id );

		}

		return $data;
	}

	public function adopt_partial_entry_children( $partial_entry, $form ) {

		$parent_entry  = new GPNF_Entry( $partial_entry );
		$child_entries = $parent_entry->get_child_entries();

		foreach ( $child_entries as $child_entry ) {
			$child_entry = new GPNF_Entry( $child_entry );
			$child_entry->set_parent_form( $form['id'], $parent_entry->id );
			$child_entry->delete_expiration();
		}

		return $partial_entry;
	}


	// # HELPERS

	public function is_nested_form_submission() {
		$parent_form_id = $this->get_parent_form_id();
		return $parent_form_id > 0;
	}

	public function is_form_submission() {
		// $_POST['gforms_save_entry'] is set when editing an entry via Gravity Flow.
		return rgpost( 'gform_submit' ) || rgpost( 'gforms_save_entry' );
	}

	public function is_nested_form_edit_submission() {
		return $this->is_nested_form_submission() && rgpost( 'gpnf_mode' ) == 'edit';
	}

	public function get_parent_form_id() {

		if ( ! $this->parent_form_id ) {
			$this->parent_form_id = $this->get_posted_parent_form_id();
		}

		return $this->parent_form_id;
	}

	public function get_nested_form( $nested_form_id ) {
		return gf_apply_filters( array( 'gpnf_get_nested_form', $nested_form_id ), GFAPI::get_form( $nested_form_id ) );
	}

	public function get_posted_parent_form_id() {
		return absint( rgpost( 'gpnf_parent_form_id' ) );
	}

	public function get_posted_nested_form_field_id() {
		return absint( rgpost( 'gpnf_nested_form_field_id' ) );
	}

	public function get_posted_entry_id() {
		return rgpost( 'gpnf_entry_id' );
	}

	public function get_fields_by_ids( $ids, $form ) {
		$fields = array();

		if ( ! is_array( $ids ) ) {
			return $fields;
		}

		foreach ( $ids as $id ) {
			foreach ( $form['fields'] as $field ) {
				if ( $field->id == $id ) {
					$fields[] = $field;
				}
			}
		}

		return $fields;
	}

	/**
	 * Due to our __call() method, is_callable() checks will result in fatal errors. We don't need this function but
	 * let's define it to avoid unpleasantries.
	 */
	public function get_form_field_value( $entry, $field_id, $field ) {
		return $field->get_value_export( $entry, $field_id );
	}

	/**
	 * Remove/replace old settings with their newer counterparts.
	 *
	 * @param $form
	 *
	 * @return mixed
	 */
	public function cleanup_form_meta( $form ) {

		$settings_map = array(
			'gp-nested-forms_fields' => 'gpnfFields',
			'gp-nested-forms_form'   => 'gpnfForm',
		);

		foreach ( $form['fields'] as &$field ) {

			if ( $field->type != 'form' ) {
				continue;
			}

			foreach ( $settings_map as $old => $new ) {
				if ( $field->$old ) {
					if ( ! $field->$new ) {
						$field->$new = $field->$old;
					}
					unset( $field->{$old} );
				}
			}
		}

		return $form;
	}

	public function wpml_translate_entry_labels( $field_keys ) {
		$field_keys[] = 'gpnfEntryLabelSingular';
		$field_keys[] = 'gpnfEntryLabelPlural';
		return $field_keys;
	}

	/**
	 * Re-render the Signature field when editing Nested Entries with the value of the entry being edited.
	 *
	 * Ticket #22155
	 */
	public function rerender_signature_field_on_edit( $markup, $field, $value, $entry_id, $form_id ) {
		static $_processing;

		if ( $field->type !== 'signature' ) {
			return $markup;
		}

		/**
		 * Prevent recursion
		 */
		if ( $_processing === true ) {
			return $markup;
		}

		if ( empty( $GLOBALS['gpnf_current_edit_entry'] ) ) {
			return $markup;
		}

		$entry = $GLOBALS['gpnf_current_edit_entry'];
		$form  = GFAPI::get_form( $form_id );

		$_processing = true;
		$markup      = GFCommon::get_field_input( $field, rgar( $entry, $field->id ), $entry_id, $form_id, $form );
		$_processing = false;

		return $markup;
	}

	public function is_preview() {
		return rgget( 'gf_page' );
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

}

function gp_nested_forms() {
	return GP_Nested_Forms::get_instance();
}

GFAddOn::register( 'GP_Nested_Forms' );

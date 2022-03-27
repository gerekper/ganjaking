<?php

if ( ! class_exists( 'GP_Plugin' ) ) {
	return;
}

class GP_Inventory extends GP_Plugin {

	private static $instance = null;

	protected $_version     = GP_INVENTORY_VERSION;
	protected $_path        = 'gp-inventory/gp-inventory.php';
	protected $_full_path   = __FILE__;
	protected $_slug        = 'gp-inventory';
	protected $_title       = 'Gravity Wiz Inventory';
	protected $_short_title = 'Inventory';

	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function minimum_requirements() {
		return array(
			'gravityforms' => array(
				'version' => '2.4',
			),
			'wordpress'    => array(
				'version' => '5.5',
			),
			'plugins'      => array(
				'gravityperks/gravityperks.php' => array(
					'name'    => 'Gravity Perks',
					'version' => '2.2.5',
				),
			),
		);
	}

	public function init() {
		parent::init();

		require_once plugin_dir_path( __FILE__ ) . 'includes/functions.php';
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-resources.php';
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-integration-gpld.php';
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-integration-gppa.php';

		gp_inventory_resources();
		gp_inventory_integration_gpld();
		gp_inventory_integration_gppa();

		load_plugin_textdomain( 'gp-inventory', false, basename( dirname( __file__ ) ) . '/languages/' );

		add_action( 'gperk_field_settings', array( $this, 'field_settings_ui' ) );
		add_action( 'admin_footer', array( $this, 'field_editor_portal' ) );

		add_action( 'init', array( $this, 'init_inventory_types' ), 16 );
	}

	public function init_ajax() {
		parent::init_ajax();

		/* Privileged */
		add_action( 'wp_ajax_gpi_get_simple_current_inventory_claimed', array( $this, 'ajax_get_simple_current_inventory_claimed' ) );
		add_action( 'wp_ajax_gpi_get_choices_current_inventory_claimed', array( $this, 'ajax_get_choices_current_inventory_claimed' ) );
	}

	public function init_admin() {
		parent::init_admin();

		GravityPerks::enqueue_field_settings();
	}

	public function scripts() {
		$scripts = array(
			array(
				'handle'    => 'gp-inventory-form-editor',
				'src'       => $this->get_base_url() . '/js/built/gp-inventory-form-editor.js',
				'version'   => $this->_version,
				'deps'      => array( 'gform_gravityforms', 'jquery' ),
				'in_footer' => true,
				'enqueue'   => array(
					array( 'admin_page' => array( 'form_editor' ) ),
				),
				'callback'  => array( $this, 'localize_admin_scripts' ),
			),
			array(
				'handle'    => 'gp-inventory',
				'src'       => $this->get_base_url() . '/js/built/gp-inventory.js',
				'version'   => $this->_version,
				'deps'      => array( 'gform_gravityforms', 'jquery' ),
				'in_footer' => true,
				'enqueue'   => array(
					array( $this, 'should_enqueue_frontend_script' ),
				),
			),
		);

		return array_merge( parent::scripts(), $scripts );
	}

	public function styles() {

		$styles = array(
			array(
				'handle'  => 'gp-inventory-form-editor',
				'src'     => $this->get_base_url() . '/styles/gp-inventory-form-editor.css',
				'version' => $this->_version,
				'enqueue' => array(
					array( 'admin_page' => array( 'form_editor' ) ),
				),
			),
		);

		return array_merge( parent::styles(), $styles );

	}

	/**
	 * @param $form array
	 *
	 * @return boolean
	 */
	public function should_enqueue_frontend_script( $form ) {
		return gp_inventory_type_advanced()->is_applicable_form( $form, true );
	}

	public function localize_admin_scripts() {
		$resources = gp_inventory_resources()->get_resources();

		wp_localize_script(
			'gp-inventory-form-editor',
			'GPI_ADMIN',
			array(
				'nonce'                            => wp_create_nonce( 'gp-inventory' ),
				// Outputting empty array will default to [] instead of {}
				'resources'                        => ! empty( $resources ) ? $resources : null,
				/**
				 * Specify what input types are supported. Input types can be excluded for field types by using
				 * the `gpi_supported_field_types` filter.
				 *
				 * @since 1.0-beta-1.0
				 *
				 * @see `gpi_supported_field_types`
				 * @see `gpi_choice_input_types`
				 *
				 * @param array $input_types The input types that are supported.
				 */
				'supportedInputTypes'              => apply_filters( 'gpi_supported_input_types', array( 'radio', 'select', 'checkbox', 'multiselect' ) ),
				/**
				 * Specify what field types are supported. This works in conjunction with `gpi_supported_input_types`
				 * by overriding specific field types.
				 *
				 * When adding a field type to this filter, you can provide a value of `true` to enable Inventory for
				 * all input types of a given field type. To limit it to certain input types for a field, provide an
				 * array of the supported input types.
				 *
				 * @example
				 *   return array( 'product' => array( 'singleproduct' ) )
				 *
				 * @since 1.0-beta-1.0
				 *
				 * @see `gpi_supported_input_types`
				 * @see `gpi_choice_input_types`
				 *
				 * @param array $field_types The field types and the supported input types for the fields.
				 */
				'supportedFieldTypes'              => apply_filters( 'gpi_supported_field_types', array(
					'product'  => true,
					'quantity' => array(),
				) ),
				/**
				 * Force the Form Editor to always show the Inventory Limit for choices, products, and resources rather than
				 * the current inventory.
				 *
				 * @since 1.0-beta-1.0
				 *
				 * @param boolean $always_show_limit_in_editor Whether to always show the limit instead of current inventory in
				 *   the Form Editor.
				 */
				'alwaysShowInventoryLimitInEditor' => apply_filters( 'gpi_always_show_inventory_limit_in_editor', false ),
				/**
				 * Specify what input types should utilize choice-based inventory.
				 *
				 * @since 1.0-beta-1.0
				 *
				 * @see `gpi_supported_input_types`
				 * @see `gpi_supported_field_types`
				 *
				 * @param array $input_types The input types that should use choice-based inventory.
				 */
				'choiceInputTypes'                 => apply_filters( 'gpi_choice_input_types', array( 'radio', 'select', 'checkbox', 'multiselect' ) ),
				'strings'                          => array(
					'add'                                 => __( 'Add', 'gp-inventory' ),
					'adding'                              => __( 'Adding...', 'gp-inventory' ),
					'edit'                                => __( 'Edit', 'gp-inventory' ),
					'editing'                             => __( 'Editing...', 'gp-inventory' ),
					'close'                               => __( 'Close', 'gp-inventory' ),
					'delete'                              => __( 'Delete', 'gp-inventory' ),
					'inventory_type'                      => __( 'Inventory Type', 'gp-inventory' ),
					'inventory_type_untracked'            => __( 'Untracked', 'gp-inventory' ),
					'inventory_type_simple'               => __( 'Simple', 'gp-inventory' ),
					'inventory_type_advanced'             => __( 'Advanced', 'gp-inventory' ),
					'inventory'                           => __( 'Inventory', 'gp-inventory' ),
					'inventory_per_combination'           => __( 'Inventory Per Combination', 'gp-inventory' ),
					'resource'                            => __( 'Resource', 'gp-inventory' ),
					'resource_name'                       => __( 'Resource Name', 'gp-inventory' ),
					'resource_scopes'                     => __( 'Scopes', 'gp-inventory' ),
					'add_scope'                           => __( 'Add Scope', 'gp-inventory' ),
					'remove_scope'                        => __( 'Remove Scope', 'gp-inventory' ),
					'resource_modal_subtitle'             => __( 'Changes will affect any field that is mapped to this resource.', 'gp-inventory' ),
					'resource_mapped_to_choice_field'     => __( 'This resource is mapped to a choice-based field. Inventory must be set on each choice in the field settings.', 'gp-inventory' ),
					'set_inventory_on_choices'            => wp_kses( __( 'Inventory must be set per choice in the <a href="#" class="gpi-go-to-choices">Choices setting</a>.', 'gp-inventory' ), array(
						'a' => array(
							'href'  => array(),
							'class' => array(),
						),
					) ),
					'select_a_resource'                   => __( 'Select a Resource', 'gp-inventory' ),
					'select_a_field'                      => __( 'Select a Field', 'gp-inventory' ),
					'add_resource'                        => __( 'Add Resource', 'gp-inventory' ),
					// translators: placeholder is for resource name
					'edit_resource'                       => __( 'Edit Resource: %s', 'gp-inventory' ),
					'show_available_inventory'            => __( 'Show available inventory', 'gp-inventory' ),
					'hide_form_inventory_exhausted'       => __( 'Hide form when inventory exhausted', 'gp-inventory' ),
					'hide_choice_inventory_exhausted'     => __( 'Hide choice when inventory exhausted', 'gp-inventory' ),
					'inventory_insufficient_message'      => __( 'Inventory Insufficient Message', 'gp-inventory' ),
					'inventory_exhausted_message'         => __( 'Inventory Exhausted Message', 'gp-inventory' ),
					'available_inventory_message'         => __( 'Inventory Available Message', 'gp-inventory' ),
					'inventory_insufficient_message_default' => $this->inventory_insufficient_default_message(),
					'inventory_exhausted_message_default' => $this->inventory_exhausted_default_message(),
					'inventory_available_message_default' => $this->inventory_available_default_message(),
					'inventory_available_on_choice_message_default' => $this->inventory_available_on_choice_default_message(),
					'tooltip_inventory_type'              => sprintf(
						'<h6>%s</h6> %s',
						__( 'Inventory Type', 'gp-inventory' ),
						__( 'Specify how inventory should be tracked for this field.<br><br><b>Untracked:</b> Inventory will not be tracked for this field.<br><br><b>Simple:</b> Inventory will be tracked specifically for this field.<br><br><b>Advanced:</b> Inventory will be tracked by a resource. A resource can be shared by multiple fields or scoped by a group of fields.', 'gp-inventory' )
					),
					'tooltip_inventory'                   => sprintf(
						'<h6>%s</h6> %s',
						__( 'Inventory', 'gp-inventory' ),
						__( 'Specify the amount of this resource that is available.', 'gp-inventory' )
					),
					'tooltip_insufficient_message'        => sprintf(
						'<h6>%s</h6> %s',
						__( 'Inventory Insufficient Message', 'gp-inventory' ),
						sprintf(
							/* translators: %s: The default message specified in GP_Inventory::inventory_insufficient_default_message() message. */
							__( 'Specify a message that will be shown when the quantity requested is greater than the amount of inventory available.<br><br>Default message:<br>%s', 'gp-inventory' ),
							$this->inventory_insufficient_default_message()
						)
					),
					'tooltip_exhausted_message'           => sprintf(
						'<h6>%s</h6> %s',
						__( 'Inventory Exhausted Message', 'gp-inventory' ),
						sprintf(
							/* translators: %s: The default message specified in GP_Inventory::inventory_exhausted_default_message() message. */
							__( 'Specify a message that will be shown when the inventory is exhausted.<br><br>Default message:<br>%s', 'gp-inventory' ),
							$this->inventory_exhausted_default_message()
						)
					),
					'tooltip_available_message'           => sprintf(
						'<h6>%s</h6> %s',
						__( 'Inventory Available Message', 'gp-inventory' ),
						sprintf(
							/* translators: %s: The default message specified in GP_Inventory::inventory_available_default_message() message. */
							__( 'Specify a message that displays the amount of inventory available.<br><br>Default message:<br>%s', 'gp-inventory' ),
							$this->inventory_available_default_message()
						)
					),
					'tooltip_resource'                    => sprintf(
						'<h6>%s</h6> %s',
						__( 'Resource', 'gp-inventory' ),
						__( 'Select or create a Resource. A Resource allows you to share inventory across multiple fields and forms - or - group multiple fields together to scope inventory by a unique combination of field values. <a href="https://gravitywiz.com/documentation/gravity-forms-inventory/#resources">Learn more.</a>', 'gp-inventory' )
					),
					'tooltip_resource_name'               => sprintf(
						'<h6>%s</h6> %s',
						__( 'Resource Name', 'gp-inventory' ),
						__( 'Give your Resource a name. This is only used to identify the Resource in the Form Editor and in other Admin screens.', 'gp-inventory' )
					),
					'tooltip_resource_scopes'             => sprintf(
						'<h6>%s</h6> %s',
						__( 'Scopes', 'gp-inventory' ),
						__( 'Specify scopes that apply to this Resource. Common scopes include Date, Time, and Location. Resource scopes can be mapped to corresponding fields in the inventory-enabled field\'s settings. <a href="https://gravitywiz.com/documentation/gravity-forms-inventory/#scopes">Learn more.</a>', 'gp-inventory' )
					),
					'tooltip_resource_scopes_map'         => sprintf(
						'<h6>%s</h6> %s',
						__( 'Scopes Map', 'gp-inventory' ),
						__( 'Map each of the selected Resources\'s scopes to a corresponding field.', 'gp-inventory' )
					),
					'tooltip_show_available_inventory'    => sprintf(
						'<h6>%s</h6> %s',
						__( 'Show available inventory', 'gp-inventory' ),
						__( 'Enable this option to display a message indicating how much inventory is available for this field.<br><br>For choice-based fields, this message will be appended to the choice label. For all other fields, this message will be displayed in the field description.', 'gp-inventory' )
					),
					'tooltip_hide_form_inventory_exhausted' => sprintf(
						'<h6>%s</h6> %s',
						__( 'Hide form when inventory exhausted', 'gp-inventory' ),
						__( 'Enable this option to hide the form when this field\'s inventory is exhausted. For choice-based fields, each choice\'s inventory must be exhausted before the form will be hidden.', 'gp-inventory' )
					),
					'tooltip_hide_choice_inventory_exhausted' => sprintf(
						'<h6>%s</h6> %s',
						__( 'Hide choice when inventory exhausted', 'gp-inventory' ),
						__( 'By default, choices are disabled when their inventory is exhausted. Enable this option to remove the choice instead.', 'gp-inventory' )
					),
					'error_adding_resource'               => __( 'Error: Could not add resource. Please try again.', 'gp-inventory' ),
					'error_editing_resource'              => __( 'Error: Could not edit resource. Please try again.', 'gp-inventory' ),
					'error_deleting_resource'             => __( 'Error: Could not delete resource. Please try again.', 'gp-inventory' ),
					// translators: placeholder is for resource name
					'delete_resource_confirm'             => __( 'Are you sure you wish to delete the "%s" resource?', 'gp-inventory' ),
					'delete_resource_scope_confirm'       => __( 'Deleting scopes on resources with existing entries is not recommend. Are you sure you wish to delete this scope?', 'gp-inventory' ),
				),
			)
		);
	}

	/**
	 * Set up inventory types
	 */
	public function init_inventory_types() {
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-inventory-type.php';
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-inventory-type-simple.php';
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-inventory-type-advanced.php';
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-inventory-type-choices.php';

		gp_inventory_type_simple();
		gp_inventory_type_advanced();
		gp_inventory_type_choices();
	}

	## Messages

	/**
	 * @return string
	 */
	public function inventory_exhausted_default_message() {
		return __( 'Sorry, there are no more of this item.', 'gp-inventory' );
	}

	/**
	 * @return string
	 */
	public function inventory_available_default_message() {
		return __( '{available} {item|items} available.', 'gp-inventory' );
	}

	/**
	 * @return string
	 */
	public function inventory_available_on_choice_default_message() {
		return __( '({available} {item|items} remaining)', 'gp-inventory' );
	}

	/**
	 * @return string
	 */
	public function inventory_insufficient_default_message() {
		return __( 'You requested {requested} of this item but there are only {available} of this item left.', 'gp-inventory' );
	}

	## AJAX
	public function ajax_get_simple_current_inventory_claimed() {
		if ( ! GFCommon::current_user_can_any( array( 'gravityforms_edit_forms' ) ) ) {
			wp_die( - 1 );
		}

		check_ajax_referer( 'gp-inventory', 'security' );

		$field = GFAPI::get_field( rgpost( 'formId' ), rgpost( 'fieldId' ) );

		switch ( rgar( $field, 'gpiInventory' ) ) {
			case 'simple':
				wp_send_json( gp_inventory_type_simple()->get_claimed_inventory( $field ) );

				break;
		}

		die();
	}

	public function ajax_get_choices_current_inventory_claimed() {
		if ( ! GFCommon::current_user_can_any( array( 'gravityforms_edit_forms' ) ) ) {
			wp_die( - 1 );
		}

		check_ajax_referer( 'gp-inventory', 'security' );

		$field = GFAPI::get_field( rgpost( 'formId' ), rgpost( 'fieldId' ) );

		switch ( rgar( $field, 'gpiInventory' ) ) {
			case 'simple':
			case 'advanced':
				/* If the resource has properties, we do not support showing the current inventory in the form editor
				 as it's tough to emulate the properties. */
				if ( ! gp_inventory_type_advanced()->is_using_properties( $field ) ) {
					wp_send_json( gp_inventory_type_choices()->get_choice_counts( $field->formId, $field ) );
				}

				break;
		}

		die();
	}

	## Admin Field Settings

	public function field_settings_ui( $position ) {
		/*
		 * The class of this root element needs to contain whatever field setting classes are used for the Add-On otherwise GF 2.6 will nuke them due to them
		 * not being present in the initial markup.
		 *
		 * Additionally, it needs to be an <li>, have the class to protect as the first class, and also have the field_setting class.
		 */
		?>
		<!-- Populated with Vue -->
		<li id="gp-inventory" class="gpi-field-setting field_setting"></li>
		<?php
	}

	public function field_editor_portal() {
		if ( ! GFCommon::is_form_editor() ) {
			return;
		}
		?>
		<!-- Portal to render modals in -->
		<div id="gp-inventory-modal-portal"></div>
		<?php
	}

	public function tooltips( $tooltips ) {
		$tooltips[ $this->_slug . '_inventory' ] = sprintf(
			'<h6>%s</h6> %s',
			__( 'GP Inventory', 'gp-inventory' ),
			__( 'Example tooltip.', 'gp-inventory' )
		);

		$tooltips[ $this->_slug . '_inventory_limit' ] = sprintf(
			'<h6>%s</h6> %s',
			__( 'GP Inventory', 'gp-inventory' ),
			__( 'Example tooltip.', 'gp-inventory' )
		);

		return $tooltips;
	}

}

function gp_inventory() {
	return GP_Inventory::get_instance();
}

GFAddOn::register( 'GP_Inventory' );

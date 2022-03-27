<?php

if ( ! class_exists( 'GP_Plugin' ) ) {
	return;
}

class GP_Disable_Entry_Creation extends GP_Plugin {


	public $version                   = GP_DISABLE_ENTRY_CREATION;
	public $min_gravity_forms_version = '1.8';

	private static $_instance = null;

	protected $_version     = GP_DISABLE_ENTRY_CREATION;
	protected $_path        = 'gp-disable-entry-creation/gp-disable-entry-creation.php';
	protected $_full_path   = __FILE__;
	protected $_slug        = 'gp-disable-entry-creation';
	protected $_title       = 'Gravity Forms Disable Entry Creation';
	protected $_short_title = 'Disable Entry Creation';

	public static function get_instance() {
		if ( self::$_instance === null ) {
			self::$_instance = isset( self::$perk ) ? new self( new self::$perk ) : new self();
		}
		return self::$_instance;
	}

	public function pre_init() {

		add_filter( 'gform_form_settings_fields', array( $this, 'add_delete_setting' ), 10, 2 );
		add_filter( 'gform_form_settings_initial_values', array( $this, 'add_form_settings_initial_values' ), 10, 2 );

	}

	public function init() {

		parent::init();

		// # UI
		if ( ! $this->is_gf_version_gte( '2.5' ) ) {
			$this->perk->add_tooltip( $this->perk->key( 'disable_entry_creation' ), $this->tooltip_content() );
			add_filter( 'gform_form_settings', array( $this, 'add_delete_setting_legacy' ), 10, 2 );
			add_filter( 'gform_form_settings', array( $this, 'add_conditional_delete_setting_legacy' ), 10, 3 );
		}

		add_action( 'gform_pre_form_settings_save', array( $this, 'save_delete_setting' ), 10 );

		// # Functionality

		add_action( 'gform_after_submission', array( $this, 'maybe_delete_form_entry' ), 15, 2 );
		add_action( 'gform_activate_user', array( $this, 'delete_form_entry_after_activation' ), 15, 3 );
		add_action( 'gform_user_updated', array( $this, 'delete_form_entry_after_update' ), 15, 3 );

	}

	public function scripts() {

		$scripts = parent::scripts();

		$scripts[] = array(
			'handle'  => 'gp-disable-entry-creation-admin',
			'deps'    => array( 'jquery' ),
			'src'     => $this->get_base_url() . '/js/gp-disable-entry-creation-admin.js',
			'version' => $this->_version,
			'enqueue' => array(
				function() {
					return GFForms::get_page() === 'form_settings' && $this->is_gf_version_gte( '2.5-beta-1' );
				},
			),
		);

		return $scripts;
	}

	public function tooltip_content() {
		return sprintf(
			'<h6>%s</h6> %s',
			__( 'Disable entry creation', 'gravityperks' ),
			__(
				'An entry must be created for Gravity Forms to function correctly; however, this option will automatically delete
                the entry and any associated files after the submission process has been completed. If the form has a User Registration
                feed, the entry will be deleted once the user has been activated or updated.',
				'gp-disable-entry-creation'
			)
		);
	}

	/**
	 * Check if installed version of Gravity Forms is greater than or equal to the specified version.
	 *
	 * @param string $version Version to compare with Gravity Forms' version.
	 *
	 * @return bool
	 */
	public function is_gf_version_gte( $version ) {
		return class_exists( 'GFForms' ) && version_compare( str_ireplace( 'beta', '', GFForms::$version ), $version, '>=' );
	}

	// Settings

	function add_delete_setting_legacy( $settings, $form ) {
		$is_enabled          = ( rgar( $form, 'deleteEntry' ) ) ? 'checked="checked"' : '';
		$settings_group_name = __( 'Entry Creation', 'gravityperks' );
		$delete_entry        = '
			<tr>
				<th>' . __( 'Entry creation', 'gravityforms' ) . ' ' . gform_tooltip( $this->perk->key( 'disable_entry_creation' ), '', true ) . '</th>
				<td>
					<input type="checkbox" id="delete_entry" name="delete_entry" value="1" ' . $is_enabled . ' onchange="ToggleEntryCreationConditionalLogic();"/>
					<label for="delete_entry">' . __( 'Disable entry creation', 'gravityperks' ) . '</label>
				</td>
			</tr>
			
		  <script type="text/javascript">	
			function ToggleEntryCreationConditionalLogic() {				
				if (jQuery("#delete_entry").is(":checked")) {
					ShowSettingRow(\'#entry_creation_conditional_logic_row\');
				} else {
					HideSettingRow(\'#entry_creation_conditional_logic_row\');
				}
			}
		  </script>';

		if ( empty( $settings[ $settings_group_name ] ) ) {
			$settings[ $settings_group_name ] = array();
		}

		$settings[ $settings_group_name ]['deleteEntry'] = $delete_entry;

		return $settings;
	}

	function add_delete_setting( $settings, $form ) {

		$settings[] = array(
			'title'  => esc_html__( 'Entry Creation', 'gp-disable-entry-creation' ),
			'fields' => array(
				array(
					'name'    => 'deleteEntry',
					'type'    => 'checkbox',
					'tooltip' => $this->tooltip_content(),
					'label'   => __( 'Disable entry creation', 'gp-disable-entry-creation' ),
					'choices' => array(
						array(
							'name'  => 'deleteEntry',
							'label' => __( 'Disable entry creation.', 'gp-disable-entry-creation' ),
						),
					),
					'fields'  => array(
						array(
							'name'        => 'entryCreationConditional',
							'type'        => 'conditional_logic',
							'label'       => '',
							'dependency'  => array(
								'live'   => true,
								'fields' => array(
									array(
										'field' => 'deleteEntry',
									),
								),
							),
							'object_type' => 'entry_creation',
							'checkbox'    => array(
								'label'  => esc_html__( 'Disable entry creation conditionally.', 'gravityforms' ),
								'hidden' => false,
							),
						),
						array(
							'name' => 'entryCreationStyles',
							'type' => 'html',
							'html' => '
								<style>
									#entry_creation_conditional_logic_container {
										padding-top: 0.5rem;
										padding-bottom: 0;
										display: none;
									}
									#entry_creation_logic_type {
										margin-left: 0.5rem;
										margin-right: 0.5rem;
									}
									#gform_setting_entryCreationStyles {
										display: none;
									}
								</style>',
						),
					),
				),
			),
		);

		return $settings;
	}

	function add_form_settings_initial_values( $initial_values, $form ) {
		$initial_values['entry_creation_conditional_logic'] = rgar( $initial_values, 'entryCreationConditional' );
		return $initial_values;
	}

	function add_conditional_delete_setting_legacy( $settings, $form ) {
		$instructions            = 'Disable entry creation if';
		$conditional_logic_style = rgar( $form, 'deleteEntry' ) ? '' : ' style="display: none;"';

		$html = '
			<tr id="entry_creation_conditional_logic_row" class="child_setting_row"' . $conditional_logic_style . '>
				<td colspan="2" class="gf_sub_settings_cell">
					<div class="gf_animate_sub_settings">
						<table>
							<tr>
				                <th style="width: 185px;">' . __( 'Conditional Logic', 'gravityforms' ) . '</th>
				                <td>
				                    <div>
				                        <input type="checkbox" id="entry_creation_conditional_logic" name="entry_creation_conditional_logic" value="1"
					                        ' . checked( ! ! rgar( $form, 'entryCreation' ), true, false ) . '
					                        onClick="SetEntryCreationConditionalLogic(this.checked); ToggleConditionalLogic(false, \'entry_creation\')" 
					                        onKeyPress="SetEntryCreationConditionalLogic(this.checked); ToggleConditionalLogic(false, \'entry_creation\')" />
				                        <label for="entry_creation_conditional_logic">' . __( 'Enable', 'gravityperks' ) . '</label>
				                    </div>
				                    <div id="entry_creation_conditional_logic_container" class="gform-settings-field__conditional-logic">
				                        <!-- dynamically populated -->
				                    </div>
				                    
				                    <style>
				                    #entry_creation_conditional_logic_container {
										padding-top: 0.5rem;
										padding-bottom: 0;
				                    }
				                    </style>
				                    
				                    <script type="text/javascript"> 
					                   gform.addFilter( \'gform_conditional_logic_description\', function( str, descPieces, objectType, obj ) {
				
										    if (objectType === \'entry_creation\') {
										        delete descPieces.actionType;
										        descPieces.objectDescription = \'' . $instructions . '\';
										        var descPiecesArr = makeArray( descPieces );
				
										        return descPiecesArr.join(\' \');
										    }
										    
										    return str;
										
										} );
										
									   gform.addFilter( \'gform_conditional_object\', function( object, objectType ) {
				
										   if (objectType !== \'entry_creation\') {
										        return object;
										   }
										    
										    if (typeof form.entryCreation === \'undefined\') {
										        form.entryCreation = {};
										        form.entryCreation.conditionalLogic = new ConditionalLogic();
										    }
										    
										    return form.entryCreation;
										
										} );
										
										function SetEntryCreationConditionalLogic(isChecked) {
											 form.entryCreation = isChecked ? { conditionalLogic: new ConditionalLogic() } : null;
										}
										
										ToggleConditionalLogic(true, \'entry_creation\');
					                </script>
				                </td>
				            </tr>
						</table>
					</div>
				</td>
			</tr>
        ';

		$settings[ __( 'Entry Creation', 'gravityperks' ) ]['entryCreationConditional'] = $html;

		return $settings;
	}

	function save_delete_setting( $form ) {

		if ( $this->is_gf_version_gte( '2.5-beta-1' ) ) {

			$form['deleteEntry'] = rgpost( '_gform_setting_deleteEntry' );

			if ( ! isset( $form['entryCreation'] ) ) {
				$form['entryCreation'] = array();
			}

			$is_conditional_checked = boolval( rgpost( '_gform_setting_entry_creation_conditional_logic' ) );

			if ( $is_conditional_checked ) {
				$form['entryCreation']['conditionalLogic'] = json_decode( rgpost( '_gform_setting_entry_creation_conditional_logic_object' ), true );
				$form['entryCreationConditional']          = rgpost( '_gform_setting_entry_creation_conditional_logic' );
			} else {
				$form['entryCreation']['conditionalLogic'] = null;
			}
		} else {

			$form['deleteEntry'] = rgpost( 'delete_entry' );

		}

		return $form;
	}

	// Functionality
	function maybe_delete_form_entry( $entry, $form ) {

		$ur_feed = $this->get_user_registration_feed( $entry, $form );

		if ( empty( $ur_feed ) || ! rgar( $ur_feed, 'is_active' ) || ! (bool) rgars( $ur_feed, 'meta/userActivationEnable' ) ) {
			$this->delete_form_entry( $entry );
		}

	}

	function delete_form_entry_after_activation( $user_id, $user_data, $signup_meta ) {
		$entry = GFAPI::get_entry( $signup_meta['lead_id'] );
		$this->delete_form_entry( $entry );
	}

	function delete_form_entry_after_update( $user_id, $config, $entry ) {
		$this->delete_form_entry( $entry );
	}

	function delete_form_entry( $entry ) {

		$form = GFAPI::get_form( $entry['form_id'] );

		// If form is configured to delete entries - AND - entry still exists (could be deleted by another process)...
		if ( rgar( $form, 'deleteEntry' ) && ! is_wp_error( GFAPI::get_entry( $entry['id'] ) ) ) {

			if ( ! GFCommon::evaluate_conditional_logic( rgars( $form, 'entryCreation/conditionalLogic' ), $form, $entry ) ) {
				return;
			}

			$delete = GFAPI::delete_entry( $entry['id'] );
			$result = ( $delete ) ? "entry {$entry['id']} successfully deleted." : $delete;
			GFCommon::log_debug( "GP Disable Entry Creation - GFAPI::delete_entry() - form #{$form['id']}: " . print_r( $result, true ) );
		}
	}

	function get_user_registration_feed( $entry, $form ) {

		$feed = array();

		if ( is_callable( 'gf_user_registration' ) && is_callable( array( gf_user_registration(), 'get_single_submission_feed' ) ) ) {
			$feed = gf_user_registration()->get_single_submission_feed( $entry, $form );
		} elseif ( is_callable( array( 'GFUser', 'get_active_config' ) ) ) {
			$feed = GFUser::get_active_config( $form, $entry );
		}

		return $feed;
	}

}

function gp_disable_entry_creation() {
	return GP_Disable_Entry_Creation::get_instance( null );
}

GFAddOn::register( 'GP_Disable_Entry_Creation' );

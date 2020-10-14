<?php

class GP_Disable_Entry_Creation extends GWPerk {

    public $version = GP_DISABLE_ENTRY_CREATION;
    public $min_gravity_forms_version = '1.8';

	private static $instance = null;

	public static function get_instance( $perk_file ) {
		if( null == self::$instance ) {
			self::$instance = new self( $perk_file );
		}
		return self::$instance;
	}

    public function init() {

        $this->add_tooltip( $this->key( 'disable_entry_creation' ), sprintf(
            '<h6>%s</h6> %s',
            __( 'Disable entry creation', 'gravityperks' ),
            __( 'An entry must be created for Gravity Forms to function correctly; however, this option will automatically delete
                the entry and any associated files after the submission process has been completed. If the form has a User Registration
                feed, the entry will be deleted once the user has been activated or updated.', 'gravityperks' )
        ) );

        // # UI

        add_filter( 'gform_form_settings',          array( $this, 'add_delete_setting' ), 10, 2 );
	    add_filter( 'gform_form_settings',          array( $this, 'add_conditional_delete_setting' ), 10, 3 );
        add_action( 'gform_pre_form_settings_save', array( $this, 'save_delete_setting' ), 10 );

        // # Functionality

        add_action( 'gform_after_submission', array( $this, 'maybe_delete_form_entry' ), 15, 2 );
        add_action( 'gform_activate_user',    array( $this, 'delete_form_entry_after_activation' ), 15, 3 );
        add_action( 'gform_user_updated',     array( $this, 'delete_form_entry_after_update' ), 15, 3 );

    }


    // Settings

	function add_delete_setting( $settings, $form ) {

		$is_enabled = ( rgar( $form, 'deleteEntry' ) ) ? 'checked="checked"' : "";

		$settings_group_name = __( 'Entry Creation', 'gravityperks' );

		if ( empty( $settings[$settings_group_name] ) ) {
			$settings[$settings_group_name] = array();
		}

		$settings[$settings_group_name]['deleteEntry'] = '
            <tr>
                <th>' . __( 'Entry creation', 'gravityforms' ) . ' ' . gform_tooltip( $this->key( 'disable_entry_creation' ), '', true ) . '</th>
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

		return $settings;

	}

	function add_conditional_delete_setting( $settings, $form ) {

		$instructions = 'Disable entry creation if';
		$conditional_logic_style = rgar($form, 'deleteEntry') ? '' : ' style="display: none;"';

		$settings[__( 'Entry Creation', 'gravityperks' )]['entryCreationConditional'] = '
			<tr id="entry_creation_conditional_logic_row" class="child_setting_row"' . $conditional_logic_style . '>
				<td colspan="2" class="gf_sub_settings_cell">
					<div class="gf_animate_sub_settings">
						<table>
							<tr>
				                <th style="width: 185px;">' . __( 'Conditional Logic', 'gravityforms' ) . '</th>
				                <td>
				                    <div>
				                        <input type="checkbox" id="entry_creation_conditional_logic" name="entry_creation_conditional_logic" value="1"
					                        ' . checked( !!rgar( $form, 'entryCreation' ), true, false ) . '
					                        onClick="SetEntryCreationConditionalLogic(this.checked); ToggleConditionalLogic(false, \'entry_creation\')" 
					                        onKeyPress="SetEntryCreationConditionalLogic(this.checked); ToggleConditionalLogic(false, \'entry_creation\')" />
				                        <label for="entry_creation_conditional_logic">' . __( 'Enable', 'gravityperks' ) . '</label>
				                    </div>
				                    <div id="entry_creation_conditional_logic_container">
				                        <!-- dynamically populated -->
				                    </div>
				                    
				                    <style>
				                    #entry_creation_conditional_logic_container { margin: 10px 0 0; display: none; width: 100% !important;
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

		return $settings;

	}

	function save_delete_setting( $form ) {
        $form['deleteEntry'] = rgpost( 'delete_entry' );

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

        if( is_callable( 'gf_user_registration' ) && is_callable( array( gf_user_registration(), 'get_single_submission_feed' ) ) ) {
            $feed = gf_user_registration()->get_single_submission_feed( $entry, $form );
        } else if( is_callable( array( 'GFUser', 'get_active_config' ) ) ) {
            $feed = GFUser::get_active_config( $form, $entry );
        }

        return $feed;
    }

}

function gp_disable_entry_creation() {
    return GP_Disable_Entry_Creation::get_instance( null );
}

<?php
/**
* Settings Content
* inside EVO_Settings()
* @version 4.0.2
*/

?>
<div class='evo_settings_box <?php echo (!empty($genral_opt['evo_rtl']) && $genral_opt['evo_rtl']=='yes')?'adminRTL':'';?>'>	
<?php
// SETTINGS SAVED MESSAGE
	$updated_code = (isset($_POST['settings-updated']) && $_POST['settings-updated']=='true')? '<div class="updated fade"><p>'.__('Settings Saved','eventon').'</p></div>':null;
	echo $updated_code;	
	
// TABS
switch ($this->focus_tab):	
	case "evcal_1":		
		// Event type custom taxonomy NAMES
		$event_type_names = evo_get_ettNames($evcal_opt[1]);
		$evt_name = $event_type_names[1];
		$evt_name2 = $event_type_names[2];

		$this->settings_tab_start(array(
			'field_group'=>'evcal_field_group',
			'nonce_key'=>AJDE_EVCAL_BASENAME,
			'nonce_field'=>'evcal_noncename',
			'tab_id'=>'evcal_1',
			'classes'=>array('evcal_admin_meta'. 'evcal_focus'),
			'inside_classes'=> array('evo_inside')
		));		
					
			require_once(AJDE_EVCAL_PATH.'/includes/admin/settings/class-settings-settings.php');
			
			$settings = new evo_settings_settings($evcal_opt);			
			
			$this->print_ajde_customization_form($settings->content(), $evcal_opt[1]);
		
		$this->settings_tab_end();


		// lightbox
		EVO()->lightbox->admin_lightbox_content(array(
			'class'=>'evo_log_lightbox', 
			'content'=>"<p class='evo_lightbox_loading'></p>",
			'title'=>__('EventON System Log','eventon'),
			'width'=>'900'
			)
		);

		?>

		<div class='evo_diag'>
			<!-- save settings -->
			<input type="submit" class="evo_admin_btn btn_prime" value="<?php _e('Save Changes') ?>" /> <a id='resetColor' style='display:none' class='evo_admin_btn btn_secondary'><?php _e('Reset to default colors','eventon')?></a>
		</div>		
		</form>

		<div class="evo_lang_export">
			<?php
				$nonce = wp_create_nonce('evo_export_settings');
				// url to export settings
				$exportURL = add_query_arg(array(
				    'action' => 'eventon_export_settings',
				    'nonce'=>$nonce
				), admin_url('admin-ajax.php'));

			?>
			<h3><?php _e('Import/Export General EventON Settings','eventon');?></h3>
			<p><i><?php _e('NOTE: Make sure to save changes after importing. This will import/export the general settings saved for eventon.','eventon');?></i></p>

			
			<div class='evo_data_upload_holder' style='position: relative;'>
				<?php 
				EVO()->elements->print_import_box_html(array(
					'box_id'=>'evo_settings_upload',
					'title'=>__('Upload JSON Settings File Form'),
					'message'=>__('NOTE: You can only upload settings data as .json file'),
					'file_type'=>'.json',
				));
				?>			

				<a id='evo_settings_import' class='evo_admin_btn btn_triad evo_data_upload_trigger'><?php _e('Import','eventon');?></a> 
				<a href='<?php echo $exportURL;?>' class='evo_admin_btn btn_triad'><?php _e('Export','eventon');?></a>
			</div>
		</div>
	
<?php  
	break;
		
	// LANGUAGE TAB
	case "evcal_2":		
			
		require_once(AJDE_EVCAL_PATH.'/includes/admin/settings/settings_language_tab.php');

		$settings_lang = new evo_settings_lang($evcal_opt);
		$settings_lang->get_content();
	
	break;
	
	// STYLES TAB
	case "evcal_3":
		
		echo '<form method="post" action="">';
		
		//settings_fields('evcal_field_group'); 
		wp_nonce_field( AJDE_EVCAL_BASENAME, 'evcal_noncename' );
				
		// styles settings tab content
		require_once(AJDE_EVCAL_PATH.'/includes/admin/settings/settings_styles_tab.php');
	
	break;
	
	// ADDON TAB
	case "evcal_4":
		
		// Addons settings tab content
		require_once(AJDE_EVCAL_PATH.'/includes/admin/settings/settings_addons_tab.php');

	
	break;
	
	// support TAB
	case "evcal_5":
		
		// Addons settings tab content
		require_once(AJDE_EVCAL_PATH.'/includes/admin/settings/settings_troubleshoot_tab.php');

	
	break;
	
	
		
	// ADVANDED extra field
	case "extra":
	
		// advanced tab content
		require_once(AJDE_EVCAL_PATH.'/includes/admin/settings/settings_advanced_tab.php');		
		
	break;
	
		default:
			do_action('eventon_settings_tabs_'. $this->focus_tab );
		break;
		
endswitch;

echo "</div>";
echo "</div>";
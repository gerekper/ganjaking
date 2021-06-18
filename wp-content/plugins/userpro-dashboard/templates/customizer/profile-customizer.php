<?php
	global $userpro;
	if( !isset( $updb_customizer_api ) ){
		$updb_customizer_api = new UPDBProfileCustomizer();
	}
	$no_of_col = $updb_default_options->updb_get_option( 'number_of_column' );
?>
<div class="profileDashboard dashboardRight" id = "dashboard-profile-customizer">
	<div class="userpro-dashboard userpro-<?php echo $i; ?> userpro-id-<?php echo $user_id; ?> ">
		<div class="userpro-section userpro-column userpro-collapsible-0 userpro-collapsed-0"><?php _e( 'Profile Customizer', 'userpro-dashboard' );?></div>
			<span><?php _e( 'Please drag&drop the modules you would like to display in your profile then click the save button.', 'userpro-dashboard' );?></span>
			<div class="updb-widgets">
			<div class="unused-widget-section-container">
			<div class="updb-basic-info"><?php _e( 'Unused Widgets', 'userpro-dashboard' );?></div>
			<div class="unused-widget-section updb-widget-section">
			 <ul class="ui-sortable droptrue" id="updb_unused_widget">
				<?php
					$updb_customizer_api->get_unused_widgets( $user_id );
				?>
			 </ul>
			</div>
			</div>
			<div class="act-widget-column">
			<?php for( $j=1; $j<=$no_of_col; $j++){?>
			<div class="updb-act-column-widget-container">
			<div class="updb-basic-info"><?php _e( 'Column '.$j.' Widgets', 'userpro-dashboard' );?></div>
			<div class="updb-widgets-column_<?php echo $j?> updb-widget-section updb-act-column-widget">
				<ul class="ui-sortable droptrue" id="updb-customizer_<?php echo $j; ?>" style="display:block;">
					<?php $updb_customizer_api->get_column_widgets( $user_id, $j );?>
				</ul>
			
			</div>
			</div>
			<?php }?>
			</div>
			</div>
		<div class="userpro-field userpro-submit userpro-column">
			<input type="button" value="Save Changes" class="userpro-button" id="save_widgets"/>
			<img src="<?php echo $userpro->skin_url(); ?>loading.gif" alt="" class="userpro-loading" />
		</div>
		</div>
</div>

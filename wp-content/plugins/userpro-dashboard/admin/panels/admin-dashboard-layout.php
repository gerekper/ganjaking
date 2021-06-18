<?php
	if( !isset( $updb_default_options ) ){
		$updb_default_options = new UPDBDefaultOptions();
	}
?>
<form method="post" action="">
	
	<h3><?php _e('Profile Customizer','userpro-dashboard'); ?></h3>
	<?php
	global $userpro;
	if( !isset( $updb_customizer_api ) ){
		$updb_customizer_api = new UPDBProfileCustomizer();
	}
	$no_of_col = $updb_default_options->updb_get_option( 'number_of_column' );
?>

	<div class="userpro-dashboard userpro-1 userpro-id-1">
		<br><span><?php _e( 'Please drag&drop the modules you would like to display in profile then click the save button.', 'userpro-dashboard' );?></span>
		<div class="updb-widgets-admin">
			<div class="unused-widget-section-container-admin">
			<div class="updb-basic-info-admin"><?php _e( 'Unused Widgets', 'userpro-dashboard' );?></div>
			<div class="unused-widget-section-admin updb-widget-section">
			 <ul class="ui-sortable droptrue" id="updb_unused_widget">
				<?php
					$updb_customizer_api->get_unused_widgets_admin();
				?>
			 </ul>
			</div>
			</div>
			<div class="act-widget-column">
			<?php for( $j=1; $j<=$no_of_col; $j++){?>
			<div class="updb-act-column-widget-container-admin">
			<div class="updb-basic-info-admin"><?php _e( 'Column '.$j.' Widgets', 'userpro-dashboard' );?></div>
			<div class="updb-widgets-column_<?php echo $j?> updb-widget-section-admin updb-act-column-widget-admin">
				<ul class="ui-sortable droptrue" id="updb-customizer_<?php echo $j; ?>" style="display:block;">
					<?php $updb_customizer_api->get_column_widgets_admin( $j );?>
				</ul>
			
			</div>
			</div>
			<?php }?>
			</div>
			</div>
		
		</div>
		<div class="userpro-field userpro-submit userpro-column">
			<input type="button" value="Save Changes" class="userpro-button" id="save_widgets_admin"/>
		</div>

</form>
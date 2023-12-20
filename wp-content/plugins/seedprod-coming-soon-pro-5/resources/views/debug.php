<h1 class="sp-text-xl sp-mt-4 sp-mb-1"><?php esc_html_e( 'System Information', 'seedprod-pro' ); ?></h1>
<textarea readonly="readonly" style="width: 100%; height: 500px"><?php echo esc_textarea( seedprod_pro_get_system_info() ); ?></textarea>


<?php
if (  ! empty( $_POST['sp_reset_cs'] ) && 1 == $_POST['sp_reset_cs'] ) {
	update_option( 'seedprod_coming_soon_page_id', false );
}
if (  ! empty( $_POST['sp_reset_mm'] ) && 1 == $_POST['sp_reset_mm'] ) {
	update_option( 'seedprod_maintenance_mode_page_id', false );
}
if (  ! empty( $_POST['sp_reset_p404'] ) && 1 == $_POST['sp_reset_p404'] ) {
	update_option( 'seedprod_404_page_id', false );
}
if (  ! empty( $_POST['sp_reset_loginp'] ) && 1 == $_POST['sp_reset_loginp'] ) {
	update_option( 'seedprod_login_page_id', false );
}
if (  ! empty( $_POST['sp_builder_debug'] ) && 1 == $_POST['sp_builder_debug'] ) {
	update_option( 'seedprod_builder_debug', true );
} elseif ( ! empty( $_POST ) ) {
	update_option( 'seedprod_builder_debug', false );
}

// get option
$seedprod_builder_debug = get_option( 'seedprod_builder_debug' );
?>
<h1 class="sp-text-xl sp-mt-4 sp-mb-1"><?php esc_html_e( 'Debug Tools', 'seedprod-pro' ); ?></h1>
<?php if ( ! empty( ! empty( $_POST ) ) ) { ?>
<div id="setting-error-settings_updated" class="notice notice-success settings-error is-dismissible" style="margin:0px 20px 0 0"> 
<p><strong>Updated.</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>
<?php } ?>
<form method="post" novalidate="novalidate">
<?php wp_nonce_field( 'debug-reset' ); ?>
<table class="form-table" role="presentation">
<tbody>
<tr>
<th scope="row">Builder Debug<br><small>If you are having a problem in the builder like inserting an image or some other feature in the builder is broken, check this box.</small></th>
<td> <fieldset><legend class="screen-reader-text"><span>Builder Debug</span></legend><label for="sp_builder_debug">
<input name="sp_builder_debug" type="checkbox" id="sp_builder_debug" value="1" <?php echo ( ! empty( $seedprod_builder_debug ) ) ? 'checked' : ''; ?>>
	Enable Builder Debug</label>
</fieldset></td>
</tr>
<tr>
<th scope="row">Reset Coming Soon Page<br><small>This will delete the current coming soon page.</small></th>
<td> <fieldset><legend class="screen-reader-text"><span>Builder Debug</span></legend><label for="sp_reset_cs">
<input name="sp_reset_cs" type="checkbox" id="sp_reset_cs" value="1">
Check Box and Save to Reset</label>
</fieldset></td>
</tr>
<tr>
<th scope="row">Reset Maintenance Mode Page<br><small>This will delete the current maintenance page.</small></th>
<td> <fieldset><legend class="screen-reader-text"><span>Builder Debug</span></legend><label for="sp_reset_mm">
<input name="sp_reset_mm" type="checkbox" id="sp_reset_mm" value="1">
Check Box and Save to Reset</label>
</fieldset></td>
</tr>
<tr>
<th scope="row">Reset Login Page<br><small>This will delete the current Custom Login page.</small></th>
<td> <fieldset><legend class="screen-reader-text"><span>Builder Debug</span></legend><label for="sp_reset_loginp">
<input name="sp_reset_loginp" type="checkbox" id="sp_reset_loginp" value="1">
Check Box and Save to Reset</label>
</fieldset></td>
</tr>
<tr>
<th scope="row">Reset 404 Page<br><small>This will delete the current 404 page.</small></th>
<td> <fieldset><legend class="screen-reader-text"><span>Builder Debug</span></legend><label for="sp_reset_p404">
<input name="sp_reset_p404" type="checkbox" id="sp_reset_p404" value="1">
Check Box and Save to Reset</label>
</fieldset></td>
</tr></tbody>
</table>
<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"></p>
</form>

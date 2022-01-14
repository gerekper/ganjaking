<?php
//  * Copyright 2014 SEEDPROD LLC (email : john@seedprod.com, twitter : @seedprod)

/**
 *  Add HTMLwebform section
 */

function seed_cspv5_legacy_section_htmlwebform( $emaillist, $page_id ) {
	// Get settings
	$settings_name = 'seed_cspv5_' . $page_id . '_' . $emaillist;
	$settings      = get_option( $settings_name );
	if ( ! empty( $settings ) ) {
		$settings = maybe_unserialize( $settings );
	}
	ob_start();
	?>
	<div class="postbox">
		<h3 class="hndle"><?php _e( 'HTMLwebform', 'seedprod' ); ?></h3>
		<div class="inside">
		<p><?php __( 'Enter a HTML web form from any provider. This will bypass the Thank You page. <a href="https://support.seedprod.com/article/5-use-any-shortcode-html-3rd-party-scripts-or-web-form" target="_blank">Learn More</a>', 'seedprod' ); ?></p>
		<form id="seed_cspv5_emaillist_settings">
		<input type="hidden" id="settings_name" name="settings_name" value="<?php echo $settings_name; ?>"/>
		<input type="hidden" id="page_id" name="page_id" value="<?php echo $page_id; ?>"/>
		<input type="hidden" id="emaillist" name="emaillist" value="<?php echo $emaillist; ?>"/>
		<p><a href="https://support.seedprod.com/article/5-use-any-shortcode-html-3rd-party-scripts-or-web-form" target="_blank">Learn how to Configure</a></p>
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row">
						<strong>HTML</strong>
					</th>
					<td>
						<textarea style="height:500px" id="html_integration" name="html_integration" class="large-text"><?php echo ( ! empty( $settings['html_integration'] ) ) ? $settings['html_integration'] : ''; ?></textarea>
						<br>
						<small class="description">Enter the html provided from the 3rd party service.</small>
					</td>
				</tr>
			</tbody>
		</table>
		</form>
		<p>
			<input id="save-btn" type="submit" value="Save and Continue Editing" class="button-primary" >
			<button id="cancel-btn" class="button-secondary">Cancel</button>
		</p>
		</div>
	</div>

	<?php
	$return_url = preg_replace( '/seed_cspv5_customize=\d*(.)*/', 'seed_cspv5_customize=' . $page_id . '&tab=content#header-form-settings', urldecode( $_GET['return'] ) );
	?>
	<script>
	<?php $save_ajax_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=seed_cspv5_save_emaillist_settings', 'seed_cspv5_save_emaillist_settings' ) ); ?>
	var save_url = '<?php echo $save_ajax_url; ?>';
	<?php $get_list_ajax_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=seed_cspv5_get_email_lists', 'seed_cspv5_get_email_lists' ) ); ?>
	var get_list_url = '<?php echo $get_list_ajax_url; ?>';
   
	jQuery( "#save-btn" ).click(function() {
		jQuery(this).prop( "disabled", true );
		var dataString = jQuery( '#seed_cspv5_emaillist_settings' ).serialize();
		var jqxhr = jQuery.post( save_url, dataString)
		  .done(function(data) {
			  if(data == '1'){
				 window.location.href = '<?php echo $return_url; ?>'; 
			  }else{
				  alert('Error. Please try again.');
			  }
		  })
		  .fail(function() {
			  alert('Error. Please try again.');
		  })
		  .always(function() {
			jQuery('#save-btn').prop( "disabled", false );
		});
	});
	
	jQuery( "#cancel-btn" ).click(function(e) {
		e.preventDefault();
		window.location.href = '<?php echo $return_url; ?>';
	});
	

	</script>
	<?php

	return $output = ob_get_clean();

}

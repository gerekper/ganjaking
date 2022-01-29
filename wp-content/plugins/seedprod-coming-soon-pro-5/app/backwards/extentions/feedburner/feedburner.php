<?php
//  * Copyright 2014 SEEDPROD LLC (email : john@seedprod.com, twitter : @seedprod)
/**
 *  Add FeedBurner section
 */

function seed_cspv5_legacy_section_feedburner( $emaillist, $page_id ) {
	// Get settings
	$settings_name = 'seed_cspv5_' . $page_id . '_' . $emaillist;
	$settings      = get_option( $settings_name );
	if ( ! empty( $settings ) ) {
		$settings = maybe_unserialize( $settings );
	}
	ob_start();
	?>
	<div class="postbox">
		<h3 class="hndle"><?php _e( 'FeedBurner', 'seedprod' ); ?></h3>
		<div class="inside">
		<p><?php __( 'Configure saving subscribers to MailChimp options. Save after you enter your api key to load your list. <a href="#">Learn More</a>', 'seedprod' ); ?></p>
		<form id="seed_cspv5_emaillist_settings">
		<input type="hidden" id="settings_name" name="settings_name" value="<?php echo $settings_name; ?>"/>
		<input type="hidden" id="page_id" name="page_id" value="<?php echo $page_id; ?>"/>
		<input type="hidden" id="emaillist" name="emaillist" value="<?php echo $emaillist; ?>"/>
		<p><a href="https://support.seedprod.com/article/81-collecting-emails-with-feedburner" target="_blank">Learn how to Configure</a></p>
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row">
						<strong>Address</strong>
					</th>
					<td>
						<input class="regular-text" type="textbox" id="feedburner_addr" name="feedburner_addr" value="<?php echo ( ! empty( $settings['feedburner_addr'] ) ) ? $settings['feedburner_addr'] : ''; ?>" />
						<br>
						<small class="description">Enter your FeedBurner address. http://feeds.feedburner.com/<i>YOURADDRESS</i></small>
					</td>
				</tr>
				
				<tr valign="top">
					<th scope="row">
						<strong>Local</strong>
					</th>
					<td>
						<input class="regular-text" type="textbox" id="feedburner_local" name="feedburner_local" value="<?php echo ( ! empty( $settings['feedburner_local'] ) ) ? $settings['feedburner_local'] : 'es_ES'; ?>" />
						<br>
						<small class="description">The language the FeedBurner form is displayed in. The default is English. <a target='_blank' href="http://support.google.com/feedburner/bin/answer.py?hl=en&answer=81423">Learn more</a>.</small>
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

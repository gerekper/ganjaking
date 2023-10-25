<?php
/*
Plugin Name: SeedProd MyMail
Plugin URI: http://www.seedprod.com
Description: SeedProd MyMail Add On
Version:  1.0.0
Author: SeedProd
Author URI: http://www.seedprod.com
TextDomain: seedprod
License: GPLv2
Copyright 2014 SEEDPROD LLC (email : john@seedprod.com, twitter : @seedprod)
*/


/**
 *  programatically enable wp head and footer
 */
add_filter( 'seed_cspv5_legacy_enable_wp_head_footer_list', 'seed_cspv5_legacy_enable_wp_head_footer_mymail' );

function seed_cspv5_legacy_enable_wp_head_footer_mymail( $arr ) {
	$arr[] = 'mymail';
	return $arr;
}



/**
 *  Add mymail section to admin
 */

function seed_cspv5_legacy_section_mymail( $emaillist, $page_id ) {
	// Get settings
	$settings_name = 'seed_cspv5_' . $page_id . '_' . $emaillist;
	$settings      = get_option( $settings_name );
	if ( ! empty( $settings ) ) {
		$settings = maybe_unserialize( $settings );
	}
	ob_start();
	?>
	<div class="postbox">
		<h3 class="hndle"><?php _e( 'Mailster formerly MyMail', 'seedprod' ); ?></h3>
		<div class="inside">
		<p><?php __( 'Configure saving subscribers to MyMail options. This email provider will bypass the coming soon page and referral tracking if enabled.', 'seedprod' ); ?></p>
		<form id="seed_cspv5_emaillist_settings">
		<input type="hidden" id="settings_name" name="settings_name" value="<?php echo $settings_name; ?>"/>
		<input type="hidden" id="page_id" name="page_id" value="<?php echo $page_id; ?>"/>
		<input type="hidden" id="emaillist" name="emaillist" value="<?php echo $emaillist; ?>"/>
		<p><a href="https://support.seedprod.com/article/79-collecting-emails-with-mymail" target="_blank">Learn how to Configure</a></p>
		<table class="form-table">
			<tbody>

				<tr valign="top">
					<th scope="row">
						<strong>Form ID</strong>
					</th>
					<td>
						<input class="regular-text" type="textbox" id="mymail_form_id" name="mymail_form_id" value="<?php echo ( ! empty( $settings['mymail_form_id'] ) ) ? $settings['mymail_form_id'] : ''; ?>" />
						<br>
						<small class="description">Enter the form ID.</small>
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





/**
 *  Output form on landing page
 */
add_filter( 'seed_cspv5_legacy_show_form_mymail', 'cspv5_legacy_show_form_mymail_shortcode', 10, 1 );

function cspv5_legacy_show_form_mymail_shortcode( $output ) {
	$seed_cspv5 = get_option( 'seed_cspv5' );
	$output     = '';
	if ( class_exists( 'mymail' ) ) {
		if ( ! empty( $seed_cspv5['mymail_form_id'] ) ) {
			$output = mymail_form( $seed_cspv5['mymail_form_id'], 100, false );
		} else {
			$output = mymail_form( 0, 100, false );
		}
	}
	return $output;

}

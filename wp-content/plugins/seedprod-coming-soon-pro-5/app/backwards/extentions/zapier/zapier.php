<?php
/**
 *  Add Zapier section
 * Copyright 2014 SEEDPROD LLC (email : john@seedprod.com, twitter : @seedprod)
 */

function seed_cspv5_legacy_section_zapier( $emaillist, $page_id ) {
	// Get settings
	$settings_name = 'seed_cspv5_' . $page_id . '_' . $emaillist;
	$settings      = get_option( $settings_name );
	if ( ! empty( $settings ) ) {
		$settings = maybe_unserialize( $settings );
	}
	ob_start();
	?>
	<div class="postbox">
		<h3 class="hndle"><?php _e( 'Zapier', 'seedprod' ); ?></h3>
		<div class="inside">
		<form id="seed_cspv5_emaillist_settings">
		<input type="hidden" id="settings_name" name="settings_name" value="<?php echo $settings_name; ?>"/>
		<input type="hidden" id="page_id" name="page_id" value="<?php echo $page_id; ?>"/>
		<input type="hidden" id="emaillist" name="emaillist" value="<?php echo $emaillist; ?>"/>
		<p><a href="https://support.seedprod.com/article/65-zapier-integration" target="_blank">Learn how to Configure</a></p>
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row">
						<strong>Zapier Webhook Url</strong>
					</th>
					<td>
						<p><a href="https://zapier.com/developer/invite/40019/4e45d7accdf3b84dc93a86e37880b0d4/" target="_blank">Click here to use this Zapier Application and get your WebHook URL</a></p><br>
						<input class="regular-text" type="textbox" id="zapier_url" name="zapier_url" value="<?php echo ( ! empty( $settings['zapier_url'] ) ) ? $settings['zapier_url'] : ''; ?>" />
						<br>
						<small class="description">Enter the WebHook URL provided by Zapier</small>
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
 *  Subscribe Zapier
 */
add_action( 'seed_cspv5_legacy_emaillist_zapier', 'seed_cspv5_legacy_emaillist_zapier_add_subscriber' );

function seed_cspv5_legacy_emaillist_zapier_add_subscriber( $args ) {
		global $seed_cspv5_post_result;

		// Page Settings
		$settings = $args['settings'];
		extract( $settings );

		//  Emaillist Settings
		$settings_name = 'seed_cspv5_' . $page_id . '_' . $emaillist;
		$e_settings    = get_option( $settings_name );
		$e_settings    = maybe_unserialize( $e_settings );
		extract( $e_settings );

	require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/backwards/lib/nameparse.php';

	// If tracking enabled
	if ( ! empty( $enable_reflink ) || ! empty( $display_optin_confirm ) ) {
		seed_cspv5_legacy_emaillist_database_add_subscriber( $args );
	}

	// Set vars
	$url  = $zapier_url;
	$list = $zapier_list_id;
	$name = '';
	if ( ! empty( $_REQUEST['name'] ) ) {
		$name = $_REQUEST['name'];
	}
	$email = $_REQUEST['email'];
	$fname = '';
	$lname = '';

	if ( ! empty( $name ) ) {
		$name  = seed_cspv5_parse_name( $name );
		$fname = $name['first'];
		$lname = $name['last'];
	}

	$ref_url = '';
	if ( ! empty( $seed_cspv5_post_result['ref_url'] ) ) {
		$ref_url = $seed_cspv5_post_result['ref_url'];
	}

	$fullname = $fname . ' ' . $lname;

	$data = array(
		'first_name' => $fname,
		'last_name'  => $lname,
		'email'      => $email,
		'ref_url'    => $ref_url,
	);

	// Make Request
	$args = array(
		'timeout' => 45,
		'body'    => $data,
	);
	if ( ! empty( $zapier_url ) ) {
		$r = wp_remote_post( $zapier_url, $args );
	}

	// Return results
	if ( is_wp_error( $r ) ) {
		$seed_cspv5_post_result['status'] = '500';
	} else {
		// if(!empty($enable_reflink)){
		//     seed_cspv5_legacy_emaillist_database_add_subscriber();
		// }
		$body = wp_remote_retrieve_body( $r );
		$body = json_decode( $body );
		//var_dump($body);
		//die();
		if ( $body->status == 'success' ) {
			$seed_cspv5_post_result['status'] = '200';
		} else {
			$seed_cspv5_post_result['status'] = $body;
		}
	}
}

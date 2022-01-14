<?php
/**
 *  Add Sendy section
 * Copyright 2014 SEEDPROD LLC (email : john@seedprod.com, twitter : @seedprod)
 */

function seed_cspv5_legacy_section_sendy( $emaillist, $page_id ) {
	// Get settings
	$settings_name = 'seed_cspv5_' . $page_id . '_' . $emaillist;
	$settings      = get_option( $settings_name );
	if ( ! empty( $settings ) ) {
		$settings = maybe_unserialize( $settings );
	}
	ob_start();
	?>
	<div class="postbox">
		<h3 class="hndle"><?php _e( 'Sendy', 'seedprod' ); ?></h3>
		<div class="inside">
		<p><?php __( 'Store emails in your Sendy app. <a href="https://support.seedprod.com/article/69-collecting-emails-with-sendy" target="_blank">Learn More</a>', 'seedprod' ); ?></p>
		<form id="seed_cspv5_emaillist_settings">
		<input type="hidden" id="settings_name" name="settings_name" value="<?php echo $settings_name; ?>"/>
		<input type="hidden" id="page_id" name="page_id" value="<?php echo $page_id; ?>"/>
		<input type="hidden" id="emaillist" name="emaillist" value="<?php echo $emaillist; ?>"/>
		<p><a href="https://support.seedprod.com/article/69-collecting-emails-with-sendy" target="_blank">Learn how to Configure</a></p>
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row">
						<strong>Sendy Url</strong>
					</th>
					<td>
						<input class="regular-text" type="textbox" id="sendy_url" name="sendy_url" value="<?php echo ( ! empty( $settings['sendy_url'] ) ) ? $settings['sendy_url'] : ''; ?>" />
						<br>
						<small class="description">The url to where your Sendy is installed. Example: http://your_sendy_installation</small>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<strong>List ID</strong>
					</th>
					<td>
						<input class="regular-text" type="textbox" id="sendy_list_id" name="sendy_list_id" value="<?php echo ( ! empty( $settings['sendy_list_id'] ) ) ? $settings['sendy_list_id'] : ''; ?>" />
						<br>
						<small class="description">The list id you want to subscribe a user to. This encrypted & hashed id can be found under View all lists section named ID in Sendy</small>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<strong>API Key</strong>
					</th>
					<td>
						<input class="regular-text" type="textbox" id="sendy_api_key" name="sendy_api_key" value="<?php echo ( ! empty( $settings['sendy_api_key'] ) ) ? $settings['sendy_api_key'] : ''; ?>" />
						<br>
						<small class="description">Available in Settings, in Sendy.</small>
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
 *  Subscribe Sendy
 */
add_action( 'seed_cspv5_legacy_emaillist_sendy', 'seed_cspv5_legacy_emaillist_sendy_add_subscriber' );

function seed_cspv5_legacy_emaillist_sendy_add_subscriber( $args ) {
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
	$url  = $sendy_url;
	$list = $sendy_list_id;
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

	$fullname = $fname . ' ' . $lname;

	$apikey = '';
	if ( ! empty( $sendy_api_key ) ) {
		$apikey = $sendy_api_key;
	}
	// Make Request
	$args = array(
		'timeout' => 45,
		'body'    => array(
			'name'    => $fullname,
			'email'   => $email,
			'list'    => $list,
			'api_key' => $apikey,
			'boolean' => 'true',
		),
	);
	if ( ! empty( $url ) && ! empty( $list ) ) {
		$r = wp_remote_post( trailingslashit( $url ) . 'subscribe', $args );
	}

	// Return results
	if ( is_wp_error( $r ) ) {
		$seed_cspv5_post_result['status'] = '500';
	} else {
		// if(!empty($enable_reflink)){
		//     seed_cspv5_legacy_emaillist_database_add_subscriber();
		// }
		$body = wp_remote_retrieve_body( $r );
		//var_dump($body);
		//die();
		if ( $body ) {
			$seed_cspv5_post_result['status'] = '200';
		} else {
			$seed_cspv5_post_result['status'] = $body;
		}
	}
}

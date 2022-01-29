<?php
//  * Copyright 2014 SEEDPROD LLC (email : john@seedprod.com, twitter : @seedprod)
/**
 *  Add InfusionSoft section
 */


function seed_cspv5_legacy_section_infusionsoft( $emaillist, $page_id ) {
	// Get settings
	$settings_name = 'seed_cspv5_' . $page_id . '_' . $emaillist;
	$settings      = get_option( $settings_name );
	if ( ! empty( $settings ) ) {
		$settings = maybe_unserialize( $settings );
	}
	ob_start();
	?>
	<div class="postbox">
		<h3 class="hndle"><?php _e( 'InfusionSoft', 'seedprod' ); ?></h3>
		<div class="inside">
		<p><?php __( 'Configure saving subscribers to InfusionSoft options. <a href="https://support.seedprod.com/article/72-collecting-emails-with-infusionsoft" target="_blank">Learn More</a>', 'seedprod' ); ?></p>
		<form id="seed_cspv5_emaillist_settings">
		<input type="hidden" id="settings_name" name="settings_name" value="<?php echo $settings_name; ?>"/>
		<input type="hidden" id="page_id" name="page_id" value="<?php echo $page_id; ?>"/>
		<input type="hidden" id="emaillist" name="emaillist" value="<?php echo $emaillist; ?>"/>
		<p><a href="https://support.seedprod.com/article/72-collecting-emails-with-infusionsoft" target="_blank">Learn how to Configure</a></p>
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row">
						<strong>App Name</strong>
					</th>
					<td>
						<input class="regular-text" type="textbox" id="infusionsoft_app" name="infusionsoft_app" value="<?php echo ( ! empty( $settings['infusionsoft_app'] ) ) ? $settings['infusionsoft_app'] : ''; ?>" />
						<br>
						<small class="description">Enter your app name.</small>
					</td>
				</tr>
				
								<tr valign="top">
					<th scope="row">
						<strong>API Key</strong>
					</th>
					<td>
						<input class="regular-text" type="textbox" id="infusionsoft_api_key" name="infusionsoft_api_key" value="<?php echo ( ! empty( $settings['infusionsoft_api_key'] ) ) ? $settings['infusionsoft_api_key'] : ''; ?>" />
						<br>
						<small class="description">Enter your api key. Learn how to <a href="http://help.infusionsoft.com/userguides/get-started/tips-and-tricks/api-key" target="_blank">generate your Infusionsoft api key</a>.</small>
					</td>
				</tr>
				
								<tr valign="top">
					<th scope="row">
						<strong>Tag ID's</strong>
					</th>
					<td>
						<input class="regular-text" type="textbox" id="infusionsoft_tag_id" name="infusionsoft_tag_id" value="<?php echo ( ! empty( $settings['infusionsoft_tag_id'] ) ) ? $settings['infusionsoft_tag_id'] : ''; ?>" />
						<br>
						<small class="description">Enter the Tag IDs seperated by commas. Tag IDs can be founds in Infusionsoft: Menu -> CRM -> Settings -> Tags</small>
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
 *  Subscribe InfusionSoft
 */
add_action( 'seed_cspv5_legacy_emaillist_infusionsoft', 'seed_cspv5_legacy_emaillist_infusionsoft_add_subscriber' );

function seed_cspv5_legacy_emaillist_infusionsoft_add_subscriber( $args ) {
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
	if ( ! class_exists( 'xmlrpc_client' ) ) {
		require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/backwards/extentions/infusionsoft/xmlrpc-2.0/lib/xmlrpc.inc';
	}

	// If tracking enabled
	if ( ! empty( $enable_reflink ) || ! empty( $display_optin_confirm ) ) {
		seed_cspv5_legacy_emaillist_database_add_subscriber( $args );
	}

	$app     = $infusionsoft_app;
	$api_key = $infusionsoft_api_key;
	$tag_id  = $infusionsoft_tag_id;

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

	###Set our Infusionsoft application as the client###
	$client = new xmlrpc_client( 'https://' . $app . '.infusionsoft.com/api/xmlrpc' );

	###Return Raw PHP Types###
	$client->return_type = 'phpvals';

	###Dont bother with certificate verification###
	$client->setSSLVerifyPeer( false );

	###Our API Key###
	$key = $api_key;

	###Build a Key-Value Array to store a contact###
	$contact = array(
		'FirstName' => $fname,
		'LastName'  => $lname,
		'Email'     => $email,
	);

	$optin_reason = 'Coming Soon Page';

	###Set up the call###
	$call  = new xmlrpcmsg(
		'ContactService.add',
		array(
			php_xmlrpc_encode( $key ),        #The encrypted API key
			php_xmlrpc_encode( $contact ),     #The contact array
		)
	);
	$call2 = new xmlrpcmsg(
		'APIEmailService.optIn',
		array(
			php_xmlrpc_encode( $key ),        #The encrypted API key
			php_xmlrpc_encode( $email ),     #The contact array
			php_xmlrpc_encode( $optin_reason ),     #The contact array
		)
	);

	###Send the call###
		$result  = $client->send( $call );
		$result2 = $client->send( $call2 );

	if ( ! empty( $tag_id ) ) {
		$tags = explode( ',', $tag_id );
		//var_dump($tags);
		foreach ( $tags as $t ) {
			$call3   = new xmlrpcmsg(
				'ContactService.addToGroup',
				array(
					php_xmlrpc_encode( $key ),        #The encrypted API key
					php_xmlrpc_encode( $result->value() ),     #The contact ID
					php_xmlrpc_encode( $t ),     #The Follow up sequence ID
				)
			);
			$result3 = $client->send( $call3 );
		}
		//var_dump($result3);
	}

	if ( isset( $_REQUEST['debug'] ) && $_REQUEST['debug'] == 'true' ) {
		var_dump( $result );
		var_dump( $result2 );
	}

	###Check the returned value to see if it was successful and set it to a variable/display the results###
	if ( ! $result->faultCode() ) {
		// if(!empty($enable_reflink)){
		//     seed_cspv5_legacy_emaillist_database_add_subscriber();
		// }
		if ( empty( $seed_cspv5_post_result['status'] ) ) {
			$seed_cspv5_post_result['status'] = '200';
		}
		// $conID = $result->value();
		// print "Contact added was " . $conID;
		// print "<BR>";
	} else {
		$seed_cspv5_post_result['status'] = '500';
		// print $result->faultCode() . "<BR>";
		// print $result->faultString() . "<BR>";die();
	}
}

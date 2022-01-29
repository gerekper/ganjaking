<?php
// * Copyright 2014 SEEDPROD LLC (email : john@seedprod.com, twitter : @seedprod)
/**
 *  Add iContact section
 */

function seed_cspv5_legacy_section_icontact( $emaillist, $page_id ) {
	// Get settings
	$settings_name = 'seed_cspv5_' . $page_id . '_' . $emaillist;
	$settings      = get_option( $settings_name );
	if ( ! empty( $settings ) ) {
		$settings = maybe_unserialize( $settings );
	}
	ob_start();
	?>
	<div class="postbox">
		<h3 class="hndle"><?php _e( 'iContact', 'seedprod' ); ?></h3>
		<div class="inside">
		<p><?php __( '<a href="https://app.icontact.com/icp/core/externallogin?sAppId=puD4TZWs2kKlKZLZZgD7IAUiqPSYPIvd" target="_blank">Authorize the App</a> and define the app password, then enter that information below. Save your username and password to load your list. <br><a href="https://support.seedprod.com/article/78-collecting-emails-with-icontact" target="_blank">Learn More</a>', 'seedprod' ); ?></p>
		<form id="seed_cspv5_emaillist_settings">
		<input type="hidden" id="settings_name" name="settings_name" value="<?php echo $settings_name; ?>"/>
		<input type="hidden" id="page_id" name="page_id" value="<?php echo $page_id; ?>"/>
		<input type="hidden" id="emaillist" name="emaillist" value="<?php echo $emaillist; ?>"/>
		<p><a href="https://support.seedprod.com/article/78-collecting-emails-with-icontact" target="_blank">Learn how to Configure</a></p>
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row">
						<strong>Username</strong>
					</th>
					<td>
					 <p><a href="https://app.icontact.com/icp/core/externallogin?sAppId=puD4TZWs2kKlKZLZZgD7IAUiqPSYPIvd" target="_blank">Authorize the App</a> and define the app password, then enter that information below. Save your username and password to load your list. <br><a href="http://support.seedprod.com/article/73-collecting-emails-with-icontact" target="_blank">Learn More</a></p>
						<input class="regular-text" type="textbox" id="icontact_username" name="icontact_username" value="<?php echo ( ! empty( $settings['icontact_username'] ) ) ? $settings['icontact_username'] : ''; ?>" />
						<br>
						<small class="description">Enter your Username.</small>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<strong>Password</strong>
					</th>
					<td>
						<input class="regular-text" type="textbox" id="icontact_password" name="icontact_password" value="<?php echo ( ! empty( $settings['icontact_password'] ) ) ? $settings['icontact_password'] : ''; ?>" />
						<br>
						<small class="description">Enter your Password.</small>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<strong><?php _e( 'List', 'seedprod' ); ?></strong>
					</th>
					<td>
						<?php
						$lists = maybe_unserialize( get_transient( "seed_cspv5_{$emaillist}_lists_{$page_id}" ) );
						seed_cspv5_select( 'icontact_listid', $lists, ( ! empty( $settings['icontact_listid'] ) ) ? $settings['icontact_listid'] : '' );
						?>
						<button id="get-lists" class="button-secondary">Refresh Lists</button>
						<br>
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
	
	jQuery( document ).ready(function($) {
		$( "#icontact_password" ).blur(function() {
		  if($( "#icontact_password" ).val() != ''){
				  $( "#get-lists" ).trigger( "click" );
		  }
		});
		
		$( "#get-lists" ).click(function(e) {
			e.preventDefault();
			if($( "#icontact_username" ).val() != '' && $( "#icontact_password" ).val() != ''){
			jQuery(this).prop( "disabled", true );
			jQuery(this).text( "Refreshing" );
			var data = $( '#seed_cspv5_emaillist_settings' ).serialize();
			$.get( get_list_url, data )
				.done(function( data ) {
					data = jQuery.parseJSON( data );
					  $('#icontact_listid').find('option').remove();
					$.each(data, function(i,v) {
						$("#icontact_listid").append($("<option />").val(i).text(v));
					});
				})
				.always(function() {
					jQuery("#get-lists").prop( "disabled", false );
					jQuery("#get-lists").text( "Refresh Lists" );
				});
			}
		});
	});
	</script>
	<?php

	return $output = ob_get_clean();

}



/**
 *  Get List from iContact
 */
function seed_cspv5_legacy_get_icontact_lists() {
	$icontact_username = $_REQUEST['icontact_username'];
	$icontact_password = $_REQUEST['icontact_password'];
	$page_id           = $_REQUEST['page_id'];
	$emaillist         = $_REQUEST['emaillist'];
	$lists             = array();

		require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/backwards/extentions/icontact/seed_cspv5_iContactApi.php';

	if ( ! isset( $pass ) && isset( $icontact_password ) ) {
		$pass = $icontact_password;
	}

	if ( ! isset( $username ) && isset( $icontact_username ) ) {
		$username = $icontact_username;
	}

	if ( ! empty( $pass ) && ! empty( $username ) ) {
		seed_cspv5_iContactApi::getInstance()->setConfig(
			array(
				'appId'       => 'puD4TZWs2kKlKZLZZgD7IAUiqPSYPIvd',
				'apiPassword' => $pass,
				'apiUsername' => $username,
			)
		);

		$oiContact = seed_cspv5_iContactApi::getInstance();

		$response = $oiContact->getLists();

	}

	if ( isset( $_REQUEST['debug'] ) && $_REQUEST['debug'] == 'true' ) {
		var_dump( $oiContact );
		var_dump( $response );
	}

	if ( empty( $response ) ) {
		$lists['false'] = __( 'No lists Found', 'seedprod' );
	} else {

		foreach ( $response as $k => $v ) {
			$lists[ $v->listId ] = $v->name;
		}
		if ( ! empty( $lists ) ) {
			set_transient( 'seed_cspv5_icontact_lists', serialize( $lists ), 86400 );
		}
	}

	return json_encode( $lists );
}


/**
 *  Subscribe iContact
 */
add_action( 'seed_cspv5_legacy_emaillist_icontact', 'seed_cspv5_legacy_emaillist_icontact_add_subscriber' );

function seed_cspv5_legacy_emaillist_icontact_add_subscriber( $args ) {
		global $seed_cspv5_post_result;

		// Page Settings
		$settings = $args['settings'];
		extract( $settings );

		//  Emaillist Settings
		$settings_name = 'seed_cspv5_' . $page_id . '_' . $emaillist;
		$e_settings    = get_option( $settings_name );
		$e_settings    = maybe_unserialize( $e_settings );
		extract( $e_settings );
	require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/backwards/extentions/icontact/seed_cspv5_iContactApi.php';
	require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/backwards/lib/nameparse.php';

	// If tracking enabled
	if ( ! empty( $enable_reflink ) || ! empty( $display_optin_confirm ) ) {
		seed_cspv5_legacy_emaillist_database_add_subscriber( $args );
	}

	$pass     = $icontact_password;
	$username = $icontact_username;
	$listId   = $icontact_listid;

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

	seed_cspv5_iContactApi::getInstance()->setConfig(
		array(
			'appId'       => 'puD4TZWs2kKlKZLZZgD7IAUiqPSYPIvd',
			'apiPassword' => $pass,
			'apiUsername' => $username,
		)
	);

	$oiContact = seed_cspv5_iContactApi::getInstance();

	$user = array(
		'email'     => $email,
		'firstName' => $fname,
		'lastName'  => $lname,
		'add_list'  => $listId,
	);

	$contact  = $oiContact->addContact( $email, $sStatus = 'normal', $sPrefix = null, $sFirstName = $fname, $sLastName = $lname );
	$response = $oiContact->subscribeContactToList( $contact->contactId, $listId, $sStatus = 'normal' );

	if ( isset( $_REQUEST['debug'] ) && $_REQUEST['debug'] == 'true' ) {
		var_dump( $oiContact );
		var_dump( $contact );
		var_dump( $response );
	}

	if ( empty( $response ) ) {
		$seed_cspv5_post_result['status'] = '500';
	} else {
		// if(!empty($enable_reflink)){
		//     seed_cspv5_legacy_emaillist_database_add_subscriber();
		// }
		if ( empty( $seed_cspv5_post_result['status'] ) ) {
			$seed_cspv5_post_result['status'] = '200';
		}
	}
}

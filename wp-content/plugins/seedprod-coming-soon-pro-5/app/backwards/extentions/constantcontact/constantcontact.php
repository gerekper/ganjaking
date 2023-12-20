<?php
/**
 *  Add ConstantContact section
 * Copyright 2014 SEEDPROD LLC (email : john@seedprod.com, twitter : @seedprod)
 */

function seed_cspv5_legacy_section_constantcontact( $emaillist, $page_id ) {
	// Get settings
	$settings_name = 'seed_cspv5_' . $page_id . '_' . $emaillist;
	$settings      = get_option( $settings_name );
	if ( ! empty( $settings ) ) {
		$settings = maybe_unserialize( $settings );
	}
	ob_start();
	?>
	<div class="postbox">
		<h3 class="hndle"><?php _e( 'ConstantContact', 'seedprod' ); ?></h3>
		<div class="inside">
		<p><?php __( 'Configure saving subscribers to Constant Contact options. Save after you enter your username and password to load your list. <a href="https://support.seedprod.com/article/73-collecting-emails-with-constant-contact" target="_blank">Learn More</a>', 'seedprod' ); ?></p>
		<form id="seed_cspv5_emaillist_settings">
		<input type="hidden" id="settings_name" name="settings_name" value="<?php echo $settings_name; ?>"/>
		<input type="hidden" id="page_id" name="page_id" value="<?php echo $page_id; ?>"/>
		<input type="hidden" id="emaillist" name="emaillist" value="<?php echo $emaillist; ?>"/>
		<p><a href="https://support.seedprod.com/article/73-collecting-emails-with-constant-contact" target="_blank">Learn how to Configure</a></p>
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row">
						<strong>Username</strong>
					</th>
					<td>
						<input class="regular-text" type="textbox" id="constantcontact_username" name="constantcontact_username" value="<?php echo ( ! empty( $settings['constantcontact_username'] ) ) ? $settings['constantcontact_username'] : ''; ?>" />
						<br>
						<small class="description">Enter your Username</small>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<strong>Password</strong>
					</th>
					<td>
						<input class="regular-text" type="textbox" id="constantcontact_password" name="constantcontact_password" value="<?php echo ( ! empty( $settings['constantcontact_password'] ) ) ? $settings['constantcontact_password'] : ''; ?>" />
						<br>
						<small class="description">Enter your Password</small>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<strong><?php _e( 'List', 'seedprod' ); ?></strong>
					</th>
					<td>
						<?php
						$lists = maybe_unserialize( get_transient( "seed_cspv5_{$emaillist}_lists_{$page_id}" ) );
						seed_cspv5_select( 'constantcontact_listid', $lists, ( ! empty( $settings['constantcontact_listid'] ) ) ? $settings['constantcontact_listid'] : '' );
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
		$( "#constantcontact_password" ).blur(function() {
		  if($( "#constantcontact_password" ).val() != ''){
				  $( "#get-lists" ).trigger( "click" );
		  }
		});
		
		$( "#get-lists" ).click(function(e) {
			e.preventDefault();
			if($( "#constantcontact_username" ).val() != '' && $( "#constantcontact_password" ).val() != ''){
			jQuery(this).prop( "disabled", true );
			jQuery(this).text( "Refreshing" );
			var data = $( '#seed_cspv5_emaillist_settings' ).serialize();
			$.get( get_list_url, data )
				.done(function( data ) {
					data = jQuery.parseJSON( data );
					  $('#constantcontact_listid').find('option').remove();
					$.each(data, function(i,v) {
						$("#constantcontact_listid").append($("<option />").val(i).text(v));
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
 *  Get List from ConstantContact
 */
function seed_cspv5_legacy_get_constantcontact_lists() {
	$constantcontact_username = $_REQUEST['constantcontact_username'];
	$constantcontact_password = $_REQUEST['constantcontact_password'];
	$page_id                  = $_REQUEST['page_id'];
	$emaillist                = $_REQUEST['emaillist'];
	$lists                    = array();

			//var_dump('miss');
	if ( class_exists( 'cc' ) ) {
		//trigger_error("Duplicate: Another Constant Contact client library is already in scope.", E_USER_WARNING);
	} else {
		require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/backwards/extentions/constantcontact/seed_cspv5_class.cc.php';
	}

	if ( ! isset( $username ) && isset( $constantcontact_username ) ) {
		$username = $constantcontact_username;
		$password = $constantcontact_password;
	}

	if ( empty( $username ) || empty( $password ) ) {
		return array();
	}

			$api = new cc( $username, $password );

			$response = $api->get_all_lists();

	if ( isset( $_REQUEST['debug'] ) && $_REQUEST['debug'] == 'true' ) {
		var_dump( $api );
		var_dump( $response );
	}

	if ( $response ) {
		foreach ( $response as $k => $v ) {
			$lists[ $v['id'] ] = $v['Name'];
		}
		if ( ! empty( $lists ) ) {
			set_transient( "seed_cspv5_{$emaillist}_lists_{$page_id}", serialize( $lists ) );
		}
	} else {
		$lists['false'] = __( 'Unable to load Constant Contact lists', 'seedprod' );
	}

		return json_encode( $lists );
}


/**
 *  Subscribe ConstantContact
 */
add_action( 'seed_cspv5_legacy_emaillist_constantcontact', 'seed_cspv5_legacy_emaillist_constantcontact_add_subscriber' );

function seed_cspv5_legacy_emaillist_constantcontact_add_subscriber( $args ) {
		global $seed_cspv5_post_result;

		// Page Settings
		$settings = $args['settings'];
		extract( $settings );

		//  Emaillist Settings
		$settings_name = 'seed_cspv5_' . $page_id . '_' . $emaillist;
		$e_settings    = get_option( $settings_name );
		$e_settings    = maybe_unserialize( $e_settings );
		extract( $e_settings );

	if ( class_exists( 'cc' ) ) {
		//trigger_error("Duplicate: Another Constant Contact client library is already in scope.", E_USER_WARNING);
	} else {
		require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/backwards/extentions/constantcontact/seed_cspv5_class.cc.php';
	}
				require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/backwards/lib/nameparse.php';

				// If tracking enabled
	if ( ! empty( $enable_reflink ) || ! empty( $display_optin_confirm ) ) {
		seed_cspv5_legacy_emaillist_database_add_subscriber( $args );
	}

				$username = $constantcontact_username;
				$password = $constantcontact_password;

				$api    = new cc( $username, $password );
				$listId = $constantcontact_listid;

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

				$contact_list = $listId;
				$extra_fields = array();

				// check if the contact exists
				$contact = $api->query_contacts( $email );
	if ( isset( $_REQUEST['debug'] ) && $_REQUEST['debug'] == 'true' ) {
		var_dump( $api );
		var_dump( $contact );
	}

				// uncomment this line if the user makes the action themselves
				$api->set_action_type( 'contact' );
				$extra_fields = array(
					'FirstName' => $fname,
					'LastName'  => $lname,
				);
				if ( $contact ) {
					$contact_ext = $api->get_contact( $contact['id'] );
					if ( in_array( $contact_list, $contact_ext['lists'] ) ) {
						$seed_cspv5_post_result['status']    = '409';
						$seed_cspv5_post_result['msg']       = $txt_already_subscribed_msg;
						$seed_cspv5_post_result['msg_class'] = 'alert-info';
					}
					$lists   = $contact_ext['lists'] + array( $contact_list );
					$updated = $api->update_contact( $contact['id'], $email, $lists, $extra_fields );
					if ( $updated ) {
						//$this->add_subscriber($email,$fname,$lname);
						if ( empty( $seed_cspv5_post_result['status'] ) ) {
							$seed_cspv5_post_result['status'] = '200';
						}
					} else {
						$seed_cspv5_post_result['status']    = '409';
						$seed_cspv5_post_result['msg']       = $txt_already_subscribed_msg;
						$seed_cspv5_post_result['msg_class'] = 'alert-info';
					};
				} else {
					$new_id = $api->create_contact( $email, $contact_list, $extra_fields );
					if ( $new_id ) {
						// if(!empty($enable_reflink)){
						//     seed_cspv5_legacy_emaillist_database_add_subscriber();
						// }
						if ( empty( $seed_cspv5_post_result['status'] ) ) {
							$seed_cspv5_post_result['status'] = '200';
						}
					} else {
						$seed_cspv5_post_result['status']    = '409';
						$seed_cspv5_post_result['msg']       = $txt_already_subscribed_msg;
						$seed_cspv5_post_result['msg_class'] = 'alert-info';
					};
				};
}

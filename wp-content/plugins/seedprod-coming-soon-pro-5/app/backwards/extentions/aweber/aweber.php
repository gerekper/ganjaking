<?php
// Copyright 2016 SEEDPROD LLC (email : john@seedprod.com, twitter : @seedprod)

/**
 *  Add Aweber section
 */

function seed_cspv5_legacy_section_aweber( $emaillist, $page_id ) {
	// Get settings
	$settings_name = 'seed_cspv5_' . $page_id . '_' . $emaillist;
	$settings      = get_option( $settings_name );
	if ( ! empty( $settings ) ) {
		$settings = maybe_unserialize( $settings );
	}
	ob_start();
	?>
	<div class="postbox">
		<h3 class="hndle"><?php _e( 'Aweber', 'seedprod' ); ?></h3>
		<div class="inside">
		<p><?php __( 'Configure saving subscribers to Aweber options. <a target="_blank" href="https://support.seedprod.com/article/71-collecting-emails-with-aweber">Learn More</a>', 'seedprod' ); ?></p>
		<form id="seed_cspv5_emaillist_settings">
		<input type="hidden" id="settings_name" name="settings_name" value="<?php echo $settings_name; ?>"/>
		<input type="hidden" id="page_id" name="page_id" value="<?php echo $page_id; ?>"/>
		<input type="hidden" id="emaillist" name="emaillist" value="<?php echo $emaillist; ?>"/>
		<p><a href="https://support.seedprod.com/article/71-collecting-emails-with-aweber" target="_blank">Learn how to Configure</a></p>
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row">
						<strong>Authorization Code</strong>
					</th>
					<td>
						<p><a href="https://auth.aweber.com/1.0/oauth/authorize_app/a662998e" target="_blank">Authorize App</a> &larr; Click the link to get you Authorization Code.</p>
						<textarea class="large-text" type="textbox" id="aweber_authorization_code" name="aweber_authorization_code"><?php echo ( ! empty( $settings['aweber_authorization_code'] ) ) ? $settings['aweber_authorization_code'] : ''; ?></textarea>
						<br>
						<small class="description"><?php _e( 'Paste in the Authorization Code you received when authorizing the app.', 'seedprod' ); ?></small>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<strong><?php _e( 'List', 'seedprod' ); ?></strong>
					</th>
					<td>
						<?php
						$lists = maybe_unserialize( get_transient( "seed_cspv5_{$emaillist}_lists_{$page_id}" ) );
						seed_cspv5_select( 'aweber_listid', $lists, ( ! empty( $settings['aweber_listid'] ) ) ? $settings['aweber_listid'] : '' );
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
		$( "#aweber_authorization_code" ).blur(function() {
		  if($( "#aweber_authorization_code" ).val() != ''){
				  $( "#get-lists" ).trigger( "click" );
		  }
		});
		
		$( "#get-lists" ).click(function(e) {
			e.preventDefault();
			if($( "#aweber_authorization_code" ).val() != ''){
			jQuery(this).prop( "disabled", true );
			jQuery(this).text( "Refreshing" );
			var data = $( '#seed_cspv5_emaillist_settings' ).serialize();
			$.get( get_list_url, data )
				.done(function( data ) {
					data = jQuery.parseJSON( data );
					  $('#aweber_listid').find('option').remove();
					$.each(data, function(i,v) {
						$("#aweber_listid").append($("<option />").val(i).text(v));
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
 *  Get List from Aweber
 */
function seed_cspv5_legacy_get_aweber_lists() {
	$aweber_authorization_code = $_REQUEST['aweber_authorization_code'];
	$page_id                   = $_REQUEST['page_id'];
	$emaillist                 = $_REQUEST['emaillist'];
	$lists                     = array();

			require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/backwards/extentions/aweber/aweber_api/aweber_api.php';

			$authorization_code = $aweber_authorization_code;
	if ( empty( $seed_cspv5_aweber_auth ) && ! empty( $authorization_code ) ) {
		try {
			$auth = AWeberAPI::getDataFromAweberID( $authorization_code );
			list($consumerKey, $consumerSecret, $accessKey, $accessSecret) = $auth;

			update_option(
				'seed_cspv5_aweber_auth_' . $page_id,
				array(
					'consumer_key'    => $consumerKey,
					'consumer_secret' => $consumerSecret,
					'access_key'      => $accessKey,
					'access_secret'   => $accessSecret,
				)
			);
			//echo '200';
		} catch ( AWeberAPIException $exc ) {
			//echo $exc;
		}
	} else {
		update_option( 'seed_cspv5_aweber_auth_' . $page_id, '' );
	}

			$aweber_auth = get_option( 'seed_cspv5_aweber_auth_' . $page_id );
	if ( ! empty( $aweber_auth ) ) {
		extract( $aweber_auth );
		$consumerKey    = $consumer_key;
		$consumerSecret = $consumer_secret;
	}

	if ( empty( $consumerKey ) || empty( $consumerSecret ) ) {
		return array();
	}

	try {
		$aweber  = new AWeberAPI( $consumerKey, $consumerSecret );
		$account = $aweber->getAccount( $access_key, $access_secret );
		if ( isset( $_REQUEST['debug'] ) && $_REQUEST['debug'] == 'true' ) {
			var_dump( $aweber );
			var_dump( $account );
		}
	} catch ( Exception $e ) {
	}

	foreach ( $account->lists as $list ) {
		$lists[ $list->id ] = $list->name;
	}

	if ( ! empty( $lists ) ) {
		set_transient( "seed_cspv5_{$emaillist}_lists_{$page_id}", serialize( $lists ) );
	} else {
		$lists['false'] = __( 'Unable to load Aweber lists', 'seedprod' );
	}

		return json_encode( $lists );
}

/**
 *  Subscribe Aweber
 */
add_action( 'seed_cspv5_legacy_emaillist_aweber', 'seed_cspv5_legacy_emaillist_aweber_add_subscriber' );

function seed_cspv5_legacy_emaillist_aweber_add_subscriber( $args ) {
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
		require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/backwards/extentions/aweber/aweber_api/aweber_api.php';

				// If tracking enabled
	if ( ! empty( $enable_reflink ) || ! empty( $display_optin_confirm ) ) {
		seed_cspv5_legacy_emaillist_database_add_subscriber( $args );
	}

				$aweber_auth = get_option( 'seed_cspv5_aweber_auth_' . $page_id );
				extract( $aweber_auth );

	if ( ! empty( $consumer_key ) ) {
		$consumerKey    = $consumer_key;
		$consumerSecret = $consumer_secret;
		$aweber         = new AWeberAPI( $consumerKey, $consumerSecret );

		$list_id = $aweber_listid;

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
	}

	try {
		$account    = $aweber->getAccount( $access_key, $access_secret );
		$account_id = $account->id;
		$listURL    = "/accounts/{$account_id}/lists/{$list_id}";
		$list       = $account->loadFromUrl( $listURL );

		# create a subscriber
		$params = array(
			'email'      => $email,
			'name'       => $fullname,
			'ip_address' => seed_cspv5_legacy_get_ip(),
			// 'ad_tracking' => 'coming_soon_pro',
			// 'last_followup_message_number_sent' => 1,
			// 'misc_notes' => 'my cool app',
			// 'name' => 'John Doe',
			// 'custom_fields' => array(
			//     'Car' => 'Ferrari 599 GTB Fiorano',
			//     'Color' => 'Red',
			// ),
		);
		$subscribers    = $list->subscribers;
		$new_subscriber = $subscribers->create( $params );

		if ( isset( $_REQUEST['debug'] ) && $_REQUEST['debug'] == 'true' ) {
			var_dump( $new_subscriber );
		}

		# success!
		//$this->add_subscriber($email,$fname,$lname);
		if ( empty( $seed_cspv5_post_result['status'] ) ) {
				$seed_cspv5_post_result['status'] = '200';
		}
	} catch ( AWeberAPIException $exc ) {
		if ( $exc->status == '400' ) {
			if ( empty( $seed_cspv5_post_result['msg'] ) ) {
				$seed_cspv5_post_result['status']    = '409';
				$seed_cspv5_post_result['msg']       = $txt_already_subscribed_msg;
				$seed_cspv5_post_result['msg_class'] = 'alert-danger';
			}
		} else {
			$seed_cspv5_post_result['status'] = '500';
			$seed_cspv5_post_result['html']   = $exc->message;
		}
		// var_dump($exc);
		// print "<h3>AWeberAPIException:</h3>";
		// print " <li> Type: $exc->type              <br>";
		// print " <li> Msg : $exc->message           <br>";
		// print " <li> Docs: $exc->documentation_url <br>";
		// print "<hr>";
	}
}

<?php
/**
 * Add CampaignMonitor section
 * Copyright 2014 SEEDPROD LLC (email : john@seedprod.com, twitter : @seedprod)
 */

function seed_cspv5_legacy_section_campaignmonitor( $emaillist, $page_id ) {
	// Get settings
	$settings_name = 'seed_cspv5_' . $page_id . '_' . $emaillist;
	$settings      = get_option( $settings_name );
	if ( ! empty( $settings ) ) {
		$settings = maybe_unserialize( $settings );
	}
	ob_start();
	?>
	<div class="postbox">
		<h3 class="hndle"><?php _e( 'CampaignMonitor', 'seedprod' ); ?></h3>
		<div class="inside">
		<p><?php __( 'Configure saving subscribers to Campaign Monitor options. Save your change after you enter your api key to load your client. Then save again after you select a client to load you list. <a href="https://support.seedprod.com/article/76-collecting-emails-with-campaign-monitor" target="_blank">Learn More</a>', 'seedprod' ); ?></p>
		<form id="seed_cspv5_emaillist_settings">
		<input type="hidden" id="settings_name" name="settings_name" value="<?php echo $settings_name; ?>"/>
		<input type="hidden" id="page_id" name="page_id" value="<?php echo $page_id; ?>"/>
		<input type="hidden" id="emaillist" name="emaillist" value="<?php echo $emaillist; ?>"/>
		<p><a href="https://support.seedprod.com/article/76-collecting-emails-with-campaign-monitor" target="_blank">Learn how to Configure</a></p>
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row">
						<strong>API Key</strong>
					</th>
					<td>
						<input class="regular-text" type="textbox" id="campaignmonitor_api_key" name="campaignmonitor_api_key" value="<?php echo ( ! empty( $settings['campaignmonitor_api_key'] ) ) ? $settings['campaignmonitor_api_key'] : ''; ?>" />
						<br>
						<small class="description">Enter your API Key. <a target="_blank" href="http://help.campaignmonitor.com/topic.aspx?t=206" target="_blank">Get your API key</a></small>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<strong>Client ID</strong>
					</th>
					<td>
						<input class="regular-text" type="textbox" id="campaignmonitor_client_id" name="campaignmonitor_client_id" value="<?php echo ( ! empty( $settings['campaignmonitor_client_id'] ) ) ? $settings['campaignmonitor_client_id'] : ''; ?>" />
						<br>
						<small class="description">Enter your API Key. <a target="_blank" href="http://help.campaignmonitor.com/topic.aspx?t=206" target="_blank">Get your API key</a></small>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<strong><?php _e( 'List', 'seedprod' ); ?></strong>
					</th>
					<td>
						<?php
						$lists = maybe_unserialize( get_transient( "seed_cspv5_{$emaillist}_lists_{$page_id}" ) );
						seed_cspv5_select( 'campaignmonitor_list_id', $lists, ( ! empty( $settings['campaignmonitor_list_id'] ) ) ? $settings['campaignmonitor_list_id'] : '' );
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
		$( "#campaignmonitor_client_id" ).blur(function() {
		  if($( "#campaignmonitor_client_id" ).val() != ''){
				  $( "#get-lists" ).trigger( "click" );
		  }
		});
		
		$( "#get-lists" ).click(function(e) {
			e.preventDefault();
			if($( "#campaignmonitor_api_key" ).val() != '' && $( "#campaignmonitor_client_id" ).val() != ''){
			jQuery(this).prop( "disabled", true );
			jQuery(this).text( "Refreshing" );
			var data = $( '#seed_cspv5_emaillist_settings' ).serialize();
			$.get( get_list_url, data )
				.done(function( data ) {
					data = jQuery.parseJSON( data );
					  $('#campaignmonitor_list_id').find('option').remove();
					$.each(data, function(i,v) {
						$("#campaignmonitor_list_id").append($("<option />").val(i).text(v));
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
 *  Get List from CampaignMonitor
 */
function seed_cspv5_legacy_get_campaignmonitor_lists( $apikey = null ) {
	$campaignmonitor_api_key   = $_REQUEST['campaignmonitor_api_key'];
	$campaignmonitor_client_id = $_REQUEST['campaignmonitor_client_id'];
	$page_id                   = $_REQUEST['page_id'];
	$emaillist                 = $_REQUEST['emaillist'];
	$lists                     = array();

	if ( class_exists( 'CS_REST_Clients' ) ) {
		//trigger_error("Duplicate: Another Campaign Moniter client library is already in scope.", E_USER_WARNING);
	} else {
		require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/backwards/extentions/campaignmonitor/campaign_monitor/csrest_clients.php';
	}

	if ( ! isset( $apikey ) && isset( $campaignmonitor_api_key ) ) {
		$apikey = $campaignmonitor_api_key;
	}
	if ( ! isset( $clientid ) && isset( $campaignmonitor_client_id ) ) {
		$clientid = $campaignmonitor_client_id;
	}

			$api = new CS_REST_Clients( $clientid, $apikey );

			$response = $api->get_lists();
	if ( isset( $_REQUEST['debug'] ) && $_REQUEST['debug'] == 'true' ) {
		var_dump( $api );
		var_dump( $response );
	}

	if ( $response->was_successful() ) {
		foreach ( $response->response as $k => $v ) {
			$lists[ $v->ListID ] = $v->Name;
		}
		if ( ! empty( $lists ) ) {
			set_transient( 'seed_cspv5_campaignmonitor_lists', serialize( $lists ), 86400 );
		}
	} else {
		$lists['false'] = __( 'Unable to load Campaign Monitor lists', 'seedprod' );
	}

		return json_encode( $lists );

}

	/**
	 *  Get List from Campaign Monitor
	 */
function cspv5_legacy_get_campaignmonitor_clients( $apikey = null ) {
	global $seed_cspv5;
	extract( $seed_cspv5 );
	$o       = $seed_cspv5;
	$clients = array();
	if ( $o['emaillist'] == 'campaignmonitor' || ( defined( 'DOING_AJAX' ) && DOING_AJAX && isset( $_GET['action'] ) && $_GET['action'] == 'seed_cspv5_campaingmonitor_client' ) ) {
		$clients = maybe_unserialize( get_transient( 'seed_cspv5_campaignmonitor_clients' ) );
		if ( empty( $clients ) ) {
			//var_dump('miss');
			if ( class_exists( 'CS_REST_General' ) ) {
				//trigger_error("Duplicate: Another Campaign Moniter client library is already in scope.", E_USER_WARNING);
			} else {
				require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/backwards/extentions/campaignmonitor/campaign_monitor/csrest_general.php';
			}

			if ( ! isset( $apikey ) && isset( $campaignmonitor_api_key ) ) {
				$apikey = $campaignmonitor_api_key;
			}

			if ( empty( $apikey ) ) {
				return array();
			}

			$api = new CS_REST_General( $apikey );

			$response = $api->get_clients();

			if ( isset( $_REQUEST['debug'] ) && $_REQUEST['debug'] == 'true' ) {
				var_dump( $api );
				var_dump( $response );
			}

			if ( $response->was_successful() ) {
				foreach ( $response->response as $k => $v ) {
					$clients[ $v->ClientID ] = $v->Name;
				}
				if ( ! empty( $clients ) ) {
					set_transient( "seed_cspv5_{$emaillist}_lists_{$page_id}", serialize( $clients ) );
				}
			} else {
				$clients['false'] = __( 'Unable to load Campaign Monitor clients', 'seedprod' );
			}
		}
	}
	return $clients;
}


/**
 *  Subscribe CampaignMonitor
 */
add_action( 'seed_cspv5_legacy_emaillist_campaignmonitor', 'seed_cspv5_legacy_emaillist_campaignmonitor_add_subscriber' );

function seed_cspv5_legacy_emaillist_campaignmonitor_add_subscriber( $args ) {
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
	if ( class_exists( 'CS_REST_Subscribers' ) ) {
		//trigger_error("Duplicate: Another Campaign Moniter client library is already in scope.", E_USER_WARNING);
	} else {
		require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/backwards/extentions/campaignmonitor/campaign_monitor/csrest_subscribers.php';
	}

				// If tracking enabled
	if ( ! empty( $enable_reflink ) || ! empty( $display_optin_confirm ) ) {
		seed_cspv5_legacy_emaillist_database_add_subscriber( $args );
	}

				$apikey = $campaignmonitor_api_key;
				$listid = $campaignmonitor_list_id;

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

				$api = new CS_REST_Subscribers( $listid, $apikey );

				$response = $api->add(
					array(
						'EmailAddress' => $email,
						'Name'         => $fname . ' ' . $lname,
						// 'CustomFields' => array(
						//     array(
						//         'Key' => 'Field Key',
						//         'Value' => 'Field Value'
						//     )
						// ),
						'Resubscribe'  => true,
					)
				);
				//var_dump($name);
				//var_dump($response);

	if ( $response->was_successful() ) {
		if ( empty( $seed_cspv5_post_result['status'] ) ) {
				$seed_cspv5_post_result['status'] = '200';
		}
	} else {
		$seed_cspv5_post_result['status'] = '500';
		$seed_cspv5_post_result['status'] = $txt_api_error_msg;
		$seed_cspv5_post_result['status'] = 'alert-danger';
	};
}

<?php
//  * Copyright 2014 SEEDPROD LLC (email : john@seedprod.com, twitter : @seedprod)
/**
 *  Add GetResponse section
 */

function seed_cspv5_legacy_section_getresponse( $emaillist, $page_id ) {
	// Get settings
	$settings_name = 'seed_cspv5_' . $page_id . '_' . $emaillist;
	$settings      = get_option( $settings_name );
	if ( ! empty( $settings ) ) {
		$settings = maybe_unserialize( $settings );
	}
	ob_start();
	?>
	<div class="postbox">
		<h3 class="hndle"><?php _e( 'GetResponse', 'seedprod' ); ?></h3>
		<div class="inside">
		<p><?php __( 'Configure saving subscribers to Get Response options. Save after you enter your api key to load your list. <a href="https://support.seedprod.com/article/74-collecting-emails-with-get-response" target="_blank">Learn More</a>', 'seedprod' ); ?></p>
		<form id="seed_cspv5_emaillist_settings">
		<input type="hidden" id="settings_name" name="settings_name" value="<?php echo $settings_name; ?>"/>
		<input type="hidden" id="page_id" name="page_id" value="<?php echo $page_id; ?>"/>
		<input type="hidden" id="emaillist" name="emaillist" value="<?php echo $emaillist; ?>"/>
		<p><a href="https://support.seedprod.com/article/74-collecting-emails-with-get-response" target="_blank">Learn how to Configure</a></p>
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row">
						<strong>API Key</strong>
					</th>
					<td>
						<input class="regular-text" type="textbox" id="getresponse_api_key" name="getresponse_api_key" value="<?php echo ( ! empty( $settings['getresponse_api_key'] ) ) ? $settings['getresponse_api_key'] : ''; ?>" />
						<br>
						<small class="description">Enter your API Key. <a target="_blank" href="https://app.getresponse.com/my_account.html" target="_blank">Get your API key</a></a></small>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<strong><?php _e( 'List', 'seedprod' ); ?></strong>
					</th>
					<td>
						<?php
						$lists = maybe_unserialize( get_transient( "seed_cspv5_{$emaillist}_lists_{$page_id}" ) );
						seed_cspv5_select( 'getresponse_listid', $lists, ( ! empty( $settings['getresponse_listid'] ) ) ? $settings['getresponse_listid'] : '' );
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
		$( "#getresponse_api_key" ).blur(function() {
		  if($( "#getresponse_api_key" ).val() != ''){
				  $( "#get-lists" ).trigger( "click" );
		  }
		});
		
		$( "#get-lists" ).click(function(e) {
			e.preventDefault();
			if($( "#getresponse_api_key" ).val() != ''){
			jQuery(this).prop( "disabled", true );
			jQuery(this).text( "Refreshing" );
			var data = $( '#seed_cspv5_emaillist_settings' ).serialize();
			$.get( get_list_url, data )
				.done(function( data ) {
					data = jQuery.parseJSON( data );
					  $('#getresponse_listid').find('option').remove();
					$.each(data, function(i,v) {
						$("#getresponse_listid").append($("<option />").val(i).text(v));
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
 *  Get List from GetResponse
 */
function seed_cspv5_legacy_get_getresponse_lists() {
	$getresponse_api_key = $_REQUEST['getresponse_api_key'];
	$page_id             = $_REQUEST['page_id'];
	$emaillist           = $_REQUEST['emaillist'];
	$lists               = array();

			require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/backwards/extentions/getresponse/seed_cspv5_GetResponseAPI.class.php';

	if ( ! isset( $apikey ) && isset( $getresponse_api_key ) ) {
		$apikey = $getresponse_api_key;
	}

	if ( empty( $apikey ) ) {
		return array();
	}

			$api = new seed_cspv5_GetResponse( $apikey );

			//$response = (array)$api->ping();

			$response = (array) $api->getCampaigns();

	if ( isset( $_REQUEST['debug'] ) && $_REQUEST['debug'] == 'true' ) {
		var_dump( $api );
		var_dump( $response );
	}

	if ( empty( $response ) ) {
		$lists['false'] = __( 'No lists Found', 'seedprod' );
		return $lists;
	} else {

		foreach ( $response as $k => $v ) {
			$lists[ $k ] = $v->name;
		}
		if ( ! empty( $lists ) ) {
			set_transient( "seed_cspv5_{$emaillist}_lists_{$page_id}", serialize( $lists ), 86400 );
		}
	}

		return json_encode( $lists );

}


/**
 *  Subscribe GetResponse
 */
add_action( 'seed_cspv5_legacy_emaillist_getresponse', 'seed_cspv5_legacy_emaillist_getresponse_add_subscriber' );

function seed_cspv5_legacy_emaillist_getresponse_add_subscriber( $args ) {
		global $seed_cspv5_post_result;

		// Page Settings
		$settings = $args['settings'];
		extract( $settings );

		//  Emaillist Settings
		$settings_name = 'seed_cspv5_' . $page_id . '_' . $emaillist;
		$e_settings    = get_option( $settings_name );
		$e_settings    = maybe_unserialize( $e_settings );
		extract( $e_settings );

				require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/backwards/extentions/getresponse/seed_cspv5_GetResponseAPI.class.php';
				require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/backwards/lib/nameparse.php';

				// If tracking enabled
	if ( ! empty( $enable_reflink ) || ! empty( $display_optin_confirm ) ) {
		seed_cspv5_legacy_emaillist_database_add_subscriber( $args );
	}

				$apikey = $getresponse_api_key;
				$api    = new seed_cspv5_GetResponse( $apikey );
				$listId = $getresponse_listid;

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

				$response = $api->addContact( $listId, $fullname, $email );

	if ( isset( $_REQUEST['debug'] ) && $_REQUEST['debug'] == 'true' ) {
		var_dump( $api );
		var_dump( $response );
	}

				//var_dump( $response);
	if ( empty( $response ) ) {
		$seed_cspv5_post_result['status'] = '500';
		$seed_cspv5_post_result['status'] = $txt_api_error_msg;
		$seed_cspv5_post_result['status'] = 'alert-danger';
	} else {
		// if(!empty($enable_reflink)){
		//     seed_cspv5_legacy_emaillist_database_add_subscriber();
		// }
		if ( empty( $seed_cspv5_post_result['status'] ) ) {
			$seed_cspv5_post_result['status'] = '200';
		}
	}
}



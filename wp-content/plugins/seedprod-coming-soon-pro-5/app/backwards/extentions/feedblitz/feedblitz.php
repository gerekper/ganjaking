<?php
//  * Copyright 2014 SEEDPROD LLC (email : john@seedprod.com, twitter : @seedprod)

/**
 *  Add Feedblitz section
 */

function seed_cspv5_legacy_section_feedblitz( $emaillist, $page_id ) {
	// Get settings
	$settings_name = 'seed_cspv5_' . $page_id . '_' . $emaillist;
	$settings      = get_option( $settings_name );
	if ( ! empty( $settings ) ) {
		$settings = maybe_unserialize( $settings );
	}
	ob_start();
	?>
	<div class="postbox">
		<h3 class="hndle"><?php _e( 'FeedBlitz', 'seedprod' ); ?></h3>
		<div class="inside">
		<p><?php __( 'Configure saving subscribers to Feedblitz options. Save after you enter your api key to load your list.', 'seedprod' ); ?></p>
		<form id="seed_cspv5_emaillist_settings">
		<input type="hidden" id="settings_name" name="settings_name" value="<?php echo $settings_name; ?>"/>
		<input type="hidden" id="page_id" name="page_id" value="<?php echo $page_id; ?>"/>
		<input type="hidden" id="emaillist" name="emaillist" value="<?php echo $emaillist; ?>"/>
		<p><a href="https://support.seedprod.com/article/83-collecting-emails-with-feedblitz" target="_blank">Learn how to Configure</a></p>
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row">
						<strong>API Key</strong>
					</th>
					<td>
						<input class="regular-text" type="textbox" id="feedblitz_api_key" name="feedblitz_api_key" value="<?php echo ( ! empty( $settings['feedblitz_api_key'] ) ) ? $settings['feedblitz_api_key'] : ''; ?>" />
						<br>
						<small class="description">Enter your API Key. <a target="_blank" href="http://support.feedblitz.com/customer/portal/articles/874021-how-do-i-get-an-api-key-" target="_blank">Get your API key</a></small>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<strong><?php _e( 'List', 'seedprod' ); ?></strong>
					</th>
					<td>
						<?php
						$lists = maybe_unserialize( get_transient( "seed_cspv5_{$emaillist}_lists_{$page_id}" ) );
						seed_cspv5_select( 'feedblitz_listid', $lists, ( ! empty( $settings['feedblitz_listid'] ) ) ? $settings['feedblitz_listid'] : '' );
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
		$( "#feedblitz_api_key" ).blur(function() {
		  if($( "#feedblitz_api_key" ).val() != ''){
				  $( "#get-lists" ).trigger( "click" );
		  }
		});
		
		$( "#get-lists" ).click(function(e) {
			e.preventDefault();
			if($( "#feedblitz_api_key" ).val() != ''){
			jQuery(this).prop( "disabled", true );
			jQuery(this).text( "Refreshing" );
			var data = $( '#seed_cspv5_emaillist_settings' ).serialize();
			$.get( get_list_url, data )
				.done(function( data ) {
					data = jQuery.parseJSON( data );
					  $('#feedblitz_listid').find('option').remove();
					$.each(data, function(i,v) {
						$("#feedblitz_listid").append($("<option />").val(i).text(v));
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
 *  Get List from Feedblitz
 */
function seed_cspv5_legacy_get_feedblitz_lists() {
	$feedblitz_api_key = $_REQUEST['feedblitz_api_key'];
	$page_id           = $_REQUEST['page_id'];
	$emaillist         = $_REQUEST['emaillist'];
	$lists             = array();

		//var_dump('miss');

	if ( ! isset( $apikey ) && isset( $feedblitz_api_key ) ) {
		$apikey = $feedblitz_api_key;
	}

	if ( empty( $apikey ) ) {
		return array();
	}

		$url = 'https://www.feedblitz.com/f.api/syndications?summary=1&key=' . $apikey;

		$response   = wp_remote_get( $url );
		$xml_string = wp_remote_retrieve_body( $response, true );
		$xml        = simplexml_load_string( $xml_string );
		$json       = json_encode( $xml );
		$api        = json_decode( $json, true );

	if ( isset( $_REQUEST['debug'] ) && $_REQUEST['debug'] == 'true' ) {
		var_dump( $api );
		var_dump( $response );
	}
	if ( $api['syndications']['count'] == 0 ) {
		$lists['false'] = __( 'No lists Found', 'seedprod' );
		return $lists;
	}
	if ( empty( $api['syndications'] ) ) {
		$lists['false'] = __( 'Unable to load Feedblitz lists, check your API Key.', 'seedprod' );
	} else {
		if ( $api['syndications']['count'] == 1 ) {
			$lists[ $api['syndications']['syndication']['id'] ] = $api['syndications']['syndication']['name'];
		} else {
			foreach ( $api['syndications']['syndication'] as $k => $v ) {
				$lists[ $v['id'] ] = $v['name'];
			}
		}

		if ( ! empty( $lists ) ) {
			set_transient( "seed_cspv5_{$emaillist}_lists_{$page_id}", serialize( $lists ) );
		}
	}

	return json_encode( $lists );
}


/**
 *  Subscribe Feedblitz
 */
add_action( 'seed_cspv5_legacy_emaillist_feedblitz', 'seed_cspv5_legacy_emaillist_feedblitz_add_subscriber' );

function seed_cspv5_legacy_emaillist_feedblitz_add_subscriber( $args ) {
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

			$apikey = $feedblitz_api_key;
			$listId = $feedblitz_listid;

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

			$url = "https://www.feedblitz.com/f/?SimpleApiSubscribe&email=$email&listid=$listId&key=$apikey";

			$fargs      = array(
				'timeout' => 10,
			);
			$response   = wp_remote_get( $url, $fargs );
			$xml_string = wp_remote_retrieve_body( $response, true );
			$xml        = simplexml_load_string( $xml_string );
			$json       = json_encode( $xml );
			$api        = json_decode( $json, true );
			if ( isset( $_REQUEST['debug'] ) && $_REQUEST['debug'] == 'true' ) {
				var_dump( $api );
				var_dump( $response );
			}

			if ( ! empty( $api['subscriberid'] ) ) {
				$seed_cspv5_post_result['status'] = '200';
			} else {
				 $seed_cspv5_post_result['msg']       = '500';
				 $seed_cspv5_post_result['msg_class'] = 'alert-danger';
			}
}

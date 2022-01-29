<?php
//  * Copyright 2016 SEEDPROD LLC (email : john@seedprod.com, twitter : @seedprod)

/**
 *  Add drip section to admin
 */

function seed_cspv5_legacy_section_drip( $emaillist, $page_id ) {

	// Get settings
	$settings_name = 'seed_cspv5_' . $page_id . '_' . $emaillist;
	$settings      = get_option( $settings_name );
	if ( ! empty( $settings ) ) {
		$settings = maybe_unserialize( $settings );
	}
	ob_start();
	?>
	<div class="postbox">
		<h3 class="hndle"><?php _e( 'Drip', 'seedprod' ); ?></h3>
		<div class="inside">
		<p><?php __( 'Configure saving subscribers to Drip options.', 'seedprod' ); ?></p>
		<form id="seed_cspv5_emaillist_settings">
		<input type="hidden" id="settings_name" name="settings_name" value="<?php echo $settings_name; ?>"/>
		<input type="hidden" id="page_id" name="page_id" value="<?php echo $page_id; ?>"/>
		<input type="hidden" id="emaillist" name="emaillist" value="<?php echo $emaillist; ?>"/>
		<p><a href="https://support.seedprod.com/article/20-collecting-email-with-drip" target="_blank">Learn how to Configure</a></p>
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row">
						<strong>API Token</strong>
					</th>
					<td>
						<input class="regular-text" type="textbox" id="drip_api_key" name="drip_api_key" value="<?php echo ( ! empty( $settings['drip_api_key'] ) ) ? $settings['drip_api_key'] : ''; ?>" />
						<br>
						<small class="description">Enter your <a target="_blank" href="http://kb.getdrip.com/general/where-can-i-find-my-api-token/">API Token</a>. </small>
					</td>
				</tr>
				
				<tr valign="top">
					<th scope="row">
						<strong>Account ID</strong>
					</th>
					<td>
						<input class="regular-text" type="textbox" id="drip_account_id" name="drip_account_id" value="<?php echo ( ! empty( $settings['drip_account_id'] ) ) ? $settings['drip_account_id'] : ''; ?>" />
						<br>
						<small class="description">Enter your Account ID.</small>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<strong><?php _e( 'Campaigns', 'seedprod' ); ?></strong>
					</th>
					<td>
						<?php
						$lists = maybe_unserialize( get_transient( "seed_cspv5_{$emaillist}_lists_{$page_id}" ) );
						seed_cspv5_select( 'drip_listid', $lists, ( ! empty( $settings['drip_listid'] ) ) ? $settings['drip_listid'] : '' );
						?>
						<button id="get-lists" class="button-secondary">Refresh Lists</button>
						<br>

					</td>
				</tr>

				<tr valign="top">
					<th scope="row">
						<strong><?php _e( 'Enable Campaign Double Opt-In', 'seedprod' ); ?></strong>
					</th>
					<td>
						<input class="" type="checkbox" id="drip_enable_double_optin" name="drip_enable_double_optin" value="1" <?php echo ( ! empty( $settings['drip_enable_double_optin'] ) ) ? 'checked' : ''; ?>>
						<br>
						<small class="description"></small>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row">
						<strong>Tags</strong>
					</th>
					<td>
						<input class="regular-text" type="textbox" id="drip_tags" name="drip_tags" value="<?php echo ( ! empty( $settings['drip_tags'] ) ) ? $settings['drip_tags'] : ''; ?>" />
						<br>
						<small class="description">Enter any tags to apply, separate multiple tags with a comma.</small>
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
	$return_url = preg_replace( '/seed_cspv5_customize=\d*(.)*/', 'seed_cspv5_customize=' . $page_id . '&tab=form', urldecode( $_GET['return'] ) );
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
		$( "#drip_account_id" ).blur(function() {
		  if($( "#drip_account_id" ).val() != ''){
				  $( "#get-lists" ).trigger( "click" );
		  }
		});
		
		$( "#get-lists" ).click(function(e) {
			e.preventDefault();
			if($( "#drip_api_key" ).val() != '' && $( "#drip_account_id" ).val() != ''){
			jQuery(this).prop( "disabled", true );
			jQuery(this).text( "Refreshing" );
			var data = $( '#seed_cspv5_emaillist_settings' ).serialize();
			$.get( get_list_url, data )
				.done(function( data ) {
					data = jQuery.parseJSON( data );
					  $('#drip_listid').find('option').remove();
					$.each(data, function(i,v) {
						$("#drip_listid").append($("<option />").val(i).text(v));
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
 *  Get List from Drip
 */
function seed_cspv5_legacy_get_drip_lists() {
	$drip_api_key    = $_REQUEST['drip_api_key'];
	$drip_account_id = $_REQUEST['drip_account_id'];
	$page_id         = $_REQUEST['page_id'];
	$emaillist       = $_REQUEST['emaillist'];
	$lists           = array();

	if ( ! isset( $apikey ) && isset( $drip_api_key ) ) {
		$apikey = $drip_api_key;
	}

	if ( empty( $apikey ) ) {
		return array();
	}

			$args = array(
				'headers' => array(
					'Authorization' => 'Basic ' . base64_encode( $apikey . ':' . '' ),
				),
			);
			$url  = "https://api.getdrip.com/v2/$drip_account_id/campaigns";

			$response = wp_remote_get( $url, $args );
			if ( isset( $_REQUEST['debug'] ) && $_REQUEST['debug'] == 'true' ) {
				var_dump( $response );
			}
			$body = json_decode( wp_remote_retrieve_body( $response ) );

			if ( ! empty( $body->campaigns ) ) {
				$lists[0] = 'Select a Campaign';
				foreach ( $body->campaigns as $k => $v ) {
					$lists[ $v->id ] = $v->name;
				}
				if ( ! empty( $lists ) ) {
					set_transient( "seed_cspv5_{$emaillist}_lists_{$page_id}", serialize( $lists ) );
				}
			}

			return json_encode( $lists );
}


/**
 *  Subscribe MailChimp
 */
add_action( 'seed_cspv5_legacy_emaillist_drip', 'seed_cspv5_legacy_emaillist_drip_add_subscriber' );

function seed_cspv5_legacy_emaillist_drip_add_subscriber( $args ) {
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

	$apikey = $drip_api_key;
	//$drip_account_id;
	$listId = $drip_listid;

	$name = '';
	if ( ! empty( $_REQUEST['name'] ) ) {
		$name = $_REQUEST['name'];
	}
	$email         = $_REQUEST['email'];
	$fname         = '';
	$lname         = '';
	$custom_fields = array();
	$tags          = array();

	if ( ! empty( $drip_tags ) ) {
		$tags = explode( ',', $drip_tags );
	}

	if ( ! empty( $name ) ) {
		$name          = seed_cspv5_parse_name( $name );
		$fname         = $name['first'];
		$lname         = $name['last'];
		$custom_fields = array(
			'first_name' => $fname,
			'last_name'  => $lname,
		);
	}

	// Add subscriber

	$postData = array(
		'subscribers' => array(
			array(
				'email'         => $email,
				'custom_fields' => $custom_fields,
				'tags'          => $tags,
			),
		),
	);

	$postData = json_encode( $postData );

	$args = array(
		'body'    => $postData,
		'headers' => array(
			'Authorization' => 'Basic ' . base64_encode( $apikey . ':' . '' ),
			'Content-Type'  => 'application/vnd.api+json',
		),
	);
	$url  = "https://api.getdrip.com/v2/$drip_account_id/subscribers";

	$response = wp_remote_post( $url, $args );

	$body1 = json_decode( wp_remote_retrieve_body( $response ) );

	if ( ! empty( $drip_enable_double_optin ) ) {
		$drip_double_optin = true;
	} else {
		$drip_double_optin = false;
	}

	// Add to campaign
	if ( ! empty( $listId ) ) {
		$postData = array(
			'subscribers' => array(
				array(
					'email'        => $email,
					'double_optin' => $drip_double_optin,
				),
			),
		);

		$postData = json_encode( $postData );

		$args = array(
			'body'    => $postData,
			'headers' => array(
				'Authorization' => 'Basic ' . base64_encode( $apikey . ':' . '' ),
				'Content-Type'  => 'application/vnd.api+json',
			),
		);
		$url  = "https://api.getdrip.com/v2/$drip_account_id/campaigns/$listId/subscribers";

		$response = wp_remote_post( $url, $args );
		if ( isset( $_REQUEST['debug'] ) && $_REQUEST['debug'] == 'true' ) {
			var_dump( $response );
		}
		//var_dump( $response );
		$body2 = json_decode( wp_remote_retrieve_body( $response ) );
		//var_dump( $body2 );
	}

	// Response

	if ( $seed_cspv5_post_result['status'] == '409' ) {
		$seed_cspv5_post_result['status'] = '200';
	}

	if ( empty( $seed_cspv5_post_result['status'] ) ) {
		$seed_cspv5_post_result['status'] = '200';
	}

}


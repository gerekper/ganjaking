<?php
//  * Copyright 2016 SEEDPROD LLC (email : john@seedprod.com, twitter : @seedprod)

/**
 *  Add activecampaign section
 */

add_filter( 'seedredux/options/seed_cspv5/sections', 'seed_cspv5_activecampaign_section' );


function seed_cspv5_legacy_section_activecampaign( $emaillist, $page_id ) {
	// Get settings
	$settings_name = 'seed_cspv5_' . $page_id . '_' . $emaillist;
	$settings      = get_option( $settings_name );
	if ( ! empty( $settings ) ) {
		$settings = maybe_unserialize( $settings );
	}
	ob_start();
	?>
	<div class="postbox">
		<h3 class="hndle"><?php _e( 'Active Campaign', 'seedprod' ); ?></h3>
		<div class="inside">
		<p><?php __( 'Configure saving subscribers to Get Response options. Save after you enter your api key to load your list. <a href="https://support.seedprod.com/article/84-collecting-emails-with-activecampaign" target="_blank">Learn More</a>', 'seedprod' ); ?></p>
		<form id="seed_cspv5_emaillist_settings">
		<input type="hidden" id="settings_name" name="settings_name" value="<?php echo $settings_name; ?>"/>
		<input type="hidden" id="page_id" name="page_id" value="<?php echo $page_id; ?>"/>
		<input type="hidden" id="emaillist" name="emaillist" value="<?php echo $emaillist; ?>"/>
		<p><a href="https://support.seedprod.com/article/84-collecting-emails-with-activecampaign" target="_blank">Learn How To Configure</a></p>
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row">
						<strong><?php _e( 'API Url', 'seedprod' ); ?></strong>
					</th>
					<td>
						<input class="regular-text" type="textbox" id="activecampaign_api_url" name="activecampaign_api_url" value="<?php echo ( ! empty( $settings['activecampaign_api_url'] ) ) ? $settings['activecampaign_api_url'] : ''; ?>" />
						<br>
						<small class="description">Enter your API Url.</small>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<strong>API Key</strong>
					</th>
					<td>
						<input class="regular-text" type="textbox" id="activecampaign_api_key" name="activecampaign_api_key" value="<?php echo ( ! empty( $settings['activecampaign_api_key'] ) ) ? $settings['activecampaign_api_key'] : ''; ?>" />
						<br>
						<small class="description">Enter your API Key.</small>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<strong><?php _e( 'List', 'seedprod' ); ?></strong>
					</th>
					<td>
						<?php
						$lists = maybe_unserialize( get_transient( "seed_cspv5_{$emaillist}_lists_{$page_id}" ) );
						seed_cspv5_select( 'activecampaign_listid', $lists, ( ! empty( $settings['activecampaign_listid'] ) ) ? $settings['activecampaign_listid'] : '' );
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
		$( "#activecampaign_api_key" ).blur(function() {
		  if($( "#activecampaign_api_key" ).val() != ''){
				  $( "#get-lists" ).trigger( "click" );
		  }
		});
		
		$( "#get-lists" ).click(function(e) {
			e.preventDefault();
			if($( "#activecampaign_api_key" ).val() != '' && $( "#activecampaign_api_url" ).val() != ''){
			jQuery(this).prop( "disabled", true );
			jQuery(this).text( "Refreshing" );
			var data = $( '#seed_cspv5_emaillist_settings' ).serialize();
			$.get( get_list_url, data )
				.done(function( data ) {
					jQuery("#get-lists").prop( "disabled", false );
					jQuery("#get-lists").text( "Refresh Lists" );
					data = jQuery.parseJSON( data );
					  $('#activecampaign_listid').find('option').remove();
					$.each(data, function(i,v) {
						$("#activecampaign_listid").append($("<option />").val(i).text(v));
					});
				})
				.always(function() {
					//console.log('always');
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
 *  Get List from activecampaign
 */
function seed_cspv5_legacy_get_activecampaign_lists() {
		$activecampaign_api_url = $_REQUEST['activecampaign_api_url'];
		$activecampaign_api_key = $_REQUEST['activecampaign_api_key'];
		$page_id                = $_REQUEST['page_id'];
		$emaillist              = $_REQUEST['emaillist'];
		$lists                  = array();

		// Make request
		$url = $activecampaign_api_url . '/admin/api.php?api_action=list_list&api_key=' . $activecampaign_api_key . '&ids=all&api_output=json';
	if ( isset( $_REQUEST['debug'] ) && $_REQUEST['debug'] == 'true' ) {
		var_dump( $url );
	}

		$response = wp_remote_get( $url );
	if ( isset( $_REQUEST['debug'] ) && $_REQUEST['debug'] == 'true' ) {
		var_dump( $response );
	}

	if ( is_wp_error( $response ) ) {
		if ( isset( $_REQUEST['debug'] ) && $_REQUEST['debug'] == 'true' ) {
			var_dump( $response );
		}
	}
		$response = wp_remote_retrieve_body( $response );
	if ( is_wp_error( $response ) ) {
		if ( isset( $_REQUEST['debug'] ) && $_REQUEST['debug'] == 'true' ) {
			var_dump( $response );
		}
	}

	if ( ! empty( $response ) ) {
		$response = json_decode( $response );
	}

	if ( $response->result_code == 0 ) {
		$lists['false'] = __( 'No lists Found', 'seedprod' );
	} else {
		foreach ( $response as $k => $v ) {
			if ( is_numeric( $k ) ) {
				$lists[ $v->id ] = $v->name;
			}
		}
		if ( ! empty( $lists ) ) {
			set_transient( "seed_cspv5_{$emaillist}_lists_{$page_id}", serialize( $lists ) );
		}
	}

		return json_encode( $lists );

}


/**
 *  Subscribe activecampaign
 */
add_action( 'seed_cspv5_legacy_emaillist_activecampaign', 'seed_cspv5_legacy_emaillist_activecampaign_add_subscriber' );

function seed_cspv5_legacy_emaillist_activecampaign_add_subscriber( $args ) {
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
	if ( ! empty( $enable_reflink ) ) {
		seed_cspv5_legacy_emaillist_database_add_subscriber( $args );
	}

				$apikey = $activecampaign_api_key;
				$apiurl = $activecampaign_api_url;
				$listId = $activecampaign_listid;
				//var_dump($listId);

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

				// Make Request

				$url      = $apiurl . '/admin/api.php?api_action=contact_add&api_key=' . $apikey . '&email=' . urlencode( $email ) . '=all&api_output=json';
				$response = wp_remote_post(
					$url,
					array(
						'method'      => 'POST',
						'timeout'     => 45,
						'redirection' => 5,
						'httpversion' => '1.0',
						'blocking'    => true,
						'headers'     => array(),
						'body'        => array(
							'first_name'        => $fname,
							'last_name'         => $lname,
							'email'             => $email,
							'p'                 => array( $listId => $listId ),
							'instantresponders' => array( $listId => 1 ),
							'status'            => array( $listId => 1 ),
						),
						'cookies'     => array(),
					)
				);

	if ( isset( $_REQUEST['debug'] ) && $_REQUEST['debug'] == 'true' ) {
		var_dump( $response );
	}

	if ( is_wp_error( $response ) ) {
		return;
	}
				$response = wp_remote_retrieve_body( $response );
	if ( is_wp_error( $response ) ) {
		return;
	}
				$response = json_decode( $response );

	if ( $response->result_code == '0' ) {
		//var_dump( $response);
		$seed_cspv5_post_result['status']    = '409';
		$seed_cspv5_post_result['msg']       = $txt_already_subscribed_msg;
		$seed_cspv5_post_result['msg_class'] = 'alert-danger';
	} else {
		if ( empty( $seed_cspv5_post_result['status'] ) ) {
			$seed_cspv5_post_result['status'] = '200';
		}
	}
}

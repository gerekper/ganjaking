<?php
//  * Copyright 2014 SEEDPROD LLC (email : john@seedprod.com, twitter : @seedprod)

/**
 *  Add convertkit section
 */

function seed_cspv5_legacy_section_convertkit( $emaillist, $page_id ) {
	// Get settings
	$settings_name = 'seed_cspv5_' . $page_id . '_' . $emaillist;
	$settings      = get_option( $settings_name );
	if ( ! empty( $settings ) ) {
		$settings = maybe_unserialize( $settings );
	}
	ob_start();
	?>
	<div class="postbox">
		<h3 class="hndle"><?php _e( 'ConvertKit', 'seedprod' ); ?></h3>
		<div class="inside">
		<p><?php __( 'Configure saving subscribers to MailChimp options. Save after you enter your api key to load your list. <a href="#">Learn More</a>', 'seedprod' ); ?></p>
		<form id="seed_cspv5_emaillist_settings">
		<input type="hidden" id="settings_name" name="settings_name" value="<?php echo $settings_name; ?>"/>
		<input type="hidden" id="page_id" name="page_id" value="<?php echo $page_id; ?>"/>
		<input type="hidden" id="emaillist" name="emaillist" value="<?php echo $emaillist; ?>"/>
		<p><a href="http://support.seedprod.com/article/121-collecting-emails-with-convert-kit" target="_blank">Learn how to Configure</a></p>
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row">
						<strong>API Key</strong>
					</th>
					<td>
						<input class="regular-text" type="textbox" id="convertkit_api_key" name="convertkit_api_key" value="<?php echo ( ! empty( $settings['convertkit_api_key'] ) ) ? $settings['convertkit_api_key'] : ''; ?>" />
						<br>
						<small class="description">Enter your API Key. <a target="_blank" href="https://app.convertkit.com/account/edit" target="_blank">Get your API key</a></small>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<strong><?php _e( 'Forms', 'seedprod' ); ?></strong>
					</th>
					<td>
						<?php
						$lists = maybe_unserialize( get_transient( "seed_cspv5_{$emaillist}_lists_forms_{$page_id}" ) );
						if ( empty( $lists ) ) {
							$lists = array();
						}
						$lists = array( '0' => 'Select a Form' ) + $lists;
						seed_cspv5_select( 'convertkit_form_listid', $lists, ( ! empty( $settings['convertkit_form_listid'] ) ) ? $settings['convertkit_form_listid'] : '' );
						?>
						<button  class="get-lists button-secondary">Refresh Lists</button>
						<br>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<strong><?php _e( 'Sequences / Courses', 'seedprod' ); ?></strong>
					</th>
					<td>
						<?php
						$lists = maybe_unserialize( get_transient( "seed_cspv5_{$emaillist}_lists_{$page_id}" ) );
						if ( empty( $lists ) ) {
							$lists = array();
						}
						$lists = array( '0' => 'Select a Course' ) + $lists;
						seed_cspv5_select( 'convertkit_listid', $lists, ( ! empty( $settings['convertkit_listid'] ) ) ? $settings['convertkit_listid'] : '' );
						?>
						<button  class="get-lists button-secondary">Refresh Lists</button>
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
		$( "#convertkit_api_key" ).blur(function() {
		  if($( "#convertkit_api_key" ).val() != ''){
				  $( "#get-lists" ).trigger( "click" );
		  }
		});
		
		$( ".get-lists" ).click(function(e) {
			e.preventDefault();
			if($( "#convertkit_api_key" ).val() != ''){
			jQuery(this).prop( "disabled", true );
			jQuery(this).text( "Refreshing" );
			var data = $( '#seed_cspv5_emaillist_settings' ).serialize();
			$.get( get_list_url, data )
				.done(function( data ) {
					data = jQuery.parseJSON( data );
					  $('#convertkit_listid').find('option').remove();
					$("#convertkit_listid").append($("<option />").val('0').text('Select a Course'));
					$.each(data.lists, function(i,v) {
						$("#convertkit_listid").append($("<option />").val(i).text(v));
					});

					$('#convertkit_form_listid').find('option').remove();
					$("#convertkit_form_listid").append($("<option />").val('0').text('Select a Form'));
					$.each(data.forms, function(i,v) {
						$("#convertkit_form_listid").append($("<option />").val(i).text(v));
					});
				})
				.always(function() {
					jQuery(".get-lists").prop( "disabled", false );
					jQuery(".get-lists").text( "Refresh Lists" );
				});
			}
		});
	});
	</script>
	<?php

	return $output = ob_get_clean();

}



/**
 *  Get List from convertkit
 */
function seed_cspv5_legacy_get_convertkit_lists() {

	$convertkit_api_key = $_REQUEST['convertkit_api_key'];
	$page_id            = $_REQUEST['page_id'];
	$emaillist          = $_REQUEST['emaillist'];
	$lists              = array();
	$forms              = array();
	try {

			//var_dump('miss');

		if ( ! isset( $apikey ) && isset( $convertkit_api_key ) ) {
			$apikey = $convertkit_api_key;
		}

		if ( empty( $apikey ) ) {
			return array();
		}

			$url      = 'https://api.convertkit.com/v3/courses?api_key=' . $apikey;
			$response = wp_remote_get( $url );
			$response = wp_remote_retrieve_body( $response );

		if ( isset( $_REQUEST['debug'] ) && $_REQUEST['debug'] == 'true' ) {
			var_dump( $response );
		}

			$response = json_decode( $response, true );
		if ( ! empty( $response['courses'] ) ) {

			foreach ( $response['courses'] as $k => $v ) {
				$lists[ $v['id'] ] = $v['name'];
			}
			if ( ! empty( $lists ) ) {
				set_transient( "seed_cspv5_{$emaillist}_lists_{$page_id}", serialize( $lists ) );
			}
		} else {
			$lists['false'] = __( 'No Courses Found', 'seedprod' );
		}

			$url      = 'https://api.convertkit.com/v3/forms?api_key=' . $apikey;
			$response = wp_remote_get( $url );
			$response = wp_remote_retrieve_body( $response );

		if ( isset( $_REQUEST['debug'] ) && $_REQUEST['debug'] == 'true' ) {
			var_dump( $response );
		}

			$response = json_decode( $response, true );
		if ( ! empty( $response['forms'] ) ) {

			foreach ( $response['forms'] as $k => $v ) {

				$forms[ $v['id'] ] = $v['name'];
			}
			if ( ! empty( $forms ) ) {
				set_transient( "seed_cspv5_{$emaillist}_lists_forms_{$page_id}", serialize( $forms ) );
			}
		} else {
			$forms['false'] = __( 'No Forms Found', 'seedprod' );
		}
	} catch ( Exception $e ) {
	}
	$r = array(
		'lists' => $lists,
		'forms' => $forms,
	);

	return json_encode( $r );
}


/**
 *  Subscribe convertkit
 */
add_action( 'seed_cspv5_legacy_emaillist_convertkit', 'seed_cspv5_legacy_emaillist_convertkit_add_subscriber' );

function seed_cspv5_legacy_emaillist_convertkit_add_subscriber( $args ) {
		global $seed_cspv5_post_result;

		// Page Settings
		$settings = $args['settings'];
		extract( $settings );

		//  Emaillist Settings
		$settings_name = 'seed_cspv5_' . $page_id . '_' . $emaillist;
		$e_settings    = get_option( $settings_name );
		$e_settings    = maybe_unserialize( $e_settings );
		extract( $e_settings );

		require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/backwards/extentions/convertkit/seed_cspv5_convertkit.class.php';
		require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/backwards/lib/nameparse.php';

				// If tracking enabled
	if ( ! empty( $enable_reflink ) || ! empty( $display_optin_confirm ) ) {
		seed_cspv5_legacy_emaillist_database_add_subscriber( $args );
	}

				$apikey = $convertkit_api_key;
				$api    = new seed_cspv5_ConvertKitAPI( $apikey );
	if ( isset( $convertkit_listid ) ) {
		$listId = $convertkit_listid;
	}
	if ( isset( $convertkit_form_listid ) ) {
		$formId = $convertkit_form_listid;
	}

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

	if ( ! empty( $listId ) ) {
		$api      = new seed_cspv5_ConvertKitAPI( $apikey );
		$options  = array(
			'email' => $email,
			'fname' => $fname,
		);
		$response = $api->course_subscribe( $listId, $options );
	}

	if ( ! empty( $formId ) ) {

		$url      = "https://api.convertkit.com/v3/forms/$formId/subscribe";
		$headers  = array( 'Content-Type' => 'application/json; charset=utf-8' );
		$body     = json_encode(
			array(
				'api_key'    => $apikey,
				'email'      => $email,
				'first_name' => $fname,
			)
		);
		$args     = array(
			'body'    => $body,
			'headers' => $headers,
		);
		$response = wp_remote_post( $url, $args );
	}

	if ( isset( $_REQUEST['debug'] ) && $_REQUEST['debug'] == 'true' ) {
		var_dump( $api );
		var_dump( $response );
	}

	if ( empty( $response ) ) {

			$seed_cspv5_post_result['msg']       = 'There was an issue adding your email.';
			$seed_cspv5_post_result['msg_class'] = 'alert-info';

	} else {

		if ( $seed_cspv5_post_result['status'] == '409' ) {
			$seed_cspv5_post_result['status'] = '200';
		}

		if ( empty( $seed_cspv5_post_result['status'] ) ) {
			$seed_cspv5_post_result['status'] = '200';
		}
	}

}


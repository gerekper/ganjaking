<?php
//  * Copyright 2016 SEEDPROD LLC (email : john@seedprod.com, twitter : @seedprod)

/**
 *  Add MailChimp section
 */

function seed_cspv5_legacy_section_mailchimp( $emaillist, $page_id ) {
	// Get settings
	$settings_name = 'seed_cspv5_' . $page_id . '_' . $emaillist;
	$settings      = get_option( $settings_name );
	if ( ! empty( $settings ) ) {
		$settings = maybe_unserialize( $settings );
	}
	ob_start();
	?>
	<div class="postbox">
		<h3 class="hndle"><?php _e( 'MailChimp', 'seedprod' ); ?></h3>
		<div class="inside">
		<p><?php __( 'Configure saving subscribers to MailChimp options. Save after you enter your api key to load your list. <a href="#">Learn More</a>', 'seedprod' ); ?></p>
		<form id="seed_cspv5_emaillist_settings">
		<input type="hidden" id="settings_name" name="settings_name" value="<?php echo $settings_name; ?>"/>
		<input type="hidden" id="page_id" name="page_id" value="<?php echo $page_id; ?>"/>
		<input type="hidden" id="emaillist" name="emaillist" value="<?php echo $emaillist; ?>"/>
		<p><a href="https://support.seedprod.com/article/82-collecting-emails-with-mailchimp" target="_blank">Learn how to Configure</a></p>
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row">
						<strong><?php _e( 'Enable MailChimp API Version 3', 'seedprod' ); ?></strong>
					</th>
					<td>
						<button id="switch-btn" class="button-secondary">Switch to new MailChimp API</button>
						<br>
						<small class="description highlight">MailChimp is deprecating all old API versions and requiring users to switch to their new API (Version 3) by the end of 2016. Click the button above to switch and configure the new API version. Make sure to re-test that subscribers are being added as you expect. </small>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<strong>API Key</strong>
					</th>
					<td>
						<input class="regular-text" type="textbox" id="mailchimp_api_key" name="mailchimp_api_key" value="<?php echo ( ! empty( $settings['mailchimp_api_key'] ) ) ? $settings['mailchimp_api_key'] : ''; ?>" />
						<br>
						<small class="description">Enter your API Key. <a target="_blank" href="http://admin.mailchimp.com/account/api-key-popup" target="_blank">Get your API key</a></small>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<strong><?php _e( 'List', 'seedprod' ); ?></strong>
					</th>
					<td>
						<?php
						$lists = maybe_unserialize( get_transient( "seed_cspv5_{$emaillist}_lists_{$page_id}" ) );
						seed_cspv5_select( 'mailchimp_listid', $lists, ( ! empty( $settings['mailchimp_listid'] ) ) ? $settings['mailchimp_listid'] : '' );
						?>
						<button id="get-lists" class="button-secondary">Refresh Lists</button>
						<br>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<strong><?php _e( 'Enable Double Opt-In', 'seedprod' ); ?></strong>
					</th>
					<td>
						<input class="" type="checkbox" id="mailchimp_enable_double_optin" name="mailchimp_enable_double_optin" value="1" <?php echo ( ! empty( $settings['mailchimp_enable_double_optin'] ) ) ? 'checked' : ''; ?>>
						<br>
						<small class="description">Learn more about <a href="http://kb.mailchimp.com/article/how-does-confirmed-optin-or-double-optin-work" target="_blank">Double Opt-In</a></small>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<strong><?php _e( 'Send Welcome Email', 'seedprod' ); ?></strong>
					</th>
					<td>
						<input class="" type="checkbox" id="mailchimp_welcome_email" name="mailchimp_welcome_email" value="1" <?php echo ( ! empty( $settings['mailchimp_welcome_email'] ) ) ? 'checked' : ''; ?>>
						<br>
						<small class="description">If your Double Opt-in is false and this is true, MailChimp will send your lists Welcome Email if this subscribe succeeds - this will not fire if MailChimp ends up updating an existing subscriber. If Double Opt-in is true, this has no effect. Learn more about <a href='http://blog.mailchimp.com/sending-welcome-emails-with-mailchimp/' target='_blank'>Welcome Emails</a>.</small>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<strong><?php _e( 'Group Name', 'seedprod' ); ?></strong>
					</th>
					<td>
						<input class="large-text" type="textbox" id="mailchimp_group_name" name="mailchimp_group_name" value="<?php echo ( ! empty( $settings['mailchimp_group_name'] ) ) ? $settings['mailchimp_group_name'] : ''; ?>" />
						<br>
						<small class="description">Optional: Enter the name of the group. Learn more about <a href="http://mailchimp.com/features/groups/" target="_blank">Groups</a></small>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<strong><?php _e( 'Groups', 'seedprod' ); ?></strong>
					</th>
					<td>
						<input class="large-text" type="textbox" id="mailchimp_groups" name="mailchimp_groups" value="<?php echo ( ! empty( $settings['mailchimp_groups'] ) ) ? $settings['mailchimp_groups'] : ''; ?>" />
						<br>
						<small class="description">Optional: Comma delimited list of interest groups to add the email to.</a></small>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<strong><?php _e( 'Update Existing', 'seedprod' ); ?></strong>
					</th>
					<td>
						<input class="" type="checkbox" id="mailchimp_update_existing" name="mailchimp_update_existing" value="1" <?php echo ( ! empty( $settings['mailchimp_update_existing'] ) ) ? 'checked' : ''; ?>>
						<br>
						<small class="description">Control whether existing subscribers should be updated instead of throwing an error.</small>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<strong><?php _e( 'Replace Interests', 'seedprod' ); ?></strong>
					</th>
					<td>
						<input class="" type="checkbox" id="mailchimp_replace_interests" name="mailchimp_replace_interests" value="1" <?php echo ( ! empty( $settings['mailchimp_replace_interests'] ) ) ? 'checked' : ''; ?>>
						<br>
						<small class="description">Whether MailChimp will replace the interest groups with the groups provided or add the provided groups to the member's interest groups.</small>
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
   

	jQuery( "#switch-btn" ).click(function() {
		jQuery(this).prop( "disabled", true );
		var dataString = jQuery( '#seed_cspv5_emaillist_settings' ).serialize();
		var jqxhr = jQuery.post( save_url, dataString+'&api_version=3')
		  .done(function(data) {
			  if(data == '1'){
				 location.reload();
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
		$( "#mailchimp_api_key" ).blur(function() {
		  if($( "#mailchimp_api_key" ).val() != ''){
				  $( "#get-lists" ).trigger( "click" );
		  }
		});
		
		$( "#get-lists" ).click(function(e) {
			e.preventDefault();
			if($( "#mailchimp_api_key" ).val() != ''){
			jQuery(this).prop( "disabled", true );
			jQuery(this).text( "Refreshing" );
			var data = $( '#seed_cspv5_emaillist_settings' ).serialize();
			$.get( get_list_url, data )
				.done(function( data ) {
					data = jQuery.parseJSON( data );
					  $('#mailchimp_listid').find('option').remove();
					$.each(data, function(i,v) {
						$("#mailchimp_listid").append($("<option />").val(i).text(v));
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
 *  Get List from MailChimp
 */
function seed_cspv5_legacy_get_mailchimp_lists() {
	$mailchimp_api_key = $_REQUEST['mailchimp_api_key'];
	$page_id           = $_REQUEST['page_id'];
	$emaillist         = $_REQUEST['emaillist'];
	$lists             = array();
	try {
		require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/backwards/extentions/mailchimp/seed_cspv5_MCAPI.class.php';

		$api = new seed_cspv5_MCAPI( $mailchimp_api_key );

		$response = $api->lists();
		if ( isset( $_REQUEST['debug'] ) && $_REQUEST['debug'] == 'true' ) {
			var_dump( $api );
			var_dump( $response );
		}

		if ( $response['total'] === 0 ) {
			$lists['false'] = __( 'No lists Found', 'seedprod' );
			return $lists;
		}
		if ( $api->errorCode ) {
			$lists['false'] = __( 'Unable to load MailChimp lists, check your API Key.', 'seedprod' );
		} else {

			foreach ( $response['data'] as $k => $v ) {
				$lists[ $v['id'] ] = $v['name'];
			}
			if ( ! empty( $lists ) ) {
				set_transient( "seed_cspv5_{$emaillist}_lists_{$page_id}", serialize( $lists ) );
			}
		}
	} catch ( Exception $e ) {

	}
	return json_encode( $lists );
}


/**
 *  Subscribe MailChimp
 */
add_action( 'seed_cspv5_legacy_emaillist_mailchimp', 'seed_cspv5_legacy_emaillist_mailchimp_add_subscriber' );

function seed_cspv5_legacy_emaillist_mailchimp_add_subscriber( $args ) {
		global $seed_cspv5_post_result;

		// Page Settings
		$settings = $args['settings'];
		extract( $settings );

		//  Emaillist Settings
		$settings_name = 'seed_cspv5_' . $page_id . '_' . $emaillist;
		$e_settings    = get_option( $settings_name );
		$e_settings    = maybe_unserialize( $e_settings );
		extract( $e_settings );

		require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/backwards/extentions/mailchimp/seed_cspv5_MCAPI.class.php';
		require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/backwards/lib/nameparse.php';

				// If tracking enabled
	if ( ! empty( $enable_reflink ) || ! empty( $display_optin_confirm ) ) {
		seed_cspv5_legacy_emaillist_database_add_subscriber( $args );
	}

				$apikey = $mailchimp_api_key;
				$api    = new seed_cspv5_MCAPI( $apikey );
				$listId = $mailchimp_listid;

	if ( ! empty( $mailchimp_enable_double_optin ) ) {
		$double_optin = true;
	} else {
		$double_optin = false;
	}

	if ( ! empty( $mailchimp_welcome_email ) ) {
		$welcome_email = true;
	} else {
		$welcome_email = false;
	}
	if ( ! empty( $mailchimp_replace_interests ) ) {
		$replace_interests = true;
	} else {
		$replace_interests = false;
	}
	if ( ! empty( $mailchimp_update_existing ) ) {
		$update_existing = true;
	} else {
		$update_existing = false;
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

				// Get meta
				// Get meta field
				$meta = null;
	if ( seed_cspv5_legacy_cu( 'fb' ) ) {
		foreach ( $_REQUEST as $k => $v ) {
			if ( substr( $k, 0, 6 ) === 'field_' ) {
				$meta[ $k ] = $_REQUEST[ $k ];
			}
		}
	}

				// Get meta
				$print_meta = '';
	if ( seed_cspv5_legacy_cu( 'fb' ) ) {
		$form_settings_name = 'seed_cspv5_' . $_REQUEST['page_id'] . '_form';
		$form_settings      = get_option( $form_settings_name );
		if ( ! empty( $form_settings ) ) {
			$form_settings = maybe_unserialize( $form_settings );
		}

		if ( ! empty( $meta ) ) {
			foreach ( $meta as $k1 => $v1 ) {
				if ( substr( $k1, 0, 6 ) === 'field_' ) {
					$print_meta .= $form_settings[ $k1 ]['label'] . ':' . $v1 . PHP_EOL;
				}
			}
		}
	}

				$merge_vars = array(
					'FNAME'  => $fname,
					'LNAME'  => $lname,
					'REFID'  => $seed_cspv5_post_result['ref'],
					'REFURL' => $seed_cspv5_post_result['ref_url'],
					'META'   => $print_meta,
				);
				//var_dump($merge_vars);die();

				if ( ! empty( $mailchimp_groups ) && ! empty( $mailchimp_group_name ) ) {
					$merge_vars['GROUPINGS'] = array(
						array(
							'name'   => $mailchimp_group_name,
							'groups' => $mailchimp_groups,
						),
					);
				}

				$retval = $api->listSubscribe( $listId, $email, apply_filters( 'seed_cspv5_mailchimp_merge_vars', $merge_vars ), $email_type = 'html', $double_optin, $update_existing, $replace_interests, $welcome_email );

				if ( isset( $_REQUEST['debug'] ) && $_REQUEST['debug'] == 'true' ) {
					var_dump( $api );
					var_dump( $retval );
				}

				if ( ! empty( $api->errorMessage ) ) {
					global $errors;
					$seed_cspv5_post_result['status']    = '400';
					$seed_cspv5_post_result['msg_class'] = 'alert-danger';
					$errors[]                            = $api->errorMessage;
				} else {
					if ( $seed_cspv5_post_result['status'] == '409' ) {
						$seed_cspv5_post_result['status'] = '200';
					}

					if ( empty( $seed_cspv5_post_result['status'] ) ) {
						$seed_cspv5_post_result['status'] = '200';
					}
				}

}

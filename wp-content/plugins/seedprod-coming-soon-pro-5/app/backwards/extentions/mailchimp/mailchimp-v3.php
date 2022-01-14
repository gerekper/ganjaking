<?php
//  * Copyright 2016 SEEDPROD LLC (email : john@seedprod.com, twitter : @seedprod)

/**
 *  Add MailChimp section
 */

function seed_cspv5_legacy_section_mailchimp_v3( $emaillist, $page_id ) {
	// Get settings
	$settings_name = 'seed_cspv5_' . $page_id . '_' . $emaillist;
	$settings      = get_option( $settings_name );
	if ( ! empty( $settings ) ) {
		$settings = maybe_unserialize( $settings );
	}
	//var_dump($settings );
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
		<input type="hidden" id="api_version" name="api_version" value="3"/>
		<p><a href="https://support.seedprod.com/article/82-collecting-emails-with-mailchimp" target="_blank">Learn how to Configure</a></p>
		<table class="form-table">
			<tbody>
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
				<tr>
				<td colspan="2"><hr><strong>Optional Settings</strong></td>
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

				<!-- <tr valign="top">
					<th scope="row">
						<strong><?php _e( 'Groups', 'seedprod' ); ?></strong>
					</th>
					<td>
						<input class="large-text" type="textbox" id="mailchimp_groups" name="mailchimp_groups" value="<?php echo ( ! empty( $settings['mailchimp_groups'] ) ) ? $settings['mailchimp_groups'] : ''; ?>" />
						<br>
						<small class="description">Optional: Comma delimited list of interest groups to add the email to.</a></small>
					</td>
				</tr> -->
				
				<tr valign="top">
					<th scope="row">
						<strong><?php _e( 'Assign Interests', 'seedprod' ); ?></strong>
					</th>
					<td>
						<?php if ( ! empty( $settings['mailchimp_api_key'] ) ) { ?>
							<?php
							$o = '';

							$o = seed_cspv5_get_mailchimp_interest_groups_v3( $settings['mailchimp_api_key'], $settings['mailchimp_listid'], $settings );

							echo $o;

							?>
						<br>
						<button id="interest-btn" class="button-secondary">Refresh Interest Groups</button>
						<?php } else { ?>
						<button id="interest-btn" class="button-secondary">Load Interest Groups</button>
						<?php } ?>
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
						<strong><?php _e( 'Send Referral URL to MailChimp', 'seedprod' ); ?></strong>
					</th>
					<td>
						<input class="" type="checkbox" id="send_refurl" name="send_refurl" value="1" <?php echo ( ! empty( $settings['send_refurl'] ) ) ? 'checked' : ''; ?>>
						<br>
						<small class="description">If you have referral tracking enabled in the plugin and want to send the Referral URL to MailChimp, create a List Merge Field in MailChimp called REFURL and check the box above.</small>
					</td>
				</tr>
				<?php if ( seed_cspv5_legacy_cu( 'fb' ) ) { ?>
				<tr>
				<td colspan="2"><hr><strong>Map Fields</strong></td>
				</tr>
				<tr>
				<td>
			   <strong> MailChimp Merge Fields</strong>
				</td>
				<td>
				 <strong>Custom Form Fields </strong>
				</td>

				</tr>
				 

				   
						<?php
						if ( ! empty( $settings['mailchimp_api_key'] ) ) {
							$merge_fields = seed_cspv5_get_mailchimp_merge_fields_v3( $settings['mailchimp_api_key'], $settings['mailchimp_listid'], $settings );
							// Get form settings
							$form_settings_name = 'seed_cspv5_' . $page_id . '_form';
							$form_settings      = get_option( $form_settings_name );
							$form_fields        = array( 'Do Not Map' );
							if ( ! empty( $form_settings ) ) {
								$form_settings = maybe_unserialize( $form_settings );
								foreach ( $form_settings as $k => $v ) {
									if ( is_array( $v ) ) {
										if ( ! empty( $v['visible'] ) && $v['visible'] == 'on' ) {
											$form_fields[ $v['name'] ] = $v['label'];

										}
									}
								}
							}

							foreach ( $merge_fields->merge_fields as $v ) {
								if ( $v->name != 'REFID' ) {
									echo '<tr valign="top"><td>' . $v->name . '</td><td>';
									seed_cspv5_select( "merge_map_$v->tag", $form_fields, ( ! empty( $settings[ "merge_map_$v->tag" ] ) ) ? $settings[ "merge_map_$v->tag" ] : '' );
									echo '</td></tr>';
								}
							}
						}

						?>
					   
				 
			
				

				<?php } ?>
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

	jQuery( "#interest-btn" ).click(function() {
		if(jQuery( "#mailchimp_api_key" ).val() == ''|| jQuery( "#mailchimp_listid" ).val() == ''){
			alert('Please enter your API Key and select a List.');
		}
		jQuery(this).prop( "disabled", true );
		var dataString = jQuery( '#seed_cspv5_emaillist_settings' ).serialize();
		var jqxhr = jQuery.post( save_url, dataString)
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
			$.get( get_list_url, data+'&mod=mailchimp_v3' )
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
function seed_cspv5_legacy_get_mailchimp_v3_lists() {
	$mailchimp_api_key = $_REQUEST['mailchimp_api_key'];
	$page_id           = $_REQUEST['page_id'];
	$emaillist         = $_REQUEST['emaillist'];
	$lists             = array();
	try {

		// Api v3
		$dc                = explode( '-', $mailchimp_api_key );
		$args              = array(
			'headers' => array(
				'Authorization' => 'Basic ' . base64_encode( 'mailchimp:' . $mailchimp_api_key ),
			),
		);
		$mailchimp_api_url = "https://{$dc[1]}.api.mailchimp.com/3.0/lists?fields=lists.id,lists.name&count=100";
		$response          = wp_remote_get( $mailchimp_api_url, $args );
		if ( isset( $_REQUEST['debug'] ) && $_REQUEST['debug'] == 'true' ) {
			var_dump( $response );
		}
		if ( is_wp_error( $response ) ) {
			$error_message  = $response->get_error_message();
			$lists['false'] = $error_message;
		} else {
			$response_code = wp_remote_retrieve_response_code( $response );

			if ( $response_code == 200 ) {
				$api_response = json_decode( wp_remote_retrieve_body( $response ), true );
				foreach ( $api_response['lists'] as $k => $v ) {
					$lists[ $v['id'] ] = $v['name'];
				}
				if ( ! empty( $lists ) ) {
					set_transient( "seed_cspv5_{$emaillist}_lists_{$page_id}", serialize( $lists ) );
				}
			} else {
				$api_response   = json_decode( wp_remote_retrieve_body( $response ), true );
				$lists['false'] = $api_response['detail'];
			}
		}
	} catch ( Exception $e ) {

	}
	return json_encode( $lists );
}


/**
 *  Subscribe MailChimp
 */
add_action( 'seed_cspv5_legacy_emaillist_mailchimp_v3', 'seed_cspv5_legacy_emaillist_mailchimp_add_subscriber_v3' );

function seed_cspv5_legacy_emaillist_mailchimp_add_subscriber_v3( $args ) {
		global $seed_cspv5_post_result;

		// Page Settings
		$settings = $args['settings'];
		extract( $settings );

		//  Emaillist Settings
		$settings_name = 'seed_cspv5_' . $page_id . '_' . $emaillist;
		$e_settings    = get_option( $settings_name );
		$e_settings    = maybe_unserialize( $e_settings );
		//var_dump($e_settings);
		extract( $e_settings );

		require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/backwards/lib/nameparse.php';

		// If tracking enabled
	if ( ! empty( $enable_reflink ) || ! empty( $display_optin_confirm ) ) {
		seed_cspv5_legacy_emaillist_database_add_subscriber( $args );
	}

				// mailchimp v3

				$apikey = $mailchimp_api_key;
				$listId = $mailchimp_listid;

	if ( ! empty( $mailchimp_enable_double_optin ) ) {
		$double_optin = 'pending';
	} else {
		$double_optin = 'subscribed';
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
					'FNAME' => $fname,
					'LNAME' => $lname,
					//'REFID'=>$seed_cspv5_post_result['ref'],
					//'REFURL'=>$seed_cspv5_post_result['ref_url'],
					//'META'=>$print_meta
				);
				//var_dump($merge_vars);die();

				if ( ! empty( $send_refurl ) ) {
					$merge_vars['REFURL'] = $seed_cspv5_post_result['ref_url'];
				}
				//var_dump($merge_vars);

				// Add custom merge data
				if ( seed_cspv5_legacy_cu( 'fb' ) ) {
					//var_dump($form_settings);
					foreach ( $e_settings as $k => $v ) {
						if ( substr( $k, 0, 10 ) === 'merge_map_' ) {
							if ( ! empty( $v ) ) {
								if ( ! empty( $meta[ $v ] ) && $form_settings[ $v ]['visible'] == 'on' ) {
									$merge_vars[ str_replace( 'merge_map_', '', $k ) ] = $meta[ $v ];
								}
							}
						}
					}
				}
				//var_dump($merge_vars);
				//die();

				$mailchimp_interests = array();
				if ( ! empty( $interests ) ) {
					foreach ( $interests as $v ) {
						$mailchimp_interests[ $v ] = true;

					}
				}

				if ( ! empty( $select_interests ) ) {
					foreach ( $select_interests as $v ) {
						if ( ! empty( $v ) ) {
							$mailchimp_interests[ $v ] = true;
						}
					}
				}

				// mailchimp v3
				$dc   = explode( '-', $mailchimp_api_key );
				$args = array(
					'headers' => array(
						'Authorization' => 'Basic ' . base64_encode( 'mailchimp:' . $mailchimp_api_key ),
					),
				);

				$data = array(
					'status'        => $double_optin,
					'email_address' => $email,
					'merge_fields'  => $merge_vars,
				);

				if ( $mailchimp_interests ) {
					$data['interests'] = $mailchimp_interests;
				}

				if ( $update_existing ) {
					$mailchimp_api_url = "https://{$dc[1]}.api.mailchimp.com/3.0/lists/" . $listId . '/members/' . md5( strtolower( $email ) );

					$response = wp_remote_post(
						$mailchimp_api_url,
						array(
							'method'  => 'PUT',
							'body'    => json_encode( $data ),
							'headers' => array(
								'Authorization' => 'Basic ' . base64_encode( 'mailchimp:' . $mailchimp_api_key ),
							),
						)
					);
				} else {
					$mailchimp_api_url = "https://{$dc[1]}.api.mailchimp.com/3.0/lists/" . $listId . '/members';

					$response = wp_remote_post(
						$mailchimp_api_url,
						array(
							'method'  => 'POST',
							'body'    => json_encode( $data ),
							'headers' => array(
								'Authorization' => 'Basic ' . base64_encode( 'mailchimp:' . $mailchimp_api_key ),
							),
						)
					);
				}

				if ( isset( $_REQUEST['debug'] ) && $_REQUEST['debug'] == 'true' ) {
					var_dump( $response );
				}

				if ( is_wp_error( $response ) ) {
					$seed_cspv5_post_result['status'] = '500';
					$error_message                    = $response->get_error_message();
					$seed_cspv5_post_result['html']   = $error_message;
					if ( $error_message == 'cURL error 6: Could not resolve host: .api.mailchimp.com' ) {
						$seed_cspv5_post_result['html'] = "Please enter your API Key in the plugin's MailChimp Settings";
					}
				} else {
					$response_code = wp_remote_retrieve_response_code( $response );
					if ( $response_code == 200 ) {
						$seed_cspv5_post_result['status'] = '200';
					} else {
						if ( $update_existing == false ) {
							$api_response = json_decode( wp_remote_retrieve_body( $response ), true );
							if ( $response_code == 400 && $api_response['title'] == 'Member Exists' ) {
								$seed_cspv5_post_result['status']    = '409';
								$seed_cspv5_post_result['msg']       = $txt_already_subscribed_msg;
								$seed_cspv5_post_result['msg_class'] = 'alert-danger';
							} else {
								$seed_cspv5_post_result['status'] = '500';
								$errors                           = '';
								if ( ! empty( $api_response['errors'] ) ) {
									$errors = urldecode( http_build_query( $api_response['errors'], '', ', ' ) );
								}
								$seed_cspv5_post_result['html'] = $api_response['detail'] . PHP_EOL . $errors;
								if ( $api_response['detail'] == 'The requested resource could not be found.' ) {
									$seed_cspv5_post_result['html'] = "Please make sure you selected a list in the plugin's MailChimp Settings.";
								}
							}
						} else {

							$seed_cspv5_post_result['status'] = '500';
							$api_response                     = json_decode( wp_remote_retrieve_body( $response ), true );
							$seed_cspv5_post_result['html']   = $api_response['detail'] . 'sas';

						}
					}
				}

}

function seed_cspv5_legacy_get_mailchimp_merge_fields_v3( $api_key, $list_id, $settings ) {
	$dc   = substr( $api_key, strpos( $api_key, '-' ) + 1 ); // datacenter, it is the part of your api key - us5, us8 etc
	$args = array(
		'headers' => array(
			'Authorization' => 'Basic ' . base64_encode( 'user:' . $api_key ),
		),
	);

	$response = wp_remote_get( 'https://' . $dc . '.api.mailchimp.com/3.0/lists/' . $list_id . '/merge-fields', $args );
	$body     = json_decode( wp_remote_retrieve_body( $response ) );

	return $body;
}


function seed_cspv5_legacy_get_mailchimp_interest_groups_v3( $api_key, $list_id, $settings ) {
	// I recommend you not to echo all the code immediately, add it into the variable first
	$output = '';

	$dc   = substr( $api_key, strpos( $api_key, '-' ) + 1 ); // datacenter, it is the part of your api key - us5, us8 etc
	$args = array(
		'headers' => array(
			'Authorization' => 'Basic ' . base64_encode( 'user:' . $api_key ),
		),
	);

	$response = wp_remote_get( 'https://' . $dc . '.api.mailchimp.com/3.0/lists/' . $list_id . '/interest-categories', $args );
	$body     = json_decode( wp_remote_retrieve_body( $response ) );

	if ( wp_remote_retrieve_response_code( $response ) == 200 && $body->total_items > 0 ) {
		foreach ( $body->categories as $group ) :

			// we can skip hidden interests
			//if( $group->type == 'hidden')
			//continue;

			// heading, name of the Interest Category
			$output .= '<h3>' . $group->title . '</h3>';

			// connect to API to get interests from each category
			$response = wp_remote_get( 'https://' . $dc . '.api.mailchimp.com/3.0/lists/' . $list_id . '/interest-categories/' . $group->id . '/interests', $args );
			$body     = json_decode( wp_remote_retrieve_body( $response ) );

			if ( wp_remote_retrieve_response_code( $response ) == 200 && $body->total_items > 0 ) {
				/* type of the interest group which can be set in MailChimp dashboard */
				switch ( $group->type ) :
					case 'checkboxes':{
						foreach ( $body->interests as $interest ) {
							$output .= '<label><input value="' . $interest->id . '" name="interests[]" type="checkbox"';
							$output .= ( in_array( $interest->id, $settings['interests'] ) ) ? 'checked' : '';
							$output .= ' /> ' . $interest->name . '</label><br />';
						}
						break;
					}
					case 'radio':{
						foreach ( $body->interests as $interest ) {
							$output .= '<label><input value="' . $interest->id . '" name="interests[' . $group->id . ']" type="radio" /> ' . $interest->name . '</label><br />';
						}
						break;
					}
					case 'dropdown':{
						$output .= '<select name="select_interests[' . $group->id . ']"><option value="">Please select...</option>';

						foreach ( $body->interests as $interest ) {
							$selected = '';
							if ( $settings['select_interests'][ $group->id ] == $interest->id ) {
								$selected = 'selected';
							}
							$output .= '<option value="' . $interest->id . '"' . $selected . '>' . $interest->name . '</option>';

						}
						$output .= '</select>';
						break;
					}
					case 'hidden': {

						foreach ( $body->interests as $interest ) {
							$selected = '';
							if ( ! empty( $settings['interests'][ $interest->id ] ) ) {
								$selected = 'checked';
							}
							$output .= '<label><input value="' . $interest->id . '" name="interests[' . $interest->id . ']" type="checkbox"   ' . $selected . ' /> ' . $interest->name . '</label><br />';
						}
						break;
					}
					default:
						break;
				endswitch;

			} else {
				$output .= '<b>' . wp_remote_retrieve_response_code( $response ) . wp_remote_retrieve_response_message( $response ) . ':</b> ' . $body->detail;
			}

		endforeach;
	} else {
		$output .= 'You have not created any interest groups for list in MailChimp.';
	}

	return $output;
}

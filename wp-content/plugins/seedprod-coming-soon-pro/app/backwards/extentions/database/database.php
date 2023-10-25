<?php
// Copyright 2016 SEEDPROD LLC (email : john@seedprod.com, twitter : @seedprod)
add_action( 'seed_cspv5_legacy_emaillist_database', 'seed_cspv5_legacy_emaillist_database_add_subscriber' );

function seed_cspv5_legacy_emaillist_database_add_subscriber( $args ) {
		global $seed_cspv5_post_result;

		// Page Settings
		$settings = $args['settings'];
		extract( $settings );

		//  Emaillist Settings
		$settings_name = 'seed_cspv5_' . $page_id . '_' . $emaillist;
		$e_settings    = get_option( $settings_name );
		$e_settings    = maybe_unserialize( $e_settings );
	if ( ! empty( $e_settings ) && is_array( $e_settings ) ) {
		extract( $e_settings );
	}

		require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/backwards/lib/nameparse.php';
		//var_dump($page_id);
		// Record reference
		$ref = '-1';
	if ( ! empty( $_REQUEST['ref'] ) ) {
		$ref = intval( $_REQUEST['ref'], 36 ) - 1000;
	}

		$name = '';
	if ( ! empty( $_REQUEST['name'] ) ) {
		$name = $_REQUEST['name'];
	}

		$optin_confirmation = 0;
	if ( ! empty( $_REQUEST['optin_confirmation'] ) ) {
		$optin_confirmation = 1;
	}

	if ( empty( $email ) ) {
		$email = strtolower( $_REQUEST['email'] );
	}
		$fname = '';
		$lname = '';

	if ( ! empty( $name ) ) {
		$name  = seed_cspv5_parse_name( $name );
		$fname = $name['first'];
		$lname = $name['last'];
	}

		// Get meta field
		$meta = null;
	if ( seed_cspv5_legacy_cu( 'fb' ) ) {
		foreach ( $_REQUEST as $k => $v ) {
			if ( substr( $k, 0, 6 ) === 'field_' ) {
				$meta[ $k ] = $_REQUEST[ $k ];
			}
		}
		$meta = serialize( $meta );
	}

		// Record user in DB if they do not exist
		global $wpdb;
		$tablename = $wpdb->prefix . 'csp3_subscribers';

		// Fraud Detection
	if ( ! empty( $enable_fraud_detection ) && ! empty( $enable_reflink ) ) {
		$sql           = "SELECT * FROM $tablename WHERE ip = %s";
		$ip            = seed_cspv5_legacy_get_ip();
		$safe_sql      = $wpdb->prepare( $sql, $ip );
		$select_result = $wpdb->get_results( $safe_sql );

		if ( count( $select_result ) > 3 ) {
			$seed_cspv5_post_result['status'] = '500';
			$seed_cspv5_post_result['html']   = 'You have reached the max number of entries.';
			return false;
		}
	}

		// Record user in DB if they do not exist

		$sql           = "SELECT * FROM $tablename WHERE email = %s AND page_id = %d";
		$safe_sql      = $wpdb->prepare( $sql, $email, $page_id );
		$select_result = $wpdb->get_row( $safe_sql );

	if ( ! empty( $optin_confirmation ) ) {
		if ( empty( $select_result->email ) || $select_result->email != $email ) {
			$values        = array(
				'email'         => $email,
				'page_id'       => $page_id,
				'referrer'      => $ref,
				'ip'            => seed_cspv5_legacy_get_ip(),
				'fname'         => $fname,
				'lname'         => $lname,
				'meta'          => $meta,
				'optin_confirm' => $optin_confirmation,
			);
			$format_values = array(
				'%s',
				'%d',
				'%s',
				'%s',
				'%s',
				'%s',
				'%d',
			);
			$insert_result = $wpdb->insert(
				$tablename,
				$values,
				$format_values
			);
			// Record ref
			if ( ! empty( $ref ) ) {
				$sql           = "UPDATE $tablename SET conversions = conversions + 1 WHERE id = %d AND page_id = %d";
				$safe_sql      = $wpdb->prepare( $sql, $ref, $page_id );
				$update_result = $wpdb->get_var( $safe_sql );
			}
		}
	} else {
		if ( empty( $select_result->email ) || $select_result->email != $email ) {
			$values        = array(
				'email'    => $email,
				'page_id'  => $page_id,
				'referrer' => $ref,
				'ip'       => seed_cspv5_legacy_get_ip(),
				'fname'    => $fname,
				'lname'    => $lname,
				'meta'     => $meta,
			);
			$format_values = array(
				'%s',
				'%d',
				'%s',
				'%s',
				'%s',
				'%s',
			);
			$insert_result = $wpdb->insert(
				$tablename,
				$values,
				$format_values
			);
			// Record ref
			if ( ! empty( $ref ) ) {
				$sql           = "UPDATE $tablename SET conversions = conversions + 1 WHERE id = %d AND page_id = %d";
				$safe_sql      = $wpdb->prepare( $sql, $ref, $page_id );
				$update_result = $wpdb->get_var( $safe_sql );
			}
		}
	}

	if ( isset( $insert_result ) && $insert_result != false ) {
		// Send notice if a new subscriber.
		if ( $emaillist == 'database' && ! empty( $database_notifications ) ) {
			$message = home_url() . __( ' You have a new email subscriber: ', 'seedprod' ) . $fname . ' ' . $lname . ' ' . $email;
			$mresult = '';
			if ( empty( $database_notifications_emails ) ) {
				$mresult = wp_mail( get_option( 'admin_email' ), home_url() . __( ' : New Email Subscriber', 'seedprod' ), $message );
			} else {
				$mresult = wp_mail( $database_notifications_emails, home_url() . __( ' : New Email Subscriber', 'seedprod' ), $message );
			}
		}

		if ( empty( $seed_cspv5_post_result['status'] ) ) {
			$seed_cspv5_post_result['status'] = '200';
		}
		$ref                               = $wpdb->insert_id + 1000;
		$seed_cspv5_post_result['ref']     = base_convert( $ref, 10, 36 );
		$seed_cspv5_post_result['ref_url'] = seed_cspv5_legacy_ref_link();
			global $wpdb;
			$tablename = $wpdb->prefix . 'csp3_subscribers';
			// Update Row
			$r = $wpdb->update(
				$tablename,
				array(
					'ref_url' => $seed_cspv5_post_result['ref_url'],

				),
				array( 'id' => $wpdb->insert_id ),
				array(
					'%s',

				),
				array( '%d' )
			);

	} else {
		// Subscriber already exist show stats
		$seed_cspv5_post_result['status']    = '409';
		$seed_cspv5_post_result['msg']       = $txt_already_subscribed_msg;
		$seed_cspv5_post_result['msg_class'] = 'alert-info';
		if ( ! empty( $select_result->ref_url ) ) {
			$ref                               = $select_result->id + 1000;
			$seed_cspv5_post_result['ref']     = base_convert( $ref, 10, 36 );
			$seed_cspv5_post_result['ref_url'] = $select_result->ref_url;
		} else {
			$ref                               = $select_result->id + 1000;
			$seed_cspv5_post_result['ref']     = base_convert( $ref, 10, 36 );
			$seed_cspv5_post_result['ref_url'] = seed_cspv5_legacy_ref_link();
		}
		$seed_cspv5_post_result['clicks'] = '0';
		if ( ! empty( $select_result->clicks ) ) {
			$seed_cspv5_post_result['clicks'] = $select_result->clicks;
		}
		$seed_cspv5_post_result['subscribers'] = '0';
		if ( ! empty( $select_result->conversions ) ) {
			$seed_cspv5_post_result['subscribers'] = $select_result->conversions;
		}

		// Conditional Stats
		$rf_url   = '';
		$rf_stats = '';
		if ( ( ! empty( $enable_reflink ) && $enable_reflink ) && ! empty( $seed_cspv5_post_result['ref'] ) ) {
			$rf_url                         = '<br><br>' . $txt_stats_referral_url . '<br>' . seed_cspv5_legacy_ref_link();
			$rf_stats                       = '<br><br>' . $txt_stats_referral_stats . '<br>' . $txt_stats_referral_clicks . ': ' . $seed_cspv5_post_result['clicks'] . '<br>' . $txt_stats_referral_subscribers . ': ' . $seed_cspv5_post_result['subscribers'];
			$seed_cspv5_post_result['msg'] .= $rf_url . $rf_stats;
		}
	}
}


function seed_cspv5_legacy_section_database( $emaillist, $page_id ) {
	// Get settings
	$settings_name = 'seed_cspv5_' . $page_id . '_' . $emaillist;
	$settings      = get_option( $settings_name );
	if ( ! empty( $settings ) ) {
		$settings = maybe_unserialize( $settings );
	}
	ob_start();
	?>
	<div class="postbox">
		<h3 class="hndle"><?php _e( 'Database Options', 'seedprod' ); ?></h3>
		<div class="inside">
		<p><?php __( '<p class="description">Configure saving subscribers to the database options. <a target="_blank" href="https://support.seedprod.com/article/70-collecting-emails-in-the-database">Learn More</a></p>', 'seedprod' ); ?></p>
		<form id="seed_cspv5_emaillist_settings">
		<input type="hidden" id="settings_name" name="settings_name" value="<?php echo $settings_name; ?>"/>
		<input type="hidden" id="page_id" name="page_id" value="<?php echo $page_id; ?>"/>
		<input type="hidden" id="emaillist" name="emaillist" value="<?php echo $emaillist; ?>"/>
		<p><a href="https://support.seedprod.com/article/70-collecting-emails-in-the-database" target="_blank">Learn how to Configure</a></p>
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row">
						<strong>Enable New Subscriber Notifications</strong>
					</th>
					<td>
						<input class="" type="checkbox" id="database_notifications" name="database_notifications" value="1" <?php echo ( ! empty( $settings['database_notifications'] ) ) ? 'checked' : ''; ?>>
						<br>
						<small class="description">Get an email notification when some subscribes.</small>
					</td>
				</tr>
<!--                 <tr valign="top">
					<th scope="row">
						<strong>Enable Double Optin Email</strong>
					</th>
					<td>
						<input class="" type="checkbox" id="database_confirmation" name="database_confirmation" value="1" <?php echo ( ! empty( $settings['database_confirmation'] ) ) ? 'checked' : ''; ?>>
						<br>
						<small class="description">Subscribers will receive a confirmation mail.</small>
					</td>
				</tr> -->
				<tr valign="top">
					<th scope="row">
						<strong>Send Notifications to this Email</strong>
					</th>
					<td>
						<input class="large-text" type="textbox" id="database_notifications_emails" name="database_notifications_emails" value="<?php echo ( ! empty( $settings['database_notifications_emails'] ) ) ? $settings['database_notifications_emails'] : ''; ?>" />
						<br>
						<small class="description">Separate multiple emails with a comma. If no email is defined, notifications while be sent to the admin email.</small>
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
	</script>
	<?php

	return $output = ob_get_clean();
}

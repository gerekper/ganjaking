<?php
// Copyright 75nineteen Media LLC (scott@75nineteen.com)

/**
 *  Add Settings section
 */

function seed_cspv5_legacy_section_followupemails( $emaillist, $page_id ) {
	// Get settings
	$settings_name = 'seed_cspv5_' . $page_id . '_' . $emaillist;
	$settings      = get_option( $settings_name );
	if ( ! empty( $settings ) ) {
		$settings = maybe_unserialize( $settings );
	}
	ob_start();
	?>
	<div class="postbox">
		<h3 class="hndle"><?php _e( 'Follow-Up Emails', 'seedprod' ); ?></h3>
		<div class="inside">
		<p><?php __( 'Configure saving subscribers to the WordPress Users Database to send Follow-up Emails after signing up. Requires <a href="http://www.75nineteen.com/woocommerce/follow-up-email-autoresponder/?utm_source=SeedProd&utm_medium=ComingSoonPro&utm_campaign=IntegrationLink">Follow-Up Emails</a> to be installed.</a>', 'seedprod' ); ?></p>
		<form id="seed_cspv5_emaillist_settings">
		<input type="hidden" id="settings_name" name="settings_name" value="<?php echo $settings_name; ?>"/>
		<input type="hidden" id="page_id" name="page_id" value="<?php echo $page_id; ?>"/>
		<input type="hidden" id="emaillist" name="emaillist" value="<?php echo $emaillist; ?>"/>
		<p><a href="http://support.seedprod.com/article/124-collecting-emails-with-follow-up-email-for-woocommerce" target="_blank">Learn how to Configure</a></p>
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row">
						<strong><?php _e( 'Email', 'seedprod' ); ?></strong>
					</th>
					<td>
						<?php
						$lists = seed_cspv5_get_followupemails_signup_emails();
						seed_cspv5_select( 'followupemails_email_id', $lists, ( ! empty( $settings['followupemails_email_id'] ) ) ? $settings['followupemails_email_id'] : '' );
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
	
	</script>
	<?php

	return $output = ob_get_clean();

}

add_action( 'seed_cspv5_legacy_emaillist_followupemails', 'seed_cspv5_legacy_emaillist_followupemails_queue_email' );

function seed_cspv5_legacy_emaillist_followupemails_queue_email( $args ) {
		global $seed_cspv5_post_result,$wpdb;

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

	$name = '';
	if ( ! empty( $_REQUEST['name'] ) ) {
		$name = $_REQUEST['name'];
	}
	$email = strtolower( $_REQUEST['email'] );
	$fname = '';
	$lname = '';

	if ( ! empty( $name ) ) {
		$name  = seed_cspv5_parse_name( $name );
		$fname = $name['first'];
		$lname = $name['last'];
	}

	$tablename    = $wpdb->prefix . 'followup_subscribers';
	$sql          = "SELECT * FROM $tablename WHERE email= %s";
	$safe_sql     = $wpdb->prepare( $sql, trim( $email ) );
	$email_exists = $wpdb->get_row( $safe_sql );

	if ( ! empty( $email_exists ) ) {
		// if email exist, see if the user is on the list
		$tablename  = $wpdb->prefix . 'followup_subscribers_to_lists';
		$sql        = "SELECT * FROM $tablename WHERE  subscriber_id = %d AND list_id = %d ";
		$safe_sql   = $wpdb->prepare( $sql, array( $email_exists->id, $followupemails_email_id ) );
		$email_rows = $wpdb->get_row( $safe_sql );

		if ( empty( $email_rows ) ) {
			// Add user to list
			$subscriber_id = $wpdb->insert_id;
			$tablename     = $wpdb->prefix . 'followup_subscribers_to_lists';
			$r             = $wpdb->insert(
				$tablename,
				array(
					'subscriber_id' => $email_exists->id,
					'list_id'       => $followupemails_email_id,
				),
				array(
					'%d',
					'%d',
				)
			);

			if ( empty( $seed_cspv5_post_result['status'] ) ) {
				$seed_cspv5_post_result['status'] = '200';
			}
		} else {
			// already added

			$seed_cspv5_post_result['status']    = '409';
			$seed_cspv5_post_result['msg']       = $txt_already_subscribed_msg;
			$seed_cspv5_post_result['msg_class'] = 'alert-danger';
		}
	} else {
		// add user
		$tablename = $wpdb->prefix . 'followup_subscribers';
		$r         = $wpdb->insert(
			$tablename,
			array(
				'email'      => $email,
				'date_added' => date( 'Y-m-d H:i:s' ),
			),
			array(
				'%s',
				'%s',
			)
		);

		// Add user to list
		$subscriber_id = $wpdb->insert_id;
		$tablename     = $wpdb->prefix . 'followup_subscribers_to_lists';
		$r             = $wpdb->insert(
			$tablename,
			array(
				'subscriber_id' => $subscriber_id,
				'list_id'       => $followupemails_email_id,
			),
			array(
				'%d',
				'%d',
			)
		);

		if ( empty( $seed_cspv5_post_result['status'] ) ) {
			$seed_cspv5_post_result['status'] = '200';
		}
	}

}

function seed_cspv5_legacy_get_followupemails_signup_emails() {
	global $wpdb;

	$emails     = array();
	$email_rows = array();

	if ( class_exists( 'FUE_Email' ) ) {

		$tablename  = $wpdb->prefix . 'followup_subscriber_lists';
		$sql        = "SELECT * FROM $tablename";
		$email_rows = $wpdb->get_results( $sql );
	}

	foreach ( $email_rows as $email ) {
		//$email = new FUE_Email( $email_id );
		$emails[ $email->id ] = $email->list_name;
	}

	return $emails;
}

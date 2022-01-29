<?php
//  * Copyright 2014 SEEDPROD LLC (email : john@seedprod.com, twitter : @seedprod)

/**
 *  Add MailPoet section
 */

function seed_cspv5_legacy_section_mailpoet( $emaillist, $page_id ) {
	// Get settings
	$settings_name = 'seed_cspv5_' . $page_id . '_' . $emaillist;
	$settings      = get_option( $settings_name );
	if ( ! empty( $settings ) ) {
		$settings = maybe_unserialize( $settings );
	}
	ob_start();
	?>
	<div class="postbox">
		<h3 class="hndle"><?php _e( 'MailPoet', 'seedprod' ); ?></h3>
		<div class="inside">
		<p><?php __( 'Configure saving subscribers to MailPoet options. <a href="https://support.seedprod.com/article/80-collecting-emails-with-mailpoet" target="_blank">Learn More</a>', 'seedprod' ); ?></p>
		<form id="seed_cspv5_emaillist_settings">
		<input type="hidden" id="settings_name" name="settings_name" value="<?php echo $settings_name; ?>"/>
		<input type="hidden" id="page_id" name="page_id" value="<?php echo $page_id; ?>"/>
		<input type="hidden" id="emaillist" name="emaillist" value="<?php echo $emaillist; ?>"/>
		<p><a href="https://support.seedprod.com/article/80-collecting-emails-with-mailpoet" target="_blank">Learn how to Configure</a></p>
		<table class="form-table">
			<tbody>

				<tr valign="top">
					<th scope="row">
						<strong><?php _e( 'List', 'seedprod' ); ?></strong>
					</th>
					<td>
						<?php
						$lists = seed_cspv5_get_mailpoet_lists();
						seed_cspv5_select( 'mailpoet_list_id', $lists, ( ! empty( $settings['mailpoet_list_id'] ) ) ? $settings['mailpoet_list_id'] : '' );
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



/**
 *  Get List from MailPoet
 */
function seed_cspv5_legacy_get_mailpoet_lists() {
	$lists = array();
	include_once ABSPATH . 'wp-admin/includes/plugin.php';
	if ( is_plugin_active( 'wysija-newsletters/index.php' ) ) {
		//get the lists and ids
		global $wpdb;
		$wlists    = array();
		$tablename = $wpdb->prefix . 'wysija_list';
		if ( $wpdb->get_var( "SHOW TABLES LIKE '$tablename'" ) == $tablename ) {
			$sql    = "SELECT list_id,name FROM $tablename WHERE is_enabled = 1";
			$wlists = $wpdb->get_results( $sql );
		}

		$lists = array();

		foreach ( $wlists as $k => $v ) {
			$lists[ $v->list_id ] = $v->name;
		}
	} else {
		$lists = array( '-1' => 'No Lists Found' );
	}

	// Version 3 check
	if ( function_exists( 'mailpoet_php_version_notice' ) && version_compare( phpversion(), '5.3.3', '>=' ) ) {
			require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/backwards/extentions/mailpoet/mailpoet-v3.php';
			$lists = seed_cspv5_mailpoetv3_get_list();
			return $lists;
	}
	return $lists;
}


/**
 *  Subscribe MailPoet
 */
add_action( 'seed_cspv5_legacy_emaillist_mailpoet', 'seed_cspv5_legacy_emaillist_mailpoet_add_subscriber' );

function seed_cspv5_legacy_emaillist_mailpoet_add_subscriber( $args ) {
		global $seed_cspv5_post_result;

		// Page Settings
		$settings = $args['settings'];
		extract( $settings );

		//  Emaillist Settings
		$settings_name = 'seed_cspv5_' . $page_id . '_' . $emaillist;
		$e_settings    = get_option( $settings_name );
		$e_settings    = maybe_unserialize( $e_settings );
		extract( $e_settings );
	include_once ABSPATH . 'wp-admin/includes/plugin.php';
	require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/backwards/lib/nameparse.php';
	if ( is_plugin_active( 'wysija-newsletters/index.php' ) && class_exists( 'WYSIJA' ) || function_exists( 'mailpoet_php_version_notice' ) ) {
		$list_id = $mailpoet_list_id;

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

		//check if the email address is recorded in wysija
		if ( function_exists( 'mailpoet_php_version_notice' ) && version_compare( phpversion(), '5.3.3', '>=' ) ) {
			require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/backwards/extentions/mailpoet/mailpoet-v3.php';
			seed_cspv5_mailpoetv3_add_subscriber( $fname, $lname, $email, $settings, $list_id );
		} else {
			$modelUser = WYSIJA::get( 'user', 'model' );
			$userData  = $modelUser->getOne( array( 'user_id' ), array( 'email' => $email ) );
			//var_dump();
			if ( ! $userData ) {
				//record the email in wysija
				$userHelper = WYSIJA::get( 'user', 'helper' );
				$data       = array(
					'user'      => array(
						'email'     => $email,
						'firstname' => $fname,
						'lastname'  => $lname,
					),
					'user_list' => array( 'list_ids' => array( $list_id ) ),
				);
				$test       = $userHelper->addSubscriber( $data );
				if ( ! empty( $enable_reflink ) || ! empty( $display_optin_confirm ) ) {
					seed_cspv5_legacy_emaillist_database_add_subscriber( $args );
				}
				if ( empty( $seed_cspv5_post_result['status'] ) ) {
					$seed_cspv5_post_result['status'] = '200';
				}
			} else {
				$user_id    = $userData['user_id'];
				$userHelper = WYSIJA::get( 'user', 'helper' );
				$userHelper->addToLists( array( $list_id ), $user_id );
				if ( ! empty( $enable_reflink ) || ! empty( $display_optin_confirm ) ) {
					seed_cspv5_legacy_emaillist_database_add_subscriber( $args );
				}
				if ( empty( $seed_cspv5_post_result['status'] ) ) {
					$seed_cspv5_post_result['status']    = '409';
					$seed_cspv5_post_result['msg']       = $txt_already_subscribed_msg;
					$seed_cspv5_post_result['msg_class'] = 'alert-info';
				}
			}
		}
	}
}

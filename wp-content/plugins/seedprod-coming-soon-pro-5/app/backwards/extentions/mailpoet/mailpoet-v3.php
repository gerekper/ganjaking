<?php

function seed_cspv5_legacy_mailpoetv3_get_list() {
	$lists                  = array();
		$subscription_lists = \MailPoet\API\API::MP( 'v1' )->getLists();

	foreach ( $subscription_lists as $k => $v ) {
		$lists[ $v['id'] ] = $v['name'];
	}
	return $lists;
}

function seed_cspv5_legacy_mailpoetv3_add_subscriber( $fname, $lname, $email, $settings, $list_id ) {
	   extract( $settings );
	   global $seed_cspv5_post_result;
	   $subscriber_data = array(
		   'email'      => $email,
		   'first_name' => $fname,
		   'last_name'  => $lname,
	   );

		$lists = array( $list_id );

	   try {
		   $subscriber = \MailPoet\API\API::MP( 'v1' )->addSubscriber( $subscriber_data, $lists );
	   } catch ( Exception $exception ) {
		   $msg = $exception->getMessage();
	   }

	   if ( ! empty( $enable_reflink ) || ! empty( $display_optin_confirm ) ) {
		   seed_cspv5_legacy_emaillist_database_add_subscriber( $args );
	   }

	   if ( empty( $msg ) ) {
		   $seed_cspv5_post_result['status'] = '200';

	   } else {
		   $seed_cspv5_post_result['status']    = '409';
		   $seed_cspv5_post_result['msg']       = $txt_already_subscribed_msg;
		   $seed_cspv5_post_result['msg_class'] = 'alert-info';
	   }
}

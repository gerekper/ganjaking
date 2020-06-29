<?php

/**
 * Update Data to 7.5
 * Look for queue items for subscription emails without subscription keys in the meta
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb;

if ( class_exists( 'WC_Subscriptions_Manager' ) ) {
	$email_ids = array_map( 'absint', fue_get_emails( 'subscription', '', array('fields' => 'ids') ) );

	if ( count( $email_ids ) ) {
		$item_ids = $wpdb->get_results("SELECT id FROM {$wpdb->prefix}followup_email_orders WHERE email_id IN (". implode(',', $email_ids) .")");

		foreach ( $item_ids as $item_id ) {
			$item = new FUE_Sending_Queue_Item( $item_id->id );

			if ( empty( $item->meta['subs_key'] ) && WC_Subscriptions_Order::order_contains_subscription( $item->order_id ) ) {
				$subs_key = WC_Subscriptions_Manager::get_subscription_key( $item->order_id );
				$item->meta['subs_key'] = $subs_key;
				$item->save();
			}
		}
	}
}

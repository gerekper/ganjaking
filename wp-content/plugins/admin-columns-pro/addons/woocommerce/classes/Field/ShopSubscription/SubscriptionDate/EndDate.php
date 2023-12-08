<?php

namespace ACA\WC\Field\ShopSubscription\SubscriptionDate;

use ACA\WC\Editing;
use ACA\WC\Field\ShopSubscription\SubscriptionDate;
use ACA\WC\Search;
use WC_DateTime;
use WC_Subscription;

class EndDate extends SubscriptionDate {

	public function set_label() {
		$this->label = __( 'End Date', 'woocommerce-subscriptions' );
	}

	public function get_date( WC_Subscription $subscription ) {
		$date = $subscription->get_date( 'end' );

		return $date ? new WC_DateTime( $date ) : null;
	}

	public function get_meta_key(): string
    {
		return '_schedule_end';
	}

	public function editing() {
		return new Editing\ShopSubscription\Date( 'end', $this->get_meta_key() );
	}

}
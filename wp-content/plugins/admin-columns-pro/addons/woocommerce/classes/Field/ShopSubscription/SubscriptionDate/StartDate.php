<?php

namespace ACA\WC\Field\ShopSubscription\SubscriptionDate;

use ACA\WC\Editing;
use ACA\WC\Field\ShopSubscription\SubscriptionDate;
use ACA\WC\Search;
use WC_DateTime;
use WC_Subscription;

class StartDate extends SubscriptionDate {

	public function set_label() {
		$this->label = __( 'Start Date', 'woocommerce-subscriptions' );
	}

	public function get_date( WC_Subscription $subscription ) {
		$date = $subscription->get_date( 'date_created' );

		return $date ? new WC_DateTime( $date ) : null;
	}

	public function get_meta_key(): string
    {
		return '_schedule_start';
	}

	public function editing() {
		return new Editing\ShopSubscription\Date( 'date_created', $this->get_meta_key() );
	}

}
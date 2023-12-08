<?php

namespace ACA\WC\Field\ShopSubscription\SubscriptionDate;

use ACA\WC\Editing;
use ACA\WC\Field\ShopSubscription\SubscriptionDate;
use ACA\WC\Search;
use WC_DateTime;
use WC_Subscription;

class TrialEnd extends SubscriptionDate {

	public function set_label() {
		$this->label = __( 'Trial End', 'woocommerce-subscriptions' );
	}

	public function get_date( WC_Subscription $subscription ) {
		$date = $subscription->get_date( 'trial_end' );

		return $date ? new WC_DateTime( $date ) : null;
	}

	public function get_meta_key(): string
    {
		return '_schedule_trial_end';
	}

	public function editing() {
		return new Editing\ShopSubscription\Date( 'trial_end', $this->get_meta_key() );
	}

}
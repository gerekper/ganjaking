<?php

namespace ACA\WC\Field\ShopSubscription\SubscriptionDate;

use ACA\WC\Editing;
use ACA\WC\Field\ShopSubscription\SubscriptionDate;
use ACA\WC\Search;
use WC_DateTime;
use WC_Subscription;

class NextPayment extends SubscriptionDate {

	public function set_label() {
		$this->label = __( 'Next Payment', 'codepress-admin-columns' );
	}

	public function get_date( WC_Subscription $subscription ) {
		$date = $subscription->get_date( 'next_payment' );

		return $date ? new WC_DateTime( $date ) : null;
	}

	public function get_meta_key(): string
    {
		return '_schedule_next_payment';
	}

	public function editing() {
		return new Editing\ShopSubscription\Date( 'next_payment', $this->get_meta_key() );
	}

}
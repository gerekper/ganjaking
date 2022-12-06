<?php

namespace ACA\WC\Editing\ProductSubscription;

use ACA\WC\Editing\EditValue;
use ACA\WC\Editing\Product\ProductNotSupportedReasonTrait;
use ACA\WC\Editing\StorageModel;
use ACA\WC\Editing\View\SubscriptionPeriod;
use ACP\Editing\Service;
use ACP\Editing\View;

class Period implements Service, Service\Editability {

	use ProductNotSupportedReasonTrait;
	use ProductSubscriptionEditableTrait;

	const KEY_INTERVAL = '_subscription_period_interval';
	const KEY_PERIOD = '_subscription_period';

	public function get_value( $id ) {
		$product = wc_get_product( $id );

		return [
			'interval' => $product->get_meta( self::KEY_INTERVAL ),
			'period'   => $product->get_meta( self::KEY_PERIOD ),
		];
	}

	public function update( int $id, $data ): void {
		update_post_meta( $id, self::KEY_INTERVAL, $data['interval'] ?? '' );
		update_post_meta( $id, self::KEY_PERIOD, $data['period'] ?? '' );
	}

	public function get_view( string $context ): ?View {
		return new SubscriptionPeriod(
			wcs_get_subscription_period_interval_strings(),
			wcs_get_subscription_period_strings()
		);
	}

}
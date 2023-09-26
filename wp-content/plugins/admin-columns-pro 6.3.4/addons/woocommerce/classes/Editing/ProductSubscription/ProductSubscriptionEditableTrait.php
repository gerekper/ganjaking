<?php

namespace ACA\WC\Editing\ProductSubscription;

trait ProductSubscriptionEditableTrait {

	public function is_editable( int $id ): bool {
		$product = wc_get_product( $id );

		return $product && $product->get_type() === 'subscription';
	}

}
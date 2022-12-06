<?php

namespace ACA\WC\Export\ShopSubscription;

use ACA\WC\Column;
use ACA\WC\Field;
use ACP;

/**
 * @property Column\ShopOrder\OrderDate $column
 */
class SubscriptionDate extends ACP\Export\Model {

	public function __construct( Column\ShopSubscription\SubscriptionDate $column ) {
		parent::__construct( $column );
	}

	public function get_value( $id ) {
		$field = $this->column->get_field();

		if ( ! $field instanceof Field ) {
			return false;
		}

		$date = $field->get_date( wcs_get_subscription( $id ) );

		if ( ! $date instanceof \DateTime ) {
			return false;
		}

		return $date->format( 'Y-m-d H:i' );
	}

}
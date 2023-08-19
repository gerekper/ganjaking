<?php

namespace ACA\WC\Settings\ShopSubscription;

use ACA\WC;
use ACA\WC\Column;
use ACA\WC\Settings;

/**
 * @property Column\ShopSubscription\SubscriptionDate $column
 */
class SubscriptionDate extends Settings\DateType {

	public function __construct( Column\ShopSubscription\SubscriptionDate $column ) {
		parent::__construct( $column );
	}

	/**
	 * @return array
	 */
	protected function get_display_options() {
		$options = [];

		foreach ( $this->column->get_fields() as $field ) {
			/** @var WC\Field\ShopSubscription\SubscriptionDate $field */
			$options[ $field->get_key() ] = $field->get_label();
		}

		asort( $options );

		return $options;
	}

}
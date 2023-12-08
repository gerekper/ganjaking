<?php

namespace ACA\BP\Search\User;

use AC;
use AC\Helper\Select\Options;
use ACP\Search\Comparison;
use ACP\Search\Operators;

class Status extends Comparison\User\UserField
	implements Comparison\Values {

	public function __construct() {
		$operators = new Operators( [
			Operators::EQ,
		] );

		parent::__construct( $operators );
	}

	protected function get_field(): string
    {
		return 'user_status';
	}

	/**
	 * @inheritdoc
	 */
	public function get_values(): Options {
		return AC\Helper\Select\Options::create_from_array( [
			0 => __( 'Active', 'buddypress' ),
			1 => __( 'Spammer', 'buddypress' ),
		] );
	}

}
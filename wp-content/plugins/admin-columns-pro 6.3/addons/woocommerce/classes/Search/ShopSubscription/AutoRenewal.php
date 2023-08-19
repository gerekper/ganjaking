<?php

namespace ACA\WC\Search\ShopSubscription;

use AC\Helper\Select\Options;
use AC\MetaType;
use ACP\Search\Comparison;
use ACP\Search\Operators;

class AutoRenewal extends Comparison\Meta
	implements Comparison\Values {

	public function __construct() {
		$operators = new Operators( [ Operators::EQ ] );

		parent::__construct( $operators, '_requires_manual_renewal', MetaType::POST );
	}

	public function get_values() {
		return Options::create_from_array( [
			'false' => __( 'True' ),
			'true'  => __( 'False' ),
		] );
	}

}
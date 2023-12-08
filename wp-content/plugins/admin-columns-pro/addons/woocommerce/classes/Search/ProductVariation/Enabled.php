<?php

namespace ACA\WC\Search\ProductVariation;

use AC\Helper\Select;
use AC\Helper\Select\Options;
use ACP\Search\Comparison;
use ACP\Search\Operators;

class Enabled extends Comparison\Post\PostField implements Comparison\Values {

	public function __construct() {
		$operators = new Operators( [
			Operators::EQ,
		] );

		parent::__construct( $operators );
	}

	/**
	 * @return string
	 */
	protected function get_field(): string {
		return 'post_status';
	}

	public function get_values(): Options {
		return Select\Options::create_from_array( [
			'private' => __( 'False', 'codepress-admin-columns' ),
			'publish' => __( 'True', 'codepress-admin-columns' ),
		] );
	}

}
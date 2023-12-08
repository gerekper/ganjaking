<?php

namespace ACA\WC\Search\ShopOrder\Customer\Meta\Serialized;

use AC;
use AC\Helper\Select\Options;
use ACA\WC\Search\ShopOrder\Customer\Meta\Serialized;

class Role extends Serialized {

	/** @var array */
	private $roles;

	public function __construct( $roles ) {
		$this->roles = $roles;

		parent::__construct( 'wp_capabilities' );
	}

	/**
	 * @return Options
	 */
	public function get_values(): Options {
		return AC\Helper\Select\Options::create_from_array( $this->roles );
	}

}
<?php

namespace ACP\Sorting\Asset\Script;

use AC\Asset\Location;
use AC\Asset\Script;
use ACP\Sorting\Table\Preference;

final class Table extends Script {

	/** @var Preference */
	private $preference;

	/**
	 * @param string     $handle
	 * @param Location   $location
	 * @param Preference $preference
	 */
	public function __construct( $handle, Location $location, Preference $preference ) {
		parent::__construct( $handle, $location, [ 'jquery' ] );

		$this->preference = $preference;
	}

	public function register() {
		parent::register();

		wp_localize_script( $this->get_handle(), 'ACP_Sorting', [
			'order'   => $this->preference->get_order(),
			'orderby' => $this->preference->get_order_by(),
		] );
	}

}
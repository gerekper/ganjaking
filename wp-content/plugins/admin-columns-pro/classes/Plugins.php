<?php

namespace ACP;

use AC\Collection;
use AC\PluginInformation;

class Plugins extends Collection {

	public function __construct( array $items = [] ) {
		array_map( [ $this, 'add' ], $items );
	}

	public function add( PluginInformation $plugin ) {
		$this->put( $plugin->get_basename(), $plugin );

		return $this;
	}

	/**
	 * @return PluginInformation[]
	 */
	public function all() {
		return parent::all();
	}

}
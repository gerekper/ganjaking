<?php

namespace ACP\Updates;

use AC\Asset;
use AC\Registrable;
use ACP\Asset\Script\PluginUpdatesCheck;
use ACP\Transient\UpdateCheckTransient;

class PeriodicUpdateCheck implements Registrable {

	/**
	 * @var Asset\Location\Absolute
	 */
	private $location;

	/**
	 * @var UpdateCheckTransient
	 */
	private $cache;

	public function __construct( Asset\Location\Absolute $location, UpdateCheckTransient $cache ) {
		$this->location = $location;
		$this->cache = $cache;
	}

	public function register() {
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
	}

	public function enqueue_scripts() {
		if ( $this->cache->is_expired() ) {
			$script = new PluginUpdatesCheck( $this->location->with_suffix( 'assets/core/js/update-plugins-check.js' ) );
			$script->enqueue();
		}
	}

}
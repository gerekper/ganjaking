<?php

namespace WCML\HomeScreen;

use WCML\Utilities\Resources;
use WCML\Utilities\WcAdminPages;
use WCML\StandAlone\IStandAloneAction;

class Hooks implements \IWPML_Action, IStandAloneAction {

	public function add_hooks() {
		if ( WcAdminPages::isHomeScreen() ) {
			add_action( 'admin_enqueue_scripts', [ $this, 'enqueueAssets' ] );
		}
	}

	public function enqueueAssets() {
		$enqueue = Resources::enqueueApp( 'homeScreen' );
		$enqueue( [
			'name' => 'wcmlHomeScreen',
			'data' => [],
		] );

	}

}

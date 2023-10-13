<?php

namespace WCML\MultiCurrency;

use WCML\StandAlone\IStandAloneAction;

class GeolocationBackendHooks implements \IWPML_Backend_Action, IStandAloneAction {

	public function add_hooks() {
		add_filter( 'wcml_geolocation_is_used', [ Geolocation::class, 'isUsed' ] );
	}
}

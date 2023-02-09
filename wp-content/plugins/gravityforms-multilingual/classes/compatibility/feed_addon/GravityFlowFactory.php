<?php

namespace GFML\Compatibility\FeedAddon;

use function WPML\Container\make;

class GravityFlowFactory implements \IWPML_Backend_Action_Loader, \IWPML_Frontend_Action_Loader {

	public function create() {
		return new GravityFlow( make( \GFML_TM_API::class ), \Gravity_Flow::get_instance() );
	}
}

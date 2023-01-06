<?php

namespace WCML\Options;

use WPML\Settings\PostType\Automatic;
use WPML\Setup\Option;

class WPML {

	/** @return bool */
	public static function shouldTranslateEverything() {
		return method_exists( Option::class, 'shouldTranslateEverything' )
		       && Option::shouldTranslateEverything();
	}

	/**
	 * @param string $postType
	 * @param bool   $state
	 */
	public static function setAutomatic( $postType, $state ) {
		if ( method_exists( Automatic::class, 'set' ) ) {
			Automatic::set( $postType, $state );
		}
	}

	/**
	 * @return bool
	 */
	public static function useAte() {
		return method_exists( \WPML_TM_ATE_Status::class, 'is_enabled_and_activated' )
			&& \WPML_TM_ATE_Status::is_enabled_and_activated();
	}
}

<?php

namespace GFML;

use WPML\FP\Logic;

class Loader {

	public static function init() {
		self::load(
			[
				\GFML_Hooks::class,
				\WPML_GFML_Filter_Field_Meta::class,
				\WPML_GFML_Filter_Country_Field::class,
				Confirmation\SaveAndContinue::class,
			]
		);

		add_action( 'wpml_gfml_tm_api_loaded', [ self::class, 'loadCompatibilityHooks' ] );
	}

	/**
	 * @return void
	 */
	public static function loadCompatibilityHooks() {
		$filteredHooks = wpml_collect(
			[
				\WPML_GF_Quiz::class                      => defined( 'GF_QUIZ_VERSION' ),
				\WPML_GF_Survey::class                    => defined( 'GF_SURVEY_VERSION' ),
				Compatibility\UserRegistration\Hooks::class => defined( 'GF_USER_REGISTRATION_VERSION' ),
				Compatibility\FeedAddon\GravityFlowFactory::class => defined( 'GRAVITY_FLOW_VERSION' ),
				Compatibility\Woocommerce\Currency::class => defined( 'WCML_VERSION' ) && wcml_is_multi_currency_on(),
			]
		)->filter( Logic::isTruthy() )
		   ->keys()
		   ->toArray();

		self::load( $filteredHooks );
	}

	/**
	 * @param string[] $hooks
	 *
	 * @return void
	 */
	private static function load( $hooks ) {
		( new \WPML_Action_Filter_Loader() )->load( $hooks );
	}
}

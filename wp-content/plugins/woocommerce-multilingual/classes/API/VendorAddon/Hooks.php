<?php

namespace WCML\API\VendorAddon;

use WPML\Element\API\Languages;
use WPML\FP\Cast;
use WPML\FP\Fns;
use WPML\FP\Obj;
use WPML\FP\Relation;
use WPML\LIB\WP\Hooks as WpHooks;
use function WPML\Container\make;
use function WPML\FP\invoke;
use function WPML\FP\pipe;
use function WPML\FP\spreadArgs;

class Hooks implements \IWPML_Backend_Action {

	const TRANSLATE_CAPABILITY = 'translate';

	const COLUMN_USER_OPTION = 'manageedit-productcolumnshidden';

	public function add_hooks() {
		/**
		 * This filter allows to enable this module
		 * by passing an array of configuration.
		 *
		 * @since 4.12.0
		 *
		 * @return null|array
		 */
		$config = apply_filters( 'wcml_vendor_addon_configuration', null );

		if ( $config ) {
			// $getConfig - Curried :: string -> mixed
			$getConfig = Obj::prop( Fns::__, $config );

			// $isVendor - Curried :: \WP_User -> bool
			$isVendor = invoke( 'has_cap' )->with( $getConfig( 'vendor_capability' ) );

			WpHooks::onFilter( 'wpml_override_is_translator', 10, 3 )
				->then( spreadArgs( self::allowVendorToTranslateHisProduct( $isVendor ) ) );

			WpHooks::onFilter( 'get_user_metadata', 10, 3 )
				->then( spreadArgs( self::forceLanguagesColumnInProductsList( $isVendor ) ) );
		}
	}

	/**
	 * @param callable $isVendor
	 *
	 * @return \Closure
	 */
	private static function allowVendorToTranslateHisProduct( callable $isVendor ) {
		// (bool, int, array) -> bool
		return function( $isTranslator, $userId, $args ) use ( $isVendor ) {
			$user = self::getUser( $userId );

			if ( $isVendor( $user ) ) {
				self::maybeSetTranslatorRequirements( $user );

				// $isProductOfVendor :: array -> bool
				$isProductOfVendor = pipe(
					Obj::prop( 'post_id' ),
					'get_post',
					Obj::prop( 'post_author' ),
					Cast::toInt(),
					Relation::equals( $userId )
				);

				return $isProductOfVendor( $args );
			}

			return $isTranslator;
		};
	}

	private static function maybeSetTranslatorRequirements( \WPML_User $user ) {
		/** @var \wpdb $wpdb */
		global $wpdb;

		$languagePairsKey = $wpdb->prefix . 'language_pairs';

		if (
			! $user->has_cap( self::TRANSLATE_CAPABILITY )
			|| $user->get( $languagePairsKey ) !== self::getAllLanguagePairsInDefault()
		) {
			$user->add_cap( self::TRANSLATE_CAPABILITY );
			$user->update_meta( $languagePairsKey, self::getAllLanguagePairsInDefault() );

			do_action( 'wpml_tm_ate_synchronize_translators' );
			do_action( 'wpml_tm_add_translation_role', $user, self::TRANSLATE_CAPABILITY );
		}
	}

	/**
	 * @return array
	 */
	private static function getAllLanguagePairsInDefault() {
		return [ Languages::getDefaultCode() => array_fill_keys( Languages::getSecondaryCodes(), 1 ) ];
	}

	/**
	 * @param callable $isVendor
	 *
	 * @return \Closure
	 */
	public static function forceLanguagesColumnInProductsList( callable $isVendor ) {
		// (mixed, int, string) -> mixed
		return function ( $value, $userId, $metaKey ) use ( $isVendor ) {
			if (
				self::COLUMN_USER_OPTION === $metaKey
				&& $isVendor( self::getUser( $userId ) )
			) {
				return [ [] ];
			}

			return $value;
		};
	}

	/**
	 * @param int $id
	 *
	 * @return \WPML_User
	 */
	private static function getUser( $id ) {
		return make( \WPML_WP_User_Factory::class )->create( $id );
	}
}

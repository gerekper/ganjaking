<?php

namespace WPML\Element\API;

use WPML\Collect\Support\Traits\Macroable;
use WPML\FP\Fns;
use function WPML\FP\curryN;

/**
 * @method static array getActive()
 *
 * It returns an array of the active languages.
 *
 * The returned array is indexed by language code and every element has the following structure:
 * ```
 *  'fr' => [
 *      'code'           => 'fr',
 *      'id'             => 3,
 *      'english_name'   => 'French',
 *      'native_name'    => 'Français',
 *      'major'          => 1,
 *      'default_locale' => 'fr_FR',
 *      'encode_url'     => 0,
 *      'tag'            => 'fr ,
 *      'display_name'   => 'French
 *  ]
 * ```
 *
 * @method static callable|string getFlagUrl( ...$code ) - Curried :: string → string
 *
 * Gets the flag url for the given language code.
 *
 * @method static callable|array withFlags( ...$langs ) - Curried :: [code => lang] → [code => lang]
 *
 * Adds the language flag url to the array of languages.
 *
 * @method static callable|array getAll() void → [lang]
 *
 * It returns an array of the all the languages.
 *
 * The returned array is indexed by language code and every element has the following structure:
 * ```
 *  'fr' => [
 *      'code'           => 'fr',
 *      'id'             => 3,
 *      'english_name'   => 'French',
 *      'native_name'    => 'Français',
 *      'major'          => 1,
 *      'default_locale' => 'fr_FR',
 *      'encode_url'     => 0,
 *      'tag'            => 'fr ,
 *      'display_name'   => 'French
 *  ]
 * ```
 */
class Languages {
	use Macroable;

	/**
	 * @ignore
	 */
	public static function init() {
		global $sitepress;

		self::macro( 'getActive', [ $sitepress, 'get_active_languages' ] );

		self::macro( 'getAll', [ $sitepress, 'get_languages' ] );

		self::macro( 'getFlagUrl', curryN( 1, [ $sitepress, 'get_flag_url' ] ) );

		self::macro( 'withFlags', curryN( 1, function ( $langs ) {
			$addFlag = function ( $lang, $code ) {
				$lang['flag_url'] = self::getFlagUrl( $code );

				return $lang;
			};

			return Fns::map( $addFlag, $langs );
		} ) );

	}

	/**
	 * Curried :: string → bool
	 * Determine if the language is Right to Left
	 *
	 * @param string|null $code
	 *
	 * @return callable|bool
	 */
	public static function isRtl( $code = null ) {
		$isRtl = function ( $code ) {
			global $sitepress;

			return $sitepress->is_rtl( $code );
		};

		return call_user_func_array( curryN( 1, $isRtl ), func_get_args() );
	}

	/**
	 * Curried :: [code => lang] → [code => lang]
	 *
	 * Adds language direction, right to left, to the languages data
	 *
	 * @param array|null $langs
	 *
	 * @return callable|array
	 */
	public static function withRtl( $langs = null ) {
		$withRtl = function ( $langs ) {
			$addRtl = function ( $lang, $code ) {
				$lang['rtl'] = self::isRtl( $code );

				return $lang;
			};

			return Fns::map( $addRtl, $langs );
		};

		return call_user_func_array( curryN( 1, $withRtl ), func_get_args() );
	}
}

Languages::init();

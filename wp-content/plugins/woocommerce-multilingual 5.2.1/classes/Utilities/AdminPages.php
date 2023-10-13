<?php

namespace WCML\Utilities;

use WCML_Admin_Menus;
use WCML_Capabilities;
use WPML\FP\Lst;
use WPML\FP\Obj;
use WPML\FP\Relation;
use function sanitize_key;
use function WCML\functions\isStandAlone;

class AdminPages {

	const TAB_MULTICURRENCY = 'multi-currency';
	const TAB_PRODUCTS      = 'products';

	/**
	 * @return string
	 */
	public static function getDefaultTab() {
		return isStandAlone() ? self::TAB_MULTICURRENCY : self::TAB_PRODUCTS;
	}

	/**
	 * @param null|string $fallback
	 *
	 * @return string|null
	 */
	public static function getCurrentTab( $fallback = null ) {
		return sanitize_key( Obj::prop( 'tab', $_GET ) ) ?: $fallback;
	}

	/**
	 * @return string
	 */
	public static function getTabToDisplay() {
		return WCML_Capabilities::canAccessAllWcmlTabs()
			? self::getCurrentTab( self::getDefaultTab() )
			: self::getDefaultTab();
	}

	/**
	 * @param string|array $tabs A single tab (string) or one of multiple tabs (array).
	 *
	 * @return bool
	 */
	public static function isTab( $tabs ) {
		return Lst::includes( self::getCurrentTab(), (array) $tabs );
	}

	/**
	 * @param string $page
	 *
	 * @return bool
	 */
	public static function isPage( $page ) {
		return Relation::propEq( 'page', $page, $_GET );
	}

	/**
	 * @return bool
	 */
	public static function isWcmlSettings() {
		return self::isPage( WCML_Admin_Menus::SLUG );
	}

	/**
	 * @return bool
	 */
	public static function isMultiCurrency() {
		$tabs = [ self::TAB_MULTICURRENCY ];

		if ( isStandAlone() ) {
			$tabs[] = null; // Also the default tab in Standalone mode.
		}

		return self::isWcmlSettings() && self::isTab( $tabs );
	}

	/**
	 * @return bool
	 */
	public static function isTranslationQueue() {
		return ! isStandAlone() && self::isTmPage( '/menu/translations-queue.php' );
	}

	/**
	 * @return bool
	 */
	public static function isTranslationsDashboard() {
		return ! isStandAlone() && self::isTmPage( '/menu/main.php' );
	}

	/**
	 * @param string $path Path after the TM folder (e.g. '/menu/main.php')
	 *
	 * @return bool
	 */
	private static function isTmPage( $path ) {
		return defined( 'WPML_TM_FOLDER' ) && self::isPage( WPML_TM_FOLDER . $path );
	}
}

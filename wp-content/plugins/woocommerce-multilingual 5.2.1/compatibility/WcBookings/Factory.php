<?php

namespace WCML\Compatibility\WcBookings;

use WCML\Compatibility\ComponentFactory;
use WCML\StandAlone\IStandAloneAction;
use function WCML\functions\getSitePress;
use function WCML\functions\getWooCommerceWpml;
use function WCML\functions\isStandAlone;

class Factory extends ComponentFactory implements IStandAloneAction {

	/**
	 * @inheritDoc
	 */
	public function create() {
		$hooks = [
			new SharedHooks( self::getWpdb() ),
		];

		if ( wcml_is_multi_currency_on() ) {
			$hooks[] = new MulticurrencyHooks( getWooCommerceWpml() );
		}

		if ( ! isStandAlone() ) {
			$hooks[] = new \WCML_Bookings(
				getSitePress(),
				getWooCommerceWpml(),
				self::getWooCommerce(),
				self::getWpdb(),
				self::getElementTranslationPackage(),
				self::getPostTranslations()
			);
		}

		if ( defined( 'WC_ACCOMMODATION_BOOKINGS_VERSION' ) && wcml_is_multi_currency_on() ) {
			$hooks[] = new \WCML_Accommodation_Bookings( getWooCommerceWpml() );
		}

		return $hooks;
	}
}

<?php

namespace WCML\Compatibility\WcBookings\Templates;

use WPML\Element\API\Languages;
use WPML\Element\API\Post;
use WPML\Element\API\PostTranslations;
use WPML\LIB\WP\Hooks;

use function WPML\FP\spreadArgs;

class MyBookings implements \IWPML_Action {

	public function add_hooks() {
		Hooks::onFilter( 'woocommerce_bookings_account_tables' )
			->then( spreadArgs( [ $this, 'filterByCurrentLanguage' ] ) );
	}

	/**
	 * @param array[] $tables
	 *
	 * @return array[]
	 */
	public function filterByCurrentLanguage( $tables ) {

		$currentLanguage = Languages::getCurrentCode();

		foreach ( $tables as $section => $table ) {
			if ( isset( $table['bookings'] ) ) {
				foreach ( $table['bookings'] as $key => $booking ) {
					$languageCode = Post::getLang( $booking->get_id() );

					// Remove bookings in other languages.
					if ( $languageCode !== $currentLanguage ) {
						unset( $tables[ $section ]['bookings'][ $key ] );

					// Fallback to original (display-as-translated).
					} elseif ( ! $booking->get_product() ) {
						$originalBookingId = PostTranslations::getOriginalId( $booking->get_id() );
						$originalBooking   = get_wc_booking( $originalBookingId );
						if ( $originalBooking ) {
							$booking->set_product_id( $originalBooking->get_product_id() );
						}
					}
				}
			}

			$tables[ $section ]['bookings'] = array_values( $tables[ $section ]['bookings'] );
		}

		return $tables;
	}

}

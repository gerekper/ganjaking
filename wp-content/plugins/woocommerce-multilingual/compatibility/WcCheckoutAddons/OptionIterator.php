<?php

namespace WCML\Compatibility\WcCheckoutAddons;

class OptionIterator {

	/**
	 * @param callable    $handler
	 * @param array|mixed $optionValue
	 *
	 * @return array|mixed
	 */
	public static function apply( callable $handler, $optionValue ) {
		if ( is_array( $optionValue ) ) {

			foreach ( $optionValue as $addonId => $addonConf ) {
				$addonConf = $handler( $addonId, $addonConf );

				if ( isset( $addonConf['options'] ) ) {
					foreach ( $addonConf['options'] as $index => $fields ) {
						$addonConf['options'][ $index ] = $handler( $index, $fields );
					}
				}

				$optionValue[ $addonId ] = $addonConf;
			}
		}

		return $optionValue;
	}
}

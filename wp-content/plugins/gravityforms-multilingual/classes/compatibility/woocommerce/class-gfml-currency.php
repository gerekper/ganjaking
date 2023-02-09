<?php

namespace GFML\Compatibility\Woocommerce;

class Currency implements \IWPML_Backend_Action, \IWPML_Frontend_Action {
	const FIELD_TYPES = [ 'product', 'option', 'shipping', 'total' ];

	public function add_hooks() {
		add_filter( 'gform_currency', [ $this, 'applyCurrency' ] );
		add_filter( 'gform_pre_render', [ $this, 'applyPriceCalculation' ] );
	}

	/**
	 * @param string $currency
	 *
	 * @return string
	 */
	public function applyCurrency( $currency ) {

		return apply_filters( 'wcml_price_currency', $currency );
	}

	/**
	 * @param array $form
	 *
	 * @return array
	 */
	public function applyPriceCalculation( $form ) {

		$convertFieldPrice = function( $data ) {
			if ( in_array( $data->type, self::FIELD_TYPES, true ) ) {

				if ( ! empty( $data->choices ) ) {
					foreach ( $data->choices as &$choice ) {
						$choice['price'] = $this->processPrice( $choice['price'] );
					}
				}

				/* phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase */
				if ( ! empty( $data->basePrice ) ) {
					/* phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase */
					$data->basePrice = $this->processPrice( $data->basePrice );
				}
			}

			return $data;
		};

		foreach ( $form['fields'] as $field ) {

			$field = $convertFieldPrice( $field );
		}

		return $form;
	}

	/**
	 * @param string $formattedPrice
	 *
	 * @return float
	 */
	private function processPrice( $formattedPrice ) {

		return $this->getConvertedPrice( $this->getRawPrice( $formattedPrice ) );
	}

	/**
	 * @param float $price
	 *
	 * @return float
	 */
	private function getConvertedPrice( $price ) {

		return apply_filters( 'wcml_raw_price_amount', $price );
	}

	/**
	 * @param string $price
	 *
	 * @return float
	 */
	private function getRawPrice( $price ) {

		$price = preg_replace( [ '~&.*?;~', '~[\p{Sc}\s]+~u' ], '', $price );

		return floatval( trim( $price ) );
	}

}

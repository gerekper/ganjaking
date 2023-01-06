<?php

namespace WCML\Compatibility\WoofWcProductFilter;

class MulticurrencyHooks implements \IWPML_Action {
	/**
	 * @var string
	 */
	private $currentCurrency;
	/**
	 * @var string
	 */
	private $defaultCurrency;
	/**
	 * @var array
	 */
	private $rates;

	/**
	 * Registers hooks.
	 */
	public function add_hooks() {
		add_action( 'init', [ $this, 'setupCurrencies' ] );
		add_filter( 'woof_get_meta_query', [ $this, 'priceInDefaultCurrency' ], 10, 1 );
		add_filter( 'wcml_exchange_rates', [ $this, 'storeExchangeRates' ], 10, 1 );
	}

	/**
	 * Stores default and current currency in attributes.
	 */
	public function setupCurrencies() {
		$this->defaultCurrency = wcml_get_woocommerce_currency_option();
		$this->currentCurrency = apply_filters( 'wcml_price_currency', $this->defaultCurrency );
	}

	/**
	 * Filters meta query from WOOf plugin and maybe replaces the prices with the one
	 * in default currency.
	 *
	 * @param array $metaQuery
	 *
	 * @return mixed
	 */
	public function priceInDefaultCurrency( $metaQuery ) {
		if ( $this->priceIsInSwitchedCurrency() ) {
			foreach ( $metaQuery as $queryIndex => $queryMeta ) {
				if ( $this->isMetaWithPriceValues( $queryIndex, $queryMeta ) ) {
					foreach ( $queryMeta['value'] as $valueIndex => $valuePrice ) {
						$metaQuery[ $queryIndex ]['value'][ $valueIndex ] = $this->getPriceInDefaultCurrency( $valuePrice );
					}
				}
			}
		}
		return $metaQuery;
	}

	/**
	 * Stores exchange rates in attribute.
	 *
	 * @param array $rates
	 *
	 * @return array
	 */
	public function storeExchangeRates( $rates ) {
		$this->rates = $rates;
		return $rates;
	}

	/**
	 * Checks if price is in non default currency.
	 *
	 * @return bool
	 */
	private function priceIsInSwitchedCurrency() {
		return $this->currentCurrency !== $this->defaultCurrency;
	}

	/**
	 * Checks if meta data has a correct format.
	 *
	 * @param int   $index
	 * @param array $meta
	 *
	 * @return bool
	 */
	private function isMetaWithPriceValues( $index, $meta ) {
		return is_numeric( $index )
			   && isset( $meta['key'], $meta['value'] )
			   && '_price' === $meta['key']
			   && is_array( $meta['value'] );
	}

	/**
	 * Recalculates price into default currency.
	 *
	 * @param int|string $valuePrice
	 *
	 * @return int|string mixed
	 */
	private function getPriceInDefaultCurrency( $valuePrice ) {
		if ( isset( $this->rates[ $this->currentCurrency ] ) ) {
			$valuePrice /= $this->rates[ $this->currentCurrency ];
		}
		return $valuePrice;
	}

}

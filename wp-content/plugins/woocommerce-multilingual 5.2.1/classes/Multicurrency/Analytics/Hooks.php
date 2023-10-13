<?php

namespace WCML\Multicurrency\Analytics;

use WCML\Utilities\Resources;
use WCML\Rest\Functions;
use WCML\StandAlone\IStandAloneAction;
use WPML\FP\Obj;
use WPML\FP\Fns;
use WCML\Utilities\WpAdminPages;

class Hooks implements \IWPML_Action, IStandAloneAction {

	/** @var \woocommerce_wpml $woocommerce_wpml */
	private $woocommerce_wpml;

	/** @var \wpdb $wpdb */
	private $wpdb;

	/** @var string $requestedCurrencyForReport */
	private $requestedCurrencyForReport;

	public function __construct( \woocommerce_wpml $woocommerce_wpml, \wpdb $wpdb ) {
		$this->woocommerce_wpml = $woocommerce_wpml;
		$this->wpdb             = $wpdb;
	}

	public function add_hooks() {
		if ( Functions::isAnalyticsPage() ) {
			add_action( 'admin_enqueue_scripts', [ $this, 'enqueueAssets' ] );
		}

		wpml_collect(
			[
				'revenue',
				'orders',
				'orders_stats',
				'products',
				'products_stats',
				'categories',
				'coupons',
				'coupons_stats',
				'taxes',
				'taxes_stats',
				'variations',
				'variations_stats',
			]
		)->each(
			function( $item ) {
				add_filter( "woocommerce_analytics_{$item}_query_args", [ $this, 'addCurrencyArg' ] );
			}
		);

		wpml_collect(
			[
				'products',
				'orders',
				'variations',
				'categories',
				'coupons',
				'taxes',
			]
		)->each(
			function( $item ) {
				add_filter( "woocommerce_analytics_clauses_where_{$item}_subquery", [ $this, 'addWhere' ] );
				add_filter( "woocommerce_analytics_clauses_where_{$item}_stats_total", [ $this, 'addWhere' ] );
				add_filter( "woocommerce_analytics_clauses_where_{$item}_stats_interval", [ $this, 'addWhere' ] );
			}
		);

		add_action( 'woocommerce_admin_report_export', [ $this, 'saveRequestedCurrencyForReport' ], -PHP_INT_MAX, 4 );
	}

	public function enqueueAssets() {
		$multiCurrency = $this->woocommerce_wpml->get_multi_currency();
		$currencies    = $multiCurrency->get_currency_codes();
		$enqueue       = Resources::enqueueApp( 'multicurrencyAnalytics' );

		$enqueue(
			[
				'name' => 'wcmlAnalytics',
				'data' => [
					'strings'         => [
						'currencyLabel' => __( 'Currency', 'woocommerce-multilingual' ),
					],
					'filterItems'     => $this->getCurrencyFilterItems( $currencies ),
					'currencyConfigs' => $this->getCurrencyConfigs( $currencies, $multiCurrency ),
				],
			]
		);
	}

	/**
	 * Labels for the currency dropdown (filter).
	 *
	 * @param array $currencies
	 *
	 * @return array
	 */
	private function getCurrencyFilterItems( $currencies ) {
		$currencyLabels = get_woocommerce_currencies();

		return wpml_collect( $currencies )
			->map(
				function( $currency ) use ( $currencyLabels ) {
					return [
						'value' => $currency,
						'label' => $currencyLabels[ $currency ],
					];
				}
			)->toArray();
	}

	/**
	 * Currency settings for displaying prices.
	 *
	 * @param array                $currencies
	 * @param \WCML_Multi_Currency $multiCurrency
	 *
	 * @return array
	 */
	private function getCurrencyConfigs( $currencies, $multiCurrency ) {
		return wpml_collect( $currencies )
			->mapWithKeys(
				function( $currency ) use ( $multiCurrency ) {
					$get = Obj::prop( Fns::__, $multiCurrency->get_currency_details_by_code( $currency ) );

					return [
						$currency => [
							'code'              => $currency,
							'symbol'            => html_entity_decode( get_woocommerce_currency_symbol( $currency ) ),
							'symbolPosition'    => $get( 'position' ),
							'thousandSeparator' => $get( 'thousand_sep' ),
							'decimalSeparator'  => $get( 'decimal_sep' ),
							'precision'         => $get( 'num_decimals' ),
						],
					];
				}
			)->toArray();
	}

	/**
	 * @param array $args
	 *
	 * @return array
	 */
	public function addCurrencyArg( $args ) {
		return Obj::assoc( 'currency', $this->getCurrency(), $args );
	}

	/**
	 * @param array $clauses
	 *
	 * @return array
	 */
	public function addWhere( $clauses ) {
		$clauses[] = $this->wpdb->prepare(
			"AND EXISTS (
				SELECT 1 FROM {$this->wpdb->postmeta}
				  WHERE post_id = {$this->wpdb->prefix}wc_order_stats.order_id
				  AND meta_key = '_order_currency'
				  AND meta_value = %s
			)",
			$this->getCurrency()
		);

		return $clauses;
	}

	/**
	 * @param int    $page
	 * @param string $id
	 * @param string $type
	 * @param array  $args
	 */
	public function saveRequestedCurrencyForReport( $page, $id, $type, $args ) {
		if ( 'orders' === $type && isset( $args['currency'] ) ) {
			$this->requestedCurrencyForReport = $args['currency'];
		}
	}

	private function getCurrency() {
		if ( ! empty( $this->requestedCurrencyForReport ) ) {
			return $this->requestedCurrencyForReport;
		}

		$rawCurrency = WpAdminPages::isDashboard()
			? Obj::prop( '_wcml_dashboard_currency', $_COOKIE )
			: Obj::prop( 'currency', $_GET );

		return $rawCurrency
			? sanitize_text_field( wp_unslash( $rawCurrency ) )
			: wcml_get_woocommerce_currency_option();
	}

}

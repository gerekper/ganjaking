<?php

namespace WCML\Multicurrency\Analytics;

use Closure;
use WPML\FP\Obj;

class Export implements \IWPML_Backend_Action, \IWPML_Frontend_Action, \IWPML_DIC_Action {

	/** @var \wpdb $wpdb */
	private $wpdb;

	public function __construct( \wpdb $wpdb ) {
		$this->wpdb = $wpdb;
	}

	public function add_hooks() {
		add_filter( 'woocommerce_admin_report_columns', $this->addSelect( 'language', 'wpml_language' ), 10, 2 );
		add_filter( 'woocommerce_report_orders_export_columns', Obj::assoc( 'language', __( 'Language', 'woocommerce-multilingual' ) ) );
		add_filter( 'woocommerce_report_orders_prepare_export_item', $this->copyProp( 'language' ), 10, 2 );

		if ( wcml_is_multi_currency_on() ) {
			add_filter( 'woocommerce_admin_report_columns', $this->addSelect( 'currency', '_order_currency' ), 10, 2 );
			add_filter( 'woocommerce_report_orders_export_columns', Obj::assoc( 'currency', __( 'Currency', 'woocommerce-multilingual' ) ) );
			add_filter( 'woocommerce_report_orders_prepare_export_item', $this->copyProp( 'currency' ), 10, 2 );
			add_filter( 'woocommerce_report_products_export_columns', Obj::assoc( 'currency', __( 'Currency', 'woocommerce-multilingual' ) ) );
			add_filter( 'woocommerce_report_products_prepare_export_item', $this->copyProp( 'currency' ), 10, 2 );
		}
	}

	/**
	 * @param string $columnName
	 * @param string $metaKey
	 *
	 * @return callable ( array, string ) → array
	 */
	public function addSelect( $columnName, $metaKey ) {
		return function( $columns, $context ) use ( $columnName, $metaKey ) {
			if ( 'orders' === $context ) {
				return Obj::assoc(
					$columnName,
					$this->wpdb->prepare(
						"(SELECT DISTINCT meta_value FROM {$this->wpdb->postmeta} WHERE post_id = {$this->wpdb->prefix}wc_order_stats.order_id AND meta_key = %s) AS %s",
						$metaKey,
						$columnName
					),
					$columns
				);
			}

			return $columns;
		};
	}

	/**
	 * @param string $column
	 *
	 * @return callable ( array, array ) → array
	 */
	public function copyProp( $column ) {
		return function ( $export_item, $item ) use ( $column ) {
			return Obj::assoc( $column, Obj::prop( $column, $item ), $export_item );
		};
	}

}

<?php

namespace ACA\WC;

use AC;
use ACA\WC\Column;
use ACA\WC\ListScreen\Product;
use WC_Subscriptions;

/**
 * @since 3.4
 */
final class Subscriptions implements AC\Registerable {

	/**
	 * @return bool
	 */
	private function is_wc_subscriptions_active() {
		if ( ! class_exists( 'WC_Subscriptions', false ) ) {
			return false;
		}

		return version_compare( WC_Subscriptions::$version, '2.6', '>=' );
	}

	public function register() {
		if ( ! $this->is_wc_subscriptions_active() ) {
			return;
		}

		add_action( 'ac/column_groups', [ $this, 'register_column_groups' ] );
		add_action( 'ac/list_screens', [ $this, 'register_list_screens' ] );
		add_action( 'ac/column_types', [ $this, 'add_product_columns' ] );
		add_action( 'ac/column_types', [ $this, 'add_user_columns' ] );
	}

	public function register_list_screens( AC\ListScreens $list_screens ) {
		$list_screens->register_list_screen( new ListScreen\Subscriptions() );
	}

	/**
	 * @param AC\Groups $groups
	 */
	public function register_column_groups( $groups ) {
		$groups->register_group( 'woocommerce_subscriptions', __( 'WooCommerce Subscriptions', 'codepress-admin-columns' ), 15 );
	}

	public function add_product_columns( AC\ListScreen $list_screen ) {
		if ( $list_screen instanceof Product ) {
			$columns = [
				Column\ProductSubscription\Expires::class,
				Column\ProductSubscription\FreeTrial::class,
				Column\ProductSubscription\LimitSubscription::class,
				Column\ProductSubscription\Period::class,
			];

			foreach ( $columns as $column ) {
				$list_screen->register_column_type( new $column );
			}
		}
	}

	public function add_user_columns( AC\ListScreen $list_screen ) {
		if ( $list_screen instanceof AC\ListScreen\User ) {
			$columns = [
				Column\UserSubscription\ActiveSubscriber::class,
				Column\UserSubscription\InactiveSubscriber::class,
				Column\UserSubscription\Subscriptions::class,
			];

			foreach ( $columns as $column ) {
				$list_screen->register_column_type( new $column );
			}
		}

	}

}
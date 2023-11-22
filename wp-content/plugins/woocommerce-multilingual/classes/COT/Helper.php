<?php

namespace WCML\COT;

use Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController;
use Automattic\WooCommerce\Internal\DataStores\Orders\DataSynchronizer;
use Automattic\WooCommerce\Internal\DataStores\Orders\OrdersTableDataStore;
use Automattic\WooCommerce\Utilities\OrderUtil;
use WPML\FP\Maybe;
use WPML\FP\Obj;
use function WPML\FP\invoke;

class Helper {


	/**
	 * HPOS constant for CRUD pages for orders (admin.php?page=wc-orders)
	 *
	 * @var string
	 *
	 * @see \Automattic\WooCommerce\Utilities\OrderUtil::get_order_admin_screen()
	 */
	const WC_ORDERS = 'wc-orders';

	/**
	 * Determines if the HPOS table is already created or not.
	 *
	 * @return bool
	 */
	public static function getTableExists() {
		return self::callStaticMethod( DataSynchronizer::class, 'get_table_exists', false ); // @phpstan-ignore-line
	}

	/**
	 * The name of the custom order table.
	 *
	 * @return string|null
	 */
	public static function getTableName() {
		return self::callStaticMethod( OrdersTableDataStore::class, 'get_orders_table_name', null ); // @phpstan-ignore-line
	}

	/**
	 * The name of the custom order meta table.
	 *
	 * @return string|null
	 */
	public static function getMetaTableName() {
		return self::callStaticMethod( OrdersTableDataStore::class, 'get_meta_table_name', null ); // @phpstan-ignore-line
	}

	/**
	 * Determine if the custom order table is in usage.
	 *
	 * @return bool
	 */
	public static function isUsageEnabled() {
		return self::callStaticMethod( CustomOrdersTableController::class, 'custom_orders_table_usage_is_enabled', false ); // @phpstan-ignore-line
	}

	/**
	 * Checks if passed id is a WC_Order object by calling Automattic\WooCommerce\Utilities\OrderUtil::is_order()
	 *
	 * @param int $id
	 * @return bool
	 */
	public static function isOrder( int $id ) : bool {
		return OrderUtil::is_order( $id ); // @phpstan-ignore-line
	}

	/**
	 * @param string $wcClass
	 * @param string $method
	 * @param mixed  $default
	 *
	 * @return mixed
	 */
	private static function callStaticMethod( $wcClass, $method, $default ) {
		return Maybe::fromNullable( self::getFromContainer( $wcClass ) )
			->map( invoke( $method ) )
			->getOrElse( $default );
	}

	/**
	 * @param string $wcClass
	 *
	 * @return mixed|object|null
	 */
	private static function getFromContainer( $wcClass ) {
		try {
			$object = \wc_get_container()->get( $wcClass );
		} catch ( \Exception $e ) {
			return null;
		}

		return $object;
	}

	/**
	 * Checks if the current screen is an admin screen for WooCommerce HPOS
	 *
	 * @param string|null $action
	 * @return bool
	 */
	private static function isOrderAdminScreen( $action ) {
		return is_admin()
			&& Obj::prop( 'page', $_GET ) === self::WC_ORDERS
			&& Obj::prop( 'action', $_GET ) === $action;
	}

	/**
	 * Checks if the current screen is an admin screen for WooCommerce New Order with the HPOS.
	 *
	 * @return bool
	 */
	public static function isOrderCreateAdminScreen() {
		return self::isOrderAdminScreen( 'new' );
	}

	/**
	 * Checks if the current screen is an admin screen for list of WooCommerce orders with the HPOS.
	 *
	 * @return bool
	 */
	public static function isOrderListAdminScreen() {
		return self::isOrderAdminScreen( null );
	}

	/**
	 * Checks if the current screen is an admin screen for WooCommerce Edit Order with the HPOS.
	 *
	 * @return bool
	 */
	public static function isOrderEditAdminScreen() {
		return self::isOrderAdminScreen( 'edit' ) && isset( $_GET['id'] );
	}

}

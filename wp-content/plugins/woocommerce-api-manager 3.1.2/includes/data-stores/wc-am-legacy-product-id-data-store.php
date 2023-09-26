<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * WooCommerce API Manager Legacy Product ID Data Store Class
 *
 * @since       2.7
 *
 * @author      Todd Lahman LLC
 * @copyright   Copyright (c) Todd Lahman LLC
 * @package     WooCommerce API Manager/Legacy Product ID Data Store
 */
class WC_AM_Legacy_Product_ID_Data_Store {

	private $legacy_product_id_table = '';

	/**
	 * @var null
	 */
	private static $_instance = null;

	/**
	 * @static
	 * @return null|\WC_AM_Associated_API_Key_Data_Store
	 */
	public static function instance() {

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	private function __construct() {
		$this->legacy_product_id_table = WC_AM_USER()->get_legacy_product_id_table_name();
	}

	/**
	 * Returns true if the Legacy Product ID String exists.
	 *
	 * @since 2.7
	 *
	 * @param string $product_id_title
	 *
	 * @return bool
	 */
	public function has_legacy_product_id( $product_id_title ) {
		global $wpdb;

		$sql = "
			SELECT legacy_product_id
			FROM {$wpdb->prefix}" . $this->legacy_product_id_table . "
			WHERE product_id_title = %s
		";

		return ! WC_AM_FORMAT()->empty( $wpdb->get_var( $wpdb->prepare( $sql, $product_id_title ) ) );
	}

	/**
	 * Returns true if the Legacy Product ID String exists.
	 *
	 * @since 2.7.2
	 *
	 * @param int $product_id_integer
	 *
	 * @return bool
	 */
	public function has_legacy_product_id_title( $product_id_integer ) {
		global $wpdb;

		$sql = "
			SELECT product_id_title
			FROM {$wpdb->prefix}" . $this->legacy_product_id_table . "
			WHERE product_id_integer = %d
		";

		return ! WC_AM_FORMAT()->empty( $wpdb->get_var( $wpdb->prepare( $sql, $product_id_integer ) ) );
	}

	/**
	 * Returns the product_id_title that matches the product_id_integer.
	 *
	 * @since 2.7.2
	 *
	 * @param $product_id_integer
	 * @param $key
	 *
	 * @return string
	 */
	public function get_product_id_title( $product_id_integer, $key = '_api_resource_title' ) {
		global $wpdb;

		$sql = "
			SELECT product_id_title
			FROM {$wpdb->prefix}" . $this->legacy_product_id_table . "
			WHERE product_id_integer = %d
		";

		$product_id_title = $wpdb->get_var( $wpdb->prepare( $sql, $product_id_integer ) );

		if ( WC_AM_FORMAT()->empty( $product_id_title ) ) {
			$product_id_title = WC_AM_PRODUCT_DATA_STORE()->get_meta( $product_id_integer, $key );

			if ( ! WC_AM_FORMAT()->empty( $product_id_title ) ) {
				$this->add_legacy_product_id( $product_id_title, $key );
			}
		}

		return ! WC_AM_FORMAT()->empty( $product_id_title ) ? (string) $product_id_title : '';
	}

	/**
	 * Returns the product_id_integer that matches the product_id_title.
	 *
	 * @since 2.7
	 *
	 * @param string $product_id_title
	 * @param string $key String used to find value in the meta database table.
	 *
	 * @return int|string
	 */
	public function get_product_id_integer( $product_id_title, $key = '_api_resource_title' ) {
		global $wpdb;

		if ( is_numeric( $product_id_title ) ) {
			return $product_id_title;
		}

		$sql = "
			SELECT product_id_integer
			FROM {$wpdb->prefix}" . $this->legacy_product_id_table . "
			WHERE product_id_title = %s
		";

		$product_id_integer = $wpdb->get_var( $wpdb->prepare( $sql, $product_id_title ) );

		if ( WC_AM_FORMAT()->empty( $product_id_integer ) ) {
			$product_id_integer = $this->add_legacy_product_id( $product_id_title, $key );
		}

		return ! WC_AM_FORMAT()->empty( $product_id_integer ) ? (int) $product_id_integer : '';
	}

	/**
	 * Add a unique $product_id_title with the matching product_id_integer.
	 *
	 * @since 2.7
	 *
	 * @param string $product_id_title
	 * @param string $key String used to find value in the meta database table.
	 *
	 * @return bool
	 */
	private function add_legacy_product_id( $product_id_title, $key = '_api_resource_title' ) {
		global $wpdb;

		$result     = false;
		$product_id = 0;

		if ( ! empty( $product_id_title ) && ! $this->has_legacy_product_id( $product_id_title ) ) {
			$product_ids = WC_AM_API_RESOURCE_DATA_STORE()->get_all_product_ids_from_api_resource_table();

			if ( ! empty( $product_ids ) ) {
				foreach ( $product_ids as $id ) {
					// Compare the String $product_id to the String title to determine the numeric product ID.
					if ( WC_AM_FORMAT()->strcmp( $product_id_title, WC_AM_PRODUCT_DATA_STORE()->get_meta( $id, $key ) ) ) {
						$product_id = $id;
						break;
					}
				}
			}

			if ( ! empty( $product_id ) ) {
				$data = array(
					'product_id_title'   => (string) $product_id_title,
					'product_id_integer' => (int) $product_id
				);

				$format = array(
					'%s',
					'%d'
				);

				$result = $wpdb->insert( $wpdb->prefix . $this->legacy_product_id_table, $data, $format );
			}
		}

		return ! WC_AM_FORMAT()->empty( $result ) ? $product_id : 0;
	}

	/**
	 * Return total number of legacy product ids.
	 * COUNT(expr) only counts non-null values, whereas COUNT(*) also counts null values.
	 *
	 * @since 2.7.1
	 *
	 * @return int
	 */
	public function count() {
		global $wpdb;

		$count = $wpdb->get_var( "
			SELECT COUNT(legacy_product_id)
			FROM {$wpdb->prefix}" . $this->legacy_product_id_table . "
		" );

		return ! empty( $count ) ? (int) $count : 0;
	}
}
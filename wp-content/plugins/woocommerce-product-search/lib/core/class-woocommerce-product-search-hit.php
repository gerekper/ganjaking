<?php
/**
 * class-woocommerce-product-search-hit.php
 *
 * Copyright (c) "kento" Karim Rahimpur www.itthinx.com
 *
 * This code is provided subject to the license granted.
 * Unauthorized use and distribution is prohibited.
 * See COPYRIGHT.txt and LICENSE.txt
 *
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * This header and all notices must be kept intact.
 *
 * @author itthinx
 * @package woocommerce-product-search
 * @since 2.0.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Hit machine.
 */
class WooCommerce_Product_Search_Hit {

	/**
	 * Queries handled in this request.
	 *
	 * @var array[string]
	 */
	private static $queries = null;

	/**
	 * Returns true if recording hits is enabled, otherwise false.
	 *
	 * @return boolean true if hits are recorded, otherwise false
	 */
	public static function get_status() {
		$options = get_option( 'woocommerce-product-search', array() );
		return isset( $options[WooCommerce_Product_Search::RECORD_HITS] ) ? $options[WooCommerce_Product_Search::RECORD_HITS] : WooCommerce_Product_Search::RECORD_HITS_DEFAULT;
	}

	/**
	 * Record a hit for the given query.
	 * Returns null if hits are not recorded or the query is not valid or when something else went wrong.
	 *
	 * @param string $query the query string
	 * @param int $count the number of results founds for the query string
	 *
	 * @return int|null
	 */
	public static function record( $query, $count ) {

		global $wpdb;

		$hit_id = null;

		if ( !self::get_status() ) {
			return $hit_id;
		}

		if ( !is_string( $query ) ) {
			return $hit_id;
		}
		$query = trim( $query );
		if ( strlen( $query ) === 0 ) {
			return $hit_id;
		}

		if ( self::$queries === null || !key_exists( $query, self::$queries ) ) {

			self::$queries[] = $query;

			$query_id = self::maybe_record_query( $query );
			if ( $query_id !== null ) {

				$count = intval( $count );
				if ( $count < 0 ) {
					$count = 0;
				}

				$now = time();
				$date     = date( 'Y-m-d', $now );
				$datetime = date( 'Y-m-d H:i:s', $now );

				$ip = null;
				if ( function_exists( 'inet_pton' ) ) {
					$remote_addr = isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : null;
					if ( $remote_addr !== null ) {
						$in_addr = inet_pton( $remote_addr );
						if ( $in_addr !== false ) {
							$ip = $in_addr;
						}
					}
				}

				$src_uri_id = null;
				$src_uri = isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : null;
				if ( $src_uri !== null ) {
					$src_uri_id = self::maybe_record_uri( $src_uri );
				}

				$current_url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
				$dest_uri_id = self::maybe_record_uri( $current_url );

				$user_id = is_user_logged_in() ? get_current_user_id() : null;

				$user_agent_id = null;
				$user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : null;
				if ( $user_agent !== null ) {
					$user_agent_id = self::maybe_record_user_agent( $user_agent );
				}

				$conditions = array( 'query_id = %d', 'date = %s', 'datetime = %s', 'count = %d' );
				$columns = array( 'query_id', 'date', 'datetime', 'count' );
				$formats = array( '%d', '%s', '%s', '%d' );
				$values  = array( $query_id, $date, $datetime, $count ); 
				foreach(
					array(
						'ip' => $ip,
						'src_uri_id' => $src_uri_id,
						'dest_uri_id' => $dest_uri_id,
						'user_id' => $user_id,
						'user_agent_id' => $user_agent_id
					) as $key => $value
				) {
					if ( $value !== null ) {
						$columns[] = $key;
						$format = $key !== 'ip' ? '%d' : '%s'; 
						$formats[] = $format;
						$values[] = $value;
						$conditions[] = "$key = $format";
					}
				}
				$filter_args = array_combine( $columns, $values );
				$conditions = implode( ' AND ', $conditions );
				$columns = '(' . implode( ',', $columns ) . ')';
				$formats = '(' . implode( ',', $formats ) . ')';
				$hit_table = WooCommerce_Product_Search_Controller::get_tablename( 'hit' );
				$rows = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(hit_id) FROM $hit_table WHERE $conditions", $values ) );
				$rows = $rows !== null ? intval( $rows ) : 0;
				if ( $rows === 0 ) {
					if ( apply_filters( 'woocommerce_product_search_record_hit', true, $query, $count, $filter_args ) ) {
						if ( $wpdb->query( $wpdb->prepare( "INSERT INTO $hit_table $columns VALUES $formats", $values ) ) ) {
							$hit_id = $wpdb->get_var( "SELECT LAST_INSERT_ID()" );
							if ( $hit_id !== null ) {
								$hit_id = intval( $hit_id );
							}
						}
					}
				}
			}

		} else {
			$hit_id = self::$queries[$query];
		}
		return $hit_id;
	}

	/**
	 * Return the id of the existing URI or add a record and return its id.
	 *
	 * @param string $uri the URI
	 *
	 * @return int|null id or null
	 */
	private static function maybe_record_uri( $uri ) {

		global $wpdb;

		$uri_id = null;

		if ( $uri !== null && is_string( $uri ) ) {
			$uri_table = WooCommerce_Product_Search_Controller::get_tablename( 'uri' );
			$uri = esc_url_raw( $uri );
			$uri = substr( $uri, 0, WooCommerce_Product_Search_Controller::MAX_URI_LENGTH );
			$uri = trim( $uri );
			if ( strlen( $uri ) > 0 ) {
				$uri_id = $wpdb->get_var( $wpdb->prepare( "SELECT uri_id FROM $uri_table WHERE uri = %s", $uri ) );
				if ( $uri_id !== null ) {
					$uri_id = intval( $uri_id );
				} else {
					if ( $wpdb->query( $wpdb->prepare( "INSERT INTO $uri_table (uri) VALUES (%s)", $uri ) ) ) {
						$uri_id = $wpdb->get_var( "SELECT LAST_INSERT_ID()" );
						if ( $uri_id !== null ) {
							$uri_id = intval( $uri_id );
						}
					}
				}
			}
		}
		return $uri_id;
	}

	/**
	 * Return the id of the existing query string or add a record and return its id.
	 *
	 * @param string $query the query string
	 *
	 * @return int|null id or null
	 */
	private static function maybe_record_query( $query ) {
		global $wpdb;

		$query_id = null;

		if ( $query !== null && is_string( $query ) ) {

			if ( function_exists( 'mb_substr' ) ) {
				$query = mb_substr( $query, 0, WooCommerce_Product_Search_Controller::MAX_QUERY_LENGTH );
			} else {
				$query = substr( $query, 0, WooCommerce_Product_Search_Controller::MAX_QUERY_LENGTH );
			}
			$query = trim( $query );

			if ( strlen( $query ) > 0 ) {
				$query_table = WooCommerce_Product_Search_Controller::get_tablename( 'query' );
				$query_id = $wpdb->get_var( $wpdb->prepare( "SELECT query_id FROM $query_table WHERE query = %s", $query ) );
				if ( $query_id !== null ) {
					$query_id = intval( $query_id );
				} else {
					if ( $wpdb->query( $wpdb->prepare( "INSERT INTO $query_table (query) VALUES (%s)", $query ) ) ) {
						$query_id = $wpdb->get_var( "SELECT LAST_INSERT_ID()" );
						if ( $query_id !== null ) {
							$query_id = intval( $query_id );
						}
					}
				}
			}

		}
		return $query_id;
	}

	/**
	 * Return the id of the existing user agent or add a record and return its id.
	 *
	 * @param string $user_agent the user agent
	 *
	 * @return int|null id or null
	 */
	private static function maybe_record_user_agent( $user_agent ) {

		global $wpdb;

		$user_agent_id = null;

		if ( $user_agent !== null && is_string( $user_agent ) ) {
			$user_agent_table = WooCommerce_Product_Search_Controller::get_tablename( 'user_agent' );
			$user_agent = substr( $user_agent, 0, WooCommerce_Product_Search_Controller::MAX_USER_AGENT_LENGTH );
			$user_agent = trim( $user_agent );
			if ( strlen( $user_agent ) > 0 ) {
				$user_agent_id = $wpdb->get_var( $wpdb->prepare( "SELECT user_agent_id FROM $user_agent_table WHERE user_agent = %s", $user_agent ) );
				if ( $user_agent_id !== null ) {
					$user_agent_id = intval( $user_agent_id );
				} else {
					if ( $wpdb->query( $wpdb->prepare( "INSERT INTO $user_agent_table (user_agent) VALUES (%s)", $user_agent ) ) ) {
						$user_agent_id = $wpdb->get_var( "SELECT LAST_INSERT_ID()" );
						if ( $user_agent_id !== null ) {
							$user_agent_id = intval( $user_agent_id );
						}
					}
				}
			}
		}
		return $user_agent_id;
	}
}

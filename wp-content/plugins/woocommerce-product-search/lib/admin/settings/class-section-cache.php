<?php
/**
 * class-section-cache.php
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
 * @since 5.0.0
 */

namespace com\itthinx\woocommerce\search\engine\admin;

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

use com\itthinx\woocommerce\search\engine\Cache;
use com\itthinx\woocommerce\search\engine\Cache_Settings;
use com\itthinx\woocommerce\search\engine\Settings;
use com\itthinx\woocommerce\search\engine\File_Cache;

/**
 * Cache Settings.
 */
class Section_Cache extends \WooCommerce_Product_Search_Admin_Base {

	const SORT_PRIORITY_MULTIPLIER = 10;

	private static $caches = array(
		'transitory'   => array( 'name' => 'Transitory' ),
		'memcached'    => array( 'name' => 'Memcached' ),
		'redis'        => array( 'name' => 'Redis' ),
		'object_cache' => array( 'name' => 'Object Cache' ),
		'file_cache'   => array( 'name' => 'File Cache' )
	);

	/**
	 * Records changes made to the settings.
	 */
	public static function save() {
		$cache_id = null;
		if ( isset( $_GET['cache_id'] ) ) {
			$cache_id = $_GET['cache_id'];
		}
		switch ( $cache_id ) {
			case 'file_cache':
				require_once 'class-section-cache-file-cache.php';
				Section_Cache_File_Cache::save();
				break;
			case 'memcached':
				require_once 'class-section-cache-memcached.php';
				Section_Cache_Memcached::save();
				break;
			case 'object_cache':
				require_once 'class-section-cache-object-cache.php';
				Section_Cache_Object_Cache::save();
				break;
			case 'redis':
				require_once 'class-section-cache-redis.php';
				Section_Cache_Redis::save();
				break;
			case 'transitory':
				require_once 'class-section-cache-transitory-cache.php';
				Section_Cache_Transitory_Cache::save();
				break;
			default:
				$settings = Cache_Settings::get_instance();
				$cache_settings = $settings->get();

				$cache_ids = array_keys( self::$caches );
				foreach ( $cache_ids as $cache_id ) {
					$enabled = !empty( $_POST["woocommerce-product-search-cache-$cache_id-enabled"] );
					$cache_settings[$cache_id]['enabled'] = $enabled;
				}

				$cache_order = isset( $_POST['cache_order'] ) ? wc_clean( wp_unslash( $_POST['cache_order'] ) ) : '';
				if ( is_array( $cache_order ) ) {
					$n = count( $cache_order );
					if ( $n > 0 ) {
						$i = $n;
						foreach ( $cache_order as $cache_id ) {
							if ( in_array( $cache_id, $cache_ids ) ) {
								$cache_settings[$cache_id]['priority'] = $i * self::SORT_PRIORITY_MULTIPLIER;
							}
							$i--;
						}
					}
				}
				$settings->set( $cache_settings );
				$settings->save();
		}
	}

	/**
	 * Sort by highest priority.
	 *
	 * @param array $cache1
	 * @param array $cache2
	 *
	 * @return int
	 */
	public static function sort( $cache1, $cache2 ) {
		$priority1 = 0;
		$priority2 = 0;
		if ( is_array( $cache1 ) ) {
			if ( isset( $cache1['priority'] ) && is_numeric( $cache1['priority'] ) ) {
				$priority1 = intval( $cache1['priority'] );
			}
		}
		if ( is_array( $cache2 ) ) {
			if ( isset( $cache2['priority'] ) && is_numeric( $cache2['priority'] ) ) {
				$priority2 = intval( $cache2['priority'] );
			}
		}
		return $priority2 - $priority1;
	}

	/**
	 * Render the appropriate section requested, caches overview or individual cache settings.
	 */
	public static function render() {

		echo '<div id="product-search-cache-tab" class="product-search-tab">';

		if ( Cache_Settings::is_hardwired() ) {
			echo '<fieldset class="wps-caches-override" disabled>';
			echo '<legend class="highlight">';
			printf(
				/* translators: 1: tag 2: string 3: tag - DO NOT omit, DO NOT change the order */
				esc_html__(
					'The cache configuration is determined by the %1$s%2$s%3$s constant.',
					'woocommerce-product-search'
				),
				'<code>',
				esc_html( Cache_Settings::which_hardwire() ),
				'</code>'
			);
			echo '</legend>';
		}

		$cache_id = null;
		if ( isset( $_GET['cache_id'] ) ) {
			$cache_id = $_GET['cache_id'];
		}
		switch ( $cache_id ) {
			case 'file_cache':
				require_once 'class-section-cache-file-cache.php';
				Section_Cache_File_Cache::render();
				break;
			case 'memcached':
				require_once 'class-section-cache-memcached.php';
				Section_Cache_Memcached::render();
				break;
			case 'object_cache':
				require_once 'class-section-cache-object-cache.php';
				Section_Cache_Object_Cache::render();
				break;
			case 'redis':
				require_once 'class-section-cache-redis.php';
				Section_Cache_Redis::render();
				break;
			case 'transitory':
				require_once 'class-section-cache-transitory-cache.php';
				Section_Cache_Transitory_Cache::render();
				break;
			default:
				self::render_caches();
		}

		if ( Cache_Settings::is_hardwired() ) {
			echo '</fieldset>';
		}

		echo '</div>';
	}

	/**
	 * Renders the caches overview section.
	 */
	private static function render_caches() {

		$settings = Cache_Settings::get_instance();

		$cache_settings = $settings->get();
		foreach ( Cache_Settings::get_defaults() as $cache_id => $data ) {
			if (
				!Cache_Settings::is_hardwired() &&
				!isset( $cache_settings[$cache_id] )
			) {
				$data['enabled'] = false;
				$cache_settings[$cache_id] = $data;
			}
		}

		uasort( $cache_settings, array( __CLASS__, 'sort' ) );

		$output = '';
		$output .= '<h3 class="section-heading">' . esc_html( __( 'Caches', 'woocommerce-product-search' ) ) . '</h3>';

		$output .= '<table class="woocommerce-product-search-cache widefat" cellspacing="0">';

		$output .= '<thead>';
		$output .= '<tr>';
		if ( !Cache_Settings::is_hardwired() ) {
			$output .= '<th class="woocommerce-product-search-cache-column-sort"></th>';
		}
		$output .= '<th class="woocommerce-product-search-cache-column-cache-info">' . esc_html__( 'Cache', 'woocommerce-product-search' ) . '</th>';
		$output .= '<th class="woocommerce-product-search-cache-column-cache-enabled">' . esc_html__( 'Enabled', 'woocommerce-product-search' ) . '</th>';

		$output .= '<th class="woocommerce-product-search-cache-column-cache-actions"></th>';
		$output .= '</tr>';
		$output .= '</thead>';

		$output .= '<tbody>';

		$count = 0;
		$count_enabled = 0;
		foreach ( $cache_settings as $cache_id => $data ) {

			$is_property = false;
			switch ( $cache_id ) {
				case 'id':
				case 'strategy':
					$is_property = true;
					break;
			}
			if ( $is_property ) {
				continue;
			}

			if ( $data['enabled'] ) {
				$count_enabled++;
			}

			if ( isset( $data['ui'] ) && !$data['ui'] ) {
				continue;
			}

			$count++;

			$output .= sprintf(
				'<tr class="%s" data-cache_id="%s">',
				( Cache_Settings::is_hardwired() || isset( $data['locked'] ) && $data['locked'] ) ? 'locked' : '',
				esc_attr( $cache_id )
			);

			if ( !Cache_Settings::is_hardwired() ) {
				$output .= '<td class="sort" width="1%">';

				if ( !isset( $data['locked'] ) || !$data['locked'] ) {

					$output .= '<div class="wc-item-reorder-nav">';
					$output .= sprintf(
						'<button type="button" class="wc-move-up" tabindex="0" aria-hidden="false" aria-label="%s">%s</button>',
						/* translators: %s: name of the cache */
						esc_attr( sprintf( __( 'Move the &quot;%s&quot; cache up', 'woocommerce-product-search' ), self::$caches[$cache_id]['name'] ) ),
						esc_html__( 'Move up', 'woocommerce-product-search' )
					);
					$output .= sprintf(
						'<button type="button" class="wc-move-down" tabindex="0" aria-hidden="false" aria-label="%s">%s</button>',
						/* translators: %s: name of the cache */
						esc_attr( sprintf( __( 'Move the &quot;%s&quot; cache down', 'woocommerce-product-search' ), self::$caches[$cache_id]['name'] ) ),
						esc_html__( 'Move down', 'woocommerce-bitcoin' )
					);

					$output .= sprintf(
						'<input type="hidden" name="cache_order[]" value="%s" />',
						esc_attr( $cache_id )
					);
					$output .= '</div>';
				}
				$output .= '</td>';
			}

			$output .= '<td class="cache-info">';
			$output .= '<div class="cache-name">';
			$output .= sprintf(
				'<a href="%s" title="%s">%s</a>',
				esc_url( add_query_arg( 'cache_id', $cache_id, self::get_admin_section_url( self::SECTION_CACHE ) ) ),
				esc_html__( 'Settings', 'woocommerce-product-search' ),
				esc_html( self::$caches[$cache_id]['name'] )
			);
			$output .= '</div>';

			if( $cache_id === 'file_cache' ) {
				$status_url = add_query_arg(
					array(
						'cache_id' => $cache_id,
						'action' => 'status'

					),
					rest_url( 'wps/v1/search/cache/status' )
				);
				$output .= sprintf(
					'<div class="wps-cache-status-box" data-cache-id="%s" data-url="%s" data-nonce="%s"></div>',
					esc_attr( $cache_id ),
					esc_url( $status_url ),
					esc_attr( wp_create_nonce( 'wp_rest' ) )
				);
			}
			$output .= '</td>';

			$output .= '<td class="cache-enabled">';
			$output .= sprintf(
				'<input type="checkbox" name="woocommerce-product-search-cache-%s-enabled" %s %s/>',
				esc_attr( $cache_id ),
				$data['enabled'] ? 'checked="checked"' : '',
				isset( $data['locked'] ) && $data['locked'] ? 'disabled' : ''
			);
			$output .= '</td>';

			$output .= '<td class="cache-settings">';

			$output .= sprintf(
				'<a class="button" href="%s">%s</a>',
				esc_url( add_query_arg( 'cache_id', $cache_id, self::get_admin_section_url( self::SECTION_CACHE ) ) ),
				esc_html__( 'Manage', 'woocommerce-product-search' )
			);

			if( $cache_id !== 'transitory' ) {
				$flush_url = add_query_arg(
					array(
						'cache_id' => $cache_id,
						'action' => 'flush'

					),
					rest_url( 'wps/v1/search/cache/flush' )
				);
				$output .= sprintf(
					'<a class="button wps-cache-action wps-cache-flush" href="%s" data-cache-id="%s" data-url="%s" data-nonce="%s">%s</a>',
					esc_url( $flush_url ),
					esc_attr( $cache_id ),
					esc_url( $flush_url ),
					esc_attr( wp_create_nonce( 'wp_rest' ) ),
					esc_html__( 'Flush', 'woocommerce-product-search' )
				);
			}

			if( $cache_id === 'file_cache' ) {
				$gc_url = add_query_arg(
					array(
						'cache_id' => $cache_id,
						'action' => 'gc'

					),
					rest_url( 'wps/v1/search/cache/gc' )
				);
				$output .= sprintf(
					'<a class="button wps-cache-action wps-cache-gc" href="%s" data-cache-id="%s" data-url="%s" data-nonce="%s">%s</a>',
					esc_url( $gc_url ),
					esc_attr( $cache_id ),
					esc_url( $gc_url ),
					esc_attr( wp_create_nonce( 'wp_rest' ) ),
					esc_html__( 'Clean', 'woocommerce-product-search' )
				);
			}

			$output .= '</td>';
		}

		if ( $count === 0 ) {
			$output .= '<tr>';
			$output .= '<td colspan="100%">';
			if ( $count_enabled === 0 ) {
				$output .= esc_html__( 'No caches', 'woocommerce-product-search' );
			} else {
				$output .= sprintf( esc_html__( 'Caching (%d)', 'woocommerce-product-search' ), $count_enabled );
			}
			$output .= '</td>';
			$output .= '</tr>';
		}

		$output .= '</tbody>';

		$output .= '</table>';

		echo $output;
	}

}

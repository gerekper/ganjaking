<?php
/**
 * class-section-cache-file-cache.php
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
use com\itthinx\woocommerce\search\engine\File_Cache;
use com\itthinx\woocommerce\search\engine\Settings;

/**
 * Cache Settings.
 */
class Section_Cache_File_Cache extends \WooCommerce_Product_Search_Admin_Base {

	/**
	 * Records changes made to the settings.
	 */
	public static function save() {
		$settings = Cache_Settings::get_instance();
		$cache_settings = $settings->get();

		$old_max_files = $cache_settings['file_cache']['max_files'];
		$old_max_size = $cache_settings['file_cache']['max_size'];
		$old_gc_interval = $cache_settings['file_cache']['gc_interval'];

		$enabled = !empty( $_POST['file-cache-enabled'] );
		$cache_settings['file_cache']['enabled'] = $enabled;

		$max_files = isset( $_POST['max_files'] ) ? trim( $_POST['max_files'] ) : '';
		if ( strlen( $max_files ) === 0 ) {
			$max_files = null;
		} else {
			$max_files = max( 0, intval( $max_files ) );
		}
		$cache_settings['file_cache']['max_files'] = $max_files;

		$max_size = isset( $_POST['max_size'] ) ? trim( $_POST['max_size'] ) : '';
		if ( strlen( $max_size ) === 0 ) {
			$max_size = null;
		}
		if ( $max_size !== null ) {
			$validated_max_size = File_Cache::parse_storage_bytes( $max_size );
			$max_size = $validated_max_size['string'];
		}
		$cache_settings['file_cache']['max_size'] = $max_size;

		$min_free_disk_space = isset( $_POST['min_free_disk_space'] ) ? trim( $_POST['min_free_disk_space'] ) : '';
		if ( strlen( $min_free_disk_space ) === 0 ) {
			$min_free_disk_space = null;
		}
		if ( $min_free_disk_space !== null ) {
			$validated_min_free_disk_space = File_Cache::parse_storage_measure( $min_free_disk_space );
			$min_free_disk_space = $validated_min_free_disk_space['string'];
		}
		$cache_settings['file_cache']['min_free_disk_space'] = $min_free_disk_space;

		$gc_interval = isset( $_POST['gc_interval'] ) ? trim( $_POST['gc_interval'] ) : '';
		if ( strlen( $gc_interval ) === 0 ) {
			$gc_interval = null;
		} else {
			$gc_interval = max( 0, intval( $gc_interval ) );
		}
		$cache_settings['file_cache']['gc_interval'] = $gc_interval;

		$gc_time_limit = isset( $_POST['gc_time_limit'] ) ? trim( $_POST['gc_time_limit'] ) : '';
		if ( strlen( $gc_time_limit ) === 0 ) {
			$gc_time_limit = null;
		} else {
			$gc_time_limit = max( File_Cache::GC_TIME_LIMIT_MIN, intval( $gc_time_limit ) );
		}
		$cache_settings['file_cache']['gc_time_limit'] = $gc_time_limit;

		$purge = !empty( $_POST['purge'] );
		$cache_settings['file_cache']['purge'] = $purge;

		if ( $max_files !== $old_max_files || $max_size !== $old_max_size ) {
			$cache = Cache::get_instance();
			$object = $cache->get_cache( 'file_cache' );
			if ( $object !== null && $object instanceof File_Cache ) {
				$object->settings_flush( $cache_settings['file_cache'] );
			} else {

				$_enabled = $cache_settings['file_cache']['enabled'];
				$cache_settings['file_cache']['enabled'] = true;

				$_gc_interval = $cache_settings['file_cache']['gc_interval'];
				$cache_settings['file_cache']['gc_interval'] = -1;
				$id = 'tmp_file_cache';
				$caches = array(
					'id' => $id,
					'file_cache' => $cache_settings['file_cache']
				);
				$tmp_cache = Cache::create_instance( $caches );
				$object = $tmp_cache->get_cache( 'file_cache' );
				if ( $object !== null && $object instanceof File_Cache ) {
					$cache_settings['file_cache']['enabled'] = $_enabled;
					$cache_settings['file_cache']['gc_interval'] = $_gc_interval;

					$object->settings_flush( $cache_settings['file_cache'] );
				}
				Cache::delete_instance( $id );
			}
		} else {

			$settings->set( $cache_settings );
			$settings->save();
		}

		if ( $gc_interval !== $old_gc_interval ) {

			Cache::delete_instance();
			$cache = Cache::get_instance();
			$object = $cache->get_cache( 'file_cache' );
			if ( $object !== null && $object instanceof File_Cache ) {

				$object->reschedule_gc();
			} else {

				$_enabled = $cache_settings['file_cache']['enabled'];
				$cache_settings['file_cache']['enabled'] = true;
				$id = 'tmp_file_cache';
				$caches = array(
					'id' => $id,
					'file_cache' => $cache_settings['file_cache']
				);
				$tmp_cache = Cache::create_instance( $caches );
				$object = $tmp_cache->get_cache( 'file_cache' );
				if ( $object !== null && $object instanceof File_Cache ) {
					$object->reschedule_gc();
				}
				Cache::delete_instance( $id );
				$cache_settings['file_cache']['enabled'] = $_enabled;
			}
		}
	}

	/**
	 * Render the File Cache settings.
	 */
	public static function render() {

		$settings = Cache_Settings::get_instance();
		$cache_settings = $settings->get();
		$enabled = isset( $cache_settings['file_cache']['enabled'] ) && $cache_settings['file_cache']['enabled'] !== null ? $cache_settings['file_cache']['enabled'] : false;
		$max_files = isset( $cache_settings['file_cache']['max_files'] ) && $cache_settings['file_cache']['max_files'] !== null ? $cache_settings['file_cache']['max_files'] : File_Cache::MAX_FILES_DEFAULT;
		$max_size = isset( $cache_settings['file_cache']['max_size'] ) && $cache_settings['file_cache']['max_size'] !== null ? $cache_settings['file_cache']['max_size'] : File_Cache::MAX_SIZE_DEFAULT;
		$min_free_disk_space = isset( $cache_settings['file_cache']['min_free_disk_space'] ) && $cache_settings['file_cache']['min_free_disk_space'] !== null ? $cache_settings['file_cache']['min_free_disk_space'] : File_Cache::MIN_FREE_DISK_SPACE_DEFAULT;
		$gc_interval = isset( $cache_settings['file_cache']['gc_interval'] ) && $cache_settings['file_cache']['gc_interval'] !== null ? $cache_settings['file_cache']['gc_interval'] : File_Cache::GC_INTERVAL_DEFAULT;
		$gc_time_limit = isset( $cache_settings['file_cache']['gc_time_limit'] ) && $cache_settings['file_cache']['gc_time_limit'] !== null ? $cache_settings['file_cache']['gc_time_limit'] : File_Cache::GC_TIME_LIMIT_DEFAULT;
		$purge = isset( $cache_settings['file_cache']['purge'] ) && $cache_settings['file_cache']['purge'] !== null ? $cache_settings['file_cache']['purge'] : File_Cache::PURGE_DEFAULT;

		echo '<div id="product-search-cache-tab" class="product-search-tab">';

		echo '<h3 class="section-heading">';
		echo esc_html( 'File Cache' );
		echo ' ';
		printf(
			'<a class="section-navigation-up" href="%s" title="%s">%s</a>',
			esc_url( self::get_admin_section_url( self::SECTION_CACHE ) ),
			esc_html( 'Caches', 'woocommerce-product-search' ),
			'<span class="dashicons dashicons-arrow-up-alt"></span>'
		);
		echo '</h3>';

		echo '<p class="description">';
		esc_html_e( 'Use the File Cache for high-performance persistent caching.', 'woocommerce-product-search' );
		echo '</p>';

		echo '<div class="cache-settings-field">';
		echo '<div class="cache-settings-field-label">';
		printf( '<label for="%s">', 'file-cache-enabled' );
		echo esc_html__( 'Enabled', 'woocommerce-product-search' );
		echo '</label>';
		echo '</div>';
		echo '<div class="cache-settings-field-input">';
		printf( '<label for="%s">', 'file-cache-enabled' );
		printf( '<input id="%s" name="%s" type="checkbox" %s />', 'file-cache-enabled', 'file-cache-enabled', $enabled ? ' checked="checked" ' : '' );
		echo ' ';
		echo esc_html__( 'Enable the File Cache', 'woocommerce-product-search' );
		echo '</label>';
		echo '</div>';
		echo '</div>';

		echo '<div class="cache-settings-field">';
		echo '<div class="cache-settings-field-label">';
		printf( '<label for="%s">', 'max_files' );
		echo esc_html( __( 'Maximum number of cache files', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</div>';
		echo '<div class="cache-settings-field-input">';
		printf( '<input id="%s" name="%s" type="number" value="%s" placeholder="%s" min="0"/>', 'max_files', 'max_files', esc_attr( $max_files ), File_Cache::MAX_FILES_DEFAULT );
		printf( '<label for="%s" class="description measure">', 'max_files' );
		echo esc_html__( 'Changes to this setting will cause the cache to be flushed.', 'woocommerce-product-search' );
		echo ' ';
		echo esc_html__( 'This limit is disabled with 0.', 'woocommerce-product-search' );
		echo '</label>';
		echo '</div>';
		echo '</div>';

		echo '<div class="cache-settings-field">';
		echo '<div class="cache-settings-field-label">';
		printf( '<label for="%s">', 'max_size' );
		echo esc_html( __( 'Maximum total size of cache files', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</div>';
		echo '<div class="cache-settings-field-input">';
		printf( '<input id="%s" class="measure" name="%s" type="text" value="%s" placeholder="%s" />', 'max_size', 'max_size', esc_attr( $max_size ), File_Cache::MAX_SIZE_DEFAULT );
		printf( '<label for="%s" class="description measure">', 'max_size' );
		echo esc_html__( 'Changes to this setting will cause the cache to be flushed.', 'woocommerce-product-search' );
		echo ' ';
		echo esc_html__( 'Specify a number of bytes.', 'woocommerce-product-search' );
		echo ' ';
		echo esc_html__( 'Allowed suffixes are K, M, G, T, P.', 'woocommerce-product-search' );
		echo ' ';
		echo esc_html__( 'This limit is disabled with 0.', 'woocommerce-product-search' );
		echo '</label>';
		echo '</div>';
		echo '</div>';

		echo '<div class="cache-settings-field">';
		echo '<div class="cache-settings-field-label">';
		printf( '<label for="%s">', 'min_free_disk_space' );
		echo esc_html( __( 'Minimum free storage space', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</div>';
		echo '<div class="cache-settings-field-input">';
		printf( '<input id="%s" class="measure" name="%s" type="text" value="%s" placeholder="%s" />', 'min_free_disk_space', 'min_free_disk_space', esc_attr( $min_free_disk_space ), File_Cache::MIN_FREE_DISK_SPACE_DEFAULT );
		printf( '<label for="%s" class="description measure">', 'min_free_disk_space' );
		echo esc_html__( 'Specify the number of bytes or a percentage of the total storage space.', 'woocommerce-product-search' );
		echo ' ';
		echo esc_html__( 'Allowed suffixes are K, M, G, T, P for bytes and % for percentage.', 'woocommerce-product-search' );
		echo '</label>';
		echo '</div>';
		echo '</div>';

		echo '<div class="cache-settings-field">';
		echo '<div class="cache-settings-field-label">';
		printf( '<label for="%s">', 'gc_interval' );
		echo esc_html( __( 'Garbage Collection Interval', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</div>';
		echo '<div class="cache-settings-field-input">';
		printf( '<input id="%s" name="%s" type="number" value="%s" placeholder="%s" min="0"/>', 'gc_interval', 'gc_interval', esc_attr( $gc_interval ), File_Cache::GC_INTERVAL_DEFAULT );
		printf( '<label for="%s" class="description measure">', 'gc_interval' );
		echo esc_html__( 'Garbage-collection interval, in seconds.', 'woocommerce-product-search' );
		echo ' ';
		echo esc_html__( 'Garbage-collection is disabled with 0.', 'woocommerce-product-search' );
		echo '</label>';
		echo '</div>';
		echo '</div>';

		echo '<div class="cache-settings-field">';
		echo '<div class="cache-settings-field-label">';
		printf( '<label for="%s">', 'gc_time_limit' );
		echo esc_html( __( 'Garbage Collection Time Limit', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</div>';
		echo '<div class="cache-settings-field-input">';
		printf( '<input id="%s" name="%s" type="number" value="%s" placeholder="%s" min="%d"/>', 'gc_time_limit', 'gc_time_limit', esc_attr( $gc_time_limit ), File_Cache::GC_TIME_LIMIT_DEFAULT, File_Cache::GC_TIME_LIMIT_MIN );
		printf( '<label for="%s" class="description measure">', 'gc_time_limit' );
		echo esc_html__( 'Time limit per garbage-collection round, in seconds.', 'woocommerce-product-search' );
		echo ' ';
		echo esc_html__( 'The time limit is disabled with 0.', 'woocommerce-product-search' );
		echo '</label>';
		echo '</div>';
		echo '</div>';

		echo '<div class="cache-settings-field">';
		echo '<div class="cache-settings-field-label">';
		printf( '<label for="%s">', 'purge' );
		echo esc_html__( 'Purge', 'woocommerce-product-search' );
		echo '</label>';
		echo '</div>';
		echo '<div class="cache-settings-field-input">';
		printf( '<label for="%s">', 'purge' );
		printf( '<input id="%s" name="%s" type="checkbox" %s />', 'purge', 'purge', $purge ? ' checked="checked" ' : '' );
		echo ' ';
		echo esc_html__( 'Purge files and directories when flushing.', 'woocommerce-product-search' );
		echo '</label>';
		echo '</div>';
		echo '</div>';

		echo '</div>';
	}

}

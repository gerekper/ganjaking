<?php
/**
 * WooCommerce Customer/Order XML Export Suite
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Customer/Order XML Export Suite to newer
 * versions in the future. If you wish to customize WooCommerce Customer/Order XML Export Suite for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-customer-order-xml-export-suite/
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2013-2019, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

/**
 * Customer/Order XML Export Suite Data Store
 *
 * Handles data persistence for exports.
 *
 * @since 2.4.0
 */
abstract class WC_Customer_Order_XML_Export_Suite_Data_Store {


	/**
	 * Persists a single item.
	 *
	 * @since 2.4.0
	 *
	 * @param \WC_Customer_Order_XML_Export_Suite_Export $export the export object this item is a part of
	 * @param string $content the content to store
	 */
	abstract public function store_item( $export, $content );


	/**
	 * Gets the file size of the given export in bytes.
	 *
	 * @since 2.4.0
	 *
	 * @param \WC_Customer_Order_XML_Export_Suite_Export $export the export object
	 * @return int file size in bytes
	 */
	abstract public function get_file_size( $export );


	/**
	 * Deletes any persisted data for the specified export.
	 *
	 * @since 2.4.0
	 *
	 * @param \WC_Customer_Order_XML_Export_Suite_Export $export the export object
	 */
	abstract public function delete_export( $export );


	/**
	 * Gets the contents of an export in a single variable.
	 *
	 * @since 2.4.0
	 *
	 * @param \WC_Customer_Order_XML_Export_Suite_Export $export the export object
	 * @return string|null the output of the export or null if not found
	 */
	abstract public function get_output( $export );


	/**
	 * Gets a streamable resource for the export file.
	 *
	 * @since 2.4.0
	 *
	 * @param \WC_Customer_Order_XML_Export_Suite_Export $export the export object
	 * @return resource|false the file stream or false if unable to get file stream
	 */
	abstract public function get_file_stream( $export );


	/**
	 * Streams data to the given file resource.
	 *
	 * @since 2.4.0
	 *
	 * @param \WC_Customer_Order_XML_Export_Suite_Export $export the export object to stream
	 * @param resource $resource the file pointer resource to stream the export to
	 */
	abstract public function stream_output( $export, $resource );


	/**
	 * Allows the data store to add arguments to new jobs.
	 *
	 * @since 2.4.0
	 *
	 * @param array $args arguments for a new job to be created from
	 * @return array additional job arguments to add
	 */
	public function get_job_args( $args ) { return array(); }


	/**
	 * Prepares content for storage.
	 *
	 * @since 2.4.0
	 *
	 * @param string $content the content to be stored
	 * @return string the content after it has been prepared for storage
	 */
	protected function prepare_content_for_storage( $content ) {
		return $content;
	}


	/**
	 * Processes content from storage.
	 *
	 * @since 2.4.0
	 *
	 * @param string $content the content to be processed
	 * @return string the content after it has been processed
	 */
	protected function process_content_from_storage( $content ) {
		return $content;
	}


}

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
 * Customer/Order XML Export Suite Database Stream Iterator
 *
 * Iterates over an export being streamed from the database. Usable with foreach().
 *
 * @since 2.4.0
 */
class WC_Customer_Order_XML_Export_Suite_Database_Stream_Iterator implements Iterator {


	/** @var \WC_Customer_Order_XML_Export_Suite_Export the export object */
	protected $export;

	/** @var \WC_Customer_Order_XML_Export_Suite_Data_Store_Database data store instance */
	protected $data_store;

	/** @var mysqli_result the database result */
	protected $data_stream;

	/** @var string current row */
	private $current_row;

	/** @var int key of the current row - an index corresponding with line numbers */
	private $current_key;

	/** @var bool end of file reached */
	private $eof;


	/**
	 * Constructs the iterator.
	 *
	 * @since 2.4.0
	 *
	 * @param \WC_Customer_Order_XML_Export_Suite_Export $export the export object
	 * @throws \SV_WC_Plugin_Exception if export is invalid
	 */
	public function __construct( $export ) {

		if ( ! $export || ! $export instanceof WC_Customer_Order_XML_Export_Suite_Export ) {

			throw new SV_WC_Plugin_Exception( __( 'Unable to find export for iteration', 'woocommerce-customer-order-xml-export-suite' ) );
		}

		$this->export     = $export;
		$this->data_store = new WC_Customer_Order_XML_Export_Suite_Data_Store_Database();
	}


	/**
	 * Iterates and gets the next item, if valid.
	 *
	 * @since 2.4.0
	 *
	 * @return string|false the next item or false if invalid
	 */
	public function get_next() {

		$this->next();

		return $this->valid() ? $this->current() : false;
	}


	/**
	 * Frees the mysqli result.
	 *
	 * @since 2.4.0
	 */
	public function free_data_stream() {

		if ( $this->data_stream ) {

			mysqli_free_result( $this->data_stream );
			$this->data_stream = null;
		}
	}


	/******** Iterator Methods -- needed for foreach() compatibility ********/


	/**
	 * Prepares the iterator to start fresh.
	 *
	 * @see \Iterator::rewind()
	 *
	 * @since 2.4.0
	 */
	public function rewind() {

		$this->free_data_stream();

		$this->current_key = 0;
		$this->current_row = '';
		$this->eof         = false;
		$this->data_stream = $this->data_store->get_data_stream( $this->export );
	}


	/**
	 * Moves forward to the next row in the results.
	 *
	 * @see \Iterator::next()
	 *
	 * @since 2.4.0
	 */
	public function next() {

		$row = $this->data_store->parse_row( $this->data_stream->fetch_assoc() );

		if ( $row ) {

			$this->current_key++;
			$this->current_row = $row;

		} else {

			$this->current_key = null;
			$this->current_row = null;
			$this->eof         = true;
		}
	}


	/**
	 * Checks if the iterator is in a valid position.
	 *
	 * @see \Iterator::valid()
	 *
	 * @since 2.4.0
	 *
	 * @return bool if the iterator is valid
	 */
	public function valid() {

		if ( $this->eof ) {

			$this->free_data_stream();

			return false;
		}

		return true;
	}


	/**
	 * Gets the current item.
	 *
	 * @see \Iterator::current()
	 *
	 * @since 2.4.0
	 *
	 * @return string|null the current item
	 */
	public function current() {

		return $this->current_row;
	}


	/**
	 * Gets the key for the current element.
	 *
	 * This should be an index corresponding with the line number in the resulting file.
	 *
	 * @see \Iterator::key()
	 *
	 * @since 2.4.0
	 *
	 * @return int|null
	 */
	public function key() {

		return $this->current_key;
	}


}

<?php
/**
 * WooCommerce Local Pickup Plus
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Local Pickup Plus to newer
 * versions in the future. If you wish to customize WooCommerce Local Pickup Plus for your
 * needs please refer to http://docs.woocommerce.com/document/local-pickup-plus/
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2012-2021, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * Pickup Locations Export class.
 *
 * @since 2.0.0
 */
class WC_Local_Pickup_Plus_Export extends \WC_Local_Pickup_Plus_Import_Export {


	/** @var int counter for number of exported Pickup Locations in a batch */
	private $exported = 0;

	/** @var resource output stream containing CSV data */
	private $stream;


	/**
	 * Export pickup locations constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		$this->action_id        = 'export';
		$this->action_label     = __( 'Export', 'woocommerce-shipping-local-pickup-plus' );
		$this->admin_page_title = __( 'Export Pickup Locations', 'woocommerce-shipping-local-pickup-plus' );
		$this->delimiter_option = 'wc_local_pickup_plus_pickup_locations_csv_export_fields_delimiter';

		parent::__construct();

		// process exports from Pickup Locations edit screen bulk action
		add_action( 'load-edit.php', array( $this, 'process_bulk_export' ) );
	}


	/**
	 * Get export fields.
	 *
	 * @since 2.0.0
	 *
	 * @return array associative array with field data
	 */
	protected function get_fields() {

		$options = array(

			array(
				'title' => $this->admin_page_title,
				'desc'  => '<small class="wc-admin-breadcrumb" style="display:none;"><a href="' . esc_url( $this->parent_url ) . '" aria-label="' . esc_html__( 'Return to Pickup Locations', 'woocommerce-shipping-local-pickup-plus' ) . '"><img draggable="false" class="emoji" alt="⤴" src="//s.w.org/images/core/emoji/2.2.1/svg/2934.svg"></a></small>',
				'type'  => 'title',
			),

			array(
				'id'       => $this->delimiter_option,
				'name'     => __( 'Separate fields by', 'woocommerce-shipping-local-pickup-plus' ),
				'type'     => 'select',
				'desc_tip' => __( 'Change the delimiter based on your desired output format.', 'woocommerce-shipping-local-pickup-plus' ),
				'options'  => array(
					'comma' => __( 'Comma', 'woocommerce-shipping-local-pickup-plus' ),
					'tab'   => __( 'Tab space', 'woocommerce-shipping-local-pickup-plus' ),
				),
			),

			array(
				'id'                => 'wc_local_pickup_plus_csv_export_pickup_locations_limit_records',
				'name'              => __( 'Limit Records', 'woocommerce-shipping-local-pickup-plus' ),
				'type'              => 'number',
				'desc'              => __( 'Limit the number of rows to be exported. Use this option when exporting very large files that are unable to complete in a single attempt.', 'woocommerce-shipping-local-pickup-plus' ),
				'class'             => 'small-text',
				'default'           => 0,
				'custom_attributes' => array(
					'min'  => 0,
					'step' => 1,
				),
			),

			array(
				'id'                => 'wc_local_pickup_plus_csv_export_pickup_locations_offset_records',
				'name'              => __( 'Offset Records', 'woocommerce-shipping-local-pickup-plus' ),
				'type'              => 'number',
				'desc'              => __( 'Set the number of records to be skipped in this export. Use this option when exporting very large files that are unable to complete in a single attempt.', 'woocommerce-shipping-local-pickup-plus' ),
				'class'             => 'small-text',
				'default'           => 0,
				'custom_attributes' => array(
					'min'  => 0,
					'step' => 1,
				),
			),

			array(
				'type' => 'sectionend',
			),

		);

		/**
		 * Filter CSV Export Pickup Locations options.
		 *
		 * @since 2.0.0
		 *
		 * @param array $options Associative array
		 */
		return apply_filters( 'wc_local_pickup_plus_csv_export_pickup_locations_options', $options );
	}


	/**
	 * Get CSV file headers.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	protected function get_csv_headers() {

		$headers = parent::get_csv_headers();

		/**
		 * Filter the Pickup Locations CSV export file row headers.
		 *
		 * @since 2.0.0
		 *
		 * @param array $csv_headers Associative array
		 * @param \WC_Local_Pickup_Plus_Export $export_instance Instance of the export class
		 */
		return (array) apply_filters( 'wc_local_pickup_plus_csv_export_pickup_locations_headers', $headers, $this );
	}


	/**
	 * Get the export CSV file name.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	private function get_file_name() {

		// file name default: blog_name_pickup_locations_YYYY_MM_DD.csv
		$file_name = str_replace( '-', '_', sanitize_file_name( strtolower( get_bloginfo( 'name' ) . '_pickup_locations_' . date_i18n( 'Y_m_d', time() ) . '.csv' ) ) );

		/**
		 * Filter the pickup locations CSV export file name.
		 *
		 * @since 2.0.0
		 *
		 * @param string $file_name the CSV file name, should have .csv extension
		 */
		return apply_filters( 'wc_local_pickup_plus_csv_export_pickup_locations_file_name', $file_name );
	}


	/**
	 * Process input form submission to export a CSV file.
	 *
	 * @since 2.0.0
	 *
	 * @param int[] $pickup_location_ids array of pickup location IDs
	 * @param array $args optional array of arguments to pass to get_posts() to fetch pickup locations
	 */
	public function process_export( $pickup_location_ids = array(), $args = array() ) {

		if ( empty( $pickup_location_ids ) ) {
			$pickup_location_ids = wc_local_pickup_plus_get_pickup_locations( wp_parse_args( array(
				'post_status'    => 'any',
				'fields'         => 'ids',
				'posts_per_page' => empty( $_POST['wc_local_pickup_plus_csv_export_pickup_locations_limit_records'] )  ? -1 : absint( $_POST['wc_local_pickup_plus_csv_export_pickup_locations_limit_records'] ),
				'offset'         => empty( $_POST['wc_local_pickup_plus_csv_export_pickup_locations_offset_records'] ) ?  0 : absint( $_POST['wc_local_pickup_plus_csv_export_pickup_locations_offset_records'] ),
			), $args ) );
		}

		if ( ! empty( $pickup_location_ids ) ) {
			// try to set unlimited script timeout and generate file for download
			@set_time_limit( 0 );
			$this->download( $this->get_file_name(), $this->get_csv( $pickup_location_ids ) );
		} else {
			// tell the user there were no Pickup Locations to export
			wc_local_pickup_plus()->get_message_handler()->add_error( __( 'No Pickup Locations found matching the criteria to export.', 'woocommerce-shipping-local-pickup-plus' ) );
		}
	}


	/**
	 * Downloads the CSV via the browser.
	 *
	 * @since 2.0.0
	 *
	 * @param string $filename the file name
	 * @param string $csv the CSV data to download as a file
	 */
	protected function download( $filename, $csv ) {

		// set headers for download
		header( 'Content-Type: text/csv; charset=' . get_option( 'blog_charset' ) );
		header( sprintf( 'Content-Disposition: attachment; filename="%s"', $filename ) );
		header( 'Pragma: no-cache' );
		header( 'Expires: 0' );

		// clear the output buffer
		@ini_set( 'zlib.output_compression', 'Off' );
		@ini_set( 'output_buffering', 'Off' );
		@ini_set( 'output_handler', '' );

		// open the output buffer for writing
		$file = fopen( 'php://output', 'w' );

		// write the generated CSV to the output buffer
		fwrite( $file, $csv );

		// close the output buffer
		fclose( $file );
		exit;
	}


	/**
	 * Write the given row to the CSV
	 *
	 * @since 2.0.0
	 *
	 * @param array $headers row headers
	 * @param array $row row data to write
	 */
	private function write( $headers, $row ) {

		$data = array();

		foreach ( $headers as $header_key ) {

			if ( ! isset( $row[ $header_key ] ) ) {
				$row[ $header_key ] = '';
			}

			$value = '';

			// strict string comparison, as values like '0' are valid
			if ( '' !== $row[ $header_key ]  ) {
				$value = $row[ $header_key ];
			}

			// escape spreadsheet sensitive characters with a single quote to prevent CSV injections, by prepending a single quote `'`
			// @link http://www.contextis.com/resources/blog/comma-separated-vulnerabilities/
			$first_char = isset( $value[0] ) ? $value[0] : '';

			if ( in_array( $first_char, array( '=', '+', '-', '@' ), false ) ) {
				$value = "'" . $value;
			}

			$data[] = $value;
		}

		fputcsv( $this->stream, $data, $this->get_fields_delimiter(), $this->get_enclosure() );
	}


	/**
	 * Get the CSV data
	 *
	 * @since 2.0.0
	 *
	 * @param int[] $pickup_location_ids array of \WC_Local_Pickup_Plus_Pickup_Location IDs
	 * @return string
	 */
	private function get_csv( array $pickup_location_ids ) {

		$exported = 0;

		// open output buffer to write CSV to
		$this->stream = fopen( 'php://output', 'w' );

		ob_start();

		/**
		 * CSV BOM (Byte order mark).
		 *
		 * Enable adding a BOM to the exported CSV.
		 *
		 * @since 2.0.0
		 *
		 * @param bool $enable_bom true to add the BOM, false otherwise (default value)
		 * @param \WC_Local_Pickup_Plus_Export instance of this class
		 */
		if ( true === apply_filters( 'wc_local_pickup_plus_csv_export_pickup_locations_enable_bom', false, $this ) ) {

			// prepends the BOM at the top of the file, before the CSV headers
			fwrite( $this->stream, chr(0xEF) . chr(0xBB) . chr(0xBF) );
		}

		$headers = $this->get_csv_headers();

		// add CSV headers
		$this->write( $headers, $headers );

		foreach ( $pickup_location_ids as $pickup_location_id ) {

			$pickup_location = wc_local_pickup_plus_get_pickup_location( $pickup_location_id );

			if ( $pickup_location->get_id() > 0 ) {

				$row = $this->get_csv_row( $headers, $pickup_location );

				if ( ! empty ( $row ) ) {

					$data = array();

					foreach ( $headers as $header_key ) {

						if ( ! isset( $row[ $header_key ] ) ) {
							$row[ $header_key ] = '';
						}

						$value = '';

						// strict string comparison, as values like '0' are valid
						if ( '' !== $row[ $header_key ]  ) {
							$value = $row[ $header_key ];
						}

						// escape spreadsheet sensitive characters with a single quote, to prevent CSV injections, by prepending a single quote `'`.
						// @link http://www.contextis.com/resources/blog/comma-separated-vulnerabilities/
						$data[] = $this->escape_value( $value );
					}

					fputcsv( $this->stream, $data, $this->get_fields_delimiter(), $this->get_enclosure() );

					$exported++;
				}
			}
		}

		$csv = ob_get_clean();

		fclose( $this->stream );

		$this->exported = $exported;

		return $csv;
	}


	/**
	 * Get an individual Pickup Location CSV row data.
	 *
	 * @since 2.0.0
	 *
	 * @param array $headers CSV headers
	 * @param \WC_Local_Pickup_Plus_Pickup_Location $pickup_location the pickup location
	 * @return array
	 */
	private function get_csv_row( $headers, $pickup_location ) {

		$row     = array();
		$columns = array_keys( $headers );

		if ( ! empty( $columns ) ) {

			foreach ( $columns as $column_name ) {

				switch ( $column_name ) {

					case 'id' :
					case 'name' :
					case 'description' :

						$method = "get_{$column_name}";
						$value  = method_exists( $pickup_location, $method ) ? $pickup_location->$method() : '';

					break;

					case 'status' :

						$post  = $pickup_location->get_post();
						$value = $post instanceof \WP_Post ? $post->post_status : '';

					break;

					case 'country' :
					case 'state' :
					case 'city' :
					case 'postcode' :
					case 'address_1' :
					case 'address_2' :
						$value = $pickup_location->get_address( $column_name );
					break;

					case 'phone' :
						$value = $pickup_location->get_phone();
					break;

					case 'latitude' :
					case 'longitude' :

						$coordinates = $pickup_location->get_coordinates();
						$abbr        = substr( $column_name, 0, 3 );
						$value       = isset( $coordinates[ $abbr ] ) ? $coordinates[ $abbr ] : 0.000000;

					break;

					case 'products' :
					case 'product_categories' :

						$items = 'products' === $column_name ? $pickup_location->get_products( array( 'exclude_categories' => true ) ) : $pickup_location->get_product_categories();
						$value = ! empty( $items ) ? implode( ',', $items ) : '';

					break;

					case 'business_hours' :

						$week        = $pickup_location->get_business_hours()->get_value();
						$string_days = array();
						$value       = '';

						// we need to ensure the days are not integers or they might not be parsed as keys in the json object
						if ( ! empty( $week ) ) {
							foreach ( $week as $day => $schedule ) {
								$string_days[ (string) $day ] = $schedule;
							}
						}

						if ( ! empty( $string_days ) ) {
							// this equates to the JSON_FORCE_OBJECT constant flag, which is only available in 5.3+
							$value = wp_json_encode( $string_days, 16 );
						}

					break;

					case 'price_adjustment' :
					case 'public_holidays' :
					case 'pickup_lead_time' :
					case 'pickup_deadline' :

						$method = "get_{$column_name}";
						$value  = method_exists( $pickup_location, $method ) ? $pickup_location->$method()->get_value() : '';
						$value = is_array( $value ) ? implode( ',', $value ) : $value;

					break;

					case 'pickup_notifications':
						$value = $pickup_location->get_email_recipients( 'string' );
					break;

					default :

						/**
						 * Filter Pickup Location CSV data custom column.
						 *
						 * @since 2.0.0
						 *
						 * @param string $value the value that should be returned for this column, default empty string
						 * @param string $key the matching key of this column
						 * @param \WC_Local_Pickup_Plus_Pickup_Location $pickup_location pickup location object
						 * @param \WC_Local_Pickup_Plus_Export $export_instance an instance of the export class
						 */
						$value = apply_filters( "wc_local_pickup_plus_csv_export_pickup_locations_{$column_name}_column", '', $column_name, $pickup_location, $this );

					break;
				}

				$row[ $column_name ] = $value;
			}
		}

		/**
		 * Filter Pickup Location CSV row data.
		 *
		 * @since 2.0.0
		 *
		 * @param array $row pickup location data in associative array format for CSV output
		 * @param \WC_Local_Pickup_Plus_Pickup_Location $pickup_location pickup location object
		 * @param \WC_Local_Pickup_Plus_Export $export_instance instance of the export class
		 */
		return (array) apply_filters( 'wc_local_pickup_plus_csv_export_pickup_location_row', $row, $pickup_location, $this );
	}


	/**
	 * Process an export from bulk action request.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 */
	public function process_bulk_export( ) {

		$wp_list_table = _get_list_table( 'WP_Posts_List_Table' );
		$action        = $wp_list_table->current_action();

		if ( 'export' === $action ) {

			if ( ! current_user_can( 'manage_woocommerce' ) ) {

				wp_die( __( 'You are not allowed to perform this action.', 'woocommerce-shipping-local-pickup-plus' ) );

			} else {

				$post_ids = isset( $_GET['post'] ) ? array_map( 'absint', $_GET['post'] ) : array();

				$this->process_export( $post_ids );
			}
		}
	}


}

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
 * @copyright   Copyright (c) 2012-2022, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_10_12 as Framework;

/**
 * Pickup Locations Export class.
 *
 * @since 2.0.0
 */
class WC_Local_Pickup_Plus_Import extends \WC_Local_Pickup_Plus_Import_Export  {


	/** @var bool whether to create new pickup locations during an import process */
	private $create_new_pickup_locations = false;

	/** @var bool whether to merge (update) existing locations data during an import */
	private $merge_existing_pickup_locations = false;


	/**
	 * Import pickup locations constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		$this->action_id        = 'import';
		$this->action_label     = __( 'Import', 'woocommerce-shipping-local-pickup-plus' );
		$this->admin_page_title = __( 'Import Pickup Locations', 'woocommerce-shipping-local-pickup-plus' );
		$this->delimiter_option = 'wc_local_pickup_plus_pickup_locations_csv_import_fields_delimiter';

		// add CSV file input field handler
		add_action( 'woocommerce_admin_field_wc-local-pickup-plus-file', array( $this, 'render_file_upload_field' ) );

		parent::__construct();
	}


	/**
	 * Get import fields.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @return array associative array with field data
	 */
	protected function get_fields() {

		$documentation_url = 'https://docs.woocommerce.com/document/local-pickup-plus/#import-locations';
		$max_upload_size   = size_format( wc_let_to_num( ini_get( 'post_max_size' ) ) );

		$options = array(

			array(
				'title' => $this->admin_page_title,
				'desc'  => '<small class="wc-admin-breadcrumb" style="display:none;"><a href="' . esc_url( $this->parent_url ) . '" aria-label="' . esc_html__( 'Return to Pickup Locations', 'woocommerce-shipping-local-pickup-plus' ) . '"><img draggable="false" class="emoji" alt="⤴" src="//s.w.org/images/core/emoji/2.2.1/svg/2934.svg"></a></small>'
				           /* translators: Placeholders: %1$s - opening <a> link HTML tag, $2$s - closing </a> link HTML tag */
				           . sprintf( __( 'Your CSV file must be formatted with the correct column names and cell data. Please %1$ssee the documentation%2$s for more information and a sample CSV file.', 'woocommerce-shipping-local-pickup-plus' ), '<a href="' . $documentation_url . '">', '</a>' ),
				'type'  => 'title',
			),

			array(
				'id'       => 'wc_local_pickup_plus_csv_import_pickup_locations_source_file',
				'title'    => __( 'Choose a file from your computer', 'woocommerce-shipping-local-pickup-plus' ),
				/* translators: Placeholder: %s - maximum uploadable file size (e.g. 8M, 20M, 100M...)  */
				'desc_tip' => sprintf( __( 'Acceptable file types: CSV or tab-delimited text files. Maximum file size: %s', 'woocommerce-shipping-local-pickup-plus' ), empty( $max_upload_size ) ? '<em>' . __( 'Undetermined', 'woocommerce-shipping-local-pickup-plus' ) . '</em>' : $max_upload_size ),
				'type'     => 'wc-local-pickup-plus-file',
			),

			array(
				'id'            => 'wc_local_pickup_plus_csv_import_pickup_locations_merge_existing',
				'title'         => __( 'Import Options', 'woocommerce-shipping-local-pickup-plus' ),
				'desc'          => __( 'Update existing records if a matching pickup location is found (by Pickup Location ID)', 'woocommerce-shipping-local-pickup-plus' ),
				'default'       => 'yes',
				'type'          => 'checkbox',
				'checkboxgroup' => 'start',
			),

			array(
				'id'            => 'wc_local_pickup_plus_csv_import_pickup_locations_create_new',
				'desc'          => __( 'Create new pickup locations if a matching Pickup Location ID is not found (skips rows when disabled)', 'woocommerce-shipping-local-pickup-plus' ),
				'default'       => 'yes',
				'type'          => 'checkbox',
				'checkboxgroup' => '',
			),

			array(
				'id'       => $this->delimiter_option,
				'title'    => __( 'Fields are separated by', 'woocommerce-shipping-local-pickup-plus' ),
				'type'     => 'select',
				'desc_tip' => __( 'Change the delimiter based on your input file format.', 'woocommerce-shipping-local-pickup-plus' ),
				'default'  => 'comma',
				'options'  => array(
					'comma' => __( 'Comma', 'woocommerce-shipping-local-pickup-plus' ),
					'tab'   => __( 'Tab space', 'woocommerce-shipping-local-pickup-plus' ),
				),
			),

			array(
				'type' => 'sectionend',
			),

		);

		/**
		 * Filter the CSV Import Pickup Locations options.
		 *
		 * @since 2.0.0
		 * @param array $options associative array.
		 */
		return apply_filters( 'wc_local_pickup_plus_csv_import_pickup_locations_options', $options );
	}


	/**
	 * Output a file input field
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param array $field field settings
	 */
	public function render_file_upload_field( $field ) {

		$field = wp_parse_args( $field, array(
			'id'       => '',
			'title'    => __( 'Choose a file from your computer', 'woocommerce-shipping-local-pickup-plus' ),
			'desc'     => '',
			'desc_tip' => '',
			'type'     => 'wc-local-pickup-plus-file',
			'class'    => '',
			'css'      => '',
			'value'    => '',
		) );

		?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $field['id'] ); ?>"><?php echo esc_html( $field['title'] ); ?></label>
			</th>
			<td class="forminp forminp-<?php echo sanitize_html_class( $field['type'] ) ?>">
				<input
					type="hidden"
					name="MAX_FILE_SIZE"
					value="<?php echo wp_max_upload_size(); ?>"
				/>
				<input
					name="<?php echo esc_attr( $field['id'] ); ?>"
					id="<?php echo esc_attr( $field['id'] ); ?>"
					type="file"
					style="<?php echo esc_attr( $field['css'] ); ?>"
					value="<?php echo esc_attr( $field['value'] ); ?>"
					class="<?php echo esc_attr( $field['class'] ); ?>"
				/><br><span class="description"><?php echo $field['desc_tip']; ?></span>
			</td>
		</tr>
		<?php
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
		 * Filter the Pickup Locations CSV import file row headers.
		 *
		 * @since 2.0.0
		 *
		 * @param array $csv_headers associative array
		 * @param \WC_Local_Pickup_Plus_Import $import_instance instance of the import class
		 */
		return (array) apply_filters( 'wc_local_pickup_plus_csv_import_pickup_locations_headers', $headers, $this );
	}


	/**
	 * Process input form submission to import.
	 *
	 * @since 2.0.0
	 */
	public function process_import() {

		// bail out and return an error notice if no file was added for upload
		if ( empty( $_FILES['wc_local_pickup_plus_csv_import_pickup_locations_source_file'] ) || empty( $_FILES['wc_local_pickup_plus_csv_import_pickup_locations_source_file']['name'] ) ) {

			wc_local_pickup_plus()->get_message_handler()->add_error(
				__( 'You must upload a file to import Pickup Locations from.', 'woocommerce-shipping-local-pickup-plus' )
			);

		// bail out if an upload error occurred (most likely a server issue)
		} elseif ( isset( $_FILES['wc_local_pickup_plus_csv_import_pickup_locations_source_file']['error'] ) && $_FILES['wc_local_pickup_plus_csv_import_pickup_locations_source_file']['error'] > 0 ) {

			wc_local_pickup_plus()->get_message_handler()->add_error(
				/* translators: Placeholder: %s - error message */
				sprintf( __( 'There was a problem uploading the file: %s', 'woocommerce-shipping-local-pickup-plus' ),
					'<em>' . $this->get_file_upload_error( $_FILES['wc_local_pickup_plus_csv_import_pickup_locations_source_file']['error'] ) . '</em>'
				)
			);

		// process the file once uploaded
		} else {

			// get CSV data from file
			if ( isset( $_FILES['wc_local_pickup_plus_csv_import_pickup_locations_source_file']['tmp_name'] ) ) {
				$csv_data = $this->parse_file_csv( $_FILES['wc_local_pickup_plus_csv_import_pickup_locations_source_file']['tmp_name'] );
			}

			// bail out if the file can't be parsed or there are only headers
			if ( empty( $csv_data ) || count( $csv_data ) <= 1 ) {

				wc_local_pickup_plus()->get_message_handler()->add_error(
					__( 'Could not find Pickup Locations to import from uploaded file.', 'woocommerce-shipping-local-pickup-plus' )
				);

			// proceed
			} else {

				// set importing options
				$this->create_new_pickup_locations     = isset( $_POST['wc_local_pickup_plus_csv_import_pickup_locations_create_new'] )     ? 1 === (int) $_POST['wc_local_pickup_plus_csv_import_pickup_locations_create_new']     : $this->create_new_pickup_locations;
				$this->merge_existing_pickup_locations = isset( $_POST['wc_local_pickup_plus_csv_import_pickup_locations_merge_existing'] ) ? 1 === (int) $_POST['wc_local_pickup_plus_csv_import_pickup_locations_merge_existing'] : $this->merge_existing_pickup_locations;

				// process rows to import
				$this->import_pickup_locations( $csv_data );
			}
		}
	}


	/**
	 * Parse a file with CSV data into an array.
	 *
	 * @since 2.0.0
	 *
	 * @param resource $file_handle file to process as a resource
	 * @return null|array array data or null on read error
	 */
	private function parse_file_csv( $file_handle ) {

		if ( is_readable( $file_handle ) ) {

			$csv_data = array();

			// get the data from file
			$file_contents = fopen( $file_handle, 'r' );

			// this helps with files from some spreadsheet/csv editors, such as Excel on Mac computers which seem to handle line breaks differently
			@ini_set( 'auto_detect_line_endings', true );

			// handle character encoding
			if ( $enc = mb_detect_encoding( $file_handle, 'UTF-8, ISO-8859-1', true ) ) {
				setlocale( LC_ALL, 'en_US.' . $enc );
			}

			$delimiter = $this->get_fields_delimiter();
			$enclosure = $this->get_enclosure();

			while ( ( $row = fgetcsv( $file_contents, 0, $delimiter, $enclosure ) ) !== false ) {
				$csv_data[] = $row;
			}

			fclose( $file_contents );

			return $csv_data;
		}

		return null;
	}


	/**
	 * Import Pickup Locations from CSV data.
	 *
	 * @since 2.0.0
	 *
	 * @param array $rows CSV import data parsed into an array format, with headers in the first key
	 */
	private function import_pickup_locations( array $rows ) {

		$created = 0;
		$merged  = 0;

		// get the column keys and remove them from the data set
		$columns = array_flip( $rows[0] );
		unset( $rows[0] );

		$total = count( $rows );

		if ( ! empty( $columns ) && ! empty( $rows ) ) {

			foreach ( $rows as $row ) {

				// try to get a Pickup Location ID
				$pickup_location_id = isset( $columns['id'] ) && ! empty( $row[ $columns['id'] ] ) ? (int) $row[ $columns['id'] ] : null;
				$pickup_location    = is_int( $pickup_location_id ) ? wc_local_pickup_plus_get_pickup_location( $pickup_location_id ) : null;

				if ( ! $pickup_location && false === $this->create_new_pickup_locations ) {
					// bail if no Pickup Location is found to update and we can't create a new one by import setting
					continue;
				}

				if ( $pickup_location && false === $this->merge_existing_pickup_locations ) {
					// bail if there is already an existing Pickup Location but we can't update it by import setting
					continue;
				}

				$import_data = array();
				$csv_headers = array_keys( $this->get_csv_headers() );

				// gather import data
				foreach ( $csv_headers as $column_key ) {
					$import_data[ $column_key ] = isset( $columns[ $column_key ] ) && ! empty( $row[ $columns[ $column_key ] ] ) ? $row[ $columns[ $column_key ] ] : null;
				}

				$import_data['id']              = $pickup_location_id;
				$import_data['pickup_location'] = $pickup_location;

				/**
				 * Filter Pickup Location CSV import data before processing an import.
				 *
				 * @since 2.0.0
				 *
				 * @param array $import_data the imported data as associative array
				 * @param string $action either 'create' or 'merge' (update) a Pickup Location
				 * @param array $columns CSV columns raw data
				 * @param array $row CSV row raw data
				 */
				$import_data = (array) apply_filters( 'wc_local_pickup_plus_csv_import_pickup_location', $import_data, true === $this->create_new_pickup_locations ? 'create' : 'merge', $columns, $row );

				// create or update a Pickup Location and bump counters
				if ( ! $pickup_location && true === $this->create_new_pickup_locations ) {
					$created += (int) $this->import_pickup_location( 'create', $import_data );
				} elseif ( $pickup_location && true === $this->merge_existing_pickup_locations ) {
					$merged  += (int) $this->import_pickup_location( 'merge', $import_data );
				}
			}
		}

		// output results as admin notice
		$this->show_results_notice( $total, $created, $merged );
	}


	/**
	 * Creates or updates a Pickup Location according to import data.
	 *
	 * @since 2.0.0
	 *
	 * @param string $action either 'create' or 'merge' (for updating)
	 * @param array $import_data pickup location import data
	 * @return null|bool
	 */
	private function import_pickup_location( $action = '', $import_data = array() ) {

		$pickup_location = null;
		$post_status     = isset( $import_data['status'] ) && in_array( $import_data['status'], array( 'draft', 'publish' ), false ) ? $import_data['status'] : 'publish';

		switch ( $action ) {

			case 'create' :

				$pickup_location_id = wp_insert_post( array(
					'post_status' => $post_status,
					'post_type'   => 'wc_pickup_location',
				) );

				if ( $pickup_location = wc_local_pickup_plus_get_pickup_location( $pickup_location_id ) ) {
					$pickup_location = $this->import_pickup_location_data( $pickup_location, $import_data, 'create' );
				}

			break;

			case 'merge' :

				if ( isset( $import_data ) && $import_data['pickup_location'] instanceof \WC_Local_Pickup_Plus_Pickup_Location ) {

					if ( ! empty( $import_data['status'] ) ) {

						$post = $import_data['pickup_location']->get_post();

						if ( $post instanceof \WP_Post && $post->post_status !== $post_status ) {

							wp_update_post( array(
								'ID'          => $import_data['pickup_location']->get_id(),
								'post_status' => $post_status
							) );
						}
					}

					$pickup_location = $this->import_pickup_location_data( $import_data['pickup_location'], $import_data, 'merge' );
				}

			break;
		}

		if ( $pickup_location instanceof \WC_Local_Pickup_Plus_Pickup_Location ) {

			/**
			 * Upon creating or updating a Pickup Location via import.
			 *
			 * @since 2.0.0
			 *
			 * @param \WC_Local_Pickup_Plus_Pickup_Location $pickup_location the imported pickup location
			 * @param string $action either 'create' or 'merge' (update) a pickup location
			 * @param array $data import data used in import process
			 */
			do_action( 'wc_local_pickup_plus_csv_import_pickup_location', $pickup_location, $action, $import_data );

			return true;
		}

		return false;
	}


	/**
	 * Update pickup location data.
	 *
	 * @since 2.0.0
	 *
	 * @param \WC_Local_Pickup_Plus_Pickup_Location $pickup_location the pickup location being updated
	 * @param array $import_data associative array with import data
	 * @param string $action type of import: 'create' or 'merge' a new or exiting entry
	 * @return null|\WC_Local_Pickup_Plus_Pickup_Location
	 */
	private function import_pickup_location_data( \WC_Local_Pickup_Plus_Pickup_Location $pickup_location, array $import_data, $action ) {

		// bail out if there's nothing to import
		if ( empty( $import_data ) || ! in_array( $action, array( 'create', 'merge' ), true ) ) {
			return null;
		}

		$local_pickup_plus = wc_local_pickup_plus_shipping_method();

		if ( $local_pickup_plus && $pickup_location instanceof \WC_Local_Pickup_Plus_Pickup_Location ) {

			$location_name = is_string( $import_data['name'] ) ? sanitize_text_field( $this->unescape_value( $import_data['name'] ) ) : '';

			// import location name (post name)
			$pickup_location->set_name( $location_name );

			// import description (address notes / post content)
			$pickup_location->set_description( ! empty( $import_data['description'] ) ? $import_data['description'] : '' );

			// import address
			$pickup_location->set_address( wp_parse_args( array(
				'name'      => $location_name,
				'address_1' => isset( $import_data['address_1'] ) ? sanitize_text_field( $this->unescape_value( $import_data['address_1'] ) )             : '',
				'address_2' => isset( $import_data['address_2'] ) ? sanitize_text_field( $this->unescape_value( $import_data['address_2'] ) )             : '',
				'postcode'  => isset( $import_data['postcode'] )  ? sanitize_text_field( $this->unescape_value( $import_data['postcode']  ) )             : '',
				'city'      => isset( $import_data['city'] )      ? sanitize_text_field( $this->unescape_value( $import_data['city']      ) )             : '',
				'state'     => isset( $import_data['state'] )     ? strtoupper( sanitize_text_field( $this->unescape_value( $import_data['state'] ) ) )   : '',
				'country'   => isset( $import_data['country'] )   ? strtoupper( sanitize_text_field( $this->unescape_value( $import_data['country'] ) ) ) : '',
			), $pickup_location->get_address()->get_array() ) );

			// import location phone number
			$phone = ! empty( $import_data['phone'] ) ? sanitize_text_field( $this->unescape_value( $import_data['phone'] ) ) : null;
			if ( is_string( $phone ) && '' !== $phone ) {
				$pickup_location->set_phone( $phone );
			} else {
				$pickup_location->delete_phone();
			}

			// import coordinates
			if ( ! empty( $import_data['latitude'] ) && ! empty( $import_data['longitude'] ) ) {
				$pickup_location->set_coordinates( $this->unescape_value( $import_data['latitude'] ), $this->unescape_value( $import_data['longitude'] ) );
			} else {
				$pickup_location->delete_coordinates();
			}

			// import available products and categories
			$products           = ! empty( $import_data['products'] )           ? array_map( 'absint', array_map( 'trim', explode( ',', $import_data['products'] ) ) )           : array();
			$product_categories = ! empty( $import_data['product_categories'] ) ? array_map( 'absint', array_map( 'trim', explode( ',', $import_data['product_categories'] ) ) ) : array();
			$pickup_location->set_products( array_filter( $products ) );
			$pickup_location->set_product_categories( array_filter( $product_categories ) );

			// import price adjustment
			$price_adjustment = ! empty( $import_data['price_adjustment'] ) ? $this->unescape_value( $import_data['price_adjustment'] ) : null;
			if ( ! empty( $price_adjustment ) && $price_adjustment !== $local_pickup_plus->get_default_price_adjustment() ) {
				$amount     = Framework\SV_WC_Helper::str_starts_with( $price_adjustment, '-' ) ? (float) ( '-' . abs( $price_adjustment ) ) : abs( $price_adjustment );
				$type       = Framework\SV_WC_Helper::str_ends_with( $price_adjustment, '%' ) ? 'percentage' : 'fixed';
				$pickup_location->set_price_adjustment( $amount > 0 ? 'cost' : 'discount', $amount, $type );
				update_post_meta( $pickup_location->get_id(), '_pickup_location_price_adjustment_enabled', 'yes' );
			} else {
				update_post_meta( $pickup_location->get_id(), '_pickup_location_price_adjustment_enabled', 'no' );
				$pickup_location->delete_price_adjustment();
			}

			// import business hours for pickup
			$business_hours_raw     = ! empty( $import_data['business_hours'] ) ? json_decode( $import_data['business_hours'], true ) : array();
			$default_business_hours = new \WC_Local_Pickup_Plus_Business_Hours( $local_pickup_plus->get_default_business_hours() );
			if ( ! empty( $business_hours_raw )  ) {
				$business_hours = array();
				// this ensures we have string keys set in our array
				foreach ( $business_hours_raw as $day => $schedule ) {
					$business_hours[ (string) $day ] = $schedule;
				}
				$business_hours = new \WC_Local_Pickup_Plus_Business_Hours( $business_hours );
				$business_hours = $business_hours->get_value();
			}
			if ( ! empty( $business_hours ) && $business_hours !== $default_business_hours->get_value() ) {
				update_post_meta( $pickup_location->get_id(), '_pickup_location_business_hours_enabled', 'yes' );
				$pickup_location->set_business_hours( $business_hours );
			} else {
				update_post_meta( $pickup_location->get_id(), '_pickup_location_business_hours_enabled', 'no' );
				$pickup_location->delete_business_hours();
			}

			// import public holidays calendar
			$public_holidays_raw = ! empty( $import_data['public_holidays'] ) && is_string( $import_data['public_holidays'] ) ? array_filter( array_map( 'trim', explode( ',', $this->unescape_value( $import_data['public_holidays'] ) ) ) ) : array();
			$public_holidays     = ! empty( $public_holidays_raw ) && is_array( $public_holidays_raw ) ? new \WC_Local_Pickup_Plus_Public_Holidays( $public_holidays_raw ) : null;
			$public_holidays     = null !== $public_holidays ? $public_holidays->get_calendar_dates() : array();
			$default_holidays    = new \WC_Local_Pickup_Plus_Public_Holidays( $local_pickup_plus->get_default_public_holidays() );
			if ( ! empty( $public_holidays ) && $public_holidays !== $default_holidays->get_calendar_dates() ) {
				update_post_meta( $pickup_location->get_id(), '_pickup_location_public_holidays_enabled', 'yes' );
				$pickup_location->set_public_holidays( $public_holidays );
			} else {
				update_post_meta( $pickup_location->get_id(), '_pickup_location_public_holidays_enabled', 'no' );
				$pickup_location->delete_public_holidays();
			}

			// import pickup lead time
			$pickup_lead_time = ! empty( $import_data['pickup_lead_time'] ) ? $this->unescape_value( $import_data['pickup_lead_time'] ) : null;
			if ( ! empty( $pickup_lead_time ) && $pickup_lead_time !== $local_pickup_plus->get_default_pickup_lead_time() ) {
				$pickup_lead_time = explode( ' ', $pickup_lead_time );
				if ( isset( $pickup_lead_time[1] ) ) {
					update_post_meta( $pickup_location->get_id(), '_pickup_location_pickup_lead_time_enabled', 'yes' );
					$pickup_location->set_pickup_lead_time( (int) $pickup_lead_time[0], $pickup_lead_time[1] );
				}
			} else {
				update_post_meta( $pickup_location->get_id(), '_pickup_location_pickup_lead_time_enabled', 'no' );
				$pickup_location->delete_pickup_lead_time();
			}

			// import pickup deadline
			$pickup_deadline = ! empty( $import_data['pickup_deadline'] ) ? $this->unescape_value( $import_data['pickup_deadline'] ) : null;
			if ( ! empty( $pickup_deadline ) && $pickup_deadline !== $local_pickup_plus->get_default_pickup_deadline() ) {
				$pickup_deadline = explode( ' ', $pickup_deadline );
				if ( isset( $pickup_deadline[1] ) ) {
					update_post_meta( $pickup_location->get_id(), '_pickup_location_pickup_deadline_enabled', 'yes' );
					$pickup_location->set_pickup_deadline( $pickup_deadline[0], $pickup_deadline[1] );
				}
			} else {
				update_post_meta( $pickup_location->get_id(), '_pickup_location_pickup_deadline_enabled', 'no' );
				$pickup_location->delete_pickup_deadline();
			}

			// import email recipients for pickup notifications
			$notifications = ! empty( $import_data['pickup_notifications'] ) ? $this->unescape_value( $import_data['pickup_notifications'] ) : array();
			if ( ! empty( $notifications ) ) {
				$pickup_location->set_email_recipients( $notifications );
			} else {
				$pickup_location->delete_email_recipients();
			}
		}

		return $pickup_location;
	}


	/**
	 * Show a notice with import results
	 *
	 * @since 2.0.0
	 *
	 * @param int $total_rows total rows in CSV file
	 * @param int $created pickup locations created
	 * @param int $merged pickup locations merged/updated
	 */
	private function show_results_notice( $total_rows = 0, $created = 0, $merged = 0 ) {

		$message_handler = wc_local_pickup_plus()->get_message_handler();
		$rows_processed  = $created + $merged;
		$skipped_rows    = $total_rows - $rows_processed;

		if ( 0 === $total_rows ) {

			$notice_type = 'error';
			$message     = __( 'Could not find Pickup Locations to import from uploaded file.', 'woocommerce-shipping-local-pickup-plus' );

		} else {

			/* translators: Placeholder: %s - Pickup Locations to import found in uploaded file */
			$message = sprintf( _n( '%s record found in file.', '%s records found in file.', $total_rows, 'woocommerce-shipping-local-pickup-plus' ), $total_rows ) . '<br>';

			if ( $rows_processed > 0 ) {

				$notice_type = 'message';

				/* translators: Placeholder: %s - Pickup Locations processed during import from file */
				$message .= ' ' . sprintf( _n( '%s row processed for import.', '%s rows processed for import.', $rows_processed, 'woocommerce-shipping-local-pickup-plus' ), $rows_processed );

				if ( $created > 0 ) {
					/* translators: Placeholder: %s - Pickup Locations created in import */
					$message .= ' ' . sprintf( _n( '%s new Pickup Location created.', '%s new Pickup Locations created.', $created, 'woocommerce-shipping-local-pickup-plus' ), $created );
				}

				if ( $merged > 0 ) {
					/* translators: Placeholder: %s - Pickup Locations updated during import */
					$message .= ' ' . sprintf( _n( '%s existing Pickup Location updated.', '%s existing Pickup Locations updated.', $merged, 'woocommerce-shipping-local-pickup-plus' ), $merged );
				}

				if ( $skipped_rows > 0 ) {
					/* translators: Placeholder: %s - skipped Pickup Locations to import from file */
					$message .= ' ' . sprintf( _n( '%s row skipped.', '%s rows skipped.', $skipped_rows, 'woocommerce-shipping-local-pickup-plus' ), $skipped_rows );
				}

			} else {

				$notice_type  = 'error';
				$message     .=  __( 'However, no Pickup Locations were created or updated with the given options.', 'woocommerce-shipping-local-pickup-plus' );
			}
		}

		$method = "add_{$notice_type}";

		if ( is_callable( array( $message_handler, $method ) ) ) {

			$message_handler->$method( $message );
		}
	}


	/**
	 * Get an error message for file upload failure.
	 *
	 * @see http://php.net/manual/en/features.file-upload.errors.php
	 *
	 * @since 2.0.0
	 *
	 * @param int $error_code a PHP error code
	 * @return string error message
	 */
	private function get_file_upload_error( $error_code ) {

		switch ( $error_code ) {
			case 1 :
			case 2 :
				return __( 'The file uploaded exceeds the maximum file size allowed.', 'woocommerce-shipping-local-pickup-plus' );
			case 3 :
				return __( 'The file was only partially uploaded. Please try again.', 'woocommerce-shipping-local-pickup-plus' );
			case 4 :
				return __( 'No file was uploaded.', 'woocommerce-shipping-local-pickup-plus' );
			case 6 :
				return __( 'Missing a temporary folder to store the file. Please contact your host.', 'woocommerce-shipping-local-pickup-plus' );
			case 7 :
				return __( 'Failed to write file to disk. Perhaps a permissions error, please contact your host.', 'woocommerce-shipping-local-pickup-plus' );
			case 8 :
				return __( 'A PHP Extension stopped the file upload. Please contact your host.', 'woocommerce-shipping-local-pickup-plus' );
			default :
				return __( 'Unknown error.', 'woocommerce-shipping-local-pickup-plus' );
		}
	}


}

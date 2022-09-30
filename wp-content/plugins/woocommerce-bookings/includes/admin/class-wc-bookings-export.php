<?php

/**
 * Class for export functionality. Inspired from the https://github.com/woocommerce/bookings-helper/.
 */
class WC_Bookings_Single_Export {

	/**
	 * Temporary directory path.
	 *
	 * @var string
	 */
	public $temp_dir;

	/**
	 * Checks to see if ZipArchive library exists.
	 *
	 * @var boolean
	 */
	public $ziparchive_available;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->temp_dir             = get_temp_dir() . 'woocommerce-bookings';
		$this->ziparchive_available = class_exists( 'ZipArchive' );

		add_action( 'admin_init', array( $this, 'catch_export_requests' ), 20 );
	}

	/**
	 * Catches form requests.
	 */
	public function catch_export_requests() {
		if ( ! isset( $_GET['action'] )
		     || 'export_product_with_global_rules' !== $_GET['action']
		     || ! isset( $_GET['_wpnonce'] )
		) {
			return;
		}

		if ( ! wp_verify_nonce( $_GET['_wpnonce'], 'export_product_with_global_rules' ) ) {
			wp_die( esc_html__( 'Unauthorised request, please try again.', 'woocommerce-bookings' ) );
		}

		// Export Product and Global rules.
		$this->export_product_with_global_rules();
	}

	/**
	 * Exports a specific product and the Global rules.
	 *
	 * @throws Exception Show error if no product exists.
	 */
	public function export_product_with_global_rules() {
		try {
			global $wpdb;

			// Check Product existence.
			$product_id     = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : '';
			$product_status = get_post_status( $product_id );
			if ( empty( $product_id ) || ! $product_status ) {
				wp_die( esc_html__( 'This booking product does not exist!', 'woocommerce-bookings' ) );
			}

			$product = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->posts} WHERE post_type = 'product' AND ID = %d", $product_id ), ARRAY_A );
			if ( empty( $product ) ) {
				wp_die( esc_html__( 'This booking product does not exist!', 'woocommerce-bookings' ) );
			}

			// Get the type of the product, accomm or booking.
			$product_type       = wp_get_post_terms( $product[0]['ID'], 'product_type' );
			$product[0]['type'] = $product_type[0]->name;

			// Product metas.
			$product_meta = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->postmeta} WHERE post_id = %d AND ( meta_key LIKE '%%wc_booking%%' OR meta_key = '_resource_base_costs' OR meta_key = '_resource_block_costs' OR meta_key = '_wc_display_cost' OR meta_key = '_virtual' )", $product_id ), ARRAY_A );

			if ( empty( $product_meta ) ) {
				wp_die( esc_html__( 'This booking product does not exist!', 'woocommerce-bookings' ) );
			}

			// Booking relationships ( resources ).
			$resources = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wc_booking_relationships WHERE product_id = %d", $product_id ), ARRAY_A );

			$prepared_resources = array();
			$prepared_persons   = array();

			// If resources exists, we need to extract the meta
			// information for each resource.
			if ( ! empty( $resources ) ) {
				foreach ( $resources as $key => $value ) {
					$resource = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->posts} WHERE post_type = 'bookable_resource' AND ID = %d", $value['resource_id'] ), ARRAY_A );

					if ( ! empty( $resource ) ) {
						$resource_meta = $wpdb->get_results( $wpdb->prepare( "SELECT meta_key, meta_value FROM {$wpdb->postmeta} WHERE post_id = %d AND ( meta_key = 'qty' OR meta_key = '_wc_booking_availability' )", $value['resource_id'] ), ARRAY_A );
					}

					$prepared_resources[] = array(
						'resource'      => $resource[0],
						'resource_meta' => $resource_meta,
					);
				}
			}

			// Persons.
			$persons = $wpdb->get_results( $wpdb->prepare( "SELECT ID, post_title, post_excerpt FROM {$wpdb->posts} WHERE post_type = 'bookable_person' AND post_parent = %d", $product_id ), ARRAY_A );

			if ( ! empty( $persons ) ) {
				foreach ( $persons as $person ) {
					$person_meta = $wpdb->get_results( $wpdb->prepare( "SELECT meta_key, meta_value FROM {$wpdb->postmeta} WHERE post_id = %d", $person['ID'] ), ARRAY_A );

					$prepared_persons[] = array( 'person' => $person, 'person_meta' => $person_meta );
				}
			}

			// Get global rules.
			$global_rules = $this->get_global_rules();

			$prepared_json = wp_json_encode(
				array(
					'product'      => $product[0],
					'product_meta' => $product_meta,
					'resources'    => $prepared_resources,
					'persons'      => $prepared_persons,
					'global_rules' => $global_rules,
				)
			);

			$this->trigger_download( $prepared_json, 'booking-product-' . $product_id . '-with-global-rules' );
		} catch ( Exception $e ) {
			wp_die( $e->getMessage() );
		}
	}

	/**
	 * Gets global availability rules.
	 *
	 * @throws Exception If error occurs, show it.
	 */
	public function get_global_rules() {
		try {
			$global_rules_json = '';

			if ( version_compare( WC_BOOKINGS_VERSION, '1.13.0', '<' ) ) {
				$global_rules = get_option( 'wc_global_booking_availability', array() );
			} else {
				$global_rules = WC_Data_Store::load( 'booking-global-availability' )->get_all_as_array();
			}

			if ( ! empty( $global_rules ) ) {
				$global_rules_json = wp_json_encode( $global_rules );
			}

			return $global_rules_json;
		} catch ( Exception $e ) {
			wp_die( $e->getMessage() );
		}
	}

	/**
	 * Triggers the download feature of the browser.
	 *
	 * @param string $data   Data to add to file.
	 * @param string $prefix File prefix to use.
	 *
	 * @throws Exception Show error if something goes wrong.
	 */
	public function trigger_download( $data = '', $prefix = '' ) {
		if ( empty( $data ) ) {
			return;
		}

		@set_time_limit( 0 );

		// Disable GZIP.
		if ( function_exists( 'apache_setenv' ) ) {
			@apache_setenv( 'no-gzip', 1 );
		}

		@ini_set( 'zlib.output_compression', 'Off' );
		@ini_set( 'output_buffering', 'Off' );
		@ini_set( 'output_handler', '' );

		$filename_prefix = $prefix;

		if ( $this->ziparchive_available ) {
			$filename = sprintf( '%1$s-%2$s', $filename_prefix, date( 'Y-m-d', current_time( 'timestamp' ) ) );

			$this->prep_transfer();

			$this->render_headers( $filename );

			if ( $this->create_zip( $filename, $data ) ) {
				readfile( $this->temp_dir . '/' . $filename . '.zip' );

				$this->clean_up();

				exit;
			} else {
				wp_die( esc_html__( 'Unable to export!', 'woocommerce-bookings' ) );
			}
		} else {
			$filename = sprintf( '%1$s-%2$s.json', $filename_prefix, date( 'Y-m-d', current_time( 'timestamp' ) ) );

			$this->render_headers( $filename );

			file_put_contents( 'php://output', $data );

			exit;
		}
	}

	/**
	 * Cleans up lingering files and folder during transfer.
	 *
	 * @param string|null $path Folder path.
	 */
	public function clean_up( $path = null ) {
		if ( null === $path ) {
			$path = $this->temp_dir;
		}

		if ( is_dir( $path ) ) {
			$objects = scandir( $path );

			foreach ( $objects as $object ) {
				if ( '.' !== $object && '..' !== $object ) {
					if ( is_dir( $path . '/' . $object ) ) {
						$this->clean_up( $path . '/' . $object );
					} else {
						unlink( $path . '/' . $object );
					}
				}
			}

			rmdir( $path );
		}
	}

	/**
	 * Creates the zip file.
	 *
	 * @param string $filename Name of file.
	 * @param string $data     Data to be zipped.
	 *
	 * @return bool
	 */
	public function create_zip( $filename, $data = false ) {
		$zip_file = $this->temp_dir . '/' . $filename . '.zip';

		$zip = new ZipArchive();
		$zip->open( $zip_file, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE );
		$zip->addFromString( $filename . '.json', $data );
		$zip->close();

		if ( file_exists( $this->temp_dir . '/' . $filename . '.zip' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Prepares the directory for file transfer.
	 *
	 * @return bool|void
	 */
	public function prep_transfer() {
		if ( ! is_dir( $this->temp_dir ) ) {
			return mkdir( $this->temp_dir );
		}
	}

	/**
	 * Renders the HTTP headers
	 *
	 * @param string $filename Path to file.
	 */
	public function render_headers( $filename ) {
		$type = 'json';

		if ( $this->ziparchive_available ) {
			$type = 'zip';
		}

		header( 'Content-Type: application/' . $type . '; charset=UTF-8' );
		header( 'Content-Disposition: attachment; filename=' . $filename . '.' . $type );
		header( 'Pragma: no-cache' );
		header( 'Expires: 0' );
	}
}

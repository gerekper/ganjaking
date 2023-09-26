<?php
/**
 * Per Shipping Product Importer class.
 *
 * @package WC_Shipping_Per_Product
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'WP_Importer' ) ) {

	/**
	 * Importer.
	 */
	class WC_Shipping_Per_Product_Importer extends WP_Importer {

		/**
		 * File ID or attachment ID.
		 *
		 * @var int
		 */
		public $id;

		/**
		 * Import page slug.
		 *
		 * @var string
		 */
		public $import_page;

		/**
		 * Delimiter.
		 *
		 * @var string
		 */
		public $delimiter;

		/**
		 * Posts.
		 *
		 * @todo This seems unused.
		 *
		 * @var array.
		 */
		public $posts = array();

		/**
		 * Number of successfully imported.
		 *
		 * @var int
		 */
		public $imported;

		/**
		 * Number of skipped.
		 *
		 * @var int
		 */
		public $skipped;

		/**
		 * __construct function.
		 *
		 * @access public
		 * @return void
		 */
		public function __construct() {
			$this->import_page = 'woocommerce_per_product_shipping_csv';
		}

		/**
		 * Registered callback function for the WordPress Importer.
		 *
		 * Manages the three separate stages of the CSV import process.
		 */
		function dispatch() {
			$this->header();

            if ( ! empty( $_POST['import-upload-form'] ) || ! empty( $_GET['step'] ) ) {
	            check_admin_referer( 'import-upload' );
            }

			if ( ! empty( $_POST['delimiter'] ) ) {
				$this->delimiter = sanitize_text_field( wp_unslash( $_POST['delimiter'] ) );
			}

			if ( ! $this->delimiter ) {
				$this->delimiter = ',';
			}

			$step = empty( $_GET['step'] ) ? 0 : (int) $_GET['step'];
			switch ( $step ) {
				case 0:
					$this->greet();
					break;
				case 1:
					if ( $this->handle_upload() ) {
						$file = get_attached_file( $this->id );

						add_filter( 'http_request_timeout', array( $this, 'bump_request_timeout' ) );

						if ( function_exists( 'gc_enable' ) ) {
							gc_enable();
						}

						@set_time_limit( 0 );
						@ob_flush();
						@flush();

						$this->import( $file );
					}
					break;
			}
			$this->footer();
		}

		/**
		 * Format data from csv.
		 *
		 * @param mixed  $data Data.
		 * @param string $enc  Encoding.
		 * @return mixed
		 */
		function format_data_from_csv( $data, $enc ) {
			return ( 'UTF-8' === $enc ) ? $data : utf8_encode( $data );
		}

		/**
		 * Import the given file.
		 *
		 * @param mixed $file File to upload.
		 * @return void
		 */
		function import( $file ) {
			global $wpdb;

			$this->imported      = 0;
			$this->skipped       = 0;
			$override_product_id = ! empty( $_GET['override_product_id'] ) ? absint( $_GET['override_product_id'] ) : false;

            // Check if product ID doesn't change.
            if ( $override_product_id ) {
	            check_admin_referer( 'override-product-id-' . absint( $_GET['override_product_id'] ), '_wpnonce_override-product-id' );
            }

			if ( ! is_file( $file ) ) {
				echo '<p><strong>' . esc_html__( 'Sorry, there has been an error.', 'woocommerce-shipping-per-product' ) . '</strong><br />';
				echo esc_html__( 'The file does not exist, please try again.', 'woocommerce-shipping-per-product' ) . '</p>';
				$this->footer();
				die();
			}

			$handle = fopen( $file, 'r' );
			if ( false !== $handle ) {
				$header = fgetcsv( $handle, 0, $this->delimiter );

				if ( sizeof( $header ) == 6 ) {

					$loop = 0;

					if ( $override_product_id ) {
						$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}woocommerce_per_product_shipping_rules WHERE product_id = %d;", $override_product_id ) );
					}

					while ( ( $row = fgetcsv( $handle, 0, $this->delimiter ) ) !== false ) {
						list( $post_id, $country, $state, $postcode, $cost, $item_cost ) = $row;

						// If $post_id is empty, skip the row
						if ( empty( $post_id ) ) {
							$this->skipped++;
							continue;
						}

						$country = trim( strtoupper( $country ) );
						$state   = trim( strtoupper( $state ) );

						if ( '*' === $country ) {
							$country = '';
						}
						if ( '*' === $state ) {
							$state = '';
						}
						if ( '*' === $postcode ) {
							$postcode = '';
						}

						$wpdb->insert(
							$wpdb->prefix . 'woocommerce_per_product_shipping_rules',
							array(
								'rule_country'   => esc_attr( $country ),
								'rule_state'     => esc_attr( $state ),
								'rule_postcode'  => esc_attr( $postcode ),
								'rule_cost'      => esc_attr( $cost ),
								'rule_item_cost' => esc_attr( $item_cost ),
								'rule_order'     => $loop,
								'product_id'     => absint( $post_id ),
							)
						);

						$loop++;
						$this->imported++;
					}
				} else {
					echo '<p><strong>' . esc_html__( 'Sorry, there has been an error.', 'woocommerce-shipping-per-product' ) . '</strong><br />';
					echo esc_html__( 'The CSV is invalid.', 'woocommerce-shipping-per-product' ) . '</p>';
					$this->footer();
					die();

				}

				fclose( $handle );
			}

			// Show Result.
			echo '<div class="updated settings-error below-h2"><p>';
			printf(
			// translators: 1) Total imported 2) Total skipped.
				esc_html__( 'Import complete - imported %1$s shipping rates and skipped %2$s.', 'woocommerce-shipping-per-product' ),
				'<strong>' . esc_html( $this->imported ) . '</strong>',
				'<strong>' . esc_html( $this->skipped ) . '</strong>'
			);
			echo '</p></div>';

			// Let the user know why rows were skipped.
			if ( $this->skipped > 0 ) {
				echo '<div class="error settings-error below-h2"><p>';
				printf(
				// translators: 1) Total skipped.
					esc_html__( '%1$s rows were missing a valid product ID', 'woocommerce-shipping-per-product' ),
					'<strong>' . esc_html( $this->skipped ) . '</strong>'
				);
				echo '</p></div>';
			}

			$this->import_end();
		}

		/**
		 * Performs post-import cleanup of files and the cache.
		 */
		public function import_end() {
			echo '<p>' . esc_html__( 'All done!', 'woocommerce-shipping-per-product' ) . '</p>';
			do_action( 'import_end' );
		}

		/**
		 * Handles the CSV upload and initial parsing of the file to prepare for
		 * displaying author import options.
		 *
		 * @return bool False if error uploading or invalid file, true otherwise.
		 */
		public function handle_upload() {
			$file = wp_import_handle_upload();

			if ( isset( $file['error'] ) || is_wp_error( $file['id'] ) ) {
				echo '<p><strong>' . esc_html__( 'Sorry, there has been an error.', 'woocommerce-shipping-per-product' ) . '</strong><br />';
				echo esc_html( $file['error'] ) . '</p>';

				return false;
			}

			$this->id = (int) $file['id'];

			return true;
		}

		/**
		 * Header HTML.
		 *
		 * @access public
		 * @return void
		 */
		public function header() {
			echo '<div class="wrap">';
			echo '<h2>' . esc_html__( 'Import Per-product Shipping Rates', 'woocommerce-shipping-per-product' ) . '</h2>';
		}

		/**
		 * Footer HTML.
		 *
		 * @access public
		 * @return void
		 */
		public function footer() {
			echo '</div>';
		}

		/**
		 * Greet handler.
		 *
		 * @access public
		 * @return void
		 */
		function greet() {
			echo '<div class="narrow">';
			echo '<p>' . esc_html__( 'Hi there! Upload a CSV file containing per-product shipping rates to import the contents into your shop. Choose a .csv file to upload, then click "Upload file and import".', 'woocommerce-shipping-per-product' ) . '</p>';

			echo '<p>' . esc_html__( 'Rates need to be defined with columns in a specific order (6 columns). Product ID, Country Code, State Code, Postcode, Cost, Item Cost', 'woocommerce-shipping-per-product' ) . '</p>';

			$action = 'admin.php?import=woocommerce_per_product_shipping_csv&step=1';

			if ( ! empty( $_GET['override_product_id'] ) && check_admin_referer( 'override-product-id-' . absint( $_GET['override_product_id'] ), '_wpnonce_override-product-id' )) {
                // Pass the nonce to check if product ID changed.
				$action .= '&override_product_id=' . absint( $_GET['override_product_id'] ) . '&_wpnonce_override-product-id=' . sanitize_text_field( $_GET['_wpnonce_override-product-id'] );
			}

			$bytes = apply_filters( 'import_upload_size_limit', wp_max_upload_size() );
			$size = size_format( $bytes );
			$upload_dir = wp_upload_dir();
			if ( ! empty( $upload_dir['error'] ) ) :
				?><div class="error"><p><?php esc_html_e( 'Before you can upload your import file, you will need to fix the following error:', 'woocommerce-shipping-per-product' ); ?></p>
				<p><strong><?php esc_html_e( $upload_dir['error'] ); ?></strong></p></div><?php
			else :
				?>
				<form enctype="multipart/form-data" id="import-upload-form" method="post" action="<?php echo esc_attr( wp_nonce_url( $action, 'import-upload' ) ); ?>">
					<table class="form-table">
						<tbody>
							<tr>
								<th>
									<label for="upload"><?php esc_html_e( 'Choose a file from your computer:', 'woocommerce-shipping-per-product' ); ?></label>
								</th>
								<td>
									<input type="file" id="upload" name="import" size="25" />
									<input type="hidden" name="action" value="save" />
									<input type="hidden" name="max_file_size" value="<?php echo esc_attr( $bytes ); ?>" />
									<small>
									<?php
									/* translators: Maximum size of file */
									printf( esc_html__( 'Maximum size: %s', 'woocommerce-shipping-per-product' ), esc_html( $size ) );
									?>
									</small>
								</td>
							</tr>
							<tr>
								<th><label><?php esc_html_e( 'Delimiter', 'woocommerce-shipping-per-product' ); ?></label><br/></th>
								<td><input type="text" name="delimiter" placeholder="," size="2" /></td>
							</tr>
						</tbody>
					</table>
					<p class="submit">
						<input type="submit" name="import-upload-form" class="button" value="<?php esc_attr_e( 'Upload file and import', 'woocommerce-shipping-per-product' ); ?>" />
					</p>
				</form>
				<?php
			endif;

			echo '</div>';
		}

		/**
		 * Added to http_request_timeout filter to force timeout at 60 seconds
		 * during import.
		 *
		 * @return int 60s
		 */
		public function bump_request_timeout( $val ) {
			return 60;
		}
	}
}

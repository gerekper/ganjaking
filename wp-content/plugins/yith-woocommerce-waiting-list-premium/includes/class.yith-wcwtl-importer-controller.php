<?php
/**
 * Class YITH_WCWTL_Importer Controller
 *
 * @package YITH WooCommerce Waiting List
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'YITH_WCWTL_Importer_Controller' ) ) {
	/**
	 * Waiting List Importer Controller - handles file upload and import forms in admin.
	 *
	 * @package     YITH WooCommerce Waiting List
	 * @version     1.6.0
	 */
	class YITH_WCWTL_Importer_Controller {

		/**
		 * The path to the current file.
		 * @var string
		 */
		protected $file = '';

		/**
		 * The current import step.
		 * @var string
		 */
		protected $current_step = '';

		/**
		 * The chosen product.
		 * @var integer
		 */
		protected $chosen_product = 0;

		/**
		 * The CSV delimiter
		 * @var string
		 */
		protected $delimiter = ',';

		/**
		 * The CSV enclosure
		 * @var string
		 */
		protected $enclosure = '"';

		/**
		 * If overwrite existing waiting list
		 * @var boolean
		 */
		protected $overwrite_existing = false;

		/**
		 * Mapped column index
		 * @var integer
		 */
		protected $column_map_index = 0;

		/**
		 * The steps.
		 * @var array
		 */
		protected $steps = array();

		/**
		 * An array of errors
		 * @since 1.6.0
		 * @var array
		 */
		protected $errors = array();

		/**
		 * Constructor.
		 *
		 * @since  1.6.0
		 * @author Francesco Licandro
		 */
		public function __construct() {

			// get importer class
			include( 'class.yith-wcwtl-importer.php' );

			// define steps
			$this->steps = array(
				'product' => array(
					'name' => __( 'Pick a product', ' yith-woocommerce-waiting-list' ),
					'view' => array( $this, 'choose_product_view' ),
				),
				'upload'  => array(
					'name' => __( 'Upload CSV file', ' yith-woocommerce-waiting-list' ),
					'view' => array( $this, 'upload_form_view' ),
				),
				'mapping' => array(
					'name' => __( 'Column mapping', ' yith-woocommerce-waiting-list' ),
					'view' => array( $this, 'mapping_form_view' ),
				),
				'import'  => array(
					'name' => __( 'Import done!', ' yith-woocommerce-waiting-list' ),
					'view' => array( $this, 'import_done_view' ),
				),
			);

			$this->current_step       = isset( $_REQUEST['step'] ) ? sanitize_key( $_REQUEST['step'] ) : 'product';
			$this->chosen_product     = isset( $_REQUEST['product'] ) ? absint( $_REQUEST['product'] ) : 0;
			$this->delimiter          = ! empty( $_REQUEST['delimiter'] ) ? wc_clean( wp_unslash( $_REQUEST['delimiter'] ) ) : ',';
			$this->enclosure          = ! empty( $_REQUEST['enclosure'] ) ? wc_clean( wp_unslash( $_REQUEST['enclosure'] ) ) : '"';
			$this->overwrite_existing = ! empty( $_REQUEST['overwrite_existing'] );
			$this->file               = isset( $_REQUEST['file'] ) ? wc_clean( wp_unslash( $_REQUEST['file'] ) ) : '';
			$this->column_map_index   = isset( $_REQUEST['column_map_index'] ) ? intval( $_REQUEST['column_map_index'] ) : 0;
			$this->errors             = get_option( 'yith_wcwtl_importer_errors', array() );

			$this->step_handler();
			// save errors
			add_action( 'shutdown', array( $this, 'save_errors' ) );
		}

		/**
		 * Get a step url
		 *
		 * @since  1.6.0
		 * @author Francesco Licandro
		 * @param string $step
		 * @return string
		 */
		protected function get_step_url( $step = '' ) {
			! $step && $step = $this->current_step;

			// build params
			$params = array( 'step' => $step );
			switch ( $step ) {
				case 'import':
				case 'product':
				case 'upload':
					$this->chosen_product && $params['product'] = $this->chosen_product;
					break;
				case 'mapping':
					$params['product'] = $this->chosen_product;
					$params['file']    = str_replace( DIRECTORY_SEPARATOR, '/', $this->file );
					( $this->delimiter !== ',' ) && $params['delimiter'] = $this->delimiter;
					( $this->enclosure !== '"' ) && $params['enclosure'] = $this->enclosure;
					$this->overwrite_existing && $params['overwrite_existing'] = 'yes';
					break;
			}

			return add_query_arg( $params, admin_url( 'admin.php?page=yith_wcwtl_panel&tab=waitlistimporter' ) );
		}

		/**
		 * Get next step url
		 *
		 * @since  1.6.0
		 * @author Francesco Licandro
		 * @return string
		 */
		protected function get_next_url() {
			$keys       = array_keys( $this->steps );
			$step_index = array_search( $this->current_step, $keys, true );
			return $this->get_step_url( isset( $keys[ $step_index + 1 ] ) ? $keys[ $step_index + 1 ] : $keys[0] );
		}

		/**
		 * Get next step url
		 *
		 * @since  1.6.0
		 * @author Francesco Licandro
		 * @return string
		 */
		protected function get_prev_url() {
			$keys       = array_keys( $this->steps );
			$step_index = array_search( $this->current_step, $keys, true );
			return $this->get_step_url( isset( $keys[ $step_index - 1 ] ) ? $keys[ $step_index - 1 ] : $keys[0] );
		}

		/**
		 * Add an error message
		 *
		 * @since  1.6.0
		 * @author Francesco Licandro
		 * @param string $error_msg
		 * @return void
		 */
		protected function add_error( $error_msg ) {
			$this->errors[] = esc_html( $error_msg );
		}

		/**
		 * Step action handler
		 *
		 * @since  1.6.0
		 * @author Francesco Licandro
		 * @return void
		 */
		protected function step_handler() {

			if ( empty( $_POST['__wpnonce'] ) || ! wp_verify_nonce( $_POST['__wpnonce'], 'yith-wcwtl-importer-action' ) ) {
				return;
			}

			try {
				switch ( $this->current_step ) {
					case 'product':
						$this->handle_product_step();
						break;
					case 'upload':
						$this->handle_upload_step();
						break;
					case 'mapping':
						$this->handle_mapping_step();
						// cause this is the last step if error is empty try to start import
						$this->import();
						break;
				}
			} catch ( Exception $e ) {
				$this->add_error( $e->getMessage() );
				return;
			}

			if ( empty( $this->errors ) ) {
				wp_safe_redirect( $this->get_next_url() );
				exit;
			}
		}

		/**
		 * Choose product view handler
		 *
		 * @since  1.6.0
		 * @author Francesco Licandro
		 * @return void
		 * @throws Exception
		 */
		protected function handle_product_step() {
			if ( empty( $_POST['product_id'] ) || ! absint( $_POST['product_id'] ) ) {
				throw new Exception( __( 'Please, pick a product', 'yith-woocommerce-waiting-list' ) );
			}

			$this->chosen_product = absint( $_POST['product_id'] );
		}

		/**
		 * Upload form handler
		 *
		 * @since  1.6.0
		 * @author Francesco Licandro
		 * @return void
		 * @throws Exception
		 */
		protected function handle_upload_step() {
			if ( ! isset( $_FILES['import'] ) ) {
				throw new Exception( __( 'File is empty. This error could also be caused by uploads being disabled in your "php.ini" or by "post_max_size" being defined as smaller than upload_max_filesize in php.ini.', 'yith-woocommerce-waiting-list' ) );
			}

			if ( ! YITH_WCWTL_Importer::is_file_valid_csv( wc_clean( wp_unslash( $_FILES['import']['name'] ) ) ) ) {
				throw new Exception( __( 'Invalid file type. The importer supports only CSV format.', 'yith-woocommerce-waiting-list' ) );
			}

			$import = $_FILES['import']; // WPCS: sanitization ok, input var ok.
			$upload = wp_handle_upload( $import, array(
				'test_form' => false,
				'mimes'     => YITH_WCWTL_Importer::get_valid_filetypes(),
			) );

			if ( isset( $upload['error'] ) ) {
				throw new Exception( $upload['error'] );
			}

			$this->file = $upload['file'];
		}

		/**
		 * Mapping form handler
		 *
		 * @since  1.6.0
		 * @author Francesco Licandro
		 * @return void
		 * @throws Exception
		 */
		protected function handle_mapping_step() {
			if ( $this->column_map_index == -1 ) {
				throw new Exception( __( 'Please choose a column to use as user email', 'yith-woocommerce-waiting-list' ) );
			}
		}

		/**
		 * Show correct importer view.
		 *
		 * @since  1.6.0
		 * @author Francesco Licandro
		 * @return void
		 */
		public function output() {
			?>
			<div class="yith-wcwtl-importer-wrap">
				<h1><?php esc_html_e( 'Import Waiting List', ' yith-woocommerce-waiting-list' ); ?></h1>
				<div class="progress-form-wrapper">
					<?php
					$this->output_steps();
					$this->output_errors();
					?>
					<div class="yith-wcwtl-wrap-current-step">
						<?php call_user_func( $this->steps[ $this->current_step ]['view'], $this ); ?>
					</div>
				</div>
			</div>
			<?php
		}

		/**
		 * Output steps view.
		 *
		 * @since  1.6.0
		 * @author Francesco Licandro
		 * @return void
		 */
		protected function output_steps() {
			include YITH_WCWTL_TEMPLATE_PATH . '/admin/importer/import-steps.php';
		}

		/**
		 * Add error message.
		 *
		 * @since  1.6.0
		 * @author Francesco Licandro
		 * @return void
		 */
		protected function output_errors() {
			if ( ! $this->errors ) {
				return;
			}
			// be sure only one error per message
			$this->errors = array_unique( $this->errors );
			foreach ( $this->errors as $error ) {
				echo '<div class="error inline">';
				echo '<p>' . esc_html( $error ) . '</p>';
				echo '</div>';
			}

			$this->errors = array();
		}

		/**
		 * Choose product view
		 *
		 * @since  1.6.0
		 * @author Francesco Licandro
		 * @return void
		 */
		protected function choose_product_view() {
			include YITH_WCWTL_TEMPLATE_PATH . '/admin/importer/import-product.php';
		}

		/**
		 * Upload form view
		 *
		 * @since  1.6.0
		 * @author Francesco Licandro
		 * @return void
		 */
		protected function upload_form_view() {
			$bytes      = apply_filters( 'import_upload_size_limit', wp_max_upload_size() );
			$size       = size_format( $bytes );
			$upload_dir = wp_upload_dir();

			include YITH_WCWTL_TEMPLATE_PATH . '/admin/importer/import-upload.php';
		}

		/**
		 * Mapping step view
		 *
		 * @since  1.6.0
		 * @author Francesco Licandro
		 * @return void
		 */
		protected function mapping_form_view() {

			$importer = new YITH_WCWTL_Importer( $this->file, array(
				'lines'     => 1,
				'delimiter' => $this->delimiter,
				'enclosure' => $this->enclosure,
			) );

			$importer->read_file();
			$headers = $importer->get_raw_keys();
			$sample  = $importer->get_raw_data();

			if ( empty( $sample ) ) {
				$this->add_error( __( 'The file is empty or using a different encoding than UTF-8, please try again with a new file.', 'yith-woocommerce-waiting-list' ) );
				wp_safe_redirect( $this->get_prev_url() );
				exit;
			}

			include YITH_WCWTL_TEMPLATE_PATH . '/admin/importer/import-mapping.php';
		}

		/**
		 * Import done view
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro
		 * @return void
		 */
		protected function import_done_view() {
			$product = wc_get_product( $this->chosen_product );
			include YITH_WCWTL_TEMPLATE_PATH . '/admin/importer/import-done.php';
		}

		/**
		 * Import step view
		 *
		 * @since  1.6.0
		 * @author Francesco Licandro
		 * @return void
		 */
		protected function import() {

			$product = wc_get_product( $this->chosen_product );
			if ( $product ) {
				$importer = new YITH_WCWTL_Importer( $this->file, array(
					'delimiter'          => $this->delimiter,
					'enclosure'          => $this->enclosure,
					'map_column'         => $this->column_map_index,
					'product_id'         => $this->chosen_product,
					'overwrite_existing' => $this->overwrite_existing,
				) );

				if ( ! $importer->run() ) {
					$this->add_error( __( 'An error occurred during the import process. Please try again.', 'yith-woocommerce-waiting-list' ) );
					wp_safe_redirect( $this->get_step_url( 'product' ) );
					exit;
				}

			} else {
				$this->add_error( sprintf( __( 'No product with ID #%s found. Please choose a different product.', 'yith-woocommerce-waiting-list' ), $this->chosen_product ) );
				$this->file && unlink( $this->file );

				wp_safe_redirect( $this->get_step_url( 'product' ) );
				exit;
			}
		}

		/**
		 * Save unprinted errors
		 *
		 * @since  1.6.0
		 * @author Francesco Licandro
		 * @return void
		 */
		public function save_errors() {
			if ( ! empty( $this->errors ) ) {
				update_option( 'yith_wcwtl_importer_errors', $this->errors );
			} else {
				delete_option( 'yith_wcwtl_importer_errors' );
			}
		}
	}
}

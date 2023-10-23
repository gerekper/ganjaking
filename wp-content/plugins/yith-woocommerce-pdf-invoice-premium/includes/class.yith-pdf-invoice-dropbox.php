<?php // phpcs:ignore WordPress.Files.FileName.NotHyphenatedLowercase, WordPress.Files.FileName.InvalidClassFileName
/**
 * YITH PDF Invoice Dropbox class.
 *
 * Handles the invoice details.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\PDFInvoice\Classes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'YITH_PDF_Invoice_DropBox' ) ) {
	/**
	 * Class that handle the Dropbox feature.
	 *
	 * @class   YITH_PDF_Invoice_DropBox
	 * @package YITH\PDFInvoice\Classes
	 * @since   1.5.4
	 */
	class YITH_PDF_Invoice_DropBox {

		/**
		 * Dropbox access token.
		 *
		 * @var string $dropbox_accesstoken
		 */
		public $dropbox_accesstoken = '';

		/**
		 * Dropbox redirect uri.
		 *
		 * @var string $dropbox_redurect_uri
		 */
		public $dropbox_redurect_uri = 'https://update.yithemes.com/dropbox-apps/authorize-new.php?app=pdf-invoice';

		/**
		 * Dropbox app key.
		 *
		 * @var string $dropbox_app_key
		 */
		public $dropbox_app_key = '58dmyrhs688d3zs';

		/**
		 * Single instance of the class
		 *
		 * @var object
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Construct
		 */
		private function __construct() {
			if ( isset( $_POST['ywpi_dropbox_key'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
				$this->dropbox_accesstoken = sanitize_text_field( wp_unslash( $_POST['ywpi_dropbox_key'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
			} else {
				$this->dropbox_accesstoken = ywpi_get_option( 'ywpi_dropbox_access_token' );
			}
		}

		/**
		 * Save DropBox access token
		 */
		public function custom_save_ywpi_dropbox() {
			if ( isset( $_POST['ywpi_dropbox_key'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
				update_option( 'ywpi_dropbox_access_token', sanitize_text_field( wp_unslash( $_POST['ywpi_dropbox_key'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
			}
		}

		/**
		 * Disable the DropBox backup
		 */

		/*
		public function disable_dropbox_backup() {
			if ( $this->dropbox_accesstoken ) {
				try {
					delete_option( 'ywpi_dropbox_access_token' );

					$dbx_client = new Dropbox\Client( $this->dropbox_accesstoken, 'PHP-Example/1.0' );

					// try to retrieve information to verify if access token is valid.
					return $dbx_client->disableAccessToken();

				} catch ( \Dropbox\Exception $e ) {
					error_log( __( 'Dropbox backup: unable to disable authorization > ', 'yith-woocommerce-pdf-invoice' ) . $e->getMessage() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
				}
			}
		}
		*/

		/**
		 * Retrieve access token starting from an authorization code
		 */
		public function get_dropbox_access_token() {
			return $this->dropbox_accesstoken;
		}

		/**
		 * Upload document to dropbox, if access token is valid
		 *
		 * @param object $document The document object.
		 */
		public function send_document_to_dropbox( $document ) {
			if ( ! $this->dropbox_accesstoken ) {
				error_log( esc_html__( 'Error: no Access Token.', 'yith-woocommerce-pdf-invoice' ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
				return;
			}

			$dropbox_accesstoken = $this->dropbox_accesstoken;

			if ( file_exists( $document->get_full_path() ) ) {
				/**
				 * APPLY_FILTERS: ywpi_dropbox_folder
				 *
				 * Filter the DropBox folder to save the documents.
				 *
				 * @param string folder name.
				 * @param object $document the document object.
				 *
				 * @return string
				 */
				$doc_full_path = $document->get_full_path();
				$doc_folder    = apply_filters( 'ywpi_dropbox_folder', ywpi_get_option( 'ywpi_dropbox_folder' ), $document );
				$doc_path      = $document->save_path;
				$file          = file_get_contents( $doc_full_path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
				$date_val      = strtotime( $document->order->get_meta( 'order_date' ) );
				$date          = getdate( $date_val );

				$doc_folder = str_replace(
					array(
						'[year]',
						'[month]',
						'[day]',
					),
					array(
						$date['year'],
						sprintf( '%02d', $date['mon'] ),
						sprintf( '%02d', $date['mday'] ),
					),
					$doc_folder
				);

				$ch = curl_init(); // phpcs:ignore WordPress.WP.AlternativeFunctions.curl_curl_init

				// phpcs:disable WordPress.WP.AlternativeFunctions.curl_curl_setopt
				curl_setopt( $ch, CURLOPT_URL, 'https://content.dropboxapi.com/2/files/upload' );
				curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
				curl_setopt( $ch, CURLOPT_POSTFIELDS, $file );
				curl_setopt( $ch, CURLOPT_POST, 1 );

				$headers   = array();
				$headers[] = "Authorization: Bearer $dropbox_accesstoken";
				$headers[] = "Dropbox-Api-Arg: {\"path\": \"/$doc_folder/$doc_path\",\"mode\": \"overwrite\",\"autorename\": true,\"mute\": false}";
				$headers[] = 'Content-Type: application/octet-stream';
				curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );

				// phpcs:enable WordPress.WP.AlternativeFunctions.curl_curl_setopt

				$result = curl_exec( $ch ); // phpcs:ignore WordPress.WP.AlternativeFunctions.curl_curl_exec

				if ( curl_errno( $ch ) ) { // phpcs:ignore WordPress.WP.AlternativeFunctions.curl_curl_errno
					error_log( esc_html__( 'Error: unable to send file to Dropbox.', 'yith-woocommerce-pdf-invoice' ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
					error_log( 'Error:' . curl_error( $ch ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.curl_curl_error, WordPress.PHP.DevelopmentFunctions.error_log_error_log
				}

				curl_close( $ch ); // phpcs:ignore WordPress.WP.AlternativeFunctions.curl_curl_close
			}
		}
	}
}

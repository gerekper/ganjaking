<?php

if ( ! defined ( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


if ( ! class_exists( 'YITH_PDF_Invoice_DropBox' ) ) {

	/**
	 * @class   YITH_PDF_Invoice_DropBox
	 *
	 * @since   1.5.4
	 * @author  Corrado Porzio
	 */
	class YITH_PDF_Invoice_DropBox {

		/**
		 * Dropbox info
		 */
		public $dropbox_accesstoken		= '';
		public $dropbox_redurect_uri	= 'https://update.yithemes.com/dropbox-apps/authorize-new.php?app=pdf-invoice';
		public $dropbox_app_key 		= '58dmyrhs688d3zs';

		/**
		 * Single instance of the class
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

			if ( isset( $_POST['ywpi_dropbox_key'] ) ) {
				$this->dropbox_accesstoken = $_POST['ywpi_dropbox_key'];
			} else {
				$this->dropbox_accesstoken = ywpi_get_option( 'ywpi_dropbox_access_token' );
			}

		}

		/**
		 * Save DropBox access token
		 */
		public function custom_save_ywpi_dropbox() {

			if ( isset( $_POST['ywpi_dropbox_key'] ) ) {
				update_option( 'ywpi_dropbox_access_token', $_POST['ywpi_dropbox_key'] );
			}

		}

		/**
		 * Disable the DropBox backup
		 */
		public function disable_dropbox_backup() {

			/*
			if ( $this->dropbox_accesstoken ) {
				try {
					delete_option( 'ywpi_dropbox_access_token' );

					$dbxClient = new Dropbox\Client( $this->dropbox_accesstoken, "PHP-Example/1.0" );

					//  try to retrieve information to verify if access token is valid
					return $dbxClient->disableAccessToken();

				} catch ( \Dropbox\Exception $e ) {
					error_log( __( 'Dropbox backup: unable to disable authorization > ', 'yith-woocommerce-pdf-invoice' ) . $e->getMessage() );
				}
			}
			*/

		}

		/**
		 * Retrieve access token starting from an authorization code
		 */
		public function get_dropbox_access_token( $auth_code = '' ) {
			
			return $this->dropbox_accesstoken;

		}

		/**
		 * Upload document to dropbox, if access token is valid
		 */
		public function send_document_to_dropbox( $document ) {

			if ( ! $this->dropbox_accesstoken ) {
				error_log( esc_html__( 'Error: no Access Token.', 'yith-woocommerce-pdf-invoice' ) );
				return;
			}

			$dropbox_accesstoken = $this->dropbox_accesstoken;

			if ( file_exists( $document->get_full_path() ) ) {

				$doc_full_path	= $document->get_full_path();
				$doc_folder		= apply_filters( 'ywpi_dropbox_folder', ywpi_get_option( 'ywpi_dropbox_folder' ), $document );
				$doc_path		= $document->save_path;
				$file 			= file_get_contents( $doc_full_path );
                $date_val       = strtotime( yit_get_prop( $document->order, 'order_date' ) );
                $date           = getdate( $date_val );

                $doc_folder = str_replace(
                    array(
                        '[year]',
                        '[month]',
                        '[day]',
                    ),
                    array(
                        $date['year'],
                        sprintf( "%02d", $date['mon'] ),
                        sprintf( "%02d", $date['mday'] ),
                    ),
                    $doc_folder );
                
				$ch = curl_init();

				curl_setopt( $ch, CURLOPT_URL, "https://content.dropboxapi.com/2/files/upload");
				curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt( $ch, CURLOPT_POSTFIELDS, $file );
				curl_setopt( $ch, CURLOPT_POST, 1 );

				$headers = array();
				$headers[] = "Authorization: Bearer $dropbox_accesstoken";
				$headers[] = "Dropbox-Api-Arg: {\"path\": \"/$doc_folder/$doc_path\",\"mode\": \"overwrite\",\"autorename\": true,\"mute\": false}";
				$headers[] = "Content-Type: application/octet-stream";
				curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );

				$result = curl_exec( $ch );

				if ( curl_errno( $ch ) ) {
					error_log( esc_html__( 'Error: unable to send file to Dropbox.', 'yith-woocommerce-pdf-invoice' ) );
					error_log( 'Error:' . curl_error( $ch ) );
				}

				curl_close ($ch);

			}

		}

	}

}
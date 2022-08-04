<?php // phpcs:ignore WordPress.NamingConventions.

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'YITH_PDF_Invoice_Google_Drive' ) ) {

	/**
	 * The Google Drive Class
	 *
	 * This Class manage the Google Drive object of the YITH PDF invoice plugin.
	 *
	 * @class   YITH_PDF_Invoice_Google_Drive
	 * @package YITH\PDF_Invoice\Classes
	 * @author  YITH
	 */
	class YITH_PDF_Invoice_Google_Drive {

		/**
		 * Single instance of the class
		 *
		 * @var object
		 */
		protected static $instance;

		/**
		 * The client object.
		 *
		 * @var object
		 */
		public $client = '';
		/**
		 * The redirect uri link for oAuth method.
		 *
		 * @var string
		 */
		public $redirect_uri = '';
		/**
		 * The scope link for oAuth method.
		 *
		 * @var string
		 */
		public $scope = '';
		/**
		 * The client id for oAuth method.
		 *
		 * @var string
		 */
		public $client_id = '';
		/**
		 * The client password for oAuth method.
		 *
		 * @var string
		 */
		public $client_password = '';
		/**
		 * The authorization code for oAuth method.
		 *
		 * @var string
		 */
		public $authorization_code = '';

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
			require_once YITH_YWPI_DIR . 'lib/vendor/autoload.php';

			$redirect_uri          = admin_url( 'admin.php' . yith_ywpi_get_panel_url( 'documents_storage' ) );
			$drive_client_id       = get_option( 'ywpi_google_drive_client_id', '' );
			$drive_client_password = get_option( 'ywpi_google_drive_client_password', '' );
			$auth_code             = get_option( 'ywpi_authorization_code', '' );

			$this->redirect_uri       = $redirect_uri;
			$this->client_id          = ! empty( $drive_client_id ) ? $drive_client_id : '';
			$this->client_password    = ! empty( $drive_client_password ) ? $drive_client_password : '';
			$this->authorization_code = ! empty( $auth_code ) ? $auth_code : '';
			$this->scope              = 'https://www.googleapis.com/auth/drive';
			$this->client             = $this->get_client();

			add_action( 'admin_init', array( $this, 'set_auth_code' ) );

		}


		/**
		 * Returns an authorized API client.
		 *
		 * @return Google_Client the authorized client object
		 */
		public function get_client() {

			$client = new Google_Client();
			$client->addScope( $this->scope );
			$client->setClientId( $this->client_id );
			$client->setClientSecret( $this->client_password );
			$client->setRedirectUri( $this->redirect_uri );
			$client->setAccessType( 'offline' );
			$client->setPrompt( 'consent' );
			$client->setApprovalPrompt( 'consent' );

			return $client;
		}

		/**
		 * Create and apply access token to the client from authorization code response.
		 *
		 * @throws \Exception Exception in case the Client::setAccessToken fails.
		 */
		public function set_auth_code() {
			if ( isset( $_GET['code'] ) ) { //phpcs:ignore
				$client = $this->get_client();

				$access_token = $client->fetchAccessTokenWithAuthCode( $_GET['code'] ); //phpcs:ignore
				if ( array_key_exists( 'error', $access_token ) ) {
					// For invalid_grant error, remove permissions from Google. -> https://myaccount.google.com/permissions.
					throw new Exception( join( ', ', $access_token ) );
				}
				update_option( 'ywpi_authorization_code', $_GET['code'] ); //phpcs:ignore
				update_option( 'ywpi_access_token_info', $access_token );

			}
		}

		/**
		 * Set the access token to the client object.
		 *
		 * @param object $client The client object.
		 *
		 * @return object
		 * @throws \Exception Exception in case the Client::setAccessToken fails.
		 */
		public function set_token( $client ) {

			$access_token      = array();
			$ywpi_access_token = ywpi_get_option( 'ywpi_access_token_info' );

			if ( ! empty( $ywpi_access_token ) ) {
				$access_token = $ywpi_access_token;
				$client->setAccessToken( $access_token );
			}

			// If there is no previous token or it's expired.
			if ( $client->isAccessTokenExpired() ) {
				// Refresh the token if possible, else fetch a new one.
				if ( $client->getRefreshToken() ) {
					$access_token = $client->fetchAccessTokenWithRefreshToken( $client->getRefreshToken() );
				} elseif ( ! empty( $this->authorization_code ) ) {
					$access_token = $client->fetchAccessTokenWithAuthCode( trim( $this->authorization_code ) );
					$client->setAccessToken( $access_token );
				}
				if ( array_key_exists( 'error', $access_token ) ) {
					// For invalid_grant error, remove permissions from Google. -> https://myaccount.google.com/permissions.
					throw new Exception( join( ', ', $access_token ) );
				}

				update_option( 'ywpi_access_token_info', $access_token );

			}

			return $client;
		}

		/**
		 * Upload a file to Google Drive.
		 *
		 * @param object $document The document object.
		 * @param string $document_type The document type.
		 * @param string $extension The extension of the document.
		 * @return void
		 */
		public function upload_file( $document, $document_type = '', $extension = '' ) {

			$this->set_token( $this->client );

			try {
				$gd_current_file = '';
				$mymetype        = ( 'xml' === $extension ) ? 'text/xml' : 'application/pdf';
				$document_path   = $document->get_full_path();
				$file_id         = $document->order->get_meta( '_ywpi_gd_file_' . $document_type . '_' . $extension . '_id' );
				$service         = new Google\Service\Drive( $this->client );
				$gd_file         = new Google\Service\Drive\DriveFile();

				$folder_arr        = $this->get_folder( $service );
				$folder            = isset( $folder_arr['folder'] ) ? $folder_arr['folder'] : '';
				$folder_is_trashed = isset( $folder_arr['is_trashed'] ) ? $folder_arr['is_trashed'] : '';
				$folder_id         = isset( $folder->id ) ? $folder->id : '';
				$folder_exists     = $this->check_if_folder_exists( $service, $folder_id );

				try {
					if ( ! empty( $file_id ) ) {
						$gd_current_file = $service->files->get(
							$file_id,
							array(
								'mimeType' => $mymetype,
								'fields'   => 'id, name',
							)
						);
					}
				} catch ( Exception $e ) {
					print esc_html( $e->getMessage() );
				}

				$file_document = ywpi_get_order_document_by_type( yit_get_prop( $document->order, 'id' ), $document_type );
				$file_name     = YITH_WooCommerce_Pdf_Invoice_Premium::get_instance()->get_document_filename( $file_document, $extension );
				if ( $folder_exists && ( empty( $file_id ) || empty( $gd_current_file->id ) || $folder_is_trashed ) ) {
					// Upload new file.
					$gd_file->setName( ( ! empty( $file_name ) ) ? $file_name : $document_type . '-' . $document->order->get_id() );
					$gd_file->setMimeType( $mymetype );
					$gd_file->setParents( array( $folder->id ) );

					$result = $service->files->create(
						$gd_file,
						array(
							'data'     => file_get_contents( $document_path ), //phpcs:ignore
							'mimeType' => $mymetype,
						)
					);

					if ( ! empty( $file_id ) ) {
						$document->order->update_meta_data( '_ywpi_gd_file_' . $document_type . '_' . $extension . '_id', $result['id'] );
					} else {
						$document->order->add_meta_data( '_ywpi_gd_file_' . $document_type . '_' . $extension . '_id', $result['id'] );
					}
					$document->order->save();

				} else {
					// Upload existing file.
					$service->files->update(
						$file_id,
						$gd_file,
						array(
							'data'     => file_get_contents( $document_path ), //phpcs:ignore
							'mimeType' => $mymetype,
						)
					);

				}
			} catch ( Exception $e ) {
				print esc_html( $e->getMessage() );
			}
		}

		/**
		 * Get the correct folder in the Google Drive space.
		 *
		 * @param $object $service The service object to do new requests.
		 * @return array $object_array
		 */
		public function get_folder( $service ) {
			try {
				$folder_arr        = array(
					'folder'     => '',
					'is_trashed' => false,
				);
				$drive_folder_name = ywpi_get_option( 'ywpi_google_drive_folder' );
				$drive_folder_name = ! empty( $drive_folder_name ) ? $drive_folder_name : __( 'Invoices', 'yith-woocommerce-pdf-invoice' );

				$folder_id     = ywpi_get_option( 'ywpi_gd_folder_id' );
				$folder_exists = $this->check_if_folder_exists( $service, $folder_id );

				if ( $folder_exists ) {
					// Get current folder.
					$folder = $service->files->get(
						$folder_id,
						array(
							'mimeType' => 'application/vnd.google-apps.folder',
							'fields'   => 'id, trashed, name',
						)
					);

					if ( $folder->trashed ) {
						$folder_arr['is_trashed'] = true;
					}
				} else {
					// Create folder.
					$folder_file = new Google_Service_Drive_DriveFile(
						array(
							'name'     => $drive_folder_name,
							'mimeType' => 'application/vnd.google-apps.folder',
						)
					);
					$folder      = $service->files->create(
						$folder_file
					);
					update_option( 'ywpi_gd_folder_id', $folder->id );
				}

				$folder_arr['folder'] = $folder;

				return $folder_arr;

			} catch ( Exception $e ) {
				print esc_html( $e->getMessage() );
			}
		}

		/**
		 * Get the folder ID if it exists, if it doesnt exist, create it and return the ID
		 *
		 * @param Google_DriveService $service Drive API service instance.
		 * @param string              $folder_id The folder id saved in database.
		 * @return boolean that was created or got.
		 */
		private function check_if_folder_exists( $service, $folder_id ) {
			// List all user files (and folders) at Drive root.
			$found = false;

			$params = array(
				'q' => "'root' in parents AND mimeType='application/vnd.google-apps.folder'", // in root folder and only folders.
			);
			$files  = $service->files->listFiles( $params );

			// Go through each one to see if there is already a folder with the specified name.
			foreach ( $files['files'] as $item ) {
				if ( $item['id'] === $folder_id ) {
					$found = true;
					break;
				}
			}

			return $found;
		}
	}
}

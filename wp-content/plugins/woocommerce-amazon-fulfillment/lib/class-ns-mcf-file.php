<?php
/**
 * File utility class for file helper functions and re-usuable code.
 *
 * @package NeverSettle\WooCommerce-Amazon-Fulfillment
 * @since 4.1.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'NS_MCF_File' ) ) {

	/**
	 * File utility class.
	 */
	class NS_MCF_File {

		/**
		 * The directory permission
		 *
		 * @var int
		 */
		public $chmod_dir = 0755;

		/**
		 * The file permission
		 *
		 * @var int
		 */
		public $chmod_file = 0644;

		/**
		 * Check if current action can be performed
		 *
		 * @var bool
		 */
		private $has_permission = false;

		/**
		 * Main constructor.
		 * Set up the file mod and check permissions.
		 */
		public function __construct() {
			$this->chmod_dir  = defined( 'FS_CHMOD_DIR' ) ? FS_CHMOD_DIR : ( fileperms( ABSPATH ) & 0777 | 0755 );
			$this->chmod_file = defined( 'FS_CHMOD_FILE' ) ? FS_CHMOD_FILE : ( fileperms( ABSPATH . 'index.php' ) & 0777 | 0644 );

			$this->check_permission();
		}

		/**
		 * Create a file
		 *
		 * @param string $file The full file path.
		 * @param string $file_content The file contents.
		 *
		 * @return bool
		 */
		public function create_file( $file, $file_content ) {
			if ( $this->has_permission ) {
				global $wp_filesystem;
				if ( ! $wp_filesystem->exists( $file ) ) {
					return $wp_filesystem->put_contents( $file, $file_content, $this->chmod_file );
				}
			}
			return false;
		}

		/**
		 * Read a file
		 *
		 * @param string $file The file.
		 *
		 * @return string
		 */
		public function read_file( $file ) {
			if ( $this->has_permission ) {
				global $wp_filesystem;
				return $wp_filesystem->get_contents( $file );
			}
			return '';
		}

		/**
		 * Create directory
		 *
		 * @param string $directory The full directory path.
		 *
		 * @return bool
		 */
		public function create_directory( $directory ) {
			if ( $this->has_permission ) {
				global $wp_filesystem;
				if ( ! $wp_filesystem->exists( $directory ) ) {
					return $wp_filesystem->mkdir( $directory, $this->chmod_dir );
				}
			}
			return false;
		}

		/**
		 * Check if a file or directory exists.
		 *
		 * @param string $file_or_dir The full file or directory path.
		 *
		 * @return bool
		 */
		public function exists( $file_or_dir ) {
			if ( $this->has_permission ) {
				global $wp_filesystem;
				return $wp_filesystem->exists( $file_or_dir );
			}
			return false;
		}

		/**
		 * Check if a directory is writable.
		 *
		 * @param string $directory The full directory path.
		 *
		 * @return bool
		 */
		public function is_writable( $directory ) {
			if ( $this->has_permission ) {
				global $wp_filesystem;
				return $wp_filesystem->is_writable( $directory );
			}
			return false;
		}

		/**
		 * Delete a file.
		 *
		 * @param string $path The full file path.
		 *
		 * @return bool
		 */
		public function delete( $path ) {
			if ( $this->has_permission ) {
				global $wp_filesystem;
				return $wp_filesystem->delete( $path );
			}
			return false;
		}

		/**
		 * Check and set permissions.
		 * This checks that the file system is ready to be written
		 */
		private function check_permission() {
			$creds                = $this->get_creds();
			$this->has_permission = true;
			if ( empty( $creds ) || ! WP_Filesystem( $creds ) ) {
				$this->has_permission = false;
			}
		}


		/**
		 * Get File access credentials.
		 *
		 * @return array
		 */
		private function get_creds() {
			if ( ! function_exists( 'get_filesystem_method' ) ) {
				include_once ABSPATH . 'wp-admin/includes/file.php';
			}

			$access_type = get_filesystem_method();
			if ( 'direct' === $access_type ) {
				$creds = request_filesystem_credentials( site_url() . '/wp-admin/', '', false, false, array() );
			} else {
				$creds = $this->get_ftp_creds( $access_type );
			}

			return $creds;
		}

		/**
		 * Check for FTP credentials.
		 *
		 * @param string $type The access type.
		 *
		 * @return array|bool
		 */
		private function get_ftp_creds( $type ) {
			$credentials = get_option(
				'ftp_credentials',
				array(
					'hostname' => '',
					'username' => '',
				)
			);

			$credentials['hostname'] = defined( 'FTP_HOST' ) ? FTP_HOST : $credentials['hostname'];
			$credentials['username'] = defined( 'FTP_USER' ) ? FTP_USER : $credentials['username'];
			$credentials['password'] = defined( 'FTP_PASS' ) ? FTP_PASS : '';

			// Check to see if we are setting the public/private keys for ssh.
			$credentials['public_key']  = defined( 'FTP_PUBKEY' ) ? FTP_PUBKEY : '';
			$credentials['private_key'] = defined( 'FTP_PRIKEY' ) ? FTP_PRIKEY : '';

			// Sanitize the hostname, Some people might pass in odd-data.
			$credentials['hostname'] = preg_replace( '|\w+://|', '', $credentials['hostname'] ); // Strip any schemes off.

			if ( strpos( $credentials['hostname'], ':' ) ) {
				list( $credentials['hostname'], $credentials['port'] ) = explode( ':', $credentials['hostname'], 2 );
				if ( ! is_numeric( $credentials['port'] ) ) {
					unset( $credentials['port'] );
				}
			} else {
				unset( $credentials['port'] );
			}

			if ( ( defined( 'FTP_SSH' ) && FTP_SSH ) || ( defined( 'FS_METHOD' ) && 'ssh2' === FS_METHOD ) ) {
				$credentials['connection_type'] = 'ssh';
			} elseif ( ( defined( 'FTP_SSL' ) && FTP_SSL ) && 'ftpext' === $type ) {
				// Only the FTP Extension understands SSL.
				$credentials['connection_type'] = 'ftps';
			} elseif ( ! isset( $credentials['connection_type'] ) ) {
				// All else fails (And it's not defaulted to something else saved), Default to FTP.
				$credentials['connection_type'] = 'ftp';
			}

			$has_creds = ( ! empty( $credentials['password'] ) && ! empty( $credentials['username'] ) && ! empty( $credentials['hostname'] ) );
			$can_ssh   = ( 'ssh' === $credentials['connection_type'] && ! empty( $credentials['public_key'] ) && ! empty( $credentials['private_key'] ) );
			if ( $has_creds || $can_ssh ) {
				$stored_credentials = $credentials;
				if ( ! empty( $stored_credentials['port'] ) ) {
					// save port as part of hostname to simplify above code.
					$stored_credentials['hostname'] .= ':' . $stored_credentials['port'];
				}

				unset( $stored_credentials['password'], $stored_credentials['port'], $stored_credentials['private_key'], $stored_credentials['public_key'] );

				return $credentials;
			}

			return false;
		}
	}
}

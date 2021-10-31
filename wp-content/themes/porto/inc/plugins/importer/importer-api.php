<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Porto_Importer_API {

	protected $demo = '';
	protected $code = '';

	protected $path_tmp  = '';
	protected $path_demo = '';

	protected $url = array(
		'changelog'        => PORTO_API_URL . 'download/changelog.php',
		'theme_version'    => PORTO_API_URL . 'download/theme_version.php',
		'theme'            => PORTO_API_URL . 'download/theme.php',
		'plugins_version'  => PORTO_API_URL . 'download/plugins_version.php',
		'plugins'          => PORTO_API_URL . 'download/plugins.php',
		'demos'            => PORTO_API_URL . 'download/demos.php',
		'blocks'           => PORTO_API_URL . 'download/blocks.php',
		'block_categories' => PORTO_API_URL . 'download/block_categories.php',
		'blocks_content'   => PORTO_API_URL . 'download/block_content.php',
	);

	public function __construct( $demo = false ) {
		if ( $demo ) {
			$this->demo     = $demo;
			$upload_dir     = wp_upload_dir();
			$this->path_tmp = wp_normalize_path( $upload_dir['basedir'] . '/porto_tmp_dir' );
			$this->makedir();
		}
		if ( function_exists( 'Porto' ) ) {
			$this->code = Porto()->get_purchase_code();
		} else {
			$this->code = get_option( 'envato_purchase_code_9207399' );
		}

		add_filter( 'http_request_args', array( $this, 'http_request_args' ), 10, 2 );
	}

	public function get_url( $id ) {
		return isset( $this->url[ $id ] ) ? $this->url[ $id ] : false;
	}

	public function http_request_args( $parsed_args = [], $url = '' ) {
		if ( false === strpos( $url, PORTO_API_URL ) ) {
			return $parsed_args;
		}
		if ( ! isset( $parsed_args['headers'] ) || ! is_array( $parsed_args['headers'] ) ) {
			$parsed_args['headers'] = array();
		}
		$parsed_args['headers']['Referer'] = site_url();

		return $parsed_args;
	}

	/**
	 * Create directories
	 */
	protected function makedir() {

		if ( ! file_exists( $this->path_tmp ) ) {
			wp_mkdir_p( $this->path_tmp );
		}

		$this->path_demo = wp_normalize_path( $this->path_tmp . '/' . $this->demo );
		if ( ! file_exists( $this->path_demo ) ) {
			wp_mkdir_p( $this->path_demo );
		}
	}

	/**
	 * Delete temporary directory
	 */
	public function delete_temp_dir() {

		// filesystem
		global $wp_filesystem;
		// Initialize the WordPress filesystem, no more using file_put_contents function
		if ( empty( $wp_filesystem ) ) {
			require_once( ABSPATH . '/wp-admin/includes/file.php' );
			WP_Filesystem();
		}

		// directory is located outside wp uploads dir
		$upload_dir = wp_upload_dir();
		if ( false === strpos( str_replace( '\\', '/', $this->path_demo ), str_replace( '\\', '/', $upload_dir['basedir'] ) ) ) {
			return false;
		}

		$wp_filesystem->delete( $this->path_demo, true );
	}

	/**
	 * Get response
	 */
	public function get_response( $target, $args = array(), $data_type = 'json' ) {

		$defaults = array(
			'user-agent' => 'Porto/' . PORTO_VERSION,
			'timeout'    => 60,
		);
		$args     = wp_parse_args( $args, $defaults );

		$url = $this->get_url( $target );
		if ( ! $url ) {
			$url = $target;
		}
		$response = wp_remote_get( esc_url_raw( $url ), $args );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$response = wp_remote_retrieve_body( $response );
		if ( 'json' == $data_type ) {
			$data = json_decode( $response, true );

			if ( isset( $data['error'] ) ) {
				return new WP_Error( 'invalid_response', $data['error'] );
			}

			return $data;
		}

		return $response;
	}

	public function generate_args( $ish = true ) {
		$args = array(
			'code' => $this->code,
		);

		if ( $this->is_localhost() ) {
			$args['local'] = 'true';
		}
		if ( $ish && Porto()->is_envato_hosted() ) {
			$args['ish'] = Porto()->get_ish();
		}
		return $args;
	}

	/**
	 * Get remote demo files
	 */
	public function get_remote_demo( $target = 'demos' ) {

		$path_unzip = wp_normalize_path( $this->path_demo . '/' . $this->demo );
		if ( is_dir( $path_unzip ) ) {
			return $path_unzip;
		}

		$url          = $this->url[ $target ];
		$args         = $this->generate_args();
		$args['demo'] = $this->demo;

		$url = add_query_arg( $args, $url );

		$args = array(
			'user-agent' => 'Porto/' . PORTO_VERSION,
			'timeout'    => 60,
		);

		$response = wp_remote_get( esc_url_raw( $url ), $args );

		if ( is_wp_error( $response ) ) {
			return $response;
		}
		$body = wp_remote_retrieve_body( $response );

		if ( empty( $body ) ) {
			return new WP_Error( 'error_download', __( 'The package could not be downloaded.', 'porto' ) );
		}

		if ( $json = json_decode( $body, true ) ) {
			if ( isset( $json['error'] ) ) {
				return new WP_Error( 'invalid_response', $json['error'] );
			}
		}

		// filesystem
		global $wp_filesystem;
		// Initialize the WordPress filesystem, no more using file_put_contents function
		if ( empty( $wp_filesystem ) ) {
			require_once( ABSPATH . '/wp-admin/includes/file.php' );
			WP_Filesystem();
		}

		$path_package = wp_normalize_path( $this->path_demo . '/' . $this->demo . '.zip' );

		if ( ! $wp_filesystem->put_contents( $path_package, $body, FS_CHMOD_FILE ) ) {
			@unlink( $path_package );
			return new WP_Error( 'error_fs', __( 'WordPress filesystem error.', 'porto' ) );
		}

		$unzip = unzip_file( $path_package, $this->path_demo );
		if ( is_wp_error( $unzip ) ) {
			return new WP_Error( 'error_unzip', __( 'The package could not be unziped.', 'porto' ) );
		}

		if ( ! is_dir( $path_unzip ) ) {
			/* translators: %s: upload path */
			return new WP_Error( 'error_folder', sprintf( __( 'Demo data directory does not exist (%s).', 'porto' ), $path_unzip ) );
		}

		return $path_unzip;
	}

	/**
	 * Get remote theme version
	 */
	public function get_latest_theme_version() {
		$response = $this->get_response( 'theme_version' );
		if ( is_wp_error( $response ) ) {
			return false;
		}
		if ( empty( $response['version'] ) ) {
			return false;
		}
		return $response['version'];
	}

	public function is_localhost() {
		if ( current_user_can( 'manage_options' ) ) {
			$current_sessions = wp_get_all_sessions();
			$whitelist        = array(
				'127.0.0.1',
				'localhost',
				'::1',
			);
			if ( isset( $current_sessions[0] ) && isset( $current_sessions[0]['ip'] ) && in_array( $current_sessions[0]['ip'], $whitelist ) ) {
				return true;
			}
		}
		return false;
	}
}

<?php

if ( class_exists( 'MPC_Plugin_Updater' ) ) {
	return;
}

class MPC_Plugin_Updater {
	public $slug = 'mpc-massive/mpc-massive.php';
	private $short_slug = 'mpc-massive';
	private $plugin_data;
	private $plugin_file;
	private $plugin_update;
	private $update_urls = array();

	function __construct( $plugin_file ) {
		if ( is_admin() ) {
			add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'set_transient' ) );
			add_filter( 'plugins_api', array( $this, 'set_plugin_info' ), 1000, 3 );

			add_action( 'in_plugin_update_message-' . $this->slug, array( $this, 'purchase_code_notice' ), 10, 2 );

			$this->plugin_file = $plugin_file;
		}
	}

	private function init_plugin_data() {
		if( empty( $this->plugin_data ) ) {
			$this->slug = plugin_basename( $this->plugin_file );
			$short_slug = explode( '/', $this->slug );
			$this->short_slug = isset( $short_slug[ 1 ] ) ? str_replace( '.php', '', $short_slug[ 1 ] ) : str_replace( '.php', '', $short_slug );

			$this->update_urls = array(
				'main' => 'https://products.mpcthemes.net/api/updates/' . $this->short_slug . '/update.json'
			);

			if( function_exists( 'get_plugin_data' ) ) {
				$this->plugin_data = get_plugin_data( $this->plugin_file );
			}
		}
	}

	public function purchase_code_notice( $plugin_data, $plugin_update, $return = false ) {
		global $mpc_ma_options;

		if( !isset( $mpc_ma_options[ 'purchase_code' ] ) || strlen( $mpc_ma_options[ 'purchase_code' ] ) == 36 ) {
			return '';
		}

		$notice = '<br/><strong>'. __( 'Automatic update is possible only with valid purchase code. Please include your purchase code at ', 'mpc' ) . '<a href="' . get_admin_url(). 'admin.php?page=ma-panel">' . __( 'Massive Panel', 'mpc' ) . '.</a></strong>';

		if( $return ) {
			return $notice;
		}

		echo $notice;
		return '';
	}

	public function get_update_info() {
		$http_args = array(
			'timeout' => 15,
		);

		$download_link = 'main';
		$request = wp_remote_get( $this->update_urls[ 'main' ], $http_args );

		if ( is_wp_error( $request ) ) {
			$res = new WP_Error( 'plugins_api_failed', __( 'An unexpected error occurred. Something may be wrong with WordPress.org or this server&#8217;s configuration. If you continue to have problems, please try the <a href="https://wordpress.org/support/">support forums</a>.' ), $request->get_error_message() );
		} else {
			$res = json_decode( wp_remote_retrieve_body( $request ) );

			if ( ! is_object( $res ) && ! is_array( $res ) ) {
				$res = new WP_Error( 'plugins_api_failed', __( 'An unexpected error occurred. Something may be wrong with WordPress.org or this server&#8217;s configuration. If you continue to have problems, please try the <a href="https://wordpress.org/support/">support forums</a>.' ), wp_remote_retrieve_body( $request ) );
			}

			$res->external = true;
			$res->sections = (array) $res->sections;
			$res->banners  = (array) $res->banners;

			$ma_options = get_option( 'mpc_ma_options' );

			if( isset( $ma_options[ 'purchase_code' ] ) && $ma_options[ 'purchase_code' ] != '' ) {
				$res->download_link = str_replace( '/'. $this->short_slug . '/update.json', '/download.php', $this->update_urls[ $download_link ] );
				$res->download_link = add_query_arg( array( 'key' => $ma_options[ 'purchase_code' ], 'product' => $this->short_slug ), $res->download_link );
			} else {
				$res->download_link = 'not_authorized';
			}
		}

		$this->plugin_update = $res;
	}

	public function set_transient( $transient ) {

		if ( isset( $transient->response[ $this->slug ] ) ) {
			return $transient;
		}

		$this->init_plugin_data();
		$this->get_update_info();

		if( isset( $this->plugin_update->version ) && isset( $this->slug ) && isset( $this->plugin_data['Version'] ) ) {
			$do_update = version_compare( $this->plugin_update->version, $this->plugin_data['Version'] );
		} else {
			return $transient;
		}

		if ( $do_update === 1 ) {
			$this->plugin_update->new_version = $this->plugin_update->version;
			$this->plugin_update->version     = $this->plugin_data['Version'];
			$this->plugin_update->package        = $this->plugin_update->download_link;
			$this->plugin_update->upgrade_notice = $this->purchase_code_notice( null, null, true );

			$transient->response[ $this->slug ] = $this->plugin_update;
		}

		return $transient;
	}

	public function set_plugin_info( $res, $action, $args ) {
		$this->init_plugin_data();

		if( $action != 'plugin_information' || false === strpos( $this->slug, $args->slug ) ) {
			return $res;
		}

		$this->get_update_info();

		if( $this->plugin_update ) {
			$res = $this->plugin_update;
		}

		return $res;
	}
}
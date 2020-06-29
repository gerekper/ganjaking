<?php
/**
 * Admin functions for bsf core.
 *
 * @package BSF core
 */

if ( ! function_exists( 'check_bsf_product_status' ) ) {
	/**
	 * Get status of product.
	 *
	 * @param  int $id product id.
	 * @return bool
	 */
	function check_bsf_product_status( $id ) {
		$brainstrom_products = ( get_option( 'brainstrom_products' ) ) ? get_option( 'brainstrom_products' ) : array();
		$bsf_product_themes  = ( isset( $brainstrom_products['themes'] ) ) ? $brainstrom_products['themes'] : array();

		if ( empty( $brainstrom_products ) ) {
			return false;
		}

		$status = false;
		foreach ( $brainstrom_products as $products ) {
			foreach ( $products as $key => $product ) {
				if ( $product['id'] === $id ) {
					$status = ( isset( $product['status'] ) ) ? $product['status'] : '';
					break;
				}
			}
		}

		return $status;
	}
}

if ( ! function_exists( 'get_bundled_plugins' ) ) {


	/**
	 * Retrieves bundled plugin data.
	 *
	 * @param  string $template product template.
	 * @return void
	 */
	function get_bundled_plugins( $template = '' ) {

		global $ultimate_referer;

		$brainstrom_products = get_option( 'brainstrom_products', array() );

		$prd_ids = array();

		if ( is_array( $brainstrom_products ) ) {
			init_bsf_core();
		}

		foreach ( $brainstrom_products as $key => $value ) {
			foreach ( $value as $key => $value2 ) {
				array_push( $prd_ids, $key );
			}
		}

		$path = bsf_get_api_url() . '?referer=' . $ultimate_referer;

		$data = array(
			'action' => 'bsf_fetch_brainstorm_products',
			'id'     => $prd_ids,
		);

		$request = wp_remote_post(
			$path,
			array(
				'body'    => $data,
				'timeout' => '10',
			)
		);

		// Request http URL if the https version fails.
		if ( is_wp_error( $request ) && wp_remote_retrieve_response_code( $request ) !== 200 ) {
			$path    = bsf_get_api_url( true ) . '?referer=' . $ultimate_referer;
			$request = wp_remote_post(
				$path,
				array(
					'body'    => $data,
					'timeout' => '8',
				)
			);
		}

		if ( ! is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) === 200 ) {
			$result  = json_decode( $request['body'] );
			$bundled = array();
			$simple  = array();

			if ( ! empty( $result ) ) {
				if ( ! empty( $result->bundled ) ) {
					$bundled = $result->bundled;
				}
				if ( ! empty( $result->simple ) ) {
					$simple = $result->simple;
				}
			}

			foreach ( $bundled as $key => $value ) {
				if ( empty( $value ) ) {
					unset( $bundled->$key );
				}
			}

			$brainstrom_bundled_products = (array) $bundled;
			update_option( 'brainstrom_bundled_products', $brainstrom_bundled_products );

			// update 'brainstorm_products'.
			$simple = json_decode( wp_json_encode( $simple ), 1 );

			foreach ( $brainstrom_products as $type => $products ) {

				foreach ( $products as $key => $product ) {
					$old_id = isset( $product['id'] ) ? $product['id'] : '';

					$simple[ $type ][ $old_id ]['template']     = isset( $brainstrom_products[ $type ][ $old_id ]['template'] ) ? $brainstrom_products[ $type ][ $old_id ]['template'] : '';
					$simple[ $type ][ $old_id ]['remote']       = isset( $simple[ $type ][ $old_id ]['version'] ) ? $simple[ $type ][ $old_id ]['version'] : '';
					$simple[ $type ][ $old_id ]['version']      = isset( $brainstrom_products[ $type ][ $old_id ]['version'] ) ? $brainstrom_products[ $type ][ $old_id ]['version'] : '';
					$simple[ $type ][ $old_id ]['purchase_key'] = isset( $brainstrom_products[ $type ][ $old_id ]['purchase_key'] ) ? $brainstrom_products[ $type ][ $old_id ]['purchase_key'] : '';
					$simple[ $type ][ $old_id ]['status']       = isset( $brainstrom_products[ $type ][ $old_id ]['status'] ) ? $brainstrom_products[ $type ][ $old_id ]['status'] : '';
					$simple[ $type ][ $old_id ]['message']      = isset( $brainstrom_products[ $type ][ $old_id ]['message'] ) ? $brainstrom_products[ $type ][ $old_id ]['message'] : '';
				}
			}

			update_option( 'brainstrom_products', $simple );
		}
	}
}

if ( ! function_exists( 'install_bsf_product' ) ) {
	/**
	 * Install product.
	 *
	 * @param  int   $install_id product id to install.
	 * @param array $data request data.
	 * @return array|bool
	 */
	function install_bsf_product( $install_id, $data ) {

		if ( ! current_user_can( 'install_plugins' ) ) {
			wp_die( esc_html_e( 'You do not have sufficient permissions to install plugins for this site.', 'bsf' ) );
		}
		$brainstrom_bundled_products = ( get_option( 'brainstrom_bundled_products' ) ) ? get_option( 'brainstrom_bundled_products' ) : array();
		$install_product_data        = array();

		if ( ! empty( $brainstrom_bundled_products ) ) :
			foreach ( $brainstrom_bundled_products as $keys => $products ) :
				if ( strlen( $keys ) > 1 ) {
					foreach ( $products as $key => $product ) {
						if ( $product->id === $install_id ) {
							$install_product_data = $product;
							break;
						}
					}
				} else {
					if ( $products->id === $install_id ) {
						$install_product_data = $products;
						break;
					}
				}
			endforeach;
		endif;

		if ( empty( $install_product_data ) ) {
			return false;
		}
		if ( 'plugin' !== $install_product_data->type ) {
			return false;
		}

		$is_wp = ( isset( $install_product_data->in_house ) && 'wp' === $install_product_data->in_house ) ? true : false;

		if ( $is_wp ) {
			$download_path = $install_product_data->download_url;
		} else {
			$path          = bsf_get_api_url() . '?referer=download-bundled-extension';
			$timezone      = date_default_timezone_get();
			$call          = 'file=' . $install_product_data->download_url . '&hashtime=' . strtotime( gmdate( 'd-m-Y h:i:s a' ) ) . '&timezone=' . $timezone;
			$hash          = $call;
			$get_path      = 'http://downloads.brainstormforce.com/';
			$download_path = rtrim( $get_path, '/' ) . '/download.php?' . $hash . '&base=ignore';
		}

		require_once ABSPATH . '/wp-admin/includes/file.php';
		WP_Filesystem();
		global $wp_filesystem;
		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		$wp_upgrader = new WP_Upgrader();
		$res         = $wp_upgrader->fs_connect(
			array(
				WP_CONTENT_DIR,
			)
		);
		if ( ! $res ) {
			wp_die( new WP_Error( 'Server error', esc_html__( "Error! Can't connect to filesystem", 'bsf' ) ) ); // PHPCS:ignore:WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		$plugin_upgrader = new Plugin_Upgrader();
		$defaults        = array(
			'clear_update_cache' => true,
		);
		$args            = array();
		$parsed_args     = wp_parse_args( $args, $defaults );

		$plugin_upgrader->init();
		$plugin_upgrader->install_strings();
		$plugin_upgrader->strings['downloading_package'] = __( 'Downloading package from Server', 'bsf' );
		$plugin_upgrader->strings['remove_old']          = __( 'Removing old plugin, if exists', 'bsf' );

		add_filter( 'upgrader_source_selection', array( $plugin_upgrader, 'check_package' ) );
		$plugin_upgrader->run(
			array(
				'package'           => $download_path,
				'destination'       => WP_PLUGIN_DIR,
				'clear_destination' => true, // Do not overwrite files.
				'clear_working'     => true,
				'hook_extra'        => array(
					'type'   => 'plugin',
					'action' => 'install',
				),
			)
		);
		remove_filter( 'upgrader_source_selection', array( $plugin_upgrader, 'check_package' ) );
		if ( ! $plugin_upgrader->result || is_wp_error( $plugin_upgrader->result ) ) {
			return $plugin_upgrader->result;
		}
		// Force refresh of plugin update information.
		wp_clean_plugins_cache( $parsed_args['clear_update_cache'] );

		$response        = array(
			'status' => true,
			'type'   => 'plugin',
			'name'   => $install_product_data->name,
			'init'   => $install_product_data->init,
		);
		$plugin_abs_path = WP_PLUGIN_DIR . '/' . $install_product_data->init;
		if ( is_file( $plugin_abs_path ) ) {
			if ( ! isset( $data['action'] ) && ! isset( $data['id'] ) ) {
				echo '|bsf-plugin-installed|';
			}
			$is_plugin_installed = true;
			if ( ! is_plugin_active( $install_product_data->init ) ) {
				activate_plugin( $install_product_data->init );
				if ( is_plugin_active( $install_product_data->init ) ) {
					if ( ! isset( $data['action'] ) && ! isset( $data['id'] ) ) {
						echo '|bsf-plugin-activated|';
					}
				}
			} else {
				if ( ! isset( $data['action'] ) && ! isset( $data['id'] ) ) {
					echo '|bsf-plugin-activated|';
				}
			}
		}
		return $response;
	}
}

if ( ! function_exists( 'bsf_install_callback' ) ) {
	/**
	 * Product install callback function.
	 *
	 * @return void
	 */
	function bsf_install_callback() {

		if ( ! wp_verify_nonce( $_REQUEST['security'], 'bsf_install_extension_nonce' ) || ! current_user_can( 'install_plugins' ) ) {
			wp_die( esc_html_e( 'Invalid request', 'bsf' ) );
		}

		$product_id = esc_attr( $_POST['product_id'] );
		$bundled    = esc_attr( $_POST['bundled'] );

		$data = array(
			'action' => $_GET['action'],
			'id'     => $_GET['id'],
		);

		$response = install_bsf_product( $product_id, $data );

		$redirect_url         = apply_filters( 'redirect_after_extension_install', $redirect_url = '', $product_id );
		$response['redirect'] = $redirect_url;

		wp_send_json( $response );
	}
}

add_action( 'wp_ajax_bsf_install', 'bsf_install_callback' );

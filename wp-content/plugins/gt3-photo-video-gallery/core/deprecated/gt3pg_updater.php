<?php
defined('ABSPATH') OR exit;

	if ( ! class_exists( 'gt3pg_updater' ) ) {
		if ( ! function_exists( 'get_plugin_data' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		class gt3pg_updater {
			protected $item_id = 0;
			protected $slug = '';

			protected $plugin_name = 'GT3 Plugin name';
			protected $version = '1.0.0';

			protected $store_url = 'https://gt3themes.com';
			public $license = '';
			protected $license_field = '';
			protected $license_status_field = '';
			protected $license_expires_field = '';
			protected $nonce = '';
			protected $file = null;
			public $status = 'inactive';
			public $expires = 'invalid';
			protected $debug = false;

			protected function __construct( $file = null ) {
				if ( $file == null || ! file_exists( $file ) ) {
					return false;
				}
				$this->file                  = $file;
				$plugin_info                 = get_plugin_data( $file );
				$this->version               = $plugin_info['Version'];
				$this->plugin_name           = $plugin_info['Name'];

				$this->init_variables();

				$this->actions();
			}

			public function init_variables() {
				$this->license_field         = $this->slug . '_license_key';
				$this->license_status_field  = $this->slug . '_license_status';
				$this->license_expires_field = $this->slug . '_expires';
				$this->nonce                 = $this->slug . '_wp_nonce';

				$this->license = trim( get_option( $this->license_field ) );
				$this->status  = trim( get_option( $this->license_status_field ) ) == 'valid' ? 'valid' : 'invalid';
				$expires       = trim( get_option( $this->license_expires_field ) );
				if ( $expires == 'lifetime' ) {
					$this->expires = $expires;
				} else if ( empty( $this->license ) || strtotime( $expires ) === false || strtotime( $expires ) == - 1 ) {
					$this->expires = 'invalid';
				} else {
					$this->expires = strtotime( $expires );
				}
			}

			protected function actions() {
				if ( class_exists( 'GT3_EDD_SL_Plugin_Updater' ) ) {
					new GT3_EDD_SL_Plugin_Updater( $this->store_url, $this->file, array(
						'version' => $this->version,
						'license' => $this->license,
						'item_id' => $this->item_id,
						'author'  => 'GT3 Theme',
						'url'     => home_url(),
						'beta'    => false
					) );
				} else {
					add_action( 'admin_notices', function () {
						$msg   = sprintf(esc_html('Update unaviable for plugin %s'),$this->plugin_name);
						$class = 'notice notice-warning gt3pg_error_notice';
						echo '<div class="' . esc_attr($class ). '"><p>' . $msg . '</p><pre></pre></div>';
					} );
				}
				$this->inline_actions();

				add_action( 'admin_init', array( $this, 'activate_license' ) );
				add_action( 'admin_init', array( $this, 'deactivate_license' ) );

				add_action( 'admin_notices', array( $this, 'admin_notices' ) );
				add_action( 'in_plugin_update_message-' . plugin_basename( $this->file ), array( $this, 'plugin_row_license_missing' ), 10, 2 );

			}

			protected function inline_actions() {
				add_action( 'admin_init', function () {
					register_setting( $this->slug . '_license', $this->license_field, array( $this, 'sanitize_license' ) );
				} );

				add_filter( 'gt3pg_admin_licence', function ( $licenses ) {
					$licenses[] = array(
						'slug'          => $this->slug,
						'status_field'  => $this->license_status_field,
						'license_field' => $this->license_field,
						'license'       => $this->license,
						'nonce'         => $this->nonce,
						'plugin_name'   => $this->plugin_name,
						'status'        => $this->status,
						'expires'       => $this->expires,
					);

					return $licenses;
				}, 20, 1 );
			}

			public function plugin_row_license_missing( $plugin_data, $version_info ) {


				if ( 'valid' != $this->status ) {

					echo '&nbsp;<strong><a href="' . esc_url( $this->get_menu_page() ) . '">' . esc_html__( 'Enter valid license key for automatic updates.', 'gt3pg' ) . '</a></strong>';
				}

			}

			public function admin_notices() {
				if ( isset( $_GET['sl_activation'] ) && ! empty( $_GET['sl_message'] ) ) {
					switch ( $_GET['sl_activation'] ) {
						case 'false':
							echo '<div class="error"><p>' . urldecode( $_GET['sl_message'] ) . '</p></div>';
							break;
						case 'true':
						default:
							// Developers can put a custom success message here for when activation is successful if they way.
							break;
					}
					unset( $_GET['sl_activation'] );
					unset( $_GET['sl_message'] );
				}

			}

			public function get_menu_page() {
				return menu_page_url( 'gt3pg_pro_license', false );
			}

			public function sanitize_license( $new ) {
				$old = get_option( $this->license_field );
				if ( $old && $old != $new ) {
					$this->remove_license();
				}

				return $new;
			}

			protected function remove_license() {
				delete_option( $this->license_status_field );
				delete_option( $this->license_expires_field );
			}

			public function activate_license() {
				if ( isset( $_POST[ $this->slug . '_activate' ] ) ) {
					if ( ! check_admin_referer( $this->nonce, $this->nonce ) ) {
						return;
					}
					$license    = trim( get_option( $this->license_field ) );
					$api_params = array( 'edd_action' => 'activate_license', 'license' => $license, 'item_id' => $this->item_id, 'url' => home_url() );
					$response   = wp_remote_post( $this->store_url, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );
					$this->gt3pg_save_file( 'activate_license', $response );
					if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
						$message = ( is_wp_error( $response ) && ! empty( $error_message ) ) ? $error_message : esc_html__( 'An error occurred, please try again. ' , 'gt3pg' );
					} else {
						$license_data = json_decode( wp_remote_retrieve_body( $response ) );
						if ( false === $license_data->success ) {
							switch ( $license_data->error ) {
								case 'expired' :
									$message = sprintf(
										esc_html__( 'Your license key expired on %s.' , 'gt3pg'),
										date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
									);
									break;
								case 'revoked' :
									$message = esc_html__( 'Your license key has been disabled.' , 'gt3pg');
									break;
								case 'missing' :
									$message = esc_html__( 'Invalid license.', 'gt3pg' );
									break;
								case 'invalid' :
								case 'site_inactive' :
									$message = esc_html__( 'Your license is not active for this URL.', 'gt3pg' );
									break;
								case 'item_name_mismatch' :
									$message = sprintf( esc_html__( 'This appears to be an invalid license key for %s.', 'gt3pg' ), $this->plugin_name );
									break;
								case 'no_activations_left':
									$message = esc_html__( 'Your license key has reached its activation limit.', 'gt3pg' );
									break;
								case 'invalid_item_id':
									$message = esc_html__( 'Product not found.', 'gt3pg' );
									break;
								default :
									$message = esc_html__( 'An error occurred, please try again.' , 'gt3pg');
									break;
							}
						}
					}
					$base_url = $this->get_menu_page();
					if ( ! empty( $message ) ) {
						$redirect = add_query_arg( array( 'sl_activation' => 'false', 'sl_message' => urlencode( $message ) ), $base_url );
						wp_redirect( $redirect );
						exit();
					}
					update_option( $this->license_status_field, $license_data->license );
					update_option( $this->license_expires_field, $license_data->expires );
					wp_redirect( $base_url );
					exit();
				}
			}

			public function deactivate_license() {
				if ( isset( $_POST[ $this->slug . '_deactivate' ] ) ) {
					if ( ! check_admin_referer( $this->nonce, $this->nonce ) ) {
						return;
					}

					$api_params = array(
						'edd_action' => 'deactivate_license',
						'license'    => $this->license,
						'item_id'    => $this->item_id,
						'url'        => home_url()
					);

					$response = wp_remote_post( $this->store_url, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );
					$this->gt3pg_save_file( 'deactivate_license', $response );
					$base_url = $this->get_menu_page();
					if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
						if ( $response instanceof WP_Error ) {
							$message = $response->get_error_message();
						} else {
							$message = esc_html__( 'An error occurred, please try again.', 'gt3pg' );
						}

						$redirect = add_query_arg( array( 'sl_activation' => 'false', 'message' => urlencode( $message ) ), $base_url );

						wp_redirect( $redirect );
						exit();
					}

					$license_data = json_decode( wp_remote_retrieve_body( $response ) );
					$this->gt3pg_save_file( 'deactivate_license_part2', $response );

					if ( $license_data->license == 'deactivated' || $license_data->license == 'failed' ) {
						$this->remove_license();
					}
					wp_redirect( $base_url );
					exit();

				}
			}

			function gt3pg_save_file( $file, $value ) {
				if ( ! $this->debug ) {
					return;
				}
				$path = dirname( __FILE__ ) . '/log';
				if ( ! file_exists( $path ) ) {
					mkdir( $path );
				}

				$date = date( 'd.m.Y_h.i.s' );
				$file = $path . '/' . $file . '_' . $date . '.txt';
				$fp   = fopen( $file, 'w+' );
				if ( is_array( $value ) || is_object( $value ) ) {
					if ( is_object( $value ) ) {
						$value = get_object_vars( $value );
					}
					$value = print_r( $value, true );
				}
				fwrite( $fp, $value );
				fflush( $fp );
				fclose( $fp );
			}

			public function check_license() {
				$api_params = array(
					'edd_action' => 'check_license',
					'license'    => $this->license,
					'item_id'    => $this->item_id,
					'url'        => home_url()
				);
				$response   = wp_remote_post( $this->store_url, array( 'body' => $api_params, 'timeout' => 15, 'sslverify' => false ) );
				if ( is_wp_error( $response ) ) {
					return false;
				}

				$license_data = json_decode( wp_remote_retrieve_body( $response ) );

				$this->gt3pg_save_file( 'check_license', $license_data );

				if ( $license_data->license != 'valid' ) {
					$this->remove_license();
				} else {
					update_option( $this->license_expires_field, $license_data->expires );
				}
				return $license_data;
			}

			protected function get_plugin_name_from_path( $file = null ) {
				if ( $file == null ) {
					$file = $this->file;
				}
				$file = explode( '/', $file );
				end( $file );
				$plugin = $file[ key( $file )-1 ] . '/' . $file[ key( $file ) ];

				return $plugin;
			}

		}
	}


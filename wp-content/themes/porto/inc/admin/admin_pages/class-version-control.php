<?php
/**
 * Porto Version Control
 *
 * @since 6.3.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'Porto_Version_Control' ) ) {
	class Porto_Version_Control {
		/**
		 * The theme and plugin data for rollback
		 *
		 * @since 6.3.0
		 */
		public $rollback_data = array(
			'porto'               => array(
				'type' => 'theme',
			),
			'porto-functionality' => array(
				'type' => 'plugin',
			),
			'porto-vc-addon'      => array(
				'type'  => 'plugin',
				'since' => '6.2.3',
			),
		);

		/**
		 * The Constructor
		 *
		 * @since 6.3.0
		 */
		public function __construct() {
			add_action( 'admin_menu', array( $this, 'admin_menu' ), 11 );
			add_action( 'wp_ajax_porto_refresh_versions', array( $this, 'refresh_versions' ) );
			add_action( 'wp_ajax_porto_rollback_version', array( $this, 'rollback_version' ) );
			add_action( 'wp_ajax_porto_apply_version', array( $this, 'apply_version' ) );
			if ( ! current_user_can( 'administrator' ) || ! isset( $_GET['page'] ) || 'porto-version-control' != $_GET['page'] ) {
				return;
			}
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ), 1001 );
		}

		public function enqueue() {
			wp_enqueue_script( 'porto-version-control', PORTO_JS . '/admin/version-control.js', array( 'porto-admin' ), PORTO_VERSION, true );
		}

		/**
		 * Add menu.
		 *
		 * @since 6.3.0
		 */
		public function admin_menu() {
			if ( Porto()->is_registered() ) {
				add_submenu_page( 'porto', __( 'Version Control', 'porto' ), __( 'Version Control', 'porto' ), 'administrator', 'porto-version-control', array( $this, 'version_control' ) );
			}
		}

		/**
		 * Get the versions.
		 *
		 * @since 6.3.0
		 */
		public function get_versions() {
			$rollback = get_site_transient( 'porto_rollback_versions' );
			if ( empty( $rollback ) ) {
				require_once PORTO_PLUGINS . '/importer/importer-api.php';
				$importer_api = new Porto_Importer_API();
				$rollback     = $importer_api->get_rollback_versions();
				$under        = array();
				if ( is_array( $rollback ) ) {
					foreach ( $rollback as $version ) {
						if ( version_compare( PORTO_VERSION, $version, '>' ) ) {
							$under[] = $version;
						}
					}
				}
				$rollback = $under;
				set_site_transient( 'porto_rollback_versions', $rollback );
			}
			return $rollback;
		}

		/**
		 * View version control.
		 *
		 * @since 6.3.0
		 */
		public function version_control() {
			$availabel_versions = $this->get_versions();
			?>
			<div class="wrap">
				<h1 class="screen-reader-text"><?php esc_html_e( 'Version Control', 'porto' ); ?></h1>
			</div>
			<div class="wrap porto-wrap">
				<?php
					porto_get_template_part(
						'inc/admin/admin_pages/header',
						null,
						array(
							'active_item' => 'setup_wizard',
							'title'       => __( 'Version Control', 'porto' ),
							'subtitle'    => __( 'To a Lower Version', 'porto' ),
						)
					);
				?>
				<main>
					<h2><?php esc_html_e( 'Rollback to a Previous Version', 'porto' ); ?></h2>
					<p><?php echo sprintf( esc_html__( 'Are you experiencing an issue with Porto version %s? If you have the issues, we recommend you should reinstall a previous version.', 'porto' ), PORTO_VERSION ); ?></p>
					<div class="porto-rollback">
						<h4 style="font-size: .875rem;"><?php esc_html_e( 'Rollback Version:', 'porto' ); ?></h4>
						<div>
							<select class="porto-rollback-version">
								<?php if ( ! empty( $availabel_versions ) && is_array( $availabel_versions ) ) : ?>
									<?php foreach ( $availabel_versions as $version ) : ?>
										<option value="<?php echo esc_attr( $version ); ?>"><?php echo esc_html( $version ); ?></option>
									<?php endforeach; ?>
								<?php endif; ?>
							</select>
							<a href="#" class="button button-primary porto-rollback-button"><i class="fas fa-redo"></i> <?php esc_html_e( 'RollBack to', 'porto' ); ?></a>
							<a href="#" class="button porto-refresh-versions"><i class="fas fa-trash-restore"></i> <?php esc_html_e( 'Refresh Versions', 'porto' ); ?></a>
						</div>
					</div>
					<p style="margin-top: 0;font-size: 12px;color: red"><?php esc_html_e( 'Warning: Please backup your database before rollback.', 'porto' ); ?></p>
				</main>
			</div>
			<?php
		}

		/**
		 * Clear transient and redirect
		 *
		 * @since 6.3.0
		 */
		public function refresh_versions() {
			check_ajax_referer( 'porto-admin-nonce' );
			delete_site_transient( 'porto_rollback_versions' );
			die;
		}

		/**
		 * Rollback Version
		 *
		 * @since 6.3.0
		 */
		public function rollback_version() {
			check_ajax_referer( 'porto-admin-nonce' );
			delete_site_transient( 'porto_rollback_versions' );

			$rollback_version = isset( $_REQUEST['version'] ) ? $_REQUEST['version'] : '';
			if ( $rollback_version ) {
				require_once PORTO_PLUGINS . '/importer/importer-api.php';
				$importer_api = new Porto_Importer_API();
				$args         = $importer_api->generate_args( false );
				$url          = $importer_api->get_url( 'rollback_versions' );
				$url          = add_query_arg( 'version', $rollback_version, $url );
				if ( isset( $args['code'] ) ) {
					$url = add_query_arg( 'code', $args['code'], $url );
				}
				// filesystem
				global $wp_filesystem;
				// Initialize the WordPress filesystem, no more using file_put_contents function
				if ( empty( $wp_filesystem ) ) {
					require_once( ABSPATH . '/wp-admin/includes/file.php' );
					WP_Filesystem();
				}

				$upload_dir = wp_upload_dir();
				foreach ( $this->rollback_data as $slug => $value ) {
					$path_package = $upload_dir['basedir'] . '/' . $slug . '.zip';
					$response     = $importer_api->get_response( add_query_arg( 'slug', $slug, $url ), array(), '' );
					if ( $response ) {
						if ( ! $wp_filesystem->put_contents( $path_package, $response, FS_CHMOD_FILE ) ) {
							@unlink( $path_package );
							wp_send_json_error();
							die;
						}
					} elseif ( 'porto-vc-addon' == $slug ) {
						deactivate_plugins( 'porto-vc-addon/init.php', true, false );
					}
				}
				wp_send_json_success();
			} else {
				wp_send_json_error();
			}

			die;
		}

		/**
		 * Apply version.
		 *
		 * @since 6.3.0
		 */
		public function apply_version() {
			check_ajax_referer( 'porto-admin-nonce' );
			$rollback_version = isset( $_REQUEST['version'] ) ? $_REQUEST['version'] : '';
			if ( $rollback_version ) {
				$upload_dir = wp_upload_dir();
				foreach ( $this->rollback_data as $slug => $value ) {
					$path_package = $upload_dir['basedir'] . '/' . $slug . '.zip';
					if ( file_exists( $path_package ) ) {
						$this->simulate_package( $rollback_version, $slug, $value['type'] );
						@unlink( $path_package );
					}
				}
			} else {
				wp_send_json_error();
			}
			die;
		}

		/**
		 * Simulates the packages
		 *
		 * @since 6.3.0
		 */
		public function simulate_package( $version, $slug, $type = 'theme' ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
			require_once ABSPATH . 'wp-admin/includes/misc.php';
			require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
			$upload_dir = wp_upload_dir();
			if ( is_ssl() ) {
				$upload_dir['baseurl'] = str_replace( 'http://', 'https://', $upload_dir['baseurl'] );
			}
			$update_data = get_site_transient( 'update_' . $type . 's' );
			if ( ! is_object( $update_data ) ) {
				$update_data = (object) $update_data;
			}
			$info = array(
				'new_version' => $version,
				'slug'        => $slug,
				'package'     => $upload_dir['baseurl'] . '/' . $slug . '.zip',
				'url'         => 'https://www.portotheme.com/wordpress/porto/',
			);
			if ( 'plugin' == $type ) {
				$update_data->response[ $slug ] = (object) $info;
			} else {
				$update_data->response[ $slug ] = $info;
			}
			set_site_transient( 'update_' . $type . 's', $update_data );
			$upgrader_args = [
				'url'   => 'update.php?action=upgrade-' . $type . '&' . $type . '=' . urlencode( $slug ),
				$type   => $slug,
				'nonce' => 'upgrade-' . $type . '_' . $slug,
				'title' => esc_html__( 'Rollback to a Previous Version', 'porto' ),
			];

			if ( 'theme' == $type ) {
				$upgrader = new Theme_Upgrader( new Theme_Upgrader_Skin( $upgrader_args ) );
			} else {
				$upgrader = new Plugin_Upgrader( new Plugin_Upgrader_Skin( $upgrader_args ) );
			}
			$upgrader->upgrade( $slug );
		}
	}
}

new Porto_Version_Control;

<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_System_Status' ) ) {

	/**
	 * YITH System Status Panel
	 *
	 * Setting Page to Manage Plugins
	 *
	 * @class      YITH_System_Status
	 * @since      1.0
	 * @author     Alberto Ruggiero
	 * @package    YITH
	 */
	class YITH_System_Status {

		/**
		 * @var string the page slug
		 */
		protected $_page = 'yith_system_info';

		/**
		 * @var array plugins requirements list
		 */
		protected $_plugins_requirements = array();

		/**
		 * @var array requirements labels
		 */
		public $_requirement_labels = array();

		/**
		 * @var int recommended memory amount 134217728 = 128M
		 */
		private $_recommended_memory = 134217728;

		/**
		 * Single instance of the class
		 *
		 * @since 1.0.0
		 * @var YITH_System_Status
		 */
		protected static $_instance = null;

		/**
		 * Main plugin Instance
		 *
		 * @return YITH_System_Status
		 * @since  1.0.0
		 * @author Alberto Ruggiero
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		/**
		 * Constructor
		 *
		 * @return void
		 * @since  1.0.0
		 * @author Alberto Ruggiero
		 */
		public function __construct() {

			if ( ! is_admin() ) {
				return;
			}

			/**
			 * Add to prevent trigger admin_init called directly
			 * wp-admin/admin-post.php?page=yith_system_info
			 */
			if ( ! is_user_logged_in() ) {
				return;
			}

			add_action( 'admin_menu', array( $this, 'add_submenu_page' ), 99 );
			add_action( 'admin_init', array( $this, 'check_system_status' ) );
			add_action( 'admin_notices', array( $this, 'activate_system_notice' ), 15 );
			add_action( 'admin_enqueue_scripts', array( $this, 'dismissable_notice' ), 20 );
			add_action( 'init', array( $this, 'set_requirements_labels' ) );

		}

		/**
		 * Set requirements labels
		 *
		 * @return void
		 * @since  1.0.0
		 * @author Alberto Ruggiero
		 */
		public function set_requirements_labels() {

			$this->_requirement_labels = array(
				'min_wp_version'    => esc_html__( 'WordPress Version', 'yith-plugin-fw' ),
				'min_wc_version'    => esc_html__( 'WooCommerce Version', 'yith-plugin-fw' ),
				'wp_memory_limit'   => esc_html__( 'Available Memory', 'yith-plugin-fw' ),
				'min_php_version'   => esc_html__( 'PHP Version', 'yith-plugin-fw' ),
				'min_tls_version'   => esc_html__( 'TLS Version', 'yith-plugin-fw' ),
				'wp_cron_enabled'   => esc_html__( 'WordPress Cron', 'yith-plugin-fw' ),
				'simplexml_enabled' => esc_html__( 'SimpleXML', 'yith-plugin-fw' ),
				'mbstring_enabled'  => esc_html__( 'MultiByte String', 'yith-plugin-fw' ),
				'imagick_version'   => esc_html__( 'ImageMagick Version', 'yith-plugin-fw' ),
				'gd_enabled'        => esc_html__( 'GD Library', 'yith-plugin-fw' ),
				'iconv_enabled'     => esc_html__( 'Iconv Module', 'yith-plugin-fw' ),
				'opcache_enabled'   => esc_html__( 'OPCache Save Comments', 'yith-plugin-fw' ),
				'url_fopen_enabled' => esc_html__( 'URL FOpen', 'yith-plugin-fw' ),
			);

		}

		/**
		 * Add "System Information" submenu page under YITH Plugins
		 *
		 * @return void
		 * @since  1.0.0
		 * @author Alberto Ruggiero
		 */
		public function add_submenu_page() {

			$system_info  = get_option( 'yith_system_info', array() );
			$error_notice = ( isset( $system_info['errors'] ) && true === $system_info['errors'] ? ' <span class="yith-system-info-menu update-plugins">!</span>' : '' );
			$settings     = array(
				'parent_page' => 'yith_plugin_panel',
				'page_title'  => esc_html__( 'System Status', 'yith-plugin-fw' ),
				'menu_title'  => esc_html__( 'System Status', 'yith-plugin-fw' ) . $error_notice,
				'capability'  => 'manage_options',
				'page'        => $this->_page,
			);

			add_submenu_page(
				$settings['parent_page'],
				$settings['page_title'],
				$settings['menu_title'],
				$settings['capability'],
				$settings['page'],
				array( $this, 'show_information_panel' )
			);
		}

		/**
		 * Add "System Information" page template under YITH Plugins
		 *
		 * @return void
		 * @since  1.0.0
		 * @author Alberto Ruggiero
		 */
		public function show_information_panel() {

			$path = defined( 'YIT_CORE_PLUGIN_PATH' ) ? YIT_CORE_PLUGIN_PATH : get_template_directory() . '/core/plugin-fw/';

			require_once( $path . '/templates/sysinfo/system-information-panel.php' );

		}

		/**
		 * Perform system status check
		 *
		 * @return void
		 * @since  1.0.0
		 * @author Alberto Ruggiero
		 */
		public function check_system_status() {

			if ( '' === get_option( 'yith_system_info' ) || ( isset( $_GET['page'] ) && $_GET['page'] === $this->_page ) ) {

				$this->add_requirements(
					esc_html__( 'YITH Plugins', 'yith-plugin-fw' ),
					array(
						'min_wp_version'  => '4.9',
						'min_wc_version'  => '3.4',
						'min_php_version' => '5.6.20',
					)
				);
				$this->add_requirements(
					esc_html__( 'WooCommerce', 'yith-plugin-fw' ),
					array(
						'wp_memory_limit' => '64M',
					)
				);

				$system_info   = $this->get_system_info();
				$check_results = array();
				$errors        = 0;

				foreach ( $system_info as $key => $value ) {
					$check_results[ $key ] = array( 'value' => $value );

					if ( isset( $this->_plugins_requirements[ $key ] ) ) {

						foreach ( $this->_plugins_requirements[ $key ] as $plugin_name => $required_value ) {

							switch ( $key ) {
								case 'wp_cron_enabled':
								case 'mbstring_enabled':
								case 'simplexml_enabled':
								case 'gd_enabled':
								case 'iconv_enabled':
								case 'url_fopen_enabled':
								case 'opcache_enabled':
									if ( ! $value ) {
										$check_results[ $key ]['errors'][ $plugin_name ] = $required_value;
										$errors ++;
									}
									break;

								case 'wp_memory_limit':
									$required_memory = $this->memory_size_to_num( $required_value );

									if ( $required_memory > $value ) {
										$check_results[ $key ]['errors'][ $plugin_name ] = $required_value;
										$errors ++;

									} elseif ( $this->_recommended_memory > $value && $value > $required_value ) {
										$check_results[ $key ]['warnings'] = 'yes';
									}
									break;

								default:
									if ( 'imagick_version' === $key ) {
										if ( ! version_compare( $value, $required_value, '>=' ) ) {
											$check_results[ $key ]['errors'][ $plugin_name ] = $required_value;
											$errors ++;
										}
									} else {
										if ( 'n/a' !== $value ) {
											if ( ! version_compare( $value, $required_value, '>=' ) ) {
												$check_results[ $key ]['errors'][ $plugin_name ] = $required_value;
												$errors ++;
											}
										} else {
											if ( 'min_wc_version' !== $key ) {
												$check_results[ $key ]['warnings'][ $plugin_name ] = $required_value;
											}
										}
									}
							}
						}
					}
				}

				update_option(
					'yith_system_info',
					array(
						'system_info' => $check_results,
						'errors'      => $errors > 0,
					)
				);

			}

		}

		/**
		 * Handle plugin requirements
		 *
		 * @param $plugin_name  string
		 * @param $requirements array
		 *
		 * @return void
		 * @since  1.0.0
		 *
		 * @author Alberto Ruggiero
		 */
		public function add_requirements( $plugin_name, $requirements ) {

			$allowed_requirements = array_keys( $this->_requirement_labels );

			foreach ( $requirements as $requirement => $value ) {

				if ( in_array( $requirement, $allowed_requirements, true ) ) {
					$this->_plugins_requirements[ $requirement ][ $plugin_name ] = $value;
				}
			}

		}

		/**
		 * Manages notice dismissing
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function dismissable_notice() {
			$script_path = defined( 'YIT_CORE_PLUGIN_URL' ) ? YIT_CORE_PLUGIN_URL : get_template_directory_uri() . '/core/plugin-fw';
			wp_register_script( 'yith-system-info', yit_load_js_file( $script_path . '/assets/js/yith-system-info.js' ), array( 'jquery' ), '1.0.0', true );
		}

		/**
		 * Show system notice
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function activate_system_notice() {

			$system_info = get_option( 'yith_system_info', '' );

			if ( ( isset( $_GET['page'] ) && $_GET['page'] === $this->_page ) || ( ! empty( $_COOKIE['hide_yith_system_alert'] ) && 'yes' === $_COOKIE['hide_yith_system_alert'] ) || ( '' === $system_info ) || ( '' !== $system_info && false === $system_info['errors'] ) ) {
				return;
			}

			$show_notice = true;

			if ( true === $show_notice ) {
				wp_enqueue_script( 'yith-system-info' );
				?>
				<div id="yith-system-alert" class="notice notice-error is-dismissible" style="position: relative;">
					<p>
						<span class="yith-logo"><img src="<?php echo yith_plugin_fw_get_default_logo(); ?>" /></span>
						<b>
							<?php esc_html_e( 'Warning!', 'yith-plugin-fw' ); ?>
						</b><br />
						<?php
						/* translators: %1$s open link tag, %2$s open link tag*/
						echo sprintf( esc_html__( 'The system check has detected some compatibility issues on your installation.%1$sClick here%2$s to know more', 'yith-plugin-fw' ), '<a href="' . esc_url( add_query_arg( array( 'page' => $this->_page ), admin_url( 'admin.php' ) ) ) . '">', '</a>' );
						?>
					</p>
					<span class="notice-dismiss"></span>

				</div>
				<?php
			}
		}

		/**
		 * Get system information
		 *
		 * @return  array
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function get_system_info() {

			$tls             = 'n/a';
			$imagick_version = 'n/a';

			if ( function_exists( 'curl_init' ) && apply_filters( 'yith_system_status_check_ssl', true ) ) {
				//Get TLS version
				$ch = curl_init();
				curl_setopt( $ch, CURLOPT_URL, 'https://www.howsmyssl.com/a/check' );
				curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
				curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
				curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
				$data = curl_exec( $ch );
				curl_close( $ch );
				$json = json_decode( $data );

				if ( is_string( $json ) && strpos( $json, '<!DOCTYPE html>' ) !== false ) {
					$tls = 'n/a';
				} else {
					$tls = null !== $json ? str_replace( 'TLS ', '', $json->tls_version ) : '';
				}

				if ( 'n/a' === $tls || '' === $tls ) {
					//run backup service
					$ch = curl_init();
					curl_setopt( $ch, CURLOPT_URL, 'https://ttl-version.yithemes.workers.dev/' );
					curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
					curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
					curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
					$data = curl_exec( $ch );
					curl_close( $ch );
					$json = json_decode( $data );

					if ( is_string( $json ) && strpos( $json, '<!DOCTYPE html>' ) !== false ) {
						$tls = 'n/a';
					} else {
						$tls = null !== $json ? str_replace( 'TLSv', '', $json->tlsVersion ) : 'n/a'; //phpcs:ignore
					}
				}
			}

			//Get PHP version
			preg_match( '#^\d+(\.\d+)*#', PHP_VERSION, $match );
			$php_version = $match[0];

			// WP memory limit.
			$wp_memory_limit = $this->memory_size_to_num( WP_MEMORY_LIMIT );
			if ( function_exists( 'memory_get_usage' ) ) {
				$wp_memory_limit = max( $wp_memory_limit, $this->memory_size_to_num( @ini_get( 'memory_limit' ) ) ); //phpcs:ignore
			}

			if ( class_exists( 'Imagick' ) && is_callable( array( 'Imagick', 'getVersion' ) ) ) {
				preg_match( '/([0-9]+\.[0-9]+\.[0-9]+)/', Imagick::getVersion()['versionString'], $imatch );
				$imagick_version = $imatch[0];
			}

			return apply_filters(
				'yith_system_additional_check',
				array(
					'min_wp_version'    => get_bloginfo( 'version' ),
					'min_wc_version'    => function_exists( 'WC' ) ? WC()->version : 'n/a',
					'wp_memory_limit'   => $wp_memory_limit,
					'min_php_version'   => $php_version,
					'min_tls_version'   => $tls,
					'imagick_version'   => $imagick_version,
					'wp_cron_enabled'   => ( ! ( defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON ) || apply_filters( 'yith_system_status_server_cron', false ) ),
					'mbstring_enabled'  => extension_loaded( 'mbstring' ),
					'simplexml_enabled' => extension_loaded( 'simplexml' ),
					'gd_enabled'        => extension_loaded( 'gd' ) && function_exists( 'gd_info' ),
					'iconv_enabled'     => extension_loaded( 'iconv' ),
					'opcache_enabled'   => ini_get( 'opcache.save_comments' ),
					'url_fopen_enabled' => ini_get( 'allow_url_fopen' ),
				)
			);

		}

		/**
		 * Convert size into number
		 *
		 * @param   $memory_size string
		 *
		 * @return  integer
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function memory_size_to_num( $memory_size ) {
			$unit = strtoupper( substr( $memory_size, - 1 ) );
			$size = substr( $memory_size, 0, - 1 );

			$multiplier = array(
				'P' => 5,
				'T' => 4,
				'G' => 3,
				'M' => 2,
				'K' => 1,
			);

			if ( isset( $multiplier[ $unit ] ) ) {
				for ( $i = 1; $i <= $multiplier[ $unit ]; $i ++ ) {
					$size *= 1024;
				}
			}

			return $size;
		}

		/**
		 * Format requirement value
		 *
		 * @param   $key   string
		 * @param   $value mixed
		 *
		 * @return  string
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function format_requirement_value( $key, $value ) {

			if ( strpos( $key, '_enabled' ) !== false ) {
				return $value ? esc_html__( 'Enabled', 'yith-plugin-fw' ) : esc_html__( 'Disabled', 'yith-plugin-fw' );
			} elseif ( 'wp_memory_limit' === $key ) {
				return esc_html( size_format( $value ) );
			} else {
				if ( 'n/a' === $value ) {
					return esc_html__( 'N/A', 'yith-plugin-fw' );
				} else {
					return $value;
				}
			}

		}

		/**
		 * Print error messages
		 *
		 * @param   $key   string
		 * @param   $item  array
		 * @param   $label string
		 *
		 * @return  void
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function print_error_messages( $key, $item, $label ) {
			?>
			<ul>
				<?php foreach ( $item['errors'] as $plugin => $requirement ) : ?>
					<li>
						<?php
						if ( strpos( $key, '_enabled' ) !== false ) {
							/* translators: %1$s plugin name, %2$s requirement name */
							echo sprintf( esc_html__( '%1$s needs %2$s enabled', 'yith-plugin-fw' ), '<b>' . $plugin . '</b>', '<b>' . $label . '</b>' );
						} elseif ( 'wp_memory_limit' === $key ) {
							/* translators: %1$s plugin name, %2$s required memory amount */
							echo sprintf( esc_html__( '%1$s needs at least %2$s of available memory', 'yith-plugin-fw' ), '<b>' . $plugin . '</b>', '<span class="error">' . esc_html( size_format( $this->memory_size_to_num( $requirement ) ) ) . '</span>' );
						} else {
							/* translators: %1$s plugin name, %2$s version number */
							echo sprintf( esc_html__( '%1$s needs at least %2$s version', 'yith-plugin-fw' ), '<b>' . $plugin . '</b>', '<span class="error">' . $requirement . '</span>' );
						}
						?>
					</li>
				<?php endforeach; ?>
			</ul>
			<?php
		}

		/**
		 * Print solution suggestions
		 *
		 * @param   $key   string
		 * @param   $item  array
		 * @param   $label string
		 *
		 * @return  void
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function print_solution_suggestion( $key, $item, $label ) {
			switch ( $key ) {
				case 'min_wp_version':
				case 'min_wc_version':
					esc_html_e( 'Update it to the latest version in order to benefit of all new features and security updates.', 'yith-plugin-fw' );
					break;
				case 'min_php_version':
				case 'min_tls_version':
					esc_html_e( 'Contact your hosting company in order to update it.', 'yith-plugin-fw' );
					break;
				case 'imagick_version':
					if ( 'n/a' === $item['value'] ) {
						esc_html_e( 'Contact your hosting company in order to install it.', 'yith-plugin-fw' );
					} else {
						esc_html_e( 'Contact your hosting company in order to update it.', 'yith-plugin-fw' );
					}
					break;
				case 'wp_cron_enabled':
					/* translators: %1$s code, %2$s file name */
					echo sprintf( esc_html__( 'Remove %1$s from %2$s file', 'yith-plugin-fw' ), '<code>define( \'DISABLE_WP_CRON\', true );</code>', '<b>wp-config.php</b>' );
					break;
				case 'mbstring_enabled':
				case 'simplexml_enabled':
				case 'gd_enabled':
				case 'iconv_enabled':
				case 'opcache_enabled':
				case 'url_fopen_enabled':
					esc_html_e( 'Contact your hosting company in order to enable it.', 'yith-plugin-fw' );
					break;
				case 'wp_memory_limit':
					/* translators: %1$s opening link tag, %2$s closing link tag */
					echo sprintf( esc_html__( 'Read more %1$shere%2$s or contact your hosting company in order to increase it.', 'yith-plugin-fw' ), '<a href="https://codex.wordpress.org/Editing_wp-config.php#Increasing_memory_allocated_to_PHP" target="_blank">', '</a>' );
					break;
				default:
					echo apply_filters( 'yith_system_generic_message', '', $key, $item, $label );
			}
		}

		/**
		 * Print warning messages
		 *
		 * @param   $key   string
		 *
		 * @return  void
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function print_warning_messages( $key ) {
			switch ( $key ) {
				case 'wp_memory_limit':
					/* translators: %s recommended memory amount */
					echo sprintf( esc_html__( 'For optimal functioning of our plugins, we suggest setting at least %s of available memory', 'yith-plugin-fw' ), '<span class="warning">' . esc_html( size_format( $this->_recommended_memory ) ) . '</span>' );
					echo '<br/>';
					/* translators: %1$s opening link tag, %2$s closing link tag */
					echo sprintf( esc_html__( 'Read more %1$shere%2$s or contact your hosting company in order to increase it.', 'yith-plugin-fw' ), '<a href="https://codex.wordpress.org/Editing_wp-config.php#Increasing_memory_allocated_to_PHP" target="_blank">', '</a>' );
					break;
				case 'min_tls_version':
					if ( ! function_exists( 'curl_init' ) ) {
						/* translators: %1$s TLS label, %2$s cURL label */
						echo sprintf( esc_html__( 'The system check cannot determine which %1$s version is installed because %2$s module is disabled. Ask your hosting company to enable it.', 'yith-plugin-fw' ), '<b>TLS</b>', '<b>cURL</b>' );
					} else {
						/* translators: %1$s TLS label */
						echo sprintf( esc_html__( 'The system check cannot determine which %1$s version is installed due to a connection issue between your site and our server.', 'yith-plugin-fw' ), '<b>TLS</b>' );
					}
					break;
			}
		}

	}
}

/**
 * Main instance of plugin
 *
 * @return YITH_System_Status object
 * @since  1.0
 * @author Alberto Ruggiero
 */
if ( ! function_exists( 'YITH_System_Status' ) ) {
	function YITH_System_Status() {//phpcs:ignore
		return YITH_System_Status::instance();
	}
}

YITH_System_Status();

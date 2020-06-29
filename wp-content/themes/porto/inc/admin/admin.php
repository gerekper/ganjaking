<?php

/**
 * Porto Admin Class
 */
defined( 'ABSPATH' ) || exit;

class Porto_Admin {

	private $_checkedPurchaseCode;

	private $activation_url = 'https://sw-themes.com/activation/porto_wp/verify_purchase.php';

	public function __construct() {
		if ( is_admin_bar_showing() ) {
			add_action( 'wp_before_admin_bar_render', array( $this, 'add_wp_toolbar_menu' ) );
		}
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'after_switch_theme', array( $this, 'after_switch_theme' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'add_theme_update_url' ), 1001 );

		if ( is_admin() ) {
			add_filter( 'pre_set_site_transient_update_themes', array( $this, 'pre_set_site_transient_update_themes' ) );
			add_filter( 'upgrader_pre_download', array( $this, 'upgrader_pre_download' ), 10, 3 );
			add_action( 'wp_ajax_porto_switch_theme_options_panel', array( $this, 'switch_options_panel' ) );
			add_action( 'wp_ajax_nopriv_porto_switch_theme_options_panel', array( $this, 'switch_options_panel' ) );
		}
	}

	public function switch_options_panel() {
		if ( current_user_can( 'edit_theme_options' ) ) {
			if ( isset( $_POST['type'] ) && 'redux' == $_POST['type'] ) {
				set_theme_mod( 'theme_options_use_new_style', false );
			} else {
				set_theme_mod( 'theme_options_use_new_style', true );
			}
		}
	}

	public function add_wp_toolbar_menu() {
		if ( current_user_can( 'edit_theme_options' ) ) {
			$porto_parent_menu_title = '<span class="ab-icon"></span><span class="ab-label">Porto</span>';
			$this->add_wp_toolbar_menu_item( $porto_parent_menu_title, false, admin_url( 'admin.php?page=porto' ), array( 'class' => 'porto-menu' ), 'porto' );
			if ( get_theme_mod( 'theme_options_use_new_style', false ) ) {
				$this->add_wp_toolbar_menu_item( __( 'Theme Options', 'porto' ), 'porto', admin_url( 'customize.php' ) );
				$this->add_wp_toolbar_menu_item( __( 'Advanced Options', 'porto' ), 'porto', admin_url( 'themes.php?page=porto_settings' ) );
			} else {
				$this->add_wp_toolbar_menu_item( __( 'Theme Options', 'porto' ), 'porto', admin_url( 'themes.php?page=porto_settings' ) );
			}
			$this->add_wp_toolbar_menu_item( __( 'Theme License', 'porto' ), 'porto', admin_url( 'admin.php?page=porto' ) );
			$this->add_wp_toolbar_menu_item( __( 'Change Log', 'porto' ), 'porto', admin_url( 'admin.php?page=porto-changelog' ) );
			// add wizard menus
			$this->add_wp_toolbar_menu_item( __( 'Setup Wizard', 'porto' ), 'porto', admin_url( 'admin.php?page=porto-setup-wizard' ) );
			$this->add_wp_toolbar_menu_item( __( 'Speed Optimize Wizard', 'porto' ), 'porto', admin_url( 'admin.php?page=porto-speed-optimize-wizard' ) );
		}
	}

	public function add_wp_toolbar_menu_item( $title, $parent = false, $href = '', $custom_meta = array(), $custom_id = '' ) {
		global $wp_admin_bar;
		if ( current_user_can( 'edit_theme_options' ) ) {
			if ( ! is_super_admin() || ! is_admin_bar_showing() ) {
				return;
			}
			// Set custom ID
			if ( $custom_id ) {
				$id = $custom_id;
			} else { // Generate ID based on $title
				$id = strtolower( str_replace( ' ', '-', $title ) );
			}
			// links from the current host will open in the current window
			$meta = strpos( $href, site_url() ) !== false ? array() : array( 'target' => '_blank' ); // external links open in new tab/window

			$meta = array_merge( $meta, $custom_meta );
			$wp_admin_bar->add_node(
				array(
					'parent' => $parent,
					'id'     => $id,
					'title'  => $title,
					'href'   => $href,
					'meta'   => $meta,
				)
			);
		}
	}

	public function admin_menu() {
		if ( is_admin() && current_user_can( 'edit_theme_options' ) ) {
			$welcome_screen = add_menu_page( 'Porto', 'Porto', 'administrator', 'porto', array( $this, 'welcome_screen' ), 'dashicons-porto-logo', 59 );
			$welcome        = add_submenu_page( 'porto', __( 'Theme license', 'porto' ), __( 'Theme License', 'porto' ), 'administrator', 'porto', array( $this, 'welcome_screen' ) );
			$changelog      = add_submenu_page( 'porto', __( 'Change Log', 'porto' ), __( 'Change Log', 'porto' ), 'administrator', 'porto-changelog', array( $this, 'changelog' ) );
			if ( get_theme_mod( 'theme_options_use_new_style', false ) ) {
				$theme_options = add_submenu_page( 'porto', __( 'Theme Options', 'porto' ), __( 'Theme Options', 'porto' ), 'administrator', 'customize.php' );
				$theme_options = add_submenu_page( 'porto', __( 'Advanced Options', 'porto' ), __( 'Advanced Options', 'porto' ), 'administrator', 'themes.php?page=porto_settings' );
			} else {
				$theme_options = add_submenu_page( 'porto', __( 'Theme Options', 'porto' ), __( 'Theme Options', 'porto' ), 'administrator', 'themes.php?page=porto_settings' );
			}
		}
	}

	public function welcome_screen() {
		require_once PORTO_ADMIN . '/admin_pages/welcome.php';
	}

	public function changelog() {
		require_once PORTO_ADMIN . '/admin_pages/changelog.php';
	}

	public function let_to_num( $size ) {
		$l   = substr( $size, -1 );
		$ret = substr( $size, 0, -1 );
		switch ( strtoupper( $l ) ) {
			case 'P':
				$ret *= 1024;
			case 'T':
				$ret *= 1024;
			case 'G':
				$ret *= 1024;
			case 'M':
				$ret *= 1024;
			case 'K':
				$ret *= 1024;
		}
		return $ret;
	}

	public function check_purchase_code() {

		if ( ! $this->_checkedPurchaseCode ) {
			$code         = isset( $_POST['code'] ) ? sanitize_text_field( $_POST['code'] ) : '';
			$code_confirm = $this->get_purchase_code();

			if ( isset( $_POST['action'] ) && ! empty( $_POST['action'] ) ) {
				preg_match( '/[a-z0-9\-]{1,63}\.[a-z\.]{2,6}$/', parse_url( site_url(), PHP_URL_HOST ), $_domain_tld );
				if ( isset( $_domain_tld[0] ) ) {
					$domain = $_domain_tld[0];
				} else {
					$domain = parse_url( site_url(), PHP_URL_HOST );
				}
				if ( ! $code || $code != $code_confirm ) {
					if ( $code_confirm ) {
						$result = $this->curl_purchase_code( $code_confirm, '', 'remove' );
					}
					if ( 'unregister' === $_POST['action'] && $result && isset( $result['result'] ) && 3 === (int) $result['result'] ) {
						$this->_checkedPurchaseCode = 'unregister';
						$this->set_purchase_code( '' );
						return $this->_checkedPurchaseCode;
					}
				}
				if ( $code ) {
					$result = $this->curl_purchase_code( $code, $domain, 'add' );
					if ( ! $result ) {
						$this->_checkedPurchaseCode = 'invalid';
						$code_confirm               = '';
					} elseif ( isset( $result['result'] ) && 1 === (int) $result['result'] ) {
						$code_confirm               = $code;
						$this->_checkedPurchaseCode = 'verified';
					} else {
						$this->_checkedPurchaseCode = $result['message'];
						$code_confirm               = '';
					}
				} else {
					$code_confirm               = '';
					$this->_checkedPurchaseCode = '';
				}
				$this->set_purchase_code( $code_confirm );
			} else {
				if ( $code && $code_confirm && $code == $code_confirm ) {
					$this->_checkedPurchaseCode = 'verified';
				}
			}
		}
		return $this->_checkedPurchaseCode;
	}

	public function curl_purchase_code( $code, $domain, $act ) {
		require_once PORTO_PLUGINS . '/importer/importer-api.php';
		$importer_api = new Porto_Importer_API();

		$result = $importer_api->get_response( $this->activation_url . "?item=9207399&code=$code&domain=$domain&siteurl=" . urlencode( site_url() ) . "&act=$act" . ( $importer_api->is_localhost() ? '&local=true' : '' ) );

		if ( ! $result || is_wp_error( $result ) ) {
			return false;
		}
		return $result;
	}

	public function get_purchase_code() {
		if ( $this->is_envato_hosted() ) {
			return SUBSCRIPTION_CODE;
		}
		return get_option( 'envato_purchase_code_9207399' );
	}

	public function is_registered() {
		if ( $this->is_envato_hosted() ) {
			return true;
		}
		return get_option( 'porto_registered' );
	}

	public function set_purchase_code( $code ) {
		update_option( 'envato_purchase_code_9207399', $code );
	}

	public function is_envato_hosted() {
		return defined( 'ENVATO_HOSTED_KEY' ) ? true : false;
	}

	public function get_ish() {
		if ( ! defined( 'ENVATO_HOSTED_KEY' ) ) {
			return false;
		}
		return substr( ENVATO_HOSTED_KEY, 0, 16 );
	}

	function get_purchase_code_asterisk() {
		$code = $this->get_purchase_code();
		if ( $code ) {
			$code = substr( $code, 0, 13 );
			$code = $code . '-****-****-************';
		}
		return $code;
	}

	public function pre_set_site_transient_update_themes( $transient ) {
		if ( ! $this->is_registered() ) {
			return $transient;
		}
		if ( empty( $transient->checked ) ) {
			return $transient;
		}

		require_once PORTO_PLUGINS . '/importer/importer-api.php';
		$importer_api   = new Porto_Importer_API();
		$new_version    = $importer_api->get_latest_theme_version();
		$theme_template = get_template();
		if ( version_compare( wp_get_theme( $theme_template )->get( 'Version' ), $new_version, '<' ) ) {

			$args = $importer_api->generate_args( false );
			if ( $this->is_envato_hosted() ) {
				$args['ish'] = $this->get_ish();
			}

			$transient->response[ $theme_template ] = array(
				'theme'       => $theme_template,
				'new_version' => $new_version,
				'url'         => $importer_api->get_url( 'changelog' ),
				'package'     => add_query_arg( $args, $importer_api->get_url( 'theme' ) ),
			);

		}
		return $transient;
	}

	public function upgrader_pre_download( $reply, $package, $obj ) {
		require_once PORTO_PLUGINS . '/importer/importer-api.php';
		$importer_api = new Porto_Importer_API();
		if ( strpos( $package, $importer_api->get_url( 'theme' ) ) !== false || strpos( $package, $importer_api->get_url( 'plugins' ) ) !== false ) {
			if ( ! $this->is_registered() ) {
				return new WP_Error( 'not_registerd', __( 'Please <a href="admin.php?page=porto">register</a> Porto theme to get access to pre-built demo websites and auto updates.', 'porto' ) );
			}
			$code   = $this->get_purchase_code();
			$domain = $importer_api->generate_args();
			$domain = $domain['domain'];
			$result = $this->curl_purchase_code( $code, $domain, 'add' );
			if ( ! isset( $result['result'] ) || 1 !== (int) $result['result'] ) {
				$message = isset( $result['message'] ) ? $result['message'] : __( 'Purchase Code is not valid or could not connect to the API server!', 'porto' );
				return new WP_Error( 'purchase_code_invalid', esc_html( $message ) );
			}
		}
		return $reply;
	}

	public function add_theme_update_url() {
		global $pagenow;
		if ( 'update-core.php' == $pagenow ) {
			require_once PORTO_PLUGINS . '/importer/importer-api.php';
			$importer_api   = new Porto_Importer_API();
			$new_version    = $importer_api->get_latest_theme_version();
			$theme_template = get_template();
			if ( version_compare( PORTO_VERSION, $new_version, '<' ) ) {
				$url         = $importer_api->get_url( 'changelog' );
				$checkbox_id = md5( wp_get_theme( $theme_template )->get( 'Name' ) );
				wp_add_inline_script( 'porto-admin', 'if (jQuery(\'#checkbox_' . $checkbox_id . '\').length) {jQuery(\'#checkbox_' . $checkbox_id . '\').closest(\'tr\').children().last().append(\'<a href="' . esc_url( $url ) . '" target="_blank">' . esc_js( __( 'View Details', 'porto' ) ) . '</a>\');}' );
			}
		}
	}

	public function after_switch_theme() {
		if ( $this->is_registered() ) {
			$this->refresh_transients();
		}
	}

	public function refresh_transients() {
		delete_site_transient( 'porto_plugins' );
		delete_site_transient( 'update_themes' );
		unset( $_COOKIE['porto_dismiss_activate_msg'] );
		setcookie( 'porto_dismiss_activate_msg', null, -1, '/' );
	}
}

$GLOBALS['porto_admin'] = new Porto_Admin();
function Porto() {
	global $porto_admin;
	if ( ! $porto_admin ) {
		$porto_admin = new Porto_Admin();
	}
	return $porto_admin;
}

if ( is_customize_preview() ) {
	require PORTO_ADMIN . '/customizer/customizer.php';

	require PORTO_ADMIN . '/customizer/header-builder.php';

	if ( get_theme_mod( 'theme_options_use_new_style', false ) ) {
		require PORTO_ADMIN . '/customizer/selective-refresh.php';
		require PORTO_ADMIN . '/customizer/customizer-reset.php';
	}
}

add_action( 'admin_init', 'porto_compile_css_on_activation' );
function porto_compile_css_on_activation() {
	if ( ! get_option( 'porto_bootstrap_style' ) ) {
		porto_compile_css( 'bootstrap' );
	}
	if ( ! get_option( 'porto_bootstrap_rtl_style' ) ) {
		porto_compile_css( 'bootstrap_rtl' );
	}
	if ( ! get_option( 'porto_dynamic_style' ) && ( false === get_transient( 'porto_dynamic_style_time' ) ) ) {
		porto_save_theme_settings();
	}
}

if ( is_admin() && ( ! function_exists( 'vc_is_inline' ) || ! vc_is_inline() ) && ! porto_is_elementor_preview() ) {
	add_action(
		'admin_init',
		function() {
			if ( isset( $_POST['porto_registration'] ) && check_admin_referer( 'porto-setup' ) ) {
				update_option( 'porto_register_error_msg', '' );
				$result = Porto()->check_purchase_code();
				if ( 'verified' === $result ) {
					update_option( 'porto_registered', true );
					Porto()->refresh_transients();
				} elseif ( 'unregister' === $result ) {
					update_option( 'porto_registered', false );
					Porto()->refresh_transients();
				} elseif ( 'invalid' === $result ) {
					update_option( 'porto_registered', false );
					update_option( 'porto_register_error_msg', __( 'Sorry, it could not connect to the Porto API server. Please try again later.', 'porto' ) );
				} else {
					update_option( 'porto_registered', false );
					update_option( 'porto_register_error_msg', $result );
				}
			}
		}
	);

	add_action(
		'admin_init',
		function() {
			if ( ! Porto()->is_registered() && ( ( 'themes.php' == $GLOBALS['pagenow'] && isset( $_GET['page'] ) && 'porto_settings' == $_GET['page'] ) || empty( $_COOKIE['porto_dismiss_activate_msg'] ) || version_compare( $_COOKIE['porto_dismiss_activate_msg'], PORTO_VERSION, '<' ) ) ) {
				add_action(
					'admin_notices',
					function() { ?>
				<div class="notice notice-error" style="position: relative;">
					<p>Please <a href="admin.php?page=porto">register</a> porto theme to get access to pre-built demo websites and auto updates.</p>
					<p><strong>Important!</strong> One <a target="_blank" href="https://themeforest.net/licenses/standard">standard license</a> is valid for only <strong>1 website</strong>. Running multiple websites on a single license is a copyright violation.</p>
					<button type="button" class="notice-dismiss porto-notice-dismiss"><span class="screen-reader-text"><?php esc_html__( 'Dismiss this notice.', 'porto' ); ?></span></button>
				</div>
				<script>
					(function($) {
						var setCookie = function (name, value, exdays) {
							var exdate = new Date();
							exdate.setDate(exdate.getDate() + exdays);
							var val = encodeURIComponent(value) + ((null === exdays) ? "" : "; expires=" + exdate.toUTCString());
							document.cookie = name + "=" + val;
						};
						$(document).on('click.porto-notice-dismiss', '.porto-notice-dismiss', function(e) {
							e.preventDefault();
							var $el = $(this).closest('.notice');
							$el.fadeTo( 100, 0, function() {
								$el.slideUp( 100, function() {
									$el.remove();
								});
							});
							setCookie('porto_dismiss_activate_msg', '<?php echo PORTO_VERSION; ?>', 30);
						});
					})(window.jQuery);
				</script>
						<?php
					}
				);
			} elseif ( ! Porto()->is_registered() && 'themes.php' == $GLOBALS['pagenow'] ) {
				add_action(
					'admin_footer',
					function() {
						?>
				<script>
					(function($){
						$(window).load(function() {
							$('.themes .theme.active .theme-screenshot').after('<div class="notice update-message notice-error notice-alt"><p>Please <a href="admin.php?page=porto" class="button-link">verify purchase</a> to get updates!</p></div>');
						});
					})(window.jQuery);
				</script>
						<?php
					}
				);
			}

		}
	);

	remove_action( 'vc_activation_hook', 'vc_page_welcome_set_redirect' );
	remove_action( 'admin_init', 'vc_page_welcome_redirect' );

	// Add Advanced Options
	if ( ! is_customize_preview() ) {
		require PORTO_ADMIN . '/setup_wizard/setup_wizard.php';
		require PORTO_ADMIN . '/setup_wizard/speed_optimize_wizard.php';
	}
}

<?php
namespace W3TC;

class Licensing_Plugin_Admin {
	private $site_inactivated = false;
	private $site_activated = false;
	/**
	 * Config
	 */
	private $_config = null;

	function __construct() {
		$this->_config = Dispatcher::config();
	}

	/**
	 * Runs plugin
	 */
	function run() {
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'wp_ajax_w3tc_verify_plugin_license_key', array( $this, 'action_verify_plugin_license_key' ) );
		add_action( "w3tc_config_ui_save-w3tc_general", array( $this, 'possible_state_change' ), 2, 10 );

		add_action( 'w3tc_message_action_licensing_upgrade',
			array( $this, 'w3tc_message_action_licensing_upgrade' ) );

		add_filter( 'w3tc_admin_bar_menu', array( $this, 'w3tc_admin_bar_menu' ) );
	}



	public function w3tc_admin_bar_menu( $menu_items ) {
		

		if ( defined( 'W3TC_DEBUG' ) && W3TC_DEBUG ) {
			$menu_items['90040.licensing'] = array(
				'id' => 'w3tc_debug_overlay_upgrade',
				'parent' => 'w3tc_debug_overlays',
				'title' => __( 'Upgrade', 'w3-total-cache' ),
				'href' => wp_nonce_url( network_admin_url(
						'admin.php?page=w3tc_dashboard&amp;' .
						'w3tc_message_action=licensing_upgrade' ), 'w3tc' )
			);
		}

		return $menu_items;
	}

	public function w3tc_message_action_licensing_upgrade() {
		add_action( 'admin_head', array( $this, 'admin_head_licensing_upgrade' ) );
	}

	public function admin_head_licensing_upgrade() {
?>
		<script type="text/javascript">
		jQuery(function() {
			w3tc_lightbox_upgrade(w3tc_nonce, 'topbar_performance');
			jQuery('#w3tc-license-instruction').show();
		});
		   </script>
		<?php
	}

	/**
	 *
	 *
	 * @param Config  $config
	 * @param Config  $old_config
	 */
	function possible_state_change( $config, $old_config ) {
		$changed = false;

		if ( $old_config->get_string( 'plugin.license_key' ) !='' &&  $config->get_string( 'plugin.license_key' ) == '' ) {
			$result = Licensing_Core::deactivate_license( $old_config->get_string( 'plugin.license_key' ) );
			if ( $result ) {
				$this->site_inactivated = true;
			}
			$changed = true;
		} elseif ( $old_config->get_string( 'plugin.license_key' ) =='' &&  $config->get_string( 'plugin.license_key' ) != '' ) {
			$result = Licensing_Core::activate_license( $config->get_string( 'plugin.license_key' ), W3TC_VERSION );
			if ( $result ) {
				$this->site_activated = true;
				$config->set( 'common.track_usage', true );
			}
			$changed = true;
		} elseif ( $old_config->get_string( 'plugin.license_key' ) != $config->get_string( 'plugin.license_key' ) ) {
			$result = Licensing_Core::activate_license( $config->get_string( 'plugin.license_key' ), W3TC_VERSION );
			if ( $result ) {
				$this->site_activated = true;
			}
			$changed = true;
		}

		if ( $changed ) {
			$state = Dispatcher::config_state();
			$state->set( 'license.next_check', 0 );
			$state->save();
		}
	}

	/**
	 * Setup notices actions
	 */
	function admin_init() {
		$capability = apply_filters( 'w3tc_capability_admin_notices',
			'manage_options' );

		$this->maybe_update_license_status();

		if ( current_user_can( $capability ) ) {
			if ( is_admin() ) {
				/**
				 * Only admin can see W3TC notices and errors
				 */
				if ( !Util_Environment::is_wpmu() ) {
					add_action( 'admin_notices', array(
							$this,
							'admin_notices'
						), 1, 1 );
				}
				add_action( 'network_admin_notices', array(
						$this,
						'admin_notices'
					), 1, 1 );

				if ( Util_Admin::is_w3tc_admin_page() ) {
					add_filter( 'w3tc_notes', array( $this, 'w3tc_notes' ) );
				}
			}
		}
	}

	private function _status_is( $s, $starts_with ) {
		$s .= '.';
		$starts_with .= '.';
		return substr( $s, 0, strlen( $starts_with ) ) == $starts_with;
	}



	/**
	 * Run license status check and display messages
	 */
	function admin_notices() {
		$message = '';

		$state = Dispatcher::config_state();
		$status = $state->get_string( 'license.status' );

		if ( defined( 'W3TC_PRO' ) ) {
		} elseif ( $status == 'no_key' ) {
		} elseif ( $this->_status_is( $status, 'inactive.expired' ) ) {
			$message = sprintf( __( 'It looks like your W3 Total Cache Pro License has expired. %s to continue using the Pro Features', 'w3-total-cache' ),
				'<input type="button" class="button-primary button-buy-plugin"' .
				' data-nonce="'. wp_create_nonce( 'w3tc' ) . '"' .
				' data-renew-key="' . esc_attr( $this->get_license_key() ) . '"' .
				' data-src="licensing_expired" value="'.__( 'Renew Now', 'w3-total-cache' ) . '" />' );
		} elseif ( $this->_status_is( $status, 'invalid' ) ) {
			$message = __( 'The W3 Total Cache license key you entered is not valid.', 'w3-total-cache' ) .
				'<a href="' . ( is_network_admin() ? network_admin_url( 'admin.php?page=w3tc_general#licensing' ):
				admin_url( 'admin.php?page=w3tc_general#licensing' ) ) . '"> ' . __( 'Please enter it again.', 'w3-total-cache' ) . '</a>';
		} elseif ( $this->_status_is( $status, 'inactive.by_rooturi.activations_limit_not_reached' ) ) {
			$message = __( 'The W3 Total Cache license key is not active for this site.', 'w3-total-cache' );
		} elseif ( $this->_status_is( $status, 'inactive.by_rooturi' ) ) {
			$message = __( 'The W3 Total Cache license key is not active for this site. ', 'w3-total-cache' ) .
				sprintf(
				__( 'You can switch your license to this website following <a class="w3tc_licensing_reset_rooturi" href="%s">this link</a>', 'w3-total-cache' ),
				Util_Ui::url( array( 'page' => 'w3tc_general', 'w3tc_licensing_reset_rooturi' => 'y' ) )
			);
		} elseif ( $this->_status_is( $status, 'inactive' ) ) {
			$message = __( 'The W3 Total Cache license key is not active.', 'w3-total-cache' );
		} elseif ( $this->_status_is( $status, 'active' ) ) {
		} else {
			$message = __( 'The W3 Total Cache license key can\'t be verified.', 'w3-total-cache' );
		}

		if ( $message ) {
			if ( !Util_Admin::is_w3tc_admin_page() ) {
				echo '<script src="' . plugins_url( 'pub/js/lightbox.js', W3TC_FILE ) . '"></script>';
				echo '<link rel="stylesheet" id="w3tc-lightbox-css"  href="' . plugins_url( 'pub/css/lightbox.css', W3TC_FILE ) . '" type="text/css" media="all" />';
			}

			Util_Ui::error_box( sprintf( "<p>$message. <a class='w3tc_licensing_check' href='%s'>" . __( 'check license status again' ) . '</a></p>',
					Util_Ui::url( array( 'page' => 'w3tc_general', 'w3tc_licensing_check_key' => 'y' ) ) )
			);
		}


		if ( $this->site_inactivated ) {
			Util_Ui::error_box( "<p>" . __( 'The W3 Total Cache license key is deactivated for this site.', 'w3-total-cache' ) ."</p>" );
		}

		if ( $this->site_activated ) {
			Util_Ui::error_box( "<p>" . __( 'The W3 Total Cache license key is activated for this site.', 'w3-total-cache' ) ."</p>" );
		}
	}



	function w3tc_notes( $notes ) {
		$terms = '';
		$state_master = Dispatcher::config_state_master();
		$terms = 'accept';
		return $notes;
	}



	/**
	 *
	 *
	 * @return string
	 */
	private function maybe_update_license_status() {
		$state = Dispatcher::config_state();
		
		$check_timeout = 3600 * 240000 * 500;
		$status = 'valid';
		$terms = true;
		$license_key = 'nullmasterinbabiato';

		
		$license = Licensing_Core::check_license( $license_key, W3TC_VERSION );

		$terms = $license->license_terms;
		$plugin_type = 'pro';
		$this->_config->set( 'plugin.type', $plugin_type );
		$state->set( 'license.status', 'active' );
		$state->set( 'license.next_check', time() + $check_timeout );
		$state->set( 'license.terms', $terms );
		$state->save();
		return $status;
	}



	function get_license_key() {
		$license_key = 'nullmasterinbabiato';
		if ( $license_key == '' )
			$license_key = ini_get( 'w3tc.license_key' );
		return $license_key;
	}



	function action_verify_plugin_license_key() {
		$license = 'nullmasterinbabiato';

		
			$status = 'active';
			echo $status->license_status;
		
		exit();
	}
}
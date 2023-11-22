<?php
/**
 * WooCommerce Compatibility Check.
 *
 * @package Automattic/WooCommerce/Grow/Tools
 */

namespace Automattic\WooCommerce\Grow\Tools\CompatChecker\v0_0_1\Checks;

use Automattic\WooCommerce\Grow\Tools\CompatChecker\v0_0_1\Exception\IncompatibleException;

defined( 'ABSPATH' ) || exit;

/**
 * The WooCommerce Compatibility Check class.
 */
class WCCompatibility extends CompatCheck {

	/**
	 * WooCommerce plugin file path.
	 *
	 * @var string
	 */
	const WC_PLUGIN_FILE = 'woocommerce/woocommerce.php';

	/**
	 * Define the L-n support policy here.
	 *
	 * @var int|float The miniumum supported WooCommerce versions before the latest.
	 *
	 * @see https://woocommerce.com/support-policy/
	 */
	private $min_wc_semver = 0.2; // By default, the latest minus two version.

	/**
	 * Determins if WooCommerce is installed.
	 *
	 * @return bool
	 */
	private function is_wc_installed() {
		$installed_plugins = get_plugins();

		return isset( $installed_plugins[ self::WC_PLUGIN_FILE ] );
	}

	/**
	 * Determines if WooCommerce is activated.
	 *
	 * @return bool
	 */
	private function is_wc_activated() {
		return class_exists( 'WooCommerce' );
	}

	/**
	 * Gets the version of the currently installed WooCommerce.
	 *
	 * @return string|null Woocommerce version number or null if undetermined.
	 */
	public static function get_wc_version() {
		return defined( 'WC_VERSION' ) && WC_VERSION ? WC_VERSION : null;
	}

	/**
	 * Determines if the WooCommerce compatible.
	 *
	 * @return bool
	 */
	private function is_wc_compatible() {
		$wc_version          = $this->get_wc_version();
		$wc_version_required = $this->plugin_data['RequiresWC'];

		if ( empty( $wc_version_required ) ) {
			return true;
		}

		return version_compare( $wc_version, $wc_version_required, '>=' );
	}

	/**
	 * Determines if the WooCommerce version is untested.
	 *
	 * @return bool
	 */
	private function is_wc_untested() {
		if ( empty( $this->plugin_data['TestedWC'] ) ) {
			return false;
		}

		$wc_version             = $this->get_wc_version();
		$wc_version_tested_upto = $this->plugin_data['TestedWC'];

		return $this->compare_major_version( $wc_version, $wc_version_tested_upto, '<=' );
	}

	/**
	 * Check WooCommerce installation and activation.
	 *
	 * @return bool
	 * @throws IncompatibleException If WooCommerce is not activated.
	 */
	private function check_wc_installation_and_activation() {
		if ( ! $this->is_wc_activated() ) {
			add_action( 'admin_notices', array( $this, 'wc_fail_load' ) );
			throw new IncompatibleException( esc_html__( 'WooCommerce is not installed or activated.', 'woogrow-compat-checker' ) );
		}
		return true;
	}

	/**
	 * Check WooCommerce version.
	 *
	 * @return bool
	 * @throws IncompatibleException If WooCommerce version is not compatible.
	 */
	private function check_wc_version() {
		if ( ! $this->is_wc_compatible() ) {
			add_action( 'admin_notices', array( $this, 'wc_out_of_date' ) );
			throw new IncompatibleException( esc_html__( 'WooCommerce version not compatible.', 'woogrow-compat-checker' ) );
		}

		if ( ! $this->is_wc_untested() ) {
			add_action( 'admin_notices', array( $this, 'wc_untested' ) );
		}

		return true;
	}

	/**
	 * Retrieves a list of the latest available WooCommerce versions.
	 *
	 * Excludes betas, release candidates and development versions.
	 * Versions are sorted from most recent to least recent.
	 *
	 * @return string[] Array of semver strings.
	 */
	private function get_latest_wc_versions() {
		$latest_wc_versions = get_transient( 'compat_checker_wc_versions' );

		if ( ! is_array( $latest_wc_versions ) ) {

			/**
			 * The endpoint to fetch the latest WooCommerce versions.
			 *
			 * @link https://codex.wordpress.org/WordPress.org_API
			 */
			$wp_org_request = wp_remote_get( 'https://api.wordpress.org/plugins/info/1.0/woocommerce.json', array( 'timeout' => 1 ) );

			if ( is_array( $wp_org_request ) && isset( $wp_org_request['body'] ) ) {

				$plugin_info = json_decode( $wp_org_request['body'], true );

				if ( is_array( $plugin_info ) && ! empty( $plugin_info['versions'] ) && is_array( $plugin_info['versions'] ) ) {

					$latest_wc_versions = array();

					// Reverse the array as WordPress supplies oldest version first, newest last.
					foreach ( array_keys( array_reverse( $plugin_info['versions'] ) ) as $wc_version ) {

						// Skip trunk, release candidates, betas and other non-final or irregular versions.
						if (
							is_string( $wc_version )
							&& '' !== $wc_version
							&& is_numeric( $wc_version[0] )
							&& false === strpos( $wc_version, '-' )
						) {
							$latest_wc_versions[] = $wc_version;
						}
					}

					set_transient( 'compat_checker_wc_versions', $latest_wc_versions, WEEK_IN_SECONDS );
				}
			}
		}

		return is_array( $latest_wc_versions ) ? $latest_wc_versions : array();
	}

	/**
	 * Get the L-n version of WooCommerce.
	 *
	 * @return string
	 */
	private function get_supported_wc_version() {

		$latest_wc_versions = $this->get_latest_wc_versions();

		if ( empty( $latest_wc_versions ) ) {
			return '';
		}

		$latest_wc_version    = current( $latest_wc_versions );
		$supported_wc_version = $latest_wc_version;

		$latest_semver    = explode( '.', $latest_wc_version );
		$supported_semver = explode( '.', (string) $this->min_wc_semver );
		$supported_major  = max( 0, (int) $latest_semver[0] - (int) $supported_semver[0] );
		$supported_minor  = isset( $supported_semver[1] ) ? (int) $supported_semver[1] : 0;
		$previous_minor   = null;

		// Loop known WooCommerce versions from the most recent until we get the oldest supported one.
		foreach ( $latest_wc_versions as $older_wc_version ) {
			// As we loop through the versions, the latest one before we break the loop will be the minimum supported version.
			$supported_wc_version = $older_wc_version;

			$older_semver = explode( '.', $older_wc_version );
			$older_major  = (int) $older_semver[0];
			$older_minor  = isset( $older_semver[1] ) ? (int) $older_semver[1]: 0;

			// if major is ignored, skip; if the minor hasn't changed (patch must be), skip.
			if ( $older_major > $supported_major || $older_minor === $previous_minor ) {
				continue;
			}

			// We reached the maximum number of supported minor versions.
			if ( $supported_minor <= 0 ) {
				break;
			}

			// Store the previous minor while we loop patch versions, which we ignore.
			$previous_minor = $older_minor;

			--$supported_minor;
		}

		return $supported_wc_version;
	}

	/**
	 * Check for WooCommerce upgrade recommendation.
	 *
	 * @return bool
	 */
	private function check_wc_upgrade_recommendation() {
		// Bail on frontend requests or if there is no definied versions to compare.
		if ( ! is_admin() || empty( $this->min_wc_semver ) || ! is_numeric( $this->min_wc_semver ) ) {
			return;
		}

		$current_wc_version   = $this->get_wc_version();
		$supported_wc_version = $this->get_supported_wc_version();

		if ( ! $this->compare_major_version( $current_wc_version, $supported_wc_version, '>=' ) ) {
			add_action( 'admin_notices', array( $this, 'make_upgrade_recommendation' ) );
		}
		return true;
	}

	/**
	 * Add notices for WooCommerce not being installed or activated.
	 */
	public function wc_fail_load() {
		$plugin_name = $this->plugin_data['Name'];

		if ( $this->is_wc_installed() ) {
			if ( ! current_user_can( 'activate_plugins' ) ) {
				return;
			}

			$activation_url = wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . self::WC_PLUGIN_FILE . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . self::WC_PLUGIN_FILE );
			$message        = sprintf(
				/* translators: %1$s - Plugin Name, %2$s - activate WooCommerce link open, %3$s - activate WooCommerce link close. */
				esc_html__( '%1$s requires WooCommerce to be activated. Please %2$sactivate WooCommerce%3$s.', 'woogrow-compat-checker' ),
				'<strong>' . $plugin_name . '</strong>',
				'<a href="' . esc_url( $activation_url ) . '">',
				'</a>'
			);
			$this->add_admin_notice(
				'woocommerce-not-activated',
				'error',
				$message
			);
		} else {
			if ( ! current_user_can( 'install_plugins' ) ) {
				return;
			}

			$install_url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=woocommerce' ), 'install-plugin_woocommerce' );
			$message     = sprintf(
				/* translators: %1$s - Plugin Name, %2$s - install WooCommerce link open, %3$s - install WooCommerce link close. */
				esc_html__( '%1$s requires WooCommerce to be installed and activated. Please %2$sinstall WooCommerce%3$s.', 'woogrow-compat-checker' ),
				'<strong>' . $plugin_name . '</strong>',
				'<a href="' . esc_url( $install_url ) . '">',
				'</a>'
			);

			$this->add_admin_notice(
				'woocommerce-not-installed',
				'error',
				$message
			);
		}
	}

	/**
	 * Add notices for out of date WooCommerce.
	 */
	public function wc_out_of_date() {
		if ( ! current_user_can( 'update_plugins' ) ) {
			return;
		}

		$plugin_name         = $this->plugin_data['Name'];
		$wc_version_required = ( 1 === substr_count( $this->plugin_data['RequiresWC'], '.' ) ) ? $this->plugin_data['RequiresWC'] . '.0' : $this->plugin_data['RequiresWC'] ; // Pad .0 if the min required WC version is not in semvar format.

		$message = sprintf(
			/* translators: %1$s - Plugin Name, %2$s - minimum WooCommerce version, %3$s - update WooCommerce link open, %4$s - update WooCommerce link close, %5$s - download minimum WooCommerce link open, %6$s - download minimum WooCommerce link close. */
			esc_html__( '%1$s requires WooCommerce version %2$s or higher. Please %3$supdate WooCommerce%4$s to the latest version, or %5$sdownload the minimum required version &raquo;%6$s', 'woogrow-compat-checker' ),
			'<strong>' . $plugin_name . '</strong>',
			$wc_version_required,
			'<a href="' . esc_url( admin_url( 'update-core.php' ) ) . '#update-plugins-table">',
			'</a>',
			'<a href="' . esc_url( 'https://downloads.wordpress.org/plugin/woocommerce.' . $wc_version_required . '.zip' ) . '">',
			'</a>'
		);

		$this->add_admin_notice(
			'woocommerce-out-of-date',
			'error',
			$message
		);
	}

	/**
	 * Adds notice for untested WooCommerce.
	 */
	public function wc_untested() {
		$plugin_name    = $this->plugin_data['Name'];
		$wc_version     = $this->get_wc_version();
		$plugin_version = $this->plugin_data['Version'];

		$message = sprintf(
			/* translators: %1$s - Plugin Name, %2$s - Plugin version, %3$s - WooCommerce version number */
			esc_html__( '%1$s - version %2$s is untested with WooCommerce %3$s.', 'woogrow-compat-checker' ),
			'<strong>' . $plugin_name . '</strong>',
			$plugin_version,
			$wc_version
		);

		$this->add_admin_notice(
			'woocommerce-untested',
			'warning is-dismissible',
			$message
		);
	}

	/**
	 * Adds notice for WooCommerce upgrade recommendation.
	 */
	public function make_upgrade_recommendation() {
		// Bail if the user does not have the update plugins capability.
		if ( ! current_user_can( 'update_plugins' ) ) {
			return;
		}

		$plugin_name        = $this->plugin_data['Name'];
		$current_wc_version = $this->get_wc_version();

		$message = sprintf(
			/* translators: Placeholders: %1$s - plugin name, %2$s - WooCommerce version number, %3$s - opening <a> HTML link tag, %4$s - closing </a> HTML link tag */
			esc_html__( 'Heads up! %1$s will soon discontinue support for WooCommerce %2$s. Please %3$supdate WooCommerce%4$s to take advantage of the latest updates and features.', 'woogrow-compat-checker' ),
			'<strong>' . $plugin_name . '</strong>',
			$current_wc_version,
			'<a href="' . esc_url( admin_url( 'update-core.php' ) ) . '#update-plugins-table">',
			'</a>'
		);

		$this->add_admin_notice(
			'woocommerce-upgrade-recommendation',
			'warning is-dismissible',
			$message
		);
	}

	/**
	 * Run all compatibility checks.
	 */
	protected function run_checks() {
		try {
			$this->check_wc_installation_and_activation();
			$this->check_wc_version();
			$this->check_wc_upgrade_recommendation();
			return true;
		} catch ( IncompatibleException $e ) {
			return false;
		}
	}
}

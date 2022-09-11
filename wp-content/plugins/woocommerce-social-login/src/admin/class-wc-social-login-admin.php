<?php
/**
 * WooCommerce Social Login
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Social Login to newer
 * versions in the future. If you wish to customize WooCommerce Social Login for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-social-login/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2014-2022, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_10_12 as Framework;

/**
 * Admin class
 *
 * @since 1.0.0
 */
class WC_Social_Login_Admin {


	/**
	 * Setup admin class
	 *
	 * @since  1.0
	 */
	public function __construct() {

		// add social login settings page
		add_filter( 'woocommerce_get_settings_pages', array( $this, 'add_settings_page' ) );

		// add social login admin report
		add_filter( 'woocommerce_admin_reports', array( $this, 'add_admin_report' ) );

		// load styles/scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'load_styles_scripts' ) );

		// show social profiles on edit user pages
		add_action( 'show_user_profile', array( $this, 'render_user_social_profiles' ) );
		add_action( 'edit_user_profile', array( $this, 'render_user_social_profiles' ) );

		// add social profiles column to the Users admin table
		add_filter( 'manage_users_columns',       array( $this, 'add_user_columns' ), 11 );
		add_filter( 'manage_users_custom_column', array( $this, 'user_column_values' ), 11, 3 );

		// adds providers information to system status report in admin
		add_action( 'woocommerce_system_status_report', array( $this, 'add_providers_system_status_report' ) );
	}


	/**
	 * Add social login settings page
	 *
	 * @since 1.0.0
	 * @param array $settings
	 * @return array
	 */
	public function add_settings_page( $settings ) {

		$settings[] = include( wc_social_login()->get_plugin_path() . '/src/admin/class-wc-social-login-settings.php' );
		return $settings;
	}


	/**
	 * Add social login report
	 *
	 * @since 1.0.0
	 * @param array $reports
	 * @return array
	 */
	public function add_admin_report( $reports ) {

		if ( isset( $reports['customers'] ) ) {

			$reports['customers']['reports']['social_login'] = array(
				/* translators: WooCommerce customers report tab title */
				'title'       => __( 'Social Registration', 'woocommerce-social-login' ),
				'description' => '',
				'hide_title'  => true,
				'callback'    => array( $this, 'get_admin_report' ),
			);
		}

		return $reports;
	}


	/**
	 * Load the report class and output it
	 */
	public static function get_admin_report() {

		include_once( wc_social_login()->get_plugin_path() . '/src/admin/class-wc-social-login-report.php' );

		$report = new \WC_Report_Social_Login();

		$report->output_report();
	}


	/**
	 * Load admin styles and scripts
	 *
	 * @since 1.0.0
	 * @param string $hook_suffix the current URL filename, ie edit.php, post.php, etc
	 */
	public function load_styles_scripts( $hook_suffix ) {

		$is_settings_page = Framework\SV_WC_Plugin_Compatibility::normalize_wc_screen_id( 'wc-settings' ) === $hook_suffix && isset( $_GET['tab'] )    && 'social_login' === $_GET['tab'];
		$is_report_page   = Framework\SV_WC_Plugin_Compatibility::normalize_wc_screen_id( 'wc-reports' )  === $hook_suffix && isset( $_GET['report'] ) && 'social_login' === $_GET['report'];
		$is_users_page    = in_array( $hook_suffix, array( 'users.php', 'profile.php', 'user-edit.php' ) );

		// load admin css only on woocommerce settings or admin report screen
		if ( $is_settings_page || $is_report_page ) {

			// admin CSS
			wp_enqueue_style( 'wc-social-login-admin', wc_social_login()->get_plugin_url() . '/assets/css/admin/wc-social-login-admin.min.css', array( 'woocommerce_admin_styles' ), \WC_Social_Login::VERSION );

			// admin JS
			wp_enqueue_script( 'wc-social-login-admin', wc_social_login()->get_plugin_url() . '/assets/js/admin/wc-social-login-admin.min.js', array( 'jquery', 'jquery-ui-sortable', 'woocommerce_admin' ), \WC_Social_Login::VERSION, true );
		}

		// WC admin.css is not enqueued on the User screens so we want to enqueue
		// the social badge styles without the 'woocommerce_admin_styles' dependency
		if (  $is_users_page ) {

			// admin CSS
			wp_enqueue_style( 'wc-social-login-admin', wc_social_login()->get_plugin_url() . '/assets/css/admin/wc-social-login-admin.min.css', array(), \WC_Social_Login::VERSION );

			// customize button colors for Users listing table and profiles
			wp_add_inline_style( 'wc-social-login-admin', wc_social_login()->get_button_colors_css() );
		}
	}


	/**
	 * Save options in admin.
	 *
	 * @since 1.0.0
	 */
	public function process_admin_options() {

		$provider_order = ( isset( $_POST['provider_order'] ) ) ? $_POST['provider_order'] : '';

		$order = array();

		if ( is_array( $provider_order ) && sizeof( $provider_order ) > 0 ) {

			$loop = 0;

			foreach ( $provider_order as $provider_id ) {

				$order[ esc_attr( $provider_id ) ] = $loop;
				$loop++;
			}
		}

		update_option( 'wc_social_login_provider_order', $order );
	}


	/**
	 * Display social profiles on the view/edit user page
	 *
	 * @since 1.3.0
	 * @param WP_User $user user object for the current edit page
	 */
	public function render_user_social_profiles( $user ) {

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		$user_id = $user->ID;

		?>
		<h3><?php esc_html_e( 'Connected Social Profiles', 'woocommerce-social-login' ) ?></h3>
		<table class="form-table">
			<tr>
				<th><label><?php esc_html_e( 'Social Profiles', 'woocommerce-social-login' ); ?></label></th>
				<td>
					<?php
						$linked_profiles = wc_social_login()->get_user_social_login_profiles( $user_id );

						foreach ( $linked_profiles as $provider_id => $profile ) :
							$provider = wc_social_login()->get_provider( $provider_id );
							printf( '<span class="social-badge social-badge-%1$s"><span class="si si-%1$s"></span>%2$s</span> ', esc_attr( $provider->get_id() ), esc_html( $provider->get_title() ) );
						endforeach;
					?>
				</td>
			</tr>
		</table>
	<?php

	}


	/**
	 * Add 'Social Profiles' column to the Users admin table
	 *
	 * @since 1.3.0
	 * @param array $columns user admin table columns
	 * @return array $columns columns array with 'Social Profiles'
	 */
	public function add_user_columns( $columns ) {
		return Framework\SV_WC_Helper::array_insert_after( $columns, 'email', array( 'wc_social_login_profiles' => __( 'Social Profiles', 'woocommerce-social-login' ) ) );
	}

	/**
	 * Render social profile icons in the 'Social Profiles' column of the Users admin table
	 *
	 * @since 1.3.0
	 * @param string $output The custom column output.
	 * @param string $column_name The column name/key.
	 * @param int $user_id The ID of the currently-listed user.
	 * @return string $output The social profile icons
	 */
	public function user_column_values( $output, $column_name, $user_id ) {

		if ( $column_name === 'wc_social_login_profiles' ) {

			$linked_profiles = wc_social_login()->get_user_social_login_profiles( $user_id );

			foreach ( $linked_profiles as $provider_id => $profile ) {
				$provider = wc_social_login()->get_provider( $provider_id );
				$output .= sprintf( '<span class="social-badge social-badge-%1$s"><span class="si si-%1$s"></span>%2$s</span> ', esc_attr( $provider->get_id() ), esc_html( $provider->get_title() ) );
			}
		}

		return $output;
	}


	/**
	 * Adds tabular data to the system status report page with providers information.
	 *
	 * @internal
	 *
	 * @since 2.6.0
	 */
	public function add_providers_system_status_report() {

		?>
		<table
			id="wc-social-login-providers"
			class="wc_status_table widefat"
			cellspacing="0">
			<thead>
				<tr>
					<th colspan="3" data-export-label="Social Login">
						<h2><?php esc_html_e( 'Social Login Providers', 'woocommerce-social-login' ); ?> <?php echo wc_help_tip( __( 'This sections shows information about social login providers.', 'woocommerce-social-login' ) ); ?></h2>
					</th>
				</tr>
			</thead>
			<?php foreach ( wc_social_login()->get_providers() as $provider ) : ?>

				<tbody>
					<tr>
						<td data-export-label="<?php echo esc_attr( ucfirst( $provider->get_id() ) ); ?>"><?php echo esc_html( $provider->get_title() ); ?>:</td>
						<td class="help">&nbsp;</td>
						<td>
							<?php if ( $provider->is_available() ): ?>
								<mark class="yes"><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Available', 'woocommerce-social-login' ); ?></mark>
							<?php elseif ( $provider->is_enabled() && ! $provider->is_configured() ) : ?>
								<mark class="error"><span class="dashicons dashicons-no"></span> <?php esc_html_e( 'Not configured', 'woocommerce-social-login' ); ?></mark>
							<?php else : ?>
								<mark class="no"><span class="dashicons dashicons-minus"></span> <?php esc_html_e( 'Disabled', 'woocommerce-social-login' ); ?></mark>
							<?php endif; ?>
						</td>
					</tr>
				</tbody>

			<?php endforeach; ?>
		</table>
		<?php
	}


}

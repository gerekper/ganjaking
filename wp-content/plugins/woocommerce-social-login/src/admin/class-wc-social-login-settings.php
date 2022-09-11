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

if ( ! class_exists( 'WC_Settings_Social_Login' ) ) :

/**
 * Settings class.
 *
 * @since 1.0.0
 */
class WC_Settings_Social_Login extends \WC_Settings_Page {


	/**
	 * Setup admin class
	 *
	 * @since  1.0
	 */
	public function __construct() {

		$this->id    = 'social_login';
		$this->label = __( 'Social Login', 'woocommerce-social-login' );

		parent::__construct();

		add_action( 'woocommerce_admin_field_social_login_providers', array( $this, 'social_login_providers_setting' ) );
	}


	/**
	 * Get sections
	 *
	 * @return array
	 */
	public function get_sections() {
		$sections = array(
			'' => __( 'Settings', 'woocommerce-social-login' )
		);

		// Load providers so we can show any global options they may have
		$providers = wc_social_login()->get_providers();

		foreach ( $providers as $provider ) {

			$sections[ strtolower( get_class( $provider ) ) ] = esc_html( $provider->get_title() );
		}

		return $sections;
	}


	/**
	 * Get settings array
	 *
	 * @since  1.0
	 * @return array settings
	 */
	public function get_settings() {

		$wc_social_login_display_options = array(
			'checkout'        => __( 'Checkout', 'woocommerce-social-login' ),
			'my_account'      => __( 'My Account', 'woocommerce-social-login' ),
			'checkout_notice' => __( 'Checkout Notice', 'woocommerce-social-login' ),
		);

		// optionally allow login buttons in Product Reviews Pro login modals
		if ( wc_social_login()->is_plugin_active( 'woocommerce-product-reviews-pro.php' ) ) {
			$wc_social_login_display_options['product_reviews_pro'] = __( 'Product Reviews Pro Login', 'woocommerce-social-login' );
		}

		$settings = array(

			array(
				'name' => __( 'Settings', 'woocommerce-social-login' ),
				'type' => 'title',
			),

			array(
				'name'     => __( 'Display Social Login buttons on:', 'woocommerce-social-login' ),
				'desc_tip' => __( 'Control where Social Login buttons are displayed.', 'woocommerce-social-login' ),
				'id'       => 'wc_social_login_display',
				'type'     => 'multiselect',
				'class'    => 'wc-enhanced-select',
				'options' => $wc_social_login_display_options,
				'default'  => array(
					'checkout',
					'my_account',
				),
			),

			array(
				'name'     => __( 'Display "Link Your Account" button on Thank You page', 'woocommerce-social-login' ),
				'desc'     => __( 'Enable to allow customers to link their social account on the Thank You page for faster login & checkout next time they purchase.', 'woocommerce-social-login' ),
				'id'       => 'wc_social_login_display_link_account_thank_you',
				'type'     => 'checkbox',
				'default'  => 'yes',
			),

			array(
				'name'     => __( 'Checkout Social Login Display Text', 'woocommerce-social-login' ),
				'desc_tip' => __( 'This option controls the text on the checkout page for the frontend section where the login providers are shown.', 'woocommerce-social-login' ),
				'id'       => 'wc_social_login_text',
				'default'  => __( 'For faster checkout, login or register using your social account.', 'woocommerce-social-login' ),
				'type'     => 'textarea',
				'css'      => 'width:100%; height: 75px;',
			),

			array(
				'name'     => __( 'Non-Checkout Social Login Display Text', 'woocommerce-social-login' ),
				'desc_tip' => __( 'This option controls the text on the non-checkout pages where the login provider buttons are shown.', 'woocommerce-social-login' ),
				'id'       => 'wc_social_login_text_non_checkout',
				'default'  => __( 'Use a social account for faster login or easy registration.', 'woocommerce-social-login' ),
				'type'     => 'textarea',
				'css'      => 'width:100%; height: 75px;',
			),

			array(
				'name'     => __( 'Force SSL for all providers', 'woocommerce-social-login' ),
				'desc'     => __( 'Enable to force SSL (HTTPS) on callback URLs for all providers (an SSL Certificate is required).', 'woocommerce-social-login' ),
				'id'       => 'wc_social_login_force_ssl_callback_url',
				'type'     => 'checkbox',
				'default'  => 'no',
			),

		);

	 	// TODO: remove this block when removing backwards compatibility
	 	// with OpAuth-style callbacks {IT 2016-10-12}
		if ( get_option( 'wc_social_login_upgraded_from_opauth' ) ) {

			$settings[] = array(
				'name'     => __( 'Callback URL format', 'woocommerce-social-login' ),
				'desc'     => __( "Set the authentication callback URL format. Legacy format is deprecated and support for it will be removed in a future version. IMPORTANT: set the format to Default only after you've updated the callback URLs for each provider.", 'woocommerce-social-login' ),
				'id'       => 'wc_social_login_callback_url_format',
				'type'     => 'select',
				'class'    => 'wc-enhanced-select',
				'options' => array(
					'default' => __( 'Default (Recommended)', 'woocommerce-social-login' ),
					'legacy'  => __( 'Legacy', 'woocommerce-social-login' ),
				),
				'default' => 'legacy',
			);
		}

		$settings[] = array( 'type' => 'social_login_providers' ); // @see WC_Settings_Social_Login::social_login_providers_setting()
		$settings[] = array( 'type' => 'sectionend' );

		/**
		 * Filter social login settings.
		 *
		 * @since 1.0.0
		 * @param array $settings
		 */
		return apply_filters('woocommerce_social_login_settings', $settings );
	}


	/**
	 * Output the settings
	 *
	 * @since 1.0.0
	 */
	public function output() {
		global $current_section;

		// Load providers so we can show any global options they may have
		$providers = wc_social_login()->get_providers();

		if ( $current_section ) {

			foreach ( $providers as $provider ) {

				if ( strtolower( get_class( $provider ) ) === strtolower( $current_section ) ) {

					$provider->admin_options();
					break;
				}
			}

		} else {

			$settings = $this->get_settings();

			\WC_Admin_Settings::output_fields( $settings );
		}
	}


	/**
	 * Output login providers settings.
	 *
	 * @since 1.0.0
	 */
	public function social_login_providers_setting() {
		?>
		<tr valign="top">
			<th scope="row" class="titledesc"><?php esc_html_e( 'Providers', 'woocommerce-social-login' ) ?></th>
				<td class="forminp">
				<table class="wc_social_login widefat" cellspacing="0">
					<thead>
						<tr>
							<th class="name"><?php esc_html_e( 'Provider', 'woocommerce-social-login' ); ?></th>
							<th class="status"><?php esc_html_e( 'Status', 'woocommerce-social-login' ); ?></th>
							<th class="settings">&nbsp;</th>
						</tr>
					</thead>
					<tbody>
							<?php
							foreach ( wc_social_login()->get_providers() as $key => $provider ) :

								echo '<tr>
									<td class="name">
										' . esc_html( $provider->get_title() ) . '
										<input type="hidden" name="provider_order[]" value="' . esc_attr( $provider->get_id() ) . '" />
									</td>
									<td class="status">';

								if ( $provider->is_available() ) :
										/* translators: Whether a social login provider is enabled or not */
										echo '<span class="status-enabled tips" data-tip="' . esc_attr__( 'Enabled', 'woocommerce-social-login' ) . '">' . esc_html__( 'Enabled', 'woocommerce-social-login' ) . '</span>';
								else :
									echo '-';
								endif;

								echo '</td>
									<td class="settings">';

									echo '<a class="button" href="' . admin_url( 'admin.php?page=wc-settings&tab=social_login&section=' . strtolower( get_class( $provider ) ) ) . '">' . esc_html__( 'Settings', 'woocommerce-social-login' ) . '</a>';

								echo '</td>
								</tr>';
							endforeach;
							?>
					</tbody>
					<tfoot>
						<tr>
							<th colspan="3">
								<span class="description"><?php esc_html_e( 'Drag and drop the above providers to control their display order.', 'woocommerce-social-login' ); ?></span>
							</th>
						</tr>
					</tfoot>
				</table>
			</td>
		</tr>
		<?php
	}


	/**
	 * Save settings
	 *
	 * @since 1.0.0
	 */
	public function save() {
		global $current_section;

		if ( ! $current_section ) {

			$settings = $this->get_settings();
			\WC_Admin_Settings::save_fields( $settings );
			wc_social_login()->get_admin_instance()->process_admin_options();

		} else {

			// ensure providers are loaded at this point so that their settings can be saved
			wc_social_login()->get_providers();

			if ( class_exists( $current_section ) ) {

				$current_section_class = new $current_section( null );

				do_action( 'woocommerce_update_options_' . $this->id . '_' . $current_section_class->id );
			}
		}
	}

}

endif;

return new WC_Settings_Social_Login();

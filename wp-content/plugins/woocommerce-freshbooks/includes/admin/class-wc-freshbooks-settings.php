<?php
/**
 * WooCommerce FreshBooks
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce FreshBooks to newer
 * versions in the future. If you wish to customize WooCommerce FreshBooks for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-freshbooks/
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2012-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * FreshBooks Settings Admin class
 *
 * Loads / saves the admin settings page
 *
 * @since 3.0
 */
class WC_FreshBooks_Settings extends \WC_Settings_Page {


	/**
	 * Add various admin hooks/filters
	 *
	 * @since 3.0
	 */
	public function __construct() {

		$this->id    = 'freshbooks';
		$this->label = __( 'FreshBooks', 'woocommerce-freshbooks' );

		parent::__construct();

		// payment gateway / method settings
		add_action( 'woocommerce_admin_field_payment_type_mapping', array( $this, 'render_payment_type_mapping_table' ) );
	}


	/**
	 * Save settings, overridden to maybe create the webhook on save
	 *
	 * @since 3.2.0
	 * @see \WC_Settings_Page::save()
	 */
	public function save() {

		add_filter( 'wc_freshbooks_settings', array( $this, 'fb_payment_type_mapping_filter' ) );

		parent::save();

		remove_filter( 'wc_freshbooks_settings', array( $this, 'fb_payment_type_mapping_filter' ) );

		$this->maybe_create_webhook();
	}


	/**
	 * Replaces the `payment_type_mapping` option type with `multiselect` to avoid an array-to-string conversion error.
	 *
	 * @internal
	 *
	 * @since 3.6.0
	 *
	 * @param array $settings
	 * @return array
	 */
	public function fb_payment_type_mapping_filter( $settings ) {

		foreach ( $settings as &$setting ) {
			if ( 'payment_type_mapping' === $setting['type'] ) {
				$setting['type'] = 'multiselect';
			}
		}

		unset( $setting );

		return $settings;
	}


	/**
	 * Creates a webhook for invoice/client/item events.
	 *
	 * @since 3.0
	 */
	private function maybe_create_webhook() {

		try {

			// valid API credentials are required
			if ( is_object( wc_freshbooks()->get_api() ) && ! get_option( 'wc_freshbooks_webhook_id' ) ) {

				$webhook_id = wc_freshbooks()->get_api()->create_webhook();

				update_option( 'wc_freshbooks_webhook_id', $webhook_id );
			}

		} catch ( Framework\SV_WC_API_Exception $e ) {

			wc_freshbooks()->log( sprintf( '%1$s - %2$s', $e->getCode(), $e->getMessage() ) );
		}
	}


	/**
	 * Get settings array
	 *
	 * @since 3.0
	 * @return array
	 */
	public function get_settings() {

		list( $default_site_language ) = explode( '_', get_locale() );

		$api_settings = array(

			array( 'name' => __( 'API Settings', 'woocommerce-freshbooks' ), 'type' => 'title' ),

			// API URL
			array(
				'id'       => 'wc_freshbooks_api_url',
				'name'     => __( 'API URL', 'woocommerce-freshbooks' ),
				'desc_tip' => __( 'Enter the API URL shown on the FreshBooks API page.', 'woocommerce-freshbooks' ),
				'type'     => 'text',
			),

			// authentication token
			array(
				'id'       => 'wc_freshbooks_authentication_token',
				'name'     => __( 'Authentication Token', 'woocommerce-freshbooks' ),
				'desc_tip' => __( 'Enter the Authentication token shown on the FreshBooks API page.', 'woocommerce-freshbooks' ),
				'type'     => 'password',
			),

			// debug mode
			array(
				'id'       => 'wc_freshbooks_debug_mode',
				'name'     => __( 'Debug Mode', 'woocommerce-freshbooks' ),
				'desc_tip' => __( 'This logs API requests/responses to the WooCommerce log. Please only enable this if you are having issues.', 'woocommerce-freshbooks' ),
				'default'  => 'off',
				'type'     => 'select',
				'options'  => array(
					'off' => __( 'Off', 'woocommerce-freshbooks' ),
					'on'  => __( 'Save to Log', 'woocommerce-freshbooks' ),
				),
			),

			array( 'type' => 'sectionend' ),

		);

		$invoice_settings = array(

			array( 'name' => __( 'Invoice Settings', 'woocommerce-freshbooks' ), 'type' => 'title' ),

			// default client
			array(
				'id'       => 'wc_freshbooks_default_client',
				'name'     => __( 'Default Client', 'woocommerce-freshbooks' ),
				'desc_tip' => __( 'Select a client to create all invoices under, or select "none" to create a new client for each customer.' ),
				'type'     => 'select',
				'options'  => $this->get_active_clients(),
				'default'  => 'none',
				'css'      => 'max-width: 400px;',
				'class'    => 'wc-enhanced-select',
			),

			// default language
			array(
				'id'       => 'wc_freshbooks_invoice_language',
				'name'     => __( 'Default Language', 'woocommerce-freshbooks' ),
				'desc_tip' => __( 'Chose the default language used for invoices. This defaults to your site language.' ),
				'type'     => 'select',
				'options'  => $this->get_available_languages(),
				'default'  => $default_site_language,
				'css'      => 'max-width: 400px;',
				'class'    => 'wc-enhanced-select',
			),

			// invoice sending method
			array(
				'id'       => 'wc_freshbooks_invoice_sending_method',
				'name'     => __( 'Send Invoices By', 'woocommerce-freshbooks' ),
				'desc_tip' => __( 'Select how to send invoices. If you select Snail Mail, you must have enough stamps in your FreshBooks account.', 'woocommerce-freshbooks' ),
				'type'     => 'select',
				'options'  => array(
					'email'      => __( 'Email', 'woocommerce-freshbooks' ),
					'snail_mail' => __( 'Snail Mail', 'woocommerce-freshbooks' ),
				),
				'default'  => 'email',
			),

			// use order number as invoice number
			array(
				'id'      => 'wc_freshbooks_use_order_number',
				'name'    => __( 'Use Order Number as Invoice Number', 'woocommerce-freshbooks' ),
				'desc'    => __( 'Enable this to use the order number as the invoice number. Disable to allow FreshBooks to auto-generate the invoice number.', 'woocommerce-freshbooks' ),
				'type'    => 'checkbox',
				'default' => 'yes',
			),

			// invoice number prefix
			array(
				'id'       => 'wc_freshbooks_invoice_number_prefix',
				'name'     => __( 'Invoice Number Prefix', 'woocommerce-freshbooks' ),
				'desc_tip' => __( 'Enter a prefix for the invoice number or leave blank to not use a prefix.', 'woocommerce-freshbooks' ),
				'type'     => 'text',
			),

			// display on My Account page
			array(
				'id'      => 'wc_freshbooks_display_view_invoice_my_account',
				'name'    => __( 'Display Link in Recent Orders', 'woocommerce-freshbooks' ),
				'desc'    => __( 'Enable this to display a "View Invoice" link for customers on the My Account page.', 'woocommerce-freshbooks' ),
				'type'    => 'checkbox',
				'default' => 'yes',
			),

			array( 'type' => 'sectionend' ),

		);

		$invoice_workflow_settings = array(
			array( 'name' => __( 'Invoice Workflow', 'woocommerce-freshbooks' ), 'type' => 'title' ),

			// automatically create invoices
			array(
				'id'      => 'wc_freshbooks_auto_create_invoices',
				'name'    => __( 'Automatically Create Invoices', 'woocommerce-freshbooks' ),
				'desc'    => __( 'Enable this to automatically create invoices for new orders, otherwise invoices must be created manually.', 'woocommerce-freshbooks' ),
				'default' => 'yes',
				'type'    => 'checkbox',
			),

			// automatically send invoices
			array(
				'id'      => 'wc_freshbooks_auto_send_invoices',
				'name'    => __( 'Automatically Send Invoices', 'woocommerce-freshbooks' ),
				'desc'    => __( 'Enable this to automatically send invoices after creation. Disable to leave created invoices as drafts.', 'woocommerce-freshbooks' ),
				'default' => 'yes',
				'type'    => 'checkbox',
			),

			// apply payments to invoice
			array(
				'id'      => 'wc_freshbooks_auto_apply_payments',
				'name'    => __( 'Automatically Apply Payments', 'woocommerce-freshbooks' ),
				'desc'    => __( 'Enable this to automatically apply invoice payments for orders that have received payment.', 'woocommerce-freshbooks' ),
				'type'    => 'checkbox',
				'default' => 'yes',
			),

			// WC gateway -> FreshBooks payment method settings
			array(
				'id'      => 'wc_freshbooks_payment_type_mapping',
				'name'    => __( 'Payment Gateway Settings', 'woocommerce-freshbooks' ),
				'type'    => 'payment_type_mapping',
			),

			array( 'type' => 'sectionend' ),

		);


		if ( wc_prices_include_tax() ) {
			$send_tax_inclusive_price = array(
				'id'      => 'wc_freshbooks_send_tax_inclusive_price',
				'name'    => __( 'Send price inclusive of tax', 'woocommerce-freshbooks' ),
				'desc'    => __( 'Enable to send prices inclusive of tax. Disable to send tax as a separate line.', 'woocommerce-freshbooks' ),
				'type'    => 'checkbox',
				'default' => 'no',
			);

			// Add setting second from the last so it's before the `secionend` part.
			array_splice( $invoice_settings, count( $invoice_settings ) - 2, 1, array( $send_tax_inclusive_price ) );
		}

		$settings = array_merge( $api_settings, $invoice_settings, $invoice_workflow_settings );

		return apply_filters( 'wc_freshbooks_settings', $settings );
	}


	/**
	 * Outputs the FreshBooks payment type settings table.
	 *
	 * @since 3.6.0
	 */
	public function render_payment_type_mapping_table() {
		?>
		<tr valign="top">

			<th scope="row" class="titledesc"><?php esc_html_e( 'Payment Type Settings', 'woocommerce-freshbooks' ); ?></th>

			<td class="forminp">
				<table class="wc_gateways widefat" cellspacing="0">

					<thead>
						<?php $this->render_payment_type_mapping_table_header(); ?>
					</thead>

					<tbody>
						<?php $this->render_payment_type_mapping_table_body(); ?>
					</tbody>

				</table>
			</td>
		</tr>
		<?php
	}


	/**
	 * Outputs the FreshBooks payment type settings table header.
	 *
	 * @since 3.6.0
	 */
	private function render_payment_type_mapping_table_header() {
		?>
		<tr valign="top">

			<th class="status"></th>

			<th class="wc-gateway">
				<?php esc_html_e( 'WooCommerce Gateway', 'woocommerce-freshbooks' ); ?>
			</th>

			<th class="fb-method">
				<?php esc_html_e( 'FreshBooks Payment Type', 'woocommerce-freshbooks' ); ?>
			</th>

		</tr>
		<?php
	}


	/**
	 * Outputs the FreshBooks payment type settings table body.
	 *
	 * @since 3.6.0
	 */
	private function render_payment_type_mapping_table_body() {

		foreach ( WC()->payment_gateways()->payment_gateways() as $gateway_id => $gateway ) :
			?>
			<tr valign="top" data-woocommerce-gateway-id="<?php echo esc_attr( $gateway_id ); ?>">

				<td class="status" width="8%">
					<?php if ( 'yes' === $gateway->enabled ) : ?>
						<span class="status-enabled tips" data-tip="<?php esc_attr_e( 'Gateway Enabled', 'woocommerce-freshbooks' ); ?>"></span>
					<?php else : ?>
						<span>-</span>
					<?php endif; ?>
				</td>

				<td class="id">
					<?php echo esc_html( $gateway->method_title ); ?>
				</td>

				<td class="method">
					<?php $this->render_payment_type_select( $gateway_id ); ?>
				</td>

			</tr>
			<?php
		endforeach;
	}


	/**
	 * Render a select box showing all available payment types.
	 *
	 * @since 3.6.0
	 * @param $gateway_id string payment gateway ID
	 */
	private function render_payment_type_select( $gateway_id ) {

		$slug     = sanitize_title( $gateway_id );
		$options  = array();
		$types    = $this->get_payment_types();
		$settings = wc_freshbooks()->get_fb_payment_type_mapping();
		$selected = 'none';

		if ( isset( $settings[ $slug ] ) && ! empty( $settings[ $slug ] ) ) {
			$selected = $settings[ $slug ];
		}

		foreach ( $types as $type ) {
			$options[ $type ] = $type;
		}

		$dropdown_name = sprintf(
			'wc_freshbooks_payment_type_mapping[%s]',
			$slug
		);

		$dropdown_args = array(
			'id'                => 'wc_freshbooks_payment_type_mapping',
			'type'              => 'select',
			'options'           => $options,
			'custom_attributes' => array(
				'style' => 'width: 90%;',
			),
		);

		woocommerce_form_field(
			$dropdown_name,
			$dropdown_args,
			$selected
		);
	}


	/**
	 * Gets active clients for the FreshBooks account.
	 *
	 * Note the API call does not paginate so this is limited to 100 active clients which should be sufficient.
	 * Active clients are cached for 5 minutes.
	 *
	 * @since 3.0
	 *
	 * @return array
	 */
	private function get_active_clients() {

		$active_clients = array( 'none' => __( 'None', 'woocommerce-freshbooks' ) );

		// during the initial install, this is sometimes not a proper object
		// which throws fatal errors when trying to call get_api()
		if ( ! is_object( wc_freshbooks() ) ) {
			return $active_clients;
		}

		try {

			foreach ( wc_freshbooks()->get_api()->get_active_clients() as $client ) {

				$active_clients[ $client['client_id'] ] = sprintf( '%1$s (%2$s)', $client['name'], $client['email'] );
			}

		} catch ( Framework\SV_WC_API_Exception $e ) {

			wc_freshbooks()->log( $e->getMessage() );
		}

		return $active_clients;
	}


	/**
	 * Gets available invoice languages via FreshBooks API.
	 *
	 * Languages are cached for 5 minutes.
	 *
	 * @since 3.0
	 *
	 * @return array
	 */
	private function get_available_languages() {

		$invoice_languages = array();

		// during the initial install, this is sometimes not a proper object
		// which throws fatal errors when trying to call get_api()
		if ( ! is_object( wc_freshbooks() ) ) {
			return $invoice_languages;
		}

		try {

			$invoice_languages = wc_freshbooks()->get_api()->get_languages();

		} catch ( Framework\SV_WC_API_Exception $e ) {

			wc_freshbooks()->log( $e->getMessage() );
		}

		return $invoice_languages;
	}


	/**
	 * Returns all FreshBooks payment methods.
	 *
	 * @since 3.6.0
	 * @return array FreshBooks payment methods.
	 */
	private function get_payment_types() {

		return array(
			'None',
			'ACH',
			'Bank Transfer',
			'Cash',
			'Check',
			'Credit',
			'Credit Card',
			'Debit',
			'VISA',
			'MASTERCARD',
			'DISCOVER',
			'NOVA',
			'AMEX',
			'DINERS',
			'EUROCARD',
			'JCB',
			'PayPal',
			'2Checkout',
		);
	}

}

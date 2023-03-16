<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles settings for Box Office.
 *
 * @since 1.1.0
 */
class WC_Box_Office_Settings {

	private $settings_tab_id = 'box_office';

	public function __construct() {
		add_action( 'woocommerce_settings_tabs_array', array( $this, 'add_woocommerce_settings_tab' ), 50 );
		add_action( 'woocommerce_settings_tabs_' . $this->settings_tab_id, array( $this, 'woocommerce_settings_tab_action' ), 10 );
		add_action( 'woocommerce_update_options_' . $this->settings_tab_id, array( $this, 'woocommerce_settings_save' ), 10 );
	}

	/**
	 * Display admin warning if Box Office emails enabled but the corresponding WC_Email is disabled
	 *
	 * @param string $inline Whether to keep notice inline if the string equals 'inline'.
	 * @return void
	 */
	public static function display_warning( $inline = '' ) {
		$inline = 'inline' === $inline ? 'inline' : '';

		?>
		<div class="notice notice-warning <?php echo $inline; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Strict assignment ?>">
			<p>
				<?php
				printf(
					/* translators: link to email settings page */
					wp_kses_post( __( '<strong>WooCommerce Box Office</strong><br />Warning: E-mails won\'t be sent because Box Office e-mails are disabled. Go to <a href="%s">WooCommerce > Settings > Emails > Box Office Emails</a> to enable them.', 'woocommerce-box-office' ) ),
					esc_url( admin_url( 'admin.php?page=wc-settings&tab=email&section=wc_box_office_email' ) )
				);
				?>
			</p>
		</div>
		<?php
	}

	/**
	 * Add settings tab to woocommerce
	 */
	public function add_woocommerce_settings_tab( $settings_tabs ) {
		$settings_tabs[ $this->settings_tab_id ] = __( 'Box Office', 'woocommerce-box-office' );
		return $settings_tabs;
	}

	/**
	 * Do this when viewing our custom settings tab(s). One function for all tabs.
	 */
	public function woocommerce_settings_tab_action() {
		$email_settings    = get_option( 'woocommerce_wc_box_office_email_settings', array( 'enabled' => 'no' ) );
		$wc_emails_enabled = 'yes' === $email_settings['enabled'];
		$bo_emails_enabled = 'yes' === get_option( 'box_office_enable_ticket_emails', 'no' );

		if ( $bo_emails_enabled && ! $wc_emails_enabled ) {
			self::display_warning();
		}

		woocommerce_admin_fields( $this->get_settings() );
	}

	/**
	 * Save settings in a single field in the database for each tab's fields (one field per tab).
	 */
	public function woocommerce_settings_save() {
		woocommerce_update_options( $this->get_settings() );
	}

	/**
	 * Returns settings array.
	 *
	 * @return array settings
	 */
	public function get_settings() {
		$settings = array(
			array(
				'name' => __( 'Ticket Pages', 'woocommerce-box-office' ),
				'type' => 'title',
				'desc' => '',
				'id'   => 'box_office_ticket_pages_title'
			),
			array(
				'title'    => __( 'My Ticket Page', 'woocommerce-box-office' ),
				'desc'     => __( 'Page contents:', 'woocommerce-box-office' ) . ' [my_ticket]',
				'id'       => 'box_office_my_ticket_page_id',
				'type'     => 'single_select_page',
				'default'  => '',
				'class'    => 'wc-enhanced-select-nostd',
				'css'      => 'min-width:300px;',
				'desc_tip' => true,
			),
			array( 'type' => 'sectionend', 'id' => 'box_office_ticket_pages_title' ),

			array(
				'name' => __( 'Display Options', 'woocommerce-box-office' ),
				'type' => 'title',
				'desc' => '',
				'id'   => 'box_office_ticket_display_options_title'
			),
			array(
				'title'       => __( 'Add to Cart Button Text', 'woocommerce-box-office' ),
				'id'          => 'box_office_add_to_cart_text',
				'default'     => '',
				'placeholder' => __( 'Ticket Detail', 'woocommerce-box-office' ),
				'type'        => 'text',
				'desc_tip'    => __( 'Define text inside add to cart button in products archive.', 'woocommerce-box-office' ),
			),
			array(
				'title'       => __( 'Ticket Title Prefix', 'woocommerce-box-office' ),
				'id'          => 'box_office_ticket_title_prefix',
				'default'     => '',
				'placeholder' => __( 'Ticket #', 'woocommerce-box-office' ),
				'type'        => 'text',
				'desc_tip'    => __( 'Define title prefix to show for each ticket to buy in a single product page.', 'woocommerce-box-office' ),
			),
			array(
				'title'       => __( 'Buy Ticket Button Text Singular', 'woocommerce-box-office' ),
				'id'          => 'box_office_add_to_cart_singular',
				'default'     => '',
				'placeholder' => __( 'Buy Ticket Now', 'woocommerce-box-office' ),
				'type'        => 'text',
				'desc_tip'    => __( 'Define text inside buy ticket button when qty of ticket to buy is one.', 'woocommerce-box-office' ),
			),
			array(
				'title'       => __( 'Buy Ticket Button Button Text Plural', 'woocommerce-box-office' ),
				'id'          => 'box_office_add_to_cart_plural',
				'default'     => '',
				'placeholder' => __( 'Buy Tickets Now', 'woocommerce-box-office' ),
				'type'        => 'text',
				'desc_tip'    => __( 'Define text inside buy ticket button when qty of ticket to buy is more than one.', 'woocommerce-box-office' ),
			),
			array( 'type' => 'sectionend', 'id' => 'box_office_ticket_display_options_title' ),

			array(
				'name' => __( 'Default Product Settings', 'woocommerce-box-office' ),
				'type' => 'title',
				'desc' => '',
				'id'   => 'box_office_default_product_settings_title',
			),
			array(
				'name'    => __( 'Enable Ticket Printing', 'woocommerce-box-office' ),
				'type'    => 'checkbox',
				'default' => 'no',
				'id'      => 'box_office_enable_ticket_printing',
				'desc'    => __( 'This will enable the \'Print ticket\' button on the ticket edit page.', 'woocommerce-box-office' ),
			),
			array(
				'name'    => __( 'Enable Ticket Emails', 'woocommerce-box-office' ),
				'type'    => 'checkbox',
				'default' => 'no',
				'desc'    => __( 'This will send an email to the contact address for each ticket whenever it is changed.', 'woocommerce-box-office' ),
				'id'      => 'box_office_enable_ticket_emails',
			),
			array(
				'name'    => __( 'Disable Ticket Editing', 'woocommerce-box-office' ),
				'type'    => 'checkbox',
				'default' => 'no',
				'desc'    => __( 'This will prevent customers from editing their purchased tickets.', 'woocommerce-box-office' ),
				'id'      => 'box_office_disable_edit_tickets',
			),
			array( 'type' => 'sectionend', 'id' => 'box_office_default_product_settings_title' ),

			array(
				'name' => __( 'Logging', 'woocommerce-box-office' ),
				'type' => 'title',
				'desc' => '',
				'id'   => 'box_office_logging_title',
			),
			array(
				'name'     => __( 'Enable Logging', 'woocommerce-box-office' ),
				'type'     => 'checkbox',
				'default'  => 'no',
				'desc'     => __( 'Save debug messages to the WooCommerce System Status log.', 'woocommerce-box-office' ),
				'desc_tip' => __( 'Note: this may log personal information. We recommend using this for debugging purposes only and deleting the logs when finished.', 'woocommerce-box-office' ),
				'id'       => 'box_office_enable_logging',
			),
			array( 'type' => 'sectionend', 'id' => 'box_office_logging_title' ),
		);

		return apply_filters( 'woocommerce_box_office_get_settings', $settings );
	}
}

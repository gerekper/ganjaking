<?php
/**
 * WC_Settings_Restrictions class
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  WooCommerce Conditional Shipping and Payments
 * @since    1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WC_Settings_Restrictions' ) ) :

/**
 * WooCommerce Global Restriction Settings.
 *
 * @version  1.5.0
 */
class WC_Settings_Restrictions extends WC_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {

		$this->id    = 'restrictions';
		$this->label = __( 'Restrictions', 'woocommerce-conditional-shipping-and-payments' );

		// Add settings page.
		add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
		// Output sections.
		add_action( 'woocommerce_sections_' . $this->id, array( $this, 'output_sections' ) );
		// Output content.
		add_action( 'woocommerce_settings_' . $this->id, array( $this, 'output' ) );
		// Process + save data.
		add_action( 'woocommerce_settings_save_' . $this->id, array( $this, 'save' ) );
		// Render "overview" field :)
		add_action( 'woocommerce_admin_field_wccsp_restrictions_overview', array( $this, 'restrictions_overview' ) );
		// Delete hook.
		add_action( 'woocommerce_settings_page_init', array( $this, 'delete' ) );
	}

	/**
	 * Get sections.
	 *
	 * @return array
	 */
	public function get_sections() {

		$restrictions = WC_CSP()->restrictions->get_admin_global_field_restrictions();

		$sections = array(
			'' => __( 'Restrictions', 'woocommerce' )
		);

		foreach ( $restrictions as $restriction_id => $restriction ) {
			$sections[ $restriction_id ] = esc_html( $restriction->get_title() );
		}

		return apply_filters( 'woocommerce_csp_get_sections_' . $this->id, $sections );
	}

	/**
	 * Get settings array.
	 *
	 * @return array
	 */
	public function get_settings() {

		return apply_filters( 'woocommerce_csp_settings', array(

			array(
				'title' => __( 'Restrictions', 'woocommerce-conditional-shipping-and-payments' ),
				'type'  => 'title',
				'desc'  => __( 'Use Restrictions to conditionally exclude Payment Gateways, Shipping Methods and Shipping Countries or States.', 'woocommerce-conditional-shipping-and-payments' ),
				'id'    => 'restriction_options'
			),

			array(
				'type'  => 'wccsp_restrictions_overview'
			),

			array( 'type' => 'sectionend', 'id' => 'global_restriction_options' ),

			array(
				'title' => __( 'Debug Options', 'woocommerce-conditional-shipping-and-payments' ),
				'type'  => 'title',
				'desc'  => __( 'Use these options to troubleshoot your payment and shipping settings.', 'woocommerce-conditional-shipping-and-payments' ),
				'id'    => 'wccsp_restrictions_debug'
			),

			array(
				'title'         => __( 'Disable Global Restrictions', 'woocommerce-conditional-shipping-and-payments' ),
				'desc'          => __( 'Disable all global restrictions', 'woocommerce-conditional-shipping-and-payments' ),
				'id'            => 'wccsp_restrictions_disable_global',
				'default'       => 'no',
				'type'          => 'checkbox',
				'checkboxgroup' => 'start',
				'desc_tip'      => __( 'Disable all restrictions created in the <strong>Payment Gateways</strong>, <strong>Shipping Methods</strong> and <strong>Shipping Countries &amp; States</strong> tab sections above.', 'woocommerce-conditional-shipping-and-payments' ),
			),

			array(
				'title'         => __( 'Disable Product Restrictions', 'woocommerce-conditional-shipping-and-payments' ),
				'desc'          => __( 'Disable all product-level restrictions', 'woocommerce-conditional-shipping-and-payments' ),
				'id'            => 'wccsp_restrictions_disable_product',
				'default'       => 'no',
				'type'          => 'checkbox',
				'checkboxgroup' => 'start',
				'desc_tip'      => __( 'Disable all restrictions created from the <strong>Product Data > Restrictions</strong> tab of your products.', 'woocommerce-conditional-shipping-and-payments' ),
			),

			array( 'type' => 'sectionend', 'id' => 'global_restriction_debug_options' ),

		) );
	}

	/**
	 * Output the settings.
	 * @return void
	 */
	public function output() {

		global $current_section;

		// Define restrictions that can be customised here.
		if ( $current_section ) {

			$restriction = WC_CSP()->restrictions->get_restriction( $current_section );

			if ( $restriction ) {
				$GLOBALS[ 'hide_save_button' ] = true;
				$restriction->admin_options();
			}

		} else {

			$settings = $this->get_settings();

			WC_Admin_Settings::output_fields( $settings );
		}
	}

	/**
	 * Save settings.
	 * @return void
	 */
	public function save() {

		global $current_section;

		if ( ! $current_section ) {

			$settings = $this->get_settings();
			WC_Admin_Settings::save_fields( $settings );

		} else {

			do_action( 'woocommerce_update_options_' . $this->id . '_' . $current_section );
		}

		// Clear cached shipping rates.
		WC_CSP_Core_Compatibility::clear_cached_shipping_rates();

		if ( empty( WC_CSP_Admin_Notices::$meta_box_notices ) ) {
			WC_CSP_Admin_Notices::add_notice( __( 'Your settings have been saved.', 'woocommerce' ), 'success', true );
		}

		wp_redirect( remove_query_arg( 'add_rule' ) );
		exit;
	}

	/**
	 * Delete restriction rule.
	 *
	 * @since  1.4.0
	 * @return void
	 */
	public function delete() {

		if ( ! isset( $_GET[ 'delete_rule' ], $_GET[ 'restriction_id' ] ) ) {
			return;
		}

		// Security.
		$delete_nonce = isset( $_GET[ 'delete_nonce' ] ) ? wc_clean( $_GET[ 'delete_nonce' ] ) : false;

		if ( ! $delete_nonce || ! wp_verify_nonce( $delete_nonce, 'wc-csp-delete-rule-nonce' ) ) {
			WC_CSP_Admin_Notices::add_notice( __( 'Failed to delete restriction rule. Please refresh the page and try again.', 'woocommerce-conditional-shipping-and-payments' ), 'error', true );
			$this->reload_overview();
		}

		$delete_rule    = is_numeric( $_GET[ 'delete_rule' ] ) ? absint( $_GET[ 'delete_rule' ] ) : -1;
		$restriction_id = wc_clean( $_GET[ 'restriction_id' ] );

		// Get the restriction object.
		$restriction_obj = WC_CSP()->restrictions->get_restriction( $restriction_id );

		if ( ! $restriction_obj ) {
			$this->reload_overview();
		}

		if ( $restriction_obj->delete_global_restriction_rule( $delete_rule ) ) {
			WC_CSP_Admin_Notices::add_notice( __( 'Restriction rule deleted successfully.', 'woocommerce-conditional-shipping-and-payments' ), 'success', true );
		}

		// Redirect.
		$this->reload_overview();
	}

	/**
	 * Redirect to Restrictions tab.
	 *
	 * @since  1.4.0
	 * @return void
	 */
	protected function reload_overview() {
		wp_redirect( admin_url( 'admin.php?page=wc-settings&tab=restrictions' ) );
		exit;
	}

	/**
	 * Filter on restriction array for enabled rules.
	 *
	 * @return boolean
	 */
	protected function is_enabled_filter( $rule ) {

		if ( is_array( $rule ) && isset( $rule[ 'enabled' ] ) ) {
			return ( 'yes' === $rule[ 'enabled' ] );
		}

		return true;
	}

	/**
	 * Output restrictions overview table.
	 *
	 * @return void
	 */
	public function restrictions_overview() {

		$restrictions     = WC_CSP()->restrictions->get_admin_global_field_restrictions();
		$restriction_data = get_option( 'wccsp_restrictions_global_settings', array() );

		?>
		<tr valign="top">
			<th scope="row" class="titledesc"><?php _e( 'Restrictions Overview', 'woocommerce-conditional-shipping-and-payments' ) ?></th>
			<td class="forminp <?php echo WC_CSP_Core_Compatibility::get_versions_class(); ?>">
				<table class="wc_shipping wc_restrictions_overview widefat wp-list-table" cellspacing="0">
					<thead>
						<tr>
							<th class="name"><?php _e( 'Restriction Type', 'woocommerce-conditional-shipping-and-payments' ); ?></th>
							<th class="status"><?php _e( 'Active Rules', 'woocommerce-conditional-shipping-and-payments' ); ?></th>
							<th class="summary"><?php _e( 'Summary', 'woocommerce-conditional-shipping-and-payments' ); ?></th>
							<th class="actions"><?php _e( 'Actions', 'woocommerce-conditional-shipping-and-payments' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $restrictions as $restriction_id => $restriction ) {

							$rules = $restriction->get_global_restriction_data( 'edit' );

							// Check if a NUX version needed.
							if ( empty( $restriction_data ) ) { ?>
								<tr>
									<td colspan="4" class="on_boarding <?php echo $restriction_id; ?>">
										<div class="on_boarding__container">
											<div class="information">
												<a class="title" href="<?php echo esc_url( admin_url( 'admin.php?page=wc-settings&tab=restrictions&section=' . $restriction_id ) ); ?>">
													<?php echo esc_html( $restriction->get_title() ); ?>
												</a>
												<p>
													<?php
													if ( 'payment_gateways' === $restriction_id ) {
														echo __( 'Restrict the payment gateways available at checkout.', 'woocommerce-conditional-shipping-and-payments' );
													} elseif ( 'shipping_methods' === $restriction_id ) {
														echo __( 'Restrict the shipping methods available at checkout.', 'woocommerce-conditional-shipping-and-payments' );
													} elseif ( 'shipping_countries' === $restriction_id ) {
														echo __( 'Restrict the shipping countries allowed at checkout.', 'woocommerce-conditional-shipping-and-payments' );
													}
													?>
												</p>
											</div>
											<a class="action" href="<?php echo esc_url( admin_url( 'admin.php?page=wc-settings&tab=restrictions&section=' . $restriction_id . '&add_rule=1' ) ); ?>" aria-label="Add Restriction">Add restriction</a>
										</div>
									</td>
								</tr>
							<?php } else { ?>
								<tr>
									<td class="name">
										<a href="<?php echo esc_url( admin_url( 'admin.php?page=wc-settings&tab=restrictions&section=' . $restriction_id ) ); ?>">
											<?php echo esc_html( $restriction->get_title() ); ?>
										</a>
									</td>
									<td class="status">
										<?php echo count( array_filter( $rules, array( $this, 'is_enabled_filter' ) ) ) . '/' . count( $rules ); ?>
									</td>
									<td class="summary" colspan="2">
										<table>
											<tbody><?php

												if ( ! empty( $rules ) ) {

													$delete_nonce = wp_create_nonce( 'wc-csp-delete-rule-nonce' );

													foreach ( $rules as $rule_key => $rule ) {
														?><tr>
															<td class="column-wccsp_title"><?php echo $restriction->get_options_description( $rule ); ?></td>
															<td class="column-wc_actions">
																<a class="button wc-action-button wccsp-edit-restriction-rule edit" title="Edit" href="<?php echo esc_url( admin_url( 'admin.php?page=wc-settings&tab=restrictions&section=' . $restriction_id . '&view_rule=' . $rule_key ) ); ?>" aria-label="Edit"></a>
																<a class="button wc-action-button wccsp-delete-restriction-rule delete" title="Delete" href="<?php echo esc_url( admin_url( 'admin.php?page=wc-settings&tab=restrictions&restriction_id=' . $restriction_id . '&delete_rule=' . $rule_key . '&delete_nonce=' . $delete_nonce ) ); ?>" aria-label="Delete"></a>
															</td>
														</tr><?php
													}
												}
												?><tr>
													<td class="column-wccsp_title"></td>
													<td class="column-wc_actions">
														<a class="button wc-action-button wccsp-add-restriction-rule add" title="Add" href="<?php echo esc_url( admin_url( 'admin.php?page=wc-settings&tab=restrictions&section=' . $restriction_id . '&add_rule=1' ) ); ?>" aria-label="Add"></a>
													</td>
												</tr>
											</tbody>
										</table>
									</td>
								</tr>
							<?php
							}
						}
					?></tbody>
				</table>
			</td>
		</tr>
		<?php
	}
}

endif;

return new WC_Settings_Restrictions();

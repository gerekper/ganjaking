<?php
/**
 * WooCommerce Local Pickup Plus
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Local Pickup Plus to newer
 * versions in the future. If you wish to customize WooCommerce Local Pickup Plus for your
 * needs please refer to http://docs.woocommerce.com/document/local-pickup-plus/
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2012-2021, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Pickup Location Data Meta Box.
 *
 * @since 2.0.0
 */
class WC_Local_Pickup_Plus_Meta_Box_Pickup_Location_Data extends \WC_Local_Pickup_Plus_Meta_Box {


	/** @var \WC_Local_Pickup_Plus_Pickup_Location the pickup location where this meta box appears */
	private $pickup_location;


	/**
	 * Meta box constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		$this->id       = 'wc-local-pickup-plus-pickup-location-data';
		$this->priority = 'high';
		$this->screens  = array( 'wc_pickup_location' );

		parent::__construct();
	}


	/**
	 * Get the meta box title.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'Pickup Location Data', 'woocommerce-shipping-local-pickup-plus' );
	}


	/**
	 * Get the meta box tabs.
	 *
	 * @since 2.0.0
	 *
	 * @param int $post_id the pickup location and post id where the tabs appear
	 * @return array associative array of tab keys and properties
	 */
	public function get_tabs( $post_id = 0 ) {

		$tabs = array(

			'address' => array(
				'label'  => __( 'Address', 'woocommerce-shipping-local-pickup-plus' ),
				'target' => 'pickup-location-address',
				'class'  => array( 'active' ),
			),

			'products' => array(
				'label'  => __( 'Available Products', 'woocommerce-shipping-local-pickup-plus' ),
				'target' => 'pickup-location-products',
			),

			'price_adjustment' => array(
				'label'  => __( 'Costs &amp; Discounts', 'woocommerce-shipping-local-pickup-plus' ),
				'target' => 'pickup-location-price-adjustment',
			),

			'pickup_appointments' => array(
				'label'  => __( 'Pickup Appointments', 'woocommerce-shipping-local-pickup-plus' ),
				'target' => 'pickup-location-appointments',
			),

			'emails' => array(
				'label'  => __( 'Email List', 'woocommerce-shipping-local-pickup-plus' ),
				'target' => 'pickup-location-emails',
			),

		);

		return (array) apply_filters( 'wc_local_pickup_plus_pickup_location_data_tabs', $tabs, $post_id );
	}


	/**
	 * Output the meta box HTML.
	 *
	 * @since 2.0.0
	 *
	 * @param \WP_Post $post the post object where the meta box appears
	 */
	public function output( \WP_Post $post ) {

		$this->pickup_location = new \WC_Local_Pickup_Plus_Pickup_Location( $post );

		?>
		<div class="panel-wrap data">

			<?php $tabs   = $this->get_tabs( $post->ID ); ?>
			<?php $panels = ! empty( $tabs ) ? array_keys( $tabs ) : array(); ?>

			<?php if ( ! empty( $tabs ) && ! empty( $panels ) ) : ?>

				<ul class="pickup_location_data_tabs wc-tabs">
					<?php foreach ( $tabs as $key => $tab ) : ?>

						<?php $class = isset( $tab['class'] ) ? $tab['class'] : array(); ?>
						<li class="<?php echo sanitize_html_class( $key ); ?>_options <?php echo sanitize_html_class( $key ); ?>_tab <?php echo implode( ' ' , array_map( 'sanitize_html_class', $class ) ); ?>">
							<a href="#<?php echo esc_attr( $tab['target'] ); ?>"><span><?php echo esc_html( $tab['label'] ); ?></span></a>
						</li>

					<?php endforeach; ?>
				</ul>

				<?php foreach ( $panels as $panel_name ) : ?>

					<?php $panel = "output_{$panel_name}_panel"; ?>

					<?php if ( method_exists( $this, $panel ) ) : ?>
						<?php $this->$panel(); ?>
					<?php endif; ?>

				<?php endforeach; ?>

			<?php endif; ?>

			<div class="clear"></div>
		</div>
		<?php
	}


	/**
	 * Get address field pieces.
	 *
	 * @since 2.0.0
	 *
	 * @return array associative array of keys and labels
	 */
	private function get_address_pieces() {
		return array(
			'country'   => __( 'Country',        'woocommerce-shipping-local-pickup-plus' ),
			'state'     => __( 'State',          'woocommerce-shipping-local-pickup-plus' ),
			'address_1' => __( 'Address Line 1', 'woocommerce-shipping-local-pickup-plus' ),
			'address_2' => __( 'Address Line 2', 'woocommerce-shipping-local-pickup-plus' ),
			'city'      => __( 'City',           'woocommerce-shipping-local-pickup-plus' ),
			'postcode'  => __( 'Postcode',       'woocommerce-shipping-local-pickup-plus'),
		);
	}


	/**
	 * Output the address and location notes panel.
	 *
	 * @since 2.0.0
	 */
	private function output_address_panel() {

		?>
		<div id="pickup-location-address" class="panel woocommerce_options_panel">

			<div class="options_group wc-local-pickup-plus-address-fields">
				<h4><?php esc_html_e( 'Address', 'woocommerce-shipping-local-pickup-plus' ); ?></h4>

				<?php foreach ( $this->get_address_pieces() as $key => $label ) : ?>

					<?php if ( 'country' === $key ) : ?>

						<?php

						$country = $this->pickup_location->get_address()->get_country();

						if ( empty( $country ) ) {
							$location = wc_get_base_location();
							$country  = $location['country'];
							$state    = $location['state'];
						} else {
							$state = $this->pickup_location->get_address()->get_state();
						}

						?>
						<p class="form-field">
							<label for="_<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label>
							<select
								id="_<?php echo esc_attr( $key ); ?>"
								name="_<?php echo esc_attr( $key ); ?>"
								style="width:100%;max-width:360px;"
								class="wc-enhanced-select">
								<?php WC()->countries->country_dropdown_options( $country, empty( $state ) ? '*' : $state ); ?>
							</select>
						</p>

					<?php elseif ( 'state' === $key ) : continue; ?>
					<?php else : ?>

						<p class="form-field">
							<label for="_<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label>
							<input
								type="text"
								id="_<?php echo esc_attr( $key ); ?>"
								name="_<?php echo esc_attr( $key ); ?>"
								class="<?php echo esc_attr( $key ); ?>"
								value="<?php echo esc_attr( $this->pickup_location->get_address( "{$key}" ) ); ?>"
							/>
						</p>

					<?php endif; ?>

				<?php endforeach; ?>

				<p class="form-field">
					<label for="_phone"><?php esc_html_e( 'Phone', 'woocommerce-shipping-local-pickup-plus' ); ?></label>
					<input
						type="text"
						id="_phone"
						name="_phone"
						class="phone"
						value="<?php echo esc_attr( $this->pickup_location->get_phone( false ) ); ?>"
					/>
				</p>

			</div>

			<h4><?php
				esc_html_e( 'Description &amp; Notes', 'woocommerce-shipping-local-pickup-plus' );
				echo wc_help_tip( 'Enter information about your location, pickup instructions, or other helpful details that customers will see at checkout and in order confirmation emails.', 'woocommerce-shipping-local-pickup-plus' ); ?></h4>
			<?php wp_editor( $this->pickup_location->get_description(), '_address_notes', array(
				'media_buttons' => false,
				'teeny'         => true,
				'editor_height' => 168,
			) ); ?>

		</div>
		<?php
	}


	/**
	 * Output the available products panel.
	 *
	 * @since 2.0.0
	 */
	private function output_products_panel() {

		?>
		<div id="pickup-location-products" class="panel woocommerce_options_panel">

			<?php $per_item_enabled = wc_local_pickup_plus_shipping_method()->is_per_item_selection_enabled(); ?>

			<div style="display: <?php echo $per_item_enabled ? 'block' : 'none'; ?>;">

				<h4><?php esc_html_e( 'Available products at this location', 'wc-shipping-local-pickup-plus' ); ?></h4>

				<?php $mode = $this->pickup_location->has_products() ? 'some' : 'any'; ?>

				<div class="options_group">

					<p class="form-field">
						<label for="_products_availability_mode"><?php esc_html_e( 'Products availability', 'wooocommerce-shipping-local-pickup-plus' ); ?></label>
						<select
							name="_products_availability_mode"
							id="_products_availability_mode">
							<option value="any"  <?php selected( 'any',  $mode, true ); ?>><?php esc_html_e( 'All products',  'woocommerce-shipping-local-pickup-plus' ); ?></option>
							<option value="some" <?php selected( 'some', $mode, true ); ?>><?php esc_html_e( 'Some products', 'woocommerce-shipping-local-pickup-plus' ); ?></option>
						</select>
						<?php echo wc_help_tip( __( 'Either allow any product to be picked up at this location, or restrict this location to only allow pickup of certain products.', 'woocommerce-shipping-local-pickup-plus' ) ); ?>
						<span class="description">
							<span class="js-show-if-products-availability-is-any"  <?php if ( 'any'  !== $mode ) { echo 'style="display:none;"'; } ?>><?php esc_html_e( 'Any product can be collected at this location.',           'wc-shipping-local-pickup-plus' ); ?></span>
							<span class="js-show-if-products-availability-is-some" <?php if ( 'some' !== $mode ) { echo 'style="display:none;"'; } ?>><?php esc_html_e( 'Only certain products can be collected at this location.', 'wc-shipping-local-pickup-plus' ); ?></span>
						</span>
					</p>

				</div>

				<div class="options_group js-show-if-products-availability-is-some" <?php if ( 'any' === $mode ) { echo 'style="display:none;"'; } ?>>

					<p class="form-field">
						<label for="_product_availability_product_ids"><?php esc_html_e( 'Products', 'woocommerce-shipping-local-pickup-plus' ); ?></label>

						<?php $product_ids = $this->pickup_location->get_products( array( 'exclude_categories' => true ) ); ?>

						<span <?php if ( empty( $product_ids ) ) { echo 'style="display: none;"'; } ?>>
							<select
								class="wc-product-search js-search-products"
								id="_product_availability_product_ids"
								name="_product_availability_product_ids[]"
								style="width:100%;"
								multiple="multiple"
								data-placeholder="<?php esc_attr_e( 'Select products&hellip;', 'woocommerce-shipping-loca-pickup-plus' ); ?>"
								data-action="woocommerce_json_search_products">
								<?php $product_ids = array_filter( array_map( 'absint', $product_ids ) ); ?>
								<?php foreach ( $product_ids as $product_id ) : ?>
									<?php if ( $product = wc_get_product( $product_id ) ) : ?>
										<option value="<?php echo $product_id; ?>" selected="selected"><?php echo wp_kses_post( $product->get_formatted_name() ); ?></option>
									<?php endif; ?>
								<?php endforeach; ?>
							</select>

							<span class="description"><?php esc_html_e( 'The chosen products are available at this location.', 'wc-shipping-local-pickup-plus' ); ?></span>
						</span>

						<button class="button button-primary js-add-products"    <?php if ( ! empty( $product_ids ) ) { echo 'style="display:none;"'; } ?>><?php esc_html_e( 'Set products',    'woocommerce-shipping-local-pickup-plus' ); ?></button>
						<button class="button button-primary js-remove-products" <?php if (   empty( $product_ids ) ) { echo 'style="display:none;"'; } ?>><?php esc_html_e( 'Remove products', 'woocommerce-shipping-local-pickup-plus' ); ?></button>
					</p>

					<p class="form-field">
						<label for="_product_availability_product_categories"><?php esc_html_e( 'Product Categories', 'woocommerce-shipping-local-pickup-plus' ); ?></label>

						<?php $category_ids = $this->pickup_location->get_product_categories(); ?>

						<span <?php if ( empty( $category_ids ) ) { echo 'style="display: none;"'; } ?>>
							<select
								id="_product_availability_product_categories"
								name="_product_availability_product_categories[]"
								class="wc-enhanced-select js-search-products"
								multiple="multiple"
								style="width:100%; <?php if ( empty( $category_ids ) ) { echo 'display: none;'; } ?>"
								data-placeholder="<?php esc_attr_e( 'Select product categories&hellip;', 'woocommerce-shipping-local-pickup-plus' ); ?>">
								<?php $categories = get_terms( 'product_cat', 'orderby=name&hide_empty=0' ); ?>
								<?php if ( is_array( $categories ) ) : ?>
									<?php foreach ( $categories as $cat ) : ?>
										<option value="<?php echo esc_attr( $cat->term_id ); ?>" <?php selected( in_array( $cat->term_id, $category_ids, false ), true, true  ); ?>><?php echo esc_html( $cat->name ); ?></option>
									<?php endforeach; ?>
								<?php endif; ?>
							</select>
							<span class="description"><?php esc_html_e( 'Products belonging to the chosen categories are available at this location.', 'wc-shipping-local-pickup-plus' ); ?></span>
						</span>

						<button class="button button-primary js-add-product-categories"    <?php if ( ! empty( $category_ids ) ) { echo 'style="display:none;"'; } ?>><?php esc_html_e( 'Set product categories',    'woocommerce-shipping-local-pickup-plus' ); ?></button>
						<button class="button button-primary js-remove-product-categories" <?php if (   empty( $category_ids ) ) { echo 'style="display:none;"'; } ?>><?php esc_html_e( 'Remove product categories', 'woocommerce-shipping-local-pickup-plus' ); ?></button>
					</p>

					<br />
				</div>
			</div>

			<div style="display: <?php echo ! $per_item_enabled ? 'block' : 'none'; ?>;">

				<h4><?php esc_html_e( 'Product availability is not active.', 'wc-shipping-local-pickup-plus' ); ?></h4>

				<p>
					<?php printf(
						/** translators: Placeholders: %1$s - <a> tag, %2$s - </a> tag */
						esc_html__( 'In order to limit the products available at this location, you will need to allow customers to choose a location for each product in their cart. %1$sLearn more &raquo;%2$s', 'wc-shipping-local-pickup-plus' ),
						'<a href="' . esc_url( wc_local_pickup_plus()->get_documentation_url() ) . '#checkout-display" target="_blank">', '</a>'
					); ?>
				</p>

			</div>

		</div>
		<?php
	}


	/**
	 * Output the price adjustment panel.
	 *
	 * @since 2.0.0
	 */
	private function output_price_adjustment_panel() {

		?>
		<div id="pickup-location-price-adjustment" class="panel woocommerce_options_panel">
			<h4><?php esc_html_e( 'Price Adjustment', 'woocommerce-shipping-local-pickup-plus' ); ?></h4>

			<?php $price_adjustment_enabled = $this->get_post_meta( '_pickup_location_price_adjustment_enabled', 'no' ); ?>

			<p class="form-field">
				<label><?php esc_html_e( 'Enable', 'woocommerce-shipping-local-pickup-plus' ); ?></label>
				<input
					type="checkbox"
					name="_price_adjustment_enabled"
					id="_price_adjustment_enabled"
					value="yes"
					<?php checked( 'yes' === $price_adjustment_enabled, true, true ); ?>
				/>
				<label class="label-checkbox" for="_price_adjustment_enabled"><?php esc_html_e( 'Set price adjustment', 'woocommerce-shipping-local-pickup-plus' ); ?></label>
				<?php echo wc_help_tip( __( 'By enabling a price adjustment for this pickup location, any default value will be overridden when customers collect their purchases at this location.', 'woocommerce-shipping-local-pickup-plus' ) ); ?>
			</p>

			<div class="form-field js-show-if-price-adjustment-enabled" <?php if ( 'yes' !== $price_adjustment_enabled ) { echo 'style="display:none;"'; } ?>>
				<label for="_price_adjustment"><?php esc_html_e( 'Price Adjustment', 'woocommerce-shipping-local-pickup-plus' ); ?></label>
				<?php

				$this->pickup_location->get_price_adjustment()->output_field_html( array(
					'name' => '_price_adjustment',
					'desc_tip' => __( 'A cost or a discount applied when choosing this pickup location at checkout. You can set a fixed or a percentage amount. When using percentage, the value will be calculated based on cart contents value.', 'woocommerce-shipping-local-pickup-plus' )
				) );

				?>
			</div>

		</div>
		<?php
	}


	/**
	 * Output the panel for available hours for scheduling a pickup.
	 *
	 * @since 2.0.0
	 */
	private function output_pickup_appointments_panel() {

		// if not using appointments from shipping settings, do not show fields in this panel
		$appointments = wc_local_pickup_plus_appointments_mode();

		?>
		<div id="pickup-location-appointments" class="panel woocommerce_options_panel">

			<h4><?php esc_html_e( 'Pickup Appointment Scheduling', 'woocommerce-shipping-local-pickup-plus' ); ?></h4>

			<?php if ( 'disabled' !== $appointments ) : ?>

				<div class="options_group">

					<?php $business_hours_enabled = $this->get_post_meta( '_pickup_location_business_hours_enabled', 'no' ); ?>

					<p class="form-field">
						<label><?php esc_html_e( 'Typical Business Hours', 'woocommerce-shipping-local-pickup-plus' ); ?></label>
						<input
							type="checkbox"
							name="_business_hours_enabled"
							id="_business_hours_enabled"
							value="yes"
							<?php checked( 'yes' === $business_hours_enabled, true, true ) ?>
						/>
						<label class="label-checkbox" for="_business_hours_enabled"><?php esc_html_e( 'Enable', 'woocommerce-shipping-local-pickup-plus' ); ?></label>
						<?php echo wc_help_tip( __( 'Set the business hours when a pickup appointment can be scheduled by customers that purchase at this location. This will override the global business hours.', 'woocommerce-shipping-local-pickup-plus' ) ); ?>
					</p>

					<div class="form-field js-show-if-business-hours-enabled" <?php if ( 'yes' !== $business_hours_enabled ) { echo 'style="display:none;"'; } ?>>
						<?php

						$this->pickup_location->get_business_hours()->output_field_html( array(
							'name' => '_business_hours',
						) );

						?>
						<div class="clear"></div>
					</div>

					<?php $public_holidays_enabled = $this->get_post_meta( '_pickup_location_public_holidays_enabled', 'no' ); ?>

					<p class="form-field">
						<label><?php esc_html_e( 'Public Holidays', 'woocommerce-shipping-local-pickup-plus' ); ?></label>
						<input
							type="checkbox"
							name="_public_holidays_enabled"
							id="_public_holidays_enabled"
							value="yes"
							<?php checked( 'yes' === $public_holidays_enabled, true, true ) ?>
						/>
						<label class="label-checkbox" for="_public_holidays_enabled"><?php esc_html_e( 'Enable', 'woocommerce-shipping-local-pickup-plus' ); ?></label>
						<?php echo wc_help_tip( __( 'You can exclude individual days of the year from having a pickup appointment scheduled by a customer who selected this location. This will override the default global public holidays.', 'woocommerce-shipping-local-pickup-plus' ) ); ?>
					</p>

					<div class="form-field js-show-if-public-holidays-enabled" <?php if ( 'yes' !== $public_holidays_enabled ) { echo 'style="display:none;"'; } ?>>
						<?php

						$this->pickup_location->get_public_holidays()->output_field_html( array(
							'name' => '_public_holidays',
						) );

						?>
						<div class="clear"></div>
						<p class="description"><?php esc_html_e( 'The selected dates will be excluded for all years.', 'woocommerce-shipping-local-pickup-plus' ); ?></p>
					</div>

					<?php $lead_time_enabled = $this->get_post_meta( '_pickup_location_pickup_lead_time_enabled', 'no' ); ?>

					<p class="form-field">
						<label><?php esc_html_e( 'Pickup Lead Time', 'woocommerce-shipping-local-pickup-plus' ); ?></label>
						<input
							type="checkbox"
							name="_pickup_lead_time_enabled"
							id="_pickup_lead_time_enabled"
							value="yes"
							<?php checked( 'yes' === $lead_time_enabled, true, true ); ?>
						/>
						<label class="label-checkbox" for="_pickup_lead_time_enabled"><?php esc_html_e( 'Enable', 'woocommerce-shipping-local-pickup-plus' ); ?></label>
						<?php echo wc_help_tip( __( 'By enabling a lead time for this pickup location, the lead time default value will be overridden when customers schedule a pickup at this location.', 'woocommerce-shipping-local-pickup-plus' ) ); ?>
					</p>

					<div class="form-field js-show-if-lead-time-enabled" <?php if ( 'yes' !== $lead_time_enabled ) { echo 'style="display:none;"'; } ?>>
						<?php

						$this->pickup_location->get_pickup_lead_time()->output_field_html( array(
							'name' => '_pickup_lead_time',
						) );

						?>
					</div>

					<?php $deadline_enabled = $this->get_post_meta( '_pickup_location_pickup_deadline_enabled', 'no' ); ?>

					<p class="form-field">
						<label><?php esc_html_e( 'Pickup Deadline', 'woocommerce-shipping-local-pickup-plus' ); ?></label>
						<input
							type="checkbox"
							name="_pickup_deadline_enabled"
							id="_pickup_deadline_enabled"
							value="yes"
							<?php checked( 'yes' === $deadline_enabled, true, true ); ?>
						/>
						<label class="label-checkbox" for="_pickup_deadline_enabled"><?php esc_html_e( 'Enable', 'woocommerce-shipping-local-pickup-plus' ); ?></label>
						<?php echo wc_help_tip( __( 'By enabling a deadline for this pickup location, the deadline default value will be overridden when customers schedule a pickup at this location.', 'woocommerce-shipping-local-pickup-plus' ) ); ?>
					</p>

					<div class="form-field js-show-if-deadline-enabled" <?php if ( 'yes' !== $deadline_enabled ) { echo 'style="display:none;"'; } ?>>
						<?php

						$this->pickup_location->get_pickup_deadline()->output_field_html( array(
							'name' => '_pickup_deadline',
						) );

						?>
					</div>

					<br>
				</div>

			<?php else : ?>

				<div class="options_group">
					<p><span class="description"><?php
						/* translators: Placeholders: %1$s - opening <a> HTML link tag, %2$s - closing </a> HTML link tag */
						printf( __( 'To set collection hours for pickup appointment scheduling, you need to enable pickup appointments from %1$sLocal Pickup Plus settings%2$s.', 'woocommerce-shipping-local-pickup-plus' ),
							'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=shipping&section=local_pickup_plus' ) . '">', '</a>' );
						?></span></p>
				</div>

			<?php endif; ?>

		</div>
		<?php
	}


	/**
	 * Output the emails panel.
	 *
	 * @since 2.0.0
	 */
	private function output_emails_panel() {

		?>
		<div id="pickup-location-emails" class="panel woocommerce_options_panel">
			<p><em><?php esc_html_e( 'Enter a comma-separated list of email recipients that should receive admin order emails when this location is included in the order. They will receive "New Order", "Cancelled Order", and "Failed Order" notification emails.', 'woocommerce-shipping-local-pickup-plus' ); ?></em></p>
			<p class="form-field">
				<label for="_notification_recipients"><?php esc_html_e( 'Recipients', 'woocommerce-shipping-local-pickup-plus' ); ?></label>
				<textarea
					type="test"
					name="_notification_recipients"
					id="_notification_recipients"
				><?php echo esc_textarea( $this->pickup_location->get_email_recipients( 'string' ) ); ?></textarea>
			</p>
		</div>
		<?php
	}


	/**
	 * Save the pickup location data.
	 *
	 * @since 2.0.0
	 *
	 * @param int $post_id the Pickup Location post id
	 * @param \WP_Post $post the Pickup Location post object
	 */
	public function update_data( $post_id, \WP_Post $post ) {

		wc_local_pickup_plus()->check_tables();

		$pickup_location = new \WC_Local_Pickup_Plus_Pickup_Location( $post );


		/* Products availability */

		$products_availability = ! empty( $_POST['_products_availability_mode'] ) ? $_POST['_products_availability_mode'] : 'any';

		if ( 'any' === $products_availability ) {

			$pickup_location->delete_products();

		} else {

			$products     = ! empty( $_POST['_product_availability_product_ids'] )        ? (array) $_POST['_product_availability_product_ids']         : array();
			$product_cats = ! empty( $_POST['_product_availability_product_categories'] ) ? (array) $_POST['_product_availability_product_categories']  : array();

			$pickup_location->set_products( $products );
			$pickup_location->set_product_categories( $product_cats );
		}


		/* Costs & Discounts (price adjustment) */

		$price_adjustment_enabled = ! empty( $_POST['_price_adjustment_enabled'] );

		if ( $price_adjustment_enabled ) {

			update_post_meta( $post_id, '_pickup_location_price_adjustment_enabled', 'yes' );

			$adjustment = $_POST['_price_adjustment'];
			$amount     = $_POST['_price_adjustment_amount'];
			$type       = $_POST['_price_adjustment_type'];

			$pickup_location->set_price_adjustment( $adjustment, (float) $amount, $type );

		} else {

			update_post_meta( $post_id, '_pickup_location_price_adjustment_enabled', 'no' );

			$pickup_location->delete_price_adjustment();
		}


		/* Business hours for pickup appointments */

		$business_hours_enabled = ! empty( $_POST['_business_hours_enabled'] );

		if ( $business_hours_enabled ) {

			$business_hours = $pickup_location->get_business_hours()->get_field_value( '_business_hours', $_POST );

			if ( ! empty( $business_hours ) ) {

				$pickup_location->set_business_hours( $business_hours );

				update_post_meta( $post_id, '_pickup_location_business_hours_enabled', 'yes' );

			} else {

				$pickup_location->delete_business_hours();

				update_post_meta( $post_id, '_pickup_location_business_hours_enabled', 'no' );
			}

		} else {

			$pickup_location->delete_business_hours();

			update_post_meta( $post_id, '_pickup_location_business_hours_enabled', 'no' );
		}


		/* Pickup location closure days / public holidays calendar */

		$public_holidays_enabled = ! empty( $_POST['_public_holidays_enabled'] );

		if ( $public_holidays_enabled ) {

			update_post_meta( $post_id, '_pickup_location_public_holidays_enabled', 'yes' );

			if ( ! empty( $_POST['_public_holidays'] ) ) {
				$pickup_location->set_public_holidays( $_POST['_public_holidays'] );
			} else {
				$pickup_location->delete_public_holidays();
			}

		} else {

			$pickup_location->delete_public_holidays();

			update_post_meta( $post_id, '_pickup_location_public_holidays_enabled', 'no' );
		}


		/* Pickup lead time */

		$lead_time_enabled = ! empty( $_POST['_pickup_lead_time_enabled'] );

		if ( $lead_time_enabled ) {

			update_post_meta( $post_id, '_pickup_location_pickup_lead_time_enabled', 'yes' );

			$pickup_location->set_pickup_lead_time( max( 0, (int) $_POST['_pickup_lead_time_amount'] ), $_POST['_pickup_lead_time_interval'] );

		} else {

			$pickup_location->delete_pickup_lead_time();

			update_post_meta( $post_id, '_pickup_location_pickup_lead_time_enabled', 'no' );
		}


		/* Pickup deadline */

		$deadline_enabled = ! empty( $_POST['_pickup_deadline_enabled'] );

		if ( $deadline_enabled ) {

			update_post_meta( $post_id, '_pickup_location_pickup_deadline_enabled', 'yes' );

			$pickup_location->set_pickup_deadline( max( 0, (int) $_POST['_pickup_deadline_amount'] ), $_POST['_pickup_deadline_interval'] );

		} else {

			$pickup_location->delete_pickup_deadline();

			update_post_meta( $post_id, '_pickup_location_pickup_deadline_enabled', 'no' );
		}


		/* Email notification recipients */

		$notification_recipients = ! empty( $_POST['_notification_recipients'] ) ? sanitize_text_field( trim( $_POST['_notification_recipients'] ) ) : null;

		if ( ! empty( $notification_recipients ) ) {
			$pickup_location->set_email_recipients( $notification_recipients );
		} else {
			$pickup_location->delete_email_recipients();
		}


		/* Location address fields */

		$address = array( 'name' => $pickup_location->get_name() );
		$pieces  = $this->get_address_pieces();
		$keys    = ! empty( $pieces ) ? array_keys( $pieces ) : array();

		foreach ( $keys as $key ) {
			if ( 'country' === $key ) {
				$pieces = ! empty( $_POST['_country'] ) ? explode( ':', $_POST['_country'] ) : array();
				// also get the state
				$address['country'] = isset( $pieces[0] ) ? sanitize_text_field( $pieces[0] ) : '';
				$address['state']   = isset( $pieces[1] ) ? sanitize_text_field( $pieces[1] ) : '';
			} elseif ( 'state' === $key ) {
				// this is contained in the country-state string
				continue;
			} else {
				// all other address pieces
				$address[ $key ]    = ! empty( $_POST[ "_{$key}" ] ) ? sanitize_text_field( $_POST[ '_' . $key ] ) : '';
			}
		}

		$pickup_location->set_address( $address );


		/* Location phone */

		if ( ! empty( $_POST['_phone'] ) ) {
			$pickup_location->set_phone( sanitize_text_field( $_POST['_phone'] ) );
		}


		/* Address Notes (post content) */

		$address_notes = ! empty( $_POST['_address_notes'] ) ? wp_kses_post( $_POST['_address_notes'] ) : '';

		// this is necessary to avoid infinite loops while updating the post below
		remove_action( 'save_post', array( $this, 'save_post' ), 10 );

		wp_update_post( array(
			'ID'           => (int) $post_id,
			'post_content' => $address_notes
		) );

		// place the callback back in its place after the post content has been updated
		add_action( 'save_post', array( $this, 'save_post' ), 10, 2 );
	}


}

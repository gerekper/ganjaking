<?php
/**
 * WooCommerce Memberships
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Memberships to newer
 * versions in the future. If you wish to customize WooCommerce Memberships for your
 * needs please refer to https://docs.woocommerce.com/document/woocommerce-memberships/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2014-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

use SkyVerge\WooCommerce\PluginFramework\v5_7_1 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Memberships Data Meta Box for products
 *
 * @since 1.0.0
 */
class WC_Memberships_Meta_Box_Product_Memberships_Data extends \WC_Memberships_Meta_Box {


	/** @var bool Flag to address saving product data on WC 3.0+. */
	private static $updated;


	/**
	 * Constructor
	 *
	 * @since 1.7.0
	 */
	public function __construct() {

		$this->id      = 'wc-memberships-product-memberships-data';
		$this->screens = array( 'product' );

		parent::__construct();
	}


	/**
	 * Returns the meta box title.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_title() {
		return __( 'Memberships', 'woocommerce-memberships' );
	}


	/**
	 * Enqueue scripts & styles for the meta box.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts_and_styles() {

		// make sure wc-admin-meta-boxes script is loaded _after_ our script,
		// so that the enhanced select fields are initialized before tabs do,
		// otherwise the placeholder text will be cut off...
		$GLOBALS['wp_scripts']->registered['wc-admin-meta-boxes']->deps[] = 'wc-memberships-admin';
	}


	/**
	 * Returns all membership plans that a product grants access to.
	 *
	 * @since 1.0.0
	 *
	 * @param int $product_id
	 * @param string $return Optional: type of data to return, id or object (default)
	 * @return \WC_Memberships_Membership_Plan[] Array of plans
	 */
	private function get_product_membership_plans( $product_id, $return = 'object' ) {

		$product_membership_plans = array();
		$membership_plans         = wc_memberships_get_membership_plans();

		if ( ! empty( $membership_plans ) ) {

			foreach ( $membership_plans as $plan ) {

				if ( $plan->has_product( $product_id ) ) {
					$product_membership_plans[] = 'object' === $return ? $plan : $plan->get_id();
				}
			}
		}

		return $product_membership_plans;
	}


	/**
	 * Returns product restriction rules.
	 *
	 * @since 1.7.0
	 *
	 * @return \WC_Memberships_Membership_Plan_Rule[] array of rules
	 */
	public function get_product_restriction_rules() {

		$product_restriction_rules = array();

		if ( $this->post instanceof \WP_Post ) {

			// get applied restriction rules
			$product_restriction_rules = wc_memberships()->get_rules_instance()->get_rules( array(
				'rule_type'         => 'product_restriction',
				'object_id'         => $this->post->ID,
				'content_type'      => 'post_type',
				'content_type_name' => $this->post->post_type,
				'exclude_inherited' => false,
				'plan_status'       => 'any',
			) );
			$membership_plan_options = $this->get_membership_plan_options();
			$membership_plan_ids     = array_keys( $membership_plan_options );

			// add empty option to create a HTML template for new rules
			$product_restriction_rules['__INDEX__'] = new \WC_Memberships_Membership_Plan_Rule( array(
				'rule_type'          => 'product_restriction',
				'object_ids'         => array( $this->post->ID ),
				'id'                 => '',
				'membership_plan_id' => array_shift( $membership_plan_ids ),
				'access_schedule'    => 'immediate',
				'access_type'        => '',
			) );
		}

		return $product_restriction_rules;
	}


	/**
	 * Returns purchasing discount rules.
	 *
	 * @since 1.7.0
	 *
	 * @return \WC_Memberships_Membership_Plan_Rule[] Array of rules
	 */
	public function get_purchasing_discount_rules() {

		$purchasing_discount_rules = array();

		if ( $this->post instanceof \WP_Post ) {

			// get applied restriction rules
			$purchasing_discount_rules = wc_memberships()->get_rules_instance()->get_rules( array(
				'rule_type'         => 'purchasing_discount',
				'object_id'         => $this->post->ID,
				'content_type'      => 'post_type',
				'content_type_name' => $this->post->post_type,
				'exclude_inherited' => false,
				'plan_status'       => 'any',
			) );

			// add empty option to create a HTML template for new rules
			$purchasing_discount_rules['__INDEX__'] = new \WC_Memberships_Membership_Plan_Rule( array(
				'rule_type'          => 'purchasing_discount',
				'object_ids'         => array( $this->post->ID ),
				'id'                 => '',
				'membership_plan_id' => '',
				'discount_type'      => '',
				'discount_amount'    => '',
				'active'             => '',
			) );
		}

		return $purchasing_discount_rules;
	}


	/**
	 * Displays the memberships meta box.
	 *
	 * @since 1.0.0
	 *
	 * @param \WP_Post $post
	 */
	public function output( \WP_Post $post ) {

		$this->post    = $post;
		$this->product = $product = wc_get_product( $post );

		?>
		<p class="grouped-notice <?php if ( ! $product->is_type( 'grouped' ) ) : ?>hide<?php endif; ?>">
			<?php esc_html_e( 'Memberships do not support grouped products.', 'woocommerce-memberships' ); ?>
		</p>

		<div class="panel-wrap wc-memberships-data <?php if ( $product->is_type( 'grouped' ) ) : ?>hide<?php endif; ?>">

			<ul class="memberships_data_tabs wc-tabs">
				<?php

				/**
				 * Filter product memberships data tabs
				 *
				 * @since 1.0.0
				 * @param array $tabs Associative array of memberships data tabs
				 */
				$memberships_data_tabs = apply_filters( 'wc_memberships_product_data_tabs', array(
					'restrict_product' => array(
						'label'  => __( 'Restrict Content', 'woocommerce-memberships' ),
						'class'  => array( 'active' ),
						'target' => 'memberships-data-restrict-product',
					),
					'grant_access' => array(
						'label'  => __( 'Grant Access', 'woocommerce-memberships' ),
						'target' => 'memberships-data-grant-access',
					),
					'purchasing_discounts' => array(
						'label'  => __( 'Discounts', 'woocommerce-memberships' ),
						'target' => 'memberships-data-purchasing-discounts',
					),
				) );

				foreach ( $memberships_data_tabs as $key => $tab ) :

					$class = isset( $tab['class'] ) ? $tab['class'] : array();
					?>
					<li class="<?php echo sanitize_html_class( $key ); ?>_options <?php echo sanitize_html_class( $key ); ?>_tab <?php echo implode( ' ' , array_map( 'sanitize_html_class', $class ) ); ?>">
						<a href="#<?php echo esc_attr( $tab['target'] ); ?>"><span><?php echo esc_html( $tab['label'] ); ?></span></a>
					</li>
					<?php

				endforeach;

				/**
				 * Fires after the product memberships data write panel tabs are displayed
				 *
				 * @since 1.0.0
				 */
				do_action( 'wc_memberships_data_product_write_panel_tabs' );

				?>
			</ul>
			<?php

			if ( ! empty( $memberships_data_tabs ) ) {

				// output the individual panels
				foreach ( array_keys( $memberships_data_tabs ) as $tab ) {

					$panel = "output_{$tab}_panel";

					if ( method_exists( $this, $panel ) ) {
						$this->$panel( $product, $post );
					}
				}
			}

			/**
			 * Fires after the product memberships data panels are displayed
			 *
			 * @since 1.0.0
			 */
			do_action( 'wc_memberships_data_product_panels' );

			?>
			<div class="clear"></div>
		</div><!-- //.panel-wrap -->
		<?php
	}


	/**
	 * Output the restrict product panel
	 *
	 * @since 1.7.0
	 * @param \WC_Product $product
	 * @param \WP_Post $post
	 */
	private function output_restrict_product_panel( $product, $post ) {

		?>
		<div id="memberships-data-restrict-product" class="panel woocommerce_options_panel">
			<?php

			woocommerce_wp_checkbox( array(
				'id'          => '_wc_memberships_force_public',
				'class'       => 'js-toggle-rules',
				'label'       => __( 'Disable restrictions', 'woocommerce-memberships' ),
				'description' => __( 'Check this box if you want to force this product to be public regardless of any restriction rules that may apply now or in the future.', 'woocommerce-memberships' ),
			) );

			?>
			<div class="js-restrictions <?php if ( 'yes' === wc_memberships_get_content_meta( $post->ID, '_wc_memberships_force_public', true ) ) : ?>hide<?php endif; ?>">

				<div class="options_group">
					<div class="table-wrap">
						<?php

						// load content restriction rules view
						require_once( wc_memberships()->get_plugin_path() . '/includes/admin/meta-boxes/views/class-wc-memberships-meta-box-view-product-restriction-rules.php' );

						// output content restriction rules view
						$view = new \WC_Memberships_Meta_Box_View_Product_Restriction_Rules( $this );
						$view->output();

						?>
					</div>
				</div>
				<?php

				// get the available membership plans
				$membership_plans = $this->get_available_membership_plans();

				if ( ! empty( $membership_plans ) ) :

					?><p><em><?php esc_html_e( 'Need to add or edit a plan?', 'woocommerce-memberships' ); ?></em> <a target="_blank" href="<?php echo esc_url( admin_url( 'edit.php?post_type=wc_membership_plan' ) ); ?>"><?php esc_html_e( 'Manage Membership Plans', 'woocommerce-memberships' ); ?></a></p><?php

				endif;

				?>
				<div class="options_group">
					<?php

					woocommerce_wp_checkbox( array(
						'id'          => '_wc_memberships_use_custom_product_viewing_restricted_message',
						'class'       => 'js-toggle-custom-message',
						'label'       => __( 'Use custom message', 'woocommerce-memberships' ),
						'description' => __( 'Check this box if you want to customize the <strong>viewing restricted message</strong> for this product.', 'woocommerce-memberships' )
					) );

					?>
					<div
						class="js-custom-message-editor-container <?php if ( 'yes' !== wc_memberships_get_content_meta( $post->ID, '_wc_memberships_use_custom_product_viewing_restricted_message', true ) ) : ?>hide<?php endif; ?>"
						style="overflow: hidden;">
						<?php

						/* translators: Placeholders: %1$s and %2$s are placeholders meant for {products} and {login_url} merge tags */
						printf( '<p>' . __( '%1$s automatically inserts the product(s) needed to gain access. %2$s inserts the URL to my account page. HTML is allowed.', 'woocommerce-memberships' ) . '</p>',
							'<strong><code>{products}</code></strong>',
							'<strong><code>{login_url}</code></strong>'
						);

						wp_editor( wc_memberships_get_content_meta( $post->ID, '_wc_memberships_product_viewing_restricted_message', true ), '_wc_memberships_product_viewing_restricted_message', array(
							'textarea_rows' => 5,
							'teeny'         => true,
						) );

						?>
					</div>

				</div>

				<div class="options_group">
					<?php

					woocommerce_wp_checkbox( array(
						'id'          => '_wc_memberships_use_custom_product_purchasing_restricted_message',
						'class'       => 'js-toggle-custom-message',
						'label'       => __( 'Use custom message', 'woocommerce-memberships' ),
						'description' => __( 'Check this box if you want to customize the <strong>purchasing restricted message</strong> for this product.', 'woocommerce-memberships' )
					) );

					?>
					<div
						class="js-custom-message-editor-container <?php if ( 'yes' !== wc_memberships_get_content_meta( $post->ID, '_wc_memberships_use_custom_product_purchasing_restricted_message', true ) ) : ?>hide<?php endif; ?>"
						style="overflow: hidden;">
						<?php

						/* translators: Placeholders: %1$s and %2$s are placeholders meant for {products} and {login_url} merge tags. */
						printf( '<p>' . __( '%1$s automatically inserts the product(s) needed to gain access. %2$s inserts the URL to my account page. HTML is allowed.', 'woocommerce-memberships' ) . '</p>',
							'<strong><code>{products}</code></strong>',
							'<strong><code>{login_url}</code></strong>'
						);

						wp_editor( wc_memberships_get_content_meta( $post->ID, '_wc_memberships_product_purchasing_restricted_message', true ), '_wc_memberships_product_purchasing_restricted_message', array(
							'textarea_rows' => 5,
							'teeny'         => true,
						) );

						?>
					</div>

				</div>
			</div>
			<?php

			/**
			 * Fires after the product memberships data product restriction panel is displayed
			 *
			 * @since 1.0.0
			 */
			do_action( 'wc_memberships_data_options_restrict_product' );

			?>
		</div><!-- //#memberships-data-restrict-products -->
		<?php
	}


	/**
	 * Output the grant access panel
	 *
	 * @since 1.7.0
	 * @param \WC_Product $product
	 * @param \WP_Post $post
	 */
	private function output_grant_access_panel( $product, $post ) {

		// get available plans
		$membership_plans      = $this->get_available_membership_plans();
		// get plans this product grants access to
		$grant_access_to_plans = $this->get_product_membership_plans( $product->get_id() );

		?>
		<div id="memberships-data-grant-access" class="panel woocommerce_options_panel">

			<p class="variable-notice <?php if ( ! $product->is_type( 'variable' ) ) : ?>hide<?php endif; ?>">
				<?php
				/* translators: %1$s - opening <a> link tag, %2$s - closing </a> link tag */
				printf( __( 'These settings affect all variations. For variation-level control use the %1$smembership plan screen%2$s.', 'woocommerce-memberships' ), '<a href="' . admin_url( 'edit.php?post_type=wc_membership_plan' ) . '">', '</a>' ); ?>
			</p>

			<!-- Plans that this product grants access to -->
			<div class="options_group">

				<?php if ( empty( $membership_plans ) ) : ?>

					<p>
						<?php
						/* translators: Placeholders: %1$s - opening <a> link tag, %2$s - closing </a> link tag */
						printf( __( 'To grant membership access, please %1$sAdd a Membership Plan%2$s', 'woocommerce-memberships' ), '<a target="_blank" href="' . esc_url( admin_url( 'post-new.php?post_type=wc_membership_plan' ) ) . '">', '</a>' ); ?>
					</p>

				<?php else : ?>

					<p class="form-field">
						<label for="_wc_memberships_membership_plan_ids"><?php esc_html_e( 'Purchasing product grants access to', 'woocommerce-memberships' ); ?></label>
						<select
							name="_wc_memberships_membership_plan_ids[]"
							id="_wc_memberships_membership_plan_ids"
							class="js-membership-plan-ids"
							style="width: 90%;"
							multiple="multiple"
							data-placeholder="<?php esc_attr_e( 'Search for a membership plan&hellip;', 'woocommerce-memberships' ); ?>"
							data-action="wc_memberships_search_membership_plans">
							<?php foreach ( $grant_access_to_plans as $plan ) : ?>
								<option value="<?php echo $plan->get_id() ?>" selected><?php echo esc_html( $plan->get_formatted_name() ); ?></option>
							<?php endforeach; ?>
						</select>
						<?php echo wc_help_tip( __( 'Select which membership plans does purchasing this product grant access to.', 'woocommerce-memberships' ) ); ?>
					</p>

					<?php

					if ( $this->product->is_type( 'variable' ) && ( $variations = $this->product->get_children() ) ) {

						$variation_grants_access_to_plans = array();

						foreach ( $variations as $variation_id ) {
							if ( $access_plans = $this->get_product_membership_plans( $variation_id ) ) {
								foreach ( $access_plans as $access_plan ) {
									$variation_grants_access_to_plans[ (int) $variation_id ] = ' <a href="' . esc_url( get_edit_post_link( $access_plan->get_id() ) ) . '">' . esc_html( $access_plan->get_formatted_name() ) . '</a>';
								}
							}
						}

						?>

						<?php if ( ! empty( $variation_grants_access_to_plans ) ) : ?>

							<p class="form-field">
								<label><?php esc_html_e( 'Purchasing individual variations grants access to', 'woocommerce-memberships' ) ?></label>
								<br />
								<span>
									<?php foreach ( $variation_grants_access_to_plans as $variation_id => $access_plan ) : ?>

										<?php if ( $variation_product = wc_get_product( $variation_id ) ) : ?>
											<strong><?php echo esc_html( $variation_product->get_formatted_name() ); ?></strong>: <?php echo wc_memberships_list_items( $variation_grants_access_to_plans, 'and' ); ?>.<br />
										<?php endif; ?>

									<?php endforeach; ?>
								</span>
							</p>

						<?php endif; ?>

						<?php
					}

					?>

					<p><em><?php esc_html_e( 'Need to add or edit a plan?', 'woocommerce-memberships' ); ?></em> <a target="_blank" href="<?php echo esc_url( admin_url( 'edit.php?post_type=wc_membership_plan' ) ); ?>"><?php esc_html_e( 'Manage Membership Plans', 'woocommerce-memberships' ); ?></a>.</p>

				<?php endif; ?>

			</div>
			<?php

			/**
			 * Fires after the product memberships data grant access panel is displayed
			 *
			 * @since 1.0.0
			 */
			do_action( 'wc_memberships_data_options_grant_access' );

			?>
		</div><!-- //#memberships-data-grant-access -->
		<?php
	}


	/**
	 * Output the purchasing discounts panel
	 *
	 * @since 1.7.0
	 * @param \WC_Product $product
	 * @param \WP_Post $post
	 */
	private function output_purchasing_discounts_panel( $product, $post ) {

		?>
		<div id="memberships-data-purchasing-discounts" class="panel woocommerce_options_panel">
			<?php

			woocommerce_wp_checkbox( array(
				'id'          => '_wc_memberships_exclude_discounts',
				'class'       => 'js-toggle-discounts',
				'label'       => __( 'Disable discounts', 'woocommerce-memberships' ),
				'description' => __( 'Check this box if you want to exclude this product from member discounts of any membership plan, regardless of discount rules that may apply now or in the future.', 'woocommerce-memberships' ),
			) );

			?>
			<div class="js-discounts table-wrap <?php if ( 'yes' === wc_memberships_get_content_meta( $post, '_wc_memberships_exclude_discounts', true ) ) : ?>hide<?php endif; ?>">
				<?php

				// load purchasing discounts rules view
				require_once( wc_memberships()->get_plugin_path() . '/includes/admin/meta-boxes/views/class-wc-memberships-meta-box-view-purchasing-discount-rules.php' );

				// output purchasing discounts rules view
				$view = new \WC_Memberships_Meta_Box_View_Purchasing_Discount_Rules( $this );
				$view->output();

				?>
			</div>
			<?php

			/**
			 * Fires after the membership plan purchasing discounts panel is displayed
			 *
			 * @since 1.0.0
			 */
			do_action( 'wc_memberships_data_options_purchasing_discounts' );

			?>
		</div><!-- //#memberships-data-purchase-discounts -->
		<?php
	}


	/**
	 * Process and save restriction rules
	 *
	 * @since 1.0.0
	 * @param int $post_id
	 * @param \WP_Post $post
	 */
	public function update_data( $post_id, \WP_Post $post ) {

		$has_updated = self::$updated;

		if ( ! $has_updated ) {
			self::$updated = true;
		} else {
			return;
		}

		// update restriction & discount rules
		\WC_Memberships_Admin_Membership_Plan_Rules::save_rules( $_POST, $post_id, array( 'product_restriction', 'purchasing_discount' ), 'post' );

		// update custom messages
		$this->update_custom_message( $post_id, array( 'product_viewing_restricted', 'product_purchasing_restricted' ) );

		// maybe force product public
		if ( ! empty( $_POST['_wc_memberships_force_public'] ) ) {
			wc_memberships()->get_restrictions_instance()->set_product_public( $post );
		} else {
			wc_memberships()->get_restrictions_instance()->unset_product_public( $post );
		}

		// maybe exclude product from member discounts
		if ( ! empty( $_POST['_wc_memberships_exclude_discounts'] ) ) {
			wc_memberships()->get_member_discounts_instance()->set_product_excluded_from_member_discounts( $post );
		} else {
			wc_memberships()->get_member_discounts_instance()->unset_product_excluded_from_member_discounts( $post );
		}

		// update membership plans that this product grants access to
		$plan_ids        = $this->get_product_membership_plans( $post->ID, 'id' );
		$posted_plan_ids = isset( $_POST['_wc_memberships_membership_plan_ids'] ) ? $_POST['_wc_memberships_membership_plan_ids'] : array();

		if ( is_string( $posted_plan_ids ) ) {
			$posted_plan_ids = explode( ',', $posted_plan_ids );
		}

		sort( $plan_ids );
		sort( $posted_plan_ids );

		// only continue processing if there are changes (loose check)
		if ( $plan_ids != $posted_plan_ids ) {

			$removed = array_diff( $plan_ids, $posted_plan_ids );
			$new     = array_diff( $posted_plan_ids, $plan_ids );

			// handle removed plans
			if ( ! empty( $removed ) ) {

				foreach ( $removed as $plan_id ) {

					if ( $plan = wc_memberships_get_membership_plan( $plan_id ) ) {

						$product_ids = $plan->get_product_ids();

						if ( ( $key = array_search( $post_id, $product_ids, false ) ) !== false ) {
							unset( $product_ids[ $key ] );
						}

						$plan->set_product_ids( $product_ids );
					}
				}
			}

			// handle new plans
			if ( ! empty( $new ) ) {

				foreach ( $new as $plan_id ) {

					if ( $plan = wc_memberships_get_membership_plan( $plan_id ) ) {

						$product_ids   = $plan->get_product_ids();
						$product_ids[] = $post_id;

						$plan->set_product_ids( $product_ids );

						if ( ! $plan->is_access_method( 'purchase' ) ) {
							$plan->set_access_method( 'purchase' );
						}
					}
				}
			}
		}
	}


}

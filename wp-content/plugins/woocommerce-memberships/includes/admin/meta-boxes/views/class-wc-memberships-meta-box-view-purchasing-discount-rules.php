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
 * View for purchasing discount rules table
 *
 * @since 1.7.0
 */
class WC_Memberships_Meta_Box_View_Purchasing_Discount_Rules extends \WC_Memberships_Meta_Box_View {


	/**
	 * HTML output.
	 *
	 * @since 1.7.0
	 *
	 * @param array $args
	 */
	public function output( $args = array() ) {

		$context             = 'wc_membership_plan' === $this->post->post_type ? 'membership_plan' : 'product';
		$product             = $this->product;
		$is_variable_product = $product ? $this->product->is_type( 'variable' ) : false;
		$colspan             = 'membership_plan' === $context || $is_variable_product ? 6 : 5;

		?>
		<table class="widefat rules purchasing-discount-rules js-rules">

			<thead>
				<tr>

					<td class="check-column">
						<label class="screen-reader-text" for="product-discount-rules-select-all"><?php esc_html_e( 'Select all', 'woocommerce-memberships' ); ?></label>
						<input
							type="checkbox"
							id="product-discount-rules-select-all"
						>
					</td>

					<?php if ( 'membership_plan' === $context ) : ?>

						<th scope="col" class="purchasing-discount-content-type content-type-column">
							<?php esc_html_e( 'Discount', 'woocommerce-memberships' ); ?>
						</th>

						<th scope="col" class="purchasing-discount-objects objects-column">
							<?php esc_html_e( 'Title', 'woocommerce-memberships' ); ?>
							<?php echo wc_help_tip( __( 'Search&hellip; or leave blank to apply to all', 'woocommerce-memberships' ) ); ?>
						</th>

					<?php else : ?>

						<?php if ( $is_variable_product ) : ?>

							<th scope="col" class="purchasing-discount-applies-to product-variation-column">
								<?php esc_html_e( 'Rule applies to', 'woocommerce-memberships' ); ?>
							</th>

						<?php endif; ?>

						<th scope="col" class="purchasing-discount-membership-plan membership-plan-column">
							<?php esc_html_e( 'Plan', 'woocommerce-memberships' ); ?>
						</th>

					<?php endif; ?>

					<th scope="col" class="purchasing-discount-discount-type discount-type-column">
						<?php esc_html_e( 'Type', 'woocommerce-memberships' ); ?>
					</th>

					<th scope="col" class="purchasing-discount-discount-amount amount-column">
						<?php esc_html_e( 'Amount', 'woocommerce-memberships' ); ?>
					</th>

					<th scope="col" class="purchasing-discount-active active-column">
						<?php esc_html_e( 'Active', 'woocommerce-memberships' ); ?>
					</th>

				</tr>
			</thead>
			<?php

			// load purchasing discount rule view object
			require_once( wc_memberships()->get_plugin_path() . '/includes/admin/meta-boxes/views/class-wc-memberships-meta-box-view-purchasing-discount-rule.php' );

			// get the purchasing discount rules
			$purchasing_discount_rules = $this->meta_box->get_purchasing_discount_rules();

			// output purchasing discount rule views
			foreach ( $purchasing_discount_rules as $index => $rule ) {

				$view = new \WC_Memberships_Meta_Box_View_Purchasing_Discount_Rule( $this->meta_box, $rule );
				$view->output( array( 'index' => $index, 'product' => $product ) );
			}

			// get available membership plans
			$membership_plans = $this->meta_box->get_available_membership_plans();

			?>
			<tbody class="norules <?php if ( count( $membership_plans ) > 0 && count( $purchasing_discount_rules ) > 1 ) : ?>hide<?php endif; ?>">
				<tr>
					<td colspan="<?php echo $colspan; ?>">
						<?php

						if ( 'membership_plan' === $context || ! empty( $membership_plans ) ) {
							esc_html_e( 'There are no discounts yet. Click below to add one.', 'woocommerce-memberships' );
						} else {
							/* translators: Placeholder: %s - "Add a membership plan" link */
							printf( __( 'To create member discounts, please %s', 'woocommerce-memberships' ),
								'<a target="_blank" href="' . esc_url( admin_url( 'post-new.php?post_type=wc_membership_plan' ) ) . '">' .
								esc_html_e( 'Add a Membership Plan', 'woocommerce-memberships' ) .
								'</a>.'
							);
						}

						?>
					</td>
				</tr>
			</tbody>

			<?php if ( 'membership_plan' === $context || ! empty( $membership_plans ) ) : ?>

				<tfoot>

					<?php if ( $is_variable_product && ( $variations = $product->get_children() ) ) : ?>

						<?php

						$rules           = wc_memberships()->get_rules_instance();
						$variation_rules = array( array() );
						$variation_ids   = array();

						foreach ( $variations as $variation_id ) {
							$variation_ids[]   = (int) $variation_id;
							$variation_rules[] = $rules->get_product_purchasing_discount_rules( $variation_id );
						}

						/* @type $variation_rules \WC_Memberships_Membership_Plan_Rule[] */
						$variation_rules = call_user_func_array( 'array_merge', $variation_rules );

						?>

						<?php if ( ! empty( $variation_rules ) ) : ?>

							<?php foreach ( $variation_rules as $variation_rule ) : ?>

								<?php $object_ids = $variation_rule->get_object_ids(); ?>
								<?php if ( ! empty( $object_ids ) ) : ?>

									<?php foreach ( $object_ids as $object_id ) : ?>

										<?php if ( in_array( $object_id, $variation_ids, false ) && ( $variation = wc_get_product( $object_id ) ) ) : ?>

											<tr>

												<th scope="row" class="check-column"></th>

												<td class="purchasing-discount-applies-to product-variation-column">
													<p class="form-field"><?php esc_html_e( $variation->get_formatted_name() ); ?></p>
												</td>

												<td class="purchasing-discount-membership-plan membership-plan-column">
													<p class="form-field">
														<label for="_variation_purchasing_discount_rules<?php echo esc_attr( $object_id ); ?>_membership_plan_id"><?php esc_html_e( 'Plan', 'woocommerce-memberships' ); ?>:</label>

														<select
															disabled
															name="_variation_purchasing_discount_rules[<?php echo esc_attr( $object_id ); ?>][membership_plan_id]"
															id="_variation_purchasing_discount_rules_<?php echo esc_attr( $object_id ); ?>_membership_plan_id"
															class="wc-enhanced-select membership-plan wide">
															<?php foreach ( $this->meta_box->get_membership_plan_options() as $id => $label ) : ?>
																<option value="<?php echo esc_attr( $id ); ?>" <?php selected( $id, $variation_rule->get_membership_plan_id() ); ?>><?php echo esc_html( $label ); ?></option>
															<?php endforeach; ?>
														</select>
													</p>
												</td>

												<td class="purchasing-discount-type discount-type-column">
													<p class="form-field">
														<label for="_variation_purchasing_discount_rules<?php echo esc_attr( $object_id ); ?>_discount_type"><?php esc_html_e( 'Type', 'woocommerce-memberships' ); ?>:</label>

														<select
															name="_variation_purchasing_discount_rules[<?php echo esc_attr( $object_id ); ?>][discount_type]"
															id="_variation_purchasing_discount_rules<?php echo esc_attr( $object_id ); ?>_discount_type"
															disabled>
															<?php foreach ( $this->meta_box->get_discount_type_options() as $key => $name ) : ?>
																<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $key, $variation_rule->get_discount_type() ); ?>><?php echo esc_html( $name ); ?></option>
															<?php endforeach; ?>
														</select>
													</p>
												</td>

												<td class="purchasing-discount-amount amount-column">
													<p class="form-field">
														<label for="_variation_purchasing_discount_rules<?php echo esc_attr( $object_id ); ?>_discount_amount"><?php esc_html_e( 'Amount', 'woocommerce-memberships' ); ?>:</label>

														<input
															type="number"
															name="_variation_purchasing_discount_rules[<?php echo esc_attr( $object_id ); ?>][discount_amount]"
															id="_variation_purchasing_discount_rules<?php echo esc_attr( $object_id ); ?>_discount_amount"
															value="<?php echo esc_attr( $variation_rule->get_discount_amount() ); ?>"
															step="<?php echo esc_attr( wc_memberships()->get_rules_instance()->get_discount_rules_precision() ); ?>"
															min="0"
															disabled
														/>
													</p>
												</td>

												<td class="purchasing-discount-active active-columns">
													<p class="form-field">
														<label for="_variation_purchasing_discount_rules<?php echo esc_attr( $object_id ); ?>_discount_active"><?php esc_html_e( 'Active', 'woocommerce-memberships' ); ?>:</label>
														<input
															type="checkbox"
															name="_variation_purchasing_discount_rules[<?php echo esc_attr( $object_id ); ?>][active]"
															id="_variation_purchasing_discount_rules<?php echo esc_attr( $object_id ); ?>_discount_active"
															value="yes"
															<?php checked( $variation_rule->is_active(), true ); ?>
															disabled
														/>
													</p>
												</td>

											</tr>

											<tr class="disabled-notice">
												<td class="check-column"></td>
												<td colspan="5">
													<span class="description"><?php
														/* translators: Placeholders: %1$s - opening HTML <a> tag, %2$s - closing </a> HTML tag */
														printf( esc_html__( 'This rule cannot be edited here. You can %1$sedit this rule on the membership plan screen%2$s.', 'woocommerce-memberships' ),
															'<a href="' . esc_url( get_edit_post_link( $variation_rule->get_membership_plan_id() ) ) . '">',
															'</a>'
														); ?></span>
												</td>
											</tr>

										<?php endif;?>

									<?php endforeach; ?>

								<?php endif; ?>

							<?php endforeach ?>

						<?php endif; ?>

					<?php endif; ?>

					<tr>
						<th colspan="<?php echo $colspan; ?>">
							<button
								type="button"
								class="button button-primary add-rule js-add-rule">
								<?php esc_html_e( 'Add New Discount', 'woocommerce-memberships' ); ?>
							</button>
							<button
								type="button"
								class="button button-secondary remove-rules js-remove-rules
						        <?php if ( count( $purchasing_discount_rules ) < 2 ) : ?>hide<?php endif; ?>">
								<?php esc_html_e( 'Delete Selected', 'woocommerce-memberships' ); ?>
							</button>
						</th>
					</tr>

				</tfoot>

			<?php endif; ?>

		</table>
		<?php
	}


}

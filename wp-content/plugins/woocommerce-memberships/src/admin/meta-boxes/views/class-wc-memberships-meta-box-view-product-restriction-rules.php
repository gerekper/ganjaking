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
 * @copyright Copyright (c) 2014-2022, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

use SkyVerge\WooCommerce\PluginFramework\v5_10_13 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * View for product restriction rules table
 *
 * @since 1.7.0
 */
class WC_Memberships_Meta_Box_View_Product_Restriction_Rules extends \WC_Memberships_Meta_Box_View {


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
		$is_variable_product = $product ? $product->is_type( 'variable' ) : false;
		$colspan             = 'membership_plan' === $context || $is_variable_product ? 6 : 5;

		?>
		<table class="widefat rules product-restriction-rules js-rules">

			<thead>
				<tr>

					<td class="check-column">
						<label class="screen-reader-text" for="product-restriction-rules-select-all"> <?php esc_html_e( 'Select all', 'woocommerce-memberships' ); ?></label>
						<input type="checkbox"
						       id="product-restriction-rules-select-all"
						/>
					</td>

					<?php if ( 'membership_plan' === $context ) : ?>

						<th scope="col" class="product-restriction-content-type content-type-column">
							<?php esc_html_e( 'Type', 'woocommerce-memberships' ); ?>
						</th>

						<th scope="col" class="product-restriction-objects objects-column">
							<?php esc_html_e( 'Title', 'woocommerce-memberships' ); ?>
							<?php echo wc_help_tip( __( 'Search&hellip; or leave blank to apply to all', 'woocommerce-memberships' ) ); ?>
						</th>

					<?php else : ?>

						<?php if ( $is_variable_product ) : ?>

							<th scope="col" class="product-restriction-applies-to product-variation-column">
								<?php esc_html_e( 'Applies to', 'woocommerce-memberships' ); ?>
							</th>

						<?php endif; ?>

						<th scope="col" class="product-restriction-membership-plan membership-plan-column">
							<?php esc_html_e( 'Plan', 'woocommerce-memberships' ); ?>
						</th>

					<?php endif; ?>

					<th scope="col" class="product-restriction-access-type access-type-column">
						<?php esc_html_e( 'Only members can', 'woocommerce-memberships' ); ?>
					</th>

					<th scope="col" class="product-restriction-access-schedule access-schedule-column">
						<?php esc_html_e( 'Accessible', 'woocommerce-memberships' ); ?>
						<?php echo wc_help_tip( __( 'When will members gain access to products?', 'woocommerce-memberships' ) ); ?>
					</th>

				</tr>
			</thead>
			<?php

			// load product restriction rule view object
			require_once( wc_memberships()->get_plugin_path() . '/src/admin/meta-boxes/views/class-wc-memberships-meta-box-view-product-restriction-rule.php' );

			// get applied product restriction rules
			$product_restriction_rules = $this->meta_box->get_product_restriction_rules();

			// output product restriction rule views
			foreach ( $product_restriction_rules as $index => $rule ) {

				$view = new \WC_Memberships_Meta_Box_View_Product_Restriction_Rule( $this->meta_box, $rule );
				$view->output( array( 'index' => $index, 'product' => $product ) );
			}

			// get available membership plans
			$membership_plans = $this->meta_box->get_available_membership_plans();

			?>
			<tbody class="norules <?php if ( count( $membership_plans ) > 0 && count( $product_restriction_rules ) > 1 ) : ?>hide<?php endif; ?>">
				<tr>
					<td colspan="<?php echo $colspan; ?>">
						<?php

						if ( 'membership_plan' === $context ) {

							esc_html_e( 'There are no rules yet. Click below to add one.', 'woocommerce-memberships' );

						} else {

							if ( empty( $membership_plans ) ) {
								/* translators: Placeholder: %s outputs "Add a Membership Plan" action link */
								$add_membership_plan_link = '<a target="_blank" href="' . esc_url( admin_url( 'post-new.php?post_type=wc_membership_plan' ) ) . '">' . esc_html__( 'Add a Membership Plan', 'woocommerce-memberships' ) . '</a>';
								printf( __( 'To create restriction rules, please %s', 'woocommerce-memberships' ), $add_membership_plan_link );
							} elseif ( ! $is_variable_product ) {
								esc_html_e( 'This product can be viewed & purchased by all customers. Add a rule to restrict viewing and/or purchasing to members.', 'woocommerce-memberships' );
							}
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
							$variation_rules[] = $rules->get_product_restriction_rules( $variation_id );
						}

						/* @type $variation_rules \WC_Memberships_Membership_Plan_Rule[] */
						$variation_rules = array_merge( ...$variation_rules );

						?>

						<?php if ( ! empty( $variation_rules ) ) : ?>

							<?php foreach ( $variation_rules as $variation_rule ) : ?>

								<?php $object_ids = $variation_rule->get_object_ids(); ?>
								<?php if ( ! empty( $object_ids ) ) : ?>

									<?php foreach ( $object_ids as $object_id ) : ?>

										<?php if ( in_array( $object_id, $variation_ids, false ) && ( $variation = wc_get_product( $object_id ) ) ) : ?>

											<tr>

												<th scope="row" class="check-column"></th>

												<td class="product-restriction-applies-to product-variation-column">
													<p class="form-field"><?php echo $variation->get_formatted_name(); ?></p>
												</td>

												<td class="product-restriction-membership-plan membership-plan-column">
													<p class="form-field">
														<label for="_variation_product_restriction_rules_<?php echo esc_attr( $object_id ); ?>_membership_plan_id"><?php esc_html_e( 'Plan', 'woocommerce-memberships' ); ?>:</label>

														<select
															name="_variation_product_restriction_rules[<?php echo esc_attr( $object_id ); ?>][membership_plan_id]"
															id="_variation_product_restriction_rules_<?php echo esc_attr( $object_id ); ?>_membership_plan_id"
															class="wc-enhanced-select membership-plan wide"
															style="width:90%;"
															disabled>
															<?php foreach ( $this->meta_box->get_membership_plan_options() as $id => $label ) : ?>
																<option value="<?php echo esc_attr( $id ); ?>" <?php selected( $id, $variation_rule->get_membership_plan_id() ); ?>><?php echo esc_html( $label ); ?></option>
															<?php endforeach; ?>
														</select>
													</p>
												</td>

												<td class="product-restriction-access-type access-type-column">
													<p class="form-field">
														<label for="_variation_product_restriction_rules_<?php echo esc_attr( $object_id ); ?>_access_type"><?php esc_html_e( 'Only members can', 'woocommerce-memberships' ); ?>:</label>

														<select
															name="_variation_product_restriction_rules[<?php echo esc_attr( $object_id ); ?>][access_type]"
															id="_variation_product_restriction_rules_<?php echo esc_attr( $object_id ); ?>_access_type"
															disabled>
															<?php foreach ( $this->meta_box->get_product_restriction_access_type_options() as $key => $label ) : ?>
																<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $key, $variation_rule->get_access_type() ); ?>><?php echo esc_html( $label ); ?></option>
															<?php endforeach; ?>
														</select>
													</p>
												</td>

												<td class="product-restriction-access-schedule access-schedule-column">
													<p class="form-field">
														<label><?php esc_html_e( 'Accessible', 'woocommerce-memberships' ); ?>:</label>

														<span class="rule-control-group">
															<?php

															$current_access_period = $variation_rule->grants_immediate_access() ? 'immediate' : 'specific';

															foreach ( $this->meta_box->get_access_schedule_period_options() as $value => $label ) :

																?>
																<label class="label-radio">
																	<input
																		type="radio"
																		name="_variation_product_restriction_rules[<?php echo esc_attr( $object_id ); ?>][access_schedule]"
																		class="js-access-schedule-period-selector js-schedule-type"
																		value="<?php echo esc_attr( $value ); ?>"
																		<?php checked( $value, $current_access_period ); ?>
																		disabled
																	/>
																	<?php echo esc_html( $label ); ?>
																</label>
																<?php

															endforeach;

															?>
														</span>

														<span class="rule-control-group rule-control-group-access-schedule-specific js-hide-if-access-schedule-immediate <?php if ( 'immediate' === $variation_rule->get_access_schedule() ) : ?>hide<?php endif;?>">
															<?php

															ob_start();

															?>
															<input
																type="number"
																name="_variation_product_restriction_rules[<?php echo esc_attr( $object_id ); ?>][access_schedule_amount]"
																id="_variation_product_restriction_rules_<?php echo esc_attr( $object_id ); ?>_access_schedule_amount"
																class="access_schedule-amount"
																value="<?php echo esc_attr( $variation_rule->get_access_schedule_amount() ); ?>"
																min="0"
																disabled
															/>
															<?php

															$amount = ob_get_clean();

															ob_start();

															?>
															<select
																name="_variation_product_restriction_rules[<?php echo esc_attr( $object_id ); ?>][access_schedule_period]"
																class="access_schedule-period js-access-schedule-period-selector"
																disabled>
																<?php foreach ( $this->meta_box->get_access_period_options() as $key => $label ) : ?>
																	<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $key, $variation_rule->get_access_schedule_period() ); ?>><?php echo esc_html( strtolower( $label ) ); ?></option>
																<?php endforeach; ?>
															</select>
															<?php

															$period = ob_get_clean();

															/* translators: Placeholders: %1$s - an opening HTML tag, %2$s - a closing HTML tag, %3$s - a numeric value, %4$s - a period, such as day(s), month(s), etc. - The result will look something like: "after 3 day(s)" */
															printf( __( '%1$safter%2$s %3$s %4$s' ),
																'<label for="_product_restriction_rules_' . esc_attr( $object_id ) . '_access_schedule_amount" class="access_schedule-amount-label">',
																'</label>',
																$amount, // already escaped
																$period  // already escaped
															);

															?>
														</span>
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
								class="button button-primary add-rule js-add-rule"><?php esc_html_e( 'Add New Rule', 'woocommerce-memberships' ); ?></button>
							<button
								type="button"
								class="button button-secondary remove-rules js-remove-rules
						        <?php if ( count( $product_restriction_rules ) < 2 ) : ?>hide<?php endif; ?>"><?php esc_html_e( 'Delete Selected', 'woocommerce-memberships' ); ?></button>
						</th>
					</tr>

				</tfoot>

			<?php endif; ?>

		</table>
		<?php
	}


}

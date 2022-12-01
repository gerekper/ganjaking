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
 * View for a product restriction rule
 *
 * @since 1.7.0
 */
class WC_Memberships_Meta_Box_View_Product_Restriction_Rule extends \WC_Memberships_Meta_Box_View {


	/**
	 * HTML Output
	 *
	 * @since 1.7.0
	 * @param array $args
	 */
	public function output( $args = array() ) {

		$index               = $this->get_rule_index( $args );
		$context             = (int) $this->rule->get_membership_plan_id() === (int) $this->post->ID ? 'membership_plan' : 'product';
		$product             = isset( $args['product'] ) ? $args['product'] : null;
		$is_variable_product = false;

		if ( 'product' === $context && $product instanceof \WC_Product ) {
			$is_variable_product = $product->is_type( 'variable' );
		}

		?>
		<tbody
			class="rule product-restriction-rule product-restriction-rule-<?php echo esc_attr( $index ); ?>
			<?php if ( ! $this->rule->current_user_can_edit() || ! $this->rule->current_context_allows_editing() ) : ?>disabled<?php endif; ?>"
			<?php echo $this->rule->is_trashed() ? 'style="display: none; height: 0;"' : ''; ?>>

			<tr>

				<th scope="row" class="check-column">
					<p class="form-field">
						<label for="_product_restriction_rules_<?php echo esc_attr( $index ); ?>_checkbox"><?php esc_html_e( 'Select', 'woocommerce-memberships' ); ?>:</label>

						<?php if ( $this->rule->current_user_can_edit() && $this->rule->current_context_allows_editing() ) : ?>

							<input
								type="checkbox"
								id="_product_restriction_rules_<?php echo esc_attr( $index ); ?>_checkbox"
							/>

						<?php endif; ?>

						<input
							type="hidden"
							name="_product_restriction_rules[<?php echo esc_attr( $index ); ?>][membership_plan_id]"
							value="<?php echo esc_attr( $this->rule->get_membership_plan_id() ); ?>"
						/>
						<input
							type="hidden"
							name="_product_restriction_rules[<?php echo esc_attr( $index ); ?>][id]"
							class="js-rule-id"
							value="<?php echo esc_attr( $this->rule->get_id() ); ?>"
						/>
						<input
							type="hidden"
							name="_product_restriction_rules[<?php echo esc_attr( $index ); ?>][remove]"
							class="js-rule-remove"
							value=""
						/>

						<?php if ( 'product' === $context && $this->rule->has_objects() ) : ?>

							<?php foreach ( $this->rule->get_object_ids() as $id ) : ?>

								<input
									type="hidden"
									name="_product_restriction_rules[<?php echo esc_attr( $index ); ?>][object_ids][]"
									value="<?php echo esc_attr( $id ); ?>"
								/>

							<?php endforeach; ?>

						<?php endif; ?>

					</p>
				</th>

				<?php if ( 'membership_plan' === $context ) : ?>

					<td class="product-restriction-content-type content-type-column">
						<p class="form-field">
							<label for="_product_restriction_rules_<?php echo esc_attr( $index ); ?>_content_type_key"><?php esc_html_e( 'Type', 'woocommerce-memberships' ); ?>:</label>

							<?php $product_restriction_content_type_options = $this->meta_box->get_product_restriction_content_type_options(); ?>

							<select
								name="_product_restriction_rules[<?php echo esc_attr( $index ); ?>][content_type_key]"
								id="_product_restriction_rules_<?php echo esc_attr( $index ); ?>_content_type_key"
								class="js-content-type"
								<?php if ( ! $this->rule->current_user_can_edit() ) : ?>disabled<?php endif; ?>>

								<?php foreach ( $product_restriction_content_type_options['post_types'] as $key => $post_type ) : ?>
									<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $key, $this->rule->get_content_type_key() ); ?> <?php if ( ! ( current_user_can( $post_type->cap->edit_posts ) && current_user_can( $post_type->cap->edit_others_posts ) ) ) : ?>disabled<?php endif; ?>><?php echo esc_html( $post_type->label ); ?></option>
								<?php endforeach; ?>

								<?php foreach ( $product_restriction_content_type_options['taxonomies'] as $key => $taxonomy ) : ?>
									<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $key, $this->rule->get_content_type_key() ); ?> <?php if ( ! ( current_user_can( $taxonomy->cap->manage_terms ) && current_user_can( $taxonomy->cap->edit_terms ) ) ) : ?>disabled<?php endif; ?> ><?php echo esc_html( $taxonomy->label ); ?></option>
								<?php endforeach; ?>

								<?php if ( ! $this->rule->is_new() && ! wc_memberships()->get_rules_instance()->rule_content_type_exists( $this->rule )  ) : ?>
									<option value="<?php echo esc_attr( $this->rule->get_content_type_key() ); ?>" selected><?php echo esc_html( $this->rule->get_content_type_key() ); ?></option>
								<?php endif; ?>

							</select>
						</p>
					</td>

					<td class="product-restriction-objects objects-column">
						<p class="form-field">
							<label for="_product_restriction_rules_<?php echo esc_attr( $index ); ?>_object_ids"><?php esc_html_e( 'Title', 'woocommerce-memberships' ); ?>:</label>
							<select
								name="_product_restriction_rules[<?php echo esc_attr( $index ); ?>][object_ids][]"
								id="_product_restriction_rules_<?php echo esc_attr( $index ); ?>_object_ids"
								class="wc-memberships-object-search js-object-ids"
								style="width: 90%;"
								multiple="multiple"
								data-placeholder="<?php esc_attr_e( 'Search&hellip; or leave blank to apply to all', 'woocommerce-memberships' ); ?>"
								data-action="<?php echo esc_attr( \WC_Memberships_Admin_Membership_Plan_Rules::get_rule_object_search_action( $this->rule ) ); ?>"
								<?php if ( ! $this->rule->current_user_can_edit() ) : ?>disabled<?php endif; ?>>
								<?php if ( $this->rule->has_objects() ) : ?>
									<?php foreach ( $this->rule->get_object_ids() as $object_id ) : ?>
										<?php if ( $object_label = \WC_Memberships_Admin_Membership_Plan_Rules::get_rule_object_label( $this->rule, $object_id, true ) ) : ?>
											<option value="<?php echo esc_attr( $object_id ); ?>" selected><?php echo esc_html( $object_label ); ?></option>
										<?php endif; ?>
									<?php endforeach; ?>
								<?php endif; ?>
							</select>
						</p>
					</td>

				<?php else : ?>

					<?php if ( $is_variable_product ) : ?>

						<td class="product-restriction-applies-to product-variation-column">
							<p class="form-field"><?php esc_html_e( 'Any variation', 'woocommerce-memberships' ); ?></p>
						</td>

					<?php endif; ?>

					<td class="product-restriction-membership-plan membership-plan-column">
						<p class="form-field">
							<label for="_product_restriction_rules_<?php echo esc_attr( $index ); ?>_membership_plan_id"><?php esc_html_e( 'Plan', 'woocommerce-memberships' ); ?>:</label>

							<select
								name="_product_restriction_rules[<?php echo esc_attr( $index ); ?>][membership_plan_id]"
								id="_product_restriction_rules_<?php echo esc_attr( $index ); ?>_membership_plan_id"
								class="<?php echo '__INDEX__' !== $index ? 'wc-enhanced-select' : ''; ?> membership-plan wide"
								style="width:90%;"
								<?php if ( ! $this->rule->current_user_can_edit() || ! $this->rule->current_context_allows_editing() ) : ?>disabled<?php endif; ?>>
								<?php foreach ( $this->meta_box->get_membership_plan_options() as $id => $label ) : ?>
									<option value="<?php echo esc_attr( $id ); ?>" <?php selected( $id, $this->rule->get_membership_plan_id() ); ?>><?php echo esc_html( $label ); ?></option>
								<?php endforeach; ?>
							</select>
						</p>
					</td>

				<?php endif; ?>

				<td class="product-restriction-access-type access-type-column">
					<p class="form-field">
						<label for="_product_restriction_rules_<?php echo esc_attr( $index ); ?>_access_type"><?php esc_html_e( 'Only members can', 'woocommerce-memberships' ); ?>:</label>

						<select
							name="_product_restriction_rules[<?php echo esc_attr( $index ); ?>][access_type]"
							id="_product_restriction_rules_<?php echo esc_attr( $index ); ?>_access_type"
							<?php if ( ! $this->rule->current_user_can_edit() || ! $this->rule->current_context_allows_editing() ) : ?>disabled<?php endif; ?>>
							<?php foreach ( $this->meta_box->get_product_restriction_access_type_options() as $key => $label ) : ?>
								<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $key, $this->rule->get_access_type() ); ?>><?php echo esc_html( $label ); ?></option>
							<?php endforeach; ?>
						</select>
					</p>
				</td>

				<td class="product-restriction-access-schedule access-schedule-column">
					<p class="form-field">
						<label><?php esc_html_e( 'Accessible', 'woocommerce-memberships' ); ?>:</label>

						<span class="rule-control-group">
							<?php

							$current_access_period = $this->rule->grants_immediate_access() ? 'immediate' : 'specific';

							foreach ( $this->meta_box->get_access_schedule_period_options() as $value => $label ) :

								?>
								<label class="label-radio">
									<input
										type="radio"
										name="_product_restriction_rules[<?php echo esc_attr( $index ); ?>][access_schedule]"
										class="js-access-schedule-period-selector js-schedule-type"
										value="<?php echo esc_attr( $value ); ?>"
										<?php checked( $value, $current_access_period ); ?>
										<?php if ( ! $this->rule->current_user_can_edit() || ! $this->rule->current_context_allows_editing() ) : ?>disabled<?php endif; ?>
									/>
									<?php echo esc_html( $label ); ?>
								</label>
								<?php

							endforeach;

							?>
						</span>

						<span class="rule-control-group rule-control-group-access-schedule-specific js-hide-if-access-schedule-immediate <?php if ( 'immediate' === $this->rule->get_access_schedule() ) : ?>hide<?php endif;?>">
							<?php

							ob_start();

							?>
							<input
								type="number"
								name="_product_restriction_rules[<?php echo esc_attr( $index ); ?>][access_schedule_amount]"
								id="_product_restriction_rules_<?php echo esc_attr( $index ); ?>_access_schedule_amount"
								class="access_schedule-amount"
								value="<?php echo esc_attr( $this->rule->get_access_schedule_amount() ); ?>"
								min="0"
								<?php if ( ! $this->rule->current_user_can_edit() || ! $this->rule->current_context_allows_editing() ) : ?>disabled<?php endif; ?>
							/>
							<?php

							$amount = ob_get_clean();

							ob_start();

							?>
							<select
								name="_product_restriction_rules[<?php echo esc_attr( $index ); ?>][access_schedule_period]"
								class="access_schedule-period js-access-schedule-period-selector"
								<?php if ( ! $this->rule->current_user_can_edit() || ! $this->rule->current_context_allows_editing() ) : ?>disabled<?php endif; ?>>
								<?php foreach ( $this->meta_box->get_access_period_options() as $key => $label ) : ?>
									<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $key, $this->rule->get_access_schedule_period() ); ?>><?php echo esc_html( strtolower( $label ) ); ?></option>
								<?php endforeach; ?>
							</select>
							<?php

							$period = ob_get_clean();

							/* translators: Placeholders: %1$s - an opening HTML tag, %2$s - a closing HTML tag, %3$s - a numeric value, %4$s - a period, such as day(s), month(s), etc. - The result will look something like: "after 3 day(s)" */
							printf( __( '%1$safter%2$s %3$s %4$s' ),
								'<label for="_product_restriction_rules_' . esc_attr( $index ) . '_access_schedule_amount" class="access_schedule-amount-label">',
								'</label>',
								$amount, // already escaped
								$period  // already escaped
							);

							?>
						</span>

						<?php

						/** This action is documented in class-wc-memberships-meta-box-view-content-restriction-rule.php **/
						do_action( 'wc_memberships_restriction_rule_access_schedule_field', $this->rule, $index );

						?>
					</p>
				</td>

			</tr>

			<?php if ( ! $this->rule->current_user_can_edit() || ! $this->rule->current_context_allows_editing() ) : ?>

				<tr class="disabled-notice" <?php echo $this->rule->is_trashed() ? 'style="display: none; height: 0;"' : ''; ?>>
					<td class="check-column"></td>
					<td colspan="<?php echo 'membership_plan' === $context || $is_variable_product ? 4 : 3; ?>">

						<?php if ( ! $this->rule->is_new() && ! wc_memberships()->get_rules_instance()->rule_content_type_exists( $this->rule )  ) : ?>

							<span class="description"><?php esc_html_e( 'This rule applies to content generated by a plugin or theme that has been deactivated or deleted.', 'woocommerce-memberships' ); ?></span>

						<?php elseif ( ! $this->rule->current_user_can_edit() ) : ?>

							<span class="description"><?php esc_html_e( 'You are not allowed to edit this rule', 'woocommerce-memberships' ); ?></span>

						<?php else : ?>

							<span class="description"><?php
								/* translators: Placeholders: %1$s - opening HTML <a> tag, %2$s - closing </a> HTML tag */
								printf( __( 'This rule cannot be edited here because it applies to multiple products. You can %1$sedit this rule on the membership plan screen%2$s.', 'woocommerce-memberships' ),
									'<a href="' . esc_url( get_edit_post_link( $this->rule->get_membership_plan_id() ) ) . '">', '</a>'
								); ?></span>

						<?php endif; ?>

					</td>
				</tr>

			<?php endif; ?>

		</tbody>
		<?php
	}


}

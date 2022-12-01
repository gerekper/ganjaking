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
 * View for a content restriction rule
 *
 * @since 1.7.0
 */
class WC_Memberships_Meta_Box_View_Content_Restriction_Rule extends \WC_Memberships_Meta_Box_View {


	/**
	 * HTML Output
	 *
	 * @since 1.7.0
	 * @param array $args
	 */
	public function output( $args = array() ) {

		$context = $this->rule->get_membership_plan_id() === (int) $this->post->ID ? 'membership_plan' : 'post';
		$index   = $this->get_rule_index( $args );

		?>
		<tbody
			class="rule content-restriction-rule content-restriction-rule-<?php echo esc_attr( $index ); ?>
			<?php if ( ! $this->rule->current_user_can_edit() || ! $this->rule->current_context_allows_editing() ) : ?>disabled<?php endif; ?>"
			<?php echo $this->rule->is_trashed() ? 'style="display: none; height: 0;"' : ''; ?>>

			<tr>

				<th class="check-column">
					<p class="form-field">
						<label for="_content_restriction_rules_<?php echo esc_attr( $index ); ?>_checkbox"><?php esc_html_e( 'Select', 'woocommerce-memberships' ); ?>:</label>

						<?php if ( ( $this->rule->current_user_can_edit() && $this->rule->current_context_allows_editing() ) || ! wc_memberships()->get_rules_instance()->rule_content_type_exists( $this->rule ) ) : ?>

							<input
								type="checkbox"
								id="_content_restriction_rules_<?php echo esc_attr( $index ); ?>_checkbox"
							/>

						<?php endif; ?>

						<input
							type="hidden"
							name="_content_restriction_rules[<?php echo esc_attr( $index ); ?>][membership_plan_id]"
							value="<?php echo esc_attr( $this->rule->get_membership_plan_id() ); ?>"
						/>
						<input
							type="hidden"
							name="_content_restriction_rules[<?php echo esc_attr( $index ); ?>][id]"
							class="js-rule-id"
							value="<?php echo esc_attr( $this->rule->get_id() ); ?>"
						/>
						<input
							type="hidden"
							name="_content_restriction_rules[<?php echo esc_attr( $index ); ?>][remove]"
							class="js-rule-remove"
							value=""
						/>

						<?php if ( 'post' === $context && $this->rule->has_objects() ) : ?>

							<?php foreach ( $this->rule->get_object_ids() as $id ) : ?>

								<input
									type="hidden"
									name="_content_restriction_rules[<?php echo esc_attr( $index ); ?>][object_ids][]"
									value="<?php echo esc_attr( $id ); ?>"
								/>

							<?php endforeach; ?>

						<?php endif; ?>
					</p>
				</th>

				<?php if ( 'membership_plan' === $context ) : ?>

					<td class="content-restriction-content-type content-type-column">
						<p class="form-field">
							<label for="_content_restriction_rules_<?php echo esc_attr( $index ); ?>_content_type_key"><?php esc_html_e( 'Type', 'woocommerce-memberships' ); ?>:</label>

							<?php

							// place post types and taxonomies into separate option groups, so that they are easier to distinguish
							$content_restriction_content_type_options = array(
								'post_types' => array(),
								'taxonomies' => array(),
							);

							// afterwards we need to prefix post_type/taxonomy names (values) with | pipes,
							// so that if a post type and taxonomy share a name we can still distinguish between them
							foreach ( \WC_Memberships_Admin_Membership_Plan_Rules::get_valid_post_types_for_content_restriction_rules() as $post_type_name => $post_type ) {
								$content_restriction_content_type_options['post_types'][ 'post_type|' . $post_type_name ] = $post_type;
							}
							foreach ( \WC_Memberships_Admin_Membership_Plan_Rules::get_valid_taxonomies_for_content_restriction_rules() as $taxonomy_name => $taxonomy ) {
								$content_restriction_content_type_options['taxonomies'][ 'taxonomy|' . $taxonomy_name ]   = $taxonomy;
							}

							?>

							<select
								name="_content_restriction_rules[<?php echo esc_attr( $index ); ?>][content_type_key]"
								id="_content_restriction_rules_<?php echo esc_attr( $index ); ?>_content_type_key"
								class="js-content-type" <?php if ( ! $this->rule->current_user_can_edit() ) : ?>disabled<?php endif; ?>>

								<optgroup label="<?php esc_attr_e( 'Post types', 'woocommerce-memberships' ); ?>">
									<?php foreach ( $content_restriction_content_type_options['post_types'] as $key => $post_type ) : ?>
										<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $key, $this->rule->get_content_type_key() ); ?> <?php if ( ! ( current_user_can( $post_type->cap->edit_posts ) && current_user_can( $post_type->cap->edit_others_posts ) ) ) : ?>disabled<?php endif; ?>><?php echo esc_html( $post_type->label ); ?></option>
									<?php endforeach; ?>
								</optgroup>

								<optgroup label="<?php esc_attr_e( 'Taxonomies', 'woocommerce-memberships' ); ?>">
									<?php foreach ( $content_restriction_content_type_options['taxonomies'] as $key => $taxonomy ) : ?>
										<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $key, $this->rule->get_content_type_key() ); ?> <?php if ( ! ( current_user_can( $taxonomy->cap->manage_terms ) && current_user_can( $taxonomy->cap->edit_terms ) ) ) : ?>disabled<?php endif; ?> ><?php echo esc_html( $taxonomy->label ); ?></option>
									<?php endforeach; ?>
								</optgroup>

								<?php if ( ! $this->rule->is_new() && ! wc_memberships()->get_rules_instance()->rule_content_type_exists( $this->rule ) ) : ?>
									<option value="<?php echo esc_attr( $this->rule->get_content_type_key() ); ?>" selected><?php echo esc_html( $this->rule->get_content_type_key() ); ?></option>
								<?php endif; ?>

							</select>

						</p>
					</td>

					<td class="content-restriction-objects objects-column">
						<p class="form-field">
							<label for="_content_restriction_rules_<?php echo esc_attr( $index ); ?>_object_ids"><?php esc_html_e( 'Title', 'woocommerce-memberships' ); ?>:</label>
							<select
								name="_content_restriction_rules[<?php echo esc_attr( $index ); ?>][object_ids][]"
								id="_content_restriction_rules_<?php echo esc_attr( $index ); ?>_object_ids"
								class="wc-memberships-object-search js-object-ids"
								style="width: 90%;"
								multiple="multiple"
								data-placeholder="<?php esc_attr_e( 'Search&hellip; or leave blank to apply to all', 'woocommerce-memberships' ); ?>"
								data-action="<?php echo esc_attr( \WC_Memberships_Admin_Membership_Plan_Rules::get_rule_object_search_action( $this->rule ) ); ?>"
								<?php if ( ! $this->rule->current_user_can_edit() ) : ?>disabled<?php endif; ?>>
								<?php if ( $this->rule->has_objects() ) : ?>
									<?php foreach ( $this->rule->get_object_ids() as $object_id ) : ?>
										<?php if ( $object_label = \WC_Memberships_Admin_Membership_Plan_Rules::get_rule_object_label( $this->rule, $object_id, true ) ) : ?>
											<option value="<?php echo $object_id; ?>" selected><?php echo esc_html( $object_label ); ?></option>
										<?php endif; ?>
									<?php endforeach; ?>
								<?php endif; ?>
							</select>
						</p>
					</td>

				<?php else : ?>

					<td class="content-restriction-membership-plan membership-plan-column">
						<p class="form-field">
							<label for="_content_restriction_rules_<?php echo esc_attr( $index ); ?>_membership_plan_id"><?php esc_html_e( 'Plan', 'woocommerce-memberships' ); ?>:</label>

							<select
								name="_content_restriction_rules[<?php echo esc_attr( $index ); ?>][membership_plan_id]"
								id="_content_restriction_rules_<?php echo esc_attr( $index ); ?>_membership_plan_id"
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

				<td class="content-restriction-access-schedule access-schedule-column">
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
										name="_content_restriction_rules[<?php echo esc_attr( $index ); ?>][access_schedule]"
										class="js-access-schedule-period-selector js-schedule-type"
										value="<?php echo esc_attr( $value ); ?>"
										<?php checked( $value, $current_access_period ); ?>
										<?php if ( ! $this->rule->current_user_can_edit() || ! $this->rule->current_context_allows_editing() ) : ?> disabled<?php endif; ?>
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
								name="_content_restriction_rules[<?php echo esc_attr( $index ); ?>][access_schedule_amount]"
								id="_content_restriction_rules_<?php echo esc_attr( $index ); ?>_access_schedule_amount"
								class="access_schedule-amount"
								value="<?php echo esc_html( $this->rule->get_access_schedule_amount() ); ?>"
								min="0"
								<?php if ( ! $this->rule->current_user_can_edit() || ! $this->rule->current_context_allows_editing() ) : ?>disabled<?php endif; ?>
							/>
							<?php

							$amount = ob_get_clean();

							ob_start();

							?>
							<select name="_content_restriction_rules[<?php echo esc_attr( $index ); ?>][access_schedule_period]"
							        class="access_schedule-period js-access-schedule-period-selector"
							        <?php if ( ! $this->rule->current_user_can_edit() || ! $this->rule->current_context_allows_editing() ) : ?>disabled<?php endif; ?>>
								<?php foreach ( $this->meta_box->get_access_period_options() as $key => $label ) : ?>
									<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $key, $this->rule->get_access_schedule_period() ); ?>><?php echo esc_html( strtolower( $label ) ); ?></option>
								<?php endforeach; ?>
							</select>
							<?php

							$period = ob_get_clean();

							/* translators: Placeholders: %1$s - an opening HTML tag, %2$s - a closing HTML tag, %3$s - a numeric value, %4$s - a period, such as day(s), month(s), etc. - The result will look something like: "after 3 day(s)" */
							printf( __( '%1$safter%2$s %3$s %4$s', 'woocommerce-memberships' ),
								'<label for="_content_restriction_rules_' . esc_attr( $index ) . '_access_schedule_amount" class="access_schedule-amount-label">',
								'</label>',
								$amount, // already escaped
								$period  // already escaped
							);

							?>
						</span>

						<?php

						/**
						 * Fires after the access schedule field is displayed for a restriction rule
						 *
						 * @since 1.0.0
						 * @param \WC_Memberships_Membership_Plan_Rule $rule
						 * @param int $index Row index
						 */
						do_action( 'wc_memberships_restriction_rule_access_schedule_field', $this->rule, $index );

						?>
					</p>
				</td>

			</tr>

			<?php if ( ! $this->rule->current_user_can_edit() || ! $this->rule->current_context_allows_editing() ) : ?>

				<tr class="disabled-notice" <?php echo $this->rule->is_trashed() ? 'style="display: none; height: 0;"' : ''; ?>>

					<td class="check-column"></td>
					<td colspan="<?php echo ( 'wc_membership_plan' === $this->post->post_type ) ? 4 : 3; ?>">

						<?php if ( ! $this->rule->is_new() && ! wc_memberships()->get_rules_instance()->rule_content_type_exists( $this->rule ) ) : ?>

							<span class="description"><?php esc_html_e( 'This rule applies to a content type created by a plugin or theme that has been deactivated or deleted.', 'woocommerce-memberships' ); ?></span>

						<?php elseif ( ! $this->rule->current_user_can_edit() ) : ?>

							<span class="description"><?php esc_html_e( 'You are not allowed to edit this rule.', 'woocommerce-memberships' ); ?></span>

						<?php else : ?>

							<span class="description"><?php
								/* translators: Placeholders: %1$s - opening HTML <a> tag, %2$s - closing </a> HTML tag */
								printf( __( 'This rule cannot be edited here because it applies to multiple content objects. You can %1$sedit this rule on the membership plan screen%2$s.', 'woocommerce-memberships' ),
									'<a href="' . esc_url( get_edit_post_link( $this->rule->get_membership_plan_id() ) ) . '">', '</a>' );
								?></span>

						<?php endif; ?>

					</td>

				</tr>

			<?php endif; ?>

		</tbody>
		<?php
	}


}

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
 * @copyright Copyright (c) 2014-2024, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

/**
 * Renders the product discounts available from the membership in the my account area.
 *
 * @type \WC_Memberships_User_Membership $customer_membership User Membership object
 * @type \WP_Query $discounted_products Query results of products post objects discounted by the membership
 * @type int $user_id The current user ID
 *
 * @version 1.13.0
 * @since 1.4.0
 */

if ( empty ( $discounted_products->posts ) ) :

	?>
	<p><?php esc_html_e( 'There are no discounts available for this membership.', 'woocommerce-memberships' ); ?></p>
	<?php

else :

	?>
	<table class="shop_table shop_table_responsive my_account_orders my_account_memberships my_membership_discounts">

		<thead>
			<tr>
				<?php

				/**
				 * Filters the Discounts table columns in Members Area.
				 *
				 * @since 1.4.0
				 *
				 * @param array $my_membership_discounts_columns associative array of column ids and names
				 * @param int $user_id the member ID
				 */
				$my_membership_discounts_columns = (array) apply_filters( 'wc_memberships_members_area_my_membership_discounts_column_names', array(
					'membership-discount-image'   => '&nbsp;' ,
					'membership-discount-title'   => wc_memberships_get_members_area_sorting_link( 'title', __( 'Title', 'woocommerce-memberships' ) ),
					'membership-discount-amount'  => esc_html__( 'Discount', 'woocommerce-memberships' ),
					'membership-discount-price'   => esc_html__( 'My Price', 'woocommerce-memberships' ),
					'membership-discount-excerpt' => esc_html__( 'Description', 'woocommerce-memberships' ),
					'membership-discount-actions' => wc_memberships_get_members_area_page_links( $customer_membership->get_plan(), 'my-membership-discounts', $discounted_products ),
				), $user_id );

				?>
				<?php foreach ( $my_membership_discounts_columns as $column_id => $column_header ) : ?>
					<th class="<?php echo esc_attr( $column_id ); ?>"><span class="nobr"><?php echo $column_header; ?></span></th>
				<?php endforeach; ?>
			</tr>
		</thead>

		<tbody>
			<?php $available_discounts = 0; ?>
			<?php foreach ( $discounted_products->posts as $discounted_product ) : ?>

				<?php

				$product = wc_get_product( $discounted_product );

				if ( ! $product ) {
					continue;
				}

				if ( $product->is_type( 'variation' ) ) {
					$parent      = wc_get_product( $product->get_parent_id( 'edit' ) );
					$product_id  = $parent ? $parent->get_id() : 0;
					$the_product = $parent ?: null;
				} else {
					$product_id  = $product->get_id();
					$the_product = $product;
				}

				// customer capabilities
				$can_view_product     = wc_memberships_user_can( $user_id, 'view' , array( 'product' => $product_id ) );
				$can_purchase_product = wc_memberships_user_can( $user_id, 'purchase', array( 'product' => $product_id ) );
				$purchase_start_time  = wc_memberships_get_user_access_start_time( $user_id, 'purchase', array( 'product' => $product_id ) );
				$can_have_discount    = wc_memberships_user_has_member_discount( $the_product );

				/**
				 * Toggles whether to show only active discounts.
				 *
				 * By default we show only the active discounts, but third parties can filter this to include also discounts that aren't currently active.
				 *
				 * @since 1.4.0
				 *
				 * @param bool $show_only_active_discounts whether to show only active discounts (default true, show only active)
				 * @param int $user_id the member ID
				 * @param int $product_id the ID of the product the discounts are for
				 */
				$show_only_active_discounts = (bool) apply_filters( 'wc_memberships_members_area_show_only_active_discounts', true, $user_id, $product_id );

				if ( $show_only_active_discounts && ! $can_have_discount ) {
					continue;
				}

				$available_discounts++;

				?>
				<tr class="membership-discount">
					<?php foreach ( $my_membership_discounts_columns as $column_id => $column_header ) : ?>

						<?php if ( 'membership-discount-image' === $column_id ) : ?>

							<td class="membership-discount-image" style="min-width: 84px;" data-title="<?php esc_attr_e( 'Image', 'woocommerce-memberships' ); ?>">
								<?php if ( $can_view_product ) : ?>
									<a href="<?php echo esc_url( get_permalink( $product_id ) ); ?>"><?php echo $product->get_image(); ?></a>
								<?php else : ?>
									<?php echo wc_placeholder_img( 'shop_thumbnail' ); ?>
								<?php endif; ?>
							</td>

						<?php elseif ( 'membership-discount-title' === $column_id ) : ?>

							<td class="membership-discount-title" data-title="<?php esc_attr_e( 'Title', 'woocommerce-memberships' ); ?>">
								<?php if ( $can_view_product ) : ?>
									<a href="<?php echo esc_url( get_permalink( $product_id ) ); ?>"><?php echo esc_html( wp_strip_all_tags( $product->get_title() ) ); ?></a>
								<?php else : ?>
									<?php echo esc_html( wp_strip_all_tags( $product->get_title() ) ); ?>
								<?php endif; ?>
							</td>

						<?php elseif ( 'membership-discount-amount' === $column_id ) : ?>

							<td class="membership-discount-amount" data-title="<?php echo esc_attr( $column_header ); ?>">
								<?php if ( $can_have_discount ) : ?>
									<?php echo wp_kses_post( wc_memberships_get_member_product_discount( $customer_membership, $product, true ) ); ?>
								<?php else : ?>
									<time datetime="<?php echo date( 'Y-m-d', $purchase_start_time ); ?>" title="<?php echo esc_attr( $purchase_start_time ); ?>">
										<?php /* translators: discount available from date */ ?>
										<?php echo esc_html( sprintf( __( 'Available from %s', 'woocommerce-memberships' ), date_i18n( get_option( 'date_format' ), $purchase_start_time ) ) ); ?>
									</time>
								<?php endif; ?>
							</td>

						<?php elseif ( 'membership-discount-price' === $column_id ) : ?>

							<td class="membership-product-price" data-title="<?php echo esc_attr( $column_header ); ?>">
								<?php if ( $can_view_product ) : ?>
									<?php echo wp_kses_post( $product->get_price_html() ); ?>
								<?php else : ?>
									<span>&ndash;</span>
								<?php endif; ?>
							</td>

						<?php elseif ( 'membership-discount-excerpt' === $column_id ) : ?>

							<td class="membership-product-excerpt" data-title="<?php echo esc_attr( $column_header ); ?>">
								<?php if ( empty( $discounted_product->post_excerpt ) ) : ?>
									<?php echo wp_kses_post( wp_trim_words( strip_shortcodes( $discounted_product->post_content ), 20 ) ); ?>
								<?php else : ?>
									<?php echo wp_kses_post( wp_trim_words( $discounted_product->post_excerpt, 20 ) ); ?>
								<?php endif; ?>
							</td>

						<?php elseif ( 'membership-discount-actions' === $column_id ) : ?>

							<td class="membership-discount-actions order-actions" data-title="<?php echo esc_attr( $column_header ); ?>">
								<?php echo wc_memberships_get_members_area_action_links( 'my-membership-discounts', $customer_membership, $product ); ?>
							</td>

						<?php else : ?>

							<td class="<?php echo esc_attr( $column_id ); ?>" data-title="<?php echo esc_attr( $column_header ); ?>">
								<?php do_action( 'wc_memberships_members_area_my_membership_discounts_column_' . $column_id, $product ); ?>
							</td>

						<?php endif; ?>

					<?php endforeach; ?>
				</tr>

			<?php endforeach; ?>

			<?php if ( $available_discounts < 1 ) : ?>
				<tr>
					<td colspan="<?php echo count( $my_membership_discounts_columns ); ?>">
						<span class="membership-discounts-no-active-discounts"><?php esc_html_e( 'There are no member discounts currently active.', 'woocommerce-memberships' ); ?></span>
					</td>
				</tr>
			<?php endif; ?>

		</tbody>

		<?php $tfoot = wc_memberships_get_members_area_page_links( $customer_membership->get_plan(), 'my-membership-discounts', $discounted_products ); ?>

		<?php if ( ! empty( $tfoot ) ) : ?>

			<tfoot>
				<tr>
					<th colspan="<?php echo count( $my_membership_discounts_columns ); ?>">
						<?php echo $tfoot; ?>
					</th>
				</tr>
			</tfoot>

		<?php endif; ?>

	</table>
	<?php

endif;

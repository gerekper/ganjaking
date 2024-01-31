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
 * Renders the products restricted to the membership in the my account area.
 *
 * @type \WC_Memberships_User_Membership $customer_membership User Membership object
 * @type \WP_Query $restricted_products Query results of products post objects for all products restricted to the membership
 * @type int $user_id The current user ID
 *
 * @version 1.13.0
 * @since 1.4.0
 */

if ( empty ( $restricted_products->posts ) ) :

	?>
	<p><?php esc_html_e( 'There are no products assigned to this membership.', 'woocommerce-memberships' ); ?></p>
	<?php

else :

	?>
	<table class="shop_table shop_table_responsive my_account_orders my_account_memberships my_membership_products">

		<thead>
			<tr>
				<?php

				/**
				 * Filters the Products table columns in Members Area.
				 *
				 * @since 1.4.0
				 *
				 * @param array $my_membership_products_columns associative array of column ids and names
				 * @param int $user_id member ID
				 */
				$my_membership_products_columns = (array) apply_filters( 'wc_memberships_members_area_my_membership_products_column_names', array(
					'membership-product-image'      => '&nbsp;',
					'membership-product-title'      => wc_memberships_get_members_area_sorting_link( 'title', __( 'Title', 'woocommerce-memberships' ) ),
					'membership-product-accessible' => esc_html__( 'Accessible', 'woocommerce-memberships' ),
					'membership-product-price'      => esc_html__( 'Price', 'woocommerce-memberships' ),
					'membership-product-excerpt'    => esc_html__( 'Description', 'woocommerce-memberships' ),
					'membership-product-actions'    => wc_memberships_get_members_area_page_links( $customer_membership->get_plan(), 'my-membership-products', $restricted_products ),
				), $user_id );

				?>
				<?php foreach ( $my_membership_products_columns as $column_id => $column_header ) : ?>
					<th class="<?php echo esc_attr( $column_id ); ?>"><span class="nobr"><?php echo $column_header; ?></span></th>
				<?php endforeach; ?>
			</tr>
		</thead>

		<tbody>
			<?php foreach ( $restricted_products->posts as $member_product ) : ?>

				<?php

				$product = wc_get_product( $member_product );

				if ( ! $product ) {
					continue;
				}

				if ( $product->is_type( 'variation' ) ) {
					$parent     = wc_get_product( $product->get_parent_id( 'edit' ) );
					$product_id = $parent ? $parent->get_id() : 0;
				} else {
					$product_id = $product->get_id();
				}

				// customer capabilities
				$can_view_product     = wc_memberships_user_can( $user_id, 'view' , array( 'product' => $product_id ) );
				$can_purchase_product = wc_memberships_user_can( $user_id, 'purchase', array( 'product' => $product_id ) );
				$view_start_time      = wc_memberships_adjust_date_by_timezone( wc_memberships_get_user_access_start_time( $user_id, 'view', array( 'product' => $product_id ) ), 'timestamp', wc_timezone_string() );
				$purchase_start_time  = wc_memberships_get_user_access_start_time( $user_id, 'purchase', array( 'product' => $product_id ) );

				?>
				<tr class="membership-product">
					<?php foreach ( $my_membership_products_columns as $column_id => $column_header ) : ?>

						<?php if ( 'membership-product-image' === $column_id ) : ?>

							<td class="membership-product-image" style="min-width: 84px;" data-title="<?php esc_attr_e( 'Image', 'woocommerce-memberships' ); ?>">
								<?php if ( $can_view_product ) : ?>
									<a href="<?php echo esc_url( get_permalink( $product_id ) ); ?>"><?php echo $product->get_image(); ?></a>
								<?php else : ?>
									<?php echo wc_placeholder_img( 'shop_thumbnail' ); ?>
								<?php endif; ?>
							</td>

						<?php elseif ( 'membership-product-title' === $column_id ) : ?>

							<td class="membership-product-title" data-title="<?php esc_attr_e( 'Title', 'woocommerce-memberships' ); ?>">
								<?php if ( $can_view_product ) : ?>
									<a href="<?php echo esc_url( get_permalink( $product_id ) ); ?>"><?php echo esc_html( wp_strip_all_tags( $product->get_title() ) ); ?></a>
								<?php else : ?>
									<?php echo esc_html( wp_strip_all_tags( $product->get_title() ) ); ?>
								<?php endif; ?>
							</td>

						<?php elseif ( 'membership-product-accessible' === $column_id ) : ?>

							<td class="membership-product-accessible" data-title="<?php echo esc_attr( $column_header ); ?>">
								<?php if ( $can_view_product ) : ?>
									<?php esc_html_e( 'Now', 'woocommerce-memberships' ); ?>
								<?php else : ?>
									<time datetime="<?php echo date( 'Y-m-d H:i:s', $view_start_time ); ?>" title="<?php echo esc_attr( $view_start_time ); ?>"><?php echo date_i18n( wc_date_format(), $view_start_time ); ?></time>
								<?php endif; ?>
							</td>

						<?php elseif( 'membership-product-price' === $column_id ) : ?>

							<td class="membership-product-price" data-title="<?php echo esc_attr( $column_header ); ?>">
								<?php if ( $can_view_product ) : ?>
									<?php echo wp_kses_post( $product->get_price_html() ); ?>
								<?php else : ?>
									<span>&ndash;</span>
								<?php endif; ?>
							</td>

						<?php elseif ( 'membership-product-excerpt' === $column_id ) : ?>

							<td class="membership-product-excerpt" data-title="<?php echo esc_attr( $column_header ); ?>">
								<?php if ( empty( $member_product->post_excerpt ) ) : ?>
									<?php echo wp_kses_post( wp_trim_words( strip_shortcodes( $member_product->post_content ), 20 ) ); ?>
								<?php else : ?>
									<?php echo wp_kses_post( wp_trim_words( $member_product->post_excerpt, 20 ) ); ?>
								<?php endif; ?>
							</td>

						<?php elseif ( 'membership-product-actions' === $column_id ) : ?>

							<td class="membership-product-actions order-actions" data-title="<?php echo esc_attr( $column_header ); ?>">
								<?php echo wc_memberships_get_members_area_action_links( 'my-membership-products', $customer_membership, $product ); ?>
							</td>

						<?php else : ?>

							<td class="<?php echo esc_attr( $column_id ); ?>" data-title="<?php echo esc_attr( $column_header ); ?>">
								<?php do_action( 'wc_memberships_members_area_my_membership_products_column_' . $column_id, $product ); ?>
							</td>

						<?php endif; ?>

					<?php endforeach; ?>
				</tr>

			<?php endforeach; ?>
		</tbody>

		<?php $tfoot = wc_memberships_get_members_area_page_links( $customer_membership->get_plan(), 'my-membership-products', $restricted_products ); ?>

		<?php if ( ! empty( $tfoot ) ) : ?>

			<tfoot>
				<tr>
					<th colspan="<?php echo count( $my_membership_products_columns ); ?>">
						<?php echo $tfoot; ?>
					</th>
				</tr>
			</tfoot>

		<?php endif; ?>

	</table>
	<?php

endif;

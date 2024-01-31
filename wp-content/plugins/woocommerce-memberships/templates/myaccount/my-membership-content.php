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
 * Renders the content restricted to the membership in the my account area.
 *
 * @type \WC_Memberships_User_Membership $customer_membership User Membership object
 * @type \WP_Query $restricted_content Query results of posts and custom post types restricted to the membership
 * @type int $user_id The current user ID
 *
 * @version 1.13.0
 * @since 1.4.0
 */

if ( empty ( $restricted_content->posts ) ) :

	?>
	<p><?php esc_html_e( 'There is no content assigned to this membership.', 'woocommerce-memberships' ); ?></p>
	<?php

else :

	?>
	<table class="shop_table shop_table_responsive my_account_orders my_account_memberships my_membership_content">

		<thead>
			<tr>
				<?php

				/**
				 * Filters the Content table columns in Members Area.
				 *
				 * @since 1.4.0
				 *
				 * @param array $my_membership_content_columns associative array of column ids and names
				 * @param int $user_id the member ID
				 */
				$my_membership_content_columns = (array) apply_filters( 'wc_memberships_members_area_my_membership_content_column_names', array(
					'membership-content-title'      => esc_html__( 'Title', 'woocommerce-memberships' ),
					'membership-content-type'       => esc_html__( 'Type', 'woocommerce-memberships' ),
					'membership-content-accessible' => esc_html__( 'Accessible', 'woocommerce-memberships' ),
					'membership-content-excerpt'    => esc_html__( 'Excerpt', 'woocommerce-memberships' ),
					'membership-content-actions'    => wc_memberships_get_members_area_page_links( $customer_membership->get_plan(), 'my-membership-content', $restricted_content ),
				), $user_id );

				?>
				<?php foreach ( $my_membership_content_columns as $column_id => $column_header ) : ?>
					<th class="<?php echo esc_attr( $column_id ); ?>">
						<span class="nobr">
							<?php if ( 'membership-content-title' === $column_id ) : ?>
								<?php echo wc_memberships_get_members_area_sorting_link( 'title', $column_header ); ?>
							<?php elseif ( 'membership-content-type' === $column_id ) : ?>
								<?php echo wc_memberships_get_members_area_sorting_link( 'type', $column_header ); ?>
							<?php else: ?>
								<?php echo $column_header; ?>
							<?php endif; ?>
						</span>
					</th>
				<?php endforeach; ?>
			</tr>
		</thead>

		<tbody>
			<?php foreach ( $restricted_content->posts as $member_post ) : ?>

				<?php

				if ( ! $member_post instanceof \WP_Post ) {
					continue;
				}

				// determine if the content is currently accessible or not
				$can_view_content = wc_memberships_user_can( $user_id, 'view', array( 'post' => $member_post->ID ) );
				$view_start_time  = wc_memberships_adjust_date_by_timezone( wc_memberships_get_user_access_start_time( $user_id, 'view', array( 'post' => $member_post->ID ) ), 'timestamp', wc_timezone_string() );

				?>
				<tr class="membership-content">
					<?php foreach ( $my_membership_content_columns as $column_id => $column_header ) : ?>

						<?php if ( 'membership-content-title' === $column_id ) : ?>

							<td class="membership-content-title" data-title="<?php echo esc_attr( $column_header ); ?>">
								<?php if ( $can_view_content ) : ?>
									<a href="<?php echo esc_url( get_permalink( $member_post->ID ) ); ?>"><?php echo esc_html( get_the_title( $member_post->ID ) ); ?></a>
								<?php else : ?>
									<?php echo esc_html( get_the_title( $member_post->ID ) ); ?>
								<?php endif; ?>
							</td>

						<?php elseif ( 'membership-content-type' === $column_id ) : ?>

							<td class="membership-content-type" data-title="<?php echo esc_attr( $column_header ); ?>">
								<?php echo wc_memberships_get_content_type_name( $member_post ); ?>
							</td>

						<?php elseif ( 'membership-content-accessible' === $column_id ) : ?>

							<td class="membership-content-accessible" data-title="<?php echo esc_attr( $column_header ); ?>">
								<?php if ( $can_view_content ) : ?>
									<?php esc_html_e( 'Now', 'woocommerce-memberships' ); ?>
								<?php else : ?>
									<time datetime="<?php echo date( 'Y-m-d H:i:s', $view_start_time ); ?>" title="<?php echo esc_attr( $view_start_time ); ?>"><?php echo date_i18n( wc_date_format(), $view_start_time ); ?></time>
								<?php endif; ?>
							</td>

						<?php elseif ( 'membership-content-excerpt' === $column_id ) : ?>

							<td class="membership-content-excerpt" data-title="<?php echo esc_attr( $column_header ); ?>">
								<?php if ( empty( $member_post->post_excerpt ) ) : ?>
									<?php echo wp_kses_post( wp_trim_words( strip_shortcodes( $member_post->post_content ), 20 ) ); ?>
								<?php else : ?>
									<?php echo wp_kses_post( wp_trim_words( $member_post->post_excerpt, 20 ) ); ?>
								<?php endif; ?>
							</td>

						<?php elseif ( 'membership-content-actions' === $column_id ) : ?>

							<td class="membership-content-actions order-actions" data-title="<?php echo esc_attr( $column_header ); ?>">
								<?php echo wc_memberships_get_members_area_action_links( 'my-membership-content', $customer_membership, $member_post ); ?>
							</td>

						<?php else : ?>

							<td class="<?php echo esc_attr( $column_id ); ?>" data-title="<?php echo esc_attr( $column_header ); ?>">
								<?php do_action( 'wc_memberships_members_area_my_membership_content_column_' . $column_id, $member_post ); ?>
							</td>

						<?php endif; ?>

					<?php endforeach; ?>
				</tr>

			<?php endforeach; ?>
		</tbody>

		<?php $tfoot = wc_memberships_get_members_area_page_links( $customer_membership->get_plan(), 'my-membership-content', $restricted_content ); ?>

		<?php if ( ! empty( $tfoot ) ) : ?>

			<tfoot>
				<tr>
					<th colspan="<?php echo count( $my_membership_content_columns ); ?>">
						<?php echo $tfoot; ?>
					</th>
				</tr>
			</tfoot>

		<?php endif; ?>

	</table>
	<?php

endif;

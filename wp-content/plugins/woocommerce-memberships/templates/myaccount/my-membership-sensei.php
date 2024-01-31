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
 * Outputs content in the "My Courses & Lessons" members area section.
 *
 * @var \WC_Memberships_User_Membership $customer_membership the user membership instance
 * @var \WP_Query $restricted_posts restricted Sensei member content
 * @var int $user_id the current member's user ID
 * @var int $paged page number if content is paginated
 *
 * @version 1.21.0
 * @since 1.21.0
 */

?>
<h3>
	<?php

	/**
	 * Filters the Sensei Member Areas content title.
	 *
	 * @since 1.21.0
	 *
	 * @param string $content_title the title
	 */
	echo esc_html( (string) apply_filters( 'wc_memberships_sensei_members_area_content_title', __( 'My Courses & Lessons', 'woocommerce-memberships' ) ) );

	?>
</h3>

<?php do_action( 'wc_memberships_before_members_area', 'my-membership-sensei', $customer_membership ); ?>

<?php if ( empty ( $restricted_content->posts ) ) : ?>

	<p><?php esc_html_e( 'There are no courses or lessons assigned to this membership.', 'woocommerce-memberships' ); ?></p>

<?php else : ?>

	<?php echo wc_memberships_get_members_area_page_links( $customer_membership->get_plan(), 'my-membership-sensei', $restricted_content ); ?>

	<table class="shop_table shop_table_responsive my_account_orders my_account_memberships my_membership_sensei">

		<thead>
			<tr>
				<?php

				/**
				 * Filters My Membership Sensei table columns in Members Area.
				 *
				 * @since 1.21.0
				 *
				 * @param array $my_membership_sensei_columns associative array of column ids and names
				 */
				$my_membership_sensei_columns = (array) apply_filters( 'wc_memberships_sensei_members_area_column_names', [
					'membership-sensei-title'      => __( 'Title', 'woocommerce-memberships' ),
					'membership-sensei-type'       => __( 'Type', 'woocommerce-memberships' ),
					'membership-sensei-accessible' => __( 'Accessible', 'woocommerce-memberships' ),
					'membership-sensei-excerpt'    => __( 'Excerpt', 'woocommerce-memberships' ),
					'membership-sensei-actions'    => '&nbsp;'
				], $user_id );

				foreach ( $my_membership_sensei_columns as $column_id => $column_name ) :

					?>
					<th class="<?php echo esc_attr( $column_id ); ?>"><span class="nobr"><?php echo esc_html( $column_name ); ?></span></th>
					<?php

				endforeach;

				?>
			</tr>
		</thead>

		<tbody>
			<?php foreach ( $restricted_content->posts as $member_post ) : ?>

				<?php

				if ( ! $member_post instanceof \WP_Post ) :
					continue;
				endif;

				// determine if the content is currently accessible or not
				$can_view_content = wc_memberships_user_can( $user_id, 'view', [ 'post' => $member_post->ID ] );
				$view_start_time  = wc_memberships_adjust_date_by_timezone( wc_memberships_get_user_access_start_time( $user_id, 'view', [ 'post' => $member_post->ID ] ), 'timestamp', wc_timezone_string() );

				?>
				<tr class="membership-sensei">
					<?php foreach ( $my_membership_sensei_columns as $column_id => $column_name ) : ?>

						<?php if ( 'membership-sensei-title' === $column_id ) : ?>

							<td class="membership-sensei-title" data-title="<?php echo esc_attr( $column_name ); ?>">
								<?php if ( $can_view_content ) : ?>
									<a href="<?php echo esc_url( get_permalink( $member_post->ID ) ); ?>"><?php echo esc_html( get_the_title( $member_post->ID ) ); ?></a>
								<?php else : ?>
									<?php echo esc_html( get_the_title( $member_post->ID ) ); ?>
								<?php endif; ?>
							</td>

						<?php elseif ( 'membership-sensei-type' === $column_id ) : ?>

							<td class="membership-sensei-type" data-title="<?php echo esc_attr( $column_name ); ?>">
								<?php echo wc_memberships_get_content_type_name( $member_post ); ?>
							</td>

						<?php elseif ( 'membership-sensei-accessible' === $column_id ) : ?>

							<td class="membership-sensei-accessible" data-title="<?php echo esc_attr( $column_name ); ?>">
								<?php if ( $can_view_content ) : ?>
									<?php esc_html_e( 'Now', 'woocommerce-memberships' ); ?>
								<?php else : ?>
									<time datetime="<?php echo date( 'Y-m-d H:i:s', $view_start_time ); ?>" title="<?php echo esc_attr( $view_start_time ); ?>"><?php echo date_i18n( wc_date_format(), $view_start_time ); ?></time>
								<?php endif; ?>
							</td>

						<?php elseif ( 'membership-sensei-excerpt' === $column_id ) : ?>

							<td class="membership-sensei-excerpt" data-title="<?php echo esc_attr( $column_name ); ?>">
								<?php if ( empty( $member_post->post_excerpt ) ) : ?>
									<?php echo wp_kses_post( wp_trim_words( strip_shortcodes( $member_post->post_content ), 20 ) ); ?>
								<?php else : ?>
									<?php echo wp_kses_post( wp_trim_words( $member_post->post_excerpt, 20 ) ); ?>
								<?php endif; ?>
							</td>

						<?php elseif ( 'membership-sensei-actions' === $column_id ) : ?>

							<td class="membership-sensei-actions order-actions" data-title="<?php echo esc_attr( $column_name ); ?>">
								<?php echo wc_memberships_get_members_area_action_links( 'my-membership-sensei', $customer_membership, $member_post ); ?>
							</td>

						<?php else : ?>

							<td class="<?php echo esc_attr( $column_id ); ?>" data-title="<?php echo esc_attr( $column_name ); ?>">
								<?php

								if ( has_action( 'wcm_sensei_member_area_column_' . $column_id ) ) {

									wc_deprecated_hook( 'wcm_sensei_member_area_column_' . $column_id, '1.21.0', 'wc_memberships_sensei_member_area_column_' . $column_id );

									/**
									 * Fires when outputting additional columns in the Sensei Members Area.
									 *
									 * This filter was inherited from a free add on and is deprecated
									 *
									 * TODO remove this deprecated hook by May 2022 or by version 2.0.0 {FN 2020-01-04}
									 *
									 * @since 1.21.0
									 * @deprecated 1.21.0
									 *
									 * @param \WP_Post $member_post
									 */
									do_action( 'wcm_sensei_member_area_column_' . $column_id, $member_post );
								}

								/**
								 * Fires when outputting additional columns in the Sensei Members Area.
								 *
								 * @since 1.21.0
								 *
								 * @param \WP_Post $member_post
								 */
								do_action( 'wc_memberships_sensei_member_area_column_' . $column_id, $member_post );

								?>
							</td>

						<?php endif; ?>

					<?php endforeach; ?>
				</tr>

			<?php endforeach; ?>
		</tbody>

	</table>

	<?php echo wc_memberships_get_members_area_page_links( $customer_membership->get_plan(), 'my-membership-sensei', $restricted_content ); ?>

<?php endif; ?>

<?php do_action( 'wc_memberships_after_members_area', 'my-membership-sensei', $customer_membership ); ?>

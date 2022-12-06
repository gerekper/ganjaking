<?php
/**
 * Template for Post Type List Items for Membership Plan
 *
 * @var YITH_WCMBS_Plan             $plan
 * @var YITH_WCMBS_Membership|false $membership
 * @var string                      $post_type
 * @var int                         $page
 */

wp_enqueue_script( 'yith_wcmbs_frontend_js' );

yith_wcmbs_late_enqueue_assets( 'membership' );

$user_id = get_current_user_id();

$sorting_order_by_getter = "get_{$post_type}_sorting_order_by";
$sorting_order_getter    = "get_{$post_type}_sorting_order";
$page                    = isset( $page ) ? $page : 1;

$args = array(
	'include_linked' => true,
	'items_per_page' => apply_filters( 'yith_wcmb_membership_plan_items_per_page', 5, $post_type ),
	'paginate'       => true,
	'page'           => $page,
	'order_by'       => $plan->$sorting_order_by_getter(),
	'order'          => $plan->$sorting_order_getter(),
);

$titles = array(
	'post'    => __( 'Included Posts', 'yith-woocommerce-membership' ),
	'page'    => __( 'Included Pages', 'yith-woocommerce-membership' ),
	'product' => __( 'Included Products', 'yith-woocommerce-membership' ),
);

$results       = $plan->get_included_items( $post_type, $args );
$items         = $results->items;
$total         = $results->total;
$max_num_pages = $results->max_num_pages;

$title = $titles[ $post_type ];
$page  = min( $page, $max_num_pages );
?>

<?php if ( $items && ( $membership || yith_wcmbs_has_full_access() ) ) : ?>
	<div class="yith-wcmbs-membership-plan-items yith-wcmbs-membership-plan-items--<?php echo esc_attr( $post_type ) ?>"
			data-post_type="<?php echo esc_attr( $post_type ); ?>"
			data-membership_id="<?php echo esc_attr( $membership ? $membership->get_id() : 0 ); ?>"
			data-plan_id="<?php echo esc_attr( $plan->get_id() ); ?>"
			data-security="<?php echo esc_attr( wp_create_nonce( "yith-wcmbs-get-plan-{$post_type}-items" ) ); ?>"
	>
		<div class='yith-wcmbs-membership-plan-items__title'><?php echo esc_html( $title ); ?></div>
		<div class='yith-wcmbs-membership-plan-items__content'>
			<div class='yith-wcmbs-membership-plan-items__items'>
				<?php foreach ( $items as $item_id ) : ?>
					<?php
					$title = get_the_title( $item_id );
					$link  = YITH_WCMBS_Manager()->get_post_link( $item_id, $user_id );
					if ( ! ! $link ) {
						$title = "<a href='{$link}'>{$title}</a>";
					}

					$delay_time   = get_post_meta( $item_id, '_yith_wcmbs_plan_delay', true );
					$has_access   = yith_wcmbs_has_full_access();
					$availability = '';
					$download     = '';

					if ( apply_filters( 'yith_wcmb_membership_plan_items_hide_item', false, $item_id, $membership, $plan ) ) {
						continue;
					}

					if ( 'product' === $post_type && YITH_WCMBS_Products_Manager()->is_allowed_download() ) {
						$has_access = YITH_WCMBS_Products_Manager()->user_has_access_to_product( get_current_user_id(), $item_id );
					} else {
						$has_access = YITH_WCMBS_Manager()->user_has_access_to_post( get_current_user_id(), $item_id );
					}

					if ( ! empty( $delay_time[ $plan->get_id() ] ) && ! $has_access ) {
						// The item has delay time for this plan
						$delay = $delay_time[ $plan->get_id() ];

						if ( $membership->is_active() ) {
							$availability_date = yith_wcmbs_date( strtotime( '+ ' . $delay . ' days', $membership->start_date + ( $membership->paused_days * 60 * 60 * 24 ) ) );
							$availability      = sprintf( __( 'Availability date: %s ', 'yith-woocommerce-membership' ), $availability_date );
						} else {
							$availability = sprintf( _n( 'available after %s day since the beginning of the membership', 'available after %s days since the beginning of the membership', $delay, 'yith-woocommerce-membership' ), $delay );
						}
					}

					if ( 'product' === $post_type ) {
						$download = do_shortcode( '[membership_download_product_links id="' . $item_id . '"]' );
					}

					$item_entries = array(
						'name'         => $title,
						'availability' => $availability,
						'download'     => $download,
					);

					$item_entries = apply_filters( "yith_wcmbs_membership_contents_{$post_type}_post_type_item_entries", $item_entries, $item_id );

					?>

					<div class="yith-wcmbs-membership-plan-items__item">
						<?php foreach ( $item_entries as $key => $entry ) : ?>
							<?php if ( ! ! $entry ) : ?>
								<div class="yith-wcmbs-membership-plan-items__item__<?php echo esc_attr( sanitize_title( $key ) ); ?>">
									<?php echo wp_kses_post( $entry ); ?>
								</div>
							<?php endif; ?>
						<?php endforeach; ?>
					</div>
				<?php endforeach; ?>
			</div>
			<div class='yith-wcmbs-membership-plan-items__footer'>
				<?php if ( 1 !== $page || 1 !== $max_num_pages ) : ?>
					<div class='yith-wcmbs-membership-plan-items__pagination'>
						<?php
						$prev_enabled = 1 !== $page;
						$prev_page    = max( 1, $page - 1 );
						$next_page    = min( $max_num_pages, $page + 1 );
						$next_enabled = $page < $max_num_pages;
						$prev_classes = array(
							'yith-wcmbs-membership-plan-items__pagination__prev',
							$prev_enabled ? 'yith-wcmbs--enabled' : 'yith-wcmbs--disabled',
						);
						$next_classes = array(
							'yith-wcmbs-membership-plan-items__pagination__next',
							$next_enabled ? 'yith-wcmbs--enabled' : 'yith-wcmbs--disabled',
						);
						$prev_classes = implode( ' ', $prev_classes );
						$next_classes = implode( ' ', $next_classes );
						?>
						<span class='<?php echo esc_attr( $prev_classes ) ?>' data-page="<?php echo esc_attr( $prev_page ) ?>"></span>
						<span class='yith-wcmbs-membership-plan-items__pagination__paging'>
							<?php
							echo wp_kses_post(
								sprintf(
								// translators: 1. the current page number; 2. number of total pages; (eg. 2 of 12).
									_x( '%1$s of %2$s', 'Pagination', 'yith-woocommerce-membership' ),
									"<span class='yith-wcmbs-membership-plan-items__pagination__paging__current'>{$page}</span>",
									"<span class='yith-wcmbs-membership-plan-items__pagination__paging__total'>{$max_num_pages}</span>"
								)
							);
							?>
						</span>
						<span class='<?php echo esc_attr( $next_classes ); ?>' data-page="<?php echo esc_attr( $next_page ); ?>"></span>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
<?php endif; ?>
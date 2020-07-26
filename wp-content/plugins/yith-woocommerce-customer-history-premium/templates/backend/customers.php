<?php

defined( 'ABSPATH' ) or exit;

/*
 *  Users
 */

$panel_page = isset( $_REQUEST['panel_page'] ) ? $_REQUEST['panel_page'] : '';
if ( $panel_page == 'others' ) {
	include YITH_WCCH_TEMPLATE_PATH . '/backend/users.php';
	die();
} else if ( $panel_page == 'customer' ) {
	include YITH_WCCH_TEMPLATE_PATH . '/backend/customer.php';
	die();
}

/*
 *  Customers
 */

global $wpdb;

$search = isset( $_GET['s'] ) ? $_GET['s'] : '';
$page = isset( $_GET['p'] ) && $_GET['p'] > 1 ? $_GET['p'] : 1;
$results_per_page = get_option( 'yith-wcch-results_per_page' ) > 0 ? get_option( 'yith-wcch-results_per_page' ) : 50;
$users_offset = ( $page - 1 ) * $results_per_page;

$users_query = new WP_User_Query( array(
	'role'				=> 'customer',
	'number'			=> $results_per_page,
	'offset'			=> $users_offset,
	'search'			=> '*' . esc_attr( $search ) . '*',
) );
$users = $users_query->get_results();

// Calculate the number of pages
$num_users = $users_query->get_total();
$max_pages = ceil( $num_users / $results_per_page );

// Users orders
$orders_array = array();
foreach ( $users as $key => $user ) {
	$orders_array[ $user->ID ] = get_posts( array(
		'numberposts' => -1,
		'meta_key'    => '_customer_user',
		'meta_value'  => $user->ID,
		'post_type'   => 'shop_order',
		'post_status' =>  'any',
		'post_parent' => '0',
	) );
	$order_count = count( $orders_array[ $user->ID ] );
	if ( get_option('yith-wcch-hide_users_with_no_orders') == 'yes' && ! $order_count > 0 ) { unset( $users[$key] ); }
}

?>

<div id="yith-woocommerce-customer-history">
	<div id="customers" class="wrap">

		<form action="" class="search-box" style="float: right;">
			<input type="hidden" name="page" value="yith_wcch_panel">
			<input type="hidden" name="tab" value="users">
			<label class="screen-reader-text" for="user-search-input"><?php echo __( 'Search Customer', 'yith-woocommerce-customer-history' ); ?>:</label>
			<input type="search" id="user-search-input" name="s" value="<?php echo $search; ?>">
			<input type="submit" id="search-submit" class="button" value="<?php echo __( 'Search Customer', 'yith-woocommerce-customer-history' ); ?>">
		</form>

		<div class="tablenav top">
			<ul class="subsubsub" style="margin-top: 4px;">
				<li class="customers"><a href="admin.php?page=yith_wcch_panel&tab=users" class="current"><?php echo __( 'Customers', 'yith-woocommerce-customer-history' ); ?></a> |</li>
				<li class="users"><a href="admin.php?page=yith_wcch_panel&tab=users&panel_page=others"><?php echo __( 'Other Users', 'yith-woocommerce-customer-history' ); ?></a> |</li>
				<li class="guestr"><a href="admin.php?page=yith_wcch_panel&tab=users&panel_page=customer&user_id=0"><?php echo __( 'Guest Users', 'yith-woocommerce-customer-history' ); ?></a></li>
			</ul>
			<div class="tablenav-pages">
				<div class="pagination-links">
					<?php echo __( 'Total', 'yith-woocommerce-customer-history' ) . ': ' . $num_users; ?> &nbsp; | &nbsp;
					<?php echo __( 'Page', 'yith-woocommerce-customer-history' ) . ': ' . $page . ' of ' . $max_pages; ?> &nbsp;
					<?php if ( $page > 1 ) : ?>
					<a class="prev-page" href="admin.php?page=yith-wcch-customers.php&p=1"><span aria-hidden="true">‹‹</span></a>
					<a class="prev-page" href="admin.php?page=yith-wcch-customers.php&p=<?php echo $page - 1; ?>"><span aria-hidden="true">‹</span></a>
					<?php else : ?>
					<span class="tablenav-pages-navspan" aria-hidden="true">‹‹</span>
					<span class="tablenav-pages-navspan" aria-hidden="true">‹</span>
					<?php endif; ?>
					<?php if ( $page < $max_pages ) : ?>
					<a class="next-page" href="admin.php?page=yith-wcch-customers.php&p=<?php echo $page + 1; ?>"><span aria-hidden="true">›</span></a>
					<a class="next-page" href="admin.php?page=yith-wcch-customers.php&p=<?php echo $max_pages; ?>"><span aria-hidden="true">››</span></a>
					<?php else : ?>
					<span class="tablenav-pages-navspan" aria-hidden="true">›</span>
					<span class="tablenav-pages-navspan" aria-hidden="true">››</span>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<table class="wp-list-table widefat fixed striped posts">
			<tr>
				<th><?php echo __( 'User', 'yith-woocommerce-customer-history' ); ?></th>
				<th><?php echo __( 'Role', 'yith-woocommerce-customer-history' ); ?></th>
				<th><?php echo __( 'Orders', 'yith-woocommerce-customer-history' ); ?></th>
				<th><?php echo __( 'Pending Orders', 'yith-woocommerce-customer-history' ); ?></th>
				<?php if ( apply_filters( 'yith_wcch_show_refund_orders_column', true ) ) : ?>
					<th><?php echo __( 'Refund Orders', 'yith-woocommerce-customer-history' ); ?></th>
				<?php endif; ?>
				<?php if ( apply_filters( 'yith_wcch_show_orders_average_column', true ) ) : ?>
					<th><?php echo __( 'Orders average', 'yith-woocommerce-customer-history' ); ?></th>
				<?php endif; ?>
				<th><?php echo __( 'Total Spent', 'yith-woocommerce-customer-history' ); ?></th>
				<?php if ( apply_filters( 'yith_wcch_show_customer_orders_sku_list', false ) ) : ?>
					<th><?php echo __( 'SKU', 'yith-woocommerce-customer-history' ); ?></th>
				<?php endif; ?>
				<th style="width: 150px;"><?php echo __( 'Actions', 'yith-woocommerce-customer-history' ); ?></th>
			</tr>

			<?php

			foreach ( $users as $user ) :

				// $order_count = wc_get_customer_order_count( $user->ID );
				// $total_spent = wc_get_customer_total_spent( $user->ID );
				$total_spent = yith_ch_get_customer_total_spent( $user->ID );

				$order_count = count( get_posts( array(
					'numberposts' => -1,
					'meta_key'    => '_customer_user',
					'meta_value'  => $user->ID,
					'post_type'   => 'shop_order',
					'post_status' =>  'any',
					'post_parent' => '0',
				) ) );

				$pending_orders_count = count( get_posts( array(
					'numberposts' => -1,
					'meta_key'    => '_customer_user',
					'meta_value'  => $user->ID,
					'post_type'   => 'shop_order',
					'post_status' =>  array( 'pending', 'wc-pending'),
					'post_parent' => '0',
				) ) );

				$refunded_orders_count = count( get_posts( array(
					'numberposts' => -1,
					'meta_key'    => '_customer_user',
					'meta_value'  => $user->ID,
					'post_type'   => 'shop_order',
					'post_status' =>  array( 'refunded', 'wc-refunded'),
					'post_parent' => '0',
				) ) );

				?>

				<tr>
					<td><a href="admin.php?page=yith-wcch-customer.php&user_id=<?php echo esc_html( $user->ID ); ?>"><?php echo esc_html( $user->display_name ); ?></a></td>
					<td><?php

						global $wp_roles;
						$roles_array = array();
						foreach ( $wp_roles->role_names as $role => $name ) {
							if ( user_can( $user, $role ) ) {
								$roles_array[] = $role;
							}
						}
						echo implode( '<br />', $roles_array );

					?></td>
					<td><?php echo $order_count; ?></td>
					<td><?php echo $pending_orders_count; ?></td>
					<?php if ( apply_filters( 'yith_wcch_show_refund_orders_column', true ) ) : ?>
						<td><?php echo $refunded_orders_count; ?></td>
					<?php endif; ?>
					<?php if ( apply_filters( 'yith_wcch_show_orders_average_column', true ) ) : ?>
						<td><?php echo $order_count > 0 ? wc_price( $total_spent / $order_count ) : wc_price( $total_spent ); ?></td>
					<?php endif; ?>
					<td><?php echo wc_price( $total_spent ); ?></td>
					<?php if ( apply_filters( 'yith_wcch_show_customer_orders_sku_list', false ) ) : ?>
						<td>
							<?php
								$orders_items_sku = array();
								foreach ( $orders_array[ $user->ID ] as $key => $order ) {
									$order = wc_get_order( $order->ID );
									foreach ($order->get_items() as $item) {
										$product = wc_get_product($item->get_product_id());
										$orders_items_sku[] = $product->get_sku();
									}
								}
								echo implode( ', ', $orders_items_sku );
							?>
						</td>
					<?php endif; ?>
					<td>
						<a href="admin.php?page=yith_wcch_panel&tab=users&panel_page=customer&user_id=<?php echo esc_html( $user->ID ); ?>" class="button"><strong><i class="fa fa-eye" aria-hidden="true"></i> <?php echo __( 'View', 'yith-woocommerce-customer-history' ); ?></strong></a>
						<a href="admin.php?page=yith_wcch_panel&tab=emails&panel_page=email&customer_id=<?php echo $user->ID; ?>" class="button"><strong><i class="fa fa-envelope" aria-hidden="true"></i> <?php echo __( 'Email', 'yith-woocommerce-customer-history' ); ?></strong></a>
						<?php
						/*
                        if ( ! is_multisite() && get_current_user_id() != $user->ID && current_user_can( 'delete_user', $user->ID ) ) {
                            echo "<a class='submitdelete button' href='" . wp_nonce_url( "admin.php?page=yith_wcch_panel&tab=users&action=delete&amp;user=$user->ID", 'bulk-users' ) . "'><strong><i class=\"fa fa-ban\" aria-hidden=\"true\"></i> " . __( 'Delete', 'yith-woocommerce-customer-history' ) . "</strong></a>";
                        }
                        if ( is_multisite() && get_current_user_id() != $user->ID && current_user_can( 'remove_user', $user->ID ) ) {
                            echo "<a class='submitdelete button' href='" . wp_nonce_url( "admin.php?page=yith_wcch_panel&tab=users&action=remove&amp;user=$user->ID", 'bulk-users' ) . "'><strong><i class=\"fa fa-ban\" aria-hidden=\"true\"></i> " . __( 'Remove', 'yith-woocommerce-customer-history' ) . "</strong></a>";
                        }
                        */
                        ?>
					</td>
				</tr>

			<?php endforeach; ?>

		</table>

		<div class="tablenav bottom">
			<div class="tablenav-pages">
				<div class="pagination-links">
					<?php echo __( 'Total', 'yith-woocommerce-customer-history' ) . ': ' . $num_users; ?> &nbsp; | &nbsp;
					<?php echo __( 'Page', 'yith-woocommerce-customer-history' ) . ': ' . $page . ' of ' . $max_pages; ?> &nbsp;
					<?php if ( $page > 1 ) : ?>
					<a class="prev-page" href="admin.php?page=yith-wcch-customers.php&p=1"><span aria-hidden="true">‹‹</span></a>
					<a class="prev-page" href="admin.php?page=yith-wcch-customers.php&p=<?php echo $page - 1; ?>"><span aria-hidden="true">‹</span></a>
					<?php else : ?>
					<span class="tablenav-pages-navspan" aria-hidden="true">‹‹</span>
					<span class="tablenav-pages-navspan" aria-hidden="true">‹</span>
					<?php endif; ?>
					<?php if ( $page < $max_pages ) : ?>
					<a class="next-page" href="admin.php?page=yith-wcch-customers.php&p=<?php echo $page + 1; ?>"><span aria-hidden="true">›</span></a>
					<a class="next-page" href="admin.php?page=yith-wcch-customers.php&p=<?php echo $max_pages; ?>"><span aria-hidden="true">››</span></a>
					<?php else : ?>
					<span class="tablenav-pages-navspan" aria-hidden="true">›</span>
					<span class="tablenav-pages-navspan" aria-hidden="true">››</span>
					<?php endif; ?>
				</div>
			</div>
		</div>

	</div>
</div>

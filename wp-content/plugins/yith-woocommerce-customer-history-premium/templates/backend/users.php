<?php

defined( 'ABSPATH' ) or exit;

/*
 *  Users
 */

global $wpdb;

$search = isset( $_REQUEST['s'] ) ? $_REQUEST['s'] : '';
$page = isset( $_REQUEST['p'] ) && $_REQUEST['p'] > 1 ? $_REQUEST['p'] : 1;
$results_per_page = get_option( 'yith-wcch-results_per_page' ) > 0 ? get_option( 'yith-wcch-results_per_page' ) : 50;
$users_offset = ( $page - 1 ) * $results_per_page;

$users_query = new WP_User_Query( array(
	'role__not_in'      => 'customer',
	'number'            => $results_per_page,
	'offset'            => $users_offset,
	'search'            => '*' . esc_attr( $search ) . '*',
	'search_columns'    => array(
		'user_login',
		'user_nicename',
		'user_email',
		'user_url',
	),
	'meta_query' => array(
		'relation' => 'OR',
		array(
			'key'     => 'first_name',
			'value'   => $search,
			'compare' => 'LIKE'
		),
		array(
			'key'     => 'last_name',
			'value'   => $search,
			'compare' => 'LIKE'
		)
	)
) );
$users = $users_query->get_results();

// Calculate the number of pages
$num_users = $users_query->get_total();
$max_pages = ceil( $num_users / $results_per_page );

if ( get_option('yith-wcch-hide_users_with_no_orders') == 'yes' ) {
	foreach ( $users as $key => $user) {
		$order_count = count( get_posts( array(
			'numberposts' => -1,
			'meta_key'    => '_customer_user',
			'meta_value'  => $user->ID,
			'post_type'   => 'shop_order',
			'post_status' =>  'any',
			'post_parent' => '0',
		) ) );
		if ( ! $order_count > 0 ) { unset( $users[$key] ); }
	}
}

?>

<div id="yith-woocommerce-customer-history">
	<div id="customers" class="wrap">

		<form action="" class="search-box" method="post" style="float: right;">
			<input type="hidden" name="page" value="yith_wcch_panel">
			<input type="hidden" name="tab" value="users">
			<input type="hidden" name="panel_page" value="others">
			<label class="screen-reader-text" for="user-search-input"><?php echo __( 'Search Customer', 'yith-woocommerce-customer-history' ); ?>:</label>
			<input type="search" id="user-search-input" name="s" value="<?php echo $search; ?>">
			<input type="submit" id="search-submit" class="button" value="<?php echo __( 'Search Customer', 'yith-woocommerce-customer-history' ); ?>">
		</form>

		<div class="tablenav top">
			<ul class="subsubsub" style="margin-top: 4px;">
				<li class="customers"><a href="admin.php?page=yith_wcch_panel&tab=users"><?php echo __( 'Customers', 'yith-woocommerce-customer-history' ); ?></a> |</li>
				<li class="users"><a href="admin.php?page=yith_wcch_panel&tab=users&panel_page=others" class="current"><?php echo __( 'Other Users', 'yith-woocommerce-customer-history' ); ?></a> |</li>
				<li class="guestr"><a href="admin.php?page=yith_wcch_panel&tab=users&panel_page=customer&user_id=0"><?php echo __( 'Guest Users', 'yith-woocommerce-customer-history' ); ?></a></li>
			</ul>
			<div class="tablenav-pages">
				<div class="pagination-links">
					<?php echo __( 'Total', 'yith-woocommerce-customer-history' ) . ': ' . $num_users; ?> &nbsp; | &nbsp;
					<?php echo __( 'Page', 'yith-woocommerce-customer-history' ) . ': ' . $page . ' of ' . $max_pages; ?> &nbsp;
					<?php if ( $page > 1 ) : ?>
					<a class="prev-page" href="admin.php?page=yith-wcch-users.php&p=1"><span aria-hidden="true">‹‹</span></a>
					<a class="prev-page" href="admin.php?page=yith-wcch-users.php&p=<?php echo $page - 1; ?>"><span aria-hidden="true">‹</span></a>
					<?php else : ?>
					<span class="tablenav-pages-navspan" aria-hidden="true">‹‹</span>
					<span class="tablenav-pages-navspan" aria-hidden="true">‹</span>
					<?php endif; ?>
					<?php if ( $page < $max_pages ) : ?>
					<a class="next-page" href="admin.php?page=yith-wcch-users.php&p=<?php echo $page + 1; ?>"><span aria-hidden="true">›</span></a>
					<a class="next-page" href="admin.php?page=yith-wcch-users.php&p=<?php echo $max_pages; ?>"><span aria-hidden="true">››</span></a>
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
				<th><?php echo __( 'Refund Orders', 'yith-woocommerce-customer-history' ); ?></th>
				<th><?php echo __( 'Orders average', 'yith-woocommerce-customer-history' ); ?></th>
				<th><?php echo __( 'Total Spent', 'yith-woocommerce-customer-history' ); ?></th>
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
							if ( user_can( $user, $role ) ) { $roles_array[] = $role; }
						}
						echo implode( '<br />', $roles_array );

					?></td>
					<td><?php echo $order_count; ?></td>
					<td><?php echo $pending_orders_count; ?></td>
					<td><?php echo $refunded_orders_count; ?></td>
					<td><?php echo $order_count > 0 ? wc_price( $total_spent / $order_count ) : wc_price( $total_spent ); ?></td>
					<td><?php echo wc_price( $total_spent ); ?></td>
					<td>
						<a href="admin.php?page=yith_wcch_panel&tab=users&panel_page=customer&user_id=<?php echo esc_html( $user->ID ); ?>" class="button"><strong><i class="fa fa-eye" aria-hidden="true"></i> <?php echo __( 'View', 'yith-woocommerce-customer-history' ); ?></strong></a>
						<a href="admin.php?page=yith_wcch_panel&tab=emails&panel_page=email&customer_id=<?php echo $user->ID; ?>" class="button"><strong><i class="fa fa-envelope" aria-hidden="true"></i> <?php echo __( 'Email', 'yith-woocommerce-customer-history' ); ?></strong></a>
						<?php
						/*
                        if ( !is_multisite() && get_current_user_id() != $user->ID && current_user_can( 'delete_user', $user->ID ) ) {
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
					<a class="prev-page" href="admin.php?page=yith-wcch-users.php&p=1"><span aria-hidden="true">‹‹</span></a>
					<a class="prev-page" href="admin.php?page=yith-wcch-users.php&p=<?php echo $page - 1; ?>"><span aria-hidden="true">‹</span></a>
					<?php else : ?>
					<span class="tablenav-pages-navspan" aria-hidden="true">‹‹</span>
					<span class="tablenav-pages-navspan" aria-hidden="true">‹</span>
					<?php endif; ?>
					<?php if ( $page < $max_pages ) : ?>
					<a class="next-page" href="admin.php?page=yith-wcch-users.php&p=<?php echo $page + 1; ?>"><span aria-hidden="true">›</span></a>
					<a class="next-page" href="admin.php?page=yith-wcch-users.php&p=<?php echo $max_pages; ?>"><span aria-hidden="true">››</span></a>
					<?php else : ?>
					<span class="tablenav-pages-navspan" aria-hidden="true">›</span>
					<span class="tablenav-pages-navspan" aria-hidden="true">››</span>
					<?php endif; ?>
				</div>
			</div>
		</div>

	</div>
</div>

<script>jQuery( document ).ready(function() { yit_open_admin_menu( 'toplevel_page_yith-wcch-customers' ); });</script>

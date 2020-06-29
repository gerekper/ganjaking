<?php

defined( 'ABSPATH' ) or exit;

/*
 *	Customers
 */

global $wpdb; ?>

<div id="yith-woocommerce-customer-history">
	<div id="emails" class="wrap">

		<h1>
			<?php echo __( 'Emails', 'yith-woocommerce-customer-history' ); ?>
            <a href="admin.php?page=yith-wcch-email.php" class="page-title-action"><?php echo __( 'Send new email', 'yith-woocommerce-customer-history' ); ?></a>
        </h1>

		<p><?php echo __( 'Complete emails list.', 'yith-woocommerce-customer-history' ); ?></p>

		<table class="wp-list-table widefat fixed striped posts">
			<tr>
				<th style="width: 10%;"><?php echo __( 'Date', 'yith-woocommerce-customer-history' ); ?></th>
				<th style="width: 10%;"><?php echo __( 'From', 'yith-woocommerce-customer-history' ); ?></th>
				<th style="width: 10%;"><?php echo __( 'To', 'yith-woocommerce-customer-history' ); ?></th>
				<th style="width: 20%;"><?php echo __( 'Subject', 'yith-woocommerce-customer-history' ); ?></th>
				<th style="width: 50%;"><?php echo __( 'Content', 'yith-woocommerce-customer-history' ); ?></th>
				<th style="width: 10%;"><?php echo __( 'Actions', 'yith-woocommerce-customer-history' ); ?></th>
			</tr>

			<?php

			$query = "SELECT * FROM {$wpdb->prefix}yith_wcch_emails ORDER BY reg_date DESC";
            $rows = $wpdb->get_results( $query );
            if ( count( $rows ) > 0 ) :

				foreach ( $rows as $key => $value ) :

					$sender = get_user_by( 'id', $value->sender_id );
					$sender_url = is_object( $sender ) ? 'admin.php?page=yith-wcch-customer.php&user_id=' . esc_html( $sender->ID ) : '#';
					$sender_name = is_object( $sender ) ? $sender->display_name : __( 'Not available', 'yith-woocommerce-customer-history' );

					$user = get_user_by( 'id', $value->user_id );
					$subject = $value->subject;
					$content = $value->content;
					$date = $value->reg_date;

					?>

					<tr>
						<td><?php echo $date; ?></td>
						<td><a href="<?php echo $sender_url; ?>"><?php echo $sender_name; ?></a></td>
						<td><a href="admin.php?page=yith-wcch-customer.php&user_id=<?php echo esc_html( $user->ID ); ?>"><?php echo esc_html( $user->display_name ); ?></a></td>
						<td><?php echo $subject; ?></td>
						<td><?php echo $content; ?></td>
						<td><a href="admin.php?page=yith-wcch-email.php&email_id=<?php echo $value->id; ?>" class="button"><strong><i class="fa fa-eye" aria-hidden="true"></i> <?php echo __( 'View', 'yith-woocommerce-customer-history' ); ?></strong></a></td>
					</tr>

				<?php endforeach; ?>
			<?php endif; ?>

		</table>

	</div>
</div>

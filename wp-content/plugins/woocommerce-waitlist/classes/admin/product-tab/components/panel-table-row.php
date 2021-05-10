<?php
/**
 * Table row for each user for waitlist and archive tabs
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$email = $user;
if ( ! is_email( $email ) ) {
	$user = get_user_by( 'id', $email );
	if ( $user ) {
		$email = $user->user_email;
	}
} else {
	if ( email_exists( $email ) ) {
		$user = get_user_by( 'email', $email );
	}
}
?>
<tr class="wcwl_user_row" data-user-id="<?php echo $email; ?>">
	<?php if ( $email ) { ?>
		<td>
			<input class="wcwl_user_checkbox" type="checkbox" name="wcwl_user_checkbox" value="<?php echo $email; ?>" data-user-email="<?php echo $email; ?>" data-date-added="<?php echo $date; ?>"/>
		</td>
		<td>
			<strong>
				<?php
				if ( isset( $user->ID ) ) {
					?>
					<a title="<?php esc_attr_e( __( 'View User Profile', 'woocommerce-waitlist' ) ); ?>" href="<?php echo get_edit_user_link( $user->ID ); ?>">
					<?php
				}
				echo $email;
				?>
					</a>
				<?php
					echo Pie_WCWL_Custom_Tab::get_user_language_flag( $email, $product_id );
				?>
					<?php
					if ( isset( $errors[ $email ] ) ) {
						echo '<span class="dashicons dashicons-warning"><span>' . $errors[ $email ] . '</span></span>';
					}
					?>
			</strong>
		</td>
		<td>
			<?php echo Pie_WCWL_Custom_Tab::format_date( $date ); ?>
		</td>
	<?php } else { ?>
		<td>
			<input class="wcwl_user_checkbox" type="checkbox" name="wcwl_user_checkbox wcwl_removed_user" value="0" data-user-email="0" data-date-added="<?php echo $date; ?>"/>
		</td>
		<td>
			<strong><?php _e( 'User removed themselves', 'woocommerce-waitlist' ); ?></strong>
		</td>
		<td>
			<?php echo Pie_WCWL_Custom_Tab::format_date( $date ); ?>
		</td>
	<?php } ?>
</tr>

<?php
/**
 * Table row for each user for waitlist and archive tabs
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<tr class="wcwl_user_row" data-user-id="<?php echo $user_id; ?>">
	<?php if ( $user ) { ?>
		<td>
			<input class="wcwl_user_checkbox" type="checkbox" name="wcwl_user_checkbox" value="<?php echo $user_id; ?>" data-user-email="<?php echo $user->user_email; ?>" data-date-added="<?php echo $date ?>"/>
		</td>
		<td>
			<strong>
				<a title="<?php esc_attr_e( __( 'View User Profile', 'woocommerce-waitlist' ) ); ?>" href="<?php echo get_edit_user_link( $user_id ); ?>">
					<?php echo $user->user_email; ?>
				</a>
				<?php echo $this->get_user_language_flag( $user_id, $product_id ); ?>
				<?php
					if ( isset( $errors[$user_id] ) ) {
						echo '<span class="dashicons dashicons-warning"><span>' . $errors[$user_id] . '</span></span>';
					}
				?>
			</strong>
		</td>
		<td>
			<?php echo $this->format_date( $date ); ?>
		</td>
	<?php } else { ?>
		<td>
			<input class="wcwl_user_checkbox" type="checkbox" name="wcwl_user_checkbox wcwl_removed_user" value="0" data-user-email="0" data-date-added="<?php echo $date ?>"/>
		</td>
		<td>
			<strong><?php _e( 'User removed themselves', 'woocommerce-waitlist' ); ?></strong>
		</td>
		<td>
			<?php echo $this->format_date( $date ); ?>
		</td>
	<?php } ?>
</tr>
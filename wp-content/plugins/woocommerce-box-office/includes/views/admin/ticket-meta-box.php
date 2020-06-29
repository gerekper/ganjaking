<?php
// Get order ID.
$order_id = get_post_meta( $post->ID, '_order', true );
$order    = wc_get_order( $order_id );

// Get product ID.
$product_id = get_post_meta( $post->ID, '_product', true );

// Get customer user ID.
$user_id = get_post_meta( $post->ID, '_user', true );
?>

<div class="panel woocommerce_options_panel">
	<p class="form-field">
		<label for="edit_ticket_url"><?php _e( 'Edit Ticket URL:', 'woocommerce-box-office' ); ?></label>
		<a id="edit_ticket_url" href="<?php echo esc_url( wcbo_get_my_ticket_url( $post->ID ) ); ?>" target="_blank">
			<?php echo esc_html( wcbo_get_my_ticket_url( $post->ID ) ); ?>
		</a>
	</p>

	<?php // Link to product. ?>
	<?php if ( $product_id ) : ?>
	<?php $product_title = get_the_title( $product_id ); ?>
	<p class="form-field">
		<label for="product_link"><?php _e( 'Product:', 'woocommerce-box-office' ); ?></label>
		<a id="product_link" href="<?php echo esc_url( admin_url( 'post.php?post=' . $product_id . '&action=edit' ) ); ?>"><?php echo esc_html( $product_title ); ?></a>
	</p>
	<?php endif; ?>

	<?php // Link to order. ?>
	<?php if ( $order ) : ?>
	<p class="form-field">
		<label for="order_link"><?php _e( 'Order:', 'woocommerce-box-office' ); ?></label>
		<a id="order_link" href="<?php echo esc_url( admin_url( 'post.php?post=' . $order_id . '&action=edit' ) ); ?>"><?php echo esc_html( $order->get_order_number() ); ?></a>
	</p>
	<?php endif; ?>

	<?php // Link to user. ?>
	<?php if ( $user_id ) : ?>
	<?php $user = get_userdata( $user_id ); ?>
	<p class="form-field">
		<label for="user_link"><?php _e( 'User:', 'woocommerce-box-office' ); ?></label>
		<a id="user_link" href="<?php echo esc_url( admin_url( 'user-edit.php?user_id=' . $user_id ) ); ?>"><?php echo esc_html( $user->display_name ); ?></a>
	</p>
	<?php endif; ?>

	<?php // Set attended status. ?>
	<?php $attended = get_post_meta( $post->ID, '_attended', true ); ?>
	<p class="form-field">
		<label for="field_attended"><?php _e( 'Attended', 'woocommerce-box-office' ); ?>:</label>
		<input type="checkbox" <?php checked( $attended, 'yes' ); ?> name="_attended" class="field_attended" id="field_attended" value="yes" />
		<span class="description"><?php _e( 'Mark this ticket as attended.', 'woocommerce-box-office' ); ?></span>
	</p>

	<?php $ticket_form->render(); ?>
</div>

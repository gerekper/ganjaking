<?php
$product_id = get_post_meta( $post->ID, '_product_id', true );
$product    = wc_get_product( $product_id );
$ticket_ids = get_post_meta( $post->ID, '_ticket_ids', true );
?>

<div class="panel woocommerce_options_panel">

	<p class="form-field">
		<strong>
			<label><?php _e( 'Subject:', 'woocommerce-box-office' ); ?></label>
			<?php echo esc_html( $post->post_title ); ?>
		</strong>
	</p>

	<p class="form-field">
		<strong>
			<label><?php _e( 'Product:', 'woocommerce-box-office' ); ?></label>
			<a href="<?php echo esc_url( admin_url( 'post.php?post=' . $product_id . '&action=edit' ) ); ?>">
				<?php echo esc_html( $product->get_title() ); ?>
			</a>
		</strong>
	</p>

	<p class="form-field">
		<strong>
			<label for="sent_by"><?php _e( 'Sent by:', 'woocommerce-box-office' ); ?></label>
			<?php echo esc_html( get_the_author_meta( 'display_name', $post->post_author ) ); ?>
		</strong>
	</p>

	<p class="form-field">
		<strong>
			<label for="date_sent"><?php _e( 'Date sent:', 'woocommerce-box-office' ); ?></label>
			<?php echo esc_html( $post->post_date ); ?>
		</strong>
	</p>

	<p class="form-field">
		<strong>
			<label for="sent_by"><?php _e( 'Total target:', 'woocommerce-box-office' ); ?></label>
			<?php echo esc_html( count( $ticket_ids ) ); ?>
		</strong>
	</p>
</div>

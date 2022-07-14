<div class="upw-overlay-content">

	<a href="#" class="upw-close"><?php _e('Close','userpro-woocommerce'); ?></a>

	<div class="upw-new">

		<div class="upw-user">
			
			<div class="upw-user-thumb"><?php echo get_avatar($user_id, 50); ?></div>
			<div class="upw-user-info">
				<div class="upw-user-name">
					<a href="<?php echo $userpro->permalink($user_id); echo 'hello';?>"><?php echo userpro_profile_data('display_name', $user_id); ?></a><?php echo userpro_show_badges($user_id, $inline=true); ?>
				</div>
				<div class="upw-user-tab"><a href="<?php echo $userpro->permalink($user_id); ?>" class="userpro-flat-btn"><?php _e('View Profile','userpro-woocommerce'); ?></a></div>
			</div>
			
		<div class="userpro-clear"></div>
		</div>
		<div class="upw-order-body-wrapper">
		<div class="upw-body">
			
			<p class=""><?php printf( __( 'Order #<strong class="order-number">%s</strong> was placed on <strong class="order-date">%s</strong> and is currently <strong class="order-status">%s</strong>.', 'userpro-woocommerce' ), $order->get_order_number(), date_i18n( get_option( 'date_format' ), strtotime( $order->order_date ) ), wc_get_order_status_name( $order->get_status() ) ); ?></p>
	<?php if ( $notes = $order->get_customer_order_notes() ) { ?>
			
			<h2><?php _e( 'Order Updates', 'woocommerce' ); ?></h2>
			<ol class="commentlist notes">
				<?php foreach ( $notes as $note ) : ?>
				<li class="">
					<div class="">
						<div class="">
							<p class=""><?php echo date_i18n( __( 'l jS \o\f F Y, h:ia', 'userpro-woocommerce' ), strtotime( $note->comment_date ) ); ?></p>
							<div class="">
								<?php echo wpautop( wptexturize( $note->comment_content ) ); ?>
							</div>
							<div class="userpro-clear"></div>
						</div>
						<div class="userpro-clear"></div>
					</div>
				</li>
				<?php endforeach; ?>
			</ol>
		
		<?php
			}
			 do_action( 'woocommerce_view_order', $order_id );		
		?>
		</div>
		
	</div>
	</div>
</div>

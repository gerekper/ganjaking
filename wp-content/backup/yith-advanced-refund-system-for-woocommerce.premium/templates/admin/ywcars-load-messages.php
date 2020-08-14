<?php
$messages = $request->get_messages();
$customer_id = $request->customer_id;
$customer = new WP_User( $customer_id );
?>
<div class="message-list">
	<?php if ( $messages ) : ?>
		<?php foreach ( array_reverse( $messages ) as $message ) : ?>
			<?php
			$admin_message = user_can( $message->author, 'manage_woocommerce' );
			?>
            <div class="ywcars_refund_info_message_box ywcars_message<?php echo $admin_message ? '_shop_manager' : '_customer' ?>">
                <div>
                    <div class="ywcars_refund_info_message_author"><?php
						echo $admin_message ?
							__( 'Shop Manager', 'yith-advanced-refund-system-for-woocommerce' ) :
							ucwords( $customer->display_name ) . ':';
						?></div>
                    <span class="ywcars_refund_info_message_date"><?php echo apply_filters( 'ywcars_datetime', $message->date ); ?></span>
                </div>
                <div class="ywcars_refund_info_message_body">
                    <span><?php echo nl2br( htmlspecialchars( $message->message ) ); ?></span>
                </div>
				<?php if ( $message->get_message_metas() ) : ?>
                    <div class="ywcars_attachments_line_separator"></div>
                    <div class="ywcars_attachments">
                        <div class="ywcars_attachments_title"><?php esc_html_e( 'Message attachments:', 'yith-advanced-refund-system-for-woocommerce' ); ?></div>
						<?php foreach ( $message->get_message_metas() as $name => $url ) : ?>
                            <?php $is_image = getimagesize( $url ); ?>
                            <div class="ywcars_single_attachment">
                                <a target="_blank" href="<?php echo $url; ?>">
                                    <img class="ywcars_attachment_thumbnail" src="<?php echo $is_image ? $url : YITH_WCARS_ASSETS_URL . 'images/attachment.png'; ?>">
                                    <span class="ywcars_attachment_file_name"><?php echo $name; ?></span>
                                </a>
                            </div>
						<?php endforeach; ?>
                    </div>
				<?php endif; ?>
            </div>
		<?php endforeach; ?>
	<?php endif; ?>
</div>
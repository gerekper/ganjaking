<?php

use \Essential_Addons_Elementor\Classes\Helper;

/**
 * Template Name: Preset 3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly
$order_items        = $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );
$show_purchase_note = $order->has_status( apply_filters( 'woocommerce_purchase_note_order_statuses', array(
	'completed',
	'processing'
) ) );

$show_customer_details = is_user_logged_in() && $order->get_user_id() === get_current_user_id();
$downloads             = $order->get_downloadable_items();
$show_downloads        = $order->has_downloadable_item() && $order->is_download_permitted();
?>
<?php if ( $order->has_status( 'failed' ) ) :
	echo 'faild';
else:
	?>

    <div class="eael-thankyou-container">
        <div class="eael-thankyou-left">
			<?php if ( $settings['eael_show_thankyou_message'] === 'yes' ): ?>
                <div class="eael-thankyou-message">
					<?php if ( $settings['eael_show_thankyou_message_icon']['value'] !== '' ): ?>
                        <div class="eael-thankyou-message-icon"><?php \Elementor\Icons_Manager::render_icon( $settings['eael_show_thankyou_message_icon'], [
								'aria-hidden' => 'true',
								'class'       => 'eael-thankyou-icon'
							] ); ?></div>
					<?php endif; ?>
                    <div class="eael-thankyou-message-text-area">
                        <div class="eael-thankyou-text"><?php echo esc_html( $settings['eael_thankyou_text'] ); ?></div>
                        <div class="eael-thankyou-message-text"><?php echo Helper::eael_wp_kses( $settings['eael_thankyou_message'] ) ?></div>
                    </div>
                </div>
			<?php endif; ?>

			<?php if ( $settings['eael_show_order_overview'] === 'yes' ): ?>
                <div class="eael-thankyou-order-overview">
					<?php if ( $settings['eael_show_order_overview_section_title'] === 'yes' ): ?>
                        <h2 class="eael-order-overview-title"><?php echo esc_html( $settings['eael_order_overview_section_title'] ) ?></h2>
					<?php endif; ?>
                    <ul class="woocommerce-order-overview order_details">
						<?php if ( $settings['eael_show_order_overview_number'] === 'yes' ): ?>
                            <li class="woocommerce-order-overview__order order">
                                <span class="woocommerce-order-overview-label"><?php echo esc_html( $settings['eael_order_overview_number_label'] ); ?></span>
                                <span class="woocommerce-order-overview-value"><?php echo $order->get_order_number(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
                            </li>
						<?php endif; ?>

						<?php if ( $settings['eael_show_order_overview_date'] === 'yes' ): ?>
                            <li class="woocommerce-order-overview__date date">
                                <span class="woocommerce-order-overview-label"><?php echo esc_html( $settings['eael_order_overview_date_label'] ); ?></span>
                                <span class="woocommerce-order-overview-value"><?php echo wc_format_datetime( $order->get_date_created(), $settings['eael_order_overview_date_format'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
                            </li>
						<?php endif; ?>

						<?php if ( $settings['eael_show_order_overview_email'] === 'yes' && is_user_logged_in() && $order->get_user_id() === get_current_user_id() && $order->get_billing_email() ) : ?>
                            <li class="woocommerce-order-overview__email email">
                                <span class="woocommerce-order-overview-label"><?php echo esc_html( $settings['eael_order_overview_email_label'] ); ?></span>
                                <span class="woocommerce-order-overview-value"><?php echo $order->get_billing_email(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
                            </li>
						<?php endif; ?>

						<?php if ( $settings['eael_show_order_overview_total'] === 'yes' ): ?>
                            <li class="woocommerce-order-overview__total total">
                                <span class="woocommerce-order-overview-label"><?php echo esc_html( $settings['eael_order_overview_total_label'] ); ?></span>
                                <span class="woocommerce-order-overview-value"><?php echo $order->get_formatted_order_total(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
                            </li>
						<?php endif; ?>

						<?php if ( $settings['eael_show_order_overview_payment_method'] === 'yes' && $order->get_payment_method_title() ) : ?>
                            <li class="woocommerce-order-overview__payment-method method">
                                <span class="woocommerce-order-overview-label"><?php echo esc_html( $settings['eael_order_overview_payment_label'] ); ?></span>
                                <span class="woocommerce-order-overview-value"><?php echo wp_kses_post( $order->get_payment_method_title() ); ?></span>
                            </li>
						<?php endif; ?>

                    </ul>
                </div>
			<?php endif; ?>
            <div class="eael-thankyou-payment-extra-info">
	            <?php do_action( 'woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id() ); ?>
            </div>
            <div class="eael-thankyou-billing-shipping eael-thankyou-billing-shipping-for-desktop">
				<?php if ( $settings['eael_show_order_billing'] === 'yes' ): ?>
                    <div class="eael-thankyou-billing">
						<?php
						if ( $settings['eael_show_billing_title'] === 'yes' ) {
							echo "<h2 class='eael-thankyou-billing-title'>" . esc_html( $settings['eael_order_billing_title'] ) . "</h2>";
						}
						?>
                        <div class="eael-thankyou-billing-address">
							<?php echo wp_kses_post( $order->get_formatted_billing_address( esc_html__( 'N/A', 'woocommerce' ) ) ); ?>
                        </div>

						<?php if ( $settings['eael_show_billing_cell_no'] === 'yes' && $order->get_billing_phone() ): ?>
                            <div class="eael-thankyou-phone">
								<?php
								if ( $settings['eael_show_billing_cell_label_type'] === 'icon' ) {
									\Elementor\Icons_Manager::render_icon( $settings['eael_show_billing_cell_label_icon'], [
										'aria-hidden' => 'true',
										'class'       => 'eael-thankyou-billing-cell-icon eael-thankyou-icon'
									] );
								} else {
									echo esc_html( $settings['eael_show_billing_cell_label'] );
								}
								echo esc_html( $order->get_billing_phone() );
								?>
                            </div>
						<?php endif; ?>

						<?php if ( $settings['eael_show_billing_email'] === 'yes' && $order->get_billing_email() ): ?>
                            <div class="eael-thankyou-email">
								<?php
								if ( $settings['eael_show_billing_email_label_type'] === 'icon' ) {
									\Elementor\Icons_Manager::render_icon( $settings['eael_show_billing_email_label_icon'], [
										'aria-hidden' => 'true',
										'class'       => 'eael-thankyou-billing-email-icon eael-thankyou-icon'
									] );
								} else {
									echo esc_html( $settings['eael_show_billing_email_label'] );
								}
								echo esc_html( $order->get_billing_email() );
								?>
                            </div>
						<?php endif; ?>
                    </div>
				<?php endif; ?>
				<?php if ( $settings['eael_show_order_shipping'] === 'yes' ): ?>
                    <div class="eael-thankyou-shipping">
						<?php
						if ( $settings['eael_show_shipping_title'] === 'yes' ) {
							echo "<h2 class='eael-thankyou-shipping-title'>" . esc_html( $settings['eael_order_shipping_title'] ) . "</h2>";
						}
						?>
                        <div class="eael-thankyou-shipping-address">
							<?php echo wp_kses_post( $order->get_formatted_shipping_address( esc_html__( 'N/A', 'woocommerce' ) ) ); ?>
                        </div>
                    </div>
				<?php endif; ?>
            </div>
        </div>
        <div class="eael-thankyou-right">

            <div class="eael-thankyou-order-details">

				<?php if ( $settings['eael_show_order_details'] === 'yes' ): ?>

                    <div class="eael-thankyou-order-items">
                        <table class="eael-thankyou-order-items-table">

							<?php if ( $settings['eael_show_order_table_heading'] === 'yes' ): ?>

                                <thead>
                                <tr>
                                    <th class="eael-thankyou-order-products"><?php echo esc_html( $settings['eael_order_table_product_label'] ); ?></th>
                                    <th class="eael-thankyou-order-totals"><?php echo esc_html( $settings['eael_order_table_total_label'] ); ?></th>
                                </tr>
                                </thead>

							<?php endif; ?>

                            <tbody>
							<?php
							foreach ( $order_items as $item_id => $item ) {
								$product = $item->get_product();
								?>
                                <tr class="eael-thankyou-order-item">

                                    <td class="eael-thankyou-order-item-details">
										<?php
										if ( $settings['eael_show_order_items_image'] === 'yes' ) {
											$image_url = wp_get_attachment_image_url( $product->get_image_id(), 'full' );
											echo '<img class="eael-thankyou-product-image" src="' . esc_url( $image_url ) . '" alt="' . esc_attr( $item->get_name() ) . '">';
										}
										echo "<div class='eael-thankyou-product-info'>";
										if ( $settings['eael_show_order_items_name'] === 'yes' ) {
											echo "<div class='eael-thankyou-product-name'>";
											$is_visible        = $product && $product->is_visible();
											$product_permalink = apply_filters( 'woocommerce_order_item_permalink', $is_visible ? $product->get_permalink( $item ) : '', $item, $order );

											echo wp_kses_post( apply_filters( 'woocommerce_order_item_name', $product_permalink ? sprintf( '<a href="%s">%s</a>', $product_permalink, $item->get_name() ) : $item->get_name(), $item, $is_visible ) );
											echo "</div>";
										}
										echo "<div class='eael-thankyou-product-summary'>";

										if ( $settings['eael_show_order_items_meta'] === 'yes' && $item->get_all_formatted_meta_data() ) {
											$hide_meta_label = empty( $settings['eael_show_order_items_meta_label'] ) || $settings['eael_show_order_items_meta_label'] != 'yes' ? 'hide-meta-label' : '';
											echo "<div class='eael-thankyou-product-meta " . esc_attr( $hide_meta_label ) . "'>";
											do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order, false );

											$meta_args = [
												'separator'    => ',</li><li>',
												'label_before' => '<span class="wc-item-meta-label">',
												'label_after'  => ':</span> '
											];

											wc_display_item_meta( $item, $meta_args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

											do_action( 'woocommerce_order_item_meta_end', $item_id, $item, $order, false );
											echo "</div>";
										}

										if ( $settings['eael_show_order_items_qty'] === 'yes' ) {
											echo "<div class='eael-thankyou-product-qty'>";
											$qty          = $item->get_quantity();
											$refunded_qty = $order->get_qty_refunded_for_item( $item_id );

											if ( $refunded_qty ) {
												$qty_display = '<del>' . esc_html( $qty ) . '</del> <ins>' . esc_html( $qty - ( $refunded_qty * - 1 ) ) . '</ins>';
											} else {
												$qty_display = esc_html( $qty );
											}

											echo apply_filters( 'woocommerce_order_item_quantity_html', ' <span class="product-quantity">' . sprintf( 'Qty: %s', $qty_display ) . '</span>', $item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
											echo "</div>";
										}
										echo "</div></div>";
										?>
                                    </td>

                                    <td class="eael-thankyou-order-item-total">
										<?php echo $order->get_formatted_line_subtotal( $item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                    </td>

                                </tr>
								<?php
							}
							?>
                            </tbody>

                        </table>
                    </div>

				<?php endif; ?>

                <div class="eael-thankyou-order-summary">
					<?php if ( $settings['eael_show_order_summary'] === 'yes' ): ?>
                        <table class="eael-thankyou-order-summary-table">
							<?php
							foreach ( $order->get_order_item_totals() as $key => $total ) {
								?>
                                <tr class="eael-thankyou-order-summary-<?php echo strtolower( str_replace( [
									' ',
									':'
								], [ '-', '' ], esc_attr( $total['label'] ) ) ) ?>">
                                    <th scope="row"><?php echo esc_html( $total['label'] ); ?></th>
                                    <td><?php echo wp_kses_post( $total['value'] ); ?></td>
                                </tr>
								<?php
							}
							if ( $order->get_customer_note() ) : ?>
                                <tr class="eael-thankyou-order-summary-note">
                                    <th><?php esc_html_e( 'Note:', 'woocommerce' ); ?></th>
                                    <td><?php echo wp_kses_post( nl2br( wptexturize( $order->get_customer_note() ) ) ); ?></td>
                                </tr>
							<?php endif; ?>
                        </table>
					<?php endif; ?>
                </div>

            </div>

            <div class="eael-thankyou-billing-shipping eael-thankyou-billing-shipping-for-mobile">
				<?php if ( $settings['eael_show_order_billing'] === 'yes' ): ?>
                    <div class="eael-thankyou-billing">
						<?php
						if ( $settings['eael_show_billing_title'] === 'yes' ) {
							echo "<h2 class='eael-thankyou-billing-title'>" . esc_html( $settings['eael_order_billing_title'] ) . "</h2>";
						}
						?>
                        <div class="eael-thankyou-billing-address">
							<?php echo wp_kses_post( $order->get_formatted_billing_address( esc_html__( 'N/A', 'woocommerce' ) ) ); ?>
                        </div>

						<?php if ( $settings['eael_show_billing_cell_no'] === 'yes' && $order->get_billing_phone() ): ?>
                            <div class="eael-thankyou-phone">
								<?php
								if ( $settings['eael_show_billing_cell_label_type'] === 'icon' ) {
									\Elementor\Icons_Manager::render_icon( $settings['eael_show_billing_cell_label_icon'], [
										'aria-hidden' => 'true',
										'class'       => 'eael-thankyou-billing-cell-icon eael-thankyou-icon'
									] );
								} else {
									echo esc_html( $settings['eael_show_billing_cell_label'] );
								}
								echo esc_html( $order->get_billing_phone() );
								?>
                            </div>
						<?php endif; ?>

						<?php if ( $settings['eael_show_billing_email'] === 'yes' && $order->get_billing_email() ): ?>
                            <div class="eael-thankyou-email">
								<?php
								if ( $settings['eael_show_billing_email_label_type'] === 'icon' ) {
									\Elementor\Icons_Manager::render_icon( $settings['eael_show_billing_email_label_icon'], [
										'aria-hidden' => 'true',
										'class'       => 'eael-thankyou-billing-email-icon eael-thankyou-icon'
									] );
								} else {
									echo esc_html( $settings['eael_show_billing_email_label'] );
								}
								echo esc_html( $order->get_billing_email() );
								?>
                            </div>
						<?php endif; ?>
                    </div>
				<?php endif; ?>
				<?php if ( $settings['eael_show_order_shipping'] === 'yes' ): ?>
                    <div class="eael-thankyou-shipping">
						<?php
						if ( $settings['eael_show_shipping_title'] === 'yes' ) {
							echo "<h2 class='eael-thankyou-shipping-title'>" . esc_html( $settings['eael_order_shipping_title'] ) . "</h2>";
						}
						?>
                        <div class="eael-thankyou-shipping-address">
							<?php echo wp_kses_post( $order->get_formatted_shipping_address( esc_html__( 'N/A', 'woocommerce' ) ) ); ?>
                        </div>
                    </div>
				<?php endif; ?>
            </div>
        </div>
    </div>
<?php
endif;
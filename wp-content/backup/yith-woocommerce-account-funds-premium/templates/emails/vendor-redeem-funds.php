<?php
/**
 * HTML Template Email Funds
 *
 * @package YITH WooCommerce Funds
 * @since   1.0.0
 * @author  YITHEMES
 */

$text_align = is_rtl() ? 'right' : 'left';
do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

    <p><?php _e( 'The following vendors have redeem funds:', 'yith-woocommerce-account-funds' ); ?></p>
    <table class="td" cellpadding="6" cellspacing="0" border="1"
           style="width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">
        <thead>
        <tr>
            <th class="td" scope="col"
                style="text-align: <?php echo esc_attr( $text_align ); ?>"><?php esc_html_e( 'Vendor Info', 'yith-woocommerce-account-funds' ); ?></th>
            <th class="td" scope="col"
                style="text-align: <?php echo esc_attr( $text_align ); ?>"><?php esc_html_e( 'Amount redeemed', 'yith-woocommerce-account-funds' ); ?></th>
        </tr>
        </thead>
        <tbody>
		<?php foreach ( $redeem_transactions as $single_redeem ): ?>
            <tr class="order_item yith_redeem_item">
                <td class="td"
                    style="text-align:<?php echo esc_attr( $text_align ); ?>; vertical-align: middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; word-wrap:break-word;">
					<?php $user_id = $single_redeem['user_id'];
					$user          = get_user_by( 'id', $user_id );
					$vendor        = yith_get_vendor( $user_id, 'user' );

					$vendor_info = sprintf(
						'<span class="yith_redeem_vendor_info">#%s %s (%s) <small>%s</small></span>',
						$user_id,
						$user->user_lastname . ' ' . $user->first_name,
						$vendor->name,
						$user->user_email
					);

					echo $vendor_info;
					?>
                </td>
                <td class="td"
                    style="text-align:<?php echo esc_attr( $text_align ); ?>; vertical-align: middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; word-wrap:break-word;">
	                <?php
	                    $amount = wc_price( $single_redeem['amount'], array( 'currency' => $single_redeem['currency'] ) );
	                    echo $amount;
	                ?>
                </td>
            </tr>
		<?php endforeach; ?>
        </tbody>

    </table>
<?php
do_action( 'woocommerce_email_footer', $email );

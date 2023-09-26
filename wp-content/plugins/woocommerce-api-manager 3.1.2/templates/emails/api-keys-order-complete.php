<?php
/**
 * API Keys Order Complete Email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/api-keys-order-complete.php.
 *
 * HOWEVER, on occasion WooCommerce API Manager will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  Todd Lahman LLC
 * @package WooCommerce API Manager/Templates/Emails
 * @version 3.1
 */

defined( 'ABSPATH' ) || exit;

$text_align = is_rtl() ? 'right' : 'left';

if ( is_object( $order ) && ! empty( $resources ) ) {
	$hide_product_order_api_keys = WC_AM_USER()->hide_product_order_api_keys();
	$hide_master_api_key         = WC_AM_USER()->hide_master_api_key();

	?><h2><?php esc_html_e( apply_filters( 'wc_api_manager_email_api_product_heading', __( 'API Product Information', 'woocommerce-api-manager' ) ) ); ?></h2>

    <div style="margin-bottom: 40px;">
        <table class="td" cellspacing="0" cellpadding="6" style="width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;" border="1">
            <thead>
            <tr>
                <th class="td" scope="col" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php esc_html_e( 'Product', 'woocommerce-api-manager' ); ?></th>
                <th class="td" scope="col" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php esc_html_e( 'ID', 'woocommerce-api-manager' ); ?></th>
                <th class="td" scope="col" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php esc_html_e( 'Activations', 'woocommerce-api-manager' ); ?></th>
                <th class="td" scope="col" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php esc_html_e( 'Expires', 'woocommerce-api-manager' ); ?></th>
            </tr>
            </thead>
            <tbody>
			<?php foreach ( $resources as $resource ) {
				$product_object = WC_AM_PRODUCT_DATA_STORE()->get_product_object( $resource->product_id );
				?>
                <tr>
                    <td class="td" style="text-align:<?php esc_attr_e( $text_align ); ?>; vertical-align:middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">
						<?php esc_attr_e( $product_object->get_title() ); ?>
                    </td>
                    <td class="td" style="text-align:center; vertical-align:middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">
						<?php echo absint( $resource->product_id ) ?>
                    </td>
                    <td class="td" style="text-align:center; vertical-align:middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">
						<?php echo absint( $resource->activations_purchased_total ) ?>
                    </td>
                    <td class="td" style="text-align:center; vertical-align:middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">
						<?php
						if ( WCAM()->get_wc_subs_exist() && ! empty( $resource->sub_id ) ) {
							esc_html_e( ( WC_AM_SUBSCRIPTION()->has_end_date_by_sub( $resource->sub_id ) ) ? date_i18n( wc_date_format(), WC_AM_SUBSCRIPTION()->get_subscription_time_by_sub_id( $resource->sub_id, 'end', 'site' ) ) : _x( 'When Cancelled', 'Used as end date for an indefinite subscription', 'woocommerce-api-manager' ) );
						} else {
							if ( WC_AM_ORDER_DATA_STORE()->is_time_expired( $resource->access_expires ?? false ) ) {
								$expires = __( 'Expired', 'woocommerce-api-manager' );
							} else {
								$expires = $resource->access_expires == 0 ? _x( 'When Cancelled', 'Used as end date for an indefinite subscription', 'woocommerce-api-manager' ) : esc_attr( WC_AM_FORMAT()->unix_timestamp_to_date( $resource->access_expires ) );
							}

							esc_html_e( $expires );
						}
						?>
                    </td>
                </tr>
				<?php if ( ! $hide_product_order_api_keys ) { ?>
                    <tr>
                        <td class="td" colspan="4" style="text-align:<?php esc_attr_e( $text_align ); ?>; vertical-align:middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">
                            <strong><?php esc_html_e( apply_filters( 'wc_api_manager_email_product_order_api_keys_row', __( 'Product Order API Key(s):', 'woocommerce-api-manager' ) ) ); ?></strong>
                            <br><?php echo esc_attr( $resource->product_order_api_key ); ?>
                        </td>
                    </tr>
				<?php } ?>
			<?php } ?>
            </tbody>
            <tfoot>
			<?php
			if ( $order->has_downloadable_item() ) {
				$my_account_url = wc_get_endpoint_url( 'api-downloads', '', wc_get_page_permalink( 'myaccount' ) );
				?>
                <tr>
                    <td class="td" scope="row" colspan="4" style="text-align:center;"><a
                                href="<?php echo esc_url( ( $my_account_url ) ); ?>"><?php echo esc_html__( 'Click here to login and download your file(s)', 'woocommerce-api-manager' ); ?></a></td>
                </tr>
			<?php }
			if ( ! $hide_master_api_key ) {
				?>
                <tr>
                    <th class="td" scope="row" colspan="4" style="text-align:center;"><?php esc_html_e( apply_filters( 'wc_api_manager_email_master_api_key_row', __( 'Master API Key', 'woocommerce-api-manager' ) ) ); ?></th>
                </tr>
                <tr>
                    <td class="td" scope="row" colspan="4" style="text-align:center;"><?php esc_attr_e( WC_AM_USER()->get_master_api_key( $order->get_customer_id() ) ) ?></td>
                </tr>
                <tr id="email_master_api_key_message_row">
                    <td class="td" scope="row" colspan="4"
                        style="text-align:center;"><?php esc_html_e( apply_filters( 'wc_api_manager_email_master_api_key_message_row', __( 'A Master API Key can be used to activate any and all products.', 'woocommerce-api-manager' ) ) ); ?></td>
                </tr>
				<?php
			}
			?>
            </tfoot>
        </table>
    </div>
<?php }
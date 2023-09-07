<?php
/**
 * Related API Resources
 *
 * Shows API Resources on the account page.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/related-api-resources.php.
 *
 * HOWEVER, on occasion WooCommerce API Manager will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @since   3.1
 *
 * @author  Todd Lahman LLC
 * @package WooCommerce API Manager/Templates
 * @version 3.1
 */

defined( 'ABSPATH' ) || exit;
?>
<header>
    <h2><?php esc_html_e( 'Related API Resources', 'woocommerce-api-manager' ); ?></h2>
</header>

<table class="shop_table shop_table_responsive my_account_orders woocommerce-orders-table">
    <thead>
    <tr>
        <th class="order-number woocommerce-orders-table__header woocommerce-orders-table__header-order-number"><span class="nobr"><?php esc_html_e( apply_filters( 'wc_api_manager_related_api_resources_api_keys_heading', __( 'API Keys', 'woocommerce-api-manager' ) ) ); ?></span></th>
        <th class="order-number woocommerce-orders-table__header woocommerce-orders-table__header-order-number"><span class="nobr"><?php esc_html_e( apply_filters( 'wc_api_manager_related_api_resources_api_downloads_heading', __( 'API Downloads', 'woocommerce-api-manager' ) ) ); ?></span></th>
    </tr>
    </thead>
    <tbody>
    <tr class="order woocommerce-orders-table__row">
        <td class="order-status woocommerce-orders-table__cell woocommerce-orders-table__cell-order-status" style="white-space:nowrap;" data-title="<?php esc_attr_e( 'API Keys', 'woocommerce-api-manager' ); ?>">
			<?php echo '<a class="woocommerce-button button view" href="' . esc_url( wc_get_endpoint_url( 'api-keys', '', wc_get_page_permalink( 'myaccount' ) ) ) . '">' . esc_html__( apply_filters( 'wc_api_manager_related_api_resources_api_keys_row', __( 'View API Keys', 'woocommerce-api-manager' ) ) ) . '</a>'; ?>
        </td>
        <td class="order-status woocommerce-orders-table__cell woocommerce-orders-table__cell-order-status" style="white-space:nowrap;" data-title="<?php esc_attr_e( 'API Downloads', 'woocommerce-api-manager' ); ?>">
			<?php echo '<a class="woocommerce-button button view" href="' . esc_url( wc_get_endpoint_url( 'api-downloads', '', wc_get_page_permalink( 'myaccount' ) ) ) . '">' . esc_html__( apply_filters( 'wc_api_manager_related_api_resources_api_keys_row', __( 'View API Downloads', 'woocommerce-api-manager' ) ) ) . '</a>'; ?>
        </td>
    </tr>
    </tbody>
</table>

<?php do_action( 'wc_api_manager_after_related_api_resources_table', $order, $resources ); ?>

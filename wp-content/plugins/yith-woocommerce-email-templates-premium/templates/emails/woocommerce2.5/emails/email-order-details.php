<?php
/**
 * Order details table shown in emails.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/email-order-details.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see           http://docs.woothemes.com/document/template-structure/
 * @author        WooThemes
 * @package       WooCommerce/Templates/Emails
 * @version       2.5.0
 */

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

global $current_email;
$template           = yith_wcet_get_email_template( $current_email );
$meta               = yith_wcet_get_template_meta( $template );
$show_thumbs        = ( isset( $meta[ 'show_prod_thumb' ] ) ) ? $meta[ 'show_prod_thumb' ] : false;
$premium_mail_style = ( !empty( $meta[ 'premium_mail_style' ] ) ) ? $meta[ 'premium_mail_style' ] : 0;

$titles = array(
    'product'  => __( 'Product', 'woocommerce' ),
    'quantity' => __( 'Quantity', 'woocommerce' ),
    'price'    => __( 'Price', 'woocommerce' )
);

foreach ( $titles as $key => $value ) {
    $titles[ $key ] = apply_filters( 'yith_wcet_order_details_table_title_' . $key, $value, $order, $sent_to_admin, $plain_text, $email );
}

do_action( 'woocommerce_email_before_order_table', $order, $sent_to_admin, $plain_text, $email );

$order_id   = $order instanceof WC_Data ? $order->get_id() : $order->id;
$order_date = yit_get_prop( $order, 'order_date', true );
?>

<?php if ( apply_filters( 'yith_wcet_order_details_show_order_title', true, $order, $sent_to_admin, $plain_text, $email ) ): ?>
    <?php if ( !$sent_to_admin ) : ?>
        <h2><?php printf( __( 'Order #%s', 'woocommerce' ), $order->get_order_number() ); ?></h2>
    <?php else : ?>
        <h2><a class="link"
               href="<?php echo esc_url( admin_url( 'post.php?post=' . $order_id . '&action=edit' ) ); ?>"><?php printf( __( 'Order #%s', 'woocommerce' ), $order->get_order_number() ); ?></a>
            (<?php printf( '<time datetime="%s">%s</time>', date_i18n( 'c', strtotime( $order_date ) ), date_i18n( wc_date_format(), strtotime( $order_date ) ) ); ?>
            )</h2>
    <?php endif; ?>
<?php endif; ?>

<table id="yith-wcet-order-items-table" cellspacing="0" cellpadding="6" style="width: 100%;">
    <thead>
    <tr>
        <th id="yith-wcet-th-title-product" class="yith-wcet-order-items-table-element" scope="col" style="padding:6px"><?php echo $titles[ 'product' ] ?></th>
        <th id="yith-wcet-th-title-quantity" class="yith-wcet-order-items-table-element" scope="col" style="padding:6px"><?php echo $titles[ 'quantity' ] ?></th>
        <th id="yith-wcet-th-title-price" class="yith-wcet-order-items-table-element" scope="col" style="padding:6px"><?php echo $titles[ 'price' ] ?></th>
    </tr>
    </thead>
    <tbody>
    <?php
    $order_table_args = array(
        'show_sku'      => $sent_to_admin,
        'show_image'    => !!$show_thumbs,
        'image_size'    => apply_filters( 'yith_wcet_product_thumbnail_size', array( 32, 32 ), $template, $current_email ),
        'plain_text'    => $plain_text,
        'sent_to_admin' => $sent_to_admin
    );
    echo function_exists( 'wc_get_email_order_items' ) ? wc_get_email_order_items( $order, $order_table_args ) : $order->email_order_items_table( $order_table_args ); ?>
    </tbody>

    <?php if ( $premium_mail_style < 2 ): ?>
        <tfoot>
        <?php
        if ( $totals = $order->get_order_item_totals() ) {
            $i       = 0;
            $t_count = count( $totals );
            foreach ( $totals as $total ) {
                $i++;
                $last_class = $i == $t_count ? 'last' : 'not_last';
                ?>
                <tr>
                <th class="yith-wcet-order-items-table-element<?php if ( $i == 1 )
                    echo '-bigtop'; ?> <?php echo $last_class; ?>" scope="row" colspan="2"><?php echo $total[ 'label' ]; ?></th>
                <td class="yith-wcet-order-items-table-element<?php if ( $i == 1 )
                    echo '-bigtop'; ?> <?php echo $last_class; ?>"><?php echo $total[ 'value' ]; ?></td>
                </tr><?php
            }
        }
        ?>
        </tfoot>
    <?php endif ?>
</table>

<?php if ( $premium_mail_style > 1 ):
    $totals_table_width_percentage = absint( apply_filters( 'yith_wcet_order_details_table_totals_table_width_percentage', '50', $current_email, $premium_mail_style ) );
    $first_column_width_percentage = 100 - $totals_table_width_percentage; ?>
    <table width="100%">
        <tr>
            <td width="100%"></td>
        </tr>
    </table>
    <table class="yith-wcet-two-columns" width="100%" cellspacing="0" cellpadding="0">
        <tr>
            <td width="<?php echo $first_column_width_percentage ?>%" style="padding:0px">

            </td>
            <td width="<?php echo $totals_table_width_percentage ?>%" style="padding:0px">
                <table id="yith-wcet-foot-price-list">
                    <?php
                    if ( $totals = $order->get_order_item_totals() ) {
                        $i       = 0;
                        $t_count = count( $totals );
                        foreach ( $totals as $total ) {
                            $i++;
                            $last_class  = $i == $t_count ? 'last' : 'not_last';
                            $total_label = str_replace( ':', '', $total[ 'label' ] );
                            $total_label = apply_filters( 'yith_wcet_total_label', $total_label, $current_email );
                            ?>
                            <tr>
                            <th <?php if ( $i == $t_count ) {
                                echo 'id="yith-wcet-total-title"';
                            } ?> class="<?php echo $last_class; ?>" scope="row" colspan="2"><?php echo $total_label; ?></th>
                            <td <?php if ( $i == $t_count ) {
                                echo 'id="yith-wcet-total-price"';
                            } ?> class="<?php echo $last_class; ?>"><?php echo $total[ 'value' ]; ?></td>
                            </tr><?php
                        }
                    }
                    ?>
                </table>
            </td>
        </tr>
    </table>
<?php endif ?>

<?php do_action( 'woocommerce_email_after_order_table', $order, $sent_to_admin, $plain_text, $email ); ?>

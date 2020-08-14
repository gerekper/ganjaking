<?php
/*
 * Template for Message
 */

$sent_by_user = get_comment_meta( $message->comment_ID, 'sent_by_user', true );

$sent_by_user_class = !!$sent_by_user ? 'yith-wcmbs-message-sent-by-user' : '';

?>

<li>
    <div class="yith-wcmbs-message-container <?php echo $sent_by_user_class; ?>">
        <div class="yith-wcmbs-message-content">
            <?php echo wpautop( wptexturize( wp_kses_post( $message->comment_content ) ) ); ?>
        </div>
        <div class="yith-wcmbs-message-date">
            <abbr class="exact-date"
                  title="<?php echo $message->comment_date; ?>"><?php printf( __( 'sent on %1$s at %2$s', 'yith-woocommerce-membership' ), date_i18n( wc_date_format(), strtotime( $message->comment_date ) ), date_i18n( wc_time_format(), strtotime( $message->comment_date ) ) ); ?></abbr>
        </div>
    </div>
</li>

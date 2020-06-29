<?php
if( !defined('ABSPATH' ) )
    exit;

if( is_user_logged_in() ){


    $user_id = get_current_user_id();
    $customer = new YITH_YWF_Customer( $user_id );
    $fund_av = apply_filters( 'yith_show_available_funds', $customer->get_funds() );
    $message = apply_filters( 'ywf_message_available_fund', sprintf('%s %s ', $message, wc_price( $fund_av ) ) );
?>
    <div class="ywf_fund_av">
        <p class="ywf_fund_message" style="text-align: <?php echo $text_align;?>;font-weight: <?php echo $font_weight;?>"><?php echo $message;?></p>
    </div>
<?php
}
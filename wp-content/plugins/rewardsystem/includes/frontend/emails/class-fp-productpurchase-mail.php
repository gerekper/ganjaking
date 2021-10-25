<?php

$SelectedUser = unserialize( $emails->sendmail_to ) ;
$Sendmailto   = ($emails->sendmail_options == '1') ? true : in_array( $user_id , $SelectedUser ) ;
if ( ! $Sendmailto )
    return ;

global $unsublink2 ;
if ( get_user_meta( $user_id , 'unsub_value' , true ) == 'yes' )
    return ;

$WMPLLang   = empty( get_user_meta( $user_id , 'rs_wpml_lang' , true ) ) ? 'en' : get_user_meta( $user_id , 'rs_wpml_lang' , true ) ;
$UserInfo   = get_userdata( $user_id ) ;
$subject    = RSWPMLSupport::fp_wpml_text( 'rs_template_' . $emails->id . '_subject' , $WMPLLang , $emails->subject ) ;
$SiteURl    = "<a href=" . site_url() . ">" . site_url() . "</a>" ;
$PointsData = new RS_Points_Data( $user_id ) ;
$Points     = $PointsData->total_available_points() ;
if ( empty( $Points ) )
    return ;

$minimumuserpoints = empty( $emails->minimum_userpoints ) ? 0 : $emails->minimum_userpoints ;
if ( $Points < $minimumuserpoints )
    return ;

$PointsInOrder    = ($emails->rsmailsendingoptions == '1') ? earned_points_from_order( $order_id ) : redeem_points_from_order( $order_id ) ;
$PointsInTemplate = ($emails->rsmailsendingoptions == '1') ? (empty( $emails->earningpoints ) ? 0 : $emails->earningpoints) : (empty( $emails->redeemingpoints ) ? 0 : $emails->redeemingpoints) ;
if ( $PointsInOrder < $PointsInTemplate )
    return ;

$referral_url      = get_option('rs_referral_link_site_referral_url')!= '' ? get_option('rs_referral_link_site_referral_url'): site_url();
$site_referral_url = get_option( 'rs_restrict_referral_points_for_same_ip' ) == 'yes' ? esc_url_raw( add_query_arg( array( 'ref' => $UserInfo->user_login , 'ip' => base64_encode( get_referrer_ip_address() ) ) , $referral_url ) ) : esc_url_raw( add_query_arg( array( 'ref' => $UserInfo->user_login ) , $referral_url ) ) ;
$site_referral_url = get_option( 'rs_referral_activated' ) =='yes' ? "<a href=" . $site_referral_url . ">" . $site_referral_url . "</a>"  : '';
$CurrencyValue = currency_value_for_available_points( $user_id ) ;
$wpnonce       = wp_create_nonce( 'rs_unsubscribe_' . $user_id ) ;
$unsublink     = esc_url_raw( add_query_arg( array( 'userid' => $user_id , 'unsub' => 'yes' , 'nonce' => $wpnonce ) , site_url() ) ) ;
$message       = RSWPMLSupport::fp_wpml_text( 'rs_template_' . $emails->id . '_message' , $WMPLLang , $emails->message ) ;
$message       = str_replace( '{rssitelink}' , $SiteURl , $message ) ;
$message       = str_replace( '{rsfirstname}' , $UserInfo->user_firstname , $message ) ;
$message       = str_replace( '{rslastname}' , $UserInfo->user_lastname , $message ) ;
$message       = str_replace( '{rspoints}' , $Points , $message ) ;
$message       = str_replace( '{site_referral_url}' , $site_referral_url , $message ) ;
$message       = str_replace( '{rs_points_in_currency}' , $CurrencyValue , $message ) ;
$message       = ( $emails->rsmailsendingoptions == '1' ) ? str_replace( '{rs_earned_points}' , $PointsInOrder , $message ) : str_replace( '{rs_redeemed_points}' , $PointsInOrder , $message ) ;
$levelname     = earn_level_name( $user_id ) ;
$message       = str_replace( '[rs_my_current_earning_level_name]' , $levelname , $message ) ;
$nextlevelname = points_to_reach_next_earn_level( $user_id ) ;
$message       = str_replace( '[rs_next_earning_level_points]' , $nextlevelname , $message ) ;
$message       = do_shortcode( $message ) ; //shortcode feature
$unsublink2    = str_replace( '{rssitelinkwithid}' , $unsublink , get_option( 'rs_unsubscribe_link_for_email' ) ) ;
add_filter( 'woocommerce_email_footer_text' , 'srp_footer_link' ) ;
ob_start() ;
wc_get_template( 'emails/email-header.php' , array( 'email_heading' => $subject ) ) ;
echo $message ;
wc_get_template( 'emails/email-footer.php' ) ;
$woo_temp_msg  = ob_get_clean() ;
$headers       = "MIME-Version: 1.0\r\n" ;
$headers       .= "Content-Type: text/html; charset=UTF-8\r\n" ;
if ( $emails->sender_opt == 'local' ) {
    FPRewardSystem::$rs_from_email_address = $emails->from_email ;
    FPRewardSystem::$rs_from_name          = $emails->from_name ;
}
add_filter( 'woocommerce_email_from_address' , 'rs_alter_from_email_of_woocommerce' , 10 , 2 ) ;
add_filter( 'woocommerce_email_from_name' , 'rs_alter_from_name_of_woocommerce' , 10 , 2 ) ;
if ( get_option( 'rs_select_mail_function' ) == 1 ) {
    if ( mail( $UserInfo->user_email , $subject , $woo_temp_msg , $headers ) ) {
        if ( $emails->mailsendingoptions == '1' )
            update_post_meta( $order_id , 'rsearningtemplates' . $emails->id , '1' ) ;
    }
} else {
    if ( WC_VERSION <= ( float ) ('2.2.0') ) {
        if ( wp_mail( $UserInfo->user_email , $subject , $woo_temp_msg , $headers = '' ) ) {
            if ( $emails->mailsendingoptions == '1' )
                update_post_meta( $order_id , 'rsearningtemplates' . $emails->id , '1' ) ;
        }
    } else {
        $mailer = WC()->mailer() ;
        $mailer->send( $UserInfo->user_email , $subject , $woo_temp_msg , $headers ) ;
        if ( $emails->mailsendingoptions == '1' )
            update_post_meta( $order_id , 'rsearningtemplates' . $emails->id , '1' ) ;
    }
}
remove_filter( 'woocommerce_email_from_address' , 'rs_alter_from_email_of_woocommerce' , 10 , 2 ) ;
remove_filter( 'woocommerce_email_from_name' , 'rs_alter_from_name_of_woocommerce' , 10 , 2 ) ;
FPRewardSystem::$rs_from_email_address = false ;
FPRewardSystem::$rs_from_name          = false ;

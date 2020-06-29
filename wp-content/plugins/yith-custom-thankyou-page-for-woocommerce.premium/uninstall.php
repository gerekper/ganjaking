<?php
/**
 * YITH Custom Thank You Page for Woocommerce Uninstall
 *
 * Delete plugin settings saved in database.
 *
 * @version 1.0.8
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

if ( get_option('yith_ctpw_uninstall_remove_options', false) == 'yes' ) {

$ctpw_options = array(
    'yith_ctpw_enable',
    'yith_ctpw_general_page_or_url',
    'yith_ctpw_general_page',
    'yith_ctpw_general_page_url',
    'yith_ctpw_priority',
    'yith_ctpw_show_header',
    'yith_ctpw_show_order_table',
    'yith_ctpw_show_customer_details',
    'ctpw_orderstyle_title_color',
    'ctpw_orderstyle_title_fontsize',
    'ctpw_social_orderstyle_title_fontweight',
    'yith_ctpw_enable_social_box',
    'yith_ctpw_enable_fb_social_box',
    'yith_ctpw_enable_twitter_social_box',
    'yith_ctpw_enable_google_social_box',
    'yith_ctpw_enable_pinterest_social_box',
    'ctpw_url_shortening',
    'ctpw_google_api_key',
    'ctpw_bitly_access_token',
    'ctpw_social_box_title_color',
    'ctpw_social_box_title_fontsize',
    'ctpw_social_box_title_fontweight',
    'ctpw_socials_titles_color',
    'ctpw_socials_titles_color_hover',
    'ctpw_socials_titles_color_active',
    'ctpw_socials_titles_color_active_hover',
    'ctpw_socials_box_main_background_selected',
    'ctpw_socials_box_main_background',
    'ctpw_socials_box_arrow_box_color',
    'ctpw_socials_box_button_color',
    'ctpw_social_box_button_title_fontsize',
    'ctpw_socials_box_button_fontcolor',
    'yith_ctpw_enable_upsells',
    'yith_ctpw_ups_columns',
    'yith_ctpw_ups_ppp',
    'yith_ctpw_ups_orderby',
    'yith_ctpw_ups_order',
    'yith_ctpw_upsells_ids',
    'ctpw_upsells_title_color',
    'ctpw_upsells_title_fontsize',
    'ctpw_upsells_title_fontweight',
    'yith_ctpw_enable_pdf',
    'yith_ctpw_enable_pdf_as_shortcode',
    'yith_ctpw_pdf_button_label',
    'yith_ctpw_pdf_button_colors',
    'yith_ctpw_pdf_button_text_colors',
    'yith_ctpw_pdf_show_logo',
    'yith_ctpw_pdf_custom_logo',
    'yith_ctpw_pdf_custom_logo_max_size',
    'yith_ctpw_pdf_show_order_header',
    'yith_ctpw_pdf_show_order_details_table',
    'yith_ctpw_pdf_show_customer_details',
    'yith_ctpw_pdf_footer_text',
    'yith_ctpw_dummy_order_id'
);

foreach ($ctpw_options as $o ) {
    delete_option( $o );
}

}
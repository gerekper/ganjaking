<?php
$customizer_settings     = function_exists('betterdocs_generate_output') ? betterdocs_generate_output() : '';
$customizer_settings_pro = function_exists('betterdocs_generate_output_pro') ? betterdocs_generate_output_pro() : ''; 
$live_search             = BetterDocs_DB::get_settings('live_search') != 'off' ? true : false;
$live_search_markup      = $live_search ? BetterDocs_Public::search() : '';
$cat_description         = $customizer_settings_pro['betterdocs_doc_list_desc_switch_layout6'] === true || $customizer_settings_pro['betterdocs_doc_list_desc_switch_layout6'] === '1' ? 'true' : 'false';
$image_enabler           = $customizer_settings_pro['betterdocs_doc_list_img_switch_layout6'] === true || $customizer_settings_pro['betterdocs_doc_list_img_switch_layout6'] === '1' ? 'true' : 'false';
$cat_title_tag           = isset( $customizer_settings['betterdocs_category_title_tag'] ) ? $customizer_settings['betterdocs_category_title_tag'] : '';
$category_markup         = shortcode_exists('betterdocs_category_grid_list') ? do_shortcode('[betterdocs_category_grid_list show_term_image="'.$image_enabler.'" show_term_description="'.$cat_description.'" term_title_tag="'.$cat_title_tag.'"]') : '';

get_header();

echo '<div class="betterdocs-wraper betterdocs-main-wraper">';
echo $live_search_markup;
echo '<div class="betterdocs-archive-wrap betterdocs-archive-main">';
echo $category_markup;
echo '</div>';
include( BETTERDOCS_PUBLIC_PATH . 'partials/faq.php' );
echo '</div>';

get_footer();
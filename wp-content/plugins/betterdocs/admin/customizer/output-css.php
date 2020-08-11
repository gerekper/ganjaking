<?php
/**
 * BetterDocs Theme Customizer outout for layout settings
 *
 * @package BetterDocs
 */

/**
 * This function adds some styles to the WordPress Customizer
 */
function betterdocs_customizer_styles() { ?>
	<style type="text/css">
		.customize-control-betterdocs-title .betterdocs-select,
		.customize-control-betterdocs-title .betterdocs-dimension{
			display: flex;
		}
		.customize-control-betterdocs-range-value {
			display: flex;
		}
		.customize-control-betterdocs-range-value .customize-control-title,
		.customize-control-betterdocs-number .customize-control-title {
			float: left;
		}
		.betterdocs-customize-control-separator {
			display: block;
			margin: 0 -12px;
			border: 1px solid #ddd;
			border-left: 0;
			border-right: 0;
			padding: 15px;
			font-size: 11px;
			font-weight: 600;
			letter-spacing: 2px;
			line-height: 1;
			text-transform: uppercase;
			color: #555;
			background-color: #fff;
		}
		.customize-control.customize-control-betterdocs-dimension,
		.customize-control-betterdocs-select {
			width: 25%;
			float: left !important;
			clear: none !important;
			margin-top: 0;
			margin-bottom: 12px;
		}
		.customize-control.customize-control-betterdocs-dimension .customize-control-title,
		.customize-control-betterdocs-select .customize-control-title{
			font-size: 11px;
			font-weight: normal;
			color: #888b8c;
			margin-top: 0;
		}
		.betterdocs-customizer-reset {
			font-size: 22px;
    		line-height: 26px;
    		margin-left: 5px;
			transition: unset;
		}
		.betterdocs-customizer-reset svg {
			width: 16px;
			fill: #FE1F4A;
		}
		.customize-control-title .customize-control-title {
			margin-bottom: 0;
		}
	</style>
	<?php

}
add_action( 'customize_controls_print_styles', 'betterdocs_customizer_styles', 999 );

function betterdocs_customize_css() {
	$output = betterdocs_generate_output();
    ?>
	<style type="text/css">
		.betterdocs-wraper.betterdocs-main-wraper {
			<?php if(!empty(get_theme_mod('betterdocs_doc_page_background_color'))) { ?>
			background-color: <?php echo get_theme_mod('betterdocs_doc_page_background_color') ?>;
			<?php } else {?>
			background-color: <?php echo $output['betterdocs_doc_page_background_color'] ?>;		
			<?php } ?>
			<?php if(!empty(get_theme_mod('betterdocs_doc_page_background_image'))) { ?>
			background-image: url(<?php echo get_theme_mod('betterdocs_doc_page_background_image') ?>);
			<?php } ?>
			<?php if(!empty(get_theme_mod('betterdocs_doc_page_background_size'))) { ?>
			background-size: <?php echo get_theme_mod('betterdocs_doc_page_background_size') ?>;
			<?php } ?>
			<?php if(!empty(get_theme_mod('betterdocs_doc_page_background_repeat'))) { ?>
			background-repeat: <?php echo get_theme_mod('betterdocs_doc_page_background_repeat') ?>;
			<?php } ?>
			<?php if(!empty(get_theme_mod('betterdocs_doc_page_background_attachment'))) { ?>
			background-attachment: <?php echo get_theme_mod('betterdocs_doc_page_background_attachment') ?>;
			<?php } ?>
			<?php if(!empty(get_theme_mod('betterdocs_doc_page_background_position'))) { ?>
			background-position: <?php echo get_theme_mod('betterdocs_doc_page_background_position') ?>;
			<?php } ?>
		}
		.betterdocs-archive-wrap.betterdocs-archive-main {
			padding-top: <?php echo $output['betterdocs_doc_page_content_padding_top'] ?>px;
			padding-bottom: <?php echo $output['betterdocs_doc_page_content_padding_bottom'] ?>px;
			padding-left: <?php echo $output['betterdocs_doc_page_content_padding_left'] ?>px;
			padding-right: <?php echo $output['betterdocs_doc_page_content_padding_right'] ?>px;
		}
		.betterdocs-archive-wrap.betterdocs-archive-main {
			width: <?php echo $output['betterdocs_doc_page_content_width'] ?>%;
			max-width: <?php echo $output['betterdocs_doc_page_content_max_width'] ?>px;
		}
		.betterdocs-archive-main .betterdocs-categories-wrap.layout-masonry .docs-single-cat-wrap {
			margin-bottom: <?php echo $output['betterdocs_doc_page_column_space'] ?>px;
		}
		.betterdocs-archive-main .betterdocs-categories-wrap.layout-flex .docs-single-cat-wrap {
			margin: <?php echo $output['betterdocs_doc_page_column_space'] ?>px; 
		}
		.betterdocs-archive-main .betterdocs-categories-wrap .docs-single-cat-wrap .docs-cat-title-wrap { 
			padding-top: <?php echo $output['betterdocs_doc_page_column_padding_top'] ?>px; 
		}
		.betterdocs-archive-main .betterdocs-categories-wrap .docs-single-cat-wrap .docs-cat-title-wrap, 
		.betterdocs-archive-main .docs-item-container { 
			padding-right: <?php echo $output['betterdocs_doc_page_column_padding_right'] ?>px;
			padding-left: <?php echo $output['betterdocs_doc_page_column_padding_left'] ?>px;  
		}
		.betterdocs-archive-main .docs-item-container { 
			padding-bottom: <?php echo $output['betterdocs_doc_page_column_padding_right'] ?>px; 
		}
		.betterdocs-archive-main .betterdocs-categories-wrap.betterdocs-category-box .docs-single-cat-wrap,
		.betterdocs-archive-main .betterdocs-categories-wrap .docs-single-cat-wrap.docs-cat-list-2-box {
			padding-top: <?php echo $output['betterdocs_doc_page_column_padding_top'] ?>px; 
			padding-right: <?php echo $output['betterdocs_doc_page_column_padding_right'] ?>px;
			padding-left: <?php echo $output['betterdocs_doc_page_column_padding_left'] ?>px; 
			padding-bottom: <?php echo $output['betterdocs_doc_page_column_padding_bottom'] ?>px; 
		}
		.betterdocs-categories-wrap.betterdocs-category-box .docs-single-cat-wrap p{
			<?php if(!empty($output['betterdocs_doc_page_cat_desc_color'])) { ?>
			color: <?php echo $output['betterdocs_doc_page_cat_desc_color'] ?>;
			<?php } ?>
		}
		.betterdocs-archive-main.betterdocs-archive-wrap .betterdocs-categories-wrap .docs-single-cat-wrap,
		.betterdocs-archive-main.betterdocs-archive-wrap .betterdocs-categories-wrap .docs-single-cat-wrap .docs-cat-title-wrap {
			<?php if(!empty($output['betterdocs_doc_page_column_borderr_topleft'])) { ?>
			border-top-left-radius: <?php echo $output['betterdocs_doc_page_column_borderr_topleft'] ?>px;
			<?php } ?>
			<?php if(!empty($output['betterdocs_doc_page_column_borderr_topright'])) { ?>
			border-top-right-radius: <?php echo $output['betterdocs_doc_page_column_borderr_topright'] ?>px;
			<?php } ?>
		}
		.betterdocs-archive-wrap .betterdocs-categories-wrap .docs-single-cat-wrap,
		.betterdocs-archive-wrap .betterdocs-categories-wrap .docs-single-cat-wrap .docs-item-container {
			<?php if(!empty($output['betterdocs_doc_page_column_borderr_bottomright'])) { ?>
			border-bottom-right-radius: <?php echo $output['betterdocs_doc_page_column_borderr_bottomright'] ?>px;
			<?php } ?>
			<?php if(!empty($output['betterdocs_doc_page_column_borderr_bottomleft'])) { ?>
			border-bottom-left-radius: <?php echo $output['betterdocs_doc_page_column_borderr_bottomleft'] ?>px;
			<?php } ?>
		}
		.betterdocs-category-list .betterdocs-categories-wrap .docs-single-cat-wrap,
		.betterdocs-category-box.white-bg .docs-single-cat-wrap,
		.betterdocs-categories-wrap.white-bg .docs-single-cat-wrap {
			<?php if(!empty($output['betterdocs_doc_page_column_bg_color'])) { ?>
			background-color: <?php echo $output['betterdocs_doc_page_column_bg_color'] ?>;
			<?php } ?>
		}
		.betterdocs-archive-main .betterdocs-category-box.ash-bg .docs-single-cat-wrap {
			<?php if(!empty($output['betterdocs_doc_page_column_bg_color2'])) { ?>
			background-color: <?php echo $output['betterdocs_doc_page_column_bg_color2'] ?>;
			<?php } ?>
		}
		.betterdocs-archive-main .betterdocs-category-box .docs-single-cat-wrap:hover,
		.betterdocs-archive-main .betterdocs-categories-wrap.white-bg .docs-single-cat-wrap.docs-cat-list-2-box:hover {
			<?php if(!empty($output['betterdocs_doc_page_column_hover_bg_color'])) { ?>
			background-color: <?php echo $output['betterdocs_doc_page_column_hover_bg_color'] ?>;
			<?php } ?>
		}
		.betterdocs-archive-main .betterdocs-category-box .docs-single-cat-wrap img {
			<?php if(!empty($output['betterdocs_doc_page_column_content_space_image'])) { ?>
			margin-bottom: <?php echo $output['betterdocs_doc_page_column_content_space_image'] ?>px;
			<?php } ?>
		}
		.betterdocs-archive-main .betterdocs-category-box .docs-single-cat-wrap .docs-cat-title,
		.betterdocs-archive-main .pro-layout-4 .docs-cat-list-2-box-content .docs-cat-title {
			<?php if(!empty($output['betterdocs_doc_page_column_content_space_title'])) { ?>
			margin-bottom: <?php echo $output['betterdocs_doc_page_column_content_space_title'] ?>px;
			<?php } ?>
		}
		.betterdocs-archive-main .betterdocs-category-box .docs-single-cat-wrap p {
			<?php if(!empty($output['betterdocs_doc_page_column_content_space_desc'])) { ?>
			margin-bottom: <?php echo $output['betterdocs_doc_page_column_content_space_desc'] ?>px;
			<?php } ?>
		}
		.betterdocs-archive-main .betterdocs-category-box .docs-single-cat-wrap span {
			<?php if(!empty($output['betterdocs_doc_page_column_content_space_counter'])) { ?>
			margin-bottom: <?php echo $output['betterdocs_doc_page_column_content_space_counter'] ?>px;
			<?php } ?>
		}
		.docs-cat-title > img { 
			height: <?php echo $output['betterdocs_doc_page_cat_icon_size_layout1'] ?>px; 
		}
		.betterdocs-archive-main .betterdocs-category-box .docs-single-cat-wrap img { 
			height: <?php echo $output['betterdocs_doc_page_cat_icon_size_layout2'] ?>px; 
		}
		.betterdocs-archive-main .docs-cat-title-inner h3,
		.betterdocs-archive-main .betterdocs-category-box .docs-single-cat-wrap .docs-cat-title,
		.betterdocs-archive-main .docs-cat-list-2-box .docs-cat-title {
			font-size: <?php echo $output['betterdocs_doc_page_cat_title_font_size'] ?>px;
		}
		.docs-cat-title-inner h3 {
			color: <?php echo $output['betterdocs_doc_page_cat_title_color'] ?>; 
		}
		.betterdocs-archive-main .betterdocs-category-box .docs-single-cat-wrap .docs-cat-title,
		.betterdocs-archive-main .docs-cat-list-2 .docs-cat-title {
			color: <?php echo $output['betterdocs_doc_page_cat_title_color2'] ?>;
		}
		<?php if ( $output['betterdocs_doc_page_cat_title_hover_color'] ) { ?>
		.betterdocs-archive-main .docs-cat-title-inner h3:hover,
		.betterdocs-archive-main .betterdocs-category-box .docs-single-cat-wrap .docs-cat-title:hover,
		.betterdocs-archive-main .docs-cat-list-2 .docs-cat-title:hover {
			color: <?php echo $output['betterdocs_doc_page_cat_title_hover_color'] ?>;
		}
		<?php } ?>
		.docs-cat-title-inner {
			border-color: <?php echo $output['betterdocs_doc_page_cat_title_border_color'] ?>; 
		}
		.docs-cat-title-inner span {
			color: <?php echo $output['betterdocs_doc_page_item_count_color'] ?>; 
			font-size: <?php echo $output['betterdocs_doc_page_item_count_font_size'] ?>px;
		}
		.betterdocs-archive-main .betterdocs-categories-wrap.betterdocs-category-box .docs-single-cat-wrap span,
		.betterdocs-archive-main .docs-cat-list-2-box .title-count span {
			color: <?php echo $output['betterdocs_doc_page_item_count_color_layout2'] ?>; 
			font-size: <?php echo $output['betterdocs_doc_page_item_count_font_size'] ?>px;
		}
		.betterdocs-categories-wrap .docs-item-count {
			background-color: <?php echo $output['betterdocs_doc_page_item_count_bg_color'] ?>; 
		}

		.betterdocs-archive-wrap .betterdocs-categories-wrap .docs-cat-title-inner span {
			background-color: <?php echo $output['betterdocs_doc_page_item_count_inner_bg_color'] ?>;
			width: <?php echo $output['betterdocs_doc_page_item_counter_size'] ?>px; 
			height: <?php echo $output['betterdocs_doc_page_item_counter_size'] ?>px;
		}
		.betterdocs-archive-wrap .betterdocs-categories-wrap .docs-item-container {
			background-color: <?php echo $output['betterdocs_doc_page_article_list_bg_color'] ?>;
		}
		.betterdocs-archive-wrap .betterdocs-categories-wrap .docs-item-container li,
		.betterdocs-archive-wrap .betterdocs-categories-wrap .docs-item-container .docs-sub-cat-title {
			margin-top: <?php echo $output['betterdocs_doc_page_article_list_margin_top'] ?>px;
			margin-right: <?php echo $output['betterdocs_doc_page_article_list_margin_right'] ?>px;
			margin-bottom: <?php echo $output['betterdocs_doc_page_article_list_margin_bottom'] ?>px;
			margin-left: <?php echo $output['betterdocs_doc_page_article_list_margin_left'] ?>px;
		}
		.betterdocs-archive-wrap .betterdocs-categories-wrap .docs-item-container li svg {
			fill: <?php echo $output['betterdocs_doc_page_list_icon_color'] ?>;
			font-size: <?php echo $output['betterdocs_doc_page_list_icon_font_size'] ?>px;
		}
		.betterdocs-archive-wrap .betterdocs-categories-wrap li a {
			color: <?php echo $output['betterdocs_doc_page_article_list_color'] ?>;
			font-size: <?php echo $output['betterdocs_doc_page_article_list_font_size'] ?>px;
		}
		.betterdocs-archive-wrap .betterdocs-categories-wrap li a:hover {
			color: <?php echo $output['betterdocs_doc_page_article_list_hover_color'] ?>;
		}
		.betterdocs-archive-wrap .betterdocs-categories-wrap .docs-item-container .docs-sub-cat-title svg {
			fill: <?php echo $output['betterdocs_doc_page_subcategory_icon_color'] ?>;
			font-size: <?php echo $output['betterdocs_doc_page_subcategory_icon_font_size'] ?>px;
		}
		.betterdocs-archive-wrap .betterdocs-categories-wrap .docs-sub-cat-title a {
			color: <?php echo $output['betterdocs_doc_page_article_subcategory_color'] ?>;
			font-size: <?php echo $output['betterdocs_doc_page_article_subcategory_font_size'] ?>px;
		}
		.betterdocs-archive-wrap .betterdocs-categories-wrap .docs-sub-cat-title a:hover {
			color: <?php echo $output['betterdocs_doc_page_article_subcategory_hover_color'] ?>;
		}
		.docs-cat-link-btn, .docs-cat-link-btn:visited {
			background-color: <?php echo $output['betterdocs_doc_page_explore_btn_bg_color'] ?>;
			font-size: <?php echo $output['betterdocs_doc_page_explore_btn_font_size'] ?>px;
			color: <?php echo $output['betterdocs_doc_page_explore_btn_color'] ?>;
			border-color: <?php echo $output['betterdocs_doc_page_explore_btn_border_color'] ?>;
			border-top-left-radius: <?php echo $output['betterdocs_doc_page_explore_btn_borderr_topleft'] ?>px;
			border-top-right-radius: <?php echo $output['betterdocs_doc_page_explore_btn_borderr_topright'] ?>px;
			border-bottom-right-radius: <?php echo $output['betterdocs_doc_page_explore_btn_borderr_bottomright'] ?>px;
			border-bottom-left-radius: <?php echo $output['betterdocs_doc_page_explore_btn_borderr_bottomleft'] ?>px;
			padding-top: <?php echo $output['betterdocs_doc_page_explore_btn_padding_top'] ?>px;
			padding-right: <?php echo $output['betterdocs_doc_page_explore_btn_padding_right'] ?>px;
			padding-bottom: <?php echo $output['betterdocs_doc_page_explore_btn_padding_bottom'] ?>px;
			padding-left: <?php echo $output['betterdocs_doc_page_explore_btn_padding_left'] ?>px;
		}
		.docs-cat-link-btn:hover {
			background-color: <?php echo $output['betterdocs_doc_page_explore_btn_hover_bg_color'] ?>;
			color: <?php echo $output['betterdocs_doc_page_explore_btn_hover_color'] ?>;
			border-color: <?php echo $output['betterdocs_doc_page_explore_btn_hover_border_color'] ?>;
		}
		.betterdocs-single-bg .betterdocs-content-area, .betterdocs-single-bg .betterdocs-content-full {
			background-color: <?php echo $output['betterdocs_doc_single_content_area_bg_color'] ?>;	
		}
		.betterdocs-single-wraper .betterdocs-content-area {
			padding-top: <?php echo $output['betterdocs_doc_single_content_area_padding_top'] ?>px;
			padding-right: <?php echo $output['betterdocs_doc_single_content_area_padding_right'] ?>px;
			padding-bottom: <?php echo $output['betterdocs_doc_single_content_area_padding_bottom'] ?>px;
			padding-left: <?php echo $output['betterdocs_doc_single_content_area_padding_left'] ?>px;
		}
		.betterdocs-single-wraper .betterdocs-content-area .docs-single-main {
			padding-top: <?php echo $output['betterdocs_doc_single_post_content_padding_top'] ?>px;
			padding-right: <?php echo $output['betterdocs_doc_single_post_content_padding_right'] ?>px;
			padding-bottom: <?php echo $output['betterdocs_doc_single_post_content_padding_bottom'] ?>px;
			padding-left: <?php echo $output['betterdocs_doc_single_post_content_padding_left'] ?>px;
		}
		.betterdocs-single-layout2 .docs-content-full-main .doc-single-content-wrapper {
			padding-top: <?php echo $output['betterdocs_doc_single_2_post_content_padding_top'] ?>px;
			padding-right: <?php echo $output['betterdocs_doc_single_2_post_content_padding_right'] ?>px;
			padding-bottom: <?php echo $output['betterdocs_doc_single_2_post_content_padding_bottom'] ?>px;
			padding-left: <?php echo $output['betterdocs_doc_single_2_post_content_padding_left'] ?>px;
		}
		.betterdocs-single-layout3 .docs-content-full-main .doc-single-content-wrapper {
			padding-top: <?php echo $output['betterdocs_doc_single_3_post_content_padding_top'] ?>px;
			padding-right: <?php echo $output['betterdocs_doc_single_3_post_content_padding_right'] ?>px;
			padding-bottom: <?php echo $output['betterdocs_doc_single_3_post_content_padding_bottom'] ?>px;
			padding-left: <?php echo $output['betterdocs_doc_single_3_post_content_padding_left'] ?>px;
		}
		.docs-single-title .betterdocs-entry-title {
			font-size: <?php echo $output['betterdocs_single_doc_title_font_size'] ?>px;
			color: <?php echo $output['betterdocs_single_doc_title_color'] ?>;
		}
		.betterdocs-breadcrumb .betterdocs-breadcrumb-item a {
			font-size: <?php echo $output['betterdocs_single_doc_breadcrumbs_font_size'] ?>px;
			color: <?php echo $output['betterdocs_single_doc_breadcrumb_color'] ?>;
		}
		.betterdocs-breadcrumb .betterdocs-breadcrumb-list .betterdocs-breadcrumb-item a:hover {
			color: <?php echo $output['betterdocs_single_doc_breadcrumb_hover_color'] ?>;
		}
		.betterdocs-breadcrumb .breadcrumb-delimiter {
			color: <?php echo $output['betterdocs_single_doc_breadcrumb_speretor_color'] ?>;
		}
		.betterdocs-breadcrumb-item.current span {
			font-size: <?php echo $output['betterdocs_single_doc_breadcrumbs_font_size'] ?>px;
			color: <?php echo $output['betterdocs_single_doc_breadcrumb_active_item_color'] ?>;
		}
		.betterdocs-toc {
			background-color: <?php echo $output['betterdocs_toc_bg_color'] ?>;
			padding-top: <?php echo $output['betterdocs_doc_single_toc_padding_top'] ?>px;
			padding-right: <?php echo $output['betterdocs_doc_single_toc_padding_right'] ?>px;
			padding-bottom: <?php echo $output['betterdocs_doc_single_toc_padding_bottom'] ?>px;
			padding-left: <?php echo $output['betterdocs_doc_single_toc_padding_left'] ?>px;
		}
		.betterdocs-entry-content .betterdocs-toc {
			margin-bottom: <?php echo $output['betterdocs_toc_margin_bottom'] ?>px;
		}
		.sticky-toc-container {
			width: <?php echo $output['betterdocs_sticky_toc_width'] ?>px;
		}
		.sticky-toc-container.toc-sticky {
			z-index: <?php echo $output['betterdocs_sticky_toc_zindex'] ?>;
			margin-top: <?php echo $output['betterdocs_sticky_toc_margin_top'] ?>px;
		}
		.betterdocs-toc > .toc-title {
			color: <?php echo $output['betterdocs_toc_title_color'] ?>;
			font-size: <?php echo $output['betterdocs_toc_title_font_size'] ?>px;
		}
		.betterdocs-entry-content .betterdocs-toc.collapsible-sm .angle-icon {
			color: <?php echo $output['betterdocs_toc_title_color'] ?>;
		}
		.betterdocs-toc > .toc-list a {
			color: <?php echo $output['betterdocs_toc_list_item_color'] ?>;
			font-size: <?php echo $output['betterdocs_toc_list_item_font_size'] ?>px;
			margin-top: <?php echo $output['betterdocs_doc_single_toc_list_margin_top'] ?>px;
			margin-right: <?php echo $output['betterdocs_doc_single_toc_list_margin_right'] ?>px;
			margin-bottom: <?php echo $output['betterdocs_doc_single_toc_list_margin_bottom'] ?>px;
			margin-left: <?php echo $output['betterdocs_doc_single_toc_list_margin_left'] ?>px;
		}
		.betterdocs-toc > .toc-list li:before {
			padding-top: <?php echo $output['betterdocs_doc_single_toc_list_margin_top'] ?>px;
		}
		.betterdocs-toc > .toc-list a:hover {
			color: <?php echo $output['betterdocs_toc_list_item_hover_color'] ?>;
		}
		.feedback-form-link .feedback-form-icon svg, .feedback-form-link .feedback-form-icon img {
			width: <?php echo $output['betterdocs_single_doc_feedback_icon_font_size'] ?>px;
		}
		.betterdocs-toc > .toc-list a.active {
			color: <?php echo $output['betterdocs_toc_active_item_color'] ?>;
		}
		.betterdocs-toc > .toc-list li {
			color: <?php echo $output['betterdocs_toc_list_number_color'] ?>;
			font-size: <?php echo $output['betterdocs_toc_list_number_font_size'] ?>px;
		}
		.betterdocs-content {
			color: <?php echo $output['betterdocs_single_content_font_color'] ?>;
			font-size: <?php echo $output['betterdocs_single_content_font_size'] ?>px;
		}
		.betterdocs-social-share .betterdocs-social-share-heading h5 {
			color: <?php echo $output['betterdocs_post_social_share_text_color'] ?>;
		}
		.betterdocs-entry-footer .feedback-form-link {
			color: <?php echo $output['betterdocs_single_doc_feedback_link_color'] ?>;
			font-size: <?php echo $output['betterdocs_single_doc_feedback_link_font_size'] ?>px;
		}
		.betterdocs-entry-footer .feedback-update-form .feedback-form-link:hover {
			color: <?php echo $output['betterdocs_single_doc_feedback_link_hover_color'] ?>;
		}
		.docs-navigation a {
			color: <?php echo $output['betterdocs_single_doc_navigation_color'] ?>;
			font-size: <?php echo $output['betterdocs_single_doc_navigation_font_size'] ?>px;
		}
		.docs-navigation a:hover {
			color: <?php echo $output['betterdocs_single_doc_navigation_hover_color'] ?>;
		}
		.docs-navigation a svg{
			fill: <?php echo $output['betterdocs_single_doc_navigation_arrow_color'] ?>;
			width: <?php echo $output['betterdocs_single_doc_navigation_arrow_font_size'] ?>px;
		}
		.betterdocs-entry-footer .update-date{
			color: <?php echo $output['betterdocs_single_doc_lu_time_color'] ?>;
			font-size: <?php echo $output['betterdocs_single_doc_lu_time_font_size'] ?>px;
		}
		.betterdocs-credit p{
			color: <?php echo $output['betterdocs_single_doc_powered_by_color'] ?>;
			font-size: <?php echo $output['betterdocs_single_doc_powered_by_font_size'] ?>px;
		}
		.betterdocs-credit p a{
			color: <?php echo $output['betterdocs_single_doc_powered_by_link_color'] ?>;
		}
		.betterdocs-sidebar-content .betterdocs-categories-wrap,
		.betterdocs-full-sidebar-left {
			background-color: <?php echo $output['betterdocs_sidebar_bg_color'] ?>;
		}
		.betterdocs-single-layout1 .betterdocs-sidebar-content .betterdocs-categories-wrap {
			<?php if(!empty($output['betterdocs_sidebar_borderr_topleft'])) { ?>
			border-top-left-radius: <?php echo $output['betterdocs_sidebar_borderr_topleft'] ?>px;
			<?php } ?>
			<?php if(!empty($output['betterdocs_sidebar_borderr_topright'])) { ?>
			border-top-right-radius: <?php echo $output['betterdocs_sidebar_borderr_topright'] ?>px;
			<?php } ?>
			<?php if(!empty($output['betterdocs_sidebar_borderr_bottomright'])) { ?>
			border-bottom-right-radius: <?php echo $output['betterdocs_sidebar_borderr_bottomright'] ?>px;
			<?php } ?>
			<?php if(!empty($output['betterdocs_sidebar_borderr_bottomleft'])) { ?>
			border-bottom-left-radius: <?php echo $output['betterdocs_sidebar_borderr_bottomleft'] ?>px;
			<?php } ?>
		}
		.betterdocs-sidebar-content .docs-single-cat-wrap .docs-cat-title-wrap {
			background-color: <?php echo $output['betterdocs_sidebar_title_bg_color'] ?>;
		}
		.betterdocs-sidebar-content .docs-cat-title > img{
			height: <?php echo $output['betterdocs_sidebar_icon_size'] ?>px;
		}
		.betterdocs-sidebar-content .docs-cat-title-inner h3{
			color: <?php echo $output['betterdocs_sidebar_title_color'] ?>;
			font-size: <?php echo $output['betterdocs_sidebar_title_font_size'] ?>px;
		}
		.betterdocs-sidebar-content .docs-cat-title-inner h3:hover {
			color: <?php echo $output['betterdocs_sidebar_title_hover_color'] ?> !important;
		}
		.betterdocs-sidebar-content .docs-cat-title-inner .cat-list-arrow-down {
			color: <?php echo $output['betterdocs_sidebar_title_color'] ?>;
		}
		.betterdocs-sidebar-content .docs-single-cat-wrap .active-title .docs-cat-title-inner h3,
		.betterdocs-sidebar-content .active-title .docs-cat-title-inner h3,
		.betterdocs-full-sidebar-left .docs-cat-title-wrap::after {
			color: <?php echo $output['betterdocs_sidebar_active_title_color'] ?>;
		}
		.betterdocs-sidebar-content .docs-item-count {
			background-color: <?php echo $output['betterdocs_sidbebar_item_count_bg_color'] ?>;
		}
		.betterdocs-sidebar-content .docs-item-count span {
			background-color: <?php echo $output['betterdocs_sidbebar_item_count_inner_bg_color'] ?>;
			color: <?php echo $output['betterdocs_sidebar_item_count_color'] ?>;
			font-size: <?php echo $output['betterdocs_sidebat_item_count_font_size'] ?>px;
		}
		.betterdocs-sidebar-content .betterdocs-categories-wrap .docs-single-cat-wrap {
			margin-top: <?php echo $output['betterdocs_sidebar_title_margin_top'] ?>px;
			margin-right: <?php echo $output['betterdocs_sidebar_title_margin_right'] ?>px;
			margin-bottom: <?php echo $output['betterdocs_sidebar_title_margin_bottom'] ?>px;
			margin-left: <?php echo $output['betterdocs_sidebar_title_margin_left'] ?>px;
		}
		.betterdocs-sidebar-content .betterdocs-categories-wrap, .betterdocs-full-sidebar-left .betterdocs-categories-wrap {
			padding-top: <?php echo $output['betterdocs_sidebar_padding_top'] ?>px;
			padding-right: <?php echo $output['betterdocs_sidebar_padding_right'] ?>px;
			padding-bottom: <?php echo $output['betterdocs_sidebar_padding_bottom'] ?>px;
			padding-left: <?php echo $output['betterdocs_sidebar_padding_left'] ?>px;
		}
		.betterdocs-sidebar-content .betterdocs-categories-wrap .docs-single-cat-wrap .docs-cat-title-wrap {
			padding-top: <?php echo $output['betterdocs_sidebar_title_padding_top'] ?>px;
			padding-right: <?php echo $output['betterdocs_sidebar_title_padding_right'] ?>px;
			padding-bottom: <?php echo $output['betterdocs_sidebar_title_padding_bottom'] ?>px;
			padding-left: <?php echo $output['betterdocs_sidebar_title_padding_left'] ?>px;
		}
		.betterdocs-single-layout2 .betterdocs-full-sidebar-left .betterdocs-sidebar-content .betterdocs-categories-wrap .docs-cat-title-inner {
			<?php if(!empty(get_theme_mod('betterdocs_sidebar_title_bg_color'))) { ?>
			background-color: <?php echo get_theme_mod('betterdocs_sidebar_title_bg_color') ?>;
			<?php } else {?>
			background-color: <?php echo $output['betterdocs_sidebar_title_bg_color'] ?>;		
			<?php } ?>
			padding-top: <?php echo $output['betterdocs_sidebar_title_padding_top'] ?>px;
			padding-right: <?php echo $output['betterdocs_sidebar_title_padding_right'] ?>px;
			padding-bottom: <?php echo $output['betterdocs_sidebar_title_padding_bottom'] ?>px;
			padding-left: <?php echo $output['betterdocs_sidebar_title_padding_left'] ?>px;
		}
		.betterdocs-sidebar-content .docs-item-container{
			background-color: <?php echo $output['betterdocs_sidbebar_item_list_bg_color'] ?>;
		}
		.betterdocs-sidebar-content .docs-single-cat-wrap .docs-cat-title-wrap.active-title{
			background-color: <?php echo $output['betterdocs_sidebar_active_cat_background_color'] ?>;
			border-color: <?php echo $output['betterdocs_sidebar_active_cat_border_color'] ?>;
		}
		.betterdocs-sidebar-content .betterdocs-categories-wrap .docs-item-container li {
			padding-left: 0;
			margin-top: <?php echo $output['betterdocs_sidebar_list_item_margin_top'] ?>px;
			margin-right: <?php echo $output['betterdocs_sidebar_list_item_margin_right'] ?>px;
			margin-bottom: <?php echo $output['betterdocs_sidebar_list_item_margin_bottom'] ?>px;
			margin-left: <?php echo $output['betterdocs_sidebar_list_item_margin_left'] ?>px;
		}
		.betterdocs-single-layout2 .betterdocs-sidebar-content .betterdocs-categories-wrap .docs-item-container li {
			margin-right: 0 !important;
		}
		.betterdocs-sidebar-content .betterdocs-categories-wrap li a {
			color: <?php echo $output['betterdocs_sidebar_list_item_color'] ?>;
			font-size: <?php echo $output['betterdocs_sidebar_list_item_font_size'] ?>px;
		}
		.betterdocs-sidebar-content .betterdocs-categories-wrap li a:hover {
			color: <?php echo $output['betterdocs_sidebar_list_item_hover_color'] ?>;
		}
		.betterdocs-sidebar-content .betterdocs-categories-wrap li svg {
			fill: <?php echo $output['betterdocs_sidebar_list_icon_color'] ?>;
			font-size: <?php echo $output['betterdocs_sidebar_list_icon_font_size'] ?>px;
		}
		.betterdocs-sidebar-content .betterdocs-categories-wrap li a.active,
		.betterdocs-sidebar-content .betterdocs-categories-wrap li.sub-list a.active {
			color: <?php echo $output['betterdocs_sidebar_active_list_item_color'] ?>;
		}	
		.betterdocs-category-wraper.betterdocs-single-wraper{
			<?php if(!empty($output['betterdocs_archive_page_background_color'])) { ?>
			background-color: <?php echo $output['betterdocs_archive_page_background_color'] ?>;
			<?php } ?>
			<?php if(!empty($output['betterdocs_archive_page_background_image'])) { ?>
			background-image: url(<?php echo $output['betterdocs_archive_page_background_image'] ?>);
			<?php } ?>
			<?php if(!empty($output['betterdocs_archive_page_background_size'])) { ?>
			background-size: <?php echo $output['betterdocs_archive_page_background_size'] ?>;
			<?php } ?>
			<?php if(!empty($output['betterdocs_archive_page_background_repeat'])) { ?>
			background-repeat: <?php echo $output['betterdocs_archive_page_background_repeat'] ?>;
			<?php } ?>
			<?php if(!empty($output['betterdocs_archive_page_background_attachment'])) { ?>
			background-attachment: <?php echo $output['betterdocs_archive_page_background_attachment'] ?>;
			<?php } ?>
			<?php if(!empty($output['betterdocs_archive_page_background_position'])) { ?>
			background-position: <?php echo $output['betterdocs_archive_page_background_position'] ?>;
			<?php } ?>
		}	
		.betterdocs-category-wraper.betterdocs-single-wraper .docs-listing-main .docs-category-listing{
			<?php if(!empty($output['betterdocs_archive_content_background_color'])) { ?>
			background-color: <?php echo $output['betterdocs_archive_content_background_color'] ?>;
			<?php } ?>
			margin-top: <?php echo $output['betterdocs_archive_content_margin_top'] ?>px;
			margin-right: <?php echo $output['betterdocs_archive_content_margin_right'] ?>px;
			margin-bottom: <?php echo $output['betterdocs_archive_content_margin_bottom'] ?>px;
			margin-left: <?php echo $output['betterdocs_archive_content_margin_left'] ?>px;
			padding-top: <?php echo $output['betterdocs_archive_content_padding_top'] ?>px;
			padding-right: <?php echo $output['betterdocs_archive_content_padding_right'] ?>px;
			padding-bottom: <?php echo $output['betterdocs_archive_content_padding_bottom'] ?>px;
			padding-left: <?php echo $output['betterdocs_archive_content_padding_left'] ?>px;
			border-radius: <?php echo $output['betterdocs_archive_content_border_radius'] ?>px;
		}
		.betterdocs-category-wraper .docs-category-listing .docs-cat-title h3 {
			color: <?php echo $output['betterdocs_archive_title_color'] ?>;
			font-size: <?php echo $output['betterdocs_archive_title_font_size'] ?>px;
			margin-top: <?php echo $output['betterdocs_archive_title_margin_top'] ?>px;
			margin-right: <?php echo $output['betterdocs_archive_title_margin_right'] ?>px;
			margin-bottom: <?php echo $output['betterdocs_archive_title_margin_bottom'] ?>px;
			margin-left: <?php echo $output['betterdocs_archive_title_margin_left'] ?>px;
		}
		.betterdocs-category-wraper .docs-category-listing .docs-cat-title p {
			color: <?php echo $output['betterdocs_archive_description_color'] ?>;
			font-size: <?php echo $output['betterdocs_archive_description_font_size'] ?>px;
			margin-top: <?php echo $output['betterdocs_archive_description_margin_top'] ?>px;
			margin-right: <?php echo $output['betterdocs_archive_description_margin_right'] ?>px;
			margin-bottom: <?php echo $output['betterdocs_archive_description_margin_bottom'] ?>px;
			margin-left: <?php echo $output['betterdocs_archive_description_margin_left'] ?>px;
		}
		.docs-category-listing .docs-list ul li, 
		.docs-category-listing .docs-list .docs-sub-cat-title {
			margin-top: <?php echo $output['betterdocs_archive_article_list_margin_top'] ?>px;
			margin-right: <?php echo $output['betterdocs_archive_article_list_margin_right'] ?>px;
			margin-bottom: <?php echo $output['betterdocs_archive_article_list_margin_bottom'] ?>px;
			margin-left: <?php echo $output['betterdocs_archive_article_list_margin_left'] ?>px;
		}
		.docs-category-listing .docs-list ul li svg {
			fill: <?php echo $output['betterdocs_archive_list_icon_color'] ?>;
			font-size: <?php echo $output['betterdocs_archive_list_icon_font_size'] ?>px;
		}
		.docs-category-listing .docs-list ul li a {
			color: <?php echo $output['betterdocs_archive_list_item_color'] ?>;
			font-size: <?php echo $output['betterdocs_archive_list_item_font_size'] ?>px;
		}
		.docs-category-listing .docs-list ul li a:hover {
			color: <?php echo $output['betterdocs_archive_list_item_hover_color'] ?>;
		}
		.betterdocs-search-form-wrap{
			<?php if(!empty($output['betterdocs_live_search_background_color'])) { ?>
			background-color: <?php echo $output['betterdocs_live_search_background_color'] ?>;
			<?php } ?>
			<?php if(!empty($output['betterdocs_live_search_background_image'])) { ?>
			background-image: url(<?php echo $output['betterdocs_live_search_background_image'] ?>);
			<?php } ?>
			<?php if(!empty($output['betterdocs_live_search_background_size'])) { ?>
			background-size: <?php echo $output['betterdocs_live_search_background_size'] ?>;
			<?php } ?>
			<?php if(!empty($output['betterdocs_live_search_background_repeat'])) { ?>
			background-repeat: <?php echo $output['betterdocs_live_search_background_repeat'] ?>;
			<?php } ?>
			<?php if(!empty($output['betterdocs_live_search_background_attachment'])) { ?>
			background-attachment: <?php echo $output['betterdocs_live_search_background_attachment'] ?>;
			<?php } ?>
			<?php if(!empty($output['betterdocs_live_search_background_position'])) { ?>
			background-position: <?php echo $output['betterdocs_live_search_background_position'] ?>;
			<?php } ?>
			padding-top: <?php echo $output['betterdocs_live_search_padding_top'] ?>px;
			padding-right: <?php echo $output['betterdocs_live_search_padding_right'] ?>px;
			padding-bottom: <?php echo $output['betterdocs_live_search_padding_bottom'] ?>px;
			padding-left: <?php echo $output['betterdocs_live_search_padding_left'] ?>px;
		}
		.betterdocs-search-heading h2 {
			line-height: 1.2;
			font-size: <?php echo $output['betterdocs_live_search_heading_font_size'] ?>px;
			color: <?php echo $output['betterdocs_live_search_heading_font_color'] ?>;
			margin-top: <?php echo $output['betterdocs_search_heading_margin_top'] ?>px;
			margin-right: <?php echo $output['betterdocs_search_heading_margin_right'] ?>px;
			margin-bottom: <?php echo $output['betterdocs_search_heading_margin_bottom'] ?>px;
			margin-left: <?php echo $output['betterdocs_search_heading_margin_left'] ?>px;
		}
		.betterdocs-search-heading h3 {
			line-height: 1.2;
			font-size: <?php echo $output['betterdocs_live_search_subheading_font_size'] ?>px;
			color: <?php echo $output['betterdocs_live_search_subheading_font_color'] ?>;
			margin-top: <?php echo $output['betterdocs_search_subheading_margin_top'] ?>px;
			margin-right: <?php echo $output['betterdocs_search_subheading_margin_right'] ?>px;
			margin-bottom: <?php echo $output['betterdocs_search_subheading_margin_bottom'] ?>px;
			margin-left: <?php echo $output['betterdocs_search_subheading_margin_left'] ?>px;
		}
		.betterdocs-searchform {
			background-color: <?php echo $output['betterdocs_search_field_background_color'] ?>;
			border-radius: <?php echo $output['betterdocs_search_field_border_radius'] ?>px;
			padding-top: <?php echo $output['betterdocs_search_field_padding_top'] ?>px;
			padding-right: <?php echo $output['betterdocs_search_field_padding_right'] ?>px;
			padding-bottom: <?php echo $output['betterdocs_search_field_padding_bottom'] ?>px;
			padding-left: <?php echo $output['betterdocs_search_field_padding_left'] ?>px;
		}
		.betterdocs-searchform .betterdocs-search-field{
			font-size: <?php echo $output['betterdocs_search_field_font_size'] ?>px;
			color: <?php echo $output['betterdocs_search_field_color'] ?>;
		}
		.betterdocs-searchform svg.docs-search-icon {
			fill: <?php echo $output['betterdocs_search_icon_color'] ?>;
			height: <?php echo $output['betterdocs_search_icon_size'] ?>px;
		}
		.docs-search-close path.close-line {
			fill: <?php echo $output['betterdocs_search_close_icon_color'] ?>;	
		}
		.docs-search-close path.close-border {
			fill: <?php echo $output['betterdocs_search_close_icon_border_color'] ?>;	
		}
		.docs-search-loader {
			stroke: <?php echo $output['betterdocs_search_close_icon_border_color'] ?>;	
		}
		.betterdocs-searchform svg.docs-search-icon:hover {
			fill: <?php echo $output['betterdocs_search_icon_hover_color'] ?>;
		}
		.betterdocs-live-search .docs-search-result {
			width: <?php echo $output['betterdocs_search_result_width'] ?>%;
			max-width: <?php echo $output['betterdocs_search_result_max_width'] ?>px;
			background-color: <?php echo $output['betterdocs_search_result_background_color'] ?>;
			border-color: <?php echo $output['betterdocs_search_result_border_color'] ?>;
		}
		.betterdocs-search-result-wrap::before {
			border-color: transparent transparent <?php echo $output['betterdocs_search_result_background_color'] ?>;
		}
		.betterdocs-live-search .docs-search-result li {
			border-color: <?php echo $output['betterdocs_search_result_item_border_color'] ?>;
		}
		.betterdocs-live-search .docs-search-result li a {
			font-size: <?php echo $output['betterdocs_search_result_item_font_size'] ?>px;
			color: <?php echo $output['betterdocs_search_result_item_font_color'] ?>;
			padding-top: <?php echo $output['betterdocs_search_result_item_padding_top'] ?>px;
			padding-right: <?php echo $output['betterdocs_search_result_item_padding_right'] ?>px;
			padding-bottom: <?php echo $output['betterdocs_search_result_item_padding_bottom'] ?>px;
			padding-left: <?php echo $output['betterdocs_search_result_item_padding_left'] ?>px;
		}
		.betterdocs-live-search .docs-search-result li:only-child {
			font-size: <?php echo $output['betterdocs_search_result_item_font_size'] ?>px;
			color: <?php echo $output['betterdocs_search_result_item_font_color'] ?>;
		}
		.betterdocs-live-search .docs-search-result li:hover {
			background-color: <?php echo $output['betterdocs_search_result_item_hover_background_color'] ?>;
		}
		.betterdocs-live-search .docs-search-result li a:hover {
			color: <?php echo $output['betterdocs_search_result_item_hover_font_color'] ?>;
		}
		.betterdocs-category-box.pro-layout-3 .docs-single-cat-wrap img,
		.docs-cat-list-2-box img {
			margin-right: <?php echo $output['betterdocs_doc_page_column_content_space_image'] ?>px;
		}
		.betterdocs-wraper .betterdocs-search-form-wrap.cat-layout-4 {
			padding-bottom: <?php echo $output['betterdocs_live_search_padding_bottom'] + 80 ?>px;
		}
	</style>
	<script>
		jQuery(document).ready(function() {
			var masonryGrid = jQuery(".betterdocs-categories-wrap.layout-masonry");
			var columnPerGrid = jQuery(".betterdocs-categories-wrap.layout-masonry").attr('data-column');
			var masonryItem = jQuery(".betterdocs-categories-wrap.layout-masonry .docs-single-cat-wrap");
			var doc_page_column_space = <?php echo $output['betterdocs_doc_page_column_space'] ?>;
			var total_margin = columnPerGrid * doc_page_column_space;
			if (masonryGrid.length) {
				masonryItem.css("width", "calc((100% - "+total_margin+"px) / "+parseInt(columnPerGrid)+")");
				masonryGrid.masonry({
					itemSelector: ".docs-single-cat-wrap",
					percentPosition: true,
					gutter: doc_page_column_space
				});
			}
		});
	</script>
    <?php
}
add_action( 'wp_head', 'betterdocs_customize_css');
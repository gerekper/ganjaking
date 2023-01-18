<?php
/**
 * BetterDocs Theme Customizer outout for layout settings
 *
 * @package BetterDocs
 */


function betterdocs_customize_css_pro() {
	$output = betterdocs_generate_output_pro();
	$layout_select = get_theme_mod('betterdocs_docs_layout_select', 'layout-1');
    ?>
	<style>
		<?php if ( BetterDocs_Multiple_Kb::$enable == 1 ) { ?>
			.betterdocs-wraper.betterdocs-mkb-wraper {
				<?php if(!empty(get_theme_mod('betterdocs_mkb_background_color'))) { ?>
				background-color: <?php echo get_theme_mod('betterdocs_mkb_background_color') ?>;
				<?php } else {?>
				background-color: <?php echo $output['betterdocs_mkb_background_color'] ?>;		
				<?php } ?>
				<?php if(!empty(get_theme_mod('betterdocs_mkb_background_image'))) { ?>
				background-image: url(<?php echo get_theme_mod('betterdocs_mkb_background_image') ?>);
				<?php } ?>
				<?php if(!empty(get_theme_mod('betterdocs_mkb_background_size'))) { ?>
				background-size: <?php echo get_theme_mod('betterdocs_mkb_background_size') ?>;
				<?php } ?>
				<?php if(!empty(get_theme_mod('betterdocs_mkb_background_repeat'))) { ?>
				background-repeat: <?php echo get_theme_mod('betterdocs_mkb_background_repeat') ?>;
				<?php } ?>
				<?php if(!empty(get_theme_mod('betterdocs_mkb_background_attachment'))) { ?>
				background-attachment: <?php echo get_theme_mod('betterdocs_mkb_background_attachment') ?>;
				<?php } ?>
				<?php if(!empty(get_theme_mod('betterdocs_mkb_background_position'))) { ?>
				background-position: <?php echo get_theme_mod('betterdocs_mkb_background_position') ?>;
				<?php } ?>
			}
			.betterdocs-archive-wrap.betterdocs-archive-mkb {
				padding-top: <?php echo $output['betterdocs_mkb_content_padding_top'] ?>px;
				padding-bottom: <?php echo $output['betterdocs_mkb_content_padding_bottom'] ?>px;
				padding-left: <?php echo $output['betterdocs_mkb_content_padding_left'] ?>px;
				padding-right: <?php echo $output['betterdocs_mkb_content_padding_right'] ?>px;
			}
			.betterdocs-archive-wrap.betterdocs-archive-mkb {
				width: <?php echo $output['betterdocs_mkb_content_width'] ?>%;
				max-width: <?php echo $output['betterdocs_mkb_content_max_width'] ?>px;
			}
			.betterdocs-categories-wrap.multiple-kb.layout-masonry .docs-single-cat-wrap {
				margin-bottom: <?php echo $output['betterdocs_mkb_column_space'] ?>px;
			}
			.betterdocs-categories-wrap.multiple-kb.layout-flex .docs-single-cat-wrap {
				margin: <?php echo $output['betterdocs_mkb_column_space'] ?>px; 
			}
			.betterdocs-categories-wrap.multiple-kb .docs-single-cat-wrap .docs-cat-title-wrap { 
				padding-top: <?php echo $output['betterdocs_mkb_column_padding_top'] ?>px; 
			}
			.betterdocs-categories-wrap.multiple-kb .docs-single-cat-wrap .docs-cat-title-wrap, 
			.betterdocs-archive-mkb .docs-item-container { 
				padding-right: <?php echo $output['betterdocs_mkb_column_padding_right'] ?>px;
				padding-left: <?php echo $output['betterdocs_mkb_column_padding_left'] ?>px;  
			}
			.betterdocs-archive-mkb .docs-item-container { 
				padding-bottom: <?php echo $output['betterdocs_mkb_column_padding_right'] ?>px; 
			}
			.betterdocs-categories-wrap.multiple-kb.betterdocs-category-box .docs-single-cat-wrap,
			.betterdocs-categories-wrap.multiple-kb .docs-single-cat-wrap.docs-cat-list-2-box {
				padding-top: <?php echo $output['betterdocs_mkb_column_padding_top'] ?>px; 
				padding-right: <?php echo $output['betterdocs_mkb_column_padding_right'] ?>px;
				padding-left: <?php echo $output['betterdocs_mkb_column_padding_left'] ?>px; 
				padding-bottom: <?php echo $output['betterdocs_mkb_column_padding_bottom'] ?>px; 
			}
			.betterdocs-categories-wrap.betterdocs-category-box .docs-single-cat-wrap p{
				<?php if(!empty($output['betterdocs_mkb_cat_desc_color'])) { ?>
				color: <?php echo $output['betterdocs_mkb_cat_desc_color'] ?>;
				<?php } ?>
			}
			.betterdocs-categories-wrap.multiple-kb .docs-single-cat-wrap,
			.betterdocs-categories-wrap.multiple-kb .docs-single-cat-wrap .docs-cat-title-wrap {
				<?php if(!empty($output['betterdocs_mkb_column_borderr_topleft'])) { ?>
				border-top-left-radius: <?php echo $output['betterdocs_mkb_column_borderr_topleft'] ?>px;
				<?php } ?>
				<?php if(!empty($output['betterdocs_mkb_column_borderr_topright'])) { ?>
				border-top-right-radius: <?php echo $output['betterdocs_mkb_column_borderr_topright'] ?>px;
				<?php } ?>
			}
			.betterdocs-categories-wrap.multiple-kb .docs-single-cat-wrap,
			.betterdocs-categories-wrap.multiple-kb .docs-single-cat-wrap .docs-item-container {
				<?php if(!empty($output['betterdocs_mkb_column_borderr_bottomright'])) { ?>
				border-bottom-right-radius: <?php echo $output['betterdocs_mkb_column_borderr_bottomright'] ?>px;
				<?php } ?>
				<?php if(!empty($output['betterdocs_mkb_column_borderr_bottomleft'])) { ?>
				border-bottom-left-radius: <?php echo $output['betterdocs_mkb_column_borderr_bottomleft'] ?>px;
				<?php } ?>
			}
			.betterdocs-category-box.multiple-kb.ash-bg .docs-single-cat-wrap {
				<?php if(!empty($output['betterdocs_mkb_column_bg_color2'])) { ?>
				background-color: <?php echo $output['betterdocs_mkb_column_bg_color2'] ?>;
				<?php } ?>
			}
			.betterdocs-category-box.multiple-kb .docs-single-cat-wrap:hover,
			.betterdocs-categories-wrap.multiple-kb.white-bg .docs-single-cat-wrap.docs-cat-list-2-box:hover {
				<?php if(!empty($output['betterdocs_mkb_column_hover_bg_color'])) { ?>
				background-color: <?php echo $output['betterdocs_mkb_column_hover_bg_color'] ?>;
				<?php } ?>
			}
			.betterdocs-category-box.multiple-kb .docs-single-cat-wrap img {
				<?php if(!empty($output['betterdocs_mkb_column_content_space_image'])) { ?>
				margin-bottom: <?php echo $output['betterdocs_mkb_column_content_space_image'] ?>px;
				<?php } ?>
			}
			.betterdocs-category-box.multiple-kb .docs-single-cat-wrap .docs-cat-title {
				<?php if(!empty($output['betterdocs_mkb_column_content_space_title'])) { ?>
				margin-bottom: <?php echo $output['betterdocs_mkb_column_content_space_title'] ?>px;
				<?php } ?>
			}
			.betterdocs-category-box.multiple-kb .docs-single-cat-wrap p {
				<?php if(!empty($output['betterdocs_mkb_column_content_space_desc'])) { ?>
				margin-bottom: <?php echo $output['betterdocs_mkb_column_content_space_desc'] ?>px;
				<?php } ?>
			}
			.betterdocs-category-box.multiple-kb .docs-single-cat-wrap span {
				<?php if(!empty($output['betterdocs_mkb_column_content_space_counter'])) { ?>
				margin-bottom: <?php echo $output['betterdocs_mkb_column_content_space_counter'] ?>px;
				<?php } ?>
			}
			.betterdocs-category-box.multiple-kb .docs-single-cat-wrap img{
				height: <?php echo $output['betterdocs_mkb_cat_icon_size'] ?>px;
			}
			.betterdocs-list-view.betterdocs-category-box.multiple-kb .docs-single-cat-wrap img { 
				height: <?php echo $output['betterdocs_mkb_cat_icon_size'] ?>px; 
			}
			.betterdocs-categories-wrap.betterdocs-category-box-pro.multiple-kb .docs-single-cat-wrap img{
				height: <?php echo $output['betterdocs_mkb_cat_icon_size'] ?>px; 
			}
			.tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap .docs-cat-title-inner .docs-cat-title .docs-cat-icon{
				height: <?php echo $output['betterdocs_mkb_cat_icon_size']; ?>px;
			}
			.betterdocs-category-box.multiple-kb .docs-single-cat-wrap p { 
				color: <?php echo $output['betterdocs_mkb_desc_color'] ?>;
			}
			.multiple-kb .docs-cat-title-inner .docs-cat-heading,
			.betterdocs-category-box.multiple-kb .docs-single-cat-wrap .docs-cat-title,
			.multiple-kb .docs-cat-list-2-box .docs-cat-title {
				font-size: <?php echo $output['betterdocs_mkb_cat_title_font_size'] ?>px;
			}
			.betterdocs-category-box.multiple-kb .docs-single-cat-wrap .docs-cat-title,
			.multiple-kb .docs-cat-list-2 .docs-cat-title {
				color: <?php echo $output['betterdocs_mkb_cat_title_color'] ?>;
			}
			.betterdocs-categories-wrap.multiple-kb.betterdocs-category-box .docs-single-cat-wrap span,
			.multiple-kb .docs-cat-list-2-box .title-count span {
				color: <?php echo $output['betterdocs_mkb_item_count_color'] ?>; 
				font-size: <?php echo $output['betterdocs_mkb_item_count_font_size'] ?>px;
			}
			<?php if ( $output['betterdocs_mkb_cat_title_hover_color'] ) { ?>
			.multiple-kb .docs-cat-title-inner .docs-cat-heading:hover,
			.betterdocs-category-box.multiple-kb .docs-single-cat-wrap .docs-cat-title:hover,
			.multiple-kb .docs-cat-list-2 .docs-cat-title:hover {
				color: <?php echo $output['betterdocs_mkb_cat_title_hover_color'] ?>;
			}
			<?php } ?>

			.betterdocs-categories-wrap.multiple-kb .betterdocs-tab-list a{
				background-color:<?php echo $output['betterdocs_mkb_list_bg_color'] ?>;
				font-size:<?php echo $output['betterdocs_mkb_list_font_size'] ?>px;
				padding-top :<?php echo $output['betterdocs_mkb_list_column_padding_top'] ?>px;
				padding-right:<?php echo $output['betterdocs_mkb_list_column_padding_right']  ?>px;
				padding-bottom:<?php echo $output['betterdocs_mkb_list_column_padding_bottom'] ?>px;
				padding-left:<?php echo $output['betterdocs_mkb_list_column_padding_left'] ?>px;
				margin-top: <?php echo $output['betterdocs_mkb_tab_list_margin_top'] ?>px;
				margin-right:<?php echo $output['betterdocs_mkb_tab_list_margin_right'] ?>px;
				margin-bottom:<?php echo $output['betterdocs_mkb_tab_list_margin_bottom'] ?>px;
				margin-left:<?php echo $output['betterdocs_mkb_tab_list_margin_left'] ?>px;
				border-top-left-radius:<?php echo $output['betterdocs_mkb_tab_list_border_topleft'] ?>px;
				border-top-right-radius:<?php echo $output['betterdocs_mkb_tab_list_border_topright'] ?>px;
				border-bottom-right-radius:<?php echo $output['betterdocs_mkb_tab_list_border_bottomright'] ?>px;
				border-bottom-left-radius:<?php echo $output['betterdocs_mkb_tab_list_border_bottomleft'] ?>px;
			}

			.betterdocs-categories-wrap.multiple-kb .betterdocs-tab-list a:hover{
				background-color: <?php echo $output['betterdocs_mkb_list_bg_hover_color'] ?>;
			}
			.betterdocs-categories-wrap.multiple-kb .betterdocs-tab-list a{
				color: <?php echo $output['betterdocs_mkb_tab_list_font_color']?>;
			}
			.betterdocs-categories-wrap.multiple-kb .betterdocs-tab-list a.active {
				background-color: <?php echo $output['betterdocs_mkb_tab_list_back_color_active'] ?>;
				color: <?php echo $output['betterdocs_mkb_tab_list_font_color_active'] ?>;
			}
			.betterdocs-categories-wrap.multiple-kb .betterdocs-tab-list a.active:focus {
				background-color: <?php echo $output['betterdocs_mkb_tab_list_back_color_active'] ?>;
				color: <?php echo $output['betterdocs_mkb_tab_list_font_color_active'] ?>;
			}
			.betterdocs-categories-wrap.multiple-kb .tabs-content .betterdocs-tab-content .betterdocs-tab-categories {
				width: <?php echo $output['betterdocs_mkb_tab_list_box_content_width'] ?>%;
				grid-gap: <?php echo $output['betterdocs_mkb_column_space'] ?>px;
				padding-top:<?php echo $output['betterdocs_mkb_column_padding_top'] ?>px;
				padding-right:<?php echo $output['betterdocs_mkb_column_padding_right'] ?>px;
				padding-bottom:<?php echo $output['betterdocs_mkb_column_padding_bottom'] ?>px;
				padding-left:<?php echo $output['betterdocs_mkb_column_padding_left'] ?>px;
				margin-top:<?php echo $output['betterdocs_mkb_tab_list_box_content_margin_top'] ?>px;
				margin-right:<?php echo $output['betterdocs_mkb_tab_list_box_content_margin_right'] ?>px;
				margin-bottom:<?php echo $output['betterdocs_mkb_tab_list_box_content_margin_bottom'] ?>px;
				margin-left:<?php echo $output['betterdocs_mkb_tab_list_box_content_margin_left'] ?>px;
			}
			.betterdocs-categories-wrap.multiple-kb .tabs-content .betterdocs-tab-categories .docs-single-cat-wrap {
				background-color: <?php echo $output['betterdocs_mkb_column_bg_color2'] ?>;
			}

			.betterdocs-categories-wrap.multiple-kb .tabs-content .betterdocs-tab-categories .docs-single-cat-wrap:hover {
				background-color: <?php echo $output['betterdocs_mkb_column_hover_bg_color'] ?>;
			}

			.betterdocs-categories-wrap.multiple-kb .tabs-content .betterdocs-tab-content .docs-single-cat-wrap .docs-cat-title-inner .docs-cat-title .docs-cat-heading {
				font-size: <?php echo $output['betterdocs_mkb_cat_title_font_size'] ?>;
			}

			.betterdocs-categories-wrap.multiple-kb .tabs-content .betterdocs-tab-content .docs-single-cat-wrap .docs-cat-title .docs-cat-heading {
				color: <?php echo $output['betterdocs_mkb_cat_title_color'] ?>;
			}

			.betterdocs-categories-wrap.multiple-kb .tabs-content .betterdocs-tab-content .docs-single-cat-wrap .docs-cat-title .docs-cat-heading:hover {
				color: <?php echo $output['betterdocs_mkb_cat_title_hover_color'] ?>;
			}
			.betterdocs-categories-wrap.multiple-kb .tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap .docs-item-container ul li a{
				color: <?php echo $output['betterdocs_mkb_column_list_color']?>;
				font-size: <?php echo $output['betterdocs_mkb_column_list_font_size']?>px;
			}
			.betterdocs-categories-wrap.multiple-kb .tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap .docs-item-container ul li {
				margin-top: <?php echo $output['betterdocs_mkb_column_list_margin_top'] ?>px;
				margin-right: <?php echo $output['betterdocs_mkb_column_list_margin_right'] ?>px;
				margin-bottom: <?php echo $output['betterdocs_mkb_list_margin_bottom']?>px;
				margin-left: <?php echo $output['betterdocs_mkb_list_margin_left'] ?>px;
			}
			.betterdocs-categories-wrap.multiple-kb .tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap .docs-item-container ul li a:hover{
				color: <?php echo $output['betterdocs_mkb_column_list_hover_color'] ?>;
			}

			.betterdocs-categories-wrap.multiple-kb .tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap .docs-item-container .docs-cat-link-btn {
				background-color: <?php echo $output['betterdocs_mkb_tab_list_explore_btn_bg_color'] ?>;
				color: <?php echo $output['betterdocs_mkb_tab_list_explore_btn_color'] ?>;
				border-color : <?php echo $output['betterdocs_mkb_tab_list_explore_btn_border_color'] ?>;
				font-size: <?php echo $output['betterdocs_mkb_tab_list_explore_btn_font_size'] ?>px;
				padding-top: <?php echo $output['betterdocs_mkb_tab_list_explore_btn_padding_top'] ?>px;
				padding-right: <?php echo $output['betterdocs_mkb_tab_list_explore_btn_padding_right'] ?>px;
				padding-bottom: <?php echo $output['betterdocs_mkb_tab_list_explore_btn_padding_bottom'] ?>px;
				padding-left: <?php echo $output['betterdocs_mkb_tab_list_explore_btn_padding_left'] ?>px;
				border-top-left-radius: <?php echo $output['betterdocs_mkb_tab_list_explore_btn_borderr_topleft'] ?>px;
				border-top-right-radius: <?php echo $output['betterdocs_mkb_tab_list_explore_btn_borderr_topright'] ?>px;
				border-bottom-right-radius: <?php echo $output['betterdocs_mkb_tab_list_explore_btn_borderr_bottomright'] ?>px;
				border-bottom-left-radius: <?php echo $output['betterdocs_mkb_tab_list_explore_btn_borderr_bottomleft'] ?>px;
			}
			.betterdocs-categories-wrap.multiple-kb .tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap .docs-item-container .docs-cat-link-btn:hover {
				background-color: <?php echo $output['betterdocs_mkb_tab_list_explore_btn_hover_bg_color'] ?>;
				color:<?php echo $output['betterdocs_mkb_tab_list_explore_btn_hover_color'] ?>;
				border-color: <?php echo $output['betterdocs_mkb_tab_list_explore_btn_hover_border_color'] ?>;
			}
			
			.betterdocs-popular-list.multiple-kb .popular-title {
				margin-top: <?php echo $output['betterdocs_mkb_popular_title_margin_top'] ?>px;
				margin-right: <?php echo $output['betterdocs_mkb_popular_title_margin_right']?>px;
				margin-bottom: <?php echo $output['betterdocs_mkb_popular_title_margin_bottom'] ?>px;
				margin-left: <?php echo $output['betterdocs_mkb_popular_title_margin_left'] ?>px;
				font-size: <?php echo $output['betterdocs_mkb_popular_title_font_size'] ?>px;
				color: <?php echo $output['betterdocs_mkb_popular_title_color'] ?>;
			} 

			.betterdocs-popular-list.multiple-kb .popular-title:hover {
				color: <?php echo $output['betterdocs_mkb_popular_title_color_hover'] ?>;
			} 

			.betterdocs-categories-wrap.multiple-kb.betterdocs-category-box .docs-single-cat-wrap span:hover {
				color: <?php echo $output['betterdocs_mkb_item_count_color_hover'] ?>;
			}

			.betterdocs-list-popular .betterdocs-archive-popular .betterdocs-categories-wrap.betterdocs-popular-list.multiple-kb {
				background-color: <?php echo $output['betterdocs_mkb_popular_list_bg_color'] ?>;
			}
			.betterdocs-list-popular .betterdocs-archive-popular .betterdocs-categories-wrap.betterdocs-popular-list.multiple-kb ul{
				padding-top: <?php echo $output['betterdocs_mkb_popular_docs_padding_top'] ?>px;
				padding-right: <?php echo $output['betterdocs_mkb_popular_docs_padding_right'] ?>px;
				padding-bottom: <?php echo $output['betterdocs_mkb_popular_docs_padding_bottom'] ?>px;
				padding-left: <?php echo $output['betterdocs_mkb_popular_docs_padding_left'] ?>px;
				margin-top: <?php echo $output['betterdocs_mkb_popular_list_margin_top'] ?>px;
				margin-right: <?php echo $output['betterdocs_mkb_popular_list_margin_right'] ?>px;
				margin-bottom: <?php echo $output['betterdocs_mkb_popular_list_margin_bottom'] ?>px;
				margin-left: <?php echo $output['betterdocs_mkb_popular_list_margin_left'] ?>px;
			}
			.betterdocs-popular-list.multiple-kb ul li a{
				color: <?php echo $output['betterdocs_mkb_popular_list_color'] ?>;
				font-size: <?php echo $output['betterdocs_mkb_popular_list_font_size'] ?>px;
			}
			
			.betterdocs-popular-list.multiple-kb ul li a:hover{
				color: <?php echo $output['betterdocs_mkb_popular_list_hover_color'] ?>;
			}
			.betterdocs-popular-list ul li svg {
				fill: <?php echo $output['betterdocs_mkb_popular_list_icon_color'] ?>;
			}

			.betterdocs-popular-list ul li svg  {
				font-size: <?php echo $output['betterdocs_mkb_popular_list_icon_font_size'] ?>px;
				min-width: <?php echo $output['betterdocs_mkb_popular_list_icon_font_size'] ?>px;
			}

		<?php } ?>
			
			.cat-layout-4 .docs-cat-list-2-items .docs-cat-title {
				font-size: <?php echo $output['betterdocs_doc_page_cat_title_font_size2'] ?>px;
			}
			.betterdocs-list-popular .betterdocs-archive-popular .betterdocs-categories-wrap.betterdocs-popular-list.single-kb ul{
				padding-top: <?php echo $output['betterdocs_doc_page_popular_docs_padding_top']?>px;
				padding-right: <?php echo $output['betterdocs_doc_page_popular_docs_padding_right']?>px;
				padding-bottom: <?php echo $output['betterdocs_doc_page_popular_docs_padding_bottom']?>px;
				padding-left: <?php echo $output['betterdocs_doc_page_popular_docs_padding_left'] ?>px;
				margin-top: <?php echo $output['betterdocs_doc_page_article_list_margin_top_2'] ?>px;
				margin-right: <?php echo $output['betterdocs_doc_page_article_list_margin_right_2'] ?>px;
				margin-bottom: <?php echo $output['betterdocs_doc_page_article_list_margin_bottom_2'] ?>px;
				margin-left: <?php echo $output['betterdocs_doc_page_article_list_margin_left_2'] ?>px;
			}
			.betterdocs-list-popular .betterdocs-archive-popular .betterdocs-categories-wrap.betterdocs-popular-list.single-kb{
				background-color: <?php echo $output['betterdocs_doc_page_article_list_bg_color_2'] ?>;
			}
			.betterdocs-list-popular .betterdocs-archive-popular .single-kb ul li a {
				color: <?php echo $output['betterdocs_doc_page_article_list_color_2']?>;
				font-size: <?php echo $output['betterdocs_doc_page_article_list_font_size_2'] ?>px;
			}
			.betterdocs-list-popular .betterdocs-archive-popular .single-kb ul li a:hover{
				color: <?php echo $output['betterdocs_doc_page_article_list_hover_color_2'] ?>;
			}
			.betterdocs-list-popular .betterdocs-archive-popular .single-kb .popular-title {
				margin-top: <?php echo $output['betterdocs_doc_page_popular_title_margin_top']?>px;
				margin-right: <?php echo $output['betterdocs_doc_page_popular_title_margin_right']?>px;
				margin-bottom: <?php echo $output['betterdocs_doc_page_popular_title_margin_bottom']?>px;
				margin-left: <?php echo $output['betterdocs_doc_page_popular_title_margin_left']?>px;
				font-size: <?php echo $output['betterdocs_doc_page_article_title_font_size_2'] ?>px;
				color: <?php echo $output['betterdocs_doc_page_article_title_color_2'] ?>;
			}
			.betterdocs-list-popular .betterdocs-archive-popular .single-kb .popular-title:hover {
				color: <?php echo $output['betterdocs_doc_page_article_title_color_hover_2'] ?>;
			}
			.betterdocs-popular-list.single-kb ul li svg path{
				fill: <?php echo $output['betterdocs_doc_page_article_list_icon_color_2'] ?>;
			}

			.betterdocs-popular-list.single-kb ul li svg {
				min-width: <?php echo $output['betterdocs_doc_page_article_list_icon_font_size_2']?>px;
			}
		.betterdocs-categories-wrap.category-grid.pro-layout-4 .docs-cat-list-2-items .docs-cat-title {
			font-size: <?php echo $output['betterdocs_doc_page_cat_title_font_size2'] ?>px;
		}
		.betterdocs-category-box.pro-layout-3 .docs-single-cat-wrap img,
		.docs-cat-list-2-box img {
			<?php if(!empty($output['betterdocs_doc_page_cat_icon_size_l_3_4'])) { ?>
			height: <?php echo $output['betterdocs_doc_page_cat_icon_size_l_3_4'] ?>px;
			<?php } ?>
			width: auto;
			margin-bottom: 0px !important;
		}
		<?php if($layout_select === 'layout-4') { ?>

		.betterdocs-archive-wrap.cat-layout-4 {
			margin-top: -<?php echo $output['betterdocs_doc_page_content_overlap'] ?>px;
		}
		.betterdocs-archive-wrap .betterdocs-categories-wrap .docs-cat-list-2-items .docs-item-container li,
		.betterdocs-archive-wrap .betterdocs-categories-wrap .docs-cat-list-2-items .docs-item-container .docs-sub-cat-title {
			margin-left: 0;
			margin-right: 0;
		}
				
		<?php if(get_theme_mod('betterdocs_doc_page_explore_btn_margin_left')) {?>
		.betterdocs-categories-wrap.pro-layout-4.single-kb .docs-single-cat-wrap.docs-cat-list-2-items .docs-item-container .docs-cat-link-btn  {
			margin-left: 0;
		}
		<?php } ?>

		<?php if(get_theme_mod('betterdocs_doc_page_explore_btn_margin_right')) {?>
		.betterdocs-categories-wrap.pro-layout-4.single-kb .docs-single-cat-wrap.docs-cat-list-2-items .docs-item-container .docs-cat-link-btn  {
			margin-right:0;
		}
		<?php } ?>
		.betterdocs-single-bg .betterdocs-content-full {
			background-color: <?php echo $output['betterdocs_doc_single_content_area_bg_color'] ?>;
		}
		.betterdocs-single-wraper .betterdocs-content-full {
			padding-right: <?php echo $output['betterdocs_doc_single_content_area_padding_right'] ?>px;
			padding-left: <?php echo $output['betterdocs_doc_single_content_area_padding_left'] ?>px;
		}
		<?php } ?>

		/** Customizer Controls (Doc Page Layout 6) **/

		/** CSS given here to again to render layout-6 properly during initial render after plugin update **/
		.betterdocs-category-grid-layout-6{
			position: relative;
			display: flex;
			margin: 0 auto;
			margin-bottom: 60px;
			padding-left: 5px;
			padding-right: 5px;
			border-bottom: 1px solid #E8E9EB;
			padding-bottom: 60px;
		}
		.betterdocs-category-grid-layout-6:last-of-type{
			padding-bottom: 0px;
			border: none;
		}

		.betterdocs-term-img{
			width:<?php echo $output['betterdocs_doc_list_img_width_layout6'] ?>%;
		}

		.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-term-title{
			font-size: <?php echo $output['betterdocs_doc_page_cat_title_font_size_layout6'] ?>px;
		}

		.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-term-count{
			color: <?php echo $output['betterdocs_doc_page_item_count_color_layout6']?>;
			background-color: <?php echo $output['betterdocs_doc_page_item_count_back_color_layout6'] ?>;
			border-top-left-radius: <?php echo $output['betterdocs_doc_page_item_count_border_radius_top_left_layout6'] ?>px;
			border-top-right-radius: <?php echo $output['betterdocs_doc_page_item_count_border_radius_top_right_layout6'] ?>px;
			border-bottom-right-radius: <?php echo $output['betterdocs_doc_page_item_count_border_radius_bottom_right_layout6'] ?>px;
			border-bottom-left-radius: <?php echo $output['betterdocs_doc_page_item_count_border_radius_bottom_left_layout6'] ?>px;
			margin-top: <?php echo $output['betterdocs_doc_page_item_count_margin_top_layout6'] ?>px;
			margin-right: <?php echo $output['betterdocs_doc_page_item_count_margin_right_layout6'] ?>px;
			margin-bottom: <?php echo $output['betterdocs_doc_page_item_count_margin_bottom_layout6'] ?>px;
			margin-left: <?php echo $output['betterdocs_doc_page_item_count_margin_left_layout6'] ?>px;
			padding-top:<?php echo $output['betterdocs_doc_page_item_count_padding_top_layout6'] ?>px;
			padding-right:<?php echo $output['betterdocs_doc_page_item_count_padding_right_layout6'] ?>px;
			padding-bottom: <?php echo $output['betterdocs_doc_page_item_count_padding_bottom_layout6'] ?>px;
			padding-left:<?php echo $output['betterdocs_doc_page_item_count_padding_left_layout6'] ?>px;
			border-style: <?php echo $output['betterdocs_doc_page_item_count_border_type_layout6'] ?>;
			border-color: <?php echo $output['betterdocs_doc_page_item_count_border_color_layout6'] ?>;
			border-top-width: <?php echo $output['betterdocs_doc_page_item_count_border_width_top_layout6'] ?>px;
			border-right-width: <?php echo $output['betterdocs_doc_page_item_count_border_width_right_layout6'] ?>px;
			border-bottom-width: <?php echo $output['betterdocs_doc_page_item_count_border_width_bottom_layout6'] ?>px;
			border-left-width:<?php echo $output['betterdocs_doc_page_item_count_border_width_left_layout6'] ?>px;
			font-size: <?php echo $output['betterdocs_doc_page_item_count_font_size_layout6'] ?>px;
		}

		.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-doc-list li .betterdocs-article p{
			font-size:<?php echo $output['betterdocs_doc_list_font_size_layout6'] ?>px;
			font-weight: <?php echo $output['betterdocs_doc_list_font_weight_layout6'] ?>;
			line-height: <?php echo $output['betterdocs_doc_list_font_line_height_layout6'] ?>px;
			color: <?php echo $output['betterdocs_doc_list_font_color_layout6'] ?>;
			margin-top: <?php echo $output['betterdocs_doc_list_margin_top_layout6'] ?>px;
			margin-left: <?php echo $output['betterdocs_doc_list_margin_left_layout6'] ?>px;
			margin-right: <?php echo $output['betterdocs_doc_list_margin_right_layout6'] ?>px;
			margin-bottom: <?php echo $output['betterdocs_doc_list_margin_bottom_layout6'] ?>px;
			padding-top: <?php echo $output['betterdocs_doc_list_padding_top_layout6'] ?>px;
			padding-right: <?php echo $output['betterdocs_doc_list_padding_right_layout6'] ?>px;
			padding-bottom: <?php echo $output['betterdocs_doc_list_padding_bottom_layout6'] ?>px;
			padding-left:<?php echo $output['betterdocs_doc_list_padding_left_layout6'] ?>px;
		}

		.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-doc-list .betterdocs-article{
			border-style: <?php echo $output['betterdocs_doc_list_border_style_layout6'] ?>;
			border-top-width: <?php echo $output['betterdocs_doc_list_border_top_layout6'] ?>px;
			border-right-width: <?php echo $output['betterdocs_doc_list_border_right_layout6'] ?>px;
			border-bottom-width: <?php echo $output['betterdocs_doc_list_border_bottom_layout6'] ?>px;
			border-left-width: <?php echo $output['betterdocs_doc_list_border_left_layout6'] ?>px;
			border-top-color: <?php echo $output['betterdocs_doc_list_border_color_top_layout6'] ?>;
			border-right-color: <?php echo $output['betterdocs_doc_list_border_color_right_layout6'] ?>;
			border-bottom-color: <?php echo $output['betterdocs_doc_list_border_color_bottom_layout6'] ?>;
			border-left-color: <?php echo $output['betterdocs_doc_list_border_color_left_layout6'] ?>;
			transition: all .4s ease-in-out;
		}

		.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-doc-list .betterdocs-article:hover{
			border-color: <?php echo $output['betterdocs_doc_list_border_color_hover_layout6'] ?> !important;
			background-color: <?php echo $output['betterdocs_doc_list_back_color_hover_layout6'] ?>;
			border-top-width: <?php echo $output['betterdocs_doc_list_border_hover_top_layout6'] ?>px;
			border-right-width:<?php echo $output['betterdocs_doc_list_border_hover_right_layout6'] ?>px;
			border-bottom-width:<?php echo $output['betterdocs_doc_list_border_hover_bottom_layout6'] ?>px;
			border-left-width:<?php echo $output['betterdocs_doc_list_border_hover_left_layout6'] ?>px;
		}

		.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-doc-list li .betterdocs-article p:hover{
			color:<?php echo $output['betterdocs_doc_list_font_color_hover_layout6'] ?>;
		}

		.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-doc-list li a .doc-list-arrow{
			height: <?php echo $output['betterdocs_doc_list_arrow_height_layout6'] ?>px;
			width: <?php echo $output['betterdocs_doc_list_arrow_width_layout6'] ?>px;
		}

		.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-doc-list li a .doc-list-arrow path{
			fill: <?php echo $output['betterdocs_doc_list_arrow_color_layout6'] ?>;
		}

		.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-doc-list li.betterdocs-explore-more .doc-explore-more{
			height: <?php echo $output['betterdocs_doc_list_explore_more_arrow_height_layout6'] ?>px;
			width: <?php echo $output['betterdocs_doc_list_explore_more_arrow_width_layout6'] ?>px;
		}

		.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-doc-list li.betterdocs-explore-more .doc-explore-more path{
			fill: <?php echo $output['betterdocs_doc_list_explore_more_arrow_color_layout6'] ?>
		}

		.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-doc-list li a.betterdocs-term-explore-more p{
			font-size: <?php echo $output['betterdocs_doc_list_explore_more_font_size_layout6'] ?>px;
			color: <?php echo $output['betterdocs_doc_list_explore_more_font_color_layout6'] ?>;
			font-weight: <?php echo $output['betterdocs_doc_list_explore_more_font_weight_layout6'] ?>;
			line-height: <?php echo $output['betterdocs_doc_list_explore_more_font_line_height_layout6'] ?>px;
			padding-top: <?php echo $output['betterdocs_doc_list_explore_more_padding_top_layout6'] ?>px;
			padding-right: <?php echo $output['betterdocs_doc_list_explore_more_padding_right_layout6'] ?>px;
			padding-bottom: <?php echo $output['betterdocs_doc_list_explore_more_padding_bottom_layout6'] ?>px;
			padding-left: <?php echo $output['betterdocs_doc_list_explore_more_padding_left_layout6'] ?>px;
			margin-top: <?php echo $output['betterdocs_doc_list_explore_more_margin_top_layout6'] ?>px;
			margin-right: <?php echo $output['betterdocs_doc_list_explore_more_margin_right_layout6'] ?>px;
			margin-bottom: <?php echo $output['betterdocs_doc_list_explore_more_margin_bottom_layout6'] ?>px;
			margin-left: <?php echo $output['betterdocs_doc_list_explore_more_margin_left_layout6'] ?>px;
		}

		.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-term-description{
			color: <?php echo $output['betterdocs_doc_list_desc_color_layout6']?>;
			font-size: <?php echo $output['betterdocs_doc_list_desc_font_size_layout6'] ?>px;
			font-weight: <?php echo $output['betterdocs_doc_list_desc_font_weight_layout6'] ?>;
			line-height: <?php echo $output['betterdocs_doc_list_desc_line_height_layout6'] ?>px;
			margin-top: <?php echo $output['betterdocs_doc_list_desc_margin_top_layout6'] ?>px;
			margin-right: <?php echo $output['betterdocs_doc_list_desc_margin_right_layout6'] ?>px;
			margin-bottom: <?php echo $output['betterdocs_doc_list_desc_margin_bottom_layout6'] ?>px;
			margin-left: <?php echo $output['betterdocs_doc_list_desc_margin_left_layout6'] ?>px;
		}

		.betterdocs-category-grid-layout-6 .betterdocs-term-title-count{
			padding-bottom: <?php echo $output['betterdocs_doc_page_cat_title_padding_bottom_layout6'] ?>px;
		}

		/** Customizer Controls (Doc Page Layout 6) **/ 

		/** Customizer Controls (Archive Page Layout 2) **/ 

		.betterdocs-doc-category-layout-2{
			background-color:<?php echo $output['betterdocs_archive_inner_content_back_color_layout2'] ?>;
		}

		.betterdocs-doc-category-term-img{
			width: <?php echo $output['betterdocs_archive_inner_content_image_size_layout2'] ?>%;
		}

		.betterdocs-doc-category-layout-2 .betterdocs-doc-category-term-img img{
			padding-top: <?php echo $output['betterdocs_archive_inner_content_image_padding_top_layout2']?>px;
			padding-right: <?php echo $output['betterdocs_archive_inner_content_image_padding_right_layout2'] ?>px;
			padding-bottom: <?php echo $output['betterdocs_archive_inner_content_image_padding_bottom_layout2'] ?>px;
			padding-left:<?php echo $output['betterdocs_archive_inner_content_image_padding_left_layout2'] ?>px;
			margin-top: <?php echo $output['betterdocs_archive_inner_content_image_margin_top_layout2'] ?>px;
			margin-right: <?php echo $output['betterdocs_archive_inner_content_image_margin_right_layout2'] ?>px;
			margin-bottom: <?php echo $output['betterdocs_archive_inner_content_image_margin_bottom_layout2'] ?>px;
			margin-left:<?php echo $output['betterdocs_archive_inner_content_image_margin_left_layout2'] ?>px;
		}

		.betterdocs-doc-category-layout-2 .betterdocs-doc-category-term-info .betterdocs-doc-category-term-title-count .betterdocs-doc-category-term-title{
			color: <?php echo $output['betterdocs_archive_title_color_layout2']?>;
			font-size:<?php echo $output['betterdocs_archive_title_font_size_layout2'] ?>px;
			margin-top:<?php echo $output['betterdocs_archive_title_margin_top_layout2'] ?>px;
			margin-right:<?php echo $output['betterdocs_archive_title_margin_right_layout2'] ?>px;
			margin-bottom:<?php echo $output['betterdocs_archive_title_margin_bottom_layout2'] ?>px;
			margin-left:<?php echo $output['betterdocs_archive_title_margin_left_layout2'] ?>px;
		}

		.betterdocs-doc-category-term-description p{
			color:<?php echo $output['betterdocs_archive_description_color_layout2'] ?>;
			font-size:<?php echo $output['betterdocs_archive_description_font_size_layout2'] ?>px;
			margin-top: <?php echo $output['betterdocs_archive_description_margin_top_layout2'] ?>px;
			margin-right: <?php echo $output['betterdocs_archive_description_margin_right_layout2'] ?>px;
			margin-bottom: <?php echo $output['betterdocs_archive_description_margin_bottom_layout2'] ?>px;
			margin-left: <?php echo $output['betterdocs_archive_description_margin_left_layout2'] ?>px;
		}

		.betterdocs-doc-category-layout-2-list li {
			border-style: <?php echo $output['betterdocs_archive_list_border_style_layout2'] ?>;
			border-top-width: <?php echo $output['betterdocs_archive_list_border_width_top_layout2'] ?>px;
			border-right-width: <?php echo $output['betterdocs_archive_list_border_width_right_layout2'] ?>px;
			border-bottom-width: <?php echo $output['betterdocs_archive_list_border_width_bottom_layout2'] ?>px;
			border-left-width: <?php echo $output['betterdocs_archive_list_border_width_left_layout2'] ?>px;
			border-top-color: <?php echo $output['betterdocs_archive_list_border_width_color_top_layout2'] ?>;
			border-right-color: <?php echo $output['betterdocs_archive_list_border_width_color_right_layout2'] ?>;
			border-bottom-color: <?php echo $output['betterdocs_archive_list_border_width_color_bottom_layout2'] ?>;
			border-left-color: <?php echo $output['betterdocs_archive_list_border_width_color_left_layout2'] ?>;
			transition: all .4s ease-in-out;
		}

		.betterdocs-doc-category-layout-2-list li:hover{
			background-color: <?php echo $output['betterdocs_archive_list_back_color_hover_layout2'] ?>;
			border-color: <?php echo $output['betterdocs_archive_list_border_color_hover_layout2'] ?> !important;
			border-top-width: <?php echo $output['betterdocs_archive_list_border_width_top_hover_layout2'] ?>px;
			border-right-width:<?php echo $output['betterdocs_archive_list_border_width_right_hover_layout2'] ?>px;
			border-bottom-width:<?php echo $output['betterdocs_archive_list_border_width_bottom_hover_layout2'] ?>px;
			border-left-width: <?php echo $output['betterdocs_archive_list_border_width_left_hover_layout2'] ?>px;
		}
		.betterdocs-doc-category-layout-2-list li:last-of-type:hover{
			border-bottom-width: <?php echo $output['betterdocs_archive_list_border_width_bottom_hover_layout2'] ?>px;
			border-bottom-color: <?php echo $output['betterdocs_archive_list_border_color_hover_layout2'] ?>;
		}

		.betterdocs-doc-category-layout-2-list li a.betterdocs-article p{
			color:<?php echo $output['betterdocs_archive_list_item_color_layout2'] ?>;
			font-size:<?php echo $output['betterdocs_archive_list_item_font_size_layout2'] ?>px;
			margin-top: <?php echo $output['betterdocs_archive_article_list_margin_top_layout2'] ?>px;
			margin-right: <?php echo $output['betterdocs_archive_article_list_margin_right_layout2'] ?>px;
			margin-bottom: <?php echo $output['betterdocs_archive_article_list_margin_bottom_layout2'] ?>px;
			margin-left:<?php echo $output['betterdocs_archive_article_list_margin_left_layout2'] ?>px;
			font-weight:<?php echo $output['betterdocs_archive_article_list_font_weight_layout2'] ?>;
			line-height:<?php echo $output['betterdocs_archive_list_item_line_height_layout2'] ?>px;
		}

		.betterdocs-doc-category-layout-2-list li a.betterdocs-article p:hover{
			color:<?php echo $output['betterdocs_archive_list_item_color_hover_layout2'] ?>;
		}

		.betterdocs-doc-category-layout-2-list li a .archive-doc-arrow{
			height:<?php echo $output['betterdocs_archive_list_item_arrow_height_layout2'] ?>px;
			width: <?php echo $output['betterdocs_archive_list_item_arrow_width_layout2'] ?>px;
			margin-top: <?php echo $output['betterdocs_archive_article_list_arrow_margin_top_layout2'] ?>px;
			margin-right: <?php echo $output['betterdocs_archive_article_list_arrow_margin_right_layout2'] ?>px;
			margin-bottom:<?php echo $output['betterdocs_archive_article_list_arrow_margin_bottom_layout2'] ?>px;
			margin-left:<?php echo $output['betterdocs_archive_article_list_arrow_margin_left_layout2'] ?>px;
		}

		.betterdocs-doc-category-layout-2-list li a .archive-doc-arrow path{
			fill: <?php echo $output['betterdocs_archive_list_item_arrow_color_layout2']?>;
		}

		.betterdocs-doc-category-layout-2-list li p.betterdocs-excerpt{
			font-weight:<?php echo $output['betterdocs_archive_article_list_excerpt_font_weight_layout2'] ?>;
			font-size:<?php echo $output['betterdocs_archive_article_list_excerpt_font_size_layout2'] ?>px;
			line-height: <?php echo $output['betterdocs_archive_article_list_excerpt_font_line_height_layout2']?>px;
			color: <?php echo $output['betterdocs_archive_article_list_excerpt_font_color_layout2'] ?>;
			margin-top:<?php echo $output['betterdocs_archive_article_list_excerpt_margin_top_layout2'] ?>px;
			margin-right:<?php echo $output['betterdocs_archive_article_list_excerpt_margin_right_layout2']?>px;
			margin-bottom:<?php echo $output['betterdocs_archive_article_list_excerpt_margin_bottom_layout2']?>px;
			margin-left:<?php echo $output['betterdocs_archive_article_list_excerpt_margin_left_layout2'] ?>px;
			padding-top: <?php echo $output['betterdocs_archive_article_list_excerpt_padding_top_layout2'] ?>px;
			padding-right: <?php echo $output['betterdocs_archive_article_list_excerpt_padding_right_layout2'] ?>px;
			padding-bottom:<?php echo $output['betterdocs_archive_article_list_excerpt_padding_bottom_layout2'] ?>px;
			padding-left:<?php echo $output['betterdocs_archive_article_list_excerpt_padding_left_layout2'] ?>px;
		}

		.betterdocs-doc-category-layout-2 .betterdocs-doc-category-term-info .betterdocs-doc-category-term-title-count .betterdocs-doc-category-term-count{
			font-weight:<?php echo $output['betterdocs_archive_article_list_counter_font_weight_layout2'] ?>;
			font-size:<?php echo $output['betterdocs_archive_article_list_counter_font_size_layout2'] ?>px;
			line-height:<?php echo $output['betterdocs_archive_article_list_counter_font_line_height_layout2'] ?>px;
			color:<?php echo $output['betterdocs_archive_article_list_counter_font_color_layout2'] ?>;
			border-top-left-radius:<?php echo $output['betterdocs_archive_article_list_counter_border_radius_top_left_layout2'] ?>px;
			border-top-right-radius:<?php echo $output['betterdocs_archive_article_list_counter_border_radius_top_right_layout2'] ?>px;
			border-bottom-right-radius:<?php echo $output['betterdocs_archive_article_list_counter_border_radius_bottom_right_layout2'] ?>px;
			border-bottom-left-radius:<?php echo $output['betterdocs_archive_article_list_counter_border_radius_bottom_left_layout2'] ?>px;
			margin-top:<?php echo $output['betterdocs_archive_article_list_counter_margin_top_layout2'] ?>px;
			margin-right:<?php echo $output['betterdocs_archive_article_list_counter_margin_right_layout2'] ?>px;
			margin-bottom:<?php echo $output['betterdocs_archive_article_list_counter_margin_bottom_layout2'] ?>px;
			margin-left:<?php echo $output['betterdocs_archive_article_list_counter_margin_left_layout2'] ?>px;
			padding-top:<?php echo $output['betterdocs_archive_article_list_counter_padding_top_layout2'] ?>px;
			padding-right:<?php echo $output['betterdocs_archive_article_list_counter_padding_right_layout2'] ?>px;
			padding-bottom:<?php echo $output['betterdocs_archive_article_list_counter_padding_bottom_layout2'] ?>px;
			padding-left:<?php echo $output['betterdocs_archive_article_list_counter_padding_left_layout2'] ?>px;
			border-color:<?php echo $output['betterdocs_archive_article_list_counter_border_color_layout2'] ?>;
			background-color:<?php echo $output['betterdocs_archive_article_list_counter_back_color_layout2'] ?>;

		}
		.betterdocs-related-info .betterdocs-related-term-text{
			color:<?php echo $output['betterdocs_archive_other_categories_title_color'] ?>;
			font-weight: <?php echo $output['betterdocs_archive_other_categories_title_font_weight'] ?>;
			font-size: <?php echo $output['betterdocs_archive_other_categories_title_font_size'] ?>px;
			line-height: <?php echo $output['betterdocs_archive_other_categories_title_line_height'] ?>px;
			padding-top: <?php echo $output['betterdocs_archive_other_categories_title_padding_top'] ?>px;
			padding-right: <?php echo $output['betterdocs_archive_other_categories_title_padding_right'] ?>px;
			padding-bottom: <?php echo $output['betterdocs_archive_other_categories_title_padding_bottom'] ?>px;
			padding-left: <?php echo $output['betterdocs_archive_other_categories_title_padding_left'] ?>px;
			margin-top: <?php echo $output['betterdocs_archive_other_categories_title_margin_top'] ?>px;
			margin-right: <?php echo $output['betterdocs_archive_other_categories_title_margin_right'] ?>px;
			margin-bottom: <?php echo $output['betterdocs_archive_other_categories_title_margin_bottom'] ?>px;
			margin-left: <?php echo $output['betterdocs_archive_other_categories_title_margin_left'] ?>px;
		}

		.betterdocs-related-info .betterdocs-related-term-text:hover{
			color: <?php echo $output['betterdocs_archive_other_categories_title_hover_color'] ?>;
		}

		.betterdocs-related-info .betterdocs-related-term-count{
			color:<?php echo $output['betterdocs_archive_other_categories_count_color'] ?>;
			background-color: <?php echo $output['betterdocs_archive_other_categories_count_back_color'] ?>;
			line-height: <?php echo $output['betterdocs_archive_other_categories_count_line_height'] ?>px;
			font-weight:<?php echo $output['betterdocs_archive_other_categories_count_font_weight'] ?>;
			font-size:<?php echo $output['betterdocs_archive_other_categories_count_font_size'] ?>px;
			border-top-left-radius: <?php echo $output['betterdocs_archive_other_categories_count_border_radius_topleft'] ?>px;
			border-top-right-radius: <?php echo $output['betterdocs_archive_other_categories_count_border_radius_topright'] ?>px;
			border-bottom-right-radius: <?php echo $output['betterdocs_archive_other_categories_count_border_radius_bottomright'] ?>px;
			border-bottom-left-radius:<?php echo $output['betterdocs_archive_other_categories_count_border_radius_bottomleft'] ?>px;
			padding-top:<?php echo $output['betterdocs_archive_other_categories_count_padding_top'] ?>px;
			padding-right: <?php echo $output['betterdocs_archive_other_categories_count_padding_right'] ?>px;
			padding-bottom: <?php echo $output['betterdocs_archive_other_categories_count_padding_bottom'] ?>px;
			padding-left: <?php echo $output['betterdocs_archive_other_categories_count_padding_left'] ?>px;
			margin-top:<?php echo $output['betterdocs_archive_other_categories_count_margin_top'] ?>px;
			margin-right:<?php echo $output['betterdocs_archive_other_categories_count_margin_right'] ?>px;
			margin-bottom:<?php echo $output['betterdocs_archive_other_categories_count_margin_bottom'] ?>px;
			margin-left:<?php echo $output['betterdocs_archive_other_categories_count_margin_left'] ?>px;
		}

		.betterdocs-related-info .betterdocs-related-term-count:hover{
			background-color: <?php echo $output['betterdocs_archive_other_categories_count_back_color_hover'] ?>;
		}

		.betterdocs-related-category .betterdocs-related-term-desc{
			color:<?php echo $output['betterdocs_archive_other_categories_description_color'] ?>;
			font-weight:<?php echo $output['betterdocs_archive_other_categories_description_font_weight'] ?>;
			font-size:<?php echo $output['betterdocs_archive_other_categories_description_font_size'] ?>px;
			line-height:<?php echo $output['betterdocs_archive_other_categories_description_line_height'] ?>px;
			padding-top:<?php echo $output['betterdocs_archive_other_categories_description_padding_top'] ?>px;
			padding-right:<?php echo $output['betterdocs_archive_other_categories_description_padding_right'] ?>px;
			padding-bottom:<?php echo $output['betterdocs_archive_other_categories_description_padding_bottom'] ?>px;
			padding-left:<?php echo $output['betterdocs_archive_other_categories_description_padding_left'] ?>px;
		}

		.betterdocs-related-category img{
			width:<?php echo $output['betterdocs_archive_other_categories_image_size'] ?>%;
		}

		.betterdocs-show-more-terms .betterdocs-load-more-button{
			color: <?php echo $output['betterdocs_archive_other_categories_button_color'] ?>;
			background-color: <?php echo $output['betterdocs_archive_other_categories_button_back_color'] ?>;
			font-weight:<?php echo $output['betterdocs_archive_other_categories_button_font_weight'] ?>;
			font-size:<?php echo $output['betterdocs_archive_other_categories_button_font_size'] ?>px;
			line-height:<?php echo $output['betterdocs_archive_other_categories_button_font_line_height'] ?>px;
			border-top-left-radius:<?php echo $output['betterdocs_archive_other_categories_button_border_radius_top_left'] ?>px;
			border-top-right-radius:<?php echo $output['betterdocs_archive_other_categories_button_border_radius_top_right'] ?>px;
			border-bottom-right-radius: <?php echo $output['betterdocs_archive_other_categories_button_border_radius_bottom_right'] ?>px;
			border-bottom-left-radius: <?php echo $output['betterdocs_archive_other_categories_button_border_radius_bottom_left'] ?>px;
			padding-top:<?php echo $output['betterdocs_archive_other_categories_button_padding_top'] ?>px;
			padding-right:<?php echo $output['betterdocs_archive_other_categories_button_padding_right'] ?>px;
			padding-bottom:<?php echo $output['betterdocs_archive_other_categories_button_padding_bottom'] ?>px;
			padding-left:<?php echo $output['betterdocs_archive_other_categories_button_padding_left'] ?>px;
		}
		.betterdocs-show-more-terms .betterdocs-load-more-button:hover, .betterdocs-show-more-terms .betterdocs-load-more-button:active, .betterdocs-show-more-terms .betterdocs-load-more-button:focus{
			background-color:<?php echo $output['betterdocs_archive_other_categories_button_back_color_hover'] ?>;
		}
		/** Customizer Controls (Archive Page Layout 2) **/ 

		/** Doc Sidebar Layout 6(For Single Doc Layout 6) **/
		.betterdocs-sidebar-layout-6{
			background-color: <?php echo $output['betterdocs_sidebar_bg_color_layout6']?>;
			padding-top:<?php echo $output['betterdocs_sidebar_padding_top_layout6'] ?>px;
			padding-right: <?php echo $output['betterdocs_sidebar_padding_right_layout6'] ?>px;
			padding-bottom:<?php echo $output['betterdocs_sidebar_padding_bottom_layout6'] ?>px;
			padding-left: <?php echo $output['betterdocs_sidebar_padding_left_layout6'] ?>px;
			margin-top: <?php echo $output['betterdocs_sidebar_margin_top_layout6'] ?>px;
			margin-right:<?php echo $output['betterdocs_sidebar_margin_right_layout6'] ?>px;
			margin-bottom: <?php echo $output['betterdocs_sidebar_margin_bottom_layout6'] ?>px;
			margin-left:<?php echo $output['betterdocs_sidebar_margin_left_layout6'] ?>px;
			border-top-left-radius:<?php echo $output['betterdocs_sidebar_border_radius_top_left_layout6'] ?>px;
			border-top-right-radius: <?php echo $output['betterdocs_sidebar_border_radius_top_right_layout6'] ?>px;
			border-bottom-right-radius: <?php echo $output['betterdocs_sidebar_border_radius_bottom_right_layout6'] ?>px;
			border-bottom-left-radius: <?php echo $output['betterdocs_sidebar_border_radius_bottom_left_layout6'] ?>px;
		}

		.betterdocs-sidebar-layout-6 .betterdocs-sidebar-category-title-count{
			background-color: <?php echo $output['betterdocs_sidebar_title_bg_color_layout6'] ?>;
		}

		.betterdocs-sidebar-layout-6 li .betterdocs-sidebar-category-title-count.current-term{
			background-color:<?php echo $output['betterdocs_sidebar_active_title_bg_color_layout6'] ?>;
		}

		.betterdocs-sidebar-layout-6 li .doc-list.current-doc-list{
			background-color:<?php echo $output['betterdocs_sidebar_active_bg_color_layout6'] ?>;
		}
		.betterdocs-sidebar-layout-6 li .doc-list ul li{
			padding-top: <?php echo $output['betterdocs_sidebar_term_list_item_padding_top_layout6'] ?>px;
			padding-right: <?php echo $output['betterdocs_sidebar_term_list_item_padding_right_layout6'] ?>px;
			padding-bottom:<?php echo $output['betterdocs_sidebar_term_list_item_padding_bottom_layout6'] ?>px;
			padding-left: <?php echo $output['betterdocs_sidebar_term_list_item_padding_left_layout6'] ?>px;
		}

		.betterdocs-sidebar-layout-6 li .doc-list ul li a{
			color:<?php echo $output['betterdocs_sidebar_term_list_item_color_layout6'] ?>;
			font-size: <?php echo $output['betterdocs_sidebar_term_list_item_font_size_layout6'] ?>px;
		}

		.betterdocs-sidebar-layout-6 li .doc-list ul li a:hover{
			color:<?php echo $output['betterdocs_sidebar_term_list_item_hover_color_layout6'] ?>;
		}

		.betterdocs-sidebar-layout-6 li .doc-list ul li svg{
			fill: <?php echo $output['betterdocs_sidebar_term_list_item_icon_color_layout6'] ?>;
			height:<?php echo $output['betterdocs_sidebar_term_list_item_icon_size_layout6'] ?>px;
			width:<?php echo $output['betterdocs_sidebar_term_list_item_icon_size_layout6'] ?>px;
		}

		.betterdocs-sidebar-layout-6 li .doc-list.current-doc-list ul li a.active-doc, .betterdocs-sidebar-layout-6 li .doc-list .nested-docs-sub-cat.current-sub-cat li a.active-doc{
			color:<?php echo $output['betterdocs_sidebar_term_list_active_item_color_layout6'] ?>;
		}

		.betterdocs-sidebar-layout-6 li .betterdocs-sidebar-category-title-count.current-term, 	.betterdocs-sidebar-layout-6 li .doc-list.current-doc-list{
			border-color:<?php echo $output['betterdocs_sidebar_active_bg_border_color_layout6'] ?>;
		}

		.betterdocs-sidebar-layout-6 li .betterdocs-sidebar-category-title-count a .betterdocs-sidebar-term-heading{
			color: <?php echo $output['betterdocs_sidebar_title_color_layout6'] ?>;
			font-size:<?php echo $output['betterdocs_sidebar_title_font_size_layout6'] ?>px;
			line-height:<?php echo $output['betterdocs_sidebar_title_font_line_height_layout6'] ?>px;
			font-weight:<?php echo $output['betterdocs_sidebar_title_font_weight_layout6'] ?>;
			padding-top: <?php echo $output['betterdocs_sidebar_title_padding_top_layout6'] ?>px;
			padding-right: <?php echo $output['betterdocs_sidebar_title_padding_right_layout6'] ?>px;
			padding-bottom: <?php echo $output['betterdocs_sidebar_title_padding_bottom_layout6'] ?>px;
			padding-left: <?php echo $output['betterdocs_sidebar_title_padding_left_layout6'] ?>px;
			margin-top: <?php echo $output['betterdocs_sidebar_title_margin_top_layout6'] ?>px;
			margin-right: <?php echo $output['betterdocs_sidebar_title_margin_right_layout6'] ?>px;
			margin-bottom:<?php echo $output['betterdocs_sidebar_title_margin_bottom_layout6'] ?>px;
			margin-left:<?php echo $output['betterdocs_sidebar_title_margin_left_layout6'] ?>px;
		}

		.betterdocs-sidebar-layout-6 li{
			border-style:<?php echo $output['betterdocs_sidebar_term_list_border_type_layout6'] ?>;
			border-top-width: <?php echo $output['betterdocs_sidebar_term_border_top_width_layout6'] ?>px;
			border-right-width: <?php echo $output['betterdocs_sidebar_term_border_right_width_layout6'] ?>px;
			border-bottom-width: <?php echo $output['betterdocs_sidebar_term_border_bottom_width_layout6'] ?>px;
			border-left-width: <?php echo $output['betterdocs_sidebar_term_border_left_width_layout6'] ?>px;
			border-color:<?php echo $output['betterdocs_sidebar_term_border_width_color_layout6'] ?>;
		}

		.betterdocs-sidebar-layout-6 li .betterdocs-sidebar-category-title-count a .betterdocs-sidebar-term-heading:hover{
			color: <?php echo $output['betterdocs_sidebar_title_hover_color_layout6'] ?>;
		}

		.betterdocs-sidebar-layout-6 li .betterdocs-sidebar-category-title-count span.betterdocs-sidebar-term-count{
			font-size:<?php echo $output['betterdocs_sidebar_term_item_counter_font_size_layout6'] ?>px;
			font-weight:<?php echo $output['betterdocs_sidebar_term_item_counter_font_weight_layout6'] ?>;
			line-height:<?php echo $output['betterdocs_sidebar_term_item_counter_font_line_height_layout6'] ?>px;
			color:<?php echo $output['betterdocs_sidebar_term_item_counter_color_layout6'] ?>;
			background-color:<?php echo $output['betterdocs_sidebar_term_item_counter_back_color_layout6'] ?>;
			border-top-left-radius: <?php echo $output['betterdocs_sidebar_term_item_counter_border_radius_top_left_layout6'] ?>px;
			border-top-right-radius: <?php echo $output['betterdocs_sidebar_term_item_counter_border_radius_top_right_layout6'] ?>px;
			border-bottom-right-radius:<?php echo $output['betterdocs_sidebar_term_item_counter_border_radius_bottom_right_layout6'] ?>px;
			border-bottom-left-radius:<?php echo $output['betterdocs_sidebar_term_item_counter_border_radius_bottom_left_layout6'] ?>px;
			padding-top:<?php echo $output['betterdocs_sidebar_term_item_counter_padding_top_layout6'] ?>px;
			padding-right:<?php echo $output['betterdocs_sidebar_term_item_counter_padding_right_layout6'] ?>px;
			padding-bottom: <?php echo $output['betterdocs_sidebar_term_item_counter_padding_bottom_layout6'] ?>px;
			padding-left:<?php echo $output['betterdocs_sidebar_term_item_counter_padding_left_layout6'] ?>px;
			margin-top: <?php echo $output['betterdocs_sidebar_term_item_counter_margin_top_layout6'] ?>px;
			margin-right:<?php echo $output['betterdocs_sidebar_term_item_counter_margin_right_layout6'] ?>px;
			margin-bottom:<?php echo $output['betterdocs_sidebar_term_item_counter_margin_bottom_layout6'] ?>px;
			margin-left:<?php echo $output['betterdocs_sidebar_term_item_counter_margin_left_layout6'] ?>px;
			border-style:<?php echo $output['betterdocs_sidebar_term_item_counter_border_type_layout6'] ?>;
			border-width: <?php echo $output['betterdocs_sidebar_term_item_counter_border_width_layout6'] ?>px;
		}
		/** Doc Sidebar Layout 6(For Single Doc Layout 6) **/

        .betterdocs-live-search .betterdocs-searchform .search-submit {
            font-size: <?php echo $output['betterdocs_new_search_button_font_size'] ?>px;
            font-weight: <?php echo $output['betterdocs_new_search_button_font_weight'] ?>;
            text-transform: <?php echo $output['betterdocs_new_search_button_text_transform'] ?>;
            letter-spacing: <?php echo $output['betterdocs_new_search_button_letter_spacing'] ?>px;
            background-color: <?php echo $output['betterdocs_search_button_background_color'] ?>;
            color: <?php echo $output['betterdocs_search_button_text_color'] ?>;
            border-top-left-radius: <?php echo $output['betterdocs_search_button_borderr_left_top'] ?>px;
            border-top-right-radius: <?php echo $output['betterdocs_search_button_borderr_right_top'] ?>px;
            border-bottom-left-radius: <?php echo $output['betterdocs_search_button_borderr_left_bottom']?>px;
            border-bottom-right-radius: <?php echo $output['betterdocs_search_button_borderr_right_bottom'] ?>px;
            padding-top: <?php echo $output['betterdocs_search_button_padding_top'] ?>px;
            padding-left: <?php echo $output['betterdocs_search_button_padding_left'] ?>px;
            padding-right: <?php echo $output['betterdocs_search_button_padding_right'] ?>px;
            padding-bottom: <?php echo $output['betterdocs_search_button_padding_bottom'] ?>px;
        }
		.betterdocs-searchform .search-submit:focus{
			background-color: <?php echo $output['betterdocs_search_button_background_color'] ?>;
			color: <?php echo $output['betterdocs_search_button_text_color'] ?>;
		}
        .betterdocs-live-search .betterdocs-popular-search-keyword .popular-keyword{
            font-size: <?php echo $output['betterdocs_popular_search_font_size'] ?>px;
            background-color: <?php echo $output['betterdocs_popular_search_background_color'] ?>;
            color: <?php echo $output['betterdocs_popular_keyword_text_color'] ?>;
            padding-top: <?php echo $output['betterdocs_popular_search_padding_top'] ?>px;
            padding-bottom: <?php echo $output['betterdocs_popular_search_padding_bottom'] ?>px;
            padding-left: <?php echo $output['betterdocs_popular_search_padding_left'] ?>px;
            padding-right: <?php echo $output['betterdocs_popular_search_padding_right'] ?>px;
            margin-left: <?php echo $output['betterdocs_popular_search_keyword_margin_left'] ?>px;
            margin-bottom: <?php echo $output['betterdocs_popular_search_keyword_margin_bottom'] ?>px;
            margin-top: <?php echo $output['betterdocs_popular_search_keyword_margin_top'] ?>px;
            margin-right: <?php echo $output['betterdocs_popular_search_keyword_margin_right'] ?>px;
			border-style: <?php echo $output['betterdocs_popular_search_keyword_border'] ?>;
			border-color: <?php echo $output['betterdocs_popular_search_keyword_border_color']?>;
			border-top-width: <?php echo $output['betterdocs_popular_search_keyword_border_width_top']?>px;
			border-right-width: <?php echo $output['betterdocs_popular_search_keyword_border_width_right'] ?>px;
			border-bottom-width: <?php echo $output['betterdocs_popular_search_keyword_border_width_bottom'] ?>px;
			border-left-width: <?php echo $output['betterdocs_popular_search_keyword_border_width_left']?>px;
			border-top-left-radius: <?php echo $output['betterdocs_popular_keyword_border_radius_left_top'] ?>px;
			border-top-right-radius: <?php echo $output['betterdocs_popular_keyword_border_radius_right_top']?>px;
			border-bottom-left-radius: <?php echo $output['betterdocs_popular_keyword_border_radius_left_bottom'] ?>px;
			border-bottom-right-radius: <?php echo $output['betterdocs_popular_keyword_border_radius_right_bottom'] ?>px;
        }
        .betterdocs-popular-search-keyword {
            margin-top: <?php echo $output['betterdocs_popular_search_margin_top'] ?>px;
            margin-right: <?php echo $output['betterdocs_popular_search_margin_right']?>px;
            margin-bottom: <?php echo $output['betterdocs_popular_search_margin_bottom'] ?>px;
            margin-left: <?php echo $output['betterdocs_popular_search_margin_left'] ?>px;
        }
        .betterdocs-popular-search-keyword .popular-search-title {
            color: <?php echo $output['betterdocs_popular_search_title_text_color']?>;
            font-size: <?php echo $output['betterdocs_popular_search_title_font_size'] ?>px;
        }
        .betterdocs-searchform .betterdocs-search-category{
            font-size: <?php echo $output['betterdocs_category_select_font_size'] ?>px;
            font-weight: <?php echo $output['betterdocs_category_select_font_weight'] ?>;
            text-transform: <?php echo $output['betterdocs_category_select_text_transform'] ?>;
            color: <?php echo $output['betterdocs_category_select_text_color'] ?>;
        }
		.betterdocs-searchform .search-submit:hover{
  			background-color: <?php echo $output['betterdocs_search_button_background_color_hover'] ?>;
			color: <?php echo $output['betterdocs_search_button_text_color'] ?>;
		}

		/** FAQ MKB Layout 1 CSS **/
		.betterdocs-faq-section-title.faq-mkb{
			<?php echo betterdocs_dimension_generator_pro('betterdocs_faq_title_margin_mkb_layout_1', 'margin'); ?>
			color: <?php echo $output['betterdocs_faq_title_color_mkb_layout_1'] ?>;
			font-size:<?php echo $output['betterdocs_faq_title_font_size_mkb_layout_1'] ?>px;
		}

		.betterdocs-faq-main-wrapper-layout-1.faq-mkb .betterdocs-faq-title h2{
			color: <?php echo $output['betterdocs_faq_category_title_color_mkb_layout_1'] ?>;
			font-size: <?php echo $output['betterdocs_faq_category_name_font_size_mkb_layout_1'] ?>px;
			<?php echo betterdocs_dimension_generator_pro('betterdocs_faq_category_name_padding_mkb_layout_1', 'padding'); ?>
		}

		.betterdocs-faq-main-wrapper-layout-1.faq-mkb .betterdocs-faq-list > li .betterdocs-faq-group .betterdocs-faq-post .betterdocs-faq-post-name{
			color: <?php echo $output['betterdocs_faq_list_color_mkb_layout_1'] ?>;
			font-size: <?php echo $output['betterdocs_faq_list_font_size_mkb_layout_1'] ?>px;
		}

		.betterdocs-faq-main-wrapper-layout-1.faq-mkb .betterdocs-faq-list > li .betterdocs-faq-group .betterdocs-faq-post{
			background-color: <?php echo $output['betterdocs_faq_list_background_color_mkb_layout_1'] ?>;
		}

		.betterdocs-faq-main-wrapper-layout-1.faq-mkb .betterdocs-faq-group .betterdocs-faq-main-content{
			background-color: <?php echo $output['betterdocs_faq_list_content_background_color_mkb_layout_1'] ?>;
			font-size: <?php echo $output['betterdocs_faq_list_content_font_size_mkb_layout_1'] ?>px;
			color: <?php echo $output['betterdocs_faq_list_content_color_mkb_layout_1'] ?>;
		}

		.betterdocs-faq-main-wrapper-layout-1.faq-mkb .betterdocs-faq-list > li .betterdocs-faq-group .betterdocs-faq-post{
			<?php echo betterdocs_dimension_generator_pro('betterdocs_faq_list_padding_mkb_layout_1', 'padding'); ?>
		}

		/** FAQ MKB Layout 2 CSS **/
		.betterdocs-faq-main-wrapper-layout-2.faq-mkb .betterdocs-faq-title h2{
			color: <?php echo $output['betterdocs_faq_category_title_color_mkb_layout_2'] ?>;
			font-size: <?php echo $output['betterdocs_faq_category_name_font_size_mkb_layout_2'] ?>px;
			<?php echo betterdocs_dimension_generator_pro('betterdocs_faq_category_name_padding_mkb_layout_2', 'padding'); ?>
		}

		.betterdocs-faq-main-wrapper-layout-2.faq-mkb .betterdocs-faq-list > li .betterdocs-faq-group .betterdocs-faq-post-layout-2 .betterdocs-faq-post-name{
			color: <?php echo $output['betterdocs_faq_list_color_mkb_layout_2'] ?>;
			font-size: <?php echo $output['betterdocs_faq_list_font_size_mkb_layout_2'] ?>px;
		}

		.betterdocs-faq-main-wrapper-layout-2.faq-mkb .betterdocs-faq-list-layout-2 > li .betterdocs-faq-group-layout-2 .betterdocs-faq-post-layout-2{
			background-color: <?php echo $output['betterdocs_faq_list_background_color_mkb_layout_2']; ?>;
			<?php echo betterdocs_dimension_generator_pro('betterdocs_faq_list_padding_mkb_layout_2', 'padding'); ?>
		}

		.betterdocs-faq-main-wrapper-layout-2.faq-mkb .betterdocs-faq-list-layout-2 > li .betterdocs-faq-group-layout-2 .betterdocs-faq-main-content-layout-2{
			background-color: <?php echo $output['betterdocs_faq_list_content_background_color_mkb_layout_2']?>;
			font-size: <?php echo $output['betterdocs_faq_list_content_font_size_mkb_layout_2'] ?>px;
			color: <?php echo $output['betterdocs_faq_list_content_color_mkb_layout_2'] ?>;
		}

	</style>
    <?php
}
add_action( 'wp_head', 'betterdocs_customize_css_pro');
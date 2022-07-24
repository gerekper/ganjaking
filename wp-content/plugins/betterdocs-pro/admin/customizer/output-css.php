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

			.betterdocs-categories-wrap .betterdocs-tab-list a{
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

			.betterdocs-categories-wrap .betterdocs-tab-list a:hover{
				background-color: <?php echo $output['betterdocs_mkb_list_bg_hover_color'] ?>;
			}
			.betterdocs-categories-wrap .betterdocs-tab-list a{
				color: <?php echo $output['betterdocs_mkb_tab_list_font_color']?>;
			}
			.betterdocs-categories-wrap .betterdocs-tab-list a.active {
				background-color: <?php echo $output['betterdocs_mkb_tab_list_back_color_active'] ?>;
				color: <?php echo $output['betterdocs_mkb_tab_list_font_color_active'] ?>;
			}
			.betterdocs-categories-wrap .betterdocs-tab-list a.active:focus {
				background-color: <?php echo $output['betterdocs_mkb_tab_list_back_color_active'] ?>;
				color: <?php echo $output['betterdocs_mkb_tab_list_font_color_active'] ?>;
			}
			.tabs-content .betterdocs-tab-content .betterdocs-tab-categories {
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
			.tabs-content .betterdocs-tab-categories .docs-single-cat-wrap {
				background-color: <?php echo $output['betterdocs_mkb_column_bg_color2'] ?>;
			}

			.tabs-content .betterdocs-tab-categories .docs-single-cat-wrap:hover {
				background-color: <?php echo $output['betterdocs_mkb_column_hover_bg_color'] ?>;
			}

			.tabs-content .betterdocs-tab-content .docs-single-cat-wrap .docs-cat-title-inner .docs-cat-title .docs-cat-heading {
				font-size: <?php echo $output['betterdocs_mkb_cat_title_font_size'] ?>;
			}

			.tabs-content .betterdocs-tab-content .docs-single-cat-wrap .docs-cat-title .docs-cat-heading {
				color: <?php echo $output['betterdocs_mkb_cat_title_color'] ?>;
			}

			.tabs-content .betterdocs-tab-content .docs-single-cat-wrap .docs-cat-title .docs-cat-heading:hover {
				color: <?php echo $output['betterdocs_mkb_cat_title_hover_color'] ?>;
			}
			.tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap .docs-item-container ul li a{
				color: <?php echo $output['betterdocs_mkb_column_list_color']?>;
				font-size: <?php echo $output['betterdocs_mkb_column_list_font_size']?>px;
			}
			.tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap .docs-item-container ul li {
				margin-top: <?php echo $output['betterdocs_mkb_column_list_margin_top'] ?>px;
				margin-right: <?php echo $output['betterdocs_mkb_column_list_margin_right'] ?>px;
				margin-bottom: <?php echo $output['betterdocs_mkb_list_margin_bottom']?>px;
				margin-left: <?php echo $output['betterdocs_mkb_list_margin_left'] ?>px;
			}
			.tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap .docs-item-container ul li a:hover{
				color: <?php echo $output['betterdocs_mkb_column_list_hover_color'] ?>;
			}

			.tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap .docs-item-container .docs-cat-link-btn {
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
			.tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap .docs-item-container .docs-cat-link-btn:hover {
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
		.betterdocs-article-reactions .betterdocs-article-reactions-heading h5 {
			color: <?php echo $output['betterdocs_post_reactions_text_color'] ?>;
		}
		.betterdocs-article-reaction-links li a {
			background-color: <?php echo $output['betterdocs_post_reactions_icon_color'] ?>;
		}
		.betterdocs-article-reaction-links li a:hover {
			background-color: <?php echo $output['betterdocs_post_reactions_icon_hover_bg_color'] ?>;
		}
		.betterdocs-article-reaction-links li a svg path {
			fill: <?php echo $output['betterdocs_post_reactions_icon_svg_color'] ?>;
		}
		.betterdocs-article-reaction-links li a:hover svg path {
			fill: <?php echo $output['betterdocs_post_reactions_icon_hover_svg_color'] ?>;
		}
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
	</style>
    <?php
}
add_action( 'wp_head', 'betterdocs_customize_css_pro');
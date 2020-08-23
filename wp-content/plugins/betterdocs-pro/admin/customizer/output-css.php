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
	<style type="text/css">
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
			.betterdocs-archive-mkb .betterdocs-categories-wrap.layout-masonry .docs-single-cat-wrap {
				margin-bottom: <?php echo $output['betterdocs_mkb_column_space'] ?>px;
			}
			.betterdocs-archive-mkb .betterdocs-categories-wrap.layout-flex .docs-single-cat-wrap {
				margin: <?php echo $output['betterdocs_mkb_column_space'] ?>px; 
			}
			.betterdocs-archive-mkb .betterdocs-categories-wrap .docs-single-cat-wrap .docs-cat-title-wrap { 
				padding-top: <?php echo $output['betterdocs_mkb_column_padding_top'] ?>px; 
			}
			.betterdocs-archive-mkb .betterdocs-categories-wrap .docs-single-cat-wrap .docs-cat-title-wrap, 
			.betterdocs-archive-mkb .docs-item-container { 
				padding-right: <?php echo $output['betterdocs_mkb_column_padding_right'] ?>px;
				padding-left: <?php echo $output['betterdocs_mkb_column_padding_left'] ?>px;  
			}
			.betterdocs-archive-mkb .docs-item-container { 
				padding-bottom: <?php echo $output['betterdocs_mkb_column_padding_right'] ?>px; 
			}
			.betterdocs-archive-mkb .betterdocs-categories-wrap.betterdocs-category-box .docs-single-cat-wrap,
			.betterdocs-archive-mkb .betterdocs-categories-wrap .docs-single-cat-wrap.docs-cat-list-2-box {
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
			.betterdocs-archive-mkb .betterdocs-categories-wrap .docs-single-cat-wrap,
			.betterdocs-archive-mkb .betterdocs-categories-wrap .docs-single-cat-wrap .docs-cat-title-wrap {
				<?php if(!empty($output['betterdocs_mkb_column_borderr_topleft'])) { ?>
				border-top-left-radius: <?php echo $output['betterdocs_mkb_column_borderr_topleft'] ?>px;
				<?php } ?>
				<?php if(!empty($output['betterdocs_mkb_column_borderr_topright'])) { ?>
				border-top-right-radius: <?php echo $output['betterdocs_mkb_column_borderr_topright'] ?>px;
				<?php } ?>
			}
			.betterdocs-archive-mkb .betterdocs-category-box.ash-bg .docs-single-cat-wrap {
				<?php if(!empty($output['betterdocs_mkb_column_bg_color2'])) { ?>
				background-color: <?php echo $output['betterdocs_mkb_column_bg_color2'] ?>;
				<?php } ?>
			}
			.betterdocs-archive-mkb .betterdocs-category-box .docs-single-cat-wrap:hover,
			.betterdocs-archive-mkb .betterdocs-categories-wrap.white-bg .docs-single-cat-wrap.docs-cat-list-2-box:hover {
				<?php if(!empty($output['betterdocs_mkb_column_hover_bg_color'])) { ?>
				background-color: <?php echo $output['betterdocs_mkb_column_hover_bg_color'] ?>;
				<?php } ?>
			}
			.betterdocs-archive-mkb .betterdocs-category-box .docs-single-cat-wrap img {
				<?php if(!empty($output['betterdocs_mkb_column_content_space_image'])) { ?>
				margin-bottom: <?php echo $output['betterdocs_mkb_column_content_space_image'] ?>px;
				<?php } ?>
			}
			.betterdocs-archive-mkb .betterdocs-category-box .docs-single-cat-wrap .docs-cat-title {
				<?php if(!empty($output['betterdocs_mkb_column_content_space_title'])) { ?>
				margin-bottom: <?php echo $output['betterdocs_mkb_column_content_space_title'] ?>px;
				<?php } ?>
			}
			.betterdocs-archive-mkb .betterdocs-category-box .docs-single-cat-wrap p {
				<?php if(!empty($output['betterdocs_mkb_column_content_space_desc'])) { ?>
				margin-bottom: <?php echo $output['betterdocs_mkb_column_content_space_desc'] ?>px;
				<?php } ?>
			}
			.betterdocs-archive-mkb .betterdocs-category-box .docs-single-cat-wrap span {
				<?php if(!empty($output['betterdocs_mkb_column_content_space_counter'])) { ?>
				margin-bottom: <?php echo $output['betterdocs_mkb_column_content_space_counter'] ?>px;
				<?php } ?>
			}
			.betterdocs-archive-mkb .betterdocs-category-box .docs-single-cat-wrap img { 
				height: <?php echo $output['betterdocs_mkb_cat_icon_size'] ?>px; 
			}
			.betterdocs-archive-mkb .docs-cat-title-inner h3,
			.betterdocs-archive-mkb .betterdocs-category-box .docs-single-cat-wrap .docs-cat-title,
			.betterdocs-archive-mkb .docs-cat-list-2-box .docs-cat-title {
				font-size: <?php echo $output['betterdocs_mkb_cat_title_font_size'] ?>px;
			}
			.betterdocs-archive-mkb .betterdocs-category-box .docs-single-cat-wrap .docs-cat-title,
			.betterdocs-archive-mkb .docs-cat-list-2 .docs-cat-title {
				color: <?php echo $output['betterdocs_mkb_cat_title_color'] ?>;
			}
			.betterdocs-archive-mkb .betterdocs-categories-wrap.betterdocs-category-box .docs-single-cat-wrap span,
			.betterdocs-archive-mkb .docs-cat-list-2-box .title-count span {
				color: <?php echo $output['betterdocs_mkb_item_count_color'] ?>; 
				font-size: <?php echo $output['betterdocs_mkb_item_count_font_size'] ?>px;
			}
			<?php if ( $output['betterdocs_mkb_cat_title_hover_color'] ) { ?>
			.betterdocs-archive-mkb .docs-cat-title-inner h3:hover,
			.betterdocs-archive-mkb .betterdocs-category-box .docs-single-cat-wrap .docs-cat-title:hover,
			.betterdocs-archive-mkb .docs-cat-list-2 .docs-cat-title:hover {
				color: <?php echo $output['betterdocs_mkb_cat_title_hover_color'] ?>;
			}
			<?php } ?>
		
		<?php } ?>

		.docs-cat-list-2-items .docs-cat-title {
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
		.docs-cat-list-2-items .docs-cat-link-btn {
			margin-left: 0;
			margin-right: 0;
		}
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
		.betterdocs-article-reaction-links li a:hover svg {
			fill: <?php echo $output['betterdocs_post_reactions_icon_color'] ?>;
		}
	</style>
    <?php
}
add_action( 'wp_head', 'betterdocs_customize_css_pro');
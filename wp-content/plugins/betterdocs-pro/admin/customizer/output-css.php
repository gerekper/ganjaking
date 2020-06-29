<?php
/**
 * BetterDocs Theme Customizer outout for layout settings
 *
 * @package BetterDocs
 */


function betterdocs_customize_css_pro() {
	$defaults = betterdocs_generate_defaults_pro();
	$layout_select = get_theme_mod('betterdocs_docs_layout_select', 'layout-1');
    ?>
	<style type="text/css">
		.docs-cat-list-2-items .docs-cat-title {
			font-size: <?php echo $defaults['betterdocs_doc_page_cat_title_font_size2'] ?>px;
		}
		.betterdocs-category-box.pro-layout-3 .docs-single-cat-wrap img,
		.docs-cat-list-2-box img {
			<?php if(!empty($defaults['betterdocs_doc_page_cat_icon_size_l_3_4'])) { ?>
			height: <?php echo $defaults['betterdocs_doc_page_cat_icon_size_l_3_4'] ?>px;
			<?php } ?>
			width: auto;
			margin-bottom: 0px !important;
		}
		<?php if($layout_select === 'layout-4') { ?>

		.betterdocs-archive-wrap.cat-layout-4 {
			margin-top: -<?php echo $defaults['betterdocs_doc_page_content_overlap'] ?>px;
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
			<?php if(!empty(get_theme_mod('betterdocs_doc_single_content_area_bg_color'))) { ?>
			background-color: <?php echo get_theme_mod('betterdocs_doc_single_content_area_bg_color') ?>;
			<?php } else {?>
			background-color: <?php echo $defaults['betterdocs_doc_single_content_area_bg_color'] ?>;		
			<?php } ?>
		}
		.betterdocs-single-wraper .betterdocs-content-full {
			padding-right: <?php echo $defaults['betterdocs_doc_single_content_area_padding_right'] ?>px;
			padding-left: <?php echo $defaults['betterdocs_doc_single_content_area_padding_left'] ?>px;
		}
		<?php } ?>
		.betterdocs-article-reactions .betterdocs-article-reactions-heading h5 {
			color: <?php echo $defaults['betterdocs_post_reactions_text_color'] ?>;
		}
		.betterdocs-article-reaction-links li a:hover svg {
			fill: <?php echo $defaults['betterdocs_post_reactions_icon_color'] ?>;
		}
	</style>
    <?php
}
add_action( 'wp_head', 'betterdocs_customize_css_pro');
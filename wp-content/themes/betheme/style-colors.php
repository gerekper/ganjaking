<?php
/**
 * @package Betheme
 * @author Muffin group
 * @link https://muffingroup.com
 */

if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly
}
?>

/**
 * Backgrounds *****
 */

#Header_wrapper, #Intro {
	background-color: <?php echo esc_attr(mfn_opts_get('background-header', '#000119')) ?>;
}
#Subheader {
	<?php
		$subheaderB = mfn_opts_get('background-subheader', '#F7F7F7');
		$subheaderA = mfn_opts_get('subheader-transparent', 100);
		$subheaderA = $subheaderA / 100;
		$subheaderA = str_replace(',', '.', $subheaderA);
	?>
	background-color: <?php echo esc_attr(mfn_hex2rgba($subheaderB, $subheaderA)); ?>;
}
.header-classic #Action_bar, .header-fixed #Action_bar, .header-plain #Action_bar, .header-split #Action_bar, .header-stack #Action_bar {
	background-color: <?php echo esc_attr(mfn_opts_get('background-action-bar', '#292b33')) ?>;
}

#Sliding-top {
	background-color: <?php echo esc_attr(mfn_opts_get('background-sliding-top', '#545454')) ?>;
}
#Sliding-top a.sliding-top-control {
	border-right-color: <?php echo esc_attr(mfn_opts_get('background-sliding-top', '#545454')) ?>;
}
#Sliding-top.st-center a.sliding-top-control,
#Sliding-top.st-left a.sliding-top-control {
	border-top-color: <?php echo esc_attr(mfn_opts_get('background-sliding-top', '#545454')) ?>;
}

#Footer {
	background-color: <?php echo esc_attr(mfn_opts_get('background-footer', '#545454')) ?>;
}

/**
 * Colors *****
 */

/* Content font */

body, ul.timeline_items, .icon_box a .desc, .icon_box a:hover .desc, .feature_list ul li a, .list_item a, .list_item a:hover,
.widget_recent_entries ul li a, .flat_box a, .flat_box a:hover, .story_box .desc, .content_slider.carousel  ul li a .title,
.content_slider.flat.description ul li .desc, .content_slider.flat.description ul li a .desc, .post-nav.minimal a i {
	color: <?php echo esc_attr(mfn_opts_get('color-text', '#626262')) ?>;
}
.post-nav.minimal a svg {
	fill: <?php echo esc_attr(mfn_opts_get('color-text', '#626262')) ?>;
}

/* Theme color */

.themecolor, .opening_hours .opening_hours_wrapper li span, .fancy_heading_icon .icon_top,
.fancy_heading_arrows .icon-right-dir, .fancy_heading_arrows .icon-left-dir, .fancy_heading_line .title,
.button-love a.mfn-love, .format-link .post-title .icon-link, .pager-single > span, .pager-single a:hover,
.widget_meta ul, .widget_pages ul, .widget_rss ul, .widget_mfn_recent_comments ul li:after, .widget_archive ul,
.widget_recent_comments ul li:after, .widget_nav_menu ul, .woocommerce ul.products li.product .price, .shop_slider .shop_slider_ul li .item_wrapper .price,
.woocommerce-page ul.products li.product .price, .widget_price_filter .price_label .from, .widget_price_filter .price_label .to,
.woocommerce ul.product_list_widget li .quantity .amount, .woocommerce .product div.entry-summary .price, .woocommerce .star-rating span,
#Error_404 .error_pic i, .style-simple #Filters .filters_wrapper ul li a:hover, .style-simple #Filters .filters_wrapper ul li.current-cat a,
.style-simple .quick_fact .title {
	color: <?php echo esc_attr(mfn_opts_get('color-theme', '#0095eb')) ?>;
}

/* Theme background */

.themebg,#comments .commentlist > li .reply a.comment-reply-link,#Filters .filters_wrapper ul li a:hover,#Filters .filters_wrapper ul li.current-cat a,.fixed-nav .arrow,
.offer_thumb .slider_pagination a:before,.offer_thumb .slider_pagination a.selected:after,.pager .pages a:hover,.pager .pages a.active,.pager .pages span.page-numbers.current,.pager-single span:after,
.portfolio_group.exposure .portfolio-item .desc-inner .line,.Recent_posts ul li .desc:after,.Recent_posts ul li .photo .c,
.slider_pagination a.selected,.slider_pagination .slick-active a,.slider_pagination a.selected:after,.slider_pagination .slick-active a:after,
.testimonials_slider .slider_images,.testimonials_slider .slider_images a:after,.testimonials_slider .slider_images:before,#Top_bar a#header_cart span,
.widget_categories ul,.widget_mfn_menu ul li a:hover,.widget_mfn_menu ul li.current-menu-item:not(.current-menu-ancestor) > a,.widget_mfn_menu ul li.current_page_item:not(.current_page_ancestor) > a,.widget_product_categories ul,.widget_recent_entries ul li:after,
.woocommerce-account table.my_account_orders .order-number a,.woocommerce-MyAccount-navigation ul li.is-active a,
.style-simple .accordion .question:after,.style-simple .faq .question:after,.style-simple .icon_box .desc_wrapper .title:before,.style-simple #Filters .filters_wrapper ul li a:after,.style-simple .article_box .desc_wrapper p:after,.style-simple .sliding_box .desc_wrapper:after,.style-simple .trailer_box:hover .desc,
.tp-bullets.simplebullets.round .bullet.selected,.tp-bullets.simplebullets.round .bullet.selected:after,.tparrows.default,.tp-bullets.tp-thumbs .bullet.selected:after{
	background-color: <?php echo esc_attr(mfn_opts_get('color-theme', '#0095eb')) ?>;
}

.Latest_news ul li .photo, .Recent_posts.blog_news ul li .photo, .style-simple .opening_hours .opening_hours_wrapper li label,
.style-simple .timeline_items li:hover h3, .style-simple .timeline_items li:nth-child(even):hover h3,
.style-simple .timeline_items li:hover .desc, .style-simple .timeline_items li:nth-child(even):hover,
.style-simple .offer_thumb .slider_pagination a.selected {
	border-color: <?php echo esc_attr(mfn_opts_get('color-theme', '#0095eb')) ?>;
}

/* Links color */

a {
	color: <?php echo esc_attr(mfn_opts_get('color-a', '#0095eb')) ?>;
}

a:hover {
	color: <?php echo esc_attr(mfn_opts_get('color-a-hover', '#2275ac')) ?>;
}

/* Selections */

<?php $selection_color = mfn_opts_get('color-selection', '#0095eb'); ?>
*::-moz-selection {
	background-color: <?php echo esc_attr($selection_color); ?>;
	color: <?php echo esc_attr(mfn_brightness($selection_color, 169, true)); ?>;
}
*::selection {
	background-color: <?php echo esc_attr($selection_color); ?>;
	color: <?php echo esc_attr(mfn_brightness($selection_color, 169, true)); ?>;
}

/* Grey */

.blockquote p.author span, .counter .desc_wrapper .title, .article_box .desc_wrapper p, .team .desc_wrapper p.subtitle,
.pricing-box .plan-header p.subtitle, .pricing-box .plan-header .price sup.period, .chart_box p, .fancy_heading .inside,
.fancy_heading_line .slogan, .post-meta, .post-meta a, .post-footer, .post-footer a span.label, .pager .pages a, .button-love a .label,
.pager-single a, #comments .commentlist > li .comment-author .says, .fixed-nav .desc .date, .filters_buttons li.label, .Recent_posts ul li a .desc .date,
.widget_recent_entries ul li .post-date, .tp_recent_tweets .twitter_time, .widget_price_filter .price_label, .shop-filters .woocommerce-result-count,
.woocommerce ul.product_list_widget li .quantity, .widget_shopping_cart ul.product_list_widget li dl, .product_meta .posted_in,
.woocommerce .shop_table .product-name .variation > dd, .shipping-calculator-button:after,  .shop_slider .shop_slider_ul li .item_wrapper .price del,
.testimonials_slider .testimonials_slider_ul li .author span, .testimonials_slider .testimonials_slider_ul li .author span a, .Latest_news ul li .desc_footer,
.share-simple-wrapper .icons a {
	color: <?php echo esc_attr(mfn_opts_get('color-note', '#a8a8a8')) ?>;
}

/* Headings font */

h1, h1 a, h1 a:hover, .text-logo #logo { color: <?php echo esc_attr(mfn_opts_get('color-h1', '#161922')) ?>; }
h2, h2 a, h2 a:hover { color: <?php echo esc_attr(mfn_opts_get('color-h2', '#161922')) ?>; }
h3, h3 a, h3 a:hover { color: <?php echo esc_attr(mfn_opts_get('color-h3', '#161922')) ?>; }
h4, h4 a, h4 a:hover, .style-simple .sliding_box .desc_wrapper h4 { color: <?php echo esc_attr(mfn_opts_get('color-h4', '#161922')) ?>; }
h5, h5 a, h5 a:hover { color: <?php echo esc_attr(mfn_opts_get('color-h5', '#161922')) ?>; }
h6, h6 a, h6 a:hover,
a.content_link .title { color: <?php echo esc_attr(mfn_opts_get('color-h6', '#161922')) ?>; }

/* Highlight */

.dropcap, .highlight:not(.highlight_image) {
	background-color: <?php echo esc_attr(mfn_opts_get('background-highlight', '#0095eb')) ?>;
}

/* Buttons */

a.button, a.tp-button {
	background-color: <?php echo esc_attr(mfn_opts_get('background-button', '#f7f7f7')) ?>;
	color: <?php echo esc_attr(mfn_opts_get('color-button', '#747474')) ?>;
}

.button-stroke a.button, .button-stroke a.button .button_icon i, .button-stroke a.tp-button {
	border-color: <?php echo esc_attr(mfn_opts_get('background-button', '#f7f7f7')) ?>;
	color: <?php echo esc_attr(mfn_opts_get('color-button', '#747474')) ?>;
}
.button-stroke a:hover.button, .button-stroke a:hover.tp-button {
	background-color: <?php echo esc_attr(mfn_opts_get('background-button', '#f7f7f7')) ?> !important;
	color: #fff;
}

/* .button_theme */

a.button_theme, a.tp-button.button_theme,
button, input[type="submit"], input[type="reset"], input[type="button"] {
	background-color: <?php echo esc_attr(mfn_opts_get('color-theme', '#0095eb')) ?>;
	color: <?php echo esc_attr(mfn_opts_get('color-button-theme', '#ffffff')) ?>;
}

.button-stroke a.button.button_theme,
.button-stroke a.button.button_theme .button_icon i, .button-stroke a.tp-button.button_theme,
.button-stroke button, .button-stroke input[type="submit"], .button-stroke input[type="reset"], .button-stroke input[type="button"] {
	border-color: <?php echo esc_attr(mfn_opts_get('color-theme', '#0095eb')) ?>;
	color: <?php echo esc_attr(mfn_opts_get('color-theme', '#0095eb')) ?> !important;
}
.button-stroke a.button.button_theme:hover, .button-stroke a.tp-button.button_theme:hover,
.button-stroke button:hover, .button-stroke input[type="submit"]:hover, .button-stroke input[type="reset"]:hover, .button-stroke input[type="button"]:hover {
	background-color: <?php echo esc_attr(mfn_opts_get('color-theme', '#0095eb')) ?> !important;
	color: <?php echo esc_attr(mfn_opts_get('color-button-theme', '#ffffff')) ?> !important;
}

/* Fancy Link */

a.mfn-link {
	color: <?php echo esc_attr(mfn_opts_get('color-fancy-link', '#656B6F')) ?>;
}
a.mfn-link-2 span, a:hover.mfn-link-2 span:before, a.hover.mfn-link-2 span:before, a.mfn-link-5 span, a.mfn-link-8:after, a.mfn-link-8:before {
	background: <?php echo esc_attr(mfn_opts_get('background-fancy-link', '#2195de')) ?>;
}
a:hover.mfn-link {
	color: <?php echo esc_attr(mfn_opts_get('color-fancy-link-hover', '#0095eb')) ?>;
}
a.mfn-link-2 span:before, a:hover.mfn-link-4:before, a:hover.mfn-link-4:after, a.hover.mfn-link-4:before, a.hover.mfn-link-4:after, a.mfn-link-5:before, a.mfn-link-7:after, a.mfn-link-7:before {
	background: <?php echo esc_attr(mfn_opts_get('background-fancy-link-hover', '#2275ac')) ?>;
}
a.mfn-link-6:before {
	border-bottom-color: <?php echo esc_attr(mfn_opts_get('background-fancy-link-hover', '#2275ac')) ?>;
}

/* Shop buttons */

.woocommerce #respond input#submit,
.woocommerce a.button,
.woocommerce button.button,
.woocommerce input.button,
.woocommerce #respond input#submit:hover,
.woocommerce a.button:hover,
.woocommerce button.button:hover,
.woocommerce input.button:hover{
	background-color: <?php echo esc_attr(mfn_opts_get('color-theme', '#0095eb')) ?>;
	color: #fff;
}

.woocommerce #respond input#submit.alt,
.woocommerce a.button.alt,
.woocommerce button.button.alt,
.woocommerce input.button.alt,
.woocommerce #respond input#submit.alt:hover,
.woocommerce a.button.alt:hover,
.woocommerce button.button.alt:hover,
.woocommerce input.button.alt:hover{
	background-color: <?php echo esc_attr(mfn_opts_get('color-theme', '#0095eb')) ?>;
	color: #fff;
}

.woocommerce #respond input#submit.disabled,
.woocommerce #respond input#submit:disabled,
.woocommerce #respond input#submit[disabled]:disabled,
.woocommerce a.button.disabled,
.woocommerce a.button:disabled,
.woocommerce a.button[disabled]:disabled,
.woocommerce button.button.disabled,
.woocommerce button.button:disabled,
.woocommerce button.button[disabled]:disabled,
.woocommerce input.button.disabled,
.woocommerce input.button:disabled,
.woocommerce input.button[disabled]:disabled{
	background-color: <?php echo esc_attr(mfn_opts_get('color-theme', '#0095eb')) ?>;
	color: #fff;
}

.woocommerce #respond input#submit.disabled:hover,
.woocommerce #respond input#submit:disabled:hover,
.woocommerce #respond input#submit[disabled]:disabled:hover,
.woocommerce a.button.disabled:hover,
.woocommerce a.button:disabled:hover,
.woocommerce a.button[disabled]:disabled:hover,
.woocommerce button.button.disabled:hover,
.woocommerce button.button:disabled:hover,
.woocommerce button.button[disabled]:disabled:hover,
.woocommerce input.button.disabled:hover,
.woocommerce input.button:disabled:hover,
.woocommerce input.button[disabled]:disabled:hover{
	background-color: <?php echo esc_attr(mfn_opts_get('color-theme', '#0095eb')) ?>;
	color: #fff;
}

.button-stroke.woocommerce-page #respond input#submit,
.button-stroke.woocommerce-page a.button,
.button-stroke.woocommerce-page button.button,
.button-stroke.woocommerce-page input.button{
	border: 2px solid <?php echo esc_attr(mfn_opts_get('color-theme', '#0095eb')) ?> !important;
	color: <?php echo esc_attr(mfn_opts_get('color-theme', '#0095eb')) ?> !important;
}

.button-stroke.woocommerce-page #respond input#submit:hover,
.button-stroke.woocommerce-page a.button:hover,
.button-stroke.woocommerce-page button.button:hover,
.button-stroke.woocommerce-page input.button:hover{
	background-color: <?php echo esc_attr(mfn_opts_get('color-theme', '#0095eb')) ?> !important;
	color: #fff !important;
}

/* Lists */

.column_column ul, .column_column ol, .the_content_wrapper ul, .the_content_wrapper ol {
	color: <?php echo esc_attr(mfn_opts_get('color-list', '#737E86')) ?>;
}

/* Dividers */

hr.hr_color, .hr_color hr, .hr_dots span {
	color: <?php echo esc_attr(mfn_opts_get('color-hr', '#0095eb')) ?>;
	background: <?php echo esc_attr(mfn_opts_get('color-hr', '#0095eb')) ?>;
}
.hr_zigzag i {
	color: <?php echo esc_attr(mfn_opts_get('color-hr', '#0095eb')) ?>;
}

/* Highlight section */

.highlight-left:after,
.highlight-right:after {
	background: <?php echo esc_attr(mfn_opts_get('background-highlight-section', '#0095eb')) ?>;
}
@media only screen and (max-width: 767px) {
	.highlight-left .wrap:first-child,
	.highlight-right .wrap:last-child {
		background: <?php echo esc_attr(mfn_opts_get('background-highlight-section', '#0095eb')) ?>;
	}
}

/**
 * Header *****
 */

<?php if (mfn_opts_get('border-top-bar')): ?>
#Top_bar {
	border-bottom-color: <?php echo esc_attr(mfn_opts_get('border-top-bar')) ?>;
}
<?php endif; ?>

#Header .top_bar_left, .header-classic #Top_bar, .header-plain #Top_bar, .header-stack #Top_bar, .header-split #Top_bar,
.header-fixed #Top_bar, .header-below #Top_bar, #Header_creative, #Top_bar #menu, .sticky-tb-color #Top_bar.is-sticky {
	background-color: <?php echo esc_attr(mfn_opts_get('background-top-left', '#ffffff')) ?>;
}
#Top_bar .wpml-languages a.active, #Top_bar .wpml-languages ul.wpml-lang-dropdown {
	background-color: <?php echo esc_attr(mfn_opts_get('background-top-left', '#ffffff')) ?>;
}

#Top_bar .top_bar_right:before {
	background-color: <?php echo esc_attr(mfn_opts_get('background-top-middle', '#e3e3e3')) ?>;
}
#Header .top_bar_right {
	background-color: <?php echo esc_attr(mfn_opts_get('background-top-right', '#f5f5f5')) ?>;
}
#Top_bar .top_bar_right a:not(.action_button) {
	color: <?php echo esc_attr(mfn_opts_get('color-top-right-a', '#444444')) ?>;
}

a.action_button{
	background-color: <?php echo esc_attr(mfn_opts_get('background-action-button', '#f7f7f7')) ?>;
	color: <?php echo esc_attr(mfn_opts_get('color-action-button', '#747474')) ?>;
}
.button-stroke a.action_button{
	border-color: <?php echo esc_attr(mfn_opts_get('background-action-button', '#f7f7f7')) ?>;
}
.button-stroke a.action_button:hover{
	background-color: <?php echo esc_attr(mfn_opts_get('background-action-button', '#f7f7f7')) ?>!important;
}

#Top_bar .menu > li > a,
#Top_bar #menu ul li.submenu .menu-toggle {
	color: <?php echo esc_attr(mfn_opts_get('color-menu-a', '#444444')) ?>;
}
#Top_bar .menu > li.current-menu-item > a,
#Top_bar .menu > li.current_page_item > a,
#Top_bar .menu > li.current-menu-parent > a,
#Top_bar .menu > li.current-page-parent > a,
#Top_bar .menu > li.current-menu-ancestor > a,
#Top_bar .menu > li.current-page-ancestor > a,
#Top_bar .menu > li.current_page_ancestor > a,
#Top_bar .menu > li.hover > a {
	color: <?php echo esc_attr(mfn_opts_get('color-menu-a-active', '#0095eb')) ?>;
}
#Top_bar .menu > li a:after {
	background: <?php echo esc_attr(mfn_opts_get('color-menu-a-active', '#0095eb')) ?>;
}

.menuo-arrows #Top_bar .menu > li.submenu > a > span:not(.description)::after {
	border-top-color: <?php echo esc_attr(mfn_opts_get('color-menu-a', '#444444')) ?>;
}
#Top_bar .menu > li.current-menu-item.submenu > a > span:not(.description)::after,
#Top_bar .menu > li.current_page_item.submenu > a > span:not(.description)::after,
#Top_bar .menu > li.current-menu-parent.submenu > a > span:not(.description)::after,
#Top_bar .menu > li.current-page-parent.submenu > a > span:not(.description)::after,
#Top_bar .menu > li.current-menu-ancestor.submenu > a > span:not(.description)::after,
#Top_bar .menu > li.current-page-ancestor.submenu > a > span:not(.description)::after,
#Top_bar .menu > li.current_page_ancestor.submenu > a > span:not(.description)::after,
#Top_bar .menu > li.hover.submenu > a > span:not(.description)::after {
	border-top-color: <?php echo esc_attr(mfn_opts_get('color-menu-a-active', '#0095eb')) ?>;
}

.menu-highlight #Top_bar #menu > ul > li.current-menu-item > a,
.menu-highlight #Top_bar #menu > ul > li.current_page_item > a,
.menu-highlight #Top_bar #menu > ul > li.current-menu-parent > a,
.menu-highlight #Top_bar #menu > ul > li.current-page-parent > a,
.menu-highlight #Top_bar #menu > ul > li.current-menu-ancestor > a,
.menu-highlight #Top_bar #menu > ul > li.current-page-ancestor > a,
.menu-highlight #Top_bar #menu > ul > li.current_page_ancestor > a,
.menu-highlight #Top_bar #menu > ul > li.hover > a {
	background: <?php echo esc_attr(mfn_opts_get('background-menu-a-active', '#F2F2F2')) ?>;
}

.menu-arrow-bottom #Top_bar .menu > li > a:after {
 		border-bottom-color: <?php echo esc_attr(mfn_opts_get('color-menu-a-active', '#0095eb')) ?>;
}
.menu-arrow-top #Top_bar .menu > li > a:after {
    border-top-color: <?php echo esc_attr(mfn_opts_get('color-menu-a-active', '#0095eb')) ?>;
}

.header-plain #Top_bar .menu > li.current-menu-item > a,
.header-plain #Top_bar .menu > li.current_page_item > a,
.header-plain #Top_bar .menu > li.current-menu-parent > a,
.header-plain #Top_bar .menu > li.current-page-parent > a,
.header-plain #Top_bar .menu > li.current-menu-ancestor > a,
.header-plain #Top_bar .menu > li.current-page-ancestor > a,
.header-plain #Top_bar .menu > li.current_page_ancestor > a,
.header-plain #Top_bar .menu > li.hover > a,
.header-plain #Top_bar a:hover#header_cart,
.header-plain #Top_bar a:hover#search_button,
.header-plain #Top_bar .wpml-languages:hover,
.header-plain #Top_bar .wpml-languages ul.wpml-lang-dropdown {
	background: <?php echo esc_attr(mfn_opts_get('background-menu-a-active', '#F2F2F2')) ?>;
	color: <?php echo esc_attr(mfn_opts_get('color-menu-a-active', '#0095eb')) ?>;
}

.header-plain #Top_bar,
.header-plain #Top_bar .menu > li > a span:not(.description),
.header-plain #Top_bar a#header_cart,
.header-plain #Top_bar a#search_button,
.header-plain #Top_bar .wpml-languages,
.header-plain #Top_bar a.action_button {
	border-color: <?php echo esc_attr(mfn_opts_get('border-menu-plain', '#f2f2f2')) ?>;
}

#Top_bar .menu > li ul {
	background-color: <?php echo esc_attr(mfn_opts_get('background-submenu', '#F2F2F2')) ?>;
}
#Top_bar .menu > li ul li a {
	color: <?php echo esc_attr(mfn_opts_get('color-submenu-a', '#5f5f5f')) ?>;
}
#Top_bar .menu > li ul li a:hover,
#Top_bar .menu > li ul li.hover > a {
	color: <?php echo esc_attr(mfn_opts_get('color-submenu-a-hover', '#2e2e2e')) ?>;
}
#Top_bar .search_wrapper {
	background: <?php echo esc_attr(mfn_opts_get('background-search', '#0095eb')) ?>;
}

.overlay-menu-toggle {
	color: <?php echo esc_attr(mfn_opts_get('color-menu-responsive-icon', '#0095eb')) ?> !important;
	background: <?php echo esc_attr(mfn_opts_get('background-menu-responsive-icon', 'transparent')) ?>;
}
#Overlay {
	background: <?php echo esc_attr(mfn_hex2rgba(mfn_opts_get('background-overlay-menu', '#0095eb'), .95)) ?>;
}
#overlay-menu ul li a, .header-overlay .overlay-menu-toggle.focus {
	color: <?php echo esc_attr(mfn_opts_get('background-overlay-menu-a', '#ffffff')) ?>;
}
#overlay-menu ul li.current-menu-item > a,
#overlay-menu ul li.current_page_item > a,
#overlay-menu ul li.current-menu-parent > a,
#overlay-menu ul li.current-page-parent > a,
#overlay-menu ul li.current-menu-ancestor > a,
#overlay-menu ul li.current-page-ancestor > a,
#overlay-menu ul li.current_page_ancestor > a {
	color: <?php echo esc_attr(mfn_opts_get('background-overlay-menu-a-active', '#B1DCFB')) ?>;
}

#Top_bar .responsive-menu-toggle,
#Header_creative .creative-menu-toggle,
#Header_creative .responsive-menu-toggle {
	color: <?php echo esc_attr(mfn_opts_get('color-menu-responsive-icon', '#0095eb')) ?>;
	background: <?php echo esc_attr(mfn_opts_get('background-menu-responsive-icon', 'transparent')) ?>;
}

#Side_slide{
	background-color: <?php echo esc_attr(mfn_opts_get('background-side-menu', '#191919')) ?>;
	border-color: <?php echo esc_attr(mfn_opts_get('background-side-menu', '#191919')) ?>;
}

#Side_slide,
#Side_slide .search-wrapper input.field,
#Side_slide a:not(.action_button),
#Side_slide #menu ul li.submenu .menu-toggle{
	color: <?php echo esc_attr(mfn_opts_get('color-side-menu-a', '#a6a6a6')) ?>;
}

#Side_slide a:not(.action_button):hover,
#Side_slide a.active,
#Side_slide #menu ul li.hover > .menu-toggle{
	color: <?php echo esc_attr(mfn_opts_get('color-side-menu-a-hover', '#ffffff')) ?>;
}

#Side_slide #menu ul li.current-menu-item > a,
#Side_slide #menu ul li.current_page_item > a,
#Side_slide #menu ul li.current-menu-parent > a,
#Side_slide #menu ul li.current-page-parent > a,
#Side_slide #menu ul li.current-menu-ancestor > a,
#Side_slide #menu ul li.current-page-ancestor > a,
#Side_slide #menu ul li.current_page_ancestor > a,
#Side_slide #menu ul li.hover > a,#Side_slide #menu ul li:hover > a{
	color: <?php echo esc_attr(mfn_opts_get('color-side-menu-a-hover', '#ffffff')) ?>;
}

#Action_bar .contact_details{
	color: <?php echo esc_attr(mfn_opts_get('color-action-bar', '#bbbbbb')) ?>
}

#Action_bar .contact_details a{
	color: <?php echo esc_attr(mfn_opts_get('color-action-bar-a', '#0095eb')) ?>
}

#Action_bar .contact_details a:hover{
	color: <?php echo esc_attr(mfn_opts_get('color-action-bar-a-hover', '#2275ac')) ?>
}

#Action_bar .social li a,
#Header_creative .social li a,
#Action_bar:not(.creative) .social-menu a{
	color: <?php echo esc_attr(mfn_opts_get('color-action-bar-social', '#bbbbbb')) ?>
}

#Action_bar .social li a:hover,
#Header_creative .social li a:hover,
#Action_bar:not(.creative) .social-menu a:hover{
	color: <?php echo esc_attr(mfn_opts_get('color-action-bar-social-hover', '#ffffff')) ?>
}

#Subheader .title  {
	color: <?php echo esc_attr(mfn_opts_get('color-subheader', '#888888')) ?>;
}
#Subheader ul.breadcrumbs li, #Subheader ul.breadcrumbs li a  {
	color: <?php echo esc_attr(mfn_hex2rgba(mfn_opts_get('color-subheader', '#888888'), .6)) ?>;
}

/**
 * Footer *****
 */

#Footer, #Footer .widget_recent_entries ul li a {
	color: <?php echo esc_attr(mfn_opts_get('color-footer', '#cccccc')) ?>;
}

#Footer a {
	color: <?php echo esc_attr(mfn_opts_get('color-footer-a', '#0095eb')) ?>;
}

#Footer a:hover {
	color: <?php echo esc_attr(mfn_opts_get('color-footer-a-hover', '#2275ac')) ?>;
}

#Footer h1, #Footer h1 a, #Footer h1 a:hover,
#Footer h2, #Footer h2 a, #Footer h2 a:hover,
#Footer h3, #Footer h3 a, #Footer h3 a:hover,
#Footer h4, #Footer h4 a, #Footer h4 a:hover,
#Footer h5, #Footer h5 a, #Footer h5 a:hover,
#Footer h6, #Footer h6 a, #Footer h6 a:hover {
	color: <?php echo esc_attr(mfn_opts_get('color-footer-heading', '#ffffff')) ?>;
}

#Footer .themecolor, #Footer .widget_meta ul, #Footer .widget_pages ul, #Footer .widget_rss ul, #Footer .widget_mfn_recent_comments ul li:after, #Footer .widget_archive ul,
#Footer .widget_recent_comments ul li:after, #Footer .widget_nav_menu ul, #Footer .widget_price_filter .price_label .from, #Footer .widget_price_filter .price_label .to,
#Footer .star-rating span {
	color: <?php echo esc_attr(mfn_opts_get('color-footer-theme', '#0095eb')) ?>;
}

#Footer .themebg, #Footer .widget_categories ul, #Footer .Recent_posts ul li .desc:after, #Footer .Recent_posts ul li .photo .c,
#Footer .widget_recent_entries ul li:after, #Footer .widget_mfn_menu ul li a:hover, #Footer .widget_product_categories ul {
	background-color: <?php echo esc_attr(mfn_opts_get('color-footer-theme', '#0095eb')) ?>;
}

#Footer .Recent_posts ul li a .desc .date, #Footer .widget_recent_entries ul li .post-date, #Footer .tp_recent_tweets .twitter_time,
#Footer .widget_price_filter .price_label, #Footer .shop-filters .woocommerce-result-count, #Footer ul.product_list_widget li .quantity,
#Footer .widget_shopping_cart ul.product_list_widget li dl {
	color: <?php echo esc_attr(mfn_opts_get('color-footer-note', '#a8a8a8')) ?>;
}

#Footer .footer_copy .social li a,
#Footer .footer_copy .social-menu a{
	color: <?php echo esc_attr(mfn_opts_get('color-footer-social', '#65666C')) ?>;
}

#Footer .footer_copy .social li a:hover,
#Footer .footer_copy .social-menu a:hover{
	color: <?php echo esc_attr(mfn_opts_get('color-footer-social-hover', '#ffffff')) ?>;
}

#Footer .footer_copy{
	border-top-color: <?php echo esc_attr(mfn_opts_get('border-copyright', 'rgba(255,255,255,.1)')) ?>;
}

a#back_to_top.button.button_js,
#popup_contact > a.button{
	color: <?php echo esc_attr(mfn_opts_get('color-footer-backtotop', '#65666C')) ?>;
	<?php if (mfn_opts_get('background-footer-backtotop')): ?>
		background: <?php echo esc_attr(mfn_opts_get('background-footer-backtotop')) ?>;
	<?php else: ?>
		background:transparent;
		-webkit-box-shadow:none;
		box-shadow:none;
	<?php endif; ?>
}

<?php if(mfn_opts_get('background-footer-backtotop')): ?>
.button-stroke #back_to_top,
.button-stroke #popup_contact > .button{
	border-color: <?php echo esc_attr(mfn_opts_get('background-footer-backtotop')) ?>;
}
.button-stroke #back_to_top:hover,
.button-stroke #popup_contact > .button:hover{
	background-color: <?php echo esc_attr(mfn_opts_get('background-footer-backtotop')) ?> !important;
}
<?php endif; ?>

<?php if (! mfn_opts_get('background-footer-backtotop')): ?>
a#back_to_top.button.button_js:after,
#popup_contact > a.button:after{
	display:none;
}
<?php endif; ?>

/**
 * Sliding top *****
 */

#Sliding-top, #Sliding-top .widget_recent_entries ul li a {
	color: <?php echo esc_attr(mfn_opts_get('color-sliding-top', '#cccccc')) ?>;
}

#Sliding-top a {
	color: <?php echo esc_attr(mfn_opts_get('color-sliding-top-a', '#0095eb')) ?>;
}

#Sliding-top a:hover {
	color: <?php echo esc_attr(mfn_opts_get('color-sliding-top-a-hover', '#2275ac')) ?>;
}

#Sliding-top h1, #Sliding-top h1 a, #Sliding-top h1 a:hover,
#Sliding-top h2, #Sliding-top h2 a, #Sliding-top h2 a:hover,
#Sliding-top h3, #Sliding-top h3 a, #Sliding-top h3 a:hover,
#Sliding-top h4, #Sliding-top h4 a, #Sliding-top h4 a:hover,
#Sliding-top h5, #Sliding-top h5 a, #Sliding-top h5 a:hover,
#Sliding-top h6, #Sliding-top h6 a, #Sliding-top h6 a:hover {
	color: <?php echo esc_attr(mfn_opts_get('color-sliding-top-heading', '#ffffff')) ?>;
}

/* Theme color */

#Sliding-top .themecolor, #Sliding-top .widget_meta ul, #Sliding-top .widget_pages ul, #Sliding-top .widget_rss ul, #Sliding-top .widget_mfn_recent_comments ul li:after, #Sliding-top .widget_archive ul,
#Sliding-top .widget_recent_comments ul li:after, #Sliding-top .widget_nav_menu ul, #Sliding-top .widget_price_filter .price_label .from, #Sliding-top .widget_price_filter .price_label .to,
#Sliding-top .star-rating span {
	color: <?php echo esc_attr(mfn_opts_get('color-sliding-top-theme', '#0095eb')) ?>;
}

/* Theme background */

#Sliding-top .themebg, #Sliding-top .widget_categories ul, #Sliding-top .Recent_posts ul li .desc:after, #Sliding-top .Recent_posts ul li .photo .c,
#Sliding-top .widget_recent_entries ul li:after, #Sliding-top .widget_mfn_menu ul li a:hover, #Sliding-top .widget_product_categories ul {
	background-color: <?php echo esc_attr(mfn_opts_get('color-sliding-top-theme', '#0095eb')) ?>;
}

/* Grey */

#Sliding-top .Recent_posts ul li a .desc .date, #Sliding-top .widget_recent_entries ul li .post-date, #Sliding-top .tp_recent_tweets .twitter_time,
#Sliding-top .widget_price_filter .price_label, #Sliding-top .shop-filters .woocommerce-result-count, #Sliding-top ul.product_list_widget li .quantity,
#Sliding-top .widget_shopping_cart ul.product_list_widget li dl {
	color: <?php echo esc_attr(mfn_opts_get('color-sliding-top-note', '#a8a8a8')) ?>;
}

/**
 * Shortcodes *****
 */

/* Blockquote */

blockquote, blockquote a, blockquote a:hover {
	color: <?php echo esc_attr(mfn_opts_get('color-blockquote', '#444444')) ?>;
}

/* Image frames & Google maps & Icon bar */

.image_frame .image_wrapper .image_links,
.portfolio_group.masonry-hover .portfolio-item .masonry-hover-wrapper .hover-desc {
	background: <?php echo esc_attr(mfn_hex2rgba(mfn_opts_get('background-imageframe-link', '#0095eb'), 0.8)) ?>;
}

.masonry.tiles .post-item .post-desc-wrapper .post-desc .post-title:after,.masonry.tiles .post-item.no-img,.masonry.tiles .post-item.format-quote,.blog-teaser li .desc-wrapper .desc .post-title:after,.blog-teaser li.no-img,.blog-teaser li.format-quote {
	background: <?php echo esc_attr(mfn_opts_get('background-imageframe-link', '#0095eb')) ?>;
}

.image_frame .image_wrapper .image_links a {
	color: <?php echo esc_attr(mfn_opts_get('color-imageframe-link', '#ffffff')) ?>;
}
.image_frame .image_wrapper .image_links a:hover {
	background: <?php echo esc_attr(mfn_opts_get('color-imageframe-link', '#ffffff')) ?>;
	color: <?php echo esc_attr(mfn_opts_get('background-imageframe-link', '#0095eb')) ?>;
}
.image_frame {
  	border-color: <?php echo esc_attr(mfn_opts_get('border-imageframe', '#f8f8f8')) ?>;
}
.image_frame .image_wrapper .mask::after {
  	background: <?php echo esc_attr(mfn_hex2rgba(mfn_opts_get('color-imageframe-mask', '#ffffff'), 0.4)) ?>;
}

/* Sliding box */

.sliding_box .desc_wrapper {
	background: <?php echo esc_attr(mfn_opts_get('background-slidingbox-title', '#0095eb')) ?>;
}
.sliding_box .desc_wrapper:after {
	border-bottom-color: <?php echo esc_attr(mfn_opts_get('background-slidingbox-title', '#0095eb')) ?>;
}

/* Counter & Chart */

.counter .icon_wrapper i {
	color: <?php echo esc_attr(mfn_opts_get('color-counter', '#0095eb')) ?>;
}

/* Quick facts */

.quick_fact .number-wrapper {
	color: <?php echo esc_attr(mfn_opts_get('color-quickfact-number', '#0095eb')) ?>;
}

/* Progress bar */

.progress_bars .bars_list li .bar .progress {
	background-color: <?php echo esc_attr(mfn_opts_get('background-progressbar', '#0095eb')) ?>;
}

/* Icon bar */

a:hover.icon_bar {
	color: <?php echo esc_attr(mfn_opts_get('color-iconbar', '#0095eb')) ?> !important;
}

/* Content links */

a.content_link, a:hover.content_link {
	color: <?php echo esc_attr(mfn_opts_get('color-contentlink', '#0095eb')) ?>;
}
a.content_link:before {
	border-bottom-color: <?php echo esc_attr(mfn_opts_get('color-contentlink', '#0095eb')) ?>;
}
a.content_link:after {
	border-color: <?php echo esc_attr(mfn_opts_get('color-contentlink', '#0095eb')) ?>;
}

/* Get in touch & Infobox */

.get_in_touch, .infobox {
	background-color: <?php echo esc_attr(mfn_opts_get('background-getintouch', '#0095eb')) ?>;
}
.google-map-contact-wrapper .get_in_touch:after {
	border-top-color: <?php echo esc_attr(mfn_opts_get('background-getintouch', '#0095eb')) ?>;
}

/* Timeline & Post timeline */

.timeline_items li h3:before,
.timeline_items:after,
.timeline .post-item:before {
	border-color: <?php echo esc_attr(mfn_opts_get('color-theme', '#0095eb')) ?>;
}

/* How it works */

.how_it_works .image .number {
	background: <?php echo esc_attr(mfn_opts_get('color-theme', '#0095eb')) ?>;
}

/* Trailer box */

.trailer_box .desc .subtitle,
.trailer_box.plain .desc .line {
	background-color: <?php echo esc_attr(mfn_opts_get('background-trailer-subtitle', '#0095eb')) ?>;
}
.trailer_box.plain .desc .subtitle {
	color: <?php echo esc_attr(mfn_opts_get('background-trailer-subtitle', '#0095eb')) ?>;
}

/* Icon box */

.icon_box .icon_wrapper, .icon_box a .icon_wrapper,
.style-simple .icon_box:hover .icon_wrapper {
	color: <?php echo esc_attr(mfn_opts_get('color-iconbox', '#0095eb')) ?>;
}
.icon_box:hover .icon_wrapper:before,
.icon_box a:hover .icon_wrapper:before {
	background-color: <?php echo esc_attr(mfn_opts_get('color-iconbox', '#0095eb')) ?>;
}

/* Clients */

ul.clients.clients_tiles li .client_wrapper:hover:before {
	background: <?php echo esc_attr(mfn_opts_get('color-theme', '#0095eb')) ?>;
}
ul.clients.clients_tiles li .client_wrapper:after {
	border-bottom-color: <?php echo esc_attr(mfn_opts_get('color-theme', '#0095eb')) ?>;
}

/* List */

.list_item.lists_1 .list_left {
	background-color: <?php echo esc_attr(mfn_opts_get('color-list-icon', '#0095eb')) ?>;
}
.list_item .list_left {
	color: <?php echo esc_attr(mfn_opts_get('color-list-icon', '#0095eb')) ?>;
}

/* Features list */

.feature_list ul li .icon i {
	color: <?php echo esc_attr(mfn_opts_get('color-list-icon', '#0095eb')) ?>;
}
.feature_list ul li:hover,
.feature_list ul li:hover a {
	background: <?php echo esc_attr(mfn_opts_get('color-list-icon', '#0095eb')) ?>;
}

/* Tabs, Accordion, Toggle, Table, Faq */

.ui-tabs .ui-tabs-nav li.ui-state-active a,
.accordion .question.active .title > .acc-icon-plus,
.accordion .question.active .title > .acc-icon-minus,
.faq .question.active .title > .acc-icon-plus,
.faq .question.active .title,
.accordion .question.active .title {
	color: <?php echo esc_attr(mfn_opts_get('color-tab-title', '#0095eb')) ?>;
}
.ui-tabs .ui-tabs-nav li.ui-state-active a:after {
	background: <?php echo esc_attr(mfn_opts_get('color-tab-title', '#0095eb')) ?>;
}
body.table-hover:not(.woocommerce-page) table tr:hover td {
	background: <?php echo esc_attr(mfn_opts_get('color-theme', '#0095eb')) ?>;
}

/* Pricing */

.pricing-box .plan-header .price sup.currency,
.pricing-box .plan-header .price > span {
	color: <?php echo esc_attr(mfn_opts_get('color-pricing-price', '#0095eb')) ?>;
}
.pricing-box .plan-inside ul li .yes {
	background: <?php echo esc_attr(mfn_opts_get('color-pricing-price', '#0095eb')) ?>;
}
.pricing-box-box.pricing-box-featured {
	background: <?php echo esc_attr(mfn_opts_get('background-pricing-featured', '#0095eb')) ?>;
}

/**
 * Forms *****
 */

/* Transparency */

<?php
	$formAlpha = mfn_opts_get('form-transparent', 100);
	$formAlpha = str_replace(',', '.', ($formAlpha / 100));
?>

/* Input, Select & Textarea */

input[type="date"], input[type="email"], input[type="number"], input[type="password"], input[type="search"], input[type="tel"], input[type="text"], input[type="url"],
select, textarea, .woocommerce .quantity input.qty,
.dark input[type="email"],.dark input[type="password"],.dark input[type="tel"],.dark input[type="text"],.dark select,.dark textarea{
	color: <?php echo esc_attr(mfn_opts_get('color-form', '#626262')) ?>;
	background-color: <?php echo esc_attr(mfn_hex2rgba(mfn_opts_get('background-form', '#ffffff'), $formAlpha)) ?>;
	border-color: <?php echo esc_attr(mfn_opts_get('border-form', '#EBEBEB')) ?>;

}
::-webkit-input-placeholder {
	color: <?php echo esc_attr(mfn_opts_get('color-form-placeholder', '#929292')) ?>;
}
::-moz-placeholder {
  color: <?php echo esc_attr(mfn_opts_get('color-form-placeholder', '#929292')) ?>;
}
:-ms-input-placeholder {
  color: <?php echo esc_attr(mfn_opts_get('color-form-placeholder', '#929292')) ?>;
}

/* Focus */

input[type="date"]:focus, input[type="email"]:focus, input[type="number"]:focus, input[type="password"]:focus, input[type="search"]:focus, input[type="tel"]:focus, input[type="text"]:focus, input[type="url"]:focus, select:focus, textarea:focus {
    color: <?php echo esc_attr(mfn_opts_get('color-form-focus', '#1982c2')) ?>;
    background-color: <?php echo esc_attr(mfn_hex2rgba(mfn_opts_get('background-form-focus', '#e9f5fc'), $formAlpha)) ?> !important;
    border-color: <?php echo esc_attr(mfn_opts_get('border-form-focus', '#d5e5ee')) ?>;
}

:focus::-webkit-input-placeholder {
	color: <?php echo esc_attr(mfn_opts_get('color-form-placeholder-focus', '#929292')) ?>;
}
:focus::-moz-placeholder {
  color: <?php echo esc_attr(mfn_opts_get('color-form-placeholder-focus', '#929292')) ?>;
}

/**
 * Shop *****
 */

.woocommerce span.onsale, .shop_slider .shop_slider_ul li .item_wrapper span.onsale {
	border-top-color: <?php echo esc_attr(mfn_opts_get('color-theme', '#0095eb')) ?> !important;
}

.woocommerce span.onsale i, .shop_slider .shop_slider_ul li .item_wrapper span.onsale i {
	color: <?php echo esc_attr(mfn_brightness(mfn_opts_get('color-theme', '#0095eb'), 169, true)); ?>;
}

.woocommerce .widget_price_filter .ui-slider .ui-slider-handle {
	border-color: <?php echo esc_attr(mfn_opts_get('color-theme', '#0095eb')) ?> !important;
}

/**
 * Responsive *****
 */

<?php if (mfn_opts_get('responsive')): ?>

@media only screen and ( min-width: 768px ){
	.header-semi #Top_bar:not(.is-sticky) {
		background-color: <?php echo esc_attr(mfn_hex2rgba(mfn_opts_get('background-top-left', '#ffffff'), .8)) ?>;
	}
}

@media only screen and ( max-width: 767px ){
	#Top_bar{
		background-color: <?php echo esc_attr(mfn_opts_get('background-top-left', '#ffffff')) ?> !important;
	}

	#Action_bar{
		background-color: <?php echo esc_attr(mfn_opts_get('mobile-background-action-bar', '#ffffff')) ?> !important;
	}

	#Action_bar .contact_details{
		color: <?php echo esc_attr(mfn_opts_get('mobile-color-action-bar', '#222222')) ?>
	}

	#Action_bar .contact_details a{
		color: <?php echo esc_attr(mfn_opts_get('mobile-color-action-bar-a', '#0095eb')) ?>
	}

	#Action_bar .contact_details a:hover{
		color: <?php echo esc_attr(mfn_opts_get('mobile-color-action-bar-a-hover', '#2275ac')) ?>
	}

	#Action_bar .social li a,
	#Action_bar .social-menu a{
		color: <?php echo esc_attr(mfn_opts_get('mobile-color-action-bar-social', '#bbbbbb')) ?>!important
	}

	#Action_bar .social li a:hover,
	#Action_bar .social-menu a:hover{
		color: <?php echo esc_attr(mfn_opts_get('mobile-color-action-bar-social-hover', '#777777')) ?>!important
	}
}

<?php endif; ?>

<?php
/**
 * Default footer
 */
return array(
	'title'      => __( 'Default footer', 'porto' ),
	'categories' => array( 'footer' ),
	'blockTypes' => array( 'core/template-part/footer' ),
	'content'    => '<!-- wp:group {"tagName":"footer","layout":{"inherit":true}} -->
	<footer class="wp-block-group"><!-- wp:porto/porto-section {"add_container":true,"tag":"div","style_options":{"bg":{"color":"rgba(34,37,41,1)"},"padding":{"top":"64px","bottom":"0px"}}} -->
	<!-- wp:columns {"className":"footer-main m-b-lg"} -->
	<div class="wp-block-columns footer-main m-b-lg"><!-- wp:column {"className":"col-md-12 col-xl-3 col-lg-3"} -->
	<div class="wp-block-column col-md-12 col-xl-3 col-lg-3"><!-- wp:porto/porto-heading {"title":"CONTACT INFO","font_size":"16px","font_weight":600,"line_height":"21px","letter_spacing":"-0.8px","color":"#ffffff","tag":"h3","style_options":{"margin":{"bottom":"19px"}}} /-->
	
	<!-- wp:porto/porto-ultimate-heading {"main_heading":"ADDRESS:","main_heading_font_size":"13px","main_heading_font_weight":400,"main_heading_line_height":"13px","main_heading_letter_spacing":"0.065px","main_heading_color":"#ffffff","content":"123 Street Name, City, England","sub_heading_font_size":"13px","sub_heading_line_height":"24px","sub_heading_letter_spacing":"0.065px","sub_heading_color":"#a8a8a8","sub_heading_margin_bottom":9,"alignment":"left","heading_tag":"h4","className":"mb-0"} /-->
	
	<!-- wp:porto/porto-ultimate-heading {"main_heading":"PHONE:","main_heading_font_size":"13px","main_heading_font_weight":400,"main_heading_line_height":"13px","main_heading_letter_spacing":"0.065px","main_heading_color":"#ffffff","content":"(123) 456-7890","sub_heading_font_size":"13px","sub_heading_line_height":"24px","sub_heading_letter_spacing":"0.065px","sub_heading_color":"#a8a8a8","sub_heading_margin_bottom":9,"alignment":"left","heading_tag":"h4","className":"mb-0"} /-->
	
	<!-- wp:porto/porto-ultimate-heading {"main_heading":"EMAIL:","main_heading_font_size":"13px","main_heading_font_weight":400,"main_heading_line_height":"13px","main_heading_letter_spacing":"0.065px","main_heading_color":"#ffffff","content":"\u003ca class=\u0022text-hover-decoration\u0022 href=\u0022mailto:mail@example.com\u0022\u003email@example.com\u003c/a\u003e","sub_heading_font_size":"13px","sub_heading_line_height":"24px","sub_heading_letter_spacing":"0.065px","sub_heading_color":"#a8a8a8","sub_heading_margin_bottom":9,"alignment":"left","heading_tag":"h4","className":"mb-0"} /-->
	
	<!-- wp:porto/porto-ultimate-heading {"main_heading":"WORKING DAYS/HOURS:","main_heading_font_size":"13px","main_heading_font_weight":400,"main_heading_line_height":"13px","main_heading_letter_spacing":"0.065px","main_heading_color":"#ffffff","content":"Mon - Sun / 9:00 AM - 8:00 PM","sub_heading_font_size":"13px","sub_heading_line_height":"24px","sub_heading_letter_spacing":"0.065px","sub_heading_color":"#a8a8a8","sub_heading_margin_bottom":26,"alignment":"left","heading_tag":"h4","className":"mb-0"} /--></div>
	<!-- /wp:column -->
	
	<!-- wp:column {"className":"col-md-6 col-xl-3 col-lg-3 col-12"} -->
	<div class="wp-block-column col-md-6 col-xl-3 col-lg-3 col-12"><!-- wp:porto/porto-heading {"title":"CUSTOMER SERVICE","font_size":"16px","font_weight":600,"line_height":"21px","letter_spacing":"-0.8px","color":"#ffffff","tag":"h3","style_options":{"margin":{"bottom":"17px"}}} /-->
	
	<!-- wp:porto/porto-heading {"title":"\u003cul class=\u0022footer-links mb-0 list-unstyled\u0022\u003e\u003ca style=\u0022letter-spacing: 0.065px;\u0022 href=\u0022#\u0022\u003eHelp \u0026amp; FAQs\u003c/a\u003e   \u003cli\u003e\u003ca href=\u0022#\u0022\u003eOrder Tracking\u003c/a\u003e\u003c/li\u003e\u003cli\u003e\u003ca href=\u0022#\u0022\u003eShipping \u0026amp; Delivery\u003c/a\u003e\u003c/li\u003e\u003cli\u003e\u003ca href=\u0022#\u0022\u003eOrders History\u003c/a\u003e\u003c/li\u003e\u003cli\u003e\u003ca href=\u0022#\u0022\u003eAdvanced Search\u003c/a\u003e\u003c/li\u003e\u003cli\u003e\u003ca href=\u0022#\u0022\u003eMy Account\u003c/a\u003e\u003c/li\u003e\u003cli\u003e\u003ca href=\u0022#\u0022\u003eCareers\u003c/a\u003e\u003c/li\u003e\u003cli\u003e\u003ca href=\u0022#\u0022\u003eAbout Us\u003c/a\u003e\u003c/li\u003e\u003cli\u003e\u003ca href=\u0022#\u0022\u003eCorporate Sales\u003c/a\u003e\u003c/li\u003e\u003cli\u003e\u003ca href=\u0022#\u0022\u003ePrivacy\u003c/a\u003e\u003c/li\u003e\u003c/ul\u003e","font_size":"13px","color":"","tag":"div","className":"mb-0 mb-md-3 pb-md-3"} /--></div>
	<!-- /wp:column -->
	
	<!-- wp:column {"className":"col-md-12 col-xl-2 col-lg-2 widget"} -->
	<div class="wp-block-column col-md-12 col-xl-2 col-lg-2 widget"><!-- wp:porto/porto-heading {"title":"POPULAR TAGS","font_size":"16px","font_weight":600,"line_height":"21px","letter_spacing":"-0.8px","color":"#ffffff","tag":"h3","style_options":{"margin":{"bottom":"15px"}}} /-->
	
	<!-- wp:tag-cloud {"numberOfTags":11,"taxonomy":"product_tag","largestFontSize":"8pt","className":"tagcloud mb-0"} /--></div>
	<!-- /wp:column -->
	
	<!-- wp:column {"className":"col-md-6 col-xl-4 col-lg-4 col-12 align-self-start widget mb-0"} -->
	<div class="wp-block-column col-md-6 col-xl-4 col-lg-4 col-12 align-self-start widget mb-0"><!-- wp:porto/porto-heading {"title":"SUBSCRIBE NEWSLETTER","font_size":"16px","font_weight":600,"line_height":"21px","letter_spacing":"-0.8px","color":"#ffffff","tag":"h3","style_options":{"margin":{"bottom":"17px"}}} /-->
	
	<!-- wp:porto/porto-heading {"title":"Get all the latest information on events,\nsales and offers. Sign up for newsletter:","tag":"p"} /--></div>
	<!-- /wp:column --></div>
	<!-- /wp:columns -->
	
	<!-- wp:separator {"style":{"color":{"background":"#313438"}},"className":"is-style-wide m-0"} -->
	<hr class="wp-block-separator has-text-color has-alpha-channel-opacity has-background is-style-wide m-0" style="background-color:#313438;color:#313438"/>
	<!-- /wp:separator -->
	
	<!-- wp:columns {"className":"mb-0 no-gutters"} -->
	<div class="wp-block-columns mb-0 no-gutters"><!-- wp:column {"className":"col-12 d-flex flex-column flex-md-row align-items-center justify-content-md-between py-2"} -->
	<div class="wp-block-column col-12 d-flex flex-column flex-md-row align-items-center justify-content-md-between py-2"><!-- wp:porto/porto-heading {"title":"© Porto eCommerce. 2022. All Rights Reserved","font_size":"11.7px","font_weight":400,"line_height":"22px","letter_spacing":"0.065px","color":"#777777","tag":"span","className":"mb-3 mb-md-0"} /--></div>
	<!-- /wp:column --></div>
	<!-- /wp:columns -->
	<!-- /wp:porto/porto-section --></footer>
	<!-- /wp:group -->',
);


(function ($) {

  "use strict";


/*-----------------------------------------------------------------------------------

 	Custom JS - All back-end jQuery

-----------------------------------------------------------------------------------*/

jQuery(document).ready(function() {


	// A few overrides to the rwmb metaboxes.

	jQuery('.rwmb-text').addClass('widefat');
	jQuery('.rwmb-oembed').css('width', '80%');
	jQuery('.rwmb-textarea').removeClass('large-text').addClass('widefat');
	jQuery('.rwmb-delete-file').on(function(e) {
		e.preventDefault();
		jQuery(this).parent().parent().slideUp(600);
	});

	// Show metaboxes according to the current post format.



/*----------------------------------------------------------------------------------*/
/*	Gallery Options
/*----------------------------------------------------------------------------------*/

	var galleryOptions = jQuery('#gallery-settings');
	var galleryTrigger = jQuery('#post-format-gallery');

	galleryOptions.css('display', 'none');

/*----------------------------------------------------------------------------------*/
/*	Video Options
/*----------------------------------------------------------------------------------*/

	var embedOptions = jQuery('#embed-settings');
	var embedTrigger = jQuery('#post-format-video');
	var embedTrigger2 = jQuery('#post-format-audio');

	embedOptions.css('display', 'none');

/*----------------------------------------------------------------------------------*/
/*	The Brain
/*----------------------------------------------------------------------------------*/

	var group = jQuery('#post-formats-select input');


	group.change( function() {

		if (jQuery(this).val() == 'gallery') {
			galleryOptions.css('display', 'block');
			ninethemeHideAll(galleryOptions);

		} else if(jQuery(this).val() == 'video') {
			embedOptions.css('display', 'block');
			ninethemeHideAll(embedOptions);

		} else if(jQuery(this).val() == 'audio') {
			embedOptions.css('display', 'block');
			ninethemeHideAll(embedOptions);

		} else {
			embedOptions.css('display', 'none');
			galleryOptions.css('display', 'none');
		}

	});

	if(galleryTrigger.is(':checked'))
		galleryOptions.css('display', 'block');

	if(embedTrigger.is(':checked'))
		embedOptions.css('display', 'block');

	if(embedTrigger2.is(':checked'))
		embedOptions.css('display', 'block');

	function ninethemeHideAll(notThisOne) {
		embedOptions.css('display', 'none');
		galleryOptions.css('display', 'none');
		notThisOne.css('display', 'block');
	}


/*----------------------------------------------------------------------------------*/
/*	for displaying page top header
/*----------------------------------------------------------------------------------*/

	//page header navigation settings
	var pagenav = jQuery('#agro_page_nav_onoff');
	var navoffinfo = jQuery('#pageheadersettings #page-nav-off-info').parents('.rwmb-custom_html-wrapper');

	if( !pagenav.is(':checked')) {
		navoffinfo.slideDown();
	}
	else {
		navoffinfo.slideUp();
	}
	pagenav.on('change', function(){
		if(!pagenav.is(':checked')) {
		navoffinfo.slideDown();
		}
		else {
		navoffinfo.slideUp();
		}
	});

	//page header top-bar settings
	var pagetopbar = jQuery('#agro_page_topbar_onoff');
	var pagetopbaroffinfo = jQuery('#pageheadersettings #page-top-bar-off-info').parents('.rwmb-custom_html-wrapper');

	if( !pagetopbar.is(':checked')) {
		pagetopbaroffinfo.slideDown();
	}
	else {
		pagetopbaroffinfo.slideUp();
	}
	pagetopbar.on('change', function(){
		if(!pagetopbar.is(':checked')) {
		pagetopbaroffinfo.slideDown();
		}
		else {
		pagetopbaroffinfo.slideUp();
		}
	});


	//page footer display settings
	var pagefooter= jQuery('#agro_page_footer_onoff');
  var pagefw= jQuery('#agro_page_footer_widgetize_onoff').parents('.rwmb-switch-wrapper');
  var pagefc= jQuery('#agro_page_footer_copyright_onoff').parents('.rwmb-switch-wrapper');
	var pagefooterinfo = jQuery('#pageheadersettings #page-footer-off-info').parents('.rwmb-custom_html-wrapper');

	if( !pagefooter.is(':checked')) {
		pagefw.slideUp();
		pagefc.slideUp();
		pagefooterinfo.slideDown();
	}
	else {
    pagefw.slideDown();
		pagefc.slideDown();
		pagefooterinfo.slideUp();
	}
	pagefooter.on('change', function(){
		if(!pagefooter.is(':checked')) {
    pagefw.slideUp();
  	pagefc.slideUp();
		pagefooterinfo.slideDown();
		}
		else {
    pagefw.slideDown();
  	pagefc.slideDown();
		pagefooterinfo.slideUp();
		}
	});


	//page hero display settings
	var pagehero = jQuery('#agro_page_hero_onoff');
	var pageheroalign = jQuery('label[for="agro_page_hero_align"]').parents('.rwmb-select-wrapper');
	var pageherobg = jQuery('label[for="agro_page_hero_bg"]').parents('.rwmb-background-wrapper');
	var pageherobgot = jQuery('label[for="agro_page_hero_overlay_type"]').parents('.rwmb-select-wrapper');
	var pageherobgo = jQuery('label[for="agro_page_hero_overlay"]').parents('.rwmb-color-wrapper');
	var pageheropt = jQuery('label[for="agro_page_hero_pt"]').parents('.rwmb-number-wrapper');
	var pageheropb = jQuery('label[for="agro_page_hero_pb"]').parents('.rwmb-number-wrapper');
	var pageherodivider = jQuery('#pageherosettings .rwmb-tab-panel-tab0 .rwmb-divider-wrapper, #pageherosettings  .rwmb-tab-tab1, #pageherosettings  .rwmb-tab-tab2, #pageherosettings  .rwmb-tab-tab3');
	var pageheroinfo = jQuery('#pageherosettings #page-hero-off-info').parents('.rwmb-custom_html-wrapper');

	if( !pagehero.is(':checked')) {
		pageheroalign.slideUp();
		pageherobg.slideUp();
		pageherobgot.slideUp();
		pageherobgo.slideUp();
		pageheropt.slideUp();
		pageheropb.slideUp();
		pageherodivider.slideUp();
		pageheroinfo.slideDown();
	}
	else {
		pageheroalign.slideDown();
		pageherobg.slideDown();
		pageherobgot.slideDown();
		pageherobgo.slideDown();
		pageheropt.slideDown();
		pageheropb.slideDown();
		pageherodivider.slideDown();
		pageheroinfo.slideUp();
	}
	pagehero.on('change', function(){
		if(!pagehero.is(':checked')) {
		pageheroalign.slideUp();
		pageherobg.slideUp();
		pageherobgot.slideUp();
		pageherobgo.slideUp();
		pageheropt.slideUp();
		pageheropb.slideUp();
		pageherodivider.slideUp();
		pageheroinfo.slideDown();
		}
		else {
		pageheroalign.slideDown();
		pageherobg.slideDown();
		pageherobgot.slideDown();
		pageherobgo.slideDown();
		pageheropt.slideDown();
		pageheropb.slideDown();
		pageherodivider.slideDown();
		pageheroinfo.slideUp();
		}
	});


});
})(jQuery);

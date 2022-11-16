(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	$(document).ready(function() {
		/// multiple kb tab grid
		$('.betterdocs-tab-list a').first().addClass('active');
		$('.betterdocs-tab-content').first().addClass('active');
		$('.tab-content-1').addClass('active');
		$('.betterdocs-tab-list a').click(function(e) {
			e.preventDefault();
			$(this).siblings('a').removeClass('active').end().addClass('active');
			let sel = this.getAttribute('data-toggle-target');
			$('.betterdocs-tab-content').removeClass('active').filter(sel).addClass('active');
		});
		/**
	 	 * Event Listener Added To Advanced Search Button To Prevent Submission
	 	 */
		$('.search-submit').on('click', function( e ) {
			e.preventDefault();
		});

	});

	/**
	 * Sidebar Layout 6 Accordion
	 */
	let active_category 	 = $('.betterdocs-sidebar-category-title-count.current-term');
	let active_category_list = $('.betterdocs-sidebar-layout-6 li .betterdocs-sidebar-category-title-count');
	let nested_subcat_title  = $('.betterdocs-sidebar-layout-6 li .doc-list .nested-docs-sub-cat-title');
	let active_sub_cat_list	 = $('.betterdocs-sidebar-layout-6 li .doc-list .nested-docs-sub-cat.current-sub-cat');
	
	if( active_category.length ) {
		$(active_category).next().slideDown('fast', 'linear');
	}
	
	active_nested_subcat_arrows();

	active_category_list.on('click', function(e){
		e.preventDefault();
		if( $(this).next().hasClass('doc-list current-doc-list') ) {
			$(this).removeClass('current-term');
			$(this).next().removeClass('current-doc-list');
			$(this).next().slideUp('fast', 'linear');
		} else if( active_category_list.hasClass('betterdocs-sidebar-category-title-count current-term') ){
			active_category_list.closest('.betterdocs-sidebar-category-title-count.current-term').next().removeClass('current-doc-list').slideUp();
			active_category_list.closest('.betterdocs-sidebar-category-title-count.current-term').removeClass('current-term');
			$(this).toggleClass('current-term');
			$(this).next().toggleClass('current-doc-list');
			$(this).next().slideDown('fast', 'linear');
		}else {
			$(this).toggleClass('current-term');
			$(this).next().toggleClass('current-doc-list');
			$(this).next().slideDown('fast', 'linear');
		}
	});

	nested_subcat_title.on('click', function(e){
		e.preventDefault();
		$(this).children(".toggle-arrow").toggle();
		$(this).next().slideToggle();
	});


	/**
	 * This code toggles the nested subcat arrow to down direction if it is activated, this approach is from bottom to top
	 */
	function active_nested_subcat_arrows() {
		if( active_sub_cat_list.length ) {
			while( active_sub_cat_list.attr('class') === 'nested-docs-sub-cat' || active_sub_cat_list.attr('class') === 'nested-docs-sub-cat current-sub-cat' ) {
				active_sub_cat_list.prev();
				active_sub_cat_list.prev().children('.toggle-arrow').toggle();
				active_sub_cat_list.parent().css('display','block');
				active_sub_cat_list = active_sub_cat_list.parent();
			}
		}
	}

	/**
	 * Related Doc Load More Event Listener
	 */
	 var load_more_button = $('.betterdocs-show-more-terms .betterdocs-load-more-button');
	 load_more_button.on('click', function(e){
		e.preventDefault();
		var loader 		  	 = $('.betterdocs-load-more-loader'); 
		var current_terms 	 = $('.betterdocs-related-doc-row').children().length;
		var action 		 	 = 'load_more_terms';
		$.ajax({
			url: show_more_catergories.ajax_url,
			type: "GET",
			data:{
				_wpnonce: show_more_catergories.nonce,
				current_terms: current_terms, 
				action: action,
				tax_page: show_more_catergories.tax_page,
				current_term_id:show_more_catergories.current_term_id,
				kb_slug:show_more_catergories.kb_slug
			},
			beforeSend: () => {
				$('.betterdocs-load-more-button .load-more-text').text('Loading');
				loader.css('display', 'block');
			},
			success: (response) => {
				if( response.data != '' ) {
					var payload = $(response.data);
					setTimeout(() => {
						$('.betterdocs-load-more-button .load-more-text').text('Load More');
						loader.css('display', 'none');
						$('.betterdocs-related-doc-row').append(payload);
						payload.css('opacity',0.0).slideDown('slow').animate({opacity: 3.0});
						if( $('.betterdocs-related-doc-row').children().length == show_more_catergories.term_count ) {
							$('.betterdocs-show-more-terms').remove();
						}
					}, 100);
				}
			}
		});
	 })

})( jQuery );

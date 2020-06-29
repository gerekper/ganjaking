/* Ajax-Layerd Nav Widgets
 * Shopping Cart: WooCommerce
 * File: Frontend JS
 * License: GPL
 * Copyright: SixtyOneDesigns
 */

/* Globals
 * Setup variables and id  for areas that are going to be refreshed
 */
var content 	= ajax_layered_nav.containers;		// new Array;			//Areas to be refreshed
var orderby 	= ajax_layered_nav.orderby;
var triggers 	= ajax_layered_nav.triggers.join(',');
var selects 	= ajax_layered_nav.selects.join(',');
var elements_to_remove = new Array; 	// Pop items in and out of this array so we know they've been refreshed
var DocReadyReload = false;				// Set this to true if your getting sone javascript problems
var isWorking = false;					// Flag to know if we're fetching a refresh
var pagination;

function checkPagination(){
	if(jQuery('nav.pagination').length > 0){
		pagination = '';
	}
}
/*	Event: document.ready
 * 	Desc: Inititalize the page
 * 			1. Calls function to add Live Handlers to the widget areas, and product area fn= pageLoaderInit()
 * 			2. Build array of ids of widgets that are going to be refresed
 */
jQuery(document).ready(function(){

	pageLoaderInit();
	// jQuery('.widget_layered_nav, .widget_layered_nav_filters, .widget_ajax_layered_nav_filters').each(function(){
		// content.push( this.id );
	// });
	 jQuery(document).on('change', orderby + ' select', function() {
	 	$form = jQuery( this ).parent();
		$form.submit();
		 return false;
	});
});

/*	Event: onpopstate
 *  Desc: Reload the page every time the browsers history changes
 */
window.onpopstate = function(event) {
	if (event.state != undefined) {
		loadPage(document.location.toString(),1);
	}
};
/* Function: pageLoaderInit
 * Desc: Add live click handlers to anchors and checkboxes
 * 			1. On Click - load page, prevent broswer
 * 			a. Calls fn = loadPage();
 */
function pageLoaderInit(){

  jQuery(document).on('click', triggers, function(event){

  		this.blur();
      	var caption = this.title || this.name || "";
      	var group = this.rel || false;
      	var count = jQuery(this).data('count');
      	if(count == 1 && ajax_layered_nav.search_page_redirect == 1){
      		window.location.href = jQuery(this).data('link');
      	}else{
      		loadPage(jQuery(this).data('link'));
      	};

      	event.preventDefault();
      	return false;
  });
  jQuery(document).on('change', selects, function(event){

  		this.blur();
      	var caption = this.title || this.name || "";
      	var group = this.rel || false;
      	var count = jQuery(this).data('count');
      	if(count == 1 && ajax_layered_nav.search_page_redirect == 1){
      		window.location.href = jQuery(this).data('link');
      	}else{
      		var optionSelected = jQuery("option:selected", this);
      		loadPage(optionSelected.data('filter'));
      	};

      	event.preventDefault();
      	return false;
  });
 }

/* Function: loadPage
 * Params:
 * 		@url	= url of target page,
 * 		@push	= whether to update browser history
 * Desc: Reloads content areas
 */
function loadPage(url, push){
	//Make sure wer're not already doing something
	if (!isWorking){
		//get domain name...
		nohttp = url.replace("http://","").replace("https://","");
		firstsla = nohttp.indexOf("/");
		pathpos = url.indexOf(nohttp);
		path = url.substring(pathpos + firstsla);
		//Only do a history state if clicked on the page.
		if (push != 1) {
			var stateObj = { foo: 1000 + Math.random()*1001 };
			/*Only push history if not IE
			 * IE doesn't support
			 */
			//ie = navigator.userAgent.match(/msie/i) || navigator.userAgent.match(/trident/i);
			if(!navigator.userAgent.match(/msie/i)){
				history.pushState(stateObj, "ajax page loaded...", path);
			}
		}
	
		/* Loop through each id in the content array()*/
		jQuery.each(content, function(index, value){
			/* Products container
			 * add an img / message to the products container to let user know it's being refreshed
			 */
			if(value ==ajax_layered_nav.product_container){
				var max = 0;
				max = jQuery(ajax_layered_nav.product_container).outerHeight();
				jQuery(value + '').fadeOut("fast", function() {
					jQuery(value).html('<center style="min-height:'+max+'px;"><p>'+ajax_layered_nav.loading_text+'...<br><img src="'+ajax_layered_nav.loading_img+'" alt="loading"></p></center>');
					jQuery(value).css({'height':max}).fadeIn("slow", function() {});
				});
			}
		});

		isWorking = true;
		jQuery.ajax( url, {
			success: function( data ) {
				showPage( data );
			},
			error: function() {
				jQuery( '#products' ).prepend( '<p class="woocommerce-error">' + ajax_layered_nav.i18n_error_message + '</p>' );
			}
		})
	}
	return false;
}

/* Function: showPage()
 * Params:
 * @details	= The result of the AJAX GET request for products.
 * desc: replaces the contents of the target div with that of the new http request
 */
 function showPage( details ) {
			isWorking = false;										//No longer making the request
			elements_to_remove=[];
			elements_to_remove = content.slice();
			var no_products = false;
			/* Update content areas */
			if(ajax_layered_nav.scrolltop == 1){
				if(jQuery(ajax_layered_nav.product_container).length > 0){
					jQuery('html').animate({scrollTop:jQuery(ajax_layered_nav.product_container).offset().top - ajax_layered_nav.offset}, 'slow');
				}
			}
			jQuery.each( content, function( index, value ) {
				if(value == ajax_layered_nav.no_products){
					if(jQuery(value, details).length > 0){
						no_products = jQuery(value, details).html();
					}

				}
				jQuery(value).each(function(){

					if( this.id != ''){
						var $id = '#'+this.id;
						if ( jQuery($id, details).length > 0  ) {
							var depth = 1;
							var output = '';
							jQuery($id).fadeOut("fast", function() {
								jQuery($id).html( jQuery($id, details).html() );
								jQuery($id).fadeIn(1);
								if (DocReadyReload == true) {
									$(document).trigger("ready");
								}
							});
						} else {												//Empty the elements
							jQuery.each(elements_to_remove, function(index,value){
								jQuery(value).empty();
							});
						}
					}else{

						if ( jQuery(value, details).length > 0 ) {

							var depth = 1;
							var output = '';
							jQuery(value).fadeOut("fast", function() {
								jQuery(value).html( function(){
									if(typeof infinite_scroll != 'undefined'){
										if ( ! navigator.userAgent.match(/(iPod|iPhone|iPad|Android)/)) {
									    	var $infiniteScrollContainer = jQuery(ajax_layered_nav.contentSelector);
											// Reset the plugin before intializing it again
											$infiniteScrollContainer.infinitescroll('destroy');
											$infiniteScrollContainer.infinitescroll('reset');
											$infiniteScrollContainer.infinitescroll('binding','unbind');
											$infiniteScrollContainer.data('infinitescroll', null);
											jQuery(window).unbind('.infscr');
											init_infinite_scroll($infiniteScrollContainer);
											$infiniteScrollContainer.infinitescroll('bind');
										}
	    							}

									return jQuery(value, details).html();
								});

								jQuery(value).fadeIn(1);
								if (DocReadyReload == true) {
									$(document).trigger("ready");
								}
							});
						} else {												//Empty the elements
							jQuery.each(elements_to_remove, function(index,value){
								jQuery(value).empty();
							});
						}
					}
				});
		});
		if(no_products){
			jQuery(ajax_layered_nav.product_container).append('<p class="'+ajax_layered_nav.no_products.replace('.','')+'">'+no_products+'</p>');
		}
		/* Re-fire the pageLoaderInit() function. This adds the live click handlers to the newly
		 * readded elemets
		 */
		pageLoaderInit();
		jQuery('body').trigger('aln_reloaded');
		return false;
}
/* Function removeByValue
 * params:
 * 		@val = value of elment to pop out of array
 * desc: Allows us to remove an element from a javascript array by value
 */
Array.prototype.removeByValue = function(val) {
    for(var i=0; i<this.length; i++) {
        if(this[i] == val) {
            this.splice(i, 1);
            break;
        }
    }
};
function init_infinite_scroll($container){
	//$container = jQuery('ul.products');

	$container.infinitescroll({
		loading: {
			msgText: "Loading the next set of products...",
			finishedMsg: "All products loaded.",
			img: ajax_layered_nav.superstore_img,

		},
		contentSelector : ajax_layered_nav.contentSelector,//"ul.products",
		itemSelector    : ajax_layered_nav.itemSelector,
		navSelector     : ajax_layered_nav.navSelector,
		nextSelector    : ajax_layered_nav.nextSelector,
		debug:false,
		state: {
			isDestroyed: false,
			isDone: false
		},
	});

}
jQuery.event.trigger({
	type: "aln_reloaded",
	time: new Date()
});

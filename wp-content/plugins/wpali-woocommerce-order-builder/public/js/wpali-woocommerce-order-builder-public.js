(function( $ ) {
	'use strict';
	if (typeof topPosition === 'undefined') {
		var FixedHeader = 30;
	}else{
		var FixedHeader = topPosition;
	}
	
	if (typeof Stickybreakpoint === 'undefined') {
		var SidebarMinWidth = 780;
	}else{
		var SidebarMinWidth = Stickybreakpoint;
	}
	if (typeof WooCommerceLayout === 'undefined') {
		var sidebar = new StickySidebar('#wwob_sticky', {
			containerSelector: 'form.cart',
			innerWrapperSelector: '.sidebar__inner',
			topSpacing: FixedHeader,
			bottomSpacing: FixedHeader,
			resizeSensor: true,
			minWidth: SidebarMinWidth,
		});
		$(window).on("load", function() {
			sidebar.updateSticky();
		});
	}	

 $('input[class=checkbox-meta]').change(function () {
	recalculate();
	boxheighcalculator(FixedHeader);

	if (typeof EnhancedCalculator === 'undefined') {
		var EnhancedStickyBar = "disabled";
	}else{
		var EnhancedStickyBar = EnhancedCalculator;
	}
	
	var productliclass = $(this).parent('li').attr('titletrigger');
	var productChecked = $(this).closest('ul.wwobfield_checkbox').find('li').not( ".wwob-clone" ).find('input[class=checkbox-meta]:checked');
	
	// Get all selected items names and push them into one string
	var productname = [];
	$(productChecked).each(function(){
		if (EnhancedStickyBar == "enabled") {
			var string = $(this).attr("details");
			var price = parseFloat(string.substr(0, string.indexOf("|")));
			   if($(this).parent('li').find('span.selected-product-checked').text() == ''){
					var sum = parseFloat(price).toFixed(2);
				}else{
					var sum = parseFloat(price * $(this).parent('li').find('span.selected-product-checked').text()).toFixed(2);
				}
			
			var curreny = $('span.wwob_currency_symbol').text();
			productname += "<p>" + $(this).parent('li').find('span.selected-product-checked').text() + " " + $(this).parent('li').find('label.wwob-checkbox-label .label-meta-container').children('p.wwob-item-name').text() + " <span class='wwob-sticky-item-price'>"+ curreny + sum + "</span></p>";
		}else{
			productname += "<p>" + $(this).parent('li').find('span.selected-product-checked').text() + " " + $(this).parent('li').find('label.wwob-checkbox-label .label-meta-container').children('p.wwob-item-name').text() + "</p>";
		}
	})
	
	// Add styling for selected items
	var allCheckoxesChecked1 = $(this).closest('ul.wwobfield_checkbox').find('input[class=checkbox-meta]:checked');
	var CheckboxNotChecked = $(this).closest('ul.wwobfield_checkbox').find('input[class=checkbox-meta]').not(':checked');
	
	$(CheckboxNotChecked).each(function(){
		$(this).closest('li').removeClass('selected-item');
		$(this).closest('li').find('.selected-product').remove();
		if ($(this).closest('ul').attr('quantity') == 'on' ) {
			$(this).closest('li').removeClass('quantity-enabled');
		}
	})
	$(allCheckoxesChecked1).each(function(){

		$(this).closest('li').not( ".wwob-clone" ).removeClass('selected-item');
		$(this).closest('li').not( ".wwob-clone" ).addClass( 'selected-item' );
		
		if (typeof checkInsideImg != 'undefined') {
			var imgHeight = $(this).closest('li').not( ".wwob-clone" ).find('img').outerHeight(true);
			$(this).closest('li').find('.selected-product').css("height", ( imgHeight ) + "px");
		}
	})
	
	// Add styling when Max Selection is reached
	
	var product_quantity_count = LocalizedVar.product_quantity_count;

	if(product_quantity_count == 'yes'){
		var checkedNum1 = $(this).closest('ul.wwobfield_checkbox').find('li').find('input[class=checkbox-meta]:checked').length;
	}else{
		var checkedNum1 = $(this).closest('ul.wwobfield_checkbox').find('li').not( ".wwob-clone" ).find('input[class=checkbox-meta]:checked').length;
	}
	
	var allCheckboxes1 = $(this).closest('ul.wwobfield_checkbox').find('input[class=checkbox-meta]');
	var maxCheckboxes = $(this).closest('ul').attr('maxselect');
     if ((checkedNum1 >= maxCheckboxes) && ( maxCheckboxes > 0 )){
		 $(allCheckboxes1).closest('ul.wwobfield_checkbox').find('li').find(".max-reached-disabled-product").remove();	
         $(allCheckboxes1).closest('ul.wwobfield_checkbox').find('li').append('<div class="max-reached-disabled-product"></div>');
		 
         $(allCheckoxesChecked1).closest('li').find(".max-reached-disabled-product").remove();		 	 
		
         $(allCheckboxes1).attr('disabled', 'disabled');
         $(allCheckoxesChecked1).removeAttr('disabled');
         
     } else {
		 
         $(allCheckboxes1).closest('ul.wwobfield_checkbox').find('li').find(".max-reached-disabled-product").remove();		
         $(allCheckboxes1).removeAttr('disabled');

     }

	var zero = 0;

	var liclass = $(this).parent('li').attr('titletrigger');
	var liclassa = $(".side-items.wwobchoice_" + liclass + " a")

	// Update Price calculation area with selected items names.
	var checkedNum = $('input[class=checkbox-meta]:checked').length;
	var prodcutsideurl = $(".side-items.wwobchoice_" + productliclass );
	
     if (checkedNum > zero) {
		 $(liclassa).attr('class', 'selected-choice');

		 $(prodcutsideurl).find('span.side-menu-items').empty();
		 $(prodcutsideurl).find('span.side-menu-items').append(productname);

		 
     } else {	 
		$(prodcutsideurl).find('span.side-menu-items').empty();
     }
	 
	 var SideItems = $(prodcutsideurl).find('span.side-menu-items')
	if ($(SideItems).is(':empty')){
		$(SideItems).closest('.sside.side-items').find('a').removeAttr("class");
	}

 });

// Uncheck the item if (.selected-product) is clicked and remove the latter / Check item if container is clicked
$(".extended-checkboxes ul.wwobfield_checkbox li").on('click', function(e) {
	var overlay = $(this).has('.selected-product').length ? "Yes" : "No";
	var overlayBlock = $(this).has('.max-reached-disabled-product').length ? "Yes" : "No";
	if (overlayBlock == "Yes") {
		return false;
	}
	if (overlay == "Yes") {
		if ($(this).closest('ul').attr('quantity') == 'on' ) {
			return true;
		}else{
			e.preventDefault();
			$(this).closest('li').find('input[class=checkbox-meta]:checked').prop('checked', false);
			$(this).find('.selected-product').remove();	
			// Trigger checkbox change function
			$(this).find("input[class=checkbox-meta]").trigger("change");
		}

	}else{
		e.preventDefault();
		$(this).find('input[class=checkbox-meta]').prop('checked', true);		
		$(this).find('.selected-product').remove();
		if ($(this).closest('ul').attr('quantity') == 'on' ) {
			$(this).removeClass('quantity-enabled');
			$(this).addClass( 'quantity-enabled' );
			$(this).append('<div class="selected-product"><a href="#" class="wwob-minus">−</a><span class="selected-product-checked">1</span><a href="#" class="wwob-plus">+</a></div>');
		}else{
			$(this).append('<div class="selected-product"><span class="selected-product-checked"></span></div>');
		}
		
		// Trigger checkbox change function
		$(this).find("input[class=checkbox-meta]").trigger("change");
	}

});
// Handling quantity selection
$('.extended-checkboxes ul.wwobfield_checkbox li').on('click', 'a.wwob-minus', function(event) {
	event.stopPropagation();
	event.preventDefault();
	var parentLi = $(this).closest('li');
	var quantity = parseInt(this.nextSibling.innerHTML);
	
	if (quantity == "1") {
		$(parentLi).find('input[class=checkbox-meta]:checked').prop('checked', false);
		$(parentLi).find('.selected-product').remove();
	}else{
		this.nextSibling.innerHTML = quantity - 1;
		$(parentLi).next('.wwob-clone').remove();
	}
	// Trigger checkbox change function
	$(parentLi).find("input[class=checkbox-meta]").trigger("change");
});
$('.extended-checkboxes ul.wwobfield_checkbox li').on('click', 'a.wwob-plus', function(event) {
	event.stopPropagation();
	event.preventDefault();
	var parentLi = $(this).closest('li');
	var quantity = parseInt(this.previousSibling.innerHTML);
	var CheckedNum = $(this).closest('ul.wwobfield_checkbox').find('input[class=checkbox-meta]:checked').length;
	var maxSelect = $(this).closest('ul').attr('maxselect');
	var Quota = $(this).closest('ul').attr('quota');
	var product_quantity_count = LocalizedVar.product_quantity_count;

		
	if ( (quantity + 1 <= Quota) || ( Quota == false ) ){
		
		if(product_quantity_count == 'yes' &&  CheckedNum >= maxSelect ){
			return;
		}
		
		this.previousSibling.innerHTML = quantity + 1;
		var wwobClone = $(parentLi).clone().insertAfter(parentLi).addClass("wwob-clone").hide();
		var wwobCloneAttr = $(wwobClone).find('input[class=checkbox-meta]').attr('name');
		$(wwobClone).find('input[class=checkbox-meta]').attr("name", wwobCloneAttr + "_" + (quantity + 1));
		
		// Trigger checkbox change function
		$(parentLi).find("input[class=checkbox-meta]").trigger("change");
	}


});

// Do not allow sticky bar to exceed the height of browser window
function boxheighcalculator(FixedHeader){

    var stickyimage = $(".product_totals .img-single").outerHeight(true);
    var stickyprice = $("ul.side_wwobform_totals.wwobform_fields").outerHeight(true);
    var stickybutton = $(".right-price-calculation-area .sticky button").outerHeight(true);
	
    var sticky = $(".sidebar__inner.sticky").height() ;
	var OverallSticky = sticky + FixedHeader;
    var page = $( window ).height();
	
		 var stickyelements = stickybutton + stickyprice + stickyimage;
		 var newheight = ( page - stickyelements) - ( FixedHeader + 30 );
		 
		 $('.product_totals .side-items.wwobchoices').css('max-height', newheight);
		 
}

// calculate the total price of selected items and add it to the floating bar
function recalculate(){
	var BasePrice = $("#woocommerce_product_base_price").val();
    var sum = parseFloat(BasePrice);
    $("input[class=checkbox-meta]:checked").each(function(){
		
		var string = $(this).attr("details");
		var price = string.substr(0, string.indexOf("|"))
      sum += parseFloat(price);
	  
    });
	
	var total = parseFloat(sum).toFixed(2);

	$('span.formattedTotalPrice.wwobinput_total').empty();
	$('span.formattedTotalPrice.wwobinput_total').text(total);
	$('input[name="total-price"]').val(sum);
}
// fix items height.
function DynamicHeight(){
    $('.wwobform_variation_wrapper.wwobform_wrapper.left ul.wwobform_fields li.wwobfield').each(function(){
		var id = $(this).attr( "id" );
		var selector = $("#" + id + " li");
		
		var maxHeight = 0;
		$(selector).each(function(){

			var hz_padding = $(this).innerHeight() - $(this).height();

			var labelHeight = $(this).find('.wwob-checkbox-label').outerHeight();
			var totalHeight = labelHeight + hz_padding;
			
		   maxHeight = totalHeight > maxHeight ? totalHeight : maxHeight;
		   
		});
		$(selector).css("height", ( maxHeight ) + "px");
    });
}
// Re-fix items height on window resize .
$(document).ready(function() {
    $(document).ready(function() {
		// Fix elements height
		$(window).on("resize", function () {
		DynamicHeight();
		boxheighcalculator(FixedHeader)
		}).resize();
	});
// re-fix images after last image is loaded
	$(window).on("load", function() {
		DynamicHeight();
	});
 
});
$(document).ready(function() { $( ".cart-image-lightbox" ).each(function( index ) { $(this).attr('data-lity'); }); });
// Do not allow add to cart if min selection is not reached
$(".woocommerce div.product form.cart .sticky .button").on('click', function(e) {
	e.stopImmediatePropagation()
	var listedProducts = $(".extended-checkboxes ul.wwobfield_checkbox");
	$(listedProducts).each(function(){
		var minSelect = $(this).attr('minselect');	
		var min_error = LocalizedVar.min_error;
		$(this).find(".min-select").remove();
		
		if ( minSelect > 0 ){
			var nChecked = $(this).find('input[type=checkbox]:checked').length;
			if (nChecked < minSelect) {
				$(this).prepend('<div class="min-select" style="text-align:center;"><span class="min-select-error">'+min_error+' <i>'+minSelect+'</i></span></div>');
				$('html, body').animate({scrollTop:$('.min-select').offset().top-100}, 'slow');
				e.preventDefault();
				return false
			}
		}
	})
	
	var listedOptions = $("ul.wwob-option");
	$(listedOptions).each(function(){
		var isRequired = $(this).attr('options_required');	
		if ( isRequired == "on" ){
			if ( $( this ).hasClass( "extra-option-text" ) ) {
				$('.extra-option-text input, .extra-option-text textarea').each(function(){
				   if($(this).val() == "" ){
					   var accordion = $(this).closest('.wwob-accordion-container').find('a.wwob-accordion' );
					   if (!$(accordion).hasClass("active")) {
							$(accordion).trigger( "click" );
					   }
					   
						$(this).addClass('wwob-warning');
						$('html, body').animate({scrollTop:$('.wwob-warning').offset().top-100}, 'slow');

						e.preventDefault();
						return false;
					}else{
						$( "input" ).removeClass( "wwob-warning" )
						$( "textarea" ).removeClass( "wwob-warning" )
					}
				 });
			} else if ( ( $( this ).is( ".wwob-option:not(.extra-option-text):not(.extra-option-select)" ) ) ) {
					$( this ).find('.min-select').remove();
					if($(this).find('input:checked').length == 0) {
					   var accordion = $(this).closest('.wwob-accordion-container').find('a.wwob-accordion' );
					   if (!$(accordion).hasClass("active")) {
							$(accordion).trigger( "click" );
					   }
					   
						$(this).closest('ul').prepend('<div class="min-select" style="text-align:center;"><span class="min-select-error">Please Make a selection</span></div>');
						$('html, body').animate({scrollTop:$('.min-select').offset().top-100}, 'slow');
						
						
						e.preventDefault();
						return false;
					}
			}else if ( ( $( this ).is( ".extra-option-select" ) ) ) {
					var selected = $(this).find('option').filter(':selected:not(:disabled)');
					console.log($(selected).length);
					if($(selected).length == 0 ) {
					   var accordion = $(this).closest('.wwob-accordion-container').find('a.wwob-accordion' );
					   if (!$(accordion).hasClass("active")) {
							$(accordion).trigger( "click" );
					   }
						$(this).find('select').addClass('wwob-warning');
						$('html, body').animate({scrollTop:$('.wwob-warning').offset().top-100}, 'slow');
						
						e.preventDefault();
						return false;
					}else{
						$( ".extra-option-select select" ).removeClass( "wwob-warning" )
					}
			}
		}
	})
	
	return true;
});

$(document).on('click', 'span.min-select-error', function() {
	$(this).closest('.min-select').remove();
});
$(document).on('click', '.wwob-ready-options', function() {
	$(this).closest('ul').find('.selected').removeClass('selected');
	$(this).addClass( 'selected' );
});

// Handle WWOB Accordions: https://www.w3schools.com/howto/howto_js_accordion.asp
var acc = document.getElementsByClassName("wwob-accordion");
var i;

for (i = 0; i < acc.length; i++) {
  acc[i].onclick = function() {
	  
    this.classList.toggle("active");
    var panel = this.nextElementSibling;
	panel.classList.toggle("active");
    if (panel.style.maxHeight){
      panel.style.maxHeight = null;
    } else {
      panel.style.maxHeight = panel.scrollHeight + "px";
    }
	}
}

})( jQuery );
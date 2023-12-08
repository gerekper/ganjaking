/*Testimonials*/
(function ($) {
	"use strict";
	var WidgetTestimonialHandler = function($scope, $) {
		var testiDiv = $scope.find('.testimonial-list');		
		if(testiDiv.length){
			let isoDiv = testiDiv[0].querySelector('.post-inner-loop');
			if( testiDiv[0].classList.contains('list-isotope') || testiDiv[0].classList.contains('list-carousel-slick') ){
				let readBtn = testiDiv[0].querySelectorAll('.testi-readbtn');
				readBtn.forEach((readbtn)=>{
					readbtn.addEventListener( 'click' , function(btn){
						let current = btn.currentTarget,					
						closeEntry = current.closest('.entry-content');
						if(closeEntry == null){
							closeEntry = current.closest('.testimonial-author-title');
						}
						let moretxt = closeEntry.querySelector('.testi-more-text'),
						buttonText = JSON.parse(current.dataset.readdata);
						
						if(moretxt.style.display != "none") {
							moretxt.style.display = "none";
							current.innerHTML = buttonText.readMore
						}else {
							moretxt.style.display = "inline";
							current.innerHTML = buttonText.readLess
						}
	
						if(testiDiv[0].classList.contains('list-isotope')){
							jQuery(isoDiv).isotope({
								itemSelector: ".grid-item",
								resizable: !0,
								sortBy: "original-order"
							});
						}
					})
				})
			}
		}	
	};	
	
	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/tp-testimonial-listout.default', WidgetTestimonialHandler);
	});
})(jQuery);
(function( $ ) {
	//'use strict';

	/*! Main Functionality for Settings SlideOut */
			$(document).ready(function() {
				
				$("#revslider_featured_slider_id").change(function(){
					preview_featured_slider();
				});
				preview_featured_slider();

				function preview_featured_slider(){
					var $selected_option = $("#revslider_featured_slider_id option:selected");
					
					if($selected_option.length) {

						if( $("#revslider_featured_slider_id")[0].selectedIndex > 0 ){	
							$(".featured-slider-slide").css("height","190px").css("background-color","#333");
							$("#preview_featured_slider").addClass( $selected_option.data('class') );
							$("#preview_featured_slider").attr( 'style' , $selected_option.data('style') );
							$("#featured_slider_edit_link").attr('href', 'admin.php?page=revslider&view=slide&id='+$selected_option.data('firstslide'));
							$(".featured-slider-slidenr").text($selected_option.data('slides'));
							$(".featured-slider-source").text($selected_option.data('source'));
							
							if($selected_option.data('image')!=""){
								$("#preview_featured_slider").css( 'background-image', 'url('+$selected_option.data('image')+')' );	
							} 
							$(".featured-slider-main-metas").show();
						}
						else{
							$(".featured-slider-main-metas").hide();
							$(".featured-slider-slide").css("height","40px").css("background-color","transparent");
						}
					}
				}

			}); //end document ready

})( jQuery );
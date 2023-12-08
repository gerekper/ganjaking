//Metro Layout Load
(function($) {
    'use strict';
	$(document).ready(function() {
		//Filter Category Post
		$('.list-isotope-metro').each(function() {
			var c = $(this);
			var uid=c.data("id");
			var inner_c=$('.'+uid).find(".post-inner-loop");
			$('.'+uid+' .post-filter-data').find(".filter-category-list").on('click',function(event) {
				event.preventDefault();
				var d = $(this).attr("data-filter");
				$(this).parent().parent().find(".active").removeClass("active"),
				$(this).addClass("active"),
				inner_c.isotope({
					filter: d,
					visibleStyle: { opacity: 1 }
				}),
                
                setTimeout(function() { 
                    theplus_setup_packery_portfolio(d);
                    $('.list-isotope-metro .post-inner-loop').isotope('layout').isotope("reloadItems");
                }, 20);
                setTimeout(function() { 
				    $("body").trigger("isotope-sorted");
                }, 30);
				
			});
		});
		if ($('.list-isotope-metro').length) {
			theplus_setup_packery_portfolio('*');
			$('.list-isotope-metro .post-inner-loop').isotope('layout').isotope("reloadItems");
		}
	});
	$(window).on("resize", function() {
		"use strict";
		if ($('.list-isotope-metro').length) {
			theplus_setup_packery_portfolio('*');
			$('.list-isotope-metro .post-inner-loop').isotope('layout');
		}		
	});
})(jQuery);
function theplus_backend_packery_portfolio(uid,metro_column,metro_style) {
	'use strict';
		var setPad=0,$=jQuery;
		var myWindow=$(window);
		var container=$("#"+uid);
		if ( metro_column== '4') {
			var	norm_size = Math.floor((container.width() - setPad*2)/4),
			double_size = norm_size*2;
			container.find('.grid-item').each(function(){
				var set_w = norm_size,
				set_h = norm_size;
				if(metro_style=='style-1'){
					if ($(this).hasClass('metro-item3') || $(this).hasClass('metro-item9') ) {
						set_w = double_size,
						set_h = double_size;
					}
					if ($(this).hasClass('metro-item4') || $(this).hasClass('metro-item10')) {
						set_w = double_size,
						set_h = norm_size;
					}
				}				
				if(metro_style=='style-2'){
					if ($(this).hasClass('metro-item1') || $(this).hasClass('metro-item5') || $(this).hasClass('metro-item9') || $(this).hasClass('metro-item10') ) {
						set_w = double_size,
						set_h = double_size;
					}
					if ($(this).hasClass('metro-item2') || $(this).hasClass('metro-item8')) {
						set_w = double_size,
						set_h = norm_size;
					}
				}
				if(metro_style=='style-3'){
					if ($(this).hasClass('metro-item5')) {
						set_w = double_size,
						set_h = norm_size;
					}
					if ($(this).hasClass('metro-item1')) {
						set_w = norm_size,
						set_h = double_size;
					}
					if ($(this).hasClass('metro-item3') || $(this).hasClass('metro-item6')) {
						set_w = double_size,
						set_h = double_size;
					}
				}
				if (myWindow.width() < 760) {
					set_w = myWindow.width() - setPad*2;
					set_h = myWindow.width() - setPad*2;
				}
				$(this).css({
					'width' : set_w+'px',
					'height' : set_h+'px'
				});							
			});
		}
		if (metro_column == '5') {
			var	norm_size = Math.floor((container.width() - setPad*2)/5),
			double_size = norm_size*2;				
			container.find('.grid-item').each(function(){
				var set_w = norm_size,
				set_h = norm_size;
				if(metro_style=='style-1'){
					if ($(this).hasClass('metro-item5') || $(this).hasClass('metro-item15')) {
						set_w = double_size,
						set_h = double_size;
					}
					if ($(this).hasClass('metro-item1') || $(this).hasClass('metro-item2') || $(this).hasClass('metro-item9') || $(this).hasClass('metro-item10')) {
						set_w = double_size,
						set_h = norm_size;
					}
					if ($(this).hasClass('metro-item3') || $(this).hasClass('metro-item6') || $(this).hasClass('metro-item14')) {
						set_w = norm_size,
						set_h = double_size;
					}
				}
				if (myWindow.width() < 760) {
					set_w = myWindow.width() - setPad*2;
					set_h = myWindow.width() - setPad*2;
				}
				$(this).css({
					'width' : set_w+'px',
					'height' : set_h+'px'
				});	
			});
		}
		if (metro_column == '6') {
			var	norm_size = Math.floor((container.width() - setPad*2)/6),
			double_size = norm_size*2;				
			container.find('.grid-item').each(function(){
				var set_w = norm_size,
				set_h = norm_size;
				if(metro_style=='style-1'){
					if ($(this).hasClass('metro-item1') || $(this).hasClass('metro-item5') || $(this).hasClass('metro-item9') || $(this).hasClass('metro-item10')) {
						set_w = double_size,
						set_h = double_size;
					}
					if ($(this).hasClass('metro-item2') || $(this).hasClass('metro-item7') || $(this).hasClass('metro-item14') || $(this).hasClass('metro-item15') || $(this).hasClass('metro-item16')) {
						set_w = double_size,
						set_h = norm_size;
					}
					if ($(this).hasClass('metro-item4') || $(this).hasClass('metro-item6') || $(this).hasClass('metro-item8')) {
						set_w = norm_size,
						set_h = double_size;
					}
				}
				if (myWindow.width() < 760) {
					set_w = myWindow.width() - setPad*2;
					set_h = myWindow.width() - setPad*2;
				}
				$(this).css({
					'width' : set_w+'px',
					'height' : set_h+'px'
				});	
			});
		}
		if (metro_column == '3') {
			var	norm_size = Math.floor((container.width() - setPad*2)/3),
			double_size = norm_size*2;				
			container.find('.grid-item').each(function(){
				var set_w = norm_size,
				set_h = norm_size;
				
				if(metro_style=='style-1'){
					if ($(this).hasClass('metro-item1') || $(this).hasClass('metro-item7')) {
						set_w = double_size,
						set_h = double_size;
					}
					if ($(this).hasClass('metro-item4') || $(this).hasClass('metro-item9')) {
						set_w = double_size,
						set_h = norm_size;
					}
				}else if(metro_style=='style-2'){
					if ($(this).hasClass('metro-item2')) {
						set_w = double_size,
						set_h = norm_size;
					}
					
					if ($(this).hasClass('metro-item4') || $(this).hasClass('metro-item8')) {
						set_w = norm_size,
						set_h = double_size;
					}
					if ($(this).hasClass('metro-item7')) {
						set_w = double_size,
						set_h = double_size;
					}
				}else if(metro_style=='style-3'){
					
					if ($(this).hasClass('metro-item4') || $(this).hasClass('metro-item15')) {
						set_w = double_size,
						set_h = norm_size;
					}
					if ($(this).hasClass('metro-item9')) {
						set_w = norm_size,
						set_h = double_size;
					}
					if ($(this).hasClass('metro-item10')) {
						set_w = double_size,
						set_h = double_size;
					}
				}else if(metro_style=='style-4'){
					
					if ($(this).hasClass('metro-item1')) {
						set_w = double_size,
						set_h = double_size;
					}
					if ($(this).hasClass('metro-item7')) {
						set_w = double_size,
						set_h = norm_size;
					}
					
				}
				if (myWindow.width() < 760) {
					set_w = myWindow.width() - setPad*2;
					set_h = myWindow.width() - setPad*2;
				}	
				$(this).css({
					'width' : set_w+'px',
					'height' : set_h+'px'
				});
			});
		}
			if (myWindow.innerWidth() > 767) {
				$("#"+uid).isotope({
					itemSelector: '.grid-item',
					layoutMode: 'masonry',
					masonry: {
						columnWidth: norm_size
					}
				});
			}else{
				$("#"+uid).isotope({
					layoutMode: 'masonry',
					masonry: {
						columnWidth: '.grid-item'
					}
				});
			}
		$("#"+uid).isotope('layout').isotope('layout').isotope( 'reloadItems' );
		
		$("#"+uid).imagesLoaded( function(){		
			$("#"+uid).isotope('layout').isotope( 'reloadItems' );		
		});
}


function theplus_setup_packery_portfolio(packery_id='*') {	
	var $=jQuery;    
	$('.list-isotope-metro').each(function(){
		var uid=$(this).data("id");
		var metro_column=$(this).attr('data-metro-columns');
		var tablet_metro_column=$(this).attr('data-tablet-metro-columns');
		var setPad = 0;
		var myWindow=$(window);
		var responsive_width=window.innerWidth;

        if(packery_id=='*'){
            var abc = '.grid-item';
        }else{
            var abc = '.grid-item'+packery_id;
        }		
        var i=1;

		if(responsive_width <= 1024 && tablet_metro_column!=undefined){
			metro_column=tablet_metro_column;
		}
		if ( metro_column== '4') {
			var metro_style=$(this).attr('data-metro-style');
			var	norm_size = Math.floor(($(this).width() - setPad*2)/4),
			double_size = norm_size*2;
			$(this).find(abc).each(function(){
                $(this).css("display","");     				                
					var set_w = norm_size,
					set_h = norm_size;
					if(metro_style=='style-1'){
                        if ( (abc == '.grid-item' && ($(this).hasClass('metro-item3') || $(this).hasClass('metro-item9'))) || (abc != '.grid-item' && (i==3 || i==9))) {
							set_w = double_size,
							set_h = double_size;
						}
                        if ( (abc == '.grid-item' && ($(this).hasClass('metro-item4') || $(this).hasClass('metro-item10'))) || (abc != '.grid-item' && (i==4 || i==10))) {
							set_w = double_size,
							set_h = norm_size;
						}
					}				
					if(metro_style=='style-2'){						
                            if ( (abc == '.grid-item' && ($(this).hasClass('metro-item1') || $(this).hasClass('metro-item5') || $(this).hasClass('metro-item9') || $(this).hasClass('metro-item10'))) || (abc != '.grid-item' && (i==1 || i==5 || i==9 || i==10))) {
							set_w = double_size,
							set_h = double_size;
						}
                        if ( (abc == '.grid-item' && ($(this).hasClass('metro-item2') || $(this).hasClass('metro-item8'))) || (abc != '.grid-item' && (i==2 || i==8))) {
							set_w = double_size,
							set_h = norm_size;
						}
					}
					if(metro_style=='style-3'){
                        if ( (abc == '.grid-item' && ($(this).hasClass('metro-item5'))) || (abc != '.grid-item' && (i==5))) {						
							set_w = double_size,
							set_h = norm_size;
						}
                        if ( (abc == '.grid-item' && ($(this).hasClass('metro-item1'))) || (abc != '.grid-item' && (i==1))) {
							set_w = norm_size,
							set_h = double_size;
						}
                        if ( (abc == '.grid-item' && ($(this).hasClass('metro-item3') || $(this).hasClass('metro-item6'))) || (abc != '.grid-item' && (i==3 || i==6))) {
							set_w = double_size,
							set_h = double_size;
						}
					}
					if (myWindow.width() < 760) {
						set_w = myWindow.width() - setPad*2;
						set_h = myWindow.width() - setPad*2;
					}
					$(this).css({
						'width' : set_w+'px',
						'height' : set_h+'px'
					});
                i++;							
			});
		}
		if (metro_column == '5') {
			var metro_style=$(this).attr('data-metro-style');
			var	norm_size = Math.floor(($(this).width() - setPad*2)/5),
			double_size = norm_size*2;				
			$(this).find(abc).each(function(){
                $(this).css("display","");
					var set_w = norm_size,
					set_h = norm_size;
					if(metro_style=='style-1'){
                        if ( (abc == '.grid-item' && ($(this).hasClass('metro-item5') || $(this).hasClass('metro-item15'))) || (abc != '.grid-item' && (i==5 || i==15))) {
							set_w = double_size,
							set_h = double_size;
						}
                        if ( (abc == '.grid-item' && ($(this).hasClass('metro-item1') || $(this).hasClass('metro-item2') || $(this).hasClass('metro-item9') || $(this).hasClass('metro-item10'))) || (abc != '.grid-item' && (i==1 || i==2 || i==9 || i==10))) {
							set_w = double_size,
							set_h = norm_size;
						}
                        if ( (abc == '.grid-item' && ($(this).hasClass('metro-item3') || $(this).hasClass('metro-item6') || $(this).hasClass('metro-item14'))) || (abc != '.grid-item' && (i==3 || i==6 || i==14))) {
							set_w = norm_size,
							set_h = double_size;
						}
					}
					if (myWindow.width() < 760) {
						set_w = myWindow.width() - setPad*2;
						set_h = myWindow.width() - setPad*2;
					}
					$(this).css({
						'width' : set_w+'px',
						'height' : set_h+'px'
					});
                i++;
			});
		}
		if (metro_column == '6') {
			var metro_style=$(this).attr('data-metro-style');
			var	norm_size = Math.floor(($(this).width() - setPad*2)/6),
			double_size = norm_size*2;				
			$(this).find(abc).each(function(){
                $(this).css("display","");
					var set_w = norm_size,
					set_h = norm_size;
					if(metro_style=='style-1'){
                        if ( (abc == '.grid-item' && ($(this).hasClass('metro-item1') || $(this).hasClass('metro-item5') || $(this).hasClass('metro-item9') || $(this).hasClass('metro-item10'))) || (abc != '.grid-item' && (i==1 || i==5 || i==9 || i==10))) {                            
							set_w = double_size,
							set_h = double_size;
						}
                        if ( (abc == '.grid-item' && ($(this).hasClass('metro-item2') || $(this).hasClass('metro-item7') || $(this).hasClass('metro-item14') || $(this).hasClass('metro-item15') || $(this).hasClass('metro-item16'))) || (abc != '.grid-item' && (i==2 || i==7 || i==14 || i==15 || i==16))) {
							set_w = double_size,
							set_h = norm_size;
						}
                        if ( (abc == '.grid-item' && ($(this).hasClass('metro-item4') || $(this).hasClass('metro-item6') || $(this).hasClass('metro-item8'))) || (abc != '.grid-item' && (i==4 || i==6 || i==8))) {
							set_w = norm_size,
							set_h = double_size;
						}
					}
					if (myWindow.width() < 760) {
						set_w = myWindow.width() - setPad*2;
						set_h = myWindow.width() - setPad*2;
					}
					$(this).css({
						'width' : set_w+'px',
						'height' : set_h+'px'
					});	
                i++;
			});
		}
		if (metro_column == '3') {
			var metro_style=$(this).attr('data-metro-style');
			if(responsive_width <= 1024 && tablet_metro_column!=undefined){
				metro_style=$(this).attr('data-tablet-metro-style');
			}
			var	norm_size = Math.floor(($(this).width() - setPad*2)/3),
			double_size = norm_size*2;
            		
			$(this).find(abc).each(function(){                    
                    $(this).css("display","");
                    var set_w = norm_size,
                    set_h = norm_size;				
                    if(metro_style=='style-1'){                        
                        if ( (abc == '.grid-item' && ($(this).hasClass('metro-item1') || $(this).hasClass('metro-item7'))) || (abc != '.grid-item' && (i==1 || i==7))) {
                            set_w = double_size,
                            set_h = double_size;
                        }
                        if ( (abc == '.grid-item' && ($(this).hasClass('metro-item4') || $(this).hasClass('metro-item9'))) || (abc != '.grid-item' && (i==4 || i==9))) {
                            set_w = double_size,
                            set_h = norm_size;
                        }
                    }else if(metro_style=='style-2'){
                        if ( (abc == '.grid-item' && ($(this).hasClass('metro-item2'))) || (abc != '.grid-item' && (i==2))) {
                            set_w = double_size,
                            set_h = norm_size;
                        }
                        
                        if ( (abc == '.grid-item' && ($(this).hasClass('metro-item4') || $(this).hasClass('metro-item8'))) || (abc != '.grid-item' && (i==4 || i==8))) {
                            set_w = norm_size,
                            set_h = double_size;
                        }

                        if ( (abc == '.grid-item' && ($(this).hasClass('metro-item7'))) || (abc != '.grid-item' && (i==7))) {
                            set_w = double_size,
                            set_h = double_size;
                        }
                    }else if(metro_style=='style-3'){
                        if ( (abc == '.grid-item' && ($(this).hasClass('metro-item4') || $(this).hasClass('metro-item15'))) || (abc != '.grid-item' && (i==4 || i==15))) {
                            set_w = double_size,
                            set_h = norm_size;
                        }
                        if ( (abc == '.grid-item' && ($(this).hasClass('metro-item9'))) || (abc != '.grid-item' && (i==9))) {                        
                            set_w = norm_size,
                            set_h = double_size;
                        }
                        if ( (abc == '.grid-item' && ($(this).hasClass('metro-item10'))) || (abc != '.grid-item' && (i==10))) {                        
                            set_w = double_size,
                            set_h = double_size;
                        }
                    }else if(metro_style=='style-4'){
                        if ( (abc == '.grid-item' && ($(this).hasClass('metro-item1'))) || (abc != '.grid-item' && (i==1))) {                        
                            set_w = double_size,
                            set_h = double_size;
                        }
                        if ( (abc == '.grid-item' && ($(this).hasClass('metro-item7'))) || (abc != '.grid-item' && (i==7))) {
                            set_w = double_size,
                            set_h = norm_size;
                        }
                        
                    }
                    if (myWindow.width() < 760) {
                        set_w = myWindow.width() - setPad*2;
                        set_h = myWindow.width() - setPad*2;
                    }	
                    $(this).css({
                        'width' : set_w+'px',
                        'height' : set_h+'px'
                    });	
                i++;
			});
		}
		
		if($(this).hasClass('list-isotope-metro')){
			if (myWindow.innerWidth() > 767) {
				$("#"+uid).isotope({
					itemSelector: '.grid-item',
					layoutMode: 'masonry',
					masonry: {
						columnWidth: norm_size
					}
				});
			}else{
				$("#"+uid).isotope({
					layoutMode: 'masonry',
					masonry: {
						columnWidth: '.grid-item'
					}
				});
			}
		}else{
			$("#"+uid).isotope({
				layoutMode: 'masonry',
				masonry: {
					columnWidth: norm_size
				}
			});
		}
		$("#"+uid).isotope('layout');
		
		$("#"+uid).imagesLoaded( function(){
			$("#"+uid).isotope('layout').isotope( 'reloadItems' );		
		});
				
	});
}
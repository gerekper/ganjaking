/*--load more post ajax--*/
;( function($) {
	'use strict';
	$(document).ready(function(){
		if($(".plus-gallery-load-more").length > 0){
			$(document).on("click",".plus-gallery-load-more",function(e){
				
				e.preventDefault();
				var current_click= $(this),
				a= $(this),
				page = a.attr('data-page'),
				total_page=a.data('total_page'),
				load_class= a.data('load-class'),
				layout=a.data('layout'),
				desktop_column=a.data('desktop-column'),
				tablet_column=a.data('tablet-column'),
				mobile_column=a.data('mobile-column'),
				metro_column=a.data('metro_column'),
				metro_style=a.data('metro_style'),
				responsive_tablet_metro=a.data('responsive_tablet_metro'),
				tablet_metro_column=a.data('tablet_metro_column'),
				tablet_metro_style=a.data('tablet_metro_style'),
				style=a.data('style'),
				on_load_gallery=a.data('on-load-gallery'),
				post_load_more=a.data('post_load_more'),
				animated_columns=a.data('animated_columns');
				
				var display_title=a.data('display_title'),
				post_title_tag=a.data('post_title_tag'),
				display_excerpt=a.data('display_excerpt'),
				current_text= a.text();
				var loaded_posts= a.data("loaded_posts");				
				if ( current_click.data('requestRunning') ) {
					return;
				}
				if(on_load_gallery==undefined || on_load_gallery==""){
					on_load_gallery=0;
				}
				current_click.data('requestRunning', true);
				if(total_page >= page){					
					var offset=(parseInt(page-1)*parseInt(post_load_more))+parseInt(on_load_gallery);
					$.ajax({
						type:'POST',
						data:'style='+style+'&action=theplus_gallery_post&layout='+layout+'&desktop_column='+desktop_column+'&tablet_column='+tablet_column+'&mobile_column='+mobile_column+'&offset='+offset+'&on_load_gallery='+on_load_gallery+'&animated_columns='+animated_columns+'&post_load_more='+post_load_more+'&metro_column='+metro_column+'&metro_style='+metro_style+'&responsive_tablet_metro='+responsive_tablet_metro+'&tablet_metro_column='+tablet_metro_column+'&tablet_metro_style='+tablet_metro_style+'&display_excerpt='+display_excerpt+'&paged='+page+'&display_title='+display_title,
						url:theplus_ajax_url,
						beforeSend: function() {
							$(current_click).text('Loading..');
							},success: function(data) {         
							if(data==''){
								$(current_click).addClass("hide");								
							}else{
								$("."+load_class+' .post-inner-loop').append( data );
								if(layout=='grid' || layout=='masonry'){
									if(!$("."+load_class).hasClass("list-grid-client")){
										var $newItems = $('');
										$("."+load_class+' .post-inner-loop').isotope( 'insert', $newItems );
										$("."+load_class+' .post-inner-loop').isotope( 'layout' ).isotope( 'reloadItems' ); 
									}
								}
								if ($('.list-isotope-metro').length) {
									theplus_setup_packery_portfolio("*");	
								}
								if($("."+load_class).parents(".animate-general").length){
									var c,d;
									if($("."+load_class).find(".animated-columns").length){
										var p = $("."+load_class).parents(".animate-general");
										var delay_time=p.data("animate-delay");
										var animation_stagger=p.data("animate-stagger");
										var d = p.data("animate-type");
										var animate_offset = p.data("animate-offset");
										p.css("opacity","1");
										c = p.find('.animated-columns');
										c.each(function() {
											var bc=$(this);
											bc.waypoint(function(direction) {
												if( direction === 'down'){
													if(!bc.hasClass("animation-done")){
														bc.addClass("animation-done").velocity(d,{ delay: delay_time,display:'auto'});
													}
												}
											}, {triggerOnce: true,  offset: animate_offset } );
										});
										}else{
										var b = $("."+load_class).parents(".animate-general");
										var delay_time=b.data("animate-delay");
										d = b.data("animate-type"),
										animate_offset = b.data("animate-offset"),
										b.waypoint(function(direction ) {
											if( direction === 'down'){
												if(!b.hasClass("animation-done")){
													b.addClass("animation-done").velocity(d, {delay: delay_time,display:'auto'});
												}
											}
										}, {triggerOnce: true,  offset: animate_offset } );
									}
								}
								
							}
							page++;
							if(page==total_page){
								$(current_click).addClass("hide");
								$(current_click).attr('data-page', page);
								$(current_click).parent(".ajax_load_more").append('<div class="plus-all-posts-loaded">'+loaded_posts+'</div>');
							}else{
								$(current_click).text(current_text);
								$(current_click).attr('data-page', page);
							}
							
							},complete: function() {
							if(layout=='grid' || layout=='masonry'){
								if(!$("."+load_class).hasClass("list-grid-client")){
									setTimeout(function(){	
										$("."+load_class+' .post-inner-loop').isotope( 'layout' ).isotope( 'reloadItems' );
									}, 500);
								}
							}
							if ($('.list-isotope-metro').length) {
								setTimeout(function(){	
									theplus_setup_packery_portfolio("*");	
								}, 500);
							}
							
							current_click.data('requestRunning', false);
						}
						}).then(function(){
						if(!$("."+load_class).hasClass("list-grid-client")){
							if(layout=='grid' || layout=='masonry'){
								var container = $("."+load_class+' .post-inner-loop');
								container.isotope({
									itemSelector: '.grid-item',
								});						
							}
						}
						if ($('.list-isotope-metro').length) {
							theplus_setup_packery_portfolio("*");	
						}
						
					});
				}else{
					$(current_click).addClass("hide");
				}
			});
		}
	});
})(jQuery );
/*--load more post ajax--*/
/*posts listing*/
(function( $ ) {
	"use strict";
	var WidgetThePlusHandler = function ($scope, $) {
		var wid_sec = $scope.parents('section.elementor-element,.elementor-element.e-container,.elementor-element.e-con');

		if( $scope.hasClass("elementor-widget") && $scope.find('.list-isotope').length > 0 ){
			var b = window.theplus || {};
			b.window = $(window),
			b.document = $(document),
			b.windowHeight = b.window.height(),
			b.windowWidth = b.window.width();	
			b.list_isotope_Posts = function() {
				var c = function(c) {
					var olvalue =  true;
					if ( document.dir == "rtl" ){
						olvalue =  false;
					}
                    
					$('.list-isotope',$scope).each(function() {
						var e, c = $(this), d = c.data("layout-type"),f = {
							itemSelector: ".grid-item",
							resizable: !0,
							sortBy: "original-order",
							originLeft : olvalue,
						};
						var uid=c.data("id");
						var inner_c=$('.'+uid).find(".post-inner-loop");
						$('.'+uid).addClass("pt-plus-isotope layout-" + d),
						e = "masonry" === d  ? "packery" : "fitRows",
						f.layoutMode = e,
						function() {
							//b.initMetroIsotope(),
							var generate_isotope = inner_c.isotope(f);
							generate_isotope.imagesLoaded().progress( function() {
								generate_isotope.isotope('layout');
							});
							generate_isotope.imagesLoaded().done( function() {
								setTimeout(function(){
									generate_isotope.isotope('layout');
								}, 300)
							});
							inner_c.removeClass('tp-listing-preloader');
							
							
						}();
					});
				};

				if($scope.find('.list-isotope .post-filter-data').length>0){
					//List Isotope Filter Item
					$('.list-isotope .post-filter-data').find(".filter-category-list").on('click',function(event) {
						event.preventDefault();
						var p_list = $(this).closest(".list-isotope"),uid = p_list.data("id");
						
						var d = $(this).attr("data-filter");
						$(this).parent().parent().find(".active").removeClass("active"),
						$(this).addClass("active"),
						$('.'+uid).find(".post-inner-loop").isotope({
							filter: d
						}),
						$("body").trigger("isotope-sorted");
					});
				}
				b.window.on("resize", function() {
					c('[data-enable-isotope="1"]')
				}),
				b.document.on("load resize", function() {
					c('[data-enable-isotope="1"]')
				}),
				$(document).ready(function() {
					c('[data-enable-isotope="1"]')					
				}),
				$("body").on("post-load resort-isotope", function() {
					setTimeout(function() {
						c('[data-enable-isotope="1"]')
					}, 800)
				}),
				$("body").on("tabs-reinited", function() {
					setTimeout(function() {
						c('[data-enable-isotope="1"]')
					}, 800)
				});
			},
			b.init = function() {				
				b.list_isotope_Posts();				
			},
			b.init();
		}

		if( $scope.hasClass("elementor-widget") && $scope.find('.list-isotope-metro').length > 0 ){
			$(window).on("resize", function() {
				theplus_setup_packery_portfolio("*");
				$('.list-isotope-metro .post-inner-loop').isotope('layout').isotope("reloadItems");
			});
			
			$("body").on("post-load resort-isotope", function() {
				setTimeout(function() {
					theplus_setup_packery_portfolio("*");
					$('.list-isotope-metro .post-inner-loop').isotope('layout');
				}, 800)
			});
			$("body").on("tabs-reinited", function() {
				setTimeout(function() {
					theplus_setup_packery_portfolio("*");
					$('.list-isotope-metro .post-inner-loop').isotope('layout');
				}, 800)
			});
		}

		if( $scope.hasClass("elementor-widget") && $scope.find('.gallery-list.gallery-style-3').length > 0 ){
			$('.gallery-list.gallery-style-3 .grid-item').each( function() { $(this).hoverdir(); } );
		}

		/* post listing out*/
		if( $scope.hasClass("elementor-widget") && $scope.find('.dynamic-listing.dynamic-listing-style-1,.blog-list.blog-style-1,.gallery-list.gallery-style-2').length > 0 ){
			$(document).ready(function($) {
				$(document).on('mouseenter',".dynamic-listing.dynamic-listing-style-1 .grid-item .blog-list-content,.blog-list.blog-style-1 .grid-item .blog-list-content,.gallery-list.gallery-style-2 .grid-item .gallery-list-content",function() {				
					$(this).find(".post-hover-content").slideDown(300)
				});
				$(document).on('mouseleave',".dynamic-listing.dynamic-listing-style-1 .grid-item .blog-list-content,.blog-list.blog-style-1 .grid-item .blog-list-content,.gallery-list.gallery-style-2 .grid-item .gallery-list-content",function() {
					$(this).find(".post-hover-content").slideUp(300)
				})
			});
		}
        
        /*----------------load More Post Ajax----------------*/
        if($(".post-load-more").length > 0){
			$(document).on("click", ".post-load-more", function(e){
				e.preventDefault();

				var current_click= $(this);
				var a = $(this);
				var page = a.attr('data-page') || 1;
				var total_page = a.data('total_page');
				var load_class = a.data('load-class');
				var layout = a.data('layout');
				var loadattr = a.data('loadattr');
				var offset_posts = a.data('offset-posts');
				var display_post = a.data('display_post');
				var post_load_more = a.data('post_load_more');
				var current_text = a.text();
				var loaded_posts = a.data("loaded_posts");
				var tp_loading_text = a.data("tp_loading_text");
				
				if ( current_click.data('requestRunning') ) {
					return;
				}

				if( offset_posts == undefined || offset_posts == ""){
					offset_posts = 0;
				}

				current_click.data('requestRunning', true);
				if(total_page >= page){					
					var offset=(parseInt(page-1)*parseInt(post_load_more))+parseInt(display_post)+parseInt(offset_posts);
					$.ajax({
						type:'POST',
						data:'action=theplus_more_post&offset='+offset+'&paged='+page+'&loadattr='+loadattr,
						url:theplus_ajax_url,
						beforeSend: function() {
							$(current_click).text(tp_loading_text);
						},
						success: function(data) {
							if(data==''){
								$(current_click).addClass("hide");								
							}else{
								$("."+load_class+' .post-inner-loop').append( data );

                                Resizelayout(layout, load_class);

								if( $("."+load_class).parents(".animate-general").length ){
									var c,d;
									if( $("."+load_class).find(".animated-columns").length ){
										var p = $("."+load_class).parents(".animate-general");
										var delay_time=p.data("animate-delay");
										var animation_stagger=p.data("animate-stagger");
										var d = p.data("animate-type");
										var animate_offset = p.data("animate-offset");
										var duration_time=p.data("animate-duration");
										c = p.find('.animated-columns:not(.animation-done)');
										if(p.data("animate-columns")=="stagger"){
											c.css("opacity","0");
                                            setTimeout(function(){	
                                                if(!c.hasClass("animation-done")){
                                                    c.addClass("animation-done").velocity(d,{ delay: delay_time,display:'auto',duration: duration_time,stagger: animation_stagger});
                                                }
                                            }, 500);
										}else if(p.data("animate-columns")=="columns"){
											c.css("opacity","0");
											setTimeout(function(){	
											c.each(function() {
												var bc=$(this);
												bc.waypoint(function(direction) {
													if( direction === 'down'){
														if(!bc.hasClass("animation-done")){
															bc.addClass("animation-done").velocity(d,{ delay: delay_time,duration: duration_time,drag:true,display:'auto'});
														}
													}
												}, {offset: animate_offset } );
											});
											}, 500);
										}
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
							
							var list_audio = $("."+load_class+' .post-inner-loop').find(".tp-audio-player-wrapper");
							if(list_audio.length){
								list_audio.each(function(){
									var id = $(this).data("id");
									var style = container.data('style');
									loadinitAudio($('.'+id+' .playlist li:first-child'),id,style);
								});
							}
						},
						complete: function() {
                            EqualHeightlayout();
                            Resizelayout(layout, load_class);

                            if($("."+load_class+' .post-filter-data').length){
                                $("."+load_class+' .post-filter-data .category-filters > li > a').each(function(){
                                    var filter = $(this).data("filter");
                                    if(filter!='' && filter!=undefined && filter==='*'){
                                        var totle_count = $("."+load_class+' .post-inner-loop .grid-item').length;
                                    }else if(filter!='' && filter!=undefined){
                                        var totle_count = $("."+load_class+' .post-inner-loop .grid-item'+filter).length;
                                    }
                                    if(totle_count){
                                        $(this).find(".all_post_count").text(totle_count);
                                    }
                                });
                            }

                            $('.elementor-widget-tp-row-background .elementor-widget-container').each(function(){
                                var trig = $(this).html();
                                $(this).closest('.elementor-section').prepend(trig);
                                $(this).remove();
                            });

                            current_click.data('requestRunning', false);
                        }
						}).then(function(){  

                        });
				}else{
					$(current_click).addClass("hide");
				}
			});
		}

        /*----------------lazy Load Ajax----------------*/
        // $(window).on("load", function() {
        $( document ).ready(function() {
            if($('body').find('.post-lazy-load').length>=1){
                
                var windowWidth, windowHeight, documentHeight, scrollTop, containerHeight, containerOffset, $window = $(window);
                
                var recalcValues = function() {
                    windowWidth = $window.width();
                    windowHeight = $window.height();
                    documentHeight = $('body').height();
                    containerHeight = $('.list-isotope,.list-isotope-metro').height();
                    containerOffset = $('.list-isotope,.list-isotope-metro').offset().top+50;
                    setTimeout(function(){
                        containerHeight = $('.list-isotope,.list-isotope-metro').height();
                        containerOffset = $('.list-isotope,.list-isotope-metro').offset().top+50;
                    }, 50);
                };
                
                recalcValues();
                $window.resize(recalcValues);
                
                $window.bind('scroll', function(e) {
                    
                    e.preventDefault();
                    recalcValues();
                    scrollTop = $window.scrollTop();
                    $('.list-isotope,.list-isotope-metro').each(function() {
                        containerHeight = $(this).height();
                        containerOffset = $(this).offset().top+50;
                        if($(this).find(".post-lazy-load").length && scrollTop < documentHeight && scrollTop > (containerHeight + containerOffset - windowHeight)){
                        
                            var current_click= $(this).find(".post-lazy-load");
                            var a= $(this).find(".post-lazy-load");
                                
                            var page = a.attr('data-page');
                            var total_page=a.data('total_page');
                            var load_class= a.data('load-class');
                            var layout=a.data('layout');
                            var loadattr=a.data('loadattr');
                            var offset_posts=a.data('offset-posts');
                            
                            var display_post=a.data('display_post');
                            var post_load_more=a.data('post_load_more');
                            
                            var current_text= a.html();
                            var loaded_posts= a.data("loaded_posts");
                            var tp_loading_text= a.data("tp_loading_text");
                            
                            if ( current_click.data('requestRunning') ) {
                                return;
                            }

                            if(offset_posts==undefined || offset_posts==""){
                                offset_posts=0;
                            }

                            if(total_page > page){
                                current_click.data('requestRunning', true);
                                var offset=(parseInt(page-1)*parseInt(post_load_more))+parseInt(display_post)+parseInt(offset_posts);							
                                $.ajax({
                                    type:'POST',
                                    data:'action=theplus_more_post&offset='+offset+'&paged='+page+'&loadattr='+loadattr,
                                    url:theplus_ajax_url,
                                    beforeSend: function() {
                                        $(current_click).text(tp_loading_text);
                                    },
                                    success: function(data) {         
                                        if(data==''){
                                            $(current_click).addClass("hide");										
                                        }else{
                                            $("."+load_class+' .post-inner-loop').append( data );
                                            
                                            Resizelayout(layout, load_class);
                                            
                                            if($("."+load_class).parents(".animate-general").length){
                                                var c,d;
                                                if($("."+load_class).find(".animated-columns").length){
                                                    var p = $("."+load_class).parents(".animate-general");
                                                    var delay_time=p.data("animate-delay");
                                                    var animation_stagger=p.data("animate-stagger");
                                                    var d = p.data("animate-type");
                                                    var animate_offset = p.data("animate-offset");
                                                    var duration_time=p.data("animate-duration");
                                                        c = p.find('.animated-columns:not(.animation-done)');
                                                        if(p.data("animate-columns")=="stagger"){
                                                            c.css("opacity","0");
                                                            setTimeout(function(){
                                                                if(!c.hasClass("animation-done")){
                                                                    c.addClass("animation-done").velocity(d,{ delay: delay_time,display:'auto',duration: duration_time,stagger: animation_stagger});
                                                                }
                                                            }, 500);
                                                        }else if(p.data("animate-columns")=="columns"){
                                                            c.css("opacity","0");
                                                            setTimeout(function(){	
                                                            c.each(function() {
                                                                var bc=$(this);
                                                                bc.waypoint(function(direction) {
                                                                    if( direction === 'down'){
                                                                        if(!bc.hasClass("animation-done")){
                                                                            bc.addClass("animation-done").velocity(d,{ delay: delay_time,duration: duration_time,drag:true,display:'auto'});
                                                                        }
                                                                    }
                                                                }, {offset: animate_offset } );
                                                            });
                                                            }, 500);
                                                        }
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
                                            $(current_click).parent(".ajax_lazy_load").append('<div class="plus-all-posts-loaded">'+loaded_posts+'</div>');
                                        }else{
                                            $(current_click).html(current_text);
                                            $(current_click).attr('data-page', page);	
                                        }
                                        
                                        var list_audio = $("."+load_class+' .post-inner-loop').find(".tp-audio-player-wrapper");
                                        if(list_audio.length){
                                            list_audio.each(function(){
                                                var id = $(this).data("id");
                                                var style = container.data('style');
                                                loadinitAudio($('.'+id+' .playlist li:first-child'),id,style);
                                            });
                                        }
                                        
                                    },
                                    complete: function() {
                                        EqualHeightlayout();
                                        Resizelayout(layout, load_class);

                                        if($("."+load_class+' .post-filter-data').length){
                                            $("."+load_class+' .post-filter-data .category-filters > li > a').each(function(){
                                                var filter = $(this).data("filter");
                                                if(filter!='' && filter!=undefined && filter==='*'){
                                                    var totle_count = $("."+load_class+' .post-inner-loop .grid-item').length;
                                                }else if(filter!='' && filter!=undefined){
                                                    var totle_count = $("."+load_class+' .post-inner-loop .grid-item'+filter).length;
                                                }
                                                if(totle_count){
                                                    $(this).find(".all_post_count").text(totle_count);
                                                }
                                            });
                                        }

                                        $('.elementor-widget-tp-row-background .elementor-widget-container').each(function(){
                                            var trig = $(this).html();
                                            $(this).closest('.elementor-section').prepend(trig);
                                            $(this).remove();
                                        });

                                        if ($("."+load_class+'.list-isotope-metro .post-inner-loop').length > 0) {
                                            var container=$("."+load_class);
                                            var uid=container.data("id");
                                            var columns=container.attr('data-metro-columns');
                                            var metro_style=container.attr('data-metro-style');
                                            theplus_backend_packery_portfolio(uid,columns,metro_style);
                                            $("."+load_class+'.list-isotope-metro .post-inner-loop').isotope('layout').isotope( 'reloadItems' );
                                        }

                                        current_click.data('requestRunning', false);
                                    }
                                    }).then(function(){
                                        if ($("."+load_class+'.list-isotope-metro .post-inner-loop').length > 0) {
                                            var container=$("."+load_class);
                                            var uid=container.data("id");
                                            var columns=container.attr('data-metro-columns');
                                            var metro_style=container.attr('data-metro-style');
                                            theplus_backend_packery_portfolio(uid,columns,metro_style);
                                            $("."+load_class+'.list-isotope-metro .post-inner-loop').isotope('layout').isotope( 'reloadItems' );
                                        }
                                });
                            }else{
                                $(current_click).addClass("hide");
                            }					
                        }
                    });
                });
            }
        });

        var EqualHeightlayout = function() {
			var Equalcontainer = jQuery('.elementor-element[data-tp-equal-height-loadded]');
			if( Equalcontainer.length > 0 ){
				EqualHeightsLoadded();
			}
		}

        var Resizelayout = function(layout, load_class) {
            if( layout == 'grid' || layout == 'masonry' ){
                var container = $("."+load_class+' .post-inner-loop');
                    container.isotope({
                        itemSelector: '.grid-item',
                    });

                if( !$("."+load_class).hasClass("list-grid-client") ){
                    setTimeout(function(){	
                        $("."+load_class+' .post-inner-loop').isotope( 'reloadItems' ).isotope();
                    }, 500);
                }
            }

            if( layout == 'metro' ){
                if ( $("."+load_class+'.list-isotope-metro .post-inner-loop').length > 0) {
                    var container = $("."+load_class),
                        uid = container.data("id"),
                        columns = container.attr('data-metro-columns'),
                        metro_style = container.attr('data-metro-style');

                        theplus_backend_packery_portfolio(uid,columns, metro_style);
                }
            }
        }

	};

	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/global', WidgetThePlusHandler);
	});

})(jQuery);

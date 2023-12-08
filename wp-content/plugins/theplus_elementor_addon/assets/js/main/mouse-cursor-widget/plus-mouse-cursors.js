/*mouse cursor widget*/(function ($) {
	"use strict";
	var WidgetMouseCursorHandler = function( $scope, $ ) {
		var container = $scope.find('.tp-mouse-cursor-wrapper'),
		pointer = container.data('plus-mc-settings');

		if(pointer.effect!='' && pointer.effect!=undefined){
			if(pointer.effect=='mc-column'){
				var effectclass = container.closest('.elementor-column'),
				ClsId = effectclass.data('id'),
				FClsId = $('.elementor-element-'+ClsId);
			}else if(pointer.effect=='mc-section'){
				var effectclass = container.closest('.elementor-section.elementor-top-section'),
				ClsId = effectclass.data('id'),
				FClsId = $('.elementor-element-'+ClsId); 					
			}else if(pointer.effect=='mc-container'){
				var effectclass = container.closest('.e-container,.e-con'),
				ClsId = effectclass.data('id'),
				FClsId = $('.elementor-element-'+ClsId); 					
			}else if(pointer.effect=='mc-widget'){
				var effectclass = container.closest('.elementor-element').prev('.elementor-element'),
				ClsId = effectclass.data('id'),				
				FClsId = $('.elementor-element-'+ClsId);
				if(pointer.type != undefined && pointer.type == 'mouse-follow-image'){				
					if(pointer.mc_cursor_icon !=undefined && pointer.mc_cursor_icon !=''){
						FClsId.append( "<img src='"+pointer.mc_cursor_icon+"' alt='Cursor Icon' class='plus-cursor-pointer-follow'>" );	
						var imgmaxwidth = pointer.mc_cursor_adjust_width;
						$('.plus-cursor-pointer-follow').css('max-width',imgmaxwidth);
					}
				}
				if(pointer.type != undefined && pointer.type == 'mouse-follow-text'){
					if(pointer.mc_cursor_text !=undefined && pointer.mc_cursor_text !=''){
				      FClsId.append( "<div class='plus-cursor-pointer-follow-text'>"+pointer.mc_cursor_text+"</div>" );	
				    }
			    }	
			    if(pointer.type != undefined && pointer.type == 'mouse-follow-circle'){
					if(pointer.circle_type != undefined && pointer.circle_type == 'cursor-predefine' || pointer.circle_type == 'cursor-custom' ){
				      FClsId.append( "<div class='plus-cursor-follow-circle'></div>" );	
				       var custmarrow = pointer.mc_cursor_adjust_symbol;
						$('.plus-cursor-follow-circle').css('cursor',custmarrow);
						if (pointer.mc_cursor_adjust_style != undefined && pointer.mc_cursor_adjust_style == 'mc-cs3') {
							var stlcustmblend = pointer.style_two_blend_mode;
					   	  $('.plus-cursor-follow-circle').css('mix-blend-mode',stlcustmblend).css('z-index',999);
					    }
					    if (pointer.mc_cursor_adjust_style != undefined && pointer.mc_cursor_adjust_style == 'mc-cs1' || pointer.mc_cursor_adjust_style == 'mc-cs3') {
					    	var stlcustmbgall = pointer.style_two_bg,	
					    	stlcustmbgallh = pointer.style_two_bgh;	
							$('.plus-cursor-follow-circle').css('background-color',stlcustmbgall);
							
							$("a").on("mouseenter",function(){
								$('.tp-mouse-hover-active .plus-cursor-follow-circle').css('background-color',stlcustmbgallh);
							});

							$("a").on("mouseleave",function(){
								$('.plus-cursor-follow-circle').css('background-color',stlcustmbgall);
							});
					    }
					    if (pointer.mc_cursor_adjust_style != undefined && pointer.mc_cursor_adjust_style == 'mc-cs1' || pointer.mc_cursor_adjust_style == 'mc-cs2' || pointer.mc_cursor_adjust_style == 'mc-cs3') {
					    	var cursor = $('.plus-cursor-follow-circle');
					    	var selctcircletag = pointer.circle_style_tag_selector;
					    	var crcltransferNml = pointer.mc_circle_transformNml;						
							var crcltransferHvr = pointer.mc_circle_transformHvr;
							var crcltransitionNml = pointer.mc_circle_transitionNml;			
							var crcltransitionHvr = pointer.mc_circle_transitionHvr;			
					    	$(selctcircletag).hover(function(){ 
						      cursor.css({ transform: crcltransferHvr}).css({ transition: 'transform '+ crcltransitionHvr +'s ease'});
						      }, function(){
						      	cursor.css({ transform: crcltransferNml}).css({ transition: 'transform '+ crcltransitionNml +'s ease'});						      
							});
					    }					    
				    }
			    }									
			}else if(pointer.effect=='mc-body'){
				$('body').attr('data-id','tpmcbody').addClass('elementor-element-tpmcbody');
				var effectclass = container.closest('.elementor-element-tpmcbody'),
				ClsId = effectclass.data('id'),			
				FClsId = $('.elementor-element-'+ClsId);							
			}
			
			if (pointer.mc_cursor_adjust_style != undefined && pointer.mc_cursor_adjust_style == 'mc-cs1' || pointer.mc_cursor_adjust_style == 'mc-cs2' || pointer.mc_cursor_adjust_style == 'mc-cs3') {
				$("a").on("mouseenter",function(){
					effectclass.addClass("tp-mouse-hover-active");
				});

				$("a").on("mouseleave",function(){					
					effectclass.removeClass("tp-mouse-hover-active");
				});
			}
		}
		
		if($(window).width() > 991){				       
			if(container){		      
				var  datatostr = JSON.stringify(pointer);
				$(effectclass).attr("data-plus-mc-settings",datatostr);				
				if(pointer.type != undefined && pointer.type == 'mouse-cursor-icon'){

					
					if(pointer.icon_type !=undefined && pointer.icon_type =='cursor-Icon-predefine'){
						
						if(pointer.mc_cursor_icon !=undefined && pointer.mc_cursor_icon !=''){
						    $('head').append('<style type="text/css">.elementor-element-'+ClsId+',.elementor-element-'+ClsId+' *,.elementor-element-'+ClsId+' *:hover{cursor: '+pointer.mc_cursor_icon+';}</style>');						
						}
					}
					if(pointer.icon_type !=undefined && pointer.icon_type =='cursor-Icon-custom'){
						if(pointer.mc_cursor_icon !=undefined && pointer.mc_cursor_icon !=''){
							var is_hover = '';
							if(pointer.mc_cursor_see_more!=undefined && pointer.mc_cursor_see_more =='yes' && pointer.mc_cursor_see_icon!=''){
								var is_hover = '.elementor-element-'+ClsId+' a,.elementor-element-'+ClsId+' a *,.elementor-element-'+ClsId+' a *:hover{cursor: -webkit-image-set(url('+pointer.mc_cursor_see_icon+') 2x) 0 0,pointer !important;cursor: url('+pointer.mc_cursor_see_icon+'),auto !important;}';						
						    }
						    $('head').append('<style type="text/css">.elementor-element-'+ClsId+',.elementor-element-'+ClsId+' *,.elementor-element-'+ClsId+' *:hover{cursor: -webkit-image-set(url('+pointer.mc_cursor_icon+') 2x) 0 0,pointer;cursor: url('+pointer.mc_cursor_icon+'),auto ;}'+is_hover+'</style>');						
						}
					}					
				}else if(pointer.type != undefined && pointer.type == 'mouse-follow-image'){
					if(pointer.mc_cursor_icon !=undefined && pointer.mc_cursor_icon !=''){
						if(pointer.effect!='mc-widget'){						
						  $scope.append('<img src="'+pointer.mc_cursor_icon+'" alt="Cursor Icon" class="plus-cursor-pointer-follow">');
						}					   						
						$(effectclass).mouseenter(function() {							
							var enterthis = $(this);			
							enterthis.addClass( "cursor-active" );							
							var is_pointer=enterthis.data('plus-mc-settings');
							var leftoffset = 0;
							if(is_pointer.mc_cursor_adjust_left !=undefined && is_pointer.mc_cursor_adjust_left !=''){
								leftoffset = is_pointer.mc_cursor_adjust_left;
							}
							var topoffset = 0;
							if(is_pointer.mc_cursor_adjust_top !=undefined && is_pointer.mc_cursor_adjust_top !=''){
								topoffset = is_pointer.mc_cursor_adjust_top;
							}
							$(document).mousemove(function(e){
								$('.plus-cursor-pointer-follow',this).offset({
									left: e.pageX + leftoffset,
									top: e.pageY + topoffset
								});
							});
							
							if(is_pointer.mc_cursor_see_more!=undefined && is_pointer.mc_cursor_see_more =='yes' && is_pointer.mc_cursor_see_icon!=''){
								$('a',this).hover(function(){
								  enterthis.find(".plus-cursor-pointer-follow").attr("src",is_pointer.mc_cursor_see_icon);
								}, function(){
								   enterthis.find(".plus-cursor-pointer-follow").attr("src",is_pointer.mc_cursor_icon);
								});									  	
							}							
						});
						$(effectclass).mouseleave(function() {
							$(this).removeClass( "cursor-active" );
						});
					}				
			    }else if(pointer.type != undefined && pointer.type == 'mouse-follow-text'){				
					if(pointer.mc_cursor_text !=undefined && pointer.mc_cursor_text !=''){
						if(pointer.effect!='mc-widget'){
						  $scope.append('<div class="plus-cursor-pointer-follow-text">'+pointer.mc_cursor_text+'</div>');
						}					  							
						$(effectclass).mouseenter(function() {							
							var enterthis = $(this);	
							enterthis.addClass( "cursor-active" );
							var wdh = $('.plus-cursor-pointer-follow-text',this).outerWidth();
							var hgt = $('.plus-cursor-pointer-follow-text',this).outerHeight();
							
							var is_pointer=enterthis.data('plus-mc-settings');
							var leftoffset = 0;
							if(is_pointer.mc_cursor_adjust_left !=undefined && is_pointer.mc_cursor_adjust_left !=''){
								leftoffset = is_pointer.mc_cursor_adjust_left;
							}
							var topoffset = 0;
							if(is_pointer.mc_cursor_adjust_top !=undefined && is_pointer.mc_cursor_adjust_top !=''){
								var topoffset = is_pointer.mc_cursor_adjust_top;
							}							                           
							$(document).mousemove(function(e){
								$('.plus-cursor-pointer-follow-text',this).offset({
									left: e.pageX + leftoffset - (wdh/2),
									top: e.pageY + topoffset - (hgt/2)
								});
							});
							 							
							if(is_pointer.mc_cursor_see_more!=undefined && is_pointer.mc_cursor_see_more =='yes' && is_pointer.mc_cursor_see_text!=''){							
							    $('a',this).hover(function(){
								   enterthis.find(".plus-cursor-pointer-follow-text").text(is_pointer.mc_cursor_see_text);
								    }, function(){
								   enterthis.find(".plus-cursor-pointer-follow-text").text(is_pointer.mc_cursor_text);
								});
							}							
						});
						$(effectclass).mouseleave(function() {
							$(this).removeClass( "cursor-active" );							
						});
					}					
				}else if(pointer.type != undefined && pointer.type == 'mouse-follow-circle' && !elementorFrontend.isEditMode()){
					if (pointer.effect!='mc-widget') {
					    if (pointer.circle_type != undefined && pointer.circle_type == 'cursor-predefine' || pointer.circle_type == 'cursor-custom') {
						    $scope.append('<div class="plus-cursor-follow-circle"></div>');
						    if(pointer.effect=='mc-body'){		     
                                if (pointer.mc_cursor_adjust_style != undefined && pointer.mc_cursor_adjust_style == 'mc-cs2') {   
                                 $('.plus-cursor-follow-circle').addClass("plus-percent-circle");
								 $('.plus-cursor-follow-circle').append('<svg class="plus-mc-svg-circle" width="200" height="200" viewport="0 0 100 100" xmlns="https://www.w3.org/2000/svg"><circle class="plus-mc-circle-st1" cx="100" cy="100" r="'+pointer.mc_2_first_circle_size+'"></circle><circle class="plus-mc-circle-st1 plus-mc-circle-progress-bar" cx="100" cy="100" r="'+pointer.mc_2_second_circle_size+'"></circle></svg>');
								    var stlonecircle = pointer.style_one_crcle_bg,
								    stlonepgcircle = pointer.style_one_crcle_prog_bg,
									stlonecircleh = pointer.style_one_crcle_bgh,
								    stlonepgcircleh = pointer.style_one_crcle_prog_bgh;	
								 $('circle').css('stroke',stlonecircle);
								 $('.plus-mc-circle-progress-bar').css('stroke',stlonepgcircle);
									$("a").on("mouseenter",function(){
										 $('.tp-mouse-hover-active circle').css('stroke',stlonecircleh);
										 $('.tp-mouse-hover-active .plus-mc-circle-progress-bar').css('stroke',stlonepgcircleh);
									});

									$("a").on("mouseleave",function(){
										$('circle').css('stroke',stlonecircle);
										$('.plus-mc-circle-progress-bar').css('stroke',stlonepgcircle);
									});
									
								    window.onload = function() {  
								  	  const crclcursor = document.querySelector('.plus-cursor-follow-circle');
									  const svg = document.querySelector('.plus-mc-svg-circle'); //svg
									  const progressBar = document.querySelector('.plus-mc-circle-progress-bar');
									  const totalLength = progressBar.getTotalLength();
									    setTopValue(svg);  
										progressBar.style.strokeDasharray = totalLength;
										progressBar.style.strokeDashoffset = totalLength;
										window.addEventListener('scroll', () => {
										     setProgress(crclcursor, progressBar, totalLength);
										}); 
									    window.addEventListener('resize', () => {
									      setTopValue(svg);
									    });
								    }
								    function setTopValue(svg) {
									  svg.style.top = document.documentElement.clientHeight * 0.5 - (svg.getBoundingClientRect().height * 0.5) + 'px';
									}
									 function setProgress(crclcursor, progressBar, totalLength) {
										const clientHeight = document.documentElement.clientHeight;
									    const scrollHeight = document.documentElement.scrollHeight;
									    const scrollTop = document.documentElement.scrollTop;
									    const percentage = scrollTop / (scrollHeight - clientHeight);
									    if(percentage === 1) {
										    crclcursor.classList.add('mc-circle-process-done');
										} else {
										    crclcursor.classList.remove('mc-circle-process-done');
										}
                                        progressBar.style.strokeDashoffset = totalLength - totalLength * percentage;
									} 
                                }
                            }
                           
						    if (pointer.mc_cursor_adjust_style != undefined && pointer.mc_cursor_adjust_style == 'mc-cs1' || pointer.mc_cursor_adjust_style == 'mc-cs3') {
                                $('.plus-cursor-follow-circle').css('background-color',pointer.style_two_bg);
								$("a").on("mouseenter",function(){
									 $('.tp-mouse-hover-active .plus-cursor-follow-circle').css('background-color',pointer.style_two_bgh);
							    });

								$("a").on("mouseleave",function(){
									$('.plus-cursor-follow-circle').css('background-color',pointer.style_two_bg);
								});								
						    }
						    if (pointer.mc_cursor_adjust_style != undefined && pointer.mc_cursor_adjust_style == 'mc-cs1' || pointer.mc_cursor_adjust_style == 'mc-cs2' || pointer.mc_cursor_adjust_style == 'mc-cs3') {
						    	var cursor = $('.plus-cursor-follow-circle');
						    	var selctcircletag = pointer.circle_style_tag_selector;
						    	var crcltransferNml = pointer.mc_circle_transformNml;			
								var crcltransferHvr = pointer.mc_circle_transformHvr;	
									
						     	$(selctcircletag).hover(function(){ 
							       cursor.css({ transform: crcltransferHvr });
							       }, function(){
							      cursor.css({ transform: crcltransferNml });
								});									
						    }			    
					    }
				    }
					if(pointer.circle_type == 'cursor-custom'){
                        $('.plus-cursor-follow-circle').css('pointer-events','none');
                        if(pointer.mc_cursor_icon !=undefined && pointer.mc_cursor_icon !=''){
	                    	var is_circle = '';
							if(pointer.mc_cursor_see_more!=undefined && pointer.mc_cursor_see_more =='yes' && pointer.mc_cursor_see_icon!=''){
								var is_circle = '.elementor-element-'+ClsId+' a,.elementor-element-'+ClsId+' a *,.elementor-element-'+ClsId+' a *:hover{cursor: -webkit-image-set(url('+pointer.mc_cursor_see_icon+') 2x) 0 0,pointer !important;cursor: url('+pointer.mc_cursor_see_icon+'),auto !important;}';
							}
							$('head').append('<style type="text/css">.elementor-element-'+ClsId+',.elementor-element-'+ClsId+' *,.elementor-element-'+ClsId+' *:hover{cursor: -webkit-image-set(url('+pointer.mc_cursor_icon+') 2x) 0 0,pointer ;cursor: url('+pointer.mc_cursor_icon+'),auto;}'+is_circle+'</style>');
		                }       
					}					
				    $(effectclass).mouseenter(function() {							
						var enterthis = $(this);		
						enterthis.addClass( "cursor-active" );							
						var wdh = $('.plus-cursor-follow-circle',this).outerWidth();
						var hgt = $('.plus-cursor-follow-circle',this).outerHeight();
						
						var is_pointer=enterthis.data('plus-mc-settings');
						var leftoffset = 0;
						if(is_pointer.mc_cursor_adjust_left !=undefined && is_pointer.mc_cursor_adjust_left !=''){
							leftoffset = is_pointer.mc_cursor_adjust_left;
						}
						var topoffset = 0;
						if(is_pointer.mc_cursor_adjust_top !=undefined && is_pointer.mc_cursor_adjust_top !=''){
							var topoffset = is_pointer.mc_cursor_adjust_top;
						}							                           
						$(document).mousemove(function(e){
							$('.plus-cursor-follow-circle',this).offset({
								left: e.pageX + leftoffset - (wdh/2),
								top: e.pageY + topoffset - (hgt/2)
							});
						});
					});
					$(effectclass).mouseleave(function() {
						var outthis = $(this);
						outthis.removeClass( "cursor-active" );
					});
				}
			}
		}
	}
	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/tp-mouse-cursor.default', WidgetMouseCursorHandler );
	});
})(jQuery);
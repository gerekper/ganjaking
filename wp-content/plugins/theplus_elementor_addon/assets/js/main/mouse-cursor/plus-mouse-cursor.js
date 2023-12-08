(function ($) {
	"use strict";
	var Plus_Column_Cursor_Pointer = function( $scope, $ ) {
		var columnId = $scope.data( 'id' );
		var container = $('.plus_cursor_pointer');
		
		if($(window).width() > 991){
			if($scope.hasClass('plus_cursor_pointer')){
			
				var cur_col = $('.plus_column_'+columnId);
				var pointer = $scope.data('plus-cursor-settings');
				
				if(pointer.style != undefined && pointer.style == 'cursor-icon'){
				
					if(pointer.cursor_icon !=undefined && pointer.cursor_icon !=''){
						var is_hover = '';
						if(pointer.cursor_see_more!=undefined && pointer.cursor_see_more =='yes' && pointer.cursor_see_icon!=''){
							var is_hover = '.plus_column_'+columnId+'.plus-cursor-icon a,.plus_column_'+columnId+'.plus-cursor-icon a *,.plus_column_'+columnId+'.plus-cursor-icon a *:hover{cursor: -webkit-image-set(url('+pointer.cursor_see_icon+') 2x) 0 0,pointer !important;cursor: url('+pointer.cursor_see_icon+'),auto !important;}';
						}
						$('head').append('<style type="text/css">.plus_column_'+columnId+',.plus_column_'+columnId+' *,.plus_column_'+columnId+' *:hover{cursor: -webkit-image-set(url('+pointer.cursor_icon+') 2x) 0 0,pointer !important;cursor: url('+pointer.cursor_icon+'),auto !important;}'+is_hover+'</style>');
					}
					
				}else if(pointer.style != undefined && pointer.style == 'follow-image'){
				
					if(pointer.cursor_icon !=undefined && pointer.cursor_icon !=''){
						$scope.append('<img src="'+pointer.cursor_icon+'" alt="Cursor Icon" class="plus-cursor-pointer-follow">');
						$(".plus_cursor_pointer.plus-follow-image").mouseenter(function() {
							
							var enterthis = $(this);
							$(".plus_cursor_pointer").removeClass("cursor-active");
							enterthis.addClass( "cursor-active" );
							
							var is_pointer=enterthis.data('plus-cursor-settings');
							var leftoffset = 0;
							if(is_pointer.cursor_adjust_left !=undefined && is_pointer.cursor_adjust_left !=''){
								leftoffset = is_pointer.cursor_adjust_left;
							}
							var topoffset = 0;
							if(is_pointer.cursor_adjust_top !=undefined && is_pointer.cursor_adjust_top !=''){
								topoffset = is_pointer.cursor_adjust_top;
							}
							
							$(document).mousemove(function(e){
								$('.plus-cursor-pointer-follow',this).offset({
									left: e.pageX + leftoffset,
									top: e.pageY + topoffset
								});
							});
							
							if(is_pointer.cursor_see_more!=undefined && is_pointer.cursor_see_more =='yes' && is_pointer.cursor_see_icon!=''){
							
								$('a',this).on("hover",function(){
								enterthis.find(".plus-cursor-pointer-follow").attr("src",is_pointer.cursor_see_icon);
								}, function() {
									enterthis.find(".plus-cursor-pointer-follow").attr("src",is_pointer.cursor_icon);
								});
								
							}
							
						}).mouseleave(function() {
							$(this).removeClass( "cursor-active" );
						});
					}
					
				}else if(pointer.style != undefined && pointer.style == 'follow-text'){
				
					if(pointer.cursor_text !=undefined && pointer.cursor_text !=''){
						$scope.append('<div class="plus-cursor-pointer-follow-text">'+pointer.cursor_text+'</div>');
						
						$(".plus_cursor_pointer.plus-follow-text").mouseenter(function() {
							
							var enterthis = $(this);
							$(".plus_cursor_pointer").removeClass("cursor-active");
							
							enterthis.addClass( "cursor-active" );
							var wdh = $('.plus-cursor-pointer-follow-text',this).outerWidth();
							var hgt = $('.plus-cursor-pointer-follow-text',this).outerHeight();
							
							var is_pointer=enterthis.data('plus-cursor-settings');
							var leftoffset = 0;
							if(is_pointer.cursor_adjust_left !=undefined && is_pointer.cursor_adjust_left !=''){
								leftoffset = is_pointer.cursor_adjust_left;
							}
							var topoffset = 0;
							if(is_pointer.cursor_adjust_top !=undefined && is_pointer.cursor_adjust_top !=''){
								var topoffset = is_pointer.cursor_adjust_top;
							}
							
							$(document).mousemove(function(e){
								$('.plus-cursor-pointer-follow-text',this).offset({
									left: e.pageX + leftoffset - (wdh/2),
									top: e.pageY + topoffset - (hgt/2)
								});
							});
							
							
							if(is_pointer.cursor_see_more!=undefined && is_pointer.cursor_see_more =='yes' && is_pointer.cursor_see_text!=''){
							
								$('a',this).on("hover",function(){
									enterthis.find(".plus-cursor-pointer-follow-text").text(is_pointer.cursor_see_text);
								}, function() {
									enterthis.find(".plus-cursor-pointer-follow-text").text(is_pointer.cursor_text);
								});
								
							}
							
						}).mouseleave(function() {
						
							$(this).removeClass( "cursor-active" );
							
						});
					}
					
				}
				
			}
		}
	}
	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/column', Plus_Column_Cursor_Pointer );
	});
})(jQuery);
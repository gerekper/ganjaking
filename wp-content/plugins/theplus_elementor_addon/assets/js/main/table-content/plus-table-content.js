/*Table Of Content*/
(function ($) {
	"use strict";
	var WidgetTableOfContentHandler = function($scope, $) {
		var container = $scope.find('.tp-table-content');

		 if(container.length){
				var settings = container.data('settings');
				tocbot.init({
					...settings
				});
				
			if( $scope.find('.tp-table-content.tp-toc-hash-tag').length ){
				var conselector = settings['contentSelector'],
				headselector = settings['headingSelector'],
				hashtagtext = settings['hashtagtext'],
				copyText = settings['copyText']
				var strarray = headselector.split(',');
				for (var i = 0; i < strarray.length; i++) {
					$(conselector+' '+strarray[i]).each(function(){
						var id = $(this).attr('id');
						if( $scope.find('.tp-table-content.tp-toc-hash-tag.tp-toc-hash-tag-hover').length ){
                            if(copyText){
                                var data = '<a href="#'+id+'" class="tp-toc-hash-tag tp-on-hover">'+hashtagtext+'</a><span class="tp-copy-hash" style="opacity: 0;">Copied</span>';
                            }else{
                                var data = '<a href="#'+id+'" class="tp-toc-hash-tag tp-on-hover">'+hashtagtext+'</a>';
                            }
						}else{
                            if(copyText){
                                var data = '<a href="#'+id+'" class="tp-toc-hash-tag">'+hashtagtext+'</a><span class="tp-copy-hash" style="opacity: 0;">Copied</span>';
                            }else{
                                var data = '<a href="#'+id+'" class="tp-toc-hash-tag">'+hashtagtext+'</a>';
                            }
						}

						$(this).append(data);
					});
				}

				$('.tp-toc-hash-tag').on('click',function (e) {
					e.preventDefault();
					$(this).next().css( "opacity", "1" );
					var toccopyText = $(this).attr('href'),
					winurl = window.location.href+toccopyText;
					var $temp = $('<input style="height:0">');
					$("body").append($temp);
					$temp.val(winurl).select();
					document.execCommand("copy");
					$temp.remove();
					setTimeout(function(){ $('.tp-copy-hash').css( "opacity", "0" ); }, 1500);
					
				});
			}
			
			if( $scope.find('.tp-table-content .table-toggle-wrap').length ){				
				$('.table-toggle-wrap').each(function(){
					var defaultToggle = $(this).data('default-toggle');
					var Width = window.innerWidth;

					if((Width>1200 && defaultToggle.md) || (Width<1201 && Width>=768 && defaultToggle.sm) || (Width<768 && defaultToggle.xs)){
						$( this ).addClass( "active" );
						$('.tp-toc',this ).slideDown(500);
						
					}else{
						$( this ).removeClass( "active" );
						$('.tp-toc', this ).slideUp(500);
					}					                                                
					$('.tp-toc-heading',this).on('click',function(){
						var togglewrap = $(this).closest('.table-toggle-wrap');
						if(togglewrap.hasClass('active')){
							togglewrap.removeClass( "active" );
							togglewrap.find('.tp-toc').slideUp(500);
							togglewrap.find('.table-toggle-icon').empty();
							togglewrap.find('.table-toggle-icon').html($(this).closest('.tp-toc-wrap').data("close"));
						}else{
							togglewrap.addClass( "active" );
							togglewrap.find('.tp-toc').slideDown(500);
							togglewrap.find('.table-toggle-icon').empty();
							togglewrap.find('.table-toggle-icon').html($(this).closest('.tp-toc-wrap').data("open"));
						}
					});
				});
			}
		}
	};
	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/tp-table-content.default', WidgetTableOfContentHandler);
	});
})(jQuery);
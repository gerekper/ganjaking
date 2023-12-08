/*Bss Filter*/(function($) {
	"use strict";
	$(document).ready(function(){
		var bss_news = $(".bss-list.bss-news");
		if(bss_news.length > 0){
			
			bss_news.each(function(){
				var filter_data= $(this).find(".post-filter-data"),
					filter= $(this).find(".showcase-filter"),
					category = filter.find('.category-filters');
					
				if(filter.length > 0){
					
					var item_width = 0;
					item_width = category.width();
					item_width += filter_data.find('.bss-nxt-prv-icn').width();
					item_width += $(this).find('.post-filter-label').width();
					
					if ( ( item_width + 1 ) > filter_data.width() ) {
						
						if(!category.find('.extrabssli').length){
							category.append( '<li class="extrabssli"><div class="extrabss-toggle"><i class="fas fa-ellipsis-h"></i></div><ul class="extrabssul"></ul></li>' );
						}
						
						var nav_width = filter_data.width();
						var cate_count = category.find('> li').length;
						
						for(var i = 0; i < cate_count; i++) {
							var item_width=0;
							category.find('> li').each(function(){
								item_width += $(this).outerWidth( true );
							});
							if ( nav_width < ( item_width + 10 ) ){
								category.find('> li').not('.extrabssli').last().appendTo(filter.find('.extrabssul'));
							}
						}
						
					}
				}
			});
			
			$(window).on('resize',function(){
				bss_news.each(function(){
					
					var filter_data= $(this).find(".post-filter-data"),
						filter= $(this).find(".showcase-filter"),
						category = filter.find('.category-filters');
					
					var nav_width = filter_data.width();
					var item_width = 0;
					
					category.find('> li').each(function(){
						item_width += $(this).outerWidth( true );
					});
					item_width += filter_data.find('.bss-nxt-prv-icn').width();
					item_width += $(this).find('.post-filter-label').width();
					
					var last_child = category.find('> li').not('.extrabssli').last().width();
					if (nav_width<(item_width)){
						if(!category.find('.extrabssli').length){
							category.append( '<li class="extrabssli"><div class="extrabss-toggle"><i class="fas fa-ellipsis-h"></i></div><ul class="extrabssul"></ul></li>' );
						}
						category.find('> li').not('.extrabssli').last().appendTo(filter.find('.extrabssul'));
					}

					if (nav_width > (item_width + (last_child-1))){
						filter.find('.extrabssul li').last().appendTo(category);
						category.find('.extrabssli').appendTo(category);
						
						if(!filter.find('.extrabssul li').length){
							category.find('.extrabssli').remove();
						}
					}					
					
				});
			});
			
			bss_news.find('.extrabss-toggle').on('click',function(){
				$(this).closest('.extrabssli').find(".extrabssul").slideToggle(); 
			});
		}
	});
	
	$(document).ready(function(){
		/*bss News Filter*/
		var bss_news = $(".bss-list.bss-news");
		if(bss_news.length > 0){
		
			var filter_data = bss_news.find('.post-filter-data');
			var grid_item = bss_news.find('.post-inner-loop .grid-item');
			var filter_click = filter_data.find(".category-filters li a");
			var currentPage=1;
			$(filter_click).on('click', function(e){
				e.preventDefault();
				var bss_news = $(this).closest('.bss-news');
				var limitPerPage =bss_news.data('newslooppage');
								
				$(this).closest('.category-filters').find(".filter-category-list").removeClass('active');
				$(this).addClass('active');
				
				var get_filter = $(this).data("filter").split('.').join("");
				
				bss_news.find(".post-inner-loop .grid-item").removeClass('show active');
				
				bss_news.find(".post-inner-loop .grid-item").each(function(){
					if(get_filter==='*'){
						if($(this)){							
							$(this).addClass('active currentpf');
						}
					}else{
						if($(this).hasClass(get_filter)){							
							$(this).addClass('active currentpf');
						}
					}
				});
				var item_active = bss_news.find(".post-inner-loop .grid-item.active");
					
				var numberOfItems = item_active.length;
				
				var totalPages = Math.ceil(numberOfItems / limitPerPage);
				
				 item_active.addClass('show');
				showPage(1,bss_news,item_active,totalPages,limitPerPage);
			});
			
			$(".bss_np.bssprv").on("click", function (e) {
				e.preventDefault();
				var bss_news = $(this).closest('.bss-news');
				var limitPerPage =bss_news.data('newslooppage');
				var item_active = bss_news.find('.post-inner-loop .grid-item.active');	
				var numberOfItems = item_active.length;
				var totalPages = Math.ceil(numberOfItems / limitPerPage);
				
				return showPage(currentPage-1,bss_news,item_active,totalPages,limitPerPage);
				
			});
			
			$(".bss_np.bssnext").on("click", function (e) {
				e.preventDefault();
				var bss_news = $(this).closest('.bss-news');
				var limitPerPage =bss_news.data('newslooppage');
				var item_active = bss_news.find('.post-inner-loop .grid-item.active');			
				var numberOfItems = item_active.length;
				var totalPages = Math.ceil(numberOfItems / limitPerPage);
				
				return showPage(currentPage+1,bss_news,item_active,totalPages,limitPerPage);
				
			});
		}
		
		function showPage(whichPage,bss_news,item_active,totalPages,limitPerPage) {
			var highlight =bss_news.data('highlight');
			if (whichPage < 1 || whichPage > totalPages) return false;
			currentPage = whichPage;
			item_active.removeClass('show bssfc fadeInEffect animated').addClass("fadeOutEffect animated")
				.slice((currentPage-1) * limitPerPage, 
						currentPage * limitPerPage).addClass('show'+ ' ' +highlight).hide(0)
						.slice(".currentpf").stop().show(0).removeClass("fadeOutEffect animated").addClass('fadeInEffect animated')
				.slice(0,1).addClass('bssfc')
		   
			bss_news.find(".bss_np.bssprv").toggleClass("disabled", currentPage === 1);
			bss_news.find(".bss_np.bssnext").toggleClass("disabled", currentPage === totalPages);
			return true;
		}
		/*bss News Filter*/
	});
})(jQuery);
/*Row Bg Scroll Color*//*Page sections background color scroll*/
function plus_onscroll_bg(){
	"use strict";
	var $=jQuery;
	var pageWrapper = $('.plus-scroll-sections-bg');
	if(pageWrapper.length > 0 && pageWrapper.data("scrolling-effect")=='yes'){
		var bgColors = pageWrapper.data('bg-colors');
		if(bgColors){
			var paraent_node=pageWrapper.closest(".elementor");
			var i=0;
			var arry_len=bgColors.length;
			paraent_node.find(".elementor-section-wrap >.elementor-element, .elementor-element").each(function(){
				if(arry_len>i){
					var FirstColor=i;
					var SecondColor=i+1;
				}else{
					i=0;
					var FirstColor=i;
					var SecondColor=i+1;
				}
				if(bgColors[FirstColor]!='' && bgColors[FirstColor]!=undefined){
					FirstColor=bgColors[FirstColor];
				}
				if(bgColors[SecondColor]!='' && bgColors[SecondColor]!=undefined){
					SecondColor=bgColors[SecondColor];
				}else{
					i=0;
					SecondColor=i;
					SecondColor=bgColors[SecondColor];
				}
				rowTransitionalColor($(this), new $.Color(FirstColor),new $.Color(SecondColor));				
				i++;
			});
		}
	}else if(pageWrapper.length > 0){
		var bgColors = pageWrapper.data('bg-colors');
		if(bgColors){
			var parent_id=pageWrapper.parent().data("elementor-id");
			var loop_scroll = pageWrapper.find(".plus-section-bg-scrolling");
			
			if($('.elementor .elementor-inner').length){
				const contentElems = Array.from(document.querySelectorAll('.elementor-'+parent_id+'> .elementor-inner >.elementor-section-wrap > .elementor-element, .elementor-'+parent_id+' > .elementor-element'));
				var totalEle=contentElems.length;
				var step=0;
			var position;			
				contentElems.forEach((el,pos) => {
					const scrollElemToWatch = pos ? contentElems[pos] : contentElems[pos];
					pos = pos ? pos : totalEle;
					const watcher = scrollMonitor.create(scrollElemToWatch,{top:-300});
				
					watcher.enterViewport(function() {
						step = pos;
						if(totalEle >= loop_scroll.length && pos+1 > loop_scroll.length){
							position=0;
						}else{
							position=pos;
						}
						pageWrapper.find(".plus-section-bg-scrolling").removeClass("active_sec");
						pageWrapper.find(".plus-section-bg-scrolling:nth-child("+(position+1)+")").addClass("active_sec");
					});
					watcher.exitViewport(function() {
						var idx = !watcher.isAboveViewport ? pos-1 : pos+1;
						if( idx <= totalEle && step !== idx ) {							
							step = idx;
							if(totalEle > loop_scroll.length && idx+1 > loop_scroll.length){
								position=0;
							}else{
								position=idx;
							}
						pageWrapper.find(".plus-section-bg-scrolling").removeClass("active_sec");
						pageWrapper.find(".plus-section-bg-scrolling:nth-child("+(position+1)+")").addClass("active_sec");
						}
					});
				});
			}else{
				const contentElems = Array.from(document.querySelectorAll('.elementor-'+parent_id+' >.elementor-section-wrap > .elementor-element, .elementor-'+parent_id+' > .elementor-element'));
				var totalEle=contentElems.length;
				var step=0;
			var position;			
				contentElems.forEach((el,pos) => {
					const scrollElemToWatch = pos ? contentElems[pos] : contentElems[pos];
					pos = pos ? pos : totalEle;
					const watcher = scrollMonitor.create(scrollElemToWatch,{top:-300});
				
					watcher.enterViewport(function() {
						step = pos;
						if(totalEle >= loop_scroll.length && pos+1 > loop_scroll.length){
							position=0;
						}else{
							position=pos;
						}
						pageWrapper.find(".plus-section-bg-scrolling").removeClass("active_sec");
						pageWrapper.find(".plus-section-bg-scrolling:nth-child("+(position+1)+")").addClass("active_sec");
					});
					watcher.exitViewport(function() {
						var idx = !watcher.isAboveViewport ? pos-1 : pos+1;
						if( idx <= totalEle && step !== idx ) {							
							step = idx;
							if(totalEle > loop_scroll.length && idx+1 > loop_scroll.length){
								position=0;
							}else{
								position=idx;
							}
						pageWrapper.find(".plus-section-bg-scrolling").removeClass("active_sec");
						pageWrapper.find(".plus-section-bg-scrolling:nth-child("+(position+1)+")").addClass("active_sec");
						}
					});
				});
			}
		}
	}
}
function rowTransitionalColor($row, firstColor, secondColor) {
    "use strict";
    var $ = jQuery, scrollPos = 0, currentRow = $row, beginningColor = firstColor, endingColor = secondColor, percentScrolled, newRed, newGreen, newBlue, newColor;
    
    $(document).scroll(function() {
        var animationBeginPos = currentRow.offset().top
          , endPart = currentRow.outerHeight() < 800 ? currentRow.outerHeight() / 4 : $(window).height()
          , animationEndPos = animationBeginPos + currentRow.outerHeight() - endPart;
        scrollPos = $(this).scrollTop();
        if (scrollPos >= animationBeginPos && scrollPos <= animationEndPos) {
            percentScrolled = (scrollPos - animationBeginPos) / (currentRow.outerHeight() - endPart);
            newRed = Math.abs(beginningColor.red() + (endingColor.red() - beginningColor.red()) * percentScrolled);
            newGreen = Math.abs(beginningColor.green() + (endingColor.green() - beginningColor.green()) * percentScrolled);
            newBlue = Math.abs(beginningColor.blue() + (endingColor.blue() - beginningColor.blue()) * percentScrolled);
            newColor = new $.Color(newRed,newGreen,newBlue);
            $('.plus-scroll-sections-bg').animate({
                backgroundColor: newColor
            }, 0)
        } else if (scrollPos > animationEndPos) {
            $('.plus-scroll-sections-bg').animate({
                backgroundColor: endingColor
            }, 0)
        }
    })
}
/*Page sections background color scroll*/
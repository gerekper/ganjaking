document.addEventListener("DOMContentLoaded", function() {
    var e = !1
      , t = document.querySelectorAll(".plus_row_scroll");
	 var sec_offset_left=0;
	 var mob_off_disable='';
    Array.prototype.forEach.call(t, function(t) {
			e = !0;
			var r = t;
			
			r.classList.add("plus_row_scroll_parent"),			
			"true" === t.getAttribute("data-body-overflow") && document.querySelector("body").classList.add("plus_row_scroll_overflow");
			if(t.getElementsByClassName("elementor-section-stretched")){
				sec_offset_left=t.style.left;
			}
			var mob_off= t.getAttribute("data-row-mobile-off");
			if(mob_off!='' && mob_off=='true'){
				mob_off_disable ='disable';
			}		
    });
	if(t.length && (mob_off_disable!='disable' || !navigator.userAgent.match(/(Mobi|Android|iPhone)/))){
		e && setTimeout(function() {
		   skrollr.init({
				forceHeight: !1,
				smoothScrolling:true,
				mobileDeceleration:0.009,
				mobileCheck: function(){
					if((/Android|iPhone|iPad|iPod|BlackBerry|Windows Phone/i).test(navigator.userAgent || navigator.vendor || window.opera)){
						if(jQuery(".plus_row_scroll_parent.elementor-section-stretched").closest(".container")){
							jQuery(".plus_row_scroll_parent.elementor-section-stretched").closest(".container").attr('id', 'skrollr-body');
						}else if(jQuery(".plus_row_scroll_parent.elementor-section-stretched").closest(".container-fluid")){
							jQuery(".plus_row_scroll_parent.elementor-section-stretched").closest(".container-fluid").attr('id', 'skrollr-body');
						}else if(jQuery(".plus_row_scroll_parent.elementor-section-stretched").closest(".elementor-inner")){
							jQuery(".plus_row_scroll_parent.elementor-section-stretched").closest(".elementor-inner").attr('id', 'skrollr-body');
						}else if(jQuery(".plus_row_scroll_parent.elementor-section-stretched").closest("body")){
							jQuery(".plus_row_scroll_parent.elementor-section-stretched").closest("body").attr('id', 'skrollr-body');
						}
					}
				}
			})
		}, 1e3);
	}
    window.refreshSkrollables = function() {
        if (t.length && document.querySelectorAll(".skrollable") && (mob_off_disable!='disable' || !navigator.userAgent.match(/(Mobi|Android|iPhone)/))) {			
            var e = skrollr.get();
            "undefined" != typeof e && e.refresh(document.querySelectorAll(".skrollable"));			
        }
		jQuery(".plus_row_scroll_parent.elementor-section-stretched").each(function(){
			var $this=jQuery(this);
			var sec_entrance=$this.data("row-entrance");
			var sec_exit=$this.data("row-exit");
			var exit_row=new Array("fly-left", "fly-right", "scale-smaller", "carousel", "stick-fly-left", "stick-fly-right","rotate-back");
			var entrance_row=new Array("fly-left", "fly-right", "scale-smaller", "carousel","rotate-back");
			if(jQuery.inArray( sec_entrance, entrance_row ) !== -1 || jQuery.inArray( sec_exit, exit_row ) !== -1){
				$this.css("left",sec_offset_left);
			}
		});
    },
    window.addEventListener("resize", function() {
        setTimeout(window.refreshSkrollables, 0.2)
    })
}),
"undefined" != typeof jQuery && jQuery(document).ready(function(e) {
    e(window).on("grid:items:added", function() {
        window.refreshSkrollables()
    })
});

(function ($) {
	
	/*Animated services box*/
	var WidgetAnimatedServicesBoxHandler = function($scope, $) {
		var container = $scope.find('.pt_plus_asb_wrapper'),
			loop_item=container.find(".service-item-loop" );
		//Image Accordion
		if(container.hasClass('image-accordion')){
			loop_item.on("mouseenter",function() {
				var flexgrow = $(this).data('flexgrow');
				$(this).closest('.image-accordion').find('.active_accrodian').css('flex-grow','1').removeClass('active_accrodian');
				$(this).addClass( "active_accrodian" ).css('flex-grow',flexgrow);
			}).on("mouseleave", function() {
				if($(this).closest('.image-accordion').data("accordion-hover")=='yes'){
					$(this).css('flex-grow','1').removeClass('active_accrodian');
				}
			});
		}
		//SlideBox
		if(container.hasClass('sliding-boxes')){
			var w=$(window).innerWidth();
			$(window).on('resize',function(){
				var w=$(window).innerWidth();
				if(w>=1024){
					var total_item = loop_item.length;		
					var new_total_item = total_item + 1; 
					var margin = (total_item - 1) * 15;
					var divWidth = container.find(".asb_wrap_list").width();			
					
					divWidth = divWidth -  margin;
					new_total_item = divWidth / new_total_item;			
					loop_item.attr('data-width',new_total_item).css('width',new_total_item );
					loop_item.find("img").css('width',new_total_item );
					loop_item.find(".tp-sb-image").css('width',new_total_item );
					loop_item.find(".asb-content").css('width',new_total_item ).css('left',new_total_item );
					container.find(".service-item-loop.active-slide" ).css('width',new_total_item*2 );
				}else{
					loop_item.find("img").css('width','' );
					loop_item.find(".tp-sb-image").css('width','' );
					loop_item.find(".asb-content").css('width','' ).css('left','' );
					container.find(".service-item-loop.active-slide" ).css('width','');
				}
				loop_item.on("mouseenter", function() {
					var width = $(this).attr('data-width');
					if(w>=1024){
						$(this ).closest('.sliding-boxes').find(".service-item-loop").removeClass("active-slide").css('width',width );
						$(this).addClass("active-slide").css('width',width * 2 );
						$('.asb-content',this).css('left',width);
					}else{
						$(this ).closest('.sliding-boxes').find(".service-item-loop").removeClass("active-slide");
						$(this).addClass("active-slide");
					}
				});
			});
			if(w>=1024){
				var total_item = loop_item.length;		
				var new_total_item = total_item + 1; 
				var margin = (total_item - 1) * 15;
				var divWidth = container.find(".asb_wrap_list").width();			
				
				divWidth = divWidth -  margin;
				new_total_item = divWidth / new_total_item;			
				loop_item.attr('data-width',new_total_item).css('width',new_total_item );
				loop_item.find("img").css('width',new_total_item );
				loop_item.find(".tp-sb-image").css('width',new_total_item );
				loop_item.find(".asb-content").css('width',new_total_item ).css('left',new_total_item );
				container.find(".service-item-loop.active-slide" ).css('width',new_total_item*2 );
			}
			loop_item.on("mouseenter", function() {
				var width = $(this).attr('data-width');
				if(w>=1024){
					$(this ).closest('.sliding-boxes').find(".service-item-loop").removeClass("active-slide").css('width',width );
					$(this).addClass("active-slide").css('width',width * 2 );
					$('.asb-content',this).css('left',width);
				}else{
					$(this ).closest('.sliding-boxes').find(".service-item-loop").removeClass("active-slide");
					$(this).addClass("active-slide");
				}
			});
		}
		//Hover Sections
		if(container.hasClass('hover-section')){
			var i = 0;
			loop_item.each(function(){
				var hover_sec_boc = $(this).data('hsboc');
				if(i==0){
					var total_hover_section = $(this).find('.hover-section-content-wrapper').data('image');					
					$(this).closest('section.elementor-element').css('background','url('+ total_hover_section +') center/cover').css('transition', 'background 0.5s linear').css('box-shadow',  hover_sec_boc + ' 0px 0px 0px 2000px inset');
				}
				i++;
			});
			loop_item.on("mouseenter", function() {
				var image = $(this).find('.hover-section-content-wrapper').data('image');
				$(this).closest('.asb_wrap_list').find(".service-item-loop").removeClass("active-hover");
				$(this).addClass("active-hover");
				$(this).closest('section.elementor-element').css('background','url('+ image +') center/cover');				
			});
		}
		//Services Element
		if (container.hasClass('services-element-style-1')) {
			$(window).on('load resize',function(){
				container.find(".se-wrapper").each(function(){
					var sec_height = $(this).height();
					var top_pa=$(this).css('padding-top');
					top_pa=parseInt(top_pa, 10);
					sec_height = sec_height + 40 +top_pa;				
					$(this).find(".se-listing-section").css('padding-top', sec_height);				
				});
			});
			container.find(".se-wrapper").each(function(){
				var sec_height = $(this).height();
				var top_pa=$(this).css('padding-top');
				top_pa=parseInt(top_pa, 10);
				sec_height = sec_height + 40 +top_pa;			
				$(this).find(".se-listing-section").css('padding-top', sec_height);				
			});
		}
		//portfolio style 1
		if (container.hasClass("portfolio-style-1")) {
			var i = 0;
			loop_item.on("mouseenter", function() {
				var imageurl = $(this).data('url');
				$(this).closest('.portfolio-style-1').find(".service-item-loop").removeClass("active-port");
				$(this).addClass("active-port");
				$(this).closest('.asb_wrap_list').find('.portfolio-hover-image').css('background','url('+ imageurl +')');				
			});			
		}
		if (container.hasClass("portfolio-style-2")) {
			loop_item.on("mouseenter", function() {
				var imageurl = $(this).data('url');
				$(this).closest('.asb_wrap_list').find(".service-item-loop").removeClass("active-port");
				$(this).addClass("active-port");
				$(this).closest('.asb_wrap_list').find('.portfolio-wrapper').css('background','url('+ imageurl +')');
			});
		}
	};
	/*Animated services box*/
	$(window).on("load resize",function(){
		if($(window).innerWidth() <= 1024){
			if ($(".pt_plus_asb_wrapper.portfolio .asb-title-link").length) {
				$(".pt_plus_asb_wrapper.portfolio .asb-title-link").one('click', function () { 
					 event.stopPropagation();							 
					$(this).removeAttr("href");
					var portfolio_click = $(this).closest('.service-item-loop.active-port').data('clickurl');					
					$(this).append('<div class="mobile-click-port"><a class="pf_a_click" href="' + portfolio_click + '">Click Here</a></div>');
				});
			}
		}
	});
	
	/*Shape Divider*/
	var WidgetShapeDividerHandler = function($scope, $) {
		var tp_shape_divider = $scope.find('.tp-plus-shape-divider');
		var tp_shape_position = tp_shape_divider.data("position");
		var tp_section_type = tp_shape_divider.data("section-type");
		
		var parent_row= tp_shape_divider.closest('section.elementor-element');
		var column_row= tp_shape_divider.closest('.elementor-column');
		var wid_sec=$scope.closest('section.elementor-element');
		
		if(wid_sec.length){
			var widget_remove_old=$(wid_sec).find(".tp-plus-shape-divider");
			if(widget_remove_old.length > 0){
				var update_id=tp_shape_divider.data("id");
				$(widget_remove_old).each(function(){
					var ids=$(this).data("id");
					if(ids==update_id){
						$("."+ids).remove();
					}
					if(ids!=undefined){
						var res = ids.replace("shape", "");
						var remove_widget=$(wid_sec).find(".elementor-element-"+res);
						if(remove_widget.length==0){
							$("."+ids).remove();
						}
					}
				});
			}
		}
		if(parent_row && tp_section_type!='column'){
			if(tp_shape_position=='top'){
				$( parent_row ).prepend(tp_shape_divider);
			}else{
				$( parent_row ).append(tp_shape_divider);
			}
			parent_row.css("position","relative");
			var bg_sec=$(wid_sec).find("> .tp-plus-shape-divider");
			if(bg_sec.data("section-hidden") !=undefined || bg_sec.data("section-hidden") !=''){
				if(!parent_row.hasClass("elementor-element-edit-mode")){
					parent_row.css("overflow",bg_sec.attr("data-section-hidden"));
				}
			}
		}else{
			if(tp_shape_position=='right' || tp_shape_position=='left'){
				$( window ).on( "load resize", function() {
					var sec_height=column_row.height();
					$(tp_shape_divider).css("width",sec_height+"px");
				});
				setTimeout(function(){
					var sec_height=column_row.height();
					var sec_offset=tp_shape_divider.offset();
					$(tp_shape_divider).css("width",sec_height+"px");
				}, 150);
			}
			$( column_row ).append(tp_shape_divider);
		}
		
		if(tp_shape_divider.hasClass('shape-wave')){
			tp_shape_divider.find('.wave-items').each(function() {				
				var _color = $(this).data('color') ? $(this).data('color') : '#8072fc'
				  , _height = $(this).data('height') ? $(this).data('height') : 80
				  , _bones = $(this).data('bones') ? $(this).data('bones') : 4
				  , _amplitude = $(this).data('amplitude') ? $(this).data('amplitude') : 40
				  , _speed = $(this).data('speed') ? $(this).data('speed') : 0.15;
				$(this).children('path').wavify({
					height: _height,
					bones: _bones,
					amplitude: _amplitude,
					color: _color,
					speed: _speed
				});
			});
		}
	};
	/*Shape Divider*/
	/*---- Scroll Navigation ----*/
	var WidgetScrollNavHandler = function($scope, $) {
		var scroll_nav = $scope.find('.theplus-scroll-navigation');
		if(scroll_nav.length > 0 ){			
			$(".theplus-scroll-navigation__item").mPageScroll2id({
				highlightSelector:".theplus-scroll-navigation__item",
				highlightClass:"highlight",
				forceSingleHighlight:true,
			});
	
		}
		var container = $scope.find('.theplus-scroll-navigation.scroll-view');
		var container_scroll_view = $scope.find('.theplus-scroll-navigation__inner');
		if(container.length > 0 && container_scroll_view){
			$(window).on('scroll', function() {
				var scroll = $(this).scrollTop();
				container.each(function () {
					var scroll_view_value = $(this).data("scroll-view");
					var uid=$(this).data("uid"),
						$scroll_top = $("."+uid );
					if (scroll > scroll_view_value) {
						$scroll_top.addClass('show');
					}else {
						$scroll_top.removeClass('show');
					}
					
				});
			});	
		}
	};
	/*---- Advanced Typography ----*/
	var WidgetAdvancedTypographyHandler = function($scope, $) {
		var advance_typography = $scope.find('.pt-plus-adv-typo-wrapper');
		var typo_circular = advance_typography.find('.typo_circular ');
		var typo_blend_mode = advance_typography.find('.typo_bg_based_text ');
		 if(typo_circular.length > 0 ){
			$(typo_circular).each(function(){
				var $this = $(this); 
				var ids= $this.attr('id');
				var custom_radius = $this.data('custom-radius');
				var custom_reversed = $this.data('custom-reversed');
				var custom_resize = $this.data('custom-resize');
					if(custom_reversed == 'yes'){
						var circular_option = new CircleType(document.getElementById(ids)).dir(-1).radius(custom_radius);
					}else {
					var circular_option = new CircleType(document.getElementById(ids)).radius(custom_radius);
					}
				if(custom_resize == 'yes'){
					$(window).on("resize",function() {
						 circular_option.radius(circular_option.element.offsetWidth / 2);
					});
				}
			});
		}
		if(typo_blend_mode.length > 0){
			var mode=$(typo_blend_mode).data("blend-mode");
			var fixed_mode=$(typo_blend_mode).closest(".elementor-fixed");
			var absolute_mode=$(typo_blend_mode).closest(".elementor-absolute");
			if(fixed_mode.length > 0){
				$(typo_blend_mode).closest(".elementor-fixed").css("mix-blend-mode",mode);
			}
			if(absolute_mode.length > 0){
				$(typo_blend_mode).closest(".elementor-absolute").css("mix-blend-mode",mode);
			}
		}
	}
	/*---- Advanced Typography ----*/
	var WidgetTimeLineContentHandler = function(e, a) {
		var container = e.find('.pt-plus-timeline-list');
			if(container.hasClass("end-pin-none")){				
				var start_icon=container.find('.timeline-item-wrap').first().find('.timeline-inner-block .point-icon');
				var end_icon=container.find('.timeline-item-wrap').last().find('.timeline-inner-block .point-icon');
				if(start_icon.length){
					start_icon=start_icon.offset().top;
				}else{
					start_icon=0;
				}
				if(end_icon.length){
					end_icon=end_icon.offset().top;
				}else{
					end_icon=0;
				}
				var total_height=end_icon-start_icon;
				var offset_top=0;
				if(!container.hasClass("start-pin-none")){
					offset_top=50;
				}
				if(container.hasClass("start-pin-none")){
					offset_top=-10;
				}
				container.find(".post-inner-loop .timeline-track").css("height",total_height + offset_top );
				
				container.find(".post-inner-loop").on( 'arrangeComplete', function() {
					var start_icon=container.find('.timeline-item-wrap').first().find('.timeline-inner-block .point-icon');
					var end_icon=container.find('.timeline-item-wrap').last().find('.timeline-inner-block .point-icon');
					if(start_icon.length){
						start_icon=start_icon.offset().top;
					}else{
						start_icon=0;
					}
					if(end_icon.length){
						end_icon=end_icon.offset().top;
					}else{
						end_icon=0;
					}
					var total_height=end_icon-start_icon;
					var offset_top=0;
					if(!container.hasClass("start-pin-none")){
						offset_top=50;
					}
					if(container.hasClass("start-pin-none")){
						offset_top=-10;
					}
					container.find(".post-inner-loop .timeline-track").css("height",total_height + offset_top );					
				});
			}
	};
	var WidgetTableContentHandler = function(e, a) {
        if (void 0 !== e) {
            e.find(".plus-table-wrapper");
            var n = e.data("id")
              , l = e.find(".plus-table")
              , t = e.find("#plus-table-id-" + n)
              , d = !1
              , r = !1
              , i = !1;
            if (0 != t.length) {
                "yes" == a(".elementor-element-" + n + " #" + t[0].id).data("searchable") && (d = !0),
                "yes" == a(".elementor-element-" + n + " #" + t[0].id).data("show-entry") && (r = !0),
                "yes" == a(".elementor-element-" + n + " #" + t[0].id).data("sort-table") && (a(".elementor-element-" + n + " #" + t[0].id + " th").css({
                    cursor: "pointer"
                }),
                i = !0);
                var o = a(".elementor-element-" + n + " #" + t[0].id).data("searchable-label");
                if (d || r || i)
                    a("#" + t[0].id).DataTable({
                        paging: r,
                        searching: d,
                        ordering: i,
                        info: !1,
						"pagingType": "full_numbers",
						"lengthMenu": [[5, 10, 15, -1], [5, 10, 15, "All"]],
                        oLanguage: {
                            sSearch: o
                        }
                    }),
                    e.find(".dataTables_length").addClass("plus-tbl-entry-wrapper plus-table-info"),
                    e.find(".dataTables_filter").addClass("plus-tbl-search-wrapper plus-table-info"),
                    e.find(".plus-table-info").wrapAll('<div class="plus-advance-heading"></div>');
                window.addEventListener("load", s),
                window.addEventListener("resize", s)
            }
        }
        function s() {
            a(window).width() > 767 ? (a(l).addClass("plus-column-rules"),
            a(l).removeClass("plus-no-column-rules")) : (a(l).removeClass("plus-column-rules"),
            a(l).addClass("plus-no-column-rules"))
        }
    };
	/*OffCanvas*/
	var WidgetOffCanvasContentHandler = function ($scope, $) {
		new PlusOffcanvas( $scope );
		var container = $scope.find('.plus-offcanvas-wrapper.scroll-view');
		var container_scroll_view = $scope.find('.offcanvas-toggle-btn.position-fixed');
		if(container.length>0 && container_scroll_view){
			$(window).on('scroll', function() {
				var scroll = $(this).scrollTop();
				container.each(function () {
					var scroll_view_value = $(this).data("scroll-view");
					var uid=$(this).data("canvas-id"),
						$scroll_top = $("."+uid );
					if (scroll > scroll_view_value) {
						$scroll_top.addClass('show');
					}else {
						$scroll_top.removeClass('show');
					}
					
				});
			});	
		}
	};
	
		
	
	/* Progress Bar */
	var WidgetProgressBarHandler = function($scope, $) {
		var container = $scope.find('.pt-plus-peicharts');
		if(container.length > 0){
			container.each(function(){
				var b=$(this);
				var e= $(this).find(".progress_bar-skill-bar-filled");
				b.waypoint(function(direction) {
					if (direction === 'down') {
						if(!b.hasClass("done-progress")){
							e.css("width", e.data("width"));
							if(b.find(".progress_bar-media.large")){
								b.find(".progress_bar-media.large").css("width", e.data("width"));
							}
							b.addClass("done-progress");
						}
					}
				}, { offset: '90%' });
			});
		}
		
	};
	/* Progress Bar */
	
	/* ------------------------------ */
	/* Advance accordion
	/* ------------------------------ */
	var WidgetAccordionHandler = function($scope, $) {

		var $plusadv_accordion = $scope.find('.theplus-accordion-wrapper');
		var $this =  $plusadv_accordion,
			$accordionID                = $this.attr('id'),
			$currentAccordion           = $('#'+$accordionID),
			$accordionType              = $this.data('accordion-type'),
			$accordionSpeed             = $this.data('toogle-speed'),
			$accrodionList              = $this.find('.theplus-accordion-item'),
			$PlusAccordionListHeader    = $accrodionList.find('.plus-accordion-header');
			
		
			$accrodionList.each(function(i) {
				if( $(this).find($PlusAccordionListHeader).hasClass('active-default') ) {
					$(this).find($PlusAccordionListHeader).addClass('active');
					$(this).find('.plus-accordion-content').addClass('active').css('display', 'block').slideDown($accordionSpeed);
					var accordionConnection=$(this).closest(".theplus-accordion-wrapper").data('connection');
					
					if(accordionConnection!='' && accordionConnection!=undefined){
							var tab_index=$(this).find('.plus-accordion-content.active').data("tab");							
							setTimeout(function(){
								accordion_tabs_connection(tab_index,accordionConnection);
							}, 150);
					}
					
				}
			});
		
		if( 'accordion' == $accordionType ) {
			$PlusAccordionListHeader.on('click', function() {
				//Check if 'active' class is already exists
				if( $(this).hasClass('active') ) {
					$(this).removeClass('active');
					$(this).next('.plus-accordion-content').removeClass('active').slideUp($accordionSpeed);
				}else {
					$PlusAccordionListHeader.removeClass('active');
					$PlusAccordionListHeader.next('.plus-accordion-content').removeClass('active').slideUp($accordionSpeed);
			
					$(this).toggleClass('active');
					$(this).next('.plus-accordion-content').slideToggle($accordionSpeed, function() {
						$(this).toggleClass('active');
					});
					var accordionConnection=$(this).closest(".theplus-accordion-wrapper").data('connection');
					if(accordionConnection!='' && accordionConnection!=undefined){
							var tab_index=$(this).data("tab");
							accordion_tabs_connection(tab_index,accordionConnection);
					}
					
				}
			});			
		}
		if( 'hover' == $accordionType ) {
			$PlusAccordionListHeader.on('mouseover', function() {
				if( $(this).hasClass('active') ) {
				//	$(this).removeClass('active');
				//	$(this).next('.plus-accordion-content').removeClass('active').slideUp($accordionSpeed);
				}else {
					$PlusAccordionListHeader.removeClass('active');
					$PlusAccordionListHeader.next('.plus-accordion-content').removeClass('active').slideUp($accordionSpeed);
			
					$(this).toggleClass('active');
					$(this).next('.plus-accordion-content').slideToggle($accordionSpeed, function() {
						$(this).toggleClass('active');
					});
					var accordionConnection=$(this).closest(".theplus-accordion-wrapper").data('connection');
					if(accordionConnection!='' && accordionConnection!=undefined){
							var tab_index=$(this).data("tab");
							accordion_tabs_connection(tab_index,accordionConnection);
					}
				}
			});			
		}
		if( 'toggle' == $accordionType ) {
			$PlusAccordionListHeader.on('click', function() {
				if( $(this).hasClass('active') ) {
					$(this).removeClass('active');
					$(this).next('.plus-accordion-content').removeClass('active').slideUp($accordionSpeed);
				}else {
					$(this).toggleClass('active');
					$(this).next('.plus-accordion-content').slideToggle($accordionSpeed, function() {
						$(this).toggleClass('active');
					});
				}
			});
		}
	}; // End of advance accordion
	/* ------------------------------ */
	/* Advance Tab
	/* ------------------------------ */
	var WidgetTabHandler = function ($scope, $) {

		$(document).ready(function($) {
			var $currentTab = $scope.find('.theplus-tabs-wrapper'),				
				$TabHover = $currentTab.data('tab-hover'),
				
				$currentTabId = '#' + $currentTab.attr('id').toString();
				
				$($currentTabId + ' ul.plus-tabs-nav li .plus-tab-header').each( function(index) {
					var default_active=$(this).closest('.theplus-tabs-wrapper').data("tab-default");
					if( default_active == index ) {
					
						$(this).removeClass('inactive').addClass('active');

						var Connection=$(this).closest(".theplus-tabs-wrapper").data('connection');
						if(Connection!='' && Connection!=undefined){
							setTimeout(function(){
								accordion_tabs_connection(parseInt(default_active+1),Connection);
							}, 150);
						}
					}
					
				} );

				$($currentTabId + ' .theplus-tabs-content-wrapper .plus-tab-content').each( function(index) {
					var default_active=$(this).closest('.theplus-tabs-wrapper').data("tab-default");
					if( default_active == index ) {
						$(this).removeClass('inactive').addClass('active');
					}
				} );
				
				if('yes' == $TabHover){
					$($currentTabId + ' ul.plus-tabs-nav li .plus-tab-header').mouseover(function(){
						var currentTabIndex = $(this).data("tab");
						var tabsContainer = $(this).closest('.theplus-tabs-wrapper');
						var tabsNav = $(tabsContainer).children('ul.plus-tabs-nav').children('li').children('.plus-tab-header');
						var tabsContent = $(tabsContainer).children('.theplus-tabs-content-wrapper').children('.plus-tab-content');
					
						$(tabsContainer).find(".plus-tab-header").removeClass('active default-active').addClass('inactive');
						$(this).addClass('active').removeClass('inactive');
					
						$(tabsContainer).find(".plus-tab-content").removeClass('active').addClass('inactive');
					$('.theplus-tabs-content-wrapper',tabsContainer).find("[data-tab='"+currentTabIndex+"']").addClass('active').removeClass('inactive');
					
						$(tabsContent).each( function(index) {
							$(this).removeClass('default-active');
						});
						var Connection=$(this).closest(".theplus-tabs-wrapper").data('connection');
						if(Connection!='' && Connection!=undefined){
								var tab_index=$(this).data("tab");
								accordion_tabs_connection(tab_index,Connection);
						}
						$($currentTabId+" .list-carousel-slick > .post-inner-loop").slick('setPosition');
					});
				}
				if($($currentTabId).hasClass("mobile-accordion")){
					$(window).on("resize",function() {
						if($(window).innerWidth() <= 600){
							$($currentTabId).addClass("mobile-accordion-tab");
						}
					});
					
				}
				
		});
	}; // End of advance tabs
	/* Stylist Icon Style */
	var WidgetStyleListHandler = function ($scope, $) {
		
		$(document).ready(function($) {
			var $target = $('.plus-stylist-list-wrapper', $scope);
			var $hover_inverse = $('.plus-stylist-list-wrapper.hover-inverse-effect', $scope);
			var $hover_inverse_global = $('.plus-stylist-list-wrapper.hover-inverse-effect-global', $scope);
			
			if($target.length){
				var $read_more =$target.find(".read-more-options");
				if($read_more.length){				
					var default_load =$target.find(".read-more-options").data("default-load");
					var $ul_listing =$target.find(".plus-icon-list-items");
					$ul_listing.each(function(){
					   $(this).find("li:gt("+default_load+")").hide();
					});
					$read_more.on("click", function(e){
						e.preventDefault();
						var $less_text=$(this).data("less-text");
						var $more_text=$(this).data("more-text");
						if($(this).hasClass("more")){
						   $(this).parent(".plus-stylist-list-wrapper").find(".plus-icon-list-items li").show();
						   $(this).text($less_text).addClass("less").removeClass("more");
						}else if($(this).hasClass("less")){
						   $(this).parent(".plus-stylist-list-wrapper").find(".plus-icon-list-items li:gt("+default_load+")").hide();
						   $(this).text($more_text).addClass("more").removeClass("less");
						}
					});
				}
			}
			if($(".plus-bg-hover-effect",$scope).length){
				$('.plus-icon-list-items >li',$target).on('mouseenter', function(e) {
					e.preventDefault();
					if (!$(this).hasClass('active')) {
						var index_el = $(this).index();

						$(this).addClass('active').siblings().removeClass('active');
						$(this).parents(".elementor-widget-tp-style-list").find('.plus-bg-hover-effect .hover-item-content').removeClass('active').eq(index_el).addClass('active');
					} else {
						return false
					}
				});
			}
			if($hover_inverse.length > 0){
				$('.plus-icon-list-items > li',$hover_inverse).on({
					mouseenter: function () {
						$(this).closest(".plus-icon-list-items").addClass("on-hover");
					},
					mouseleave: function () {
						$(this).closest(".plus-icon-list-items").removeClass("on-hover");
					}
				});
			}
			if($target.hasClass("hover-inverse-effect-global")){
				
				$('.plus-icon-list-items > li',$hover_inverse_global).on({
					mouseenter: function () {
						$('body').addClass("hover-stylist-global");
						var hover_class = $(this).closest(".plus-stylist-list-wrapper").data("hover-inverse");
						$(".hover-inverse-effect-global."+hover_class+" .plus-icon-list-items").addClass("on-hover");
					},
					mouseleave: function () {
						$('body').removeClass("hover-stylist-global");
						var hover_class = $(this).closest(".plus-stylist-list-wrapper").data("hover-inverse");
						$(".hover-inverse-effect-global."+hover_class+" .plus-icon-list-items").removeClass("on-hover");
					}
				});
			}
		});
	};
	/* Stylist Icon Style */
	/* smooth scroll page */
	var WidgetSmoothScrollHandler = function ($scope, $) {

		$(document).ready(function($) {
			var $container = $('.plus-smooth-scroll', $scope);
			if($container.length){
				var data_frameRate=($container.attr("data-frameRate") == undefined) ? 150 : $container.attr("data-frameRate"),
					data_animationTime=($container.attr("data-animationTime") == undefined) ? 1000 : $container.attr("data-animationTime"),
					data_stepSize=($container.attr("data-stepSize") == undefined) ? 100 : $container.attr("data-stepSize"),
					data_pulseAlgorithm=($container.attr("data-pulseAlgorithm") == undefined) ? 1 : $container.attr("data-pulseAlgorithm"),
					data_pulseScale=($container.attr("data-pulseScale") == undefined) ? 4 : $container.attr("data-pulseScale"),
					data_pulseNormalize=($container.attr("data-pulseNormalize") == undefined) ? 1 : $container.attr("data-pulseNormalize"),
					data_accelerationDelta=($container.attr("data-accelerationDelta") == undefined) ? 50 : $container.attr("data-accelerationDelta"),
					data_accelerationMax=($container.attr("data-accelerationMax") == undefined) ? 3 : $container.attr("data-accelerationMax"),
					data_keyboardSupport=($container.attr("data-keyboardSupport") == undefined) ? 1 : $container.attr("data-keyboardSupport"),				
					data_arrowScroll=($container.attr("data-arrowScroll") == undefined) ? 50 : $container.attr("data-arrowScroll"),				
					data_touchpadSupport=($container.attr("data-touchpadSupport") == undefined) ? 0 : $container.attr("data-touchpadSupport"),				
					data_fixedBackground=($container.attr("data-fixedBackground") == undefined) ? 1 : $container.attr("data-fixedBackground"),				
					data_tablet_off=($container.attr("data-tablet-off") == undefined) ? 50 : $container.attr("data-tablet-off");				
				
					if(!$('body').hasClass("plus-smooth-scroll-tras")){
					    $('body').addClass("plus-smooth-scroll-tras");
					    $('head').append('<style>.plus-smooth-scroll-tras .magic-scroll .parallax-scroll,.plus-smooth-scroll-tras .magic-scroll .scale-scroll,.plus-smooth-scroll-tras .magic-scroll .both-scroll{-webkit-transition: -webkit-transform 0s ease .0s;-ms-transition: -ms-transform 0s ease .0s;-moz-transition: -moz-transform 0s ease .0s;-o-transition: -o-transform 0s ease .0s;transition: transform 0s ease .0s;will-change: transform;}</style>');
					}
				if(data_tablet_off=='yes'){				    
						var width=window.innerWidth;
						if(width>800){
						    if(!$('body').hasClass("plus-smooth-scroll-tras")){
					            $('body').addClass("plus-smooth-scroll-tras");
						    }
						    SmoothScroll({frameRate:data_frameRate,animationTime:data_animationTime,stepSize:data_stepSize,pulseAlgorithm:data_pulseAlgorithm,pulseScale:data_pulseScale,pulseNormalize:data_pulseNormalize,accelerationDelta:data_accelerationDelta,accelerationMax:data_accelerationMax,keyboardSupport:data_keyboardSupport,arrowScroll:data_arrowScroll,touchpadSupport:data_touchpadSupport,fixedBackground:data_fixedBackground});
						}else{
						    if($('body').hasClass("plus-smooth-scroll-tras")){
					            $('body').removeClass("plus-smooth-scroll-tras");
						    }
						}
				}else{
					SmoothScroll({frameRate:data_frameRate,animationTime:data_animationTime,stepSize:data_stepSize,pulseAlgorithm:data_pulseAlgorithm,pulseScale:data_pulseScale,pulseNormalize:data_pulseNormalize,accelerationDelta:data_accelerationDelta,accelerationMax:data_accelerationMax,keyboardSupport:data_keyboardSupport,arrowScroll:data_arrowScroll,touchpadSupport:data_touchpadSupport,fixedBackground:data_fixedBackground})
				}
				
			
			}
		});
	};
	/* smooth scroll page */
	
	/* pricing table */
	var WidgetPricingTableHandler = function ($scope, $) {
		
		$(document).ready(function($) {
			var $target = $('.plus-pricing-table', $scope);
			if($target.length){
				var $read_more =$target.find(".read-more-options");
				var default_load =$target.find(".read-more-options").data("default-load");
				var $ul_listing =$target.find(".pricing-content-wrap.listing-content.style-1 .plus-icon-list-items");
				$ul_listing.each(function(){				   
				   $(this).find("li:gt("+default_load+")").hide();
				});
				$read_more.on("click", function(e){
					e.preventDefault();
					var a=$(this),$less_text=a.data("less-text");
					var $more_text=a.data("more-text");
					if(a.hasClass("more")){
					   a.parent(".pricing-content-wrap.listing-content").find(".plus-icon-list-items li").show();
					   a.text($less_text).addClass("less").removeClass("more");
					}else if(a.hasClass("less")){
					   a.parent(".pricing-content-wrap.listing-content").find(".plus-icon-list-items li:gt("+default_load+")").hide();
					   a.text($more_text).addClass("more").removeClass("less");
					}
				});
			}
			
		});
	};
	/* pricing table */
	/* Hotspot */
	var WidgetHotspotHandler = function ($scope, $) {
		
		$(document).ready(function($) {
			var $target = $('.theplus-hotspot', $scope);
			if($target.length){
				var $overlay_color =$target.find(".theplus-hotspot-inner.overlay-bg-color");
				var $pin_hover =$target.find(".pin-hotspot-loop");
				if($overlay_color.length > 0){
					
					$pin_hover.mouseover(function() {						
						$(this).closest(".theplus-hotspot-inner.overlay-bg-color").addClass("on-hover");
					}).mouseout(function() {
						$(this).closest(".theplus-hotspot-inner.overlay-bg-color").removeClass("on-hover");
					});
				}
			}
		});
	};
	/* Hotspot */
	/* carousel anything*/
	var WidgetCarouselAnythingHandler = function ($scope, $) {
		$(document).ready(function() {
		var $target = $('.theplus-carousel-anything-wrapper', $scope);
		
			if($target.length){
				var uid=$target.data("id");
				
				$('.'+uid+' > .post-inner-loop').on('beforeChange', function(e, slick, currentSlide, nextSlide) {
					if(currentSlide!=nextSlide){
						var $animatingElements = $('.grid-item.slick-slide:not(.slick-active)').find('.animate-general');
						$animatingElements.each(function() {
							var p = $(this);
							p.removeClass("animation-done");
							p.css("opacity","0");
						});						
					}
					if($('.'+uid).data("connection")!='' && $('.'+uid).data("connection")!=undefined){
						var connection= $('.'+uid).data("connection");
						if(!$("#"+connection).find('.plus-accordion-header[data-tab="'+parseInt(nextSlide+1)+'"]').hasClass("active")){
							$("#"+connection).find('.plus-accordion-header[data-tab="'+parseInt(nextSlide+1)+'"]').trigger("click");
						}
						if(!$("#"+connection).find('li .plus-tab-header[data-tab="'+parseInt(nextSlide+1)+'"]').hasClass("active")){
							$("#"+connection).find('li .plus-tab-header[data-tab="'+parseInt(nextSlide+1)+'"]').trigger("click");
						}
					}
				});
				
				$('.'+uid+' > .post-inner-loop').on('afterChange', function(e, slick, currentSlide, nextSlide) {
					var $animatingElements = $('.grid-item.slick-slide.slick-active').find('.animate-general');					
					doAnimations($animatingElements);
				});
				function doAnimations(elements) {
					elements.each(function() {
						var p = $(this);
						var delay_time=p.data("animate-delay");
						var duration_time=p.data("animate-duration");
						var d = p.data("animate-type");
						if(!p.hasClass("animation-done")){
							p.addClass("animation-done").velocity(d,{ delay: delay_time,duration: duration_time,display:'auto'});
						}
					});
				}
			}
		});
	};
	/* carousel anything*/
	/* carousel remote*/
	var WidgetCarouselRemoteHandler = function ($scope, $) {		
		$(document).ready(function() {
		var $target = $('.theplus-carousel-remote', $scope);
			if($target.length){
				$(".theplus-carousel-remote .custom-nav-remote").on("click", function(e){
					e.preventDefault();
					
					var remote_uid=$(this).data("id");
					var remote_type = $(this).closest(".theplus-carousel-remote").data("remote");
					
					if(remote_uid!='' && remote_uid!=undefined && remote_type=='carousel'){	
					
						var carousel_slide=$(this).data("nav");
						
						if(carousel_slide=='next'){
							$('.'+remote_uid+' > .post-inner-loop').slick("slickNext");
						} else if(carousel_slide=='prev'){
							$('.'+remote_uid+' > .post-inner-loop').slick("slickPrev");
						}
						
					}else if(remote_uid!='' && remote_uid!=undefined && remote_type=='switcher'){
						
						var switcher_toggle=$(this).data("nav");
						
						var switch_toggle = $('#'+remote_uid).find('.switch-toggle');
						var switch_1_toggle = $('#'+remote_uid).find('.switch-1');
						var switch_2_toggle = $('#'+remote_uid).find('.switch-2');
						
						$(".theplus-carousel-remote .custom-nav-remote").removeClass("active");
						$(this).addClass("active");
						
						if(switcher_toggle=='next'){
							switch_2_toggle.trigger("click");							
						} else if(switcher_toggle=='prev'){	
							switch_1_toggle.trigger("click");
						}
					}
				});				
			}
		});
	};
	/* carousel remote*/
		
	$(window).on('elementor/frontend/init', function () {
		
		elementorFrontend.hooks.addAction('frontend/element_ready/tp-table.default', WidgetTableContentHandler);	
		elementorFrontend.hooks.addAction('frontend/element_ready/tp-off-canvas.default', WidgetOffCanvasContentHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/tp-progress-bar.default', WidgetProgressBarHandler);
		
		elementorFrontend.hooks.addAction('frontend/element_ready/tp-accordion.default', WidgetAccordionHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/tp-hotspot.default', WidgetHotspotHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/tp-tabs-tours.default', WidgetTabHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/tp-style-list.default', WidgetStyleListHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/tp-smooth-scroll.default', WidgetSmoothScrollHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/tp-pricing-table.default', WidgetPricingTableHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/tp-carousel-anything.default', WidgetCarouselAnythingHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/tp-carousel-remote.default', WidgetCarouselRemoteHandler);
	});
	
	var WidgetGoogleMapHandler = function ($scope, $) {
		var gmap = $scope.find('.pt-plus-adv-map');
		(function($) {
			'use strict';
			$(document).ready(function() {
				   $(".pt-plus-overlay-map-content").each(function() {
					   var uid= $(this).data('uid');
					   var desc_color = $(this).data( 'desc_color');
					 var toggle_btn_color=$(this).data('toggle-btn-color');
					var toggle_active_color=$(this).data('toggle-active-color');
					
				   $('head').append('<style >.checked-'+uid+':not(checked) + .check-label-'+uid+':after,.checked-'+uid+' + .check-label-'+uid+':before{border-color: '+toggle_btn_color+';}.checked-'+uid+':checked + .check-label-'+uid+':after{    border-color: '+toggle_active_color+';}</style>');
				  });
				var elements = document.querySelectorAll('.pt-plus-adv-map');
				Array.prototype.forEach.call(elements, function(el) {
					var $this = $(el),
					data_id = $this.data( 'id' ),
					data = $this.data( 'adv-maps' ),
					data_style = $this.data( 'map-style' ),
					map = null,
					bounds = null,
					infoWindow = null,
					position = null;
					var styles1='';
					
					if(!$this.hasClass("map-loaded")){
					if(data_style=='style-1'){
						styles1='[{"featureType":"all","elementType":"all","stylers":[{"hue":"#ff0000"},{"saturation":-100},{"lightness":-30}]},{"featureType":"all","elementType":"labels.text.fill","stylers":[{"color":"#ffffff"}]},{"featureType":"all","elementType":"labels.text.stroke","stylers":[{"color":"#353535"}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#656565"}]},{"featureType":"poi","elementType":"geometry.fill","stylers":[{"color":"#505050"}]},{"featureType":"poi","elementType":"geometry.stroke","stylers":[{"color":"#808080"}]},{"featureType":"road","elementType":"geometry","stylers":[{"color":"#454545"}]}]';
						}else if(data_style=='style-2'){
						styles1='[{"featureType":"administrative","elementType":"all","stylers":[{"saturation":"-100"}]},{"featureType":"administrative.province","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"landscape","elementType":"all","stylers":[{"saturation":-100},{"lightness":65},{"visibility":"on"}]},{"featureType":"poi","elementType":"all","stylers":[{"saturation":-100},{"lightness":"50"},{"visibility":"simplified"}]},{"featureType":"road","elementType":"all","stylers":[{"saturation":"-100"}]},{"featureType":"road.highway","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"road.arterial","elementType":"all","stylers":[{"lightness":"30"}]},{"featureType":"road.local","elementType":"all","stylers":[{"lightness":"40"}]},{"featureType":"transit","elementType":"all","stylers":[{"saturation":-100},{"visibility":"simplified"}]},{"featureType":"water","elementType":"geometry","stylers":[{"hue":"#ffff00"},{"lightness":-25},{"saturation":-97}]},{"featureType":"water","elementType":"labels","stylers":[{"lightness":-25},{"saturation":-100}]}]';
						}else if(data_style=='style-3'){
						styles1='[{"featureType":"water","elementType":"geometry","stylers":[{"color":"#e9e9e9"},{"lightness":17}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#f5f5f5"},{"lightness":20}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#ffffff"},{"lightness":17}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#ffffff"},{"lightness":29},{"weight":0.2}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#ffffff"},{"lightness":18}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#ffffff"},{"lightness":16}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#f5f5f5"},{"lightness":21}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#dedede"},{"lightness":21}]},{"elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#ffffff"},{"lightness":16}]},{"elementType":"labels.text.fill","stylers":[{"saturation":36},{"color":"#333333"},{"lightness":40}]},{"elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#f2f2f2"},{"lightness":19}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#fefefe"},{"lightness":20}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#fefefe"},{"lightness":17},{"weight":1.2}]}]';
						}else if(data_style=='style-4'){
						styles1='[{"featureType":"administrative","elementType":"labels.text.fill","stylers":[{"color":"#444444"}]},{"featureType":"landscape","elementType":"all","stylers":[{"color":"#f2f2f2"}]},{"featureType":"poi","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"all","stylers":[{"saturation":-100},{"lightness":45}]},{"featureType":"road.highway","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"road.arterial","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"water","elementType":"all","stylers":[{"color":"#46bcec"},{"visibility":"on"}]}]';
						}else if(data_style=='style-5'){
						styles1='[{"featureType":"all","elementType":"labels.text.fill","stylers":[{"saturation":36},{"color":"#000000"},{"lightness":40}]},{"featureType":"all","elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#000000"},{"lightness":16}]},{"featureType":"all","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#000000"},{"lightness":20}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#000000"},{"lightness":17},{"weight":1.2}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":20}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":21}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#000000"},{"lightness":17}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#000000"},{"lightness":29},{"weight":0.2}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":18}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":16}]},{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":19}]},{"featureType":"water","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":17}]}]';
						}else if(data_style=='style-6'){
						styles1='[{"elementType":"geometry","stylers":[{"hue":"#ff4400"},{"saturation":-68},{"lightness":-4},{"gamma":0.72}]},{"featureType":"road","elementType":"labels.icon"},{"featureType":"landscape.man_made","elementType":"geometry","stylers":[{"hue":"#0077ff"},{"gamma":3.1}]},{"featureType":"water","stylers":[{"hue":"#00ccff"},{"gamma":0.44},{"saturation":-33}]},{"featureType":"poi.park","stylers":[{"hue":"#44ff00"},{"saturation":-23}]},{"featureType":"water","elementType":"labels.text.fill","stylers":[{"hue":"#007fff"},{"gamma":0.77},{"saturation":65},{"lightness":99}]},{"featureType":"water","elementType":"labels.text.stroke","stylers":[{"gamma":0.11},{"weight":5.6},{"saturation":99},{"hue":"#0091ff"},{"lightness":-86}]},{"featureType":"transit.line","elementType":"geometry","stylers":[{"lightness":-48},{"hue":"#ff5e00"},{"gamma":1.2},{"saturation":-23}]},{"featureType":"transit","elementType":"labels.text.stroke","stylers":[{"saturation":-64},{"hue":"#ff9100"},{"lightness":16},{"gamma":0.47},{"weight":2.7}]}]';
						}else if(data_style=='style-7'){
						styles1='[{"featureType":"water","stylers":[{"color":"#0e171d"}]},{"featureType":"landscape","stylers":[{"color":"#1e303d"}]},{"featureType":"road","stylers":[{"color":"#1e303d"}]},{"featureType":"poi.park","stylers":[{"color":"#1e303d"}]},{"featureType":"transit","stylers":[{"color":"#182731"},{"visibility":"simplified"}]},{"featureType":"poi","elementType":"labels.icon","stylers":[{"color":"#f0c514"},{"visibility":"off"}]},{"featureType":"poi","elementType":"labels.text.stroke","stylers":[{"color":"#1e303d"},{"visibility":"off"}]},{"featureType":"transit","elementType":"labels.text.fill","stylers":[{"color":"#e77e24"},{"visibility":"off"}]},{"featureType":"road","elementType":"labels.text.fill","stylers":[{"color":"#94a5a6"}]},{"featureType":"administrative","elementType":"labels","stylers":[{"visibility":"simplified"},{"color":"#e84c3c"}]},{"featureType":"poi","stylers":[{"color":"#e84c3c"},{"visibility":"off"}]}]';
					}
					var _toBuild = [];
					var build = function() {
						data.options.mapTypeId = google.maps.MapTypeId[data.options.mapTypeId];
						data.options.styles = data.style;
						if(styles1!=''){
							data.options.styles =JSON.parse(styles1);
							
						}          
						
						bounds = new google.maps.LatLngBounds();
						map = new google.maps.Map(document.getElementById(data_id), data.options);
						infoWindow = new google.maps.InfoWindow();
						
						map.setOptions({
							scrollwheel : data.options.scrollwheel,
							panControl : data.options.panControl,
							draggable:  data.options.draggable,
							zoomControl:  data.options.zoomControl,
							mapTypeControl:  data.options.mapTypeControl,
							scaleControl:  data.options.scaleControl,
							fullscreenControl:  data.options.fullscreenControl,
							streetViewControl: data.options.streetViewControl,
						});
						var marker, i;
						map.setTilt(45);
						
						
						
						google.maps.event.addListener(infoWindow , 'domready', function() {
							
							
							var iwOuter = $('.gm-style-iw');
							var iwBackground = iwOuter.prev();
							
							var parentdiv = iwOuter.parent('div');
							parentdiv.addClass('marker-icon');
							var iwCloseBtn = iwOuter.next();
							iwCloseBtn.hide();
							
							iwOuter.addClass('marker-title');
							
							
						});
						
						
						
						for (i = 0; i < data.places.length; i++) {
							position = new google.maps.LatLng(data.places[i].latitude, data.places[i].longitude);
							
							bounds.extend(position);
							
							marker = new google.maps.Marker({
								position: position,
								map: map,
								title: data.places[i].address,
								icon: data.places[i].pin_icon
							});
							
							google.maps.event.addListener(marker, 'click', (function(marker, i) {
								return function() { 
									if(data.places[i].address.length > 1) {
										infoWindow.setContent('<div class="gmap_info_content"><p>'+ data.places[i].address +'</p></div>');
									}
									
									infoWindow.open(map, marker);
								};
							})(marker, i));
							
							map.fitBounds(bounds);
						}
						
						
						var boundsListener = google.maps.event.addListener((map), 'idle', function(event) {
							this.setZoom(data.options.zoom);
							google.maps.event.removeListener(boundsListener);
						});
						
						
						var update = function() {
							google.maps.event.trigger(map, "resize");
							map.setCenter(position);
						};
						update();
					};
					var initAll = function() {
						for( var i = 0, l = _toBuild.length; i < l; i++ ) {
							_toBuild[i]();
						}
					};
					var initialize= function() {
						initAll();
					};
					
					_toBuild.push( build );
					initialize();					
					$this.addClass("map-loaded");
					}
					
				});
					
			});
		})(jQuery);
	};
	var WidgetRowBackgroundHandler = function ($scope, $) {
		var row_bg_elem = $scope.find('.pt-plus-row-set').eq(0);
				
		var parent_row= row_bg_elem.closest('section.elementor-element');
		var wid_sec=$scope.closest('section.elementor-element');
		
		if(wid_sec.length){
			var widget_remove_old=$(wid_sec).find("> .pt-plus-row-set");
			widget_remove_old.remove();
			var remove_page_gradient=$scope.closest('.elementor').find("> .plus-row-bg-gradient");
			remove_page_gradient.remove();	
			var scroll_section_bg=$scope.closest('.elementor').find("> .plus-scroll-sections-bg");
			scroll_section_bg.remove();			
		}
		var animate_gradient= $scope.find('.pt-plus-row-set .plus-row-bg-gradient').data('full-page');
		if(animate_gradient=='yes'){
			var page_gradient= $scope.find('.plus-row-bg-gradient')
			row_bg_elem.closest('.elementor').prepend(page_gradient);
			row_bg_elem.closest('.elementor').css("position",'inherit');
			plus_onscroll_bg();
		}
		if(parent_row){			
			if($scope.find('.pt-plus-row-set .plus-scroll-sections-bg').length>0){
				var scroll_sec_bg= $scope.find('.plus-scroll-sections-bg')
				row_bg_elem.closest('.elementor').prepend(scroll_sec_bg);
				row_bg_elem.closest('.elementor').css("position",'inherit');
				plus_onscroll_bg();				
			}			
			$( parent_row ).prepend(row_bg_elem);
			parent_row.css("position","relative");
			var bg_sec=$(wid_sec).find("> .pt-plus-row-set");
			if(bg_sec.data("section-hidden") !=undefined || bg_sec.data("section-hidden") !=''){
				if(!parent_row.hasClass("elementor-element-edit-mode")){
					parent_row.css("overflow",bg_sec.attr("data-section-hidden"));
				}
			}
		}
		if(wid_sec.find(".snow-particles").length){
			var snow_particles = document.querySelector('.snow-particles');
			snow_particles_background($('.snow-particles').parent(), snow_particles);
		}
		/*auto moving image*/
		if(wid_sec.find(".columns-bg-image.columns_animated_bg,.image-bgscroll").length){
			$('.columns-bg-image.columns_animated_bg,.image-bgscroll').each(function() {
				var $self = $(this),
				dir = $self.data('direction'),
				speed = 100 - $self.data('parallax_sense'),
				coords = 0,
				mobileEnabled = ($self.data('mobile_enable') && $self.data('mobile_enable') == '1') ? true : false;
				
				if(!mobileEnabled && Modernizr.touch) return;
				
				setInterval(function() {
					if(dir == 'left' || dir == 'bottom')
					coords -= 1;
					else
					coords += 1;
					if(dir == 'left' || dir == 'right')
					$self.css('backgroundPosition', coords +'px 50%');
					else
					$self.css('backgroundPosition', '50% '+ coords + 'px');
				}, speed);
			});
		}
		/*auto moving image*/
		/* mouse tilt parallax image*/
		if(wid_sec.find(".pt_plus_image_parallax_inner_hover").length){
		
			$(".pt_plus_image_parallax_inner_hover").waypoint(function() {
				$(".pt_plus_image_parallax_inner_hover").each(function(){
					var dopacity= $(this).attr('data-opacity');
					var damount= $(this).attr('data-amount');
					var dperspective= $(this).attr('data-perspective');
					var dscale= $(this).attr('data-scale');
					var dtype= $(this).attr('data-type');
					
					$(this).css('opacity',dopacity);		
					var offset = 0;
					if ( dtype === 'tilt' ) {
						offset = - parseInt( damount ) * .6 + '%';
						} else {
						offset = - parseInt( damount ) + 'px';
					}
					$(this).css('top',offset);
					$(this).css('left',offset);
					$(this).css('right',offset);
					$(this).css('bottom',offset);
					$(this).css('width','auto');
					$(this).css('height','auto');
					$(this).css('transform','scale('+dscale+') perspective('+dperspective+'px)');
				});
			}, { offset: '85%' });
			var elements = document.querySelectorAll('.pt_plus_image_mouse_hover');
			
			Array.prototype.forEach.call(elements, function(el, i) {
				// find Row
				var row = el.parentNode;
				//row.style.overflow = 'hidden';
				row.classList.add('image_parallax_row');
				
			});
			
			
			// Bind to mousemove so animate the hover row
			var elements = document.querySelectorAll('.image_parallax_row');
			Array.prototype.forEach.call(elements, function(row, i) {
				
				row.addEventListener('mousemove', function(e) {
					
					// Get the parent row
					var parentRow = e.target.parentNode;
					while ( ! parentRow.classList.contains('image_parallax_row') ) {
						
						if ( parentRow.tagName === 'HTML' ) {
							return;
						}
						
						parentRow = parentRow.parentNode;
					}
					
					// Get the % location of the mouse position inside the row
					var rect = parentRow.getBoundingClientRect();
					var top = e.pageY - ( rect.top + window.pageYOffset );
					var left = e.pageX  - ( rect.left + window.pageXOffset );
					top /= parentRow.clientHeight;
					left /= parentRow.clientWidth;
					
					// Move all the hover inner divs
					var hoverRows = parentRow.querySelectorAll('.pt_plus_image_parallax_inner_hover');
					Array.prototype.forEach.call(hoverRows, function(hoverBg, i) {
						
						// Parameters
						var amount = parseFloat( hoverBg.getAttribute( 'data-amount' ) );
						var dperspective = parseFloat( hoverBg.getAttribute( 'data-perspective' ) );
						var dscale = parseFloat( hoverBg.getAttribute( 'data-scale' ) );
						var inverted = hoverBg.getAttribute( 'data-inverted' ) === 'true';
						var transform;
						
						if ( hoverBg.getAttribute( 'data-type' ) === 'tilt' ) {
							
							var rotateY = left * amount - amount / 2;
							var rotateX = ( 1 - top ) * amount - amount / 2;
							if ( inverted ) {
								rotateY = ( 1 - left ) * amount - amount / 2;
								rotateX = top * amount - amount / 2;
							}
							
							transform = 'scale('+dscale+') perspective('+dperspective+'px) ';
							transform += 'rotateY(' + rotateY + 'deg) ';
							transform += 'rotateX(' + rotateX + 'deg) ';
							
							hoverBg.style.transition = 'all 0s';
							hoverBg.style.webkitTransform = transform;
							hoverBg.style.transform = transform;
							
							} else {
							
							var moveX = left * amount - amount / 2;
							var moveY = top * amount - amount / 2;
							if ( inverted ) {
								moveX *= -1;
								moveY *= -1;
							}
							transform = 'scale('+dscale+') translate3D(' + moveX + 'px, ' + moveY + 'px, 0) ';
							
							hoverBg.style.transition = 'all 0s';
							hoverBg.style.webkitTransform = transform;
							hoverBg.style.transform = transform;
							}
						
					});
				});
				
				
				// Bind to mousemove so animate the hover
				row.addEventListener('mouseout', function(e) {
					
					// Get the parent row
					var parentRow = e.target.parentNode;
					while ( ! parentRow.classList.contains('image_parallax_row') ) {
						
						if ( parentRow.tagName === 'HTML' ) {
							return;
						}
						
						parentRow = parentRow.parentNode;
					}
					
					// Reset all the animations
					var hoverRows = parentRow.querySelectorAll('.pt_plus_image_parallax_inner_hover');
					Array.prototype.forEach.call(hoverRows, function(hoverBg, i) {
						
						var amount = parseFloat( hoverBg.getAttribute( 'data-amount' ) );
						var scale = parseFloat( hoverBg.getAttribute( 'data-scale' ) );
						var perspective = parseFloat( hoverBg.getAttribute( 'data-perspective' ) );
						
						hoverBg.style.transition = 'all 3s ease-in-out';
						if ( hoverBg.getAttribute( 'data-type' ) === 'tilt' ) {
							hoverBg.style.webkitTransform = 'scale('+scale+') perspective('+perspective+'px) rotateY(0) rotateX(0)';
							hoverBg.style.transform = 'scale('+scale+') perspective('+perspective+'px) rotateY(0) rotateX(0)';
							} else {
							hoverBg.style.webkitTransform = 'scale('+scale+') translate3D(0, 0, 0)';
							hoverBg.style.transform = 'scale('+scale+') translate3D(0, 0, 0)';
						}
						
					});
				});
			});
		}
		/* mouse tilt parallax image*/
		/* mouse hover parallax image*/
		if(wid_sec.find(".pt_plus_mordern_image_parallax").length){
			var elements = document.querySelectorAll('.pt_plus_mordern_image_parallax');
			
			Array.prototype.forEach.call(elements, function(el, i) {
				// find Row
				var row = el.parentNode;
				/*while ( ! row.classList.contains('elementor-element') && ! row.classList.contains('elementor-element') ) {
					if ( row.tagName === 'HTML' ) {
						return;
					}
					row = row.parentNode;
				}*/
				
				//row.parentElement.style.overflow = 'hidden';
				row.parentElement.classList.add('image_parallax_row');		
			});
			// Bind to mousemove so animate the hover row
			var elements = document.querySelectorAll('.image_parallax_row');
			Array.prototype.forEach.call(elements, function(row, i) {
				
				row.addEventListener('mousemove', function(e) {
					
					// Get the parent row
					var parentRow = e.target.parentNode;
					while ( ! parentRow.classList.contains('image_parallax_row') ) {
						
						if ( parentRow.tagName === 'HTML' ) {
							return;
						}
						
						parentRow = parentRow.parentNode;
					}
					
					// Get the % location of the mouse position inside the row
					var rect = parentRow.getBoundingClientRect();
					var top = e.pageY - ( rect.top + window.pageYOffset );
					var left = e.pageX  - ( rect.left + window.pageXOffset );
					top /= parentRow.clientHeight;
					left /= parentRow.clientWidth;
					
					// Move all the hover inner divs
					var hoverRows = parentRow.querySelectorAll('.parallax_image');
					Array.prototype.forEach.call(hoverRows, function(hoverBg, i) {
						
						// Parameters
						var amount = parseFloat( hoverBg.getAttribute( 'data-parallax' ) );
						TweenLite.to( hoverBg, 0.2, {x : -(( e.clientX - (window.innerWidth/2) ) / amount ),y : -(( e.clientY - (window.innerHeight/2) ) / amount ) });
					});
				});
				
				
				// Bind to mousemove so animate the hover row to it's default state
				row.addEventListener('mouseout', function(e) {
					
					// Get the parent row
					var parentRow = e.target.parentNode;
					while ( ! parentRow.classList.contains('image_parallax_row') ) {
						
						if ( parentRow.tagName === 'HTML' ) {
							return;
						}
						
						parentRow = parentRow.parentNode;
					}
					
					// Reset all the animations
					var hoverRows = parentRow.querySelectorAll('.parallax_image');
					Array.prototype.forEach.call(hoverRows, function(hoverBg, i) {
						
						var amount = parseFloat( hoverBg.getAttribute( 'data-parallax' ) );
						
						TweenLite.to( hoverBg, 0.2, {x : -(( e.clientX - (window.innerWidth/2) ) / amount ),y : -(( e.clientY - (window.innerHeight/2) ) / amount ) });
						
						
					});
				});
			});
		}
		/* mouse hover parallax image*/
		/* mouse scroll parallax image*/
		if(wid_sec.find(".row-parallax-bg-img").length){
			var controller = new ScrollMagic.Controller();
			$('.row-parallax-bg-img').each(function(index, elem){
				var tween = 'tween1-'+index;
				tween = new TimelineMax();
				var lengthBox = $(elem).find('.parallax-bg-img').length;
				
				var $bcg =  $(elem).find('.parallax-bg-img');
				
				var slideParallaxScene = new ScrollMagic.Scene({
					triggerElement: elem,
					triggerHook: 1,
					duration: "200%"
				})
				.setTween(TweenMax.fromTo($bcg, 1, {delay:0.5,backgroundPositionY: '20%', ease:Power0.easeNone},{delay:0.5,backgroundPositionY: '80%', ease:Power0.easeNone}))
				.addTo(controller);
			})
		}
		/* mouse scroll parallax image*/
		if(wid_sec.find(".row-animated-bg").length){
			$(".row-animated-bg").each(function() {
				var data_id= $(this).data('id');
				var data_time=$(this).data('bg-time');
				var colors =$(this).data('bg');
				$('.'+data_id).animatedBG({
					colorSet: colors,
					speed: data_time
				});
			});
		}
		/* mouse scroll parallax image*/
		if(wid_sec.find(".columns-video-bg video, .columns-video-bg .columns-bg-frame").length){
			setTimeout(function(){
			$('.columns-video-bg video, .columns-video-bg .columns-bg-frame').pt_plus_VideoBgInit();
				$('.self-hosted-videos').each(function() {
					var $self=$(this);
					$self[0].play();
				});
			}, 100);
			$.fn.pt_plus_VideoBgInit = function() {
				return this.each(function() {
					var $self = $(this),
						ratio = 1.778,
						pWidth = $self.parent().width(),
						pHeight = $self.parent().height(),
						selfWidth,
						selfHeight;
					var setSizes = function() {
						if(pWidth / ratio < pHeight) {
							selfWidth = Math.ceil(pHeight * ratio);
							selfHeight = pHeight;
							$self.css({
								'width': selfWidth,
								'height': selfHeight
							});
						} else {
							selfWidth = pWidth;
							selfHeight = Math.ceil(pWidth / ratio);
							$self.css({
								'width': selfWidth,
								'height': selfHeight
							});
						}
					};				
					setSizes();
					$(window).on('resize', setSizes);
				});
			};
		}
		if(wid_sec.find(".columns-youtube-bg").length){
			var tag = document.createElement('script');

			tag.src = "//www.youtube.com/iframe_api";
			var firstScriptTag = document.getElementsByTagName('script')[0];
			firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
			
			var players = {};
			
			window.onYouTubeIframeAPIReady = function() {
				$('.columns-youtube-bg iframe').each(function() {
					var $self = $(this),
						id = $self.attr('id');
						players[id] = new YT.Player(id, {   
							   playerVars: {autoplay:1},    
							events: {
							   onReady: function(e) {
							   if($self.data('muted') && $self.data('muted') == '1') {
								  e.target.mute();
							   }
								  e.target.playVideo();
							   },
							   onStateChange: function(e) {
									if(e && e.data === 1){
										var videoHolder = document.getElementById('wrapper-'+id);
										if(videoHolder && videoHolder.id){
											videoHolder.classList.remove('tp-loading');
										}
									}else if(e && e.data === 0){
										e.target.playVideo()
									}
								}
							},
							
						});
					
				});
			};
		}
		if(wid_sec.find(".columns-vimeo-bg").length){
			$(document).ready(function() {
				$('.columns-vimeo-bg iframe').each(function() {
					var $self = $(this)
						id = $self.attr('id');
					if (window.addEventListener) {
						window.addEventListener('message', onMessageReceived, false);
					} else {
						window.attachEvent('onmessage', onMessageReceived, false);
					}
			
					function onMessageReceived(e) {
						var data = JSON.parse(e.data);
						
						switch (data.event) {
							case 'ready':
								$self[0].contentWindow.postMessage('{"method":"play", "value":1}','*');
								if($self.data('muted') && $self.data('muted') == '1') {
									$self[0].contentWindow.postMessage('{"method":"setVolume", "value":0}','*');
								}
								var videoHolder = document.getElementById('wrapper-'+id);
								if(videoHolder && videoHolder.id){
									videoHolder.classList.remove('tp-loading');
								}
								break;
						}
					}
				});
			});
		}
		
		/*--canvas style 2--*/
		if(wid_sec.find(".pt-plus-row-canvas-style-2").length){
			$(document).ready(function() {
			if ($(".pt-plus-row-canvas-style-2").length) {
				var can2_color =$(".pt-plus-row-canvas-style-2").attr('data-color');
				particlesJS("pt-plus-row-canvas-2",{particles:{number:{value:80,density:{enable:!0,value_area:800}},color:{value:can2_color},shape:{type:"circle",stroke:{width:0,color:"#000000"},polygon:{nb_sides:5},image:{src:"img/github.svg",width:100,height:100}},opacity:{value:.5,random:!1,anim:{enable:!1,speed:1,opacity_min:.1,sync:!1}},size:{value:2,random:!0,anim:{enable:!1,speed:40,size_min:.1,sync:!1}},line_linked:{enable:!0,distance:150,color:can2_color,opacity:.4,width:1},move:{enable:!0,speed:2,direction:"none",random:!1,straight:!1,out_mode:"out",bounce:!1,attract:{enable:!1,rotateX:600,rotateY:1200}}},interactivity:{detect_on:"canvas",events:{onhover:{enable:!0,mode:"grab"},onclick:{enable:!0,mode:"push"},resize:!0},modes:{grab:{distance:150,line_linked:{opacity:1}},bubble:{distance:400,size:40,duration:2,opacity:8,speed:3},repulse:{distance:200,duration:.4},push:{particles_nb:4},remove:{particles_nb:2}}},retina_detect:!0});
			}
			});
		}
		/*canvas style 2--*/
		/*--canvas style 3--*/
		if(wid_sec.find(".canvas-style-3").length){
			$(document).ready(function() {
			if ($(".canvas-style-3").length) {
				var can3_color =$(".canvas-style-3").attr('data-color');
				var can3_type=$(".canvas-style-3").attr('data-type');
				particlesJS("pt-plus-row-canvas-3", {"particles":{"number":{"value":80,"density":{"enable":true,"value_area":800}},"color":{"value":can3_color},"shape":{"type":can3_type,"stroke":{"width":4,"color":can3_color},"polygon":{"nb_sides":8},"image":{"src":"","width":100,"height":100}},"opacity":{"value":0.5,"random":false,"anim":{"enable":false,"speed":1,"opacity_min":0.1,"sync":false}},"size":{"value":2,"random":true,"anim":{"enable":false,"speed":102.321728,"size_min":25.174393,"sync":true}},"line_linked":{"enable":true,"distance":150,"color":can3_color,"opacity":0.4,"width":1},"move":{"enable":true,"speed":6,"direction":"none","random":false,"straight":false,"out_mode":"out","bounce":false,"attract":{"enable":false,"rotateX":600,"rotateY":1200}}},"interactivity":{"detect_on":"canvas","events":{"onhover":{"enable":false,"mode":"grab"},"onclick":{"enable":true,"mode":"push"},"resize":true},"modes":{"grab":{"distance":923.076923,"line_linked":{"opacity":1}},"bubble":{"distance":287.712287,"size":40,"duration":3.916083,"opacity":1,"speed":3},"repulse":{"distance":200,"duration":0.4},"push":{"particles_nb":4},"remove":{"particles_nb":2}}},"retina_detect":true});
			}
			});
		}
		/*canvas style 3--*/
		/*--canvas style 4--*/
		if(wid_sec.find(".canvas-style-4").length){
			$(document).ready(function() {
			if ($(".canvas-style-4").length) {
				var can4_color =$(".canvas-style-4").attr('data-color');
				var can4_type=$(".canvas-style-4").attr('data-type');
				particlesJS("pt-plus-row-canvas-4", {"particles":{"number":{"value":10,"density":{"enable":true,"value_area":800}},"color":{"value":can4_color},"shape":{"type":can4_type,"stroke":{"width":0,"color":can4_color},"polygon":{"nb_sides":5},"image":{"src":"","width":100,"height":100}},"opacity":{"value":0.505074,"random":true,"anim":{"enable":false,"speed":1,"opacity_min":0.1,"sync":false}},"size":{"value":100.213253,"random":true,"anim":{"enable":true,"speed":10,"size_min":40,"sync":false}},"line_linked":{"enable":false,"distance":481.023618,"color":can4_color,"opacity":1,"width":2},"move":{"enable":true,"speed":8,"direction":"none","random":false,"straight":false,"out_mode":"out","bounce":false,"attract":{"enable":false,"rotateX":600,"rotateY":1200}}},"interactivity":{"detect_on":"canvas","events":{"onhover":{"enable":false,"mode":"bubble"},"onclick":{"enable":false,"mode":"push"},"resize":true},"modes":{"grab":{"distance":431.568431,"line_linked":{"opacity":0.364281}},"bubble":{"distance":263.73626373626377,"size":55.944055,"duration":2.157842,"opacity":0.335664,"speed":3},"repulse":{"distance":239.760239,"duration":0.4},"push":{"particles_nb":4},"remove":{"particles_nb":2}}},"retina_detect":true});
			}
			});
		}
		/*canvas style 4--*/
		/*--canvas style 5--*/
		if(wid_sec.find(".pt-plus-row-canvas-style-5").length){
			$(document).ready(function() {
			if ($(".pt-plus-row-canvas-style-5").length) {
				var can_color =$(".pt-plus-row-canvas-style-5").attr('data-color');
				particlesJS("pt-plus-row-canvas-5",{particles:{number:{value:600,density:{enable:!0,value_area:800}},color:{value:can_color},shape:{type:"circle",stroke:{width:0,color:"#000000"},polygon:{nb_sides:5},image:{src:"",width:100,height:100}},opacity:{value:0,random:!1,anim:{enable:!1,speed:0,opacity_min:0,sync:!1}},size:{value:3,random:!0,anim:{enable:!1,speed:40,size_min:.1,sync:!1}},line_linked:{enable:!0,distance:32.068241,color:can_color,opacity:.8,width:1},move:{enable:!0,speed:4,direction:"none",random:!1,straight:!1,out_mode:"out",bounce:!1,attract:{enable:!1,rotateX:600,rotateY:1200}}},interactivity:{detect_on:"canvas",events:{onhover:{enable:true,mode:"repulse"},onclick:{enable:!1,mode:"push"},resize:!0},modes:{grab:{distance:400,line_linked:{opacity:1}},bubble:{distance:200,size:140,duration:2,opacity:8,speed:2},repulse:{distance:100,duration:.4},push:{particles_nb:4},remove:{particles_nb:2}}},retina_detect:!0});
			}
			});
		}
		/*canvas style 5--*/
		/*--canvas style 7--*/
		if(wid_sec.find(".canvas-style-6").length){
			$(document).ready(function() {
				var cancolor=$(".canvas-style-6").attr("data-canvas-color");
				$(".canvas-style-6").particleground({
				  minSpeedX: 0.1,
				  maxSpeedX: 0.3,
				  minSpeedY: 0.1,
				  maxSpeedY: 0.3,
				  directionX: "center",
				  directionY: "up",
				  density: 10000,
				  dotColor: cancolor,
				  lineColor: cancolor,
				  particleRadius: 7,
				  lineWidth: 1,
				  curvedLines: false,
				  proximity: 100,
				  parallax: true,
				  parallaxMultiplier: 5,
				  onInit: function() {},
				  onDestroy: function() {}
				});
			});
		}
		/*canvas style 7--*/
		/*--canvas style 7--*/
		if(wid_sec.find(".canvas-style-7").length){
			$(document).ready(function() {
			if ($(".canvas-style-7").length) {
				var can7_color =$(".canvas-style-7").attr('data-color');
				var can7_type=$(".canvas-style-7").attr('data-type');
				particlesJS("pt-plus-row-canvas-7", {"particles":{"number":{"value":400,"density":{"enable":true,"value_area":2840.9315098761817}},"color":{"value":can7_color},"shape":{"type":can7_type,"stroke":{"width":0,"color":can7_color},"polygon":{"nb_sides":5},"image":{"src":"","width":100,"height":100}},"opacity":{"value":0.5,"random":true,"anim":{"enable":false,"speed":1,"opacity_min":0.1,"sync":false}},"size":{"value":11,"random":true,"anim":{"enable":false,"speed":40,"size_min":0.1,"sync":false}},"line_linked":{"enable":false,"distance":224.4776885211732,"color":can7_color,"opacity":0.1683582663908799,"width":1.2827296486924182},"move":{"enable":true,"speed":3,"direction":"bottom","random":true,"straight":false,"out_mode":"bounce","bounce":false,"attract":{"enable":false,"rotateX":881.8766334760375,"rotateY":1200}}},"interactivity":{"detect_on":"canvas","events":{"onhover":{"enable":true,"mode":"bubble"},"onclick":{"enable":true,"mode":"repulse"},"resize":true},"modes":{"grab":{"distance":400,"line_linked":{"opacity":0.5}},"bubble":{"distance":400,"size":4,"duration":0.3,"opacity":1,"speed":3},"repulse":{"distance":200,"duration":0.4},"push":{"particles_nb":4},"remove":{"particles_nb":2}}},"retina_detect":true});
			}
			});
		}
		/*canvas style 7--*/
		
		/*------------- parallax images js ---*/
		if(wid_sec.find(".parallax_image").length){
			$(".parallax_image").each(function() {
				var win_width = $(window).width();
				var tablet_w=$(this).attr('data-tablet-width');
				var mobile_w=$(this).attr('data-mobile-width');
				if(win_width<768){
					$(this).css('width',tablet_w);
				}
				if(win_width<480){
					$(this).css('width',mobile_w);
				}
				if(win_width>=768){
					$(this).css('width','auto');
				}
			});
		}
		/*------------- parallax images js ---*/
		
	};
	
	/* Default load WidgetThePlusHandler */
	var WidgetThePlusHandler = function ($scope, $) {
		var row_bg_elem = $scope.find('.pt-plus-row-set').eq(0);
		
		var parent_row= row_bg_elem.parents('section.elementor-element');
		var wid_sec=$scope.parents('section.elementor-element');
		
		/*scroll reveal effect*/
		if(wid_sec.find(".pt-plus-reveal").length){
			$('.pt-plus-reveal').each(function() {
			var b=$(this);
			var uid=b.data('reveal-id');
			var color_1=b.data('effect-color-1');
			var color_2=b.data('effect-color-2');
				$('head').append("<style type='text/css'>."+uid+".animated::before{background: "+color_2+";}."+uid+".animated::after{background: "+color_1+";}</style>");
				b.waypoint(function(direction) {
					if( direction === 'down'){
						if(b.hasClass("animated")){
							b.hasClass("animated");
							}else{
							b.addClass("animated");
						}
					}
				}, {triggerOnce: true,  offset: '85%' } );
			});
		}
		/*scroll reveal effect*/
		
		/*mouse hover paralalx*/
		( function ( $ ) {
			'use strict';
			var $parallaxContainer 	  = $(".pt-plus-move-parallax");
			var $parallaxItems		    = $parallaxContainer.find(".parallax-move");
			var fixer  = 0.0008;
			if($parallaxContainer.length > 0){
				$(".pt-plus-move-parallax").on("mouseleave", function(event){
					var pageX =  event.pageX - ($(this).width() * 0.5);
					var pageY =  event.pageY - ($(this).height() * 0.5);
					$(this).find(".parallax-move").each(function(){
						var item 	= $(this);
						var speedX	= item.data("move_speed_x");  				
						var speedY	= item.data("move_speed_y");
						TweenLite.to(item,0.9,{
							x: (0)*fixer,
							y: (0)*fixer
						});
					});
				});
				
				$parallaxContainer.on('mousemove', function(e){
					$(this).find(".parallax-move").each(function(){
						var item 	= $(this);
						var speedX	= item.data("move_speed_x");
						var speedY	= item.data("move_speed_y");
						$(this).parallaxmovecontent(speedX,speedY, e);
					});
				});
			$.fn.parallaxmovecontent = function (resistancex, resistancey, mouse ) {
				var $el = $( this );
				TweenLite.to( $el, 0.5, {
					x : -(( mouse.clientX - (window.innerWidth/2) ) / resistancex),
					y : -(( mouse.clientY - (window.innerHeight/2) ) / resistancey)
				});
			};
			}
		} ( jQuery ) );	
		/*mouse hover paralalx*/
		/* 3d hover tilt effect*/
		if(wid_sec.find(".hover-tilt").length){
			$(".hover-tilt").hover3d({
				selector: ".blog-list-style-content,.portfolio-item-content,> .addbanner-block,> .addbanner_product_box,> .vc_single_image-wrapper,> .cascading-inner-loop,> .pt-plus-magic-box,> .call-to-action-img,> .blog-hover-inner-tilt,> .logo-image-wrap, .info-box-bg-box",
				shine: !1,
				invert: !0,
				sensitivity: 20,
			});
		}
		/* 3d hover tilt effect*/
		/* hover tilt js */
		if(wid_sec.find(".js-tilt").length){			
				$('.js-tilt').tilt();
		}
		/* hover tilt js */
		
		/* CountDown */
		if(wid_sec.find('.pt_plus_countdown').length>0){
		( function ( $ ) {
		"use strict";
			$(document).ready(function () {
				theplus_countdown();
				$(".pt_plus_countdown").change(function () {
					theplus_countdown();
				});
			});	
			function theplus_countdown(){
				$(".pt_plus_countdown").each(function () {
					var timer1 = $(this).attr("data-timer");
					var offset_timer = $(this).attr("data-offset");
					var text_days=$(this).data("days");
					var text_hours=$(this).data("hours");
					var text_minutes=$(this).data("minutes");
					var text_seconds=$(this).data("seconds");
					$(this).downCount({
						date: timer1,
						offset: offset_timer,
						text_day:text_days,
						text_hour:text_hours,
						text_minute:text_minutes,
						text_second:text_seconds,
					});
				});
			}
		} ( jQuery ) );
		}
		/* CountDown*/
		/*-social icon element----*/
		if(wid_sec.find('.ts-chaffle').length>0){
			( function ( $ ) {	
				'use strict';
				$(document).ready(function() {
					$('.ts-chaffle').chaffle({
						speed: 20,
						time: 140
					});
				});		
			} ( jQuery ) );
		}
		/*-social icon element----*/
		
		/*device carousel */
		if(wid_sec.find('.plus-device-carousal').length>0){
			var carousel_elem = $scope.find('.plus-device-carousal').eq(0);
			var $self=carousel_elem;
			var $uid=$self.data("id");			
			var infinite=$self.data("infinite");
			var autoplay=$self.data("autoplay");
			var autoplay_speed=$self.data("autoplay_speed");
			var speed=$self.data("speed");
			if(!$('.'+$uid).hasClass("done-carousel")){
				$('.'+$uid).slick({
						arrows:false,
						dots:false,
						infinite: infinite,
						speed: speed,
						autoplay: autoplay,
						autoplaySpeed: autoplay_speed,
						centerMode: true,
						centerPadding: '0px',
						slidesToShow: 1,
						slidesToScroll: 1,
						draggable:true,
						variableWidth: true,
					});
			$('.'+$uid).addClass("done-carousel");
			}
		}
		/*device carousel */
		
		
		
		/* switcher toggle load*/
		if(wid_sec.find('.theplus-switcher').length>0){
			var target = $scope.find('.theplus-switcher');
			var switch_toggle = target.find('.switch-toggle');
			var switch_1_toggle = target.find('.switch-1');
			var switch_2_toggle = target.find('.switch-2');
				$(document).ready(function(){
					var i,n,f,r;
					
					$(switch_1_toggle).unbind().on("click", function(e) {
						i = $(this).parents(".theplus-switcher").find(".switcher-section-1");
						n = $(this).parents(".theplus-switcher").find(".switcher-section-2");
						f = $(this).parents(".theplus-switcher").find(".switcher-toggle");
						r = $(this).parents(".theplus-switcher").find(".switch-toggle");
						r.prop("checked", !1),
						i.show(),
						n.hide(),
						f.hasClass("active") ? (f.removeClass("active").addClass("inactive")) : (f.removeClass("inactive").addClass("active"))
					});
					$(switch_2_toggle).unbind().on("click", function(e) {
						i = $(this).parents(".theplus-switcher").find(".switcher-section-1");
						n = $(this).parents(".theplus-switcher").find(".switcher-section-2");
						f = $(this).parents(".theplus-switcher").find(".switcher-toggle");
						r = $(this).parents(".theplus-switcher").find(".switch-toggle");
						r.prop("checked", !0),
						i.hide(),
						n.show(),
						f.hasClass("active") ? (f.removeClass("active").addClass("inactive")) : (f.removeClass("inactive").addClass("active"))
					});
				});
		}
		/* switcher toggle load*/
		/* post listing out*/
		if(wid_sec.find('.list-isotope').length>0){
			var b = window.theplus || {};
			b.window = $(window),
			b.document = $(document),
			b.windowHeight = b.window.height(),
			b.windowWidth = b.window.width();	
			b.list_isotope_Posts = function() {
				var c = function(c) {
					$('.list-isotope').each(function() {
						
						var e, c = $(this), d = c.data("layout-type"),f = {
							itemSelector: ".grid-item",
							resizable: !0,
							sortBy: "original-order"
						};
						var uid=c.data("id");
						var inner_c=$('.'+uid).find(".post-inner-loop");
						$('.'+uid).addClass("pt-plus-isotope layout-" + d),
						e = "masonry" === d  ? "packery" : "fitRows",
						f.layoutMode = e,
						function() {
							//b.initMetroIsotope(),
							inner_c.isotope(f)
						}();
					})
				};
				b.window.on("load resize", function() {
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
				}),
				$.browser.firefox = /firefox/.test(navigator.userAgent.toLowerCase()),
				$.browser.firefox && setTimeout(function() {
					c('[data-enable-isotope="1"]')
				}, 2500);
			},
			b.init = function() {				
				b.list_isotope_Posts();				
			}
			,
			b.init();
		}
		if(wid_sec.find('.list-isotope-metro').length>0){
			if ($('.list-isotope-metro').length) {
				
				$(window).on("load resize", function() {
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
				$.browser.firefox = /firefox/.test(navigator.userAgent.toLowerCase()),
				$.browser.firefox && setTimeout(function() {
					theplus_setup_packery_portfolio("*");
					$('.list-isotope-metro .post-inner-loop').isotope('layout');
				}, 2500);
			}			
		}
		if(wid_sec.find('.list-carousel-slick').length>0){
			var carousel_elem = $scope.find('.list-carousel-slick').eq(0);
			$(document).ready(function() {
				if (carousel_elem.length > 0) {
					if(!carousel_elem.hasClass("done-carousel")){
						theplus_carousel_list();
					}
				}
			});			
		}
		if(wid_sec.find('.gallery-list.gallery-style-3').length>0){
			$('.gallery-list.gallery-style-3 .grid-item').each( function() { $(this).hoverdir(); } );
		}
		/* post listing out*/
		if(wid_sec.find('.blog-list.blog-style-1,.gallery-list.gallery-style-2').length>0){
			$(document).ready(function($) {
				$(document).on('mouseenter',".blog-list.blog-style-1 .grid-item .blog-list-content,.gallery-list.gallery-style-2 .grid-item .gallery-list-content",function() {				
					$(this).find(".post-hover-content").slideDown(300)
				});
				$(document).on('mouseleave',".blog-list.blog-style-1 .grid-item .blog-list-content,.gallery-list.gallery-style-2 .grid-item .gallery-list-content",function() {
					$(this).find(".post-hover-content").slideUp(300)
				})
			});
		}
		if(wid_sec.find('.pt_plus_asb_wrapper.article-box.article-box-style-1').length>0){
			$(document).on('mouseenter',".article-box-style-1 .article-box-inner-content",function() {
				$(this).find(".asb-desc").slideDown(300)
				$(this).find(".pt-plus-button-wrapper").slideDown(300)	
			});
			$(document).on('mouseleave',".article-box-style-1 .article-box-inner-content",function() {
				$(this).find(".asb-desc").slideUp(300)
				$(this).find(".pt-plus-button-wrapper").slideUp(300)
			});
		}
	};
	/* Default load WidgetThePlusHandler */
	/* Backend load WidgetThePlusHandlerBackEnd */
	var WidgetThePlusHandlerBackEnd = function ($scope, $) {
		var wid_sec=$scope.parents('section.elementor-element');
		
		/*--- on load animation ----*/
		if(wid_sec.find(".animate-general").length){
				"use strict";
				$scope.find('.animate-general').each(function() {
					var c, p=$(this);
					if(!p.hasClass("animation-done")){
						if(p.find('.animated-columns').length){
							var b = $('.animated-columns',this);				
							var delay_time=p.data("animate-delay");
							
							c = p.find('.animated-columns');
							c.each(function() {
								if(!$(this).hasClass("animation-done")){
									$(this).css("opacity", "0");
								}
							});
							
							}else{			
							var b=$(this);
							var delay_time=b.data("animate-delay");
							
							if(b.data("animate-item")){
								c = b.find(b.data("animate-item"));
								c.each(function() {
									if(!$(this).hasClass("animation-done")){
										$(this).css("opacity", "0");
									}
								});
								}else{
								b.css("opacity", "0");
							}
						}
					}
				});
				
				var d = function() {
					$scope.find('.animate-general').each(function() {
						var c, d, p=$(this), e = "85%";
						var id=$(this).data("id");
						if(p.data("animate-columns")=="stagger"){
							var b = $('.animated-columns',this);
							var animation_stagger=p.data("animate-stagger");
							var delay_time=p.data("animate-delay");
							var out_delay_time=p.data("animate-out-delay");
							var duration_time=p.data("animate-duration");
							var out_duration_time=p.data("animate-out-duration");
							var d = p.data("animate-type");
							var o = p.data("animate-out-type");												
							var animate_offset = p.data("animate-offset");
							
							p.css("opacity","1");
							c = p.find('.animated-columns');
							p.waypoint(function(direction) {
								if( direction === 'down'){
									if(!c.hasClass("animation-done")){
										c.addClass("animation-done").removeClass("animation-out-done").velocity(d,{ delay: delay_time,duration: duration_time,display:'auto',stagger: animation_stagger});
									}
								}else if (direction === 'up' && o!='' && o!=undefined && !c.hasClass("animation-out-done")) {
									c.addClass("animation-out-done").removeClass("animation-done").velocity(o,{ delay: out_delay_time,duration: out_duration_time,display:'auto',stagger: animation_stagger});
								}
							}, { offset: animate_offset } );
							if(c){
								$('head').append("<style type='text/css'>."+id+" .animated-columns.animation-done{opacity:1;}</style>")
							}
							
							}else if(p.data("animate-columns")=="columns"){
							
							var b = $('.animated-columns',this);
							var delay_time=p.data("animate-delay");
							var out_delay_time=p.data("animate-out-delay");
							var d = p.data("animate-type");
							var o = p.data("animate-out-type");	
							var animate_offset = p.data("animate-offset");
							var duration_time=p.data("animate-duration");
							var out_duration_time=p.data("animate-out-duration");
							p.css("opacity","1");
							c = p.find('.animated-columns');
							c.each(function() {
								var bc=$(this);
								bc.waypoint(function(direction) {
									if( direction === 'down'){
										if(!bc.hasClass("animation-done")){
											bc.addClass("animation-done").removeClass("animation-out-done").velocity(d,{ delay: delay_time,duration: duration_time,drag:true,display:'auto'});
										}
									}else if (direction === 'up' && o!='' && o!=undefined && !bc.hasClass("animation-out-done")) {
										bc.addClass("animation-out-done").removeClass("animation-done").velocity(o,{ delay: out_delay_time,duration: out_duration_time,display:'auto'});
									}
								}, { offset: animate_offset } );
							});
							if(c){
								$('head').append("<style type='text/css'>."+id+" .animated-columns.animation-done{opacity:1;}</style>")
							}
							}else{
							var b = $(this);
							var delay_time=b.data("animate-delay");
							var out_delay_time=b.data("animate-out-delay");
							var duration_time=b.data("animate-duration");
							var out_duration_time=p.data("animate-out-duration");
							d = b.data("animate-type"),
							o = b.data("animate-out-type"),
							animate_offset = b.data("animate-offset"),
							b.waypoint(function(direction ) {
								if( direction === 'down'){
									if(!b.hasClass("animation-done")){
										b.addClass("animation-done").removeClass("animation-out-done").velocity(d, {delay: delay_time,duration: duration_time,display:'auto'});
									}
								}else if (direction === 'up' && o!='' && o!=undefined && !b.hasClass("animation-out-done")) {
									if(!b.hasClass("animation-out-done")){
										b.addClass("animation-out-done").removeClass("animation-done").velocity(o,{ delay: out_delay_time,duration: out_duration_time,display:'auto' });
									}
								}
							}, { offset: animate_offset } );
						}
					})
				},
				e = function() {
					$(".call-on-waypoint").each(function() {
						var c = $(this);
						c.waypoint(function() {
							c.trigger("on-waypoin")
							}, {
							triggerOnce: !0,
							offset: "bottom-in-view"
						})
					})
				};				
				
				e(); d();
				
		}
		/*--- on load animation ----*/
		
		/*magic scroll */
		if(wid_sec.find(".magic-scroll").length){
			( function ( $ ) {	
			'use strict';
				$(document).ready(function(){
					pt_plus_animateParalax();
				});
			} ( jQuery ) );
		}
		/*magic scroll */
		/*cascading Slide Show Image*/
		if(wid_sec.find(".cascading-block").length > 0){
			cascading_overflow();
		}
		if(wid_sec.find('.slide_show_image').length>0){
			cascading_slide_show_image();
		}
		/*cascading Slide Show Image*/
		/*Text Heading Animation*/
		if(wid_sec.find('.pt-plus-cd-headline').length>0){
			plus_heading_animation();
		}
		/*Text Heading Animation*/
		/* bg creative parallax */
		if(wid_sec.find('.creative-simple-img-parallax').length>0){
				plus_bgimage_scrollparallax();
		}
		/* bg creative parallax */
		/* animated svg */
		$(document).ready(function() {
			setTimeout(function(){
			$('.pt_plus_row_bg_animated_svg').pt_plus_animated_svg();			
			$('.pt_plus_animated_svg,.ts-hover-draw-svg').pt_plus_animated_svg();
			$('body').find('.pt_plus_row_bg_animated_svg').attr('style', 'stroke:black');
		}, 100);
		});
		/* animated svg */
		if(wid_sec.find('.list-isotope-metro').length>0){
			$(document).ready(function(){
				"use strict";
				var container=wid_sec.find('.list-isotope-metro');
				var uid=container.data("id");
				var columns=container.attr('data-metro-columns');
				var metro_style=container.attr('data-metro-style');
				theplus_backend_packery_portfolio(uid,columns,metro_style);
			});
		}
		if(wid_sec.find('.list-carousel-slick').length>0){
			var carousel_elem = $scope.find('.list-carousel-slick').eq(0);
			$(document).ready(function() {
				if (carousel_elem.length > 0) {
					if(!carousel_elem.hasClass("done-carousel")){
						theplus_carousel_list();
					}
				}
			});
		}
		if(wid_sec.find('.theplus-contact-form').length){
			$(document).ready(function() {
				plus_cf7_form();
			});
		}
		/*-video post---*/
		if(wid_sec.find('iframe').length>0){
			initFluidVids();
		}
		/*-video post ----*/
		if(wid_sec.find(".columns-vimeo-bg").length){
			$(document).ready(function() {
				$('.columns-vimeo-bg iframe').each(function() {
					var $self = $(this)
						id = $self.attr('id');
					if (window.addEventListener) {
						window.addEventListener('message', onMessageReceived, false);
					} else {
						window.attachEvent('onmessage', onMessageReceived, false);
					}
			
					function onMessageReceived(e) {
						var data = JSON.parse(e.data);
						
						switch (data.event) {
							case 'ready':
								$self[0].contentWindow.postMessage('{"method":"play", "value":1}','*');
								if($self.data('muted') && $self.data('muted') == '1') {
									$self[0].contentWindow.postMessage('{"method":"setVolume", "value":0}','*');
								}
								var videoHolder = document.getElementById('wrapper-'+id);
								if(videoHolder && videoHolder.id){
									videoHolder.classList.remove('tp-loading');
								}
								break;
						}
					}
				});
			});
		}
	};
	/* Backend load WidgetThePlusHandler */
	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/tp-advanced-typography.default', WidgetAdvancedTypographyHandler);	
		elementorFrontend.hooks.addAction('frontend/element_ready/tp-timeline.default', WidgetTimeLineContentHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/tp-animated-service-boxes.default', WidgetAnimatedServicesBoxHandler);		
		elementorFrontend.hooks.addAction('frontend/element_ready/tp-shape-divider.default', WidgetShapeDividerHandler);	
		elementorFrontend.hooks.addAction('frontend/element_ready/tp-scroll-navigation.default', WidgetScrollNavHandler);		
		elementorFrontend.hooks.addAction('frontend/element_ready/tp-row-background.default', WidgetRowBackgroundHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/global', WidgetThePlusHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/tp-google-map.default', WidgetGoogleMapHandler);		
		if (elementorFrontend.isEditMode()) {		
			elementorFrontend.hooks.addAction('frontend/element_ready/tp-google-map.default', WidgetGoogleMapHandler);
			elementorFrontend.hooks.addAction('frontend/element_ready/tp-row-background.default', WidgetRowBackgroundHandler);
			elementorFrontend.hooks.addAction('frontend/element_ready/global', WidgetThePlusHandler);
			elementorFrontend.hooks.addAction('frontend/element_ready/global', WidgetThePlusHandlerBackEnd);
			
		}
	});
})(jQuery);

/*-video post ! fluidvids.js v2.4.1*/
!function(e,t){"function"==typeof define&&define.amd?define(t):"object"==typeof exports?module.exports=t:e.fluidvids=t()}(this,function(){"use strict";function e(e){return new RegExp("^(https?:)?//(?:"+d.players.join("|")+").*$","i").test(e)}function t(e,t){return parseInt(e,10)/parseInt(t,10)*100+"%"}function i(i){if((e(i.src)||e(i.data))&&!i.getAttribute("data-fluidvids")){var n=document.createElement("div");i.parentNode.insertBefore(n,i),i.className+=(i.className?" ":"")+"fluidvids-item",i.setAttribute("data-fluidvids","loaded"),n.className+="fluidvids",n.style.paddingTop=t(i.height,i.width),n.appendChild(i)}}function n(){var e=document.createElement("div");e.innerHTML="<p>x</p>"}var d={selector:["iframe","object"],players:["www.youtube.com","player.vimeo.com"]},r=document.head||document.getElementsByTagName("head")[0];return d.render=function(){for(var e=document.querySelectorAll(d.selector.join()),t=e.length;t--;)i(e[t])},d.init=function(e){for(var t in e)d[t]=e[t];d.render(),n()},d});

function initFluidVids(){
	fluidvids.init({ selector: ['iframe:not(.pt-plus-bg-video)'],players: ['www.youtube.com', 'player.vimeo.com']});	
}
( function( $ ) {
	'use strict';
	if($('iframe').length){
		$(document).ready(function(){
			initFluidVids();
		});
		$('body').on('post-load', initFluidVids);
	}
})(jQuery);
/*-video post ----*/
/*Page sections background color scroll*/
function plus_onscroll_bg(){
	var $=jQuery;
	var pageWrapper = $('.plus-scroll-sections-bg');
	if(pageWrapper.length > 0 && pageWrapper.data("scrolling-effect")=='yes'){
		var bgColors = pageWrapper.data('bg-colors');
		if(bgColors){
			var paraent_node=pageWrapper.closest(".elementor");
			var i=0;
			var arry_len=bgColors.length;
			paraent_node.find(".elementor-section-wrap >.elementor-element").each(function(){
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
			const contentElems = Array.from(document.querySelectorAll('.elementor-'+parent_id+'>.elementor-inner > .elementor-section-wrap > .elementor-element'));
			var totalEle=contentElems.length;
			var step=0;
			var position;			
				contentElems.forEach((el,pos) => {
					const scrollElemToWatch = pos ? contentElems[pos] : contentElems[pos];
					pos = pos ? pos : totalEle;
					const watcher = scrollMonitor.create(scrollElemToWatch,-300);
				
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
/* plus extra Column Sticky option*/
( function( $ ) {

	'use strict';

	var PlusExtra = {

		init: function() {
			elementorFrontend.hooks.addAction( 'frontend/element_ready/column', PlusExtra.plus_Sticky_Column );
		},

		plus_Sticky_Column: function( $scope ) {
			var $target  = $scope,
				$window  = $( window ),
				columnId = $target.data( 'id' ),
				editMode = Boolean( elementorFrontend.isEditMode() ),
				settings = {},
				stickyInstance = null,
				stickyInstanceOptions = {
					topSpacing: 40,
					bottomSpacing: 40,
					containerSelector: '.elementor-row',
					innerWrapperSelector: '.elementor-column-wrap'
				};

			if ( ! editMode ) {
				settings = $target.data( 'plus-sticky-column-settings' );

				if ( $target.hasClass( 'plus-sticky-column-sticky' ) ) {

					if ( -1 !== settings['stickyOn'].indexOf( elementorFrontend.getCurrentDeviceMode() ) ) {

						stickyInstanceOptions.topSpacing = settings['topSpacing'];
						stickyInstanceOptions.bottomSpacing = settings['bottomSpacing'];

						$target.data( 'stickyColumnInit', true );
						stickyInstance = new StickySidebar( $target[0], stickyInstanceOptions );

						$window.on( 'resize.PlusExtraColumnSticky orientationchange.PlusExtraColumnSticky', PlusExtraTools.debounce( 50, resizeDebounce ) );
					}
				}
			} else {
				settings = PlusExtra.stickycolumnSettings( columnId );

				if ( 'true' === settings['sticky'] ) {
					$target.addClass( 'plus-sticky-column-sticky' );

					if ( -1 !== settings['stickyOn'].indexOf( elementorFrontend.getCurrentDeviceMode() ) ) {
						stickyInstanceOptions.topSpacing = settings['topSpacing'];
						stickyInstanceOptions.bottomSpacing = settings['bottomSpacing'];

						$target.data( 'stickyColumnInit', true );
						stickyInstance = new StickySidebar( $target[0], stickyInstanceOptions );

						$window.on( 'resize.PlusExtraColumnSticky orientationchange.PlusExtraColumnSticky', PlusExtraTools.debounce( 50, resizeDebounce ) );
					}
				}
			}

			function resizeDebounce() {
				var currentDeviceMode = elementorFrontend.getCurrentDeviceMode(),
					availableDevices  = settings['stickyOn'] || [],
					isInit            = $target.data( 'stickyColumnInit' );

				if ( -1 !== availableDevices.indexOf( currentDeviceMode ) ) {

					if ( ! isInit ) {
						$target.data( 'stickyColumnInit', true );
						stickyInstance = new StickySidebar( $target[0], stickyInstanceOptions );
						stickyInstance.updateSticky();
					}
				} else {
					$target.data( 'stickyColumnInit', false );
					stickyInstance.destroy();
				}
			}

		},

		stickycolumnSettings: function( columnId ) {
			var editorElements = null,
				columnData     = {};

			if ( ! window.elementorFrontend.hasOwnProperty( 'elements' ) ) {
				return false;
			}

			editorElements = window.elementorFrontend.elements;

			if ( ! editorElements.models ) {
				return false;
			}

			$.each( editorElements.models, function( index, obj ) {

				$.each( obj.attributes.elements.models, function( index, obj ) {
					if ( columnId == obj.id ) {
						columnData = obj.attributes.settings.attributes;
					}
				} );

			} );
			
			return {
				'sticky': columnData['plus_column_sticky'] || false,
				'topSpacing': columnData['plus_sticky_top_spacing'] || 40,
				'bottomSpacing': columnData['plus_sticky_bottom_spacing'] || 40,
				'stickyOn': columnData['plus_sticky_enable_on'] || [ 'desktop', 'tablet', 'mobile']
			}
		},

	};

	$( window ).on( 'elementor/frontend/init', PlusExtra.init );

	var PlusExtraTools = {
		debounce: function( threshold, callback ) {
			var timeout;

			return function debounced( $event ) {
				function delayed() {
					callback.call( this, $event );
					timeout = null;
				}

				if ( timeout ) {
					clearTimeout( timeout );
				}

				timeout = setTimeout( delayed, threshold );
			};
		}
	}

}( jQuery, window.elementorFrontend ) );

/* plus extra Column Sticky option*/

/*--- on load animation ----*/
( function ( $ ) {
	'use strict';
	$(document).ready(function() {
		"use strict";
		$('.animate-general').each(function() {
			var c, p=$(this);
			if(!p.hasClass("animation-done")){
				if(p.find('.animated-columns').length){
					var b = $('.animated-columns',this);				
					var delay_time=p.data("animate-delay");
					
					c = p.find('.animated-columns');
					c.each(function() {
						$(this).css("opacity", "0");
					});
					
					}else{			
					var b=$(this);
					var delay_time=b.data("animate-delay");
					
					if(b.data("animate-item")){
						c = b.find(b.data("animate-item"));
						c.each(function() {
							$(this).css("opacity", "0");
						});
						}else{
						b.css("opacity", "0");
					}
				}
			}
		});
		
		var d = function() {
			$('.animate-general').each(function() {
				var c, d, p=$(this), e = "85%";
				var id=$(this).data("id");
				if(p.data("animate-columns")=="stagger"){
					var b = $('.animated-columns',this);
					var animation_stagger=p.data("animate-stagger");
					var delay_time=p.data("animate-delay");
					var out_delay_time=p.data("animate-out-delay");
					var duration_time=p.data("animate-duration");
					var out_duration_time=p.data("animate-out-duration");
					var d = p.data("animate-type");
					var o = p.data("animate-out-type");												
					var animate_offset = p.data("animate-offset");
					
					p.css("opacity","1");
					c = p.find('.animated-columns');
					p.waypoint(function(direction) {
						if( direction === 'down'){
							if(!c.hasClass("animation-done")){
								c.addClass("animation-done").removeClass("animation-out-done").velocity(d,{ delay: delay_time,duration: duration_time,display:'auto',stagger: animation_stagger});
							}
						}else if (direction === 'up' && o!='' && o!=undefined && !c.hasClass("animation-out-done")) {
							c.addClass("animation-out-done").removeClass("animation-done").velocity(o,{ delay: out_delay_time,duration: out_duration_time,display:'auto',stagger: animation_stagger});
						}
					}, { offset: animate_offset } );
					if(c){
						$('head').append("<style type='text/css'>."+id+" .animated-columns.animation-done{opacity:1;}</style>")
					}
					
					}else if(p.data("animate-columns")=="columns"){
					
					var b = $('.animated-columns',this);
					var delay_time=p.data("animate-delay");
					var out_delay_time=p.data("animate-out-delay");
					var d = p.data("animate-type");
					var o = p.data("animate-out-type");	
					var animate_offset = p.data("animate-offset");
					var duration_time=p.data("animate-duration");
					var out_duration_time=p.data("animate-out-duration");
					p.css("opacity","1");
					c = p.find('.animated-columns');
					c.each(function() {
						var bc=$(this);
						bc.waypoint(function(direction) {
							if( direction === 'down'){
								if(!bc.hasClass("animation-done")){
									bc.addClass("animation-done").removeClass("animation-out-done").velocity(d,{ delay: delay_time,duration: duration_time,drag:true,display:'auto'});
								}
							}else if (direction === 'up' && o!='' && o!=undefined && !bc.hasClass("animation-out-done")) {
								bc.addClass("animation-out-done").removeClass("animation-done").velocity(o,{ delay: out_delay_time,duration: out_duration_time,display:'auto'});
							}
						}, { offset: animate_offset } );
					});
					if(c){
						$('head').append("<style type='text/css'>."+id+" .animated-columns.animation-done{opacity:1;}</style>")
					}
					}else{
					var b = $(this);
					var delay_time=b.data("animate-delay");
					var out_delay_time=b.data("animate-out-delay");
					var duration_time=b.data("animate-duration");
					var out_duration_time=p.data("animate-out-duration");
					d = b.data("animate-type"),
					o = b.data("animate-out-type"),
					animate_offset = b.data("animate-offset"),
					b.waypoint(function(direction ) {
						if( direction === 'down'){
							if(!b.hasClass("animation-done")){
								b.addClass("animation-done").removeClass("animation-out-done").velocity(d, {delay: delay_time,duration: duration_time,display:'auto'});
							}
						}else if (direction === 'up' && o!='' && o!=undefined && !b.hasClass("animation-out-done")) {
							if(!b.hasClass("animation-out-done")){
								b.addClass("animation-out-done").removeClass("animation-done").velocity(o,{ delay: out_delay_time,duration: out_duration_time,display:'auto' });
							}
						}
					}, { offset: animate_offset } );
				}
			})
		},
		e = function() {
			$(".call-on-waypoint").each(function() {
				var c = $(this);
				c.waypoint(function() {
					c.trigger("on-waypoin")
					}, {
					triggerOnce: !0,
					offset: "bottom-in-view"
				})
			})
		};
		$(document).ready(e),$(window).on('load',e),
		$(document.body).on('post-load', function() {
			e()
		}),
		$(document).ready(d),$(window).on('load',d),
		$(document.body).on('post-load', function() {
			d()
		});
		$(document).ready(function(){
		e(); d();
		});
	});
} ( jQuery ) );
/*--- on load animation ----*/
/*accordion/tabs connection*/
function accordion_tabs_connection(tab_index,connection){
	var $=jQuery;
	if(connection!='' && $("."+connection).length==1){
		var current=$('.'+connection+' > .post-inner-loop').slick('slickCurrentSlide');
		if(current!=(tab_index-1)){
			$('.'+connection+' > .post-inner-loop').slick('slickGoTo', tab_index-1);
		}
	}
}
/*accordion/tabs connection*/
/*------------- magic scroll js ---*/
( function ( $ ) {	
	'use strict';
	$(document).ready(function(){
		pt_plus_animateParalax();
	});
} ( jQuery ) );
		
function pt_plus_animateParalax() {
	var $=jQuery;
	if($('body').find('.magic-scroll').length>0){
		var controller = new ScrollMagic.Controller();
		$('.magic-scroll').each(function(index, elem){
			var tween = 'tween-'+index;
			tween = new TimelineMax();
			var lengthBox = $(elem).find('.parallax-scroll').length;
			var scroll_offset=$(elem).find('.parallax-scroll').data("scroll_offset");
			var scroll_duration=$(elem).find('.parallax-scroll').data("scroll_duration");
			for(var i=0; i < lengthBox; i++){
				var speed = 0.5;
				var scroll_type=$(elem).find('.parallax-scroll').data("scroll_type");
				var scroll_x_from=$(elem).find('.parallax-scroll').data("scroll_x_from");
				var scroll_x_to=$(elem).find('.parallax-scroll').data("scroll_x_to");				
				var scroll_y_from=$(elem).find('.parallax-scroll').data("scroll_y_from");
				var scroll_y_to=$(elem).find('.parallax-scroll').data("scroll_y_to");
				var scroll_opacity_from=$(elem).find('.parallax-scroll').data("scroll_opacity_from");
				var scroll_opacity_to=$(elem).find('.parallax-scroll').data("scroll_opacity_to");
				var scroll_rotate_from=$(elem).find('.parallax-scroll').data("scroll_rotate_from");
				var scroll_rotate_to=$(elem).find('.parallax-scroll').data("scroll_rotate_to");
				var scroll_scale_from=$(elem).find('.parallax-scroll').data("scroll_scale_from");
				var scroll_scale_to=$(elem).find('.parallax-scroll').data("scroll_scale_to");
				
				var j1 = 0.2*(i+1);
				var k1 = 0.5*i;
				if(scroll_type=='position'){
					if(i==0) {
						
						tween.fromTo($(elem).find('.parallax-scroll:eq('+i+')'), 1, {scale:scroll_scale_from,rotation:scroll_rotate_from,opacity:scroll_opacity_from,x:-(scroll_x_from*speed),y:-(scroll_y_from*speed), ease: Linear.easeNone},{scale:scroll_scale_to,rotation:scroll_rotate_to,opacity:scroll_opacity_to,x:-(scroll_x_to*speed),y:-(scroll_y_to*speed), ease: Linear.easeNone})
					}else {
						tween.to($(elem).find('.parallax-scroll:eq('+i+')'), 1, {scale:scroll_scale_to,y:-(scroll_y_to*speed), ease: Linear.easeNone}, '-=1')
					}
				}
			}			
			new ScrollMagic.Scene({triggerElement: elem, duration: scroll_duration, triggerHook:.5,offset: scroll_offset})
				.setTween(tween)
				.addTo(controller);
		})
	}
}
/*------------- magic scroll js ---*/

/* list carosel slick*/
function theplus_carousel_list(data_widget=''){
	var $=jQuery;
	$('.list-carousel-slick').each(function() {
			var $self=$(this);			
			var $uid=$self.data("id");
			var slider_direction=$self.data("slider_direction");
			var slide_speed=$self.data("slide_speed");
			var default_active_slide=$self.data("default_active_slide");
			var slider_desktop_column=$self.data("slider_desktop_column");
			var steps_slide=$self.data("steps_slide");
			var slide_fade_inout=$self.data("slide_fade_inout");
			var slider_padding=$self.data("slider_padding");
			
			var slider_draggable=$self.data("slider_draggable");
			var slider_infinite=$self.data("slider_infinite");
			var slider_pause_hover=$self.data("slider_pause_hover");
			var slider_adaptive_height=$self.data("slider_adaptive_height");
			var slider_autoplay=$self.data("slider_autoplay");
			var autoplay_speed=$self.data("autoplay_speed");
			var slider_rows=$self.data("slider_rows");
			
			var slider_center_mode=$self.data("slider_center_mode");
			var center_padding=$self.data("center_padding");
			var scale_center_slide=$self.data("scale_center_slide");
			var scale_normal_slide=$self.data("scale_normal_slide");
			var opacity_normal_slide=$self.data("opacity_normal_slide");
			
			var slider_dots=$self.data("slider_dots");
			var slider_dots_style=$self.data("slider_dots_style");
			
			var slider_arrows=$self.data("slider_arrows");
			var slider_arrows_style=$self.data("slider_arrows_style");
			var arrows_position=$self.data("arrows_position");
			
			var slider_responsive_tablet=$self.data("slider_responsive_tablet");
			var slider_tablet_column=$self.data("slider_tablet_column");
			var tablet_steps_slide=$self.data("tablet_steps_slide");
			var tablet_center_mode=$self.data("tablet_center_mode");
			var tablet_center_padding=$self.data("tablet_center_padding");
			var tablet_slider_draggable=$self.data("tablet_slider_draggable");
			var tablet_slider_infinite=$self.data("tablet_slider_infinite");
			var tablet_slider_autoplay=$self.data("tablet_slider_autoplay");
			var tablet_autoplay_speed=$self.data("tablet_autoplay_speed");
			var tablet_slider_dots=$self.data("tablet_slider_dots");
			var tablet_slider_arrows=$self.data("tablet_slider_arrows");
			var tablet_slider_rows=$self.data("tablet_slider_rows");
			var tablet_center_mode=$self.data("tablet_center_mode");
			var tablet_center_padding=$self.data("tablet_center_padding");
			
			
			var slider_responsive_mobile=$self.data("slider_responsive_mobile");
			var mobile_slider_draggable=$self.data("mobile_slider_draggable");
			var mobile_slider_infinite=$self.data("mobile_slider_infinite");
			var mobile_slider_autoplay=$self.data("mobile_slider_autoplay");
			var mobile_autoplay_speed=$self.data("mobile_autoplay_speed");
			
			
			var slider_mobile_column=$self.data("slider_mobile_column");
			var mobile_steps_slide=$self.data("mobile_steps_slide");
			var mobile_center_mode=$self.data("mobile_center_mode");
			var mobile_center_padding=$self.data("mobile_center_padding");
			var mobile_slider_dots=$self.data("mobile_slider_dots");
			var mobile_slider_arrows=$self.data("mobile_slider_arrows");
			var mobile_slider_rows=$self.data("mobile_slider_rows");
			var mobile_center_mode=$self.data("mobile_center_mode");
			var mobile_center_padding=$self.data("mobile_center_padding");
			
			var testimonial_style=$self.data("testimonial-style");
			var slide_mouse_scroll=$self.data("slide_mouse_scroll");
			
			if(steps_slide=='1'){
				steps_slide=='1';
			}else{
				steps_slide=slider_desktop_column;
			}
			if(tablet_steps_slide=='1'){
				tablet_steps_slide=='1';
			}else{
				tablet_steps_slide=slider_tablet_column;
			}
			if(slider_responsive_tablet!='yes'){
				tablet_slider_draggable=slider_draggable;
				tablet_slider_infinite=slider_infinite;
				tablet_slider_autoplay=slider_autoplay;
				tablet_autoplay_speed=autoplay_speed;
				tablet_slider_dots=slider_dots;
				tablet_slider_arrows=slider_arrows;
				tablet_slider_rows=slider_rows;
				tablet_center_mode=slider_center_mode;
				tablet_center_padding=center_padding;
			}
			if(slider_responsive_mobile!='yes'){
				mobile_slider_draggable=slider_draggable;
				mobile_slider_infinite=slider_infinite;
				mobile_slider_autoplay=slider_autoplay;
				mobile_autoplay_speed=autoplay_speed;
				mobile_slider_dots=slider_dots;
				mobile_slider_arrows=slider_arrows;
				mobile_slider_rows=slider_rows;
				mobile_center_mode=slider_center_mode;
				mobile_center_padding=center_padding;
			}
			
			if(mobile_steps_slide=='1'){
				mobile_steps_slide=='1';
			}else{
				mobile_steps_slide=slider_mobile_column;
			}
			
			if(slider_arrows_style=='style-1'){
				var prev_arrow='<button type="button" class="slick-nav slick-prev '+slider_arrows_style+'"></button>';
				var next_arrow='<button type="button" class="slick-nav slick-next '+slider_arrows_style+'"></button>';
			}
			
			if(slider_arrows_style=='style-2'){
				var prev_arrow='<button type="button" class="slick-nav slick-prev '+slider_arrows_style+'"><span class="icon-wrap"></span></button>';
				var next_arrow='<button type="button" class="slick-nav slick-next '+slider_arrows_style+'"><span class="icon-wrap"></span></button>';
			}
			if(slider_arrows_style=='style-3' || slider_arrows_style=='style-4' ){
				var prev_arrow='<button type="button" class="slick-nav slick-prev '+slider_arrows_style+' '+arrows_position+'"></button>';
				var next_arrow='<button type="button" class="slick-nav slick-next '+slider_arrows_style+' '+arrows_position+'"></button>';
			}
			if(slider_arrows_style=='style-5'){
				var prev_arrow='<button type="button" class="slick-nav slick-prev '+slider_arrows_style+'"><span class="icon-wrap"></span></button>';
				var next_arrow='<button type="button" class="slick-nav slick-next '+slider_arrows_style+'"><span class="icon-wrap"></span></button>';
			}
			if(slider_arrows_style=='style-6'){
				var prev_arrow='<button type="button" class="slick-nav slick-prev '+slider_arrows_style+'"><span class="icon-wrap"><i class="fas fa-long-arrow-alt-left" aria-hidden="true"></i></span></button>';
				var next_arrow='<button type="button" class="slick-nav slick-next '+slider_arrows_style+'"><span class="icon-wrap"><i class="fas fa-long-arrow-alt-right" aria-hidden="true"></i></span></button>';
			}
			
			if(default_active_slide==undefined){
				default_active_slide=0;
			}
			var args = {dots: slider_dots,
					vertical: slider_direction,
					fade:slide_fade_inout,
					arrows: slider_arrows,
					infinite: slider_infinite,
					speed: slide_speed,
					initialSlide: default_active_slide,
					adaptiveHeight: slider_adaptive_height,
					autoplay: slider_autoplay,
					autoplaySpeed: autoplay_speed,
					pauseOnHover: slider_pause_hover,
					centerMode: slider_center_mode,
					centerPadding: center_padding+'px',
					prevArrow: prev_arrow,
					nextArrow: next_arrow,
					slidesToShow: slider_desktop_column,
					slidesToScroll: steps_slide,
					draggable:slider_draggable,
					dotsClass:slider_dots_style,
					rows : slider_rows,
					responsive: [
						{
							breakpoint: 800,
							settings: {
								dots: tablet_slider_dots,
								arrows: tablet_slider_arrows,
								infinite: tablet_slider_infinite,
								autoplay: tablet_slider_autoplay,
								autoplaySpeed: tablet_autoplay_speed,
								draggable:tablet_slider_draggable,
								rows : tablet_slider_rows,
								slidesToShow: slider_tablet_column,
								slidesToScroll: tablet_steps_slide,
								centerMode: tablet_center_mode,
								centerPadding: tablet_center_padding+'px',
							}
						},
						{
							breakpoint: 600,
							settings: {
								dots: mobile_slider_dots,
								arrows: mobile_slider_arrows,
								infinite: mobile_slider_infinite,
								autoplay: mobile_slider_autoplay,
								autoplaySpeed: mobile_autoplay_speed,
								draggable:mobile_slider_draggable,
								rows : mobile_slider_rows,
								slidesToShow: slider_mobile_column,
								slidesToScroll: mobile_steps_slide,
								centerMode: mobile_center_mode,
								centerPadding: mobile_center_padding+'px',
							}
						}
					]
			}
			if(!$(this).hasClass("done-carousel") && !$self.hasClass('theplus-insta-grid')){
				$('.'+$uid+' > .post-inner-loop').slick(args);
				setTimeout(function(){
					$(".slick-dots.style-2 li").each(function(){
						if($(this).find("svg").length==0){
							$(this).append('<svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 16 16" preserveAspectRatio="none"><circle cx="8" cy="8" r="6.215"></circle></svg>');
						}
					});
				}, 1000);
				$(this).addClass("done-carousel");
				if(slide_mouse_scroll==true && slide_mouse_scroll!=undefined){
					
					$('.'+$uid+' > .post-inner-loop').mousewheel(function(e) {
						e.preventDefault();
						if (e.deltaY < 0) {
							$('.'+$uid+' > .post-inner-loop').slick("slickNext");
							} else {
							$('.'+$uid+' > .post-inner-loop').slick("slickPrev");
						}
					});
				}
			}else if(!$(this).hasClass("done-carousel") && $self.hasClass('theplus-insta-grid') && data_widget!='' && data_widget=='instagram'){
				if($('.'+$uid+' > .post-inner-loop').find('.theplus-insta-feed').length > 0){
					$('.'+$uid+' > .post-inner-loop').slick(args);
					$(this).addClass("done-carousel");				
					setTimeout(function(){
						$(".slick-dots.style-2 li").each(function(){
							if($(this).find("svg").length==0){
								$(this).append('<svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 16 16" preserveAspectRatio="none"><circle cx="8" cy="8" r="6.215"></circle></svg>');
							}
						});
					}, 1000);
				}
			}
			
	});
}
/* list carosel slick*/
/*----cascading image loop slide -----*/
(function($) {
    'use strict';
	$(document).ready(function() {
		cascading_slide_show_image();
		cascading_overflow();
	});
	$(window).on("load resize",function() {
		cascading_overflow();
	});
})(jQuery);
function cascading_slide_show_image(){
	(function($) {
    'use strict';
	$(document).ready(function() {
	$(".slide_show_image").length && $(".slide_show_image").each(function() {
		var t = $(this),uid1=t.data("uid");
		var uid=$('.'+uid1);
		$('.'+uid1+'.slide_show_image .cascading-image:last').addClass('active');
		var  i = t.find(".cascading-image"),opt=t.data("play");
		$('.'+uid1+" .cascading-image").each(function() {
			var o = $(this);
			if(opt=='onclick'){
				
				}else{
				var time=$('.'+uid1).data('interval_time');
				setInterval(function () {
					var current = $('.'+uid1+'.slide_show_image .cascading-image.active').removeClass('active'),
					o = current.next().length ? current.next() : current.siblings().filter(':first');
					o.addClass('active');
					if (!i.last().is(o))
					return o.addClass("out").animate({
						opacity: 0.7,
						}, 200, function() {
						o.detach(),
						o.insertAfter(i.last()).animate({
							opacity: 1,
							}, 500, function() {
							o.removeClass("out")
						}),
						i = t.find(".cascading-image"),
						i.removeClass('active'),
						o.addClass("active");
					}),
					!1
				}, time);
			
			}
		})
	});
	});
	})(jQuery);
}
function cascading_overflow(){
	var $=jQuery;
	$(".cascading-block").length && $(".cascading-block").each(function() {
		var width = window.innerWidth;
		var cadcading_overflow=$(this);
		var cadcading_overflow_desktop=$(this).data('overflow-desktop');
		var cadcading_overflow_tablet=$(this).data('overflow-tablet');
		var cadcading_overflow_mobile=$(this).data('overflow-mobile');
		if(cadcading_overflow_desktop=='yes'  && width > 991){
			cadcading_overflow.closest("section.elementor-element").css("overflow","hidden");	
		}else if(cadcading_overflow_tablet=='yes' && (width <= 991 && width > 600)){
			cadcading_overflow.closest("section.elementor-element").css("overflow","hidden");	
		}else if(cadcading_overflow_mobile=='yes' && width <= 600){
			cadcading_overflow.closest("section.elementor-element").css("overflow","hidden");	
		}else{
			cadcading_overflow.closest("section.elementor-element").css("overflow","visible");
		}
	});
}
/*----cascading image loop slide -----*/

/*----mordern parallax image----*/
(function($) {
'use strict';
$(window).on("resize",function() {

$(".mordern-image-effect").each(function() {
var win_width = $(window).width();

var tab_w=$(this).attr('data-tablet-width');
var mob_w=$(this).attr('data-mobile-width');
if(win_width<768){
$(this).css('width',tab_w);
}
if(win_width<480){
$(this).css('width',mob_w);
}
if(win_width>=768){
$(this).css('width','auto');
}
});

});
})(jQuery);
/*-----mordern parallax image---*/
/*--- animated background color ---*/
;(function($, window, document, undefined) {
	$.fn.animatedBG = function(options){
		var defaults = {
				colorSet: ['#ef008c', '#00be59', '#654b9e', '#ff5432', '#00d8e6'],
				speed: 3000
			},
			settings = $.extend({}, defaults, options);

		return this.each(function(){
			var $this = $(this);

			$this.each(function(){
				var $el = $(this),
					colors = settings.colorSet;
				
				function shiftColor() {
					var color = colors.shift();
					colors.push(color);
					return color;
				}

				// initial color
				var initColor = shiftColor();
				$el.css('backgroundColor', initColor);
				setInterval(function(){
					var color = shiftColor();
					$el.animate({backgroundColor: color}, 3000);
				}, settings.speed);
			});
		});
	};
	$(function(){
		$(document).ready(function() {
		$(".row-animated-bg").each(function() {
			var data_id= $(this).data('id');
			var data_time=$(this).data('bg-time');
			var colors =$(this).data('bg');
			$('.'+data_id).animatedBG({
				colorSet: colors,
				speed: data_time
			});
		});
		});
	});
}(jQuery, window, document));
/*--- animated background color ---*/
/*---bg imageclip---*/
(function($) {
'use strict';
$(document).ready(function() {
$(".pt-plus-row-imageclip").each(function() {
var data_id= $(this).data('id');
var border_width= $(this).data('border-width');
var border_style= $(this).data('border-style');
var border_color= $(this).data('border-color');
var box_shadow=$(this).data('box-shadow');
$('head').append('<style >.'+data_id+' .segmenter__shadow{border-width:'+border_width+';border-style:'+border_style+';border-color:'+border_color+';box-shadow:'+box_shadow+';}</style>');

});

});
})(jQuery);
/*---bg imageclip---*/
/*text Heading Animation*/
(function($) {
    'use strict';
	$(document).ready(function() {
		plus_heading_animation();
	});
})(jQuery);
function plus_heading_animation(){
	/*------ heading animation--------*/
		jQuery(document).ready(function($){
			"use strict";
			//set animation timing
			var animationDelay = 2500,
			//loading bar effect
			barAnimationDelay = 3800,
			barWaiting = barAnimationDelay - 3000, 
			//letters effect
			lettersDelay = 50,
			//type effect
			typeLettersDelay = 150,
			selectionDuration = 500,
			typeAnimationDelay = selectionDuration + 800,
			//clip effect 
			revealDuration = 600,
			revealAnimationDelay = 1500;
			
			pt_plus_initHeadline();
			
			
			function pt_plus_initHeadline() {
				//insert <i> element for each letter of a changing word
				singleLetters(jQuery('.pt-plus-cd-headline.letters').find('b'));
				//initialise headline animation
				animateHeadline(jQuery('.pt-plus-cd-headline'));
			}
			
			function singleLetters($words) {
				$words.each(function(){
					var i;
					var word = jQuery(this),
					letters = word.text().split(''),
					selected = word.hasClass('is-visible');
					for (i in letters) {
						if(word.parents('.rotate-2').length > 0) letters[i] = '<em>' + letters[i] + '</em>';
						letters[i] = (selected) ? '<i class="in">' + letters[i] + '</i>': '<i>' + letters[i] + '</i>';
					}
					var newLetters = letters.join('');
					word.html(newLetters).css('opacity', 1);
				});
			}
			
			function animateHeadline($headlines) {
				var duration = animationDelay;
				$headlines.each(function(){
					var headline = jQuery(this);
					
					if(headline.hasClass('loading-bar')) {
						duration = barAnimationDelay;
						setTimeout(function(){ headline.find('.cd-words-wrapper').addClass('is-loading') }, barWaiting);
						} else if (headline.hasClass('clip')){
						var spanWrapper = headline.find('.cd-words-wrapper'),
						newWidth = spanWrapper.width() + 10
						spanWrapper.css('width', newWidth);
						} else if (!headline.hasClass('type') ) {
						//assign to .cd-words-wrapper the width of its longest word
						var words = headline.find('.cd-words-wrapper b'),
						width = 0;
						words.each(function(){
							var wordWidth = jQuery(this).width();
							if (wordWidth > width) width = wordWidth;
						});
						headline.find('.cd-words-wrapper').css('width', width+12);
					};
					
					//trigger animation
					setTimeout(function(){ hideWord( headline.find('.is-visible').eq(0) ) }, duration);
				});
			}
			
			function hideWord($word) {
				var nextWord = takeNext($word);
				
				if($word.parents('.pt-plus-cd-headline').hasClass('type')) {
					var parentSpan = $word.parent('.cd-words-wrapper');
					parentSpan.addClass('selected').removeClass('waiting');	
					setTimeout(function(){ 
						parentSpan.removeClass('selected'); 
						$word.removeClass('is-visible').addClass('is-hidden').children('i').removeClass('in').addClass('out');
					}, selectionDuration);
					setTimeout(function(){ showWord(nextWord, typeLettersDelay) }, typeAnimationDelay);
					
					} else if($word.parents('.pt-plus-cd-headline').hasClass('letters')) {
					var bool = ($word.children('i').length >= nextWord.children('i').length) ? true : false;
					hideLetter($word.find('i').eq(0), $word, bool, lettersDelay);
					showLetter(nextWord.find('i').eq(0), nextWord, bool, lettersDelay);
					
					}  else if($word.parents('.pt-plus-cd-headline').hasClass('clip')) {
					$word.parents('.cd-words-wrapper').animate({ width : '2px' }, revealDuration, function(){
						switchWord($word, nextWord);
						showWord(nextWord);
					});
					
					} else if ($word.parents('.pt-plus-cd-headline').hasClass('loading-bar')){
					$word.parents('.cd-words-wrapper').removeClass('is-loading');
					switchWord($word, nextWord);
					setTimeout(function(){ hideWord(nextWord) }, barAnimationDelay);
					setTimeout(function(){ $word.parents('.cd-words-wrapper').addClass('is-loading') }, barWaiting);
					
					} else {
					switchWord($word, nextWord);
					setTimeout(function(){ hideWord(nextWord) }, animationDelay);
				}
			}
			
			function showWord($word, $duration) {
				if($word.parents('.pt-plus-cd-headline').hasClass('type')) {
					showLetter($word.find('i').eq(0), $word, false, $duration);
					$word.addClass('is-visible').removeClass('is-hidden');
					
					}  else if($word.parents('.pt-plus-cd-headline').hasClass('clip')) {
					$word.parents('.cd-words-wrapper').animate({ 'width' : $word.width() + 10 }, revealDuration, function(){ 
						setTimeout(function(){ hideWord($word) }, revealAnimationDelay); 
					});
				}
			}
			
			function hideLetter($letter, $word, $bool, $duration) {
				$letter.removeClass('in').addClass('out');
				
				if(!$letter.is(':last-child')) {
					setTimeout(function(){ hideLetter($letter.next(), $word, $bool, $duration); }, $duration);  
					} else if($bool) { 
					setTimeout(function(){ hideWord(takeNext($word)) }, animationDelay);
				}
				
				if($letter.is(':last-child') && jQuery('html').hasClass('no-csstransitions')) {
					var nextWord = takeNext($word);
					switchWord($word, nextWord);
				} 
			}
			
			function showLetter($letter, $word, $bool, $duration) {
				$letter.addClass('in').removeClass('out');
				
				if(!$letter.is(':last-child')) { 
					setTimeout(function(){ showLetter($letter.next(), $word, $bool, $duration); }, $duration); 
					} else { 
					if($word.parents('.pt-plus-cd-headline').hasClass('type')) { setTimeout(function(){ $word.parents('.cd-words-wrapper').addClass('waiting'); }, 200);}
					if(!$bool) { setTimeout(function(){ hideWord($word) }, animationDelay) }
				}
			}
			
			function takeNext($word) {
				return (!$word.is(':last-child')) ? $word.next() : $word.parent().children().eq(0);
			}
			
			function takePrev($word) {
				return (!$word.is(':first-child')) ? $word.prev() : $word.parent().children().last();
			}
			
			function switchWord($oldWord, $newWord) {
				$oldWord.removeClass('is-visible').addClass('is-hidden');
				$newWord.removeClass('is-hidden').addClass('is-visible');
			}
		});
		/*----header animation element--------*/
}
/*text Heading Animation*/
/*video bg responsive*/
(function($) {
    'use strict';
	var d_i=0,t_i=0,m_i=0;
	$(window).on('load resize',function() {
		var inner_width=window.innerWidth;
		if($('body').find(".plus-video-poster").length>0){
			$('.plus-video-poster').each(function(){
				var desktop_poster=$(this).data("desktop-poster"),tablet_poster=$(this).data("tablet-poster"),mobile_poster=$(this).data("mobile-poster");				
				if(tablet_poster==undefined || tablet_poster==''){
					tablet_poster=desktop_poster;
				}
				if(mobile_poster==undefined || mobile_poster==''){
					mobile_poster=tablet_poster;
				}
				if(inner_width<=991 && inner_width>=601 && tablet_poster!=undefined && t_i==0){
					if(!$(this).hasClass("self-hosted-video-bg")){
						$(this).css('background-image', 'url(' + tablet_poster + ')');
					}else{
						$(this).css('background-image', 'url(' + tablet_poster + ')');
						$('video',this).attr('poster',tablet_poster);
					}
					d_i=0;t_i++;m_i=0;
				}
				if(inner_width<=600 && mobile_poster!=undefined && m_i==0){
					if(!$(this).hasClass("self-hosted-video-bg")){
						$(this).css('background-image', 'url(' + mobile_poster + ')');
					}else{
						$(this).css('background-image', 'url(' + mobile_poster + ')');
						$('video',this).attr('poster',mobile_poster);
					}
					d_i=0;t_i=0;m_i++;
				}
				if(inner_width>=992 && desktop_poster!=undefined && d_i==0){
					if(!$(this).hasClass("self-hosted-video-bg")){
						$(this).css('background-image', 'url(' + desktop_poster + ')');
					}else{
						$(this).css('background-image', 'url(' + desktop_poster + ')');
						$('video',this).attr('poster',desktop_poster);
					}
					d_i++;t_i=0;m_i=0;
				}
			});
		}
	});	
})(jQuery);
/*video bg responsive*/

/*----------animated image hover tilt option------------------*/
( function ( $ ) {	
	'use strict';
	$(document).ready(function () {
		if($(".hover-tilt").length){
			$(".hover-tilt").hover3d({
				selector: ".blog-list-style-content,> .cascading-inner-loop,> .call-to-action-img,> .blog-hover-inner-tilt,> .logo-image-wrap",
				shine: !1,
				perspective: 2e3,
				invert: !0,
				sensitivity: 35,
			});
		}
	});
} ( jQuery ) );	
/*----------animated image hover tilt option------------------*/

/*---------creative simple image super parallax----------------------*/
function plus_bgimage_scrollparallax(){
	var $=jQuery;
	if($('body').find('.creative-simple-img-parallax').length>0){
		var controller = new ScrollMagic.Controller();
		$('.creative-simple-img-parallax').each(function(index, elem){
			var data_parallax =$(this).data("scroll-parallax");
			data_parallax = -(data_parallax);
			var parallax_image=$('.simple-parallax-img',this);
			var tween = 'tween-'+index;
			tween = new TimelineMax();
			new ScrollMagic.Scene({
                triggerElement: elem,
				duration: '150%'
			}).setTween(tween.from(parallax_image, 1, {x:data_parallax,ease: Linear.easeNone})).addTo(controller);;
		});
	}
}
( function ( $ ) {
	'use strict';
	$(document).ready(function(){
		plus_bgimage_scrollparallax();
	});
} ( jQuery ) );
/*---------creative simple image super parallax----------------------*/
/*--------- animated svg js -----------*/
( function ( $ ) {	
	'use strict';
	$.fn.pt_plus_animated_svg = function() {
		return this.each(function() {
			var $self = $(this);
			var data_id=$self.data("id");
			var data_duration=$self.data("duration");
			var data_type=$self.data("type");
			var data_stroke=$self.data("stroke");
			var data_fill_color=$self.data("fill_color");
			new Vivus(data_id, {type: data_type, duration: data_duration,forceRender:false,start: 'inViewport',onReady: function (myVivus) {
				var c=myVivus.el.childNodes;
				var show_id=document.getElementById(data_id);
				show_id.style.opacity = "1";
				if(data_stroke!=''){
					for (var i = 0; i < c.length; i++) {
						$(c[i]).attr("fill", data_fill_color);
						$(c[i]).attr("stroke",data_stroke);
						var child=c[i];
						var pchildern=child.children;
						if(pchildern != undefined){
							for(var j=0; j < pchildern.length; j++){
								$(pchildern[j]).attr("fill", data_fill_color);
								$(pchildern[j]).attr("stroke",data_stroke);
							}
						}
					}
				}
			} });
		});
	};	
	
	$(window).on("load",function() {
		setTimeout(function(){
			$('.pt_plus_row_bg_animated_svg').pt_plus_animated_svg();
			$('.pt_plus_animated_svg,.ts-hover-draw-svg').pt_plus_animated_svg();
			$('body').find('.pt_plus_row_bg_animated_svg').attr('style', 'stroke:black');
		}, 100);
	});
	$(document).ready(function() {
		$('.plus-hover-draw-svg .svg_inner_block').on("mouseenter",function() {
			var $self;
			$self = $(this).parent();
			var data_id=$self.data("id");
			var data_duration=$self.data("duration");
			var data_type=$self.data("type");
			new Vivus(data_id, {type: data_type, duration: data_duration,start: 'inViewport'}).reset().play();
		}).on("mouseleave", function() {
		});
	});
} ( jQuery ) );
/*--------- animated svg js -----------*/
/*- contact form-----------*/
( function ( $ ) {	
	'use strict';
	$(document).ready(function() {
		plus_cf7_form();
		/*caldera form*/
		if($(".caldera-grid .checkbox label").length > 0 || $(".caldera-grid .radio label").length > 0){
			$(".caldera-grid .checkbox label, .caldera-grid .radio label").each(function(){
				var $this=$(this);
				var checkbox_id=$(this).find('input[type="checkbox"]').attr('id');
				var radio_id=$(this).find('input[type="radio"]').attr('id');
				if(checkbox_id!=undefined){			
					$(this).append('<span class="caldera_checkbox_label" for="'+checkbox_id+'"></span>');
				}
				if(radio_id!=undefined){
					$(this).append('<span class="caldera_radio_label" for="'+radio_id+'"></span>');
				}
			});
		}
		/*caldera form*/
		/*gravity form*/
		if( $(".pt_plus_gravity_form .gform_wrapper .ginput_container_checkbox").length > 0 ){
			$(".pt_plus_gravity_form .gform_wrapper .ginput_container_checkbox label").each(function(){
				var $this=$(this);
				var g_checkbox_id=$this.find('input[type="checkbox"]');
				if(g_checkbox_id!=undefined){			
					$this.append('<span class="gravity_checkbox_label"></span>');
				}
			});
		}

		if( $(".pt_plus_gravity_form .gform_wrapper .ginput_container_radio").length > 0 ){
			$(".pt_plus_gravity_form .gform_wrapper .ginput_container_radio label").each(function(){
				var $this = $(this);
				var g_radio_id = $this.find('input[type="radio"]');				
				if(g_radio_id != undefined){			
					$this.append('<span class="gravity_radio_label"></span>');
				}
			});
		}
		/*gravity form*/
	});
} ( jQuery ) );
function plus_cf7_form(){
	var $=jQuery;
		$('.theplus-contact-form').each(function(){
			var radio_checkbox='plus-checkbox';
			var i=0;
			if(!$(this).hasClass("tp-form-loaded")){
			$(".wpcf7-form-control.wpcf7-radio .wpcf7-list-item",this).each(function(){
				var text_val=$(this).find('.wpcf7-list-item-label').text();
				$(this).find('.wpcf7-list-item-label').remove();
				var label_Tags=$('input[type="radio"]',this);
				if ( label_Tags.parent().is( 'label' )) {
					label_Tags.unwrap();
				}
				var radio_name=$(this).find('input[type="radio"]').attr('name');
				$(this).append('<label class="input__radio_btn" for="'+radio_name+i+'">'+text_val+'<div class="toggle-button__icon"></div></label>');
				$(this).find('input[type="radio"]').attr('id',radio_name+i);
				
				$(this).find('input[type="radio"]').addClass("input-radio-check");
				$(this).parents(".wpcf7-form-control-wrap").addClass(radio_checkbox);
				i++;
			});
			var i=0;
			$(".wpcf7-form-control.wpcf7-checkbox .wpcf7-list-item",this).each(function(){
				var text_val=$(this).find('.wpcf7-list-item-label').text();
				$(this).find('.wpcf7-list-item-label').remove();
				var label_Tags=$('input[type="checkbox"]',this);
				if ( label_Tags.parent().is( 'label' )) {
					label_Tags.unwrap();
				}
				$(this).append('<label class="input__checkbox_btn" for="'+radio_checkbox+i+'">'+text_val+'<div class="toggle-button__icon"></div></label>');
				$(this).find('input[type="checkbox"]').attr('id',radio_checkbox+i);
				
				$(this).find('input[type="checkbox"]').addClass("input-checkbox-check");
				$(this).parents(".wpcf7-form-control-wrap").addClass(radio_checkbox);
				i++;
			});
			$(".wpcf7-form-control-wrap input[type='file']",this).each(function(){
				var file_name=$(this).attr('name');
				$(this).attr('id',file_name+i);
				$(this).attr('data-multiple-caption',"{count} files selected");
				$(this).parents(".wpcf7-form-control-wrap").append('<label class="input__file_btn" for="'+file_name+i+'"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="17" viewBox="0 0 20 17"><path d="M10 0l-5.2 4.9h3.3v5.1h3.8v-5.1h3.3l-5.2-4.9zm9.3 11.5l-3.2-2.1h-2l3.4 2.6h-3.5c-.1 0-.2.1-.2.1l-.8 2.3h-6l-.8-2.2c-.1-.1-.1-.2-.2-.2h-3.6l3.4-2.6h-2l-3.2 2.1c-.4.3-.7 1-.6 1.5l.6 3.1c.1.5.7.9 1.2.9h16.3c.6 0 1.1-.4 1.3-.9l.6-3.1c.1-.5-.2-1.2-.7-1.5z"></path></svg><span>Choose a file…</span></label>');
				$(this).parents(".wpcf7-form-control-wrap").addClass("cf7-style-file");
				i++;
			});
				$(this).addClass("tp-form-loaded");
			}
		});	
}
/*- contact form-----------*/

/*--Content hover Effects --*/
( function ( $ ) {
	'use strict';
	$(document).ready(function () {
		if($('.content_hover_effect').length > 0){
			$('.content_hover_effect').each(function () {
				var $this=$(this);
				var hover_uniqid = $this.data('hover_uniqid');
				var hover_shadow = $this.data('hover_shadow');
				var content_hover_effects= $this.data('content_hover_effects');
				if(content_hover_effects=='float_shadow'){
					$('head').append('<style >.'+hover_uniqid+'.content_hover_float_shadow:before{background: -webkit-radial-gradient(center, ellipse, '+hover_shadow+' 0%, rgba(60, 60, 60, 0) 70%);background: radial-gradient(ellipse at center, '+hover_shadow+' 0%, rgba(60, 60, 60, 0) 70%);}</style>');
				}
				if(content_hover_effects=='shadow_radial'){
					$('head').append('<style >.'+hover_uniqid+'.content_hover_radial:after{background: -webkit-radial-gradient(50% -50%, ellipse, '+hover_shadow+' 0%, rgba(0, 0, 0, 0) 80%);background: radial-gradient(ellipse at 50% -50%, '+hover_shadow+' 0%, rgba(0, 0, 0, 0) 80%);}.'+hover_uniqid+'.content_hover_radial:before{background: -webkit-radial-gradient(50% 150%, ellipse, '+hover_shadow+' 0%, rgba(0, 0, 0, 0) 80%);background: radial-gradient(ellipse at 50% 150%, '+hover_shadow+' 0%, rgba(0, 0, 0, 0) 80%);}</style>');
				}
				if(content_hover_effects=='grow_shadow'){
					$('head').append('<style >.'+hover_uniqid+'.content_hover_grow_shadow:hover{-webkit-box-shadow: 0 10px 10px -10px '+hover_shadow+';-moz-box-shadow: 0 10px 10px -10px '+hover_shadow+';box-shadow: 0 10px 10px -10px '+hover_shadow+';}</style>');
				}			
			});
		}
	});	
} ( jQuery ) );
/*--Content hover Effects --*/
/*--Content hover Effects --*/
( function ( $ ) {
	'use strict';
	$(document).ready(function () {
		if($('.animted-content-inner').length > 0){
			$('.animted-content-inner').each(function () {
				var $this=$(this);
				var bg_uniqid = $this.data('bg_uniqid');
				var bg_animated_color = $this.data('bg_animated_color');
				if(bg_uniqid!=undefined && bg_animated_color!=undefined){
					$('head').append('<style >.'+bg_uniqid+'.pt-plus-bg-color-animated:after{background: '+bg_animated_color+';}</style>');
				}
			});
		}
	});	
} ( jQuery ) );
/*--Content hover Effects --*/
/*-the plus video--*/
! function(e) {
    "use strict";
	
    function t(t) {
        var a = t.find("video"),
		n = t.find(".ts-video-lazyload");
        if (t.is("[data-grow]") && t.css("max-width", "none"), t.find(".ts-video-title, .ts-video-description, .ts-video-play-btn, .ts-video-thumbnail").addClass("ts-video-hidden"), n.length) {
            var i = n.data();
            e("<iframe></iframe>").attr(i).insertAfter(n)
		}
        a.length && a.get(0).play()
	}
	
    function a() {
        e(".ts-video-wrapper[data-inview-lazyload]").one("inview", function(a, n) {
            n && t(e(this))
		})
	}
    e(document).on("click", '[data-mode="lazyload"] .ts-video-play-btn', function(a) {
        a.preventDefault(), t(e(this).closest(".ts-video-wrapper"))
		}), a(), e(document).ajaxComplete(function() {
        a()
		}), e(document).on("lity:open", function() {
        
		}), e(document).on("lity:ready", function(t, a) {
        var n = a.element(),
		i = n.find("video"),
		r = n.find(".ts-video-lazyload");
        if (e(".lity-wrap").attr("id", "ts-video"), r.length) e("<iframe></iframe>").attr(r.data()).insertAfter(r);
        i.length && i.get(0).play()
		}), e(document).on("lity:close", function(t, a) {
        a.element().find("video").length && a.element().find("video").get(0).pause(), e(".ts-video-lity-container .pt-plus-video-frame").remove(), e("[data-hidden-fixed]").removeClass("ts-video-hidden")
		}), e(document).ready(function() {
        e(".ts-video-lightbox-link").off()
	})
}(jQuery);
/*-the plus video--*/

function pt_plus_hexToRgbA(hex,data_opacity){
    var c;
    if(/^#([A-Fa-f0-9]{3}){1,2}$/.test(hex)){
        c= hex.substring(1).split('');
        if(c.length== 3){
            c= [c[0], c[0], c[1], c[1], c[2], c[2]];
		}
        c= '0x'+c.join('');
        return 'rgba('+[(c>>16)&255, (c>>8)&255, c&255].join(',')+','+data_opacity+')';
	}
}
/*-Grid Masonry Metro list js-----*/
(function($) {
    'use strict';
	$(document).ready(function() {
		$('.list-isotope-metro').each(function() {
			var c = $(this);
			var uid=c.data("id");
			var inner_c=$('.'+uid).find(".post-inner-loop");
			
		});
	});
})(jQuery);

(function($) {
    'use strict';
	$(window).on("load resize", function() {
		"use strict";
		if ($('.list-isotope-metro').length) {
			theplus_setup_packery_portfolio("*");	
			$('.list-isotope-metro .post-inner-loop').isotope('layout');
		}		
	});
	$(document).ready(function() {
		if ($('.list-isotope-metro').length) {
			theplus_setup_packery_portfolio("*");
			$('.list-isotope-metro .post-inner-loop').isotope('layout').isotope("reloadItems");
		}
	});
})(jQuery);
function theplus_backend_packery_portfolio(uid,metro_column,metro_style) {
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
function theplus_setup_packery_portfolio(packery_id) {
	var $=jQuery;
	$('.list-isotope-metro').each(function(){
		var uid=$(this).data("id");
		var metro_column=$(this).attr('data-metro-columns');
		var tablet_metro_column=$(this).attr('data-tablet-metro-columns');
		var setPad = 0;
		var myWindow=$(window);
		var responsive_width=window.innerWidth;
		if(responsive_width <= 1024 && tablet_metro_column!=undefined){
			metro_column=tablet_metro_column;
		}
		if ( metro_column== '4') {
			var metro_style=$(this).attr('data-metro-style');
			var	norm_size = Math.floor(($(this).width() - setPad*2)/4),
			double_size = norm_size*2;
			$(this).find('.grid-item').each(function(){
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
			var metro_style=$(this).attr('data-metro-style');
			var	norm_size = Math.floor(($(this).width() - setPad*2)/5),
			double_size = norm_size*2;				
			$(this).find('.grid-item').each(function(){
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
			var metro_style=$(this).attr('data-metro-style');
			var	norm_size = Math.floor(($(this).width() - setPad*2)/6),
			double_size = norm_size*2;				
			$(this).find('.grid-item').each(function(){
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
			var metro_style=$(this).attr('data-metro-style');
			if(responsive_width <= 1024 && tablet_metro_column!=undefined){
				metro_style=$(this).attr('data-tablet-metro-style');
			}
			var	norm_size = Math.floor(($(this).width() - setPad*2)/3),
			double_size = norm_size*2;				
			$(this).find('.grid-item').each(function(){
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
/*-Grid Masonry Metro list js-----*/

/*----load more post ajax----------------*/
;( function($) {
	'use strict';
	$(document).ready(function(){
		if($(".post-load-more").length > 0){
			$(document).on("click",".post-load-more",function(e){
				
				e.preventDefault();
				var current_click= $(this);
				var a= $(this);
				var post_load=a.data('load');
				var post_type=a.data('post_type');
				var texonomy_category=a.data('texonomy_category');
				
				var page = a.attr('data-page');
				var total_page=a.data('total_page');
				var load_class= a.data('load-class');
				var layout=a.data('layout');
				var desktop_column=a.data('desktop-column');
				var tablet_column=a.data('tablet-column');
				var mobile_column=a.data('mobile-column');
				var metro_column=a.data('metro_column');				
				var metro_style=a.data('metro_style');
				var responsive_tablet_metro=a.data('responsive_tablet_metro');
				var tablet_metro_column=a.data('tablet_metro_column');
				var tablet_metro_style=a.data('tablet_metro_style');
				var style=a.data('style');
				var style_layout=a.data('style_layout');
				var offset_posts=a.data('offset-posts');
				var category=a.data('category');
				var post_tags=a.data('post_tags');
				var order_by=a.data('order_by');
				var post_order=a.data('post_order');
				var filter_category=a.data('filter_category');
				var display_post=a.data('display_post');
				var post_load_more=a.data('post_load_more');
				var cart_button=a.data('cart_button');
				var animated_columns=a.data('animated_columns');
				
				var display_post_title=a.data('display_post_title');
				
				var display_post_meta=a.data('display_post_meta');
				var post_meta_tag_style=a.data('post_meta_tag_style');
				var display_excerpt=a.data('display_excerpt');
				var post_excerpt_count=a.data('post_excerpt_count');
				var display_post_category=a.data('display_post_category');
				var post_category_style=a.data('post_category_style');
				var featured_image_type=a.data('featured_image_type');
				
				var display_button=a.data('display_button');
				var button_style=a.data('button_style');
				var before_after=a.data('before_after');
				var button_text=a.data('button_text');
				var button_icon_style=a.data('button_icon_style');
				var button_icon=a.data('button_icon');
				var button_icons_mind=a.data('button_icons_mind');
				var current_text= a.text();
				var loaded_posts= a.data("loaded_posts");
				if ( current_click.data('requestRunning') ) {
					return;
				}
				if(offset_posts==undefined || offset_posts==""){
					offset_posts=0;
				}
				current_click.data('requestRunning', true);
				if(total_page >= page){					
					var offset=(parseInt(page-1)*parseInt(post_load_more))+parseInt(display_post)+parseInt(offset_posts);
					$.ajax({
						type:'POST',
						data:'style='+style+'&style_layout='+style_layout+'&action=theplus_more_post&post_load='+post_load+'&post_type='+post_type+'&texonomy_category='+texonomy_category+'&layout='+layout+'&desktop_column='+desktop_column+'&tablet_column='+tablet_column+'&mobile_column='+mobile_column+'&offset='+offset+'&category='+category+'&post_tags='+post_tags+'&display_post='+display_post+'&order_by='+order_by+'&filter_category='+filter_category+'&post_order='+post_order+'&animated_columns='+animated_columns+'&post_load_more='+post_load_more+'&cart_button='+cart_button+'&metro_column='+metro_column+'&metro_style='+metro_style+'&responsive_tablet_metro='+responsive_tablet_metro+'&tablet_metro_column='+tablet_metro_column+'&tablet_metro_style='+tablet_metro_style+'&display_post_meta='+display_post_meta+'&post_meta_tag_style='+post_meta_tag_style+'&display_excerpt='+display_excerpt+'&post_excerpt_count='+post_excerpt_count+'&display_post_category='+display_post_category+'&post_category_style='+post_category_style+'&featured_image_type='+featured_image_type+'&display_button='+display_button+'&button_style='+button_style+'&before_after='+before_after+'&button_text='+button_text+'&button_icon_style='+button_icon_style+'&button_icon='+button_icon+'&button_icons_mind='+button_icons_mind+'&paged='+page+'&display_post_title='+display_post_title,
						url:theplus_ajax_url,
						beforeSend: function() {
							$(current_click).text('Loading..');
							},success: function(data) {         
							if(data==''){
								$(current_click).addClass("hide");
								$(current_click).parent(".ajax_load_more").append('<div class="plus-all-posts-loaded">'+loaded_posts+'</div>');
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
										animate_offset = p.data("animate-offset"),
										b.waypoint(function(direction ) {
											if( direction === 'down'){
												if(!b.hasClass("animation-done")){
													b.addClass("animation-done").velocity(d, {delay: delay_time,display:'auto'});
												}
											}
										}, {triggerOnce: true,  offset: animate_offset } );
									}
								}
								$(".hover-tilt").hover3d({
									selector: ".blog-list-style-content,.portfolio-item-content",
									shine: !1,
									perspective: 1000,
									invert: !0,
									sensitivity: 35,
								});
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
/*----load more post ajax----------------*/
/*----lazy load ajax----------------*/
;( function($) {
	'use strict';	
	$(window).on("load",function() {	
		if($('body').find('.post-lazy-load').length>=1){
			
			var windowWidth, windowHeight, documentHeight, scrollTop, containerHeight, containerOffset, $window = $(window);
			
			var recalcValues = function() {
				windowWidth = $window.width();
				windowHeight = $window.height();
				documentHeight = $('body').height();
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
				if(scrollTop < documentHeight && scrollTop > (containerHeight + containerOffset - windowHeight)){
					
					$(".post-lazy-load").each(function() {
						var current_click= $(this);
						var a= $(this);
						var post_load=a.data('load');
						var post_type=a.data('post_type');
						var texonomy_category=a.data('texonomy_category');
						
						var page = a.attr('data-page');
						var total_page=a.data('total_page');
						var load_class= a.data('load-class');
						var layout=a.data('layout');
						var desktop_column=a.data('desktop-column');
						var tablet_column=a.data('tablet-column');
						var mobile_column=a.data('mobile-column');
						var metro_column=a.data('metro_column');
						var metro_style=a.data('metro_style');
						var responsive_tablet_metro=a.data('responsive_tablet_metro');
						var tablet_metro_column=a.data('tablet_metro_column');
						var tablet_metro_style=a.data('tablet_metro_style');
						var style=a.data('style');
						var style_layout=a.data('style_layout');
						var offset_posts=a.data('offset-posts');
						var category=a.data('category');
						var post_tags=a.data('post_tags');
						var order_by=a.data('order_by');
						var post_order=a.data('post_order');
						var filter_category=a.data('filter_category');
						var display_post=a.data('display_post');
						var post_load_more=a.data('post_load_more');
						var cart_button=a.data('cart_button');
						var animated_columns=a.data('animated_columns');
						
						var display_post_title=a.data('display_post_title');
						
						var display_post_meta=a.data('display_post_meta');
						var post_meta_tag_style=a.data('post_meta_tag_style');
						var display_excerpt=a.data('display_excerpt');
						var post_excerpt_count=a.data('post_excerpt_count');
						var display_post_category=a.data('display_post_category');
						var post_category_style=a.data('post_category_style');
						var featured_image_type=a.data('featured_image_type');
						
						var display_button=a.data('display_button');
						var button_style=a.data('button_style');
						var before_after=a.data('before_after');
						var button_text=a.data('button_text');
						var button_icon_style=a.data('button_icon_style');
						var button_icon=a.data('button_icon');
						var button_icons_mind=a.data('button_icons_mind');
						
						var current_text= a.text();
						var loaded_posts= a.data("loaded_posts");
						if ( current_click.data('requestRunning') ) {
							return;
						}
						if(offset_posts==undefined || offset_posts==""){
							offset_posts=0;
						}
						if(page<=total_page){
							current_click.data('requestRunning', true);
							var offset=(parseInt(page-1)*parseInt(post_load_more))+parseInt(display_post)+parseInt(offset_posts);							
							$.ajax({
								type:'POST',
								data:'style='+style+'&action=theplus_more_post&post_load='+post_load+'&post_type='+post_type+'&texonomy_category='+texonomy_category+'&layout='+layout+'&desktop_column='+desktop_column+'&tablet_column='+tablet_column+'&mobile_column='+mobile_column+'&offset='+offset+'&category='+category+'&post_tags='+post_tags+'&display_post='+display_post+'&order_by='+order_by+'&filter_category='+filter_category+'&post_order='+post_order+'&animated_columns='+animated_columns+'&post_load_more='+post_load_more+'&cart_button='+cart_button+'&metro_column='+metro_column+'&metro_style='+metro_style+'&responsive_tablet_metro='+responsive_tablet_metro+'&tablet_metro_column='+tablet_metro_column+'&tablet_metro_style='+tablet_metro_style+'&display_post_meta='+display_post_meta+'&post_meta_tag_style='+post_meta_tag_style+'&display_excerpt='+display_excerpt+'&post_excerpt_count='+post_excerpt_count+'&display_post_category='+display_post_category+'&post_category_style='+post_category_style+'&featured_image_type='+featured_image_type+'&display_button='+display_button+'&button_style='+button_style+'&before_after='+before_after+'&button_text='+button_text+'&button_icon_style='+button_icon_style+'&button_icon='+button_icon+'&button_icons_mind='+button_icons_mind+'&paged='+page+'&display_post_title='+display_post_title,
								url:theplus_ajax_url,
								beforeSend: function() {
									$(current_click).text('Loading..');
									},success: function(data) {         
									if(data==''){
										$(current_click).addClass("hide");
										$(current_click).parent(".ajax_lazy_load").append('<div class="plus-all-posts-loaded">'+loaded_posts+'</div>');
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
												animate_offset = p.data("animate-offset"),
												b.waypoint(function(direction ) {
													if( direction === 'down'){
														if(!b.hasClass("animation-done")){
															b.addClass("animation-done").velocity(d, {delay: delay_time,display:'auto'});
														}
													}
												}, {triggerOnce: true,  offset: animate_offset } );
											}
											
										}
										
										$(".hover-tilt").hover3d({
											selector: ".blog-list-style-content,.portfolio-item-content",
											shine: !1,
											perspective: 2e3,
											invert: !0,
											sensitivity: 35,
										});
										page++;
										if(page==total_page){
											$(current_click).addClass("hide");
											$(current_click).attr('data-page', page);
											$(current_click).parent(".ajax_lazy_load").append('<div class="plus-all-posts-loaded">'+loaded_posts+'</div>');
											}else{
											$(current_click).text(current_text);
											$(current_click).attr('data-page', page);	
										}
									}
									$(current_click).text(current_text);
									page++;
									$(current_click).attr('data-page', page);	
									
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
		}
	});
})(jQuery );
/*---*/

/*--bottom bubble js ---*/
function snow_particles_background(canvas_scene, canvas_inner) {
	let circles, target, animateHeader = true;
	let canvas = canvas_inner;
	let width = canvas_scene.innerWidth();
	let height = canvas_scene.innerHeight();
	let canvas_header = canvas_scene;
	let ctx = canvas.getContext('2d');

	initHeader();
	addListeners();

	function initHeader() {
		canvas.width = width;
		canvas.height = height;
		target = {
			x: 0,
			y: height
		};
		canvas_header.css({
			'height': height + 'px'
		});
		circles = [];
		for (let x = 0; x < width * 0.5; x++) {
			let c = new Circle();
			circles.push(c);
		}
		animate();
	}

	function addListeners() {
		window.addEventListener('scroll', scrollCheck);
		window.addEventListener('resize', resize);
	}

	function scrollCheck() {
		if (document.body.scrollTop > height) animateHeader = false;
		else animateHeader = true;
	}

	function resize() {
		width = window.innerWidth;
		height = window.innerHeight;
		canvas_header.css({
			'height': height + 'px'
		});
		canvas.width = width;
		canvas.height = height;
	}

	function animate() {
		if (animateHeader) {
			ctx.clearRect(0, 0, width, height);
			for (let i in circles) {
				circles[i].draw();
			}
		}
		requestAnimationFrame(animate);
	}


	function Circle() {
		let $this = this;

		(function () {
			$this.pos = {};
			init();
		})();

		function init() {
			$this.pos.x = Math.random() * width;
			$this.pos.y = height + Math.random() * 100;
			$this.alpha = 0.1 + Math.random() * 0.4;
			$this.scale = 0.1 + Math.random() * 0.3;
			$this.velocity = Math.random();
		}

		this.draw = function () {
			if ($this.alpha <= 0) {
				init();
			}
			$this.pos.y -= $this.velocity;
			$this.alpha -= 0.0003;
			ctx.beginPath();
			ctx.arc($this.pos.x, $this.pos.y, $this.scale * 10, 0, 2 * Math.PI, false);
			ctx.fillStyle = 'rgba(255,255,255,' + $this.alpha + ')';
			ctx.fill();
		};
	}
}
/*--bottom bubble js ---*/
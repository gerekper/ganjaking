var Widget_DCE_Dynamicposts_timeline_Handler = function ($scope, $) {
	var elementSettings = dceGetElementSettings($scope);
	var timelineEl = $scope.find('.dce-timeline-container.dce-skin-timeline');
    var primoBlocco = 0;
	var primoBloccoPos = 0;
    var ultimoBlocco = 0;
	var ultimoBloccoPos = 0;
    var isTimelineEnabled = false;
    var scrtop = 0;
    var rowspace = Number(elementSettings[dceDynamicPostsSkinPrefix+'timeline_rowspace']['size']);

	function initTimeline() {
	    var verticalTimelines = document.getElementsByClassName("js-dce-timeline"),
			verticalTimelinesArray = [],
			scrolling = false;
		if( verticalTimelines.length > 0 ) {
			for( var i = 0; i < verticalTimelines.length; i++) {
				(function(i){
					verticalTimelinesArray.push(new VerticalTimeline(verticalTimelines[i]));
				})(i);
			}

			// on risize adapting blocks and elements
			window.addEventListener("resize", function(event) {
				checkTimelineScroll();
			});
			//show timeline blocks on scrolling
			window.addEventListener("scroll", function(event) {
				if( !scrolling ) {
					scrolling = true;
					(!window.requestAnimationFrame) ? setTimeout(checkTimelineScroll, 250) : window.requestAnimationFrame(checkTimelineScroll);
				}
			});
			// controllo lo scroll e eseguo la situazione
			checkTimelineScroll();
		}
		function checkTimelineScroll() {
			primoBloccoPos = (primoBlocco.offset().top - timelineEl.offset().top);
			if(primoBloccoPos <= 0 ) primoBloccoPos = 0;
			$scope.find('.dce-timeline-wrapper').get(0).style.setProperty('--lineTop', (primoBloccoPos+10)+'px');

			ultimoBloccoPos = (ultimoBlocco.offset().top - timelineEl.offset().top) + rowspace;
			$scope.find('.dce-timeline-wrapper').get(0).style.setProperty('--lineFixed', ultimoBloccoPos+'px');

			verticalTimelinesArray.forEach(function(timeline){
				timelineSectionHeight = timelineEl.outerHeight(),
				scrtop = $window.scrollTop() - timelineEl.offset().top + ($window.outerHeight() * timeline.offset);
				if( scrtop >= ultimoBloccoPos ){
					scrtop = ultimoBloccoPos;
				}
				$scope.find('.dce-timeline-wrapper').get(0).style.setProperty('--lineProgress', scrtop+'px');

				timeline.showBlocks();
			});
			scrolling = false;
		}
		var isTimelineEnabled = true;
	}

	function layoutTimeline(){
		rowspace = Number(elementSettings[dceDynamicPostsSkinPrefix+'timeline_rowspace']['size']);
		checkTimelineScroll();
	}

	// Vertical Timeline - by CodyHouse.co
	function VerticalTimeline( element ) {
		this.element = element;
		this.blocks = this.element.getElementsByClassName("dce-timeline__block");
		this.images = this.element.getElementsByClassName("dce-timeline__img");
		this.contents = this.element.getElementsByClassName("dce-timeline__content");
		this.offset = 0.5;
		this.hideBlocks();

		primoBlocco = $(this.blocks).first().find( '.dce-timeline__img' );
		ultimoBlocco = $(this.blocks).last().find( '.dce-timeline__img' );

	}

	VerticalTimeline.prototype.hideBlocks = function() {
		if ( !"classList" in document.documentElement ) {
			return; // no animation on older browsers
		}
		//hide timeline blocks which are outside the viewport
		var self = this;
		for( var i = 0; i < this.blocks.length; i++) {
			(function(i){
				if( self.blocks[i].getBoundingClientRect().top > window.innerHeight*self.offset ) {

					self.images[i].classList.add("dce-timeline__img--hidden");
					self.contents[i].classList.add("dce-timeline__content--hidden");
				}
			})(i);
		}
	};

	VerticalTimeline.prototype.showBlocks = function() {
		if ( ! "classList" in document.documentElement ) {
			return;
		}
		var self = this;
		for( var i = 0; i < this.blocks.length; i++) {
			(function(i){
				if(  self.images[i].getBoundingClientRect().top <= window.innerHeight*self.offset ) {
					// add bounce-in animation
					if(self.contents[i].classList.contains("dce-timeline__content--hidden")){
					self.images[i].classList.add("dce-timeline__img--bounce-in");
					self.contents[i].classList.add("dce-timeline__content--bounce-in");
					self.images[i].classList.remove("dce-timeline__img--hidden");
					self.contents[i].classList.remove("dce-timeline__content--hidden");
					}
					self.blocks[i].classList.add("dce-timeline__focus");
				}else{
					self.blocks[i].classList.remove("dce-timeline__focus");
				}
			})(i);
		}
	};

	// -------------- init
    /* A couple of selections. */
    $body = $scope.find('.dce-nextpost-wrapper');
    $window = $(window);
    $html = $(document.documentElement);

    initTimeline();

	// Callback function executed when mutations occur
	var Dyncontel_MutationObserverCallback = function(mutationsList, observer) {
	    for(var mutation of mutationsList) {
	        if (mutation.type == 'attributes') {
	           if (mutation.attributeName === 'class') {
		            if (isTimelineEnabled) {
				      layoutTimeline();
				    }
		        }
	        }
	    }
	};
	dceObserveElement($scope[0], Dyncontel_MutationObserverCallback);
};

jQuery(window).on('elementor/frontend/init', function () {
    elementorFrontend.hooks.addAction('frontend/element_ready/dce-dynamicposts-v2.timeline', Widget_DCE_Dynamicposts_timeline_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-dynamic-woo-products.timeline', Widget_DCE_Dynamicposts_timeline_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-dynamic-woo-products-on-sale.timeline', Widget_DCE_Dynamicposts_timeline_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-dynamic-show-favorites.timeline', Widget_DCE_Dynamicposts_timeline_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-my-posts.timeline', Widget_DCE_Dynamicposts_timeline_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-search-results.timeline', Widget_DCE_Dynamicposts_timeline_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-sticky-posts.timeline', Widget_DCE_Dynamicposts_timeline_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-metabox-relationship.timeline', Widget_DCE_Dynamicposts_timeline_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-acf-relationship.timeline', Widget_DCE_Dynamicposts_timeline_Handler);
});

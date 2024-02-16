
var contentTimelineHandler = function($scope, $) {
	let $timeline  		= $scope.find( '.eael-timeline' ),
		$horizontalTimelineWrap = $scope.find( '.horizontal-timeline-wrapper' ),
		$timelineTrack	= $scope.find( '.eael-horizontal-timeline-track' ),
		$nextArrow 		= $scope.find( '.eael-next-arrow' ),
		$prevArrow 		= $scope.find( '.eael-prev-arrow' ),
		$arrows			= $scope.find( '.eael-arrow' ),
		itemsCount        = $scope.find( '.eael-horizontal-timeline-list--middle .eael-horizontal-timeline-item' ).length,
		currentTransform  = 0,
		currentPosition   = 0,
		transform = {
			desktop: 100 / 3,
			tablet:  100 / 3,
			mobile:  100 / 1
		},
		maxPosition = {
			desktop: Math.max( 0, (itemsCount - 3) ),
			tablet:  Math.max( 0, (itemsCount - 3) ),
			mobile:  Math.max( 0, (itemsCount - 1) )
		},
		currentDeviceMode = elementorFrontend.getCurrentDeviceMode(),
		slidesToScroll 	= $horizontalTimelineWrap.data('slide_to_scroll'),
		columns 	= {
			desktop: 3,
			tablet:  3,
			mobile:  3
		};
		
	var contentBlock 		= $(".eael-content-timeline-block");
	let horizontalTimeline 	= $scope.find('.eael-horizontal-timeline-track').length;

	let containerWidth 		= $('.eael-content-timeline-container', $scope).width();
	let containerLeft 		= $('.eael-content-timeline-container', $scope).offset().left;

	if( horizontalTimeline ){
		$('.eael-horizontal-timeline-track', $scope).on('scroll', function(e) {
			let scrollLeftPosition 	= e.currentTarget.scrollLeft;

			$(".eael-horizontal-timeline-item", $scope).each(function(){
				let itemLeft = $(this).offset().left;
				if( itemLeft > -50 && itemLeft < ( containerLeft + containerWidth + scrollLeftPosition - 100 ) ){
					$(this).addClass('is-active');
				} else{
					if($(this).hasClass('is-active')){
						$(this).removeClass('is-active');
					}
				}
			});
		});
	}

	$(window).on("scroll", function() {
		contentBlock.each(function() {
			if ($(this).find(".eael-highlight")) {
				// Calculate screen middle position, top offset and line height and
				// change line height dynamically

				var lineEnd =
					contentBlock.height() * 0.15 + window.innerHeight / 2;
				var topOffset = $(this).offset().top;
				var lineHeight = window.scrollY + lineEnd * 1.3 - topOffset;

				$(this)
					.find(".eael-content-timeline-inner")
					.css("height", lineHeight + "px");
			}
		});

		if (this.oldScroll > this.scrollY == false) {
			this.oldScroll = this.scrollY;
			// Scroll Down
			$(".eael-content-timeline-block.eael-highlight")
				.prev()
				.find(".eael-content-timeline-inner")
				.removeClass("eael-muted")
				.addClass("eael-highlighted");
		} else if (this.oldScroll > this.scrollY == true) {
			this.oldScroll = this.scrollY;
			// Scroll Up
			$(".eael-content-timeline-block.eael-highlight")
				.find(".eael-content-timeline-inner")
				.addClass("eael-prev-highlighted");
			$(".eael-content-timeline-block.eael-highlight")
				.next()
				.find(".eael-content-timeline-inner")
				.removeClass("eael-highlighted")
				.removeClass("eael-prev-highlighted")
				.addClass("eael-muted");
		}
	});

	setLinePosition();
	
	function setLinePosition() {
		let $line             = $scope.find( '.eael-horizontal-timeline__line' ),
			$firstPoint       = $scope.find( '.eael-horizontal-timeline-item__point-content:first' ),
			$lastPoint        = $scope.find( '.eael-horizontal-timeline-item__point-content:last' ),
			firstPointLeftPos = $firstPoint.position()?.left + parseInt( $firstPoint.css( 'marginLeft' ) ),
			lastPointLeftPos  = $lastPoint.position()?.left + parseInt( $lastPoint.css( 'marginLeft' ) ),
			pointWidth        = $firstPoint.outerWidth();

		if( firstPointLeftPos && lastPointLeftPos && pointWidth ) {
			$line.css( {
				'left': '45px',
				'width': Math.abs( lastPointLeftPos - firstPointLeftPos )
			} );
		}
	}

	// Arrows
	if ( $nextArrow[0] && maxPosition[ currentDeviceMode ] === 0 ) {
		$nextArrow.addClass( 'eael-arrow-disabled' );
	}

	if ( $arrows[0] ) {
		let slidesScroll      = typeof slidesToScroll[ currentDeviceMode ] !== 'undefined' ? parseInt( slidesToScroll[ currentDeviceMode ] ) : 1,
			xPos              = 0,
			yPos              = 0,
			diffpos;

		$arrows.on( 'click', function( event ){
			var $this             = $( this ),
				currentDeviceMode = elementorFrontend.getCurrentDeviceMode(),
				direction         = $this.hasClass( 'eael-next-arrow' ) ? 'next' : 'prev',
				dirMultiplier     = -1;

			if ( slidesScroll > columns[ currentDeviceMode ] ) {
				slidesScroll = columns[ currentDeviceMode ];
			}

			if ( 'next' === direction && currentPosition < maxPosition[ currentDeviceMode ] ) {
				currentPosition += slidesScroll;

				if ( currentPosition > maxPosition[ currentDeviceMode ] ) {
					currentPosition = maxPosition[ currentDeviceMode ];
				}
			}

			if ( 'prev' === direction && currentPosition > 0 ) {
				currentPosition -= slidesScroll;

				if ( currentPosition < 0 ) {
					currentPosition = 0;
				}
			}

			if ( currentPosition > 0 ) {
				$prevArrow.removeClass( 'eael-arrow-disabled' );
			} else {
				$prevArrow.addClass( 'eael-arrow-disabled' );
			}

			if ( currentPosition === maxPosition[ currentDeviceMode ] ) {
				$nextArrow.addClass( 'eaek-arrow-disabled' );
			} else {
				$nextArrow.removeClass( 'eaek-arrow-disabled' );
			}

			if ( currentPosition === 0 ) {
				currentTransform = 0;
			} else {
				currentTransform = currentPosition * transform[ currentDeviceMode ];
			}

			$timelineTrack.css({
				'transform': 'translateX(' + dirMultiplier * currentTransform + '%)'
			});
			
			$(".eael-horizontal-timeline-item", $scope).each(function(){
				let itemLeft = $(this).offset().left;
				if( itemLeft < ( containerLeft + containerWidth ) ){
					$(this).addClass('is-active');
				} else{
					if($(this).hasClass('is-active')){
						$(this).removeClass('is-active');
					}
				}
			});

		} );
	}
};

jQuery(window).on("elementor/frontend/init", function() {
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/eael-content-timeline.default",
		contentTimelineHandler
	);
});
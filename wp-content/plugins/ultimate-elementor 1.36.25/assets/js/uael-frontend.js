( function( $ ) {

	var hotspotInterval = [];
	var hoverFlag = false;
	var isElEditMode = false;
	window.is_fb_loggedin = false;
	window.is_google_loggedin = false;
	var id = window.location.hash.substring( 1 );
	var pattern = new RegExp( "^[\\w\\-]+$" );
	var sanitize_input = pattern.test( id );

	/**
	 * Function to fetch widget settings.
	 */
	var getWidgetSettings = function ($element) {
		var widgetSettings = {},
			modelCID       = $element.data( 'model-cid' );

		if ( isElEditMode && modelCID ) {
			var settings     = elementorFrontend.config.elements.data[ modelCID ],
				settingsKeys = elementorFrontend.config.elements.keys[ settings.attributes.widgetType || settings.attributes.elType ];

			jQuery.each(
				settings.getActiveControls(),
				function( controlKey ) {
					if ( -1 !== settingsKeys.indexOf( controlKey ) ) {
						widgetSettings[ controlKey ] = settings.attributes[ controlKey ];
					}
				}
			);
		} else {
			widgetSettings = $element.data( 'settings' ) || {};
		}

		return widgetSettings;
	};

	/**
	 * Function for Before After Slider animation.
	 *
	 */
	var UAELBASlider = function( $element ) {
		
		$element.css( 'width', '100%' );
		
		var closest_section = $element.closest('.elementor-section');
			if ( 0 != closest_section.length ) {
				$element.css( 'height', ' ' );
			}
		var closest_container = $element.closest('.e-con');
			if ( 0 != closest_container.length ) {
				$element.css( 'height', '100%' );
			}

		max = -1;

		$element.find( "img" ).each(function() {
			if( max < $(this).width() ) {
				max = $(this).width();
			}
		});

		$element.css( 'width', max + 'px' );
	}

	/**
	 * Function for GF Styler select field.
	 *
	 */
	var WidgetUAELGFStylerHandler = function( $scope, $ ) {

		if ( 'undefined' == typeof $scope )
			return;

		var confirmation_div = $scope.find( '.gform_confirmation_message' );
		var form_title = $scope.find( '.uael-gf-form-title' );
		var form_desc = $scope.find( '.uael-gf-form-desc' );

		$scope.find('select:not([multiple])').each(function() {
			var	gfSelectField = $( this );
			if( gfSelectField.next().hasClass('chosen-container') ) {
				gfSelectField.next().wrap( "<span class='uael-gf-select-custom'></span>" );
			} else {
				gfSelectField.wrap( "<span class='uael-gf-select-custom'></span>" );
			}
		});


		if( $scope.hasClass( 'uael-gf-ajax-yes' ) ){
			//AJAX form submission
			jQuery( document ).on( 'gform_confirmation_loaded', function( event, formId ){
    		// code to be trigger when confirmation page is loaded
				form_title.hide();
				form_desc.hide();
			});
		} else {
			//Hide the forms title and description after submit.
			if( confirmation_div.length > 0 ){
				form_title.hide();
				form_desc.hide();
			} else {
				form_title.show();
				form_desc.show();
			}
		}

		if( typeof gform !== 'undefined' ){
			gform.addAction( 'gform_input_change', function( elem ) {
			    if( $scope.find( '.gfield_radio .gchoice_button' ).length && ! $scope.hasClass( 'uael-gf-check-default-yes' ) && 'radio' == $scope.find( elem ).attr( 'type' ) ){
			    	if( $scope.find( elem ).parent().hasClass( 'uael-radio-active') ){
						$scope.find( elem ).parent().removeClass( 'uael-radio-active' );
			    	} else {
						$scope.find( '.gchoice_button' ).removeClass( 'uael-radio-active' );
						$scope.find( elem ).parent().addClass( 'uael-radio-active' );
			    	}
			    }
			}, 10, 3 );
		}
	}

	/**
	 * Function for Caldera Styler select field.
	 *
	 */
	var WidgetUAELCafStylerHandler = function( $scope, $ ) {

		if ( 'undefined' == typeof $scope )
			return;

		var	cafSelectFields = $scope.find('select');
		cafSelectFields.wrap( "<div class='uael-caf-select-custom'></div>" );

		checkRadioField( $scope );

		$( document ).on( 'cf.add', function(){
		   checkRadioField( $scope );
		});

		// Check if custom span exists after radio field.
		function checkRadioField( $scope ) {

			$scope.find('input:radio').each(function() {

				var $this = $( this );

				var radioField = $this.next().hasClass('uael-caf-radio-custom');

				if( radioField ) {
					return;
				} else {
					$this.after( "<span class='uael-caf-radio-custom'></span>" );
				}

			});

		}
	}

	/**
	 * Function for CF7 Styler select field.
	 *
	 */
	var WidgetUAELCF7StylerHandler = function( $scope, $ ) {

		if ( 'undefined' == typeof $scope )
			return;

		var	cf7SelectFields = $scope.find('select:not([multiple])'),
			cf7Loader = $scope.find('span.ajax-loader');


		cf7SelectFields.wrap( "<span class='uael-cf7-select-custom'></span>" );

		cf7Loader.wrap( "<div class='uael-cf7-loader-active'></div>" );

		var wpcf7event = document.querySelector( '.wpcf7' );

		if( null !== wpcf7event ) {
			wpcf7event.addEventListener( 'wpcf7submit', function( event ) {
				var cf7ErrorFields = $scope.find('.wpcf7-not-valid-tip');
			    cf7ErrorFields.wrap( "<span class='uael-cf7-alert'></span>" );
			}, false );
		}

	}

	/**
	 * Function for Fancy Text animation.
	 *
	 */
	 var UAELFancyText = function() {

		var id 					= $( this ).data( 'id' );
		var $this 				= $( this ).find( '.uael-fancy-text-node' );
		var animation			= $this.data( 'animation' );
		var fancystring 		= $this.data( 'strings' );
		var nodeclass           = '.elementor-element-' + id;

		var typespeed 			= $this.data( 'type-speed' );
		var backspeed 			= $this.data( 'back-speed' );
		var startdelay 			= $this.data( 'start-delay' );
		var backdelay 			= $this.data( 'back-delay' );
		var loop 				= $this.data( 'loop' );
		var showcursor 			= $this.data( 'show_cursor' );
		var cursorchar 			= $this.data( 'cursor-char' );

		var speed 				= $this.data('speed');
		var pause				= $this.data('pause');
		var mousepause			= $this.data('mousepause');

		if ( 'type' == animation ) {
			$( nodeclass + ' .uael-typed-main' ).typed({
				strings: fancystring,
				typeSpeed: typespeed,
				startDelay: startdelay,
				backSpeed: backspeed,
				backDelay: backdelay,
				loop: loop,
				showCursor: showcursor,
				cursorChar: cursorchar,
	        });

		} else if ( 'slide' == animation ) {
			$( nodeclass + ' .uael-fancy-text-slide' ).css( 'opacity', '1' );
			$( nodeclass + ' .uael-slide-main' ).vTicker('init', {
					strings: fancystring,
					speed: speed,
					pause: pause,
					mousePause: mousepause,
			});
		} else {

			UAELEffects._animateHeadline(
				$( nodeclass ).find( '.uael-slide-main_ul' ), $this
			);
		}
	}

	/**
	 * Hotspot Tooltip handler Function.
	 *
	 */
	var WidgetUAELHotspotHandler = function( $scope, $ ) {

		if ( 'undefined' == typeof $scope ) {
			return;
		}

		var id 				= $scope.data( 'id' );
		var $this 			= $scope.find( '.uael-hotspot-container' );
		var side			= $this.data( 'side' );
		var trigger			= $this.data( 'hotspottrigger' );
		var arrow			= $this.data( 'arrow' );
		var distance		= $this.data( 'distance' );
		var delay 			= $this.data( 'delay' );
		var animation		= $this.data( 'animation' );
		var anim_duration 	= $this.data( 'animduration' );
		var uaelclass		= 'uael-tooltip-wrap-' + id + ' uael-hotspot-tooltip';
		var zindex			= $this.data( 'zindex' );
		var autoplay		= $this.data( 'autoplay' );
		var repeat 			= $this.data( 'repeat' );
		var overlay 		= $this.data( 'overlay' );

		var length 			= $this.data( 'length' );
		var tour_interval 	= $this.data( 'tourinterval' );
		var action_autoplay = $this.data( 'autoaction' );
		var sid;
		var	scrolling = false;
		var viewport_position	= $this.data( 'hotspotviewport' );
		var tooltip_maxwidth	= $this.data( 'tooltip-maxwidth' );
		var tooltip_minwidth	= $this.data( 'tooltip-minwidth' );

		if( 'custom' == trigger ) {
			passtrigger = 'click';
		} else {
			passtrigger = trigger;
		}
		clearInterval( hotspotInterval[ id ] );

		// Declare & pass values to Tooltipster js function.
		function tooltipsterCall( selector, triggerValue ) {
			$( selector ).tooltipster({
	        	theme: ['tooltipster-noir', 'tooltipster-noir-customized'],
	        	minWidth: tooltip_minwidth,
	        	maxWidth: tooltip_maxwidth,
	        	side : side,
	        	trigger : triggerValue,
	        	arrow : arrow,
	        	distance : distance,
	        	delay : delay,
	        	animation : animation,
	        	uaelclass : uaelclass,
	        	zIndex : zindex,
	        	interactive : true,
	        	animationDuration : anim_duration,
	        });
		}

		// Disable prev & next nav for 1st & last tooltip.
		function tooltipNav() {
			if( 'yes' != repeat ) {
				$( ".uael-prev-" + id + '[data-tooltipid="1"]' ).addClass( "inactive" );
				$( ".uael-next-" + id + '[data-tooltipid="' + length + '"]' ).addClass( "inactive" );
			}
		}

		// Execute Tooltipster function
		tooltipsterCall( '.uael-hotspot-main-' + id, trigger );

		// Tooltip execution for tour functionality.
		function sectionInterval() {

			hotspotInterval[ id ] = setInterval( function() {
				var $open_hotspot_node = $( '.uael-hotspot-main-' + id + '.open' );
				sid = $open_hotspot_node.data( 'uaeltour' );

				if( ! hoverFlag ) {
					$open_hotspot_node.trigger( 'click' );
					if( 'yes' == repeat ) {
						if ( ! elementorFrontend.isEditMode() ) {
							if( sid == length ) {
								sid = 1;
							} else {
								sid = sid + 1;
							}
							$('.uael-hotspot-main-' + id + '[data-uaeltour="' + sid + '"]').trigger( 'click' );
							$( window ).on( 'scroll', checkScroll );

							function checkScroll() {
								if( !scrolling ) {
									scrolling = true;
									(!window.requestAnimationFrame) ? setTimeout(updateSections, 300) : window.requestAnimationFrame(updateSections);
								}
							}

							function updateSections() {
								var halfWindowHeight = $(window).height()/2,
									scrollTop = $(window).scrollTop(),
									section = $scope.find( '.uael-hotspot-container' );

								if( ! (section.offset().top - halfWindowHeight < scrollTop ) && ( section.offset().top + section.height() - halfWindowHeight > scrollTop) ) {
								} else {
									var hotspot_main = $( '.uael-hotspot-main-' + id + '.open' );
									hotspot_main.tooltipster( 'close' );
									hotspot_main.removeClass( 'open' );
									clearInterval( hotspotInterval[ id ] );
									buttonOverlay();
									$( overlay_id ).show();
								}
								scrolling = false;
							}
						} else {
							if( sid < length ) {
								sid = sid + 1;
								$('.uael-hotspot-main-' + id + '[data-uaeltour="' + sid + '"]').trigger( 'click' );
							}
							else if( sid == length ) {
								clearInterval( hotspotInterval[ id ] );
								buttonOverlay();
								$( overlay_id ).show();
							}
						}

					} else if( 'no' == repeat ) {
						if( sid < length ) {
							sid = sid + 1;
							$( '.uael-hotspot-main-' + id + '[data-uaeltour="' + sid + '"]' ).trigger( 'click' );
						}
						else if( sid == length ) {
							clearInterval( hotspotInterval[ id ] );
							buttonOverlay();
							$( overlay_id ).show();
						}
					}
				}

				tour_interval 	= $( '.uael-hotspot-container' ).data( 'tourinterval' );
				tour_interval = parseInt( tour_interval );
			}, tour_interval );
		}

		// Execute Tooltip execution for tour functionality
		function tourPlay() {

			clearInterval( hotspotInterval[ id ] );

			// Open previous tooltip on trigger
			$( '.uael-prev-' + id ).off('click.prevtrigger').on( 'click.prevtrigger', function(e) {
				clearInterval( hotspotInterval[ id ] );
				var sid = $(this).data( 'tooltipid' );
				if( sid <= length ) {
					$( '.uael-hotspot-main-' + id + '[data-uaeltour="' + sid + '"]' ).trigger( 'click' );
					if( 'yes' == repeat ) {
						if( sid == 1 ) {
							sid = length + 1;
						}
					}
					sid = sid - 1;
					$( '.uael-hotspot-main-' + id + '[data-uaeltour="' + sid + '"]' ).trigger( 'click' );
				}
				if( 'yes' == autoplay ) {
					sectionInterval();
				}
			});

			// Open next tooltip on trigger
			$( '.uael-next-' + id ).off('click.nexttrigger').on( 'click.nexttrigger', function(e) {
				clearInterval( hotspotInterval[ id ] );
				var sid = $(this).data( 'tooltipid' );
				if( sid <= length ) {
					$( '.uael-hotspot-main-' + id + '[data-uaeltour="' + sid + '"]' ).trigger( 'click' );
					if( 'yes' == repeat ) {
						if( sid == length ) {
							sid = 0;
						}
					}
					sid = sid + 1;
					$( '.uael-hotspot-main-' + id + '[data-uaeltour="' + sid + '"]' ).trigger( 'click' );
				}
				if( 'yes' == autoplay ) {
					sectionInterval();
				}
			});

			$( '.uael-tour-end-' + id ).off('click.endtour').on( 'click.endtour', function(e) {
				clearInterval( hotspotInterval[ id ] );
				e.preventDefault();
				var hotspot_main = $( '.uael-hotspot-main-' + id + '.open' );
				hotspot_main.tooltipster( 'close' );
				hotspot_main.removeClass( 'open' );

				if( 'auto' == action_autoplay && 'yes' == autoplay ) {
					$( '.uael-hotspot-main-' + id ).css( "pointer-events", "none" );
				} else {
					buttonOverlay();
					$( overlay_id ).show();
				}
			});

			// Add & remove open class for tooltip.
			$( '.uael-hotspot-main-' + id ).off('click.triggertour').on('click.triggertour', function(e) {
				var $this = $(this);
				if ( ! $this.hasClass('open') ) {
					$this.tooltipster( 'open' );
					$this.addClass( 'open' );
				    if( 'yes' == autoplay ) {
						$this.css( "pointer-events", "visible" );
						$( '.uael-hotspot-main-' + id + '.open' ).on( 'mouseenter mouseleave', function(){
							hoverFlag = true;
						}, function(){
							hoverFlag = false;
						});
					}
				} else {
					$this.tooltipster( 'close' );
					$this.removeClass( 'open' );
					if( 'yes' == autoplay ) {
						$this.css( "pointer-events", "none" );
					}
				}
			});

			//Initialy open first tooltip by default.
			if( 'yes' == autoplay ) {
				$( '.uael-hotspot-main-' + id ).css( "pointer-events", "none" );
				tooltipNav();
				$( '.uael-hotspot-main-' + id + '[data-uaeltour="1"]' ).trigger( 'click' );
				sectionInterval();
			} else if( 'no' == autoplay ) {
				$( '.uael-hotspot-main-' + id ).css( "pointer-events", "none" );
				tooltipNav();
				$( '.uael-hotspot-main-' + id + '[data-uaeltour="1"]' ).trigger( 'click' );
			}
		}

		// Add button overlay when tour ends.
		function buttonOverlay() {
			if( 'custom' == trigger ) {
				if( 'yes' == overlay ) {
					if( 'yes' == autoplay ) {
						var overlay_id 	= $scope.find( '.uael-hotspot-overlay' );
						var button_id 	= $scope.find( '.uael-overlay-button' );

						if( ! isElEditMode ) {
							$( button_id ).off().on( 'click', function(e) {
								$( overlay_id ).hide();
								tourPlay();
							});
						}
					}
				} else if( 'auto' == action_autoplay && 'yes' == autoplay ) {
					if( ! isElEditMode ) {

						if( typeof elementorFrontend.waypoint !== 'undefined' ) {
							elementorFrontend.waypoint(
								$this,
								tourPlay,
								{
									offset: viewport_position + '%'
								}
							);
						}
					}
				} else {
					tourPlay();
				}
			}
		}

		// Start of hotspot functionality.
		if( 'custom' == trigger ) {

			var overlay_id 	= $scope.find( '.uael-hotspot-overlay' );
			buttonOverlay();
		} else {
			clearInterval( hotspotInterval[ id ] );
		}

	}

	/**
	 * Price Table Tooltip handler Function.
	 *
	 */
	var WidgetUAELPriceTableHandler = function( $scope, $ ) {

		if ( 'undefined' == typeof $scope ) {
			return;
		}

		var id 				        = $scope.data( 'id' );
		var $this 			        = $scope.find( '.uael-price-table-features-list' );
		var side			        = $this.data( 'side' );
		var trigger			        = $this.data( 'hotspottrigger' );
		var arrow			        = $this.data( 'arrow' );
		var distance		        = $this.data( 'distance' );
		var delay 			        = $this.data( 'delay' );
		var animation		        = $this.data( 'animation' );
		var anim_duration 	        = $this.data( 'animduration' );
		var uaelclass		        = 'uael-price-table-wrap-' + id;
		var uaelclassStrikeTooltip	= 'uael-price-table-wrap-' + id;
		var zindex			        = $this.data( 'zindex' );
		var length 			        = $this.data( 'length' );
		var tooltip_maxwidth	    = $this.data( 'tooltip-maxwidth' );
		var tooltip_minwidth	    = $this.data( 'tooltip-minwidth' );
		var responsive              = $this.data( 'tooltip-responsive' );
		var enable_tooltip          = $this.data( 'enable-tooltip' );
		var pricing_container       = $scope.find( '.uael-pricing-container' );
		var strike_tooltip          = pricing_container.data( 'strike-tooltip' );
		var strike_tooltip_position = pricing_container.data( 'strike-tooltip-position' );
		var strike_tooltip_hide     = pricing_container.data( 'strike-tooltip-hide' );

		uaelclass += ' uael-price-table-tooltip uael-features-tooltip-hide-' + responsive;
		$this.addClass( 'uael-features-tooltip-hide-' + responsive );

		uaelclassStrikeTooltip += ' uael-strike-price-tooltip uael-strike-tooltip-hide-' + strike_tooltip_hide;
		$this.addClass( 'uael-strike-tooltip-hide-' + strike_tooltip_hide );

		// Declare & pass values to Tooltipster js function.
		function tableTooltipsterCall( selector, triggerValue ) {
			$( selector ).tooltipster({
	        	theme: ['tooltipster-noir', 'tooltipster-noir-customized'],
	        	minWidth: tooltip_minwidth,
	        	maxWidth: tooltip_maxwidth,
	        	side : side,
	        	trigger : triggerValue,
	        	arrow : arrow,
	        	distance : distance,
	        	delay : delay,
	        	animation : animation,
	        	zIndex : zindex,
	        	interactive : true,
	        	animationDuration : anim_duration,
	        	uaelclass: uaelclass
	        });
		}

		if( 'yes' === enable_tooltip ){
			// Execute Tooltipster function
			tableTooltipsterCall( '.uael-price-table-content-' + id, trigger );
		}

		if ( 'yes' === strike_tooltip ) {
			$( '.uael-strike-tooltip' ).tooltipster(
				{
					theme: ['tooltipster-noir', 'tooltipster-noir-customized'],
					side : strike_tooltip_position,
					trigger : 'hover',
					arrow : true,
					distance : 6,
					delay : 300,
					animation : 'fade',
					zIndex : 99,
					interactive : true,
					animationDuration : 350,
					uaelclass: uaelclass
				}
			);
		}
	}

	/**
	 * Before After Slider handler Function.
	 *
	 */
	 var WidgetUAELBASliderHandler = function( $scope, $ ) {

		if ( 'undefined' == typeof $scope )
			return;

		var selector = $scope.find( '.uael-ba-container' );
		var initial_offset = selector.data( 'offset' );
		var move_on_hover = selector.data( 'move-on-hover' );
		var orientation = selector.data( 'orientation' );

		$scope.css( 'width', '' );
		$scope.css( 'height', '' );

		if( 'yes' == move_on_hover ) {
			move_on_hover = true;
		} else {
			move_on_hover = false;
		}

		$scope.imagesLoaded( function() {

			UAELBASlider( $scope );

			$scope.find( '.uael-ba-container' ).twentytwenty(
	            {
	                default_offset_pct: initial_offset,
	                move_on_hover: move_on_hover,
	                orientation: orientation
	            }
	        );

	        $( window ).on( 'resize', function( e ) {
	        	UAELBASlider( $scope );
	        } );
		} );
	};

	/**
	 * Fancy text handler Function.
	 *
	 */
	 var WidgetUAELFancyTextHandler = function( $scope, $ ) {

		if ( 'undefined' == typeof $scope ) {
			return;
		}
		var node_id = $scope.data( 'id' );
		var viewport_position	= 90;
		var selector = $( '.elementor-element-' + node_id );

		var current_device = elementorFrontend.getCurrentDeviceMode();
		if( 'mobile' === current_device ) {
			viewport_position	= 100;
		}

		if( typeof elementorFrontend.waypoint !== 'undefined' ) {
			elementorFrontend.waypoint(
				selector,
				UAELFancyText,
				{
					offset: viewport_position + '%'
				}
			);
		}

	};

	/**
	 *
	 * Timeline handler Function.
	 *
	 */
	var WidgetUAELTimelineHandler = function( $scope, $ ) {

		if ( 'undefined' == typeof $scope )
			return;

		// Define variables.
		var $this          		= $scope.find( '.uael-timeline-node' );
		var timeline_main   	= $scope.find(".uael-timeline-main");

		if ( timeline_main.length < 1 ) {
			return false;
		}

		var animate_border 		= $scope.find(".animate-border");
		var timeline_icon  		= $scope.find(".uael-timeline-marker");
		var line_inner   		= $scope.find(".uael-timeline__line__inner");
		var line_outer   		= $scope.find(".uael-timeline__line");
		var $icon_class 		= $scope.find(".uael-timeline-marker");
		var $card_last 			= $scope.find(".uael-timeline-field:last-child");

		var timeline_start_icon = $icon_class.first().position();
		var timeline_end_icon = $icon_class.last().position();

		line_outer.css('top', timeline_start_icon.top );

		var timeline_card_height = $card_last.height();

		var last_item_top = $card_last.offset().top - $this.offset().top;

		var $last_item, parent_top;

		if( $scope.hasClass('uael-timeline-arrow-center')) {

			line_outer.css('bottom', timeline_end_icon.top );

			parent_top = last_item_top - timeline_start_icon.top;
			$last_item = parent_top + timeline_end_icon.top;

		} else if( $scope.hasClass('uael-timeline-arrow-top')) {

			var top_height = timeline_card_height - timeline_end_icon.top;
			line_outer.css('bottom', top_height );

			$last_item = last_item_top;

		} else if( $scope.hasClass('uael-timeline-arrow-bottom')) {

			var bottom_height = timeline_card_height - timeline_end_icon.top;
			line_outer.css('bottom', bottom_height );

			parent_top = last_item_top - timeline_start_icon.top;
			$last_item = parent_top + timeline_end_icon.top;

		}

		var elementEnd = $last_item + 20;

		var initial_height = 0;

		line_inner.height(initial_height);

		var num = 0;

		// Callback function for all event listeners.
		function uaelTimelineFunc() {
			timeline_main   	= $scope.find(".uael-timeline-main");
			if ( timeline_main.length < 1 ) {
				return false;
			}

			var $document = $(document);
			// Repeat code for window resize event starts.
			timeline_start_icon = $icon_class.first().position();
			timeline_end_icon 	= $icon_class.last().position();

			$card_last 			= $scope.find(".uael-timeline-field").last();

			line_outer.css('top', timeline_start_icon.top );

			timeline_card_height = $card_last.height();

			last_item_top = $card_last.offset().top - $this.offset().top;

			if( $scope.hasClass('uael-timeline-arrow-center')) {

				line_outer.css('bottom', timeline_end_icon.top );
				parent_top = last_item_top - timeline_start_icon.top;
				$last_item = parent_top + timeline_end_icon.top;

			} else if( $scope.hasClass('uael-timeline-arrow-top')) {

				var top_height = timeline_card_height - timeline_end_icon.top;
				line_outer.css('bottom', top_height );
				$last_item = last_item_top;

			} else if( $scope.hasClass('uael-timeline-arrow-bottom')) {

				var bottom_height = timeline_card_height - timeline_end_icon.top;
				line_outer.css('bottom', bottom_height );
				parent_top = last_item_top - timeline_start_icon.top;
				$last_item = parent_top + timeline_end_icon.top;
			}
			elementEnd = $last_item + 20;

			// Repeat code for window resize event ends.

			var viewportHeight = document.documentElement.clientHeight;
			var viewportHeightHalf = viewportHeight/2;
			var elementPos = $this.offset().top;
			var new_elementPos = elementPos + timeline_start_icon.top;

			var photoViewportOffsetTop = new_elementPos - $document.scrollTop();

			if (photoViewportOffsetTop < 0) {
				photoViewportOffsetTop = Math.abs(photoViewportOffsetTop);
			} else {
				photoViewportOffsetTop = -Math.abs(photoViewportOffsetTop);
			}

			if ( elementPos < (viewportHeightHalf) ) {

				if ( (viewportHeightHalf) + Math.abs(photoViewportOffsetTop) < (elementEnd) ) {
					line_inner.height((viewportHeightHalf) + photoViewportOffsetTop);
				}else{
					if ( (photoViewportOffsetTop + viewportHeightHalf) >= elementEnd ) {
						line_inner.height(elementEnd);
					}
				}
			} else {
				if ( (photoViewportOffsetTop  + viewportHeightHalf) < elementEnd ) {
					if (0 > photoViewportOffsetTop) {
						line_inner.height((viewportHeightHalf) - Math.abs(photoViewportOffsetTop));
						++num;
					} else {
						line_inner.height((viewportHeightHalf) + photoViewportOffsetTop);
					}
				} else {
					if ( (photoViewportOffsetTop + viewportHeightHalf) >= elementEnd ) {
						line_inner.height(elementEnd);
					}
				}
			}

			var timeline_icon_pos, timeline_card_pos;
			var elementPos, elementCardPos;
			var timeline_icon_top, timeline_card_top;
			timeline_icon = $scope.find(".uael-timeline-marker");
			animate_border 	= $scope.find(".animate-border");

			for (var i = 0; i < timeline_icon.length; i++) {

				timeline_icon_pos = $(timeline_icon[i]).offset().top;
				timeline_card_pos = $(animate_border[i]).offset().top;

				elementPos = $this.offset().top;
				elementCardPos = $this.offset().top;

				timeline_icon_top = timeline_icon_pos - $document.scrollTop();
				timeline_card_top = timeline_card_pos - $document.scrollTop();

				if ( ( timeline_card_top ) < ( ( viewportHeightHalf ) ) ) {

					animate_border[i].classList.remove("out-view");
					animate_border[i].classList.add("in-view");

				} else {
					// Remove classes if element is below than half of viewport.
					animate_border[i].classList.add("out-view");
					animate_border[i].classList.remove("in-view");
				}

				if ( ( timeline_icon_top ) < ( ( viewportHeightHalf ) ) ) {

					// Add classes if element is above than half of viewport.
					timeline_icon[i].classList.remove("out-view-timeline-icon");
					timeline_icon[i].classList.add("in-view-timeline-icon");

				} else {

					// Remove classes if element is below than half of viewport.
					timeline_icon[i].classList.add("out-view-timeline-icon");
					timeline_icon[i].classList.remove("in-view-timeline-icon");

				}
			}

		}
		// Listen for events.
		window.addEventListener("load", uaelTimelineFunc);
		window.addEventListener("resize", uaelTimelineFunc);
		window.addEventListener("scroll", uaelTimelineFunc);
		window.addEventListener("click", function() {
			uaelTimelineFunc();
		});

		var post_selector = $scope.find( '.uael-days' );

		var node_id = $scope.data( 'id' );

		if ( post_selector.hasClass( 'uael-timeline-infinite-load' ) ) {

			$( window ).scroll( function(){
				$('.elementor-element-' + node_id + ' .uael-timeline-wrapper').jscroll({
					loadingHtml: '<img src="' + uael_post_loader_script.post_loader + '" />',
				    nextSelector: '#uael-timeline-' + node_id + ' a.next',
				    contentSelector: '.elementor-element-' + node_id + ' .uael-timeline-main',
				    callback: function() {
			            window.addEventListener("load", uaelTimelineFunc);
						window.addEventListener("resize", uaelTimelineFunc);
						window.addEventListener("scroll", uaelTimelineFunc);
			        }
				});
			});

		}
	};

	/*
	 *
	 * Radio Button Switcher JS Function.
	 *
	 */
	var WidgetUAELContentToggleHandler = function( $scope, $ ) {

		if ( 'undefined' == typeof $scope ) {
			return;
		}

		var rbs_section_1   = $scope.find( ".uael-rbs-section-1" );
		var rbs_section_2   = $scope.find( ".uael-rbs-section-2" );
		var main_btn        = $scope.find( ".uael-main-btn" );
		var switch_type     = main_btn.attr( 'data-switch-type' );
		var rbs_label_1   	= $scope.find( ".uael-sec-1" );
		var rbs_label_2   	= $scope.find( ".uael-sec-2" );
		var current_class;

		switch ( switch_type ) {
			case 'round_1':
				current_class = '.uael-switch-round-1';
				break;
			case 'round_2':
				current_class = '.uael-switch-round-2';
				break;
			case 'rectangle':
				current_class = '.uael-switch-rectangle';
				break;
			case 'label_box':
				current_class = '.uael-switch-label-box';
				break;
			default:
				current_class = 'No Class Selected';
				break;
		}
		var rbs_switch      = $scope.find( current_class );

		if( '' !== id && sanitize_input ){
			if ( id === 'content-1' || id === 'content-2' ) {
				UAELContentToggle._openOnLink( $scope, rbs_switch );
			}
		}

		setTimeout( function(){

			if( rbs_switch.is( ':checked' ) ) {
				rbs_section_1.hide();
				rbs_section_2.show();
			} else {
				rbs_section_1.show();
				rbs_section_2.hide();
			}
		}, 100 );

		rbs_switch.on( 'click', function(e){
			rbs_section_1.toggle();
			rbs_section_2.toggle();
		});

		/* Label 1 Click */
		rbs_label_1.on( 'click', function(e){
			// Uncheck
			rbs_switch.prop( "checked", false);
			rbs_section_1.show();
			rbs_section_2.hide();

		});

		/* Label 2 Click */
		rbs_label_2.on('click', function(e){
			// Check
			rbs_switch.prop( "checked", true);
			rbs_section_1.hide();
			rbs_section_2.show();
		});
	};

	UAELContentToggle = {
		/**
		 * Open specific section on click of link
		 *
		 */

		_openOnLink: function( $scope, rbs_switch ){

			var node_id 		= $scope.data( 'id' );
			var node          	= '.elementor-element-' + node_id;
			var node_toggle     = '#uael-toggle-init' + node;

			$( 'html, body' ).animate( {
				scrollTop: $( '#uael-toggle-init' ).find( '.uael-rbs-wrapper' ).offset().top
			}, 500 );

			if( id === 'content-1' ) {

				$( node_toggle + ' .uael-rbs-content-1' ).show();
				$( node_toggle + ' .uael-rbs-content-2' ).hide();
				rbs_switch.prop( "checked", false );
			} else {

				$( node_toggle + ' .uael-rbs-content-2' ).show();
				$( node_toggle + ' .uael-rbs-content-1' ).hide();
				rbs_switch.prop( "checked", true );
			}
		}
	}

	/**
	 * Video Gallery handler Function.
	 *
	 */
	 var WidgetUAELVideoGalleryHandler = function( $scope, $ ) {

		if ( 'undefined' == typeof $scope ) {
			return;
		}

		var selector = $scope.find( '.uael-video-gallery-wrap' );
		var layout = selector.data( 'layout' );
		var action = selector.data( 'action' );
		var all_filters = selector.data( 'all-filters' );
		var $tabs_dropdown = $scope.find('.uael-filters-dropdown-list');

		if ( selector.length < 1 ) {
			return;
		}

		if ( 'lightbox' == action ) {
			$scope.find( '.uael-vg__play_full' ).fancybox();
		} else if ( 'inline' == action ) {
			$scope.find( '.uael-vg__play_full' ).on( 'click', function( e ) {

				e.preventDefault();

				var iframe 		= $( "<iframe/>" );
				var $this 		= $( this );
				var vurl 		= $this.data( 'url' );
				var overlay		= $this.closest( '.uael-video__gallery-item' ).find( '.uael-vg__overlay' );
				var wrap_outer = $this.closest( '.uael-video__gallery-iframe' );

				iframe.attr( 'src', vurl );
				iframe.attr( 'frameborder', '0' );
				iframe.attr( 'allowfullscreen', '1' );
				iframe.attr( 'allow', 'autoplay;encrypted-media;' );

				wrap_outer.html( iframe );
				wrap_outer.attr( 'style', 'background:#000;' );
				overlay.hide();

			} );
		}

		// If Carousel is the layout.
		if( 'carousel' == layout ) {

			var slider_options 	= selector.data( 'vg_slider' );

			if ( selector.find( '.uael-video__gallery-iframe' ).imagesLoaded( { background: true } ) )
			{
				selector.slick( slider_options );
			}
		}

		$( 'html' ).on( 'click', function() {
			$tabs_dropdown.removeClass( 'show-list' );
		});

		$scope.on( 'click', '.uael-filters-dropdown-button', function(e) {
			e.stopPropagation();
			$tabs_dropdown.addClass( 'show-list' );
		});

		// If Filters is the layout.
		if( selector.hasClass( 'uael-video-gallery-filter' ) ) {

			var filters = $scope.find( '.uael-video__gallery-filters' );
			var def_cat = '*';
			var filter_cat;

			if( '' !== id && sanitize_input ) {
				var select_filter = filters.find("[data-filter='" + '.' + id.toLowerCase() + "']");

				if ( select_filter.length > 0 ) {
					def_cat 	= '.' + id.toLowerCase();
					select_filter.siblings().removeClass( 'uael-filter__current' );
					select_filter.addClass( 'uael-filter__current' );
				}
			}

			if ( filters.length > 0 ) {

				var def_filter = filters.data( 'default' );

				if ( '' !== def_filter ) {

					def_cat 	= def_filter;
					def_cat_sel = filters.find( '[data-filter="' + def_filter + '"]' );

					if ( def_cat_sel.length > 0 ) {
						def_cat_sel.siblings().removeClass( 'uael-filter__current' );
						def_cat_sel.addClass( 'uael-filter__current' );
					}

					if ( -1 == all_filters.indexOf( def_cat.replace('.', "") ) ) {
						def_cat = '*';
					}
				}
			}

			var $obj = {};

			selector.imagesLoaded( { background: '.item' }, function( e ) {

				$obj = selector.isotope({
					filter: def_cat,
					layoutMode: 'masonry',
					itemSelector: '.uael-video__gallery-item',
				});

				selector.find( '.uael-video__gallery-item' ).resize( function() {
					$obj.isotope( 'layout' );
				});
			});

			$scope.find( '.uael-video__gallery-filter' ).on( 'click', function() {

				$( this ).siblings().removeClass( 'uael-filter__current' );
				$( this ).addClass( 'uael-filter__current' );

				var value = $( this ).data( 'filter' );

				if( '*' === value ) {
					filter_cat = $scope.find('.uael-video-gallery-wrap').data('filter-default');
				} else {
					filter_cat = value.replace( '.filter-', "" );
				}

				if( $scope.find( '.uael-video__gallery-filters' ).data( 'default' ) ){
					var def_filter = $scope.find( '.uael-video__gallery-filters' ).data( 'default' );
					var def_filter_length = def_filter.length - 8;
				}
				else{
					var def_filter = $scope.find('.uael-video-gallery-wrap').data('filter-default');
					var def_filter_length = def_filter.length;
				}
				var ajax_str_img_text = $scope.find( '.uael-filter__current' ).text(),
				ajax_str_filter_text  = $scope.find( '.uael-filters-dropdown-list .uael-filter__current' ).text(),
				url                   = window.location.hash.replace( '#', '' ),
				str_replace_text      = ajax_str_img_text.replace( ajax_str_filter_text,'' ),
				str_cat_text          = ajax_str_img_text.replace( str_replace_text,'' );
				if( ( !url && ( window.screen.availWidth > 768 ) ) || url ){
					str_cat_text = ajax_str_img_text.replace( ajax_str_filter_text,'' );
				}
				if( url && ( window.screen.availWidth < 768 ) ){
					str_cat_text = ajax_str_img_text.replace( str_replace_text,'' );
				}

				$scope.find( '.uael-filters-dropdown-button' ).text( str_cat_text );
				selector.isotope( { filter: value } );
			});

			if( $scope.find( '.uael-video__gallery-filters' ).data( 'default' ) ){
				var def_filter = $scope.find( '.uael-video__gallery-filters' ).data( 'default' );
				var def_filter_length = def_filter.length - 8;
			}
			else{
				var def_filter = $scope.find('.uael-video-gallery-wrap').data('filter-default');
				var def_filter_length = def_filter.length;
			}

			var str_vid_text = $scope.find( '.uael-filter__current' ).first().text();
			$scope.find( '.uael-filters-dropdown-button' ).text( str_vid_text );
		}
	}

	/*
		* Image Gallery handler Function.
		*
		*/
	var WidgetUAELImageGalleryHandler = function( $scope, $ ) {

		if ( 'undefined' == typeof $scope ) {
			return;
		}

		var $justified_selector	= $scope.find('.uael-img-justified-wrap');
		var row_height			= $justified_selector.data( 'rowheight' );
		var lastrow				= $justified_selector.data( 'lastrow' );
		var $tabs_dropdown 		= $scope.find('.uael-filters-dropdown-list');

		var img_gallery	 		= $scope.find('.uael-image-lightbox-wrap');
		var lightbox_actions 	= [];
		var fancybox_node_id 	= 'uael-fancybox-gallery-' + $scope.data( 'id' );
		var lightbox_loop 		= img_gallery.data( 'lightbox-gallery-loop' );
		var default_filter;

		if ( $scope.find( '.uael-masonry-filters' ).data( 'default' ) ) {
			default_filter = $scope.find( '.uael-masonry-filters' ).data( 'default' );
		} else {
			default_filter = $scope.find( '.uael-img-gallery-wrap' ).data( 'filter-default' )
		}

		if( img_gallery.length > 0 ) {
			lightbox_actions = JSON.parse( img_gallery.attr('data-lightbox_actions') );
		}

		var fancyboxInit = {
			initializeLightbox: function ( imgNode ) {
				$scope.find( imgNode + ' [data-fancybox="uael-gallery"]' ).fancybox({
					buttons: lightbox_actions,
					animationEffect: "fade",
					baseClass: fancybox_node_id,
					loop: lightbox_loop,
				});
			}
		}

		if ( undefined !== default_filter ) {
			if ( '' === default_filter || 'All' === default_filter ) {
				fancyboxInit.initializeLightbox( '.uael-grid-item' );
			} else {
				fancyboxInit.initializeLightbox( '.uael-grid-item.' + default_filter.substr( 1 ) );
			}
		}

		$scope.on(
			'click',
			'.uael-masonry-filter',
			function (){
				var $this  = $( this );
				var filter = $this.attr( 'data-filter' );
				if ( '*' === filter ) {
					fancyboxInit.initializeLightbox( '.uael-grid-item' );
				} else {
					fancyboxInit.initializeLightbox( '.uael-grid-item.' + filter.substr( 1 ) );
				}
			}
		);

		if ( $justified_selector.length > 0 ) {
			$justified_selector.imagesLoaded( function() {
			})
			.done(function( instance ) {
				$justified_selector.justifiedGallery({
					rowHeight : row_height,
					lastRow : lastrow,
					selector : 'div',
					waitThumbnailsLoad : true,
				});
			});
		}

		$('html').on('click', function() {
			$tabs_dropdown.removeClass( 'show-list' );
		});

		$scope.on( 'click', '.uael-filters-dropdown-button', function(e) {
			e.stopPropagation();
			$tabs_dropdown.addClass( 'show-list' );
		});

		/* Carousel */
		var slider_selector	= $scope.find('.uael-img-carousel-wrap');
		if ( slider_selector.length > 0 ) {

			var adaptiveImageHeight = function( e, obj ) {

				var node = obj.$slider,
				post_active = node.find('.slick-slide.slick-active'),
				max_height = -1;

				post_active.each(function( i ) {

					var $this = $( this ),
						this_height = $this.innerHeight();

					if( max_height < this_height ) {
						max_height = this_height;
					}
				});

				node.find('.slick-list.draggable').animate({ height: max_height }, { duration: 200, easing: 'linear' });
				max_height = -1;
			};

			var slider_options 	= JSON.parse( slider_selector.attr('data-image_carousel') );

			/* Execute when slick initialize */
			slider_selector.on('init', adaptiveImageHeight );

			$scope.imagesLoaded( function(e) {

				slider_selector.slick(slider_options);

				/* After slick slide change */
				slider_selector.on('afterChange', adaptiveImageHeight );

				slider_selector.find('.uael-grid-item').resize( function() {
					// Manually refresh positioning of slick
					setTimeout(function() {
						slider_selector.slick('setPosition');
					}, 300);
				});
			});
		}


		/* Grid */
		if ( ! isElEditMode ) {

			var selector = $scope.find( '.uael-img-grid-masonry-wrap' );

			if ( selector.length < 1 ) {
				return;
			}

			if ( ! ( selector.hasClass('uael-masonry') || selector.hasClass('uael-cat-filters') ) ) {
				return;
			}

			var layoutMode = 'fitRows';
			var filter_cat;

			if ( selector.hasClass('uael-masonry') ) {
				layoutMode = 'masonry';
			}

			var filters = $scope.find('.uael-masonry-filters');
			var def_cat = '*';

			if( '' !== id && sanitize_input ) {
				var select_filter = filters.find("[data-filter='" + '.' + id.toLowerCase() + "']");

				if ( select_filter.length > 0 ) {
					def_cat 	= '.' + id.toLowerCase();
					select_filter.siblings().removeClass('uael-current');
					select_filter.addClass('uael-current');
				}
			}

			if ( filters.length > 0 ) {

				var def_filter = filters.attr('data-default');

				if ( '' !== def_filter ) {

					def_cat 	= def_filter;
					def_cat_sel = filters.find('[data-filter="'+def_filter+'"]');

					if ( def_cat_sel.length > 0 ) {
						def_cat_sel.siblings().removeClass('uael-current');
						def_cat_sel.addClass('uael-current');
					}
				}
			}

			if ( $justified_selector.length > 0 ) {
				$justified_selector.imagesLoaded( function() {
				})
				.done(function( instance ) {
					$justified_selector.justifiedGallery({
						filter: def_cat,
						rowHeight : row_height,
						lastRow : lastrow,
						selector : 'div',
					});
				});
			} else {
				var masonaryArgs = {
					// set itemSelector so .grid-sizer is not used in layout
					filter 			: def_cat,
					itemSelector	: '.uael-grid-item',
					percentPosition : true,
					layoutMode		: layoutMode,
					hiddenStyle 	: {
						opacity 	: 0,
					},
				};

				var $isotopeObj = {};

				$scope.imagesLoaded( function(e) {
					$isotopeObj = selector.isotope( masonaryArgs );
				});
			}

			// bind filter button click
			$scope.on( 'click', '.uael-masonry-filter', function() {

				var $this 		= $(this);
				var filterValue = $this.attr('data-filter');

				$this.siblings().removeClass('uael-current');
				$this.addClass('uael-current');

				if( '*' === filterValue ) {
					filter_cat = $scope.find('.uael-img-gallery-wrap').data('filter-default');
				} else {
					filter_cat = filterValue.substr(1);
				}

				if( $scope.find( '.uael-masonry-filters' ).data( 'default' ) ){
					var def_filter = $scope.find( '.uael-masonry-filters' ).data( 'default' );
				}
				else{
					var def_filter = '.' + $scope.find('.uael-img-gallery-wrap').data( 'filter-default' );
				}
				var ajax_str_img_text = $scope.find( '.uael-masonry-filters-wrapper .uael-current' ).text(),
				ajax_str_filter_text  = $scope.find( '.uael-filters-dropdown-list .uael-current' ).text(),
				url                   = window.location.hash.replace( '#', '' ),
				str_replace_text      = ajax_str_img_text.replace( ajax_str_filter_text,'' ),
				str_cat_text          = ajax_str_img_text.replace( str_replace_text,'' );
				if( ( !url && ( window.screen.availWidth > 768 ) ) || url ){
					str_cat_text = ajax_str_img_text.replace( ajax_str_filter_text,'' );
				}
				if( url && ( window.screen.availWidth < 768 ) ){
					str_cat_text = ajax_str_img_text.replace( str_replace_text,'' );
				}
				$scope.find( '.uael-filters-dropdown-button' ).text( str_cat_text );

				if ( $justified_selector.length > 0 ) {
					$justified_selector.justifiedGallery({
						filter: filterValue,
						rowHeight : row_height,
						lastRow : lastrow,
						selector : 'div',
					});
				} else {
					$isotopeObj.isotope({ filter: filterValue });
				}
			});
			if( $scope.find( '.uael-masonry-filters' ).data( 'default' ) ){
				var def_filter = $scope.find( '.uael-masonry-filters' ).data( 'default' );
			}
			else{
				var def_filter = '.' + $scope.find('.uael-img-gallery-wrap').data( 'filter-default' );
			}

			var str_img_text = $scope.find( '.uael-filters-dropdown-list .uael-current' ).text();
			$scope.find( '.uael-filters-dropdown-button' ).text( str_img_text );
		}
	};

	UAELVideo = {

		/**
		 * Auto Play Video
		 *
		 */

		_play: function( selector,outer_wrap ) {

			var iframe 		= $( "<iframe/>" );
			var vurl 		= selector.data( 'src' );

	        if ( 0 == selector.find( 'iframe' ).length ) {

				if( outer_wrap.hasClass( 'uael-video-type-vimeo' ) || outer_wrap.hasClass( 'uael-video-type-youtube' ) || outer_wrap.hasClass( 'uael-video-type-wistia' ) ){
					iframe.attr( 'src', vurl );
				}
				iframe.attr( 'frameborder', '0' );
				iframe.attr( 'allowfullscreen', '1' );
				iframe.attr( 'allow', 'autoplay;encrypted-media;' );
				selector.html( iframe );
				if( outer_wrap.hasClass( 'uael-video-type-hosted' ) ) {
					var hosted_video_html = JSON.parse( outer_wrap.data( 'hosted-html' ) );
					iframe.ready( function() {
						var hosted_video_iframe = iframe.contents().find( 'body' ).css( {"margin":"0px"} );
						hosted_video_iframe.html( hosted_video_html );
						iframe.contents().find( 'video' ).css( {"width":"100%", "height":"100%"} );
						iframe.contents().find( 'video' ).attr( 'autoplay','autoplay' );
					});
				}

	        }

	        selector.closest( '.uael-video__outer-wrap' ).find( '.uael-vimeo-wrap' ).hide();
		}
	}

	UAELEffects = {

		_animateHeadline : function( $headlines, $widget_data ) {

			$headlines.each( function() {

        		var headline = $( this );
        		var speed    = $widget_data.data( 'speed' );

      			setTimeout( function()
	    			{
	    				UAELEffects._hideWord ( headline.find( '.uael-active-heading' ), $widget_data );
	      			},

      			speed );
	    	});
		},

		_hideWord : function ( $word, $widget_data ) {

			var nextWord = UAELEffects._takeNext( $word );
			var animation = $widget_data.data( 'animation' );
			var speed = $widget_data.data( 'speed' );

			if( 'clip' == animation ){

				var clip_speed = $widget_data.data( 'clip_speed' );
				var pause_time = $widget_data.data( 'pause_time' );

				$word.parents( '.uael-slide-main_ul' ).animate(
					{ width : '0px' },
					clip_speed, function(){
						setTimeout( function(){

							UAELEffects._switchWord( $word, nextWord );
							UAELEffects._showWord( nextWord, $widget_data );

						}, pause_time);
					}
				 );
			} else {

				UAELEffects._switchWord( $word, nextWord );

				setTimeout( function()
				   	{
						UAELEffects._hideWord( nextWord, $widget_data )
				   	},
				speed );
			}
		},

		_takeNext: function( $word ) {
			return ( !$word.is( ':last-child' ) ) ? $word.next() : $word.parent().children().eq( 0 );
		},

		_switchWord: function( $oldWord, $newWord ) {

			$oldWord.removeClass( 'uael-active-heading' ).addClass( 'uael-inactive-heading' );
			$newWord.removeClass( 'uael-inactive-heading' ).addClass( 'uael-active-heading' );
		},

		_showWord: function( $word, $widget_data ) {

			var animation = $widget_data.data( 'animation' );

			if( 'clip' == animation ) {

				var clip_speed = $widget_data.data( 'clip_speed' );
				var pause_time = $widget_data.data( 'pause_time' );

				$word.parents( '.uael-slide-main_ul' ).animate(
					{ 'width' : $word.width() + 3 },
					clip_speed,
					function(){
						setTimeout( function()
							{
								UAELEffects._hideWord( $word, $widget_data )
							},
						pause_time );
					}
				);
			}
		}
	}

	/*
	* Video handler Function.
	*
	*/
	var WidgetUAELVideoHandler = function( $scope, $ ) {

		if ( 'undefined' == typeof $scope ) {
			return;
		}

		var outer_wrap = $scope.find( '.uael-video__outer-wrap' );
		var inner_wrap = $scope.find( '.uael-video-inner-wrap' );
		var sticky_desktop = outer_wrap.data( 'hidedesktop' );
		var sticky_tablet = outer_wrap.data( 'hidetablet' );
		var sticky_mobile = outer_wrap.data( 'hidemobile' );
		var sticky_margin_bottom = outer_wrap.data( 'stickybottom' );
		var viewport = outer_wrap.data('vsticky-viewport');
		var is_lightbox = outer_wrap.hasClass( 'uael-video-play-lightbox' );

		outer_wrap.off( 'click' ).on( 'click', function( e ) {
			if( 'yes' == outer_wrap.data( 'vsticky' ) ) {
				var sticky_target = e.target.className;

				if( ( sticky_target.toString().indexOf( 'uael-sticky-close-icon' ) >= 0 ) || ( sticky_target.toString().indexOf( 'uael-video-sticky-close' ) >= 0 ) ) {
					return false;
				}
			}
			var selector = $( this ).find( '.uael-video__play' );

			if( ! is_lightbox ) {
				UAELVideo._play( selector, outer_wrap );
			}

		});

		if( ( '1' == outer_wrap.data( 'autoplay' ) || true == outer_wrap.data( 'device' ) ) && ( ! is_lightbox ) ) {

			UAELVideo._play( $scope.find( '.uael-video__play' ), outer_wrap );
		}

		if( 'yes' == outer_wrap.data( 'vsticky' ) ) {

			if( typeof elementorFrontend.waypoint !== 'undefined' ) {
				var uael_waypoint = elementorFrontend.waypoint(
					outer_wrap,
					function ( direction ) {
						if ( 'down' === direction ) {
							outer_wrap.removeClass( 'uael-sticky-hide' );
							outer_wrap.addClass( 'uael-sticky-apply' );
							$( document ).trigger( 'uael_after_sticky_applied', [ $scope ] );
						} else {
							outer_wrap.removeClass( 'uael-sticky-apply' );
							outer_wrap.addClass( 'uael-sticky-hide' );
						}
					},
					{ offset: viewport + '%', triggerOnce: false }
				);
			}

			var close_button = $scope.find( '.uael-video-sticky-close' );
			close_button.off( 'click.closetrigger' ).on( 'click.closetrigger', function(e) {
				uael_waypoint[0].disable();
				outer_wrap.removeClass( 'uael-sticky-apply' );
				outer_wrap.removeClass( 'uael-sticky-hide' );
			});
			checkResize( uael_waypoint );
			checkScroll();

			window.addEventListener( "scroll", checkScroll );
			$( window ).on( 'resize', function( e ) {
				checkResize( uael_waypoint );
			} );

		}

		function checkResize( uael_waypoint ) {
			var currentDeviceMode = elementorFrontend.getCurrentDeviceMode();

	  		if( ( '' !== sticky_desktop && currentDeviceMode == sticky_desktop ) || ( '' !== sticky_tablet && currentDeviceMode == sticky_tablet ) || ( '' !== sticky_mobile && currentDeviceMode == sticky_mobile ) ) {
  				disableSticky( uael_waypoint );
	  		} else {
	  			uael_waypoint[0].enable();
	  		}
		}

		function disableSticky( uael_waypoint ) {
			uael_waypoint[0].disable();
			outer_wrap.removeClass( 'uael-sticky-apply' );
			outer_wrap.removeClass( 'uael-sticky-hide' );
		}

		function checkScroll() {
			if( !isElEditMode && outer_wrap.hasClass( 'uael-sticky-apply' ) ){
				inner_wrap.draggable({ start: function() {
					$( this ).css({ transform: "none", top: $( this ).offset().top + "px", left: $( this ).offset().left + "px" });
					},
					containment: 'window'
				});
			}
		}

		$( document ).on( 'uael_after_sticky_applied', function( e, $scope ) {
			var infobar = $scope.find( '.uael-video-sticky-infobar' );

			if( 0 !== infobar.length ) {
				var infobar_height = infobar.outerHeight();

				if( $scope.hasClass( 'uael-video-sticky-center_left' ) || $scope.hasClass( 'uael-video-sticky-center_right' ) ) {
					infobar_height = Math.ceil( infobar_height / 2 );
					inner_wrap.css( 'top', 'calc( 50% - ' + infobar_height + 'px )' );
				}

				if( $scope.hasClass( 'uael-video-sticky-bottom_left' ) || $scope.hasClass( 'uael-video-sticky-bottom_right' ) ) {
					if( '' !== sticky_margin_bottom ) {
						infobar_height = Math.ceil( infobar_height );
						var stick_bottom = infobar_height + sticky_margin_bottom;
						inner_wrap.css( 'bottom', stick_bottom );
					}
				}
			}
		});

		// Fix the black border around video on safari.
		$(window).on('load', function (){
			setTimeout(function () {
				var videoFrame = $('.uael-video__outer-wrap iframe');
				if( videoFrame.length ){
					var bodyInFrame = videoFrame.contents().find('body');
					bodyInFrame.css('margin', '0');
				}
			}, 1000);
		});
	};

	/*
	 * Login Form handler Function.
	 *
	 */
	var WidgetUAELLoginFormHandler = function( $scope, $ ) {

		if ( 'undefined' == typeof $scope ) {
			return;
		}

		var scope_id = $scope.data( 'id' );
		var ajaxurl = uael_script.ajax_url;
		var widget_wrapper = $scope.find( '.uael-login-form-wrapper' );
		var form_wrapper = widget_wrapper.find( '.uael-form' );
		var submit_button = $scope.find( '.uael-login-form-submit' );
		var submit_text = submit_button.find( '.elementor-button-text' );

		var username = $scope.find( '.uael-login-form-username' );
		var password = $scope.find( '.uael-login-form-password' );
		var rememberme = $scope.find( '.uael-login-form-remember' );

		var facebook_button = $scope.find( '.uaelFacebookContentWrapper' );
		var facebook_text = facebook_button.find('.uael-facebook-text');

		var google_button = $scope.find( '.uaelGoogleContentWrapper' );

		var redirect_url =  $scope.find( '.uael-login-form-wrapper' ).data( 'redirect-url' );
		var send_email = $scope.find( '.uael-login-form-social-wrapper' ).data( 'send-email' );

		var nonce = $scope.find('.uael-login-form-wrapper' ).data('nonce');

		var ajax_enable = submit_button.data( 'ajax-enable' );
		var toggle_password = $scope.find('.toggle-password');

		$scope.find( '.elementor-field' ).on( 'keyup', function( e ) {
			$( this ).siblings( '.uael-register-field-message' ).remove();
		});

		$scope.find( '.uael-password-wrapper' ).on( 'keyup',function(e) {
			$( this ).next( '.uael-register-field-message' ).remove();
		});

		toggle_password.on( 'click', function(){
			var $this = $( this );
			$this.toggleClass('fa-eye fa-eye-slash');
			var input = $this.parent().find('input');
			if (input.attr('type') == 'password') {
				input.attr('type', 'text');
			} else {
				input.attr('type', 'password');
			}
		});

		/**
		 * Validate form on submit button click.
		 *
		 */
		submit_button.on( 'click', function() {

			var invalid_field = false;
			var error_exists = $scope.find( '.uael-loginform-error' );

			if( '' === username.val() ) {
				_printErrorMessages( $scope, username, uael_login_form_script.required );
				invalid_field = true;
			}

			if( '' === password.val() ) {
				_printErrorMessages( $scope, password, uael_login_form_script.required );
				invalid_field = true;
			}

			if( ! elementorFrontend.isEditMode() ) {

				if( 'yes' === ajax_enable ) {

					event.preventDefault();

					if( ! invalid_field && error_exists.length === 0 ) {

						var data = {
							'username'  : username.val(),
							'password' : password.val(),
							'rememberme' : rememberme.val()
						};

						$.post( ajaxurl, {
							action: 'uael_login_form_submit',
							data: data,
							nonce: nonce,
							method: 'post',
							dataType: 'json',
							beforeSend: function () {

								form_wrapper.animate({
									opacity: '0.45'
								}, 500 ).addClass( 'uael-form-waiting' );

								if( ! submit_text.hasClass( 'disabled' ) ) {
									submit_text.addClass( 'disabled' );
									submit_text.append( '<span class="uael-form-loader"></span>' );
								}
							},
						}, function ( response ) {

							form_wrapper.animate({
								opacity: '1'
							}, 100 ).removeClass( 'uael-form-waiting' );

							submit_text.find( '.uael-form-loader' ).remove();
							submit_text.removeClass( 'disabled' );

							if ( true === response.success ) {
								$scope.find( '.uael-register-field-message' ).remove();
								$scope.find( '.uael-form' ).trigger( 'reset' );
								if( undefined === redirect_url ) {
									location.reload();
								} else {
									window.location = redirect_url;
								}
							} else if ( false === response.success && 'incorrect_password' === response.data ) {
								_printErrorMessages( $scope, password, uael_login_form_script.incorrect_password );
							} else if ( false === response.success && 'invalid_username' === response.data ) {
								_printErrorMessages( $scope, username, uael_login_form_script.invalid_username );
							} else if ( false === response.success && 'invalid_email' === response.data ) {
								_printErrorMessages( $scope, username, uael_login_form_script.invalid_email );
							}

						});
					}
				} else {

					if( ! invalid_field && error_exists.length === 0 ) {
						form_wrapper.animate({
							opacity: '0.45'
						}, 500 ).addClass( 'uael-form-waiting' );

						if( ! submit_text.hasClass( 'disabled' ) ) {
							submit_text.addClass( 'disabled' );
							submit_text.append( '<span class="uael-form-loader"></span>' );
						}
						return true;
					} else {
						return false;
					}
				}
			}

		});

		/**
		 * Display error messages
		 *
		 */
		function _printErrorMessages( $scope, form_field, message ) {

			var field_type = form_field.attr( 'name' );
			var password_wrapper = $scope.find( '.uael-password-wrapper' );
			var is_password_error = password_wrapper.next().hasClass( 'uael-register-field-message' );
			var $is_error = form_field.next().hasClass( 'uael-register-field-message' );

				switch( field_type ) {
					case 'password':
						if( is_password_error ) {
							return;
						} else {
							password_wrapper.after( '<span class="uael-register-field-message"><span class="uael-loginform-error">' + message + '</span></span>' );
						}
						break;
					default:
						if( $is_error ) {
							return;
						} else {
							form_field.after( '<span class="uael-register-field-message"><span class="uael-loginform-error">' + message + '</span></span>' );
						}
				}
		}

		if( ! elementorFrontend.isEditMode() ) {

			if( facebook_button.length > 0 ) {
				/**
				 * Login with Facebook.
				 *
				 */
				facebook_button.on( 'click', function() {

					if( ! is_fb_loggedin ) {
						FB.login ( function ( response ) {
					        if ( response.authResponse ) {
					            // Get and display the user profile data.
					            getFbUserData();
					        } else {
					           // $scope.find( '.status' ).addClass( 'error' ).text( 'User cancelled login or did not fully authorize.' );
					        }
					    }, { scope: 'email' } );
					}

				});

				// Fetch the user profile data from facebook.
				function getFbUserData() {
				    FB.api( '/me', { fields: 'id, name, first_name, last_name, email, link, gender, locale, picture' },
				    function ( response ) {

					 	var userID = FB.getAuthResponse()[ 'userID' ];
				    	var access_token = FB.getAuthResponse()[ 'accessToken' ];

				    	window.is_fb_loggedin = true;

				        var fb_data = {
							'id'  : response.id,
							'name' : response.name,
							'first_name' : response.first_name,
							'last_name' : response.last_name,
							'email' : response.email,
							'link' : response.link,
							'send_email' : send_email,
						};

				        $.post( ajaxurl, {
							action: 'uael_login_form_facebook',
							data: fb_data,
							nonce: nonce,
							method: 'post',
							dataType: 'json',
				        	userID : userID,
							security_string : access_token,
							beforeSend: function () {
								form_wrapper.animate({
										opacity: '0.45'
								}, 500 ).addClass( 'uael-form-waiting' );

								if( ! facebook_text.hasClass( 'disabled' ) ) {
									facebook_text.addClass( 'disabled' );
									facebook_text.append( '<span class="uael-form-loader"></span>' );
								}
							}
						}, function ( data ) {
							if( data.success === true ) {

								form_wrapper.animate({
									opacity: '1'
								}, 100 ).removeClass( 'uael-form-waiting' );

								facebook_text.find( '.uael-form-loader' ).remove();
								facebook_text.removeClass( 'disabled' );

								$scope.find( '.status' ).addClass( 'success' ).text( uael_login_form_script.logged_in_message + response.first_name + '!' );
								if( undefined === redirect_url ) {
									location.reload();
								} else {
									window.location = redirect_url;
								}
							} else {
								location.reload();
							}
						});

				    });

				}

				window.fbAsyncInit = function() {
					var app_id = facebook_button.data( 'appid' );
				    // FB JavaScript SDK configuration and setup.
				    FB.init({
				      appId      : app_id, // FB App ID.
				      cookie     : true,  // enable cookies to allow the server to access the session.
				      xfbml      : true,  // parse social plugins on this page.
				      version    : 'v2.8' // use graph api version 2.8.
				    });
				};

				// Load the JavaScript SDK asynchronously.
				( function( d, s, id ) {

				    var js, fjs = d.getElementsByTagName( s )[0];
				    if ( d.getElementById( id ) ) {
				    	return;
				    }
				    js = d.createElement( s );
				    js.id = id;
				    js.src = "//connect.facebook.net/en_US/sdk.js";
				    fjs.parentNode.insertBefore( js, fjs );
				} ( document, 'script', 'facebook-jssdk' ) );
			}

			if( google_button.length > 0 ) {

				var client_id = google_button.data( 'clientid' );
				var google_scope_id = document.getElementById( 'uael-google-login-' + scope_id );

				/**
				 * Login with Google.
				 *
				 */
				gapi.load( 'auth2', function() {
					// Retrieve the singleton for the GoogleAuth library and set up the client.
					auth2 = gapi.auth2.init({
					  client_id: client_id,
					  cookiepolicy: 'single_host_origin',
					});
				
					auth2.attachClickHandler(google_scope_id, {},
						function(googleUser) {
							var google_data = {
								'send_email': send_email,
							};
					
							if (window.is_google_loggedin) {
								var id_token = googleUser.getAuthResponse().id_token;
								var google_text = google_button.find('.uael-google-text');
						
									$.ajax({
										url: ajaxurl,
										method: 'post',
										dataType: 'json',
										data: {
											nonce: nonce,
											action: 'uael_login_form_google',
											data: google_data,
											id_token: id_token,
										},
										beforeSend: function() {
											form_wrapper.animate({
												opacity: '0.45'
											}, 500).addClass('uael-form-waiting');
							
											if (!google_text.hasClass('disabled')) {
												google_text.addClass('disabled');
												google_text.append('<span class="uael-form-loader"></span>');
											}
										},
										success: function(data) {
											if (data.success === true) {
												form_wrapper.animate({
													opacity: '1'
												}, 100).removeClass('uael-form-waiting');
											
												google_text.find('.uael-form-loader').remove();
												google_text.removeClass('disabled');
											
												$scope.find('.status').addClass('success').text(uael_login_form_script.logged_in_message + data.username + '!');
												
												if (typeof redirect_url === 'undefined') {
													location.reload();
												} else {
													window.location = redirect_url;
												}
											}
										}
								});
							}
					  },
					  function(error) {
						// Handle any errors here
					  }
					);
				});

			}
		}


		google_button.on( 'click', function() {
			window.is_google_loggedin = true;
		});

	};

	var WidgetUAELFAQHandler = function( $scope, $ ) {
		var uael_faq_answer = $scope.find('.uael-faq-accordion > .uael-accordion-content');
		var layout = $scope.find( '.uael-faq-container' );
		var faq_layout = layout.data('layout');
			$scope.find('.uael-accordion-title').on( 'click keypress',
				function(e){
					e.preventDefault();
					$this = $(this);
					uael_accordion_activate_deactivate($this);
				}
			);
			function uael_accordion_activate_deactivate($this) {
					if('toggle' == faq_layout ) {
						if( $this.hasClass('uael-title-active') ){
							$this.removeClass('uael-title-active');
							$this.attr('aria-expanded', 'false');
						}
						else{
							$this.addClass('uael-title-active');
							$this.attr('aria-expanded', 'true');
						}
						$this.next('.uael-accordion-content').slideToggle( 'normal','swing');
					}
					else if( 'accordion' == faq_layout ){
						if( $this.hasClass('uael-title-active') ){
							$this.removeClass( 'uael-title-active');
							$this.next('.uael-accordion-content').slideUp( 'normal','swing',
							    function(){
						    		$(this).prev().removeClass('uael-title-active');
									$this.attr('aria-expanded', 'false');
								});
						} else {
							if( uael_faq_answer.hasClass('uael-title-active') ){
								uael_faq_answer.removeClass('uael-title-active');
							}
						    uael_faq_answer.slideUp('normal','swing' , function(){
						    	$(this).prev().removeClass('uael-title-active');
						    });

						    $this.addClass('uael-title-active');
						    $this.next('.uael-accordion-content').slideDown('normal','swing', function(){
								$(this).prev().addClass('uael-title-active');
								$this.attr('aria-expanded', 'true');
							});
						}
				    return false;
					}
				}
	}

	/**
	 * Function for FF Styler select field.
	 *
	 */
	var WidgetUAELFFStylerHandler = function( $scope, $ ) {

		if ( 'undefined' == typeof $scope )
			return;

		$scope.find('select:not([multiple])').each(function() {
			var	gfSelectField = $( this );
			gfSelectField.wrap( "<span class='uael-ff-select-custom'></span>" );
		});
	}

	/**
	 * Welcome Music handler Function.
	 */
	var WidgetUAELWelcomeMusicHandler = function ($scope, $){
		if ( 'undefined' == typeof $scope ) {
			return;
		}

		var track          = $scope.find( '.uael-welcome-track' );
		var musicContainer = $scope.find( '.uael-welcome-music-container' );
		var autoplay       = ( track.length > 0 ) ? track.data( 'autoplay' ) : '';
		var musicVolume    = musicContainer.data( 'volume' );
		var audio          = ( track.length > 0 ) ? track[0] : '';
		var playPauseBtn   = $scope.find( '#uael-play-pause' );
		var play           = playPauseBtn.find( '.play' );
		var pause          = playPauseBtn.find( '.pause' );

		if ( autoplay ) {
			var playPromise = audio.play();
			if ( playPromise ) {
				playPromise.catch( ( e ) => {
					if ( e.name === 'NotAllowedError' || e.name === 'NotSupportedError' ) {
						playPauseBtn.toggleClass( 'uael-pause' );
						playPauseBtn.toggleClass( 'uael-play' );
					}
				}).then( () => {
					playPauseBtn.toggleClass( 'uael-play' );
					playPauseBtn.toggleClass( 'uael-pause' );
				});
			}
		}

		playPauseBtn.on(
			'click',
			function (){
				var $this = $( this );
				if ( $this.hasClass( 'uael-play' ) ) {
					audio.play();
					$this.toggleClass( 'uael-play' );
					$this.toggleClass( 'uael-pause' );

				} else {
					audio.pause();
					$this.toggleClass( 'uael-pause' );
					$this.toggleClass( 'uael-play' );
				}
			}
		);

		$( '.uael-welcome-track' ).on(
			'ended',
			function() {
				playPauseBtn.toggleClass( 'uael-pause' );
				play.css( 'display', 'block' );
				playPauseBtn.toggleClass( 'uael-play' );
				pause.css( 'display', 'none' );
			}
		);

		if ( ! isNaN( musicVolume ) && '' !== musicVolume && undefined !== musicVolume && '' !== audio ) {
			audio.volume = parseFloat( musicVolume / 100 );
		}

	}

	/**
	 * Instagram Feed handler Function.
	 */
	var WidgetUAELInstagramFeedHandler = function ( $scope, $ ) {
		var widgetId		= $scope.data( 'id' ),
			elementSettings = getWidgetSettings( $scope ),
			feed            = $scope.find( '.uael-instagram-feed' ).eq( 0 ),
			layout          = elementSettings.uae_insta_layout_type;

		if ( ! feed.length ) {
			return;
		}

		if ( layout === 'masonry' ) {
			var grid = $( '#uael-instafeed-' + widgetId ).imagesLoaded( function() {
				grid.masonry(
					{
					itemSelector:    '.uael-feed-item',
					percentPosition: true
				});
			});
		}
	}

	/**
	 * Twitter Feed handler Function.
	 */
	var WidgetUAELTwitterFeedHandler = function ($scope, $){
		/* Carousel */
		var slider_selector	= $scope.find('.uael-twitter-feed-carousel');
		if ( slider_selector.length > 0 ) {

			var adaptiveImageHeight = function( e, obj ) {
				var node = obj.$slider,
				post_active = node.find('.slick-slide.slick-active'),
				max_height = -1;

				post_active.each(function( i ) {
					var $this = $( this ),
					this_height = $this.innerHeight();
					if( max_height < this_height ) {
						max_height = this_height + 50;
					}
				});

				node.find('.slick-list.draggable').animate({ height: max_height }, { duration: 200, easing: 'linear' });
				max_height = -1;
			};

			var slider_options 	= JSON.parse( slider_selector.attr('data-twitter_carousel_settings') );
			/* Execute when slick initialize */
			slider_selector.on('init', adaptiveImageHeight );
			$scope.imagesLoaded( function(e) {

				slider_selector.slick(slider_options);

				/* After slick slide change */
				slider_selector.on('afterChange', adaptiveImageHeight );
				var slider_items = slider_selector.find( '.uael-twitter-feed-item' );
				slider_items.on( 'resize', function() {
					// Manually refresh positioning of slick
					setTimeout(function() {
						slider_selector.slick( 'setPosition' );
					}, 300);
				});
			});
		}
	}

	$( window ).on( 'elementor/frontend/init', function () {

		var elementor_elements = ['widget', 'section', 'column', 'container'];

		if ( elementorFrontend.isEditMode() ) {
			isElEditMode = true;
		}

		elementorFrontend.hooks.addAction( 'frontend/element_ready/uael-fancy-heading.default', WidgetUAELFancyTextHandler );

		elementorFrontend.hooks.addAction( 'frontend/element_ready/uael-ba-slider.default', WidgetUAELBASliderHandler );

		elementorFrontend.hooks.addAction( 'frontend/element_ready/uael-hotspot.default', WidgetUAELHotspotHandler );

		elementorFrontend.hooks.addAction( 'frontend/element_ready/uael-timeline.default', WidgetUAELTimelineHandler );

		elementorFrontend.hooks.addAction( 'frontend/element_ready/uael-content-toggle.default', WidgetUAELContentToggleHandler );

		elementorFrontend.hooks.addAction( 'frontend/element_ready/uael-gf-styler.default', WidgetUAELGFStylerHandler );

		elementorFrontend.hooks.addAction( 'frontend/element_ready/uael-cf7-styler.default', WidgetUAELCF7StylerHandler );

		elementorFrontend.hooks.addAction( 'frontend/element_ready/uael-image-gallery.default', WidgetUAELImageGalleryHandler );

		elementorFrontend.hooks.addAction( 'frontend/element_ready/uael-video.default', WidgetUAELVideoHandler );

		elementorFrontend.hooks.addAction( 'frontend/element_ready/uael-video-gallery.default', WidgetUAELVideoGalleryHandler );

		elementorFrontend.hooks.addAction( 'frontend/element_ready/uael-caf-styler.default', WidgetUAELCafStylerHandler );

		elementorFrontend.hooks.addAction( 'frontend/element_ready/uael-login-form.default', WidgetUAELLoginFormHandler );

		elementorFrontend.hooks.addAction( 'frontend/element_ready/uael-faq.default', WidgetUAELFAQHandler );

		elementorFrontend.hooks.addAction( 'frontend/element_ready/uael-ff-styler.default', WidgetUAELFFStylerHandler );

		elementorFrontend.hooks.addAction( 'frontend/element_ready/uael-price-table.default', WidgetUAELPriceTableHandler );

		elementorFrontend.hooks.addAction( 'frontend/element_ready/uael-welcome-music.default', WidgetUAELWelcomeMusicHandler );

		elementorFrontend.hooks.addAction( 'frontend/element_ready/uael-instagram-feed.default', WidgetUAELInstagramFeedHandler );

		elementorFrontend.hooks.addAction( 'frontend/element_ready/uael-twitter.default', WidgetUAELTwitterFeedHandler );

		if( isElEditMode ) {

			elementor.channels.data.on( 'element:after:duplicate element:after:remove', function( e, arg ) {
				$( '.elementor-widget-uael-ba-slider' ).each( function() {
					WidgetUAELBASliderHandler( $( this ), $ );
				} );
			} );

			elementor_elements.forEach(element => {
				elementor.hooks.addAction( 'panel/open_editor/' + element, function( panel, model, view ) {
					var settings_panel = panel.$el;
					settings_panel.on( 'change', '[data-setting="display_condition_enable"]', function( event ) {

						if ( $( this ).is( ':checked' ) ) {
							var GetLocalTimeZone = new Date().getTimezoneOffset();
							GetLocalTimeZone = GetLocalTimeZone == 0 ? 0 : -GetLocalTimeZone;
							var uael_secure = ( document.location.protocol === 'https:' ) ? 'secure' : '';
							document.cookie = "GetLocalTimeZone=" + GetLocalTimeZone + ";SameSite=Strict;" + uael_secure;
						} else {
							document.cookie = "GetLocalTimeZone= ; expires = Thu, 01 Jan 1970 00:00:00 GMT"
						}

					} );
				} );
			});
		}

	});

} )( jQuery );

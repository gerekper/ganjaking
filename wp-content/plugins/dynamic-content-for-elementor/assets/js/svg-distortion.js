(
	function ( $ ) {
		var WidgetElements_SvgDistortionHandler = function ( $scope,$ ) {
			var elementSettings = dceGetElementSettings( $scope );
			var id_scope = $scope.attr( 'data-id' );
			var image_url = $scope.find( '.dce_distortion' ).attr( 'data-dispimage' );

			if ( ! image_url ) {
				return;
			}

			// Il filtro displacement
			var feDisp = $scope.find( 'feDisplacementMap#displacement-map' )[ 0 ];
			// L'immagine di distorsione
			var feImage = $scope.find( 'feImage#displacement-image' )[ 0 ];

			var scaleMap = elementSettings.disp_factor.size;
			var scaleImage = elementSettings.disp_scale.size + '%';
			var posImage = (
				               (
					               100 - (
						               Number( elementSettings.disp_scale.size )
					               )
				               ) / 2
			               ) + '%';

			var random_animation = false;
			var random_animation_range = 0;

			//in caso di rollover e scroll ho i valori di arrivo..
			if ( elementSettings.svg_trigger == 'rollover' || elementSettings.svg_trigger == 'scroll' ) {
				var scaleMapTo = elementSettings.disp_factor_to.size || 0;
				var scaleImageTo = elementSettings.disp_scale_to.size + '%' || '100%';
				var posImageTo = (
					                 (
						                 100 - (
							                 Number( elementSettings.disp_scale_to.size )
						                 )
					                 ) / 2
				                 ) + '%' || '0%';
			}
			// quando è animato e considero il valore random
			if ( elementSettings.svg_trigger == 'animation' ) {
				random_animation = Boolean( elementSettings.random_animation );

				if ( random_animation ) {
					random_animation_range = Number( elementSettings.random_animation_range.size );
					var scaleMap_rand_min = Number( scaleMap - random_animation_range );
					var scaleMap_rand_max = Number( scaleMap + random_animation_range );

					// parto dal valore 1 impostato
					var random_val_1 = scaleMap;
					// passo ad un valore casuale compreso nel range
					var random_val_2 = getRandomValue( scaleMap_rand_min,scaleMap_rand_max );
				}

			}
			// per tutte le animazioni
			if ( elementSettings.svg_trigger != 'static' ) {
				var animation_delay = elementSettings.delay_animation.size || 1;
				var animation_speed = elementSettings.speed_animation.size || 3;
				var easing_animation_ease = elementSettings.easing_animation_ease || 'Power3';
				var easing_animation = elementSettings.easing_animation || 'easeInOut';
				var easeFunction = easing_animation_ease + '.' + easing_animation;
			}
			// in caso di animation vado da zero(0) al valore di partenza...

			// in caso di random vado dal valore di partenza ad un valore a caso vicino (considero un range)

			var run = $( '#dce-svg-' + id_scope ).attr( 'data-run' );

			// pulisco tutto
			if ( elementorFrontend.isEditMode() ) {
				if ( tl ) tl.kill( feDisp );
				if ( tli ) tli.kill( feImage );

				$( '.elementor-element[data-id=' + id_scope + '] svg, .' + elementSettings.id_svg_class +
				   ' a' ).off( 'mouseenter' );
				$( '.elementor-element[data-id=' + id_scope + '] svg, .' + elementSettings.id_svg_class +
				   ' a' ).off( 'mouseleave' );
				$( '.elementor-element[data-id=' + id_scope + '] svg, .' + elementSettings.id_svg_class +
				   ' a' ).off( 'touchstart' );
				$( '.elementor-element[data-id=' + id_scope + '] svg, .' + elementSettings.id_svg_class +
				   ' a' ).off( 'touchend' );
			}

			var tl = new gsap.timeline( { repeat:-1,repeatDelay:animation_delay } );
			var tli = new gsap.timeline( { repeat:-1,repeatDelay:animation_delay } );

			var ferma = function () {
				tl.pause();
				tli.pause();
			};
			var riproduci = function () {
				tl.play();
				tli.play();
			};

			var playShapeEl = function () {

				function repeatOften() {

					if ( run != $( '#dce-svg-' + id_scope ).attr( 'data-run' ) ) {

						run = $( '#dce-svg-' + id_scope ).attr( 'data-run' );
						if ( run == 'running' ) {
							riproduci();
						} else {
							ferma();
						}

					}

					requestAnimationFrame( repeatOften );

				}
				requestAnimationFrame( repeatOften );
			};
			// Animations
			var moveFnComplete = function () {
				random_val_1 = random_val_2;
				random_val_2 = getRandomValue( scaleMap_rand_min,scaleMap_rand_max );
				createAnimation( true );
			};

			function createAnimation( $random = false ) {

				if ( $random ) {
					tl = new gsap.timeline( { repeat:0 } );

					tl.to(
						feDisp,
						{
							duration:animation_speed,
							onComplete:moveFnComplete,
							attr:{
								scale:random_val_1
							},
							ease:easeFunction
						},
						0
					).to(
						feDisp,
						{
							duration:animation_speed,
							attr:{
								scale:random_val_2
							},
							ease:easeFunction
						},
						animation_speed
					);
				} else {
					tl.to(
						feDisp,
						{
							duration:animation_speed,
							attr:{
								scale:0
							},
							ease:easeFunction
						},
						0
					).to(
						feDisp,
						{
							duration:animation_speed,
							attr:{
								scale:scaleMap
							},
							ease:easeFunction
						},
						animation_speed
					);
				}
				if ( run == 'paused' && elementorFrontend.isEditMode() ) {
					ferma();
				} else {
					riproduci();
				}

			}

			var mouseenterFn = function () {

				tl = new gsap.timeline( { repeat:0 } );
				tli = new gsap.timeline( { repeat:0 } );

				tl.to(
					feDisp,
					{
						duration:animation_speed,
						attr:{
							scale:scaleMapTo
						},
						ease:easeFunction
					},
					0
				);
				tli.to(
					feImage,
					{
						duration:animation_speed,
						attr:{
							x:posImageTo,
							y:posImageTo,
							width:scaleImageTo,
							height:scaleImageTo
						},
						ease:easeFunction
					},
					0
				);
			};
			var mouseleaveFn = function () {
				tl = new gsap.timeline( { repeat:0 } );
				tli = new gsap.timeline( { repeat:0 } );

				tl.to(
					feDisp,
					{
						duration:animation_speed,
						attr:{
							scale:scaleMap
						},
						ease:easeFunction
					},
					0
				);
				tli.to(
					feImage,
					{
						duration:animation_speed,
						attr:{
							x:posImage,
							y:posImage,
							width:scaleImage,
							height:scaleImage
						},
						ease:easeFunction
					},
					0
				);
			};
			// Scroll
			var active_scrollAnalysi = function ( $el ) {
				if ( $el ) {

					tl = new gsap.timeline( { repeat:0,paused:true, } );

					var runAnim = function ( dir ) {
						if ( dir == 'down' ) {

							tl.to(
								feDisp,
								{
									duration:animation_speed,
									attr:{
										scale:scaleMapTo
									},
									ease:easeFunction
								},
								animation_delay
							);
							tli.to(
								feImage,
								{
									duration:animation_speed,
									attr:{
										x:posImageTo,
										y:posImageTo,
										width:scaleImageTo,
										height:scaleImageTo
									},
									ease:easeFunction
								},
								animation_delay
							);
							tl.restart();
							tli.restart();
						} else if ( dir == 'up' ) {

							tl.to(
								feDisp,
								{
									duration:animation_speed,
									attr:{ scale:scaleMap },
									ease:easeFunction
								},
								animation_delay
							);
							tli.to(
								feImage,
								{
									duration:animation_speed,
									attr:{
										x:posImage,
										y:posImage,
										width:scaleImage,
										height:scaleImage
									},
									ease:easeFunction
								},
								animation_delay
							);
							tl.restart();
							tli.restart();
						}
					};
					var waypointOptions = {
						triggerOnce:false,
						continuous:true
					};
					elementorFrontend.waypoint( $( $el ),runAnim,waypointOptions );
				}
			};

			if ( elementSettings.svg_trigger == 'animation' ) {

				createAnimation( random_animation );
				if ( elementorFrontend.isEditMode() ) playShapeEl();

			} else if ( elementSettings.svg_trigger == 'rollover' ) {

				$( '.elementor-element[data-id=' + id_scope + '] svg, .' + elementSettings.id_svg_class +
				   ' a' ).on( 'mouseenter',mouseenterFn );
				$( '.elementor-element[data-id=' + id_scope + '] svg, .' + elementSettings.id_svg_class +
				   ' a' ).on( 'mouseleave',mouseleaveFn );
				$( '.elementor-element[data-id=' + id_scope + '] svg, .' + elementSettings.id_svg_class +
				   ' a' ).on( 'touchstart',mouseenterFn );
				$( '.elementor-element[data-id=' + id_scope + '] svg, .' + elementSettings.id_svg_class +
				   ' a' ).on( 'touchend',mouseleaveFn );

			} else if ( elementSettings.svg_trigger == 'scroll' ) {

				$( '#dce-svg-' + id_scope ).attr( 'data-run','paused' );

				active_scrollAnalysi( '#dce-svg-' + id_scope );
			}
			
			function getRandomValue( min,max ) {
				min = Math.ceil( min );
				max = Math.floor( max );
				return Math.floor( Math.random() * (
					max - min
				) ) + min; //Il max è escluso e il min è incluso

			}

		};

		$( window ).on( 'elementor/frontend/init',function () {
			elementorFrontend.hooks.addAction( 'frontend/element_ready/dyncontel-svgdistortion.default',WidgetElements_SvgDistortionHandler );
		} );
	}
)( jQuery );

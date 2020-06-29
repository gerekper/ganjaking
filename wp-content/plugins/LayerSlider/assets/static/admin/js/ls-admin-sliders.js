var importModalWindowTimeline = null,
	importModalWindowTransition = null,
	importModalThumbnailsTransition = null,

	draggedSliderItem = null,
	targetSliderItem = null,

	sliderDragGroupingTimeout = null,
	sliderGroupRenameTimeout = null,

	$lastOpenedGroup,

	shuffleContainers = []
	activeShuffleContainerIndex = 0;


jQuery(function($) {

	// Tabs
	$('.km-tabs').kmTabs();

	// Auto-submit filter/search bar when choosing different view mode
	// from drop-down menus.
	$('#ls-slider-filters').on('change', 'select', function() {
		$(this).closest('#ls-slider-filters').submit();
	});


	$('.ls-sliders-grid').on('contextmenu', '.preview', function( e ) {
		e.preventDefault();
		$(this).parent().find('.slider-actions-button').click();

	}).on('click', '.slider-item .checkbox', function() {

		$( this ).closest('.slider-item').toggleClass('ls-selected');
		checkSliderSelection();

	}).on('click', '.slider-item .preview', function( event ) {

		if( event.ctrlKey || event.metaKey ) {

			event.preventDefault();

			$( this ).closest('.slider-item').find('.checkbox').click();
		}


	}).on('click', '.slider-actions-button', function() {
		$(this).closest('.slider-item').addClass('ls-opened');


	}).on('click', '.slider-item.group-item', function( e ) {
		e.preventDefault();

		var $this 		= $( this ),
			groupName 	= $.trim( $this.find('.name').html() ).replace(/"/g, '&quot;');

		$lastOpenedGroup = $this;

		kmw.modal.open({
			into: '.ls-sliders-grid',
			title: '<input value="'+groupName+'"><a href="#" class="button button-primary ls-remove-group-button" data-help="'+LS_l10n.SLRemoveGroupTooltip+'" data-help-delay="100">'+LS_l10n.SLRemoveGroupButton+'</a>',
			content: $this.next().children(),
			maxWidth: 1380,
			minWidth: 600,
			modalClasses: 'ls-slider-group-modal-window',
			animationIn: 'scale',
			overlaySettings: {
				animationIn: 'fade'
			}
		});



		setTimeout( function() {
			removeSliderFromGroupDraggable();
		}, 200);

	});


	$( document ).on('input', '.ls-slider-group-modal-window .kmw-modal-title input', function() {

		$this = $( this );

		clearTimeout( sliderGroupRenameTimeout );
		sliderGroupRenameTimeout = setTimeout( function() {

			$.get( ajaxurl, {
				action: 'ls_rename_slider_group',
				groupId: $lastOpenedGroup.data('id'),
				name: $this.val()
			});

		}, 300 );


		$lastOpenedGroup.find('.name').text( $this.val() );
	});

	$( document ).on('click', '.ls-slider-group-modal-window .ls-remove-group-button', function( e) {

		e.preventDefault();
		kmUI.popover.close();


		setTimeout( function() {

			if( confirm( LS_l10n.SLRemoveGroupConfirm ) ) {

				$.get( ajaxurl, {
					action: 'ls_delete_slider_group',
					groupId: $lastOpenedGroup.data('id'),
				});

				var $sliders = $('.ls-slider-group-modal-window .slider-item');

				// Destroy previous draggable instance (if any)
				if( $sliders.hasClass('ui-draggable') ) {
					$sliders.draggable('destroy');
				}

				// Destroy previous droppable instance (if any)
				if( $sliders.hasClass('ui-droppable') ) {
					$sliders.droppable('destroy');
				}

				$sliders.insertAfter('.ls-sliders-grid .ls-grid-buttons');

				setTimeout( function() {
					addSliderToGroupDraggable();
					addSliderToGroupDroppable();

					createSliderGroupDroppable();
				}, 300 );


				$lastOpenedGroup.next().remove();
				$lastOpenedGroup.remove();

				kmw.modal.close();
			}

		}, 300 );
	});


	$('.ls-sliders-grid').on('mouseleave', '.slider-item', function() {
		$(this).closest('.slider-item').removeClass('ls-opened').removeClass('ls-export-options-open');


	// Add slider
	}).on('click', '#ls-add-slider-button', function(e) {
		e.preventDefault();

		var $button = $(this),
			$wrap 	= $button.closest('.slider-item-wrapper'),
			$sheet 	= $('#ls-add-slider-template');

		if( ! $sheet.length ) {
			$sheet = $( $('#tmpl-ls-add-slider-grid').text() ).appendTo( $wrap );
		}

		$sheet.find('input').focus();
		TweenLite.set( $sheet, { x: 235 });
		TweenLite.to( [ $button[0], $sheet[0] ], 0.5, {
			x: '-=235'
		});

	// Export options
	}).on('click', '.ls-export-options-button', function( e ) {
		e.preventDefault();
		$(this).closest('.slider-item').addClass('ls-export-options-open');
	});



	$('.ls-sliders-list').on('click', '#ls-add-slider-button', function(e) {
		e.preventDefault();

		var offsets = $(this).offset();
		var popup = $('#ls-add-slider-template-list').length ?
					$('#ls-add-slider-template-list') :
					$( $('#tmpl-ls-add-slider-list').html() ).prependTo('body');

		popup.css({
			top : offsets.top + 35,
			left : offsets.left - popup.outerWidth() / 2 + $(this).width() / 2 + 7
		}).show().animate({ marginTop : 0, opacity : 1 }, 150, function() {
			$(this).find('.inner input').focus();
		});

		$('<div>', { 'class' : 'ls-overlay dim'}).prependTo('body');


	}).on('click', '.slider-actions-button', function() {

		var $this = $(this);
		setTimeout(function() {
			var offsets = $this.position(),
				height 	= $('#ls-slider-actions-template').removeClass('ls-hidden').show().height();

			$('#ls-slider-actions-template').css({
				top : offsets.top + 15 - height / 2,
				right : 40,
				marginTop : 0,
				opacity : 1
			});

			$('#ls-slider-actions-template a:eq(0)').data('id', $this.data('id') );
			$('#ls-slider-actions-template a:eq(0)').data('slug', $this.data('slug') );

			$('#ls-slider-actions-template a:eq(1)').attr('href', $this.data('export-url') );
			$('#ls-slider-actions-template a:eq(2)').attr('href', $this.data('export-html-url') );
			$('#ls-slider-actions-template a:eq(3)').attr('href', $this.data('duplicate-url') );
			$('#ls-slider-actions-template a:eq(4)').attr('href', $this.data('revisions-url') );
			$('#ls-slider-actions-template a:eq(5)').attr('href', $this.data('remove-url') );


			setTimeout(function() {
				$('body').one('click', function() {
					$('#ls-slider-actions-template').addClass('ls-hidden');
				});
			}, 200);
		}, 100);
	});

	// Slider remove
	$('.ls-slider-list-form').on('click', 'a.remove', function(e) {
		e.preventDefault();
		if(confirm(LS_l10n.SLRemoveSlider)){
			document.location.href = $(this).attr('href');
		}


	// Upload
	}).on('click', '#ls-import-button', function(e) {
		e.preventDefault();

		kmw.modal.open({
			content: $('#tmpl-upload-sliders').text(),
			minWidth: 400,
			maxWidth: 700
		});


	// Embed
	}).on('click', 'a.embed', function(e) {
		e.preventDefault();

		var $this 	= $(this),
			$modal 	= kmw.modal.open({
				content: $('#tmpl-embed-slider').text(),
				minWidth: 400,
				maxWidth: 980
			}),
			id 		= $this.data('id'),
			slug 	= $this.data('slug') || id;



		$modal.find('input.shortcode').val('[layerslider id="'+slug+'"]');

		$('.km-accordion').kmAccordion();

	// HTML export
	}).on('click', 'a.ls-html-export', function( e ) {

		if( ! window.lsSiteActivation ) {
			e.preventDefault();

			lsDisplayActivationWindow();

			return false;
		}



		if( window.localStorage ) {

			if( ! localStorage.lsExportHTMLWarning ) {
				localStorage.lsExportHTMLWarning = 0;
			}

			var counter = parseInt( localStorage.lsExportHTMLWarning ) || 0;

			if( counter < 3 ) {

				localStorage.lsExportHTMLWarning = ++counter;

				if( ! confirm( LS_l10n.SLExportSliderHTML ) ) {
					e.preventDefault();
					return false;
				}
			}
		}
	});

	// Pagivation
	$('.pagination-links a.disabled').click(function(e) {
		e.preventDefault();
	});



	// Drag and drop import
	$( document ).on('dragover.ls', '.slider-item.import-sliders', function( e ) {
		e.preventDefault();
		$( this ).addClass('ls-dragover')

	}).on('dragleave.ls drop.ls', '.slider-item.import-sliders', function( e ) {
		e.preventDefault();
		$( this ).removeClass('ls-dragover')
	}).on('drop.ls', '.slider-item.import-sliders', function( event ) {

		var oe 		= event.originalEvent,
			files 	= event.originalEvent.dataTransfer.files,
			$this 	= $( this ),
			$form 	= $('#tmpl-quick-import-form');


		// Prevent uploading empty or multiple file selection
		if( files.length === 0 ||  files.length > 1 ) {
			return false;
		}

		// Prevent uploading files other than ZIP packages
		if( files[0].name.toLowerCase().indexOf('.zip') === -1 ) {
			return false;
		}


		if( ! $form.length ) {
			$form = $( $('#tmpl-quick-import').text() ).prependTo('body');
		}

		$this.addClass('importing');

		$form.find('input[type="file"]')[0].files = files;
		$form.submit();
	});

	// Import window file input
	$( document ).on( 'change', '#ls-upload-modal-window .file input', function() {

		var file = this.files[0],
			$input = $(this),
			$parent = $input.parent(),
			$span = $input.prev();

		if( !$input.data( 'original-text' ) ){
			$input.data( 'original-text', $span.text() );
		}

		if( file ) {
			$span.text( file.name );
			$parent.addClass( 'file-chosen' );
		} else {
			$span.text( $input.data( 'original-text' ) );
			$parent.removeClass( 'file-chosen' );
		}
	});




	// Import sample slider
	$( '#ls-import-samples-button' ).on( 'click', function( event ) {

		event.preventDefault();

		var	$modal;

		// If the Template Store was previously opened on the current page,
		// just grab the element, do not bother re-appending and setting
		// up events, etc.

		// Append dark overlay
		if( !jQuery( '#ls-import-modal-overlay' ).length ){
			jQuery( '<div id="ls-import-modal-overlay">' ).appendTo( '#wpwrap' );
		}

		if( jQuery( '#ls-import-modal-window' ).length ){

			$modal = jQuery( '#ls-import-modal-window' );

		// First time open on the current page. Set up the UI and others.
		} else {

			// Append the template & setup the live logo
			$modal = jQuery( jQuery('#tmpl-import-sliders').text() ).hide().prependTo('body');

			// Update last store view date
			if( $modal.hasClass('has-updates') ) {
				jQuery.get( window.ajaxurl, { action: 'ls_store_opened' });
			}

			// Hide all template items temporarily for faster animations
			jQuery( '#ls-import-modal-window .items' ).hide();


			// Setup Shuffle. Use setTimeout to avoid timing issues.
			setTimeout(function(){

				// Init Shuffle
				jQuery( '#ls-import-modal-window .inner .items' ).each( function() {

					shuffleContainers.push( new Shuffle( this, {
						itemSelector: '.item',
						speed: 400,
						easing:'ease-in-out',
						delimeter: ',',
						filterMode: Shuffle.FilterMode.ALL
					}) );
				});

			}, 100 );



			// Initialize Looking for more? slider
			setTimeout( function() {
				jQuery('#popups-looking-for-more').layerSlider({
					keybNav: false,
					touchNav: false,
					skin: 'v6',
					navPrevNext: false,
					hoverPrevNext: false,
					navStartStop: false,
					navButtons: false,
					showCircleTimer: false,
					useSrcset: false,
					skinsPath: pluginPath + 'layerslider/skins/'
				});

				jQuery('#open-webshopworks-popups').on('click', function() {
					jQuery('#ls-import-modal-window .source-filter li:last').click();
				});
			}, 1200 );

			importModalWindowTimeline = new TimelineMax({
				onStart: function(){
					jQuery( '#ls-import-modal-overlay' ).show();
					jQuery( 'html, body' ).addClass( 'ls-no-overflow' );
					jQuery(document).on( 'keyup.LS', function( e ) {
						if( e.keyCode === 27 ){
							jQuery( '#ls-import-samples-button' ).data( 'lsModalTimeline' ).reverse().timeScale(1.5);
						}
					});
				},
				onComplete: function(){
					if( importModalWindowTimeline ) {
						importModalWindowTimeline.remove( importModalThumbnailsTransition );
					}
				},
				onReverseComplete: function(){
					jQuery( 'html, body' ).removeClass( 'ls-no-overflow' );
					jQuery(document).off( 'keyup.LS' );
					jQuery( '#ls-import-modal-overlay' ).hide();
					TweenMax.set( jQuery( '#ls-import-modal-window' )[0], { css: { y: -100000 } });
				},
				paused: true
			});

			$(this).data( 'lsModalTimeline', importModalWindowTimeline );

			importModalWindowTimeline.fromTo( $('#ls-import-modal-overlay')[0], 0.75, {
				autoCSS: false,
				css: {
					opacity: 0
				}
			},{
				autoCSS: false,
				css: {
					opacity: 0.75
				},
				ease: Quart.easeInOut
			}, 0 );

			importModalThumbnailsTransition = TweenMax.fromTo( $( '#ls-import-modal-window .items' )[0], 0.5, {
				autoCSS: false,
				css: {
					opacity: 0,
					display: 'block'
				}
			},{
				autoCSS: false,
				css: {
					opacity: 1
				},
			ease: Quart.easeInOut
			});

			importModalWindowTimeline.add( importModalThumbnailsTransition, 0.75 );

			importModalWindowTimeline.add( function(){
				shuffleContainers[0].update();
			}, 0.25 );
		}

		importModalWindowTimeline.remove( importModalWindowTransition );

		importModalWindowTransition = TweenMax.fromTo( $modal[0], 0.75, {
			autoCSS: false,
			css: {
				position: 'fixed',
				display: 'block',
				y: 0,
				x: jQuery( window ).width()
			}
		},{
			autoCSS: false,
			css: {
				x: 0
			},
			ease: Quart.easeInOut
		}, 0 );

		importModalWindowTimeline.add( importModalWindowTransition, 0 );

		importModalWindowTimeline.play();
	});



	// Template Store: Content chooser
	jQuery( document ).on('click', '#ls-import-modal-window .content-filter li, #ls-import-modal-window .source-filter li', function() {

		activeShuffleContainerIndex = jQuery( this ).data('index');

		jQuery('#ls-import-modal-window .inner')
			.removeClass('active')
			.eq( activeShuffleContainerIndex )
			.addClass('active')
			.find('.items')
			.show();

		// Display the Coming Soon tile if the category
		// has no entries at all.
		var $tiles = jQuery( '.shuffle:visible .shuffle-item--visible' );
		jQuery( '.coming-soon' )[ $tiles.length ? 'removeClass' : 'addClass' ]('visible');

		setTimeout( function() {

			jQuery.each( shuffleContainers, function( index, item ) {
				item.update();
			});

		}, 50 );
	});



	// Template Store: Slider filters
	jQuery( document ).on( 'click', '#ls-import-modal-window .shuffle-filters li', function(){

		// Highlight selected category
		jQuery(this).addClass('active').siblings().removeClass('active');

		// Collect selected categories
		var categories = [];
		jQuery('#ls-import-modal-window .shuffle-filters:visible li.active').each( function() {

			var category = jQuery(this).data( 'group' );

			if( category ) {
				categories.push( category );
			}
		});


		// Filter sliders
		shuffleContainers[ activeShuffleContainerIndex ].filter( categories );

		// Display the Coming Soon tile if the category
		// has no entries at all.
		var $tiles = jQuery( '.shuffle:visible .shuffle-item--visible' );
		jQuery( '.coming-soon' )[ $tiles.length ? 'removeClass' : 'addClass' ]('visible');
	});


	$( document ).on( 'click', '#ls-import-modal-window > header b', function(){
		$( '#ls-import-samples-button' ).data( 'lsModalTimeline' ).reverse();
	});

	// Close add slider window
	$(document).on( 'click', '.ls-overlay', function() {

		if($(this).data('manualclose')) {
			return false;
		}

		if($('.ls-pointer').length) {
			$('.ls-overlay').remove();
			$('.ls-pointer').animate({ marginTop : 40, opacity : 0 }, 150);
		}

	// Upload window
	}).on('submit', '#ls-upload-modal-window form', function(e) {

		jQuery('.button', this).text(LS_l10n.SLUploadSlider).addClass('saving');

	}).on('click', '.ls-open-template-store', function(e) {

		e.preventDefault();

		kmw.modal.close();

		setTimeout(function() {
			$('#ls-import-samples-button').click();
		}, $(this).data('delay') || 0);
	});

	// Auto-update setup screen
	$('.button-activation').click(function(e) {
		e.preventDefault();

		var $wrapper 	= $(this).closest('.ls-box'),
			$guide 		= $wrapper.find('.guide'),
			$form 		= $wrapper.find('form'),
			width 		= $wrapper.outerWidth(true) + 10;

		$form.show().find('.key input').focus();

		TweenLite.set( $form, { x: width });
		TweenLite.to( [ $guide[0], $form[0] ], 0.5, {
			x: '-='+width,
			onComplete: function() {
				$guide.hide();
				$wrapper.addClass('ls-opened');
			}
		});
	});

	// Auto-update authorization
	$('.ls-auto-update form').submit(function(e) {

		// Prevent browser default submission
		e.preventDefault();

		var $form 	= $(this),
			$key 	= $form.find('.key input'),
			$button = $form.find('.button-save:visible');

		if( $key.val().length < 10 ) {
			alert(LS_l10n.SLEnterCode);
			return false;
		}

		// Send request and provide feedback message
		$button.data('text', $button.text() ).text(LS_l10n.working).addClass('saving');

		// Post it
		$.ajax({
			type: 'POST',
			url: ajaxurl,
			data: $(this).serialize(),
			error: function( jqXHR, textStatus, errorThrown ) {
				alert(LS_l10n.SLActivationError.replace('%s', errorThrown) );
				$button.removeClass('saving').text( $button.data('text') );
			},
			success: function( data ) {

				// Parse response and set message
				data = $.parseJSON(data);

				// Success
				if( data && ! data.errCode ) {

					// Apply activated state to GUI
					$form.closest('.ls-box').addClass('active');

					// Display activation message
					$('p.note', $form).css('color', '#74bf48').text( data.message );

					// Make sure that features requiring activation will
					// work without refreshing the page.
					window.lsSiteActivation = true;

				// HTML-based error message (if any)
				} else if( typeof data.messageHTML !== "undefined" ) {

					kmw.modal.open({
						title: data.titleHTML ? data.titleHTML : LS_l10n.activationErrorTitle,
						content: '<div id="tmpl-activation-error-modal-window">'+data.messageHTML+'</div>',
						maxWidth: 600,
						minWidth: 400,
						animationIn: 'scale',
						overlaySettings: {
							animationIn: 'fade'
						}
					});

				// Alert message (if any)
				} else if( typeof data.message !== "undefined" ) {
					alert(data.message);
				}

				$button.removeClass('saving').text( $button.data('text') );
			}
		});
	});


	// Auto-update deauthorization
	$('.ls-auto-update a.ls-deauthorize').click(function(event) {
		event.preventDefault();

		if( confirm(LS_l10n.SLDeactivate) ) {

			var $form = $(this).closest('form');

			$.get( ajaxurl, $.param({ action: 'layerslider_deauthorize_site'}), function(data) {

				// Parse response and set message
				var data = $.parseJSON(data);

				if( data && ! data.errCode ) {

					var $box 	= $form.closest('.ls-box'),
						$guide 	= $box.find('.guide'),
						$notice = $form.find('p.note');

					$notice.css('color', '#666').text('');

					$form.find('.key input').val('');
					$box.removeClass('active');

					$form.hide();
					$guide.css('transform', 'translateX(0px)').show();

					window.lsSiteActivation = false;
				}

				// Alert message (if any)
				if(typeof data.message !== "undefined") {
					alert(data.message);
				}
			});
		}
	});

	var lsShowActivationBox = function( activateBox ) {

		document.location.hash = '';

		kmw.modal.close();

		var $box = $('.ls-product-banner.ls-auto-update');


		if( ! $box.length || $box.is(':hidden') ) {
			kmw.modal.open({
				content: '#tmpl-activation-unavailable',
				maxWidth: 600
			});

			return false;
		}

		var	$window = $(window),
			wh 		= $window.height(),
			bt 		= $box.offset().top,
			bh 		= $box.height(),
			top 	= bt + (bh / 2) - (wh / 2);



		$('html,body').animate({ scrollTop: top }, 500, function() {
			setTimeout(function() {

				TweenMax.to( $box[0], 0.2, {
					yoyo: true,
					repeat: 3,
					ease: Quad.easeInOut,
					scale: 1.1,
					onComplete: function() {

						if( activateBox && ! $box.hasClass('ls-opened') ) {
							setTimeout(function() {
								$box.find('.button-activation').click();
							}, 300 );
						}
					}
				});
			}, 200);
		});
	};

	$('.ls-product-banner .unlock, .ls-show-activation-box').click(function(e) {
		e.preventDefault();
		lsShowActivationBox();
	});

	$( document ).on('click', '#activation-modal-window .button-activation', function( e ) {

		e.preventDefault();

		if( $(this).closest('#ls-import-modal-window').length ) {

			jQuery(document).trigger( jQuery.Event('keyup', { keyCode: 27 }) );
			setTimeout(function() {
				lsShowActivationBox( true );
			}, 800);

		} else {

			kmw.modal.close( false, {
				onClose: function() {
					lsShowActivationBox( true );
				}
			});
		}
	});

	if( document.location.href.indexOf('#activationBox') !== -1 ) {
		setTimeout(function() {
			lsShowActivationBox( true );
		}, 500 );
	}



	// News filters
	$('.ls-news .filters li').click(function() {

		// Highlight
		$(this).siblings().attr('class', '');
		$(this).attr('class', 'active');

		// Get stuff
		var page = $(this).data('page');
		var frame = $(this).closest('.ls-box').find('iframe');
		var baseUrl = frame.attr('src').split('#')[0];

		// Set filter
		frame.attr('src', baseUrl+'#'+page);

	});


	// Shortcode
	$('input.ls-shortcode').click(function() {
		this.focus();
		this.select();
	});

	// Importing demo sliders
	$( document ).on('click', '#ls-import-modal-window .item-import a', function( event ) {
		event.preventDefault();

		var $item 		= jQuery(this),
			$figure 	= $item.closest('figure'),
			name 		= $figure.data('name'),
			handle 		= $figure.data('handle'),
			collection 	= $figure.data('collection'),
			bundled 	= !! $figure.data('bundled'),
			action 		= bundled ? 'ls_import_bundled' : 'ls_import_online';


		// Premium notice
		if( $figure.data('premium') && ! window.lsSiteActivation ) {

			lsDisplayActivationWindow({
				into: '#ls-import-modal-window',
				title: LS_l10n.activationTemplate
			});

			return;

		// Version warning
		} else if( $figure.data('version-warning') ) {

			kmw.modal.open({
				into: '#ls-import-modal-window',
				title: LS_l10n.TSVersionWarningTitle,
				content: LS_l10n.TSVersionWarningContent
			});
			return;
		}

		kmw.modal.open({
			content: '#tmpl-importing',
			into: '#ls-import-modal-window',
			minWidth: 380,
			maxWidth: 380,
			closeButton: false,
			closeOnEscape: false,
			animationIn: 'scale',
			overlaySettings: {
				closeOnClick: false,
				animationIn: 'fade'
			}
		});

		jQuery.ajax({
			url: ajaxurl,
			data: {
				action: action,
				slider: handle,
				name: name,
				collection: collection,
				security: window.lsImportNonce
			},

			beforeSend: function( jqXHR, settings ) {

				setTimeout( function( ) {

					var $modal = jQuery('#ls-loading-modal-window').closest('.kmw-modal');

					TweenLite.to( $modal[0], 0.5, {
						minWidth: 580,
						maxWidth: 580,
						height: 446,
						maxHeight: 480,

						onComplete: function() {
							$('<div class="ls-import-notice">'+LS_l10n.SLImportNotice+'</div>')
							.hide()
							.appendTo( $modal.find('.kmw-modal-content') )
							.fadeIn( 250 );
						}
					});
				}, 1000*60 );
			},

			success: function(data, textStatus, jqXHR) {

				data = data ? JSON.parse( data ) : {};

				if( data.success ) {
					document.location.href = data.url;

				} else {

					kmw.modal.close();

					if( data.reload ) {
						window.location.reload( true );
						return;
					}

					if( data.errCode && data.errCode == 'ERR_WW_POPUPS_PURCHASE_NOT_FOUND') {


							lsDisplayActivationWindow({
								into: '#ls-import-modal-window',
								title: LS_l10n.purchaseWWPopups,
								content: '#tmpl-purchase-webshopworks-popups',
								minHeight: 680,
								maxHeight: 680
							});


						return;
					}

					setTimeout(function() {
						kmw.modal.open({
							into: '#ls-import-modal-window',
							title: data.title || LS_l10n.SLImportErrorTitle,
							content: data.message || LS_l10n.SLImportError,
							animationIn: 'scale',
							overlaySettings: {
								animationIn: 'fade'
							}

						});

					}, 600);
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {

				kmw.modal.close();

				setTimeout(function() {
					kmw.modal.open({
						into: '#ls-import-modal-window',
						title: LS_l10n.SLImportErrorTitle,
						content: LS_l10n.SLImportHTTPError.replace('%s', errorThrown),
						animationIn: 'scale',
						overlaySettings: {
							animationIn: 'fade'
						}

					});

				}, 600);
			},
			complete: function() {

			}
		});
	});

	if( document.location.hash === '#open-template-store' ) {
		setTimeout( function() {
			$('#ls-import-samples-button').click();
		}, 500);
	}


	$('.layerslider_notice_img .button-install').click(function( e ) {

		if( ! window.lsSiteActivation ) {
			e.preventDefault();
			lsDisplayActivationWindow({
				title: LS_l10n.activationUpdate
			});
		}
	});




	var addSliderToGroupDraggable = function() {

		$('.ls-sliders-grid > .slider-item').draggable({
			scope: 'add-to-group',
			cancel: '.group-item, .hero',
			handle: '.preview',
			distance: 5,
			helper: 'clone',
			revert: 'invalid',
			revertDuration: 300,
			start: function( event, ui ) {

				draggedSliderItem = event.target;
				$( draggedSliderItem ).addClass('dragging-original');
			},

			stop: function( event, ui ) {
				$( event.target ).removeClass('dragging-original');
			}
		});
	};


	var addSliderToGroupDroppable = function() {

		$('.ls-sliders-grid .group-item').droppable({
			scope: 'add-to-group',
			accept: '.slider-item',
			tolerance: 'pointer',
			hoverClass: 'slider-dropping',
			over: function( event, ui ) {

				ui.helper.find('.preview').addClass('slider-dropping');
			},

			out: function( event, ui ) {
				ui.helper.find('.preview').removeClass('slider-dropping');
			},


			drop: function( event, ui ) {

				addSliderToGroup( event.target, draggedSliderItem );
			}
		});
	};



	var removeSliderFromGroupDraggable = function() {

		$('.ls-sliders-grid .kmw-modal-inner .slider-item').draggable({
			scope: 'remove-from-group',
			handle: '.preview',
			appendTo: '.ls-sliders-grid',
			distance: 5,
			helper: 'clone',
			zIndex: 9999999,
			revert: 'invalid',
			revertDuration: 300,
			start: function( event, ui ) {
				draggedSliderItem = event.target;
				$( draggedSliderItem ).addClass('dragging-original');
				$('#ls-group-remove-area').addClass('active');
			},

			stop: function( event, ui ) {
				$( draggedSliderItem ).removeClass('dragging-original');
				$('#ls-group-remove-area').removeClass('active');
			}
		});
	};


	var removeSliderFromGroupDroppable = function() {

		$('#ls-group-remove-area .ls-drop-area').droppable({
			scope: 'remove-from-group',
			accept: '.slider-item',
			tolerance: 'pointer',

			over: function( event, ui ) {
				ui.draggable.addClass('over-drag-area');
				ui.helper.find('.preview').addClass('cursor-default');
				$( event.target ).addClass('over');
			},

			out: function( event, ui ) {
				ui.draggable.removeClass('over-drag-area');
				ui.helper.find('.preview').removeClass('cursor-default');
				$( event.target ).removeClass('over');
			},

			drop: function( event, ui ) {

				$( event.target ).removeClass('over');
				ui.draggable.removeClass('over-drag-area');

				removeSliderFromGroup(
					$lastOpenedGroup,
					ui.draggable
				);
			}
		});
	};



	var createSliderGroupLastEvent;

	var createSliderGroupDroppable = function() {

		$('.ls-sliders-grid .slider-item:not(.hero,.group-item)').droppable({
			scope: 'add-to-group',
			accept: '.slider-item',
			tolerance: 'pointer',
			hoverClass: 'slider-dropping',

			over: function( event, ui ) {

				var f = function(){
					targetSliderItem = event.target;
					$( event.target ).addClass('create-group');
					ui.helper.find('.preview').addClass('slider-dropping');
					createSliderGroupLastEvent = 'over';
				};

				if( createSliderGroupLastEvent == 'over' ){
					setTimeout( function(){
						f();
					}, 0 );
				} else {
					f();
				}
			},

			out: function( event, ui ) {

				var f = function(){
					targetSliderItem = null;
					$('.slider-item').removeClass('create-group');
					ui.helper.find('.preview').removeClass('slider-dropping');
					createSliderGroupLastEvent = 'out';
				};

				if( createSliderGroupLastEvent == 'out' ){

					setTimeout( function(){
						f();
					}, 0 );
				} else {
					f();
				}
			},

			deactivate: function( event, ui ) {
				clearTimeout( sliderDragGroupingTimeout );
				$('.slider-item').removeClass('create-group');
				ui.helper.find('.preview').removeClass('slider-dropping');
			},

			drop: function( event, ui ) {

				if( targetSliderItem ) {

					var $template 	= $( $('#tmpl-slider-group-item').text() ),
						$markup 	= $template.insertAfter( targetSliderItem ),
						$group 		= $markup.filter('.group-item');

					addSliderToGroup( $group, targetSliderItem, true );
					addSliderToGroup( $group, draggedSliderItem, true );

					$( targetSliderItem ).hide();
					$( draggedSliderItem ).hide();

					addSliderToGroupDroppable();

					$.getJSON( ajaxurl, {
						action: 'ls_create_slider_group',
						items: [
							$( targetSliderItem ).data('id'),
							$( draggedSliderItem ).data('id')
						]

					}, function( data ) {
						$group.data('id', data.groupId );
					});
				}
			}
		});
	};






	var addSliderToGroup = function( groupElement, sliderElement, withoutXHR ) {

		var $group 			= $( groupElement ),
			$groupItems 	= $group.find('.items'),
			$slider 		= $( sliderElement ),
			$sliderPreview 	= $slider.find('.preview'),
			$groupItem 		= $( $('#tmpl-slider-group-placeholder').text() );

		// XHR request to add slider to group
		if( ! withoutXHR ) {
			$.get( ajaxurl, {
				action: 'ls_add_slider_to_group',
				sliderId: $slider.data('id'),
				groupId: $group.data('id')
			});
		}


		// Add slider to group on UI
		if( ! $sliderPreview.find('.no-preview').length ) {
			$groupItem.find('.preview').css('background-image', $sliderPreview.css('background-image') );
			$groupItem.find('.preview').empty();
		}

		// Destroy previous draggable instance (if any)
		if( $slider.hasClass('ui-draggable') ) {
			$slider.draggable('destroy');
		}

		// Destroy previous droppable instance (if any)
		if( $slider.hasClass('ui-droppable') ) {
			$slider.droppable('destroy');
		}

		$slider.clone( true, true )
			.removeClass('dragging-original')
			.removeClass('create-group')
			.appendTo( $group.next().children() );

		$groupItem.appendTo( $groupItems );
		setTimeout( function() {
			$groupItem.removeClass('scale0');
		}, 100 );

		// Remove the original element
		$slider.remove();
	};



	var removeSliderFromGroup = function( groupElement, sliderElement, withoutXHR ) {

		var $group 			= $( groupElement ),
			$groupItems 	= $group.find('.items'),
			$slider 		= $( sliderElement ),
			$sliderPreview 	= $slider.find('.preview'),
			$siblings 		= $slider.siblings();

		// XHR request to add slider to group
		if( ! withoutXHR ) {
			$.get( ajaxurl, {
				action: 'ls_remove_slider_from_group',
				sliderId: $slider.data('id'),
				groupId: $group.data('id')
			});
		}

		// Remove from preview items
		$groupItems.children().eq( $slider.index() ).remove();

		// Destroy previous draggable instance (if any)
		if( $slider.hasClass('ui-draggable') ) {
			$slider.draggable('destroy');
		}

		// Destroy previous droppable instance (if any)
		if( $slider.hasClass('ui-droppable') ) {
			$slider.droppable('destroy');
		}

		// Remove slider from group
		$slider.insertAfter('.ls-sliders-grid .ls-grid-buttons');

		setTimeout( function() {
			addSliderToGroupDraggable();
			addSliderToGroupDroppable();

			createSliderGroupDroppable();
		}, 300 );


		// Handle auto-group deletion in case of removing
		// the last element.
		if( $siblings.length < 1 ) {

			$group.next().remove();
			$group.remove();

			kmw.modal.close();
		}
	};



	var checkSliderSelection = function() {

		$selected = $('.ls-sliders-grid .slider-item.ls-selected' );

		if( $selected.length ) {
			$('.ls-sliders-grid').addClass('ls-has-selection');
		} else {
			$('.ls-sliders-grid').removeClass('ls-has-selection');
		}
	};


	// Group draggable & droppable
	addSliderToGroupDraggable();
	addSliderToGroupDroppable();

	createSliderGroupDroppable();

	removeSliderFromGroupDraggable();
	removeSliderFromGroupDroppable();

});

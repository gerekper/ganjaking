'use strict';

/* global yith_wcwl, jQuery, tinymce, ajaxurl */

jQuery( ( $ ) => {
	let updatePreviewXHR = null;

	// constants definition.
	const Wizard = function( el, args ) {
			const self = this;

			self.settings = {};

			self.modal = null;

			self._init = function () {
				self.settings = $.extend( {
						template: el.data('template'),
						template_data: {},
						container: '.yith-wcwl-wizard-modal',
						events: {},
					},
					args
				);

				if ( typeof self.settings.events.init === 'function' ){
					self.settings.events.init( el, args );
				}

				self._initOpener();
			};

			self._initOpener = function() {
				el.on( 'click', function( ev ){
					const t = $( this );
					let settings = self.settings.template_data;

					ev.preventDefault();

					// init opener-specific template data
					if ( typeof settings === 'function' ) {
						settings = ( settings )( t );
					}

					t.WCBackboneModal( {
						template: self.settings.template,
						variable: settings,
					} );

					var container = $( self.settings.container );

					self._initEditor( container );
					self._initEnhancedSelect( container );
					self._initTabs( container );
					self._initSteps( container );
					self._initOptions( container, settings );
					self._initEvents( container, self.settings.events );
				} );
			};

			self._initEditor = function( modal ){
				modal.find( '.with-editor' ).each( function(){
					const t = $( this ),
						id = t.attr( 'id' );

					// Destroy any existing editor so that it can be re-initialized when popup opens.
					if ( tinymce.get( id ) ) {
						wp.editor.remove( id );
					}

					wp.editor.initialize( id, {
						tinymce: {
							wpautop: true,
							init_instance_callback ( editor ) {
								editor.on( 'Change', function( e ) {
									t.val( editor.getContent() ).change();
								} );
							}
						},
						quicktags: true,
						mediaButtons: true,
					} );
				} );
			};

			self._initEnhancedSelect = function( modal ){
				$( document.body ).trigger( 'wc-enhanced-select-init' );
			};

			self._initTabs = function( modal ){
				modal.find( '.tabs' ).on( 'click', 'a', function( ev ){
					const t = $(this),
						ul = t.closest( 'ul' ),
						a = ul.find( 'a' ),
						p = ul.parent(),
						tabs = p.find( '.tab' ),
						target = t.data( 'target' ),
						tab = $( target );
					let changed = false;

					ev.preventDefault();

					if ( ! t.hasClass( 'active' ) ){
						changed = true;
					}

					a.attr( 'aria-selected', 'false' ).removeClass( 'active' );
					t.attr( 'aria-selected', 'true' ).addClass( 'active' );

					tabs.attr( 'aria-expanded', 'false' ).removeClass( 'active' ).hide();
					tab.attr( 'aria-expanded', 'true' ).addClass( 'active' ).show();

					if ( changed ){
						t.trigger( 'tabChange' );
					}
				} );
			};

			self._initOptions = function( modal, values ){
				$.each( values, function( i, v ){
					const field = modal.find( '[name="' + i + '"]' );

					if ( ! field.length || v === field.val() ){
						return;
					}

					if ( field.is( 'select' ) && v && ! field.find( 'option[value="' + v + '"]' ).length ){
						field.append( '<option value="' + v + '" selected="selected">' + v + ' </option>' );
					} else {
						field.val(v);
					}
				} );
			};

			self._initSteps = function( modal ){
				// show only first step by default
				modal.find( '.step' ).hide().first().show();

				// init continue button
				modal.find( '.continue-button' ).on( 'click', function( ev ){
					const t = $( this ),
						current_step = t.closest( '.step' ),
						next_step = current_step.next( '.step' );

					ev.preventDefault();

					if ( next_step.length ) {
						self._changeStep( modal, current_step, next_step );
					}
				} );

				// init back button
				modal.find( '.back-button' ).on( 'click', function( ev ){
					const t = $( this ),
						current_step = t.closest( '.step' ),
						prev_step = current_step.prev( '.step' );

					ev.preventDefault();

					if ( prev_step.length ) {
						self._changeStep( modal, current_step, prev_step );
					}
				} );
			};

			self._initEvents = function( modal, events ){
				if ( typeof self.settings.events.open === 'function' ){
					self.settings.events.open( el, modal );
				}

				$.each( events, function( i, v ){
					let target = null;

					// exclude general events
					if ( i === 'init' || i === 'open' ){
						return;
					}

					// tab events
					else if ( i === 'tabChange' ){
						target = modal.find( '.tabs' );
					}

					// step events
					else if ( i === 'stepChange' ){
						target = modal.find( '.step' );
					}

					// input changes
					else {
						target = modal.find( ':input' );
					}

					target.on( i, function( ev ) {
						return ( v )( $( this ), modal, ev );
					} );
				} );
			};

			self._changeStep = function( modal, current, next ) {
				current.animate(
					{
						opacity: 0
					},
					{
						duration: 200,
						complete: function () {
							var modalContent = modal.find( 'article' ),
								modalContentWidth = modalContent.outerWidth(),
								modalContentHeight = modalContent.outerHeight();

							// calculate step size
							modalContent.outerWidth( 'auto' );
							modalContent.outerHeight( 'auto' );

							current.hide();
							next.show();

							const nextWidth = next.outerWidth(),
								nextHeight = next.outerHeight();

							next.hide();
							current.css( 'opacity', 1 );

							// fix modal size
							modalContent.outerWidth( modalContentWidth );
							modalContent.outerHeight( modalContentHeight );

							modalContent.animate(
								{
									width: nextWidth,
									height: nextHeight
								},
								{
									duration: 200,
									complete() {
										next.fadeIn( 200 );
									},
								}
							);
						},
					}
				);

				next.trigger( 'stepChange' );
			};

			self._init();
		},
		updatePreview = function ( el, modal, ev ) {
			const preview = modal.find( '.email-preview' ),
				template = modal.find('#template').val();

			if ( updatePreviewXHR ) {
				updatePreviewXHR.abort();
			}

			updatePreviewXHR = $.ajax( {
				url: ajaxurl + '?action=preview_promotion_email&_wpnonce=' + yith_wcwl.nonce.preview_promotion_email,
				data: modal.find('form').serialize(),
				method: 'POST',
				beforeSend () {
					preview.block( {
						message: null,
						overlayCSS: {
							background: 'transparent',
							opacity: 0.6,
						},
					} );
				},
				complete () {
					preview.unblock();
				},
				success ( data ) {
					preview
						.removeClass( 'html plain' )
						.addClass( template )
						.find('.no-interactions')
						.html( data );
				}
			} );
		},
		getPromotionWizardData = function(){
			return {
				template: 'yith-wcwl-promotion-wizard',
				template_data: function( el ){
					var data = el.data( 'draft' );

					if( ! data ) {
						data = $.extend( data, {
							product_id  : el.data('product_id'),
							user_id     : el.data('user_id'),
							content_html: yith_wcwl.promotion.content_html,
							content_text: yith_wcwl.promotion.content_text,
							coupon      : false,
						} );
					}

					return data;
				},
				events: {
					change: updatePreview,
					open: function( el, modal, ev ){
						modal.find( '#content_html-tmce' ).click();
						updatePreview( el, modal, ev );
					},
					tabChange: function( el, modal, ev ){
						modal.find( '#template' ).val( el.find( '.active' ).data( 'template' ) );
						updatePreview( el, modal, ev );
					},
					stepChange: function( el, modal, ev ){
						var counter = el.find( '.receivers-count' ),
							additional_info = el.find( '.show-on-long-queue' ),
							threshold = additional_info.data('threshold');

						if( ! counter.length ){
							return;
						}

						$.ajax( {
							url: ajaxurl + '?action=calculate_promotion_email_receivers&_wpnonce=' + yith_wcwl.nonce.calculate_promotion_email_receivers,
							data: modal.find('form').serialize(),
							method: 'post',
							beforeSend() {
								counter.css( 'opacity', 0.3 );

								if ( additional_info.length ) {
									additional_info.hide();
								}
							},
							complete() {
								counter.css( 'opacity', 1 );
							},
							success( data ) {
								if ( typeof data.label === 'undefined' ) {
									return;
								}

								counter.html( data.label );

								if ( additional_info.length && typeof data.count !== 'undefined' && data.count > threshold ) {
									additional_info.show();
								}
							}
						} );
					}
				}
			}
		};

	// jQuery wizard extension.
	$.fn.wizard = function ( args ) {
		const t = $( this );

		if ( t.length ) {
			new Wizard( t, args );
		}

		return t;
	};

	// init wizard.
	$( '.create-promotion' ).wizard( getPromotionWizardData() );
	$( '.restore-draft' ).wizard( getPromotionWizardData() );
} );

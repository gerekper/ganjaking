/* global _, _wpCustomizeSPDesignerPreviewSettings, WebFont */
( function( wp, $ ) {
	if ( ! wp || ! wp.customize ) { return; }

	var api = wp.customize;

	// Link settings.
	api.SPDesignerPreviewData = {
		webSafeFonts: []
	};

	if ( 'undefined' !== typeof _wpCustomizeSPDesignerPreviewSettings ) {
		$.extend( api.SPDesignerPreviewData, _wpCustomizeSPDesignerPreviewSettings );
	}

	api.SPDesignerPreview = {
		PointClickActive: false,
		$activeSelector: '',

		init: function() {
			var self = this, initializedSettings = {};

			api.preview.bind( 'sp-designer-point-click', function( status ) {
				if ( true === status ) {
					self.PointClickActive = true;
				} else {
					self.PointClickActive = false;
				}
			});

			// Selectors
			self.setupPointClickUI();

			api.each( function( setting, id ) {
				setting.id = id;

				if ( ! initializedSettings[ setting.id ] ) {
					if ( self.bindListener( setting ) ) {
						initializedSettings[ setting.id ] = true;
					}
				}
			});

			api.preview.bind( 'setting', function( args ) {
				var id, value, setting;
				args = args.slice();
				id = args.shift();
				value = args.shift();

				setting = api( id );

				if ( ! setting ) {
					setting = api.create( id, value );
				}

				if ( ! setting.id ) {
					setting.id = id;
				}

				if ( ! initializedSettings[ setting.id ] ) {
					if ( self.bindListener( setting ) ) {
						initializedSettings[ setting.id ] = true;
						setting.callbacks.fireWith( setting, [ setting(), null ] );
					}
				}
			});
		},

		/**
		 *
		 * @param {wp.customize.Value} setting
		 * @returns {boolean} Whether the setting was bound.
		 */
		bindListener: function( setting ) {
			var matches;

			matches = setting.id.match( /^sp_designer_css_data\[(.+?)]/ );
			if ( matches ) {
				setting.bind( this.updateCSS );
				return true;
			}

			return false;
		},

		/**
		 * Updates the CSS of a selector in the frontend.
		 */
		updateCSS: function( to, from ) {
			// Remove inline styles if item is set for deletition.
			if ( false === to ) {
				$( from.selector ).removeAttr( 'style' );
				return;
			}

			var setting = this, css = setting.get(), newStyle, textDecoration;

			newStyle = {
				'display':            ( ( 'none' === css.updateDisplay ) ? 'none' : '' ),
				'font-size':          css.fontSize + css.fontSizeUnit,
				'letter-spacing':     css.letterSpacing + css.letterSpacingUnit,
				'line-height':        css.lineHeight + 'px',
				'font-style':         ( ( 'italic' === css.fontStyle ) ? 'italic' : 'normal' ),
				'font-weight':        css.fontWeight,
				'margin-top':         css.marginTop + css.marginTopUnit,
				'margin-bottom':      css.marginBottom + css.marginBottomUnit,
				'margin-left':        css.marginLeft + css.marginLeftUnit,
				'margin-right':       css.marginRight + css.marginRightUnit,
				'padding-top':        css.paddingTop + css.paddingTopUnit,
				'padding-bottom':     css.paddingBottom + css.paddingBottomUnit,
				'padding-left':       css.paddingLeft + css.paddingLeftUnit,
				'padding-right':      css.paddingRight + css.paddingRightUnit,
				'color':              css.color,
				'border-width':       css.borderWidth + css.borderWidthUnit,
				'border-style':       css.borderStyle,
				'border-color':       css.borderColor,
				'border-radius':      css.borderRadius + css.borderRadiusUnit,
				'background-color':   css.backgroundColor
			};

			// Font Family
			if ( 'Default' === css.fontFamily || '' === css.fontFamily ) {
				newStyle['font-family'] = '';
			} else {
				// Only use WebFont is the font is a Google Font.
				if ( ! _.contains( api.SPDesignerPreviewData.webSafeFonts, css.fontFamily ) ) {
					var family = css.fontFamily;

					if ( css.fontVariant ) {
						family = family + ':' + css.fontVariant;

						var variantWeight = parseInt( css.fontVariant, 10 );

						// Set the correct weight for this variant.
						if ( ! _.isNaN( variantWeight ) ) {
							newStyle['font-weight'] = variantWeight;
						}
					}

					WebFont.load({
						google: { families: [ family ] }
					});
				}

				newStyle['font-family'] = css.fontFamily;
			}

			// Text decoration
			textDecoration = [];
			if ( 'underline' === css.textUnderline ) {
				textDecoration.push( 'underline' );
			}

			if ( 'line-through' === css.textLineThrough ) {
				textDecoration.push( 'line-through' );
			}

			if ( 0 === textDecoration.length ) {
				textDecoration.push( 'none' );
			}

			textDecoration = textDecoration.join( ' ' );

			newStyle['text-decoration'] = textDecoration;

			// Background
			if ( css.backgroundImage && css.backgroundImage.url ) {
				newStyle['background-image']      = 'url("' + css.backgroundImage.url + '")';
				newStyle['background-repeat']     = css.backgroundRepeat;
				newStyle['background-position']   = css.backgroundPosition;
				newStyle['background-attachment'] = css.backgroundAttachment;
			} else {
				newStyle['background-image']      = '';
			}

			var outputStyle = '';

			_.each( newStyle, function( val, key ) {
				if ( '' !== val ) {
					outputStyle += key + ':' + val + ';';
				}
			});

			var selectorPrefix = api.SPDesignerPreviewData.prefixOtherClasses;

			if ( 'body' === css.selector ) {
				selectorPrefix = api.SPDesignerPreviewData.prefixBodyClass;
				outputStyle = 'body' + selectorPrefix + '{' + outputStyle + '}';
			} else {
				outputStyle = selectorPrefix + ' ' + css.selector + '{' + outputStyle + '}';
			}

			var sanitizedStyleID;

			// Replace spaces with dashes
			sanitizedStyleID = css.selector.replace(/\s+/g, '-').toLowerCase();

			// Lowercase
			sanitizedStyleID = sanitizedStyleID.toLowerCase();

			// Strip invalid characters
			sanitizedStyleID = sanitizedStyleID.replace( /[^0-9A-Za-z-]/g, '' );

			// Remove any other style tags for this selector
			$( '#' + 'sp-designer-' + sanitizedStyleID ).remove();

			// Append style tag to <head>
			$(' <style/>' ).attr( 'id', 'sp-designer-' + sanitizedStyleID ).text( outputStyle ).appendTo( document.head );
		},

		/**
		 * Collects the CSS properties and sends them to the Customizer.
		 */
		addSelector: function( selector ) {
			var $selector = $( selector ), data;

			data = {
				'selector': selector,
				'cssProperties': {
					'updateDisplay':         $selector.css( 'display' ),
					'fontWeight':            $selector.css( 'font-weight' ),
					'fontSize':              $selector.css( 'font-size' ),
					'letterSpacing':         $selector.css( 'letter-spacing' ),
					'lineHeight':            $selector.css( 'line-height' ),
					'fontStyle':             $selector.css( 'font-style' ),
					'textDecoration':        $selector.css( 'text-decoration' ),
					'color':                 $selector.css( 'color' ),
					'borderRadius':          $selector.css( 'border-radius' ),
					'borderColor':           $selector.css( 'border-color' ),
					'borderStyle':           $selector.css( 'border-style' ),
					'borderWidth':           $selector.css( 'border-width' ),
					'marginTop':             $selector.css( 'margin-top' ),
					'marginLeft':            $selector.css( 'margin-left' ),
					'marginRight':           $selector.css( 'margin-right' ),
					'marginBottom':          $selector.css( 'margin-bottom' ),
					'paddingTop':            $selector.css( 'padding-top' ),
					'paddingLeft':           $selector.css( 'padding-left' ),
					'paddingRight':          $selector.css( 'padding-right' ),
					'paddingBottom':         $selector.css( 'padding-bottom' ),
					'backgroundColor':       $selector.css( 'background-color' ),
					'backgroundRepeat':      $selector.css( 'background-repeat' ),
					'backgroundPosition':    $selector.css( 'background-position' ),
					'backgroundAttachment':  $selector.css( 'background-attachment' )
				}
			};

			api.preview.send( 'sp-designer-click-selector', data );
		},

		setupPointClickUI: function() {
			var self = this, $borderTop, $borderBottom, $borderLeft, $borderRight, $elementLabel;

			_.each( api.SPDesignerPreviewData.selectorsMap, function( selector ) {
				$( selector.selector ).attr( 'data-sp-element-selector', selector.selector );
				$( selector.selector ).attr( 'data-sp-element-title', selector.name );
			});

			// Borders for hovers
			$borderTop    = $( '<div/>' ).addClass( 'sp-element-border sp-element-border-top' );
			$borderBottom = $( '<div/>' ).addClass( 'sp-element-border sp-element-border-bottom' );
			$borderLeft   = $( '<div/>' ).addClass( 'sp-element-border sp-element-border-left' );
			$borderRight  = $( '<div/>' ).addClass( 'sp-element-border sp-element-border-right' );

			// Append borders to body
			$( 'body' ).append( $borderTop );
			$( 'body' ).append( $borderBottom );
			$( 'body' ).append( $borderLeft );
			$( 'body' ).append( $borderRight );

			// Element info label
			$elementLabel = $( '<div/>' ).addClass( 'sp-element-label' );
			$( 'body' ).append( $elementLabel );

			var setUItoDefault = function() {
				self.$activeSelector = '';

				$( '.sp-element-same' ).remove();

				$( self.$activeSelector ).css({
					'cursor': 'auto'
				});

				// Remove inline styles from borders
				$borderTop.attr( 'style', '' );
				$borderBottom.attr( 'style', '' );
				$borderLeft.attr( 'style', '' );
				$borderRight.attr( 'style', '' );

				// Remove text and inline styles from label
				$elementLabel.text( '' ).attr( 'style', '' );
			};

			// Block clicks on all elements, add selector to Customizer when clicked.
			$( '*' ).on( 'click', function() {
				if ( true === self.PointClickActive && '' !== self.$activeSelector ) {
					if ( self.$activeSelector.attr( 'data-sp-element-selector' ) ) {
						var selector = self.$activeSelector.data( 'sp-element-selector' );
						self.addSelector( selector );
						setUItoDefault();
					}

					return false;
				}
			});

			// Hover action
			$( '[data-sp-element-selector]' ).hover(
				function() {
					var element  = this,
						selector = $( element ).data( 'sp-element-selector' ),
						title    = $( element ).data( 'sp-element-title' ),
						position = $( element ).offset(),
						width    = $( element ).outerWidth(),
						height   = $( element ).outerHeight();

					if ( false === self.PointClickActive ) {
						return false;
					}

					if ( '' !== self.$activeSelector && ! $( element ).is( self.$activeSelector ) ) {
						if ( self.$activeSelector.has( $( element ) ) ) {
							setUItoDefault();
						} else {
							return false;
						}
					}

					self.$activeSelector = $( element );

					// Fix overflow when the element is next to the edge
					if ( 0 === position.left ) {
						position.left = 2;
						width = width - 4;
					}

					// Add borders around element
					$borderTop.css({
						'top': position.top,
						'left': position.left,
						'width': width
					});

					$borderBottom.css({
						'top': ( position.top + height ),
						'left': position.left,
						'width': width
					});

					$borderLeft.css({
						'top': position.top,
						'left': ( position.left - 2 ),
						'height': ( height + 2 )
					});

					$borderRight.css({
						'top': position.top,
						'left': ( position.left + width ),
						'height': ( height + 2 )
					});

					$elementLabel.text( title );

					$elementLabel.css({
						'top': ( position.top + height ),
						'left': ( position.left - 2 ),
						'width': ( width + 4 ),
						'min-width': $elementLabel.outerWidth()
					});

					// Look for other elements with the same selector and identify them
					$( selector ).not( this ).each( function() {
						var position = $( this ).offset(),
							width    = $( this ).outerWidth(),
							height   = $( this ).outerHeight();

						$( '<div/>' ).addClass( 'sp-element-same' ).css({
							'position': 'absolute',
							'z-index': '999999',
							'width': width,
							'height': height,
							'top': position.top,
							'left': position.left
						}).appendTo( 'body' );
					});

					$( this ).css({
						'cursor': 'pointer'
					});
				}, function() {
					setUItoDefault();

					$( this ).css({
						'cursor': 'auto'
					});
				}
			);
		}
	};

	api.bind( 'preview-ready', function() {
		api.preview.bind( 'active', function() {
			api.SPDesignerPreview.init();
		} );
	} );
} )( window.wp, jQuery );
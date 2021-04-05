/* global _, _wpCustomizeSPDesignerSettings */
( function( wp, $ ) {
	'use strict';

	if ( ! wp || ! wp.customize ) { return; }

	// Set up our namespace.
	var api = wp.customize;

	api.SPDesigner = api.SPDesigner || {};

	// Link settings.
	api.SPDesigner.data = {
		phpIntMax: 0,
		section: '',
		fontVariants: {}
	};

	if ( 'undefined' !== typeof _wpCustomizeSPDesignerSettings ) {
		$.extend( api.SPDesigner.data, _wpCustomizeSPDesignerSettings );
	}

	/**
	 * Generates random integers.
	 *
	 * Heavily inspired by the nav menus implementation. Thanks!
	 *
	 * @see wp-admin/js/customize-nav-menus.js
	 * @return {number}
	 */
	api.SPDesigner.generateRandomSelectorId = function() {
		return Math.ceil( api.SPDesigner.data.phpIntMax * Math.random() );
	};

	/**
	 * A jQuery widget for the CSS measurement properties.
	 */
	api.SPDesigner.measurementPicker = {
		_create: function() {
			var self    = this,
				el      = self.element,
				_wrap   = '<div class="sp-measurement-picker" />',
				_before = '<a tabindex="0" class="button sp-measurement-result" />',
				_container = '<div class="sp-measurement-container" />';

			// keep close bound so it can be attached to a body listener
			self.close = $.proxy( self.close, self );

			this.valueInput = el.find( '.sp-measurement-value' );
			this.unitSelect = el.find( '.sp-measurement-unit' );

			if ( ! this.valueInput || ! this.unitSelect ) {
				return;
			}

			// Hide input and wrap with a container div
			this.valueInput.next( '.sp-measurement-unit' ).addBack().wrapAll( _wrap );

			self.wrap      = this.valueInput.parent().hide().wrap( $( _container ) );
			self.container = self.wrap.parent();
			self.toggler   = $( _before ).prependTo( self.container );

			// Set Initial value
			self.updateToggler();

			self._addListeners();
		},

		_addListeners: function() {
			var self = this;

			// prevent any clicks inside this widget from leaking to the top and closing it
			self.container.on( 'click.measurementpicker', function( event ) {
				event.stopPropagation();
			});

			self.toggler.click( function(){
				if ( self.toggler.hasClass( 'sp-measurement-picker-open' ) ) {
					self.close();
				} else {
					self.open();
				}
			});

			self.valueInput.change( function() {
				self.updateToggler();
			});

			self.unitSelect.change( function() {
				self.updateToggler();
			});
		},

		open: function() {
			this.wrap.show();
			this.toggler.hide();

			this.wrap.addClass( 'sp-measurement-picker-active' );
			this.toggler.addClass( 'sp-measurement-picker-open' );

			$( 'body' ).trigger( 'click.measurementpicker' ).on( 'click.measurementpicker', this.close );
		},

		close: function() {
			this.wrap.hide();
			this.toggler.show();

			this.wrap.removeClass( 'sp-measurement-picker-active' );
			this.toggler.removeClass( 'sp-measurement-picker-open' );

			$( 'body' ).off( 'click.measurementpicker', this.close );
		},

		updateToggler: function() {
			this.toggler.text( this.valueInput.val().toString() + this.unitSelect.val().toString() );
		}
	};

	$.widget( 'sp.spMeasurementPicker', api.SPDesigner.measurementPicker );

	/**
	 * wp.customize.PowerpackDesigner.CSSControl
	 *
	 * Customizer control for a CSS control.
	 *
	 * @constructor
	 * @augments wp.customize.Control
	 */
	api.SPDesigner.CSSControl = api.Control.extend({
		/**
		 * Set up the control.
		 */
		ready: function() {
			var control = this;

			/*
			 * Since the control is not registered in PHP, we need to prevent the
			 * preview's sending of the activeControls to result in this control
			 * being deactivated.
			 */
			control.active.validate = function() {
				var value, section = api.section( control.section() );
				if ( section ) {
					value = section.active();
				} else {
					value = false;
				}
				return value;
			};

			this._setupUpdateUI();
			this._setupUI();
		},

		/**
		 * Close any open controls and open this one.
		 */
		focus: function() {
			api.each( function( setting, id ) {
				var matches = id.match( /^sp_designer_css_data\[(.+?)]/ );

				if ( matches && ! _.isUndefined( api.control( id ) ) ) {
					var control = api.control( id );
					control.close();
				}
			});

			this.open();
		},

		/**
		 * Open this control.
		 */
		open: function() {
			this.container.closest( '.accordion-section-content' ).addClass( 'sp-designer-control-settings-visible' );
			this.container.find( '.sp-designer-selector-content' ).addClass( 'sp-designer-selector-content-visible' );

			/*
			 * Since WordPress 4.7 we no longer need to adjust the height of the
			 * section. For backwards compatibility, this targets the `customize-pane-child`
			 * class introduced in WordPress 4.7.
			 */
			if ( ! $( this.container.closest( '.accordion-section-content' ) ).hasClass( 'customize-pane-child' ) ) {
				// Adjust control height on resize.
				this._resizeContentHeight();
				$( window ).on( 'resize.customizer-section', _.debounce( _.bind( this._resizeContentHeight, this ), 200 ) );
			}
		},

		/**
		 * Close this control.
		 */
		close: function() {
			this.container.closest( '.sp-designer-control-settings-visible' ).removeClass( 'sp-designer-control-settings-visible' );
			this.container.find( '.sp-designer-selector-content-visible' ).removeClass( 'sp-designer-selector-content-visible' );
		},

		/**
		 * Set up the UI.
		 */
		_setupUI: function() {
			var control = this, itemTemplate, data, container;

			// Load fake section title template.
			itemTemplate = wp.template( 'sp-designer-selector-title' );

			// Data to send to the itemTemplate.
			data = {
				'action': api.section( this.section() ).params.customizeAction,
				'title': api.section( this.section() ).params.title
			};

			// Container for this control.
			container = control.container;

			container.closest( '.accordion-section-content' ).addClass( 'sp-accordion-section-content' );

			// Add section title to fake section.
			container.find( '.sp-designer-selector-content' ).prepend( itemTemplate( data ) );

			// Track clicks on the control title and add open class to the closest accordion section.
			container.find( '.sp-designer-selector-title' ).on( 'click', function( event ) {
				event.preventDefault();
				control.open();
			});

			// Tracks clicks on the fake section title back button.
			container.find( '.customize-section-back' ).on( 'click', function( event ) {
				event.preventDefault();
				control.close();
			});

			// Track clicks on the Remove button
			container.find( 'button.item-delete' ).on( 'click', function( event ) {
				event.preventDefault();
				control.setting.set( false );
			});

			// Initizalize Selectize
			container.find( '[data-sp-designer-property="fontFamily"]' ).selectize({
				onChange: function( value ) {
					var font = _.findWhere( api.SPDesigner.data.fontVariants, { family: value } ),
						fontVariant = container.find( '[data-sp-designer-property="fontVariant"]' )[0].selectize,
						variantList;

					variantList = control._getFontVariantOptions( font );

					fontVariant.clearOptions();
					fontVariant.addOption( variantList );

					// Set default value to "regular" or, alternatively,
					// the first one in the list.
					if ( _.findWhere( variantList, { id: 'regular' } ) ) {
						fontVariant.setValue( 'regular' );
					} else {
						fontVariant.setValue( variantList[0].id );
					}

					fontVariant.refreshOptions( false );

					// Disable input if there's only one variant.
					if ( 1 >= variantList.length ) {
						fontVariant.disable();
					} else {
						fontVariant.enable();
					}
				},

				onDropdownClose: function() {
					if ( '' === this.getValue() ) {
						this.setValue( 'Default' );
					}
				}
			});

			container.find( '[data-sp-designer-property="fontVariant"]' ).selectize({
				valueField: 'id',

				labelField: 'text',

				searchField: 'text',

				onInitialize: function() {
					var fontFamily,
						font,
						variantList,
						elementValue;

					fontFamily  = container.find( '[data-sp-designer-property="fontFamily"]' )[0].selectize;
					font        = _.findWhere( api.SPDesigner.data.fontVariants, { family: fontFamily.getValue() } );
					variantList = control._getFontVariantOptions( font );

					this.clearOptions();
					this.addOption( variantList );

					elementValue = _.noop();
					if ( control.elements.fontVariant && ! _.isUndefined( control.elements.fontVariant.get() ) ) {
						elementValue = control.elements.fontVariant.get();
					}

					if ( elementValue ) {
						this.setValue( control.elements.fontVariant.get() );
					} else {
						this.setValue( 'regular' );
					}

					this.refreshOptions( false );

					// Disable input if there's only one variant.
					if ( 1 >= variantList.length ) {
						this.disable();
					} else {
						this.enable();
					}
				},

				onDropdownClose: function() {
					if ( '' === this.getValue() ) {
						this.setValue( 'regular' );
					}
				}
			});

			// Initialize wpColorPicker
			container.find( '.sp-designer-color input[type=text]' ).wpColorPicker({
				change: function() {
					var property = $( this ).data( 'sp-designer-property' );
					if ( control.elements[ property ] ) {
						control.elements[ property ].set( $( this ).wpColorPicker( 'color' ) );
					}
				},

				clear: function() {
					var property = $( this ).data( 'sp-designer-property' );
					if ( control.elements[ property ] ) {
						control.elements[ property ].set( '' );
					}
				}
			});

			// Measurement Picker
			container.find( '.sp-designer-measurement' ).spMeasurementPicker();

			// Background Image: Initialize
			control.bgImage              = {};
			control.bgImage.container    = container.find( '.sp-designer-background-image' );
			control.bgImage.placeholder  = $( control.bgImage.container ).find( '.placeholder' );
			control.bgImage.img          = $( control.bgImage.container ).find( 'img' );
			control.bgImage.btnAdd       = $( control.bgImage.container ).find( '.new' );
			control.bgImage.btnChange    = $( control.bgImage.container ).find( '.change' );
			control.bgImage.btnRemove    = $( control.bgImage.container ).find( '.remove' );
			control.bgImage.bgRepeat     = container.find( '.sp-designer-background-repeat' );
			control.bgImage.bgPosition   = container.find( '.sp-designer-background-position' );
			control.bgImage.bgAttachment = container.find( '.sp-designer-background-attachment' );

			// Background Image: Handy shortcut so we don't have to us _.bind every time we add a callback
			_.bindAll( control, '_bgImageRemoveImg', '_bgImageUpload', '_bgImageRender', '_bgImageUpdate', '_bgImagePick' );

			// Background Image: Actions
			control.bgImage.btnAdd.on( 'click', this._bgImageUpload );
			control.bgImage.btnChange.on( 'click', this._bgImageUpload );
			control.bgImage.btnRemove.on( 'click', this._bgImageRemoveImg );

			// Background Image: Render
			this._bgImageRender();
		},

		/**
		 * Set up event handlers for updating CSS properties.
		 */
		_setupUpdateUI: function() {
			var control = this,
				settingValue = control.setting(), elements;

			elements = [
				'updateDisplay', 'fontStyle', 'textUnderline', 'textLineThrough', 'fontFamily', 'fontVariant', 'fontSize', 'fontSizeUnit', 'fontWeight', 'lineHeight',
				'letterSpacing', 'letterSpacingUnit', 'letterSpacingUnit', 'color', 'marginTop', 'marginTopUnit', 'marginBottom', 'marginBottomUnit',
				'marginLeft', 'marginLeftUnit', 'marginRight', 'marginRightUnit', 'paddingTop', 'paddingTopUnit', 'paddingBottom', 'paddingBottomUnit',
				'paddingLeft', 'paddingLeftUnit', 'paddingRight', 'paddingRightUnit', 'borderRadius', 'borderRadiusUnit', 'borderColor', 'borderStyle',
				'borderWidth', 'borderWidthUnit', 'backgroundColor', 'backgroundRepeat', 'backgroundPosition', 'backgroundAttachment'
			];

			control.elements = {};

			_.each( elements, function( element ) {
				control.elements[ element ] = new api.Element( control.container.find( '[data-sp-designer-property="' + element + '"]' ) );
			});

			_.each( control.elements, function( element, property ) {
				element.bind( function( value ) {
					if ( element.element.is( 'input[type=checkbox]' ) ) {
						value = ( value ) ? element.element.val() : '';
					}

					if ( 'fontWeight' === property && 'bold' === value ) {
						value = 700;
					}

					var settingValue = control.setting();

					if ( settingValue && settingValue[ property ] !== value ) {
						settingValue = _.clone( settingValue );
						settingValue[ property ] = value;
						control.setting.set( settingValue );
						element.set( settingValue[ property ] );
					}
				});

				if ( settingValue ) {
					element.set( settingValue[ property ] );
				}
			});

			control.setting.bind( function( to ) {
				if ( false === to ) {
					control.close();
					control.container.remove();
					api.control.remove( control.id );

					// Refresh previewer to remove style in <head>
					api.previewer.refresh();
				} else {
					// Update the elements' values to match the new setting properties.
					_.each( to, function( value, key ) {
						if ( control.elements[ key ] ) {
							control.elements[ key ].set( to[ key ] );
						}
					});
				}
			});
		},

		/**
		 * Retrieves the font variants for a given font.
		 *
		 * @param  {object} font
		 * @return {object} variantList An object with the font variants.
		 */
		_getFontVariantOptions: function ( font ) {
			var variantList = [];

			if ( _.isUndefined( font ) || _.isUndefined( font.variants ) ) {
				variantList.push(
					{
						id: 'regular',
						text: 'Regular'
					}
				);
			} else {
				variantList = font.variants;
			}

			return variantList;
		},

		/**
		 * Remember that _.bind was used to maintain `this` as the control
		 * object rather than the usual jQuery way of binding to the DOM element.
		 */
		_bgImageUpload: function ( event ) {
			event.preventDefault();

			if ( ! this.bgImage.frame ) {
				this._bgImageInitFrame();
			}

			this.bgImage.frame.open();
		},

		/**
		 * Set the media frame so that it can be reused and accessed when needed.
		 */
		_bgImageInitFrame: function() {
			this.bgImage.frame = wp.media({
				// The title of the media modal
				title: api.SPDesigner.data.bgImage.l10n.choose,
				// restrict to specified mime type
				library: {
					type: api.SPDesigner.data.bgImage.mime_type
				},
				// Customize the submit button.
				button: {
					// Set the text of the button.
					text: api.SPDesigner.data.bgImage.l10n.set
				},
				// Just one, thanks.
				multiple: false
			});

			// When an image is selected, run a callback.
			this.bgImage.frame.on( 'select', this._bgImagePick );
		},

		/**
		 * Fired when an image is selected in the media modal. Gets the selected
		 * image information, and sets it within the control.
		 */
		_bgImagePick: function() {
			// get the attachment from the modal frame
			var attachment = this.bgImage.frame.state().get( 'selection' ).single();
			if ( 'image' === attachment.get( 'type' ) ) {
				var value = this._bgImageReduceMembers( attachment.toJSON() );
				this._bgImageUpdate( value );
			}
		},

		/**
		 * Reduces the attachment object to just the few desired members.
		 * @param  {object} attachment An attachment object provided by the
		 *                             medial modal.
		 * @return {object}            A reduced media object.
		 */
		_bgImageReduceMembers: function( attachment ) {
			var desired = [
					'id',
					'sizes',
					'url'
				],
				output = {};
			$.each( desired, function( i, key ) {
				output[key] = attachment[key];
			});
			return output;
		},

		/**
		 * Called when the "Remove Image" link is clicked. Sets thes setting back
		 * to its default state.
		 * @param  {object} event jQuery Event object from click event.
		 */
		_bgImageRemoveImg: function( event ) {
			event.preventDefault();

			this._bgImageUpdate( {
				url: '',
				id: 0
			} );
		},

		/**
		 * Sets the background image setting.
		 * @param  {object} attachment Media object.
		 */
		_bgImageUpdate: function( value ) {
			var control = this, settingValue = control.setting(), property = 'backgroundImage';

			if ( settingValue && settingValue[ property ] !== value ) {
				settingValue = _.clone( settingValue );
				settingValue[ property ] = value;
				control.setting.set( settingValue );

				// Setting has changed, render!
				this._bgImageRender();
			}
		},

		/**
		 * Called on init and whenever a setting is changed. Shows the thumbnail
		 * when there is one or the upload button when there isn't.
		 */
		_bgImageRender: function() {
			var value = this.setting();

			if ( value && value.backgroundImage && value.backgroundImage.url ) {
				this.bgImage.placeholder.hide();
				if ( ! value.backgroundImage.sizes || ! value.backgroundImage.sizes.medium ) {
					this.bgImage.img.attr( 'src', value.backgroundImage.url );
				} else {
					this.bgImage.img.attr( 'src', value.backgroundImage.sizes.medium.url );
				}
				this.bgImage.img.show();
				this.bgImage.btnRemove.show();
				this.bgImage.btnChange.show();
				this.bgImage.btnAdd.hide();
				this.bgImage.bgRepeat.show();
				this.bgImage.bgPosition.show();
				this.bgImage.bgAttachment.show();
			} else {
				this.bgImage.img.hide();
				this.bgImage.placeholder.show();
				this.bgImage.btnRemove.hide();
				this.bgImage.btnChange.hide();
				this.bgImage.btnAdd.show();
				this.bgImage.bgRepeat.hide();
				this.bgImage.bgPosition.hide();
				this.bgImage.bgAttachment.hide();
			}
		},

		_resizeContentHeight: function() {
			this.container.find('.sp-designer-selector-content').css( 'height', this.container.closest( '.accordion-section-content' ).height() );
		}
	});

	/**
	 * wp.customize.SPDesignerCustomizer
	 *
	 */
	api.SPDesignerCustomizer = {
		pointClickStatus: false,

		/**
		 * Called after preview has finished loading. Listens to events from
		 * the preview window.
		 */
		init: function () {
			var self = this;
			api.previewer.bind( 'sp-designer-click-selector', $.proxy( self.maybeBuildControl, this ) );
			this.pointClickToggle();
		},

		/**
		 * Tracks clicks on the Point&Click UI toggle button and sends the event
		 * to the preview window.
		 */
		pointClickToggle: function() {
			var self = this;

			$( '.sp-designer-point-click-toggle' ).on( 'click', function( event ) {
				event.preventDefault();

				if ( false === self.pointClickStatus ) {
					self.pointClickStatus = true;
				} else {
					self.pointClickStatus = false;
				}

				$( this ).closest( '#customize-theme-controls' ).toggleClass( 'sp-add-a-style-active', self.pointClickStatus );

				api.previewer.send( 'sp-designer-point-click', self.pointClickStatus );
			});
		},

		/**
		 * Checks if a control already exists. If not, a new control is created.
		 *
		 * @param {Object} data The CSS selector properties.
		 */
		maybeBuildControl: function( data ) {
			var control = _.noop(), selectorId, customizeId, settingArgs, setting, settingDefaults, label;

			// Check if there's already a control for this selector.
			api.each( function( setting, id ) {
				var matches = id.match( /^sp_designer_css_data\[(.+?)]/ );

				if ( matches && ! _.isUndefined( api.control( id ) ) ) {
					var existingControl = api.control( id ), existingControlSetting = existingControl.setting();

					if ( existingControl && data.selector === existingControlSetting.selector ) {
						control = existingControl;
						return;
					}
				}
			});

			// If the control doesn't exist, create a new one.
			if ( _.isUndefined( control ) ) {
				selectorId = api.SPDesigner.generateRandomSelectorId();

				customizeId = 'sp_designer_css_data[' + selectorId + ']';

				settingArgs = {
					type: 'sp_designer_css_data',
					transport: 'postMessage',
					previewer: api.previewer
				};

				settingDefaults = this._handleDefaults( data.cssProperties );
				settingDefaults.selector = data.selector;

				setting = api.create( customizeId, customizeId, settingDefaults, settingArgs );

				// Get selector nice name from the selectors map
				label = _.findWhere( api.SPDesigner.data.selectorsMap, { selector: data.selector } );

				if ( ! _.isUndefined( label ) ) {
					label = label.name;
				} else {
					label = data.selector;
				}

				control = new api.controlConstructor.sp_designer_css( customizeId, {
					params: {
						type: 'sp_designer_css',
						content: '<li id="customize-control-' + String( selectorId ) + '" class="customize-control customize-control-sp_designer_css"></li>',
						label: label,
						section: api.SPDesigner.data.section,
						priority: this._calculatePriority(),
						active: true,
						settings: {
							'default': customizeId
						},
						id: selectorId,
						css: settingDefaults
					},
					previewer: api.previewer
				});

				api.control.add( customizeId, control );
			}

			// Disable point & click interface.
			this.pointClickStatus = false;
			$( '#customize-theme-controls' ).toggleClass( 'sp-add-a-style-active', false );
			api.previewer.send( 'sp-designer-point-click', false );

			// Switch to control
			control.focus();
		},

		/**
		 * Caculates the priority for a new control.
		 */
		_calculatePriority: function() {
			var priority = 10;

			api.control.each( function( control ) {
				if ( 'sp_designer_css' === control.params.type && control.setting() ) {
					priority = Math.max( priority, control.priority() );
				}
			});

			priority += 1;

			return priority;
		},

		/**
		 * Sets and sanitizes the default values for each CSS property.
		 *
		 * @param {Object} cssProperties The CSS selector properties.
		 */
		_handleDefaults: function( cssProperties ) {
			var defaults = {
				'updateDisplay':         ( ( cssProperties.display ) ? 'none' : 'inline' ),

				'fontSize':              ( ( cssProperties.fontSize && 0 <= parseInt( cssProperties.fontSize, 10 ) ) ? parseInt( cssProperties.fontSize, 10 ) : 0 ),
				'fontSizeUnit':          ( ( cssProperties.fontSizeUnit ) ? cssProperties.fontSizeUnit : 'px' ),
				'letterSpacing':         ( ( cssProperties.letterSpacing ) ? parseInt( cssProperties.letterSpacing, 10 ) : 0 ),
				'letterSpacingUnit':     ( ( cssProperties.letterSpacingUnit ) ? cssProperties.letterSpacingUnit : 'px' ),
				'lineHeight':            ( ( cssProperties.lineHeight && 0 <= parseInt( cssProperties.lineHeight, 10 ) ) ? parseInt( cssProperties.lineHeight, 10 ) : 0 ),
				'fontFamily':            ( ( cssProperties.fontFamily ) ? cssProperties.fontFamily : 'Default' ),
				'fontStyle':             ( ( 'italic' === cssProperties.fontStyle ) ? cssProperties.fontStyle : '' ),
				'color':                 ( ( cssProperties.color && isHexColor( rgb2hex( cssProperties.color ) ) ) ? rgb2hex( cssProperties.color ) : '' ),

				'borderRadius':          ( ( cssProperties.borderRadius && 0 <= parseInt( cssProperties.borderRadius, 10 ) ) ? parseInt( cssProperties.borderRadius, 10 ) : 0 ),
				'borderRadiusUnit':      ( ( cssProperties.borderRadiusUnit ) ? cssProperties.borderRadiusUnit : 'px' ),
				'borderColor':           ( ( cssProperties.borderColor && isHexColor( rgb2hex( cssProperties.borderColor ) ) ) ? rgb2hex( cssProperties.borderColor ) : '' ),
				'borderStyle':           ( ( _.contains( [ 'none', 'dotted', 'dashed', 'double', 'solid' ], cssProperties.borderStyle ) ) ? cssProperties.borderStyle : 'none' ),
				'borderWidth':           ( ( cssProperties.borderWidth && 0 < parseInt( cssProperties.borderWidth, 10 ) ) ? parseInt( cssProperties.borderWidth, 10 ) : 1 ),
				'borderWidthUnit':       ( ( cssProperties.borderWidthUnit ) ? cssProperties.borderWidthUnit : 'px' ),

				'backgroundColor':       ( ( cssProperties.backgroundColor && isHexColor( rgb2hex( cssProperties.backgroundColor ) ) ) ? rgb2hex( cssProperties.backgroundColor ) : '' ),
				'backgroundRepeat':      ( ( _.contains( [ 'no-repeat', 'repeat', 'repeat-x', 'repeat-y' ], cssProperties.backgroundRepeat ) ) ? cssProperties.backgroundRepeat : 'no-repeat' ),
				'backgroundPosition':    ( ( _.contains( [ 'left', 'center', 'right' ], cssProperties.backgroundPosition ) ) ? cssProperties.backgroundPosition : 'left' ),
				'backgroundAttachment':  ( ( _.contains( [ 'scroll', 'fixed' ], cssProperties.backgroundAttachment ) ) ? cssProperties.backgroundAttachment : 'scroll' ),

				'marginTop':             ( ( cssProperties.marginTop ) ? parseInt( cssProperties.marginTop, 10 ) : 0 ),
				'marginTopUnit':         ( ( cssProperties.marginTopUnit ) ? cssProperties.marginTopUnit : 'px' ),
				'marginLeft':            ( ( cssProperties.marginLeft ) ? parseInt( cssProperties.marginLeft, 10 ) : 0 ),
				'marginLeftUnit':        ( ( cssProperties.marginLeftUnit ) ? cssProperties.marginLeftUnit : 'px' ),
				'marginRight':           ( ( cssProperties.marginRight ) ? parseInt( cssProperties.marginRight, 10 ) : 0 ),
				'marginRightUnit':       ( ( cssProperties.marginRightUnit ) ? cssProperties.marginRightUnit : 'px' ),
				'marginBottom':          ( ( cssProperties.marginBottom ) ? parseInt( cssProperties.marginBottom, 10 ) : 0 ),
				'marginBottomUnit':      ( ( cssProperties.marginBottomUnit ) ? cssProperties.marginBottomUnit : 'px' ),
				'paddingTop':            ( ( cssProperties.paddingTop && 0 <= parseInt( cssProperties.paddingTop, 10 ) ) ? parseInt( cssProperties.paddingTop, 10 ) : 0 ),
				'paddingTopUnit':        ( ( cssProperties.paddingTopUnit ) ? cssProperties.paddingTopUnit : 'px' ),
				'paddingLeft':           ( ( cssProperties.paddingLeft && 0 <= parseInt( cssProperties.paddingLeft, 10 ) ) ? parseInt( cssProperties.paddingLeft, 10 ) : 0 ),
				'paddingLeftUnit':       ( ( cssProperties.paddingLeftUnit ) ? cssProperties.paddingLeftUnit : 'px' ),
				'paddingRight':          ( ( cssProperties.paddingRight && 0 <= parseInt( cssProperties.paddingRight, 10 ) ) ? parseInt( cssProperties.paddingRight, 10 ) : 0 ),
				'paddingRightUnit':      ( ( cssProperties.paddingRightUnit ) ? cssProperties.paddingRightUnit : 'px' ),
				'paddingBottom':         ( ( cssProperties.paddingBottom && 0 <= parseInt( cssProperties.paddingBottom, 10 ) ) ? parseInt( cssProperties.paddingBottom, 10 ) : 0 ),
				'paddingBottomUnit':     ( ( cssProperties.paddingBottomUnit ) ? cssProperties.paddingBottomUnit : 'px' )
			};

			// Font Weight
			defaults.fontWeight = '';

			if ( isNaN( cssProperties.fontWeight ) && 'bold' === cssProperties.fontWeight ) {
				defaults.fontWeight = 700;
			} else if ( _.contains( [ 100, 200, 300, 400, 500, 600, 700, 800, 900 ], parseInt( cssProperties.fontWeight, 10 ) ) ) {
				defaults.fontWeight = parseInt( cssProperties.fontWeight, 10 );
			}

			// Text Decoration
			defaults.textUnderline = '';
			defaults.textLineThrough = '';
			if ( cssProperties.textDecoration && 'none' !== cssProperties.textDecoration ) {
				var textDecoration = _.compact( cssProperties.textDecoration.split( ' ' ) );

				if ( _.contains( textDecoration, 'underline' ) ) {
					defaults.textUnderline = 'underline';
				}

				if ( _.contains( textDecoration, 'line-through' ) ) {
					defaults.textLineThrough = 'line-through';
				}
			}

			return defaults;
		}
	};

	/**
	 * Convert RGB to Hex.
	 * Adapted from: http://stackoverflow.com/a/3627747
	 *
	 * @param {string} rgb{a} color
	 * @returns {string} hex color
	 */
	function rgb2hex( rgb ) {
		if ( /^#[0-9A-F]{6}$/i.test( rgb ) ) {
			return rgb;
		}

		// Transparent?
		if ( rgb === 'rgba(0, 0, 0, 0)' || rgb === 'transparent' ) {
			return '';
		}

		rgb = rgb.match(/^rgba?[\s+]?\([\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?/i);

		function hex( x ) {
			return ( '0' + parseInt( x, 10 ).toString(16) ).slice(-2);
		}

		return '#' + hex( rgb[1] ) + hex( rgb[2] ) + hex( rgb[3] );
	}

	/**
	 * Checks if given color is in hex format.
	 *
	 * @param {string} color
	 * @returns {string} color
	 */
	function isHexColor( color ) {
		return /(^#[0-9A-F]{6}$)|(^#[0-9A-F]{3}$)/i.test( color );
	}

	/**
	 * Extends wp.customize.controlConstructor with control constructor for
	 * sp_designer_css.
	 */
	$.extend( api.controlConstructor, {
		sp_designer_css: api.SPDesigner.CSSControl
	});

	api.bind( 'ready', function() {
		api.SPDesignerCustomizer.init();
	});
} )( window.wp, jQuery );
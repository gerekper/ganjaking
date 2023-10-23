/* global yithWcbmMetaboxOptions */
/* global yithWcbmMetaboxPremiumOptions */

jQuery( function ( $ ) {
	var addBadgeButton          = $( '.edit-php.post-type-yith-wcbm-badge .page-title-action, .edit-php.post-type-yith-wcbm-badge .yith-plugin-fw__list-table-blank-state__cta' ),
		badgePreviewInWpList    = $( '.column-yith_wcbm_preview .yith-wcbm-badge' ),
		isChecked               = function ( input ) {
			input = input instanceof jQuery ? input : $( this );
			return input.is( ':checked' );
		},
		block                   = function ( element ) {
			element.block( {
				message   : '',
				overlayCSS: { backgroundColor: '#ffffff', opacity: 0.8, cursor: 'wait' }
			} );
		},
		unblock                 = function ( element ) {
			element.unblock();
		},
		getElementTextColor     = function ( element, defaultColor ) {
			var $element = $( element ),
				color    = defaultColor ? defaultColor : '#000000',
				cssColor = $element.css( 'color' );
			if ( $element && $element.text().trim() ) {
				var $elementWithoutChild = $element.clone();
				$elementWithoutChild.find( '*' ).remove();
				if ( $elementWithoutChild.text().trim() ) {
					if ( cssColor && ( cssColor.indexOf( 'rgb' ) !== -1 || cssColor.indexOf( '#' ) !== -1 || cssColor.indexOf( 'hsl' ) !== -1 ) ) {
						color = cssColor;
					}
					return color;
				} else {
					var children = $element.find( '> *' ),
						i        = -1;
					while ( children[ ++i ] ) {
						if ( $( children[ i ] ).text().trim() ) {
							return getElementTextColor( children[ i ], cssColor );
						}
					}
				}
			}

			return color;
		},
		hexToHSL                = function ( color ) {
			color     = color.replace( '#', '' );
			var red   = parseInt( color[ 0 ] + color[ 1 ], 16 ) / 255,
				green = parseInt( color[ 2 ] + color[ 3 ], 16 ) / 255,
				blue  = parseInt( color[ 4 ] + color[ 5 ], 16 ) / 255,
				max   = Math.max( red, green, blue ),
				min   = Math.min( red, green, blue ),
				h,
				s,
				l     = ( max + min ) / 2 * 100;
			if ( max === min ) {
				h = 0;
				s = 0;
			} else {
				var d = max - min;
				s     = 100 * ( l > 50 ? d / ( 2 - max - min ) : d / ( max + min ) );
				if ( red === max ) {
					h = ( green - blue ) / d + ( green < blue ? 6 : 0 );
				} else if ( green === max ) {
					h = ( blue - red ) / d + 2;
				} else {
					h = ( red - green ) / d + 4;
				}
				h *= 60;
			}

			return { 'h': h, 's': s, 'l': l };
		},
		colorWithFactorial      = function ( color, factorial ) {
			color       = color.replace( '#', '' );
			var red     = color[ 0 ] + color[ 1 ],
				green   = color[ 2 ] + color[ 3 ],
				blue    = color[ 4 ] + color[ 5 ],
				red_d   = Math.round( factorial * parseInt( red, 16 ) ),
				green_d = Math.round( factorial * parseInt( green, 16 ) ),
				blue_d  = Math.round( factorial * parseInt( blue, 16 ) ),
				r1      = ( red_d < 16 ? '0' : '' ) + red_d.toString( 16 ),
				g1      = ( green_d < 16 ? '0' : '' ) + green_d.toString( 16 ),
				b1      = ( blue_d < 16 ? '0' : '' ) + blue_d.toString( 16 );
			return '#' + r1 + g1 + b1;
		},
		elementLineHeightFix    = function ( element ) {
			$( element ).find( 'p' ).map( function ( index, paragraph ) {
				var fontSize = $( paragraph ).find( 'span' ).css( 'font-size' );
				if ( fontSize ) {
					$( paragraph ).css( 'line-height', fontSize );
				}
				return paragraph;
			} );
			return $( element );
		},
		badge                   = {
			preview                    : $( '.yith-wcbm-preview-metabox .yith-wcbm-badge' ),
			container                  : $( '.yith-wcbm-preview-metabox .yith-wcbm-preview-container' ),
			templates                  : {
				css     : wp.template( 'yith-wcbm-css-badge-html' ),
				advanced: wp.template( 'yith-wcbm-advanced-badge-html' )
			},
			fields                     : {
				allRows           : $( '#yith-wcbm-metabox .yith-wcbm-badge-type-row ~ .the-metabox' ),
				type              : $( '#_type' ),
				uploadedImageWidth: $( '#_uploaded_image_width' ),
				width             : $( '#_size-dimension-width' ),
				height            : $( '#_size-dimension-height' ),
				sizeUnit          : $( '#_size input.yith-plugin-fw-dimensions__unit__value' ),
				backgroundColor   : $( '#_background_color' ),
				textColor         : $( '#_text_color' ),
				padding           : {
					top   : $( '#_padding-dimension-top' ),
					right : $( '#_padding-dimension-right' ),
					bottom: $( '#_padding-dimension-bottom' ),
					left  : $( '#_padding-dimension-left' ),
					unit  : $( '#_padding input.yith-plugin-fw-dimensions__unit__value' )
				},
				borderRadius      : {
					topLeft    : $( '#_border_radius-dimension-top-left' ),
					topRight   : $( '#_border_radius-dimension-top-right' ),
					bottomRight: $( '#_border_radius-dimension-bottom-right' ),
					bottomLeft : $( '#_border_radius-dimension-bottom-left' ),
					unit       : $( '#_border_radius input.yith-plugin-fw-dimensions__unit__value' )
				},
				opacity           : $( '#_opacity' ),
				rotation          : {
					x: $( '#_rotation_x' ),
					y: $( '#_rotation_y' ),
					z: $( '#_rotation_z' )
				},
				flipText          : {
					inputs   : $( '#_flip_text input' ),
					inputsRow: $( '.yith-wcbm-flip-text-row' ),
					enable   : $( '#_use_flip_text' )
				},
				positionType      : $( '#_position_type input' ),
				anchorPoint       : $( '#_anchor_point input' ),
				valuePosition     : {
					top                  : $( '#_position_values-dimension-top' ),
					right                : $( '#_position_values-dimension-right' ),
					bottom               : $( '#_position_values-dimension-bottom' ),
					left                 : $( '#_position_values-dimension-left' ),
					unit                 : $( '#_position_values input.yith-plugin-fw-dimensions__unit__value' ),
					unitPxHandler        : $( '#_position_values .yith-plugin-fw-dimensions__unit--px-unit' ),
					unitPercentageHandler: $( '#_position_values .yith-plugin-fw-dimensions__unit--percentage-unit' )
				},
				valuePositionRows : $( '.yith-wcbm-position-values-row, .yith-wcbm-anchor-point-row' ),
				fixedPosition     : $( '.yith-wcbm-position-field input' ),
				fixedAlignment    : $( '.yith-wcbm-alignment-field input' ),
				fixedPositionRows : $( '.yith-wcbm-position-fixed-row, .yith-wcbm-alignment-fixed-row' ),
				alignmentPreview  : $( '#_alignment-container .yith-wcbm-alignment-preview' ),
				margin            : {
					top   : $( '#_margin-dimension-top' ),
					right : $( '#_margin-dimension-right' ),
					bottom: $( '#_margin-dimension-bottom' ),
					left  : $( '#_margin-dimension-left' ),
					unit  : $( '#_margin input.yith-plugin-fw-dimensions__unit__value' )
				},
				textArea          : $( '.yith-wcbm-text-editor-row textarea' ),
				textContainer     : $( '#_text-container' ),
				imageBadges       : {
					container: $( '#_image' ),
					getInputs: function () {
						return $( '#_image input' );
					},
					assetsUrl: $( '#_image-container .yith-wcbm-badge-library__badges' ).data( 'assets-url' ),
					labels   : $( '#_image-container .yith-wcbm-badge-list-element' ),
					upload   : {
						container: $( '.yith-wcbm-upload-image' ),
						img      : $( '.yith-wcbm-upload-image img' ),
						url      : $( '#yith-wcbm-upload-image-attachment-url' ),
						id       : $( '#yith-wcbm-upload-image-attachment-id' )
					}
				},
				cssBadges         : {
					container: $( '#_css' ),
					getInputs: function () {
						return $( '#_css input' );
					},
					assetsUrl: $( '#_css-container .yith-wcbm-badge-library__badges' ).data( 'assets-url' )
				},
				advancedBadges    : {
					container: $( '#_advanced' ),
					getInputs: function () {
						return $( '#_advanced input' );
					},
					assetsUrl: $( '#_advanced-container .yith-wcbm-badge-library__badges' ).data( 'assets-url' ) ? $( '#_advanced-container .yith-wcbm-badge-library__badges' ).data( 'assets-url' ).replace( 'advanced-badge-previews', 'advanced-badges' ) : ''
				},
				advancedDisplay   : {
					inputs: $( '#_advanced_display input' ),
					row   : $( '#_advanced_display-container' ).parent()
				}
			},
			getCheckedValue            : function ( option ) {
				var optionToFilter = badge.fields[ option ];
				if ( ['imageBadges', 'cssBadges', 'advancedBadges'].includes( option ) ) {
					optionToFilter = optionToFilter.getInputs();
				}
				if ( ['flipText', 'advancedDisplay'].includes( option ) ) {
					optionToFilter = optionToFilter.inputs;
				}

				return optionToFilter ? optionToFilter.filter( isChecked ).val() : false;
			},
			getPositionType            : function () {
				return badge.getCheckedValue( 'positionType' );
			},
			getFixedPosition           : function () {
				return badge.getCheckedValue( 'fixedPosition' );
			},
			getFixedAlignment          : function () {
				return badge.getCheckedValue( 'fixedAlignment' );
			},
			getAnchorPoint             : function () {
				return badge.getCheckedValue( 'anchorPoint' );
			},
			getSelectedImageBadge      : function () {
				return badge.getCheckedValue( 'imageBadges' );
			},
			getSelectedImageBadgeID    : function () {
				return badge.getCheckedValue( 'imageBadges' ).replace( '.svg', '' );
			},
			getSelectedCssBadge        : function () {
				return badge.getCheckedValue( 'cssBadges' );
			},
			getSelectedAdvancedBadge   : function () {
				return badge.getCheckedValue( 'advancedBadges' );
			},
			getAdvancedBadgeDisplay    : function () {
				return badge.getCheckedValue( 'advancedDisplay' );
			},
			getSelectedBadgeId         : function () {
				var badgeID = false;
				switch ( badge.getType() ) {
					case 'image':
						badgeID = badge.getSelectedImageBadge();
						break;
					case 'css':
						badgeID = badge.getSelectedCssBadge();
						break;
					case 'advanced':
						badgeID = badge.getSelectedAdvancedBadge();
						break;
				}
				return badgeID ? Math.abs( badgeID.replace( '.svg', '' ) ) : false;
			},
			getSelectedFlipText        : function () {
				return badge.fields.flipText.enable.is( ':checked' ) ? badge.getCheckedValue( 'flipText' ) : 'no';
			},
			getFixedPositionCss        : function () {
				var position       = badge.getFixedPosition(),
					alignment      = badge.getFixedAlignment(),
					positionRules  = {
						top   : {
							top: 0
						},
						middle: {
							top: '50%'
						},
						bottom: {
							bottom: 0
						}
					},
					alignmentRules = {
						left  : {
							left: 0
						},
						center: {
							left: '50%'
						},
						right : {
							right: 0
						}
					},
					css            = {
						left  : 'auto',
						right : 'auto',
						top   : 'auto',
						bottom: 'auto'
					};
				if ( alignmentRules[ alignment ] ) {
					$.each( alignmentRules[ alignment ], function ( ruleName, ruleValue ) {
						css[ ruleName ] = ruleValue;
					} );
				}
				if ( positionRules[ position ] ) {
					$.each( positionRules[ position ], function ( ruleName, ruleValue ) {
						css[ ruleName ] = ruleValue;
					} );
				}

				return css;
			},
			getSelectedImageBadgeURL   : function () {
				var value = badge.getSelectedImageBadge();
				if ( 'upload' === value ) {
					return badge.fields.imageBadges.upload.url.val();
				}

				return badge.fields.imageBadges.assetsUrl + '/' + value;
			},
			getSelectedCssBadgeURL     : function () {
				return badge.fields.cssBadges.assetsUrl + '/' + badge.getSelectedCssBadge();
			},
			getSelectedAdvancedBadgeURL: function () {
				return badge.fields.advancedBadges.assetsUrl + '/' + badge.getSelectedAdvancedBadge();
			},
			getBadgeUrl                : function () {
				var url = '';
				switch ( badge.getType() ) {
					case 'image':
						url = badge.getSelectedImageBadgeURL();
						break;
					case 'css':
						url = badge.getSelectedCssBadgeURL();
						break;
					case 'advanced':
						url = badge.getSelectedAdvancedBadgeURL();
						break;
				}
				return url;
			},
			getType                    : function () {
				return badge.fields.type.val();
			},
			isType                     : function ( type ) {
				return badge.getType() === type;
			},
			getUploadedImageWidth      : function () {
				return badge.fields.uploadedImageWidth.val();
			},
			getWidthCSS                : function () {
				var width = badge.fields.width.val(),
					unit  = 'percentage' === badge.fields.sizeUnit.val() ? '%' : 'px';

				return width > 0 ? width + unit : 'auto';
			},
			getHeightCSS               : function () {
				var height = badge.fields.height.val(),
					unit   = 'percentage' === badge.fields.sizeUnit.val() ? '%' : 'px';

				return height > 0 ? height + unit : 'auto';
			},
			getBackgroundColor         : function () {
				return badge.fields.backgroundColor.val();
			},
			getTextColor               : function () {
				return badge.isType( 'advanced' ) ? badge.fields.textColor.val() : badge.getTextColorFromText();
			},
			getTextColorFromText       : function () {
				var metaboxBody = $( metabox.getTextEditorBody() ).html(),
					text        = metaboxBody ? metaboxBody : badge.getTextEditorContent();

				return getElementTextColor( $( '<div>' + text + '</div>', '#3c434a' ) );
			},
			getDimensions              : function ( option, dimensions ) {
				var defaults = {
					first : 'top',
					second: 'right',
					third : 'bottom',
					fourth: 'left'
				};
				dimensions   = !dimensions ? defaults : dimensions;
				if ( badge.fields[ option ] ) {
					var firstDimension  = Math.round( badge.fields[ option ][ dimensions.first ].val() ),
						secondDimension = Math.round( badge.fields[ option ][ dimensions.second ].val() ),
						thirdDimension  = Math.round( badge.fields[ option ][ dimensions.third ].val() ),
						fourthDimension = Math.round( badge.fields[ option ][ dimensions.fourth ].val() ),
						unit            = 'percentage' === badge.fields[ option ].unit.val() ? '%' : 'px';
					return firstDimension + unit + ' ' + secondDimension + unit + ' ' + thirdDimension + unit + ' ' + fourthDimension + unit;
				}
				return '';
			},
			setAnchorPoint             : function ( value ) {
				var anchorPointContainer = $( '#_anchor_point' );
				anchorPointContainer.find( 'input' ).prop( 'checked', false );
				anchorPointContainer.find( 'input[value="' + value + '"]' ).prop( 'checked', true );
				metabox.handleAnchorPointChange();
			},
			getPadding                 : function () {
				return badge.getDimensions( 'padding' );
			},
			getBorderRadius            : function () {
				var dimensions = {
					first : 'topLeft',
					second: 'topRight',
					third : 'bottomRight',
					fourth: 'bottomLeft'
				};
				return badge.getDimensions( 'borderRadius', dimensions );
			},
			getOpacity                 : function () {
				return badge.fields.opacity.val() + '%';
			},
			getTransform               : function () {
				var translate = '';
				if ( 'fixed' === badge.getPositionType() ) {
					var position             = badge.getFixedPosition(),
						alignment            = badge.getFixedAlignment(),
						positionToTransform  = {
							top   : '0',
							middle: '-50%',
							bottom: '0'
						},
						alignmentToTransform = {
							left  : '0',
							center: '-50%',
							right : '0'
						};
					translate                = 'translate( ' + alignmentToTransform[ alignment ] + ', ' + positionToTransform[ position ] + ' )';
				}
				var rotate = 'rotateX(' + Math.abs( badge.fields.rotation.x.val() ) + 'deg) rotateY(' + Math.abs( badge.fields.rotation.y.val() ) + 'deg) rotateZ(' + Math.abs( badge.fields.rotation.z.val() ) + 'deg)';

				return translate + ' ' + rotate;
			},
			getMargin                  : function () {
				return badge.getDimensions( 'margin' );
			},
			resetMargin                : function () {
				if ( badge.hasMargin() ) {
					badge.fields.margin.top.val( 0 );
					badge.fields.margin.left.val( 0 );
					badge.fields.margin.bottom.val( 0 );
					badge.fields.margin.right.val( 0 ).trigger( 'change' );
				}
			},
			hasMargin                  : function () {
				return Math.round( badge.fields.margin.top.val() ) || Math.round( badge.fields.margin.right.val() ) || Math.round( badge.fields.margin.bottom.val() ) || Math.round( badge.fields.margin.left.val() );
			},
			getTextEditorContent       : function () {
				var content = badge.fields.textContainer.find( '.html-active' ).length ? badge.fields.textArea.val() : $( metabox.getTextEditorBody() ).html();

				if ( !content ) {
					content = elementLineHeightFix( $( '<div>' + badge.fields.textArea.val() + '</div>' ) ).html();
				}

				Object.keys( yithWcbmMetaboxPremiumOptions.badgePlaceholders ).forEach( function ( placeholder ) {
					content = content.replaceAll( '{{' + placeholder + '}}', yithWcbmMetaboxPremiumOptions.badgePlaceholders[ placeholder ] );
				} );

				return content;
			},
			getValuePositionCss        : function () {
				var positionToGet = badge.getAnchorPoint().split( '-' ),
					unit          = 'percentage' === badge.fields.valuePosition.unit.val() ? '%' : 'px',
					css           = {
						top   : 'auto',
						right : 'auto',
						bottom: 'auto',
						left  : 'auto'
					};

				if ( positionToGet[ 0 ] && positionToGet[ 1 ] ) {
					css[ positionToGet[ 0 ] ] = badge.fields.valuePosition[ positionToGet[ 0 ] ].val() + unit;
					css[ positionToGet[ 1 ] ] = badge.fields.valuePosition[ positionToGet[ 1 ] ].val() + unit;
				}

				return css;
			},
			hasFlipText                : function ( flip ) {
				return flip === badge.getSelectedFlipText();
			},
			getFLipTextCss             : function () {
				var css = {};
				switch ( badge.getSelectedFlipText() ) {
					case 'vertical':
						css = { transform: 'scale(1, -1)' };
						if ( badge.isType( 'css' ) && 8 === badge.getSelectedBadgeId() ) {
							css.transform += ' translate(-50%,50% )';
						}
						break;
					case 'horizontal':
						css = { transform: 'scale(-1, 1)' };
						if ( badge.isType( 'css' ) && 8 === badge.getSelectedBadgeId() ) {
							css.transform += ' translate(50%,-50% )';
						}
						break;
					case 'both':
						css = { transform: 'scale(-1, -1)' };
						if ( badge.isType( 'css' ) && 8 === badge.getSelectedBadgeId() ) {
							css.transform += ' translate(50%,50% )';
						}
						break;
				}

				return css;
			},
			getCSS                     : function () {
				var positionType = badge.getPositionType(),
					positionCss  = 'fixed' === positionType ? badge.getFixedPositionCss() : badge.getValuePositionCss(),
					css          = {
						padding        : 0,
						'border-radius': 0,
						transform      : badge.getTransform(),
						margin         : badge.getMargin(),
						opacity        : badge.getOpacity()
					};
				css              = Object.assign( {}, css, positionCss );
				switch ( badge.getType() ) {
					case 'text':
						css.width              = badge.getWidthCSS();
						css.height             = badge.getHeightCSS();
						css.padding            = badge.getPadding();
						css[ 'border-radius' ] = badge.getBorderRadius();
						break;
					case 'image':
						if ( 'upload' === badge.getSelectedImageBadge() ) {
							var width                 = badge.getUploadedImageWidth();
							css[ 'background-color' ] = '';
							css.width                 = Math.abs( width ) ? badge.getUploadedImageWidth() + 'px' : 'auto';
							css.height                = 'auto';
							break;
						}
					case 'css':
						css[ 'background-color' ] = '';
						css.width                 = 'auto';
						css.height                = 'auto';
						break;
					case 'advanced':
						css.width  = '';
						css.height = '';
						break;
				}

				return css;
			},
			replacePreviewWith         : function ( $newPreview ) {
				badge.preview.removeAttr( 'class' );
				badge.preview.addClass( ( ( Array )( $newPreview[ 0 ].classList ) ).join( ' ' ) );
				badge.preview.html( $newPreview.html() );
			},
			updatePreview              : function ( e ) {
				badge.preview.css( 'background-color', '' );
				switch ( badge.getType() ) {
					case 'text':
						badge.replacePreviewWith( $( '<div class="yith-wcbm-badge yith-wcbm-badge-text"><div class="yith-wcbm-badge-text">' + badge.getTextEditorContent() + '</div></div>' ) );
						break;
					case 'image':
						var content;
						if ( yithWcbmMetaboxOptions[ 'imageBadges' ] && yithWcbmMetaboxOptions[ 'imageBadges' ][ badge.getSelectedImageBadgeID() ] ) {
							content = yithWcbmMetaboxOptions[ 'imageBadges' ][ badge.getSelectedImageBadgeID() ];
						} else {
							content = '<img src="' + badge.getBadgeUrl() + '" alt="">';
						}
						badge.replacePreviewWith( $( '<div class="yith-wcbm-badge yith-wcbm-badge-image ' + ( 'upload' === badge.getSelectedImageBadge() ? 'yith-wcbm-badge-image-uploaded' : '' ) + '">' + content + '</div>' ) );
						break;
					case 'css':
						var badgeId   = badge.getSelectedBadgeId();
						var text      = badge.getTextEditorContent(),
							$newBadge = $( badge.templates.css( {
								classes : 'yith-wcbm-badge yith-wcbm-badge-css',
								id      : badgeId,
								style   : badgeId,
								badgeSvg: yithWcbmMetaboxPremiumOptions.cssBadges[ badge.getSelectedBadgeId() ],
								text    : text
							} ) );
						badge.replacePreviewWith( $newBadge );
						break;
					case 'advanced':
						var badgeId = badge.getSelectedBadgeId();
						if ( yithWcbmMetaboxPremiumOptions.advancedBadges[ badge.getSelectedBadgeId() ] ) {
							var $newBadge = $( badge.templates.advanced(
								{
									classes : 'yith-wcbm-badge yith-wcbm-badge-' + badgeId,
									badgeSvg: yithWcbmMetaboxPremiumOptions.advancedBadges[ badge.getSelectedBadgeId() ],
									id      : badgeId,
									style   : badgeId
								} ) );
							console.log( badge.getSelectedBadgeId(), yithWcbmMetaboxPremiumOptions.advancedBadges[ badge.getSelectedBadgeId() ] );
							$newBadge.removeClass( 'yith-wcbm-advanced-display-amount yith-wcbm-advanced-display-percentage' ).addClass( 'yith-wcbm-advanced-display-' + badge.getAdvancedBadgeDisplay() );
							badge.replacePreviewWith( $newBadge );
						}
						break;
				}
				if ( badge.fields.flipText.enable.is( ':checked' ) ) {
					badge.preview.addClass( 'yith-wcbm-badge-' + badge.getType() + '--flip-' + badge.getSelectedFlipText() );
				}

				badge.updateCssVariables();
				badge.preview.css( badge.getCSS() );
				badge.makeItDraggable();
			},
			makeItDraggable            : function () {
				badge.preview.draggable( {
					containment: '.yith-wcbm-preview-wrapper',
					cursor     : 'grab',
					grid       : [1, 1],
					drag       : badge.draggingHandler
				} );
			},
			draggingHandler            : function () {
				badge.resetMargin();
				if ( 'fixed' === badge.getPositionType() ) {
					badge.fields.positionType.first().prop( 'checked', true ).trigger( 'change' );
				}
				if ( 'px' === badge.fields.valuePosition.unit.val() ) {
					badge.fields.valuePosition.unitPercentageHandler.trigger( 'click' );
				}

				var x               = Math.round( parseInt( badge.preview.css( 'left' ).replace( 'px', '' ) ) ),
					y               = Math.round( parseInt( badge.preview.css( 'top' ).replace( 'px', '' ) ) ),
					height          = Math.round( badge.preview.outerHeight() ),
					containerHeight = Math.round( badge.container.outerHeight() ),
					width           = Math.round( badge.preview.outerWidth() ),
					containerWidth  = Math.round( badge.container.outerWidth() ),
					position        = {
						top   : 0,
						left  : 0,
						bottom: 0,
						right : 0
					},
					anchorPoint     = ( y / containerHeight * 100 > 50 ? 'bottom' : 'top' ) + '-' + ( x / containerWidth * 100 > 50 ? 'right' : 'left' );

				badge.preview.css( { bottom: 'auto', right: 'auto', transform: badge.getTransform() } );
				badge.setAnchorPoint( anchorPoint );

				switch ( anchorPoint ) {
					case 'top-left':
						position.top  = Math.round( y / containerHeight * 10000 ) / 100;
						position.left = Math.round( x / containerWidth * 10000 ) / 100;
						break;
					case 'top-right':
						position.top   = Math.round( y / containerHeight * 10000 ) / 100;
						position.right = Math.round( ( containerWidth - x - width ) / containerWidth * 10000 ) / 100;
						break;
					case 'bottom-right':
						position.bottom = Math.round( ( containerHeight - y - height ) / containerHeight * 10000 ) / 100;
						position.right  = Math.round( ( containerWidth - x - width ) / containerWidth * 10000 ) / 100;
						break;
					case 'bottom-left':
						position.bottom = Math.round( ( containerHeight - y - height ) / containerHeight * 10000 ) / 100;
						position.left   = Math.round( x / containerWidth * 10000 ) / 100;
						break;
				}
				$.each( position, function ( side, value ) {
					badge.fields.valuePosition[ side ].val( value );
				} );

			},
			unsetCssVariables          : function () {
				var variables = ['primary', 'text', 'secondary', 'secondary-light', 'secondary-dark', 'tertiary', 'triadic-positive', 'triadic-negative', 'analogous-positive', 'analogous-negative'];
				for ( var i = 0; i < variables.length; i++ ) {
					document.documentElement.style.setProperty( '--badge-' + variables[ i ] + '-color', 'unset' );
				}
			},
			updateCssVariables         : function ( color ) {
				color = !color ? badge.getBackgroundColor() : color;

				var colorHsl     = hexToHSL( color ),
					cssVariables = {
						'primary'           : color,
						'text'              : badge.getTextColor(),
						'secondary'         : colorWithFactorial( color, 0.6 ),
						'secondary-light'   : colorWithFactorial( color, 0.7 ),
						'secondary-dark'    : colorWithFactorial( color, 0.5 ),
						'tertiary'          : colorWithFactorial( color, 0.4 ),
						'triadic-positive'  : 'hsl(' + ( colorHsl.h + 90 ) + ',' + colorHsl.s + '% ,' + colorHsl.l + '%)',
						'triadic-negative'  : 'hsl(' + ( colorHsl.h - 90 ) + ',' + colorHsl.s + '% ,' + colorHsl.l + '%)',
						'analogous-positive': 'hsl(' + ( colorHsl.h + 30 ) + ',' + colorHsl.s + '% ,' + colorHsl.l + '%)',
						'analogous-negative': 'hsl(' + ( colorHsl.h - 30 ) + ',' + colorHsl.s + '% ,' + colorHsl.l + '%)'
					};

				$.each( cssVariables, function ( name, value ) {
					document.documentElement.style.setProperty( '--badge-' + name + '-color', value );
				} );
			}
		},
		metabox                 = {
			selectors                      : {
				badgeType                   : '.yith-wcbm-badge-type',
				badgeTitle                  : '#yith-wcbm-title',
				uploadImage                 : '.yith-wcbm-upload-image:not(.yith-wcbm-upload-image--uploaded) #yith-wcbm-upload-image',
				badgeLibraryItemClass       : '.yith-wcbm-badge-library__badges .yith-wcbm-badge-library__badge',
				removeUploadedImage         : '.yith-wcbm-upload-image.yith-wcbm-upload-image--uploaded.yith-wcbm-badge-list-element--selected #yith-wcbm-upload-image',
				backgroundColorInput        : '#_background_color',
				textColorInput              : '#_text_color',
				anchorPoint                 : '#_anchor_point input',
				positionType                : '#_position_type-container input',
				position                    : '.yith-wcbm-position-field input',
				alignment                   : '.yith-wcbm-alignment-field input',
				rotation                    : 'input.yith-wcbm-rotation-input',
				rotationSlider              : '.yith-wcbm-rotation-slider input, input.yith-wcbm-rotation-input',
				imageField                  : '.yith-wcbm-badge-list-element input',
				useFlipText                 : '#_use_flip_text',
				libraryTab                  : '.yith-wcbm-badge-library-tab',
				addBadgeToLibraryButtons    : '.yith-wcbm-badge-more__add-to-library-button:not(.yith-wcbm-badge-more__add-to-library-button--locked)',
				addAllBadgesToLibraryButtons: '.yith-wcbm-badge-more__add-all-button',
				checkForPlaceholders        : '.yith-wcbm-check-for-placeholders',
				placeholderWrapper          : '.yith-wcbm-placeholders-list .yith-wcbm-placeholder-wrapper'
			},
			init                           : function () {
				$( document ).on( 'click', metabox.selectors.uploadImage, metabox.uploadBadgeImage );
				$( document ).on( 'click', metabox.selectors.libraryTab, metabox.handleLibraryTabClick );
				$( document ).on( 'click', metabox.selectors.badgeLibraryItemClass, metabox.setBadgeFromData );
				$( document ).on( 'click', metabox.selectors.removeUploadedImage, metabox.removeBadgeImage );
				$( document ).on( 'click', metabox.selectors.addBadgeToLibraryButtons, metabox.handleAddBadgeToLibrary );
				$( document ).on( 'click', metabox.selectors.addAllBadgesToLibraryButtons, metabox.handleAddAllBadgesToLibrary );
				$( document ).on( 'click', metabox.selectors.checkForPlaceholders, metabox.openPlaceholdersModal );
				$( document ).on( 'click', metabox.selectors.placeholderWrapper, metabox.copyPlaceholder );

				$( document ).on( 'change', metabox.selectors.backgroundColorInput, metabox.updateBackgroundColor );
				$( document ).on( 'change', metabox.selectors.textColorInput, metabox.updateTextColor );
				$( document ).on( 'change', metabox.selectors.badgeType, metabox.handleBadgeTypeChange );
				$( document ).on( 'change', metabox.selectors.anchorPoint, metabox.handleAnchorPointChange );
				$( document ).on( 'change', metabox.selectors.positionType, metabox.handlePositionTypeChange );
				$( document ).on( 'change', metabox.selectors.position, metabox.handlePositionChange );
				$( document ).on( 'change', metabox.selectors.position + ' , ' + metabox.selectors.alignment, metabox.handleFixedPositionChange );
				$( document ).on( 'change', metabox.selectors.imageField, metabox.handleBadgeImageSelection );
				$( document ).on( 'change', metabox.selectors.useFlipText, metabox.handleFLipTextVisibility );
				$( document ).on( 'change keyup', metabox.selectors.rotation, metabox.handleRotationChangeFromInput );
				$( document ).on( 'change keyup', metabox.selectors.rotationSlider, metabox.handleRotationChangeFromSlider );
				$( document ).on( 'change keyup invalid', metabox.selectors.badgeTitle, metabox.checkInputValidity );
				$( metabox.selectors.badgeTitle ).on( 'invalid', metabox.checkInputValidity );

				metabox.initEditors();
				metabox.initSelect();
				metabox.initSliders();
				metabox.initImageSelectors();
				metabox.initColorPickers();
				metabox.initAddBadgeToLibraryStyle();

				switch ( badge.getType() ) {
					case 'image':
						badge.fields.imageBadges.getInputs().filter( isChecked ).trigger( 'change' );
						break;
					case 'css':
						badge.fields.cssBadges.getInputs().filter( isChecked ).trigger( 'change' );
						break;
					case 'advanced':
						badge.fields.advancedBadges.getInputs().filter( isChecked ).trigger( 'change' );
						break;
				}

				$( '#_type' ).trigger( 'change' );
				$( '#_position-container input:checked' ).trigger( 'change' );
				$( '#yith-wcbm-title' ).val( $( '#title' ).val() );
				badge.fields.positionType.filter( isChecked ).trigger( 'change' );
				if ( 'values' === badge.getPositionType() ) {
					badge.fields.anchorPoint.filter( isChecked ).trigger( 'change' );
				}
				metabox.handleAdvancedDisplayVisibility();

				badge.updatePreview();
			},
			initEditorBackground           : function () {
				badge.fields.backgroundColor.trigger( 'change' );
			},
			initImageSelectors             : function () {
				$( '.yith-wcbm-badge-library__badges' ).each( function ( index, imagesContainer ) {
					var selectedImage         = $( imagesContainer ).find( '.yith-wcbm-badge-list-element--selected' ),
						offsetTop             = selectedImage[ 0 ] ? selectedImage[ 0 ].offsetTop : 0,
						height                = selectedImage.outerHeight();
					imagesContainer.scrollTop = offsetTop - height / 2 - 69;
				} );
			},
			setBackgroundColor             : function ( color ) {
				badge.fields.backgroundColor.val( color ).trigger( 'change' );
			},
			setTextColor                   : function ( color ) {
				badge.fields.textColor.val( color ).trigger( 'change' );
			},
			setBadgeFromData               : function () {
				if ( yithWcbmMetaboxPremiumOptions.badgeList.hasOwnProperty( badge.getType() ) && yithWcbmMetaboxPremiumOptions.badgeList[ badge.getType() ].hasOwnProperty( badge.getSelectedBadgeId() ) ) {
					metabox.setBackgroundColor( yithWcbmMetaboxPremiumOptions.badgeList[ badge.getType() ][ badge.getSelectedBadgeId() ].backgroundColor );
					if ( badge.isType( 'advanced' ) ) {
						metabox.setTextColor( yithWcbmMetaboxPremiumOptions.badgeList[ badge.getType() ][ badge.getSelectedBadgeId() ].textColor );
					} else if ( badge.isType( 'css' ) ) {
						var $body = $( metabox.getTextEditorBody() );
						$body.html( $body.html().replace( /data-mce-style=".+?"/g, '' ) );
						$body.find( 'p, span' ).each( function ( index, element ) {
							$( element ).css( 'color', yithWcbmMetaboxPremiumOptions.badgeList[ badge.getType() ][ badge.getSelectedBadgeId() ].textColor );
						} );
						badge.fields.textArea.val( $body.html() );
					}
				}
				$( metabox.getTextEditorBody() ).trigger( 'change' );
			},
			getEditorFontSizes             : function () {
				var fontSizes = [];
				for ( var i = yithWcbmMetaboxOptions.editor.fontSize.min, cont = 1; i < yithWcbmMetaboxOptions.editor.fontSize.max; i += Math.ceil( cont / 10 ), cont++ ) {
					fontSizes.push( i + 'pt' );
				}
				fontSizes.push( yithWcbmMetaboxOptions.editor.fontSize.max + 'pt' );
				return fontSizes.join( ' ' );
			},
			getTextEditorBody              : function () {
				if ( !metabox.textEditorBody || !metabox.textEditorBody.length ) {
					metabox.textEditorBody = $( '#_text_ifr' ).contents().find( 'body#tinymce' );
				}
				return metabox.textEditorBody;
			},
			initEditors                    : function () {
				$( 'textarea.yith-wcbm-text-editor:not(.yith-wcbm-text-editor-initialized)' ).each( function () {
					var textarea      = $( this ),
						textareaId    = textarea.attr( 'id' ),
						fixLineHeight = function () {
							var $textEditorContent = $( this.iframeElement ).contents().find( '#tinymce' );
							$textEditorContent.find( 'p' ).map( function ( index, paragraph ) {
								var fontSize = $( paragraph ).find( 'span' ).css( 'font-size' );
								if ( fontSize ) {
									$( paragraph ).css( 'line-height', fontSize );
								}
								return paragraph;
							} );
						};
					textarea.addClass( 'yith-wcbm-text-editor-initialized' );
					wp.editor.initialize( textareaId, {
						tinymce     : {
							wpautop               : false,
							toolbar1              : 'undo,redo,removeformat,fontselect,fontsizeselect,forecolor,bold,italic,,blockquote,alignleft,aligncenter,alignright',
							textarea_rows         : 10,
							font_formats          : yithWcbmMetaboxOptions.editor.fonts.join( '' ),
							fontsize_formats      : metabox.getEditorFontSizes(),
							init_instance_callback: function ( editor ) {
								editor.on( 'keyup', function () {
									var $textEditorContent = $( this.iframeElement ).contents().find( '#tinymce' );
									elementLineHeightFix( $textEditorContent );
								} );
								editor.on( 'keyup', badge.updatePreview );
								$( editor.iframeElement ).contents().find( 'html,body' ).attr( 'style', 'height: 100%; font-family: "Open Sans", sans-serif; line-height: normal !important;' );
								textarea.trigger( 'yith-wcbm-editor-initialized' );
							}
						},
						quicktags   : { buttons: 'strong,em,block,del,ins,img,ul,ol,li,code,more,close' },
						mediaButtons: false
					} );
				} );
			},
			initSelect                     : function () {
				$( '.yith-enhanced-select' ).selectWoo( { minimumResultsForSearch: Infinity } );
			},
			initSliders                    : function () {
				$( '.yith-wcbm-rotation-slider .ui-slider .ui-slider' ).each( function () {
					$( this ).slider( {
						slide: function ( event, ui ) {
							$( this ).find( 'input' ).val( ui.value ).trigger( 'change' );
							$( ui.handle ).text( ui.value );
						}
					} );
				} );

			},
			initColorPickers               : function () {
				badge.fields.backgroundColor.wpColorPicker( { change: metabox.updateBackgroundColor } );
				badge.fields.textColor.wpColorPicker( { change: metabox.updateTextColor } );
			},
			initAddBadgeToLibraryStyle     : function () {
				var addToLibraryText = $( '.yith-wcbm-badge-more__add-to-library-button-text' );
				addToLibraryText     = addToLibraryText ? $( addToLibraryText[ 0 ] ) : false;
				if ( addToLibraryText ) {
					var temporaryDiv = $( '<span>' + addToLibraryText.html() + '</span>' ).css( {
						'font-size'  : '10px',
						'white-space': 'nowrap',
						'min-width'  : 'fit-content',
						'font-weight': '600'
					} );
					$( 'body' ).append( temporaryDiv );
					document.documentElement.style.setProperty( '--add-badge-to-library-length', temporaryDiv.outerWidth() + 'px' );
					temporaryDiv.remove();
				}
			},
			setTextEditorCss               : function ( css ) {
				var textEditorBody = metabox.getTextEditorBody();
				textEditorBody.css( 'background', '' );
				textEditorBody.css( css );
			},
			updateBackgroundColor          : function ( e, ui ) {
				if ( !badge.isType( 'image' ) ) {
					var color = ui && ui.hasOwnProperty( 'color' ) ? ui.color.toString() : $( this ).val();
					metabox.setTextEditorCss( { 'background-color': color } );
					badge.updatePreview();
					badge.updateCssVariables( color );
				}
			},
			updateTextColor                : function ( e, ui ) {
				var color = ui && ui.hasOwnProperty( 'color' ) ? ui.color.toString() : $( this ).val();
				document.documentElement.style.setProperty( '--badge-text-color', color );
			},
			handleBadgeTypeChange          : function () {
				var type = $( this ).val();
				badge.fields.allRows.hide();
				$( '.yith-wcbm-visible-if-' + type ).show();
				switch ( type ) {
					case 'image':
						$( '#_image-container input:checked' ).trigger( 'change' );
						break;
					case 'advanced':
						metabox.handleAdvancedDisplayVisibility();
					case 'css':
						badge.fields.textColor.trigger( 'change' );
					case 'text':
						badge.fields.backgroundColor.trigger( 'change' );
						badge.fields.flipText.enable.trigger( 'change' );
						break;
				}
				badge.fields.positionType.filter( isChecked ).trigger( 'change' );
			},
			handlePositionChange           : function () {
				var $alignmentPreview = badge.fields.alignmentPreview,
					position          = $( this ).val();
				$alignmentPreview.removeClass( 'yith-wcbm-alignment-preview--top yith-wcbm-alignment-preview--middle yith-wcbm-alignment-preview--bottom' );
				$alignmentPreview.addClass( 'yith-wcbm-alignment-preview--' + position );
			},
			handleFixedPositionChange      : function () {
				var position  = badge.getFixedPosition(),
					alignment = badge.getFixedAlignment();
				badge.fields.margin[ 'bottom' === position ? 'top' : 'bottom' ].closest( '.yith-plugin-fw-dimensions__dimension' ).hide();
				badge.fields.margin[ 'bottom' === position ? 'bottom' : 'top' ].closest( '.yith-plugin-fw-dimensions__dimension' ).show();
				badge.fields.margin[ 'right' === alignment ? 'left' : 'right' ].closest( '.yith-plugin-fw-dimensions__dimension' ).hide();
				badge.fields.margin[ 'right' === alignment ? 'right' : 'left' ].closest( '.yith-plugin-fw-dimensions__dimension' ).show();
			},
			handleBadgeImageSelection      : function () {
				var selectedInput   = $( this ),
					imagesContainer = selectedInput.closest( '.yith-wcbm-badge-library__badges' );
				imagesContainer.find( '.yith-wcbm-badge-list-element--selected' ).removeClass( 'yith-wcbm-badge-list-element--selected' );
				selectedInput.closest( '.yith-wcbm-badge-list-element' ).addClass( 'yith-wcbm-badge-list-element--selected' );
				if ( badge.isType( 'advanced' ) ) {
					metabox.handleAdvancedDisplayVisibility();
				} else if ( badge.isType( 'image' ) ) {
					var imageUploadFields = $( '.yith-wcbm-visible-if-image--upload' );
					'upload' === badge.getSelectedImageBadge() ? imageUploadFields.show() : imageUploadFields.hide();
				}

			},
			handleAdvancedDisplayVisibility: function () {
				if ( badge.isType( 'advanced' ) ) {
					if ( yithWcbmMetaboxPremiumOptions.badgeList.hasOwnProperty( 'advanced' ) && yithWcbmMetaboxPremiumOptions.badgeList.advanced.hasOwnProperty( badge.getSelectedBadgeId() ) && yithWcbmMetaboxPremiumOptions.badgeList.advanced[ badge.getSelectedBadgeId() ].hasOwnProperty( 'chooseWhatDisplay' ) && !yithWcbmMetaboxPremiumOptions.badgeList.advanced[ badge.getSelectedBadgeId() ].chooseWhatDisplay ) {
						badge.fields.advancedDisplay.row.hide();
					} else {
						badge.fields.advancedDisplay.row.show();
					}
				}
			},
			handleFLipTextVisibility       : function () {
				if ( $( this ).is( ':checked' ) ) {
					badge.fields.flipText.inputsRow.show();
				} else {
					badge.fields.flipText.inputsRow.hide();
				}
			},
			uploadBadgeImage               : function () {
				var custom_uploader,
					custom_uploader_states = [
						new wp.media.controller.Library( {
							library   : wp.media.query(),
							multiple  : false,
							title     : 'Choose Image',
							priority  : 20,
							filterable: 'uploaded'
						} )
					];

				custom_uploader = wp.media.frames.downloadable_file = wp.media( {
					title   : yithWcbmMetaboxOptions.i18n.uploadAttachment,
					library : {
						type: ''
					},
					button  : {
						text: yithWcbmMetaboxOptions.i18n.uploadAttachment
					},
					multiple: false,
					states  : custom_uploader_states
				} );

				custom_uploader.on( 'close', function ( e ) {
					if ( !custom_uploader.state().get( 'selection' ).length ) {
						metabox.removeBadgeImage();
					}
				} );

				custom_uploader.on( 'select', function () {
					var attachment = custom_uploader.state().get( 'selection' ).first().toJSON();
					badge.fields.imageBadges.upload.url.val( attachment.url );
					badge.fields.imageBadges.upload.container.addClass( 'yith-wcbm-upload-image--uploaded' );
					badge.fields.imageBadges.upload.img.prop( 'src', attachment.url );
					badge.fields.imageBadges.upload.id.val( attachment.id );
					badge.updatePreview();
				} );


				custom_uploader.open();
			},
			removeBadgeImage               : function () {
				badge.fields.imageBadges.upload.container.removeClass( 'yith-wcbm-upload-image--uploaded' );
				badge.fields.imageBadges.upload.img.prop( 'src', '' );
				badge.fields.imageBadges.upload.url.val( '' );
				badge.fields.imageBadges.upload.id.val( '' );
				badge.fields.imageBadges.labels.not( '.yith-wcbm-upload-image' ).first().find( 'input' ).prop( 'checked', true ).trigger( 'change' );
			},
			handleRotationChangeFromInput  : function () {
				var $input   = $( this ),
					linkedTo = $input.data( 'linked-to' ),
					value    = Math.min( 360, Math.max( 0, $input.val() ) );
				$( '#' + linkedTo ).val( value );
				$( '#' + linkedTo + '-div' ).slider( 'value', value );
				$input.val( value );
			},
			handleRotationChangeFromSlider : function () {
				var $slider = $( this );
				$( 'input[data-linked-to="' + $slider.attr( 'id' ) + '"]' ).val( Math.min( 360, Math.max( 0, $slider.val() ) ) );
			},
			handlePositionTypeChange       : function () {
				if ( 'fixed' === $( this ).val() ) {
					badge.fields.fixedPositionRows.show();
					badge.fields.valuePositionRows.hide();
				} else {
					badge.fields.anchorPoint.filter( isChecked ).trigger( 'change' );
					badge.fields.valuePositionRows.show();
					badge.fields.fixedPositionRows.hide();
				}
			},
			handleAnchorPointChange        : function () {
				$.each( badge.fields.valuePosition, function () {
					$( this ).closest( '.yith-plugin-fw-dimensions__dimension' ).hide();
				} );
				$.each( badge.fields.margin, function () {
					$( this ).closest( '.yith-plugin-fw-dimensions__dimension' ).hide();
				} );
				badge.getAnchorPoint().split( '-' ).forEach( function ( key ) {
					badge.fields.valuePosition[ key ].closest( '.yith-plugin-fw-dimensions__dimension' ).show();
					badge.fields.margin[ key ].closest( '.yith-plugin-fw-dimensions__dimension' ).show();
				} );
			},
			handleLibraryTabClick          : function () {
				var container  = $( this ).closest( '.yith-wcbm-badge-library-wrapper' ),
					moreTab    = container.find( '.yith-wcbm-badge-more__badges' ),
					libraryTab = container.find( '.yith-wcbm-badge-library__badges' );
				container.find( '.yith-wcbm-badge-library-tab' ).removeClass( 'yith-wcbm-badge-library-tab--selected' );

				if ( $( this ).hasClass( 'yith-wcbm-badge-library-tab__more' ) ) {
					libraryTab.hide();
					moreTab.css( 'display', 'grid' );
				} else {
					libraryTab.css( 'display', 'grid' );
					moreTab.hide();
					moreTab.find( '.yith-wcbm-badge-more__add-to-library-button--downloaded' ).removeClass( 'yith-wcbm-badge-more__add-to-library-button--downloading' );
				}
				$( this ).addClass( 'yith-wcbm-badge-library-tab--selected' );
			},
			addBadgeToLibrary              : function ( $addBadgeButton, buttonsList ) {
				var badgeContainer = $addBadgeButton.closest( '.yith-wcbm-badge-more__badge' ),
					badgeID        = badgeContainer.data( 'badge-id' ),
					badgeType      = badge.getType(),
					post_data      = {
						action    : yithWcbmMetaboxPremiumOptions.actions.addBadgeToLibrary,
						security  : yithWcbmMetaboxPremiumOptions.security.addBadgeToLibrary,
						badge_type: badgeType,
						badge_id  : badgeID
					},
					success        = false;

				$addBadgeButton.addClass( 'yith-wcbm-badge-more__add-to-library-button--downloading' );

				$.ajax( {
					type    : 'POST',
					dataType: 'json',
					data    : post_data,
					url     : yithWcbmMetaboxPremiumOptions.ajaxurl,
					success : function ( response ) {
						if ( response[ 'success' ] ) {
							success          = true;
							var badgeTypeKey = badgeType + 'Badges';
							if ( 'image' === badgeType ) {
								if ( !yithWcbmMetaboxOptions[ 'imageBadges' ] ) {
									yithWcbmMetaboxOptions[ 'imageBadges' ] = {};
								}
								yithWcbmMetaboxOptions[ 'imageBadges' ][ badgeID.replace( '.svg', '' ) ] = response[ 'badgeContent' ];
							} else {
								if ( !yithWcbmMetaboxPremiumOptions[ badgeTypeKey ] ) {
									yithWcbmMetaboxPremiumOptions[ badgeTypeKey ] = {};
								}
								yithWcbmMetaboxPremiumOptions[ badgeTypeKey ][ badgeID.replace( '.svg', '' ) ] = response[ 'badgeContent' ];
							}

							var template        = wp.template( 'yith-wcbm-badge-library-' + badge.getType() ),
								downloadedBadge = $( template( {
									value     : badgeID,
									previewUrl: badgeContainer.data( 'badge-preview-url' )
								} ) );
							badge.fields[ badgeTypeKey ].container.append( downloadedBadge );
						}
					},
					complete: function () {
						if ( success ) {
							$addBadgeButton.addClass( 'yith-wcbm-badge-more__add-to-library-button--downloaded' );
						} else {
							$addBadgeButton.addClass( 'yith-wcbm-badge-more__add-to-library-button--failed' );
							setTimeout( function () {
								$addBadgeButton.removeClass( 'yith-wcbm-badge-more__add-to-library-button--failed yith-wcbm-badge-more__add-to-library-button--downloading' );
							}, 2500 );
						}

						if ( buttonsList && Array.isArray( buttonsList ) && buttonsList.length > 0 ) {
							$nextButton = $( buttonsList.shift() );
							metabox.addBadgeToLibrary( $nextButton, buttonsList );
						} else {
							$addBadgeButton.closest( '.yith-wcbm-badge-more__badges' ).find( '.yith-wcbm-badge-more__add-all-button' ).removeClass( 'yith-wcbm-badge-more__add-all-button--disabled' );
						}
					}
				} );
			},
			handleAddBadgeToLibrary        : function () {
				metabox.addBadgeToLibrary( $( this ) );
			},
			handleAddAllBadgesToLibrary    : function ( e ) {
				e.preventDefault();
				var button       = $( this ),
					container    = button.closest( '.yith-wcbm-badge-list-container' ),
					addBadgeList = container.find( '.yith-wcbm-badge-more__badge .yith-wcbm-badge-more__add-to-library-button:not(.yith-wcbm-badge-more__add-to-library-button--downloading)' );
				button.addClass( 'yith-wcbm-badge-more__add-all-button--disabled' );
				addBadgeList = addBadgeList.toArray();
				metabox.addBadgeToLibrary( $( addBadgeList.shift() ), addBadgeList );
			},
			checkInputValidity             : function () {
				var $input       = $( this ),
					$description = $input.closest( '.yith-plugin-fw-metabox-field-row' ).find( '.description' ),
					color        = $input.is( ':valid' ) ? '' : '#ea0034';
				$input.css( 'border-color', color );
				$description.css( 'color', color );
			},
			openPlaceholdersModal          : function () {
				yith.ui.modal( {
					...yithWcbmMetaboxPremiumOptions.modals.badgePlaceholders,
					closeWhenClickingOnOverlay: true,
					allowClosingWithEsc       : true,
					width                     : 'auto'
				} );
			},
			copyPlaceholder                : function ( e ) {
				if ( !!e?.originalEvent ) {
					$( this ).find( '.yith-wcbm-placeholder-input .yith-plugin-fw-copy-to-clipboard__copy' ).trigger( 'click' );
				}
			}
		},
		scaleBadgeOnWpList      = function () {
			var badge       = $( this ),
				badgeHeight = $( this ).outerHeight(),
				badgeWidth  = $( this ).outerWidth();
			if ( badgeHeight > 60 || badgeWidth > 200 ) {
				// TODO: zoom not work in Firefox Browser.
				badge.css( 'zoom', Math.min( 60 / badgeHeight, 200 / badgeWidth ) );
			}
		}, // WP List
		openModalToAddBadge     = function ( e ) {
			e.preventDefault();
			yith.ui.modal( {
				title                     : yithWcbmMetaboxOptions.addBadgeModal.title,
				content                   : yithWcbmMetaboxOptions.addBadgeModal.content,
				closeWhenClickingOnOverlay: true,
				width                     : 700
			} );
		},// WP List
		handleToggleEnableBadge = function () {
			var toggleInput     = $( this ),
				toggleContainer = toggleInput.closest( '.yith-wcbm-enable-badge' ),
				badgeID         = toggleContainer.data( 'badge-id' ),
				post_data       = {
					action      : yithWcbmMetaboxOptions.actions.toggleEnableBadge,
					security    : yithWcbmMetaboxOptions.security.toggleEnableBadge,
					badge_id    : badgeID,
					badge_enable: toggleInput.prop( 'checked' ) ? 'yes' : 'no'
				};
			block( toggleContainer );

			$.ajax( {
				type    : 'POST',
				dataType: 'json',
				data    : post_data,
				url     : yithWcbmMetaboxOptions.ajaxurl,
				success : function ( response ) {
					if ( !response[ 'success' ] ) {
						toggleInput.prop( 'checked', !toggleInput.prop( 'checked' ) );
					}
				},
				complete: function () {
					unblock( toggleContainer );
				}
			} );
		};// WP List


	if ( 'edit-yith-wcbm-badge' === yithWcbmMetaboxOptions.screenID ) {
		badgePreviewInWpList.each( scaleBadgeOnWpList );
		addBadgeButton.on( 'click', openModalToAddBadge );
		$( document ).on( 'click', '.yith-wcbm-enable-badge input', handleToggleEnableBadge );
		$( document ).on( 'click', '.yith_wcbm_actions .yith-plugin-fw__action-button--delete-action a', function ( e ) {
			e.preventDefault();
			e.stopPropagation();

			var url       = $( this ).attr( 'href' ),
				badgeName = $( this ).closest( 'tr' ).find( 'td.title a' ).html();

			yith.ui.confirm( {
				title            : yithWcbmMetaboxOptions.i18n.deleteBadgeModal.title,
				message          : yithWcbmMetaboxOptions.i18n.deleteBadgeModal.message.replace( '%s', badgeName ),
				confirmButtonType: 'delete',
				confirmButton    : yithWcbmMetaboxOptions.i18n.deleteBadgeModal.confirmButton,
				closeAfterConfirm: false,
				onConfirm        : function () {
					window.location.href = url;
				}
			} );
		} );

	} else {
		$( document ).on( 'change keyup', '.yith-wcbm-badge-options-metabox input, .yith-wcbm-badge-options-metabox select, .yith-wcbm-badge-options-metabox textarea', badge.updatePreview );
		$( document ).on( 'change keyup click', '#_text-container, .mce-container, .mce-toolbar-grp', badge.updatePreview );
		$( document ).on( 'yith-wcbm-editor-initialized', '.yith-wcbm-badge-options-metabox textarea', metabox.initEditorBackground );

		metabox.init();
	}
} );

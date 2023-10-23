/* global yithWcbmMetaboxOptions */

jQuery( function ($) {
	var addBadgeButton          = $( '.edit-php.post-type-yith-wcbm-badge .page-title-action, .edit-php.post-type-yith-wcbm-badge .yith-plugin-fw__list-table-blank-state__cta' ),
		badgePreviewInWpList    = $( '.column-yith_wcbm_preview .yith-wcbm-badge' ),
		isChecked               = function (input) {
			input = input instanceof jQuery ? input : $( this );
			return input.is( ':checked' );
		},
		block                   = function (element) {
			element.block( {
				message   : '',
				overlayCSS: {backgroundColor: '#FFFFFF', opacity: 0.8, cursor: 'wait'},
			} );
		},
		unblock                 = function (element) {
			element.unblock();
		},
		elementLineHeightFix    = function (element) {
			$( element ).find( 'p' ).map( function (index, paragraph) {
				var fontSize = $( paragraph ).find( 'span' ).css( 'font-size' );
				if ( fontSize ) {
					$( paragraph ).css( 'line-height', fontSize );
				}
				return paragraph;
			} );
			return $( element );
		},
		badge                   = {
			preview                 : $( '.yith-wcbm-preview-metabox .yith-wcbm-badge' ),
			container               : $( '.yith-wcbm-preview-metabox .yith-wcbm-preview-container' ),
			fields                  : {
				allRows          : $( '#yith-wcbm-metabox .yith-wcbm-badge-type-row ~ .the-metabox' ),
				type             : $( '#_type' ),
				width            : $( '#_size-dimension-width' ),
				height           : $( '#_size-dimension-height' ),
				backgroundColor  : $( '#_background_color' ),
				textColor        : $( '#_text_color' ),
				padding          : {
					top   : $( '#_padding-dimension-top' ),
					right : $( '#_padding-dimension-right' ),
					bottom: $( '#_padding-dimension-bottom' ),
					left  : $( '#_padding-dimension-left' ),
					unit  : $( '#_padding input.yith-plugin-fw-dimensions__unit__value' ),
				},
				borderRadius     : {
					topLeft    : $( '#_border_radius-dimension-top-left' ),
					topRight   : $( '#_border_radius-dimension-top-right' ),
					bottomRight: $( '#_border_radius-dimension-bottom-right' ),
					bottomLeft : $( '#_border_radius-dimension-bottom-left' ),
					unit       : $( '#_border_radius input.yith-plugin-fw-dimensions__unit__value' ),
				},
				fixedPosition    : $( '.yith-wcbm-position-field input' ),
				fixedAlignment   : $( '.yith-wcbm-alignment-field input' ),
				fixedPositionRows: $( '.yith-wcbm-position-fixed-row, .yith-wcbm-alignment-fixed-row' ),
				alignmentPreview : $( '#_alignment-container .yith-wcbm-alignment-preview' ),
				textArea         : $( '.yith-wcbm-text-editor-row textarea' ),
				imageBadges      : {
					container: $( '#_image' ),
					getInputs: function () { return $( '#_image input' );},
					assetsUrl: $( '#_image-container .yith-wcbm-badge-library__badges' ).data( 'assets-url' ),
					labels   : $( '#_image-container .yith-wcbm-badge-list-element' ),
					upload   : {
						container: $( '.yith-wcbm-upload-image' ),
						img      : $( '.yith-wcbm-upload-image img' ),
						url      : $( '#yith-wcbm-upload-image-attachment-url' ),
						id       : $( '#yith-wcbm-upload-image-attachment-id' ),
					},
				},
			},
			getCheckedValue         : function (option) {
				var optionToFilter = badge.fields[ option ];
				if ( 'imageBadges' === option ) {
					optionToFilter = optionToFilter.getInputs();
				}

				return optionToFilter ? optionToFilter.filter( isChecked ).val() : false;
			},
			getFixedPosition        : function () {
				return badge.getCheckedValue( 'fixedPosition' );
			},
			getFixedAlignment       : function () {
				return badge.getCheckedValue( 'fixedAlignment' );
			},
			getSelectedImageBadge   : function () {
				return badge.getCheckedValue( 'imageBadges' );
			},
			getSelectedImageBadgeID : function () {
				return badge.getCheckedValue( 'imageBadges' ).replace( '.svg', '' );
			},
			getSelectedBadgeId      : function () {
				var badgeID = false;
				if ( badge.isType( 'image' ) ) {
					badgeID = badge.getSelectedImageBadge();
				}
				return badgeID ? Math.abs( badgeID.replace( '.svg', '' ) ) : false;
			},
			getFixedPositionCss     : function () {
				var position       = badge.getFixedPosition(),
					alignment      = badge.getFixedAlignment(),
					positionRules  = {
						top   : {
							top: 0,
						},
						middle: {
							top: '50%',
						},
						bottom: {
							bottom: 0,
						},
					},
					alignmentRules = {
						left  : {
							left: 0,
						},
						center: {
							left: '50%',
						},
						right : {
							right: 0,
						},
					},
					css            = {
						left  : 'auto',
						right : 'auto',
						top   : 'auto',
						bottom: 'auto',
					};
				if ( alignmentRules[ alignment ] ) {
					$.each( alignmentRules[ alignment ], function (ruleName, ruleValue) {
						css[ ruleName ] = ruleValue;
					} );
				}
				if ( positionRules[ position ] ) {
					$.each( positionRules[ position ], function (ruleName, ruleValue) {
						css[ ruleName ] = ruleValue;
					} );
				}

				return css;
			},
			getSelectedImageBadgeURL: function () {
				return badge.fields.imageBadges.assetsUrl + '/' + badge.getSelectedImageBadge();
			},
			getBadgeUrl             : function () {
				var url = '';
				if ( badge.isType( 'image' ) ) {
					url = badge.getSelectedImageBadgeURL();
				}

				return url;
			},
			getType                 : function () {
				return badge.fields.type.val();
			},
			isType                  : function (type) {
				return badge.getType() === type;
			},
			getWidthCSS             : function () {
				var width = badge.fields.width.val();

				return width > 0 ? width + 'px' : 'auto';
			},
			getHeightCSS            : function () {
				var height = badge.fields.height.val();

				return height > 0 ? height + 'px' : 'auto';
			},
			getBackgroundColor      : function () {
				return badge.fields.backgroundColor.val();
			},
			getTextColor            : function () {
				return badge.isType( 'advanced' ) ? badge.fields.textColor.val() : badge.getTextColorFromText();
			},
			getTextColorFromText    : function () {
				var metaboxBody = $( metabox.getTextEditorBody() ).html(),
					text        = metaboxBody ? metaboxBody : badge.getTextEditorContent(),
					$elements   = $( '<div>' + text + '</div>' ).find( 'p,span' ),
					i           = -1;
				while ( $elements[ ++i ] ) {
					var color = $( $elements[ i ] ).css( 'color' );
					if ( color && (color.indexOf( 'rgb' ) !== -1 || color.indexOf( '#' ) !== -1 || color.indexOf( 'hsl' ) !== -1) ) {
						return color;
					}
				}
				return '#3c434a';
			},
			getDimensions           : function (option, dimensions) {
				var defaults = {
					first : 'top',
					second: 'right',
					third : 'bottom',
					fourth: 'left',
				};
				dimensions   = ! dimensions ? defaults : dimensions;
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
			getPadding              : function () {
				return badge.getDimensions( 'padding' );
			},
			getBorderRadius         : function () {
				var dimensions = {
					first : 'topLeft',
					second: 'topRight',
					third : 'bottomRight',
					fourth: 'bottomLeft',
				};
				return badge.getDimensions( 'borderRadius', dimensions );
			},
			getTransform            : function () {
				var position             = badge.getFixedPosition(),
					alignment            = badge.getFixedAlignment(),
					positionToTransform  = {
						top   : '0',
						middle: '-50%',
						bottom: '0',
					},
					alignmentToTransform = {
						left  : '0',
						center: '-50%',
						right : '0',
					};

				return 'translate( ' + alignmentToTransform[ alignment ] + ', ' + positionToTransform[ position ] + ' )';
			},
			getTextEditorContent    : function () {
				var textEditor = tinymce.editors.filter( function (editor) {return editor.initialized && '_text' === editor.id; } ),
					content    = $( metabox.getTextEditorBody() ).html();
				if ( ! content ) {
					content = elementLineHeightFix( $( '<div>' + badge.fields.textArea.val() + '</div>' ) ).html();
				}

				return content;
			},
			getCSS                  : function () {
				var positionCss = badge.getFixedPositionCss(),
					css         = {
						padding        : 0,
						'border-radius': 0,
						transform      : badge.getTransform(),
					};
				css             = Object.assign( {}, css, positionCss );
				switch ( badge.getType() ) {
					case 'text':
						css.width              = badge.getWidthCSS();
						css.height             = badge.getHeightCSS();
						css.padding            = badge.getPadding();
						css[ 'border-radius' ] = badge.getBorderRadius();
						break;
					case 'image':
						css.width  = '';
						css.height = '';
						break;
				}

				return css;
			},
			replacePreviewWith      : function ($newPreview) {
				badge.preview.removeAttr( 'class' );
				badge.preview.addClass( ((Array)( $newPreview[ 0 ].classList )).join( ' ' ) );
				badge.preview.html( $newPreview.html() );
			},
			updatePreview           : function (e) {
				badge.preview.css( 'background-color', '' );
				switch ( badge.getType() ) {
					case 'text':
						badge.replacePreviewWith( $( '<div class="yith-wcbm-badge yith-wcbm-badge-text"><div class="yith-wcbm-badge-text">' + badge.getTextEditorContent() + '</div></div>' ) );
						badge.updateTextAreaValue();
						break;
					case 'image':
						var content;
						if ( yithWcbmMetaboxOptions[ 'imageBadges' ] && yithWcbmMetaboxOptions[ 'imageBadges' ][ badge.getSelectedImageBadgeID() ] ) {
							content = yithWcbmMetaboxOptions[ 'imageBadges' ][ badge.getSelectedImageBadgeID() ];
						} else {
							content = '<img src="' + badge.getBadgeUrl() + '" alt="">';
						}
						badge.replacePreviewWith( $( '<div class="yith-wcbm-badge yith-wcbm-badge-image ' + ('upload' === badge.getSelectedImageBadge() ? 'yith-wcbm-badge-image-uploaded' : '') + '">' + content + '</div>' ) );
						break;
				}

				badge.updateCssVariables();
				badge.preview.css( badge.getCSS() );
			},
			updateTextAreaValue     : function () {
				badge.fields.textArea.val( badge.getTextEditorContent() );
			},
			unsetCssVariables       : function () {
				var variables = ['primary', 'text'];
				for ( var i = 0; i < variables.length; i++ ) {
					document.documentElement.style.setProperty( '--badge-' + variables[ i ] + '-color', 'unset' );
				}
			},
			updateCssVariables      : function (color) {
				color = ! color ? badge.getBackgroundColor() : color;

				var cssVariables = {
					'primary': color,
					'text'   : badge.getTextColor(),
				};

				$.each( cssVariables, function (name, value) {
					document.documentElement.style.setProperty( '--badge-' + name + '-color', value );
				} );
			},
		},
		metabox                 = {
			selectors                : {
				badgeType           : '.yith-wcbm-badge-type',
				badgeTitle          : '#yith-wcbm-title',
				backgroundColorInput: '#_background_color',
				textColorInput      : '#_text_color',
				position            : '.yith-wcbm-position-field input',
				alignment           : '.yith-wcbm-alignment-field input',
				imageField          : '.yith-wcbm-badge-list-element input',
			},
			init                     : function () {
				$( document ).on( 'change', metabox.selectors.backgroundColorInput, metabox.updateBackgroundColor );
				$( document ).on( 'change', metabox.selectors.textColorInput, metabox.updateTextColor );
				$( document ).on( 'change', metabox.selectors.badgeType, metabox.handleBadgeTypeChange );
				$( document ).on( 'change', metabox.selectors.position, metabox.handlePositionChange );
				$( document ).on( 'change', metabox.selectors.imageField, metabox.handleBadgeImageSelection );
				$( document ).on( 'change keyup invalid', metabox.selectors.badgeTitle, metabox.checkInputValidity );
				$( metabox.selectors.badgeTitle ).on( 'invalid', metabox.checkInputValidity );

				metabox.initEditors();
				metabox.initSelect();
				metabox.initImageSelectors();
				metabox.initColorPickers();

				switch ( badge.getType() ) {
					case 'image':
						badge.fields.imageBadges.getInputs().filter( isChecked ).trigger( 'change' );
						break;
				}

				$( '#_type' ).trigger( 'change' );
				$( '#yith-wcbm-title' ).val( $( '#title' ).val() );
				badge.updatePreview();
			},
			initEditorBackground     : function () {
				badge.fields.backgroundColor.trigger( 'change' );
			},
			initImageSelectors       : function () {
				$( '.yith-wcbm-badge-library__badges' ).each( function (index, imagesContainer) {
					var selectedImage         = $( imagesContainer ).find( '.yith-wcbm-badge-list-element--selected' ),
						offsetTop             = selectedImage[ 0 ] ? selectedImage[ 0 ].offsetTop : 0,
						height                = selectedImage.outerHeight();
					imagesContainer.scrollTop = offsetTop - height / 2 - 69;
				} );
			},
			setBackgroundColor       : function (color) {
				badge.fields.backgroundColor.val( color ).trigger( 'change' );
			},
			setTextColor             : function (color) {
				badge.fields.textColor.val( color ).trigger( 'change' );
			},
			getEditorFontSizes       : function () {
				var fontSizes = [];
				for ( var i = yithWcbmMetaboxOptions.editor.fontSize.min, cont = 1; i < yithWcbmMetaboxOptions.editor.fontSize.max; i += Math.ceil( cont / 10 ), cont++ ) {
					fontSizes.push( i + 'pt' );
				}
				fontSizes.push( yithWcbmMetaboxOptions.editor.fontSize.max + 'pt' );
				return fontSizes.join( ' ' );
			},
			getTextEditorBody        : function () {
				if ( ! metabox.textEditorBody || ! metabox.textEditorBody.length ) {
					metabox.textEditorBody = $( '#_text_ifr' ).contents().find( 'body#tinymce' );
				}
				return metabox.textEditorBody;
			},
			initEditors              : function () {
				$( 'textarea.yith-wcbm-text-editor:not(.yith-wcbm-text-editor-initialized)' ).each( function () {
					var textarea      = $( this ),
						textareaId    = textarea.attr( 'id' ),
						fixLineHeight = function () {
							var $textEditorContent = $( this.iframeElement ).contents().find( '#tinymce' );
							$textEditorContent.find( 'p' ).map( function (index, paragraph) {
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
							init_instance_callback: function (editor) {
								editor.on( 'keyup', function () {
									var $textEditorContent = $( this.iframeElement ).contents().find( '#tinymce' );
									elementLineHeightFix( $textEditorContent );
								} );
								editor.on( 'keyup', badge.updatePreview );
								$( editor.iframeElement ).contents().find( 'html,body' ).attr( 'style', 'height: 100%; font-family: "Open Sans", sans-serif; line-height: normal !important;' );
								textarea.trigger( 'yith-wcbm-editor-initialized' );
							},
						},
						quicktags   : {buttons: 'strong,em,block,del,ins,img,ul,ol,li,code,more,close'},
						mediaButtons: false,
					} );
				} );
			},
			initSelect               : function () {
				$( '.yith-enhanced-select' ).selectWoo( {minimumResultsForSearch: Infinity} );
			},
			initColorPickers         : function () {
				badge.fields.backgroundColor.wpColorPicker( {change: metabox.updateBackgroundColor} );
				badge.fields.textColor.wpColorPicker( {change: metabox.updateTextColor} );
			},
			setTextEditorCss         : function (css) {
				var textEditorBody = metabox.getTextEditorBody();
				textEditorBody.css( 'background', '' );
				textEditorBody.css( css );
			},
			updateBackgroundColor    : function (e, ui) {
				if ( ! badge.isType( 'image' ) ) {
					var color = ui && ui.hasOwnProperty( 'color' ) ? ui.color.toString() : $( this ).val();
					metabox.setTextEditorCss( {'background-color': color} );
					badge.updatePreview();
					badge.updateCssVariables( color );
				}
			},
			updateTextColor          : function (e, ui) {
				var color = ui && ui.hasOwnProperty( 'color' ) ? ui.color.toString() : $( this ).val();
				document.documentElement.style.setProperty( '--badge-text-color', color );
			},
			handleBadgeTypeChange    : function () {
				var type = $( this ).val();
				badge.fields.allRows.hide();
				$( '.yith-wcbm-visible-if-' + type ).show();
				switch ( type ) {
					case 'image':
						$( '#_image-container input:checked' ).trigger( 'change' );
						break;
					case 'text':
						badge.fields.backgroundColor.trigger( 'change' );
						break;
				}
			},
			handlePositionChange     : function () {
				var $alignmentPreview = badge.fields.alignmentPreview,
					position          = $( this ).val();
				$alignmentPreview.removeClass( 'yith-wcbm-alignment-preview--top yith-wcbm-alignment-preview--middle yith-wcbm-alignment-preview--bottom' );
				$alignmentPreview.addClass( 'yith-wcbm-alignment-preview--' + position );
			},
			handleBadgeImageSelection: function () {
				var selectedInput   = $( this ),
					imagesContainer = selectedInput.closest( '.yith-wcbm-badge-library__badges' );
				imagesContainer.find( '.yith-wcbm-badge-list-element--selected' ).removeClass( 'yith-wcbm-badge-list-element--selected' );
				selectedInput.closest( '.yith-wcbm-badge-list-element' ).addClass( 'yith-wcbm-badge-list-element--selected' );
			},
			checkInputValidity       : function () {
				var $input       = $( this ),
					$description = $input.closest( '.yith-plugin-fw-metabox-field-row' ).find( '.description' ),
					color        = $input.is( ':valid' ) ? '' : '#ea0034';
				$input.css( 'border-color', color );
				$description.css( 'color', color );
			},
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
		openModalToAddBadge     = function (e) {
			e.preventDefault();
			yith.ui.modal( {
				title                     : yithWcbmMetaboxOptions.addBadgeModal.title,
				content                   : yithWcbmMetaboxOptions.addBadgeModal.content,
				closeWhenClickingOnOverlay: true,
				width                     : 700,
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
					badge_enable: toggleInput.prop( 'checked' ) ? 'yes' : 'no',
				};
			block( toggleContainer );

			$.ajax( {
				type    : 'POST',
				dataType: 'json',
				data    : post_data,
				url     : yithWcbmMetaboxOptions.ajaxurl,
				success : function (response) {
					if ( ! response[ 'success' ] ) {
						toggleInput.prop( 'checked', ! toggleInput.prop( 'checked' ) );
					}
				},
				complete: function () {
					unblock( toggleContainer );
				},
			} );
		};// WP List


	if ( 'edit-yith-wcbm-badge' === yithWcbmMetaboxOptions.screenID ) {
		badgePreviewInWpList.each( scaleBadgeOnWpList );
		addBadgeButton.on( 'click', openModalToAddBadge );
		$( document ).on( 'click', '.yith-wcbm-enable-badge input', handleToggleEnableBadge );
		$( document ).on( 'click', '.yith_wcbm_actions .yith-plugin-fw__action-button--delete-action a', function (e) {
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
				},
			} );
		} );

	} else {
		$( document ).on( 'change keyup', '.yith-wcbm-badge-options-metabox input, .yith-wcbm-badge-options-metabox select, .yith-wcbm-badge-options-metabox textarea', badge.updatePreview );
		$( document ).on( 'change keyup click', '#_text-container, .mce-container, .mce-toolbar-grp', badge.updatePreview );
		$( document ).on( 'yith-wcbm-editor-initialized', '.yith-wcbm-badge-options-metabox textarea', metabox.initEditorBackground );

		metabox.init();
	}
} );

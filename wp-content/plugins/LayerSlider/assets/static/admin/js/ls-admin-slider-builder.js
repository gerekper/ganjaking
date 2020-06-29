// Stores the database ID of
// currently editing slider.
var LS_sliderID = 0,


// Store the indexes of currently
// selected items on the interface.
LS_activeSlideIndex = 0,
LS_activeLayerIndexSet = [],
LS_activeLayerPageIndex = 0,
LS_activeLayerTransitionTab = 0,
LS_activeScreenType = 'desktop',

LS_lastSelectedLayerIndex = 0,

// Stores all preview items using an object
// to easily add and modify items.
//
// NOTE: it's not a jQuery collection, but a
// collection of jQuery-enabled elements.
LS_previewItems = [],


// Object references, pointing to the currently selected
// slide/layer data. These are not working copies, any
// change made will affect the main data object. This makes
// possible to avoid issues caused by inconsistent data.
LS_activeSlideData = {},
LS_activeLayerDataSet = [],
LS_activeStaticLayersDataSet = [],


// These objects will be filled with the default slide/layer
// properties when needed. They purpose as a caching mechanism
// for bulk slide/layer creation.
LS_defaultSliderData = {},
LS_defaultSlideData = {},
LS_defaultLayerData = {},


// Stores all previous editing sessions
// to cache results and speed up operations.
LS_editorSessions = [],

// Flag for unsaved changes on site.
// We use this to display a warning
// for the user when leaving the page.
LS_editorIsDirty = false,


// Flag for transformed layers due to
// combo box preview, which needs to
// be updated after closing the combo box.
LS_comboBoxIsDirty = false,


// Flag for dragging operations to better
// handle layer selection in a group-select
// scenario.
LS_layerWasDragged = false,


// Stores default UI settings of
// editing sessions.
LS_defaultEditorSession = {
	slideIndex: LS_activeSlideIndex,
	layerIndex: LS_activeLayerIndexSet,
	zoomSlider: 100,
	zoomAutoFit: true
},


// Stores temporary data for all
// copy & pate operations.
LS_clipboard = {},


// Stores the main UI elements
LS_previewZoom = 1,
LS_previewArea,
LS_previewHolder,
LS_previewWrapper,

// Context menu
LS_contextMenuTop = 10,
LS_contextMenuLeft = 10,


LS_transformStyles = [
	'rotation',
	'rotationX',
	'rotationY',
	'scaleX',
	'scaleY',
	'skewX',
	'skewY'
];

var $lasso = jQuery();

// Utility functions to perform
// commonly used tasks.
var LS_Utils = {

	convertDateToUTC: function(date) {
		return new Date(
				date.getUTCFullYear(),
				date.getUTCMonth(),
				date.getUTCDate(),
				date.getUTCHours(),
				date.getUTCMinutes(),
				date.getUTCSeconds()
		);
	},

	jsTimeZoneID: function() {
		var offset = new Date().getTimezoneOffset(), o = Math.abs(offset);
		return (offset < 0 ? "+" : "-") + ("00" + Math.floor(o / 60)).slice(-2) + ":" + ("00" + (o % 60)).slice(-2);
	},

	dataURItoBlob: function( dataURI, type ) {

		type = type || 'image/png';

		var binary = atob(dataURI.split(',')[1]);
		var array = [];
		for(var i = 0; i < binary.length; i++) {
			array.push(binary.charCodeAt(i));
		}
		return new Blob([new Uint8Array(array)], { type: type });
	},

	moveArrayItem: function(array, from, to) {
		if( to === from ) return;

		var target = array[from];
		var increment = to < from ? -1 : 1;

		for(var k = from; k != to; k += increment){
			array[k] = array[k + increment];
		}
		array[to] = target;
	},


	toAbsoluteURL: function(url) {
		// Handle absolute URLs (with protocol-relative prefix)
		// Example: //domain.com/file.png
		if (url.search(/^\/\//) != -1) {
			return window.location.protocol + url;
		}

		// Handle absolute URLs (with explicit origin)
		// Example: http://domain.com/file.png
		if (url.search(/:\/\//) != -1) {
			return url;
		}

		// Handle absolute URLs (without explicit origin)
		// Example: /file.png
		if (url.search(/^\//) != -1) {
			return window.location.origin + url;
		}

		// Handle relative URLs
		// Example: file.png
		var base = window.location.href.match(/(.*\/)/)[0];
		return base + url;
	},

	// credits: http://phpjs.org/functions/strip_tags/
	stripTags: function(input, allowed) {

		allowed = (((allowed || '') + '')
			.toLowerCase()
			.match(/<[a-z][a-z0-9]*>/g) || [])
			.join('');
		var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi,
			commentsAndPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;
		return input.replace(commentsAndPhpTags, '')
			.replace(tags, function($0, $1) {
				return allowed.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 : '';
			});
	},

	// credits: http://phpjs.org/functions/nl2br/
	nl2br: function(str, is_xhtml) {
		var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br ' + '/>' : '<br>'; // Adjust comment to avoid issue on phpjs.org display
		return (str + '')
			.replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
	},

	// credits: http://stackoverflow.com/questions/3169786/clear-text-selection-with-javascript
	removeTextSelection: function() {
		var selection = window.getSelection ? window.getSelection() : document.selection ? document.selection : null;
		if(!!selection) selection.empty ? selection.empty() : selection.removeAllRanges();
	},

	// credits: http://locutus.io/php/stripslashes/
	stripslashes: function(str) {
	  return (str + '')
		.replace(/\\(.?)/g, function (s, n1) {
		switch (n1) {
			case '\\':
			  return '\\'
			case '0':
			  return '\u0000'
			case '':
			  return ''
			default:
			  return n1
		  }
		});
	},


	// credits: http://locutus.io/php/parse_url/
	parse_url: function(str, component) {
		var query;

		var mode = (typeof require !== 'undefined' ? require('../info/ini_get')('locutus.parse_url.mode') : undefined) || 'php';

		var key = [
			'source',
			'scheme',
			'authority',
			'userInfo',
			'user',
			'pass',
			'host',
			'port',
			'relative',
			'path',
			'directory',
			'file',
			'query',
			'fragment'
		];

		// For loose we added one optional slash to post-scheme to catch file:/// (should restrict this)
		var parser = {
			php: new RegExp([
				'(?:([^:\\/?#]+):)?',
				'(?:\\/\\/()(?:(?:()(?:([^:@\\/]*):?([^:@\\/]*))?@)?([^:\\/?#]*)(?::(\\d*))?))?',
				'()',
				'(?:(()(?:(?:[^?#\\/]*\\/)*)()(?:[^?#]*))(?:\\?([^#]*))?(?:#(.*))?)'
			].join('')),
			strict: new RegExp([
				'(?:([^:\\/?#]+):)?',
				'(?:\\/\\/((?:(([^:@\\/]*):?([^:@\\/]*))?@)?([^:\\/?#]*)(?::(\\d*))?))?',
				'((((?:[^?#\\/]*\\/)*)([^?#]*))(?:\\?([^#]*))?(?:#(.*))?)'
			].join('')),
			loose: new RegExp([
				'(?:(?![^:@]+:[^:@\\/]*@)([^:\\/?#.]+):)?',
				'(?:\\/\\/\\/?)?',
				'((?:(([^:@\\/]*):?([^:@\\/]*))?@)?([^:\\/?#]*)(?::(\\d*))?)',
				'(((\\/(?:[^?#](?![^?#\\/]*\\.[^?#\\/.]+(?:[?#]|$)))*\\/?)?([^?#\\/]*))',
				'(?:\\?([^#]*))?(?:#(.*))?)'
			].join(''))
		};

		var m = parser[mode].exec(str);
		var uri = {};
		var i = 14;

		while (i--) {
			if (m[i]) {
				uri[key[i]] = m[i];
			}
		}

		if (component) {
			return uri[component.replace('PHP_URL_', '').toLowerCase()];
		}

		if (mode !== 'php') {
			var name = (typeof require !== 'undefined' ? require('../info/ini_get')('locutus.parse_url.queryKey') : undefined) || 'queryKey';
			parser = /(?:^|&)([^&=]*)=?([^&]*)/g;
			uri[name] = {};
			query = uri[key[12]] || '';
			query.replace(parser, function ($0, $1, $2) {
				if ($1) {
					uri[name][$1] = $2;
				}
			});
		}

		delete uri.source;
		return uri;
	}
};


var LS_GUI = {

	updateImagePicker: function( $picker, image, updateProperties ) {

		updateProperties = updateProperties || {};

		if( typeof $picker === 'string' ) {
			$picker = jQuery('input[name="'+$picker+'"]').next();
		}

		if( image === 'useCurrent' ) {
			image = $picker.find('img').attr('src');
		}

		if( image && image.indexOf('blank.gif') !== -1 ) {
			if( ! updateProperties.fromPost ) {
				image = false;
			}
		}

		$picker
			.removeClass('has-image not-set')
			.addClass( image ? 'has-image' : 'not-set' )
			.find('img').attr('src', image ||  lsTrImgPath+'/blank.gif' );
	},


	updateLinkPicker: function( $input ) {

		if( typeof $input === 'string' ) {
			$input = jQuery('input[name="'+$input+'"]');
		}

		// Do nothing if no input found. Revisions and other pages
		// might load the Slider Builder script without an active
		// editor session in place.
		if( ! $input.length ) { return; }

		var $holder 		= $input.closest('.ls-slide-link'),
			inputName 		= $input.attr('name'),
			inputVal 		= $input.val(),
			isSlide 		= $holder.closest('.ls-slide-options').length,
			dataArea 		= isSlide ? LS_activeSlideData.properties : LS_activeLayerDataSet[0],

			$linkIdInput 	= $holder.find('input[name="linkId"]'),
			$linkNameInput 	= $holder.find('input[name="linkName"]'),
			$linkTypeInput 	= $holder.find('input[name="linkType"]'),


			linkId 			= $linkIdInput.val(),
			linkName 		= $linkNameInput.val(),
			linkType 		= $linkTypeInput.val(),
			l10nKey;

			// Normalize HTML entities
			linkName 		= jQuery('<textarea>').html(linkName).text();


		// Smart Link
		if( linkName && ( ( linkId && '#' === linkId.substr(0, 1) ) || ( inputVal && '#' === inputVal.substr(0, 1) ) ) ) {

			var placeholder = LS_l10n.SBLinkSmartAction.replace( '%s', linkName );

			$holder.addClass('has-link');

			// If linkId is not yet set, copy the input field value
			if( ! linkId.length ) {
				$linkIdInput.val( inputVal );
			}
			$input.val( placeholder ).prop('disabled', true);


		// URL from Dynamic Layer
		} else if( ( linkId && '[post-url]' === linkId ) || ( inputVal && '[post-url]' === inputVal ) ) {
			$holder.addClass('has-link');
			$input.val( LS_l10n.SBLinkPostDynURL ).prop('disabled', true);
			$linkIdInput.val('[post-url]');


		// Specific WP Post/Page
		} else if( linkId && linkName && linkType ) {

			l10nKey = 'SBLinkText'+ucFirst( linkType );

			$holder.addClass('has-link');
			$input.val( LS_l10n[l10nKey].replace('%s', linkName) ).prop('disabled', true);


		// No formatted link
		} else {
			$holder.removeClass('has-link');
			$input.prop('disabled', false);
		}


		// Update data source
		dataArea[ inputName ] 	= $input.val();
		dataArea.linkId 		= $linkIdInput.val();
		dataArea.linkName 		= $linkNameInput.val();
		dataArea.linkType 		= $linkTypeInput.val();

	},

	deeplinkSection: function() {
		var hash 		= document.location.hash.replace('#', ''),
			$target 	= jQuery('[data-deeplink="'+hash+'"]');

		if( $target.length ) {
			$target.click();
		}
	}
};



// Slide specific undo & redo operations.
// Uses callbacks to run any code stored by
// other methods. Supports custom parameters.
var LS_UndoManager = {

	index: -1,
	stack: [],
	limit: 50,

	add: function(cmd, name, updateInfo) {

		// Invalidate items higher on the stack when
		// called from an undo-ed position
		this.stack.splice(this.index + 1, this.stack.length - this.index);
		this.index = this.stack.length - 1;

		// Maintain stack limit
		if(this.stack.length > this.limit) {
			this.stack.shift();
		}

		// Verify 'history' object in slide
		if(!LS_activeSlideData.hasOwnProperty('history')) {
			LS_activeSlideData.history = [];
		}

		// Prepare updateInfo
		this.prepareUpdateInfo( updateInfo );

		// Add item
		this.stack.push({
			cmd: cmd,
			name: name,
			updateInfo: updateInfo
		});

		// Maintain buttons and stack index
		this.index = this.stack.length - 1;
		this.maintainButtons();

		// Mark unsaved changes on page
		LS_editorIsDirty = true;
	},


	// Replace this.stack when switching slides
	// to support slide-specific history.
	update: function() {

		// Verify 'history' object in slide
		if(!LS_activeSlideData.hasOwnProperty('history')) {
			LS_activeSlideData.history = [];
		}

		this.stack = LS_activeSlideData.history;
		this.index = this.stack.length - 1;

		if( LS_activeSlideData.meta && LS_activeSlideData.meta.undoStackIndex ) {
			this.index = LS_activeSlideData.meta.undoStackIndex;
		}

		this.maintainButtons();
	},


	// Merges new changes into the last item in
	// the UndoManager stack.
	merge: function( cmd, name, updateInfo ) {
		var lastItem = this.stack[ this.stack.length - 1 ];
		this.prepareUpdateInfo( updateInfo );
		jQuery.extend(true, lastItem.updateInfo, updateInfo);
	},


	// Empties the current slide's history and reset
	// every UndoManager-related properties
	empty: function() {
		LS_activeSlideData.history = [];

		if( LS_activeSlideData.meta && LS_activeSlideData.meta.undoStackIndex ) {
			LS_activeSlideData.meta.undoStackIndex = -1;
		}

		this.update();
	},


	undo: function() {
		if(this.stack[this.index]) {
			this.execute('undo', this.stack[this.index], this.stack[this.index-1]);
			this.index--;
			this.maintainButtons();
		}
	},


	redo: function() {
		if(this.stack[this.index+1]) {
			this.index++;
			this.execute('redo', this.stack[this.index], this.stack[this.index+1]);
			this.maintainButtons();
		}
	},


	prepareUpdateInfo: function( updateInfo ) {

		if( updateInfo && typeof updateInfo === 'object') {
			jQuery.each(updateInfo, function(key, val) {

				if( typeof val === 'object') {
					LS_UndoManager.prepareUpdateInfo( val );
					return true;
				}

				if( val === null || typeof val === 'undefined') {
					updateInfo[key] = '';
				}
			});
		}
	},

	maintainButtons: function(itemIndex) {

		var undoButton = jQuery('.ls-editor-undo'),
			redoButton = jQuery('.ls-editor-redo');

		LS_activeSlideData.meta.undoStackIndex = this.index;

		if(this.index !== -1) { undoButton.removeClass('disabled'); }
			else { undoButton.addClass('disabled'); }

		if(this.index < this.stack.length-1) { redoButton.removeClass('disabled'); }
			else { redoButton.addClass('disabled'); }
	},

	execute: function(action, item, followingItem) {

		var layerIndexSet = [];

		// Convert object to array to easily
		// handle multi-action steps.
		if( jQuery.type(item.updateInfo) === 'object' ) {
			item.updateInfo = [item.updateInfo];
		}

		// Iterate through actions in step.
		for(var c = 0; c < item.updateInfo.length; c++) {

			this.executeItem(
				item.cmd,
				item.updateInfo[c].itemIndex,
				item.updateInfo[c][action],
				item.updateInfo[c]
			);

			layerIndexSet.push( item.updateInfo[c].itemIndex );
		}

		this.restoreSelection( action, layerIndexSet, followingItem );
	},


	restoreSelection: function(action, layerIndexSet, followingItem) {

		if( followingItem && action === 'undo'  ) {

			var followingIndexSet = [];

			if( jQuery.type(followingItem.updateInfo) === 'object' ) {
				followingItem.updateInfo = [followingItem.updateInfo];
			}

			for(var c = 0; c < followingItem.updateInfo.length; c++) {
				followingIndexSet.push( followingItem.updateInfo[c].itemIndex );
			}
		}

		// Re-select affected layers if the selection has changed
		if( JSON.stringify( followingIndexSet || layerIndexSet) !== JSON.stringify(LS_activeLayerIndexSet)  ) {
			if( LS_activeSlideData.sublayers.length-1 < Math.max.apply(Math, followingIndexSet || layerIndexSet) ) {
				LayerSlider.selectLayer( [ LS_activeSlideData.sublayers.length-1] );
			} else {
				LayerSlider.selectLayer( followingIndexSet || layerIndexSet );
			}
		}
	},


	executeItem: function(command, itemIndex, updateInfo, item) {

		switch(command) {

			case 'slide.general':
				this.updateOptions(LS_activeSlideData.properties, itemIndex, updateInfo, 'slide');
				LayerSlider.updateSlidePreviews();
				LayerSlider.updateSlideInterfaceItems();
				LayerSlider.generatePreview();
				break;


			case 'slide.layers':
				if(jQuery.isEmptyObject(updateInfo.data)) {
					LayerSlider.removeLayer(itemIndex, { histroyEvent: true, requireConfirmation: false });
				} else {
					LayerSlider.addLayer(updateInfo.data, itemIndex, { histroyEvent: true });
					LayerSlider.selectLayer( item.selectIndex );
				}
				LS_DataSource.buildLayersList();
				LayerSlider.generatePreview();
				break;


			case 'layer.order':
				LS_Utils.moveArrayItem(LS_activeSlideData.sublayers, updateInfo.from, updateInfo.to);
				LS_DataSource.buildLayersList();
				LayerSlider.generatePreview();
				break;


			case 'layer.general':
				this.updateOptions(LS_activeSlideData.sublayers[itemIndex], itemIndex, updateInfo);
				LayerSlider.updateLayerInterfaceItems(itemIndex);
				LayerSlider.generatePreviewItem(itemIndex);
				LayerSlider.updatePreviewSelection();
				break;


			case 'layer.transition':
				this.updateOptions(LS_activeSlideData.sublayers[itemIndex].transition, itemIndex, updateInfo);
				LayerSlider.generatePreviewItem(itemIndex);
				break;


			case 'layer.style':
				this.updateOptions(LS_activeSlideData.sublayers[itemIndex].styles, itemIndex, updateInfo);
				LayerSlider.generatePreviewItem(itemIndex);
				LayerSlider.updatePreviewSelection();
				break;
		}
	},


	// Iterates over the updateInfo object,
	// overrides the keys in the provided
	// data object.
	updateOptions: function( data, index, updateInfo, area ) {

		area = area || 'layers';
		var parent = (area === 'slide') ? '.ls-slide-options' : '.ls-layers-table';

		jQuery.each(updateInfo, function(key, val) {

			//if( data.hasOwnProperty(key) ) {

				if( typeof val === 'object' ) {
					LS_UndoManager.updateOptions( data[key], index, updateInfo[key], area);
					return true;
				}

				// Update data
				data[key] = val;

				// Handle UI if it's the active layer
				if( area === 'slide' || (LS_activeLayerIndexSet.length == 1 && index == LS_activeLayerIndexSet[0]) ) {

					var $target = jQuery(parent+' '+'[name="'+key+'"]'),
						eventType = 'input';

					if( ! $target.is(':checkbox') ) {
						$target.val(val).trigger('input').trigger('keyup');
					}

					if($target.is(':checkbox')) {
						if(val) {
							$target.prop('checked', true);
							$target.next().addClass('on').removeClass('off');
						} else {
							$target.prop('checked', false);
							$target.next().addClass('off').removeClass('on');
						}
						return;

					} else if($target.is('select')) {
						eventType = 'change';
					}
					var jqEvent = jQuery.Event(eventType, { target: $target[0], UndoManagerAction: true });
					jQuery('#ls-layers').triggerHandler(jqEvent);
				}

			//}
		});
	},


	saveOriginalInputValues: function( $input ) {

		var prevVals 	= [],
			type 		= null,
			optionName 	= $input.attr('name'),
			optionValue = $input.is(':checkbox') ? ! $input.prop('checked') : $input.val();

		// Save input value as a generic solution
		$input.data('prevVal', optionValue );

		// Override saved data if it's a layer option
		if( $input.closest('.ls-sublayer-pages').length ) {

			if( $input.hasClass('sublayerprop') ) { type = 'transition'; }
				else if( $input.hasClass('auto') ) { type = 'styles'; }

			jQuery.each(LS_activeLayerDataSet, function(item, layerData) {
				var area = layerData;
				if( type ) { area = area[type]; }

				prevVals.push( area[optionName] );
			});

			$input.data('prevVal', prevVals );
		}
	},


	trackInputs: function( event, element ) {

		event = event || { type: 'change' };

		if( event.UndoManagerAction ) { return false; }

		var $input = jQuery(element),
			cmd, name, index;

		if( event.type.toLowerCase() == 'click' && $input.is('input,textarea') ) {
			return;
		}

		if( event.type.toLowerCase() !== 'change' ) {
			this.saveOriginalInputValues( $input );
			return;
		} else if( event.type.toLowerCase() === 'change' && $input.is(':checkbox') ) {
			this.saveOriginalInputValues( $input );
		}

		// Skip colorpickers, as they rapidly send change events
		if( $input.hasClass('ls-colorpicker') ) {
			return;
		}

		if( $input.closest('.ls-sublayer-pages').length ) {
			cmd = 'layer.general';
			name = LS_l10n.SBUndoLayer;
			index = LS_activeLayerIndexSet[0];

			if($input.hasClass('sublayerprop')) { cmd = 'layer.transition'; }
				else if($input.hasClass('auto')) { cmd = 'layer.style'; }

		} else if( $input.closest('.ls-slide-options').length ) {
			cmd = 'slide.general';
			name = LS_l10n.SBUndoSlide;
			index = LS_activeSlideIndex;

		} else {
			return true;
		}

		var updateInfo 	= [],
			optionName 	= $input.attr('name'),
			optionValue = $input.is(':checkbox') ? $input.prop('checked') : $input.val(),
			prevValue 	= $input.data('prevVal'),
			action 		= $input.hasClass('undomanager-merge') ? 'merge': 'add';

		if( ! optionName ) {
			return false;
		}

		// Layer option change, handle multiple
		// selection (if any).
		if( typeof prevValue === 'object' ) {

			jQuery.each(LS_activeLayerIndexSet, function( index, layerIndex ) {
				var undo = {}, redo = {};
					undo[ optionName ] = prevValue[ index ];
					redo[ optionName ] = optionValue;

				if( prevValue[ index ] !== optionValue ) {
					updateInfo.push({
						itemIndex: layerIndex,
						undo: undo,
						redo: redo
					});
				}
			});

		// Slide option change
		} else {

			if( prevValue !== optionValue ) {

				var undo = {}, redo = {};
					undo[ optionName ] = prevValue;
					redo[ optionName ] = optionValue;

				updateInfo.push({
					itemIndex: index,
					undo: undo,
					redo: redo
				});
			}
		}

		LS_UndoManager[action](cmd, name, updateInfo);
	}
};


var LayerSlider = {

	uploadInput: null,
	dragIndex: 0,
	timeout: 0,
	mediaCheckTimeout: 0,
	isSlidePreviewActive: false,
	isLayerPreviewActive: false,
	selectableTimeout: 0,

	getSliderSize: function() {

		var sliderProps = window.lsSliderData.properties, width, height;

		if( sliderProps.type && sliderProps.type === 'popup' ) {
			width 	= sliderProps.popupWidth  || 640;
			height 	= sliderProps.popupHeight || 360;
		} else {
			width 	= parseInt(sliderProps.sublayercontainer) || sliderProps.width || 1280;
			height 	= sliderProps.height || 720;
		}

		return {
			width: parseInt(width),
			height: parseInt(height)
		};
	},

	sliderIsEmpty: function( length ) {

		var isEmpty = true;

		jQuery.each(window.lsSliderData.layers, function(slideKey, slide) {

			if( jQuery.trim( slide.properties.background ) ) {
				isEmpty = false; return false;
			}

			jQuery.each(slide.sublayers, function(layerKey, layer) {

				// Has image
				if( layer.media === 'img' ) {
					if( layer.image ) {
						isEmpty = false; return false;
					}

				// Has textual content
				} else if( layer.html ) {
					isEmpty = false; return false;

				// Has visual content
				} else if( layer.styles.width || layer.styles.height ) {

					if( layer.html || layer.styles.background ) {
						isEmpty = false; return false;

					} else if( layer.styles['border-top'] || layer.styles['border-right'] || layer.styles['border-bottom'] || layer.styles['border-left'] ) {
						isEmpty = false; return false;
					}
				}
			});

			if( length && length === slideKey+1 ) {
				return false;
			}
		});

		return isEmpty;
	},


	selectMainTab: function(el) {

		var $tab = jQuery(el);

		// Select new tab
		$tab.addClass('active').siblings().removeClass('active');

		// Show new tab contents
		jQuery('#ls-pages .ls-page').removeClass('active');
		jQuery('#ls-pages .ls-page').eq( $tab.index() ).addClass('active');

		// Make sure to properly resize the transition options
		if( $tab.hasClass('layers') ) {
			kmUI.smartResize.set();
		}

		// Init CodeMirror
		if($tab.hasClass('callbacks')) {
			if(jQuery('.ls-callback-page .CodeMirror-code').length === 0) {
				LS_CodeMirror.init({ mode: 'javascript', autofocus : false, styleActiveLine : false });
				jQuery(window).scrollTop(0);
			}
		}
	},


	selectSettingsTab: function(li) {

		var $li 	= jQuery( li ),
			index 	= $li.index();

		if( $li.hasClass('locked') ) {
			return false;
		}

		$li.addClass('active').siblings().removeClass('active');
		jQuery('div.ls-settings-contents > table > tbody.active').removeClass('active');
		jQuery('div.ls-settings-contents > table > tbody').eq(index).addClass('active');

		// Make sure that the Slider Settings section is selected
		jQuery('#ls-main-nav-bar .settings').click();

		// Update hash for deeplinking
		document.location.hash = jQuery(li).data('deeplink');
	},


	addSlide: function( slideData, addProperties ) {

		var defaultProperties = {
			atIndex: window.lsSliderData.layers.length,
			selectSlide: true
		};

		addProperties = jQuery.extend( true, defaultProperties, addProperties );


		var hasSlideData = slideData ? true : false;

		if( ! slideData ) {

			// Get default data objects for slides and layers
			var slideData = jQuery.extend(true, {}, LS_DataSource.getDefaultSlideData());
				slideData = {
					properties: slideData,
					sublayers: []
				};
		}


		// Add new slide data to data source
		window.lsSliderData.layers.splice(
			addProperties.atIndex, 0, slideData
		);

		// Add new slide tab
		var $tab = jQuery( jQuery('#tmpl-slide-tab').text() ).appendToWithIndex('#ls-slide-tabs', addProperties.atIndex);

		if( hasSlideData && slideData.properties.title ) {
			$tab.find('.ls-slide-name input').val( slideData.properties.title );
		}

		if( hasSlideData && slideData.properties.skip ) {
			$tab.addClass('skip');
		}

		// Name new slide properly
		LayerSlider.updateSlidePreviews();
		LayerSlider.addSlideSortables();
		LS_activeLayerPageIndex = 0;
		LS_editorIsDirty = true;

		TweenLite.fromTo($tab[0], 0.4, {
			scale: 0
		},{
			scale: 1,
			onComplete: function() {

				// Show new slide, re-initialize
				// interactive features
				if( addProperties.selectSlide ) {
					$tab.children('.ls-slide-preview').click();
					LayerSlider.addLayerSortables();
				}
			}
		});
	},


	removeSlide: function( el ) {

		if( confirm( LS_l10n.SBRemoveSlide ) ) {

			// Get tab and menu item index
			var $tab = jQuery(el).closest('.ls-slide-tab'),
				$newTab = null,
				index = $tab.index(),
				transitionDuration;

			// Open next or prev layer
			if( $tab.next('.ls-slide-tab').length > 0 ) {
				$newTab = $tab.next();

			} else if( $tab.prev().length > 0 ) {
				$newTab = $tab.prev();
			}


			transitionDuration = window.lsSliderData.layers.length-1 < 1 ? 0 : 0.4;

			TweenLite.to($tab[0], transitionDuration, {
				scale: 0,
				ease:Power1.easeIn,
				onComplete: function() {
					setTimeout( function() {

						// Remove tab
						$tab.remove();

						// Remove tab and slide data
						window.lsSliderData.layers.splice( index, 1 );

						// Create a new slide if the last one
						// was removed
						if( window.lsSliderData.layers < 1 ) {
							LayerSlider.addSlide();
							return true;
						}

						if( $newTab && LS_activeSlideIndex === index ) {
							$newTab.children('.ls-slide-preview').click();
						}
					}, 30);
				}
			});
		}
	},


	selectSlide: function(slideIndex, selectProperties) {

		// Set selectProperties to an empty object by default
		selectProperties = selectProperties || {};

		// Bail out early if it's the currently active layer
		if( !selectProperties.forceSelect && LS_activeSlideIndex === slideIndex) { return false; }

		// Set active slide, highlight new tab
		jQuery('#ls-slide-tabs')
			.children()
			.eq(slideIndex)
			.addClass('active')

			.siblings()
			.removeClass('active');

		// Stop live preview
		LayerSlider.stopSlidePreview();
		LayerSlider.stopLayerPreview();

		// Set new slide index & data
		LS_activeSlideIndex = slideIndex;
		LS_activeSlideData = window.lsSliderData.layers[ slideIndex ];

		// Create the 'meta' object if not set
		if(!LS_activeSlideData.meta) {
			LS_activeSlideData.meta = {};
		}

		// Make sure to include new slide options in all cases
		var defaults = jQuery.extend( true, {}, LS_DataSource.getDefaultSlideData() );
		LS_activeSlideData.properties = jQuery.extend( true, defaults, LS_activeSlideData.properties );

		// Set active layer index set
		LS_activeLayerIndexSet = LS_activeSlideData.meta.activeLayers || [];
		LS_lastSelectedLayerIndex = LS_activeLayerIndexSet[0];

		// Add static layers
		LS_activeStaticLayersDataSet = LayerSlider.staticLayersForSlide( slideIndex );

		// Build slide
		LS_DataSource.buildSlide();
		LayerSlider.generatePreview();
		LayerSlider.selectLayer(LS_activeLayerIndexSet);
		LayerSlider.updatePreviewSelection();
		LS_UndoManager.update();
	},

	showSlideActions: function( el ) {

		var $slide 		= jQuery( el ).closest('.ls-slide-tab'),
			$controls 	= $slide.children(':not(.ls-slide-preview, .ls-slide-actions-sheet)'),
			$sheet 		= $slide.children('.ls-slide-actions-sheet');

		$slide.addClass('slide-actions-opened');

		$controls.addClass('ls-hidden');
		$sheet.removeClass('ls-hidden');

		TweenLite.to($sheet[0], 0.3, {
			y: 0
		});
	},


	hideSlideActions: function( ) {

		var $slide 	= jQuery('.ls-slide-tab.slide-actions-opened');

		if( $slide.length ) {

			var	$sheet 		= $slide.children('.ls-slide-actions-sheet'),
				$controls 	= $slide.children(':not(.ls-slide-preview, .ls-slide-actions-sheet)');

			$slide.removeClass('slide-actions-opened');

			$controls.removeClass('ls-hidden');

			TweenLite.to($sheet[0], 0.3, {
				y: -80
			});
		}
	},

	renameSlide: function( input ) {

		var $input = jQuery(input),
			$slide = $input.closest('.ls-slide-tab'),
			slideData = window.lsSliderData.layers[ $slide.index() ];

		slideData.properties.title = $input.val();
	},


	duplicateSlide: function( el ) {

		// Duplicate slide by using jQuery.extend()
		// to make sure it's a copy instead of an
		// object reference.
		var slideIndex 	= jQuery( el ).closest('.ls-slide-tab').index(),
			slideData 	= window.lsSliderData.layers[ slideIndex ],
			newSlideData = jQuery.extend( true, {}, slideData );

		// Assign new UUID
		newSlideData.properties.uuid = LS_DataSource.generateUUID();

		// Rename slide
		if( !! newSlideData.properties.title) {
			newSlideData.properties.title += ' copy';
		}

		LayerSlider.addSlide( newSlideData, {
			atIndex: slideIndex + 1
		} );
	},


	toggleSlideVisibility: function( el ) {

		var $tab = jQuery( el ).closest( '.ls-slide-tab' ),
			slideData = window.lsSliderData.layers[ $tab.index() ];

		// Current hidden, show it
		if( slideData.properties.skip ) {
			slideData.properties.skip = false;
			$tab.removeClass('skip');

		// Hide it
		} else {
			slideData.properties.skip = true;
			$tab.addClass('skip');
		}
	},


	setCustomSlideProperties: function( event, element ) {

		var $tr = jQuery(element).closest('tr'),
			$inputs = jQuery('input', $tr );

		if( ! $inputs.eq(0).val() && ! $inputs.eq(1).val() ) {
			$tr.remove();
		}


		var properties = LS_activeSlideData.properties.customProperties = {};

		jQuery('.ls-custom-slide-properties tr:not(:last-child)').each(function() {

			var $key = jQuery('td:first-child input', this),
				$val = jQuery('td:last-child input', this),
				key  = $key.val(),
				val  = $val.val();

			if( key && /^[a-zA-Z]([a-zA-Z0-9_-]+)$/.test( key ) ) {
				$key.removeClass('error');
				properties[ key ] = val;

			} else {
				$key.addClass('error');
			}
		});
	},


	updateCustomSlideProperties: function( ) {

		var slideData = LS_activeSlideData.properties;

		slideData.customProperties = slideData.customProperties || {};

		var properties = LS_activeSlideData.properties.customProperties || {},
			$customProps = jQuery('.ls-custom-slide-properties');

		// Sort keys
		Object.keys( properties ).sort().forEach( function( key ) {
			var value = properties[ key ];
			delete properties[ key ];
			properties[ key ] = value;
		});

		// Reset custom properties fields
		jQuery('tr:not(:last-child)', $customProps).remove();

		// Fill in custom properties
		jQuery.each(properties, function(key, val) {
			jQuery('tr:last-child input:eq(0)', $customProps).val( key );
			jQuery('tr:last-child input:eq(1)', $customProps).val( val ).trigger('keyup');
		});
	},



	toggleAdvancedSlideOptions: function( el ) {

		var $el 	= jQuery(el),
			$target = jQuery('.ls-slide-options tr.ls-advanced');

		if( $el.hasClass('ls-opened') ) {
			$el.removeClass('ls-opened');
			$target.addClass('ls-hidden');
		} else {
			$el.addClass('ls-opened');
			$target.removeClass('ls-hidden');
		}
	},


	setPreviewZoom: function( value ) {

		LS_previewZoom = value;

		jQuery('.ls-editor-slider-val').text(''+Math.round(value * 100)+'%');

		jQuery( '.ls-preview-transform' ).css({
			transform: 'scale('+value+')'
		}).parent().trigger('zoom');

		var sliderSize = LayerSlider.getSliderSize();

		jQuery( '.ls-preview-size' ).css({
			width: sliderSize.width * value,
			height: sliderSize.height * value
		});

		LayerSlider.updatePreviewSelection();
	},


	addPreviewSlider: function(target, value) {

		jQuery(target).slider({
			value: value, min: 0.5, max: 1.5, step: 0.01,
			range: 'min', orientation: 'horizontal',
			slide: function(event, ui) {

				// Disable auto-fit when resizing manually
				if( jQuery('#zoom-fit').prop('checked') ){
					jQuery('#zoom-fit').next().click();
				}

				LayerSlider.setPreviewZoom(ui.value);

				// Restart previews (if any)
				if( LayerSlider.isSlidePreviewActive ) {
					LayerSlider.stopSlidePreview( );
				}

				if( LayerSlider.isLayerPreviewActive ) {
					LayerSlider.stopLayerPreview( true );
				}
			},

			change: function(event, ui) {
				LS_previewZoom = ui.value;
				LayerSlider.updatePreviewSelection();
			}
		});


		// Resize preview on page load
		if( jQuery('#zoom-fit').prop('checked') ) {
			LayerSlider.autoFitPreview(target);

		// Slide value on page load
		} else if(typeof value != "undefined" && value != 1 ) {
			jQuery(target).slider('value', parseInt(value));
			LayerSlider.setPreviewZoom(value);
		}

		jQuery(document).on('click','#zoom-fit',function(){

			if( jQuery(this).prop('checked') ){
				LayerSlider.autoFitPreview(target, 0.75);
			}
		});

		jQuery(window).resize(function( event ){
			if( event.target === window ) {
				LayerSlider.autoFitPreview(target);
			}
		});


		jQuery('#collapse-menu').click(function() {
			LayerSlider.autoFitPreview(target);
		});
	},


	autoFitPreview: function(target, duration){

		if( jQuery('#zoom-fit').prop('checked') ){

			var sliderSize 	= LayerSlider.getSliderSize(),
				width 		= sliderSize.width,
				height 		= sliderSize.height,
				// 905(px) is the minimum width to keep the slider settings table organized
				smallestRatio = 916 / width > 0.5 ? 916 / width : 0.5,
				padding = (document.location.href.indexOf('ls-revisions') !== -1) ? 0 : 32,
				ratio = ( jQuery('.wrap').eq(0).outerWidth() - padding ) / width;


			if( ratio < smallestRatio ){
				ratio = smallestRatio;
			} else if( ratio > 1 ){
				ratio = 1;
			}

			jQuery(target).slider('value', ratio );
			LayerSlider.setPreviewZoom( ratio );

			// jQuery('.ls-editor-slider-val').text(ratio+'%');

			// if( duration ){
			// 	TweenLite.to(
			// 		jQuery('#ls-preview-layers')[0],
			// 		duration,
			// 		{
			// 			parseTransform: true,
			// 			scale: ratio/100,
			// 			ease: 'Quint.easeInOut',
			// 			onUpdate: function() {
			// 				jQuery('.ls-preview-td').trigger('zoom');
			// 				LayerSlider.updatePreviewSelection();
			// 			}
			// 		}
			// 	);

			// 	TweenLite.to(
			// 		[LS_previewHolder, LS_previewWrapper],
			// 		duration,
			// 		{
			// 			width: width * ratio / 100,
			// 			height: height * ratio / 100,
			// 			ease: 'Quint.easeInOut'
			// 		}
			// 	);
			// }else{
				// LayerSlider.setPreviewZoom( ratio );
			// }
		}
	},


	addLayer: function(layerDataSet, atIndexSet, addProperties) {

		var c, len, selectIndexSet = [], updateInfo = [], emptyData, emptyIndex;

		// Set removeProperties to an empty object by default
		addProperties = addProperties || { selectLayer: true };

		// Get default layer data if not provided
		emptyData 	 = !layerDataSet;
		layerDataSet = layerDataSet || jQuery.extend(true, {}, LS_DataSource.getDefaultLayerData() );
		layerDataSet = jQuery.makeArray( layerDataSet );

		c = layerDataSet.length;

		// Add layer to the top if
		// not specified otherwise.
		emptyIndex = ! atIndexSet;
		atIndexSet = ! emptyIndex ? atIndexSet : [].fill(0, c);
		atIndexSet = jQuery.makeArray( atIndexSet );

		// Iterate backwards to keep indexes consistent throughout
		// the sequence. Don't use .revert() on data sets reference,
		// as it will change the original set as well.
		while(c--) {

			// Add new layer data to data source
			LS_activeSlideData.sublayers.splice(atIndexSet[c], 0, layerDataSet[c]);

			// Offsetting indexes to follow data storage
			// changes in case of multiple additions.
			selectIndexSet.push( atIndexSet[c] + c );

			// UndoManager
			updateInfo.push({
				itemIndex: atIndexSet[c],
				selectIndex: selectIndexSet[c],
				undo: { data: {} },
				redo: { data: layerDataSet[c] }
			});
		}

		// Maintain undoManager
		if( ! addProperties.histroyEvent) {
			LS_UndoManager.add(
				'slide.layers',
				updateInfo.length > 1 ? LS_l10n.SBUndoNewLayers : LS_l10n.SBUndoNewLayer,
				updateInfo
			);
		}

		// Update layers list and preview
		LS_DataSource.buildLayersList();
		LayerSlider.generatePreview();

		// Select new layers
		if( addProperties.selectLayer ) {

			if( addProperties.hasOwnProperty('selectPage') ) {
				LS_activeLayerPageIndex = addProperties.selectPage;
			}

			LayerSlider.selectLayer( selectIndexSet );

			if( emptyData && updateInfo.length === 1 ) {
				jQuery('.ls-sublayers  li.active .ls-sublayer-title').focus().select();
			}
		}
	},


	addFormattedLayer: function( el, layerProperties ) {

		// Hide add layer modal
		jQuery('body').off('click.ls-layer-types');
		jQuery('.ls-empty-layer-notification').removeClass('ls-hidden');
		TweenLite.to( jQuery('.ls-layer-types'), 0.3, {
			y: -330,
			onComplete: function() {
				jQuery('.ls-layer-types-wrapper').hide();
			}
		});

		var layerType = jQuery(el).data('type'),
			layerData;

		if( layerType === 'import' ) {
			LS_ImportLayer.open();
			return;
		}


		// Get default layer data
		layerData = jQuery.extend(true, {}, LS_DataSource.getDefaultLayerData() );

		// Set layer type
		layerData.media = layerType;


		// Set font size to 18 pixels for text based layers
		if( ['text', 'html', 'post'].indexOf( layerType ) !== -1 ) {
			jQuery.extend( layerData.styles, {
				'font-size': 18
			});
		}

		switch( layerType ) {

			case 'text':
				layerData.html = LS_l10n.SBPreviewTextPlaceholder;
				break;

			case 'html':
				layerData.html = LS_l10n.SBPreviewHTMLPlaceholder;
				break;

			case 'icon':
				jQuery.extend( layerData.styles, {
					'font-size': 64
				});
				break;

			case 'button':
				layerData.html = LS_l10n.SBPreviewButtonPlaceholder;
				jQuery.extend( layerData.styles, {
					'padding-top': 15,
					'padding-right': 60,
					'padding-bottom': 15,
					'padding-left': 60,
					'font-family': 'Arial, sans-serif',
					'font-size': 14,
					'font-weight': 700,
					'background': '#1b9af7',
					'color': '#fff',
					'border-radius': 50
				});
				break;

			case 'post':
				layerData.html = LS_l10n.SBPreviewPostPlaceholder;
				break;

		}

		// Merge provided layer properties (if any)
		if( layerProperties ) {
			jQuery.extend(true, layerData, layerProperties);
		}

		// Add formatted layer
		LayerSlider.addLayer( layerData, null, {
			selectLayer: true,
			selectPage: 0
		});

		// Choose icon after adding
		if( layerType === 'icon' ) {
			LS_InsertIcons.showIcons();

		// Choose icon after adding
		} else if( layerType === 'media' ) {
			LS_InsertMedia.open();

		// Bring up Media Library when adding
		// image layer
		} else if( layerType === 'img') {
			jQuery('.ls-layer-image').click();
		}
	},


	selectLayer: function(layerIndexSet, selectProperties) {

		// Bail out early if the current slide has no layers
		if( ! LS_activeSlideData.sublayers.length ) {
			jQuery('.ls-timeline-switch, .ls-sublayer-nav').hide();
			jQuery('.ls-sublayer-pages').empty();
			jQuery('.ls-empty-layer-notification').show();
			return false;

		} else {
			jQuery('.ls-timeline-switch, .ls-sublayer-nav').show();
			jQuery('.ls-empty-layer-notification').hide();
		}

		// Bail out early if there's no active layer selection
		if( ! layerIndexSet || ! layerIndexSet.length ) { return false; }

		// Bail out if the new selection exceeds array range
		if( LS_activeSlideData.sublayers.length-1 < Math.max.apply(Math, layerIndexSet) ) {
			return;
		}

		// Bail out early if the current selection is the same
		// if( layerIndexSet.length == LS_activeLayerIndexSet.length ) {
		// 	if( layerIndexSet.every(function(v,i) { return v === LS_activeLayerIndexSet[i];}) ) {
		// 		return false;
		// 	}
		// }

		// Set removeProperties to an empty object by default
		selectProperties = selectProperties || {};

		// Bail out early if it's already a selected layer
		// if( !selectProperties.forceSelect &&
		// 	LS_activeLayerIndexSet.indexOf(layerIndex) !== -1 ) {
		// 	return false;
		// }

		var $layersList 	= jQuery('.ls-sublayers li'),
			$layerOptions 	= jQuery('.ls-sublayer-pages-wrapper');

		// Stop layer preview session (if any)
		LayerSlider.stopLayerPreview();

		// Update stored data & preview based on
		// the passed selection index set.
		LS_activeLayerIndexSet = [];
		LS_activeLayerDataSet = [];
		$layersList.removeClass('active');
		jQuery('#ls-preview-layers > *').removeClass('ui-selected');
		jQuery.each(layerIndexSet, function(idx, layerIndex) {
			LS_activeLayerIndexSet.push(layerIndex);
			LS_activeLayerDataSet.push(
				LS_activeSlideData.sublayers[layerIndex]
			);
			LS_previewItems[layerIndex].addClass('ui-selected');
			$layersList.eq(layerIndex).addClass('active');
		});

		jQuery.each(LS_activeLayerDataSet, function(index, layerData) {
			if( ! layerData.meta) {
				layerData.meta = {};
			}
		});

		// Show/Hide layer options depending on
		// the number of selected layers
		if(LS_activeLayerIndexSet.length > 1) {
			LayerSlider.startMultipleSelection();
		} else {
			LayerSlider.stopMultipleSelection();
		}

		// Build new layer ...
		if(LS_activeLayerIndexSet.length === 1) {
			LS_DataSource.buildLayer();
			LS_lastSelectedLayerIndex = LS_activeLayerIndexSet[0];
		}

		// Store selection
		LS_Utils.removeTextSelection();
		LayerSlider.updatePreviewSelection();
		LS_activeSlideData.meta.activeLayers = LS_activeLayerIndexSet;
		jQuery('.ls-timeline-switch, .ls-sublayer-nav').show();
		jQuery('.ls-empty-layer-notification').hide();

		// Create layer transition preview animations
		layerTransitionPreview.create();
	},


	startMultipleSelection: function() {

		var $layerOptions 	= jQuery('.ls-sublayer-pages-wrapper'),
			$layerNav 		= jQuery('.ls-sublayer-nav'),
			$contentTab 	= $layerNav.children().eq(0);

		// Hide 'Content' and select the 'Transitions'
		// layer tab if needed.
		$contentTab.hide();
		if( $contentTab.hasClass('active') ) {
			$contentTab.next().click();
		}


		jQuery('#ls-layers-settings-popout').addClass('ls-multiple-selection');

		// Reset input field
		jQuery('input,textarea', $layerOptions).filter('.sublayerprop,.auto').val('');
		jQuery('.ls-sublayer-pages .minicolors-swatch-color').css('background', 'transparent');


		// Prepend empty option to select fields
		jQuery('select:not(.ls-multi-selected)', $layerOptions)
			.filter('.sublayerprop,.auto')
			.add( jQuery('.ls-slide-link select', $layerOptions) )
			.addClass('ls-multi-selected')
			.prepend('<option></option>');

		// Select the empty option in select fields
		jQuery('select', $layerOptions)
			.filter('.sublayerprop,.auto')
			.add( jQuery('.ls-slide-link select', $layerOptions) )
			.children().prop('selected', false)
			.eq(0).prop('selected', true);

		// Reset checkboxes
		jQuery('.ls-checkbox', $layerOptions)
			.removeClass('on off')
			.addClass('indeterminate');

		// Reset transition selection
		jQuery('#ls-transition-selector-table .active').removeClass('active');
		jQuery('#ls-layer-transitions .ls-h-button .ls-checkbox').removeClass('on');

		// Reset links
		jQuery('.ls-slide-link input', $layerOptions)
			.val('')
			.prop('disabled', false)
			.closest('.ls-slide-link')
			.removeClass('has-link');

		// Reset custom attributes field
		jQuery('.ls-sublayer-custom-attributes tr:not(:last-child)').remove();
	},


	stopMultipleSelection: function() {

		var $layerOptions 	= jQuery('.ls-sublayer-pages-wrapper'),
			$layerNav 		= jQuery('.ls-sublayer-nav');

		// Show the Content layer tab
		$layerNav.children().eq(0).show();

		jQuery('#ls-layers-settings-popout').removeClass('ls-multiple-selection');
	},


	selectLayerPage: function(pageIndex) {

		// Select new tab
		jQuery('.ls-sublayer-nav a').removeClass('active')
			.eq(pageIndex).addClass('active');

		// Show the corresponding page
		jQuery('#ls-layers .ls-sublayer-page').removeClass('active')
			.eq( pageIndex ).addClass('active');

		// Store lastly selected layer page
		LS_activeLayerPageIndex = pageIndex;

		// SET: styles
		kmUI.smartResize.set();
	},


	selectTransitionPage: function( td ) {

		var $td = jQuery(td),
			index = ($td.index() - 1)  / 2,
			$target = jQuery('#ls-layer-transitions').children().eq(index);

		$target.addClass('active').siblings().removeClass('active');
		$td.addClass('selected').siblings().removeClass('selected');

		jQuery( '#ls-transition-selector' ).val( index );

		LS_activeLayerTransitionTab = index;

		$target.removeClass('disabled');
		if( ! $target.find('.ls-h-button input').prop('checked') ) {
			$target.addClass('disabled');
		}
	},

	enableTransitionPage: function( input ) {

		LayerSlider.reorderTransitionProperties(
			jQuery( input ).closest('section').index()
		);

		LayerSlider.checkForOpeningTransition();

 	},


 	checkForOpeningTransition: function() {

 		// Don't show the warning in multi-select
 		if( LS_activeLayerIndexSet.length === 1 ) {

	 		$table 			= jQuery('#ls-transition-selector-table');
	 		$transitions 	= jQuery('.ls-opening-transition.active', $table);
	 		$warning 		= jQuery('#ls-transition-warning');

			$warning[ $transitions.length ? 'removeClass' : 'addClass' ]('visible');
		}
 	},


 	reorderTransitionProperties: function( sectionIndex ) {

 		// if( LS_activeLayerIndexSet.length > 1) {
 		// 	return;
 		// }

 		var media 		= LS_activeLayerDataSet[0].media || '',
 			index,
 			$sections 	= jQuery('#ls-layer-transitions').children(),
 			$section,
 			$input,
 			$td;

 		if( typeof sectionIndex !== 'undefined' ) {
 			$sections = $sections.eq( sectionIndex );
 		}


 		$sections.each(function() {

 			$section 	= jQuery(this);
 			index 		= $section.index();
 			$input 		= $section.find('input.toggle').eq(0);
 			$td 		= jQuery('#ls-transition-selector-table td:not(.ls-padding)').eq( index );

 			// Disabled
 			if( ! $input.prop('checked') ) {
 				$td.removeClass('active');
 				$section.addClass('disabled');
 				$section.find(':input').each(function() {
					var $this 	= jQuery(this),
						name 	= $this.attr('name'),
						value 	= $this.is(':checkbox') ? $this.prop('checked') : $this.val();

					if( name && ! $this.is('.toggle') ) {
						$this.data('value', value );
						delete LS_activeLayerDataSet[0].transition[ name ];
					}
				});

 			// Active
 			} else {
 				$td.addClass('active');
 				$section.removeClass('disabled');
 				$section.find(':input').each(function() {
					var $this 	= jQuery(this),
						name 	= $this.attr('name'),
						value 	= $this.data('value');

					if( name && ! $this.is('.toggle') ) {
						LS_activeLayerDataSet[0].transition[ name ] = value;
					}
				});
 			}
 		});

 	},


	removeLayer: function(layerIndexSet, removeProperties) {

		// Set removeProperties to an empty object by default
		removeProperties = removeProperties || { requireConfirmation: true };

		// Require confirmation from user
		// if it's not a history event.
		if( removeProperties.requireConfirmation ) {
			if( !confirm( LS_l10n.SBRemoveLayer ) ) {
				return false;
			}
		}

		// Get active layers if no index was provided
		if( ! layerIndexSet  && layerIndexSet !== 0 ) {
			layerIndexSet = LS_activeLayerIndexSet;

		// Convert a single index to an index set
		} else if( typeof layerIndexSet === 'number') {
			layerIndexSet = [layerIndexSet];
		}

		// Get layer(s)
		var c = layerIndexSet.length, $layers = jQuery('.ls-sublayers li'),
			updateInfo = [], $layer, $newLayer, layerIndex, layerData;

		// Iterate backwards to keep indexes consistent throughout the sequence.
		// Don't use .revert() on a LS_activeLayerIndexSet reference, as it will
		// change the original set as well.
		while(c--) {
			layerIndex 	= layerIndexSet[c];
			$layer 		= $layers.eq(layerIndex);
			layerData 	= jQuery.extend(true, {}, LS_activeSlideData.sublayers[layerIndex]);

			// Get the next or prev layer
			if($layer.next().length > 0) { $newLayer = $layer.next(); }
				else if($layer.prev().length > 0) { $newLayer = $layer.prev(); }

			// Setup UndoManager updateInfo object
			updateInfo.push({
				itemIndex: layerIndex,
				undo: { data: layerData },
				redo: { data: {} }
			});

			// Remove layer from data source and UI
			LS_activeSlideData.sublayers.splice(layerIndex, 1);
			$layer.remove();
		}

		// Empty slide, hide UI items
		if( ! LS_activeSlideData.sublayers.length ) {
			jQuery('.ls-timeline-switch, .ls-sublayer-nav').hide();
			jQuery('.ls-multi-select-notice').hide();
			jQuery('.ls-sublayer-pages').empty();
			jQuery('.ls-empty-layer-notification').show();

			LS_activeLayerIndexSet = [];

		// Update UI otherwise
		// Select new layer. The .click() event will
		// maintain the active layer index and data.
		} else if( $newLayer ) {
			LayerSlider.selectLayer( [ $newLayer.index() ] );
			LayerSlider.reindexLayers();
		}


		// Update preview
		LayerSlider.generatePreview();
		LayerSlider.updatePreviewSelection();

		// Maintain undoManager only if
		// it wasn't a history action
		if( !removeProperties.histroyEvent && updateInfo.length) {
			LS_UndoManager.add('slide.layers', LS_l10n.SBUndoRemoveLayer, updateInfo);
		}
	},


	hideLayer: function( el ) {

		var layerIndexSet 	= LS_activeLayerIndexSet,
			layerDataSet 	= LS_activeLayerDataSet,
			updateInfo 		= [],
			layerData,
			$control;

		// Get layer data if provided
		if( el ) {
			layerIndexSet 	= [ jQuery(el).closest('li').index() ];
			layerDataSet 	= [ LS_activeSlideData.sublayers[ layerIndexSet[0] ] ];
		}


		jQuery.each( layerIndexSet, function( index, layerIndex ) {

			layerData 	= layerDataSet[ index ];
			$control 	= jQuery('.ls-sublayers .ls-icon-eye').eq(layerIndex);

			updateInfo.push({
				itemIndex: layerIndex,
				undo: { skip: !!layerData.skip },
				redo: { skip: !layerData.skip }
			});

			// Hide/show layer
			layerData.skip = !layerData.skip;
			if( layerData.skip ) { $control.addClass('disabled'); }
				else { $control.removeClass('disabled'); }

			// Update preview
			LayerSlider.generatePreviewItem( layerIndex );
		});

		// Maintain history
		LS_UndoManager.add('layer.general', LS_l10n.SBUndoHideLayer, updateInfo);
	},


	lockLayer: function(el) {

		var layerIndexSet 	= LS_activeLayerIndexSet,
			layerDataSet 	= LS_activeLayerDataSet,
			updateInfo 		= [],
			layerData,
			$previewItem,
			$control;

		// Get layer data if provided
		if( el ) {
			layerIndexSet 	= [ jQuery(el).closest('li').index() ];
			layerDataSet 	= [ LS_activeSlideData.sublayers[ layerIndexSet[0] ] ];
		}


		jQuery.each( layerIndexSet, function( index, layerIndex ) {

			layerData 		= layerDataSet[ index ];
			$previewItem 	= LayerSlider.previewItemAtIndex( layerIndex );
			$control 		= jQuery('.ls-sublayers .ls-icon-lock').eq(layerIndex);

			updateInfo.push({
				itemIndex: layerIndex,
				undo: { locked: !!layerData.locked },
				redo: { locked: !layerData.locked }
			});

			// Lock layer
			layerData.locked = !layerData.locked;
			if( layerData.locked ) {
				$control.removeClass('disabled');
				$previewItem.addClass('disabled');
				$lasso.hide();

			// Unlock layer
			} else {

				$control.addClass('disabled');
				$previewItem.removeClass('disabled');
			}

			// Update preview
			LayerSlider.generatePreviewItem( layerIndex );

		});

		// Maintain history
		LS_UndoManager.add('layer.general', LS_l10n.SBUndoLockLayer, updateInfo);
	},


	setLayerMedia: function(mediaType, $mediaEl, layerData) {

		switch(mediaType) {
			case 'img':

			var src = layerData.imageThumb || pluginPath+'admin/img/blank.gif',
				classes = layerData.imageThumb ? '' : ' dashicons dashicons-format-image';

				$mediaEl.attr('class', 'ls-sublayer-thumb'+classes).html('<img src="'+(layerData.imageThumb || pluginPath+'admin/img/blank.gif')+'">');
				break;

			case 'html':
				$mediaEl.addClass('dashicons dashicons-editor-code');
				break;

			case 'button':
				$mediaEl.addClass('dashicons dashicons-marker');
				break;

			case 'icon':
				$mediaEl.addClass('dashicons dashicons-flag');
				break;

			case 'media':
				$mediaEl.addClass('dashicons dashicons-video-alt3');
				break;

			case 'post':
				$mediaEl.addClass('dashicons dashicons-admin-post');
				break;

			default:
				$mediaEl.addClass('dashicons dashicons-text');
				break;
		}
	},


	setLayerAttributes: function( event, element ) {

		if( event.type === 'change' && ! jQuery(element).is(':checkbox') ) {
			return;
		}

		var $tr = jQuery(element).closest('tr'),
			$inputs = jQuery('input', $tr );

		if( ! $inputs.eq(0).val() && ! $inputs.eq(1).val() ) {
			$tr.remove();
		}

		jQuery.each(LS_activeLayerDataSet, function(index, layerData) {

			var innerAttrs = layerData.innerAttributes = {},
				outerAttrs = layerData.outerAttributes = {};

			jQuery('.ls-sublayer-custom-attributes tr:not(:last-child)').each(function() {

				var $key = jQuery('td.first input', this),
					$val = jQuery('td.second input', this),
					$chb = jQuery('td.third input', this),
					key  = $key.val(),
					val  = $val.val();

				if( key && /^[a-zA-Z]([a-zA-Z0-9_-]+)$/.test( key ) ) {
					$key.removeClass('error');

					if( $chb.prop('checked') ) {

						outerAttrs[ key ] = val;
					} else {
						innerAttrs[ key ] = val;
					}

				} else {
					$key.addClass('error');
				}
			});
		});
	},

	updateLayerBackgroundNotification: function() {
		var layerBG = LS_activeLayerDataSet[0].layerBackground;
		jQuery('#ls-background-notification')[ layerBG ? 'removeClass' : 'addClass' ]('ls-hidden');
	},

	updateLayerAttributes: function( layerData ) {

		// Make sure to have objects for data
		layerData.innerAttributes = layerData.innerAttributes || {};
		layerData.outerAttributes = layerData.outerAttributes || {};

		var customAttrs = jQuery.extend( {}, layerData.innerAttributes, layerData.outerAttributes),
			$customAttributes = jQuery('.ls-sublayer-custom-attributes');

		// Sort keys
		Object.keys(customAttrs).sort().forEach(function(key) {
			var value = customAttrs[key];
			delete customAttrs[key];
			customAttrs[key] = value;
		});

		jQuery.each(customAttrs, function(key, val) {
			jQuery('tr:last-child input:eq(2)', $customAttributes).prop('checked', key in layerData.outerAttributes );
			jQuery('tr:last-child input:eq(1)', $customAttributes).val( val );
			jQuery('tr:last-child input:eq(0)', $customAttributes).val( key ).trigger('keyup');
		});
	},

	updateLayerBorderPadding: function(el) {

		var $input 	= jQuery(el),
			value 	= parseInt( $input.val() ),
			type 	= ($input.parent().index() === 1) ? 'border' : 'padding',
			edge 	= $input.closest('tr').data('edge');
			sel 	= '.ls-'+type+'-'+edge+'-value';

		jQuery(sel).text( value || '–' );
	},

	// Iterate through all slides and their layers to
	// find the ones appearing on the target slide.
	staticLayersForSlide: function( targetSlideIndex ) {

		var staticLayers = [];

		jQuery.each(window.lsSliderData.layers, function(slideIndex, slideData) {
			jQuery.each(slideData.sublayers, function(layerIndex, layerData) {

				if( layerData.transition.static ) {
					var staticOut = layerData.transition.static;
					if( ( staticOut > targetSlideIndex || staticOut === 'forever' ) && slideIndex < targetSlideIndex ) {

						staticLayers.push({
							slideIndex: slideIndex,
							slideData: 	slideData,
							layerIndex: layerIndex,
							layerData: 	layerData
						});
					}
				}
			});
		});

		return staticLayers;
	},


	reindexStaticLayers: function() {

		jQuery.each(window.lsSliderData.layers, function(slideIndex, slideData) {
			jQuery.each(slideData.sublayers, function(layerIndex, layerData) {

				if( layerData.transition.staticUUID ) {
					var staticOut = LS_DataSource.slideForUUID( layerData.transition.staticUUID );
					if( staticOut ) {
						layerData.transition.static = staticOut + 1;
					}
				}
			});
		});
	},

	setupStaticLayersChooser: function( select ) {

		var $select = jQuery(select);

			// Remove previously added options
			$select.children('[value="forever"]').nextAll().remove();

			// Gather slide data
			var sliderData 	= window.lsSliderData,
				slideCount 	= sliderData.layers ? sliderData.layers.length : 0,
				markup 		= '<option value="-2" disabled>–</option>',
				slideName;

			// Generate markup
			for( var s = 0; s < slideCount; s++) {
				slideName 	= sliderData.layers[s].properties.title;
				slideName 	= slideName ? ' ('+slideName+')' : '';
				markup += '<option value="'+(s+1)+'">'+LS_l10n.SBStaticUntil.replace('%d', (s+1))+' '+slideName+'</option>';
			}

			// Append select options
			$select.append(markup);

			var staticVal = parseInt( LS_activeLayerDataSet[0].transition.static );
			if( staticVal ) {
				$select.children('[value="'+staticVal+'"]').prop('selected', true)
					.siblings().prop('selected', false);
			}

	},


	revealStaticLayer: function( el ) {

		var $target = jQuery(el).closest('li'),
			index 	= $target.index(),
			data 	= LS_activeStaticLayersDataSet[ index ];

		LayerSlider.selectSlide( data.slideIndex );
		LayerSlider.selectLayer( [data.layerIndex] );
	},


	addColorPicker: function(el) {
		jQuery(el).minicolors({
			opacity: true,
			changeDelay: 100,
			position: 'bottom right',
			change: function(hex, opacity) {
				//LayerSlider.willGeneratePreview();
			}
		}).blur(function( event ) {
			event.stopImmediatePropagation();

			jQuery(this)
				.removeClass('ls-colorpicker')
				.trigger('change')
				.addClass('ls-colorpicker');

		});
	},


	duplicateLayer: function( ) {
		this.pasteLayer( this.copyLayer( false).layers );
	},


	copyLayer: function(useStorage, layerDataSet, layerIndexSet, copyProperties) {

		// Defaults
		useStorage 		= useStorage 	|| true;
		layerDataSet 	= layerDataSet 	|| LS_activeLayerDataSet;
		layerIndexSet 	= layerIndexSet || LS_activeLayerIndexSet;
		copyProperties 	= copyProperties || { shiftLayers: true };

		// Iterate over the data set, clone objects and
		// make some visual adjustments on items
		var clipboardData = [];
		jQuery.each(layerDataSet, function(key, item) {

			// Copy layer data object
			var copy = jQuery.extend(true, {}, item);
			copy.subtitle += ' copy';

			// Add copy to the new set
			clipboardData.push(copy);
		});

		// Build clipboard data
		clipboardData = {
			layers: clipboardData,
			sliderID: copyProperties.sliderID || LS_sliderID,
			slideIndex: copyProperties.slideIndex || LS_activeSlideIndex,
			layerIndexSet: layerIndexSet
		};

		// Save to storage and return copies
		useStorage && localStorage.setObject('ls-layer-clipboard', clipboardData);
		return clipboardData;
	},


	pasteLayer: function(layerDataSet, layerIndexSet, pasteProperties) {

		// Check for provided data, fetch from clipboard if not
		var isDataProvided 	= layerDataSet ? true : false,
			clipboardData 	= localStorage.getObject('ls-layer-clipboard'),
			addIndexSet;

		if( ! clipboardData ) {
			alert(LS_l10n.SBPasteLayerError);
			return;
		}

		layerDataSet 		= layerDataSet 	|| clipboardData.layers;
		layerIndexSet 		= layerIndexSet || clipboardData.layerIndexSet;

		// Warn users when there's nothing on the clipboard
		// and halt execution.
		if( ! layerDataSet ) {
			alert(LS_l10n.SBPasteLayerError);
			return;
		}

		// Set pasteProperties to an empty object by default
		pasteProperties = pasteProperties || {};

		// If the layer is from the same slide, then
		// find the uppermost selected layer index
		// and insert everything into that position.
		// Otherwise insert at the beginning of the layers list.
		// -
		// Trying to insert layers before their parents
		// individually is complex, and it will fragment
		// dupe selection.
		if(clipboardData.sliderID !== LS_sliderID || clipboardData.slideIndex !== LS_activeSlideIndex) {
			addIndexSet = [].fill( 0, layerIndexSet.length);
		} else {
			addIndexSet = [].fill( Math.min.apply(Math, layerIndexSet), layerIndexSet.length);
		}


		// Generate UUIDs for the new layers
		jQuery.each( layerDataSet, function( index, layerData ) {
			layerData.uuid = LS_DataSource.generateUUID();
		});

		// Insert new layers
		LayerSlider.addLayer(layerDataSet, addIndexSet, { selectLayer: true } );

		// Copy pasted layer to make a new reference
		// and update settings like position and name
		if( ! isDataProvided) {
			this.copyLayer(true, layerDataSet, layerIndexSet, {
				sliderID: clipboardData.sliderID,
				slideIndex: clipboardData.slideIndex
			});
		}
	},


	selectMediaType: function(el, layerIndex) {

		// Gather layer data
		layerIndex = layerIndex ? layerIndex : LS_activeLayerIndexSet;
		layerIndex = (typeof layerIndex === 'object') ? layerIndex[0] : layerIndex;
		var layerData  	= LS_activeSlideData.sublayers[layerIndex],
			layer 		= jQuery(el).closest('.ls-sublayer-page'),
			$layerItem 	= jQuery('.ls-sublayers li').eq(layerIndex),
			section 	= jQuery(el).data('section'),
			placeholder = jQuery(el).data('placeholder'),
			sections 	= jQuery('.ls-layer-sections', layer).children();

		// Set active class
		jQuery(el).addClass('active').siblings().removeClass('active');

		// Store selection
		if( section ) {
			layerData.media = section;
		}

		// Show the corresponding sections
		sections.hide().removeClass('ls-hidden');
		jQuery('.ls-sublayer-element', layer).hide().removeClass('ls-hidden');
		jQuery('.ls-html-code .ls-options, .ls-html-code .ls-open-media-modal-button, .ls-html-code .ls-button-options, .ls-html-code .ls-icon-options', layer).addClass('ls-hidden');
		jQuery('.ls-html-code .ls-insert-icon', layer).removeClass('ls-hidden');
		jQuery('.ls-html-code .ls-replace-icon-button', layer).addClass('ls-hidden');
		jQuery('.ls-html-code .ls-html-textarea', layer).removeClass('ls-hidden');

		switch(section) {
			case 'img':
				sections.eq(0).show();
				var src 	= layerData.imageThumb || pluginPath+'admin/img/blank.gif',
					classes = layerData.imageThumb ? '' : ' dashicons dashicons-format-image';

				jQuery('.ls-sublayer-thumb', $layerItem).attr('class', 'ls-sublayer-thumb'+classes).html('<img src="'+(layerData.imageThumb || pluginPath+'admin/img/blank.gif')+'">');
				break;

			case 'text':
				sections.eq(1).show();
				layer.find('.ls-sublayer-element').show();
				jQuery('.ls-html-code textarea').attr('placeholder', placeholder );
				jQuery('.ls-sublayer-thumb', $layerItem).attr('class', 'ls-sublayer-thumb dashicons dashicons-text').html('');
				break;

			case 'icon':
				sections.eq(1).show();
				jQuery('.ls-html-code .ls-options, .ls-html-code .ls-html-textarea', layer).addClass('ls-hidden');
				jQuery('.ls-html-code .ls-icon-options', layer).removeClass('ls-hidden');
				jQuery('.ls-sublayer-thumb', $layerItem).attr('class', 'ls-sublayer-thumb dashicons dashicons-flag').html('');
				break;


			case 'button':
				sections.eq(1).show();
				jQuery('.ls-html-code .ls-options, .ls-html-code .ls-open-media-modal-button', layer).addClass('ls-hidden');
				jQuery('.ls-html-code .ls-button-options', layer).removeClass('ls-hidden');
				jQuery('.ls-html-code .ls-replace-icon-button', layer).removeClass('ls-hidden');
				jQuery('.ls-html-code textarea').attr('placeholder', placeholder );
				jQuery('.ls-sublayer-thumb', $layerItem).attr('class', 'ls-sublayer-thumb dashicons dashicons-marker').html('');
				break;

			case 'html':
				sections.eq(1).show();
				jQuery('.ls-html-code .ls-options, .ls-html-code .ls-open-media-modal-button', layer).addClass('ls-hidden');
				jQuery('.ls-html-code textarea').attr('placeholder', placeholder );
				jQuery('.ls-sublayer-thumb', $layerItem).attr('class', 'ls-sublayer-thumb dashicons dashicons-editor-code').html('');
				break;

			case 'media':
				sections.eq(1).show();
				jQuery('.ls-html-code .ls-options, .ls-html-code .ls-open-media-modal-button', layer).removeClass('ls-hidden');
				jQuery('.ls-html-code .ls-insert-icon', layer).addClass('ls-hidden');
				jQuery('.ls-html-code textarea').attr('placeholder', placeholder );
				jQuery('.ls-sublayer-thumb', $layerItem).attr('class', 'ls-sublayer-thumb dashicons dashicons-video-alt3').html('');
				break;

			case 'post':
				sections.eq(1).show();
				sections.eq(2).show();
				jQuery('.ls-html-code textarea').attr('placeholder', placeholder );
				jQuery('.ls-sublayer-thumb', $layerItem).attr('class', 'ls-sublayer-thumb dashicons dashicons-admin-post').html('');
				break;
		}

		if( section === 'img' || section === 'media' ) {
			jQuery('#ls-layer-transitions .ls-text-transition .ls-checkbox.toggle.on').click();
		}

		jQuery('.ls-sublayer-pages-wrapper').attr('class', 'ls-sublayer-pages-wrapper ls-layer-type-' + layerData.media);

		// Remove previous placeholder <li> (if any)
		jQuery('.ls-layer-kind ul .placeholder').remove();

		// Prepend placeholder <li>
		jQuery(el)
			.clone()
			.removeClass('active')
			.addClass('placeholder')
			.prependTo('.ls-layer-kind ul');

	},


	selectElementType: function(el, layerIndex) {

		// Layer and properties
		layerIndex = layerIndex ? layerIndex : LS_activeLayerIndexSet;
		layerIndex = (typeof layerIndex === 'object') ? layerIndex[0] : layerIndex;

		var layerData  = LS_activeSlideData.sublayers[layerIndex],
			layer = jQuery(el).closest('.ls-sublayer-page'),
			element = jQuery(el).data('element');

		// Set active class
		jQuery(el).siblings().removeClass('active');
		jQuery(el).addClass('active');

		// Store selection
		if( element ) {
			layerData.type = element;
		}

	},


	copyLayerSettings: function(el) {

		var $el 		= jQuery(el),
			$wrapper 	= $el.closest('[data-storage]'),
			storage 	= $wrapper.attr('data-storage'),
			data 		= { styles: {}, transition: {} };

		// Iterate over options, store values
		$wrapper.find(':input').each(function() {
			if(this.name) {
				var $item 	= jQuery(this),
					area 	= $item.hasClass('sublayerprop') ? 'transition' : 'styles';

				data[area][this.name] = $item.is(':checkbox') ? $item.prop('checked') : $item.val();
			}
		});

		// Add data to clipboard
		var LS_clipboard = localStorage.getObject('ls-options-clipboard') || {};
		LS_clipboard[ storage ] = {
			timestamp: Math.floor(Date.now() / 1000),
			data: data
		};
		localStorage.setObject('ls-options-clipboard', LS_clipboard);

		// Send feedback to users
		$el.css('color', '#fcd116');
		setTimeout(function() {
			$el.css('color', '#00a0d2');
		}, 1000);
	},


	pasteLayerSettings: function(el) {

		var $el 		= jQuery(el),
			$wrapper 	= $el.closest('[data-storage]'),
			storage 	= $wrapper.attr('data-storage'),
			updateInfo 	= [];



		// Don't allow pasting options when the corresponding
		// transition sections is disabled
		if( $wrapper.closest('#ls-layer-transitions').length ) {
			if( ! $wrapper.find('.ls-h-button input').prop('checked') ) {
				$wrapper.find('.overlay').click();
				return;
			}
		}

		// Get clipboard data
		var LS_clipboard = localStorage.getObject('ls-options-clipboard') || {},
			clipboard = LS_clipboard[storage],
			timestamp = Math.floor(Date.now() / 1000);

		// Validate clipboard data
		if( ! clipboard || jQuery.isEmptyObject(clipboard.data) || clipboard.timestamp < timestamp - 60 * 60 * 3 ) {
			alert(LS_l10n.SBPasteError);
			return false;
		}

		// Iterate over all selected layers
		jQuery.each(LS_activeLayerIndexSet, function(index, layerIndex) {

			var layerData 	= LS_activeLayerDataSet[ index ],
				undoObj 	= {},
				redoObj 	= {};

			// Iterate over options, set new values
			$wrapper.find(':input').each(function() {
				if(this.name && this.name != 'top' && this.name != 'left') { // !!! don't paste left & top style

					var $this 	= jQuery(this),
						area 	= $this.hasClass('sublayerprop') ? 'transition' : 'styles',
						data 	= layerData[area];
						curVal 	= layerData[area][this.name],
						newVal 	= clipboard.data[area][this.name];

					if( this.name === 'style' ) { curVal = layerData[this.name]; }

					if( curVal != newVal ) {

						if( ! undoObj[ area ] ) { undoObj[ area ] = {}; }
						if( ! redoObj[ area ] ) { redoObj[ area ] = {}; }

						undoObj[ area ][ this.name ] = curVal;
						redoObj[ area ][ this.name ] = newVal;
					}

					// Handle custom CSS field separately
					if( this.name === 'style' ) { layerData.style = newVal; }
						else { data[this.name] = newVal; }
				}
			});

			updateInfo.push({
				itemIndex: layerIndex,
				undo: undoObj,
				redo: redoObj
			});

			LS_DataSource.buildLayer();

			// Update affected layer in preview
			// in case of style changes
			if( storage === 'ls-styles' ) {
				LayerSlider.generatePreviewItem( layerIndex );
			}
		});

		// Add UndoManager action
		LS_UndoManager.add('layer.general', LS_l10n.SBUndoPasteSettings, updateInfo);


		$el.css('color', '#90ca77');
		setTimeout(function() { $el.css('color', '#00a0d2'); }, 1000);

	},


	updateSlideInterfaceItems: function() {

		var slideData 	= LS_activeSlideData.properties,
			imgSrc 		= slideData.backgroundThumb || slideData.background;

		LS_GUI.updateImagePicker( 'background', imgSrc );
		LS_GUI.updateLinkPicker('layer_link');
	},

	updateLayerInterfaceItems: function(layerIndex) {

		var $layer = jQuery('.ls-sublayer-pages'),
			$layerItem = jQuery('.ls-sublayers li').eq(layerIndex),
			layerData = LS_activeSlideData.sublayers[layerIndex];

		if( ! layerData ) { return; }

		// Image previews
		LS_GUI.updateImagePicker( 'image', layerData.imageThumb || layerData.image );
		LS_GUI.updateImagePicker( 'poster', layerData.posterThumb || layerData.poster );
		LS_GUI.updateImagePicker( 'layerBackground', layerData.layerBackgroundThumb || layerData.layerBackground );

		// Linking field
		LS_GUI.updateLinkPicker( 'url' );

		// Select layer and media type
		if(typeof layerData.media == 'undefined') {
			switch(layerData.type) {
				case 'img': layerData.media = 'img'; break;
				case 'div': layerData.media = 'html'; break;
				default: layerData.media = 'text';
			}
		}

		LayerSlider.selectMediaType( $layer.find('.ls-layer-kind li[data-section="'+layerData.media+'"]').eq(0), layerIndex );
		LayerSlider.selectElementType( $layer.find('.ls-sublayer-element > li[data-element="'+layerData.type+'"]'), layerIndex );

		// Skip
		if(layerData.skip) { jQuery('.ls-icon-eye', $layerItem).addClass('disabled'); }
			else { jQuery('.ls-icon-eye', $layerItem).removeClass('disabled'); }

		if(layerData.locked) { jQuery('.ls-icon-lock', $layerItem).removeClass('disabled'); }
			else { jQuery('.ls-icon-lock', $layerItem).addClass('disabled'); }
	},

	changeLayerScreenType: function( $button, updateLayer  ) {


		jQuery('.ls-set-screen-types button').each(function() {

			var layerData 	= LS_activeLayerDataSet[0],
				$item 		= jQuery(this),
				type 		= $item.data('type');

			if( $button && $button.is( $item ) ) {
				layerData['hide_on_'+type] = ! layerData['hide_on_'+type];
			}

			$item[ layerData['hide_on_'+type] ? 'removeClass' : 'addClass' ]('playing');
		});


		if( updateLayer ) {
			LayerSlider.generatePreviewItem( LS_activeLayerIndexSet[0] );
			setTimeout(function() {
				LS_DataSource.buildLayersListItem( LS_activeLayerIndexSet[0] );
			}, 200);
		}
	},

	changeVideoType: function( event ) {

		var $input 			= jQuery('.ls-sublayer-basic input.bgvideo'),
			$options 		= jQuery('.ls-sublayer-basic .ls-media-options');
			$notification 	= jQuery('.ls-sublayer-basic .ls-bgvideo-options');

		if( $input.prop('checked') ) {
			$options.find('td').hide().filter('.volume,.overlay, .muted,.loop').show();
			$notification.show();

		} else {
			$options.find('td').show().filter('.overlay').hide();
			$notification.hide();
		}


		if( event && event.type === 'change' ) {
			LS_activeLayerDataSet[0].locked = $input.prop('checked') ? true : false;
			LS_DataSource.buildLayersListItem( LS_activeLayerIndexSet[0] );
		}
	},



	validateCustomCSS: function( $textarea ) {

		var keys = ['mix-blend-mode', 'filter'];

		for(var c = 0; c < keys.length; c++) {

			if( $textarea.val().indexOf(keys[c]) !== -1 ) {

				$textarea.val( $textarea.val().replace( new RegExp(keys[c], 'gi'), '') );

				TweenMax.to( jQuery('.ls-sublayer-style :input[name="'+keys[c]+'"]')[0], 0.15, {
					yoyo: true,
					repeat: 3,
					ease: Quad.easeInOut,
					scale: 1.2,
					backgroundColor: 'rgba(255, 0, 0, 0.2)'
				});
			}
		}
	},


	willGeneratePreview: function() {
		clearTimeout(LayerSlider.timeout);
		LayerSlider.timeout = setTimeout(function() {
				LayerSlider.generatePreview();
		}, 1000);
	},


	generatePreview: function() {

		// –––––––––––––––––––––––––––––––––––––––––––––
		// READ-ONLY BLOCK
		//
		// Group DOM read/access operations together,
		// so the browser can cache and apply them in a
		// in a single pass, triggering only one reflow.
		// ———————————————————————————––––––––––––––––––

		// Slider data sets
	var sliderProps = window.lsSliderData.properties,
		sliderSize 	= LayerSlider.getSliderSize(),
		slideIndex 	= LS_activeSlideIndex,
		slideData 	= LS_activeSlideData,
		slideProps 	= slideData.properties,
		layers 		= slideData.sublayers,
		$settings 	= jQuery('.ls-settings'),


		// Preview data
		width 		= sliderSize.width,
		height 		= sliderSize.height,
		bgColor 	= sliderProps.backgroundcolor,
		bgImage 	= sliderProps.backgroundimage,
		yourLogo 	= sliderProps.yourlogo,
		yourLogoStyle = sliderProps.yourlogostyle,
		posts 		= window.lsPostsJSON || [],
		postOffset 	= slideProps.post_offset,
		slideBG 	= slideProps.background,
		slideBGSize = slideProps.bgsize,
		slideBGPos 	= slideProps.bgposition,
		post;



		// --- Adjust default values ---
		height 		= (height.indexOf('%') !== -1) ? 400 : parseInt(height);
		postOffset 	= (postOffset == -1) ? slideIndex : postOffset;
		post 		= posts[ postOffset ] || {};




		// –––––––––––––––––––––––––––––––––––––––––––––
		// WRITE-ONLY BLOCK
		//
		// Use only DOM write operations after this comment,
		// so the browser can cache and apply them in a
		// in a single pass, triggering only one reflow.
		// ———————————————————————————––––––––––––––––––


		// --- Set preview canvas size ---
		LS_previewArea.css({
			width : width,
			height : height
		}).empty();

		jQuery('.ls-preview-size').css({
			width : width * LS_previewZoom,
			height : height * LS_previewZoom
		});

		// Make sure to follow preview area size changes
		jQuery('.ls-ruler').trigger('resize');
		LayerSlider.autoFitPreview();


		// --- Set global background ---
		LS_previewHolder.css({
			backgroundColor : bgColor || 'transparent',
			backgroundImage : bgImage ? 'url('+bgImage+')' : 'none',
			backgroundRepeat: sliderProps.globalBGRepeat,
			backgroundAttachment: sliderProps.globalBGAttachment,
			backgroundPosition: sliderProps.globalBGPosition,
			backgroundSize: sliderProps.globalBGSize
		});

		// Empty preview items list, so we don't include beyond
		// array bounds objects from previous slide in case of
		// slide change.
		LS_previewItems = [];

		// Handle post content
		if(slideBG == '[image-url]') {
			slideBG = post['image-url'];
			LS_GUI.updateImagePicker( 'background', post['image-url'], { fromPost: true });
		}

		// -- Set slide background && empty previous content ---
		if( ! slideBGSize || slideBGSize === 'inherit') {
			slideBGSize = sliderProps.slideBGSize;
		}

		if( ! slideBGPos || slideBGPos === 'inherit') {
			slideBGPos = sliderProps.slideBGPosition;
		}

		LS_previewArea.css({
			backgroundImage: slideBG ? 'url('+slideBG+')' : 'none',
			backgroundSize: slideBGSize || 'auto',
			backgroundPosition: slideBGPos || 'center center',
			backgroundColor: slideProps.bgcolor || 'transparent',
			backgroundRepeat: 'no-repeat'
		});

		if( sliderProps.sliderclass ) {
			LS_previewArea.addClass( sliderProps.sliderclass );
		}


		// -- Set background on slide tab
		slideBG = slideBG || pluginPath+'admin/img/blank.gif';
		jQuery('#ls-slide-tabs')
			.find('.ls-slide-tab:eq('+slideIndex+') .ls-slide-preview')
			.css('background-image', slideBG);



		// --- Setup yourLogo ---
		LS_previewHolder.parent().find('.yourlogo').remove();
		if( yourLogo ) {

			var logo = jQuery('<img src="'+yourLogo+'" class="yourlogo">').prependTo( LS_previewHolder );
			logo.attr('style', yourLogoStyle);

			var oL, oR, oT, oB,
				logoLeft, logoRight, logoTop, logoBottom;
				oL = oR = oT = oB = 'auto';

			if( logo.css('left') != 'auto' ){
				logoLeft = logo[0].style.left;
			}
			if( logo.css('right') != 'auto' ){
				logoRight = logo[0].style.right;
			}
			if( logo.css('top') != 'auto' ){
				logoTop = logo[0].style.top;
			}
			if( logo.css('bottom') != 'auto' ){
				logoBottom = logo[0].style.bottom;
			}

			if( logoLeft && logoLeft.indexOf('%') != -1 ){
				oL = width / 100 * parseInt( logoLeft ) - logo.width() / 2;
			}else{
				oL = parseInt( logoLeft );
			}

			if( logoRight && logoRight.indexOf('%') != -1 ){
				oR = width / 100 * parseInt( logoRight ) - logo.width() / 2;
			}else{
				oR = parseInt( logoRight );
			}

			if( logoTop && logoTop.indexOf('%') != -1 ){
				oT = height / 100 * parseInt( logoTop ) - logo.height() / 2;
			}else{
				oT = parseInt( logoTop );
			}

			if( logoBottom && logoBottom.indexOf('%') != -1 ){
				oB = height / 100 * parseInt( logoBottom ) - logo.height() / 2;
			}else{
				oB = parseInt( logoBottom );
			}

			logo.css({
				left : oL,
				right : oR,
				top : oT,
				bottom : oB
			});
		}


		// --- Setup layers ---
		for(var c = 0, len = layers.length; c < len; c++) {
			LayerSlider.generatePreviewItem( c, post);
		}

		// --- Setup static layers ---
		LayerSlider.generateStaticPreview();
	},


	generateStaticPreview: function() {

		LS_previewArea.children('.ls-static-layer').remove();

		jQuery.each(LS_activeStaticLayersDataSet, function(idx, data) {
			LayerSlider.generatePreviewItem( idx, false, {
				$targetArea: LS_previewArea,
				$layerItem: LS_previewArea.children('.ls-static-layer').eq(idx),
				layerData: data.layerData,
				isStatic: true
			});
		});
	},


	willGeneratePreviewItem: function(layerIndex) {
		clearTimeout(LayerSlider.timeout);
		LayerSlider.timeout = setTimeout(function() {
				LayerSlider.generatePreviewItem(layerIndex);
		}, 200);
	},


	generateSelectedPreviewItems: function() {
		jQuery.each(LS_activeLayerIndexSet, function(index, layerIndex) {
			LayerSlider.generatePreviewItem( layerIndex );
		});
	},


	generatePreviewItem: function(layerIndex, post, generateProperties) {

		if( jQuery.type( layerIndex ) === 'array' ) {
			layerIndex = layerIndex[0];
		}

		generateProperties = generateProperties || {};
		generateProperties = jQuery.extend({}, {
			$targetArea: LS_previewArea,
			$layerItem: LS_previewItems[layerIndex],
			layerData: LS_activeSlideData.sublayers[layerIndex],
			isStatic: false

		}, generateProperties);

		// Don't update the editor while live previews are active
		if( LayerSlider.isLayerPreviewActive ) { return false; }

		// Remove affected item to replace with an updated one
		if( generateProperties.$layerItem ) {
			generateProperties.$layerItem.remove();
		}


		// Get layer data sets
		var layerData = generateProperties.layerData,
			layerCount 	= LS_activeSlideData.sublayers ? LS_activeSlideData.sublayers.length : 0,

			// Get layer attributes
			item,
			type 	= layerData.type,
			html 	= layerData.html,
			id 		= layerData.id,

			// Get style settings
			top 	= layerData.styles.top,
			left 	= layerData.styles.left,

			innerAttrs = layerData.innerAttributes || {},
			outerAttrs = layerData.outerAttributes || {};

		if( generateProperties.isStatic ) {
			layerIndex = layerCount + layerIndex;
		}

		switch( layerData.media ) {
			case 'img':
				type = 'img';
				break;

			case 'button':
			case 'icon':
				type = 'span';
				break;

			case 'media':
			case 'html':
				type = 'div';
				break;

			case 'post':
				type = 'post';
				break;
		}

		// Get post content if not passed
		if( ! post ) {
			var posts = window.lsPostsJSON || [],
				postOffset = LS_activeSlideData.properties.post_offset;

			if( postOffset == -1 ) {
				postOffset = LS_activeSlideIndex;
			}

			post = posts[postOffset] || {};
		}

		// Hidden layer
		if(layerData.skip || layerData['hide_on_'+LS_activeScreenType] ) {

			item = jQuery('<div class="ls-l">').appendToWithIndex(generateProperties.$targetArea, layerIndex).hide();
			if( ! generateProperties.isStatic ) {
				LS_previewItems[layerIndex] = item;
			}

			return true;
		}



		// Append element
		if(type == 'img') {
			var url = layerData.image;

			if(url == '[image-url]') {
				url = post['image-url'] || '';
				LS_GUI.updateImagePicker( 'image', post['image-url'], { fromPost: true } );
			}

			var tmpContent = url ? '<img src="'+url+'">' : '<div class="ls-layer-placeholder ls-image-placeholder"><span class="dashicons dashicons-format-image"></span><span>'+LS_l10n.SBPreviewImagePlaceholder+'</span></div>';
			item = jQuery(tmpContent).hide().appendToWithIndex(generateProperties.$targetArea, layerIndex);

		} else if(type == 'post') {

			var textlength = layerData.post_text_length;
			for(var key in post) {
				if(html && html.indexOf('['+key+']') !== -1) {
					var postVal = post[key];
					if( (key == 'title' || key == 'content' || key == 'excerpt') && textlength > 0) {
						postVal = LS_Utils.stripTags(postVal).substr(0, textlength);
						postVal = LS_Utils.nl2br(postVal);
					}
					html = html.replace('['+key+']', postVal);
				}
			}

			// Test for html wrapper
			html = jQuery.trim(html);

			var first = html.substr(0, 1),
				last = html.substr(html.length-1, 1);
			if(first == '<' && last == '>') {
				html = html.replace(/(\r\n|\n|\r)/gm,"");
				item = jQuery(html).appendToWithIndex(generateProperties.$targetArea, layerIndex);
			} else {
				item = jQuery('<div>').html(html).appendToWithIndex(generateProperties.$targetArea, layerIndex);
			}

		} else {

			// Empty media placeholder layer
			if( layerData.media === 'media' && ! html ) {
				item = jQuery('<div class="ls-layer-placeholder ls-media-placeholder"><span class="dashicons dashicons-video-alt3"></span><span>'+LS_l10n.SBPreviewMediaPlaceholder+'</span></div>').appendToWithIndex(generateProperties.$targetArea, layerIndex);

			// Empty icon placeholder layer
			} else if( layerData.media === 'icon' && ! html ) {
				item = jQuery('<div class="ls-layer-placeholder ls-icon-placeholder"><span class="dashicons dashicons-flag"></span><span>'+LS_l10n.SBPreviewIconPlaceholder+'</span></div>').appendToWithIndex(generateProperties.$targetArea, layerIndex);

			} else {

				item = jQuery('<'+type+'>').appendToWithIndex(generateProperties.$targetArea, layerIndex);
				if(html !== '') { item.html(html); }
			}
		}


		// Transition properties
		var transforms = {}, trKey, trVal, defVal;
		for( trKey in layerData.transition) {
			if( LS_transformStyles.indexOf( trKey ) !== -1) {

				trVal = layerData.transition[trKey];

				if( ! trVal && trVal !== 0 ) { continue; }

				trVal = trVal.toString();

				defVal 	= ( trKey.indexOf('scale') !== -1 ) ? 1 : 0;
				if( parseInt(trVal) !== defVal ) {
					transforms[ trKey ] = parseFloat( trVal );
				}
			}
		}

		// Styles
		var styles = { 'z-index': (100 + layerCount) - layerIndex };
		for(var sKey in layerData.styles) {
			var cssVal = layerData.styles[sKey];

			if( ( ! cssVal && cssVal !== 0 ) || cssVal === 'unset' ) { continue; }

			cssVal = cssVal.toString();
			if( cssVal.slice(-1) == ';' ) { cssVal = cssVal.substring(0, cssVal.length - 1); }

			styles[sKey] = isNumber(cssVal) ? cssVal + 'px' : cssVal;

			if( ['z-index', 'font-weight', 'opacity'].indexOf( sKey )  !== -1 ) {
				styles[sKey] = cssVal;
			}
		}

		// Background
		if( layerData.layerBackground ) {

			if( layerData.layerBackground === '[image-url]') {
				LS_GUI.updateImagePicker( 'layerBackground', post['image-url'], { fromPost: true } );
				styles['background-image'] = 'url("'+post['image-url']+'")';

			} else {
				styles['background-image'] = 'url("'+layerData.layerBackground+'")';
			}

		}

		// Locked layer
		layerData.hasTransforms = ! jQuery.isEmptyObject( transforms );



		// Apply style settings and attributes
		item.attr( jQuery.extend({}, innerAttrs, outerAttrs) ).attr({
			id: id,
			style: layerData.style,
		}).css(styles).css({
			whiteSpace: !layerData.styles.wordwrap ? 'nowrap' : 'normal',
		}).addClass(layerData['class']);

		// Restore selection
		if( ! generateProperties.isStatic ) {
			LS_previewItems[layerIndex] = item;
			if(LS_activeLayerIndexSet.indexOf(layerIndex) !== -1) {
				item.addClass('ui-selected');
			} else {
				item.removeClass('ui-selected');
			}
		}

		// Add ls-l or static layer classes
		item.addClass( generateProperties.isStatic ? 'disabled ls-static-layer' : 'ls-l' );

		if( layerData.locked ) { item.addClass('disabled'); }
		if( layerData.hasTransforms ) { item.addClass('transformed'); }
		if( document.location.href.indexOf('ls-revisions') !== -1 ) {
			item.addClass('disabled');
		}

		// Iframes & media embeds
		var $iframe = item.children('iframe,video').eq(0);
		if( $iframe.length ) {

			if( layerData.transition.backgroundvideo ) {

				item.addClass('disabled bgvideo').css({
					pointerEvents: 'none'
				}).children('video').prop('controls', false);

				if( layerData.transition.overlay ) {
					if( layerData.transition.overlayer !== 'disabled' ) {
						jQuery('<div>', {
							'class': 'video-overlay',
							'style': 'background-image: url('+layerData.transition.overlay+')'
						}).appendTo( item );
					}
				}

				// Exit script
				LayerSlider.updatePreviewSelection();
				return;

			} else {

				var width 	= parseInt( $iframe.attr('width') ) || $iframe.width(),
					height 	= parseInt( $iframe.attr('height') ) || $iframe.height();

				if( ! layerData.styles.width ) {
					item.width( width );
				}

				if( ! layerData.styles.height ) {
					item.height( height );
				}
			}
		}

		// Make sure to override controls for media elements if set by media settings.
		if( layerData.media === 'media' && item.children('audio,video').length ) {

			if( layerData.transition.controls === 'enabled' ) {
				item.children('audio,video').prop('controls', true);
			} else if( layerData.transition.controls === 'disabled' ) {
				item.children('audio,video').prop('controls', false);
			}
		}

		if( item.is('img') ) {

			item.on( 'load', function(){
				LayerSlider.setPositions(item, top, left);
				LayerSlider.updatePreviewSelection();
				clearTimeout(LayerSlider.selectableTimeout);
				LayerSlider.selectableTimeout = setTimeout(function() {
					LayerSlider.updatePreviewSelection();
				}, 100);
			}).attr('src',item.attr('src') );
		}else{
			LayerSlider.setPositions(item, top, left);
			LayerSlider.updatePreviewSelection();
		}

		// DO TRANSFORMS
		transforms.transformPerspective = 500;
		transforms.transformOrigin = layerData.transition.transformoriginin || '50% 50% 0';

		if( transforms.transformOrigin.indexOf( 'slider') !== -1 ){

			var sliderSize = LayerSlider.getSliderSize(),
				sliderWidth = sliderSize.width,
				sliderHeight = sliderSize.height,
				itemLeft = parseFloat( item[0].style.left ),
				itemTop = parseFloat( item[0].style.top ),
				itemWidth = item.outerWidth(),
				itemHeight = item.outerHeight();

			transforms.transformOrigin = transforms.transformOrigin
			.replace( 'sliderleft', -itemLeft + 'px' )
			.replace( 'sliderright', sliderWidth - itemLeft + 'px' )
			.replace( 'slidercenter', sliderWidth / 2 - itemLeft + 'px' )
			.replace( 'slidermiddle', sliderHeight / 2 - itemTop + 'px' )
			.replace( 'slidertop', -itemTop + 'px' )
			.replace( 'sliderbottom', sliderHeight - itemTop + 'px' );
		}

		TweenMax.set( item[0], transforms );

		// Add draggable
		LayerSlider.addDraggable();
	},

	setPositions: function(item, top, left, returnOnly) {

		item.show();

		var cssTop 	= top ? parseInt(top) : 0,
			cssLeft = left ? parseInt(left) : 0,
			style = item[0].style,
			marginLeft = parseInt( style.marginLeft ) || 0,
			marginTop = parseInt( style.marginTop ) || 0;

		// Position the element
		if( top && top.indexOf('%') !== -1 ) {

			if( cssTop === 0 ) {
				cssTop = 0 + marginTop;
			} else if( cssTop === 100 ) {
				cssTop = LS_previewArea.height() - item.outerHeight() + marginTop;
			} else {
				cssTop = LS_previewArea.height() / 100 * cssTop - item.outerHeight() / 2 + marginTop;
			}
		} else if( LS_activeLayerIndexSet.length === 1 ) {
			cssLeft += marginLeft;
		}

		if( left && left.indexOf('%') !== -1 ) {

			if( cssLeft === 0 ) {
				cssLeft =  0 + marginLeft;
			} else if( cssLeft === 100 ) {
				cssLeft = LS_previewArea.width() - item.outerWidth() + marginLeft;
			} else {
				cssLeft = LS_previewArea.width() / 100 * cssLeft - item.outerWidth() / 2 + marginLeft;
			}
		} else if( LS_activeLayerIndexSet.length === 1 ) {
			cssTop += marginTop;
		}

		if( returnOnly ) {
			return {
				top: cssTop,
				left: cssLeft
			};
		}

		item.css({ top: cssTop, left: cssLeft });
	},



	previewItemAtIndex: function(index) {
		return LS_previewArea.children('.ls-l').eq(index);
	},


	updatePreviewSelection: function() {

		// Hide lasso and stop execution
		// if there's no selected layers
		if( ! LS_activeLayerIndexSet.length ||
			! LS_activeSlideData.sublayers.length ||
			jQuery('.ls-editing').length) {
			$lasso.hide();
			return;
		}

		if( LS_activeLayerIndexSet.length === 1 ) {
			var layerData = LS_activeLayerDataSet[0];
			if ( layerData && ( layerData.hasTransforms || layerData.locked ) ) {
				$lasso.hide();
				return;
			}
		}

		var a = { left: Infinity, top: Infinity },
			b = { left: -Infinity, top: -Infinity };

		jQuery.each(LS_activeLayerIndexSet, function(idx, layerIndex) {
			var $item = LS_previewItems[layerIndex];
			if($item) {
				var p = $item.position(),
					q = {
						top: p.top + $item.outerHeight() * LS_previewZoom,
						left: p.left + $item.outerWidth() * LS_previewZoom
					};

				if( p.left < a.left ){ a.left = p.left; }
				if( p.top < a.top ){ a.top = p.top; }
				if( q.left > b.left ){ b.left = q.left; }
				if( q.top > b.top ){ b.top = q.top; }
			}
		});

		a.width = b.left - a.left;
		a.height = b.top - a.top;
		$lasso.css(a).show();

		if( ! $lasso.hasClass('ls-resizable-disabled') ) {
			$lasso.removeClass('ui-resizable-disabled').css(a).show();
		}


		if( LS_activeLayerIndexSet.length === 1 ) {
			var layerIndex = LS_activeLayerIndexSet[0];

			if( LS_previewItems[layerIndex] ) {
				if( LS_previewItems[layerIndex].hasClass('ls-layer-placeholder') ) {
					$lasso.addClass('ui-resizable-disabled');
				}
			}
		}

		// Mark the position of 0x0 px selection
		if( ! a.width || ! a.height ) {
			$lasso.addClass('ui-resizable-disabled');
		}
	},


	hidePreviewSelection: function() {
		jQuery('.ls-preview-wrapper').addClass('hide-selection');
	},


	showPreviewSelection: function() {
		jQuery('.ls-preview-wrapper').removeClass('hide-selection');
	},

	openMediaLibrary: function() {

		jQuery(document).on('click', '.ls-upload', function(e) {
			e.preventDefault();

			uploadInput = this;

			// Get library type
			var type = jQuery(this).hasClass('ls-insert-media') ? ['video', 'audio'] : ['image'];
			var multiple = jQuery(this).hasClass('ls-bulk-upload');

			// Media Library params
			var frame = wp.media({
				title : 'image' === type[0] ? LS_l10n.SBMediaLibraryImage : LS_l10n.SBMediaLibraryMedia,
				multiple : multiple,
				library : { type: type },
				button : { text: 'Insert' }
			});

			// Runs on select
			frame.on('select',function() {

				// Get attachment(s) data
				var attachment 	= frame.state().get('selection').first().toJSON(),
					attachments = frame.state().get('selection').toJSON(),
					updateInfo 	= [],
					previewImg, newLayerData;



				// Slide image upload
				// -------------------------------------
				if(jQuery(uploadInput).hasClass('ls-slide-image') ) {

					// Set image chooser preview
					previewImg = !typeof attachment.sizes.medium ? attachment.sizes.medium.url : attachment.sizes.full.url;
					LS_GUI.updateImagePicker( jQuery(uploadInput),  previewImg);

					// Add action to UndoManager
					LS_UndoManager.add('slide.general', LS_l10n.SBUndoSlideImage, {
						itemIndex: LS_activeSlideIndex,
						undo: {
							background: LS_activeSlideData.properties.background,
							backgroundId: LS_activeSlideData.properties.backgroundId,
							backgroundThumb: LS_activeSlideData.properties.backgroundThumb
						},
						redo: {
							background: attachment.url,
							backgroundId: attachment.id,
							backgroundThumb: previewImg
						}
					});

					// Set current layer image
					LS_activeSlideData.properties.background = attachment.url;
					LS_activeSlideData.properties.backgroundId = attachment.id;
					LS_activeSlideData.properties.backgroundThumb = previewImg;


					// Set other images
					for(c = 1; c < attachments.length; c++) {

						// Get preview image url
						previewImg = !typeof attachments[c].sizes.medium ? attachments[c].sizes.medium.url : attachments[c].sizes.full.url;

						// Build new slide
						var newSlideData = jQuery.extend(true, {}, LS_DataSource.getDefaultSlideData());
							newSlideData.background = attachments[c].url;
							newSlideData.backgroundId = attachments[c].id;
							newSlideData.backgroundThumb = previewImg;

						newSlideData = {
							properties: newSlideData,
							sublayers: []
						};

						LayerSlider.addSlide( newSlideData, {
							selectSlide: false,
							atIndex: LS_activeSlideIndex + c
						} );
					}

				LayerSlider.updateSlidePreviews();



				// Slide thumbnail upload
				// -------------------------------------
				} else if(jQuery(uploadInput).hasClass('ls-slide-thumbnail') ) {

					// Set image chooser preview
					previewImg = !typeof attachment.sizes.medium ? attachment.sizes.medium.url : attachment.sizes.full.url;
					LS_GUI.updateImagePicker( jQuery(uploadInput),  previewImg);

					// Set current layer image
					LS_activeSlideData.properties.thumbnail = attachment.url;
					LS_activeSlideData.properties.thumbnailId = attachment.id;
					LS_activeSlideData.properties.thumbnailThumb = previewImg;

					LayerSlider.updateSlidePreviews();


				// Layer image upload
				// -------------------------------------
				} else if(jQuery(uploadInput).hasClass('ls-layer-image') ) {

					// Set image chooser preview
					previewImg = !typeof attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.sizes.full.url;
					LS_GUI.updateImagePicker( jQuery(uploadInput),  previewImg);

					// Add action to UndoManager
					LS_UndoManager.add('layer.general', LS_l10n.SBUndoLayerImage, {
						itemIndex: LS_activeLayerIndexSet[0],
						undo: {
							image: LS_activeLayerDataSet[0].image,
							imageId: LS_activeLayerDataSet[0].imageId,
							imageThumb: LS_activeLayerDataSet[0].imageThumb
						},
						redo: {
							image: attachment.url,
							imageId: attachment.id,
							imageThumb: previewImg
						}
					});

					// Set current layer image
					LS_activeLayerDataSet[0].image = attachment.url;
					LS_activeLayerDataSet[0].imageId = attachment.id;
					LS_activeLayerDataSet[0].imageThumb = previewImg;

					// Set other images
					for(c = 1; c < attachments.length; c++) {

						// Get preview image url
						previewImg = !typeof attachments[c].sizes.thumbnail ? attachments[c].sizes.thumbnail.url : attachments[c].sizes.full.url;

						// Build new layer
						newLayerData = jQuery.extend(true, {}, LS_DataSource.getDefaultLayerData());
						newLayerData.image = attachments[c].url;
						newLayerData.imageId = attachments[c].id;
						newLayerData.imageThumb = previewImg;
						newLayerData.styles.top = (10*c)+'px';
						newLayerData.styles.left = (10*c)+'px';

						// Add new layer
						LS_activeSlideData.sublayers.unshift(newLayerData);
						updateInfo.push({
							itemIndex: 0,
							undo: { data: {} },
							redo: { data: newLayerData }
						});
					}

					// Rebuild layers list
					LS_DataSource.buildLayersList();

					// Maintain UndoManager
					if(updateInfo.length) {
						LS_UndoManager.add('slide.layers', LS_l10n.SBUndoNewLayers, updateInfo);
					}


				// Media (video/audio) image upload
				// -------------------------------------
				} else if( jQuery(uploadInput).hasClass('ls-media-image') ) {

					// Set image chooser preview
					previewImg = !typeof attachment.sizes.medium ? attachment.sizes.medium.url : attachment.sizes.full.url;
					LS_GUI.updateImagePicker( jQuery(uploadInput),  previewImg);

					// Add action to UndoManager
					LS_UndoManager.add('layer.general', LS_l10n.SBUndoVideoPoster, {
						itemIndex: LS_activeLayerIndexSet[0],
						undo: {
							poster: LS_activeLayerDataSet[0].poster,
							posterId: LS_activeLayerDataSet[0].posterId,
							posterThumb: LS_activeLayerDataSet[0].posterThumb
						},
						redo: {
							poster: attachment.url,
							posterId: attachment.id,
							posterThumb: previewImg
						}
					});

					// Set current layer poster
					LS_activeLayerDataSet[0].poster = attachment.url;
					LS_activeLayerDataSet[0].posterId = attachment.id;
					LS_activeLayerDataSet[0].posterThumb = previewImg;



				// Layer background upload
				// -------------------------------------
				} else if( jQuery(uploadInput).hasClass('ls-layer-background-image') ) {

					// Set image chooser preview
					previewImg = !typeof attachment.sizes.medium ? attachment.sizes.medium.url : attachment.sizes.full.url;
					LS_GUI.updateImagePicker( jQuery(uploadInput),  previewImg);

					// Add action to UndoManager
					LS_UndoManager.add('layer.general', LS_l10n.SBUndoLayerBackground, {
						itemIndex: LS_activeLayerIndexSet[0],
						undo: {
							poster: LS_activeLayerDataSet[0].layerBackground,
							layerBackgroundId: LS_activeLayerDataSet[0].layerBackgroundId,
							layerBackgroundThumb: LS_activeLayerDataSet[0].layerBackgroundThumb
						},
						redo: {
							layerBackground: attachment.url,
							layerBackgroundId: attachment.id,
							layerBackgroundThumb: previewImg
						}
					});

					// Set current layer background
					LS_activeLayerDataSet[0].layerBackground = attachment.url;
					LS_activeLayerDataSet[0].layerBackgroundId = attachment.id;
					LS_activeLayerDataSet[0].layerBackgroundThumb = previewImg;

					LayerSlider.updateLayerBackgroundNotification();



				// Global slider background
				// -------------------------------------
				} else if( jQuery(uploadInput).hasClass('ls-global-background') ) {

					// Set image chooser preview
					previewImg = !typeof attachment.sizes.medium ? attachment.sizes.medium.url : attachment.sizes.full.url;
					LS_GUI.updateImagePicker( jQuery(uploadInput),  previewImg);

					// Store changes and update the preview
					window.lsSliderData.properties.backgroundimage = attachment.url;
					window.lsSliderData.properties.backgroundimageId = attachment.id;


				// YourLogo
				// -------------------------------------
				} else if( jQuery(uploadInput).hasClass('ls-yourlogo-upload') ) {

					// Set image chooser preview
					previewImg = !typeof attachment.sizes.medium ? attachment.sizes.medium.url : attachment.sizes.full.url;
					LS_GUI.updateImagePicker( jQuery(uploadInput),  previewImg);

					// Store changes and update the preview
					window.lsSliderData.properties.yourlogo = attachment.url;
					window.lsSliderData.properties.yourlogoId = attachment.id;


				// Slider Preview
				// -------------------------------------
				} else if( jQuery(uploadInput).hasClass('ls-slider-preview') ) {

					// Set image chooser preview
					previewImg = !typeof attachment.sizes.medium ? attachment.sizes.medium.url : attachment.sizes.full.url;
					LS_GUI.updateImagePicker( jQuery(uploadInput),  previewImg);

					// Make sure that the meta object exits
					if( ! window.lsSliderData.meta ) {
						window.lsSliderData.meta = {};
					}

					// Store changes and update the preview
					window.lsSliderData.meta.preview = attachment.url;
					window.lsSliderData.meta.previewId = attachment.id;


				// Multimedia HTML
				} else if( jQuery(uploadInput).hasClass('ls-insert-media')) {

					var hasVideo 	= false,
						hasAudio 	= false,

						videos 		= [],
						audios 		= [],

						url 		= '',
						mediaHTML 	= '';

					// Iterate over selected items
					for(c = 0; c < attachments.length; c++) {

						url = '/' + attachments[c].url.split('/').slice(3).join('/');
						if(attachments[c].type === 'video') {
							hasVideo = true;
							videos.push({ url: url, mime: attachments[c].mime });

						} else if(attachments[c].type === 'audio') {
							hasAudio = true;
							audios.push({ url: url, mime: attachments[c].mime });
						}
					}

					// Insert multimedia
					if(hasVideo) {
						mediaHTML += '<video width="640" height="360" preload="metadata" controls>\r\n';
						for(c = 0; c < videos.length; c++) {
							mediaHTML += '\t<source src="'+videos[c].url+'" type="'+videos[c].mime+'">\r\n';
						}
						mediaHTML += '</video>';
					}

					if(hasAudio) {

						if(hasVideo) { mediaHTML += '\r\n\r\n'; }

						mediaHTML += '<audio preload="metadata" nocontrols>\r\n';
						for(c = 0; c < audios.length; c++) {
							mediaHTML += '\t<source src="'+audios[c].url+'" type="'+audios[c].mime+'">\r\n';
						}
						mediaHTML += '</audio>';
					}


					// Set up undoManager action
					LS_UndoManager.add('layer.general', LS_l10n.SBUndoLayer, {
						itemIndex: LS_activeLayerIndexSet[0],
						undo: { html: jQuery('.ls-html-textarea textarea').val() },
						redo: { html: mediaHTML }
					});

					// Save new value to DataStore
					LS_activeLayerDataSet[0].html = mediaHTML;
					jQuery('.ls-html-textarea textarea').val(mediaHTML);

				// Image with input field
				} else {
					jQuery(uploadInput).val( attachment.url );
					if(jQuery(uploadInput).is('input[name="image"]')) {
						jQuery(uploadInput).prev().attr('src', attachment.url);
					}
				}

				// Generate preview
				LayerSlider.generatePreview();
			});

			// Open ML
			frame.open();
		});
	},


	initImageEditor: function( imageURL ) {

		var pixieBaseURL 	= pixieModuleData.baseURL;
			pixieJSFile 	= pixieBaseURL +'/'+ pixieModuleData.jsFile;
			pixieCSSFile 	= pixieBaseURL +'/'+ pixieModuleData.cssFile;


		jQuery('<link>').appendTo('head').attr({
			type: 'text/css',
			rel: 'stylesheet',
			href: pixieCSSFile
		});

		jQuery('<pixie-editor></pixie-editor>').prependTo('body');

		jQuery.getScript( pixieJSFile ).done( function() {

			window.pixieEditor = new Pixie({
				crossOrigin: true,
				urls: {
					assets: pixieBaseURL+'/'
				},

				tools: {
					stickers: {
						replaceDefault: true,
						items: []
					}
				},

				ui: {
					mode: 'overlay',
					theme: 'dark',
					openImageDialog: false,
					toolbar: {
						hideOpenButton: true
					},

					nav: {
						position: 'top',
						replaceDefault: true,
						items: [
							{name: 'filter', icon: 'filter-custom', action: 'filter'},
							{type: 'separator'},
							{name: 'resize', icon: 'resize-custom', action: 'resize'},
							{name: 'crop', icon: 'crop-custom', action: 'crop'},
							{name: 'transform', icon: 'transform-custom', action: 'rotate'},
							{type: 'separator'},
							{name: 'draw', icon: 'pencil-custom', action: 'draw'},
							{name: 'text', icon: 'text-box-custom', action: 'text'},
							{name: 'shapes', icon: 'polygon-custom', action: 'shapes'},
							{name: 'frame', icon: 'frame-custom', action: 'frame'},
							{type: 'separator'},
							{name: 'corners', icon: 'rounded-corner-custom', action: 'round'},
							{name: 'background', icon: 'background-custom', action: 'background'},
							{name: 'merge', icon: 'merge-custom', action: 'merge'},
						]
					},
				},

				onLoad: function() {
					pixieEditor.resetAndOpenEditor('image',  imageURL );
				},

				onOpen: function() {

					// Hide any open tooltips
					kmUI.popover.close();
					setTimeout( function() {
						kmUI.popover.close();
					}, 300 );
				},

				onClose: function( ) {
					jQuery('#pixie-current-image').removeAttr('id');
				},

				onSave: function( imgData, dirtyName ) {

					// Display saving notification
					kmUI.notify.show({
						icon: 'dashicons-admin-appearance',
						iconColor: '#f9a241',
						iconSize: 36,
						text: LS_l10n.notifyPixieSave
					});

					// Introduce a little delay, so the
					// saving notification can render properly
					// before we try to do any thread-heavy thing.
					setTimeout( function() {

						// Begin image upload
						LayerSlider.uploadImageEditorPic( imgData, function() {

							// Success, hide the notification and
							// close the image editor
							kmUI.notify.hide();
							window.pixieEditor.close();
						});
					}, 1000 );
				}
			});


		}).fail( function() {
			//pixieModuleData.needsDL = true;
			//LayerSlider.openImageEditor( imageURL );
		});
	},


	downloadImageEditor: function( dlProperties ) {

		kmw.modal.open({
			content: '#tmpl-downloading-module',
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

		jQuery.getJSON( ajaxurl, {
			action: 'ls_download_module',
			module: dlProperties.moduleHandle
		}, function( data ) {

			if( data && data.success ) {

				if( dlProperties.onComplete ) {
					dlProperties.onComplete();
				}

			} else if( data.message ) {

				kmw.modal.close();
				kmw.modal.open({
					title: LS_l10n.moduleDLErrorTitle,
					content: data.message,
					animationIn: 'scale',
					overlaySettings: {
						animationIn: 'fade'
					}
				});
			}
		});
	},


	openImageEditor: function( imageURL ) {

		// Editor is ready, just open the image
		if( window.pixieEditor ) {
			pixieEditor.resetAndOpenEditor('image',  imageURL );

		// Editor needs to be loaded
		} else {


			// Editor needs to be downloaded first
			if( pixieModuleData.needsDL ) {

				LayerSlider.downloadImageEditor({
					moduleHandle: pixieModuleData.handle,
					onComplete: function() {
						kmw.modal.close();
						pixieModuleData.needsDL = false;
						LayerSlider.initImageEditor( imageURL );
					}
				});

			// Editor found, continue loading it
			} else {

				LayerSlider.initImageEditor( imageURL );
			}


		}
	},


	uploadImageEditorPic: function( imgData, callback ) {

		var $image 	= jQuery('#pixie-current-image').removeAttr('id');
			$parent = $image.closest('.ls-image'),

			imgName = 'pixie_'+Date.now()+'.png',
			imgBlob = LS_Utils.dataURItoBlob( imgData );

			imgBlob.lastModifiedDate = new Date();
			imgBlob.name = imgName;
			imgBlob.filename = imgName;

		LayerSlider.uploadImageToMediaLibrary(imgBlob, function(data) {
			$image.attr('src', data.url);

			if( $parent.hasClass('ls-slide-image') ) {

				// Add action to UndoManager
				LS_UndoManager.add('slide.general', LS_l10n.SBUndoSlideImage, {
					itemIndex: LS_activeSlideIndex,
					undo: {
						background: LS_activeSlideData.properties.background,
						backgroundId: LS_activeSlideData.properties.backgroundId,
						backgroundThumb: LS_activeSlideData.properties.backgroundThumb
					},
					redo: {
						background: data.url,
						backgroundId: data.id,
						backgroundThumb: data.url
					}
				});

				LS_activeSlideData.properties.background = data.url;
				LS_activeSlideData.properties.backgroundId = data.id;
				LS_activeSlideData.properties.backgroundThumb = data.url;

				LayerSlider.updateSlidePreviews();
				LayerSlider.generatePreview();

			} else if( $parent.hasClass('ls-slide-thumbnail') ) {

				LS_activeSlideData.properties.thumbnail = data.url;
				LS_activeSlideData.properties.thumbnailId = data.id;
				LS_activeSlideData.properties.thumbnailThumb = data.url;

				LayerSlider.updateSlidePreviews();


			} else if( $parent.hasClass('ls-layer-image') ) {

				// Add action to UndoManager
				LS_UndoManager.add('layer.general', LS_l10n.SBUndoLayerImage, {
					itemIndex: LS_activeLayerIndexSet[0],
					undo: {
						image: LS_activeLayerDataSet[0].image,
						imageId: LS_activeLayerDataSet[0].imageId,
						imageThumb: LS_activeLayerDataSet[0].imageThumb
					},
					redo: {
						image: data.url,
						imageId: data.id,
						imageThumb: data.url
					}
				});

				LS_activeLayerDataSet[0].image = data.url;
				LS_activeLayerDataSet[0].imageId = data.id;
				LS_activeLayerDataSet[0].imageThumb = data.url;

				LayerSlider.generatePreviewItem( LS_activeLayerIndexSet[0] );


			} else if( $parent.hasClass('ls-media-image') ) {

				// Add action to UndoManager
				LS_UndoManager.add('layer.general', LS_l10n.SBUndoVideoPoster, {
					itemIndex: LS_activeLayerIndexSet[0],
					undo: {
						poster: LS_activeLayerDataSet[0].poster,
						posterId: LS_activeLayerDataSet[0].posterId,
						posterThumb: LS_activeLayerDataSet[0].posterThumb
					},
					redo: {
						poster: data.url,
						posterId: data.id,
						posterThumb: data.url
					}
				});

				LS_activeLayerDataSet[0].poster = data.url;
				LS_activeLayerDataSet[0].posterId = data.id;
				LS_activeLayerDataSet[0].posterThumb = data.url;

			} else if( $parent.hasClass('ls-layer-background-image') ) {

				// Add action to UndoManager
				LS_UndoManager.add('layer.general', LS_l10n.SBUndoLayerBackground, {
					itemIndex: LS_activeLayerIndexSet[0],
					undo: {
						background: LS_activeLayerDataSet[0].layerBackground,
						layerBackgroundId: LS_activeLayerDataSet[0].layerBackgroundId,
						layerBackgroundThumb: LS_activeLayerDataSet[0].layerBackgroundThumb
					},
					redo: {
						layerBackground: data.url,
						layerBackgroundId: data.id,
						layerBackgroundThumb: data.url
					}
				});

				LS_activeLayerDataSet[0].layerBackground = data.url;
				LS_activeLayerDataSet[0].layerBackgroundId = data.id;
				LS_activeLayerDataSet[0].layerBackgroundThumb = data.url;

				LayerSlider.generatePreviewItem( LS_activeLayerIndexSet[0] );
				LayerSlider.updateLayerBackgroundNotification();
			}

			if( callback ) {
				callback();
			}
		});
	},


	handleDroppedImages: function(event) {

		var oe 	= event.originalEvent,
			files = oe.dataTransfer.files,
			p = LS_previewArea.offset(),
			x = (jQuery(window).scrollLeft() + oe.clientX - p.left) / LS_previewZoom,
			y = (jQuery(window).scrollTop() + oe.clientY - p.top) / LS_previewZoom,
			updateInfo = [],
			layerDataSet = [],
			layerIndexSet = [],
			counter = 1;

		// Iterate over the dropped files
		jQuery.each(files, function(index, file) {
			LayerSlider.uploadImageToMediaLibrary(file, function(data) {

				// Build new layer
				var layerData = jQuery.extend(true, {}, LS_DataSource.getDefaultLayerData());
				layerData.image = data.url;
				layerData.imageId = data.id;
				layerData.imageThumb = data.sizes.thumbnail ? data.sizes.thumbnail.url : data.url;
				layerData.subtitle = file.name;
				layerData.styles.left = x+'px';
				layerData.styles.top = y+'px';

				layerIndexSet.push(0);
				layerDataSet.push(layerData);

				// Increase next layer offsets
				x += 20;
				y += 20;

				// Add new layers when every image
				// has been uploaded
				if(counter++ === files.length) {
					LayerSlider.addLayer( layerDataSet, layerIndexSet );
				}
			});
		});
	},


	uploadImageToMediaLibrary: function(file, callback) {
		if(file.type.indexOf('image') === 0) {

			// Build FormData object
			var formData = new FormData();
			formData.append('action', 'upload-attachment');
			formData.append('async-upload', file, file.name);
			formData.append('name', file.name);
			formData.append('_wpnonce', _wpPluploadSettings.defaults.multipart_params._wpnonce);

			jQuery.ajax({
				url: ajaxurl.replace('admin-ajax', 'async-upload'),
				method: 'POST',
				data: formData,
				dataType: 'json',
				processData: false,
				contentType: false,
				error: function(jqXHR, textStatus, errorThrown) {
					alert( LS_l10n.SBUploadErrorMessage.replace('%s', errorThrown) );
				},
				success: function(resp) {

					if(!resp || !resp.success) {
						alert(LS_l10n.SBUploadError);
						return;
					}

					if(typeof callback != "undefined") {
						callback(resp.data);
					}
				}
			});
		}
	},

	addLayerSortables: function() {

		// Bind sortable function
		jQuery('.ls-sublayer-sortable').sortable({

			handle : 'span.ls-sublayer-sortable-handle',
			containment : 'parent',
			tolerance : 'pointer',
			axis : 'y',

			start: function() {
				LayerSlider.dragIndex = jQuery('.ui-sortable-placeholder').index() - 1;
			},

			change: function() {
				jQuery('.ui-sortable-helper').addClass('moving');
			},

			stop: function(event, ui) {

				// Get indexes
				var oldIndex = LayerSlider.dragIndex;
				var index = jQuery('.moving').removeClass('moving').index();

				LS_UndoManager.add('layer.order', LS_l10n.SBUndoSortLayers, {
					itemIndex: null,
					undo: { from: index, to: oldIndex },
					redo: { from: oldIndex, to: index }
				});

				if( index > -1 ){
					LS_Utils.moveArrayItem(LS_activeSlideData.sublayers, oldIndex, index);
				}

				// Update active layer index
				LS_activeLayerIndexSet = [];
				jQuery('.ls-sublayers li.active').each(function() {
					LS_activeLayerIndexSet.push( jQuery(this).index() );
				});

				// Reindex layers
				LayerSlider.reindexLayers();
				LayerSlider.generatePreview();
			}
		});
	},


	addSlideSortables: function() {

		if( document.location.href.indexOf('section=revisions') !== -1 ) {
			return;
		}

		jQuery('#ls-slide-tabs').sortable({

			containment: 'parent',
			tolerance: 'pointer',
			handle: '.ls-slide-preview',
			items: '.ls-slide-tab',
			cancel: '.unsortable',
			distance: 3,

			start: function() {
				LayerSlider.dragIndex = jQuery('.ui-sortable-placeholder').index() - 1;
			},

			change: function() {
				jQuery('.ui-sortable-helper').addClass('moving');
			},

			stop: function(event, ui) {

				// Get indexes
				var oldIndex = LayerSlider.dragIndex,
					index = jQuery('.moving').removeClass('moving').index();

				if( index > -1 ){
					LS_Utils.moveArrayItem(window.lsSliderData.layers, oldIndex, index);
				}

				// Update active slide index
				LS_activeSlideIndex = jQuery('#ls-slide-tabs .ls-slide-tab.active').index();

				// Add static layers
				LS_activeStaticLayersDataSet = LayerSlider.staticLayersForSlide( LS_activeSlideIndex );

				// Reindex slides
				LayerSlider.reindexStaticLayers();
				LayerSlider.generateStaticPreview();
				LS_DataSource.buildLayersList();
			}
		});
	},


	addDraggable: function() {

		// Add dragables and update settings
		// while and after dragging
		LS_previewArea.children('.ls-l').draggable({
			snap: true,
			snapTolerance: 10,
			cancel: '.disabled,.transformed',
			start: function(e, ui) {

				// Fix for deselect
				if( !ui.helper.hasClass('ui-selected') ){
					ui.helper.addClass('ui-selected').trigger('selectablestop.ls');
				}

				// Store selected layers & lasso originalPosition
				$lasso.data('originalPosition', $lasso.position());
				jQuery('.ls-preview .ui-selected').each(function() {
					var pos = jQuery(this).position();
					jQuery(this).data('originalPosition', {
						'top': pos.top / LS_previewZoom,
						'left': pos.left / LS_previewZoom,
					});
				});
			},

			drag: function(event, ui) {
				LayerSlider.dragging(ui);
			},

			stop: function(event, ui) {

				var updateInfo = [];
				LayerSlider.dragging(ui);

				jQuery('.ls-preview .ui-selected').each(function() {

					var $layer 			= jQuery(this),
						index 			= $layer.index(),
						position 		= $layer.position(),
						newTop 			= Math.round( position.top / LS_previewZoom ) +'px',
						newLeft 		= Math.round( position.left / LS_previewZoom ) +'px',
						origPosition 	= $layer.data('originalPosition');


					// Maintain changes in data source
					LS_activeSlideData.sublayers[index].styles.top  = newTop;
					LS_activeSlideData.sublayers[index].styles.left = newLeft;

					// Gather changes for undoing
					updateInfo.push({
						itemIndex: index,
						undo: { left: origPosition.left+'px', top: origPosition.top+'px' },
						redo: { left: newLeft, top: newTop }
					});
				});

				// Add changes to undoManager
				LS_UndoManager.add('layer.style', LS_l10n.SBUndoLayerPosition, updateInfo.reverse());
			}
		});
	},


	dragging: function(ui) {

		// Fix positions when zoomed
		ui.position.top = Math.round(ui.position.top  / LS_previewZoom );
		ui.position.left = Math.round(ui.position.left / LS_previewZoom );

		var index 	= ui.helper.index(),
			top 	= Math.round( ui.position.top ),
			left 	= Math.round( ui.position.left );

		// Update input field values if it's visible
		if(LS_activeLayerIndexSet.length === 1) {

			// Update input fields
			jQuery('.ls-sublayer-style input[name="top"]').val( ui.helper.position().top / LS_previewZoom + 'px');
			jQuery('.ls-sublayer-style input[name="left"]').val( ui.helper.position().left / LS_previewZoom + 'px');
		}
	},


	resizing: function(e, ui) {

		var rh = ui.size.height / ui.originalSize.height,
			rw = ui.size.width / ui.originalSize.width,
			uiRatio = ui.originalSize.width / ui.originalSize.height,
			tagNames = [], layer, $layer, layerIndex, layerData, width,
			height, op, os, r;

			if( !$lasso.data( 'dragDirection') ){
				$lasso.data( 'dragDirection', rh === 1 ? 'horizontal' : 'vertical' );
			}

		// Update layer data
		jQuery('.ls-preview .ui-selected').each(function() {

			layer 		= this;
			$layer 		= jQuery(this);
			layerIndex 	= $layer.index();
			layerData 	= LS_activeSlideData.sublayers[layerIndex];

			tagNames.push( layer.tagName.toLowerCase() );

			op = $layer.data('originalPosition');
			os = $layer.data('originalSize');

			layerData.styles.top 	= layer.style.top 	= Math.round( (op.top - Math.round( ui.originalPosition.top / LS_previewZoom ) ) * rh + Math.round( ui.position.top / LS_previewZoom ) ) + 'px';
			layerData.styles.left 	= layer.style.left 	= Math.round( (op.left - Math.round( ui.originalPosition.left / LS_previewZoom ) ) * rw + Math.round( ui.position.left / LS_previewZoom ) ) + 'px';

			width = Math.round(os.width * rw) + 'px';
			height = Math.round(os.height * rh) + 'px';

			if( layerData.styles.width || $layer.is('img,div') ) {
				layerData.styles.width 	= width;
			}

			if( layerData.styles.height || $layer.is('img,div') ) {
				layerData.styles.height = height;
			}

			$layer.outerWidth(width);
			$layer.outerHeight(height);


			// Font-size only
			if( ! $layer.is( 'img, iframe, video, audio' ) ) {
				r = ui.size.width / ui.originalSize.width;
				layerData.styles['font-size'] 	= layer.style.fontSize 	= Math.round( r * os.fontSize ) +'px';

				if( os.lineHeight ) {
					layerData.styles['line-height'] = layer.style.lineHeight = Math.round( r * os.lineHeight ) +'px';
				}
			}

			if(LS_activeLayerIndexSet.length === 1) {

				if( layerData.styles.width || $layer.is('img,div') ) {
					jQuery('.ls-sublayer-style input[name="width"]').val( layer.style.width);
				}

				if( layerData.styles.height || $layer.is('img,div') ) {
					jQuery('.ls-sublayer-style input[name="height"]').val( layer.style.height);
				}

				jQuery('.ls-sublayer-style input[name="top"]').val( layer.style.top);
				jQuery('.ls-sublayer-style input[name="left"]').val( layer.style.left);
				jQuery('.ls-sublayer-style input[name="font-size"]').val(layerData.styles['font-size']);
				if( os.lineHeight ) {
					jQuery('.ls-sublayer-style input[name="line-height"]').val( layerData.styles['line-height']+'px' );
				}
			}
		});

		if( tagNames.indexOf('img') === -1 && tagNames.indexOf('div') === -1 ) {
			switch( $lasso.data( 'dragDirection') ){
				case 'horizontal':
					ui.size.height = ui.size.width / uiRatio;
				break;
				case 'vertical':
					ui.size.width = ui.size.height * uiRatio;
				break;
			}
		}

		// Update lasso size info
		$lasso.attr({
			'data-info-0': 'w: ' + Math.round(ui.size.width) + 'px',
			'data-info-1': 'h: ' + Math.round(ui.size.height) + 'px'
		});
	},

	contextMenu: function(e) {

		// Bail out if preview is active or when using Revisions
		if( LayerSlider.isSlidePreviewActive || LayerSlider.isLayerPreviewActive || document.location.href.indexOf('ls-revisions') !== -1 ) {
			return;
		}

		// Vars to hold overlapping elements
		// and mouse position
		var items 	= [],
			mt 		= e.pageY;
			ml 		= e.pageX;


		LS_contextMenuTop = e.pageY - LS_previewArea.offset().top;
		LS_contextMenuLeft = e.pageX - LS_previewArea.offset().left;

		// Loop through layers list
		LS_previewArea.children('.ls-l').each(function(layerIndex) {

			// Get layer item and data
			var $layer 		= jQuery(this),
				layerData 	= LS_activeSlideData.sublayers[ $layer.index() ],

				// Get layer positions and dimensions
				t = LS_previewArea.offset().top + $layer.position().top,
				l = LS_previewArea.offset().left + $layer.position().left,
				w = $layer.outerWidth() * LS_previewZoom,
				h = $layer.outerHeight() * LS_previewZoom;

			if( (mt > t && mt < t+h) && (ml > l && ml < l+w) ) {
				items.push({ index: layerIndex, data: layerData });
			}
		});


		// Attempt to find the contextmenu instance.
		var $contextMenu = jQuery('.ls-preview-context-menu');

		// Create new contextmenu instance if it wasn't
		// added to the document previously.
		if( ! $contextMenu.length ) {
			$contextMenu = jQuery( jQuery('#tmpl-ls-preview-context-menu').text() ).hide().prependTo('body');
		}

		// Reset the contextmenu and position it to its new location
		$contextMenu.find('.ls-context-overlapping-layers ul').empty();
		$contextMenu.find('li').show();
		$contextMenu
			.stop( true, true )
			.css({ top: mt, left: ml })
			.fadeIn( 100 );


		// Close event
		jQuery('body').on('mousedown.ls-context-menu', function(e) {

			var $target = jQuery( e.target );

			// Prevent closing the context menu by default if
			// the event target was within it and the action
			// should depend on the clicked item.
			if( $target.closest('.ls-preview-context-menu').length ) {

				// Clicked item is not a group list and has an action
				// to be executed. Close the context menu with a bit of
				// delay, so the event handler can be triggered before the
				// item becomes hidden, which can cause issues to browsers.
				if( ! $target.is('li.group') ) {
					setTimeout(function() {
						LayerSlider.closeContextMenu();
					}, 150 );


				}

			// The event target has nothing to do with the context
			// menu, so it can be closed right away.
			} else {
				LayerSlider.closeContextMenu();
			}
		});

		// Loop through intersecting elements (if any)
		if( items.length > 1 ) {
			jQuery.each( items, function( idx, data ) {

				var layerIndex = data.index,
					layerData = data.data,

					$li = jQuery('<li><p></p><span>'+layerData.subtitle+'</span></li>').appendTo( $contextMenu.find('.ls-context-overlapping-layers ul') );
					$li.data('layerIndex', layerIndex);

				LayerSlider.setLayerMedia( layerData.media,  jQuery('p', $li), layerData );
			});
		} else {
			$contextMenu.find('.ls-context-overlapping-layers').hide();
		}

		// Empty slide, no layers
		if( ! LS_activeSlideData.sublayers.length ) {
			$contextMenu.find(' > ul > li').not(':first-child, .ls-context-menu-paste-layer').hide();
		}

	},

	closeContextMenu: function() {

		jQuery('body').off('mousedown.ls-context-menu');
		jQuery('.ls-preview-context-menu').fadeOut( 100 );
	},


	highlightPreviewItem: function(el) {

		// Get layer related data
		var layerIndex = jQuery(el).data('layerIndex');
		var $previewItem = LS_previewArea.children('.ls-l').eq(layerIndex);


		// Highlight item
		$previewItem.addClass('highlighted').siblings().addClass('lowlighted');

	},


	selectPreviewItem: function( layerIndex, event ) {

		// Remove layer highlights (if any)
		LS_previewArea.children().removeClass('highlighted lowlighted');

		if( ! event.ctrlKey && ! event.metaKey ) {
			if( JSON.stringify(LS_activeLayerIndexSet) !== '['+layerIndex+']' ) {
				return LayerSlider.selectLayer( [ layerIndex ] );
			}

		} else {

			// Get layer
			var $previewItem = LS_previewArea.children().eq( layerIndex );

			// Select layer
			LS_previewHolder.triggerHandler(
				jQuery.Event('mousedown.ls', {
					target: $previewItem[0],
					which: 1,
					shiftKey: event.shiftKey,
					ctrlKey: event.ctrlKey,
					metaKey: event.metaKey
				})
			);
		}
	},


	editLayerToggle: function() {
		if(LS_activeLayerIndexSet.length === 1) {
			var $editing 	= jQuery('.ls-editing'),
				$layer 		= LS_previewItems[ LS_activeLayerIndexSet[0] ];

			if(!$editing.length) {
				this.editLayerStart($layer);
			} else {
				this.editLayerEnd($editing);
			}
		}
	},


	editLayerStart: function( $layer ) {

		// Bring up the Media Library in case of image layer
		if( $layer.is('img') || LS_activeLayerDataSet[0].media === 'img' ) {
			jQuery('.ls-layer-image').click();
			return false;

		// Bring up the Icon Chooser in case of an icon layer
		} else if( LS_activeLayerDataSet[0].media === 'icon' ) {
			jQuery('.ls-replace-icon').last().click();
			return false;

		// Do nothing with media layers
		} else if( LS_activeLayerDataSet[0].media === 'media' ) {
			LS_InsertMedia.open();
			return false;
		}

		LayerSlider.selectLayer( [$layer.index() ] );

		// Get layer data
		var layerData = LS_activeLayerDataSet[0];

		// Bail out early if it's a locked layer
		if( $layer.hasClass('disabled') || layerData.locked) { return false; }

		// Enable editing
		$layer.addClass('disabled ls-editing')
			.prop('contenteditable', true)
			.focus();

		// Hide selectable/resizable
		$lasso.addClass('ui-resizable-disabled').hide();

		// Save current value for undoManager
		jQuery('.ls-html-code textarea').data('prevVal',layerData.html);

		// Select all text
		document.execCommand('selectAll');

		// End editing when clicking away
		jQuery(document).on('click.ls-editing', function(event) {
			if(!jQuery(event.target).hasClass('ls-editing')) {
				LayerSlider.editLayerEnd( jQuery('.ls-editing') );
			}
		});
	},

	editLayer: function(e) {
		if((e.metaKey || e.ctrlKey || e.altKey) && e.which === 13) {
			e.preventDefault();
			document.execCommand('insertHTML', false, '\r\n&nbsp;');
		}
	},

	editLayerUpdate: function(layer) {
		var content 	= layer.textContent,
			$textarea 	= jQuery('.ls-html-code textarea'),
			styles 		= LS_activeLayerDataSet[0].styles;

		$textarea.val(content);
		LS_activeLayerDataSet[0].html = content;

		LayerSlider.setPositions( jQuery(layer), styles.top, styles.left);
	},

	editLayerPaste: function(event) {
		event.preventDefault();
		document.execCommand('insertHTML', false,
			event.originalEvent.clipboardData.getData('text/plain')
		);
	},

	editLayerEnd: function($layer) {
		jQuery(document).off('click.ls-editing');
		$layer.prop('contenteditable', false).removeClass('disabled ls-editing');
		jQuery('.ls-html-code textarea').trigger('change');
		LayerSlider.updatePreviewSelection();
	},

	reindexLayers: function(el) {

		var layerCount = LS_activeSlideData.sublayers.length;
			layerCount = layerCount ? layerCount : 0;

		// Reindex default layers' title
		jQuery('#ls-layers .ls-sublayers > li').each(function(index) {
			var layerTitle 	= jQuery(this).find('.ls-sublayer-title').val(),
				pattern 	= LS_l10n.SBLayerTitle.substring(0, LS_l10n.SBLayerTitle.length-2);

			if( layerTitle.indexOf(pattern) != -1 && layerTitle.indexOf('copy') == -1) {
				jQuery(this).find('.ls-sublayer-title').val( LS_l10n.SBLayerTitle.replace('%d', (layerCount-index) ) );
			}
		});
	},


	updateSlidePreviews: function() {

		jQuery('#ls-slide-tabs .ls-slide-tab').each( function( index ) {

			var $slide 		= jQuery( this ),
				$preview 	= $slide.children('.ls-slide-preview'),
				slideIndex 	= $slide.index(),
				slideData 	= window.lsSliderData.layers[ slideIndex ],
				slideProps 	= slideData.properties;


			if( slideProps.thumbnail ) {
				$preview.css('background-image', 'url('+slideProps.thumbnail+')' );
				$slide.removeClass('empty');

			} else if( slideProps.background ) {
				$preview.css('background-image', 'url('+slideProps.background+')' );
				$slide.removeClass('empty');

			} else {
				$preview.css('background-image', 'none');
				$slide.addClass('empty');
			}
		});
	},


	rebuildSlides: function() {

		var $tab;

		jQuery.each(window.lsSliderData.layers, function(slideKey, slideData) {

			// Try to locate a preview tab and update it if exists
			$tab = jQuery('#ls-slide-tabs').children().eq( slideKey );

			// Create a new tab if needed
			if( ! $tab.length ) {
				$tab = jQuery( jQuery('#tmpl-slide-tab').text() ).appendTo('#ls-slide-tabs');
			}

			// Update title
			if( slideData.properties.title ) {
				$tab.find('.ls-slide-name input').val( slideData.properties.title );
			}
		});

		// Remove additional and unnecessary tabs (if any)
		$tab.nextAll().remove();

		// Restore active selection
		jQuery('#ls-slide-tabs')
			.children()
			.removeClass('active')
			.eq( LS_activeSlideIndex )
			.addClass('active');

		LayerSlider.updateSlidePreviews();
	},

	checkMediaAutoPlay: function( $textarea, prop, val ) {

		clearTimeout( LayerSlider.mediaCheckTimeout );
		LayerSlider.mediaCheckTimeout = setTimeout( function() {

			if( val.indexOf('autoplay') !== -1 ) {

				var $media = jQuery(val).filter('iframe'),
					autoplayDetected = false;

				 if( $media.is('iframe') ) {

					var URL = $media.attr('src').split('?'),
						targetIndex = -1;

					if( URL[1] ) {
						params = URL[1].split('&');
						jQuery.each(params, function(index, item) {
							if( item.indexOf('autoplay') !== -1 ) {
								autoplayDetected = true;
								targetIndex = index;
							}
						});

						if( targetIndex > -1 ) {
							params.splice(targetIndex, 1);
						}
					}

					if( typeof params !== 'undefined' ) {
						$media.attr('src', URL[0]+'?'+params.join('&') );
					}

				 } else if( $media.is('video') || $media.is('audio') ) {
					autoplayDetected = true;
					$media.removeAttr('autoplay');
				 }


				 if( autoplayDetected ) {

					$textarea.val( $media[0].outerHTML );
					$autoplay = jQuery('select[name="autoplay"]');

					jQuery('option', $autoplay)
						.prop('selected', false)
						.eq(1).prop('selected', true);

					TweenLite.to($autoplay[0], 0.2, {
						css: { scale: 1.3 },
						onComplete: function() {
							TweenLite.to($autoplay[0], 0.2, {
								css: { scale: 1 }
							});
						}
					});
				}
			}
		}, 100, $textarea, prop, val);
	},

	startSlidePreview: function( sliderOptions ) {

		// Stop **layer** preview if it's currently running
		// to prevent simultaneous instances
		this.stopLayerPreview(true);

		// Stop slide preview if it's currently running
		if(this.isSlidePreviewActive) {
			LayerSlider.stopSlidePreview();
			return true;
		}

		this.isSlidePreviewActive = true;

		sliderOptions = sliderOptions || {};

		// Get slider settings and preview container
		var sliderProps = window.lsSliderData.properties,
			sliderSize 	= LayerSlider.getSliderSize(),
			plugins 	= [];

		// Switch between preview and editor
		var $slider  = jQuery('#ls-layers .ls-real-time-preview').show();
			$slider  = jQuery('<div id="ls-preview-timeline" class="ls-wp-container">').appendTo( $slider );

		if( sliderProps.sliderclass ) {
			$slider.addClass( sliderProps.sliderclass );
		}

		jQuery('#ls-layers .ls-preview').hide();
		jQuery('#ls-layers .ls-preview-button').html('Stop').addClass('playing');

		LayerSlider.hidePreviewSelection();

		// Empty the preview area to avoid ID collisions
		LS_previewArea.empty();

		// Append slides & layers
		this.populateSliderPreview( $slider, plugins );

		// Handle plugins
		if( sliderOptions && sliderOptions.plugins ) {
			sliderOptions.plugins = jQuery.merge(sliderOptions.plugins, plugins);
		}

		var sliderDefaults = {
			type: 'responsive',
			width: sliderSize.width,
			height: sliderSize.height,
			skin: 'v6',
			skinsPath: pluginPath + 'layerslider/skins/',
			firstSlide: LS_activeSlideIndex + 1,
			autoStart: true,
			pauseOnHover: false,
			startInViewport: false,
			autoPlayVideos: sliderProps.autoplayvideos ? true : false,
			slideBGSize: sliderProps.slideBGSize,
			slideBGPosition: sliderProps.slideBGPosition,
			globalBGColor: sliderProps.backgroundcolor,
			globalBGImage: sliderProps.backgroundimage,
			globalBGAttachment: sliderProps.globalBGAttachment,
			globalBGRepeat: sliderProps.globalBGRepeat,
			globalBGPosition: sliderProps.globalBGPosition,
			globalBGSize: sliderProps.globalBGSize,
			parallaxScrollReverse: sliderProps.parallaxScrollReverse,
			playByScroll: sliderProps.playByScroll ? true : false,
			playByScrollStart: sliderProps.playByScrollStart ? true : false,
			playByScrollSkipSlideBreaks: sliderProps.playByScrollSkipSlideBreaks ? true : false,
			playByScrollSpeed: sliderProps.playByScrollSpeed || 1,
			navButtons: false,
			navStartStop: false,
			forceLayersOutDuration: sliderProps.forceLayersOutDuration || 750,
			allowRestartOnResize: sliderProps.allowRestartOnResize ? true : false,
			preferBlendMode: sliderProps.preferBlendMode,
			plugins: plugins
		};

		if( sliderProps.maxRatio ) {
			sliderDefaults.maxRatio = sliderProps.maxRatio;
		}

		// Init layerslider
		$slider.layerSlider(
			jQuery.extend( true, sliderDefaults, sliderOptions )

		).on('slideTimelineDidComplete', function( event, slider ) {
			// if( jQuery('.ls-timeline-switch li').eq(0).hasClass('active') ) {
			// 	slider.api('replay');
			// 	return false;
			// }

		}).on( 'slideTimelineDidCreate', function(){
			jQuery( '.ls-slidebar-slider' ).attr({
				'data-help': LS_l10n.SBDragMe,
				'data-km-ui-popover-once': 'true',
				'data-km-ui-popover-theme': 'red',
				'data-km-ui-popover-autoclose': 3,
				'data-km-ui-popover-distance': 20
			}).trigger( 'mouseenter' );
		});
	},



	stopSlidePreview: function() {

		if( this.isSlidePreviewActive ) {
			this.isSlidePreviewActive = false;

			// Show the editor
			jQuery('#ls-layers .ls-preview').show();

			// Stop LayerSlider and empty the preview contents
			var layersliders = jQuery('#ls-layers .ls-real-time-preview');
			layersliders.find('.ls-container').layerSlider( 'destroy', true );
			layersliders.hide();

			// Rewrote the Preview button text
			var btnText = document.location.href.indexOf('ls-revisions') !== -1 ? LS_l10n.SBPreviewSlide : LS_l10n.slideNoun;
			jQuery('#ls-layers .ls-preview-button').text( btnText ).removeClass('playing');

			kmUI.popover.close();

			LayerSlider.generatePreview();
			LayerSlider.showPreviewSelection();
			LayerSlider.updatePreviewSelection();

			// Remove timeline
			jQuery('.ls-timeline-switch li:first-child').click();

			// SET: layer editor size
			kmUI.smartResize.set();
		}
	},


	startPopupPreview: function( sliderOptions, button ) {

		// Stop both layer & slide preview if they are active
		this.stopLayerPreview(true);
		this.stopSlidePreview();

		sliderOptions = sliderOptions || {};

		// Prevent pressing the Preview button multiple times
		jQuery(button).prop('disabled', true);
		setTimeout(function() {
			jQuery(button).prop('disabled', false);
		}, 1000);

		// Get slider settings and preview container
		var sliderProps = window.lsSliderData.properties,
			width 		= parseInt(sliderProps.popupWidth),
			height 		= parseInt(sliderProps.popupHeight),
			sliderCSS 	= sliderProps.sliderstyle,
			circleTimer = sliderProps.circletimer ? true : false,
			plugins 	= ['popup'];

		// Append live preview element
		var $slider  = jQuery('<div id="ls-popup-preview" class="ls-wp-container">').appendTo('body');

		if( sliderCSS ) {
			$slider.attr('style', sliderCSS);
		}

		if( sliderProps.sliderclass ) {
			$slider.addClass( sliderProps.sliderclass );
		}


		// Get popup init options
		jQuery('.ls-settings-popup .popup-prop').each(function() {
			if( this.name ) { sliderOptions[ this.name ] = window.lsSliderData.properties[ this.name ]; }
		});

		// Append slides & layers
		if( LayerSlider.sliderIsEmpty( 1 ) ) {
			$slider.html( jQuery('#tmpl-popup-example-slider').text() );
			width = 700;
			height = 500;
			circleTimer = false;
			sliderOptions.popupCloseButtonStyle = 'top: 20px; left: 40px;';
			sliderOptions.popupPositionHorizontal = 'center';
			sliderOptions.popupPositionVertical = 'middle';
			sliderOptions.popupFitWidth = false;
			sliderOptions.popupFitHeight = false;
		} else {
			this.populateSliderPreview( $slider, plugins );
		}



		// Handle plugins
		if( sliderOptions && sliderOptions.plugins ) {
			sliderOptions.plugins = jQuery.merge(sliderOptions.plugins, plugins);
		}

		var sliderDefaults = {
			type: 'popup',
			width: width,
			height: height,
			popupWidth: width,
			popupHeight: height,
			skin: sliderProps.skin,
			skinsPath: pluginPath + 'layerslider/skins/',
			autoStart: sliderProps.autostart ? true : false,
			pauseOnHover: sliderProps.pauseonhover,
			firstSlide: sliderProps.firstlayer,
			shuffleSlideshow: sliderProps.randomslideshow ? true : false,
			navPrevNext: sliderProps.navprevnext ? true : false,
			hoverPrevNext: sliderProps.hoverprevnext ? true : false,
			navStartStop: sliderProps.navstartstop ? true : false,
			navButtons: sliderProps.navbuttons ? true : false,
			hoverBottomNav: sliderProps.hoverbottomnav ? true : false,
			showBarTimer: sliderProps.bartimer ? true : false,
			showCircleTimer: circleTimer,
			thumbnailNavigation: sliderProps.thumb_nav,
			tnContainerWidth: sliderProps.thumb_container_width,
			tnWidth: sliderProps.thumb_width,
			tnHeight: sliderProps.thumb_height,
			tnActiveOpacity: sliderProps.thumb_active_opacity,
			tnInactiveOpacity: sliderProps.thumb_inactive_opacity,
			startInViewport: false,
			autoPlayVideos: sliderProps.autoplayvideos ? true : false,
			slideBGSize: sliderProps.slideBGSize,
			slideBGPosition: sliderProps.slideBGPosition,
			globalBGColor: sliderProps.backgroundcolor,
			globalBGImage: sliderProps.backgroundimage,
			globalBGAttachment: sliderProps.globalBGAttachment,
			globalBGRepeat: sliderProps.globalBGRepeat,
			globalBGPosition: sliderProps.globalBGPosition,
			globalBGSize: sliderProps.globalBGSize,
			parallaxScrollReverse: sliderProps.parallaxScrollReverse,
			forceLayersOutDuration: sliderProps.forceLayersOutDuration || 750,
			allowRestartOnResize: sliderProps.allowRestartOnResize ? true : false,
			preferBlendMode: sliderProps.preferBlendMode,
			plugins: plugins,

			// Popup Settings
			popupShowOnce: true,
			popupShowOnTimeout: 0.01,
			popupDisableOverlay: false,
			popupOverlayClickToClose: true
		};

		if( sliderProps.maxRatio ) {
			sliderDefaults.maxRatio = sliderProps.maxRatio;
		}

		// Init layerslider
		$slider.layerSlider( jQuery.extend( true, sliderDefaults, sliderOptions ) );
	},


	populateSliderPreview: function( $slider, plugins ) {

		var sliderProps = window.lsSliderData.properties,
			callbacks 	= window.lsSliderData.callbacks,
			posts 		= window.lsPostsJSON || [];

		// Iterate over the slides
		jQuery.each(window.lsSliderData.layers, function(slideIndex, slideData) {

			// Slide data
			var slideProps = slideData.properties,
				layers = slideData.sublayers.reverse();

			// Get post content if any
			var postOffset = slideProps.post_offset;
			if(postOffset == -1) { postOffset = slideIndex; }
			var post = posts[postOffset];

			// Slide attributes
			var properties = '', sKey, sVal;
			for( sKey in slideProps) {
				sVal = slideProps[ sKey ];
				if( sVal !== '' && sVal !== 'null' ) {

					// Slide BG inheritance
					if( sKey === 'bgsize' && sVal === 'inherit' ) {
						sVal = sliderProps.slideBGSize;

					} else if( sKey === 'bgposition' && sVal === 'inherit' ) {
						sVal = sliderProps.slideBGPosition;
					}

					if( sKey === 'transitionorigami' && sVal ) {
						if(plugins.indexOf('origami') === -1) {
							plugins.push('origami');
						}
					}

					properties += sKey+':'+sVal+';';
				}
			}

			// Build the Slide
			var layer = jQuery('<div class="ls-slide">')
							.attr('data-ls', properties)
							.appendTo( $slider );

			// Get background
			var background = slideProps.background;
			if(background === '[image-url]') {
				background = post['image-url'];
			}

			// Add background
			if(background) {
				jQuery('<img src="'+background+'" class="ls-bg">').appendTo(layer);
			}

			// Get selected transitions
			var tr2d = slideProps['2d_transitions'],
				tr3d = slideProps['3d_transitions'],
				tr2dcustom = slideProps.custom_2d_transitions,
				tr3dcustom = slideProps.custom_3d_transitions;

			// Apply transitions
			if(tr2d) layer.attr('data-ls', layer.attr('data-ls') + ' transition2d: '+tr2d+'; ');
			if(tr3d) layer.attr('data-ls', layer.attr('data-ls') + ' transition3d: '+tr3d+'; ');
			if(tr2dcustom) layer.attr('data-ls', layer.attr('data-ls') + ' customtransition2d: '+tr2dcustom+'; ');
			if(tr3dcustom) layer.attr('data-ls', layer.attr('data-ls') + ' customtransition3d: '+tr3dcustom+'; ');


			// Iterate over layers
			jQuery.each(layers, function(layerKey, layerData) {
				LayerSlider.appendLivePreviewItem(layerKey, layerData, layer, post);
			});

			// Revert back to original layer order, as the reversed
			// layers list is only a visual thing on the admin UI.
			slideData.sublayers.reverse();
		});


		// Apply API events (if any)
		if( callbacks ) {

			for( var key in callbacks ) {

				var callback 	= callbacks[ key ],
					startIndex 	= callback.indexOf('{') + 1,
					endIndex 	= callback.length - 1;
					body 		= callback.substring(startIndex, endIndex);

				$slider.on(key, new Function('event', 'slider', body));
			}
		}
	},


	startLayerPreview: function(button, forceStop) {

		// Stop **slide** preview if it's currently running
		// to prevent simultaneous instances
		this.stopSlidePreview();

		// Stop or restart current preview session (if any)
		if(this.isLayerPreviewActive){
			LayerSlider.stopLayerPreview(forceStop);

			if( !!forceStop ){
				return;
			}
		}


		// Check for Multi-Select
		if( LS_activeLayerDataSet.length > 1 ) {
			alert(LS_l10n.SBLayerPreviewMultiSelect);
			return;
		}

		// Change preview state
		this.isLayerPreviewActive = true;
		jQuery(button).addClass('playing').text( LS_l10n.stop );

		// Hide other layers
		LayerSlider.hidePreviewSelection();
		LS_previewArea.children().addClass('ls-transparent');

		// Create container element
		var $wrapper = jQuery('<div>').addClass('ls-layer-preview-wrapper').appendTo('.ls-preview-wrapper');

		// Slide properties
		var sliderProps = window.lsSliderData.properties,
			slideProps 	= LS_activeSlideData.properties,
			postOffset 	= slideProps.post_offset;

		if(postOffset == -1) { postOffset = LS_activeSlideIndex; }
		var posts 	= window.lsPostsJSON || [];
		var post 	= posts[postOffset];

		// Slide attributes
		var properties = '', sKey, sVal;
		for( sKey in slideProps) {
			sVal = slideProps[ sKey ];

			// Don't allow empty values & force auto slide duration
			if( sVal !== '' && sVal !== 'null' && sKey !== 'slidedelay' ) {
				properties += sKey+':'+sVal+';';
			}
		}

		if( sliderProps.sliderclass ) {
			$wrapper.addClass( sliderProps.sliderclass );
		}

		// Add slide
		$s1 = jQuery('<div>').attr({
			'class': 'ls-slide',
			'data-ls': properties
		}).appendTo($wrapper);

		// Get layer data
		var item = LS_activeLayerDataSet[0],
			layerData = jQuery.extend(true, {}, item);
			layerData.transition.delayin = 100;


		LayerSlider.appendLivePreviewItem(0, layerData, $s1, post);

		item.skip = true;
		LS_previewItems[ LS_activeLayerIndexSet[0] ].addClass('ls-invisible');

		var sliderSize = LayerSlider.getSliderSize();

		// Initialize slider
		$wrapper.layerSlider({
			type: 'responsive',
			width: sliderSize.width,
			height: sliderSize.height,
			skin: 'v6',
			skinsPath: pluginPath + 'layerslider/skins/',
			pauseOnHover: false,
			autoPlayVideos: false,
			startInViewport: false,
			keybNav: false,
			navButtons: false,
			navStartStop: false,
			navPrevNext: false
		}).on('slideTimelineDidComplete', function( event, slider ) {
			if( jQuery('.ls-timeline-switch li').eq(0).hasClass('active') ) {
				slider.api('replay');
				return false;
			}
		});

	},


	stopLayerPreview: function(forceStop){

		if(this.isLayerPreviewActive) {

			// Change preview state
			this.isLayerPreviewActive = false;
			LayerSlider.showPreviewSelection();
			jQuery('.ls-layer-preview-button').removeClass('playing').text( LS_l10n.layer );

			jQuery.each(LS_activeLayerDataSet, function(index, item) {
				item.skip = false;
			});

			kmUI.popover.close();

			// Restore editing area
			// LS_activeLayerDataSet.skip = false;
			if( forceStop ) {
				LayerSlider.generateSelectedPreviewItems();
			}

			jQuery('.ls-layer-preview-wrapper').layerSlider( 'destroy', true );
			LS_previewArea.children().removeClass('ls-transparent');
		}
	},


	appendLivePreviewItem: function(layerKey, layerData, $slide, post) {

		// Skip sublayer?
		if( !!layerData.skip || layerData['hide_on_'+LS_activeScreenType] ) {
			return true;
		}

		// Gather sublayer data
		var type = layerData.type;
		switch( layerData.media ) {
			case 'img':
				type = 'img';
				break;

			case 'button':
			case 'icon':
				type = 'span';
				break;

			case 'html':
			case 'media':
				type = 'div';
				break;

			case 'post':
				type = 'post';
				break;
		}

		var image = layerData.image,
			html = layerData.html,
			style = layerData.style,
			top = layerData.styles.top,
			left = layerData.styles.left,
			skip = layerData.hasOwnProperty('skip'),
			url = layerData.url,
			id = layerData.id,
			classes = layerData['class'],

			innerAttrs = layerData.innerAttributes || {},
			outerAttrs = layerData.outerAttributes || {};

		// Transition properties
		var sublayerprops = '', trKey, trVal;
		for( trKey in layerData.transition) {

			trVal = layerData.transition[ trKey ];

			if( trKey.indexOf('perspective') !== -1 &&  trVal.toString() === '500') {
				continue;

			}

			if( trKey === 'backgroundvideo' && ! trVal ) {
				continue;
			}

			if( trVal !== '' && trVal !== null && trVal !== 'null' && trVal !== 'inherit' ) {
				sublayerprops += trKey+':'+trVal+';';
			}
		}


		// Styles
		var styles = {}, cssProp, cssVal;
		for( cssProp in layerData.styles ) {
			cssVal = layerData.styles[cssProp];

			if( ( ! cssVal && cssVal !== 0 ) || cssVal === 'unset' ) { continue; }
			cssVal = cssVal.toString();

			if(cssVal.slice(-1) == ';' ) {
				cssVal = cssVal.substring(0, cssVal.length - 1);
			}
			if (cssVal) { // !! fix for unused styles don't override Custom CSS
				styles[cssProp] = isNumber(cssVal) ? cssVal + 'px' : cssVal;

				if( ['z-index', 'font-weight', 'opacity'].indexOf( cssProp )  !== -1 ) {
					styles[cssProp] = cssVal;
				}
			}
		}

		// Background
		if( layerData.layerBackground ) {
			styles['background-image'] = 'url("'+layerData.layerBackground+'")';
		}

		// Build the sublayer
		var sublayer;
		if(type == 'img') {
			if(!image) { return true; }
			if(image == '[image-url]') { image = post['image-url']; }

			sublayer = jQuery('<img src="'+image+'" class="ls-l">').appendTo($slide);

		} else if(type == 'post') {

			// Parse post placeholders
			var textlength = layerData.post_text_length;
			for(var key in post) {
				if(html.indexOf('['+key+']') !== -1) {
					if( (key == 'title' || key == 'content' || key == 'excerpt') && textlength > 0) {
						post[key] = post[key].substr(0, textlength);
					}
					html = html.replace('['+key+']', post[key]);
				}
			}

			// Test html
			html = jQuery.trim(html);
			var first = html.substr(0, 1);
			var last = html.substr(html.length-1, 1);
			if(first == '<' && last == '>') {
				html = html.replace(/(\r\n|\n|\r)/gm,"");
				sublayer = jQuery(html).appendTo($slide).addClass('ls-l');
			} else {
				sublayer = jQuery('<div>').appendTo($slide).html(html).addClass('ls-l');
			}

		} else {
			sublayer = jQuery('<'+type+'>').appendTo($slide).html(html).addClass('ls-l');

			// Rewrite Youtube/Vimeo iframe src to data-src
			var $video = sublayer.find('iframe[src*="youtube-nocookie.com"], iframe[src*="youtube.com"], iframe[src*="youtu.be"], iframe[src*="player.vimeo"]');
			if( $video.length ) {
				$video.attr('data-src', $video.attr('src') ).removeAttr('src');
			}
		}

		// Apply styles
		sublayer
			.attr({ 'id': id, 'style': style })
			.css(styles)
			.css('white-space', !layerData.styles.wordwrap ? 'nowrap' : 'normal')
			.addClass(classes);

		// Apply attributes
		for( var iaKey in innerAttrs ) {
			if( iaKey.toLowerCase() === 'class' ) {
				sublayer.addClass( innerAttrs[iaKey] );
				continue;
			}

			sublayer[0].setAttribute(iaKey, innerAttrs[iaKey]);
		}

		// Position the element
		if(top.indexOf('%') != -1) { sublayer.css({ top : top });
			} else { sublayer.css({ top : parseInt(top) }); }

		if(left.indexOf('%') != -1) { sublayer.css({ left : left });
			} else { sublayer.css({ left : parseInt(left) }); }

		if( url ) {

			if( layerData.linkId ) {

				if( '#' === layerData.linkId.substr(0, 1) ) {
					url = layerData.linkId;

				} else if( '[post-url]' === layerData.linkId ) {
					url = post['post-url'];

				} else {
					url = '#';
				}
			}

			if( '[post-url]' === url ) {
				url = post['post-url'];
			}

			var linkNotification = '';
			if( '#' === url && layerData.linkId ) {
				linkNotification = ' data-help="'+LS_l10n.SBPreviewLinkNotAvailable.replace('%s', layerData.linkName)+'" data-help-delay="1"';
			}

			var anchor = jQuery('<a href="'+url+'"'+linkNotification+' target="_blank"></a>');
				anchor.attr( outerAttrs );

			sublayer.wrap( anchor );
		} else {

			// Apply attributes
			for( var oaKey in outerAttrs ) {
				if( oaKey.toLowerCase() === 'class' ) {
					sublayer.addClass( outerAttrs[oaKey] );
					continue;
				}

				sublayer[0].setAttribute(oaKey, outerAttrs[oaKey]);
			}
		}

		sublayer.attr('data-ls', sublayerprops);
	},


	updatePopupNotifications: function() {

		var $wrapper 	= jQuery('#ls-popup-notifications'),
			$layout 	= jQuery('.ls-popup-layout-notification', $wrapper),
			$trigger 	= jQuery('.ls-popup-trigger-notification', $wrapper),
			sliderProps = window.lsSliderData.properties,
			layoutCond 	= sliderProps.type !== 'popup',
			triggerCond = jQuery.trim(sliderProps.popupShowOnTimeout) || jQuery.trim(sliderProps.popupShowOnIdle) || jQuery.trim(sliderProps.popupShowOnScroll) || sliderProps.popupShowOnLeave || jQuery.trim(sliderProps.popupShowOnClick);

		$layout[ layoutCond ? 'removeClass' : 'addClass' ]('ls-hidden');
		$trigger[ ! triggerCond ? 'removeClass' : 'addClass' ]('ls-hidden');

		$wrapper.children(':not(.ls-hidden):first').removeClass('ls-hidden').siblings().addClass('ls-hidden');
	},


	updatePopupPositionGrid: function() {

		var vPos = window.lsSliderData.properties.popupPositionVertical,
			hPos = window.lsSliderData.properties.popupPositionHorizontal;

		jQuery('.ls-popup-position td[data-move="'+vPos+' '+hPos+'"]').click();
	},


	updatePopupPreview: function() {

		var fitWidth 	= window.lsSliderData.properties.popupFitWidth,
			fitHeight 	= window.lsSliderData.properties.popupFitHeight,
			vPos 		= window.lsSliderData.properties.popupPositionVertical,
			hPos 		= window.lsSliderData.properties.popupPositionHorizontal,
			$preview 	= jQuery('.ls-settings-popup .ls-popup-layout-preview .ls-popup-layout-inner');

			$preview.attr('class', 'ls-popup-layout-inner ls-popup-'+vPos+' ls-popup-'+hPos);

			if( fitWidth ) { $preview.addClass('ls-popup-fitwidth'); }
			if( fitHeight ) { $preview.addClass('ls-popup-fitheight'); }
	},


	updateLayerPreview: function() {

		var $slider = jQuery('.ls-real-time-preview .ls-container'),
			$layer 	= jQuery('.ls-layer', $slider);



		$slider.layerSlider( 'updateLayerData', $layer, 'scalein: 2; rotatein: 360; scaleout: 2; rotateout: 360; rotate: -45;' );
	},



	openTransitionGallery: function() {

		kmw.modal.open({
			content: '#tmpl-ls-transition-modal',
			minWidth: 980,
			maxWidth: 980,
			maxHeight: '100%',

			onBeforeClose: function() {

				if( window.lsHideTransition ) {
					window.lsHideTransition();
				}
			}
		});

		// Append transitions
		LayerSlider.appendTransition(0, '', '2d_transitions', layerSliderTransitions.t2d);
		LayerSlider.appendTransition(1, '', '3d_transitions', layerSliderTransitions.t3d);

		// Append custom transitions
		if(typeof layerSliderCustomTransitions != "undefined") {
			if(layerSliderCustomTransitions.t2d.length) {
				LayerSlider.appendTransition(2, '', 'custom_2d_transitions', layerSliderCustomTransitions.t2d);
			}
			if(layerSliderCustomTransitions.t3d.length) {
				LayerSlider.appendTransition(3, '', 'custom_3d_transitions', layerSliderCustomTransitions.t3d);
			}
		}

		jQuery('#ls-transition-window .ls-select-special-transition').each(function() {
			var $this 	= jQuery(this),
				name 	= $this.data('name');


			$this.addClass( LS_activeSlideData.properties[ name ] ? 'on' : 'off' );
		});

		// Select proper tab
		jQuery('#ls-transition-window .filters li.active').click();
	},


	appendTransition: function(index, title, tbodyclass, transitions) {

		// Append new section
		var section = jQuery( '#ls-transitions-list section:eq('+index+') div' ).empty();

		// Get checked transitions
		var checked = LS_activeSlideData.properties[tbodyclass];
			checked = checked ? checked.split(',') : [];

		if( transitions && transitions.length ) {
			for( c = 0; c < transitions.length; c++ ){
				var addClass = '';
				if(checked.indexOf(''+(c+1)+'') != -1 || checked == 'all') {
					addClass = 'added';
				}
				section.append( jQuery( '<div class="tr-item '+addClass+'"data-key="' + ( c + 1 ) + '"><span><i>' + ( c + 1 ) + '</i><i class="dashicons dashicons-yes"></i></span><span>' + transitions[c].name + '</span></div>' ) );
			}
		}
	},


	selectAllTransition: function(index, check) {

		// Get checkbox and transition type
		var checkbox = jQuery('#transitiongallery-header i:last'),
			type = jQuery('#ls-transitions-list section').eq(index).data('tr-type');

		if(check) {

			jQuery( '#ls-transitions-list section:eq('+index+')' ).find('.tr-item').addClass('added');
			checkbox.attr('class', 'on').text( LS_l10n.deselectAll );
			LS_activeSlideData.properties[ type ] = 'all';

		} else {

			jQuery( '#ls-transitions-list section:eq('+index+')' ).find('.tr-item').removeClass('added');
			checkbox.attr('class', 'off').text( LS_l10n.selectAll);
			LS_activeSlideData.properties[ type ] = '';
		}
	},

	toggleTransition: function(el) {

		var $item 		= jQuery(el),
			$section 	= $item.closest('section'),
			$trs 		= $section.find('.tr-item'),
			type 		= $section.data('tr-type');

		// Toggle addded class
		$item.toggleClass('added');

		// All selected
		if($trs.filter('.added').length == $trs.length) {

			LayerSlider.selectAllTransition( $section.index(), true );
			return;

		// Uncheck select all
		} else {

			// Check the checkbox
			jQuery('#transitiongallery-header i:last').attr('class', 'off').text( LS_l10n.selectAll );
		}

		// Gather checked selected transitions
		var checked = [];
		$trs.filter('.added').each(function() {
			checked.push( jQuery(this).data('key') );
		});

		// Set data
		LS_activeSlideData.properties[ type ] = checked.join(',');
	},


	save: function( saveProperties ) {

		saveProperties = saveProperties || {};

		// Bring all layers back in,
		// as it can mess with saving.
		this.stopLayerPreview(true);

		// Get the slider data
		var sliderData = jQuery.extend(true, {}, window.lsSliderData);

		// Temporary disable submit button
		jQuery('.ls-publish').addClass('saving').find('button').text( LS_l10n.saving ).attr('disabled', true);

		// Serialize slider settings to prevent jQuery form converting form data
		sliderData.properties = JSON.stringify(sliderData.properties);

		// 1. Iterate over the slides and encode them
		//    to workaround PHP's array size limitation.
		//
		// 2. Iterate over the styles object of layers
		//    to remove empty values added mistakenly.
		//
		// 3. Also check whether they use dynamic content.
		//
		// 4. Generate UUIDs on save for every layer for WPML
		//    and other purposes that requires a persistent ID.
		jQuery.each(sliderData.layers, function(slideIndex, slideData) {
			slideData.properties.post_content = false;
			jQuery.each(slideData.sublayers, function(layerIndex, layerData) {

				if( layerData.styles ) {
					jQuery.each(layerData.styles, function(cssIndex, cssVal) {
						if( cssVal === '' ) {
							delete layerData.styles[cssIndex];
						}
					});
				}

				layerData.transition 	= JSON.stringify(layerData.transition);
				layerData.styles 		= JSON.stringify(layerData.styles);

				if(slideData.properties.post_content === false && layerData.media == 'post') {
					slideData.properties.post_content = true;
				}

				var uuid = LS_DataSource.uuidForLayer( layerIndex, slideIndex);
				sliderData.layers[ slideIndex ].sublayers[ layerIndex ].uuid = uuid;
			});

			// Reverse the list of layers, as it is only
			// a visual thing on the admin UI.
			slideData.sublayers.reverse();
			sliderData.layers[slideIndex] = JSON.stringify(slideData);
		});


		// Save slider
		jQuery.ajax({
			type: 'POST', url: ajaxurl, dataType: 'text',
			data: {
				_wpnonce: jQuery('#ls-slider-form input[name="_wpnonce"]').val(),
				_wp_http_referer: jQuery('#ls-slider-form input[name="_wp_http_referer"]').val(),
				action: 'ls_save_slider',
				id: LS_sliderID,
				sliderData: sliderData
			},
			error: function(jqXHR, textStatus, errorThrown) {
				jQuery('.ls-publish').removeClass('saving').addClass('failed').find('button').text( LS_l10n.error );
				setTimeout(function() {
					alert( LS_l10n.SBSaveError.replace('%s', errorThrown ) );
				}, 100);
			},
			success: function(jqXHR, textStatus) {

				// Consider the editor as "clean", do not show
				// unsaved changes warning when leaving the page.
				LS_editorIsDirty = false;

				// Button feedback
				jQuery('.ls-publish').removeClass('saving').addClass('saved').find('button').text( LS_l10n.saved );

				// Display on screen notification when save
				// was initiated by a keyboard shortcut.
				if( saveProperties.usedShortcut && typeof lsScreenOptions !== 'undefined' && lsScreenOptions.useNotifyOSD === 'true' ) {
					kmUI.notify.show({
						icon: 'dashicons-yes',
						iconColor: '#7eb917',
						text: LS_l10n.notifySliderSaved
					});
				}
			},
			complete: function(data) {

				setTimeout(function() {
					jQuery('.ls-publish').removeClass('saved failed').find('button').text( LS_l10n.save ).attr('disabled', false);
					kmUI.notify.hide();
				}, 2000);
			}
		});
	},
};



var LS_InsertIcons = {

	timeout: null,

	init: function() {

		jQuery('#ls-layers').on('click', '.ls-insert-icon', function(e) {
			e.preventDefault();
			LS_InsertIcons.showIcons();
		});

		jQuery('#ls-layers').on('click', '.ls-replace-icon', function(e) {
			e.preventDefault();

			var $textarea = jQuery('.ls-sublayer-page textarea[name="html"]');
				$textarea.val('');

			LS_InsertIcons.showIcons();
		});

		jQuery(document).on('click', '#ls-insert-icons-modal-window section div', function(e) {
			e.preventDefault();
			LS_InsertIcons.insert( this );
		});

		jQuery(document).on('input change', '#ls-insert-icons-modal-window input', function(e) {
			e.preventDefault();
			LS_InsertIcons.search( jQuery(this).val() );
		});
	},


	showIcons: function() {

		kmw.modal.open({
			content: '#tmpl-insert-icons-modal',
			maxWidth: 930,
			minWidth: 400,
			maxHeight: '100%'
		});
	},


	search: function( term ) {

		// No search term.
		// Make sure to display everything.
		if( ! term || term.length < 2 ) {
			jQuery('#ls-insert-icons-modal-window section').show().prev().show();
			jQuery('#ls-insert-icons-modal-window section div').show();


		// Filter
		} else {

			clearTimeout( LS_InsertIcons.timeout );
			LS_InsertIcons.timeout = setTimeout(function() {
				jQuery('#ls-insert-icons-modal-window section').each(function() {
					var hasMatch = false;
					jQuery('div', this).each(function() {
						if( jQuery(this).data('help').indexOf( term ) !== -1 ) {
							hasMatch = true;
						} else {
							jQuery(this).hide();
						}
					});

					// Hide the section if there are no matches
					if( ! hasMatch ) {
						jQuery(this).hide().prev().hide();
					}
				});
			}, 200);
		}
	},


	insert: function( icon ) {

		var $icon 		= jQuery( icon ),
			text 		= '<i class="fa fa-'+$icon.data('help')+'"></i>',
			element 	= jQuery('.ls-sublayer-page textarea[name="html"]')[0];


		element.value += ' '+text;


		jQuery(element).trigger('input').trigger('change');

		LS_InsertIcons.close();
	},


	close: function() {

		setTimeout(function() {
			kmUI.popover.close();
		}, 500);

		kmw.modal.close();
	}

};


var LS_InsertMedia = {

	init: function() {

		jQuery('#ls-layers').on('click', '.ls-open-media-modal-button', function(e) {
			e.preventDefault();
			LS_InsertMedia.open();
		});

		jQuery(document).on('input', '#tmpl-insert-media-modal-window input, #tmpl-insert-media-modal-window textarea', function() {
			LS_InsertMedia.preview( jQuery( this ) );
		});

		jQuery(document).on('click', '#tmpl-insert-media-modal-window button.ls-html5-button', function(e) {
			e.preventDefault();

			kmw.modal.close( false, {
				onClose: function() {
					jQuery('.ls-sublayer-pages .ls-insert-media').click();
				}
			});
		});

		jQuery(document).on('click', '#tmpl-insert-media-modal-window button.ls-insert', function(e) {
			e.preventDefault();
			LS_InsertMedia.insert( jQuery( this ) );
		});
	},


	open: function() {

		kmw.modal.open({
			content: '#tmpl-insert-media-modal',
			minWidth: 980,
			maxWidth: 980
		});
	},


	parseURL: function() {


	},


	preview: function( $input ) {

		var $preview 	= jQuery('#tmpl-insert-media-modal-window .ls-media-preview'),
			inputVal 	= $input.val(),
			videoID,
			$media;

		// Empty field, reset preview
		if( '' === inputVal ) {
			$preview.html('');
			return;
		}

		// Insert from URL
		if( $input.is('input') ) {

			// Vimeo
			if( -1 !== inputVal.indexOf('vimeo') ) {
				videoID = LS_InsertMedia.parseVimeoURL( inputVal );

				if( videoID ) {
					$preview.html('<iframe src="https://player.vimeo.com/video/'+videoID+'" width="240" height="240" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>')
				}

			// YouTube
			} else if( -1 !== inputVal.indexOf('youtu') ) {
				videoID = LS_InsertMedia.parseYouTubeURL( inputVal );

				if( videoID ) {
					$preview.html('<iframe width="240" height="240" src="https://www.youtube.com/embed/'+videoID+'" frameborder="0" allowfullscreen></iframe>')
				}
			}



		// Embed code
		} else {

			try {
				$media = jQuery( inputVal );
				$media.attr({ width: 240, height: 240 });
				$preview.html( $media );

			} catch (e) {
				$preview.html('');
			}
		}
	},


	parseYouTubeURL: function( url ) {

		var matches = url.match(/^.*(?:(?:youtu\.be\/|v\/|vi\/|u\/\w\/|embed\/)|(?:(?:watch)?\?v(?:i)?=|\&v(?:i)?=))([^#\&\?]*).*/);

		if( matches && matches[1] ) {
			return matches[1];
		}

		return false;
	},


	parseVimeoURL: function( url ) {

		var matches = url.match(/^.*(vimeo\.com\/)((channels\/[A-z]+\/)|(groups\/[A-z]+\/videos\/))?([0-9]+)/);

		if( matches && matches[5] ) {
			return matches[5];
		}

		return false;
	},


	insert: function( $button ) {

		var $target 	= jQuery('.ls-sublayer-pages .ls-html-code textarea'),
			targetVal 	= $target.val(),
			mediaVal 	= $button.prev().val(),
			success 	= false;

		// Insert from URL
		if( $button.prev().is('input') ) {

			// Vimeo
			if( -1 !== mediaVal.indexOf('vimeo') ) {
				videoID = LS_InsertMedia.parseVimeoURL( mediaVal );

				if( videoID ) {
					success = true;
					$target.val('<iframe src="https://player.vimeo.com/video/'+videoID+'" width="640" height="360" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>')
				}

			// YouTube
			} else if( -1 !== mediaVal.indexOf('youtu') ) {
				videoID = LS_InsertMedia.parseYouTubeURL( mediaVal );

				if( videoID ) {
					success = true;
					$target.val('<iframe width="560" height="315" src="https://www.youtube.com/embed/'+videoID+'" frameborder="0" allowfullscreen></iframe>')
				}
			}

		// Embed code
		} else {
			success = true;
			$target.val( mediaVal );
		}

		if( success ) {

			// Set up undoManager action
			LS_UndoManager.add('layer.general', LS_l10n.SBUndoLayer, {
				itemIndex: LS_activeLayerIndexSet[0],
				undo: { html: targetVal },
				redo: { html: $target.val() }
			});

			// Save new value to DataStore
			LS_activeLayerDataSet[0].html = $target.val();

			// Generate preview and close modal
			LayerSlider.generatePreviewItem( LS_activeLayerIndexSet[0] );
			LS_InsertMedia.close();
		}
	},


	close: function() {
		kmw.modal.close();
	}
};



var LS_ButtonPresets = {

	init: function() {

		jQuery('#ls-layers').on('click', '.ls-choose-button-preset', function(e) {
			e.preventDefault();
			LS_ButtonPresets.open();
		});

		jQuery(document).on('click', '#tmpl-button-presets-modal-window li', function() {
			LS_ButtonPresets.apply( this );
		});
	},


	open: function() {

		kmw.modal.open({
			content: '#tmpl-button-presets',
			maxWidth: 980,
			minWidth: 980
		});
	},


	apply: function( li ) {

		var data 			= jQuery( li ).data('options'),
			layerStyles 	= LS_activeLayerDataSet[0].styles,
			undoObj 		= {},
			redoObj 		= {};


		data = typeof data === 'object' ? data : JSON.parse( data );
		data = jQuery.extend( true, {}, data );

		// Make sure to maintain layer position
		data.top 	= layerStyles.top;
		data.left 	= layerStyles.left;

		// Make sure to empty custom CSS
		LS_activeLayerDataSet[0].style = '';

		// Gather changes for UndoManager
		jQuery.each(data, function( prop, val ) {

			if( prop === 'html' ) {
				LS_activeLayerDataSet[0].html = val;
			} else if( prop === 'style' ) {
				LS_activeLayerDataSet[0].style = val;
			}

			if( layerStyles[ prop ] != val ) {
				undoObj[ prop ] = layerStyles[ prop ] || '';
				redoObj[ prop ] = val;
			}
		});

		// Apply preset data
		LS_activeLayerDataSet[0].styles = data;

		// Add to UndoManager
		LS_UndoManager.add('layer.style', LS_l10n.SBUndoLayerStyles, {
			itemIndex: LS_activeLayerIndexSet[0],
			undo: undoObj,
			redo: redoObj
		});

		LS_DataSource.buildLayer( LS_activeLayerIndexSet[0] );
		LayerSlider.generatePreviewItem( LS_activeLayerIndexSet[0] );

		LS_ButtonPresets.close();
	},


	close: function() {
		kmw.modal.close();
	}
};


var LS_ImportSlide = {

	init: function() {

		jQuery(document).on('click', '#ls-import-slide', function(e) {
			e.preventDefault();
			LS_ImportSlide.open( );
		});

		jQuery(document).on('click', '#tmpl-import-slide-modal-window .ls-import-layer-sliders .slider-item', function() {
			LS_ImportLayer.selectSlider( this );
		});

		jQuery(document).on('click', '#tmpl-import-slide-modal-window .ls-import-layer-slides .slider-item', function() {
			LS_ImportSlide.selectSlide( this );
		});
	},


	open: function() {

		kmw.modal.open({
			content: '#tmpl-import-slide',
			maxWidth: 980,
			minWidth: 980,
			maxHeight: '100%'
		});

		setTimeout(function() {
			kmUI.popover.close();
			LS_ImportLayer.loadSliders();
		}, 300);
	},


	selectSlide: function( item ) {

		var $item = jQuery(item);

		$item.addClass('added');

		LayerSlider.addSlide( $item.data('slide-data') );
	}
};



var LS_ImportLayer = {

	init: function() {

		jQuery(document).on('click', '#tmpl-import-layer-modal-window .ls-import-layer-sliders .slider-item', function() {
			LS_ImportLayer.selectSlider( this );
		});

		jQuery(document).on('click', '#tmpl-import-layer-modal-window .ls-import-layer-slides .slider-item', function() {
			LS_ImportLayer.selectSlide( this );
		});

		jQuery(document).on('click', '#tmpl-import-layer-modal-window .ls-import-layer-layers .layer-item', function() {
			LS_ImportLayer.selectLayer( this );
		});
	},


	open: function() {

		kmw.modal.open({
			content: '#tmpl-import-layer',
			minWidth: 980,
			maxWidth: 980,
			maxHeight: '100%'
		});

		setTimeout(function() {
			LS_ImportLayer.loadSliders();
		}, 300);
	},


	loadSliders: function() {

		jQuery.getJSON( ajaxurl, { action: 'ls_get_mce_sliders' }, function( data ) {

			var $target = jQuery('.ls-import-layer-sliders');

			if( ! data || ! data.length ) {
				$target.html(LS_l10n.SBImportLayerNoSlider);
				return;
			}

			$target.empty();

			jQuery.each(data, function(index, item) {

				var $item = jQuery('<div class="slider-item">\
						<div class="slider-item-wrapper">\
							<div class="preview">\
								<div class="no-preview">\
									<h5>'+LS_MCE_l10n.MCENoPreview+'</h5>\
								</div>\
							</div>\
							<div class="info">\
								<div class="name"></div>\
							</div>\
						</div>\
					</div>');

				$item.data({
					'id': item.id,
					'slug': item.slug
				});

				if( item.preview ) {
					jQuery('.preview', $item).empty().css({
						'background-image': 'url('+item.preview+')'
					});
				}

				jQuery('.name', $item).html( item.name );

				$item.appendTo( $target );
			});
		});
	},


	selectSlider: function( item ) {

		var $item = jQuery(item);

		$item.addClass('selected').siblings().removeClass('selected');

		jQuery('.ls-import-layer-layers').html( LS_l10n.SBImportLayerSelectSlide );

		LS_ImportLayer.loadSlides( $item.data('id') );
	},


	loadSlides: function( sliderID ) {

		jQuery.getJSON( ajaxurl, { action: 'ls_get_mce_slides', sliderID: sliderID }, function( data ) {

			var $target = jQuery('.ls-import-layer-slides');

			if( ! data || ! data.length ) {
				$target.html(LS_l10n.SBImportLayerNoSlide);
				return;
			}

			$target.empty();

			jQuery.each(data, function(index, item) {

				if( ! item || ! item.properties ) {
					return true;
				}

				var $item = jQuery('<div class="slider-item">\
						<div class="slider-item-wrapper">\
							<div class="preview">\
								<div class="no-preview">\
									<h5>'+LS_MCE_l10n.MCENoPreview+'</h5>\
								</div>\
							</div>\
							<div class="info">\
								<div class="name"></div>\
							</div>\
						</div>\
					</div>');

				$item.data('slide-data', item);

				if( item.properties.background || item.properties.thumbnail ) {
					var img = item.properties.thumbnail || item.properties.background;
					jQuery('.preview', $item).empty().css({
						'background-image': 'url('+img+')'
					});
				}

				jQuery('.name', $item).html( item.properties.title );

				$item.appendTo( $target );
			});
		});
	},


	selectSlide: function( item ) {

		var $item = jQuery(item);

		$item.addClass('selected').siblings().removeClass('selected');

		LS_ImportLayer.loadLayers( $item.data('slide-data') );
	},


	loadLayers: function( slideData ) {

		var $holder = jQuery('.ls-import-layer-layers');
		if( ! slideData || ! slideData.sublayers || ! slideData.sublayers.length ) {
			$holder.html(LS_l10n.SBImportLayerNoLayer);
			return;
		}

		$holder.html('<table><tbody></tbody></table>');

		var $target = jQuery('.ls-import-layer-layers table tbody');

		jQuery.each(slideData.sublayers, function(index, item) {

			var $item = jQuery('<tr class="layer-item">\
					<td class="preview">\
						<i class="dashicons"></i>\
					</td>\
					<td class="type"></td>\
					<td class="name">\
						<div>\
							<span></span>\
							<i class="dashicons dashicons-yes"></i>\
						</div>\
					</td>\
				</tr>');

			$item.data('layer-data', item);


			var mediaIcons = {
				img: 'dashicons-format-image',
				icon: 'dashicons-flag',
				text: 'dashicons-text',
				button: 'dashicons-marker',
				media: 'dashicons-video-alt3',
				html: 'dashicons-editor-code',
				post: 'dashicons-admin-post'
			};

			var mediaTypes = {
				img: LS_l10n.SBLayerTypeImg,
				icon: LS_l10n.SBLayerTypeIcon,
				text: LS_l10n.SBLayerTypeText,
				button: LS_l10n.SBLayerTypeButton,
				media: LS_l10n.SBLayerTypeMedia,
				html: LS_l10n.SBLayerTypeHTML,
				post: LS_l10n.SBLayerTypePost
			};

			jQuery('.preview .dashicons', $item).addClass( mediaIcons[ item.media ] );
			jQuery('.type', $item).html( mediaTypes[ item.media ] );
			jQuery('.name span', $item).html( item.subtitle );

			if( item.media === 'img' && item.image ) {
				jQuery('.preview', $item).html('<img src="'+item.image+'">');
			}

			$item.appendTo( $target );
		});
	},


	selectLayer: function( tr ) {

		$tr = jQuery( tr );

		// Highlight row
		$tr.addClass('added');

		// Add layer
		LayerSlider.addLayer( [ $tr.data('layer-data') ] );
	},


	close: function() {
		kmw.modal.close();
	}
};


var LS_PostOptions = {

	init: function() {

		jQuery('#ls-layers').on('click', '.ls-configure-posts', function(e) {
			e.preventDefault(); LS_PostOptions.open(this);
		});

		jQuery('#ls-post-options select:not(.ls-post-taxonomy, .post_offset)').change(function() {
			window.lsSliderData.properties[ jQuery(this).attr('name') ] = jQuery(this).val();
			LS_PostOptions.change(this);
		});

		jQuery('#ls-post-options select.offset').change(function() {
			LS_activeSlideData.properties.post_offset = jQuery(this).val();
			LayerSlider.willGeneratePreview();
		});

		jQuery('#ls-post-options select.ls-post-taxonomy').change(function() {
			window.lsSliderData.properties.post_taxonomy = jQuery(this).val();
			LS_PostOptions.getTaxonoies(this);
		});

		jQuery('#ls-layers').on('click', '.ls-post-placeholders li', function() {
			LS_PostOptions.insertPlaceholder(this);
		});
	},


	open: function(el) {

		kmw.modal.open({
			id: 'ls-post-options-window',
			content: jQuery('#ls-post-options'),
			minWidth: 880,
			maxWidth: 880
		});


		// Get slide's post offset
		var offset = parseInt(LS_activeSlideData.properties.post_offset) + 1;

		// Show modal window
		var modal = jQuery('#ls-post-options').show();
			modal.find('select.offset option').prop('selected', false).eq(offset).prop('selected', true);


		// First open?
		if(modal.find('.ls-post-previews ul').children().length === 0) {
			LS_PostOptions.change( modal.find('select')[0] );
		}
	},


	getTaxonoies: function(select) {

		var target = jQuery(select).next().empty();

		if(jQuery(select).val() == 0) {
			LS_PostOptions.change(select);

		} else {

			jQuery.post(ajaxurl, jQuery.param({ action : 'ls_get_taxonomies', taxonomy : jQuery(select).val() }), function(data) {
				data = jQuery.parseJSON(data);
				for(c = 0; c < data.length; c++) {
					target.append( jQuery('<option>', { 'value' : data[c].term_id, 'text' : data[c].name }));
				}
			});
		}
	},


	change: function(el) {

		// Get options
		var items = {};
		jQuery('#ls-post-options').find('select').each(function() {
			items[ jQuery(this).data('param') ] = jQuery(this).val();
		});

		jQuery.post(ajaxurl, jQuery.param({ action: 'ls_get_post_details', params : items }), function(data) {

			// Handle data
			var parsed = jQuery.parseJSON(data);
			window.lsPostsJSON = parsed;

			// Update preview
			LayerSlider.willGeneratePreview();
			LS_PostOptions.update(el, parsed );
		});
	},


	update: function(el, data) {

		var preview = jQuery('#ls-post-options').find('.ls-post-previews ul').empty();

		if(data.length === 0) {
			preview.append( jQuery('<li>')
				.append( jQuery('<h4>', { 'text' : LS_l10n.SBPostFilterWarning }) )
			);

		} else {
			for(c = 0; c < data.length; c++) {

				var content = data[c].content;

				if( content ) {
					content = content.replace(/<br>/g, '');
					content = content.replace(/<br \/>/g, '');
				}

				preview.append( jQuery('<li>')
					.append( jQuery('<span>', { 'class' : 'counter', 'text' : ''+(c+1)+'. ' }))
					.append( jQuery('<img>', { 'src' : data[c]['thumbnail-url'] } ))
					.append( jQuery('<h3>', { 'html' : data[c].title } ))
					.append( jQuery('<div>', { 'class': 'content', 'html' : content } ))
					.append( jQuery('<span>', { 'class' : 'author', 'text' : data[c]['date-published']+' by '+data[c].author } ))
				);
			}
		}
	},


	close: function() {
		kmw.modal.close();
	},


	insertPlaceholder: function(el) {

		var element = jQuery(el).closest('.ls-sublayer-page').find('textarea[name="html"]')[0];
		var text = (typeof jQuery(el).data('placeholder') != "undefined") ? jQuery(el).data('placeholder') : jQuery(el).children().text();

		if (document.selection) {
			element.focus();
			var sel = document.selection.createRange();
			sel.text = text;
			element.focus();
		} else if (element.selectionStart || element.selectionStart === 0) {
			var startPos = element.selectionStart;
			var endPos = element.selectionEnd;
			var scrollTop = element.scrollTop;
			element.value = element.value.substring(0, startPos) + text + element.value.substring(endPos, element.value.length);
			element.focus();
			element.selectionStart = startPos + text.length;
			element.selectionEnd = startPos + text.length;
			element.scrollTop = scrollTop;
		} else {
			element.value += text;
			element.focus();
		}

		jQuery(element).trigger('input').trigger('change');
	}
};




var LS_PostChooser = {

	timeout: null,
	data: null,
	opened: null,

	init: function() {

		jQuery('#ls-layers').on('click', '.ls-slide-link a.post', function(e) {
			e.preventDefault();

			LS_PostChooser.opener = this;
			LS_PostChooser.open();
		});

		jQuery(document).on('click', '#ls-post-chooser-modal-window li', function(e) {
			e.preventDefault();
			LS_PostChooser.select( jQuery(this) );
		});

		jQuery(document).on('keyup', '#ls-post-chooser-modal-window input', function(e) {
			LS_PostChooser.search();

		}).on('change', '#ls-post-chooser-modal-window select', function(e) {
			LS_PostChooser.search(1);

		}).on('submit', '#ls-post-chooser-modal-window form', function(e) {
			e.preventDefault();
			LS_PostChooser.search(1);
		});
	},

	open: function() {

		kmw.modal.open({
			content: '#tmpl-post-chooser',
			minWidth: 720,
			maxWidth: 930,
			maxHeight: '100%'
		});

		this.search();
	},

	search: function( timeout ) {

		timeout = timeout || 300;

		clearTimeout( LS_PostChooser.timeout );
		LS_PostChooser.timeout = setTimeout(function() {
			var $form = jQuery('#ls-post-chooser-modal-window form');
			jQuery.getJSON( ajaxurl, $form.serialize(), function( data ) {

				LS_PostChooser.data = data;

				jQuery('#ls-post-chooser-modal-window .ls-post-previews ul').empty();
				jQuery.each( data, function( index, item ) {

					jQuery('<li>\
						<img src="'+item['image-url']+'">\
						<h3>'+item.title+'</h3>\
						<div>'+item.content.substr(0, 200)+'</div>\
						<span class="author">'+item['date-published']+' by '+item.author+'</span>\
					</li>').appendTo('#ls-post-chooser-modal-window .ls-post-previews ul');
				});
			});
		}, timeout);
	},

	select: function( $li ) {

		var item 	= LS_PostChooser.data[ $li.index() ],
			l10nKey = 'SBLinkText'+ucFirst(item['post-type']),
			$holder = jQuery(LS_PostChooser.opener).closest('.ls-slide-link'),
			$input 	= jQuery('input.url', $holder);

		// Normalize HTML entities
		item.title = jQuery('<textarea>').html( item.title ).text();

		// Set link properties
		$input.val( LS_l10n[l10nKey].replace('%s', item.title) )
			.prop('disabled', true)
		.next()
			.val( item['post-id'] )
		.next()
			.val( item.title )
		.next()
			.val( item['post-type'] );

		// UndoManager action name
		var isSlide 	= $holder.closest('.ls-slide-options').length,
			linkData 	= isSlide ? LS_activeSlideData.properties : LS_activeLayerDataSet[0],
			undoText 	= isSlide ? LS_l10n.SBUndoSlide : LS_l10n.SBUndoLayer,
			undoArea 	= isSlide ? 'slide.general' : 'layer.general',
			undoIndex 	= isSlide ? LS_activeSlideIndex : LS_activeLayerIndexSet[0],
			urlField 	= isSlide ? 'layer_link' : 'url';

		// Add link change to UndoManager
		LS_UndoManager.add( undoArea, undoText, {
			itemIndex: undoIndex,
			undo: {
				[urlField]: linkData[urlField] || '',
				linkId: linkData.linkId || '',
				linkName: linkData.linkName || '',
				linkType: linkData.linkType || ''
			},
			redo: {
				[urlField]: LS_l10n[l10nKey].replace('%s', item.title),
				linkId: item['post-id'],
				linkName: item.title,
				linkType: item['post-type']
			}
		});

		// Set link placeholder & push data to datastore
		$holder.addClass('has-link').find('input').trigger( 'input' );

		kmw.modal.close();
	}
};



var LS_DataSource = {

	buildSlide: function() {

		var $slide = jQuery('#ls-layers .ls-layer-box');
		var $slideOptions = $slide.find('.ls-slide-options');

		// Reset checkboxes
		$slideOptions.find('.ls-checkbox').remove();
		$slideOptions.find('input:checkbox').prop('checked', false);

		// Get default slide options
		var defaults = LS_DataSource.getDefaultSlideData();

		// Loop through slide option form items
		var $formItems = jQuery($slideOptions.find('input,textarea,select'));
		LS_DataSource.setFormItemValues($formItems, LS_activeSlideData.properties, defaults);

		// Set checboxes and color picker
		$slideOptions.find('input:checkbox').customCheckbox();
		LayerSlider.addColorPicker( $slideOptions.find('.ls-colorpicker') );

		// Set image placeholders
		LS_GUI.updateImagePicker( 'background', LS_activeSlideData.properties.backgroundThumb );
		LS_GUI.updateImagePicker( 'thumbnail', LS_activeSlideData.properties.thumbnailThumb );

		LS_GUI.updateLinkPicker('layer_link');

		LayerSlider.updateCustomSlideProperties();

		this.buildLayersList();
	},


	buildLayersList: function( buildProperties ) {

		buildProperties = buildProperties || { updateLayer: true };

		// Get the layer list and empty it (if any)
		var $layersList = jQuery('#ls-layers .ls-sublayers').empty();

		// Build layers
		var numOfLayers = !LS_activeSlideData.sublayers ? 0 : LS_activeSlideData.sublayers.length;
		var $template = jQuery(jQuery('#ls-layer-item-template').html());

		for(var c = 0; c < numOfLayers; c++) {

			var layerData = LS_activeSlideData.sublayers[c];
			var $layer = $template.clone();
			$layer.find('.ls-sublayer-number').text(c+1);
			$layer.find('.ls-sublayer-title').val(layerData.subtitle);

			// Hidden layer
			if(layerData.skip) { $layer.find('.ls-icon-eye').addClass('disabled'); }

			// Locked layer
			if(layerData.locked) { $layer.find('.ls-icon-lock').removeClass('disabled'); }

			// Not visible on current screen type
			$layer[ layerData['hide_on_'+LS_activeScreenType] ? 'addClass' : 'removeClass' ]('dim');

			LayerSlider.setLayerMedia( layerData.media,  jQuery('.ls-sublayer-thumb', $layer), layerData );
			$layersList.append($layer);
		}


		// Reset static layers
		jQuery('.ls-layers-list .subheader').hide();
		jQuery('.ls-static-sublayers').empty();
		jQuery('.ls-sublayer-wrapper').removeClass('has-static-layers');

		// Add static layers (if any)
		if( LS_activeStaticLayersDataSet.length ) {

			jQuery('.ls-layers-list .subheader').show();
			jQuery('.ls-sublayer-wrapper').addClass('has-static-layers');

			$template = jQuery( jQuery('#ls-static-layer-item-template').html() );
			jQuery.each(LS_activeStaticLayersDataSet, function(idx, data) {

				var layerData = data.layerData,
					$layer = $template.clone();

					$layer.find('.ls-sublayer-number').text(idx+1);
					$layer.find('.ls-sublayer-title').text(layerData.subtitle);


				LayerSlider.setLayerMedia( layerData.media,  jQuery('.ls-sublayer-thumb', $layer), layerData );
				$layer.appendTo('.ls-static-sublayers');
			});
		}

		// Select first layer
		jQuery.each(LS_activeLayerIndexSet, function(index, layerIndex) {
			$layersList.children().eq( layerIndex ).addClass('active');
		});

		if( buildProperties.updateLayer ) {
			this.buildLayer();
		}
	},


	buildLayersListItem: function( layerIndex ) {

		var layerData 	= LS_activeSlideData.sublayers[ layerIndex ],
			$template 	= jQuery(jQuery('#ls-layer-item-template').html()),
			$target 	= jQuery('#ls-layers .ls-sublayers li').eq( layerIndex );
			$layer 		= $template.clone();


		$layer.find('.ls-sublayer-number').text(layerIndex+1);
		$layer.find('.ls-sublayer-title').val(layerData.subtitle);

		// Hidden layer
		if(layerData.skip) { $layer.find('.ls-icon-eye').addClass('disabled'); }

		// Locked layer
		if(layerData.locked) { $layer.find('.ls-icon-lock').removeClass('disabled'); }

		// Not visible on current screen type
		$layer[ layerData['hide_on_'+LS_activeScreenType] ? 'addClass' : 'removeClass' ]('dim');

		// Active?
		if( LS_activeLayerIndexSet[0] === $target.index() ) {
			$layer.addClass('active');
		}

		LayerSlider.setLayerMedia( layerData.media,  jQuery('.ls-sublayer-thumb', $layer), layerData );
		$target.replaceWith( $layer );
	},


	buildLayer: function() {

		// Bail out early if there's no layers on slide
		if( ! LS_activeLayerDataSet.length ||
			! LS_activeSlideData.sublayers.length) {
				return false;
		}

		// Find active layer
		var $layerItem 	= jQuery('#ls-layers .ls-sublayers li.active'),
			$layer 		= jQuery('.ls-sublayer-pages'),
			layerIndex 	= LS_activeLayerIndexSet[0],
			layerData 	= LS_activeLayerDataSet[0];

		// Empty earlier layers and add new
		jQuery('.ls-sublayer-pages').empty();
		jQuery('.ls-sublayer-pages').html( jQuery('#ls-layer-template').html() );


		// Reset checkboxes
		// $layer.find('.ls-checkbox').remove();
		// $layer.find('input:checkbox:not(.noreset)').prop('checked', false);

		var $formItems = jQuery('input,textarea,select', $layer).filter(':not(.auto,.sublayerprop)'),
			$styleItems = jQuery('input,textarea,select', $layer).filter('.auto'),
			$transitionItems = jQuery('input,textarea,select', $layer).filter('.sublayerprop');

		LS_DataSource.setFormItemValues($formItems, layerData);
		LS_DataSource.setFormItemValues($styleItems, layerData.styles);
		jQuery('.ls-border-padding input').each(function() {
			LayerSlider.updateLayerBorderPadding( this );
		});

		// Backwards compatibility: put transitions settings into
		// the 'transition' object within the layer data
		if( ! layerData.transition || jQuery.isEmptyObject(layerData.transition) ) {
			this.restoreOldTransitionSettings( $transitionItems );
		}

		LS_DataSource.setFormItemValues($transitionItems, layerData.transition);
		LayerSlider.updateLayerAttributes( layerData );

		LayerSlider.updateLayerInterfaceItems(layerIndex);

		// Set image placeholder
		LS_GUI.updateImagePicker('image', layerData.imageThumb );
		LS_GUI.updateImagePicker('poster', layerData.posterThumb );

		// Set link placeholder
		LS_GUI.updateLinkPicker('url');

		// Set static layer chooser
		LayerSlider.setupStaticLayersChooser( $layer.find('.ls-sublayer-options select[name="static"]')[0] );

		// Layer background notification:
		LayerSlider.updateLayerBackgroundNotification();


		// Init custom interface plugins
		$layer.find(':checkbox:not(.noreplace)').customCheckbox();
		LayerSlider.addColorPicker( $layer.find('.ls-colorpicker') );
		LayerSlider.changeLayerScreenType();
		LayerSlider.changeVideoType();

		jQuery('#ls-layer-transitions section .ls-h-button input').each(function() {
			var $input 		= jQuery(this),
				$section 	= $input.closest('section'),
				index 		= $section.index(),
				$target 	= jQuery('#ls-transition-selector-table td:not(.ls-padding)');

			if( $input.prop('checked') && layerData.transition[ $input.attr('name') ] !== false ) {
				$target.eq( index ).addClass('active');

			} else if( $input.prop('checked') ) {
				$input.prop('checked', false);
				$input.next().removeClass('on');
			}
		});
		jQuery('#ls-transition-selector-table td:not(.ls-padding)').eq(LS_activeLayerTransitionTab).click();
		LayerSlider.checkForOpeningTransition();

		// Select lastly viewed subpage
		LayerSlider.selectLayerPage(LS_activeLayerPageIndex);

		if( LS_activeLayerIndexSet.length > 1 ) {
			LayerSlider.startMultipleSelection();
		}
	},

	setFormItemValues: function($items, values, defaults) {

		// Bail out early if no value was specified
		if( ! $items || ! values || jQuery.isEmptyObject( values ) ) { return false; }

		// Iterate over items
		for(var itemIndex = 0; itemIndex < $items.length; itemIndex++) {

			var $item = jQuery($items[itemIndex]),
				value = values[ $item.attr('name') ];

			if( ! $item.attr('name') ) { continue; }

			if( ! value && value !== false ) {
				if( typeof defaults == 'undefined' ) {
					continue;
				}

				value = defaults[$item.attr('name')];
			}

			// Checkboxes
			if($item.is(':checkbox')) {
				$item.prop('checked', Boolean(value)).data('value', Boolean(value));

			// Input, textarea
			} else if($item.is('input,textarea')) {
				$item.val(value).data('value', value);

			// Select
			} else if($item.is('select')) {
				$item.children().prop('selected', false);
				$item.children('[value="'+value+'"]').prop('selected', true);
				$item.data('value', value);
			}
		}
	},


	readSliderSettings: function() {

		// Return previously stored data whenever it's possible
		if( !jQuery.isEmptyObject(LS_defaultSliderData) ) {
			return LS_defaultSliderData;
		}

		var settings = {};
		jQuery('.ls-slider-settings').find('input,textarea,select').each(function() {

			var item = jQuery(this),
				prop = item.attr('name'),
				 val = item.is(':checkbox') ? item.prop('checked') : item.val();

			if(prop && val !== false) { settings[ prop ] = val; }
		});

		return settings;
	},


	parseSliderSetting: function() {

		var settings = window.lsSliderData.properties,
			key,
			val;

		for( key in settings ) {

			switch( settings[key] ) {
				case 'on':
				case 'true':
					settings[key] = true;
					break;

				case 'off':
				case 'false':
					settings[key] = false;
					break;
			}
		}
	},


	getDefaultSlideData: function() {

		// Return previously stored data whenever it's possible
		if( ! jQuery.isEmptyObject(LS_defaultSlideData)) {
			return LS_defaultSlideData;
		}

		// Get slide template
		var $template = jQuery( jQuery('#ls-slide-template').text() );

		// Iterate over form items and add their values to LS_defaultSlideData
		jQuery('.ls-slide-options', $template).find('input, textarea, select').each(function() {

			var item = jQuery(this),
				prop = item.attr('name'),
				val  = item.is(':checkbox') ? item.prop('checked') : item.val();

			if( prop ) { LS_defaultSlideData[ prop ] = val; }
		});

		return LS_defaultSlideData;
	},


	getDefaultLayerData: function() {

		// Build the default data object if there's no
		// stored copy yet
		if( jQuery.isEmptyObject( LS_defaultLayerData ) ) {

			var $template 	= jQuery( jQuery('#ls-layer-template').text() ),
				$inputs 	= jQuery();

			// Transition and style options will be stored in a sub-object
			LS_defaultLayerData.transition = {};
			LS_defaultLayerData.styles = {};

			$template.each(function() {

				var $this = jQuery(this);

				if( $this.hasClass('ls-sublayer-options') ) {
					jQuery('section .toggle', this).filter(':checkbox:checked').each(function() {
						$inputs = $inputs.add( jQuery('input, textarea, select', jQuery(this).closest('section') ) );
					});
				} else {
					 $inputs =  $inputs.add( jQuery('input, textarea, select', $this) );
				}
			});

			// Iterate over form items and add their values to LS_defaultLayerData
			$inputs.each(function() {

				var item 	= jQuery(this),
					prop 	= item.attr('name'),
					val 	= item.is(':checkbox') ? item.prop('checked') : item.val();

				if( prop ) {

					if( item.hasClass('sublayerprop') ) {
						LS_defaultLayerData.transition[prop] = val;

					} else if( item.hasClass('auto') ) {
						if( val !== '' ) {
							LS_defaultLayerData.styles[prop] = val;
						}

					} else {
						LS_defaultLayerData[prop] = val;
					}
				}
			});
		}

		// Make sure to always override the layer title in the stored copy
		// to avoid name collisions and weird behaviors.
		var layerCount 	= LS_activeSlideData.sublayers ? LS_activeSlideData.sublayers.length : 0,
			layerName 	= LS_l10n.SBLayerTitle.replace('%d', layerCount+1);

		LS_defaultLayerData.subtitle = layerName;

		return LS_defaultLayerData;
	},


	uuidForSlide: function( slideIndex ) {

		slideIndex = slideIndex || LS_activeSlideIndex;
		return this.uuidForObject(
			window.lsSliderData.layers[slideIndex].properties
		);
	},


	slideForUUID: function( uuid ) {

		var slideIndex;

		jQuery.each(window.lsSliderData.layers, function(index, slide) {
			if( slide.properties.uuid && slide.properties.uuid == uuid ) {
				slideIndex = index;
				return false;
			}
		});

		return slideIndex;
	},


	uuidForLayer: function( layerIndex, slideIndex ) {

		if( typeof layerIndex === 'undefined' ) {
			layerIndex = LS_activeLayerIndexSet[0];
		}

		if( typeof slideIndex === 'undefined' ) {
			slideIndex = LS_activeSlideIndex;
		}


		var slideData = window.lsSliderData.layers[ slideIndex ];

		return this.uuidForObject( slideData.sublayers[ layerIndex ] );
	},


	layerForUUID: function( uuid ) {

	},


	uuidForObject: function( data ) {

		if( ! data.uuid || ! data.uuid.length ) {
			data.uuid = this.generateUUID();
		}

		return data.uuid;
	},


	generateUUID: function() {
		return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
			var r = Math.random()*16|0, v = c == 'x' ? r : (r&0x3|0x8);
			return v.toString(16);
		});
	},


	// Backwards compatibility: put transitions settings into
	// the 'transition' object within the layer data
	restoreOldTransitionSettings: function($inputs) {

		// Get transition option names
		var options = [];
		for(var i=0;typeof($inputs[i])!='undefined';options.push($inputs[i++].getAttribute('name')));

		jQuery.each(window.lsSliderData.layers, function(slideKey, slideData) {
			jQuery.each(slideData.sublayers, function(layerKey, layerData) {
				for(var l = 0; l < options.length; l++) {
					if(layerData[ options[l] ]) {
						layerData.transition[ options[l] ] = layerData[ options[l] ];
						delete layerData[ options[l] ];
					}
				}
			});
		});
	}
};


var initSliderBuilder = function() {

	// Collapse sidebar while editing
	if( typeof lsScreenOptions !== 'undefined' && lsScreenOptions.collapseSidebar === 'true' ) {
		jQuery('body').addClass('folded');
	}

	// Expand sidebar on hover
	var sidebarTimeout = null;
	jQuery('#adminmenumain').hover(
		function() {
			if( typeof lsScreenOptions !== 'undefined' && lsScreenOptions.expandSidebarOnHover === 'true' ) {
				sidebarTimeout = setTimeout(function() {
					jQuery('body').removeClass('folded').addClass('ls-add-fold');
				}, 100 );
			}
		},
		function() {

			if( typeof lsScreenOptions !== 'undefined' && lsScreenOptions.collapseSidebar === 'true' ) {
				clearTimeout( sidebarTimeout );
				setTimeout(function() {
					jQuery('body').addClass('folded');
				}, 120);
			}
		}
	);

	jQuery('.km-tabs').kmTabs();

	// Set the DB ID of currently editing slider
	if( ! LS_sliderID ) {
		LS_sliderID = jQuery('#ls-slider-form input[name="slider_id"]').val();
	}

	LS_previewArea 		= jQuery('#ls-preview-layers');
	LS_previewHolder 	= LS_previewArea.parent();
	LS_previewWrapper 	= LS_previewHolder.parent();


	// Set a small delay to prevent unintentional
	// dragging operations when a user clicks on
	// tabs or Preview items
	jQuery.ui.draggable.prototype.options.distance = 3;
	jQuery.ui.sortable.prototype.options.distance = 3;


	// Add default slide data to data source if it's a new slider
	if(window.lsSliderData.properties.new) {
		window.lsSliderData.properties = LS_DataSource.readSliderSettings();
		window.lsSliderData.properties.createdWith = jQuery('input[name="sliderVersion"]').val();
		window.lsSliderData.layers = [{
			properties: jQuery.extend(true, {}, LS_DataSource.getDefaultSlideData()),
			sublayers: []
		}];

	// Extend existing slider data with defaults,
	// so we can guarantee that new options added in
	// future updates will always be present in the
	// data source object.
	} else {
		window.lsSliderData.properties = jQuery.extend( {},
			LS_DataSource.readSliderSettings(),
			window.lsSliderData.properties
		);
	}

	// Set callbacks
	var callbacks = window.lsSliderData.callbacks;
	jQuery('.ls-callback-box textarea:not([readonly])').each(function() {

		var $textarea 	= jQuery(this),
			name 		= $textarea.attr('name'),
			useData 	= $textarea.data('event-data');

		if( callbacks && callbacks[name] ) {
			$textarea.val( LS_Utils.stripslashes( callbacks[name] ) );
		}
	});

	LS_GUI.updateImagePicker( 'yourlogo', 'useCurrent' );
	LS_GUI.updateImagePicker( 'backgroundimage', 'useCurrent' );
	LS_GUI.updateImagePicker( 'preview', 'useCurrent' );
	LayerSlider.selectSlide(LS_activeSlideIndex, { forceSelect: true });


	// URL rewrite after creating slider
	if( history.replaceState ) {
		if(document.location.href.indexOf('&showsettings=1') != -1) {
			var url = document.location.href.replace('&showsettings=1', '');
			history.replaceState(null, document.title, url);
		}
	}


	// Show "unsaved changes" warning
	jQuery( window ).on('beforeunload', function(e) {

		if( LS_editorIsDirty ) {
			var dialogText = LS_l10n.SBUnsavedChanges;
			e.returnValue = dialogText;
			return dialogText;
		}
	});


	// Main tab bar page select
	jQuery('#ls-main-nav-bar a:not(.unselectable)').click(function(e) {

		LayerSlider.selectMainTab( this );

		if( jQuery(this).hasClass('layers') ) {
			$ruler.trigger('resize.lsRuler');
			LayerSlider.generatePreview();
			LayerSlider.updatePreviewSelection();
		}
	});

	// Settings sidebar
	jQuery('ul.ls-settings-sidebar > li').click(function() {
		LayerSlider.selectSettingsTab(this);
	});

	// Deeplink Slider Settings
	if( document.location.hash ) { LS_GUI.deeplinkSection(); }
	jQuery(window).on('hashchange', function() {
		LS_GUI.deeplinkSection();
	});

	// Settings: checkboxes
	jQuery('.ls-settings :checkbox, .ls-layer-box :checkbox:not(.noreplace)').customCheckbox();


	// Settings: datepicker
	var datePickerInterval = 0,
		datepicker = jQuery('.ls-settings .ls-datepicker-input').datepicker({
		inline: true,
		classes: 'ls-datepicker',
		language: 'en',
		dateFormat: 'yyyy-mm-dd',
		timeFormat: 'hh:ii:00',
		todayButton: new Date(),
		clearButton: true,
		timepicker: true,
		keyboardNav: false,
		range: false,

		onSelect: function(formattedDate, date, inst) {
			inst.$el.prev().fadeOut(200);
			inst.$el.trigger('input');
		}

	}).on('input', function() {
		var $this 	= jQuery(this);
			val 	= jQuery.trim( $this.val() );

			$this.prev().fadeOut(200);

		clearTimeout(datePickerInterval);
		datePickerInterval = setTimeout(function() {

			if( val.length > 2 && ! val.match(/^\d{4}-\d{2}-\d{2}/) ) {
				jQuery.getJSON( ajaxurl, { action: 'ls_parse_date', date: val }, function(data) {
					if( ! data.errorCount && data.dateStr != '' ) {
						$this.prev().fadeIn(200).removeClass('error').children('span').text( data.dateStr );
					} else {
						$this.prev().fadeIn(200).addClass('error').children('span').text( LS_l10n.SBInvalidFormat );
					}
				});
			}
		}, 1000);

	}).each(function() {

		var $this 	= jQuery(this),
			key 	= jQuery(this).data('schedule-key');


		if( window.lsSliderData.properties[ key ] ) {
			var date = window.lsSliderData.properties[ key ] +''+ LS_Utils.jsTimeZoneID()
			startDate = new Date( date );

			$this.data('datepicker').selectDate( startDate );
			$this.trigger('keyup');
		}
	});


	// Settings: Popup Presets
	jQuery('.ls-settings-popup').on('click', '#tmpl-popup-presets-button', function(e) {
		e.preventDefault();

		kmw.modal.open({
			content: '#tmpl-popup-presets-window',
			minWidth: 930,
			maxWidth: 930
		});


	// Settings: Popup Include Pages
	}).on('click', '.ls-popup-include-all-pages', function() {
		var $switch 	= jQuery(this),
			$targets 	= jQuery('.ls-popup-include-pages span:not(:first-child), .ls-popup-include-custom-pages');

		if( $switch.hasClass('on') ) {
			$targets.removeClass('ls-hidden');
		} else {
			$targets.addClass('ls-hidden');
		}
	// Settings: Popup Preview
	}).on('click', '.ls-popup-preview-button', function(e) {
		e.preventDefault();
		LayerSlider.startPopupPreview({}, this);

	// Settings: Popup Position
	}).on('click', '.ls-popup-position td', function(e) {

		var $td 	= jQuery(this),
			$table 	= $td.closest('table'),
			moves 	= $td.data('move').split(' ');

		// Update UI
		$table.find('td').removeClass('active');
		$td.addClass('active');

		// Update settings
		jQuery('input[name="popupPositionVertical"]').val( moves[0] );
		jQuery('input[name="popupPositionHorizontal"]').val( moves[1] );
		window.lsSliderData.properties.popupPositionVertical = moves[0];
		window.lsSliderData.properties.popupPositionHorizontal = moves[1];

		// Update preview
		LayerSlider.updatePopupPreview();

	}).on('click', '.ls-popup-fit-width, .ls-popup-fit-height', function() {
		setTimeout(function() {
			LayerSlider.updatePopupPreview();
		}, 100);

	}).on('keyup change', '.ls-popup-triggers :input', function() {
		setTimeout(function() {
			LayerSlider.updatePopupNotifications();
		}, 100);
	});

	if( jQuery('.ls-popup-include-all-pages').hasClass('on') ) {
		jQuery('.ls-popup-include-pages span:not(:first-child), .ls-popup-include-custom-pages').addClass('ls-hidden');
	}

	LayerSlider.updatePopupPositionGrid();
	LayerSlider.updatePopupPreview();


	// Settings: Popup presets
	jQuery(document).on('click', '#ls-popup-presets-modal-window .ls-layout-illustration-grid', function() {
		var $item 		= jQuery(this),
			$options 	= jQuery('.ls-settings-popup :input'),
			data 		= $item.data('options');


		if( typeof data === 'string' ) {
			data = JSON.parse( data );
		}

		for( var key in data ) {
			window.lsSliderData.properties[ key ] = data[key];
			var $input = $options.filter('[name="'+key+'"]');

			// Handle checkboxes
			if( typeof data[key] === 'boolean' ) {
				if( data[key] != $input.prop('checked') ) {
					$input.next().click();
				}
			} else {
				$input.val( data[key] );
			}
		}

		// Update settings
		LayerSlider.updatePopupPositionGrid();
		LayerSlider.updatePopupPreview();

		// Close modal
		kmw.modal.close();
	});

	// Uploads
	LayerSlider.openMediaLibrary();
	kmComboBox.init();

	// Clear uploaded image button
	jQuery(document).on({
		mouseenter: function() {
			if( jQuery(this).hasClass('has-image') ) {
				jQuery(this).addClass('hover');
			}
		},
		mouseleave: function() {
			jQuery(this).removeClass('hover');
		}
	}, '.ls-image');


	jQuery(document).on('click', '.ls-image a.pixie', function(e) {

		e.preventDefault();
		e.stopPropagation();

		// Set ID on the currently editing image
		var $parent = jQuery(this).parent(),
			$image 	= $parent.find('img').attr('id', 'pixie-current-image'),
			imageURL;

		// Prevent popover to become visible after opening the editor
		jQuery('body').addClass('hidepopover');

		// Find image URL
		if( $parent.hasClass('ls-slide-image') ) {
			imageURL = LS_activeSlideData.properties.background;
		} else if( $parent.hasClass('ls-slide-thumbnail') ) {
			imageURL = LS_activeSlideData.properties.thumbnail;
		} else if( $parent.hasClass('ls-layer-image') ) {
			imageURL = LS_activeLayerDataSet[0].image;
		} else if( $parent.hasClass('ls-media-image') ) {
			imageURL = LS_activeLayerDataSet[0].poster;
		} else if( $parent.hasClass('ls-layer-background-image') ) {
			imageURL = LS_activeLayerDataSet[0].layerBackground;
		}

		// Open image editor
		LayerSlider.openImageEditor( LS_Utils.toAbsoluteURL( imageURL ) );
	});

	// Clear uploads
	jQuery(document).on('click', '.ls-image .dashicons-dismiss', function(e) {
		e.preventDefault();
		e.stopPropagation();

		var $parent = jQuery(this).parent();

		$parent.removeClass('hover');
		$parent.prev().val('').prev().val('');
		LS_GUI.updateImagePicker( $parent, '' );

		// Global background
		if( $parent.hasClass('ls-global-background') ) {
			window.lsSliderData.properties.backgroundimage = '';
			window.lsSliderData.properties.backgroundimageId = '';
			window.lsSliderData.properties.backgroundimageThumb = '';

		} else if( $parent.hasClass('ls-yourlogo-upload') ) {
			window.lsSliderData.properties.yourlogo = '';
			window.lsSliderData.properties.yourlogoId = '';
			window.lsSliderData.properties.yourlogoThumb = '';

		} else if( $parent.hasClass('ls-slider-preview') ) {

			window.lsSliderData.meta.preview = '';
			window.lsSliderData.meta.previewId = '';

		} else if( $parent.hasClass('ls-slide-image') ) {

			LS_UndoManager.add('slide.general', LS_l10n.SBUndoRemoveSlideImage, {
				itemIndex: LS_activeSlideIndex,
				undo: {
					background: LS_activeSlideData.properties.background,
					backgroundId: LS_activeSlideData.properties.backgroundId,
					backgroundThumb: LS_activeSlideData.properties.backgroundThumb
				},
				redo: {
					background: '',
					backgroundId: '',
					backgroundThumb: ''
				}
			});

			LS_activeSlideData.properties.background = '';
			LS_activeSlideData.properties.backgroundId = '';
			LS_activeSlideData.properties.backgroundThumb = '';

			LayerSlider.updateSlidePreviews();


		} else if( $parent.hasClass('ls-slide-thumbnail') ) {

			LS_activeSlideData.properties.thumbnail = '';
			LS_activeSlideData.properties.thumbnailId = '';
			LS_activeSlideData.properties.thumbnailThumb = '';

			LayerSlider.updateSlidePreviews();

		} else if( $parent.hasClass('ls-layer-image') ) {

			LS_UndoManager.add('layer.general', LS_l10n.SBUndoRemoveLayerImage, {
				itemIndex: LS_activeLayerIndexSet[0],
				undo: {
					image: LS_activeLayerDataSet[0].image,
					imageId: LS_activeLayerDataSet[0].imageId,
					imageThumb: LS_activeLayerDataSet[0].imageThumb
				},
				redo: {
					image: '',
					imageId: '',
					imageThumb: ''
				}
			});

			LS_activeLayerDataSet[0].image = '';
			LS_activeLayerDataSet[0].imageId = '';
			LS_activeLayerDataSet[0].imageThumb = '';
			jQuery('.ls-sublayers li').eq(LS_activeLayerIndexSet[0])
				.find('.ls-sublayer-thumb').addClass('dashicons dashicons-format-image')
				.find('img').remove();


		} else if( $parent.hasClass('ls-media-image') ) {

			LS_UndoManager.add('layer.general', LS_l10n.SBUndoRemoveVideoPoster, {
				itemIndex: LS_activeLayerIndexSet[0],
				undo: {
					poster: LS_activeLayerDataSet[0].poster,
					posterId: LS_activeLayerDataSet[0].posterId,
					posterThumb: LS_activeLayerDataSet[0].posterThumb
				},
				redo: {
					poster: '',
					posterId: '',
					posterThumb: ''
				}
			});

			LS_activeLayerDataSet[0].poster = '';
			LS_activeLayerDataSet[0].posterId = '';
			LS_activeLayerDataSet[0].posterThumb = '';

		} else if( $parent.hasClass('ls-layer-background-image') ) {

			LS_UndoManager.add('layer.general', LS_l10n.SBUndoRemoveLayerBG, {
				itemIndex: LS_activeLayerIndexSet[0],
				undo: {
					poster: LS_activeLayerDataSet[0].layerBackground,
					layerBackgroundId: LS_activeLayerDataSet[0].layerBackgroundId,
					layerBackgroundThumb: LS_activeLayerDataSet[0].layerBackgroundThumb
				},
				redo: {
					layerBackground: '',
					layerBackgroundId: '',
					layerBackgroundThumb: ''
				}
			});

			LS_activeLayerDataSet[0].layerBackground = '';
			LS_activeLayerDataSet[0].layerBackgroundId = '';
			LS_activeLayerDataSet[0].layerBackgroundThumb = '';

			LayerSlider.updateLayerBackgroundNotification();
		}


		LayerSlider.generatePreview();

	}).on('click', '.ls-timeline-switch li', function(e) {
		e.preventDefault();

		// Bail out early if it's the active menu item
		if( jQuery(this).hasClass('active') ) { return false; }

		var $item = jQuery(this),
			$layerList = jQuery('.ls-sublayers');

		// Toogle switch
		$item.addClass('active').siblings().removeClass('active');

		if( $item.index() == 1 ){
			jQuery('.ls-layers-table').hide().next().show();
			jQuery('.ls-add-sublayer').hide();

			LayerSlider.startSlidePreview({
				autoStart: false,
				pauseLayers: true,
				plugins: [{
					namespace: 'timeline',
					js: 'timeline/layerslider.timeline.js',
					css: 'timeline/layerslider.timeline.css',
					settings: {
						showLayersInfo: true
					}
				}]
			});


		} else {
			jQuery('.ls-layers-table').show().next().hide();
			jQuery('.ls-add-sublayer').show();

			LayerSlider.stopSlidePreview();
		}


	// Select layer type
	}).on('click', '.ls-layer-types li', function() {
		LayerSlider.addFormattedLayer( this );

	}).on('click', '.ls-context-add-layer li', function() {
		LayerSlider.addFormattedLayer( this, {
			styles: {
				top: LS_contextMenuTop / LS_previewZoom,
				left: LS_contextMenuLeft / LS_previewZoom
			}
		});


	}).on('click', '.ls-context-menu-duplicate', function() {
		LayerSlider.duplicateLayer();

	}).on('click', '.ls-context-menu-remove', function() {
		LayerSlider.removeLayer();

	}).on('click', '.ls-context-menu-hide', function() {


		LayerSlider.hideLayer();

	}).on('click', '.ls-context-menu-lock', function() {
		LayerSlider.lockLayer();

	}).on('click', '.ls-context-menu-copy-styles', function() {
		LayerSlider.copyLayerSettings( jQuery('.ls-sublayer-style .ls-h-actions .copy') );

	}).on('click', '.ls-context-menu-paste-styles', function() {
		LayerSlider.pasteLayerSettings( jQuery('.ls-sublayer-style .ls-h-actions .paste') );

	}).on('click', '.ls-context-menu-copy-layer', function() {
		LayerSlider.copyLayer( );

	}).on('click', '.ls-context-menu-paste-layer', function() {
		LayerSlider.pasteLayer( );
	});

	// Settings: store any form element change in  data source
	jQuery('.ls-slider-settings').on('input change click', 'input,textarea,select', function(event) {

		// Bail out early if there was a click event
		// fired on a non-checkbox form item
		if(event.type === 'click') {
			if( !jQuery(this).is(':checkbox') ) {
				return false;
			}
		}

		// Get option data
		var item = jQuery(this),
			prop = item.attr('name'),
			val  = item.is(':checkbox') ? item.prop('checked') : item.val();

		if( prop === 'width' || prop === 'height' ) {

			if( val && ! val.toString().match(/^\d+$/) ) {
				val = parseInt(val) || '';
				item.val( val );
			}
		}

		// Set new setting
		window.lsSliderData.properties[ prop ] = val;

		// Mark unsaved changes on page
		LS_editorIsDirty = true;

		// Update preview
		if(item.is('select, :checkbox')) {
			LayerSlider.generatePreview();
		} else {
			LayerSlider.willGeneratePreview();
		}
	});

	// Settings: init slider size chooser
	jQuery('.ls-slider-dimensions').on('click', 'div', function(e) {

		var $this 	= jQuery(this),
			type 	= $this.data('type');

		if( $this.hasClass('locked') ) {
			return;
		}

		$this.siblings('input[type="hidden"]').val( type );
		$this.addClass('active').siblings().removeClass('active');

		// Reset rows
		jQuery('.ls-settings-contents .ls-popup-hide').show();
		jQuery('.full-width-row, .full-size-row, .popup-row', jQuery('.ls-settings-contents')).hide();

		switch( type ) {
			case 'fullwidth':
				jQuery('.ls-settings-contents .full-width-row').css('display', 'table-row');
				break;

			case 'fullsize':
				jQuery('.ls-settings-contents .full-size-row').css('display', 'table-row');
				break;

			case 'popup':
				jQuery('.ls-settings-contents .ls-popup-hide').hide();
				jQuery('.ls-settings-contents .popup-row').css('display', 'table-row');
				break;
		}

		// Update data source & reload preview
		window.lsSliderData.properties.type = type;
		LayerSlider.updatePopupNotifications();
		LayerSlider.generatePreview();
	});

	// Select slider type
	jQuery('.ls-slider-dimensions div[data-type="'+window.lsSliderData.properties.type+'"]').click();

	// if ($dim.find('input[name=fullpage]:checked').length) {
	// 	$dim.find('.full-screen').click();
	// } else if ($dim.find('input[name=forceresponsive]:checked').length) {
	// 	$dim.find('.full-width').click();
	// } else if ($dim.find('input[name=responsive]:checked').length) {
	// 	$dim.find('.responsive').click();
	// } else {
	// 	$dim.find('.non-responsive').click();
	// }
	// Settings: update slider height for full-screen
	jQuery('#container-height').on('change', function() {
		jQuery('#slider-height').val(this.value).change();
	});

	// Settings: reset button
	jQuery(document).on('click', '.ls-reset', function() {

		// Empty field
		jQuery(this).prev().val('');

		// Generate preview
		LayerSlider.generatePreview();
	});


	// Callbacks: store any form element change in  data source
	jQuery('.ls-callback-page').on('updated.ls', 'textarea:not([readonly])', function( event, cm ) {

		if( typeof window.lsSliderData.callbacks !== 'object' ) {
			window.lsSliderData.callbacks = {};
		}


		var key 	= jQuery(this).attr('name'),
			val 	= jQuery(this).val(),
			test 	= val.match(/\{([\s\S]*)\}/m)[1].replace(/(\r\n|\n|\r)/gm, '');

		if( jQuery.trim( test ).length ) {
			window.lsSliderData.callbacks[ key ] = val;
		} else {
			delete window.lsSliderData.callbacks[ key ];
		}
	});


	// Add slide
	jQuery('#ls-add-slide').click(function(e) {
		e.preventDefault(); LayerSlider.addSlide();
	});

	// Select slide
	jQuery('#ls-slide-tabs').on('click', '.ls-slide-preview', function( ) {

		var $tab = jQuery(this).closest('.ls-slide-tab');

		if( ! $tab.hasClass('active') ) {
			LayerSlider.selectSlide( $tab.index(), { forceSelect: true } );
		}

	// Rename slide
	}).on('input', '.ls-slide-name input', function( ) {
		LayerSlider.renameSlide( this );

	// Show slide actions
	}).on('click', '.ls-slide-actions', function() {
		LayerSlider.showSlideActions( this );

	}).on('contextmenu', '.ls-slide-preview', function( e ) {

		if( document.location.href.indexOf('section=revisions') === -1 ) {
			e.preventDefault();
			LayerSlider.showSlideActions( this );
		}

	// Hide slide actions
	}).on('mouseleave', '.ls-slide-actions-sheet', function() {
		LayerSlider.hideSlideActions( );

	// Duplicate slide
	}).on('click', '.ls-slide-duplicate', function() {
		LayerSlider.hideSlideActions();
		LayerSlider.duplicateSlide( this );

	// Remove slide
	}).on('click', '.ls-slide-remove', function() {
		LayerSlider.hideSlideActions();
		LayerSlider.removeSlide( this );

	}).on('click', '.ls-slide-visibility', function() {
		LayerSlider.hideSlideActions();
		LayerSlider.toggleSlideVisibility( this );
	});

	// Custom Slide Properties
	jQuery('#ls-layers').on('keyup', '.ls-custom-slide-properties tr:last-child input', function() {

	if( jQuery(this).val() ) {
		var $tr = jQuery(this).closest('tr').removeClass('ls-hidden');
		$tr.clone().insertAfter( $tr ).find('input').val('');
	}
	// Custom Slide Properties
	}).on('keyup', '.ls-custom-slide-properties tr:not(:last-child) input', function( event ) {
		LayerSlider.setCustomSlideProperties(event, this);
	});

	// Open popout editor
	jQuery( document ).on( 'click', '#ls-open-popout-editor', function( e ){

		e.preventDefault();

		jQuery('body').addClass('ls-popout-editor-open');

		var $popoutEditor 	= jQuery( '#ls-layers-settings-popout' ),
			$parent 		= jQuery( '.ls-sublayer-wrapper' );


		$popoutEditor.removeClass( 'ls-layers-settings-normal' ).addClass( 'ls-layers-settings-floating' ).draggable({
			handle: '#ls-layers-settings-popout-handler',
			containment: '#ls-slider-form',
			scroll: false
		}).resizable({
			minHeight: 450,
			minWidth: 350,
			maxWidth: 1500,
			create: function(){
				kmUI.smartResize.set();
			},
			resize: function(){
				kmUI.smartResize.set();
			}
		});

		// Position Popout editor to the center of the screen
		// if the "remember last location" flag is not present.
		if( ! $popoutEditor.data( 'km-remember-position' ) ) {

			var editorWidth 	= $popoutEditor.width(),
				editorHeight 	= $popoutEditor.height(),
				parentWidth 	= $parent.width(),
				windowScrollTop = jQuery( window ).scrollTop(),
				windowHeight 	= jQuery( window ).height(),
				offsetTop 		= $parent.offset().top;

			$popoutEditor.data( 'km-remember-position', true ).css({
				top: (windowScrollTop - offsetTop) + ( ( windowHeight - editorHeight ) / 2 ),
				left: ( parentWidth / 2 ) - ( editorWidth / 2)
			});
		}

		jQuery( '#ls-layers-settings-popout-handler' ).trigger( 'mouseenter' );
		jQuery( '.ls-preview-wrapper' ).addClass( 'ls-forceto-left' );

	// Close popout editor
	}).on( 'click', '#ls-close-popout-editor,#ls-popout-desc button', function( e ){

		e.preventDefault();

		jQuery('body').removeClass('ls-popout-editor-open');

		var $popoutEditor = jQuery( '#ls-layers-settings-popout' );

		// User clicked on the "Restore Editor" button,
		// so we should reset position and other data.
		if( jQuery( e.target ).is('#ls-popout-desc button') ) {
			$popoutEditor.removeData( 'km-remember-position' );
		}

		$popoutEditor
			.addClass( 'ls-layers-settings-normal' )
			.removeClass( 'ls-layers-settings-floating' )
			.draggable( 'destroy' )
			.resizable( 'destroy' );

		jQuery( '.ls-preview-wrapper' ).removeClass( 'ls-forceto-left' );

		kmUI.smartResize.set();
	});

	// Enter URL
	jQuery('#ls-layers').on('click', '.ls-url-prompt', function(e){
		e.preventDefault();

		var url = prompt( LS_l10n.SBEnterImageURL );
		if( ! url ) { return false; }

		var $el 	= jQuery(this),
			$target = $el.parent().prev();

		// Slide image
		if($target.hasClass('ls-slide-image')) {
			LS_activeSlideData.properties.background = url;
			LS_activeSlideData.properties.backgroundId = '';
			LS_activeSlideData.properties.backgroundThumb = url;

		// Slide thumbnail
		} else if($target.hasClass('ls-slide-thumbnail')) {
			LS_activeSlideData.properties.thumbnail = url;
			LS_activeSlideData.properties.thumbnailId = '';
			LS_activeSlideData.properties.thumbnailThumb = url;

		// Image layer
		} else if($target.hasClass('ls-layer-image')) {
			LS_activeLayerDataSet[0].image = url;
			LS_activeLayerDataSet[0].imageId = '';
			LS_activeLayerDataSet[0].imageThumb = url;

		// Media image
		} else if($el.hasClass('ls-media-prompt')) {
			$target = jQuery('.ls-image.ls-media-image');
			LS_activeLayerDataSet[0].poster = url;
			LS_activeLayerDataSet[0].posterId = '';
			LS_activeLayerDataSet[0].posterThumb = url;

		// Layer background
		} else if($target.hasClass('ls-layer-background-image')) {
			LS_activeLayerDataSet[0].layerBackground = url;
			LS_activeLayerDataSet[0].layerBackgroundId = '';
			LS_activeLayerDataSet[0].layerBackgroundThumb = url;

			LayerSlider.updateLayerBackgroundNotification();
		}

		LS_GUI.updateImagePicker( $target, url );
		LayerSlider.generatePreview();

	}).on('click', '.ls-capture-slide', function( event ) {
		event.preventDefault();

		if( typeof lsScreenOptions !== 'undefined' && lsScreenOptions.useNotifyOSD === 'true' ) {

			kmUI.notify.show({
				icon: 'dashicons-camera',
				iconColor: '#f9a241',
				text: LS_l10n.notifyCaptureSlide
			});
		}

		// Hide preview selection and live slide UI elements
		LayerSlider.hidePreviewSelection();
		jQuery('.ls-gui-element:visible').addClass('ls-hidden');

		// html2canvas freezes the browser completely,
		// not even animations will occur. Using setTimeout
		// to first render user feedback, then start the capture.
		setTimeout(function() {

			var $target = LayerSlider.isSlidePreviewActive ? jQuery('.ls-real-time-preview') : LS_previewHolder;

			html2canvas( $target[0], {
				// backgroundColor: null,
				scale: 1

			}).then(function( canvas ) {

				// Restore preview selection and slider UI elements
				LayerSlider.showPreviewSelection();
				jQuery('.ls-gui-element.ls-hidden').removeClass('ls-hidden');

				var imgName = 'ls-slider-'+LS_sliderID+'-slide-'+(LS_activeSlideIndex+1)+'.png',
					imgData = canvas.toDataURL( 'image/jpeg', 0.92 ),
					imgBlob = LS_Utils.dataURItoBlob( imgData, 'image/jpeg' );
					imgBlob.lastModifiedDate = new Date();
					imgBlob.name = imgName;
					imgBlob.filename = imgName;

				LayerSlider.uploadImageToMediaLibrary(imgBlob, function(data) {

					// Save to data store
					LS_activeSlideData.properties.thumbnail = data.url;
					LS_activeSlideData.properties.thumbnailId = data.id;
					LS_activeSlideData.properties.thumbnailThumb = data.url;

					// Update UI
					LS_GUI.updateImagePicker( 'thumbnail',  data.url );
					LayerSlider.updateSlidePreviews();

					kmUI.notify.hide();
				});
			});

		}, 1000);
	});



	// Slide options: input, textarea, select
	jQuery('#ls-layers').on('input change click', '.ls-slide-options input, .ls-slide-options textarea, .ls-slide-options select', function(event) {

		// Bail out early if there was a click event
		// fired on a non-checkbox form item
		if(event.type === 'click') {
			if( !jQuery(this).is(':checkbox') ) {
				return false;
			}
		}

		var item = jQuery(this),
			prop = item.attr('name'),
			val  = item.is(':checkbox') ? item.prop('checked') : item.val();

		LS_activeSlideData.properties[prop] = val;

		// Update preview when setting properties
		// related to the background image
		var updateKeys = [
			'bgsize', 'bgposition', 'bgcolor'
		];

		if( updateKeys.indexOf(prop) !== -1 ) {
			LayerSlider.generatePreview();
		}
	});

	// Open Transition gallery
	jQuery('#ls-layers').on('click', '.ls-select-transitions', function(e) {
		e.preventDefault();
		LayerSlider.openTransitionGallery();
	});

	// Origami banner
	jQuery(document).on('click', '#tryorigami', function() {
		jQuery('#transitionmenu li:last').click();

	// Enable/disable special effects
	}).on('click', '#ls-transition-window .ls-select-special-transition', function(e) {

		var $item = jQuery(this),
			checked;

		if( $item.hasClass('locked') ) {
			return true;
		}

		// Turn off
		if( $item.hasClass('on') ) {
			$item.removeClass('on').addClass('off');
			checked = false;

		// Turn on
		} else {
			$item.removeClass('off').addClass('on');
			checked = true;
		}

		LS_activeSlideData.properties[ $item.data('name') ] = checked;

	// Add/Remove layer transitions
	}).on('click', '#ls-transitions-list section .tr-item', function(e) {
		e.preventDefault();
		LayerSlider.toggleTransition(this);

	// Select/Deselect all transitions
	}).on('click', '#transitiongallery-header i:last', function(e) {
		var check = jQuery(this).hasClass('off') ? true : false;
		jQuery('#ls-transitions-list section.active').each(function() {
			LayerSlider.selectAllTransition( jQuery(this).index(), check );
		});

	// Apply on others
	}).on('click', '#transitiongallery-header i:not(:last)', function(e) {

		// Confirmation
		if( ! confirm( LS_l10n.SBTransitionApplyOthers ) ) {
			return false;
		}

		// Dim color briefly
		var button = jQuery(this);
		button.css('opacity', '.5');
		setTimeout(function() {
			button.css('opacity', '1');
		}, 2000);

		// Apply to other slides
		jQuery.each(window.lsSliderData.layers, function(slideIndex, slideData) {
			slideData.properties['3d_transitions'] 		= LS_activeSlideData.properties['3d_transitions'];
			slideData.properties['2d_transitions'] 		= LS_activeSlideData.properties['2d_transitions'];
			slideData.properties.custom_3d_transitions 	= LS_activeSlideData.properties.custom_3d_transitions;
			slideData.properties.custom_2d_transitions 	= LS_activeSlideData.properties.custom_2d_transitions;
			slideData.properties.transitionorigami 		= LS_activeSlideData.properties.transitionorigami;
		});

	}).on('click', '#ls-more-slide-options', function() {
		LayerSlider.toggleAdvancedSlideOptions( this );

	// Show/Hide transition
	}).on('mouseenter', '#ls-transitions-list section .tr-item', function() {
		lsShowTransition( this );

	}).on('mouseleave', '#ls-transitions-list section .tr-item', function() {
		lsHideTransition( this );
	});

	// Add slide
	jQuery('#ls-layers').on('click', '.ls-add-sublayer', function(e) {
		e.preventDefault();

		// Show pointer and append overlay
		jQuery('body').off('click.ls-layer-types');
		jQuery('.ls-empty-layer-notification').addClass('ls-hidden');
		jQuery('.ls-layer-types-wrapper').show();
		TweenLite.to( jQuery('.ls-layer-types'), 0.3, {
			y: 0
		});

		setTimeout(function() {
			jQuery('body').one('click.ls-layer-types', function() {
				jQuery('.ls-empty-layer-notification').removeClass('ls-hidden');
				TweenLite.to( jQuery('.ls-layer-types'), 0.3, {
					y: -330,
					onComplete: function() {
						jQuery('.ls-layer-types-wrapper').hide();
					}
				});
			});
		}, 200);


	// Select layer
	}).on('click', '.ls-sublayers li', function( event ) {

		// Range Select
		if( event.shiftKey && LS_activeLayerDataSet.length === 1 ) {

			var val1 	= LS_lastSelectedLayerIndex || LS_activeLayerIndexSet[0],
				val2 	= jQuery(this).index(),

				start 	= Math.min(val1, val2),
				end 	= Math.max(val1, val2),

				indexes = [];

			for(var i = start; i <= end; i++) {
				indexes.push(i);
			}

			LayerSlider.selectLayer( indexes );

		// Manual select
		} else {
			LayerSlider.selectPreviewItem( jQuery(this).index(), event );
		}


	}).on('keyup', 'input[name="subtitle"]', function() {
		var index = jQuery(this).closest('li').index();
		LS_activeSlideData.sublayers[index].subtitle = jQuery(this).val();

	// Layer pages
	}).on('click', '.ls-sublayer-nav a', function(e) {
		e.preventDefault(); LayerSlider.selectLayerPage( jQuery(this).index() );

	// Remove layer
	}).on('click', '.ls-sublayers a.remove', function(e) {
		e.preventDefault(); e.stopPropagation();
		LayerSlider.removeLayer( jQuery(this).closest('li').index() );

	// Duplicate layer
	}).on('click', '.ls-sublayers a.duplicate', function(e) {
		e.preventDefault(); e.stopPropagation();
		LayerSlider.duplicateLayer();

	}).on('click', '.ls-set-screen-types button', function(e) {
		e.preventDefault();
		LayerSlider.changeLayerScreenType( jQuery(this), true );

	// Layer media type
	}).on('click', '.ls-layer-kind li:not(:first-child)', function(e) {
		e.preventDefault();
		var $item = jQuery(this);

		if( ! $item.hasClass('active') ) {

			LS_UndoManager.add('layer.general', LS_l10n.SBUndoLayerMedia, {
				itemIndex: LS_activeLayerIndexSet[0],
				undo: { media: LS_activeLayerDataSet[0].media },
				redo: { media: $item.data('section') }
			});

			LayerSlider.selectMediaType(this);
			LayerSlider.generatePreviewItem( LS_activeLayerIndexSet[0] );

			jQuery('.ls-layer-kind').removeClass('hover');
		}

	// Change layer media type
	}).on('click', '.ls-layer-kind li:first-child', function(e) {
		e.preventDefault();

		jQuery(this).closest('.ls-layer-kind').addClass('opened');
		setTimeout(function() {
			jQuery('html').one('click', function() {
				jQuery('.ls-layer-kind').removeClass('opened');
			});
		}, 100);


	}).on('mouseenter', '.ls-layer-kind', function() {
		jQuery(this).addClass('hover');

	// Layer element type
	}).on('click', '.ls-sublayer-element > li', function(e) {
		e.preventDefault();
		var $item = jQuery(this);

		LS_UndoManager.add('layer.general', LS_l10n.SBUndoLayerType, {
			itemIndex: LS_activeLayerIndexSet[0],
			undo: { type: LS_activeLayerDataSet[0].type },
			redo: { type: $item.data('element') }
		});

		LayerSlider.selectElementType(this);
		LayerSlider.generatePreviewItem( LS_activeLayerIndexSet[0] );

	// Layer options: input, textarea, select
	}).on('input change click', '.ls-sublayer-pages input, .ls-sublayer-pages textarea, .ls-sublayer-pages select', function(event) {

		// Ignore events triggered by UndoManager
		if(event.UndoManagerAction) { return false; }

		// Bail out early if there was a click event
		// fired on a non-checkbox form item
		if(event.type === 'click' && ! jQuery(this).is(':checkbox')) {
			return false;
		}

		// Prevent triggering the change event
		// on non-select form items
		if(event.type === 'change' && ! jQuery(this).is('select')) {
			return false;
		}

		var $item 	= jQuery(this),
			defVal 	= $item.attr('data-value'),
			prop 	= $item.attr('name'),
			val  	= $item.is(':checkbox') ? $item.prop('checked') : $item.val(),
			posVal = (prop == 'top' || prop == 'left') ? true : false;

		// Boolean conversion
		if( val === 'true' ) { val = true; }
		if( val === 'false' ) { val = false; }

		jQuery.each(LS_activeLayerDataSet, function(index, layerData) {

			var layerIndex 	= LS_activeLayerIndexSet[ index ],
				area 		= layerData;

			if( $item.hasClass('sublayerprop') ) { area = area.transition; }
				else if( $item.hasClass('auto') ) { area = area.styles; }

			// Null values indicate empty option.
			// We should remove them entirely from data source.
			if( ! posVal  && ( val === null || val === 'null' || val === '' ) ) {
				delete area[ prop ];

			// Exclude saving style properties where the default value
			// is the same that the user provides.
			} else if( ! posVal && ( $item.hasClass('auto') && typeof defVal !== 'undefined' && defVal.toString() === val.toString() ) ) {
				delete area[ prop ];

			} else {
				area[ prop ] = val;
			}


			LayerSlider.generatePreviewItem( layerIndex );
		});



		if( $item.closest('.ls-sublayer-style').length ) {
			LayerSlider.updatePreviewSelection();
		}

		if( LS_activeLayerDataSet.length === 1 ) {

			// Check if media embed code contains autoplay setting
			if( prop === 'html' &&  LS_activeLayerDataSet[0].media === 'media' ) {
				LayerSlider.checkMediaAutoPlay( $item, prop, val );
			}

			// Restart layer preview
			if( LayerSlider.isLayerPreviewActive  ){
				LayerSlider.startLayerPreview( jQuery('.ls-layer-preview-button') );
			}
		}

		// startAt
		var $li = $item.closest('.start-at-wrapper'),
			$ul = $li.parent();

		if( $li.length && ! $item.is('.start-at-calc') ) {

			var timing 	= jQuery('.start-at-timing', $ul).val(),
				operator 	= jQuery('.start-at-operator', $ul).val(),
				value 		= jQuery('.start-at-value', $ul).val(),
				$calcInput 	= jQuery('.start-at-calc', $ul);

			$calcInput.data('prevVal', $calcInput.val() );
			setTimeout(function() {
				$calcInput.val( timing +' '+ operator +' '+ value).trigger('input');
				LS_UndoManager.trackInputs( null, $calcInput );
			}, 100);
		}

	}).on('change', '.ls-sublayer-basic input.bgvideo', function( event ) {
		LayerSlider.changeVideoType(event );

	}).on('input', '.ls-sublayer-style textarea.style', function() {
		LayerSlider.validateCustomCSS( jQuery(this) );

	// Active transition sections
	}).on('click', '#ls-transition-selector-table td:not(.ls-padding)', function(event) {
		LayerSlider.selectTransitionPage( this );

	}).on('change', '#ls-transition-selector', function(event) {
		jQuery( '#ls-transition-selector-table td:not(.ls-padding)' ).eq( jQuery(this).val() ).click();

	}).on('change', '#ls-layer-transitions .ls-h-button input', function(event) {
		LayerSlider.enableTransitionPage( this );

	}).on('click', '#ls-layer-transitions .overlay', function(event) {
		var $this 		= jQuery(this),
			$section 	= $this.closest('section'),
			$checkbox 	= $section.find('.ls-h-button .ls-checkbox.toggle');

			if( $checkbox.hasClass('off') ) {

				if( $checkbox.data( 'tl' ) ){
					$checkbox.data( 'tl' ).progress(1).kill();
				}

				var tl = new TimelineMax();

				tl.to( $checkbox[0], 0.12, {
					yoyo: true,
					repeat: 3,
					ease: Quad.easeInOut,
					scale: 1.5,
					backgroundColor: '#ff1d1d'
				});

				$checkbox.data( 'tl', tl );
			}

	// Copy transition settings
	}).on('click', '.ls-h-actions .copy', function(event) {
		event.preventDefault();
		event.stopPropagation();
		LayerSlider.copyLayerSettings(this);

	// Paste transition settings
	}).on('click', '.ls-h-actions .paste', function(event) {
		event.preventDefault();
		event.stopPropagation();
		LayerSlider.pasteLayerSettings(this);
		jQuery('.ls-border-padding input').each(function() {
			LayerSlider.updateLayerBorderPadding( this );
		});

	// Static select
	}).on('mouseenter', '.ls-sublayer-options select[name="static"]', function() {
		LayerSlider.setupStaticLayersChooser( this );

	}).on('keyup', '.ls-sublayer-custom-attributes tr:last-child input', function() {

		if( jQuery(this).val() ) {
			var $tr = jQuery(this).closest('tr').removeClass('ls-hidden');
			$tr.clone().insertAfter( $tr ).find('input').val('');
		}

	}).on('keyup change', '.ls-sublayer-custom-attributes tr:not(:last-child) input', function( event ) {
		LayerSlider.setLayerAttributes(event, this);

	}).on('input', '.ls-border-padding input', function() {
		LayerSlider.updateLayerBorderPadding( this );




	// Pick static layer
	}).on('change', '.ls-sublayer-options select[name="static"]', function() {

		var $select = jQuery(this),
			value 	= $select.val(),
			index 	= parseInt( jQuery(this).val() ),
			uuid;

		if( value === 'null' || value === null || index === 0  ) {
			delete LS_activeLayerDataSet[0].transition.static;
			delete LS_activeLayerDataSet[0].transition.staticUUID;
			return;
		}

		if(index && index > 0) {
			uuid = LS_DataSource.uuidForSlide( index - 1 );
			LS_activeLayerDataSet[0].transition.staticUUID = uuid;
		}



	// Pick transformOrigin
	}).on('click', '#ls-layer-transitions .dashicons-search:not(.active)', function(event) {
		event.stopPropagation();

		var $this = jQuery(this).addClass('active'),
			$origin = $this.next(),
			$picker = jQuery('<div>').addClass('ls-origin-picker').appendTo('.ls-preview-wrapper');

		$picker.on('click', function(e) {

			var o = $picker.offset();
				x = e.pageX - o.left,
				y = e.pageY - o.top,
				$layer = LS_previewItems[ LS_activeLayerIndexSet[0] ],
				p = $layer.position(),
				ox = (x - p.left) / ( $layer.outerWidth() * LS_previewZoom ),
				oy = (y - p.top) / ( $layer.outerHeight() * LS_previewZoom ),

			$origin.val([
				Math.round(ox * 1000) / 10 + '%',
				Math.round(oy * 1000) / 10 + '%',
				$origin.val().split(/\s+/)[2] || ''
			].join(' ').trim());

			$origin.trigger('input');
		});

		jQuery(document).one('click', function() {
			jQuery('.ls-origin-picker').remove();
			jQuery('.dashicons-search.active').removeClass('active');
		});

		var origin = $origin.attr('name');
		jQuery.each(LS_previewItems, function(i, $sl) {

			var layerTransition = LS_activeSlideData.sublayers[i].transition;

			if( layerTransition && layerTransition[origin] ) {

				var o = layerTransition[origin].split(/\s+/),
					$layer = LS_previewItems[ LS_activeLayerIndexSet[0] ];

				if( o.length > 1 ) {

					var x = o[0] == 'left' ? '0' : (o[0] == 'right' ? '100%' : o[0]),
						y = o[1] == 'top' ? '0' : (o[1] == 'bottom' ? '100%' : o[1]),
						p = $sl.position();

					x = x.indexOf('%') < 0 ? parseInt(x) : parseFloat(x) / 100 * $sl.outerWidth();
					y = y.indexOf('%') < 0 ? parseInt(y) : parseFloat(y) / 100 * $sl.outerHeight();

					if ( ! isNaN( x ) && ! isNaN( y ) ) {
						jQuery('<div>')
							.addClass('ls-origin-point' + ($sl.is( $layer ) ? ' ls-origin-active' : ''))
							.css({
								left: p.left + (x * LS_previewZoom),
								top: p.top + (y * LS_previewZoom)
							}).appendTo($picker);
					}
				}
			}
		});
	});


	// Sublayer: sortables, draggable, etc
	LayerSlider.addSlideSortables();
	LayerSlider.addLayerSortables();
	LayerSlider.addDraggable();


	// Slide(r) Preview
	jQuery('#ls-layers').on('click', '.ls-preview-button', function(e) {
		e.preventDefault();
		LayerSlider.startSlidePreview();
	});

	// Animate Layer
	jQuery('#ls-layers').on('click', '.ls-layer-preview-button', function(e) {
		e.preventDefault(); LayerSlider.startLayerPreview(this, true);
	});


	// List intersecting preview items when right clicking on them
	LS_previewWrapper.on('contextmenu',function(e) {
		e.preventDefault(); LayerSlider.contextMenu(e);
	});

	// Don't drag locked layers
	LS_previewArea.on('dragstart', '.disabled,.transformed', function(e) {
		e.preventDefault();

	}).on('dblclick', '> *:not(.disabled)', function() {
		LayerSlider.editLayerStart( jQuery(this) );


	}).on('keydown', '.ls-editing', function( event ) {
		LayerSlider.editLayer( event );


	}).on('keyup', '.ls-editing', function() {
		LayerSlider.editLayerUpdate(this);


	}).on('paste', '.ls-editing', function( event ) {
		LayerSlider.editLayerPaste( event );

	});

	jQuery('.ls-real-time-preview').on('click', 'a[href="#"]', function( event ) {
		event.preventDefault();
	});

	// Highlight preview item when hovering the intersecting layers list
	jQuery(document).on({
		mouseenter: function() { LayerSlider.highlightPreviewItem(this); },
		mouseleave: function() { LS_previewArea.children().removeClass('highlighted lowlighted'); },
		}, '.ls-context-overlapping-layers li'
	);

	// Select layer from intersecting layers list
	jQuery(document).on('click', '.ls-context-overlapping-layers li', function(event) {
		var layerIndex = jQuery(this).data('layerIndex');
		LayerSlider.selectPreviewItem( layerIndex, event );
	});


	// Highlight dropable zone
	jQuery(document).on('dragover.ls', '.ls-preview-wrapper', function(e) {
		e.preventDefault();
		jQuery(this).addClass('ls-dragover');
	}).on('dragleave.ls drop.ls', '.ls-preview-wrapper', function(e) {
		e.preventDefault();
		jQuery(this).removeClass('ls-dragover');
	});

	// Handle dropped images
	jQuery('#ls-pages').on('drop.ls', '.ls-preview', function(event) {
		LayerSlider.handleDroppedImages(event);
	});


	// Handle alignment buttons
	jQuery(document).on('click', '#ls-layer-alignment td, .ls-context-menu-align li', function(event) {

		var $selection 		= jQuery('.ui-selected-helper'),
			moves 			= jQuery(this).data('move').split(' '),
			selTop 			= $selection.position().top,
			selLeft 		= $selection.position().left,
			selWidth 		= $selection.width(),
			selHeight 		= $selection.height(),
			areaWidth 		= LS_previewArea.width() * LS_previewZoom,
			areaHeight 		= LS_previewArea.height() * LS_previewZoom,
			updateInfo 		= [],
			isHorizontal 	= false,
			isVertical 		= false,
			diffTop, diffLeft, x, xp, y, yp;

			// Reposition, calc diff
			for(var c = 0; c < moves.length; c++) {
				switch(moves[c]) {
					case 'left': 	x = 0; xp = '0%'; isHorizontal = true; break;
					case 'center': 	x = areaWidth / 2 - selWidth / 2; xp = '50%'; isHorizontal = true; break;
					case 'right': 	x = areaWidth - selWidth; xp = '100%'; isHorizontal = true; break;

					case 'top': 	y = 0; yp = '0%'; isVertical = true; break;
					case 'middle': 	y = areaHeight / 2 - selHeight / 2; yp = '50%'; isVertical = true; break;
					case 'bottom': 	y = areaHeight - selHeight; yp = '100%'; isVertical = true; break;
				}
			}

		diffTop 	= selTop  - y;
		diffLeft 	= selLeft - x;


		jQuery.each(LS_activeLayerIndexSet, function(idx, layerIndex) {

			// Get layer data
			var layerData = LS_activeSlideData.sublayers[layerIndex],
				undoObj = {},
				redoObj = {};

			// Bail out early if it's a locked layer
			if( layerData.locked ) { return false; }

			// Get preview item, current position & direction
			var $previewItem = LS_previewItems[layerIndex],
				position = $previewItem.position(),
				left = Math.round( (position.left - diffLeft) / LS_previewZoom ).toString(),
				top = Math.round( (position.top - diffTop) / LS_previewZoom ).toString();

			// Use percents when only one layer is selected
			if( LS_activeLayerIndexSet.length === 1 ) {
				left = xp;
				top = yp;
			}

			// Set horizontal values
			if( isHorizontal ) {
				undoObj.left = layerData.styles.left;
				redoObj.left = left;
				layerData.styles.left = left;
				jQuery('.ls-sublayer-pages input[name=left]').val(left);
			}

			// Set vertical values
			if( isVertical ) {
				undoObj.top = layerData.styles.top;
				redoObj.top = top;
				layerData.styles.top = top;
				jQuery('.ls-sublayer-pages input[name=top]').val(top);
			}

			// Maintain history
			updateInfo.push({
				itemIndex: layerIndex,
				undo: undoObj,
				redo: redoObj
			});

			LayerSlider.generatePreviewItem(layerIndex);
		});

		// Maintain history
		LayerSlider.updatePreviewSelection();
		LS_UndoManager.add('layer.style', LS_l10n.SBUndoAlignLayer, updateInfo);

	}).on('click', '.ls-editor-layouts button', function(e) {
		e.preventDefault();

		LS_activeScreenType = jQuery(this).data('type');
		jQuery(this).addClass('playing').siblings().removeClass('playing');

		LS_DataSource.buildLayersList();

		if( LayerSlider.isLayerPreviewActive ) {
			LayerSlider.stopLayerPreview( true );
		}

		if( LayerSlider.isSlidePreviewActive ) {
			LayerSlider.stopSlidePreview();
			LayerSlider.startSlidePreview();
		}

		LayerSlider.generatePreview();
	});



	// GLOBAL SHORTCUTS
	var keyTimeout = null, oldX = {}, oldY = {},
		slidesItem = jQuery('#ls-main-nav-bar .layers');

	jQuery(document).on('keydown', function(e) {

		if( typeof lsScreenOptions !== 'undefined' && lsScreenOptions.useKeyboardShortcuts === 'false' ) {
			return;
		}

		if( document.location.href.indexOf('ls-revisions') !== -1 ) {
			return;
		}

		// Save slider when pressing Ctrl/Cmd + S
		if( (e.metaKey || e.ctrlKey) && e.which == 83 ) {
			if( ! e.altKey ) {
				e.preventDefault();
				LayerSlider.save({ usedShortcut: true });
				return;
			}
		}

		// Disable keyboard shortcuts while the
		// main builder interface is not visible.
		if( ! slidesItem.length || ! slidesItem.hasClass('active') ) {
			return true;
		}

		// Disable keyboard shortcuts while editing
		// images in the Pixie Image Editor
		if( jQuery('#pixie-current-image').length ) {
			return true;
		}

		var $target = jQuery(e.target);

		if(e.which == 13) {

			// Blur input fields when pressing enter
			if($target.is(':input:not(textarea)')) {
				e.preventDefault();
				e.target.blur();
				return;

			// Toggle layer editing
			} else if( !$target.is(':input') && !e.metaKey && !e.ctrlKey && !e.altKey ) {
				e.preventDefault();
				LayerSlider.editLayerToggle();
				return;
			}
		}

		// Disable keyboard shortcuts while editing
		// a layer with the 'contenteditable' attribute.
		if( jQuery('.ls-editing').length ) {
			return;
		}


		// Toggle layer preview with Shift/Alt/Ctrl + space bar
		if( (e.shiftKey || e.altKey || e.ctrlKey) && e.which == 32 && !jQuery(e.target).is(':input') ) {
			e.preventDefault();
			return jQuery('.ls-layer-preview-button').click();
		}


		// Toggle slide preview with the space bar
		if(e.which == 32 && !jQuery(e.target).is(':input')) {
			e.preventDefault();
			return jQuery('.ls-preview-button').click();
		}

		// Disable the other keyboard shortcuts while in preview mode
		if( LayerSlider.isSlidePreviewActive || LayerSlider.isLayerPreviewActive ) {
			return;
		}


		// Redo on Ctrl/Cmd + Shift + Z
		// or Ctrl/Cmd + Y
		if( ((e.metaKey || e.ctrlKey) && e.shiftKey && e.which == 90) ||
			((e.metaKey || e.ctrlKey) && e.which == 89) ) {
			if( !jQuery(e.target).is(':input') ) {
				e.preventDefault();
				return LS_UndoManager.redo();
			}
		}


		// Undo on Ctrl/Cmd + Z
		if( (e.metaKey || e.ctrlKey) && e.which == 90 ) {
			if( !jQuery(e.target).is(':input') ) {
				e.preventDefault();
				return LS_UndoManager.undo();
			}
		}


		// Remove selected layer when pressing del/backspace
		if(e.which == 8 || e.which == 46) {
			if( !jQuery(e.target).is(':input') ) {
				e.preventDefault();
				LayerSlider.removeLayer();
				return;
			}
		}

		// Duplicate layer when pressing Ctrl/Cmd + D
		if( (e.metaKey || e.ctrlKey) && e.which == 68 ) {
			e.preventDefault();
			LayerSlider.duplicateLayer();
			return;
		}

		// Cut layer when pressing Ctrl/Cmd + X
		if( (e.metaKey || e.ctrlKey) && e.which == 88 ) {
			if( !jQuery(e.target).is(':input') ) {
				e.preventDefault();
				LayerSlider.copyLayer(true, LS_activeLayerDataSet, LS_activeLayerIndexSet, { shiftLayers: false });
				LayerSlider.removeLayer(null, { requireConfirmation: false });
				return;
			}
		}

		// Copy layer when pressing Ctrl/Cmd + C
		if( (e.metaKey || e.ctrlKey) && e.which == 67 ) {

			// Copy only if there's no text selection
			if( ! document.getSelection().toString() ) {
				if( ! jQuery(e.target).is(':input') ) {
					e.preventDefault();
					LayerSlider.copyLayer(true);
					return;
				}

			// Remove selection after copying text on page,
			// so future copy events on layers will be uninterrupted.
			} else {
				setTimeout(function() {
					LS_Utils.removeTextSelection();
				}, 300);
			}
		}

		// Paste layer when pressing Ctrl/Cmd + V
		if( (e.metaKey || e.ctrlKey) && e.which == 86 ) {
			if( !jQuery(e.target).is(':input') ) {
				e.preventDefault();
				LayerSlider.pasteLayer();
				return;
			}
		}


		// Move layers in preview with arrow buttons
		if( [37,38,39,40].indexOf(e.which) !== -1 ) {
			if( ! jQuery(e.target).is(':input') ) {
				e.preventDefault();

				var updateInfo = [];

				jQuery.each(LS_activeLayerIndexSet, function(idx, layerIndex) {
					var layerData 	= LS_activeSlideData.sublayers[layerIndex],
						previewItem = LS_previewItems[layerIndex];

					if( ! layerData ) { return true; }
					if(layerData.locked ) { return true; }

					var x = Math.round( parseInt(layerData.styles.left) ),
						y = Math.round( parseInt(layerData.styles.top) );

					if( layerData.styles.left.indexOf('%') !== -1 || layerData.styles.top.indexOf('%') !== -1 ) {
						var positions = LayerSlider.setPositions(previewItem, layerData.styles.top, layerData.styles.left, true);
						x = positions.left;
						y = positions.top;
					}

					if( ! oldX[layerIndex] ) { oldX[layerIndex] = x; }
					if( ! oldY[layerIndex] ) { oldY[layerIndex] = y; }

					var left = 0, top = 0;
					switch(e.which) {
						case 37: left--; break; // left
						case 38: top--;  break; // up
						case 39: left++;  break; // right
						case 40: top++;  break; // down
					}

					// Move horizontally
					if(left) {
						e.preventDefault();
						x += (e.shiftKey || e.altKey) ? left*10 : left;

						layerData.styles.left = x+'px';
						previewItem.css('left', x+'px');
						jQuery('.ls-sublayer-pages input[name=left]').val(x + 'px');
					}

					// Move vertically
					if(top) {
						e.preventDefault();
						y += (e.shiftKey || e.altKey) ? top*10 : top;

						layerData.styles.top = y+'px';
						previewItem.css('top', y+'px');
						jQuery('.ls-sublayer-pages input[name=top]').val(y + 'px');
					}

					updateInfo.push({
						itemIndex: layerIndex,
						undo: { left: oldX[layerIndex]+'px', top: oldY[layerIndex]+'px'},
						redo: { left: x+'px', top: y+'px'},
					});
				});

				clearTimeout(keyTimeout);
				keyTimeout = setTimeout(function() {
					LS_UndoManager.add('layer.style', LS_l10n.SBUndoLayerPosition, updateInfo.reverse());
					oldX = {}; oldY = {};
				}, 1000);

				LayerSlider.updatePreviewSelection();
			}
		}
	});


	// Save changes
	jQuery('#ls-slider-form').submit(function(e) {
		e.preventDefault();
		LayerSlider.save(this);
	});

	// Add color picker
	LayerSlider.addColorPicker( jQuery('.ls-slider-settings input.ls-colorpicker') );


	// Show color picker on focus
	jQuery('.color').focus(function() {
		jQuery(this).next().slideDown();

	// Hide color picker on blur
	}).blur(function() {
		jQuery(this).next().slideUp();
	});

	// Jump to original layer
	jQuery('.ls-static-sublayers').on('click', '.ls-icon-jump', function(e) {
		e.preventDefault();
		e.stopPropagation();
		LayerSlider.revealStaticLayer( this );
	});


	// Eye icon for layers
	jQuery('.ls-sublayers').on('click', '.ls-icon-eye', function(e) {
		e.stopPropagation();
		LayerSlider.hideLayer(this);


	// Lock icon for layers
	}).on('click', '.ls-icon-lock', function(e) {
		e.stopPropagation();
		LayerSlider.lockLayer(this);


	// Collapse layer before sorting
	}).on('mousedown', '.ls-sublayer-sortable-handle', function(){
		jQuery(this).closest('.ls-sublayers').addClass('dragging');


	// Expand layer after sorting
	}).on('mouseup', '.ls-sublayer-sortable-handle', function(){
		jQuery('#ls-layers .ls-layer-box.active .ls-sublayer-sortable').removeClass('dragging');
	});

	LS_PostOptions.init();
	LS_PostChooser.init();
	LS_InsertIcons.init();
	LS_InsertMedia.init();
	LS_ButtonPresets.init();
	LS_ImportSlide.init();
	LS_ImportLayer.init();

	// Transitions gallery
	jQuery(document).on('click', '#transitionmenu ul li', function() {

		// Update navigation
		jQuery(this).siblings().removeClass('active');
		jQuery(this).addClass('active');

		// Update view
		jQuery('#ls-transitions-list section').removeClass('active');
		jQuery('#ls-transitions-list section').eq( jQuery(this).index() ).addClass('active');

		// Show the select all / deselect all button
		jQuery('#transitionmenu i:last-child').show();

		// Custom transitions
		if(jQuery(this).index() == 2) {
			jQuery('#ls-transitions-list section').eq(3).addClass('active');

		// Special effects
		} else if(jQuery(this).index() == 3) {
			jQuery('#ls-transitions-list section').eq(3).removeClass('active');
			jQuery('#ls-transitions-list section').eq(4).addClass('active');
			jQuery('#transitionmenu i:last-child').hide();
		}

		// Update 'Select all' button
		var trs = jQuery('#ls-transitions-list section.active').find('.tr-item');

		if(trs.filter('.added').length == trs.length) {
			jQuery('#transitiongallery-header i:last').attr('class', 'on').text( LS_l10n.deselectAll );
		} else {
			jQuery('#transitiongallery-header i:last').attr('class', 'off').text( LS_l10n.selectAll );
		}
	});

	// Link slide to post url
	jQuery('#ls-layers').on('click', '.ls-slide-link a.dyn', function(e) {
		e.preventDefault();

		var $holder = jQuery(this).closest('.ls-slide-link'),
			$input 	= jQuery('input.url', $holder);

		// UndoManager action name
		var isSlide 	= $holder.closest('.ls-slide-options').length,
			linkData 	= isSlide ? LS_activeSlideData.properties : LS_activeLayerDataSet[0],
			undoText 	= isSlide ? LS_l10n.SBUndoSlide : LS_l10n.SBUndoLayer,
			undoArea 	= isSlide ? 'slide.general' : 'layer.general',
			undoIndex 	= isSlide ? LS_activeSlideIndex : LS_activeLayerIndexSet[0],
			urlField 	= isSlide ? 'layer_link' : 'url';

		// Add UndoManager action
		LS_UndoManager.add( undoArea, undoText, {
			itemIndex: undoIndex,
			undo: {
				[urlField]: linkData[urlField] || '',
				linkId: linkData.linkId || '',
				linkName: linkData.linkName || '',
				linkType: linkData.linkType || ''
			},
			redo: {
				[urlField]: '[post-url]',
				linkId: '',
				linkName: '',
				linkType: ''
			}
		});

		// Remove placeholder & push data to datasource
		$input.val('[post-url]');
		$holder.find('input')
			.trigger('input');

		// Update interface
		LS_GUI.updateLinkPicker( $input );

	// Insert Link dropdown
	}).on('click', '.ls-insert-link-button', function(e) {
		e.preventDefault();

		var $dropdown = jQuery('.ls-insert-link');

		setTimeout(function() {

			jQuery('body').off('click.ls-insert-link');
			$dropdown.removeClass('ls-hidden');;

			TweenLite.set( $dropdown, {
				y: 20,
				opacity: 0
			});

			TweenLite.to( $dropdown, 0.15, {
				y: 0,
				opacity: 1
			});


			jQuery('body').one('click.ls-insert-link', function() {

				TweenLite.to( jQuery('.ls-insert-link'), 0.15, {
					y: 20,
					opacity: 0,
					onComplete: function() {
						jQuery('.ls-insert-link').addClass('ls-hidden');
					}
				});

			});

		}, 100);


	// Empty linking field
	}).on('click', '.ls-slide-link a.change', function(e) {
		e.preventDefault();
		var $parent = jQuery(this).closest('.ls-slide-link');

		// UndoManager action name
		var isSlide 	= $parent.closest('.ls-slide-options').length,
			linkData 	= isSlide ? LS_activeSlideData.properties : LS_activeLayerDataSet[0],
			undoText 	= isSlide ? LS_l10n.SBUndoSlide : LS_l10n.SBUndoLayer,
			undoArea 	= isSlide ? 'slide.general' : 'layer.general',
			undoIndex 	= isSlide ? LS_activeSlideIndex : LS_activeLayerIndexSet[0],
			urlField 	= isSlide ? 'layer_link' : 'url';

		// Add UndoManager action
		LS_UndoManager.add( undoArea, undoText, {
			itemIndex: undoIndex,
			undo: {
				[urlField]: linkData[urlField] || '',
				linkId: linkData.linkId || '',
				linkName: linkData.linkName || '',
				linkType: linkData.linkType || ''
			},
			redo: {
				[urlField]: '',
				linkId: '',
				linkName: '',
				linkType: ''
			}
		});

		// Remove placeholder & push data to datasource
		$parent
			.removeClass('has-link')
			.find('input')
			.val('')
			.prop('disabled', false)
			.trigger('input');

		// Update interface
		LS_GUI.updateLinkPicker('url');

	});


	jQuery('#ls-layers').on('click', '#ls-background-notification a', function( e ) {
		e.preventDefault();
		jQuery('.ls-layer-style-tab').click();
	});

	// Use post image as slide background
	jQuery('#ls-layers').on('click', '.slide-image .ls-post-image', function(e) {
		e.preventDefault();

		var imageHolder = jQuery(this).closest('.slide-image').find('.ls-image');

		// Slide image
		if( imageHolder.hasClass('ls-slide-image') ) {
			LS_activeSlideData.properties.background = '[image-url]';
			LS_activeSlideData.properties.backgroundId = '';
			LS_activeSlideData.properties.backgroundThumb = '';

			// Reset image field, generatePreview() will populate them
			// with the actual content (if any)
			LS_GUI.updateImagePicker( 'background', false );


		// Layer image
		} else if( imageHolder.hasClass('ls-layer-image') ) {
			LS_activeLayerDataSet[0].image = '[image-url]';
			LS_activeLayerDataSet[0].imageId = '';
			LS_activeLayerDataSet[0].imageThumb = '';

			// Reset image field, generatePreview() will populate them
			// with the actual content (if any)
			LS_GUI.updateImagePicker( 'image', false );
			jQuery('.ls-sublayers li').eq(LS_activeLayerIndexSet[0])
				.find('.ls-sublayer-thumb').addClass('dashicons dashicons-format-image')
				.find('img').remove();


		// Layer background
		} else if( imageHolder.hasClass('ls-layer-background-image') ) {
			LS_activeLayerDataSet[0].layerBackground = '[image-url]';
			LS_activeLayerDataSet[0].layerBackgroundId = '';
			LS_activeLayerDataSet[0].layerBackgroundThumb = '';

			// Reset image field, generatePreview() will populate them
			// with the actual content (if any)
			LS_GUI.updateImagePicker( 'layerBackground', false );
		}

		LayerSlider.generatePreview();
	});


	LS_DataSource.buildSlide();
	LayerSlider.addPreviewSlider( jQuery('#ls-layers .ls-editor-slider'), 1 );
	LayerSlider.generatePreview();

	var $ruler = jQuery('.ls-preview-td').lsRuler();

	// Undo
	jQuery('#ls-layers').on('click', '.ls-editor-undo:not(.disabled)', function() {
		LS_UndoManager.undo();

	// Redo
	}).on('click', '.ls-editor-redo:not(.disabled)', function() {
		LS_UndoManager.redo();

	// UndoManager track options
	}).on('click focus change', 'select, input, textarea', function(event) {
		LS_UndoManager.trackInputs( event, this );

	});

	$lasso = jQuery('<div>').resizable({
		handles: 'all'

	// keep aspect ratio when resize layer at corner
	}).on('mousedown.ls', '.ui-resizable-handle', function(e){
		if( e.which == 1 ){
			$lasso.data('ui-resizable')._aspectRatio = !!this.className.match(/-se|-sw|-ne|-nw/);
		}


	// store selected layers size & position
	}).on('resizestart.ls', function( event, ui ){

		var uiPos = ui.helper.position();

		ui.originalPosition.top = uiPos.top;
		ui.originalPosition.left = uiPos.left;

		jQuery('.ls-preview .ui-selected').each(function() {
			var $layer 	= jQuery(this),
				pos 	= $layer.position();

			$layer.data('originalPosition', {
				top: pos.top / LS_previewZoom,
				left: pos.left / LS_previewZoom
			}).data('originalSize', {
				width: $layer.outerWidth(),
				height: $layer.outerHeight(),
				fontSize: parseInt($layer.css('fontSize')),
				lineHeight: $layer.css('lineHeight').indexOf('px') !== -1 ? parseInt( $layer.css('lineHeight') ) : false
			});
		});


	// update selected layers size & position
	}).on('resize.ls', function(e, ui){
		LayerSlider.resizing(e, ui);

	}).on('resizestop.ls', function(e, ui) {

		var updateInfo 	= [];
		LayerSlider.resizing(e, ui);

		// Remove directio data from $lasso
		$lasso.removeData( 'dragDirection');

		jQuery('.ls-preview .ui-selected').each(function() {
			var $layer 		= jQuery(this),
				index 		= $layer.index(),
				layerData 	= LS_activeSlideData.sublayers[index],
				position 	= $layer.position(),
				size 		= { width: $layer.width(), height: $layer.height() },
				fontSize 	= parseInt($layer.css('font-size')),
				lineHeight 	= parseInt($layer.css('line-height')),
				origPos 	= $layer.data('originalPosition'),
				origSize 	= $layer.data('originalSize');

			var undoObj = {
				itemIndex: index,
				undo: {
					top: origPos.top+'px',
					left: origPos.left+'px',
					width: origSize.width+'px',
					height: origSize.height+'px',
					'font-size': origSize.fontSize+'px',
					'line-height': origSize.lineHeight+'px',
				},
				redo: {
					top: Math.round(position.top / LS_previewZoom)+'px',
					left: Math.round(position.left / LS_previewZoom)+'px',
					width: Math.round(size.width)+'px',
					height: Math.round(size.height)+'px',
					'font-size': Math.round(fontSize)+'px',
					'line-height': Math.round(lineHeight)+'px'
				}
			};


			if( ! layerData.styles.width && ! $layer.is('img,div') ) {
				$layer.width('auto');
				delete undoObj.undo.width;
				delete undoObj.redo.width;
			}

			if( ! layerData.styles.height && ! $layer.is('img,div') ) {
				$layer.height('auto');
				delete undoObj.undo.height;
				delete undoObj.redo.height;
			}


			updateInfo.push(undoObj);
		});

		LS_UndoManager.add('layer.style', LS_l10n.SBUndoLayerResize, updateInfo);
		LayerSlider.updatePreviewSelection();

	}).addClass('ui-selected-helper').appendTo( LS_previewHolder );


	LS_previewHolder.on('mouseup.ls', function(e) {

		var $helper = jQuery('.ui-selectable-helper');
		if( $helper.length ) {

			var pos 		= $helper.position(),
				selTop 		= pos.top,
				selLeft 	= pos.left,
				selWidth  	= $helper.outerWidth(),
				selHeight 	= $helper.outerHeight(),
				items = [];

			// Loop through layers list
			LS_previewArea.children('.ls-l').each(function(layerIndex) {

				var $layer 	= jQuery(this),
					t = LS_previewArea.offset().top + $layer.position().top,
					l = LS_previewArea.offset().left + $layer.position().left,
					w = $layer.outerWidth() * LS_previewZoom,
					h = $layer.outerHeight() * LS_previewZoom;

				if(
					(t > selTop  &&  t+h < selTop+selHeight) &&
					(l > selLeft  &&  l+w < selLeft+selWidth)
				) {
					items.push(layerIndex);
				}
			});

			if(items.length) {
				LayerSlider.selectLayer( items );
			}
		}


	}).selectable({
		tolerance: 'fit',
		filter: '.ui-draggable:not(.disabled,.transformed)',
		cancel: '.disabled,.transformed'

	// removeFrom | addTo selected layers

	}).on('mouseup.ls', '.ui-draggable', function(e) {

		// Allow selecting a single layer, even if it's
		// already part of the selection if it wasn't dragged
		if( e.which !== 3 && ! LS_layerWasDragged ) {
			if( ! e.ctrlKey && ! e.metaKey ) {

				var $layer 		=  jQuery(this),
					layerIndex 	= $layer.index(),
					layerData 	= LS_activeSlideData.sublayers[ layerIndex ];

				// Prevent locked layers to be selected
				if( ! layerData || layerData.locked ) { return false; }

				LayerSlider.selectLayer( [ jQuery(this).index() ] );
				return;
			}
		}

	}).on('mousedown.ls', '.ui-draggable', function(e){

		LS_layerWasDragged = false;

		if( e.which == 1 ) {

			var $layer 		= jQuery(this),
				layerIndex 	= $layer.index(),
				layerData 	= LS_activeSlideData.sublayers[ layerIndex ];

			// Prevent locked layers to be selected
			if( ! layerData || layerData.locked ) { return false; }

			if( $layer.hasClass('ui-selected') ){
				if( e.ctrlKey || e.metaKey ){
					$layer.removeClass('ui-selected').trigger('selectablestop.ls');
				}
			} else {
				if( !e.ctrlKey && !e.metaKey ){
					$layer.siblings('.ui-selected').removeClass('ui-selected');
				}
				$layer.addClass('ui-selected').trigger('selectablestop.ls');
			}

		}

	// store selected layers, compute lasso position & size
	}).on('selectablestop.ls', function(e, ui){

		var layerIndexSet = [];
		jQuery('.ls-preview-td .ui-selected').each(function() {
			layerIndexSet.push( jQuery(this).index() );

		});

		if( ! layerIndexSet.length ) {
			layerIndexSet = LS_activeLayerIndexSet;
		}

		LayerSlider.selectLayer(layerIndexSet);


	}).on( 'dragstart.ls', function(u, ui){

		LS_layerWasDragged = true;

		var snapEl = ui.helper.data('ui-draggable').snapElements,
			snapElLength = snapEl.length,
			$item, width, height;

		for( var s=0; s<snapElLength; s++ ) {

			$item = jQuery( snapEl[s].item );

			snapEl[s].width = $item.width() * LS_previewZoom;
			snapEl[s].height = $item.height() * LS_previewZoom;
		}

			ui.helper.data({
				originalWidth: ui.helper[0].style.width,
				originalHeight: ui.helper[0].style.height
			});

	}).on('dragstop.ls', function(e, ui) {

		ui.helper[0].style.width = ui.helper.data('originalWidth') || 'auto';
		ui.helper[0].style.height = ui.helper.data('originalHeight') || 'auto';

	}).on('drag.ls', function(e, ui){

		jQuery.data( ui.helper[0], 'ui-draggable' ).helperProportions = {
			width: ui.helper.width() * LS_previewZoom,
			height: ui.helper.height() * LS_previewZoom
		};

		var dy = ( ui.position.top - ui.originalPosition.top ) / LS_previewZoom,
			dx = ( ui.position.left - ui.originalPosition.left ) / LS_previewZoom;

		// Move only horizontally/vertically while pressing shift
		if( e.shiftKey ){
			if( Math.abs(dx) >= Math.abs(dy) ){
				dy = 0; ui.position.top = ui.originalPosition.top;
			}else{
				dx = 0; ui.position.left = ui.originalPosition.left;
			}
			ui.helper.css(ui.position);
		}

		// Disable snapTo while pressing ctrl/cmd key
		if( ui.helper.draggable('option', 'snap')){
			var d = ui.helper.data('ui-draggable');
			if( (e.ctrlKey || e.metaKey) && d.snapElements.length ){
				d._snapElements = d.snapElements; d.snapElements = [];
			}
			if( !(e.ctrlKey || e.metaKey) && !d.snapElements.length ){
				d.snapElements = d._snapElements;
			}
		}

		// Update selected layers position
		jQuery.each(LS_activeLayerIndexSet, function(idx, layerIndex) {
			var $item = LS_previewItems[layerIndex];
			var op = $item.data('originalPosition');
			$item[0].style.top = ( op.top + dy ) + 'px';
			$item[0].style.left = ( op.left + dx ) + 'px';
		});

		// Update lasso position & position info
		var op = $lasso.data('originalPosition');
		$lasso.css({
			top:  op.top + dy * LS_previewZoom + 'px',
			left: op.left + dx * LS_previewZoom + 'px'
		}).attr({
			'data-info-0': 'x: ' + $lasso.css('left'),
			'data-info-1': 'y: ' + $lasso.css('top')
		});

	});

	// km-ui smartResize init
	kmUI.smartResize.init( '#ls-layers-settings-popout' );


	setTimeout(function() {
		LayerSlider.updatePreviewSelection();
	}, 200);

	if( document.fonts && document.fonts.ready && window.Promise ) {
		document.fonts.ready.then( function() {

			jQuery.each( LS_previewItems, function( layerIndex, $item ) {

				var layers 	= LS_activeSlideData.sublayers,
					data 	= layers[ layerIndex ],
					top 	= data.styles.top,
					left 	= data.styles.left;

				LayerSlider.setPositions( $item, top, left );
			});

			LayerSlider.updatePreviewSelection();

		});
	}


	jQuery(document).on('click', '.ls-activation-lock', function( e ) {
		e.preventDefault();
		lsDisplayActivationWindow();
	});
};

jQuery(document).ready(function() {

	// Initialize the interface only if the
	// lsSliderData variable is set.
	if( window.lsSliderData ) {
		initSliderBuilder();
	}
});


(function( $ ) {

	$.fn.lsRuler = function(unit) {
		unit = unit || 50;

		var $this = this.addClass('ls-ruler'),
			$preview = LS_previewWrapper;

		var offsetX = 0, offsetY = 0;
		var $info = $('<div class="ls-ruler-info">').appendTo(document.body);

		var onDragRulerLineX = function(e) {

			var y = parseFloat( $preview.data('lsRulerPos').y );
			$info.css({
				display: y > 0 ? 'block' : 'none',
				left: e.pageX + 15,
				top: e.pageY - 35,
			}).html('Y: '+ Math.round(y / LS_previewZoom) +' px');
		};

		var onDragRulerLineY = function(e) {
			var x = parseFloat( $preview.data('lsRulerPos').x );
			$info.css({
				display: x > 0 ? 'block' : 'none',
				left: e.pageX + 20,
				top: e.pageY - 40,
			}).html('X: '+ Math.round(x / LS_previewZoom) +' px');
		};


		var $x = $('<div class="ls-ruler-x">').draggable({
			axis: 'y',
			cursorAt: {top: 0},
			helper: function() {
				return $('<div class="ls-ruler-line-x">').appendTo(LS_previewWrapper);
			},
			drag: onDragRulerLineX,
			stop: function(e, ui) {
				$info.css('display', '');
				if (ui.position.top < 0) return;
				var $clone = ui.helper.clone().removeClass('ui-draggable-dragging');
					$clone.draggable({
						axis: 'y',
						start: function(e, ui) {
							offsetY = ui.offset.top - e.pageY;
						},
						drag: onDragRulerLineX,
						stop: function(e, ui) {
							offsetY = 0;
							$info.css('display', '');
							ui.position.top < 0 && ui.helper.remove();
						}
					}).data({
						originalTop: ui.position.top / LS_previewZoom,
						originalLeft: ui.position.left / LS_previewZoom
					}).appendTo($preview);
			}
		}).appendTo(LS_previewWrapper);

		var $y = $('<div class="ls-ruler-y">').draggable({
			axis: 'x',
			cursorAt: {left: 0},
			helper: function() {
				return $('<div class="ls-ruler-line-y">').appendTo(LS_previewWrapper);
			},
			drag: onDragRulerLineY,
			stop: function(e, ui) {
				$info.css('display', '');
				if (ui.position.left < 0) return;
				var $clone = ui.helper.clone().removeClass('ui-draggable-dragging');
					$clone.draggable({
						axis: 'x',
						start: function(e, ui) {
							offsetX = ui.offset.left - e.pageX;
						},
						drag: onDragRulerLineY,
						stop: function(e, ui) {
							offsetX = 0;
							$info.css('display', '');
							ui.position.left < 0 && ui.helper.remove();
						}
					}).data({
						originalTop: ui.position.top / LS_previewZoom,
						originalLeft: ui.position.left / LS_previewZoom
					}).appendTo($preview);
			}
		}).appendTo(LS_previewWrapper);

		var $xw = $('<div class="ls-ruler-wrapper">').appendTo($x),
			$yw = $('<div class="ls-ruler-wrapper">').appendTo($y),
			$xt = $('<div class="ls-ruler-tracker">').appendTo($x),
			$yt = $('<div class="ls-ruler-tracker">').appendTo($y);

		$this.on('zoom.lsRuler', function() {

			// Lower the number of ticks when zoomed out
			$this.toggleClass('disable-5px', LS_previewZoom < 0.75);

			// Resize ruler ticks
			$x.add($y).css({ fontSize: LS_previewZoom * unit });

			// Resize guide lines
			jQuery('.ls-ruler-line-x, .ls-ruler-line-y').each(function() {
				var top 	= jQuery(this).data('originalTop') * LS_previewZoom,
					left 	= jQuery(this).data('originalLeft') * LS_previewZoom;

				jQuery(this).css({ top: top, left: left });
			});

		}).on('resize.lsRuler', function() {

			$this.trigger('zoom.lsRuler');
			var xu = Math.ceil($preview.width() / LS_previewZoom / unit);
			var yu = Math.ceil($preview.height() / LS_previewZoom / unit);
			for (var i = $xw.children().length; i < xu; i++)
				$xw.append('<div class="ls-ruler-unit"><div class="ls-ruler-num">'+ i * unit);
			for (var j = $yw.children().length; j < yu; j++)
				$yw.append('<div class="ls-ruler-unit"><div class="ls-ruler-num">'+ j * unit);
		}).trigger('resize.lsRuler');

		$preview.on('mousemove.lsRuler', function(e) {
			var pos = {
				x: e.pageX + offsetX - Math.round($x.offset().left),
				y: e.pageY + offsetY - Math.round($y.offset().top)
			};
			$preview.data('lsRulerPos', pos);
			$xt.css('left', pos.x);
			$yt.css('top', pos.y);
		}).on('mouseleave.lsRuler', function() {
			$xt.css('left', '');
			$yt.css('top', '');
		});

		return $this;
	};

}( jQuery ));


var kmComboBox = {

	init: function() {

		jQuery(document).on('focus', '.km-combo-input:not(.opened)', function() {
			LS_comboBoxIsDirty = false;
			kmComboBox.show( jQuery(this) );

		}).on('click', '.km-combo-box li', function(){
			LS_comboBoxIsDirty = false;
			kmComboBox.select( jQuery(this) );

		}).on('blur', '.km-combo-input.opened', function() {
			var $input = jQuery(this);
			setTimeout(function($input) {
				kmComboBox.hide( $input );
			}, 200, $input);

			setTimeout(function() {
				if( LS_comboBoxIsDirty ) {
					LayerSlider.generateSelectedPreviewItems();
				}
			}, 100);

		}).on('mouseenter', '.km-combo-box li', function() {
			var $item 		= jQuery(this),
				cssProp 	= $item.parent().data('css-property'),
				cssVal 		= $item.text(),
				fontFamily 	= $item.data('font-family');


			if( fontFamily || cssProp ) {
				LS_previewArea
					.children('.ui-selected')
					.css( cssProp || 'font-family', fontFamily || parseInt(cssVal));

				LayerSlider.updatePreviewSelection();
				LS_comboBoxIsDirty = true;
			}
		});
	},


	show: function( $input ) {

		var $wrapper,
			$list,
			$parent = $input.parent(),
			options,
			width,
			input,
			list,
			paddingTop;

		$parent.addClass( 'km-combo-parent' );

		input = {
			width: $input.outerWidth(),
			height: $input.outerHeight( true ),
			left: $input.position().left - parseInt( $parent.css( 'padding-left') ),
			top: $input.position().top,
			margins: parseInt( $input.css( 'margin-top' ) ) + parseInt( $input.css( 'margin-bottom' ) )
		};

		$parent.removeClass( 'km-combo-parent' );

		// Retrieve list options
		options = $input.data('options');

		// Create wrapper
		$wrapper = jQuery('<div class="km-combo-box"></div>').insertAfter( $input.addClass( 'opened' ) );

		// Insert combo-list after the input
		$list = jQuery('<ul class="km-combo-list">').appendTo( $wrapper );
		$list.data('css-property', $input.data('css-property') );


		// Populate list
		jQuery.each(options, function(index, option) {
			var optionName = jQuery.type(option) == 'string' ? option : option.name || option.value,
				optionValue = option.value || optionName,
				listItem = jQuery('<li>').data({
					name: optionName,
					value: optionValue,
					linkAction: option.linkAction || false
				});

				listItem.text(optionName).appendTo($list);

			if( option.font ) {
				listItem
					.data('font-family', optionValue)
					.css('font-family', optionValue);
			} else if( option.button ) {

			} else if( option.class ) {
				listItem.addClass( option.class );
			}
		});

		// set styles
		list = {
			paddingLeft: parseInt( $list.css( 'padding-left' ) ),
			paddingRight: parseInt( $list.css( 'padding-right' ) ),
			paddingTop: parseInt( $list.css( 'padding-top' ) ),
			paddingBottom: parseInt( $list.css( 'padding-bottom' ) ),
			width: $list.outerWidth()
		};
		$list.css({
			paddingTop: list.paddingTop * 2 + input.height
		});
		list.height = $list.outerHeight();
		wrapper = {
			width: Math.max( input.width + list.paddingLeft + list.paddingRight, list.width ) + 4
		};

		$wrapper.css({
			width: wrapper.width,
			transform: 'translate3d( ' + ( ( input.left + input.width / 2 ) - wrapper.width / 2 ) + 'px,' + ( - input.height - list.paddingTop ) + 'px, 0 )',
			height: list.height
		});

	},


	hide: function( $input ) {

		// Remove the list and wrapper
		$input.removeClass( 'opened' ).next('.km-combo-box').remove();
	},


	select: function( $li ) {

		if( $li.is('.group,.button') ) {
			return;
		}

		var $wrapper 	= $li.closest('.km-combo-box'),
			$input 		= $wrapper.prev('input');

		// Link Action
		if( $li.data('linkAction' ) ) {

			var $holder = $input.closest('.ls-slide-link');

			// UndoManager action name
			var isSlide 	= $holder.closest('.ls-slide-options').length,
				linkData 	= isSlide ? LS_activeSlideData.properties : LS_activeLayerDataSet[0],
				undoText 	= isSlide ? LS_l10n.SBUndoSlide : LS_l10n.SBUndoLayer,
				undoArea 	= isSlide ? 'slide.general' : 'layer.general',
				undoIndex 	= isSlide ? LS_activeSlideIndex : LS_activeLayerIndexSet[0],
				urlField 	= isSlide ? 'layer_link' : 'url';

			// Add link change to UndoManager
			LS_UndoManager.add( undoArea, undoText, {
				itemIndex: undoIndex,
				undo: {
					[urlField]: linkData[urlField] || '',
					linkId: linkData.linkId || '',
					linkName: linkData.linkName || '',
					linkType: linkData.linkType || ''
				},
				redo: {
					[urlField]: $li.data('value'),
					linkId: '',
					linkName: $li.data('name'),
					linkType: ''
				}
			});

			// Push data to DataSource
			$input.val( $li.data('value') );
			$holder.find('input[name="linkName"]').val( $li.data('name') );

			$holder
				.find('input')
				.trigger('input');

			LS_GUI.updateLinkPicker( $input );

		// Enter value into input & trigger event
		} else {

			$input.val( $li.data('value') ).trigger('input').trigger('change');
		}
	}

};

var layerTransitionPreview = {

	create: function(){

		jQuery( '#ls-layers' ).on( 'mouseenter', '#ls-transition-selector-table .ls-tpreview-wrapper', function(){

			var _tl = new TimelineMax(),
				$el = jQuery(this);

			if( $el.data( 'ls-tpreview' ) ){
				$el.data( 'ls-tpreview' ).clear().kill();
				$el.removeData( 'ls-tpreview' );
			}

			switch( $el.attr('id').split( 'ls-tpreview-')[1] ){

				case 'in':
					_tl.fromTo( $el.find( '.ls-preview-layer' )[0], 1.5, {
						opacity: 1,
						x: -90
					},{
						ease: Quart.easeInOut,
						x: 0
					}).to( $el.find( '.ls-preview-layer' )[0], 0.25, {
						opacity: 0,
						onComplete: function(){
							_tl.progress( 0 );
						}
					});
				break;

				case 'out':
					_tl.to( $el.find( '.ls-preview-layer' )[0], 1.5, {
						ease: Quart.easeInOut,
						x: 90
					}).fromTo( $el.find( '.ls-preview-layer' )[0], 0.25, {
						immediateRender: false,
						x: 0,
						opacity: 0
					},{
						opacity: 1,
						onComplete: function(){
							_tl.progress( 0 );
						}
					});
				break;

				case 'textin':
					_tl.staggerFromTo( $el.find( '.ls-preview-layer_t' ).get(), 1, {
						opacity: 1,
						x: -100,
						rotation: -90
					},{
						ease: Quart.easeOut,
						x: 0,
						rotation: 0
					}, 0.15, null, function(){
						_tl.staggerTo( $el.find( '.ls-preview-layer_t' ).get(), 0.25, {
							opacity: 0
						}, 0, null, function(){
							_tl.progress( 0 );
						});
					});
				break;

				case 'textout':
					_tl.staggerTo( $el.find( '.ls-preview-layer_t' ).get(), 1, {
						x: 100,
						rotation: 90,
						ease: Quart.easeIn,
					}, 0.15, null, function(){
						_tl.staggerFromTo( $el.find( '.ls-preview-layer_t' ).get(), 0.25, {
							immediateRender: false,
							x: 0,
							opacity: 0,
							rotation: 0
						},{
							opacity: 1
						}, 0, null, function(){
							_tl.progress( 0 );
						});
					});
				break;

				case 'loop':
					_tl.to( $el.find( '.ls-preview-layer' )[0], 1.5, {
						rotation: 360,
						repeat: -1,
						ease: Linear.easeNone
					});
				break;

				case 'hover':
					_tl.to( $el.find( '.ls-preview-layer' )[0], .75, {
						scale: 1.5,
						repeat: -1,
						yoyo: true,
						ease: Quad.easeInOut
					});
				break;

				case 'parallax':
					_tl.to( $el.find( '.ls-preview-layer' )[0], 1, {
						x: -10,
						repeat: -1,
						yoyo: true,
						ease: Quad.easeInOut
					}, 0 );
					_tl.to( $el.find( '.ls-preview-layer_b' )[0], 1, {
						x: -15,
						repeat: -1,
						yoyo: true,
						ease: Quad.easeInOut
					}, 0 );
				break;
			}

			jQuery(this).data( 'ls-tpreview', _tl );
		});

		jQuery( '#ls-layers' ).on( 'mouseleave', '#ls-transition-selector-table .ls-tpreview-wrapper', function(){

			if( jQuery(this).data( 'ls-tpreview' ) ){
				jQuery(this).data( 'ls-tpreview' ).clear().kill();
				jQuery(this).removeData( 'ls-tpreview' );
				TweenMax.set( jQuery(this).find( '.ls-preview-layer, .ls-preview-layer_t' ).get(), {
					opacity: 1,
					rotation: 0,
					scale: 1,
					x: 0,
				});
			}
		});
	}
};


var prepTemplateForRelease = function() {

	var sliderData 	= window.lsSliderData,
		sliderProps = sliderData.properties;

	// Global BG & YourLogo
	if( sliderProps.backgroundimage ) { sliderProps.backgroundimage = LS_Utils.parse_url( sliderProps.backgroundimage, 'PHP_URL_PATH'); }
	if( sliderProps.yourlogo ) { sliderProps.yourlogo = LS_Utils.parse_url( sliderProps.yourlogo, 'PHP_URL_PATH'); }
	if( sliderProps.preview ) { sliderProps.preview = LS_Utils.parse_url( sliderProps.preview, 'PHP_URL_PATH'); }
	if( sliderData.meta && sliderData.meta.preview ) { sliderData.meta.preview = LS_Utils.parse_url( sliderData.meta.preview, 'PHP_URL_PATH'); }


	// Slides
	jQuery.each(window.lsSliderData.layers, function(slideIndex, slideData) {

		var slideProps = slideData.properties;

		slideData.history = [];

		if( slideData.meta && slideData.meta.undoStackIndex ) {
			slideData.meta.undoStackIndex = -1;
		}

		if( slideProps.background ) { slideProps.background = LS_Utils.parse_url( slideProps.background, 'PHP_URL_PATH'); }
		if( slideProps.backgroundThumb ) { slideProps.backgroundThumb = LS_Utils.parse_url( slideProps.backgroundThumb, 'PHP_URL_PATH'); }

		if( slideProps.thumbnail ) { slideProps.thumbnail = LS_Utils.parse_url( slideProps.thumbnail, 'PHP_URL_PATH'); }
		if( slideProps.thumbnailThumb ) { slideProps.thumbnailThumb = LS_Utils.parse_url( slideProps.thumbnailThumb, 'PHP_URL_PATH'); }

		// Layers
		jQuery.each(slideData.sublayers, function(layerIndex, layerData) {

			if( layerData.image ) { layerData.image = LS_Utils.parse_url( layerData.image, 'PHP_URL_PATH'); }
			if( layerData.imageThumb ) { layerData.imageThumb = LS_Utils.parse_url( layerData.imageThumb, 'PHP_URL_PATH'); }

			if( layerData.poster ) { layerData.poster = LS_Utils.parse_url( layerData.poster, 'PHP_URL_PATH'); }
			if( layerData.posterThumb ) { layerData.posterThumb = LS_Utils.parse_url( layerData.posterThumb, 'PHP_URL_PATH'); }

			if( layerData.layerBackground ) { layerData.layerBackground = LS_Utils.parse_url( layerData.layerBackground, 'PHP_URL_PATH'); }
			if( layerData.layerBackgroundThumb ) { layerData.layerBackgroundThumb = LS_Utils.parse_url( layerData.layerBackgroundThumb, 'PHP_URL_PATH'); }
		});
	});

	LS_UndoManager.update();

	alert("All Done. Performed tasks:\r\n\r\n– Converted URLs to relative format\r\n– Emptied slides history\r\n\r\nManual save required.");
};
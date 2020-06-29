if (!Array.prototype.indexOf) {
	Array.prototype.indexOf = function (searchElement /*, fromIndex */ ) {
		"use strict";
		if (this === null) {
			throw new TypeError();
		}
		var t = Object(this);
		var len = t.length >>> 0;
		if (len === 0) {
			return -1;
		}
		var n = 0;
		if (arguments.length > 1) {
			n = Number(arguments[1]);
			if (n != n) { // shortcut for verifying if it's NaN
				n = 0;
			} else if (n != 0 && n != Infinity && n != -Infinity) {
				n = (n > 0 || -1) * Math.floor(Math.abs(n));
			}
		}
		if (n >= len) {
			return -1;
		}
		var k = n >= 0 ? n : Math.max(len - Math.abs(n), 0);
		for (; k < len; k++) {
			if (k in t && t[k] === searchElement) {
				return k;
			}
		}
		return -1;
	};
}

Array.prototype.fill = function(value, length){
	while(length--){
		this[length] = value;
	}
	return this;
};

Storage.prototype.setObject = function(key, value) {
	this.setItem(key, JSON.stringify(value));
};

Storage.prototype.getObject = function(key) {
	var value = this.getItem(key);
	return value && JSON.parse(value);
};




function isNumber(n) {
	return !isNaN(parseFloat(n)) && isFinite(n);
}

function ucFirst(string) {
	return string.charAt(0).toUpperCase() + string.slice(1);
}



(function( $ ) {
	$.fn.appendToWithIndex = function(to, index) {

		if( !( to instanceof jQuery ) ) { to = $(to); }

		if(index == 0) {
			this.prependTo(to);
		} else {
			this.insertAfter( to.children(':eq('+(index-1)+')') );
		}

		return this;
	};
})( jQuery );



(function( $ ) {

	$.fn.customCheckbox = function() {
		return this.each(function() {

			// Get element & hide it
			var $el = $(this).hide();

			// Create replacement element
			var $rep = $('<a href="#"><span></span></a>').addClass('ls-checkbox').insertAfter(this);
				$rep.addClass( $el.attr('class') );

			// Add data-* params to replacement element
			$.each( $el.data(), function( key, val ) {
				key = key.replace(/([a-zA-Z])(?=[A-Z])/g, '$1-').toLowerCase()
				$rep.attr('data-'+key, val);
			});

			// Set default state
			if( $el.prop( 'checked' ) ) {
				$rep.addClass( 'on' );
			} else {
				$rep.addClass( 'off' );
			}
		});
	};
})( jQuery );


(function( $ ) {

	$.fn.kmTabs = function(p) {

		var properties = $.extend({}, p);

		return this.each(function(){

			var $tabs = $(this);
			var $content =  properties.content ? $(properties.content) : $(this).next('.km-tabs-content');

			$tabs.on('click', 'a', function(event){

				event.preventDefault();

				if( $(this).hasClass('active') ) {
					return false;
				}

				$tabs.children().removeClass('active');
				$(this).addClass('active');

				var index = $(this).index();
				var $iContent = $content.children().eq(index);

				$iContent.find('.km-tabs-inner').css({
					display : 'block'
				});
				var targetedHeight = $iContent.outerHeight();
				$iContent.find('.km-tabs-inner').css({
					display : 'none'
				});

				$content.find('> .active .km-tabs-inner').fadeOut(200,function(){
					$iContent.find('.km-tabs-inner').fadeIn(200);
					$content.children().removeClass('active').eq(index).addClass('active');
				});

				$content.animate({
					height: targetedHeight
				},400, function(){
					$content.css('height','auto');
				});

			});
		});
	};

}( jQuery ));


(function( $ ) {

	$.fn.kmAccordion = function() {

		this.children().each(function(){

			var $aOuter =  $(this),
				$aHead = $(this).children(':first-child'),
				$aBody = $(this).children(':last-child');

			$aHead.on('click', function(event){

				// Don't trigger .click() on accordion controls
				if($(event.target).is('.dashicons, .ls-checkbox, .ls-checkbox > span')) {
					return;
				}

				if( $aOuter.hasClass('km-accordion-opened') ){
					$aBody.css('overflow', 'hidden').slideUp(function(){
						$aBody.css('overflow', 'visible');
						$aOuter.removeClass('km-accordion-opened');
					});
				}else{
					$aBody.css('overflow', 'hidden').slideDown(function(){
						$aBody.css('overflow', 'visible');
						$aOuter.addClass('km-accordion-opened');
					});
					if( !$aOuter.parent().hasClass('km-accordion-multiple') ){
						var $siblingBody = $aOuter.siblings('.km-accordion-opened').children(':last-child');
						$siblingBody.css('overflow', 'hidden').slideUp(function(){
							$siblingBody.css('overflow', 'visible');
							$(this).parent().removeClass('km-accordion-opened');
						});
					}
				}

			});
		});

		return this;
	};

}( jQuery ));



var LS_CodeMirror = {

	init : function(settings) {

		var defaults = {
			mode: 'css',
			theme: 'solarized',
			lineNumbers: true,
			lineWrapping: true,
			autofocus: true,
			indentUnit: 4,
			indentWithTabs: true,
			foldGutter: true,
			gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"],
			styleActiveLine: true,
			extraKeys: {
				"Ctrl-Q": function(cm) {
					cm.foldCode(cm.getCursor());
				}
			}
		};


		jQuery('.ls-codemirror').each(function() {

			var options = jQuery.extend(true, {}, defaults, settings || {});

			if( jQuery(this).prop('readonly') ) {
				options.readOnly = true;
				options.theme += ' readonly';
			}

			var cm = CodeMirror.fromTextArea(this, options);

			cm.on('change', function( cm ) {

				cm.save();
				jQuery( cm.getTextArea() ).trigger('updated.ls', cm);
			});


			if( jQuery(this).closest('.ls-callback-box').length ) {

				cm.on('beforeChange',function( cm, change ) {

					// Select all
					if( change.from.line === 0 && change.to.line === cm.lastLine() ) {

						cm.setSelection({ line: 1, ch: 0 }, { line: cm.lastLine()-1, ch: 99999 });
						cm.replaceSelection( change.text[0] );
						change.cancel();

					} else {

						if( change.from.line === 0) {
							change.from.line = 1;
						}

						if( change.to.line === cm.lastLine() ) {
							change.to.line = cm.lastLine()-1;
						}
					}
				});
			}
		});
	}
};



jQuery(function($) {

	var lsScreenOptionsActions = {

		init : function() {

			// Form submit
			$(document).on('submit', '#ls-screen-options-form', function(e) {
				e.preventDefault(); lsScreenOptionsActions.saveSettings(this, true);
			});

			// Checkboxes
			$(document).on('click', '#ls-screen-options-form input:checkbox', function() {

				var reload 	= false,
					option 	= $(this).attr('name'),
					value 	= $(this).prop('checked');

				if(typeof lsScreenOptionsActions[ option ] != "undefined") {
					lsScreenOptionsActions[ option ](this);
				}

				if( typeof lsScreenOptions !== 'undefined') {
					lsScreenOptions[ option ] = value.toString();
				}

				if($(this).hasClass('reload')) { reload = true; }

				lsScreenOptionsActions.saveSettings( $(this).closest('form'), reload );
			});
		},

		saveSettings : function(form, reload) {

			var options = {};
			$(form).find('input:not([type="hidden"])').each(function() {
				if( $(this).is(':checkbox')) {
					options[$(this).attr('name')] = $(this).prop('checked');
				} else {
					options[$(this).attr('name')] = $(this).val();
				}
			});

			var data = $.param({
				action : 'ls_save_screen_options',
				_wpnonce: jQuery('input[name="_wpnonce"]', form).val(),
				_wp_http_referer: jQuery('input[name="_wp_http_referer"]', form).val(),
				options : options,

			});

			// Save settings
			$.post(ajaxurl, data, function() {
				if(typeof reload != "undefined" && reload === true) {
					document.location.href = LS_l10n.adminURL+'?page=layerslider';
				}
			});
		},

		showTooltips : function(el) {

			if( $(el).prop('checked') === true ) {
				kmUI.popover.init();
			} else {
				kmUI.popover.destroy();
			}
		}
	};

	var lsSlideUnder = {

		init : function(){

			$(document).on('click', '[data-ls-su]', function() {
				//if( $(this).parent().find('.ls-su').length == 0 ){
					lsSlideUnder.open($(this));
				//}
			});
		},

		create : function($el){

			// lsSlideUnder container is positioned absolute so we need a relative or absolute position parent element

			if( $el.parent().css('position') == 'static' ){
				$el.parent().css('position','relative');
			}

			if( $el.css('position') == 'static' ){
				$el.css('position','relative');
			}

			// Creating lsSlideUnder HTML markup

			var $su = $('<div>'),
				$sui = $('<div>'),
				$suc = $('<div>');

			$su.addClass('ls-su');
			$sui.addClass('ls-su-inner');
			$suc.addClass('ls-su-content');

			// Appending into the parent of the opener element

			$el.parent().prepend( $su
					.append( $sui
				.append( $suc
					)
				)
			);

			// Copying some CSS properties from the opener element

			var suiProps = [
				'borderRightStyle',
				'borderRightWidth',
				'borderRightColor',
				'borderLeftStyle',
				'borderLeftWidth',
				'borderLeftColor',
				'borderBottomStyle',
				'borderBottomWidth',
				'borderBottomColor',
				'backgroundColor'
			];

			for(i=0;i<suiProps.length;i++){
				$sui.css( suiProps[i], $el.css(suiProps[i]) );
			}

			$suc.css({
				'paddingTop' : $el.css('paddingLeft'),
				'paddingLeft' : $el.css('paddingLeft'),
				'paddingBottom' : $el.css('paddingLeft'),
				'paddingRight' : $el.css('paddingRight')
			});

			// Sizing and positioning

			$su.css({
				left: $el.position().left + parseInt( $el.css('marginLeft') ),
				top: $el.position().top + parseInt( $el.css('marginTop') ) + $el.outerHeight(),
				width: $el.width() + parseInt( $el.css('paddingLeft') ) + parseInt( $el.css('paddingRight') ) + parseInt( $el.css('borderLeftWidth')) + parseInt( $el.css('borderRightWidth'))
			});

			// Inserting data to content

			$suc.append( $el.siblings('.ls-su-data').html() );
		},

		open : function( $el ){

			if( !$el.parent().find( '.ls-su' ).length ){
				lsSlideUnder.create( $el );
			}

			$su = $el.parent().find( '.ls-su' );
			$sui = $el.parent().find( '.ls-su-inner' );

			if( $su.hasClass( 'ls-su-opened') ){
				return;
			}

			$su.addClass( 'ls-su-opened' );

			TweenLite.set( $su.parent()[0], {
					z:100
			});

			TweenLite.set( $su[0],
				{
					opacity: .7,
					height: 'auto',
					transformOrigin: 'center top',
					rotationX: 90,
					transformPerspective: 500
				}
			);

			TweenLite.set( $sui[0],
				{
					top: 0
				}
			);

			TweenLite.to(
				$su[0],
				2,
				{
					opacity: 1,
					rotationX: 0,
					ease: 'Elastic.easeOut'
				}
			);

			// Creating close function

			$(document).one( 'click', function(e){
				lsSlideUnder.close($su, $sui);
			});
		},

		close : function($su, $sui){

			TweenLite.to(
				$sui[0],
				.3,
				{
					top: -$sui.outerHeight(),
					ease: 'Quart.easeIn'
				}
			);

			TweenLite.to(
				$su[0],
				.3,
				{
					opacity: .7,
					height: 0,
					ease: 'Quart.easeIn',
					onComplete : function(){
						$su.removeClass( 'ls-su-opened' );
					}
				}
			);
		}
	};

	lsSlideUnder.init();

	var lsUIDependencies = {

		init : function() {

			$(document).on('change input click', '[data-toggleitems], [data-showitems], [data-hideitems]', function( event ) {

				var $el = jQuery( this );


				if( event.type === 'click' && $el.is('select,input,textarea,.ls-checkbox') ) {
					return;
				}


				if( $el.is('select') ) {
					$el = $el.children(':selected');
				}

				if( $el.data('showitems') ) {
					jQuery( $el.data('showitems') ).addClass('ls-hidden');
				}

				if ( $el.data('hideitems') ) {
					jQuery( $el.data('hideitems') ).removeClass('ls-hidden');
				}

				if( $el.data('toggleitems') ) {

					var $targets = jQuery( $el.data('toggleitems') );

					if( $el.data('opened') ) {
						$el.data('opened', false);
						$targets.addClass('ls-hidden');
					} else {
						$el.data('opened', true);
						$targets.removeClass('ls-hidden');
					}
				}
			});
		},
	};




	// Popovers
	if(typeof lsScreenOptions != 'undefined' && lsScreenOptions['showTooltips'] == 'true') {
		kmUI.popover.init();
	}

	lsUIDependencies.init();

	lsScreenOptionsActions.init();

	// Screen options
	$('#ls-guides, #ls-screen-options').children(':first-child').prependTo('#screen-meta');
	$('#ls-guides, #ls-screen-options').children(':last-child').prependTo('#screen-meta-links');



	// CodeMirror
	if(document.location.href.indexOf('&action=edit') === -1) {
		LS_CodeMirror.init();
	}

	// About page
	if( document.location.href.indexOf('section=about') !== -1 ) {
		$('.km-tabs').kmTabs();
	}


	// Skin/CSS Editor
	if( document.location.href.indexOf('section=skin-editor') !== -1 ) {
		$('select[name="skin"]').change(function() {
			document.location.href = LS_l10n.adminURL+'?page=layerslider-options&section=skin-editor&skin=' + $(this).children(':selected').val();
		});
	}


	// Checkbox event
	$(document).on('click', '.ls-checkbox', function(e){
		e.preventDefault();

		// Get checkbox
		var el = $(this).prev()[0];

		// Disabled, exit quietly
		if( el.disabled ) { return; }

		if( $(el).is(':checked') && ! $(this).is('.indeterminate') ) {
			$(el).prop('checked', false);
			$(this).removeClass('on indeterminate').addClass('off');
		} else {
			$(el).prop('checked', true);
			$(this).removeClass('off indeterminate').addClass('on');
		}

		// Trigger events
		$(el).trigger('change');
		$('#ls-layers').trigger( $.Event('click', { target : el } ) );
		$(document).trigger( $.Event('click', { target : el } ) );
	});

});


var lsDisplayActivationWindow = function( windowProperties ) {

	var deafultProperties = {
		into: 'body',
		title: LS_l10n.activationFeature,
		content: jQuery('#tmpl-activation').text(),
		minHeight: 740,
		maxHeight: 740
	};

	windowProperties = jQuery.extend( true, deafultProperties, windowProperties );

	kmw.modal.open({
		uid: 'activation-window',
		into: windowProperties.into,
		title: windowProperties.title,
		content: windowProperties.content,
		minWidth: 880,
		maxWidth: 880,
		minHeight: windowProperties.minHeight,
		maxHeight: windowProperties.maxHeight,
		zIndex: 9999999,

		modalClasses: 'activation-modal-window',

		overlaySettings: {
			animationIn: 'fade',
			zIndex: 9999999,
		},

		onOpen: function( modal ) {

			jQuery( modal.element ).addClass('kmw-modal-visible');
		}
	});

}
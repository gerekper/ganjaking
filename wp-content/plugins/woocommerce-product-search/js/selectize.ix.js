/*!
 * selectize.ix.js
 *
 * Copyright (c) 2018 "kento" Karim Rahimpur www.itthinx.com
 * 
 * ixremove derived from remove_button Copyright (c) 2013 Brian Reavis & contributors
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not use this
 * file except in compliance with the License. You may obtain a copy of the License at:
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software distributed under
 * the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF
 * ANY KIND, either express or implied. See the License for the specific language
 * governing permissions and limitations under the License.
 *
 * @author itthinx
 * @package woocommerce-product-search
 * @since 2.9.0
 */

Selectize.define( 'ixnorm', function( options ) {

	var self = this;

	self.positionDropdown = ( function() {
		var original = self.positionDropdown;
		return function() {
			var $control = self.$control;
			var offset = self.settings.dropdownParent === 'body' ? $control.offset() : $control.position();
			offset.top += $control.outerHeight(true);
			self.$dropdown.css( {
				width : $control[0].getBoundingClientRect().width,
				top   : 0,
				left  : 0
			} );
		};
	} )();
});

Selectize.define( 'ixboxed', function( options ) {

	var self = this;


	self.close = ( function() {
		var original = self.close;
		return function() {
		};
	} )();

	self.setup = ( function() {
		var original = self.setup;
		return function() {
			original.apply(self, arguments);
			self.open();
		};
	} )();

});

var ixboxed = {};

( function( $ ) {

	ixboxed.adjustSize = function( selectize_element, size, i ) {
		if ( typeof size === 'undefined' ) {
			size = 10;
		}
		if ( typeof i === 'undefined' ) {
			i = 0;
		} else {
			if ( i >= 42 ) {
				return;
			}
		}
		var dropdown_content = $( selectize_element ).closest( '.selectize-control' ).find( '.selectize-dropdown-content' );
		if ( dropdown_content.length > 0 ) {

			var children = dropdown_content.children();
			if ( children.length > 0 ) {
				var dropdown = $( selectize_element ).closest( '.selectize-control' ).find( '.selectize-dropdown' );

				var line_height = parseFloat( dropdown_content.first().css( 'line-height' ) ); // in pixels
				var j = 0, sum = 0;
				children.each( function( index, element ) {
					if ( index < size ) {
						sum += parseFloat( $( element ).outerHeight() );
					} else {
						return false;
					}
				} );
				sum += parseFloat( dropdown_content.css( 'padding-top' ) ); // pixels
				sum += parseFloat( dropdown_content.css( 'padding-bottom' ) ); // pixels
				var height = size * line_height;
				if ( sum > 0 ) {
					height = sum;
				}
				var css = {
					'height'     : height + 'px',
					'max-height' : height + 'px'
				};
				dropdown.css( css );
			} else {
				setTimeout( function() { ixboxed.adjustSize( selectize_element, size, i + 1 ); }, 100 );
			}
		}

	};
} )( jQuery );

( function( $ ) {

	Selectize.define( 'ixremove', function( options ) {

		var self = this;

		options = $.extend( {
			label     : '&times;',
			title     : selectize_ix.clear,
			className : 'remove',
			append    : true
		}, options );

		/**
		 * Escapes a string for use within HTML.
		 *
		 * @param {string} str
		 * @returns {string}
		 */
		var escape_html = function(str) {
			return (str + '')
				.replace(/&/g, '&amp;')
				.replace(/</g, '&lt;')
				.replace(/>/g, '&gt;')
				.replace(/"/g, '&quot;');
		};

		var singleClose = function(thisRef, options) {

			options.className = 'remove-single';

			var self = thisRef;
			var html = '<a href="javascript:void(0)" class="' + options.className + '" tabindex="-1" title="' + escape_html(options.title) + '">' + options.label + '</a>';

			/**
			 * Appends an element as a child (with raw HTML).
			 *
			 * @param {string} html_container
			 * @param {string} html_element
			 * @return {string}
			 */
			var append = function(html_container, html_element) {

				return jQuery( html_container ).append( html_element );
			};

			thisRef.setup = (function() {
				var original = self.setup;
				return function() {

					if (options.append) {
						var id = jQuery(self.$input.context).attr('id');
						var selectizer = jQuery('#'+id);

						var render_item = self.settings.render.item;
						self.settings.render.item = function(data) {
							return append(render_item.apply(thisRef, arguments), html);
						};
					}

					original.apply(thisRef, arguments);

					thisRef.$control.on('click', '.' + options.className, function(e) {
						e.preventDefault();
						if (self.isLocked) return;

						self.clear();
					});

				};
			})();
		};

		var multiClose = function(thisRef, options) {

			var self = thisRef;
			var html = '<a href="javascript:void(0)" class="' + options.className + '" tabindex="-1" title="' + escape_html(options.title) + '">' + options.label + '</a>';

			/**
			 * Appends an element as a child (with raw HTML).
			 *
			 * @param {string} html_container
			 * @param {string} html_element
			 * @return {string}
			 */
			var append = function(html_container, html_element) {
				var pos = html_container.search(/(<\/[^>]+>\s*)$/);
				return html_container.substring(0, pos) + html_element + html_container.substring(pos);
			};

			thisRef.setup = (function() {
				var original = self.setup;
				return function() {

					if (options.append) {
						var render_item = self.settings.render.item;
						self.settings.render.item = function(data) {
							return append(render_item.apply(thisRef, arguments), html);
						};
					}

					original.apply(thisRef, arguments);

					thisRef.$control.on('click', '.' + options.className, function(e) {
						e.preventDefault();
						if (self.isLocked) return;

						var $item = jQuery(e.currentTarget).parent();
						self.setActiveItem($item);
						if (self.deleteSelection()) {
							self.setCaret(self.items.length);
						}
					});

				};
			})();
		};

		jQuery.extend( Selectize.prototype, {

			set_items: function( values, silent ) {

				if ( typeof values !== 'object' ) {
					values = [];
				}

				if ( typeof silent === 'undefined' ) {
					silent = true;
				}

				for ( var i = 0; i < this.items.length ; i++ ) {
					this.removeItem( this.items[i], silent );
				}
				for ( var i = 0; i < values.length ; i++ ) {
					this.addItem( values[i], silent );
				}
				this.updatePlaceholder();

				this.showInput();
			}
		} );

		self.getSearchOptions = ( function() {
			var original = self.getSearchOptions;
			return function() {
				var settings = self.settings;
				var sort = settings.sortField;
				if (typeof sort === 'string') {
					sort = [{field: sort}];
				}
				return {
					fields      : settings.searchField,
					conjunction : settings.searchConjunction,
					sort        : sort,
					nesting     : settings.nesting,
					sort_empty  : false
				};
			};
		} )();


		jQuery( document ).ready( function() {

			jQuery( document ).on( 'focusin focusout', '.selectize-input', function( event ) {
				var dropdown = jQuery( this ).closest( '.selectize-control' ).find( '.selectize-dropdown' ).first(),
					z_index = dropdown.css( 'z-index' ),
					z_index2 = z_index > 0 ? z_index * 2 : 1;
				if ( !dropdown.data( 'zindex' ) ) {
					dropdown.data( 'zindex', z_index );
				}
				switch ( event.type ) {
					case 'focusin' :
						dropdown.css( 'z-index', z_index2 );
						break;
					case 'focusout' :
						dropdown.css( 'z-index', dropdown.data( 'zindex' ) );
						break;
				}
			} );
		} );

		if ( this.settings.mode === 'single' ) {
			singleClose( this, options );
		} else {
			multiClose( this, options );
		}
	});

} )( jQuery );

( function( $ ) {

	Selectize.define( 'ixthumbnail', function( options ) {

		var self = this;

		options = $.extend(
			{
				show_selected_thumbnails : true
			},
			options
		);

		var isset = function(object) {
			return typeof object !== 'undefined';
		};

		var hash_key = function(value) {
			if (typeof value === 'undefined' || value === null) return null;
			if (typeof value === 'boolean') return value ? '1' : '0';
			return value + '';
		};

		var escape_html = function(str) {
			return (str + '')
				.replace(/&/g, '&amp;')
				.replace(/</g, '&lt;')
				.replace(/>/g, '&gt;')
				.replace(/"/g, '&quot;');
		};

		self.render = ( function() {
			var original = self.render;
			return function( templateName, data ) {
				var value, id, label;
				var html = '';
				var cache = false;
				var self = this;
				var regex_tag = /^[\t \r\n]*<([a-z][a-z0-9\-_]*(?:\:[a-z][a-z0-9\-_]*)?)/i;

				if (templateName === 'option' || templateName === 'item') {
					value = hash_key(data[self.settings.valueField]);
					cache = !!value;
				}

				if (cache) {
					if (!isset(self.renderCache[templateName])) {
						self.renderCache[templateName] = {};
					}
					if (self.renderCache[templateName].hasOwnProperty(value)) {
						return self.renderCache[templateName][value];
					}
				}

				html = $(self.settings.render[templateName].apply(this, [data, escape_html]));

				if (templateName === 'option' || templateName === 'option_create') {
					if (!data[self.settings.disabledField]) {
						html.attr('data-selectable', '');
					}
				}
				else if (templateName === 'optgroup') {
					id = data[self.settings.optgroupValueField] || '';
					html.attr('data-group', id);
					if(data[self.settings.disabledField]) {
						html.attr('data-disabled', '');
					}
				}
				if (templateName === 'option' || templateName === 'item') {
					html.attr('data-value', value || '');

				}

				if (cache) {
					self.renderCache[templateName][value] = html[0];
				}

				return html[0];
			};
		} )();

		self.setupTemplates = ( function() {
			var original = self.setupTemplates;
			return function() {
				original.apply(self);
				self.settings.render.option = function( data, escape ) {
					var thumbnail = ix_dropdown_thumbnails[data.value],
						thumbnail_html = '',
						padding = '',
						label = data[self.settings.labelField],
						depth = 0,
						step = 0,
						style = '',
						dir = 'ltr';

					if ( typeof thumbnail !== 'undefined' ) {
						if ( typeof thumbnail.html !== 'undefined' ) {
							thumbnail_html = thumbnail.html;
							padding = thumbnail.padding;

							depth = thumbnail.depth,
							step = thumbnail.padding_step;
							var padding_width = Math.max( 0, depth - 1 ) * step;
							style =
								'width:' + padding_width + 'px;' +
								'min-width:' + padding_width + 'px;' +
								'max-width:' + padding_width + 'px';
						}
					}

					var output =
						'<div class="option">' +
							'<span class="option-padding" style="' + style + '">' +
							padding +
							'</span>' +
							'<span class="option-thumbnail">' +
							thumbnail_html +
							'</span>' +
							'<span class="option-label">' +
							escape( label ) +
							'</span>' +
						'</div>';
					return output;
				};

				if ( options.show_selected_thumbnails ) {
					self.settings.render.item = self.settings.render.option;
				}
			};
		} )();
	});

} )( jQuery );

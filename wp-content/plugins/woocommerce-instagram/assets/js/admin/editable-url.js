/**
 * Editable URL
 *
 * @package WC_Instagram/Assets/JS/Admin
 * @since   3.0.0
 */

/* global wcSetClipboard, wcClearClipboard, wc_instagram_editable_url_params */
( function( $ ) {

	'use strict';

	if ( typeof wc_instagram_editable_url_params === 'undefined' ) {
		return false;
	}

	$.EditableURL = function( input, options ) {
		var defaults = {
			'copied': 'Copied!',
			'buttons': {
				'edit': 'Edit',
				'save': 'Ok',
				'cancel': 'Cancel',
				'copy': 'Copy URL'
			}
		};

		this.options = $.extend( true, {}, defaults, options );

		this.$input     = $( input );
		this.$container = $( '<div class="wc-instagram-editable-url"></div>' );

		this.url     = this.$input.data( 'url' );
		this.value   = this.$input.val();
		this.editing = false;

		this.bindEvents();
		this.render();
	};

	$.EditableURL.prototype = {

		bindEvents: function() {
			var that = this;

			this.$container.on( 'keypress', 'input[type="text"]', function( event ) {
				if ( 13 === event.keyCode ) {
					event.preventDefault();

					that.$container.find( '.editable-url-actions .save' ).trigger( 'click' );
				}
			});

			this.$container.on( 'click', '.editable-url-actions .edit', function() {
				that.setEditing( true );
			});

			this.$container.on( 'click', '.editable-url-actions .copy', function() {
				wcClearClipboard();
				wcSetClipboard( that.getUrl(), $( this ) );
			});

			this.$container.on( 'aftercopy', '.editable-url-actions .copy', function() {
				$( this ).tipTip( {
					'attribute':  'data-tip',
					'activation': 'focus',
					'fadeIn':     50,
					'fadeOut':    50,
					'delay':      0
				}).trigger( 'focus' );
			});

			this.$container.on( 'click', '.editable-url-actions .save', function() {
				var value = that.$container.find( 'input' ).val();

				if ( value !== that.value ) {
					that.$input.trigger( 'wc_instagram_editable_url_change', value );
				} else {
					that.setEditing( false );
				}
			});

			this.$container.on( 'click', '.editable-url-actions .cancel', function() {
				that.setEditing( false );
			});

			this.$input.on( 'change', function() {
				that.value = $( this ).val();

				that.setEditing( false );
			});
		},

		getUrl: function() {
			return ( this.value ? this.url.replace( '{editable}', this.value ) : '' );
		},

		setEditing: function( editing ) {
			this.editing = editing;
			this.updateContainerHtml();
		},

		render: function () {
			// Hide the default input field.
			this.$input.attr( 'type', 'hidden' );

			// Initialize the container HTML content.
			this.updateContainerHtml();

			// Add the container to the DOM.
			this.$input.after( this.$container );
		},

		updateContainerHtml: function () {
			var html = ( this.editing ? this.getEditingURLHtml() : this.getLinkHtml() );

			html += this.getActionsHtml();

			this.$container.html( html );
		},

		getLinkHtml: function () {
			var label = '';

			if ( this.value ) {
				label = this.url.replace( '{editable}', '<span class="editable">' + this.value + '</span>' );
			}

			return '<a href="' + this.getUrl() + '" target="_blank">'+ label + '</a>';
		},

		getEditingURLHtml: function() {
			var input = '<input type="text" value="' + this.value + '" />';

			return '<code>' + this.url.replace( '{editable}', '</code>' + input + '<code>' ) + '</code>';
		},

		getActionsHtml: function () {
			var actions = [];

			if ( this.editing ) {
				actions.push( '<button class="save button button-small" type="button">' + this.options.buttons.save + '</button>' );
				actions.push( '<button class="cancel button-link" type="button">' + this.options.buttons.cancel + '</button>' );
			} else {
				actions.push( '<button class="edit button button-small" type="button">' + this.options.buttons.edit + '</button>' );

				if ( this.value ) {
					actions.push( '<button class="copy button button-small" type="button" data-tip="' + this.options.copied + '">' + this.options.buttons.copy + '</button>' );
				}
			}

			return '<span class="editable-url-actions">' + actions.join( '' ) + '</span>';
		}
	};

	$.fn.wc_instagram_editable_url = function( options ) {
		options = $.extend( true, {}, wc_instagram_editable_url_params, options );

		this.each(function() {
			new $.EditableURL( this, options );
		});

		return this;
	};
})( jQuery );

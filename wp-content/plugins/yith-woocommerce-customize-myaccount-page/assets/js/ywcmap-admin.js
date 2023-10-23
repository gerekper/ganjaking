jQuery( document ).ready( function( $ ) {
	"use strict";

	function applySelect2( select ) {
		if ( typeof $.fn.selectWoo != 'undefined' ) {
			$.each( select, function() {
				let data, value,
					selected = {};

				// build data
				if ( $( this ).hasClass( 'icon-select' ) ) {
					value = $( this ).data( 'value' );
					data = {
						data: ywcmap_icons,
						escapeMarkup: function( m ) {
							return m;
						},
						templateResult: function( icon ) {
							return $( '<span><i class="fa fa-' + icon.text + '"></i>   ' + icon.text + '</span>' );
						},
					};
				} else {
					data = {
						minimumResultsForSearch: 10
					};
				}

				$( this ).selectWoo( data );
				if ( typeof value != 'undefined' && value ) {
					$( this ).val( value ).change();
				}
			} );
		}
	}

	applySelect2( $( '#yith_wcmap_panel_general' ).find( 'select' ) );
	applySelect2( $( '#yith_wcmap_banners' ).find( 'select' ) );
	$( document ).on( 'yith-add-box-button-toggle', function() {
		applySelect2( $( '#yith_wcmap_banners_add_box' ).find( 'select' ) );
	} );

	$( document ).on( 'yith_select_images_value_changed', function( ev ) {
		let value = $( 'select[name="yith_wcmap_menu_position"]' ).val(),
			target = $( '#yith_wcmap_menu_layout-wrapper li' );

		if ( 'horizontal' !== value ) {
			value = 'vertical';
		}

		$.each( target, function( t ) {
			$( this ).find( 'img' ).attr( 'src', $( this ).attr( 'data-' + value ) );
		} );
	} ).trigger( 'yith_select_images_value_changed' );

	const ywcmap_items_list = {
		// Vars
		itemsSection: $( '#yith-wcmap-items-list' ),
		addItemForm: $( '#yith-wcmap-add-item-form' ),
		itemsForm: $( '#yith-wcmap-items-form' ),
		itemsList: $( '.items-container' ),

		// Init
		init: function() {

			if ( ! this.itemsForm.length ) {
				return false;
			}

			this._nestable();
			this._handleOptionsDeps();
			applySelect2( this.itemsForm.find( 'select' ), true );

			$( document ).on( 'yith_wcmap_item_added', this._reInitItem );

			// Handle actions
			this.itemsList.on( 'click', '.toggle-options', this.toggleOptions );
			this.itemsList.on( 'change', '.item-actions .on_off', this.onOffItem );
			this.itemsList.on( 'click', '.remove-item:not(.disabled)', this.removeItem );
			this.itemsList.on( 'click', '.save-item', this.saveItem );
			this.itemsList.on( 'click', '.reset-item', this.resetItem );
			// Handle drag stop
			this.itemsList.on( 'change', this._reInitItem );
			// Handle add new item
			this.itemsSection.on( 'click', '.create-new-item', this.createNewItem );
			this.addItemForm.on( 'submit', this.addItem );
			// Handle banners modal
			this.itemsSection.on( 'click', '.insert-banner-button', this.bannersModal );


			this.itemsSection.on( 'keyup', '.required-error', this._resetRequiredError );
			this.itemsForm.on( 'submit', this.submitForm );
		},

		// Common
		_nestable: function() {
			if ( typeof $.fn.nestable != 'undefined' ) {
				this.itemsList.nestable( {
					'expandBtnHTML': '',
					'collapseBtnHTML': ''
				} );
			}
		},
		_handleOptionsDeps: function() {
			ywcmap_items_list.itemsSection.find( '[data-deps]:not(.deps-initialized)' ).each( function() {

				let wrap = $( this ),
					deps = wrap.attr( 'data-deps' ).split( ',' ),
					values = wrap.attr( 'data-deps_value' ).split( ',' ),
					conditions = [];

				wrap.addClass( 'deps-initialized' );

				$.each( deps, function( i, dep ) {
					$( '[name="' + dep + '"]' ).on( 'change', function() {

						let value = this.value,
							check_values = '';

						// exclude radio if not checked
						if ( this.type == 'radio' && ! $( this ).is( ':checked' ) ) {
							return;
						}

						if ( this.type == 'checkbox' ) {
							value = $( this ).is( ':checked' ) ? 'yes' : 'no';
						}

						check_values = values[i] + ''; // force to string
						check_values = check_values.split( '|' );
						conditions[i] = $.inArray( value, check_values ) !== -1;

						if ( $.inArray( false, conditions ) === -1 ) {
							wrap.fadeIn();
						} else {
							wrap.hide();
						}

					} ).change();
				} );
			} );
		},
		_reInitItem: function( event, item ) {
			if ( typeof item != 'undefined' ) {
				applySelect2( item.find( 'select' ) );
				ywcmap_items_list._handleOptionsDeps();
				ywcmap_items_list._initTinyMCE( item.find( 'textarea' ).attr( 'id' ) );
			}
		},
		_addItemToList: function( id, item ) {
			// if exists replace, otherwise append
			let current = ywcmap_items_list.itemsList.find( 'li[data-id="' + id + '"]' );
			if ( current.length ) {
				current.replaceWith( item ).fadeIn();
			} else {
				ywcmap_items_list.itemsList.children( 'ol' ).append( item );
			}

			// recalculate item
			item = ywcmap_items_list.itemsList.find( 'li[data-id="' + id + '"]' );

			$( '.dd' ).nestable( 'reset' );
			$( document ).trigger( 'yith_wcmap_item_added', [item] );
		},
		_ajaxRequest: function( data, item ) {

			data.push(
				{name: "action", value: ywcmap.ajaxAction},
				{name: "security", value: ywcmap.ajaxNonce},
				{name: "context", value: "admin"},
				{name: "page", value: ywcmap.page}
			);

			return $.ajax( {
				url: ywcmap.ajaxUrl,
				data: data,
				dataType: 'json',
				type: 'POST',
				beforeSend: function() {
					item.find( '.spinner' ).addClass( 'show' );
					item.block( {
						message: null,
						overlayCSS: {
							background: '#fff no-repeat center',
							opacity: 0.5,
							cursor: 'none'
						}
					} );
				},
				complete: function() {
					item.find( '.spinner' ).removeClass( 'show' );
					item.unblock();
				}
			} );
		},
		_initTinyMCE: function( id ) {
			// get tinymce options
			let mceInit = tinyMCEPreInit.mceInit,
				mceKey = Object.keys( mceInit )[0],
				mce = mceInit[mceKey],
				// get quicktags options
				qtInit = tinyMCEPreInit.qtInit,
				qtKey = Object.keys( qtInit )[0],
				qt = mceInit[qtKey];

			// change id
			mce.selector = id;
			mce.body_class = mce.body_class.replace( mceKey, id );
			qt.id = id;

			tinyMCE.init( mce );
			tinyMCE.execCommand( 'mceRemoveEditor', true, id );
			tinyMCE.execCommand( 'mceAddEditor', true, id );

			quicktags( qt );
			QTags._buttonsInit();
		},
		_validateRequired: function( data, item ) {

			let errors = [],
				j = 0;

			$.each( data, function( i, el ) {
				let elem = item.find( '[name="' + el.name + '"]' );
				if ( elem.hasClass( 'required' ) && '' === el.value ) {
					elem.addClass( 'required-error' );
					errors[j++] = elem;
				}
			} );

			if ( ! errors.length ) {
				return true;
			}

			window.scrollTo( {
				top: errors[0].offset().top - 200,
				left: 0,
				behavior: 'smooth'
			} );

			return false;
		},
		_resetRequiredError: function( event ) {
			if ( $( this ).val() ) {
				$( this ).removeClass( 'required-error' );
			}
		},

		// Toggle options
		toggleOptions: function( event ) {
			let item = $( this ).closest( '.item' );

			item.toggleClass( 'opened' )
				.find( '.item-content' ).first().toggleClass( 'dd-nodrag' )
				.end().find( '.item-options' ).first().slideToggle();
		},

		// On-Off
		onOffRestore: function( input ) {
			// Restore checkbox
			if ( input.is( ':checked' ) ) {
				input.removeAttr( 'checked' ).removeClass( 'onoffchecked' );
			} else {
				input.prop( 'checked', true );
			}
		},
		onOffItem: function( event ) {
			event.preventDefault();

			let item = $( this ).closest( '.item' ),
				data = [
					{name: "request", value: "activate"},
					{name: "value", value: $( this ).is( ':checked' ) ? 'yes' : 'no'},
					{name: "item", value: item.data( 'id' )},
				];

			ywcmap_items_list._ajaxRequest( data, item )
				.done( ( response ) => {
					if ( ! response?.success ) {
						// Restore checkbox
						ywcmap_items_list.onOffRestore( $( this ) );
					}
				} )
				.fail( ( response ) => {
					console.log( response );
					// Restore checkbox
					ywcmap_items_list.onOffRestore( $( this ) );
				} );

		},

		// Remove
		removeItem: function() {

			let r = confirm( ywcmap.removeAlert );
			if ( true === r ) {
				let item = $( this ).closest( '.item' ),
					data = [
						{name: "request", value: "remove"},
						{name: "item", value: item.data( 'id' )},
					];

				ywcmap_items_list._ajaxRequest( data, item )
					.done( ( response ) => {
						if ( response?.success ) {
							let group = item.find( 'ol.items' );
							// if group move child
							if ( group.length ) {
								let child_items = group.find( 'li.item' );
								// move!
								item.after( child_items );
							}
							// then remove field
							item.remove();
						}
					} )
					.fail( ( response ) => {
						console.log( response );
					} );
			} else {
				return false;
			}
		},

		// Reset
		resetItem: function( event ) {
			event.preventDefault();

			let item = $( this ).closest( '.item' ),
				data = [
					{name: "request", value: "reset"},
					{name: "item", value: item.data( 'id' )},
					{name: "type", value: item.data( 'type' )}
				];

			ywcmap_items_list._ajaxRequest( data, item )
				.done( ( response ) => {
					if ( response?.success ) {
						ywcmap_items_list._addItemToList( response.data.id, response.data.html );
					}
				} )
				.fail( ( response ) => {
					console.log( response );
				} );
		},

		// Save
		saveItem: function( event ) {
			event.preventDefault();

			let item = $( this ).closest( '.item' ),
				itemID = item.data( 'id' ),
				data;

			// before serialize the data, switch all editor to text to be sure to get all changes
			$( '.wp-editor-tabs' ).find( 'button.switch-html' ).click();

			data = ywcmap_items_list.itemsForm.serializeArray().filter( function( el ) {
				return el.name.replace( /(\[.*\])/, '' ) === ('yith_wcmap_endpoint_' + itemID);
			} );

			if ( ! data.length || ! ywcmap_items_list._validateRequired( data, item ) ) {
				return false;
			}

			data.push(
				{name: "request", value: "save"},
				{name: "item", value: itemID},
				{name: "type", value: item.data( 'type' )}
			);

			ywcmap_items_list._ajaxRequest( data, item )
				.done( ( response ) => {
					if ( response?.success ) {
						ywcmap_items_list._addItemToList( response.data.id, response.data.html );
					}
				} )
				.fail( ( response ) => {
					console.log( response );
				} );

		},
		submitForm: function() {
			if ( typeof $.fn.nestable == 'undefined' ) {
				return;
			}

			var j = $( '.dd' ).nestable( 'serialize' ),
				v = JSON.stringify( j );

			$( 'input.items-order' ).val( v );
		},

		// Create
		createNewItem: function( event ) {
			event.stopPropagation();

			let currentLabel = $( this ).text(),
				targetId = $( this ).data( 'target' ),
				template = wp.template( 'new-' + targetId + '-item' );

			if ( $( this ).siblings().hasClass( 'closed' ) ) {
				return false;
			}

			if ( $( this ).hasClass( 'closed' ) ) {
				$( this )
					.text( $( this ).data( 'label' ) )
					.data( 'label', currentLabel )
					.removeClass( 'closed' )
					.siblings( 'button' )
					.show();

				ywcmap_items_list.addItemForm.hide().find( '.new-item-template' ).remove();
				ywcmap_items_list.addItemForm.find( 'input[name="type"]' ).val( '' );
			} else {
				$( this )
					.text( $( this ).data( 'label' ) )
					.data( 'label', currentLabel )
					.addClass( 'closed' )
					.siblings( 'button' )
					.hide();

				ywcmap_items_list.addItemForm.prepend( template( {} ) ).fadeIn();
				ywcmap_items_list.addItemForm.find( 'input[name="type"]' ).val( targetId );

				let newItem = ywcmap_items_list.addItemForm.find( '.new-item-template' );
				$( document ).trigger( 'yith_wcmap_item_added', [newItem] );
			}
		},
		addItem: function( event ) {
			event.preventDefault();

			let data;

			// before serialize the data, switch all editor to text to be sure to get all changes
			$( '.wp-editor-tabs' ).find( 'button.switch-html' ).click();

			data = ywcmap_items_list.addItemForm.serializeArray();
			if ( ! ywcmap_items_list._validateRequired( data, $( this ) ) ) {
				return false;
			}

			data.push( {name: "request", value: "add"} );
			ywcmap_items_list._ajaxRequest( data, $( this ) )
				.done( ( response ) => {
					if ( response?.success ) {
						$( '.create-new-item.closed' ).click();
						ywcmap_items_list._addItemToList( response.data.id, response.data.html );
					}
				} )
				.fail( ( response ) => {
					console.log( response );
				} );
		},

		// Banners Modal
		bannersModal: function( event ) {
			event.preventDefault();

			let target = $( this ).data( 'editor' ),
				modal = $( '#banners-modal' );

			if ( modal.length && typeof $.fn.dialog != 'undefined' ) {
				modal.dialog( {
					resizable: false,
					modal: true,
					dialogClass: 'yith-wcmap-banners-modal yith-plugin-ui',
					buttons: {
						'Add': function() {
							let selected = $( this ).find( '.banners' ).val();
							if ( selected.length && typeof tinymce != 'undefined' ) {
								let textarea = $( '#' + target ),
									editor = tinymce.get( '' + target ),
									shortcode = ywcmap.bannerShortcode.replace( '{{banners}}', selected.join( ',' ) );

								if ( typeof editor != null ) {
									editor.execCommand( 'mceInsertContent', false, shortcode );
								}

								textarea.val( textarea.val() + shortcode ).change();
							}

							modal.dialog( 'close' );
						},
					},
					open: function( event, ui ) {
						applySelect2( $( this ).find( 'select' ) );
					},
					close: function( event, ui ) {
						$( this ).find( '.banners' ).val( '' );
						modal.dialog( 'destroy' );
					}
				} );
			}
		}
	};

	ywcmap_items_list.init();
} );
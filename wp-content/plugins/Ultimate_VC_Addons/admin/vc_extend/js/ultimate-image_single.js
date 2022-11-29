/**
 * Callback function for the 'click' event of the 'Set Footer Image'
 * anchor in its meta box.
 *
 * Displays the media uploader for selecting an image.
 *
 * @since 0.1.0
 */

( function ( $, window, undefined ) {
	/* 	= Image Up loader
	 *-------------------------------------------------*/
	const pn = 'ULT_Image_Single',
		document = window.document,
		defaults = {
			add: '.ult_add_image',
			remove: '#remove-thumbnail',
		};

	function ult( element, options ) {
		this.element = element;
		this.options = $.extend( {}, defaults, options );
		this._defaults = defaults;
		this._name = pn;
		this.init();
	}

	ult.prototype.save_and_show_image = function (
		id,
		url,
		caption,
		alt,
		title,
		description
	) {
		const $t = $( this.element );

		$t.find( '.ult_selected_image_list .inner' )
			.children( 'img' )
			.attr( 'src', url )
			.attr( 'alt', caption )
			.show()
			.parent()
			.removeClass( 'hidden' );
		let string = '';
		string += id != '' ? 'id^' + id + '|' : '';
		string += url != '' ? 'url^' + url + '|' : '';
		string += caption != '' ? 'caption^' + caption + '|' : '';
		string += alt != '' ? 'alt^' + alt + '|' : '';
		string += title != '' ? 'title^' + title + '|' : '';
		string += description != '' ? 'description^' + description + '|' : '';

		if ( string.substr( -1 ) === '|' ) {
			string = string.substr( 0, string.length - 1 );
		}

		$t.find( '.ult-image_single-value' ).val( string );
		//	show image
		$t.find( '.ult_selected_image' ).show();
	};

	/* = {start} wp media uploader
	 *------------------------------------------------------------------------*/
	ult.prototype.renderMediaUploader = function () {
		'use strict';

		let fn, image_data, json;
		const self = this;
		if ( undefined !== fn ) {
			fn.open();
			return;
		}

		fn = wp.media( {
			title: 'Select or Upload Image',
			button: {
				text: 'Use this image',
			},
			library: { type: 'image' },
			multiple: false, // Set to true to allow multiple files to be selected
		} );

		//	Insert from {SELECT}
		fn.on( 'select', function () {
			// console.log(wp.media.string);

			// Read the JSON data returned from the Media Uploader
			json = fn.state().get( 'selection' ).first().toJSON();

			if ( 0 > $.trim( json.url.length ) ) {
				return;
			}

			//	{save} image - id & src - for {SELECT}
			const id = json.id || null;
			const url = json.url || null;
			const caption = json.caption || null;
			const alt = json.alt || null;
			const title = json.title || null;
			const description = json.description || null;
			self.save_and_show_image(
				id,
				url,
				caption,
				alt,
				title,
				description
			);
		} );

		//	Insert from {URL}
		fn.state( 'embed' ).on( 'select', function () {
			const state = fn.state(),
				type = state.get( 'type' ),
				embed = state.props.toJSON();

			//	{save} image - id & src - for {INSERT FROM URL}
			const id = null;
			const caption = embed.caption || null;
			const url = embed.url || null;
			const alt = embed.alt || null;
			const title = embed.title || null;
			const description = embed.description || null;
			self.save_and_show_image(
				id,
				url,
				caption,
				alt,
				title,
				description
			);
		} );

		// Now display the actual fn
		fn.open();
	};

	ult.prototype.resetUploadForm = function () {
		const $t = $( this.element );
		$t.find( '.ult_selected_image' ).hide();
		//	{Remove} image - ID & SRC
		$t.find( '.ult-image_single-value' ).val( '' );
		//$t.find('.ult-image_single-value').val('null|null');
	};

	ult.prototype.renderFeaturedImage = function () {
		const $t = $( this.element );

		const v = $t.find( '.ult-image_single-value' ).val();
		if ( '' !== $.trim( v ) ) {
			const tm = v.split( '|' );

			let id, url, title, alt, description, caption, old_id, old_url;
			old_id = tm[ 0 ];
			old_url = tm[ 1 ];

			jQuery.each( tm, function ( i, tmv ) {
				if ( stripos( tmv, '^' ) !== false ) {
					const tmva = tmv.split( '|' );
					if (
						Object.prototype.toString.call( tmva ) ==
						'[object Array]'
					) {
						jQuery.each( tmva, function ( j, tmvav ) {
							const tmvav_array = tmvav.split( '^' );
							eval(
								tmvav_array[ 0 ] +
									' = "' +
									tmvav_array[ 1 ] +
									'"'
							);
						} );
					}
				} else {
					id = old_id;
					url = old_url;
				}
			} );

			// var url = url.split('|');
			if ( typeof url !== 'undefined' ) {
				if ( url.indexOf( 'url:' ) != -1 ) {
					url = url.split( 'url:' ).pop();
				}
				if ( url.indexOf( 'url^' ) != -1 ) {
					url = url.split( 'url^' ).pop();
				}
			}

			//	Saved Image - ID
			if ( typeof id !== 'undefined' && id != 'null' ) {
				if ( ! url ) {
					// set process
					$t.find( '.spinner.ult_img_single_spinner' ).css(
						'visibility',
						'visible'
					);
					const data = {
						action: 'ult_get_attachment_url',
						attach_id: parseInt( id ),
						security: uavc.ult_get_attachment_url,
					};
					$.post( ajaxurl, data, function ( img_url ) {
						$t.find( '.spinner.ult_img_single_spinner' ).css(
							'visibility',
							'hidden'
						);
						$t.find( '.ult_selected_image_list .inner' )
							.children( 'img' )
							.attr( 'src', img_url );
					} );
				}
			}

			//	Saved Image - SRC
			if ( typeof url !== 'undefined' && url != 'null' ) {
				$t.find( '.ult_selected_image_list .inner' )
					.children( 'img' )
					.attr( 'src', url );
				$t.find( '.ult_selected_image' ).show();
			} else {
				$t.find( '.ult_selected_image' ).hide();
			}
		} else {
			$t.find( '.ult_selected_image' ).hide();
			//	{Default} image - ID & SRC
			$t.find( '.ult-image_single-value' ).val( '' );
			//$t.find('.ult-image_single-value').val('null|null');
		}
	};
	/* = {end} wp media uploader
	 *------------------------------------------------------------------------*/

	ult.prototype.init = function () {
		const self = this;
		const i = self._defaults;
		const $t = $( self.element );

		self.renderFeaturedImage();
		//	add image
		$t.find( i.add ).click( function ( event ) {
			// Stop the anchor's default behavior
			event.preventDefault();
			self.renderMediaUploader();
		} );

		// remove image
		$t.find( i.remove ).click( function ( event ) {
			event.preventDefault();
			self.resetUploadForm();
		} );
	};

	$.fn[ pn ] = function ( options ) {
		return this.each( function () {
			if ( ! $.data( this, 'plugin_' + pn ) ) {
				$.data( this, 'plugin_' + pn, new ult( this, options ) );
			}
		} );
	};

	//	initial call
	$( document ).ready( function () {
		$( '.ult-image_single' ).ULT_Image_Single();
	} );
} )( jQuery, window );

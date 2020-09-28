/* global vc, YoastSEO, _, jQuery */
(function ( $ ) {
	'use strict';
	var imageEventString, vcYoast, relevantData, eventsList;

	relevantData = {};
	eventsList = [
		'sync',
		'add',
		'update'
	];
	var contentModification = _.memoize( function ( data ) {
		data = _.reduce( relevantData, function ( memo, value, key ) {
			if ( value.html ) {
				memo = memo.replace( '"' + value.text + '"', value.html );
			}
			if ( value.image && value.param ) {
				var i, imagesString = '', attachment;
				for ( i = 0; value.image.length > i; i ++ ) {
					attachment = window.wp.media.model.Attachment.get( value.image[ i ] );
					if ( attachment.get( 'url' ) ) {
						imagesString += '<img src=\'' + attachment.get( 'url' ) + '\' alt=\'' + (attachment.get( 'alt' ) || attachment.get( 'caption' ) || attachment.get( 'title' )) + '\'>';
					}
				}
				memo += imagesString;
			}
			return memo;
		}, data );

		return data;
	} );

	function getImageEventString( e ) {
		return ' shortcodes:' + e + ':param:type:attach_image' + ' shortcodes:' + e + ':param:type:attach_images';
	}

	// add relevant data for images
	imageEventString = _.reduce( eventsList, function ( memo, e ) {
		return memo + getImageEventString( e );
	}, '' );
	vc.events.on( imageEventString, function ( model, param, settings ) {
		if ( param && param.length > 0 ) {
			var ids = param.split( /\s*,\s*/ );
			_.each( ids, function ( id ) {
				var attachment = window.wp.media.model.Attachment.get( id );
				if ( !attachment.get( 'url' ) ) {
					attachment.once( 'sync', function () {
						if ( window.YoastSEO ) {
							YoastSEO.app.pluginReloaded( 'wpbVendorYoast' );
						}
					} );
					attachment.fetch();
				}
			} );
			relevantData[ model.get( 'id' ) + settings.param_name ] = {
				image: ids,
				paramName: settings.param_name,
				param: param
			};
		}
	} );
	vc.events.on( getImageEventString( 'destroy' ), function ( model, param, settings ) {
		delete relevantData[ model.get( 'id' ) + settings.param_name ];
	} );
	// Add relevant data to headings
	vc.events.on( 'shortcodes:vc_custom_heading', function ( model, event ) {
		var params, tagSearch;
		params = model.get( 'params' );
		params = _.extend( {}, vc.getDefaults( model.get( 'shortcode' ) ), params );

		if ( 'destroy' === event ) {
			delete relevantData[ model.get( 'id' ) ];
		} else if ( params.text && params.font_container ) {
			tagSearch = params.font_container.match( /tag:([^\|]+)/ );
			if ( tagSearch[ 1 ] ) {
				relevantData[ model.get( 'id' ) ] = {
					html: '<' + tagSearch[ 1 ] + '>' + params.text + '</' + tagSearch[ 1 ] + '>',
					text: params.text
				};
			}
		}
	} );

	$( window ).on( 'YoastSEO:ready', function () {
		var VcVendorYoast = function () {
			// init
			YoastSEO.app.registerPlugin( 'wpbVendorYoast', { status: 'ready' } );
			YoastSEO.app.pluginReady( 'wpbVendorYoast' );
			YoastSEO.app.registerModification( 'content', contentModification, 'wpbVendorYoast', 5 );
		};

		vcYoast = new VcVendorYoast();
	} );
	$( document ).ready( function () {
		if ( window.wp && wp.hooks && wp.hooks.addFilter ) {
			wp.hooks.addFilter( 'rank_math_content', 'wpbRankMath', contentModification );
		}
	} );
})( window.jQuery );

/* global wp_mail_smtp */
'use strict';

/**
 * WP Mail SMTP select2 enhancements.
 *
 * @since 2.9.0
 */
( function( document, window, $ ) {

	// Default translations.
	$.fn.select2.defaults.set( 'language', {
		errorLoading: function() {

			return wp_mail_smtp.select2.i18n.error_loading;
		},
		loadingMore : function() {

			return wp_mail_smtp.select2.i18n.loading_more;
		},
		noResults   : function() {

			return wp_mail_smtp.select2.i18n.no_results;
		},
		searching   : function() {

			return wp_mail_smtp.select2.i18n.searching;
		}
	} );

	// Cache ajax loaded data.
	$.fn.select2.amd.define(
		'select2/data/cacheableAjax',
		[ 'select2/data/ajax', 'select2/utils' ],
		function( AjaxAdapter, Utils ) {

			/**
			 * Cacheable ajax adapter.
			 *
			 * @since 2.9.0
			 *
			 * @param {object} $element Select jQuery object.
			 * @param {object} options Options.
			 */
			function CacheableAjaxAdapter( $element, options ) {

				CacheableAjaxAdapter.__super__.constructor.call( this, $element, options );
			}

			Utils.Extend( CacheableAjaxAdapter, AjaxAdapter ); // eslint-disable-line new-cap

			CacheableAjaxAdapter.prototype.query = function( params, callback ) {

				var self = this,
					term = params.term !== undefined && params.term !== '' ? params.term : 'initial',
					cacheDataSource = self.options.get( 'cacheDataSource' );

				if ( params._type === 'query' && cacheDataSource[ term ] !== undefined ) {
					callback( cacheDataSource[ term ] );
				} else {
					var ajaxAdapter = new AjaxAdapter( this.$element, this.options );

					ajaxAdapter.query( params, function( data ) {

						var cacheResults = cacheDataSource[ term ] !== undefined ? cacheDataSource[ term ].results : [];
						cacheResults = cacheResults.concat( data.results );
						cacheDataSource[ term ] = $.extend( {}, data, {results: cacheResults} );
						callback( data );
					} );
				}
			};

			return CacheableAjaxAdapter;
		}
	);

}( document, window, jQuery ) );

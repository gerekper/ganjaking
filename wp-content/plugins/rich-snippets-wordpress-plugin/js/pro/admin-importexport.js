(
		function () {
			'use strict';

			var importexport = function () {
				this.$code   = null;
				this.$import = null;
				this.$export = null;
				this.uniqid  = 0;

				this.init = function () {
					var self     = this;
					self.$code   = jQuery( '.wpb-rs-importexport-code' );
					self.$import = jQuery( '.wpb-rs-import' );
					self.$export = jQuery( '.wpb-rs-export' );

					self.$code.on( 'change keyup paste', function () {
						var code = jQuery( this ).val();

						if ( '' === code ) {
							self.$import.addClass( 'button-disabled' );
						} else {
							self.$import.removeClass( 'button-disabled' );
						}
					} );

					self.$import.on( 'click', function ( e ) {
						e.preventDefault();

						if ( jQuery( this ).hasClass( 'button-disabled' ) ) {
							return;
						}

						var code = self.$code.val();
						self.import( code );
					} );

					self.$export.on( 'click', function ( e ) {
						e.preventDefault();
						self.export();
					} );
				};

				this.export = function () {
					var self = this;

					/* fetch closest snippet */
					var $snippet = this.$export.closest( '.wpb-rs-schema-main' );

					/* If there is nothing, maybe we're on the Global Snippets screen */
					if ( $snippet.length <= 0 ) {
						$snippet = jQuery( '.wpb-rs-schema-main' );
					}

					if ( $snippet.length <= 0 ) {
						rich_snippets_errors.print_error( WPB_RS_IEPORT.i18n.nothing_to_export );
						return;
					}

					var snippet_form_elements = $snippet.find( ':input' ).serializeArray();
					var snippet_form_data     = {};

					jQuery.each( snippet_form_elements, function ( key, form_element ) {
						snippet_form_data[ form_element.name ] = form_element.value;
					} );

					var $position_metabox  = jQuery( '#wp-rs-mb-position' );
					var position_form_data = {};

					if ( $position_metabox.length > 0 ) {
						position_form_data = window.rich_snippets.position.fetch_all_fields();
					}

					jQuery.ajax( {
						'url'       : WPB_RS_IEPORT.rest_url + '/schema/export/',
						'dataType'  : 'json',
						'beforeSend': function ( xhr ) {
							xhr.setRequestHeader( 'X-WP-Nonce', WPB_RS_IEPORT.nonce );
							self.$export.addClass( 'installing' );
						},
						'data'      : Object.assign( snippet_form_data, position_form_data ),
						'method'    : 'POST'
					} ).done( function ( snippets ) {

						self.$code.val( '' );

						jQuery.each( snippets, function ( k, snippet ) {

							try {
								var snippet_code = JSON.stringify( snippet, null, 4 );
							} catch ( error ) {
								rich_snippets_errors.print_error( WPB_RS_IEPORT.i18n.invalid_json );
								return;
							}

							self.$code.val( self.$code.val() + snippet_code );
							self.$code.trigger( 'keyup' );
						} );

					} ).fail( function ( xhr, text_status, error ) {
						rich_snippets_errors.ajax_error_handler( xhr, text_status, error );
					} ).always( function () {
						self.$export.removeClass( 'installing' );
					} );
				};

				this.import = function ( code ) {
					var main_snippet,
					    form_data,
					    self = this;

					if ( code.length <= 0 ) {
						rich_snippets_errors.print_error( WPB_RS_IEPORT.i18n.enter_content );
						return;
					}

					try {
						var json = JSON.parse( code );
					} catch ( error ) {
						rich_snippets_errors.print_error( WPB_RS_IEPORT.i18n.enter_content );
						return;
					}

					if ( !confirm( WPB_RS_IEPORT.i18n.are_you_sure_import ) ) {
						return;
					}

					if ( json.hasOwnProperty( '@ruleset' ) ) {
						var ruleset = json[ '@ruleset' ];
						delete json[ '@ruleset' ];
						window.rich_snippets.position.remove_all_rules();

						var i = 0;
						jQuery.each( ruleset, function ( rg_key, rulegroup ) {
							if ( i > 0 ) {
								window.rich_snippets.position.add_rule_subset();
							}
							jQuery.each( rulegroup, function ( r_key, rule ) {
								window.rich_snippets.position.add_rule( rule );
							} );
							i++;
						} );
					}

					var callback = function ( data ) {

						self.$import.removeClass( 'installing' );

						jQuery.each( data.forms, function ( key, form ) {
							var $form = jQuery( form );

							/* Find jQuery object */
							var $snippet = window.rich_snippets.snippets[ key ].$main_snippet;

							/* If on Global Snippets screen: */
							if ( $snippet.hasClass( 'inside' ) ) {
								$form.find( '> button' ).remove();
								$form.closest( '.wpb-rs-single-snippet' ).addClass( 'inside' ).removeClass( 'wpb-rs-single-snippet' );
							}

							$snippet.replaceWith( $form );

							window.rich_snippets.snippets[ key ] = new window.rich_snippets.snippet( $form );
							window.rich_snippets.snippets[ key ].init();

						} );

					};

					var always = function () {
						self.$import.removeClass( 'installing' );
					};

					this.$import.addClass( 'installing' );

					if ( this.is_json_ld( json ) ) {
						main_snippet = this.fetch_from_json_ld( json, {} );
						form_data    = main_snippet.form_data;
						rich_snippets.posts.load_snippets( callback, always, form_data );
					} else {
						rich_snippets.posts.load_snippets( callback, always, {}, JSON.stringify( json ) );
					}
				};

				this.is_json_ld = function ( code ) {
					return !(code.hasOwnProperty( '_is_export' ) && code._is_export);
				};

				this.trailingslashit = function ( url ) {
					if ( '/' === url.slice( -1 ) ) {
						return url;
					}

					return url + '/';
				};


				this.fetch_from_json_ld = function ( code, form_data ) {
					var self       = this;
					var snippet_id = this.get_uniqid( 'snip-' );
					var type       = code.hasOwnProperty( '@type' ) ? code[ '@type' ] : '';
					var context    = code.hasOwnProperty( '@context' ) ? code[ '@context' ] : 'http://schema.org';

					if ( type.length <= 0 || context.length <= 0 ) {
						return {
							'snippet_id': snippet_id,
							'form_data' : form_data
						};
					}

					context = this.trailingslashit( context );

					form_data[ snippet_id ] = {
						'id'        : context + type,
						'loop'      : '',
						'properties': {}
					};

					jQuery.each( code, function ( key, value ) {

						if ( '@type' === key || '@context' === key ) {
							return;
						}

						var prop_id = self.get_uniqid( '' );

						var o = {
							'id'                  : context + key,
							'overridable'         : 0,
							'overridable_multiple': 0,
							'ref'                 : '',
							'subfield_select'     : '',
							'textfield'           : ''
						};

						if ( value.hasOwnProperty( '@type' ) ) {
							var sub_snippet = self.fetch_from_json_ld( value, form_data );

							/* passing by reference does not work in JS, so we do this: */
							form_data = sub_snippet.form_data;

							o.ref = sub_snippet.snippet_id;
						} else {
							o.textfield       = value;
							o.subfield_select = 'textfield';
						}

						form_data[ snippet_id ][ 'properties' ][ prop_id ] = o;

					} );

					return {
						'snippet_id': snippet_id,
						'form_data' : form_data
					};

				};


				this.get_uniqid = function ( pre ) {
					this.uniqid++;

					return pre + this.uniqid;
				};

			};

			jQuery( document ).ready( function () {
				window.rich_snippets.importexport = new importexport();
				window.rich_snippets.importexport.init();
			} );
		}
)();

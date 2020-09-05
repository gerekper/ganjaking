(
		function () {
			'use strict';

			var position = function () {
				this.ruleset_table = null;
				this.new_rule_row  = null;
				this.group_break   = null;

				this.init = function () {
					var self           = this;
					self.ruleset_table = jQuery( '.wpb-rs-ruleset' );
					self.new_rule_row  = self.ruleset_table.find( 'thead tr:first-child' );
					self.group_break   = self.ruleset_table.find( 'thead .wpb-rs-rule-group-break' );

					/* Show at least one rule so that the user can start editing */
					if ( self.ruleset_table.find( 'tbody tr' ).length <= 0 ) {
						self.add_rule();
					}

					/* Events */
					jQuery( document ).on( 'click', '.wpb-rs-rulegroup-add', function ( e ) {
						e.preventDefault();
						self.add_rule_subset( { 'add_rule': true } );
					} );

					jQuery( document ).on( 'click', '.wpb-rs-rule-add', function ( e ) {
						e.preventDefault();
						var append_obj = jQuery( this ).closest( 'tr' );
						self.add_rule( { 'append_obj': append_obj } );
					} );

					jQuery( document ).on( 'click', '.wpb-rs-rule-remove', function ( e ) {
						e.preventDefault();
						self.remove_rule( jQuery( this ).closest( 'tr' ) );
					} );

					jQuery( document ).on( 'click', '.wpb-rs-rulegroup-remove', function ( e ) {
						e.preventDefault();
						self.remove_group( jQuery( this ).closest( 'tr' ) );
					} );

					jQuery( document ).on( 'submit', '#post', function () {
						self.update_rule_select_names( self.ruleset_table );
					} );

					jQuery( document ).on( 'change', '.wpb-rs-param select', function () {
						self.load_value_select( jQuery( this ) );
					} );

					jQuery( '.wpb-rs-value select' ).each( function () {
						var make_select2 = jQuery( this ).attr( 'data-make_select2' );

						if ( 1 === parseInt( make_select2 ) ) {
							self.make_select2( jQuery( this ) );
						}
					} );
				};

				this.remove_all_rules = function () {
					this.ruleset_table.find( 'tbody tr' ).remove();
				};

				this.add_rule_subset = function ( args ) {
					args = args || {};

					this.ruleset_table.find( 'tbody' ).append( this.group_break.clone() );

					if ( args.hasOwnProperty( 'add_rule' ) && args.add_rule ) {
						this.add_rule();
					}
				};

				this.add_rule = function ( args ) {
					args = args || {};

					var row = this.new_rule_row.clone();

					if ( args.hasOwnProperty( 'param' ) ) {
						row.find( '.wpb-rs-param select' ).val( args.param );
					}

					if ( args.hasOwnProperty( 'operator' ) ) {
						row.find( '.wpb-rs-operator select' ).val( args.operator );
					}

					if ( args.hasOwnProperty( 'value' ) ) {
						row.find( '.wpb-rs-value select' ).val( args.value );
						row.find( '.wpb-rs-param select' ).attr( 'data-preselect', args.value );
					}

					if ( args.hasOwnProperty( 'append_obj' ) ) {
						/* Create a new row in between */
						args.append_obj.after( row );
					} else {
						/* Crate a new row at the bottom */
						this.ruleset_table.find( 'tbody' ).append( row );
					}

					if ( args.hasOwnProperty( 'param' ) ) {
						this.load_value_select( row.find( '.wpb-rs-param select' ) );
					}
				};

				this.fetch_all_fields = function () {
					var data = {};

					var groups_no = 0;
					var row_no    = 0;

					this.ruleset_table.find( 'tbody' ).find( '.wpb-rs-rule, .wpb-rs-rule-group-break' ).each( function () {
						if ( jQuery( this ).hasClass( 'wpb-rs-rule-group-break' ) ) {
							groups_no++;
						} else {
							jQuery( this ).find( '.wpb-rs-select, select' ).each( function () {
								var name = jQuery( this ).attr( 'name' ).replace( '[%rule_group%][%rule%]', '[' + groups_no + '][' + row_no + ']' );
								if ( jQuery( this ).hasClass( 'wpb-rs-select' ) ) {
									data[ name ] = jQuery( this ).data( 'value' );
								} else {
									data[ name ] = jQuery( this ).val();
								}
							} );
							row_no++;
						}
					} );

					return data;
				};

				this.update_rule_select_names = function ( $form ) {

					/* Make sure all select2 elements have the right value */
					$form.find( 'input.wpb-rs-select' ).each( function () {
						var value = jQuery( this ).data( 'value' );
						if ( '' === value ) {
							return;
						}

						jQuery( this ).val( value );
					} );

					/* Before the form gets submitted, we update the select names properly. */
					var groups_no = 0;
					var row_no    = 0;

					$form.find( 'tbody' ).find( '.wpb-rs-rule, .wpb-rs-rule-group-break' ).each( function () {
						if ( jQuery( this ).hasClass( 'wpb-rs-rule-group-break' ) ) {
							groups_no++;
						} else {
							jQuery( this ).find( '.wpb-rs-select, select' ).each( function () {
								var name = jQuery( this ).attr( 'name' ).replace( '[%rule_group%][%rule%]', '[' + groups_no + '][' + row_no + ']' );
								jQuery( this ).attr( 'name', name );
							} );
							row_no++;
						}
					} );

					/* submit the form */
					return true;
				};

				this.remove_rule = function ( row ) {
					if ( this.get_group_rules_count( row ) <= 1 ) {
						/* If this is the last element of the group, remove the whole group*/
						this.remove_group( row );
					} else {
						row.remove();
					}
				};

				this.remove_group = function ( row ) {

					var group,
					    rules;

					if ( row.hasClass( 'wpb-rs-rule-group-break' ) ) {
						group = row;
						rules = group.nextUntil( '.wpb-rs-rule-group-break' );
					} else {
						rules = this.get_group_rules( row );
						group = rules.first().prev().remove();
					}

					group.remove();
					rules.remove();
				};

				this.get_group_rules_count = function ( row ) {
					return this.get_group_rules( row ).length;
				};

				this.get_group_rules = function ( row ) {
					return row.prevUntil( '.wpb-rs-rule-group-break' ).addBack().nextUntil( '.wpb-rs-rule-group-break' ).addBack();
				};

				this.load_value_select = function ( $triggered_obj ) {
					var self      = this;
					var rule_td   = $triggered_obj.closest( '.wpb-rs-rule' );
					var preselect = rule_td.find( '.wpb-rs-param select' ).attr( 'data-preselect' );

					jQuery.ajax( {
						'url'       : WPB_RS_POS.rest_url + '/positions/value-select/',
						'dataType'  : 'json',
						'beforeSend': function ( xhr ) {
							xhr.setRequestHeader( 'X-WP-Nonce', WPB_RS_POS.nonce );
							var rule_value_td = rule_td.find( '.wpb-rs-value' );
							rule_value_td.html( '<div class="wpb-rs-ps-loader updating-message"><p class="updating-message"></p></div>' );
						},
						'data'      : {
							'param': $triggered_obj.val()
						},
						'method'    : 'GET'
					} ).done( function ( data ) {
						rule_td.find( '.wpb-rs-value' ).html( data.select_html );

						var select = rule_td.find( '.wpb-rs-value select' );

						var make_select2 = select.attr( 'data-make_select2' );

						if ( 1 === parseInt( make_select2 ) ) {

							if ( preselect ) {
								select.append( '<option selected="selected" value="' + preselect + '">Post ID: ' + preselect + '</option>' );
							}

							self.make_select2( select );
						} else {
							select.val( preselect );
						}
					} ).fail( function ( xhr, text_status, error ) {
						rich_snippets_errors.ajax_error_handler( xhr, text_status, error );
					} ).always( function () {
						rule_td.find( '.wpb-rs-ps-loader' ).remove();
					} );
				};

				this.make_select2 = function ( $obj ) {

					var $select2 = jQuery( '<input type="text" autocomplete="off" value="" />' );
					$select2.val( $obj.children( 'option' ).filter( ':selected' ).text() );
					$select2.data( 'value', $obj.val() );
					$select2.prop( 'id', $obj.prop( 'id' ) );
					$select2.prop( 'class', $obj.prop( 'class' ) );
					$select2.addClass( 'wpb-rs-select' );
					$select2.prop( 'name', $obj.prop( 'name' ) );
					$obj.replaceWith( $select2 );

					var param = $obj.attr( 'data-param' );

					rich_snippets.snippets[ 0 ].select2( $select2, {
						'ajax': {
							'url'       : WPB_RS_POS.rest_url + '/positions/value-possibilities',
							'dataType'  : 'json',
							'beforeSend': function ( xhr ) {
								xhr.setRequestHeader( 'X-WP-Nonce', WPB_RS_POS.nonce );
								rich_snippets.snippets[ 0 ].on_ajax_before_send();
							},
							'complete'  : function () {
								rich_snippets.snippets[ 0 ].on_ajax_complete();
							},
							'data'      : function ( params ) {
								return {
									'q': params.term, 'page': params.page, 'param': param
								};
							},
							'method'    : 'GET',
							'cache'     : true,
							'error'     : function ( xhr, text_status, error ) {
								rich_snippets_errors.ajax_error_handler( xhr, text_status, error );
							},
							'success'   : function ( response ) {
								var items = {};
								jQuery.each( response.values, function ( k, v ) {
									items[ v ] = k;
								} );
								rich_snippets.snippets[ 0 ].select2_update_list( $select2, response.values );
							}
						}
					} );

				};
			};

			jQuery( document ).ready( function () {
				rich_snippets.position = new position();
				rich_snippets.position.init();
			} );
		}
)();

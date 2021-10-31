( function ( $ ) {
	$( document ).ready( function () {
		if ( typeof window.VcRowView != 'undefined' ) {
			window.VcRowView.prototype.convertRowColumns = function ( layout ) {
				var Shortcodes = vc.shortcodes;
				var layout_split = layout.toString().split( /_/ )
					, columns = Shortcodes.where( {
						parent_id: this.model.id
					} )
					, new_columns = []
					, new_layout = []
					, new_width = "";
				return _.each( layout_split, function ( new_column_params, i ) {
					var new_column, new_column_params;
					if ( new_column_params != 'flex1' && new_column_params != 'flexauto' ) {
						new_column_params = _.map( new_column_params.toString().split( "" ), function ( v, i ) {
							return parseInt( v, 10 );
						} );
						new_width = 3 < new_column_params.length ? new_column_params[ 0 ] + "" + new_column_params[ 1 ] + "/" + new_column_params[ 2 ] + new_column_params[ 3 ] : 2 < new_column_params.length ? new_column_params[ 0 ] + "/" + new_column_params[ 1 ] + new_column_params[ 2 ] : new_column_params[ 0 ] + "/" + new_column_params[ 1 ];
					}
					else {
						new_width = new_column_params.slice( 0, 4 ) + '-' + new_column_params.slice( 4 );
						new_column_params = [ new_column_params.slice( 0, 4 ), new_column_params.slice( 4 ) ];
					}
					new_layout.push( new_width ),
						new_column_params = _.extend( _.isUndefined( columns[ i ] ) ? {} : columns[ i ].get( "params" ), {
							width: new_width
						} ),
						vc.storage.lock(),
						new_column = Shortcodes.create( {
							shortcode: this.getChildTag(),
							params: new_column_params,
							parent_id: this.model.id
						} ),
						_.isObject( columns[ i ] ) && _.each( Shortcodes.where( {
							parent_id: columns[ i ].id
						} ), function ( shortcode ) {
							vc.storage.lock(),
								shortcode.save( {
									parent_id: new_column.id
								} ),
								vc.storage.lock(),
								shortcode.trigger( "change_parent_id" )
						} ),
						new_columns.push( new_column )
				}, this ),
					layout_split.length < columns.length && _.each( columns.slice( layout_split.length ), function ( column ) {
						_.each( Shortcodes.where( {
							parent_id: column.id
						} ), function ( shortcode ) {
							vc.storage.lock(),
								shortcode.save( {
									parent_id: _.last( new_columns ).id
								} ),
								vc.storage.lock(),
								shortcode.trigger( "change_parent_id" )
						} )
					} ),
					_.each( columns, function ( shortcode ) {
						vc.storage.lock(),
							shortcode.destroy()
					}, this ),
					this.model.save(),
					this.setActiveLayoutButton( "" + layout ),
					new_layout
			}
		}
		if ( typeof window.InlineShortcodeView_vc_row != 'undefined' ) {
			window.InlineShortcodeView_vc_row.prototype.convertToWidthsArray = function ( string ) {
				return _.map( string.split( /_/ ), function ( c ) {
					if ( c != 'flex1' && c != 'flexauto' ) {
						var w = c.split( "" );
						return w.splice( Math.floor( c.length / 2 ), 0, "/" ),
							w.join( "" )
					}
					else {
						return c.slice( 0, 4 ) + '/' + c.slice( 4 );
					}
				} )
			}
		}
		if ( typeof window.InlineShortcodeView_vc_column != 'undefined' ) {
			window.InlineShortcodeView_vc_column.prototype.setColumnClasses = function () {

				var offset = this.getParam( "offset" ) || ""
					, width = this.getParam( "width" ) || "1/1"
					, $content = this.$el.find( "> .wpb_column" );
				if ( width.indexOf( 'flex' ) == -1 ) {
					this.css_class_width = this.convertSize( width ),
						this.css_class_width !== width && ( this.css_class_width = this.css_class_width.replace( /[^\d]/g, "" ) ),
						$content.removeClass( "vc_col-sm-" + this.css_class_width ),
						offset.match( /vc_col\-sm\-\d+/ ) || this.$el.addClass( "vc_col-sm-" + this.css_class_width ),
						vc.responsive_disabled && ( offset = offset.replace( /vc_col\-(lg|md|xs)[^\s]*/g, "" ) ),
						_.isEmpty( offset ) || ( $content.removeClass( offset ),
							this.$el.addClass( offset ) )
				}
				else {
					if ( width == 'flex/1' || width == 'flex-1' ) this.$el.addClass( 'wpb-flex-1' );
					else return this.$el.addClass( 'wpb-flex-auto' );
				}
			}
		}
		$( 'body' ).on( 'vcPanel.shown', '#vc_ui-panel-edit-element[data-vc-shortcode="porto_products"]', function() {
			orderAutoComplete();
			jQuery('.wpb_el_type_autocomplete[data-vc-shortcode-param-name="orderby"] input.autocomplete_field[name="orderby"]').data('vcParamObject').updateItems = function() {
				this.selected_items.length ? this.$input_param.val(this.getSelectedItems().join(", ")) : this.$input_param.val("")
				orderAutoComplete();
			}
		} );
	} );

	var orderAutoComplete = function() {
		if( jQuery( '.orderby.autocomplete_field' ).length ) {
			var orderby = jQuery( '.orderby.autocomplete_field' ).val();
			jQuery('.wpb_el_type_porto_button_group[data-vc-shortcode-param-name*="order_"]').each(function() {
				var $this = jQuery( this );
				var paramName = $this.attr( 'data-vc-shortcode-param-name' ).slice(6);
				if( orderby.indexOf( paramName ) > -1 ) {
					$this.removeClass( 'vc_dependent-hidden' );
				} else {
					$this.addClass( 'vc_dependent-hidden' );
				}
			});			
		}
	}

} )( window.jQuery )
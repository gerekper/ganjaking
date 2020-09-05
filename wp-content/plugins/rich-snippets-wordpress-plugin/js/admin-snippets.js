var rich_snippets = {
  'snippets': []
};

(
    function () {
      'use strict';

      rich_snippets.snippet = function ( $main_snippet ) {
        this.$main_snippet   = $main_snippet;
        this.$main_select    = null;
        this.main_select2    = null;
        this.$popular_items  = null;
        this.actions         = [];
        this.loaders_running = 0;
        this.filters         = [];
        this.actions         = [];

        this.init = function () {
          this.do_action( 'before_init', this );

          var self = this;

          this.$main_select   = self.$main_snippet.find( '.wpb-rs-schema-main-select' );
          this.$popular_items = self.$main_snippet.find( '.wpb-rs-popular' );

          jQuery( this.$main_snippet ).on( 'click', '.wpb-rs-schema-property-actions-overridable input', function () {
            self.check_overridables( jQuery( this ) );
          } );

          this.main_select2 = this.select2( this.$main_select, {
            'ajax':     {
              'url':        WPB_RS_ADMIN.rest_url + '/schemas/types/',
              'dataType':   'json',
              'beforeSend': function ( xhr ) {
                xhr.setRequestHeader( 'X-WP-Nonce', WPB_RS_ADMIN.nonce );
                self.on_ajax_before_send( xhr );
              },
              'complete':   function () {
                self.on_ajax_complete();
              },
              'success':    function ( response ) {
                var items = {};
                jQuery.each( response.schema_types, function ( k, v ) {
                  items[v] = v;
                } );
                self.select2_update_list( self.$main_select, items );
              },
              'data':       {
                'type': 'class'
              },
              'method':     'GET',
              'cache':      true,
              'error':      function ( xhr, text_status, error ) {
                rich_snippets_errors.ajax_error_handler( xhr, text_status, error );
              }
            },
            'callback': function ( value ) {
              self.select_main_schema_type( {'schema_type': value} );
            }
          } );

          jQuery( this.$main_snippet ).on( 'change', '.wpb-rs-schema-property-field-subfield-select', function ( e ) {
            self.subfield_select( {}, jQuery( this ) );
          } );

          jQuery( this.$main_snippet ).on( 'click', '.wpb-rs-schema-property-row .wpb-rs-schema-property-actions a', function ( e ) {
            e.preventDefault();
            self.property_action( jQuery( this ) );
          } );

          this.$popular_items.find( '.button' ).not( '.help' ).on( 'click', function ( e ) {
            e.preventDefault();
            self.click_popular( e, jQuery( this ) );
          } );

          jQuery( this.$main_snippet ).on( 'click', '.wpb-rs-new-type-button', function ( e ) {
            e.preventDefault();
            var value = jQuery( this ).parent().find( 'input' ).val();

            if ( '' !== value ) {
              self.select_main_schema_type( {'schema_type': value} );
            }
          } );

          jQuery( this.$main_snippet ).on( 'click', '.wpb-rs-new-property-button', function ( e ) {
            e.preventDefault();
            var value = jQuery( this ).parent().find( 'input' ).val();
            if ( '' !== value ) {
              var $table = jQuery( this ).closest( 'table' );
              self.print_properties( {
                'props':       [value],
                'append_obj':  $table,
                'schema_type': $table.data( 'schema_type' ),
                'snippet_id':  $table.data( 'snippet_id' )
              } );
            }
          } );

          this.init_property_select( this.$main_snippet );

          $main_snippet.on( 'click', '.wpb-rs-property-expander', function ( e ) {
            e.preventDefault();
            var dashicon = jQuery( this ).find( '.dashicons' );
            var $table   = jQuery( this ).closest( '.wpb-rs-property-list' );
            var $text    = jQuery( this ).find( 'span' ).last();

            if ( dashicon.hasClass( 'dashicons-arrow-right' ) ) {
              self.expand_properties( $table );
              dashicon.removeClass( 'dashicons-arrow-right' ).addClass( 'dashicons-arrow-down' );
              $text.text( WPB_RS_ADMIN.i18n.collapse );
            }
            else {
              self.collapse_properties( $table );
              dashicon.addClass( 'dashicons-arrow-right' ).removeClass( 'dashicons-arrow-down' );
              $text.text( WPB_RS_ADMIN.i18n.expand );
            }
          } );

          this.do_action( 'init', this );
        };

        this.on_ajax_before_send = function ( xhr ) {
          this.loaders_running ++;
          this.$main_snippet.find( '.wpb-rs-loader' ).css( 'display', 'block' );
          this.do_action( 'ajax_before_send', xhr );
        };

        this.on_ajax_complete = function () {
          var self = this;
          this.loaders_running --;

          if ( this.loaders_running <= 0 ) {
            this.loaders_running = 0;
            this.$main_snippet.find( '.wpb-rs-loader' ).css( 'display', 'none' );
          }

          /* Reset loader regardless of any other ajax calls running */
          setTimeout( function () {
            self.loaders_running = 0;
            self.$main_snippet.find( '.wpb-rs-loader' ).css( 'display', 'none' );
          }, 20000 );
        };

        this.select_main_schema_type = function ( args ) {
          args.main_schema = true;
          this.clear_all_rows( this.$main_snippet.find( '.wpb-rs-property-list-main' ) );
          this.select_schema_type( args );
        };

        this.select_schema_type = function ( event ) {
          var schema_type    = event.hasOwnProperty( 'params' ) ? event.params.data.id : event.schema_type;
          var include_table  = event.hasOwnProperty( 'wpb_rs_include_table' ) ? event.wpb_rs_include_table : true;
          var append_obj     = event.hasOwnProperty( 'wpb_rs_append_obj' ) ? event.wpb_rs_append_obj : this.$main_snippet.find( '.wpb-rs-property-list-main' );
          var return_type    = event.hasOwnProperty( 'wpb_rs_return_type' ) ? event.wpb_rs_return_type : 'required';
          var is_main_schema = event.hasOwnProperty( 'main_schema' ) && event.main_schema;
          var self           = this;

          this.load_properties( {
            'schema_type': schema_type,
            'return_type': return_type,
            'callback':    function ( properties ) {
              self.print_properties( {
                'include_table':  include_table,
                'append_obj':     append_obj,
                'schema_type':    schema_type,
                'props':          properties,
                'is_main_schema': is_main_schema
              } );
            }
          } );
        };

        this.load_properties = function ( args ) {

          args     = (
              typeof args !== 'undefined'
          ) ? args : {};
          var self = this;

          var schema_type = args.hasOwnProperty( 'schema_type' ) ? args.schema_type : '';
          var return_type = args.hasOwnProperty( 'return_type' ) ? args.return_type : 'required';
          var q           = args.hasOwnProperty( 'q' ) ? args.q : '';
          var callback    = args.hasOwnProperty( 'callback' ) ? args.callback : function () {
          };

          return jQuery.ajax( {
            'url':        WPB_RS_ADMIN.rest_url + '/schemas/properties/',
            'dataType':   'json',
            'beforeSend': function ( xhr ) {
              xhr.setRequestHeader( 'X-WP-Nonce', WPB_RS_ADMIN.nonce );
              self.on_ajax_before_send( xhr );
            },
            'data':       {
              'schema_type': self.remove_http( schema_type ),
              'return_type': return_type,
              'q':           q
            },
            'method':     'GET'
          } ).done( function ( data ) {
            callback( data.properties );
          } ).fail( function ( xhr, text_status, error ) {
            rich_snippets_errors.ajax_error_handler( xhr, text_status, error );
          } ).always( function () {
            self.on_ajax_complete();
          } );
        };

        this.remove_http = function ( str_arr ) {

          if ( 'string' === typeof str_arr ) {
            return str_arr.replace( /http(s)?:\/\//g, '' );
          }

          if ( 'object' === typeof str_arr ) {
            jQuery.each( str_arr, function ( i, v ) {
              str_arr[i] = v.replace( /http(s)?:\/\//g, '' );
            } );
          }

          return str_arr;
        };

        this.print_properties = function ( args ) {

          args = (
              typeof args !== 'undefined'
          ) ? args : {};

          var self = this;

          var props = args.hasOwnProperty( 'props' ) ? args.props : [];

          /**
           * If the whole <table>-HTML code should be returned
           * @type {boolean}
           */
          var include_table = args.hasOwnProperty( 'include_table' ) ? args.include_table : false;

          /**
           * In case of include_table = true this should be the <table>-Object
           * In case of include_true = false this should be a DIV-Element
           */
          var $append_obj = args.hasOwnProperty( 'append_obj' ) ? args.append_obj : self.$main_snippet.find( '.not-existent' );

          var schema_type = args.hasOwnProperty( 'schema_type' ) ? args.schema_type : '';

          var snippet_id = args.hasOwnProperty( 'snippet_id' ) ? args.snippet_id : '';

          jQuery.ajax( {
            'url':        WPB_RS_ADMIN.rest_url + '/schemas/properties/html/',
            'dataType':   'json',
            'beforeSend': function ( xhr ) {
              xhr.setRequestHeader( 'X-WP-Nonce', WPB_RS_ADMIN.nonce );
              self.on_ajax_before_send( xhr );
            },
            'data':       {
              'properties':     self.remove_http( props ),
              'include_table':  include_table,
              'schema_type':    self.remove_http( schema_type ),
              'post_id':        WPB_RS_ADMIN.post_id,
              'snippet_id':     snippet_id,
              'is_main_schema': args.is_main_schema
            },
            'method':     'POST'
          } ).done( function ( data ) {
            if ( 'string' === jQuery.type( data ) ) {
              var $table = jQuery( data );

              /* Injects a whole <table> into the DOM */
              $append_obj.html( $table );

              /* Reference to the child table */
              var $prop_field = $append_obj.closest( '.wpb-rs-schema-property-field' );
              $prop_field.find( '.wpb-rs-schema-property-ref' ).first().val( $table.data( 'snippet_id' ) );

              self.init_property_select( $append_obj.parent() );
            }
            else {
              /* Only adds some rows */
              jQuery.each( data, function ( property_name, html ) {
                args.append_obj.find( 'tbody' ).first().append( html );
              } );
            }

            /* Initialize custom fields */
            rs_fields.init( self.$snippets );
          } ).fail( function ( xhr, text_status, error ) {
            rich_snippets_errors.ajax_error_handler( xhr, text_status, error );
          } ).always( function () {
            self.on_ajax_complete();
          } );
        };

        this.toggle_extra_fields = function ( $fields, schema_type ) {
          $fields.each( function () {

            if ( - 1 !== schema_type.indexOf( jQuery( this ).data( 'name' ) ) ) {
              jQuery( this ).removeClass( 'wpb-rs-hidden' );
            }
            else {
              jQuery( this ).addClass( 'wpb-rs-hidden' );
            }
          } );
        };

        this.subfield_select = function ( args, $triggered_obj ) {

          /* The value */
          var schema_type     = $triggered_obj.val();
          var $property_field = $triggered_obj.closest( '.wpb-rs-schema-property-field' );
          var label           = $triggered_obj.find( 'option:selected' ).text();

          $property_field.prev().find( '.wpb-rs-schema-property-type-selected' ).text( label );

          if ( 1 !== $triggered_obj.find( 'option:selected' ).data( 'has_schema' ) ) {
            /* Remove property list */
            this.remove_property_list( $property_field.find( '.wpb-rs-property-list' ).first() );

            /* delete reference field */
            $property_field.find( '.wpb-rs-schema-property-ref' ).first().val( '' );

            /* Show/hide input fields, if necessary */
            this.toggle_extra_fields( $property_field.find( '.wpb-rs-schema-property-extra-fields' ).first().children(), schema_type );

            return false;
          }

          /* Show/hide input fields, if necessary */
          this.toggle_extra_fields( $property_field.find( '.wpb-rs-schema-property-subclass-properties' ).next( '.wpb-rs-schema-property-extra-fields' ).children(), schema_type );

          /* The options */
          args.wpb_rs_include_table = true;
          args.wpb_rs_append_obj    = $property_field.find( '.wpb-rs-schema-property-subclass-properties' );
          args.wpb_rs_return_type   = 'required';
          args.schema_type          = schema_type;

          this.select_schema_type( args );
        };

        this.click_popular = function ( event, $clicked_el ) {
          var schema_type = $clicked_el.data( 'value' );

          /* make schema type visible to the user */
          this.main_select2.val( schema_type ).trigger( 'change' );

          /* fire main schema selection functionality (as this is not triggered above) */
          this.select_main_schema_type( {'schema_type': schema_type} );
        };

        this.clear_all_rows = function ( props_table ) {

          props_table.find( 'tbody' ).html( '' );
        };

        this.remove_property_list = function ( table_obj ) {

          table_obj.remove();
        };

        this.property_action = function ( $triggered_obj ) {
          if ( $triggered_obj.parent().hasClass( 'delete' ) ) {
            var $row = $triggered_obj.closest( 'tr' );
            $row.next().remove(); /* remove row settings */
            $row.remove(); /* remove row header*/
          }
          else if ( $triggered_obj.parent().hasClass( 'edit' ) ) {
            var $tr = $triggered_obj.closest( 'tr' );
            if ( $tr.hasClass( 'opened' ) ) {
              $tr.removeClass( 'opened' );
            }
            else {
              $tr.addClass( 'opened' );
            }
          }
        };

        this.init_property_select = function ( $parent ) {

          var self = this;

          $parent.find( '.wpb-rs-schema-new-property' ).each( function () {
            var $input           = jQuery( this );
            var $prop_list_table = jQuery( this ).closest( '.wpb-rs-property-list' );
            var schema_type      = $prop_list_table.data( 'schema_type' );
            var snippet_id       = $prop_list_table.data( 'snippet_id' );

            self.select2( $input, {
              'ajax':        {
                'url':        WPB_RS_ADMIN.rest_url + '/schemas/properties',
                'dataType':   'json',
                'beforeSend': function ( xhr ) {
                  xhr.setRequestHeader( 'X-WP-Nonce', WPB_RS_ADMIN.nonce );
                  self.on_ajax_before_send( xhr );
                },
                'complete':   function () {
                  self.on_ajax_complete();
                },
                'data':       {
                  'schema_type': self.remove_http( schema_type ),
                  'return_type': 'parents'
                  /* q will be added automatically */
                },
                'method':     'GET',
                'error':      function ( xhr, text_status, error ) {
                  rich_snippets_errors.ajax_error_handler( xhr, text_status, error );
                },
                'success':    function ( response ) {
                  var items = {};
                  jQuery.each( response.properties, function ( k, v ) {
                    items[v] = v;
                  } );
                  self.select2_update_list( $input, items );
                }
              }, 'callback': function ( value ) {
                var prop = value;

                if ( '' === prop ) {
                  return;
                }

                self.print_properties( {
                  'props':       [prop],
                  'append_obj':  $input.closest( 'table' ),
                  'schema_type': schema_type,
                  'snippet_id':  snippet_id
                } );
              }
            } );
          } );
        };

        this.select2 = function ( $input, args ) {
          var self = this;

          $input.on( 'keyup', jQuery.extend( {}, args.ajax ), _.debounce( self.select2_ajax, 1000 ) );

          $input.wrap( '<div class=\'wpb-rs-select2-outer\'></div>' );

          $input.parent().on( 'click', 'li', function () {
            self.select2_select( jQuery( this ), args.callback );
          } );

          return $input;
        };

        this.select2_ajax = function ( event ) {

          /* Read AJAX args */
          var ajax_args = event.data;

          /* Do not trigger when string length is < 3 */
          var q = jQuery( this ).val();
          if ( q.length < 3 ) {
            return true;
          }

          /* Add query string to ajax args */
          ajax_args.data['q'] = q;

          jQuery.ajax( ajax_args );

        };

        this.select2_update_list = function ( $obj, key_value_pairs ) {
          var input_id = $obj.prop( 'id' ).replace( '_hidden', '' );
          var $input   = jQuery( '#' + input_id );
          var $outer   = $input.parent();
          var $options = $outer.find( '.wpb-rs-select-options' );

          if ( 1 !== $options.length ) {
            $options = jQuery( '<ul class="wpb-rs-select-options"></ul>' );
            $input.after( $options );
          }
          else {
            $options.html( '' );
          }

          $options.append( '<li class="wpb-rs-select2-close" data-value="close"><span class="dashicons dashicons-no"></span></li>' );

          jQuery.each( key_value_pairs, function ( k, v ) {
            $options.append( '<li data-value="' + k + '">' + v + '</li>' );
          } );

        };

        this.select2_select = function ( $el, callback ) {
          /* get the value */
          var value = $el.data( 'value' );

          /* close if user closed the popup */
          if ( 'close' === value ) {
            $el.parent().remove();
            return;
          }

          /* get the <input> HTML element */
          var $input = $el.closest( '.wpb-rs-select2-outer' ).find( 'input' );

          /* write the new value to the <input> HTML element */
          $input.val( $el.text() );

          /* Update the data-value from the <input> HTML element, too */
          $input.data( 'value', value );

          /* close the popup */
          this.select2_close( $el );

          /* call the callback */
          if ( jQuery.isFunction( callback ) ) {
            callback( value );
          }
        };

        this.select2_close = function ( $el ) {
          $el.parent().remove();
        };

        this.check_overridables = function ( $el ) {
          if ( ! $el.prop( 'checked' ) ) {
            return;
          }

          $el.parents( '.wpb-rs-schema-property-field' ).each( function () {
            jQuery( this ).prev().find( '.wpb-rs-schema-property-actions-overridable input' ).prop( 'checked', true );
          } );
        };

        this.add_action = function ( tag, func, priority ) {
          if ( ! jQuery.isArray( this.actions[tag] ) ) {
            this.actions[tag] = [];
          }

          this.actions[tag].push( {
            'function': func,
            'priority': parseInt( priority )
          } );
        };

        this.do_action = function ( tag, args ) {
          if ( ! jQuery.isArray( this.actions[tag] ) ) {
            return false;
          }

          this.actions = this.actions.sort( function ( a, b ) {
            return a.priority - b.priority;
          } );

          jQuery.each( this.actions[tag], function ( index, action ) {
            if ( jQuery.isFunction( action.function ) ) {
              action.function( args );
            }
          } );
        };

        this.expand_properties = function ( $table ) {
          $table.find( '> tbody > tr.wpb-rs-schema-property-row' ).addClass( 'opened' );
        };

        this.collapse_properties = function ( $table ) {
          $table.find( '> tbody > tr.wpb-rs-schema-property-row' ).removeClass( 'opened' );
        };
      };

      jQuery( document ).ready( function () {
        var $main = jQuery( '#wp-rs-mb-main' );
        if ( 1 === $main.length ) {
          var snippet = new rich_snippets.snippet( $main.find( '.wpb-rs-schema-main' ).parent() );
          snippet.init();
          rich_snippets.snippets.push( snippet );
        }
      } );

    }
)();

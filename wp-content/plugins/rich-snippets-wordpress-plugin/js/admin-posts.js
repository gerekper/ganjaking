(
    function () {
      'use strict';

      var posts_settings = function () {
        this.$snippets = null;
        this.$loader   = null;

        this.init = function () {
          var self = this;

          self.$snippets = jQuery( '#wpb_rs_snippets' );
          self.$loader   = self.$snippets.parent().children( '.wpb-rs-loader' );

          jQuery( document ).on( 'click', '.wpb-rs-add-snippet', function ( e ) {
            e.preventDefault();
            self.add_snippet();
          } );

          jQuery( document ).on( 'click', '.wpb-rs-remove-snippet', function ( e ) {
            e.preventDefault();
            self.remove_snippet( jQuery( this ) );
          } );

          jQuery( document ).on( 'click', '.wpb-rs-load-snippets', function ( e ) {
            e.preventDefault();
            self.load_snippets( function ( data ) {
              self.after_load_snippets( data );
            } );
          } );

          jQuery( document ).on( 'click', '.wpb-rs-toggle-snippet', function ( e ) {
            e.preventDefault();
            self.toggle_snippet( jQuery( this ).closest( '.wpb-rs-single-snippet' ) );
          } );

          jQuery( document ).on( 'click', '.wpb-rs-snippet-closed', function ( e ) {
            if ( jQuery( this ).hasClass( 'wpb-rs-snippet-closed' ) ) {
              self.toggle_snippet( jQuery( this ).closest( '.wpb-rs-single-snippet' ) );
            }
          } );
        };

        this.on_ajax_before_send = function () {
          this.$loader.css( 'display', 'block' );
        };

        this.on_ajax_complete = function () {
          this.$loader.css( 'display', 'none' );
        };

        this.add_snippet = function () {
          var self = this;

          /* Load all snippets first */
          this.load_snippets( self.after_load_snippets );

          jQuery.ajax( {
            'url':        WPB_RS_POSTS.rest_url + '/form_new/',
            'dataType':   'json',
            'beforeSend': function ( xhr ) {
              xhr.setRequestHeader( 'X-WP-Nonce', WPB_RS_POSTS.nonce );
              self.on_ajax_before_send();
            },
            'error':      function ( xhr, text_status, error ) {
              rich_snippets_errors.ajax_error_handler( xhr, text_status, error );
            },
            'data':       {
              'post_id': WPB_RS_POSTS.post_id
            },
            'method':     'GET'
          } ).done( function ( data ) {
            self.setup_form( data.form, false );
          } ).fail( function ( xhr, text_status, error ) {
            window.rich_snippets_errors.ajax_error_handler( xhr, text_status, error );
          } ).always( function () {
            self.on_ajax_complete();
          } );
        };

        this.setup_form = function ( form, toggle ) {
          /* Create jQuery object */
          var $snippet_form = jQuery( form );

          /* Append to the DOM */
          this.$snippets.append( $snippet_form );

          /* Init $snippet_form */
          var snippet = new rich_snippets.snippet( $snippet_form );
          snippet.init();

          /* toggle form */
          if ( 'toggle' === toggle ) {
            this.toggle_snippet( $snippet_form );
          }
        };

        this.remove_snippet = function ( $button ) {
          var self       = this;
          var snippet_id = $button.closest( '.wpb-rs-single-snippet' ).find( '.wpb-rs-schema-main' ).data( 'uid' );

          jQuery.ajax( {
            'url':        WPB_RS_POSTS.rest_url + '/snippets_delete/',
            'dataType':   'json',
            'beforeSend': function ( xhr ) {
              xhr.setRequestHeader( 'X-WP-Nonce', WPB_RS_POSTS.nonce );
              self.on_ajax_before_send();
            },
            'data':       {
              'post_id': WPB_RS_POSTS.post_id, 'snippet_ids': [snippet_id]
            },
            'method':     'DELETE'
          } ).done( function ( data ) {
            $button.closest( '.wpb-rs-single-snippet' ).remove();
          } ).fail( function ( xhr, text_status, error ) {
            rich_snippets_errors.ajax_error_handler( xhr, text_status, error );
          } ).always( function () {
            self.on_ajax_complete();
          } );

        };

        this.after_load_snippets = function ( data ) {
          var self = this;
          data.forms.forEach( function ( val ) {
            self.setup_form( val, 'toggle' );
          } );

          /* prevent loading it again */
          this.$snippets.data( 'snippets-loaded', 1 );

          /* Remove 'x snippets are available' text */
          this.$snippets.find( '.wpb-rs-available-schemas-text' ).remove();

          /* Initialize custom fields */
          rs_fields.init( this.$snippets );
        };

        this.load_snippets = function ( callback, always, form_data, snippet_export ) {
          form_data      = typeof form_data !== 'undefined' ? form_data : {};
          snippet_export = typeof snippet_export !== 'undefined' ? snippet_export : {};
          callback       = typeof callback !== 'undefined' ? callback : function ( data ) {};
          always         = typeof always !== 'undefined' ? always : function () {
            self.on_ajax_complete();
          };

          if ( Object.keys( form_data ).length <= 0 ) {
            if ( 1 === parseInt( this.$snippets.data( 'snippets-loaded' ) ) ) {
              /* already loaded */
              return;
            }
          }

          var self = this;

          var settings = {
            'url':        WPB_RS_POSTS.rest_url + '/snippets_forms/',
            'dataType':   'json',
            'beforeSend': function ( xhr ) {
              xhr.setRequestHeader( 'X-WP-Nonce', WPB_RS_POSTS.nonce );
              self.on_ajax_before_send();
            },
            'data':       {
              'post_id':   WPB_RS_POSTS.post_id,
              'form_data': form_data
            },
            'method':     'POST'
          };

          if ( Object.keys( snippet_export ).length > 0 ) {
            settings.contentType = 'application/json; charset=utf-8';
            settings.data        = snippet_export;
            settings.url         = settings.url + '?post_id=' + WPB_RS_POSTS.post_id;
          }

          jQuery.ajax( settings ).done( callback ).fail( function ( xhr, text_status, error ) {
            window.rich_snippets_errors.ajax_error_handler( xhr, text_status, error );
          } ).always( always );
        };

        this.toggle_snippet = function ( $snippet ) {
          var $toggled_text = $snippet.find( '> .wpb-rs-type' );
          var status        = $toggled_text.length === 1 ? 'closed' : 'open';

          if ( 'open' === status ) {
            $snippet.addClass( 'wpb-rs-snippet-closed' );

            /* Fetch the main element class */
            var toggled_text = $snippet.find( '.wpb-rs-schema-main' ).data( 'type' );

            /* and prepend it */
            $snippet.prepend( jQuery( '<p class="wpb-rs-type">' + toggled_text + '</p>' ) );

            /* Toggle dashicon */
            $snippet.find( '.wpb-rs-toggle-snippet span' ).addClass( 'dashicons-arrow-up-alt2' ).removeClass( 'dashicons-arrow-down-alt2' );

          }
          else if ( 'closed' === status ) {
            $snippet.removeClass( 'wpb-rs-snippet-closed' );

            /* Remove the extra text */
            $toggled_text.remove();

            /* Toggle dashicon */
            $snippet.find( '.wpb-rs-toggle-snippet span' ).removeClass( 'dashicons-arrow-up-alt2' ).addClass( 'dashicons-arrow-down-alt2' );
          }
        };
      };

      jQuery( document ).ready( function () {
        window.rich_snippets.posts = new posts_settings();
        window.rich_snippets.posts.init();
      } );
    }
)();


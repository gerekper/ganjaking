var rich_snippets_errors;

(
    function () {
      'use strict';

      var errors = function () {
        this.$errors = null;

        this.init = function () {
          this.$errors = jQuery( '.wpb-rs-errors' );
        };

        this.print_error = function ( message ) {
          var self = this;

          var $error = jQuery( '<p class="wpb-rs-error">' + message + '</p>' );

          this.$errors.append( $error );

          this.maybe_show_popup();

          setTimeout( function ( $e ) {
            /* if there is only one error left, fade out the whole popup */
            if ( self.popup_error_count() <= 1 ) {
              self.$errors.hide();
            }

            $e.fadeOut().remove();

          }, 8000, $error );
        };

        this.maybe_hide_popup = function () {
          if ( this.popup_has_errors() ) {
            return;
          }

          this.$errors.hide();
        };

        this.maybe_show_popup = function () {
          if ( this.$errors.is( ':visible' ) ) {
            return;
          }

          this.$errors.show();
        };

        this.popup_has_errors = function () {
          return this.popup_error_count() > 0;
        };

        this.popup_error_count = function () {
          return this.$errors.find( '.wpb-rs-error' ).length;
        };

        this.ajax_error_handler = function ( xhr, text_status, error ) {

          var data = xhr.responseJSON;

          if ( !data ) {
            this.print_error( error );
            return;
          }

          if ( data.hasOwnProperty( 'message' ) ) {
            this.print_error( data.message );
          }
        };
      };

      jQuery( document ).ready( function () {
        rich_snippets_errors = new errors();
        rich_snippets_errors.init();
      } );

    }
)();

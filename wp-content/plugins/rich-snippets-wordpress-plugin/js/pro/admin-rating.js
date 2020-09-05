var rich_snippets_admin_rating;

(
    function () {
      'use strict';

      rich_snippets_admin_rating = function () {
        this.notice         = null;
        this.button_yes     = null;
        this.button_no      = null;
        this.button_dismiss = null;
        this.message        = null;

        this.init = function () {
          var self = this;

          this.notice         = jQuery( '.wpb-rs-rating-notice' );
          this.button_dismiss = jQuery( '.wpb-rs-rating-notice button.notice-dismiss' );
          this.message        = jQuery( '.wpb-rs-rating-notice p' );

          this.button_dismiss.on( 'click', function () {
            self.dismiss();
          } );

          jQuery( document ).on( 'click', '.wpb-rs-rating-notice .button', function ( e ) {
            var target = jQuery( e.currentTarget );
            var link   = target.prop( 'href' );

            if ( '#' === link || '#' === link.charAt( link.length - 1 ) ) {
              e.preventDefault();
            }

            var next_step = parseInt( target.data( 'next' ) );

            if ( next_step > 0 ) {
              self.next_step( next_step );
            }

            if ( 1 === parseInt( target.data( 'close' ) ) ) {
              self.close_notice();
            }
          } );

        };

        this.close_notice = function () {
          this.notice.find( '.notice-dismiss' ).trigger( 'click' );
        };

        this.next_step = function ( step ) {
          var i,
              button,
              button_link,
              button_data_next,
              button_data_close,
              button_class;

          if ( ! WPB_RS_ADMIN_RATING.steps.hasOwnProperty( step ) ) {
            return;
          }

          var step_obj = WPB_RS_ADMIN_RATING.steps[step];
          console.log( step_obj );

          var new_text = step_obj.text;

          if ( step_obj.hasOwnProperty( 'buttons' ) ) {
            for ( i = 0; i < step_obj.buttons.length; i ++ ) {
              button            = step_obj.buttons[i];
              button_link       = button.hasOwnProperty( 'link' ) ? button.link : '#';
              button_data_next  = button.hasOwnProperty( 'next' ) ? 'data-next="' + parseInt( button.next ) + '"' : '';
              button_data_close = button.hasOwnProperty( 'close' ) && button.close ? 'data-close="1"' : '';
              button_class      = ['button'];

              if ( button_link !== '#' ) {
                button_class.push( 'button-primary' );
              }

              new_text += ' <a href="' + button_link + '" target="_blank" class="' + button_class.join( ' ' ) + '" ' + button_data_next + ' ' + button_data_close + '>' + button.label + '</a>';
            }
          }

          this.message.html( new_text );
        };

        this.dismiss = function () {
          this.update_option_via_rest_api( WPB_RS_ADMIN_RATING.rest_url + '/rating/dismiss' );
        };

        this.update_option_via_rest_api = function ( url ) {
          jQuery.ajax( {
            'url':        url,
            'dataType':   'json',
            'beforeSend': function ( xhr ) {
              xhr.setRequestHeader( 'X-WP-Nonce', WPB_RS_ADMIN_RATING.nonce );
            },
            'method':     'POST'
          } ).fail( function ( jqXHR, text_status, thrown_error ) {
            console.error( jqXHR, text_status, thrown_error );
          } ).done( function ( response ) {

            if ( ! response ) {
              console.error( 'No response.' );
              return false;
            }

            if ( ! response.hasOwnProperty( 'updated' ) ) {
              console.error( 'No response found.' );
              return false;
            }

            if ( ! response.updated ) {
              console.error( 'Could not update option (got FALSE).' );
            }
          } );
        };

      };


      jQuery( document ).ready( function () {
        new rich_snippets_admin_rating().init();
      } );

    }
)();




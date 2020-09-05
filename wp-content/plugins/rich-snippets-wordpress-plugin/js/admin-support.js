var rich_snippets_admin_support;

(
    function () {
      'use strict';

      rich_snippets_admin_support = function () {
        this.$faq_search_btn         = null;
        this.$faq_search_input       = null;
        this.$faq_results            = null;
        this.is_vote_running         = false;
        this.$add_feature_button     = null;
        this.is_feature_send_running = false;
        this.$deactivate_license_btn = null;
        this.$license_error          = null;

        this.init = function () {
          var self = this;

          self.$faq_search_btn         = jQuery( '.wpb-rs-support-faq-search-button' );
          self.$faq_search_input       = jQuery( '.wpb-rs-support-faq-search-input' );
          self.$faq_results            = jQuery( '.wpb-rs-support-faq-results' );
          self.$add_feature_button     = jQuery( '.wpb-rs-support-feature-text-send' );
          self.$deactivate_license_btn = jQuery( '.wpb-rs-support-deactivate-license' );
          self.$license_error          = jQuery( '.wpb-rs-support-deactivate-license-error' );

          self.$faq_search_input.on( 'keyup', _.debounce( function () {
            self.faq_search();
          }, 1000 ) );

          self.$faq_search_btn.on( 'click', function ( e ) {
            e.preventDefault();
            self.faq_search();
          } );

          jQuery( '.wpb-rs-support-feature-requests' ).on( 'click', '.wpb-rs-support-feature-vote', function ( e ) {
            e.preventDefault();

            if ( jQuery( this ).hasClass( 'disabled' ) ) {
              return false;
            }

            var vote_direction = jQuery( this ).data( 'direction' );
            var comment_id     = jQuery( this ).closest( 'tr' ).data( 'comment_id' );
            self.vote_feature( comment_id, vote_direction, jQuery( this ) );
          } );

          self.$add_feature_button.on( 'click', function ( e ) {
            e.preventDefault();
            self.add_feature();
          } );

          self.$deactivate_license_btn.on( 'click', function ( e ) {
            e.preventDefault();
            self.deactivate_license();
          } );

        };

        this.faq_search = function () {
          var self = this;
          var q    = self.$faq_search_input.val();

          jQuery.ajax( {
            'url':        WPB_RS_SUPPORT.rest_url + '/faq/',
            'dataType':   'json',
            'beforeSend': function ( xhr ) {
              xhr.setRequestHeader( 'X-WP-Nonce', WPB_RS_SUPPORT.nonce );
              self.$faq_search_btn.addClass( 'installing' );
            },
            'complete':   function () {
              self.$faq_search_btn.removeClass( 'installing' );
            },
            'data':       {
              'q': q,
            },
            'method':     'GET',
            'success':    function ( response ) {
              if ( response.hasOwnProperty( 'html' ) ) {
                self.$faq_results.html( response.html );
              }
            },
          } );
        };

        this.vote_feature = function ( comment_id, direction, $button ) {
          var self = this;

          if ( self.is_vote_running ) {
            return false;
          }

          jQuery.ajax( {
            'url':        WPB_RS_SUPPORT.rest_url + '/feature-request/vote/',
            'dataType':   'json',
            'beforeSend': function ( xhr ) {
              xhr.setRequestHeader( 'X-WP-Nonce', WPB_RS_SUPPORT.nonce );
              $button.addClass( 'installing' );
              $button.parent().find( 'a' ).addClass( 'disabled' );
              self.is_vote_running = true;
            },
            'complete':   function () {
              $button.removeClass( 'installing' );
              self.is_vote_running = false;
            },
            'data':       {
              'comment_id': comment_id, 'direction': direction,
            },
            'method':     'POST',
            'error':      function ( xhr, text_status, error ) {
              $button.parent().find( 'a' ).removeClass( 'disabled' );

              var $error = $button.closest( 'tr' ).find( '.vote-errors' );
              var data   = xhr.responseJSON;

              if ( ! data ) {
                self.print_error( error, $error );
                return;
              }

              if ( data.hasOwnProperty( 'message' ) ) {
                self.print_error( data.message, $error );
              }
            },
            'success':    function ( response ) {
              if ( response.hasOwnProperty( 'success' ) && response.success ) {
                $button.closest( '.vote-column' ).html( '<span class="dashicons dashicons-yes"></span>' );
                $button.addClass( 'disabled' );
              }
            },
          } );
        };

        this.add_feature = function () {
          var self = this;

          var $feature_text = self.$add_feature_button.parent().find( 'textarea' );
          var feature_text  = $feature_text.val();

          if ( '' === feature_text ) {
            return false;
          }

          var $gdpr_p = self.$add_feature_button.parent().find( '.gdpr' );
          var gdpr    = $gdpr_p.find( 'input' ).prop( 'checked' );
          console.log( gdpr );

          if ( ! gdpr ) {
            $gdpr_p.addClass( 'notice notice-error inside' );
            return false;
          }
          else {
            $gdpr_p.removeClass( 'notice notice-error inside' );
          }

          if ( self.is_feature_send_running ) {
            return false;
          }

          jQuery.ajax( {
            'url':        WPB_RS_SUPPORT.rest_url + '/feature-request/',
            'dataType':   'json',
            'beforeSend': function ( xhr ) {
              xhr.setRequestHeader( 'X-WP-Nonce', WPB_RS_SUPPORT.nonce );
              self.$add_feature_button.addClass( 'installing' );
              self.is_feature_send_running = true;
            },
            'complete':   function () {
              self.$add_feature_button.removeClass( 'installing' );
              self.is_feature_send_running = false;
            },
            'data':       {
              'content': feature_text,
            },
            'method':     'POST',
            'error':      function ( xhr, text_status, error ) {

              var $error = self.$add_feature_button.parent().find( '.vote-errors' );
              var data   = xhr.responseJSON;

              if ( ! data ) {
                self.print_error( error, $error );
                return;
              }

              if ( data.hasOwnProperty( 'message' ) ) {
                self.print_error( data.message, $error );
              }
            },
            'success':    function ( response ) {
              if ( response.hasOwnProperty( 'success' ) && response.success ) {
                /* Add OK-Icon */
                self.$add_feature_button.prepend( '<span class="dashicons dashicons-yes"></span>' );

                /* Empty text area field */
                $feature_text.val( '' );

                /* Remove OK-Icon after 5 seconds */
                setTimeout( function () {
                  self.$add_feature_button.find( 'span' ).remove();
                }, 5000 );

                /* Display success-message for 8 seconds */
                jQuery( '.vote-success' ).fadeIn( 400, function () {
                  setTimeout( function () {
                    jQuery( '.vote-success' ).fadeOut();
                  }, 8000 );
                } );
              }
            },
          } );

        };

        this.print_error = function ( error, $field ) {

          $field.html( error );

          setTimeout( function () {
            $field.text( '' );
          }, 8000 );
        };


        this.deactivate_license = function () {
          var self = this;

          jQuery.ajax( {
            'url':        WPB_RS_SUPPORT.rest_url + '/deactivate-license/',
            'dataType':   'json',
            'beforeSend': function ( xhr ) {
              xhr.setRequestHeader( 'X-WP-Nonce', WPB_RS_SUPPORT.nonce );
              self.$deactivate_license_btn.addClass( 'installing' );
              self.$deactivate_license_btn.prop( 'disabled', true );
            },
            'complete':   function () {
              self.$deactivate_license_btn.removeClass( 'installing' );
              self.$deactivate_license_btn.prop( 'disabled', false );
            },
            'data':       {},
            'method':     'POST',
            'error':      function ( xhr, text_status, error ) {

              var data = xhr.responseJSON;

              if ( ! data ) {
                self.print_error( error, self.$license_error );
                return;
              }

              if ( data.hasOwnProperty( 'message' ) ) {
                self.print_error( data.message, self.$license_error );
              }
            },
            'success':    function ( response ) {
              if ( response.hasOwnProperty( 'deactivated' ) && response.deactivated ) {

                if ( response.hasOwnProperty( 'redirect_url' ) && '' !== response.redirect_url ) {
                  window.location.replace( response.redirect_url );
                }
              }
            },
          } );
        };

      };

      jQuery( document ).ready( function () {
        new rich_snippets_admin_support().init();
      } );

    }
)();




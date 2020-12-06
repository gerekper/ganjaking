(function($) {

  'use strict';

  $(function() {

    // loginpress redirects tabs.
    $('.loginpress-redirects-tab').on( 'click', function(event) {

      event.preventDefault();

      var target = $(this).attr('href');
      $(target).show().siblings('table').hide();
      $(this).addClass('loginpress-redirects-active').siblings().removeClass('loginpress-redirects-active');

      if( target == '#loginpress_login_redirect_users' ) {
        $('#loginpress_redirect_user_search').show();
        $('#loginpress_redirect_role_search').hide();
        $('[for="loginpress_login_redirects[login_redirects]"]').html("Search Username");
        $('.login_redirects .description').html("Search Username for apply redirects on that.");
      }

      if( target == '#loginpress_login_redirect_roles' ) {
        $('#loginpress_redirect_role_search').show();
        $('#loginpress_redirect_user_search').hide();
        $('[for="loginpress_login_redirects[login_redirects]"]').html("Search Roles");
        $('.login_redirects .description').html("Search Role for apply redirects on that.");
      }
    });

    // Apply ajax on click new button.
    $(document).on( "click", ".loginpress-user-redirects-update", function(event) {

      event.preventDefault();

      var el      = $(this);
      var tr      = el.closest('tr');
      var id      = tr.attr("data-login-redirects-user");
      var logout  = tr.find( '.loginpress_logout_redirects_url input[type=text]').val();
      var login   = tr.find( '.loginpress_login_redirects_url input[type=text]' ).val();
      var _nonce  = tr.find('.loginpress__user-redirects_nonce').val();

      $.ajax({
        url : ajaxurl,
        type: 'POST',
        data: {
          action  : 'loginpress_login_redirects_update',
          security: _nonce,
          login   : login,
          logout  : logout,
          id      : id
        },
        beforeSend: function() {
          tr.find( '.autologin-sniper' ).show();
          tr.find( '.loginpress-user-redirects-update' ).attr( "disabled", "disabled" );
        },
        success: function( response ) {
          tr.find( '.autologin-sniper' ).hide();
          tr.find( '.loginpress-user-redirects-update' ).removeAttr( "disabled" );
          tr.find( '.loginpress_autologin_code p' ).html(response);
        }
      }); // !Ajax.
    }); // !click .loginpress-user-redirects-update.

    // Apply ajax on click delete button.
    $(document).on( "click", ".loginpress-user-redirects-delete", function(event) {

      event.preventDefault();

      var el     = $(this);
      var tr     = el.closest('tr');
      var id     = tr.attr("data-login-redirects-user");
      var _nonce = tr.find('.loginpress__user-redirects_nonce').val();

      $.ajax({

        url : ajaxurl,
        type: 'POST',
        data: {
          action  : 'loginpress_login_redirects_delete',
          security: _nonce,
          id      : id
        },
        beforeSend: function() {
          tr.find( '.loginpress_autologin_code p' ).html('');
          tr.find( '.autologin-sniper' ).show();
          tr.find( '.loginpress-user-redirects-update' ).attr( "disabled", "disabled" );
          tr.find( '.loginpress-user-redirects-delete' ).attr( "disabled", "disabled" );
        },
        success: function( response ) {
          $( '#loginpress_redirects_user_id_' + id ).remove();
        }
      }); // !Ajax.
    }); // !click .loginpress-user-redirects-delete.

    // Apply ajax on click new button.
    $(document).on( "click", ".loginpress-redirects-role-update", function(event) {

      event.preventDefault();

      var el      = $(this);
      var tr      = el.closest('tr');
      var role    = tr.attr( "data-login-redirects-role" );
      var logout  = tr.find( '.loginpress_logout_redirects_url input[type=text]').val();
      var login   = tr.find( '.loginpress_login_redirects_url input[type=text]' ).val();
      var _nonce  = tr.find('.loginpress__role-redirects_nonce').val();

      $.ajax({

        url : ajaxurl,
        type: 'POST',
        data: {
          action  : 'loginpress_login_redirects_role_update',
          security: _nonce,
          login   : login,
          logout  : logout,
          role    : role,
        },
        beforeSend: function() {
          tr.find( '.autologin-sniper' ).show();
          tr.find( '.loginpress-redirects-role-update' ).attr( "disabled", "disabled" );
        },
        success: function( response ) {
          tr.find( '.autologin-sniper' ).hide();
          tr.find( '.loginpress-redirects-role-update' ).removeAttr( "disabled" );
          tr.find( '.loginpress_autologin_code p' ).html(response);
        }
      }); // !Ajax.
    }); // !click .loginpress-redirects-role-update.

    // Apply ajax on click delete button.
    $(document).on( "click", ".loginpress-redirects-role-delete", function(event) {

      event.preventDefault();

      var el     = $(this);
      var tr     = el.closest('tr');
      var role   = tr.attr( "data-login-redirects-role" );
      var _nonce = tr.find('.loginpress__role-redirects_nonce').val();

      $.ajax({

        url : ajaxurl,
        type: 'POST',
        data: {
          action  : 'loginpress_login_redirects_role_delete',
          security: _nonce,
          role    : role,
        },
        beforeSend: function() {
          tr.find( '.loginpress_autologin_code p' ).html('');
          tr.find( '.autologin-sniper' ).show();
          tr.find( '.loginpress-redirects-role-update' ).attr( "disabled", "disabled" );
          tr.find( '.loginpress-redirects-role-delete' ).attr( "disabled", "disabled" );
        },
        success: function( response ) {
          $( '#loginpress_redirects_role_' + role ).remove();
        }
      }); // !Ajax.
    }); // !click .loginpress-redirects-role-delete.

  });
})(jQuery); // This invokes the function above and allows us to use '$' in place of 'jQuery' in our code.

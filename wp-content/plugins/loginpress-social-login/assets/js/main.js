jQuery(document).ready(function($) {
	'use strict';

  $("#wpb-loginpress_social_logins\\[facebook\\]").on('click', function() {

    if ($('#wpb-loginpress_social_logins\\[facebook\\]').is(":checked")) {
      $('tr.facebook_app_id').show();
      $('tr.facebook_app_secret').show();
    } else {
      $('tr.facebook_app_id').hide();
      $('tr.facebook_app_secret').hide();
    }
  });

  $("#wpb-loginpress_social_logins\\[twitter\\]").on('click', function() {

    if ($('#wpb-loginpress_social_logins\\[twitter\\]').is(":checked")) {
      $('tr.twitter_oauth_token').show();
      $('tr.twitter_token_secret').show();
      $('tr.twitter_callback_url').show();
    } else {
      $('tr.twitter_oauth_token').hide();
      $('tr.twitter_token_secret').hide();
      $('tr.twitter_callback_url').hide();
    }
  });

  $("#wpb-loginpress_social_logins\\[gplus\\]").on('click', function() {

    if ($('#wpb-loginpress_social_logins\\[gplus\\]').is(":checked")) {
      $('tr.gplus_client_id').show();
      $('tr.gplus_client_secret').show();
      $('tr.gplus_redirect_uri').show();
    } else {
      $('tr.gplus_client_id').hide();
      $('tr.gplus_client_secret').hide();
      $('tr.gplus_redirect_uri').hide();
    }
  });

	$("#wpb-loginpress_social_logins\\[linkedin\\]").on('click', function() {

		if ($('#wpb-loginpress_social_logins\\[linkedin\\]').is(":checked")) {
			$('tr.linkedin_client_id').show();
			$('tr.linkedin_client_secret').show();
			$('tr.linkedin_redirect_uri').show();
		} else {
			$('tr.linkedin_client_id').hide();
			$('tr.linkedin_client_secret').hide();
			$('tr.linkedin_redirect_uri').hide();
		}
	});


  $(window).on('load', function() {
    if ($('#wpb-loginpress_social_logins\\[facebook\\]').is(":checked")) {

      $('tr.facebook_app_id').show();
      $('tr.facebook_app_secret').show();
    }

    if ($('#wpb-loginpress_social_logins\\[twitter\\]').is(":checked")) {

      $('tr.twitter_oauth_token').show();
      $('tr.twitter_token_secret').show();
      $('tr.twitter_callback_url').show();
    }

    if ($('#wpb-loginpress_social_logins\\[gplus\\]').is(":checked")) {

      $('tr.gplus_client_id').show();
      $('tr.gplus_client_secret').show();
      $('tr.gplus_redirect_uri').show();
    }

		if ($('#wpb-loginpress_social_logins\\[linkedin\\]').is(":checked")) {

			$('tr.linkedin_client_id').show();
			$('tr.linkedin_client_secret').show();
			$('tr.linkedin_redirect_uri').show();
		}
  });

	$('.loginpress-social-login-tab').on( 'click', function(event) {

      event.preventDefault();

      var target = $(this).attr('href');
      $(this).addClass('loginpress-social-login-active').siblings().removeClass('loginpress-social-login-active');

      if( target == '#loginpress_social_login_settings' ) { // Settings Tab.
        $('#loginpress_social_logins table').show();
	      $('#loginpress_social_logins p.submit').show();
        $('#loginpress_social_login_help').hide();
      }

      if( target == '#loginpress_social_login_help' ) { // Help Tab.
        $('#loginpress_social_logins table').hide();
	      $('#loginpress_social_logins p.submit').hide();
        $('#loginpress_social_login_help').show();
      }
  });
  $('.loginpress-social-accordions .loginpress-accordions').on( 'click', function(event) {

    event.preventDefault();
    $(this).toggleClass('loginpress-accordions-acive').closest('.loginpress-social-accordions').siblings().find('.loginpress-accordions').removeClass('loginpress-accordions-acive');
    $(this).next().slideToggle().closest('.loginpress-social-accordions').siblings().find('.loginpress-social-tabs ').slideUp();
  });

});

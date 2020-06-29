jQuery(document).ready(function($) {

  var get_webhook_row = function (id) {
    return MPDT.webhooks.row_html.replace(/\{\{\id\}\}/g,id);
  };

  var mpdt_highlight_code = function () {
    $('pre code').each(function(i, block) {
      hljs.highlightBlock(block);
    });
  };

  $('.mpdt_add_row').click(function() {
    // var id = $('#mpdt_ops_form .mpdt_ops_row').length; //causing issues when elements are deleted
    //Generate a random int between 0 and 10,000 - small possibility of a duplicate - but better than the $('.class').length method above
    var id = Math.floor((Math.random() * 99999) + 1);
    $(this).parent().before(get_webhook_row(id));
    return false;
  });

  //Remove webhook row
  $('body').on('click', 'a.mpdt_remove_row', function() {
    var answer = confirm("Are you sure you want to delete this Webook?");

    if(answer) {
      $('div#mpdt_ops_row-' + $(this).attr('data-value')).fadeOut(500, function() {
        $(this).remove();
      });
    }

    return false;
  });

  var mpdt_checks = function(obj, uncheck) {
    var id = $(obj).data('id');
    if ($(obj).is(':checked')) {
      $('.mpdt_row_'+id+'_checkbox input').prop('checked', true);
    }
    else if(uncheck) {
      $('.mpdt_row_'+id+'_checkbox input').prop('checked', false);
    }
  };

  $('.mpdt_all_checkbox input').each( function (i,v) {
    mpdt_checks(this, false);
  });

  $('body').on( 'click', '.mpdt_all_checkbox input', function (e) {
    mpdt_checks(this, true);
  });

  $('body').on( 'click', '.mpdt_toggle_advanced', function (e) {
    e.preventDefault();
    var id = '#mpdt_advanced_' + $(this).data('id');
    $(id).slideToggle();
  });

  $('select#mpdt_routes_dropdown').on('change', function (e) {
    if ($(this).val()=='-1') {
      return $('#mpdt_route_display').slideUp();
    }

    var _this = this;

    $('.mpdt_routes_dropdown_wrap .mpdt_rolling').show();
    $('#mpdt_route_display').slideUp(
      200,
      function () {
        var args = {
          'action': 'mpdt_api_data',
          'endpoint': $(_this).val()
        };

        var route = MPDT.api.routes[$(_this).val()];
        var html = MPDT.api.route_html;
        var update_args_html = '';
        var search_args_html = '';

        $.post(ajaxurl, args, function(data) {

          if (typeof route.search_args === 'object') {
            for (var slug in route.search_args) {
              var curr_input = route.search_args[slug];
              var search_html = MPDT.api.route_search_html;

              search_html = search_html
                .replace(/\{\{field\}\}/g,slug)
                .replace(/\{\{type\}\}/g,curr_input.type)
                .replace(/\{\{default\}\}/g,((typeof curr_input.default=='undefined' || curr_input.default=='') ? '' : ', ' + MPDT.str.default_value + ' ' + curr_input.default))
                .replace(/\{\{description\}\}/g,curr_input.description);

              search_args_html += search_html;
            }
          }
          else {
            search_args_html = route.search_args;
          }

          if (typeof route.update_args === 'object') {
            for (var slug in route.update_args) {
              var curr_input = route.update_args[slug];
              var input_html = MPDT.api.route_input_html;

              input_html = input_html
                .replace(/\{\{field\}\}/g,slug)
                .replace(/\{\{name\}\}/g,curr_input.name)
                .replace(/\{\{type\}\}/g,curr_input.type)
                .replace(/\{\{default\}\}/g,((typeof curr_input.default=='undefined' || curr_input.default=='') ? '' : ', ' + MPDT.str.default_value + ' ' + curr_input.default))
                .replace(/\{\{required\}\}/g,((curr_input.required!=false) ? '&nbsp;(<strong><em>'+curr_input.required+'</em></strong>)' : ''))
                .replace(/\{\{desc\}\}/g,curr_input.desc);

              update_args_html += input_html;
            }
          }
          else {
            update_args_html = route.update_args;
          }

          html = html
            .replace(/\{\{name\}\}/g,route.name)
            .replace(/\{\{method\}\}/g,route.method)
            .replace(/\{\{desc\}\}/g,route.desc)
            .replace(/\{\{url\}\}/g,route.url)
            .replace(/\{\{auth\}\}/g,(route.auth ? '✔︎' : ''))
            .replace(/\{\{search_args\}\}/g,search_args_html)
            .replace(/\{\{update_args\}\}/g,update_args_html)
            .replace(/\{\{output\}\}/g,route.output)
            .replace(/\{\{request\}\}/g,data.request)
            .replace(/\{\{response\}\}/g,JSON.stringify(data.response,undefined,2));

          $('#mpdt_route').html(html);

          mpdt_highlight_code();

          if(!$('#mpdt_route_display').is(':visible')) {
            $('#mpdt_route_display').slideDown();
          }

          $('.mpdt_routes_dropdown_wrap .mpdt_rolling').hide();
        },'json');
      }
    );
  });

  $('select#mpdt_events_dropdown').on('change', function (e) {
    if ($(this).val()=='-1') {
      return $('#mpdt_event_display').slideUp();
    }

    var curr_event = $(this).val();
    var event_info = MPDT.webhooks.events[curr_event];
    var _this = this;

    $('.mpdt_events_dropdown_wrap .mpdt_rolling').show();
    $('#mpdt_event_display').slideUp(
      200,
      function () {
        var args = {
          'action': 'mpdt_event_data',
          'event': curr_event
        };

        $.post(ajaxurl, args, function(data) {
          $('.mepr_event_description').html(event_info.desc);
          $('#mpdt_event_json pre code').html(JSON.stringify(data,undefined,2));

          mpdt_highlight_code();

          if(!$('#mpdt_event_display').is(':visible')) {
            $('#mpdt_event_display').slideDown();
          }

          $('.mpdt_events_dropdown_wrap .mpdt_rolling').hide();
        },'json');
      }
    );
  });

  $('body').on('click', '.mpdt_test_webhook button', function (e) {
    var evt = $('select#mpdt_events_dropdown').val();

    if (evt=='-1') {
      // Never should get here but just in case something crazy happens
      alert('An event must be selected.');
    }

    var _this = this;

    $('.mpdt_test_webhook .mpdt_rolling').show();

    var args = {
      'action': 'mpdt_send_event',
      'event': evt
    };

    $.post(ajaxurl, args, function(data) {
      $('.mpdt_test_webhook .mpdt_rolling').hide();

      if(typeof data.errors != 'undefined') {
        $('.mpdt_test_webhook .mpdt_test_webhook_error').html(data.errors[0]);
        $('.mpdt_test_webhook .mpdt_test_webhook_error').show().delay(3000).fadeOut();
      }
      else {
        $('.mpdt_test_webhook .mpdt_test_webhook_message').html(data.message);
        $('.mpdt_test_webhook .mpdt_test_webhook_message').show().delay(3000).fadeOut();
      }
    },'json');

  });

  $('a.mpdt-clipboard').on('click', function(e) {
    e.preventDefault();
  });
  let clipboard = new ClipboardJS('.mpdt-clipboard');
  let copy_text = 'Copy to Clipboard';
  $('.mpdt-clipboard').tooltipster({
    theme: 'tooltipster-borderless',
    content: copy_text,
    trigger: 'custom',
    triggerClose: {
      mouseleave: true,
      touchleave: true
    },
    triggerOpen: {
      mouseenter: true,
      touchstart: true
    }
  });
  clipboard.on('success', function(e) {
    let tooltip = $(e.trigger).tooltipster('instance');
    tooltip.content('Copied!')
    .one('after', function(){
      tooltip.content(copy_text);
    });
  })
  .on('error', function(e) {
    let tooltip = $(e.trigger).tooltipster('instance');
    tooltip.content('Oops, Copy Failed!')
    .one('after', function(){
      instance.content(copy_text);
    });
  });

  $('.mpdt-regenerate').on('click', function(e) {
    e.preventDefault();
    let confirmation = confirm('Are you sure? If you proceed, your existing key will no longer work and all applications using it will need to be updated with the newly regenerated key.');
    if(confirmation) {
      let args = {
        'action': 'mpdt_regen_api_key',
        'regen_api_key_nonce': MPDT.regen_api_key_nonce,
      };
      $.get(ajaxurl, args, function(response) {
        let tooltip = $('.mpdt-regenerate').tooltipster('instance');
        tooltip.content('API Key Generated!')
        .one('after', function(){
          tooltip.content('Regenerate API Key');
        });
        $('#mpdt_api_key').val(response.data.api_key);
      });
    }
  });

  $('.mpdt-regenerate').tooltipster({
    theme: 'tooltipster-borderless',
    content: 'Regenerate API Key',
    trigger: 'custom',
    triggerClose: {
      mouseleave: true,
      touchleave: true
    },
    triggerOpen: {
      mouseenter: true,
      touchstart: true
    }
  });
});

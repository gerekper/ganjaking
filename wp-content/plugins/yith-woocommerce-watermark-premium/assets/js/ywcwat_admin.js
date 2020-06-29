/**
 * Created by Your Inspiration on 16/09/2015.
 */

jQuery(document).ready(function ($) {

  var get_html_result = function ($attach_id, $response) {
    var html = '';


    for (var i = 0, l = $response.length; i < l; i++) {
      var result = $response[i],
        error = ywcwat_params.messages.error_messages[result[0]],
        shop_size = ywcwat_params.messages.shop_sizes[result[1]];

      var p = ywcwat_params.messages.log_message + ' ' + $attach_id + ' ' + error + ' ' + ywcwat_params.messages.on + ' ' + shop_size;

      html += '<p>' + p + '</p>';
    }

    return html;

  };


  var current_index = 0;

  $(document).on('click', '.ywcwat_apply_all_watermark', function (e) {
    e.preventDefault();

    var t = $(this),
      attach_ids = JSON.parse(ywcwat_params.attach_id),
      total_img = attach_ids.length,
      progressbar = $('#ywcwat-progressbar_all'),
      progressbar_perc = $('#ywcwat-progressbar-percent_all'),
      message_container = $('.ywcwat_messages');


    progressbar.parents('tr').show();


    message_container.hide();
    //initialize progressbar
    progressbar.progressbar();


    progressbar.show();

    if (current_index == total_img) {
      progressbar_perc.html("0%");
      current_index = 0;
    }

    var i = 0;

    t.prop('disabled', true);


    function ApplyAllWatermark(attach_id) {

      var data = {
        ywcwat_attach_id: attach_id,
        action: ywcwat_params.actions.apply_all_watermark
      };

      $.ajax({

        type: 'POST',
        url: ywcwat_params.ajax_url,
        data: data,
        dataType: 'json',
        success: function (response) {
          //finish
          if (current_index == total_img && typeof response.result == 'undefined') {

            message_container.removeClass('complete_task').addClass('complete_all_task');
            message_container.find('.ywcwat_icon').addClass('dashicons dashicons-yes');
            message_container.find('.ywcwat_text').html(ywcwat_params.messages.complete_all_task);
            message_container.show();
            t.prop('disabled', false);
            progressbar.hide();
          } else {

            var html = get_html_result(attach_id, response);

            $('.ywcwat_log_row td').show();
            $('#ywcwat_log_container').append(html);
            progressbar.progressbar("value", ((current_index + 1) / total_img) * 100);
            progressbar_perc.html(Math.round(((current_index + 1) / total_img) * 1000) / 10 + "%");

            if (current_index < total_img) {

              current_index++;
              ApplyAllWatermark(attach_ids[current_index]);
            }

          }
        }
      });

    }

    ApplyAllWatermark(attach_ids[current_index]);

  });

  $(document).on('click', '#ywcwat_reset_watermark', function (e) {

    var t = $(this);

    var answer = window.confirm(ywcwat_params.messages.reset_confirm);

    if (answer) {

      var data = {
        ywcwat_reset_watermark: 'reset',
        action: ywcwat_params.actions.reset_watermark
      };

      $.ajax({

        type: 'POST',
        url: ywcwat_params.ajax_url,
        data: data,
        dataType: 'json',
        success: function (response) {

          $('.ywcwat_messages').addClass('complete_task');
          $('.ywcwat_icon').addClass('dashicons dashicons-yes');

          var text;

          if (response.success == 0 || response.success > 1) {

            text = response.success + " " + ywcwat_params.messages.plural_success_image + ", ";

          } else {
            text = response.success + " " + ywcwat_params.messages.singular_success_image + ", ";
          }

          if (response.error == 0 || response.error > 1) {

            text += response.error + " " + ywcwat_params.messages.plural_error_image + ", ";

          } else {

            text += response.error + " " + ywcwat_params.messages.singular_error_image + ", ";
          }

          $('.ywcwat_text').html(text);
          $('.ywcwat_messages').show();

        }
      });
    }


  });

  $(document).on('click', '#ywcwat_show_log', function (e) {
    e.preventDefault();
    var old_text = $(this).val(),
      new_text = $(this).data('hide_log');
    $(this).val(new_text).data('hide_log', old_text);
    $('#ywcwat_log_container').slideToggle('slow');
  });

  $(document).on('click', '#ywcwat_apply_product', function (e) {
    e.preventDefault();

    var product_id = $(this).data('product_id'),
      data = {
        product_id: product_id,
        action: ywcwat_params.actions.save_watermark_on_single_product
      };

    $.ajax({
      type: 'POST',
      url: ywcwat_params.ajax_url,
      data: data,
      dataType: 'json',
      beforeSend: function () {
        t.siblings('.ajax-loading').css('visibility', 'visible');
      },
      complete: function () {
        t.siblings('.ajax-loading').css('visibility', 'hidden');
      },
      success: function (response) {


      }
    });
  });


  /**WATERMARK PREMIUM CODE */

  var collapse = $('.ywcwat-collapse'),
    image_file_frame,
    block_loader = ywcwat_params.block_loader,
    dialog = $("#ywcwat_preview_image").dialog({
      resizable: false,
      autoOpen: false,
      height: "auto",
      width: 400,
      modal: true,
      buttons: {
        Ok: function () {
          $(this).dialog("close");

        }
      }
    }),
    get_row_index = function (element) {

      var id = element.parents('.ywcwat_row').attr('id');

      return id;
    },
    init_watermark_admin_field = function () {

      /*Load a new Image*/
      $(document).on('click', '.ywcwat_load_image_watermark', function (e) {

        e.preventDefault();
        var t = $(this),
          this_row = $('#' + get_row_index(t));
        watermark_url = this_row.find('.ywcwat_url');
        watermark_id = this_row.find('input:hidden[id^="ywcwat_watermark_id-"]');
        table_position = this_row.find('.ywcwat_bottom_right');

        // If the media frame already exists, reopen it.
        if (image_file_frame) {
          image_file_frame.open();
          return;
        }

        var downloadable_file_states = [
          // Main states.
          new wp.media.controller.Library({
            library: wp.media.query({type: 'image'}),
            multiple: false,
            title: t.data('choose'),
            priority: 20,
            filterable: 'all'
          })
        ];

        // Create the media frame.
        image_file_frame = wp.media.frames.downloadable_file = wp.media({
          // Set the title of the modal.
          title: t.data('choose'),
          library: {
            type: 'image'
          },
          button: {
            text: t.data('choose')
          },
          multiple: false,
          states: downloadable_file_states
        });

        // When an image is selected, run a callback.
        image_file_frame.on('select', function () {


          var selection = image_file_frame.state().get('selection');

          selection.map(function (attachment) {

            attachment = attachment.toJSON();

            if (attachment.url) {
              watermark_id.val(attachment.id);
              watermark_url.val(attachment.url);
              table_position.click();
            }

          });
        });

        // Finally, open the modal.
        image_file_frame.open();

      });

      /* Show / hide watermark fields */
      $('.ywcwat_select_type_wat').on('change', function (e) {


        var t = $(this),
          value_select = t.val(),
          this_row = $('#' + get_row_index(t)),
          text_field_container = this_row.find('.ywcwat_text_field_container'),
          img_field_container = this_row.find('.ywcwat_img_field_container'),
          general_field_container = this_row.find('.ywcwat_general_field_container');


        switch (value_select) {

          case 'type_text' :
            img_field_container.hide();
            text_field_container.show();
            general_field_container.show();

            break;
          case 'type_img' :
            img_field_container.show();
            text_field_container.hide();
            general_field_container.show();
            break;
          case 'no':
            img_field_container.hide();
            text_field_container.hide();
            general_field_container.hide();
            break;

        }

      }).change();

      /*
          Remove the watermark
       */
      var lock = false;
      $(document).on('click', '.ywcwat_remove_watermark', function (e) {
        e.preventDefault();
        if (!lock) {
          var t = $(this),
            table_to_remove_id = t.data('element_id');

          var answer = window.confirm(ywcwat_params.delete_single_watermark.confirm_delete_watermark);

          if (answer) {
            var row_to_delete = $('#ywcwat_row-' + table_to_remove_id),
              watermark_id = row_to_delete.find('#ywcwat_id-' + table_to_remove_id).val(),
              data = {
                ywcwat_unique_id: watermark_id,
                action: ywcwat_params.actions.remove_watermark
              };

            row_to_delete.block({
              message: null,
              overlayCSS: {
                background: '#fff url(' + block_loader + ') no-repeat center',
                opacity: 0.5,
                cursor: 'none'
              }
            });
            $.ajax({

              type: 'POST',
              url: ywcwat_params.ajax_url,
              data: data,
              dataType: 'json',
              success: function (response) {

                $('#ywcwat_row-' + table_to_remove_id).remove();
                init_watermark_admin_field();
              }

            });
            lock = true;
          }
        }
        return false;
      });

      $(document).on('click', '.ywcwat_container_position tr td', function (e) {

        var t = $(this);

        show_watermark_position(t);


      });

      $(document).on('click', '.ywcwat_preview', function (e) {
        e.preventDefault();

        var t = $(this),
          watermark_id = t.data('watermark_id'),
          row = t.parents('.ywcwat_field'),
          field = row.find('input, select, checkbox').serialize(),
          data = {
            ywcwat_id: watermark_id,
            action: ywcwat_params.actions.preview_watermark,
            ywcwat_args: field,
          };


        $.ajax({

          type: 'POST',
          url: ywcwat_params.ajax_url,
          data: data,
          dataType: 'json',
          success: function (response) {


            if (response.result) {
              $('#ywcwat_preview_image').find('img').attr('src', response.message).show();
            } else {
              $('#ywcwat_preview_image').html('<p>' + response.message + '</p>');
            }
            dialog.dialog("open");

          }

        });


      });

      $('#ywcwat_preview_image').on('dialogclose', function (event) {
        $(this).find('img').attr('src', '');
      });

    },
    show_watermark_position = function ($current_table_cell) {
      var table = $current_table_cell.parent().parent(),
        this_row = table.parents('.ywcwat_column_position'),
        text_position = this_row.find('.ywcwat_text_position'),
        hidden_field_pos = this_row.find('.ywcwat_watermark_position:eq(0)');

      table.find('td').removeClass('position_select');
      $current_table_cell.addClass('position_select');


      if ($current_table_cell.hasClass("ywcwat_top_left")) {

        hidden_field_pos.val('top_left');
        text_position.html(ywcwat_params.label_position.top_left);

      } else if ($current_table_cell.hasClass("ywcwat_top_center")) {

        hidden_field_pos.val('top_center');
        text_position.html(ywcwat_params.label_position.top_center);
      } else if ($current_table_cell.hasClass("ywcwat_top_right")) {

        hidden_field_pos.val('top_right');
        text_position.html(ywcwat_params.label_position.top_right);

      } else if ($current_table_cell.hasClass("ywcwat_middle_left")) {


        hidden_field_pos.val('middle_left');
        text_position.html(ywcwat_params.label_position.middle_left);

      } else if ($current_table_cell.hasClass("ywcwat_middle_center")) {


        hidden_field_pos.val('middle_center');
        text_position.html(ywcwat_params.label_position.middle_center);

      } else if ($current_table_cell.hasClass("ywcwat_middle_right")) {


        hidden_field_pos.val('middle_right');
        text_position.html(ywcwat_params.label_position.middle_right);


      } else if ($current_table_cell.hasClass("ywcwat_bottom_left")) {


        hidden_field_pos.val('bottom_left');
        text_position.html(ywcwat_params.label_position.bottom_left);

      } else if ($current_table_cell.hasClass("ywcwat_bottom_center")) {


        hidden_field_pos.val('bottom_center');
        text_position.html(ywcwat_params.label_position.bottom_center);

      } else {
        //bottom right default

        hidden_field_pos.val('bottom_right');
        text_position.html(ywcwat_params.label_position.bottom_right);
      }
    }


  collapse.each(function () {
    $(this).toggleClass('expand').nextUntil('tr.ywcwat-collapse').slideToggle('fast');
  });

  $(document).on('click', '.ywcwat-collapse', function () {
    $(this).toggleClass('expand').nextUntil('tr.ywcwat-collapse').slideToggle('fast');
  });

  $('body').on('ywcwat-init-admin-fields', function () {

    init_watermark_admin_field();
    $(document).find('.ywcwat_container_position tr td.position_select').click();
    $(document.body).trigger('wc-enhanced-select-init');
    $(document.body).trigger('yith-framework-enhanced-select-init');
    $('.ywcwat_color_picker').wpColorPicker();
  }).trigger('ywcwat-init-admin-fields');


});

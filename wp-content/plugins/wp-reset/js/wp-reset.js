/**
 * WP Reset
 * https://wpreset.com/
 * (c) WebFactory Ltd, 2017-2020
 */

jQuery(document).ready(function ($) {
  // init tabs
  $("#wp-reset-tabs")
    .tabs({
      create: function () {
        $("#loading-tabs").remove();
      },
      activate: function (event, ui) {
        localStorage.setItem(
          "wp-reset-tabs",
          $("#wp-reset-tabs").tabs("option", "active")
        );
      },
      active: localStorage.getItem("wp-reset-tabs") || 0,
    })
    .show();

  // helper for swithcing tabs & linking anchors in different tabs
  $(".tools_page_wp-reset").on("click", ".change-tab", function (e) {
    e.preventDefault();

    $("#wp-reset-tabs").tabs("option", "active", $(this).data("tab"));

    // get the link anchor and scroll to it
    target = this.href.split("#")[1];
    if (target) {
      $.scrollTo("#" + target, 500, { offset: { top: -50, left: 0 } });
    }

    $(this).blur();
    return false;
  }); // jump to tab/anchor helper

  // helper for scrolling to anchor
  $(".tools_page_wp-reset").on("click", ".scrollto", function (e) {
    e.preventDefault();

    // get the link anchor and scroll to it
    target = this.href.split("#")[1];
    if (target) {
      $.scrollTo("#" + target, 500, { offset: { top: -50, left: 0 } });
    }

    $(this).blur();
    return false;
  }); // scroll to anchor helper

  // toggle button dropdown menu
  $(".tools_page_wp-reset").on("click", ".button.dropdown-toggle", function (
    e
  ) {
    e.preventDefault();

    parent_dropdown = $(this).parent(".dropdown");
    sibling_menu = $(this).siblings(".dropdown-menu");

    $(".dropdown").not(parent_dropdown).removeClass("show");
    $(".dropdown-menu").not(sibling_menu).removeClass("show");

    $(parent_dropdown).toggleClass("show");
    $(sibling_menu).toggleClass("show");

    return false;
  }); // toggle button dropdown menu

  $(document).on(
    "click",
    ":not(.dropdown-toggle), .dropdown-item",
    function () {
      wpr_close_dropdowns();
    }
  );

  // delete transients
  $(".tools_page_wp-reset").on("click", "#delete-transients", function (e) {
    e.preventDefault();

    run_tool(this, "delete_transients");

    return false;
  }); // delete transients

  // purge cache
  $(".tools_page_wp-reset").on("click", "#purge-cache", function (e) {
    e.preventDefault();

    run_tool(this, "purge_cache");

    return false;
  }); // purge cache

  // delete uploads
  $(".tools_page_wp-reset").on("click", "#delete-uploads", function (e) {
    e.preventDefault();

    run_tool(this, "delete_uploads");

    return false;
  }); // delete uploads

  // reset theme options (mods)
  $(".tools_page_wp-reset").on("click", "#reset-theme-options", function (e) {
    e.preventDefault();

    run_tool(this, "reset_theme_options");

    return false;
  }); // reset theme options

  // delete themes
  $(".tools_page_wp-reset").on("click", "#delete-themes", function (e) {
    e.preventDefault();

    run_tool(this, "delete_themes");

    return false;
  }); // delete themes

  // delete plugins
  $(".tools_page_wp-reset").on("click", "#delete-plugins", function (e) {
    e.preventDefault();

    var button = this;
    var confirm_title =
      $(button).data("confirm-title") || wp_reset.confirm_title;

    var tools = [
      {
        tool: "deactivate_plugins",
        description: "Deactivating plugins.",
        extra_data: { keep_default_theme: 0, keep_current_theme: 0 },
      },
      {
        tool: "delete_plugins",
        description: "Deleting plugins.",
        extra_data: {
          keep_wp_reset: 1,
          silent_deactivate: 0,
        },
      },
    ];
    var looper = $.Deferred().resolve();

    confirm_action(
      confirm_title,
      $(button).data("text-confirm"),
      $(button).data("btn-confirm") || $(button).text(),
      wp_reset.cancel_button
    ).then((result) => {
      if (!result.value) {
        return false;
      }

      wpr_swal.fire({
        title: $(button).data("text-wait"),
        text: " ",
        type: false,
        allowOutsideClick: false,
        allowEscapeKey: false,
        allowEnterKey: false,
        showConfirmButton: false,
        imageUrl: wp_reset.icon_url,
        onOpen: () => {
          $(wpr_swal.getImage()).addClass("wpr_rotating");
        },
        imageWidth: 100,
        imageHeight: 100,
        imageAlt: $(button).data("text-wait"),
      });

      var failed = false;
      $.each(tools, function (i, data) {
        looper = looper.then(function () {
          if (failed) {
            return false;
          }
          wpr_swal.getContent().querySelector("#swal2-content").textContent =
            i + 1 + "/" + tools.length + " - " + data.description;
          return $.ajax({
            data: {
              action: "wp_reset_run_tool",
              _ajax_nonce: wp_reset.nonce_run_tool,
              tool: data.tool,
              extra_data: data.extra_data,
            },
            url: ajaxurl,
          })
            .done(function (response) {
              if (response.success) {
                failed = false;
                if (i == tools.length - 1) {
                  if (response.data == 1) {
                    msg = $(button).data("text-done-singular");
                  } else {
                    msg = $(button)
                      .data("text-done")
                      .replace("%n", response.data);
                  }
                  wpr_swal_params = { icon: "success", title: msg };

                  wpr_swal.fire(wpr_swal_params);
                }
              } else {
                wpr_swal.close();
                wpr_swal.fire({
                  icon: "error",
                  title: wp_reset.documented_error,
                  text: response.data,
                });
                failed = true;
                return false;
              }
            })
            .error(function (response) {
              wpr_swal.close();
              wpr_swal.fire({
                icon: "error",
                title: wp_reset.undocumented_error,
              });
              failed = true;
              return false;
            });
        });
      });
    }); // confirm ok

    return false;
  }); // delete plugins

  // drop custom tables
  $(".tools_page_wp-reset").on("click", "#drop-custom-tables", function (e) {
    e.preventDefault();

    run_tool(this, "drop_custom_tables");

    return false;
  }); // drop custom tables

  // truncate custom tables
  $(".tools_page_wp-reset").on("click", "#truncate-custom-tables", function (
    e
  ) {
    e.preventDefault();

    run_tool(this, "truncate_custom_tables");

    return false;
  }); // truncate custom tables

  // delete htaccess file
  $(".tools_page_wp-reset").on("click", "#delete-htaccess", function (e) {
    e.preventDefault();

    run_tool(this, "delete_htaccess");

    return false;
  }); // delete htaccess file

  // delete auth cookies
  $(".tools_page_wp-reset").on("click", "#delete-wp-cookies", function (e) {
    e.preventDefault();

    run_tool(this, "delete_wp_cookies");

    return false;
  }); // delete auth cookies

  // delete local data
  $(".tools_page_wp-reset").on("click", "#delete-local-data", function (e) {
    e.preventDefault();
    var button = this;
    var confirm_title =
      $(button).data("confirm-title") || wp_reset.confirm_title;

    confirm_action(
      confirm_title,
      $(button).data("text-confirm"),
      $(button).data("btn-confirm"),
      wp_reset.cancel_button
    ).then((result) => {
      if (!result.value) {
        return false;
      }

      cnt = wpr_clear_local(true, true);
      if (cnt == 1) {
        msg = $(button).data("text-done-singular");
      } else {
        msg = $(button).data("text-done").replace("%n", cnt);
      }
      wpr_swal.fire({ icon: "success", title: msg });
    });

    return false;
  }); // delete local data

  // compare snapshot
  $("#wpr-snapshots").on("click", ".compare-snapshot", "click", function (e) {
    e.preventDefault();
    uid = $(this).data("ss-uid");
    button = $(this);

    block_ui($(button).data("wait-msg"));
    $.get({
      url: ajaxurl,
      data: {
        action: "wp_reset_run_tool",
        _ajax_nonce: wp_reset.nonce_run_tool,
        tool: "compare_snapshots",
        extra_data: uid,
      },
    })
      .always(function (data) {
        wpr_swal.close();
      })
      .done(function (data) {
        if (data.success) {
          msg = $(button).data("title").replace("%s", $(button).data("name"));
          wpr_swal.fire({
            width: "90%",
            title: msg,
            html: data.data,
            showConfirmButton: false,
            allowEnterKey: false,
            focusConfirm: false,
            showCloseButton: true,
            customClass: "compare-snapshots",
          });
        } else {
          wpr_swal.fire({
            icon: "error",
            title: wp_reset.documented_error + " " + data.data,
          });
        }
      })
      .fail(function (data) {
        wpr_swal.fire({ icon: "error", title: wp_reset.undocumented_error });
      });

    return false;
  }); // compare snapshot

  // restore snapshot
  $("#wpr-snapshots").on("click", ".restore-snapshot", "click", function (e) {
    e.preventDefault();
    uid = $(this).data("ss-uid");

    run_tool(this, "restore_snapshot", uid);

    return false;
  }); // restore snapshot

  // download snapshot
  $("#wpr-snapshots").on("click", ".download-snapshot", "click", function (e) {
    e.preventDefault();
    uid = $(this).data("ss-uid");
    button = this;

    block_ui($(this).data("wait-msg"));
    $.get({
      url: ajaxurl,
      data: {
        action: "wp_reset_run_tool",
        _ajax_nonce: wp_reset.nonce_run_tool,
        tool: "download_snapshot",
        extra_data: uid,
      },
    })
      .always(function (data) {
        wpr_swal.close();
      })
      .done(function (data) {
        if (data.success) {
          msg = $(button).data("success-msg").replace("%s", data.data);
          wpr_swal.fire({ icon: "success", title: msg });
        } else {
          wpr_swal.fire({
            icon: "error",
            title: wp_reset.documented_error + " " + data.data,
          });
        }
      })
      .fail(function (data) {
        wpr_swal.fire({ icon: "error", title: wp_reset.undocumented_error });
      });

    return false;
  }); // download snapshot

  // delete snapshot
  $("#wpr-snapshots").on("click", ".delete-snapshot", "click", function (e) {
    e.preventDefault();
    uid = $(this).data("ss-uid");

    run_tool(this, "delete_snapshot", uid);

    return false;
  }); // delete snapshot

  // create snapshot
  $(".tools_page_wp-reset").on("click", ".create-new-snapshot", function (e) {
    e.preventDefault();

    description = $(this).data("description") || "";

    wpr_swal
      .fire({
        title: wp_reset.snapshot_title,
        icon: "question",
        text: wp_reset.snapshot_text,
        input: "text",
        inputValue: description,
        inputPlaceholder: wp_reset.snapshot_placeholder,
        showCancelButton: true,
        focusConfirm: false,
        confirmButtonText: wp_reset.snapshot_confirm,
        cancelButtonText: wp_reset.cancel_button,
        width: 600,
      })
      .then((result) => {
        if (typeof result.value != "undefined") {
          block = block_ui(wp_reset.snapshot_wait);
          $.get({
            url: ajaxurl,
            data: {
              action: "wp_reset_run_tool",
              _ajax_nonce: wp_reset.nonce_run_tool,
              tool: "create_snapshot",
              extra_data: result.value,
            },
          })
            .always(function (data) {
              wpr_swal.close();
            })
            .done(function (data) {
              if (data.success) {
                wpr_swal
                  .fire({
                    icon: "success",
                    title: wp_reset.snapshot_success,
                    timer: 2500,
                    timerProgressBar: true,
                    showConfirmButton: true,
                  })
                  .then((result) => {
                    location.reload();
                  });
              } else {
                wpr_swal.fire({
                  icon: "error",
                  title: wp_reset.documented_error + " " + data.data,
                });
              }
            })
            .fail(function (data) {
              wpr_swal.fire({
                icon: "error",
                title: wp_reset.undocumented_error,
              });
            });
        } // if confirmed
      });

    return false;
  }); // create snapshot

  // show/hide extra table info in snapshot diff
  $("body.tools_page_wp-reset").on("click", ".header-row", function (e) {
    e.preventDefault();

    parent = $(this).parents("div.wpr-table-container > table > tbody");
    $(" > tr:not(.header-row)", parent).toggleClass("hidden");

    $("span.dashicons", parent)
      .toggleClass("dashicons-arrow-down-alt2")
      .toggleClass("dashicons-arrow-up-alt2");

    return false;
  }); // show hide extra info in diff

  // standard way of running a tool, with confirmation, loading and success message
  function run_tool(button, tool_name, extra_data) {
    var confirm_title =
      $(button).data("confirm-title") || wp_reset.confirm_title;

    wpr_close_dropdowns();

    confirm_action(
      confirm_title,
      $(button).data("text-confirm"),
      $(button).data("btn-confirm"),
      wp_reset.cancel_button
    ).then((result) => {
      if (result.value) {
        block = block_ui($(button).data("text-wait"));
        $.get({
          url: ajaxurl,
          data: {
            action: "wp_reset_run_tool",
            _ajax_nonce: wp_reset.nonce_run_tool,
            tool: tool_name,
            extra_data: extra_data,
          },
        })
          .always(function (data) {
            wpr_swal.close();
          })
          .done(function (data) {
            if (data.success) {
              if (data.data == 1) {
                msg = $(button).data("text-done-singular");
              } else {
                msg = $(button).data("text-done").replace("%n", data.data);
              }
              wpr_swal
                .fire({
                  icon: "success",
                  title: msg,
                  timer: 2500,
                  timerProgressBar: true,
                })
                .then(() => {
                  if (tool_name == "restore_snapshot") {
                    location.reload();
                  }
                });
              if (tool_name == "delete_snapshot") {
                $("#wpr-ss-" + extra_data).remove();
                if ($("#wpr-snapshots tr").length <= 1) {
                  $("#wpr-snapshots").hide();
                  $("#ss-no-snapshots").show();
                }
              }
            } else {
              wpr_swal.fire({
                icon: "error",
                title: wp_reset.documented_error + " " + data.data,
              });
            }
          })
          .fail(function (data) {
            wpr_swal.fire({
              icon: "error",
              title: wp_reset.undocumented_error,
            });
          });
      } // if confirmed
    });
  } // run_tool

  // display a message while an action is performed
  function block_ui(message) {
    tmp = wpr_swal.fire({
      text: message,
      type: false,
      imageUrl: wp_reset.icon_url,
      onOpen: () => {
        $(wpr_swal.getImage()).addClass("rotating");
      },
      imageWidth: 100,
      imageHeight: 100,
      imageAlt: message,
      allowOutsideClick: false,
      allowEscapeKey: false,
      allowEnterKey: false,
      showConfirmButton: false,
    });

    return tmp;
  } // block_ui

  // display dialog to confirm action
  function confirm_action(title, question, btn_confirm, btn_cancel) {
    tmp = wpr_swal.fire({
      title: title,
      icon: "question",
      html: question,
      showCancelButton: true,
      focusConfirm: false,
      focusCancel: true,
      confirmButtonText: btn_confirm,
      cancelButtonText: btn_cancel,
      confirmButtonColor: "#dd3036",
      width: 650,
    });

    return tmp;
  } // confirm_action

  $("#wp_reset_form").on("submit", function (e, confirmed) {
    if (!confirmed) {
      $("#wp_reset_submit").trigger("click");
      e.preventDefault();
      return false;
    }

    $(this).off("submit").submit();
    return true;
  }); // bypass default submit behaviour

  $("#wp_reset_submit").click(function (e) {
    if ($("#wp_reset_confirm").val() !== "reset") {
      wpr_swal.fire({
        title: wp_reset.invalid_confirmation_title,
        text: wp_reset.invalid_confirmation,
        icon: "error",
        confirmButtonText: wp_reset.ok_button,
      });

      e.preventDefault();
      return false;
    } // wrong confirmation code

    message = wp_reset.confirm1 + "<br>" + wp_reset.confirm2;
    wpr_swal
      .fire({
        title: wp_reset.confirm_title_reset,
        icon: "question",
        html: message,
        showCancelButton: true,
        focusConfirm: false,
        focusCancel: true,
        confirmButtonText: wp_reset.confirm_button,
        cancelButtonText: wp_reset.cancel_button,
        confirmButtonColor: "#dd3036",
        width: 600,
      })
      .then((result) => {
        if (result.value === true) {
          block_ui(wp_reset.doing_reset);
          $("#wp_reset_form").trigger("submit", true);
        }
      });

    e.preventDefault();
    return false;
  }); // reset submit

  // collapse / expand card
  $(".tools_page_wp-reset").on("click", ".toggle-card", function (
    e,
    skip_anim
  ) {
    e.preventDefault();

    card = $(this).parents(".card").toggleClass("collapsed");
    $(".dashicons", this)
      .toggleClass("dashicons-arrow-up-alt2")
      .toggleClass("dashicons-arrow-down-alt2");
    $(this).blur();

    if (typeof skip_anim != "undefined" && skip_anim) {
      $(card).find(".card-body").toggle();
    } else {
      $(card).find(".card-body").slideToggle(500);
    }

    cards = localStorage.getItem("wp-reset-cards");
    if (cards == null) {
      cards = new Object();
    } else {
      cards = JSON.parse(cards);
    }

    card_id = card.attr("id") || $("h4", card).attr("id") || "";

    if (card.hasClass("collapsed")) {
      cards[card_id] = "collapsed";
    } else {
      cards[card_id] = "expanded";
    }
    localStorage.setItem("wp-reset-cards", JSON.stringify(cards));

    return false;
  }); // toggle-card

  // handle saved and preset cards' state
  cards_state = localStorage.getItem("wp-reset-cards");
  if (cards_state != null) {
    cards_state = JSON.parse(cards_state);
  } else {
    cards_state = new Object();
  }

  cards = $(".card");
  $.each(cards, function (index, card) {
    card_name = $("h4", card).attr("id");
    if (!card_name) {
      return;
    }

    if (
      typeof cards_state == "object" &&
      cards_state.hasOwnProperty(card_name)
    ) {
      if (cards_state[card_name] == "collapsed") {
        $("a.toggle-card", card).trigger("click", true);
      }
    } else {
      if ($(card).hasClass("default-collapsed")) {
        $("a.toggle-card", card).trigger("click", true);
      }
    }
  });

  // dismiss notice / pointer
  $(".wpr-dismiss-notice").on("click", function (e) {
    notice_name = $(this).data("notice");
    if (!notice_name) {
      return true;
    }

    if ($(this).data("survey")) {
      $("#survey-dialog").dialog("close");
    }

    $.get(ajaxurl, {
      notice_name: notice_name,
      _ajax_nonce: wp_reset.nonce_dismiss_notice,
      action: "wp_reset_dismiss_notice",
    });

    $(this).parents(".notice-wrapper").fadeOut();

    e.preventDefault();
    return false;
  }); // dismiss notice

  // maybe init survey dialog
  if (wp_reset.open_survey) {
    $("#survey-dialog").dialog({
      dialogClass: "wp-dialog wpr-dialog wpr-survey-dialog",
      modal: 1,
      resizable: false,
      width: 800,
      height: "auto",
      show: "fade",
      hide: "fade",
      close: function (event, ui) {},
      open: function (event, ui) {
        wpr_fix_dialog_close(event, ui);
      },
      autoOpen: true,
      closeOnEscape: true,
    });
  }

  // turn questions into checkboxes
  $(".question-wrapper").on("click", function (e) {
    if ($(this).hasClass("selected")) {
      $(this).removeClass("selected");
    } else {
      if ($(".question-wrapper.selected").length >= 2) {
        wpr_swal.fire({
          icon: "error",
          allowOutsideClick: false,
          text: "You can choose only up to 2 features at a time.",
        });
      } else {
        $(this).addClass("selected");
      }
    }

    e.preventDefault();
    return false;
  });

  // submit and hide survey
  $(".submit-survey").on("click", function (e) {
    if (
      $(".question-wrapper.selected").length != 2 &&
      $(".question-wrapper.selected").length != 1
    ) {
      wpr_swal.fire({
        icon: "error",
        allowOutsideClick: false,
        text: "Please choose 1 or 2 features you would like us to build next.",
      });
      return false;
    }

    if (
      $("#survey-dialog .custom-input").val() == "" &&
      $("#survey-dialog .custom-input")
        .parents("div.question-wrapper")
        .hasClass("selected")
    ) {
      wpr_swal.fire({
        icon: "error",
        allowOutsideClick: false,
        text: "Please describe the custom feature you need.",
      });
      return false;
    }

    answers = "";
    $(".question-wrapper.selected").each(function (i, el) {
      answers += $(el).data("value") + ",";
    });

    $.post(ajaxurl, {
      survey: "features",
      answers: answers,
      emailme: $("#survey-dialog #emailme:checked").val(),
      custom_answer: $("#survey-dialog .custom-input").val(),
      _ajax_nonce: wp_reset.nonce_submit_survey,
      action: "wp_reset_submit_survey",
    });

    $("#survey-dialog").dialog("close");
    wpr_swal.fire({
      icon: "success",
      text: "Thank you for your time! We appriciate your input!",
    });

    e.preventDefault();
    return false;
  });

  $(".tools_page_wp-reset").on("click", ".open-webhooks-dialog", function (e) {
    $(this).blur();
    $("#webhooks-dialog").dialog("open");

    e.preventDefault();
    return false;
  });

  // webhooks dialog init
  $("#webhooks-dialog").dialog({
    dialogClass: "wp-dialog wpr-dialog webhooks-dialog",
    modal: 1,
    resizable: false,
    title: "WP Webhooks - Connect WordPress to any 3rd party system",
    width: 550,
    height: "auto",
    show: "fade",
    hide: "fade",
    open: function (event, ui) {
      wpr_fix_dialog_close(event, ui);
      $(this)
        .siblings()
        .find("span.ui-dialog-title")
        .html(wp_reset.webhooks_dialog_title);
    },
    close: function (event, ui) {},
    autoOpen: false,
    closeOnEscape: true,
  });
  $(window).resize(function (e) {
    $("#webhooks-dialog").dialog("option", "position", {
      my: "center",
      at: "center",
      of: window,
    });
  });

  jQuery("#install-webhooks").on("click", function (e) {
    $("#webhooks-dialog").dialog("close");
    jQuery("body").append(
      '<div style="width:550px;height:450px; position:fixed;top:10%;left:50%;margin-left:-275px; color:#444; background-color: #fbfbfb;border:1px solid #DDD; border-radius:4px;box-shadow: 0px 0px 0px 4000px rgba(0, 0, 0, 0.85);z-index: 9999999;"><iframe src="' +
        wp_reset.webhooks_install_url +
        '" style="width:100%;height:100%;border:none;" /></div>'
    );
    jQuery("#wpwrap").css("pointer-events", "none");
    e.preventDefault();
    return false;
  });

  // todo: not finished
  $(".tools_page_wp-reset").on(
    "click",
    ".button-pro-feature, .pro-feature",
    function (e) {
      e.preventDefault();
      this.blur();

      tool_id =
        $(this).data("feature") || $(".pro-feature", this).data("feature");
      if (!tool_id) {
        $("#wp-reset-tabs").tabs("option", "active", 5);
        $.scrollTo($("#pro-pricing"), 500, { offset: { top: -50, left: 0 } });
        return;
      }

      details = $("#pro-feature-details-" + tool_id);
      if (details.length != 1) {
        $("#wp-reset-tabs").tabs("option", "active", 5);
        $.scrollTo($("#pro-pricing"), 500, { offset: { top: -50, left: 0 } });
        return;
      }

      wpr_swal
        .fire({
          title: tool_id,
          html: "Dialog content",
          footer:
            'See everything WP Reset PRO offers on &nbsp;<a target="_blank" href="https://wpreset.com">wpreset.com</a>',
          icon: "",
          showCloseButton: true,
          focusConfirm: true,
          confirmButtonText: "Grab the 30% discount",
        })
        .then((result) => {
          if (result.value) {
            $("#wp-reset-tabs").tabs("option", "active", 5);
            $.scrollTo($("#pro-pricing"), 500, {
              offset: { top: -50, left: 0 },
            });
          }
        });

      return false;
    }
  );

  $("#show-table-details").on("click", function (e) {
    e.preventDefault();

    $.get({
      url: ajaxurl,
      data: {
        action: "wp_reset_run_tool",
        _ajax_nonce: wp_reset.nonce_run_tool,
        tool: "get_table_details",
      },
    })
      .done(function (data) {
        if (data.success) {
          $("#wpr-table-details").html(data.data);
        } else {
          wpr_swal.fire({
            type: "error",
            title: wp_reset.documented_error + " " + data.data,
          });
        }
      })
      .fail(function (data) {
        wpr_swal.fire({ type: "error", title: wp_reset.undocumented_error });
      });

    return false;
  }); // show table details

  $("#wpr-save-license").on("click", function (e) {
    e.preventDefault();

    button = this;
    safe_refresh = true;

    block = block_ui($(button).data("text-wait"));
    wf_licensing_verify_licence_ajax(
      "wpr",
      $("#wpr-license-key").val(),
      button
    );

    return;
  }); // license activation button

  $("#wpr-keyless-activation").on("click", function (e) {
    e.preventDefault();

    button = this;
    safe_refresh = true;

    block = block_ui($(button).data("text-wait"));
    wf_licensing_verify_licence_ajax("wpr", "keyless", button);

    return;
  }); // keyless license activation button

  $("#wpr-deactivate-license").on("click", function (e) {
    e.preventDefault();

    button = this;
    safe_refresh = true;

    block = block_ui($(button).data("text-wait"));
    wf_licensing_deactivate_licence_ajax(
      "wpr",
      $("#wpr-license-key").val(),
      button
    );

    return;
  }); // deactivate license button

  $("#wpr-license-key").on("keypress", function (e) {
    if (e.keyCode == 13) {
      e.preventDefault();
      $("#wpr-save-license").trigger("click");
    }
  }); // trigger license save on enter

  // Collections
  // for demo purposes only
  var collections_ajax_queue = [];
  var collections_ajax_queue_count = 0;
  var collections_ajax_queue_index = 0;
  var collections_errors = [];
  var collections_retried = false;
  wp_reset.collections = [];
  wp_reset.collections[1] = {
    id: 1,
    name: "Must-have WordPress Plugins",
    created: "2020-04-01",
    items: [],
  };
  wp_reset.collections[1].items[10] = {
    id: "10",
    type: "plugin",
    source: "repo",
    note: "",
    slug: "eps-301-redirects",
    name: "301 Redirects",
    version: "0.4",
  };

  wp_reset.collections[1].items[11] = {
    id: "11",
    type: "plugin",
    source: "repo",
    note: "",
    slug: "classic-editor",
    name: "Classic Editor",
    version: "0.4",
  };

  wp_reset.collections[1].items[12] = {
    id: "12",
    type: "plugin",
    source: "repo",
    note: "",
    slug: "simple-author-box",
    name: "Simple Author Box",
    version: "0.4",
  };

  wp_reset.collections[1].items[13] = {
    id: "13",
    type: "plugin",
    source: "repo",
    note: "",
    slug: "sticky-menu-or-anything-on-scroll",
    name: "Sticky Menu (or Anything!) on Scroll",
    version: "0.4",
  };

  wp_reset.collections[1].items[14] = {
    id: "14",
    type: "plugin",
    source: "repo",
    note: "",
    slug: "under-construction-page",
    name: "UnderConstructionPage",
    version: "0.4",
  };

  wp_reset.collections[1].items[15] = {
    id: "15",
    type: "plugin",
    source: "repo",
    note: "",
    slug: "wp-external-links",
    name: "WP External Links",
    version: "0.4",
  };

  $(document).on("click", ".install-collection", function (e) {
    do_delete = $(this).data("delete");
    do_activate = $(this).data("activate");
    collection_id = $(this).parents(".card").data("collection-id");
    show_install_collection(collection_id, do_delete, do_activate);
  });

  // install collection item
  $(".tools_page_wp-reset").on("click", ".install-collection-item", function (
    e
  ) {
    e.preventDefault();

    wpr_close_dropdowns();

    collection_id = $(this).parents(".card").data("collection-id");
    collection_item_id = $(this).parents("tr").data("collection-item-id");
    do_activate = $(this).data("activate");

    item_data =
      wp_reset.collections[collection_id]["items"][collection_item_id];

    wpr_swal
      .fire({
        title: "Installing <br />" + item_data["name"],
        html: '<div class="wpr-collections-installer"></div>',
        width: 600,
        onRender: function () {
          collections_ajax_queue.push({
            slug: item_data.slug,
            name: item_data.name,
            extra_data: {
              source: item_data.source,
              collection_id: collection_id,
              collection_item_id: collection_item_id,
            },
            action: "install_" + item_data.type,
          });
          collections_ajax_queue.push({
            slug: item_data.slug,
            name: item_data.name,
            action: "check_install_" + item_data.type,
          });

          $(".wpr-collections-installer").append(
            '<div class="wpr-collections-installer-message" data-action="' +
              item_data.slug +
              "_install_" +
              item_data.type +
              '"><span class="dashicons"></span>' +
              wp_reset.installing +
              " " +
              item_data.name +
              "</div>"
          );

          if (do_activate) {
            collections_ajax_queue.push({
              slug: item_data.slug,
              name: item_data.name,
              action: "activate_" + item_data.type,
              extra_data: {
                source: item_data.source,
                collection_id: collection_id,
                collection_item_id: collection_item_id,
              },
            });
            collections_ajax_queue.push({
              slug: item_data.slug,
              name: item_data.name,
              action: "check_activate_" + item_data.type,
            });

            $(".wpr-collections-installer").append(
              '<div class="wpr-collections-installer-message" data-action="' +
                item_data.slug +
                "_activate_" +
                item_data.type +
                '"><span class="dashicons"></span>' +
                wp_reset.activating +
                " " +
                item_data.name +
                "</div>"
            );
          }
          collections_do_ajax();
        },
        allowOutsideClick: false,
        allowEscapeKey: false,
        allowEnterKey: false,
        showCancelButton: true,
        confirmButtonText: "OK",
      })
      .then((result) => {
        collections_ajax_queue = [];
      });

    return false;
  }); // install collection item

  // show install collection popup
  function show_install_collection(collection_id, do_delete, do_activate) {
    wpr_swal
      .fire({
        title:
          "Installing collection<br />" +
          wp_reset.collections[collection_id]["name"],
        html: '<div class="wpr-collections-installer"></div>',
        width: 600,
        onRender: function () {
          install_collection(collection_id, do_activate, do_delete);
        },
        allowOutsideClick: false,
        allowEscapeKey: false,
        allowEnterKey: false,
        showCancelButton: true,
        showConfirmButton: false,
        confirmButtonText: "OK",
      })
      .then((result) => {
        collections_ajax_queue = [];
      });
  } // show install collection popup

  // create install collection queue and add popup content
  function install_collection(collection_id, do_activate, do_delete) {
    collections_ajax_queue = [];
    collections_ajax_queue_count = 0;
    collections_ajax_queue_index = 0;
    collections_errors = [];

    console.log(wp_reset.collections[collection_id]);

    for (item in wp_reset.collections[collection_id]["items"]) {
      item_data = wp_reset.collections[collection_id]["items"][item];
      collections_ajax_queue.push({
        slug: item_data.slug,
        name: item_data.name,
        extra_data: {
          source: item_data.source,
          collection_id: collection_id,
          collection_item_id: item_data.id,
        },
        action: "install_" + item_data.type,
      });
      collections_ajax_queue.push({
        slug: item_data.slug,
        name: item_data.name,
        action: "check_install_" + item_data.type,
      });

      if (do_activate) {
        collections_ajax_queue.push({
          slug: item_data.slug,
          name: item_data.name,
          action: "activate_" + item_data.type,
          extra_data: {
            source: item_data.source,
            collection_id: collection_id,
            collection_item_id: item_data.id,
          },
        });
        collections_ajax_queue.push({
          slug: item_data.slug,
          name: item_data.name,
          action: "check_activate_" + item_data.type,
        });
      }
    }

    for (ci in collections_ajax_queue) {
      var message = false;
      collections_ajax_queue_count++;

      switch (collections_ajax_queue[ci].action) {
        case "deactivate_plugin":
          message = wp_reset.deactivating;
          break;
        case "delete_plugin":
        case "delete_theme":
          message = wp_reset.deleting;
          break;
        case "install_plugin":
        case "install_theme":
          message = wp_reset.installing;
          break;
        case "activate_plugin":
        case "activate_theme":
          message = wp_reset.activating;
          break;
        case "activate_license_plugin":
        case "activate_license_theme":
          message = wp_reset.activating_license;
          break;
      }

      if (message !== false) {
        $(".wpr-collections-installer").append(
          '<div class="wpr-collections-installer-message" data-action="' +
            collections_ajax_queue[ci].slug +
            "_" +
            collections_ajax_queue[ci].action +
            '"><span class="dashicons"></span> ' +
            message +
            " " +
            collections_ajax_queue[ci].name +
            "</div>"
        );
      }
    }

    collections_ajax_queue_count = collections_ajax_queue.length;
    collections_do_ajax();
  } // create install collection queue

  // run collection ajax
  function collections_do_ajax() {
    collection_item = collections_ajax_queue[collections_ajax_queue_index];
    message_id =
      collection_item.slug + "_" + collection_item.action.replace("check_", "");

    $('[data-action="' + message_id + '"]').addClass(
      "wpr-collections-installer-loading"
    );

    var query_data_type = "text";
    if (
      collection_item.action == "check_deactivate_plugin" ||
      collection_item.action == "check_delete_plugin" ||
      collection_item.action == "check_install_plugin" ||
      collection_item.action == "check_activate_plugin" ||
      collection_item.action == "check_install_theme" ||
      collection_item.action == "check_activate_theme" ||
      collection_item.action == "check_delete_theme" ||
      collection_item.action == "check_activate_license_plugin" ||
      collection_item.action == "check_activate_license_theme"
    ) {
      query_data_type = "json";
    }

    if (!("extra_data" in collection_item)) {
      collection_item.extra_data = {};
    }

    $.ajax({
      url: ajaxurl,
      type: "get",
      dataType: query_data_type,
      data: {
        action: "wp_reset_run_tool",
        _ajax_nonce: wp_reset.nonce_run_tool,
        tool: collection_item.action,
        slug: collection_item.slug,
        extra_data: collection_item.extra_data,
      },
      complete: function (data) {
        if (typeof data.responseJSON == "undefined") {
          data = false;
        } else {
          data = data.responseJSON;
        }
        var do_next_called = false;
        if (
          collection_item.action == "check_deactivate_plugin" ||
          collection_item.action == "check_delete_plugin" ||
          collection_item.action == "check_install_plugin" ||
          collection_item.action == "check_activate_plugin" ||
          collection_item.action == "check_install_theme" ||
          collection_item.action == "check_activate_theme" ||
          collection_item.action == "check_delete_theme" ||
          collection_item.action == "check_activate_license_plugin" ||
          collection_item.action == "check_activate_license_theme"
        ) {
          if (data == false && collections_retried == false) {
            collections_retried = true;
            collections_do_ajax();
            return false;
          }
          if (
            (collection_item.action == "check_deactivate_plugin" &&
              data.data == "inactive") ||
            (collection_item.action == "check_delete_plugin" &&
              data.data == "deleted") ||
            (collection_item.action == "check_install_plugin" &&
              (data.data == "inactive" || data.data == "active")) ||
            (collection_item.action == "check_activate_plugin" &&
              data.data == "active") ||
            (collection_item.action == "check_install_theme" &&
              (data.data == "inactive" || data.data == "active")) ||
            (collection_item.action == "check_activate_theme" &&
              data.data == "active") ||
            (collection_item.action == "check_delete_theme" &&
              data.data == "deleted") ||
            (collection_item.action == "check_activate_license_plugin" &&
              data.data == "license_active") ||
            (collection_item.action == "check_activate_license_theme" &&
              data.data == "license_active")
          ) {
            $('[data-action="' + message_id + '"]').addClass(
              "wpr-collections-installer-success"
            );
          } else {
            var error = false;
            switch (collection_item.action) {
              case "check_deactivate_plugin":
                error = wp_reset.deactivate_failed + " " + collection_item.name;
                break;
              case "check_delete_plugin":
              case "check_delete_theme":
                error = wp_reset.delete_failed + " " + collection_item.name;
                break;
              case "check_install_plugin":
              case "check_install_theme":
                if (data.data == "active") {
                  error =
                    collection_item.name +
                    " " +
                    wp_reset.install_failed_existing;
                } else if (data.data == "deleted") {
                  error = wp_reset.install_failed + " " + collection_item.name;
                } else {
                  error = data.data;
                }
                break;
              case "check_activate_plugin":
              case "check_activate_theme":
                error = wp_reset.activate_failed + " " + collection_item.name;
                break;
              case "check_activate_license_plugin":
              case "check_activate_license_theme":
                if (data.data == "unknown") {
                  error =
                    wp_reset.activating_license_unknown +
                    " " +
                    collection_item.name;
                } else {
                  error =
                    wp_reset.activating_license_failed +
                    " " +
                    collection_item.name;
                }
                break;
              default:
                error = false;
            }

            if (error != false) {
              $('[data-action="' + message_id + '"]').append(
                '<div class="wpr-collections-error">' + error + "</div>"
              );
              collections_errors.push(error);
            }

            $('[data-action="' + message_id + '"]').addClass(
              "wpr-collections-installer-error"
            );
          }

          $('[data-action="' + message_id + '"]').removeClass(
            "wpr-collections-installer-loading"
          );
          $('[data-action="' + message_id + '"]').addClass(
            "wpr-collections-installer-done"
          );
        }

        collections_retried = false;
        collections_ajax_queue_index++;

        if (collections_ajax_queue.length == 0) {
          wpr_swal.close();
          return false;
        }

        if (
          do_next_called == false &&
          typeof collections_ajax_queue[collections_ajax_queue_index] !==
            "undefined"
        ) {
          if (
            typeof $(
              ".wpr-collections-installer div.wpr-collections-installer-done:last"
            ).offset() !== "undefined"
          ) {
            var scroll_top =
              $(
                ".wpr-collections-installer div.wpr-collections-installer-done:last"
              ).offset().top -
              $(".wpr-collections-installer").offset().top +
              $(".wpr-collections-installer").scrollTop() -
              60;
            $(".wpr-collections-installer").animate(
              { scrollTop: scroll_top },
              600,
              function () {
                collections_do_ajax();
              }
            );
          } else {
            collections_do_ajax();
          }
        } else {
          wpr_swal.close();

          $(".wpr-collections-installer").css("padding", "0px 40px");
          $(".wpr-collections-installer").css("height", "236px");

          if (collections_errors.length > 0) {
            var errors_html = "";
            for (e in collections_errors) {
              errors_html +=
                '<div class="wpr-collections-installer-message wpr-collections-installer-error"><span class="dashicons"></span> ' +
                collections_errors[e] +
                "</div>";
            }
            wpr_swal
              .fire({
                icon: "error",
                title:
                  "Collection was installed, but the following errors occured!",
                html:
                  '<div class="wpr-collections-installer-errors">' +
                  errors_html +
                  "</div>",
                showConfirmButton: true,
              })
              .then((result) => {
                location.reload();
              });
          } else {
            wpr_swal
              .fire({
                icon: "success",
                title: "Collection was installed successfully!",
                showConfirmButton: true,
                timer: 2500,
                timerProgressBar: true,
              })
              .then((result) => {
                location.reload();
              });
          }
        }
      },
    });
  } //run collection ajax
}); // onload

function wpr_fix_dialog_close(event, ui) {
  jQuery(".ui-widget-overlay").bind("click", function () {
    jQuery("#" + event.target.id).dialog("close");
  });
} // wpr_fix_dialog_close

function wpr_clear_local(clear_cookies, clear_storage) {
  var cnt = 0;

  if (clear_cookies) {
    var cookies = Cookies.get();
    cnt += Object.keys(cookies).length;
    for (cookie in cookies) {
      Cookies.remove(cookie);
    }
  }

  if (clear_storage) {
    cnt += localStorage.length + sessionStorage.length;
    localStorage.clear();
    sessionStorage.clear();
  }

  return cnt;
} // wpr_clear_local

function wpr_close_dropdowns() {
  jQuery(".dropdown").removeClass("show");
  jQuery(".dropdown-menu").removeClass("show");
} // wpr_close_dropdowns

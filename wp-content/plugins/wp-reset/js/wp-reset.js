/**
 * WP Reset PRO
 * https://wpreset.com/
 * (c) WebFactory Ltd, 2017-2021
 */

jQuery(document).ready(function ($) {
  wp_reset.is_plugin_page = parseInt(wp_reset.is_plugin_page, 10);
  wp_reset.add_collection_item_dialog_markup = false;

  // init tabs
  if (wp_reset.is_plugin_page) {
    $("#wp-reset-tabs")
      .tabs({
        create: function () {
          $("#loading-tabs").remove();
          if (window.location.hash) {
            $("#wp-reset-tabs").tabs(
              "option",
              "active",
              $('a[href="' + location.hash + '"]')
                .parent()
                .index()
            );
          }
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
  }

  $(window).on("hashchange", function () {
    $("#wp-reset-tabs").tabs(
      "option",
      "active",
      $('a[href="' + location.hash + '"]')
        .parent()
        .index()
    );
  });

  // helper for switching tabs & linking anchors in different tabs
  $(".tools_page_wp-reset").on("click", ".change-tab", function (e) {
    e.preventDefault();
    wpr_swal.close();
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

    $(sibling_menu).position({
      of: parent_dropdown,
      my: "left top",
      at: "left bottom",
      collision: "flip flip",
    });

    return false;
  }); // toggle button dropdown menu

  $(document).on(
    "click",
    ":not(.dropdown-toggle), .dropdown-item",
    function () {
      wpr_close_dropdowns();
    }
  );

  // purge cache
  $(".tools_page_wp-reset").on("click", "#purge-cache", function (e) {
    e.preventDefault();

    run_tool_confirm(this, "purge_cache");

    return false;
  }); // purge cache

  // reset options table
  $(".tools_page_wp-reset").on("click", "#reset-options", function (e) {
    e.preventDefault();

    var params = new Object();
    if ($("#reset-options-reactivate-theme").is(":checked")) {
      params.reactivate_theme = 1;
    }
    if ($("#reset-options-reactivate-plugins").is(":checked")) {
      params.reactivate_plugins = 1;
    }

    run_tool_confirm(this, "reset_options", params);

    return false;
  }); // reset options

  $(".tools_page_wp-reset").on("click", "#deactivate-license", function (e) {
    e.preventDefault();
    button = this;

    wf_licensing_deactivate_licence_ajax(
      "wpr",
      $("#license-key").val(),
      button
    );
    return;
  });

  // validate license
  $(".tools_page_wp-reset").on("click", "#save-license", function (
    e,
    deactivate
  ) {
    e.preventDefault();
    button = this;
    safe_refresh = true;
    block = block_ui($(button).data("text-wait"));

    wf_licensing_verify_licence_ajax("wpr", $("#license-key").val(), button);

    return false;
  }); // validate license

  $("#wpr_keyless_activation").on("click", function (e) {
    e.preventDefault();

    button = this;
    safe_refresh = true;
    block = block_ui($(button).data("text-wait"));

    wf_licensing_verify_licence_ajax("wpr", "keyless", button);
    return;
  });

  $("#wpr_deactivate_license").on("click", function (e) {
    e.preventDefault();

    button = this;
    safe_refresh = true;

    wf_licensing_deactivate_licence_ajax(
      "wpr",
      $("#license-key").val(),
      button
    );
    return;
  });

  // fix for enter press in license field
  $("#license-key").on("keypress", function (e) {
    if (e.which == 13) {
      e.preventDefault();
      $("#save-license").trigger("click");
      return false;
    }
  }); // if enter on license key field

  // show/hide suboptions
  $(".tools_page_wp-reset").on(
    "change",
    ".toggle-wrapper.has-suboption input",
    function (e, onload) {
      sub_option = $(this).parents(".option-group").find(".sub-option-group");

      if (onload) {
        duration = 0;
      } else {
        duration = 400;
      }

      if ($(this).is(":checked")) {
        $(sub_option).slideDown(duration);
      } else {
        $(sub_option).slideUp(duration);
      }
    }
  );
  $(".toggle-wrapper.has-suboption input").trigger("change", true);

  $(".tools_page_wp-reset").on("change", "#option_cloud_service", function (e) {
    switch ($(this).val()) {
      case "dropbox":
        cloud_name = "Dropbox";
        cloud_service = "dropbox";
        break;
      case "gdrive":
        cloud_name = "Google Drive";
        cloud_service = "gdrive";
        break;
      case "pcloud":
        cloud_name = "pCloud";
        cloud_service = "pcloud";
        break;
      case "pcloudeu":
        cloud_name = "pCloud EU";
        cloud_service = "pcloudeu";
        break;
      case "icedrive":
        cloud_name = "Icedrive";
        cloud_service = "icedrive";
        break;
      case "wpreset":
        if(wp_reset.rebranding !== "0"){
            cloud_name = wp_reset.rebranding.name + ' Cloud';
        } else {
            cloud_name = "WP Reset";
        }
        cloud_service = "wpreset";
        break;
      default:
        cloud_name = "";
        cloud_service = "disabled";
        break;
    }

    if (
      cloud_service == "dropbox" ||
      cloud_service == "pcloud" ||
      cloud_service == "pcloudeu" ||
      cloud_service == "gdrive"
    ) {
      swal_text = "";
      if (cloud_service == "dropbox") {
        swal_text = "";
      }
      if (cloud_service == "gdrive") {
        swal_text =
          "Our Google Drive integration (app) still has a small number of users so you'll be prompted with an extra warning. Please do not be alarmed. This is normal and will go away as soon as more people start using the integration.";
      }
      if (cloud_service == "pcloud" || cloud_service == "pcloudeu") {
        swal_text =
          "To connect to the pCloud API you will need to authorize the WP Reset App and then you will see a box with the access code that you will have to Copy into the box in the next popup.";
      }
      
      wpr_swal
        .fire({
          type: "",
          title:
            "Click the button below to connect to your " +
            cloud_name +
            " account.",
          html: swal_text,
          confirmButtonText: "Connect " + cloud_name,
          cancelButtonText: "Cancel",
          showConfirmButton: true,
          showCancelButton: true,
        })
        .then((result) => {
          block_ui();
          if (result.value) {
            $.get({
              url: ajaxurl,
              data: {
                action: "wp_reset_run_tool",
                _ajax_nonce: wp_reset.nonce_run_tool,
                tool: "cloud_action",
                extra_data: {
                  action: "cloud_authorize_get_url",
                  service: cloud_service,
                },
              },
            })
              .always(function (data) {
                wpr_swal.close();
              })
              .done(function (data) {
                if (data.success) {
                  if (cloud_service == "pcloud" || cloud_service == "pcloudeu") {
                    wpr_swal
                      .fire({
                        title: "Connect to pCloud",
                        html:
                          '<a href="' +
                          data.data +
                          '" target="_blank" class="swal2-confirm swal2-styled" style="text-decoration:none;">Click here to get your pCloud access code</a><br /><br /> then enter your pCloud access code',
                        input: "text",
                        inputValue: "",
                        inputPlaceholder: "pCloud access code",
                        showCancelButton: true,
                        focusConfirm: false,
                        confirmButtonText: "Save",
                        cancelButtonText: wp_reset.cancel_button,
                        width: 600,
                      })
                      .then((result) => {
                        if (
                          result.dismiss ||
                          typeof result.value == "undefined"
                        ) {
                          return;
                        } else {
                          block_ui();
                          location.href =
                            wp_reset.settings_url +
                            "&authorize_cloud=" + cloud_service + "&code=" +
                            result.value;
                        }
                      });
                  } else {
                    location.href = data.data;
                  }
                } else {
                  wpr_swal.fire({
                    type: "error",
                    title: wp_reset.documented_error + " " + data.data,
                  });
                }
              })
              .fail(function (data) {
                wpr_swal.fire({
                  type: "error",
                  title: wp_reset.undocumented_error,
                });
              });
          } else {
            $("#option_cloud_service").val("disabled");
          }
        });
    } else if(cloud_service == "icedrive"){
        swal_text = 'Icedrive cloud is only available to Icedrive PRO users via WebDav. To connect to the Icedrive WebDav API you will need to enter your Icedrive email and WebDav Access key found on the Access page in your Icedrive account:';
        swal_text += '<input class="swal2-input" type="text" id="icedrive_user" name="icedrive_user" placeholder="Icedrive email" value="" />';
        swal_text += '<input class="swal2-input" type="text" id="icedrive_pass" name="icedrive_pass" placeholder="Access key" value="" />';

        wpr_swal
        .fire({
          type: "",
          title:
            "Connect to your " +
            cloud_name +
            " account.",
          html: swal_text,
          confirmButtonText: "Connect " + cloud_name,
          cancelButtonText: "Cancel",
          showConfirmButton: true,
          showCancelButton: true,
        })
        .then((result) => {
            tmp = wpr_swal.getContent();
            icedrive_user = $(tmp).find("#icedrive_user").val();
            icedrive_pass = $(tmp).find("#icedrive_pass").val();
            block_ui();
          if (result.value) {
            $.get({
              url: ajaxurl,
              data: {
                action: "wp_reset_run_tool",
                _ajax_nonce: wp_reset.nonce_run_tool,
                tool: "cloud_action",
                extra_data: {
                  action: "cloud_authorize_auth",
                  service: cloud_service,
                  user: icedrive_user,
                  pass: icedrive_pass
                },
              },
            })
              .always(function (data) {
                wpr_swal.close();
              })
              .done(function (data) {
                if (data.data) {
                    wpr_swal.fire({
                        title: data.data,
                        type: data.success?'success':'error',
                        width: 600,
                        height: 300,
                        allowEnterKey: true,
                        showCancelButton: true,
                        showConfirmButton: false,
                        showCloseButton: true,
                        allowEscapeKey: true,
                        allowOutsideClick: false,
                        cancelButtonText: "Close",
                    })
                    .then((result) => {
                        window.location.href = wp_reset.settings_url;
                    });
                } else {
                  wpr_swal.fire({
                    type: "error",
                    title: wp_reset.documented_error + " " + data.data,
                  });
                }
              })
              .fail(function (data) {
                wpr_swal.fire({
                  type: "error",
                  title: wp_reset.undocumented_error,
                });
              });
          } else {
            $("#option_cloud_service").val("disabled");
          }
        });
    } else {
      if (cloud_service == "disabled") {
        swal_title = "Cloud disabled";
        block_ui("Disabling cloud.");
      } else {
        swal_title = "Cloud service changed to " + cloud_name;
        block_ui("Switching cloud service to " + cloud_name);
      }
      $.get({
        url: ajaxurl,
        data: {
          action: "wp_reset_run_tool",
          _ajax_nonce: wp_reset.nonce_run_tool,
          tool: "cloud_action",
          extra_data: {
            action: "cloud_switch_service",
            service: cloud_service,
          },
        },
      })
        .always(function (data) {
          wpr_swal.close();
        })
        .done(function (data) {
          if (data.success) {
            wpr_swal
              .fire({
                type: "success",
                title: swal_title,
                timer: 1500,
                showConfirmButton: false,
              })
              .then((result) => {
                window.location.href = wp_reset.settings_url;
              });
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
    }
  });

  $(".tools_page_wp-reset").on("click", "#save-snapshot-options", function (e) {
    e.preventDefault();

    block_ui();
    $.get({
      url: ajaxurl,
      data: {
        action: "wp_reset_run_tool",
        _ajax_nonce: wp_reset.nonce_run_tool,
        tool: "save_snapshot_options",
        extra_data: {
          tools_snapshots: Number($("#option_tools_snapshots").is(":checked")),
          snapshots_autoupload: Number(
            $("#option_snapshots_autoupload").is(":checked")
          ),
          autosnapshots_autoupload: Number(
            $("#option_autosnapshots_autoupload").is(":checked")
          ),
          snapshots_upload_delete: Number(
            $("#option_snapshots_upload_delete").is(":checked")
          ),
          events_snapshots: Number(
            $("#option_events_snapshots").is(":checked")
          ),
          snapshots_size_alert: $("#option_snapshots_size_alert").val(),
          prune_snapshots: Number($("#option_prune_snapshots").is(":checked")),
          prune_snapshots_details: $("#option_prune_snapshots_details").val(),
          prune_cloud_snapshots: Number($("#option_prune_cloud_snapshots").is(":checked")),
          prune_cloud_snapshots_details: $("#option_prune_cloud_snapshots_details").val(),
          adminbar_snapshots: Number(
            $("#option_adminbar_snapshots").is(":checked")
          ),
          optimize_tables: Number($("#option_optimize_tables").is(":checked")),
          throttle_ajax: Number($("#option_throttle_ajax").is(":checked")),
          fix_datetime: Number($("#option_fix_datetime").is(":checked")),
          alternate_db_connection: Number($("#option_alternate_db_connection").is(":checked")),
          ajax_snapshots_export: Number(
            $("#option_ajax_snapshots_export").is(":checked")
          ),
        },
      },
    })
      .always(function (data) {
        wpr_swal.close();
      })
      .done(function (data) {
        if (data.success) {
          wpr_swal
            .fire({
              type: "success",
              title: "Options saved", //todo: localize
              timer: 1500,
              showConfirmButton: false,
            })
            .then((result) => {
              window.location.href = wp_reset.settings_url;
            });
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
  });

  // delete content
  $(".tools_page_wp-reset").on("click", "#delete-content", function (e) {
    e.preventDefault();

    var params = new Object();
    params.types = $("#delete-content-types").val();

    if (!params.types) {
      wpr_swal.fire({
        type: "error",
        title: "Pick at least one post type to delete.",
        text: "Use Ctrl + click to select multiple post types.",
      });
      return false;
    }

    run_tool_confirm(this, "delete_content", params);

    return false;
  }); // delete content

  // site reset
  $(".tools_page_wp-reset").on("click", "#site-reset", function (e) {
    e.preventDefault();
    var button = this;
    var confirm_title =
      $(button).data("confirm-title") || wp_reset.confirm_title;

    if ($("#site_reset_confirm").val() !== "reset") {
      wpr_swal.fire({
        title: wp_reset.invalid_confirmation_title,
        text: wp_reset.invalid_confirmation,
        type: "error",
        confirmButtonText: wp_reset.ok_button,
      });
      return false;
    } // wrong confirmation code

    confirm_action(
      confirm_title,
      $(button).data("text-confirm"),
      $(button).data("btn-confirm") || $(button).text(),
      wp_reset.cancel_button
    ).then((result) => {
      if (!result.value) {
        return false;
      }

      //moved code to do_reset_site so we can go though create_snapshot if needed
      if (
        typeof wp_reset.tools_autosnapshot === "object" &&
        wp_reset.tools_autosnapshot !== null
      ) {
        create_snapshot(
          wp_reset.tools_autosnapshot["reset_site"],
          1,
          do_reset_site,
          [$(button).data("text-wait")]
        );
      } else {
        do_reset_site($(button).data("text-wait"));
      }
    }); // confirm ok

    return false;
  }); // site reset

  function do_reset_site(wait_message) {
    block_ui(wait_message);

    $.get({
        url: ajaxurl,
        data: {
            action: 'wp_reset_run_tool',
            _ajax_nonce: wp_reset.nonce_run_tool,
            tool: 'before_reset'
        },
    }).done(function (data) {
        if (data.success) {
            $.get({
                url: ajaxurl,
                data: {
                  action: "wp_reset_run_tool",
                  _ajax_nonce: wp_reset.nonce_run_tool,
                  tool: "site_reset",
                  extra_data: {
                    reactivate_theme: $("#site-reset-reactivate-theme").is(":checked")
                      ? 1
                      : 0,
                    reactivate_plugins: $("#site-reset-reactivate-plugins").is(":checked")
                      ? 1
                      : 0,
                    reactivate_wpreset: $("#site-reset-reactivate-wpreset").is(":checked")
                      ? 1
                      : 0,
                  },
                },
              })
                .done(function (data) {
                  if (data.success && data.data) {
                    if ($("#site-reset-reactivate-wpreset").is(":checked")) {
                      wpr_swal
                        .fire({
                          type: "success",
                          title:
                            "Site successfully reset! The page will reload in a moment.",
                          timer: 1500,
                          showConfirmButton: false,
                        })
                        .then(() => {
                          window.location.href = wp_reset.settings_url;
                        });
                    } else {
                      window.location.href = data.data;
                    }
                  } else {
                    wpr_swal.close();
                    wpr_swal.fire({
                      type: "error",
                      title: wp_reset.documented_error + " " + data.data,
                    });
                  }
                })
                .fail(function (data) {
                  wpr_swal.close();
                  wpr_swal.fire({ type: "error", title: wp_reset.undocumented_error });
                });
        } else {
            wpr_swal.fire({
                icon: 'error',
                title: wp_reset.undocumented_error,
            });
        }
    }).fail(function (data) {
        wpr_swal.fire({
            icon: 'error',
            title: wp_reset.undocumented_error,
        });
    });

    
  }

  // nuclear reset
  $(".tools_page_wp-reset").on("click", "#nuclear-reset", function (e) {
    e.preventDefault();
    var button = this;
    var confirm_title =
      $(button).data("confirm-title") || wp_reset.confirm_title;

    if ($("#nuclear_reset_confirm").val() !== "reset") {
      wpr_swal.fire({
        title: wp_reset.invalid_confirmation_title,
        text: wp_reset.invalid_confirmation,
        type: "error",
        confirmButtonText: wp_reset.ok_button,
      });
      return false;
    } // wrong confirmation code

    confirm_action(
      confirm_title,
      $(button).data("text-confirm"),
      $(button).data("btn-confirm") || $(button).text(),
      wp_reset.cancel_button
    ).then((result) => {
      if (!result.value) {
        return false;
      }

      //moved code to do_nuclear_reset so we can go though create_snapshot if needed
      if (
        typeof wp_reset.tools_autosnapshot === "object" &&
        wp_reset.tools_autosnapshot !== null
      ) {
        create_snapshot(
          wp_reset.tools_autosnapshot["reset_site"],
          1,
          do_nuclear_reset,
          [$(button).data("text-wait")]
        );
      } else {
        do_nuclear_reset($(button).data("text-wait"));
      }
    }); // confirm ok

    return false;
  }); // nuclear reset

  function do_nuclear_reset(wait_message) {
    var tools = [
      {
        tool: "delete_themes",
        description: "Deleting themes.",
        extra_data: { keep_default_theme: 0, keep_current_theme: 0 },
      },
      {
        tool: "deactivate_plugins",
        description: "Deactivating plugins.",
        extra_data: {
          keep_wp_reset: 1,
          silent_deactivate: 0,
        },
      },
      {
        tool: "delete_plugins",
        description: "Deleting plugins.",
        extra_data: {
          keep_wp_reset: 1,
        },
      },
      {
        tool: "delete_uploads",
        description: "Cleaning uploads folder.",
        extra_data: "",
      },
      {
        tool: "delete_wp_content",
        description: "Cleaning wp-content folder.",
        extra_data: "",
      },
      {
        tool: "delete_mu_plugins",
        description: "Deleting MU plugins.",
        extra_data: "",
      },
      {
        tool: "delete_dropins",
        description: "Deleting drop-ins.",
        extra_data: "",
      },
      {
        tool: "site_reset",
        description: "Resetting site.",
        extra_data: {
          reactivate_theme: 0,
          reactivate_plugins: 0,
          reactivate_wpreset: $("#nuclear-reset-reactivate-wpreset").is(
            ":checked"
          )
            ? 1
            : 0,
          reactivate_webhooks: 0,
        },
      },
    ];
    var looper = $.Deferred().resolve();

    wpr_swal.fire({
      title: wait_message,
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
      imageAlt: wait_message,
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
                if ($("#nuclear-reset-reactivate-wpreset").is(":checked")) {
                  wpr_swal
                    .fire({
                      type: "success",
                      title:
                        "Site successfully reset! The page will reload in a moment.",
                      timer: 1500,
                      showConfirmButton: false,
                    })
                    .then(() => {
                      window.location.href = wp_reset.settings_url;
                    });
                } else {
                  window.location.href = response.data;
                }
              }
            } else {
              wpr_swal.close();
              wpr_swal.fire({
                type: "error",
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
              type: "error",
              title: wp_reset.undocumented_error,
            });
            failed = true;
            return false;
          });
      });
    });
  }

  // delete auth cookies
  $(".tools_page_wp-reset").on("click", "#delete-wp-cookies", function (e) {
    e.preventDefault();

    run_tool_confirm(this, "delete_wp_cookies");

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
      wpr_swal.fire({ type: "success", title: msg });
    });

    return false;
  }); // delete local data

  // delete widgets
  $(".tools_page_wp-reset").on("click", "#delete-widgets", function (e) {
    e.preventDefault();

    run_tool_confirm(this, "delete_widgets");

    return false;
  }); // delete widgets

  // delete transients
  $(".tools_page_wp-reset").on("click", "#delete-transients", function (e) {
    e.preventDefault();

    run_tool_confirm(this, "delete_transients");

    return false;
  }); // delete transients

  // delete uploads
  $(".tools_page_wp-reset").on("click", "#delete-uploads", function (e) {
    e.preventDefault();

    run_tool_confirm(this, "delete_uploads");

    return false;
  }); // delete uploads

  // delete uploads
  $(".tools_page_wp-reset").on("click", "#delete-wp-content", function (e) {
    e.preventDefault();

    run_tool_confirm(this, "delete_wp_content");

    return false;
  }); // delete wp-content

  // reset theme options (mods)
  $(".tools_page_wp-reset").on("click", "#reset-theme-options", function (e) {
    e.preventDefault();

    run_tool_confirm(this, "reset_theme_options");

    return false;
  }); // reset theme options

  // delete themes
  $(".tools_page_wp-reset").on("click", "#delete-themes", function (e) {
    e.preventDefault();

    run_tool_confirm(this, "delete_themes", {
      keep_default_theme: 0,
      keep_current_theme: 0,
    });

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
                  wpr_swal_params = { type: "success", title: msg };

                  wpr_swal.fire(wpr_swal_params);
                }
              } else {
                wpr_swal.close();
                wpr_swal.fire({
                  type: "error",
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
                type: "error",
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

  // switch wp version
  $(".tools_page_wp-reset").on("click", "#switch-wp-version", function (e) {
    e.preventDefault();

    var params = new Object();
    params.version = $("#select-wp-version").val();

    if (!params.version) {
      wpr_swal.fire({
        type: "error",
        title: "Pick the WordPress version you want to switch to.",
      });
      return false;
    }

    run_tool_confirm(this, "switch_wp_version", params);

    return false;
  }); // switch wp version

  // refresh list of wp versions
  $(".tools_page_wp-reset").on("click", "#refresh-wp-versions", function (e) {
    e.preventDefault();
    button = $(this);
    block_ui($(button).data("text-wait"));
    $.get({
      url: ajaxurl,
      data: {
        action: "wp_reset_run_tool",
        _ajax_nonce: wp_reset.nonce_run_tool,
        tool: "refresh_wp_versions",
      },
    })
      .always(function (data) {
        wpr_swal.close();
      })
      .done(function (data) {
        if (data.success) {
          wpr_swal
            .fire({
              type: "success",
              title: "WordPress versions list refreshed",
              timer: 1500,
              showConfirmButton: false,
            })
            .then(() => {
              location.reload();
            });
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
  }); // switch wp version

  // drop custom tables
  $(".tools_page_wp-reset").on("click", "#drop-custom-tables", function (e) {
    e.preventDefault();

    var params = new Object();
    params.tables = $("#empty-delete-tables-tables").val();

    if (!params.tables) {
      wpr_swal.fire({
        type: "error",
        title: "Pick at least one custom table to empty or delete.",
        text: "Use Ctrl + click to select multiple tables.",
      });
      return false;
    }

    run_tool_confirm(this, "drop_custom_tables", params);

    return false;
  }); // drop custom tables

  // truncate custom tables
  $(".tools_page_wp-reset").on("click", "#truncate-custom-tables", function (
    e
  ) {
    e.preventDefault();

    var params = new Object();
    params.tables = $("#empty-delete-tables-tables").val();

    if (!params.tables) {
      wpr_swal.fire({
        type: "error",
        title: "Pick at least one custom table to empty or delete.",
        text: "Use Ctrl + click to select multiple tables.",
      });
      return false;
    }

    run_tool_confirm(this, "truncate_custom_tables", params);

    return false;
  }); // truncate custom tables

  // delete htaccess file
  $(".tools_page_wp-reset").on("click", "#delete-htaccess", function (e) {
    e.preventDefault();

    run_tool_confirm(this, "delete_htaccess");

    return false;
  }); // delete htaccess file

  // reset user roles
  $(".tools_page_wp-reset").on("click", "#reset-user-roles", function (e) {
    e.preventDefault();

    run_tool_confirm(this, "reset_user_roles");

    return false;
  }); // reset user roles

  // restore htaccess file
  $(".tools_page_wp-reset").on("click", "#restore-htaccess", function (e) {
    e.preventDefault();

    run_tool_confirm(this, "restore_htaccess");

    return false;
  }); // restore htaccess file

  // trigger name edit from menu
  $(".tools_page_wp-reset").on(
    "click",
    ".ss-action.edit-snapshot-description",
    function (e) {
      e.preventDefault();

      $(this).parents("tr").find(".snapshot-name").trigger("click");

      return false;
    }
  ); // trigger name edit from menu

  // edit snapshot name
  $(".tools_page_wp-reset").on("click", ".snapshot-name", function (e) {
    e.preventDefault();

    wpr_close_dropdowns();

    snapshot_uid = $(this).parents("tr").data("ss-uid");
    if ($(this).find("i").length == 1) {
      snapshot_name = "";
    } else {
      snapshot_name = $(this).text();
    }
    snapshot_name_holder = this;

    wpr_swal
      .fire({
        title: "Edit snapshot description", //todo: localize
        type: "question",
        text: wp_reset.snapshot_text,
        input: "text",
        inputValue: snapshot_name,
        inputPlaceholder: wp_reset.snapshot_placeholder,
        showCancelButton: true,
        focusConfirm: false,
        confirmButtonText: "Save", //todo: localize
        cancelButtonText: wp_reset.cancel_button,
        width: 600,
      })
      .then((result) => {
        if (result.dismiss || typeof result.value == "undefined") {
          return;
        } else {
          block_ui();
          $.get({
            url: ajaxurl,
            data: {
              action: "wp_reset_run_tool",
              _ajax_nonce: wp_reset.nonce_run_tool,
              tool: "edit_snapshot_name",
              extra_data: { uid: snapshot_uid, new_name: result.value },
            },
          })
            .always(function (data) {
              wpr_swal.close();
            })
            .done(function (data) {
              if (data.success) {
                wpr_swal
                  .fire({
                    type: "success",
                    title: "Snapshot description saved.", //todo: localize
                    timer: 1500,
                    showConfirmButton: false,
                  })
                  .then((result) => {
                    if (data.data) {
                      $(snapshot_name_holder).text(data.data);
                    } else {
                      $(snapshot_name_holder).html(
                        "<i>Click to add description.</i>"
                      );
                    }
                  });
              } else {
                wpr_swal.fire({
                  type: "error",
                  title: wp_reset.documented_error + " " + data.data,
                });
              }
            })
            .fail(function (data) {
              wpr_swal.fire({
                type: "error",
                title: wp_reset.undocumented_error,
              });
            });
        }
      });

    return false;
  }); // edit snapshot name

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
  });

  $("#snapshots-table-user, #snapshots-table-auto")
    .on("DOMSubtreeModified", function (e) {
      table = this;

      if ($("tr", table).length <= 2) {
        $("tr.table-empty", table).show();
      } else {
        $("tr.table-empty", table).hide();
      }
    })
    .trigger("DOMSubtreeModified");

  $(".tools_page_wp-reset").on(
    "wpr-collection-table-changed",
    ".collection-table",
    function (e) {
      table = this;

      if ($("tr", table).length <= 2) {
        $("tr.table-empty", table).show();
      } else {
        $("tr.table-empty", table).hide();
      }
    }
  );
  $(".collection-table").trigger("wpr-collection-table-changed");

  // compare snapshot
  $("#snapshots-table-auto, #snapshots-table-user").on(
    "click",
    ".compare-snapshot",
    function (e) {
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
              type: "error",
              title: wp_reset.documented_error + " " + data.data,
            });
          }
        })
        .fail(function (data) {
          wpr_swal.fire({ type: "error", title: wp_reset.undocumented_error });
        });

      return false;
    }
  ); // compare snapshot

  // restore snapshot
  $("#snapshots-table-auto, #snapshots-table-user").on(
    "click",
    ".restore-snapshot",
    function (e) {
      e.preventDefault();
      uid = $(this).data("ss-uid");

      run_tool_confirm(this, "restore_snapshot", uid);

      return false;
    }
  ); // restore snapshot

  // download snapshot
  $("#snapshots-table-auto, #snapshots-table-user").on(
    "click",
    ".download-snapshot",
    function (e) {
      e.preventDefault();
      uid = $(this).data("ss-uid");
      button = this;

      export_snapshot(uid);

      return false;
    }
  ); // download snapshot

  // delete snapshot
  $("#snapshots-table-auto, #snapshots-table-user").on(
    "click",
    ".delete-snapshot",
    function (e) {
      e.preventDefault();
      uid = $(this).data("ss-uid");

      run_tool_confirm(this, "delete_snapshot", uid, true);

      return false;
    }
  ); // delete snapshot

  $(".tools_page_wp-reset").on("click", ".cloud-upload-snapshot", function (e) {
    e.preventDefault();
    uid = $(this).data("ss-uid");
    button = this;

    export_snapshot(uid, wpr_cloud, [
      "snapshot_upload",
      uid,
      wp_reset.cloud_snapshot_uploading,
      wp_reset.cloud_snapshot_uploaded,
      "reload",
    ]);
  });

  $(".tools_page_wp-reset").on("click", ".cloud-delete-snapshot", function (e) {
    e.preventDefault();
    uid = $(this).data("ss-uid");

    wpr_cloud_confirm(
      this,
      "snapshot_delete",
      uid,
      wp_reset.cloud_snapshot_deleting,
      wp_reset.cloud_snapshot_deleted,
      "reload"
    );
  });

  $(".tools_page_wp-reset").on("click", ".cloud-download-snapshot", function (
    e
  ) {
    e.preventDefault();
    uid = $(this).data("ss-uid");

    wpr_cloud(
      "snapshot_download",
      uid,
      wp_reset.cloud_snapshot_downloading,
      wp_reset.cloud_snapshot_downloaded,
      "reload"
    );
  });

  $(".tools_page_wp-reset").on("click", ".refresh-cloud-snapshots", function (
    e
  ) {
    e.preventDefault();
    wpr_cloud(
      "snapshots_refresh",
      false,
      wp_reset.cloud_snapshots_refresh,
      wp_reset.cloud_snapshots_refreshed,
      "reload"
    );
  });

  // delete collection
  $(".tools_page_wp-reset").on("click", ".delete-collection", function (e) {
    e.preventDefault();

    collection_id = $(this).parents(".card").data("collection-id");

    run_tool_confirm(
      this,
      "delete_collection",
      { collection_id },
      true,
      function (return_data, call_params) {
        $('.card[data-collection-id="' + collection_id + '"]').slideUp(
          1000,
          function () {
            $(this).remove();
            if ($(".card[data-collection-id]").length == 0) {
              $("#no-collections").removeClass("hidden");
            }
          }
        );
      }
    );

    return false;
  }); // delete collection

  function edit_collection_item(edit, params) {
    if (!wp_reset.add_collection_item_dialog_markup) {
      markup = $(".edit-collection-item-dialog");
      markup = $(markup).removeClass("hidden");
      $(".edit-collection-item-dialog").remove();
      wp_reset.add_collection_item_dialog_markup = markup;
    } else {
      markup = wp_reset.add_collection_item_dialog_markup;
    }

    if (edit) {
      title = "Edit collection item";
      confirm_btn = "Save changes";
      tool_endpoint = "edit_collection_item";
      wait_msg = "Saving collection item. Please wait.";
      success_msg = "Collection item saved";
      $(markup).find(".dialog-collection-item-id").val(params.item_id);
      $(markup).find(".collection-item-type").addClass("disabled");
      $(markup).find(".collection-item-source").addClass("disabled");
      $(markup).find(".dialog-collection-item-slug").addClass("disabled");
      $(markup).find("#edit-collection-item-source-zip").addClass("disabled");
    } else {
      title = "Add new collection item";
      confirm_btn = "Add new item";
      tool_endpoint = "add_collection_item";
      wait_msg = "Adding new collection item. Please wait.";
      success_msg = "New collection item added";
      $(markup).find(".collection-item-type").removeClass("disabled");
      $(markup).find(".dialog-collection-item-slug").removeClass("disabled");
      $(markup).find(".collection-item-source").removeClass("disabled");
      $(markup).find("#dialog-collection-item-type").removeProp("checked");
      $(markup).find("#dialog-collection-item-source").removeProp("checked");
      $(markup).find("#edit-collection-item-source-wp").removeClass("disabled");
      $(markup)
        .find("#edit-collection-item-source-zip")
        .removeClass("disabled");
      $(markup).find("#edit-collection-item-source-wp").show();
    }

    if (params.type == "theme") {
      $(markup).find("#dialog-collection-item-type").prop("checked", "checked");
    }

    if (params.source == "zip") {
      $(markup)
        .find("#dialog-collection-item-source")
        .prop("checked", "checked");
      $(markup).find("#edit-collection-item-license-key").show();
      $(markup).find("#edit-collection-item-source-wp").hide();
      $(markup).find("#edit-collection-item-source-zip").show();
    } else {
      $(markup).find("#edit-collection-item-source-wp").show();
      $(markup).find("#edit-collection-item-source-zip").hide();
      $(markup).find("#edit-collection-item-license-key").hide();
    }

    if (wp_reset.cloud_service != 1 && $("#dialog-collection-item-type").length > 0) {
        $(markup).find(".collection-item-source > label").addClass("disabled");

        $(markup).find("#dialog-collection-item-type").removeProp("checked");

        if (
            $(markup)
            .find(".collection-item-source")
            .html()
            .indexOf("to upload ZIP") == -1
        ) {
            $(markup)
            .find(".collection-item-source")
            .append(
                ' Select a <a class="change-tab" data-tab="4" href="#">cloud service</a> to upload ZIP'
            );
        }
    }
    $(markup)
      .find(".dialog-collection-item-slug")
      .empty()
      .append(
        $("<option></option>").attr("value", params.slug).text(params.slug)
      );
    $(markup).find(".dialog-collection-item-note").val(params.note);

    $(markup)
      .find(".dialog-collection-item-license-key")
      .val(params.license_key);

    $(markup).find(".dialog-collection-id").val(params.collection_id);

    wpr_swal
      .fire({
        title: title,
        html: markup,
        confirmButtonText: confirm_btn,
        showCancelButton: true,
        onRender: function () {
          if ($(".dialog-collection-item-slug").hasClass("disabled")) {
          } else {
            $(".dialog-collection-item-slug").select2({
              ajax: {
                url: ajaxurl,
                dataType: "json",
                data: function (params) {
                  var query = {
                    action: "wp_reset_run_tool",
                    _ajax_nonce: wp_reset.nonce_run_tool,
                    tool: "wp_slug_search",
                    extra_data: {
                      search: params.term,
                      theme: $("#dialog-collection-item-type").is(":checked"),
                    },
                  };
                  return query;
                },
                processResults: function (data) {
                  return {
                    results: data.data,
                  };
                },
              },
            });
          }
        },
        preConfirm: () => {
          tmp = wpr_swal.getContent();
          type = $(tmp).find("#dialog-collection-item-type").is(":checked")
            ? "theme"
            : "plugin";
          source = $(tmp).find("#dialog-collection-item-source").is(":checked")
            ? "zip"
            : "repo";
          slug = $(tmp).find(".dialog-collection-item-slug").val();

          if (source == "slug") {
            if (!slug) {
              wpr_swal.showValidationMessage(
                "Please enter the " + type + " slug."
              );
            }
            if (type == "plugin" && source == "slug") {
              $.get(
                "https://api.wordpress.org/plugins/info/1.0/" + slug + ".json",
                function (data, status) {
                  if (status != "success") {
                    wpr_swal.showValidationMessage(
                      "Unable to check plugin slug in wp.org repository."
                    );
                  }
                  if (data.error) {
                    wpr_swal.showValidationMessage(
                      "Please double-check the plugin slug. " + data.error
                    );
                  }
                }
              );
            } else {
              $.get(
                "https://api.wordpress.org/themes/info/1.1/?action=theme_information&request[slug]=" +
                  slug +
                  "",
                function (data, status) {
                  if (status != "success") {
                    wpr_swal.showValidationMessage(
                      "Unable to check theme slug in wp.org repository."
                    );
                  }
                  if (!data) {
                    wpr_swal.showValidationMessage(
                      "Please double-check the theme slug."
                    );
                  }
                }
              );
            }
          }
          return true;
        },
      })
      .then(function (result) {
        if (result.dismiss || typeof result.value == "undefined") {
          return;
        }
        tmp = wpr_swal.getContent();
        item_id = $(tmp).find(".dialog-collection-item-id").val();
        collection_id = $(tmp).find(".dialog-collection-id").val();

        var formData = new FormData();
        var file = $(tmp).find(".dialog-collection-item-zip")[0].files[0];
        formData.append("zip", file);
        formData.append("collection_id", collection_id);
        formData.append("item_id", item_id);
        formData.append(
          "type",
          $(tmp).find("#dialog-collection-item-type").is(":checked")
            ? "theme"
            : "plugin"
        );
        formData.append(
          "slug",
          $(tmp).find(".dialog-collection-item-slug").val()
        );
        formData.append(
          "note",
          $(tmp).find(".dialog-collection-item-note").val()
        );
        formData.append(
          "license_key",
          $(tmp).find(".dialog-collection-item-license-key").val()
        );
        formData.append(
          "source",
          $(tmp).find("#dialog-collection-item-source").is(":checked")
            ? "zip"
            : "repo"
        );
        formData.append("action", "wp_reset_run_tool");
        formData.append("tool", tool_endpoint);
        formData.append("_ajax_nonce", wp_reset.nonce_run_tool);
        block = block_ui(wait_msg);

        $.ajax({
          method: "post",
          url: ajaxurl,
          data: formData,
          processData: false,
          contentType: false,
        })
          .always(function (data) {
            wpr_swal.close();
          })
          .done(function (data) {
            if (data.success) {
              wp_reset.collections = data.data.collections;
              wpr_swal
                .fire({
                  type: "success",
                  title: success_msg,
                  timer: 1500,
                  showConfirmButton: false,
                })
                .then(() => {
                  if (edit) {
                    $(
                      "tr[data-collection-item-id=" + item_id + "]"
                    ).replaceWith(data.data.item);
                  } else {
                    $("#card-collection-" + collection_id)
                      .parent()
                      .find(".collection-table tr:nth-child(2)")
                      .after(data.data.item);
                    $("#card-collection-" + collection_id)
                      .parent()
                      .find(".collection-table")
                      .trigger("wpr-collection-table-changed");
                  }
                });
            } else if (!data.success && !data.data) {
              wpr_swal.fire({
                type: "error",
                title: wp_reset.undocumented_error,
              });
            } else {
              wpr_swal.fire({
                type: "error",
                title: wp_reset.documented_error + " " + data.data,
              });
            }
          })
          .fail(function (data) {
            wpr_swal.fire({
              type: "error",
              title: wp_reset.undocumented_error,
            });
          });
      });
  } // edit_collection_item

  $(".tools_page_wp-reset").on("change", ".collection-item-source", function (
    e
  ) {
    is_zip = $("#dialog-collection-item-source").is(":checked");

    if ($("#dialog-collection-item-source").is(":checked")) {
      $("#edit-collection-item-source-wp").hide();
      $("#edit-collection-item-source-zip").show();
      $("#edit-collection-item-license-key").show();
    } else {
      $("#edit-collection-item-source-wp").show();
      $("#edit-collection-item-source-zip").hide();
      $("#edit-collection-item-license-key").hide();
    }
  });

  $(".tools_page_wp-reset").on("click", ".add-collection-item", function (e) {
    e.preventDefault();
    edit_collection_item(false, {
      collection_id: $(this).data("collection-id"),
      type: "plugin",
      source: "repo",
      slug: "",
      note: "",
      license_key: "",
    });

    return false;
  });

  $(".tools_page_wp-reset").on("click", ".edit-collection-item", function (e) {
    edit_collection_item(true, {
      collection_id: $(this).parents(".card").data("collection-id"),
      item_id: $(this).data("item-id"),
      type: $(this).data("item-type"),
      source: $(this).data("item-source"),
      slug: $(this).data("item-slug"),
      note: $(this).data("item-note"),
      license_key: $(this).data("item-license_key"),
    });

    return false;
  });

  // delete collection item
  $(".tools_page_wp-reset").on("click", ".delete-collection-item", function (
    e
  ) {
    e.preventDefault();

    collection_id = $(this).parents(".card").data("collection-id");
    collection_item_id = $(this).parents("tr").data("collection-item-id");

    run_tool_confirm(
      this,
      "delete_collection_item",
      { collection_id, collection_item_id },
      true,
      function (return_data, call_params) {
        wp_reset.collections = return_data.collections;
        $('tr[data-collection-item-id="' + collection_item_id + '"]')
          .css("background-color", "#ff0000")
          .fadeOut(500, function () {
            $(this).remove();
            $('.card[data-collection-id="' + collection_id + '"]')
              .find(".collection-table")
              .trigger("wpr-collection-table-changed");
          });
      }
    );

    return false;
  }); // delete collection item

  // edit collection name
  $(".tools_page_wp-reset").on("click", ".edit-collection-name", function (e) {
    e.preventDefault();
    wpr_close_dropdowns();

    button = this;
    collection_id = $(this).parents(".card").data("collection-id");

    collection_name = $(this).parents(".card").find(".card-name").text();
    collection_name_holder = $(this).parents(".card").find(".card-name");

    wpr_swal
      .fire({
        title: $(button).data("text-title"),
        type: "question",
        text: wp_reset.collection_add_text,
        input: "text",
        inputValue: collection_name,
        inputPlaceholder: wp_reset.collection_add_placeholder,
        showCancelButton: true,
        focusConfirm: false,
        confirmButtonText: $(button).data("text-confirm"),
        cancelButtonText: wp_reset.cancel_button,
        width: 600,
      })
      .then((result) => {
        if (result.dismiss || typeof result.value == "undefined") {
          return;
        } else {
          block_ui("Saving collection. Please wait.");
          $.get({
            url: ajaxurl,
            data: {
              action: "wp_reset_run_tool",
              _ajax_nonce: wp_reset.nonce_run_tool,
              tool: "edit_collection_name",
              extra_data: {
                collection_id: collection_id,
                collection_name: result.value,
              },
            },
          })
            .always(function (data) {
              wpr_swal.close();
            })
            .done(function (data) {
              if (data.success && data.data) {
                wpr_swal
                  .fire({
                    type: "success",
                    title: $(button).data("text-done"),
                    timer: 1500,
                    showConfirmButton: false,
                  })
                  .then((result) => {
                    $(collection_name_holder).text(data.data);
                  });
              } else {
                wpr_swal.fire({
                  type: "error",
                  title: wp_reset.documented_error + " " + data.data,
                });
              }
            })
            .fail(function (data) {
              wpr_swal.fire({
                type: "error",
                title: wp_reset.undocumented_error,
              });
            });
        }
      });

    return false;
  }); // edit collection name

  // add new collection
  $(".tools_page_wp-reset").on("click", ".add-new-collection", function (e) {
    e.preventDefault();

    wpr_swal
      .fire({
        title: wp_reset.collection_add_title,
        type: "question",
        text: wp_reset.collection_add_text,
        input: "text",
        inputPlaceholder: wp_reset.collection_add_placeholder,
        showCancelButton: true,
        focusConfirm: false,
        confirmButtonText: wp_reset.collection_add_confirm,
        cancelButtonText: wp_reset.cancel_button,
        width: 600,
      })
      .then((result) => {
        if (result.dismiss || typeof result.value == "undefined") {
          return;
        } else {
          block_ui(wp_reset.collection_add_wait);
          $.get({
            url: ajaxurl,
            data: {
              action: "wp_reset_run_tool",
              _ajax_nonce: wp_reset.nonce_run_tool,
              tool: "add_new_collection",
              extra_data: { name: result.value },
            },
          })
            .always(function (data) {
              wpr_swal.close();
            })
            .done(function (data) {
              if (data.success) {
                wpr_swal
                  .fire({
                    type: "success",
                    title: wp_reset.collection_add_success,
                    timer: 1500,
                    showConfirmButton: false,
                  })
                  .then((result) => {
                    $("#no-collections").addClass("hidden");
                    $("#new-collection-placeholder").after(data.data);
                    $("#new-collection-placeholder")
                      .next(".card")
                      .first()
                      .hide()
                      .find(".collection-table")
                      .trigger("wpr-collection-table-changed");
                    $("#new-collection-placeholder")
                      .next(".card")
                      .first()
                      .slideDown(1000);
                  });
              } else {
                wpr_swal.fire({
                  type: "error",
                  title: wp_reset.documented_error + " " + data.data,
                });
              }
            })
            .fail(function (data) {
              wpr_swal.fire({
                type: "error",
                title: wp_reset.undocumented_error,
              });
            });
        }
      });

    return false;
  }); // add new collection

  // reload collections
  $(".tools_page_wp-reset").on("click", ".reload-collections", function (e) {
    e.preventDefault();

    block_ui("Reloading collections. Please wait."); // todo: localize

    $.get({
      url: ajaxurl,
      data: {
        action: "wp_reset_run_tool",
        _ajax_nonce: wp_reset.nonce_run_tool,
        tool: "reload_collections",
      },
    })
      .always(function (data) {
        wpr_swal.close();
      })
      .done(function (data) {
        if (data.success) {
          wpr_swal
            .fire({
              type: "success",
              title: "Collections reloaded",
              timer: 1500,
              showConfirmButton: false,
            })
            .then(function () {
              location.reload();
            });
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
  }); // reload collections

  // preview snapshot
  $("#snapshots-table-auto, #snapshots-table-user").on(
    "click",
    ".preview-snapshot",
    function (e) {
      e.preventDefault();

      wpr_close_dropdowns();

      wpr_swal.fire({
        type: "info",
        title: $(this).data("title"),
        text: $(this).data("description"),
      });

      return false;
    }
  ); // delete snapshot

  // disabled option - delete MU plugins
  $(".tools_page_wp-reset").on("click change", "#delete-mu-plugins", function (
    e
  ) {
    e.preventDefault();

    run_tool_confirm(this, "delete_mu_plugins");

    return false;
  }); // disabled option - delete MU plugins

  // disabled option - delete dropins
  $(".tools_page_wp-reset").on("click change", "#delete-dropins", function (e) {
    e.preventDefault();

    run_tool_confirm(this, "delete_dropins");

    return false;
  }); // disabled option - delete dropins

  // open Help Scout Beacon
  $(".tools_page_wp-reset").on("click", ".open-beacon", function (e) {
    e.preventDefault();

    Beacon("open");

    return false;
  });

  // create snapshot shortcut in admin bar
  $(document).on("click", ".wpr-admin-bar-create-snapshot", function (e) {
    e.preventDefault();

    create_snapshot();

    return false;
  }); // create snapshot shortcut in admin bar

  // create snapshot shortcut in each tool
  $(document).on("click", ".tools-create-snapshot", function (e) {
    e.preventDefault();
    description = $(this).data("snapshot-description") || "";

    create_snapshot(description);

    return false;
  }); // create snapshot shortcut in each tool

  var wpr_autouploader_check_timer;
  var wpr_autouploader_thread;
  var wpr_autouploader = false;

  function wpr_autouploader_run() {
    wpr_autouploader_thread = setInterval(function () {
      if (wpr_autouploader !== true) {
        return;
      }

      localStorage[wp_reset.autouploader_key] = Date.now();
      $.get({
        url: ajaxurl,
        data: {
          action: "wp_reset_run_tool",
          _ajax_nonce: wp_reset.nonce_run_tool,
          tool: "autouploader_step",
          extra_data: {},
        },
      })
        .done(function (response) {
          //console.log(response);
        })
        .error(function (response) {
          //console.log("Error autouploader");
        });
    }, 5000);
  }

  function wpr_autouploader_status() {
    wpr_autouploader_thread = setInterval(function () {
      //setInterval
      $.get({
        url: ajaxurl,
        data: {
          action: "wp_reset_run_tool",
          _ajax_nonce: wp_reset.nonce_run_tool,
          tool: "autouploader_status",
          extra_data: {},
        },
      })
        .done(function (response) {
          if (response.data) {
            var snapshots_count = 0;
            $(".wpr-autouploader-info").remove();
            for (snapshot in response.data) {
              var $snapshot_html = $("#wpr-ss-" + snapshot).find(".ss-name");
              if (response.data[snapshot].status == "finished") {
                if ($snapshot_html.children(".dashicons-cloud").length == 0) {
                  $snapshot_html.append(
                    '<span data-tooltip="Available in the cloud" class="dashicons dashicons-cloud tooltipstered"></span>'
                  );
                }
              } else if (response.data[snapshot].status == "error") {
                $snapshot_html.append(
                  '<div class="wpr-autouploader-info wpr-autouploader-info-error">' +
                    response.data[snapshot].message +
                    "</div>"
                );
              } else {
                snapshots_count++;
                $snapshot_html.append(
                  '<div class="wpr-collections-installer-loading wpr-autouploader-info"><span class="dashicons"></span>' +
                    response.data[snapshot].message +
                    "</div>"
                );
              }
            }

            if (snapshots_count > 0) {
              $(".wpr-adminbar-icon").addClass("wpr-autouploader-icon");
              if($("#wpr-autouploader-status").length){
                $("#wp-admin-bar-wpr-reset-ab-default").children('a').html('Uploading ' + snapshots_count + ' Automatic Snapshot' + (snapshots_count>1?'s':''));
              } else {
                $("#wp-admin-bar-wpr-reset-ab-default").prepend('<li id="wpr-autouploader-status"><a class="ab-item" href="' + wp_reset.settings_url + '#tab-snapshots">Uploading ' + snapshots_count + ' Automatic Snapshot' + (snapshots_count>1?'s':'') + '</a></li>');
              }
            } else {
              $(".wpr-adminbar-icon").removeClass("wpr-autouploader-icon");
              $("#wpr-autouploader-status").remove();
            }
          }
        })
        .error(function (response) {
          //console.log("Failed retrieving autouploader status");
        });
    }, 10000);
  }

  function wpr_autouploader_check() {
    if (
      !localStorage.hasOwnProperty(wp_reset.autouploader_key) ||
      wpr_autouploader == true ||
      Date.now() - localStorage[wp_reset.autouploader_key] > 10000
    ) {
      localStorage[wp_reset.autouploader_key] = Date.now();
      wpr_autouploader = true;
      wpr_autouploader_run();
    } else {
      window.clearTimeout(wpr_autouploader_check_timer);
      wpr_autouploader_check_timer = window.setTimeout(function () {
        wpr_autouploader_check();
      }, 5000);
    }
  }

  //autosnapshots autoupload
  if (wp_reset.autosnapshots_autoupload == 1) {
    wpr_autouploader_check();
    wpr_autouploader_status();
  }

  var create_snapshot_steps;

  function create_snapshot(
    description,
    auto = 0,
    success_callback,
    callback_args
  ) {
    description = $.trim(description);

    $.Deferred(function () {
      if (description) {
        this.resolve(description);
      } else {
        wpr_swal
          .fire({
            title: wp_reset.snapshot_title,
            type: "question",
            text: wp_reset.snapshot_text,
            input: "text",
            inputPlaceholder: wp_reset.snapshot_placeholder,
            showCancelButton: true,
            focusConfirm: false,
            confirmButtonText: wp_reset.snapshot_confirm,
            cancelButtonText: wp_reset.cancel_button,
            width: 600,
          })
          .then((result) => {
            if (result.dismiss || typeof result.value == "undefined") {
              return;
            } else {
              this.resolve(result.value);
            }
          });
      }
    }).then((result) => {
      if (typeof result != "undefined") {
        snapshot_swal = wpr_swal.fire({
          title: wp_reset.snapshot_wait,
          text: wp_reset.snapshot_wait,
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
          imageAlt: wp_reset.snapshot_wait,
        });

        tmp = $.get({
          url: ajaxurl,
          data: {
            action: "wp_reset_run_tool",
            _ajax_nonce: wp_reset.nonce_run_tool,
            tool: "create_snapshot",
            extra_data: { name: result, ajax: 1, auto: auto },
          },
        })
          .always(function (data) {
            if (typeof callback == "function") {
              //todo: add support failed
            }
          })
          .done(function (data) {
            if (data.success) {
              create_snapshot_steps = data.data;
              create_snapshot_run_steps(auto, success_callback, callback_args);
            } else {
              wpr_swal.close();
              wpr_swal.fire({
                type: "error",
                title: wp_reset.documented_error + " " + data.data,
              });
            }

            return data;
          })
          .fail(function (data) {
            wpr_swal.close();
            wpr_swal.fire({
              type: "error",
              title: wp_reset.undocumented_error,
            });
          });
      } // if confirmed
    });
  }

  function create_snapshot_run_steps(auto, success_callback, callback_args) {
    var looper = $.Deferred().resolve();

    wpr_swal
      .getContent()
      .querySelector("#swal2-content")
      .classList.add("swal-content-left");

    var failed = false;
    $.each(create_snapshot_steps, function (i, data) {
      looper = looper.then(function () {
        if (failed) {
          return false;
        }

        wpr_swal.getContent().querySelector("#swal2-content").innerHTML =
          i + 1 + "/" + create_snapshot_steps.length + " - " + data.description;

        return $.ajax({
          data: {
            action: "wp_reset_run_tool",
            _ajax_nonce: wp_reset.nonce_run_tool,
            tool: "create_snapshot_step",
            extra_data: data,
          },
          url: ajaxurl,
        })
          .done(function (response) {
            if (response.success) {
              failed = false;
              if (i == create_snapshot_steps.length - 1) {
                if (wp_reset.is_plugin_page) {
                  if (auto) {
                    $("#snapshots-table-auto tr:nth-child(2)").after(
                      response.data.html
                    );
                  } else {
                    $("#snapshots-table-user tr:nth-child(2)").after(
                      response.data.html
                    );
                  }
                }

                if(wp_reset.snapshots_autoupload == true && response.data.auto == '0'){
                    export_snapshot(response.data.uid, wpr_cloud, ['snapshot_upload', response.data.uid, wp_reset.cloud_snapshot_uploading, wp_reset.cloud_snapshot_uploaded, 'reload']);
                } else if (typeof success_callback == "function") {
                  if (auto) {
                    wpr_autouploader_check();
                    wpr_autouploader_status();
                  }

                  success_callback.apply(this, callback_args);
                } else {
                  wpr_swal.fire({
                    type: "success",
                    title: wp_reset.snapshot_success,
                    timer: 1500,
                    showConfirmButton: false,
                  });
                }
              }
            } else {
              wpr_swal.close();
              wpr_swal.fire({
                type: "error",
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
              type: "error",
              title: wp_reset.undocumented_error,
            });
            failed = true;
            return false;
          });
      });
    });

    return;
  }

  var export_snapshot_steps;
  function export_snapshot(snapshot, success_callback, callback_args) {
    snapshot_swal = wpr_swal.fire({
      title: wp_reset.export_wait,
      text: wp_reset.export_wait,
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
      imageAlt: wp_reset.export_wait,
    });

    tmp = $.get({
      url: ajaxurl,
      data: {
        action: "wp_reset_run_tool",
        _ajax_nonce: wp_reset.nonce_run_tool,
        tool: "download_snapshot",
        extra_data: { uid: snapshot, ajax: 1 },
      },
    })
      .done(function (data) {
        if (data.success) {
          if (Array.isArray(data.data)) {
            export_snapshot_steps = data.data;
            export_snapshot_run_steps(success_callback, callback_args);
          } else {
            if (typeof success_callback == "function") {
              success_callback.apply(this, callback_args);
            } else {
              msg = $(button).data("success-msg").replace("%s", data.data);
              wpr_swal.fire({ type: "success", title: msg });
            }
          }
        } else {
          wpr_swal.close();
          wpr_swal.fire({
            type: "error",
            title: wp_reset.documented_error + " " + data.data,
          });
        }

        return data;
      })
      .fail(function (data) {
        wpr_swal.close();
        wpr_swal.fire({ type: "error", title: wp_reset.undocumented_error });
      });
  }

  function export_snapshot_run_steps(success_callback, callback_args) {
    var looper = $.Deferred().resolve();

    wpr_swal
      .getContent()
      .querySelector("#swal2-content")
      .classList.add("swal-content-left");

    var failed = false;

    $.each(export_snapshot_steps, function (i, data) {
      looper = looper.then(function () {
        if (failed) {
          return false;
        }

        wpr_swal.getContent().querySelector("#swal2-content").innerHTML =
          i + 1 + "/" + export_snapshot_steps.length + " - " + data.description;
        return $.ajax({
          data: {
            action: "wp_reset_run_tool",
            _ajax_nonce: wp_reset.nonce_run_tool,
            tool: "export_snapshot_step",
            extra_data: data,
          },
          url: ajaxurl,
        })
          .done(function (response) {
            if (response.success) {
              failed = false;
              if (i == export_snapshot_steps.length - 1) {
                if (typeof success_callback == "function") {
                  success_callback.apply(this, callback_args);
                } else {
                  msg = $(button)
                    .data("success-msg")
                    .replace("%s", response.data);
                  wpr_swal.fire({ type: "success", title: msg });
                }
              }
            } else {
              wpr_swal.close();
              wpr_swal.fire({
                type: "error",
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
              type: "error",
              title: wp_reset.undocumented_error,
            });
            failed = true;
            return false;
          });
      });
    });

    return;
  }

  // standard way of running a tool, with confirmation, loading and success message
  function wpr_cloud_confirm(
    button,
    action,
    parameters,
    swal_text,
    success_message,
    success_callback,
    callback_args
  ) {
    var confirm_title =
      $(button).data("confirm-title") || wp_reset.confirm_title;
    var btn_confirm = $(button).data("btn-confirm") || $(button).text();

    wpr_close_dropdowns();

    confirm_action(
      confirm_title,
      $(button).data("text-confirm"),
      btn_confirm,
      wp_reset.cancel_button
    ).then((result) => {
      if (result.value) {
        wpr_cloud(
          action,
          parameters,
          swal_text,
          success_message,
          success_callback,
          callback_args
        );
      } // if confirmed
    });
  }

  function wpr_cloud(
    action,
    parameters,
    swal_text,
    success_message,
    success_callback,
    callback_args
  ) {
    snapshot_swal = wpr_swal.fire({
      title: swal_text,
      text: swal_text,
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
      imageAlt: swal_text,
    });

    tmp = $.get({
      url: ajaxurl,
      data: {
        action: "wp_reset_run_tool",
        _ajax_nonce: wp_reset.nonce_run_tool,
        tool: "cloud_action",
        extra_data: { action: action, parameters: parameters },
      },
    })
      .always(function (data) {
        if (typeof callback == "function") {
          //todo: add support failed
        }
      })
      .done(function (response) {
        if (response.success) {
          if (response.data && response.data.continue == 1) {
            wpr_cloud_do_action(
              response.data,
              success_message,
              success_callback,
              callback_args
            );
          } else {
            if (response.data && response.data.action == "import") {
              import_snapshot(response.data.uid);
            }

            if (typeof success_callback == "function") {
              success_callback.apply(this, callback_args);
            } else {
              wpr_swal
                .fire({
                  type: "success",
                  title: success_message,
                  timer: 1500,
                  showConfirmButton: false,
                })
                .then(() => {
                  if (success_callback == "reload") {
                    window.location.href = wp_reset.settings_url;
                  } else {
                    wpr_swal.close();
                  }
                });
            }
          }
        } else {
          wpr_swal.close();
          wpr_swal.fire({
            type: "error",
            title: wp_reset.documented_error + " " + response.data,
          });
        }

        return response;
      })
      .fail(function (data) {
        wpr_swal.close();
        wpr_swal.fire({ type: "error", title: wp_reset.undocumented_error });
      });
  }

  function wpr_cloud_do_action(
    action,
    success_message,
    success_callback,
    callback_args
  ) {
    $.ajax({
      data: {
        action: "wp_reset_run_tool",
        _ajax_nonce: wp_reset.nonce_run_tool,
        tool: "cloud_action",
        extra_data: action,
      },
      url: ajaxurl,
    })
      .done(function (response) {
        if (response.success) {
          if (response.data && response.data.continue == 1) {
            wpr_swal.getContent().querySelector("#swal2-content").innerHTML =
              response.data.message;
            wpr_cloud_do_action(
              response.data,
              success_message,
              success_callback,
              callback_args
            );
          } else {
            if (
              response.data &&
              response.data.action == "import_snapshot_steps"
            ) {
              import_snapshot_steps = response.data.steps;
              import_snapshot_run_steps(success_callback, callback_args);
            } else if (typeof success_callback == "function") {
              success_callback.apply(this, callback_args);
            } else {
              wpr_swal
                .fire({
                  type: "success",
                  title: success_message,
                  timer: 1500,
                  showConfirmButton: false,
                })
                .then(() => {
                  if (success_callback == "reload") {
                    window.location.href = wp_reset.settings_url;
                  } else {
                    wpr_swal.close();
                  }
                });
            }
          }
        } else {
          wpr_swal.close();
          wpr_swal.fire({
            type: "error",
            title: wp_reset.documented_error,
            text: response.data,
          });
          failed = true;
          return false;
        }
      })
      .error(function (response) {
        wpr_swal.close();
        wpr_swal.fire({ type: "error", title: wp_reset.undocumented_error });
        failed = true;
        return false;
      });
  }

  // create snapshot button in WPR
  $(document).on("click", ".create-new-snapshot", function (e) {
    e.preventDefault();
    description = $(this).data("snapshot-description") || "";

    create_snapshot(description);

    return false;
  }); // create snapshot button in WPR

  function import_snapshot(success_callback, callback_args) {
    wpr_swal
      .fire({
        title: "Select a file",
        showCancelButton: true,
        confirmButtonText: "Upload",
        input: "file",
        onBeforeOpen: () => {
          $(".swal2-file").change(function () {
            if (this.files[0].size > wp_reset.max_upload_size) {
              $(".swal2-file").val("");
              alert(
                "The import size exceeds the maximum upload size of your website!"
              );
              return;
            }
            var reader = new FileReader();
            reader.readAsDataURL(this.files[0]);
          });
        },
        width: 600,
      })
      .then((file) => {
        if (file.value) {
          var formData = new FormData();
          var file = $(".swal2-file")[0].files[0];
          formData.append("snapshot_zip", file);
          formData.append("action", "wp_reset_run_tool");
          formData.append("tool", "import_snapshot");
          formData.append("_ajax_nonce", wp_reset.nonce_run_tool);
          block = block_ui(wp_reset.snapshot_importing);

          $.ajax({
            method: "post",
            url: ajaxurl,
            data: formData,
            processData: false,
            contentType: false,
            success: function (data) {
              if (data.success) {
                if (data.data == true) {
                  wpr_swal
                    .fire({
                      type: "success",
                      title:
                        wp_reset.import_success +
                        " The page will reload in a moment.",
                      timer: 1500,
                      showConfirmButton: false,
                    })
                    .then(() => {
                      window.location.href = wp_reset.settings_url;
                    });
                } else {
                  import_snapshot_steps = data.data;
                  import_snapshot_run_steps(success_callback, callback_args);
                }
              } else {
                wpr_swal.fire({
                  type: "error",
                  title: wp_reset.documented_error + " " + data.data,
                });
              }
            },
            error: function () {
              wpr_swal.fire({
                type: "error",
                title: wp_reset.undocumented_error,
              });
            },
          });
        }
      });
  } // import_snapshot

  var import_snapshot_steps;
  function import_snapshot_run_steps(success_callback, callback_args) {
    var looper = $.Deferred().resolve();

    wpr_swal
      .getContent()
      .querySelector("#swal2-content")
      .classList.add("swal-content-left");

    var failed = false;

    $.each(import_snapshot_steps, function (i, data) {
      looper = looper.then(function () {
        if (failed) {
          return false;
        }

        wpr_swal.getContent().querySelector("#swal2-content").innerHTML =
          i + 1 + "/" + import_snapshot_steps.length + " - " + data.description;
        return $.ajax({
          data: {
            action: "wp_reset_run_tool",
            _ajax_nonce: wp_reset.nonce_run_tool,
            tool: "import_snapshot_step",
            extra_data: data,
          },
          url: ajaxurl,
        })
          .done(function (response) {
            if (response.success) {
              failed = false;
              if (i == import_snapshot_steps.length - 1) {
                if (typeof success_callback == "function") {
                  success_callback.apply(this, callback_args);
                } else {
                  wpr_swal
                    .fire({
                      type: "success",
                      title:
                        wp_reset.import_success +
                        " The page will reload in a moment.",
                      timer: 1500,
                      showConfirmButton: false,
                    })
                    .then(() => {
                      window.location.href = wp_reset.settings_url;
                    });
                }
              }
            } else {
              wpr_swal.close();
              wpr_swal.fire({
                type: "error",
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
              type: "error",
              title: wp_reset.undocumented_error,
            });
            failed = true;
            return false;
          });
      });
    });

    return;
  }

  // import snapshot button in WPR
  $(document).on("click", ".import-snapshot", function (e) {
    e.preventDefault();

    import_snapshot();

    return false;
  }); // import snapshot button in WPR

  // delete snapshots button in WPR
  $(document).on("click", ".delete-snapshots", function (e) {
    e.preventDefault();
    var delete_snapshots = $(this).data("snapshots");
    var selected_snapshots = [];
    if($(this).data("snapshots") == 'selected_user'){
        $.each($("input[name='selected_snapshots']:checked"), function(){
            selected_snapshots.push($(this).val());
        });
        delete_snapshots = 'selected';
    }
    if($(this).data("snapshots") == 'selected_auto'){
        $.each($("input[name='selected_autosnapshots']:checked"), function(){
            selected_snapshots.push($(this).val());
        });
        delete_snapshots = 'selected';
    }

    if(delete_snapshots == 'selected' && selected_snapshots.length == 0){
        wpr_swal.fire({
            type: "error",
            title: 'No snapshots selected',
        });
        return;
    }
    run_tool_confirm(this, "delete_snapshots", {
      delete: delete_snapshots,
      ids: selected_snapshots
    });

    return false;
  }); // delete snapshots button in WPR

  // delete cloud snapshots button in WPR
  $(document).on("click", ".delete-cloud-snapshots", function (e) {
    e.preventDefault();
    var delete_cloud_snapshots = $(this).data("snapshots");
    var selected_snapshots = [];
    if($(this).data("snapshots") == 'selected_user'){
        $.each($("input[name='selected_snapshots']:checked"), function(){
            selected_snapshots.push($(this).val());
        });
        delete_cloud_snapshots = 'selected';
    }
    if($(this).data("snapshots") == 'selected_auto'){
        $.each($("input[name='selected_autosnapshots']:checked"), function(){
            selected_snapshots.push($(this).val());
        });
        delete_cloud_snapshots = 'selected';
    }

    if(delete_cloud_snapshots == 'selected' && selected_snapshots.length == 0){
        wpr_swal.fire({
            type: "error",
            title: 'No snapshots selected',
        });
        return;
    }
    run_tool_confirm(this, "delete_cloud_snapshots", {
      delete: delete_cloud_snapshots,
      ids: selected_snapshots
    });

    return false;
  }); // delete snapshots button in WPR

  // Collections
  var collections_ajax_queue = [];
  var collections_ajax_queue_count = 0;
  var collections_ajax_queue_index = 0;
  var collections_errors = [];
  var collections_retried = false;

  $(document).on("click", ".install-collection", function (e) {
    e.preventDefault();
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
              '<span class="wpr-collections-installer-message-text">' +
              wp_reset.installing +
              " " +
              item_data.name +
              "</span>" +
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
                '<span class="wpr-collections-installer-message-text">' +
                wp_reset.activating +
                " " +
                item_data.name +
                "</span>" +
                "</div>"
            );

            if (item_data.license_key.length > 0) {
              collections_ajax_queue.push({
                slug: item_data.slug,
                name: item_data.name,
                extra_data: {
                  license_key: item_data.license_key,
                },
                action: "activate_license_" + item_data.type,
              });

              collections_ajax_queue.push({
                slug: item_data.slug,
                name: item_data.name,
                extra_data: {
                  license_key: item_data.license_key,
                },
                action: "check_activate_license_" + item_data.type,
              });

              $(".wpr-collections-installer").append(
                '<div class="wpr-collections-installer-message" data-action="' +
                  item_data.slug +
                  "_activate_license_" +
                  item_data.type +
                  '"><span class="dashicons"></span>' +
                  '<span class="wpr-collections-installer-message-text">' +
                  wp_reset.activating_license +
                  " " +
                  item_data.name +
                  "</span>" +
                  "</div>"
              );
            }
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

    if (do_delete) {
      if (wp_reset.collections[collection_id]["has_plugins"]) {
        for (plugin in wp_reset.installed_plugins) {
          plugin_data = wp_reset.installed_plugins[plugin];
          if (plugin_data["active"] == "1") {
            // Deactivate Plugin First
            collections_ajax_queue.push({
              slug: plugin_data.slug,
              name: plugin_data.name,
              action: "deactivate_plugin",
            });
            collections_ajax_queue.push({
              slug: plugin_data.slug,
              name: plugin_data.name,
              action: "check_deactivate_plugin",
            });
          }
          collections_ajax_queue.push({
            slug: plugin_data.slug,
            name: plugin_data.name,
            action: "delete_plugin",
          });
          collections_ajax_queue.push({
            slug: plugin_data.slug,
            name: plugin_data.name,
            action: "check_delete_plugin",
          });
        }
      }

      if (wp_reset.collections[collection_id]["has_themes"]) {
        for (theme in wp_reset.installed_themes) {
          theme_data = wp_reset.installed_themes[theme];
          collections_ajax_queue.push({
            slug: theme_data.slug,
            name: theme_data.name,
            action: "delete_theme",
          });
          collections_ajax_queue.push({
            slug: theme_data.slug,
            name: theme_data.name,
            action: "check_delete_theme",
          });
        }
      }
    }

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

        if (item_data.license_key.length > 0) {
          collections_ajax_queue.push({
            slug: item_data.slug,
            name: item_data.name,
            extra_data: {
              license_key: item_data.license_key,
            },
            action: "activate_license_" + item_data.type,
          });

          collections_ajax_queue.push({
            slug: item_data.slug,
            name: item_data.name,
            extra_data: {
              license_key: item_data.license_key,
            },
            action: "check_activate_license_" + item_data.type,
          });
        }
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
            '<span class="wpr-collections-installer-message-text">' +
            message +
            " " +
            collections_ajax_queue[ci].name +
            "</span>" +
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
                  //error = wp_reset.activating_license_unknown + ' ' + collection_item.name;
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
                type: "error",
                title:
                  "Collection was installed, but the following errors occurred!", //todo: localize
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
                type: "success",
                title: "Collection was installed successfully!", //todo: localize
                showConfirmButton: true,
              })
              .then((result) => {
                location.reload();
              });
          }
        }
      },
    });
  } //run collection ajax

  // onboarding

  $(".tools_page_wp-reset").on("click", ".delete-recovery-script", function (
    e
  ) {
    e.preventDefault();

    block = block_ui("Removing emergency recovery script");
    tmp = $.get({
      url: ajaxurl,
      data: {
        action: "wp_reset_run_tool",
        _ajax_nonce: wp_reset.nonce_run_tool,
        tool: "uninstall_recovery",
      },
    })
      .always(function (data) {
        wpr_swal.close();
      })
      .done(function (data) {
        if (data.success) {
          wpr_swal
            .fire({
              type: "success",
              title: "Emergency recovery script has been removed",
              width: 600,
              allowEnterKey: false,
              showCancelButton: false,
              showCloseButton: false,
              showConfirmButton: false,
              timer: 2000,
            })
            .then((result) => {
              window.location.href = wp_reset.settings_url;
            });
        } else {
          wpr_swal
            .fire({
              type: "error",
              title:
                "Emergency recovery script could not be removed!<br />" +
                data.data,
              width: 600,
              allowEnterKey: false,
              showCancelButton: false,
              showCloseButton: true,
              confirmButtonText: "Close",
            })
            .then((result) => {
              wpr_swal.close();
            });
        }
        return data;
      })
      .fail(function (data) {
        wpr_swal.fire({ type: "error", title: wp_reset.undocumented_error });
      });

    return false;
  });

  $(".tools_page_wp-reset").on("click", ".update-recovery-script", function (
    e
  ) {
    e.preventDefault();

    block = block_ui("Updating emergency recovery script");
    tmp = $.get({
      url: ajaxurl,
      data: {
        action: "wp_reset_run_tool",
        _ajax_nonce: wp_reset.nonce_run_tool,
        tool: "update_recovery",
      },
    })
      .always(function (data) {
        wpr_swal.close();
      })
      .done(function (data) {
        if (data.success) {
          wpr_swal
            .fire({
              type: "success",
              title: "Emergency recovery script has been updated",
              width: 600,
              allowEnterKey: false,
              showCancelButton: false,
              showCloseButton: false,
              showConfirmButton: false,
              timer: 2000,
            })
            .then((result) => {
              window.location.href = wp_reset.settings_url;
            });
        } else {
          wpr_swal
            .fire({
              type: "error",
              title:
                "Emergency recovery script could not be updated!<br />" +
                data.data,
              width: 600,
              allowEnterKey: false,
              showCancelButton: false,
              showCloseButton: true,
              confirmButtonText: "Close",
            })
            .then((result) => {
              wpr_swal.close();
            });
        }
        return data;
      })
      .fail(function (data) {
        wpr_swal.fire({ type: "error", title: wp_reset.undocumented_error });
      });

    return false;
  });

  $(".tools_page_wp-reset").on("click", ".install-recovery-script", function (
    e
  ) {
    e.preventDefault();

    block = block_ui("Installing emergency recovery script");
    tmp = $.get({
      url: ajaxurl,
      data: {
        action: "wp_reset_run_tool",
        _ajax_nonce: wp_reset.nonce_run_tool,
        tool: "install_recovery",
      },
    })
      .always(function (data) {
        wpr_swal.close();
      })
      .done(function (data) {
        if (data.success) {
          wpr_swal
            .fire({
              type: "success",
              title: "Emergency recovery script has been installed",
              width: 600,
              allowEnterKey: false,
              showCancelButton: false,
              showCloseButton: false,
              showConfirmButton: false,
              timer: 2000,
            })
            .then((result) => {
              window.location.href = wp_reset.settings_url;
            });
        } else {
          wpr_swal
            .fire({
              type: "error",
              title:
                "Emergency recovery script could not be installed!<br />" +
                data.data,
              width: 600,
              allowEnterKey: false,
              showCancelButton: false,
              showCloseButton: true,
              confirmButtonText: "Close",
            })
            .then((result) => {
              wpr_swal.close();
            });
        }
        return data;
      })
      .fail(function (data) {
        wpr_swal.fire({ type: "error", title: wp_reset.undocumented_error });
      });

    return false;
  });

  if (wp_reset.onboarding_done == false) {
    onboarding_open_step0();
  }

  if ("js_notice" in wp_reset) {
    wpr_swal
      .fire({
        title: wp_reset.js_notice.text,
        type: wp_reset.js_notice.type,
        width: 600,
        height: 300,
        allowEnterKey: true,
        showCancelButton: true,
        showConfirmButton: false,
        showCloseButton: true,
        allowEscapeKey: true,
        allowOutsideClick: false,
        cancelButtonText: "Close",
      })
      .then((result) => {
        window.location.href = wp_reset.settings_url;
      });
  }

  $(".tools_page_wp-reset").on("click", ".open-onboarding", function (e) {
    e.preventDefault();

    onboarding_open_step0();

    return false;
  });

  function dismiss_onboarding() {
    $.get({
      url: ajaxurl,
      data: {
        action: "wp_reset_run_tool",
        _ajax_nonce: wp_reset.nonce_run_tool,
        tool: "onboarding_done",
      },
    });
  } // dismiss_onboarding

  function onboarding_open_step0() {
    html = '<div class="wpr-onboarding">';
    if(wp_reset.rebranding !== "0"){
        html += "<h2>Welcome to " + wp_reset.rebranding.name + "!</h2>";
    } else {
        html += "<h2>Welcome to WP Reset PRO!</h2>";
    }
    html +=
      '<p class="textleft">Onboarding takes only a minute. It creates a test snapshot to see if your site meets the requirements, and it sets up the emergency recovery script. Everything found in onboarding can be changed and configured later on too.</p><p class="textleft">If you want to re-run onboarding open the Support tab and click "run onboarding".</p><br>';
    html += "</div>";

    wpr_swal
      .fire({
        html: html,
        width: 800,
        height: 400,
        allowEnterKey: true,
        showCancelButton: true,
        showCloseButton: true,
        allowEscapeKey: true,
        allowOutsideClick: false,
        confirmButtonText: "Start onboarding",
        cancelButtonText: "Skip onboarding",
      })
      .then((result) => {
        if (result.value) {
          onboarding_open_step1();
        } else {
          dismiss_onboarding();
          wpr_swal.close();
        }
      });
  } // onboarding_open_step0

  $(".tools_page_wp-reset").on("change", ".change-single-option", function () {
    tmp = $.get({
      url: ajaxurl,
      data: {
        action: "wp_reset_run_tool",
        _ajax_nonce: wp_reset.nonce_run_tool,
        tool: "change_single_option",
        extra_data: {
          option: $(this).data("option"),
          value: $(this).is(":checked"),
        },
      },
    });
  });

  function onboarding_open_step1() {
    block = block_ui("Creating a test snapshot. Please wait.");
    tmp = $.get({
      url: ajaxurl,
      data: {
        action: "wp_reset_run_tool",
        _ajax_nonce: wp_reset.nonce_run_tool,
        tool: "test_snapshot",
      },
    })
      .always(function (data) {
        wpr_swal.close();
      })
      .done(function (data) {
        html = '<div class="wpr-onboarding">';

        if (data.success) {
          if (data.data.auto) {
            html +=
              "<h2>Everything looks good!<br>Auto snapshots are enabled on updates and tools.</h2>";
          } else {
            html +=
              "<h2>It took a bit longer than we expected to do a test snapshot so we disabled auto snapshots. You can still enable them if you wish.</h2>";
          }
        } else {
          html += "<h2>An error occurred creating the snapshot</h2>";
          html += '<h2 class="red">' + data.data + "</h2>";
          html +=
            "<h2>We disabled autosnapshots for now. Please resolve the error above and then you can enable auto snapshots in the Snapshots tab.</h2>";
        }

        let plugin_name = 'WP Reset';
        if(wp_reset.rebranding !== "0"){
            plugin_name = wp_reset.rebranding.name;
        }

        html +=
          '<div class="option-group"><div class="toggle-wrapper"><input type="checkbox" id="ob_option_tools_snapshots" class="change-single-option" data-option="tools_snapshots" ' +
          (data.data.auto ? 'checked="checked"' : "") +
          ' value="1" name="ob_option_tools_snapshots"><label for="ob_option_tools_snapshots" class="toggle"><span class="toggle_handler"></span></label></div><div class="option-group-desc">Automatically create snapshots before running ' + plugin_name + ' tools</div></div>';
        html +=
          '<div class="option-group"><div class="toggle-wrapper"><input type="checkbox" id="ob_option_events_snapshots" class="change-single-option" data-option="events_snapshots" ' +
          (data.data.auto ? 'checked="checked"' : "") +
          ' value="1" name="ob_option_events_snapshots"><label for="ob_option_events_snapshots" class="toggle"><span class="toggle_handler"></span></label></div><div class="option-group-desc">Automatically create snapshots when manipulating plugins and themes</div></div>';

        html += "</div>";

        wpr_swal
          .fire({
            html: html,
            width: 800,
            allowEnterKey: false,
            showCancelButton: false,
            showCloseButton: true,
            allowOutsideClick: false,
            confirmButtonText: "Next - configure Emergency Recovery Script",
            allowOutsideClick: false,
          })
          .then((result) => {
            if (result.dismiss === wpr_swal.DismissReason.close) {
              dismiss_onboarding();
            }
            if (result.value) {
              onboarding_open_step2();
            } else {
              wpr_swal.close();
            }
          });

        return data;
      })
      .fail(function (data) {
        wpr_swal.fire({ type: "error", title: wp_reset.undocumented_error });
      });
  }

  function onboarding_open_step2() {
    wpr_swal.close();

    html = '<div class="wpr-onboarding">';
    html += "<h2>Emergency Recovery Script setup</h2>";
    html +=
      '<p class="textleft">Emergency recovery script is a standalone, single-file, WP independent script created to recover a WP site in the most difficult situations. When access to admin is not possible, when core files are compromised, when you get the white-screen or can\'t log in. <b>There are two ways to set up the script:</b></p>';

    html += '<div class="half">';
    html +=
      '<p class="textleft" style="padding-right: 30px; box-sizing: border-box;">';
    html += "<b>Install the script now</b><br>";
    html +=
      "In case something goes wrong, the script will already be available. ";
    html +=
      "We'll generate a unique password so that only you can access the script.";
    html += " This setup is recommended for development environments.";
    html += "</p>";
    html += "</div>";

    html += '<div class="half">';
    html +=
      '<p class="textleft" style="padding-left: 30px; box-sizing: border-box;">';
    html += "<b>Install the script later, when needed</b><br>";
    html += 'When needed you\'ll install the script from the "Support" tab';
    if(wp_reset.rebranding === "0"){
        html += ', or generate a new instance in WP Reset Dashboard, and upload manually via FTP. ';
    } else {
        html += '. ';
    }

    html += "This setup is recommended for production environments.";
    html += "</p>";
    html += "</div>";

    html +=
      "<br><br>Do you want to install the Emergency Recovery script on your server?<br />";

    wpr_swal
      .fire({
        html: html,
        width: 800,
        allowEnterKey: false,
        allowOutsideClick: false,
        showCancelButton: true,
        showCloseButton: true,
        allowOutsideClick: false,
        confirmButtonText: "Yes, install the script now",
        cancelButtonText: "No, I'll install it when needed",
      })
      .then((result) => {
        if (result.value) {
          block = block_ui("Setting up recovery script.");
          tmp = $.get({
            url: ajaxurl,
            data: {
              action: "wp_reset_run_tool",
              _ajax_nonce: wp_reset.nonce_run_tool,
              tool: "install_recovery",
            },
          })
            .always(function (data) {
              wpr_swal.close();
            })
            .done(function (data) {
              html = '<div class="wpr-onboarding">';
              type = "success";
              if (data.success) {
                html +=
                  "<h2>Emergency recovery script has been installed successfully!</h2>";
                html +=
                  'You can access it on the URL below with the following password: <strong style="color:#F00;">' +
                  data.data.pass +
                  "</strong>. More details are available in the Support tab:<br />";
                html +=
                  '<a href="' +
                  data.data.url +
                  '" target="_blank">' +
                  data.data.url +
                  "</a>";
              } else {
                html +=
                  "<h2>Emergency recovery script could not be installed!</h2>";
                html += data.data;
                type = "error";
              }
              html += "</div>";

              wpr_swal
                .fire({
                  html: html,
                  type: type,
                  width: 800,
                  allowEnterKey: false,
                  showCancelButton: false,
                  showCloseButton: true,
                  allowOutsideClick: false,
                  confirmButtonText: "Finish",
                  cancelButtonText: "Skip",
                })
                .then((result) => {
                  dismiss_onboarding();
                  if (result.value) {
                    //onboarding_open_step3();
                    wpr_swal.close();
                  } else {
                    wpr_swal.close();
                  }
                });
            })
            .fail(function (data) {
              wpr_swal.fire({
                type: "error",
                title: wp_reset.undocumented_error,
              });
            });
        } else {
          if (result.dismiss === wpr_swal.DismissReason.close) {
            dismiss_onboarding();
          }
          if (result.dismiss == "cancel") {
            dismiss_onboarding();
            //onboarding_open_step3();
            wpr_swal.close();
          } else {
            dismiss_onboarding();
            wpr_swal.close();
          }
        }
      });
  }

  function onboarding_open_step3() {
    wpr_swal.close();

    html = '<div class="wpr-onboarding">';
    html +=
      "<h2>Reminder email</h2><p>An email with instructions for installing the WP Reset Emergency recovery script manually will be sent to you. Please verify that the email address below is correct:</p>";
    html +=
      'Administrator email address: <input type="text" id="onboarding_recovery_email" value="' +
      wp_reset.admin_email +
      '" />';
    html += "</div>";

    wpr_swal
      .fire({
        html: html,
        width: 800,
        allowEnterKey: false,
        showCancelButton: false,
        showCloseButton: true,
        confirmButtonText: "Send",
      })
      .then((result) => {
        if (result.value) {
          admin_email = $("#onboarding_recovery_email").val();
          block = block_ui("Sending email.");
          tmp = $.get({
            url: ajaxurl,
            data: {
              action: "wp_reset_run_tool",
              _ajax_nonce: wp_reset.nonce_run_tool,
              tool: "email_recovery",
              extra_data: admin_email,
            },
          })
            .always(function (data) {
              wpr_swal.close();
            })
            .done(function (data) {
              html = '<div class="wpr-onboarding">';
              type = "success";
              if (data.success) {
                html += "<h2>Instructions emailed successfully!</h2>";
              } else {
                html += "<h2>An error occurred emailing the instructions!</h2>";
                html += data.data;
                type = "error";
              }
              html += "</div>";

              wpr_swal
                .fire({
                  html: html,
                  type: type,
                  width: 800,
                  allowEnterKey: false,
                  showCancelButton: false,
                  showCloseButton: true,
                  confirmButtonText: "Finish",
                })
                .then((result) => {
                  wpr_swal.close();
                });
            })
            .fail(function (data) {
              wpr_swal.fire({
                type: "error",
                title: wp_reset.undocumented_error,
              });
            });
        } else {
          wpr_swal.close();
        }
      });
  }

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

  $("body.tools_page_wp-reset").on(
    "click",
    ".toggle-auto-activation-supported-list",
    function (e) {
      e.preventDefault();
      $(this).replaceWith(":");
      $(".auto-activation-supported-list").toggleClass("hidden");
    }
  );

  // standard way of running a tool, with confirmation, loading and success message
  function run_tool_confirm(
    button,
    tool_name,
    extra_data,
    auto_close,
    success_callback
  ) {
    var confirm_title =
      $(button).data("confirm-title") || wp_reset.confirm_title;
    var btn_confirm = $(button).data("btn-confirm") || $(button).text();

    wpr_close_dropdowns();

    confirm_action(
      confirm_title,
      $(button).data("text-confirm"),
      btn_confirm,
      wp_reset.cancel_button
    ).then((result) => {
      if (result.value) {
        if (wp_reset.tools_autosnapshot.hasOwnProperty(tool_name)) {
          create_snapshot(wp_reset.tools_autosnapshot[tool_name], 1, run_tool, [
            button,
            tool_name,
            extra_data,
            auto_close,
            success_callback,
          ]);
        } else {
          run_tool(button, tool_name, extra_data, auto_close, success_callback);
        }
      } // if confirmed
    });
  }

  // standard way of running a tool, with confirmation, loading and success message
  function run_tool(
    button,
    tool_name,
    extra_data,
    auto_close,
    success_callback
  ) {
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
          if (auto_close) {
            wpr_swal_params = {
              type: "success",
              title: msg,
              timer: 1500,
              showConfirmButton: false,
            };
          } else {
            wpr_swal_params = { type: "success", title: msg };
          }
          wpr_swal.fire(wpr_swal_params).then(() => {
            if (
              tool_name == "restore_snapshot" ||
              tool_name == "delete_snapshot" ||
              tool_name == "delete_snapshots" ||
              tool_name == "delete_cloud_snapshots" ||
              tool_name == "drop_custom_tables"
            ) {
              location.reload();
            }
            if (typeof success_callback == "function") {
              success_callback(data.data, extra_data);
            }
          });
          if (tool_name == "delete_snapshot") {
            $("#wpr-ss-" + extra_data).remove();
          }
        } else if (!data.success && !data.data) {
          wpr_swal.fire({
            type: "error",
            title: wp_reset.undocumented_error,
          });
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
  } // run_tool

  // display a message while an action is performed
  function block_ui(message) {
    tmp = wpr_swal.fire({
      text: message,
      type: false,
      imageUrl: wp_reset.icon_url,
      onOpen: () => {
        $(wpr_swal.getImage()).addClass("wpr_rotating");
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
  function confirm_action(title, question, btn_confirm, btn_cancel, type = 'question') {
    tmp = wpr_swal.fire({
      title: title,
      type: type,
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


  $(".tools_page_wp-reset").on("click", ".enable-debug-mode", function(e){
    e.preventDefault();
    var confirm_title = $(this).data("confirm-title") || wp_reset.confirm_title;
      confirm_action(
        confirm_title,
        $(this).data("text-confirm"),
        $(this).data("btn-confirm"),
        wp_reset.cancel_button,
        'warning'
      ).then((result) => {
        if (!result.value) {
          return false;
        }
  
        location.href=$(this).attr('href');
      });
  });


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

  // init cards; collapse those that need collapsing
  cards = localStorage.getItem("wp-reset-cards");
  if (cards != null) {
    cards = JSON.parse(cards);
  }
  $.each(cards, function (card_name, card_value) {
    if (card_value == "collapsed") {
      $("a.toggle-card", "#" + card_name).trigger("click", true);
    }
  });

  // dismiss notice / pointer
  $(".wpr-dismiss-notice").on("click", function (e) {
    notice_name = $(this).data("notice");
    if (!notice_name) {
      return true;
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

  // init Help Scout beacon
  if (wp_reset.is_plugin_page && wp_reset.whitelabel != "1" && wp_reset.rebranding === "0") {
    Beacon("config", {
      enableFabAnimation: false,
      display: {},
      contactForm: {},
      labels: {},
    });
    Beacon("prefill", {
      name: "\n\n\n" + wp_reset.support_name,
      subject: "WP Reset PRO in-plugin support",
      email: "",
      text: "\n\n\n" + wp_reset.support_text,
    });
    Beacon("init", "bf765c98-c988-4d72-b866-1caa073cf0b0");
  }

  $(document).ready(function () {
    $("[data-tooltip]").tooltipster({
      theme: ["tooltipster-punk", "tooltipster-wpr"],
      delay: 0,
    });
  });

  // open HS docs and show article based on tool name
  $(".documentation-link").on("click", function (e) {
    e.preventDefault();

    search = $(this).data("tool-title");
    Beacon("search", search);
    Beacon("open");

    return false;
  });

  $("body").on("thickbox:iframe:loaded", function () {
    if ($("#TB_iframeContent").length > 0) {
      $TB_iframeContent = $("#TB_iframeContent").contents();
      jQuery($TB_iframeContent).on(
        "click",
        "#plugin_install_from_iframe",
        function () {
          location.href = jQuery(this).attr("href");
        }
      );
    }
  });
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

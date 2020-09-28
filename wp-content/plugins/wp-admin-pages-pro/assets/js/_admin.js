var install_user_select;

(function($) {

$(document).ready(function() {

  if(typeof($.fn.select2) === "undefined"){
    $.fn.select2 = $.fn.wuSelect2;
  }

  //users target
  install_user_select = function install_user_select() {

    $('#wu-ajax-users').select2({
      initSelection: function (element, callback) {
        // the input tag has a value attribute  preloaded that points to a preselected repository's id
        // this function resolves that id attribute to an object that select2 can render 
        // using its formatResult renderer - that way the repository name is shown preselected

        $.ajax({
          method: "GET",
          url: ajaxurl + "?action=callback_target_users&term_users=" + $('#wu-ajax-users').val(),
          dataType: 'json',
        }).done(function (data) {

          callback(
            $.map(data, function (item) {

              return {
                text: item.data.user_nicename + ' (' + item.data.user_email + ')',
                slug: item.data.user_nicename,
                id: item.data.ID
              };
            })
          );
        });

      },
      minimumInputLength: 2,
      tags: [],
      ajax: {
        url: ajaxurl,
        dataType: 'json',
        type: "GET",
        quietMillis: 50,
        data: function (term_users) {
          return {
            term_users: term_users,
            action: 'callback_target_users',
          };
        },
        results: function (data) {

          return {
            results: $.map(data, function (item) {

              return {
                text: item.data.user_nicename + ' (' + item.data.user_email + ')',
                slug: item.data.user_nicename,
                id: item.data.ID
              };
            })
          };
        }
      }
    });

  }; // end install_user_select
  

  //sites excludes
  $('#wu-ajax-excludes').select2({
    initSelection: function(element, callback) {
      // the input tag has a value attribute  preloaded that points to a preselected repository's id
      // this function resolves that id attribute to an object that select2 can render
      // using its formatResult renderer - that way the repository name is shown preselected

        $.ajax({
         method: "GET",
         url:ajaxurl + "?action=wu_query_sites&term_sites=" + $('#wu-ajax-excludes').val(),
         dataType: 'json',
        }).done(function(data) {

          callback(
            $.map(data, function(item) {
              return {
                text: item.blogname + " (" + item.domain + item.path + ")",
                slug: item.domain,
                id: item.blog_id
              };
            })
          );
        });
  
    },
    minimumInputLength: 2,
    tags: [],
    ajax: {
        url: ajaxurl,
        dataType: 'json',
        type: "GET",
        quietMillis: 50,
        data: function (term_sites) {
            return {
              term_sites: term_sites,
              action: 'wu_query_sites',
            };
        },
        results: function (data) {
            return {
                results: $.map(data, function (item) {

                    //if not is multisite
                   if(item.blog_id !== "1"){
                      return {
                        text: item.blogname + " (" + item.domain + item.path + ")",
                        slug: item.domain,
                        id: item.blog_id
                       };
                    }else{console.log("Something error: ajax search select2");}
                })
            };
        }
    }
});
});
})(jQuery);

(function($) {
  $(document).ready(function() {

    /**
     * Example 1 
     * Load from SELECT field   
     */
    wu_admin_page_submit_box = new Vue({
      el: "#postbox-container-1",
      data: {
        menu_type: wu_apc_settings.menu_type,
        page_to_replace: wu_apc_settings.page_to_replace ? wu_apc_settings.page_to_replace : 'index.php',
        replace_mode: wu_apc_settings.replace_mode ? wu_apc_settings.replace_mode : 'all',
        content_type: wu_apc_settings.content_type ? wu_apc_settings.content_type : 'normal',
        external_link_url_embedable: true,
        widget_priority: wu_apc_settings.widget_priority ? wu_apc_settings.widget_priority : 'high',
        widget_position: wu_apc_settings.widget_position ? wu_apc_settings.widget_position : 'normal',
        widget_welcome: wu_apc_settings.widget_welcome,
        widget_welcome_dismissible: wu_apc_settings.widget_welcome_dismissible,
        apply_multiple_pages: wu_apc_settings.apply_multiple_pages,
      },
      mounted: function() {
        $("#menu_icon_selector").fontIconPicker();
        this.check_apply_multiple_pages(this.apply_multiple_pages);
      },
      updated: function () {

      },
      watch: {
        menu_type: function(value) {
          wu_editor_type.menu_type = value;
          wu_admin_page_advanced_options.menu_type = value;
          this.check_iframe_conditional(value);

          if(this.content_type == 'hide_page' && this.menu_type == 'replace' || this.content_type == 'hide_page' && this.menu_type == 'replace_submenu'){
            this.remove_select_multiple();
            this.apply_multiple_pages = true;
          } else {
            this.apply_multiple_pages = false;
          }

          this.check_apply_multiple_pages(this.apply_multiple_pages);

        },
        content_type: function(value) {

          if(value === 'hide_page') {

            this.menu_type = 'replace';

            this.apply_multiple_pages = true; 

          }

        },
        apply_multiple_pages: function(value) {

          this.check_apply_multiple_pages(value);

        },
      },
      methods: {
        check_apply_multiple_pages: function(value) {

          if (value) {

            setTimeout(function(page_to_replace){

              $('#page_to_replace').attr("multiple","multiple");
              $('#page_to_replace').attr("name","pages_to_replace[]");
              $('#page_to_replace').select2();
              $("#page_to_replace").select2('val', page_to_replace);

            }, 400, this.page_to_replace);

          } else {
            this.remove_select_multiple();
          }

        },
        remove_select_multiple: function() {

          $('#page_to_replace').select2('destroy');
          $('#page_to_replace').removeAttr('multiple');
          $('#page_to_replace').attr("name","page_to_replace");

        },

        check_iframe_conditional: function(value) {
          if(value == 'replace' || value == 'replace_submenu' || value == 'widget'){
            $("#external_link_iframe").prop("checked", true);
          }

        },
      }
    });

    wu_admin_page_permissions = new Vue({
      el: "#wu_permissions",
      mounted: function() {
        install_user_select();
      },
      data: {
        limit_access: wu_apc_settings.limit_access
      },
    });

    wu_admin_page_advanced_options = new Vue({
      el: "#postbox-container-2 .inside #wu_separator",
      data: {
        menu_type: wu_apc_settings.menu_type,
        content_type: wu_apc_settings.content_type,
        conditionaly_input: !(wu_apc_settings.menu_type == 'widget' || wu_apc_settings.menu_type == 'submenu'),
        separator_before: wu_apc_settings.separator_before,
        separator_after: wu_apc_settings.separator_after,
      },
      watch: {
        menu_type: function(value) {
          if (value == 'widget' || value == 'submenu') {
            this.conditionaly_input = false;
            this.separator_before   = false;
            this.separator_after    = false;
          } else {
            this.conditionaly_input = true;
            this.separator_before   = wu_apc_settings.separator_before;
            this.separator_after    = wu_apc_settings.separator_after;
          }
          //console.log(value);   v-if="menu_type != 'widget' && menu_type != 'submenu'"
        },
      },
    });

    wu_editor_type = new Vue({
      el: "#wu-apc-editor-app",
      delimiters: ["<%", "%>"],
      data: {
        menu_type: wu_apc_settings.menu_type,
        content_type: wu_apc_settings.content_type ? wu_apc_settings.content_type : 'normal',
        editorInitialized: false,
        cssEditorInitialized: false,
        jsEditorInitialized: false,
        template_id: typeof window['wu_apc_' + wu_apc_settings.content_type + '_options'] !== 'undefined' ? window['wu_apc_' + wu_apc_settings.content_type + '_options'].template_id : '',
        external_link_url: wu_apc_settings.external_link_url,
        external_link_url_embedable: true,
        external_link_url_checking: true,
      },
      watch: {
        content_type: function(value) {
          if (value == "html") {
            this.setHTML();
          }
          wu_admin_page_submit_box.content_type = value;
        },
        external_link_url_embedable: function(value) {
          wu_admin_page_submit_box.external_link_url_embedable = value;
        },
        external_link_url: function(value) {
          this.check_embedable(value);
        },

      },
      mounted: function() {

        $(".wu-tooltip").tipTip();
        
        var that = this;

        if (this.content_type == "html") {
          this.setHTML();
        }

        if (this.content_type == "external_link") {
          this.check_embedable(this.external_link_url);
        }

        $(".wu-code-css").on("click", function() {
          if (that.cssEditorInitialized === false) {
            wp.codeEditor.initialize("css-content", wu_apc_editor_css_editor_settings);
            that.cssEditorInitialized = true;
          }
        });

        $(".wu-code-js").on("click", function() {
          if (that.jsEditorInitialized === false) {
            wp.codeEditor.initialize("js-content", wu_apc_editor_js_editor_settings);
            that.jsEditorInitialized = true;
          }
        });

        $(".wu-select-all").click(function(e) {
          e.preventDefault();
          var toggle = $(this).data("is-selected");
          $("#" + $(this).data("select-all"))
            .find(":input")
            .prop("checked", !toggle);
          $(this).data("is-selected", !toggle);
        });

      },
      methods: {
        check_embedable: function(value) {
          var that = this;
          that.external_link_url_checking = true;
          var url = $.get({
            type: 'GET',
            url: ajaxurl,
            data: {
              action: 'wu_is_embedable',
              url: value,
              _wpnonce: $('#_iframe_wpnonce').val(),
            }
          });
          url.done(function (data) {
            that.external_link_url_embedable = data;
            that.external_link_url_checking = false;
          });
        },
        setHTML: function() {

          if (this.editorInitialized) {
            return;
          }

          setTimeout(function() {
            wp.codeEditor.initialize("html-content", wu_apc_editor_html_editor_settings);
          }, 0);

          this.editorInitialized = true;
        },
        checkType: function(content_type) {
          return this.content_type == type;
        }
      }
    });

  });

})(jQuery);

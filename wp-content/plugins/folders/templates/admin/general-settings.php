<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
    <!-- do not change here, Free/Pro URL Change -->
    <link rel='stylesheet' href='<?php echo WCP_FOLDER_URL ?>assets/css/settings.css' type='text/css' media='all' />
    <link rel='stylesheet' href='<?php echo WCP_FOLDER_URL ?>assets/css/folder-icon.css' type='text/css' media='all' />
    <link rel='stylesheet' href='<?php echo WCP_FOLDER_URL ?>assets/css/spectrum.min.css' type='text/css' media='all' />
    <script src="<?php echo WCP_FOLDER_URL ?>assets/js/spectrum.min.js"></script>
    <style>
        <?php if ( function_exists( 'is_rtl' ) && is_rtl() ) { ?>
        #setting-form {
            float: right;
        }
        <?php } ?>
    </style>
    <script>
        (function (factory) {
            "use strict";
            if (typeof define === 'function' && define.amd) {
                define(['jquery'], factory);
            }
            else if(typeof module !== 'undefined' && module.exports) {
                module.exports = factory(require('jquery'));
            }
            else {
                factory(jQuery);
            }
        }(function ($, undefined) {
            var selectedItem;
            var importTitle = "<?php esc_html_e("Import folders from %plugin%", "folders"); ?>";
            var importDesc = "<?php esc_html_e("Are you sure you'd like to import %d folders from %plugin%?", "folders"); ?>";
            var removeTitle = "<?php esc_html_e("Are you sure?", "folders"); ?>";
            var removeDesc = "<?php esc_html_e("You're about to delete %plugin%'s folders. Are you sure you'd like to proceed?", "folders"); ?>";
            $(document).ready(function(){
                $(document).on("click",".form-cancel-btn, .close-popup-button, .folder-popup-form",function(){
                    $(".folder-popup-form").hide();
                });
                $(document).on("click",".popup-form-content", function(e){
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                });
                $(document).on("click",".folder-select",function(){
                    if($(this).is(":checked")) {
                        $(this).closest("tr").find(".hide-show-option").removeClass("hide-option");
                    } else {
                        $(this).closest("tr").find(".hide-show-option").addClass("hide-option");
                    }
                });
                $(document).on("click", ".accordion-header", function(){
                    if($(this).hasClass("active")) {
                        $(this).closest(".accordion").find(".accordion-content").slideUp();
                        $(this).removeClass("active");
                    } else {
                        $(this).closest(".accordion").find(".accordion-content").slideDown();
                        $(this).addClass("active");
                    }
                });
                $(".accordion-header:first").trigger("click");
                $("#folder_font, #folder_size").change(function(){
                    setCSSProperties();
                });
                setCSSProperties();
                $('.color-field').spectrum({
                    chooseText: "Submit",
                    preferredFormat: "hex",
                    showInput: true,
                    cancelText: "Cancel",
                    move: function (color) {
                        $(this).val(color.toHexString());
                        setCSSProperties();
                    },
                    change: function (color) {
                        $(this).val(color.toHexString());
                        setCSSProperties();
                    }
                });
                $(document).on("click", ".import-folder-data", function(e){
                    selectedItem = $(this).closest("tr").data("plugin");
                    if(!$(this).hasClass("in-popup")) {
                        var pluginName = $(this).closest("tr").find(".plugin-name").html();
                        var pluginFolders = parseInt($(this).closest("tr").data("folders"));
                        var popupTitle = importTitle.replace("%plugin%", pluginName);
                        $(".import-folder-title").html(popupTitle);
                        var popupDesc = importDesc.replace("%plugin%", "<b>" + pluginName + "</b>");
                        popupDesc = popupDesc.replace("%d", "<b>" + pluginFolders + "</b>");
                        $(".import-folder-note").html(popupDesc);
                        $("#import-plugin-data").show();
                    } else {
                        importPluginData();
                    }
                });
                $(document).on("click", "#import-folder-button", function(e){
                    importPluginData();
                });
                $(document).on("click", ".remove-folder-data", function(e){
                    selectedItem = $(this).closest("tr").data("plugin");
                    var pluginName = $(this).closest("tr").find(".plugin-name").html();
                    var pluginFolders = parseInt($(this).closest("tr").data("folders"));
                    var popupTitle = removeTitle.replace("%plugin%", pluginName);
                    $(".remove-folder-title").html(popupTitle);
                    var popupDesc = removeDesc.replace("%plugin%", "<b>" + pluginName + "</b>");
                    popupDesc = popupDesc.replace("%d", "<b>" + pluginFolders + "</b>");
                    $(".remove-folder-note").html(popupDesc);
                    $("#remove-plugin-data").show();
                });
                $(document).on("click", "#remove-folder-button", function(){
                    removePluginData();
                });
            });

            function importPluginData() {
                $("#import-folder-button").addClass("button");
                $("#import-folder-button").prop("disabled", true);
                $(".other-plugins-"+selectedItem+" .import-folder-data").prop("disabled", true);
                $(".other-plugins-"+selectedItem+" .import-folder-data .spinner").addClass("active");
                $.ajax({
                    url: "<?php echo admin_url("admin-ajax.php") ?>",
                    data: {
                        'plugin': $(".other-plugins-"+selectedItem).data("plugin"),
                        'nonce': $(".other-plugins-"+selectedItem).data("nonce"),
                        'action': 'wcp_import_plugin_folders_data'
                    },
                    type: 'post',
                    success: function(res){
                        var response = $.parseJSON(res);
                        if(response.status == -1) {
                            $(".other-plugins-"+selectedItem+" .import-folder-data").prop("disabled", false);
                            $(".other-plugins-"+selectedItem+" .import-folder-data .spinner").removeClass("active");
                            $("#import-third-party-plugin-data").hide();
                            $("#no-more-folder-credit").show();
                            $("#import-folder-button").removeClass("button");
                            $("#import-folder-button").prop("disabled", false);
                        } else if(response.status) {
                            $(".other-plugins-"+response.data.plugin+" .import-message").html(response.message).addClass("success-import");
                            $(".other-plugins-"+response.data.plugin+" .import-folder-data").remove();
                        } else {
                            $(".other-plugins-"+response.data.plugin+" .import-message").html(response.message).addClass("error-import");
                            $(".other-plugins-"+response.data.plugin+" .import-folder-data").remove();
                        }
                        $("#import-folder-button").prop("disabled", false);
                        $("#import-plugin-data").hide();
                    }
                });
            }

            function removePluginData() {
                $(".other-plugins-"+selectedItem+" .remove-folder-data .spinner").addClass("active");
                $.ajax({
                    url: "<?php echo admin_url("admin-ajax.php") ?>",
                    data: {
                        'plugin': $(".other-plugins-"+selectedItem).data("plugin"),
                        'nonce': $(".other-plugins-"+selectedItem).data("nonce"),
                        'action': 'wcp_remove_plugin_folders_data'
                    },
                    type: 'post',
                    success: function(res){
                        var response = $.parseJSON(res);
                        $("#remove-plugin-data").hide();
                        if(response.status) {
                            $(".other-plugins-"+response.data.plugin).remove();
                        } else {
                            $(".other-plugins-"+response.data.plugin+" .import-message").html(response.message).addClass("error-import");
                            $(".other-plugins-"+response.data.plugin+" .remove-folder-data .spinner").removeClass("active");
                        }
                    }
                });
            }

            function setCSSProperties() {
                if($("#new_folder_color").val() != "") {
                    $("#add-new-folder").css("border-color", $("#new_folder_color").val());
                    $("#add-new-folder").css("background-color", $("#new_folder_color").val());
                }
                if($("#bulk_organize_button_color").val() != "") {
                    $(".organize-button").css("border-color", $("#bulk_organize_button_color").val());
                    $(".organize-button").css("background-color", $("#bulk_organize_button_color").val());
                    $(".organize-button").css("color", "#ffffff");
                }
                if($("#dropdown_color").val() != "") {
                    $(".media-select").css("border-color", $("#dropdown_color").val());
                    $(".media-select").css("color", $("#dropdown_color").val());
                }
                if($("#folder_bg_color").val() != "") {
                    $(".all-posts.active-item").css("border-color", $("#folder_bg_color").val());
                    $(".all-posts.active-item").css("background-color", $("#folder_bg_color").val());
                    $(".all-posts.active-item").css("color", "#ffffff");
                }
                $("#custom-css").html("");
                if($("#folder_font").val() != "") {
                    font_val = $("#folder_font").val();
                    $('head').append('<link href="https://fonts.googleapis.com/css?family=' + font_val + ':400,600,700" rel="stylesheet" type="text/css" class="chaty-google-font">');
                    $('.preview-box').css('font-family', font_val);
                } else {
                    $('.preview-box').css('style', "");
                }
                if($("#folder_size").val() != "") {
                    $(".folder-list li a span, .header-posts a, .un-categorised-items a").css("font-size", $("#folder_size").val()+"px");
                } else {
                    $(".folder-list li a span, .header-posts a, .un-categorised-items a").css("font-size", "14px");
                }
            }
        }));
    </script>
    <div id="custom-css">

    </div>
    <div class="wrap">
        <h1><?php esc_html_e( 'Folders Settings', WCP_FOLDER ); ?></h1>
        <?php
        settings_fields('folders_settings');
        settings_fields('default_folders');
        settings_fields('customize_folders');
        $options = get_option('folders_settings');
        $default_folders = get_option('default_folders');
        $customize_folders = get_option('customize_folders');
        $default_folders = (empty($default_folders) || !is_array($default_folders))?array():$default_folders;
        do_settings_sections( __FILE__ );
        ?>
        <?php if($setting_page!="upgrade-to-pro") { ?>
        <form action="options.php" method="post" id="setting-form">
            <?php } ?>
            <div class="folders-tabs">
                <div class="folder-tab-menu">
                    <ul>
                        <li><a class="<?php echo esc_attr(($setting_page=="folder-settings")?"active":"") ?>" href="<?php echo esc_url($settingURL."&setting_page=folder-settings") ?>"><?php esc_html_e( 'Folders Settings', WCP_FOLDER ); ?></a></li>
                        <li><a class="<?php echo esc_attr(($setting_page=="customize-folders")?"active":"") ?>" href="<?php echo esc_url($settingURL."&setting_page=customize-folders") ?>"><?php esc_html_e( 'Customize Folders', WCP_FOLDER ); ?></a></li>
                        <li><a class="<?php echo esc_attr(($setting_page=="folders-import")?"active":"") ?>" href="<?php echo esc_url($settingURL."&setting_page=folders-import") ?>"><?php esc_html_e( 'Import', WCP_FOLDER ); ?></a></li>
                        <?php if($isInSettings) { ?>
                            <li><a class="<?php echo esc_attr(($setting_page=="upgrade-to-pro")?"active":"") ?>" href="<?php echo esc_url($settingURL."&setting_page=upgrade-to-pro") ?>"><?php esc_html_e( 'Upgrade to Pro', WCP_FOLDER ); ?></a></li>
                        <?php } ?>
                    </ul>
                </div>
                <div class="folder-tab-content">
                    <div class="tab-content <?php echo esc_attr(($setting_page=="folder-settings")?"active":"") ?>" id="folder-settings">
                        <div class="accordion-content no-bp">
                            <div class="accordion-left">
                                <table class="form-table">
                                    <tboby>
                                        <?php
                                        $post_types = get_post_types( array( ), 'objects' );
                                        $post_array = array("page", "post", "attachment");
                                        foreach ( $post_types as $post_type ) : ?>
                                            <?php
                                            if ( ! $post_type->show_ui) continue;
                                            $is_checked = !in_array( $post_type->name, $options )?"hide-option":"";
                                            $selected_id = (isset($default_folders[$post_type->name]))?$default_folders[$post_type->name]:"all";
                                            if(in_array($post_type->name, $post_array)){
                                                ?>
                                                <tr>
                                                    <td class="no-padding">
                                                        <label label for="folders_<?php echo esc_attr($post_type->name); ?>" class="custom-checkbox">
                                                            <input type="checkbox" class="folder-select sr-only" id="folders_<?php echo esc_attr($post_type->name); ?>" name="folders_settings[]" value="<?php echo esc_attr($post_type->name); ?>"<?php if ( in_array( $post_type->name, $options ) ) echo ' checked="checked"'; ?>/>
                                                            <span></span>
                                                        </label>
                                                    </td>
                                                    <td width="220px">
                                                        <label for="folders_<?php echo esc_attr($post_type->name); ?>" ><?php esc_html_e( 'Use Folders with: ', WCP_FOLDER )." ".esc_html_e($post_type->label); ?></label>
                                                    </td>
                                                    <td class="default-folder">
                                                        <label class="hide-show-option <?php echo esc_attr($is_checked) ?>" for="folders_for_<?php echo esc_attr($post_type->name); ?>" ><?php esc_html_e( 'Default folder: ', WCP_FOLDER ) ?></label>
                                                    </td>
                                                    <td>
                                                        <select class="hide-show-option <?php echo esc_attr($is_checked) ?>" id="folders_for_<?php echo esc_attr($post_type->name); ?>" name="default_folders[<?php echo esc_attr($post_type->name); ?>]" ?>">
                                                            <option value="">All <?php echo esc_attr($post_type->label) ?> Folder</option>
                                                            <option value="-1" <?php echo ($selected_id == -1)?"selected":"" ?>>Unassigned <?php echo esc_attr($post_type->label) ?></option>
                                                            <?php
                                                            if(isset($terms_data[$post_type->name]) && !empty($terms_data[$post_type->name])) {
                                                                foreach ($terms_data[$post_type->name] as $term) {
                                                                    $selected = ($selected_id == $term->slug)?"selected":"";
                                                                    echo "<option ".esc_attr($selected)." value='".esc_attr($term->slug)."'>".esc_attr($term->name)."</option>";
                                                                }
                                                            } ?>
                                                        </select>
                                                    </td>
                                                </tr>
                                                <?php
                                            } else { ?>
                                                <tr>
                                                    <td class="no-padding">
                                                        <label label for="folders_<?php echo esc_attr($post_type->name); ?>" class="custom-checkbox">
                                                            <input type="checkbox" class="sr-only folder-select" id="folders_<?php echo esc_attr($post_type->name); ?>" name="folders_settings[]" value="<?php echo esc_attr($post_type->name); ?>"<?php if ( in_array( $post_type->name, $options ) ) echo ' checked="checked"'; ?>/>
                                                            <span></span>
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <label for="folders_<?php echo esc_attr($post_type->name); ?>" ><?php esc_html_e( 'Use Folders with: ', WCP_FOLDER )." ".esc_html_e($post_type->label); ?></label>
                                                    </td>
                                                    <td class="default-folder">
                                                        <label class="hide-show-option <?php echo esc_attr($is_checked) ?>" for="folders_for_<?php echo esc_attr($post_type->name); ?>" ><?php esc_html_e( 'Default folder: ', WCP_FOLDER ) ?></label>
                                                    </td>
                                                    <td>
                                                        <select class="hide-show-option <?php echo esc_attr($is_checked) ?>" id="folders_for_<?php echo esc_attr($post_type->name); ?>" name="default_folders[<?php echo esc_attr($post_type->name); ?>]" ?>">
                                                            <option value="">All <?php echo esc_attr($post_type->label) ?> Folder</option>
                                                            <option value="-1" <?php echo ($selected_id == -1)?"selected":"" ?>>Unassigned <?php echo esc_attr($post_type->label) ?></option>
                                                            <?php
                                                            if(isset($terms_data[$post_type->name]) && !empty($terms_data[$post_type->name])) {
                                                                foreach ($terms_data[$post_type->name] as $term) {
                                                                    $selected = ($selected_id == $term->slug)?"selected":"";
                                                                    echo "<option ".esc_attr($selected)." value='".esc_attr($term->slug)."'>".esc_attr($term->name)."</option>";
                                                                }
                                                            } ?>
                                                        </select>
                                                    </td>
                                                </tr>
                                            <?php }
                                        endforeach; ?>
                                        <?php
                                        $show_in_page = !isset($customize_folders['show_folder_in_settings'])?"no":$customize_folders['show_folder_in_settings'];
                                        ?>
                                        <tr>
                                            <td class="no-padding">
                                                <input type="hidden" name="customize_folders[show_folder_in_settings]" value="no">
                                                <label for="show_folder_in_settings" class="custom-checkbox">
                                                    <input id="show_folder_in_settings" class="sr-only" <?php checked($show_in_page, "yes") ?> type="checkbox" name="customize_folders[show_folder_in_settings]" value="yes">
                                                    <span></span>
                                                </label>
                                            </td>
                                            <td colspan="3">
                                                <label for="show_folder_in_settings" ><?php esc_html_e( 'Place the Folders settings page nested under "Settings"', WCP_FOLDER ); ?></label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="20" class="no-padding">
                                                <?php $val = get_option("folders_show_in_menu"); ?>
                                                <input type="hidden" name="folders_show_in_menu" value="off" />
                                                <label for="folders_show_in_menu" class="custom-checkbox">
                                                    <input class="sr-only" type="checkbox" id="folders_show_in_menu" name="folders_show_in_menu" value="on" <?php echo ($val == "on")?"checked='checked'":"" ?>/>
                                                    <span></span>
                                                </label>
                                            </td>
                                            <td colspan="3">
                                                <label for="folders_show_in_menu" ><?php esc_html_e( 'Show the folders also in WordPress menu', WCP_FOLDER ); ?></label>
                                            </td>
                                        </tr>
                                        <!-- Do not make changes here, Only for Free -->
                                    </tboby>
                                </table>
                                <input type="hidden" name="folders_settings1[premio_folder_option]" value="yes" />
                            </div>
                            <div class="accordion-right">
                                <div class="premio-help">
                                    <a href="https://premio.io/help/folders/?utm_source=pluginspage" target="_blank">
                                        <div class="premio-help-btn">
                                            <img src="<?php echo esc_url(WCP_FOLDER_URL."assets/images/premio-help.png") ?>" alt="Premio Help" class="Premio Help" />
                                            <div class="need-help">Need Help</div>
                                            <div class="visit-our">Visit our</div>
                                            <div class="knowledge-base">knowledge base</div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <div class="clear"></div>
                            <?php submit_button(); ?>
                            <div class="separator"></div>
                            <table class="form-table">
                                <tfoot>
                                <tr>
                                    <td class="no-padding" width="20px">
                                        <span class="dashicons dashicons-editor-help"></span>
                                    </td>
                                    <td width="220px">
                                        <?php
                                        // $tlfs = get_option("folder_old_plugin_folder_status");
                                        $tlfs = 10000;
                                        // if($tlfs == false || $tlfs < 10) {
                                        //     $tlfs = 10;
                                        // }
                                        $total = WCP_Folders::get_ttl_fldrs();
                                        // if($total > $tlfs) {
                                        //     $tlfs = $total;
                                        // }
                                        ?>
                                        You have used <b><?php echo esc_attr($total) ?></b>/<?php echo esc_attr($tlfs) ?> Folders.
                                    </td>
                                    <td class="no-padding" colspan="2">
                                    <span class="pink">YOU ROCK!</span>
                                    </td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="tab-content <?php echo esc_attr(($setting_page=="customize-folders")?"active":"") ?>" id="customize-folders">
                        <div class="accordion-content">
                            <div class="accordion-left">
                                <table class="form-table">
                                    <?php
                                    $color = !isset($customize_folders['new_folder_color'])||empty($customize_folders['new_folder_color'])?"#FA166B":$customize_folders['new_folder_color'];
                                    ?>
                                    <tr>
                                        <td width="220px" class="no-padding">
                                            <label for="new_folder_color" ><b>"New Folder"</b> button color</label>
                                        </td>
                                        <td width="32px">
                                            <input type="text" class="color-field" name="customize_folders[new_folder_color]" id="new_folder_color" value="<?php echo esc_attr($color) ?>" />
                                        </td>
                                        <td rowspan="4" >

                                        </td>
                                    </tr>
                                    <?php
                                    $color = !isset($customize_folders['bulk_organize_button_color'])||empty($customize_folders['bulk_organize_button_color'])?"#FA166B":$customize_folders['bulk_organize_button_color'];
                                    ?>
                                    <tr>
                                        <td class="no-padding">
                                            <label for="bulk_organize_button_color" ><b>"Bulk Organize"</b> button color</label>
                                        </td>
                                        <td>
                                            <input type="text" class="color-field" name="customize_folders[bulk_organize_button_color]" id="bulk_organize_button_color" value="<?php echo esc_attr($color) ?>" />
                                        </td>
                                    </tr>
                                    <?php
                                    $color = !isset($customize_folders['media_replace_button'])||empty($customize_folders['media_replace_button'])?"#FA166B":$customize_folders['media_replace_button'];
                                    ?>
                                    <tr>
                                        <td class="no-padding">
                                            <label for="bulk_organize_button_color" ><b>"Replace File"</b> media library button</label>
                                        </td>
                                        <td>
                                            <input type="text" class="color-field" name="customize_folders[media_replace_button]" id="media_replace_button" value="<?php echo esc_attr($color) ?>" />
                                        </td>
                                    </tr>
                                    <?php
                                    $color = !isset($customize_folders['dropdown_color'])||empty($customize_folders['dropdown_color'])?"#484848":$customize_folders['dropdown_color'];
                                    ?>
                                    <tr>
                                        <td class="no-padding">
                                            <label for="dropdown_color" >Dropdown color</label>
                                        </td>
                                        <td>
                                            <input type="text" class="color-field" name="customize_folders[dropdown_color]" id="dropdown_color" value="<?php echo esc_attr($color) ?>" />
                                        </td>
                                    </tr>
                                    <?php
                                    $color = !isset($customize_folders['folder_bg_color'])||empty($customize_folders['folder_bg_color'])?"#FA166B":$customize_folders['folder_bg_color'];
                                    ?>
                                    <tr>
                                        <td class="no-padding">
                                            <label for="folder_bg_color" >Folders background color</label>
                                        </td>
                                        <td>
                                            <input type="text" class="color-field" name="customize_folders[folder_bg_color]" id="folder_bg_color" value="<?php echo esc_attr($color) ?>" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="no-padding">
                                            <label for="folder_font" >Folders font</label>
                                        </td>
                                        <td colspan="2">
                                            <?php
                                            $font = !isset($customize_folders['folder_font'])||empty($customize_folders['folder_font'])?"":$customize_folders['folder_font'];
                                            $index = 0;
                                            ?>
                                            <select name="customize_folders[folder_font]" id="folder_font" >
                                                <?php $group = '';
                                                foreach ($fonts as $key => $value):
                                                    $title = $key;
                                                    if($index == 0) {
                                                        $key = "";
                                                    }
                                                    $index++;
                                                    if ($value != $group) {
                                                        echo '<optgroup label="' . $value . '">';
                                                        $group = $value;
                                                    }
                                                    ?>
                                                    <option value="<?php echo $key; ?>" <?php selected($font, $key); ?>><?php echo $title; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="no-padding">
                                            <label for="folder_size" >Folders size</label>
                                        </td>
                                        <td colspan="2">
                                            <?php
                                            $sizes = array(
                                                "12" => "Small",
                                                "16" => "Medium",
                                                "20" => "Large"
                                            );
                                            $size = !isset($customize_folders['folder_size'])||empty($customize_folders['folder_size'])?"16":$customize_folders['folder_size'];
                                            ?>
                                            <select name="customize_folders[folder_size]" id="folder_size" >
                                                <?php
                                                foreach ($sizes as $key=>$value) {
                                                    $selected = ($key == $size)?"selected":"";
                                                    echo "<option ".$selected." value='".$key."'>".$value."</option>";
                                                }
                                                ?>
                                            </select>
                                        </td>
                                    </tr>
                                    <?php
                                    $show_in_page = !isset($customize_folders['show_in_page'])||empty($customize_folders['show_in_page'])?"show":$customize_folders['show_in_page'];
                                    if(empty($show_in_page)) {
                                        $show_in_page = "show";
                                    }
                                    ?>
                                    <tr>
                                        <td colspan="3" class="no-padding">
                                            <input type="hidden" name="customize_folders[show_in_page]" value="hide">
                                            <div class="custom-checkbox">
                                                <input id="show_folders" class="sr-only" <?php checked($show_in_page, "show") ?> type="checkbox" name="customize_folders[show_in_page]" value="show">
                                                <span></span>
                                            </div>
                                            <label for="show_folders">Show Folders in upper position</label>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="accordion-right">
                                <div class="preview-text">
                                    Preview
                                    <div class="preview-text-info">See the full functionality on your media library, posts, pages, and custom posts</div>
                                </div>
                                <div class="preview-inner-box">
                                    <div class="preview-box">
                                        <div class="wcp-custom-form">
                                            <div class="form-title">
                                                Folders
                                                <a href="javascript:;" class="add-new-folder" id="add-new-folder">
                                                    <span class="create_new_folder"><i class="pfolder-add-folder"></i></span>
                                                    <span>New Folder</span>
                                                </a>
                                                <div class="clear"></div>
                                            </div>
                                            <div class="form-options">
                                                <ul>
                                                    <li>
                                                        <div class="custom-checkbox">
                                                            <input type="checkbox" class="sr-only" >
                                                            <span></span>
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:;" id="inline-update"><span class="icon pfolder-edit-folder"><span class="path2"></span></span> <span class="text">Rename</span> </a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:;" id="inline-remove"><span class="icon pfolder-remove"></span> <span class="text">Delete</span> </a>
                                                    </li>
                                                    <li class="last">
                                                        <a href="javascript:;" id="expand-collapse-list" data-tooltip="Expand"><span class="icon pfolder-arrow-down"></span></a>
                                                    </li>
                                                    <li class="last">
                                                        <a href="javascript:;" ><span class="icon pfolder-arrow-sort"></span></a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="shadow-box">
                                            <div class="header-posts">
                                                <a href="javascript:;" class="all-posts active-item">All Files <span class="total-count">215</span></a>
                                            </div>
                                            <div class="un-categorised-items  ui-droppable">
                                                <a href="javascript:;" class="un-categorized-posts">Unassigned Files <span class="total-count total-empty">191</span> </a>
                                            </div>
                                            <div class="separator"></div>
                                            <ul class="folder-list">
                                                <li><a href="javascript:;"><i class="wcp-icon pfolder-folder-close"></i> <span>Folder 1</span><span class="total-count">20</span><span class="clear"></span></a></li>
                                                <li><a href="javascript:;"><i class="wcp-icon pfolder-folder-close"></i> <span>Folder 2</span><span class="total-count">13</span><span class="clear"></span></a></li>
                                                <li><a href="javascript:;"><i class="wcp-icon pfolder-folder-close"></i> <span>Folder 3</span><span class="total-count">5</span><span class="clear"></span></a></li>
                                            </ul>
                                            <div class="separator"></div>
                                            <div class="media-buttons">
                                                <select class="media-select">
                                                    <option>All Files</option>
                                                    <option>Folder 1</option>
                                                    <option>Folder 2</option>
                                                    <option>Folder 3</option>
                                                </select>
                                                <button type="button" class="button organize-button">Bulk Organize</button>
                                                <div style="clear: both;"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="clear"></div>
                            <?php submit_button(); ?>
                        </div>
                    </div>
                    <div class="tab-content <?php echo esc_attr(($setting_page=="folders-import")?"active":"") ?>" id="folder-import">
                        <?php if($is_plugin_exists) { ?>
                            <div class="import-folder-table">
                                <table>
                                    <tbody>
                                    <?php foreach ($plugin_info as $slug=>$plugin) {
                                        if($plugin['is_exists']) { ?>
                                            <tr class="other-plugins-<?php echo esc_attr__($slug) ?>" data-plugin="<?php echo esc_attr__($slug) ?>" data-nonce="<?php echo wp_create_nonce("import_data_from_".$slug) ?>" data-folders="<?php echo esc_attr($plugin['total_folders']) ?>" data-attachments="<?php echo esc_attr($plugin['total_attachments']) ?>">
                                                <th class="plugin-name"><?php echo esc_attr__($plugin['name']) ?></th>
                                                <td>
                                                    <span class="import-message"><?php printf(esc_html__("%s folder%s and %s attachment%s", "folders"), "<b>".$plugin['total_folders']."</b>", ($plugin['total_folders']>1)?esc_html__("s"):"" ,"<b>".$plugin['total_attachments']."</b>", ($plugin['total_attachments']>1)?esc_html__("s"):"") ?></span>
                                                    <button type="button" class="button button-primary import-folder-data"><?php esc_html_e("Import", "folders"); ?> <span class="spinner"></span></button>
                                                    <button type="button" class="button button-secondary remove-folder-data"><?php esc_html_e("Delete plugin data", "folders"); ?> <span class="spinner"></span></button>
                                                </td>
                                            </tr>
                                        <?php }
                                    } ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php } else { ?>
                            <div class="no-plugin-message">
                                <p><?php esc_html_e("We couldn't detect any external folders that can be imported.", WCP_FOLDER); ?></p>
                                <p><?php echo sprintf(esc_html__("If you have external folders that were not detected, please contact us at %s", WCP_FOLDER), "<a href='mailto:contact@premio.io'>contact@premio.io</a>"); ?></p>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="tab-content <?php echo esc_attr(($setting_page=="upgrade-to-pro")?"active":"") ?>">
                        <?php if($setting_page=="upgrade-to-pro") { ?>
                            <?php include_once "upgrade-table.php" ?>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <?php
            ?>
            <input type="hidden" name="folder_nonce" value="<?php echo wp_create_nonce("folder_settings") ?>">
            <input type="hidden" name="folder_page" value="<?php echo $_SERVER['REQUEST_URI'] ?>">
            <?php if($setting_page!="upgrade-to-pro") { ?>
        </form>
    <?php } ?>
    </div>

    <div class="folder-popup-form" id="import-plugin-data">
        <div class="popup-form-content">
            <div class="popup-content">
                <div class="close-popup-button">
                    <a class="" href="javascript:;"><span></span></a>
                </div>
                <div class="import-folder-title"></div>
                <div class="import-folder-note">Are you sure you'd like to import $x folders from $plugin?</div>
                <div class="folder-form-buttons">
                    <button type="submit" class="form-submit-btn" id="import-folder-button"><?php esc_html_e("Import", WCP_FOLDER); ?></button>
                    <a href="javascript:;" class="form-cancel-btn"><?php esc_html_e("Cancel", WCP_FOLDER); ?></a>
                </div>
            </div>
        </div>
    </div>

    <div class="folder-popup-form" id="remove-plugin-data">
        <div class="popup-form-content">
            <div class="popup-content">
                <div class="close-popup-button">
                    <a class="" href="javascript:;"><span></span></a>
                </div>
                <div class="remove-folder-title">Are you sure?</div>
                <div class="remove-folder-note">You're about to delete $plugin's folders. Are you sure you'd like to proceed?</div>
                <div class="folder-form-buttons">
                    <button type="submit" class="form-submit-btn delete-folder-plugin" id="remove-folder-button"><?php esc_html_e("Delete plugin data", WCP_FOLDER); ?></button>
                    <a href="javascript:;" class="form-cancel-btn"><?php esc_html_e("Cancel", WCP_FOLDER); ?></a>
                </div>
            </div>
        </div>
    </div>

<?php
$option = get_option("folder_intro_box");
if(($option == "show" || get_option("folder_redirect_status") == 2) && $is_plugin_exists) { ?>
    <div class="folder-popup-form" id="import-third-party-plugin-data" style="display: block" ?>
        <div class="popup-form-content">
            <div class="popup-content">
                <div class="close-popup-button">
                    <a class="" href="javascript:;"><span></span></a>
                </div>
                <div class="import-plugin-title"><?php esc_html_e("Import data", WCP_FOLDER); ?></div>
                <div class="import-plugin-note"><?php esc_html_e("We've detected that you use another folders plugin. Would you like the Folders plugin to import your current folders? Keep in mind you can always do it in Folders Settings -> Import", WCP_FOLDER) ?></div>
                <div class="plugin-import-table">
                    <div class="import-folder-table">
                        <table>
                            <tbody>
                            <?php foreach ($plugin_info as $slug=>$plugin) {
                                if($plugin['is_exists']) { ?>
                                    <tr class="other-plugins-<?php echo esc_attr__($slug) ?>" data-plugin="<?php echo esc_attr__($slug) ?>" data-nonce="<?php echo wp_create_nonce("import_data_from_".$slug) ?>" data-folders="<?php echo esc_attr($plugin['total_folders']) ?>" data-attachments="<?php echo esc_attr($plugin['total_attachments']) ?>">
                                        <th class="plugin-name"><?php echo esc_attr__($plugin['name']) ?></th>
                                        <td>
                                            <button type="button" class="button button-primary import-folder-data in-popup"><?php esc_html_e("Import", "folders"); ?> <span class="spinner"></span></button>
                                            <span class="import-message"><?php printf(esc_html__("%s folder%s and %s attachment%s", "folders"), "<b>".$plugin['total_folders']."</b>", ($plugin['total_folders']>1)?esc_html__("s"):"" ,"<b>".$plugin['total_attachments']."</b>", ($plugin['total_attachments']>1)?esc_html__("s"):"") ?></span>
                                        </td>
                                    </tr>
                                <?php }
                            } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="folder-form-buttons">
                    <div class=""></div>
                    <a href="javascript:;" id="cancel-plugin-import" class="form-cancel-btn"><?php esc_html_e("Close", WCP_FOLDER); ?></a>
                </div>
            </div>
        </div>
    </div>
    <?php
    if($option != "show") {
        update_option("folder_redirect_status", 3);
    }
} ?>
<div class="folder-popup-form" id="no-more-folder-credit">
    <div class="popup-form-content">
        <div class="popup-content">
            <div class="close-popup-button">
                <a class="" href="javascript:;"><span></span></a>
            </div>
            <div class="add-update-folder-title" id="folder-limitation-message">
                You've reached the 10 folder limitation!
            </div>
            <div class="folder-form-message">
                Unlock unlimited amount of folders by upgrading to one of our pro plans.
            </div>
            <div class="folder-form-buttons">
                <a href="javascript:;" class="form-cancel-btn">Cancel</a>
                <a href="<?php echo esc_url($this->getFoldersUpgradeURL()) ?>" target="_blank" class="form-submit-btn">See Pro Plans</a>
            </div>
        </div>
    </div>
</div>

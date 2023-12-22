<?php

namespace NinjaTablesPro\App\Hooks\Handlers;

use NinjaTables\Framework\Support\Arr;

class CustomJsHandler
{
    public $ninja_table_after_print = [];

    public function dragAndDropTableCustomJS($tableId)
    {
        $ninja_table_builder_setting = get_post_meta($tableId, '_ninja_table_builder_table_settings', true);
        $customJS                    = Arr::get($ninja_table_builder_setting, 'custom_js.value', '');
        $styleId                     = "ninja_table_builder_custom_js_$tableId";

        if ($customJS !== '') {
            add_action('wp_footer', function () use ($customJS, $styleId) {
                ?>
                <script id="<?php echo $styleId ?>" type="text/javascript">
                    <?php echo $customJS; ?>
                </script>
                <?php
            }, 999);
        }
    }

    public function ninjaTablesAfterTablePrint($table)
    {
        if (in_array($table->ID, $this->ninja_table_after_print)) {
            return;
        } else {
            $this->ninja_table_after_print[] = $table->ID;
        }

        $customJS = get_post_meta($table->ID, '_ninja_tables_custom_js', true);

        if ($customJS) {
            add_action('wp_footer', function () use ($customJS, $table) {
                ?>
                <script type="text/javascript">
                    jQuery(document).on('ninja_table_ready_init_table_id_<?php echo $table->ID; ?>', function (e, params) {
                        var $table = params.$table;
                        var $ = jQuery;
                        var tableConfig = params.tableConfig;

                        jQuery('.nt_force_download').on('click', function (e) {
                            e.preventDefault();
                            console.log('hello');
                            const url = $(this).attr('href');
                            const requestURL = window.ninja_footables.ajax_url + '?action=ninja_table_force_download&url=' + url + '&ninja_table_public_nonce=' + window.ninja_footables.ninja_table_public_nonce;
                            window.location.href = requestURL;
                        });

                        if (window.ninjaTableAfterPrint) {
                            if (window.ninjaTableAfterPrint.indexOf(tableConfig.table_id) != -1) {
                                return;
                            } else {
                                window.ninjaTableAfterPrint.push(tableConfig.table_id);
                            }
                        } else {
                            window.ninjaTableAfterPrint = [tableConfig.table_id];
                        }

                        try {
                            <?php echo $customJS; ?>
                        } catch (e) {
                            console.warn('Error in custom JS of Ninja Table ID: ' + tableConfig.table_id);
                            console.error(e);
                        }
                    });
                </script>
                <?php
            }, 999);
        }
    }

    public function forceDownloadScript()
    {
        wp_add_inline_script('ninja-tables-pro', "jQuery(document).on('click', '.nt_force_download', function (e) {
    e.preventDefault();
    const url = jQuery(this).attr('href');
    const requestURL = window.ninja_footables.ajax_url + '?action=ninja_table_force_download&url=' + url + '&ninja_table_public_nonce=' + window.ninja_footables.ninja_table_public_nonce;
    window.location.href = requestURL;
});", 'after');
    }
}

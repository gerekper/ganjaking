<div id="footable_parent_<?php echo $table_id; ?>"
     class="footable_parent ninja_table_wrapper loading_ninja_table wp_table_data_press_parent <?php echo $settings['css_lib']; ?> <?php echo $tableHasColor; ?>">
    <?php if (isset($settings['show_title']) && $settings['show_title']) : ?>
        <?php do_action('ninja_tables_before_table_title', $table); ?>
        <h3 class="table_title footable_title"><?php echo esc_attr($table->post_title); ?></h3>
        <?php do_action('ninja_tables_after_table_title', $table); ?>
    <?php endif; ?>
    <?php if (isset($settings['show_description']) && $settings['show_description']) : ?>
        <?php do_action('ninja_tables_before_table_description', $table); ?>
        <div
            class="table_description footable_description"><?php echo do_shortcode(wp_kses_post($table->post_content)); ?></div>
        <?php do_action('ninja_tables_after_table_description',
            $table); ?>
    <?php endif; ?>
    <?php do_action('ninja_tables_before_table_print', $table, $table_vars); ?>
    <table data-ninja_table_instance="<?php echo $table_instance_name; ?>" <?php echo $foo_table_attributes; ?>
           id="footable_<?php echo intval($table_id); ?>"
           data-unique_identifier="<?php echo $tableArray['uniqueID']; ?>"
           class=" foo-table ninja_footable foo_table_<?php echo intval($table_id); ?> <?php echo $tableArray['uniqueID']; ?> <?php echo esc_attr($table_classes); ?>">
        <?php if ($tableCaption): ?>
            <caption><?php echo $tableCaption; ?></caption>
        <?php endif; ?>
        <colgroup>
            <?php foreach ($formatted_columns as $index => $column) : ?>
                <col class="ninja_column_<?php echo $index . ' ' . $column['breakpoints']; ?>">
            <?php endforeach; ?>
        </colgroup>
        <?php do_action('ninja_tables_inside_table_render', $table, $table_vars); ?>
    </table>
    <?php do_action('ninja_tables_after_table_print', $table, $table_vars); ?>

    <?php if (strpos($table_classes, 'ninja_require_initial_hide') != false): ?>
        <div class="footable-loader">
            <span class="fooicon fooicon-loader"></span>
        </div>
    <?php endif; ?>

    <?php if (is_user_logged_in() && ninja_table_admin_role()): ?>
        <a class="nt_edit_link" href="<?php echo admin_url('admin.php?page=ninja_tables#/tables/' . $table->ID); ?>">
            <?php _e('Edit Table', 'ninja-tables') ?>
        </a>
    <?php endif; ?>

</div>

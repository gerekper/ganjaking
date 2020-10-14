<?php namespace NinjaTables\Classes;

use NinjaTable\TableDrivers\NinjaFooTable;

class ProcessDemoPage
{
    public function handleExteriorPages()
    {
        if (isset($_GET['ninjatable_preview']) && $_GET['ninjatable_preview']) {
            if (ninja_table_admin_role()) {
                $tableId = intval($_GET['ninjatable_preview']);

                do_action('ninja_tables_will_render_table', $tableId);

                wp_enqueue_style('ninja-tables-preview', plugin_dir_url(__DIR__) . "assets/css/ninja-tables-preview.css");

                $this->renderPreview($tableId);
            }
        }
    }

    public function renderPreview($table_id)
    {
        NinjaFooTable::enqueuePublicCss();

        $table = get_post($table_id);

        if ($table) {
            include NINJA_TABLES_DIR_PATH . 'public/views/frameless/show_review.php';
            exit();
        }
    }
}
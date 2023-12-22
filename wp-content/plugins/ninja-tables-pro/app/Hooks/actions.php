<?php

/**
 * All registered action's handlers should be in app\Hooks\Handlers,
 * addAction is similar to add_action and addCustomAction is just a
 * wrapper over add_action which will add a prefix to the hook name
 * using the plugin slug to make it unique in all wordpress plugins,
 * ex: $app->addCustomAction('foo', ['FooHandler', 'handleFoo']) is
 * equivalent to add_action('slug-foo', ['FooHandler', 'handleFoo']).
 */

/**
 * @var $app NinjaTablesPro\App\Application
 */

use NinjaTablesPro\App\Hooks\Handlers\TableEditorHandler;
use NinjaTablesPro\App\Hooks\Handlers\TableHandler;
use NinjaTablesPro\App\Hooks\Handlers\DataProviderHandler;
use NinjaTablesPro\App\Hooks\Handlers\ExtraShortCodeHandler;
use NinjaTablesPro\App\Hooks\Handlers\CustomFilterHandler;
use NinjaTablesPro\App\Hooks\Handlers\CustomJsHandler;

//admin hooks
$app->addAction('init', [DataProviderHandler::class, 'handle']);
$app->addAction('init', [ExtraShortCodeHandler::class, 'register']);

$app->addAction('ninja_table_rendering_table_vars', [CustomFilterHandler::class, 'ninjaTableRenderingTableVars'], 100, 2);
$app->addAction('ninja_tables_after_table_print', [CustomJsHandler::class, 'ninjaTablesAfterTablePrint']);

$app->addAction('ninja_tables_loaded_boot_script', [TableHandler::class, 'loadFormulaParser']);
$app->addAction('ninja_tables_custom_code_before_save', [TableEditorHandler::class, 'savedCustomCode']);

// public hooks
$app->addAction('ninja_tables_after_table_print', [TableEditorHandler::class, 'addEditorDom'], 10, 2);
$app->addAction('wp_ajax_ninja_table_pro_update_row', [TableEditorHandler::class, 'routeUpdateRow']);
$app->addAction('wp_ajax_nopriv_ninja_table_pro_update_row', [TableEditorHandler::class, 'routeUpdateRow']);

$app->addAction('ninja_table_update_row_data_default', [TableEditorHandler::class, 'updateRowDefaultTable'], 10, 3);

$app->addAction('wp_ajax_ninja_table_pro_get_editing_settings', [TableEditorHandler::class, 'getSettings']);
$app->addAction('wp_ajax_ninja_table_pro_update_editing_settings', [TableEditorHandler::class, 'updateSettings']);

$app->addAction('wp_ajax_ninja_table_pro_delete_row', [TableEditorHandler::class, 'routeDeleteRow']);
$app->addAction('ninja_table_delete_row_data_default', [TableEditorHandler::class, 'deleteRowDefaultTable'], 10, 2);

$app->addAction('ninja_tables_require_formulajs', [TableHandler::class, 'loadFormulaParser']);
$app->addAction('ninja_tables_load_lightbox', [TableHandler::class, 'ninjaTablesLoadLightbox']);

$app->addAction('ninja_tables_item_attributes', [TableHandler::class, 'ninjaTablesItemAttributes']);

$app->addAction('ninja_tables_drag_and_drop_after_table_print', [CustomJsHandler::class, 'dragAndDropTableCustomJS'], 10, 2);
$app->addAction('wp_ajax_ninja_table_force_download', [TableHandler::class, 'forceDownload']);
$app->addAction('wp_ajax_nopriv_ninja_table_force_download', [TableHandler::class, 'forceDownload']);
$app->addAction('ninja_tables_after_table_print', [CustomJsHandler::class, 'forceDownloadScript']);

<?php

/**
 * All registered filter's handlers should be in app\Hooks\Handlers,
 * addFilter is similar to add_filter and addCustomFlter is just a
 * wrapper over add_filter which will add a prefix to the hook name
 * using the plugin slug to make it unique in all wordpress plugins,
 * ex: $app->addCustomFilter('foo', ['FooHandler', 'handleFoo']) is
 * equivalent to add_filter('slug-foo', ['FooHandler', 'handleFoo']).
 */

/**
 * @var $app NinjaTablesPro\App\Application
 */

use NinjaTablesPro\App\Hooks\Handlers\PositionHandler;
use NinjaTablesPro\App\Hooks\Handlers\TableHandler;
use NinjaTablesPro\App\Hooks\Handlers\PlaceholderParserHandler;

//admin filters
$app->addFilter('ninja_tables_item_attributes', [PositionHandler::class, 'make']);
$app->addFilter('ninja_tables_import_table_data', [PositionHandler::class, 'maker'], 10, 2);


// public filters
$app->addFilter('ninja_table_column_attributes', [TableHandler::class, 'addOriginalColumn'], 10, 3);
$app->addFilter('ninja_table_own_data_filter_query', [TableHandler::class, 'ownDataFilter'], 10, 2);
$app->addFilter('ninja_tables_total_size_query', [TableHandler::class, 'ownDataTotalFilter'], 10, 2);

$app->addFilter('ninja_table_activated_features', function ($features) {
    $features['ninja_table_front_editor'] = true;
    return $features;
});

$app->addFilter('ninja_table_admin_role', function ($permission) {
   return get_option('_ninja_tables_permission', $permission);
});

$app->addFilter('ninja_table_js_config', [TableHandler::class, 'ninjaTableJsConfig'], 10, 2);
$app->addFilter('ninja_table_column_attributes', [TableHandler::class, 'ninjaTableColumnAttributes'], 10, 2);
$app->addFilter('ninja_tables_shortcode_defaults', [TableHandler::class, 'ninjaTablesShortcodeDefaults']);
$app->addFilter('ninja_tables_rendering_table_settings', [TableHandler::class, 'ninjaTablesRenderingTableSettings'], 10,
    2);
$app->addFilter('ninja_table_rendering_table_vars', [TableHandler::class, 'ninjaTableRenderingTableVars'], 10, 3);
$app->addFilter('ninja_parse_placeholder', [PlaceholderParserHandler::class, 'parse']);
$app->addFilter('ninja_tables_get_public_data', [TableHandler::class, 'ninjaTableGetPublicData'], 11, 1);


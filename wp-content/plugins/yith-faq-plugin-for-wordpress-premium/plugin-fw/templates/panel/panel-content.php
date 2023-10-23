<?php
/**
 * The Template for displaying the panel
 *
 * @var YIT_Plugin_Panel $panel
 * @package    YITH\PluginFramework\Templates
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

$panel->print_tabs_nav();
$panel->render_panel_content_page();

<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');


class UniteCreatorFilters{
	
	const FILTER_CLIENTSIDE_GENERAL_SETTINGS = "uc_get_client_general_settings";
	const FILTER_MODIFY_GENERAL_SETTINGS = "uc_modify_general_settings";
	const FILTER_MANAGER_MENU_SINGLE = "uc_manager_addons_menu_single";
	const FILTER_MANAGER_PAGES_MENU_SINGLE = "uc_manager_pages_menu_single";
	const FILTER_MANAGER_MENU_FIELD = "uc_manager_addons_menu_field";
	const FILTER_MANAGER_MENU_MULTIPLE = "uc_manager_addons_menu_multiple";
	const FILTER_MANAGER_MENU_CATEGORY = "uc_manager_addons_menu_category";
	const FILTER_MANAGER_ADDONS_PLUGINS = "uc_manager_addons_plugins";
	const FILTER_MANAGER_ADDON_ADDHTML = "addon_library_manager_addon_addhtml";
	const FILTER_MANAGER_LAYOUT_ADDHTML = "addon_library_manager_layout_addhtml";
	const FILTER_MANAGER_LAYOUT_LI_ADDHTML = "addon_library_manager_layout_li_addhtml";
	const FILTER_MANAGER_PAGE_ADD_ITEM_HTML = "addon_library_manager_page_add_item_html";
	const ACTION_MANAGER_PAGES_ADD_HTML = "uc_manager_pages_add_html";
	const FILTER_GET_MANAGER_OBJECT_BYDATA = "uc_get_manager_object_bydata";
	const FILTER_MANAGER_ADDONS_CATEGORY_SETTINGS = "uc_filter_manager_addons_category_settings";
	const FILTER_URL_LAYOUTS_LIST = "uc_filter_url_layouts_list";
	const FILTER_URL_TEMPLATES_LIST = "uc_filter_url_templates_list";
	const FILTER_GET_DATASET_RECORDS = "uc_filter_get_dataset_records";
	const FILTER_GET_DATASET_HEADERS = "uc_filter_get_dataset_headers";
	
	
	const FILTER_ADMIN_AJAX_ACTION = "addon_library_ajax_action";
	const FILTER_ADMIN_VIEW_FILEPATH = "addon_library_admin_view_filepath";
	const FILTER_MODIFY_URL_VIEW = "addon_library_modify_url_view";
	const FILTER_MODIFY_URL_LAYOUT_PREVIEW_FRONT = "addon_library_modify_url_layout_preview_front";
	const FILTER_LAYOUTS_ACTIONS_COL_WIDTH = "addon_library_layouts_actions_colwidth";	
	const FILTER_EXPORT_ADDON_DATA = "addon_library_export_addon_data";
	const FILTER_EXPORT_CAT_TITLE = "addon_library_export_cat_title";
	const FILTER_PARAMS_DIALOG_MAIN_PARAMS = "addon_library_params_dialog_main_param";
	const FILTER_GET_GENERAL_SETTINGS_FILEPATH = "addon_library_get_general_settings_filepath";
	const FILTER_ADD_ADDON_OUTPUT_CONSTANT_DATA = "addon_library_add_constant_data";
	const FILTER_LAYOUT_PROPERTIES_SETTINGS = "addon_library_get_layout_properties_settings";
	const FILTER_MODIFY_ADDON_OUTPUT_PARAMS = "addon_library_modify_addon_output_params";
	
	const ACTION_VALIDATE_GENERAL_SETTINGS = "uc_validate_general_settings";
	const ACTION_MANAGER_ITEM_BUTTONS1 = "uc_manager_action_item_buttons1";
	const ACTION_MANAGER_ITEM_BUTTONS2 = "uc_manager_action_item_buttons2";
	const ACTION_MANAGER_ITEM_BUTTONS3 = "uc_manager_action_item_buttons3";
	const ACTION_MANAGER_PAGES_ITEM_BUTTONS = "uc_manager_pages_action_item_buttons";
	
	const ACTION_EDIT_ADDON_EXTRA_BUTTONS = "addon_library_addon_edit_extra_buttons";
	const ACTION_EDIT_GLOBALS = "addon_library_edit_globals";
	const ACTION_BOTTOM_PLUGIN_VERSION = "addon_library_bottom_plugin_version";
	const ACTION_ADD_ADMIN_SCRIPTS = "addon_library_add_admin_scripts";
	const ACTION_ADD_LAYOUT_TOOLBAR_BUTTON = "addon_library_add_layout_toolbar_button";
	const ACTION_ADD_LAYOUTS_TOOLBAR_BUTTON = "addon_library_add_layouts_toolbar_button";
	const ACTION_ADD_ADDONS_TOOLBAR_BUTTON = "addon_library_add_addons_toolbar_button";	
	const ACTION_LAYOUT_EDIT_HTML = "addon_library_layout_edit_html";
	const ACTION_MODIFY_ADDONS_MANAGER = "addon_library_modify_addons_manager";
	const ACTION_LAYOUTS_LIST_ACTIONS = "addon_library_layouts_list_actions";
	const ACTION_EDIT_ADDON_ADDSETTINGS = "addon_library_edit_addon_addsettings";
	const ACTION_AFTER_LAYOUT_PREVIEW_OUTPUT = "addon_library_after_layout_preview_output";
	const ACTION_AFTER_IMPORT_LAYOUT_FILE = "addon_library_after_import_layout_file";
	const ACTION_AFTER_IMPORT_LAYOUT_FILE_IMAGES = "addon_library_after_import_layout_images";
	const ACTION_BEFORE_ADMIN_INIT = "addon_library_before_admin_init";
	const ACTION_RUN_AFTER_INCLUDES = "addon_library_action_run_after_includes";
	const ACTION_AFTER_INIT_GLOBALS = "addon_library_after_init_globals";
	
	
}
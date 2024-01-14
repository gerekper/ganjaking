<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

class UniteCreatorActions{


	/**
	 * on update layout response, function for override
	 */
	protected function onUpdateLayoutResponse($response){


		$isUpdate = $response["is_update"];

		//create
		if($isUpdate == false){
			HelperUC::ajaxResponseData($response);
		}else{
			//update

			$message = $response["message"];
			$pageName = UniteFunctionsUC::getVal($response, "page_name");

			$arrData = array();
			if(!empty($pageName))
				$arrData["page_name"] = $pageName;

			HelperUC::ajaxResponseSuccess($message, $arrData);
		}
	}

	/**
	 * get data array from request
	 */
	private function getDataFromRequest(){

		$data = UniteFunctionsUC::getPostGetVariable("data", "", UniteFunctionsUC::SANITIZE_NOTHING);
		if(empty($data))
			$data = $_REQUEST;

		if(is_string($data)){
			$arrData = (array)json_decode($data);

			if(empty($arrData)){
				$arrData = stripslashes(trim($data));
				$arrData = (array)json_decode($arrData);
			}

			$data = $arrData;
		}

		return ($data);
	}

	/**
	 * on ajax action
	 */
	public function onAjaxAction(){

		if(GlobalsUC::$inDev == true || GlobalsUC::$debugAjaxErrors == true){
			ini_set("display_errors", "on");
			error_reporting(E_ALL);

		}


		$actionType = UniteFunctionsUC::getPostGetVariable("action", "", UniteFunctionsUC::SANITIZE_KEY);

		if($actionType != GlobalsUC::PLUGIN_NAME . "_ajax_action")
			return (false);

		$action = UniteFunctionsUC::getPostGetVariable("client_action", "", UniteFunctionsUC::SANITIZE_KEY);

		//check front actions
		switch($action){
			/*
			case "get_filters_data":
				$this->onAjaxFrontAction();
				exit();
			break;
			*/
		}

		$operations = new ProviderOperationsUC();
		$addons = new UniteCreatorAddons();
		$assets = new UniteCreatorAssetsWork();
		$categories = new UniteCreatorCategories();
		$layouts = new UniteCreatorLayouts();
		$webAPI = new UniteCreatorWebAPI();

		$data = $this->getDataFromRequest();
		$addonType = $addons->getAddonTypeFromData($data);

		$data = UniteFunctionsUC::convertStdClassToArray($data);
		$data = UniteProviderFunctionsUC::normalizeAjaxInputData($data);

		try{
			if(method_exists("UniteProviderFunctionsUC", "verifyNonce")){
				$nonce = UniteFunctionsUC::getPostGetVariable("nonce", "", UniteFunctionsUC::SANITIZE_NOTHING);
				UniteProviderFunctionsUC::verifyNonce($nonce);
			}

			switch($action){
				case "remove_category":

					HelperProviderUC::verifyAdminPermission();

					$response = $categories->removeFromData($data);

					HelperUC::ajaxResponseSuccess(esc_html__("The category deleted successfully", "unlimited-elements-for-elementor"), $response);
				break;
				case "update_category":

					HelperProviderUC::verifyAdminPermission();

					$categories->updateFromData($data);

					HelperUC::ajaxResponseSuccess(esc_html__("Category updated", "unlimited-elements-for-elementor"));
				break;
				case "update_cat_order":

					HelperProviderUC::verifyAdminPermission();

					$categories->updateOrderFromData($data);

					HelperUC::ajaxResponseSuccess(esc_html__("Order updated", "unlimited-elements-for-elementor"));
				break;
				case "get_category_settings_html":

					$manager = UniteCreatorManager::getObjManagerByAddonType($addonType);
					$response = $manager->getCatSettingsHtmlFromData($data);

					HelperUC::ajaxResponseData($response);
				break;
				case "get_cat_addons":

					$manager = UniteCreatorManager::getObjManagerByAddonType($addonType, $data);
					$response = $manager->getCatAddonsHtmlFromData($data);

					HelperUC::ajaxResponseData($response);
				break;
				case "get_layouts_params_settings_html":

					$manager = UniteCreatorManager::getObjManagerByAddonType($addonType, $data);
					$response = $manager->getAddonPropertiesDialogHtmlFromData($data);

					HelperUC::ajaxResponseData($response);
				break;
				case "get_catlist":

					$manager = UniteCreatorManager::getObjManagerByAddonType($addonType, $data);
					$response = $manager->getCatListFromData($data);

					HelperUC::ajaxResponseData($response);
				break;
				case "get_layouts_categories":
					$response = $categories->getLayoutsCatsListFromData($data);

					HelperUC::ajaxResponseData($response);
				break;
				case "get_addon_changelog":

					HelperProviderUC::verifyAdminPermission();
					HelperProviderUC::verifyAddonChangelogEnabled();

					$response = $operations->getAddonChangelogFromData($data);

					HelperUC::ajaxResponseData($response);
				break;
				case "add_addon_changelog":

					HelperProviderUC::verifyAdminPermission();
					HelperProviderUC::verifyAddonChangelogEnabled();

					$addons->addAddonChangelog($data);

					HelperUC::ajaxResponseSuccess(esc_html__("Log added.", "unlimited-elements-for-elementor"));
				break;
				case "update_addon_changelog":

					HelperProviderUC::verifyAdminPermission();
					HelperProviderUC::verifyAddonChangelogEnabled();

					$addons->updateAddonChangelog($data);

					HelperUC::ajaxResponseSuccess(esc_html__("Log updated.", "unlimited-elements-for-elementor"));
				break;
				case "delete_addon_changelog":

					HelperProviderUC::verifyAdminPermission();
					HelperProviderUC::verifyAddonChangelogEnabled();

					$addons->deleteAddonChangelog($data);

					HelperUC::ajaxResponseSuccess(esc_html__("Log deleted.", "unlimited-elements-for-elementor"));
				break;
				case "get_addon_revisions":

					HelperProviderUC::verifyAdminPermission();
					HelperProviderUC::verifyAddonRevisionsEnabled();

					$response = $operations->getAddonRevisionsFromData($data);

					HelperUC::ajaxResponseData($response);
				break;
				case "create_addon_revision":

					HelperProviderUC::verifyAdminPermission();
					HelperProviderUC::verifyAddonRevisionsEnabled();

					$addons->createAddonRevision($data);

					HelperUC::ajaxResponseSuccess(esc_html__("Revision created.", "unlimited-elements-for-elementor"));
				break;
				case "restore_addon_revision":

					HelperProviderUC::verifyAdminPermission();
					HelperProviderUC::verifyAddonRevisionsEnabled();

					$response = $addons->restoreAddonRevision($data);

					HelperUC::ajaxResponseSuccess(esc_html__("Revision restored.", "unlimited-elements-for-elementor"), $response);
				break;
				case "download_addon_revision":

					HelperProviderUC::verifyAdminPermission();
					HelperProviderUC::verifyAddonRevisionsEnabled();

					$addons->downloadAddonRevision($data);
					exit;
				break;
				case "update_addon":

					HelperProviderUC::verifyAdminPermission();

					$addons->updateAddonFromData($data);

					HelperUC::ajaxResponseSuccess(esc_html__("Updated.", "unlimited-elements-for-elementor"));
				break;
				case "get_addon_bulk_dialog":

					$response = $operations->getAddonBulkDialogFromData($data);

					HelperUC::ajaxResponseData($response);
				break;
				case "update_addons_bulk":

					HelperProviderUC::verifyAdminPermission();

					$addons->updateAddonsBulkFromData($data);
					$response = $operations->getAddonBulkDialogFromData($data);

					HelperUC::ajaxResponseData($response);
				break;
				case "delete_addon":

					HelperProviderUC::verifyAdminPermission();

					$addons->deleteAddonFromData($data);

					HelperUC::ajaxResponseSuccess(esc_html__("The addon deleted successfully", "unlimited-elements-for-elementor"));
				break;
				case "add_category":

					HelperProviderUC::verifyAdminPermission();

					$response = $categories->addFromData($data);

					HelperUC::ajaxResponseData($response);
				break;
				case "add_addon":

					HelperProviderUC::verifyAdminPermission();

					if(GlobalsUC::$permisison_add === false)
						UniteFunctionsUC::throwError("Operation not permitted");

					$response = $addons->createFromManager($data);

					HelperUC::ajaxResponseSuccess(esc_html__("Widget added successfully", "unlimited-elements-for-elementor"), $response);
				break;
				case "update_addon_title":

					HelperProviderUC::verifyAdminPermission();

					$addons->updateAddonTitleFromData($data);

					HelperUC::ajaxResponseSuccess(esc_html__("Widget updated successfully", "unlimited-elements-for-elementor"));
				break;
				case "update_addons_activation":

					HelperProviderUC::verifyAdminPermission();

					$addons->activateAddonsFromData($data);

					HelperUC::ajaxResponseSuccess(esc_html__("Widgets updated successfully", "unlimited-elements-for-elementor"));
				break;
				case "remove_addons":

					HelperProviderUC::verifyAdminPermission();

					$response = $addons->removeAddonsFromData($data);

					HelperUC::ajaxResponseSuccess(esc_html__("Widgets Removed", "unlimited-elements-for-elementor"), $response);
				break;
				case "update_addons_order":

					HelperProviderUC::verifyAdminPermission();

					$addons->saveOrderFromData($data);

					HelperUC::ajaxResponseSuccess(esc_html__("Order Saved", "unlimited-elements-for-elementor"));
				break;
				case "update_layouts_order":

					HelperProviderUC::verifyAdminPermission();

					$layouts->updateOrderFromData($data);

					HelperUC::ajaxResponseSuccess(esc_html__("Order Saved", "unlimited-elements-for-elementor"));
				break;
				case "move_addons":

					HelperProviderUC::verifyAdminPermission();

					$response = $addons->moveAddonsFromData($data);

					HelperUC::ajaxResponseSuccess(esc_html__("Done Operation", "unlimited-elements-for-elementor"), $response);
				break;
				case "duplicate_addons":

					HelperProviderUC::verifyAdminPermission();

					$response = $addons->duplicateAddonsFromData($data);

					HelperUC::ajaxResponseSuccess(esc_html__("Duplicated Successfully", "unlimited-elements-for-elementor"), $response);
				break;
				case "get_addon_config_html":  //from elementor

					$response = $addons->getAddonConfigHTML($data);

					HelperUC::ajaxResponseData($response);
				break;
				case "get_addon_settings_html":    //from elementor/gutenberg

					$html = $addons->getAddonSettingsHTMLFromData($data);

					HelperUC::ajaxResponseData(array("html" => $html));
				break;
				case "get_addon_item_settings_html":  //from elementor

					$html = $addons->getAddonItemsSettingsHTMLFromData($data);

					HelperUC::ajaxResponseData(array("html" => $html));
				break;
				case "get_addon_editor_data":  //from elementor

					$response = $addons->getAddonEditorData($data);

					HelperUC::ajaxResponseData($response);
				break;
				case "get_addon_output_data":  //from elementor editor bg/gutenberg

					$response = $addons->getAddonOutputData($data);

					HelperUC::ajaxResponseData($response);
				break;
				case "show_preview":

					$addons->showAddonPreviewFromData($data);
					exit;
				break;
				case "save_addon_defaults":

					HelperProviderUC::verifyAdminPermission();

					$addons->saveAddonDefaultsFromData($data);

					HelperUC::ajaxResponseSuccess(esc_html__("Saved", "unlimited-elements-for-elementor"));
				break;
				case "save_test_addon":

					HelperProviderUC::verifyAdminPermission();

					$addons->saveTestAddonData($data);

					HelperUC::ajaxResponseSuccess(esc_html__("Saved", "unlimited-elements-for-elementor"));
				break;
				case "get_test_addon_data":

					$response = $addons->getTestAddonData($data);

					HelperUC::ajaxResponseData($response);
				break;
				case "delete_test_addon_data":

					$addons->deleteTestAddonData($data);

					HelperUC::ajaxResponseSuccess(esc_html__("Test data deleted", "unlimited-elements-for-elementor"));
				break;
				case "export_addon":

					HelperProviderUC::verifyAdminPermission();

					$addons->exportAddon($data);
					exit;
				break;
				case "export_cat_addons":

					HelperProviderUC::verifyAdminPermission();

					$addons->exportCatAddons($data);
				break;
				case "import_addons":

					HelperProviderUC::verifyAdminPermission();

					$response = $addons->importAddons($data);

					HelperUC::ajaxResponseSuccess(esc_html__("Addons Imported", "unlimited-elements-for-elementor"), $response);
				break;
				case "import_layouts":

					HelperProviderUC::verifyAdminPermission();

					$urlRedirect = $layouts->importLayouts($data);

					if(!empty($urlRedirect))
						HelperUC::ajaxResponseSuccessRedirect(HelperUC::getText("layout_imported"), $urlRedirect);
					else
						HelperUC::ajaxResponseSuccess(HelperUC::getText("layout_imported"));

				break;
				case "get_version_text":

					$content = HelperHtmlUC::getVersionText();

					HelperUC::ajaxResponseData(array("text" => $content));
				break;
				case "update_plugin":

					HelperProviderUC::verifyAdminPermission();

					if(method_exists("UniteProviderFunctionsUC", "updatePlugin"))
						UniteProviderFunctionsUC::updatePlugin();
					else{
						echo "Functionality Don't Exists";
						exit;
					}

				break;
				case "update_general_settings":

					HelperProviderUC::verifyAdminPermission();

					$operations->updateGeneralSettingsFromData($data);

					HelperUC::ajaxResponseSuccess(esc_html__("Settings Saved", "unlimited-elements-for-elementor"));
				break;
				case "update_global_layout_settings":

					HelperProviderUC::verifyAdminPermission();

					UniteCreatorLayout::updateLayoutGlobalSettingsFromData($data);

					HelperUC::ajaxResponseSuccess(esc_html__("Settings Saved", "unlimited-elements-for-elementor"));
				break;
				case "update_layout":

					HelperProviderUC::verifyAdminPermission();

					$response = $layouts->updateLayoutFromData($data);

					$this->onUpdateLayoutResponse($response);
				break;
				case "update_layout_category":

					HelperProviderUC::verifyAdminPermission();

					$layouts->updateLayoutCategoryFromData($data);

					HelperUC::ajaxResponseSuccess(esc_html__("Category Updated", "unlimited-elements-for-elementor"));
				break;
				case "update_layout_params":

					HelperProviderUC::verifyAdminPermission();

					$response = $layouts->updateParamsFromData($data);

					HelperUC::ajaxResponseSuccess(esc_html__("Layout Updated", "unlimited-elements-for-elementor"), $response);
				break;
				case "delete_layout":

					HelperProviderUC::verifyAdminPermission();

					$layouts->deleteLayoutFromData($data);

					$urlLayouts = HelperUC::getViewUrl_LayoutsList();

					HelperUC::ajaxResponseSuccessRedirect(HelperUC::getText("layout_deleted"), $urlLayouts);
				break;
				case "export_layout":

					HelperProviderUC::verifyAdminPermission();

					$layouts->exportLayout($data);
					exit;
				break;
				case "activate_product":

					HelperProviderUC::verifyAdminPermission();

					$expireDays = $webAPI->activateProductFromData($data);

					HelperUC::ajaxResponseSuccess(esc_html__("Product Activated", "unlimited-elements-for-elementor"), array("expire_days" => $expireDays));
				break;
				case "deactivate_product":

					HelperProviderUC::verifyAdminPermission();

					$webAPI->deactivateProduct($data);

					HelperUC::ajaxResponseSuccess("Product Deactivated, please refresh the page");
				break;
				case "check_catalog":

					$isForce = UniteFunctionsUC::getVal($data, "force");
					$isForce = UniteFunctionsUC::strToBool($isForce);

					$response = $webAPI->checkUpdateCatalog($isForce);

					$operations->checkInstagramRenewToken();

					HelperUC::ajaxResponseData($response);
				break;
				case "install_catalog_addon":

					HelperProviderUC::verifyAdminPermission();

					$response = $webAPI->installCatalogAddonFromData($data);

					HelperUC::ajaxResponseSuccess(esc_html__("Widget Installed", "unlimited-elements-for-elementor"), $response);
				break;
				case "install_catalog_page":

					HelperProviderUC::verifyAdminPermission();

					$response = $webAPI->installCatalogPageFromData($data);

					HelperUC::ajaxResponseSuccess(esc_html__("Template Installed", "unlimited-elements-for-elementor"), $response);
				break;
				case "update_addon_from_catalog":  //by id

					HelperProviderUC::verifyAdminPermission();

					$urlRedirect = $addons->updateAddonFromCatalogFromData($data);

					if(!empty($urlRedirect))
						HelperUC::ajaxResponseSuccessRedirect(esc_html__("Widget Updated", "unlimited-elements-for-elementor"), $urlRedirect);
					else
						HelperUC::ajaxResponseSuccess(esc_html__("Widget Updated", "unlimited-elements-for-elementor"));

				break;
				case "get_shapes_css":

					$objShapes = new UniteShapeManagerUC();
					$objShapes->outputCssShapes();
					exit;
				break;
				case "save_screenshot":

					$response = $operations->saveScreenshotFromData($data);

					HelperUC::ajaxResponseSuccess(esc_html__("Screenshot Saved", "unlimited-elements-for-elementor"), $response);
				break;
				case "save_section_tolibrary":

					HelperProviderUC::verifyAdminPermission();

					$response = $layouts->saveSectionToLibraryFromData($data);

					HelperUC::ajaxResponseSuccess(esc_html__("Section Saved", "unlimited-elements-for-elementor"), $response);
				break;
				case "get_grid_import_layout_data":

					$response = $layouts->getLayoutGridDataForEditor($data);

					HelperUC::ajaxResponseData($response);
				break;
				case "save_custom_settings":

					HelperProviderUC::verifyAdminPermission();

					$operations->updateCustomSettingsFromData($data);

					HelperUC::ajaxResponseSuccess(esc_html__("Settings Saved", "unlimited-elements-for-elementor"));
				break;
				case "get_terms_list_forselect":

					$arrTermsList = $operations->getTermsListForSelectFromData($data);

					HelperUC::ajaxResponseData($arrTermsList);

				break;
				case "get_posts_list_forselect":

					$arrPostList = $operations->getPostListForSelectFromData($data);

					HelperUC::ajaxResponseData($arrPostList);
				break;
				case "get_select2_post_titles":

					$arrData = $operations->getSelect2PostTitles($data);

					HelperUC::ajaxResponseData(array("select2_data" => $arrData));
				break;
				case "get_select2_terms_titles":

					$arrData = $operations->getSelect2TermsTitles($data);

					HelperUC::ajaxResponseData(array("select2_data" => $arrData));
				break;
				case "get_post_child_params":

					$response = $operations->getPostAttributesFromData($data);

					HelperUC::ajaxResponseData($response);
				break;
				case "import_elementor_catalog_template":

					HelperProviderUC::verifyAdminPermission();

					$response = $webAPI->installCatalogTemplateFromData($data);

					HelperUC::ajaxResponseSuccess(esc_html__("Template Imported", "unlimited-elements-for-elementor"), $response);
				break;
				case "save_instagram_connect_data":

					HelperProviderUC::verifyAdminPermission();

					$objServices = new UniteServicesUC();
					$objServices->includeInstagramAPI();

					HelperInstaUC::saveInstagramConnectDataAjax($data);
				break;
				case "renew_instagram_access_token":

					HelperProviderUC::verifyAdminPermission();

					$objServices = new UniteServicesUC();
					$objServices->includeInstagramAPI();

					HelperInstaUC::renewAccessToken();
					HelperInstaUC::redirectToGeneralSettings();
				break;
				case "save_google_connect_data":

					HelperProviderUC::verifyAdminPermission();

					$objServices = new UniteServicesUC();
					$objServices->includeGoogleAPI();

					try{
						$params = array();
						$error = UniteFunctionsUC::getVal($data, "error");

						if(empty($error) === false)
							UniteFunctionsUC::throwError($error);

						UEGoogleAPIHelper::saveCredentials($data);
					}catch(Exception $exception){
						$params = array("google_connect_error" => $exception->getMessage());
					}

					UEGoogleAPIHelper::redirectToSettings($params);
				break;
				case "remove_google_connect_data":

					HelperProviderUC::verifyAdminPermission();

					$objServices = new UniteServicesUC();
					$objServices->includeGoogleAPI();

					try{
						$params = array();
						$error = UniteFunctionsUC::getVal($data, "error");

						if(empty($error) === false)
							UniteFunctionsUC::throwError($error);

						UEGoogleAPIHelper::removeCredentials();
					}catch(Exception $exception){
						$params = array("google_connect_error" => $exception->getMessage());
					}

					UEGoogleAPIHelper::redirectToSettings($params);
				break;
				case "dismiss_notice":

					UCAdminNoticesManager::dismissNotice($data['id']);

					HelperUC::ajaxResponseSuccess(esc_html__("Notice Dismissed", "unlimited-elements-for-elementor"));
				break;
				case "postpone_notice":

					UCAdminNoticesManager::postponeNotice($data['id'], $data['duration']);

					HelperUC::ajaxResponseSuccess(esc_html__("Notice Postponed", "unlimited-elements-for-elementor"));
				break;
				default:

					//check assets
					$found = $assets->checkAjaxActions($action, $data);

					if(!$found)
						$found = UniteProviderFunctionsUC::applyFilters(UniteCreatorFilters::FILTER_ADMIN_AJAX_ACTION, $found, $action, $data);

					if(!$found)
						HelperUC::ajaxResponseError("wrong ajax action: <b>$action</b> ");
				break;
			}
		}catch(Exception $e){
			$errorMessage = $e->getMessage();

			if(GlobalsUC::$SHOW_TRACE === true){
				$trace = $e->getTraceAsString();
				$errorMessage .= "<pre>" . $trace . "</pre>";
			}

			HelperUC::ajaxResponseError($errorMessage);
		}

		//it's an ajax action, so exit
		HelperUC::ajaxResponseError("No response output on <b> $action </b> action. please check with the developer.");
		exit;
	}

	/**
	 * on ajax action
	 */
	public function onAjaxFrontAction(){

		$actionType = UniteFunctionsUC::getPostGetVariable("action", "", UniteFunctionsUC::SANITIZE_KEY);

		if($actionType != GlobalsUC::PLUGIN_NAME . "_ajax_action")
			return (false);

		$action = UniteFunctionsUC::getPostGetVariable("client_action", "", UniteFunctionsUC::SANITIZE_KEY);
		$data = $this->getDataFromRequest();

		try{
			//switch($action){}
		}catch(Exception $e){
			$message = $e->getMessage();
			$errorMessage = $message;

			if(GlobalsUC::$SHOW_TRACE == true){
				$trace = $e->getTraceAsString();
				$errorMessage = $message . "<pre>" . $trace . "</pre>";
			}

			HelperUC::ajaxResponseError($errorMessage);
		}

		//it's an ajax action, so exit
		HelperUC::ajaxResponseError("No response output on <b> $action </b> action. please check with the developer.");
		exit();
	}

}

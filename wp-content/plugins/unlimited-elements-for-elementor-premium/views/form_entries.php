<?php

/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

require GlobalsUC::$pathProvider . "views/form_entry_service.php";

$id = UniteFunctionsUC::getGetVar("entry", null, UniteFunctionsUC::SANITIZE_ID);
$action = UniteFunctionsUC::getGetVar("action", null, UniteFunctionsUC::SANITIZE_KEY);

if(empty($id) === false && $action === "view"){
	require HelperUC::getPathViewObject("form_entry_view.class");

	$formEntry = new UCFormEntryView($id);
	$formEntry->display();
}else{
	require HelperUC::getPathViewObject("form_entries_view.class");

	$formEntries = new UCFormEntriesView();
	$formEntries->display();
}

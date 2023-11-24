"use strict";

function UniteCreatorAdmin_LayoutsList(){
	
	var t = this;
	var g_providerAdmin = new UniteProviderAdminUC();
	var g_settingsGlobal, g_tableLayouts;
	var g_objBrowser = new UniteCreatorBrowser(),g_objButtonCatalogImport;
	
	
	//layouts related
	var g_selectedCatID = -1, g_openedLayout = -1, g_selectedSort = "";
	var g_searchText,g_oldCatTitle = "", g_canRename = true;
	var g_isDeleteInProcess = false;
	
	
	if(!g_ucAdmin)
		var g_ucAdmin = new UniteAdminUC();

	
	/**
	 * init global settings dialog
	 */
	function initGlobalSettingsDialog(){
		
		//init settings
		var settingsWrapper = jQuery("#uc_layout_general_settings");
		
		
		g_settingsGlobal = new UniteSettingsUC();
		g_settingsGlobal.init(settingsWrapper);
		
		//on open dialog click
		jQuery("#uc_layouts_global_settings").on("click",function(){
			
			var dialogOptions = {
					minWidth: 750
			};
			
			g_ucAdmin.openCommonDialog("#uc_dialog_layout_global_settings", null, dialogOptions);
			
		});
		
		jQuery("#uc_dialog_layout_global_settings_action").on("click",function(){
			
			var settingsData = g_settingsGlobal.getSettingsValues();
			var data = {
				settings_values: settingsData
			};
			
			g_ucAdmin.dialogAjaxRequest("uc_dialog_layout_global_settings", "update_global_layout_settings", data);
			
		});
		
	}
	
	
	/**
	 * on delete layout click
	 */
	function onDeleteClick(){
		var objButton = jQuery(this);
		var objLoader = objButton.siblings(".uc-loader-delete");
		
		var textDelete = g_tableLayouts.data("text-delete");
				
		if(confirm(textDelete) == false)
			return(false);
			
		objButton.hide();
		objLoader.show();
		
		var layoutID = objButton.data("layoutid");
		
		var data = {
				layout_id: layoutID
		};
		
		g_ucAdmin.ajaxRequest("delete_layout", data);
		
	}
	
	
	/**
	 * duplicating addon
	 */
	this.onDuplicateClick = function(event, layoutID, objButton, addParams){
		
		if(!objButton)
			var objButton = jQuery(this);
		
		var objLoader = objButton.siblings(".uc-loader-duplicate");
		
		objButton.hide();
		objLoader.show();
		
		if(!layoutID)
			var layoutID = objButton.data("layoutid");
		
		var data = {
				layout_id: layoutID
		};
		
		if(addParams)
			data = jQuery.extend(data, addParams);
		
		g_ucAdmin.ajaxRequest("duplicate_layout", data);
		
	}
	
	
	/**
	 * on export click
	 */
	this.onExportClick = function(event, layoutID){
		
		if(!layoutID){
			var objButton = jQuery(this);
			var layoutID = objButton.data("layoutid");
		}
		
		var params = "id="+layoutID;
		var urlExport = g_ucAdmin.getUrlAjax("export_layout", params);
		
		location.href=urlExport;
		
	}
	
	
	
	function ___________IMPORT_DIALOG_____________(){}
	
	
	/**
	 * open import layout dialog
	 */
	function openImportLayoutDialog(){
						
		jQuery("#dialog_import_layouts_file").val("");
		
		var options = {minWidth:700, minHeight:300};
				
		g_ucAdmin.openCommonDialog("#uc_dialog_import_layouts", null, options);
		
	}
	
	
	/**
	 * open import page catalog
	 */
	function openImportPageCatalog(){
		
		g_objBrowser.openAddonsBrowser(null, function(pageData){
			
			//g_objBrowser.closeCatalog();
			if(pageData && pageData.url_redirect)
				window.location.href = pageData.url_redirect;
			else
				window.location.reload();
			
		});	

	}
	
	
	/**
	 * init import layout dialog
	 */
	this.initImportLayoutDialog = function(addParams, layoutID){
				
		jQuery("#uc_button_import_layout").on("click",openImportLayoutDialog);
		g_objButtonCatalogImport = jQuery("#uc_button_import_layout_from_catalog");
		
		if(g_objButtonCatalogImport.length)
			g_objButtonCatalogImport.on("click",openImportPageCatalog);
				
		jQuery("#uc_dialog_import_layouts_action").on("click",function(){
			
			var isOverwrite = jQuery("#dialog_import_layouts_file_overwrite").is(":checked");
	        var data = {overwrite_addons:isOverwrite};
	        if(addParams)
	        	data.params = addParams;
	        
	        if(layoutID){
	        	data.layoutID = layoutID;
	        	data.no_redirect = true;
	        }
	        	        
	        var objData = new FormData();
	        var jsonData = JSON.stringify(data);
	    	objData.append("data", jsonData);
	    	
	    	g_ucAdmin.addFormFilesToData("dialog_import_layouts_form", objData);
	    	
			g_ucAdmin.dialogAjaxRequest("uc_dialog_import_layouts", "import_layouts", objData);
		});
		
	};
	
	/**
	 * init view events
	 */
	function initEvents(){
		
		if(g_tableLayouts){
			
			g_tableLayouts.on("click", ".button_delete", onDeleteClick);
			g_tableLayouts.on("click", ".button_duplicate", t.onDuplicateClick);
			g_tableLayouts.on("click", ".button_export", t.onExportClick);
		
		}
		
	}
	
	/**
	 * init import page dialog
	 */
	this.initImportPageCatalog = function(addParams){
		
		var objBrowserWrapper = jQuery("#uc_addon_browser_layout");
		
		//validate browser if there is button
		if(g_objButtonCatalogImport.length){
			g_ucAdmin.validateDomElement(objBrowserWrapper, "pages browser");
		}
		
		if(objBrowserWrapper.length == 0)
			return(false);
		
		g_objBrowser.init(objBrowserWrapper, addParams);
		
	};
		
	function ___________CATEGORIES_____________(){}

		
	/**
	 * init categories related events
	 */
	function initEventsCats(){
		
		
		/**
		* event handler for clear filter
		*/
		jQuery(document).on('click', '.uc-catdialog-button-filter-clear', function(){
			filterClear();
		});
		
		/**
		* event handler for filter by word
		*/
		jQuery(document).on('click', '.uc-catdialog-button-filter', function(){
			var val = jQuery('.uc-catdialog-button-clearfilter').val();
			if(val == ''){
				filterClear();
				return;
			}
						
			loadCats(g_selectedCatID, g_selectedSort, val);
			
		});
		
		
		/**
		* handler to submit delete button
		*/
		jQuery(document).on('click', '.uc-button-delete-category', function(){
			
			var catid = parseInt(jQuery(this).data('catid'));
			if(isNaN(catid)) 
				catid = jQuery(this).attr('data-catid');
			
			deleteCat(catid);
		});
		
		
		//event handler for change sort
		jQuery(document).on('click', 'a.uc-link-change-cat-sort', function(){
			
			var type = jQuery(this).data('type');
			
			if(type == g_selectedSort) 
				return false;
			
			g_selectedSort = type;
			
			loadCats();

		});
		
		
		// event handler for button set category
		jQuery("#uc_dialog_add_category_action").on("click",setCategoryForLayout);
		
		jQuery(".uc-layouts-list-category").on("click",onChangeCategoryClick);
		
		//function for event "click" button cancel while editing category
		jQuery(document).on('click', '.egn-cancel-inp', function(){
			var parent = jQuery(this).parent('td').parent('tr');
			var catid = jQuery(parent).data('catid');
			
			//cancel editing
			closeEdit(catid, g_oldCatTitle);			
		});
		
		
		//click save event handler
		jQuery(document).on('click', '.egn-save-inp', function(){
			saveCatName(this);
		});
		
		
	}

	/**
	* on change category on layouts table click
	*/
	function onChangeCategoryClick(){
		
		var objButton = jQuery(this);

		var action = objButton.data("action");
		var layoutID = objButton.data("layoutid");
		
		var catID = objButton.data("catid"); 
		catID = parseInt(catID);
		
		openManageCategoryDialog(layoutID, catID);
		
	}
	
	/**
	 * open add category dialog
	 */
	function openManageCategoryDialog(layoutID, catID){
					
		var objDialog = jQuery("#uc_dialog_add_category");
		objDialog.data("catid", catID);
		objDialog.data("layoutid", layoutID);
		
		g_selectedCatID = catID;
		
		g_openedLayout = layoutID;
		
		g_ucAdmin.openCommonDialog("#uc_dialog_add_category", function(){
			
			loadCats(catID);
			
			jQuery("#uc_dialog_add_category_catname").val("").focus();
		});
		
	}
	
	/*
	*	showing list of categories
	**/
	function loadCats(sort, filter_word){
		
		var data = {};
		data.type = "layout";
		
		if(sort == 'a-z' || sort == 'z-a'){
			data.sort = sort;
		} else if(g_selectedSort != ''){
			data.sort = g_selectedSort;
		}
		
		if(filter_word != ''){
			g_searchText = filter_word;
			data.filter_word = filter_word;
		} else if(g_searchText != ""){
			data.filter_word = g_searchText;
		}
		
		jQuery("#list_layouts_cats").html('Loading...');

		
		g_ucAdmin.ajaxRequest("get_layouts_categories", data, function(response){
			
			var html = "<table>"; //prepare html
			
			jQuery.each(response.cats_list, function(key, value){
				
				var addHTML = ""; //for selected attr
				
				if(value.id == g_selectedCatID)
					addHTML = "selected";
				
				html += "<tr class='category " + addHTML + "' data-catid='"+value.id+"' data-countl='"+value.num_layouts+"'><td class='cat-name'>"+value.title+"</td><td class='controls'></td></tr>";
			});
			
			html += "</table>";
			
			jQuery("#list_layouts_cats").html(html);
			
			jQuery("#list_layouts_cats td.controls:gt(0)").append(" <span class='uc_layout_category_rename'>rename</span> | <span class='uc-link-delete-category'>delete</span>")
			
			scrollToCat(g_selectedCatID);
			
		});
	}

	/*
	* show dialog with custom text (for messages)
	*/
	function showMsgCats(msg, isError){
		
		if(isError == true)
			msg = "<div class='unite-color-red'>"+msg+"</div>";
		
		jQuery("#uc_layout_categories_message").html(msg);
		jQuery("#uc_layout_categories_message").dialog({
			minWidth:400,
			buttons:{
				"Close":function(){
					jQuery("#uc_layout_categories_message").dialog("close");
				}
			}
		});
		
	}
	
	
	/**
	* hide my dialog
	*/
	function hideMsgCats(){
		jQuery('#uc_layout_categories_message').dialog('close').html("");
	}
	
	 /**
	 * function for add new category
	 */
	function addCategory(){
		
		var data = {};
		data.catname = jQuery("#uc_dialog_add_category_catname").val();
		data.type = "layout";
		
		g_ucAdmin.dialogAjaxRequest("uc_dialog_add_category", "add_category", data, function(response){
			loadCats();
		}, {noclose: true});
		
	}

	 /**
	 * function initialige manage category dialog and some events
	 */
	function initManageCategoryDialog(){

		jQuery("#uc_dialog_add_category_button_add").on("click",addCategory);
		
		// set update title onenter function
		jQuery("#uc_dialog_add_category_catname").keyup(function(event){
			if(event.keyCode == 13)
				addCategory();
		});

		// set events
		jQuery(document).on('click', '.uc_layout_category_rename', function(){
			renameCategory(this);
		});

		jQuery(document).on('click', '#list_layouts_cats tr.category', function(){
			if(g_selectedCatID != -1) 
				jQuery('tr.category[data-catid='+g_selectedCatID+']').removeClass('selected');

			g_selectedCatID = jQuery(this).data('catid');
			jQuery(this).addClass('selected')
		});	
	}

	/**
	*	function that show input and needed buttons to edit name category
	*/
	function renameCategory(elem){
		
		if(!g_canRename) 
			return false;
		
		g_canRename = false;
		var parent = jQuery(elem).parent('td').parent('tr');

		var catid = jQuery(parent).data('catid');

		var val_form = jQuery(parent).find('td.cat-name').html();
		g_oldCatTitle = val_form;

		var html = "<input name='egn-change-name' data-catid='"+catid+"' value='"+val_form+"' type='text'><button class='egn-save-inp unite-button-primary'>Save</button><button class='egn-cancel-inp unite-button-secondary'>cancel</button>";
		jQuery(parent).find('td.cat-name').html(html);
	}
	
	/**
	* hide input and buttons and instead show  name of category
	*/
	function closeEdit(catId, txt){
		jQuery('.category[data-catid='+catId+'] td.cat-name').html(txt);
		g_canRename = true;
		g_oldCatTitle = "";
	}

	/**
	* prepare for save category name
	*/
	function saveCatName(elem){
		
		var parent = jQuery(elem).parent('td');
		jQuery(elem).attr('disabled', 'true').html('Saving...');
		var objInput = jQuery(parent).find('input[name=egn-change-name]');
		
		var catid = objInput.data('catid');
		var newTitle = objInput.val();
		
		updateCategoryTitle(catid, newTitle);
	}
	
	/**
	* scroll to needed category by id
	*/
	function scrollToCat(catID){
		
		if(catID == 0 || catID == '' || catID == -1) 
			return false;
		
		jQuery('#list_layouts_cats').scrollTop(parseInt(jQuery('.category[data-catid='+catID+']').offset().top - 134));
	}

	
	/**
	 * function invoke from the dialog update button
	 */
	function updateCategoryTitle(catID, newTitle){
		
		var data = {
			cat_id: catID,
			title: newTitle
		};
		
		//show update error
		g_ucAdmin.setErrorMessageID(function(message, operation){
			
			jQuery('.egn-save-inp:disabled').removeAttr('disabled').html('Save');
			showMsgCats(message, true);
			
		});
		
		g_ucAdmin.ajaxRequest("update_category", data, function(response){
			
			jQuery('a.uc-layouts-list-category[data-catid='+catID+']').html(newTitle);
			closeEdit(catID, newTitle);
			
		});
	}
	
	/**
	* get id from current selected category
	*/
	function getSelectedCatIDFromHtmlTable(){
		
		var id = parseInt(jQuery('#list_layouts_cats table tr.category.selected').data('catid'));
		
		if(!isNaN(id)) 
			return id;
		
		return 0;
	}
	
	/**
	 * get name category by id (using list of categories)
	 */
	function getCategoryNameFromHtmlTableById(id){
		
		var name = jQuery('#list_layouts_cats table tr.category[data-catid='+id+'] td.cat-name').html();
		return name;
	}
	
	/**
	* delete category
	*/
	function deleteCat(catId){
		
		jQuery('.egn-btn-del').attr('disabled', 'true').html('deleting...');
		var catId = parseInt(catId);
		if(isNaN(catId)) 
			return false;

		var data = {};
		data.catID = catId;
		data.type = 'layout';
		
		g_ucAdmin.ajaxRequest("remove_category", data, function(response){
			g_isDeleteInProcess = false;
			jQuery('a.uc-layouts-list-category[data-catid='+catId+']').html('Uncategorized').attr("data-catid", 0).data('catid', 0);

			hideMsgCats();

			if(g_selectedCatID == catId) 
				g_selectedCatID = -1;
			
			loadCats();
		});
		
	}
	
	/*
	* event handler for delete label, prepare data and check count of categories
	*/
	jQuery(document).on('click', '.uc-link-delete-category', function(){
		
		if(g_isDeleteInProcess) 
			return false;

		g_isDeleteInProcess = true;
		
		var parent = jQuery(this).parent('td').parent('tr');
		
		var catid = jQuery(parent).data('catid');
		
		if(!catid || catid == '') 
			return false;

		var count = jQuery(parent).data('countl');
		
		if(count > 0){
			showMsgCats('This category contains layouts. Are you sure? <br/><br/><a class="unite-button-primary egn-btn-del uc-button-delete-category" href="javascript:void(0)" data-catid="'+catid+'">Yes, delete category</a>');
		} else {
			showMsgCats('deleting...');
			deleteCat(catid);
			g_isDeleteInProcess = false;
		}
		
	});
	
	/**
	* set category to layout (receives button object)
	*/
	function setCategoryForLayout(){
				
		var catID = getSelectedCatIDFromHtmlTable();
		var layoutID = g_openedLayout;
		
		data = {
			layoutid: layoutID,
			catid: catID
		};
		
		g_ucAdmin.dialogAjaxRequest("uc_dialog_add_category", "update_layout_category", data, function(){
			
			var objTableItem = jQuery('a.uc-layouts-list-category[data-layoutid='+layoutID+']');
			
			objTableItem.html(getCategoryNameFromHtmlTableById(catID));
			objTableItem.attr("data-catid", catID).data('catid', catID);
		});
		
	}
	
	
	/**
	* clear filter (clear variables and input and reload list of categories)
	*/
	function filterClear(){
		g_searchText = "";
		jQuery('.uc-catdialog-button-clearfilter').val('');
		loadCats();
	}
	
	
	/**
	 * init the categories
	 */
	function initCategories(){
		
		g_selectedCatID = -1;
		
		initEventsCats();
		initManageCategoryDialog();
	}
	
	
	function ___________INIT_____________(){}

	/**
	 * objects list view
	 */
	this.initObjectsListView = function(){
				
		g_tableLayouts = jQuery("#uc_table_layouts");
		if(g_tableLayouts.length == 0)
			g_tableLayouts = null;
		
		//g_ucAdmin.validateDomElement(g_tableLayouts, "table layouts");
				
		initGlobalSettingsDialog();
		t.initImportLayoutDialog();
		
		t.initImportPageCatalog();
		
		initEvents();
		
		initCategories();
		
	}

	
}
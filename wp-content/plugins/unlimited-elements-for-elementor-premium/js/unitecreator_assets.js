"use strict";

function UCAssetsManager(){

	var g_objWrapper, g_activePath, g_startPath, g_pathKey, g_objFileList;
	var g_objPanel, g_codeMirror, g_objBrowserMove, g_objErrorFilelist;
	
	var g_options = {
			single_item_select:false,
			custom_startPath:null,
			addon_id:null
	};
	
	if(!g_ucAdmin){
		var g_ucAdmin = new UniteAdminUC();
	}
	
	var t = this;
	
	//internal events
	var events = {
			CHANGE_FILELIST: "change_filelist",
			SELECT_ITEM: "select_item",
			UPDATE_FILES: "update_files",
			SELECT_OPERATION: "select_click"		//trigger only when clicked on checkbox or on item
	};
	
	var g_temp = {
		needRefreshAssets: false,
		isBrowser:false,
		funcOnSelectOperation:null,
		funcOnAjaxLoadPath:null,
		funcOnUpdateFiles:null
	};
	
	
	function ____________GETTERS______________(){};

	
	/**
	 * get data from item
	 */
	function getItemData(objItem){
		var data = {};
		
		data["type"] = objItem.data("type");
		data["file"] = objItem.data("file");
		data["url"] = objItem.data("url");
		data["full_url"] = g_ucAdmin.urlToFull(data["url"]);
		data["filepath"] = g_activePath+"/"+data["file"];
		
		return(data);
	}
	
	
	/**
	 * get items array from objects
	 */
	function getArrItemsFromObjects(objItems){

		var arrItems = [];
		
		jQuery.each(objItems,function(index, item){
			var objItem = jQuery(item);
			var data = getItemData(objItem);
			data.objItem = objItem;
			
			arrItems.push(data);
		});
		
		return(arrItems);
	}

	
	/**
	 * get checkboxes object
	 */
	function getArrItems(){
		var objItems = getObjItems();
		var arrItems = getArrItemsFromObjects(objItems);
		
		return(arrItems);
	}
	
	/**
	 * get items array by filename
	 */
	function getItemByFilename(filename){
		
		var arrItems = getArrItems();
		
		var arrItemsNew = arrItems.filter(function(item){
			
			if(item.file == filename)
				return(true);
			
			return(false);
		});
		
		if(arrItemsNew.length == 0)
			return(null);
		
		var arrItem = arrItemsNew[0];
				
		return(arrItem);
	}
	
	
	/**
	 * get items objects
	 */
	function getObjItems(type){
		var selector = ".uc-filelist-item";
		if(type)
			selector += ".uc-type-"+type;
		
		var objItems = g_objWrapper.find(selector);
		
		return(objItems);
	}
	
	/**
	 * get child deletories object - without the ..
	 */
	function getObjChildDirs(){
		var objDirs = getObjItems("dir");
		objDirs = objDirs.not(".uc-dir-back");
		return(objDirs);
	}
	
	
	/**
	 * check if some item selected
	 */
	function isItemSelected(objItem){
		if(objItem.hasClass("uc-filelist-item-selected"))
			return(true);
		
		return(false);
	}
	
	
	/**
	 * get selected items objects
	 */
	function getObjSelectedItems(){
		var objItems = g_objWrapper.find(".uc-filelist-item-selected");
		return(objItems);
	}
	
	
	/**
	 * get unselected items objects
	 */
	function getObjUnselectedItems(){
		var objItems = g_objWrapper.find(".uc-filelist-item").not(".uc-filelist-item-selected");
		return(objItems);
	}
	
	
	/**
	 * get all selected items
	 */
	function getArrSelectedItems(){
		var objItems = getObjSelectedItems();
		var arrItems = getArrItemsFromObjects(objItems);
		
		return(arrItems);
		
	}
	
	
	/**
	 * get single selected item, if the item is not single throw error
	 */
	function getSelectedSingleItem(){
		var arrItems = getArrSelectedItems();
		if(arrItems.length != 1)
			throw new Error("Wrong number of selected item. Should be 1");
		
		var item = arrItems[0];
		return(item);
	}
	
	
	/**
	 * get assets items array
	 */
	function getArrSelectedFiles(){
		
		var arrFiles = [];
		var arrItems = getArrSelectedItems();
		
		jQuery.each(arrItems, function(index, item){
			arrFiles.push(item.file);
		});
		
		return(arrFiles);
	}
	
	
	/**
	 * get num items
	 */
	function getNumItems(type){
		var objItems = getObjItems(type);
		var numItems = objItems.length;
		return(numItems);
	}
	
	
	/**
	 * get num selected items
	 */
	function getNumSelectedItems(){
		var objItems = g_objWrapper.find(".uc-filelist-item-selected");
		var numItems = objItems.length;
		return(numItems);
	}

	
	/**
	 * get parent folder
	 */
	function getParentFolder(path){
		
		if(path.length == 0)
			return(path);
		
		var searchPos = path.length-2;
		
		var lastSap = path.lastIndexOf("/", searchPos);
		
		if(lastSap == -1)
			lastSap = path.lastIndexOf("\\", searchPos);
				
		if(lastSap == -1)
			return(path);
		
		path = path.substring(0, lastSap);
		return(path);
	}
	
	
	/**
	 * get path by file. concat file to active path
	 */
	function getPathByFile(file){
		
		var path = g_activePath;
		
		if(file == ".."){		//get parent path
			path = getParentFolder(path);
		}else{
			var isWinSlash = (path.indexOf("\\") !== -1);
			if(isWinSlash == true)
				path = path + "\\" + file;
			else
				path = path + "/" + file;
		}
		
		path = path.replace("//","/");
		path = path.replace("\\\\","\\");
		
		return(path);
	}
	
	
	
	function ____________OPERATIONS______________(){};
	
	
	/**
	 * update filepath to the the relevant div
	 */
	function updateActivePath(path){
		g_activePath = path;
		g_objWrapper.find(".uc-assets-activepath .uc-pathname").text(".."+path);
	}
	
	
	/**
	 * check some item
	 */
	function selectItem(objItem, isCheck){
		
		//skip unselectable item
		if(objItem.hasClass("uc-filelist-selectable") == false)
			return(true);
		
		var objCheckbox = objItem.find(".uc-filelist-checkbox");
		
		if(isCheck == true)
			objItem.addClass("uc-filelist-item-selected");
		else
			objItem.removeClass("uc-filelist-item-selected");
		
		if(objCheckbox.length != 0){
			objCheckbox.prop('checked', isCheck);
		}
		
		triggerEvent(events.SELECT_ITEM, [objItem, isCheck]);
		
		
	}
	
	
	/**
	 * select single item
	 */
	function selectSingleItem(objItem){
		
		if(objItem.hasClass("uc-filelist-selectable") == false)
			return(true);
		
		unselectAllItems(objItem);
		selectItem(objItem, true);
	}

	
	/**
	 * toggle item selection
	 */
	function toggleItemSelection(objItem){
		
		var isSelected = isItemSelected(objItem);
		
		if(isSelected == false)
			selectItem(objItem, true);
		else
			selectItem(objItem, false);
	}
	
	
	/**
	 * select all items
	 */
	function selectAllItems(){
		
		var objItems = getObjItems();
		objItems = objItems.filter(".uc-filelist-selectable");
		
		jQuery(objItems).each(function(index, item){
			var objItem = jQuery(item);
			selectItem(objItem, true);
		});
		
	}
	
	
	/**
	 * unselect all items
	 */
	function unselectAllItems(objExcept){
		
		var objItems = getObjSelectedItems();
		
		if(objExcept)
			objItems = objItems.not(objExcept);
		
		jQuery(objItems).each(function(index, item){
			var objItem = jQuery(item);
			selectItem(objItem, false);
		});
		
	}

	
	/**
	 * on delete items click, delete selected items
	 */
	function deleteSelectedFiles(){
		
		var arrFiles = getArrSelectedFiles();
		
		if(arrFiles.length == 0){
			alert("No Files Chosen");
			return(false);
		}
		
		var numFiles = arrFiles.length;
		
		var message = "Do you sure you want to delete "+ numFiles+ " files?";
		if(confirm(message) == false)
			return(false);
		
		//mark selected files with "deleting class"
		var selectedItems = g_objFileList.find(".uc-filelist-item-selected");
		
		selectedItems.addClass("uc-filelist-item-deleting");
		selectedItems.removeClass("uc-filelist-item-selected");
		
		g_objWrapper.find(".uc-button-delete-file").hide();
		g_objWrapper.find(".uc-preloader-deleting").show();
		
		assetsAjaxRequest("assets_delete_files", {arrFiles: arrFiles, path: g_activePath, pathkey:g_pathKey}, function(response){
			
			var htmlList = response.html;
			g_objFileList.html(htmlList);
			
			g_objWrapper.find(".uc-preloader-deleting").hide();
			g_objWrapper.find(".uc-button-delete-file").show().addClass("button-disabled");
			
			triggerEvent(events.CHANGE_FILELIST);
			triggerEvent(events.UPDATE_FILES);
		});
	
	}
	
	
	function ____________UPLOAD______________(){};
	
	
	/**
	 * upload dialog on close
	 */
	function uploadDialogOnClose(){
		
		var objDialog = jQuery("#uc_dialog_upload_files");
		var objManager = objDialog.data("objManager");
		var objDropzone = objDialog.data("dropzone");
		
		var isNeedRefresh = objDialog.data("needRefresh");
		
		if(isNeedRefresh === true)
			objManager.refreshQuite();
		
		objDropzone.removeAllFiles();
		jQuery("#uc_dialog_upload_files").dialog("close");
		
	}
	
	/**
	 * open upload dialog, may occur from different managers
	 */
	function openDialogUpload(objManager){
		
		var objDialog = jQuery("#uc_dialog_upload_files");
		
		objDialog.data("manager", objManager);
		
		var buttonOpts = {};
		
		buttonOpts["Close"] = uploadDialogOnClose; 
		
		objDialog.dialog({
			dialogClass:"unite-ui",			
			buttons:buttonOpts,
			minWidth:960,
			modal:true,
			close:uploadDialogOnClose,
			open:function(){

				objDialog.data("needRefresh", false);
				
				var activePath = objManager.getActivePath();
				
				jQuery("#uc_dialogupload_activepath").html(activePath);
				jQuery("#uc_input_upload_path").val(activePath);
				jQuery("#uc_input_pathkey").val(g_pathKey);
			}
		});
		
	}
	
	/**
	 * init upload files dialog on assets tab
	 */
	function initUploadFilesDialog(){
		
		var objDialog = jQuery("#uc_dialog_upload_files");
		if(objDialog.length == 0)
			return(false);
		
		var objDropzone = objDialog.data("dropzone");
		if(objDropzone)
			return(false);		//allow init only once
		
		//init dropzone
		try{
			Dropzone.autoDiscover = false;
			var objDropzone = new Dropzone("#uc_form_dropzone");
		}catch(error){
			
			var strError = error + " Some other plugin loading Dropzone in this page. <br> Please check the console wich of them, and turn it off. They should load dropzone library only in their page.";
			
			g_ucAdmin.showErrorMessage(strError);
						
			throw error;
			
		}
		objDialog.data("dropzone", objDropzone);
		
		objDropzone.on("addedfile", function(file,second) {
			
			triggerEvent(events.UPDATE_FILES);
			objDialog.data("needRefresh", true);
			
		});
		 
		objDropzone.on("queuecomplete", function(file) {
			
			var objManager = objDialog.data("manager");
			if(!objManager)
				throw new Error("assets manager not found, something wrong.");
			
			objManager.refreshQuite();
			
			objDialog.data("needRefresh", false);
		 });		
		
	}
	
	
	function ____________CREATE_FOLDER______________(){};
	
	
	/**
	 * open create folder dialog
	 */
	function openCreateFolderDialog(){
		
		var objDialog = jQuery("#uc_dialog_create_folder");
		if(objDialog.length == 0)
			throw new Error("The create folder dialog must be here");
		
		//init fields
		jQuery("#uc_dialog_create_folder_name").val("");
		
		//oepn dialog
		g_ucAdmin.openCommonDialog(objDialog);
		
	}

	
	/**
	 * create folder
	 */
	function createFolder(){
		
		var folderName = jQuery("#uc_dialog_create_folder_name").val();
		
		var data = {
				"pathkey":g_pathKey,
				"path":g_activePath,
				"folder_name":folderName
			};
		
		data = modifyDataBeforeAjax(data);
		
		g_ucAdmin.dialogAjaxRequest("uc_dialog_create_folder", "assets_create_folder", data, function(response){
			g_objFileList.html(response.html);
			
			triggerEvent(events.CHANGE_FILELIST);
			triggerEvent(events.UPDATE_FILES);
			
		});
		
	}
	
	
	/**
	 * init create folder actions
	 */
	function initCreateFolderActions(){
		jQuery("#uc_dialog_create_folder_action").on("click",createFolder);
		jQuery("#uc_dialog_create_folder_name").doOnEnter(createFolder);
	}
	
	
	function ____________CREATE_FILE______________(){};

	/**
	 * open create folder dialog
	 */
	function openCreateFileDialog(){
		
		var objDialog = jQuery("#uc_dialog_create_file");
		if(objDialog.length == 0)
			throw new Error("The create file dialog must be here");
		
		//init fields
		jQuery("#uc_dialog_create_file_name").val("");
		
		//open dialog
		g_ucAdmin.openCommonDialog(objDialog);
		
	}

	
	/**
	 * create folder
	 */
	function createFile(){
		
		var fileName = jQuery("#uc_dialog_create_file_name").val();
		
		var data = {
				"pathkey":g_pathKey,
				"path":g_activePath,
				"filename":fileName
			};
		
		data = modifyDataBeforeAjax(data);

		g_ucAdmin.dialogAjaxRequest("uc_dialog_create_file", "assets_create_file", data, function(response){
			g_objFileList.html(response.html);
			
			triggerEvent(events.CHANGE_FILELIST);
			triggerEvent(events.UPDATE_FILES);
		});
		
	}
	
	
	/**
	 * init create folder actions
	 */
	function initCreateFileActions(){
		
		jQuery("#uc_dialog_create_file_action").on("click",createFile);
		
		jQuery("#uc_dialog_create_file_name").doOnEnter(createFile);
	}
	
	
	function ____________SELECT_ALL______________(){};
	
	
	/**
	 * update select all button state
	 */
	function updateSelectAllButtonState(){
		
		var objButton = g_objPanel.find(".uc-button-select-all");
		
		var numItems = getNumItems();
		
		if(numItems == 0){
			objButton.addClass("button-disabled");
			
			objButton.html(objButton.data("textselect"));
			return(false);
		}
		
		objButton.removeClass("button-disabled");
		
		var numSelected = getNumSelectedItems();
		if(numSelected != numItems){
			objButton.html(objButton.data("textselect"));
		}else{
			objButton.html(objButton.data("textunselect"));
		}
		
	}
	
	
	/**
	 * select / unselect all files
	 */
	function selectUnselectAll(){
		var objUnselectedItems = getObjUnselectedItems();
		if(objUnselectedItems.length != 0)
			selectAllItems();
		else
			unselectAllItems();
		
	}
	
	
	function ____________EDIT_FILE______________(){};
	
	
	/**
	 * on edit dialog open
	 */
	function onEditDialogOpen(item){
		
		var objTextarea = jQuery("#uc_dialog_edit_file_textarea");
		
		if(g_codeMirror)
			g_codeMirror.toTextArea();

		objTextarea.hide();
		
		var data = {filename: item.file, path: g_activePath, pathkey: g_pathKey};
		g_ucAdmin.setErrorMessageID("uc_dialog_edit_file_error");
		g_ucAdmin.setAjaxLoaderID("uc_dialog_edit_file_loader");
		assetsAjaxRequest("assets_get_file_content", data, function(response){
			
			objTextarea.show();
			objTextarea.val(response.content);
		    
			var modeName;
			
			switch(item.type){
				default:
				case "html":
					modeName = "htmlmixed";
				break;
				case "xml":
					modeName = "xml";
				break;
				case "css":
					modeName = "css";
				break;
				case "javascript":
					modeName = "javascript";
				break;
			}
			
			var optionsCM = {
					mode: {name: modeName },
					lineNumbers: true
			 };
			
			g_codeMirror = CodeMirror.fromTextArea(objTextarea[0], optionsCM);
			
		});
		
	}
	
	
	/**
	 * on edit dialog save functionality
	 */
	function onEditDialogSave(){
		
		if(!g_codeMirror)
			throw new Error("Codemirror editor not found");
		
		var content = g_codeMirror.getValue();
		var objDialog = jQuery("#uc_dialog_edit_file");
		
		var item = objDialog.data("item");
		
		var data = {filename: item.file, path: g_activePath, pathkey: g_pathKey, content: content};
		
		g_ucAdmin.setAjaxLoaderID("uc_dialog_edit_file_loadersaving");
		g_ucAdmin.setErrorMessageID("uc_dialog_edit_file_error");
		g_ucAdmin.setSuccessMessageID("uc_dialog_edit_file_success");
		
    assetsAjaxRequest('assets_save_file', data, function () {
      triggerEvent(events.UPDATE_FILES);
    });
		
	}
	
	
	/**
	 * open edit file dialog from selected file
	 */
	function openEditFileDialog(){
		var item = getSelectedSingleItem();

		var objDialog = jQuery("#uc_dialog_edit_file");

		var buttonOpts = {};
		
		buttonOpts[g_uctext.close] = function(){
			objDialog.dialog("close");
		};

		buttonOpts[g_uctext.save] = function(){
			onEditDialogSave();
		};
		
		var dialogTitle = g_uctext.edit_file+": "+item.file;
		
		objDialog.data("item", item);
		
		var dialogExtendOptions = {
			      "closable" : true,
			      "minimizable" : true,
			      "maximizable" : true,
			      "collapsable" : true
			    };
		
		
		objDialog.dialog({
			dialogClass:"unite-ui",			
			buttons:buttonOpts,
			minWidth:"1000",
			minHeight:550,
			title: dialogTitle,
			modal:false,
			open:function(){
				onEditDialogOpen(item);
			}
		}).dialogExtend(dialogExtendOptions);
		
		
	}
	
	function ____________MOVE_FILES______________(){};
	
	
	/**
	 * get path for copy / move, this folder or parent
	 */
	function getPathForCopyMove(){
		var path = g_activePath;
		var objDirs = getObjChildDirs();
		var numDirs = objDirs.length;
		if(objDirs.length == 0)
			path = getParentFolder(path);
		
		return(path);
	}
	
	
	/**
	 * set path for move dialog
	 */
	function dialogMoveSetPath(pathMove){
		
		jQuery("#uc_dialog_move_files_url").html(pathMove).data("path", pathMove);
	
		var objButton = jQuery("#uc_dialog_move_files_action");
		
		var objDialog = jQuery("#uc_dialog_move_files");
		var basePath = objDialog.data("base_path");
		
		//disable / enable action button
		if(pathMove === basePath)
			objButton.addClass("button-disabled");
		else
			objButton.removeClass("button-disabled");
		
	}
	
	
	/**
	 * open move files dialog
	 */
	function openMoveFilesDialog(){
				
		var options = {
			minWidth:700
		};
		
		g_ucAdmin.openCommonDialog("uc_dialog_move_files", function(){
			
			var objDialog = jQuery("#uc_dialog_move_files");
			
			//save init data
			
			objDialog.data("base_path", g_activePath);
			
			var arrFiles = getArrSelectedFiles();
			var numFiles = arrFiles.length;
			
			if(numFiles == 0)
				return(false);
			
			objDialog.data("arr_files", arrFiles);
			
			//set move path and load path
			var pathMove = getPathForCopyMove();
			dialogMoveSetPath(pathMove);
			
			//update label text
			var objLabel = objDialog.find("#uc_dialog_move_label");
			
			var labelText = objLabel.data("text");
			labelText = labelText.replace("%1",numFiles);
			
			objLabel.html(labelText+":");
			
			g_objBrowserMove.loadPath(pathMove, true);
		}, options);
		
	}
	
	
	/**
	 * do dialog mvoe files request
	 */
	function dialogMoveFilesRequest(actionOnExists){
		
		var objDialog = jQuery("#uc_dialog_move_files");
		
		var arrFiles = objDialog.data("arr_files");
		var basePath = objDialog.data("base_path");
		
		var data = {
				pathkey: g_pathKey,
				pathSource: basePath,
				arrFiles: arrFiles,
				pathTarget: jQuery("#uc_dialog_move_files_url").data("path")
		};

		if(actionOnExists)
			data.actionOnExists = actionOnExists;
		
		jQuery("#uc_dialog_move_files_actions_wrapper").show();
		jQuery("#uc_dialog_move_message").hide();
		
		var dialogID = "uc_dialog_move_files";
		g_ucAdmin.setAjaxLoaderID(dialogID + "_loader");
		g_ucAdmin.setErrorMessageID(dialogID + "_error");
		g_ucAdmin.setAjaxHideButtonID(dialogID + "_action");
		
		var objSuccessMessage = jQuery("#"+dialogID + "_success");
		
		assetsAjaxRequest("assets_move_files", data, function(response){
			
			//of not moved
			if(response.hasOwnProperty("done") && response.done === false){
				
				jQuery("#uc_dialog_move_files_actions_wrapper").hide();
				jQuery("#uc_dialog_move_message").show();
				jQuery("#uc_dialog_move_message_text").html(response.message);
				
				
			}else{
				
				//if successfully moved
				objSuccessMessage.html(response.message);
				g_objFileList.html(response.html);
				
				jQuery("#"+dialogID).dialog("close");

				triggerEvent(events.CHANGE_FILELIST);
				triggerEvent(events.UPDATE_FILES);
			}
			
		});
		
	}
	
	
	/**
	 * init move dialog actions
	 */
	function initMoveFileActions(){
		
		var objDialogMove = jQuery("#uc_dialog_move_files");
				
		//init move dialog folder browser 
		var objBrowserMoveWrapper = jQuery("#uc_movefile_browser");
		
		g_objBrowserMove = new UCAssetsManager();
		g_objBrowserMove.init(objBrowserMoveWrapper);
		
		//on fielist change
		g_objBrowserMove.eventOnUpdateFilelist(function(){
			var path = g_objBrowserMove.getActivePath();
			dialogMoveSetPath(path);
		});
		
		//on checkbox select
		g_objBrowserMove.eventOnSelectOperation(function(){
			
			var arrItems = g_objBrowserMove.getArrSelectedItems();
			var numItems = arrItems.length;
			
			if(numItems > 1)
				throw new Error("number of selected items can be 1 or 0");
			
			if(numItems == 0){
				var path = g_objBrowserMove.getActivePath();
			}else{
				var objItem = arrItems[0];
				var path = objItem.filepath;
			}

			dialogMoveSetPath(path);
			
		});
		
		//move files action
		jQuery("#uc_dialog_move_files_action").on("click",function(){
			
			if(jQuery(this).hasClass("button-disabled"))
				return(false);
			
			dialogMoveFilesRequest();
		});
		
		//onExists button click
		objDialogMove.find(".uc-dialog-move-message .unite-button-secondary").on("click",function(){
			var action = jQuery(this).data("action");
			
			if(action == "cancel"){		//return to initial position
				jQuery("#uc_dialog_move_files_actions_wrapper").show();
				jQuery("#uc_dialog_move_message").hide();
				jQuery("#uc_dialog_move_files_action").show();
			}else{
				dialogMoveFilesRequest(action);
			}
		});
	}
	
	function ____________RENAME_FILES______________(){}

	
	/**
	 * open rename files dialog
	 */
	function openRenameFileDialog(){
		
		var objDialog = jQuery("#uc_dialog_rename_file");
		if(objDialog.length == 0)
			throw new Error("The rename file dialog must be here");
		
		var objItem = getSelectedSingleItem();
		var filename = objItem.file;
		
		jQuery("#uc_dialog_rename_file_input").val(filename).select();
		
		//open dialog
		g_ucAdmin.openCommonDialog(objDialog);
		
	}
	
	/**
	 * create folder
	 */
	function renameFile(){
		
		var fileName = jQuery("#uc_dialog_rename_file_input").val();
		
		var objFile = getSelectedSingleItem();
		
		var data = {
				"pathkey":g_pathKey,
				"path":g_activePath,
				"filename":objFile.file,
				"filename_new":fileName
			};
		
		data = modifyDataBeforeAjax(data);
		
		g_ucAdmin.dialogAjaxRequest("uc_dialog_rename_file", "assets_rename_file", data, function(response){
			g_objFileList.html(response.html);
			
			triggerEvent(events.CHANGE_FILELIST);
			triggerEvent(events.UPDATE_FILES);
		});
		
	}
	
	
	/**
	 * init create folder actions
	 */
	function initRenameFileActions(){
		
		jQuery("#uc_dialog_rename_file_action").on("click",renameFile);
		
		jQuery("#uc_dialog_rename_file_input").doOnEnter(renameFile);
	}
	
	function ____________VIEW_FILES______________(){}
	
	/**
	 * run view files
	 */
	function runViewFile(){
		
		var objItem = getSelectedSingleItem();
		
		var fullUrl = g_ucAdmin.getVal(objItem, "full_url");
		
		if(!fullUrl){
			alert("view not available");
			return(false);
		}
		
		window.open(fullUrl);
	}
	
	
	function ____________UNZIP______________(){}
		
	
	/**
	 * unzip selected file
	 */
	function unzipSelectedFile(){
		var item = getSelectedSingleItem();
		
		var data = {pathkey:g_pathKey, path: g_activePath, filename: item.file};
		
		var objLoader = g_objPanel.find(".uc-preloader-unzip");
		objLoader.show();
		
		assetsAjaxRequest("assets_unzip_file",data,function(response){
			objLoader.hide();
			g_objFileList.html(response.html);
			
			triggerEvent(events.CHANGE_FILELIST);
			triggerEvent(events.UPDATE_FILES);
		});
		
	}
	
	function ____________ACTIONS_PANEL______________(){};
	
	
	/**
	 * check operations buttons
	 */
	function checkActionPanelButtons(){
		
		if(g_objPanel.length == 0)
			return(false);
		
		var buttonsSingle = g_objPanel.find(".uc-relate-single");
		var buttonsMultiple = g_objPanel.find(".uc-relate-multiple");
		var buttonsAll = buttonsSingle.add(buttonsMultiple);
		var buttonSpecial = g_objPanel.find(".uc-relate-special");
		var buttonsFilesOnly = g_objPanel.find(".uc-relate-file");
		
		var numSelected = getNumSelectedItems();
		
		if(numSelected == 0){
			g_ucAdmin.disableButton(buttonsAll);
			g_ucAdmin.disableButton(buttonSpecial);
		}
		else
			if(numSelected == 1){	//single mode
								
				var item = getSelectedSingleItem();
				var itemType = item.type;
				var isDir = (itemType == "dir");
				
				//set buttons for disable
				var buttonsDisable = null;
				if(isDir == true)
					buttonsDisable = buttonsFilesOnly;
				
				//remove the disable buttons from the enable list
				if(buttonsDisable)
					buttonsAll = buttonsAll.not(buttonsDisable);
				
				g_ucAdmin.enableButton(buttonsAll);
				
				if(buttonsDisable)
					g_ucAdmin.disableButton(buttonsDisable);
				
				//enable type related buttons
				var classType = ".uc-relate-type-" + itemType;
				var buttonsType = g_objPanel.find(classType);
				
				g_ucAdmin.enableButton(buttonsType);
			}
			else{	//multiple mode - single buttons disabled
				g_ucAdmin.disableButton(buttonsSingle);
				g_ucAdmin.disableButton(buttonSpecial);
				
				g_ucAdmin.enableButton(buttonsMultiple);
			}
		
		//update select panel button
		updateSelectAllButtonState();
	}
	
	
	/**
	 * run some action
	 */
	function runAction(action){
		
		if(g_temp.isBrowser == true){
			switch(action){
				case "select_all":
					selectUnselectAll();
				break;
				default:
					trace("wrong browser action: " + action);
				break;
			}
			return(false);
		}
		
		//do manager mode actions
		switch(action){
			case "select_all":
				selectUnselectAll();
			break;
			case "delete":
				deleteSelectedFiles();
			break;
			case "upload":
				openDialogUpload(t);
			break;
			case "create_file":
				openCreateFileDialog();
			break;
			case "create_folder":
				openCreateFolderDialog();
			break;
			case "edit":
				openEditFileDialog();
			break;
			case "move":
				openMoveFilesDialog();
			break;
			case "unzip":
				unzipSelectedFile();
			break;
			case "rename":
				openRenameFileDialog();
			break;
			case "view":
				runViewFile();
			break;
			default:
				trace("wrong action: " + action);
			break;
		}
	}
	
	
	/**
	 * init actions panel
	 */
	function initActionsPanel(){
		
		g_objPanel = g_objWrapper.find(".uc-assets-buttons-panel");
		
		if(g_objPanel.length == 0)
			return(false);
		
		/**
		 * on buttons click - run action
		 */
		g_objPanel.find("a.uc-panel-button").on("click",function(){
			var objButton = jQuery(this);
			if(objButton.hasClass("button-disabled"))
				return(false);
			
			var action = jQuery(this).data("action");
			runAction(action);
		});
		
		
		//init global events
		onEvent(events.SELECT_ITEM, function(){
			checkActionPanelButtons();
		});
		
		onEvent(events.CHANGE_FILELIST, function(){
			checkActionPanelButtons();
		});
		
	}
	
	
	
	function ____________INIT______________(){};
	
	
	/**
	 * uncheck all assets checkboxes
	 */
	function uncheckOnInit(){
		
		var objCheckboxes = g_objWrapper.find(".uc-filelist-checkbox");

		objCheckboxes.each(function(){
			var checkbox = jQuery(this);
			var initChecked = checkbox.data("initchecked");
			
			if(!initChecked)
				checkbox.prop('checked', false);
		});
		
	}
	
	
	/**
	 * edit manager mode
	 */
	function initManagerMode(){
		
		//init dropzone only once
		initUploadFilesDialog();
		
		initCreateFolderActions();
		initCreateFileActions();
		initMoveFileActions();
		initRenameFileActions();
		
		initActionsPanel();
		
	}

	
	/**
	 * validate that the manager has put only once
	 */
	function validateManagerPutOnce(){
		var isManagerPut = jQuery.data( document.body, "uc-manager-put-once");
		if(isManagerPut === true)
			throw new Error("The file manager can't be put twice to the page");

		jQuery.data( document.body, "uc-manager-put-once", true );
	}
	
	
	/**
	 * init options
	 */
	function initOptions(){
		
		var objOptions = g_objWrapper.data("options");
				
		if(typeof objOptions != "object")
			throw new Error("The input options are not object");
				
		g_options = jQuery.extend(g_options, objOptions);
				
	}
	
	
	/**
	 * init the assets
	 */
	function init(){
		
		g_activePath = g_objWrapper.data("path");
		g_startPath = g_objWrapper.data("startpath");
				
		g_temp.isBrowser = g_objWrapper.data("isbrowser");
		g_temp.isBrowser = g_ucAdmin.strToBool(g_temp.isBrowser);
		
		g_pathKey = g_objWrapper.data("pathkey");
		g_objFileList = g_objWrapper.find(".uc-filelist");
		g_objErrorFilelist = g_objWrapper.find(".uc-filelist-error");

		
		initOptions();
		
		if(g_temp.isBrowser === false){
			
			validateManagerPutOnce();
			
			initManagerMode();
		}
		
		uncheckOnInit();
		
		initEvents();
		
		//triger change filelist change event
		triggerEvent(events.CHANGE_FILELIST);
	}
	
	function ____________EVENTS______________(){};

	
	/**
	 * on assets click event, do operations according item
	 */
	function onItemClick(){
				
		var objItem = jQuery(this);
		
		//protection against double event handling
		var isBelongs = isObjectBelongsToParent(objItem);
		if(isBelongs == false)
			return(true);
		
		var type = objItem.data("type");
		var file = objItem.data("file");
		
		if(type == "dir"){
			t.loadPath(file);
			return(false);
		}
		
		//on filename click:
		
		//if browser mode - then do identical to checkbox click
		if(g_temp.isBrowser == true && g_options.single_item_select == false)
			toggleItemSelection(objItem);
		else
			selectSingleItem(objItem);
		
		var isSelected = isItemSelected(objItem);
		triggerEvent(events.SELECT_OPERATION, [objItem, isSelected]);
		
	}
	
	/**
	 * check if some object belongs to it's parent 
	 * to avoid double event handling
	 */
	function isObjectBelongsToParent(obj){
		
		var objParent = obj.parents(".uc-assets-wrapper");
		var parentID = objParent.attr("id");
		var wrapperID = t.getID();
		
		if(parentID == wrapperID)
			return(true);
		
		return(false);
	}
	
	
	/**
	 * on checkbox click
	 */
	function onCheckboxClick(event){
		
		event.stopPropagation();
		
		var objCheckbox = jQuery(this);
		
		var isBelongs = isObjectBelongsToParent(objCheckbox);
		
		if(isBelongs == false)
			return(true);
		
		var isChecked = objCheckbox.is(":checked");
		
		var objItem = objCheckbox.parents(".uc-filelist-item");
		
		if(g_options.single_item_select == true){
			if(isChecked == false)
				selectItem(objItem, false);	//unselect item
			else
				selectSingleItem(objItem);	//select single item
		}
		else
			selectItem(objItem, isChecked);
		
		triggerEvent(events.SELECT_OPERATION, [objItem, isChecked]);

	}
	
	
	/**
	 * trigger internal event
	 */
	function triggerEvent(eventName, params){
		if(!params)
			var params = null;
		
		g_objWrapper.trigger(eventName, params);
	}
	
	
	/**
	 * on internal event
	 */
	function onEvent(eventName, func){
		g_objWrapper.on(eventName, func);
	}
	
	
	/**
	 * init events
	 */
	function initEvents(){
				
		g_objFileList.on("click", "input.uc-filelist-checkbox", onCheckboxClick);
		g_objFileList.on("click", "a.uc-filelist-item", onItemClick);
		
		//on select item event
		onEvent(events.SELECT_OPERATION, function(event, item, isChecked){
			
			var objItem = jQuery(item);
			
			if(typeof g_temp.funcOnSelectOperation == "function"){
				var itemData = getItemData(objItem);
				g_temp.funcOnSelectOperation(isChecked, itemData);
			}
			
		});
		
		
		//on update files event
		onEvent(events.UPDATE_FILES, function(){
			
			if(typeof g_temp.funcOnUpdateFiles == "function")
				g_temp.funcOnUpdateFiles();
			
		});
		
		
	}
	
	
	/**
	 * get arr selected items bridge
	 */
	this.getArrSelectedItems = function(){
		return getArrSelectedItems();
	}
	
	/**
	 * check by urls
	 */
	this.checkByUrls = function(arrUrls){
		var arrItems = getArrItems();

		jQuery(arrItems).each(function(index, data){
			var url = data.full_url;
			var found = (jQuery.inArray(url, arrUrls) != -1);
			selectItem(data.objItem, found);
		});
	
	}
	
	
	/**
	 * get active path
	 */
	this.getActivePath = function(){
		
		return(g_activePath);
	}
	
	
	/**
	 * get relative activepath
	 */
	this.getActivePathRelative = function(){
		
		var pathRelative = g_activePath.replace(g_startPath, ""); 
		pathRelative = g_ucAdmin.stripPathSlashes(pathRelative);
		
		return(pathRelative);
	}
	
	
	/**
	 * return if activepath is start path
	 */
	this.isStartPath = function(){
		
		var isStart = (g_activePath == g_startPath);
		return(isStart);
	}
	
	/**
	 * set custom start path
	 */
	this.setCustomStartPath = function(path){
		g_options.custom_startPath = path;
	}
	
	
	/**
	 * modify data before ajax
	 */
	function modifyDataBeforeAjax(data){
		
		if(!data)
			data = {};
		
		if(g_options.addon_id)
			data["addonID"] = g_options.addon_id;
		
		return(data);
	}
	
	
	/**
	 * call ajax request and add data
	 */
	function assetsAjaxRequest(action, data, funcSuccess){
		
		data = modifyDataBeforeAjax(data);
		
		g_ucAdmin.ajaxRequest(action, data, funcSuccess);
	}
	
	
	/**
	 * load assets dir
	 */
	this.loadPath = function(file, byPath, quiteMode){
		
		if(!quiteMode)
			var quiteMode = false;
		
		//choose small circle in active bar, or filelist preloader
		var preloaderID = ".uc-preloader-filelist";
		if(quiteMode == true)
			preloaderID = ".uc-preloader-refreshpath";
				
		if(!file){
			var path = g_activePath;
		}else{

			if(byPath === true){
				var path = file;
			}else{	//load by file
				var path = getPathByFile(file);
			}
			
		}
		
		
		if(!path)
			throw new Error("empty path");
		
		
		//show preloader, hide filelist
		var objPreloader = g_objWrapper.find(preloaderID);
				
		if(objPreloader)
			objPreloader.show();
		
		if(quiteMode == false)
			g_objFileList.hide();
		
		//update active path
		updateActivePath(path);
		
		var data = {path: path, pathkey: g_pathKey};
		if(g_temp.funcOnAjaxLoadPath)
			data = g_temp.funcOnAjaxLoadPath(data);
		
		if(g_options.custom_startPath != null)
			data.startpath = g_options.custom_startPath;
		
		//request path change
		g_objErrorFilelist.hide();
		g_ucAdmin.setErrorMessageID(g_objErrorFilelist);
		assetsAjaxRequest("assets_get_filelist", data, function(response){
		
			if(objPreloader)
				objPreloader.hide();
			
			var htmlList = response.html;
			g_objFileList.html(htmlList);
			
			if(quiteMode == false)
				g_objFileList.show();
			
			triggerEvent(events.CHANGE_FILELIST);
		});
		
	}
	
	/**
	 * silent refresh - without visible loader show
	 */
	this.refreshQuite = function(){
		t.loadPath(null, null, true);
	}
	
	
	/**
	 * init the assets manager
	 */
	this.init = function(objWrapper){
		
		g_objWrapper = objWrapper;
		
		if(g_objWrapper.length == 0)
			throw new Error("Can't find assets wrapper");
		
		if(g_objWrapper.hasClass("uc-assets-wrapper") == false)
			throw new Error("Wrong assets manager wrapper");
		
		//check startup error
		var startupErrorWrapper = g_objWrapper.find(".uc-assets-startup-error");
		if(startupErrorWrapper.length !== 0)
			return(false);
		
		init();
	}
	
	
	/**
	 * get assets manager ID
	 */
	this.getID = function(){
		
		var id = g_objWrapper.attr("id");
		return(id);
	}
	
	
	/**
	 * set function on ajax load path, to add additional fields
	 */
	this.eventOnAjaxLoadpath = function(func){
		g_temp.funcOnAjaxLoadPath = func;
	}
	
	
	/**
	 * set function that run after update filelist
	 */
	this.eventOnUpdateFilelist = function(func){
		onEvent(events.CHANGE_FILELIST, func);
	}
	
	
	/**
	 * set function on update files
	 */
	this.eventOnUpdateFiles = function(func){
		g_temp.funcOnUpdateFiles = func;
	}
	
	/**
	 * on operatio select event
	 */
	this.eventOnSelectOperation = function(func){
		g_temp.funcOnSelectOperation = func;		
	}
	
	/**
	 * get array of items by filename
	 */
	this.getItemByFilename = function(filename){
		
		return getItemByFilename(filename);
	}
	
}
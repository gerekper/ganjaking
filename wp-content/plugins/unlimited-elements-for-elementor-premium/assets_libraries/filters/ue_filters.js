
function UEDynamicFilters(){
	
	var g_objFilters, g_objGrid, g_filtersData, g_urlBase;
	var g_urlAjax, g_lastGridAjaxCall, g_cache = {}, g_objBody;
	var g_remote = null, g_lastSyncGrids;
	
	var g_showDebug = false;
	
	var g_types = {
		PAGINATION:"pagination",
		LOADMORE:"loadmore",
		TERMS_LIST:"terms_list",
		SEARCH: "search",
		SELECT: "select",
		SUMMARY: "summary"
	};
	
	var g_vars = {
		CLASS_DIV_DEBUG:"uc-div-ajax-debug",
		CLASS_GRID:"uc-filterable-grid",		
		CLASS_GRID_NOREFRESH:"uc-grid-norefresh",	//grid that will not refresh
		DEBUG_AJAX_OPTIONS: false,
		CLASS_CLICKED:"uc-clicked",
		CLASS_HIDDEN: "uc-filter-hidden",	//don't refresh with this class
		CLASS_INITING: "uc-filter-initing",
		CLASS_INITING_HIDDEN: "uc-initing-filter-hidden",
		CLASS_REFRESH_SOON: "uc-ajax-refresh-soon",
		EVENT_SET_HTML_ITEMS: "uc_ajax_sethtml",
		
		EVENT_BEFORE_REFRESH: "uc_before_ajax_refresh",	   //on grid
		EVENT_AJAX_REFRESHED: "uc_ajax_refreshed",	   //on grid
		EVENT_AJAX_REFRESHED_BODY: "uc_ajax_refreshed_body",	   //on grid
		EVENT_INIT_FILTER:"init_filter",
		ACTION_REFRESH_GRID: "uc_refresh",	//listen on grid
		ACTION_GET_FILTERS_URL: "uc_get_filters_url",	//listen on grid
		
		REFRESH_MODE_PAGINATION: "pagination",
		REFRESH_MODE_LOADMORE: "loadmore",
		trashold_handle:null
	};
	
	var g_options = {
		is_cache_enabled:true,
		ajax_reload: false,
		widget_name: null
	};
	
	
	/**
	 * console log some string
	 */
	function trace(str){
		console.log(str);
	}
	
	function ________GENERAL_______________(){}
	
		
	
	/**
	 * add url param
	 */
	function addUrlParam(url, param, value){
		
		if(url.indexOf("?") == -1)
			url += "?";
		else
			url += "&";
		
		if(typeof value == "undefined")
			url += param;
		else	
			url += param + "=" + value;
		
		return(url);
	}
	
	
	/**
	 * get object property
	 */
	function getVal(obj, name, defaultValue){
		
		if(!defaultValue)
			var defaultValue = "";
		
		var val = "";
		
		if(!obj || typeof obj != "object")
			val = defaultValue;
		else if(obj.hasOwnProperty(name) == false){
			val = defaultValue;
		}else{
			val = obj[name];			
		}
		
		return(val);
	}
	
	/**
	 * turn string value ("true", "false") to string 
	 */
	function strToBool(str){
		
		switch(typeof str){
			case "boolean":
				return(str);
			break;
			case "undefined":
				return(false);
			break;
			case "number":
				if(str == 0)
					return(false);
				else 
					return(true);
			break;
			case "string":
				str = str.toLowerCase();
						
				if(str == "true" || str == "1")
					return(true);
				else
					return(false);
				
			break;
		}
		
		return(false);
	};
	
	/**
	 * get offsets distance
	 */
	function getOffsetsDistance(offset1, offset2){
	  
	  var dx = offset2.left-offset1.left;
	  var dy = offset2.top-offset1.top;
	  
	  return Math.sqrt(dx*dx+dy*dy); 
	}
	
	
	/**
	 * get closest object by offset
	 */
	function getClosestByOffset(objParents, objElement, isVertical){
		
		if(objParents.length == 0){
			throw new Error("get closest by offset error - grids not found");
		}
		
		
		if(g_showDebug == true){
			
			trace("get closest grids for");
			trace(objElement)
			trace("parents");
			trace(objParents);
		}
		
		var objClosest = null;
		var minDiff = 1000000;
		
		var elementOffset = objElement.offset();
		
		jQuery.each(objParents, function(index, parent){
			
			var objParent = jQuery(parent);
			
			var objGrid = jQuery(parent);	//return this one
			
			var distance = 0;
			
			var isVisible = objParent.is(":visible");
			
			var constantHeight = null;
			
			if(isVisible == false){
				objParent = objParent.parent();
			}
			
			var parentOffset = objParent.offset();
			
			if(isVertical == true){
				
				var offsetY = elementOffset.top;
				var parentY = parentOffset.top;
				
				//get bottom of the parent
				if(parentY < offsetY)
					parentY += objParent.height();
				
				var distance = Math.abs(offsetY - parentY);
				
			}else{
				
				var parentOffset = objParent.offset();

				var distance = getOffsetsDistance(parentOffset, elementOffset);
			}
			
			if(g_showDebug == true){
				
				trace(objParent);
				trace("distance: " + distance);
				
				trace("is vertical: " + isVertical);
			}
			
			if(distance < minDiff){
				minDiff = distance;
				objClosest = objGrid;
			}
			
		});
		
		if(g_showDebug == true){
			
			trace("filter: ");
			trace(objElement);
			
			trace("Closest grid found:");
			trace(objClosest);
		}
		
		
		return(objClosest);
	}
	
	/**
	 * get all grids
	 */
	function getAllGrids(){
		
		var objGrids = jQuery("."+ g_vars.CLASS_GRID);
						
		return(objGrids);
	}
	
	/**
	 * get closest grid to some object
	 */
	/**
	 * get closest grid to some object
	 */
	function getClosestGrid(objSource){
		
		//in case there is only one grid - return it
		if(g_objGrid)
			return(g_objGrid);
		
		//in case there are nothing:
		var objGrids = getAllGrids();
		
		if(objGrids.length == 0)
			return(null);
		
		if(g_showDebug == true){
			
			trace("get closest grids");
			trace(objSource);
			trace(objGrids);
		}
		
		//get grid from current section
		
		var objSection = objSource.parents("section");
		
		var objGrid = objSection.find("."+ g_vars.CLASS_GRID);
		
		if(objGrid.length == 1)
			return(objGrid);
		
		//get closest by offset
		
		var objSingleGrid = getClosestByOffset(objGrids, objSource, true);
		
		if(objSingleGrid && objSingleGrid.length == 1)
			return(objSingleGrid);		
		
		//return first grid in the list
		
		var objFirstGrid = jQuery(objGrids[0]);
		
		return(objFirstGrid);
	}
	
	
	
	/**
	 * add filter object to grid
	 */
	function bindFilterToGrid(objGrid, objFilter){
		
		var arrFilters = objGrid.data("filters");
		var objTypes = objGrid.data("filter_types");
		
		if(!arrFilters)
			arrFilters = [];
		
		if(!objTypes)
			objTypes = {};
		
		var type = getFilterType(objFilter);
		
		//validate double types
		
		if(objTypes.hasOwnProperty(type)){
			
			switch(type){
				case g_types.LOADMORE:
					
					trace("Double filter not allowed");					
					trace("existing Filters:");
					trace(arrFilters);
					
					trace("Second Filter");
					trace(objFilter);
					
					trace("Grid:");
					trace(objGrid);
										
					showElementError(objFilter, "Double load more button for one grid not allowed")
					return(false);
				break;
			}
			
		}
		
		objTypes[type] = true;
				
		arrFilters.push(objFilter);
		
		//add init after filters
		var isInitAfter = objFilter.data("initafter");
		
		if(isInitAfter === true){
			
			var arrFiltersInitAfter = objGrid.data("filters_init_after");
			
			if(!arrFiltersInitAfter)
				arrFiltersInitAfter = [];
			
			arrFiltersInitAfter.push(objFilter);
			
			objGrid.data("filters_init_after", arrFiltersInitAfter);
		}
		
		objGrid.data("filters", arrFilters);
		objGrid.data("filter_types", objTypes);
		
		
	}
	
	/**
	 * 
	 * get element widget id from parent wrapper
	 */
	function getElementWidgetID(objElement){
		
		if(!objElement || objElement.length == 0)
			throw new Error("Element not found");
		
		//get widget id
		
		var objWidget = objElement.parents(".elementor-widget");
		
		if(objWidget.langth == 0)
			throw new Error("Element parent not found");
		
		var widgetID = objWidget.data("id");
		
		if(!widgetID)
			throw new Error("widget id not found");
		
		return(widgetID);
	}
	
	/**
	 * get the grid widget object from elementor element id
	 */
	function getGridFromElementorElementID(elementID){
		
		var objElement = g_objBody.find(".elementor-widget[data-id='"+elementID+"']");
		
		if(objElement.length == 0)
			return(null);
		
		var objGrid = objElement.find("."+g_vars.CLASS_GRID);
		
		if(objGrid.length != 1)
			return(null);
		
		return(objGrid);
	}
	
	
	
	/**
	 * get synced widget IDs
	 */
	function getSyncedWidgetData(objElement){
		
		if(!g_remote)
			return(false);
		
		var arrSyncedGrids = g_remote.getSyncedElements(objElement);
		
		if(!arrSyncedGrids)
			return(false);
		
		var arrWidgetIDs = [];
		
		var objGrids = null;
		
		for(var index in arrSyncedGrids){
			
			var objGrid = arrSyncedGrids[index];
			
			if(objGrid.hasClass(g_vars.CLASS_GRID) == false){
				
				var message = "Please enable ajax on all synced widgets";
				var message2 = "Please enable ajax on this synced widget";
				
				showElementError(objGrid, message2);
				
				showAjaxError(message);
				throw new Error(message);
				return(false);
			}
			
			var objWidgetID = getElementWidgetID(objGrid);
			
			//add to jquery collection
			
			if(!objGrids)
				objGrids = objGrid;
			else
				objGrids = objGrids.add(objGrid);
			
			arrWidgetIDs.push(objWidgetID);
		}
		
		var strWidgetIDs = arrWidgetIDs.toString();
		
		var objOutput = {};
		objOutput["ids"] = strWidgetIDs;
		objOutput["grids"] = objGrids;
		
		return(objOutput);
	}
	
	
	/**
	 * get element layout data
	 */
	function getElementLayoutData(objElement, addSyncedGrids){
		
		if(!objElement || objElement.length == 0)
			throw new Error("Element not found");
		
		//get widget id
		
		var objWidget = objElement.parents(".elementor-widget");
		
		if(objWidget.langth == 0)
			throw new Error("Element parent not found");
		
		var widgetID = objWidget.data("id");
		
		if(!widgetID)
			throw new Error("widget id not found");
		
		//get synced grids
		var objSyncedData = null;
		
		//add sync if allowed and available
		
		if(addSyncedGrids){
			
			var objSyncedData = getSyncedWidgetData(objElement);
			
			if(g_showDebug && objSyncedData){
				
				trace("sync data");
				trace(objSyncedData);
			}
		}else{
			
			if(g_showDebug)
				trace("skip sync grid");
			
		}

		
		//get layout id
		var objLayout = objWidget.parents(".elementor");
		
		if(objLayout.length == 0)
			throw new Error("layout not found");
		
		var layoutID = objLayout.data("elementor-id");
		
		var output = {};
		
		output["widgetid"] = widgetID;
		output["layoutid"] = layoutID;
		
		if(objSyncedData){
			output["synced_widgetids"] = objSyncedData["ids"];
			output["synced_grids"] = objSyncedData["grids"];
		}
		
		return(output);
	}
	
	/**
	 * show element error above it
	 */
	function showElementError(objElement, error){
		
		var objParent = objElement.parent();
		
		var objError = objParent.find(".uc-filers-error-message");
		if(objError.length == 0){
			objParent.append("<div class='uc-filers-error-message' style='color:red;position:absolute;top:-24px;left:0px;'></div>");
			var objError = objParent.find(".uc-filers-error-message");
			objParent.css("border","1px solid red !important");
		}
		
		objError.append(error);
		
	}
	
	/**
	 * get grid empty message
	 */
	function getGridEmptyMessage(objGrid){
	
		var gridID = objGrid.attr("id");
		
		if(!gridID)
			return(null);
		
		var objEmptyMessage = jQuery("#"+gridID+"_empty_message");
		
		if(objEmptyMessage.length == 0)
			return(null);
		
		return(objEmptyMessage);
	}
	
	
	function ________FILTERS_______________(){}
	
	
	/**
	 * get filter type
	 */
	function getFilterType(objFilter){
		
		if(objFilter.hasClass("uc-filter-pagination"))
			return(g_types.PAGINATION);
				
		if(objFilter.hasClass("uc-filter-load-more"))
			return(g_types.LOADMORE);
		
		var filterType = objFilter.data("filtertype")
		
		if(filterType)
			return(filterType);
		
		throw new Error("wrong filter type");
		
		return(null);
	}
	
	
	/**
	 * clear non main grid filters
	 * hide children and just clear the main filters
	 */
	function clearChildFilters(objGrid, objCurrentFilter, isHideChildren, termID){
		
		var objFilters = objGrid.data("filters");
		
		if(!objFilters)
			return(false);
		
		if(objFilters.length == 0)
			return(false);
		
		var currentFilterID = objCurrentFilter.attr("id");
				
		jQuery.each(objFilters, function(index, filter){
			
			var objFilter = jQuery(filter);
			var filterID = objFilter.attr("id");
			
			if(filterID == currentFilterID)
				return(true);
						
			var role = objFilter.data("role");
						
			if(role != "child" && role != "main" && role != "term_child")
				return(true);
			
			var isHide = false;
			var isShow = false;
			
			switch(role){
				case "term_child":
					if(isHideChildren == true)
						isHide = true;
						
					var linkedTermID = objFilter.data("childterm");
					
					if(linkedTermID == termID){		//show the filter
						
						objFilter.removeClass(g_vars.CLASS_HIDDEN);
						objFilter.removeClass(g_vars.CLASS_INITING);
						objFilter.removeClass(g_vars.CLASS_INITING_HIDDEN);
					}else{
						isHide = true;
					}
						
				break;
				case "child":
					
					if(isHideChildren == true)
						isHide = true;
					else{
						//hide the filters and refresh
						
						objFilter.removeClass(g_vars.CLASS_HIDDEN);
						
						objFilter.addClass(g_vars.CLASS_INITING);
						objFilter.addClass(g_vars.CLASS_INITING_HIDDEN);
						
					}
					
				break;
			}
						
			//hide the child filters and not refresh
			
			if(isHide == true)
				objFilter.addClass(g_vars.CLASS_HIDDEN);	
			
			
			clearFilter(objFilter);
						
		});
		
	}
	
	/**
	 * clear some filter
	 */
	function clearFilter(objFilter){
		
		var type = getFilterType(objFilter);
		
		switch(type){
			case g_types.TERMS_LIST:
				var objSelectedItems = objFilter.find(".ue_taxonomy_item.uc-selected");
				objSelectedItems.removeClass("uc-selected");
				
				var objAll = objFilter.find(".ue_taxonomy_item.uc-item-all");
				objAll.addClass("uc-selected");
			
			break;
			case g_types.SELECT:
				
				var objSelect = objFilter.find("select");
				objSelect.val("");
				
			break;

		}
		
	}
	
	function ________PAGINATION_FILTER______(){}
	
	
	/**
	 * get pagination selected url or null if is current
	 */
	function getPaginationSelectedData(objPagination){
		
		var objCurrentLink = objPagination.find("a.current");
		
		if(objCurrentLink.length == 0)
			return(null);
		
		var url = objCurrentLink.attr("href");
		
		if(!url)
			return(null);
		
		var numPage = objCurrentLink.text();
				
		if(jQuery.isNumeric(numPage) == false)
			numPage = null;
		
		numPage = Number(numPage);
		
		if(numPage === 1)
			numPage = null;
		
		var output = {};
		output["url"] = url;
		output["page"] = numPage;
		
		return(output);
	}
	
	
	/**
	 * on ajax pagination click
	 */
	function onAjaxPaginationLinkClick(event){
		
		var objLink = jQuery(this);
		
		var objPagination = objLink.parents(".uc-filter-pagination");
				
		var objLinkCurrent = objPagination.find(".current");
		
		
		//on next button click
		
		if(objLink.hasClass("next")){
			
			var nextLink = objLinkCurrent.next();
			
			var objNextLink = jQuery(nextLink);
			
			objNextLink.trigger("click");
			
			return(false);
		}

		
		//on prev button click
		
		if(objLink.hasClass("prev")){
			
			var prevLink = objLinkCurrent.prev();
			
			var objPrevLink = jQuery(prevLink);
			
			objPrevLink.trigger("click");
			
			return(false);
		}
		
		objLinkCurrent.removeClass("current");
		
		objLink.addClass("current");
		
		var objGrid = objPagination.data("grid");
		
		if(!objGrid || objGrid.length == 0)
			throw new Error("Grid not found!");
		
		//run the ajax, prevent default
		event.preventDefault();
				
		objPagination.addClass(g_vars.CLASS_CLICKED);
		
		refreshAjaxGrid(objGrid, g_vars.REFRESH_MODE_PAGINATION);
		
		return(false);
	}

	
	function ________LOAD_MORE_______________(){}
	
	
	/**
	 * get current load more page
	 */
	function getLoadMoreUrlData(objFilter){
		
		var objData = objFilter.find(".uc-filter-load-more__data");
		
		var nextOffset = objData.data("nextoffset");
		if(!nextOffset)		
			nextOffset = null;
				
		var numItems = objFilter.data("numitems");
		
		if(!numItems)
			numItems = null;
		
		//affect only single grids
		
		var isSingleGridOnly = objFilter.data("affect_single_grid");
		
			
		var data = {};
		data.offset = nextOffset;
		data.numItems = numItems;
		data.singlegrid = isSingleGridOnly;
		
		return(data);
	}
	
	
	/**
	 * init load more filter
	 */
	function initLoadMoreFilter(objLoadMore){
		
		var objData = objFilter.find(".uc-filter-load-more__data");
		
		var isMore = objData.data("more");
		if(isMore !== true)
			return(false);
	
		//check if nessesary
		objLoadMore.addClass("uc-loadmore-active");
	}
	
	
	/**
	 * do the load more operation
	 */
	function onLoadMoreClick(){
		
		var objLink = jQuery(this);
		
		var objLoadMore = objLink.parents(".uc-filter-load-more");
		
		var objData = objLoadMore.find(".uc-filter-load-more__data");
		
		var isMore = objData.data("more");
		
		if(isMore == false)
			return(false);
		
		var objGrid = objLoadMore.data("grid");
		
		if(!objGrid || objGrid.length == 0)
			throw new Error("Grid not found!");
		
		//run the ajax, prevent default
		
		objLoadMore.addClass(g_vars.CLASS_CLICKED);
		
		refreshAjaxGrid(objGrid, g_vars.REFRESH_MODE_LOADMORE);
		
	}
	
	function ________SELECT_______________(){}
	
	/**
	 * init select filter, select the selected item (avoid cache)
	 */
	function initSelectFilter(objFilter){
				
		var objSelected = objFilter.find(".uc-selected");
		
		if(objSelected.length == 0)
			return(false);
		
		var value = objSelected.attr("value");
		
		var objSelect = objFilter.find("select");
		
		objSelect.val(value);
		
	}
	
	
	function ________TERMS_LIST_______________(){}
	
	
	/**
	 * on terms list click
	 */
	function onTermsLinkClick(event){
		
		var className = "uc-selected";
		
		event.preventDefault();
				
		var objLink = jQuery(this);
		
		if(objLink.hasClass("uc-grid-filter")){
			
			var objTermsFilter = objLink;
			
		}else{
			
			var objTermsFilter = objLink.parents(".uc-grid-filter");
		}
		
		var filterType = getFilterType(objTermsFilter);
		
		if(filterType == g_types.SELECT){
			
			var objLink = objTermsFilter.find("option:selected");
			
		}
		
		if(filterType == g_types.TERMS_LIST){			

			var objActiveLinks = objLink.siblings("."+className).not(objLink);
			
			objActiveLinks.removeClass(className);
			objLink.addClass(className);
			
		}
		
		
		//set not refresh next iteration, because of the clicked
		setNoRefreshFilter(objTermsFilter);		
		var objGrid = objTermsFilter.data("grid");
		
		if(!objGrid || objGrid.length == 0)
			throw new Error("Grid not found");
		
		//if main filter - clear other filters
		
		var filterRole = objTermsFilter.data("role");
		
		var termID = objLink.data("id");
		
		var isHideChildren = false;
		if(!termID)
			isHideChildren = true;
		
		if(filterRole == "main")
			clearChildFilters(objGrid, objTermsFilter, isHideChildren, termID);
		
		//refresh grid		
		refreshAjaxGrid(objGrid);
		
	}
		
	
	/**
	 * get terms list term id
	 */
	function getTermsListSelectedTerm(objFilter){
		
		if(!objFilter)
			return(null);
		
		var filterType = getFilterType(objFilter);
		
		var objSelected = objFilter.find(".uc-selected");
		
		if(filterType == g_types.SELECT){
			
			var objSelected = objFilter.find("option:selected");
			
		}else{
			
			var objSelected = objFilter.find(".uc-selected");
		}
		
		if(objSelected.length == 0){
			
			if(g_showDebug == true){
				trace("no selected found, skipping...");
			}
			
			return(null);
		}
		
		//check for hidden
		
		if(filterType == g_types.TERMS_LIST && objSelected.is(":hidden")){
			
			if(g_showDebug == true){
				
				trace("the selected object");
				trace(objSelected);
				trace("the term is hidden, skipping...");
				
			}
			
			return(null);
		}
		
		if(objSelected.length > 1)
			objSelected = jQuery(objSelected[0]);
		
		var id = objSelected.data("id");
		var slug = objSelected.data("slug");
		var taxonomy = objSelected.data("taxonomy");
		
		if(!taxonomy)
			return(null);
		
		var objTerm = {
			"id": id,
			"slug": slug,
			"taxonomy": taxonomy
		};
		
		return(objTerm);
	}

	/**
	 * select items in terms list by terms
	 */
	function termListSelectItems(objFilter, arrTerms){
		
		//deselect
		var objSelected = objFilter.find(".uc-selected");
		
		objSelected.removeClass("uc-selected");
		
		//select by term
		
		jQuery.each(arrTerms, function(index, term){
			
			var termID = getVal(term,"id");
			var objItem = objFilter.find("a.ue_taxonomy_item[data-id='"+termID+"']");
			
			if(objItem.length == 0)
				return(true);
			
			objItem.addClass("uc-selected");
		});
		
		
	}
	
	function ________GENERAL_FILTER_______________(){}
	
	
	/**
	 * on general filter change
	 */
	function onFilterChange(){
		
		var objFilter = jQuery(this);
		
		setNoRefreshFilter(objFilter);
		
		var objGrid = objFilter.data("grid");
				
		if(!objGrid || objGrid.length == 0){
			
			trace(objGrid);
			
			throw new Error("Wrong filter change");
			return(false);
		}
		
		
		refreshAjaxGrid(objGrid);
	}
	
	
	/**
	 * select filter items by terms, without refresh, just set selected
	 */
	function selectFilterItemsByTerms(objFilters, arrTerms){
		
		if(!objFilters || objFilters.length == 0)
			return(false);
		
		if(!arrTerms || arrTerms.length == 0)
			return(false);
		
		jQuery.each(objFilters,function(index, filter){
			
			var objFilter = jQuery(filter);
			
			selectFilterItems(objFilter, arrTerms);
			
		});
				
	}
	
	/**
	 * select filter items
	 */
	function selectFilterItems(objFilter, arrTerms){
		
		var type = getFilterType(objFilter);
		
		switch(type){
			//case g_types.SELECT:
			case g_types.TERMS_LIST:
				
				termListSelectItems(objFilter, arrTerms);
				
			break;
		}
		
	}
	
	function ________INIT_FILTERS_______________(){}
	
	/**
	 * get filter taxonomy id's
	 */
	function getFilterTaxIDs(objFilter, objIDs){
		
		var type = getFilterType(objFilter);
		
		//skip the if
		if(type == g_types.SELECT)
			var objItems = objFilter.find(".uc-select-filter__option");
		else
			var objItems = objFilter.find(".ue_taxonomy_item");
		
		if(objItems.length == 0)
			return(objIDs);
		
		jQuery.each(objItems, function(index, item){
			
			var objItem = jQuery(item);
			var taxID = objItem.data("id");
			
			if(!taxID)
				return(true);
			
			objIDs[taxID] = true;
		});
		
		
		return(objIDs);
	}
	
	/**
	 * get tax id's list string from assoc object
	 */
	function getTermDsList(objIDs){
		
		var strIDs = "";
		for(var id in objIDs){
			
			if(jQuery.isNumeric(id) == false)
				continue;
			
			if(strIDs)
				strIDs += ",";
			
			strIDs += id;
		}
		
		return(strIDs);
	}
	
	
	function ________DATA_______________(){}
	
	 	
	/**
	 * build terms query
	 * ucterms=product_cat~shoes.dress;cat~123.43;
	 */
	function buildTermsQuery(arrTerms){
		
		var query = "";
				
		//break by taxonomy
		var arrTax = {};
		jQuery.each(arrTerms, function(index, objTerm){
			
			var taxonomy = objTerm["taxonomy"];
			var slug = objTerm["slug"];
			
			var objTax = getVal(arrTax, taxonomy);
			if(!objTax)
				objTax = {};
			
			objTax[slug] = true;
			arrTax[taxonomy] = objTax;
			
		});
		
		//combine the query
		
		if(!arrTax)
			return(null);
		
		jQuery.each(arrTax,function(taxonomy,objSlugs){
			
			var strSlugs = "";
						
			var moreThenOne = false;
			for (var slug in objSlugs){
				
				if(strSlugs){
					moreThenOne = true;
					strSlugs += ".";
				}
				
				strSlugs += slug;
			}
			
			//add "and"
			if(moreThenOne == true)
				strSlugs += ".*";
			
			var strTax = taxonomy+"~"+strSlugs;
						
			if(query)
				query += ";";
			
			query += strTax;
			
		});
				
		return(query);
	}
	
	
	function ________AJAX_CACHE_________(){}

	/**
	 * get ajax url
	 */
	function getAjaxCacheKeyFromUrl(ajaxUrl){
		
		var key = ajaxUrl;
		
		key = key.replace(g_urlAjax, "");
		key = key.replace(g_urlBase, "");
		
		//replace special signs
		key = replaceAll(key, "/","");
		key = replaceAll(key, "?","_");
		key = replaceAll(key, "&","_");
		key = replaceAll(key, "=","_");
		
		return(key);
	}
	
	/**
	 * get ajax cache key
	 */
	function getAjaxCacheKey(ajaxUrl, action, objData){
		
	    if(g_options.is_cache_enabled == false)
	    	return(false);
	    
	    //cache only by url meanwhile
	    
	    if(jQuery.isEmptyObject(objData) == false)
	    	return(false);
	    
	    if(action)
	    	return(false);
	    
	    var cacheKey = getAjaxCacheKeyFromUrl(ajaxUrl);
	    
	    if(!cacheKey)
	    	return(false);
	    
	    return(cacheKey);
	}
	
	
	/**
	 * cache ajax response
	 */
	function cacheAjaxResponse(ajaxUrl, action, objData, response){
		
	    var cacheKey = getAjaxCacheKey(ajaxUrl, action, objData);
	    
	    if(!cacheKey)
	    	return(false);
	    
	    //some precoutions for overload
	    if(g_cache.length > 100)
	    	return(false);
	    
	    g_cache[cacheKey] = response;
	    
	}
	
		
	function ________AJAX_RESPONSE_______________(){}

	/**
	 * replace the grid debug
	 */
	function operateAjax_setHtmlDebug(response, objGrid){
				
		//replace the debug
		var htmlDebug = getVal(response, "html_debug");
				
		if(!htmlDebug)
			return(false);
		
		var gridParent = objGrid.parent();
				
		var objDebug = objGrid.siblings(".uc-debug-query-wrapper");
		
		if(objDebug.length == 0)
			return(false);
				
		objDebug.replaceWith(htmlDebug);
	}
	
	
	/**
	 * set html grid from ajax response
	 */
	function operateAjax_setHtmlGrid(response, objGrid, isLoadMore){
		
		if(g_showDebug == true){
			trace("set html grid, response: ");
			trace(response);
			
			trace("obj grid:");
			trace(objGrid);
		}
		
		if(objGrid.length == 0)
			return(false);
						
		var objItemsWrapper = getGridItemsWrapper(objGrid);
		var objItemsWrapper2 = getGridItemsWrapper(objGrid, true);
		
		if(g_showDebug == true){
			trace("items wrapper 1: ");
			trace(objItemsWrapper);
			
			trace("items wrapper 2:");
			trace(objItemsWrapper2);
		}
		
		
		if(!objItemsWrapper || objItemsWrapper.length == 0)
			throw new Error("Missing items wrapper: .uc-items-wrapper");
		
	
		operateAjax_setHtmlDebug(response, objGrid);
		
		//set grid items
		
		//if init filters mode, and no items response - don't set
		if(response.hasOwnProperty("html_items") == false)
			return(false);
				
		var htmlItems = getVal(response, "html_items");
		
		var htmlItems2 = null;
		
		if(objItemsWrapper2)
			htmlItems2 = getVal(response, "html_items2"); 
		
		//replace widget id
		var gridID = objGrid.attr("id");
		
		htmlItems = replaceAll(htmlItems, "%uc_widget_id%", gridID);
		
		if(htmlItems2)
			htmlItems2 = replaceAll(htmlItems2, "%uc_widget_id%", gridID);
		
		var isCustomRefresh = objGrid.data("custom-sethtml");
		
		//show / hide empty message if available and empty response
		var objEmptyMessage = getGridEmptyMessage(objGrid);
				
		if(objEmptyMessage){
			if(htmlItems == "")
				objEmptyMessage.show();
			else
				objEmptyMessage.hide();				
		}
		
		//set the query data
		var queryDataOriginal = getVal(response, "query_data");
		var queryIDs = getVal(response,"query_ids");
		
		var queryData = jQuery.extend({}, queryDataOriginal);
		
		//add to old data
				
		if(isLoadMore == true){
			
			var currentQueryData = objGrid.attr("querydata");
			
			var objCurrentData = jQuery.parseJSON(currentQueryData);
			var currentNumPosts = getVal(objCurrentData, "count_posts");
			
			queryData.count_posts += currentNumPosts;
			
			var currentQueryIDs = objGrid.data("postids");
			
			if(queryIDs && currentQueryIDs)
				queryIDs = currentQueryIDs + "," + queryIDs;
		}
		
		//query data replace
		
		if(queryData){
			
			objGrid.removeAttr("querydata");
			
			var jsonData = JSON.stringify(queryData);
			objGrid.attr("querydata", jsonData);
			
			objGrid.data("querydata", queryData);
		}
		
		//post id's replace
		
		objGrid.removeAttr("data-postids");
		objGrid.attr("data-postids", queryIDs);
		objGrid.data("postids", queryIDs);
		
		
		//if custom refresh - just save the new html in data
		if(isCustomRefresh == true){
			
			objGrid.trigger(g_vars.EVENT_SET_HTML_ITEMS,[htmlItems, isLoadMore, htmlItems2]);
			return(false);
		}
		
		
		if(!htmlItems2)
			htmlItems2 = "";
		
		if(isLoadMore === true){
			
			if(g_showDebug == true){
				trace("append load more");
			}
			
			objItemsWrapper.append(htmlItems);
			
			if(objItemsWrapper)
				objItemsWrapper.append(htmlItems2);
			
		}else{
			
			objItemsWrapper.html(htmlItems);
			
			if(objItemsWrapper2 && objItemsWrapper2.length)
				objItemsWrapper2.html(htmlItems2);
			
		}
			
	}
	
	
	/**
	 * refresh synced grids
	 */
	function operateAjax_setHtmlSyngGrids(response, objGrid, isLoadMore){
				
		var objSyncWidgetsResponse = getVal(response, "html_sync_widgets");
		
		if(g_showDebug == true){
			trace("set html sync grids");
			trace(objSyncWidgetsResponse);
		}
		
		var queryData = getVal(response,"query_data");
		
		if(!objSyncWidgetsResponse)
			return(false);
		
		jQuery.each(objSyncWidgetsResponse, function(elementID, childResponse){
			
			var objGridWidget = getGridFromElementorElementID(elementID);
			
			if(!objGridWidget)
				return(true);
			
			objGridWidget.removeClass(g_vars.CLASS_REFRESH_SOON);
			
			childResponse.query_data = queryData;
			
			operateAjax_setHtmlGrid(childResponse, objGridWidget, isLoadMore);
			
			objGrid.trigger(g_vars.EVENT_AJAX_REFRESHED);
			g_objBody.trigger(g_vars.EVENT_AJAX_REFRESHED_BODY, [objGridWidget]);
						
		});
		
	}
	
	
	/**
	 * replace filters html
	 */
	function operateAjax_setHtmlWidgets(response, objFilters){
		
		if(!objFilters)
			return(false);
		
		if(objFilters.length == 0)
			return(false);
		
		var objHtmlWidgets = getVal(response, "html_widgets");
		
		if(!objHtmlWidgets)
			return(false);
				
		if(objHtmlWidgets.length == 0)
			return(false);
		
		var objHtmlDebug = getVal(response, "html_widgets_debug");
				
		jQuery.each(objFilters, function(index, objFilter){
			
			var widgetID = getElementWidgetID(objFilter);
			
			if(!widgetID)
				return(true);
			
			var html = getVal(objHtmlWidgets, widgetID);
			
			var objHtml = jQuery(html);
			
			var htmlInner = objHtml.html();
			
			//set the class
			
			var filterClassName = objHtml.attr("class");
						
			objFilter.attr("class", filterClassName);
			
			objFilter.removeClass(g_vars.CLASS_INITING);
			objFilter.removeClass(g_vars.CLASS_REFRESH_SOON);
			
			objFilter.html(htmlInner);
			
			
			//---- put the debug if exists
			
			var htmlDebug = null;
			
			if(objHtmlDebug)
				var htmlDebug = getVal(objHtmlDebug, widgetID);
			
			if(htmlDebug){
				var objParent = objFilter.parents(".elementor-widget-container");
				var objDebug = objParent.find(".uc-div-ajax-debug");
				
				if(objDebug.length)
					objDebug.replaceWith(htmlDebug);
			}
			
			objFilter.trigger("uc_ajax_reloaded");
			
		});
		
	}
	
	/**
	 * scroll to grid top
	 */
	function scrollToGridTop(objGrid){
		
		var gapTop = 150;
		
		var gridOffset = objGrid.offset().top;
		
		var gridTop = gridOffset - gapTop;
		
		if(gridTop < 0)
			gridTop = 0;
		
		//check if the grid top is visible
		
		var currentPos = jQuery(window).scrollTop();
		
		if(currentPos <= gridOffset)
			return(false);
		
		window.scrollTo({ top: gridTop, behavior: 'smooth' });
		
	}
	
	
	/**
	 * operate the response
	 */
	function operateAjaxRefreshResponse(response, objGrid, objFilters, isLoadMore, isNoScroll){
		
		operateAjax_setHtmlGrid(response, objGrid, isLoadMore);
		
		operateAjax_setHtmlWidgets(response, objFilters);
		
		operateAjax_setHtmlSyngGrids(response, objGrid, isLoadMore);
		
		objGrid.trigger(g_vars.EVENT_AJAX_REFRESHED);
		g_objBody.trigger(g_vars.EVENT_AJAX_REFRESHED_BODY, [objGrid]);
		
		//trigger body as well
		
		//scroll to top
		if(isLoadMore == false && isNoScroll !== true){
			
			setTimeout(function(){
				
				scrollToGridTop(objGrid);
				
			},200);
			
		}
				
	}
	
	
	/**
	 * replace all occurances
	 */
	function replaceAll(text, from, to){
		
		return text.split(from).join(to);		
	};
	
	
	
	
	/**
	 * get response from ajax cache
	 */
	function getResponseFromAjaxCache(ajaxUrl, action, objData){
	
	    var cacheKey = getAjaxCacheKey(ajaxUrl, action, objData);
	    
	    if(!cacheKey)
	    	return(false);
		
	    var response = getVal(g_cache, cacheKey);
	    
	    return(response);
	}
	
	
	function ________AJAX_______________(){}
	
	/**
	 * set this filter not to refresh next time
	 */
	function setNoRefreshFilter(objFilter){
		
		objFilter.data("uc_norefresh",true);
		
	}
	
	/**
	 * show ajax error, should be something visible
	 */
	function showAjaxError(message){
		
		alert(message);
		
	}
	
	/**
	 * get the debug object
	 */
	function getDebugObject(){
		
		var objGrid = g_lastGridAjaxCall;
		
		if(!objGrid)
			return(null);
		
		var objDebug = objGrid.find("."+g_vars.CLASS_DIV_DEBUG);
		
		if(objDebug.length)
			return(objDebug);
		
		//insert if not exists
		
		objGrid.after("<div class='"+g_vars.CLASS_DIV_DEBUG+"' style='padding:10px;display:none;background-color:#D8FCC6'></div>");
		
		var objDebug = jQuery("body").find("."+g_vars.CLASS_DIV_DEBUG);
		
		return(objDebug);
	}
	
	
	/**
	 * show ajax debug
	 */
	function showAjaxDebug(str){
		
		trace("Ajax Error! - Check the debug");
		
		str = jQuery.trim(str);
		
		if(!str || str.length == 0)
			return(false);
		
		var objStr = jQuery(str);
		
		if(objStr.find("header").length || objStr.find("body").length){
			str = "Wrong ajax response!";
		}
		
		var objDebug = getDebugObject();
		
		if(!objDebug || objDebug.length == 0){
			
			alert(str);
			
			throw new Error("debug not found");
		}
		
		objDebug.show();
		objDebug.html(str);
		
	}
	
	
	/**
	 * small ajax request
	 */
	function ajaxRequest(ajaxUrl, action, objData, onSuccess){
		
		if(g_showDebug == true){
			trace("ajax request");
			trace(ajaxUrl);		
		}
		
		if(!objData)
			var objData = {};
		
		if(typeof objData != "object")
			throw new Error("wrong ajax param");
		
		//check response from cache
		var responseFromCache = getResponseFromAjaxCache(ajaxUrl, action, objData);
		
		if(responseFromCache){
			
			//simulate ajax request
			setTimeout(function(){
				onSuccess(responseFromCache);
			}, 300);
			
			return(false);
		}		
		
		var ajaxData = {};
		ajaxData["action"] = "unlimitedelements_ajax_action";
		ajaxData["client_action"] = action;
		
		var ajaxtype = "get";
		
		if(jQuery.isEmptyObject(objData) == false){
			ajaxData["data"] = objData;
			ajaxtype = "post";
		}
				
		
		var ajaxOptions = {
				type:ajaxtype,
				url:ajaxUrl,
				success:function(response){
					
					if(!response){
						showAjaxError("Empty ajax response!");
						return(false);					
					}
										
					if(typeof response != "object"){
						
						try{
							
							response = jQuery.parseJSON(response);
							
						}catch(e){
							
							showAjaxDebug(response);
							
							showAjaxError("Ajax Error!!! not ajax response");
							return(false);
						}
					}
					
					if(response == -1){
						showAjaxError("ajax error!!!");
						return(false);
					}
					
					if(response == 0){
						showAjaxError("ajax error, action: <b>"+action+"</b> not found");
						return(false);
					}
					
					if(response.success == undefined){
						showAjaxError("The 'success' param is a must!");
						return(false);
					}
					
					
					if(response.success == false){
						showAjaxError(response.message);
						return(false);
					}
					
					cacheAjaxResponse(ajaxUrl, action, objData, response);
					
					if(typeof onSuccess == "function"){
										
						onSuccess(response);
					}
					
				},
				error:function(jqXHR, textStatus, errorThrown){
										
					switch(textStatus){
						case "parsererror":
						case "error":
							
							//showAjaxError("parse error");
							
							showAjaxDebug(jqXHR.responseText);
							
						break;
					}
				}
		}
		
		if(ajaxtype == "post"){
			ajaxOptions.dataType = 'json';
			ajaxOptions.data = ajaxData
		}
		
		var handle = jQuery.ajax(ajaxOptions);
		
		return(handle);
	}
	
	
	
	/**
	 * get grid items wrapper
	 */
	function getGridItemsWrapper(objGrid, isSecond){
		
		var classItems = "uc-items-wrapper";
		
		if(isSecond == true)
			classItems = "uc-items-wrapper2";
		
		if(objGrid.hasClass(classItems))
			return(objGrid);
		
		var objItemsWrapper = objGrid.find("."+classItems);
		
		if(objItemsWrapper.length == 0 && isSecond == false)
			throw new Error("Missing items wrapper - with class: uc-items-wrapper");
		
		if(objItemsWrapper.length == 0)
			return(null);
		
		return(objItemsWrapper);
	}
	
	
	/**
	 * set ajax loader
	 */
	function showAjaxLoader(objElement){
		
		objElement.addClass("uc-ajax-loading");		
	}
	
	/**
	 * hide ajax loader
	 */
	function hideAjaxLoader(objElement){
		
		objElement.removeClass("uc-ajax-loading");		
	}
	
	
	/**
	 * show multiple ajax loader
	 */
	function showMultipleAjaxLoaders(objElements, isShow){
		
		if(!objElements)
			return(false);
		
		if(objElements.length == 0)
			return(false);
		
		jQuery.each(objElements,function(index, objElement){
			
			objElement = jQuery(objElement);
			
			if(isShow == true){
				
				showAjaxLoader(objElement);
			}
			else
				hideAjaxLoader(objElement);
		});
		
	}
	
		
	/**
	 * refresh ajax grid
	 */
	function refreshAjaxGrid(objGrid, refreshType){
		
		var isLoadMore = (refreshType == g_vars.REFRESH_MODE_LOADMORE);	 //for the output
		var isFiltersInit = (refreshType == "filters");
		
		//for the options - not refresh other filters
		var isLoadMoreMode = (refreshType == g_vars.REFRESH_MODE_LOADMORE || refreshType == g_vars.REFRESH_MODE_PAGINATION);
			
		//get all grid filters
		var objFilters = objGrid.data("filters");
		
		if(!objFilters)
			return(false);
		
		if(objFilters.length == 0)
			return(false);
		
		if(objGrid.hasClass(g_vars.CLASS_GRID_NOREFRESH))
			return(false);
		
		var objAjaxOptions = getGridAjaxOptions(objFilters, objGrid, isFiltersInit, isLoadMoreMode);
		
		if(!objAjaxOptions){
			
			trace("ajax options are null");
			return(false);
		}
				
		var ajaxUrl = objAjaxOptions["ajax_url"];
		var urlReplace = objAjaxOptions["url_replace"];
		var arrTerms = objAjaxOptions["terms"];
		
		if(g_vars.DEBUG_AJAX_OPTIONS == true){
			
			trace("DEBUG AJAX OPTIONS");
			trace(objAjaxOptions);
			return(false);
		}
		
		//set the url params
		var behave = objGrid.data("filterbehave");
		
		var isSetUrl = (behave == "mixed" || behave == "mixed_back");
		
		if(isFiltersInit == false && isSetUrl === true){
			
			if(behave == "mixed_back"){
				
				//save state for back button
				
				var gridID = objGrid.attr("id");
				
				//save initial state
				var isStateEmpty = jQuery.isEmptyObject(history.state);
								
				var objState = {"ucaction":"change", "ajaxurl":ajaxUrl, "gridid":gridID, selected_terms:arrTerms};
				
				if(isStateEmpty){
					
					var ajaxUrlInitial = objGrid.data("initajaxurl");
					
					objState["ajaxurl"] = ajaxUrlInitial;
					
					history.replaceState(objState, null, urlReplace);
				}
				
				history.pushState(objState, null, urlReplace);		//with back
				
			}
			else
				history.replaceState({}, null, urlReplace);		//without back
		}
		
		doGridAjaxRequest(ajaxUrl, objGrid, objFilters, isLoadMore, isFiltersInit);
		
	}
	
	
	/**
	 * do the actual grid ajax request
	 */
	function doGridAjaxRequest(ajaxUrl, objGrid, objFilters, isLoadMore, isFiltersInit){
		
		var objEmptyMessage = getGridEmptyMessage(objGrid);
		
		//set the loaders
		
		if(isLoadMore !== true && isFiltersInit !== true){
			
			showAjaxLoader(objGrid);
			
			if(objEmptyMessage)
				showAjaxLoader(objEmptyMessage);
		}
		
		var objFiltersToReload = objFilters.filter(function(objFilter){
			
			return objFilter.hasClass(g_vars.CLASS_REFRESH_SOON);
		});
		
		showMultipleAjaxLoaders(objFiltersToReload, true);
				
		if(g_lastSyncGrids && isLoadMore !== true)
			showMultipleAjaxLoaders(g_lastSyncGrids, true);
		
		
		//ajax reload
		g_lastGridAjaxCall = objGrid;
		
		objGrid.trigger(g_vars.EVENT_BEFORE_REFRESH);
		
		var lastAjaxHandle = objGrid.data("last_ajax_refresh_handle");
		
		if(lastAjaxHandle){
			lastAjaxHandle.abort();
		}
		
		var ajaxHandle = ajaxRequest(ajaxUrl,null,null, function(response){
			
			if(isLoadMore !== true){
				hideAjaxLoader(objGrid);
				
				if(objEmptyMessage)
					hideAjaxLoader(objEmptyMessage);
			}
			
			showMultipleAjaxLoaders(objFilters, false);
			
			if(g_lastSyncGrids)
				showMultipleAjaxLoaders(g_lastSyncGrids, false);
			
			operateAjaxRefreshResponse(response, objGrid, objFilters, isLoadMore);
			
		});
		
		objGrid.data("last_ajax_refresh_handle", ajaxHandle);
		
	}
	
	function ________STATE_RELATED_______________(){}

	
	/**
	 * do history
	 */
	function changeToHistoryState(state){
		
		if(g_showDebug == true){
			trace("change to history");
			trace(state);
		}
		
		var ajaxUrl = getVal(state, "ajaxurl");
		
		var gridID = getVal(state, "gridid");
		
		var arrTerms = getVal(state, "selected_terms");
		
		if(!gridID)
			return(false);
		
		if(!ajaxUrl)
			return(false);

		var objGrid = jQuery("#"+gridID);
		
		var objFilters = objGrid.data("filters");
		
		if(!objFilters)
			return(false);
		
		//select by terms
		
		selectFilterItemsByTerms(objFilters, arrTerms);
		
		var responseFromCache = getResponseFromAjaxCache(ajaxUrl);
		
		if(!responseFromCache){
			
			//do ajax request
			
			doGridAjaxRequest(ajaxUrl, objGrid, objFilters);
			
			return(false);
		}
		
		//get data from cache
		
		operateAjaxRefreshResponse(responseFromCache, objGrid, objFilters, false, true);
		
	}
	
	/**
	 * on pop state, if it's a grid state, set the grid
	 */
	function onPopState(){
		
		if(!history.state)
			return(true);
		
		var action = getVal(history.state, "ucaction");
		
		if(!action)
			return(true);
		
		switch(action){
			case "change":
				
				changeToHistoryState(history.state);
				
			break;
			default:
				throw new Error("Wrong history action: " + action);
			break;
		}
		
	}
	
	
	function ________RUN_______________(){}
	
	/**
	 * get url filters string
	 */
	function getGridUrlFiltersString(objGrid){
		
		var objFilters = objGrid.data("filters");
		
		if(!objFilters)
			return("");
		
		var objAjaxOptions = getGridAjaxOptions(objFilters, objGrid, false);
		
		var strFilters = getVal(objAjaxOptions, "filters_string");
		
		return(strFilters);
	}
	
	
	/**
	 * get grid ajax options
	 */
	function getGridAjaxOptions(objFilters, objGrid, isFiltersInitMode, isLoadMoreMode){
		
		if(!isLoadMoreMode)
			var isLoadMoreMode = false;
		
		if(g_showDebug){
			trace("getGridAjaxOptions");
						
			trace("Filters:");
			trace(objFilters);
			
			trace("grid:");
			trace(objGrid);
			trace("is init: " + isFiltersInitMode);
		}
		
		
		//filter only visible elements (by it's parents)
		
		var objVisibleFilters = objFilters.filter(function(objFilter){
			
			var objParent = objFilter.parent();
			
			return(!objParent.is(":hidden"));
		});
		
		
		if(objVisibleFilters.length < objFilters.length){
			
			var objFilters = objVisibleFilters;
			
			if(g_showDebug){
				trace("Visible Filters: ");
				trace(objFilters);
				
			}
		}
		
		if(!objFilters || objFilters.length == 0)
			return(null);
				
		var urlReplace = g_urlBase;
		
		var urlAjax = g_urlBase;
		
		var strRefreshIDs = "";
		
		var isReplaceMode = false;
		var page = null;
		var offset = null;
		var numItems = null;
		var arrTerms = [];
		var objTaxIDs = {};
		var strSelectedTerms = "";
		var search = "";
		var addSyncedGrids = true;
		
		
		//get ajax options
		jQuery.each(objFilters, function(index, objFilter){
			
			var isNoRefresh = objFilter.data("uc_norefresh");
			
			var type = getFilterType(objFilter);
			
			if(g_showDebug == true){
				
				trace("filter: "+type);
				trace(objFilter);
			}
			
			switch(type){
				case g_types.PAGINATION:
					
					if(isFiltersInitMode == true)
						return(true);
					
					//run pagination only if it's clicked, unless reset pagination
					var isClicked = objFilter.hasClass(g_vars.CLASS_CLICKED);
					if(isClicked == true){
						
						 var paginationData = getPaginationSelectedData(objFilter);
						 
						 var paginationPage = getVal(paginationData, "page"); 
						 
						 if(paginationPage)
							 page = paginationPage;		//never set the url
						 
						 if(g_showDebug){
							 trace("pagination data");
							 trace(paginationData);
						 }
						 
						objFilter.removeClass(g_vars.CLASS_CLICKED);
					}
					
				break;
				case g_types.LOADMORE:
					
					if(isFiltersInitMode == true)
						return(true);
					
					//run load more only if it's clicked, unless reset load more
					var isClicked = objFilter.hasClass(g_vars.CLASS_CLICKED);
					if(isClicked == true){
						
						var loadMoreData = getLoadMoreUrlData(objFilter);
						offset = loadMoreData.offset;
						numItems = loadMoreData.numItems;
						
						var isSingleGrid = loadMoreData.singlegrid;
						
						if(isSingleGrid == true)
							addSyncedGrids = false;
												
						if(!offset)
							urlAjax = null;
						
						objFilter.removeClass(g_vars.CLASS_CLICKED);
					}
					
				break;
				case g_types.TERMS_LIST:
				case g_types.SELECT:
										
					//if not init mode - take first item
					var objTerm = getTermsListSelectedTerm(objFilter);
					
					if(objTerm){
						
						if(isFiltersInitMode == false)
							arrTerms.push(objTerm);
						else{
							var termID = objTerm.id;
							if(strSelectedTerms)
								strSelectedTerms +=",";
							
							strSelectedTerms += termID;
						}
					}
															
					//replace mode 
					
					var modeReplace = objFilter.data("replace-mode");
					if(modeReplace === true)
						isReplaceMode = true;
					
					if(isLoadMoreMode == true)
						isNoRefresh = true;
					
					//debug
					if(g_showDebug == true){
						
						trace("Selected Term: ");
						trace(objTerm);
					}
					
				break;
				case g_types.SUMMARY:
					
					isNoRefresh = true;
					
					//take nothing
				break;
				case g_types.SEARCH:
					
					isNoRefresh = true;
					
					var objInput = objFilter.find("input");
					
					search = objInput.val();
					search = search.trim();
					
				break;
				default:
					throw new Error("Unknown filter type: "+type);
				break;
			}
			
			//handle filters init mode
			
			if(isFiltersInitMode == true){
				
				var isInit = objFilter.data("initafter");
				
				if(isInit == false)
					isNoRefresh = true;
			}
			
			//if hidden - no refresh
			var isFilterHidden = objFilter.hasClass(g_vars.CLASS_HIDDEN);
			if(isFilterHidden == true)
				isNoRefresh = true;
			
			objFilter.data("uc_norefresh",false);
			
			var filterRole = objFilter.data("role");
			
			var isMainFilter = (filterRole == "main");
			var isTermChild = (filterRole == "term_child");
			
			//add to refresh filter if it's qualify
			
			var isRefresh = true;
			
			if(isFiltersInitMode == false && (isMainFilter === true || isTermChild == true))
				isRefresh = false;
			
			if(isNoRefresh === true)
				isRefresh = false;
			
			if(isRefresh == true){
				
				var filterWidgetID = getElementWidgetID(objFilter);
				
				//add test tax id's for init mode
				objTaxIDs = getFilterTaxIDs(objFilter, objTaxIDs);
				
				if(strRefreshIDs)
					strRefreshIDs += ",";
				
				strRefreshIDs += filterWidgetID;
				
				objFilter.addClass(g_vars.CLASS_REFRESH_SOON);
			}
					
			
		});		//end filters iteration
		
		
		//add init filters additions
		
		var urlAddition_filtersTest = "";
		var strTaxIDs = getTermDsList(objTaxIDs);
		
		if(isFiltersInitMode == true){
		
			if(!strTaxIDs)
				urlAjax = null;
			else{
				
				if(urlAddition_filtersTest)
					urlAddition_filtersTest += "&";
				
				urlAddition_filtersTest += "modeinit=true";
			}
		}
		
		if(strTaxIDs){
			if(urlAddition_filtersTest)
				urlAddition_filtersTest += "&";
			
			urlAddition_filtersTest += "testtermids="+strTaxIDs;
		}
		
		g_lastSyncGrids = null;
		
		if(urlAjax == null)
			return(null);
		
		var dataLayout = getElementLayoutData(objGrid, addSyncedGrids);
		
		var widgetID = dataLayout["widgetid"];
		var layoutID = dataLayout["layoutid"];
		
		//disable synced
		
		if(addSyncedGrids == false){
			
			var syncedWidgetIDs = false;
			g_lastSyncGrids = null;
			
		}else{
			
			var syncedWidgetIDs = getVal(dataLayout,"synced_widgetids");
			g_lastSyncGrids = getVal(dataLayout,"synced_grids");
		}
		
		
		var urlFilterString = "";
		
		var urlAddition = "ucfrontajaxaction=getfiltersdata&layoutid="+layoutID+"&elid="+widgetID;
		
		urlAjax = addUrlParam(urlAjax, urlAddition);
		
		if(syncedWidgetIDs)
			urlAjax += "&syncelids="+syncedWidgetIDs;
		
		if(urlAddition_filtersTest)
			urlAjax = addUrlParam(urlAjax, urlAddition_filtersTest);
		
		if(page){
			urlAjax += "&ucpage="+page;
			
			urlReplace = addUrlParam(urlReplace, "ucpage="+page);
		}
				
		if(numItems)
			urlAjax += "&uccount="+numItems;
		
		if(arrTerms.length){
			
			var strTerms = buildTermsQuery(arrTerms);
			if(strTerms)
				urlAjax += "&ucterms="+strTerms;
			
			//set the url params as well
			
			urlReplace = addUrlParam(urlReplace, "ucterms="+strTerms);
			
			urlFilterString = addUrlParam(urlFilterString, "ucterms="+strTerms);
		}
		
		if(isFiltersInitMode && strSelectedTerms)
			urlAjax += "&ucinitselectedterms="+strSelectedTerms;
		
		//add refresh ids
		if(strRefreshIDs)
			urlAjax += "&addelids="+strRefreshIDs;
		
		if(isReplaceMode == true)
			urlAjax += "&ucreplace=1";
		
		//search
		if(search){
			search = encodeURIComponent(search);
			//search = escape(search);
			urlAjax += "&ucs=" + search;
			
			urlFilterString = addUrlParam(urlFilterString, "ucs=" + search);
		}
		
		//avoid duplicates - exclude, disable the offset
		
		if(objGrid.hasClass("uc-avoid-duplicates")){
			
			var strExcludePostIDs = getExcludePostIDs();
			
			if(strExcludePostIDs){
				urlAjax += "&ucexclude="+strExcludePostIDs;
				offset = null;
				
				urlFilterString = addUrlParam(urlFilterString, "ucexclude=" + strExcludePostIDs);
			}
			
		}
		
		if(offset){
			urlAjax += "&ucoffset="+offset;
			
			urlFilterString = addUrlParam(urlFilterString, "offset=" + offset);
		}
		
		//remove the "?" from first
		if(urlFilterString)
			urlFilterString = urlFilterString.substring(1);
		
		
		var output = {};
		output["ajax_url"] = urlAjax;
		output["url_replace"] = urlReplace;
		output["terms"] = arrTerms;
		output["filters_string"] = urlFilterString;
		
		return(output);
	}
	
	
	/**
	 * get all exclude post ids from all avoid duplicates grids
	 */
	function getExcludePostIDs(){
		
		var objGrids = jQuery(".uc-avoid-duplicates");
		
		if(objGrids.length == 0)
			return("");
		
		var strIDs = "";
		
		jQuery.each(objGrids, function(index, grid){
			var objGrid = jQuery(grid);
			
			var postIDs = objGrid.data("postids");
			
			if(!postIDs)
				return(true);
			
			if(strIDs)
				strIDs += ",";
			
			strIDs += postIDs;
		});
		
		
		return(strIDs);
	}
	
	
	function ________INIT_______________(){}
	
		
	/**
	 * init listing object
	 */
	function initGridObject(){
		
		//init the listing
		g_objGrid = jQuery("."+ g_vars.CLASS_GRID);
		
		if(g_objGrid.length == 0){
			g_objGrid = null;
			return(false);
		}
		
		//set only available grid
		if(g_objGrid.length > 1){
			g_objGrid = null;
		}
		
	}
	
		
	
	/**
	 * init the globals
	 */
	function initGlobals(){
		
		if(typeof g_strFiltersData === "undefined")
			return(false);
		
		g_filtersData = JSON.parse(g_strFiltersData);
		
		if(jQuery.isEmptyObject(g_filtersData)){
			
			trace("filters error - filters data not found");
			return(false);
		}
		
		g_urlBase = getVal(g_filtersData, "urlbase");
		g_urlAjax = getVal(g_filtersData, "urlajax");
		
		var isShowDebug = getVal(g_filtersData, "debug");
		
		if(isShowDebug == true)
			g_showDebug = true;

		if(g_showDebug == true)
			trace("Show Filters Debug");
			
		
		if(!g_urlBase){
			trace("ue filters error - base url not inited");
			return(false);
		}

		if(!g_urlAjax){
			trace("ue filters error - ajax url not inited");
			return(false);
		}
		
		return(true);
	}
	
	
	/**
	 * init filter and bing to grid
	 */
	function initFilter(objFilter, type){
		
		var objGrid = getClosestGrid(objFilter);
		
		var error = "Filter Parent not found! Please put the posts element on the page, and turn on 'Enable Post Filtering' option on it";
		
		if(!objGrid){			
			showElementError(objFilter, error);
			return(null);
		}
		
		var isAjax = objGrid.data("ajax");
		
		if(isAjax == false){
			showElementError(objFilter, error);
			return(false);
		}
		
		//bind grid to filter
		objFilter.data("grid", objGrid);
				
		//bind filter to grid
		bindFilterToGrid(objGrid, objFilter);
		
		
		switch(type){
			case g_types.SELECT:
				initSelectFilter(objFilter);
			break;
		}
		
		objFilter.trigger(g_vars.EVENT_INIT_FILTER);
		
	}
	
	
	/**
	 * init filter events by types
	 */
	function initFilterEventsByTypes(arrTypes, objFilters){
		
		if(!arrTypes || arrTypes.length == 0)
			return(false);
		
		//init the events
		var objParent = objFilters.parents(".elementor");
		
		if(objParent.length > 1)
			objParent = jQuery(objParent[0]);
		
		for(var type in arrTypes){
						
			switch(type){
				case g_types.PAGINATION:
					
					objParent.on("click",".uc-filter-pagination a", onAjaxPaginationLinkClick);
					
				break;
				case g_types.LOADMORE:
					
					//load more
					objParent.on("click",".uc-filter-load-more__link", onLoadMoreClick);
				break;
				case g_types.TERMS_LIST:
					
					objParent.on("click",".ue_taxonomy.uc-grid-filter a.ue_taxonomy_item", onTermsLinkClick);
					
				break;
				case g_types.SEARCH:
					
					//do nothing for init
					
				break;
				case g_types.SELECT:
					
					objParent.on("change", ".uc-select-filter__select", onTermsLinkClick);
					
				break;
				case g_types.SUMMARY:
					
					//do nothing for now
					
				break;
				default:
					trace("init by type - unrecognized type: "+type);
				break;
			}
		}
				
	}
	
	
	/**
	 * init pagination filter
	 */
	function initFilters(){
		
		var objFilters = jQuery(".uc-grid-filter,.uc-filter-pagination");
		
		if(g_showDebug == true){
			
			trace("init filters");
			
			if(objFilters.length == 0)
				trace("no filters found");
			else
				trace(objFilters);
		}
		
		if(objFilters.length == 0)
			return(false);
		
		var arrTypes = {};
		
		jQuery.each(objFilters, function(index, filter){
			
			var objFilter = jQuery(filter);
			var type = getFilterType(objFilter);
			
			initFilter(objFilter, type);
						
			arrTypes[type] = true;
			
		});
		
		
		initFilterEventsByTypes(arrTypes, objFilters);
		
	}
	
	
	/**
	 * check and call ajax init filters
	 */
	function ajaxInitFilters(){
		
		var objGrids = getAllGrids();
		
		if(objGrids.length == 0)
			return(false);
						
		jQuery.each(objGrids, function(index, grid){
			
			var objGrid = jQuery(grid);
			
			var objInitFilters = objGrid.data("filters_init_after");
						
			if(!objInitFilters || objInitFilters.length == 0)
				return(true);
			
			refreshAjaxGrid(objGrid, "filters");
			
		});
				
		
	}
	
	
	
	
	/**
	 * init events
	 */
	function initEvents(){
		
		addEventListener('popstate', onPopState);
		
		//init grids events
		
		var objGrids = jQuery("."+ g_vars.CLASS_GRID);
		
		if(objGrids.length == 0)
			return(false);
		
		
		//grid several action
		
		objGrids.on(g_vars.ACTION_REFRESH_GRID,function(){
			
			var objGrid = jQuery(this);
			refreshAjaxGrid(objGrid);
		});
		
		
		
		objGrids.on(g_vars.ACTION_GET_FILTERS_URL,function(){
			
			var objGrid = jQuery(this);
			
			var urlFilters = getGridUrlFiltersString(objGrid);
			
			return(urlFilters);
		});
		
		
	}
	
	
	
	/**
	 * set init state ajax url for each grid (for go back)
	 */
	function initOriginGridAjaxUrls(){
		
		var objGrids = getAllGrids();
		
		jQuery.each(objGrids, function(index, grid){
			
			var objGrid = jQuery(grid);
			
			var behave = objGrid.data("filterbehave");
						
			if(behave != "mixed_back")
				return(true);
			
			//get all grid filters
			var objFilters = objGrid.data("filters");
			
			if(!objFilters)
				return(false);
			
			if(objFilters.length == 0)
				return(false);
			
			var objAjaxOptions = getGridAjaxOptions(objFilters, objGrid);
			
			var ajaxUrlInit = getVal(objAjaxOptions, "ajax_url");
			
			objGrid.data("initajaxurl", ajaxUrlInit);
			
		});
		
		
	}

	/**
	 * validate the grids
	 */
	function validateGrid(objGrid){
		
		//check for |raw absence
		
		var isAjax = objGrid.data("ajax");
		
		if(isAjax === "'true'")
			showElementError(objGrid, "This grid configured wrong way, missing |raw in html attributes");
		
	}
	
	
	/**
	 * add some validation to the grids
	 */
	function validateGrids(){
		
		var objGrids = getAllGrids();
		
		jQuery.each(objGrids, function(index, grid){
			
			var objGrid = jQuery(grid);
			
			validateGrid(objGrids);
		});
	}
	
	
	/**
	 * init
	 */
	function init(){
		
		g_objBody = jQuery("body");
		
		var success = initGlobals();
		
		//run again on fail 3 times
		if(success == false){
			
			if(typeof window.ueFiltersTimeoutCounter != "undefined")
					window.ueFiltersTimeoutCounter++;
			else
				window.ueFiltersTimeoutCounter = 0;
			
			if(window.ueFiltersTimeoutCounter == 3){
				trace("Failed to init filters");
				return(false);
			}
			
			setTimeout(init, 200);
			
			return(false);
		}
		
		//init remote object if exists
		if(typeof UERemoteConnection == "function")
			g_remote = window.ueRemoteConnection;
		
		validateGrids();
		
		//init the single grid object
		initGridObject();
		
		initFilters();
		
		ajaxInitFilters();
		
		//set initial ajax url
		initOriginGridAjaxUrls();
		
		initEvents();
		
	}
	
	
	/**
	 * is element in viewport
	 */
	this.isElementInViewport = function(objElement) {
		  
		  var elementTop = objElement.offset().top;
		  var elementBottom = elementTop + objElement.outerHeight();

		  var viewportTop = jQuery(window).scrollTop();
		  var viewportBottom = viewportTop + jQuery(window).height();

		  return (elementBottom > viewportTop && elementTop < viewportBottom);
	}
	
	/**
	 * run function with trashold
	 */
	this.runWithTrashold = function(func, trashold){
		
		if(g_vars.trashold_handle)
			clearTimeout(g_vars.trashold_handle);
		
		g_vars.trashold_handle = setTimeout(func, trashold);
		
	};
	
	
	/**
	 * init the class
	 */
	function construct(){
		
		if(!jQuery){
			trace("Filters not loaded, jQuery not loaded");
			return(false);
		}
		
		jQuery("document").ready(init);
		
	}
	
	construct();
}

g_ucDynamicFilters = new UEDynamicFilters();


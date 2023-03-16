(function($) {

  function getSortableUl($select) {
	
	  var objParent = $select.parent();
	  var objUl = objParent.find(".select2-container ul");
	  	  
    return objUl;
  };
  
  /**
   * get list items
   */
  function getListItems(objUL){
	  
	  var objItems = objUL.find("li.select2-selection__choice");
	  
	  return(objItems);
  }
  
  /**
   * get list items values
   */
  function getObjULValues(objUL){
	  
	  var objItems = getListItems(objUL);
	  
	  var objValues = {};
	  
	  jQuery.each(objItems,function(index, li){
		  
		  var objItem = jQuery(li);
		  
		  var value = objItem.data("value");
		  
		  objValues[value] = null;
	  });
	  
	  
	  return(objValues);
  }

  /**
   * get list items values
   */
  function getArrULValues(objUL){
	  
	  var objItems = getListItems(objUL);
	  
	  var arrValues = [];
	  
	  jQuery.each(objItems,function(index, li){
		  
		  var objItem = jQuery(li);
		  
		  var value = objItem.data("value");
		  var text = objItem.text();
		  
		  text = text.substring(1);
		  		  
		  value = value.toString();
		  
		  arrValues.push([value,text]);
	  });
	  
	  
	  return(arrValues);
  }
  
  
  /**
   * get select from ul
   */
  function getSelectFromUL(objUL){
	
	  var objContainer = objUL.parents(".select2-container");
	  var objSelectInput = objContainer.siblings("select");
	  
	  return(objSelectInput);
  }
  
  /**
   * update values
   */
  function updateLIValues(objUL, arrValues){
	  
	  var objItems = getListItems(objUL);
	  
	  if(objItems.length != arrValues.length){
		  trace("num items not match!");
		  return(false);
	  }
	  
	  jQuery.each(arrValues, function(index, value){
		  
		  var objItem = jQuery(objItems[index]);
		  
		  objItem.attr("data-value",value);
	  });
	  
  }
  
  
  /**
   * init sortable ul
   */
  function initSortableUl($ul, options) {
	
	if($ul.length == 0){
		trace("no url found");
		return(false);
	}
	
    $ul.sortable({
      forcePlaceholderSize: true,
      items: 'li.select2-selection__choice',
      placeholder : '<li>&nbsp;</li>',
      start:function(event){
    	  
    	  var objUL = jQuery(event.target);
    	  var objSelect = getSelectFromUL(objUL);    	  
    	  var arrValues = objSelect.val();
    	      	  
    	  updateLIValues(objUL, arrValues);
    	  
      },
      update:function(event){
    	      	  
    	  var objUL = jQuery(event.target);
    	  
    	  var objSelectInput = getSelectFromUL(objUL);
    	  
    	  var arrInitIDs = [];
    	  
    	  var arrValues = getArrULValues(objUL);
    	  
    	  if(arrValues.length == 0)
    		  return(false);
    	  
    	  if(!arrValues[0])
    		  return(false);
    	  
    	  objSelectInput.html("");
    	  
    	  for(var index in arrValues){
    		  
    		  var item = arrValues[index];
    		  
    		  var value = item[0];
    		  var text = item[1];
    		  
        	  var option = new Option(text, value, true, true);
        	  
        	  objSelectInput.append(option);
    	  }
    	      	  
    	  objSelectInput.trigger("change");
    	  
      }
    
    });
    
    
  };
  
    
  function trace(str){
	  
	  console.log(str);
  }
  
  function initSelect2Sortable($select) {
		  
    var observer,
        $ul;
    
    //$select.select2();
    $ul = getSortableUl($select);
    
    if($ul.length == 0){
    	
    	return(false);
    }
        
    observer = new MutationObserver(function(mutations) {
      initSortableUl($ul);
      observer.disconnect();
    });

    $select.on('select2-selecting', function() {
      observer.observe($ul.get(0), { subtree: false, childList: true, attributes: false });
    });

    initSortableUl($ul, { bindSortEvent: true, $select: $select });
    
    $select.data('hasSelect2Sortable', true);
  };


  function sortSelect2Sortable($select, val) {
	  
    var $ul = getSortableUl($select),
        $lis = $ul.find('.select2-search-choice');

    $.each(val, function(i, id) {
      $lis.each(function() {
        if (id == $(this).data('select2Data').id) {
          $(this).insertBefore($ul.find('.select2-search-field'));
        }
      });
    });

    $ul.trigger('sortupdate');
  }

  $.fn.extend({

    select2Sortable: function(val) {
      
      var objSelect = jQuery(this);
      
      var hasInit = objSelect.data('hasSelect2Sortable');
      
      if(hasInit)
    	  return(this);
      
     initSelect2Sortable(objSelect);
        
      

      return this;
    }
  });
}(window.jQuery));

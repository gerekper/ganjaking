"use strict";

//version: 1.10

function UnlimitedElementsForm(){
  
  var t = this;
  
  //selectors
  var ueInputFieldSelector, ueNumberSelector, ueNumberErrorSelector, ueOptionFieldSelector;
  
  //objects
  var g_objCalcInputs;
  
  //helpers
  var g_allowedSymbols, g_parents = [];
  
  /**
  * trace
  */
  function trace(str){
    console.log(str);
  }
  
  /**
  * show custom error
  */
  function showCustomError(objError, errorText, consoleErrorText){
    
    objError.text(errorText);
    
    objError.show();
    
    var objErrorParent = objError.parents(".debug-wrapper");
    
    if(!objErrorParent.length)
    throw new Error(consoleErrorText);    
    
    objErrorParent.addClass("ue_error_true");
    
    throw new Error(consoleErrorText); 
    
  }
  
  /**
  * get formula names
  */
  function getFormulaNames(expr, objError){ 
    
    var regex = /\[(.*?)\]/g;
    
    var matches = expr.match(regex);   
    
    var names;    
    
    if(matches)      
    names = matches.map(match => match.substring(1, match.length - 1));     
    
    if(names == undefined)
    return(false);
    
    //check for space inside name
    names.forEach(function(name, index){
      
      for (var i = 0; i < name.length; i++) {
        
        var currentChar = name[i];
        
        if(currentChar === " "){
          
          var errorText = 'Unlimited Elements Form Error: Name option must not contain spaces inside. Found in name: '+name;
          var consoleErrorText = "Space character in name found";
          
          showCustomError(objError, errorText, consoleErrorText);
          
        }
        
      }
      
    });
    
    //remove spacing in case they were not removed before
    expr = expr.replace(/\s+/g, "");
    
    var unmatches = expr.replace(regex, "").split(/[\[\]]/);
    
    //exclude allowed symbols and check if array is empty, if not - then in formula some fields written without square parentacess
    unmatches = unmatches[0].replace(g_allowedSymbols, "").split(/[\[\]]/);
    
    if(unmatches[0].length > 0){
      
      var errorText = 'Unlimited Elements Form Error: Input Name should be surrounded by square parentheses inside Formula';
      var consoleErrorText = "Missing square parentheses inside Formula";
      
      showCustomError(objError, errorText, consoleErrorText);
      
    }    
    
    return(names);
    
  }
  
  /**
  * replace fields name with its values
  */
  function replaceNamesWithValues(expr, objError){
    
    var names = getFormulaNames(expr, objError);
    
    if(names == undefined || names == false)
    return(expr);
    
    names.forEach(function(name, index){
      
      var objInpput = jQuery(ueInputFieldSelector+'[name="'+name+'"]');
      
      if(!objInpput.length){
        
        var errorText = 'Unlimited Elements Form Error: couldn"t find Number Field Widget with name: '+name;
        var consoleErrorText = "Invalid Number Field Widget Name";
        
        showCustomError(objError, errorText, consoleErrorText);
        
      }
      
      if(objInpput.length > 1){
        
        var errorText = 'Unlimited Elements Form Error: Name option must be unique. Found '+objInpput.length+' Number Field Widgets with name: '+name;
        var consoleErrorText = "Invalid Number Field Widget Name";
        
        showCustomError(objError, errorText, consoleErrorText);
        
      }
      
      //check if uppercase characters are in name
      for (var i = 0; i < name.length; i++) {
        
        var currentChar = name[i];
        
        if (currentChar === currentChar.toUpperCase() && currentChar !== currentChar.toLowerCase()){
          
          var errorText = 'Unlimited Elements Form Error: Name option must not contain Uppercase characters. Found in name: '+name;
          var consoleErrorText = "Uppercase in name found";
          
          showCustomError(objError, errorText, consoleErrorText);
          
        }
        
        if(currentChar === " "){
          
          var errorText = 'Unlimited Elements Form Error: Name option must not contain spaces inside. Found in name: '+name;
          var consoleErrorText = "Space character in name found";
          
          showCustomError(objError, errorText, consoleErrorText);
          
        }
        
      }
      
      var inputValue = objInpput.val();  
      
      //if input is empty then count it as 0
      if(inputValue.length == 0)
      inputValue = 0;
      
      //see if input value is round number, if so - make sure the number is unformatted
      var dataSeparateThousandsFormat =  objInpput.data("separate-thousands-format");
   		
      if(dataSeparateThousandsFormat == "de-DE"){

        inputValue = Number(inputValue.replace(".", "").replace(",", ""));
       
      }else{
        
        //make sure value is number type (usefull for separating thousand option)
        inputValue = Number(inputValue.toString().replace(",", ''));
     	
      }      		
		
      //add parentheses if valus is less then 0
      if(inputValue < 0)
      inputValue = "("+inputValue+")";	
      
      expr = expr.replace(name, inputValue);
      expr = expr.replace('[', '');
      expr = expr.replace(']', '');
      
    });
    
    return(expr);
    
  }
  
  /*
  * validate the expression
  */
  function validateExpression(expr){  
    
    //allow Math.something (math js operation), numbers, float numbers, math operators, dots, comas    
    var matches = expr.match(g_allowedSymbols);
    
    var result = "";
    
    if (matches) 
    result = matches.join('');    
    
    expr = result;
    
    return(expr);
    
  }
  
  /**
  * get result from expression
  */
  function getResult(expr, objError, objCalcInput) {
    
    //if space just erase it    
    expr = expr.replace(/\s+/g, "");
    
    //replace inputs name with its values
    expr = replaceNamesWithValues(expr, objError);
    
    var result;
    
    //returan closest value from lookup table if needed
    var dataLookupTableMode = objCalcInput.data("lookup-table");
    
    if(dataLookupTableMode == true){
      
      result = getClosestValue(objCalcInput, objError);
      
      return(result);
      
    }    
    
    //validate espression
    expr = validateExpression(expr);
    
    var errorText = `Unlimited Elements Form Error: wrong math operation: ${expr}`;
    var consoleErrorText = `Invalid operation: ${expr}`;
    
    //catch math operation error
    try{
      
      result = eval(expr);
      
      //hide error message after successful calculation
      objError.hide();
      
    }
    
    catch{      
      
      showCustomError(objError, errorText, consoleErrorText);
      
    }
    
    if(isNaN(result) == true){
      
      showCustomError(objError, errorText, consoleErrorText);
      
    }
    
    return result;
    
  }
  
  /**
  * get number formated in fractional number
  */
  function getFractionalResult(result, objCalcInput){
    
    var dataCharNum = objCalcInput.data("char-num");
    
    //replace coma woth period if needed here		
    var dataPeriod = objCalcInput.data("dot-instead-coma");
    
    if(dataPeriod == true){
      
      //change type of the field to "text" (this makes coma change to dot)
      objCalcInput.attr("type", "text");
      
      return(result.toFixed(dataCharNum));  
      
    } else{
      
      return(result.toFixed(dataCharNum))
      
    }		
    
  }
  
  /**
  * format result number
  */
  function formatResultNumber(result, objCalcInput){
    
    var dataFormat = objCalcInput.data("format");
    
    if(dataFormat == "round")
    return(Math.round(result))
    
    if(dataFormat == "floor")
    return(Math.floor(result))
    
    if(dataFormat == "ceil")
    return(Math.ceil(result))
    
    if(dataFormat == "fractional")
    return(getFractionalResult(result, objCalcInput));
    
  }
  
  /**
  * add coma to separate thousands
  */
  function getValueWithSeparatedThousands(val, objCalcInput){
    
    //input can be "text" or "number" type (some old versions of Text Field widget has calculation mode)
    
    var dataSeparateThousands = objCalcInput.data("separate-thousands");
    
    //if no such attribute exit function
    if(!dataSeparateThousands)
    return(val)
    
    //if it set to false exit too
    if(dataSeparateThousands == false)
    return(val);
    
    var inputType = objCalcInput.attr("type");
    
    //if type is not "text" then make it "text"
    if(inputType != "text")
    objCalcInput.attr("type", "text");
    
    var dataSeparateThousandsFormat = objCalcInput.data("separate-thousands-format");
    
    if(!dataSeparateThousandsFormat)
    dataSeparateThousandsFormat = "en-US";
    
    val = val.toString().split(".");
    
    //different format available only for round numbers, if number isn't round then format only with coma
    if(val.length > 1 && dataSeparateThousandsFormat == "en-US")
    val = parseFloat(val[0]).toLocaleString(dataSeparateThousandsFormat) + '.' + val[1];
    else
    val = parseFloat(val[0]).toLocaleString(dataSeparateThousandsFormat)
    
    return(val);
    
  }
  
  /**
  * init calc mode
  */
  function setResult(objCalcInput, objError){
    
    //if data formula is empty
    var dataFormula = objCalcInput.data("formula");
    
    if(dataFormula == undefined)
    return(false);
    
    //get result with numbers instead of fields name
    var result = getResult(dataFormula, objError, objCalcInput);
    
    //format result
    result = formatResultNumber(result, objCalcInput);
    
    //separate thousands
    result = getValueWithSeparatedThousands(result, objCalcInput);
    
    //set result to input
    objCalcInput.val(result);
    
    //set readonly attr if needed
    var dataRemoveReadonlyCalcMode = objCalcInput.data("remove-readonly-for-calc-mode");
    var inputType = objCalcInput.attr("type");
    
    if(dataRemoveReadonlyCalcMode == false || inputType != "number")
    objCalcInput.attr('readonly', '');
    
  }
  
  /**
  * input change controll
  */
  function onInputChange(objCalcInput){
    
    objCalcInput.trigger("input_calc");
    
  }
  
  /**
  * get parent object
  */
  function getParentInput(dataName){
    
    dataName = dataName.replace('[', '');
    dataName = dataName.replace(']', '');
    
    var objInput = jQuery(ueInputFieldSelector+'[name="'+dataName+'"]');
    
    return(objInput);
    
  }
  
  /**
  * assign parent for lookup table fields
  */
  function assignParentsForLookupTable(objParent, parentAttrName){
    
    var parentIdAttribute = objParent.attr("id");
    var objFormula = objParent.find("[data-formula]");
    
    var dataXField = objFormula.data("field-name-x");
    var dataYField = objFormula.data("field-name-y");
    
    var objXField = getParentInput(dataXField);
    var objYField = getParentInput(dataYField);
    
    objXField.attr(parentAttrName, parentIdAttribute);
    objYField.attr(parentAttrName, parentIdAttribute);
    
  }
  
  /**
  * remove duplicated values from array
  */
  function removeDuplicatesFromArray(arr) {
    
    var uniqueArray = [];
    
    jQuery.each(arr, function(index, value) {
      
      if (jQuery.inArray(value, uniqueArray) === -1)
      uniqueArray.push(value);
      
    });
    
    return uniqueArray;
  }
  
  /**
  * assign parent calc number field input to each input inside formula
  */
  function assignParentNumberField(objParent, objError){
    
    var objFormula = objParent.find("[data-formula]");
    var expr = objFormula.data("formula");
    var parentIdAttribute = objParent.attr("id");
    var parentAttrName = "data-parent-formula-input";
    var dataLookup = objFormula.data("lookup-table");
    
    if(dataLookup == true){
      
      assignParentsForLookupTable(objParent, parentAttrName);
      
      return(false);
      
    }
    
    var names = getFormulaNames(expr, objError);
    
    if(names == undefined || names == false)
    return(false);
    
    for(let i=0;i<names.length;i++){
      
      var objInpput = getParentInput(names[i]);      
      
      g_parents.push(parentIdAttribute);
      
      g_parents = removeDuplicatesFromArray(g_parents);
      
      objInpput.attr(parentAttrName, g_parents);      
      
    }  
    
  }
  
  /**
  * Parse the CSV data into a two-dimensional array
  */
  function getLookupTable(csvData){
    
    csvData = csvData.split('\n').map(function (row) {
      return row.split(',');
    });
    
    return(csvData);
    
  }
  
  // Function to calculate the Euclidean distance between two points (width, height)
  function euclideanDistance(x1, y1, x2, y2) {
    
    return Math.sqrt(Math.pow(x1 - x2, 2) + Math.pow(y1 - y2, 2));
    
  }
  
  /**
  * find closest value
  */
  function getClosestValue(objCalcInput, objError) {
    
    var dataX = objCalcInput.data("field-name-x");
    var dataY = objCalcInput.data("field-name-y");    
    var objXField = getParentInput(dataX);
    var objYField = getParentInput(dataY);
    
    var csvData = objCalcInput.data("csv");
    var xValue = objXField.val();
    var yValue = objYField.val();
    
    //format csv table
    var lookupTable = getLookupTable(csvData);
    
    if(!xValue){
      
      var errorText = 'Unlimited Elements Form Error: no x-value found.';
      var consoleErrorText = 'Unlimited Elements Form Error: no x-value found.';
      
      showCustomError(objError, errorText, consoleErrorText);
      
    }
    
    if(!yValue){
      
      var errorText = 'Unlimited Elements Form Error: no x-value found.';
      var consoleErrorText = 'Unlimited Elements Form Error: no x-value found.';
      
      showCustomError(objError, errorText, consoleErrorText);
      
    }
    
    //hide error object in case of successful calculation
    objError.hide();
    
    var closestValue = null;
    var closestDistance = Infinity;
    
    for (var row = 1; row < lookupTable.length; row++) {
      
      for (var col = 1; col < lookupTable[row].length; col++) {
        
        var tableValue = lookupTable[row][col];
        var tableX = lookupTable[0][col];
        var tableY = lookupTable[row][0];
        var distance = euclideanDistance(xValue, yValue, tableX, tableY);
        
        if (distance < closestDistance) {
          
          closestDistance = distance;
          closestValue = tableValue;
          
        }
        
      }
      
    }
    
    return +closestValue;
    
  }
  
  /**
  * get parent input calc
  */
  function getParentCalcInput(objInput){
    
    var parentsArray = objInput.attr("data-parent-formula-input");
    
    var objParentsArray = [];
    
    //make sure attr is an array
    if(parentsArray != undefined){
      
      parentsArray = parentsArray.split(",");
      
      parentsArray.forEach(function(id, index){
        
        var parentId = id;
        var objParentCalcInput = jQuery("#"+parentId).find("[data-calc-mode='true']");
        
        objParentsArray.push(objParentCalcInput);
        
      });
      
      return(objParentsArray);
      
    }
    
  }
  
  /**
  * show main input
  */
  function showField(objFieldWidget, classHidden){
    
    objFieldWidget.removeClass(classHidden);
    
  }
  
  /**
  * hide main input
  */
  function hideField(objFieldWidget, classHidden){
    
    objFieldWidget.addClass(classHidden);
    
  }
  
  /**
  * get condition
  */
  function getConditions(visibilityCondition, condition, objFieldValue, fieldValue){
    
    switch (condition) {
      case "=":
      
      visibilityCondition = objFieldValue == fieldValue;
      
      break;
      case ">":
      
      visibilityCondition = objFieldValue > fieldValue;
      
      break;
      case ">=":
      
      visibilityCondition = objFieldValue >= fieldValue;
      
      break;
      case "<":
      
      visibilityCondition = objFieldValue < fieldValue;
      
      break;
      case "<=":
      
      visibilityCondition = objFieldValue <= fieldValue;
      
      break;
      case "!=":
      
      visibilityCondition = objFieldValue != fieldValue;
      
      break;
      
    }
    
    return(visibilityCondition);
    
  }
  
  /**
  * get operator
  */
  function getOperators(operator, visibilityOperator){
    
    switch (operator){
      
      case "and":
      
      visibilityOperator = "&&";
      
      break;
      case "or":
      
      visibilityOperator = "||";
      
      break;
      
    }   
    
    return(visibilityOperator);
    
  }
  
  
  /**
  * get names
  */
  function getNames(arrNames, fieldName){
    
    arrNames = [];
    arrNames.push(fieldName);
    
    return(arrNames);
    
  }
  
  /**
  * equal condition and input names error
  */
  function equalConditionInputNameError(objField, arrNames, classError){
    
    var inputName = objField.attr("name");
    
    var isNamesEqual = arrNames.indexOf(inputName) != -1;
    
    if(isNamesEqual == true){
      
      var errorHtml = "<div class="+classError+">Unlimited Field Error: can't set condition. Condition Item Name equals Field Name: [ " + inputName + " ]. Please use different names.</div>";
      
      jQuery(errorHtml).insertBefore(objField.parent());
      
    }
    
  }
  
  /**
  * set visibility in editor
  */
  function setVisibilityInEditor(objFieldWidget, classError, classHidden){    
    
    var hiddenHtml = "<div class="+classError+">Unlimited Field is hidden due to Visibility Condition Options. <br> This message shows only in editor.</div>";
    var objError = objFieldWidget.find("."+classError);
    
    if(objFieldWidget.hasClass(classHidden) == true){
      
      if(!objError || !objError.length)
      objFieldWidget.prepend(hiddenHtml);
      
    }else{
      
      if(objError && objError.length)
      objError.remove();
      
    }
    
  }
  
  /**
  * check if calculator input includes invisible inputs
  */
  function checkInvisibleInputsInFormula(){
    
    //if no calc mode inpu found on page - do nothing
    if(!g_objCalcInputs.length)
    return(false);			
    
    //look after each calc mode input field on a page
    g_objCalcInputs.each(function(){
      
      var objCalcInput = jQuery(this);
      
      //find main warapper of the widget
      var objCalcWidget = objCalcInput.parents(ueNumberSelector);		
      var objError = objCalcWidget.find(ueNumberErrorSelector);
      var formula = objCalcInput.data('formula');
      
      if(!formula)
      return(true);
      
      var names = getFormulaNames(formula, objError);
      
      if(names == undefined || names == false)
      return(false);
      
      names.forEach(function(name, index){
        
        var objInpput = jQuery(ueInputFieldSelector+'[name="'+name+'"]');
        
        //check if field is hidden due to condition
        // if(objInpput.is(':visible') == false){
        
        //   var errorText = 'Unlimited Elements Form Error: Field is invisible on the page, but contains in formula: '+name+'.';
        //   var consoleErrorText = `Field is invisible on the page, but contains in formula: ${name}`;
        
        //   showCustomError(objError, errorText, consoleErrorText);
        
        // }
        
      });
      
    });    
    
  }
  
  /**
  * recalculate parent inputs
  */
  function recalculateParentInputs(objParentCalkInputs){
    
    if(objParentCalkInputs != undefined){
      
      objParentCalkInputs.forEach(function(parent, index){
        
        onInputChange(parent);          
        
      });
      
    }
    
  }
  
  /*
  * process the visibility array
  */
  t.setVisibility = function(conditionArray, widgetId){	  
    
    var objFieldWidget = jQuery("#"+widgetId);
    var classHidden = "ucform-has-conditions";
    var classError = "ue-error";
    
    var conditions = conditionArray.visibility_conditions;
    var conditionsNum = conditions.length;
    
    if(conditionsNum == 0)
    return(false);
    
    var totalVisibilityCondition;
    
    //create val to contain all the names for errors catching purposes
    var arrNames;
    
    for(let i=0; i<conditionsNum; i++){
      
      var conditionArray = conditions[i];
      var condition = conditionArray.condition;
      var fieldName = conditionArray.field_name;
      var fieldValue = parseInt(conditionArray.field_value);
      var operator = conditionArray.operator;
      var id = conditionArray._id;
      
      var objField = jQuery(ueInputFieldSelector+'[name="'+fieldName+'"]');

      var objFieldValue = parseInt(objField.val());
            
      //sets the condition: "==", ">", "<" ...
      var visibilityCondition = getConditions(visibilityCondition, condition, objFieldValue, fieldValue);
      
      //set the conditions: "&&", "||"
      var visibilityOperator = getOperators(operator, visibilityOperator);             
      
      //if only one item exist - ignore the condition ("&&", "||")
      if(i == 0)
      totalVisibilityCondition = visibilityCondition;
      
      if(i > 0)
      totalVisibilityCondition += visibilityOperator + visibilityCondition;      
      
      //show error if condition name equals input field name
      arrNames = getNames(arrNames, fieldName);
      
      var objInputField = objFieldWidget.find(ueInputFieldSelector);
      
      equalConditionInputNameError(objInputField, arrNames, classError);
      
    }
    
    var isInEditor = objField.data("editor");
    
    if(eval(totalVisibilityCondition) == true){
      
      showField(objFieldWidget, classHidden);
      
      if(isInEditor == "yes")
      setVisibilityInEditor(objFieldWidget, classError, classHidden);
      
    }
    
    if(eval(totalVisibilityCondition) == false){      
      
      hideField(objFieldWidget, classHidden);
      
      if(isInEditor == "yes")
      setVisibilityInEditor(objFieldWidget, classError, classHidden);
      
    }
    
    //check if in formula exists invisible field
    checkInvisibleInputsInFormula();
    
  }
  
  /**
  * init the form
  */
  t.init = function(){
    
    //if no calc mode inpu found on page - do nothing
    if(!g_objCalcInputs.length)
    return(false);
    
    //look after each calc mode input field on a page
    g_objCalcInputs.each(function(){
      
      var objCalcInput = jQuery(this);
      
      //find main warapper of the widget
      var objCalcWidget = objCalcInput.parents(ueNumberSelector);		
      var objError = objCalcWidget.find(ueNumberErrorSelector);
      
      //assign parent calc input number field widget for each ue field input that is inside formula of the number filed
      assignParentNumberField(objCalcWidget, objError);
      
      //set result in input
      setResult(objCalcInput, objError);    
      
      //set result on custom shange event
      objCalcInput.on('input_calc', function(){
        
        var objInput = jQuery(this); //triggered input        
        
        setResult(objInput, objError);
        
        var objParentCalkInputs = getParentCalcInput(objInput); //parent calc input with formula attr
        
        if(objParentCalkInputs != undefined){
          
          objParentCalkInputs.forEach(function(parent, index){
            
            var objParentError = parent.find(ueNumberErrorSelector);
            
            setResult(parent, objParentError);         
            
          });
          
        }
        
        
      });
      
    });
    
    //init events
    var objAllInputFields = jQuery(ueInputFieldSelector);
    
    //on input change trigger only parent calc number field, not all of them
    objAllInputFields.on('input', function(){
      
      var objInput = jQuery(this); //triggered input
      var objParentCalkInputs = getParentCalcInput(objInput); //parent calc input with formula attr
      
      recalculateParentInputs(objParentCalkInputs);   
      
    });
    
    objAllInputFields.each(function(){
      
      var objInput = jQuery(this); //triggered input
      var objParentCalkInputs = getParentCalcInput(objInput); //parent calc input with formula attr
      var dataCalcMode = objInput.data("calc-mode");
      
      if(dataCalcMode === false)
      return(true);
      
      recalculateParentInputs(objParentCalkInputs);
      
    });
    
  }
  
  /**
  * init vars
  */
  function initVars(){
    
    //selector
    ueInputFieldSelector = ".ue-input-field";
    ueNumberSelector = ".ue-number, .ue-content";
    ueNumberErrorSelector = ".ue-number-error";
    ueOptionFieldSelector = ".ue-option-field";
    
    //objects
    g_objCalcInputs = jQuery(ueInputFieldSelector+'[data-calc-mode="true"]');
    
    //helpers
    g_allowedSymbols = /Math\.[a-zA-Z]+|\d+(?:\.\d+)?|[-+*/().,]+/g;
    
  }
  
  initVars();
  
}

var g_ucUnlimitedForms = new UnlimitedElementsForm();

g_ucUnlimitedForms.init();

jQuery( document ).on( 'elementor/popup/show', (event, id, objPopup) => {
  
  var g_ucUnlimitedForms = new UnlimitedElementsForm();
  
  g_ucUnlimitedForms.init();
  
});	
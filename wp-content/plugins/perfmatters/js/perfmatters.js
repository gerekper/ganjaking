//Perfmatters Admin JS
jQuery(document).ready(function($) {

	//tab-content display
	$('.perfmatters-subnav > a').click(function(e) {

		e.preventDefault();
					
		//get displaying tab content jQuery selector
		var active_tab = $('.perfmatters-subnav > a.active');		
					
		//find actived navigation and remove 'active' css
		active_tab.removeClass('active');
					
		//add 'active' css into clicked navigation
		$(this).addClass('active');

		//var selected_tab_id = $(this).attr('rel');
		$('#perfmatters-options-form').attr('action', "options.php" + "#" + $(this).attr('rel'));
					
		//hide displaying tab content
		$(active_tab.attr('href')).removeClass('active').addClass('hide');
					
		//show target tab content
		$($(this).attr('href')).removeClass('hide').addClass('active');

		$('#perfmatters-admin .CodeMirror').each(function(i, el) {
		    el.CodeMirror.refresh();
		});
	});

    //tooltip display
	$(".perfmatters-tooltip").hover(function(){
	    $(this).closest("tr").find(".perfmatters-tooltip-text").fadeIn(100);
	},function(){
	    $(this).closest("tr").find(".perfmatters-tooltip-text").fadeOut(100);
	});
	
	//add input row
	$('.perfmatters-add-input-row').on('click', function(ev) {
		ev.preventDefault();

		var rowCount = $(this).prop('rel');

		rowCount++;

		var $container = $(this).closest('.perfmatters-input-row-wrapper').find('.perfmatters-input-row-container');

		var $clonedRow = $container.find('.perfmatters-input-row').last().clone();

		$clonedRow.find(':text, select').val('');
		$clonedRow.find(':checkbox').prop('checked', false);

		perfmattersUpdateRowCount($clonedRow, rowCount);

		$container.append($clonedRow);
		
		$(this).prop('rel', rowCount);
	});

	//delete input row
	$('.perfmatters-input-row-wrapper').on('click', '.perfmatters-delete-input-row', function(ev) {
		ev.preventDefault();

		var siblings = $(this).closest('.perfmatters-input-row').siblings();
		var $addButton = $(this).closest('.perfmatters-input-row-wrapper').find('.perfmatters-add-input-row');

		if($addButton.prop('rel') == 0) {
			$row = $(this).closest('.perfmatters-input-row');
			$row.find(':text, select').val('');
			$row.find(':checkbox').prop("checked", false);
		}
		else {
			$(this).closest('.perfmatters-input-row').remove();
			$addButton.prop('rel', $addButton.prop('rel') - 1);
		}
		
		siblings.each(function(i) {
			perfmattersUpdateRowCount(this, i);
		});
	});

	//input display control
	$('.perfmatters-input-controller input, .perfmatters-input-controller select').change(function() {

		var controller = $(this);

		var inputID = $(this).attr('id');

		var nestedControllers = [];

		$('.' + inputID).each(function() {

			var skipFlag = true;
			var forceHide = false;
			var forceShow = false;

			if($(this).hasClass('perfmatters-input-controller')) {
				nestedControllers.push($(this).find('input').attr('id'));
			}

			var currentInputContainer = this;

			$.each(nestedControllers, function(index, value) {

				var controlChecked = $('#' + value).is(':checked');
				var controlReverse = $('#' + value).closest('.perfmatters-input-controller').hasClass('perfmatters-input-controller-reverse');

	  			if($(currentInputContainer).hasClass(value) && (controlChecked == controlReverse)) {
	  				skipFlag = false;
	  				return false;
	  			}
			});

			if(controller.is('select')) {
				var classNames = this.className.match(/perfmatters-select-control-([^\s]*)/g);
				var foundClass = ($.inArray('perfmatters-select-control-' + controller.val(), classNames)) >= 0;

				if(classNames && (foundClass != $(this).hasClass('perfmatters-control-reverse'))) {
					forceShow = true;
				}
				else {
					forceHide = true;
				}
			}

			if(skipFlag) {
				if(($(this).hasClass('hidden') || forceShow) && !forceHide) {
					$(this).removeClass('hidden');
				}
				else {
					$(this).addClass('hidden');
				}
			}

		});
	});

	//validate input
	$("#perfmatters-admin [perfmatters_validate]").keypress(function(e) {

		//grab input and pattern
		var code = e.which;
		var character = String.fromCharCode(code);
		var pattern = $(this).attr('perfmatters_validate');

		//prevent input if character is invalid
		if(!character.match(pattern)) {
			e.preventDefault();
		}
	});

	//initialize codemirror textareas
	var $codemirror = $('.perfmatters-codemirror');
	if($codemirror.length) {
		$codemirror.each(function() {
			wp.codeEditor.initialize(this, cm_settings);
		});
	}
});

//update row count for given input row attributes
function perfmattersUpdateRowCount(row, rowCount) {
	jQuery(row).find('input, select, label').each(function() {
		if(jQuery(this).attr('id')) {
			jQuery(this).attr('id', jQuery(this).attr('id').replace(/[0-9]+/g, rowCount));
		}
		if(jQuery(this).attr('name')) {
			jQuery(this).attr('name', jQuery(this).attr('name').replace(/[0-9]+/g, rowCount));
		}
		if(jQuery(this).attr('for')) {
			jQuery(this).attr('for', jQuery(this).attr('for').replace(/[0-9]+/g, rowCount));
		}
	});
}
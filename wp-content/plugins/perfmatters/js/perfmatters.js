jQuery(document).ready(function($) {

	//tab-content display
	$('#perfmatters-menu > a').click(function(e) {

		$('.perfmatters-button-message').hide();
					
		var active_tab = $(this).closest('#perfmatters-menu').find('a.active');	
		var selected = $(this).attr('rel');

		active_tab.removeClass('active');
		$('#' + active_tab.attr('rel')).removeClass('active');
					
		$(this).addClass('active');
		$('#' + selected).addClass('active');

		$('#perfmatters-options-form').attr('data-pm-option', selected.split('-')[0]);
		
		$('#perfmatters-admin .CodeMirror').each(function(i, el) {
		    el.CodeMirror.refresh();
		});
	});

	//menu toggle
	var menuToggle = document.getElementById('perfmatters-menu-toggle');
	if(menuToggle) {
		menuToggle.addEventListener('click', function(e) {
			e.preventDefault();
			var header = document.getElementById('perfmatters-menu');
			if(!header.classList.contains('perfmatters-menu-expanded')) {
				header.classList.add('perfmatters-menu-expanded');
			}
			else {
				header.classList.remove('perfmatters-menu-expanded');
			}
		});
	}

    //tooltip display
	$(".perfmatters-tooltip").hover(function() {
	    $(this).closest("tr").find(".perfmatters-tooltip-text").fadeIn(100);
	},function(){
	    $(this).closest("tr").find(".perfmatters-tooltip-text").fadeOut(100);
	});
	
	//add input row
	$('.perfmatters-add-input-row').on('click', function(ev) {
		ev.preventDefault();

		var rowCount = $(this).prop('rel');

		if(rowCount < 1) {
			$(this).closest('.perfmatters-input-row-wrapper').find('.perfmatters-input-row').addClass('perfmatters-opened').show();
		}
		else {
			var $container = $(this).closest('.perfmatters-input-row-wrapper').find('.perfmatters-input-row-container');
			var $clonedRow = $container.find('.perfmatters-input-row').last().clone();

			$clonedRow.addClass('perfmatters-opened');
			$clonedRow.find(':text, select').val('');
			$clonedRow.find(':checkbox').prop('checked', false);

			perfmattersUpdateRowCount($clonedRow, rowCount);

			$container.append($clonedRow);
		}
		rowCount++;

		$(this).prop('rel', rowCount);
	});

	//delete input row
	$('.perfmatters-input-row-wrapper').on('click', '.perfmatters-delete-input-row', function(ev) {
		ev.preventDefault();

		var siblings = $(this).closest('.perfmatters-input-row').siblings();
		var $addButton = $(this).closest('.perfmatters-input-row-wrapper').find('.perfmatters-add-input-row');

		if($addButton.prop('rel') == 1) {
			$row = $(this).closest('.perfmatters-input-row');
			$row.find(':text, select').val('');
			$row.find(':checkbox').prop("checked", false);
			$row.hide();
		}
		else {
			$(this).closest('.perfmatters-input-row').remove();
		}

		$addButton.prop('rel', $addButton.prop('rel') - 1);
		
		siblings.each(function(i) {
			perfmattersUpdateRowCount(this, i);
		});
	});

	//expand input row
	$('.perfmatters-input-row-wrapper').on('click', '.perfmatters-expand-input-row', function(ev) {
		ev.preventDefault();

		$row = $(this).closest('.perfmatters-input-row');

		if($row.hasClass('perfmatters-opened')) {
			$row.removeClass('perfmatters-opened');
		}
		else {
			$row.addClass('perfmatters-opened');
		}
	});

	//quick exclusions
	$(".perfmatters-quick-exclusion-title-bar").click(function(e) {
        var clicked = $(this).closest(".perfmatters-quick-exclusion");
        if(clicked.hasClass("perfmatters-opened")) {
            clicked.removeClass("perfmatters-opened");
        }
        else {
        	clicked.addClass("perfmatters-opened");
        }
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
			var optionSelected = false;

			if($(this).hasClass('perfmatters-input-controller')) {
				nestedControllers.push($(this).find('input, select').attr('id'));
			}

			var currentInputContainer = this;

			$.each(nestedControllers, function(index, value) {
				var currentController = $('#' + value);

				if(currentController.is('input')) {

					var controlChecked = $('#' + value).is(':checked');
					var controlReverse = $('#' + value).closest('.perfmatters-input-controller').hasClass('perfmatters-input-controller-reverse');

		  			if($(currentInputContainer).hasClass(value) && (controlChecked == controlReverse)) {
		  				skipFlag = false;
		  				return false;
		  			}
		  		}
		  		else if(currentController.is('select')) {
		  			var classNames = currentInputContainer.className.match(/perfmatters-select-control-([^\s]*)/g);

		  			if(classNames) {
						var foundClass = ($.inArray('perfmatters-select-control-' + $('#' + value).val(), classNames)) >= 0;
						if(!foundClass) {
							forceHide = true;
						}
					}
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

	//show advanced toggle
	$('#perfmatters-options-form #show_advanced').click(function(e) {
		var container = $('#perfmatters-options');
		var checked = $(this).is(':checked');
		if(checked) {
			container.removeClass('pm-hide-advanced');
		}
		else {
			container.addClass('pm-hide-advanced');
		}
	});
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


jQuery(function($) {

	var pmActionButtonTimeouts = [];

	//action button press
	$('button[data-pm-action]').click(function(e) {

		e.preventDefault();

		//confirmation dialog
		var confirmation = $(this).attr('data-pm-confirmation');
		if(confirmation && !confirm(confirmation)) {
			return;
		}

		//assign variables
		var action = $(this).attr('data-pm-action');
		var button = $(this);
		var container = $(this).closest('.perfmatters-button-container');
		var text = container.find('.perfmatters-button-text');
		var spinner = container.find('.perfmatters-button-spinner');
		var message = container.find('.perfmatters-button-message');

		//reset message
		message.html('');
		message.removeClass('perfmatters-error');
	 	clearTimeout(pmActionButtonTimeouts[action]);

	 	//switch to spinner
	    $(this).attr('disabled', true);
	    text.hide();
	    spinner.css('display', 'block');

	    //setup form data
	    var formData = new FormData();
	    formData.append('action', action);
	    formData.append('nonce', PERFMATTERS.nonce);

	    //additional setup
	    if(action == 'import_settings') {
    		formData.append('perfmatters_import_settings_file', document.getElementById('perfmatters-import-settings-file').files[0]);
	    }
	    else if(action == 'scan_database') {
	    	$('#tools-database .perfmatters-option-data').html('');
	    }
	    else {
	    	var form = $(this).closest('form');
	    	form.find('.CodeMirror').each(function() {
			    this.CodeMirror.save();
			});
	    	formData.append('form', form.serialize());
	    }

	    //ajax request
		$.ajax({
	        type: "POST",
	        url: PERFMATTERS.ajaxurl,
	        data: formData,
	        processData: false,
       		contentType: false
	    })
	    .done(function(r) {

	    	//add message error class
	    	if(!r.success) {
	    		message.addClass('perfmatters-error');
	    	}

	    	//export settings
	    	if(action == 'export_settings' && r.data.export) {
	    		var blob = new Blob([r.data.export], {
			        type: 'application/json'
		      	});
			    var link = document.createElement('a');
			    link.href = window.URL.createObjectURL(blob);

			    var d = new Date();
				var month = d.getMonth()+1;
				var day = d.getDate();
				var dateString = d.getFullYear() + '-' + (month<10 ? '0' : '') + month + '-' + (day<10 ? '0' : '') + day;

			    link.download = 'perfmatters-settings-export-' + dateString + '.json';
			    link.click();
	    	}

	    	//scan database
	    	if(action =='scan_database' && r.data.data) {

				const timer = ms => new Promise(res => setTimeout(res, ms));

				async function loadOptionData () {

					var keys = Object.keys(r.data.data);

					for(var i = 0; i < keys.length; i++) {
				    	$('#database-' + keys[i]).closest('td').find('.perfmatters-option-data').html(r.data.data[keys[i]]);
				    	await timer(250);
				  	}
				}
				loadOptionData();
	    	}
		})
		.fail(function(r) {
			message.addClass('perfmatters-error');
			message.html(PERFMATTERS.strings.failed);
		})
		.always(function(r) {
			
			//show response message
			if(r.data && r.data.message) {
				message.html(r.data.message);
			}
			message.fadeIn();
			clearTimeout(pmActionButtonTimeouts[action]);
			pmActionButtonTimeouts[action] = setTimeout(function() {
				message.fadeOut();
			}, 2500);

			//re-enable button
			button.attr('disabled', false);
			text.show();
	       	spinner.css('display', 'none');

	       	//clear checkboxes
	       	if(action == 'purge_meta') {
	       		$('#perfmatters-purge-meta input:checkbox').removeAttr('checked');
	       	}

	       	//reload page
	       	if(r.data && r.data.reload) {
	       		location.reload();
	       	}
		})
	});
});
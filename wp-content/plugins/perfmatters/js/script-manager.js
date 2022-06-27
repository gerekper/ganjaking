document.addEventListener("DOMContentLoaded", function() {

	//hide loader
	var loader = document.getElementById('pmsm-loading-wrapper');
	if(loader) {
		loader.style.display = "none";
	}

	//group heading containers
	var groupHeadings = document.querySelectorAll(".pmsm-group-heading");

	groupHeadings.forEach(function(groupHeading) {

		groupHeading.addEventListener('change', function(e) {

			var elem = e.target;

			//group status toggle/select
			if(elem.classList.contains('perfmatters-status-toggle') || elem.classList.contains('perfmatters-status-select')) {

				var group = elem.closest('.perfmatters-script-manager-group');

				var table = group.querySelector('.perfmatters-script-manager-section table');
				var disabled = group.querySelector('.perfmatters-script-manager-section .perfmatters-script-manager-assets-disabled');
				var muBadge = group.querySelector('.pmsm-mu-mode-badge');

				if((elem.type == 'checkbox' && elem.checked) || (elem.type == 'select-one' && elem.value == 'disabled')) {

					elem.classList.add('disabled');

					if(table) {
						table.style.display = "none";
					}
					if(disabled) {
						disabled.style.display = "block";
					}
					if(muBadge) {
						muBadge.style.display = "inline-block";
					}
				}
				else {

					elem.classList.remove('disabled');

					if(table) {
						table.style.display = "table";
					}
					if(disabled) {
						disabled.style.display = "none";
					}
					if(muBadge) {
						muBadge.style.display = "none";
					}
				}
			} 
		});
	});

	//section containers
	var sections = document.querySelectorAll(".perfmatters-script-manager-section");

	sections.forEach(function(section) {

		section.addEventListener('change', function(e) {

			var elem = e.target;

			//script status toggle/select
			if(elem.classList.contains('perfmatters-status-toggle') || elem.classList.contains('perfmatters-status-select')) {

				var tr = elem.closest('tr');

				var controls = tr.querySelector('.perfmatters-script-manager-controls');

				if((elem.type == 'checkbox' && elem.checked) || (elem.type == 'select-one' && elem.value == 'disabled')) {

					elem.classList.add('disabled');
					controls.style.display = "block";
				}
				else {

					elem.classList.remove('disabled');
					controls.style.display = "none";
				}
			}

			//disables
			if(elem.classList.contains('pmsm-disable-everywhere')) {
				var controls = elem.closest('.perfmatters-script-manager-controls');

				var enable = controls.querySelector('.perfmatters-script-manager-enable');
				var hideMatches = controls.querySelectorAll('.pmsm-everywhere-hide');

				enable.style.display = (elem.checked ? "block" : "none");

				hideMatches.forEach(function(hide) {
					if(elem.checked) {
						hide.classList.add("pmsm-hide");
					}
					else {
						hide.classList.remove("pmsm-hide");
					}
				});
			}
		});
	});

	//set changed status of selected inputs
	var inputs = document.querySelectorAll("#pmsm-main-form input, #pmsm-main-form select");

	inputs.forEach(function(input) {

		input.addEventListener('change', function(e) {

			var elem = e.target;

			elem.classList.add('pmsm-changed');

			if(elem.type == 'checkbox') {

				var checkboxContainer = elem.closest('.pmsm-checkbox-container');
				var checkboxes = checkboxContainer.querySelectorAll('input');

				checkboxes.forEach(function(checkbox) {
					checkbox.classList.add('pmsm-changed');
				});
			}
		});
	});

	var mainForm = document.getElementById("pmsm-main-form");

	//submit main script manager form
	if(mainForm) {

		mainForm.addEventListener('submit', function(e) {

			//prevent server side submission
			e.preventDefault();

			//disable any inputs that weren't touched
		    var inputs = e.target.querySelectorAll('input:not(.pmsm-changed), select:not(.pmsm-changed)');
		    inputs.forEach(function(input) {
		    	input.disabled = true;
		    });

		    //save button feedback
		    var saveButton = document.querySelector('#pmsm-save input');
		    var saveSpinner = document.querySelector('#pmsm-save .pmsm-spinner');
		    saveButton.value = pmsm.messages.buttonSaving;
		    saveSpinner.style.display = "inline-block";

		    //get form data
		    const formData = new FormData(e.target);
	  		const formDataString = new URLSearchParams(formData).toString();

	  		//ajax request
	    	var request = new XMLHttpRequest();

	    	request.open('POST', pmsm.ajaxURL, true);
	    	request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
	    	request.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
		    request.onload = function() {

		    	//successful request
		        if(this.status >= 200 && this.status < 400) {

		            //setup message variable
		    		var message;

		            if(this.response == 'update_success') {
			    		message = pmsm.messages.updateSuccess;

			    		//if script status was toggled back on, clear child input values
			    		var changedScriptStatusToggles = e.target.querySelectorAll('.perfmatters-script-manager-section .perfmatters-status-toggle.pmsm-changed');
			    		changedScriptStatusToggles.forEach(function(toggle) {
			    			if(!toggle.checked) {

			    				var toggleRow = toggle.closest('tr');

			    				toggleRow.querySelector('.perfmatters-script-manager-enable').style.display = "none";

			    				var rowInputs = toggleRow.querySelectorAll('input');
			    				rowInputs.forEach(function(input) {
			    					if(input.type == "checkbox" || input.type == "radio") {
			    						input.checked = false;
			    					}
			    					else if(input.type == "text") {
			    						input.value = "";
			    					}
			    				});
			    			}
			    		});

			    		//if group status was toggled back on, clear child input values
			    		var changedGroupStatusToggles = e.target.querySelectorAll('.pmsm-group-heading .perfmatters-status-toggle.pmsm-changed');
			    		changedGroupStatusToggles.forEach(function(toggle) {
			    			if(!toggle.checked) {

			    				var toggleGroup = toggle.closest('.perfmatters-script-manager-group');

			    				var toggleGroupAssets = toggleGroup.querySelector('.perfmatters-script-manager-assets-disabled');

			    				toggleGroupAssets.querySelector('.perfmatters-script-manager-enable').style.display = "none";

			    				var groupInputs = toggleGroupAssets.querySelectorAll('input');
			    				groupInputs.forEach(function(input) {
			    					if(input.type == "checkbox" || input.type == "radio") {
			    						input.checked = false;
			    					}
			    					else if(input.type == "text") {
			    						input.value = "";
			    					}
			    				});
			    			}
			    		});

			    		//reset changed inputs
			    		var changedInputs = e.target.querySelectorAll('.pmsm-changed');
				        changedInputs.forEach(function(input) {
				        	input.classList.remove('pmsm-changed');
				        });

			    	}
			    	else if(this.response == 'update_failure') {
			    		message = pmsm.messages.updateFailure;
			    	}
			    	else if(this.response == 'update_nooption') {
			    		message = pmsm.messages.updateNoOption;
			    	}
			    	else if(this.response == 'update_nochange') {
			    		message = pmsm.messages.updateNoChange;
			    	}

			    	//display message
			    	if(message) {
			    		pmsmPopupMessage(message);
			    	}

		        	//reenable form inputs
				    inputs.forEach(function(input) {
				    	input.disabled = false;
				    });

			        saveButton.value = pmsm.messages.buttonSave;
			        saveSpinner.style.display = "none";
		        }
		        else {
		            //failed request response
		            console.log(this.response);
		        }
		    };
	    	request.onerror = function() {
	        	//connection error
	    	};

	    	//send request
	    	request.send('action=pmsm_save&current_id=' + pmsm.currentID + '&pmsm_data=' + encodeURIComponent(formDataString));
		});
	}

	//reset button
	var resetButton = document.getElementById('pmsm-reset');
	if(resetButton) {
		resetButton.addEventListener('click', function(ev) {
			ev.preventDefault();
			var resetForm = document.getElementById('pmsm-reset-form');
			var confirmCheck = confirm(resetForm.getAttribute('pmsm-confirm'));
			if(confirmCheck) {
				resetForm.submit();
			}
		});
	}

	//menu toggle
	var menuToggle = document.getElementById('pmsm-menu-toggle');
	if(menuToggle) {
		menuToggle.addEventListener('click', function(ev) {
			ev.preventDefault();
			var header = document.getElementById('perfmatters-script-manager-header');

			if(window.innerWidth > 782) {
				if(!header.classList.contains('pmsm-header-minimal')) {
					header.classList.add('pmsm-header-minimal');
				}
				else {
					header.classList.remove('pmsm-header-minimal');
				}
			}
			else {
				if(!header.classList.contains('pmsm-header-expanded')) {
					header.classList.add('pmsm-header-expanded');
				}
				else {
					header.classList.remove('pmsm-header-expanded');
				}
			}
			
			
		});
		window.addEventListener('click', function(e) {
			var header = document.getElementById('perfmatters-script-manager-header');
			if(!header.contains(e.target)) {
				header.classList.remove('pmsm-header-expanded');
			}
		});
	}
});

//popup message after submit
function pmsmPopupMessage(message) {

	if(message) {
		var messageContainer = document.getElementById('pmsm-message');

		messageContainer.innerHTML = message;

		messageContainer.classList.add('pmsm-fade');

		setTimeout(function() {
			messageContainer.classList.remove('pmsm-fade');
		}, 2000);
	}
}
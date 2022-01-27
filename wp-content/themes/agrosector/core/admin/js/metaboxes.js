"use strict";

jQuery(window).on('load', function () {
	metabox_dependancy ();
	metabox_tab();
	change_vertical_header_order();
	metabox_select_icon ();
});


function metabox_tab(){
	var title,id,showing_field;
	jQuery( '#advanced-sortables' ).wrapInner( "<div class='gt3_tab_meta_container'></div>" );
	jQuery( '#advanced-sortables' ).append('<div class="gt3_tab_meta_titles"></div>')
	jQuery('#advanced-sortables').find('.postbox ').each(function(){
		title = jQuery(this).find('h2').text();
		id = jQuery(this).attr('id');
		jQuery('#advanced-sortables').find('.gt3_tab_meta_titles').append('<h2 class="'+id+'">'+(title)+'</h2>');
	})

	jQuery('.gt3_tab_meta_container').children().hide();
	jQuery('.gt3_tab_meta_container').find('.postbox:first-child').show();
	jQuery('.gt3_tab_meta_titles h2:first-child').addClass('active');
	jQuery('#advanced-sortables').find('.gt3_tab_meta_titles h2').on("click",function(){
		if (!jQuery(this).hasClass('active')) {
			showing_field = jQuery(this).attr('class');
			jQuery(this).addClass('active').siblings().removeClass('active');
			jQuery('#advanced-sortables #'+showing_field).show().siblings().hide();
		}

	})
}


function metabox_dependancy (){
	jQuery('.rwmb-input').find('[data-dependency]').each(function(){

	var parentContainer,
		parentDepend,
		operationDepend,
		valueDepend,
		parentDependValue;
	parentContainer = jQuery(this).parents('.rwmb-field');
	var dependencyArray = jQuery(this).data('dependency');
	var returni = true;
	var valuee,ret;

	var dependencyArrayLength = dependencyArray[0].length;

	var eachElement = {};
	for (var i = dependencyArrayLength - 1; i >= 0; i--) {

		parentDepend 	= dependencyArray[0][i][0];
		operationDepend = dependencyArray[0][i][1];
		valueDepend 	= dependencyArray[0][i][2];

		if (jQuery('#'+ parentDepend).length) {
			parentDependValue 	= metabox_get_parent_value (jQuery('#'+ parentDepend));
			returni = returni && metabox_compare (parentDependValue ,operationDepend, valueDepend);



			if (typeof(eachElement[parentDepend]) == 'undefined') {
				valuee = [operationDepend, valueDepend]
				var metabox_parent = {};
				metabox_parent['properties'] = [valuee];
				ret = metabox_parent;
				eachElement[parentDepend] = ret;
			}else{
				valuee = [operationDepend, valueDepend];
				var metabox_parent = {};
				eachElement[parentDepend]['properties'].push(valuee);
			}
		}

	}

	for (var i = Object.getOwnPropertyNames(eachElement).length - 1; i >= 0; i--) {
		var selectors = Object.getOwnPropertyNames(eachElement)[i]
		var y = 1;
		jQuery('#'+selectors).on('change',function(){
			selectors = this.getAttribute('id');
			parentDependValue = metabox_get_parent_value (this);
			prop = eachElement[selectors]['properties']
			var returnii = true;
			for (var i = prop.length - 1; i >= 0; i--) {
				returnii = returnii && metabox_compare (parentDependValue ,prop[i][0], prop[i][1])
			}
			if (Object.getOwnPropertyNames(eachElement).length > 1) {
				for (var i = Object.getOwnPropertyNames(eachElement).length - 1; i >= 0; i--) {
					if (true) {
						var selectors = Object.getOwnPropertyNames(eachElement)[i]
						var parentDependValue = metabox_get_parent_value ('#'+selectors);
						var prop = eachElement[selectors]['properties']
						returnii = returnii && metabox_compare (parentDependValue ,prop[0][0], prop[0][1]);
					}
				}
			}
			if (returnii) {
				parentContainer.slideDown(150);
			}else{
				parentContainer.slideUp(150);
			}
		})
	}

	if (returni) {
		parentContainer.show();
	}else{
		parentContainer.hide();
	}

})
}
function metabox_compare (parentDependValue ,operationDepend, valueDepend){
	switch(operationDepend) {
		case '=':
			return parentDependValue === valueDepend;
		break;
		case '!=':
			return parentDependValue !== valueDepend;
		break;
		case 'includes':
			return valueDepend.includes(parentDependValue);
		break;

	}
}

function metabox_get_parent_value (parentDepend){
	parentDepend = jQuery(parentDepend)[0];
	switch(parentDepend.tagName) {
		case 'SELECT':
			return parentDepend.value;
		case 'DIV':
			if (jQuery(parentDepend).attr('id') === 'formatdiv') {
				return jQuery(parentDepend).find('input[type=radio]:checked').val()
			}
		case 'INPUT':
			return jQuery(parentDepend).parent().find('input[type=checkbox]').is(":checked");
		break;
	}
}

function change_vertical_header_order(){
	jQuery('#bottom_header_vertical_order').on('change',function(){
		if (jQuery(this)[0].value === '1') {
			jQuery('body').addClass('bottom_header_vertical_order');
		}else{
			jQuery('body').removeClass('bottom_header_vertical_order');
		}
	})
}

function metabox_select_icon () {
	function update() {
		var $this = jQuery( this ),
			options = $this.data( 'options' );
			function formatState (state) {
			  if (!state.id) { return state.text; }
			  var $state = jQuery(
			    '<span><i class="'+ state.element.value.toLowerCase() + '"/> ' + state.text + '</span>'
			  );
			  return $state;
			};

		options['templateResult'] = formatState;
		options['templateSelection'] = formatState;
		$this.siblings( '.select2-container' ).remove();
		$this.show().select2( options );
	}
	jQuery( ':input.rwmb-select_icon' ).each( update );
	jQuery( '.rwmb-input' ).on( 'clone', ':input.rwmb-select_icon', update );
}



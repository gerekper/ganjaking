(function( $ ) {
	'use strict';
// Display or hide Extra Options > Custom Options group || Workaround for group type.
	function ExtraOptionsCheck(triggered) {
		if($(triggered).is(':checked')){
			if($('input[data-conditional-id=wwob_extra_options][value=custom]').is(':checked')){
				$('.cmb2-id-wwob-extra-custom-option').show();
			}
		}else{
			if($('input[data-conditional-id=wwob_extra_options][value=custom]').is(':checked')){
				$('.cmb2-id-wwob-extra-custom-option').hide();
			}
		}
	}
	$("input#wwob_extra_options").ready( ExtraCustomOptionsCheck(this) );
	$(document).on("change", "input#wwob_extra_options", function () { ExtraOptionsCheck(this) });

	function ExtraCustomOptionsCheck(triggered) {
		if($(triggered).is(':checked')){
			$('.cmb2-id-wwob-extra-custom-option').show();
		}else{
			$('.cmb2-id-wwob-extra-custom-option').hide();
		}
	}
	$('input#wwob_enable_disable1').ready( ExtraCustomOptionsCheck(this) );
	$(document).on("change", "input[data-conditional-id=wwob_extra_options][value=custom]", function () { ExtraCustomOptionsCheck(this) });
	
	function EnableDisableCheck(triggered) {
		if($(triggered).find('input#wwob_enable_disable1').is(':checked')){
			$('.cmb2-id-wwob-group').show();
		}else{
			$('.cmb2-id-wwob-group').hide();
		}
	}
	$(".cmb2-id-wwob-enable-disable").ready( EnableDisableCheck(this) );
	$(document).on("click", ".cmb2-id-wwob-enable-disable", function () { EnableDisableCheck(this) });
	
	// Product Metabox Tabs
	$(document).ready(function($) {
		$('.cmb2-id-wwob-enable-disable').trigger('click');
		$('.tab-content:not(.current)').css('display', 'none');
		$('body').on('click', '.tabs-nav a', function(event) {
			event.preventDefault();
			$(this).parent().addClass("current");
			$(this).parent().siblings().removeClass("current");
			var tab = $(this).attr("href");
			tab = tab.replace("#", "."); // change anchor tag # (id ref) to . (class ref) so it works with repeating sections
			// alert(tab);
			$(this).closest('.cmb-tabs').children('.tab-content').not(tab).css('display', 'none');
			$(this).closest('.cmb-tabs').children(tab).fadeIn();
		});
		$('.cmb-tabs').show();
	
		// Enable sorting repeatable groups
		$( "#wwob_group_repeat" ).sortable({
			tolerance: 'touch',
			drop: function () {
				alert('delete!');
			}
		});
		$( "#wwob_group_repeat" ).disableSelection();
		});
	
})( jQuery );
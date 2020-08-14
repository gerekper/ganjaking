/**
 * Administration
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Product Add-Ons
 * @version 1.0.0
 */
jQuery(document).ready( function($) {
	'use strict';

	if ( typeof yith_wapo_general === 'undefined' ) {
		return false;
	}

	var wc_cp_block_params = {};

	wc_cp_block_params = {
		message: null,
		overlayCSS: {
			background: '#fff',
			opacity: 0.6
		}
	};

	// Select2 & SelectWoo
	if ( typeof $().select2 == 'function' ) {
		$(".wapo-plugin .products_id-select2").select2();
		$(".wapo-plugin .categories_id-select2").select2();
		$(".wapo-plugin .attributes_id-select2").select2();
		$(".wapo-plugin .depend-select2").select2();
	} else if ( typeof $().selectWoo == 'function' ) {
		$(".wapo-plugin .products_id-select2").selectWoo();
		$(".wapo-plugin .categories_id-select2").selectWoo();
		$(".wapo-plugin .attributes_id-select2").selectWoo();
		$(".wapo-plugin .depend-select2").selectWoo();
	}

	/*
	 *	+++++ GROUPS +++++
	 */

	// Delete Group & Add-on
	$('.button.delete_group, .button.delete-addon').click( function (e) {
		var result = window.confirm( yith_wapo_general.confirm_text );
		if ( result == false ) {
			e.preventDefault();
			return false;
		}
		$('.type-row form').remove();
	});

	/*
	 *	+++++ ADD-ONS +++++
	 */

	// Open type new
	$('.wapo-type-edit').click( function() {
		if ( $(this).next('form').is(':visible') ) {
			$(this).next('form').hide();
		} else {
			$(this).next('form').show();
		}
		$(this).next('form').find('select[name="type"]').change();
		$(this).next('form').find('input[name="required"]').change();
	});
	$('.wapo-new-addon').click( function() {
		$(this).hide();
		$(this).next('form').show();
	});
	$('.wapo-new-addon-cancel').click( function() {
		$(this).parent('form').hide();
		$(this).parent('form').prev('.wapo-new-addon').show();
	});

	// Delete Group & Add-on
	$('.button.duplicate-addon').click( function (e) {
		$('.type-row form').remove();
	});

	/*
	 *	+++++ OPTIONS +++++
	 */

	// Manage options table
	$('#wapo-types .options table .option-label input').on( 'change', function(){
		var delete_button = $( '.button.remove-row', $(this).parents('tr') );
		if ( $(this).val() ) { delete_button.fadeIn(); }
		else { delete_button.fadeOut(); }
	});

	// Add new option
	$('#wapo-types .options table .button.add_option').click( function() {
		var tbody = $(this).closest('table').find('tbody');
		var $container = $( this ).parents('.wp-list-table');
		$container.block( wc_cp_block_params );
		var data = { action: 'ywcp_add_new_option', };
		$.post( yith_wapo_general.ajax_url, data, function( response ) {
			tbody.append( response );
			tbody.closest('form').find('select[name="type"]').change();
			$container.unblock();
		} );
	});

	// Duplicate option
	$('.options table').on( 'click', '.button.duplicate-row', function(){
		var option = $(this).parents('tr');
		var clone = option.clone();
		var label = clone.find( 'input[name="options[label][]"]' ).val() + ' (copy)';
		clone.find( 'input[name="options[label][]"]' ).val( label );
		option.after( clone );
	});

	// Rempove option
	$('.options table').on( 'click', '.button.remove-row', function(){
		var result = window.confirm( yith_wapo_general.confirm_text );
		if ( result == true ) { $(this).parents('tr').remove(); }
	});

	// Change type
	$('.type select').on( 'change', function(){
		$(this).parents('form').removeClass().addClass($(this).val());
		changeType( $(this) );
	});

	$('#wapo-types .required input[name="required"]').on( 'change', function(){
		var $required_all_options = $(this).parents('form').find('.required_all_options');
		var $required_all_options_checkbox =  $required_all_options.find('input[type="checkbox"]');
		if( $(this).attr('checked') == 'checked' ) {
			$required_all_options.show();
		} else {
			$required_all_options.hide();
			$required_all_options_checkbox.attr('checked', false);
		}
	});

	function changeType( item ) {
		if ( item ) { var parent = item.parents('.type-row'); }
		else { var parent = $('body'); }
		var type = item.val();
		switch(type) {
			case 'checkbox':
			case 'color':
			case 'date':
			case 'labels':
			case 'multiple_labels':
			case 'radio':
			case 'file':
				$('form .option-min input', parent).attr('disabled','disabled');
				$('form .option-min input', parent).val('-');
				$('form .option-max input', parent).attr('disabled','disabled');
				$('form .option-max input', parent).val('-');
				break;
			default:

				$('form.number .option-min input', parent).removeAttr('disabled');
				$('form.number .option-max input', parent).removeAttr('disabled');
				if( $('form.number .option-min input', parent).val() == '-' ) $('form.number .option-min input', parent).val('');
				if( $('form.number .option-max input', parent).val() == '-' ) $('form.number .option-max input', parent).val('');

				$('form.price .option-min input', parent).removeAttr('disabled');
				$('form.price .option-max input', parent).removeAttr('disabled');
				if( $('form.price .option-min input', parent).val() == '-' ) $('form.price .option-min input', parent).val('');
				if( $('form.price .option-max input', parent).val() == '-' ) $('form.price .option-max input', parent).val('');

				$('form.range .option-min input', parent).removeAttr('disabled');
				$('form.range .option-max input', parent).removeAttr('disabled');
				if( $('form.range .option-min input', parent).val() == '-' ) $('form.range .option-min input', parent).val('');
				if( $('form.range .option-max input', parent).val() == '-' ) $('form.range .option-max input', parent).val('');

				$('form.textarea .option-min input', parent).removeAttr('disabled');
				$('form.textarea .option-max input', parent).removeAttr('disabled');
				if( $('form.textarea .option-min input', parent).val() == '-' ) $('form.textarea .option-min input', parent).val('');
				if( $('form.textarea .option-max input', parent).val() == '-' ) $('form.textarea .option-max input', parent).val('');

				break;
		}
		if( type == 'number' ) {
			$('form .form-row .calculate_quantity_sum', parent).show();
			$('form .form-row .max_input_values_amount', parent).show();
			$('form .form-row .min_input_values_amount', parent).show();
		}  else {
			$('form .form-row .calculate_quantity_sum', parent).hide();
			$('form .form-row .calculate_quantity_sum input[type="checkbox"]', parent).removeAttr('checked');
			$('form .form-row .max_input_values_amount', parent).hide();
			$('form .form-row .min_input_values_amount', parent).hide();
		}
	}

	$('.calculate_quantity_sum > input[type="checkbox"]').on('change',function(){
		if( $(this).attr('checked') == 'checked' ) {
			var $elements = $(this).closest('form').find('div.options .option-min input');
			$elements.each(function(){
				if($(this).val()=='') $(this).val('0');
			});
		}s
	});

	// Sortable
	$('#wapo-types .sortable').sortable({
		axis: 'y',
		update: function (event, ui) {
			var priority = 1;
			var types_order = '';
			$('.sortable > li').each(function(i) {
				var id = $( 'input[name="id"]', this ).val();
				types_order += id + ',';
				$('input[name="types-order"]').val( types_order );
				priority++;
			});
		}
	});

	$('#wapo-types div.options table.yith_wapo_option_table tbody').sortable({
		axis: 'y',
		helper: fixWidthSortHelper,
		update: function (event, ui) { }
	});

	var fixWidthSortHelper = function(e, ui) {
		ui.children().each(function() {
			$(this).width($(this).width());
		});
		return ui;
	};

	// Default / Checked
	$('form.select .option-default input[type=checkbox], form.radio .option-default input[type=checkbox]').on('click', function(){
		var form = $(this).parents('form');
		if ( $(this).is(':checked') ){
			$('.option-default input[type=checkbox]', form).removeAttr('checked');
			$(this).attr('checked', 'checked');
		}
	});

	// Option
	$('.option-image .opt-image-upload, .image-upload').click( function(e) {
		e.preventDefault();
		var parent = $(this).parent();
		var custom_uploader = wp.media({
			title: yith_wapo_general.uploader_title,
			button: { text: yith_wapo_general.uploader_button_text },
			multiple: false  // Set this to true to allow multiple files to be selected
		})
		.on('select', function() {
			var attachment = custom_uploader.state().get('selection').first().toJSON();
			$('.opt-image, .image', parent).attr( 'src', attachment.url );
			$('.opt-image, .image', parent).val( attachment.url );
			$('.opt-image-alt', parent).val( attachment.alt );

		})
		.open();
	});

	$('.option-image .opt-remove, .image .remove').click( function(){
		var parent = $(this).parent();
		$('.opt-image, .image', parent).attr('src', yith_wapo_general.place_holder_url );
		$('.opt-image, .image', parent).val('');
	});

	if ( typeof tipTip == 'function' ) {
		var tiptip_args = {
			'attribute': 'data-tip',
			'fadeIn': 50,
			'fadeOut': 50,
			'delay': 200
		};
		$( '.woocommerce-help-tip' ).tipTip( tiptip_args );
	}

});
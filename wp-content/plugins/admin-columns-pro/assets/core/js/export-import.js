'use strict';/*
 *	Fires when the dom is ready
 *
 */jQuery(document).ready(function($){ac_export_multiselect($);ac_import($);ac_importexport($)});/*
 * Export Multiselect
 *
 * @since 1.5
 */function ac_export_multiselect($){// init
$('.ac-export-multiselect').multiSelect();// click events
$('.export-select-all').click(function(e){$(this).parents('form').find('.ac-export-multiselect').multiSelect('select_all');e.preventDefault()});$('.export-deselect-all').click(function(e){$(this).parents('form').find('.ac-export-multiselect').multiSelect('deselect_all');e.preventDefault()})}/*
 * Import
 *
 * @since 1.5
 */function ac_import($){var container=$('#ac-import-input');$('#upload',container).change(function(){if($(this).val())$('#import-submit',container).addClass('button-primary');else $('#import-submit',container).removeClass('button-primary')})}function ac_importexport($){$('#php-export-results').find('textarea').on('focus, mouseup',function(){$(this).select()}).select().focus()}
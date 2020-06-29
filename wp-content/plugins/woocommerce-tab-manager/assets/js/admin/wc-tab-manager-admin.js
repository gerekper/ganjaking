jQuery( function($){

	/* global quicktags, QTags, wc_tab_manager_admin_params, wpActiveEditor: true */

	// Initial order
	var $wcProductTabItems = $('.woocommerce_product_tabs').find('.woocommerce_product_tab');

	// sort by position
	$wcProductTabItems.sort(function(a, b) {
		var compA = parseInt($(a).attr('rel'), 10);
		var compB = parseInt($(b).attr('rel'), 10);
		return (compA < compB) ? -1 : (compA > compB) ? 1 : 0;
	});

	// reorder
	$wcProductTabItems.each( function(idx, itm) { $('.woocommerce_product_tabs').append(itm); } );

	// update the product tab indexes, based on the current ordering
	function productTabRowIndexes() {
		$('.woocommerce_product_tabs .woocommerce_product_tab').each(function(index, el){
			$('.product_tab_position', el).val( parseInt( $(el).index('.woocommerce_product_tabs .woocommerce_product_tab'), 10 ) );
		});
	}

	// Add rows
	$('button.add_product_tab').on('click', function() {

		var size = $('.woocommerce_product_tabs .woocommerce_product_tab').size();

		var productTabId = $('select.product_tab').val();

		if (!productTabId) {

			var data = {
				action:   'wc_tab_manager_get_editor',
				size:     size,
				security: wc_tab_manager_admin_params.get_editor_nonce
			};

			$.post( wc_tab_manager_admin_params.ajax_url, data, function(response) {

				$('.tab_content_editor_' + data.size).append( response );
				$('#producttabcontent' + data.size).val( $('#product_tab_content_' + data.size).val() );
				$('#product_tab_content_' + data.size).remove();
				try {
					var qt = quicktags( { id:'producttabcontent' + data.size, buttons:'strong,em,link,block,del,ins,img,ul,ol,li,code,more,spell,close,fullscreen' } ); // jshint ignore:line
					// force the quicktag buttons to render by calling the "prive" _buttonsInit() method
					QTags._buttonsInit();
					// hook up the "Upload/Insert" links
					$('#wp-producttabcontent' + data.size + '-wrap').mousedown(function(){
						wpActiveEditor = this.id.slice(3, -5);
					});
				} catch(e) {}

			});

			// Add custom tab row
			$('.woocommerce_product_tabs').append('<div class="woocommerce_product_tab wc-metabox">\
				<h3>\
					<button type="button" class="remove_row button">' + wc_tab_manager_admin_params.remove_label + '</button>\
					<div class="handlediv" title="' + wc_tab_manager_admin_params.click_to_toggle + '"></div>\
					<strong class="product_tab_name"></strong>\
				</h3>\
				<table class="woocommerce_product_tab_data wc-metabox-content">\
					<tr>\
						<td>\
							<div class="options_group">\
								<p class="form-field product_tab_title_field">\
									<label for="product_tab_title_' + size + '">' + wc_tab_manager_admin_params.title_label + '</label>\
									<input type="text" value="" id="product_tab_title_' + size + '" name="product_tab_title[' + size + ']" class="short product_tab_title"> <span class="description">' + wc_tab_manager_admin_params.title_description + '</span>\
								</p>\
							</div>\
							<div class="tab_content_editor_' + size + '">\
								<textarea id="product_tab_content_' + size + '" class="large-text" style="float:left;" rows="10" name="product_tab_content[' + size + ']"></textarea>\
							</div>\
							<input type="hidden" name="product_tab_active[' + size + ']" class="product_tab_active" value="1" />\
							<input type="hidden" name="product_tab_position[' + size + ']" class="product_tab_position" value="' + size + '" />\
							<input type="hidden" name="product_tab_type[' + size + ']" class="product_tab_tab" value="product" />\
							<input type="hidden" name="product_tab_id[' + size + ']" class="product_tab_id" value="" />\
						</td>\
					</tr>\
				</table>\
			</div>');

			productTabTitleChange();

		} else {

			// Reveal core/global row
			var $thisrow = $('.woocommerce_product_tabs .woocommerce_product_tab.' + productTabId);
			if (!$thisrow.is(':visible')) {
				$('.woocommerce_product_tabs').append( $thisrow );
				$thisrow.find('.product_tab_active').val(1);
				productTabRowIndexes();
			}
			$thisrow.show().find('.woocommerce_product_tab_data').show();

		}

		$('select.product_tab').val('');
	});

	// listener to update the product tab name in the h3 when the tab title is changed
	// (except for the core tabs, who's displayed names we do not change)
	function productTabTitleChange() {
		$('.woocommerce_product_tabs').on('blur', 'input.product_tab_title', function() {
			var $parent = $(this).closest('.woocommerce_product_tab');
			if (!$parent.hasClass('product_tab_core')) {
				$parent.find('strong.product_tab_name').text( $(this).val() );
			}
		});
	}
	productTabTitleChange();


	$('.woocommerce_product_tabs').on('click', 'button.remove_row', function() {
		var answer = confirm(wc_tab_manager_admin_params.remove_product_tab); // jshint ignore:line
		if (answer) {
			var $parent = $(this).parent().parent();

			$parent.hide();
			$parent.find('.product_tab_active').val(0);
			productTabRowIndexes();
		}
		return false;
	});

	// Attribute ordering
	var tabs = $( '.woocommerce_product_tabs' );
	if ( tabs.length ) {
		tabs.sortable({
			items:'.woocommerce_product_tab',
			cursor:'move',
			axis:'y',
			handle: 'h3',
			scrollSensitivity:40,
			forcePlaceholderSize: true,
			helper: 'clone',
			opacity: 0.65,
			placeholder: 'wc-metabox-sortable-placeholder',
			start:function(event,ui){
			ui.item.css('background-color','#f6f6f6');
			},
			stop:function(event,ui){
				ui.item.removeAttr('style');
				productTabRowIndexes();
			}
		});
	}

	// Override default layout handler
	$('#_override_tab_layout').change(function() {
		if ( $(this).is(':checked') ) {
			$('#wc_tab_manager_block').hide();
		} else {
			$('#wc_tab_manager_block').show();
		}
	}).change();
});

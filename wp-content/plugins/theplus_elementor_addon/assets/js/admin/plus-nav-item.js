(function ($) {
	"use strict";
	// Clear cache files
	$(document).ready(function(){
		$('.plus-color-fields').each(function(){
			$(this).wpColorPicker();
		});
		
		var menuImageUpdate = function( item_id, thumb_id ) {
			
			wp.media.post( 'plus-menu-icon-img', {
				json:         true,
				post_id:      item_id,
				thumbnail_id: thumb_id,				
				_wpnonce:     menuImage.settings.nonce
			}).done( function( html ) {
				$('.plus-menu-item-icon-images', '#menu-item-' + item_id).html( html );
			});
		};
		$('#menu-to-edit').on('click', '.menu-item .tp-menu-item-icon-type', function (e) {
			var radioValue = $(this).val();
            if(radioValue=='icon_image'){
				$(this).closest('.menu-item').find(".plus-menu-field-image").slideDown(200);
				$(this).closest('.menu-item').find(".field-tp-icon-class").slideUp(200);
			}else if(radioValue=='icon_class'){
				$(this).closest('.menu-item').find(".plus-menu-field-image").slideUp(200);
				$(this).closest('.menu-item').find(".field-tp-icon-class").slideDown(200);
			}		
		});
		$(".plus-select-megamenu").on("change",function() {
			var value= $('option:selected', this).val();
			if(value =='default' ){
				$(this).closest('.menu-item').find(".field-tp-dropdown-width").slideDown(200);
				$(this).closest('.menu-item').find(".field-tp-menu-alignment").slideDown(200);
			}else{
				$(this).closest('.menu-item').find(".field-tp-dropdown-width").slideUp(200);
				$(this).closest('.menu-item').find(".field-tp-menu-alignment").slideUp(200);
			}
		});
		$('#menu-to-edit').on('click', '.menu-item .tp-menu-item-icon-type', function (e) {
			var radioValue = $(this).val();
            if(radioValue=='icon_image'){
				$(this).closest('.menu-item').find(".plus-menu-field-image").slideDown(200);
				$(this).closest('.menu-item').find(".field-tp-icon-class").slideUp(200);
			}else if(radioValue=='icon_class'){
				$(this).closest('.menu-item').find(".plus-menu-field-image").slideUp(200);
				$(this).closest('.menu-item').find(".field-tp-icon-class").slideDown(200);
			}		
		});
		$('#menu-to-edit').on('click', '.menu-item .plus-menu-icon-thumbnail', function (e) {
			e.preventDefault();
			e.stopPropagation();

			var item_id = $(this).parents('.plus-menu-field-image').find('.plus-menu-icon-thumbnail').data("item-id"),
				uploader = wp.media({
					title: menuImage.l10n.uploaderTitle, // todo: translate
					button: { text: menuImage.l10n.uploaderButtonText },
					multiple: false
				}).on('select', function () {
					var attachment = uploader.state().get('selection').first().toJSON();					
					menuImageUpdate( item_id, attachment.id );
				}).open();
		}).on('click', '.menu-item .plus-menu-icon-remove', function (e) {
			e.preventDefault();
			e.stopPropagation();

			var item_id = $(this).parents('.plus-menu-field-image').find('.plus-menu-icon-thumbnail').data("item-id");
			menuImageUpdate( item_id, -1 );
		});
		
	});
})(window.jQuery);

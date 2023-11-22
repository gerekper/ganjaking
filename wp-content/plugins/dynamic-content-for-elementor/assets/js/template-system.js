jQuery(function() {
	// reopen last settings
	if (location.hash) {
		var hash = location.hash;
		var mbtn = '#'+jQuery('.nav-tab-link[href='+hash+']').closest('.dce-template-edit').attr('id');
		jQuery('.nav-tab-link[href='+mbtn+']').trigger('click');
		jQuery('.nav-tab-link[href='+hash+']').trigger('click');
	}

	jQuery('.dce-quick-goto-active-setting').on('click', function(){
		var href = jQuery(this).attr('href');
		var mbtn = jQuery(this).closest('.dce-template-list-li').find('.nav-tab-link');
		mbtn.trigger('click');
		jQuery('.nav-tab-link[href='+href+']').trigger('click');
		var scrollmem = jQuery('html').scrollTop() || jQuery('body').scrollTop();
		location.hash = href.substr(1);
		jQuery('html,body').scrollTop(scrollmem);
		jQuery(this).addClass('dce-quick-goto-active-setting-active');
		return false;
	});

	jQuery('.dce-template-quick-remove').on('click', function(){
		var quick_remove = jQuery(this).closest('.dce-template-select-wrapper').find('.dce-select-template');
		quick_remove.val(0);
		quick_remove.trigger('change');
		jQuery(this).addClass('hidden');
		return false;
	});

	jQuery('.dce-select-template').on('change', function(){
		var quick_edit = jQuery(this).closest('.dce-template-select-wrapper').find('.dce-template-quick-edit');
		var quick_remove = jQuery(this).closest('.dce-template-select-wrapper').find('.dce-template-quick-remove');
		if (jQuery(this).val() > 0) {
			quick_remove.removeClass('hidden');
			quick_edit.removeClass('hidden');
			quick_edit.attr('href', quick_edit.data('href')+jQuery(this).val());
		} else {
			quick_edit.addClass('hidden');
			quick_edit.addClass('hidden');
		}
	});

	jQuery('.dce-template-post-body-single .dce-select-template').on('change', function(){
		if (jQuery(this).val() > 0) {
			jQuery(this).closest('.dce-template-main-content').find('.dce-template-page').removeClass('dce-template-content-original').addClass('dce-template-content-template');
		} else {
			jQuery(this).closest('.dce-template-main-content').find('.dce-template-page').addClass('dce-template-content-original').removeClass('dce-template-content-template');
		}
	});

	jQuery('.dce-template-post-body-archive .dce-select-template').on('change', function(){
		if (jQuery(this).val() > 0) {
			jQuery(this).closest('.dce-template-main-content').find('.dce-template-archive-type').removeClass('hidden');
			jQuery(this).closest('.dce-template-main-content').find('.dce-template-page').removeClass('dce-template-content-original').addClass('dce-template-content-template');
			jQuery(this).closest('.dce-template-main-content').find('.dce-radio-container input[type=radio]:checked').trigger('click');
		} else {
			jQuery(this).closest('.dce-template-main-content').find('.dce-template-page').addClass('dce-template-content-original').removeClass('dce-template-content-template');
			jQuery(this).closest('.dce-template-main-content').find('.dce-template-archive-type').addClass('hidden');
			jQuery(this).closest('.dce-template-main-content').find('.dce-template-page-content').attr('class', 'dce-template-page-content dce-template-page-content-original');
		}
	});

	jQuery('.dce-template-before .dce-select-template, .dce-template-after .dce-select-template').on('change', function(){
		if (jQuery(this).val() > 0) {
			jQuery(this).closest('.dce-template-panel').find('.dce-template-icon-bar').addClass('dce-template-icon-bar-template');
		} else {
			jQuery(this).closest('.dce-template-panel').find('.dce-template-icon-bar').removeClass('dce-template-icon-bar-template');
		}
	});

	jQuery('.dce-template-post-body-archive .dce-radio-container-template').on('click', function(){
		var value = jQuery(this).find('input[type=radio]').val();
		if (value && value != 'canvas') {
			jQuery(this).closest('.dce-template-main').find('.dce-template-archive-blocks').removeClass('hidden');
		} else {
			jQuery(this).closest('.dce-template-main').find('.dce-template-archive-blocks').addClass('hidden');
		}
		if (!value) {
			jQuery(this).closest('.dce-template-main').find('.dce-template-teaser').addClass('hidden');
		} else {
			jQuery(this).closest('.dce-template-main').find('.dce-template-teaser').removeClass('hidden');
		}
		if (!value) {
			value = 'original';
		}
		if (value == 'blank') {
			value = 'full';
		}
		jQuery(this).closest('.dce-template-main-content').find('.dce-template-page-content').attr('class', 'dce-template-page-content dce-template-page-content-'+value);
	});

	jQuery('.dce-template-post-body-single .dce-radio-container-template').on('click', function(){
		var value = jQuery(this).find('input[type=radio]').val();
		if (value && value != 'canvas') {
			jQuery(this).closest('.dce-template-main').find('.dce-template-single-blocks').removeClass('hidden');
		} else {
			jQuery(this).closest('.dce-template-main').find('.dce-template-single-blocks').addClass('hidden');
		}
		if (!value || value == '0') {
			value = 'original';
		}
		if (value == 'header-footer' || value == 1 || value == '1') {
			value = 'full';
		}
		if (value == 'canvas' || value == 2 || value == '2') {
			value = 'canvas';
		}
		jQuery(this).closest('.dce-template-main-content').find('.dce-template-page-content').attr('class', 'dce-template-page-content dce-template-page-content-'+value);

		jQuery(this).closest('.dce-template-main-content').find('.dce-template-page').removeClass('dce-template-content-canvas').removeClass('dce-template-content-default').removeClass('dce-template-content-full');
		if (value != 'original') {
			jQuery(this).closest('.dce-template-main-content').find('.dce-template-page').addClass('dce-template-content-'+value);
		}
	});

	if (!template_system.active) {
        jQuery('#menu-settings-column .accordion-section').removeClass('open');
    }
});

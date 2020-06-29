(function($) {
	$(document).ready(function(){
	$('.vc_edit-form-tab[data-tab="pt_plus_templates"] .sortable_templates ul > li').each(function() {
				"all" == $(this).attr("data-sort") ? $(this).find(".count").html($('.vc_edit-form-tab[data-tab="pt_plus_templates"] .vc_ui-template-list > .vc_ui-template').length) : $(this).find(".count").html($('.vc_edit-form-tab[data-tab="pt_plus_templates"] .vc_ui-template-list > .vc_ui-template.' + $(this).attr("data-sort")).length)
			}), $('.vc_edit-form-tab[data-tab="pt_plus_templates"] .sortable_templates li[data-sort="all"]').addClass("active").trigger("click"), $('.vc_edit-form-tab[data-tab="pt_plus_templates"] .sortable_templates li').click(function() {
				$('.vc_edit-form-tab[data-tab="pt_plus_templates"] .sortable_templates li').removeClass("active"), $(this).addClass("active");
				var t = $(this).attr("data-sort");
				$('.vc_edit-form-tab[data-tab="pt_plus_templates"] .vc_ui-template-list > .vc_ui-template').removeClass("hidden"), "all" != t && $('.vc_edit-form-tab[data-tab="pt_plus_templates"] .vc_ui-template-list > .vc_ui-template:not(.' + t + ")").addClass("hidden")
			}),
			$('.vc_ui-template', $(this.el) ).removeClass('is-loading').find('.vc-composer-icon').removeClass('vc-c-icon-sync').addClass('vc-c-icon-add');
			$('.vc_ui-control-button i', $(this.el) ).removeClass('rotating');
			$(this.el).on('click', '.vc_ui-template [data-template-handler]' ,function() {

				$(this).closest('.vc_ui-template').addClass('is-loading')
				if ( $(this).is('.vc_ui-control-button') ) {
					$(this).find('.vc-composer-icon').removeClass('vc-c-icon-add').addClass('vc-c-icon-sync rotating');
				} else {
					$(this).next('.vc_ui-list-bar-item-actions').find('.vc-composer-icon').removeClass('vc-c-icon-add').addClass('vc-c-icon-sync rotating');
				}

			});
	});
})(jQuery);
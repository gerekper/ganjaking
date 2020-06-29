jQuery(document).ready(function($){
	var pt_plusCustomDependencies = function() {
	$('[data-vc-shortcode="tp_blog_list"]').each(function() {
		var value=$('[name="blog_style"]').find('option:selected').val();
		if($('[name="blog_style"]').find('option:selected').length > 0 && (value=='style-4' || value=='style-5' || value=='style-6')) {
			var loop=$('.blog_layout_field').parents(".wpb_el_type_radio_select_image").find(".image_picker_selector > li");
			loop.eq(2).show();
		}else{
			var loop=$('.blog_layout_field').parents(".wpb_el_type_radio_select_image").find(".image_picker_selector > li");
			loop.eq(2).hide();
			if($('[name="layout"]').find('option:selected').val() == 'metro') {
			$('[name="layout"]').find('option:selected').removeAttr('selected').children('option[value="grid"]').attr('selected', 'selected');
					$('[name="layout"]').trigger('change');
			}
		}
	});
	};
	$(window).load(function() {
		$('.vc_ui-panel-window').on('vcPanel.shown',pt_plusCustomDependencies);
	});
	$('body').on('change', '[data-vc-shortcode-param-name="items"]', pt_plusCustomDependencies);
	$('body').on('change', '[name="blog_style"]', pt_plusCustomDependencies);
	$('body').on('change', '[name="layout"]', pt_plusCustomDependencies);
});
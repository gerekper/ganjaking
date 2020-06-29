jQuery(document.body).on('click', '.porto-sc-image-select li', function(e) {
	jQuery(this).addClass('active').siblings().removeClass('active');
	jQuery(this).closest('.porto-sc-image-select').next('.wpb_vc_param_value').val(jQuery(this).data('id'));
});
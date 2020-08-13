/** @var ct_ultimate_gdpr_newsletter object - localized */

jQuery(document).on('ready', function () {
	if(ct_ultimate_gdpr_newsletter.checkbox_top == true) {
		jQuery('.tnp-subscription form').first().prepend(ct_ultimate_gdpr_newsletter.checkbox);
		jQuery('.tnp-widget-minimal form').prepend(ct_ultimate_gdpr_newsletter.checkbox_widget_minimal);
		jQuery('.tnp-widget form').prepend(ct_ultimate_gdpr_newsletter.checkbox_widget);
	}else{
    	jQuery('.tnp-subscription form').first().append(ct_ultimate_gdpr_newsletter.checkbox);
    	jQuery('.tnp-widget-minimal form').append(ct_ultimate_gdpr_newsletter.checkbox_widget_minimal);
    	jQuery('.tnp-widget form').append(ct_ultimate_gdpr_newsletter.checkbox_widget);
    }
});
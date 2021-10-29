function wafp_view_dashboard_affiliate_page(url, action, period) {
  jQuery('.wafp-stats-loader').show();
  jQuery.ajax({
      type: "POST",
      url: url,
      data: "plugin=wafp&controller=dashboard&action=" + action + "&period=" + period,
      success: function (html) {
          jQuery("#tooltip").remove(); // clear out the tooltip
          jQuery('#wafp-dash-affiliate-panel').replaceWith(html);
          jQuery('.wafp-stats-loader').hide();
      }
  });
}

jQuery(document).ready(function() {
  jQuery('#wafp_agreement_agree').click( function() {
    jQuery('#wafp_signup_agreement_text').slideToggle();
    return false;
  });

  jQuery('.wafp-aff-info-toggle').click( function(e) {
    e.preventDefault();
    jQuery('#'+jQuery(this).attr('data-id')).slideToggle();
  });

});

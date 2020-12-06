jQuery(document).ready(function($) {
  var action = ($('#mepr-aweber-enabled').is(":checked")?'show':'hide');
  $('#mepr-aweber-product-panel')[action]();
  $('#mepr-aweber-enabled').click(function() {
    $('#mepr-aweber-product-panel')['slideToggle']('fast');
  });

  mepr_load_aweber_list_dropdown('#mepr-adv-aweber-list', MeprProducts.wpnonce);
}); //End main document.ready func


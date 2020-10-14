jQuery(document).ready(function($) {
  if($('#mepr_buddypress_membership_groups').is(":checked")) {
    $('div#mepr_buddypress_membership_groups_area').show();
  } else {
    $('div#mepr_buddypress_membership_groups_area').hide();
  }

  $('#mepr_buddypress_membership_groups').click(function() {
    $('div#mepr_buddypress_membership_groups_area').slideToggle();
  });
}); //End main document.ready func

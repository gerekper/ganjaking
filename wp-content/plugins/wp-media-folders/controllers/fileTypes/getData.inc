<?php


add_action('wp_ajax_FEML.fileTypes.getData', function() {
  if (!current_user_can('upload_files')) feml_error_perms();

  $results = array();

  $results['allowed'] = feml_services_fileTypes_getAllowed();
  $results['system'] = feml_services_fileTypes_getSystem();
  $results['custom'] = feml_services_fileTypes_getCustom();

  wp_send_json_success($results);
});

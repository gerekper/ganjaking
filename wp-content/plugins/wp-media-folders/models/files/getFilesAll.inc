<?php


function feml_models_files_getFilesAll() {
  global $wpdb;

  $is_admin = current_user_can('administrator');
  $restrict_owner = get_option('feml_restrict_by_owner', 'false') === 'true';
  $where_restrict = $restrict_owner && !$is_admin ? 'AND post_author = %d' : '';

  $query = "
    SELECT ID,
           post_title
    FROM $wpdb->posts
    WHERE post_type = %s
      $where_restrict
  ";

  $params = array('attachment');

  if ($restrict_owner && !$is_admin) {
    $params[] = get_current_user_id();
  }

  $res = $wpdb->query($wpdb->prepare($query, $params));
  if ($res === false) feml_error($wpdb->last_error);

  $files = $wpdb->last_result;

  foreach ($files as &$file) {
    $file->ID = intval($file->ID);
  }

  return $files;
}

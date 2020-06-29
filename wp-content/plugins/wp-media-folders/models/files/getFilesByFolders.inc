<?php


function feml_models_files_getFilesByFolders($ids) {
  global $wpdb;

  $patterns = feml_pattern($ids, '%d');

  $is_admin = current_user_can('administrator');
  $restrict_owner = get_option('feml_restrict_by_owner', 'false') === 'true';
  $where_restrict = $restrict_owner && !$is_admin ? 'AND p.post_author = %d' : '';

  $query = "
    SELECT p.ID,
           p.post_title
    FROM $wpdb->posts p
    LEFT JOIN $wpdb->term_relationships tr
      ON p.ID = tr.object_id
    LEFT JOIN $wpdb->term_taxonomy tt
      ON tr.term_taxonomy_id = tt.term_taxonomy_id
    WHERE tt.term_id IN ($patterns)
      $where_restrict
  ";

  if ($restrict_owner && !$is_admin) {
    $ids[] = get_current_user_id();
  }

  $res = $wpdb->query($wpdb->prepare($query, $ids));
  if ($res === false) feml_error($wpdb->last_error);

  $files = $wpdb->last_result;

  foreach ($files as &$file) {
    $file->ID = intval($file->ID);
  }

  return $files;
}

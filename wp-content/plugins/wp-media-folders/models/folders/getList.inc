<?php


function feml_models_folders_getList() {
  global $wpdb;

  $is_admin = current_user_can('administrator');
  $restrict_owner = get_option('feml_restrict_by_owner', 'false') === 'true';
  $where_restrict = $restrict_owner && !$is_admin ? "AND (t.term_group = 0 OR t.term_group = %d)" : '';

  $query = "
    SELECT tt.term_id,
           tt.parent,
           tt.count,
           t.name
    FROM $wpdb->term_taxonomy tt
    LEFT JOIN $wpdb->terms t
      ON tt.term_id = t.term_id
    WHERE tt.taxonomy = %s
      $where_restrict
  ";

  $params = array('feml-folder');

  if ($restrict_owner && !$is_admin) {
    $params[] = get_current_user_id();
  }

  $res = $wpdb->query($wpdb->prepare($query, $params));
  if ($res === false) feml_error($wpdb->last_error);

  $folders = $wpdb->last_result;

  foreach ($folders as &$folder) {
    $folder->term_id = intval($folder->term_id);
    $folder->parent = intval($folder->parent);
    $folder->count = intval($folder->count);
  }

  return $folders;
}

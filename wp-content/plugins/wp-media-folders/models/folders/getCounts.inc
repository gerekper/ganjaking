<?php


function feml_models_folders_getCounts() {
  global $wpdb;

  $is_admin = current_user_can('administrator');
  $restrict_owner = get_option('feml_restrict_by_owner', 'false') === 'true';
  $join_restrict = $restrict_owner && !$is_admin ? "LEFT JOIN $wpdb->terms t ON t.term_id = tt.term_id" : '';
  $where_restrict = $restrict_owner && !$is_admin ? "AND (t.term_group = 0 OR t.term_group = %d)" : '';

  $query = "
    SELECT tt.term_id,
           tt.count
    FROM $wpdb->term_taxonomy tt
    $join_restrict
    WHERE tt.taxonomy = %s
      $where_restrict
  ";

  $params = array('feml-folder');

  if ($restrict_owner && !$is_admin) {
    $params[] = get_current_user_id();
  }

  $res = $wpdb->query($wpdb->prepare($query, $params));
  if ($res === false) feml_error($wpdb->last_error);

  $counts = $wpdb->last_result;

  foreach ($counts as &$item) {
    $item->term_id = intval($item->term_id);
    $item->count = intval($item->count);
  }

  return $counts;
}

<?php


function feml_models_folders_getRootCount() {
  global $wpdb;

  $query = "
    SELECT COUNT(*) AS cc
    FROM $wpdb->posts p
    WHERE NOT EXISTS(
      SELECT 1
      FROM $wpdb->term_relationships tr
      INNER JOIN $wpdb->term_taxonomy tt
        ON tr.term_taxonomy_id = tt.term_taxonomy_id
      WHERE tt.taxonomy = %s
        AND tr.object_id = p.ID
      )
      AND p.post_type = %s
  ";

  $params = array('feml-folder', 'attachment');

  $res = $wpdb->query($wpdb->prepare($query, $params));
  if ($res === false) feml_error($wpdb->last_error);

  return intval($wpdb->last_result[0]->cc);
}

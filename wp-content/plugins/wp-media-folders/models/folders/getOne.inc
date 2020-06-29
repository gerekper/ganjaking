<?php


function feml_models_folders_getOne($id) {
  global $wpdb;

  $query = "
    SELECT tt.term_id,
           t.name
    FROM $wpdb->term_taxonomy AS tt
    LEFT JOIN $wpdb->terms AS t
      ON tt.term_id = t.term_id
    WHERE tt.taxonomy = %s
      AND tt.term_id = %d
  ";

  $params = array('feml-folder', $id);

  $res = $wpdb->query($wpdb->prepare($query, $params));
  if ($res === false) feml_error($wpdb->last_error);

  if (count($wpdb->last_result) === 0) {
    return false;
  }

  $folder = $wpdb->last_result[0];

  $folder->term_id = intval($folder->term_id);

  return $folder;
}

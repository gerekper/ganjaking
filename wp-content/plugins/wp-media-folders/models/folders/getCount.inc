<?php


function feml_models_folders_getCount() {
  global $wpdb;

  $query = "
    SELECT COUNT(*) AS cc
    FROM $wpdb->term_taxonomy
    WHERE taxonomy = %s
  ";

  $params = array('feml-folder');

  $res = $wpdb->query($wpdb->prepare($query, $params));
  if ($res === false) feml_error($wpdb->last_error);

  return intval($wpdb->last_result[0]->cc);
}

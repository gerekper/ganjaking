<?php


add_filter('ajax_query_attachments_args', function($query) {
  if (!isset($_POST['parent'])) return $query;

  $parent = intval($_POST['parent']);
  $s = isset($_POST['query']['s']) ? $_POST['query']['s'] : '';
  $ids = isset($_POST['ids']) ? $_POST['ids'] : '';

  $terms = $ids !== '' ? explode(',', $ids) : array($parent);
  $terms = array_map('intval', $terms);

  $query['tax_query'] = isset($query['tax_query'])
    ? $query['tax_query']
    : array();

  if ($parent === 0) {
    if ($s === '') {
      $query['tax_query'][] = array(
        'taxonomy' => 'feml-folder',
        'operator' => 'NOT EXISTS'
      );
    }
  } else {
    $query['tax_query'][] = array(
      'taxonomy' => 'feml-folder',
      'field' => 'term_id',
      'terms' => $terms,
      'include_children' => false
    );
  }

  $is_admin = current_user_can('administrator');
  $restrict_owner = get_option('feml_restrict_by_owner', 'false') === 'true';
  if ($restrict_owner && !$is_admin) $query['author'] = get_current_user_id();

  return $query;
});


add_filter('wp_prepare_attachment_for_js', function($response, $attachment, $meta) {
  $response['femlShortcut'] = isset($meta['feml_shortcut_parent'])
    ? $meta['feml_shortcut_parent']
    : 0;

  return $response;
}, 1000, 3);

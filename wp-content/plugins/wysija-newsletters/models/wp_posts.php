<?php
defined('WYSIJA') or die('Restricted access');

class WYSIJA_model_wp_posts extends WYSIJA_model {

  var $pk = 'ID';
  var $tableWP = true;
  var $table_name = 'posts';
  var $columns = array(
    'ID' => array(
      'req' => true,
      'type' => 'integer'
    ),
    'post_author' => array('type' => 'integer'),
    'post_date' => array(),
    'post_date_gmt' => array(),
    'post_content' => array(),
    'post_title' => array(),
    'post_excerpt' => array(),
    'post_status' => array(),
    'comment_status' => array(),
    'ping_status' => array(),
    'post_password' => array(),
    'post_name' => array(),
    'to_ping' => array(),
    'pinged' => array(),
    'post_modified' => array(),
    'post_modified_gmt' => array(),
    'post_content_filtered' => array(),
    'post_parent' => array('type' => 'integer'),
    'guid' => array(),
    'menu_order' => array('type' => 'integer'),
    'post_type' => array(),
    'post_mime_type' => array(),
    'comment_count' => array('type' => 'integer'),
  );

  function __construct() {
    parent::__construct();
    $this->table_prefix = '';
  }

  function get_posts($args = array()) {
    /**
     * SELECT A.ID, A.post_title, A.post_content, A.post_date FROM `wp_posts` A
     * LEFT JOIN `wp_term_relationships` B ON (A.ID = B.object_id)
     * LEFT JOIN `wp_term_taxonomy` C ON (C.term_taxonomy_id = B.term_taxonomy_id)
     * WHERE C.term_id IN (326) AND A.post_type IN ('post') AND A.post_status IN ('publish') ORDER BY post_date DESC LIMIT 0,10;
     *
     */
    $default_args = array(
      'post_limit' => 10,
      'offset' => 0,
      'category' => null,
      'not_category' => null,
      'orderby' => 'post_date',
      'order' => 'DESC',
      'include' => null,
      'exclude' => null,
      'meta_key' => null,
      'meta_value' => null,
      'post_type' => null,
      'post_mime_type' => null,
      'post_parent' => null,
      'post_status' => 'publish',
      'post_date' => null,
      'is_search_query' => false,
      'search' => null
    );

    $args = array_merge($default_args, $args);

    // set categories
    if(isset($args['category_ids']) && strlen(trim($args['category_ids'])) > 0) {
      $args['category'] = explode(',', trim($args['category_ids']));
    } else {
      if(isset($args['post_category']) && (int) $args['post_category'] > 0) {
        $args['category'] = (int) $args['post_category'];
      }
    }
    if(isset($args['include_category_ids']) && !empty($args['include_category_ids'])) {
      $args['category'] = $args['include_category_ids'];
    }
    if(isset($args['exclude_category_ids']) && !empty($args['exclude_category_ids'])) {
      $args['not_category'] = $args['exclude_category_ids'];
    }

    // default selected fields
    $post_fields = array(
      'A.ID',
      'A.post_title',
      'A.post_content',
      'A.post_excerpt',
      'A.post_author',
      'A.post_type',
      'A.post_status'
    );

    $additional_post_fields = array(
      'post_date',
      'post_date_gmt',
      'comment_status',
      'ping_status',
      'post_name',
      'to_ping',
      'pinged',
      'post_modified',
      'post_modified_gmt',
      'post_content_filtered',
      'post_parent',
      'guid',
      'menu_order',
      'post_mime_type',
      'comment_count'
    );

    // look for manual fields to select
    if(isset($args['post_fields']) && is_array($args['post_fields']) && !empty($args['post_fields'])) {
      $extra_post_fields = array_values(
        array_intersect(
          $additional_post_fields,
          array_map('esc_sql', $args['post_fields'])
        )
      );
      // merge both fields selection
      $post_fields = array_merge(array('A.ID'), $extra_post_fields);
    }

    $query = sprintf('SELECT DISTINCT %s FROM `[wp]' . $this->table_name . '` A ', join(', ', $post_fields));

    if($args['is_search_query'] === true) {
      $count_query = 'SELECT COUNT(DISTINCT A.ID) as total FROM `[wp]' . $this->table_name . '` A ';
    }

    // search by category
    if((isset($args['category']) && !empty($args['category'])) || (isset($args['not_category']) && !empty($args['not_category']))) {
      $query_joins = 'JOIN `[wp]term_relationships` B ON (A.ID = B.object_id) ';
      $query_joins .= 'JOIN `[wp]term_taxonomy` C ON (C.term_taxonomy_id = B.term_taxonomy_id) ';

      $query .= $query_joins;

      if($args['is_search_query'] === true) {
        $count_query .= $query_joins;
      }
    }

    $conditions = array();

    if(isset($args['include']) && $args['include'] !== null) {
      $conditions[] = array(
        'col' => 'A.ID',
        'sign' => 'IN',
        'val' => $args['include'],
        'cast' => 'int'
      );
    } else {
      foreach ($args as $type => $value) {
        if(!$value) continue;
        switch ($type) {
        case 'category':
          $conditions[] = array(
            'col' => 'C.term_id',
            'sign' => 'IN',
            'val' => $value,
            'cast' => 'int'
          );
        break;
        case 'not_category':
          $conditions[] = array(
            'col' => 'C.term_id',
            'sign' => 'NOT IN',
            'val' => $value,
            'cast' => 'int'
          );
        break;
        case 'include':
          $conditions[] = array(
            'col' => 'A.ID',
            'sign' => 'IN',
            'val' => $value,
            'cast' => 'int'
          );
        break;
        case 'exclude':
          $conditions[] = array(
            'col' => 'A.ID',
            'sign' => 'NOT IN',
            'val' => $value,
            'cast' => 'int'
          );
        break;
        case 'cpt': // this is for backwards compatibility's sake
        case 'post_type':
          $conditions[] = array(
            'col' => 'A.post_type',
            'sign' => 'IN',
            'val' => $value
          );
        break;
        case 'post_status':
          $conditions[] = array(
            'col' => 'A.post_status',
            'sign' => 'IN',
            'val' => $value
          );
        break;
        case 'post_date':
          // apply timezone to date value
          $helper_toolbox = WYSIJA::get('toolbox', 'helper');
          $value = $helper_toolbox->time_tzed($value);

          if($value !== '') {
            $conditions[] = array(
              'col' => 'A.post_date',
              'sign' => '>',
              'val' => $value
            );
          }
        break;
        case 'search':
          $conditions[] = array(
            'col' => 'A.post_title',
            'sign' => 'LIKE',
            'val' => '%' . $value . '%'
          );
        break;
        }
      }
    }

    // set static conditions for post statuses (we don't want drafts and such to appear in search results)
    if($args['include'] === null) {
      $conditions[] = array(
        'col' => 'A.post_status',
        'sign' => 'NOT IN',
        'val' => array(
          'auto-draft',
          'inherit'
        )
      );
    }

    // where conditions
    if(!empty($conditions)) {
      $query_conditions = $this->build_conditions($conditions);

      $query .= $query_conditions;

      if($args['is_search_query'] === true) {
        $count_query .= $query_conditions;
      }
    }

    // order by
    if(isset($args['orderby'])) {
      $query .= ' ORDER BY ' . $args['orderby'];
      if(isset($args['sort_by'])) {
        $query .= ' ' . (($args['sort_by'] === 'newest') ? 'DESC' : 'ASC');
      } else {
        if(isset($args['order'])) {
          $query .= ' ' . $args['order'];
        }
      }
    }

    // set limit (only if we are not requesting posts based on their id)
    if(array_key_exists('include', $args) && $args['include'] === null) {
      $query_offset = (isset($args['query_offset']) ? (int) $args['query_offset'] : 0);
      $query_limit = ((isset($args['post_limit']) && (int) $args['post_limit'] > 0) ? (int) $args['post_limit'] : 10);
      $query .= sprintf(' LIMIT %d,%d', $query_offset, $query_limit);
    }

    if($args['is_search_query'] === true) {
      return array(
        'rows' => $this->query('get_res', $query),
        'count' => $this->query('get_row', $count_query)
      );
    } else {
      return $this->query('get_res', $query);
    }
  }

  function build_conditions($conditions) {
    $query = '';
    $i = 0;

    foreach ($conditions as $key => $data) {

      if($i > 0) $query .= ' AND ';

      $query .= $data['col'] . ' ';

      $value = $data['val'];

      switch ($data['sign']) {
      case 'IN':
      case 'NOT IN':
        $values = '';
        if(is_array($value)) {
          if(array_key_exists('cast', $data) && $data['cast'] === 'int') {
            $count = count($value);
            for ($j = 0; $j < $count; $j++) {
              if($value[$j] === null) continue;
              $value[$j] = intval($value[$j]);
            }
            $values = join(', ', $value);
          } else {
            $values = "'" . join("', '", $value) . "'";
          }
          $query .= $data['sign'] . ' (' . $values . ')';
        } else {
          if(strpos($value, ',') === false) {
            // single value
            if(array_key_exists('cast', $data) && $data['cast'] === 'int') {
              $query .= '= ' . (int) $value;
            } else {
              $query .= '= "' . $value . '"';
            }
          } else {
            // multiple values
            $values = "'" . join("','", explode(',', $value)) . "'";
            $query .= $data['sign'] . ' (' . $values . ')';
          }
        }
      break;
      case 'LIKE':
        $query .= ' LIKE "' . $value . '"';
      break;
      default:
        $sign = '=';
        if(isset($data['sign'])) $sign = $data['sign'];

        if(array_key_exists('cast', $data) && $data['cast'] === 'int') {
          $query .= $sign . (int) $value . " ";
        } else {
          $query .= $sign . "'" . $value . "' ";
        }
      }
      $i++;
    }

    if($query === '') {
      return '';
    } else {
      return 'WHERE ' . $query;
    }
  }
}
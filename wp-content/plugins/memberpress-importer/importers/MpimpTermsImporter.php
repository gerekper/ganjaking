<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class MpimpTermsImporter extends MpimpBaseImporter {
  public function form() {
    $term_types = get_taxonomies(array('_builtin' => false), 'objects');
    ?>
    <select name="args[taxonomy]">
    <?php
      foreach( $term_types as $term => $obj ) {
      ?>
        <option value="<?php echo $term; ?>"><?php echo $obj->labels->name . " ({$term})"; ?></option>
      <?php
      }
    ?>
    </select>
    <?php
  }

  public function import($row,$args) {
    $required = array( 'term', 'slug' );
    $this->check_required('terms', array_keys($row), $required);

    if( isset( $args['taxonomy'] ) ) {
      $row['taxonomy'] = $args['taxonomy'];
    }
    else {
      $row['taxonomy'] = $this->def( $row, 'taxonomy', 'post_tag' );
    }

    $row['parent'] = $this->def( $row, 'parent', 0 );

    $params = array();
    foreach( $row as $col => $cell ) {
      if( in_array( $col, $required ) ) {
        $this->fail_if_empty($col, $cell);
      }
      if( !in_array( $col, array('term','taxonomy') ) ) {
        $params[$col] = $cell;
      }
    }

    $term = $row['term'];
    $taxonomy = $row['taxonomy'];

    if( $t = term_exists( $term, $taxonomy, $params['parent'] ) ) {
      $ids = wp_update_term( $t['term_id'], $taxonomy, $params );
      if( !is_wp_error( $ids ) ) {
        return sprintf( __('Term (term=%1$s, term_id=%2$s) already existed and was updated successfully'), $term, $ids['term_id'] );
      }
      else {
        throw new Exception(__('Term existed and failed to be updated'));
      }
    }
    else {
      $ids = wp_insert_term( $term, $taxonomy, $params );
      if( !is_wp_error( $ids ) ) {
        return sprintf( __('Term (term=%1$s, term_id=%2$s) was created successfully'), $term, $ids['term_id'] );
      }
      else {
        throw new Exception(__('Term failed to be created'));
      }
    }
  }
}


<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class MpimpCustomPostTypesImporter extends MpimpPostsImporter {
  public function form() {
    $post_types = get_post_types(array('_builtin' => false), 'objects');
    ?>
    <select name="args[post_type]">
    <?php
      foreach( $post_types as $cpt => $obj ) {
      ?>
        <option value="<?php echo $cpt; ?>"><?php echo $obj->labels->name . " ({$cpt})"; ?></option>
      <?php
      }
    ?>
    </select>
    <?php
  }

  public function import($row,$args) {
    $row['post_type'] = $args['post_type'];
    $required = array('post_title', 'post_type');
    $this->check_required($args['post_type'], array_keys($row), $required);

    $this->import_post($row,$args);
  }
}


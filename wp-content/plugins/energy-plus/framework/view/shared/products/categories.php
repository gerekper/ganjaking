<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
}
?>

<?php echo EnergyPlus_View::run('header-energyplus'); ?>
<?php echo EnergyPlus_View::run('header-page', array('type'=> 1, 'title' => esc_html__('Categories', 'energyplus'), 'description' => '', 'buttons'=> '<a href="' . admin_url( 'edit-tags.php?taxonomy=product_cat&post_type=product' ). '" class="btn btn-sm btn-danger trig"> + &nbsp; ' . esc_html__('New category', 'energyplus'). ' &nbsp;</a>')); ?>
<?php echo EnergyPlus_View::run('products/nav' ) ?>

<div id="energyplus-categories" class="__A__GP">

  <?php if (0 === count( $categories )) {  ?>
    <div class="__A__EmptyTable d-flex align-items-center justify-content-center text-center">
      <div>  <span class="dashicons dashicons-marker"></span><br><?php esc_html_e('No records found', 'energyplus'); ?></div>
    </div>
  <?php } else {   ?>
    <div class="container-fluid __A__Header d-none d-sm-block">
      <div class="row">
        <div class="col-lg-12"><?php esc_html_e('Drag and drop to re-order categories', 'energyplus'); ?></div>
      </div>
    </div>
    <?php
    function categories($categories, $all, $d = 0) {  ?>
      <ol class="<?php echo "__A__Depth_".$d; if (0 === $d) echo '__A__Sortable'; ?>">
        <?php
        foreach ($categories AS $category) {  ?>
        <li id='__A__Category_<?php echo intval($category['id'])?>'>
          <div class="__A__Item2">
            <div class="row">
              <div class="col-8 col-lg-11"><?php echo esc_html($category['name'])?></div>
              <div class="col-4 col-lg-1 text-right __A__RightMe">
                <div class="__A__Actions text-right float-right">
                  <a href="<?php echo admin_url( 'term.php?taxonomy=product_cat&post_type=product&tag_ID=' . intval($category['id']). '&action=edit&energyplus_hide' );?>" class="__A__Button1 __A__MainButton trig"><?php esc_html_e('View', 'energyplus'); ?></a>
                </div>
              </div>
            </div>
          </div>
          <?php
          if (isset($all[$category['id']])) {
            categories($all[$category['id']], $all, ++$d);
            --$d;
          }?>
        </li>
        <?php } ?>
    </ol>
  <?php }
  categories($categories[0], $categories);
  ?>
</div>
<?php }  ?>

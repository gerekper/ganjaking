<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
}
?>

<?php echo EnergyPlus_View::run('header-energyplus'); ?>

<?php echo EnergyPlus_View::run('header-page', array('type'=> 1, 'title' => esc_html($reactor['title']), 'description' => '', 'buttons'=>'')); ?>

<?php echo EnergyPlus_View::reactor('tweaks/views/nav', array('id'=> $reactor['id']) ) ?>

<?php $last_icons = array(); ?>

<div class="__A__List_M1 __A__Container __A__GP">

  <?php if (0 === count($posts)) { ?>
    <div class="__A__EmptyTable d-flex align-items-center justify-content-center text-center">
      <div>  <span class="dashicons dashicons-marker"></span><br><?php esc_html_e('No records found', 'energyplus'); ?></div>
    </div>
  <?php } else { ?>
    <div class="__A__Customers_Container">
      <?php foreach ( $posts AS $post ) {
        $extra = maybe_unserialize($post['extra']);
        ?>
        <div class="btnA __A__Item collapsed" id="item_<?php echo  esc_attr($post['event_id'])?>" data-toggle="collapse" data-target="#item_d_<?php echo  esc_attr($post['event_id'])?>" aria-expanded="false" aria-controls="item_d_<?php echo esc_attr($post['event_id'])?>">
          <div class="liste  row d-flex align-items-center">
            <div class="col col-1 ___A__Col_3  align-middle" data-colname="<?php esc_attr_e('Icon', 'energyplus'); ?>">
              <i class="<?php echo esc_attr($extra['icon']) ?>" style="font-size:22px;"></i>
            </div>
            <div class="col col-6    align-middle" data-colname="<?php esc_attr_e('Details', 'energyplus'); ?>">
              <strong><?php echo esc_html($extra['title']) ?></strong>
            </div>

            <div class="col col-2  __A__Col_3   align-middle" data-colname="<?php esc_attr_e('Date', 'energyplus'); ?>">
              <?php echo date_i18n('M d, H:i', strtotime($post['time'])) ?>
            </div>

            <div class="col col-2  __A__Col_3 align-middle text-right" data-colname="<?php esc_attr_e('BY', 'energyplus'); ?>">
              <?php $created_by = get_userdata($extra['created_by']);
              echo esc_html( $created_by->display_name) ?>
            </div>

          </div>
          <div class="collapse col-xs-12 col-sm-12 col-md-12 text-right" id="item_d_<?php echo esc_attr($post['event_id'])?>">
            <div class="__A__Item_Details">
              <div class="row">
                <div class="col-sm-10 text-left">
                  <?php echo wp_kses_post(nl2br($extra['content'])); ?>
                </div>
                <div class="col-sm-2 __A__Customer_Details_Actions text-left">
                  <?php if (get_current_user_id() == $extra['created_by']) { ?>
                    <a href="<?php echo EnergyPlus_Helpers::admin_page('reactors', array('action'=> 'detail', 'id'=>'announcements', 'do'=>'edit', 'view'=> intval($post['event_id'])))?>" class="__A__HideMe __A__StopPropagation"><?php esc_html_e('Edit', 'energyplus'); ?></a>
                    <br>
                    <br>
                    <a class="text-danger" href="<?php echo EnergyPlus_Helpers::secure_url('reactors', $post['event_id'], array('action'=> 'detail', 'id'=>'announcements', 'do'=>'delete', 'view'=> intval($post['event_id'])))?>" onclick="if (!confirm('<?php esc_attr_e("Are you sure to delete?", "energyplus")?>')) return false;"><?php esc_html_e('Delete', 'energyplus'); ?></a>
                  <?php } ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      <?php } ?>
    </div>
  <?php } ?>
  <p></p>
  <p></p>
  <div class="pt-3 pm-5 text-center w-100">
    <a class="btn btn-sm btn-danger __A__Button1" style="background:red !important; color:#fff !important" href="<?php echo EnergyPlus_Helpers::admin_page('reactors', array('action'=> 'detail', 'id'=>'announcements', 'do'=>'edit', 'view'=> '0'))?>"><?php echo esc_html__('Create a new announcement', 'energyplus')?></a>
  </div>

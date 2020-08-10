<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
}


if (isset($args['h']) && 1 === intval($args['h'])) { $height = 106; } else { $height = 100; }

$width = 100/$per_line;

?>
<div class="__A__Widget_Links_S h-100">

  <?php if (0 === count($items)) { ?>
    <div class="__A__EmptyTable <?php if (0 < count($items)) echo ' d-none'; else echo 'd-flex'; ?> align-items-center justify-content-center text-center">
      <div><a class="trig" style="font-size:16px; color:#ccc; text-decoration:underline;" href="<?php echo EnergyPlus_Helpers::admin_page('dashboard', array('action'=>'wd_settings', 'id'=>$args['id']))?>"><?php esc_html_e('Create a shortcut', 'energyplus'); ?></a></div>
    </div>
  <?php } ?>

  <?php if (1 === $style) { ?>
    <ul class="__A__Widget_Links_Style_1">
      <?php foreach ($items AS $item) { ?>
        <?php if (1 === intval($item['active']) && ( 0 === count($item['users']) || in_array(get_current_user_id(), array_values($item['users'])))) { ?>
          <a href="<?php echo esc_url_raw($item['url'])?>" class="<?php if ("1" === $item['open']) echo "trig";?>" <?php if ("2" === $item['open']) echo "target='_blank' ";?> style="color: <?php echo esc_attr($item['text_color'])?>">
            <li style="<?php if ($item['background_color'] !== 'transparent') echo 'border-bottom:0px;border-right:0px solid;'?> width: <?php echo esc_attr($width);?>%; height: <?php echo esc_attr($height);?>px; background: <?php echo esc_attr($item['background_color'])?>">
              <?php

              if ('' !== $item['icon']) {
                echo "<i class='__A__FA ". esc_attr($item['icon']). "'></i>" ;
              } else {

              }
              echo esc_html($item['title'])
              ?>
            </li>
          </a>
        <?php } ?>
      <?php } ?>
    </ul>
  <?php } ?>

  <?php if (2 === $style) { ?>
    <ul class="__A__Widget_Links_Style_1 __A__Widget_Links_Style_2">
      <?php foreach ($items AS $item) { ?>
        <?php if (1 === intval($item['active']) && ( 0 === count($item['users']) || in_array(get_current_user_id(), array_values($item['users'])))) { ?>
          <a href="<?php echo esc_url_raw($item['url'])?>" class="<?php if ("1" === $item['open']) echo "trig";?>" <?php if ("2" === $item['open']) echo "target='_blank' ";?> style="color: <?php echo esc_attr($item['text_color'])?>">
            <li class="d-flex align-items-center justify-content-center" style="<?php if ($item['background_color'] !== 'transparent') echo 'border-bottom:0px;border-right:0px solid;'?> width: <?php echo esc_attr($width);?>%; height: <?php echo esc_attr($height);?>px; background: <?php echo esc_attr($item['background_color'])?>">
              <?php

              if ('' !== $item['icon']) {
                echo "<i class='__A__FA ". esc_attr($item['icon']). "' title='".esc_html($item['title'])."'></i>" ;
              }
              ?>
            </li>
          </a>
        <?php } ?>
      <?php } ?>
    </ul>
  <?php } ?>

  <?php if (3 === $style) { ?>
    <ul class="__A__Widget_Links_Style_1 __A__Widget_Links_Style_2">
      <?php foreach ($items AS $item) { ?>
        <?php if (1 === intval($item['active']) && ( 0 === count($item['users']) || in_array(get_current_user_id(), array_values($item['users'])))) { ?>
          <a href="<?php echo esc_url_raw($item['url'])?>" class="<?php if ("1" === $item['open']) echo "trig";?>" <?php if ("2" === $item['open']) echo "target='_blank' ";?> style="color: <?php echo esc_attr($item['text_color'])?>">
            <li class="d-flex align-items-center justify-content-center" style="<?php if ($item['background_color'] !== 'transparent') echo 'border-bottom:0px;border-right:0px solid;'?> width: <?php echo esc_attr($width);?>%; height: <?php echo esc_attr($height);?>px; background: <?php echo esc_attr($item['background_color'])?>">
              <?php

              echo esc_html($item['title'])

              ?>
            </li>
          </a>
        <?php } ?>
      <?php } ?>
    </ul>
  <?php } ?>


</div>

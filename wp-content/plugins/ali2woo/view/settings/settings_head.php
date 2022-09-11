<h1><?php _ex('Aliexpress Dropship Settings', 'Setting title', 'ali2woo');?></h1>
<div class="a2w-content">
    <?php include_once A2W()->plugin_path() . '/view/chrome_notify.php'; ?>
    
    <ul class="tabs">
      <?php foreach($modules as $module):?>
      <li class="tabs__tab <?php echo $current_module == $module['id'] ? 'active' : ""; ?>" role="presentation" ><a class="tabs__link" href="<?php echo admin_url('admin.php?page=a2w_setting&subpage='.$module['id']); ?>"><?php echo $module['name'] ?></a></li>
      <?php endforeach; ?>
    </ul>

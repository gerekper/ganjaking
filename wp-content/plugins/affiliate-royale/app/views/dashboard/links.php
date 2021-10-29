<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>
<h3><?php _e('My Links &amp; Banners', 'affiliate-royale', 'easy-affiliate'); ?></h3>

<ul class="wafp-link-list">
<?php
  foreach($links as $link)
  {
    ?>
  <li>
    <div class="wafp-target-url">
      <strong><?php _e('Target URL:', 'affiliate-royale', 'easy-affiliate'); ?></strong>&nbsp;<?php echo $link->rec->target_url; ?><br/>
    </div>
    <div class="wafp-link-code">
      <div><strong><?php _e('Code:', 'affiliate-royale', 'easy-affiliate'); ?></strong></div>
      <?php
        if( empty( $link->rec->description ) and empty( $link->rec->image ) ):
          $link_code = htmlentities($link->display_url($affiliate_id));
        else:
          $link_code = htmlentities($link->link_code($affiliate_id));
        endif;
      ?>
      <input type="text" style="display: inline-block;" onfocus="this.select();" onclick="this.select();" readonly="true" value="<?php echo $link_code; ?>" />
      <span class="wafp-clipboard"><i class="ar-icon-clipboard ar-list-icon icon-clipboardjs" data-clipboard-text="<?php echo $link_code; ?>"></i></span>
    </div>
    <div class="wafp-link-preview">
      <strong><?php _e('Preview:', 'affiliate-royale', 'easy-affiliate'); ?></strong>
      <?php
        if(isset($link->rec->image) and !empty($link->rec->image)){
          echo '<br/>' . $link->link_code($affiliate_id, '_blank');
          if ($link->rec->width and $link->rec->height)
            echo "<div>({$link->rec->width}x{$link->rec->height})</div>";
        }
        else
          echo $link->link_code($affiliate_id, '_blank');
      ?>
    </div>
    <?php
      if(!empty($link->rec->info)):
        ?>
        <div class="wafp-additional-info">
        <strong><?php _e('Additional Info:', 'affiliate-royale', 'easy-affiliate'); ?></strong> <?php echo stripslashes($link->rec->info); ?>
        </div>
        <?php
      endif;
    ?>

  </li>
    <?php
  }
  do_action('wafp-dashboard-links-page-li', $affiliate_id);
?>
</ul>

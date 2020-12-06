<?php $perpage = (isset($_REQUEST['perpage']))?$_REQUEST['perpage']:10; ?>

<input id="cspf-table-search" value="<?php echo (isset($_REQUEST['search']))?$_REQUEST['search']:''; ?>" data-value="<?php _e('Search ...', 'affiliate-royale', 'easy-affiliate'); ?>" /><br/><br/>
<div id="table-actions">
  <?php _e('Display', 'affiliate-royale', 'easy-affiliate'); ?>&nbsp;
  <select id="cspf-table-perpage">
    <option value="10"<?php selected(10, $perpage); ?>>10</option>
    <option value="25"<?php selected(25, $perpage); ?>>25</option>
    <option value="50"<?php selected(50, $perpage); ?>>50</option>
    <option value="100"<?php selected(100, $perpage); ?>>100&nbsp;</option>
  </select>&nbsp;
  <?php _e('entries', 'affiliate-royale', 'easy-affiliate'); ?>
</div>

<?php
//Loop for each record
if(!empty($records)) {
  $row_index = 0;
  foreach($records as $rec) {
    $alternate = ( $row_index++ % 2 ? '' : 'alternate' );
    //Open the line
    ?>
    <tr id="record_<?php echo $rec->subscr_id; ?>" class="<?php echo $alternate; ?>">
    <?php
    foreach( $columns as $column_name => $column_display_name ) {
      //Style attributes for each col
      $class = "class='$column_name column-$column_name'";
      $style = "";
      if( in_array( $column_name, $hidden ) ) $style = ' style="display:none;"';
      $attributes = $class . $style;

      //Shouldn't be here
      //$editlink  = '/wp-admin/link.php?action=edit&link_id='.(int)$rec->link_id;

      //Display the cell
      switch( $column_name ) {
        case 'col_post_date':
          ?>
          <td <?php echo $attributes; ?>><?php echo $rec->post_date; ?></td>
          <?php
          break;
        case 'col_subscr_id':
          ?>
          <td <?php echo $attributes; ?>><?php echo $rec->subscr_id; ?></td>
          <?php
          break;
        case 'col_affiliate':
          ?>
          <td <?php echo $attributes; ?>><a href="<?php echo admin_url("/user-edit.php?user_id={$rec->affiliate_id}"); ?>"><?php echo $rec->affiliate; ?></a></td>
          <?php
          break;
        case 'col_subscr_type':
          ?>
          <td <?php echo $attributes; ?>><?php echo ucwords($rec->subscr_type); ?></td>
          <?php
          break;
        case 'col_del_sub':
          ?>
          <td <?php echo $attributes; ?>>
            <a href="#" class="wafp_del_sub wafp-icon remove-16" data-value="<?php echo $rec->subscr_id; ?>" title="<?php _e('Delete this Subscription.', 'affiliate-royale', 'easy-affiliate'); ?>"></a>
          </td>
          <?php
          break;
      }
    }

    ?>
    </tr>
    <?php
  }
}

<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>
<?php
if(!empty($records)) {
  $row_index = 0;
  foreach($records as $row) {
    $alternate = ( $row_index++ % 2 ? '' : 'alternate' );
    //Open the line
    ?>
    <tr id="record_<?php echo $row->id; ?>" class="<?php echo $alternate; ?>">
    <?php
    foreach( $columns as $column_name => $column_display_name ) {
      //Style attributes for each col
      $class = "class='$column_name column-$column_name'";
      $style = "";
      if( in_array( $column_name, $hidden ) ) $style = ' style="display:none;"';
      $attributes = $class . $style;

      $editlink = admin_url( 'user-edit.php?user_id=' . (int)$row->affiliate_id );

      //Display the cell
      switch( $column_name ) {
        case 'col_created_at': ?>
          <td><?php echo $row->created_at; ?></td>
          <?php break;
        case 'col_user_login': ?>
          <td><a href="<?php echo $editlink; ?>"><?php echo $row->user_login; ?></a></td>
          <?php break;
        case 'col_target_url': ?>
          <td><?php echo $row->target_url; ?></td>
          <?php break;
        case 'col_ip': ?>
          <td><?php echo $row->ip; ?></td>
          <?php break;
        case 'col_referrer':
          if( !empty($row->referrer) ): ?>
            <td><a href="<?php echo $row->referrer; ?>"><?php echo $row->referrer; ?></a></td>
            <?php
          else: ?>
            <td><?php echo $row->referrer; ?></td>
            <?php
          endif;
          break;
      }
    }
    ?>
    </tr>
    <?php
  }
}

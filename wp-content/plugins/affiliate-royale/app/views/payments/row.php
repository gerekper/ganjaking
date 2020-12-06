<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>
<?php
if(!empty($records)) {
  $row_index = 0;
  foreach($records as $row) {
    $alternate = ( $row_index++ % 2 ? '' : 'alternate' );
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
        case 'col_affiliate': ?>
          <td><a href="<?php echo $editlink; ?>"><?php echo $row->affiliate; ?></a></td>
          <?php break;
        case 'col_created_at': ?>
          <td><?php echo $row->created_at; ?></a></td>
          <?php break;
        case 'col_amount': ?>
          <td><?php echo WafpAppHelper::format_currency($row->amount); ?></a></td>
          <?php break;
      }
    }
    ?>
    </tr>
    <?php
  }
}

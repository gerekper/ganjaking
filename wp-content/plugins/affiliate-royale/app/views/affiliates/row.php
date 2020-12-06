<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
//Loop for each record
if(!empty($records)) {
  $row_index = 0;
  foreach($records as $rec) {
    $alternate = ( $row_index++ % 2 ? '' : 'alternate' );
    ?>
    <tr id="record_<?php echo $rec->ID; ?>" class="<?php echo $alternate; ?>">
    <?php
    foreach( $columns as $column_name => $column_display_name ) {
      //Style attributes for each col
      $class = "class='$column_name column-$column_name'";
      $style = "";
      if( in_array( $column_name, $hidden ) ) $style = ' style="display:none;"';
      $attributes = $class . $style;

      //edit link
      $editlink = admin_url( 'user-edit.php?user_id=' . (int)$rec->ID );

      //Display the cell
      switch( $column_name ) {
        case 'col_signup_date': ?>
          <td <?php echo $attributes; ?>><?php echo $rec->signup_date; ?></td>
          <?php
          break;
        case 'col_username': ?>
          <td <?php echo $attributes; ?>><a href="<?php echo $editlink; ?>"><?php echo $rec->username; ?></a></td>
          <?php
          break;
        case 'col_first_name': ?>
          <td <?php echo $attributes; ?>><?php echo $rec->first_name; ?></td>
          <?php
          break;
        case 'col_last_name': ?>
          <td <?php echo $attributes; ?>><?php echo $rec->last_name; ?></td>
          <?php
          break;
        case 'col_ID': ?>
          <td <?php echo $attributes; ?>><?php echo $rec->ID; ?></td>
          <?php
          break;
        case 'col_mtd_clicks': ?>
          <td <?php echo $attributes; ?>><?php echo $rec->mtd_clicks; ?></td>
          <?php
          break;
        case 'col_ytd_clicks': ?>
          <td <?php echo $attributes; ?>><?php echo $rec->ytd_clicks; ?></td>
          <?php
          break;
        case 'col_mtd_commissions': ?>
          <td <?php echo $attributes; ?>><?php echo $rec->mtd_commissions; ?></td>
          <?php
          break;
        case 'col_ytd_commissions': ?>
          <td <?php echo $attributes; ?>><?php echo $rec->ytd_commissions; ?></td>
          <?php
          break;
        case 'col_parent_name': ?>
          <td <?php echo $attributes; ?>><?php echo $rec->parent_name; ?></td>
          <?php
          break;
        case 'col_post_date': ?>
          <td <?php echo $attributes; ?>><?php echo $rec->post_date; ?></td>
          <?php
          break;
      }
    }

    ?>
    </tr>
    <?php
  }
}

<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

if(!empty($records)) {
  $row_index = 0;
  foreach($records as $rec) {
    $alternate = ( $row_index++ % 2 ? '' : 'alternate' );
    //Open the line
    ?>
    <tr id="record_<?php echo $rec->ID; ?>" class="<?php echo $alternate; ?>">
    <?php
    foreach($columns as $column_name => $column_display_name) {
      //Style attributes for each col
      $class = "class=\"{$column_name} column-{$column_name}\"";
      $style = "";
      if(in_array($column_name, $hidden)) {
        $style = ' style="display:none;"';
      }
      $attributes = $class.$style;
      //$editlink = admin_url('user-edit.php?user_id='.(int)$rec->ID);
      //$deletelink = admin_url('user-edit.php?user_id='.(int)$rec->ID);
      $deletelink = wp_nonce_url( "users.php?action=delete&amp;user={$rec->ID}", 'bulk-users' );
      $editlink = esc_url( add_query_arg( 'wp_http_referer', urlencode( wp_unslash( $_SERVER['REQUEST_URI'] ) ), get_edit_user_link( $rec->ID ) ) );

      //Display the cell
      switch($column_name) {
        case 'col_id':
          ?>
          <td <?php echo $attributes; ?>><?php echo $rec->ID; ?></td>
          <?php
          break;
        case 'col_file_id':
            ?>
            <td <?php echo $attributes; ?>><a href="<?php echo get_edit_post_link($rec->file_id) ?>"><?php echo get_the_title($rec->file_id); ?></a></td>
            <?php
          break;
        case 'col_user_id':
            $user = get_user_by( 'id', $rec->user_id );
            if($user){
              $user = sprintf('<a href="%s">%s</a>', get_edit_user_link($rec->user_id), $user->user_login );
            }
            else{
              $user = __("Guest", 'memberpress-downloads');
            }
            ?>
            <td <?php echo $attributes; ?>><?php echo $user; ?></td>
            <?php
          break;
        case 'col_ip_address':
            ?>
            <td <?php echo $attributes; ?>><?php echo $rec->ip_address; ?></td>
            <?php
          break;
        case 'col_created_at':
            ?>
            <td <?php echo $attributes; ?>><?php echo date('F d, Y H:i:s', strtotime($rec->created_at)); ?></td>
            <?php
          break;

        default:
          \MeprHooks::do_action('mepr_members_list_table_row', $attributes, $rec, $column_name, $column_display_name);
      }
    }
    ?>    </tr>
    <?php
  } //End foreach
} //End if

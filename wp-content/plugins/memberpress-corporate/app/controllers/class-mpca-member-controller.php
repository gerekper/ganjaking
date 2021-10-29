<?php if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); }
class MPCA_Member_Controller {
  public function __construct() {
    add_filter('mepr-admin-members-cols', array($this, 'customize_admin_members_cols'));
    add_filter('mepr_members_list_table_row', array($this, 'customize_admin_members_table_content'), 10, 4);
  }

  public function customize_admin_members_cols($cols) {
    $cols['col_role'] = __('Role', 'memberpress-corporate');

    return $cols;
  }

  public function customize_admin_members_table_content($attributes, $rec, $column_name, $column_display_name) {
    if($column_name === 'col_role') {
      $user = get_user_by('login', $rec->username);
      $caid = get_user_meta($user->ID, 'mpca_corporate_account_id');
      $ca_type = __('None', 'memberpress-corporate');
      $user_corporate_accounts = MPCA_Corporate_Account::get_all_by_user_id($user->ID);
      if(!empty($caid)) {
        $ca_type = __('Sub Account', 'memberpress-corporate');
      }
      elseif(!empty($user_corporate_accounts)) {
        // Check if the member is a corporate account owner
        $ca_type = __('Corp Account', 'memberpress-corporate');
      }
      ?>
        <td <?php echo $attributes; ?>><?php echo $ca_type; ?></td>
      <?php
    }
  }
}

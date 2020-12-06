<div class="mp_wrapper">
  <h3 class="mpca-fat-bottom"><?php printf(__('Corporate account for %s', 'memberpress-corporate'), $owner_name); ?></h3>

  <div id="mpca_sub_accounts_used" class="mpca-fat-bottom">
    <h4><?php printf(__('%1$s of %2$s Sub Accounts Used', 'memberpress-corporate'), $ca->num_sub_accounts_used(), $ca->num_sub_accounts); ?></h4>
  </div>

  <?php MeprView::render('/shared/errors', compact('errors','message')); ?>

  <div id="mpca-add-sub-user" class="mpca-fat-bottom">

    <?php
      $sub_welcome_checked = isset($_POST['action']) ? isset($_POST['userdata[welcome]']) : false;
    ?>

    <?php if($ca->num_sub_accounts > $ca->num_sub_accounts_used()): ?>
    <button id="mpca-add-sub-user-btn" class="mpca-fat-bottom" type="button" value=""><?php _e('Add Sub Account', 'memberpress-corporate') ?></button>
    <?php endif ?>

    <form action="" method="post" id="mpca-add-sub-user-form" class="mpca-hidden">
      <input type="hidden" name="action" value="manage_sub_accounts" />
      <input type="hidden" name="manage_sub_accounts_form" value="add" />
      <label>
        <span><?php _e('Existing Username', 'memberpress-corporate'); ?> </span>
      </label>
      <?php if(MeprUtils::is_mepr_admin()): ?>
        <input value="" type="text" name="userdata[existing_login]" class="mepr_suggest_user" placeholder="<?php _e('Begin Typing Name', 'memberpress', 'memberpress-corporate') ?>" />
      <?php else: ?>
        <input value="" type="text" name="userdata[existing_login]" />
      <?php endif ?>
      <label>
        <span><?php echo '- '; _e('OR', 'memberpress-corporate'); echo ' -'; ?></span>
      </label>
    <?php if(!$mepr_options->username_is_email): ?>
      <label>
        <span><?php _e('Username', 'memberpress-corporate'); ?> </span>
        <input id="" type="text" name="userdata[user_login]" />
      </label>
      <?php endif ?>

      <label>
        <span><?php _e('Email', 'memberpress-corporate'); ?> </span>
        <input id="" type="text" name="userdata[user_email]" />
      </label>

      <?php if($mepr_options->show_fname_lname): ?>
        <label>
          <span><?php _e('First Name', 'memberpress-corporate'); ?></span>
          <input id="" type="text" name="userdata[first_name]" />
        </label>
        <label>
          <span><?php _e('Last Name', 'memberpress-corporate'); ?></span>
          <input id="" type="text" name="userdata[last_name]" />
        </label>
      <?php endif ?>

      <label class="mpca-fat-bottom">
        <input type="checkbox" name="userdata[welcome]" <?php checked($sub_welcome_checked); ?> />
        <span><?php _e('Send NEW members the welcome email', 'memberpress-corporate'); ?></span>
      </label>

      <input type="submit" value="<?php _e('Submit', 'memberpress-corporate') ?>" />
    </form>
  </div>

  <div class="mpca-search mpca-fat-bottom">
    <input
      id="mpca_sub_account_search"
      type="text" placeholder="<?php _e('Search Sub Accounts...', 'memberpress-corporate'); ?>"
      value="<?php echo $search; ?>" />
  </div>

  <?php if(!empty($sub_accounts)): ?>
    <?php $alt = false; ?>
    <div class="mpca-sub-account-page-info">
      <?php printf(__('Page %1$s of %2$s (%3$s Sub Accounts)', 'memberpress-corporate'), $currpage, $total_pages, $total_sub_accounts); ?>
    </div>
    <div class="mpca-table-overflow">
      <table id="mpca-sub-accounts-table" class="mepr-account-table">
        <thead>
          <tr>
            <th><?php _ex('Username', 'ui', 'memberpress-corporate'); ?></th>
            <th><?php _ex('Email', 'ui', 'memberpress-corporate'); ?></th>
            <th><?php _ex('First Name', 'ui', 'memberpress-corporate'); ?></th>
            <th><?php _ex('Last Name', 'ui', 'memberpress-corporate'); ?></th>
            <th> </th>
            <?php do_action('mpca-sub-accounts-th', $mepr_current_user, $sub_accounts); ?>
          </tr>
        </thead>
        <tbody>
          <?php
          foreach($sub_accounts as $sa):
            ?>
            <tr id="mpca-sub-accounts-row-<?php echo $sa->ID; ?>" class="mpca-sub-accounts-row <?php echo (isset($alt) && !$alt)?'mepr-alt-row':''; ?>">
              <td><?php echo $sa->user_login; ?></td>
              <td><?php echo $sa->user_email; ?></td>
              <td><?php echo $sa->first_name; ?></td>
              <td><?php echo $sa->last_name; ?></td>
              <td><a href="" data-ca="<?php echo $ca->id; ?>" data-sa="<?php echo $sa->ID; ?>" class="mpca-remove-sub-account"><?php _e('Remove', 'memberpress-corporate'); ?></a></td>
              <?php do_action('mpca-sub-accounts-td', $mepr_current_user, $sa); ?>
            </tr>
            <?php $alt = !$alt; ?>
          <?php endforeach; ?>
          <?php do_action('mpca-sub-accounts-table', $mepr_current_user, $sub_accounts); ?>
        </tbody>
      </table>
    </div>
    <br/>
    <div id="mepr-sub-account-paging">
      <?php $sub_account_search = !empty($search) ? "&search={$search}" : ''; ?>
      <?php if($prev_page): ?>
        <a href="<?php echo "{$account_url}{$delim}action=manage_sub_accounts&ca={$ca->uuid}&currpage={$prev_page}{$sub_account_search}"; ?>">&lt;&lt; <?php _ex('Previous Page', 'ui', 'memberpress-corporate'); ?></a>
      <?php endif; ?>
      <?php if($next_page): ?>
        <a href="<?php echo "{$account_url}{$delim}action=manage_sub_accounts&ca={$ca->uuid}&currpage={$next_page}{$sub_account_search}"; ?>" style="float:right;"><?php _ex('Next Page', 'ui', 'memberpress-corporate'); ?> &gt;&gt;</a>
      <?php endif; ?>
    </div>
    <div style="clear:both">&nbsp;</div>
    <?php
  else:
    _ex('You have no sub accounts to display.', 'ui', 'memberpress-corporate');
  endif;
  ?>
  <div id="mpca_export_sub_accounts" class="mpca-fat-bottom">
    <a href="<?php echo $ca->export_url(); ?>"><?php _e('Export Sub Accounts', 'memberpress-corporate');?></a>
  </div>

  <div id="mpca_signup_url" class="mpca-fat-bottom">
    <h4><?php _e('Signup URL', 'memberpress-corporate'); ?></h4>
    <p><?php _e('People signing up with this link will be automatically added to your account', 'memberpress-corporate'); ?></p>

    <?php $app_helper->clipboard_input($ca->signup_url(), '', 'mpca-20'); ?>
  </div>

  <?php if($ca->num_sub_accounts > $ca->num_sub_accounts_used() && defined('MPCA_IMPORTERS_PATH') === true): ?>
  <div id="mpca_import_sub_accounts">
    <h4><?php _e('Import Sub Accounts via CSV', 'memberpress-corporate'); ?></h4>
    <div><small><em><?php _e('(Maximum 200 Sub Accounts per CSV file)', 'memberpress-corporate'); ?></em></small></div>

    <?php
      $csv_notice_checked = isset($_POST['action']) ? isset($_POST['notice']) : true;
      $csv_welcome_checked = isset($_POST['action']) ? isset($_POST['welcome']) : false;
    ?>

    <form action="" method="post" enctype="multipart/form-data">
      <input type="hidden" name="action" value="manage_sub_accounts" />
      <input type="hidden" name="manage_sub_accounts_form" value="import" />
      <input type="hidden" name="ca" value="<?php echo $ca->uuid; ?>" />
      <br/>
      <input type="checkbox" name="notice" <?php checked($csv_notice_checked); ?> />
      <?php _e('Send NEW members a password reset link (does not email existing members)', 'memberpress-corporate'); ?>
      <br/>
      <input type="checkbox" name="welcome" <?php checked($csv_welcome_checked); ?> />
      <?php _e('Send NEW members the welcome email if they haven\'t received it already for the membership.', 'memberpress-corporate'); ?>
      <br/><br/>
      <input type="file" name="mpca_sub_accounts_csv" id="mpca_sub_accounts_csv">
      <input type="submit" value="<?php _e('Upload CSV', 'memberpress-corporate'); ?>" name="submit">
      <span class="mpca-loading-gif" style="display: none;">
        <img src="<?php echo admin_url('images/loading.gif'); ?>" />
        <em><?php _e('Importing sub accounts...', 'memberpress-corporate'); ?></em>
      </span>
    </form>
  </div>
  <?php endif ?>
</div>

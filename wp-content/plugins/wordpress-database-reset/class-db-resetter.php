<?php

if (!class_exists('DB_Resetter')) :

  class DB_Resetter
  {

    private $backup;
    private $blog_data;
    private $theme_plugin_data;
    private $user_data;
    private $preserved;
    private $selected;
    private $reactivate;
    private $user;
    private $wp_tables;
    private $reset_users = false;

    public function __construct()
    {
      $this->set_wp_tables();
      $this->set_user();
    }

    public function reset(array $tables)
    {
      if (wp_verify_nonce(@$_REQUEST['submit_reset_form'], 'reset_nounce') && current_user_can('administrator')) {
         // Check if current user is Admin and check the nonce

        if (in_array('users', $tables)) {
          $this->reset_users = true;
        }

        $this->validate_selected($tables);
        $this->set_backup();
        $this->reinstall();
        $this->restore_backup();
      } else {
        throw new Exception(__('Please reload the page and try again. Double check your security code.', 'wordpress-database-reset'));
      }
    }

    private function validate_selected(array $tables)
    {
      if (!empty($tables) && is_array($tables)) {
        $this->selected = array_flip($tables);
        return;
      }

      throw new Exception(__('You did not select any database tables', 'wordpress-database-reset'));
    }

    private function set_backup()
    {
      $this->set_tables_to_preserve($this->selected);
      $this->back_up_tables($this->preserved);
      $this->set_user_session_tokens();
      $this->set_blog_data();

      if ($this->should_restore_theme_plugin_data()) {
        $this->set_theme_plugin_data();
      }
    }

    private function set_tables_to_preserve(array $tables)
    {
      $this->preserved = array_diff_key($this->wp_tables, $tables);
    }

    private function back_up_tables(array $tables)
    {
      global $wpdb;
      $this->backup = array();

      foreach ($tables as $table) {
        $this->backup[$table] = $wpdb->get_results("SELECT * FROM {$table}");
      }
    }

    private function set_user_session_tokens()
    {
      if (get_user_meta($this->user->ID, 'session_tokens')) {
        $this->user_data = array(
          'session_tokens' => get_user_meta($this->user->ID, 'session_tokens', true)
        );
      }
    }

    private function set_blog_data()
    {
      $this->blog_data = array(
        'name' => get_option('blogname'),
        'public' => get_option('blog_public'),
        'site_url' => get_option('siteurl')
      );
    }

    private function should_restore_theme_plugin_data()
    {
      return ('true' === $this->reactivate);
    }

    private function set_theme_plugin_data()
    {
      $this->theme_plugin_data = array(
        'active-plugins' => get_option('active_plugins'),
        'current-theme' => get_option('current_theme'),
        'stylesheet' => get_option('stylesheet'),
        'template' => get_option('template')
      );
    }

    private function reinstall()
    {
      $this->drop_wp_tables();
      $this->install_wp();
      $this->update_user_settings();
    }

    private function drop_wp_tables()
    {
      global $wpdb;

      foreach ($this->wp_tables as $wp_table) {
        $wpdb->query("DROP TABLE {$wp_table}");
      }
    }

    private function install_wp()
    {
      return db_reset_install(
        $this->blog_data['name'],
        $this->user->user_login,
        $this->user->user_email,
        $this->blog_data['public'],
        $this->blog_data['site_url']
      );
    }

    private function update_user_settings()
    {
      global $wpdb;

      $user_id = $this->reset_users ? 1 : $this->user->ID;

      $wpdb->query(
        $wpdb->prepare(
          "UPDATE $wpdb->users
          SET user_pass = '%s', user_activation_key = ''
          WHERE ID = '%d'",
          $this->user->user_pass,
          $user_id
        )
      );

      if ($this->reset_users) {
        wp_clear_auth_cookie();
        wp_set_auth_cookie(true);
      }
    }

    private function restore_backup()
    {
      $this->delete_backup_table_rows($this->preserved);
      $this->restore_backup_tables($this->backup);
      $this->restore_user_session_tokens();
      $this->assert_theme_plugin_data_needs_reset();
    }

    private function delete_backup_table_rows(array $tables)
    {
      global $wpdb;

      foreach ($tables as $table) {
        $wpdb->query("DELETE FROM {$table}");
      }
    }

    private function restore_backup_tables(array $tables)
    {
      global $wpdb;

      foreach ($tables as $table => $data) {
        foreach ($data as $row) {
          $columns = $values = array();

          foreach ($row as $column => $value) {
            $columns[] = $column;
            $values[] = esc_sql($value);
          }

          $wpdb->query("INSERT INTO $table (" . implode(', ', $columns) . ") VALUES ('" . implode("', '", $values) . "')");
        }
      }
    }

    private function restore_user_session_tokens()
    {
      add_user_meta($this->user->ID, 'session_tokens', $this->user_data['session_tokens']);
    }

    private function assert_theme_plugin_data_needs_reset()
    {
      if ($this->should_restore_theme_plugin_data()) {
        $this->restore_theme_plugin_data();
      }
    }

    private function restore_theme_plugin_data()
    {
      update_option('active_plugins', $this->theme_plugin_data['active-plugins']);
      update_option('template', $this->theme_plugin_data['template']);
      update_option('stylesheet', $this->theme_plugin_data['stylesheet']);

      if (!empty($this->theme_plugin_data['current-theme'])) {
        update_option('current_theme', $this->theme_plugin_data['current-theme']);
      }
    }

    public function set_reactivate($with_theme_plugin_data)
    {
      $this->reactivate = $with_theme_plugin_data;
    }

    private function set_wp_tables()
    {
      global $wpdb;
      $this->wp_tables = $wpdb->tables();
    }

    public function get_wp_tables()
    {
      return $this->wp_tables;
    }

    private function set_user()
    {
      global $current_user;

      $this->user = (0 !== $current_user->ID) ?
        wp_get_current_user() :
        get_userdata(1);
    }

    public function get_user()
    {
      return $this->user;
    }
  }

endif;

<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>
<div id="header" style="width: 680px; padding: 0px; margin: 0 auto; text-align: left;">
  <h1 style="font-size: 30px; margin-bottom:4px;"><?php _ex('Hi {$user_first_name}!', 'welcome-email', 'memberpress-corporate'); ?></h1>
</div>
<div id="body" style="width: 600px; background: white; padding: 40px; margin: 0 auto; text-align: left;">
  <div id="receipt">
    <div class="section" style="display: block; margin-bottom: 24px;"><?php _ex('Welcome to <strong>{$blog_name}</strong>. You now have access to the {$product_name} membership content.', 'welcome-email', 'memberpress-corporate'); ?></div>
    <div class="section" style="display: block; margin-bottom: 24px;"><?php _ex('Use the info below to access your account.', 'welcome-email', 'memberpress-corporate'); ?></div>
    <div class="section" style="display: block; margin-bottom: 24px;"><?php _ex('You can login here: <a href="{$login_page}">{$login_page}</a>', 'welcome-email', 'memberpress-corporate'); ?></div>
    <div class="section" style="display: block; margin-bottom: 24px;"><?php _ex('Account Info:', 'welcome-email', 'memberpress-corporate'); ?></div>
    <div class="section" style="display: block; margin-bottom: 24px;">
      <table style="clear: both;" class="transaction">
        <tr><th style="text-align: left;"><?php _ex('Membership:', 'welcome-email', 'memberpress-corporate'); ?></th><td>{$product_name}</td></tr>
        <tr><th style="text-align: left;"><?php _ex('Username:', 'ui', 'memberpress', 'memberpress-corporate'); ?></th><td>{$username}</td></tr>
        <tr><th style="text-align: left;"><?php _ex('Password:', 'ui', 'memberpress', 'memberpress-corporate'); ?></th><td><?php _ex('*** Password you set during signup ***', 'ui', 'memberpress', 'memberpress-corporate'); ?></td></tr>
      </table>
    </div>
    <div class="section" style="display: block; margin-bottom: 24px;"><?php _ex('Cheers!', 'ui', 'memberpress', 'memberpress-corporate'); ?></div>
    <div class="section" style="display: block; margin-bottom: 24px;"><?php _ex('The {$blog_name} Team', 'ui', 'memberpress', 'memberpress-corporate'); ?></div>
  </div>
</div>

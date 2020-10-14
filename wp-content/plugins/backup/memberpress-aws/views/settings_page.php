<h2><?php _e('MemberPress AWS', 'memberpress-aws'); ?></h2>

<div>&nbsp;</div>

<form action="" method="post">
  <?php $this->display_option_fields(); ?>

  <div>&nbsp;</div>

  <h3><?php _e('Enable AWS Plugin Automatic Updates', 'memberpress-aws'); ?></h3>
  <table class="form-table">
    <tbody>
      <tr valign="top">
        <th scope="row">
          <label for="mepr_aws_license_key"><?php _e('AWS License Key:', 'memberpress-aws'); ?></label>
        </th>
        <td>
          <input type="text" name="mepr_aws_license_key" id="mepr_aws_license_key" value="<?php echo $license_key; ?>" class="mpaws-text-input form-field" size="30" />
        </td>
      </tr>
    </tbody>
  </table>

  <div>&nbsp;</div>
  <input type="submit" name="<?php _e('Update'); ?>" value="<?php _e('Update'); ?>" class="button button-primary" />
</form>

<script>
  jQuery(document).ready(function($) {
    $('#aws').show();
  });
</script>

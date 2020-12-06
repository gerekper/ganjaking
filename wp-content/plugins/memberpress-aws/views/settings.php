<div id="aws" class="mpaws-options-hidden-pane">
  <h3><?php _e('Amazon AWS Integration', 'memberpress-aws'); ?></h3>
  <table class="form-table">
    <tbody>
      <tr valign="top">
        <th scope="row">
          <label for="mepr_aws_access_key"><?php _e('AWS Access Key:', 'memberpress-aws'); ?></label>
          <?php
            MpawsUtils::info_tooltip(
              'mepr-aws-access-key',
              __('AWS Access Key', 'memberpress-aws'),
              __('You can find your AWS Access Key by logging into your aws management portal.', 'memberpress-aws')
            );
          ?>
        </th>
        <td>
          <input type="text" name="mepr_aws_access_key" id="mepr_aws_access_key" value="<?php echo $access_key; ?>" class="mpaws-text-input form-field" size="30" />
        </td>
      </tr>
      <tr valign="top">
        <th scope="row">
          <label for="mepr_aws_secret_key"><?php _e('AWS Secret Key:', 'memberpress-aws'); ?></label>
          <?php
            MpawsUtils::info_tooltip(
              'mepr-aws-secret-key',
              __('AWS Secret Key', 'memberpress-aws'),
              __('You can find your AWS Access Key by logging into your aws management portal.', 'memberpress-aws')
            );
          ?>
        </th>
        <td>
          <input type="password" name="mepr_aws_secret_key" id="mepr_aws_secret_key" value="<?php echo $secret_key; ?>" class="mpaws-text-input form-field" size="30" />
        </td>
      </tr>
      <?php if( MPAWS_CAN_USE_SDK ): ?>
        <tr valign="top">
          <th scope="row">
            <label for="mepr_aws_v4_enabled"><?php _e('Use V4 Signatures:', 'memberpress-aws'); ?></label>
            <?php
              MpawsUtils::info_tooltip(
                'mepr-aws-v4-enabled',
                __('Enable AWS V4 Signatures', 'memberpress-aws'),
                __('All AWS regions support V4 signatures but we recommend enabling AWS V4 Signatures only if your AWS region is new (regions such as eu-central-1 that are newer than January, 2014 only support V4 signatures).<br/><br/>V4 signatures are more secure than the default signatures but are more complex to generate so they can affect your website\'s performance.<br/><br/><strong>Note:</strong> When using V4 Signatures you must also provide the region that you\'re using.', 'memberpress-aws')
              );
            ?>
          </th>
          <td>
            <input type="checkbox" name="mepr_aws_v4_enabled" id="mepr_aws_v4_enabled" class="form-field" <?php checked($v4_enabled); ?> />
          </td>
        </tr>
      <?php else: ?>
        <tr valign="top">
          <th scope="row">
            <label for="mepr_aws_v4_enabled"><?php _e('Use V4 Signatures:', 'memberpress-aws'); ?></label>
            <?php
              MpawsUtils::info_tooltip(
                'mepr-aws-v4-enabled',
                __('Enable AWS V4 Signatures', 'memberpress-aws'),
                __('All AWS regions support V4 signatures but we recommend enabling AWS V4 Signatures only if your AWS region is new (regions such as eu-central-1 that are newer than January, 2014 only support V4 signatures).<br/><br/>V4 signatures are more secure than the default signatures but are more complex to generate so they can affect your website\'s performance.<br/><br/><strong>Note:</strong> When using V4 Signatures you must also provide the region that you\'re using.', 'memberpress-aws')
              );
            ?>
          </th>
          <td>
            <div><b><?php _e('Your webserver must be upgraded to enable V4 Signatures:'); ?></b></div>
            <?php
              $good_str = __('<span style="color: green;"><b>GOOD</b></span>');
              $bad_str = __('<span style="color: red;"><b>BAD</b></span>');
              $php_str = (version_compare(PHP_VERSION, '5.3.3', '>=') ? $good_str : $bad_str . __(' &ndash; you need to be running PHP version 5.3.3 or above'));

              if( function_exists('curl_version') ) {
                $curl_version = curl_version();
                $curl_ver_str = (version_compare($curl_version['version'], '7.16.2', '>=') ? $good_str : $bad_str . __(' &ndash; you need to be running cURL version 7.16.2 or above'));
                $curl_str = sprintf(__('cURL is installed: %s'), $good_str);
                $ssl_str = (isset($curl_version['ssl_version']) ? sprintf(__('cURL was compiled with OpenSSL: %s'), $good_str) : sprintf(__('cURL was not compiled with OpenSSL: %s'), $bad_str));
                $libz_str = (isset($curl_version['libz_version']) ? sprintf(__('cURL was compiled with libz: %s'), $good_str) : sprintf(__('cURL was not compiled with libz: %s'), $bad_str));
              }
              else {
                $curl_str = sprintf(__('cURL isn\'t installed: %s'), $bad_str);
              }
            ?>
            <ul>
              <li><?php printf( __('You\'re running PHP Version %1$s: %2$s'), PHP_VERSION, $php_str ); ?></li>
              <li><?php echo $curl_str; ?></li>
              <?php if( isset($curl_ver_str) ): ?>
                <li><?php printf( __('You\'re running cURL version %1$s: %2$s'), $curl_version['version'], $curl_ver_str ); ?></li>
                <li><?php echo $ssl_str; ?></li>
                <li><?php echo $libz_str; ?></li>
              <?php endif; ?>
            </ul>
          </td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
  <?php if( MPAWS_CAN_USE_SDK ): ?>
    <div class="mpaws-hidden mpaws-options-pane" id="mepr_aws_v4_options" style="background-color: white;">
      <table class="form-table">
        <tbody>
          <tr valign="top">
            <th scope="row">
              <label for="mepr_aws_region"><?php _e('AWS Region:', 'memberpress-aws'); ?></label>
              <?php
                MpawsUtils::info_tooltip(
                  'mepr-aws-region',
                  __('AWS Region', 'memberpress-aws'),
                  __('You can find your AWS Region by logging into your AWS management portal.', 'memberpress-aws')
                );
              ?>
            </th>
            <td>
              <select name="mepr_aws_region" id="mepr_aws_region">
                <?php foreach( $regions as $reg_val => $reg_str ): ?>
                  <option value="<?php echo $reg_val; ?>" <?php selected($region,$reg_val); ?>><?php echo $reg_str; ?></option>
                <?php endforeach; ?>
              </select>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  <?php endif; ?>

  <div>&nbsp;</div>

  <!--
  <h3>
    <?php _e('AWS Domain Restriction Policy'); ?>
    <?php
      MpawsUtils::info_tooltip(
        'mepr-aws-domain-restriction-policy',
        __('AWS Domain Restriction Policy', 'memberpress-aws'),
        __('Copy & Paste this policy into your AWS Bucket Permissions to restrict access to content for any buckets to this website. NOTE: Make sure you change the name of \'examplebucket\' to your bucket name before saving the policy at AWS.', 'memberpress-aws')
      );
    ?>
  </h3>
  <div><a href="https://www.memberpress.com/user-manual/memberpress-aws/#domain-restriction-policy"><?php _e('Click here to read more about adding a domain restriction policy'); ?></a></div>
  <?php
    $aws_domain = MpawsUtils::site_domain();
    $aws_policy_id = uniqid();
  ?>
  <textarea id="aws_bucket_policy" class="large-text" style="height: 300px;">
{
  "Version":"2012-10-17",
  "Id":"<?php echo $aws_policy_id; ?>",
  "Statement":[
    {
      "Sid":"Allow get requests originating from <?php echo $aws_domain; ?>",
      "Effect":"Allow",
      "Principal":"*",
      "Action":"s3:GetObject",
      "Resource":"arn:aws:s3:::examplebucket/*",
      "Condition":{
        "StringLike":{"aws:Referer":["http://www.<?php echo $aws_domain; ?>/*","http://<?php echo $aws_domain; ?>/*","https://www.<?php echo $aws_domain; ?>/*","https://<?php echo $aws_domain; ?>/*"]}
      }
    }
  ]
}
  </textarea>
  -->
</div>

<script>
  jQuery(document).ready(function($) {
    $('.nav-tab-wrapper').on('mepr-show-nav-tab', function(e, chosen) {
      $('div.mpaws-options-hidden-pane').hide();
      $('div#' + chosen + '.mpaws-options-hidden-pane').show();
    });

    <?php if( MPAWS_CAN_USE_SDK ): ?>
      //Hide/Show AWS V4 Enabled
      if($('#mepr_aws_v4_enabled').is(":checked")) {
        $('div#mepr_aws_v4_options').slideDown();
      } else {
        $('div#mepr_aws_v4_options').slideUp();
      }
      $('#mepr_aws_v4_enabled').click(function() {
        $('div#mepr_aws_v4_options').slideToggle();
      });
    <?php endif; ?>

    $('#aws_bucket_policy').on('click', function(e) {
      $(this).select();
    });
  });
</script>


<?php require 'header.php'; ?>
  <!-- Header image -->
  <!--[if mso | IE]><table align="center" border="0" cellpadding="0" cellspacing="0" class="" style="width:600px;" width="600" ><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]-->
  <div style="margin:0px auto;max-width:600px;">
    <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="width:100%;">
      <tbody>
        <tr>
          <td style="direction:ltr;font-size:0px;padding:38px 0 0;text-align:center;">
            <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:top;width:600px;" ><![endif]-->
            <div class="mj-column-per-100 mj-outlook-group-fix" style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
              <table border="0" cellpadding="0" cellspacing="0" role="presentation" width="100%">
                <tbody>
                  <tr>
                    <td class="smush-header-logo" style="background-color:#2DC4E0;border-radius:15px 15px 0 0;vertical-align:top;padding:25px 0;">
                      <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="" width="100%">
                        <tbody>
                          <tr>
                            <td align="center" style="font-size:0px;padding:10px 25px;word-break:break-word;">
                              <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="border-collapse:collapse;border-spacing:0px;">
                                <tbody>
                                  <tr>
                                    <td>
                                      <img src="<?php echo esc_url( WP_SMUSH_URL . 'app/assets/images/email/logo.png' ); ?>" style="border:0;outline:none;text-decoration:none;height:30px;width:auto;vertical-align:middle;"  alt="">
                                      <span style="color: #FFFFFF;font-family: Roboto, Arial, sans-serif;font-size: 20px;font-weight: 700;text-align: left;margin-left: 16px;line-height:30px; vertical-align:middle;">
                                          <?php echo esc_html( $title ); ?>
                                      </span>
                                    </td>
                                  </tr>
                                </tbody>
                              </table>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <!--[if mso | IE]></td></tr></table><![endif]-->
          </td>
        </tr>
      </tbody>
    </table>
  </div>
  <!--[if mso | IE]></td></tr></table><![endif]-->
  <!-- END Header image -->
  <!-- Main content -->
  <!--[if mso | IE]><table align="center" border="0" cellpadding="0" cellspacing="0" class="main-content-outlook" style="width:600px;" width="600" ><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]-->
  <div style="margin:0px auto;max-width:600px;">
    <table class="main-content" align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="background:#ffffff;background-color:#ffffff;width:100%;">
      <tbody>
        <tr>
          <td style="direction:ltr;font-size:0px;padding:20px 0 45px;text-align:center;">
            <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:top;width:550px;" ><![endif]-->
            <div class="mj-column-per-100 mj-outlook-group-fix" style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
              <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="margin:0px auto;max-width: 600px;vertical-align:top;" width="100%">
                <tbody>
                  <tr>
                    <td align="left" style="font-size:0px;padding:0;word-break:break-word;">
                      <div style="margin:0px auto;max-width: 600px;font-family:Roboto, Arial, sans-serif;font-size:18px;letter-spacing:-.25px;line-height:30px;text-align:left;color:#1A1A1A;">
                        <?php echo $content_body;//phpcs:ignore ?>
                        <table border="0" cellpadding="0" cellspacing="0" role="presentation" width="100%" style="margin:0px auto;max-width: 600px;padding:0 0;">
                          <tbody>
                              <tr>
                                <td style="margin:0 0;padding:0 25px;">
                                    <table border="0" cellpadding="0" cellspacing="0" role="presentation" width="100%" style="margin:0 auto;max-width:600px;padding:0;">
                                      <tbody>
                                        <tr>
                                          <td style="margin:0;padding:0 0 10px;">
                                            <!--Cheers block-->
                                            <p style="color:#1A1A1A;font-family:Roboto,Arial,sans-serif;font-size:16px;font-weight:normal;line-height:30px;margin:30px 0 0;padding:0;text-align:left;">
                                                <?php esc_html_e( 'Cheers,', 'wp-smushit' ); ?>
                                                <br/>
                                                <?php esc_html_e( 'The WPMU DEV Team.', 'wp-smushit' ); ?>
                                            </p>
                                            <!--End Cheers block-->
                                          </td>
                                        </tr>
                                      </tbody>
                                    </table>
                                  <?php
                                    if( ! empty( $content_upsell ) ) {
                                      echo $content_upsell;//phpcs:ignore
                                    }
                                  ?>
                                </td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <!--[if mso | IE]></td></tr></table><![endif]-->
          </td>
        </tr>
      </tbody>
    </table>
  </div>
  <!--[if mso | IE]></td></tr></table><![endif]-->
  <!-- END Main content -->
  <!-- Footer -->
  <!--[if mso | IE]><table align="center" border="0" cellpadding="0" cellspacing="0" class="" style="width:600px;" width="600" ><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]-->
  <div style="margin:0px auto;border-radius:0 0 15px 15px;max-width:600px;">
    <table class="wpmudev-footer-logo" align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="background:#E7F1FB;background-color:#E7F1FB;width:100%;border-radius:0 0 15px 15px;">
      <tbody>
        <tr>
          <td style="direction:ltr;font-size:0px;padding:20px 0;text-align:center;">
            <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:top;width:600px;" ><![endif]-->
            <div class="mj-column-per-100 mj-outlook-group-fix" style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
              <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="vertical-align:top;" width="100%">
                <tbody>
                  <tr>
                    <td align="center" style="font-size:0px;padding:10px 25px;word-break:break-word;">
                      <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="border-collapse:collapse;border-spacing:0px;">
                        <tbody>
                          <tr>
                            <td style="width:168px;">
                              <img height="auto" src="https://mcusercontent.com/53a1e972a043d1264ed082a5b/images/e60c4943-5368-4e02-3f35-021bbfc3eea4.png" style="border:0;display:block;outline:none;text-decoration:none;height:auto;width:100%;font-size:13px;" width="168" />
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <!--[if mso | IE]></td></tr></table><![endif]-->
          </td>
        </tr>
      </tbody>
    </table>
  </div>
  <!--[if mso | IE]></td></tr></table><table align="center" border="0" cellpadding="0" cellspacing="0" class="" style="width:600px;" width="600" ><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]-->
  <div class="wpmudev-follow-us" style="margin:0px auto;max-width:600px;">
    <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="width:100%;">
      <tbody>
        <tr>
          <td style="direction:ltr;font-size:0px;padding:25px 20px 15px;text-align:center;">
            <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:top;width:560px;" ><![endif]-->
            <div class="mj-column-per-100 mj-outlook-group-fix" style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
              <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="vertical-align:top;" width="100%">
                <tbody>
                  <tr>
                    <td align="center" style="font-size:0px;padding:0;word-break:break-word;">
                      <!--[if mso | IE]><table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" ><tr><td><![endif]-->
                      <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="float:none;display:inline-table;">
                        <tr class="hidden-img">
                          <td style="padding:1px;vertical-align:middle;">
                            <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="background:transparent;border-radius:3px;width:0;">
                              <tr>
                                <td style="font-size:0;height:0;vertical-align:middle;width:0;">
                                  <img height="0" style="border-radius:3px;display:block;" width="0" alt="" />
                                </td>
                              </tr>
                            </table>
                          </td>
                          <td style="vertical-align:middle;">
                            <span style="color:#333333;font-size:13px;font-weight:700;font-family:Roboto, Arial, sans-serif;line-height:25px;text-decoration:none;"> Follow us </span>
                          </td>
                        </tr>
                      </table>
                      <!--[if mso | IE]></td><td><![endif]-->
                      <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="float:none;display:inline-table;">
                        <tr>
                          <td style="padding:1px;vertical-align:middle;">
                            <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="background:transparent;border-radius:3px;width:25px;">
                              <tr>
                                <td style="font-size:0;height:25px;vertical-align:middle;width:25px;">
                                  <a href="https://www.facebook.com/wpmudev" target="_blank">
                                    <span class="smush-light-img">
                                      <img height="25" src="https://mcusercontent.com/53a1e972a043d1264ed082a5b/images/2ebf5329-b6fa-d7ab-330a-376099e5186a.png" style="border-radius:3px;display:block;" width="25" alt="" />
                                    </span>
                                    <span class="smush-dark-img" style="display:inline-block;display:none;width:0;height:0;visibility:hidden;margin:0;padding:0">
                                      <img src="<?php echo esc_url( WP_SMUSH_URL . 'app/assets/images/email/facebook-dark-mode.png' ); ?>" style="border-radius:3px;display:block;" width="25" height="25" alt="" />
                                    </span>
                                  </a>
                                </td>
                              </tr>
                            </table>
                          </td>
                        </tr>
                      </table>
                      <!--[if mso | IE]></td><td><![endif]-->
                      <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="float:none;display:inline-table;">
                        <tr>
                          <td style="padding:1px;vertical-align:middle;">
                            <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="background:transparent;border-radius:3px;width:25px;">
                              <tr>
                                <td style="font-size:0;height:25px;vertical-align:middle;width:25px;">
                                  <a href="https://www.instagram.com/wpmu_dev/" target="_blank">
                                    <span class="smush-light-img">
                                      <img height="25" src="https://mcusercontent.com/53a1e972a043d1264ed082a5b/images/2fdea1f1-c823-e43d-68a2-279fc2f254a0.png" style="border-radius:3px;display:block;" width="25" alt="" />
                                    </span>
                                    <span class="smush-dark-img" style="display:inline-block;display:none;width:0;height:0;visibility:hidden;margin:0;padding:0">
                                      <img src="<?php echo esc_url( WP_SMUSH_URL . 'app/assets/images/email/instagram-dark-mode.png' ); ?>" style="border-radius:3px;display:block;" width="25" height="25" alt="" />
                                    </span>
                                  </a>
                                </td>
                              </tr>
                            </table>
                          </td>
                        </tr>
                      </table>
                      <!--[if mso | IE]></td><td><![endif]-->
                      <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="float:none;display:inline-table;">
                        <tr>
                          <td style="padding:1px;vertical-align:middle;">
                            <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="background:transparent;border-radius:3px;width:25px;">
                              <tr>
                                <td style="font-size:0;height:25px;vertical-align:middle;width:25px;">
                                  <a href="https://twitter.com/wpmudev" target="_blank">
                                    <span class="smush-light-img">
                                      <img height="25" src="https://mcusercontent.com/53a1e972a043d1264ed082a5b/images/025ac077-8a43-38ac-89d8-c331106e1c35.png" style="border-radius:3px;display:block;" width="25" alt="" />
                                    </span>
                                    <span class="smush-dark-img" style="display:inline-block;display:none;width:0;height:0;visibility:hidden;margin:0;padding:0">
                                      <img src="<?php echo esc_url( WP_SMUSH_URL . 'app/assets/images/email/twitter-dark-mode.png' ); ?>" style="border-radius:3px;display:block;" width="25" height="25" alt="" />
                                    </span>
                                  </a>
                                </td>
                              </tr>
                            </table>
                          </td>
                        </tr>
                      </table>
                      <!--[if mso | IE]></td></tr></table><![endif]-->
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <!--[if mso | IE]></td></tr></table><![endif]-->
          </td>
        </tr>
      </tbody>
    </table>
  </div>
  <!--[if mso | IE]></td></tr></table><table align="center" border="0" cellpadding="0" cellspacing="0" class="" style="width:600px;" width="600" ><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]-->
  <div class="wpmudev-footer" style="margin:0px auto;max-width:600px;">
    <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="width:100%;">
      <tbody>
        <tr>
          <td style="direction:ltr;font-size:0px;padding:0;text-align:center;">
            <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:top;width:600px;" ><![endif]-->
            <div class="mj-column-per-100 mj-outlook-group-fix" style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
              <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="vertical-align:top;" width="100%">
                <tbody>
                  <tr>
                    <td align="center" style="font-size:0px;padding:0 0 15px;word-break:break-word;">
                      <div style="font-family:Roboto, Arial, sans-serif;font-size:9px;letter-spacing:-.25px;line-height:30px;text-align:center;color:#505050;">INCSUB PO BOX 163, ALBERT PARK, VICTORIA.3206 AUSTRALIA</div>
                    </td>
                  </tr>
                  <tr>
                    <td align="center" style="font-size:0px;padding:0 0 25px;word-break:break-word;">
                      <div style="font-family:Roboto, Arial, sans-serif;font-size:10px;letter-spacing:-.25px;line-height:30px;text-align:center;color:#1A1A1A;"></div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <!--[if mso | IE]></td></tr></table><![endif]-->
          </td>
        </tr>
      </tbody>
    </table>
  </div>
  <!--[if mso | IE]></td></tr></table><![endif]-->
<?php require 'footer.php'; ?>
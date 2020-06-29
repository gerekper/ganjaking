<?php

namespace MailOptin\Core\EmailCampaigns\Newsletter\Templates;

use MailOptin\Core\EmailCampaigns\Newsletter\AbstractTemplate;

class Lucid extends AbstractTemplate
{
    public $template_name = 'Lucid';

    public function __construct($email_campaign_id)
    {
        // -------------- Template header logo width and height dimension --------------------------------- //
        add_filter('mailoptin_template_customizer_header_logo_args', function ($args) {
            $args['width']  = 308;
            $args['height'] = 48;

            return $args;
        });

        add_filter('mo_email_content_elements_text_element', [$this, 'remove_block_background_color']);
        add_filter('mo_email_content_elements_button_element', [$this, 'remove_block_background_color']);
        add_filter('mo_email_content_elements_divider_element', [$this, 'remove_block_background_color']);
        add_filter('mo_email_content_elements_image_element', [$this, 'remove_block_background_color']);
        add_filter('mo_email_content_elements_spacer_element', [$this, 'remove_block_background_color']);
        add_filter('mo_email_content_elements_posts_element', [$this, 'remove_block_background_color']);

        parent::__construct($email_campaign_id);
    }

    public function remove_block_background_color($settings)
    {
        unset($settings['block_background_color']);

        return $settings;
    }

    /**
     * Default template values.
     *
     * @return array
     */
    public function default_customizer_values()
    {
        return [
            'page_background_color'                    => '#f2f4f6',
            'header_text_color'                        => '#bbbfc3',
            'header_web_version_link_color'            => '#74787e',
            'content_background_color'                 => '#ffffff',
            'content_text_color'                       => '#74787e',
            'content_ellipsis_button_background_color' => '#dc4d2f',
            'content_ellipsis_button_text_color'       => '#ffffff',
            'footer_text_color'                        => '#aeaeae',
            'footer_unsubscribe_link_color'            => '#74787e',
        ];
    }

    /**
     * @param mixed $settings
     *
     * @return mixed
     */
    public function customizer_page_settings($settings)
    {
        return $settings;
    }

    /**
     * @param array $controls
     * @param \WP_Customize_Manager $wp_customize
     * @param string $option_prefix
     * @param \MailOptin\Core\Admin\Customizer\EmailCampaign\Customizer $customizerClassInstance
     *
     * @return array
     */
    public function customizer_page_controls($controls, $wp_customize, $option_prefix, $customizerClassInstance)
    {
        return $controls;
    }

    /**
     * {@inheritdoc}
     *
     * @param mixed $header_settings
     *
     * @return mixed
     */
    public function customizer_header_settings($header_settings)
    {
        unset($header_settings['header_background_color']);

        return $header_settings;
    }

    /**
     * {@inheritdoc}
     *
     * @param array $header_controls
     * @param \WP_Customize_Manager $wp_customize
     * @param string $option_prefix
     * @param \MailOptin\Core\Admin\Customizer\EmailCampaign\Customizer $customizerClassInstance
     *
     * @return array
     */
    public function customizer_header_controls($header_controls, $wp_customize, $option_prefix, $customizerClassInstance)
    {
        unset($header_controls['header_background_color']);

        return $header_controls;
    }

    /**
     * {@inheritdoc}
     *
     * @param mixed $content_settings
     *
     * @return mixed
     */
    public function customizer_content_settings($content_settings)
    {
        return $content_settings;
    }

    /**
     * {@inheritdoc}
     *
     * @param array $content_controls
     * @param \WP_Customize_Manager $wp_customize
     * @param string $option_prefix MailOptin\Core\Admin\Customizer\EmailCampaign
     * @param \MailOptin\Core\Admin\Customizer\EmailCampaign\Customizer $customizerClassInstance
     *
     * @return array
     */
    public function customizer_content_controls($content_controls, $wp_customize, $option_prefix, $customizerClassInstance)
    {
        return $content_controls;
    }

    /**
     * {@inheritdoc}
     *
     * @param mixed $footer_settings
     *
     * @return mixed
     */
    public function customizer_footer_settings($footer_settings)
    {
        unset($footer_settings['footer_background_color']);

        return $footer_settings;
    }


    /**
     * {@inheritdoc}
     *
     * @param array $footer_controls
     * @param \WP_Customize_Manager $wp_customize
     * @param string $option_prefix
     * @param \MailOptin\Core\Admin\Customizer\EmailCampaign\Customizer $customizerClassInstance
     *
     * @return array
     */
    public function customizer_footer_controls($footer_controls, $wp_customize, $option_prefix, $customizerClassInstance)
    {
        unset($footer_controls['footer_background_color']);

        return $footer_controls;
    }

    public function get_script()
    {
    }

    /**
     * Template body.
     *
     * @return string
     */
    public function get_body()
    {
        $view_web_version = apply_filters('mo_email_template_view_web_version', '<a class="webversion-label mo-header-web-version-label mo-header-web-version-color" href="{{webversion}}">[mo_header_web_version_link_label]</a>');
        $unsubscribe_link = apply_filters('mo_email_template_unsubscribe_link', '<a class="unsubscribe mo-footer-unsubscribe-link-label mo-footer-unsubscribe-link-color" href="{{unsubscribe}}">[mo_footer_unsubscribe_link_label]</a>');

        $body = <<<HTML
  <table class="email-wrapper mo-page-bg-color" width="100%" cellpadding="0" cellspacing="0">
    <tr>
      <td align="center">
        <table class="email-content" width="100%" cellpadding="0" cellspacing="0">
          <!-- Logo -->
          <tr class="mo-header-container">
            <td class="email-masthead">
            $view_web_version
            <br><br>
              <div class="email-masthead_name mo-header-text mo-header-text-color">[mo_header_logo_text]</div>
            </td>
          </tr>
          <!-- Email Body -->
          <tr class="mo-body-container">
            <td class="email-body mo-content-background-color" width="100%">
              <table class="email-body_inner content-cell" align="center" width="570" cellpadding="0" cellspacing="0">
                <!-- Body content -->
                {{newsletter.content}}
              </table>
            </td>
          </tr>
          <tr class="mo-footer-container">
            <td>
              <table class="email-footer mo-footer-text-color mo-footer-font-size" align="center" width="570" cellpadding="0" cellspacing="0">
                <tr>
                  <td class="content-cell">
                    <p class="sub center mo-footer-copyright-line">[mo_footer_copyright_line]</p>
                    <p class="sub center mo-footer-description">[mo_footer_description]</p>
                    <p class="sub center"><span class="unsubscribe-line mo-footer-unsubscribe-line">[mo_footer_unsubscribe_line]</span>  $unsubscribe_link.</p>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
HTML;

        return apply_filters('mo_ped_lucid_email_template_body', $body, $this);
    }


    /**
     * Template CSS styling.
     *
     * @return string
     */
    public function get_styles()
    {
        return <<<CSS
    /* Base ------------------------------ */
    body {
      font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;
      -webkit-box-sizing: border-box;
      box-sizing: border-box;
      width: 100%;
      height: 100%;
      margin: 0;
      line-height: 1.4;
      color: #74787E;
      padding: 0;
      -webkit-text-size-adjust: 100%;
      -ms-text-size-adjust: 100%;
    }
    
    body * {
        -moz-box-sizing:    border-box;
        -webkit-box-sizing: border-box;
        box-sizing:         border-box;
    }
    
    a {
      color: #3869D4;
      text-decoration: underline;
    }

    /* Layout ------------------------------ */
    .email-wrapper {
      width: 100%;
      margin: 0;
      padding: 0;
    }
    .email-content {
      width: 100%;
      margin: 0;
      padding: 0;
    }

    /* Masthead ----------------------- */
    .email-masthead {
      padding: 25px 0;
      text-align: center;
    }
    .email-masthead a {
     font-size: 10px;
    }

    .email-masthead_logo {
      max-width: 400px;
      border: 0;
    }
    .email-masthead_name {
      font-size: 25px;
      font-weight: bold;
      text-decoration: none;
    }

    /* Body ------------------------------ */
    .email-body {
      width: 100%;
      margin: 0;
      padding: 0;
    }
    
    .email-body a {
      text-decoration: none;
    }

    .email-body img {
      max-width:500px;
      height: auto;
      padding-bottom: 10px;
    }

    .email-body_inner {
      width: 570px;
      margin: 0 auto;
      padding: 0;
    }

    .email-footer {
      width: 570px;
      margin: 0 auto;
      padding: 0;
      text-align: center;
    }

    .body-action {
      width: 100%;
      margin: 30px auto 50px;
      padding: 0;
    }

    .body-sub {
      margin-top: 25px;
      padding-top: 25px;
      border-top: 1px solid #EDEFF2;
    }

    .content-cell {
      padding: 35px;
    }

    .align-right {
      text-align: right;
    }

    /* Type ------------------------------ */

    h1 a {
      color: #2F3133;
      text-decoration: none;
    }

    h1 {
      margin-top: 0;
      color: #2F3133;
      font-weight: bold;
      /*text-align: left;*/
    }
    h2 {
      margin-top: 0;
      /*color: #2F3133;*/
      font-weight: bold;
      /*text-align: left;*/
    }
    h3 {
      margin-top: 0;
      /*color: #2F3133;*/
      font-weight: bold;
      /*text-align: left;*/
    }
    p {
      margin-top: 0;
      line-height: 1.5em;
    }
    
    p.center {
      text-align: center;
    }

    /* Buttons ------------------------------ */
    .button {
      display: inline-block;
      width: 200px;
      border-radius: 3px;
      font-size: 15px;
      line-height: 45px;
      text-align: center;
      text-decoration: none;
      background-color: #dc4d2f;
      -webkit-text-size-adjust: none;
      mso-hide: all;
    }

    /*Media Queries ------------------------------ */
    @media only screen and (max-width: 600px) {
      .email-body_inner,
      .email-footer {
        width: 100% !important;
      }
    }
    @media only screen and (max-width: 500px) {
      .button {
        width: 100% !important;
      }
    }
    
    pre {
        overflow: auto;
        border: 1px dashed #888;
        padding: 5px 10px;
        margin: 0;
        text-align: left;
        width: 500px;                          /* specify width  */
        white-space: pre-wrap;                 /* CSS3 browsers  */
        white-space: -moz-pre-wrap !important; /* 1999+ Mozilla  */
        white-space: -pre-wrap;                /* Opera 4 thru 6 */
        white-space: -o-pre-wrap;              /* Opera 7 and up */
        word-wrap: break-word;                 /* IE 5.5+ and up */
        }
        /* from mjml */
        #outlook a {
              padding: 0;
        }
        
         td {
            border-collapse: collapse;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
         }
        
         img {
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
            -ms-interpolation-mode: bicubic;
         }
CSS;

    }

    public function email_content_builder_element_defaults($defaults)
    {
        $defaults['text']['text_font_family'] = 'Arial';
        $defaults['text']['text_font_size']   = '16';

        $defaults['button']['button_font_family']      = 'Arial';
        $defaults['button']['button_width']            = '100';
        $defaults['button']['button_background_color'] = '#dc4d2f';
        $defaults['button']['button_color']            = '#ffffff';
        $defaults['button']['button_font_size']        = '16';
        $defaults['button']['button_border_radius']    = '3';
        $defaults['button']['block_padding']           = [
            'top'    => '10',
            'bottom' => '10',
            'right'  => '0',
            'left'   => '0'
        ];
        $defaults['button']['button_padding']          = [
            'top'    => '15',
            'bottom' => '15',
            'right'  => '10',
            'left'   => '10'
        ];

        $defaults['divider']['block_padding'] = [
            'top'    => '10',
            'bottom' => '10',
            'right'  => '0',
            'left'   => '0'
        ];

        $defaults['image']['block_padding'] = [
            'top'    => '10',
            'bottom' => '10',
            'right'  => '0',
            'left'   => '0'
        ];

        $defaults['posts']['block_padding'] = [
            'top'    => '10',
            'bottom' => '10',
            'right'  => '0',
            'left'   => '0'
        ];

        $defaults['posts']['post_title_color'] = '#2F3133';
        $defaults['posts']['read_more_color']  = '#3869D4';
        $defaults['posts']['post_font_family'] = 'Arial';
        $defaults['posts']['block_padding']    = [
            'top'    => '10',
            'bottom' => '10',
            'right'  => '0',
            'left'   => '0'
        ];

        return $defaults;
    }

    /**
     * @param \WP_Post $post
     */
    public function posts_block_tmpl($id, $post, $settings)
    {
        $block_padding         = $settings['block_padding'];
        $read_more_link_text   = $settings['read_more_text'];
        $post_title_color      = $settings['post_title_color'];
        $read_more_color       = $settings['read_more_color'];
        $post_font_family      = $settings['post_font_family'];
        $post_metas            = $settings['post_metas'];
        $remove_feature_image  = $settings['remove_feature_image'];
        $remove_post_content   = $settings['remove_post_content'];
        $remove_read_more_link = $settings['remove_read_more_link'];
        $post_content_length   = $settings['post_content_length'];

        ob_start();
        ?>
        <tr>
            <td align="left" style="font-size:0px;padding-top:<?= $block_padding['top'] ?>px;padding-right:<?= $block_padding['right'] ?>px;padding-left:<?= $block_padding['left'] ?>px;padding-bottom:0;word-break:break-word;">
                <div class="mo-email-builder-element" data-id="<?= $id ?>" style="font-family:<?= $post_font_family ?>;font-size:13px;line-height:1;text-align:left;color:#F45E43;">
                    <a href="<?= $this->post_url($post) ?>">
                        <h1 style="color:<?= $post_title_color ?>"><?= $this->post_title($post) ?></h1></a>
                </div>
            </td>
        </tr>

        <?php if ( ! empty($post_metas)) : ?>
        <tr>
            <td align="left" style="font-size:0px;padding-bottom:<?= $block_padding['bottom'] ?>px;padding-right:<?= $block_padding['right'] ?>px;padding-left:<?= $block_padding['left'] ?>px;padding-top:0;word-break:break-word;">
                <div class="mo-content-text-color mo-email-builder-element" data-id="<?= $id ?>" style="font-family:<?= $post_font_family ?>;font-size:12px;font-weight:400;line-height:22px;text-align:left;/*color:#6f6f6f;*/">
                    <?= $this->post_meta($post, $post_metas) ?>
                </div>
            </td>
        </tr>
    <?php endif; ?>

        <?php if ($remove_feature_image !== true) : ?>
        <tr>
            <td align="center" style="font-size:0px;padding:10px 0px;word-break:break-word;">
                <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="border-collapse:collapse;border-spacing:0px;">
                    <tbody>
                    <tr>
                        <td style="width:550px;">
                            <img class="mo-email-builder-element" data-id="<?= $id ?>" height="auto" src="<?= $this->feature_image($post, $this->email_campaign_id, @$settings['default_image_url']) ?>" style="border:0;display:block;outline:none;text-decoration:none;height:auto;width:100%;font-size:13px;" width="550"/>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    <?php endif; ?>

        <?php if ($remove_post_content !== true) : ?>
        <tr>
            <td align="left" style="font-size:0px;padding:10px 0px;word-break:break-word;">
                <div class="mo-content-text-color mo-email-builder-element" data-id="<?= $id ?>" style="font-family:<?= $post_font_family ?>;font-size:14px;line-height:24px;text-align:left;/*color:#6f6f6f;*/">
                    <?= $this->post_content($post, $post_content_length) ?>
                </div>
            </td>
        </tr>
        <?php if ($remove_read_more_link !== true) : ?>
            <tr>
                <td align="left" style="font-size:0px;padding:10px 0px;word-break:break-word;">
                    <div class="mo-content-text-color mo-email-builder-element" data-id="<?= $id ?>" style="font-family:<?= $post_font_family ?>;font-size:14px;line-height:1;text-align:left;text-decoration:underline;/*color:#007bff;*/">
                        <a style="color:<?= $read_more_color ?>" href="<?= $this->post_url($post) ?>"><?= $read_more_link_text ?></a>
                    </div>
                </td>
            </tr>
        <?php endif; ?>
    <?php endif; ?>

        <tr>
            <td style="background:transparent;font-size:0px;word-break:break-word;">
                <!--[if mso | IE]>

                <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td height=25px" style="vertical-align:top;height:25px;">

                <![endif]-->
                <div style="height:25px;"> &nbsp;</div>
                <!--[if mso | IE]>

                </td></tr></table>

                <![endif]-->
            </td>
        </tr>
        <?php

        return ob_get_clean();
    }

    public function posts_block($id, $settings)
    {
        $post_list = $settings['post_list'];

        $html = '';

        if (is_array($post_list) && ! empty($post_list)) {
            foreach ($post_list as $post) {
                $html .= $this->posts_block_tmpl($id, $post, $settings);
            }
        }

        return $html;
    }

    public function text_block($id, $settings)
    {
        $text          = wpautop(stripslashes($settings['text_content']));
        $font_family   = $this->get_font_family_stack($settings['text_font_family']);
        $font_size     = $settings['text_font_size'] . 'px';
        $block_padding = $settings['block_padding'];
        $padding       = $block_padding['top'] . 'px ' . $block_padding['right'] . 'px ' . $block_padding['bottom'] . 'px ' . $block_padding['left'] . 'px';

        return <<<HTML
<tr>
    <td align="left" style="/*background:transparent;*/font-size:0px;padding:$padding;word-break:break-word;">
        <div class="mo-email-builder-element mo-content-text-color" id="$id" style="font-family:$font_family;font-size:$font_size;line-height:1;text-align:left;/*color:#74787e;*/">$text</div>
    </td>
</tr>
HTML;
    }

    public function button_block($id, $settings)
    {
        $block_padding = $settings['block_padding'];
        $block_padding = $block_padding['top'] . 'px ' . $block_padding['right'] . 'px ' . $block_padding['bottom'] . 'px ' . $block_padding['left'] . 'px';

        $button_text             = $settings['button_text'];
        $button_link             = esc_url_raw($settings['button_link']);
        $button_width            = $settings['button_width'];
        $button_background_color = $settings['button_background_color'];
        $button_color            = $settings['button_color'];
        $button_font_size        = $settings['button_font_size'];
        $button_alignment        = $settings['button_alignment'];

        $button_padding = $settings['button_padding'];
        $button_padding = $button_padding['top'] . 'px ' . $button_padding['right'] . 'px ' . $button_padding['bottom'] . 'px ' . $button_padding['left'] . 'px';

        $button_border_radius = $settings['button_border_radius'];
        $button_font_family   = $settings['button_font_family'];
        $button_font_weight   = $settings['button_font_weight'];

        return <<<HTML
<tr>
    <td align="$button_alignment" vertical-align="middle" style="font-size:0px;padding:$block_padding;word-break:break-word;">
        <table class="mo-email-builder-element" id="$id" border="0" cellpadding="0" cellspacing="0" role="presentation" style="border-collapse:separate;width:$button_width%;line-height:100%;">
            <tr>
                <td align="center" bgcolor="$button_background_color" role="presentation" style="border:none;border-radius:15px;cursor:auto;mso-padding-alt:$button_padding;background:$button_background_color;" valign="middle">
                    <a href="$button_link" style="display:inline-block;width:100%;background:$button_background_color;color:$button_color;font-family:$button_font_family;font-size:{$button_font_size}px;font-weight:$button_font_weight;line-height:120%;margin:0;text-decoration:none;text-transform:none;padding:$button_padding;mso-padding-alt:0px;border-radius:{$button_border_radius}px;" target="_blank">$button_text</a>
                </td>
            </tr>
        </table>
    </td>
</tr>
HTML;
    }

    public function divider_block($id, $settings)
    {
        $block_padding = $settings['block_padding'];
        $block_padding = $block_padding['top'] . 'px ' . $block_padding['right'] . 'px ' . $block_padding['bottom'] . 'px ' . $block_padding['left'] . 'px';

        $divider_width  = $settings['divider_width'];
        $divider_style  = $settings['divider_style'];
        $divider_color  = $settings['divider_color'];
        $divider_height = $settings['divider_height'];

        return <<<HTML
<tr>
    <td class="mo-email-builder-element" id="$id" style="font-size:0px;padding:$block_padding;word-break:break-word;">
        <p style="border-top:$divider_style {$divider_height}px $divider_color;font-size:0;margin:0px auto;width:$divider_width%;"></p>
        <!--[if mso | IE]>
        <table
                align="center" border="0" cellpadding="0" cellspacing="0" style="border-top:$divider_style {$divider_height}px $divider_color;font-size:1;margin:0px auto;width:450px;" role="presentation" width="450px"
        >
            <tr>
                <td style="height:0;line-height:0;">
                    &nbsp;
                </td>
            </tr>
        </table>
        <![endif]-->
    </td>
</tr>
HTML;
    }

    public function spacer_block($id, $settings)
    {
        $block_padding = $settings['block_padding'];
        $block_padding = $block_padding['top'] . 'px ' . $block_padding['right'] . 'px ' . $block_padding['bottom'] . 'px ' . $block_padding['left'] . 'px';

        $spacer_height = $settings['spacer_height'];

        return <<<HTML
<tr>
    <td class="mo-email-builder-element" id="$id" style="background:transparent;font-size:0px;padding:$block_padding;word-break:break-word;">
        <!--[if mso | IE]>

        <table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td height="{$spacer_height}px" style="vertical-align:top;height:{$spacer_height}px;">

        <![endif]-->
        <div style="height:{$spacer_height}px;"> &nbsp; </div>
        <!--[if mso | IE]>

        </td></tr></table>

        <![endif]-->
    </td>
</tr>
HTML;
    }

    public function image_block($id, $settings)
    {
        $block_padding = $settings['block_padding'];
        $block_padding = $block_padding['top'] . 'px ' . $block_padding['right'] . 'px ' . $block_padding['bottom'] . 'px ' . $block_padding['left'] . 'px';

        $image_url       = $settings['image_url'];
        $image_width     = $settings['image_width'];
        $image_alignment = $settings['image_alignment'];
        $image_alt_text  = $settings['image_alt_text'];
        $image_link      = $settings['image_link'];

        ob_start();
        ?>
        <tr>
            <td align="<?= $image_alignment ?>" style="font-size:0px;padding:<?= $block_padding ?>;word-break:break-word;">
                <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="border-collapse:collapse;border-spacing:0px;">
                    <tbody>
                    <tr>
                        <td style="width:<?= $image_width ?>px;">
                            <?php if ( ! empty($image_link)) : ?>
                            <a href="<?= $image_link ?>" target="_blank">
                                <?php endif; ?>
                                <img class="mo-email-builder-element" id="<?= $id ?>" alt="<?= $image_alt_text ?>" height="auto" src="<?= $image_url ?>" style="border:0;display:block;outline:none;text-decoration:none;height:auto;width:100%;font-size:13px;" width="<?= $image_width ?>"/>
                                <?php if ( ! empty($image_link)) : ?>
                            </a>
                        <?php endif; ?>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <?php
        return ob_get_clean();
    }
}
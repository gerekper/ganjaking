<?php

namespace MailOptin\Core\EmailCampaigns\PostsEmailDigest\Templates;


use MailOptin\Core\EmailCampaigns\PostsEmailDigest\AbstractTemplate;
use MailOptin\Core\Repositories\EmailCampaignRepository;

class Lucid extends AbstractTemplate
{
    public $template_name = 'Lucid';

    public function __construct($email_campaign_id, $posts)
    {
        // -------------- Template header logo width and height dimension --------------------------------- //
        add_filter('mailoptin_template_customizer_header_logo_args', function ($args) {
            $args['width']  = 308;
            $args['height'] = 48;

            return $args;
        });

        add_filter('mailoptin_customizer_email_campaign_post_content_length', function () {
            return 60;
        });

        parent::__construct($email_campaign_id, $posts);
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
            'content_headline_color'                   => '#2F3133',
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

    public function single_post_item()
    {
        ob_start();
        ?>
        <a href="{{post.url}}">
            <h1 class="mo-content-title-font-size mo-content-headline-color">{{post.title}}</h1>
            {{post.meta}}
            <img class"mo-imgix" src="{{post.feature.image}}">
        </a>
        {{post.content}}
        <!-- Action -->
        <table class="body-action mo-content-remove-ellipsis-button" width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td>
                    <div class="mo-content-button-alignment">
                        <a href="{{post.url}}" class="button button--red mo-content-button-background-color mo-content-button-text-color mo-content-read-more-label">[mo_content_ellipsis_button_label]</a>
                    </div>
                </td>
            </tr>
        </table>
        <?php

        return ob_get_clean();
    }

    public function delimiter()
    {
        ob_start();
        ?>
        <p style="border-top:solid 1px lightgrey;margin:10px auto 50px;width:100%;"></p>

        <!--[if mso | IE]>
        <table align="center" border="0" cellpadding="0" cellspacing="0" style="border-top:solid 1px lightgrey;margin:10px auto 50px;width:550px;" role="presentation" width="550px">
            <tr>
                <td style="height:0;line-height:0;">&nbsp;</td>
            </tr>
        </table>
        <![endif]-->
        <?php

        return ob_get_clean();
    }

    /**
     * Template body.
     *
     * @return string
     */
    public function get_body()
    {
        $view_web_version    = apply_filters('mo_email_template_view_web_version', '<a class="webversion-label mo-header-web-version-label mo-header-web-version-color" href="{{webversion}}">[mo_header_web_version_link_label]</a>');
        $unsubscribe_link    = apply_filters('mo_email_template_unsubscribe_link', '<a class="unsubscribe mo-footer-unsubscribe-link-label mo-footer-unsubscribe-link-color" href="{{unsubscribe}}">[mo_footer_unsubscribe_link_label]</a>');
        $before_main_content = EmailCampaignRepository::get_merged_customizer_value($this->email_campaign_id, 'content_before_main_content');
        $after_main_content = EmailCampaignRepository::get_merged_customizer_value($this->email_campaign_id, 'content_after_main_content');
        $content             = $this->parsed_post_list();

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
          <tr>
            <td class="email-body mo-content-background-color" width="100%">
              <table class="email-body_inner mo-content-body-font-size mo-content-alignment" align="center" width="570" cellpadding="0" cellspacing="0">
                <!-- Body content -->
                <tr>
                  <td class="content-cell mo-content-text-color" style="width: 570px;max-width: 570px;">
                  <div class="mo-before-main-content">$before_main_content</div>
                    $content
                  <div class="mo-after-main-content">$after_main_content</div>
                  </td>
                </tr>
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
      -webkit-text-size-adjust: none;
    }
    a {
      color: #3869D4;
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
    
    .mo-post-meta {
      color: #6f6f6f;
      font-size: 12px;
      font-weight: 400;
      line-height: 22px;
      padding: 0 0 5px 0;
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
CSS;

    }
}
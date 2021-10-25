<?php

namespace MailOptin\Core\Admin\Customizer\EmailCampaign;

use MailOptin\Core\Admin\Customizer\CustomControls\EmailContentBuilder;
use MailOptin\Core\Repositories\EmailCampaignRepository;
use function MailOptin\Core\mo_test_admin_email;

/**
 * Main aim is to serve as a unify store for all template customizer default settings.
 */
class AbstractCustomizer
{
    /** @var array store arrays of template customizer default values. */
    public $customizer_defaults;

    public $email_campaign_id;

    /**
     * AbstractCustomizer constructor.
     *
     * @param null|int $email_campaign_id
     */
    public function __construct($email_campaign_id)
    {
        $this->email_campaign_id = $email_campaign_id;

        $this->email_campaign_type  = EmailCampaignRepository::get_email_campaign_type($email_campaign_id);
        $this->email_campaign_class = EmailCampaignRepository::get_template_class($email_campaign_id);

        $this->customizer_defaults = $this->register_customizer_defaults();
    }

    public function campaign_title_default()
    {
        $val = sprintf('[New post] %s', '{{title}}');
        if ($this->email_campaign_type == EmailCampaignRepository::POSTS_EMAIL_DIGEST) {
            $val = __('Round up of Latest Posts', 'mailoptin');
        }

        return $val;
    }

    public function npp_code_your_own()
    {
        $output = <<<HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>[post-title]</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <style type="text/css">
    body {
        background-color: #fff;
    }
  .message-content {
    max-width: 600px;
    margin: 30px auto;
    font-family: Georgia, Times, serif;
  }
  .message-content div { padding-bottom: 10px; }
  .message-content img { max-width: 100%; height: auto; }

  * { font-family: Georgia, Times, serif; }

  h1, h2, h3, h4, h5, h6 {
    color: #333;
  }
  h1 { font-size: 32px; }
  h2 { font-size: 24px; }
  h3 { font-size: 18px; }

  p, p *, li, li * {
    font-size: 18px;
    line-height: 1.5em;
  }

  p, ul {
    margin-bottom: 1em;
    color: #333333;
    font-family: Georgia, Times, serif;
  }

  blockquote {
    border-left: solid 5px #aaa;
    margin: 20px 0px;
    padding: 15px 30px;
    font-size: 20px;
    line-height: 1.5em;
    font-style: italic;
    color: #444;
    font-family: Georgia, Times, serif;
  }

  a {
    text-decoration: none;
    border-bottom: 1px dotted #0875c1;
    color: #0875c1;
  }

  a:hover {
    color: #1b8ede;
    border-bottom-color: #1b8ede;
  }

  .button {
    border: none;
    background: #777;
    color: #fff;
    padding: 10px;
    display: inline-block;
    margin: 10px 0px;
    font-family: Helvetica, Arial, sans-serif;
    -webkit-border-radius: 3px;
    -moz-border-radius: 3px;
    border-radius: 3px;
  }

  .button:hover {
    color: #fff;
    background: #666;
  }

  .footer {
    border-top: 1px dotted #888;
    padding: 20px 0px;
    font-family: Helvetica, Arial, sans-serif;
    color: #aaa;
    font-size: 12px;
  }
</style>
</head>

<body>
<!--[if mso]>
<center>
    <table><tr><td width="580">
<![endif]-->
<div class='message-content'>
    
    <h2>[post-title]</h2>
    
    [post-feature-image]

    [post-content]

    <div class="footer">
        <a href="[unsubscribe]">Unsubscribe</a>
    </div>
</div>
<!--[if mso]>
</td></tr></table>
</center>
<![endif]-->

</body>

</html>
HTML;

        if ($this->email_campaign_type == EmailCampaignRepository::POSTS_EMAIL_DIGEST) {
            $output = <<<HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Posts Roundup</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <style>
    body {
        background-color: #fff;
    }
  .message-content {
    max-width: 600px;
    margin: 30px auto;
    font-family: Georgia, Times, serif;
  }
  .message-content div { padding-bottom: 10px; }
  .message-content img { max-width: 100%; height: auto; }

  * { font-family: Georgia, Times, serif; }

  h1, h2, h3, h4, h5, h6 {
    color: #333;
  }
  h1 { font-size: 32px; }
  h2 { font-size: 24px; }
  h3 { font-size: 18px; }

  p, p *, li, li * {
    font-size: 18px;
    line-height: 1.5em;
  }

  p, ul {
    margin-bottom: 1em;
    color: #333333;
    font-family: Georgia, Times, serif;
  }

  blockquote {
    border-left: solid 5px #aaa;
    margin: 20px 0px;
    padding: 15px 30px;
    font-size: 20px;
    line-height: 1.5em;
    font-style: italic;
    color: #444;
    font-family: Georgia, Times, serif;
  }

  a {
    text-decoration: none;
    border-bottom: 1px dotted #0875c1;
    color: #0875c1;
  }

  a:hover {
    color: #1b8ede;
    border-bottom-color: #1b8ede;
  }

  .button {
    border: none;
    background: #777;
    color: #fff;
    padding: 10px;
    display: inline-block;
    margin: 10px 0px;
    font-family: Helvetica, Arial, sans-serif;
    -webkit-border-radius: 3px;
    -moz-border-radius: 3px;
    border-radius: 3px;
  }

  .button:hover {
    color: #fff;
    background: #666;
  }

  .footer {
    border-top: 1px dotted #888;
    padding: 20px 0px;
    font-family: Helvetica, Arial, sans-serif;
    color: #aaa;
    font-size: 12px;
  }
</style>
</head>

<body>
<!--[if mso]>
<center>
    <table><tr><td width="580">
<![endif]-->
<div class='message-content'>
    
    [posts-loop]
    
        <h2>[post-title]</h2>
        
        [post-feature-image]
        
        [post-content]
    
    [/posts-loop]

    <div class="footer">
        <a href="[unsubscribe]">Unsubscribe</a>
    </div>
</div>
<!--[if mso]>
</td></tr></table>
</center>
<![endif]-->

</body>

</html>
HTML;

        }

        if ($this->email_campaign_type == EmailCampaignRepository::NEWSLETTER) {
            $output = <<<HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Posts Roundup</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <style>
    body {
        background-color: #fff;
    }
  .message-content {
    max-width: 600px;
    margin: 30px auto;
    font-family: Georgia, Times, serif;
  }
  .message-content div { padding-bottom: 10px; }
  .message-content img { max-width: 100%; height: auto; }

  * { font-family: Georgia, Times, serif; }

  h1, h2, h3, h4, h5, h6 {
    color: #333;
  }
  h1 { font-size: 32px; }
  h2 { font-size: 24px; }
  h3 { font-size: 18px; }

  p, p *, li, li * {
    font-size: 18px;
    line-height: 1.5em;
  }

  p, ul {
    margin-bottom: 1em;
    color: #333333;
    font-family: Georgia, Times, serif;
  }

  blockquote {
    border-left: solid 5px #aaa;
    margin: 20px 0px;
    padding: 15px 30px;
    font-size: 20px;
    line-height: 1.5em;
    font-style: italic;
    color: #444;
    font-family: Georgia, Times, serif;
  }

  a {
    text-decoration: none;
    border-bottom: 1px dotted #0875c1;
    color: #0875c1;
  }

  a:hover {
    color: #1b8ede;
    border-bottom-color: #1b8ede;
  }

  .button {
    border: none;
    background: #777;
    color: #fff;
    padding: 10px;
    display: inline-block;
    margin: 10px 0px;
    font-family: Helvetica, Arial, sans-serif;
    -webkit-border-radius: 3px;
    -moz-border-radius: 3px;
    border-radius: 3px;
  }

  .button:hover {
    color: #fff;
    background: #666;
  }

  .footer {
    border-top: 1px dotted #888;
    padding: 20px 0px;
    font-family: Helvetica, Arial, sans-serif;
    color: #aaa;
    font-size: 12px;
  }
</style>
</head>

<body>
<!--[if mso]>
<center>
    <table><tr><td width="580">
<![endif]-->
<div class='message-content'>
    
    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas iaculis condimentum ipsum non placerat. Pellentesque mattis faucibus arcu, ac dapibus augue sodales ac. In libero mauris, cursus a nisl nec, euismod facilisis nunc. Ut sed magna eget lorem lobortis tempus. Pellentesque sagittis convallis sagittis. Maecenas sagittis odio tortor, ut placerat tellus consectetur vitae. Vivamus a dapibus nulla. Curabitur non diam vitae lectus facilisis commodo a vel leo. Vestibulum sollicitudin, nulla ac placerat malesuada, tellus eros ullamcorper risus, in porttitor nulla justo nec diam. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris iaculis dolor in tortor congue commodo. Sed sed faucibus eros, non tincidunt elit. Vestibulum et pretium orci, eu mollis velit. Duis iaculis placerat ornare. Suspendisse tempus ultricies lectus eget tempus. Quisque facilisis egestas erat, ac sollicitudin sem.</p>

    <p>Vestibulum sodales elit justo, sed pharetra magna congue eu. Cras ullamcorper sem quis lorem laoreet finibus. Fusce feugiat purus mollis, placerat nulla vitae, finibus enim. Nullam venenatis justo urna, ut aliquet justo tincidunt eu. Suspendisse a risus semper urna aliquet mattis. Nam mollis, dolor vel tempus pulvinar, massa turpis laoreet leo, quis ultricies arcu dolor hendrerit ante. Sed pulvinar viverra enim nec laoreet. Nulla quis turpis non leo ultricies efficitur. Cras a nibh sit amet est euismod semper. Nunc quis ex nibh.</p>

    <p>In ut nunc quis tellus suscipit porttitor. Morbi porta eros ut sem vestibulum molestie. Fusce maximus tincidunt est vel imperdiet. Quisque nec luctus metus. Sed sed erat quis augue viverra aliquet. Aliquam rutrum ut ipsum ut vestibulum. Curabitur porta est vel massa auctor sodales. Duis congue eget ex in varius. Aliquam id placerat erat. Aenean tincidunt porta tempus. Aenean molestie tincidunt nunc, quis scelerisque arcu laoreet quis. Curabitur nec erat interdum, vehicula turpis et, commodo purus. Proin nec quam enim. Nunc vitae suscipit ante. Proin ullamcorper ante at tortor suscipit, id euismod lectus semper.</p>

    <p>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Cras felis nibh, bibendum sit amet enim ac, semper dapibus erat. Fusce ornare faucibus gravida. Sed ut sem purus. In et massa egestas, malesuada quam ac, tristique erat. Aliquam malesuada turpis et eros elementum commodo sed eget justo. Vestibulum mattis ex et convallis pharetra. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Aenean eget dictum metus. Sed ut enim quis est elementum finibus. Mauris mattis rutrum varius. Phasellus vitae augue felis. Etiam est mi, dapibus eu lectus vel, gravida faucibus eros. Etiam libero augue, rhoncus non velit et, aliquam eleifend neque. Praesent vitae ex nulla. Quisque in pretium lorem.</p>

    <div class="footer">
        <a href="[unsubscribe]">Unsubscribe</a>
    </div>
</div>
<!--[if mso]>
</td></tr></table>
</center>
<![endif]-->

</body>

</html>
HTML;
        }

        return $output;
    }

    /**
     * Return array of template customizer default values.
     *
     * @return array
     */
    public function register_customizer_defaults()
    {
        $email_campaign_id = $this->email_campaign_id;

        // the JSON flag options are required to prevent having an un-parsable string.
        // thankfully, JSON.parse can parse unicodes such as u2028 https://stackoverflow.com/a/12869914/2648410
        $email_newsletter_content_default = json_encode([
            [
                'id'       => wp_generate_password(18, false),
                'type'     => 'text',
                'settings' => EmailContentBuilder\Misc::elements_default_fields_values($email_campaign_id)['text']
            ]
        ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);

        $blog_name    = \MailOptin\Core\site_title();
        $current_year = date("Y");

        $defaults                            = [];
        $defaults['activate_email_campaign'] = apply_filters('mailoptin_customizer_email_campaign_activate', '', $this->customizer_defaults, $this->email_campaign_type, $this->email_campaign_class);

        $defaults['email_campaign_title'] = apply_filters('mailoptin_email_campaign_title_default', EmailCampaignRepository::get_email_campaign_name($email_campaign_id), $this->customizer_defaults, $this->email_campaign_type, $this->email_campaign_class);

        $defaults['email_campaign_subject'] = apply_filters('mailoptin_email_campaign_subject_default', $this->campaign_title_default(), $this->customizer_defaults, $this->email_campaign_type, $this->email_campaign_class);

        $defaults['default_image_url']         = apply_filters('mailoptin_customizer_email_campaign_default_image_url', MAILOPTIN_ASSETS_URL . 'images/email-templates/default-feature-img.jpg', $this->customizer_defaults, $this->email_campaign_type, $this->email_campaign_class);
        $defaults['post_content_type']         = apply_filters('mailoptin_customizer_email_campaign_post_content_type', 'post_content', $this->customizer_defaults, $this->email_campaign_type, $this->email_campaign_class);
        $defaults['post_content_length']       = apply_filters('mailoptin_customizer_email_campaign_post_content_length', 150, $this->customizer_defaults, $this->email_campaign_type, $this->email_campaign_class);
        $defaults['custom_post_type']          = apply_filters('mailoptin_customizer_email_campaign_custom_post_type', 'post', $this->customizer_defaults, $this->email_campaign_type, $this->email_campaign_class);
        $defaults['custom_post_type_settings'] = apply_filters('mailoptin_customizer_email_campaign_custom_post_type_settings', '', $this->customizer_defaults, $this->email_campaign_type, $this->email_campaign_class);
        $defaults['item_number']               = apply_filters('mailoptin_customizer_email_campaign_item_number', 5, $this->customizer_defaults, $this->email_campaign_type, $this->email_campaign_class);
        $defaults['post_categories']           = apply_filters('mailoptin_customizer_email_campaign_post_categories', [], $this->customizer_defaults, $this->email_campaign_type, $this->email_campaign_class);
        $defaults['post_tags']                 = apply_filters('mailoptin_customizer_email_campaign_post_tags', [], $this->customizer_defaults, $this->email_campaign_type, $this->email_campaign_class);
        $defaults['post_authors']              = apply_filters('mailoptin_customizer_email_campaign_post_authors', [], $this->customizer_defaults, $this->email_campaign_type, $this->email_campaign_class);
        $defaults['connection_service']        = apply_filters('mailoptin_customizer_email_campaign_connection_service', '', $this->customizer_defaults, $this->email_campaign_type, $this->email_campaign_class);
        $defaults['connection_email_list']     = apply_filters('mailoptin_customizer_email_campaign_connection_email_list', '', $this->customizer_defaults, $this->email_campaign_type, $this->email_campaign_class);
        $defaults['send_immediately']          = apply_filters('mailoptin_customizer_email_campaign_send_immediately', false, $this->customizer_defaults, $this->email_campaign_type, $this->email_campaign_class);
        $defaults['schedule_type']             = apply_filters('mailoptin_customizer_email_campaign_schedule_type', 'hours', $this->customizer_defaults, $this->email_campaign_type, $this->email_campaign_class);
        $defaults['schedule_digit']            = apply_filters('mailoptin_customizer_email_campaign_schedule_digit', '', $this->customizer_defaults, $this->email_campaign_type, $this->email_campaign_class);

        $defaults['schedule_interval']   = apply_filters('mailoptin_customizer_email_campaign_schedule_interval', 'every_day', $this->customizer_defaults, $this->email_campaign_type, $this->email_campaign_class);
        $defaults['schedule_time']       = apply_filters('mailoptin_customizer_email_campaign_schedule_time', '00', $this->customizer_defaults, $this->email_campaign_type, $this->email_campaign_class);
        $defaults['schedule_day']        = apply_filters('mailoptin_customizer_email_campaign_schedule_day', '0', $this->customizer_defaults, $this->email_campaign_type, $this->email_campaign_class);
        $defaults['schedule_month_date'] = apply_filters('mailoptin_customizer_email_campaign_schedule_month_date', '1', $this->customizer_defaults, $this->email_campaign_type, $this->email_campaign_class);

        $defaults['code_your_own'] = apply_filters('mailoptin_code_your_own_default', $this->npp_code_your_own(), $this->customizer_defaults, $this->email_campaign_type);

        $defaults['page_background_color'] = apply_filters('mailoptin_page_background_color_default', '', $this->customizer_defaults, $this->email_campaign_type, $this->email_campaign_class);

        $defaults['header_removal']                = apply_filters('mailoptin_header_removal_default', false, $this->customizer_defaults, $this->email_campaign_type, $this->email_campaign_class);
        $defaults['header_logo']                   = apply_filters('mailoptin_header_logo_default', '', $this->customizer_defaults, $this->email_campaign_type, $this->email_campaign_class);
        $defaults['header_background_color']       = apply_filters('mailoptin_header_background_color_default', '', $this->customizer_defaults, $this->email_campaign_type, $this->email_campaign_class);
        $defaults['header_text_color']             = apply_filters('mailoptin_header_text_color_default', '', $this->customizer_defaults, $this->email_campaign_type, $this->email_campaign_class);
        $defaults['header_text']                   = apply_filters('mailoptin_header_text_default', $blog_name, $this->customizer_defaults, $this->email_campaign_type, $this->email_campaign_class);
        $defaults['header_web_version_link_label'] = apply_filters('mailoptin_header_web_version_link_label_default', __('View this email in your browser', 'mailoptin'), $this->customizer_defaults, $this->email_campaign_type, $this->email_campaign_class);
        $defaults['header_web_version_link_color'] = apply_filters('mailoptin_header_web_version_link_color_default', '', $this->customizer_defaults, $this->email_campaign_type, $this->email_campaign_class);

        $defaults['content_before_main_content']              = apply_filters('mailoptin_content_before_main_content_default', '', $this->customizer_defaults, $this->email_campaign_type, $this->email_campaign_class);
        $defaults['content_after_main_content']               = apply_filters('mailoptin_content_after_main_content_default', '', $this->customizer_defaults, $this->email_campaign_type, $this->email_campaign_class);
        $defaults['content_remove_ellipsis_button']           = apply_filters('mailoptin_content_remove_ellipsis_button_default', false, $this->customizer_defaults, $this->email_campaign_type, $this->email_campaign_class);
        $defaults['content_post_meta']                        = apply_filters('mailoptin_content_post_meta_default', [], $this->customizer_defaults, $this->email_campaign_type, $this->email_campaign_class);
        $defaults['content_remove_post_link']                 = apply_filters('mailoptin_content_remove_post_link_default', false, $this->customizer_defaults, $this->email_campaign_type, $this->email_campaign_class);
        $defaults['content_remove_post_body']                 = apply_filters('mailoptin_content_remove_post_body_default', false, $this->customizer_defaults, $this->email_campaign_type, $this->email_campaign_class);
        $defaults['content_remove_feature_image']             = apply_filters('mailoptin_content_remove_feature_image_default', false, $this->customizer_defaults, $this->email_campaign_type, $this->email_campaign_class);
        $defaults['content_background_color']                 = apply_filters('mailoptin_content_background_color_default', '', $this->customizer_defaults, $this->email_campaign_type, $this->email_campaign_class);
        $defaults['content_text_color']                       = apply_filters('mailoptin_content_text_color_default', '', $this->customizer_defaults, $this->email_campaign_type, $this->email_campaign_class);
        $defaults['content_headline_color']                   = apply_filters('mailoptin_content_headline_color_default', '', $this->customizer_defaults, $this->email_campaign_type, $this->email_campaign_class);
        $defaults['content_alignment']                        = apply_filters('mailoptin_content_alignment_default', 'center', $this->customizer_defaults, $this->email_campaign_type, $this->email_campaign_class);
        $defaults['content_ellipsis_button_alignment']        = apply_filters('mailoptin_content_ellipsis_button_alignment_default', 'center', $this->customizer_defaults, $this->email_campaign_type, $this->email_campaign_class);
        $defaults['content_ellipsis_button_background_color'] = apply_filters('mailoptin_content_ellipsis_button_background_color_default', '', $this->customizer_defaults, $this->email_campaign_type, $this->email_campaign_class);
        $defaults['content_ellipsis_button_text_color']       = apply_filters('mailoptin_content_ellipsis_button_text_color_default', '', $this->customizer_defaults, $this->email_campaign_type, $this->email_campaign_class);
        $defaults['content_title_font_size']                  = apply_filters('mailoptin_content_title_font_size_default', '19', $this->customizer_defaults, $this->email_campaign_type, $this->email_campaign_class);
        $defaults['content_body_font_size']                   = apply_filters('mailoptin_content_body_font_size_default', '16', $this->customizer_defaults, $this->email_campaign_type, $this->email_campaign_class);
        $defaults['content_ellipsis_button_label']            = apply_filters('mailoptin_content_ellipsis_button_label_default', __('Read more', 'mailoptin'), $this->customizer_defaults, $this->email_campaign_type, $this->email_campaign_class);
        $defaults['email_newsletter_content']                 = apply_filters('mailoptin_email_newsletter_content_default', $email_newsletter_content_default, $this->customizer_defaults, $this->email_campaign_type, $this->email_campaign_class);

        $defaults['footer_removal']                = apply_filters('mailoptin_footer_removal_default', false, $this->customizer_defaults, $this->email_campaign_type, $this->email_campaign_class);
        $defaults['footer_background_color']       = apply_filters('mailoptin_footer_background_color_default', '', $this->customizer_defaults, $this->email_campaign_type, $this->email_campaign_class);
        $defaults['footer_text_color']             = apply_filters('mailoptin_footer_text_color_default', '', $this->customizer_defaults, $this->email_campaign_type, $this->email_campaign_class);
        $defaults['footer_font_size']              = apply_filters('mailoptin_footer_font_size_default', '12', $this->customizer_defaults, $this->email_campaign_type, $this->email_campaign_class);
        $defaults['footer_copyright_line']         = apply_filters('mailoptin_footer_copyright_line_default', "&copy; $current_year $blog_name. All rights reserved.", $this->customizer_defaults, $this->email_campaign_type, $this->email_campaign_class);
        $defaults['footer_unsubscribe_line']       = apply_filters('mailoptin_footer_unsubscribe_line_default', __('If you do not want to receive emails from us, you can', 'mailoptin'), $this->customizer_defaults, $this->email_campaign_type, $this->email_campaign_class);
        $defaults['footer_unsubscribe_link_label'] = apply_filters('mailoptin_footer_unsubscribe_link_label_default', __('unsubscribe', 'mailoptin'), $this->customizer_defaults, $this->email_campaign_type, $this->email_campaign_class);
        $defaults['footer_unsubscribe_link_color'] = apply_filters('mailoptin_footer_unsubscribe_link_color_default', '', $this->customizer_defaults, $this->email_campaign_type, $this->email_campaign_class);
        $defaults['footer_description']            = apply_filters('mailoptin_footer_description_default', "Our mailing address is:
{{company_name}}
{{company_address}},
{{company_address_2}}
{{company_city}}, {{company_state}} {{company_zip}}.
{{company_country}}.", $this->customizer_defaults, $this->email_campaign_type, $this->email_campaign_class);

        $defaults['send_test_email_input']         = apply_filters('mailoptin_send_test_email_input_default', mo_test_admin_email(), $this->customizer_defaults, $this->email_campaign_type, $this->email_campaign_class);

        return apply_filters('mailoptin_template_customizer_defaults', $defaults, $this->customizer_defaults, $this->email_campaign_type, $this->email_campaign_class);
    }

}
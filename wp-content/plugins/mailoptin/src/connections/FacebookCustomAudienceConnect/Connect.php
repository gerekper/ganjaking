<?php

namespace MailOptin\FacebookCustomAudienceConnect;

use MailOptin\Core\Connections\ConnectionInterface;
use MailOptin\Core\PluginSettings\Connections;
use function MailOptin\Core\current_user_has_privilege;

class Connect extends AbstractFacebookCustomAudienceConnect implements ConnectionInterface
{
    public $adAccountId;

    /**
     * @var string key of connection service. its important all connection name ends with "Connect"
     */
    public static $connectionName = 'FacebookCustomAudienceConnect';

    public function __construct()
    {
        ConnectSettingsPage::get_instance();

        add_filter('mailoptin_registered_connections', array($this, 'register_connection'));

        add_action('wp_ajax_mailoptin_create_fbca', [$this, 'create_fb_custom_audience']);

        $this->adAccountId = Connections::instance()->fbca_adaccount_id();

        add_action('mailoptin_admin_notices', function () {
            add_action('admin_notices', array($this, 'admin_notices'));
        });

        parent::__construct();
    }

    public static function features_support()
    {
        return [self::OPTIN_CAMPAIGN_SUPPORT];
    }

    /**
     * Register Connection.
     *
     * @param array $connections
     *
     * @return array
     */
    public function register_connection($connections)
    {
        $connections[self::$connectionName] = __('Facebook Custom Audience', 'mailoptin');

        return $connections;
    }

    public function create_fb_custom_audience()
    {
        if ( ! current_user_has_privilege()) return;

        check_ajax_referer('mailoptin-admin-nonce', 'nonce');

        if ( ! isset($_REQUEST['fbca_name'], $_REQUEST['fbca_description'])) wp_send_json_error();

        $name        = sanitize_text_field($_REQUEST['fbca_name']);
        $description = sanitize_text_field($_REQUEST['fbca_description']);

        try {
            $response = $this->fbca_instance()->createCustomAudience($this->adAccountId, $name, $description);

            if ($response) {
                wp_send_json_success(
                    ['redirect' => esc_url_raw(add_query_arg('fbca', 'true', MAILOPTIN_CONNECTIONS_SETTINGS_PAGE))]
                );
            }

        } catch (\Exception $e) {
            wp_send_json_error($e->getMessage());
        }
    }

    public function admin_notices()
    {
        if (self::is_connected()) {

            $message = '';
            if (isset($_GET['fbca']) && $_GET['fbca'] == 'true') {
                $message = esc_html__('New Facebook custom audience successfully created', 'mailoptin');
            }

            if (get_option('mo_fbca_access_token_expired_status', 'false') == 'true') {
                $message = sprintf(
                    esc_html__('Facebook app access token for custom audience has expired. %sLearn how to generate a new one%s', 'mailoptin'),
                    '<a target="_blank" href="https://mailoptin.io/article/connect-mailoptin-facebook-custom-audience/#generateAccessToken">', '</a>'
                );
            }

            if (empty($message)) return;

            echo '<div id="message" class="updated notice is-dismissible">';
            echo '<p>';
            echo $message;
            echo '</p>';
            echo '</div>';
        }
    }

    /**
     * {@inherit_doc}
     *
     * Return array of email list
     *
     * @return mixed
     */
    public function get_email_list()
    {
        try {

            $audiences = $this->fbca_instance()->getCustomAudiences($this->adAccountId);

            if ( ! empty($audiences)) {

                // an array with list id as key and name as value.
                $audiences_array = array();

                foreach ($audiences as $audience) {
                    if ($audience->subtype != 'CUSTOM') continue;

                    if (is_object($audience) && isset($audience->id)) {
                        $audiences_array[$audience->id] = $audience->name;
                    }
                }

                return $audiences_array;
            }

            $audiences = is_object($audiences) || is_array($audiences) ? json_encode($audiences) : $audiences;

            self::save_optin_error_log($audiences, 'facebookcustomaudience');

            return [];

        } catch (\Exception $e) {
            self::save_optin_error_log($e->getMessage(), 'facebookcustomaudience');

            return [];
        }
    }

    public function get_optin_fields($list_id = '')
    {
        return [];
    }

    public function replace_placeholder_tags($content, $type = 'html')
    {
        return $this->replace_footer_placeholder_tags($content);
    }

    /**
     *
     * {@inheritdoc}
     *
     * @param int $email_campaign_id
     * @param int $campaign_log_id
     * @param string $subject
     * @param string $content_html
     * @param string $content_text
     *
     * @return array
     * @throws \Exception
     *
     */
    public function send_newsletter($email_campaign_id, $campaign_log_id, $subject, $content_html, $content_text)
    {
    }

    /**
     * @param string $email
     * @param string $name
     * @param string $list_id ID of email list to add subscriber to
     * @param mixed|null $extras
     *
     * @return mixed
     */
    public function subscribe($email, $name, $list_id, $extras = null)
    {
        return (new Subscription($email, $name, $list_id, $extras, $this))->subscribe();
    }

    /**
     * Singleton poop.
     *
     * @return Connect|null
     */
    public static function get_instance()
    {
        static $instance = null;

        if (is_null($instance)) {
            $instance = new self();
        }

        return $instance;
    }
}
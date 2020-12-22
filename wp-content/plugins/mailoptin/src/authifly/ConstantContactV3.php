<?php

namespace Authifly\Provider;

use Authifly\Adapter\OAuth2;
use Authifly\Data\Collection;
use Authifly\Data;
use Authifly\Exception\InvalidArgumentException;
use MailOptin\Core\Connections\AbstractConnect;

/**
 * ConstantContactV3 OAuth2 provider adapter.
 */
class ConstantContactV3 extends OAuth2
{
    /**
     * {@inheritdoc}
     */
    protected $apiBaseUrl = 'https://api.cc.email/v3/';

    /**
     * {@inheritdoc}
     */
    protected $authorizeUrl = 'https://api.cc.email/v3/idfed';

    /**
     * {@inheritdoc}
     */
    protected $accessTokenUrl = 'https://idfed.constantcontact.com/as/token.oauth2';

    /**
     * {@inheritdoc}
     */
    protected $apiDocumentation = 'https://developer.constantcontact.com/docs/authentication/oauth-2.0-server-flow.html';

    /**
     * {@inheritdoc}
     */
    protected $scope = 'contact_data campaign_data';

    protected $supportRequestState = false;

    /**
     * {@inheritdoc}
     */
    protected function initialize()
    {
        parent::initialize();

        $refresh_token = $this->getStoredData('refresh_token');

        if (empty($refresh_token)) {
            $refresh_token = $this->config->get('refresh_token');
        }

        $this->tokenRefreshParameters['refresh_token'] = $refresh_token;

        $this->tokenRefreshHeaders = [
            'Authorization' => sprintf('Basic %s', base64_encode($this->clientId . ':' . $this->clientSecret))
        ];

        $access_token = $this->getStoredData('access_token');

        if (empty($access_token)) $access_token = $this->config->get('access_token');

        if ( ! empty($access_token)) {
            $this->apiRequestHeaders = [
                'Authorization' => 'Bearer ' . $access_token
            ];
        }
    }

    /**
     * @param string $url
     * @param string $method
     * @param array $parameters
     * @param array $headers
     *
     * @return mixed
     * @throws \Exception
     */
    public function apiRequest($url, $method = 'GET', $parameters = [], $headers = [])
    {
        if (strrpos($url, 'http://') !== 0 && strrpos($url, 'https://') !== 0) {
            $url = $this->apiBaseUrl . $url;
        }

        $parameters = array_replace($this->apiRequestParameters, (array)$parameters);
        $headers    = array_replace($this->apiRequestHeaders, (array)$headers);

        $response = $this->httpClient->request(
            $url,
            $method,     // HTTP Request Method. Defaults to GET.
            $parameters, // Request Parameters
            $headers     // Request Headers
        );

        if (401 == $this->httpClient->getResponseHttpCode()) {

            $new_access_token = $this->mailoptin_external_token_refresh();

            $headers['Authorization'] = sprintf('Bearer %s', $new_access_token);

            $response = $this->httpClient->request(
                $url,
                $method,     // HTTP Request Method. Defaults to GET.
                $parameters, // Request Parameters
                $headers     // Request Headers
            );
        }

        $this->validateApiResponse('Signed API request has returned an error');

        $response = (new Data\Parser())->parse($response);

        return $response;
    }

    /**
     * @throws \Exception
     */
    public function mailoptin_external_token_refresh()
    {
        $refresh_token = $this->tokenRefreshParameters['refresh_token'];

        $response = AbstractConnect::static_oauth_token_refresh('constantcontactv3', $refresh_token);

        if (isset($response['success']) && $response['success'] === true) {

            if (isset($response['data']['access_token'])) {
                $this->storeData('access_token', $response['data']['access_token']);
            }

            if (isset($response['data']['refresh_token'])) {
                $this->storeData('refresh_token', $response['data']['refresh_token']);
            }

            $option_name = MAILOPTIN_CONNECTIONS_DB_OPTION_NAME;
            $old_data    = get_option($option_name, []);
            $new_data    = [
                'ctctv3_access_token'  => $response['data']['access_token'],
                'ctctv3_refresh_token' => $response['data']['refresh_token'],
            ];

            update_option($option_name, array_merge($old_data, $new_data));

            return $response['data']['access_token'];
        }
    }

    /**
     *
     * @return array
     * @throws \Exception
     */
    public function getContactList()
    {
        $response = $this->apiRequest("contact_lists", 'GET', ['limit' => 1000]);

        $lists = (new Collection($response))->filter('lists')->toArray();

        $filtered = [];

        foreach ($lists as $list) {
            $filtered[$list->list_id] = $list->name;
        }

        return $filtered;
    }

    /**
     * Return all custom fields defined in user's account.
     *
     * @return mixed
     * @throws \Exception
     */
    public function getContactsCustomFields()
    {
        $response = $this->apiRequest('contact_custom_fields');

        $data          = new Collection($response);
        $custom_fields = $data->filter('custom_fields')->toArray();

        return $custom_fields;
    }

    /**
     * Create contact and add to email/contact list.
     *
     * @param $payloads
     * @param array $headers
     *
     * @return mixed
     * @throws \Exception
     */
    public function createOrUpdateContact($payloads, $headers = [])
    {
        $headers = array_replace(['Content-Type' => 'application/json'], $headers);

        return $this->apiRequest('contacts/sign_up_form', 'POST', $payloads, $headers);
    }

    /**
     * Create (draft) email campaign.
     *
     * @param array $payload
     * @param array $headers
     *
     * @return mixed
     * @throws \Exception
     */
    public function createEmailCampaign(array $payload, $headers = [])
    {
        $required_fields = ['from_name', 'from_email', 'reply_to_email', 'subject', 'html_content'];

        $footer_required_fields = ['organization_name', 'address_line1', 'country_code'];

        if (is_array($payload['email_campaign_activities'])) {
            foreach ($required_fields as $required_field) {
                if ( ! in_array($required_field, array_keys($payload['email_campaign_activities']))) :
                    throw new InvalidArgumentException(sprintf('%s required field is missing', $required_field));
                    break;
                endif;
            }
        }

        if (is_array($payload['email_campaign_activities'][0]['physical_address_in_footer'])) {
            foreach ($footer_required_fields as $required_field) {
                if ( ! in_array($required_field, array_keys($payload['email_campaign_activities'][0]['physical_address_in_footer']))) :
                    throw new InvalidArgumentException(sprintf('%s required field is missing', $required_field));
                    break;
                endif;
            }
        }

        $headers = array_replace(['Content-Type' => 'application/json'], $headers);

        $response = $this->apiRequest('emails', 'POST', $payload, $headers);

        if (is_array($response) && isset($response[0]) && isset($response[0]->error_key)) {
            throw new InvalidArgumentException($response[0]->error_message, $this->httpClient->getResponseHttpCode());
        }

        return $response;

    }

    /**
     * Update an email campaign activities
     *
     * @param array $headers
     * @param array $payload
     * @param string $campaign_activity_id
     *
     * @return mixed
     * @throws \Exception
     *
     */
    public function updateEmailCampaign(array $payload, $campaign_activity_id, $headers = [])
    {
        $headers = array_replace(['Content-Type' => 'application/json'], $headers);

        $response = $this->apiRequest("emails/activities/$campaign_activity_id", 'PUT', $payload, $headers);

        if (is_array($response) && isset($response[0]) && isset($response[0]->error_key)) {
            throw new \Exception($response[0]->error_message, $this->httpClient->getResponseHttpCode());
        }

        return $response;
    }

    /**
     * Send email campaign immediately.
     *
     * @param string $campaign_activity_id
     * @param array $headers
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function sendEmailCampaign($campaign_activity_id, $headers = [])
    {
        $payload['scheduled_date'] = "0";

        $headers = array_replace(['Content-Type' => 'application/json'], $headers);

        $response = $this->apiRequest("emails/activities/$campaign_activity_id/schedules", 'POST', $payload, $headers);

        if (is_array($response) && isset($response[0]) && isset($response[0]->error_key)) {
            throw new \Exception($response[0]->error_message, $this->httpClient->getResponseHttpCode());
        }

        return $response;
    }
}

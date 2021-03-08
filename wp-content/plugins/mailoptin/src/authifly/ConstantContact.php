<?php

namespace Authifly\Provider;

use Authifly\Adapter\OAuth2;
use Authifly\Exception\InvalidArgumentException;
use Authifly\Exception\UnexpectedApiResponseException;

/**
 * ConstantContact OAuth2 provider adapter.
 */
class ConstantContact extends OAuth2
{
    /**
     * {@inheritdoc}
     */
    protected $apiBaseUrl = 'https://api.constantcontact.com/v2/';

    /**
     * {@inheritdoc}
     */
    protected $authorizeUrl = 'https://oauth2.constantcontact.com/oauth2/oauth/siteowner/authorize';

    /**
     * {@inheritdoc}
     */
    protected $accessTokenUrl = 'https://oauth2.constantcontact.com/oauth2/oauth/token';

    /**
     * {@inheritdoc}
     */
    protected $apiDocumentation = 'https://developer.constantcontact.com/docs/authentication/oauth-2.0-server-flow.html';

    protected $validateApiResponseHttpCode = false;

    /**
     * {@inheritdoc}
     */
    protected function initialize()
    {
        parent::initialize();

        $access_token = $this->getStoredData('access_token');

        $config_access_token = $this->config->get('access_token');

        if ( ! empty($config_access_token)) {
            $access_token = $config_access_token;
        }

        if ( ! empty($access_token)) {
            $this->apiRequestHeaders = [
                'Authorization' => 'Bearer ' . $access_token
            ];
        }
    }

    /**
     * Return API key supplied as AuthiFly config.
     *
     * @return mixed
     */
    protected function apiKey()
    {
        return $this->config->filter('keys')->get('key');
    }

    /**
     * @param array $headers you could use this to supply your own authorization header with access token.
     *
     * E.g $constantcontact->getContactList(['Authorization' => 'Bearer ' . 'a8a8f842-a420-4d72-6ye7-25323f4e4934'])
     *
     * @return object
     */
    public function getContactList($headers = [])
    {
        return $this->apiRequest('lists', 'GET', ['api_key' => $this->apiKey()], $headers);
    }

    /**
     * Retrieve details of a contact identified by email address.
     *
     * @param string $email_address
     * @param array $headers
     *
     * @return object
     */
    public function fetchContact($email_address, $headers = [])
    {
        return $this->apiRequest('contacts', 'GET', ['api_key' => $this->apiKey(), 'email' => $email_address], $headers);
    }

    /**
     * Create contact and add to email/contact list.
     *
     * @param string $email_address
     * @param int $list_id
     * @param string $first_name
     * @param string $last_name
     * @param array $custom_fields
     * @param array $headers
     *
     * @return object
     */
    public function createContact($email_address, $list_id, $first_name = '', $last_name = '', $custom_fields = [], $headers = [])
    {
        $data                                         = array();
        $data['email_addresses']                      = array();
        $data['email_addresses'][0]['status']         = 'ACTIVE';
        $data['email_addresses'][0]['confirm_status'] = 'CONFIRMED';
        $data['email_addresses'][0]['opt_in_source'] = 'ACTION_BY_VISITOR';
        $data['email_addresses'][0]['email_address']  = $email_address;
        if ( ! empty($custom_fields) && is_array($custom_fields)) {
            $index = 1;
            foreach ($custom_fields as $value) {
                $data['custom_fields'][] = [
                    'label' => "CustomField$index",
                    'name'  => "custom_field_{$index}",
                    'value' => $value
                ];
                $index++;
            }
        }
        $data['lists']          = array();
        $data['lists'][0]['id'] = $list_id;
        if ( ! empty($first_name)) {
            $data['first_name'] = $first_name;
        }

        if ( ! empty($last_name)) {
            $data['last_name'] = $last_name;
        }

        $headers = array_replace(['Content-Type' => 'application/json'], $headers);

        return $this->apiRequest(sprintf('contacts?action_by=%s&api_key=%s', 'ACTION_BY_VISITOR', $this->apiKey()), 'POST', $data, $headers);
    }

    /**
     * Add email address/contact to an email list/contact list.
     *
     * @param string $email_address
     * @param int $list_id
     * @param string $first_name
     * @param string $last_name
     * @param array $headers
     *
     * @return bool
     * @throws InvalidArgumentException
     * @throws UnexpectedApiResponseException
     */
    public function addContactToList($email_address, $list_id, $first_name = '', $last_name = '', $custom_fields = [], $headers = [])
    {
        // Check if email already exists in Constant Contact.
        $contact = $this->fetchContact($email_address, $headers);

        // Bail if there was a problem.
        if (isset($contact->error_key)) {
            throw new UnexpectedApiResponseException($contact->error_key, $this->httpClient->getResponseHttpCode());
        }

        // If we have a previous contact, only update the list association.
        if ( ! empty($contact->results)) {
            $data = $contact->results[0];

            // Check if they are assigned to lists already.
            if ( ! empty($data->lists)) {
                foreach ($data->lists as $i => $list) {
                    // bail if they are already assigned.
                    if (isset($list->id) && $list_id == $list->id) {
                        return true;
                    }
                }

                // Otherwise, add them to the list.
                $new_list                         = new \stdClass;
                $new_list->id                     = $list_id;
                $new_list->status                 = 'ACTIVE';
                $data->lists[count($data->lists)] = $new_list;
            } else {
                // Add the contact to the list.
                $data->lists      = array();
                $new_list         = new \stdClass;
                $new_list->id     = $list_id;
                $new_list->status = 'ACTIVE';
                $data->lists[0]   = $new_list;
            }

            $contact_id = $contact->results[0]->id;

            $headers = array_replace(['Content-Type' => 'application/json'], $headers);

            $response = $this->apiRequest(sprintf('contacts/%d?api_key=%s&action_by=%s', $contact_id, $this->apiKey(), 'ACTION_BY_VISITOR'), 'PUT', $data, $headers);

            if (is_array($response) && isset($response[0]) && isset($response[0]->error_key)) {
                throw new InvalidArgumentException($response[0]->error_message, $this->httpClient->getResponseHttpCode());
            }

            return true;
        }

        $response = $this->createContact($email_address, $list_id, $first_name, $last_name, $custom_fields, $headers);

        if (is_array($response) && isset($response[0]) && isset($response[0]->error_key)) {
            throw new InvalidArgumentException($response[0]->error_message, $this->httpClient->getResponseHttpCode());
        }

        return true;
    }

    /**
     * Create (draft) email campaign.
     *
     * @param array $payload
     * @param array $headers
     *
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function createEmailCampaign($payload, $headers = [])
    {
        $defaults = ['is_permission_reminder_enabled' => false, 'email_content_format' => 'HTML', 'status' => 'DRAFT'];

        $required_fields = ['name', 'subject', 'from_name', 'from_email', 'reply_to_email', 'email_content', 'text_content', 'email_content_format', 'message_footer'];

        $footer_required_fields = ['organization_name', 'address_line_1', 'city', 'country'];

        $payload = array_replace($defaults, $payload);

        foreach ($required_fields as $required_field) {
            if ( ! in_array($required_field, array_keys($payload))) :
                throw new InvalidArgumentException(sprintf('%s required field is missing', $required_field));
                break;
            endif;
        }

        if (is_array($payload['message_footer'])) {
            foreach ($footer_required_fields as $required_field) {
                if ( ! in_array($required_field, array_keys($payload['message_footer']))) :
                    throw new InvalidArgumentException(sprintf('%s required field is missing', $required_field));
                    break;
                endif;
            }
        }

        if (is_array($payload['message_footer'])) {
            if ( ! array_intersect(['international_state', 'state'], array_keys($payload['message_footer']))) {
                throw new InvalidArgumentException('one of international_state and state field is missing');
            }
        }

        $headers = array_replace(['Content-Type' => 'application/json'], $headers);

        $response = $this->apiRequest(sprintf('emailmarketing/campaigns?api_key=%s', $this->apiKey()), 'POST', $payload, $headers);

        if (is_array($response) && isset($response[0]) && isset($response[0]->error_key)) {
            throw new InvalidArgumentException($response[0]->error_message, $this->httpClient->getResponseHttpCode());
        }

        return $response;
    }


    /**
     * Send email campaign immediately.
     *
     * @param int $campaign_id
     * @param array $headers
     *
     * @return mixed
     * @throws InvalidArgumentException
     *
     */
    public function sendEmailCampaign($campaign_id, $headers = [])
    {
        $payload = ['empty_json'];

        $headers = array_replace(['Content-Type' => 'application/json'], $headers);

        $response = $this->apiRequest(sprintf('emailmarketing/campaigns/%d/schedules?api_key=%s', $campaign_id, $this->apiKey()), 'POST', $payload, $headers);

        if (is_array($response) && isset($response[0]) && isset($response[0]->error_key)) {
            throw new InvalidArgumentException($response[0]->error_message, $this->httpClient->getResponseHttpCode());
        }

        return $response;
    }

    /**
     * Schedule email campaign.
     *
     * @param int $campaign_id
     * @param string $scheduled_time Unix timestamp to schedule the campaign for sending.
     * @param array $headers
     *
     * @return mixed
     * @throws InvalidArgumentException
     *
     */
    public function scheduleEmailCampaign($campaign_id, $scheduled_time, $headers = [])
    {
        // convert timestamp to ISO 8601 date which is only accepted by Ctct API
        $date = date('c', $scheduled_time);

        $payload = ['scheduled_date' => $date];

        $headers = array_replace(['Content-Type' => 'application/json'], $headers);

        $response = $this->apiRequest(sprintf('emailmarketing/campaigns/%d/schedules?api_key=%s', $campaign_id, $this->apiKey()), 'POST', $payload, $headers);

        if (is_array($response) && isset($response[0]) && isset($response[0]->error_key)) {
            throw new InvalidArgumentException($response[0]->error_message, $this->httpClient->getResponseHttpCode());
        }

        return $response;
    }
}

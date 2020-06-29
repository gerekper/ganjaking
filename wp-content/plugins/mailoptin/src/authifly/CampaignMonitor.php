<?php

namespace Authifly\Provider;

use Authifly\Adapter\OAuth2;
use Authifly\Exception\InvalidArgumentException;


class CampaignMonitor extends OAuth2
{
    /**
     * {@inheritdoc}
     */
    protected $apiBaseUrl = 'https://api.createsend.com/api/v3.2/';

    /**
     * {@inheritdoc}
     */
    protected $authorizeUrl = 'https://api.createsend.com/oauth';

    /**
     * {@inheritdoc}
     */
    protected $accessTokenUrl = 'https://api.createsend.com/oauth/token';

    /**
     * {@inheritdoc}
     */
    protected $apiDocumentation = 'https://www.campaignmonitor.com/api/';

    /**
     * Campaign monitor require 'type' query parameter with value 'web_server' to be appended to the authorization URL.
     * @see https://www.campaignmonitor.com/api/getting-started/#authenticating-with-oauth
     *
     * {@inheritdoc}
     */
    protected function initialize()
    {
        $this->AuthorizeUrlParameters = [
            'response_type' => 'code',
            'type'          => 'web_server',
            'client_id'     => $this->clientId,
            'redirect_uri'  => $this->callback,
            'scope'         => $this->scope,
        ];

        $this->tokenExchangeParameters = [
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type'    => 'authorization_code',
            'redirect_uri'  => $this->callback
        ];

        $this->tokenRefreshParameters = [
            'grant_type'    => 'refresh_token',
            'refresh_token' => $this->getStoredData('refresh_token'),
        ];

        /** Campaign monitor explicitly require access token to be set as Bearer.  */

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
     * Retrieve clients.
     *
     * @return array key/index is the clientID and value is the client name.
     */
    public function getClients()
    {
        $result = $this->apiRequest('clients.json', 'GET');

        return array_reduce($result, function ($carry, $item) {
            $carry[$item->ClientID] = $item->Name;

            return $carry;
        });
    }

    /**
     * Get email list belonging to a client.
     *
     * @param string $client_id
     *
     * @return object
     * @throws InvalidArgumentException
     */
    public function getEmailList($client_id)
    {
        if (empty($client_id)) {
            throw new InvalidArgumentException('Client ID is missing');
        }

        return $this->apiRequest("clients/$client_id/lists.json");
    }

    /**
     * Get custom fields of a list.
     *
     * @param string $list_id
     *
     * @return object
     * @throws InvalidArgumentException
     */
    public function getListCustomFields($list_id)
    {
        if (empty($list_id)) {
            throw new InvalidArgumentException('List ID is missing');
        }

        return $this->apiRequest("lists/$list_id/customfields.json");
    }

    /**
     * Add subscriber to an email list.
     *
     * @param string $list_id
     * @param array $payload
     *
     * @return object
     * @throws InvalidArgumentException
     */
    public function addSubscriber($list_id, $payload = [])
    {
        if (empty($list_id)) {
            throw new InvalidArgumentException('List ID is missing');
        }

        if (empty($payload)) {
            throw new InvalidArgumentException('Payload is missing');
        }

        $headers = ['Content-Type' => 'application/json'];

        return $this->apiRequest("subscribers/$list_id.json", 'POST', $payload, $headers);
    }

    /**
     * Add subscriber to an email list taking their email and name.
     *
     * @param string $list_id
     * @param string $email
     * @param string $name
     * @param array $custom_fields
     *
     * @return bool
     * @throws InvalidArgumentException
     */
    public function addSubscriberEmailName($list_id, $email, $name = '', $custom_fields = [], $consent = 'Unchanged')
    {
        if (empty($list_id)) {
            throw new InvalidArgumentException('List ID is missing');
        }

        if (empty($email)) {
            throw new InvalidArgumentException('Email address is missing');
        }

        $custom_fields_payload = [
            [
                'Key'   => 'Note',
                'Value' => 'Via MailOptin',
            ]
        ];

        if ( ! empty($custom_fields) && is_array($custom_fields)) {
            foreach ($custom_fields as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $val) {
                        $custom_fields_payload[] = [
                            'Key'   => $key,
                            'Value' => $val,
                        ];
                    }
                    continue;
                }
                $custom_fields_payload[] = [
                    'Key'   => $key,
                    'Value' => $value,
                ];
            }
        }

        $payload = [
            "EmailAddress"                           => $email,
            "Name"                                   => $name,
            "CustomFields"                           => $custom_fields_payload,
            "Resubscribe"                            => true,
            "RestartSubscriptionBasedAutoresponders" => true,
            "ConsentToTrack"                         => $consent
        ];

        $payload = array_filter($payload, function ($value) {
            return ! empty($value);
        });

        $this->addSubscriber($list_id, $payload);

        return 201 === $this->httpClient->getResponseHttpCode();
    }

    /**
     * Create draft campaign.
     *
     * @param string $client_id
     * @param array $payload
     *
     * @return object
     * @throws InvalidArgumentException
     */
    public function createDraftCampaign($client_id, $payload)
    {
        if (empty($client_id)) {
            throw new InvalidArgumentException('Client ID is missing');
        }

        if (empty($payload)) {
            throw new InvalidArgumentException('Payload is missing');
        }

        if (empty($payload['ListIDs']) && empty($payload['SegmentIDs'])) {
            throw new InvalidArgumentException('List IDs or Segments to send to is missing.');
        }

        $required_fields = ['Name', 'Subject', 'FromName', 'FromEmail', 'ReplyTo', 'HtmlUrl'];

        foreach ($required_fields as $required_field) {
            if ( ! in_array($required_field, array_keys($payload))) :
                throw new InvalidArgumentException(sprintf('%s required field is missing', $required_field));
                break;
            endif;
        }

        $headers = ['Content-Type' => 'application/json'];

        return $this->apiRequest("campaigns/$client_id.json", 'POST', $payload, $headers);
    }

    /**
     * Send draft campaign.
     *
     * @see https://www.campaignmonitor.com/api/campaigns/#sending-draft-campaign
     *
     * @param int $campaign_id
     * @param string $confirmation_email
     * @param string $send_date
     *
     * @return bool
     * @throws InvalidArgumentException
     */
    public function sendDraftCampaign($campaign_id, $confirmation_email, $send_date = 'Immediately')
    {
        if (empty($campaign_id)) {
            throw new InvalidArgumentException('Campaign ID is missing');
        }

        if (empty($confirmation_email)) {
            throw new InvalidArgumentException('Confirmation email address cannot be empty.');
        }

        $headers = ['Content-Type' => 'application/json'];

        $payload = [
            'ConfirmationEmail' => $confirmation_email,
            'SendDate'          => $send_date
        ];

        $this->apiRequest("campaigns/$campaign_id/send.json", 'POST', $payload, $headers);

        return 200 === $this->httpClient->getResponseHttpCode();
    }
}
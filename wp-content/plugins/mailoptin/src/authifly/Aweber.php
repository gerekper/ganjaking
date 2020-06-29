<?php

namespace Authifly\Provider;

use Authifly\Adapter\OAuth1;
use Authifly\Exception\Exception;
use Authifly\Exception\InvalidArgumentException;
use Authifly\Data;

class Aweber extends OAuth1
{
    /**
     * {@inheritdoc}
     */
    protected $apiBaseUrl = 'https://api.aweber.com/1.0/';
    /**
     * {@inheritdoc}
     */
    protected $authorizeUrl = 'https://auth.aweber.com/1.0/oauth/authorize';
    /**
     * {@inheritdoc}
     */
    protected $requestTokenUrl = 'https://auth.aweber.com/1.0/oauth/request_token';
    /**
     * {@inheritdoc}
     */
    protected $accessTokenUrl = 'https://auth.aweber.com/1.0/oauth/access_token';
    /**
     * {@inheritdoc}
     */
    protected $apiDocumentation = 'https://labs.aweber.com/docs/authentication';

    /**
     * Fetch account details
     *
     * @return mixed
     */
    public function fetchAccount()
    {
        /**
         * object(stdClass)[12]
         * public 'total_size' => int 1
         * public 'start' => int 0
         * public 'entries' =>
         * array (size=1)
         * 0 =>
         * object(stdClass)[10]
         * public 'http_etag' => string '"b74b526122119aa35af824f91214febb63000000-ca5feee2b7fbb6febfca8af5541541ea960aaedb"' (length=83)
         * public 'lists_collection_link' => string 'https://api.aweber.com/1.0/accounts/1158000/lists' (length=49)
         * public 'self_link' => string 'https://api.aweber.com/1.0/accounts/1158000' (length=43)
         * public 'resource_type_link' => string 'https://api.aweber.com/1.0/#account' (length=35)
         * public 'id' => int 1158000
         * public 'integrations_collection_link' => string 'https://api.aweber.com/1.0/accounts/1158000/integrations' (length=56)
         * public 'resource_type_link' => string 'https://api.aweber.com/1.0/#accounts' (length=36)
         */
        $response = $this->apiRequest('accounts');

        $data    = new Data\Collection($response);
        $entries = $data->filter('entries')->toArray();

        return $entries[0];
    }

    /**
     * Get account ID
     *
     * @return int
     */
    public function fetchAccountId()
    {
        return $this->fetchAccount()->id;
    }

    /**
     * Fetch email/subscriber list.
     *
     * @param int $account_id
     *
     * @throws InvalidArgumentException
     *
     * @return array
     */
    public function fetchEmailList($account_id)
    {
        if (empty($account_id)) {
            throw new InvalidArgumentException('Account ID is missing');
        }

        $response = $this->apiRequest("accounts/$account_id/lists");

        $data = new Data\Collection($response);

        return $data->filter('entries')->toArray();
    }

    /**
     * Fetch email/subscriber list custom fields.
     *
     * @param int $account_id
     *
     * @throws InvalidArgumentException
     *
     * @return array
     */
    public function getListCustomFields($account_id, $list_id)
    {
        if (empty($account_id)) {
            throw new InvalidArgumentException('Account ID is missing');
        }

        if (empty($list_id)) {
            throw new InvalidArgumentException('List ID is missing');
        }

        $response = $this->apiRequest("accounts/$account_id/lists/$list_id/custom_fields");

        $data = new Data\Collection($response);

        return $data->filter('entries')->toArray();
    }

    /**
     * Fetch email/subscriber list.
     *
     * @param int $account_id
     *
     * @throws InvalidArgumentException
     *
     * @return array
     */
    public function fetchEmailListNameAndId($account_id)
    {
        /**
         * array (size=2)
         * 0 =>
         * array (size=2)
         * 0 => int 4687900
         * 1 => string 'Blog subscribers' (length=16)
         * 1 =>
         * array (size=2)
         * 0 => int 4688698
         * 1 => string 'Software buyers' (length=15)
         */
        if (empty($account_id)) {
            throw new InvalidArgumentException('Account ID is missing');
        }

        $response = $this->fetchEmailList($account_id);

        return array_reduce($response, function ($carry, $item) {
            $carry[] = [$item->id, $item->name];

            return $carry;
        });
    }

    /**
     * Add subscriber to email/subscriber list.
     *
     * @param string|int $account_id
     * @param string|int $list_id
     * @param mixed $payload
     *
     * @throws InvalidArgumentException
     *
     * @return array
     */
    public function addSubscriber($account_id, $list_id, $payload = [])
    {
        if (empty($account_id) || empty($list_id) || empty($payload)) {
            throw new InvalidArgumentException('Account ID or list ID or payload is missing');
        }

        $parameters = array_merge(['ws.op' => 'create'], $payload);

        return $this->apiRequest("accounts/$account_id/lists/$list_id/subscribers", 'POST', $parameters);

    }

    /**
     * Add subscriber supplying just email and name(optional)
     *
     * @param int $account_id
     * @param int $list_id
     * @param string $email
     * @param string $name
     *
     * @return bool
     *
     * @throws InvalidArgumentException
     */
    public function addSubscriberEmailAndName($account_id, $list_id, $email, $name = '')
    {
        if (empty($account_id) || empty($list_id) || empty($email)) {
            throw new InvalidArgumentException('Account ID or list ID or email address is missing');
        }

        try {
            $payload = ['email' => $email, 'name' => $name, 'ip_address' => $_SERVER['REMOTE_ADDR']];

            $this->addSubscriber($account_id, $list_id, $payload);

            return 201 === $this->httpClient->getResponseHttpCode();

        } catch (Exception $e) {

            $httpStatusCode   = $this->httpClient->getResponseHttpCode();
            $httpResponseBody = $this->httpClient->getResponseBody();

            if (400 === $httpStatusCode && strpos($httpResponseBody, 'already subscribed')) {
                return true;
            }

            throw new $e;
        }
    }

    /**
     * Create broadcast.
     *
     * @param int $account_id
     * @param int $list_id
     * @param array $payload see https://labs.aweber.com/docs/reference/1.0#broadcast_collection
     *
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    public function createBroadCast($account_id, $list_id, $payload)
    {
        if (empty($account_id) || empty($list_id) || empty($payload)) {
            throw new InvalidArgumentException('One of either Account ID, list ID or payload is missing');
        }

        $required_fields = ['body_html', 'body_text', 'subject'];

        foreach ($required_fields as $required_field) {
            if ( ! in_array($required_field, array_keys($payload))) :
                throw new InvalidArgumentException(sprintf('%s required field is missing', $required_field));
                break;
            endif;
        }

        return $this->apiRequest("accounts/$account_id/lists/$list_id/broadcasts", 'POST', $payload);
    }

    /**
     * Schedule broadcast.
     *
     * @param int $account_id
     * @param int $list_id
     * @param int $broadcast_id
     * @param string $scheduled_time Unix timestamp when the broadcast will be scheduled for sending.
     *
     * @return bool
     *
     * @throws InvalidArgumentException
     */
    public function scheduleBroadCast($account_id, $list_id, $broadcast_id, $scheduled_time)
    {
        if (empty($account_id) || empty($list_id) || empty($broadcast_id) || empty($scheduled_time)) {
            throw new InvalidArgumentException('One of either Account ID, list ID, broadcast ID or scheduled_time is missing');
        }

        // convert timestamp to ISO 8601 date which is only accepted by Ctct API
        $scheduled_for = date('c', $scheduled_time);

        $this->apiRequest("accounts/$account_id/lists/$list_id/broadcasts/$broadcast_id/schedule", 'POST', ['scheduled_for' => $scheduled_for]);

        return 200 === $this->httpClient->getResponseHttpCode();
    }

    /**
     * Create and send broadcast.
     *
     * @param int $account_id
     * @param int $list_id
     * @param string $subject
     * @param string $body_html
     * @param string $body_text
     * @param null|string $schedule if this isn't specified, broadcast will be sent immediately.
     *
     * @return array|bool
     * @throws InvalidArgumentException
     */
    public function createSendBroadCast($account_id, $list_id, $subject, $body_html, $body_text, $schedule = null)
    {
        if (empty($account_id) || empty($list_id) || empty($subject) || empty($body_html) || empty($body_text)) {
            throw new InvalidArgumentException('One of either Account ID, list ID, subject, body_html or body_text is missing');
        }

        // create the broadcast
        $response = $this->createBroadCast($account_id, $list_id, [
            'subject'   => $subject,
            'body_html' => $body_html,
            'body_text' => $body_text
        ]);

        $broadcast_id = $response->broadcast_id;

        if (is_null($schedule)) {
            $schedule = strtotime('+30 seconds');
        }

        $send = $this->scheduleBroadCast($account_id, $list_id, $broadcast_id, $schedule);

        if ($send) {
            return ['success' => true, 'broadcast_id' => $broadcast_id];
        }

        return $send;
    }
}
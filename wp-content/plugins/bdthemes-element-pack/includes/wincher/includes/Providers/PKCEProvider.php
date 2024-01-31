<?php

namespace ElementPack\Wincher\Providers;

use GuzzleHttp\Exception\BadResponseException;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\GenericProvider;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Token\AccessTokenInterface;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\RequestInterface;

/**
 * PKCE Provider class.
 */
class PKCEProvider extends GenericProvider
{
    use BearerAuthorizationTrait;

    /**
     * The method to use.
     *
     * @var string
     */
    protected $pkceMethod;

    /**
     * The PKCE code.
     *
     * @var string
     */
    protected $pkceCode;

    /**
     * Set the value of the pkceCode parameter.
     *
     * When using PKCE this should be set before requesting an access token.
     *
     * @param string $pkceCode
     *
     * @return self
     */
    public function setPkceCode($pkceCode)
    {
        $this->pkceCode = $pkceCode;

        return $this;
    }

    /**
     * Returns the current value of the pkceCode parameter.
     *
     * This can be accessed by the redirect handler during authorization.
     *
     * @return string
     */
    public function getPkceCode()
    {
        return $this->pkceCode;
    }

    /**
     * Returns a new random string to use as PKCE code_verifier and
     * hashed as code_challenge parameters in an authorization flow.
     * Must be between 43 and 128 characters long.
     *
     * @param int $length length of the random string to be generated
     *
     * @return string
     *
     * @throws Exception throws exception if an invalid value is passed to random_bytes
     */
    protected function getRandomPkceCode($length = 64)
    {
        return substr(
            strtr(
                base64_encode(random_bytes($length)),
                '+/',
                '-_'
            ),
            0,
            $length
        );
    }

    /**
     * Returns the current value of the pkceMethod parameter.
     *
     * @return string|null
     */
    protected function getPkceMethod()
    {
        return $this->pkceMethod;
    }

    /**
     * Returns authorization parameters based on provided options.
     *
     * @param array $options the options to use in the authorization parameters
     *
     * @return array The authorization parameters
     *
     * @throws InvalidArgumentException|Exception throws exception if an invalid PCKE method is passed in the options
     */
    protected function getAuthorizationParameters(array $options)
    {
        if (empty($options['state'])) {
            $options['state'] = $this->getRandomState();
        }

        if (empty($options['scope'])) {
            $options['scope'] = $this->getDefaultScopes();
        }

        $options += [
            'response_type' => 'code',
        ];

        if (is_array($options['scope'])) {
            $separator = $this->getScopeSeparator();
            $options['scope'] = implode($separator, $options['scope']);
        }

        // Store the state as it may need to be accessed later on.
        $this->state = $options['state'];

        $pkce_method = $this->getPkceMethod();
        if (!empty($pkce_method)) {
            $this->pkceCode = $this->getRandomPkceCode();
            if ('S256' === $pkce_method) {
                $options['code_challenge'] = trim(
                    strtr(
                        base64_encode(hash('sha256', $this->pkceCode, true)),
                        '+/',
                        '-_'
                    ),
                    '='
                );
            } elseif ('plain' === $pkce_method) {
                $options['code_challenge'] = $this->pkceCode;
            } else {
                throw new InvalidArgumentException('Unknown PKCE method "' . $pkce_method . '".');
            }
            $options['code_challenge_method'] = $pkce_method;
        }

        // Business code layer might set a different redirect_uri parameter.
        // Depending on the context, leave it as-is.
        if (!isset($options['redirect_uri'])) {
            $options['redirect_uri'] = $this->redirectUri;
        }

        $options['client_id'] = $this->clientId;

        return $options;
    }

    /**
     * Requests an access token using a specified grant and option set.
     *
     * @param mixed $grant   the grant to request access for
     * @param array $options the options to use with the current request
     *
     * @return AccessToken|AccessTokenInterface the access token
     *
     * @throws IdentityProviderException exception thrown if the provider response contains errors
     */
    public function getAccessToken($grant, array $options = [])
    {
        $grant = $this->verifyGrant($grant);

        $params = [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri' => $this->redirectUri,
        ];

        if (!empty($this->pkceCode)) {
            $params['code_verifier'] = $this->pkceCode;
        }

        $params = $grant->prepareRequestParameters($params, $options);
        $request = $this->getAccessTokenRequest($params);
        $response = $this->getParsedResponse($request);

        if (false === is_array($response)) {
            throw new UnexpectedValueException('Invalid response received from Authorization Server. Expected JSON.');
        }

        $prepared = $this->prepareAccessTokenResponse($response);

        return $this->createAccessToken($prepared, $grant);
    }

    /**
     * Returns all options that can be configured.
     *
     * @return array the configurable options
     */
    protected function getConfigurableOptions()
    {
        return array_merge(
            $this->getRequiredOptions(),
            [
                'accessTokenMethod',
                'accessTokenResourceOwnerId',
                'scopeSeparator',
                'responseError',
                'responseCode',
                'responseResourceOwnerId',
                'scopes',
                'pkceMethod',
            ]
        );
    }

    /**
     * Parses the request response.
     *
     * @param RequestInterface $request the request interface
     *
     * @return array the parsed response
     *
     * @throws IdentityProviderException exception thrown if there is no proper identity provider
     */
    public function getParsedResponse(RequestInterface $request)
    {
        try {
            $response = $this->getResponse($request);
        } catch (BadResponseException $e) {
            $response = $e->getResponse();
        }

        $parsed = $this->parseResponse($response);

        $this->checkResponse($response, $parsed);

        if (!is_array($parsed)) {
            $parsed = ['data' => []];
        }

        // Add the response code as this is omitted from Winchers API.
        if (!array_key_exists('status', $parsed)) {
            $parsed['status'] = $response->getStatusCode();
        }

        return $parsed;
    }
}

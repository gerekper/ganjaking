<?php

namespace WPMailSMTP\Vendor\Aws\Handler\GuzzleV6;

use Exception;
use WPMailSMTP\Vendor\GuzzleHttp\Exception\ConnectException;
use WPMailSMTP\Vendor\GuzzleHttp\Exception\RequestException;
use WPMailSMTP\Vendor\GuzzleHttp\Promise;
use WPMailSMTP\Vendor\GuzzleHttp\Client;
use WPMailSMTP\Vendor\GuzzleHttp\ClientInterface;
use WPMailSMTP\Vendor\GuzzleHttp\TransferStats;
use WPMailSMTP\Vendor\Psr\Http\Message\RequestInterface as Psr7Request;
/**
 * A request handler that sends PSR-7-compatible requests with Guzzle 6.
 */
class GuzzleHandler
{
    /** @var ClientInterface */
    private $client;
    /**
     * @param ClientInterface $client
     */
    public function __construct(\WPMailSMTP\Vendor\GuzzleHttp\ClientInterface $client = null)
    {
        $this->client = $client ?: new \WPMailSMTP\Vendor\GuzzleHttp\Client();
    }
    /**
     * @param Psr7Request $request
     * @param array       $options
     *
     * @return Promise\Promise
     */
    public function __invoke(\WPMailSMTP\Vendor\Psr\Http\Message\RequestInterface $request, array $options = [])
    {
        $request = $request->withHeader('User-Agent', $request->getHeaderLine('User-Agent') . ' ' . \WPMailSMTP\Vendor\GuzzleHttp\default_user_agent());
        return $this->client->sendAsync($request, $this->parseOptions($options))->otherwise(static function ($e) {
            $error = ['exception' => $e, 'connection_error' => $e instanceof \WPMailSMTP\Vendor\GuzzleHttp\Exception\ConnectException, 'response' => null];
            if ($e instanceof \WPMailSMTP\Vendor\GuzzleHttp\Exception\RequestException && $e->getResponse()) {
                $error['response'] = $e->getResponse();
            }
            return new \WPMailSMTP\Vendor\GuzzleHttp\Promise\RejectedPromise($error);
        });
    }
    private function parseOptions(array $options)
    {
        if (isset($options['http_stats_receiver'])) {
            $fn = $options['http_stats_receiver'];
            unset($options['http_stats_receiver']);
            $prev = isset($options['on_stats']) ? $options['on_stats'] : null;
            $options['on_stats'] = static function (\WPMailSMTP\Vendor\GuzzleHttp\TransferStats $stats) use($fn, $prev) {
                if (\is_callable($prev)) {
                    $prev($stats);
                }
                $transferStats = ['total_time' => $stats->getTransferTime()];
                $transferStats += $stats->getHandlerStats();
                $fn($transferStats);
            };
        }
        return $options;
    }
}

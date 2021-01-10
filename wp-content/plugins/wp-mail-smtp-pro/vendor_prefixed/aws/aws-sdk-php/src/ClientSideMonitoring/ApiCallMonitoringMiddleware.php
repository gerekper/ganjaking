<?php

namespace WPMailSMTP\Vendor\Aws\ClientSideMonitoring;

use WPMailSMTP\Vendor\Aws\CommandInterface;
use WPMailSMTP\Vendor\Aws\Exception\AwsException;
use WPMailSMTP\Vendor\Aws\MonitoringEventsInterface;
use WPMailSMTP\Vendor\Aws\ResultInterface;
use WPMailSMTP\Vendor\Psr\Http\Message\RequestInterface;
/**
 * @internal
 */
class ApiCallMonitoringMiddleware extends \WPMailSMTP\Vendor\Aws\ClientSideMonitoring\AbstractMonitoringMiddleware
{
    /**
     * Api Call Attempt event keys for each Api Call event key
     *
     * @var array
     */
    private static $eventKeys = ['FinalAwsException' => 'AwsException', 'FinalAwsExceptionMessage' => 'AwsExceptionMessage', 'FinalSdkException' => 'SdkException', 'FinalSdkExceptionMessage' => 'SdkExceptionMessage', 'FinalHttpStatusCode' => 'HttpStatusCode'];
    /**
     * Standard middleware wrapper function with CSM options passed in.
     *
     * @param callable $credentialProvider
     * @param mixed  $options
     * @param string $region
     * @param string $service
     * @return callable
     */
    public static function wrap(callable $credentialProvider, $options, $region, $service)
    {
        return function (callable $handler) use($credentialProvider, $options, $region, $service) {
            return new static($handler, $credentialProvider, $options, $region, $service);
        };
    }
    /**
     * {@inheritdoc}
     */
    public static function getRequestData(\WPMailSMTP\Vendor\Psr\Http\Message\RequestInterface $request)
    {
        return [];
    }
    /**
     * {@inheritdoc}
     */
    public static function getResponseData($klass)
    {
        if ($klass instanceof \WPMailSMTP\Vendor\Aws\ResultInterface) {
            $data = ['AttemptCount' => self::getResultAttemptCount($klass), 'MaxRetriesExceeded' => 0];
        } elseif ($klass instanceof \Exception) {
            $data = ['AttemptCount' => self::getExceptionAttemptCount($klass), 'MaxRetriesExceeded' => self::getMaxRetriesExceeded($klass)];
        } else {
            throw new \InvalidArgumentException('Parameter must be an instance of ResultInterface or Exception.');
        }
        return $data + self::getFinalAttemptData($klass);
    }
    private static function getResultAttemptCount(\WPMailSMTP\Vendor\Aws\ResultInterface $result)
    {
        if (isset($result['@metadata']['transferStats']['http'])) {
            return \count($result['@metadata']['transferStats']['http']);
        }
        return 1;
    }
    private static function getExceptionAttemptCount(\Exception $e)
    {
        $attemptCount = 0;
        if ($e instanceof \WPMailSMTP\Vendor\Aws\MonitoringEventsInterface) {
            foreach ($e->getMonitoringEvents() as $event) {
                if (isset($event['Type']) && $event['Type'] === 'ApiCallAttempt') {
                    $attemptCount++;
                }
            }
        }
        return $attemptCount;
    }
    private static function getFinalAttemptData($klass)
    {
        $data = [];
        if ($klass instanceof \WPMailSMTP\Vendor\Aws\MonitoringEventsInterface) {
            $finalAttempt = self::getFinalAttempt($klass->getMonitoringEvents());
            if (!empty($finalAttempt)) {
                foreach (self::$eventKeys as $callKey => $attemptKey) {
                    if (isset($finalAttempt[$attemptKey])) {
                        $data[$callKey] = $finalAttempt[$attemptKey];
                    }
                }
            }
        }
        return $data;
    }
    private static function getFinalAttempt(array $events)
    {
        for (\end($events); \key($events) !== null; \prev($events)) {
            $current = \current($events);
            if (isset($current['Type']) && $current['Type'] === 'ApiCallAttempt') {
                return $current;
            }
        }
        return null;
    }
    private static function getMaxRetriesExceeded($klass)
    {
        if ($klass instanceof \WPMailSMTP\Vendor\Aws\Exception\AwsException && $klass->isMaxRetriesExceeded()) {
            return 1;
        }
        return 0;
    }
    /**
     * {@inheritdoc}
     */
    protected function populateRequestEventData(\WPMailSMTP\Vendor\Aws\CommandInterface $cmd, \WPMailSMTP\Vendor\Psr\Http\Message\RequestInterface $request, array $event)
    {
        $event = parent::populateRequestEventData($cmd, $request, $event);
        $event['Type'] = 'ApiCall';
        return $event;
    }
    /**
     * {@inheritdoc}
     */
    protected function populateResultEventData($result, array $event)
    {
        $event = parent::populateResultEventData($result, $event);
        $event['Latency'] = (int) (\floor(\microtime(\true) * 1000) - $event['Timestamp']);
        return $event;
    }
}

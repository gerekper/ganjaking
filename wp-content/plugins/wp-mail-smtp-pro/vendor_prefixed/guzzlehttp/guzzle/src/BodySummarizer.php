<?php

namespace WPMailSMTP\Vendor\GuzzleHttp;

use WPMailSMTP\Vendor\Psr\Http\Message\MessageInterface;
final class BodySummarizer implements \WPMailSMTP\Vendor\GuzzleHttp\BodySummarizerInterface
{
    /**
     * @var int|null
     */
    private $truncateAt;
    public function __construct(int $truncateAt = null)
    {
        $this->truncateAt = $truncateAt;
    }
    /**
     * Returns a summarized message body.
     */
    public function summarize(\WPMailSMTP\Vendor\Psr\Http\Message\MessageInterface $message) : ?string
    {
        return $this->truncateAt === null ? \WPMailSMTP\Vendor\GuzzleHttp\Psr7\Message::bodySummary($message) : \WPMailSMTP\Vendor\GuzzleHttp\Psr7\Message::bodySummary($message, $this->truncateAt);
    }
}
